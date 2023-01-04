<?php

namespace Sellkit\Contact_Segmentation;

use Sellkit\Contact_Segmentation\libraries\Mobile_Detect;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Class Contact Data Updater.
 *
 * @package Sellkit\Contact_Segmentation
 * @SuppressWarnings(ExcessiveClassComplexity)
 * @since 1.1.0
 */
class Contact_Data_Updater {

	const SELLKIT_CONTACT_SEGMENTATION_LOCATION_API = 'sjYudEYJDjBMDDoKyxHd'; // TODO we should get it dynamically.
	const SELLKIT_CONTACT_SEGMENTATION_FORCE_UPDATE = 'sellkit_contact_segmentation_force_update';

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Contact_Data_Updater
	 */
	private static $instance = null;

	/**
	 * Contact data.
	 *
	 * @since 1.1.0
	 * @var array Contact data.
	 */
	public $data;

	/**
	 * New contact data.
	 *
	 * @since 1.1.0
	 * @var array Contact data.
	 */
	public static $new_data = [];

	/**
	 * Check if data is expired or not.
	 *
	 * @since 1.1.0
	 * @var boolean Is data expired.
	 */
	public $is_expired = false;

	/**
	 * Contact_Data constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		if ( wp_doing_ajax() ) {
			return;
		}

		$this->data = Contact_Data::$historical_data;

		if ( ! is_user_logged_in() ) {
			$this->update_browser_language();
			$this->maybe_update_device();
			$this->update_utm_data();
			$this->maybe_update_ip();
			$this->maybe_update_locations();
			$this->update_user_type();
			$this->update_url_query_string();

			add_action( 'wp', [ $this, 'update_viewed_details' ], 10 );
			add_action( 'wp', [ $this, 'maybe_update_data' ], 9999 );
			return;
		}

		$this->update_utm_data();
		$this->update_url_query_string();
		$this->update_browser_language();

		add_action( 'wp', [ $this, 'update_viewed_details' ], 10 );

		$force_update = get_user_meta( get_current_user_id(), self::SELLKIT_CONTACT_SEGMENTATION_FORCE_UPDATE, true );

		if ( empty( $this->data ) ) {
			$force_update = true;
		}

		$this->maybe_update_ip();
		$this->maybe_update_locations();
		$this->maybe_update_device();

		if ( true == $force_update ) { //phpcs:ignore
			$this->update_order_info();
		}

		add_action( 'wp', [ $this, 'maybe_update_data' ], 9999 );
	}

	/**
	 * If updating is required start to update.
	 *
	 * @since 1.1.0
	 */
	public function maybe_update_data() {
		if ( empty( self::$new_data ) ) {
			return;
		}

		if ( ! is_user_logged_in() && is_array( self::$new_data ) ) {
			setcookie( 'sellkit_contact_segmentation', wp_json_encode( array_merge( Contact_Data::$historical_data, self::$new_data ) ), 2147483647, '/' );
			return;
		}

		if ( empty( Contact_Data::$historical_data ) && ! empty( self::$new_data ) ) {
			self::$new_data['email'] = Contact_Data::get_user_email();

			sellkit()->db->insert( 'contact_segmentation', self::$new_data );

			update_user_meta( get_current_user_id(), self::SELLKIT_CONTACT_SEGMENTATION_FORCE_UPDATE, false );

			return;
		}

		self::update_contact_segmentation( self::$new_data, Contact_Data::get_user_email() );
	}

	/**
	 * Get the class instance.
	 *
	 * @since 1.1.0
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Update utm data.
	 *
	 * @since 1.1.0
	 */
	private function update_utm_data() {
		$utm_source   = $this->check_utm_data( 'utm_source' );
		$utm_campaign = $this->check_utm_data( 'utm_campaign' );
		$utm_medium   = $this->check_utm_data( 'utm_medium' );
		$utm_content  = $this->check_utm_data( 'utm_content' );
		$utm_term     = $this->check_utm_data( 'utm_term' );

		if ( $this->check_utm_is_new( $utm_source, 'utm_source' ) ) {
			self::$new_data['utm_source'] = $utm_source;
		}

		if ( $this->check_utm_is_new( $utm_campaign, 'utm_campaign' ) ) {
			self::$new_data['utm_campaign'] = $utm_campaign;
		}

		if ( $this->check_utm_is_new( $utm_content, 'utm_content' ) ) {
			self::$new_data['utm_content'] = $utm_content;
		}

		if ( $this->check_utm_is_new( $utm_term, 'utm_term' ) ) {
			self::$new_data['utm_term'] = $utm_term;
		}

		if ( $this->check_utm_is_new( $utm_medium, 'utm_medium' ) ) {
			self::$new_data['utm_medium'] = $utm_medium;
		}
	}

