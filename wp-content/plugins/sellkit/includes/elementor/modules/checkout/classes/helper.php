<?php

namespace Sellkit\Elementor\Modules\Checkout\Classes;

defined( 'ABSPATH' ) || exit;

use Elementor\Plugin as Elementor;

/**
 * Helper class.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @since 1.1.0
 */
class Helper {

	/**
	 * Instance of this class.
	 *
	 * @since   NEXT
	 * @access  public
	 * @var Checkout
	 */
	public static $instance;

	/**
	 * Provides access to a single instance of a module using the singleton pattern.
	 *
	 * @since   NEXT
	 * @return  object
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Default sellkit checkout widget billing fields.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function billing_fields() {
		return [
			'billing_first_name' => esc_html__( 'Billing First Name', 'sellkit' ),
			'billing_last_name'  => esc_html__( 'Billing Last Name', 'sellkit' ),
			'billing_address_1'  => esc_html__( 'Billing Address 1', 'sellkit' ),
			'billing_address_2'  => esc_html__( 'Billing Address 2', 'sellkit' ),
			'billing_city'       => esc_html__( 'Billing City', 'sellkit' ),
			'billing_postcode'   => esc_html__( 'Billing Postal Code', 'sellkit' ),
			'billing_country'    => esc_html__( 'Billing Country', 'sellkit' ),
			'billing_state'      => esc_html__( 'Billing State', 'sellkit' ),
			'billing_email'      => esc_html__( 'Billing State', 'sellkit' ),
		];
	}

	/**
	 * Default sellkit checkout widget shipping fields.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function shipping_fields() {
		return [
			'shipping_first_name' => esc_html__( 'shipping First Name', 'sellkit' ),
			'shipping_last_name'  => esc_html__( 'shipping Last Name', 'sellkit' ),
			'shipping_address_1'  => esc_html__( 'shipping Address 1', 'sellkit' ),
			'shipping_address_2'  => esc_html__( 'shipping Address 2', 'sellkit' ),
			'shipping_city'       => esc_html__( 'shipping City', 'sellkit' ),
			'shipping_postcode'   => esc_html__( 'shipping Postal Code', 'sellkit' ),
			'shipping_country'    => esc_html__( 'shipping Country', 'sellkit' ),
			'shipping_state'      => esc_html__( 'shipping State', 'sellkit' ),
		];
	}

	/**
	 * Gets user defined fields and return array of slug.
	 *
	 * @param array  $user_defined_fields widget fields.
	 * @param string $type billing/shipping field.
	 * @return array
	 * @since 1.1.0
	 */
	public function get_user_defined_fields_slug( $user_defined_fields, $type ) {
		$fields = [];

		foreach ( $user_defined_fields as $field ) {
			$fields[] = $field[ $type ];
		}

		return $fields;
	}

