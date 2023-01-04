<?php
/**
 * Plugin Name: Sellkit Pro
 * Plugin URI: https://artbees.net
 * Description: Upgrade your WooCommerce shopper experience with professional checkout templates. advanced product filters and product variation swatches. Introduce new heights of simplicity and dramatically fasten the checkout process with express checkout, pre-filled and auto-populated forms, auto-apply coupons, address auto-complete, sign up with a checkbox, and more. SellKit Pro provides advanced customer segmentation based on in-site activity, RFM values, geolocation, referral source and more so you can hyper-target your customers with dynamic discounts, personalised coupons, effective checkout alerts, and drastically boost engagement and loyalty. You can measure your success with advanced analytics not only for critical sales metrics but also funnel engagement, promotions, and more and enjoy standard support from Artbees experts.
 * Version: 1.5.1
 * Author: Artbees
 * Author URI: https://artbees.net
 * Text Domain: sellkit-pro
 * License: GPL2
 *
 * @package Sellkit
 */

use Sellkit\Admin\Funnel\Funnel;
use Sellkit\Admin\Settings\Sellkit_Admin_Settings;
use Sellkit\Contact_Segmentation\Conditions;
use Sellkit\Contact_Segmentation\Operators;
use Sellkit\Database;
use Sellkit_Pro\Core\Install;

defined( 'ABSPATH' ) || die();

