<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post, $thegem_product_data;

$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );

$isLegacy = $thegem_product_data['product_page_layout'] === 'legacy';
$isDescription = $thegem_product_data['product_page_elements_description'];

if ( ! $short_description ) {
	return;
}

?>

<?php if ($isLegacy || $isDescription): ?>
    <div class="woocommerce-product-details__short-description">
        <?php echo $short_description; // WPCS: XSS ok. ?>
    </div>
<?php endif; ?>