	/**
	 * Assign user defined values to each field.
	 *
	 * @param array  $default_fields : default woocommerce fields.
	 * @param array  $widget_fields : fields get added by user from widget option.
	 * @param string $type : shipping / billing.
	 * @param array  $settings : whole widget settings.
	 * @return array
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function assign_settings_per_field( $default_fields, $widget_fields, $type, $settings ) {
		// Assign user properties.
		foreach ( $widget_fields as $key => $field ) {
			$field_slug = $field[ $type . '_list_field' ];
			$is_custom  = false;

			// IF field is a custom field we replace key with id. So it will be unique.
			// !important: user has to define id for each field using custom id field.
			if ( 'custom_role' === $field_slug ) {
				// Create billing_bla_bla & shipping_bla_bla for custom field.
				$field_slug = $type . '_' . $field['_id'];

				if ( ! empty( $field[ $type . '_custom_id' ] ) ) {
					$field_slug = $type . '_' . $field[ $type . '_custom_id' ];
				}

				$is_custom = true;
			}

			$default_fields[ $field_slug ]['label']    = $field[ $type . '_list_placeholder' ];
			$default_fields[ $field_slug ]['class']    = explode( ' ', $field[ $type . '_list_class' ] ); // phpcs:ignore
			$default_fields[ $field_slug ]['class'][]  = $field[ $type . '_width' ];
			$default_fields[ $field_slug ]['class'][]  = 'sellkit-widget-checkout-fields';
			$default_fields[ $field_slug ]['class'][]  = 'sellkit-checkout-fields-validation-' . $field[ $type . '_list_validation' ];
			$default_fields[ $field_slug ]['required'] = ( 'yes' === $field[ $type . '_list_required' ] ) ? true : false;
			$default_fields[ $field_slug ]['clear']    = ( 'yes' === $field[ $type . '_list_clear' ] ) ? true : false;
			$default_fields[ $field_slug ]['priority'] = ( $key + 1 ) * 10;
			// Widget defined fields are local. third party plugins's fields are not.
			$default_fields[ $field_slug ]['local'] = true;

			// Append postal code autocomplete class.
			if (
				array_key_exists( 'state_lookup_by_postcode', $settings )
				&& 'yes' === $settings['state_lookup_by_postcode']
				&& ( 'billing_postcode' === $field_slug || 'shipping_postcode' === $field_slug )
			) {
				$default_fields[ $field_slug ]['class'][] = 'post_code_autocomplete';
			}

			// IF not custom field, go next.
			if ( false === $is_custom ) {
				continue;
			}

			$default_fields = $this->custom_field_setup( $field, $type, $field_slug, $default_fields );
		}

		foreach ( $default_fields as $key => $details ) {
			if ( ! array_key_exists( 'local', $details ) || false === $details['local'] ) {
				$default_fields[ $key ]['priority'] = 500;
			}
		}

		return $default_fields;
	}

	/**
	 * Setup custom field options and assign required things to custom fields.
	 * also helps to reduce assign_settings_per_field method complexity.
	 *
	 * @param array  $field html string of checkout fields.
	 * @param string $type type of checkout fields.
	 * @param string $field_slug unique sluf per field.
	 * @param array  $default_fields default checkout fields.
	 * @return array
	 * @since 1.1.0
	 */
	private function custom_field_setup( $field, $type, $field_slug, $default_fields ) {
		$tag = $field[ $type . '_custom_type' ];

		// Set default options.
		$default_fields[ $field_slug ]['class'][] = ( $field[ $type . '_width' ] ) ? $field[ $type . '_width' ] : 'w-100';
		$default_fields[ $field_slug ]['type']    = ( $field[ $type . '_custom_type' ] ) ? $field[ $type . '_custom_type' ] : 'text';
		$default_fields[ $field_slug ]['default'] = $field[ $type . '_custom_value' ];

		// Convert select to multiselect.
		if ( 'multiselect' === $tag ) {
			$default_fields[ $field_slug ]['type'] = 'multiselect';
		}

		// Assign one extra class to style radio group wrapper.
		if ( 'radio' === $tag ) {
			$default_fields[ $field_slug ]['type']    = 'radio';
			$default_fields[ $field_slug ]['class'][] = 'radio-group-wrapper';
			$default_fields[ $field_slug ]['mode']    = $field[ $type . '_radio_field_mode' ];
		}

		// Check if checkbox must be checked on default.
		if ( 'checkbox' === $tag ) {
			$default_fields[ $field_slug ]['checked'] = $field[ $type . '_checkbox_custom_value' ];
		}

		// Assign options.
		$tags = [ 'select', 'multiselect', 'radio' ];
		if ( in_array( $tag, $tags, true ) ) {
			$options = $this->convert_value_to_array( $field[ $type . '_custom_options' ] );

			$default_fields[ $field_slug ]['options'] = $options;
		}

		return $default_fields;
	}

	/**
	 * Gets widget options string and convert it to array to be used for select / radio and etc.
	 *
	 * @param string $value string of options.
	 * @return array
	 * @since 1.1.0
	 */
	private function convert_value_to_array( $value ) {
		$array  = [];
		$values = preg_split( '/\r\n|\r|\n/', $value );

		foreach ( $values as $option ) {
			$split              = explode( ':', $option );
			$array[ $split[0] ] = $split[1];
		}

		return $array;
	}

