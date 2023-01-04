<?php

namespace Sellkit_Pro\Discounts;

use Sellkit\Database;
use WC_Tax;

defined( 'ABSPATH' ) || die();

/**
 * Class Dynamic Discount.
 *
 * @package Sellkit\Dynamic_Discount
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @since 1.1.0
 */
class Dynamic_Discount {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Dynamic_Discount
	 */
	private static $instance = null;

	/**
	 * Discount value.
	 *
	 * @since 1.1.0
	 * @var string|integer Discount value.
	 */
	public $discount_value = 0;

	/**
	 * An array of discounts.
	 *
	 * @since 1.1.0
	 * @var array Discount.
	 */
	public $total_discounts = [];


	/**
	 * Class Instance.
	 *
	 * @since 1.1.0
	 * @return Dynamic_Discount
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Dynamic_Discount constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'set_discounts' ] );
		add_action( 'woocommerce_review_order_before_order_total', [ $this, 'add_discount_to_checkout' ], 9999 );

		add_filter( 'woocommerce_calculated_total', [ $this, 'update_total_value' ], 9999 );
		add_filter( 'woocommerce_checkout_create_order', [ $this, 'update_order_meta' ], 10 );
		add_filter( 'woocommerce_checkout_order_created', [ $this, 'add_discount_logs' ], 10 );
	}

	/**
	 * Add a discount to an Orders programmatically
	 * (Using the FEE API - A negative fee)
	 *
	 * @since  3.2.0
	 * @param int    $order_id  The order ID. Required.
	 * @param string $title  The label name for the discount. Required.
	 * @param mixed  $amount  Fixed amount (float) or percentage based on the subtotal. Required.
	 * @param string $tax_class  The tax Class. '' by default. Optional.
	 */
	public function wc_order_add_discount( $order_id, $title, $amount, $tax_class = '' ) {
		$order    = wc_get_order( $order_id );
		$subtotal = $order->get_subtotal();
		$item     = new \WC_Order_Item_Fee();

		if ( strpos( $amount, '%' ) !== false ) {
			$percentage = (float) str_replace( [ '%', ' ' ], [ '', '' ], $amount );
			$percentage = $percentage > 100 ? -100 : -$percentage;
			$discount   = $percentage * $subtotal / 100;
		} else {
			$discount = (float) str_replace( ' ', '', $amount );
			$discount = $discount > $subtotal ? -$subtotal : -$discount;
		}

		$item->set_tax_class( $tax_class );
		$item->set_name( $title );
		$item->set_amount( $discount );
		$item->set_total( $discount );

		if ( '0' !== $item->get_tax_class() && 'taxable' === $item->get_tax_status() && wc_tax_enabled() ) {
			$tax_for   = array(
				'country'   => $order->get_shipping_country(),
				'state'     => $order->get_shipping_state(),
				'postcode'  => $order->get_shipping_postcode(),
				'city'      => $order->get_shipping_city(),
				'tax_class' => $item->get_tax_class(),
			);
			$tax_rates = WC_Tax::find_rates( $tax_for );
			$taxes     = WC_Tax::calc_tax( $item->get_total(), $tax_rates, false );

			if ( method_exists( $item, 'get_subtotal' ) ) {
				$subtotal_taxes = WC_Tax::calc_tax( $item->get_subtotal(), $tax_rates, false );
				$item->set_taxes(
					[
						'total' => $taxes,
						'subtotal' => $subtotal_taxes,
					]
				);
				$item->set_total_tax( array_sum( $taxes ) );
			} else {
				$item->set_taxes( [ 'total' => $taxes ] );
				$item->set_total_tax( array_sum( $taxes ) );
			}
			$has_taxes = true;
		} else {
			$item->set_taxes( false );
			$has_taxes = false;
		}

		$item->save();
		$order->add_item( $item );
		$order->calculate_totals( $has_taxes );
		$order->save();
	}

	/**
	 * Update order meta.
	 *
	 * @since 1.1.0
	 * @param object $order Order object.
	 * @return mixed
	 */
	public function update_order_meta( $order ) {
		$discounts = [];

		foreach ( $this->total_discounts as $discount_id => $total_discount ) {
			if ( $total_discount['price'] > 0 ) {
				$discounts[ $discount_id ] = $total_discount;
			}
		}

		if ( ! empty( $discounts ) ) {
			$order->update_meta_data( 'sellkit_discounts', $discounts );
		}

		return $order;
	}

