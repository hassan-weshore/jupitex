<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class whitin time period.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.2.3
 */
class Whitin_Time_Period extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.2.3
	 */
	public function get_name() {
		return 'whitin-time-period';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.2.3
	 */
	public function get_title() {
		return esc_html__( 'Whitin Time Period', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.2.3
	 */
	public function get_type() {
		return 'whitin-time-period-field';
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.2.3
	 */
	public function is_pro() {
		return true;
	}

	/**
	 * Checks if the values are valid or not.
	 *
	 * @since 1.2.3
	 * @param mixed  $condition_value  The value of condition input.
	 * @param string $operator  Operator name.
	 */
	public function is_valid( $condition_value, $operator ) {
		$start_time = strtotime( $condition_value['start_time'] );
		$due_time   = strtotime( $condition_value['due_time'] );

		if ( $start_time > $due_time ) {
			return false;
		}

		$current_time = strtotime( current_time( 'H:i' ) );

		if ( 'is' === $operator && $current_time > $start_time && $current_time < $due_time ) {
			return true;
		}

		if ( 'is-not' === $operator && ! ( $current_time > $start_time && $current_time < $due_time ) ) {
			return true;
		}

		return false;
	}
}