	/**
	 * Is used to validate user defined custom fields after pressing place order button at checkout page.
	 * Validate both custom shipping and billing fields at once if exists.
	 *
	 * @uses sellkit_Core\Raven\Modules\Checkout\Classes\Global_Hooks::validate_user_defined_fields
	 * @param array $shipping_fields custom shipping fields.
	 * @param array $billing_fields custom billing fields.
	 * @return void
	 * @since 1.1.0
	 */
	public function validate_user_defined_fields( $shipping_fields, $billing_fields ) {
		foreach ( $shipping_fields as $data ) {
			// Escape all if cart does not need shipping information.
			if ( ! WC()->cart->needs_shipping() ) {
				continue;
			}

			$final_name = $data['shipping_list_field'];

			if ( 'custom_role' === $final_name ) {
				$final_name = 'shipping_' . $data['_id'];

				if ( ! empty( $data['shipping_custom_id'] ) ) {
					$id         = $data['shipping_custom_id'];
					$final_name = "shipping_$id";
				}
			}

			// Get $name variable from label or placeholder and/or id to be used in validate error.
			$label = $this->create_name_for_errors( $data, __( 'Shipping', 'sellkit' ), 'shipping', $final_name );

			// Field value.
			$value = filter_input( INPUT_POST, $final_name, FILTER_DEFAULT );

			// #1. Required validation.
			$is_required = ( ! empty( $data['shipping_list_required'] ) && 'yes' === $data['shipping_list_required'] ) ? true : false;

			if ( true === $is_required && ( ! array_key_exists( $final_name, $_POST ) || empty( $value ) ) ) { // phpcs:ignore
				/* translators: %s field_name. */
				wc_add_notice( sprintf( __( '%s is a required field.', 'sellkit' ), '<strong>' . $label . '</strong>' ), 'error' );
			}

			// #2. Phone validation.
			$this->phone_validation( $data, $final_name, 'shipping', $label );

			// #3. Postcode validation.
			$this->postcode_validation( $data, $final_name, 'shipping', $label );
		}

		foreach ( $billing_fields as $data ) {
			$final_name = $data['billing_list_field'];

			if ( 'custom_role' !== $final_name ) {
				continue;
			}

			$final_name = 'billing_' . $data['_id'];

			if ( ! empty( $data['billing_custom_id'] ) ) {
				$id         = $data['billing_custom_id'];
				$final_name = "billing_$id";
			}

			// Get $name variable from label or placeholder and/or id to be used in validate error.
			$label = $this->create_name_for_errors( $data, __( 'Billing', 'sellkit' ), 'billing', $final_name );

			// Field value.
			$value = filter_input( INPUT_POST, $final_name, FILTER_DEFAULT );

			// #1. Required validation.
			$is_required = ( 'yes' === $data['billing_list_required'] ) ? true : false;

			if ( true === $is_required && empty( $final_name ) ) { // phpcs:ignore
				/* translators: %s field_name. */
				wc_add_notice( sprintf( __( '%s is a required field.', 'sellkit' ), '<strong>' . $label . '</strong>' ), 'error' );
			}

			// #2. phone validation.
			$this->phone_validation( $data, $final_name, 'billing', $label );

			// #3. Postcode validation.
			$this->postcode_validation( $data, $final_name, 'billing', $label );
		}

		// Validate main checkout email.
		$email = filter_input( INPUT_POST, 'billing_email', FILTER_SANITIZE_EMAIL );
		$valid = filter_var( $email, FILTER_VALIDATE_EMAIL );

		if ( ! $email ) {
			/* translators: %s field_name. */
			wc_add_notice( sprintf( __( '%s is a required field.', 'sellkit' ), '<strong>' . __( 'Email address', 'sellkit' ) . '</strong>' ), 'error' );
			return;
		}

		if ( ! $valid ) {
			/* translators: %s field_name. */
			wc_add_notice( sprintf( __( '%s is not a valid email.', 'sellkit' ), '<strong>' . __( 'Email address', 'sellkit' ) . '</strong>' ), 'error' );
		}
	}

