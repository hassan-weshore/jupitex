<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Referral Source Post Category.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Referral_Source_Post_Category extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'referral-source-post-category';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'Referral Source (post Category)', 'sellkit' );
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
	 * @return array
	 */
	public function get_options() {
		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		return sellkit_get_terms( 'category', sanitize_text_field( $input_value ) );
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

	/**
	 * Check if the condition is active or not.
	 *
	 * @return bool
	 */
	public function is_active() {
		return false;
	}
}
