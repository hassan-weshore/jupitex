<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_Pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Viewed Product.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.0.0
 */
class Viewed_Product extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.0.0
	 */
	public function get_name() {
		return 'viewed-product';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Viewed Products', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.0.0
	 */
	public function get_type() {
		return self::SELLKIT_MULTISELECT_CONDITION_VALUE;
	}

	/**
	 * Get the options
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_options() {
		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		return sellkit_get_products( sanitize_text_field( $input_value ) );
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.0.0
	 */
	public function is_pro() {
		return true;
	}

	/**
	 * All the conditions are not searchable by default.
	 *
	 * @return false
	 * @since 1.0.0
	 */
	public function is_searchable() {
		return true;
	}
}
