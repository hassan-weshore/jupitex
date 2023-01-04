<?php

namespace Sellkit\Contact_Segmentation\Rfm;

use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Class RFM score base class.
 *
 * @package Sellkit\Contact_Segmentation'Rfm
 * @since 1.1.0
 */
class Rfm_Score {

	/**
	 * Rfm Update instance.
	 *
	 * @var Updater Rfm updater.
	 * @since 1.1.0
	 */
	public static $rfm_updater;

	/**
	 * Rfm Update instance.
	 *
	 * @var Updater Rfm updater.
	 * @since 1.1.0
	 */
	public static $user_updater;

	/**
	 * Rfm_Score constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		sellkit()->load_files( [
			'contact-segmentation/rfm/updater',
			'contact-segmentation/rfm/user-updater',
		] );

		self::$rfm_updater  = new Updater();
		self::$user_updater = new User_Updater();

		add_action( 'sellkit_update_rfm_score', [ $this, 'update_rfm' ] );

		if (
			empty( sellkit_get_option( 'contact_segmentation_users_are_imported' ) ) &&
			empty( sellkit_get_option( 'contact_segmentation_users_updating_started' ) )
		) {
			sellkit_update_option( 'contact_segmentation_users_updating_started', true );
			$this->update_rfm_users();
		}
	}

	/**
	 * Updates RFM data.
	 *
	 * @since 1.1.0
	 */
	public function update_rfm() {
		global $wpdb;

		if ( empty( sellkit_get_option( 'contact_segmentation_users_are_imported' ) ) ) {
			$this->update_rfm_users();
			return;
		}

		$sellkit_prefix = Database::DATABASE_PREFIX;

		// phpcs:disable
		$results = $wpdb->get_results(
			"SELECT count(*) as total_contacts, MAX(last_order_date) as max_recency, MIN(last_order_date) as min_recency,
       		MAX(total_spent) as max_monetary, MAX(total_order_count) as max_frequency
			from {$wpdb->prefix}{$sellkit_prefix}contact_segmentation"
		);
		// phpcs:enable

		if ( is_wp_error( $results ) ) {
			new \WP_Error( __( 'Somethings went wrong', 'sellkit' ) );
		}

		if ( empty( $results[0] ) ) {
			return;
		}

		$rfm_data       = (array) $results[0];
		$total_contacts = ceil( $rfm_data['total_contacts'] / 100 );

		for ( $i = 100; $i <= ( $total_contacts * 100 ); $i = $i + 100 ) {
			self::$rfm_updater->push_to_queue(
				[
					'max_number' => $i,
					'max_recency' => $rfm_data['max_recency'],
					'min_recency' => $rfm_data['min_recency'],
					'max_monetary' => $rfm_data['max_monetary'],
					'max_frequency' => $rfm_data['max_frequency'],
				]
			);
		}

		self::$rfm_updater->save()->dispatch();
	}

	/**
	 * Updates RFM users.
	 *
	 * @since 1.1.0
	 */
	public function update_rfm_users() {
		$emails = get_users( [ 'fields' => [ 'user_email' ] ] );

		foreach ( $emails as $email ) {
			self::$user_updater->push_to_queue( $email->user_email );
		}

		self::$user_updater->save()->dispatch();
	}
}

new Rfm_Score();
