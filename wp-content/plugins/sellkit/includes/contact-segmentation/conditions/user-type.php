<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class User_Type_Condition
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class User_Type extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'user-type';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'User Type', 'sellkit' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return self::SELLKIT_DROP_DOWN_CONDITION_VALUE;
	}

	/**
	 * Get the options
	 *
	 * @since 1.1.0
	 * @return string[]
	 */
	public function get_options() {
		return [
			'first_time_visitor' => __( 'First-time visitor', 'sellkit' ),
			'returning_visitor' => __( 'Returning visitor', 'sellkit' ),
			'lead' => __( 'Lead', 'sellkit' ),
			'customer' => __( 'Customer', 'sellkit' ),
		];
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.1.0
	 */
	public function is_pro() {
		return false;
	}
}
