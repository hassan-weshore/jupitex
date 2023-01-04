<?php

namespace Sellkit_Pro\Admin\Notices;

defined( 'ABSPATH' ) || die();

/**
 * Geolocation service class.
 *
 * @since 1.2.3
 */
class Geolocation_Service extends Notice_Base {

	/**
	 * Notice key.
	 *
	 * @since 1.2.3
	 * @var string
	 */
	public $key = 'geolocation-service';

	/**
	 * Geolocation_Service constructor.
	 *
	 * @since 1.2.3
	 */
	public function __construct() {
		parent::__construct();

		$this->title   = esc_html__( 'SellKit Geolocation Service', 'sellkit-pro' );
		$this->content = esc_html__( 'In order to use Geolocation service you will need to create an API key from your Sellkit dashboard. This feature is free for Sellkit Pro users.', 'sellkit-pro' );
		$this->buttons = [
			admin_url( '/admin.php?page=sellkit-settings' ) => esc_html__( 'Go to SellKit Settings', 'sellkit-pro' ),
		];
	}

	/**
	 * Check if notice is valid or not.
	 *
	 * @since 1.2.3
	 * @return bool
	 */
	public function is_valid() {
		if ( in_array( $this->key, $this->dismissed_notices, true ) || ! function_exists( 'WC' ) ) {
			return false;
		}

		if ( empty( sellkit_get_option( 'geolocation_api_key' ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Set the priority of notice.
	 *
	 * @since 1.2.3
	 * @return int
	 */
	public function priority() {
		return 10;
	}
}
