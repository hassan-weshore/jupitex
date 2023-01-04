<?php

namespace TheGem_Elementor\Widgets\SearchBar;

use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;

if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Search Bar.
 */
class TheGem_SearchBar extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_SEARCHBAR_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_SEARCHBAR_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_SEARCHBAR_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_SEARCHBAR_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-searchbar', THEGEM_ELEMENTOR_WIDGET_SEARCHBAR_URL . '/assets/css/thegem-searchbar.css', array(), NULL);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-searchbar';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Search Bar', 'thegem');
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
		return ['thegem_elements'];
	}

	public function get_style_depends() {
		return ['thegem-searchbar'];
	}

	public function get_script_depends() {
		return [''];
	}

	/*Show reload button*/
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
			'light' => __('Light', 'thegem'),
			'dark' => __('Dark', 'thegem'),
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
		return 'dark';
	}


	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

			$this->start_controls_section(
				'section_layout',
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

			$this->end_controls_section();

			$this->start_controls_section(
				'section_content',
				[
					'label' => __('Label & Placeholder', 'thegem'),
				]
			);

			$this->add_control(
				'show_label',
				[
					'label'     => __( 'Label', 'thegem' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => '',
				]
			);

			$this->add_control(
				'label_text',
				[
					'label' => __('Label Text', 'thegem'),
					'type' => Controls_Manager::TEXT,
					'default' => __('Label', 'thegem'),
					'condition' => [
						'show_label' => 'yes',
					],
				]
			);	

			$this->add_control(
				'show_placeholder',
				[
					'label'     => __( 'Placeholder', 'thegem' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
				]
			);

			$this->add_control(
				'placeholder_text',
				[
					'label' => __('Placeholder Text', 'thegem'),
					'type' => Controls_Manager::TEXT,
					'default' => __('Search...', 'thegem'),
					'condition' => [
						'show_placeholder' => 'yes',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_button',
				[
					'label' => __('Search Button', 'thegem'),
				]
			);

			$this->add_control(
				'button_text',
				[
					'label' => __('Text', 'thegem'),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'default' => __('', 'thegem'),
				]
			);

			$this->add_control(
				'button_text_weight',
				[
					'label' => __('Text Weight', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'default' => 'thin',
					'options' => [
						'bold' => __('Bold', 'thegem'),
						'thin' => __('Thin', 'thegem'),
					],
				]
			);

			$this->add_control(
				'add_icon',
				[
					'label' => 'Add Icon',
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'button_icon',
				[
					'label' => __( 'Icon', 'thegem' ),
					'type' => Controls_Manager::ICONS,
					'default' => [
						'value' => 'fas fa-search',
						'library' => 'fa-solid',
					],
					'condition' => [
						'add_icon'	=> 'yes'
					],
				]
			);

			$this->end_controls_section();

			$this->add_styles_controls($this);

	}

	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls($control) {

		$this->control = $control;

		/* Search Bar Style */
		$this->searchbar_style($control);

		/* Bar Content Style */
		$this->bar_content_style($control);

		/* Label Style */
		$this->bar_label_style($control);

		/* Search Button Style */
		$this->search_button_style($control);
	}

	/**
	 * Search Bar Style
	 * @access protected
	 */
	protected function searchbar_style($control) {

		$control->start_controls_section(
			'searchbar_style_section',
			[
				'label' => __('Search Bar Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'bar_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['%', 'px'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form form' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'bar_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'size_units' => ['%', 'px'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-field' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .gem-search-form.gem-search-form-submit-inside .search-submit' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .gem-search-form .search-submit .search-submit-icon i' => 'line-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'bar_input_padding',
			[
				'label' => __('Input Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
				'size_units' => ['%', 'px'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-field' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// $control->add_responsive_control(
		// 	'bar_padding',
		// 	[
		// 		'label' => __('Padding', 'thegem'),
		// 		'type' => Controls_Manager::DIMENSIONS,
		// 		'size_units' => ['px', 'em', '%'],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .gem-search-form .search-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );

		$control->add_responsive_control(
			'bar_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('tabs_field_state');

		$control->start_controls_tab(
			'tab_field_normal',
			[
				'label' => __('Normal', 'thegem'),
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bar_border',
				'selector' => '{{WRAPPER}} .gem-search-form .search-field',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bar_box_shadow',
				'selector' => '{{WRAPPER}} .gem-search-form .search-field',
			]
		);

		$control->add_control(
			'bar_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-field' => 'background-color: {{VALUE}}',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'tab_field_focus',
			[
				'label' => __('Focus', 'thegem'),
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bar_focus_border',
				'selector' => '{{WRAPPER}} .gem-search-form .search-field:focus',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bar_focus_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .gem-search-form .search-field:focus',
			]
		);

		$control->add_control(
			'bar_focus_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-field:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Bar Content Style
	 * @access protected
	 */
	protected function bar_content_style($control) {

		$control->start_controls_section(
			'bar_content_style_section',
			[
				'label' => __('Bar Content Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'bar_input_heading',
			[
				'label' => __('Input Text', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bar_input_typography',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-search-form .search-field',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3				
			]
		);

		$control->add_control(
			'bar_input_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-field' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'bar_placeholder_heading',
			[
				'label' => __('Placeholder Text', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bar_placeholder_typography',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} ::placeholder',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3				
			]
		);

		$control->add_control(
			'bar_placeholder_color',
			[
				'label' => __('Placeholder Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Bar Label Style
	 * @access protected
	 */
	protected function bar_label_style($control) {

		$control->start_controls_section(
			'bar_label_style_section',
			[
				'label' => __('Label Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'label_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'row' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'column' => [
						'title' => __('Top', 'thegem'),
						'icon' => 'eicon-v-align-top',
					],
				],
				'toggle' => false,
				'default' => 'row',
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-form' => 'flex-direction: {{VALUE}};',
				],
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'label_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'flex-start' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'label_position' => 'column',
				],
				'toggle' => false,
				'default' => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .gem-search-form label' => 'align-self: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'label_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'label_position' => 'column',
				],
			]
		);

		$control->add_responsive_control(
			'label_horizontal_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					// '{{WRAPPER}} .gem-search-form label' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-search-form label' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'label_position' => 'row',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'label' => __('Typography', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-search-form label',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3
			]
		);

		$control->add_control(
			'label_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form label' => 'color: {{VALUE}}',
				],
			]
		);

		$control->end_controls_section();
	}


	/**
	 * Search Button Style
	 * @access protected
	 */
	protected function search_button_style($control) {

		$control->start_controls_section(
			'search_button_style_section',
			[
				'label' => __('Search Button Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
				'button_inside',
				[
					'label'     => __( 'Button Inside Input', 'thegem' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
				]
			);

		$control->add_responsive_control(
			'search_button_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
					'bottom' => [
						'title' => __('Bottom', 'thegem'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'toggle' => false,
				'default' => 'right',
				'render_type' => 'template',
				'selectors_dictionary' => [
					'left' => 'order: 2; margin-left: 20px;',
					'right' => 'margin-right: 20px;',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form.gem-search-form-submit-outside .search-field' => '{{VALUE}}',
				],
				'condition' => [
					'button_inside!' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'bottom_button_alignment',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'flex-start' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'button_inside!' => 'yes',
					'search_button_alignment' => 'bottom',
				],
				'toggle' => false,
				'default' => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .gem-search-form.gem-search-form-submit-bottom .search-submit' => 'align-self: {{VALUE}};',
				],
			]
		);

		

		$control->add_responsive_control(
			'bar_spacing',
			[
				'label' => __('Spacing Bottom', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '20',
					'unit' => 'px',
				],
				'condition' => [
					'button_inside!' => 'yes',
					'search_button_alignment' => 'bottom',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form.gem-search-form-submit-outside.gem-search-form-submit-bottom .search-field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'search_button_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'condition' => [
					'button_inside!' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form.gem-search-form-submit-outside:not(.gem-search-form-submit-bottom) .search-field' => 'width: calc(100% - {{SIZE}}{{UNIT}});',
					'{{WRAPPER}} .gem-search-form.gem-search-form-submit-outside .search-submit' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'search_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'search_button_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'none' => __('None', 'thegem'),
					'solid' => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'search_button_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'search_button_border_type!' => 'none',
				],
			]
		);

		$control->add_responsive_control(
			'search_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'condition' => [
					'button_inside!' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('search_button_tabs');
		$control->start_controls_tab('search_button_tab_normal', ['label' => __('Normal', 'thegem')]);

		$control->add_control(
			'search_button_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'search_button_typography',
				'selector' => '{{WRAPPER}} .gem-search-form .search-submit .search-submit-text, {{WRAPPER}} .gem-search-form .search-submit .search-submit-icon i',
				'scheme' => Schemes\Typography::TYPOGRAPHY_4,
			]
		);

		$control->add_responsive_control(
			'search_button_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'search_button_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'search_button_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-search-form .search-submit',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('search_button_tab_hover', ['label' => __('Hover', 'thegem')]);

		$control->add_control(
			'search_button_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit:hover' => 'color: {{VALUE}};',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'search_button_typography_hover',
				'selector' => '{{WRAPPER}} .gem-search-form .search-submit:hover',
				'scheme' => Schemes\Typography::TYPOGRAPHY_4,
			]
		);

		$control->add_responsive_control(
			'search_button_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'search_button_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'search_button_shadow_hover',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-search-form .search-submit:hover',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_responsive_control(
			'search_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'	=>	'before',
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_icon_alignment',
			[
				'label' => __('Icon Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'render_type' => 'template',
				'default' => 'right',
				'selectors_dictionary' => [
					'left' => 'order: 2;',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form .search-submit .search-submit-text' => '{{VALUE}}',
				],
				'condition' => [
						'add_icon'	=> 'yes',
						'button_text!'	=> ''
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_spacing',
			[
				'label' => __('Icon Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'condition' => [
						'add_icon'	=> 'yes',
						'button_text!'	=> ''
					],
				'selectors' => [
					'{{WRAPPER}} .gem-search-form.gem-search-form-submit-icon-right .search-submit .search-submit-icon' => 'margin-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .gem-search-form.gem-search-form-submit-icon-left .search-submit .search-submit-icon' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'searchbar-wrap',
			[
				'class' => [
					'gem-search-form',
					'gem-search-form-style-' . $settings['thegem_elementor_preset'],
					'gem-search-form-submit-' . ('yes' === $settings['button_inside'] ? 'inside' : 'outside'),
					('yes' != $settings['button_inside'] ? 'gem-search-form-submit-' . $settings['search_button_alignment'] : ''),
					('yes' === $settings['add_icon'] ? 'gem-search-form-submit-icon-' . $settings['button_icon_alignment'] : ''),
					]]);

		?>

		<div <?php echo $this->get_render_attribute_string('searchbar-wrap'); ?>>
			<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if ('yes' === $settings['show_label'] ) {
					echo '<label>' . $settings['label_text'] . '</label><span class="input-submit-spanned">';
				}?>
				<input class="search-field" type="search" name="s" <?php echo 'yes' === $settings['show_placeholder'] ? 'placeholder="' . $settings['placeholder_text'] : ''; ?>" />
				<button class="gem-button gem-button-size-normal gem-button-style-flat search-submit <?php echo 'gem-button-text-weight-' . $settings['button_text_weight']; ?>" type="submit">
				<?php if ($settings['button_text']) {
					 echo '<span class="search-submit-text">' . $settings['button_text'] . '</span>';
				}
				if ( ! empty( $settings['button_icon']['value'] )) { 
					echo '<span class="search-submit-icon">';
					Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] );
					echo  '</span>';}?>
				</button>
				<?php if ('yes' === $settings['show_label'] ) {
					echo '</span>';
				}?>
			</form>
		</div>

		<?php }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_SearchBar());