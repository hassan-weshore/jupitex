<?php

use Elementor\Core\Base\Document;
use Elementor\Plugin;

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Product_Description_Widget extends Sellkit_Elementor_Upsell_Base_Widget {

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_name() {
		return 'sellkit-product-description';
	}

	public function get_title() {
		return __( 'Product Description', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-product-description-icon';
	}

	protected function register_controls() {
		$this->register_content_section_controls();
		$this->register_style_section_controls();
	}

	private function register_content_section_controls() {
		$this->start_controls_section(
			'content_tab',
			[
				'label' => __( 'Content', 'sellkit' ),
				'tab' => 'content',
			]
		);

		$this->add_control(
			'content_type',
			[
				'label' => __( 'Content Type', 'sellkit' ),
				'type' => 'select',
				'default' => 'full_desc',
				'options' => [
					'full_desc' => __( 'Full Description', 'sellkit' ),
					'short_desc' => __( 'Short Description', 'sellkit' ),
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_section_controls() {
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
					'justify' => [
						'title' => __( 'Justified', 'sellkit' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .sellkit-product-description-widget' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'input_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-product-description-widget',
			]
		);

		$this->add_control(
			'input_color',
			[
				'label' => __( 'Text Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-product-description-widget' => 'color: {{SIZE}};',
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
				'selector' => '{{WRAPPER}} .sellkit-product-description-widget',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$product  = $this->get_product_object();
		$settings = $this->get_settings_for_display();

		if ( empty( $product ) ) {
			return;
		}
		?>
		<p class="sellkit-product-description-widget">
			<?php
				echo 'short_desc' === $settings['content_type'] ? $product->get_short_description() : $product->get_description();
			?>
		</p>
		<?php
	}
}
