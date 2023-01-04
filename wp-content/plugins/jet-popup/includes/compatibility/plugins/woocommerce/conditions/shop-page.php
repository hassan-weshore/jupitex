<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Shop_Page extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-shop-page';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Shop Page', 'jet-popup' );
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
		return 8;
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
		return is_shop();
	}

}
