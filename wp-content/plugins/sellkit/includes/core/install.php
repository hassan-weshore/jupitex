<?php

namespace Sellkit\Core;

use Sellkit\Core\Update\Db_Updater;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Class Install.
 *
 * @since 1.1.0
 */
class Install {

	/**
	 * Background process object.
	 *
	 * @since 1.1.0
	 * @var Db_Updater
	 */
	public static $updater;

	/**
	 * DB updates and callbacks that need to be run per version.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	private static $updates = [
		'1.0.0' => [
			'check_database_tables',
		],
		'1.2.1' => [
			'add_ip_column_to_contact_segmentation',
		],
		'1.2.3' => [ // @since 1.2.3
			'add_url_query_string_column_to_contact_segmentation',
		],
		'1.2.7' => [ // @since 1.5.0
			'add_funnel_contact_table',
		],
	];

	/**
	 * Install constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		sellkit()->load_files( [
			'core/admin/feedback',
		] );

		$current_version = $this->get_current_database_version();
		$last_version    = array_key_last( self::$updates );

		if ( $current_version === $last_version ) {
			return;
		}

		sellkit()->load_files( [
			'core/update/db-updater',
			'core/update/updater-functions',
		] );

		$this->maybe_update();
	}

	/**
	 * If there are some updates it start to update.
	 *
	 * @since 1.1.0
	 */
	public function maybe_update() {
		$current_db_version = $this->get_current_database_version();
		$current_db_version = ! empty( $current_db_version ) ? $current_db_version : 0;
		self::$updater      = new Db_Updater();
		$has_new_update     = false;

		foreach ( self::$updates as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					self::$updater->push_to_queue( [
						'callback_function' => $update_callback,
						'db_version' => $version,
					] );
				}

				$has_new_update = true;
			}
		}

		if ( $has_new_update ) {
			self::$updater->save()->dispatch();
		}
	}

	/**
	 * Gets the current db version.
	 *
	 * @since 1.1.0
	 * @return bool|mixed
	 */
	public function get_current_database_version() {
		$current_db_version = sellkit_get_option( 'current_db_version' );

		if ( empty( $current_db_version ) ) {
			return false;
		}

		return $current_db_version;
	}

	/**
	 * Gets all tables which are not installed.
	 *
	 * @since 1.1.0
	 */
	public static function not_installed_tables() {
		global $wpdb;

		$database_name  = DB_NAME;
		$sellkit_prefix = Database::DATABASE_PREFIX;

		// phpcs:disable
		$installed_tables = $wpdb->get_results(
		"SHOW TABLES from `{$database_name}`
		where Tables_in_{$database_name} like '%applied_funnel%'
		or Tables_in_{$database_name} like '%contact_segmentation%'
		" );
		// phpcs:enable

		$neat_installed_tables = [];

		foreach ( $installed_tables as $installed_table ) {
			$table_data              = (array) $installed_table;
			$neat_installed_tables[] = str_replace( $wpdb->prefix . $sellkit_prefix, '', $table_data[ "Tables_in_{$database_name}" ] );
		}

		return array_diff( array_keys( Database::tables() ), $neat_installed_tables );
	}

	/**
	 * Creates necessary tables.
	 *
	 * @since 1.1.0
	 * @param array $tables Tables.
	 */
	public static function create_necessary_tables( $tables ) {
		foreach ( $tables as $table ) {
			Database::create_new_table( $table );
		}
	}

	/**
	 * Checks database tables.
	 *
	 * @since 1.1.0
	 */
	public static function check_database_tables() {
		$not_installed_tables = self::not_installed_tables();

		if ( ! empty( $not_installed_tables ) ) {
			self::create_necessary_tables( $not_installed_tables );
		}
	}
}

new Install();
