<?php

global $post, $product, $woocommerce_loop;

$params = $GLOBALS['thegem_grid_params'];

$terms = get_the_terms($post->ID, 'product_cat');
$slugs = array();
foreach ($terms as $term) {
	$slugs[] = $term->slug;
}

$thegem_classes = array('portfolio-item', 'inline-column', 'product', 'portfolio-1x-' . $params['layout_version'] . '-item');
$thegem_classes = array_merge($thegem_classes, $slugs);

$thegem_image_classes = array('image');
$thegem_caption_classes = array('caption');

if($params['caption_position'] == 'zigzag') {
	$thegem_caption_position = 'right';
} else {
	$thegem_caption_position = $params['caption_position'];
}

$thegem_classes = array_merge($thegem_classes, array('col-xs-12'));

if($params['display_titles'] == 'page' && $params['hover'] != 'gradient' && $params['hover'] != 'circular') {
	if($params['layout_version'] == 'fullwidth') {
		$thegem_image_classes = array_merge($thegem_image_classes, array('col-md-8', 'col-xs-12'));
		$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-md-4', 'col-xs-12'));
		if($thegem_caption_position == 'left') {
			$thegem_image_classes = array_merge($thegem_image_classes, array('col-md-push-4'));
			$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-md-pull-8'));
		}
	} else {
		$thegem_image_classes = array_merge($thegem_image_classes, array('col-md-7', 'col-xs-12'));
		$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-md-5', 'col-xs-12'));
		if($thegem_caption_position == 'left') {
			$thegem_image_classes = array_merge($thegem_image_classes, array('col-md-push-5'));
			$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-md-pull-7'));
		}
	}
}

$thegem_size = 'thegem-portfolio-1x';
if($params['display_titles'] == 'hover' || $params['hover'] == 'gradient' || $params['hover'] == 'circular') {
	$thegem_size .= '-hover';
} else {
	$thegem_size .= $params['layout_version'] == 'sidebar' ? '-sidebar' : '';
}

$thegem_bottom_line = false;
if(!$params['disable_socials']) {
	$thegem_bottom_line = true;
}

$thegem_classes[] = 'item-animations-not-inited';

$gap_size = round(intval($params['gaps_size'])/2);

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

?>

<div <?php post_class($thegem_classes); ?> style="padding: <?php echo intval($gap_size); ?>px;" data-sort-date="<?php echo get_the_date('U'); ?>">
	<div class="item-separator-box"></div>
	<div class="wrap clearfix">
		<div <?php post_class($thegem_image_classes); ?>>
			<div class="image-inner">
				<?php if (has_post_thumbnail()): ?>
					<?php
						$thegem_sources = array();
						$picture_info = thegem_generate_picture(get_post_thumbnail_id(), $thegem_size, $thegem_sources, array('alt' => get_the_title()), true);
						if ($picture_info && !empty($picture_info['default']) && !empty($picture_info['default'][0]) && $product_hover_image_id) {
							$thegem_hover_size = $thegem_size;
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
						<?php if($params['display_titles'] == 'hover' || $params['hover'] == 'gradient' || $params['hover'] == 'circular'): ?>
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
		<?php if($params['display_titles'] == 'page' && $params['hover'] != 'gradient' && $params['hover'] != 'circular'): ?>
			<div <?php post_class($thegem_caption_classes); ?>>
				<div class="caption-sizable-content<?php echo ($thegem_bottom_line ? ' with-bottom-line' : ''); ?>">
					<div class="title title-h3">
						<a href="<?php echo get_permalink(); ?>"><span class="light"><?php the_title(); ?></span></a>
					</div>
					<div class="product-info clearfix">
						<?php do_action('woocommerce_before_shop_loop_item_title'); ?>
						<?php do_action('woocommerce_after_shop_loop_item_title'); ?>
					</div>
					<?php if($product_short_description) : ?><div class="subtitle"><?php echo $product_short_description; ?></div><?php endif; ?>
				</div>
				<div class="caption-bottom-line">
					<div class="product-bottom">
						<?php do_action('woocommerce_after_shop_loop_item'); ?>
						<?php if(!$params['disable_socials']): ?>
							<div class="post-footer-sharing"><?php thegem_button(array('corner' => 0, 'icon' => 'share', 'size' => 'tiny', 'background_color' => 'transparent', 'extra_class' => 'bottom-product-link'), 1); ?><div class="sharing-popup"><?php thegem_socials_sharing(); ?><svg class="sharing-styled-arrow"><use xlink:href="<?php echo esc_url(get_template_directory_uri() . '/css/post-arrow.svg'); ?>#dec-post-arrow"></use></svg></div></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<div class="product-labels"><?php do_action('woocommerce_shop_loop_item_labels'); ?></div>
	</div>
</div>
