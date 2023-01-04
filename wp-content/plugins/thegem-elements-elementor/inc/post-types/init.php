<?php

require_once(plugin_dir_path( __FILE__ ) . 'portfolios.php');
require_once(plugin_dir_path( __FILE__ ) . 'teams.php');
require_once(plugin_dir_path( __FILE__ ) . 'testimonials.php');
require_once(plugin_dir_path( __FILE__ ) . 'news.php');
require_once(plugin_dir_path( __FILE__ ) . 'titles.php');
require_once(plugin_dir_path( __FILE__ ) . 'footers.php');
require_once(plugin_dir_path( __FILE__ ) . 'slideshows.php');

function thegem_rewrite_flush() {
	thegem_news_post_type_init();
	thegem_portfolio_item_post_type_init();

	thegem_rewrite_rules_flush();
}

function thegem_rewrite_rules_flush() {
	// force recreate rewrite rules, flush_rewrite_rules works unstable
	delete_option( 'rewrite_rules' );
}

register_activation_hook($thegem_plugin_file, 'thegem_rewrite_flush' );
register_deactivation_hook($thegem_plugin_file, 'thegem_rewrite_rules_flush' );

add_action( 'after_switch_theme', 'thegem_rewrite_rules_flush' );
