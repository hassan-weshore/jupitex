<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class url query string.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.2.3
 */
class Url_Query_String extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.2.3
	 */
	public function get_name() {
		return 'url-query-string';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.2.3
	 */
	public function get_title() {
		return esc_html__( 'URL Query String Key', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.2.3
	 */
	public function get_type() {
		return 'url-query-string-field';
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.2.3
	 */
	public function is_pro() {
		return true;
	}

	/**
	 * Checks if the values are valid or not.
	 *
	 * @since 1.2.3
	 * @param mixed  $condition_value  The value of condition input.
	 * @param string $operator  Operator name.
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function is_valid( $condition_value, $operator ) {
		$value = $condition_value['value'];

		if ( empty( $value ) || empty( $this->data['url_query_string'] ) ) {
			return false;
		}

		foreach ( $this->data['url_query_string'] as $query_var ) {
			if ( 'is' === $operator && $query_var === $value ) {
				return true;
			}

			if ( 'is-not' === $operator && $query_var !== $value ) {
				return true;
			}

			if ( 'contains' === $operator && false !== strpos( $query_var, $value ) ) {
				return true;
			}

			if ( 'end-with' === $operator && str_ends_with( $query_var, $value ) ) {
				return true;
			}

			if ( 'starts-with' === $operator && str_starts_with( $query_var, $value ) ) {
				return true;
			}
		}

		return false;
	}
}
