<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
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
?>
<div class="woocommerce-billing-fields sellkit-one-page-checkout-billing sellkit-checkout-local-fields">
	<h4 class="sellkit-one-page-checkout-billing-header heading">
		<?php esc_html_e( 'Billing details', 'woocommerce' ); ?>
	</h4>
	<p class="billing-desc sub-heading">
		<?php echo apply_filters( 'sellkit_core/widgets/checkout/custom_message/select_address_text', __( 'Select the address that matches your card or payment method.', 'sellkit' ) ); ?>
	</p>
	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<?php if ( WC()->cart->needs_shipping() ) : // Display this section just if we need to ship products. ?>

	<div class="billing-method" >
		<div>
			<label class="wrp method-a">
				<input type="radio" value="same" name="billing-method" class="sellkit-billing-method-a">
				<span class="checkmark"></span>
				<span class="labels"><?php echo esc_html__( 'Same as shipping address', 'sellkit' ); ?></span>
			</label>
		</div>
		<hr>
		<div>
			<label class="wrp method-b">
				<input type="radio" value="diff" name="billing-method" class="sellkit-billing-method-b" checked >
				<span class="checkmark"></span>
				<span class="labels"><?php echo esc_html__( 'Use a different billing address', 'sellkit' ); ?></span>
			</label>
		</div>
	</div>

	<?php endif; ?>

	<div class="woocommerce-billing-fields__field-wrapper" id="sellkit-checkout-billing-field-wrapper">
		<?php
			$fields = $checkout->get_checkout_fields( 'billing' );

			do_action( 'sellkit_checkout_billing_fields', $fields, $checkout );
		?>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
	<div class="sub-heading">
		<?php wc_get_template( 'checkout/terms.php' ); ?>
	</div>
</div>
