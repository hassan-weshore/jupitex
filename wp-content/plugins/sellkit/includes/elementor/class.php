<?php

namespace Sellkit\Elementor;

use Elementor\Plugin as Elementor;
use Elementor\Utils as ElementorUtils;

defined( 'ABSPATH' ) || die();

/**
 * SellKit Elementor class.
 *
 * @since 1.1.0
 */
class Sellkit_Elementor {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Elementor
	 */
	private static $instance = null;

	/**
	 * Modules.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	public $modules = [];

	/**
	 * Get a class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return Sellkit_Elementor Class
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
		add_action( 'elementor/init', [ $this, 'init' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ], 15 );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'frontend_register_scripts' ], 10 );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'frontend_register_styles' ], 10 );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'frontend_enqueue_styles' ], 10 );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'frontend_enqueue_scripts' ], 10 );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_enqueue_styles' ], 10 );

		spl_autoload_register( [ $this, 'autoload' ] );
		remove_theme_support( 'wc-product-gallery-lightbox' );
	}

	/**
	 * Initialize.
	 *
	 * @since 1.1.0
	 */
	public function init() {
		$this->register_modules();

		// Add this category, after basic category.
		Elementor::$instance->elements_manager->add_category(
			'sellkit',
			[
				'title' => __( 'Sellkit', 'sellkit' ),
				'icon'  => 'fa fa-plug',
			],
			1
		);
	}

	/**
	 * Initialize.
	 *
	 * @since 1.1.0
	 */
	public function register_modules() {
		// phpcs:disable
		$modules = [
			'base/base-widget',
			'base/base-module',
			'base/upsell-base-widget',
			'modules/checkout',
			'modules/order-cart-details',
			'modules/order-details',
			'modules/dynamic-keywords',
			'modules/product-price',
			'modules/product-images',
			'modules/product-title',
			'modules/product-description',
			'modules/product-quantity',
			'modules/accept-reject-button',
			'modules/optin',
		];
		// phpcs:enable

		foreach ( $modules as $module ) {
			// Prepare module data.
			$module_data = explode( '/', $module );
			$module_path = $module_data[0];
			$module_name = $module_data[1];

			// Prepare class name.
			$class_name  = str_replace( '-', ' ', $module_name );
			$class_name  = str_replace( ' ', '_', ucwords( $class_name ) );
			$class_name  = "Sellkit_Elementor_{$class_name}";
			$class_name .= ( 'base' === $module_path ) ? '' : '_Module';

			// Prepare class path.
			$class_path  = "elementor/{$module}";
			$class_path .= ( 'base' === $module_path ) ? '' : '/module';

			// Require.
			sellkit()->load_files( [ $class_path ] );

			if ( 'base' === $module_path ) {
				continue;
			}

			// Register.
			if ( $class_name::is_active() ) {
				$this->modules[ $module_name ] = $class_name::get_instance();
			}
		}
	}

	/**
	 * Register controls with Elementor by sellkit prefix.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param object $controls_manager The controls manager.
	 */
	public function register_controls( $controls_manager ) {
		$controls = [
			'file_uploader',
		];

		// Register controls.
		foreach ( $controls as $control ) {
			$class_path = str_replace( '_', '-', $control );
			$class_path = "elementor/controls/{$class_path}";
			sellkit()->load_files( [ $class_path ] );

			$class_name = 'Sellkit_Elementor_Controls_' . $control;
			$controls_manager->register_control( 'sellkit_' . $control, new $class_name() );
		}
	}

	/**
	 * Autoload classes based on namespace.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $class Name of class.
	 */
	public function autoload( $class ) {
		// Return if sellkit name space is not set.
		if ( class_exists( $class ) || 0 !== stripos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$filename = str_replace( __NAMESPACE__ . '\\', '', $class );
		$filename = str_replace( '\\', DIRECTORY_SEPARATOR, $filename );
		$filename = str_replace( '_', '-', $filename );
		$filename = sellkit()->plugin_dir() . '/includes/elementor/' . strtolower( $filename ) . '.php';

		// Return if file is not found.
		if ( ! file_exists( $filename ) ) {
			return;
		}

		include $filename;
	}

	/**
	 * Register front-end scripts
	 *
	 * @since 1.1.0
	 */
	public function frontend_register_scripts() {
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';

		wp_register_script(
			'sellkit-initialize-widgets',
			sellkit()->plugin_url() . 'assets/dist/js/elementor-init' . $suffix . '.js',
			[ 'jquery', 'wc-checkout' ],
			sellkit()->version(),
			true
		);

		wp_localize_script( 'sellkit-initialize-widgets', 'sellkit_elementor', $this->get_localize_data() );
	}

	/**
	 * Get localize data.
	 *
	 * @return array
	 */
	public function get_localize_data() {
		return [
			'nonce' => wp_create_nonce( 'sellkit_elementor' ),
			'wcNeedShipping' => ( function_exists( 'WC' ) ) ? WC()->cart->needs_shipping() : '',
		];
	}

	/**
	 * Registers styles.
	 *
	 * Registers all the front-end styles.
	 *
	 * Fires after Elementor front-end styles are registered.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function frontend_register_styles() {
		$rtl    = is_rtl() ? '-rtl' : '';
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';

		wp_register_style(
			'sellkit-frontend',
			sellkit()->plugin_url() . 'assets/dist/css/frontend' . $rtl . $suffix . '.css',
			[],
			sellkit()->version()
		);
	}

	/**
	 * Enqueue all the front-end styles.
	 *
	 * Fires after Elementor front-end styles are enqueued.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function frontend_enqueue_styles() {
		wp_enqueue_style( 'sellkit-frontend' );
	}

	/**
	 * Enqueue all the front-end scripts.
	 *
	 * Fires after Elementor front-end scripts are enqueued.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function frontend_enqueue_scripts() {
		wp_enqueue_script( 'sellkit-initialize-widgets' );
	}

	/**
	 * Enqueue all the editor styles.
	 *
	 * Fires after Elementor editor style are enqueued.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function editor_enqueue_styles() {
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';

		wp_enqueue_style(
			'sellkit-element-icons',
			sellkit()->plugin_url() . 'assets/dist/css/editor' . $suffix . '.css',
			[],
			sellkit()->version()
		);
	}
}

Sellkit_Elementor::get_instance();
