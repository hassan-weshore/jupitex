<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Popup_Settings' ) ) {

	/**
	 * Define Jet_Popup_Settings class
	 */
	class Jet_Popup_Settings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * [$key description]
		 * @var string
		 */
		public $key = 'jet-popup-settings';

		/**
		 * [$localize_data description]
		 * @var array
		 */
		public $localize_data = [];

		/**
		 * [$settings description]
		 * @var null
		 */
		public $settings = null;

		/**
		 * Init page
		 */
		public function __construct() {
			add_action( 'admin_menu', [ $this, 'register_page' ], 91 );
			add_action( 'wp_ajax_jet_popup_save_settings', [ $this, 'save_settings' ] );
			add_action( 'wp_ajax_get_mailchimp_user_data', [ $this, 'get_mailchimp_user_data' ] );
			add_action( 'wp_ajax_get_mailchimp_lists', [ $this, 'get_mailchimp_lists' ] );
			add_action( 'wp_ajax_get_mailchimp_list_merge_fields', [ $this, 'get_mailchimp_list_merge_fields' ] );
		}

		/**
		 * Returns post type slug
		 *
		 * @return string
		 */
		public function slug() {
			return jet_popup()->post_type->slug();
		}

		/**
		 * [get description]
		 * @param  [type]  $setting [description]
		 * @param  boolean $default [description]
		 * @return [type]           [description]
		 */
		public function get( $setting, $default = false ) {

			if ( null === $this->settings ) {
				$this->settings = get_option( $this->key, [] );
			}

			return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : $default;
		}


		/**
		 * @return mixed|void
		 */
		public function get_popup_default_settings() {
			return apply_filters( 'jet-popup/settings/default-popup-settings', [
				'jet_popup_type'                 => 'default',
				'jet_popup_animation'            => 'fade',
				'jet_popup_open_trigger'         => 'attach',
				'jet_popup_page_load_delay'      => 1,
				'jet_popup_user_inactivity_time' => 3,
				'jet_popup_scrolled_to_value'    => 10,
				'jet_popup_on_date_value'        => '',
				'jet_popup_on_time_start_value'  => '',
				'jet_popup_on_time_end_value'    => '',
				'jet_popup_custom_selector'      => '',
				'jet_popup_prevent_scrolling'    => false,
				'jet_popup_show_once'            => false,
				'jet_popup_show_again_delay'     => 'none',
				'jet_popup_use_ajax'             => false,
				'jet_popup_force_ajax'           => false,
				'jet_role_condition'             => [],
				'use_close_button'               => true,
				'close_button_icon'              => 'fas fa-times',
				'use_overlay'                    => true,
				'close_on_overlay_click'         => true,
			] );
		}

		/**
		 * @param $settings
		 *
		 * @return array
		 */
		public function merge_with_defaults_settings( $settings = [] ) {
			$popup_settings = [];
			$popup_default_settings = $this->get_popup_default_settings();

			if ( ! empty( $settings ) ) {

				foreach ( $popup_default_settings as $option => $value ) {

					if ( array_key_exists( $option, $settings ) ) {
						$popup_settings[ $option ] = $settings[ $option ];

						$new_setting = 'selected_' . $option;

						if ( array_key_exists( $new_setting, $settings ) ) {
							$popup_settings[ $new_setting ] = $settings[ $new_setting ];
						}

					} else {
						$popup_settings[ $option ] = $value;
					}
				}
			}

			return $popup_settings;
		}

		/**
		 * @param $popup_id
		 *
		 * @return array|mixed|void
		 */
		public function get_popup_settings( $popup_id = false ) {
			$popup_meta_settings = get_post_meta( $popup_id, '_settings', true );

			if ( ! empty( $popup_meta_settings ) ) {
				return $this->merge_with_defaults_settings( $popup_meta_settings );
			}

			$elementor_meta_settings = get_post_meta( $popup_id, '_elementor_page_settings', true );

			if ( ! empty( $elementor_meta_settings ) ) {
				return $this->merge_with_defaults_settings( $elementor_meta_settings );
			}

			return $this->get_popup_default_settings();
		}


		/**
		 * @param $popup_id
		 * @param $settings
		 *
		 * @return void
		 */
		public function update_popup_settings( $popup_id = false, $settings = [] ) {
			$settings = $this->merge_with_defaults_settings( $settings );
			update_post_meta( $popup_id, '_settings', $settings );

			$elementor_meta_settings = get_post_meta( $popup_id, '_elementor_page_settings', true );

			if ( ! empty( $elementor_meta_settings ) ) {
				$elementor_meta_settings = wp_parse_args( $settings, $elementor_meta_settings );
				update_post_meta( $popup_id, '_elementor_page_settings', $elementor_meta_settings );
			}

			return true;

		}

		/**
		 * [get_settings_page description]
		 * @return [type] [description]
		 */
		public function get_settings_page_url() {
			return add_query_arg(
				array(
					'page' => 'jet-dashboard-settings-page',
					'subpage' => 'jet-popup-integrations'
				),
				admin_url( 'admin.php' )
			);
		}

		/**
		 * [generate_localize_data description]
		 * @return [type] [description]
		 */
		public function get_settings_page_config() {

			$mailchimp_api_data = get_option( $this->key . '_mailchimp', [] );

			return [
				'settings' => [
					'apikey' => $this->get( 'apikey', '' ),
				],
				'mailchimpApiData'  => $mailchimp_api_data,
			];
		}

		/**
		 * Register add/edit page
		 *
		 * @return void
		 */
		public function register_page() {
			add_submenu_page(
				'edit.php?post_type=jet-popup',
				__( 'Settings', 'jet-popup' ),
				__( 'Settings', 'jet-popup' ),
				'manage_options',
				add_query_arg(
					array(
						'page' => 'jet-dashboard-settings-page',
						'subpage' => 'jet-popup-integrations'
					),
					admin_url( 'admin.php' )
				)
			);
		}

		/**
		 * [save_settings description]
		 * @return [type] [description]
		 */
		public function save_settings() {

			$data = ( ! empty( $_POST['data'] ) ) ? $_POST['data'] : false;

			if ( ! $data ) {
				wp_send_json( [
					'type' => 'error',
					'title' => __( 'Error', 'jet-popup' ),
					'desc'  => __( 'Server error. Please, try again later', 'jet-popup' ),
				] );
			}

			$current = get_option( $this->key, [] );

			foreach ( $data as $key => $value ) {
				$current[ $key ] = is_array( $value ) ? $value : esc_attr( $value );
			}

			update_option( $this->key, $current );

			wp_send_json( [
				'type'  => 'success',
				'title' => __( 'Success', 'jet-popup' ),
				'desc'  => __( 'Settings have been saved!', 'jet-popup' ),
			] );
		}

		/**
		 * [get_mailchimp_lists description]
		 * @return [type] [description]
		 */
		public function get_mailchimp_user_data() {

			if ( empty( $_POST['apikey'] ) ) {
				wp_send_json( [
					'type' => 'error',
					'title' => __( 'Error', 'jet-popup' ),
					'desc'  => __( 'Server error. Please, try again later', 'jet-popup' ),
				] );
			}

			$api_key = $_POST['apikey'];

			$key_data = explode( '-', $api_key );

			$api_server = sprintf( 'https://%s.api.mailchimp.com/3.0/', $key_data[1] );

			$url = esc_url( trailingslashit( $api_server ) );

			$request = wp_remote_post( $url, [
				'method'      => 'GET',
				'timeout'     => 20,
				'headers'     => [
					'Content-Type'  => 'application/json',
					'Authorization' => 'apikey ' . $api_key
				],
			] );

			if ( is_wp_error( $request ) ) {
				wp_send_json( [
					'type' => 'error',
					'title' => __( 'MailChimp Error', 'jet-popup' ),
					'desc'  => __( 'Server error. Please, check your apikey status or format', 'jet-popup' ),
				] );
			}

			$request = json_decode( wp_remote_retrieve_body( $request ), true );

			$current = get_option( $this->key . '_mailchimp', [] );

			$current[ $api_key ]['account'] = $request;

			update_option( $this->key . '_mailchimp', $current );

			wp_send_json( [
				'type'     => 'success',
				'title'    => __( 'Success', 'jet-popup' ),
				'desc'     => __( 'Account Data were received', 'jet-popup' ),
				'request'  => $request,
			] );
		}

		/**
		 * [get_mailchimp_lists description]
		 * @return [type] [description]
		 */
		public function get_mailchimp_lists() {

			if ( empty( $_POST['apikey'] ) ) {
				wp_send_json( [
					'type' => 'error',
					'title' => __( 'Error', 'jet-popup' ),
					'desc'  => __( 'Server error. Please, try again later', 'jet-popup' ),
				] );
			}

			$api_key = $_POST['apikey'];

			$key_data = explode( '-', $api_key );

			$api_server = sprintf( 'https://%s.api.mailchimp.com/3.0/', $key_data[1] );

			$url = esc_url( trailingslashit( $api_server . 'lists' ) );

			$request = wp_remote_post( $url, [
				'method'      => 'GET',
				'timeout'     => 20,
				'headers'     => [
					'Content-Type'  => 'application/json',
					'Authorization' => 'apikey ' . $api_key
				],
			] );

			if ( is_wp_error( $request ) ) {
				wp_send_json( [
					'type' => 'error',
					'title' => __( 'MailChimp Error', 'jet-popup' ),
					'desc'  => __( 'Server error. Please, check your apikey status or format', 'jet-popup' ),
				] );
			}

			$request = json_decode( wp_remote_retrieve_body( $request ), true );

			$current = get_option( $this->key . '_mailchimp', [] );

			if ( array_key_exists( 'lists', $request ) ) {
				$lists = $request['lists'];
				$temp_lists = [];

				if ( ! empty( $lists ) ) {
					foreach ( $lists as $key => $list_data ) {
						$temp_lists[ $list_data[ 'id' ] ]['info'] = $list_data;
					}

					$current[ $api_key ]['lists'] = $temp_lists;
				}

				update_option( $this->key . '_mailchimp', $current );
			}

			wp_send_json( [
				'type'     => 'success',
				'title'    => __( 'Success', 'jet-popup' ),
				'desc'     => __( 'Lists were received', 'jet-popup' ),
				'request'  => $request,
			] );
		}

		/**
		 * [get_mailchimp_lists description]
		 * @return [type] [description]
		 */
		public function get_mailchimp_list_merge_fields() {

			if ( empty( $_POST['apikey'] ) ) {
				wp_send_json( [
					'type' => 'error',
					'title' => __( 'Error', 'jet-popup' ),
					'desc'  => __( 'Server error. Please, try again later', 'jet-popup' ),
				] );
			}

			$api_key = $_POST['apikey'];

			$key_data = explode( '-', $api_key );

			$list_id = $_POST['listid'];

			$api_server = sprintf( 'https://%s.api.mailchimp.com/3.0/', $key_data[1] );

			$url = esc_url( trailingslashit( $api_server . 'lists/' . $list_id . '/merge-fields' ) );

			$request = wp_remote_post( $url, [
				'method'      => 'GET',
				'timeout'     => 20,
				'headers'     => [
					'Content-Type'  => 'application/json',
					'Authorization' => 'apikey ' . $api_key
				],
			] );

			if ( is_wp_error( $request ) ) {
				wp_send_json( [
					'type' => 'error',
					'title' => __( 'MailChimp Error', 'jet-popup' ),
					'desc'  => __( 'Server error. Please, check your apikey status or format', 'jet-popup' ),
				] );
			}

			$request = json_decode( wp_remote_retrieve_body( $request ), true );

			$current = get_option( $this->key . '_mailchimp', [] );

			if ( array_key_exists( 'merge_fields', $request ) ) {
				$current[ $api_key ]['lists'][ $list_id ]['merge_fields'] = $request['merge_fields'];
				update_option( $this->key . '_mailchimp', $current );
			}

			wp_send_json( [
				'type'     => 'success',
				'title'    => __( 'Success', 'jet-popup' ),
				'desc'     => __( 'Merge Fields were received', 'jet-popup' ),
				'request'  => $request,
			] );
		}

		/**
		 * [get_user_lists description]
		 * @return [type] [description]
		 */
		public function get_user_lists() {
			$current = get_option( jet_popup()->settings->key . '_mailchimp', [] );

			$current_api = $this->get( 'apikey', '' );

			if ( empty( $current_api ) || ! array_key_exists( $current_api, $current ) ) {
				return false;
			}

			$apikey_data = $current[ $current_api ];

			if ( ! array_key_exists( 'lists', $apikey_data ) ) {
				return false;
			}

			$lists = $apikey_data['lists'];

			return $lists;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}
