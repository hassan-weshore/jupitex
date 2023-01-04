<?php
namespace TheGem_Elementor\Widgets\Accordion;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Controls_Stack;
use Elementor\Control_Media;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Core\Schemes;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Accordion.
 */
class TheGem_Accordion extends Widget_Base
{
	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */
	protected $presets;

	public function __construct($data = [], $args = null)
	{
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_ACCORDION_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_ACCORDION_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_ACCORDION_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_ACCORDION_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-accordion',
			THEGEM_ELEMENTOR_WIDGET_ACCORDION_URL . '/assets/css/thegem-accordion.css',
			array(),
			null
		);

		wp_register_script('thegem-tta', THEGEM_ELEMENTOR_WIDGET_ACCORDION_URL . '/assets/js/thegem-tta.js', array('jquery'), null, true);

		wp_register_script(
			'thegem-accordion',
			THEGEM_ELEMENTOR_WIDGET_ACCORDION_URL . '/assets/js/thegem-accordion.js',
			array('thegem-tta'),
			null,
			true
		);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-accordion';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Accordion', 'thegem');
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
		return ['thegem-accordion'];
	}

	public function get_script_depends()
	{
		return ['thegem-accordion'];
	}

	/*Show reload button*/
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
	protected function get_presets_options()
	{
		$out = array(
			'classic' => __('Classic', 'thegem'),
			'modern' => __('Modern', 'thegem'),
			'flat' => __('Flat', 'thegem'),
			'outline' => __('Outline', 'thegem'),
			'simple_solid' => __('Simple solid', 'thegem'),
			'simple_dashed' => __('Simple dashed', 'thegem'),
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
	protected function set_default_presets_options()
	{
		return 'classic';
	}

	/**
	 * Get the list of all local section templates
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_local_section_templates()
	{
		$items = Plugin::instance()->templates_manager->get_source('local')->get_items();
		if (!empty($items)) {
			$items = wp_list_filter($items, ['type' => 'section']);
			$items = wp_list_pluck($items, 'title', 'template_id');
			return $items;
		}
		return [];
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls()
	{
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'thegem_elementor_preset',
			[
				'label'   => __('Skin', 'thegem'),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_accordion',
			[
				'label' => __('Accordion', 'thegem'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Title', 'thegem'),
				'default' => __('Accordion Title', 'thegem'),
				'placeholder' => __('Type Accordion Title', 'thegem'),
			]
		);

		$repeater->add_control(
			'section_id',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Section ID', 'thegem'),
				'placeholder' => __('Type unique ID here', 'thegem'),
			]
		);

		$repeater->add_control(
			'icon',
			[
				'type' => Controls_Manager::ICONS,
				'label' => __('Icon', 'thegem'),
				'show_label' => false,
				'default' => [
					'library' => 'fa-solid',
					'value' => 'fas fa-bell'
				],
			]
		);

		$repeater->add_control(
			'source',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __('Content Source', 'thegem'),
				'default' => 'editor',
				'separator' => 'before',
				'options' => [
					'editor' => __('Editor', 'thegem'),
					'template' => __('Template', 'thegem'),
				]
			]
		);

		$repeater->add_control(
			'editor',
			[
				'label' => __('Content Editor', 'thegem'),
				'show_label' => false,
				'type' => Controls_Manager::WYSIWYG,
				'condition' => [
					'source' => 'editor',
				],
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore <br><br>et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
			]
		);

		$repeater->add_control(
			'template',
			[
				'label' => __('Section Template', 'thegem'),
				'placeholder' => __('Select a section template for as tab content', 'thegem'),
				'label_block' => true,
				'description' => sprintf(
					__('Wondering what is section template or need to create one? Please click %1$shere%2$s ', 'thegem'),
					'<a target="_blank" href="' . esc_url(admin_url('/edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=section')) . '">',
					'</a>'
				),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_local_section_templates(),
				'condition' => [
					'source' => 'template',
				]
			]
		);

		$this->add_control(
			'tabs',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{title}}',
				'default' => [
					[
						'title' => 'Accordion Item 1',
						'source' => 'editor',
					],
					[
						'title' => 'Accordion Item 2',
						'source' => 'editor',
					],
					[
						'title' => 'Accordion Item 3',
						'source' => 'editor',
					]
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'_section_options',
			[
				'label' => __('Options', 'thegem'),
			]
		);

		$this->add_control(
			'closed_icon',
			[
				'type' => Controls_Manager::ICONS,
				'label' => __('Closed Icon', 'thegem'),
				'default' => [
					'library' => 'fa-solid',
					'value' => 'fas fa-plus'
				],
			]
		);

		$this->add_control(
			'opened_icon',
			[
				'type' => Controls_Manager::ICONS,
				'label' => __('Opened Icon', 'thegem'),
				'default' => [
					'library' => 'fa-solid',
					'value' => 'fas fa-minus'
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
	public function add_styles_controls($control)
	{
		$this->control = $control;

		/*Item Container*/
		$this->item_container($control);

		/*Item Title*/
		$this->item_title($control);

		/*Closed Item*/
		$this->closed_item($control);

		/*Item Content*/
		$this->item_content($control);
	}

	/**
	 * Icon Styles
	 * @access protected
	 */
	protected function item_container($control)
	{
		$control->start_controls_section(
			'item_container_style',
			[
				'label' => __('Item Container', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'v_spacing',
			[
				'label' => __('Vertical Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel:not(:first-child)' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_section',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'background-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading, {{WRAPPER}} .gem-tta-panel .gem-tta-panel-body',
			]
		);

		$control->remove_control('background_section_image');

		$control->add_control(
			'section_separator_color',
			[
				'label' => __('Separator Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel + .gem-tta-panel' => 'border-top-color: {{VALUE}}!important;',
				],
			]
		);

		$control->add_responsive_control(
			'separator_width',
			[
				'label' => __('Separator Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel + .gem-tta-panel' => 'border-top-width: {{SIZE}}px!important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'section_border',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0px {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-body' => 'border-width: 0px {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta-panel.gem-tta-active+.gem-tta-panel .gem-tta-panel-heading,
							{{WRAPPER}} .gem-tta-panel:not(:first-child) .gem-tta-panel-heading' => 'margin-top: -{{TOP}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading,
							{{WRAPPER}} .gem-tta-panel:not(:last-child) .gem-tta-panel-heading' => 'margin-bottom: -{{BOTTOM}}{{UNIT}}!important;',
						],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading, {{WRAPPER}} .gem-tta-panel .gem-tta-panel-body',
			]
		);

		$control->add_responsive_control(
			'section_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0px 0px!important;',
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-body' => 'border-radius: 0px 0px {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_dashed',
						],
					],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-tta-panel.gem-tta-active',
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Item Title
	 * @access protected
	 */
	protected function item_title($control)
	{
		$control->start_controls_section(
			'item_title_style',
			[
				'label' => __('Item Title', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'item_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel .gem-tta-panel-title > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_title',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'background-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading',
			]
		);

		$control->remove_control('background_title_image');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_border',
				'label' => __('Bottom Border Type', 'thegem'),
				'fields_options' => [
					'border' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-top-style: {{VALUE}}!important;',
						],
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-body',
			]
		);

		$control->remove_control('item_border_color');

		$control->remove_control('item_border_width');

		$control->add_responsive_control(
			'item_bottom_border_width',
			[
				'label' => __('Bottom Border Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-body' => 'border-top-width: {{SIZE}}px!important;',
				],
			]
		);

		$control->add_control(
			'item_bottom_border_color',
			[
				'label' => __('Separator Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-body' => 'border-top-color: {{VALUE}}!important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'simple_item_border',
				'label' => __('Bottom Border Type', 'thegem'),
				'fields_options' => [
					'border' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-top-style: {{VALUE}}!important;',
						],
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-panel + .gem-tta-panel',
			]
		);

		$control->remove_control('simple_item_border_color');

		$control->remove_control('simple_item_border_width');

		$control->add_control(
			'title_heading',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'title_position',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left' => 'justify-content: flex-start;',
					'center' => 'justify-content: center;',
					'right' => 'justify-content: flex-end;',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel .gem-tta-panel-title > a' => '{{VALUE}}',
				],
				'toggle' => false,
				'default' => 'left',
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'title_typo',
				'selector' => '{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-title-text',
			]
		);

		$control->add_control(
			'title_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-title > a .gem-tta-title-text' => 'color: {{VALUE}}!important;',					
				],
			]
		);

		$control->add_control(
			'title_icon_heading',
			[
				'label' => __('Title Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'title_icon_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel .gem-tta-icon-left' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel .gem-tta-icon-right' => 'margin-left: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'title_icon_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-icon i' => 'font-size: {{SIZE}}px; line-height: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-title-text' => 'line-height: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-icon svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);

		$control->add_control(
			'title_icon_color',
			[
				'label' => __('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-icon i:before' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-icon svg' => 'fill: {{VALUE}}!important;',
					
				],
			]
		);

		$control->add_responsive_control(
			'icon_position',
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
				],
				'toggle' => false,
				'default' => 'left',
			]
		);

		$control->add_control(
			'icon_heading',
			[
				'label' => __('Open Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'closed_icon_position',
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
				],
				'toggle' => false,
				'default' => 'left',
			]
		);

		$control->add_responsive_control(
			'icon_spacing',
			[
				'label' => __('Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-controls-icon-position-right .gem-tta-controls-icon i,
					{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-controls-icon-position-right .gem-tta-controls-icon svg' => 'right: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-controls-icon-position-left .gem-tta-controls-icon i,
					{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-controls-icon-position-left .gem-tta-controls-icon svg' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-controls-icon-position-right .gem-tta-controls-icon i,
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-controls-icon-position-right .gem-tta-controls-icon svg' => 'right: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-controls-icon-position-left .gem-tta-controls-icon i,
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-controls-icon-position-left .gem-tta-controls-icon svg' => 'left: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'icon_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-controls-icon i' => 'font-size: {{SIZE}}px; line-height: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-controls-icon svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-controls-icon i' => 'font-size: {{SIZE}}px; line-height: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-controls-icon svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);

		$control->add_control(
			'icon_color',
			[
				'label' => __('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-controls-icon-opened i:before' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-controls-icon-opened svg' => 'fill: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Closed Item
	 * @access protected
	 */
	protected function closed_item($control)
	{
		$control->start_controls_section(
			'closed_item_style',
			[
				'label' => __('Closed Item', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->start_controls_tabs('closed_tabs');
		$control->start_controls_tab('closed_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_closed_normal',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'background-color: {{VALUE}};',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading',
			]
		);

		$control->remove_control('background_closed_normal_image');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'closed_border_normal',
				'label' => __('Border', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading',
			]
		);

		$control->add_responsive_control(
			'closed_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_dashed',
						],
					],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'closed_box_shadow',
				'label' => __('Shadow', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading',
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('closed_tabs_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_closed_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading:hover,
				{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading:focus',
			]
		);

		$control->remove_control('background_closed_hover_image');

		$control->add_control(
			'border_closed_color_hover',
			[
				'label' => __('Border Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_solid',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'simple_dashed',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading:hover,
				{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'closed_title_heading',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('title_tabs');
		$control->start_controls_tab('title_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'title_typo_normal',
				'selector' => '{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-title-text',
			]
		);

		$control->add_control(
			'title_color_normal',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('title_tabs_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'title_typo_hover',
				'selector' => '{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:focus .gem-tta-title-text, {{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:hover .gem-tta-title-text',
			]
		);

		$control->add_control(
			'title_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:focus, {{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:hover' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'title_open_icon_heading',
			[
				'label' => __('Title Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('color_tabs');
		$control->start_controls_tab('color_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
			'icon_color_normal',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a .gem-tta-icon i' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a .gem-tta-icon svg' => 'fill: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('color_tabs_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'icon_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:hover .gem-tta-icon i, 
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:focus .gem-tta-icon i' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:hover .gem-tta-icon svg, 
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:focus .gem-tta-icon svg' => 'fill: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'closed_icon_heading',
			[
				'label' => __('Closed Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('closed_color_tabs');
		$control->start_controls_tab('closed_color_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
			'icon_closed_color_normal',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a .gem-tta-controls-icon-closed i' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a .gem-tta-controls-icon-closed svg' => 'fill: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('closed_color_tabs_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'icon_closed_color_hover',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:hover .gem-tta-controls-icon-closed i, 
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:focus .gem-tta-controls-icon-closed i' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:hover .gem-tta-controls-icon-closed svg, 
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-title > a:focus .gem-tta-controls-icon-closed svg' => 'fill: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Item Content
	 * @access protected
	 */
	protected function item_content($control)
	{
		$control->start_controls_section(
			'item_content_style',
			[
				'label' => __('Item Content', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'item_content_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-tta.gem-tta-general.gem-tta-accordion .gem-tta-panel-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'item_content_typo',
				'selector' => '{{WRAPPER}} .gem-tta-panel-body',
			]
		);

		$control->add_control(
			'item_content_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel-body' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'item_content_position',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left' => 'text-align: left!important;',
					'center' => 'text-align: center!important;',
					'right' => 'text-align: right!important;',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-panel-body' => '{{VALUE}}',
				],
				'toggle' => false,
				'default' => 'left',
			]
		);

		$control->end_controls_section();
	}

	/** Get current preset
	 * @param $val
	 * @return string
	 */
	protected function get_setting_preset($val)
	{
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
	public function render()
	{
		$settings = $this->get_settings_for_display();

		$preset = $this->get_setting_preset($settings['thegem_elementor_preset']);

		if ( empty($preset) ) return;

        $this->add_render_attribute( 'accordion-wrap', 'class', 
		[
			'gem-tta-general',
			'gem-tta',
			'gem-tta-accordion',
			'gem-tta-color-thegem',
			'gem-tta-style-' . $preset,
			'gem-tta-shape-rounded',
			'gem-tta-o-shape-group',
			'gem-tta-controls-align-' . $settings['title_position'],
		]);

		$id_str = $this->get_id();

		?>
<div class="gem-tta-container" data-vc-action="collapse">
	<div <?php echo $this->get_render_attribute_string( 'accordion-wrap' ); ?>>
	    <div class="gem-tta-panels-container">
	        <div class="gem-tta-panels">

	        	<?php foreach ( $settings['tabs'] as $index => $item ) :
	                $count = $index + 1;

	                $title_setting_key = $this->get_repeater_setting_key( 'title', 'tabs', $index );
					// $this->add_inline_editing_attributes( $title_setting_key );

	                if ( $item['source'] === 'editor' ) {
	                    $content_setting_key = $this->get_repeater_setting_key( 'editor', 'tabs', $index );
	                    $this->add_inline_editing_attributes( $content_setting_key, 'advanced' );
	                } else {
	                    $content_setting_key = $this->get_repeater_setting_key( 'section', 'tabs', $index );
					}
					
					$section_id = ! empty( $item['section_id']) ? $item['section_id'] : 'section-' . $id_str . '-' . $count;

	                $this->add_render_attribute( $title_setting_key, [
	                    'id' => $section_id,
	                    'class' => [ 'gem-tta-panel', ($index === 0 ? 'gem-tta-active' : '') ],
	                    'data-vc-content' => '.gem-tta-panel-body',
	                ] );

	                $this->add_render_attribute( $content_setting_key, 'class', 'gem-tta-panel-body');

	                ?>

	            	<div <?php echo $this->get_render_attribute_string( $title_setting_key ); ?>>
	                    <div class="gem-tta-panel-heading">
	                    	<h4 class="gem-tta-panel-title gem-tta-controls-icon-position-<?php echo $settings['closed_icon_position']; ?>">
	                    		<a href="#<?php echo $section_id; ?>" data-vc-accordion data-vc-container=".gem-tta-container">

	                    			<?php if ( ! empty( $item['icon']['value'] ) && 'right' !== $settings['icon_position'] ) { ?>
	                    				<span class="gem-tta-icon gem-tta-icon-left">
										 	<?php Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true'] );?>
										</span>
									<?php } ?>

	                    			<span class="gem-tta-title-text">
	                    				<?php echo wp_kses( $item[ 'title' ], 'post' ); ?>
	                    			</span>								

									<?php if ( ! empty( $item['icon']['value'] ) && 'right' === $settings['icon_position'] ) { ?>
	                    				<span class="gem-tta-icon gem-tta-icon-right">
										 	<?php Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true'] );?>
										</span>
									<?php } ?>
									
									<span class="gem-tta-controls-icon gem-tta-controls-icon-opened">
										<?php if ( ! empty( $settings['opened_icon']['value'] ) ) { Icons_Manager::render_icon( $settings['opened_icon'], [ 'aria-hidden' => 'true'] ); }?>
									</span>
									<span class="gem-tta-controls-icon gem-tta-controls-icon-closed">
										<?php if ( ! empty( $settings['closed_icon']['value'] ) ) { Icons_Manager::render_icon( $settings['closed_icon'], [ 'aria-hidden' => 'true' ] ); }?>
									</span>
	                    		</a>
	                    	</h4>
	                    </div>
	                    <div <?php echo $this->get_render_attribute_string( $content_setting_key ); ?>>
				                    <?php
				                        if ( $item['source'] === 'editor' ) :
				                            echo '<div class="gem-text-output">' . $this->parse_text_editor( $item['editor'] ) . '</div>';
				                        elseif ( $item['source'] === 'template' && $item['template'] ) :
				                            echo Plugin::instance()->frontend->get_builder_content_for_display( $item['template'] );
				                            if ( is_admin() && Plugin::$instance->editor->is_edit_mode() ) {
												$link = add_query_arg(
													array(
														'elementor' => '',
													),
													get_permalink( $item['template'] )
												);
												echo sprintf( '<a class="gem-tta-template-edit gem-button gem-button-size-small gem-button-style-flat gem-button-text-weight-thin" data-tta-template-edit-link="%s">%s</a>', $link, esc_html__( 'Edit Template', 'thegem' ) );
											}			                            
				                        endif;
				                    ?>
				                </div>
				            </div>
	            <?php endforeach; ?>
	        </div>
	    </div>
    </div>
</div>
			<?php	
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new TheGem_Accordion());
