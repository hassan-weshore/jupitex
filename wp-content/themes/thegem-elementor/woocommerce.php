<?php
/**
 * Template Name: Woocommerce
 * The Woocommerce template file
 * @package TheGem
 */

	if (isset($_GET['thegem_products_ajax'])) {
		remove_all_actions('woocommerce_before_shop_loop');
		remove_all_actions('woocommerce_after_shop_loop');
		remove_all_actions('woocommerce_archive_description');

		echo '<div data-paged="' . get_query_var( 'paged' ) . '">';
		woocommerce_content();
		echo '</div>';
		exit;
	}

	$thegem_grid_type = thegem_get_option('product_archive_type');

	$thegem_item_data = array(
		'sidebar_position' => '',
		'sidebar_sticky' => '',
		'effects_no_bottom_margin' => 0,
		'effects_no_top_margin' => 0,
		'slideshow_type' => '',
		'slideshow_slideshow' => '',
		'slideshow_layerslider' => '',
		'slideshow_revslider' => '',
	);
	$thegem_page_id = wc_get_page_id('shop');

	if(is_product()) {
		$thegem_page_id = get_the_ID();

		$GLOBALS['thegem_product_data'] = thegem_get_output_product_page_data($thegem_page_id);
		$thegem_product_data = $GLOBALS['thegem_product_data'];
	}

	$thegem_item_data = thegem_get_output_page_settings($thegem_page_id);
	if (!is_singular( 'product' ) && $thegem_grid_type == 'grid') {
		$thegem_item_data = thegem_get_output_page_settings($thegem_page_id, array(), 'product_category');
	}
	if(is_tax()) {
		if ($thegem_grid_type !== 'grid') {
			$thegem_item_data = thegem_get_output_page_settings(0, array(), 'product_category');
		}
		$thegem_term_id = get_queried_object()->term_id;
		if(get_term_meta($thegem_term_id , 'thegem_taxonomy_custom_page_options', true)) {
			$thegem_item_data = thegem_get_output_page_settings($thegem_term_id, array(), 'term');
		}
	}

	$thegem_sidebar_stiky = $thegem_item_data['sidebar_sticky'] ? 1 : 0;
	$thegem_sidebar_position = thegem_check_array_value(array('', 'left', 'right'), $thegem_item_data['sidebar_position'], '');
	$thegem_panel_classes = array('panel', 'row');
	$thegem_center_classes = 'panel-center';
	$thegem_sidebar_classes = '';

	if(is_active_sidebar('shop-sidebar') && $thegem_item_data['sidebar_show'] && $thegem_sidebar_position && (is_singular( 'product' ) || $thegem_grid_type != 'grid')) {
		$thegem_panel_classes[] = 'panel-sidebar-position-'.$thegem_sidebar_position;
		$thegem_panel_classes[] = 'with-sidebar';
		$thegem_center_classes .= ' col-lg-9 col-md-9 col-sm-12';
		if($thegem_sidebar_position == 'left') {
			$thegem_center_classes .= ' col-md-push-3 col-sm-push-0';
			$thegem_sidebar_classes .= ' col-md-pull-9 col-sm-pull-0';
		}
	} else {
		$thegem_center_classes .= ' col-xs-12';
		if ($thegem_item_data['sidebar_show']) {
			$thegem_center_classes .= ' panel-sidebar-position-'.$thegem_sidebar_position;
		}
	}

	get_header();

	if ($thegem_grid_type == 'grid') {
		$thegem_sidebar_classes .= ' portfolio-filters-list style-sidebar'; ?>
		<script>
			(function ($) {
				$(document).ready(function () {
					$('.portfolio-filters-list .widget_layered_nav, .portfolio-filters-list .widget_product_categories').find('.count').each(function () {
						$(this).html($(this).html().replace('(', '').replace(')', '')).css('opacity', 1);
					});
				});
			})(jQuery);
		</script>
		<?php
	}

	if($thegem_sidebar_stiky) {
		$thegem_panel_classes[] = 'panel-sidebar-sticky';
		wp_enqueue_script('thegem-sticky');
	}

?>

<div id="main-content" class="main-content">

<?php
	if($thegem_item_data['title_show'] && $thegem_item_data['title_style'] == 3 && $thegem_item_data['slideshow_type'] && !is_search()) {
		thegem_slideshow_block(array('slideshow_type' => $thegem_item_data['slideshow_type'], 'slideshow' => $thegem_item_data['slideshow_slideshow'], 'lslider' => $thegem_item_data['slideshow_layerslider'], 'slider' => $thegem_item_data['slideshow_revslider']));
	}
?>

	<?= thegem_page_title() ?>

	<div class="block-content">
		<div class="<?=is_product() && $thegem_product_data['product_page_layout'] != 'legacy' && $thegem_product_data['product_page_layout_fullwidth'] ? 'container-fullwidth' : 'container'?>">
            <?php if (!is_post_type_archive('product') && $thegem_item_data['page_layout_breadcrumbs']) : $bottomSpacing = $thegem_item_data['page_layout_breadcrumbs_bottom_spacing'];?>
                <div class="page-breadcrumbs page-breadcrumbs--<?=$thegem_item_data['page_layout_breadcrumbs_alignment']?>" <?php if ($bottomSpacing) : ?>style="margin-bottom: <?=esc_attr($bottomSpacing).'px'?>"<?php endif; ?>>
	                <?= gem_breadcrumbs(true) ?>
                </div>
            <?php endif; ?>

			<div class="<?php echo esc_attr(implode(' ', $thegem_panel_classes)); ?>">
				<div class="<?php echo esc_attr($thegem_center_classes); ?>">
					<?php
					if (!is_singular( 'product' ) && $thegem_grid_type == 'grid') {
						thegem_woocommerce_grid_content(is_active_sidebar('shop-sidebar') && $thegem_item_data['sidebar_show'] && $thegem_sidebar_position);
					} else {
						woocommerce_content();
					} ?>
				</div>

				<?php
					if(is_active_sidebar('shop-sidebar') && $thegem_item_data['sidebar_show'] && $thegem_sidebar_position && (is_singular( 'product' ) || $thegem_grid_type != 'grid')) {
						echo '<div class="sidebar col-lg-3 col-md-3 col-sm-12'.esc_attr($thegem_sidebar_classes).' '.esc_attr($thegem_sidebar_position).'" role="complementary">';
						get_sidebar('shop');
						echo '</div><!-- .sidebar -->';
					}
				?>
			</div>

			<?php if(is_product()) {
				do_action( 'woocommerce_after_single_product_summary' );
				do_action( 'woocommerce_after_single_product' );
			} ?>
		</div>
	</div>
	<?php get_sidebar('shop-bottom'); ?>
</div><!-- #main-content -->

<?php
get_footer();
