<?php

namespace Sellkit\Admin\Components\Analytics\Funnel;

use Sellkit\Admin\Components\Analytics;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.NPathComplexity)
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
		$this->set_alert_details();
	}

	/**
	 * Set discount details.
	 *
	 * @since 1.1.0
	 */
	public function set_alert_details() {
		global $wpdb;

		$funnel_id = sellkit_htmlspecialchars( INPUT_GET, 'target_id' );

		$start_date = time() - ( 60 * 60 * 24 * Analytics::$date_range );

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$funnel_table   = "{$wpdb->prefix}{$sellkit_prefix}applied_funnel";

		// phpcs:disable
		$query_result = $wpdb->get_results(
			$wpdb->prepare( "SELECT SUM(visit) as visit, SUM(unique_visit) as unique_visit
     		, SUM(revenue) as revenue, SUM(orders) as orders, SUM(upsell_revenue) as upsell_revenue
			, SUM(is_started_number) as total_started, SUM(is_finished_number) as total_finished
       		FROM {$funnel_table}
				where applied_at > {$start_date} and funnel_id " . Analytics::target_id_condition( $funnel_id ) . " %d", $funnel_id ),
			ARRAY_A );

		$this->data['visit']               = ! empty( $query_result[0]['visit'] ) ? $query_result[0]['visit'] : 0;
		$this->data['unique_visit']        = ! empty( $query_result[0]['unique_visit'] ) ? $query_result[0]['unique_visit'] : 0;
		$this->data['revenue']             = ! empty( $query_result[0]['revenue'] ) ? $query_result[0]['revenue'] : 0;
		$this->data['orders']              = ! empty( $query_result[0]['orders'] ) ? $query_result[0]['orders'] : 0;
		$this->data['upsell_revenue']      = ! empty( $query_result[0]['upsell_revenue'] ) ? $query_result[0]['upsell_revenue'] : 0;
		$total_leave                       = $query_result[0]['total_started'] - $query_result[0]['total_finished'];
		$bounce_rate                       = ! empty( $query_result[0]['total_started'] ) ? ( $total_leave * 100 ) / $query_result[0]['total_started'] : 0;
		$this->data['bounce_rate']         = ! empty( $bounce_rate ) ? floatval( number_format( $bounce_rate , 2 ) ) : 0;
		$conversion_rate                   = empty( $query_result[0]['total_started'] ) ? 0 : ( $query_result[0]['total_finished'] / $query_result[0]['total_started'] ) * 100;
		$this->data['conversion_rate']     = ! empty( $conversion_rate ) ? floatval( number_format( $conversion_rate , 2 ) ) : 0;
		$average_order_value               = empty( $this->data['orders'] ) ? 0 : $this->data['revenue'] / $this->data['orders'];
		$this->data['average_order_value'] = ! empty( $average_order_value ) ? floatval( number_format( $average_order_value , 2 ) ) : 0;
		// phpcs:enable

		$this->data['target_title'] = get_the_title( $funnel_id );
	}
}
