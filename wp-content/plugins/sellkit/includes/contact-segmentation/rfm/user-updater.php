<?php

namespace Sellkit\Contact_Segmentation\rfm;

use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Class RFM user data updater.
 *
 * @package Sellkit\Contact_Segmentation
 * @SuppressWarnings(ExcessiveClassComplexity)
 * @since 1.1.0
 */
class User_Updater extends \WP_Background_Process {

	/**
	 * Queue Action.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $action = 'rfm_user_updating';

	/**
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $email Queue item to iterate over.
	 *
	 * @return mixed
	 */
	protected function task( $email ) {
		$this->update_users( $email );

		return false;
	}

	/**
	 * Updates the users.
	 *
	 * @since 1.1.0
	 * @param array $email Email.
	 */
	public function update_users( $email ) {
		global $wpdb;

		$sellkit_prefix = Database::DATABASE_PREFIX;

		// phpcs:disable
		$contact_segmentation_user = $wpdb->get_results(
			$wpdb->prepare( "SELECT * from {$wpdb->prefix}{$sellkit_prefix}contact_segmentation
			where email = %s", $email )
		);
		// phpcs:enable

		if ( ! empty( $contact_segmentation_user ) ) {
			return;
		}

		$current_user = get_user_by( 'email', $email );

		$args = [
			'customer_id' => $current_user->ID,
			'post_status' => 'completed',
			'post_type' => 'shop_order',
			'limit' => -1,
			'orderby' => 'id',
			'order' => 'DESC',
		];

		$orders       = wc_get_orders( $args );
		$order_number = count( $orders );
		$last_order   = ! empty( $orders[0] ) ? $orders[0] : '';
		$total_spent  = 0;

		foreach ( $orders as $order ) {
			$total_spent += $order->get_total();
		}

		$last_order_date = $last_order ? strtotime( $last_order->get_date_completed()->date( 'Y-m-d H:i:s' ) ) : '';

		sellkit()->db->insert( 'contact_segmentation', [
			'email' => $email,
			'last_order_date' => $last_order_date,
			'total_spent' => $total_spent,
			'total_order_count' => $order_number,
		] );
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 *
	 * @since 1.1.0
	 */
	protected function complete() {
		parent::complete();

		sellkit_update_option( 'contact_segmentation_users_are_imported', true );
		sellkit_update_option( 'contact_segmentation_users_updating_started', false );
		do_action( 'sellkit_update_rfm_score' );
	}
}
