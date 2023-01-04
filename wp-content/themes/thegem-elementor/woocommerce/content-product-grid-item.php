<?php
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
add_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
add_action('woocommerce_after_shop_loop_item', 'thegem_woocommerce_after_shop_loop_item_link', 15);
add_action('woocommerce_after_shop_loop_item', 'thegem_woocommerce_after_shop_loop_item_wishlist', 20);
global $post, $product, $woocommerce_loop;

if (!isset($product_grid_item_size)) {
	$params = $GLOBALS['thegem_grid_params'];
}

if (!isset($product_grid_item_size)) {
	$thegem_product_featured_data = thegem_get_sanitize_product_featured_data(get_the_ID());

	if (empty($params['ignore_highlights']) && !empty($thegem_product_featured_data['highlight'])) {
		$thegem_highlight_type = $thegem_product_featured_data['highlight_type'];
	} else {
		$thegem_highlight_type = 'disabled';
	}

	$terms = get_the_terms($post->ID, 'product_cat');
	$slugs = array();
	foreach ($terms as $term) {
		$slugs[] = $term->slug;
	}
} else {
	$slugs = array();
	$thegem_highlight_type = 'disabled';
}

$thegem_classes = array('portfolio-item', 'inline-column', 'product');
$thegem_classes = array_merge($thegem_classes, $slugs);

$thegem_image_classes = array('image');
$thegem_caption_classes = array('caption');

if ($params['style'] != 'metro') {
	if ($params['layout'] == '1x') {
		$thegem_classes = array_merge($thegem_classes, array('col-xs-12'));
		$thegem_image_classes = array_merge($thegem_image_classes, array('col-sm-5', 'col-xs-12'));
		$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-sm-7', 'col-xs-12'));
	}

	if ($params['layout'] == '2x') {
		if ($thegem_highlight_type != 'disabled' && empty($params['is_slider']) && $thegem_highlight_type != 'vertical')
			$thegem_classes = array_merge($thegem_classes, array('col-xs-12'));
		else
			$thegem_classes = array_merge($thegem_classes, array('col-sm-6', 'col-xs-12'));
	}

	if ($params['layout'] == '3x') {
		if ($thegem_highlight_type != 'disabled' && empty($params['is_slider']) && $thegem_highlight_type != 'vertical')
			$thegem_classes = array_merge($thegem_classes, array('col-md-8', 'col-xs-8'));
		else
			$thegem_classes = array_merge($thegem_classes, array('col-md-4', 'col-xs-4'));
	}

	if ($params['layout'] == '4x') {
		if ($thegem_highlight_type != 'disabled' && empty($params['is_slider']) && $thegem_highlight_type != 'vertical')
			$thegem_classes = array_merge($thegem_classes, array('col-md-6', 'col-sm-8', 'col-xs-8'));
		else
			$thegem_classes = array_merge($thegem_classes, array('col-md-3', 'col-sm-4', 'col-xs-4'));
	}
}

if ($thegem_highlight_type != 'disabled' && empty($params['is_slider'])) {
	$thegem_classes[] = 'double-item';
}

if ($thegem_highlight_type != 'disabled' && empty($params['is_slider'])) {
	$thegem_classes[] = 'double-item-' . $thegem_highlight_type;
}

$thegem_size = 'thegem-portfolio-justified';
$thegem_sizes = thegem_image_sizes();
if ($params['layout'] != '1x') {
	if ($params['style'] == 'masonry') {
		$thegem_size = 'thegem-portfolio-masonry';
		if ($thegem_highlight_type != 'disabled' && empty($params['is_slider'])) {
			$thegem_size = 'thegem-portfolio-masonry-double';
		}
	} elseif ($params['style'] == 'metro') {
		$thegem_size = 'thegem-portfolio-metro';
	} else {
		if ($thegem_highlight_type != 'disabled' && empty($params['is_slider'])) {
			$thegem_size = 'thegem-portfolio-double-' . str_replace('%', '',$params['layout']);

			if ( ($params['display_titles'] == 'hover' || $params['hover'] == 'gradient' || $params['hover'] == 'circular') && isset($thegem_sizes[$thegem_size.'-hover'])) {
				$thegem_size .= '-hover';
			}

			if(isset($thegem_sizes[$thegem_size.'-gap-'.$params['gaps_size']])) {
				$thegem_size .= '-gap-'.$params['gaps_size'];
			}

			if ($params['layout'] == '100%' && $params['display_titles'] == 'page') {
				$thegem_size .= '-page';
			}

		}
	}
	if ($thegem_highlight_type != 'disabled' && $params['style'] != 'metro' && empty($params['is_slider']) && $thegem_highlight_type != 'squared') {
		$thegem_size .= '-' . $thegem_highlight_type;
	}
} else {
	$thegem_size = 'thegem-portfolio-1x';
}

$thegem_classes[] = 'item-animations-not-inited';

$thegem_size = apply_filters('portfolio_size_filter', $thegem_size);

$thegem_sources = array();

