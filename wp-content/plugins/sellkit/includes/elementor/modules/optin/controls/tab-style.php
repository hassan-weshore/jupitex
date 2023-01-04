<?php

defined( 'ABSPATH' ) || die();

/**
 * Holds methods for adding Optin widget's style tab controls.
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class Sellkit_Elementor_Optin_Tab_Style {

	public function __construct( $widget ) {
		$this->add_section_general( $widget );
		$this->add_section_label( $widget );
		$this->add_section_field( $widget );
		$this->add_section_select( $widget );
		$this->add_section_checkbox( $widget );
		$this->add_section_radio( $widget );
		$this->add_section_button( $widget );
		$this->add_section_message_style( $widget );
	}

	public static function add_section_general( $widget ) {
		$widget->start_controls_section(
			'section_style_general',
			[
				'label' => esc_html__( 'General', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$widget->add_responsive_control(
			'general_column_spacing',
			[
				'label'   => esc_html__( 'Column Spacing', 'sellkit' ),
				'type'    => 'slider',
				'default' => [ 'size' => 7 ],
				'range'   => [
					'px' => [
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-group' => 'padding-left: calc( {{SIZE}}{{UNIT}} / 2 );padding-right: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}} .sellkit-optin'       => 'margin-left : calc( -{{SIZE}}{{UNIT}} / 2 );margin-right: calc( -{{SIZE}}{{UNIT}} / 2 );',
				],
			]
		);

		$widget->add_responsive_control(
			'general_row_spacing',
			[
				'label'     => esc_html__( 'Row Spacing', 'sellkit' ),
				'type'      => 'slider',
				'default'   => [ 'size' => 7 ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-group:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->end_controls_section();
	}

	public static function add_section_label( $widget ) {
		$all_4s = '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}';

		$widget->start_controls_section(
			'section_style_label',
			[
				'label' => esc_html__( 'Label', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$widget->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-label' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .sellkit-field-label',
				'scheme'   => '3',
			]
		);

		$widget->add_responsive_control(
			'label_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field-group > .sellkit-field-label' => "padding: {$all_4s};",
				],
			]
		);

		$widget->end_controls_section();
	}

	public static function add_section_field( $widget ) {
		$all_4s = '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}';

		$widget->start_controls_section(
			'section_style_field',
			[
				'label' => esc_html__( 'Field', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$widget->start_controls_tabs( 'field_tabs_state' );

		//-------------TAB NORMAL-------------
		$widget->start_controls_tab(
			'field_tab_state_normal',
			[
				'label' => esc_html__( 'Normal', 'sellkit' ),
			]
		);

		$widget->add_control(
			'field_tab_background_color_normal',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field' => 'background-color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'border',
			[
				'name'     => 'field_tab_border_normal',
				'selector' => '{{WRAPPER}} .sellkit-field:not(:focus)',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'color' => [
						'default' => '#111111',
					],
				],
			]
		);

		$widget->add_responsive_control(
			'field_tab_border_radius_normal',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field'                           => "border-radius: {$all_4s};",
					'{{WRAPPER}} .iti__flag-container .iti__selected-flag' => 'border-radius: {{TOP}}{{UNIT}} 0 0 {{LEFT}}{{UNIT}};',
				],
			]
		);

		$widget->add_group_control(
			'box-shadow',
			[
				'name'      => 'field_tab_box_shadow_normal',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .sellkit-field',
				'exclude'   => [ 'box_shadow_position' ],
			]
		);

		$widget->add_control(
			'field_tab_placeholder_heading_normal',
			[
				'type'      => 'heading',
				'separator' => 'before',
				'label'     => esc_html__( 'Placeholder', 'sellkit' ),
			]
		);

		$widget->add_control(
			'field_tab_color_placeholder',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field:placeholder-shown::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'     => 'field_tab_typography_placeholder',
				'selector' => '{{WRAPPER}} .sellkit-field:placeholder-shown:not(:focus)',
				'scheme'   => '3',
			]
		);

		$widget->add_control(
			'field_tab_value_heading_normal',
			[
				'type'      => 'heading',
				'separator' => 'before',
				'label'     => esc_html__( 'Value', 'sellkit' ),
			]
		);

		$widget->add_control(
			'field_tab_color_value',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field:not(:placeholder-shown)' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'     => 'field_tab_typography_value',
				'selector' => '{{WRAPPER}} .sellkit-field:not(:placeholder-shown):not(:focus)',
				'scheme'   => '3',
			]
		);

		$widget->end_controls_tab();

		//-------------TAB FOCUS-------------
		$widget->start_controls_tab(
			'field_tab_state_focus',
			[
				'label' => esc_html__( 'Focus', 'sellkit' ),
			]
		);

		$widget->add_control(
			'field_tab_background_color_focus',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'border',
			[
				'name'     => 'field_tab_border_focus',
				'selector' => '{{WRAPPER}} .sellkit-field:focus',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'color' => [
						'default' => '#111111',
					],
				],
			]
		);

		$widget->add_responsive_control(
			'field_tab_border_radius_focus',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field:focus' => "border-radius: {$all_4s};",
				],
			]
		);

		$widget->add_group_control(
			'box-shadow',
			[
				'name'      => 'field_tab_box_shadow_focus',
				'exclude'   => [ 'box_shadow_position' ],
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .sellkit-field:focus',
			]
		);

		$widget->add_control(
			'field_tab_placeholder_heading_focus',
			[
				'type'      => 'heading',
				'separator' => 'before',
				'label'     => esc_html__( 'Placeholder', 'sellkit' ),
			]
		);

		$widget->add_control(
			'field_tab_color_placeholder_foucus',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field:placeholder-shown:focus::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'     => 'field_tab_typography_placeholder_foucs',
				'selector' => '{{WRAPPER}} .sellkit-field:placeholder-shown:focus',
				'scheme'   => '3',
			]
		);

		$widget->add_control(
			'field_tab_value_heading_foucs',
			[
				'type'      => 'heading',
				'separator' => 'before',
				'label'     => esc_html__( 'Value', 'sellkit' ),
			]
		);

		$widget->add_control(
			'field_tab_color_value_foucs',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field:not(:placeholder-shown):focus' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'     => 'field_tab_typography_value_foucs',
				'selector' => '{{WRAPPER}} .sellkit-field:not(:placeholder-shown):focus',
				'scheme'   => '3',
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();

		$widget->add_responsive_control(
			'field_padding',
			[
				'label'      => esc_html__( 'Padding', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field' => "padding: {$all_4s};",
				],
			]
		);

		$widget->end_controls_section();
	}

	public static function add_section_select( $widget ) {
		$widget->start_controls_section(
			'section_style_select',
			[
				'label' => esc_html__( 'Select', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$widget->add_control(
			'select_arrow_icon',
			[
				'label'       => esc_html__( 'Icon', 'sellkit' ),
				'type'        => 'icons',
				'skin'        => 'inline',
				'label_block' => false,
				'default'     => [
					'value'   => 'fas fa-angle-down',
					'library' => 'fa-solid',
				],
			]
		);

		$widget->add_control(
			'select_arrow_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-select-arrow'       => 'color: {{VALUE}};',
					'{{WRAPPER}} .sellkit-field-select-arrow > svg' => 'fill : {{VALUE}};',
					'{{WRAPPER}} svg.sellkit-field-select-arrow'    => 'fill : {{VALUE}};',
				],
			]
		);

		$widget->add_responsive_control(
			'select_arrow_size',
			[
				'label'     => esc_html__( 'Size', 'sellkit' ),
				'type'      => 'slider',
				'default'   => [ 'size' => '20' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-select-arrow'       => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sellkit-field-select-arrow > svg' => 'width    : {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} svg.sellkit-field-select-arrow'    => 'width    : {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'select_arrow_vertical_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'sellkit' ),
				'type'       => 'slider',
				'size_units' => [ 'px', '%', 'vm' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field-select-arrow' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'select_arrow_horizontal_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'sellkit' ),
				'type'       => 'slider',
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'size' => '13',
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field-select-arrow' => ( is_rtl() ? 'left' : 'right' ) . ':{{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->end_controls_section();
	}

	public static function add_section_checkbox( $widget ) {
		$all_4s = '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}';

		$widget->start_controls_section(
			'section_style_checkbox',
			[
				'label' => esc_html__( 'Checkbox', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$widget->add_responsive_control(
			'checkbox_size',
			[
				'label' => esc_html__( 'Size', 'sellkit' ),
				'type' => 'slider',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field-subgroup .sellkit-field-label'          => 'padding-left: calc({{SIZE}}{{UNIT}} + 8px); line-height: calc({{SIZE}}{{UNIT}} + 2px);',
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field-subgroup .sellkit-field-label:before'   => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field-subgroup .sellkit-field-label:after'    => 'width: calc({{SIZE}}{{UNIT}} - 8px); height: calc({{SIZE}}{{UNIT}} - 8px);',
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field-subgroup .sellkit-field-label'        => 'padding-left: calc({{SIZE}}{{UNIT}} + 8px);line-height: calc({{SIZE}}{{UNIT}} + 2px);',
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field-subgroup .sellkit-field-label:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field-subgroup .sellkit-field-label:after'  => 'width: calc({{SIZE}}{{UNIT}} - 8px); height: calc({{SIZE}}{{UNIT}} - 8px);',
				],
			]
		);

		$widget->add_control(
			'checkbox_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field-subgroup .sellkit-field-label'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field-subgroup .sellkit-field-label' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'     => 'checkbox_typography',
				'scheme'   => '3',
				'selector' => implode( ', ', [
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field-subgroup .sellkit-field-label',
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field-subgroup .sellkit-field-label',
				] ),
			]
		);

		$widget->add_responsive_control(
			'checkbox_spacing_between',
			[
				'label'      => esc_html__( 'Spacing Between', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field-option'   => "margin: {$all_4s};",
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field-option' => "margin: {$all_4s};",
				],
			]
		);

		$widget->add_responsive_control(
			'checkbox_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field-subgroup'   => "padding: {$all_4s};",
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field-subgroup' => "padding: {$all_4s};",
				],
			]
		);

		$widget->start_controls_tabs( 'checkbox_tabs_state' );

		$widget->start_controls_tab(
			'checkbox_tab_state_normal',
			[
				'label' => esc_html__( 'Normal', 'sellkit' ),
			]
		);

		$widget->add_control(
			'checkbox_tab_background_color_normal',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field ~ .sellkit-field-label:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field ~ .sellkit-field-label:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'border',
			[
				'name'           => 'checkbox_tab_border_normal',
				'selector'       => implode( ', ', [
					'{{WRAPPER}} .sellkit-field-option-checkbox .sellkit-field:not(:checked) ~ label:before',
					'{{WRAPPER}} .sellkit-field-option-acceptance .sellkit-field:not(:checked) ~ label:before',
				] ),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'color' => [
						'default' => '#111111',
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'sellkit' ),
					],
				],
			]
		);

		$widget->add_group_control(
			'box-shadow',
			[
				'name'     => 'checkbox_tab_box_shadow_normal',
				'selector' => implode( ', ', [
					'{{WRAPPER}} .sellkit-field-option-checkbox .sellkit-field:not(:checked) ~ label:before',
					'{{WRAPPER}} .sellkit-field-option-acceptance .sellkit-field:not(:checked) ~ label:before',
				] ),
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'checkbox_tab_state_checked',
			[
				'label' => esc_html__( 'Checked', 'sellkit' ),
			]
		);

		$widget->add_control(
			'checkbox_tab_background_color_checked',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field:checked ~ label:after'   => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field:checked ~ label:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'border',
			[
				'name'           => 'checkbox_tab_border_checked',
				'selector'       => implode( ', ', [
					'{{WRAPPER}} .sellkit-field-option-checkbox .sellkit-field:checked ~ label:before',
					'{{WRAPPER}} .sellkit-field-option-acceptance .sellkit-field:checked ~ label:before',
				] ),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'color' => [
						'default' => '#111111',
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'sellkit' ),
					],
				],
			]
		);

		$widget->add_group_control(
			'box-shadow',
			[
				'name'     => 'checkbox_tab_box_shadow_checked',
				'selector' => implode( ', ', [
					'{{WRAPPER}} .sellkit-field-option-checkbox .sellkit-field:checked ~ label:before',
					'{{WRAPPER}} .sellkit-field-option-acceptance .sellkit-field:checked ~ label:before',
				] ),
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();

		$widget->add_control(
			'checkbox_separator',
			[
				'type' => 'divider',
			]
		);

		$widget->add_control(
			'checkbox_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field-subgroup .sellkit-field-label:before'   => "border-radius: {$all_4s};",
					'{{WRAPPER}} .sellkit-field-type-checkbox .sellkit-field-subgroup .sellkit-field-label:after'    => "border-radius: {$all_4s};",
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field-subgroup .sellkit-field-label:before' => "border-radius: {$all_4s};",
					'{{WRAPPER}} .sellkit-field-type-acceptance .sellkit-field-subgroup .sellkit-field-label:after'  => "border-radius: {$all_4s};",
				],
			]
		);

		$widget->end_controls_section();
	}

	public static function add_section_radio( $widget ) {
		$all_4s = '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}';

		$widget->start_controls_section(
			'section_style_radio',
			[
				'label' => esc_html__( 'Radio', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$widget->add_responsive_control(
			'radio_size',
			[
				'label'     => esc_html__( 'Size', 'sellkit' ),
				'type'      => 'slider',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-type-radio .sellkit-field-subgroup .sellkit-field-label'        => 'padding-left: calc({{SIZE}}{{UNIT}} + 8px);line-height: calc({{SIZE}}{{UNIT}} + 2px);',
					'{{WRAPPER}} .sellkit-field-type-radio .sellkit-field-subgroup .sellkit-field-label:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sellkit-field-type-radio .sellkit-field-subgroup .sellkit-field-label:after'  => 'width: calc({{SIZE}}{{UNIT}} - 8px); height: calc({{SIZE}}{{UNIT}} - 8px);',
				],
			]
		);

		$widget->add_control(
			'radio_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-type-radio .sellkit-field-subgroup .sellkit-field-label' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'     => 'radio_typography',
				'selector' => '{{WRAPPER}} .sellkit-field-type-radio .sellkit-field-subgroup .sellkit-field-label',
				'scheme'   => '3',
			]
		);

		$widget->add_responsive_control(
			'radio_spacing_between',
			[
				'label'      => esc_html__( 'Spacing Between', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field-type-radio .sellkit-field-option' => "margin: {$all_4s};",
				],
			]
		);

		$widget->add_responsive_control(
			'radio_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-field-type-radio .sellkit-field-subgroup' => "padding: {$all_4s};",
				],
			]
		);

		$widget->start_controls_tabs( 'radio_tabs_state' );

		$widget->start_controls_tab(
			'radio_tab_state_normal',
			[
				'label' => esc_html__( 'Normal', 'sellkit' ),
			]
		);

		$widget->add_control(
			'radio_tab_background_color_normal',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-type-radio .sellkit-field ~ .sellkit-field-label:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'border',
			[
				'name'           => 'radio_tab_border_normal',
				'selector'       => '{{WRAPPER}} .sellkit-field-type-radio .sellkit-field:not(:checked) ~ .sellkit-field-label:before',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'color' => [
						'default' => '#111111',
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'sellkit' ),
					],
				],
			]
		);

		$widget->add_group_control(
			'box-shadow',
			[
				'name'     => 'radio_tab_box_shadow_normal',
				'selector' => '{{WRAPPER}} .sellkit-field-type-radio .sellkit-field:not(:checked) ~ .sellkit-field-label:before',
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'radio_tab_state_checked',
			[
				'label' => esc_html__( 'Checked', 'sellkit' ),
			]
		);

		$widget->add_control(
			'radio_tab_background_color_checked',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-field-type-radio .sellkit-field:checked ~ .sellkit-field-label:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'border',
			[
				'name'           => 'radio_tab_border_checked',
				'selector'       => '{{WRAPPER}} .sellkit-field-type-radio .sellkit-field:checked ~ .sellkit-field-label:before',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'color' => [
						'default' => '#111111',
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'sellkit' ),
					],
				],
			]
		);

		$widget->add_group_control(
			'box-shadow',
			[
				'name'     => 'radio_tab_box_shadow_checked',
				'selector' => '{{WRAPPER}} .sellkit-field-type-radio .sellkit-field:checked ~ .sellkit-field-label:before',
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();

		$widget->end_controls_section();
	}

	public static function add_section_button( $widget ) {
		$all_4s = '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}';

		$widget->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$widget->add_responsive_control(
			'button_width',
			[
				'label'      => esc_html__( 'Width', 'sellkit' ),
				'type'       => 'slider',
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'size' => 100,
					'unit' => '%',
				],
				'range'      => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-submit-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'button_height',
			[
				'label'     => esc_html__( 'Height', 'sellkit' ),
				'type'      => 'slider',
				'range'     => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-submit-button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'button_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-submit-button' => "margin: {$all_4s};",
				],
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'     => 'button_typography_text',
				'scheme'   => '3',
				'selector' => '{{WRAPPER}} button.sellkit-submit-button span.sellkit-submit-button-text',
				'fields_options' => [
					'typography' => [
						'label' => esc_html__( 'Text Typography', 'sellkit' ),
					],
				],
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'     => 'button_typography_subtext',
				'scheme'   => '3',
				'selector' => '{{WRAPPER}} button.sellkit-submit-button span.sellkit-submit-button-subtext',
				'fields_options' => [
					'typography' => [
						'label' => esc_html__( 'SubText Typography', 'sellkit' ),
					],
				],
			]
		);

		$widget->add_responsive_control(
			'button_align',
			[
				'label'        => esc_html__( 'Alignment', 'sellkit' ),
				'type'         => 'choose',
				'toggle'       => false,
				'default'      => 'flex-end',
				'options'      => [
					'flex-start'   => [
						'title' => esc_html__( 'Left', 'sellkit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sellkit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'  => [
						'title' => esc_html__( 'Right', 'sellkit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'    => [
					'{{WRAPPER}} div.sellkit-field-type-submit-button' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$widget->start_controls_tabs( 'button_tabs' );

		$widget->start_controls_tab(
			'button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'sellkit' ),
			]
		);

		$widget->add_control(
			'button_normal_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-submit-button' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'background',
			[
				'name'     => 'button_normal_background',
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .sellkit-submit-button',
			]
		);

		$widget->add_group_control(
			'box-shadow',
			[
				'name'      => 'button_normal_box_shadow',
				'selector'  => '{{WRAPPER}} .sellkit-submit-button',
				'exclude'   => [ 'box_shadow_position' ],
			]
		);

		$widget->add_control(
			'button_normal_border_heading',
			[
				'label'     => esc_html__( 'Border', 'sellkit' ),
				'type'      => 'heading',
				'separator' => 'before',
			]
		);

		$widget->add_group_control(
			'border',
			[
				'name'           => 'button_normal_border',
				'selector'       => '{{WRAPPER}} .sellkit-submit-button',
				'fields_options' => [
					'border'   => [
						'default' => 'solid',
					],
					'width'    => [
						'label' => esc_html__( 'Border Width', 'sellkit' ),
					],
					'color'    => [
						'default' => '#111111',
					],
				],
			]
		);

		$widget->add_responsive_control(
			'button_normal_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-submit-button' => "border-radius: {$all_4s};",
				],
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'button_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'sellkit' ),
			]
		);

		$widget->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-submit-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			'background',
			[
				'name'     => 'button_hover_background',
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .sellkit-submit-button:hover',
			]
		);

		$widget->add_group_control(
			'box-shadow',
			[
				'name'      => 'button_hover_box_shadow',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .sellkit-submit-button:hover',
				'exclude'   => [ 'box_shadow_position' ],
			]
		);

		$widget->add_control(
			'button_hover_border_heading',
			[
				'label'     => esc_html__( 'Border', 'sellkit' ),
				'type'      => 'heading',
				'separator' => 'before',
			]
		);

		$widget->add_group_control(
			'border',
			[
				'name'           => 'button_hover_border',
				'selector'       => '{{WRAPPER}} .sellkit-submit-button:hover',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'unit'   => 'px',
							'top'    => '1',
							'left'   => '1',
							'right'  => '1',
							'bottom' => '1',
						],
					],
					'color'  => [
						'default' => '#111111',
					],
				],
			]
		);

		$widget->add_responsive_control(
			'button_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-submit-button:hover' => "border-radius: {$all_4s};",
				],
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();

		$widget->add_control(
			'button_icon_heading',
			[
				'label'     => esc_html__( 'Icon', 'sellkit' ),
				'type'      => 'heading',
				'separator' => 'before',
				'condition'  => [ 'submit_button_icon[value]!' => '' ],
			]
		);

		$widget->add_responsive_control(
			'button_icon_size',
			[
				'label'      => esc_html__( 'Size', 'sellkit' ),
				'type'       => 'slider',
				'condition'  => [ 'submit_button_icon[value]!' => '' ],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-submit-button i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sellkit-submit-button svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_control(
			'button_icon_space_between',
			[
				'label'      => esc_html__( 'Space Between', 'sellkit' ),
				'type'       => 'slider',
				'condition'  => [ 'submit_button_icon[value]!' => '' ],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}}.sellkit-optin-button-icon-left .sellkit-submit-button i'    => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.sellkit-optin-button-icon-left .sellkit-submit-button svg'  => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.sellkit-optin-button-icon-right .sellkit-submit-button i'   => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.sellkit-optin-button-icon-right .sellkit-submit-button svg' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_control(
			'button_icon_align',
			[
				'label'        => esc_html__( 'Alignment', 'sellkit' ),
				'type'         => 'choose',
				'toggle'       => false,
				'condition'    => [ 'submit_button_icon[value]!' => '' ],
				'prefix_class' => 'sellkit-optin-button-icon-',
				'default'      => 'left',
				'options'      => [
					'left'  => [
						'title' => esc_html__( 'Left', 'sellkit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'sellkit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		$widget->start_controls_tabs( 'button_icon_tabs' );

		$widget->start_controls_tab(
			'button_icon_tabs_normal',
			[
				'label'     => esc_html__( 'Normal', 'sellkit' ),
				'condition' => [
					'submit_button_icon[library]!' => [ '', 'svg' ],
				],
			]
		);

		$widget->add_control(
			'button_icon_color_normal',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-submit-button i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .sellkit-submit-button svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'submit_button_icon[library]!' => [ '', 'svg' ],
				],
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'button_icon_tabs_hover',
			[
				'label'     => esc_html__( 'Hover', 'sellkit' ),
				'condition' => [
					'submit_button_icon[library]!' => [ '', 'svg' ],
				],
			]
		);

		$widget->add_control(
			'button_icon_color_hover',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-submit-button:hover i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .sellkit-submit-button:hover svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'submit_button_icon[library]!' => [ '', 'svg' ],
				],
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();

		$widget->end_controls_section();
	}

	public static function add_section_message_style( $widget ) {

		$widget->start_controls_section(
			'message_text_style',
			[
				'label' => esc_html__( 'Messages', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$widget->add_group_control(
			'typography',
			[
				'name'      => 'message_text_typography',
				'scheme'    => '3',
				'selector' => '{{WRAPPER}} .sellkit-optin-response, {{WRAPPER}} .sellkit-optin small.sellkit-optin-text',
			]
		);

		$widget->add_control(
			'seccess_message_color',
			[
				'label'     => esc_html__( 'Success Message Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-optin-success .sellkit-optin-response' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_control(
			'error_message_color',
			[
				'label'     => esc_html__( 'Error Message Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-optin-error .sellkit-optin-response' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_control(
			'inline_message_color',
			[
				'label'     => esc_html__( 'Inline Message Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-optin .sellkit-optin-text' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->end_controls_section();
	}
}
