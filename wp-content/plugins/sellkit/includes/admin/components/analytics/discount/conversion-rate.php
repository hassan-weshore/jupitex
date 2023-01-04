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
class Conversion_Rate {

	/**
	 * All valid discoints.
	 *
	 * @var $discounts array Discounts.
	 */
	public $discounts;

	/**
	 * Abandoned discounts.
	 *
	 * @var $discounts array Abandoned discounts.
	 */
	public $applied_discounts;

	/**
	 * Discount id.
	 *
	 * @var $discount_id int discount_id.
	 */
	public $discount_id;

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param int $discount_id Discount id.
	 */
	public function __construct( $discount_id ) {
		$this->discount_id = $discount_id;

		$this->set_discounts_details();
	}

	/**
	 * Set discount details.
	 *
	 * @since 1.1.0
	 */
	public function set_discounts_details() {
		global $wpdb;

		$start_date = time() - ( 60 * 60 * 24 * Analytics::$date_range );

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$discount_table = "{$wpdb->prefix}{$sellkit_prefix}applied_discount";

		// phpcs:disable
		$applied_discounts = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`, count(*) as total FROM {$discount_table}
				where applied_at > {$start_date} and `order_id` IS NOT NULL and discount_id " . Analytics::target_id_condition( $this->discount_id ) . " %d
				GROUP BY `day` ORDER BY `day` ASC", $this->discount_id ),
			ARRAY_A );

		$discounts = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`, count(*) as total FROM {$discount_table}
				where applied_at > {$start_date} and discount_id " . Analytics::target_id_condition( $this->discount_id ) . " %d
				GROUP BY `day` ORDER BY `day` ASC", $this->discount_id ),
			ARRAY_A );
		// phpcs:enable

		$prepared_applied_discount = [];
		foreach ( $applied_discounts as $abandoned_discount ) {
			$prepared_applied_discount[ $abandoned_discount['day'] ] = $abandoned_discount['total'];
		}

		$prepared_discount = [];
		foreach ( $discounts as $discount ) {
			$prepared_discount[ $discount['day'] ] = $discount['total'];
		}

		$this->applied_discounts = $prepared_applied_discount;
		$this->discounts         = $prepared_discount;
	}

	/**
	 * Getting chart data.
	 *
	 * @since 1.1.0
	 */
	public function get_data() {
		$chart_data = [];

		for ( $i = Analytics::$date_range; $i >= 0; $i-- ) {
			$time   = strtotime( "-{$i} days" );
			$date   = date( 'M_d', $time );
			$result = [
				'date' => str_replace( '_', ' ', $date ),
				'value' => 0,
			];

			if ( $this->discounts[ $date ] && $this->applied_discounts[ $date ] ) {
				$result['value'] = ( $this->applied_discounts[ $date ] / $this->discounts[ $date ] ) * 100;
				$result['value'] = round( $result['value'], 2 );
			}

			$chart_data[] = $result;
		}

		return $chart_data;
	}

}