	/**
	 * Check utm data.
	 *
	 * @param string $utm Utm name.
	 * @since 1.2.8
	 * @return string|null
	 */
	private function check_utm_data( $utm ) {
		$value = sellkit_htmlspecialchars( INPUT_GET, $utm );

		if ( ! empty( $value ) ) {
			return $value;
		}

		$data = array_change_key_case( $_GET, CASE_LOWER ); //phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification

		if ( empty( $data[ $utm ] ) ) {
			return $value;
		}

		return sanitize_text_field( $data[ $utm ] );
	}

	/**
	 * Update locations data from API.
	 *
	 * @since 1.1.0
	 */
	private function maybe_update_locations() {
		$ip = sellkit_get_ip();

		if ( ! empty( $this->data['ip'] ) && $ip === $this->data['ip'] ) {
			return;
		}

		$api_key = sellkit_get_option( 'geolocation_api_key' );

		if ( empty( $api_key ) ) {
			return;
		}

		$response = wp_remote_get( 'https://location.stg.growmatik.ai/v1', [
			'timeout' => 10,
			'body' => [
				'apiKey' => $api_key,
				'ip' => $ip,
			],
		] );

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) && 200 === (int) $response_code ) {
			$location_data                     = json_decode( $response['body'] );
			self::$new_data['visitor_country'] = strtolower( $location_data->country );
			self::$new_data['visitor_city']    = strtolower( $location_data->city );
		}

		self::$new_data['updated_at'] = time();
	}

	/**
	 * Update user type.
	 *
	 * @since 1.2.3
	 */
	private function update_user_type() {
		if ( empty( $this->data['user_type'] ) ) {
			self::$new_data['user_type'] = 'first_time_visitor';
		}

		if ( ! empty( $this->data['user_type'] ) ) {
			self::$new_data['user_type'] = 'returning_visitor';
		}
	}

	/**
	 * Updates device types.
	 *
	 * @since 1.1.0
	 */
	private function maybe_update_device() {
		$detector        = new Mobile_Detect();
		$current_display = 'desktop';

		if ( $detector->isMobile() ) {
			$current_display = 'mobile';
		}

		if ( $detector->isTablet() ) {
			$current_display = 'tablet';
		}

		if ( ! empty( $this->data['user_device'] ) && $this->data['user_device'] === $current_display ) {
			return;
		}

		self::$new_data['user_device'] = $current_display;
	}

	/**
	 * Update browser language.
	 *
	 * @since 1.1.0
	 */
	private function update_browser_language() {
		$data = [];

		if ( ! empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			$data = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ); // phpcs:ignore
		}

		$language = ! empty( $data[0] ) ? $data[0] : '';

		if ( empty( $language ) ) {
			return;
		}

		if ( empty( $this->data['browser_language'] ) || ( $this->data['browser_language'] !== $language ) ) {
			self::$new_data['browser_language'] = $language;
		}
	}

	/**
	 * Updates the IP if it's changed.
	 *
	 * @since 1.2.1
	 */
	private function maybe_update_ip() {
		$ip = sellkit_get_ip();

		if ( ! empty( $this->data['ip'] ) && $this->data['ip'] === $ip ) {
			return;
		}

		self::$new_data['ip'] = $ip;
	}

	/**
	 * Checks utm value.
	 *
	 * @param string $new_utm New Utm.
	 * @param string $type Utm type.
	 * @return bool
	 */
	private function check_utm_is_new( $new_utm, $type ) {
		if ( empty( $new_utm ) ) {
			return false;
		}

		if ( empty( $this->data[ $type ] ) ) {
			return true;
		}

		if ( $new_utm !== $this->data[ $type ] ) {
			return true;
		}
	}

	/**
	 * Updates user information.
	 *
	 * @since 1.1.0
	 */
	public function update_order_info() {
		$current_user = wp_get_current_user();

		$args = [
			'customer_id' => $current_user->ID,
			'post_status' => 'completed',
			'post_type' => 'shop_order',
			'limit' => -1,
			'orderby' => 'id',
			'order' => 'DESC',
		];

		if ( ! sellkit()->has_valid_dependencies() ) {
			return;
		}

		$orders               = wc_get_orders( $args );
		$order_number         = count( $orders );
		$total_spent          = 0;
		$first_order          = ! empty( end( $orders ) ) ? end( $orders ) : '';
		$last_order           = ! empty( $orders[0] ) ? $orders[0] : '';
		$purchased_products   = [];
		$purchased_categories = [];
		$billing_countries    = [];
		$billing_cities       = [];
		$shipping_countries   = [];
		$shipping_cities      = [];

		foreach ( $orders as $order ) {
			$total_spent         += $order->get_total();
			$billing_countries[]  = $order->get_billing_country();
			$billing_cities[]     = $order->get_billing_city();
			$shipping_countries[] = $order->get_shipping_country();
			$shipping_cities[]    = $order->get_shipping_city();

			foreach ( $order->get_items() as $item ) {
				$purchased_products[] = $item['product_id'];

				$term_list            = wp_get_post_terms( $item['product_id'], 'product_cat', [ 'fields' => 'ids' ] );
				$purchased_categories = array_merge( $purchased_categories, $term_list );
			}
		}

		$first_order_date = $first_order ? strtotime( $first_order->get_date_completed()->date( 'Y-m-d H:i:s' ) ) : '';
		$last_order_date  = $last_order ? strtotime( $last_order->get_date_completed()->date( 'Y-m-d H:i:s' ) ) : '';

		self::$new_data['total_order_count']  = $order_number;
		self::$new_data['total_spent']        = $total_spent;
		self::$new_data['first_order_date']   = $first_order_date;
		self::$new_data['last_order_date']    = $last_order_date;
		self::$new_data['purchased_product']  = array_unique( $purchased_products );
		self::$new_data['billing_country']    = array_unique( $billing_countries );
		self::$new_data['billing_city']       = array_unique( $billing_cities );
		self::$new_data['shipping_country']   = array_unique( $shipping_countries );
		self::$new_data['shipping_city']      = array_unique( $shipping_cities );
		self::$new_data['purchased_category'] = array_unique( $purchased_categories );

		if ( $order_number > 0 ) {
			self::$new_data['user_type'] = 'customer';
			return;
		}

		self::$new_data['user_type'] = 'lead';
	}

	/**
	 * Force update data.
	 *
	 * @since 1.1.0
	 * @param string $order_id Order id.
	 */
	public static function force_update_data( $order_id ) {
		$user_id = get_post_meta( $order_id, '_customer_user', true );

		if ( empty( $user_id ) ) {
			$order   = wc_get_order( $order_id );
			$user    = $order->get_billing_email();
			$user_id = email_exists( $user );
		}

		if ( ! empty( $user_id ) ) {
			update_user_meta( $user_id, self::SELLKIT_CONTACT_SEGMENTATION_FORCE_UPDATE, true );
		}
	}

	/**
	 * Update url query string.
	 *
	 * @since 1.2.3
	 */
	public function update_url_query_string() {
		$old_vars = (array) Contact_Data::$historical_data;

		$invalid_vars = [
			'doing_wp_cron',
			'customize_changeset_uuid',
			'customize_theme',
			'customize_messenger_channel',
			'elementor_library',
			'elementor-preview',
			'ver',
			'jupiterx-layout-builder-type',
			'jupiterx-layout-builder-preview',
			'preview-id',
		];

		$valid_vars = [];
		$new_vars   = [];

		$query_vars = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification

		if ( ! isset( $old_vars['url_query_string'] ) ) {
			$old_vars['url_query_string'] = [];
		}

		foreach ( $query_vars as $key => $value ) {
			if ( is_object( $old_vars['url_query_string'] ) ) {
				$old_vars['url_query_string'] = (array) $old_vars['url_query_string'];
			}

			if (
				in_array( $key, $invalid_vars, true ) ||
				in_array( $value, $old_vars['url_query_string'], true )
			) {
				continue;
			}

			$valid_vars[] = sanitize_text_field( $key );
		}

		if ( ! empty( $old_vars['url_query_string'] ) ) {
			$new_vars = $old_vars['url_query_string'];
		}

		if ( ! empty( $valid_vars ) ) {
			self::$new_data['url_query_string'] = array_unique( array_merge( $valid_vars, $new_vars ) );
		}
	}

	/**
	 * Update viewed products. it should be done in wp hook so we have to update the data directly.
	 * it would be better to update these data with the main updating process.
	 *
	 * @since 1.1.0
	 */
	public function update_viewed_details() {
		$old_data = Contact_Data::$historical_data;

		if ( is_singular( 'product' ) ) {
			$product_id   = get_the_ID();
			$new_products = [];

			if ( ! empty( $old_data['viewed_product'] ) ) {
				$new_products = $old_data['viewed_product'];
			}

			if ( empty( $old_data['viewed_product'] ) || ! in_array( $product_id, $old_data['viewed_product'], false ) ) { // phpcs:ignore
				self::$new_data['viewed_product'] = array_unique( array_merge( [ $product_id ], $new_products ) );
			}
		}

		$queries = get_queried_object();
		if ( ! empty( $queries->taxonomy ) && 'product_cat' === $queries->taxonomy ) {
			$current_cat_id = $queries->term_id;
			$new_cats       = [];

			if ( ! empty( $old_data['viewed_category'] ) ) {
				$new_cats = $old_data['viewed_category'];
			}

			if ( empty( $old_data['viewed_category'] ) || ! in_array( $current_cat_id, $old_data['viewed_category'], false ) ) { // phpcs:ignore
				self::$new_data['viewed_category'] = array_unique( array_merge( [ $current_cat_id ], $new_cats ) );
			}
		}
	}

	/**
	 * Updates contact segmentation data.
	 *
	 * @since 1.1.0
	 * @param array  $data Data.
	 * @param string $email Email.
	 */
	public static function update_contact_segmentation( $data, $email ) {
		sellkit()->db->update( 'contact_segmentation', $data, [ 'email' => $email ] );

		update_user_meta( get_current_user_id(), self::SELLKIT_CONTACT_SEGMENTATION_FORCE_UPDATE, false );
	}
}
