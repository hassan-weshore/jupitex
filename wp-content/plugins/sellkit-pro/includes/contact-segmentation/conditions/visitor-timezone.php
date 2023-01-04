<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_Pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Visitor's timezone.
 *
 * @package Sellkit_Pro\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Visitor_Timezone extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'visitor-timezone';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'Visitor\'s Timezone', 'sellkit-pro' );
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
	 * It is pro feature or not.
	 *
	 * @since 1.1.0
	 */
	public function is_pro() {
		return true;
	}

	/**
	 * It searchable.
	 *
	 * @since 1.1.0
	 */
	public function is_searchable() {
		return true;
	}

	/**
	 * Gets options.
	 *
	 * @since 1.1.0
	 * @return string[]
	 */
	public function get_options() {
		$timezones = timezone_identifiers_list();

		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		return sellkit_filter_array( $timezones, $input_value );
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
