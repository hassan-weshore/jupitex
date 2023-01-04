<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Billing Country.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Billing_Country extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'billing-country';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return esc_html__( 'Past Order Billing Country', 'sellkit' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return self::SELLKIT_MULTISELECT_CONDITION_VALUE;
	}

	/**
	 * Get Countries.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_options() {
		if ( ! sellkit()->has_valid_dependencies() ) {
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
	 * @since 1.1.0
	 */
	public function is_pro() {
		return true;
	}

	/**
	 * It searchable.
	 *
	 * @since 1.1.0
	 */
	public function is_searchable() {
		return true;
	}

	/**
	 * It searchable.
	 *
	 * @since 1.1.0
	 */
	public function open_menu_on_click() {
		return true;
	}
}
