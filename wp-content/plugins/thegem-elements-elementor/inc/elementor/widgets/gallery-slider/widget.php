<?php

namespace TheGem_Elementor\Widgets\GallerySlider;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
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
 * Elementor widget for Gallery Slider.
 */
class TheGem_GallerySlider extends Widget_Base {
	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */
	protected $presets;

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_GALLERYSLIDER_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_GALLERYSLIDER_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_GALLERYSLIDER_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_GALLERYSLIDER_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-gallery-slider',
			THEGEM_ELEMENTOR_WIDGET_GALLERYSLIDER_URL . '/assets/css/thegem-gallery-slider.css',
			array(),
			null
		);

		wp_register_script('thegem-galleryslider-script', THEGEM_ELEMENTOR_WIDGET_GALLERYSLIDER_URL . '/assets/js/thegem-gallery.js', array('jquery', 'thegem-scroll-monitor', 'jquery-carouFredSel'), null, true);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-gallery-slider';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Gallery Slider', 'thegem');
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
		return ['thegem-gallery-slider'];
	}

	public function get_script_depends() {
		return ['thegem-galleryslider-script'];
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
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'label' => __('Aspect Ratio', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fullwidth',
				'options' => [
					'fullwidth' => __('Wide', 'thegem'),
					'sidebar' => __('Normal', 'thegem'),
				],
				'description' => 'Recommended min. image size:<br/>
				Wide: 1170 x 540 px<br/>
				Normal: 870 x 540 px',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slides',
			[
				'label' => __('Slides', 'thegem'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'slide_image',
			[
				'label' => __('Image', 'thegem'),
				'type' => Controls_Manager::MEDIA,
				'frontend_available' => true,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'slide_heading',
			[
				'label' => __('Title & Subtitle', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$repeater->add_control(
			'slide_title',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __('Type title here', 'thegem'),
			]
		);

		$repeater->add_control(
			'slide_subtitle',
			[
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'placeholder' => __('Type subtitle here', 'thegem'),
			]
		);

		$this->add_control(
			'slides_list',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'slide_image' => [
							'url' => Utils::get_placeholder_image_src(),
						],
						'slide_title' => 'Title 1',
						'slide_subtitle' => 'Description 1',

					],
					[
						'slide_image' => [
							'url' => Utils::get_placeholder_image_src(),
						],
						'slide_title' => 'Title 2',
						'slide_subtitle' => 'Description 2',
					],
					[
						'slide_image' => [
							'url' => Utils::get_placeholder_image_src(),
						],
						'slide_title' => 'Title 3',
						'slide_subtitle' => 'Description 3',
					],
				],
				'title_field' => '{{{ slide_title }}}',
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
			'thumbs_show',
			[
				'label' => __('Thumbnails Bar', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
			]
		);

		$this->add_control(
			'dots_show',
			[
				'label' => __('Navigation Dots', 'thegem'),
				'default' => 'no',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __('Autoplay', 'thegem'),
				'default' => 'no',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_responsive_control(
			'autoplay_speed',
			[
				'label' => __('Autoplay Speed', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 500,
					],
				],
				'default' => [
					'size' => 0,
				],
				'condition' => [
					'autoplay' => 'yes',
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

		/*Image Styles*/
		$this->image_styles($control);

		/*Title & Description Styles*/
		$this->tile_description_styles($control);

		/*Slider Arrows Styles*/
		$this->slider_arrows_styles($control);

		/*Thumbnail Arrows Styles*/
		$this->thumbnail_arrows_styles($control);

		/*Navigation Dots Styles*/
		$this->navigation_dots_styles($control);
	}

	/**
	 * Image Styles
	 * @access protected
	 */
	protected function image_styles($control) {
		$control->start_controls_section(
			'image_style',
			[
				'label' => __('Image Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .gem-gallery-preview-carousel-wrap .gem-gallery-item a img' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-gallery-preview-carousel-wrap .gem-gallery-item a img',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'image_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$control->add_control(
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
					// 'circular' => __('Circular Overlay', 'thegem'),
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_background_hover',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'default' => 'classic',
				'fields_options' => [
					'background' => [
						'label' => _x('Overlay Type', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .gem-gallery-preview-carousel-wrap .gem-gallery-item a:before' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .gem-gallery-thumbs-carousel-wrap .active .gem-gallery-item-image a:before' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .gem-gallery-thumbs-carousel-wrap .gem-gallery-item svg' => 'fill: {{VALUE}};',
							'{{WRAPPER}} .gem-gallery-thumbs-carousel-wrap .gem-gallery-item .gem-gallery-item-image a:hover:before' => 'background-color: {{VALUE}};',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .gem-gallery-preview-carousel-wrap .gem-gallery-item a:before' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
							'{{WRAPPER}} .gem-gallery-thumbs-carousel-wrap .active .gem-gallery-item-image a:before' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
							'{{WRAPPER}} .gem-gallery-thumbs-carousel-wrap .gem-gallery-item svg' => 'fill: {{color.VALUE}};',
							'{{WRAPPER}} .gem-gallery-thumbs-carousel-wrap .gem-gallery-item .gem-gallery-item-image a:hover:before' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .gem-gallery-preview-carousel-wrap .gem-gallery-item a:before' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
							'{{WRAPPER}} .gem-gallery-thumbs-carousel-wrap .active .gem-gallery-item-image a:before' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
							'{{WRAPPER}} .gem-gallery-thumbs-carousel-wrap .gem-gallery-item svg' => 'fill: {{color.VALUE}};',
							'{{WRAPPER}} .gem-gallery-thumbs-carousel-wrap .gem-gallery-item .gem-gallery-item-image a:hover:before' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
						],
					],
				],
			]
		);

		$control->remove_control('image_background_hover_image');

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_hover',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-gallery-preview-carousel-wrap .gem-gallery-item a:hover img',
			]
		);

		$control->add_control(
			'icon_header',
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
				'default' => [
					'value' => 'fas fa-camera',
					'library' => 'fa-solid',
				],
				'condition' => [
					'icon_show' => 'yes',
					'hover_effect' => ['default'],
				],
			]
		);

		$control->add_control(
			'icon_alt',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'gem-elegant icon-camera-alt',
					'library' => 'thegem-elegant',
				],
				'condition' => [
					'icon_show' => 'yes',
					'hover_effect!' => ['default'],
				],
			]
		);

		$control->add_responsive_control(
			'icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'icon_show' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-carousel-wrap .gem-gallery-item i' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .gem-gallery-preview-carousel-wrap .gem-gallery-item i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};
					font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
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
				'size_units' => ['deg'],
				'range' => [
					'deg' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-carousel-wrap .gem-gallery-item i' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
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
	 * Title & Description Styles
	 * @access protected
	 */
	protected function tile_description_styles($control) {
		$control->start_controls_section(
			'tile_description_style',
			[
				'label' => __('Title & Description', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'tile_description_typo',
				'selector' => '{{WRAPPER}} .gem-gallery-item .gem-gallery-item-title, {{WRAPPER}} .gem-gallery-item .gem-gallery-item-description',
			]
		);

		$control->add_control(
			'tile_description_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-item .gem-gallery-item-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .gem-gallery-item .gem-gallery-item-description' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Slider Arrows Styles
	 * @access protected
	 */
	protected function slider_arrows_styles($control) {
		$control->start_controls_section(
			's_arrows_style',
			[
				'label' => __('Slider Arrows', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			's_arrow_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						// 'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev, {{WRAPPER}} .gem-gallery-preview-next' => 'width: {{SIZE}}px;',
					'{{WRAPPER}} .gem-gallery-preview-prev:after, {{WRAPPER}} .gem-gallery-preview-next:after' => 'margin-left: calc(-{{SIZE}}px/2); margin-top: calc(-{{SIZE}}px/2); width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			's_arrow_icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						// 'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:after, {{WRAPPER}} .gem-gallery-preview-next:after' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			's_arrow_icon_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:after, {{WRAPPER}} .gem-gallery-preview-next:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 's_arrow_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-gallery-preview-prev:after, {{WRAPPER}} .gem-gallery-preview-next:after',
			]
		);

		$control->remove_control('s_arrow_border_color');

		$control->add_responsive_control(
			's_arrow_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:after, {{WRAPPER}} .gem-gallery-preview-next:after' => 'top: calc(50% - {{SIZE}}px);',
				],
			]
		);

		$control->add_responsive_control(
			's_arrow_side_spacing',
			[
				'label' => __('Side Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:after' => 'left: calc(50% + {{SIZE}}px);',
					'{{WRAPPER}} .gem-gallery-preview-next:after' => 'left: calc(50% - {{SIZE}}px);',
				],
			]
		);

		$control->start_controls_tabs('s_arrow_tabs');
		$control->start_controls_tab('s_arrow_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			's_arrow_bg_color_normal',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:after, {{WRAPPER}} .gem-gallery-preview-next:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			's_arrow_border_color_normal',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:after, {{WRAPPER}} .gem-gallery-preview-next:after' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			's_arrow_icon_color_normal',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:after, {{WRAPPER}} .gem-gallery-preview-next:after' => 'color: {{VALUE}}!important;',
				],
			]
		);


		$control->end_controls_tab();

		$control->start_controls_tab(
			's_arrow_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$control->add_responsive_control(
			's_arrow_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:hover:after, {{WRAPPER}} .gem-gallery-preview-next:hover:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			's_arrow_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:hover:after, {{WRAPPER}} .gem-gallery-preview-next:hover:after' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			's_arrow_icon_color_hover',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-prev:hover:after, {{WRAPPER}} .gem-gallery-preview-next:hover:after' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Thumbnail Arrows Styles
	 * @access protected
	 */
	protected function thumbnail_arrows_styles($control) {
		$control->start_controls_section(
			't_arrows_style',
			[
				'label' => __('Thumbnail Arrows', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'thumbs_show' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			't_arrow_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'before',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-thumbs-prev, {{WRAPPER}} .gem-gallery-thumbs-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 't_arrow_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-gallery-thumbs-prev, {{WRAPPER}} .gem-gallery-thumbs-next',
			]
		);

		$control->remove_control('t_arrow_border_color');

		$control->start_controls_tabs('t_arrow_tabs');
		$control->start_controls_tab('t_arrow_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			't_arrow_bg_color_normal',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery .gem-gallery-thumbs-prev:after, {{WRAPPER}} .gem-gallery .gem-gallery-thumbs-next:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			't_arrow_border_color_normal',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-thumbs-prev, {{WRAPPER}} .gem-gallery-thumbs-next' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			't_arrow_icon_color_normal',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-thumbs-prev:after, {{WRAPPER}} .gem-gallery-thumbs-next:after' => 'color: {{VALUE}}!important;',
				],
			]
		);


		$control->end_controls_tab();

		$control->start_controls_tab(
			't_arrow_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);

		$control->add_responsive_control(
			't_arrow_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-thumbs-prev:hover:after, {{WRAPPER}} .gem-gallery-thumbs-next:hover:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			't_arrow_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-thumbs-prev:hover, {{WRAPPER}} .gem-gallery-thumbs-next:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			't_arrow_icon_color_hover',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-thumbs-prev:hover:after, {{WRAPPER}} .gem-gallery-thumbs-next:hover:after' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Navigation Dots Styles
	 * @access protected
	 */
	protected function navigation_dots_styles($control) {
		$control->start_controls_section(
			'dots_style',
			[
				'label' => __('Navigation Dots', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'dots_show' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'dots_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						// 'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-pagination a' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'dots_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						// 'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-pagination' => 'padding-top: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'dots_space_between',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						// 'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-pagination a' => 'margin-left: calc({{SIZE}}px/2); margin-right: calc({{SIZE}}px/2);',
				],
			]
		);

		$control->start_controls_tabs('dots_tabs');
		$control->start_controls_tab('dots_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'dots_bg_color_normal',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-pagination a:not(.selected)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'dots_border_normal',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-gallery-preview-pagination a:not(.selected)',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'dots_active',
			[
				'label' => __('Active', 'thegem'),
			]
		);

		$control->add_responsive_control(
			'dots_bg_color_active',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-gallery-preview-pagination a.selected' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'dots_border_active',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-gallery-preview-pagination a.selected',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

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

		$this->add_render_attribute('main-slider-wrap', 'class',
			[
				'gem-gallery',
				'gem-gallery-hover-' . $settings['hover_effect'],
				('yes' === $settings['thumbs_show'] ? '' : ' no-thumbs'),
				('yes' === $settings['dots_show'] ? ' with-pagination' : '')
			]);

		if ('yes' === $settings['autoplay']) {
			$this->add_render_attribute('main-slider-wrap', 'data-autoscroll', $settings['autoplay_speed']['size']);
		}

		if ('default' === $settings['hover_effect']) {
			$icon = $settings['icon'];
		} else {
			$icon = $settings['icon_alt'];
		}

		$gallery_uid = uniqid();

		?>

		<div class="preloader">
			<div class="preloader-spin"></div>
		</div>
		<div <?php echo $this->get_render_attribute_string('main-slider-wrap'); ?>>
			<?php foreach ($settings['slides_list'] as $index => $item) : ?>
				<?php if (!empty($item['slide_image'])) :
					$thumb_image_url = thegem_generate_thumbnail_src($item['slide_image']['id'], 'thegem-post-thumb-small');
					$preview_image_url = thegem_generate_thumbnail_src($item['slide_image']['id'], 'thegem-gallery-' . esc_attr($settings['aspect_ratio']));

					$preset_path = __DIR__ . '/templates/content-gallery-item.php';
					$preset_path_filtered = apply_filters('thegem_gallery_slider_item_preset', $preset_path);
					$preset_path_theme = get_stylesheet_directory() . '/templates/gallery-slider/content-gallery-item.php';

					if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
						include($preset_path_theme);
					} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
						include($preset_path_filtered);
					}
					?>

				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php if (is_admin()): ?>
			<script type="text/javascript">
				jQuery('.elementor-element-<?php echo $this->get_id(); ?> .gem-gallery').initGalleries();
				jQuery('body').updateGalleries();
			</script>
		<?php endif;
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new TheGem_GallerySlider());
