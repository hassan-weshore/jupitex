<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Cart_Items_Quantity_Condition
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Cart_Items_Quantity extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'cart-items-quantity';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'Cart Items Quantity', 'sellkit' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return self::SELLKIT_NUMBER_CONDITION_VALUE;
	}

	/**
	 * Gets value.
	 *
	 * @since 1.1.0
	 */
	public function get_value() {
		if ( ! sellkit()->has_valid_dependencies() ) {
			return 0;
		}

		return is_array( wc()->cart->get_cart_item_quantities() ) ? count( wc()->cart->get_cart_item_quantities() ) : 0;
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
