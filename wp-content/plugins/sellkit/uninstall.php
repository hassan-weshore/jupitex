<?php
/**
 * Sellkit Uninstall
 *
 * Uninstalling Sellkit deletes tables, and options.
 *
 * @package Sellkit\Uninstaller
 * @version NEXT
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Include file of Database Class.
require_once dirname( __FILE__ ) . '/includes/db.php';

$options                 = get_option( 'sellkit', [] );
$multisite_delete_switch = is_multisite() ? get_site_option( 'delete_data' ) : false;

if ( '1' === $options['delete_data'] || '1' === $multisite_delete_switch ) {
	// Drop Sellkit admin tables.
	Sellkit\Database::drop_all_tables();

	// Delete sellkit option from wp_options.
	delete_option( 'sellkit' );

	if ( is_multisite() ) {
		sellkit_multisite_remove_tables();
		sellkit_multisite_remove_data();
	}

	sellkit_delete_posts();
}

/**
 * Deletes posts.
 *
 * @since 1.3.2
 * @return void
 */
function sellkit_delete_posts() {
	global $wpdb;

	$posts_id  = $wpdb->prefix . 'posts.id';
	$meta_id   = $wpdb->prefix . 'postmeta.post_id';
	$posts     = $wpdb->prefix . 'posts'; // phpcs:ignore
	$meta      = $wpdb->prefix . 'postmeta';
	$post_type = $wpdb->prefix . 'posts.post_type'; //phpcs:ignore

	// phpcs:disable
	$wpdb->query(
		"DELETE $posts, $meta
		FROM $posts
		INNER JOIN $meta ON $meta_id = $posts_id
		WHERE $post_type IN ( 'sellkit-funnels', 'sellkit_step' )"
	);
	// phpcs:enable

	// Unset sellkit cookie.
	setcookie( 'sellkit_contact_segmentation', null, -1, '/' );

	// Clear any cached data that has been removed.
	wp_cache_flush();

	// Temporary option.
	update_option( 'sellkit_pro_delete_data', '1' );
}

/**
 * Removes tables.
 *
 * @since 1.3.2
 * @return void
 */
function sellkit_multisite_remove_tables() {
	global $wpdb;

	$database_name = DB_NAME;

	// phpcs:disable
	$query = $wpdb->get_results( "
		SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' )
	    AS statement FROM information_schema.tables
	    WHERE table_schema = '$database_name' AND ( table_name LIKE '%_sellkit_contact_segmentation' OR table_name LIKE '%_sellkit_applied_funnel' );
	" );

	if ( ! empty( $query[0]->statement ) ) {
		$wpdb->query( $query[0]->statement );
	}
	// phpcs:enable
}

/**
 * Removes multisite data.
 *
 * @since 1.3.2
 * @return void
 */
function sellkit_multisite_remove_data() {
	$sites = get_sites();

	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );
		sellkit_delete_posts();
		delete_option( 'sellkit' );
		restore_current_blog();
	}

	delete_site_option( 'delete_data' );
}
