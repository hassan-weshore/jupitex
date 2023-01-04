<?php

namespace Sellkit\Funnel\Steps;

use Sellkit_Funnel;

defined( 'ABSPATH' ) || die();

/**
 * Class Decision.
 *
 * @since 1.5.0
 */
class Decision {

	/**
	 * Decision constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		$this->funnel = Sellkit_Funnel::get_instance();

	}

	/**
	 * Upsell actions.
	 *
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function do_actions() {
		$conditions = ! empty( $this->funnel->current_step_data['data']['conditions'] ) ? $this->funnel->current_step_data['data']['conditions'] : [];

		$is_valid = sellkit_conditions_validation( $conditions );

		$args = [];

		if ( ! empty( $_GET['order-key'] ) ) { // phpcs:ignore
			$args['order-key'] = $_GET['order-key']; // phpcs:ignore
		}

		if ( $is_valid && ! empty( $this->funnel->next_step_data['page_id'] ) ) {
			wp_safe_redirect( add_query_arg( $args, get_permalink( $this->funnel->next_step_data['page_id'] ) ) );
			return;
		}

		if ( ! $is_valid && ! empty( $this->funnel->next_no_step_data['page_id'] ) ) {
			wp_safe_redirect( add_query_arg( $args, get_permalink( $this->funnel->next_no_step_data['page_id'] ) ) );
			return;
		}

		if ( ! empty( $this->funnel->end_node_step_data['page_id'] ) ) {
			wp_safe_redirect( add_query_arg( $args, get_permalink( $this->funnel->end_node_step_data['page_id'] ) ) );
		}
	}
}
