<?php

namespace Sellkit\Contact_Segmentation\Operators;

use Sellkit\Contact_Segmentation\Operator_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Greater_Than.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Greater_Than extends Operator_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'greater-than';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'greater than', 'sellkit' );
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_conditions() {
		return [
			'cart-items-quantity',
			'total-order-count',
			'total-spent',
			'cart-subtotal',
		];
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 * @param mixed $value            The value of current value.
	 * @param mixed $condition_value  The value of condition input.
	 */
	public function is_valid( $value, $condition_value ) {
		if ( $value > $condition_value ) {
			return true;
		}

		return false;
	}
}
