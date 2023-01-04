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
class Customers extends Analytics_Base {

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param int $rule_id Rule id.
	 */
	public function __construct( $rule_id ) {
		$this->target_id = $rule_id;

		$this->set_coupons_details();
	}

	/**
	 * Set discount details.
	 *
	 * @since 1.1.0
	 */
	public function set_coupons_details() {
		global $wpdb;

		$start_date = time() - ( 60 * 60 * 24 * Analytics::$date_range );

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$coupon_table   = "{$wpdb->prefix}{$sellkit_prefix}applied_coupon";

		// phpcs:disable
		$customers = $wpdb->get_results(
			$wpdb->prepare( "SELECT a.day, count(*) as total FROM
                     ( SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`, coupon_id, email FROM {$coupon_table}
                    where applied_at > {$start_date} and rule_id " . Analytics::target_id_condition( $this->target_id ) . " %d GROUP BY `day`, `email` ORDER BY `day` ASC ) as a
					group by a.day; ",
				$this->target_id ),
			ARRAY_A );
		// phpcs:enable

		$prepared_coupon = [];
		foreach ( $customers as $customer ) {
			$prepared_coupon[ $customer['day'] ] = intval( $customer['total'] );
		}

		$this->output = $prepared_coupon;
	}
}
