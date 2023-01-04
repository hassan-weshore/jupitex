<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_Pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Time Deadline.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Time_Deadline extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'time-deadline';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return esc_html__( 'Time Deadline', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return 'time-deadline-field';
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
	 * Checks if the values are valid or not.
	 *
	 * @since 1.1.0
	 * @param mixed  $condition_value  The value of condition input.
	 * @param string $operator  Operator name.
	 */
	public function is_valid( $condition_value, $operator ) {
		$deadline_time = $condition_value['time_deadline'];
		$deadline_days = $condition_value['day_deadline'];
		$current_time  = strtotime( current_time( 'H:i' ) );
		$deadline_time = strtotime( $deadline_time );

		if ( 'is' === $operator && ! in_array( strtolower( date( 'l' ) ), $deadline_days, true ) ) {
			return false;
		}

		if ( 'is-not' === $operator && in_array( date( 'l' ), $deadline_days, true ) ) {
			return false;
		}

		if ( 'is' === $operator && $current_time > $deadline_time ) {
			return false;
		}

		if ( 'is-not' === $operator && $current_time < $deadline_time ) {
			return false;
		}

		return true;
	}
}
