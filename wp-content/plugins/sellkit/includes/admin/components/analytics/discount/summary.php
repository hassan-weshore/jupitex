<?php

namespace Sellkit\Admin\Components\Analytics\Discount;

use Sellkit\Admin\Components\Analytics;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
class summary {

	/**
	 * All valid discounts.
	 *
	 * @var $data array Discounts.
	 */
	public $data;

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var summary
	 */
	private static $instance = null;

	/**
	 * Gets class instance.
	 *
	 * @since 1.1.0
	 * @return summary
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->set_discounts_details();
	}

	/**
	 * Set discount details.
	 *
	 * @since 1.1.0
	 */
	public function set_discounts_details() {
		global $wpdb;

		$discount_id = sellkit_htmlspecialchars( INPUT_GET, 'target_id' );

		$start_date = time() - ( 60 * 60 * 24 * Analytics::$date_range );

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$discount_table = "{$wpdb->prefix}{$sellkit_prefix}applied_discount";

		// phpcs:disable
		$query_result = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(*) as applied_discount FROM {$discount_table}
				where applied_at > {$start_date} and discount_id " . Analytics::target_id_condition( $discount_id ) . " %d", $discount_id),
			ARRAY_A );

		$this->data['applied_discount'] = ! empty( $query_result[0]['applied_discount'] ) ? $query_result[0]['applied_discount'] : 0;

		$query_result = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(*) as converted FROM {$discount_table}
				where applied_at > {$start_date} and order_id IS NOT NULL and discount_id " . Analytics::target_id_condition( $discount_id ) . " %d", $discount_id ),
			ARRAY_A );

		$this->data['converted'] = ! empty( $query_result[0]['converted'] ) ? $query_result[0]['converted'] : 0;

		$revenues = $wpdb->get_results(
			$wpdb->prepare( "SELECT SUM( order_total ) as revenue FROM {$discount_table}
				where applied_at > {$start_date} and order_id IS NOT NULL and discount_id " . Analytics::target_id_condition( $discount_id ) . " %d", $discount_id ),
			ARRAY_A );

		$this->data['revenue'] = ! empty( $revenues[0]['revenue'] ) ? round( $revenues[0]['revenue'], 2 ) : 0;


		$discounts = $wpdb->get_results(
			$wpdb->prepare( "SELECT SUM( total_amount ) as total_discount FROM {$discount_table}
				where applied_at > {$start_date} and order_id IS NOT NULL and discount_id " . Analytics::target_id_condition( $discount_id ) . " %d", $discount_id ),
			ARRAY_A );

		$this->data['total_discount'] = ! empty( $discounts[0]['total_discount'] ) ? $discounts[0]['total_discount'] : 0;

		$customers = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(DISTINCT(email)) as customers FROM {$discount_table}
                     where applied_at > {$start_date} and discount_id " . Analytics::target_id_condition( $discount_id ) . " %d GROUP BY email; ", $discount_id ),
			ARRAY_A );

		$this->data['customers'] = ! empty( $customers ) && is_array( $customers ) ? count( $customers ) : 0;
		// phpcs:enable

		if ( empty( $this->data['converted'] ) ) {
			$this->data['conversion_rate'] = 0;
		}

		if ( ! empty( $this->data['converted'] ) ) {
			$this->data['conversion_rate'] = ( $this->data['converted'] / $this->data['applied_discount'] ) * 100;
			$this->data['conversion_rate'] = round( $this->data['conversion_rate'], 2 );
		}

		$this->data['target_title'] = get_the_title( $discount_id );
	}
}