if ($params['style'] == 'metro') {
	$thegem_sources = array(
		array('media' => '(min-width: 550px) and (max-width: 1100px)', 'srcset' => array('1x' => 'thegem-portfolio-metro-medium', '2x' => 'thegem-portfolio-metro-retina'))
	);
}

if ($thegem_highlight_type == 'disabled' || !empty($params['is_slider']) ||
		($params['style'] == 'masonry' && $thegem_highlight_type != 'disabled') && $thegem_highlight_type == 'vertical') {

	$retina_size = $params['style'] == 'justified' ? $thegem_size : 'thegem-portfolio-masonry-double';

	if ($params['layout'] == '100%') {
		if ($params['style'] == 'justified' || $params['style'] == 'masonry') {
			switch ($params['fullwidth_columns']) {
				case '4':
					$thegem_sources = array(
						array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-2x-500', '2x' => $retina_size)),
						array('media' => '(min-width: 1280px) and (max-width: 1495px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-fullwidth-5x', '2x' => $retina_size)),
						array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-fullwidth-4x', '2x' => $retina_size))
					);
					break;

				case '5':
					$thegem_sources = array(
						array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-2x-500', '2x' => $retina_size)),
						array('media' => '(min-width: 1495px) and (max-width: 1680px), (min-width: 550px) and (max-width: 1280px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-fullwidth-4x', '2x' => $retina_size)),
						array('media' => '(min-width: 1680px) and (max-width: 1920px), (min-width: 1280px) and (max-width: 1495px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-fullwidth-5x', '2x' => $retina_size))
					);
					break;

				case '6':
					$thegem_sources = array(
						array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-2x-500', '2x' => $retina_size)),
						array('media' => '(min-width: 1495px) and (max-width: 1680px), (min-width: 550px) and (max-width: 1280px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-fullwidth-4x', '2x' => $retina_size)),
						array('media' => '(min-width: 1680px) and (max-width: 1920px), (min-width: 1280px) and (max-width: 1495px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-fullwidth-5x', '2x' => $retina_size))
					);
					break;
			}
		}
	} else {
		if ($params['style'] == 'justified' || $params['style'] == 'masonry') {
			switch ($params['layout']) {
				case '2x':
					$thegem_sources = array(
						array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-2x-500', '2x' => $retina_size)),
						array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-2x', '2x' => $retina_size))
					);
					break;

				case '3x':
					$thegem_sources = array(
						array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-2x-500', '2x' => $retina_size)),
						array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-3x', '2x' => $retina_size))
					);
					break;

				case '4x':
					$thegem_sources = array(
						array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-2x-500', '2x' => $retina_size)),
						array('media' => '(max-width: 1100px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-3x', '2x' => $retina_size)),
						array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-4x', '2x' => $retina_size))
					);
					break;
			}
		}
	}
}

if ($params['layout'] != '1x' && $thegem_highlight_type == 'horizontal') {
	$thegem_sources = array(
		array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $params['style'] . '-2x-500', '2x' => 'thegem-portfolio-' . $params['style']))
	);
}

$gap_size = round(intval($params['gaps_size'])/2);

if (!isset($product_grid_item_size)) {
	$product_hover_image_id = 0;
	if ($params['display_titles'] == 'page' && $params['hover'] != 'gradient' && $params['hover'] != 'circular') {
		$gallery = $product->get_gallery_image_ids();
		$has_product_hover = get_post_meta($post->ID, 'thegem_product_disable_hover', true);
		if (isset($gallery[0]) && !$has_product_hover) {
			$product_hover_image_id = $gallery[0];
			$thegem_classes[] = 'image-hover';
		}
	}

	$rating_count = $product->get_rating_count();
	if ($rating_count > 0) {
		$thegem_classes[] = 'has-rating';
	}

	$product_short_description = $product->get_short_description();
	$product_short_description = strip_shortcodes($product_short_description);
	$product_short_description = wp_strip_all_tags($product_short_description);
	$product_short_description_length = apply_filters( 'excerpt_length', 20 );
	$product_short_description_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
	$product_short_description = wp_trim_words($product_short_description, $product_short_description_length, $product_short_description_more);
}

?>

