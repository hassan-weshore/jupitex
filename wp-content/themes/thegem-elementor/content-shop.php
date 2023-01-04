<?php
/**
 * The template used for displaying page content in blog
 */

	$thegem_item_data = array(
		'sidebar_position' => '',
		'sidebar_sticky' => '',
		'blog_style' => 'default',
		'blog_post_per_page' => '',
		'blog_categories' => '',
		'blog_post_types' => '',
		'blog_pagination' => ''
	);
	$thegem_item_data = thegem_get_post_data($thegem_item_data, 'page', get_the_ID());
	$thegem_sidebar_position = thegem_check_array_value(array('', 'left', 'right'), $thegem_item_data['sidebar_position'], '');
	$thegem_sidebar_stiky = $thegem_item_data['sidebar_sticky'] ? 1 : 0;
	$thegem_panel_classes = array('panel', 'row');
	$thegem_center_classes = 'panel-center';
	$thegem_sidebar_classes = '';
	if(is_active_sidebar('shop-sidebar') && $thegem_sidebar_position) {
		$thegem_panel_classes[] = 'panel-sidebar-position-'.$thegem_sidebar_position;
		$thegem_panel_classes[] = 'with-sidebar';
		$thegem_center_classes.= ' col-lg-9 col-md-9 col-sm-12';
		if($thegem_sidebar_position == 'left') {
			$thegem_center_classes .= ' col-md-push-3 col-sm-push-0';
			$thegem_sidebar_classes .= ' col-md-pull-9 col-sm-pull-0';
		}
	} else {
		$thegem_center_classes .= ' col-xs-12';
	}
	if($thegem_sidebar_stiky) {
		$thegem_panel_classes[] = 'panel-sidebar-sticky';
		wp_enqueue_script('thegem-sticky');
	}
?>

<div class="block-content">
	<div class="container">
		<div class="<?php echo esc_attr(implode(' ', $thegem_panel_classes)); ?>">
			<div class="<?php echo esc_attr($thegem_center_classes); ?>">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="entry-content">
						<?php
							the_content();
							wp_link_pages( array(
								'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'thegem' ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
							) );

						?>
					</div><!-- .entry-content -->

				</article><!-- #post-## -->

			</div>

			<?php
				if(is_active_sidebar('shop-sidebar') && $thegem_sidebar_position) {
					echo '<div class="sidebar col-lg-3 col-md-3 col-sm-12'.esc_attr($thegem_sidebar_classes).'" role="complementary">';
					get_sidebar('shop');
					echo '</div><!-- .sidebar -->';
				}
			?>
		</div>
		<div class="fullwidth-block products-page-separator"><div class="gem-divider gem-divider-style-6"></div></div>
		<?php get_sidebar('shop-bottom'); ?>
	</div>
</div>
