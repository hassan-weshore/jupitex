<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Search_Results extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-search-results';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Search Results', 'jet-popup' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'woocommerce';
	}

	/**
	 * @return string
	 */
	public function get_sub_group() {
		return 'woocommerce-page';
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return 9;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_products_archive';
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $args ) {
		return is_search() && 'product' === get_query_var( 'post_type' );
	}

}
