<?php

use Sellkit\Elementor\Modules\Order_Cart_Details\Classes\Order_Cart_Detail_Data;

defined( 'ABSPATH' ) || die();

/*
 * @SuppressWarnings(PHPMD)
 */
class Sellkit_Elementor_Order_Cart_Details_Widget extends Sellkit_Elementor_Base_Widget {

	private $order_data;

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->order_data = Order_Cart_Detail_Data::get_instance();
	}

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_name() {
		return 'sellkit-order-cart-details';
	}

	public function get_title() {
		return __( 'Order Cart Details', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-order-cart-details-icon';
	}

	protected function register_controls() {
		$this->register_settings_controls();
		$this->register_box_style_controls();
		$this->register_items_style_controls();
	}

	private function register_box_style_controls() {
		$this->start_controls_section(
			'section_box',
			[
				'label' => __( 'Box', 'sellkit' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'box_background_color',
			[
				'label' => __( 'Background Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-cart-detail' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'box_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => __( 'Border Width', 'sellkit' ),
					],
				],
				'selector' => '{{WRAPPER}} .sellkit-order-cart-detail',
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label' => __( 'Color', 'sellkit' ),
				'type' => 'color',
				'condition' => [
					'box_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-cart-detail' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label' => __( 'Border Radius', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-cart-detail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'box_box_shadow',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .sellkit-order-cart-detail',
			]
		);

		$this->add_responsive_control(
			'box_spacing',
			[
				'label' => esc_html__( 'Margin', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-cart-detail' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __( 'Padding', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-cart-detail' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_items_style_controls() {
		$this->start_controls_section(
			'section_items',
			[
				'label' => __( 'Cart Items', 'sellkit' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'thumbnail_heading_title',
			[
				'label' => __( 'Thumbnail', 'sellkit' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'Thumbnail_width',
			[
				'label' => __( 'Width', 'sellkit' ),
				'type' => 'slider',
				'default' => [
					'size' => 80,
				],
				'size_units' => [ '%', 'px' ],
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
					'{{WRAPPER}} .sellkit-order-cart-detail .sellkit-order-cart-detail-items-thumbnail' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumbnail_height',
			[
				'label' => __( 'Height', 'sellkit' ),
				'type' => 'slider',
				'default' => [
					'size' => 80,
				],
				'size_units' => [ '%', 'px' ],
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
					'{{WRAPPER}} .sellkit-order-cart-detail .sellkit-order-cart-detail-items-thumbnail' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumbnail_border_radius',
			[
				'label' => __( 'Border Radius', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-cart-detail .sellkit-order-cart-detail-items-thumbnail-image-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'thumbnail_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => __( 'Border Width', 'sellkit' ),
					],
				],
				'selector' => '{{WRAPPER}} .sellkit-order-cart-detail .sellkit-order-cart-detail-items-thumbnail',
			]
		);

		$this->add_control(
			'thumbnail_border_color',
			[
				'label' => __( 'Color', 'sellkit' ),
				'type' => 'color',
				'condition' => [
					'thumbnail_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-cart-detail .sellkit-order-cart-detail-items-thumbnail' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_title_heading_title',
			[
				'label' => __( 'Product Title', 'sellkit' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'product_name_typography',
				'exclude' => [ 'line_height' ],
				'selector' => '{{WRAPPER}} .sellkit-order-cart-detail-items-product-title',
				'scheme' => '1',
			]
		);

		$this->add_control(
			'product_name_font_color',
			[
				'label' => __( 'Font Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-cart-detail-items-product-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_price_heading_title',
			[
				'label' => __( 'Product Price', 'sellkit' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'product_price_typography',
				'exclude' => [ 'line_height' ],
				'selector' => '{{WRAPPER}} .sellkit-order-cart-detail-items-price',
				'scheme' => '1',
			]
		);

		$this->add_control(
			'product_price_font_color',
			[
				'label' => __( 'Font Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-cart-detail-items-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_settings_controls() {
		$this->start_controls_section(
			'settings',
			[
				'label' => __( 'Settings', 'sellkit' ),
			]
		);

		$this->add_control(
			'heading',
			[
				'label' => __( 'Heading', 'sellkit' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => __( 'Show', 'sellkit' ),
				'label_off' => __( 'Hide', 'sellkit' ),
			]
		);

		$this->add_control(
			'label',
			[
				'label' => __( 'Label', 'sellkit' ),
				'type' => 'text',
				'placeholder' => __( 'Enter cart box title', 'sellkit' ),
				'default' => __( 'Cart', 'sellkit' ),
			]
		);

		$this->add_control(
			'cart_items',
			[
				'label' => __( 'Cart Items', 'sellkit' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => __( 'Show', 'sellkit' ),
				'label_off' => __( 'Hide', 'sellkit' ),
			]
		);

		$this->add_control(
			'product_thumbnail',
			[
				'label' => __( 'Product Thumbnail', 'sellkit' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => __( 'Show', 'sellkit' ),
				'label_off' => __( 'Hide', 'sellkit' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings          = $this->get_settings_for_display();
		$heading           = $settings['heading'];
		$label             = $settings['label'];
		$cart_items        = $settings['cart_items'];
		$product_thumbnail = $settings['product_thumbnail'];
		$order             = $this->order_data->order;
		$order_data        = $this->order_data->get_details_data();

		if ( empty( $order_data ) ) {
			return;
		}

		$order_items       = $order_data['items'];
		$order_items_total = $order_data['prices'];

		require_once sellkit()->plugin_dir() . '/includes/elementor/modules/order-cart-details/templates/order-details.php';
	}
}
