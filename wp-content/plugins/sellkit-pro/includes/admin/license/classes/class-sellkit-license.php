<?php
/**
 * Handing licese features
 *
 * @link       Artbees.net
 * @since      1.0.0
 *
 * @package    Sellkit
 * @subpackage Sellkit/admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sellkit License.
 */
class Sellkit_License {

	/**
	 * Sellkit Website URL
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $sellkit_site;

	/**
	 * Admin url.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $admin_url;

	/**
	 * Site url.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $site_url;

	/**
	 * Product ID that will be activated.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $product_id;

	/**
	 * Stores license data.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $sellkit_license_option;

	/**
	 * Sellkit license admin page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $sellkit_license_page;

	/**
	 * Class construct.
	 */
	public function __construct() {

		$this->sellkit_site           = 'https://my.getsellkit.com/';
		$this->product_id             = 'prod_KRQzQ84ZyrIzXy';
		$this->sellkit_license_option = 'sellkit_license';
		$this->sellkit_license_page   = 'sellkit-license';
		$this->admin_url              = admin_url( 'admin.php?page=' . $this->sellkit_license_page );
		$this->site_url               = home_url();

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
				'callback' => [ $this, 'remote_deactivate' ],
				'permission_callback' => '__return_true',
			],
		] );

	}



	/**
	 * Get option.
	 *
	 * @param string $name name.
	 * @since 1.2.0
	 */
	public function get( $name ) {
		$option = get_option( $this->sellkit_license_option );

		if ( empty( $option[ $name ] ) ) {
			return false;
		}

		return $option[ $name ];
	}



	/**
	 * Get Secure URL to remote access to register license.
	 *
	 * @since 1.0.0
	 */
	public function switch() {

		$result = wp_remote_post( $this->sellkit_site . '/wp-json/sellkit/v1/license/delete', [
			'body' => [
				'site_key'     => $this->get( 'site_key' ),
				'secret_token' => $this->get( 'secret_token' ),
			],
			'headers' => [
				'-sellkit-client-id' => $this->get( 'client_id' ),
				'-sellkit-auth-token' => $this->get( 'auth_token' ),
			],
		] );

		$body = json_decode( wp_remote_retrieve_body( $result ), true );

		if ( isset( $body['status'] ) ) {
			delete_option( $this->sellkit_license_option );
			delete_option( 'sellkit_dismiss_license_notice' );
		}

		$this->activate();
	}

	/**
	 * Get Secure URL to remote access to register license.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		$current_theme = wp_get_theme();
		$nonce         = wp_create_nonce( $this->sellkit_license_page );
		$session_token = $this->create_session_token();
		$partner_id    = $this->get_partner_id();

		$url_params = [
			'action'        => 'connect',
			'site_url'      => urlencode( $this->site_url ), //phpcs:ignore
			'redirect_url'  => urlencode( $this->admin_url ), //phpcs:ignore
			'product_id'    => $this->product_id,
			'theme'         => $current_theme->get( 'Name' ),
			'version'       => SELLKIT_PRO_VERSION, // current Sellkit Plugin Version.
			'session_token' => $session_token,
			'nonce'         => $nonce,
		];

		if ( $partner_id ) {
			$url_params['partner_id'] = $partner_id;
		}

		$url = add_query_arg( $url_params, $this->sellkit_site . '/authorize' );

		wp_redirect( $url ); //phpcs:ignore
	}

	/**
	 * Deactivate license from client and Sellkit server.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		$result = wp_remote_post( $this->sellkit_site . '/wp-json/sellkit/v1/license/delete', [
			'body' => [
				'site_key'     => $this->get( 'site_key' ),
				'secret_token' => $this->get( 'secret_token' ),
			],
			'headers' => [
				'-sellkit-client-id' => $this->get( 'client_id' ),
				'-sellkit-auth-token' => $this->get( 'auth_token' ),
			],
		] );

		$body = json_decode( wp_remote_retrieve_body( $result ), true );

		delete_option( $this->sellkit_license_option );
		delete_option( 'sellkit_dismiss_license_notice' );

		wp_redirect( $this->admin_url ); //phpcs:ignore
		exit;
	}

	/**
	 * Remote deactivate.
	 *
	 * @param object $request request.
	 * @since 1.0.0
	 */
	public function remote_deactivate( $request ) {

		$site_key     = $request->get_param( 'site_key' );
		$secret_token = $request->get_param( 'secret_token' );

		if ( empty( $site_key ) || empty( $secret_token ) ) {
			return new \WP_Error(
				'invalid_request',
				esc_html__( 'Invalid request.', 'sellkit-pro' ),
				[ 'status' => 404 ]
			);
		}

		if ( $this->get( 'site_key' ) === $site_key && $this->get( 'secret_token' ) === $secret_token ) {
			delete_option( $this->sellkit_license_option );
			delete_option( 'sellkit_dismiss_license_notice' );
		}

		return new \WP_REST_Response( true, 200 );

	}

	/**
	 * Get the secure URL that will route the app to the appropriate action.
	 *
	 * @param string $action action name.
	 * @since 1.0.0
	 */
	public function get_url( $action ) {

		$url = add_query_arg([
			'page'   => $this->sellkit_license_page,
			'action' => $action,
			'nonce'  => wp_create_nonce( $this->sellkit_license_page ),
		], admin_url( 'admin.php' ) );

		return $url;
	}

	/**
	 * Connect after successful authorization.
	 *
	 * @since 1.0.0
	 */
	public function connect() {
		$user_email    = sellkit_htmlspecialchars( INPUT_GET, 'user_email' );
		$site_key      = sellkit_htmlspecialchars( INPUT_GET, 'site_key' );
		$secret_token  = sellkit_htmlspecialchars( INPUT_GET, 'secret_token' );
		$session_token = sellkit_htmlspecialchars( INPUT_GET, 'session_token' );
		$client_id     = sellkit_htmlspecialchars( INPUT_GET, 'client_id' );
		$auth_token    = sellkit_htmlspecialchars( INPUT_GET, 'auth_token' );

		if ( $this->verify_session_token( $session_token ) ) {

			update_option( $this->sellkit_license_option, [
				'status'       => 'active',
				'user_email'   => $user_email,
				'client_id'    => $client_id,
				'auth_token'   => $auth_token,
				'site_key'     => $site_key,
				'secret_token' => $secret_token,
			]);

			$this->destroy_session_token();

		}

		wp_redirect( $this->admin_url ); //phpcs:ignore

	}

	/**
	 * Create session token for imporved security.
	 *
	 * @return  string
	 * @since  1.0.0
	 */
	public function create_session_token() {

		$transient_name = 'sellkit_secure_token';
		$transient      = get_transient( $transient_name );

		if ( $transient ) {
			return $transient;
		}

		$token = $this->generate_token();

		set_transient( $transient_name, $token, 60 * 60 );

		return $token;

	}

	/**
	 * Verify session token.
	 *
	 * @param string $token token.
	 * @return boolean
	 * @since  1.0.0
	 */
	public function verify_session_token( $token ) {

		$transient_name = 'sellkit_secure_token';
		$transient      = get_transient( $transient_name );

		if ( ! $transient ) {
			return false;
		}

		if ( $token === $transient ) {
			return true;
		}
		return false;

	}

	/**
	 * Destroy session token.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function destroy_session_token() {

		delete_transient( 'sellkit_secure_token' );

	}

	/**
	 * Generate a secret token.
	 *
	 * @return  string
	 * @since  1.0.0
	 */
	public function generate_token() {
		return hash_hmac( 'sha256', get_current_user_id(), 'ucEJgMKmo6NbZztjs' . time() );
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

		if ( 'active' === $this->get( 'status' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( false === $this->get( 'status' ) ) {
			if ( false !== get_option( 'sellkit_dismiss_license_notice' ) ) {
				return;
			}
			$message = sprintf(
				'<span style="display:block;margin:0;">'
				. '<h3>'
				. __( 'Welcome to Sellkit Pro', 'sellkit-pro' )
				. '</h3>'
				. __( 'Please activate your license to get plugin updates, premium support and access to some locked features.', 'sellkit-pro' )
				. '</span>'
			);

			$message .= sprintf(
				'<span style="display:block;margin-top:1em; clear: both;">' .
				'<a class="button-primary" href="%1$s">%2$s</a></span>',
				$this->get_url( 'activate' ),
				__( 'Connect & Activate', 'sellkit-pro' )
			);

			$is_dismissable_class = 'is-dismissible';
		}

		if ( 'expired' === $this->get( 'status' ) ) {

			if ( false !== get_option( 'sellkit_dismiss_license_notice' ) ) {
				return;
			}
			$message = sprintf(
				'<span style="display:block;margin:0;">'
				. '<h3>'
				. __( 'Your License has expired!', 'sellkit-pro' )
				. '</h3>'
				. __( 'Please renew your license to continue getting plugin updates, premium support and access to some locked features.', 'sellkit-pro' )
				. '</span>'
			);

			$message .= sprintf(
				'<span style="display:block;margin-top:1em; clear: both;">' .
				'<a class="button-primary" href="https://my.getsellkit.com/subscriptions">%s</a></span>',
				__( 'Renew License', 'sellkit-pro' )
			);

			$is_dismissable_class = '';

		}

		if ( 'invalid' === $this->get( 'status' ) ) {

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
				$this->get_url( 'activate' ),
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



	/**
	 * Checks if plugin bundled and if so return partner id
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_partner_id() {

		if ( defined( 'SELLKIT_BUNDLED' ) ) {
			return SELLKIT_PARTNER_ID;
		}
		return; //phpcs:ignore
	}


	/**
	 * Checks if there site is connected to remote and the license status is active.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_license_active() {

		if ( 'active' === $this->get( 'status' ) ) {
			return true;
		}

		return false;

	}

}

new Sellkit_License();
