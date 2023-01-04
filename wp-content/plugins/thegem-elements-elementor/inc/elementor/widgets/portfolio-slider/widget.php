<?php

namespace TheGem_Elementor\Widgets\PortfolioSlider;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;

use WP_Query;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Portfolio Slider.
 */
class TheGem_PortfolioSlider extends Widget_Base {

	public $states_list;

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PORTFOLIOSLIDER_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_PORTFOLIOSLIDER_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PORTFOLIOSLIDER_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_PORTFOLIOSLIDER_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-portfolio-slider',
			THEGEM_ELEMENTOR_WIDGET_PORTFOLIOSLIDER_URL . '/assets/css/portfolio-slider.css',
			array(
				'thegem-portfolio-styles',
				'thegem-animations',
				'thegem-hovers',
			),
			null
		);

		wp_register_script(
			'thegem-juraSlider',
			THEGEM_ELEMENTOR_WIDGET_PORTFOLIOSLIDER_URL . '/assets/js/jquery.juraSlider.js', null, null, true);

		wp_register_script(
			'thegem-portfolio-slider',
			THEGEM_ELEMENTOR_WIDGET_PORTFOLIOSLIDER_URL . '/assets/js/thegem-portfolio.js',
			array(
				'jquery',
				'thegem-juraSlider',
				// 'thegem-items-animations'
			),
			'1.0.0',
			true
		);

		$this->states_list = [
			'normal' => __('Normal', 'thegem'),
			'hover' => __('Hover', 'thegem'),
		];
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-portfolio-slider';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Portfolio Slider', 'thegem');
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
		return ['thegem_portfolios'];
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
				'thegem-portfolio-slider'];
		}
		return ['thegem-portfolio-slider'];
	}

	public function get_script_depends() {
		return ['thegem-portfolio-slider'];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
	}


	/**
	 * Make options select portfolio sets
	 * @access protected
	 * @return array
	 */
	protected function select_portfolio_sets() {
		$out = ['0' => __('All', 'thegem')];
		$terms = get_terms([
			'taxonomy' => 'thegem_portfolios',
			'hide_empty' => true,
		]);

		if (empty($terms) || is_wp_error($terms)) {
			return $out;
		}

		foreach ((array)$terms as $term) {
			if (!empty($term->name)) {
				$out[$term->slug] = $term->name;
			}
		}

		return $out;
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
			'columns',
			[
				'label' => __('Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => '3x',
				'options' => [
					'3x' => __('3x columns', 'thegem'),
					'100%' => __('100% width', 'thegem'),
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
					'3' => __('3 Columns', 'thegem'),
					'4' => __('4 Columns', 'thegem'),
					'5' => __('5 Columns', 'thegem'),
				],
				'condition' => [
					'columns' => '100%',
				],
				'description' => __(' Number of columns for 100% width portfolio slider 
					for desktop resolutions starting from 1920 px and above', 'thegem'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_portfolios',
			[
				'label' => __('Portfolios', 'thegem'),
			]
		);

		$this->add_control(
			'content_portfolios_cat',
			[
				'label' => __('Select Portfolios', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->select_portfolio_sets(),
				'frontend_available' => true,
			]
		);

		$portfolio_fields = [
			'title' => __('Title', 'thegem'),
			'description' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'sets' => __('Category', 'thegem'),
			'likes' => __('Likes', 'thegem'),
		];

		foreach ($portfolio_fields as $ekey => $elem) {

			$condition = [];

			if ($ekey == 'likes') {
				$condition = [
					'caption_position' => 'page',
				];
			}

			$this->add_control(
				'portfolio_show_' . $ekey, [
					'label' => $elem,
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('Show', 'thegem'),
					'label_off' => __('Hide', 'thegem'),
					'frontend_available' => true,
					'condition' => $condition,
				]
			);

		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_caption',
			[
				'label' => __('Caption', 'thegem'),
			]
		);

		$this->add_control(
			'caption_position',
			[
				'label' => __('Caption Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'page',
				'options' => [
					'page' => __('Below Image', 'thegem'),
					'hover' => __('On Hover', 'thegem'),
					'image' => __('On Image', 'thegem'),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sharing',
			[
				'label' => __('Social Sharing', 'thegem'),
			]
		);

		$this->add_control(
			'social_sharing',
			[
				'label' => __('Social Sharing', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$share_options = [
			'facebook' => __('Facebook', 'thegem'),
			'twitter' => __('Twitter', 'thegem'),
			'pinterest' => __('Pinterest', 'thegem'),
			'tumblr' => __('Tumblr', 'thegem'),
			'linkedin' => __('Linkedin', 'thegem'),
			'reddit' => __('Reddit', 'thegem'),
		];

		foreach ($share_options as $ekey => $elem) {

			$this->add_control(
				'shares_show_' . $ekey, [
					'label' => $elem,
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('Show', 'thegem'),
					'label_off' => __('Hide', 'thegem'),
					'frontend_available' => true,
					'condition' => [
						'social_sharing' => 'yes',
					],
				]
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_animations',
			[
				'label' => __('Animations', 'thegem'),
			]
		);

		$this->add_control(
			'animation_effect',
			[
				'label' => __('Sliding Animation', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'dynamic',
				'options' => [
					'dynamic' => __('Dynamic Slide', 'thegem'),
					'one' => __('One-by-one', 'thegem'),
				],
			]
		);

		$this->add_control(
			'loading_animation',
			[
				'label' => __('Lazy Loading Animation', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);		

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional',
			[
				'label' => __('Additional Options', 'thegem'),
			]
		);

		$this->add_control(
			'autoscroll',
			[
				'label' => __('Autoscroll', 'thegem'),
				'default' => 'no',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_responsive_control(
			'autoscroll_speed',
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
					'autoscroll' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_show',
			[
				'label' => __('Arrows Bar', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
			]
		);

		$this->add_control(
			'left_icon_full',
			[
				'label' => __('Left Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'gem-mdi mdi-arrow-left',
					'library' => 'thegem-mdi',
				],
				'condition' => [
					'arrows_show' => 'yes',
					'columns' => '100%',
				],
			]
		);

		$this->add_control(
			'right_icon_full',
			[
				'label' => __('Right Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'gem-mdi mdi-arrow-right',
					'library' => 'thegem-mdi',
				],
				'condition' => [
					'arrows_show' => 'yes',
					'columns' => '100%',
				],
			]
		);

		$this->add_control(
			'left_icon_3x',
			[
				'label' => __('Left Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'gem-mdi mdi-chevron-left',
					'library' => 'thegem-mdi',
				],
				'condition' => [
					'arrows_show' => 'yes',
					'columns' => '3x',
				],
			]
		);

		$this->add_control(
			'right_icon_3x',
			[
				'label' => __('Right Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'gem-mdi mdi-chevron-right',
					'library' => 'thegem-mdi',
				],
				'condition' => [
					'arrows_show' => 'yes',
					'columns' => '3x',
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

		/* Images Style */
		$this->image_style($control);

		/* Caption Style */
		$this->caption_style($control);

		/* Caption Container Style */
		$this->caption_container_style($control);

		/* Arrows Style */
		$this->arrows_style($control);

		/* Likes Style */
		$this->likes_style($control);

		/* Hover Icons (Custom) */
		$this->hover_icons_style($control);
	}

	/**
	 * Images Style
	 * @access protected
	 */
	protected function image_style($control) {
		$control->start_controls_section(
			'image_style',
			[
				'label' => __('Images Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'image_gaps',
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
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portolio-slider-center>div>div' => 'margin: calc(-{{SIZE}}{{UNIT}}/2) ;',
					'{{WRAPPER}} .portfolio-slider .portfolio-item' => 'padding: calc({{SIZE}}{{UNIT}}/2);',
				]
			]
		);

		$control->add_control(
			'image_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio-slider .portfolio-item .wrap' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.portfolio-slider:not(.shadowed-container) .portfolio-item .image, 
				{{WRAPPER}} .portfolio.portfolio-slider.shadowed-container .portfolio-item .wrap',
			]
		);

		$control->add_control(
			'shadowed_container',
			[
				'label' => __('Apply shadow on caption container', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'caption_position',
							'operator' => '=',
							'value' => 'page',
						],
						[
							'name' => 'image_shadow_box_shadow_type',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$control->start_controls_tabs('image_tabs');
		$control->start_controls_tab('image_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
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
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image-inner' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-slider .portfolio-item .image-inner',
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('image_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$this->add_control(
			'image_hover_effect_hover',
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
				'condition' => ['caption_position' => 'hover',],
			]
		);

		$this->add_control(
			'image_hover_effect_page',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Cyan Breeze', 'thegem'),
					'zooming-blur' => __('Zooming White', 'thegem'),
					'horizontal-sliding' => __('Horizontal Sliding', 'thegem'),
					'vertical-sliding' => __('Vertical Sliding', 'thegem'),
				],
				'condition' => ['caption_position' => 'page',],
			]
		);

		$this->add_control(
			'image_hover_effect_image',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'gradient',
				'options' => [
					'gradient' => __('Gradient', 'thegem'),
					'circular' => __('Circular Overlay', 'thegem'),
				],
				'condition' => ['caption_position' => 'image',],
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
							'{{WRAPPER}} .portfolio-slider .portfolio-item .overlay:before, .portfolio-slider.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio-slider .portfolio-item .overlay:before, .portfolio-slider.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio-slider .portfolio-item .overlay:before, .portfolio-slider.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				]

			]
		);

		$control->remove_control('image_hover_overlay_image');

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_hover_css',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-slider .portfolio-item:hover .image-inner',
			]
		);

		$control->add_control(
			'icons_header',
			[
				'label' => __('Icons', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'icons_show',
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
			'icons_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image .overlay .links a.icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image .overlay .links .overlay-line:after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .image .overlay .links .socials-item-icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'icons_show' => 'yes',
				],
			]
		);


		$control->add_control(
			'icons_size',
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
					'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item:not(.hover-zooming-blur) .image .overlay .links a.icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item:not(.hover-zooming-blur) .image .overlay .links a.icon i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item:not(.hover-zooming-blur) .image .overlay .links a.icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-slider.hover-zooming-blur .portfolio-item .image .overlay .links a.icon, {{WRAPPER}} .portfolio.portfolio-slider.hover-gradient .portfolio-item .image .overlay .links a.icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-slider.hover-zooming-blur .portfolio-item .image .overlay .links a.icon i, {{WRAPPER}} .portfolio.portfolio-slider.hover-gradient .portfolio-item .image .overlay .links a.icon i' => 'font-size: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.portfolio-slider.hover-zooming-blur .portfolio-item .image .overlay .links a.icon svg, {{WRAPPER}} .portfolio.portfolio-slider.hover-gradient .portfolio-item .image .overlay .links a.icon svg' => 'width: calc({{SIZE}}{{UNIT}}/2); height: calc({{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'icons_show' => 'yes',
				],
			]
		);


		$control->add_control(
			'icons_customize',
			[
				'label' => __('Want to customize?', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'icons_show' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

		/**
	 * Caption Style
	 * @access protected
	 */
	protected function caption_style($control) {
		$control->start_controls_section(
			'caption_style',
			[
				'label' => __('Caption Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$caption_options = [
			'title' => __('Title', 'thegem'),
			'subtitle' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'set' => __('Category', 'thegem'),
		];

		foreach ($caption_options as $ekey => $elem) {

			if ($ekey == 'set') {
				$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .overlay  .caption .' . $ekey . ', {{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .overlay .caption .info .set a';
			} else {
				$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .overlay  .caption .' . $ekey;
			}

			$control->add_control(
				$ekey . '_header',
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$control->add_group_control(Group_Control_Typography::get_type(),
				[
					'label' => __('Typography', 'thegem'),
					'name' => $ekey . '_typography',
					'selector' => $selector,
					'condition' => [
						'caption_position' => ['hover', 'image'],
					]
				]
			);

			$control->add_control(
				$ekey . '_color',
				[
					'label' => __('Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						$selector => 'color: {{VALUE}};',
					],
					'condition' => [
						'caption_position' => ['hover', 'image'],
					]
				]
			);

			if ($ekey == 'set') {

				$this->add_control(
					'category_in_text',
					[
						'label' => __('"in" Text', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'input_type' => 'text',
						'default' => __('in', 'thegem'),
						'condition' => [
							'caption_position' => ['hover'],
						]
					]
				);

				$control->add_control(
					'category_in_color',
					[
						'label' => __('"in" Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .caption .info .set .in_text' => 'color: {{VALUE}};',
						],
						'condition' => [
							'caption_position' => ['hover'],
						]
					]
				);


			}

			$control->start_controls_tabs($ekey . '_tabs', [
				'condition' => [
					'caption_position' => ['page'],
				],
			]);

			if (!empty($control->states_list)) {
				foreach ((array)$control->states_list as $stkey => $stelem) {
					$condition = [];
					$state = '';
					if ($stkey == 'active') {
						continue;
					} else if ($stkey == 'hover') {
						$condition = ['caption_position' => 'page'];
						$state = ':hover';
					}
					if ($ekey == 'set') {
						$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item' . $state . ' .caption .info .set a';
					} else {
						$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item' . $state . ' .caption .' . $ekey;
					}

					$control->start_controls_tab($ekey . '_tab_' . $stkey, [
						'label' => $stelem,
						'condition' => $condition
					]);

					$control->add_group_control(Group_Control_Typography::get_type(),
						[
							'label' => __('Typography', 'thegem'),
							'name' => $ekey . '_typography_' . $stkey,
							'selector' => $selector,
							'condition' => $condition
						]
					);

					$control->add_control(
						$ekey . '_color_' . $stkey,
						[
							'label' => __('Color', 'thegem'),
							'type' => Controls_Manager::COLOR,
							'label_block' => false,
							'selectors' => [
								$selector => 'color: {{VALUE}};',
							],
							'condition' => $condition
						]
					);

					if ($ekey == 'set' && $stkey == 'normal') {

						$this->add_control(
							'category_in_text_page',
							[
								'label' => __('"in" Text', 'thegem'),
								'type' => Controls_Manager::TEXT,
								'input_type' => 'text',
								'default' => __('in', 'thegem'),
							]
						);

						$control->add_control(
							'category_in_color_page',
							[
								'label' => __('"in" Color', 'thegem'),
								'type' => Controls_Manager::COLOR,
								'label_block' => false,
								'selectors' => [
									'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .caption .info .set .in_text' => 'color: {{VALUE}};',
								],
								'condition' => $condition
							]
						);


					}

					$control->end_controls_tab();

				}
			}

			$control->end_controls_tabs();
		}

		$control->end_controls_section();
	}

	/**
	 * Caption Container Style
	 * @access protected
	 */
	protected function caption_container_style($control) {
		$control->start_controls_section(
			'caption_container_style',
			[
				'label' => __('Caption Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['caption_position' => 'page']
			]
		);

		$control->add_control(
			'caption_container_preset',
			[
				'label' => __('Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'white',
				'options' => [
					'white' => __('White', 'thegem'),
					'gray ' => __('Gray', 'thegem'),
					'dark' => __('Dark', 'thegem'),
				]
			]
		);

		$control->add_control(
			'caption_container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .wrap > .caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.portfolio-slider.title-on-page .portfolio-item .wrap' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'caption_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portfolio-item .wrap > .caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'caption_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-slider .portfolio-item .wrap > .caption',
			]
		);

		$control->start_controls_tabs('caption_container_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('caption_container_tab_' . $stkey, ['label' => $stelem]);

				$control->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name' => 'caption_container_background_' . $stkey,
						'label' => __('Background Type', 'thegem'),
						'types' => ['classic', 'gradient'],
						'selector' => '{{WRAPPER}} .portfolio-slider .portfolio-item'.$state.' .wrap > .caption',
					]
				);
				$control->remove_control('image_hover_overlay_image');

				$control->remove_control('caption_container_background_'. $stkey.'_image');

				$control->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'caption_container_border_' . $stkey,
						'label' => __('Border', 'thegem'),
						'fields_options' => [
							'color' => [
								'default' => '#dfe5e8',
							],
						],
						'selector' => '{{WRAPPER}} .portfolio-slider .portfolio-item'.$state.' .wrap > .caption',
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'spacing_title_header',
			[
				'label' => 'Title',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'spacing_title',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => ['bottom'],
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portfolio-item .wrap > .caption .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'spacing_description_header',
			[
				'label' => 'Description',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'spacing_description',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portfolio-item .wrap > .caption .subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$control->add_control(
			'spacing_separator_header',
			[
				'label' => 'Separator',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'spacing_separator_weight',
			[
				'label' => __('Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
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
					'size' => 1,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portfolio-item .wrap > .caption .caption-separator' => 'height: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->start_controls_tabs('spacing_separator_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				if ($stkey == 'active') {
					continue;
				}
				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('spacing_separator_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'spacing_separator_color' . $stkey,
					[
						'label' => __('Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-slider .portfolio-item'.$state.' .wrap > .caption .caption-separator' => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'spacing_separator_size' . $stkey,
					[
						'label' => __('Size', 'thegem'),
						'type' => Controls_Manager::SLIDER,
						'size_units' => ['px', '%', 'rem', 'em'],
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
						'selectors' => [
							'{{WRAPPER}} .portfolio-slider .portfolio-item'.$state.' .wrap > .caption .caption-separator' => 'width: {{SIZE}}{{UNIT}}',
						]
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Arrows Style
	 * @access protected
	 */
	protected function arrows_style($control) {

		$control->start_controls_section(
			'arrows_style',
			[
				'label' => __('Arrows Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'arrow_preset_3x',
			[
				'label' => __('Size Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'small' => __('Small', 'thegem'),
					'big' => __('Big', 'thegem'),
				],
				'condition' => [
					'columns' => '3x',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_preset_100',
			[
				'label' => __('Size Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'big',
				'options' => [
					'small' => __('Small', 'thegem'),
					'big' => __('Big', 'thegem'),
				],
				'condition' => [
					'columns' => '100%',
				],
			]
		);

		// $this->add_control(
		// 	'arrow_fullwidth',
		// 	[
		// 		'label' => __('Stretch to fullwidth', 'thegem'),
		// 		'default' => '',
		// 		'type' => Controls_Manager::SWITCHER,
		// 		'label_on' => __('On', 'thegem'),
		// 		'label_off' => __('Off', 'thegem'),
		// 	]
		// );

		$control->add_responsive_control(
			'arrow_icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portolio-slider-prev span i, {{WRAPPER}} .portfolio-slider .portolio-slider-next span i' => 'font-size: {{SIZE}}px;', 
					'{{WRAPPER}} .portfolio-slider .portolio-slider-prev span svg, {{WRAPPER}} .portfolio-slider .portolio-slider-next span svg' => 'width: {{SIZE}}px;', 
				],
			]
		);

		$control->add_control(
			'arrow_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portolio-slider-prev span, {{WRAPPER}} .portfolio-slider .portolio-slider-next span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'arrow_icon_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
                    '{{WRAPPER}} .portfolio-slider .portolio-slider-prev span, {{WRAPPER}} .portfolio-slider .portolio-slider-next span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrow_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-slider .portolio-slider-prev span, {{WRAPPER}} .portfolio-slider .portolio-slider-next span',
			]
		);

		$control->remove_control('arrow_border_color');

		$control->add_responsive_control(
			'arrow_bottom_spacing',
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
					'{{WRAPPER}} .portfolio-slider .portolio-slider-prev, {{WRAPPER}} .portfolio-slider .portolio-slider-next' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'arrow_side_spacing',
			[
				'label' => __('Side Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portolio-slider-prev' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .portfolio-slider .portolio-slider-next' => 'margin-right: {{SIZE}}px;',
				],
			]
		);

		$control->start_controls_tabs('arrow_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('arrow_tab_' . $stkey, ['label' => $stelem]);

				$control->add_responsive_control(
					'arrow_bg_color' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-slider .portolio-slider-prev'.$state.' span, {{WRAPPER}} .portfolio-slider .portolio-slider-next'.$state.' span' => 'background-color: {{VALUE}};',
						],
					]
				);	
		
				$control->add_responsive_control(
					'arrow_border_color' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-slider .portolio-slider-prev'.$state.' span, {{WRAPPER}} .portfolio-slider .portolio-slider-next'.$state.' span' => 'border-color: {{VALUE}};',
						],
					]
				);	
		
				$control->add_responsive_control(
					'arrow_icon_color' . $stkey,
					[
						'label' => __('Icon Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-slider .portolio-slider-prev'.$state.' span i, {{WRAPPER}} .portfolio-slider .portolio-slider-next'.$state.' span i' => 'color: {{VALUE}}!important;',
							'{{WRAPPER}} .portfolio-slider .portolio-slider-prev'.$state.' span svg, {{WRAPPER}} .portfolio-slider .portolio-slider-next'.$state.' span svg' => 'fill: {{VALUE}}!important;',
						],
					]
				);					

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->end_controls_section();

	}


/**
	 * Likes Style
	 * @access protected
	 */
	protected function likes_style($control) {

		$control->start_controls_section(
			'likes_style',
			[
				'label' => __('Likes Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'caption_position' => 'page',
				],
			]
		);

		$control->add_control(
			'likes_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$control->start_controls_tabs('likes_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}

				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('likes_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'likes_icon_color_' . $stkey,
					[
						'label' => __('Icon Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-likes .zilla-likes' . $state . ' i' => 'color: {{VALUE}}; transition: all 0.3s;',
						]
					]
				);

				$control->add_control(
					'likes_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-likes .zilla-likes' . $state => 'color: {{VALUE}};',
						]
					]
				);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'likes_typography_' . $stkey,
						'selector' => '{{WRAPPER}} .portfolio-likes .zilla-likes' . $state,
					]
				);


				$control->end_controls_tab();

			}
		}


		$control->end_controls_section();
	}


	/**
	 * Hover Icons (Custom)
	 * @access protected
	 */
	protected function hover_icons_style($control) {

		$control->start_controls_section(
			'hover_icons_style',
			[
				'label' => __('Hover Icons (Custom) ', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icons_customize' => 'yes',
				]
			]
		);

		$this->add_control(
			'important_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Hover icons depend on types of portfolio item (image, link, video etc.). If some type is not selected, corresponding icon will not appear. ', 'thegem'),
				'content_classes' => 'elementor-control-field-description',
			]
		);

		$icons_list = [
			'share' => __('Sharing Button', 'thegem'),
			'self-link' => __('Portfolio Page', 'thegem'),
			'full-image' => __('Fancybox Image', 'thegem'),
			'inner-link' => __('Internal Link', 'thegem'),
			'outer-link' => __('External Link', 'thegem'),
			'video' => __('Video', 'thegem')
		];

		foreach ($icons_list as $ekey => $elem) {

			$condition = [];

			$add_selector = '';
			if ($ekey == 'share') {
				$condition = [
					'social_sharing' => 'yes'
				];
				$add_selector = ', {{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .image .overlay .links .socials-item-icon';
			}

			$control->add_control(
				'hover_icon_header_' . $ekey,
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'condition' => $condition
				]
			);

			$control->add_control(
				'hover_icon_' . $ekey,
				[
					'label' => __('Icon', 'thegem'),
					'type' => Controls_Manager::ICONS,
					'condition' => $condition
				]
			);

			$control->add_control(
				'hover_icon_color_' . $ekey,
				[
					'label' => __('Icon Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .image .overlay .links a.' . $ekey . ' i' . $add_selector => 'color: {{VALUE}};',
						'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .image .overlay .links a.' . $ekey . ' svg' . $add_selector => 'fill: {{VALUE}};',
					],
					'condition' => $condition
				]
			);

			$control->add_control(
				'hover_icon_rotate_' . $ekey,
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
						'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .image .overlay .links a.' . $ekey . ' i' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
						'{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .image .overlay .links a.' . $ekey . ' svg' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
						'{{WRAPPER}} .portfolio.portfolio-slider.hover-gradient .portfolio-item .image .overlay .links a.' . $ekey . ' svg' => 'transform: translate(-50%, -50%) rotate({{SIZE}}deg); -webkit-transform: translate(-50%, -50%) rotate({{SIZE}}deg);',
					],
					'condition' => $condition
				]
			);

			$control->end_controls_tab();


		}

		$control->end_controls_section();
	}

	public function get_portfolio_posts($portfolios_cat) {
		if (empty($portfolios_cat)) {
			return null;
		}

		$args = array(
			'post_type' => 'thegem_pf_item',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);

		if (!in_array('0', $portfolios_cat, true)) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'thegem_portfolios',
					'field' => 'slug',
					'terms' => $portfolios_cat
				)
			);
		}

		$portfolio_loop = new WP_Query($args);

		return $portfolio_loop;
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

		$terms = $settings['content_portfolios_cat'];

		if ('yes' === $settings['loading_animation']){
			thegem_lazy_loading_enqueue();
		}

		if ($settings['caption_position'] == 'image') {
			$hover_effect = $settings['image_hover_effect_image'];
		} else if ($settings['caption_position'] == 'page') {
			$hover_effect = $settings['image_hover_effect_page'];
		} else {
			$hover_effect = $settings['image_hover_effect_hover'];
		}

		if ($settings['category_in_text']) {
			$in_text = $settings['category_in_text'];
		} else if ($settings['category_in_text_page']) {
			$in_text = $settings['category_in_text_page'];
		} else {
			$in_text = '';
		}

		wp_enqueue_style('thegem-hovers-' . $hover_effect);

		if ($settings['caption_position'] == 'hover') {
			$title_on = 'hover';
		} else {
			$title_on = 'page';
		}

		$this->add_render_attribute(
			'portfolio-wrap',
			[
				'class' => [
					'portfolio portfolio-slider clearfix col-lg-12 col-md-12 col-sm-12',
					($settings['columns'] === '100%' ? 'fullwidth-columns-' . $settings['columns_100'] : 'columns-3'),
					'background-style-' . $settings['caption_container_preset'],
					'portfolio_slider_arrow_' . ($settings['columns'] === '100%' ? $settings['arrow_preset_100'] : $settings['arrow_preset_3x']),
					'hover-' . $hover_effect,
					'title-on-' . $title_on,
					'gem-slider-animation-' . $settings['animation_effect'],
					($settings['shadowed_container'] === 'yes' ? 'shadowed-container' : ''),
					($settings['loading_animation'] === 'yes' ? 'lazy-loading' : ''),
					($settings['caption_position'] === 'image' && $settings['image_hover_effect_image'] === 'gradient' ? 'hover-gradient-title' : ''),
					($settings['caption_position'] === 'image' && $settings['image_hover_effect_image'] === 'circular' ? 'hover-circular-title' : ''),
					($settings['caption_position'] === 'hover' || $settings['caption_position'] === 'image' ? 'hover-title' : ''),
				],
				'data-hover' => $hover_effect,
			]
		);

		if ('yes' === $settings['autoscroll']) {
			$this->add_render_attribute( 'scroll-wrap', 'data-autoscroll', $settings['autoscroll_speed']['size']);
		}	
		
		global $post;

		if ( ! empty ( $terms ) ) {
			$portfolio_loop = $this->get_portfolio_posts($terms);
		} else { ?>
			<div class="bordered-box centered-box styled-subtitle">
					<?php echo __('Please select portfolios in "Portfolios" section', 'thegem') ?>
			</div>
			<?php return;
		}

		if ($portfolio_loop->have_posts()) {

			if (in_array("all", $terms)) {
				$terms = get_terms('thegem_portfolios', array('hide_empty' => false));
			} else {
				foreach ($terms as $key => $term) {
					$terms[$key] = get_term_by('id', $term, 'thegem_portfolios');
					if (!$terms[$key]) {
						unset($terms[$key]);
					}
				}
			} ?>
			<div class="preloader">
				<div class="preloader-spin"></div>
			</div>
			<div <?php echo $this->get_render_attribute_string('portfolio-wrap') . ('yes' === $settings['loading_animation'] ? ' data-ll-item-delay="0"' : ''); ?> >
				<div class="navigation <?php if($settings['columns'] === '100%'): ?>fullwidth-block<?php endif; ?>">
					<?php if ($settings['arrows_show'] === 'yes'): ?>
					<div class="portolio-slider-prev">
						<span>
							<?php if ($settings['columns'] === '100%') {
								if ( ! empty( $settings['left_icon_full']['value'] ) ) { Icons_Manager::render_icon( $settings['left_icon_full'], [ 'aria-hidden' => 'true'] ); }
							} else {
								if ( ! empty( $settings['left_icon_3x']['value'] ) ) { Icons_Manager::render_icon( $settings['left_icon_3x'], [ 'aria-hidden' => 'true'] ); }
							}?>
						</span>
					</div>
					<div class="portolio-slider-next">
						<span>
							<?php if ($settings['columns'] === '100%') {
								if ( ! empty( $settings['right_icon_full']['value'] ) ) { Icons_Manager::render_icon( $settings['right_icon_full'], [ 'aria-hidden' => 'true'] ); }
							} else {
								if ( ! empty( $settings['right_icon_3x']['value'] ) ) { Icons_Manager::render_icon( $settings['right_icon_3x'], [ 'aria-hidden' => 'true'] ); }
							}?>
						</span>
					</div>
					<?php endif; ?>
					<div class="portolio-slider-content">
						<div class="portolio-slider-center">
							<div class="<?php if($settings['columns'] == '100%'): ?>fullwidth-block<?php endif; ?>">
								<div>
									<div class="portfolio-set clearfix" <?php echo $this->get_render_attribute_string('scroll-wrap'); ?>>
									
									<?php while ($portfolio_loop->have_posts()) : $portfolio_loop->the_post(); 
										$slugs = wp_get_object_terms($post->ID, 'thegem_portfolios', array('fields' => 'slugs'));

										$preset_path = __DIR__ . '/templates/content-portfolio-carusel-item.php';
										$preset_path_filtered = apply_filters( 'thegem_portfolio_slider_item_preset', $preset_path);
										$preset_path_theme = get_stylesheet_directory() . '/templates/portfolio-slider/content-portfolio-carusel-item.php';

										if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
											include($preset_path_theme);
										} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
											include($preset_path_filtered);
										}
									endwhile; ?>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		<?php } else {	?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select portfolios in "Portfolios" section', 'thegem') ?>
			</div>
		<?php
		}
		wp_reset_postdata();

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script type="text/javascript">
					jQuery('.elementor-element-<?php echo $this->get_id(); ?> .portfolio.portfolio-slider').initPortfoliosSlider();
					
					elementor.channels.editor.on('change', function (view) {
						var changed = view.elementSettingsModel.changed;

						if (changed.image_gaps || changed.spacing_description || changed.spacing_title) {
							jQuery('.elementor-element-<?php echo $this->get_id(); ?> .portfolio.portfolio-slider').initPortfoliosSlider();
						}
					});
					
            </script>
		<?php endif;
	}
}


\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_PortfolioSlider());

