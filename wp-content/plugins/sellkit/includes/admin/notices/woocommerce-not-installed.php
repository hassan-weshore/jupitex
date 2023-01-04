<?php

namespace Sellkit\Admin\Notices;

defined( 'ABSPATH' ) || die();

/**
 * Woocommerce not installed.
 *
 * @since 1.1.0
 */
class Woocommerce_Not_Installed extends Notice_Base {

	/**
	 * Notice key.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $key = 'sellkit-woocommerce-not-installed';

	/**
	 * Construct of class.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_sellkit_install_woocommerce_plugin_by_notice', [ $this, 'installer' ] );

		$this->assets();

		$this->content = $this->content_html();
		$this->buttons = [
			'#install-woo' => esc_html__( 'Install WooCommerce', 'sellkit' ),
		];
	}

	/**
	 * Enqueue required assets because these won't be loaded if Woocommerce is not installed.
	 *
	 * @since 1.1.0
	 */
	public function assets() {
		if ( ! $this->is_valid() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', function() {
			wp_enqueue_style(
				'sellkit-admin-notices-woo',
				sellkit()->plugin_url() . 'assets/dist/css/admin.min.css',
				[],
				sellkit()->version()
			);

			wp_enqueue_script(
				'sellkit-admin-notice-woo',
				sellkit()->plugin_url() . 'assets/dist/js/admin.min.js',
				[ 'jquery' ],
				sellkit()->version(),
				true
			);
		} );
	}

	/**
	 * Check if notice is valid or not.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_valid() {
		if ( class_exists( 'woocommerce' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Content of notice.
	 *
	 * @since 1.1.0
	 */
	public function content_html() {
		$message = sprintf(
			'<span>'
			. '<h3>'
			. esc_html__( 'Welcome to Sellkit', 'sellkit' )
			. '</h3>'
			/* Translators: 1: bold sellkit 2: bold woocommerce */
			. esc_html__( 'The %1$s plugin requires the %2$s plugin to be installed & activated as well.', 'sellkit' )
			. '</span>',
			'<b>Sellkit</b>',
			'<b>WooCommerce</b>'
		);

		return $message;
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

	/**
	 * Install WooCommerce plugin.
	 *
	 * @since 1.2.1
	 */
	public function installer() {
		check_ajax_referer( 'sellkit_admin', 'nonce' );

		require_once ABSPATH . '/wp-load.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$result = $this->sellkit_install_plugin( 'woocommerce' );

		if ( false === $result ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * Install WordPress plugin by slug.
	 *
	 * @param array $slug plugins slug.
	 * @since 1.2.1
	 * @return bool
	 */
	private function sellkit_install_plugin( $slug ) {
		$plugin_dir = WP_PLUGIN_DIR . '/' . $slug;

		// Plugin not installed.
		if ( ! is_dir( $plugin_dir ) ) {
			$api = plugins_api(
				'plugin_information',
				[
					'slug' => $slug,
					'fields' => [
						'short_description' => false,
						'sections' => false,
						'requires' => false,
						'rating' => false,
						'ratings' => false,
						'downloaded' => false,
						'last_updated' => false,
						'added' => false,
						'tags' => false,
						'compatibility' => false,
						'homepage' => false,
						'donate_link' => false,
					],
				]
			);

			$skin = new \Plugin_Installer_Skin( [ 'api' => $api ] );

			$upgrader = new \Plugin_Upgrader( $skin );

			$install = $upgrader->install( $api->download_link );

			if ( true !== $install ) {
				return false;
			}
		}

		$plugin_path = $plugin_dir . '/' . $slug . '.php';

		if ( file_exists( $plugin_path ) ) {
			activate_plugin( $plugin_path );

			return true;
		}

		return false;
	}
}
