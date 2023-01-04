<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class whitin date range.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.2.3
 */
class Whitin_Date_Range extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.2.3
	 */
	public function get_name() {
		return 'whitin-date-range';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.2.3
	 */
	public function get_title() {
		return esc_html__( 'Whitin Date Range', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.2.3
	 */
	public function get_type() {
		return 'date';
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
		$start_date = $condition_value['start_date'];
		$end_date   = $condition_value['end_date'];

		$current_date = strtotime( date( 'Y-m-d 0:0:0' ) );

		if ( 'is' === $operator && $current_date > $start_date && $current_date < $end_date ) {
			return true;
		}

		if ( 'is-not' === $operator && ! ( $current_date > $start_date && $current_date < $end_date ) ) {
			return true;
		}

		return false;
	}
}
