<?php
/**
 * Cart item data (when outputting non-flat)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-item-data.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php foreach ( $item_data as $key => $data ) : ?>
	<span class="sellkit-checkout-variation-item">
		<?php echo wp_kses_post( $data['key'] ) . ':'; ?>
		<?php echo wp_kses_post( $data['display'] ); ?>
		<?php if ( array_key_last( $item_data ) !== $key ) : ?>
			<?php echo '<i class="fas fa-slash"></i>'; ?>
		<?php endif; ?>
	</span>
<?php endforeach; ?>
