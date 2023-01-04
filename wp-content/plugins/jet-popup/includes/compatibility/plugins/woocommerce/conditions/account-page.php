<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Account_Page extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-account-page';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Account Page', 'jet-popup' );
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
		return 59;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_account_page';
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $args ) {
		return is_account_page();
	}

}
