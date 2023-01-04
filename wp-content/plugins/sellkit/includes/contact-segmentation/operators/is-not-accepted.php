<?php

namespace Sellkit\Contact_Segmentation\Operators;

use Sellkit\Contact_Segmentation\Operator_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit_Is_Not_Operator
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.5.0
 */
class Is_Not_Accepted extends Operator_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.5.0
	 */
	public function get_name() {
		return 'is-not-accepted';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.5.0
	 */
	public function get_title() {
		return esc_html__( 'is not accepted', 'sellkit' );
	}

	/**
	 * Conditions.
	 *
	 * @since 1.5.0
	 */
	public function get_conditions() {
		return [
			'upsell',
			'downsell',
		];
	}

	/**
	 * Condition title.
	 *
	 * @since 1.5.0
	 * @param mixed $value            mixed The value of current value.
	 * @param mixed $condition_value  The value of condition input.
	 */
	public function is_valid( $value, $condition_value ) {
		if ( ! in_array( $condition_value['value'], $value, true ) ) {
			return true;
		}

		return false;
	}
}
