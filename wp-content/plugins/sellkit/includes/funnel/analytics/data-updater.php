<?php

namespace Sellkit\Funnel\Analytics;

use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Class Data updater.
 *
 * @since 1.1.0
 */
class Data_Updater {

	/**
	 * Funnel id.
	 *
	 * @var string.
	 */
	public $funnel_id;

	/**
	 * Funnel id.
	 *
	 * @var string.
	 */
	public $today_min_time;

	/**
	 * Data_Updater constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->today_min_time = strtotime( date( 'Y-m-d 0:0:0' ) );
	}

	/**
	 * Sets funnel id.
	 *
	 * @since 1.1.0
	 * @param int $funnel_id  Funnel id.
	 */
	public function set_funnel_id( $funnel_id ) {
		$this->funnel_id = $funnel_id;
	}

	/**
	 * Adds new visit.
	 *
	 * @since 1.1.0
	 * @param bool $is_unique The type of visit.
	 */
	public function add_new_visit( $is_unique = false ) {
		$data         = self::has_funnel_data();
		$visit_column = 'visit';

		if ( $is_unique ) {
			$visit_column = 'unique_visit';
		}

		if ( $data ) {
			global $wpdb;

			$table = $wpdb->prefix . Database::DATABASE_PREFIX . 'applied_funnel';

			$sql = "UPDATE `$table` SET {$visit_column} = %d WHERE funnel_id = %d AND applied_at > %s";

			$this->check_current_query = false;

			//phpcs:disable
			$wpdb->query(
				$wpdb->prepare( $sql, $data[ $visit_column ] + 1, $this->funnel_id, $this->today_min_time )
			);
			//phpcs:enable

			return;
		}

		$this->insert_applied_funnel();
	}

	/**
	 * Check if has funnel data.
	 *
	 * @since 1.1.0
	 */
	private function has_funnel_data() {
		global $wpdb;

		$database_prefix = Database::DATABASE_PREFIX;

		//phpcs:disable
		$result = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * from {$wpdb->prefix}{$database_prefix}applied_funnel
				WHERE funnel_id = %s and applied_at > %s",
				$this->funnel_id,
				$this->today_min_time
			)
			, ARRAY_A );
		//phpcs:enable

		if ( empty( $result[0] ) ) {
			return false;
		}

		return $result[0];
	}

	/**
	 * Inserts a new funnel query.
	 *
	 * @since 1.1.0
	 */
	private function insert_applied_funnel() {
		sellkit()->db->insert( 'applied_funnel', [
			'visit' => 1,
			'unique_visit' => 0,
			'funnel_id' => $this->funnel_id,
			'applied_at' => time(),
		] );
	}

	/**
	 * Adds new finish log.
	 *
	 * @since 1.1.0
	 */
	public function add_new_finish_log() {
		$data = self::has_funnel_data();

		if ( $data ) {
			global $wpdb;

			$table = $wpdb->prefix . Database::DATABASE_PREFIX . 'applied_funnel';

			$sql = "UPDATE `$table` SET  is_finished_number = %d WHERE funnel_id = %d AND applied_at > %s";

			//phpcs:disable
			$wpdb->query(
				$wpdb->prepare( $sql, $data[ 'is_finished_number' ] + 1, $this->funnel_id, $this->today_min_time )
			);
			//phpcs:enable
		}
	}

	/**
	 * Adds new start log.
	 *
	 * @since 1.1.0
	 */
	public function add_new_start_log() {
		$data = self::has_funnel_data();

		if ( empty( $data ) ) {
			return;
		}

		global $wpdb;

		$table = $wpdb->prefix . Database::DATABASE_PREFIX . 'applied_funnel';

		$sql = "UPDATE `$table` SET  is_started_number = %d WHERE funnel_id = %d AND applied_at > %s";

		//phpcs:disable
		$wpdb->query(
			$wpdb->prepare( $sql, $data[ 'is_started_number' ] + 1, $this->funnel_id, $this->today_min_time )
		);
		//phpcs:enable
	}

	/**
	 * Adds new order log.
	 *
	 * @since 1.1.0
	 * @param object $order Order object.
	 * @param string $type Type of order.
	 */
	public function add_new_order_log( $order, $type = false ) {
		$data = self::has_funnel_data();

		if ( empty( $data ) ) {
			return;
		}

		global $wpdb;

		$table = $wpdb->prefix . Database::DATABASE_PREFIX . 'applied_funnel';

		$sql = "UPDATE `$table` SET `orders` = %d, revenue = %d WHERE funnel_id = %d AND applied_at > %s";

		if ( empty( $data['revenue'] ) ) {
			$data['revenue'] = 0;
		}

		//phpcs:disable
		$wpdb->query(
			$wpdb->prepare( $sql, $data[ 'orders' ] + 1, $data[ 'revenue' ] + $order->get_total(), $this->funnel_id, $this->today_min_time )
		);

		if ( 'upsell' === $type ) {
			$wpdb->query(
				$wpdb->prepare( "UPDATE `$table` SET upsell_revenue = %s WHERE funnel_id = %d AND applied_at > %s", $data[ 'upsell_revenue' ] + $order->get_total(), $this->funnel_id, $this->today_min_time )
			);
		}

		//phpcs:enable
	}
}
