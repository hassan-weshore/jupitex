<?php

namespace Sellkit;

defined( 'ABSPATH' ) || die();

/**
 * Class Database.
 *
 * @package Sellkit\Contact_Segmentation\Base
 * @since 1.1.0
 */
class Database {

	const DATABASE_PREFIX = 'sellkit_';

	/**
	 * Return table name by key.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public static function tables() {
		global $wpdb;

		$tables = [
			'contact_segmentation' => [
				'name'  => $wpdb->prefix . self::DATABASE_PREFIX . 'contact_segmentation',
				'query' => '
						id bigint(20) NOT NULL AUTO_INCREMENT,
						email VARCHAR(50) UNIQUE NOT NULL,
						utm_source text,
						utm_medium text,
						utm_campaign text,
						utm_term text,
						utm_content text,
						visitor_country longtext,
						visitor_city longtext,
						user_device VARCHAR(11),
						browser_language VARCHAR(11),
						user_type VARCHAR(11),
						total_order_count VARCHAR(11),
						total_spent VARCHAR(11),
						first_order_date VARCHAR(11),
						last_order_date VARCHAR(11),
						purchased_product longtext,
						purchased_category longtext,
						billing_country text,
						billing_city text,
						shipping_country text,
						shipping_city text,
						viewed_product longtext,
						viewed_category longtext,
						rfm_r VARCHAR(3),
						rfm_f VARCHAR(3),
						rfm_m VARCHAR(3),
						ip VARCHAR(255),
						url_query_string longtext,
						updated_at VARCHAR(11),
						PRIMARY KEY ( id )
					',
			],
			'applied_funnel' => [
				'name'  => $wpdb->prefix . self::DATABASE_PREFIX . 'applied_funnel',
				'query' => '
					id bigint(20) NOT NULL AUTO_INCREMENT,
					funnel_id bigint(20) NOT NULL,
					visit int(11) DEFAULT 0,
					unique_visit bigint(20),
					is_started_number bigint(20),
					is_finished_number bigint(20),
					revenue VARCHAR(20),
					upsell_revenue VARCHAR(20),
					orders bigint(20),
					applied_at VARCHAR(20),
					PRIMARY KEY ( id )
				',
			],
			'funnel_contact' => [
				'name'  => $wpdb->prefix . self::DATABASE_PREFIX . 'funnel_contact',
				'query' => '
						id bigint(20) NOT NULL AUTO_INCREMENT,
						funnel_id bigint(20) NOT NULL,
						user_id bigint(20) NOT NULL,
						order_bump longtext DEFAULT NULL,
						upsell longtext DEFAULT NULL,
						downsell longtext DEFAULT NULL,
						optin longtext DEFAULT NULL,
						checkout longtext DEFAULT NULL,
						total_spent longtext DEFAULT NULL,
						updated_at VARCHAR(11),
						created_at VARCHAR(11),
						PRIMARY KEY ( id )
					',
			],
		];

		return apply_filters( 'sellkit_tables', $tables );
	}

	/**
	 * Create all tables on activation.
	 *
	 * @since 1.1.0
	 */
	public static function create_all_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		foreach ( self::tables() as $table ) {
			$table_name  = $table['name'];
			$table_query = $table['query'];

			if ( $table_name === $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) ) { // phpcs:ignore
				continue;
			}

			$sql = "CREATE TABLE $table_name (
					$table_query
				) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			dbDelta( $sql );
		}
	}

	/**
	 * Create a new table.
	 *
	 * @since 1.1.0
	 * @param string $table_main_name Table main name.
	 */
	public static function create_new_table( $table_main_name ) {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table       = self::tables()[ $table_main_name ];
		$table_name  = $table['name'];
		$table_query = $table['query'];

		if ( $table_name === $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) ) { // phpcs:ignore
			return;
		}

		$sql = "CREATE TABLE $table_name (
				$table_query
			) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
	}

	/**
	 * Drop all tables on deactivation.
	 *
	 * @since 1.1.0
	 */
	public static function drop_all_tables() {
		foreach ( self::tables() as $table ) {
			global $wpdb;

			$table_name = $table['name'];

			$wpdb->query( "DROP TABLE IF EXISTS $table_name" ); // phpcs:ignore
		}
	}

	/**
	 * Update row into table.
	 *
	 * @param string $table_name Table name.
	 * @param array  $data Data.
	 * @param array  $where Which row.
	 * @param array  $format Data.
	 * @since 1.1.0
	 */
	public function update( $table_name, $data, $where, $format = null ) {
		$prepared_data = [];

		foreach ( $data as $key => $value ) {
			$prepared_data[ $key ] = maybe_serialize( $value );
		}

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $wpdb->update( $wpdb->prefix . self::DATABASE_PREFIX . $table_name, $prepared_data, $where, $format );

		if ( ! $result ) {
			return false;
		}

		return $result;
	}

	/**
	 * Insert row into table.
	 *
	 * @param string $table_name Table name.
	 * @param array  $data Data.
	 * @param array  $format Data.
	 * @since 1.1.0
	 */
	public function insert( $table_name, $data, $format = null ) {
		$prepared_data = [];

		foreach ( $data as $key => $value ) {
			$prepared_data[ $key ] = maybe_serialize( $value );
		}

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $wpdb->insert( $wpdb->prefix . self::DATABASE_PREFIX . $table_name, $prepared_data, $format );

		if ( empty( $result ) || is_wp_error( $result ) ) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Gets database data.
	 *
	 * @since 1.1.0
	 * @param string $table_name Table name.
	 * @param array  $args Where args.
	 * @param null   $callback Callback function.
	 * @return array|false|object
	 */
	public function get( $table_name = null, $args = [], $callback = null ) {
		global $wpdb;

		$query      = "SELECT * FROM {$wpdb->prefix}" . self::DATABASE_PREFIX . "$table_name"; // phpcs:ignore

		if ( ! empty( $args ) ) {
			$query    .= ' WHERE ';
			$connector = ' AND ';

			foreach ( $args as $key => $value ) {
				if ( ! is_array( $value ) ) {
					$query .= sprintf( '`%1$s` = \'%2$s\'', esc_sql( $key ), esc_sql( $value ) );
				} else {
					$value  = array_map( 'esc_sql', $value );
					$query .= sprintf( '`%1$s` IN (%2$s)', esc_sql( $key ), implode( ',', $value ) );
				}

				if ( end( $args ) !== $value ) {
					$query .= $connector;
				}
			}
		}

		$query .= ' ORDER BY id DESC';

		$result = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore

		if ( ! $result ) {
			return false;
		}

		if ( ! $callback ) {
			return $result;
		}

		return array_map( $callback, $result );
	}
}