	/**
	 * Adding orders log to the database.
	 *
	 * @since 1.1.0
	 * @param object $order Order.
	 */
	public function add_discount_logs( $order ) {
		$discounts = $order->get_meta( 'sellkit_discounts', true );

		if ( empty( $discounts ) ) {
			return;
		}

		$discount_data = ! empty( array_values( $discounts )[0] ) ? array_values( $discounts )[0] : '';

		if ( empty( $discount_data ) ) {
			return;
		}

		$this->wc_order_add_discount( $order->get_id(), $discount_data['label'], $discount_data['price'] );

		foreach ( $discounts as $discount_id => $discount ) {
			if ( empty( $discount['applied_discount_id'] ) ) {
				continue;
			}

			sellkit()->db->update(
				'applied_discount',
				[
					'order_id' => $order->get_id(),
					'order_total' => $order->get_total(),
				],
				[
					'id' => $discount['applied_discount_id'],
				]
			);
		}
	}

	/**
	 * Calculate the discount value
	 *
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function set_discounts() {
		$action = sellkit_htmlspecialchars( INPUT_GET, 'wc-ajax' );

		if ( ! function_exists( 'sellkit_condition_match' ) ) {
			return;
		}

		if ( ! wp_doing_ajax() || ( 'update_order_review' !== $action && 'checkout' !== $action ) ) {
			return;
		}

		$conditions = $this->get_discounts_conditions();

		foreach ( $conditions as $discount_id => $condition ) {
			$is_valid       = true;
			$condition_type = ! empty( $condition[1]['type'] ) ? $condition[1]['type'] : 'and';

			if ( empty( $conditions[ $discount_id ] ) ) {
				$this->total_discounts[ $discount_id ] = $this->set_discount_info( $discount_id, time() );

				return;
			}

			if ( 'or' === $condition_type ) {
				$is_valid = false;
			}

			foreach ( $condition as $item ) {
				if ( is_array( $item['condition_value'] ) && ! empty( $item['condition_value'][0]['value'] ) ) {
					$item['condition_value'] = sellkit_get_multi_select_values( $item['condition_value'] );
				}

				$result = sellkit_condition_match( $item['condition_subject'], $item['condition_operator'], $item['condition_value'] );

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
				$this->total_discounts[ $discount_id ] = $this->set_discount_info( $discount_id, time() );

				return;
			}
		}
	}

	/**
	 * Sets discount info.
	 *
	 * @since 1.1.0
	 * @param string $discount_id Discount Id.
	 * @param string $applied_at Time.
	 * @return array
	 */
	private function set_discount_info( $discount_id, $applied_at ) {
		return [
			'type' => get_post_meta( $discount_id, 'sellkit_type', true ),
			'value' => get_post_meta( $discount_id, 'value', true ),
			'label' => get_post_meta( $discount_id, 'label', true ),
			'filters' => get_post_meta( $discount_id, 'filters', true ),
			'use_with_other_discounts' => get_post_meta( $discount_id, 'use_with_other_discount', true ),
			'apply_to_on_sale_product' => get_post_meta( $discount_id, 'apply_to_on_sale_product', true ),
			'applied_at' => $applied_at,
			'discount_id' => $discount_id,
		];
	}

	/**
	 * Calculate total discount.
	 *
	 * @since 1.1.0
	 * @param string $total Total.
	 * @param string $value Value.
	 * @param string $type  Type.
	 * @return false|float|int|mixed
	 */
	public function calculate_discount( $total, $value, $type ) {
		if ( 'fixed-cart' === $type && $total < $value ) {
			return $total;
		}

		if ( 'fixed-cart' === $type ) {
			return $value;
		}

		if ( 'percentage' === $type ) {
			return ( $total * floatval( $value ) ) / 100;
		}

		return false;
	}

