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
class Summary {

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
	 * @var Summary
	 */
	private static $instance = null;

	/**
	 * Gets class instance.
	 *
	 * @since 1.1.0
	 * @return Summary
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

		$rule_id = sellkit_htmlspecialchars( INPUT_GET, 'target_id' );

		$start_date = time() - ( 60 * 60 * 24 * Analytics::$date_range );

		$sellkit_prefix    = Database::DATABASE_PREFIX;
		$discount_table    = "{$wpdb->prefix}{$sellkit_prefix}applied_coupon";
		$posts_table       = "{$wpdb->prefix}posts ";
		$posts_meta_table  = "{$wpdb->prefix}postmeta ";
		$coupon_start_date = date( 'Y-m-d H:i:s', time() - ( 60 * 60 * 24 * Analytics::$date_range ) );

		// phpcs:disable
		$coupons = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(*) as created_coupon FROM {$posts_table} AS p
				LEFT JOIN {$posts_meta_table} pm ON pm.post_id = p.id AND pm.meta_key = 'sellkit_personalised_coupon_rule'
				WHERE %s < p.post_date_gmt and p.post_type = 'shop_coupon' and pm.meta_value " . Analytics::target_id_condition( $rule_id ) . " %s", $coupon_start_date, $rule_id ),
			ARRAY_A );

		$this->data['created_coupon'] = ! empty( $coupons[0]['created_coupon'] ) ? $coupons[0]['created_coupon'] : 0;

		$query_result = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(*) as used_coupon FROM {$discount_table}
				where applied_at > {$start_date} and rule_id " . Analytics::target_id_condition( $rule_id ) . " %d", $rule_id ),
			ARRAY_A );

		$this->data['used_coupon'] = ! empty( $query_result[0]['used_coupon'] ) ? $query_result[0]['used_coupon'] : 0;

		$monetary_data = $wpdb->get_results(
			$wpdb->prepare( "SELECT SUM( revenue ) as revenue, SUM( total_discount ) as total_discount FROM {$discount_table}
				where applied_at > {$start_date} and rule_id " . Analytics::target_id_condition( $rule_id ) . " %d", $rule_id ),
			ARRAY_A );

		$this->data['revenue']        = ! empty( $monetary_data[0]['revenue'] ) ? $monetary_data[0]['revenue'] : 0;
		$this->data['total_discount'] = ! empty( $monetary_data[0]['total_discount'] ) ? $monetary_data[0]['total_discount'] : 0;

		$customers = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(DISTINCT(email)) as customers FROM {$discount_table}
                     where applied_at > {$start_date} and rule_id " . Analytics::target_id_condition( $rule_id ) . " %d GROUP BY email; ", $rule_id ),
			ARRAY_A );

		$this->data['customers'] = ! empty( $customers ) && is_array( $customers ) ? count( $customers ) : 0;
		// phpcs:enable

		if ( empty( $this->data['used_coupon'] ) ) {
			$this->data['conversion_rate'] = 0;
		}

		if ( ! empty( $this->data['used_coupon'] ) ) {
			$this->data['conversion_rate'] = ( $this->data['used_coupon'] / $this->data['created_coupon'] ) * 100;
			$this->data['conversion_rate'] = floatval( number_format( $this->data['conversion_rate'], 2 ) );
		}

		$this->data['target_title'] = get_the_title( $rule_id );
	}
}
