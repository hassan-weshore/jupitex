<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

class TheGem_Divider_Extended {

	private static $instance = null;


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {
		add_action( 'elementor/element/divider/section_divider/before_section_end', array( $this, 'before_section_end' ), 10, 2 );
		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render' ));
	}

	public function before_section_end( $element, $args ) {

		$element->add_control(
			'thegem_text_style',
			[
				'label' => 'Text Style',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => 'default',
					'title-h1' => 'Title H1',
					'title-h2' => 'Title H2',
					'title-h3' => 'Title H3',
					'title-h4' => 'Title H4',
					'title-h5' => 'Title H5',
					'title-h6' => 'Title H6',
					'title-xlarge' => 'Title xLarge',
					'styled-subtitle' => 'Styled Subtitle',
				],
				'default' => 'default',
				'condition' => [
					'look' => 'line_text',
				],
			]
		);

		$element->add_control(
			'thegem_text_weight',
			[
				'label' => 'Title Weight',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'bold' => 'Bold',
					'thin' => 'Thin',
				],
				'default' => 'bold',
				'condition' => [
					'thegem_text_style!' => ['styled-subtitle', 'default']
				]
			]
		);

	}

	public function before_render($element) {

		if('divider' !== $element->get_name()) {
			return ;
		}

		$settings = $element->get_settings_for_display();

		if ( ! empty( $settings['thegem_text_style'] ) ) {
			$element->add_render_attribute( 'text', 'class', [$settings['thegem_text_style'], 'gem-style-text'] );
		}

		if ( ! empty( $settings['thegem_text_weight'] ) && $settings['thegem_text_weight'] === 'thin' ) {
			$element->add_render_attribute( 'text', 'class', 'light' );
		}

	}

}

TheGem_Divider_Extended::instance();