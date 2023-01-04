<?php
/**
 * Temporary class to handle some features.
 *
 * @link       Artbees.net
 * @since      1.0.0
 *
 * @package    Sellkit
 * @subpackage Sellkit/admin
 */
class Sellkit_Extras {

	/**
	 * License.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $license;

	/**
	 * Class construct.
	 */
	public function __construct() {

		$this->license = new Sellkit_License();

		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		add_action( 'admin_notices', [ $this, 'license_problem_notices' ] );
		add_action( 'wp_ajax_sellkit_dismiss_license_notice', [ $this, 'dismiss_license_notice' ] );

		register_activation_hook( SELLKIT_PRO_BASENAME, [ $this, 'licensing_schedules' ] );
		register_deactivation_hook( SELLKIT_PRO_BASENAME, [ $this, 'clear_licensing_schedules' ] );

		add_action( 'sellkit_license_checks', [ $this, 'validate_license' ] );
		add_action( 'admin_menu', [ $this, 'add_options_page' ], 999 );
		add_action( 'admin_init', [ $this, 'license_action_callback' ] );
		add_action( 'admin_init', [ $this, 'maybe_license_check' ] );

	}


	/**
	 * Adds license page.
	 *
	 * @return void
	 */
	public function add_options_page() {

		add_submenu_page(
			'sellkit-dashboard',
			esc_html__( 'Sellkit License', 'sellkit-pro' ),
			esc_html__( 'License', 'sellkit-pro' ),
			'manage_options',
			$this->license->sellkit_license_page,
			[ $this, 'render_option_page' ],
			9999
		);

	}


	/**
	 * Selectively renders license page based.
	 *
	 * @return void
	 */
	public function render_option_page() {
		if ( $this->license->get( 'site_key' ) ) {
			sellkit_pro()->load_files( [
				'admin/license/partials/license-connected',
			] );
		} else {
			sellkit_pro()->load_files( [
				'admin/license/partials/license-not-connected',
			] );
		}

	}

	/**
	 * Registers license schedules for various periodic checks.
	 *
	 * @return void
	 */
	public function licensing_schedules() {
		if ( ! wp_next_scheduled( 'sellkit_license_checks' ) ) {
			wp_schedule_event( time(), 'daily', 'sellkit_license_checks' );
		}
	}

