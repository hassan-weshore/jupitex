<?php
/**
 * JetWooBuilder Single Rating widget template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/single-product/rating.php
 */

global $product;

if ( ! is_a( $product, 'WC_Product' ) || 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
	return;
}

$settings = $this->get_settings_for_display();

$rating_icon  = isset( $settings['rating_icon'] ) ? $settings['rating_icon'] : 'jetwoo-front-icon-rating-1';
$empty_rating = isset( $settings['show_single_empty_rating'] ) ? filter_var( $settings['show_single_empty_rating'], FILTER_VALIDATE_BOOLEAN ) : false;
$rating       = jet_woo_builder_template_functions()->get_product_custom_rating( $rating_icon, $empty_rating );
$classes = [ 'woocommerce-product-rating' ];

if ( $empty_rating && empty( $product->get_average_rating() ) ) {
	$classes[] = 'empty';
}

if ( ! $rating ) {
	return;
}

$review_count   = $product->get_review_count();
$single_caption = isset( $settings['single_rating_reviews_link_caption_single'] ) ? esc_html( $settings['single_rating_reviews_link_caption_single'] ) : ' customer review';
$plural_caption = isset( $settings['single_rating_reviews_link_caption_plural'] ) ? esc_html( $settings['single_rating_reviews_link_caption_plural'] ) : ' customer reviews';

$counter_html = sprintf(
	_n( '(%s' . $single_caption . ')', '(%s' . $plural_caption . ')', $review_count, 'jet-woo-builder' ),
	'<span class="count">' . esc_attr( $review_count ) . '</span>'
);

$counter = apply_filters( 'jet-woo-builder/jet-single-rating/rating-counter-label', $counter_html, $review_count );
?>

<div class="<?php echo implode( ' ', $classes ); ?>">
	<?php echo $rating; ?>

	<?php if ( comments_open() ) : ?>
		<a href="#reviews" class="woocommerce-review-link" rel="nofollow">
			<?php echo $counter; ?>
		</a>
	<?php endif; ?>
</div>