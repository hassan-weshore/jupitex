<?php

namespace TheGem_Elementor\Widgets\FeaturedPostsSlider;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;

use WP_Query;

if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Featured Posts Slider.
 */
class TheGem_FeaturedPostsSlider extends Widget_Base {

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-featured-posts-slider-style', THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_URL . '/assets/css/thegem-featured-posts-slider.css', array(), NULL);
		wp_register_script('thegem-featured-posts-slider-script', THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_URL . '/assets/js/thegem-featured-posts-slider.js', array('jquery', 'jquery-carouFredSel'), NULL);

		$this->states_list = [
			'normal' => __('Normal', 'thegem'),
			'hover' => __('Hover', 'thegem'),
			'active' => __('Active', 'thegem'),
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
		return 'thegem-featured-posts-slider';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Featured Posts Slider', 'thegem');
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
		return ['thegem_blog'];
	}

	public function get_style_depends() {
		return ['thegem-featured-posts-slider-style'];
	}

	public function get_script_depends() {
		return ['thegem-featured-posts-slider-script'];
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
			'default' => __('Classic', 'thegem'),
			'new' => __('Alternative', 'thegem'),
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

	protected function select_blog_categories() {
		$query_args = array(
			'post_type' => array('post', 'thegem_news'),
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'thegem_show_featured_posts_slider',
					'value' => 1
				)
			),
			'posts_per_page' => -1,
		);

