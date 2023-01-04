<?php

namespace Sellkit\Admin\Components\Analytics\Coupon;

use Sellkit\Admin\Components\Analytics;
use Sellkit\Admin\Components\Analytics\Analytics_Base;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
class Created_Coupon extends Analytics_Base {

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param int $rule_id Discount id.
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

		$start_date       = date( 'Y-m-d H:i:s', time() - ( 60 * 60 * 24 * Analytics::$date_range ) );
		$posts_table      = "{$wpdb->prefix}posts ";
		$posts_meta_table = "{$wpdb->prefix}postmeta ";

		// phpcs:disable
		$coupons = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(p.id) as total, pm.meta_key, DATE_FORMAT( p.post_date_gmt, '%%b_%%d') as day FROM {$posts_table} AS p
				LEFT JOIN {$posts_meta_table} pm ON pm.post_id = p.id AND pm.meta_key = 'sellkit_personalised_coupon_rule'
				WHERE %s < p.post_date_gmt and p.post_type = 'shop_coupon' and pm.meta_value " . Analytics::target_id_condition( $this->target_id ) . " %s
				GROUP BY DAY(p.post_date_gmt)", $start_date, $this->target_id ),
			ARRAY_A );
		// phpcs:enable

		$neat_data = [];

		foreach ( $coupons as $coupon ) {
			$neat_data[ $coupon['day'] ] = intval( $coupon['total'] );
		}

		$this->output = $neat_data;
	}
}
