<?php
defined( 'ABSPATH' ) || die();
/**
 * WooCommerce Atribute Swatches.
 *
 * @package Sellkit\Artbees_WC_Attribute_Swatches
 * @since 1.1.0
 */
class Artbees_WC_Attribute_Swatches {

	/**
	 * Artbees_WC_Attribute_Swatches instance.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var Artbees_WC_Attribute_Swatches
	 */
	private static $instance;

	/**
	 * Artbees_WC_Attribute_Swatches constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		if ( ! sellkit_pro()->has_valid_dependencies() ) {
			return;
		}

		$this->load_files();
		$this->loaded_scripts();
	}

	/**
	 * Get the class instance.
	 *
	 * @since 1.1.0
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load required files.
	 *
	 * @since 1.1.0
	 */
	private function load_files() {
		if ( ! is_admin() ) {
			sellkit_pro()->load_files( [
				'attribute-swatches/includes/frontend/class-attributes-frontend',
				'attribute-swatches/includes/frontend/class-attributes-loop',
			] );

			return;
		}

		sellkit_pro()->load_files( [
			'attribute-swatches/includes/class-attributes',
			'attribute-swatches/includes/fields/class-field-base',
			'attribute-swatches/includes/fields/class-radio',
			'attribute-swatches/includes/fields/class-text',
			'attribute-swatches/includes/fields/class-color',
			'attribute-swatches/includes/fields/class-image',
			'attribute-swatches/includes/class-attribute-term-fields',
			'attribute-swatches/includes/product-fields/class-product-fields',
			'attribute-swatches/includes/class-products',
		] );
	}

	/**
	 * Load Scripts.
	 *
	 * @since 1.1.0
	 */
	public function loaded_scripts() {
		add_action( 'admin_head', [ $this, 'is_wc_pages' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_scripts' ] );
	}

	/**
	 * Load Style in frontend.
	 *
	 * @since 1.1.0
	 *
	 * TODO: Update after transfer to plugin.
	 */
	public function frontend_styles() {
		wp_enqueue_style(
			'was-frontend',
			sellkit_pro()->plugin_url() . 'assets/dist/css/swatches-frontend.min.css',
			[],
			sellkit_pro()->version()
		);
	}

	/**
	 * Load JS in frontend.
	 *
	 * @since 1.1.0
	 *
	 * TODO: Update after transfer to plugin.
	 */
	public function frontend_scripts() {
		wp_enqueue_script(
			'was-frontend',
			sellkit_pro()->plugin_url() . 'assets/dist/js/swatches-frontend.min.js',
			[ 'jquery' ],
			sellkit_pro()->version(),
			true
		);
	}

	/**
	 * Load JS Scripts.
	 *
	 * @since 1.1.0
	 *
	 * TODO: Update after transfer to plugin.
	 */
	public function admin_scripts() {
		if ( ! $this->is_wc_pages() ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'was-admin',
			sellkit_pro()->plugin_url() . 'assets/dist/js/swatches-admin.min.js',
			[ 'jquery' ],
			sellkit_pro()->version(),
			true
		);

		wp_localize_script( 'was-admin', 'was', [ 'nonce' => wp_create_nonce( 'was-admin' ) ] );
	}

	/**
	 * Load Style.
	 *
	 * @since 1.1.0
	 *
	 * TODO: Update after transfer to plugin.
	 */
	public function admin_styles() {
		if ( ! $this->is_wc_pages() ) {
			return;
		}

		wp_enqueue_style(
			'was-admin',
			sellkit_pro()->plugin_url() . 'assets/dist/css/swatches-admin.min.css',
			[],
			sellkit_pro()->version()
		);
	}

	/**
	 * Check is product edit page or attribute.
	 *
	 * @since 1.1.0
	 */
	public function is_wc_pages() {
		$valid_pages = [ 'product_page_product_attributes', 'edit-tags', 'term', 'post' ];
		global $current_screen;

		if (
			'product' === $current_screen->post_type &&
			in_array( $current_screen->base, $valid_pages, true )
		) {
			return true;
		}

		return false;
	}
}

/**
 * Initialize the Artbees_WC_Attribute_Swatches.
 *
 * @since 1.1.0
 */
function artbees_wc_attribute_swatches() {
	return Artbees_WC_Attribute_Swatches::get_instance();
}

artbees_wc_attribute_swatches();
