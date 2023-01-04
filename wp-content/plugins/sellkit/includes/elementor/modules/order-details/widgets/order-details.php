<?php

use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Plugin as Elementor;

defined( 'ABSPATH' ) || die();

/**
 * Temporary supressed.
 *
 * @SuppressWarnings(ExcessiveClassLength)
 * @SuppressWarnings(ExcessiveClassComplexity)
 */
class Sellkit_Elementor_Order_Details_Widget extends Sellkit_Elementor_Base_Widget {

	public function get_name() {
		return 'sellkit-order-details';
	}

	public function get_title() {
		return esc_html__( 'Order Details', 'sellkit' );
	}

	public function get_icon() {
		return 'sellkit-element-icon sellkit-order-details-icon';
	}

	protected function register_controls() {
		$this->register_section_details_order_items();
		$this->register_settings();
		$this->register_section_Box();
		$this->register_section_order_details_item();
	}

	/**
	 * Temporary supressed.
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function register_section_details_order_items() {
		$this->start_controls_section(
			'section_order_detail_items',
			[
				'label' => esc_html__( 'Order Details items', 'sellkit' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			[
				'label' => esc_html__( 'Type', 'sellkit' ),
				'type' => 'select',
				'options' => Sellkit_Elementor_Order_Details_Module::get_field_types(),
				'default' => 'order_number',
			]
		);

		$repeater->add_control(
			'label',
			[
				'label' => esc_html__( 'Title', 'sellkit' ),
				'placeholder' => esc_html__( 'Title', 'sellkit' ),
				'type' => 'text',
			]
		);

		$repeater->add_control(
			'detail_item_icon',
			[
				'label' => esc_html__( 'Icon', 'sellkit' ),
				'type' => 'icons',
				'fa4compatibility' => 'icon',
			]
		);

		$repeater->add_responsive_control(
			'detail_item_width',
			[
				'label' => esc_html__( 'Item Width', 'sellkit' ),
				'type' => 'select',
				'options' => [
					'' => esc_html__( 'Default', 'sellkit' ),
					'100' => '100%',
					'80' => '80%',
					'75' => '75%',
					'66' => '66%',
					'60' => '60%',
					'50' => '50%',
					'40' => '40%',
					'33' => '33%',
					'25' => '25%',
					'20' => '20%',
				],
				'default' => '100',
			]
		);

		$this->add_control(
			'fields',
			[
				'type' => 'repeater',
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'type' => 'order_number',
						'label' => esc_html__( 'Order Number', 'sellkit' ),
					],
				],
				'frontend_available' => true,
				'title_field' => '{{{ label }}}',
			]
		);

		$this->end_controls_section();
	}

	private function register_settings() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__( 'Settings', 'sellkit' ),
			]
		);

		$this->add_control(
			'list_name',
			[
				'label' => esc_html__( 'Heading', 'sellkit' ),
				'type' => 'text',
				'default' => 'Order Details',
				'placeholder' => esc_html__( 'Enter your list name', 'sellkit' ),
			]
		);

		$this->end_controls_section();
	}

	private function register_section_Box() {
		$this->start_controls_section(
			'section_style_Box',
			[
				'label' => esc_html__( 'Box', 'sellkit' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'order_details_box_background_color',
			[
				'label' => esc_html__( 'Background Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-details' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'order_details_box_border',
				'selector' => '{{WRAPPER}} .sellkit-order-details',
			]
		);

		$this->add_responsive_control(
			'order_details_box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-details' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'order_details_box_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .sellkit-order-details',
			]
		);

		$this->add_responsive_control(
			'order_details_box_padding',
			[
				'label' => esc_html__( 'Padding', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_details_box_margin',
			[
				'label' => esc_html__( 'Margin', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-details' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_order_details_item() {
		$this->start_controls_section(
			'section_style_order_details_item',
			[
				'label' => esc_html__( 'Order Details Item', 'sellkit' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'order_details_heading',
			[
				'label' => esc_html__( 'Heading', 'sellkit' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'order_details_heading_typography',
				'selector' => '{{WRAPPER}} .sellkit-order-details h3',
				'scheme' => '3',
			]
		);

		$this->add_control(
			'order_details_heading_color',
			[
				'label' => esc_html__( 'Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-details h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_details_heading_margin',
			[
				'label' => esc_html__( 'Margin', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-details h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'order_details_icon',
			[
				'label' => esc_html__( 'Icon', 'sellkit' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'order_details_icon_size',
			[
				'label' => esc_html__( 'Size', 'sellkit' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-details .sellkit-order-details-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sellkit-order-details .sellkit-order-details-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'order_details_icon_color',
			[
				'label' => esc_html__( 'Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-details .sellkit-order-details-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .sellkit-order-details .sellkit-order-details-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'order_details_icon_space_between',
			[
				'label' => esc_html__( 'Space Between', 'sellkit' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .sellkit-order-details-left .sellkit-order-details-icon i, {{WRAPPER}} .sellkit-order-details-left .sellkit-order-details-icon svg' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sellkit-order-details-right .sellkit-order-details-icon i, {{WRAPPER}} .sellkit-order-details-right .sellkit-order-details-icon svg' => 'margin-left: {{SIZE}}{{UNIT}};',

				],
			]
		);

		$this->add_control(
			'order_details_data',
			[
				'label' => esc_html__( 'Data', 'sellkit' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'order_details_data_typography',
				'selector' => '{{WRAPPER}} .order-details-item-content',
				'scheme' => '3',
			]
		);

		$this->add_control(
			'order_details_data_color',
			[
				'label' => esc_html__( 'Color', 'sellkit' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .order-details-item-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_details_data_margin',
			[
				'label' => esc_html__( 'Margin', 'sellkit' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .order-details-item-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function render_heading() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['list_name'] ) ) {
			return;
		}

		printf( '<h3>%1$s</h3>', $settings['list_name'] );
	}

	private function render_item_content() {
		$settings = $this->get_settings_for_display();
		$fields   = $settings['fields'];

		$this->add_render_attribute( 'details-order-content', [
			'class' => 'sellkit-order-details-content sellkit-flex sellkit-flex-wrap col-100',
		] );

		// phpcs:disable
		$order = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : false;
		$order = wc_get_order( $order );

		require sellkit()->plugin_dir() . 'includes/elementor/modules/order-details/templates/thankyou.php';
	}

	private function render_item() {

		$direction = 'left';
		if ( is_rtl() ) {
			$direction = 'right';
		}
		$this->add_render_attribute( 'details-order-item-wrapper', [
			'class' => 'woocommerce-order-overview woocommerce-thankyou-order-details order_details sellkit-order-details sellkit-order-details-' . $direction,
			'id' => 'sellkit-order-details-' . $this->get_id(),
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'details-order-item-wrapper' ); ?>>
			<?php
				$this->render_heading();
				$this->render_item_content();
			?>
		</div>
		<?php
	}

	protected function render() {
		$this->render_item();
	}

}
