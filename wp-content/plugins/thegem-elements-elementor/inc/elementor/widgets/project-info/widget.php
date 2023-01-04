<?php

namespace TheGem_Elementor\Widgets\ProjectInfo;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Team.
 */
class TheGem_ProjectInfo extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */
	protected $presets;

	public $preset_elements_select;

	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		if ( ! defined('THEGEM_ELEMENTOR_WIDGET_PROJECT_INFO_DIR' )) {
			   define('THEGEM_ELEMENTOR_WIDGET_PROJECT_INFO_DIR', rtrim(__DIR__, ' /\\'));
		}

		if ( ! defined('THEGEM_ELEMENTOR_WIDGET_PROJECT_INFO_URL') ) {
			   define('THEGEM_ELEMENTOR_WIDGET_PROJECT_INFO_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-projectinfo', THEGEM_ELEMENTOR_WIDGET_PROJECT_INFO_URL . '/assets/css/thegem-projectinfo.css', array(), null);
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */

	public function get_name() {

		return 'thegem-projectinfo';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */

	public function get_title() {

		return __('Project Info', 'thegem');
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

		return [ 'thegem_portfolios' ];
	}

	public function get_style_depends() {

		return [ 'thegem-projectinfo' ];
	}

	public function get_script_depends() {

		return [];
	}

	public function is_reload_preview_required() {

		return true;
	}

	/**
	 * Create presets options for Select
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_presets_options() {

		$out = array(
			'project-1' => __('Style 1', 'thegem'),
			'project-2' => __('Style 2', 'thegem'),
		);

		return $out;
	}

	/**
	 * Get default presets options for Select
	 *
	 *
	 * @access protected
	 * @return string
	 */
	protected function set_default_presets_options() {

		return 'project-1';
	}


	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		// Sections Project Items
		$this->start_controls_section(
			'pi_items_heading',
			[
				'label' => __('Project Items', 'thegem'),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'pi_items_tabs' );

		$repeater->start_controls_tab( 'pi_items_tab_content', [ 'label' => __( 'CONTENT', 'thegem' ), ] );

		$repeater->add_control(
			'heading_block_title',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'pi_item_title',
			[
				'label' => __( 'Title Text', 'thegem' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Quick title', 'thegem' ),
				'label_block' => 'true',
			]
		);

		$repeater->add_control(
			'pi_item_title_html_tag',
			[
				'label' => __( 'Title HTML Tag', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default' => 'h6',
			]
		);

		$repeater->add_control(
			'pi_item_title_html_tag_weight',
			[
				'label' => __( 'Title Weight', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold' => __( 'Bold', 'thegem' ),
					'light' => __( 'Thin', 'thegem' ),
				],
			]
		);

		$repeater->add_control(
			'pi_item_title_html_tag_h_disable',
			[
				'label' => __( 'Disable H-tag', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'thegem' ),
				'label_off' => __( 'No', 'thegem' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$repeater->add_control(
			'heading_block_description',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'pi_item_description',
			[
				'label' => __( 'Content Text', 'thegem' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.', 'thegem' ),
			]
		);

		$repeater->add_control(
			'heading_block_link',
			[
				'label' => __( 'Item Link', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'pi_item_link',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'pi_items_tab_icon', [ 'label' => __( 'ICON', 'thegem' ), ] );

		$repeater->add_control(
			'pi_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [],
			]
		);

		$repeater->add_control(
			'content_icon_want_customize',
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
			'content_icon_color',
			[
				'label'       => __( 'Icon Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'content_icon_want_customize' => 'yes'
				],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.gem-project-info-item .icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.gem-project-info-item .icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_icon_bg_color',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', ],
				'default' => 'classic',
				'condition'   => [
					'content_icon_want_customize' => 'yes'
				],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.gem-project-info-item .icon'
			]
		);

		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_icon_border',
				'label' => __( 'Icon Border Color', 'thegem' ),
				'condition'   => [
					'content_icon_want_customize' => 'yes'
				],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.gem-project-info-item .icon',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'pi_list_items',
			[
				'label' => __( 'List Items', 'thegem' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'pi_item_title' => __( 'Client', 'thegem' ),
						'pi_item_description' => __( 'Lorem ipsum dolor sit amet, con secte tur adipi', 'thegem' ),
					],
					[
						'pi_item_title' => __( 'Category', 'thegem' ),
						'pi_item_description' => __( 'Lorem ipsum dolor sit amet, con secte tur adipi', 'thegem' ),
					],
					[
						'pi_item_title' => __( 'Tags', 'thegem' ),
						'pi_item_description' => __( 'Lorem ipsum dolor sit amet, con secte tur adipi', 'thegem' ),
					],
					[
						'pi_item_title' => __( 'Project URL', 'thegem' ),
						'pi_item_description' => __( 'Lorem ipsum dolor sit amet, con secte tur adipi', 'thegem' ),
					],
				],
				'title_field' => '{{{ pi_item_title }}}',
				'render' => 'template',
			]
		);

		$this->end_controls_section();

		// Sections Layout
		$this->start_controls_section(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'thegem_project_skin',
			[
				'label' => __('Skin', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
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
			'pi_container_style',
			[
				'label' => __('Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [],
			]
		);

		// Gaps Style 1
		$control->add_control(
			'pi_container_gaps_1',
			[
				'label'        => __( 'No Gaps', 'thegem' ),
				'type'         => Controls_Manager::SWITCHER,
				'condition'   => [
					'thegem_project_skin' => 'project-1'
				],
				'label_on'     => __( 'On', 'thegem' ),
				'label_off'    => __( 'Off', 'thegem' ),
				'return_value' => 'yes',
				'default'      => '',
				'render_type'  => 'template',
			]
		);

		// Gaps Style 2
		$control->add_control(
			'pi_container_gaps_2',
			[
				'label'        => __( 'No Gaps', 'thegem' ),
				'type'         => Controls_Manager::SWITCHER,
				'condition'   => [
					'thegem_project_skin' => 'project-2'
				],
				'label_on'     => __( 'On', 'thegem' ),
				'label_off'    => __( 'Off', 'thegem' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'render_type'  => 'template',
			]
		);

		// Bottom Spacing Style 1
		$control->add_responsive_control(
			'pi_spacing_gaps_1',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => ''
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item' => ' margin-bottom:{{SIZE}}{{UNIT}}',
				],
			]
		);

		// Bottom Spacing Style 2
		$control->add_responsive_control(
			'pi_spacing_gaps_2',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => ''
				],
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item' => ' margin-bottom:{{SIZE}}{{UNIT}}',
				],
			]
		);

		// Separator Weight Style 1
		$control->add_control(
			'pi_container_separator_weight_1',
			[
				'label' => __('Separator Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => 'yes'
				],
				'size_units' => ['px', ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item' => 'border-bottom:{{SIZE}}{{UNIT}} solid;',
				],
			]
		);

		// Separator Weight Style 2
		$control->add_control(
			'pi_container_separator_weight_2',
			[
				'label' => __('Separator Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => 'yes'
				],
				'size_units' => ['px', ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item' => 'border-bottom:{{SIZE}}{{UNIT}} solid;',
				],
			]
		);

		// Separator Color Style 1
		$control->add_control(
			'pi_container_separator_color_1',
			[
				'label'       => __( 'Separator Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => 'yes'
				],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .gem-project-info-item' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Separator Color Style 2
		$control->add_control(
			'pi_container_separator_color_2',
			[
				'label'       => __( 'Separator Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => 'yes'
				],
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .gem-project-info-item' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Radius All Style 1
		$control->add_responsive_control(
			'pi_container_radius_all_1',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => 'yes'
				],
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Radius All Style 2
		$control->add_responsive_control(
			'pi_container_radius_all_2',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => 'yes'
				],
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Radius Item Style 1
		$control->add_responsive_control(
			'pi_container_radius_item_1',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => ''
				],
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Radius Item Style 2
		$control->add_responsive_control(
			'pi_container_radius_item_2',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => ''
				],
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'pi_container_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('pi_container_tabs');

		$control->start_controls_tab('pi_container_tab_normal', ['label' => __('Normal', 'thegem'),]);

		// Background
		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'pi_container_bg_color',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'default' => 'classic',
				'selector' => '{{WRAPPER}} .gem-project-info-item'
			]
		);

		// Border Item Style 1
		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pi_container_border_item_1',
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => ''
				],
				'label' => __( 'Border Type', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-project-info-item',
			]
		);

		// Border Item Style 2
		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pi_container_border_item_2',
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => ''
				],
				'label' => __( 'Border Type', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-project-info-item',
			]
		);

		// Border All Style 1
		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pi_container_border_all_1',
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => 'yes'
				],
				'label' => __( 'Border Type', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-project-info',
			]
		);

		// Border All Style 2
		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pi_container_border_all_2',
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => 'yes'
				],
				'label' => __( 'Border Type', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-project-info',
			]
		);

		// Shadow All Style 1
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pi_container_shadow_all_1',
				'label' => __( 'Shadow', 'thegem' ),
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => ''
				],
				'selector' => '{{WRAPPER}} .gem-project-info-item',
			]
		);

		// Shadow All Style 2
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pi_container_shadow_all_2',
				'label' => __( 'Shadow', 'thegem' ),
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => ''
				],
				'selector' => '{{WRAPPER}} .gem-project-info-item',
			]
		);

		// Shadow Item Style 1
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pi_container_shadow_item_1',
				'label' => __( 'Shadow', 'thegem' ),
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => 'yes'
				],
				'selector' => '{{WRAPPER}} .gem-project-info',
			]
		);

		// Shadow Item Style 2
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pi_container_shadow_item_2',
				'label' => __( 'Shadow', 'thegem' ),
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => 'yes'
				],
				'selector' => '{{WRAPPER}} .gem-project-info',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('pi_container_tab_hover', ['label' => __('Hover', 'thegem'),]);

		// Background
		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'pi_container_bg_color_hv',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'default' => 'classic',
				'selector' => '{{WRAPPER}} .gem-project-info-item:before'
			]
		);

		// Border All Style 1
		$control->add_control(
			'pi_container_border_all_1_hv',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => 'yes'
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info:hover' => 'border-color:{{VALUE}};',
				],
			]
		);

		// Border All Style 2
		$control->add_control(
			'pi_container_border_all_2_hv',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => 'yes'
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info:hover' => 'border-color:{{VALUE}};',
				],
			]
		);

		// Border Item Style 1
		$control->add_control(
			'pi_container_border_item_1_hv',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => ''
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item:hover' => 'border-color:{{VALUE}};',
				],
			]
		);

		// Border Item Style 2
		$control->add_control(
			'pi_container_border_item_2_hv',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => ''
				],
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item:hover' => 'border-color:{{VALUE}};',
				],
			]
		);

		// Shadow Item Style 1
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pi_container_shadow_item_1_hv',
				'label' => __( 'Shadow', 'thegem' ),
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => ''
				],
				'selector' => '{{WRAPPER}} .gem-project-info-item:hover',
			]
		);

		// Shadow Item Style 2
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pi_container_shadow_item_2_hv',
				'label' => __( 'Shadow', 'thegem' ),
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => ''
				],
				'selector' => '{{WRAPPER}} .gem-project-info-item:hover',
			]
		);

		// Shadow All Style 1
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pi_container_shadow_all_1_hv',
				'label' => __( 'Shadow', 'thegem' ),
				'condition'   => [
					'thegem_project_skin' => 'project-1',
					'pi_container_gaps_1' => 'yes'
				],
				'selector' => '{{WRAPPER}} .gem-project-info:hover',
			]
		);

		// Shadow All Style 2
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pi_container_shadow_all_2_hv',
				'label' => __( 'Shadow', 'thegem' ),
				'condition'   => [
					'thegem_project_skin' => 'project-2',
					'pi_container_gaps_2' => 'yes'
				],
				'selector' => '{{WRAPPER}} .gem-project-info:hover',
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * Title & Description Styles
	 * @access protected
	 */
	protected function title_description_styles( $control ) {

		$control->start_controls_section(
			'pi_title_description_style',
			[
				'label' => __('Title & Description Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		// Title
		$control->add_control(
			'pi_title_description_title',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('pi_title_tabs');

		$control->start_controls_tab('pi_title_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'pi_title_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item .gem-wrapper-project-info .title' => 'margin-bottom:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'pi_title_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item .gem-wrapper-project-info .title' => 'color:{{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'pi_title_typ',
				'selector' => '{{WRAPPER}} .gem-project-info-item .gem-wrapper-project-info .title',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('pi_title_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_responsive_control(
			'pi_title_spacing_hv',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item:hover .gem-wrapper-project-info .title' => 'margin-bottom:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'pi_title_color_hv',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item:hover .gem-wrapper-project-info .title' => 'color:{{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'pi_title_typ_hv',
				'selector' => '{{WRAPPER}} .gem-project-info-item:hover .gem-wrapper-project-info .title',
			]
		);

		$control->end_controls_tabs();

		// Description
		$control->add_control(
			'pi_title_description_description',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('pi_title_description_1_tabs');

		$control->start_controls_tab('pi_title_description_1_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
			'pi_description_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item .gem-wrapper-project-info .description' => 'color:{{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'pi_decription_typ',
				'selector' => '{{WRAPPER}} .gem-project-info-item .gem-wrapper-project-info .description',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('pi_title_description_1_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'pi_description_color_hv',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item:hover .gem-wrapper-project-info .description' => 'color:{{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'pi_decription_typ_hv',
				'selector' => '{{WRAPPER}} .gem-project-info-item:hover .gem-wrapper-project-info .description',
			]
		);

		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * Icon Styles
	 * @access protected
	 */
	protected function icon_styles( $control ) {

		$control->start_controls_section(
			'pi_icon_style',
			[
				'label' => __('Icon Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'pi_icon_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item .icon i' => 'font-size:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .gem-project-info-item .icon.gem-svg-icon svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'pi_icon_spacing-top',
			[
				'label' => __('Vertical Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition'   => [
					'thegem_project_skin' => 'project-1'
				],
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'rem' => [
						'min' => -100,
						'max' => 100,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item .icon' => ' margin-top:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'pi_icon_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
				/*'default' => [
					'size' => 5,
					'unit' => 'px',
				],*/
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item .icon i' => ' padding:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .gem-project-info-item .icon.gem-svg-icon' => ' padding:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'pi_icon_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$control->add_responsive_control(
			'pi_icon_right_spacing',
			[
				'label' => __('Right Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item .gem-wrapper-project-info' => ' padding-left:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'pi_icon_shadow_project-1',
				'label' => __( 'Shadow', 'thegem' ),
				'condition'   => [
					'thegem_project_skin' => 'project-1'
				],
				'fields_options' => [
					'text_shadow' => [
						'selectors' => [
							'{{WRAPPER}} .gem-project-info-item .icon i' => 'text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
							'{{WRAPPER}} .gem-project-info-item .icon svg' => 'filter: drop-shadow({{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}});',
						],
					]
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pi_icon_shadow_project-2',
				'condition'   => [
					'thegem_project_skin' => 'project-2'
				],
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-project-info-item .icon',
			]
		);

		$control->start_controls_tabs('pi_icon_tabs');

		$control->start_controls_tab('pi_icon_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'pi_icon_bg_color',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'default' => 'classic',
				'selector' => '{{WRAPPER}} .gem-project-info-item .icon'
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pi_icon_border',
				'label' => __( 'Border Type', 'thegem' ),
				'condition' => [],
				'selector' => '{{WRAPPER}} .gem-project-info-item .icon',
			]
		);

		$control->add_control(
			'pi_icon_color',
			[
				'label'       => __( 'Icon Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .gem-project-info-item .icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-project-info-item .icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'pi_icon_rotate',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
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
					'{{WRAPPER}} .gem-project-info-item .icon i' => 'transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .gem-project-info-item:hover .icon svg' => 'transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('pi_icon_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'pi_icon_bg_color_hv',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'default' => 'classic',
				'selector' => '{{WRAPPER}} .gem-project-info-item .icon:before'
			]
		);

		$control->add_control(
			'pi_icon_border_hv',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-project-info-item:hover .icon' => 'border-color:{{VALUE}};',
				],
			]
		);

		$control->add_control(
			'pi_icon_color_hv',
			[
				'label'       => __( 'Icon Color', 'thegem' ),
				'type'        => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => [
					'{{WRAPPER}} .gem-project-info-item:hover .icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-project-info-item:hover .icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'pi_icon_rotate_hv',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
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
					'{{WRAPPER}} .gem-project-info-item:hover .icon i' => 'transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .gem-project-info-item:hover .icon svg' => 'transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->end_controls_tabs();

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

		/*Title & Description Styles*/
		$this->title_description_styles( $control );

		/*Icon Styles*/
		$this->icon_styles( $control );

	}


	/** Get current preset
	 * @param $val
	 * @return string
	 */
	protected function get_setting_preset( $val ) {

		if ( empty($val) ) {
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

		$preset = $this->get_setting_preset($settings['thegem_project_skin']);

		$class_project_style = ( $preset === 'project-1') ? 'gem-project-info-style-1' : 'gem-project-info-style-2';

		$gem_gaps =  ( 'yes' === $settings['pi_container_gaps_1'] || 'yes' === $settings['pi_container_gaps_2'] ) ? 'gem-gaps' : '';

		$gem_gaps_container = ( 'yes' === $settings['pi_container_gaps_1'] || 'yes' === $settings['pi_container_gaps_2'] ) ? 'gem-gaps-container' : '';

		if ( empty($preset) ) return;

		$preset_path = __DIR__ . '/templates/output-project.php';

		if ( ! empty( $preset_path ) && file_exists( $preset_path ) ) {

			include( $preset_path );
		}
	}

}

Plugin::instance()->widgets_manager->register_widget_type( new TheGem_ProjectInfo() );