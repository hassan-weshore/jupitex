<?php

namespace Sellkit\Elementor\Modules\Checkout\Integrations;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Integrations\Integration;

/**
 * Integration class to integrate paypal for WooCommerce gateway with sellkit checkout widget.
 *
 * @since 1.1.0
 */
class Paypal_For_Woocommerce extends Integration {
	/**
	 * Check requirement to enable gateway in sellkit checkout widget.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	protected function requirements() {
		// Plugin is not installed.
		if ( ! class_exists( 'Angelleye_PayPal_Express_Checkout_Helper' ) ) {
			return false;
		}

		$this->parent = \Angelleye_PayPal_Express_Checkout_Helper::instance();

		// Gateway is not active.
		if ( 'no' === $this->parent->enabled ) {
			return false;
		}

		return true;
	}

	/**
	 * Content of express checkout methods.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function content() {

		add_filter( 'angelleye_ec_checkout_page_buy_now_nutton', function( $button_output ) {
			/* translators: %1$s: URL to paypal %2$s: paypal image src %3$s: image alt text */
			$button = sprintf(
				'<div id="paypal_ec_button">
				<a class="paypal_checkout_button" href="%1$s">
				<img src="%2$s" class="ec_checkout_page_button_type_paypalimage"  border="0" alt="%3$s" />
				</a>
				</div>',
				esc_url( add_query_arg( 'pp_action', 'set_express_checkout', untrailingslashit( WC()->api_request_url( 'WC_Gateway_PayPal_Express_AngellEYE' ) ) ) ),
				\WC_Gateway_PayPal_Express_AngellEYE::angelleye_get_paypalimage(),
				esc_html__( 'Pay with PayPal', 'paypal-for-woocommerce' )
			);

			return $button;
		} );

		ob_start();
			$button = '';
			echo apply_filters( 'angelleye_ec_checkout_page_buy_now_nutton', $button );
		echo ob_get_clean();
	}

	/**
	 * Hooks to integrate current gateway with sellkit checkout widget.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function hooks() {
		remove_action( 'woocommerce_before_checkout_form', [ $this->parent, 'checkout_message' ], 5 );
		remove_action( 'woocommerce_before_checkout_billing_form', [ $this->parent, 'ec_formatted_billing_address' ], 9 );
		remove_action( 'woocommerce_before_checkout_shipping_form', [ $this->parent, 'angelleye_shipping_sec_title' ], 5 );
	}
}
