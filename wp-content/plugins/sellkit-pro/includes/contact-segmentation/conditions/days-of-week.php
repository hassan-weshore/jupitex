<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Days of the week.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.2.3
 */
class Days_Of_Week extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.2.3
	 */
	public function get_name() {
		return 'days-of-week';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.2.3
	 */
	public function get_title() {
		return __( 'Day of the Week', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.2.3
	 */
	public function get_type() {
		return self::SELLKIT_MULTISELECT_CONDITION_VALUE;
	}

	/**
	 * Get Countries.
	 *
	 * @since 1.2.3
	 * @return array
	 */
	public function get_options() {
		$days = [
			'saturday' => esc_html__( 'Saturday', 'sellkit-pro' ),
			'sunday' => esc_html__( 'Sunday', 'sellkit-pro' ),
			'monday' => esc_html__( 'Monday', 'sellkit-pro' ),
			'tuesday' => esc_html__( 'Tuesday', 'sellkit-pro' ),
			'wednesday' => esc_html__( 'Wednesday', 'sellkit-pro' ),
			'thursday' => esc_html__( 'Thursday', 'sellkit-pro' ),
			'friday' => esc_html__( 'Friday', 'sellkit-pro' ),
		];

		return $days;
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
		if ( 'is' === $operator && in_array( strtolower( date( 'l' ) ), $condition_value, true ) ) {
			return true;
		}

		if ( 'is-not' === $operator && ! in_array( strtolower( date( 'l' ) ), $condition_value, true ) ) {
			return true;
		}

		return false;
	}
}
