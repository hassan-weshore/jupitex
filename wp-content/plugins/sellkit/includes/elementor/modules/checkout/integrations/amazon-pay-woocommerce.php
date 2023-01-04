<?php

namespace Sellkit\Elementor\Modules\Checkout\Integrations;

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Integrations\Integration;

/**
 * Integration class to integrate woocommerce amazon pay with sellkit checkout widget.
 *
 * @since 1.1.0
 */
class Amazon_Pay_Woocommerce extends Integration {
	/**
	 * Check requirement to enable gateway in sellkit checkout widget.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	protected function requirements() {
		// Plugin not installed.
		if ( ! class_exists( 'WC_Amazon_Payments_Advanced' ) ) {
			return false;
		}

		$this->instance = wc_apa()->get_gateway();

		// Gateway is not active.
		if ( ! $this->instance->is_available() ) {
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
		echo '<div class="sellkit-amazon-pay-woocommerce">';
		echo $this->instance->checkout_button( false );
		echo '</div>';
	}

	/**
	 * Hooks to integrate current gateway with sellkit checkout widget.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function hooks() {
		remove_action( 'woocommerce_before_checkout_form', [ $this->instance, 'checkout_message' ], 5 );
	}
}
