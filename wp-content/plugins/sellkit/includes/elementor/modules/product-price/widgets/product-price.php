<?php

use Elementor\Core\Base\Document;
use Elementor\Plugin;

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Product_Price_Widget extends Sellkit_Elementor_Upsell_Base_Widget {

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_name() {
		return 'sellkit-product-price';
	}

	public function get_title() {
		return __( 'Product Price', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-product-price-icon';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'price_style',
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
					'{{WRAPPER}} .sellkit-product-price-widget' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'input_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .sellkit-product-price-widget',
			]
		);

		$this->add_control(
			'input_color',
			[
				'label' => __( 'Text Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-product-price-widget' => 'color: {{SIZE}};',
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
				'selector' => '{{WRAPPER}} .sellkit-product-price-widget',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Calculate the discount price.
	 *
	 * @param $regular_price
	 * @param $product_detail
	 * @return false|int
	 * @since 1.1.0
	 */
	private function get_discount_price( $regular_price, $product_detail ) {
		$discount_type  = $product_detail['discountType'];
		$discount_value = ! empty( $product_detail['discount'] ) ? $product_detail['discount'] : 0;
		$discount_price = false;

		if ( 'fixed' === $discount_type ) {
			$discount_price = $regular_price - $discount_value;
		}

		if ( 'percentage' === $discount_type ) {
			$discount_price = $regular_price - ( ( $discount_value * $regular_price ) / 100 );
		}

		return $discount_price;
	}

	protected function render() {
		if ( empty( $this->get_product_object() ) ) {
			return;
		}

		$products       = $this->step_data['data']['products']['list'];
		$product_detail = reset( $products );
		$product_id     = key( $products );
		$product        = wc_get_product( $product_id );
		$regular_price  = $product->get_regular_price();
		$discount_price = $this->get_discount_price( $regular_price, $product_detail );
		?>
			<p class="sellkit-product-price-widget">
				<del>
					<?php
					if ( $regular_price > $discount_price ) {
						echo wc_price( $regular_price );
					}
					?>
				</del>
				<ins><?php echo wc_price( $discount_price ); ?></ins>
			</p>
		<?php
	}
}
