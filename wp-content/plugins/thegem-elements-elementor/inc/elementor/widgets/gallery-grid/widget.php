<?php

namespace TheGem_Elementor\Widgets\GalleryGrid;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;


if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Gallery Grid.
 */
class TheGem_GalleryGrid extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */
	protected $presets;

	public $preset_elements_select;

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-gallery-grid-styles',
			THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_URL . '/assets/css/thegem-gallery-grid.css',
			array(
				'thegem-wrapboxes',
				'thegem-animations',
				'thegem-hovers'
			),
			null
		);
		wp_register_script(
			'thegem-gallery-grid-scripts',
			THEGEM_ELEMENTOR_WIDGET_GALLERYGRID_URL . '/assets/js/thegem-gallery-grid.js',
			array(
				'jquery',
				'isotope-js',
				'thegem-isotope-masonry-custom',
				'thegem-isotope-metro',
				'thegem-scroll-monitor',
				'thegem-items-animations'),
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
		return 'thegem-gallery-grid';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Gallery Grid', 'thegem');
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
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-hovers-default',
				'thegem-hovers-zooming-blur',
				'thegem-hovers-horizontal-sliding',
				'thegem-hovers-vertical-sliding',
				'thegem-hovers-gradient',
				'thegem-hovers-circular',
				'thegem-gallery-grid-styles'];
		}
		return ['thegem-gallery-grid-styles'];
	}

	public function get_script_depends() {
		return ['thegem-gallery-grid-scripts'];
	}

	/* Show reload button */
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
			'default' => __('No border (default)', 'thegem'),
			'1' => __('8px and border', 'thegem'),
			'2' => __('16px and border', 'thegem'),
			'3' => __('8px outlined border', 'thegem'),
			'4' => __('20px outlined border', 'thegem'),
			'5' => __('20px border with shadow', 'thegem'),
			'6' => __('Combined border', 'thegem'),
			'7' => __('20px border radius', 'thegem'),
			'8' => __('55px border radius', 'thegem'),
			'9' => __('Dashed inside', 'thegem'),
			'10' => __('Dashed outside', 'thegem'),
			'11' => __('Rounded with border', 'thegem'),
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

		$this->add_control(
			'columns',
			[
				'label' => __('Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '3x',
				'options' => [
					'2x' => __('2x columns', 'thegem'),
					'3x' => __('3x columns', 'thegem'),
					'4x' => __('4x columns', 'thegem'),
					'100%' => __('100% width', 'thegem'),
				],
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'justified',
				'options' => [
					'justified' => __('Justified Grid', 'thegem'),
					'masonry' => __('Masonry Grid', 'thegem'),
					'metro' => __('Metro Style', 'thegem'),
				],
			]
		);

		$this->add_control(
			'columns_100',
			[
				'label' => __('100% Width Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => [
					'4' => __('4 Columns', 'thegem'),
					'5' => __('5 Columns', 'thegem'),
				],
				'condition' => [
					'columns' => '100%',
				],
				'description' => __('Number of columns for 100% width grid starting from 1920px resolutions', 'thegem'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Content', 'thegem'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'gallery_title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Title', 'elementor-pro'),
				'default' => __('New Gallery', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'multiple_gallery',
			[
				'type' => Controls_Manager::GALLERY,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'galleries',
			[
				'type' => Controls_Manager::REPEATER,
				'label' => __('Galleries', 'elementor-pro'),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ gallery_title }}}',
				'default' => [
					[
						'gallery_title' => __('New Gallery', 'thegem'),
					],
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
			'loading_animation',
			[
				'label' => __('Loading Animation', 'thegem'),
				'default' => 'no',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'animation_effect',
			[
				'label' => __('Animation Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'bounce',
				'options' => [
					'bounce' => __('Bounce', 'thegem'),
					'move-up' => __('Move Up', 'thegem'),
					'fade-in' => __('Fade In', 'thegem'),
					'fall-perspective' => __('Fall Perspective', 'thegem'),
					'scale' => __('Scale', 'thegem'),
					'flip' => __('Flip', 'thegem'),
				],
				'condition' => [
					'loading_animation' => 'yes',
				],
			]
		);

		$this->add_control(
			'metro_max_row_height',
			[
				'label' => __('Max. row\'s height in metro grid (px)', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 600,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 380
				],
				'description' => __('Metro grid auto sets the row\'s height. Specify max. allowed height for best appearance (380px recommended).', 'thegem'),
				'condition' => [
					'layout' => 'metro',
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

		/* Container Style */
		$this->container_style($control);

		/* Image Style */
		$this->image_style($control);

		/* Inner Border Style */
		$this->inner_border_style($control);
	}

	/**
	 * Image Styles
	 * @access protected
	 */
	protected function image_style($control) {
		$control->start_controls_section(
			'image_style',
			[
				'label' => __('Image Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'image_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gallery-item .image-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gallery-item .overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item .wrap',
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
					'{{WRAPPER}} .gallery-item .overlay-wrap' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item img',
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
					'{{WRAPPER}} .gallery-item .overlay-wrap' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('image_tabs_hover', ['label' => __('Hover', 'thegem'),]);

		$this->add_control(
			'hover_effect',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Cyan Breeze', 'thegem'),
					'zooming-blur' => __('Zooming White', 'thegem'),
					'horizontal-sliding' => __('Horizontal Sliding', 'thegem'),
					'vertical-sliding' => __('Vertical Sliding', 'thegem'),
					'gradient' => __('Gradient', 'thegem'),
					'circular' => __('Circular Overlay', 'thegem'),
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_hover_overlay',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'default' => 'classic',
				'fields_options' => [
					'background' => [
						'label' => _x('Overlay Type', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .gallery-item .overlay:before, .hover-circular .gallery-item .overlay-wrap .overlay .overlay-circle' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .gallery-item .overlay:before, .hover-circular .gallery-item .overlay-wrap .overlay .overlay-circle' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .gallery-item .overlay:before, .hover-circular .gallery-item .overlay-wrap .overlay .overlay-circle' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				]

			]
		);

		$control->remove_control('image_hover_overlay_image');

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'hover_css',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item .overlay-wrap:hover img',
			]
		);

		$control->add_control(
			'hover_blend_mode',
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
					'{{WRAPPER}} .gallery-item:hover .overlay-wrap' => 'mix-blend-mode: {{VALUE}}',
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
			]
		);

		$control->add_control(
			'icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'icon_show' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gallery-item .overlay i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gallery-item .overlay .overlay-line' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'icon_show' => 'yes',
				],
			]
		);

		$control->add_control(
			'icon_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gallery-item .overlay i' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'icon_show' => 'yes',
					'hover_effect' => 'zooming-blur'
				],
			]
		);



		$control->add_responsive_control(
			'icon_size',
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
					'{{WRAPPER}} .gallery-item .overlay a.icon i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-gallery-grid:not(.hover-zooming-blur):not(.hover-gradient) .gallery-item .overlay a.icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-gallery-grid.hover-zooming-blur .gallery-item  .overlay a.icon i' => 'font-size: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .gem-gallery-grid.hover-gradient .gallery-item  .overlay a.icon i' => 'font-size: calc({{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'icon_show' => 'yes',
				],
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
					'{{WRAPPER}} .gallery-item .overlay a.icon i' => 'display: inline-block; transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'icon_show' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Container Style
	 * @access protected
	 */
	protected function container_style($control) {
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
				'selector' => '{{WRAPPER}} .gallery-item .wrap',
			]
		);

		$control->remove_control('container_background_image');

		$control->add_responsive_control(
			'container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gallery-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .gem-gallery-grid .gallery-item .wrap',
			]
		);

		$control->add_responsive_control(
			'container_margin',
			[
				'label' => __('Gaps', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
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
					'size' => 16,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-grid .gallery-item' => 'padding: calc({{SIZE}}{{UNIT}}/2) !important;',
					'{{WRAPPER}} .gem-gallery-grid .row' => 'margin-top: calc(-{{SIZE}}{{UNIT}}/2); margin-bottom: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .gem-gallery-grid .not-fullwidth-block ul' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .gem-gallery-grid .fullwidth-block' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',
				]
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
					'{{WRAPPER}} .gallery-item .wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item .wrap',
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Inner Border Style
	 * @access protected
	 */
	protected function inner_border_style($control) {
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
									'value' => '9'
								],
								[
									'name' => 'thegem_elementor_preset',
									'operator' => '=',
									'value' => '11'
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
					'{{WRAPPER}} .gallery-item .gem-wrapbox-inner:after' => 'top: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}}; bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gallery-item .overlay-wrap:after' => 'top: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}}; bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'inner_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gallery-item .gem-wrapbox-inner:after, {{WRAPPER}} .gallery-item .overlay-wrap:after',
			]
		);

		$control->end_controls_section();
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

		wp_enqueue_style('thegem-hovers-' . $settings['hover_effect']);

		$is_metro = 'metro' === $settings['layout'];
		$is_masonry = 'masonry' === $settings['layout'];

		$layout_columns_count = -1;
		if ($settings['columns'] == '2x')
			$layout_columns_count = 2;
		if ($settings['columns'] == '3x')
			$layout_columns_count = 3;
		if ($settings['columns'] == '4x')
			$layout_columns_count = 4;

		$this->add_render_attribute('main-slider-wrap', 'class',
			[
				'gem-gallery-grid col-lg-12 col-md-12 col-sm-12',
				' gallery-style-' . $settings['layout'],
				' hover-' . $settings['hover_effect'],
				($is_metro ? ' metro metro-item-style-' . $settings['thegem_elementor_preset'] . ' without-padding' : ' gaps-margin'),
				($is_masonry ? ' gallery-items-masonry' : ''),
				($layout_columns_count != -1 ? 'columns-' . $layout_columns_count : ''),
				($settings['loading_animation'] == 'yes' ? ' item-animation-' . $settings['animation_effect'] : ''),
				($settings['columns'] == '100%' ? ' fullwidth-columns fullwidth-columns-' . intval($settings['columns_100']) : ''),
				(Plugin::$instance->editor->is_edit_mode() ? 'lazy-loading-not-hide' : '')
			]);
		$this->add_render_attribute('main-slider-wrap', 'data-hover', $settings['hover_effect']);

		$galleries = [];

		if (!empty($settings['galleries'])) { ?>

			<?php foreach ($settings['galleries'] as $index => $gallery) :
				$galleries[$index] = $gallery['multiple_gallery'];
			endforeach; ?>

			<?php
			$gallery_items = [];
			foreach ($galleries as $gallery_index => $gallery) {
				foreach ($gallery as $index => $item) {
					if (in_array($item['id'], array_keys($gallery_items), true)) {
						$gallery_items[$item['id']][] = $gallery_index;
					} else {
						$gallery_items[$item['id']] = [$gallery_index];
					}
				}
			}
			$gallery_uid = uniqid();

		if (!empty($gallery_items)) { ?>
			<div class="preloader">
				<div class="preloader-spin"></div>
			</div>
			<div class="gallery-preloader-wrapper">

				<div class="row">

					<div <?php echo $this->get_render_attribute_string('main-slider-wrap'); ?>>

						<div class="<?php echo $settings['columns'] == '100%' ? 'fullwidth-block' : 'not-fullwidth-block'; ?> ">

							<ul class="gallery-set clearfix"
								data-max-row-height="<?php echo $settings['metro_max_row_height'] ? $settings['metro_max_row_height']['size'] : ''; ?>">
								<?php
								foreach ($gallery_items as $img_id => $tags) :
									$thegem_item = get_post($img_id);

									if (!$thegem_item) {
										continue;
									}
									$thegem_highlight = (bool)get_post_meta($thegem_item->ID, 'highlight', true);
									$thegem_highligh_type = get_post_meta($thegem_item->ID, 'highligh_type', true);
									if (!$thegem_highligh_type) {
										$thegem_highligh_type = 'squared';
									}
									$thegem_attachment_link = get_post_meta($thegem_item->ID, 'attachment_link', true);
									$thegem_single_icon = true;

									if (!empty($thegem_attachment_link)) {
										$thegem_single_icon = false;
									}


									if ($settings['layout'] == 'justified') {
										$thegem_size = 'thegem-gallery-justified';
										if ($thegem_highlight) {
											$thegem_size = 'thegem-gallery-justified-double';
											if ($settings['columns'] == '4x') {
												$thegem_size = 'thegem-gallery-justified-double-4x';
											}
										}
									} else if ($settings['layout'] == 'masonry') {
										if ($thegem_highlight) {
											$thegem_size = 'thegem-gallery-masonry-double';
										} else {
											$thegem_size = 'thegem-gallery-masonry';
										}
									}

									if (isset($thegem_size) && $settings['columns'] == '100%') {
										$thegem_size .= '-100';
									}

									if ($settings['layout'] == 'metro') {
										$thegem_size = 'thegem-gallery-metro';
									}

									if ($thegem_highlight && $settings['layout'] != 'metro' && $thegem_highligh_type != 'squared') {
										$thegem_size .= '-' . $thegem_highligh_type;
									}
									if ($settings['columns'] == '2x') {
										$thegem_size = 'thegem-gallery-' . $settings['layout'];
									}

									$thegem_full_image_url = wp_get_attachment_image_src($thegem_item->ID, 'full');

									$thegem_classes = array('gallery-item');

									if ($settings['layout'] != 'metro' && $settings['columns'] == '2x') {
										$thegem_classes = array_merge($thegem_classes, array('col-lg-6', 'col-md-6', 'col-sm-6', 'col-xs-12'));
									}

									if ($settings['layout'] != 'metro' && $settings['columns'] == '3x') {
										if ($thegem_highlight && $thegem_highligh_type != 'vertical') {
											$thegem_classes = array_merge($thegem_classes, array('col-lg-8', 'col-md-8', 'col-sm-12', 'col-xs-12'));
										} else {
											$thegem_classes = array_merge($thegem_classes, array('col-lg-4', 'col-md-4', 'col-sm-6', 'col-xs-6'));
										}
									}

									if ($settings['layout'] != 'metro' && $settings['columns'] == '4x') {
										if ($thegem_highlight && $thegem_highligh_type != 'vertical') {
											$thegem_classes = array_merge($thegem_classes, array('col-lg-6', 'col-md-6', 'col-sm-8', 'col-xs-12'));
										} else {
											$thegem_classes = array_merge($thegem_classes, array('col-lg-3', 'col-md-3', 'col-sm-4', 'col-xs-6'));
										}
									}

									if ($settings['layout'] != 'metro' && $thegem_highlight) {
										$thegem_classes[] = 'double-item';
									}

									if ($settings['layout'] != 'metro' && $thegem_highlight && $thegem_highligh_type != 'squared') {
										$thegem_classes[] = 'double-item-' . $thegem_highligh_type;
									}

									$thegem_wrap_classes = $settings['thegem_elementor_preset'];

									$thegem_classes[] = 'item-animations-not-inited';

									if (empty($settings['icon']['value'])) {
										$thegem_classes[] = 'single-icon';
									}

									$thegem_sources = array();

									if ($settings['layout'] == 'metro') {
										$thegem_sources = array(
											array('media' => '(min-width: 550px) and (max-width: 1100px)', 'srcset' => array('1x' => 'thegem-gallery-metro-medium', '2x' => 'thegem-gallery-metro-retina'))
										);
									}

									if (!$thegem_highlight) {
										$retina_size = $settings['layout'] == 'justified' ? $thegem_size : 'thegem-gallery-masonry-double';

										if ($settings['columns'] == '100%') {
											if ($settings['layout'] == 'justified' || $settings['layout'] == 'masonry') {
												switch ($settings['columns_100']) {
													case '4':
														$thegem_sources = array(
															array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
															array('media' => '(max-width: 992px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-fullwidth-5x', '2x' => $retina_size)),
															array('media' => '(max-width: 1032px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-4x-small', '2x' => $retina_size)),
															array('media' => '(max-width: 1180px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-4x', '2x' => $retina_size)),
															array('media' => '(max-width: 1280px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-5x', '2x' => $retina_size)),
															array('media' => '(max-width: 1495px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-fullwidth-5x', '2x' => $retina_size)),
															array('media' => '(max-width: 1575px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-3x', '2x' => $retina_size)),
															array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-fullwidth-4x', '2x' => $retina_size)),
														);
														break;

													case '5':
														$thegem_sources = array(
															array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
															array('media' => '(min-width: 992px) and (max-width: 1175px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-4x', '2x' => $retina_size)),
															array('media' => '(min-width: 1495px) and (max-width: 1680px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-fullwidth-4x', '2x' => $retina_size)),
															array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-fullwidth-5x', '2x' => $retina_size))
														);
														break;
												}
											}
										} else {
											if ($settings['layout'] == 'justified' || $settings['layout'] == 'masonry') {
												switch ($settings['columns']) {
													case '2x':
														$thegem_sources = array(
															array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
															array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-2x', '2x' => $retina_size))
														);
														break;

													case '3x':
														$thegem_sources = array(
															array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
															array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-3x', '2x' => $retina_size))
														);
														break;

													case '4x':
														$thegem_sources = array(
															array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
															array('media' => '(max-width: 1100px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-3x', '2x' => $retina_size)),
															array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-4x', '2x' => $retina_size))
														);
														break;
												}
											}
										}
									} else {
										$retina_size = $thegem_size;
										if ($settings['columns'] == '100%') {
											if ($settings['layout'] == 'justified' || $settings['layout'] == 'masonry') {
												switch ($settings['columns_100']) {
													case '4':
														$thegem_sources = array(
															array('media' => '(max-width: 700px),(min-width: 825px) and (max-width: 992px),(min-width: 1095px) and (max-width: 1495px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-double-100-' . $thegem_highligh_type . '-5', '2x' => $retina_size)),
															array('media' => '(min-width: 700px) and (max-width: 825px),(min-width: 992px) and (max-width: 1095px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-double-100-' . $thegem_highligh_type . '-6', '2x' => $retina_size)),
															array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-double-100-' . $thegem_highligh_type . '-4', '2x' => $retina_size))
														);
														break;

													case '5':
														$thegem_sources = array(
															array('media' => '(max-width: 700px),(min-width: 825px) and (max-width: 992px),(min-width: 1095px) and (max-width: 1495px),(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-double-100-' . $thegem_highligh_type . '-5', '2x' => $retina_size)),
															array('media' => '(min-width: 700px) and (max-width: 825px),(min-width: 992px) and (max-width: 1095px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-double-100-' . $thegem_highligh_type . '-6', '2x' => $retina_size)),
															array('media' => '(max-width: 1680px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-double-100-' . $thegem_highligh_type . '-4', '2x' => $retina_size)),
														);
														break;
												}
											}
										} else {
											if ($settings['layout'] == 'justified' || $settings['layout'] == 'masonry') {
												switch ($settings['columns']) {
													case '2x':
														$thegem_sources = array(
															array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
															array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-2x', '2x' => $retina_size))
														);
														break;

													case '4x':
														$thegem_sources = array(
															array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-double-4x', '2x' => $retina_size)),
															array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-gallery-' . $settings['layout'] . '-double-4x-' . $thegem_highligh_type, '2x' => $retina_size))
														);
														break;
												}
											}
										}
									}
									$preset_path = __DIR__ . '/templates/content-gallery-item.php';

									if (!empty($preset_path) && file_exists($preset_path)) {
										include($preset_path);
									}
									?>

								<?php endforeach; ?>
							</ul>

						</div>
					</div>
				</div>

				<?php
				} elseif (Plugin::$instance->editor->is_edit_mode()) { ?>
					<div class="no-elements-gallery-grid">
						<i class="eicon-gallery-justified" aria-hidden="true"></i>
					</div>
				<?php }
				?>
			</div>

			<?php
			if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
				<script type="text/javascript">
                    (function ($) {

						setTimeout(function () {
							if (!$('.elementor-element-<?php echo $this->get_id(); ?> .gem-gallery-grid').length) {
								return;
							}
							$('.elementor-element-<?php echo $this->get_id(); ?> .gem-gallery-grid').initGalleriesGrid();
						}, 1000);

                        elementor.channels.editor.on('change', function (view) {
                            var changed = view.elementSettingsModel.changed;

                            if (changed.container_margin !== undefined || changed.justified_row_height !== undefined || changed.container_padding !== undefined ) {
								setTimeout(function () {
									$('.elementor-element-<?php echo $this->get_id(); ?> .gem-gallery-grid').initGalleriesGrid();
								}, 500);
                            }
                        });

                    })(jQuery);

				</script>
			<?php endif;
		}
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_GalleryGrid());