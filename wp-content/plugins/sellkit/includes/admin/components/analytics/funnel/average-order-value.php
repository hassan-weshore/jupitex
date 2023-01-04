<?php

namespace Sellkit\Admin\Components\Analytics\Funnel;

use Sellkit\Admin\Components\Analytics;
use Sellkit\Admin\Components\Analytics\Analytics_Base;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Conversion Rate class.
 *
 * @since 1.1.0
 */
class Average_Order_Value extends Analytics_Base {

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param int $funnel_id Rule id.
	 */
	public function __construct( $funnel_id ) {
		$this->target_id = $funnel_id;

		$this->set_alert_details();
	}

	/**
	 * Set discount details.
	 *
	 * @since 1.1.0
	 */
	public function set_alert_details() {
		global $wpdb;

		$start_date = time() - ( 60 * 60 * 24 * Analytics::$date_range );

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$funnel_table   = "{$wpdb->prefix}{$sellkit_prefix}applied_funnel";

		// phpcs:disable
		$results = $wpdb->get_results(
			$wpdb->prepare( "SELECT FROM_UNIXTIME(applied_at, '%%b_%%d') as `day`,
       			SUM(revenue) as total, SUM(orders) as total_orders
       			FROM {$funnel_table}
				where applied_at > {$start_date} and funnel_id " . Analytics::target_id_condition( $this->target_id ) . " %d
				GROUP BY `day` ORDER BY `day` ASC;", $this->target_id ),
			ARRAY_A );
		// phpcs:enable

		$neat_data = [];

		foreach ( $results as $result ) {
			$average_value = $result['total'] / $result['total_orders'];

			$neat_data[ $result['day'] ] = floatval( number_format( $average_value, 2 ) );
		}

		$this->output = $neat_data;
	}
}
