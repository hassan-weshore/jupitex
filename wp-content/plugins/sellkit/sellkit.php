<?php
/**
 * Plugin Name: Sellkit
 * Plugin URI: https://getsellkit.com/
 * Description: Build unlimited sales funnels, one-click order bumps and upsells, custom thank you pages, and more. The free version of SellKit also offers a huge round of features to optimize your WooCommerce store: build and style single or multi-step checkout pages with advanced styling options. Add, remove & reorder form fields. Fasten the form submission process with pre-populated form data, Instant form validation, and removing cart page. All this and more is 100% free and for an unlimited number of sites.
 * Version: 1.5.4
 * Author: Artbees
 * Author URI: https://artbees.net
 * Text Domain: sellkit
 * License: GPL2
 *
 * @package Sellkit
 */

use Sellkit\Admin\Funnel\Funnel;
use Sellkit\Admin\Settings\Sellkit_Admin_Settings;
use Sellkit\Contact_Segmentation\Conditions;
use Sellkit\Contact_Segmentation\Operators;
use Sellkit\Core\Install;
use Sellkit\Database;
use Sellkit\Product_Meta_Key\Sellkit_Product_Meta_Key;

defined( 'ABSPATH' ) || die();

/**
 * Check If sellkit Class exists.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
if ( ! class_exists( 'Sellkit' ) ) {

	/**
	 * SellKit class.
	 *
	 * @since 1.1.0
	 */
	class Sellkit {

		/**
		 * Class instance.
		 *
		 * @since 1.1.0
		 * @var Sellkit
		 */
		private static $instance = null;

		/**
		 * The plugin version number.
		 *
		 * @since 1.1.0
		 *
		 * @access private
		 * @var string
		 */
		private static $version;

		/**
		 * The plugin basename.
		 *
		 * @since 1.1.0
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_basename;

		/**
		 * The plugin name.
		 *
		 * @since 1.1.0
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_name;

		/**
		 * The plugin directory.
		 *
		 * @since 1.1.0
		 *
		 * @access private
		 * @var string
		 */
		public static $plugin_dir;

		/**
		 * The plugin URL.
		 *
		 * @since 1.1.0
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_url;

		/**
		 * The plugin assets URL.
		 *
		 * @since 1.1.0
		 * @access public
		 *
		 * @var string
		 */
		public static $plugin_assets_url;

		/**
		 * Database object.
		 *
		 * @since 1.1.0
		 * @access public
		 * @var Database
		 */
		public $db;

		/**
		 * Sellkit menus.
		 *
		 * @since 1.1.0
		 * @var array Sellkit menus.
		 */
		public $sellkit_menus = [
			'sellkit-dashboard',
			'sellkit-funnel',
			'sellkit-settings',
		];

		/**
		 * Has pro plugin or not.
		 *
		 * @since 1.1.0
		 * @var array Checks if has pro or not.
		 */
		public $has_pro = false;

		/**
		 * Get a class instance.
		 *
		 * @since 1.1.0
		 *
		 * @return Sellkit Class
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
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
			$this->define_constants();

			$this->load_files( [
				'db',
				'functions',
				'funnel/analytics/data-updater',
				'funnel/class',
				'funnel/contacts/base-contacts',
				'funnel/steps',
			] );

			$this->db = new Database();

			add_action( 'plugins_loaded', [ $this, 'check_if_has_sellkit_pro' ], 11 );
			add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
			add_action( 'init', [ $this, 'init' ] );
			add_action( 'admin_init', [ $this, 'admin_init' ] );
			add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
			add_action( 'admin_menu', [ $this, 'register_admin_settings_menu' ], 12 );

			// Editor script.
			add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_script' ] );

			add_action( 'requests-curl.before_request', [ $this, 'actions_before_curl_requests' ], 9999 );

			// Register activation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );

			if ( ! $this->is_sellkit_screen() ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
			add_action( 'admin_head', [ $this, 'remove_notice_for_sellkit_pages' ] );
			add_filter( 'admin_body_class', [ $this, 'sellkit_admin_body_class' ] );
		}

		/**
		 * Actions before all curl requests.
		 *
		 * @since 1.5.4
		 * @return void
		 */
		public function actions_before_curl_requests() {
			session_write_close();
		}

		/**
		 * Has sellKit pro.
		 *
		 * @since 1.1.0
		 */
		public function check_if_has_sellkit_pro() {
			if ( class_exists( 'Sellkit_Pro' ) ) {
				$this->has_pro = true;
			}
		}

		/**
		 * Defines constants used by the plugin.
		 *
		 * @since 1.1.0
		 */
		protected function define_constants() {
			$plugin_data = get_file_data( __FILE__, array( 'Plugin Name', 'Version' ), 'sellkit' );

			self::$plugin_basename   = plugin_basename( __FILE__ );
			self::$plugin_name       = array_shift( $plugin_data );
			self::$version           = array_shift( $plugin_data );
			self::$plugin_dir        = trailingslashit( plugin_dir_path( __FILE__ ) );
			self::$plugin_url        = trailingslashit( plugin_dir_url( __FILE__ ) );
			self::$plugin_assets_url = trailingslashit( self::$plugin_url . 'assets' );
		}

		/**
		 * Plugins loaded.
		 *
		 * @since 1.1.0
		 */
		public function plugins_loaded() {
			$this->load_files( [
				'elementor/class',
				'admin/class-promote-sellkit-pro',
			] );
		}

		/**
		 * Do some stuff on plugin activation.
		 *
		 * @since  NEXT
		 * @return void
		 */
		public function activation() {
			$this->load_files( [
				'core/libraries/wp-async-request',
				'core/libraries/wp-background-process',
				'core/install',
			] );

			Install::check_database_tables();

			if ( empty( sellkit_get_option( 'current_db_version' ) ) ) {
				sellkit_update_option( 'current_db_version', '1.2.7' ); // @since 1.5.0
			}

			if ( ! wp_next_scheduled( 'sellkit_update_rfm_score' ) ) {
				wp_schedule_event( time(), 'twicedaily', 'sellkit_update_rfm_score' );
			}
		}

		/**
		 * Adding sellkit class to body.
		 *
		 * @param string $classes Sellkit the sellkit class.
		 * @return string
		 */
		public function sellkit_admin_body_class( $classes ) {
			return "{$classes} sellkit";
		}

		/**
		 * Initialize admin.
		 *
		 * @since 1.1.0
		 */
		public function admin_init() {
			$this->load_files( [
				'admin/class-notices',
				'admin/class',
			] );

			$this->load_files( [
				'admin/utilities',
				'admin/class-components',
				'admin/components/class-list-table',
				'admin/components/class-filters',
				'admin/components/class-analytics',
				'admin/class-posts',
				'admin/settings/class-settings',
				'admin/class-custom-tables',
				'admin/funnel/class',
				'core/install',
			] );
		}

		/**
		 * Initialize.
		 *
		 * @since 1.1.0
		 */
		public function init() {
			$this->load_files( [
				'admin/class-steps',
				'utilities/functions',
				'contact-segmentation/class',
				'dynamic-keywords/class',
				'core/libraries/wp-async-request',
				'core/libraries/wp-background-process',
				'settings/class',
				'contact-segmentation/rfm/class',
				'admin/settings/integration/class',
			] );

			// Register post types.
			$post_types = [
				// 'sellkit-analytics',
				'sellkit-funnels',
			];

			foreach ( $post_types as $post_type ) {
				register_post_type( $post_type, [
					'public' => false,
				] );
			}

			load_plugin_textdomain( 'sellkit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Enqueue editor scripts.
		 *
		 * @since 1.1.0
		 * @return void
		 */
		public function enqueue_editor_script() {
			wp_enqueue_script(
				'sellkit-editor',
				sellkit()->plugin_url() . 'assets/dist/js/editor.min.js',
				[ 'jquery' ],
				self::$version,
				true
			);
		}

		/**
		 * Enqueue admin scripts.
		 *
		 * @since 1.1.0
		 */
		public function enqueue_admin_scripts() {
			$page = ! empty( $_GET['page'] ) ? $_GET['page'] : ''; // phpcs:ignore

			if ( ! in_array( $page, $this->sellkit_menus, true ) ) {
				return false;
			}

			wp_enqueue_editor();

			wp_enqueue_script(
				'sellkit',
				sellkit()->plugin_url() . 'assets/dist/admin/sellkit.js',
				[ 'lodash', 'wp-element', 'wp-i18n', 'wp-util' ],
				sellkit()->version(),
				true
			);

			wp_localize_script(
				'sellkit',
				'sellkit',
				$this->get_localize_data()
			);

			wp_enqueue_style(
				'sellkit',
				sellkit()->plugin_url() . 'assets/dist/admin/sellkit.css',
				[],
				sellkit()->version()
			);

			wp_set_script_translations( 'sellkit', 'sellkit', sellkit()->plugin_dir() . 'languages' );
		}

		/**
		 * Register admin menu.
		 *
		 * @since 1.1.0
		 * @SuppressWarnings(PHPMD.NPathComplexity)
		 */
		public function register_admin_menu() {
			$menu_name    = esc_html__( 'Sellkit', 'sellkit' );
			$initial_page = 'sellkit-dashboard';
			$submenu      = [
				'sellkit-dashboard' => esc_html__( 'Dashboard', 'sellkit' ),
				'sellkit-funnel' => esc_html__( 'Funnels', 'sellkit' ),
			];

			$submenu = apply_filters( 'sellkit_sub_menu', $submenu );

			$svg_file = wp_remote_get( self::plugin_url() . 'assets/img/icons/sellkit-dashboard.svg' );

			add_menu_page(
				$menu_name,
				$menu_name,
				'manage_options',
				$initial_page,
				[ $this, 'register_admin_menu_callback' ],
				'data:image/svg+xml;base64,' . base64_encode( wp_remote_retrieve_body( $svg_file ) ),
				'58.1'
			);

			foreach ( $submenu as $slug => $title ) {
				add_submenu_page(
					$initial_page,
					"{$menu_name} {$title}",
					$title,
					'edit_theme_options',
					$slug,
					[ $this, 'register_admin_menu_callback' ]
				);
			}
		}

		/**
		 * Adds settings menu.
		 *
		 * @since 1.1.0
		 */
		public function register_admin_settings_menu() {
			add_submenu_page(
				'sellkit-dashboard',
				esc_html__( 'Sellkit', 'sellkit' ) . ' ' . esc_html__( 'Settings', 'sellkit' ),
				esc_html__( 'Settings', 'sellkit' ),
				'edit_theme_options',
				'sellkit-settings',
				[ $this, 'register_admin_menu_callback' ]
			);
		}

		/**
		 * Register admin menu callback.
		 *
		 * @since 1.1.0
		 */
		public function register_admin_menu_callback() {
			?>
			<div id="wrap" class="wrap">
				<!-- It's required for notices, otherwise WP adds the notices wherever it finds the first heading element. -->
				<h1></h1>
				<div id="sellkit-root"></div>
			</div>
			<?php
		}

		/**
		 * Check if in SellKit pages.
		 *
		 * @since 1.1.0
		 *
		 * @return boolean SellKit screen.
		 */
		private function is_sellkit_screen() {
			$page = sellkit_htmlspecialchars( INPUT_GET, 'page' );

			return (
				is_admin() &&
				isset( $page ) &&
				strpos( $page, 'sellkit' ) !== false
			);
		}

		/**
		 * Get localize data.
		 *
		 * @since 1.1.0
		 *
		 * @return array
		 */
		public function get_localize_data() {
			return [
				'nonce' => wp_create_nonce( 'sellkit' ),
				'assetsUrl' => self::$plugin_assets_url,
				'dynamicKeywords' => Sellkit_Dynamic_Keywords::$keywords['contact_segmentation'],
				'defaultFunnelSteps' => Sellkit_Admin_Steps::get_default_funnel_steps(),
				'woocommerceSettings' => $this->get_woocommerce_settings_data(),
				'contactSegmentation' => [
					'conditionsOperators' => Operators::$condition_operator_names,
					'operators' => Operators::$names,
					'conditionsData' => Conditions::$data,
					'browserLanguages' => Conditions\Browser_Language::browser_languages_array(),
				],
				'adminUrl' => admin_url(),
				'url' => site_url(),
				'timezones' => timezone_identifiers_list(),
				'defaultSettings' => [
					'defaultTimezone' => get_option( 'timezone_string' ),
				],
				'elementorActivation' => class_exists( 'Elementor\Plugin' ) ? true : false,
				'funnelsTemplateSource' => Funnel::SELLKIT_FUNNELS_TEMPLATE_SOURCE,
				'removedContentBoxes' => Sellkit_Admin_Settings::get_removed_content_box(),
				'sellkitProIsActive' => $this->has_pro,
				'wcIsActivated' => $this->has_valid_dependencies(),
			];
		}

		/**
		 * Get woocommerce settings data.
		 *
		 * @since 1.1.0
		 *
		 * @return array|boolean
		 */
		public function get_woocommerce_settings_data() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return false;
			}

			return [
				'currencyFormatNumDecimals' => wc_get_price_decimals(),
				'currencySymbol' => html_entity_decode( get_woocommerce_currency_symbol() ),
				'currencyFormatDecimalSep' => wc_get_price_decimal_separator(),
				'currencyFormatThousandSep' => wc_get_price_thousand_separator(),
				'currencyPosition' => get_option( 'woocommerce_currency_pos' ),
				'productDefaultThumbnail' => wc_placeholder_img_src(),
			];
		}

		/**
		 * Remove notices.
		 *
		 * @since 1.1.0
		 */
		public function remove_notice_for_sellkit_pages() {
			remove_all_actions( 'admin_notices' );
		}

		/**
		 * Sellkit dependencies.
		 *
		 * @since 1.1.0
		 */
		public function has_valid_dependencies() {
			if ( ! class_exists( 'woocommerce' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Loads specified PHP files from the plugin includes directory.
		 *
		 * @since 1.1.0
		 *
		 * @param array $file_names The names of the files to be loaded in the includes directory.
		 */
		public function load_files( $file_names = array() ) {

			foreach ( $file_names as $file_name ) {
				$path = self::plugin_dir() . 'includes/' . $file_name . '.php';

				if ( file_exists( $path ) ) {
					require_once $path;
				}
			}
		}

		/**
		 * Returns the version number of the plugin.
		 *
		 * @since 1.1.0
		 *
		 * @return string
		 */
		public function version() {
			return self::$version;
		}

		/**
		 * Returns the plugin basename.
		 *
		 * @since 1.1.0
		 *
		 * @return string
		 */
		public function plugin_basename() {
			return self::$plugin_basename;
		}

		/**
		 * Returns the plugin name.
		 *
		 * @since 1.1.0
		 *
		 * @return string
		 */
		public function plugin_name() {
			return self::$plugin_name;
		}

		/**
		 * Returns the plugin directory.
		 *
		 * @since 1.1.0
		 *
		 * @return string
		 */
		public function plugin_dir() {
			return self::$plugin_dir;
		}

		/**
		 * Returns the plugin URL.
		 *
		 * @since 1.1.0
		 *
		 * @return string
		 */
		public function plugin_url() {
			return self::$plugin_url;
		}

		/**
		 * Returns the plugin assets URL.
		 *
		 * @since 1.1.0
		 *
		 * @return string
		 */
		public function plugin_assets_url() {
			return self::$plugin_assets_url;
		}
	}
}

if ( ! function_exists( 'sellkit' ) ) {
	/**
	 * Initialize the Sellkit.
	 *
	 * @since 1.1.0
	 */
	function sellkit() {
		return Sellkit::get_instance();
	}
}

/**
 * Initialize the Sellkit application.
 *
 * @since 1.1.0
 */
sellkit();
