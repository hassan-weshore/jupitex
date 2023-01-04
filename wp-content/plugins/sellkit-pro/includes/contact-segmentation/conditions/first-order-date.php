<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class First Order Date.
 *
 * @package Sellkit_Pro\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class First_Order_Date extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'first-order-date';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'First Order Date', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return 'date';
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
