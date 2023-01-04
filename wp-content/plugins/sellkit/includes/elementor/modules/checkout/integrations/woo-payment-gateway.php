<?php

namespace Sellkit\Elementor\Modules\Checkout\Integrations;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Integrations\Integration;

/**
 * Integration class to integrate Plugins Braintree For WooCommerce gateways with sellkit checkout widget.
 *
 * @since 1.1.0
 */
class Woo_Payment_Gateway extends Integration {
	/**
	 * Check requirement to enable gateway in sellkit checkout widget.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	protected function requirements() {
		return defined( 'WC_BRAINTREE_PLUGIN_NAME' );
	}

	/**
	 * Content of express checkout methods.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function content() {
		$gateways = [];

		foreach ( WC()->payment_gateways()->get_available_payment_gateways() as $id => $gateway ) {
			if ( $gateway->supports( 'wc_braintree_banner_checkout' ) && $gateway->banner_checkout_enabled() ) {
				$gateways[ $id ] = $gateway;
			}
		}

		if ( count( $gateways ) > 0 ) {
			foreach ( $gateways as $gateway ) :?>
				<div class="wc-braintree-banner-gateway wc_braintree_banner_gateway_<?php echo esc_attr( $gateway->id ); ?>">
					<?php $gateway->banner_fields(); ?>
				</div>
				<?php
			endforeach;
		}
	}

	/**
	 * Hooks to integrate current gateway with sellkit checkout widget.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function hooks() {
		remove_action( 'woocommerce_checkout_before_customer_details', 'wc_braintree_banner_checkout_template' );
	}
}
