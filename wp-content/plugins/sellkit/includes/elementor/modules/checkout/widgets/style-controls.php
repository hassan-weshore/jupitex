<?php

namespace Sellkit\Elementor\Modules\Checkout\Widgets;

defined( 'ABSPATH' ) || die();

class Style_Controls extends \Sellkit_Elementor_Checkout_Widget {
	public function __construct() {
		$this->form_style();
		$this->heading_style();
		$this->divider_style();
		$this->step_style();
		$this->buttons_style();
		$this->fields_style();
		$this->order_summary_style();
	}

	private function form_style() {
		$this->start_controls_section(
			'section_form_style',
			[
				'label' => esc_html__( 'Form', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$this->add_control(
			'form_col_gap',
			[
				'label'      => esc_html__( 'Columns gap', 'sellkit' ),
				'type'       => 'slider',
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} #sellkit-checkout-multistep-inner-wrap' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [
					'layout-type' => 'multi-step',
				],
			]
		);

		$this->add_control(
			'form_row_gap',
			[
				'label'      => esc_html__( 'Row gap', 'sellkit' ),
				'type'       => 'slider',
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-checkout' => 'row-gap: {{SIZE}}{{UNIT}}; display:grid',
				],
				'condition'    => [
					'layout-type' => 'one-page',
				],
			]
		);

		$this->add_control(
			'form_links_color',
			[
				'label'     => esc_html__( 'Links Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-widget-links' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'form_links_hover_color',
			[
				'label'     => esc_html__( 'Links Hover Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-widget-links:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'form_collapsible_background',
			[
				'label'     => esc_html__( 'Collapsible Form Background', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id #sellkit-checkout-billing-field-wrapper' => 'background-color: {{VALUE}} !important',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-multistep-checkout-sidebar .sellkit-mobile-multistep-order-summary' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_section();
	}

	private function heading_style() {
		$this->start_controls_section(
			'section_heading_style',
			[
				'label' => esc_html__( 'Heading', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'heading_typography2',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' =>
					'{{WRAPPER}} .heading,
					{{WRAPPER}} #sellkit-checkout-widget-id form .sellkit-one-page-checkout-shipping .shipping_address #shipping_text_title,
					{{WRAPPER}} #sellkit-checkout-widget-id form .sellkit-multistep-checkout-second #shipping_header_title,
					{{WRAPPER}} #sellkit-checkout-widget-id form .sellkit-multistep-checkout-third #payment_method_title,
					{{WRAPPER}} #sellkit-checkout-widget-id h4#shipping_header_title',
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .heading' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id form .sellkit-one-page-checkout-shipping .shipping_address #shipping_text_title' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id form .sellkit-multistep-checkout-second #shipping_header_title' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id form .sellkit-multistep-checkout-third #payment_method_title' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id h4#shipping_header_title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'heading_align',
			[
				'label'     => esc_html__( 'Alignment', 'sellkit' ),
				'type'      => 'choose',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'sellkit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sellkit' ),
						'icon'  => 'eicon-text-align-justify',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'sellkit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .heading' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} #sellkit-checkout-widget-id form .sellkit-one-page-checkout-shipping .shipping_address #shipping_text_title' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} #sellkit-checkout-widget-id form .sellkit-multistep-checkout-second #shipping_header_title' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} #sellkit-checkout-widget-id form .sellkit-multistep-checkout-third #payment_method_title' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} #sellkit-checkout-widget-id h4#shipping_header_title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->divider( 'hr_01' );

		$this->add_control(
			'sub_heading_title',
			[
				'label' => esc_html__( 'Sub Heading', 'sellkit' ),
				'type'  => 'raw_html',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'sub-heading_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} #sellkit-checkout-widget-id .sub-heading',
			]
		);

		$this->add_control(
			'sub-heading_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sub-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'sub-heading_align',
			[
				'label'     => esc_html__( 'Alignment', 'sellkit' ),
				'type'      => 'choose',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'sellkit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sellkit' ),
						'icon'  => 'eicon-text-align-justify',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'sellkit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sub-heading' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function divider_style() {
		$this->start_controls_section(
			'section_divider_style',
			[
				'label' => esc_html__( 'Divider', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name'     => 'divider_border',
				'label'    => esc_html__( 'Border', 'sellkit' ),
				'selector' => '{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-widget-divider,
							   {{WRAPPER}} #sellkit-checkout-widget-id #order_review #sellkit-checkout-widget-order-review-wrap .sellkit-checkout-widget-divider,
							   {{WRAPPER}} #sellkit-checkout-widget-id #sellkit-checkout-multistep-inner-wrap .sellkit-checkout-widget-divider',
			]
		);

		$this->add_responsive_control(
			'divider_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-checkout-widget-divider' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'divider_border_border!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	private function step_style() {
		$this->start_controls_section(
			'section_step_style',
			[
				'label' => esc_html__( 'Step', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'step_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} .sellkit-checkout-widget-breadcrumb span',
			]
		);

		$this->add_responsive_control(
			'step_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'sellkit' ),
				'type'      => 'choose',
				'options'   => [
					'flex-start'   => [
						'title' => esc_html__( 'Left', 'sellkit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sellkit' ),
						'icon'  => 'eicon-text-align-justify',
					],
					'flex-end'  => [
						'title' => esc_html__( 'Right', 'sellkit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .sellkit-checkout-widget-breadcrumb' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs(
			'step_style_tabs'
		);

		$this->start_controls_tab(
			'style_inactive_tab',
			[
				'label' => esc_html__( 'UPCOMING', 'sellkit' ),
			]
		);

		$this->add_control(
			'step_inactive_links_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #checkout-widget-breadcrumb span.inactive' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'step_inactive_links_color_hove',
			[
				'label'     => esc_html__( 'Links Hover Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #checkout-widget-breadcrumb span.inactive:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_active_tab',
			[
				'label' => esc_html__( 'IN-PROGRESS', 'sellkit' ),
			]
		);

		$this->add_control(
			'step_active_links_color',
			[
				'label'     => esc_html__( 'Links Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #checkout-widget-breadcrumb span.current' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'step_active_links_color_hove',
			[
				'label'     => esc_html__( 'Links Hover Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #checkout-widget-breadcrumb span.current:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_completed_tab',
			[
				'label' => esc_html__( 'COMPLETED', 'sellkit' ),
			]
		);

		$this->add_control(
			'step_completed_links_color',
			[
				'label'     => esc_html__( 'Links Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #checkout-widget-breadcrumb span.blue-line' => 'color: {{VALUE}}',
					'{{WRAPPER}} #checkout-widget-breadcrumb span.blue-line a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'step_completed_links_color_hove',
			[
				'label'     => esc_html__( 'Links Hover Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #checkout-widget-breadcrumb span.blue-line:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} #checkout-widget-breadcrumb span.blue-line a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'step_heading_1',
			[
				'label'     => esc_html__( 'Separator Icon', 'sellkit' ),
				'type'      => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'step_separator_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #checkout-widget-breadcrumb .sellkit-checkout-widget-bc-icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'step_separator_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'sellkit' ),
				'type'       => 'slider',
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} #checkout-widget-breadcrumb .sellkit-checkout-widget-bc-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'step_separator_icon_spacing',
			[
				'label'      => esc_html__( 'Icon Spacing', 'sellkit' ),
				'type'       => 'slider',
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-checkout-widget-breadcrumb' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Button styles.
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function buttons_style() {
		$this->start_controls_section(
			'section_buttons_style',
			[
				'label' => esc_html__( 'Button', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$this->add_control(
			'button_style_heading_1',
			[
				'label' => esc_html__( 'Primary button', 'sellkit' ),
				'type'  => 'heading',
			]
		);

		$this->start_controls_tabs(
			'button_primary_style_tabs'
		);

		$this->start_controls_tab(
			'button_primary_style_normal_tab',
			[
				'label' => esc_html__( 'NORMAL', 'sellkit' ),
			]
		);

		$this->add_control(
			'button_primary_normal_color',
			[
				'label'     => esc_html__( 'Text Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-checkout-widget-primary-button' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'button_primary_normal_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-widget-primary-button',
				'fields_options' => [
					'typography'     => [
						'default' => 'yes',
					],
					'text_transform' => [
						'default' => 'none',
					],
				],
			]
		);

		$this->add_control(
			'button_primary_normal_bg',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-checkout-widget-primary-button' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_primary_style_hover_tab',
			[
				'label' => esc_html__( 'HOVER', 'sellkit' ),
			]
		);

		$this->add_control(
			'button_primary_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-checkout-widget-primary-button:hover' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'button_primary_hover_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-widget-primary-button:hover',
			]
		);

		$this->add_control(
			'button_primary_hover_bg',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-checkout-widget-primary-button:hover' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->divider( 'button_end_primary_hr' );

		$this->add_group_control(
			'border',
			[
				'name'     => 'button_primary_border',
				'label'    => esc_html__( 'Border', 'sellkit' ),
				'selector' => '{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-widget-primary-button',
			]
		);

		$this->add_responsive_control(
			'button_primary_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-widget-primary-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_primary_text_padding',
			[
				'label'      => esc_html__( 'Text Padding', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-widget-primary-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_style_heading_2',
			[
				'label'     => esc_html__( 'Secondary button', 'sellkit' ),
				'type'      => 'heading',
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs(
			'button_secondary_style_tabs'
		);

		$this->start_controls_tab(
			'button_secondary_style_normal_tab',
			[
				'label' => esc_html__( 'NORMAL', 'sellkit' ),
			]
		);

		$this->add_control(
			'button_secondary_normal_color',
			[
				'label'     => esc_html__( 'Text Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout .sellkit-checkout-widget-secondary-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'button_secondary_normal_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} .woocommerce-checkout .sellkit-checkout-widget-secondary-button',
			]
		);

		$this->add_control(
			'button_secondary_normal_bg',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout .sellkit-checkout-widget-secondary-button' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_secondary_style_hover_tab',
			[
				'label' => esc_html__( 'HOVER', 'sellkit' ),
			]
		);

		$this->add_control(
			'button_secondary_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-checkout-widget-secondary-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'button_secondary_hover_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} .sellkit-checkout-widget-secondary-button:hover',
			]
		);

		$this->add_control(
			'button_secondary_hover_bg',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-checkout-widget-secondary-button:hover' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->divider( 'button_end_secondary_hr' );

		$this->add_group_control(
			'border',
			[
				'name'     => 'button_secondary_border',
				'label'    => esc_html__( 'Border', 'sellkit' ),
				'selector' => '{{WRAPPER}} .sellkit-checkout-widget-secondary-button',
			]
		);

		$this->add_responsive_control(
			'button_secondary_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-checkout-widget-secondary-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_secondary_text_padding',
			[
				'label'      => esc_html__( 'Text Padding', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .sellkit-checkout-widget-secondary-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_style_heading_3',
			[
				'label'     => esc_html__( 'Return Link', 'sellkit' ),
				'type'      => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'button_return_link_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} .woocommerce-checkout .sellkit-checkout-widget-return-button > span',
			]
		);

		$this->start_controls_tabs(
			'button_return_link_style_tabs'
		);

		$this->start_controls_tab(
			'button_return_link_style_normal_tab',
			[
				'label' => esc_html__( 'NORMAL', 'sellkit' ),
			]
		);

		$this->add_control(
			'button_return_link_normal_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-checkout-widget-return-button > span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .sellkit-checkout-widget-return-button > i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_return_link_style_hover_tab',
			[
				'label' => esc_html__( 'HOVER', 'sellkit' ),
			]
		);

		$this->add_control(
			'button_return_link_hover_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-checkout-widget-return-button:hover > *' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function fields_style() {
		$this->start_controls_section(
			'section_fields_style',
			[
				'label' => esc_html__( 'Field', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$this->add_control(
			'field_style_heading_1',
			[
				'label' => esc_html__( 'Label', 'sellkit' ),
				'type'  => 'heading',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'field_label_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} .sellkit-widget-checkout-fields p input::placeholder,
					.sellkit-widget-checkout-fields p select::placeholder,
					#sellkit-checkout-widget-id #sellkit-checkout-billing-field-wrapper .mini-title,
					#sellkit-checkout-widget-id #sellkit-checkout-widget-shipping-fields .mini-title,
					#sellkit-checkout-widget-id .sellkit-custom-coupon-form .jx-coupon::placeholder',
			]
		);

		$this->add_control(
			'field_section_color_',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-widget-checkout-fields p input::placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .sellkit-widget-checkout-fields p select::placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .sellkit-widget-checkout-fields p textarea::placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id #sellkit-checkout-billing-field-wrapper .mini-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id #sellkit-checkout-widget-shipping-fields .mini-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-custom-coupon-form .jx-coupon::placeholder' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'field_style_heading_2',
			[
				'label'     => esc_html__( 'Input', 'sellkit' ),
				'type'      => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'field_input_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields input,
					#sellkit-checkout-widget-id .sellkit-widget-checkout-fields textarea,
					#sellkit-checkout-widget-id .sellkit-widget-checkout-fields select,
					#sellkit-checkout-widget-id .sellkit-widget-checkout-fields option,
					#sellkit-checkout-widget-id .sellkit-custom-coupon-form .jx-coupon',
			]
		);

		$this->add_control(
			'field_input_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields input' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields textarea' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields select' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields option' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-custom-coupon-form .jx-coupon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'field_style_heading_3',
			[
				'label'     => esc_html__( 'Border', 'sellkit' ),
				'type'      => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name'     => 'field_border',
				'label'    => esc_html__( 'Border', 'sellkit' ),
				'selector' => ' {{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields input,
					{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields textarea,
					{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields select,
					{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-custom-coupon-form .jx-coupon',
			]
		);

		$this->add_responsive_control(
			'fields_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-custom-coupon-form .jx-coupon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'field_border_focus_color',
			[
				'label'     => esc_html__( 'Focus Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields textarea:focus' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields input:focus'    => 'border-color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-widget-checkout-fields select:focus'   => 'border-color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-custom-coupon-form .jx-coupon:focus'   => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'field_error_validate_color',
			[
				'label'     => esc_html__( 'Error Validation Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-fields-wrapper .required-alarm' => 'color: {{VALUE}}',
					'{{WRAPPER}} .login-section-error' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-fields-wrapper .sellkit-checkout-field-global-errors' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'fields_background_color_',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-fields-wrapper input' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-fields-wrapper select' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-checkout-fields-wrapper textarea' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-custom-coupon-form .jx-coupon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function order_summary_style() {
		$this->start_controls_section(
			'section_order_summary_style',
			[
				'label' => esc_html__( 'Order Summary', 'sellkit' ),
				'tab'   => 'style',
			]
		);

		$this->add_control(
			'order_summary_style_heading_1',
			[
				'label'     => esc_html__( 'Product', 'sellkit' ),
				'type'      => 'heading',
			]
		);

		$this->add_control(
			'order_summary_show_image',
			[
				'label'        => esc_html__( 'Enable Image', 'sellkit' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'sellkit' ),
				'label_off'    => esc_html__( 'No', 'sellkit' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'order_summary_product_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} .sellkit-one-page-checkout-product-name .name-price,
					.sellkit-one-page-checkout-product-price *',
			]
		);

		$this->add_control(
			'order_summary_product_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-one-page-checkout-product-name .name-price' => 'color: {{VALUE}}',
					'{{WRAPPER}} .sellkit-one-page-checkout-product-price *' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'order_summary_item_image_border_color',
			[
				'label'     => esc_html__( 'Image Border Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-one-page-checkout-product-name img' => 'border: 1px solid {{VALUE}}',
				],
			]
		);

		$this->divider( 'order_summary_divider_1' );

		$this->add_control(
			'order_summary_style_heading_2',
			[
				'label'     => esc_html__( 'Subtotal', 'sellkit' ),
				'type'      => 'heading',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'order_summary_subtotal_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} #sellkit-checkout-widget-id #order_review tfoot tr:not(:last-child) th,
					#sellkit-checkout-widget-id #order_review tfoot tr:not(:last-child) td,
					#sellkit-checkout-widget-id #order_review tfoot tr:not(:last-child) td *',
			]
		);

		$this->add_control(
			'order_summary_subtotal_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id #order_review tfoot tr:not(:last-child) th' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id #order_review tfoot tr:not(:last-child) td' => 'color: {{VALUE}}',
					'{{WRAPPER}} #sellkit-checkout-widget-id #order_review tfoot tr:not(:last-child) td *' => 'color: {{VALUE}}',
				],
			]
		);

		$this->divider( 'order_summary_divider_2' );

		$this->add_control(
			'order_summary_style_heading_3',
			[
				'label'     => esc_html__( 'Total', 'sellkit' ),
				'type'      => 'heading',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name'     => 'order_summary_total_typography',
				'label'    => esc_html__( 'Typography', 'sellkit' ),
				'selector' => '{{WRAPPER}} #sellkit-checkout-widget-id .order-total th, .order-total td *',
			]
		);

		$this->add_control(
			'order_summary_total_color',
			[
				'label'     => esc_html__( 'Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .order-total th' => 'color: {{VALUE}}',
					'{{WRAPPER}} .order-total td *' => 'color: {{VALUE}}',
				],
			]
		);

		$this->divider( 'order_summary_divider_3' );

		$this->add_control(
			'order_summary_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-multistep-checkout-sidebar' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'order_summary_inner_wrap_background_color',
			[
				'label'     => esc_html__( 'Inner Background Color', 'sellkit' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-multistep-checkout-sidebar #sellkit-checkout-widget-order-review-wrap' => 'background-color: {{VALUE}} !important',
				],
				'condition' => [
					'layout-type' => 'multi-step',
				],
			]
		);

		$this->divider( 'order_summary_divider_4' );

		$this->add_group_control(
			'border',
			[
				'name'     => 'order_summary_border',
				'label'    => esc_html__( 'Border Type', 'sellkit' ),
				'selector' => '{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-multistep-checkout-sidebar',
			]
		);

		$this->add_responsive_control(
			'order_summary_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-multistep-checkout-sidebar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->divider( 'order_summary_divider_5' );

		$this->add_group_control(
			'box-shadow',
			[
				'name'      => 'order_summary_box_shadow',
				'label'     => esc_html__( 'Box Shadow', 'sellkit' ),
				'selector'  => '{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-multistep-checkout-sidebar',
			]
		);

		$this->divider( 'order_summary_divider_6' );

		$this->add_responsive_control(
			'order_summary_padding',
			[
				'label'      => esc_html__( 'Padding', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-multistep-checkout-sidebar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_summary_margin',
			[
				'label'      => esc_html__( 'Margin', 'sellkit' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #sellkit-checkout-widget-id .sellkit-multistep-checkout-sidebar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function divider( $name ) {
		$this->add_control(
			$name,
			[
				'type' => 'divider',
			]
		);
	}
}
