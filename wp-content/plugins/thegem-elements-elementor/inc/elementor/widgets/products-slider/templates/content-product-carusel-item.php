<?php

use Elementor\Icons_Manager;

global $post, $product, $woocommerce_loop;

remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

if (!empty($settings['hover_icon_wishlist']['value'])) {
	$wishlist_icon = $settings['hover_icon_wishlist']['value'];
	$wishlist_attribute_string = 'icon="' . $wishlist_icon . '" link_classes="wishlist add_to_wishlist icon"';
} else {
	$wishlist_attribute_string = 'icon="fa fa-heart-o" link_classes="wishlist add_to_wishlist icon default"';
}

$rating_count = 0;
$review_count = 0;
$average = 0;

if (wc_review_ratings_enabled()) {
	$rating_count = $product->get_rating_count();
	$review_count = $product->get_review_count();
	$average = $product->get_average_rating();
}

$thegem_classes = array('portfolio-item', 'inline-column', 'product');

$thegem_image_classes = array('image');
$thegem_caption_classes = array('caption');

$thegem_sizes = thegem_image_sizes();
$thegem_build = '';


if ($settings['columns'] === '3x') {
	$thegem_size = 'thegem-portfolio-carusel-3x';
	$thegem_classes = array_merge($thegem_classes, array('col-md-4', 'col-xs-6'));
	$thegem_build = '3x';
} else if ($settings['columns'] === '2x') {
	$thegem_size = 'thegem-portfolio-carusel-2x';
	$thegem_classes = array_merge($thegem_classes, array('col-md-6', 'col-xs-6'));
	$thegem_build = '2x';
} else {
	if ($settings['columns_100'] === '3') {
		$thegem_size = 'thegem-portfolio-carusel-full-3x';
		$thegem_build = '3xfull';
	}
	if ($settings['columns_100'] === '4') {
		$thegem_size = 'thegem-portfolio-carusel-4x';
		$thegem_build = '4xfull';
	}
	if ($settings['columns_100'] === '5' || $settings['columns_100'] === '6') {
		$thegem_size = 'thegem-portfolio-carusel-5x';
		$thegem_build = '5xfull';
	}
}

if ($settings['style'] === 'masonry') {
	$thegem_size .= '-masonry';
}

//$thegem_size .= '-masonry';

if ('yes' === $settings['loading_animation']) {
	$thegem_classes[] = 'lazy-loading-item';
}

