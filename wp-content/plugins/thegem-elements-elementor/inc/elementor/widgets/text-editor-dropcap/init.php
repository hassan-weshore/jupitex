<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

class TheGem_Text_Editor_Dropcap {

	private static $instance = null;


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {
		add_action( 'elementor/element/text-editor/section_drop_cap/before_section_end', array( $this, 'before_section_end' ), 10, 2 );
	}

	public function before_section_end( $element, $args ) {

		$element->add_control(
			'thegem_drop_cap_bottom_space',
			[
				'label' => __( 'Bottom Space', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-drop-cap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->update_control(
			'drop_cap_view',
			[
				'default' => 'stacked'
			]
		);

		$element->add_responsive_control(
			'thegem_vertical_position',
			[
				'label' => __( 'Vertical Position', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-drop-cap' => 'margin-top: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-text-editor' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

	}

}

TheGem_Text_Editor_Dropcap::instance();