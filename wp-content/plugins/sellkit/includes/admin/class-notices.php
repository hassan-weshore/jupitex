<?php

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Sellkit_Notices {

	/**
	 * Sellkit_Notices constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->load_notices();

		add_action( 'wp_ajax_sellkit_admin_notice_dismiss', [ $this, 'dismiss_notice' ] );
	}

	/**
	 * Load notices.
	 *
	 * @since 1.1.0
	 * @param string $plugin_name Plugin name.
	 */
	public function load_notices( $plugin_name = 'sellkit' ) {
		$path = trailingslashit( sellkit()->plugin_dir() . 'includes/admin/notices' );

		if (
			'sellkit-pro' === $plugin_name &&
			sellkit()->has_pro &&
			file_exists( sellkit_pro()->plugin_dir() . 'includes/admin/notices/notice-base.php' )
		) {
			$path = trailingslashit( sellkit_pro()->plugin_dir() . 'includes/admin/notices' );
			require_once sellkit_pro()->plugin_dir() . 'includes/admin/notices/notice-base.php';
		}

		$file_paths = glob( $path . '*.php' );

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				continue;
			}

			require_once $file_path;

			$file_name      = str_replace( '.php', '', basename( $file_path ) );
			$operator_class = str_replace( '-', ' ', $file_name );
			$operator_class = str_replace( ' ', '_', ucwords( $operator_class ) );

			if ( 'sellkit' === $plugin_name ) {
				$operator_class = "Sellkit\Admin\Notices\\{$operator_class}";
			}

			if ( 'sellkit-pro' === $plugin_name && sellkit()->has_pro ) {
				$operator_class = "Sellkit_Pro\Admin\Notices\\{$operator_class}";
			}

			if ( ! class_exists( $operator_class ) || 'notice-base' === $file_name ) {
				continue;
			}

			$notice = new $operator_class();

			if ( $notice->is_valid() ) {
				add_action( 'admin_notices', [ $notice, 'notice_content_wrapper' ], $notice->priority() );
			}
		}

		if ( sellkit()->has_pro && 'sellkit' === $plugin_name ) {
			$this->load_notices( 'sellkit-pro' );
		}
	}

	/**
	 * Dismiss notice handler.
	 *
	 * @since 1.1.0
	 */
	public function dismiss_notice() {
		check_ajax_referer( 'sellkit_admin', 'nonce' );

		$new_notice_key        = ! empty( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		$dismissed_notices     = sellkit_get_option( 'dismissed_notices' );
		$dismissed_notices     = empty( $dismissed_notices ) ? [] : $dismissed_notices;
		$new_dismissed_notices = array_merge( $dismissed_notices, [ $new_notice_key ] );

		sellkit_update_option( 'dismissed_notices', array_filter( array_unique( $new_dismissed_notices ) ) );
	}
}

new Sellkit_Notices();
