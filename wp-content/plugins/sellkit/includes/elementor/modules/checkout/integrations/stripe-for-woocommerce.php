<?php

namespace Sellkit\Elementor\Modules\Checkout\Integrations;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Integrations\Integration;

/**
 * Integration class to integrate Payment Plugins for Stripe WooCommerce gateway with sellkit checkout widget.
 *
 * @since 1.1.0
 */
class Stripe_For_Woocommerce extends Integration {
	/**
	 * Check requirement to enable gateway in sellkit checkout widget.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	protected function requirements() {
		return class_exists( '\WC_Stripe_Field_Manager' );
	}

	/**
	 * Content of express checkout methods.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function content() {
		$gateways = [];

		foreach ( WC()->payment_gateways()->get_available_payment_gateways() as $gateway ) {
			if ( $gateway->supports( 'wc_stripe_banner_checkout' ) && $gateway->banner_checkout_enabled() ) {
				$gateways[ $gateway->id ] = $gateway;
			}
		}

		if ( count( $gateways ) > 0 ) {
			echo '<div class="sellkit-stripe-integration"><ul class="wc_stripe_checkout_banner_gateways">';
			foreach ( $gateways as $gateway ) :
				?>
					<li class="wc-stripe-checkout-banner-gateway banner_payment_method_<?php echo esc_attr( $gateway->id ); ?>"></li>
				<?php
			endforeach;
			echo '</ul></div>';
		}
	}

	/**
	 * Hooks to integrate current gateway with sellkit checkout widget.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function hooks() {
		remove_action( 'woocommerce_checkout_before_customer_details', [ 'WC_Stripe_Field_Manager', 'output_banner_checkout_fields' ] );
	}
}
