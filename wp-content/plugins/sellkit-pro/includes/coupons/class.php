<?php

namespace Sellkit_Pro\Coupons;

defined( 'ABSPATH' ) || die();

/**
 * Class Coupons.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @since 1.1.0
 */
class Coupons {

	/**
	 * Coupons constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		sellkit()->load_files( [ 'coupons/coupons-management' ] );

		add_action( 'wp_ajax_sellkit_get_personalised_coupons', [ $this, 'get_coupon' ] );
		add_action( 'wp_ajax_nopriv_sellkit_get_personalised_coupons', [ $this, 'get_coupon' ] );
		add_action( 'wp_ajax_sellkit_check_personalised_coupon', [ $this, 'check_coupon' ] );
		add_action( 'wp_ajax_nopriv_sellkit_check_personalised_coupon', [ $this, 'check_coupon' ] );
	}

	/**
	 * Create Coupons.
	 *
	 * @since 1.1.0
	 * @param string $display_type Display type.
	 */
	public function create_coupons( $display_type ) {
		$rule = $this->get_rule_by_conditions( $display_type );

		$coupon_meta       = get_post_meta( $rule );
		$filters           = get_post_meta( $rule, 'filters', true );
		$coupon_type       = ! empty( $coupon_meta['sellkit_type'][0] ) ? $coupon_meta['sellkit_type'][0] : 'percent';
		$coupon_amount     = ! empty( $coupon_meta['value'][0] ) ? $coupon_meta['value'][0] : 0;
		$use_with_others   = ! empty( $coupon_meta['use_with_other_coupon'][0] ) ? $coupon_meta['use_with_other_coupon'][0] : false;
		$apply_to_on_sale  = ! empty( $coupon_meta['apply_onsale_product'][0] ) ? $coupon_meta['apply_onsale_product'][0] : true;
		$minimum_subtotal  = ! empty( $coupon_meta['minimum_subtotal_count'][0] ) ? $coupon_meta['minimum_subtotal_count'][0] : '';
		$coupon_limitation = ! empty( $coupon_meta['can_use_count_total'][0] ) ? $coupon_meta['can_use_count_total'][0] : '';
		$expiration_number = ! empty( $coupon_meta['sellkit_expiration_date_number'][0] ) ? $coupon_meta['sellkit_expiration_date_number'][0] : '';
		$expiration_type   = ! empty( $coupon_meta['sellkit_expiration_date_type'][0] ) ? $coupon_meta['sellkit_expiration_date_type'][0] : '';
		$is_individual     = ! empty( $coupon_meta['sellkit_can_use'][0] ) ? $coupon_meta['sellkit_can_use'][0] : false;
		$expiration_time   = $this->get_expiration_time( $expiration_number, $expiration_type );

		$coupon = new \WC_Coupon();

		// Generate a non existing coupon code name.
		$coupon_code = self::generate_coupon_code();

		// Set the necessary coupon data.
		$coupon->set_code( $coupon_code );

		foreach ( $filters as $filter ) {
			if ( 'products' === $filter['subject'] && 'is' === $filter['operator'] ) {
				$coupon->set_product_ids( sellkit_get_multi_select_values( $filter['value'] ) );
			}

			if ( 'products' === $filter['subject'] && 'is_not' === $filter['operator'] ) {
				$coupon->set_excluded_product_ids( sellkit_get_multi_select_values( $filter['value'] ) );
			}

			if ( 'categories' === $filter['subject'] && 'is_not' === $filter['operator'] ) {
				$coupon->set_excluded_product_categories( sellkit_get_multi_select_values( $filter['value'] ) );
			}

			if ( 'categories' === $filter['subject'] && 'is' === $filter['operator'] ) {
				$coupon->set_product_categories( sellkit_get_multi_select_values( $filter['value'] ) );
			}
		}

		if ( 'percent' === $coupon_type && $coupon_amount > 100 ) {
			$coupon_amount = 100;
		}

		if ( empty( $coupon_amount ) ) {
			return;
		}

		$coupon->set_discount_type( $coupon_type );
		$coupon->set_amount( floatval( $coupon_amount ) );
		$coupon->set_individual_use( $use_with_others );
		$coupon->set_exclude_sale_items( ! $apply_to_on_sale );
		$coupon->set_minimum_amount( $minimum_subtotal );
		$coupon->set_usage_limit( $coupon_limitation );

		$coupon->add_meta_data( 'sellkit_personalised_coupon_rule', $rule );

		if ( ! empty( $expiration_time ) ) {
			$coupon->set_date_expires( $expiration_time );
		}

		if ( $is_individual && ! empty( wp_get_current_user()->user_email ) ) {
			$coupon->set_email_restrictions( wp_get_current_user()->user_email );
		}

		// Create, publish and save coupon (data).
		$coupon->save();

		return [
			'code' => $coupon_code,
			'amount' => $coupon_amount,
			'type' => $coupon_type,
			'expiration_date' => empty( $expiration_time ) ? '' : get_date_from_gmt( date( 'Y-m-d H:i:s', $expiration_time ), 'Y/m/d h:i A' ),
			'display_type' => $display_type,
			'rule_id' => $rule,
		];
	}

