<?php

namespace Sellkit\Settings;

defined( 'ABSPATH' ) || exit;
/**
 * Sellkit settings.
 *
 * @since 1.1.0
 */
class Sellkit_Settings {
	/**
	 * The class instance.
	 *
	 * @var Object Class instance.
	 * @since 1.1.0
	 */
	public static $instance = null;

	/**
	 * Class Instance.
	 *
	 * @since 1.1.0
	 * @return Sellkit_Settings|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class construct.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'apply_sellkit_cart_settings' ] );
	}

	/**
	 * Apply sellkit cart settings in frontend.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function apply_sellkit_cart_settings() {
		$options = get_option( 'sellkit', [] );

		if ( is_admin() || ! function_exists( 'WC' ) || ! is_cart() ) {
			return;
		}

		// Escape default woocommerce cart page.
		if ( array_key_exists( 'skip_cart_page', $options ) && '1' === $options['skip_cart_page'] ) {
			if ( WC()->cart->cart_contents_count > 0 ) {
				echo wc_get_checkout_url();
				wp_safe_redirect( wc_get_checkout_url() );
				exit();
			}
		}

		// Set default woocommerce empty cart page template.
		if (
			empty( $options['empty_cart_template'] )
			|| empty( $options['empty_cart_template'][0] )
			|| ! defined( 'ELEMENTOR_VERSION' )
			|| WC()->cart->cart_contents_count > 0
		) {
			return;
		}

		$template_id = $options['empty_cart_template'][0];

		if ( (int) $template_id <= 0 ) {
			return;
		}

		add_action( 'template_redirect', function() {
			jupiterx_add_filter( 'jupiterx_layout', 'c' );

			sellkit()->load_files( [
				'templates/canvas',
			] );

			exit;
		}, 5 );

		add_filter( 'the_content', function() use ( $template_id ) {
			ob_start();
			echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
			return ob_get_clean();
		} );
	}
}

Sellkit_Settings::get_instance();
