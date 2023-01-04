<?php

namespace Sellkit\Contact_Segmentation;

defined( 'ABSPATH' ) || die();

/**
 * Class Operators.
 *
 * @package Sellkit\Contact_Segmentation\Base.
 * @since 1.1.0
 */
abstract class Operator_Base {

	/**
	 * Get title.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	abstract public function get_title();

	/**
	 * Get name.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get conditions.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	abstract public function get_conditions();

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 * @param mixed $value      The value of current value.
	 * @param mixed $condition_value The value of condition input.
	 */
	abstract public function is_valid( $value, $condition_value );
}
