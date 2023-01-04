<?php
namespace TheGem_Elementor\Widgets\StyledImage;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
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
 * Elementor widget for Styled Image.
 */
class TheGem_StyledImage extends Widget_Base
{
	public function __construct($data = [], $args = null)
	{
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_STYLEDIMAGE_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_STYLEDIMAGE_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_STYLEDIMAGE_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_STYLEDIMAGE_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-styledimage',
			THEGEM_ELEMENTOR_WIDGET_STYLEDIMAGE_URL . '/assets/css/thegem-styledimage.css',
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
		return 'thegem-styledimage';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Image', 'thegem');
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
		return ['thegem-wrapboxes', 'thegem-styledimage'];
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
			'default' => __( 'No border (default)', 'thegem' ),
			'style-1' => __( '8px and border', 'thegem' ),
			'style-2' => __( '16px and border', 'thegem' ),
			'style-3' => __( '8px outlined border', 'thegem' ),
			'style-4' => __( '20px outlined border', 'thegem' ),
			'style-5' => __( '20px border with shadow', 'thegem' ),
			'style-6' => __( 'Combined border', 'thegem' ),
			'style-7' => __( '20px border radius', 'thegem' ),
			'style-8' => __( '55px border radius', 'thegem' ),
			'style-9' => __( 'Dashed inside', 'thegem' ),
			'style-10' => __( 'Dashed outside', 'thegem' ),
			'style-11' => __( 'Rounded with border', 'thegem' ),
			'style-13' => __( 'Rounded without border', 'thegem' ),
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
		// $this->get_custom_presets();

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
				'type'	=> Controls_Manager::SELECT,
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
				'label' => __('Content', 'thegem'),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'thegem' ),
				'type' => Controls_Manager::MEDIA,
				'frontend_available' => true,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'imagesize',
				'default' => 'full',
				'separator' => 'none',
				'condition' => [ 'image[url]!' => Utils::get_placeholder_image_src() ],
			]
		);

		$this->add_control(
			'link_to',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'file',
				'options' => [
					'none' => __( 'None', 'thegem' ),
					'file' => __( 'Media File', 'thegem' ),
					'custom' => __( 'Custom URL', 'thegem' ),
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
				'condition' => [
					'link_to' => 'custom',
				],
				'show_label' => false,
			]
		);

		$this->add_control(
			'open_lightbox',
			[
				'label' => __('Lightbox', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'link_to' => 'file',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_options',
			[
				'label' => __('Options', 'thegem'),
			]
		);

		$this->add_control(
			'lazy',
			[
				'label' => __('Lazy Loading', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
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
		$this->image_styles($control);

		/*Container Styles*/
		$this->container_styles($control);

		/*Inner Border Styles*/
		$this->inner_styles($control);
	}

	/**
	 * Image Styles
	 * @access protected
	 */
	protected function image_styles($control)
	{
		$control->start_controls_section(
			'image_style',
			[
				'label' => __('Image Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'image_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%', 'px', 'vw'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'image_position',
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
				'default' => 'left',
			]
		);

		$control->add_responsive_control(
			'image_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-image img,
					{{WRAPPER}} .gem-image a:before,
					{{WRAPPER}} .gem-image .gem-wrapbox-inner,
					{{WRAPPER}} .gem-image .gem-wrapbox-inner:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-image .gem-wrapbox-inner',
			]
		);

		$control->start_controls_tabs('image_tabs');
		$control->start_controls_tab('image_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'image_opacity_normal',
			[
				'label' => __('Opacity', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-image img' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-image img',
			]
		);

		$control->add_control(
			'image_blend_mode_normal',
			[
				'label' => __('Blend Mode', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Normal', 'thegem'),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'color-burn' => 'Color Burn',
					'hue' => 'Hue',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'exclusion' => 'Exclusion',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-image img' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'image_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'link_to',
							'operator' => '!=',
							'value' => 'none',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'image_opacity_hover',
			[
				'label' => __('Opacity', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-image a:hover img' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_background_hover',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'toggle' => true,
				'fields_options' => [
					'background' => [
						'label' => _x('Overlay Type', 'Background Control', 'thegem'),
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(0, 188, 212, 0.8)',						
					],
				],
				'selector' => '{{WRAPPER}} .gem-image a:before',
				'condition' => [
					'link_to!' => 'custom',
				],
			]
		);

		$control->remove_control('image_background_hover_image');
		$control->remove_control('image_background_hover_image_tablet');
		$control->remove_control('image_background_hover_image_mobile');

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_custom_background_hover',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'toggle' => true,
				'selector' => '{{WRAPPER}} .gem-image a:before',
				'condition' => [
					'link_to' => 'custom',
				],
			]
		);

		$control->remove_control('image_custom_background_hover_image');
		$control->remove_control('image_custom_background_hover_image_tablet');
		$control->remove_control('image_custom_background_hover_image_mobile');

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_hover',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-image a:hover img',
			]
		);

		$control->add_control(
			'blend_mode_hover',
			[
				'label' => __('Blend Mode', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Normal', 'thegem'),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'color-burn' => 'Color Burn',
					'hue' => 'Hue',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'exclusion' => 'Exclusion',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-image a:hover img' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$control->add_control(
			'iconheader',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'link_to',
							'operator' => '!=',
							'value' => 'none',
						],
					],
				],
			]
		);

		$control->add_control(
			'icon_show',
			[
				'label' => __('Show', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'link_to' => 'file',
				],
			]
		);

		$control->add_control(
			'icon_custom_show',
			[
				'label' => __('Show', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'link_to' => 'custom',
				],
			]
		);

		$control->add_control(
			'icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-camera',
					'library' => 'fa-solid',
				],
				'conditions' => [
			   	'relation' => 'or',
				'terms' => [
					[
						'terms' => [
							[
								'name' => 'link_to',
								'operator' => '=',
								'value' => 'custom'
							],
							[
								'name' => ' icon_custom_show',
								'operator' => '=',
								'value' => 'yes'
							],
						],
					],
					[
						'terms' => [
							[
								'name' => 'link_to',
								'operator' => '=',
								'value' => 'file'
							],
							[
								'name' => ' icon_show',
								'operator' => '=',
								'value' => 'yes'
							],
						],
					],
				]
			]
		]
		);

		$control->add_responsive_control(
			'icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-image a i' => 'color: {{VALUE}};',
				],
				'conditions' => [
			   	'relation' => 'or',
				'terms' => [
					[
						'terms' => [
							[
								'name' => 'link_to',
								'operator' => '=',
								'value' => 'custom'
							],
							[
								'name' => ' icon_custom_show',
								'operator' => '=',
								'value' => 'yes'
							],
						],
					],
					[
						'terms' => [
							[
								'name' => 'link_to',
								'operator' => '=',
								'value' => 'file'
							],
							[
								'name' => ' icon_show',
								'operator' => '=',
								'value' => 'yes'
							],
						],
					],
				]
			]
			]
		);	

		$control->add_responsive_control(
			'iconsize',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
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
				'selectors' => [
					'{{WRAPPER}} .gem-image a i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};
					font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-top: calc(-{{SIZE}}{{UNIT}}/2);',
				],
				'conditions' => [
			   	'relation' => 'or',
				'terms' => [
					[
						'terms' => [
							[
								'name' => 'link_to',
								'operator' => '=',
								'value' => 'custom'
							],
							[
								'name' => ' icon_custom_show',
								'operator' => '=',
								'value' => 'yes'
							],
						],
					],
					[
						'terms' => [
							[
								'name' => 'link_to',
								'operator' => '=',
								'value' => 'file'
							],
							[
								'name' => ' icon_show',
								'operator' => '=',
								'value' => 'yes'
							],
						],
					],
				]
			]
			]
		);

		$control->add_control(
			'icon_rotate',
			[
				'label' => __('Rotate', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-image a i' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'conditions' => [
			   	'relation' => 'or',
				'terms' => [
					[
						'terms' => [
							[
								'name' => 'link_to',
								'operator' => '=',
								'value' => 'custom'
							],
							[
								'name' => ' icon_custom_show',
								'operator' => '=',
								'value' => 'yes'
							],
						],
					],
					[
						'terms' => [
							[
								'name' => 'link_to',
								'operator' => '=',
								'value' => 'file'
							],
							[
								'name' => ' icon_show',
								'operator' => '=',
								'value' => 'yes'
							],
						],
					],
				]
			]
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		

		$control->end_controls_section();
	}

	/**
	 * Container Styles
	 * @access protected
	 */
	protected function container_styles($control)
	{
		$control->start_controls_section(
			'container_style',
			[
				'label' => __('Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'container_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .gem-image',
			]
		);

		$control->remove_control('container_background_image');
		$control->remove_control('container_background_image_tablet');
		$control->remove_control('container_background_image_mobile');

		$control->add_responsive_control(
			'container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-image',
			]
		);

		$control->add_responsive_control(
			'container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-image',
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Inner Border Styles
	 * @access protected
	 */
	protected function inner_styles($control)
	{
		$control->start_controls_section(
			'inner_style',
			[
				'label' => __('Inner Border Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'thegem_elementor_preset',
									'operator' => '=',
									'value' => 'style-9'
								],
								[
									'name' => 'thegem_elementor_preset',
									'operator' => '=',
									'value' => 'style-11'
								],
							],
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'inner_spacing',
			[
				'label' => __('Inner Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
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
				'selectors' => [
					'{{WRAPPER}} .gem-image .gem-wrapbox-inner:after' => 'top: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}}; bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'inner_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-image .gem-wrapbox-inner:after',
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Import media images.
	 *
	 * Used to import media control files from external sites while importing
	 * Elementor template JSON file, and replacing the old data.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Control settings
	 *
	 * @return array Control settings.
	 */
/*
	 public function on_import( $settings ) {
		if ( empty( $settings['image']['url'] ) ) {
			return $settings;
		}

		$settings['image'] = Plugin::$instance->templates_manager->get_import_images_instance()->import( $settings['image'] );

		if ( ! $settings['image'] ) {
			$settings = [
				'id' => '',
				'url' => Utils::get_placeholder_image_src(),
			];
		}

		return $settings;
	}
*/
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

		if ('yes' === $settings['lazy']) {
			thegem_lazy_loading_enqueue();
		}

		$this->add_render_attribute( 'main-wrap', 'class', 
		[
			'gem-image',
			'gem-wrapbox',
			'gem-wrapbox-' . $settings['thegem_elementor_preset'],
			'gem-wrapbox-position-' . $settings['image_position'],
			('yes' === $settings['lazy'] ? 'lazy-loading' : '' ),
			(Plugin::$instance->editor->is_edit_mode() ? 'lazy-loading-not-hide' : '' )
		]);

		$image_url = ($settings['image']['id']) ? Group_Control_Image_Size::get_attachment_image_src($settings['image']['id'], 'imagesize', $settings) : $settings['image']['url'];

		$link = $this->get_link_url( $settings );

		if ( $link ) {
			$this->add_link_attributes( 'link', $link );
			if('yes' === $settings['open_lightbox']) {
				$this->add_render_attribute( 'link', 'class', 'fancybox');
			}
		}

		$preset_path = __DIR__ . '/templates/content-image-item.php';
		$preset_path_filtered = apply_filters( 'thegem_image_item_preset', $preset_path);
		$preset_path_theme = get_stylesheet_directory() . '/templates/image/content-image-item.php';

		if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
			include($preset_path_theme);
		} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
			include($preset_path_filtered);
		}
		?>


	<?php if( is_admin() && Plugin::$instance->editor->is_edit_mode() && ('yes' === $settings['lazy'])): ?>
		<script type="text/javascript">jQuery.lazyLoading();</script>
	<?php endif; ?>

		<?php
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
		if ( 'none' === $settings['link_to'] ) {
			return false;
		}

		if ( 'custom' === $settings['link_to'] ) {
			if ( empty( $settings['link']['url'] ) ) {
				return false;
			}

			return $settings['link'];
		}

		return [
			'url' => $settings['image']['url'],
		];
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new TheGem_StyledImage());
