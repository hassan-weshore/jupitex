<?php

namespace Sellkit\Contact_Segmentation\Operators;

use Sellkit\Contact_Segmentation\Operator_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class On.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class On extends Operator_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'on';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'on...', 'sellkit' );
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
		$start_date_time = strtotime( date( 'Y-m-d 00:00:00', $condition_value ) );
		$end_date_time   = strtotime( date( 'Y-m-d 24:00:00', $condition_value ) );

		if ( $value > $start_date_time && $value < $end_date_time ) {
			return true;
		}

		return false;
	}
}