$product_hover_image_id = 0;
if ($settings['caption_position'] === 'page' && $hover_effect != 'gradient' && $hover_effect != 'circular') {
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
$product_short_description_length = apply_filters('excerpt_length', 20);
$product_short_description_more = apply_filters('excerpt_more', ' ' . '[&hellip;]');
$product_short_description = wp_trim_words($product_short_description, $product_short_description_length, $product_short_description_more);

?>


<div <?php post_class($thegem_classes); ?>
	<?php if ('yes' === $settings['loading_animation']): ?>data-ll-effect="move-up"<?php endif; ?>
	data-sort-date="<?php echo get_the_date('U'); ?>">
	<div class="item-separator-box"></div>
	<div class="wrap clearfix">
		<div <?php post_class($thegem_image_classes); ?>>
			<div class="image-inner <?php echo $settings['style'] === 'justified' ? $thegem_size : ''; ?>">
				<?php if (has_post_thumbnail()): ?>
					<?php
					$thegem_sources = array();
					$picture_info = thegem_generate_picture(get_post_thumbnail_id(), $thegem_size, $thegem_sources, array('alt' => get_the_title()), true);
					if ($picture_info && !empty($picture_info['default']) && !empty($picture_info['default'][0]) && $product_hover_image_id) {
						$thegem_hover_size = $thegem_size;
						if ($settings['style'] === 'masonry') {
							$thegem_hover_size = $thegem_size . '-' . $picture_info['default'][1] . '-' . $picture_info['default'][2];
							// add_filter('thegem_image_sizes', create_function('$sizes', '$size=$sizes["' . $thegem_size . '"]; $size[1]=' . $picture_info['default'][2] . '; $size[2]=true; $sizes["' . $thegem_hover_size . '"]=$size; return $sizes;'));
							add_filter('thegem_image_sizes', function ($sizes) use ($thegem_size, $thegem_hover_size, $picture_info) {
								$size = $sizes[$thegem_size];
								$size[1] = $picture_info['default'][2];
								$size[2] = true;
								$sizes[$thegem_hover_size] = $size;
								return $sizes;
							});
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
						<?php if ('yes' === $settings['icons_show'] || $settings['caption_position'] == 'page'): ?>
							<div class="portfolio-icons product-bottom">
								<div class="portfolio-icons-inner clearfix">
									<!-- Add to cart -->
									<?php if ($product->is_in_stock()): ?>
										<a href="<?php echo $product->add_to_cart_url() ?>" data-quantity="1"
										   class="icon cart button product_type_simple add_to_cart_button ajax_add_to_cart"
										   data-product_id="<?php echo $product->get_id(); ?>"
										   data-product_sku="<?php echo $product->get_sku(); ?>"
										   aria-label="<?php echo 'Add ' . $product->get_name() . ' to your cart'; ?>"
										   rel="nofollow">
											<?php if ($settings['hover_icon_cart']['value']) {
												Icons_Manager::render_icon($settings['hover_icon_cart'], ['aria-hidden' => 'true']);
											} else { ?>
												<i class="default"></i>
											<?php } ?>
											<?php echo esc_html($product->add_to_cart_text()) ?>
										</a>
									<?php endif; ?>

									<!-- Product Link -->
									<a href="<?= esc_url(get_the_permalink()); ?>"
									   class="icon product-page bottom-product-link full-product-link">
										<?php if ($settings['hover_icon_full']['value']) {
											Icons_Manager::render_icon($settings['hover_icon_full'], ['aria-hidden' => 'true']);
										} else { ?>
											<i class="default"></i>
										<?php } ?>
									</a>

									<!-- YITH -->
									<?php if (defined('YITH_WCWL') && $settings['products_show_wishlist'] == 'yes'): ?>
										<span class="icon yith-icon">
												<?php if ($settings['hover_icon_wishlist']['value']) {
													Icons_Manager::render_icon($settings['hover_icon_wishlist'], ['aria-hidden' => 'true']);
												} else { ?>
													<i class="default"></i>
												<?php } ?>
											<?php echo do_shortcode("[yith_wcwl_add_to_wishlist icon='' link_classes='wishlist add_to_wishlist icon' thegem_products_grid='1']"); ?>
											</span>
									<?php endif; ?>

									<?php if ($settings['social_sharing'] == 'yes'): ?>
										<a href="javascript: void(0);" class="icon share">
											<?php if ($settings['hover_icon_share']['value']) {
												Icons_Manager::render_icon($settings['hover_icon_share'], ['aria-hidden' => 'true']);
											} else { ?>
												<i class="default"></i>
											<?php } ?>
										</a>
									<?php endif; ?>
								</div>

								<div class="overlay-line"></div>
								<?php if ($settings['social_sharing'] == 'yes'): ?>
									<div class="portfolio-sharing-pane"><?php include 'socials-sharing.php'; ?></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if ($settings['caption_position'] == 'hover' || $hover_effect == 'gradient' || $hover_effect == 'circular'): ?>
							<div class="caption">
								<?php if ($settings['products_show_title'] == 'yes') : ?>
									<div class="title title-h4">
										<?php if ($hover_effect != 'default' && $hover_effect != 'gradient' && $hover_effect != 'circular') {
											echo '<span class="light">';
										} ?>
										<?php the_title(); ?>
										<?php if ($hover_effect != 'default') {
											echo '</span>';
										} ?>
									</div>
								<?php endif; ?>
								<?php if ($settings['caption_position'] != 'hover' || $settings['products_show_description'] == 'yes') : ?>
									<div class="description">
										<?php if ($product_short_description) : ?>
											<div class="subtitle"><?php echo $product_short_description; ?></div><?php endif; ?>
									</div>
								<?php endif; ?>
								<div class="product-info clearfix">
									<?php if ($settings['products_show_price'] == 'yes') {
										do_action('woocommerce_after_shop_loop_item_title');
									}
									if ($rating_count > 0 && wc_review_ratings_enabled() && 'yes' === $settings['products_show_reviews']) {
										do_action('woocommerce_before_shop_loop_item_title');
									} ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php if ($settings['caption_position'] == 'page'): ?>
					<a href="<?php echo get_permalink(); ?>"></a>
				<?php endif; ?>
			</div>
		</div>
		<?php if ($settings['caption_position'] == 'page'): ?>
			<div <?php post_class($thegem_caption_classes); ?>>
				<div class="product-info clearfix">
					<?php if ($rating_count > 0 && wc_review_ratings_enabled() && 'yes' === $settings['products_show_reviews']) { ?>

						<?php do_action('woocommerce_before_shop_loop_item_title'); ?>

					<?php } else { ?>

						<div class="product-rating product-rating-empty">
							<div class="empty-rating">

							</div>
						</div>

					<?php } ?>

					<?php if ($settings['products_show_title'] == 'yes') : ?>
						<div class="title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></div>
					<?php endif; ?>
					<?php if ($settings['products_show_price'] == 'yes') { ?>
						<?php do_action('woocommerce_after_shop_loop_item_title'); ?>
					<?php } ?>
				</div>
				<?php if ('yes' === $settings['capicons_show'] && $settings['caption_position'] == 'page'): ?>
					<div class="product-bottom clearfix">
						<!-- Add to cart -->
						<?php if ($product->is_in_stock()): ?>
							<a href="<?php echo $product->add_to_cart_url() ?>" data-quantity="1"
							   class="icon cart button product_type_simple add_to_cart_button ajax_add_to_cart"
							   data-product_id="<?php echo $product->get_id(); ?>"
							   data-product_sku="<?php echo $product->get_sku(); ?>"
							   aria-label="<?php echo 'Add ' . $product->get_name() . ' to your cart'; ?>"
							   rel="nofollow">
								<?php if ($settings['hover_icon_cart']['value']) {
									Icons_Manager::render_icon($settings['hover_icon_cart'], ['aria-hidden' => 'true']);
								} else { ?>
									<i class="default"></i>
								<?php } ?>
								<?php echo esc_html($product->add_to_cart_text()) ?>
							</a>
						<?php endif; ?>

						<!-- Product Link -->
						<a href="<?= esc_url(get_the_permalink()); ?>"
						   class="icon product-page bottom-product-link full-product-link">
							<?php if ($settings['hover_icon_full']['value']) {
								Icons_Manager::render_icon($settings['hover_icon_full'], ['aria-hidden' => 'true']);
							} else { ?>
								<i class="default"></i>
							<?php } ?>
						</a>

						<!-- YITH -->
						<?php if (defined('YITH_WCWL') && $settings['products_show_wishlist'] == 'yes'): ?>
							<span class="icon yith-icon">
												<?php if ($settings['hover_icon_wishlist']['value']) {
													Icons_Manager::render_icon($settings['hover_icon_wishlist'], ['aria-hidden' => 'true']);
												} else { ?>
													<i class="default"></i>
												<?php } ?>
								<?php echo do_shortcode("[yith_wcwl_add_to_wishlist icon='' link_classes='wishlist add_to_wishlist icon' thegem_products_grid='1']"); ?>
											</span>
						<?php endif; ?>

						<!-- Sharing -->
						<?php if ($settings['social_sharing'] == 'yes'): ?>
							<div class="post-footer-sharing">
								<a href="javascript: void(0);" class="icon share bottom-product-link">
									<?php if ($settings['hover_icon_share']['value']) {
										Icons_Manager::render_icon($settings['hover_icon_share'], ['aria-hidden' => 'true']);
									} else { ?>
										<i class="default"></i>
									<?php } ?>
								</a>

								<div class="sharing-popup"><?php include 'socials-sharing.php'; ?>
									<svg class="sharing-styled-arrow">
										<use xlink:href="<?php echo esc_url(get_template_directory_uri() . '/css/post-arrow.svg'); ?>#dec-post-arrow"></use>
									</svg>
								</div>

							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="product-labels">
			<?php if ($settings['products_show_sale_label'] == 'yes' && $product->is_on_sale()) : ?>
				<?php echo apply_filters('woocommerce_sale_flash', '<span class="onsale title-h6"><span class="rotate-back"><span class="text">' . $settings['products_text_sale_label'] . '</span></span></span>', $post, $product); ?>
			<?php endif; ?>
			<?php if ($settings['products_show_new_label'] == 'yes' && $product->is_featured()) : ?>
				<?php echo apply_filters('thegem_woocommerce_featured_flash', '<span class="new-label title-h6"><span class="rotate-back"><span class="text">' . $settings['products_text_new_label'] . '</span></span></span>', $post, $product); ?>
			<?php endif; ?>
			<?php if ($settings['products_show_out_label'] == 'yes' && !$product->is_in_stock()) : ?>
				<?php echo apply_filters('thegem_woocommerce_out_of_stock_flash', '<span class="out-of-stock-label title-h6"><span class="rotate-back"><span class="text">' . $settings['products_text_out_label'] . '</span></span></span>', $post, $product); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
