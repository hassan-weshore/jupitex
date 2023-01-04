<?php

namespace Sellkit\Contact_Segmentation\Operators;

use Sellkit\Contact_Segmentation\Operator_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Dates Between.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Dates_Between extends Operator_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'dates-between';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'dates between', 'sellkit' );
	}

	/**
	 * Conditions.
	 *
	 * @since 1.1.0
	 */
	public function get_conditions() {
		return [
			'first-order-date',
			'last-order-date',
			'signup-date',
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
		$start_date = $condition_value['start_date'];
		$end_date   = $condition_value['end_date'];

		if ( $value > $start_date && $value < $end_date ) {
			return true;
		}

		return false;
	}
}