	/**
	 * Coupons generator.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public static function generate_coupon_code() {
		return strtolower( wp_generate_password( 10, false ) );
	}

	/**
	 * Gets expiration time.
	 *
	 * @since 1.1.0
	 * @param string $number Number of type.
	 * @param string $type The type of time.
	 */
	private function get_expiration_time( $number, $type ) {
		if ( empty( $number ) || empty( $type ) ) {
			return false;
		}

		$type_days = 1;

		if ( 'weeks' === $type ) {
			$type_days = 7;
		}

		if ( 'months' === $type ) {
			$type_days = 30;
		}

		return time() + ( ( intval( $number ) * $type_days ) * ( 60 * 60 * 24 ) );
	}

	/**
	 * Gets coupons.
	 *
	 * @since 1.1.0
	 */
	public function get_coupon() {
		$nonce        = sellkit_htmlspecialchars( INPUT_GET, 'nonce' );
		$display_type = sellkit_htmlspecialchars( INPUT_GET, 'display_type' );

		wp_verify_nonce( $nonce, 'sellkit_elementor' );

		$coupon = $this->create_coupons( $display_type );

		if ( ! empty( $coupon ) ) {
			setcookie( 'sellkit_personalised_coupon', wp_json_encode( $coupon ), time() + 86400, '/' );
		}

		wp_send_json_success( $coupon );
	}

	/**
	 * Gets rules by conditions.
	 *
	 * @since 1.1.0
	 * @return string
	 * @param string $display_type Display Type.
	 */
	private function get_rule_by_conditions( $display_type ) {
		$args = [
			'post_type' => 'sellkit-coupon',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby'  => [ 'meta_value_num' => 'ASC' ],
			'meta_key' => 'sellkit_usage_limit', // phpcs:ignore
		];

		if ( 'add-to-content' === $display_type ) {
			$args['meta_query'] = [ // phpcs:ignore
				'relation' => 'AND',
				[
					'key'     => 'sellkit_add_content_page_post',
					'value'   => 'true',
				],
			];
		}

		$query = new \WP_Query( $args );

		foreach ( $query->posts as $post ) {
			$conditions     = get_post_meta( $post->ID, 'conditions', true );
			$is_valid       = true;
			$condition_type = ! empty( $conditions[1]['type'] ) ? $conditions[1]['type'] : 'and';

			if ( 'or' === $condition_type ) {
				$is_valid = false;
			}

			foreach ( $conditions as $condition ) {
				if ( is_array( $condition['condition_value'] ) && ! empty( $condition['condition_value'][0]['value'] ) ) {
					$condition['condition_value'] = sellkit_get_multi_select_values( $condition['condition_value'] );
				}

				$result = sellkit_condition_match( $condition['condition_subject'], $condition['condition_operator'], $condition['condition_value'] );

				if ( is_wp_error( $result ) ) {
					continue;
				}

				if ( ! $result ) {
					$is_valid = false;
				}

				if ( $result && 'or' === $condition_type ) {
					$is_valid = true;
					break;
				}
			}

			if ( true === $is_valid ) {
				return $post->ID;
			}
		}
	}

	/**
	 * Checks the cookie coupon.
	 *
	 * @since 1.2.3
	 */
	public function check_coupon() {
		check_ajax_referer( 'sellkit_elementor', 'nonce' );

		$rule_id    = sellkit_htmlspecialchars( INPUT_GET, 'rule_id' );
		$conditions = get_post_meta( $rule_id, 'conditions', true );

		if ( sellkit_conditions_validation( $conditions ) ) {
			wp_send_json_success( [ 'coupon_is_valid' => true ] );
		}

		wp_send_json_success( [ 'coupon_is_valid' => false ] );
	}
}

new Coupons();
