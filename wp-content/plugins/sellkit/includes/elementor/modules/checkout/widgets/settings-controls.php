<?php

namespace Sellkit\Elementor\Modules\Checkout\Widgets;

defined( 'ABSPATH' ) || die();

use Elementor\Settings;
use Elementor\Plugin;

/**
 * Checkout settings controls.
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @since 1.1.0
 */
class Settings_Controls extends \Sellkit_Elementor_Checkout_Widget {
	public function __construct() {
		$this->layout_settings();
		$this->google_autocomplete_settings();
		$this->coupon_form_visibility();
		$this->shipping_fields();
		$this->billing_fields();
		$this->custom_messages();
	}

	private function shipping_fields_type() {
		$fields = [
			'shipping_first_name' => esc_html__( 'Shipping First Name', 'sellkit' ),
			'shipping_last_name'  => esc_html__( 'Shipping Last Name', 'sellkit' ),
			'shipping_company'    => esc_html__( 'Shipping Company', 'sellkit' ),
			'shipping_address_1'  => esc_html__( 'Shipping Address 1', 'sellkit' ),
			'shipping_address_2'  => esc_html__( 'Shipping Address 2', 'sellkit' ),
			'shipping_city'       => esc_html__( 'Shipping City', 'sellkit' ),
			'shipping_postcode'   => esc_html__( 'Shipping Postcode', 'sellkit' ),
			'shipping_country'    => esc_html__( 'Shipping Country', 'sellkit' ),
			'shipping_state'      => esc_html__( 'Shipping State', 'sellkit' ),
			'custom_field_pro'    => esc_html__( 'Custom Field (Sellkit Pro)', 'sellkit' ),
		];

		if ( sellkit()->has_pro ) {
			unset( $fields['custom_field_pro'] );
			$fields = \Sellkit_Elementor_Checkout_Pro_Module::checkout_custom_fields( $fields );
		}

		return $fields;
	}

	private function billing_fields_type() {
		$fields = [
			'billing_first_name' => esc_html__( 'Billing First Name', 'sellkit' ),
			'billing_last_name'  => esc_html__( 'Billing Last Name', 'sellkit' ),
			'billing_company'    => esc_html__( 'Billing Company', 'sellkit' ),
			'billing_address_1'  => esc_html__( 'Billing Address 1', 'sellkit' ),
			'billing_address_2'  => esc_html__( 'Billing Address 2', 'sellkit' ),
			'billing_city'       => esc_html__( 'Billing City', 'sellkit' ),
			'billing_postcode'   => esc_html__( 'Billing Postcode', 'sellkit' ),
			'billing_country'    => esc_html__( 'Billing Country', 'sellkit' ),
			'billing_state'      => esc_html__( 'Billing State', 'sellkit' ),
			'billing_phone'      => esc_html__( 'Billing Phone', 'sellkit' ),
			'custom_field_pro'   => esc_html__( 'Custom Field (Sellkit Pro)', 'sellkit' ),
		];

		if ( sellkit()->has_pro ) {
			unset( $fields['custom_field_pro'] );
			$fields = \Sellkit_Elementor_Checkout_Pro_Module::checkout_custom_fields( $fields );
		}

		return $fields;
	}

