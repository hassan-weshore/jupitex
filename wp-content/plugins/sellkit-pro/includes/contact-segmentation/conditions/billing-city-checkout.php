<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_Pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Billing City.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.2.6
 */
class Billing_City_Checkout extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.2.6
	 */
	public function get_name() {
		return 'billing-city-checkout';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.2.6
	 */
	public function get_title() {
		return esc_html__( 'Billing City on Checkout', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.2.6
	 */
	public function get_type() {
		return self::SELLKIT_TEXT_CONDITION_VALUE;
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.2.6
	 */
	public function is_pro() {
		return true;
	}

	/**
	 * Get value.
	 *
	 * @since 1.2.6
	 */
	public function get_value() {
		$action = sellkit_htmlspecialchars( INPUT_GET, 'wc-ajax' );

		if ( wp_doing_ajax() && 'checkout' === $action ) {
			return wc()->customer->get_billing_city( 'edit' );
		}

		if ( empty( $_POST['city'] ) ) { //phpcs:ignore
			return '';
		}

		$shipping_city = sanitize_text_field( $_POST['city'] ); //phpcs:ignore

		return $shipping_city;
	}
}
