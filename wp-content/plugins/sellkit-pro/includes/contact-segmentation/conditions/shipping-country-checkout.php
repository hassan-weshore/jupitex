<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_Pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class shipping country value.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.2.6
 */
class Shipping_Country_Checkout extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.2.6
	 */
	public function get_name() {
		return 'shipping-country-checkout';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.2.6
	 */
	public function get_title() {
		return esc_html__( 'Shipping Country on Checkout', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.2.6
	 */
	public function get_type() {
		return self::SELLKIT_MULTISELECT_CONDITION_VALUE;
	}

	/**
	 * Get Countries.
	 *
	 * @since 1.2.6
	 * @return array
	 */
	public function get_options() {
		if ( ! sellkit_pro()->has_valid_dependencies() ) {
			return [];
		}

		$input_value        = ! empty( $_GET['input_value'] ) ? sanitize_text_field( $_GET['input_value'] ) : ''; // phpcs:ignore
		$countries          = new \WC_Countries();
		$countries          = $countries->get_countries();
		$filtered_countries = [];

		if ( empty( $input_value ) ) {
			return $countries;
		}

		foreach ( $countries as $key => $country ) {

			if ( strpos( strtolower( $country ), strtolower( $input_value ) ) !== 0 ) {
				continue;
			}

			$filtered_countries[ $key ] = html_entity_decode( $country );
		}

		return $filtered_countries;
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
		if ( empty( $_POST['s_country'] ) ) { //phpcs:ignore
			return wc()->customer->get_shipping_country( 'edit' );
		}

		$shipping_country = sanitize_text_field( $_POST['s_country'] ); //phpcs:ignore

		return $shipping_country;
	}
}
