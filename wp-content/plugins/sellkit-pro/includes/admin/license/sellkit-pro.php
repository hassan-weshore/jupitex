<?php

defined( 'ABSPATH' ) || exit;

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
sellkit_pro()->load_files( [
	'admin/license/classes/class-sellkit',
] );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.1.0
 */
if ( ! function_exists( 'run_sellkit' ) ) {
	/**
	 * Run main class
	 *
	 * @since 1.1.0
	 */
	function run_sellkit() {

		$plugin = new Sellkit_Pro_Connect();
		$plugin->run();

	}
	run_sellkit();
}
