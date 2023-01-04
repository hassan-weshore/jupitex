<?php

namespace Sellkit\Contact_Segmentation\Operators;

use Sellkit\Contact_Segmentation\Operator_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit_Is_Not_Operator
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Is_Not extends Operator_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'is-not';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'is not', 'sellkit' );
	}

	/**
	 * Conditions.
	 *
	 * @since 1.1.0
	 */
	public function get_conditions() {
		return [
			'browser-language',
			'user-type',
			'utm-source',
			'utm-medium',
			'utm-campaign',
			'utm-content',
			'utm-term',
			'referral-source-url',
			'time-deadline',
			'billing-city',
			'billing-city-checkout',
			'shipping-city',
			'shipping-city-checkout',
			'visitor-city',
			'days-of-week',
			'whitin-date-range',
			'whitin-time-period',
			'url-query-string',
			'login-status',
		];
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 * @param mixed $value            mixed The value of current value.
	 * @param mixed $condition_value  The value of condition input.
	 */
	public function is_valid( $value, $condition_value ) {
		if ( $value !== $condition_value ) {
			return true;
		}

		return false;
	}
}
