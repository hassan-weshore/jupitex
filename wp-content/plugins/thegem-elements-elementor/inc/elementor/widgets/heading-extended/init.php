<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

class TheGem_Heading_Extended {

	private static $instance = null;


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {
		add_action( 'elementor/element/heading/section_title/before_section_end', array( $this, 'before_section_end' ), 10, 2 );
		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render' ));
		add_filter( 'elementor/widget/print_template', array( $this, 'print_template'), 10, 2);
	}

	public function before_section_end( $element, $args ) {

		$element->add_control(
			'thegem_heading_style',
			[
				'label' => 'DIV Style',
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
				'default' => 'title-h2',
				'condition' => [
					'header_size' => 'div',
				]
			]
		);

		$element->add_control(
			'thegem_heading_weight',
			[
				'label' => 'Title Weight',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'bold' => 'Bold',
					'thin' => 'Thin',
				],
				'default' => 'bold',
				'condition' => [
					'thegem_heading_style!' => ['styled-subtitle', 'default']
				]
			]
		);

	}

	public function before_render($element) {

		if('heading' !== $element->get_name()) {
			return ;
		}

		$settings = $element->get_settings_for_display();

		if ( ! empty( $settings['thegem_heading_style'] ) ) {
			$element->add_render_attribute( 'title', 'class', $settings['thegem_heading_style'] );
		}

		if ( ! empty( $settings['thegem_heading_weight'] ) && $settings['thegem_heading_weight'] === 'thin' ) {
			$element->add_render_attribute( 'title', 'class', 'light' );
		}

	}

	public function print_template($template, $element) {

		if('heading' === $element->get_name()) {
			$old_template = 'view.addInlineEditingAttributes( \'title\' )';
			ob_start();
?>

		if ( 'div' === settings.header_size && '' !== settings.thegem_heading_style ) {
			view.addRenderAttribute( 'title', 'class', settings.thegem_heading_style );
		}

		if ( 'thin' === settings.thegem_heading_weight ) {
			view.addRenderAttribute( 'title', 'class', 'light' );
		}

		view.addInlineEditingAttributes( 'title' );
<?php
			$new_template = ob_get_clean();

			$template = str_replace( $old_template, $new_template, $template );
		}

		return $template;
	}

}

TheGem_Heading_Extended::instance();