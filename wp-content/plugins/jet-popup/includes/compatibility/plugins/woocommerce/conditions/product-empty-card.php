<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Product_Empty_Card extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-product-empty-card';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Product Empty Cart', 'jet-popup' );
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
		return 'jet_products_card';
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg ) {
		return is_cart() && 0 === WC()->cart->get_cart_contents_count();
	}

}
