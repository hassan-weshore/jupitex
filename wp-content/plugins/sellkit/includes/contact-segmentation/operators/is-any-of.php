<?php

namespace Sellkit\Contact_Segmentation\Operators;

use Sellkit\Contact_Segmentation\Operator_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit_Is_Any_Of_Operator
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Is_Any_Of extends Operator_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'is-any-of';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'is any of', 'sellkit' );
	}

	/**
	 * Conditions.
	 *
	 * @since 1.1.0
	 */
	public function get_conditions() {
		return [
			'user-device',
			'user-role',
			'purchased-product',
			'viewed-category',
			'viewed-product',
			'purchased-category',
			'customer-value',
			'cart-tag',
			'shipping-country',
			'shipping-country-checkout',
			'billing-country',
			'billing-country-checkout',
			'cart-category',
			'cart-item',
			'referral-source-internal-post',
			'referral-source-product-category',
			'referral-source-post-category',
			'visitor-country',
			'visitor-timezone',
			'visitor-currency',
			'visitor-language',
			'customer-value',
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
		if ( ! is_array( $value ) ) {
			$value = [ $value ];
		}

		if ( ! empty( array_intersect( $value, $condition_value ) ) ) {
			return true;
		}

		return false;
	}
}
