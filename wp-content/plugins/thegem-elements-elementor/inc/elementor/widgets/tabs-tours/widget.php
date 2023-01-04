<?php
namespace TheGem_Elementor\Widgets\Tabs;

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
 * Elementor widget for Tabs.
 */
class TheGem_Tabs extends Widget_Base
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

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TABS_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_TABS_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_TABS_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_TABS_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-tabs',
			THEGEM_ELEMENTOR_WIDGET_TABS_URL . '/assets/css/thegem-tabs.css',
			array('thegem-accordion'),
			null
		);

		wp_register_script('thegem-tabs', THEGEM_ELEMENTOR_WIDGET_TABS_URL . '/assets/js/thegem-tabs.js', array('thegem-accordion'), null, true);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-tabs';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Tabs & Tours', 'thegem');
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
		return ['thegem-tabs'];
	}

	public function get_script_depends()
	{
		return ['thegem-tabs'];
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
			'section_tabs',
			[
				'label' => __('Tabs', 'thegem'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Title', 'thegem'),
				'default' => __('Tab Title', 'thegem'),
				'placeholder' => __('Type Tab Title', 'thegem'),
			]
		);

		$repeater->add_control(
			'section_id',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Section ID', 'thegem'),
                // 'default' => __( 'Section ID', 'thegem' ),
				'placeholder' => __('Type unique ID here', 'thegem'),
			]
		);

		$repeater->add_control(
			'icon',
			[
				'type' => Controls_Manager::ICONS,
				'label' => __('Icon', 'thegem'),
				'show_label' => false,
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
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore <br><br>et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
				'condition' => [
					'source' => 'editor',
				]
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
				'render_type' => 'template',
				'default' => [
					[
						'title' => 'Tab Item 1',
						'source' => 'editor',
						'icon' => [
							'library' => 'fa-solid',
							'value' => 'fas fa-bell'
						],
					],
					[
						'title' => 'Tab Item 2',
						'source' => 'editor',
						'icon' => [
							'library' => 'fa-solid',
							'value' => 'fas fa-bell'
						],
					]
				]
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

		/*Title Container*/
		$this->title_container($control);

		/*Title Style*/
		$this->title_style($control);

		/*Content Container*/
		$this->content_container($control);

		/*Content Style*/
		$this->content_style($control);
	}

	/**
	 * Title Container
	 * @access protected
	 */
	protected function title_container($control)
	{
		$control->start_controls_section(
			'title_container_style',
			[
				'label' => __('Title Container', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'title_container_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __('Top', 'elementor'),
						'icon' => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __('Bottom', 'elementor'),
						'icon' => 'eicon-v-align-bottom',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'default' => 'top',
			]
		);

		$control->remove_control('title_container_position_mobile');

		$control->add_responsive_control(
			'title_container_alignment_h',
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
					'stretch' => [
						'title' => __('Stretch', 'elementor'),
						'icon' => 'eicon-h-align-stretch',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'top',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'bottom',
						],
					],
				],
				'toggle' => false,
				'default' => 'left',
			]
		);

		$control->remove_control('title_container_alignment_h_mobile');

		$control->add_responsive_control(
			'title_container_alignment_v',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Top', 'thegem'),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-v-align-middle',
					],
					'stretch' => [
						'title' => __('Stretch', 'elementor'),
						'icon' => 'eicon-v-align-stretch',
					],
					'right' => [
						'title' => __('Bottom', 'thegem'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'left',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'right',
						],
					],
				],
				'toggle' => false,
				'default' => 'left',
			]
		);

		$control->remove_control('title_container_alignment_v_mobile');

		$control->add_responsive_control(
			'title_gap',
			[
				'label' => __('Gap', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'thegem'),
				'label_off' => __('No', 'thegem'),
				'frontend_available' => true,
				'return_value' => 'yes'
			]
		);

		$control->remove_control('title_gap_mobile');

		$control->add_responsive_control(
			'title_container_radius_gap',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-gap .gem-tta-tab>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_radius_gap_mobile');

		$control->add_responsive_control(
			'title_container_radius_nogap_left',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['top', 'left'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'left',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap .gem-tta-tab>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_radius_nogap_left_mobile');

		$control->add_responsive_control(
			'title_container_radius_nogap_right',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['right', 'bottom'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'right',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap .gem-tta-tab>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_radius_nogap_right_mobile');

		$control->add_responsive_control(
			'title_container_radius_nogap_top',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['right', 'top'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'top',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap .gem-tta-tab>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_radius_nogap_top_mobile');

		$control->add_responsive_control(
			'title_container_radius_nogap_bottom',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['left', 'bottom'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'bottom',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap .gem-tta-tab>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_radius_nogap_bottom_mobile');

		$control->add_responsive_control(
			'title_container_margin_gap',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tabs.gem-tta-general .gem-tta-tab > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_margin_gap_mobile');

		$control->add_responsive_control(
			'title_container_margin_nogap_top',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['left', 'bottom', 'right'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'bottom',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tabs.gem-tta-general .gem-tta-tab > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_margin_nogap_top_mobile');

		$control->add_responsive_control(
			'title_container_margin_nogap_bottom',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['left', 'top', 'right'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'top',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tabs.gem-tta-general .gem-tta-tab > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_margin_nogap_bottom_mobile');

		$control->add_responsive_control(
			'title_container_margin_nogap_left',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['bottom', 'top', 'right'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'right',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tabs.gem-tta-general .gem-tta-tab > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_margin_nogap_left_mobile');

		$control->add_responsive_control(
			'title_container_margin_nogap_right',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['bottom', 'top', 'left'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'left',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tabs.gem-tta-general .gem-tta-tab > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_margin_nogap_right_mobile');

		$control->add_responsive_control(
			'title_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tabs.gem-tta-general .gem-tta-tab > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->remove_control('title_container_padding_mobile');

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
		$control->remove_control('v_spacing');
		$control->remove_control('v_spacing_tablet');
		

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'title_container_box_shadow',
				'label' => __('Shadow', 'thegem'),
				'fields_options' => [
					'box_shadow' => [
						'selectors' => [
							'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-tab:not(.gem-tta-active)>a, {{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-tab.gem-tta-active>a,
				{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel-heading',
			]
		);

		$control->start_controls_tabs('title_container_tabs');
		$control->start_controls_tab('title_container_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_container_background_normal',
				'label' => __('Background', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'background-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tab:not(.gem-tta-active)>a, 
				{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading',
			]
		);

		$control->remove_control('title_container_background_normal_image');
		$control->remove_control('title_container_background_normal_image_tablet');
		$control->remove_control('title_container_background_normal_image_mobile');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'title_container_border_normal',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading' => 
							'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important; margin-top: unset;',
						],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tab:not(.gem-tta-active)>a, {{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading',
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('title_container_tabs_active', ['label' => __('Active', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_container_background_active',
				'label' => __('Background', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'background-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tab.gem-tta-active>a, 
				{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading',
			]
		);

		$control->remove_control('title_container_background_active_image');
		$control->remove_control('title_container_background_active_image_tablet');
		$control->remove_control('title_container_background_active_image_mobile');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'title_container_border_active_gap',
				'label' => __('Border', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 
							'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important; margin-bottom: unset;',
						],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tab.gem-tta-active>a',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'title_container_border_active_nogap_top',
				'label' => __('Border', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'top',
						],
					],
				],
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0px {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta-tabs .gem-tta-tabs-container .gem-tta-active a span' => 'padding-bottom: {{TOP}}px!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 
							'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important; margin-bottom: unset;',
						],
						// 'allowed_dimensions' => ['left', 'top', 'right'],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tab.gem-tta-active>a',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'title_container_border_active_nogap_bottom',
				'label' => __('Border', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'bottom',
						],
					],
				],
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: 0px {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta-tabs .gem-tta-tabs-container .gem-tta-active a span' => 'padding-top: {{BOTTOM}}px!important;',	
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 
							'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important; margin-bottom: unset;',
						],
						// 'allowed_dimensions' => ['left', 'bottom', 'right'],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tab.gem-tta-active>a',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'title_container_border_active_nogap_left',
				'label' => __('Border', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'left',
						],
					],
				],
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} 0px {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 
							'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important; margin-bottom: unset;',
						],
						// 'allowed_dimensions' => ['left', 'bottom', 'top'],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tab.gem-tta-active>a',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'title_container_border_active_nogap_right',
				'label' => __('Border', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'right',
						],
					],
				],
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0px!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 
							'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important; margin-bottom: unset;',
						],
						// 'allowed_dimensions' => ['right', 'bottom', 'top'],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
							'{{WRAPPER}} .gem-tta.gem-tta-general .gem-tta-panels .gem-tta-panel.gem-tta-active .gem-tta-panel-heading' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tab.gem-tta-active>a',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Title Style
	 * @access protected
	 */
	protected function title_style($control)
	{
		$control->start_controls_section(
			'title_style',
			[
				'label' => __('Title Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'title_heading',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('title_style_tabs');
		$control->start_controls_tab('title_style_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'title_typo_normal',
				'selector' => '{{WRAPPER}} .gem-tta-tab:not(.gem-tta-active) a .gem-tta-title-text, 
				{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading a .gem-tta-title-text',
			]
		);

		$control->add_control(
			'title_color_normal',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tab:not(.gem-tta-active) a .gem-tta-title-text, 
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading a .gem-tta-title-text' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('title_style_tabs_active', ['label' => __('Active', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'title_typo_active',
				'selector' => '{{WRAPPER}} .gem-tta-tab.gem-tta-active a .gem-tta-title-text, 
				{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading a .gem-tta-title-text',
			]
		);

		$control->add_control(
			'title_color_active',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tab.gem-tta-active a .gem-tta-title-text, 
					{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading a .gem-tta-title-text' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'icon_heading',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'title_icon_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __('Top', 'elementor'),
						'icon' => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __('Bottom', 'elementor'),
						'icon' => 'eicon-v-align-bottom',
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

		$control->remove_control('title_icon_position_mobile');

		$control->start_controls_tabs('title_icon_tabs');
		$control->start_controls_tab('title_icon_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'title_icon_size_normal',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon i, 
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading a i' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon svg, 
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading a svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'title_icon_spacing_normal',
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
					'{{WRAPPER}} .gem-tta-tabs-icon-position-left .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon i,
					{{WRAPPER}} .gem-tta-tabs-icon-position-left .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon svg' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-tabs-icon-position-right .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon i,
					{{WRAPPER}} .gem-tta-tabs-icon-position-right .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon svg' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-tabs-icon-position-top .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon i,
					{{WRAPPER}} .gem-tta-tabs-icon-position-top .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon svg' => 'margin-bottom: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-tabs-icon-position-bottom .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon i,
					{{WRAPPER}} .gem-tta-tabs-icon-position-bottom .gem-tta-tab:not(.gem-tta-active) .gem-tta-icon svg' => 'margin-top: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading a i,
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading a svg' => 'margin-right: {{SIZE}}px;',
				],
			]
		);

		$control->add_control(
			'title_icon_color_normal',
			[
				'label' => __('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tab:not(.gem-tta-active) a .gem-tta-icon i, 
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading a i' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} .gem-tta-tab:not(.gem-tta-active) a .gem-tta-icon svg, 
					{{WRAPPER}} .gem-tta-panel:not(.gem-tta-active) .gem-tta-panel-heading a svg' => 'fill: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('title_icon_tabs_active', ['label' => __('Active', 'thegem'),]);

		$control->add_responsive_control(
			'title_icon_size_active',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tab.gem-tta-active .gem-tta-icon i, 
					{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading a i' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-tab.gem-tta-active .gem-tta-icon svg, 
					{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading a svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'title_icon_spacing_active',
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
					'{{WRAPPER}} .gem-tta-tabs-icon-position-left .gem-tta-tab.gem-tta-active .gem-tta-icon i,
					{{WRAPPER}} .gem-tta-tabs-icon-position-left .gem-tta-tab.gem-tta-active .gem-tta-icon svg' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-tabs-icon-position-right .gem-tta-tab.gem-tta-active .gem-tta-icon i,
					{{WRAPPER}} .gem-tta-tabs-icon-position-right .gem-tta-tab.gem-tta-active .gem-tta-icon svg' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-tabs-icon-position-top .gem-tta-tab.gem-tta-active .gem-tta-icon i,
					{{WRAPPER}} .gem-tta-tabs-icon-position-top .gem-tta-tab.gem-tta-active .gem-tta-icon svg' => 'margin-bottom: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-tabs-icon-position-bottom .gem-tta-tab.gem-tta-active .gem-tta-icon i,
					{{WRAPPER}} .gem-tta-tabs-icon-position-bottom .gem-tta-tab.gem-tta-active .gem-tta-icon svg' => 'margin-top: {{SIZE}}px;',
					'{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading a i,
					{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading a svg' => 'margin-right: {{SIZE}}px;',
				],
			]
		);

		$control->add_control(
			'title_icon_color_active',
			[
				'label' => __('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tab.gem-tta-active a .gem-tta-icon i, 
					{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading a i' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} .gem-tta-tab.gem-tta-active a .gem-tta-icon svg, 
					{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-heading a svg' => 'fill: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Content Container
	 * @access protected
	 */
	protected function content_container($control)
	{
		$control->start_controls_section(
			'content_container_style',
			[
				'label' => __('Content Container', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_container_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'background-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-panel .gem-tta-panel-body,
				{{WRAPPER}} .gem-tta-panel.gem-tta-active .gem-tta-panel-body,
				{{WRAPPER}} .gem-tta-tabs .gem-tta-panels',
			]
		);

		$control->remove_control('content_container_background_image');
		$control->remove_control('content_container_background_image_tablet');
		$control->remove_control('content_container_background_image_mobile');

		$control->add_responsive_control(
			'content_container_radius_gap',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-gap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_gap_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_top_left',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['left', 'bottom', 'right'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'top',
						],
						[
							'name'  => 'title_container_alignment_h',
							'operator' => '=',
							'value' => 'left',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_top_left_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_top_right',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['left', 'bottom', 'top'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'top',
						],
						[
							'name'  => 'title_container_alignment_h',
							'operator' => '=',
							'value' => 'right',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_top_right_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_top_center',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'top',
						],
						[
							'name'  => 'title_container_alignment_h',
							'operator' => '=',
							'value' => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_top_center_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_top_stretch',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['left', 'bottom'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'top',
						],
						[
							'name'  => 'title_container_alignment_h',
							'operator' => '=',
							'value' => 'stretch',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_top_stretch_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_bottom_left',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['right', 'top', 'bottom'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'bottom',
						],
						[
							'name'  => 'title_container_alignment_h',
							'operator' => '=',
							'value' => 'left',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_bottom_left_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_bottom_right',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['right', 'top', 'left'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'bottom',
						],
						[
							'name'  => 'title_container_alignment_h',
							'operator' => '=',
							'value' => 'right',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_bottom_right_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_bottom_center',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'bottom',
						],
						[
							'name'  => 'title_container_alignment_h',
							'operator' => '=',
							'value' => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_bottom_center_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_bottom_stretch',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['right', 'top'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'bottom',
						],
						[
							'name'  => 'title_container_alignment_h',
							'operator' => '=',
							'value' => 'stretch',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_bottom_stretch_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_left_top',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['bottom', 'right', 'left'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'left',
						],
						[
							'name'  => 'title_container_alignment_v',
							'operator' => '=',
							'value' => 'left',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_left_top_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_left_bottom',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['bottom', 'right', 'top'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'left',
						],
						[
							'name'  => 'title_container_alignment_v',
							'operator' => '=',
							'value' => 'right',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_left_bottom_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_left_center',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'left',
						],
						[
							'name'  => 'title_container_alignment_v',
							'operator' => '=',
							'value' => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_left_center_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_left_stretch',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['bottom', 'right'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'left',
						],
						[
							'name'  => 'title_container_alignment_v',
							'operator' => '=',
							'value' => 'stretch',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_left_stretch_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_right_top',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['left', 'top', 'bottom'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'right',
						],
						[
							'name'  => 'title_container_alignment_v',
							'operator' => '=',
							'value' => 'left',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_right_top_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_right_bottom',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['left', 'top', 'right'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'right',
						],
						[
							'name'  => 'title_container_alignment_v',
							'operator' => '=',
							'value' => 'right',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_right_bottom_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_right_center',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'right',
						],
						[
							'name'  => 'title_container_alignment_v',
							'operator' => '=',
							'value' => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_right_center_mobile');

		$control->add_responsive_control(
			'content_container_radius_nogap_right_stretch',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'allowed_dimensions' => ['left', 'top'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name'  => 'title_container_position',
							'operator' => '=',
							'value' => 'right',
						],
						[
							'name'  => 'title_container_alignment_v',
							'operator' => '=',
							'value' => 'stretch',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-nogap.gem-tta-tabs .gem-tta-panels' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_radius_nogap_right_stretch_mobile');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_container_border_default',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => 'classic',
				],
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-top .gem-tta-panels' => 'margin-top: calc(1px - {{TOP}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-left .gem-tta-panels' => 'margin-left: calc(1px - {{LEFT}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-bottom .gem-tta-panels' => 'margin-bottom: calc(1px - {{BOTTOM}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-right .gem-tta-panels' => 'margin-right: calc(1px - {{RIGHT}}px);',							
						],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tabs .gem-tta-panels, {{WRAPPER}} .gem-tta-tabs .gem-tta-panels .gem-tta-panel-respbody',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_container_border_outline',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => 'outline',
				],
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-top .gem-tta-panels' => 'margin-top: calc(2px - {{TOP}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-left .gem-tta-panels' => 'margin-left: calc(2px - {{LEFT}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-bottom .gem-tta-panels' => 'margin-bottom: calc(2px - {{BOTTOM}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-right .gem-tta-panels' => 'margin-right: calc(2px - {{RIGHT}}px);',							
						],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tabs .gem-tta-panels, {{WRAPPER}} .gem-tta-tabs .gem-tta-panels .gem-tta-panel-respbody',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_container_border_modern',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['modern'],
				],
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-top .gem-tta-panels' => 'margin-top: calc(0px - {{TOP}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-left .gem-tta-panels' => 'margin-left: calc(0px - {{LEFT}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-bottom .gem-tta-panels' => 'margin-bottom: calc(0px - {{BOTTOM}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-right .gem-tta-panels' => 'margin-right: calc(0px - {{RIGHT}}px);',							
						],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tabs .gem-tta-panels, {{WRAPPER}} .gem-tta-tabs .gem-tta-panels .gem-tta-panel-respbody',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_container_border_flat',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['flat'],
				],
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-top .gem-tta-panels' => 'margin-top: calc(1px - {{TOP}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-left .gem-tta-panels' => 'margin-left: calc(1px - {{LEFT}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-bottom .gem-tta-panels' => 'margin-bottom: calc(1px - {{BOTTOM}}px);',
							'{{WRAPPER}} .gem-tta-tabs.gem-tta-tabs-position-right .gem-tta-panels' => 'margin-right: calc(1px - {{RIGHT}}px);',							
						],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}!important;',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-tta-tabs .gem-tta-panels, {{WRAPPER}} .gem-tta-tabs .gem-tta-panels .gem-tta-panel-respbody',
			]
		);

		$control->add_responsive_control(
			'content_container_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'title_gap',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tabs .gem-tta-panels' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->remove_control('content_container_margin_mobile');

		$control->add_responsive_control(
			'content_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tabs .gem-tta-panels .gem-tta-panel-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .gem-tta-tabs .gem-tta-panels,  {{WRAPPER}} .gem-tta-tabs .gem-tta-panels .gem-tta-panel-respbody',
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Content Style
	 * @access protected
	 */
	protected function content_style($control)
	{
		$control->start_controls_section(
			'content_style',
			[
				'label' => __('Content Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'content_style_typo',
				'selector' => '{{WRAPPER}} .gem-tta-tabs .gem-tta-panels .gem-tta-panel-body',
			]
		);

		$control->add_control(
			'content_style_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-tta-tabs .gem-tta-panels .gem-tta-panel-body' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$control->add_responsive_control(
			'content_style_position',
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
					'{{WRAPPER}} .gem-tta-tabs .gem-tta-panels .gem-tta-panel-body' => '{{VALUE}}',
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

        $this->add_render_attribute( 'tabs-wrap', 'class', 
		[
			'gem-tta-general',
			'gem-tta',
			'gem-tta-tabs',
			'gem-tta-color-thegem',
			'gem-tta-shape-rounded',
			('yes' === $settings['title_gap'] ? 'gem-tta-gap' : 'gem-tta-nogap'),
			'gem-tta-style-' . $preset,
			'gem-tta-tabs-position-' . $settings['title_container_position'],
			'gem-tta-controls-align-' . (! empty($settings['title_container_alignment_v']) ? $settings['title_container_alignment_v'] : $settings['title_container_alignment_h'])
		]);

		$id_str = $this->get_id();

		?>
<div class="gem-tta-container" data-vc-action="collapse">
    <div <?php echo $this->get_render_attribute_string( 'tabs-wrap' ); ?>>
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

                $this->add_render_attribute( $content_setting_key, 'class', 'gem-tta-panel-body gem-tta-panel-respbody');

                ?>


                <div <?php echo $this->get_render_attribute_string( $title_setting_key ); ?>>
                    <div class="gem-tta-panel-heading">
                    	<h4 class="gem-tta-panel-title">
                    		<a href="#<?php echo $section_id; ?>" data-vc-accordion data-vc-container=".gem-tta-container">
                    			<?php if ( ! empty( $item['icon']['value'] )) { Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true', 'class' => ['gem-tta-icon'] ] ); }?>
                    			<span class="gem-tta-title-text">
                    				<?php echo wp_kses( $item[ 'title' ], 'post' ); ?>
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
        <div class="gem-tta-tabs-container gem-tta-tabs-icon-position-<?php echo $settings['title_icon_position']; ?>">
            <ul class="gem-tta-tabs-list">

            	<?php foreach ( $settings['tabs'] as $index => $item ) :
                $count = $index + 1;

                $section_href = ! empty( $item['section_id']) ? $item['section_id'] : 'section-' . $id_str . '-' . $count;

                ?>


                <li class="gem-tta-tab <?php echo $index === 0 ? 'gem-tta-active' : ''; ?>" data-vc-tab>
                	<a href="#<?php echo $section_href; ?>" data-vc-tabs data-vc-container=".gem-tta">
                				<span class="gem-tta-icon">
									<?php if ( ! empty( $item['icon']['value'] ) ) { Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true'] ); }?>
								</span>
                    			<span class="gem-tta-title-text">
                    				<?php echo wp_kses( $item[ 'title' ], 'post' ); ?>
                    			</span>
                	</a>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
			<?php	
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new TheGem_Tabs());