	private function layout_settings() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'sellkit' ),
			]
		);

		$this->add_control(
			'layout-type',
			[
				'label'   => esc_html__( 'Layout', 'sellkit' ),
				'type'    => 'select',
				'default' => 'one-page',
				'options' => [
					'one-page'   => esc_html__( 'Single Page', 'sellkit' ),
					'multi-step' => esc_html__( 'Multi Steps', 'sellkit' ),
				],
			]
		);

		$this->add_control(
			'show_cart_items',
			[
				'label'        => esc_html__( 'Cart Items', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_cart_edit',
			[
				'label'        => esc_html__( 'Enable Cart Editing', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'show_cart_items' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_breadcrumb',
			[
				'label'        => esc_html__( 'Steps', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'layout-type' => 'multi-step',
				],
			]
		);

		$this->add_control(
			'show_shipping_method',
			[
				'label'        => esc_html__( 'Shipping Method', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_preview_box',
			[
				'label'        => esc_html__( 'Preview Box', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'layout-type' => 'multi-step',
				],
			]
		);

		if ( sellkit()->has_pro ) {
			\Sellkit_Elementor_Checkout_Pro_Module::express_checkout_control( $this );
		} else {
			$this->add_control(
				'show_express_checkout_promotions',
				[
					'label'        => esc_html__( 'Express Checkout', 'sellkit' ),
					'classes'      => 'elementor-control-checkout-express-promotion',
					'type'         => 'switcher',
					'label_on'     => esc_html__( 'Show', 'sellkit' ),
					'label_off'    => esc_html__( 'Hide', 'sellkit' ),
					'return_value' => 'yes',
					'default'      => 'no',
				]
			);
		}

		$this->add_control(
			'show_sticky_cart_details',
			[
				'label'        => esc_html__( 'Checkout Sticky Cart', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'layout-type' => 'multi-step',
				],
			]
		);

		$this->add_control(
			'place_order_btn_txt',
			[
				'label' => esc_html__( 'Place Order Button Text', 'sellkit' ),
				'type' => 'text',
				'label_block' => true,
				'default' => esc_html__( 'Complete Order', 'sellkit' ),
			]
		);

		$this->end_controls_section();
	}

	public function get_template( $texts ) {
		ob_start();
		?>
		<div class="elementor-nerd-box">
			<span class="sellkit-checkout-promotion-rocket"></span>
			<div class="sellkit-checkout-autocomplete-promotion-title"><?php echo $texts['title']; ?></div>
			<?php foreach ( $texts['messages'] as $message ) { ?>
				<div class="sellkit-checkout-promotion-message"><?php echo $message; ?></div>
			<?php }

			// Show a `Go Pro` button only if the user doesn't have Pro.
			if ( $texts['link'] ) { ?>
				<a
					class="sellkit-checkout-autocomplete-promotion-btn"
					href="https://getsellkit.com/pricing/"
					target="_blank"
				>
					<?php echo esc_html__( 'Upgrade Now', 'sellkit' ); ?>
				</a>
			<?php } ?>
		</div>
		<?php

		return ob_get_clean();
	}

	private function google_autocomplete_settings() {
		if ( sellkit()->has_pro ) {
			\Sellkit_Elementor_Checkout_Pro_Module::google_address_autocomplete( $this );
			return;
		}

		$messages = [
			sprintf(
				/* translators: 1: bold tag 2: bold tag */
				esc_html__( 'You are using a free version of Sellkit. Upgrade to %1$s Sellkit Pro %2$s to use this feature.', 'sellkit' ),
				'<b>',
				'</b>'
			),
		];

		$this->start_controls_section(
			'google_autocomplete_settings',
			[
				'label' => esc_html__( 'Address Autocomplete', 'sellkit' ),
			]
		);

		$this->add_control(
			'sellkit_checkout_autocomplete_address_promotion',
			[
				'type' => 'raw_html',
				'raw' => $this->get_template( [
					'title' => esc_html__( 'Upgrade to Sellkit Pro', 'sellkit' ),
					'messages' => $messages,
					'link' => 'https://getsellkit.com/pricing/',
				] ),
			]
		);

		$this->end_controls_section();
	}

	private function coupon_form_visibility() {
		$this->start_controls_section(
			'coupon_form_visibility',
			[
				'label' => esc_html__( 'Coupon', 'sellkit' ),
			]
		);

		$this->add_control(
			'show_coupon_field',
			[
				'label'        => esc_html__( 'Enable Coupon Field', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'sellkit' ),
				'label_off'    => esc_html__( 'No', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'coupon_field_type',
			[
				'label'   => esc_html__( 'Coupon Field Behavior', 'sellkit' ),
				'type'    => 'select',
				'default' => 'normal',
				'options' => [
					'normal'       => esc_html__( 'ُNormal', 'sellkit' ),
					'collapsible' => esc_html__( 'Collapsible', 'sellkit' ),
				],
			]
		);

		$this->end_controls_section();
	}

	private function shipping_fields() {
		$this->start_controls_section(
			'shipping_fields',
			[
				'label' => esc_html__( 'Shipping Fields', 'sellkit' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'shipping_list_field',
			[
				'label'    => esc_html__( 'Field role', 'sellkit' ),
				'type'     => 'select',
				'options'  => $this->shipping_fields_type(),
			]
		);

		$repeater->add_control(
			'shipping_custom_type',
			[
				'label'     => esc_html__( 'Custom Field Type', 'sellkit' ),
				'type'      => 'select',
				'options'   => [
					'text'        => esc_html__( 'Text Field', 'sellkit' ),
					'textarea'    => esc_html__( 'Textarea Field', 'sellkit' ),
					'select'      => esc_html__( 'Select Field', 'sellkit' ),
					'multiselect' => esc_html__( 'Multiselect Field', 'sellkit' ),
					'checkbox'    => esc_html__( 'Checkbox', 'sellkit' ),
					'radio'       => esc_html__( 'Radio', 'sellkit' ),
					'tel'         => esc_html__( 'Tel Field', 'sellkit' ),
					'email'       => esc_html__( 'Email Field', 'sellkit' ),
					'hidden'      => esc_html__( 'Hidden Field', 'sellkit' ),
				],
				'condition' => [
					'shipping_list_field' => 'custom_role',
				],
				'default' => 'text',
			]
		);

		$repeater->add_control(
			'shipping_custom_options',
			[
				'label'       => esc_html__( 'Options', 'sellkit' ),
				'type'        => 'textarea',
				'rows'        => 5,
				'placeholder' => esc_html__( 'e.g. option1:label1', 'sellkit' ),
				'description' => esc_html__( 'Each line one option and separate value and label with (:)', 'sellkit' ),
				'condition'   => [
					'shipping_custom_type' => [ 'select', 'multiselect', 'radio' ],
				],
			]
		);

		$repeater->add_control(
			'shipping_radio_field_mode',
			[
				'label' => esc_html__( 'Display Mode', 'sellkit' ),
				'type' => 'choose',
				'options' => [
					'inline' => [
						'title' => esc_html__( 'Inline', 'sellkit' ),
						'icon'  => 'eicon-ellipsis-h',
					],
					'list' => [
						'title' => esc_html__( 'List', 'sellkit' ),
						'icon'  => 'eicon-bullet-list',
					],
				],
				'default' => 'list',
				'toggle' => true,
				'condition'   => [
					'shipping_custom_type' => [ 'radio' ],
				],
			]
		);

		$repeater->add_control(
			'shipping_custom_id',
			[
				'label'       => esc_html__( 'Custom Field ID', 'sellkit' ),
				'type'        => 'text',
				'placeholder' => 'e.g. my_custom_field',
				'condition'   => [
					'shipping_list_field' => 'custom_role',
				],
			]
		);

		$repeater->add_control(
			'shipping_custom_value',
			[
				'label'     => esc_html__( 'Default Value', 'sellkit' ),
				'type'      => 'text',
				'condition' => [
					'shipping_list_field'  => 'custom_role',
					'shipping_custom_type' => [ 'text', 'textarea', 'select', 'radio', 'hidden', 'tel', 'email' ],
				],
			]
		);

		$repeater->add_control(
			'shipping_checkbox_custom_value',
			[
				'label'     => esc_html__( 'Default Value', 'sellkit' ),
				'type'      => 'select',
				'options'   => [
					'true'  => 'True',
					'false' => 'False',
				],
				'condition' => [
					'shipping_list_field'  => 'custom_role',
					'shipping_custom_type' => [ 'checkbox' ],
				],
			]
		);

		$repeater->add_control(
			'shipping_width',
			[
				'label'     => esc_html__( 'Width', 'sellkit' ),
				'type'      => 'select',
				'options'   => [
					'w-10'  => esc_html__( '10%', 'sellkit' ),
					'w-20'  => esc_html__( '20%', 'sellkit' ),
					'w-30'  => esc_html__( '30%', 'sellkit' ),
					'w-33'  => esc_html__( '33%', 'sellkit' ),
					'w-34'  => esc_html__( '34%', 'sellkit' ),
					'w-40'  => esc_html__( '40%', 'sellkit' ),
					'w-50'  => esc_html__( '50%', 'sellkit' ),
					'w-60'  => esc_html__( '60%', 'sellkit' ),
					'w-70'  => esc_html__( '70%', 'sellkit' ),
					'w-80'  => esc_html__( '80%', 'sellkit' ),
					'w-90'  => esc_html__( '90%', 'sellkit' ),
					'w-100' => esc_html__( '100%', 'sellkit' ),
				],
				'default' => 'w-100',
			]
		);

		$repeater->add_control(
			'shipping_list_placeholder',
			[
				'label' => esc_html__( 'Field Label', 'sellkit' ),
				'type'  => 'text',
				'default' => esc_html__( 'Default label', 'sellkit' ),
			]
		);

		$repeater->add_control(
			'shipping_list_class',
			[
				'label'       => esc_html__( 'Field Class', 'sellkit' ),
				'type'        => 'text',
				'description' => esc_html__( 'Divide each class with space character', 'sellkit' ),
			]
		);

		$repeater->add_control(
			'shipping_list_validation',
			[
				'label'     => esc_html__( 'Validation', 'sellkit' ),
				'type'      => 'select',
				'options'   => [
					''         => esc_html__( 'Select one...', 'sellkit' ),
					'phone'    => esc_html__( 'Phone validation', 'sellkit' ),
					'postcode' => esc_html__( 'Postcode Validation', 'sellkit' ),
				],
				'default'   => '',
				'condition' => [
					'shipping_custom_type' => [ 'text', 'select', 'tel' ],
				],
			]
		);

		$repeater->add_control(
			'shipping_list_required',
			[
				'label'   => esc_html__( 'Field Required?', 'sellkit' ),
				'type'    => 'select',
				'options' => [
					'yes' => esc_html__( 'Yes', 'sellkit' ),
					'no'  => esc_html__( 'No', 'sellkit' ),
				],
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'shipping_list_clear',
			[
				'label'       => esc_html__( 'Field Clear', 'sellkit' ),
				'type'        => 'select',
				'description' => 'Applies a clear fix to the field',
				'options'     => [
					'yes' => esc_html__( 'Yes', 'sellkit' ),
					'no'  => esc_html__( 'No', 'sellkit' ),
				],
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'shipping_show_in_thankyou',
			[
				'label'        => esc_html__( 'Show in Thank You Page', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'shipping_list_field' => 'custom_role',
				],
			]
		);

		$repeater->add_control(
			'shipping_show_in_order_mail',
			[
				'label'        => esc_html__( 'Show in Order Email', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'shipping_list_field' => 'custom_role',
				],
			]
		);

		$this->add_control(
			'shipping_list',
			[
				'label'   => esc_html__( 'Shipping Fields', 'sellkit' ),
				'type'    => 'repeater',
				'fields'  => $repeater->get_controls(),
				'item_actions' => [
					'duplicate' => false,
				],
				'prevent_empty' => false,
				'default' => [
					[
						'shipping_list_field'       => 'shipping_first_name',
						'shipping_list_placeholder' => esc_html__( 'First Name', 'sellkit' ),
						'shipping_width'            => 'w-50',
						'shipping_list_required'    => 'yes',
						'shipping_list_clear'       => 'no',
					],
					[
						'shipping_list_field'       => 'shipping_last_name',
						'shipping_list_placeholder' => esc_html__( 'Last Name', 'sellkit' ),
						'shipping_width'            => 'w-50',
						'shipping_list_required'    => 'no',
						'shipping_list_clear'       => 'no',
					],
					[
						'shipping_list_field'       => 'shipping_address_1',
						'shipping_list_placeholder' => esc_html__( 'Address', 'sellkit' ),
						'shipping_width'            => 'w-100',
						'shipping_list_required'    => 'no',
						'shipping_list_clear'       => 'no',
					],
					[
						'shipping_list_field'       => 'shipping_address_2',
						'shipping_list_placeholder' => esc_html__( 'apartment, suit, unit, etc.', 'sellkit' ),
						'shipping_width'            => 'w-100',
						'shipping_list_required'    => 'no',
						'shipping_list_clear'       => 'no',
					],
					[
						'shipping_list_field'       => 'shipping_country',
						'shipping_list_placeholder' => esc_html__( 'Country', 'sellkit' ),
						'shipping_width'            => 'w-33',
						'shipping_list_required'    => 'no',
						'shipping_list_clear'       => 'no',
					],
					[
						'shipping_list_field'       => 'shipping_state',
						'shipping_list_placeholder' => esc_html__( 'State', 'sellkit' ),
						'shipping_width'            => 'w-33',
						'shipping_list_required'    => 'no',
						'shipping_list_clear'       => 'no',
					],
					[
						'shipping_list_field'       => 'shipping_postcode',
						'shipping_list_placeholder' => esc_html__( 'Postal code', 'sellkit' ),
						'shipping_width'            => 'w-34',
						'shipping_list_required'    => 'no',
						'shipping_list_clear'       => 'no',
					],
					[
						'shipping_list_field'       => 'shipping_city',
						'shipping_list_placeholder' => esc_html__( 'City', 'sellkit' ),
						'shipping_width'            => 'w-100',
						'shipping_list_required'    => 'no',
						'shipping_list_clear'       => 'no',
					],
				],
				'title_field' => '{{{ shipping_list_placeholder }}}',
			]
		);

		$this->end_controls_section();
	}

	public function billing_fields() {
		$this->start_controls_section(
			'billing_fields',
			[
				'label' => esc_html__( 'Billings Fields', 'sellkit' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		/* Prevent user to add email field , we added that at login form. billing_email excluded. */
		$repeater->add_control(
			'billing_list_field',
			[
				'label'    => esc_html__( 'Field role', 'sellkit' ),
				'type'     => 'select',
				'options'  => $this->billing_fields_type(),
			]
		);

		$repeater->add_control(
			'billing_custom_type',
			[
				'label'     => esc_html__( 'Custom Field Type', 'sellkit' ),
				'type'      => 'select',
				'options'   => [
					'text'        => esc_html__( 'Text Field', 'sellkit' ),
					'textarea'    => esc_html__( 'Textarea Field', 'sellkit' ),
					'select'      => esc_html__( 'Select Field', 'sellkit' ),
					'multiselect' => esc_html__( 'Multiselect Field', 'sellkit' ),
					'checkbox'    => esc_html__( 'Checkbox', 'sellkit' ),
					'radio'       => esc_html__( 'Radio', 'sellkit' ),
					'tel'         => esc_html__( 'Tel Field', 'sellkit' ),
					'email'       => esc_html__( 'Email Field', 'sellkit' ),
					'hidden'      => esc_html__( 'Hidden Field', 'sellkit' ),
				],
				'condition' => [
					'billing_list_field' => 'custom_role',
				],
				'default' => 'text',
			]
		);

		$repeater->add_control(
			'billing_custom_options',
			[
				'label'       => esc_html__( 'Options', 'sellkit' ),
				'type'        => 'textarea',
				'rows'        => 5,
				'placeholder' => esc_html__( 'e.g. option1:label1', 'sellkit' ),
				'description' => esc_html__( 'Each line one option and separate value and label with (:)', 'sellkit' ),
				'condition'   => [
					'billing_custom_type' => [ 'select', 'multiselect', 'radio' ],
				],
			]
		);

		$repeater->add_control(
			'billing_radio_field_mode',
			[
				'label' => esc_html__( 'Display Mode', 'sellkit' ),
				'type' => 'choose',
				'options' => [
					'inline' => [
						'title' => esc_html__( 'Inline', 'sellkit' ),
						'icon'  => 'eicon-ellipsis-h',
					],
					'list' => [
						'title' => esc_html__( 'List', 'sellkit' ),
						'icon'  => 'eicon-bullet-list',
					],
				],
				'default' => 'list',
				'toggle' => true,
				'condition'   => [
					'billing_custom_type' => [ 'radio' ],
				],
			]
		);

		$repeater->add_control(
			'billing_custom_id',
			[
				'label'       => esc_html__( 'Custom Field ID', 'sellkit' ),
				'type'        => 'text',
				'placeholder' => 'e.g. my_custom_field',
				'condition'   => [
					'billing_list_field' => 'custom_role',
				],
			]
		);

		$repeater->add_control(
			'billing_custom_value',
			[
				'label'     => esc_html__( 'Default Value', 'sellkit' ),
				'type'      => 'text',
				'condition' => [
					'billing_list_field'  => 'custom_role',
					'billing_custom_type' => [ 'text', 'textarea', 'select', 'multiselect', 'radio', 'tel', 'email', 'hidden' ],
				],
			]
		);

		$repeater->add_control(
			'billing_checkbox_custom_value',
			[
				'label'     => esc_html__( 'Default Value', 'sellkit' ),
				'type'      => 'select',
				'options'   => [
					'true'  => 'True',
					'false' => 'False',
				],
				'condition' => [
					'billing_list_field'  => 'custom_role',
					'billing_custom_type' => [ 'checkbox' ],
				],
			]
		);

		$repeater->add_control(
			'billing_width',
			[
				'label'     => esc_html__( 'Width', 'sellkit' ),
				'type'      => 'select',
				'options'   => [
					'w-10'  => esc_html__( '10%', 'sellkit' ),
					'w-20'  => esc_html__( '20%', 'sellkit' ),
					'w-30'  => esc_html__( '30%', 'sellkit' ),
					'w-33'  => esc_html__( '33%', 'sellkit' ),
					'w-34'  => esc_html__( '34%', 'sellkit' ),
					'w-40'  => esc_html__( '40%', 'sellkit' ),
					'w-50'  => esc_html__( '50%', 'sellkit' ),
					'w-60'  => esc_html__( '60%', 'sellkit' ),
					'w-70'  => esc_html__( '70%', 'sellkit' ),
					'w-80'  => esc_html__( '80%', 'sellkit' ),
					'w-90'  => esc_html__( '90%', 'sellkit' ),
					'w-100' => esc_html__( '100%', 'sellkit' ),
				],
				'default' => 'w-100',
			]
		);

		$repeater->add_control(
			'billing_list_placeholder',
			[
				'label' => esc_html__( 'Field Label', 'sellkit' ),
				'type'  => 'text',
				'default' => esc_html__( 'Default label', 'sellkit' ),
			]
		);

		$repeater->add_control(
			'billing_list_class',
			[
				'label'       => esc_html__( 'Field Class', 'sellkit' ),
				'type'        => 'text',
				'description' => esc_html__( 'Divide each class with space character', 'sellkit' ),
			]
		);

		$repeater->add_control(
			'billing_list_validation',
			[
				'label'     => esc_html__( 'Validation', 'sellkit' ),
				'type'      => 'select',
				'options'   => [
					''         => esc_html__( 'Select one...', 'sellkit' ),
					'phone'    => esc_html__( 'Phone validation', 'sellkit' ),
					'postcode' => esc_html__( 'Postcode Validation', 'sellkit' ),
				],
				'default'   => '',
				'condition' => [
					'billing_custom_type' => [ 'text', 'select', 'tel' ],
				],
			]
		);

		$repeater->add_control(
			'billing_list_required',
			[
				'label'   => esc_html__( 'Field Required?', 'sellkit' ),
				'type'    => 'select',
				'options' => [
					'yes' => esc_html__( 'Yes', 'sellkit' ),
					'no'  => esc_html__( 'No', 'sellkit' ),
				],
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'billing_list_clear',
			[
				'label'       => esc_html__( 'Field Clear', 'sellkit' ),
				'type'        => 'select',
				'description' => esc_html__( 'Yes or No, applies a clear fix to the field', 'sellkit' ),
				'options'     => [
					'yes' => esc_html__( 'Yes', 'sellkit' ),
					'no'  => esc_html__( 'No', 'sellkit' ),
				],
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'billing_show_in_thankyou',
			[
				'label'        => esc_html__( 'Show in Thank You Page', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'billing_list_field' => 'custom_role',
				],
			]
		);

		$repeater->add_control(
			'billing_show_in_order_mail',
			[
				'label'        => esc_html__( 'Show in Order Email', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Show', 'sellkit' ),
				'label_off'    => esc_html__( 'Hide', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'billing_list_field' => 'custom_role',
				],
			]
		);

		$this->add_control(
			'billing_list',
			[
				'label'   => esc_html__( 'Billing Fields', 'sellkit' ),
				'type'    => 'repeater',
				'fields'  => $repeater->get_controls(),
				'item_actions' => [
					'duplicate' => false,
				],
				'prevent_empty' => false,
				'default' => [
					[
						'billing_list_field'       => 'billing_first_name',
						'billing_list_placeholder' => esc_html__( 'First Name', 'sellkit' ),
						'billing_width'            => 'w-50',
						'billing_list_required'    => 'yes',
						'billing_list_clear'       => 'no',
					],
					[
						'billing_list_field'       => 'billing_last_name',
						'billing_list_placeholder' => esc_html__( 'Last Name', 'sellkit' ),
						'billing_width'            => 'w-50',
						'billing_list_required'    => 'no',
						'billing_list_clear'       => 'no',
					],
					[
						'billing_list_field'       => 'billing_address_1',
						'billing_list_placeholder' => esc_html__( 'Address', 'sellkit' ),
						'billing_width'            => 'w-100',
						'billing_list_required'    => 'no',
						'billing_list_clear'       => 'no',
					],
					[
						'billing_list_field'       => 'billing_address_2',
						'billing_list_placeholder' => esc_html__( 'apartment, suit, unit, etc.', 'sellkit' ),
						'billing_width'            => 'w-100',
						'billing_list_required'    => 'no',
						'billing_list_clear'       => 'no',
					],
					[
						'billing_list_field'       => 'billing_country',
						'billing_list_placeholder' => esc_html__( 'Country', 'sellkit' ),
						'billing_width'            => 'w-33',
						'billing_list_required'    => 'no',
						'billing_list_clear'       => 'no',
					],
					[
						'billing_list_field'       => 'billing_state',
						'billing_list_placeholder' => esc_html__( 'State', 'sellkit' ),
						'billing_width'            => 'w-33',
						'billing_list_required'    => 'no',
						'billing_list_clear'       => 'no',
					],
					[
						'billing_list_field'       => 'billing_postcode',
						'billing_list_placeholder' => esc_html__( 'Postal code', 'sellkit' ),
						'billing_width'            => 'w-34',
						'billing_list_required'    => 'no',
						'billing_list_clear'       => 'no',
					],
					[
						'billing_list_field'       => 'billing_city',
						'billing_list_placeholder' => esc_html__( 'City', 'sellkit' ),
						'billing_width'            => 'w-100',
						'billing_list_required'    => 'no',
						'billing_list_clear'       => 'no',
					],
				],
				'title_field' => '{{{ billing_list_placeholder }}}',
			]
		);

		$this->end_controls_section();
	}

	private function custom_messages() {
		$this->start_controls_section(
			'custom_messages',
			[
				'label' => esc_html__( 'Custom Messages', 'sellkit' ),
			]
		);

		$this->add_control(
			'empty_cart',
			[
				'type'        => 'text',
				'default'     => esc_html__( 'Your shopping cart is empty.', 'sellkit' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'create_website_account',
			[
				'type'        => 'text',
				'default'     => esc_html__( 'Create a {{website}} account', 'sellkit' ),
				'label_block' => true,
				'description' => '{{website}} will automatically point to your website name',
			]
		);

		$this->add_control(
			'secure_transaction_text',
			[
				'type'        => 'text',
				'default'     => esc_html__( 'All transactions are secure and encrypted.', 'sellkit' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'select_address_text',
			[
				'type'        => 'text',
				'default'     => esc_html__( 'Select the address that matches your card or payment method.', 'sellkit' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}
}
