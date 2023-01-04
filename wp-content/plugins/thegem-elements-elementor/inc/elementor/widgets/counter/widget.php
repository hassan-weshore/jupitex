<?php

namespace TheGem_Elementor\Widgets\Counter;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

use WP_Query;


if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Team.
 */
class TheGem_Counter extends Widget_Base
{

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */
	protected $presets;

	public $preset_elements_select;

	public function __construct($data = [], $args = null)
	{
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_COUNTER_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_COUNTER_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_COUNTER_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_COUNTER_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-counter-css', THEGEM_ELEMENTOR_WIDGET_COUNTER_URL . '/assets/css/thegem-counter.css', array('odometr'), null);
		wp_register_script('thegem-counter-js', THEGEM_ELEMENTOR_WIDGET_COUNTER_URL . '/assets/js/thegem-counters.js', array('jquery', 'odometr'), null, true);
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */

	public function get_name() {
		return 'thegem-counter';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */

	public function get_title() {
		return __('Counter', 'thegem');
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
	public function get_categories()
	{
		return ['thegem_elements'];
	}

	public function get_style_depends()
	{
		return ['thegem-counter-css'];
	}

	public function get_script_depends()
	{
		return ['thegem-counter-js'];
	}

	public function is_reload_preview_required()
	{
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
	public function get_val($control_id, $control_sub = null)
	{
		if (empty($control_sub)) {
			return $this->get_settings()[$control_id];
		} else {
			return $this->get_settings()[$control_id][$control_sub];
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
			'counter-preset1' => __( 'Style 1', 'thegem' ),
			'counter-preset2' => __( 'Style 2', 'thegem' ),
			'counter-preset3' => __( 'Vertical Style', 'thegem' ),
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

		return 'counter-preset1';
	}


	/**
	 * @return array
	 * Get list teams persons
	 */
	protected function select_team_all_persons() {
		$args = array(
			'post_type' => 'thegem_team_person',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		);

		$posts = get_posts($args);
		$arr_teams_person = [0 => 'Select Person'];
		if( count($posts) > 0 ) {
			foreach($posts as $p){
				$arr_teams_person[$p->ID] =  get_post_meta($p->ID, 'thegem_team_person_data')[0]['name'];
			}
		}
		return $arr_teams_person;
	}



	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		// Sections Layout
		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'thegem_elementor_preset',
			[
				'label' => __('Skin', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'numbers_format',
			[
				'label' => __('Numbers Format', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('(ddd).ddd', 'thegem'),
				'description' => 'Example: (ddd).ddd -> 9999.99, ( ddd).ddd -> 9 999.99, (,ddd).ddd -> 9,999.99',
				'placeholder' => __('(ddd).ddd', 'thegem'),
			]
		);

		$this->end_controls_section();


		// Sections Counter
		$this->start_controls_section(
			'counter_content_settings',
			[
				'label' => __( 'Counter', 'thegem' ),

			]
		);

		$this->add_control(
			'counter_starting_number',
			[
				'label' => __( 'Starting Number', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 0,
			]
		);

		$this->add_control(
			'counter_ending_number',
			[
				'label' => __( 'Ending Number', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 1000,
			]
		);

		$this->add_control(
			'counter_number_suffix',
			[
				'label' => __( 'Number Suffix', 'thegem' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '', 'thegem' ),
				'placeholder' => __( 'Number Suffix', 'thegem' ),
			]
		);


		$this->add_control(
			'counter_spacing_suffix',
			[
				'label' => 'Spacing',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'thegem' ),
				'label_off' => __( 'No', 'thegem' ),
				'return_value' => 'yes',
				'default'      => '',
				'render_type'  => 'template',
			]
		);


		$this->add_control(
			'counter_description',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => __( '', 'thegem' ),
				'placeholder' => __( 'Counters', 'thegem' ),
			]
		);

		$this->add_control(
			'counter_link',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->add_control(
			'counter_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();


		// Sections Team Person
		$this->start_controls_section(
			'counter_team_settings',
			[
				'label' => __( 'Team Person', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => 'counter-preset3',
				],

			]
		);

		$this->add_control(
			'counter_show_person_block',
			[
				'label' => 'Show Team Person',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'content_team_all_persons',
			[
				'condition' => [
					'counter_show_person_block' => 'yes',
				],
				'label' => __( 'Select Team Person', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => array_keys($this->select_team_all_persons())[0],
				'options' => $this->select_team_all_persons(),
			]
		);

		$preset_elements_select = [
			'img' => __('Image', 'thegem'),
			'name' => __('Name', 'thegem'),
			'position' => __('Position', 'thegem'),
			'phone' => __('Phone', 'thegem'),
			'email' => __('Email', 'thegem'),
			'social' => __('Social', 'thegem'),
		];

		foreach ( $preset_elements_select as $ekey => $elem ) {

			$this->add_control(
				'counter-preset3_content_elems_'. $ekey, [
					'condition' => [
						'counter_show_person_block' => 'yes',
					],
					'label' => $elem,
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'thegem' ),
					'label_off' => __( 'Hide', 'thegem' ),
					'frontend_available' => true,
				]
			);
		}

		$this->add_control(
			'counter_show_connector',
			[
				'condition' => [
					'counter_show_person_block' => 'yes',
				],
				'label' => 'Connector',
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'counter_show_connector_no',
			[
				'condition' => [
					'counter_show_person_block' => '',
				],
				'label' => 'Connector',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();


		// Sections Additional Options
		$this->start_controls_section(
			'_additional_options_settings',
			[
				'label' => __( 'Additional Options', 'thegem' ),
			]
		);

		$this->add_control(
			'counter_animation_enabled',
			[
				'label' => 'Animation enabled',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'frontend_available' => true,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->add_styles_controls($this);

	}


	/**
	 * Container Styles
	 * @access protected
	 */
	protected function container_styles( $control ) {

		$control->start_controls_section(
			'counter_container',
			[
				'label' => __( 'Container Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->start_controls_tabs( 'counter_container_tabs' );

		$control->start_controls_tab( 'counter_container_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'counter_background',
				'label' => __( 'Background Color', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-counter-box'
			]
		);

		$control->add_responsive_control(
			'counter_container_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_container_border',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-counter-box',

			]
		);

		$control->add_responsive_control(
			'counter_container_align',
			[
				'label' => __( 'Content Align', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'separator' => 'before',
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
					'left' => 'text-align: left; align-items: flex-start; justify-content: flex-start;',
					'center' => 'text-align: center; align-items: center; justify-content: center;',
					'right' => 'text-align: right; align-items: flex-end; justify-content: flex-end;',

				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box, 
					 {{WRAPPER}} .gem-counter-icon, 
					 {{WRAPPER}} .gem-counter-number, 
					 {{WRAPPER}} .gem-counter-text' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'counter_icon_font_size1',
			[
				'label' => __( 'Top Spacing ', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => 'counter-preset3',
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
	   );

		$control->add_responsive_control(
			'counter_container_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'counter_container_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-counter-box',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'counter_container_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'counter_background_hv',
				'label' => __( 'Background Color', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-counter-box::before'
			]
		);

		$control->add_control(
			'counter_container_brdcolor_hv',
			[
				'label' => __( 'Border Container Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box:hover' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'counter_container_shadow_hv',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-counter-box:hover',
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * Number Styles
	 * @access protected
	 */
	protected function number_styles( $control ) {

		$control->start_controls_section(
			'counter_number',
			[
				'label' => __( 'Number Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->start_controls_tabs( 'counter_number_tabs' );

		$control->start_controls_tab( 'counter_number_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_control(
			'counter_number_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-number' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'counter_title_typ',
				'selector' => '{{WRAPPER}} .gem-counter-number'
			]
		);

		$control->add_responsive_control(
			'counter_number_width',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-number' => 'margin-bottom:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'counter_number_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'counter_number_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box:hover .gem-counter-number' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'counter_number_typ_hv',
				'selector' => '{{WRAPPER}} .gem-counter-number:hover'
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();
	}



	/**
	 * Description Styles
	 * @access protected
	 */
	protected function description_styles( $control ) {

		$control->start_controls_section(
			'counter_description_style',
			[
				'label' => __( 'Description Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->start_controls_tabs( 'counter_description_tabs' );

		$control->start_controls_tab( 'counter_description_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_control(
			'counter_description_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-text' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'counter_description_typ',
				'selector' => '{{WRAPPER}} .gem-counter-text'
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'counter_description_style_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'counter_description_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box:hover .gem-counter-text' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'counter_description_typ_hv',
				'selector' => '{{WRAPPER}} .gem-counter-text:hover'
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}



	/**
	 * Icon Styles
	 * @access protected
	 */
	protected function icon_styles( $control ) {

		$control->start_controls_section(
			'counter_icon_style',
			[
				'label' => __( 'Icon Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		// Style 1 & Vertical Style

		$control->add_responsive_control(
			'counter_icon_font_size',
			[
				'label' => __( 'Size', 'thegem' ),
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=', // it accepts:  =,==, !=,!==,  in, !in etc.
							'value' => 'counter-preset3',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'counter-preset1',
						],
					]
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
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
					'size' => 48,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon-inner i' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: 1; display: flex; justify-content: center; align-items: center;',
                    '{{WRAPPER}} .gem-icon-inner .wrapper-icon-inner' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .gem-icon-inner svg' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'counter_icon_padding_style1',
			[
				'label' => __( 'Padding', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => ['counter-preset1']
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
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
					'size' => 5,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon-inner' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'counter_icon_padding_style3',
			[
				'label' => __( 'Padding', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => ['counter-preset3']
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
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
					'size' => 20,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon-inner' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
				'counter_icon_bottom_spacing',
				[
					'label' => __( 'Bottom Spacing', 'thegem' ),
					'condition' => [
						'thegem_elementor_preset' => 'counter-preset1',
					],
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'rem', 'em' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 300,
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
						'size' => 10,
						'unit' => 'px',
					],
					'selectors' => [
						'{{WRAPPER}} .gem-counter-icon' => 'margin-bottom:{{SIZE}}{{UNIT}}',
					],
				]
			);

		$control->add_responsive_control(
				'counter_icon_top_spacing',
				[
					'label' => __( 'Top Spacing', 'thegem' ),
					'condition' => [
						'thegem_elementor_preset' => 'counter-preset3',
					],
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'rem', 'em' ],
					'range' => [
						'px' => [
							'min' => -200,
							'max' => 200,
						],
						'%' => [
							'max' => 100,
						],
						'rem' => [
							'max' => 100,
						],
						'em' => [
							'max' => 100,
						],
					],
					'default' => [
						'size' => -47,
						'unit' => 'px',
					],
					'selectors' => [
						'{{WRAPPER}} .gem-counter-icon' => 'margin-top:{{SIZE}}{{UNIT}}',
					],
				]
			);

		$control->add_responsive_control(
				'counter_icon_bottom_spacing_preset3',
				[
					'label' => __( 'Bottom Spacing', 'thegem' ),
					'condition' => [
						'thegem_elementor_preset' => 'counter-preset3',
					],
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'rem', 'em' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 200,
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
					'selectors' => [
						'{{WRAPPER}} .gem-counter-number' => 'margin-top:{{SIZE}}{{UNIT}}',
					],
				]
			);

		$control->add_responsive_control(
			'counter_icon_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=', // it accepts:  =,==, !=,!==,  in, !in etc.
							'value' => 'counter-preset3',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'counter-preset1',
						],
					]
				],
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				//'default' => !empty($this->preset_styles['image_radius']) ? $this->preset_styles['image_radius'] : null,
				'selectors' => [
					'{{WRAPPER}} .gem-icon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;'
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'counter_icon_shadow',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=', // it accepts:  =,==, !=,!==,  in, !in etc.
							'value' => 'counter-preset3',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'counter-preset1',
						],
					]
				],
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-icon-inner',
			]
		);

		$control->start_controls_tabs( 'counter_icon_tabs',
			[
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=', // it accepts:  =,==, !=,!==,  in, !in etc.
							'value' => 'counter-preset3',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'counter-preset1',
						],
					]
				],
			]);

		$control->start_controls_tab( 'counter_icon_tab_normal', ['label' => __( 'Normal', 'thegem' ),] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'counter_icon_background',
				'label' => __( 'Background Color', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-icon-inner'
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_icon_border_style1',
				'label' => __( 'Border', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => ['counter-preset1']
				],
				'selector' => '{{WRAPPER}} .gem-icon-inner',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_icon_border_style3',
				'label' => __( 'Border', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => ['counter-preset3']
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '3',
							'right' => '3',
							'bottom' => '3',
							'left' => '3',
							'isLinked' => true,
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-icon-inner',
			]
		);

		$control->add_control(
			'counter_icon_bgcolor',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-icon-inner i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gem-icon-inner .wrapper-icon-inner svg' => 'fill:{{VALUE}};',
                    '{{WRAPPER}} .gem-icon-inner svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_control(
			'counter_rotate_icon_style',
			[
				'label' => __( 'Rotate Icon, %', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 360,
					],
					'px' => [
						'min' => 0,
						'max' => 360,
					],
					'em' => [
						'min' => 0,
						'max' => 360,
					],
					'rem' => [
						'min' => 0,
						'max' => 360,
					]
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon-inner' => 'transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .gem-icon-inner .wrapper-icon-inner svg' => 'transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'counter_icon_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'counter_icon_background_hv',
				'label' => __( 'Background Color', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .icon-hover-bg'
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_icon_border_hv',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner',
			]
		);

		$control->add_control(
			'counter_icon_bgcolor_hv',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner .wrapper-icon-inner svg' => 'fill:{{VALUE}};',
                    '{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_control(
			'counter_rotate_icon_style_hv',
			[
				'label' => __( 'Rotate Icon, %', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 360,
					],
					'px' => [
						'min' => 0,
						'max' => 360,
					],
					'em' => [
						'min' => 0,
						'max' => 360,
					],
					'rem' => [
						'min' => 0,
						'max' => 360,
					]
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner' => 'transform: rotate({{SIZE}}deg)',
                    '{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner .wrapper-icon-inner svg' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();


		// For Style 2

		$control->add_responsive_control(
			'counter_icon_font_size_style2',
			[
				'label' => __( 'Size', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'counter-preset2' ]
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 200,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 48,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon-inner' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'counter_inner_border_style2',
			[
				'label' => __( 'Padding', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'counter-preset2' ]
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'%' => [
						'min' => 30,
						'max' => 300,
					],
					'px' => [
						'min' => 0,
						'max' => 400,
					],
					'em' => [
						'min' => 0,
						'max' => 400,
					],
					'rem' => [
						'min' => 0,
						'max' => 400,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-icon-circle-2' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};line-height:{{SIZE}}{{UNIT}};',

				],
			]
		);

		$control->start_controls_tabs(
			'counter_icon_tabs_preset2',
			[
				'condition' => [
					'thegem_elementor_preset' => [ 'counter-preset2' ]
				],

			]
		);

		$control->start_controls_tab( 'counter_icon_tab_normal_preset2', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_control(
			'counter_icon_bgcolor_style2',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-icon-inner i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gem-icon-inner svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_icon_border_style2',
				'label' => __( 'Border', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => ['counter-preset2']
				],
				'fields_options' => [
					'border' => [
						'default' => '',
					],
					'width' => [
						'default' => [
							'top' => '2',
							'right' => '2',
							'bottom' => '2',
							'left' => '2',
							'isLinked' => true,
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-counter-icon-circle-2',
			]
		);

		$control->add_control(
			'counter_rotate_icon_style2',
			[
				'label' => __( 'Rotate Icon, %', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 360,
					],
					'px' => [
						'min' => 0,
						'max' => 360,
					],
					'em' => [
						'min' => 0,
						'max' => 360,
					],
					'rem' => [
						'min' => 0,
						'max' => 360,
					]
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon-inner' => 'transform: rotate({{SIZE}}deg);',
                    '{{WRAPPER}} .gem-icon-inner .wrapper-icon-inner svg' => 'transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'counter_icon_tab_hover_preset2', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'counter_icon_bgcolor_style2_hv',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_icon_border_style2_hv',
				'label' => __( 'Border', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => ['counter-preset2']
				],
				'fields_options' => [
					'border' => [
						'default' => '',
					],
					'width' => [
						'default' => [
							'top' => '2',
							'right' => '2',
							'bottom' => '2',
							'left' => '2',
							'isLinked' => true,
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-counter-box:hover .gem-counter-icon-circle-2',
			]
		);

		$control->add_control(
			'counter_rotate_icon_style2_hv',
			[
				'label' => __( 'Rotate Icon, %', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 360,
					],
					'px' => [
						'min' => 0,
						'max' => 360,
					],
					'em' => [
						'min' => 0,
						'max' => 360,
					],
					'rem' => [
						'min' => 0,
						'max' => 360,
					]
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner' => 'transform: rotate({{SIZE}}deg);',
                    '{{WRAPPER}} .gem-counter-box:hover .gem-icon-inner .wrapper-icon-inner svg' => 'transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		//Outer Border Style
		$control->add_control(
			'counter_double_borders_options',
			[
				'label' => __( 'Outer Border Style', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'counter-preset2' ]
				],
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'counter_double_borders_style2',
			[
				'label' => __( 'Radius', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'counter-preset2' ]
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-icon-circle-1' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'counter_number_width_style2',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'counter-preset2' ]
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-icon' => 'margin-bottom:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->start_controls_tabs( 'counter_icon_db_tabs',
			[
				'condition' => [
					'thegem_elementor_preset' => [ 'counter-preset2' ]
				],
			]
		);

		$control->start_controls_tab( 'counter_icon_tab_db_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_control(
			'counter_bg_color_db_border_icon',
			[
				'label' => __( 'Background Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-icon-circle-1' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_icon_border_outer_style2',
				'label' => __( 'Border', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => ['counter-preset2']
				],
				'fields_options' => [
					'border' => [
						'default' => '',
					],
					'width' => [
						'default' => [
							'top' => '4',
							'right' => '4',
							'bottom' => '4',
							'left' => '4',
							'isLinked' => true,
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-counter-icon-circle-1',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'counter_icon_tab_db_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'counter_bg_color_db_border_icon_hv',
			[
				'label' => __( 'Background Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-box:hover .gem-counter-icon-circle-1' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_icon_border_outer_style2_hv',
				'label' => __( 'Border', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => ['counter-preset2']
				],
				'fields_options' => [
					'border' => [
						'default' => '',
					],
					'width' => [
						'default' => [
							'top' => '4',
							'right' => '4',
							'bottom' => '4',
							'left' => '4',
							'isLinked' => true,
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-counter-box:hover .gem-counter-icon-circle-1',
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();
	}



	/**
	 * Team Person Styles
	 * @access protected
	 */
	protected function team_person_styles( $control ) {

		$control->start_controls_section(
			'counter_team_person_style',
			[

				'label' => __( 'Team Person Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'counter_show_person_block' => 'yes',
				],
			]
		);

		//Start Container Style
		$control->add_control(
			'counter_container_options',
			[
				'label' => __( 'Container Style', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs( 'counter_team_person_tabs' );

		$control->start_controls_tab( 'counter_team_person_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'counter_team_person_bgcolor',
				'label' => __( 'Background Color', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .team-person-info'
			]
		);

		$control->add_responsive_control(
			'counter_team_person_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .team-person-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_team_person',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .team-person-info',

			]
		);

		$control->add_responsive_control(
			'counter_team_person_align',
			[
				'label' => __( 'Content Align', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				//'default' => 'center',
				'label_block' => false,
				'separator' => 'before',
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
					'left' => 'text-align: left;align-items: flex-start;',
					'center' => 'text-align: center;align-items: center;',
					'right' => 'text-align: right;align-items: flex-end;',

				],
				'selectors' => [
					'{{WRAPPER}} .team-person-info' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'counter_team_person_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .team-person-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'counter_team_person_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .team-person-info',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'counter_team_person_style_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'counter_team_person_bgcolor_hv',
				'label' => __( 'Background Color', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .team-person-info:hover'
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_team_person_hv',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .team-person-info:hover',

			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'counter_team_person_shadow_hv',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .team-person-info:hover',
			]
		);

		$control->end_controls_tabs();
		//End Container Style


		//Start Image Style
		$control->add_control(
			'counter_image_options',
			[
				'label' => __( 'Image Style', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'counter_image_team_person_dimension',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 200,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 90,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .team-person-image img' => 'max-width:{{SIZE}}%; width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'counter_image_team_person_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				//'default' => !empty($this->preset_styles['image_radius']) ? $this->preset_styles['image_radius'] : null,
				'selectors' => [
					'{{WRAPPER}} .team-person-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'counter_image_team_person_border',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .team-person-image img',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'counter_image_team_person_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .team-person-image img',
			]
		);
		//End Image Style


		//Start Name Style
		$control->add_control(
			'counter_name_options',
			[
				'label' => __( 'Name', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'counter_name_team_person_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				//'default' => 'rgb(60, 57, 80)',
				'selectors' => [
					'{{WRAPPER}} .team-person-name' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'counter_name_team_person_typ',
				'selector' => '{{WRAPPER}} .team-person-name',
			]
		);

		$control->add_responsive_control(
			'counter_name_team_person_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 5,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .team-person-name' => 'margin-bottom:{{SIZE}}{{UNIT}}; margin-top:20px;',
				],
			]
		);
		//End Name Style


		//Start Position Style
		$control->add_control(
			'counter_position_options',
			[
				'label' => __( 'Position', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'counter_position_team_person_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-position' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'counter_position_team_person_typ',
				'selector' => '{{WRAPPER}} .team-person-position',
			]
		);

		$control->add_responsive_control(
			'counter_position_team_person_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .team-person-position' => 'margin-bottom:{{SIZE}}{{UNIT}}',
				],
			]
		);
		//End Position Style

		//Start Phone Number Style
		$control->add_control(
			'counter_phone_number_options',
			[
				'label' => __( 'Phone Number', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'counter_phone_team_person_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-phone a' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'counter_phone_team_person_typ',
				'selector' => '{{WRAPPER}} .team-person-phone a'
			]
		);

		$control->add_responsive_control(
			'counter_phone_team_person_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .team-person-phone' => 'margin-bottom:{{SIZE}}{{UNIT}}',
				],
			]
		);
		//End Phone Number Style

		//Start Email Style
		$control->add_control(
			'counter_email_options',
			[
				'label' => __( 'Email ', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'counter_email_team_person_icon_size',
			[
				'label' => __( 'Size Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 15,
						'max' => 100,
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
				'selectors' => [
					'{{WRAPPER}} .team-person-email a:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'counter_email_person_tabs' );

		$control->start_controls_tab( 'counter_email_person_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'counter_email_team_person_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-email a' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'counter_email_team_person_typ',
				'selector' => '{{WRAPPER}} .team-person-email a'
			]
		);

		$control->add_responsive_control(
			'counter_email_team_person_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 40,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .team-person-email' => 'margin-bottom:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'counter_email_person_style_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_responsive_control(
			'counter_email_team_person_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-info:hover .team-person-email a' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'counter_email_team_person_typ_hv',
				'selector' => '{{WRAPPER}} .team-person-email a:hover',
			]
		);

		$control->end_controls_tabs();
		//End Email Style


		//Start Social Icons Style
		$control->add_control(
			'counter_social_icons_options',
			[
				'label' => __( 'Social Icons ', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'counter_social_icons_person_size',
			[
				'label' => __( 'Size Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 15,
						'max' => 100,
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
					'size' => 32,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .team-person-socials .socials-item-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'counter_social_icons_tabs' );

		$control->start_controls_tab( 'counter_social_icons_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'counter_social_icons_person_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-socials .socials-item-icon' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'counter_social_icons_style_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_responsive_control(
			'counter_social_icons_person_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-info:hover .team-person-socials .socials-item-icon' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->end_controls_tabs();

		$control->end_controls_section();
		//End Social Icons Style
	}



	/**
	 * Connector Styles
	 * @access protected
	 */
	protected function connector_styles( $control ) {

		$control->start_controls_section(
			'counter_connector_style',
			[
				'label' => __( 'Connector Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,

				'condition' => [
					'thegem_elementor_preset' => 'counter-preset3',
				]

			]
		);

		$control->add_control(
			'counter_connector_person_bgcolor',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-counter-container .divider-counter span' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_responsive_control(
			'counter_connector_width',
			[
				'label' => __( 'Weight', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
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
					'size' => 3,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-container .divider-counter span' => 'border-left-width: {{SIZE}}{{UNIT}}; border-right-width: 0;',
				],
			]
		);

		$control->add_responsive_control(
			'counter_connector_height',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
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
					'size' => 90,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-counter-container .divider-counter span' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->end_controls_section();

	}



	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls( $control ) {

		$this->control = $control;

		/*Container Styles*/
		$this->container_styles( $control );

		/*Numbers  Styles*/
		$this->number_styles( $control );

		/*Description  Styles*/
		$this->description_styles( $control );

		/*Image  Styles*/
		$this->icon_styles( $control );

		/*Team Person Style*/
		$this->team_person_styles( $control );

		/*Connector Style*/
		$this->connector_styles( $control );

	}


	/** Get current preset
	 * @param $val
	 * @return string
	 */
	protected function get_setting_preset($val) {

		if (empty($val)) {
			return '';
		}
		return $val;
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

		$preset = $this->get_setting_preset($settings['thegem_elementor_preset']);

		if ( empty($preset) ) return;

		if ( 'yes' === $settings['counter_animation_enabled'] ) {
			thegem_lazy_loading_enqueue();
		}

		$preset_puth = __DIR__ . '/templates/output-' . $preset . '.php';

		?>

		<div class="<?php echo $preset; ?>">
			<div class="gem-counter-container">

                <div class="preloader"><div class="preloader-spin"></div></div>

				<?php
				if ( ! empty( $preset_puth ) && file_exists( $preset_puth )) {
					include( $preset_puth );
				}
				?>
			</div>
		</div>

		<?php

		if ( is_admin() && \Elementor\Plugin::$instance->editor->is_edit_mode() ): ?>
			<script type="text/javascript">
				jQuery('.elementor-element-<?php echo $this->get_id(); ?> .gem-counter').each(function() {
					init_odometer(this);
				});
			</script>
		<?php endif;

	}


	/**
	 * Retrieve image widget link URL.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $settings
	 *
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 */
	private function get_link_url( $settings ) {

		if ( empty( $settings['counter_link' ]['url'] ) ) {
			return false;
		}

		return [
			'url'		 => $settings['counter_link' ]['url'],
			'nofollow'	=> $settings['counter_link']['nofollow'],
			'is_external' => $settings['counter_link']['is_external'],
		];
	}
}


\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_Counter());