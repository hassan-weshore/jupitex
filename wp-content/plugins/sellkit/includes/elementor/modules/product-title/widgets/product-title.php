<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Product_Title_Widget extends Sellkit_Elementor_Upsell_Base_Widget {

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_name() {
		return 'sellkit-product-title';
	}

	public function get_title() {
		return __( 'Product Title', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-product-title-icon';
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
			'title_color',
			[
				'label' => __( 'Title Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .product_title-widget .entry-title' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'input_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .product_title-widget .entry-title',
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
				'selector' => '{{WRAPPER}} .product_title-widget .entry-title',
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
			'html_tag',
			[
				'label' => __( 'HTML Tag', 'sellkit' ),
				'type' => 'select',
				'default' => 'h1',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'p' => 'p',
					'span' => 'span',
				],
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
					'{{WRAPPER}} .product_title-widget' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$html_tag = $settings['html_tag'];
		$product  = $this->get_product_object();

		if ( empty( $product ) ) {
			return;
		}

		$product_id = $product->get_id();
		?>
		<div class="product_title-widget">
			<<?php echo $html_tag; ?> class="entry-title"><?php echo get_the_title( $product_id ); ?></<?php echo $html_tag; ?>>
		</div>
		<?php
	}
}
