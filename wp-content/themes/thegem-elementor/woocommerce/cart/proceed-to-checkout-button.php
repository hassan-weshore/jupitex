<?php
/**
 * Proceed to checkout button
 *
 * Contains the markup for the proceed to checkout button on the cart.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/proceed-to-checkout-button.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$thegem_cart_layout = thegem_get_option('cart_layout', 'modern');
?>
<?php
	thegem_button(array(
		'tag' => 'a',
		'href' => esc_url( wc_get_checkout_url() ),
		'text' => esc_html__( 'Proceed to checkout', 'woocommerce' ),
		'size' => $thegem_cart_layout != 'modern' ? 'medium' : 'small',
		'extra_class' => 'checkout-button-button',
		'attributes' => array(
			'class' => 'checkout-button button alt wc-forward gem-button-tablet-size-small'
		)
	), true);
?>
