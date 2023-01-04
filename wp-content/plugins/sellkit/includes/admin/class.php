<?php

defined( 'ABSPATH' ) || die();

/**
 * Sellkit admin class.
 *
 * @since 1.2.1
 */
class Sellkit_Admin {
	/**
	 * Construct
	 *
	 * @since 1.2.1
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
	}

	/**
	 * Enqueues admin scripts.
	 *
	 * @since 1.2.1
	 */
	public function enqueue_admin_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'sellkit-admin',
			sellkit()->plugin_url() . 'assets/dist/js/admin' . $suffix . '.js',
			[ 'lodash', 'wp-element', 'wp-i18n', 'wp-util' ],
			sellkit()->version(),
			true
		);

		wp_enqueue_style(
			'sellkit-admin',
			sellkit()->plugin_url() . 'assets/dist/css/admin' . $suffix . '.css',
			[],
			sellkit()->version()
		);

		wp_localize_script(
			'sellkit-admin',
			'sellkitAdmin',
			[
				'nonce' => wp_create_nonce( 'sellkit_admin' ),
			]
		);
	}
}

new Sellkit_Admin();