/**
 * Check If sellkit pro Class exists.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
if ( ! class_exists( 'Sellkit_Pro' ) ) {

	/**
	 * SellKit class.
	 *
	 * @since 1.1.0
	 */
	class Sellkit_Pro {

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
		 * Sellkit Pro Menus.
		 *
		 * @since 1.1.0
		 * @var string[]
		 */
		public $sellkit_pro_menus = [
			'sellkit-alert',
			'sellkit-discount',
			'sellkit-coupon',
		];

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
			] );

			add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
			add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 11 );
			add_action( 'init', [ $this, 'init' ] );
			add_action( 'admin_init', [ $this, 'admin_init' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'frontend_enqueue_scripts' ], 10 );

			register_activation_hook( __FILE__, array( $this, 'activation' ) );

			if ( ! $this->is_sellkit_screen() ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
			add_action( 'admin_head', [ $this, 'remove_notice_for_sellkit_pages' ] );
			add_filter( 'admin_body_class', [ $this, 'sellkit_admin_body_class' ] );
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

			define( 'SELLKIT_PRO_VERSION', self::$version );
			define( 'SELLKIT_PRO_FILE_PATH', __FILE__ );
			define( 'SELLKIT_PRO_SLUG', self::$plugin_dir );
			define( 'SELLKIT_PRO_BASENAME', self::$plugin_basename );
		}

		/**
		 * Plugins loaded.
		 *
		 * @since 1.1.0
		 */
		public function plugins_loaded() {
			if ( ! class_exists( 'Sellkit' ) ) {
				return false;
			}

			$this->load_files( [
				'elementor/class',
			] );

			if ( ! class_exists( 'woocommerce' ) ) {
				return false;
			}

			$this->load_files( [
				'attribute-swatches/includes/frontend/class-attributes-frontend',
				'attribute-swatches/includes/frontend/class-attributes-loop',
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

			sellkit_update_option( 'pro_current_db_version', '1.0.0' );
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
			if ( ! class_exists( 'Sellkit' ) ) {
				$this->load_files( [
					'admin/class-notices',
				] );
			}

			if ( ! $this->has_valid_dependencies() ) {
				return false;
			}

			$this->load_files( [
				'core/install',
			] );
		}

		/**
		 * Initialize.
		 *
		 * @since 1.1.0
		 */
		public function init() {
			if ( ! class_exists( 'Sellkit' ) ) {
				return;
			}

			$this->load_files( [
				'discounts/class',
				'coupons/class',
				'alerts/class',
				'attribute-swatches/class',
				'admin/license/sellkit-pro',
				'contact-segmentation/class',
				'core/libraries/wp-async-request',
				'core/libraries/wp-background-process',
			] );

			// Register post types.
			$post_types = [
				'sellkit-discount',
				'sellkit-coupon',
				'sellkit-alert',
			];

			foreach ( $post_types as $post_type ) {
				register_post_type( $post_type, [
					'public' => false,
				] );
			}

			load_plugin_textdomain( 'sellkit-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Enqueue admin scripts.
		 *
		 * @since 1.1.0
		 */
		public function enqueue_admin_scripts() {
			if ( ! class_exists( 'Sellkit' ) ) {
				return;
			}

			$page = ! empty( $_GET['page'] ) ? $_GET['page'] : ''; // phpcs:ignore

			if ( ! in_array( $page, $this->sellkit_pro_menus, true ) ) {
				return false;
			}

			wp_enqueue_editor();

			wp_enqueue_script(
				'sellkit-pro',
				sellkit_pro()->plugin_url() . 'assets/dist/admin/sellkit.js',
				[ 'lodash', 'wp-element', 'wp-i18n', 'wp-util' ],
				sellkit_pro()->version(),
				true
			);

			wp_localize_script(
				'sellkit-pro',
				'sellkit',
				$this->get_localize_data()
			);

			wp_enqueue_style(
				'sellkit-pro',
				sellkit_pro()->plugin_url() . 'assets/dist/admin/sellkit.css',
				[],
				sellkit_pro()->version()
			);

			wp_set_script_translations( 'sellkit-pro', 'sellkit', sellkit_pro()->plugin_dir() . 'languages' );

			if ( ! $this->has_valid_dependencies() ) {
				return false;
			}

			$page = sellkit_htmlspecialchars( INPUT_GET, 'page' );

			if ( 'sellkit-alert' === $page ) {
				wp_register_style( 'woocommerce', WC()->plugin_url() . '/assets/css/woocommerce.css', [], sellkit_pro()->version() );

				if ( class_exists( 'woocommerce' ) ) {
					wp_enqueue_style( 'woocommerce' );
				}
			}
		}

		/**
		 * Register admin menu.
		 *
		 * @since 1.1.0
		 * @SuppressWarnings(PHPMD.NPathComplexity)
		 */
		public function register_admin_menu() {
			if ( ! class_exists( 'Sellkit' ) ) {
				return;
			}

			$menu_name    = esc_html__( 'Sellkit', 'sellkit-pro' );
			$initial_page = 'sellkit-dashboard';
			$submenu      = [
				'sellkit-discount' => esc_html__( 'Discounts', 'sellkit-pro' ),
				'sellkit-coupon' => esc_html__( 'Coupons', 'sellkit-pro' ),
				'sellkit-alert' => esc_html__( 'Notices', 'sellkit-pro' ),
			];

			$position = 10;
			foreach ( $submenu as $slug => $title ) {
				$position++;
				add_submenu_page(
					$initial_page,
					"{$menu_name} {$title}",
					$title,
					'edit_theme_options',
					$slug,
					[ $this, 'register_admin_menu_callback' ],
					$position
				);
			}
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
				'nonce'                 => wp_create_nonce( 'sellkit' ),
				'assetsUrl'             => self::$plugin_assets_url,
				'dynamicKeywords'       => Sellkit_Dynamic_Keywords::$keywords['contact_segmentation'],
				'defaultFunnelSteps'    => Sellkit_Admin_Steps::get_default_funnel_steps(),
				'woocommerceSettings'   => $this->get_woocommerce_settings_data(),
				'contactSegmentation'   => [
					'conditionsOperators' => Operators::$condition_operator_names,
					'operators'           => Operators::$names,
					'conditionsData'      => Conditions::$data,
				],
				'adminUrl'              => admin_url(),
				'url'                   => site_url(),
				'timezones'             => timezone_identifiers_list(),
				'defaultSettings'       => [
					'defaultTimezone' => get_option( 'timezone_string' ),
				],
				'elementorActivation'   => class_exists( 'Elementor\Plugin' ),
				'funnelsTemplateSource' => Funnel::SELLKIT_FUNNELS_TEMPLATE_SOURCE,
				'removedContentBoxes'   => Sellkit_Admin_Settings::get_removed_content_box(),
				'sellkitProIsActive'    => true,
				'wcIsActivated' => class_exists( 'WooCommerce' ),
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

			if ( ! class_exists( 'Sellkit' ) ) {
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

		/**
		 * Enqueue frontend scripts.
		 *
		 * @since NEXt
		 */
		public function frontend_enqueue_scripts() {
			if ( ! $this->has_valid_dependencies() ) {
				return false;
			}

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script(
				'sellkit-pro-frontend',
				sellkit_pro()->plugin_url() . 'assets/dist/js/frontend-init' . $suffix . '.js',
				[ 'jquery', 'wp-util' ],
				sellkit_pro()->version(),
				true
			);

			wp_localize_script( 'sellkit-pro-frontend', 'sellkit_pro_frontend',
				apply_filters( 'sellkit_pro_frontend_scripts_args', [
					'pages' => [],
					'nonce' => wp_create_nonce( 'sellkit_frontend_nonce' ),
				] )
			);
		}
	}
}

if ( ! function_exists( 'sellkit_pro' ) ) {
	/**
	 * Initialize the Sellkit.
	 *
	 * @since 1.1.0
	 */
	function sellkit_pro() {
		return Sellkit_Pro::get_instance();
	}
}

/**
 * Initialize the Sellkit application.
 *
 * @since 1.1.0
 */
sellkit_pro();
