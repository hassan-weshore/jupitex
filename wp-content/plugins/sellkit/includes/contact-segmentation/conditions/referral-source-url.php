<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Referral Source Url.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Referral_Source_Url extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'referral-source-url';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'Referral Source (URL)', 'sellkit' );
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
	 * Check if the condition is active or not.
	 *
	 * @return bool
	 */
	public function is_active() {
		return false;
	}
}
