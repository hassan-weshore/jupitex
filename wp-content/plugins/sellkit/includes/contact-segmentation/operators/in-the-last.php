<?php

namespace Sellkit\Contact_Segmentation\Operators;

use Sellkit\Contact_Segmentation\Operator_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class In The Last.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class In_The_Last extends Operator_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'in-the-last';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'in the last', 'sellkit' );
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
		if ( empty( $condition_value['number'] ) || empty( $condition_value['type'] ) ) {
			return false;
		}

		$number = $condition_value['number'];
		$type   = $condition_value['type'];

		$time_string  = "{$number} {$type} ago";
		$minimum_time = strtotime( $time_string );

		if ( $value > $minimum_time ) {
			return true;
		}

		return false;
	}
}
