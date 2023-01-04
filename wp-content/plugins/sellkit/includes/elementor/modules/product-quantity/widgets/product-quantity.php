<?php

use Elementor\Core\Base\Document;
use Elementor\Plugin;

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Product_Quantity_Widget extends Sellkit_Elementor_Upsell_Base_Widget {

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_name() {
		return 'sellkit-product-quantity';
	}

	public function get_title() {
		return __( 'Product Quantity', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-quantity-selector-icon';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'style_tab',
			[
				'label' => __( 'Style', 'sellkit' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'sellkit' ),
				'type' => 'choose',
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', 'sellkit' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'sellkit' ),
						'icon' => 'fa fa-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'sellkit' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'start',
				'selectors' => [
					'{{WRAPPER}} .sellkit-product-quantity-widget' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_selector_width',
			[
				'label' => __( 'Width', 'sellkit' ),
				'type' => 'slider',
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-product-quantity-widget .quantity' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Label Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-product-quantity-widget label' => 'color: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label' => __( 'Input Text Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-product-quantity-widget input' => 'color: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'input_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-product-quantity-widget .quantity input, {{WRAPPER}} .sellkit-product-quantity-widget .quantity .screen-reader-text',
			]
		);

		$this->add_group_control(
			'text-shadow',
			[
				'name' => 'text_shadow',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => __( 'Text Shadow', 'sellkit' ),
					],
				],
				'selector' => '{{WRAPPER}} .sellkit-product-quantity-widget .quantity input, {{WRAPPER}} .sellkit-product-quantity-widget .quantity label',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		global $product;

		$product = $this->get_product_object();

		if ( empty( $product ) ) {
			return;
		}

		?>
		<div class="sellkit-product-quantity-widget">
			<?php
				// phpcs:disable
				woocommerce_quantity_input(
					[
						'product_name' => '',
						'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
						'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
						'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
					]
				);
				// phpcs:enable
			?>
		</div>
		<?php
	}
}
