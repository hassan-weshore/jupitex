<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * Initializing the Download and Redirect action by extending Action base..
 *
 * @since 1.5.0
 */
class Sellkit_Elementor_Optin_Action_Download_Redirect extends Sellkit_Elementor_Optin_Action_Base {

	public static $instance;

	public static function get_instance() {
		if ( empty( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function __construct() {
		// Register download hooks.
		add_action( 'admin_post_sellkit_download_file', [ self::class, 'handle_file_download' ] );
		add_action( 'admsellkitin_post_nopriv_sellkit_download_file', [ self::class, 'handle_file_download' ] );
	}

	public function get_name() {
		return 'download_redirect';
	}

	// Just for the sake of override!
	public function get_title() {}

	// Just for the sake of override!
	public function add_controls( $widget ) {}

	// Just for the sake of override!
	public function add_action_section_controls( $widget ) {}

	public function run( AjaxHandler $ajax_handler ) {

		// Proceed only if all other actions are done.
		if ( ! $ajax_handler->is_success ) {
			return;
		}

		$settings        = $ajax_handler->form['settings'];
		$download_source = $settings['download_source'];

		switch ( $download_source ) {
			case 'file':
				$this->download_file( $ajax_handler, $settings['download_file'] );
				break;
			case 'url':
				$this->download_url( $ajax_handler, $settings['download_url'] );
				break;
		}

		$this->redirect( $ajax_handler, $settings['redirect_to'], $settings['redirect_url'] );
	}

	private function download_file( AjaxHandler $ajax_handler, $file ) {
		if ( empty( $file['files'] ) ) {
			return;
		}

		$file = $file['files'][0];

		if ( ! file_exists( $file['path'] ) ) {
			$admin_error = esc_html__( 'Download error: The file doesn\'t exist anymore.', 'sellkit' );
			return $ajax_handler->add_response( 'admin_errors', $admin_error );
		}

		$args = [
			'action'   => 'sellkit_download_file',
			'file'     => base64_encode( $file['path'] ),
			'_wpnonce' => wp_create_nonce(),
		];

		$url = add_query_arg( $args, admin_url( 'admin-post.php' ) );
		return $ajax_handler->add_response( 'downloadURL', $url );
	}

	private function download_url( AjaxHandler $ajax_handler, $url ) {
		if ( empty( $url ) || empty( $url['url'] ) ) {
			return;
		}

		if ( ! filter_var( $url['url'], FILTER_VALIDATE_URL ) ) {
			$admin_error = esc_html__( 'Download error: Invalid file URL.', 'sellkit' );
			return $ajax_handler->add_response( 'admin_errors', $admin_error );
		}

		return $ajax_handler->add_response( 'downloadURL', $url['url'] );
	}

	private function redirect( AjaxHandler $ajax_handler, $target, $url ) {
		// When redirect target is funnel next step.
		if ( 'funnel' === $target ) {
			$funnel = sellkit_funnel();

			if ( empty( $funnel->next_step_data ) || empty( $funnel->next_step_data['page_id'] ) ) {
				$ajax_handler
					->set_success( false )
					->add_response( 'errors', esc_html__( 'Internal server error: failed to find next funnel step.', 'sellkit' ) );
				return;
			}

			$next_page_id  = sellkit_funnel()->next_step_data['page_id'];
			$current_url   = filter_input( INPUT_POST, 'referrer', FILTER_SANITIZE_URL );
			$current_query = wp_parse_url( $current_url, PHP_URL_QUERY );

			wp_parse_str( $current_query, $params );
			unset( $params['sellkit_step'] );

			// Nonce is already checked in AJAX handler class, so we ignore its phpcs warning.
			$next_step_url = add_query_arg( $params, get_permalink( intval( $next_page_id ) ) ); //phpcs:ignore WordPress.Security.NonceVerification
			return $ajax_handler->add_response( 'redirectURL', $next_step_url );
		}

		// When redirect target is a custom URL.
		if ( empty( $url ) || empty( $url['url'] ) ) {
			return;
		}

		if ( ! filter_var( $url['url'], FILTER_VALIDATE_URL ) ) {
			$admin_error = esc_html__( 'Redirect error: Invalid URL.', 'sellkit' );
			return $ajax_handler->add_response( 'admin_errors', $admin_error );
		}

		return $ajax_handler->add_response( 'redirectURL', $url['url'] );
	}

	/**
	 * Called by hook and handles file download.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 */
	public static function handle_file_download() {
		$file  = filter_input( INPUT_GET, 'file' );
		$nonce = filter_input( INPUT_GET, '_wpnonce' );

		// Validate nonce.
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce ) ) {
			wp_die( '<script>window.close();</script>' );
		}

		$file       = base64_decode( $file ); // phpcs:ignore
		$upload_dir = wp_get_upload_dir();

		// Make sure file exists.
		if ( empty( $file ) || ! file_exists( $file ) ) {
			wp_die( '<script>window.close();</script>' );
		}

		// Restrict the download to WP upload directory.
		if ( strpos( $file, $upload_dir['basedir'] ) > 0 ) {
			wp_die( '<script>window.close();</script>' );
		}

		$file_name = pathinfo( $file, PATHINFO_BASENAME );
		$file_ext  = pathinfo( $file, PATHINFO_EXTENSION );

		// Strip hash.
		$file_name  = str_replace( $file_ext, '', $file_name );
		$file_parts = explode( '__', $file_name );
		$file_name  = array_shift( $file_parts );
		$file_name .= '.' . $file_ext;

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename="' . $file_name . '"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $file ) );
		// phpcs:ignore WordPress.WP.AlternativeFunctions
		readfile( $file );
	}
}
