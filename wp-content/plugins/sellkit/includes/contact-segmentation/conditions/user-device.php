<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class User_Device_Condition
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class User_Device extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'user-device';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'User Device', 'sellkit' );
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
	 * Get the options
	 *
	 * @since 1.1.0
	 * @return string[]
	 */
	public function get_options() {
		return [
			'desktop' => __( 'Desktop', 'sellkit' ),
			'tablet'  => __( 'Tablet', 'sellkit' ),
			'mobile'  => __( 'Mobile', 'sellkit' ),
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
