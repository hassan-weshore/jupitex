<?php

use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Components class.
 *
 * @since 1.1.0
 */
class Sellkit_Custom_Tables {

	const CUSTOM_TABLE_PER_PAGE = 20;

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Custom_Tables
	 */
	private static $instance = null;

	/**
	 * Get a class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return Sellkit_Custom_Tables Class instance.
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
		add_action( 'wp_ajax_sellkit_get_applied_discount', [ $this, 'get_applied_discounts' ] );
		add_action( 'wp_ajax_sellkit_get_generated_coupons', [ $this, 'get_generated_coupons' ] );
		add_action( 'wp_ajax_sellkit_funnel_get_contacts', [ $this, 'get_contacts' ] );
		add_action( 'wp_ajax_sellkit_funnel_get_contacts_history', [ $this, 'get_history' ] );
		add_action( 'wp_ajax_sellkit_funnel_delete_contact', [ $this, 'delete_contact' ] );
		add_action( 'wp_ajax_sellkit_funnel_get_contact_details', [ $this, 'get_contact_details' ] );
		add_action( 'wp_ajax_sellkit_funnel_get_funnel_orders', [ $this, 'get_funnel_orders' ] );
		add_action( 'wp_ajax_sellkit_funnel_save_contact_details', [ $this, 'save_contact_details' ] );
	}

	/**
	 * Gets applied discounts.
	 *
	 * @since 1.1.0
	 */
	public function get_applied_discounts() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$page = sellkit_htmlspecialchars( INPUT_GET, 'page' );
		$id   = sellkit_htmlspecialchars( INPUT_GET, 'id' );

		if ( empty( $page ) ) {
			$page = 1;
		}

		$page_index = ( $page - 1 ) * self::CUSTOM_TABLE_PER_PAGE;
		$limit      = self::CUSTOM_TABLE_PER_PAGE;

		global $wpdb;

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$discount_table = "{$wpdb->prefix}{$sellkit_prefix}applied_discount";

		// phpcs:disable
		$prepared_query = $wpdb->prepare( "SELECT $discount_table.*, {$wpdb->prefix}users.ID as user_id  FROM {$discount_table}
		LEFT JOIN {$wpdb->prefix}users on {$discount_table}.email = {$wpdb->prefix}users.user_email
		where {$discount_table}.discount_id = %d order by {$discount_table}.id desc limit %d , %d;",
			$id,
			$page_index,
			$limit
		);

		$prepared_total_query = $wpdb->prepare( "SELECT count(*) as total_discounts FROM {$wpdb->prefix}{$sellkit_prefix}applied_discount where discount_id = %d;",
			$id
		);

		$discounts = $wpdb->get_results(
			$prepared_query,
			ARRAY_A );

		$total = $wpdb->get_results(
			$prepared_total_query,
			ARRAY_A );

		$total_discount_num = ! empty( $total[0]['total_discounts'] ) ? $total[0]['total_discounts'] : '';
		// phpcs:enable

