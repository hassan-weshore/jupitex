<?php

namespace Sellkit\Elementor\Modules\Checkout\Classes;

defined( 'ABSPATH' ) || exit;

use Sellkit\Elementor\Modules\Checkout\Classes\{ Helper, Global_Hooks };
use Elementor\Plugin as Elementor;

/**
 * Local hooks.
 * Applies before widget.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @since 1.1.0
 */
class Local_Hooks {
	/**
	 * Create instance of class without construct.
	 *
	 * @since 1.1.0
	 * @return object
	 */
	public static function instance() {
		$class = new \ReflectionClass( __CLASS__ );
		return $class->newInstanceWithoutConstructor();
	}

	/**
	 * Class construct.
	 *
	 * @param array $settings widget settings.
	 * @param array $widget widget object.
	 * @since 1.1.0
	 */
	public function __construct( $settings, $widget ) {
		$this->settings = $settings;
		$this->widget   = $widget;
		$this->actions();
	}

	/**
	 * Actions.
	 * required actions to apply changes to woocommerce checkout shortcode.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	private function actions() {
		// Empty cart notice.
		add_action( 'woocommerce_before_checkout_form_cart_notices', [ $this, 'empty_cart_notice' ] );

		// sellkit wrapper for checkout widget.
		add_action( 'woocommerce_before_checkout_form', [ $this, 'open_multistep_wrap' ] );
		add_action( 'woocommerce_after_checkout_form', [ $this, 'close_multistep_wrap' ], 999 );

		// Add express section to widget.
		$this->add_express_checkout();

		// Replace Woocommerce templates that require huge changes.
		add_filter( 'woocommerce_locate_template', [ $this, 'template_replace' ], 1, 3 );

		// Remove coupon from it's default location. will add custom coupon form based on design at desired location.
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10, 1 );

		// Filter all woocommerce field right before printing them in our widget.
		add_filter( 'woocommerce_checkout_fields', [ $this, 'woocommerce_checkout_fields' ] );

		// Hide shipping fields section title.
		add_filter( 'sellkit-checkout-disable-shipping-fields-title', [ $this, 'check_to_disable_shipping_title' ] );

		// Customize shipping fields based on user desire.
		add_action( 'sellkit_checkout_shipping_fields', [ $this, 'sellkit_checkout_shipping_field' ], 10, 2 );

		// Customize billing fields based on user desire.
		add_action( 'sellkit_checkout_billing_fields', [ $this, 'sellkit_checkout_billing_field' ], 10, 2 );

		// Rename Shipping text to Shipping Method.
		add_filter( 'woocommerce_shipping_package_name', [ $this, 'rename_shipping_text' ] );

		// Remove login form from default location.
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );

		// Append widget settings to checkout form 2 hidden fields form_id post_id.
		add_action( 'woocommerce_checkout_before_customer_details', [ $this, 'widget_settings' ] );

		// Apply custom messages.
		$this->custom_messages();

		// Enable or Disable Sections based on user selection.
		add_action( 'woocommerce_before_checkout_form', function() {
			self::enable_disable_sections( $this->settings, [] );
		} );

		// Inject required scripts for google address autocomplete.
		add_action( 'sellkit-after-checkout-content', [ $this, 'inject_google_autocomplete' ] );

		// Order bump html.
		add_action( 'woocommerce_before_checkout_form', [ $this, 'order_bump' ] );

		// Bundle products.
		$this->bundled_products();
	}

	/**
	 * Add custom notice if cart is empty.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function empty_cart_notice() {
		global $woocommerce;
		$count = $woocommerce->cart->get_cart_contents_count();

		if ( 0 === $count ) {
			/* translators: %s: Empty cart message. */
			echo sprintf(
				'<div class="elementor-alert elementor-alert-info">%s</div>',
				$this->settings['empty_cart']
			);
		}
	}

	/**
	 * Opens a wrapper around widget checkout form.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function open_multistep_wrap() {
		?>
			<div id="sellkit-checkout-widget-id" class="sellkit-multistep-checkout-wrap sellkit-mobile-design-checkout-widget">
		<?php
	}

	/**
	 * Close opened wrapper around checkout form.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function close_multistep_wrap() {
		echo '</div>';
	}

	/**
	 * Inject express checkout section into the checkout form based on layout.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	private function add_express_checkout() {
		if ( ! sellkit()->has_pro ) {
			return;
		}

		$show_express = true;

		// Disable express checkout.
		if ( 'yes' !== $this->settings['show_express_checkout'] ) {
			$show_express = false;
		}

		$gateways = [
			'Paypal_For_Woocommerce',
			'Woo_Payment_Gateway',
			'Stripe_For_Woocommerce',
			'Klarma_Checkout_Woocommerce',
			'Amazon_Pay_Woocommerce',
			'Stripe_Woocommerce_Official',
		];

		foreach ( $gateways as $gateway ) {
			$class = 'Sellkit\Elementor\Modules\Checkout\Integrations\\' . $gateway;
			$class = new $class();
			$class->run();
		}

		if ( false === $show_express ) {
			return;
		}

		if ( 'one-page' === $this->settings['layout-type'] ) {
			add_action( 'sellkit_checkout_one_page_express_methods', [ $this, 'express_checkout_html' ] );
			return;
		}

		add_action( 'sellkit-checkout-step-a-begins', [ $this, 'express_checkout_html' ], 20 );
	}

	/**
	 * Replace Woocommerce templates that needs to be changed, locally.
	 * this template_replace does not affect default checkout page.
	 *
	 * @param string $template template name.
	 * @param string $template_name name.
	 * @param string $template_path path of template.
	 * @return string
	 * @since 1.1.0
	 */
	public function template_replace( $template, $template_name, $template_path ) {
		$basename = basename( $template );
		$our_path = sellkit()->plugin_dir() . 'includes/elementor/modules/checkout/';

		switch ( $basename ) {
			case 'form-checkout.php':
				$template = $our_path . 'templates/form-checkout.php';
				break;
			case 'form-login.php':
				$template = $our_path . 'templates/form-login.php';
				break;
			case 'form-shipping.php':
				$template = $our_path . 'templates/form-shipping.php';
				break;
			case 'form-billing.php':
				$template = $our_path . 'templates/form-billing.php';
				break;
			case 'payment.php':
				$template = $our_path . 'templates/payment.php';
				break;
			case 'payment-method.php':
				$template = $our_path . 'templates/payment-method.php';
				break;
			case 'review-order.php':
				$template = $our_path . 'templates/review-order.php';
				break;
			case 'cart-shipping.php':
				$template = $our_path . 'templates/cart-shipping.php';
				break;
			case 'cart-item-data.php':
				$template = $our_path . 'templates/cart-item-data.php';
				break;
			case 'terms.php':
				$template = $our_path . 'templates/terms.php';
				break;
		}

		return $template;
	}

	/**
	 * Customize shipping fields parameters based on user needs through widget options.
	 *
	 * @param array $default_fields woocommerce fields.
	 * @return array
	 * @since 1.1.0
	 */
	public function customize_shipping_field( $default_fields ) {
		$widget_shipping_fields = $this->settings['shipping_list'];
		$widget_field_slug      = Helper::instance()->get_user_defined_fields_slug( $widget_shipping_fields, 'shipping_list_field' );

		// Unset default fields.
		foreach ( $default_fields as $key => $field ) {
			if ( ! in_array( $key, $widget_field_slug, true ) ) {
				unset( $default_fields[ $key ] );
			}
		}

		return $default_fields;
	}

	/**
	 * Check to disable shipping fields section title.
	 *
	 * @since 1.2.1
	 */
	public function check_to_disable_shipping_title() {
		$widget_shipping_fields = $this->settings['shipping_list'];

		if ( 0 === count( $widget_shipping_fields ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Print shipping field in frontend using our customized fields.
	 *
	 * @param array  $fields : shipping fields.
	 * @param object $checkout : checkout object.
	 * @since 1.1.0
	 */
	public function sellkit_checkout_shipping_field( $fields, $checkout ) {
		$default_text = [
			'shipping_first_name',
			'shipping_last_name',
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_postcode',
			'shipping_city',
		];

		$default_select = [
			'shipping_country',
			'shipping_state',
		];

		foreach ( $fields as $key => $details ) {
			if ( in_array( $key, $default_text, true ) ) {
				$fields[ $key ]['type'] = 'text';
			}

			if ( in_array( $key, $default_select, true ) ) {
				$fields[ $key ]['type'] = 'select';
			}
		}

		$widget_shipping_fields = $this->settings['shipping_list'];
		$default_fields         = Helper::instance()->assign_settings_per_field( $fields, $widget_shipping_fields, 'shipping', $this->settings );

		$priority = array_column( $default_fields, 'priority' );
		array_multisort( $priority, SORT_ASC, $default_fields );

		foreach ( $default_fields as $key => $details ) {
			if ( ! array_key_exists( 'type', $details ) ) {
				continue;
			}

			$class = $details['type'];
			$class = '\Sellkit\Elementor\Modules\Checkout\Fields\\' . ucfirst( $class );
			$class = new $class();
			$field = '';

			$class->final_html_structure( $field, $details, $key );
		}
	}

	/**
	 * Customize billing fields parameters based on user needs through widget options.
	 *
	 * @param array $default_fields woocommerce fields.
	 * @return array
	 * @since 1.1.0
	 */
	public function customize_billing_field( $default_fields ) {
		$widget_billing_fields = $this->settings['billing_list'];
		$widget_field_slug     = Helper::instance()->get_user_defined_fields_slug( $widget_billing_fields, 'billing_list_field' );

		// Unset default fields.
		foreach ( $default_fields as $key => $field ) {
			if ( ! in_array( $key, $widget_field_slug, true ) && 'billing_email' !== $field ) {
				unset( $default_fields[ $key ] );
			}
		}

		return $default_fields;
	}

	/**
	 * Print billing fields in frontend using our customized fields.
	 *
	 * @param array  $fields : billing fields.
	 * @param object $checkout : checkout object.
	 * @since 1.1.0
	 */
	public function sellkit_checkout_billing_field( $fields, $checkout ) {
		$default_text = [
			'billing_first_name',
			'billing_last_name',
			'billing_company',
			'billing_address_1',
			'billing_address_2',
			'billing_postcode',
			'billing_city',
		];

		$default_select = [
			'billing_country',
			'billing_state',
		];

		$default_tel = [
			'billing_phone',
		];

		foreach ( $fields as $key => $details ) {
			if ( in_array( $key, $default_text, true ) ) {
				$fields[ $key ]['type'] = 'text';
			}

			if ( in_array( $key, $default_select, true ) ) {
				$fields[ $key ]['type'] = 'select';
			}

			if ( in_array( $key, $default_tel, true ) ) {
				$fields[ $key ]['type'] = 'tel';
			}
		}

		$widget_billing_fields = $this->settings['billing_list'];
		$default_fields        = Helper::instance()->assign_settings_per_field( $fields, $widget_billing_fields, 'billing', $this->settings );

		$priority = array_column( $default_fields, 'priority' );
		array_multisort( $priority, SORT_ASC, $default_fields );

		foreach ( $default_fields as $key => $details ) {
			// Never shot field that it's type is not defined.
			if ( ! array_key_exists( 'type', $details ) ) {
				continue;
			}

			// Billing email is already defined at top of page.
			if ( 'billing_email' === $key ) {
				continue;
			}

			$class = $details['type'];
			$class = '\Sellkit\Elementor\Modules\Checkout\Fields\\' . ucfirst( $class );
			$class = new $class();
			$field = '';

			$field = $class->final_html_structure( $field, $details, $key );
		}
	}

	/**
	 * Hook to fields before shortcode.
	 *
	 * @param array $default_fields woocommerce fields.
	 * @return array
	 * @since 1.1.0
	 */
	public function woocommerce_checkout_fields( $default_fields ) {
		$widget_billing_fields  = $this->settings['billing_list'];
		$widget_shipping_fields = $this->settings['shipping_list'];

		// Unset fields.
		$default_shipping_fields    = $default_fields['shipping'];
		$default_fields['shipping'] = $this->customize_shipping_field( $default_shipping_fields );

		// Unset fields.
		$default_billing_fields    = $default_fields['billing'];
		$default_fields['billing'] = $this->customize_billing_field( $default_billing_fields );

		foreach ( $widget_billing_fields as $data ) {
			$slug = $data['billing_list_field'];

			if ( 'yes' !== $data['billing_list_required'] && array_key_exists( $slug, $default_fields['billing'] ) ) {
				$default_fields['billing'][ $slug ]['required'] = false;
				unset( $default_fields['billing'][ $slug ]['required'] );
			}
		}

		foreach ( $widget_shipping_fields as $data ) {
			$slug = $data['shipping_list_field'];

			if ( 'yes' !== $data['shipping_list_required'] && array_key_exists( $slug, $default_fields['shipping'] ) ) {
				$default_fields['shipping'][ $slug ]['required'] = false;
				unset( $default_fields['shipping'][ $slug ]['required'] );
			}
		}

		return $default_fields;
	}

	/**
	 * Rename Shipping text
	 *
	 * @param string $name title of shipping methods.
	 * @return string
	 * @since 1.1.0
	 */
	public function rename_shipping_text( $name ) {
		return '<h4 id="shipping_header_title" class="shipping-method-header heading">' . __( 'Shipping Methods', 'sellkit' ) . '</h4>';
	}

	/**
	 * Adds widget settings as hidden input to checkout form, to be used in various places.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function widget_settings() {
		?>
			<input type="hidden" name="post_id" value="<?php echo esc_attr( self::get_current_post_id() ); ?>" />
			<input type="hidden" name="form_id" value="<?php echo esc_attr( $this->widget->get_id() ); ?>" />
		<?php
	}

	/**
	 * Get post ID based on document.
	 *
	 * @since 1.1.0
	 */
	public static function get_current_post_id() {
		if ( isset( Elementor::$instance->documents ) && ! empty( Elementor::$instance->documents->get_current() ) ) {
			return Elementor::$instance->documents->get_current()->get_main_id();
		}

		return get_the_ID();
	}

	/**
	 * Express checkout html.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function express_checkout_html() {
		ob_start();
		?>
			<section class="sellkit-checkout-widget-express-checkout sellkit-checkout-express-checkout-step-1">
				<fieldset class="express-box sellkit-checkout-widget-divider">
					<legend class="sellkit-express-checkout-legend heading">
						<?php echo __( 'Express Checkout', 'sellkit' ); ?>
					</legend>
					<div class="express-methods">
						<?php do_action( 'sellkit-checkout-widget-express-methods' ); ?>
					</div>
				</fieldset>
			</section>
			<section class="sellkit-checkout-widget-express-checkout sellkit-checkout-express-checkout-step-2">
				<fieldset id="sellkit-checkout-or-divider" class="divider-box sellkit-checkout-widget-divider">
					<legend class="heading"><?php echo __( 'OR', 'sellkit' ); ?></legend>
				</fieldset>
			</section>
		<?php
		$express = ob_get_clean();
		$express = apply_filters( 'sellkit-checkout-widget-express-checkout', $express );
		echo $express;
	}

	/**
	 * Apply custom Messages.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	private function custom_messages() {
		add_filter( 'sellkit_core/widgets/checkout/custom_message/already_have_account_text', function() {
			return $this->settings['already_have_account_text'];
		} );

		add_filter( 'sellkit_core/widgets/checkout/custom_message/login_toggle_text', function() {
			return $this->settings['login_toggle_text'];
		} );

		add_action( 'sellkit_core/widgets/checkout/custom_message/create_website_account', function() {
			$site = get_bloginfo( 'name' );
			$text = $this->settings['create_website_account'];

			echo str_replace( '{{website}}', $site, $text );
		} );

		add_filter( 'sellkit_core/widgets/checkout/custom_message/secure_transaction_text', function() {
			return $this->settings['secure_transaction_text'];
		} );

		add_filter( 'sellkit_core/widgets/checkout/custom_message/select_address_text', function() {
			return $this->settings['select_address_text'];
		} );

		add_filter( 'sellkit-checkout-place-order-btn-text', function( $default ) {
			if ( ! empty( $this->settings['place_order_btn_txt'] ) ) {
				return $this->settings['place_order_btn_txt'];
			}

			return $default;
		}, 10 );
	}

	/**
	 * Enable or disable some of checkout sections.
	 *
	 * @param array $settings widget settings.
	 * @param array $posted_data checkout form data.
	 * @return void
	 * @since 1.1.0
	 */
	public static function enable_disable_sections( $settings, $posted_data = [] ) {
		if ( Elementor::$instance->editor->is_edit_mode() || array_key_exists( 'sellkit-elementor-editor-mode', $posted_data ) ) {

			add_action( 'sellkit-checkout-after-coupon-form-ajax', [ Helper::instance(), 'editor_mode_extra_js' ] );

			// Let coupon form to be viewable in editor mode.
			add_action( 'sellkit-checkout-widget-custom-coupon-form', function() use ( $settings ) {
				Global_Hooks::instance()->coupon_form( $settings );
			} );

			return;
		}

		if ( wp_doing_ajax() ) {
			// Hide order item images.
			add_filter( 'sellkit/includes/elementor/modules/checkout/product-image', function( $img_html ) use ( $settings ) {
				if ( ! array_key_exists( 'order_summary_show_image', $settings ) ) {
					return $img_html;
				}

				return '';
			} );

			add_filter( 'sellkit-checkout-cart-item', function( $cart_item ) use ( $settings ) {
				if ( ! array_key_exists( 'show_cart_items', $settings ) ) {
					return $cart_item;
				}

				return '';
			} );

			// Disable order item quantity input.
			if ( array_key_exists( 'show_cart_edit', $settings ) || array_key_exists( 'bundle-products-force', $posted_data ) ) {
				add_action( 'sellkit-checkout-widget-disable-quantity', [ Helper::instance(), 'checkout_order_hidden_quantity' ], 10, 2 );
			}

			add_filter( 'woocommerce_shipping_package_name', function() {
				return '<h4 id="shipping_header_title" class="shipping-method-header heading">' . esc_html__( 'Shipping Methods', 'sellkit' ) . '</h4>';
			} );

			add_filter( 'woocommerce_cart_ready_to_calc_shipping', function() use ( $settings ) {
				if ( ! array_key_exists( 'show_shipping_method', $settings ) ) {
					return true;
				}

				return false;
			} );

			add_action( 'sellkit-checkout-widget-custom-coupon-form', function() use ( $settings ) {
				Global_Hooks::instance()->coupon_form( $settings );
			}  );

			return;
		}

		// Normal mode when page is loading.
		add_filter( 'woocommerce_cart_ready_to_calc_shipping', function() use ( $settings ) {
			if ( 'yes' === $settings['show_shipping_method'] ) {
				return true;
			}

			return false;
		}, 10 );

		add_filter( 'sellkit/includes/elementor/modules/checkout/product-image', function( $img_html ) use ( $settings ) {
			if ( 'yes' === $settings['order_summary_show_image'] ) {
				return $img_html;
			}

			return '';
		} );

		add_filter( 'sellkit-checkout-cart-item', function( $cart_item ) use ( $settings ) {
			if ( 'yes' === $settings['show_cart_items'] ) {
				return $cart_item;
			}

			return '';
		}, 10 );

		// Disable order item quantity input.
		if ( array_key_exists( 'show_cart_edit', $settings ) ) {
			add_action( 'sellkit-checkout-widget-disable-quantity', [ Helper::instance(), 'checkout_order_hidden_quantity' ], 10, 2 );

			add_filter( 'sellkit/includes/elementor/modules/checkout/quanity/class', function( $classes ) {
				return $classes .= ' sellkit-checkout-widget-d-block';
			} );
		}

		add_filter( 'sellkit-checkout-place-order-btn-text', function( $place_order_btn ) {
			if ( ! empty( $settings['place_order_btn_txt'] ) ) {
				return $settings['place_order_btn_txt'];
			}

			return $place_order_btn;
		}, 10 );

		add_action( 'sellkit-checkout-widget-custom-coupon-form', function() use ( $settings ) {
			if ( 'yes' !== $settings['show_coupon_field'] ) {
				return;
			}

			self::coupon_form( $settings );
		} );
	}

	/**
	 * Inject google autocomplete address script if enabled for shipping/billing section.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function inject_google_autocomplete() {
		if ( ! sellkit()->has_pro ) {
			return;
		}

		\Sellkit_Elementor_Checkout_Pro_Module::checkout_google_autocomplete_address( $this->settings );
	}

	/**
	 * Place custom coupon form in checkout order-review based design.
	 * ! we have used same form in Global_Hooks. but this one for first load of page.
	 *
	 * @param array $settings widget settings.
	 * @see sellkit_Core\Raven\Modules\Checkout\Classes\Global_Hooks::coupon_form.
	 * @return void
	 * @since 1.1.0
	 */
	public static function coupon_form( $settings ) {
		echo '<tr class="coupon-form border-none"><td colspan="2">';
			self::form( $settings );
		echo '</td></tr>';
	}

	/**
	 * New custom coupon form HTML.
	 *
	 * @param array $settings widget settings.
	 * @return void
	 * @since 1.1.0
	 */
	public static function form( $settings ) {
		$class = 'sellkit-custom-coupon-form-d-none';
		if ( ! array_key_exists( 'coupon_field_type', $settings ) || 'normal' === $settings['coupon_field_type'] ) {
			$class = 'sellkit-normal-coupon-form';
		}
		?>
			<?php if ( array_key_exists( 'coupon_field_type', $settings ) && 'collapsible' === $settings['coupon_field_type'] ) : ?>
				<span id="copoun_toggle" class="copoun_toggle sellkit-coupon-toggle sellkit-checkout-widget-links">
					<?php echo __( 'Have a coupon? Click here to enter your code', 'sellkit' ); ?>
				</span>
				<?php $class .= ' sellkit-checkout-collapsible'; ?>
				<?php Helper::instance()->editor_mode_extra_js(); ?>
			<?php endif; ?>
			<div class="sellkit-custom-coupon-form <?php echo $class; ?>">
				<p class="jx-form-row-first">
					<input class="jx-coupon" type="text" placeholder="<?php esc_attr_e( 'Enter Promo code', 'sellkit' ); ?>" />
				</p>

				<p class="jx-form-row-last">
					<span class="sellkit-checkout-widget-secondary-button jx-apply-coupon sellkit-apply-coupon" >
						<?php esc_html_e( 'Apply', 'sellkit' ); ?>
					</span>
				</p>
			</div>
		<?php
	}

	/**
	 * Insert bundled product.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function bundled_products() {
		global $wp_query;
		$query  = $wp_query->query_vars;
		$option = 'default';

		if ( ! array_key_exists( 'funnel_product_settings', $query ) ) {
			return;
		}

		if ( ! empty( $query['funnel_product_settings'] ) ) {
			$option = $query['funnel_product_settings'];
		}

		if ( 'default' === $option ) {
			return;
		}

		add_action( 'sellkit_checkout_required_hidden_fields', function() use ( $option ) {
			?>
				<input type="hidden" name="sellkit-bundle-products" value="<?php echo esc_attr( $option ); ?>" >
			<?php
		}, 10 );

		self::bundle_product_action( $option );
	}

	/**
	 * Display bundle products.
	 *
	 * @param string $option bundle products type.
	 * @since 1.1.0
	 * return void
	 */
	public static function bundle_product_action( $option ) {
		add_action( 'sellkit-checkout-widget-disable-quantity', [ Helper::instance(), 'checkout_order_hidden_quantity' ], 10, 2 );

		if ( 'force-products' === $option || Elementor::$instance->editor->is_edit_mode() ) {
			return;
		}

		$fields_type = 'radio';
		$products    = WC()->cart->get_cart();
		$default     = null;
		$default_q   = null;
		$readonly    = 'readonly';

		if ( 'allow-buyers' === $option ) {
			$fields_type = 'checkbox';
			$readonly    = '';
		}

		ob_start();
		?>
			<section class="sellkit-checkout-bundled-products">
				<div class="sellkit-checkout-bundled-inner-wrap">
					<span class="sellkit-checkout-bundled-header heading"><?php echo esc_html__( 'Your Products', 'sellkit' ); ?></span>
					<table class="sellkit-checkout-bundled-products-table">
						<thead>
							<tr class="sellkit-checkout-bundled-products-head-row">
								<th class="sellkit-head-row-title"><?php echo esc_html__( 'Product', 'sellkit' ); ?></th>
								<th class="sellkit-head-row-quantity"><?php echo esc_html__( 'Quantity', 'sellkit' ); ?></th>
								<th class="sellkit-head-row-price"><?php echo esc_html__( 'Price', 'sellkit' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $products as $cart_item_key => $cart_item ) : ?>
								<?php
									$_product  = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
									$default   = $_product->get_id();
									$default_q = $cart_item['quantity'];
									$unique_id = 'bundle-unique-id-' . $default;
								?>
								<tr class="sellkit-checkout-bundled-products-item">
									<td class="sellkit-checkout-bundled-title">
										<input
											type="<?php echo esc_attr( $fields_type ); ?>"
											value="<?php echo esc_attr( $_product->get_id() ); ?>"
											name="sellkit-checkout-bundle-item"
											class="sellkit-checkout-bundle-item"
											id="<?php echo esc_attr( $unique_id ); ?>"
											<?php echo ( array_key_last( $products ) === $cart_item_key ) ? 'checked' : ''; ?>
										>
										<label for="<?php echo esc_attr( $unique_id ); ?>">
											<?php echo $_product->get_name(); ?>
										</label>
									</td>
									<td class="sellkit-checkout-bundled-quantity">
										<input type="number" <?php echo esc_attr( $readonly ); ?> min="1" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" data-id="<?php echo esc_attr( $cart_item_key ); ?>" class="sellkit-checkout-single-bundle-item-quantity" >
									</td>
									<td class="sellkit-checkout-bundled-price">
										<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
									</td>
								<tr>
								<?php if ( array_key_last( $products ) !== $cart_item_key ) : ?>
									<tr>
										<td colspan="3" class="sellkit-checkout-bundled-spacer-row">
											<hr>
										</td>
									</tr>
								<?php endif; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</section>
		<?php
		$bundle_html = ob_get_clean();

		add_action( 'sellkit-bundled-products-position', function() use ( $bundle_html ) {
			echo $bundle_html;
		} );

		if ( ! wp_doing_ajax() ) {
			WC()->cart->empty_cart();

			if ( null !== $default ) {
				WC()->cart->add_to_cart( $default, $default_q );

				if ( ! WC()->cart->needs_shipping() ) {
					wp_add_inline_script( 'sellkit-initialize-widgets', 'var sellkitCheckoutShipping = true', 'before' );
				}
			}

			Global_Hooks::make_changes_after_cart_item_edit( get_the_id() );
		}
	}

	/**
	 * Trigger to activate order bumb or not for this checkout.
	 *
	 * @param array $data checkout form data.
	 * @since 1.1.0
	 * @return void
	 */
	public static function order_bump( $data = [] ) {
		if ( 'woocommerce_before_checkout_form' === current_action() ) {
			self::order_bump_frontend_manager();
			return;
		}
	}

	/**
	 * Order bump to manage frontend.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	private static function order_bump_frontend_manager() {
		global $wp_query;
		$query = $wp_query->query_vars;

		if ( ! array_key_exists( 'bump_data', $query ) ) {
			return;
		}

		$bumps = $query['bump_data'];

		foreach ( $bumps as $bump ) {
			if ( ! array_key_exists( 'products', $bump['data'] ) ) {
				continue;
			}

			$design   = $bump['data']['design'];
			$products = $bump['data']['products'];

			add_action( $design['sellkit_funnels_bump_position'], function() use ( $design, $products ) {
				self::bump_html( $design, $products );
			}, 5 );
		}
	}

	/**
	 * Order bump html.
	 *
	 * @param array $design design properties.
	 * @param array $products products details.
	 * @since 1.1.0
	 * @return void
	 */
	public static function bump_html( $design, $products ) {
		$list = $products['list'];
		$ids  = [];

		foreach ( $list as $id => $details ) {
			$ids[]    = $id;
			$qty      = $details['quantity'];
			$discount = $details['discount'];
			$type     = $details['discountType'];
		}

		$ids_string = implode( '|', $ids );
		$product    = wc_get_product( $ids_string );
		$qty        = ( empty( $qty ) ) ? 1 : $qty;

		if ( empty( $product ) || ! $product->get_id() ) {
			return;
		}

		$unique_id = 'bump-title-' . $ids_string;

		$class = '';
		if (
			'sellkit-checkout-before-order-summary' === current_action() ||
			'sellkit-checkout-after-order-summary' === current_action()
		) {
			$class = 'sellkit-bump-review-order';
		}
		?>
			<div class="sellkit-checkout-step-bump-wrapper <?php echo esc_attr( $class ); ?>" >
				<div class="sellkit-checkout-bump-order-header">
					<div class="sellkit-bump-order-left-header">
						<img src="<?php echo esc_attr( sellkit()->plugin_assets_url() . 'img/right-arrow.svg' ); ?>" >
						<input
							type="checkbox"
							value="<?php echo esc_attr( $ids_string ); ?>"
							class="sellkit-checkout-bump-order-products"
							data-qty="<?php echo esc_attr( $qty ); ?>"
							name="sellkit_bump_data_<?php echo esc_attr( $ids_string ); ?>"
							id="<?php echo esc_html( $unique_id ); ?>"
						>
						<label
							for="<?php echo esc_attr( $unique_id ); ?>"
							class="sellkit-checkout-order-bump-title"
						>
							<?php echo esc_html( $design['sellkit_funnels_bump_checkbox_label'] ); ?>
						</label>
					</div>
					<div class="sellkit-bump-order-right-header">
						<span class="sellkit-checkout-order-bump-price">
							<?php
								$discounted_price = Helper::calculate_discount( (int) $ids_string, $type, $discount );
								$sale_price       = $product->get_sale_price();
								$regular_price    = $product->get_regular_price();
								$main_price       = ( strpos( $type, 'sale' ) !== false ) ? $sale_price : $regular_price;

								if ( floatval( $main_price ) > floatval( $discounted_price ) ) {
									?>
										<del aria-hidden="true">
											<span class="woocommerce-Price-amount amount">
												<bdi>
													<span class="woocommerce-Price-currencySymbol">
														<?php echo wc_price( $main_price ); ?>
													</span>
												</bdi>
											</span>
										</del>
										<bdi class="bump-price-bolded"><?php echo wc_price( $discounted_price ); ?></bdi>
									<?php
								} else {
									?>
										<bdi><?php echo wc_price( $main_price ); ?></bdi>
									<?php
								}
							?>
						</span>
					</div>
				</div>
				<div class="sellkit-checkout-bump-order-body" >
					<div class="sellkit-bump-order-left-body">
						<?php $image = wp_get_attachment_image_src( $product->get_image_id() ); ?>
						<?php if ( 'true' === $design['sellkit_funnels_bump_product_image'] && ! empty( $image[0] ) ) : ?>
						<img src="<?php echo esc_attr( $image[0] ); ?>" >
						<?php endif; ?>
					</div>
					<div class="sellkit-bump-order-right-body">
						<div class="sellkit-bump-order-description">
							<?php echo $design['sellkit_content']; ?>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
}
