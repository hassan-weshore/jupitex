<?php
/**
 * Plugin Name: TheGem Blocks (for Elementor)
 * Plugin URI: https://codex-themes.com/thegem/blocks-landing/
 * Description: TheGem Blocks is a plugin bundled with TheGem theme providing an access to the premium collection of pre-designed page sections like hero, about, services etc. Speed up your workflow. Create unique layouts. Mix & match on the fly.
 * Version: 1.0.4
 * Author: Codex Themes
 * Author URI: http://codex-themes.com/thegem/
 */


if (!is_admin()) {
	return;
}


if (!defined('ABSPATH')) {
	exit;
}

if (!defined('THEGEM_BLOCKS_DIR')) {
	define('THEGEM_BLOCKS_DIR', plugin_dir_path(__FILE__));
}

if (!defined('THEGEM_BLOCKS_URL')) {
	define('THEGEM_BLOCKS_URL', plugin_dir_url(__FILE__));
}

if (!function_exists('thegem_blocks_init')) {
	function thegem_blocks_init() {
		$theGemThemeOptions = get_option('thegem_theme_options');
		//if (is_user_logged_in() && $theGemThemeOptions && isset($theGemThemeOptions['purchase_code']) && class_exists('TheGemBlocksHelper')) {
			require_once THEGEM_BLOCKS_DIR . '/inc/classes/thegem-blocks.php';
			$theGemBlocks = new TheGemBlocks();
			$theGemBlocks->init();
		//}
		return true;
	}
}

thegem_blocks_init();

function thegem_blocks_plugins_loaded() {
	if (!function_exists('thegem_is_plugin_active') || !thegem_is_plugin_active('thegem-elements-elementor/thegem-elements-elementor.php')) {
		add_action( 'admin_notices', 'thegem_blocks_plugin_required_message' );
		return;
	}

	$plugin_data = get_plugin_data(trailingslashit(WP_PLUGIN_DIR).'thegem-elements-elementor/thegem-elements-elementor.php');
	if (!version_compare( $plugin_data['Version'], '4.3.0', '>=')) {
		add_action('admin_notices', 'thegem_blocks_plugin_required_message');
		return;
	}
}
add_action( 'plugins_loaded', 'thegem_blocks_plugins_loaded' );

function thegem_blocks_plugin_required_message() {
    if (isset($_GET['activate'])) {
        unset($_GET['activate']);
    }

	$message = '<p>'.sprintf(
		esc_html__( '%1$s plugin requires %2$s plugin in version %3$s or later. Please update.', 'thegem' ),
        '<strong>' . esc_html__( 'TheGem Blocks', 'thegem' ) . '</strong>',
        '<strong>' . esc_html__( 'TheGem Theme Elements', 'thegem' ) . '</strong>',
        '4.3.0'
	).'</p>';
	$message .= '<p>'.sprintf(wp_kses(__('<strong><a href="%s" class="thegem-update-link">Update now</a></strong>.', 'thegem'), array('strong' => array(), 'a' => array('href' => array(), 'class' => array()))), esc_url(admin_url('update-core.php'))).'</p>';

	printf('<div class="notice notice-error is-dismissible">%1$s</div>', $message);
}
