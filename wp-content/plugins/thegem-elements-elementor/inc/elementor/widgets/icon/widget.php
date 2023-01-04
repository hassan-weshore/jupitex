<?php
namespace TheGem_Elementor\Widgets\Icon;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Controls_Stack;
use Elementor\Control_Media;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Core\Schemes;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Icon.
 */
class TheGem_Icon extends Widget_Base
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

		if (!defined('THEGEM_ELEMENTOR_WIDGET_ICON_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_ICON_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_ICON_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_ICON_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-icon-css',
			THEGEM_ELEMENTOR_WIDGET_ICON_URL . '/assets/css/thegem-icon.css',
			array(),
			null
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
		return 'thegem-icon';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Icon', 'thegem');
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
		return ['thegem-icon-css'];
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
	protected function get_presets_options() {
		$out = array(
			'default' => __('Default', 'thegem'),
			'circle' => __('Circle', 'thegem'),
			'square' => __('Square', 'thegem'),
			'romb' => __('Diamond', 'thegem'),
			'hexagon' => __('Hexagon', 'thegem'),
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
		return 'default';
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
				'label'   => __('Shape', 'thegem'),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'small' => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem'),
					'xlarge' => __('Xlarge', 'thegem'),
				],
				'default' => 'medium',
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __('Color Split', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'' => __('None', 'thegem'),
					'gradient' => __('Gradient', 'thegem'),
					'angle-45deg-r' => __('45 degree Right', 'thegem'),
					'angle-45deg-l' => __('45 degree Left', 'thegem'),
					'angle-90deg' => __('90 degree', 'thegem'),					
				],
				'default' => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon',
			[
				'label' => __('Icon', 'thegem'),
			]
		);		

		$this->add_control(
			'icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'show_label' => false,
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

		/*Image Styles*/
		$this->icon_styles($control);
	}

	/**
	 * Icon Styles
	 * @access protected
	 */
	protected function icon_styles($control)
	{
		$control->start_controls_section(
			'icon_style',
			[
				'label' => __('Icon Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'size_default',
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
					'{{WRAPPER}} .gem-icon' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .gem-icon .gem-icon-inner .padding-box-inner' => 'width: {{SIZE}}px;height: {{SIZE}}px;line-height: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'size_small',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 36,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'size',
							'operator' => '=',
							'value' => 'small',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'font-size: {{SIZE}}px;',	
					'{{WRAPPER}} .gem-icon .gem-icon-inner svg' => 'width: {{SIZE}}px;',	
				],
			]
		);

		$control->add_responsive_control(
			'size_medium',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 58,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'size',
							'operator' => '=',
							'value' => 'medium',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .gem-icon .gem-icon-inner svg' => 'width: {{SIZE}}px;',			
				],
			]
		);

		$control->add_responsive_control(
			'size_large',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 114,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'size',
							'operator' => '=',
							'value' => 'large',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .gem-icon .gem-icon-inner svg' => 'width: {{SIZE}}px;',			
				],
			]
		);

		$control->add_responsive_control(
			'size_xlarge',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 164,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'size',
							'operator' => '=',
							'value' => 'xlarge',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .gem-icon .gem-icon-inner svg' => 'width: {{SIZE}}px;',			
				],
			]
		);

		$control->add_responsive_control(
			'box_size',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'padding: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'box_padding_small',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'size',
							'operator' => '=',
							'value' => 'small',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}}  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2); 
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(59px - {{SIZE}}px * 2);height: calc(49px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'box_padding_medium',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'size',
							'operator' => '=',
							'value' => 'medium',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors' => [
					'{{WRAPPER}}  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2); 
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(91px - {{SIZE}}px * 2);height: calc(77px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'box_padding_large',
			[
				'label' => __('Box Padding', 'thegem'),
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
							'name'  => 'size',
							'operator' => '=',
							'value' => 'large',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}}  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2); 
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(181px - {{SIZE}}px * 2);height: calc(153px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'box_padding_xlarge',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'size',
							'operator' => '=',
							'value' => 'xlarge',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}}  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2); 
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(267px - {{SIZE}}px * 2);height: calc(227px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => __('Border', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-icon',
			]
		);

		$control->remove_control('border_color');

		$control->add_responsive_control(
			'position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'centered' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'default' => 'centered',
			]
		);

		$control->start_controls_tabs('tabs');
		$control->start_controls_tab('tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_normal',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-icon',
			]
		);

		$control->remove_control('background_normal_image');

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_normal_hexagon',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-icon-shape-hexagon-top-inner-before',
			]
		);

		$control->remove_control('background_normal_hexagon_image');

		$control->add_control(
			'border_color_normal',
			[
				'label' => __( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .gem-icon .gem-icon-shape-hexagon-back-inner-before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'color_normal',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'style',
							'operator' => '=',
							'value' => '',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon .back-angle i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);		
		
		
		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'color_gradient_normal',
				'types' => ['gradient'],
				'default' => 'gradient',
				'fields_options' => [
					'background' => [
						'label' => _x('Icon Color', 'Background Control', 'thegem'),
					],
					'gradient_angle' => [
						'selectors' => [
							'{{SELECTOR}}' => '-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
					],
					'color' => [
						'default' => '#91a0ac',
					],
					'color_b' => [
						'default' => '#65707e',
					]
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'style',
							'operator' => '=',
							'value' => 'gradient',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-icon .back-angle i',
			]
		);

		$control->remove_control('color_gradient_normal_image');

		$control->add_control(
			'color_1_normal',
			[
				'label' => __( 'Icon Color 1', 'elementor' ),
				'type' => Controls_Manager::COLOR,
								'conditions' => [
					'terms' => [
						[
							'name'  => 'style',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'style',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'color_2_normal',
			[
				'label' => __( 'Icon Color 2', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'style',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'style',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'default' => '#65707e',
				'selectors' => [
					'{{WRAPPER}} .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);		

		$control->add_responsive_control(
			'rotate_normal',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon .padding-box-inner' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);	

		$control->add_responsive_control(
			'rotate_hexagon_normal',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon .back-angle i' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);	

		$control->add_responsive_control(
			'box_rotate_normal',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'romb',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->add_responsive_control(
			'box_rotate_romb_normal',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 45,
				],
				'tablet_default' => [
					'unit' => 'deg',
					'size' => 45,
				],
				'mobile_default' => [
					'unit' => 'deg',
					'size' => 45,
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'romb',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'transform: rotate({{SIZE}}); -webkit-transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'shadow_normal',
				'label' => __( 'Icon Shadow', 'thegem' ),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'default',
						],
					],
				],
				'fields_options' => [
					'text_shadow' => [
						'selectors' => [
							'{{WRAPPER}} .gem-icon .back-angle i' => 'text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
							'{{WRAPPER}} .gem-icon svg' => 'filter: drop-shadow({{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}});',
						],
					]
				],				
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_normal',
				'label' => __('Box Shadow', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-icon',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} a:hover .gem-icon',
			]
		);

		$control->remove_control('background_hover_image');

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_hover_hexagon',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} a:hover .gem-icon .gem-icon-shape-hexagon-top-inner-before',
			]
		);

		$control->remove_control('background_hover_hexagon_image');

		$control->add_control(
			'border_color_hover',
			[
				'label' => __( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} a:hover .gem-icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} a:hover .gem-icon .gem-icon-shape-hexagon-back-inner-before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'color_hover',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'style',
							'operator' => '=',
							'value' => '',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} a:hover .gem-icon .back-angle i' => 'color: {{VALUE}};',
					'{{WRAPPER}} a:hover .gem-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);		
		
		
		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'color_gradient_hover',
				'types' => ['gradient'],
				'default' => 'gradient',
				'fields_options' => [
					'background' => [
						'label' => _x('Icon Color', 'Background Control', 'thegem'),
					],
					'gradient_angle' => [
						'selectors' => [
							'{{SELECTOR}}' => '-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
					]
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'style',
							'operator' => '=',
							'value' => 'gradient',
						],
					],
				],
				'selector' => '{{WRAPPER}} a:hover .gem-icon .back-angle i',
			]
		);

		$control->remove_control('color_gradient_hover_image');

		$control->add_control(
			'color_1_hover',
			[
				'label' => __( 'Icon Color 1', 'elementor' ),
				'type' => Controls_Manager::COLOR,
								'conditions' => [
					'terms' => [
						[
							'name'  => 'style',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'style',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} a:hover .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} a:hover .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'color_2_hover',
			[
				'label' => __( 'Icon Color 2', 'elementor' ),
				'type' => Controls_Manager::COLOR,
								'conditions' => [
					'terms' => [
						[
							'name'  => 'style',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'style',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'default' => '#65707e',
				'selectors' => [
					'{{WRAPPER}} a:hover .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} a:hover .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);		

		$control->add_responsive_control(
			'rotate_hover',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} a:hover .gem-icon .padding-box-inner' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);	

		$control->add_responsive_control(
			'rotate_hexagon_hover',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} a:hover .gem-icon .back-angle i' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$control->add_responsive_control(
			'box_rotate_hover',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'romb',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} a:hover .gem-icon' => 'transform: rotate({{SIZE}}); -webkit-transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->add_responsive_control(
			'box_rotate_romb_hover',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 45,
				],
				'tablet_default' => [
					'unit' => 'deg',
					'size' => 45,
				],
				'mobile_default' => [
					'unit' => 'deg',
					'size' => 45,
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'romb',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} a:hover .gem-icon' => 'transform: rotate({{SIZE}}); -webkit-transform: rotate({{SIZE}}deg);',

				],
			]
		);		


		$control->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'shadow_hover',
				'label' => __( 'Icon Shadow', 'thegem' ),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '=',
							'value' => 'default',
						],
					],
				],
				'fields_options' => [
					'text_shadow' => [
						'selectors' => [
							'{{WRAPPER}} a:hover .gem-icon .back-angle i' => 'text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
							'{{WRAPPER}} a:hover .gem-icon svg' => 'filter: drop-shadow({{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}});',
						],
					]
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_hover',
				'label' => __('Box Shadow', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} a:hover .gem-icon',
			]
		);		

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
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
	public function render()
	{
		$settings = $this->get_settings_for_display();

		$preset = $this->get_setting_preset($settings['thegem_elementor_preset']);

		if ( empty($preset) ) return;

		if ( ! empty( $settings['link']['url'] ) ) {		
					
			$this->add_render_attribute( 'a-wrapper', 'href', $settings['link']['url'] );

			if ( ! empty( $settings['link']['is_external'] ) ) {
				$this->add_render_attribute( 'a-wrapper', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'a-wrapper', 'rel', 'nofollow' );
			}
		}
		?>
				<div class="<?php echo $settings['position']; ?>-box icon-inline-position">

				<a <?php echo $this->get_render_attribute_string( 'a-wrapper' ); ?>>

				<div class="gem-icon gem-icon-pack-fontawesome gem-icon-size-<?php echo $settings['size']; ?> <?php echo $settings['style']; ?> gem-icon-shape-<?php echo $preset; ?> ">

				<?php if ( 'hexagon' === $preset) : ?>

					<div class="gem-icon-shape-hexagon-back">
						<div class="gem-icon-shape-hexagon-back-inner">
							<div class="gem-icon-shape-hexagon-back-inner-before"></div>
						</div>
					</div>

					<div class="gem-icon-shape-hexagon-top">
						<div class="gem-icon-shape-hexagon-top-inner">
							<div class="gem-icon-shape-hexagon-top-inner-before"></div>
						</div>
					</div>

				<?php endif; ?>	
				
				<div class="gem-icon-inner">				

				<?php if ( 'romb' === $preset) : ?>						
					<div class="romb-icon-conteiner">
				<?php endif; ?>

				<?php if ( 'hexagon' !== $preset) : ?>						
					<div class="padding-box-inner">	
				<?php endif; ?>							


				<?php if ('gradient' === $settings['style']) { ?>						
					<span class="gem-icon-style-gradient"><span class="back-angle">
					<?php if ( ! empty( $settings['icon']['value'] ) ) { Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); }?>
					</span></span>
				<?php } else { ?>	
					<span class="gem-icon-half-1"><span class="back-angle">
					<?php if ( ! empty( $settings['icon']['value'] ) ) { Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); }?>
					</span></span>
					<span class="gem-icon-half-2"><span class="back-angle">
					<?php if ( ! empty( $settings['icon']['value'] ) ) { Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); }?>
					</span></span>	
				<?php } ?>

				</div>				

				<?php if ( 'romb' === $preset) : ?>						
					</div>
				<?php endif; ?>					

				<?php if ( 'hexagon' !== $preset) : ?>						
					</div>
				<?php endif; ?>					

				</div>

				</a>

				</div>
			<?php	
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new TheGem_Icon());
