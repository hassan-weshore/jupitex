<?php

namespace Sellkit\Admin\Components\Analytics\Discount;

use Sellkit\Admin\Components\Analytics;
use Sellkit\Admin\Components\Analytics\Analytics_Base;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
class Revenue extends Analytics_Base {

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param int $rule_id Rule id.
	 */
	public function __construct( $rule_id ) {
		$this->target_id = $rule_id;

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
		$revenues = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`, SUM( order_total ) as total FROM {$discount_table}
				where applied_at > {$start_date} and order_id IS NOT NULL and discount_id " . Analytics::target_id_condition( $this->target_id ) . " %d
				GROUP BY `day` ORDER BY `day` ASC", $this->target_id ),
			ARRAY_A );
		// phpcs:enable

		$prepared_discount = [];
		foreach ( $revenues as $revenue ) {
			$prepared_discount[ $revenue['day'] ] = round( $revenue['total'] );
		}

		$this->output = $prepared_discount;
	}
}