	/**
	 * Get all discount conditions.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	private function get_discounts_conditions() {
		$current_time = time();

		$args = [
			'post_type' => 'sellkit-discount',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => [ // phpcs:ignore
				'relation' => 'OR',
				[
					'relation' => 'AND',
					[
						'key'     => 'date_range_start',
						'value'   => $current_time,
						'compare' => '<',
					],
					[
						'key'     => 'date_range_end',
						'value'   => $current_time,
						'compare' => '>',
					],
				],
				[
					'relation' => 'OR',
					[
						'key'     => 'date_range_start',
						'value'   => '',
						'compare' => '=',
					],
					[
						'key'     => 'date_range_end',
						'value'   => '',
						'compare' => '=',
					],
				],
			],
			'orderby'  => [ 'meta_value_num' => 'ASC' ],
			'meta_key' => 'sellkit_usage_limit', // phpcs:ignore
		];

		$query = new \WP_Query( $args );

		$conditions_meta = [];

		foreach ( $query->posts as $post ) {
			if ( $this->check_is_valid_discount_for_user( $post->ID ) ) {
				$conditions_meta[ $post->ID ] = get_post_meta( $post->ID, 'conditions', true );
			}
		}

		return $conditions_meta;
	}

	/**
	 * Gets consumed discounts ids.
	 *
	 * @since 1.1.0
	 */
	public function get_consumed_discounts() {
		global $wpdb;
		$sellkit_prefix = Database::DATABASE_PREFIX;

		// phpcs:disable
		$prepared_query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}{$sellkit_prefix}applied_discount
			where email = %s and order_id is not null ;",
			$this->get_user_email()
		);

		$discounts = $wpdb->get_results(
				$prepared_query,
				ARRAY_A );
		// phpcs:enable

		return $discounts;
	}

	/**
	 * Get active discounts
	 *
	 * @since 1.2.7
	 */
	public function get_last_applied_discount() {
		global $wpdb;
		$sellkit_prefix = Database::DATABASE_PREFIX;
		$this->get_user_email();

		// phpcs:disable
		$prepared_query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}{$sellkit_prefix}applied_discount
			where email = %s and order_id is null order by id desc ;",
			$this->get_user_email()
		);

		$discounts = $wpdb->get_results(
				$prepared_query,
				ARRAY_A );
		// phpcs:enable

		return ! empty( $discounts[0] ) ? $discounts[0] : false;
	}

	/**
	 * Gets user email.
	 *
	 * @since 1.2.7
	 * @return string|null
	 */
	public function get_user_email() {
		global $current_user;

		if ( ! empty( $current_user->user_email ) ) {
			return $current_user->user_email;
		}

		return wc()->customer->get_billing_email( 'edit' );
	}

	/**
	 * Check a discount validation.
	 *
	 * @since 1.1.0
	 * @param integer $current_discount Current discount id.
	 * @return bool
	 */
	private function check_is_valid_discount_for_user( $current_discount ) {
		$can_repeat_discount = get_post_meta( $current_discount, 'repeat_discount_for_same_customer', true );

		$consumed_discounts_info = $this->get_consumed_discounts();

		if ( empty( $consumed_discounts_info ) ) {
			return true;
		}

		if ( $can_repeat_discount ) {
			return true;
		}

		foreach ( $consumed_discounts_info as $discount ) {
			if ( (int) $discount['discount_id'] === (int) $current_discount ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Adding discount to checkout total table.
	 *
	 * @since 1.1.0
	 */
	public function add_discount_to_checkout() {
		foreach ( $this->total_discounts as $discount_id => $total_discount ) {
			if ( empty( $total_discount['price'] ) ) {
				continue;
			}
			?>
			<tr class="order-total">
				<th><?php echo $total_discount['label']; ?></th>
				<td>-<?php echo wc_price( $total_discount['price'] ); ?></td>
			</tr>
			<?php
		}
	}

	/**
	 * Discount array.
	 *
	 * @since 1.2.7
	 * @param array  $total_discount Discount array.
	 * @param string $price Discount price.
	 * @param string $total Total amount.
	 */
	public function insert_discount_log( $total_discount, $price, $total ) {
		if ( empty( $price ) ) {
			return false;
		}

		$similar_discount = $this->get_last_applied_discount();

		if (
			empty( $similar_discount ) ||
			(int) $similar_discount['discount_id'] !== (int) $total_discount['discount_id'] ||
			(
				! empty( $similar_discount ) &&
				floatval( $similar_discount['total_amount'] ) !== floatval( $price ) &&
				floatval( $similar_discount['order_total'] ) !== floatval( $total )
			)
		) {
			$result = sellkit()->db->insert( 'applied_discount', [
				'email'        => $this->get_user_email(),
				'order_id'     => null,
				'discount_id'  => $total_discount['discount_id'],
				'order_total'  => $total - $price,
				'total_amount' => $price,
				'applied_at'   => $total_discount['applied_at'],
			] );

			return $result;
		}

		return $similar_discount['id'];
	}

	/**
	 * Handle discounted value.
	 *
	 * @since 1.1.0
	 * @param string|float $total Cart object.
	 */
	public function update_total_value( $total ) {
		$this->update_discountable_total_price();

		$total_discount = 0;
		$has_coupon     = wc()->cart->applied_coupons;

		if ( empty( $this->total_discounts ) ) {
			return $total;
		}

		foreach ( $this->total_discounts as $discount_id => $discount_array ) {
			$discount_price = $this->calculate_discount( $discount_array['total_discountable_price'], $discount_array['value'], $discount_array['type'] );
			$total_discount = floatval( $total_discount ) + floatval( $discount_price );

			if ( ! empty( $has_coupon ) && 'true' != $discount_array['use_with_other_discounts'] ) { // phpcs:ignore
				$this->total_discounts[ $discount_id ]['price'] = 0;
				continue;
			}

			$this->total_discounts[ $discount_id ]['price']               = $discount_price;
			$this->total_discounts[ $discount_id ]['applied_discount_id'] = $this->insert_discount_log( $discount_array, $discount_price, $total );
		}

		$this->discount_value = $total_discount;

		return ( $total - $this->discount_value ) >= 0 ? $total - $this->discount_value : 0;
	}

	/**
	 * Handle discountable total value.
	 *
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function update_discountable_total_price() {
		$cart_products = [];
		foreach ( wc()->cart->get_cart() as $item ) {
			$cart_products[] = $item['product_id'];
		}

		foreach ( $this->total_discounts as $discount_id => $discount_array ) {
			$discountable_products = [];
			$valid_cart_products   = $cart_products;

			$filter_details = $this->get_filter_details( $discount_array['filters'] );

			if ( ! empty( $filter_details['allowed_categories'] ) || ! empty( $filter_details['banned_categories'] ) ) {
				$valid_cart_products = $this->get_valid_products_by_category( $filter_details['allowed_categories'], $filter_details['banned_categories'], $cart_products );
			}

			$not_banned_products = array_diff( $valid_cart_products, $filter_details['banned_products'] );

			if ( ! empty( $filter_details['allowed_products'] ) ) {
				$not_banned_products = array_intersect( $filter_details['allowed_products'], $not_banned_products );
			}

			if ( ! empty( $not_banned_products ) ) {
				$discountable_products = array_intersect( $valid_cart_products, $not_banned_products );
			}

			if ( empty( $filter_details['allowed_products'] ) && empty( $filter_details['banned_products'] ) ) {
				$discountable_products = $valid_cart_products;
			}

			if ( empty( $discountable_products ) ) {
				$this->total_discounts[ $discount_id ]['total_discountable_price'] = 0;
				continue;
			}

			$total_price = 0;

			if ( empty( $discount_array['apply_to_on_sale_product'] ) ) {
				$discountable_products = $this->remove_on_sale_products( $discountable_products );
			}

			foreach ( wc()->cart->get_cart() as $cart_item ) {
				if ( in_array( $cart_item['product_id'], $discountable_products, true ) ) {
					$total_price = $total_price + $cart_item['line_total'];
				}
			}

			$this->total_discounts[ $discount_id ]['total_discountable_price'] = $total_price;
		}
	}


	/**
	 * Removes on sales products.
	 *
	 * @since 1.1.0
	 * @param array $products Products.
	 * @return array
	 */
	public function remove_on_sale_products( $products ) {
		$result = [];

		foreach ( $products as $product ) {
			$sale_price = get_post_meta( $product, '_sale_price', true );

			if ( empty( $sale_price ) ) {
				$result[] = $product;
			}
		}

		return $result;
	}

	/**
	 * Get filter details.
	 *
	 * @param array $filters Filter array.
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function get_filter_details( $filters ) {
		$allowed_products   = [];
		$allowed_categories = [];
		$banned_products    = [];
		$banned_categories  = [];

		foreach ( $filters as $filter ) {
			foreach ( $filter['value'] as $item ) {
				if (
					'is' === $filter['operator'] &&
					'products' === $filter['subject'] &&
					! empty( $item['value'] )
				) {
					$allowed_products[] = $item['value'];

					continue;
				}

				if ( 'is_not' === $filter['operator'] && 'products' === $filter['subject'] ) {
					$banned_products[] = $item['value'];
					continue;
				}

				if ( 'is' === $filter['operator'] && 'categories' === $filter['subject'] ) {
					$allowed_categories[] = $item['value'];
					continue;
				}

				if ( 'is_not' === $filter['operator'] && 'categories' === $filter['subject'] ) {
					$banned_categories[] = $item['value'];
				}
			}
		}

		return [
			'allowed_products' => $allowed_products,
			'allowed_categories' => $allowed_categories,
			'banned_products' => $banned_products,
			'banned_categories' => $banned_categories,
		];
	}

	/**
	 * Get valid products, checks with categories.
	 *
	 * @since 1.1.0
	 * @param array $allowed_categories Allowed categories.
	 * @param array $banned_categories Banned categories.
	 * @param array $cart_products Cart products.
	 * @return array
	 */
	public function get_valid_products_by_category( $allowed_categories, $banned_categories, $cart_products ) {
		$allowed_products = [];

		foreach ( $cart_products as $cart_product ) {
			$term_list = wp_get_post_terms( $cart_product, 'product_cat', [ 'fields' => 'ids' ] );
			if (
				( ! empty( $banned_categories ) && ! array_intersect( $term_list, $banned_categories ) ) ||
				( ! empty( $allowed_categories ) && array_intersect( $term_list, $allowed_categories ) )
			) {
				$allowed_products[] = $cart_product;
			}
		}

		return $allowed_products;
	}
}

Dynamic_Discount::get_instance();
