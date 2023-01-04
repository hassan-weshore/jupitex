<?php

namespace Sellkit\Elementor\Modules\Checkout\Integrations;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Integrations\Integration;

/**
 * Integration class to integrate stripe woocommerce official with sellkit checkout widget.
 *
 * @since 1.1.0
 */
class Stripe_Woocommerce_Official extends Integration {
	/**
	 * Original plugin class holder.
	 *
	 * @since 1.1.0
	 * @var object
	 */
	private $parent;

	/**
	 * Check requirement to enable gateway in sellkit checkout widget.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	protected function requirements() {
		// Plugin not installed.
		if ( ! defined( 'WC_STRIPE_PLUGIN_PATH' ) ) {
			return false;
		}

		if ( ! class_exists( 'WC_Stripe_Payment_Request' ) ) {
			return false;
		}

		$this->parent = \WC_Stripe_Payment_Request::instance();

		return true;
	}

	/**
	 * Content of express checkout methods.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function content() {
		?>
			<div class="sellkit-stripe-woocommerce-official-integration" >
				<?php $this->parent->display_payment_request_button_html(); ?>
			</div>
		<?php
	}

	/**
	 * Hooks to integrate current gateway with sellkit checkout widget.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function hooks() {
		remove_action( 'woocommerce_checkout_before_customer_details', [ 'WC_Stripe_Payment_Request', 'display_payment_request_button_html' ], 1 );
		remove_action( 'woocommerce_checkout_before_customer_details', [ 'WC_Stripe_Payment_Request', 'display_payment_request_button_separator_html' ], 2 );
	}
}
