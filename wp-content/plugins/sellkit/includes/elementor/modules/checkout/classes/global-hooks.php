<?php

namespace Sellkit\Elementor\Modules\Checkout\Classes;

defined( 'ABSPATH' ) || exit;

use Sellkit\Elementor\Modules\Checkout\Classes\{ Local_Hooks, Helper };
use Sellkit\Funnel\Steps\Checkout;

/**
 * Global hooks.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @since 1.1.0
 */
class Global_Hooks {
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
	 * required actions that affects woocommerce global checkout shortcode / page.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		// Create empty shortcode to simulate our widget page as checkout page.
		add_shortcode( 'sellkit_checkout_widget_simulated', function() {
			return '';
		} );

		add_action( 'wp_ajax_sellkit_checkout_ajax_handler', [ $this, 'ajax_handler' ] );
		add_action( 'wp_ajax_nopriv_sellkit_checkout_ajax_handler', [ $this, 'ajax_handler' ] );

		// Add shipping total to review order by custom hook.
		add_action( 'sellkit-checkout-widget-display-shipping-price', [ $this, 'add_shipping_total_to_review_order' ] );

		// Modify checkout order-review item.
		add_action( 'sellkit-one-page-checkout-custom-order-item', [ $this, 'modify_checkout_order_item' ], 1, 4 );

		// Modify sent data before processing & validate order.
		add_filter( 'woocommerce_checkout_posted_data', [ $this, 'modify_order_data_before_validate' ], 10, 1 );

		// Validate user defined fields.
		add_action( 'woocommerce_checkout_process', [ $this, 'validate_user_defined_fields' ] );

