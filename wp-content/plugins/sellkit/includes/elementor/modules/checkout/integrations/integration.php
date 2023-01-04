<?php

namespace Sellkit\Elementor\Modules\Checkout\Integrations;

defined( 'ABSPATH' ) || die();

/**
 * Integration class to integrate gateways with sellkit checkout widget.
 *
 * @since 1.1.0
 */
abstract class Integration {
	/**
	 * Active available wooCommerce gateways.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $gateways;

	/**
	 * Run this class content if requirements are met.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function run() {
		$this->gateways = WC()->payment_gateways->get_available_payment_gateways();

		if ( false === $this->requirements() ) {
			return;
		}

		$this->hooks();
		add_action( 'sellkit-checkout-widget-express-methods', [ $this, 'content' ] );
	}

	/**
	 * Check requirement to enable gateway in sellkit checkout widget.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	abstract protected function requirements();

	/**
	 * Content of express checkout methods.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	abstract protected function content();

	/**
	 * Hooks to integrate current gateway with sellkit checkout widget.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	abstract protected function hooks();
}
