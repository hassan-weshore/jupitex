<?php

use Elementor\Widget_Button;
use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Accept_Reject_Button_Widget extends Sellkit_Elementor_Upsell_Base_Widget {

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_name() {
		return 'sellkit-accept-reject-button';
	}

	public function get_title() {
		return __( 'Accept/Reject Button', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-accept-reject-button-icon';
	}

	protected function register_controls() {
		$this->register_content_box_controls();
		$this->register_style_box_controls();
	}

	/**
	 * Content section controls.
	 *
	 * @since 1.1.0
	 */
	private function register_content_box_controls() {
		$this->start_controls_section(
			'content',
			[
				'label' => __( 'Content', 'sellkit' ),
				'tab' => 'content',
			]
		);

		$this->add_control(
			'offer_type',
			[
				'label' => __( 'Offer Type', 'sellkit' ),
				'type' => 'select',
				'default' => 'accept',
				'options' => [
					'accept' => __( 'Accept Offer', 'sellkit' ),
					'reject' => __( 'Reject Offer', 'sellkit' ),
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'sellkit' ),
				'default' => __( 'Offer Action', 'sellkit' ),
				'type' => 'text',
			]
		);

		$this->add_control(
			'sub_title',
			[
				'label' => __( 'Sub Title', 'sellkit' ),
				'type' => 'text',
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label' => __( 'Alignment', 'sellkit' ),
				'type' => 'choose',
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'sellkit' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'sellkit' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'sellkit' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button-widget' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_size',
			[
				'label' => __( 'Size', 'sellkit' ),
				'type' => 'select',
				'default' => 'md',
				'options' => Widget_Button::get_button_sizes(),
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon', 'elementor' ),
				'type' => 'icons',
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'icon_spacing',
			[
				'label' => __( 'Icon Spacing', 'sellkit' ),
				'type' => 'slider',
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button i' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'selected_icon!' => [
						'value' => '',
						'library' => '',
					],
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'sellkit' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'selected_icon!' => [
						'value' => '',
						'library' => '',
					],
				],
			]
		);

		$this->add_control(
			'title_spacing',
			[
				'label' => __( 'Title Spacing', 'sellkit' ),
				'type' => 'slider',
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'sub_title!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style section controls.
	 *
	 * @since 1.1.0
	 */
	private function register_style_box_controls() {
		$this->start_controls_section(
			'style',
			[
				'label' => __( 'Button', 'sellkit' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'input_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button-title',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'sub_title_typography',
				'label' => __( 'Subtitle Typography', 'sellkit' ),
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button .sellkit-accept-reject-button-sub-title',
				'condition' => [
					'sub_title!' => '',
				],
			]
		);

		$this->add_group_control(
			'text-shadow',
			[
				'name' => 'play_icon_shadow',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => __( 'Text Shadow', 'sellkit' ),
					],
				],
				'selector' => '{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button',
			]
		);

		$this->start_controls_tabs( 'tabs_content' );

		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => __( 'Normal', 'sellkit' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'background',
				'label' => __( 'Background Type', 'sellkit' ),
				'type' => 'background',
				'default' => '#635cff',
				'selector' => '{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => __( 'Hover', 'sellkit' ),
			]
		);

		$this->add_control(
			'hover_text_color',
			[
				'label' => __( 'Text Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'hover_background',
				'label' => __( 'Background Type', 'sellkit' ),
				'type' => 'background',
				'selector' => '{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'border_separator',
			[
				'type' => 'divider',
			]
		);

		$this->add_control(
			'border_type',
			[
				'label' => __( 'Border Type', 'sellkit' ),
				'type' => 'select',
				'options' => [
					'' => __( 'None', 'sellkit' ),
					'solid' => __( 'Solid', 'sellkit' ),
					'double' => __( 'Double', 'sellkit' ),
					'dotted' => __( 'Dotted', 'sellkit' ),
					'dashed' => __( 'Dashed', 'sellkit' ),
					'groove' => __( 'Groove', 'sellkit' ),
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button' => 'border-style: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'border_type!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label' => __( 'Border Width', 'sellkit' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button' => 'border-width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'border_type!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button',
			]
		);

		$this->add_control(
			'padding_separator',
			[
				'type' => 'divider',
			]
		);

		$this->add_responsive_control(
			'personalised_coupons_padding',
			[
				'label' => __( 'Padding', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-accept-reject-button-widget .sellkit-accept-reject-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$order_key         = sellkit_htmlspecialchars( INPUT_GET, 'order-key' );
		$settings          = $this->get_settings_for_display();
		$button_type_class = ( 'accept' === $settings['offer_type'] ) ? 'sellkit-upsell-accept-button' : 'sellkit-upsell-reject-button';
		?>
		<div class="sellkit-accept-reject-button-widget">
			<a
				class="elementor-button sellkit-accept-reject-button <?php echo $button_type_class; ?> elementor-size-<?php echo $settings['button_size']; ?>"
				data-order-key="<?php echo esc_attr( $order_key ); ?>"
			>
				<?php Icons_Manager::render_icon( $settings['selected_icon'] ); ?>
				<span class="sellkit-accept-reject-button-title" ><?php echo $settings['title']; ?></span>
				<br>
				<span class="sellkit-accept-reject-button-sub-title"><?php echo $settings['sub_title']; ?></span>
			</a>
			<div class="sellkit-upsell-popup">
				<div class="sellkit-upsell-popup-body">
					<div class="sellkit-upsell-popup-header">
						<img src="<?php echo sellkit()->plugin_url() . 'assets/img/icons/close-cross.svg'; ?>" >
					</div>
					<div class="sellkit-upsell-popup-content">
						<div class="sellkit-upsell-popup-icon">
							<img class="rotate sellkit-upsell-updating active" src="<?php echo sellkit()->plugin_url() . 'assets/img/icons/sync-alt.svg'; ?>" >
							<img class="sellkit-upsell-accepted" src="<?php echo sellkit()->plugin_url() . 'assets/img/icons/check-circle.svg'; ?>" >
							<img class="sellkit-upsell-rejected" src="<?php echo sellkit()->plugin_url() . 'assets/img/icons/times-circle.svg'; ?>" >
						</div>
						<div class="sellkit-upsell-popup-text">
							<div class="sellkit-upsell-updating active">
								<?php esc_html_e( 'Updating your order…', 'sellkit' ); ?>
							</div>
							<div class="sellkit-upsell-accepted">
								<?php esc_html_e( 'Congratulations! Your item has been successfully added to the order.', 'sellkit' ); ?>
							</div>
							<div class="sellkit-upsell-rejected">
								<?php esc_html_e( 'Sorry! We were unable to add this item to your order.', 'sellkit' ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
