<?php

namespace Sellkit\Admin\Notices;

defined( 'ABSPATH' ) || die();

/**
 * Woocommerce settings page class.
 *
 * @since 1.1.0
 */
class Woocommerce_Settings extends Notice_Base {

	/**
	 * Notice key.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $key = 'woocommerce-page-notices';

	/**
	 * Woocommerce_Settings constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		parent::__construct();

		$this->content = esc_html__( 'Unlock more engagement and sales for your WooCommerce store with advanced checkout optimization, dynamic discounts and personalized promotions.', 'sellkit' );
		$this->buttons = [
			'https://getsellkit.com/pricing/?utm_source=plugin-promotion-woocommerce-main&utm_medium=wp-dashboard&utm_campaign=upgrade-to-pro' => esc_html__( 'Upgrade to SellKit Pro', 'sellkit' ),
		];
	}

	/**
	 * Check if notice is valid or not.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_valid() {
		if ( in_array( $this->key, $this->dismissed_notices, true ) || sellkit()->has_pro || defined( 'SELLKIT_BUNDLED' ) ) {
			return false;
		}

		$current_page = ! empty( $_GET['page'] ) ? htmlentities( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore

		if ( 'wc-settings' === $current_page ) {
			return true;
		}

		return false;
	}

	/**
	 * Set the priority of notice.
	 *
	 * @since 1.1.0
	 * @return int
	 */
	public function priority() {
		return 1;
	}
}
