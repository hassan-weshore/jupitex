<?php

namespace Sellkit\Contact_Segmentation\rfm;

use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Class RFM Data Updater.
 *
 * @package Sellkit\Contact_Segmentation
 * @SuppressWarnings(ExcessiveClassComplexity)
 * @since 1.1.0
 */
class Updater extends \WP_Background_Process {

	/**
	 * Queue Action.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $action = 'rfm_updating_process';

	/**
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		$this->update_rfm( $item );

		return false;
	}

	/**
	 * Calculate RFM data and update it.
	 *
	 * @since 1.1.0
	 * @param array $data RFM data.
	 */
	public function update_rfm( $data ) {
		global $wpdb;

		$sellkit_prefix = Database::DATABASE_PREFIX;

		$limit  = $data['max_number'];
		$offset = $data['max_number'] - 100;

		// phpcs:disable
		$contacts = $wpdb->get_results(
			$wpdb->prepare( "SELECT
					id, last_order_date, total_spent, total_order_count
					from {$wpdb->prefix}{$sellkit_prefix}contact_segmentation
					order by id asc limit %d offset %d", $limit, $offset)
		);
		// phpcs:enable

		if ( is_wp_error( $contacts ) ) {
			new \WP_Error( __( 'Somethings went wrong', 'sellkit' ) );
		}

		if ( empty( $contacts ) ) {
			return;
		}

		foreach ( $contacts as $contact ) {
			$rfm_data = [
				'rfm_r' => self::get_score( intval( $data['max_recency'] ) - intval( $data['min_recency'] ), intval( $contact->last_order_date ) - intval( $data['min_recency'] ) ),
				'rfm_f' => self::get_score( $data['max_frequency'], $contact->total_order_count ),
				'rfm_m' => self::get_score( $data['max_monetary'], $contact->total_spent ),
			];

			sellkit()->db->update( 'contact_segmentation', $rfm_data, [ 'id' => $contact->id ] );
		}
	}

	/**
	 * Gets RFM score.
	 *
	 * @param int $max_value Max value.
	 * @param int $current_value Current value.
	 * @return int
	 * @since 1.1.0
	 */
	public static function get_score( $max_value, $current_value ) {
		if ( $current_value <= $max_value * 0.2 ) {
			return 1;
		}

		if ( $current_value <= $max_value * 0.4 ) {
			return 2;
		}

		if ( $current_value <= $max_value * 0.6 ) {
			return 3;
		}

		if ( $current_value <= $max_value * 0.8 ) {
			return 4;
		}

		return 5;
	}
}
