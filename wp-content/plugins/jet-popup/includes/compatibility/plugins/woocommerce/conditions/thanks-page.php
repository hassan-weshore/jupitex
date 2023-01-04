<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Thanks_Page extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-thanks-page';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Thank You Page', 'jet-popup' );
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
		return 58;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_products_checkout';
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg ) {
		global $wp;

		return is_checkout() && ! empty( $wp->query_vars['order-received'] );
	}

}
