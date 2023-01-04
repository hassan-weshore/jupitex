<?php

namespace Sellkit\Funnel\Steps;

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit_Thankyou.
 *
 * @since 1.1.0
 */
class Thankyou extends Base_Step {

	/**
	 * Thankyou constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'template_redirect', [ $this, 'redirect_after_purchase' ], 10 );
	}

	/**
	 * Redirects after purchasing.
	 *
	 * @since 1.1.0
	 */
	public function redirect_after_purchase() {
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		global $wp;

		$funnel = sellkit_funnel();

		if ( ! empty( $funnel->funnel_id ) && 'thankyou' === $funnel->current_step_data['type']['key'] ) {
			return;
		}

		if ( ! function_exists( 'is_checkout' ) ) {
			return;
		}

		if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
			$order_id  = absint( $wp->query_vars['order-received'] );
			$order_key = sellkit_htmlspecialchars( INPUT_GET, 'key' );
			$order     = wc_get_order( $order_id );
			$next_step = $order->get_meta( 'sellkit_funnel_next_step_data' );

			if ( empty( $next_step ) ) {
				return;
			}

			$last_price = $order->get_total() - $order->get_total_discount() - $order->get_total_tax();

			$this->contacts->add_total_spent( $last_price );

			$next_step_link = add_query_arg( [ 'order-key' => $order_key ], get_permalink( $next_step['page_id'] ) );

			wp_safe_redirect( $next_step_link );
			exit();
		}
	}
}
