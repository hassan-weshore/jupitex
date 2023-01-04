<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $thegem_product_data;

thegem_woocommerce_rating_separator();

$isLegacy = $thegem_product_data['product_page_layout'] === 'legacy';
$isPrice = $thegem_product_data['product_page_elements_price'];
$isPriceStrikethrough = $thegem_product_data['product_page_elements_price_strikethrough'];

?>

<?php if ($isLegacy || $isPrice): ?>
    <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?> <?=!$isPriceStrikethrough ? 'not-strikethrough' : null?>">
        <?php echo $product->get_price_html(); ?>
    </p>
<?php endif; ?>
