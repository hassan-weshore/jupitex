<?php

namespace Sellkit\Admin\Components\Analytics\Coupon;

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
	 * Output data.
	 *
	 * @var $coupons array Discounts.
	 */
	public $coupons;

	/**
	 * Abandoned coupons.
	 *
	 * @var $coupons array Abandoned coupons.
	 */
	public $applied_coupons;

	/**
	 * Coupon id.
	 *
	 * @var $coupon_id int coupon_id.
	 */
	public $coupon_id;

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param int $coupon_id Discount id.
	 */
	public function __construct( $coupon_id ) {
		$this->coupon_id = $coupon_id;

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

		$sellkit_prefix   = Database::DATABASE_PREFIX;
		$coupon_table     = "{$wpdb->prefix}{$sellkit_prefix}applied_coupon";
		$posts_table      = "{$wpdb->prefix}posts ";
		$posts_meta_table = "{$wpdb->prefix}postmeta ";

		// phpcs:disable
		$applied_coupons = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`, count(*) as total FROM {$coupon_table}
				where applied_at > {$start_date} and rule_id = %d
				GROUP BY `day` ORDER BY `day` ASC", $this->coupon_id ),
			ARRAY_A );

		$coupons = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(p.id) as total, pm.meta_key, DATE_FORMAT( p.post_date_gmt, '%%b_%%d') as day FROM {$posts_table} AS p
				LEFT JOIN {$posts_meta_table} pm ON pm.post_id = p.id AND pm.meta_key = 'sellkit_personalised_coupon_rule'
				WHERE %s < p.post_date_gmt and p.post_type = 'shop_coupon' and pm.meta_value = %s
				GROUP BY DAY(p.post_date_gmt)", $start_date, $this->coupon_id ),
			ARRAY_A );
		// phpcs:enable

		$prepared_applied_coupon = [];
		foreach ( $applied_coupons as $abandoned_discount ) {
			$prepared_applied_coupon[ $abandoned_discount['day'] ] = $abandoned_discount['total'];
		}

		$prepared_coupon = [];
		foreach ( $coupons as $discount ) {
			$prepared_coupon[ $discount['day'] ] = $discount['total'];
		}

		$this->applied_coupons = $prepared_applied_coupon;
		$this->coupons         = $prepared_coupon;
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

			if ( $this->coupons[ $date ] && $this->applied_coupons[ $date ] ) {
				$result['value'] = number_format( ( $this->applied_coupons[ $date ] / $this->coupons[ $date ] ) * 100, 2 );
				$result['value'] = floatval( $result['value'] );
			}

			$chart_data[] = $result;
		}

		return $chart_data;
	}
}
