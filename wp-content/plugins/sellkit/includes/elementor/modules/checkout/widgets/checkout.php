<?php

defined( 'ABSPATH' ) || die();

use Sellkit\Elementor\Modules\Checkout\Widgets\{ Settings_Controls, Style_Controls };
use Sellkit\Elementor\Modules\Checkout\Classes\{Global_Hooks, Local_Hooks, Multi_Step, Helper };
use Elementor\Plugin as Elementor;

/**
 * Checkout class.
 *
 * @since 1.1.0
 */
class Sellkit_Elementor_Checkout_Widget extends Sellkit_Elementor_Base_Widget {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'frontend_enqueue_scripts' ], 10 );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'editor_enqueue_scripts' ], 10 );
	}

	public function get_name() {
		return 'sellkit-checkout';
	}

	public function get_title() {
		return __( 'Checkout Form', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-checkout-icon';
	}

	public function frontend_enqueue_scripts() {
		wp_enqueue_script( 'wc-checkout' );
	}

	public function editor_enqueue_scripts() {
		wp_enqueue_script( 'wc-checkout' );
	}

	public function is_reload_preview_required() {
		return true;
	}

	public function get_style_depends() {
		return [ 'font-awesome' ];
	}

	/**
	 * Apply multi-step functionality.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	private function apply_multi_step_design( $settings ) {
		new Multi_Step( $settings );
	}

	/**
	 * Apply local hooks.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	private function apply_local_hooks( $settings ) {
		new Local_Hooks( $settings, $this );
	}

	protected function register_controls() {
		require __DIR__ . '/settings-controls.php';
		require __DIR__ . '/style-controls.php';

		new Settings_Controls();
		new Style_Controls();
	}

	protected function render() {
		add_filter( 'woocommerce_is_checkout', function() {
			return true;
		}, 10 );

		$settings = $this->get_settings_for_display();

		$this->clear_sellkit_checkout_custom_hooks( $settings );

		if ( 'multi-step' === $settings['layout-type'] ) {
			$this->apply_multi_step_design( $settings );
		}

		$this->apply_local_hooks( $settings );

		if ( 'one-page' === $settings['layout-type'] ) {
			$this->add_render_attribute(
				'wrapper',
				'class',
				'sellkit-checkout-widget-one-page-build sellkit-checkout-widget-main-wrapper'
			);
		} else {
			$this->add_render_attribute(
				'wrapper',
				'class',
				'sellkit-checkout-widget-multi-page-build sellkit-checkout-widget-main-wrapper'
			);
		}

		if ( Elementor::$instance->editor->is_edit_mode() ) {
			$current_user = wp_get_current_user()->ID;
			wp_set_current_user( 0 );
		}

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . ' >';
		echo do_shortcode( '[woocommerce_checkout]' );
		echo do_shortcode( '[sellkit_checkout_widget_simulated]' );
		echo '</div>';

		do_action( 'sellkit-after-checkout-content', $settings );

		if ( Elementor::$instance->editor->is_edit_mode() ) {
			wp_set_current_user( $current_user );
		}

	}

	/**
	 * Remove every function attached to sellkit checkout custom hooks.
	 *
	 * @param array $settings widget settings.
	 * @since 1.1.0
	 * @return void
	 */
	private function clear_sellkit_checkout_custom_hooks( $settings ) {
		// Removed function that attached to custom hooks to prevent any error.
		remove_all_actions( 'sellkit-checkout-cart-item' );
		remove_all_actions( 'sellkit-checkout-step-a-begins' );
		remove_all_actions( 'sellkit-checkout-step-a-ends' );
		remove_all_actions( 'sellkit-checkout-step-b-begins' );
		remove_all_actions( 'sellkit-checkout-step-b-ends' );
		remove_all_actions( 'sellkit-checkout-widget-step-two-header' );
		remove_all_actions( 'sellkit-checkout-step-c-begins' );
		remove_all_actions( 'sellkit-checkout-multistep-third-step-back-btn' );
		remove_all_actions( 'sellkit-checkout-step-c-ends' );
		remove_all_actions( 'sellkit-checkout-widget-step-three-header' );
		remove_all_actions( 'sellkit-checkout-multistep-sidebar-begins' );
		remove_all_actions( 'sellkit-checkout-multistep-sidebar-ends' );
		remove_all_actions( 'sellkit-checkout-widget-breadcrumb-desktop' );
		remove_all_actions( 'sellkit-checkout-widget-breadcrumb-mobile' );

		remove_all_actions( 'sellkit_checkout_shipping_fields' );
		remove_all_actions( 'sellkit_checkout_billing_fields' );

		remove_all_actions( 'sellkit_checkout_after_shipping_section' );

		remove_all_actions( 'sellkit_checkout_one_page_express_methods' );

		remove_all_actions( 'sellkit-checkout-widget-custom-coupon-form' );

		remove_all_filters( 'woocommerce_locate_template' );

		// !TODO We can use this method to move / remove billing and shipping fields.
		add_action( 'woocommerce_checkout_shipping', [ WC()->checkout(), 'checkout_form_shipping' ] ); // ! added for theme integration. Astra.
		remove_action( 'woocommerce_checkout_billing', [ WC()->checkout(), 'checkout_form_shipping' ] ); // ! added for theme integration. Astra.

		if ( Elementor::$instance->editor->is_edit_mode() ) {
			$this->editor_mode( $settings );
		}
	}

	/**
	 * Editor mode, Render widget content on clicking it's wrapper.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	private function editor_mode( $settings ) {
		add_action( 'sellkit_checkout_required_hidden_fields', function() {
			echo '<input type="hidden" name="sellkit-elementor-editor-mode" value="true" >';
		} );

		// Some frontend Hooks doesn't work in editor mode.
		// So we just hide them in editor mode, BUT we use real hooks in frontend.
		// Purpose is to increase user experience in editor mode.

		if ( 'yes' !== $settings['show_cart_items'] ) {
			$this->hide_items_in_editor( 'cart_item' );
		}

		if ( 'yes' !== $settings['show_cart_edit'] ) {
			$this->hide_items_in_editor( 'sellkit-one-page-checkout-product-qty' );

			add_action( 'sellkit-checkout-widget-disable-quantity', [ Helper::instance(), 'checkout_order_hidden_quantity' ], 10, 2 );
		}

		if ( 'yes' !== $settings['show_shipping_method'] ) {
			$this->hide_items_in_editor( 'sellkit-one-page-shipping-methods' );
		}

		add_action( 'sellkit-checkout-widget-custom-coupon-form', function() use ( $settings ) {
			Local_Hooks::coupon_form( $settings );
		} );

		if ( 'yes' !== $settings['show_coupon_field'] ) {
			$this->hide_items_in_editor( 'coupon-form' );
		}

		if ( 'yes' !== $settings['order_summary_show_image'] ) {
			$this->hide_items_in_editor( 'sellkit-checkout-widget-item-image' );
		}
	}

	private function hide_items_in_editor( $class ) {
		echo sprintf(
			/** Translators : %s : class name */
			'<style>.%s { display:none !important; }</style>',
			$class
		);
	}
}