		// Save user defined fields.
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'save_user_defined_fields' ] );

		// Add custom shipping field to email.
		add_filter( 'woocommerce_order_get_formatted_shipping_address', [ $this, 'attach_user_shipping_fields_to_order_email' ], 10, 3 );

		// Add custom billing field to email.
		add_filter( 'woocommerce_order_get_formatted_billing_address', [ $this, 'attach_user_billing_fields_to_order_email' ], 10, 3 );

		// Filter checkout ajax fragment.
		add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'checkout_fragment' ] );

		// Simulate checkout page for our widget.
		add_action( 'wp', [ $this, 'simulate_our_widget_page_as_checkout' ] );

		// Fix shipping method price change on ajax.
		// Manage bump order discounts.
		// Template replacement.
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'sellkit_checkout_during_ajax' ], 10, 1 );

		add_filter( 'sellkit-shipping-methods-choosen-method', [ $this, 'sellkit_default_shipping_method' ], 10, 1 );

		// Modify discounted products.
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_cart_calculate' ], 999 );
	}

	/**
	 * Handle ajax calls.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function ajax_handler() {
		check_ajax_referer( 'sellkit_elementor', 'nonce' );

		$sub_action = filter_input( INPUT_POST, 'sub_action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( method_exists( $this, $sub_action ) ) {
			call_user_func( [ $this, $sub_action ] );
			return;
		}

		wp_send_json_error();
	}

	/**
	 * Modify checkout ajax response HTML.
	 *
	 * @param array $response woocommerce checkout page ajax response.
	 * @return array
	 * @since 1.1.0
	 */
	public function checkout_fragment( $response ) {
		// Get posted data and identify if it's been sent by jupiter widget or not.
		$checkout_form_data = filter_input( INPUT_POST, 'post_data', FILTER_DEFAULT );
		$checkout_form_data = explode( '&', $checkout_form_data );
		$posted_data        = [];

		foreach ( $checkout_form_data as $input ) {
			$item = explode( '=', urldecode( $input ) );

			$posted_data[ trim( $item[0] ) ] = $item[1];
		}

		// Identify if it's been sent by jupiter checkout widget or not.
		if ( ! array_key_exists( 'form_id', $posted_data ) && ! array_key_exists( 'post_id', $posted_data ) ) {
			// Default woocommerce checkout.
			return $response;
		}

		// Get widget settings.
		$widget_settings = Helper::instance()->retrieve_checkout_widget_settings( $posted_data['post_id'], $posted_data['form_id'] );

		// Order review fragment.
		ob_start();
		woocommerce_order_review();
		$order_review = ob_get_clean();

		// Payment fragment.
		ob_start();
		woocommerce_checkout_payment();
		$payment = ob_get_clean();

		// Shipping method fragment.
		ob_start();
		echo '<section class="sellkit-one-page-shipping-methods">';
		wc_cart_totals_shipping_html();
		echo '</section>';
		$shipping_method = ob_get_clean();

		$response['.woocommerce-checkout-review-order-table'] = $order_review;
		$response['.woocommerce-checkout-payment']            = $payment;
		$response['.sellkit-one-page-shipping-methods']       = '';

		if ( ! array_key_exists( 'show_shipping_method', $widget_settings ) ) {
			$response['.sellkit-one-page-shipping-methods'] = $shipping_method;
		}

		return $response;
	}

	/**
	 * Apply coupon code using ajax.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function apply_coupon() {
		$code = filter_input( INPUT_POST, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( empty( $code ) ) {
			wp_send_json_error();
		}

		$result = WC()->cart->add_discount( $code );
		wp_send_json_success( $result );
	}

	/**
	 * Update cart item quantity in checkout page by ajax.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function change_cart_item_qty() {
		$qty       = filter_input( INPUT_POST, 'qty', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$id        = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$action    = filter_input( INPUT_POST, 'mode', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$funnel_id = filter_input( INPUT_POST, 'related_checkout', FILTER_SANITIZE_NUMBER_INT );

		self::make_changes_after_cart_item_edit( $funnel_id );

		if ( 'add' === $action ) {
			WC()->cart->add_to_cart( $id, $qty );
			self::make_changes_after_cart_item_edit( $funnel_id );
			wp_send_json_success();
		}

		if ( 'remove' === $action ) {
			$post_type = get_post_type( $id );

			if ( 'product_variation' === $post_type ) {
				foreach ( WC()->cart->get_cart() as $item_key => $item ) {
					if ( $item['variation_id'] === (int) $id ) {
						WC()->cart->remove_cart_item( $item_key ); // we remove it.
						break; // stop the loop.
					}
				}

				self::make_changes_after_cart_item_edit( $funnel_id );

				wp_send_json_success();
			}

			$product_cart_id = WC()->cart->generate_cart_id( $id );
			$cart_item_key   = WC()->cart->find_product_in_cart( $product_cart_id );

			if ( $cart_item_key ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}

			self::make_changes_after_cart_item_edit( $funnel_id );

			wp_send_json_success();
		}

		( $qty > 0 ) ? WC()->cart->set_quantity( $id, $qty ) : WC()->cart->remove_cart_item( $id );

		self::make_changes_after_cart_item_edit( $funnel_id );

		wp_send_json_success();
	}

	/**
	 * Apply changes after editing cart items.
	 *
	 * @since 1.2.5
	 * @param int $funnel_id funnel step id.
	 */
	public static function make_changes_after_cart_item_edit( $funnel_id ) {
		if ( empty( $funnel_id ) ) {
			return;
		}

		$funnel_data       = get_post_meta( $funnel_id, 'step_data', true );
		$optimization_data = ! empty( $funnel_data['data']['optimization'] ) ? $funnel_data['data']['optimization'] : '';

		if ( empty( $optimization_data ) ) {
			return;
		}

		// Help to apply funnel discount prices and coupons when changing product quantity.
		self::apply_discounted_prices( wc()->cart, $funnel_id );
		self::apply_discounted_prices( wc()->cart, $funnel_id, 'bumps' );

		if ( Checkout::apply_coupon_validation( $optimization_data ) ) {
			WC()->cart->remove_coupons();

			foreach ( $optimization_data['auto_apply_coupons'] as $auto_apply_coupon ) {
				wc()->cart->add_discount( get_the_title( $auto_apply_coupon['value'] ) );
			}

			wc_clear_notices();
		}
	}

	/**
	 * Place shipping price based on design to checkout order-review.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function add_shipping_total_to_review_order() {
		?>
			<tr class="sellkit-shipping-total">
				<th><?php echo __( 'Shipping', 'sellkit' ); ?></th>
				<td>
					<?php
						$price = WC()->cart->get_shipping_total();

						if ( WC()->cart->display_prices_including_tax() ) {
							$price = WC()->cart->get_shipping_total() + WC()->cart->shipping_tax_total;
						}

						echo wc_price( $price );
					?>
				</td>
			</tr>
		<?php
	}

	/**
	 * Modify checkout review-order based on design.
	 *
	 * @return void
	 * @param string $row html string of cart row.
	 * @param object $product product object.
	 * @param array  $cart_item cart item information.
	 * @param string $cart_item_key cart item unique key.
	 * @since 1.1.0
	 */
	public function modify_checkout_order_item( $row, $product, $cart_item, $cart_item_key ) {
		$cart_items = WC()->cart->get_cart();

		//phpcs:disable
		ob_start();
			?>
				<div style="display: inline-block" class="sellkit-checkout-widget-item-image">
					<?php echo $product->get_image(); ?>
				</div>
			<?php
		$img_html = ob_get_clean();
		$img_html = apply_filters( 'sellkit/includes/elementor/modules/checkout/product-image', $img_html, $product, $cart_item_key );

		ob_start();

		$extra_class = '';
		if ( $cart_items[ $cart_item_key ] === end( $cart_items ) ) {
			$extra_class = ' last-cart-item';
		}
		?>
			<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ) . $extra_class; ?>">

				<td class="product-name sellkit-one-page-checkout-product-name">
					<?php
						echo $img_html;

						$fix_style = '';

						if ( '' === $img_html ) {
							$fix_style = 'margin-left: 0px;';
						}
					?>

					<div class="name-price" style="<?php echo esc_attr( $fix_style ); ?>">
						<span style="display:inline-block">
							<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;'; ?>
						</span>
						<span class="sellkit-checkout-variations">
							<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<?php echo apply_filters( 'sellkit-checkout-widget-disable-quantity', '<input type="number" data-id="' . esc_attr( $cart_item_key ) . '" value="' . esc_attr( $cart_item['quantity'] ) . '" class="sellkit-one-page-checkout-product-qty" >', $cart_item['quantity'] ); ?>
						<?php do_action( 'sellkit-checkout-editor-mode-quantity', $cart_item['quantity'] ); ?>
					</div>
				</td>
				<td class="product-total sellkit-one-page-checkout-product-price">
					<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
		<?php
		$cart_item = ob_get_clean();

		echo apply_filters( 'sellkit-checkout-cart-item' , $cart_item );
		//phpcs:enable
	}

	/**
	 * Modify fields after pressing place order button.
	 * we hooked here because some removed fields still get checked and have not removed by previous hooks.
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @param array $data checkout form data.
	 * @return array $data data of woocommerce checkout form.
	 * @since 1.1.0
	 */
	public function modify_order_data_before_validate( $data ) {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$form_id = filter_input( INPUT_POST, 'form_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( empty( $post_id ) && empty( $form_id ) ) {
			return $data;
		}

		$settings = Helper::instance()->retrieve_checkout_widget_settings( $post_id, $form_id );

		if ( empty( $settings ) ) {
			return $data;
		}

		$widget_shipping_field = Helper::instance()->get_user_defined_fields_slug( $settings['shipping_list'], 'shipping_list_field' );
		$widget_billing_field  = Helper::instance()->get_user_defined_fields_slug( $settings['billing_list'], 'billing_list_field' );

		$default_shipping_fields = Helper::instance()->shipping_fields();
		$default_billing_fields  = Helper::instance()->billing_fields();

		// Unset removed shipping fields.
		foreach ( $default_shipping_fields as $field => $details ) {
			if ( ! in_array( $field, $widget_shipping_field, true ) ) {
				unset( $data[ $field ] );
			}
		}

		// Unset removed billing fields.
		foreach ( $default_billing_fields as $field => $details ) {
			if ( ! in_array( $field, $widget_billing_field, true ) && 'billing_email' !== $field ) {
				unset( $data[ $field ] );
			}
		}

		$mail = filter_input( INPUT_POST, 'billing_email', FILTER_SANITIZE_EMAIL );

		if ( ! array_key_exists( 'billing_email', $data ) && ! empty( $mail ) ) {
			$data['billing_email'] = $mail;
		}

		return $data;
	}

	/**
	 * Login user by ajax.
	 * sign user in by using form included in widget.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function auth_user() {
		$email  = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
		$pass   = filter_input( INPUT_POST, 'pass', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$verify = filter_var( $email, FILTER_VALIDATE_EMAIL );

		if ( ! $verify || empty( $email ) || empty( $pass ) ) {
			wp_send_json_error( __( 'Email or password field is not valid.', 'sellkit' ) );
		}

		$result = wp_authenticate( $email, $pass );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		wp_clear_auth_cookie();
		wp_set_current_user( $result->ID );
		wp_set_auth_cookie( $result->ID );

		wp_send_json_success( $result );
	}

	/**
	 * Get widget settings in ajax calls using form and post id.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	private function widget_settings() {
		$form_id = filter_input( INPUT_POST, 'form_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		if ( empty( $form_id ) && empty( $post_id ) ) {
			return;
		}

		$widget_settings = Helper::instance()->retrieve_checkout_widget_settings( $post_id, $form_id );

		return $widget_settings;
	}

	/**
	 * Validate user defined custom value.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function validate_user_defined_fields() {
		$widget_settings = $this->widget_settings();

		if ( empty( $widget_settings ) ) {
			return;
		}

		$widget_shipping_fields = $widget_settings['shipping_list'];
		$widget_billing_fields  = $widget_settings['billing_list'];

		Helper::instance()->validate_user_defined_fields( $widget_shipping_fields, $widget_billing_fields );
		Helper::instance()->make_sure_to_convert_required_field_to_optional( $widget_shipping_fields, $widget_billing_fields );
	}

	/**
	 * Save user defined custom fields value to database.
	 *
	 * @param int $order_id id of woocommerce order.
	 * @return void
	 * @since 1.1.0
	 */
	public function save_user_defined_fields( $order_id ) {
		$widget_settings = $this->widget_settings();

		if ( empty( $widget_settings ) ) {
			return;
		}

		$widget_shipping_fields = $widget_settings['shipping_list'];
		$widget_billing_fields  = $widget_settings['billing_list'];

		Helper::instance()->save_user_defined_fields( $widget_shipping_fields, $widget_billing_fields, $order_id );
	}

	/**
	 * Attach user defined shipping field values to order emails.
	 *
	 * @param string $address address.
	 * @param string $raw_address raw address.
	 * @param object $order woocommerce order object.
	 * @return string
	 * @since 1.1.0
	 */
	public function attach_user_shipping_fields_to_order_email( $address, $raw_address, $order ) {
		$order_id      = $order->get_id();
		$custom_fields = get_post_meta( $order_id, 'sellkit_checkout_widget_custom_field_of_order', true );

		if ( empty( $custom_fields ) || ! is_array( $custom_fields ) ) {
			return $address;
		}

		foreach ( $custom_fields as $field ) {
			if ( 'yes' !== $field['show_in_email'] ) {
				continue;
			}

			$key   = $field['key_name'];
			$value = get_post_meta( $order_id, $key, true );

			if ( strpos( $key, 'shipping' ) !== false ) {
				$address .= '<br>' . $value;
			}
		}

		return $address;
	}

	/**
	 * Attach user defined billing field values to order emails.
	 *
	 * @param string $address address.
	 * @param string $raw_address raw address.
	 * @param object $order woocommerce order object.
	 * @return string
	 * @since 1.1.0
	 */
	public function attach_user_billing_fields_to_order_email( $address, $raw_address, $order ) {
		$order_id      = $order->get_id();
		$custom_fields = get_post_meta( $order_id, 'sellkit_checkout_widget_custom_field_of_order', true );

		if ( empty( $custom_fields ) || ! is_array( $custom_fields ) ) {
			return $address;
		}

		foreach ( $custom_fields as $field ) {
			if ( 'yes' !== $field['show_in_email'] ) {
				continue;
			}

			$key   = $field['key_name'];
			$value = get_post_meta( $order_id, $key, true );

			if ( strpos( $key, 'billing' ) !== false ) {
				$address .= '<br>' . $value;
			}
		}

		return $address;
	}

	/**
	 * Place custom coupon form in checkout order-review based design.
	 * We have used same form in Local_Hooks. but this one is for ajax response.
	 *
	 * @see sellkit_Core\Raven\Modules\Checkout\Classes\Local_Hooks::coupon_form.
	 * @param array $settings widget settings.
	 * @return void
	 * @since 1.1.0
	 */
	public function coupon_form( $settings ) {
		if ( array_key_exists( 'show_coupon_field', $settings ) ) {
			return;
		}

		echo '<tr class="coupon-form border-none"><td colspan="2">';
			Local_Hooks::form( $settings );
		echo '</td></tr>';

		do_action( 'sellkit-checkout-after-coupon-form-ajax' );
	}

	/**
	 * Look for an email if exists or not.
	 * Is used for login process.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function search_for_email() {
		$email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
		$valid = filter_var( $email, FILTER_VALIDATE_EMAIL );

		if ( ! $valid || empty( $email ) ) {
			wp_send_json_error();
		}

		$check = email_exists( $email );

		if ( false === $check ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * Look for an username if exists or not.
	 * Is used for register process.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function search_for_username() {
		$username = filter_input( INPUT_POST, 'user', FILTER_SANITIZE_EMAIL );

		if ( empty( $username ) ) {
			wp_send_json_error();
		}

		$username = sanitize_user( $username );
		$check    = username_exists( $username );

		if ( false !== $check ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * Validate postcode via ajax.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function validate_postcode() {
		$country  = filter_input( INPUT_POST, 'country_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$postcode = filter_input( INPUT_POST, 'post_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$parent   = filter_input( INPUT_POST, 'parent', FILTER_DEFAULT );

		$postcode = wc_format_postcode( $postcode, $country );
		$valid    = \WC_Validation::is_postcode( $postcode, $country );

		if ( ! $valid ) {
			wp_send_json_error( $parent );
		}

		wp_send_json_success( $parent );
	}

	/**
	 * Validate phone number via ajax.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function validate_phone_number() {
		$phone  = filter_input( INPUT_POST, 'phone_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$parent = filter_input( INPUT_POST, 'parent', FILTER_DEFAULT );

		$valid = \WC_Validation::is_phone( $phone );

		if ( ! $valid ) {
			wp_send_json_error( $parent );
		}

		wp_send_json_success( $parent );
	}

	/**
	 * Simulate checkout page where our widget is present. using empty shortcode [sellkit_checkout_widget_simulated].
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function simulate_our_widget_page_as_checkout() {
		$page_id = get_the_ID();
		$content = get_post_field( 'post_content', $page_id );

		if ( false === strpos( $content, 'woocommerce_checkout' ) ) {
			return;
		}

		add_filter( 'woocommerce_is_checkout', function() {
			return true;
		} );

		if ( false === strpos( $content, 'sellkit_checkout_widget_simulated' ) ) {
			return;
		}

		add_filter( 'theme_mod_jupiterx_jupiterx_checkout_cart_elements', function() {
			return [];
		}, 10 );
	}

	/**
	 * Integrate user country with our widget.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function set_customer_details_ajax() {
		$country          = filter_input( INPUT_POST, 'country', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$state            = filter_input( INPUT_POST, 'state', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$shipping_country = filter_input( INPUT_POST, 'shipping_country', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$shipping_state   = filter_input( INPUT_POST, 'shipping_state', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! empty( $country ) ) {
			setcookie( 'sellkit-checkout-billing-cc', $country, time() + ( 86400 * 3 ), '/' );
			setcookie( 'sellkit-checkout-billing-state', $state, time() + ( 86400 * 3 ), '/' );
		}

		if ( ! empty( $shipping_country ) ) {
			setcookie( 'sellkit-checkout-shipping-cc', $shipping_country, time() + ( 86400 * 3 ), '/' );
			setcookie( 'sellkit-checkout-shipping-state', $shipping_state, time() + ( 86400 * 3 ), '/' );
		}

		wp_send_json_success();
	}

	/**
	 * State lookup using postcode and country.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function sellkit_state_lookup_by_postcode() {
		$country  = filter_input( INPUT_POST, 'country_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$postcode = filter_input( INPUT_POST, 'postcode_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$api      = 'https://api.zippopotam.us/' . $country . '/' . $postcode;

		if ( empty( $country ) || empty( $postcode ) ) {
			wp_send_json_error( esc_html__( 'Not valid inputs', 'sellkit' ) );
		}

		$response = wp_remote_get( $api );

		if ( is_wp_error( $response ) || ! is_array( $response ) ) {
			wp_send_json_error( esc_html__( 'Server error.', 'sellkit' ) );
		}

		if ( '{}' === $response['body'] ) {
			// Country or postcode is not supported. it's kina an error but we show nothing in frontend.
			wp_send_json_error( '' );
		}

		$body = json_decode( $response['body'], true );

		wp_send_json_success( $body );
	}

	/**
	 * Modify cart contents using bundled products.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function sellkit_checkout_modify_cart_by_bundle_products() {
		$qty         = filter_input( INPUT_POST, 'qty', FILTER_SANITIZE_NUMBER_INT );
		$id          = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
		$type        = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$checkout_id = filter_input( INPUT_POST, 'checkout_id', FILTER_SANITIZE_NUMBER_INT );
		$modify      = filter_input( INPUT_POST, 'modify', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$key         = filter_input( INPUT_POST, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! empty( $key ) ) {
			foreach ( WC()->cart->get_cart() as $item_key => $item ) {
				if ( $item_key === $key ) {
					WC()->cart->set_quantity( $item_key, $qty, true );
					break; // stop the loop.
				}
			}

			wp_send_json_success();
		}

		if ( 'radio' === $type ) {
			$step_data     = get_post_meta( $checkout_id, 'step_data', true );
			$products      = $step_data['data']['products']['list'];
			$selected      = $products[ $id ];
			$real_quantity = ( empty( $selected['quantity'] ) ) ? 1 : $selected['quantity'];

			if ( (int) $real_quantity !== (int) $qty ) {
				wp_send_json_error( esc_html__( 'Oh do not be tricky.', 'sellkit' ) );
			}
		}

		if ( 'radio' === $type ) {
			WC()->cart->empty_cart();
			WC()->cart->add_to_cart( $id, $qty );
		}

		if ( 'checkbox' === $type && 'add' === $modify ) {
			WC()->cart->add_to_cart( $id, $qty );
		}

		if ( 'checkbox' === $type && 'remove' === $modify ) {
			$post_type = get_post_type( $id );

			// Maybe product is a variation.
			if ( 'product_variation' === $post_type ) {
				foreach ( WC()->cart->get_cart() as $item_key => $item ) {
					if ( $item['variation_id'] === (int) $id ) {
						WC()->cart->remove_cart_item( $item_key ); // we remove it.
						break; // stop the loop.
					}
				}

				self::make_changes_after_cart_item_edit( $checkout_id );

				wp_send_json_success();
			}

			// Default product.
			$product_cart_id = WC()->cart->generate_cart_id( $id );
			$cart_item_key   = WC()->cart->find_product_in_cart( $product_cart_id );

			if ( $cart_item_key ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}

		self::make_changes_after_cart_item_edit( $checkout_id );

		wp_send_json_success();
	}

	/**
	 * Apply funnel discount.
	 * Will be called twice, first when page is loading , second during checkout ajax.
	 *
	 * @param object $cart cart object.
	 * @param int    $checkout_id_ajax checkout id.
	 * @param string $source source of products, bump or default products to be checked.
	 * @since 1.2.8
	 */
	public static function apply_discounted_prices( $cart = [], $checkout_id_ajax = null, $source = 'default' ) {
		$checkout_id = get_queried_object_id();

		if ( wp_doing_ajax() ) {
			$checkout_id = $checkout_id_ajax;
		}

		if ( empty( $checkout_id ) ) {
			return;
		}

		$step_data = get_post_meta( $checkout_id, 'step_data', true );

		if ( empty( $step_data ) ) {
			return;
		}

		$products = [];

		if ( 'default' === $source ) {
			$products = $step_data['data']['products']['list'];
		}

		if ( 'bumps' === $source && ! empty( $step_data['bump'] ) ) {
			$bumps = $step_data['bump'];

			foreach ( $bumps as $bump ) {
				if ( ! array_key_exists( 'products', $bump['data'] ) ) {
					continue;
				}

				$key              = array_key_first( $bump['data']['products']['list'] );
				$products[ $key ] = $bump['data']['products']['list'][ $key ];
			}
		}

		if ( empty( $products ) ) {
			return;
		}

		$final_price = 0;

		foreach ( $cart->get_cart() as $key => $details ) {
			$item_id = $details['product_id'];
			$price   = false;

			if ( ! empty( $details['variation_id'] ) ) {
				$item_id = $details['variation_id'];
			}

			foreach ( $products as $product_id => $product_details ) {
				if ( $item_id === $product_id ) {
					$discount_type  = $product_details['discountType'];
					$discount_value = $product_details['discount'];

					$price = Helper::calculate_discount( $item_id, $discount_type, $discount_value );
				}
			}

			if ( false !== $price ) {
				$details['data']->set_price( $price );
			}

			$final_price += $details['data']->get_price( $price ) * $details['quantity'];
		}

		self::modify_products_price_before_woo_apply_discounts( $final_price );
	}

	/**
	 * Modify cart subtotal before validating discounts.
	 *
	 * @since 1.2.8
	 * @param int $final_price cart new subtotal.
	 */
	public static function modify_products_price_before_woo_apply_discounts( $final_price ) {
		add_filter( 'woocommerce_coupon_validate_minimum_amount', function( $value, $coupon ) use ( $final_price ) {
			return $coupon->get_minimum_amount() > $final_price;
		}, 99, 2 );

		add_filter( 'woocommerce_coupon_validate_maximum_amount', function( $value, $coupon ) use ( $final_price ) {
			return $coupon->get_maximum_amount() < $final_price;
		}, 99, 2 );
	}

	/**
	 * Fix shipping method price on checkout ajax state change.
	 *
	 * @param string $data checkout form data.
	 * @since 1.1.0
	 * @return void
	 */
	public function sellkit_checkout_during_ajax( $data ) {
		$data  = explode( '&', $data );
		$clear = [];

		foreach ( $data as $key => $input ) {
			$input              = explode( '=', $input );
			$clear[ $input[0] ] = $input[1];
		}

		if ( ! array_key_exists( 'form_id', $clear ) ) {
			return;
		}

		$this->apply_template_replacement_during_ajax( $clear );

		$_POST = Helper::instance()->assigning_default_ajax_shipping_fields( $_POST, $clear ); // phpcs:ignore

		Local_Hooks::order_bump( $clear );

		if ( array_key_exists( 'sellkit-bundle-products', $clear ) ) {
			$option = $clear['sellkit-bundle-products'];

			Local_Hooks::bundle_product_action( $option );
		}

		// Apply discounts.
		if ( ! array_key_exists( 'sellkit_current_page_id', $clear ) ) {
			return;
		}

		self::apply_discounted_prices( wc()->cart, $clear['sellkit_current_page_id'] );
		self::apply_discounted_prices( wc()->cart, $clear['sellkit_current_page_id'], 'bumps' );
	}

	/**
	 * Template replacement during ajax call.
	 *
	 * @param array $data checkout form data.
	 * @since 1.2.1
	 */
	private function apply_template_replacement_during_ajax( $data ) {
		$widget_settings = Helper::instance()->retrieve_checkout_widget_settings( $data['post_id'], $data['form_id'] );

		Local_Hooks::enable_disable_sections( $widget_settings, $data );

		add_filter( 'wc_get_template', function( $located, $template_name ) {
			$our_path = sellkit()->plugin_dir() . 'includes/elementor/modules/checkout/templates/';
			$files    = [
				'review-order.php',
				'payment-method.php',
				'payment.php',
				'form-checkout.php',
				'cart-item-data.php',
				'cart-shipping.php',
				'terms.php',
			];
			$template = str_replace( 'checkout/', '', $template_name );
			$template = str_replace( 'cart/', '', $template );

			if ( in_array( $template, $files, true ) ) {
				$located = $our_path . $template;
			}

			return $located;
		}, 10, 2 );
	}

	/**
	 * Modify default selected shipping method.
	 *
	 * @since 1.2.8
	 * @param string $default default selected method.
	 * @return string
	 */
	public function sellkit_default_shipping_method( $default ) {
		$applied_coupons = WC()->cart->get_applied_coupons();

		if ( count( $applied_coupons ) < 1 ) {
			return $default;
		}

		foreach ( $applied_coupons as $coupon_code ) {

			$coupon = new \WC_Coupon( $coupon_code );

			if ( $coupon->get_free_shipping() ) {

				if ( count( WC()->session->get( 'shipping_for_package_0' )['rates'] ) > 0 ) {
					// Loop through.
					foreach ( WC()->session->get( 'shipping_for_package_0' )['rates'] as $rate_id => $rate ) {
						// For free shipping.
						if ( 'free_shipping' === $rate->method_id ) {
							return $rate_id;
						}
					}
				}
			}
		}

		return $default;
	}

	/**
	 * Recalculate checkout and cart prices.
	 *
	 * @since 1.2.8
	 */
	public function before_cart_calculate() {
		// More than twice isn't necessary. this is called multiple times by WooCommerce.
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 && wp_doing_ajax() ) {
			return;
		}

		// Gather checkout id.
		$checkout_id = get_queried_object_id();

		if ( wp_doing_ajax() ) {
			// First try to catch id using our ajax call.
			$checkout_id = filter_input( INPUT_POST, 'related_checkout', FILTER_SANITIZE_NUMBER_INT );

			// Second try to catch id using woocommerce ajax.
			if ( empty( $checkout_id ) ) {
				$checkout_id = filter_input( INPUT_POST, 'sellkit_current_page_id', FILTER_SANITIZE_NUMBER_INT );
			}

			// Catch id after pressing place order button. none of above methods worked.
			if ( empty( $checkout_id ) ) {
				$data = filter_input( INPUT_POST, 'post_data', FILTER_DEFAULT );

				parse_str( $data, $data );

				if ( array_key_exists( 'sellkit_current_page_id', $data ) ) {
					$checkout_id = $data['sellkit_current_page_id'];
				}
			}
		}

		// Empty id ? no action.
		if ( empty( $checkout_id ) ) {
			return;
		}

		$checkout_id = (int) $checkout_id;

		// Apply funnel prices.
		self::apply_discounted_prices( wc()->cart, $checkout_id );
		self::apply_discounted_prices( wc()->cart, $checkout_id, 'bumps' );

		// We should re apply coupons after each price changes. to make sure everything is correct.
		$optimization_data = ! empty( $funnel_data['data']['optimization'] ) ? $funnel_data['data']['optimization'] : '';

		if ( Checkout::apply_coupon_validation( $optimization_data ) ) {
			WC()->cart->remove_coupons();

			foreach ( $optimization_data['auto_apply_coupons'] as $auto_apply_coupon ) {
				wc()->cart->add_discount( get_the_title( $auto_apply_coupon['value'] ) );
			}

			wc_clear_notices();
		}
	}
}
