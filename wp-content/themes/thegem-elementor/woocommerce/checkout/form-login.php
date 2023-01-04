<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
	return;
}

$thegem_checkout_type = thegem_checkout_get_type();

?>
<?php if ($thegem_checkout_type != 'multi-step') : ?>
<div class="checkout-notice checkout-login-notice">
	<?php echo apply_filters( 'woocommerce_checkout_login_message', esc_html__( 'Returning customer?', 'woocommerce' ) ) . ' <a href="#" class="showlogin checkout-show-login-popup">' . esc_html__( 'Click here to login', 'woocommerce' ) . '</a>'; ?>
</div>

<div id="checkout-login-popup" class="woocommerce" style="display: none;"><div class="checkout-login">
<?php endif; ?>

<?php if($thegem_checkout_type == 'multi-step') : ?>
<div class="checkout-contents" data-tab-content-id="checkout-signin">
<div class="row" id="customer_details">
<div class="col-sm-6 col-xs-12 checkout-login">
<?php endif; ?>

<<?php echo ($thegem_checkout_type == 'one-page-modern' ? 'h3' : 'h2'); ?> class="light"><?php echo apply_filters( 'woocommerce_checkout_login_message', esc_html__( 'Existing customer', 'thegem' ) ); ?></<?php echo ($thegem_checkout_type == 'one-page-modern' ? 'h3' : 'h2'); ?>>

<?php

woocommerce_login_form(
	array(
		'message'  => '',
		'redirect' => wc_get_checkout_url(),
		'hidden'   => false,
	)
);

?>

<?php if ($thegem_checkout_type != 'multi-step') : ?>
</div></div>
<?php endif; ?>

<?php if($thegem_checkout_type == 'multi-step') : ?>
<?php if ($checkout->enable_guest_checkout || $checkout->enable_signup): ?>
</div>
<div class="col-sm-6 col-xs-12 checkout-signin">
	<h2><span class="light"><?php esc_html_e('New customer','thegem'); ?></span></h2>
	<?php
		if (!$checkout->is_registration_required()) {
			thegem_button(array(
				'tag' => 'button',
				'text' => esc_html__( 'Checkout as guest', 'thegem' ),
				'style' => 'flat',
				'extra_class' => 'checkout-as-guest',
				'attributes' => array(
					'type' => 'button',
				)
			), true);
		}
	?>
	<?php
		if ($checkout->is_registration_enabled()) {
			thegem_button(array(
				'tag' => 'button',
				'text' => esc_html__( 'Create an account', 'thegem' ),
				'style' => 'flat',
				'extra_class' => 'checkout-create-account',
				'attributes' => array(
					'type' => 'button',
				)
			), true);
		}
	?>
</div>
<?php endif; ?>
</div><!-- #customer_details -->
</div>
<?php endif; ?>
