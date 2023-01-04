<?php
/**
 * This class handles plugin updates for Sellkit pro.
 *
 * @link       Artbees.net
 * @since      1.0.0
 *
 * @package    Sellkit
 * @subpackage Sellkit/admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class sellkit updater.
 */
class Sellkit_Plugin_Updater {
	// phpcs:disable
	public $plugin_slug;
	public $plugin_name;
	public $version;
	private $remote_response_transient_key;
	private $plugin_information_page_cache;
	//phpcs:enable

	/**
	 * Class construct.
	 */
	public function __construct() {

		$license = new Sellkit_License();

		if ( ! $license->is_license_active() ) {
			return;
		}

		$this->plugin_slug                   = SELLKIT_PRO_SLUG;
		$this->version                       = SELLKIT_PRO_VERSION;
		$this->plugin_name                   = SELLKIT_PRO_BASENAME;
		$this->remote_response_transient_key = md5( sanitize_key( $this->plugin_slug ) . 'remote_response_transient' );
		$this->plugin_information_page_cache = 'sellkit_pro_api_' . substr( md5( serialize( $this->plugin_slug ) ), 0, 15 ); // phpcs:ignore

		add_filter( 'plugins_api', [ $this, 'plugins_api_filter' ], 20, 3 );
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ], 50 );
		add_action( 'delete_site_transient_update_plugins', [ $this, 'delete_transients' ] );
		remove_action( 'after_plugin_row_' . $this->plugin_name, 'wp_plugin_update_row' );
		add_action( 'after_plugin_row_' . $this->plugin_name, [ $this, 'update_notification' ], 10, 2 );

		$this->maybe_delete_transients();
	}


	/**
	 * Update notification.
	 *
	 * @param string $file file.
	 * @since 1.0.0
	 */
	public function update_notification( $file ) {
		if ( is_network_admin() ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( ! is_multisite() ) {
			return;
		}

		if ( $this->plugin_name !== $file ) {
			return;
		}

		remove_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );

		$update_cache = get_site_transient( 'update_plugins' );
		$update_cache = $this->check_transient_data( $update_cache );
		set_site_transient( 'update_plugins', $update_cache );

		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
	}

	/**
	 * Request.
	 *
	 * @since 1.0.0
	 */
	public function request() {

		$license = new Sellkit_License();

		$remote = wp_remote_get(
			$license->sellkit_site . 'wp-json/sellkit/v1/updates/latest',
			array(
				'timeout' => 10,
				'body'    => [
					'plugin_slug' => 'sellkit-pro',
				],
				'headers' => [
					'Accept'              => 'application/json',
					'-sellkit-client-id'  => $license->get( 'client_id' ),
					'-sellkit-auth-token' => $license->get( 'auth_token' ),
				],
			)
		);

		if (
			is_wp_error( $remote )
			|| 200 !== wp_remote_retrieve_response_code( $remote )
			|| empty( wp_remote_retrieve_body( $remote ) )
		) {
			return false;
		}

		$remote = json_decode( wp_remote_retrieve_body( $remote ) );

		return $remote[0];
	}

	/**
	 * Plugin filter.
	 *
	 * @param array  $_data data.
	 * @param string $_action action.
	 * @param array  $_args args.
	 * @since 1.0.0
	 */
	public function plugins_api_filter( $_data, $_action = '', $_args = null ) {

		if ( 'plugin_information' !== $_action ) {
			return $_data;
		}

		if ( ! isset( $_args->slug ) || ( $_args->slug !== $this->plugin_slug ) ) {
			return $_data;
		}

		$api_request_transient = get_transient( $this->plugin_information_page_cache );

		if ( empty( $api_request_transient ) ) {
			$remote = $this->request();
			if ( ! $remote ) {
				return $_data;
			}

			$api_request_transient = new stdClass();

			$api_request_transient->name           = $remote->name;
			$api_request_transient->slug           = $remote->slug;
			$api_request_transient->version        = $remote->version;
			$api_request_transient->tested         = $remote->tested;
			$api_request_transient->requires       = $remote->requires;
			$api_request_transient->author         = $remote->author;
			$api_request_transient->author_profile = $remote->author_profile;
			$api_request_transient->download_link  = $remote->download_url;
			$api_request_transient->trunk          = $remote->download_url;
			$api_request_transient->requires_php   = $remote->requires_php;
			$api_request_transient->last_updated   = $remote->last_updated;

			$api_request_transient->sections = [
				'description'  => $remote->sections->description,
				'installation' => $remote->sections->installation,
				'changelog'    => $remote->sections->changelog,
			];

			if ( ! empty( $remote->banners ) ) {
				$api_request_transient->banners = [
					'low'  => $remote->banners->low,
					'high' => $remote->banners->high,
				];
			}

			if ( ! empty( $remote->icons ) ) {
				$api_request_transient->icons = [
					'1x' => $remote->icons->icon_1x,
					'2x' => $remote->icons->icon_2x,
				];
			}

			set_transient( $this->plugin_information_page_cache, $api_request_transient, DAY_IN_SECONDS );
		}

		return $api_request_transient;

	}

	/**
	 * Check transient.
	 *
	 * @param object $transient transient.
	 * @since 1.0.0
	 */
	private function check_transient_data( $transient ) {

		if ( ! is_object( $transient ) ) {
			$transient = new \stdClass();
		}

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$remote = $this->request();

		if ( is_wp_error( $remote ) ) {
			return $transient;
		}

		if ( version_compare( get_bloginfo( 'version' ), $remote->requires, '<' ) ) {
			return $transient;
		}

		if ( version_compare( $this->version, $remote->version, '<' ) ) {

			$res              = new stdClass();
			$res->slug        = $this->plugin_slug;
			$res->plugin      = SELLKIT_PRO_BASENAME;
			$res->new_version = $remote->version;
			$res->tested      = $remote->tested;
			$res->package     = $remote->download_url;

			if ( ! empty( $remote->icons ) ) {
				$res->icons = [
					'1x' => $remote->icons->icon_1x,
					'2x' => $remote->icons->icon_2x,
				];
			}

			$transient->response[ $res->plugin ] = $res;
		}

		return $transient;
	}


	/**
	 * Check update.
	 *
	 * @param object $_transient_data data.
	 * @since 1.0.0
	 */
	public function check_update( $_transient_data ) {
		global $pagenow;

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new \stdClass();
		}

		if ( 'plugins.php' === $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		return $this->check_transient_data( $_transient_data );
	}

	/**
	 * Delete transient.
	 *
	 * @since 1.0.0
	 */
	public function delete_transients() {

		delete_transient( $this->remote_response_transient_key );
		delete_transient( $this->plugin_information_page_cache );

	}

	/**
	 * Delete transient.
	 *
	 * @since 1.0.0
	 */
	private function maybe_delete_transients() {
		global $pagenow;

		if ( 'update-core.php' === $pagenow && isset( $_GET['force-check'] ) ) { // phpcs:ignore
			$this->delete_transients();
		}
	}

}

new Sellkit_Plugin_Updater();