	/**
	 * Create name for each field to be used in errors text.
	 *
	 * @param array  $data field data.
	 * @param string $display_type shipping / billing with text-domain.
	 * @param string $type shipping / billing.
	 * @param string $final_name field key.
	 * @return string
	 * @since 1.1.0
	 */
	private function create_name_for_errors( $data, $display_type, $type, $final_name ) {
		$name = $data[ $type . '_list_placeholder' ];

		if ( empty( $data[ $type . '_list_placeholder' ] ) ) {
			$name = $data[ $type . '_list_label' ];
		}

		$label = $display_type . ' ' . $name;

		if ( empty( $name ) ) {
			$label = $final_name;
		}

		return $label;
	}

	/**
	 * Validate phone number using woocommerce way.
	 *
	 * @see woocommerce/includes/class-wc-validation.php
	 * @param array  $data widget field data.
	 * @param string $final_name key of field in checkout form.
	 * @param string $type shipping/billing.
	 * @param string $label field label for error.
	 * @return boolean
	 * @since 1.1.0
	 */
	private function phone_validation( $data, $final_name, $type, $label ) {
		if ( ! array_key_exists( $type . '_list_validation', $data ) || 'phone' !== $data[ $type . '_list_validation' ] ) {
			return true;
		}

		$phone = filter_input( INPUT_POST, $final_name, FILTER_DEFAULT );
		$valid = \WC_Validation::is_phone( $phone );

		if ( ! $valid ) {
			/* translators: %s field_name. */
			wc_add_notice( sprintf( __( '%s is not a valid phone.', 'sellkit' ), '<strong>' . $label . '</strong>' ), 'error' );
		}
	}

	/**
	 * Validate postcode using woocommerce way.
	 *
	 * @param array  $data field data.
	 * @param string $final_name field key in checkout form.
	 * @param string $type shipping / billing.
	 * @param string $label field label to be used in error text.
	 * @return void
	 * @since 1.1.0
	 */
	private function postcode_validation( $data, $final_name, $type, $label ) {
		if ( ! array_key_exists( $type . '_list_validation', $data ) || 'postcode' !== $data[ $type . '_list_validation' ] ) {
			return;
		}

		$postcode = filter_input( INPUT_POST, $final_name, FILTER_DEFAULT );
		$country  = filter_input( INPUT_POST, $type . '_country' );
		$postcode = wc_format_postcode( $postcode, $country );
		$valid    = \WC_Validation::is_postcode( $postcode, $country );

		if ( ! $valid ) {
			/* translators: %s field_name. */
			wc_add_notice( sprintf( __( '%s is not a valid postcode.', 'sellkit' ), '<strong>' . $label . '</strong>' ), 'error' );
		}
	}

