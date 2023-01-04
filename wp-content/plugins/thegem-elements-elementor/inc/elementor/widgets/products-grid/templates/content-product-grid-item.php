<?php use Elementor\Icons_Manager; ?>
<?php
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

if (!empty($settings['hover_icon_wishlist']['value'])) {
	$wishlist_icon = $settings['hover_icon_wishlist']['value'];
	$wishlist_attribute_string = 'icon="' . $wishlist_icon . '" link_classes="wishlist add_to_wishlist icon"';
} else {
	$wishlist_attribute_string = 'icon="fa fa-heart-o" link_classes="wishlist add_to_wishlist icon default"';
}

if (!isset($product_grid_item_size)): ?>
	<div <?php post_class($thegem_classes, $post_id); ?>
			style="padding: calc(<?= $settings['image_gaps']['size'].$settings['image_gaps']['unit'] ?>/2)"
			data-default-sort="<?php echo intval(get_post()->menu_order); ?>"
			data-sort-date="<?php echo get_the_date('U'); ?>">
		<div class="item-separator-box"></div>
		<div class="wrap clearfix">
			<div <?php post_class($thegem_image_classes, $post_id); ?>>
				<div class="image-inner">
					<?php if (has_post_thumbnail($post_id)): ?>
						<?php
						$picture_info = thegem_generate_picture(get_post_thumbnail_id($post_id), $thegem_size, $thegem_sources, array('alt' => get_the_title($post_id), 'style' => 'max-width: 110%'), true);
						if ($picture_info && !empty($picture_info['default']) && !empty($picture_info['default'][0]) && $product_hover_image_id) {
							$thegem_hover_size = $thegem_size;
							if ($settings['layout'] == 'masonry') {
								$thegem_hover_size = $thegem_size . '-' . $picture_info['default'][1] . '-' . $picture_info['default'][2];
								global $thegem_size_template_global, $picture_info_template_global, $thegem_hover_size_template_global;
								$thegem_size_template_global = $thegem_size;
								$picture_info_template_global = $picture_info;
								$thegem_hover_size_template_global = $thegem_hover_size;
								add_filter('thegem_image_sizes', function ($sizes) {
									global $thegem_size_template_global, $picture_info_template_global, $thegem_hover_size_template_global;
									$size = $sizes[$thegem_size_template_global];
									$size[1] = $picture_info_template_global['default'][2];
									$size[2] = true;
									$sizes[$thegem_hover_size_template_global] = $size;
									return $sizes;
								});
								$thegem_sources = array();
							}
							if ($settings['layout'] == 'metro') {
								$thegem_hover_size = $thegem_size . '-' . $picture_info['default'][1] . '-' . $picture_info['default'][2];
								global $thegem_size_template_global, $picture_info_template_global, $thegem_hover_size_template_global;
								$thegem_size_template_global = $thegem_size;
								$picture_info_template_global = $picture_info;
								$thegem_hover_size_template_global = $thegem_hover_size;
								add_filter('thegem_image_sizes', function ($sizes) {
									global $thegem_size_template_global, $picture_info_template_global, $thegem_hover_size_template_global;
									$size = $sizes[$thegem_size_template_global];
									$size[0] = $picture_info_template_global['default'][1];
									$size[2] = true;
									$sizes[$thegem_hover_size_template_global] = $size;
									return $sizes;
								});
								$thegem_sources = array();
							}
							thegem_generate_picture($product_hover_image_id, $thegem_hover_size, $thegem_sources, array(
								'alt' => get_the_title(),
								'class' => 'image-hover',
								'style' => 'max-width: 110%'
							));
						}
						?>
					<?php endif; ?>
				</div>
				<div class="overlay">
					<div class="overlay-circle"></div>
					<div class="links-wrapper">
						<div class="links">
							<?php if ($settings['icons_show'] == 'yes'): ?>
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
												<?php if (isset($settings['hover_icon_cart']['value'])) {
													Icons_Manager::render_icon($settings['hover_icon_cart'], ['aria-hidden' => 'true']);
												} else { ?>
													<i class="default"></i>
												<?php } ?>
												<?php echo esc_html($product->add_to_cart_text()) ?>
											</a>
										<?php endif; ?>

										<!-- Product Link -->
										<a href="<?= esc_url(get_the_permalink()); ?>"
										   class="icon product-page bottom-product-link">
											<?php if (isset($settings['hover_icon_product-page']['value'])) {
												Icons_Manager::render_icon($settings['hover_icon_product-page'], ['aria-hidden' => 'true']);
											} else { ?>
												<i class="default"></i>
											<?php } ?>
										</a>

										<!-- YITH -->
										<?php if (defined('YITH_WCWL') && $settings['product_show_wishlist'] == 'yes'): ?>
											<span class="icon yith-icon">
												<?php if (isset($settings['hover_icon_wishlist']['value'])) {
													Icons_Manager::render_icon($settings['hover_icon_wishlist'], ['aria-hidden' => 'true']);
												} else { ?>
													<i class="default"></i>
												<?php } ?>
												<?php echo do_shortcode("[yith_wcwl_add_to_wishlist icon='' link_classes='wishlist add_to_wishlist icon' thegem_products_grid='1']"); ?>
											</span>
										<?php endif; ?>

										<?php if ($settings['social_sharing'] == 'yes'): ?>
											<a href="javascript: void(0);" class="icon share">
												<?php if (isset($settings['hover_icon_share']['value'])) {
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
							<?php if (($settings['caption_position'] == 'hover' && $settings['columns'] != '1x') || $hover_effect == 'gradient' || $hover_effect == 'circular'): ?>
								<div class="caption">
									<?php if ($settings['product_show_title'] == 'yes') { ?>
										<div class="title title-h4">
											<?php if ($hover_effect != 'default' && $hover_effect != 'gradient' && $hover_effect != 'circular') {
												echo '<span class="light">';
											} ?>
											<?php the_title(); ?>
											<?php if ($hover_effect != 'default') {
												echo '</span>';
											} ?>
										</div>
									<?php } ?>
									<?php if ($settings['caption_position'] != 'hover' || $settings['product_show_description'] == 'yes') { ?>
										<div class="description">
											<?php if ($product_short_description) : ?>
												<div class="subtitle"><?php echo $product_short_description; ?></div>
											<?php endif; ?>
										</div>
									<?php } ?>
									<div class="product-info clearfix">
										<?php if ($settings['product_show_price'] == 'yes') { ?>
											<?php do_action('woocommerce_after_shop_loop_item_title'); ?>
										<?php } ?>
										<?php if ($settings['product_show_reviews'] == 'yes') { ?>
											<?php do_action('woocommerce_before_shop_loop_item_title'); ?>
										<?php } else { ?>
											<div class="product-rating product-rating-empty">
												<div class="empty-rating"></div>
											</div>
										<?php } ?>
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
			<?php if (($settings['caption_position'] == 'page' || $settings['columns'] == '1x') && $hover_effect != 'gradient' && $hover_effect != 'circular'): ?>
				<div <?php post_class($thegem_caption_classes); ?>>
					<div class="product-info clearfix">
						<?php if ($settings['product_show_reviews'] == 'yes') { ?>
							<?php do_action('woocommerce_before_shop_loop_item_title'); ?>
						<?php } else { ?>
							<div class="product-rating product-rating-empty">
								<div class="empty-rating"></div>
							</div>
						<?php } ?>
						<?php if ($settings['product_show_title'] == 'yes') { ?>
							<div class="title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></div>
						<?php } ?>
						<?php if ($settings['product_show_price'] == 'yes') { ?>
							<?php do_action('woocommerce_after_shop_loop_item_title'); ?>
						<?php } ?>
					</div>
					<?php if ($settings['caption_icons_show'] == 'yes'): ?>
						<div class="product-bottom clearfix">

							<!-- Add to cart -->
							<?php if ($product->is_in_stock()): ?>
								<a href="<?php echo $product->add_to_cart_url() ?>" data-quantity="1"
								   class="icon cart button product_type_simple add_to_cart_button ajax_add_to_cart"
								   data-product_id="<?php echo $product->get_id(); ?>"
								   data-product_sku="<?php echo $product->get_sku(); ?>"
								   aria-label="<?php echo 'Add ' . $product->get_name() . ' to your cart'; ?>"
								   rel="nofollow">
									<?php if (isset($settings['hover_icon_cart']['value'])) {
										Icons_Manager::render_icon($settings['hover_icon_cart'], ['aria-hidden' => 'true']);
									} else { ?>
										<i class="default"></i>
									<?php } ?>
									<?php echo esc_html($product->add_to_cart_text()) ?>
								</a>
							<?php endif; ?>

							<!-- Product Link -->
							<a href="<?= esc_url(get_the_permalink()); ?>"
							   class="icon product-page bottom-product-link">
								<?php if (isset($settings['hover_icon_product-page']['value'])) {
									Icons_Manager::render_icon($settings['hover_icon_product-page'], ['aria-hidden' => 'true']);
								} else { ?>
									<i class="default"></i>
								<?php } ?>
							</a>

							<!-- YITH -->
							<?php if (defined('YITH_WCWL') && $settings['product_show_wishlist'] == 'yes'): ?>
								<span class="icon yith-icon">
												<?php if (isset($settings['hover_icon_wishlist']['value'])) {
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
										<?php if (isset($settings['hover_icon_share']['value'])) {
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
					<?php if ($settings['product_show_price'] == 'yes' && !$product->get_price_html()) { ?>
						<span class="price empty-price">OUT</span>
					<?php } ?>
				</div>
			<?php endif; ?>
			<div class="product-labels">
				<?php if ($settings['product_show_sale'] == 'yes' && $product->is_on_sale()) : ?>
					<?php echo apply_filters('woocommerce_sale_flash', '<span class="onsale title-h6"><span class="rotate-back"><span class="text">' . $settings['sale_label_text'] . '</span></span></span>', $post, $product); ?>
				<?php endif; ?>
				<?php if ($settings['product_show_new'] == 'yes' && $product->is_featured()) : ?>
					<?php echo apply_filters('thegem_woocommerce_featured_flash', '<span class="new-label title-h6"><span class="rotate-back"><span class="text">' . $settings['new_label_text'] . '</span></span></span>', $post, $product); ?>
				<?php endif; ?>
				<?php if ($settings['product_show_out'] == 'yes' && !$product->is_in_stock()) : ?>
					<?php echo apply_filters('thegem_woocommerce_out_of_stock_flash', '<span class="out-of-stock-label title-h6"><span class="rotate-back"><span class="text">' . $settings['out_label_text'] . '</span></span></span>', $post, $product); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php else:
	array_push($thegem_classes,'size-item'); ?>
	<div <?php post_class($thegem_classes); ?>>
	</div>
<?php endif; ?>