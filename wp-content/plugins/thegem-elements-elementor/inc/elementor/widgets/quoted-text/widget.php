<?php
namespace TheGem_Elementor\Widgets\QuotedText;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;


if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Elementor widget for QuotedText.
 */
class TheGem_QuotedText extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if( ! defined( 'THEGEM_ELEMENTOR_WIDGET_QOUTE_DIR' ) ){
			define( 'THEGEM_ELEMENTOR_WIDGET_QOUTE_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_QOUTE_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_QOUTE_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}
		wp_register_style( 'thegem-quote', THEGEM_ELEMENTOR_WIDGET_QOUTE_URL . '/assets/css/thegem-quote.css', array(), null );
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-quoted-text';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Quoted Text', 'thegem' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return str_replace('thegem-', 'thegem-eicon thegem-eicon-', $this->get_name());
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'thegem_elements' ];
	}

	public function get_style_depends() {
		return [ 'thegem-quote' ];
	}

	/*Show reload button*/
	public function is_reload_preview_required() {
		return true;
	}


	/**
	 * Retrieve the value setting
	 * @access public
	 *
	 * @param string $control_id Control id
	 * @param string $control_sub Control value name (size, unit)
	 *
	 * @return string
	 */
	public function get_val( $control_id, $control_sub = null ) {
		if ( empty( $control_sub ) ) {
			return $this->get_settings()[ $control_id ];
		} else {
			return $this->get_settings()[ $control_id ][ $control_sub ];
		}
	}

	/**
	 * Create presets options for Select
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_presets_options() {
		$out = array(
			'style-1' => __( 'Design 1', 'thegem' ),
			'style-2' => __( 'Design 2', 'thegem' ),
			'style-3' => __( 'Design 3', 'thegem' ),
			'style-4' => __( 'Design 4', 'thegem' ),
			'style-5' => __( 'Design 5', 'thegem' ),
		);
		return $out;
	}

	/**
	 * Get default presets options for Select
	 *
	 * @param int $index
	 *
	 * @access protected
	 * @return string
	 */
	protected function set_default_presets_options() {
		return 'style-1';
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Layout', 'thegem' ),
			]
		);

			$this->add_control(
				'thegem_elementor_preset',
				[
					'label'   => __( 'Skin', 'thegem' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $this->get_presets_options(),
					'default' => $this->set_default_presets_options(),
					'frontend_available' => true,
					'render_type' => 'none',
				]
			);

		$this->end_controls_section();
		$this->start_controls_section(
			'content_settings',
			[
				'label' => __( 'Content', 'thegem' ),
			]
		);

			$this->add_control(
				'quoted_text',
				[
					'label' => __( 'Quoted text', 'thegem' ),
					'type' => Controls_Manager::WYSIWYG,
					'default' => __( 'Type your text here', 'thegem' ),
					'placeholder' => __( 'Type your text here', 'thegem' ),
				]
			);

		$this->end_controls_section();

		$this->add_styles_controls( $this );

	}

	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls($control) {

		$this->control = $control;

		/*Container Styles*/
		$this->container_styles( $control );

		/*Quoted Text  Styles*/
		$this->quoted_text_styles( $control );

		/*Quote  Styles*/
		$this->quote_style( $control );

	}


		/**
	 * Container Styles
	 * @access protected
	 */
	protected function container_styles( $control ) {
		
		$control->start_controls_section(
			'container',
			[
				'label' => __( 'Container Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->start_controls_tabs( 'container_tabs' );

			$control->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'container_bgcolor',
					'label' => __( 'Background Type', 'thegem' ),
					'types' => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .gem-quote',
				]
			);

			$control->add_responsive_control(
				'container_radius',
				[
					'label' => __( 'Radius', 'thegem' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'rem', 'em' ],
					'label_block' => true,
					'selectors' => [
						'{{WRAPPER}} .gem-quote' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$control->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'container_border',
					'label' => __( 'Border', 'thegem' ),
					'selector' => '{{WRAPPER}} .gem-quote',
					'condition' => [
						'thegem_elementor_preset!' => 'style-3',
					],
				]
			);

			$control->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'container_inner_border',
					'label' => __( 'Inner Border', 'thegem' ),
					'selector' => '{{WRAPPER}} .gem-quote.gem-quote-style-3 blockquote',
					'fields_options' => [
						'border' => [
							'label' => _x('Inner Border', 'Inner Border', 'thegem'),
						],
					],
					'condition' => [
						'thegem_elementor_preset' => 'style-3',
					],
				]
			);

			$control->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'container_outer_border',
					'label' => __( 'Outer Border', 'thegem' ),
					'selector' => '{{WRAPPER}} .gem-quote',
					'fields_options' => [
						'border' => [
							'label' => _x('Outer Border', 'Outer Border', 'thegem'),
						],
					],
					'condition' => [
						'thegem_elementor_preset' => 'style-3',
					],
				]
			);

			$control->add_responsive_control(
				'container_padding',
				[
					'label' => __( 'Padding', 'thegem' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'rem', 'em' ],
					'label_block' => true,
					'selectors' => [
						'{{WRAPPER}} .gem-quote:not(.gem-quote-style-3)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .gem-quote.gem-quote-style-3 blockquote' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$control->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'container_shadow',
					'label' => __( 'Shadow', 'thegem' ),
					'selector' => '{{WRAPPER}} .gem-quote',
				]
			);

		$control->end_controls_tabs();

		$control->end_controls_section();

	}

	/**
	 * Quoted Text Styles
	 * @access protected
	 */
	protected function quoted_text_styles( $control ) {

		$control->start_controls_section(
			'quoted_text_style',
			[
				'label' => __( 'Quoted Text Style ', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'quoted_text_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-quote blockquote, {{WRAPPER}} .gem-quote blockquote span, {{WRAPPER}} .gem-quote blockquote p' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'quoted_text_typ',
				'selector' => '{{WRAPPER}} .gem-quote blockquote, {{WRAPPER}} .gem-quote blockquote span, {{WRAPPER}} .gem-quote blockquote p',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'quoted_text_align',
			[
				'label' => __( 'Alignment', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'thegem' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'thegem' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'text-align: left;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;',

				],
				'selectors' => [
					'{{WRAPPER}} .gem-quote blockquote, {{WRAPPER}} .gem-quote blockquote span, {{WRAPPER}} .gem-quote blockquote p' => '{{VALUE}}',
				],
			]
		);

		$control->end_controls_section();

	}

	/**
	 * Quote Style
	 * @access protected
	 */
	protected function quote_style( $control ) {

		$control->start_controls_section(
			'quote',
			[
				'label' => __( 'Quote Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'quote_size',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'rem' => [
						'min' => 1,
						'max' => 100,
					],
					'em' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 76,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-quote::after' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'quote_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-quote::after' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'quote_align',
			[
				'label' => __( 'Alignment', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'thegem' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'thegem' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'left: 87px;',
					'center' => 'right: 46%;',
					'right' => 'right: 10%;',

				],
				'selectors' => [
					'{{WRAPPER}} .gem-quote::after' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'quote_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'allowed_dimensions' => ['bottom', 'right', 'left'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-quote::after' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->end_controls_section();

	}
	/**
	 * Helper check in array value
	 * @access protected
	 * @return string
	 */
	function is_in_array_value( $array = array(), $value = '', $default = '' ) {
		if ( in_array( $value, $array ) ) {
			return $value;
		}
		return $default;
	}

	protected function get_setting_preset( $val ) {
		if( empty( $val ) ) {
			return '';
		}

		return $val;
	}

	protected function get_presets_arg( $val ) {
		if ( empty( $val ) ) {
			return null;
		}

		return json_decode( $val, true );
	}


	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render() {
		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'quoted_text', 'none' );
		$this->add_render_attribute( 'quoted_text', 'class', 'gem-text-output' );

		$preset_path = __DIR__ . '/templates/preset_html1.php';

		if ( !empty( $preset_path) && file_exists( $preset_path) ){
			include( $preset_path );
		}
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new TheGem_QuotedText() );
