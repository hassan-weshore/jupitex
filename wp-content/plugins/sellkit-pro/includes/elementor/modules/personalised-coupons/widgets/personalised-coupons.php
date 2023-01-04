<?php

defined( 'ABSPATH' ) || die();

use Elementor\Group_Control_Image_Size;
use Elementor\Plugin;
use Sellkit_Pro\Coupons\Coupons;

class Sellkit_Elementor_Personalised_Coupons_Widget extends Sellkit_Elementor_Base_Widget {

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_name() {
		return 'sellkit-personalised-coupons';
	}

	public function get_title() {
		return esc_html__( 'Smart Coupon', 'sellkit-pro' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-personalized-coupon-icon';
	}

	protected function register_controls() {
		$this->register_content_section_controls();
		$this->register_box_style_controls();
		$this->register_heading_style_controls();
		$this->register_description_style_controls();
		$this->register_expiration_date_style_controls();
		$this->register_coupon_box_style_controls();
		$this->register_coupon_button_style_controls();
		$this->register_coupon_hero_image_style_controls();
	}

	private function register_content_section_controls() {
		$this->start_controls_section(
			'content',
			[
				'label' => esc_html__( 'Content', 'sellkit-pro' ),
			]
		);

		$this->add_control(
			'heading',
			[
				'label' => esc_html__( 'Heading', 'sellkit-pro' ),
				'type' => 'text',
				'placeholder' => esc_html__( 'Enter your text...', 'sellkit-pro' ),
				'default' => esc_html__( 'You unlocked a new coupon!', 'sellkit-pro' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label' => esc_html__( 'Description', 'sellkit-pro' ),
				'type' => 'textarea',
				'placeholder' => esc_html__( 'Enter your description...', 'sellkit-pro' ),
				'separator' => 'none',
				'show_label' => false,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'expiration_date',
			[
				'label' => esc_html__( 'Show Expiration Date', 'sellkit-pro' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'sellkit-pro' ),
				'label_off' => esc_html__( 'Hide', 'sellkit-pro' ),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'expiration_date_text',
			[
				'label' => esc_html__( 'Prefix text', 'sellkit-pro' ),
				'type' => 'text',
				'default' => esc_html__( 'Expires on : ', 'sellkit-pro' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'expiration_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'call_to_action_button',
			[
				'type' => 'heading',
				'label' => esc_html__( 'Call To Action Button', 'sellkit-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_action_button',
			[
				'label' => esc_html__( 'Show Button', 'sellkit-pro' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'sellkit-pro' ),
				'label_off' => esc_html__( 'Hide', 'sellkit-pro' ),
			]
		);

		$this->add_control(
			'action_button_text',
			[
				'label' => esc_html__( 'Button Text', 'sellkit-pro' ),
				'type' => 'text',
				'default' => esc_html__( 'SHOP NOW', 'sellkit-pro' ),
				'condition' => [
					'show_action_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'action_button_url',
			[
				'label' => esc_html__( 'Button URL', 'sellkit-pro' ),
				'type' => 'text',
				'default' => '',
				'condition' => [
					'show_action_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'hero_image_heading',
			[
				'type' => 'heading',
				'label' => esc_html__( 'Hero image', 'sellkit-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hero_image',
			[
				'label' => esc_html__( 'Choose Image', 'sellkit-pro' ),
				'type' => 'media',
			]
		);

		$this->add_group_control(
			'image-size',
			[
				'name' => 'image',
				'default' => 'large',
			]
		);

		$this->end_controls_section();
	}

	private function register_box_style_controls() {
		$this->start_controls_section(
			'box_section',
			[
				'label' => esc_html__( 'Box', 'sellkit-pro' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'box_alignment',
			[
				'label'  => esc_html__( 'Alignment', 'sellkit-pro' ),
				'type' => 'choose',
				'default' => 'center',
				'options' => [
					'baseline' => [
						'title' => esc_html__( 'Left', 'sellkit-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sellkit-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'sellkit-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-wrap' => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .sellkit-personalised-coupons-content-wrap' => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .sellkit-personalised-coupons-image' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'personalised_coupons_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-wrap',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'personalised_coupons_border',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-wrap',
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'personalised_coupons_box_shadow',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-wrap',
			]
		);

		$this->add_responsive_control(
			'personalised_coupons_padding',
			[
				'label' => esc_html__( 'Padding', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'personalised_coupons_margin',
			[
				'label' => esc_html__( 'Margin', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_heading_style_controls() {
		$this->start_controls_section(
			'heading_section',
			[
				'label' => esc_html__( 'Heading', 'sellkit-pro' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'heading_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-title',
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => esc_html__( 'Text Color', 'sellkit-pro' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_align',
			[
				'label' => esc_html__( 'Alignment', 'sellkit-pro' ),
				'type' => 'choose',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'sellkit-pro' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sellkit-pro' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'sellkit-pro' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-title' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'heading_padding',
			[
				'label' => esc_html__( 'Padding', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_margin',
			[
				'label' => esc_html__( 'Margin', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_description_style_controls() {
		$this->start_controls_section(
			'description_section',
			[
				'label' => esc_html__( 'Description', 'sellkit-pro' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'description_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-content',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Text Color', 'sellkit-pro' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_align',
			[
				'label' => esc_html__( 'Alignment', 'sellkit-pro' ),
				'type' => 'choose',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'sellkit-pro' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sellkit-pro' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'sellkit-pro' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-content' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'description_padding',
			[
				'label' => esc_html__( 'Padding', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_margin',
			[
				'label' => esc_html__( 'Margin', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_expiration_date_style_controls() {
		$this->start_controls_section(
			'expiration_date_section',
			[
				'label' => esc_html__( 'Expiration Date', 'sellkit-pro' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'expiration_date_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-expiration-date',
			]
		);

		$this->add_control(
			'expiration_date_color',
			[
				'label' => esc_html__( 'Text Color', 'sellkit-pro' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-expiration-date' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'expiration_date_align',
			[
				'label' => esc_html__( 'Alignment', 'sellkit-pro' ),
				'type' => 'choose',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'sellkit-pro' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sellkit-pro' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'sellkit-pro' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-expiration-date' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'expiration_date_padding',
			[
				'label' => esc_html__( 'Padding', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-expiration-date' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'expiration_date_margin',
			[
				'label' => esc_html__( 'Margin', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-expiration-date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_coupon_box_style_controls() {
		$this->start_controls_section(
			'coupon_box_section',
			[
				'label' => esc_html__( 'Coupon Box', 'sellkit-pro' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'coupon_box_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-code-box',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'coupon_box_border',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-code-box',
			]
		);

		$this->add_responsive_control(
			'coupon_box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-code-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'coupon_box_box_shadow',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-code-box',
			]
		);

		$this->add_responsive_control(
			'coupon_box_color',
			[
				'label' => esc_html__( 'Text Color', 'sellkit-pro' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-code-box' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'box_heading_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-code-box',
			]
		);

		$this->add_responsive_control(
			'coupon_box_padding',
			[
				'label' => esc_html__( 'Padding', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-code-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_box_margin',
			[
				'label' => esc_html__( 'Margin', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-code-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_coupon_button_style_controls() {
		$this->start_controls_section(
			'button_section',
			[
				'label' => esc_html__( 'Button', 'sellkit-pro' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label' => esc_html__( 'Width', 'sellkit-pro' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 30,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_height',
			[
				'label' => esc_html__( 'Height', 'sellkit-pro' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'range' => [
					'%' => [
						'min' => 30,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label' => esc_html__( 'Spacing', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label' => esc_html__( 'Alignment', 'sellkit-pro' ),
				'type' => 'choose',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'sellkit-pro' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sellkit-pro' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'sellkit-pro' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-button-wrap' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab(
			'icon_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'sellkit-pro' ),
			]
		);

		$this->button_section_tab_controls_normal();

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'sellkit-pro' ),
			]
		);

		$this->button_section_tab_controls_hover();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function button_section_tab_controls_normal() {
		$this->add_control(
			'button_text_color_normal',
			[
				'label' => esc_html__( 'Text Color', 'sellkit-pro' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'button_typography_normal',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-button',
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'button_background__normal',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-button',
			]
		);

		$this->add_control(
			'button_border_heading_normal',
			[
				'type' => 'heading',
				'label' => 'Border',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'button_border_normal',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-button',
			]
		);

		$this->add_responsive_control(
			'button_border_radius_normal',
			[
				'label' => esc_html__( 'Border Radius', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'button_box_shadow_normal',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-button',
			]
		);
	}

	private function button_section_tab_controls_hover() {
		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'sellkit-pro' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'button_typography_hover',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-button:hover',
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'button_background_hover',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-button:hover',
			]
		);

		$this->add_control(
			'button_border_heading_hover',
			[
				'type' => 'heading',
				'label' => 'Border',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'button_border_hover',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-button:hover',
			]
		);

		$this->add_responsive_control(
			'button_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .sellkit-personalised-coupons-button:hover',
			]
		);
	}

	private function register_coupon_hero_image_style_controls() {
		$this->start_controls_section(
			'hero_image_section',
			[
				'label' => esc_html__( 'Hero Image', 'sellkit-pro' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'hero_image_location',
			[
				'label'  => esc_html__( 'Hero Image Location', 'sellkit-pro' ),
				'type' => 'select',
				'default' => 'top',
				'options' => [
					'top'  => esc_html__( 'Top of the content', 'sellkit-pro' ),
					'left'  => esc_html__( 'Left side', 'sellkit-pro' ),
					'right'  => esc_html__( 'Right side', 'sellkit-pro' ),
					'bottom'  => esc_html__( 'Bottom of the content', 'sellkit-pro' ),
				],
			]
		);

		$this->add_responsive_control(
			'hero_image_column_size',
			[
				'label' => esc_html__( 'Image Coulmn Size', 'sellkit-pro' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'hero_image_width',
			[
				'label' => esc_html__( 'Width', 'sellkit-pro' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px', 'vw' ],
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 30,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'hero_image_max_width',
			[
				'label' => esc_html__( 'Max Width', 'sellkit-pro' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px', 'vw' ],
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 30,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-image img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'hero_image_height',
			[
				'label' => esc_html__( 'Height', 'sellkit-pro' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'vh' ],
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'range' => [
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-image img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'hero_image_margin',
			[
				'label' => esc_html__( 'Margin', 'sellkit-pro' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-personalised-coupons-image img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$this->get_coupon_template( [
			'code' => Coupons::generate_coupon_code(),
			'expiration_date' => date_i18n( 'Y/m/d h:i A' ),
		] );
	}

	/**
	 * Gets coupon template.
	 *
	 * @since 1.1.0
	 * @param array $coupon Coupon data.
	 */
	private function get_coupon_template( $coupon ) {
		$settings            = $this->get_settings_for_display();
		$image               = '';
		$hero_image_location = '';
		$is_edit_mode        = Plugin::instance()->editor->is_edit_mode();

		if ( ! empty( $settings['action_button_url'] ) ) {
			$this->add_render_attribute( 'link', 'href', esc_url( $settings['action_button_url'] ) );
		}

		if ( $settings['hero_image']['url'] ) {
			$image .= '<div class="sellkit-personalised-coupons-image">';
			$image .= Group_Control_Image_Size::get_attachment_image_html( $settings, 'image', 'hero_image' );
			$image .= '</div>';

			$hero_image_location = 'sellkit-personalised-coupons-' . $settings['hero_image_location'] . '';
		}

		$wrapper_class = "sellkit-personalised-coupons-wrap {$hero_image_location} ";

		if ( $is_edit_mode ) {
			$wrapper_class .= 'is-edit-mode';
		}

		$this->add_render_attribute( 'wrapper', [
			'class' => $wrapper_class,
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php echo $image; ?>

			<div class="sellkit-personalised-coupons-content-wrap">
				<?php if ( $settings['heading'] ) : ?>
				<div class="sellkit-personalised-coupons-title"><?php echo esc_html( $settings['heading'] ); ?></div>
				<?php endif; ?>

				<?php if ( $settings['description'] ) : ?>
				<div class="sellkit-personalised-coupons-content"><?php echo esc_html( $settings['description'] ); ?></div>
				<?php endif; ?>

				<?php if ( $settings['expiration_date'] ) : ?>
				<div class="sellkit-personalised-coupons-expiration-date">
					<?php echo esc_html( $settings['expiration_date_text'] ); ?>
					<div class="sellkit-personalised-coupons-expiration-date-value">
						<?php echo $coupon['expiration_date']; ?>
					</div>
				</div>
				<?php endif; ?>

				<div class="sellkit-personalised-coupons-code">
					<div class="sellkit-personalised-coupons-code-box">
						<?php echo esc_html( $coupon['code'] ); ?>
					</div>
				</div>
				<br>
				<?php if ( $settings['show_action_button'] ) : ?>
					<div class="sellkit-personalised-coupons-button-wrap">
						<a class="sellkit-personalised-coupons-button"
								<?php echo $this->get_render_attribute_string( 'link' ); ?>>
							<?php echo esc_html( $settings['action_button_text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
