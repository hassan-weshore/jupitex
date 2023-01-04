<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
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
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;

$show_title = apply_filters( 'sellkit-checkout-disable-shipping-fields-title', true );

if ( WC()->cart->needs_shipping() ) : ?>
	<div class="woocommerce-shipping-fields sellkit-one-page-checkout-shipping sellkit-checkout-local-fields">

		<div class="shipping_address">
			<?php if ( true === $show_title ) : ?>
			<h4 id="shipping_text_title" class="header heading" style="width:100%">
				<?php echo __( 'Shipping address', 'sellkit' ); ?>
			</h4>
			<?php endif; ?>

			<div id="ship-to-different-address">
				<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" type="checkbox" name="ship_to_different_address" value="1" checked/>
			</div>

			<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

			<div class="woocommerce-shipping-fields__field-wrapper" id="sellkit-checkout-widget-shipping-fields">
				<?php
					$fields = $checkout->get_checkout_fields( 'shipping' );

					do_action( 'sellkit_checkout_shipping_fields', $fields, $checkout );
				?>
			</div>

			<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>
		</div>
	</div>
<?php endif; ?>
	<div class="sellkit-woocommerce-additional-fields">
		<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>
		<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
	</div>
	<?php do_action( 'sellkit_checkout_after_shipping_section' ); ?>
