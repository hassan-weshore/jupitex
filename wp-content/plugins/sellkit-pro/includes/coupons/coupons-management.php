<?php

namespace Sellkit_Pro\Coupons;

defined( 'ABSPATH' ) || die();

/**
 * Class Coupons management.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @since 1.1.0
 */
class CouponsManagement {

	/**
	 * Coupons constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'woocommerce_new_order', [ $this, 'update_data_after_new_order' ], 10, 2 );
	}

	/**
	 * Create new coupon log.
	 *
	 * @since 1.1.0
	 * @param array $data Coupons data.
	 */
	public static function create( $data ) {
		if ( ! sellkit_pro()->has_valid_dependencies() ) {
			return;
		}

		sellkit()->db->insert( 'applied_coupon', $data );
	}

	/**
	 * Updates data.
	 *
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @param int $order_id Order id.
	 * @param int $order Order object.
	 */
	public function update_data_after_new_order( $order_id, $order ) {
		$coupons = $order->get_coupons();

		foreach ( $coupons as $coupon ) {
			$coupon = new \WC_Coupon( $coupon->get_code() );

			if ( empty( $coupon->get_meta( 'sellkit_personalised_coupon_rule' ) ) ) {
				continue;
			}

			self::create( [
				'coupon_id' => $coupon->get_id(),
				'rule_id' => $coupon->get_meta( 'sellkit_personalised_coupon_rule' ),
				'email' => $order->get_billing_email(),
				'revenue' => $order->get_total(),
				'total_discount' => $coupon->get_amount( 'edit' ),
				'applied_at' => time(),
			] );
		}
	}

	/**
	 * Gets coupon id.
	 *
	 * @since 1.1.0
	 * @param string $coupon_id Coupon id.
	 */
	public static function get_data( $coupon_id ) {
		return sellkit()->db->get( 'applied_coupon', [
			'coupon_id' => $coupon_id,
		] );
	}
}

new CouponsManagement();
