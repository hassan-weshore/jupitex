<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Login Status.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.5.0
 */
class Login_Status extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.5.0
	 */
	public function get_name() {
		return 'login-status';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.5.0
	 */
	public function get_title() {
		return esc_html__( 'Login Status', 'sellkit' );
	}

	/**
	 * Gets value.
	 *
	 * @since 1.5.0
	 */
	public function get_value() {
		if ( is_user_logged_in() ) {
			return 'logged-in';
		}

		return 'logged-out';
	}

	/**
	 * Condition type.
	 *
	 * @since 1.5.0
	 */
	public function get_type() {
		return self::SELLKIT_DROP_DOWN_CONDITION_VALUE;
	}

	/**
	 * Get the options.
	 *
	 * @since 1.5.0
	 * @return string[]
	 */
	public function get_options() {
		return [
			'logged-in' => esc_html__( 'logged in', 'sellkit' ),
			'logged-out' => esc_html__( 'logged out', 'sellkit' ),
		];
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.5.0
	 */
	public function is_pro() {
		return false;
	}
}
