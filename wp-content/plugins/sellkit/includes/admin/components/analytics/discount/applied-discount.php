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
class Applied_Discount extends Analytics_Base {

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
		$discounts = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`, count(*) as total FROM {$discount_table}
				where applied_at > {$start_date} and discount_id " . Analytics::target_id_condition( $this->target_id ) . " %d
				GROUP BY `day` ORDER BY `day` ASC;", $this->target_id),
			ARRAY_A );
		// phpcs:enable

		$neat_data = [];

		foreach ( $discounts as $discount ) {
			$neat_data[ $discount['day'] ] = intval( $discount['total'] );
		}

		$this->output = $neat_data;
	}
}
