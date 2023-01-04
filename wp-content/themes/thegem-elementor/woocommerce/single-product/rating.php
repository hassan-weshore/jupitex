<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product, $thegem_product_data;

if ( ! wc_review_ratings_enabled() ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

$isLegacy = $thegem_product_data['product_page_layout'] === 'legacy';
$isReviews = $thegem_product_data['product_page_elements_reviews'];
$isReviewText = $thegem_product_data['product_page_elements_reviews_text'];
$reviewText = '%s '.esc_html($thegem_product_data['product_page_elements_reviews_text']);

if ( $rating_count < 1 ) {
    return ;
}

if ( $isLegacy || (!$isLegacy && $isReviews) ) : ?>

	<div class="woocommerce-product-rating">
		<?php echo wc_get_rating_html( $average, $rating_count ); // WPCS: XSS ok. ?>
		<?php if ( comments_open() ) : ?>
			<?php //phpcs:disable ?>
            <div class="product-reviews-link">
                <a href="<?php the_permalink(); ?>#tab-reviews" class="woocommerce-review-link" rel="nofollow">
                    <?php printf( _n( $isReviewText ? $reviewText : '%s customer review', $isReviewText ? $reviewText : '%s customer reviews', $review_count, 'woocommerce' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>
                </a>
            </div>
			<?php // phpcs:enable ?>
		<?php endif ?>
	</div>

<?php endif; ?>
