<?php
/**
 * Add extensions main class.
 *
 * @package JupiterX_Core\Raven
 * @since 1.0.0
 */

namespace JupiterX_Core\Raven;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;
use Elementor\Utils as ElementorUtils;
use Elementor\Settings;
use JupiterX_Core\Raven\Utils;

/**
 * Plugin class.
 *
 * @since 1.0.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
final class Plugin {
	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Plugin
	 */
	public static $instance;

	/**
	 * Modules.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var object
	 */
	public $modules = [];

	/**
	 * Core Modules.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @var array
	 */
	public $core_modules = [];

	/**
	 * Default Modules.
	 *
	 * @since 2.5.1
	 * @access public
	 *
	 * @var array
	 */
	public static $default_modules = [];

	/**
	 * The plugin name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_name;

	/**
	 * The plugin version number.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_version;

	/**
	 * The minimum Elementor version number required.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $minimum_elementor_version = '2.0.0';

	/**
	 * The plugin directory.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_path;

	/**
	 * The plugin URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_url;

	/**
	 * The plugin assets URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_assets_url;

	/**
	 * Disables class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'jupiterx-core' ), '1.0.0' );
	}

	/**
	 * Disables unserializing of the class.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'jupiterx-core' ), '1.0.0' );
	}

	/**
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'check_elementor_version' ] );
	}

	/**
	 * Checks Elementor version compatibility.
	 *
	 * First checks if Elementor is installed and active,
	 * then checks Elementor version compatibility.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function check_elementor_version() {
		if ( ! class_exists( '\\JupiterX_Core\\Raven\\Utils' ) ) {
			if ( empty( self::$plugin_path ) ) {
				self::$plugin_path = trailingslashit( plugin_dir_path( JUPITERX_CORE_RAVEN__FILE__ ) );
			}
			// Requires Utils class.
			require_once self::$plugin_path . 'includes/utils.php';
		}

		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			return;
		}

		// Check for the minimum required Elementor version.
		if ( ! version_compare( ELEMENTOR_VERSION, self::$minimum_elementor_version, '>=' ) ) {
			if ( current_user_can( 'update_plugins' ) ) {
				add_action( 'admin_notices',
				[ $this, 'admin_notice_minimum_elementor_version' ] );
			}
			// don't go further.
			return;
		}

		spl_autoload_register( [ $this, 'autoload' ] );

		$this->define_constants();
		$this->add_hooks();
	}

	/**
	 * Displays notice on the admin dashboard if Elementor version is lower than the
	 * required minimum.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security
			unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security
		}

		$message = sprintf(
			'<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">'
			/* translators: 1: Plugin name 2: Elementor */
			. esc_html__( '%1$s requires version %3$s or greater of %2$s plugin.', 'jupiterx-core' )
			. '</span>',
			'<strong>' . esc_html__( 'JupiterX Core', 'jupiterx-core' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'jupiterx-core' ) . '</strong>',
			self::$minimum_elementor_version
		);

		$file_path   = 'elementor/elementor.php';
		$update_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );

		$message .= sprintf(
			'<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">' .
			'<a class="button-primary" href="%1$s">%2$s</a></span>',
			$update_link, esc_html__( 'Update Elementor Now', 'jupiterx-core' )
		);

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
	}

	/**
	 * Autoload classes based on namesapce.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $class Name of class.
	 */
	public function autoload( $class ) {

		// Return if Raven name space is not set.
		if ( class_exists( $class ) || 0 !== stripos( $class, __NAMESPACE__ ) ) {
			return;
		}

		/**
		 * Prepare filename.
		 *
		 * @todo Refactor to use preg_replace.
		 */
		$filename = str_replace( __NAMESPACE__ . '\\', '', $class );
		$filename = str_replace( '\\', DIRECTORY_SEPARATOR, $filename );
		$filename = str_replace( '_', '-', $filename );
		$filename = self::$plugin_path . 'includes/' . strtolower( $filename ) . '.php';

		// Return if file is not found.
		if ( ! file_exists( $filename ) ) {
			return;
		}

		include $filename;
	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function define_constants() {
		$plugin_data = get_file_data( JUPITERX_CORE_RAVEN__FILE__, array( 'Plugin Name', 'Version' ), 'jupiterx-core' );

		self::$plugin_name       = array_shift( $plugin_data );
		self::$plugin_version    = array_shift( $plugin_data );
		self::$plugin_path       = trailingslashit( plugin_dir_path( JUPITERX_CORE_RAVEN__FILE__ ) );
		self::$plugin_url        = trailingslashit( plugin_dir_url( JUPITERX_CORE_RAVEN__FILE__ ) );
		self::$plugin_assets_url = trailingslashit( self::$plugin_url . 'assets' );
	}

	/**
	 * Adds required hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function add_hooks() {
		add_action( 'elementor/init', [ $this, 'init' ], 0 );
		add_action( 'elementor/editor/footer', [ $this, 'editor_templates' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ], 15 );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_enqueue_styles' ], 0 );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'editor_enqueue_scripts' ], 0 );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'preview_enqueue_styles' ], 0 );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'frontend_register_styles' ], 0 );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'frontend_enqueue_styles' ], 0 );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'frontend_register_scripts' ], 0 );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'frontend_enqueue_scripts' ], 0 );
		add_action( 'elementor/theme/register_locations', [ $this, 'jupiterx_register_elementor_locations' ] );
		add_filter( 'elementor/editor/localize_settings', [ $this, 'customize_elementor_localized_settings' ] );

		add_action( 'wp_ajax_raven_sync_libraries', [ $this, 'sync_libraries' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ] );

		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 20 );
		}

		if ( function_exists( 'WC' ) ) {
			add_action( 'elementor/frontend/the_content', [ $this, 'layout_builder_wc_add_wrapper' ] );
			add_filter( 'elementor/widget/render_content', [ $this, 'layout_builder_wc_add_wrapper_in_editor' ], 10, 2 );

			// Layout builder preview.
			add_action( 'jupiterx-layout-builder-preview-product', [ $this, 'layout_builder_single_product' ] );
		}

		add_action( 'admin_init', [ $this, 'disable_elementor_onboard' ] );
	}

	/**
	 * Disable elementor onboard.
	 *
	 * @since 2.5.0
	 * @access public
	 */
	public function disable_elementor_onboard() {
		if ( ! empty( get_option( 'elementor_onboarded' ) ) ) {
			return;
		}

		update_option( 'elementor_onboarded', 1 );
	}

	/**
	 * Add required class for woocommerce single page
	 * in layout builder preview.
	 *
	 * @since 2.5.0
	 * @access public
	 */
	public function layout_builder_single_product() {
		add_filter( 'body_class', function( $classes ) {
			$woo_classes = [ 'woocommerce', 'woocommerce-page', 'woocommerce-js' ];

			return array_merge( $classes, $woo_classes );
		} );

		global $product;

		add_action( 'the_content', function( $content ) use ( $product ) {
			$html = sprintf(
				'<div class="%1$s">%2$s</div>',
				esc_attr( implode( ' ', wc_get_product_class( '', $product ) ) ),
				$content
			);

			return $html;
		} );
	}

	/**
	 * Add a wrapper for product page build in layout builder.
	 *
	 * @since 2.5.0
	 *
	 * @param string $content Elementor contents html.
	 * @access public
	 */
	public function layout_builder_wc_add_wrapper( $content ) {
		$template_id = apply_filters( 'jupiterx-conditions-manager-template-id', 0 );
		$page_type   = get_post_meta( $template_id, 'jx-layout-type', true );

		if ( ! wp_doing_ajax() && ! is_product() || 'product' !== $page_type ) {
			return $content;
		}

		global $product;

		$html = sprintf(
			'<div id="product-%1$s" class="%2$s">%3$s</div>',
			get_the_ID(),
			esc_attr( implode( ' ', wc_get_product_class( '', $product ) ) ),
			$content
		);

		return $html;
	}

	/**
	 * Add a wrapper for product page build in layout builder in editor.
	 *
	 * @since 2.5.0
	 *
	 * @param string $template Elementor widget content html.
	 * @param object $widget Elementor widget object.
	 * @access public
	 */
	public function layout_builder_wc_add_wrapper_in_editor( $template, $widget ) {
		$template_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );

		if ( empty( $template_id ) ) {
			$template_id = filter_input( INPUT_GET, 'preview_id', FILTER_SANITIZE_NUMBER_INT );
		}

		if ( empty( $template_id ) ) {
			$template_id = filter_input( INPUT_POST, 'editor_post_id', FILTER_SANITIZE_NUMBER_INT );
		}

		$page_type = get_post_meta( $template_id, 'jx-layout-type', true );

		if ( 'product' !== $page_type ) {
			return $template;
		}

		$product_widgets = [
			'raven-product-title',
			'raven-product-gallery',
			'raven-product-additional-cart',
			'raven-product-content',
			'raven-product-data-tabs',
			'raven-product-meta',
			'raven-product-price',
			'raven-product-rating',
			'raven-product-short-description',
			'raven-product-add-to-cart',
			'raven-woocommerce-breadcrumbs',
			'raven-product-reviews',
		];

		$product_widgets = apply_filters( 'jupiterx_valid_product_widgets', $product_widgets );

		if ( empty( $product_widgets ) ) {
			return $template;
		}

		global $product;

		if ( empty( $product ) ) {
			$product = Utils::get_product();
		}

		if ( in_array( $widget->get_name(), $product_widgets, true ) ) {
			$template = sprintf(
				'<div class="woocommerce"><div id="product-%1$s" class="%2$s">%3$s</div></div>',
				get_the_ID(),
				esc_attr( implode( ' ', wc_get_product_class( '', $product ) ) ),
				$template
			);

			return $template;
		}

		return $template;
	}

	/**
	 * Add support elementor theme locations.
	 *
	 * @since 1.0.0
	 *
	 * @param object $elementor_theme_manager Elementor theme manager object.
	 * @access public
	 */
	public function jupiterx_register_elementor_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_location( 'header' );
		$elementor_theme_manager->register_location( 'footer' );

		if ( ! class_exists( 'ElementorPro\Plugin' ) ) {
			$elementor_theme_manager->register_location( 'single' );
		}
	}

	/**
	 * Register controls with Elementor by raven prefix.
	 * raven-loop-animation, jupiterx-core-raven-parallax-scroll, ...
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $controls_manager The controls manager.
	 */
	public function register_controls( $controls_manager ) {
		/**
		 * List of all controls and group controls.
		 * Credit: goo.gl/hkvhZJ - preg_grep solution
		 */
		$controls       = preg_grep( '/^((?!index.php).)*$/', glob( self::$plugin_path . '/includes/controls/*.php' ) );
		$group_controls = preg_grep( '/^((?!index.php).)*$/', glob( self::$plugin_path . '/includes/controls/group/*.php' ) );

		// Register controls.
		foreach ( $controls as $control ) {

			// Prepare control name.
			$control_name = basename( $control, '.php' );
			$control_name = str_replace( '-', '_', $control_name );

			// Prepare class name.
			$class_name = str_replace( '-', '_', $control_name );
			$class_name = __NAMESPACE__ . '\Controls\\' . $class_name;

			// Register now.
			$controls_manager->register_control( 'raven_' . $control_name, new $class_name() );
		}

		// Register group controls.
		foreach ( $group_controls as $control ) {

			// Prepare control name.
			$control_name = basename( $control, '.php' );

			// Prepare class name.
			$class_name = str_replace( '-', '_', $control_name );
			$class_name = __NAMESPACE__ . '\Controls\Group\\' . $class_name;

			// Register now.
			$controls_manager->add_group_control( 'raven-' . $control_name, new $class_name() );
		}

		$this->jupiterx_icons( $controls_manager );
	}

	/**
	 * Adds Jupiter X icon to existing icon control in Elementor
	 *
	 * @since 1.0.0
	 *
	 * @param object $controls_manager Control manager instance.
	 *
	 * @return void
	 */
	public function jupiterx_icons( $controls_manager ) {

		$elementor_icons = $controls_manager->get_control( 'icon' )->get_settings( 'options' );

		$jupiterx_icons = array_merge(
			$elementor_icons,
			array(
				'jupiterx-icon-creative-market'  => 'creative-market',
				'jupiterx-icon-long-arrow'       => 'long-arrow',
				'jupiterx-icon-search-1'         => 'search-1',
				'jupiterx-icon-search-2'         => 'search-2',
				'jupiterx-icon-search-3'         => 'search-3',
				'jupiterx-icon-search-4'         => 'search-4',
				'jupiterx-icon-share-email'      => 'share-email',
				'jupiterx-icon-shopping-cart-1'  => 'shopping-cart-1',
				'jupiterx-icon-shopping-cart-2'  => 'shopping-cart-2',
				'jupiterx-icon-shopping-cart-3'  => 'shopping-cart-3',
				'jupiterx-icon-shopping-cart-4'  => 'shopping-cart-4',
				'jupiterx-icon-shopping-cart-5'  => 'shopping-cart-5',
				'jupiterx-icon-shopping-cart-6'  => 'shopping-cart-6',
				'jupiterx-icon-shopping-cart-7'  => 'shopping-cart-7',
				'jupiterx-icon-shopping-cart-8'  => 'shopping-cart-8',
				'jupiterx-icon-shopping-cart-9'  => 'shopping-cart-9',
				'jupiterx-icon-shopping-cart-10' => 'shopping-cart-10',
				'jupiterx-icon-zillow'           => 'zillow',
				'jupiterx-icon-zomato'           => 'zomato',
			)
		);

		$controls_manager->get_control( 'icon' )->set_settings( 'options', $jupiterx_icons );
	}

	/**
	 * Get modules.
	 *
	 * @since 1.20.0
	 * @access public
	 */
	public static function get_modules( $primary = false ) {
		$modules = [
			'alert' => __( 'Alert', 'jupiterx-core' ),
			'button' => __( 'Button', 'jupiterx-core' ),
			'categories' => __( 'Categories', 'jupiterx-core' ),
			'countdown' => __( 'Countdown', 'jupiterx-core' ),
			'counter' => __( 'Counter', 'jupiterx-core' ),
			'divider' => __( 'Divider', 'jupiterx-core' ),
			'flex-spacer' => __( 'Flex Spacer', 'jupiterx-core' ),
			'forms' => __( 'Forms', 'jupiterx-core' ),
			'global-widget' => esc_html__( 'Global Widget', 'jupiterx-core' ),
			'heading' => __( 'Heading', 'jupiterx-core' ),
			'icon' => __( 'Icon', 'jupiterx-core' ),
			'image' => __( 'Image', 'jupiterx-core' ),
			'image-gallery' => __( 'Image Gallery', 'jupiterx-core' ),
			'nav-menu' => __( 'Navigation Menu', 'jupiterx-core' ),
			'photo-album' => __( 'Photo Album', 'jupiterx-core' ),
			'photo-roller' => __( 'Photo Roller', 'jupiterx-core' ),
			'posts' => esc_html__( 'Posts', 'jupiterx-core' ),
			'post-content' => __( 'Post Content', 'jupiterx-core' ),
			'post-comments' => __( 'Post Comments', 'jupiterx-core' ),
			'post-meta' => __( 'Post Meta', 'jupiterx-core' ),
			'products' => __( 'Products', 'jupiterx-core' ),
			'search-form' => __( 'Search Form', 'jupiterx-core' ),
			'shopping-cart' => __( 'Shopping Cart', 'jupiterx-core' ),
			'site-logo' => __( 'Site Logo', 'jupiterx-core' ),
			'tabs' => __( 'Tabs', 'jupiterx-core' ),
			'video' => __( 'Video', 'jupiterx-core' ),
			'breadcrumbs' => __( 'Breadcrumbs', 'jupiterx-core' ),
			'add-to-cart' => esc_html__( 'Add To Cart', 'jupiterx-core' ),
			'advanced-nav-menu' => esc_html__( 'Advanced Menu', 'jupiterx-core' ),
			'archive-title' => esc_html__( 'Archive Title', 'jupiterx-core' ),
			'author-box' => esc_html__( 'Author Box', 'jupiterx-core' ),
			'animated-heading' => esc_html__( 'Animated Heading', 'jupiterx-core' ),
			'archive-description' => esc_html__( 'Archive Description', 'jupiterx-core' ),
			'business-hours' => esc_html__( 'Business Hours', 'jupiterx-core' ),
			'call-to-action' => esc_html__( 'Call To Action', 'jupiterx-core' ),
			'cart' => esc_html__( 'Cart', 'jupiterx-core' ),
			'carousel' => esc_html__( 'Carousel', 'jupiterx-core' ),
			'content-switch' => esc_html__( 'Content switch', 'jupiterx-core' ),
			'custom-css' => esc_html__( 'Custom CSS', 'jupiterx-core' ),
			'flip-box' => esc_html__( 'Flip Box', 'jupiterx-core' ),
			'hotspot' => esc_html__( 'Hotspot', 'jupiterx-core' ),
			'lottie' => esc_html__( 'Lottie', 'jupiterx-core' ),
			'product-data-tabs' => esc_html__( 'Product Data Tabs', 'jupiterx-core' ),
			'post-title' => esc_html__( 'Post Title', 'jupiterx-core' ),
			'preview-settings' => esc_html__( 'Preview Settings', 'jupiterx-core' ),
			'product-reviews' => esc_html__( 'Product Reviews', 'jupiterx-core' ),
			'product-additional-info' => esc_html__( 'Product Additional Information', 'jupiterx-core' ),
			'price-list' => esc_html__( 'Price List', 'jupiterx-core' ),
			'pricing-table' => esc_html__( 'Pricing Table', 'jupiterx-core' ),
			'product-rating' => esc_html__( 'Product Rating', 'jupiterx-core' ),
			'product-gallery' => esc_html__( 'Product Gallery', 'jupiterx-core' ),
			'product-meta' => esc_html__( 'Product Meta', 'jupiterx-core' ),
			'product-short-description' => esc_html__( 'Product Short Description', 'jupiterx-core' ),
			'product-price' => esc_html__( 'Product Price', 'jupiterx-core' ),
			'site-title' => esc_html__( 'Site Title', 'jupiterx-core' ),
			'slider' => esc_html__( 'Slider', 'jupiterx-core' ),
			'tooltip' => esc_html__( 'Tooltip', 'jupiterx-core' ),
			'product-title' => esc_html__( 'Product Title', 'jupiterx-core' ),
			'role-manager' => esc_html__( 'Role Manager', 'jupiterx-core' ),
			'product-content' => esc_html__( 'Product Content', 'jupiterx-core' ),
			'custom-attributes' => esc_html__( 'Custom Attributes', 'jupiterx-core' ),
			'woocommerce-breadcrumbs' => esc_html__( 'WooCommerce Breadcrumbs', 'jupiterx-core' ),
			'motion_effects' => esc_html__( 'Motion Effects', 'jupiterx-core' ),
			'my-account' => esc_html__( 'My Account', 'jupiterx-core' ),
		];

		if ( $primary ) {
			return $modules;
		}

		$modules = array_keys( $modules );

		self::$default_modules = $modules;

		if ( function_exists( 'jupiterx_get_option' ) ) {
			$database_modules = jupiterx_get_option( 'elements', $modules );

			if ( count( $database_modules ) > count( $modules ) ) {
				jupiterx_update_option( 'elements', $modules );
				$database_modules = $modules;
			}

			$modules = $database_modules;
		}

		// Merge four special modules into modules.
		$modules = array_merge( $modules, [ 'custom-scripts', 'column', 'elementor-ads', 'scroll-snap' ] );

		// Merge sellkit if its >= V2.0.0
		$version = wp_get_theme()->get( 'Version' );

		// Adds sellkit preview modules.
		if ( $version >= '2.0.0' ) {
			$modules = array_merge( $modules, [ 'sellkit' ] );
		}

		// Remove empty value from modules.
		$modules = array_filter( $modules, 'strlen' );

		return $modules;
	}

	/**
	 * Register modules.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_modules() {

		foreach ( self::get_modules() as $module_name ) {
			// Prepare class name.
			$class_name = str_replace( '-', ' ', $module_name );
			$class_name = str_replace( ' ', '_', ucwords( $class_name ) );
			$class_name = __NAMESPACE__ . '\Modules\\' . $class_name . '\Module';

			// Register.
			if ( class_exists( $class_name ) && $class_name::is_active() ) {
				$this->modules[ $module_name ] = $class_name::get_instance();
			}
		}

		$core_modules = [
			'template',
			'compatibility',
			'dynamic-tags',
			'dynamic-styles',
		];

		if ( ! class_exists( 'ElementorPro\Plugin' ) ) {
			$core_modules[] = 'library';
		}

		foreach ( $core_modules as $core_module_name ) {
			// Prepare class name.
			$class_name = str_replace( '-', ' ', $core_module_name );
			$class_name = str_replace( ' ', '_', ucwords( $class_name ) );
			$class_name = __NAMESPACE__ . '\Core\\' . $class_name . '\Module';

			// Register.
			$this->core_modules[ $core_module_name ] = new $class_name();
		}
	}

	/**
	 * Adds actions after Elementor init.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		if ( function_exists( 'jupiterx_is_premium' ) && empty( jupiterx_is_premium() ) ) {
			return;
		}

		// Register modules.
		$this->register_modules();

		// Add this category, after basic category.
		Elementor::$instance->elements_manager->add_category(
			'jupiterx-core-raven-elements',
			[
				'title' => __( 'Jupiter X Elements', 'jupiterx-core' ),
				'icon'  => 'fa fa-plug',
			],
			1
		);

		Elementor::$instance->elements_manager->add_category(
			'jupiterx-core-raven-woo-elements',
			[
				'title' => __( 'Jupiter X Products', 'jupiterx-core' ),
				'icon'  => 'fa fa-plug',
			],
			1
		);

		if ( ! function_exists( 'sellkit' ) ) {
			Elementor::$instance->elements_manager->add_category(
				'sellkit',
				[
					'title' => __( 'Sellkit', 'jupiterx-core' ),
					'icon'  => 'fa fa-plug',
				],
				1
			);
		}

		// Requires Utils class.
		require_once self::$plugin_path . '/includes/utils.php';

		do_action_deprecated( 'raven/init', [], '1.18.0', 'jupiterx_core_raven_init' );

		do_action( 'jupiterx_core_raven_init' );
	}

	/**
	 * Print editor templates.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function editor_templates() {
		require_once self::$plugin_path . '/includes/editor-templates/templates.php';
	}

	/**
	 * Enqueue styles.
	 *
	 * Enqueue all the editor styles.
	 *
	 * Fires after Elementor editor styles are enqueued.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function editor_enqueue_styles() {
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';

		wp_enqueue_style(
			'jupiterx-core-raven-icons',
			self::$plugin_assets_url . 'lib/raven-icons/css/raven-icons' . $suffix . '.css',
			[],
			self::$plugin_version
		);

		wp_enqueue_style(
			'jupiterx-core-raven-editor',
			self::$plugin_assets_url . 'css/editor' . $suffix . '.css',
			[],
			self::$plugin_version
		);

		wp_enqueue_style(
			'jupiterx-icons',
			self::$plugin_assets_url . 'css/icons' . $suffix . '.css',
			[],
			self::$plugin_version
		);
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue all the editor scripts.
	 *
	 * Fires after Elementor editor scripts are enqueued.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function editor_enqueue_scripts() {
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			'jupiterx-core-raven-editor',
			self::$plugin_assets_url . 'js/editor' . $suffix . '.js',
			[ 'jquery' ],
			self::$plugin_version,
			true
		);

		$active_elements = [];
		$options         = get_option( 'jupiterx', [] );

		if ( isset( $options['elements'] ) ) {
			$active_elements = $options['elements'];
		}

		if ( function_exists( 'jupiterx_get_option' ) ) {
			$active_elements = jupiterx_get_option( 'elements', self::$default_modules );
		}

		wp_localize_script( 'jupiterx-core-raven-editor', 'jupiterxOptions', [
			'activeElements' => $active_elements,
		] );
	}

	/**
	 * Inject theme version to elementor editor object.
	 *
	 * @param array $settings elementor config
	 * @since 2.0.5
	 * @return array
	 */
	public function customize_elementor_localized_settings( $settings ) {
		$new_settings                  = [];
		$new_settings['jx_version']    = wp_get_theme()->get( 'Version' );
		$new_settings['jx_layout']     = 'none';
		$new_settings['jx_elementor']  = 'free';
		$new_settings['jx_conditions'] = false;
		$new_settings['jx_nonce']      = wp_create_nonce( 'jupiterx_control_panel' );

		$template_id    = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
		$layout_builder = filter_input( INPUT_GET, 'layout-builder', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( empty( $template_id ) ) {
			$template_id = get_the_id();
		}

		if ( metadata_exists( 'post', $template_id, 'jx-layout-type' ) && ( ! empty( $layout_builder ) || ! defined( 'ELEMENTOR_PRO_VERSION' ) ) ) {
			$new_settings['jx_layout'] = get_post_meta( $template_id, 'jx-layout-type', true );
		}

		// Check conditions.
		$conditions = get_post_meta( $template_id, 'jupiterx-condition-rules', true );
		if ( is_array( $conditions ) && count( $conditions ) > 0 ) {
			$new_settings['jx_conditions'] = true;
		}

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$new_settings['jx_elementor'] = 'pro';
		}

		$new_settings['jx_post_type'] = get_post_type( $template_id );

		$settings = array_replace_recursive( $settings,
			$new_settings
		);

		return $settings;
	}

	/**
	 * Preview styles.
	 *
	 * Preview all the preview styles.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function preview_enqueue_styles() {
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';

		wp_enqueue_style(
			'jupiterx-core-raven-icons',
			self::$plugin_assets_url . 'lib/raven-icons/css/raven-icons' . $suffix . '.css',
			[],
			self::$plugin_version
		);
	}

	/**
	 * Registers styles.
	 *
	 * Registers all the front-end styles.
	 *
	 * Fires after Elementor front-end styles are registered.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function frontend_register_styles() {
		$rtl    = is_rtl() ? '-rtl' : '';
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';
		$dep    = [ 'font-awesome' ];

		if ( function_exists( 'kucrut_register_sdk' ) ) {
			$dep = [];
		}

		wp_register_style(
			'jupiterx-core-raven-frontend',
			self::$plugin_assets_url . 'css/frontend' . $rtl . $suffix . '.css',
			$dep,
			self::$plugin_version
		);
	}

	/**
	 * Enqueue all the front-end styles.
	 *
	 * Fires after Elementor front-end styles are enqueued.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function frontend_enqueue_styles() {
		wp_enqueue_style( 'jupiterx-core-raven-frontend' );
	}


	/**
	 * Registers all the front-end scripts.
	 *
	 * Fires after Elementor front-end scripts are registered.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function frontend_register_scripts() {
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';

		wp_register_script(
			'jupiterx-core-raven-url-polyfill',
			self::$plugin_assets_url . 'lib/url-polyfill/url-polyfill' . $suffix . '.js',
			[ 'jquery' ],
			'1.1.7',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-parallax-scroll',
			self::$plugin_assets_url . 'lib/parallax-scroll/jquery.parallax-scroll' . $suffix . '.js',
			[ 'jquery' ],
			'1.0.0',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-countdown',
			self::$plugin_assets_url . 'lib/countdown/jquery.countdown' . $suffix . '.js',
			[ 'jquery' ],
			'2.2.0',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-enquire',
			self::$plugin_assets_url . 'lib/enquire/enquire' . $suffix . '.js',
			[],
			'2.1.2',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-savvior',
			self::$plugin_assets_url . 'lib/savvior/savvior' . $suffix . '.js',
			[ 'jupiterx-core-raven-enquire' ],
			'0.6.0',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-anime',
			self::$plugin_assets_url . 'lib/anime/anime' . $suffix . '.js',
			[],
			'2.2.0',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-stack-motion-effects',
			self::$plugin_assets_url . 'lib/stack-motion-effects/stack-motion-effects' . $suffix . '.js',
			[],
			'1.0.0',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-object-fit',
			self::$plugin_assets_url . 'lib/object-fit/object-fit' . $suffix . '.js',
			[ 'jquery' ],
			'2.1.1',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-smartmenus',
			self::$plugin_assets_url . 'lib/smartmenus/jquery.smartmenus' . $suffix . '.js',
			[ 'jquery' ],
			'1.1.0',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-pagination',
			self::$plugin_assets_url . 'lib/pagination/jquery.twbsPagination' . $suffix . '.js',
			[ 'jquery' ],
			'1.4.2',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-packery',
			self::$plugin_assets_url . 'lib/packery/packery' . $suffix . '.js',
			[ 'jquery' ],
			'2.0.1',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-isotope',
			self::$plugin_assets_url . 'lib/isotope/isotope' . $suffix . '.js',
			[ 'jquery' ],
			'3.0.6',
			true
		);

		wp_register_script(
			'jupiterx-core-raven-frontend',
			self::$plugin_assets_url . 'js/frontend' . $suffix . '.js',
			[ 'jquery', 'wp-util' ],
			self::$plugin_version,
			true
		);
	}

	/**
	 * Enqueue all the front-end scripts.
	 *
	 * Fires after Elementor front-end scripts are enqueued.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function frontend_enqueue_scripts() {
		wp_enqueue_script( 'jupiterx-core-raven-frontend' );

		foreach ( $this->modules as $module_name => $module_instance ) {
			$translations = $module_instance->translations();

			if ( empty( $translations ) ) {
				continue;
			}

			$module_name = str_replace( '-', ' ', $module_name );
			$module_name = str_replace( ' ', '', ucwords( $module_name ) );

			wp_localize_script(
				'jupiterx-core-raven-frontend',
				'raven' . $module_name . 'Translations',
				$translations
			);
		}

		wp_localize_script(
			'jupiterx-core-raven-frontend',
			'ravenTools',
			$this->raven_localize_frontend_data()
		);
	}

	/**
	 * Localized data for frontend.
	 *
	 * @since 2.5.0
	 */
	private function raven_localize_frontend_data() {
		return [
			'nonce' => wp_create_nonce( 'jupiterx-core-raven' ),
			'activeElements' => jupiterx_get_option( 'elements', self::$default_modules ),
		];
	}

	/**
	 * Register raven admin scripts.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_admin_scripts() {
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			'jupiterx-core-raven-admin',
			self::$plugin_assets_url . 'js/admin' . $suffix . '.js',
			[ 'jquery' ],
			self::$plugin_version,
			true
		);
	}

	/**
	 * Add Raven tab in Elementor Settings page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $settings Settings.
	 */
	public function register_admin_fields( $settings ) {
		if ( ! jupiterx_is_premium() ) {
			return;
		}

		$settings->add_tab(
			'raven', [
				'label' => __( 'Jupiter X', 'jupiterx-core' ),
			]
		);

		$fields = [
			'raven_google_api_key' => [
				'label' => __( 'API Key', 'jupiterx-core' ),
				'field_args' => [
					'type' => 'text',
					/* translators: %s: Google Developer Console URL  */
					'desc' => sprintf( __( 'This API key will be used for maps, places. <a href="%s" target="_blank">Get your API key</a>.', 'jupiterx-core' ), 'https://console.developers.google.com' ),
				],
			],
			'raven_google_client_id' => [
				'label' => __( 'Google client id', 'jupiterx-core' ),
				'field_args' => [
					'type' => 'text',
				],
			],
		];

		$settings->add_section( 'raven', 'raven_google_api_key', [
			'callback' => function() {
				echo '<hr><h2>' . esc_html__( 'Google API Key', 'jupiterx-core' ) . '</h2>';
			},
			'fields' => $fields,
		] );
	}

	/**
	 * Sync libraries.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return void
	 */
	public function sync_libraries() {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( empty( $_POST['library'] ) ) {
			wp_send_json_error( __( 'library field is missing', 'jupiterx-core' ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification
		$library = sanitize_text_field( wp_unslash( $_POST['library'] ) );

		if ( 'presets' === $library && isset( $this->core_modules['preset'] ) ) {
			$cached_elements = get_transient( 'raven_presets_elements_cached' );

			delete_transient( 'raven_presets_elements' );
			delete_transient( 'raven_presets_elements_cached' );

			if ( false === $cached_elements ) {
				wp_send_json_success();
			}

			foreach ( $cached_elements as $element ) {
				delete_transient( 'raven_preset_' . $element );
			}

			wp_send_json_success();
		}

		wp_send_json_error( __( 'invalid library value received', 'jupiterx-core' ) );
	}
}

/**
 * Returns the Plugin application instance.
 *
 * @since 1.0.0
 *
 * @return Plugin
 */
function jupiterx_core_raven() {
	return Plugin::get_instance();
}

/**
 * Initializes the JupiterX_Core Raven extension.
 *
 * @since 1.0.0
 */
jupiterx_core_raven();
