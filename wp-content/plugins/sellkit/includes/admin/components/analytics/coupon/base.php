<?php

namespace Sellkit\Admin\Components\Analytics\Coupon;

use Sellkit\Admin\Components\Analytics;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
abstract class Base {

	/**
	 * Output data.
	 *
	 * @var $coupons array Discounts.
	 */
	public $output;

	/**
	 * Rule id.
	 *
	 * @var $rule_id int Rule id.
	 */
	public $rule_id;

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
