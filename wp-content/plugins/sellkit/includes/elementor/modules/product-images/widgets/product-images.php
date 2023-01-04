<?php

use Elementor\Settings;

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Product_Images_Widget extends Sellkit_Elementor_Upsell_Base_Widget {

	public function get_script_depends() {
		return [ 'zoom', 'flexslider', 'photoswipe', 'photoswipe-ui-default', 'wc-single-product' ];
	}

	public function get_style_depends() {
		return [ 'photoswipe', 'photoswipe-default-skin' ];
	}

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_name() {
		return 'sellkit-product-images';
	}

	public function get_title() {
		return __( 'Product Images', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-product-images-icon';
	}

	protected function register_controls() {
		$this->register_content_box_controls();

		$this->start_controls_section(
			'style',
			[
				'label' => __( 'Style', 'sellkit' ),
				'tab' => 'style',
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
					'{{WRAPPER}} .product.product-images-is-slider .flex-viewport' => 'border-style: {{VALUE}} !important;',
					'{{WRAPPER}} .product:not(.product-images-is-slider) .woocommerce-product-gallery__wrapper' => 'border-style: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .product.product-images-is-slider .flex-viewport' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} .product:not(.product-images-is-slider) .woocommerce-product-gallery__wrapper' => 'border-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} .product.product-images-is-slider .flex-viewport' => 'border-width: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .product:not(.product-images-is-slider) .woocommerce-product-gallery__wrapper' => 'border-width: {{SIZE}}{{UNIT}} !important;',
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
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .product.product-images-is-slider .flex-viewport' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .product:not(.product-images-is-slider) .woocommerce-product-gallery__wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'bottom_spacing',
			[
				'label' => __( 'Spacing', 'sellkit' ),
				'type' => 'slider',
				'default' => [
					'size' => 10,
				],
				'description' => __( 'Between main image and gallery slider(If slider available)', 'sellkit' ),
				'selectors' => [
					'{{WRAPPER}} .flex-viewport' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_switch' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'gallery_width',
			[
				'label' => __( 'Width', 'sellkit' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px' ],
				'render_type' => 'template',
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 5,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'gallery_max_width',
			[
				'label' => __( 'Max Width (%)', 'sellkit' ),
				'type' => 'slider',
				'size_units' => [ '%' ],
				'render_type' => 'template',
				'range' => [
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
					'{{WRAPPER}} .woocommerce-product-gallery' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumbanil_heading',
			[
				'label' => __( 'Thumbnail', 'sellkit' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'slider_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumbnail_border_type',
			[
				'label' => __( 'Border Type', 'sellkit' ),
				'type' => 'select',
				'render_type' => 'template',
				'options' => [
					'' => __( 'None', 'sellkit' ),
					'solid' => __( 'Solid', 'sellkit' ),
					'double' => __( 'Double', 'sellkit' ),
					'dotted' => __( 'Dotted', 'sellkit' ),
					'dashed' => __( 'Dashed', 'sellkit' ),
					'groove' => __( 'Groove', 'sellkit' ),
				],
				'selectors' => [
					'{{WRAPPER}} .flex-control-nav li' => 'border-style: {{VALUE}} !important;',
				],
				'condition' => [
					'slider_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumbnail_border_color',
			[
				'label' => __( 'Border Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .flex-control-nav li' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'thumbnail_border_type!' => '',
					'slider_switch' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_border_width',
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
					'{{WRAPPER}} .flex-control-nav li' => 'border-width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'thumbnail_border_type!' => '',
					'slider_switch' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_border_radius',
			[
				'label' => __( 'Border Radius', 'sellkit' ),
				'type' => 'dimensions',
				'frontend_available' => true,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .flex-control-nav li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; ',
				],
				'condition' => [
					'slider_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumbnail_spacing',
			[
				'label' => __( 'Spacing', 'sellkit' ),
				'type' => 'slider',
				'default' => [
					'size' => 10,
				],
				'description' => __( 'Between main image and gallery slider(If slider available)', 'sellkit' ),
				'selectors' => [
					'{{WRAPPER}} .flex-control-nav li' => 'margin-right: {{SIZE}}{{UNIT}} !important; margin-bottom: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'slider_switch' => 'yes',
				],
			]
		);

		$this->end_controls_section();
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
			'slider_switch',
			[
				'label' => __( 'Enable Slider', 'sellkit' ),
				'type' => 'switcher',
				'default' => 'yes',
				'description' => __( 'Note: Slider is only show if gallery images are available.', 'sellkit' ),
				'yes' => __( 'Yes', 'sellkit' ),
				'no' => __( 'No', 'sellkit' ),
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label' => __( 'Alignment', 'sellkit' ),
				'type' => 'choose',
				'default' => 'start',
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
				'selectors' => [
					'{{WRAPPER}} .product' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		global $product;

		$settings     = $this->get_settings_for_display();
		$product      = $this->get_product_object();
		$slider_class = 'yes' === $settings['slider_switch'] ? 'product-images-is-slider' : '';

		if ( empty( $product ) ) {
			return;
		}

		echo "<div class='sellkit-product-image-widget product $slider_class' >";

		if ( 'yes' !== $settings['slider_switch'] ) {
			$product->set_gallery_image_ids( [] );
		}

		wc_get_template( 'single-product/product-image.php' );

		echo '</div>';
	}
}
