<?php

namespace Sellkit\Admin\Components\Analytics\Coupon;

use Sellkit\Admin\Components\Analytics;
use Sellkit\Admin\Components\Analytics\Analytics_Base;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
class Total_Discount extends Analytics_Base {

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param int $rule_id Discount id.
	 */
	public function __construct( $rule_id ) {
		$this->target_id = $rule_id;

		$this->set_coupon_details();
	}

	/**
	 * Set discount details.
	 *
	 * @since 1.1.0
	 */
	public function set_coupon_details() {
		global $wpdb;

		$start_date = time() - ( 60 * 60 * 24 * Analytics::$date_range );

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$coupon_table   = "{$wpdb->prefix}{$sellkit_prefix}applied_coupon";

		// phpcs:disable
		$total_discounts = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`, SUM( total_discount ) as total FROM {$coupon_table}
				where applied_at > {$start_date} and rule_id " . Analytics::target_id_condition( $this->target_id ) . " %d
				GROUP BY `day` ORDER BY `day` ASC", $this->target_id ),
			ARRAY_A );
		// phpcs:enable

		$prepared_coupons = [];
		foreach ( $total_discounts as $total_discount ) {
			$prepared_coupons[ $total_discount['day'] ] = $total_discount['total'];
		}

		$this->output = $prepared_coupons;
	}

	/**
	 * Getting chart data.
	 *
	 * @since 1.1.0
	 */
	public function get_data() {
		$chart_data = [];

		for ( $i = Analytics::$date_range; $i >= 0; $i-- ) {
			$time = strtotime( "-{$i} days" );
			$date = date( 'M_d', $time );

			$chart_data[] = [
				'date' => str_replace( '_', ' ', $date ),
				'value' => ! empty( $this->output[ $date ] ) ? floatval( $this->output[ $date ] ) : 0,
			];
		}

		return $chart_data;
	}
}
