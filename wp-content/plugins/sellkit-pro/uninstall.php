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
$sellkit_pro_delete_data = get_option( 'sellkit_pro_delete_data', 0 );

if ( '1' === $options['delete_data'] || '1' === $sellkit_pro_delete_data ) {
	// Drop Sellkit admin tables.
	Sellkit_Pro\Database::drop_all_tables();

	// Remove temporary option.
	delete_option( 'sellkit_pro_delete_data' );

	sellkit_pro_delete_posts();

	if ( is_multisite() ) {
		sellkit_pro_multisite_remove_tables();
		sellkit_pro_multisite_remove_data();
	}
}

/**
 * Deletes SellKit pro posts.
 *
 * @since 1.3.0
 * @return void
 */
function sellkit_pro_delete_posts() {
	global $wpdb;

	$posts_id  = $wpdb->prefix . 'posts.id';
	$meta_id   = $wpdb->prefix . 'postmeta.post_id';
	$posts     = $wpdb->prefix . 'posts'; // phpcs:ignore
	$meta      = $wpdb->prefix . 'postmeta';
	$post_type = $wpdb->prefix . 'posts.post_type'; // phpcs:ignore

	// phpcs:disable
	$wpdb->query(
		"DELETE $posts, $meta
		FROM $posts
		INNER JOIN $meta ON $meta_id = $posts_id
		WHERE $post_type IN ( 'sellkit-coupon', 'sellkit-alert', 'sellkit-discount' )"
	);
	// phpcs:enable

	// Clear any cached data that has been removed.
	wp_cache_flush();

	sellkit_pro_delete_option( 'pro_current_db_version' );
}

/**
 * Removes tables.
 *
 * @since 1.3.0
 * @return void
 */
function sellkit_pro_multisite_remove_tables() {
	global $wpdb;

	$database_name = DB_NAME;

	// phpcs:disable
	$query = $wpdb->get_results( "
		SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' )
	    AS statement FROM information_schema.tables
	    WHERE table_schema = '$database_name' AND ( table_name LIKE '%_sellkit_applied_alert' OR table_name LIKE '%_sellkit_applied_coupon' OR table_name LIKE '%_sellkit_applied_discount' );
	" );

	if ( ! empty( $query[0]->statement ) ) {
		$wpdb->query( $query[0]->statement );
	}
	// phpcs:enable
}

/**
 * Removes multisite data.
 *
 * @return void
 */
function sellkit_pro_multisite_remove_data() {
	$sites = get_sites();

	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );
		sellkit_pro_delete_posts();
		restore_current_blog();
	}

	delete_site_option( 'delete_data' );
}

/**
 * Delete options.
 *
 * @since 1.3.0
 * @param string $option Option name.
 * @return bool
 */
function sellkit_pro_delete_option( $option ) {
	$options = get_option( 'sellkit', [] );

	// Option not exist.
	if ( ! isset( $options[ $option ] ) ) {
		return false;
	}

	// Remove the option.
	unset( $options[ $option ] );
	update_option( 'sellkit', $options );

	return true;
}