		$query = new WP_Query($query_args);
		$categories = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				foreach (get_the_category() as $category) {
					$categories[$category->cat_ID] = $category;
				}
			}
		}
		wp_reset_postdata();

		$categories = array_values($categories);

		$items = ['0' => __('All', 'thegem')];

		foreach ($categories as $category) {
			$items[$category->slug] = $category->name;
		}

		return $items;
	}

	/**
	 * Get Featured Posts Slider [ if exists ]
	 */
	protected function select_contact_form() {
		$options = array();

		if (function_exists('wpcf7')) {
			$wpcf7_form_list = get_posts(array(
				'post_type' => 'wpcf7_contact_form',
				'showposts' => 999,
			));
			$options[0] = esc_html__('Select a Contact Form', 'thegem');
			if (!empty($wpcf7_form_list) && !is_wp_error($wpcf7_form_list)) {
				foreach ($wpcf7_form_list as $post) {
					$options[$post->ID] = $post->post_title;
				}
			} else {
				$options[0] = esc_html__('Create a Form First', 'thegem');
			}
		}
		return $options;
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
			'fullwidth',
			[
				'label' => __('Stretch to Fullwidth', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'fullheight',
			[
				'label' => __('Fit to Screen', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
				'condition' => [
					'fullwidth' => 'yes'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Featured Posts', 'thegem'),
			]
		);

		if (count($this->select_blog_categories()) < 2) {

			$this->add_control(
				'no_categories',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('No featured posts in no categories were found. Please select some posts as featured using blog post settings in page options', 'thegem'),
					'content_classes' => 'elementor-descriptor',
				]
			);
		} else {
			$this->add_control(
				'categories',
				[
					'label' => __('Select Blog Categories', 'thegem'),
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'options' => $this->select_blog_categories(),
					'frontend_available' => true,
					'label_block' => true,
				]
			);
		}

		$slider_fields = [
			'featured' => __('Featured Image', 'thegem'),
			'title' => __('Title', 'thegem'),
			'excerpt' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'categories' => __('Categories', 'thegem'),
			'author' => __('Author', 'thegem'),
			'author_avatar' => __('Author’s Avatar', 'thegem'),
			'button' => __('“Read More” Button', 'thegem'),
		];

		foreach ($slider_fields as $ekey => $elem) {

			$this->add_control(
				'slider_show_' . $ekey, [
					'label' => $elem,
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('Show', 'thegem'),
					'label_off' => __('Hide', 'thegem'),
					'frontend_available' => true,
				]
			);

			if ($ekey == 'title') {
				$this->add_control(
					'title_style',
					[
						'label' => __('Title Size Preset', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'small' => __('Small', 'thegem'),
							'normal' => __('Normal', 'thegem'),
							'big' => __('Big', 'thegem'),
							'large' => __('Large', 'thegem'),
						],
						'default' => 'normal',
						'condition' => [
							'slider_show_title' => 'yes',
						],
					]
				);
			} else if ($ekey == 'button') {
				$this->add_control(
					'more_button_text',
					[
						'label' => __('Button Text', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'input_type' => 'text',
						'default' => __('Read More', 'thegem'),
						'condition' => [
							'slider_show_button' => 'yes',
						],
					]
				);

				$this->add_control(
					'more_button_icon',
					[
						'label' => __('Button Icon', 'thegem'),
						'type' => Controls_Manager::ICONS,
						'condition' => [
							'slider_show_button' => 'yes',
						],
					]
				);

				$this->add_control(
					'more_button_text_weight',
					[
						'label' => __('Text Weight', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'normal' => __('Normal', 'thegem'),
							'thin' => __('Thin', 'thegem'),
						],
						'default' => 'normal',
						'condition' => [
							'slider_show_button' => 'yes',
						],
					]
				);
			}
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			[
				'label' => __('Navigation & Sorting', 'thegem'),
			]
		);

		$this->add_responsive_control(
			'max_posts',
			[
				'label' => __('Max. number of posts in slider', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => 3,
				'description' => __('Use -1 to show all', 'thegem'),
			]
		);

		$this->add_control(
			'order_by',
			[
				'label' => __('Posts sorting in slider', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'date_desc' => __('Date descending', 'thegem'),
					'date_asc' => __('Date ascending', 'thegem'),
				],
				'default' => 'date_desc',
			]
		);

		$this->add_control(
			'slider_show_navigation',
			[
				'label' => __('Navigation', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'paginator_type',
			[
				'label' => __('Navigation Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'arrows' => __('Arrows', 'thegem'),
					'bullets' => __('Navigation Dots', 'thegem'),
				],
				'default' => 'arrows',
				'condition' => [
					'slider_show_navigation' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_arrows',
			[
				'label' => __('Arrows', 'thegem'),
				'condition' => [
					'slider_show_navigation' => 'yes',
					'paginator_type' => 'arrows',
				],
			]
		);

		$this->add_control(
			'paginator_icon',
			[
				'label' => __('Arrows Icons Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => __('Arrows Style 1', 'thegem'),
					'2' => __('Arrows Style 2', 'thegem'),
					'3' => __('Arrows Style 3', 'thegem'),
					'4' => __('Arrows Style 4', 'thegem'),
					'5' => __('Arrows Style 5', 'thegem'),
				],
				'default' => '1',
				'condition' => [
					'custom_arrows!' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_arrows',
			[
				'label' => __('Custom Arrows Icons', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'left_arrow_icon',
			[
				'label' => __('Left Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'right_arrow_icon',
			[
				'label' => __('Right Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_bullets',
			[
				'label' => __('Navigation Dots', 'thegem'),
				'condition' => [
					'slider_show_navigation' => 'yes',
					'paginator_type' => 'bullets',
				],
			]
		);

		$this->add_control(
			'paginator_dots_size',
			[
				'label' => __('Dots Size Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'regular' => __('Regular', 'thegem'),
					'large' => __('Large', 'thegem'),
				],
				'default' => 'regular',
			]
		);

		$this->add_control(
			'paginator_dots_style',
			[
				'label' => __('Dots Style Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'light' => __('Light', 'thegem'),
					'dark' => __('Dark', 'thegem'),
				],
				'default' => 'light',
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
			'sliding_effect',
			[
				'label' => __('Sliding effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'slide' => __('Slide', 'thegem'),
					'fade' => __('Fade', 'thegem'),
				],
				'default' => 'slide',
			]
		);

		$this->add_control(
			'auto_scroll',
			[
				'label' => __('Autoscroll', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_responsive_control(
			'auto_scroll_speed',
			[
				'label' => __('Autoscroll Speed', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 100,
				'description' => __('Speed in Milliseconds, example - 5000', 'thegem'),
				'condition' => [
					'auto_scroll' => 'yes',
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

		/* Image Container Style */
		$this->image_container_style($control);

		/* Caption Style */
		$this->caption_style($control);

		/* Read More Button Style */
		$this->more_button_style($control);

		/* Arrows Style */
		$this->arrows_style($control);

		/* Navigation Dots Style */
		$this->bullets_style($control);
	}

	/**
	 * Image Container Style
	 * @access protected
	 */
	protected function image_container_style($control) {

		$control->start_controls_section(
			'image_container_style_section',
			[
				'label' => __('Image Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'container_height',
			[
				'label' => __( 'Height', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'render_type' => 'template',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slide-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'content_alignment',
			[
				'label' => __('Content Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slide-item' => 'text-align: {{VALUE}};',
//					'{{WRAPPER}} .gem-featured-post-meta-author .author' => 'margin-{{VALUE}}: 0;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_container_background',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'background' => [
						'label' => _x('Background ', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .slide-item' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .slide-item' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .slide-item' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				],
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);
		$control->remove_control('image_container_background_image');

		$control->add_responsive_control(
			'image_container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .slide-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_container_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .slide-item',
			]
		);

		$control->add_responsive_control(
			'image_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .slide-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		/*	$control->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'image_container_shadow',
					'label' => __('Shadow', 'thegem'),
					'selector' => '{{WRAPPER}} .slide-item',
				]
			);*/

		$control->add_control(
			'image_heading',
			[
				'label' => __('Image Overlay', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'image_opacity',
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
					'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'opacity: calc({{SIZE}}/100);',
				],
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_overlay',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'background' => [
						'label' => _x('Background ', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				],
			]
		);
		$control->remove_control('image_overlay_image');

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-featured-posts-slide-overlay',
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);

		$control->add_control(
			'image_blend_mode',
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
					'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'mix-blend-mode: {{VALUE}}',
				],
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);

		$control->add_control(
			'title_heading',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_title' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'title_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_title' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'title_bottom_spacing',
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
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_title' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'title_max_width',
			[
				'label' => __('Title Max Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1920,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-title div' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_title' => 'yes',
				],
			]
		);

		$control->add_control(
			'description_heading',
			[
				'label' => __('Description', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_excerpt' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'description_bottom_spacing',
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
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_excerpt' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'description_max_width',
			[
				'label' => __('Description Max Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1920,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-excerpt div' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_excerpt' => 'yes',
				],
			]
		);

		$control->add_control(
			'categories_heading',
			[
				'label' => __('Categories', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_categories' => 'yes',
					'thegem_elementor_preset' => 'default',
				],
			]
		);

		$control->add_responsive_control(
			'categories_bottom_spacing',
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
				'selectors' => [
					'{{WRAPPER}} .with-label .gem-featured-post-meta-categories' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_categories' => 'yes',
					'thegem_elementor_preset' => 'default',
				],
			]
		);

		$control->add_control(
			'date_heading',
			[
				'label' => __('Date', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_date' => 'yes',
					'thegem_elementor_preset' => 'new',
				],
			]
		);

		$control->add_responsive_control(
			'date_bottom_spacing',
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
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-date' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_date' => 'yes',
					'thegem_elementor_preset' => 'new',
				],
			]
		);

		$control->add_control(
			'author_heading',
			[
				'label' => __('Author', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_author' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'author_bottom_spacing',
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
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-meta-author' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_author' => 'yes',
				],
			]
		);

		$control->end_controls_section();
	}

	/* Repeatable Text Style Controls for Caption Style */

	protected function caption_text_controls($control, $ekey, $hover = false) {
		if ($hover) {
			$hover_name = '_hover';
			$hover_selector = ':hover';
		} else {
			$hover_name = '';
			$hover_selector = '';
		}

		if ($ekey == 'categories') {
			$selector = '.slide-item .gem-featured-post-meta-categories span';
		} else if ($ekey == 'author') {
			$selector = '.gem-featured-post-meta-author .author .author-name';
		} else {
			$selector = '{{WRAPPER}} .gem-featured-post-' . $ekey;
		}

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'caption_typography_' . $ekey . $hover_name,
				'label' => __('Typography', 'thegem'),
				'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => $selector . $hover_selector,
				'condition' => [
					'slider_show_' . $ekey => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_color_' . $ekey . $hover_name,
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$selector . $hover_selector => 'color: {{VALUE}}',
				],
				'condition' => [
					'slider_show_' . $ekey => 'yes',
				],
			]
		);
	}

	/**
	 * Caption Style
	 * @access protected
	 */
	protected function caption_style($control) {

		$control->start_controls_section(
			'caption_style_section',
			[
				'label' => __('Caption Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$caption_fields = [
			'title' => __('Title', 'thegem'),
			'excerpt' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'categories' => __('Categories', 'thegem'),
			'author' => __('Author', 'thegem'),
		];

		foreach ($caption_fields as $ekey => $elem) {

			$control->add_control(
				'caption_heading_' . $ekey,
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'slider_show_' . $ekey => 'yes',
					],
				]
			);

			if ($ekey == 'categories') {
				$control->start_controls_tabs('caption_categories_tabs');
				$control->start_controls_tab('caption_categories_tab_normal', ['label' => __('Normal', 'thegem')]);

				$this->caption_text_controls($control, $ekey);

				$control->add_control(
					'caption_background_categories',
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .style-new .gem-featured-post-meta-categories span' => 'background: {{VALUE}}',
						],
						'condition' => [
							'slider_show_' . $ekey => 'yes',
							'thegem_elementor_preset' => 'new',
						],
					]
				);

				$control->end_controls_tab();

				$control->start_controls_tab('caption_categories_tab_hover', ['label' => __('Hover', 'thegem')]);

				$this->caption_text_controls($control, $ekey, true);

				$control->add_control(
					'caption_background_categories_hover',
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .style-new .gem-featured-post-meta-categories span:hover' => 'background: {{VALUE}}',
						],
						'condition' => [
							'slider_show_' . $ekey => 'yes',
							'thegem_elementor_preset' => 'new',
						],
					]
				);

				$control->end_controls_tab();
				$control->end_controls_tabs();

			} else {
				$this->caption_text_controls($control, $ekey);
			}

			if ($ekey == 'author') {
				$this->add_control(
					'by_text',
					[
						'label' => __('“By” text', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'input_type' => 'text',
						'default' => __('By', 'thegem'),
						'condition' => [
							'slider_show_' . $ekey => 'yes',
						],
					]
				);
			}


		}

		$control->end_controls_section();
	}

	/**
	 * Read More Button Style
	 * @access protected
	 */
	protected function more_button_style($control) {

		$control->start_controls_section(
			'more_button_style_section',
			[
				'label' => __('“Read More” Button', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_show_button' => 'yes',
				],
			]
		);

		$control->add_control(
			'more_button_type',
			[
				'label' => __('Button Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'flat',
				'options' => [
					'flat' => __('Flat', 'thegem'),
					'outline' => __('Outline', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'more_button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'small',
				'options' => [
					'small' => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'more_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'more_button_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-featured-post-btn-box a.gem-button',
			]
		);

		$control->remove_control('more_button_border_type_color');

		$control->add_responsive_control(
			'more_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('more_button_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}

				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('more_button_tab_' . $stkey, ['label' => $stelem]);

				$control->add_responsive_control(
					'more_button_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state . ' span' => 'color: {{VALUE}};',
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state . ' i:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state . ' svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'more_button_typography_' . $stkey,
						'selector' => '{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state . ' span',
						'scheme' => Schemes\Typography::TYPOGRAPHY_1,
					]
				);

				$control->add_responsive_control(
					'more_button_bg_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'more_button_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'more_button_shadow_' . $stkey,
						'label' => __('Shadow', 'thegem'),
						'selector' => '{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_responsive_control(
			'more_button_icon_align',
			[
				'label' => __('Icon Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'left',
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
				'selectors_dictionary' => [
					'left' => 'left',
					'right' => 'right',
				],
			]
		);

		$control->add_responsive_control(
			'more_button_icon_spacing',
			[
				'label' => __('Icon Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
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
					'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button.gem-button-icon-position-right .gem-button-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button.gem-button-icon-position-left .gem-button-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Arrows Style
	 * @access protected
	 */
	protected function arrows_style($control) {

		$control->start_controls_section(
			'arrows_style_section',
			[
				'label' => __('Arrows Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'paginator_type' => 'arrows',
				],
			]
		);

		$this->add_control(
			'paginator_size',
			[
				'label' => __('Size Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'regular' => __('Regular', 'thegem'),
					'large' => __('Large', 'thegem'),
				],
				'default' => 'regular',
			]
		);

		$this->add_control(
			'paginator_style',
			[
				'label' => __('Style Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'light' => __('Light', 'thegem'),
					'dark' => __('Dark', 'thegem'),
				],
				'default' => 'light',
			]
		);

		$this->add_responsive_control(
			'paginator_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left_right' => __('Left & right', 'thegem'),
					'bottom_centered' => __('Bottom centered', 'thegem'),
				],
				'default' => 'left_right',
			]
		);

		$control->add_responsive_control(
			'arrows_icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
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
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrows_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a',
			]
		);
		$control->remove_control('arrows_border_type_color');

		$control->add_responsive_control(
			'arrows_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'margin-top: {{SIZE}}px;',
				],
				'condition' => [
					'paginator_position' => 'left_right',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_bottom_spacing',
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
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'margin-bottom: {{SIZE}}px;',
				],
				'condition' => [
					'paginator_position' => 'bottom_centered',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_side_spacing',
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
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a.gem-featured-posts-slide-prev' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a.gem-featured-posts-slide-next' => 'margin-right: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_between_spacing',
			[
				'label' => __('Spacing Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'margin-left: calc({{SIZE}}px/2); margin-right: calc({{SIZE}}px/2);',
				],
				'condition' => [
					'paginator_position' => 'bottom_centered',
				],
			]
		);

		$control->start_controls_tabs('arrows_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}

				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('arrows_tab_' . $stkey, ['label' => $stelem]);


				$control->add_responsive_control(
					'arrows_bg_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'arrows_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'arrows_icon_color_' . $stkey,
					[
						'label' => __('Icon Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' . $state => 'color: {{VALUE}};',
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
	 * Navigation Dots Style
	 * @access protected
	 */
	protected function bullets_style($control) {

		$control->start_controls_section(
			'bullets_style_section',
			[
				'label' => __('Navigation Dots', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'paginator_type' => 'bullets',
				],
			]
		);

		$control->add_responsive_control(
			'bullets_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider-dots a > span, .gem-featured-posts-slider-dots.size-regular a > span' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'bullets_bottom_position',
			[
				'label' => __('Bottom Position', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider-dots' => 'bottom: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'bullets_between_spacing',
			[
				'label' => __('Spacing Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider-dots a' => 'margin: 0 calc({{SIZE}}px/2);',
				],
			]
		);

		$control->start_controls_tabs('bullets_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'hover') {
					continue;
				}

				$state = '';
				if ($stkey == 'active') {
					$state = '.selected';
				} else {
					$state = ':not(.selected)';
				}

				$control->start_controls_tab('bullets_tab_' . $stkey, ['label' => $stelem]);


				$control->add_responsive_control(
					'bullets_bg_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slider-dots a' . $state . ' > span, .gem-featured-posts-slider-dots.size-regular a' . $state . ' > span' => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'bullets_border_type_' . $stkey,
						'label' => __('Border Type', 'thegem'),
						'selector' => '{{WRAPPER}} .gem-featured-posts-slider-dots a' . $state . ' > span, .gem-featured-posts-slider-dots.size-regular a' . $state . ' > span',
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();

		if (!empty ($settings['categories'])) {
			$terms = $settings['categories'];
		} else { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select blog categories in "Featured Posts" section', 'thegem') ?>
			</div>
			<?php return;
		}

		$taxonomies = array('category');
		if (taxonomy_exists('thegem_news_sets')) {
			$taxonomies[] = 'thegem_news_sets';
		}

		$style = '<style>@media(max-width: 767px) {
					   .gem-featured-posts-slider .slide-item {height: '.$settings['container_height_mobile']['size'].'px}
					}
					@media (min-width: 768px) and (max-width: 1024px) {
					  .gem-featured-posts-slider .slide-item {height: '.$settings['container_height_tablet']['size'].'px} 
					}
					@media (min-width: 1025px) {
					 .gem-featured-posts-slider .slide-item {height: '.$settings['container_height']['size'].'px}   
					}</style>';
		echo($style);

		global $post;
		$current_post = $post;

		$post_types = post_type_exists('thegem_news') ? array('post', 'thegem_news') : array('post');

		$query_args = array(
			'post__not_in' => array($current_post->ID),
			'post_type' => $post_types,
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'thegem_show_featured_posts_slider',
					'value' => 1
				)
			),
			'ignore_sticky_posts' => 1,
			'orderby' => 'date',
			'order' => $settings['order_by'] == 'date_asc' ? 'ASC' : 'DESC'
		);

		if (!empty($settings['categories']) && !in_array('0', $settings['categories'], true)) {
			$query_args['tax_query'] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => $settings['categories']
				),
			);

			if (taxonomy_exists('thegem_news_sets')) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'thegem_news_sets',
					'field' => 'slug',
					'terms' => $settings['categories']
				);
			}
		}

		$query_args['posts_per_page'] = $settings['max_posts'];
		$query = new WP_Query($query_args);

		if (is_array($settings)) {
			foreach ($settings as $key => $value) {
				if (substr($key, 0, 10) == 'paginator_') {
					$paginator_params[substr($key, 10)] = $value;
				}
			}
		}
		if ($paginator_params['icon'] == null) {
			$paginator_params['icon'] = 'custom';
		}

		$this->add_render_attribute(
			'slider-wrap',
			[
				'class' => [
					'gem-featured-posts-slider',
					'style-' . $settings['thegem_elementor_preset'],
					($settings['fullwidth'] == 'yes' ? 'fullwidth-block' : ''),
//					($settings['centered-captions'] == 'yes' ? 'centered-captions' : ''),
				],
				'data-paginator' => htmlspecialchars(json_encode($paginator_params)),
				'data-sliding-effect' => $settings['sliding_effect'],
				'data-auto-scroll' => ($settings['auto_scroll'] == 'yes' && intval($settings['auto_scroll_speed']) > 0) ? esc_attr(intval($settings['auto_scroll_speed'])) : 'false',
			]
		);

		$title_class = [];

		switch ($settings['title_style']) {
			case 'small':
				$title_class[] = 'title-h4';
				break;
			case 'normal':
				$title_class[] = 'title-h2';
				break;
			case 'big':
				$title_class[] = 'title-h1';
				break;
			case 'large':
				$title_class[] = 'title-xlarge';
				break;
		}

		$title_class = implode(' ', $title_class);


		$this->add_render_attribute(
			'button-wrap',
			[
				'class' => [
					'load-more-button gem-button',
					'gem-button-size-' . $settings['more_button_size'],
					'gem-button-style-' . $settings['more_button_type'],
					'gem-button-icon-position-' . $settings['more_button_icon_align'],
					'gem-button-text-weight-' . $settings['more_button_text_weight'],
				],
			]
		);

		if ($query->have_posts()) : ?>
			<div class="preloader">
				<div class="preloader-spin"></div>
			</div>
			<div <?php echo $this->get_render_attribute_string('slider-wrap'); ?>>
				<?php while ($query->have_posts()) {
					$query->the_post();
					$preset_path = __DIR__ . '/templates/content-blog-item-featured-posts-slider.php';
					$preset_path_filtered = apply_filters( 'thegem_featured_posts_slider_item_preset', $preset_path);
					$preset_path_theme = get_stylesheet_directory() . '/templates/featured-posts-slider/content-blog-item-featured-posts-slider.php';

					if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
						include($preset_path_theme);
					} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
						include($preset_path_filtered);
					}
				} ?>

			</div>
			<?php if (!empty($settings['left_arrow_icon']['value'])) : ?>
				<span class="custom-arrow-left">
					<?php \Elementor\Icons_Manager::render_icon($settings['left_arrow_icon'], ['aria-hidden' => 'true']); ?>
				</span>
			<?php endif; ?>
			<?php if (!empty($settings['right_arrow_icon']['value'])) : ?>
				<span class="custom-arrow-right">
					<?php \Elementor\Icons_Manager::render_icon($settings['right_arrow_icon'], ['aria-hidden' => 'true']); ?>
				</span>
			<?php endif; ?>
		<?php endif; ?>

		<?php wp_reset_postdata();

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script type="text/javascript">
				jQuery('body').prepareFeaturedPostsSlider();
				jQuery('body').updateFeaturedPostsSlider();
			</script>
		<?php endif;

	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_FeaturedPostsSlider());