<?php

namespace Sellkit\Core\Update;

use Sellkit\Core\Install;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Class Updater functions.
 *
 * @since 1.1.0
 */
class Updater_Functions {

	/**
	 * First background process updating function.
	 *
	 * @since 1.1.0
	 */
	public function check_database_tables() {
		Install::check_database_tables();
	}

	/**
	 * Add ip to contact segmentation column.
	 *
	 * @since 1.2.1
	 */
	public function add_ip_column_to_contact_segmentation() {
		global $wpdb;

		$prefix = $wpdb->prefix . Database::DATABASE_PREFIX;

		if ( ! function_exists( 'check_column' ) ) {
			require_once ABSPATH . 'wp-admin/install-helper.php';
		}

		if ( ! empty( check_column( "{$prefix}contact_segmentation", 'ip', 'varchar(255)' ) ) ) {
			return;
		}

		$wpdb->query("ALTER TABLE {$prefix}contact_segmentation ADD ip VARCHAR (255) NULL DEFAULT NULL"); // phpcs:ignore
	}

	/**
	 * Add url query string to contact segmentation column.
	 *
	 * @since 1.2.3
	 */
	public function add_url_query_string_column_to_contact_segmentation() {
		global $wpdb;

		$prefix = $wpdb->prefix . Database::DATABASE_PREFIX;

		if ( ! function_exists( 'check_column' ) ) {
			require_once ABSPATH . '/wp-admin/install-helper.php';
		}

		if ( ! empty( check_column( "{$prefix}contact_segmentation", 'url_query_string', 'longtext' ) ) ) {
			return;
		}

		$wpdb->query("ALTER TABLE {$prefix}contact_segmentation ADD url_query_string longtext NULL DEFAULT NULL"); // phpcs:ignore
	}

	/**
	 * Adds funnel contact table.
	 *
	 * @since 1.5.0
	 */
	public function add_funnel_contact_table() {
		global $wpdb;

		$is_testcase_site = strpos( site_url(), 'artbees.team' ); // It should be removed.

		if (
			in_array( 'funnel_contact', $wpdb->tables, true ) &&
			! $is_testcase_site  // It should be removed.
		) {
			return;
		}

		// phpcs:disable
		if ( $is_testcase_site ) {
			$wpdb->query( "DROP TABLE IF EXISTS $wpdb->prefix" . Database::DATABASE_PREFIX . "funnel_contact" ); // It should be removed.
		}
		// phpcs:enable

		Database::create_new_table( 'funnel_contact' );
	}
}
