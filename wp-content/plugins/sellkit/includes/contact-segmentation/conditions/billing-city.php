<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;
use Sellkit\Contact_Segmentation\Operators;

defined( 'ABSPATH' ) || die();

/**
 * Class Billing City.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Billing_City extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'billing-city';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return esc_html__( 'Past Order Billing City', 'sellkit' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return self::SELLKIT_TEXT_CONDITION_VALUE;
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
	 * If it's valid or not.
	 *
	 * @since 1.1.0
	 * @param array  $condition_value Condition value.
	 * @param string $operator_name Operator name.
	 * @return bool
	 */
	public function is_valid( $condition_value, $operator_name ) {
		$operator          = Operators::$operators[ $operator_name ];
		$is_not_validation = [];

		foreach ( $this->data['billing_city'] as $city ) {
			$is_valid = $operator->is_valid( $city, $condition_value );

			if ( 'is-not' === $operator_name ) {
				$is_not_validation[] = $is_valid;
			}

			if ( $is_valid && 'is-not' !== $operator_name ) {
				return true;
			}
		}

		if (
			'is-not' === $operator_name &&
			1 === count( array_unique( $is_not_validation ) ) &&
			true === $is_not_validation[0]
		) {
			return true;
		}

		return false;
	}
}
