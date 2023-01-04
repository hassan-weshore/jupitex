<?php

namespace Sellkit\Contact_Segmentation\Operators;

use Sellkit\Contact_Segmentation\Operator_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Threshold Range.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Threshold_Range extends Operator_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'threshold-range';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return esc_html__( 'threshold range', 'sellkit' );
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_conditions() {
		return [
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
		$threshold_range        = $condition_value['threshold_range'];
		$target_subtotal_amount = $condition_value['target_subtotal_amount'];

		if ( $value > $threshold_range && $value < $target_subtotal_amount ) {
			return true;
		}

		return false;
	}
}