	/**
	 * Clears future schedules after plugin deactivations.
	 *
	 * @return void
	 */
	public function clear_licensing_schedules() {
		wp_clear_scheduled_hook( 'sellkit_license_checks' );
	}

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route( 'sellkit-license/v1', '/deactivate', [
			[
				'methods'  => 'POST',
				'callback' => [ $this->license, 'remote_deactivate' ],
				'permission_callback' => '__return_true',
			],
		] );

	}

	/**
	 * Check if license is valid when user visits license page.
	 *
	 * @return void
	 */
	public function maybe_license_check() {
		$page = sellkit_htmlspecialchars( INPUT_GET, 'page' );

		if ( ! empty( $page ) && $this->license->sellkit_license_page === $page ) {
			$this->validate_license();
		}
	}


	/**
	 * Process remote license callback.
	 *
	 * @since 1.0.0
	 */
	public function license_action_callback() {

		$page   = sellkit_htmlspecialchars( INPUT_GET, 'page' );
		$action = sellkit_htmlspecialchars( INPUT_GET, 'action' );
		$nonce  = sellkit_htmlspecialchars( INPUT_GET, 'nonce' );

		if ( empty( $page ) || empty( $action ) || $this->license->sellkit_license_page !== $page ) {
			return;
		}

		if ( ! wp_verify_nonce( $nonce, $this->license->sellkit_license_page ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		call_user_func( [ $this->license, $action ] );
	}


	/**
	 * Validates license with remote API.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function validate_license() {

		if ( false === $this->license->get( 'status' ) ) {
			return;
		}

		$result = wp_remote_get( $this->license->sellkit_site . '/wp-json/sellkit/v1/license/validate', [
			'body' => [
				'site_key'     => $this->license->get( 'site_key' ),
				'secret_token' => $this->license->get( 'secret_token' ),
				'user_email'   => $this->license->get( 'user_email' ),
			],
			'headers' => [
				'-sellkit-client-id' => $this->license->get( 'client_id' ),
				'-sellkit-auth-token' => $this->license->get( 'auth_token' ),
			],
		]);

		if ( ! is_wp_error( $result ) ) {

			$body = json_decode( wp_remote_retrieve_body( $result ) );

			if ( isset( $body->license_status ) && ! empty( $body->license_status ) ) {
				$license_option           = get_option( $this->license->sellkit_license_option );
				$license_option['status'] = $body->license_status;
				update_option( $this->license->sellkit_license_option, $license_option );
			}
		}

	}


	/**
	 * Show connect notice if not connected.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function license_problem_notices() {

		if ( 'active' === $this->license->get( 'status' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( false === $this->license->get( 'status' ) ) {
			if ( false !== get_option( 'sellkit_dismiss_license_notice' ) ) {
				return;
			}
			$message = sprintf(
				'<span style="display:block;margin:0;">'
				. '<h3>'
				. __( 'Welcome to Sellkit Pro', 'sellkit-pro' )
				. '</h3>'
				. __( 'Please activate your license to get plugin updates, premium support, and access to premium funnel templates.', 'sellkit-pro' )
				. '</span>'
			);

			$message .= sprintf(
				'<span style="display:block;margin-top:1em; clear: both;">' .
				'<a class="button-primary" href="%1$s">%2$s</a></span>',
				$this->license->get_url( 'activate' ),
				__( 'Connect & Activate', 'sellkit-pro' )
			);

			$is_dismissable_class = 'is-dismissible';
		}

		if ( 'expired' === $this->license->get( 'status' ) ) {

			if ( false !== get_option( 'sellkit_dismiss_license_notice' ) ) {
				return;
			}
			$message = sprintf(
				'<span style="display:block;margin:0;">'
				. '<h3>'
				. __( 'Your License has expired!', 'sellkit-pro' )
				. '</h3>'
				. __( 'Please activate your license to get plugin updates, premium support, and access to premium funnel templates.', 'sellkit-pro' )
				. '</span>'
			);

			$message .= sprintf(
				'<span style="display:block;margin-top:1em; clear: both;">' .
				'<a class="button-primary" href="https://my.getsellkit.com/subscriptions">%s</a></span>',
				__( 'Renew License', 'sellkit-pro' )
			);

			$is_dismissable_class = '';

		}

		if ( 'invalid' === $this->license->get( 'status' ) ) {

			if ( false !== get_option( 'sellkit_dismiss_license_notice' ) ) {
				return;
			}
			$message = sprintf(
				'<span style="display:block;margin:0;">'
				. '<h3>'
				. __( 'Your License seems to be invalid!!', 'sellkit-pro' )
				. '</h3>'
				. __( 'Your license details(Site Key, Secret Token, Account Email) does not match our records. Please deactivate the license and then reactivate it again to resolve this problem.', 'sellkit-pro' )
				. '</span>'
			);

			$message .= sprintf(
				'<span style="display:block;margin-top:1em; clear: both;">' .
				'<a class="button-primary" href="%1$s">%2$s</a></span>',
				$this->license->get_url( 'activate' ),
				__( 'Deactivate & Activate', 'sellkit-pro' )
			);

			$is_dismissable_class = '';

		}

		if ( ! isset( $message ) ) {
			return;
		}

		printf(
			'<div data-nonce="%s" class="sellkit-notice notice sellkit-license-notice %s">%s<div class="sellkit-notice-content">%s</div></div>',
			wp_create_nonce( 'sellkit_dismiss_license_notice' ),
			$is_dismissable_class,
			'<div class="sellkit-notice-aside"><span class="sellkit-notice-aside-icon"><span></div>',
			$message
		);
	}

	/**
	 * Set option if notice is dismissed.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function dismiss_license_notice() {
		check_ajax_referer( 'sellkit_dismiss_license_notice' );
		update_option( 'sellkit_dismiss_license_notice', 1 );
	}

}

new Sellkit_Extras();