	/**
	 * Is used to save user defined custom fields in database for the current order id.
	 * save both billing and shipping custom fields at once if exists.
	 *
	 * @uses sellkit_Core\Raven\Modules\Checkout\Classes\Global_Hooks::save_user_defined_fields
	 * @param array $shipping_fields custom shipping fields.
	 * @param array $billing_fields custom billing fields.
	 * @param int   $order_id order id.
	 * @return void
	 * @since 1.1.0
	 */
	public function save_user_defined_fields( $shipping_fields, $billing_fields, $order_id ) {
		// We store saved fields in this array and save keys as a meta for this order, to use it later.
		$saved_fields = [];

		foreach ( $shipping_fields as $field ) {
			if ( empty( $field['shipping_custom_id'] ) ) {
				continue;
			}

			$item       = $this->prepare_custom_fields_to_save( $field, 'shipping' );
			$final_name = $item['key_name'];

			// Field value.
			$value = sellkit_htmlspecialchars( INPUT_POST, $final_name );
			if ( 'multiselect' === $field['shipping_custom_type'] ) {
				$value = filter_input( INPUT_POST, $final_name, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			}

			// Some user defined field might be not required ( & empty ). so again we check if field is has value save it.
			if ( ! empty( $value ) ) { // phpcs:ignore
				update_post_meta( $order_id, $final_name, sanitize_text_field( $value ) ); // phpcs:ignore
				array_push( $saved_fields, $item );
			}
		}

		foreach ( $billing_fields as $field ) {
			if ( empty( $field['billing_custom_id'] ) ) {
				continue;
			}

			$item       = $this->prepare_custom_fields_to_save( $field, 'billing' );
			$final_name = $item['key_name'];

			// Field value.
			$value = sellkit_htmlspecialchars( INPUT_POST, $final_name );
			if ( 'multiselect' === $field['billing_custom_type'] ) {
				$value = filter_input( INPUT_POST, $final_name, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			}

			// Some user defined field might be not required ( & empty ). so again we check if field has value save it.
			if ( ! empty( $value ) ) { // phpcs:ignore
				update_post_meta( $order_id, $final_name, sanitize_text_field( $value ) ); // phpcs:ignore
				array_push( $saved_fields, $item );
			}
		}

		// Save id of those custom fields that are validate and has value as an meta for order to be used later.
		if ( count( $saved_fields ) > 0 ) {
			update_post_meta( $order_id, 'sellkit_checkout_widget_custom_field_of_order', $saved_fields );
		}
	}

	/**
	 * The way to save custom fields names as array for order as meta to be used later.
	 *
	 * @param array  $data sent data from checkout.
	 * @param string $type shippin/billing.
	 * @return array
	 * @since 1.1.0
	 */
	private function prepare_custom_fields_to_save( $data, $type ) {
		$id         = $data[ $type . '_custom_id' ];
		$final_name = $type . '_' . $id;
		$item       = [
			'show_in_thank_you' => ( 'yes' === $data[ $type . '_show_in_thankyou' ] ) ? 'yes' : 'no',
			'show_in_email'     => ( 'yes' === $data[ $type . '_show_in_order_mail' ] ) ? 'yes' : 'no',
			'key_name'          => $final_name,
		];

		return $item;
	}

	/**
	 * Convert required fields to optional based on widget options.
	 * will be called after clicking place order button.
	 *
	 * @uses sellkit_Core\Raven\Modules\Checkout\Classes\Global_Hooks::validate_user_defined_fields
	 * @param array $widget_shipping_fields user defined fields in widget.
	 * @param array $widget_billing_fields user defined fields in widget.
	 * @return void
	 * @since 1.1.0
	 */
	public function make_sure_to_convert_required_field_to_optional( $widget_shipping_fields, $widget_billing_fields ) {
		add_filter( 'woocommerce_checkout_fields', function( $default_fields ) use ( $widget_shipping_fields, $widget_billing_fields ) {
			// Make phone field optional at beginning.
			$default_fields['billing']['billing_phone']['required'] = false;
			unset( $default_fields['billing']['billing_email'] );

			// Make it optional.
			foreach ( $widget_billing_fields as $data ) {
				$slug        = $data['billing_list_field'];
				$is_required = false;

				if ( ! empty( $data['billing_list_required'] ) && 'yes' === $data['billing_list_required'] ) {
					$is_required = true;
				}

				$default_fields['billing'][ $slug ]['required'] = $is_required;
			}

			foreach ( $widget_shipping_fields as $data ) {
				$slug        = $data['shipping_list_field'];
				$is_required = false;

				if ( ! empty( $data['shipping_list_required'] ) && 'yes' === $data['shipping_list_required'] ) {
					$is_required = true;
				}

				$default_fields['shipping'][ $slug ]['required'] = $is_required;
			}

			return $default_fields;
		} );
	}

	/**
	 * Retrieve required widget settings of current checkout widget.
	 *
	 * @param int    $post_id page id.
	 * @param string $form_id form id.
	 * @return array
	 * @since 1.1.0
	 */
	public function retrieve_checkout_widget_settings( $post_id, $form_id ) {
		$form_meta = Elementor::$instance->documents->get( $post_id )->get_elements_data();
		$form      = self::find_element_recursive( $form_meta, $form_id );

		if ( ! is_array( $form ) || ! array_key_exists( 'settings', $form ) ) {
			return [];
		}

		$settings = $form['settings'];

		return $settings;
	}

	/**
	 * Find elementor element.
	 * Code block copied from sellkit-core directly.
	 *
	 * @param array  $elements elements of elementor.
	 * @param string $form_id elementor form id.
	 * @return array
	 * @since 1.1.0
	 */
	public static function find_element_recursive( $elements, $form_id ) {
		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = self::find_element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	/**
	 * Hide checkout order product quantity input and show raw quantity.
	 *
	 * @param string $input_html default quantity input.
	 * @param int    $quantity product quantity in cart.
	 * @since 1.1.0
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function checkout_order_hidden_quantity( $input_html, $quantity ) {
		echo sprintf(
			/** Translators: %s: product quantity */
			'<strong class="product-quantity"> <i class="fa fa-times" aria-hidden="true"></i>%s</strong>',
			esc_html( $quantity )
		);
	}

	/**
	 * Calculate item discount.
	 *
	 * @param int    $id product id.
	 * @param string $type discount type.
	 * @param int    $value discount value.
	 * @return int|boolean
	 * @since 1.2.8
	 */
	public static function calculate_discount( $id, $type, $value ) {
		if ( false === $type && false === $value ) {
			return false;
		}

		$product = wc_get_product( $id );
		$regular = $product->get_regular_price();
		$sale    = $product->get_sale_price();
		$price   = false;

		if ( strpos( $type, 'sale' ) !== false ) {
			$discount = ( 'fixed-sale' === $type ) ? floatval( $value ) : ( floatval( $sale ) * floatval( $value ) ) / 100;
			$discount = floatval( $sale ) - $discount;

			return ( $discount > 0 ) ? $discount : 0;
		}

		if ( strpos( $type, 'sale' ) === false ) {
			$discount = ( 'fixed' === $type ) ? floatval( $value ) : ( floatval( $regular ) * floatval( $value ) ) / 100;
			$discount = floatval( $regular ) - $discount;

			return ( $discount > 0 ) ? $discount : 0;
		}

		return $price;
	}

	/**
	 * Add extra js in editor mode.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function editor_mode_extra_js() {
		?>
			<script>
				jQuery( document ).on( 'mousemove', function() {
					$( '.sellkit-coupon-toggle' ).off( 'click' ).on( 'click', function() {
						let direction = 'row';
						const status = $( '.sellkit-custom-coupon-form' ).css( 'display' );
						let displayValue = '';

						if ( $( window ).width() < 600 ) {
							direction = 'column';
						}

						if ( 'none' === status ) {
							displayValue = 'inline-flex';
						} else {
							displayValue = 'none';
						}

						$( '.sellkit-custom-coupon-form' ).css( {
							display: displayValue,
							flexDirection: direction,
						} );
					} );
				} );
			</script>
		<?php
	}

	/**
	 * Assigning checkout ajax default shipping fields after ajax is sent.
	 *
	 * @param array $post post request.
	 * @param array $clear checkout form data.
	 * @return array
	 * @since 1.2.8
	 */
	public function assigning_default_ajax_shipping_fields( $post, $clear ) {
		if ( array_key_exists( 'billing_state', $clear ) ) {
			$post['state'] = $clear['billing_state'];
		}

		if ( array_key_exists( 'shipping_country', $clear ) ) {
			$post['s_country'] = $clear['shipping_country'];
		}

		if ( array_key_exists( 'shipping_state', $clear ) ) {
			$post['s_state'] = $clear['shipping_state'];
		}

		if ( array_key_exists( 'shipping_postcode', $clear ) ) {
			$post['s_postcode'] = $clear['shipping_postcode'];
		}

		if ( array_key_exists( 'shipping_city', $clear ) ) {
			$post['s_city'] = $clear['shipping_city'];
		}

		if ( array_key_exists( 'shipping_address_1', $clear ) ) {
			$post['s_address'] = $clear['shipping_address_1'];
		}

		if ( array_key_exists( 'shipping_address_2', $clear ) ) {
			$post['s_address_2'] = $clear['shipping_address_2'];
		}

		return $post;
	}
}
