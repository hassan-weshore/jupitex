<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Cart Subtotal.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Cart_Subtotal extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'cart-subtotal';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'Cart Subtotal', 'sellkit' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return [
			'less-than' => self::SELLKIT_NUMBER_CONDITION_VALUE,
			'greater-than' => self::SELLKIT_NUMBER_CONDITION_VALUE,
			'threshold-range' => 'threshold-range-field',
		];
	}

	/**
	 * Gets value.
	 *
	 * @since 1.1.0
	 */
	public function get_value() {
		if ( ! sellkit()->has_valid_dependencies() ) {
			return '';
		}

		return wc()->cart->get_subtotal();
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.1.0
	 */
	public function is_pro() {
		return true;
	}
}
