<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;
use Sellkit\Contact_Segmentation\Contact_Data;

defined( 'ABSPATH' ) || die();

/**
 * Class Cart_Tag.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Cart_Tag extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'cart-tag';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'Cart Tags', 'sellkit' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return self::SELLKIT_MULTISELECT_CONDITION_VALUE;
	}

	/**
	 * Gets value.
	 *
	 * @since 1.1.0
	 */
	public function get_value() {
		return Contact_Data::get_cart_terms( 'product_tag' );
	}

	/**
	 * Get the options
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_options() {
		if ( ! sellkit()->has_valid_dependencies() ) {
			return [];
		}

		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		return sellkit_get_terms( 'product_tag', $input_value );
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
	 * All the conditions are not searchable by default.
	 *
	 * @return false
	 * @since 1.1.0
	 */
	public function is_searchable() {
		return true;
	}
}