		wp_send_json_success( [
			'discounts' => $discounts,
			'total' => $total_discount_num,
			'max_item_num' => self::CUSTOM_TABLE_PER_PAGE,
		] );
	}

	/**
	 * Gets generated coupons.
	 *
	 * @since 1.1.0
	 */
	public function get_generated_coupons() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$page = sellkit_htmlspecialchars( INPUT_GET, 'page' );
		$id   = sellkit_htmlspecialchars( INPUT_GET, 'id' );

		$args = [
			'post_type' => 'shop_coupon',
			'post_status' => 'publish',
			'posts_per_page' => self::CUSTOM_TABLE_PER_PAGE,
			'paged' => sanitize_text_field( $page ),
			'orderby' => 'ID',
			'order' => 'DESC',
			'meta_key'   => 'sellkit_personalised_coupon_rule', // phpcs:ignore
			'meta_value' => sanitize_text_field( $id ), // phpcs:ignore
		];

		$query = new WP_Query( $args );

		$coupons = [];

		foreach ( $query->posts as $post ) {
			$expiry_date    = get_post_meta( $post->ID, 'date_expires', true );
			$customer_email = get_post_meta( $post->ID, 'customer_email', true );
			$usage_limit    = get_post_meta( $post->ID, 'usage_limit', true );
			$usage_count    = get_post_meta( $post->ID, 'usage_count', true );

			$post->customer    = ! empty( $customer_email ) ? $customer_email : '';
			$post->usage_limit = ! empty( $usage_limit ) ? $usage_limit : '';
			$post->usage_count = ! empty( $usage_count ) ? $usage_count : '';
			$post->created_at  = date_i18n( 'Y/m/d h:i A', strtotime( $post->post_date ) );

			if ( ! empty( $expiry_date ) ) {
				$post->expiry_date = round( ( get_post_meta( $post->ID, 'date_expires', true ) - time() ) / 86400 );
			}

			$coupons[] = $post;
		}

		wp_send_json_success( [
			'coupons' => $coupons,
			'max_item_num' => self::CUSTOM_TABLE_PER_PAGE,
			'total' => $query->found_posts,
		] );
	}

	/**
	 * Gets contacts.
	 *
	 * @since 1.5.0
	 */
	public function get_contacts() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$page      = sellkit_htmlspecialchars( INPUT_GET, 'page' );
		$funnel_id = sellkit_htmlspecialchars( INPUT_GET, 'funnel_id' );

		if ( empty( $page ) ) {
			$page = 1;
		}

		$page_index = ( $page - 1 ) * self::CUSTOM_TABLE_PER_PAGE;
		$limit      = self::CUSTOM_TABLE_PER_PAGE;

		global $wpdb;

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$contact_table  = "{$wpdb->prefix}{$sellkit_prefix}funnel_contact";
		$users_table    = "{$wpdb->prefix}users ";

		// phpcs:disable
		$contacts = $wpdb->get_results(
			$wpdb->prepare( "SELECT ct.user_id as user_id, ct.id as id, ct.created_at, ut.user_email, ut.display_name, SUM( ct.total_spent ) as sum_total_spent FROM {$contact_table} AS ct
				LEFT JOIN {$users_table} ut ON ut.ID = ct.user_id
				where ct.funnel_id = %d
				group by ct.user_id
				order by ct.id desc limit %d , %d;", $funnel_id, $page_index, $limit ), ARRAY_A );

		$total_contacts = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(*) as total_contacts FROM {$contact_table} where funnel_id = %d;", $funnel_id ), ARRAY_A );
		$total_contacts_num = ! empty( $total_contacts[0]['total_contacts'] ) ? $total_contacts[0]['total_contacts'] : '';
		// phpcs:enable

		wp_send_json_success( [
			'contacts' => $contacts,
			'total' => intval( $total_contacts_num ),
			'max_item_num' => self::CUSTOM_TABLE_PER_PAGE,
		] );
	}

	/**
	 * Gets contacts.
	 *
	 * @since 1.5.0
	 */
	public function get_history() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$page       = sellkit_htmlspecialchars( INPUT_GET, 'page' );
		$funnel_id  = sellkit_htmlspecialchars( INPUT_GET, 'funnel_id' );
		$contact_id = sellkit_htmlspecialchars( INPUT_GET, 'contact_id' );
		$user_id    = $this->get_user_id_by_contact_table( $contact_id );

		if ( empty( $user_id ) ) {
			wp_send_json_error( esc_html__( 'Something went wrong.', 'sellkit' ) );
		}

		if ( empty( $page ) ) {
			$page = 1;
		}

		$page_index = ( $page - 1 ) * self::CUSTOM_TABLE_PER_PAGE;
		$limit      = self::CUSTOM_TABLE_PER_PAGE;

		global $wpdb;

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$contact_table  = "{$wpdb->prefix}{$sellkit_prefix}funnel_contact";
		$users_table    = "{$wpdb->prefix}users ";

		// phpcs:disable
		$contacts = $wpdb->get_results(
			$wpdb->prepare( "SELECT ct.*, ct.created_at, ut.user_email, ut.display_name, ct.total_spent as sum_total_spent FROM {$contact_table} AS ct
				LEFT JOIN {$users_table} ut ON ut.ID = ct.user_id
				where ct.funnel_id = %d and ct.user_id = %d
				order by ct.id desc limit %d , %d;", $funnel_id, $user_id, $page_index, $limit ), ARRAY_A );

		$total_contacts = $wpdb->get_results(
			$wpdb->prepare( "SELECT count(*) as total_contacts FROM {$contact_table} where funnel_id = %d;", $funnel_id ), ARRAY_A );
		$total_contacts_num = ! empty( $total_contacts[0]['total_contacts'] ) ? $total_contacts[0]['total_contacts'] : '';
		// phpcs:enable

		wp_send_json_success( [
			'contacts' => $contacts,
			'total' => intval( $total_contacts_num ),
			'max_item_num' => self::CUSTOM_TABLE_PER_PAGE,
		] );
	}

	/**
	 * Delete a single contact.
	 *
	 * @since 1.5.0
	 */
	public function delete_contact() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$entry_id = sellkit_htmlspecialchars( INPUT_GET, 'entry_id' );

		global $wpdb;

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$contact_table  = "{$wpdb->prefix}{$sellkit_prefix}funnel_contact";

		// phpcs:disable
		$delete_result = $wpdb->delete( $contact_table, [ 'id' => intval( $entry_id ) ] );
		// phpcs:enable

		if ( ! $delete_result ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * Gets contact details.
	 *
	 * @since 1.5.0
	 */
	public function get_contact_details() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$contact_id = sellkit_htmlspecialchars( INPUT_GET, 'contact_id' );

		global $wpdb;

		$sellkit_prefix       = Database::DATABASE_PREFIX;
		$contact_table        = "{$wpdb->prefix}{$sellkit_prefix}funnel_contact";
		$contact_segmentation = "{$wpdb->prefix}{$sellkit_prefix}contact_segmentation";
		$users_table          = "{$wpdb->prefix}users ";

		// phpcs:disable
		 $contact_details = $wpdb->get_results(
			$wpdb->prepare( "SELECT ct.*, ut.user_email, ut.display_name, cs.* FROM {$contact_table} AS ct
				LEFT JOIN {$users_table} ut ON ut.ID = ct.user_id
				LEFT JOIN {$contact_segmentation} cs ON ut.user_email = cs.email
				WHERE ct.id = %d limit 1;", $contact_id ), ARRAY_A );
		 // phpcs:enable

		if ( empty( $contact_details[0] ) || ! is_array( $contact_details[0] ) ) {
			wp_send_json_error( esc_html__( 'Something went wrong.', 'sellkit' ) );
		}

		$user_id = $contact_details[0]['user_id'];

		wp_send_json_success( [
			'user_details' => $contact_details[0],
			'user_meta'    => array_merge( get_user_meta( $user_id ), [ 'user_role' => get_userdata( $user_id )->roles ] ),
		] );
	}

	/**
	 * Gets funnel orders.
	 *
	 * @since 1.5.0
	 */
	public function get_funnel_orders() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$contact_id = sellkit_htmlspecialchars( INPUT_GET, 'contact_id' );
		$funnel_id  = sellkit_htmlspecialchars( INPUT_GET, 'funnel_id' );
		$page_index = sellkit_htmlspecialchars( INPUT_GET, 'page' );
		$limit      = self::CUSTOM_TABLE_PER_PAGE;
		$user_id    = $this->get_user_id_by_contact_table( $contact_id );

		if ( empty( $user_id ) ) {
			wp_send_json_error( esc_html__( 'Something went wrong.', 'sellkit' ) );
		}

		$contact_orders = $this->get_contact_order_details( $funnel_id, $user_id, $page_index, $limit );

		wp_send_json_success( [
			'user_orders' => $contact_orders['orders'],
			'total' => intval( $contact_orders['total'] ),
			'max_item_num' => self::CUSTOM_TABLE_PER_PAGE,
		] );
	}

	/**
	 * Gets funnel id by contact table.
	 *
	 * @since 1.5.0
	 * @param string $contact_id Contact Id.
	 */
	public function get_user_id_by_contact_table( $contact_id ) {
		global $wpdb;

		$sellkit_prefix = Database::DATABASE_PREFIX;
		$contact_table  = "{$wpdb->prefix}{$sellkit_prefix}funnel_contact";

		// phpcs:disable
		$contact_details = $wpdb->get_results(
			$wpdb->prepare( "SELECT ct.user_id FROM {$contact_table} AS ct
				WHERE ct.id = %d limit 1;", $contact_id ), ARRAY_A );
		// phpcs:enable

		if ( empty( $contact_details[0] ) || ! is_array( $contact_details[0] ) ) {
			return false;
		}

		return $contact_details[0]['user_id'];
	}

	/**
	 * Saves contact details.
	 *
	 * @since 1.5.0
	 */
	public function save_contact_details() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$fields     = sellkit_post( 'fields' );
		$first_name = ! empty( $fields['contacts_detail_first_name'] ) ? sanitize_text_field( $fields['contacts_detail_first_name'] ) : '';
		$last_name  = ! empty( $fields['contacts_detail_last_name'] ) ? sanitize_text_field( $fields['contacts_detail_last_name'] ) : '';
		$user_id    = filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT );

		$result = wp_update_user( [
			'ID' => $user_id,
			'first_name' => $first_name,
			'last_name' => $last_name,
		] );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		wp_send_json_success( esc_html__( 'The details have been updated.', 'sellkit' ) );
	}

	/**
	 * Get order details of a contact ID related to a given funnel ID.
	 *
	 * @param int $funnel_id ID of the funnel.
	 * @param int $user_id Equals user ID of WordPress, customer ID of woocommerce and contact ID of sellkit.
	 * @param int $page Page number.
	 * @param int $limit Page limitation.
	 * @return array Order details with a custom structure which is parsed in frontend.
	 *
	 * @since 1.5.0
	 * @access private
	 */
	private function get_contact_order_details( $funnel_id, $user_id, $page, $limit ) {
		$funnel_orders = [];

		$total_orders = wc_get_orders( [
			'customer_id'   => $user_id,
			'meta_key'      => 'sellkit_funnel_id', // phpcs:ignore
			'meta_value'    => $funnel_id, // phpcs:ignore
			'meta_compare'  => '=',
			'limit'         => -1,
		] );

		$orders = wc_get_orders( [
			'customer_id'   => $user_id,
			'meta_key'      => 'sellkit_funnel_id', // phpcs:ignore
			'meta_value'    => $funnel_id, // phpcs:ignore
			'meta_compare'  => '=',
			'limit'         => $limit,
			'paged'         => $page,
			'return'        => 'objects'
		] );

		foreach ( $orders as $order ) {
			$products = [];

			foreach ( $order->get_items() as $order_item ) {
				$data = $order_item->get_data();

				if ( ! array_key_exists( 'product_id', $data ) ) {
					continue;
				}

				$product = wc_get_product( $data['product_id'] );

				if ( empty( $product ) ) {
					continue;
				}

				$products[] = [
					'id'            => $data['product_id'],
					'name'          => $data['name'],
					'quantity'      => $data['quantity'],
					'order_price'   => floatval( $data['total'] / $data['quantity'] ),
					'regular_price' => floatval( $product->get_regular_price() ),
					'image_src'     => wp_get_attachment_image_src( $product->get_image_id() ),
				];
			}

			$funnel_orders[] = [
				'order_number'  => $order->get_order_number(),
				'order_date'    => $order->get_date_created()->date( 'Y/m/d g:i A' ),
				'order_revenue' => $order->get_total() - $order->get_total_discount() - $order->get_total_tax(),
				'products'      => $products,
			];
		}

		return [
			'orders' => $funnel_orders,
			'total' => count( $total_orders ),
		];
	}
}

Sellkit_Custom_Tables::get_instance();
