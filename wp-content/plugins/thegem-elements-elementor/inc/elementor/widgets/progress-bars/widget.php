<?php

namespace TheGem_Elementor\Widgets\Diagram;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Elementor widget for Diagram.
 */
class TheGem_Diagram extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_DIAGRAM_DIR' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_DIAGRAM_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_DIAGRAM_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_DIAGRAM_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}

		wp_register_style( 'thegem-diagrams', THEGEM_ELEMENTOR_WIDGET_DIAGRAM_URL . '/assets/css/thegem-diagrams.css', array(), null );
		wp_register_script( 'thegem-diagram_circle', THEGEM_ELEMENTOR_WIDGET_DIAGRAM_URL . '/assets/js/diagram_circle.js', array( 'jquery', 'raphael' ), null, true );
		wp_register_script( 'thegem-diagram_line', THEGEM_ELEMENTOR_WIDGET_DIAGRAM_URL . '/assets/js/diagram_line.js', array( 'jquery' ), null, true );

	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-diagram';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Progress Bars', 'thegem' );
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
		return [ 'thegem-diagrams' ];
	}

	public function get_script_depends() {
		if ( Plugin::$instance->preview->is_preview_mode() ) {
			return [ 'thegem-diagram_line', 'thegem-diagram_circle' ];
		}

		return [];
	}

	/*Show reload button*/
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		$this->content_settings();

		$this->style_settings();

	}

	/**
	 * Content Settings
	 * @access protected
	 */
	protected function content_settings() {

		$this->start_controls_section(
			'layout_settings',
			[
				'label' => __( 'Layout', 'thegem' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'content_layout',
			[
				'label'              => __( 'Layout', 'thegem' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'linear',
				'multiple'           => false,
				'options'            => [
					'linear'   => __( 'Linear', 'thegem' ),
					'circular' => __( 'Circular', 'thegem' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'content_style',
			[
				'label'              => __( 'Style', 'thegem' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'style-2',
				'multiple'           => false,
				'options'            => [
					'style-2' => __( 'Style 1', 'thegem' ),
					'style-3' => __( 'Style 2', 'thegem' ),
				],
				'condition'          => [
					'content_layout' => 'linear'
				],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_items',
			[
				'label' => __( 'Items', 'thegem' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'content_items_skill',
			[
				'label'       => __( 'Skill', 'thegem' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Skill #', 'thegem' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'content_items_level',
			[
				'label'      => __( 'Level', 'thegem' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 50,
				],
			]
		);

		$repeater->add_control(
			'content_items_want_customize',
			[
				'label'        => __( 'Want to customize?', 'thegem' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'thegem' ),
				'label_off'    => __( 'No', 'thegem' ),
				'return_value' => 'yes',
				'default'      => '',
				'render_type'  => 'template',
			]
		);

		$repeater->add_control(
			'content_items_level_color',
			[
				'label'       => __( 'Level Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'content_items_want_customize' => 'yes'
				],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .clearfix .skill-line div' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'content_items_base_color',
			[
				'label'       => __( 'Base Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'content_items_want_customize' => 'yes',
				],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .diagram-wrapper .digram-line-box {{CURRENT_ITEM}} .clearfix .skill-line' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'content_items_name_color',
			[
				'label'       => __( 'Name Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'content_items_want_customize' => 'yes'
				],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .digram-line-box {{CURRENT_ITEM}} .diagram-skill-title' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'content_items_percentage_color',
			[
				'label'       => __( 'Percentage Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'content_items_want_customize' => 'yes'
				],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.skill-element .diagram-skill-amount' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_items_items',
			[
				'label'       => __( 'Items', 'thegem' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'content_items_skill'            => __( 'Skill #1', 'thegem' ),
						'content_items_level'            => [
							'unit' => '%',
							'size' => 70,
						],
						'content_items_level_color'      => '#ff0000',
						'content_items_base_color'       => '#e8edf1',
						'content_items_name_color'       => '#5f727f',
						'content_items_percentage_color' => '#5f727f',
					],
					[
						'content_items_skill'            => __( 'Skill #2', 'thegem' ),
						'content_items_level'            => [
							'unit' => '%',
							'size' => 70,
						],
						'content_items_level_color'      => '#ffff00',
						'content_items_base_color'       => '#e8edf1',
						'content_items_name_color'       => '#5f727f',
						'content_items_percentage_color' => '#5f727f',
					],
					[
						'content_items_skill'            => __( 'Skill #3', 'thegem' ),
						'content_items_level'            => [
							'unit' => '%',
							'size' => 70,
						],
						'content_items_level_color'      => '#ff00ff',
						'content_items_base_color'       => '#e8edf1',
						'content_items_name_color'       => '#5f727f',
						'content_items_percentage_color' => '#5f727f',
					],
				],
				'title_field' => '{{{ content_items_skill }}} - {{{ content_items_level.size }}}%',
			]
		);

		$this->end_controls_section();

		$this->content_summary();

		$this->start_controls_section(
			'options_settings',
			[
				'label' => __( 'Options', 'thegem' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_items_list',
			[
				'label'     => __( 'Show Items List', 'thegem' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'content_layout' => 'circular'
				],
			]
		);

		$this->add_control(
			'show_percentage',
			[
				'label'     => __( 'Show Percentage', 'thegem' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'content_layout' => 'linear'
				],
			]
		);

		$this->add_control(
			'lazy',
			[
				'label'     => __( 'Lazy Loading Animation', 'thegem' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'content_layout' => 'linear'
				],
				'default'   => 'yes'
			]
		);

		$this->end_controls_section();

	}

	/**
	 * ✔ Tab content section Summary
	 */
	protected function content_summary() {
		$this->start_controls_section(
			'summary_settings',
			[
				'label'     => __( 'Summary', 'thegem' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'content_layout' => 'circular'
				]
			]
		);

		$this->add_control(
			'summary_title',
			[
				'label'       => __( 'Title', 'thegem' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Title', 'thegem' ),
				'label_block' => true
			]
		);

		$this->add_control(
			'summary_description',
			[
				'label'   => __( 'Description', 'thegem' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Summary comes here', 'thegem' )
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Settings
	 */
	protected function style_settings() {

		$this->bars_style();

		$this->name_style();

		$this->percentage_style();

		$this->summary_style();

	}

	/**
	 * Bars Style
	 */
	protected function bars_style() {
		$this->start_controls_section(
			'bars_style_settings',
			[
				'label' => __( 'Bars Style', 'thegem' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bars_length',
			[
				'label'     => __( 'Bar Length', 'thegem' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => [
					'content_layout' => 'linear'
				],
				'selectors' => [
					'{{WRAPPER}} .skill-line' => 'width: {{SIZE}}%;',
				]
			]
		);

		$this->add_control(
			'bars_weight',
			[
				'label'      => __( 'Bar Weight', 'thegem' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => [
					'content_layout' => 'linear'
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .skill-line'     => 'height: {{SIZE}}px;',
					'{{WRAPPER}} .skill-line div' => 'height: {{SIZE}}px;',
				]
			]
		);

		$this->add_control(
			'bars_level_color',
			[
				'label'       => __( 'Level Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#3c3950',
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .diagram-wrapper .skill-line div' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .diagram-circle input.color'      => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'bars_base_color',
			[
				'label'       => __( 'Base Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'render_type' => 'template',
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .diagram-wrapper .digram-line-box .skill-line' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'bars_radius',
			[
				'label'     => __( 'Radius', 'thegem' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'condition' => [
					'content_layout' => 'linear'
				],
				'selectors' => [
					'{{WRAPPER}} .skill-line'     => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					'{{WRAPPER}} .skill-line div' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_control(
			'hr-1',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'content_layout' => 'linear'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'bars_border',
				'label'       => __( 'Border', 'thegem' ),
				'label_block' => true,
				'condition'   => [
					'content_layout' => 'linear'
				],
				'selector'    => '{{WRAPPER}} {{CURRENT_ITEM}} .skill-line div',
			]
		);


		$this->add_control(
			'hr-2',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'content_layout' => 'linear'
				],
			]
		);

		$this->add_control(
			'spacing_between',
			[
				'label'     => __( 'Spacing Between', 'thegem' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => [
					'content_layout' => 'linear'
				],
				'selectors' => [
					'{{WRAPPER}} .diagram-wrapper .digram-line-box .skill-element + .skill-element' => 'margin-top: {{SIZE}}px;',
				]
			]
		);

		$this->add_control(
			'hr-3',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'content_layout' => 'linear'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'label'       => __( 'Shadow', 'thegem' ),
				'name'        => 'bars_shadow',
				'label_block' => true,
				'condition'   => [
					'content_layout' => 'linear'
				],
				'selector'    => '{{WRAPPER}} .skill-line'
			]
		);

		$this->end_controls_section();
	}

	protected function name_style() {
		$this->start_controls_section(
			'name_style_settings',
			[
				'label' => __( 'Name Style', 'thegem' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'name_style_color',
			[
				'label'       => __( 'Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .digram-line-box .diagram-skill-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'name_style_typography',
				'label'     => __( 'Typography', 'thegem' ),
				'condition' => [
					'content_layout' => 'linear'
				],
				'selector'  => '{{WRAPPER}} .diagram-skill-title',
			]
		);

		$this->add_control(
			'name_style_bottom_spacing',
			[
				'label'      => __( 'Bottom Spacing', 'thegem' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => [
					'content_layout' => 'linear',
					'content_style!' => 'style-3',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 50,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .skill-title' => 'margin-bottom: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'name_style_left_spacing',
			[
				'label'      => __( 'Left Spacing', 'thegem' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 50,
						'max'  => 300,
						'step' => 1,
					],
				],
				'condition'  => [
					'content_layout' => 'linear'
				],
				'selectors'  => [
					'{{WRAPPER}} .skill-title' => 'margin-left: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'inner_area_name_style',
			[
				'label'     => __( 'Inner Area', 'thegem' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'content_layout' => 'circular'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'name_style_inner_area_typography',
				'label'     => __( 'Typography', 'thegem' ),
				'condition' => [
					'content_layout' => 'circular'
				],
				'selector'  => '{{WRAPPER}} .diagram-circle .text span.diagram-skill-title'
			]
		);

		$this->add_control(
			'inner_area_name_items_list',
			[
				'label'     => __( 'Items List', 'thegem' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'content_layout' => 'circular'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'name_style_items_list_typography',
				'label'     => __( 'Typography', 'thegem' ),
				'condition' => [
					'content_layout' => 'circular'
				],
				'selector'  => '{{WRAPPER}} .diagram-legend-title'
			]
		);

		$this->add_control(
			'name_style_items_list_position',
			[
				'type'      => Controls_Manager::CHOOSE,
				'label'     => __( 'Position', 'thegem' ),
				'condition' => [
					'content_layout' => 'circular'
				],
				'options'   => [
					'bottom' => [
						'title' => __( 'Bottom', 'thegem' ),
						'icon'  => 'fa fa-caret-down',
					],
					'right'  => [
						'title' => __( 'Right', 'thegem' ),
						'icon'  => 'fa fa-caret-right',
					],
				],
				'default'   => 'right'
			]
		);

		// For right legend - left spacing
		$this->add_control(
			'name_style_items_list_spacing',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Spacing', 'thegem' ),
				'condition'  => [
					'content_layout'                 => 'circular',
					'name_style_items_list_position' => 'right'
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => - 100,
						'max' => 400,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .diagram-legend' => 'margin-left: {{SIZE}}px;'
				]
			]
		);

		// For bottom legend - top spacing
		$this->add_control(
			'name_style_items_list_spacing-2',
			[
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Spacing', 'thegem' ),
				'condition'  => [
					'content_layout'                 => 'circular',
					'name_style_items_list_position' => 'bottom',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => - 100,
						'max' => 400,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .diagram-legend' => 'margin-top: {{SIZE}}px;'
				]
			]
		);

		$this->end_controls_section();
	}

	protected function percentage_style() {
		$this->start_controls_section(
			'percentage_style_settings',
			[
				'label' => __( 'Percentage Style', 'thegem' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'percentage_style_color',
			[
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Color', 'thegem' ),
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .digram-line-box .diagram-skill-amount' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'percentage_style_typography',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => '{{WRAPPER}} .diagram-skill-amount'
			]
		);

		$this->add_control(
			'percentage_right_spacing',
			[
				'label'      => __( 'Right Spacing', 'thegem' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'%' => [
						'min' => - 100,
						'max' => 300,
					],
				],
				'default'    => [
					'unit' => 'px'
				],
				'condition'  => [
					'content_style!'  => 'style-3',
					'content_layout!' => 'circular'
				],
				'selectors'  => [
					'{{WRAPPER}} .skill-amount' => 'margin-right: {{SIZE}}px;',
				]
			]
		);

		$this->add_control(
			'percentage_left_spacing',
			[
				'label'      => __( 'Left Spacing', 'thegem' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => [
					'content_style'   => 'style-3',
					'content_layout!' => 'circular'
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => - 100,
						'max' => 300,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors'  => [
					'{{WRAPPER}} .style-3 .skill-title > span:last-child' => 'margin-left: {{SIZE}}px;',
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * ✔ Tab Style section Summary
	 */
	protected function summary_style() {
		$this->start_controls_section(
			'summary_style_settings',
			[
				'label'     => __( 'Summary Style', 'thegem' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_layout' => 'circular'
				],
			]
		);

		$this->add_control(
			'summary_style_title_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => __( 'Title', 'thegem' )
			]
		);

		$this->add_control(
			'summary_style_title_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .text .diagram-summary-title' => 'color:{{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'summary_style_title_typography',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => '{{WRAPPER}} .text .diagram-summary-title',
			]
		);


		$this->add_control(
			'summary_style_summary_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => __( 'Summary', 'thegem' )
			]
		);

		$this->add_control(
			'summary_style_summary_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'thegem' ),
				'selectors' => [
					'{{WRAPPER}} .text .diagram-summary-summary' => 'color:{{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'summary_style_summary_typography',
				'label'    => __( 'Typography', 'thegem' ),
				'selector' => '{{WRAPPER}} .text .diagram-summary-summary'
			]
		);

		$this->end_controls_section();
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
		$skills   = $settings['content_items_items'];

		$template = __DIR__ . "/templates/preset_html-{$settings['content_layout']}.php";

		if ( 'yes' === $settings['lazy'] ) {
			thegem_lazy_loading_enqueue();
		}

		if ( 'linear' === $settings['content_layout'] ) {
			wp_enqueue_script( 'thegem-diagram_line' );

			$diagram_wrapper = 'diagram_wrapper';
			$lazy_wrapper    = 'lazy_wrapper';
			$lazy_item       = 'lazy_item';

			$this->add_render_attribute( $diagram_wrapper, 'class', [
				'diagram-wrapper',
				$settings['content_style']
			] );

			$this->add_render_attribute( $lazy_item, 'class', [
				'digram-line-box'
			] );

			if ( 'yes' === $settings['lazy'] ) {
				$this->add_render_attribute( $lazy_wrapper, 'class', [
					'lazy-loading',
					'lazy-loading-not-hide'
				] );

				$this->add_render_attribute( $lazy_wrapper, 'data-ll-item-delay', [
					'0'
				] );

				$this->add_render_attribute( $lazy_item, 'class', [
					'lazy-loading-item'
				] );

				$this->add_render_attribute( $lazy_item, 'data-ll-effect', [
					'action'
				] );

				$this->add_render_attribute( $lazy_item, 'data-ll-action-func', [
					'thegem_start_line_digram'
				] );
			}

			if ( $settings['bars_base_color'] ) {
				$this->add_render_attribute( $diagram_wrapper, 'class', 'want-customize' );
			}

			if ( Plugin::$instance->editor->is_edit_mode() ) {
				$this->add_render_attribute( $lazy_item, 'class', 'lazy-loading-editor' );
			}

		} else {
			$diagram_wrapper = 'diagram_wrapper';
			$this->add_render_attribute( $diagram_wrapper,
				[
					'class'                => [ 'diagram-circle', 'clearfix' ],
					'data-show-legend'     => $settings['show_items_list'],
					'data-title'           => $settings['summary_title'],
					'data-summary'         => $settings['summary_description'],
					'data-legend-position' => $settings['name_style_items_list_position'],
					'data-base-color'      => $settings['bars_base_color'] ? $settings['bars_base_color'] : '#e8edf1',
				]
			);

			wp_enqueue_script( 'thegem-diagram_circle' );
		}

		if ( ! empty( $template ) && file_exists( $template ) ) {
			include $template;
		}

	}
}

Plugin::instance()->widgets_manager->register_widget_type( new TheGem_Diagram() );
