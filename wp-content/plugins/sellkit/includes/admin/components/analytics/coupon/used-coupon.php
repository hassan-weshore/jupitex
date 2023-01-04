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
class Used_Coupon extends Analytics_Base {

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
		$coupons = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`, count(*) as total FROM {$coupon_table}
				where applied_at > {$start_date} and rule_id " . Analytics::target_id_condition( $this->target_id ) . " %d
				GROUP BY `day` ORDER BY `day` ASC;", $this->target_id ),
			ARRAY_A );
		// phpcs:enable

		$neat_data = [];

		foreach ( $coupons as $coupon ) {
			$neat_data[ $coupon['day'] ] = intval( $coupon['total'] );
		}

		$this->output = $neat_data;
	}
}
