<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Optin_Field_Tel extends Sellkit_Elementor_Optin_Field_Text {

	public static function get_field_type() {
		return 'tel';
	}

	public function get_input_type() {
		return 'tel';
	}

	public function add_field_render_attribute() {
		parent::add_field_render_attribute();

		$this->widget->add_render_attribute( 'field-' . $this->get_id(), [
			'title'   => esc_html__( 'The value should only consist numbers and phone characters (-, +, (), etc.)', 'sellkit' ),
			'pattern' => '^[0-9\-\+\s\(\)]*$',
		] );

		// Intelligent tel attributes.
		$iti_switcher_options = [
			'iti_tel'                  => 'data-iti-tel',
			'iti_tel_ip_detect'        => 'data-iti-ip-detect',
			'iti_tel_require_area'     => 'data-iti-area-required',
			'iti_tel_internationalize' => 'data-iti-internationalize',
			'iti_tel_allow_dropdown'   => 'data-iti-allow-dropdown',
		];

		foreach ( $iti_switcher_options as $id => $attr ) {
			if ( isset( $this->field[ $id ] ) && 'yes' === $this->field[ $id ] ) {
				$this->widget->add_render_attribute( 'field-' . $this->get_id(), $attr );
			}
		}

		$iti_select_options = [
			'iti_tel_country_include' => 'data-iti-country-include',
			'iti_tel_tel_type' => 'data-iti-tel-type',
		];

		foreach ( $iti_select_options as $id => $attr ) {
			if ( ! empty( $this->field[ $id ] ) || '0' === $this->field[ $id ] ) {
				$this->widget->add_render_attribute( 'field-' . $this->get_id(), $attr, $this->field[ $id ] );
			}
		}
	}

	public static function get_additional_controls() {
		$commons = parent::get_common_controls();

		$iti_enabled = [
			'name'     => 'iti_tel',
			'operator' => '===',
			'value'    => 'yes',
		];

		return [
			'label' => $commons['label'],
			'field_value' => $commons['field_value'],
			'placeholder' => $commons['placeholder'],
			'required' => $commons['required'],
			'iti_tel' => [
				'label'      => esc_html__( 'Intelligent', 'sellkit' ),
				'type'       => 'popover_toggle',
				'conditions' => [
					'terms' => [
						parent::get_type_condition(),
						[
							'name'     => 'required',
							'operator' => '===',
							'value'    => 'true',
						],
					],
				],
			],
			'iti_tel_tel_type' => [
				'label'       => esc_html__( 'Type', 'sellkit' ),
				'type'        => 'select',
				'default'     => 'all',
				'options'     => [
					'all' => esc_html__( 'All', 'sellkit' ),
					'0'   => esc_html__( 'Fixed Line', 'sellkit' ),
					'1'   => esc_html__( 'Mobile', 'sellkit' ),
					'2'   => esc_html__( 'Fixed Line or Mobile', 'sellkit' ),
					'3'   => esc_html__( 'Toll Free', 'sellkit' ),
					'4'   => esc_html__( 'Premium Rate', 'sellkit' ),
					'5'   => esc_html__( 'Shared Cost', 'sellkit' ),
					'6'   => esc_html__( 'VOIP', 'sellkit' ),
					'7'   => esc_html__( 'Personal Number', 'sellkit' ),
					'8'   => esc_html__( 'Pager', 'sellkit' ),
					'9'   => esc_html__( 'UAN', 'sellkit' ),
					'10'  => esc_html__( 'Voicemail', 'sellkit' ),
				],
				'popover'     => [ 'start' => true ],
				'render_type' => 'template',
				'conditions'   => [
					'terms' => [ $iti_enabled ],
				],
			],
			'iti_tel_require_area' => [
				'label'      => esc_html__( 'Require Area Code', 'sellkit' ),
				'type'       => 'switcher',
				'default'    => 'yes',
				'conditions' => [
					'terms' => [ $iti_enabled ],
				],
			],
			'iti_tel_allow_dropdown' => [
				'label'       => esc_html__( 'Allow Dropdown', 'sellkit' ),
				'type'        => 'switcher',
				'default'     => 'yes',
				'render_type' => 'template',
				'conditions'  => [
					'terms' => [ $iti_enabled ],
				],
			],
			'iti_tel_country_include' => [
				'label'       => esc_html__( 'Only Include Countries', 'sellkit' ),
				'type'        => 'select2',
				'options'     => [],
				'description' => esc_html__( 'Leave empty to include all countries.', 'sellkit' ),
				'label_block' => true,
				'multiple'    => true,
				'render_type' => 'template',
				'conditions'  => [
					'terms' => [ $iti_enabled ],
				],
			],
			'iti_tel_ip_detect' => [
				'label'       => esc_html__( 'Auto Detect by IP', 'sellkit' ),
				'type'        => 'switcher',
				'default'     => 'yes',
				'render_type' => 'template',
				'conditions'  => [
					'terms' => [
						$iti_enabled,
						[
							'name' => 'iti_tel_allow_dropdown',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			],
			'iti_tel_internationalize' => [
				'label'       => esc_html__( 'Internationalize', 'sellkit' ),
				'type'        => 'switcher',
				'popover'     => [ 'end' => true ],
				'render_type' => 'template',
				'description' => esc_html__( 'Convert entered national numbers to international format on form submit.', 'sellkit' ),
				'conditions'  => [
					'terms' => [ $iti_enabled ],
				],
			],
			'width_responsive' => $commons['width_responsive'],
		];
	}
}