<?php if (!isset($product_grid_item_size)): ?>
	<div <?php post_class($thegem_classes); ?> style="padding: <?php echo intval($gap_size); ?>px;" data-default-sort="<?php echo intval(get_post()->menu_order); ?>" data-sort-date="<?php echo get_the_date('U'); ?>">
		<div class="item-separator-box"></div>
		<div class="wrap clearfix">
			<div <?php post_class($thegem_image_classes); ?>>
				<div class="image-inner">
					<?php if (has_post_thumbnail()): ?>
						<?php
							$picture_info = thegem_generate_picture(get_post_thumbnail_id(), $thegem_size, $thegem_sources, array('alt' => get_the_title()), true);
							if ($picture_info && !empty($picture_info['default']) && !empty($picture_info['default'][0]) && $product_hover_image_id) {
								$thegem_hover_size = $thegem_size;
								if ($params['style'] == 'masonry') {
									$thegem_hover_size = $thegem_size . '-' . $picture_info['default'][1] . '-' . $picture_info['default'][2];
									global $thegem_size_template_global, $picture_info_template_global, $thegem_hover_size_template_global;
									$thegem_size_template_global = $thegem_size;
									$picture_info_template_global = $picture_info;
									$thegem_hover_size_template_global = $thegem_hover_size;
									add_filter('thegem_image_sizes', function($sizes) {global $thegem_size_template_global, $picture_info_template_global, $thegem_hover_size_template_global; $size=$sizes[$thegem_size_template_global]; $size[1]=$picture_info_template_global['default'][2]; $size[2]=true; $sizes[$thegem_hover_size_template_global]=$size; return $sizes; });
									$thegem_sources = array();
								}
								if ($params['style'] == 'metro') {
									$thegem_hover_size = $thegem_size . '-' . $picture_info['default'][1] . '-' . $picture_info['default'][2];
									global $thegem_size_template_global, $picture_info_template_global, $thegem_hover_size_template_global;
									$thegem_size_template_global = $thegem_size;
									$picture_info_template_global = $picture_info;
									$thegem_hover_size_template_global = $thegem_hover_size;
									add_filter('thegem_image_sizes', function($sizes) {global $thegem_size_template_global, $picture_info_template_global, $thegem_hover_size_template_global; $size=$sizes[$thegem_size_template_global]; $size[0]=$picture_info_template_global['default'][1]; $size[2]=true; $sizes[$thegem_hover_size_template_global]=$size; return $sizes; });
									$thegem_sources = array();
								}
								thegem_generate_picture($product_hover_image_id, $thegem_hover_size, $thegem_sources, array(
									'alt' => get_the_title(),
									'class' => 'image-hover'
								));
							}
						?>
					<?php endif; ?>
				</div>
				<div class="overlay">
					<div class="overlay-circle"></div>
					<div class="links-wrapper">
						<div class="links">
							<div class="portfolio-icons product-bottom">
								<div class="portfolio-icons-inner clearfix">
									<?php do_action('woocommerce_after_shop_loop_item'); ?>
									<?php if(!$params['disable_socials']): ?>
										<a href="javascript: void(0);" class="icon share"></a>
									<?php endif; ?>
								</div>

								<div class="overlay-line"></div>
								<?php if(!$params['disable_socials']): ?>
									<div class="portfolio-sharing-pane"><?php thegem_socials_sharing(); ?></div>
								<?php endif; ?>
							</div>
							<?php if( ($params['display_titles'] == 'hover' && $params['layout'] != '1x') || $params['hover'] == 'gradient' || $params['hover'] == 'circular'): ?>
								<div class="caption">
									<div class="title title-h4">
										<?php if($params['hover'] != 'default' && $params['hover'] != 'gradient' && $params['hover'] != 'circular') { echo '<span class="light">'; } ?>
										<?php the_title(); ?>
										<?php if($params['hover'] != 'default') { echo '</span>'; } ?>
									</div>
									<div class="description">
										<?php if($product_short_description) : ?><div class="subtitle"><?php echo $product_short_description; ?></div><?php endif; ?>
									</div>
									<div class="product-info clearfix">
										<?php do_action('woocommerce_after_shop_loop_item_title'); ?>
										<?php do_action('woocommerce_before_shop_loop_item_title'); ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<?php if($params['display_titles'] == 'page' && $params['hover'] != 'gradient' && $params['hover'] != 'circular'): ?>
						<a href="<?php echo get_permalink(); ?>"></a>
					<?php endif; ?>
				</div>
			</div>
			<?php if( ($params['display_titles'] == 'page' || $params['layout'] == '1x') && $params['hover'] != 'gradient' && $params['hover'] != 'circular'): ?>
				<div <?php post_class($thegem_caption_classes); ?>>
					<div class="product-info clearfix">
						<?php do_action('woocommerce_before_shop_loop_item_title'); ?>
						<div class="title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></div>
						<?php do_action('woocommerce_after_shop_loop_item_title'); ?>
					</div>
					<div class="product-bottom clearfix">
						<?php do_action('woocommerce_after_shop_loop_item'); ?>
						<?php if(!$params['disable_socials']): ?>
							<div class="post-footer-sharing"><?php thegem_button(array('corner' => 0, 'icon' => 'share', 'size' => 'tiny', 'background_color' => 'transparent', 'extra_class' => 'bottom-product-link'), 1); ?><div class="sharing-popup"><?php thegem_socials_sharing(); ?><svg class="sharing-styled-arrow"><use xlink:href="<?php echo esc_url(get_template_directory_uri() . '/css/post-arrow.svg'); ?>#dec-post-arrow"></use></svg></div></div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="product-labels"><?php do_action('woocommerce_shop_loop_item_labels'); ?></div>
		</div>
	</div>
<?php else: ?>
	<div <?php post_class($thegem_classes); ?>>
	</div>
<?php endif; ?>
