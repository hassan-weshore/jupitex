<?php

namespace TheGem_Elementor\Widgets\Extended_BlogGrid;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Core\Schemes;

if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Extended Blog Grid.
 */
class TheGem_Extended_BlogGrid extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_EXTENDEDBLOGGRID_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_EXTENDEDBLOGGRID_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_EXTENDEDBLOGGRID_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_EXTENDEDBLOGGRID_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-extended-blog-grid-styles',
			THEGEM_ELEMENTOR_WIDGET_EXTENDEDBLOGGRID_URL . '/assets/css/thegem-blog-grid.css',
			array(
				'thegem-portfolio-styles',
				'thegem-extended-blog-grid-hovers-styles',
				'mediaelement',
				'wp-mediaelement',
				'thegem-animations',
			),
			null
		);

		wp_register_style('thegem-extended-blog-grid-hovers-styles', THEGEM_ELEMENTOR_WIDGET_EXTENDEDBLOGGRID_URL . '/assets/css/thegem-blog-grid-hovers.css', array(), null);

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
		return 'thegem-extended-blog-grid';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Extended Grid', 'thegem');
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
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-button',
				'thegem-news-grid-version-default-hovers-default',
				'thegem-news-grid-version-default-hovers-zooming-blur',
				'thegem-news-grid-version-default-hovers-horizontal-sliding',
				'thegem-news-grid-version-default-hovers-vertical-sliding',
				'thegem-news-grid-version-default-hovers-gradient',
				'thegem-news-grid-version-default-hovers-circular',
				'thegem-hovers-default',
				'thegem-hovers-zooming-blur',
				'thegem-hovers-horizontal-sliding',
				'thegem-hovers-vertical-sliding',
				'thegem-hovers-gradient',
				'thegem-hovers-circular',
				'thegem-news-grid-hovers',
				'thegem-news-grid-version-new-hovers-default',
				'thegem-news-grid-version-new-hovers-zooming-blur',
				'thegem-news-grid-version-new-hovers-horizontal-sliding',
				'thegem-news-grid-version-new-hovers-vertical-sliding',
				'thegem-news-grid-version-new-hovers-gradient',
				'thegem-news-grid-version-new-hovers-circular',
				'thegem-extended-blog-grid-styles'];
		}
		return ['thegem-extended-blog-grid-styles'];
	}

	public function get_script_depends() {
		return ['thegem-portfolio-scripts'];
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

	/**
	 * Make options select blog categories
	 * @access protected
	 * @return array
	 */
	protected function select_blog_categories() {
		$out = ['0' => __('All', 'thegem')];
		$terms = get_terms([
			'taxonomy' => 'category',
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
			'section_blog',
			[
				'label' => __('Blog', 'thegem'),
			]
		);

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

		$blog_fields = [
			'title' => __('Title', 'thegem'),
			'description' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'categories' => __('Categories', 'thegem'),
			'author' => __('Author', 'thegem'),
			'author_avatar' => __('Author’s Avatar', 'thegem'),
			'comments' => __('Comments', 'thegem'),
			'likes' => __('Likes', 'thegem'),
			'filter' => __('Filter Buttons', 'thegem'),
			'sorting' => __('Sorting', 'thegem'),
		];

		foreach ($blog_fields as $ekey => $elem) {

			$condition = [];

			$this->add_control(
				'blog_show_' . $ekey, [
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
					'hover' => __('On Image', 'thegem'),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pagination',
			[
				'label' => __('Pagination', 'thegem'),
			]
		);

		$this->add_responsive_control(
			'items_per_page',
			[
				'label' => __('Items Per Page', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => 8,
				'description' => __('Use -1 to show all', 'thegem'),
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => __('Pagination', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => __('Pagination Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal' => __('Numbers', 'thegem'),
					'more' => __('Load More Button', 'thegem'),
					'scroll' => __('Infinite Scroll', 'thegem'),
				],
				'condition' => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Load More', 'thegem'),
				'condition' => [
					'pagination_type' => 'more',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'pagination_type' => 'more',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_stretch_full_width',
			[
				'label' => __('Stretch to Full Width', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'pagination_type' => 'more',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_show_separator',
			[
				'label' => __('Separator', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'more_stretch_full_width',
							'operator' => '!=',
							'value' => 'yes',
						],
						[
							'name' => 'pagination_type',
							'operator' => '=',
							'value' => 'more',
						],
						[
							'name' => 'show_pagination',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sharing',
			[
				'label' => __('Social Sharing', 'thegem'),
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name' => 'caption_position',
							'operator' => '!=',
							'value' => 'page',
						],
					],
				],
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

		$this->add_control(
			'sharing_icon',
			[
				'label' => __('Sharing Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'social_sharing' => 'yes'
				],
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
					'condition' => [
						'social_sharing' => 'yes',
					]
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
			'loading_animation',
			[
				'label' => __('Lazy Loading Animation', 'thegem'),
				'default' => '',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional',
			[
				'label' => __('Additional Options', 'thegem'),
			]
		);

		$this->add_control(
			'ignore_highlights',
			[
				'label' => __('Ignore Highlighted Posts', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_metro',
			[
				'label' => __('Metro Options', 'thegem'),
				'condition' => [
					'layout' => 'metro',
				],
			]
		);

		$this->add_responsive_control(
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

		/* Grid Images Style */
		$this->image_style($control);

		/* Caption Style */
		$this->caption_style($control);

		/* Caption Container Style */
		$this->caption_container_style($control);

		/* Caption Position On Image Style */
		$this->caption_image_style($control);

		/* Filter Buttons Style */
		$this->filter_buttons_style($control);

		/* Sorting Style */
		$this->sorting_style($control);

		/* Pagination Style */
		$this->pagination_style($control);

		/* Pagination More Style */
		$this->pagination_more_style($control);

		/* Sharing Style */
		$this->sharing_styles($control);
	}

	/**
	 * Grid Images Style
	 * @access protected
	 */
	protected function image_style($control) {
		$control->start_controls_section(
			'image_style',
			[
				'label' => __('Grid Images Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
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
				'default' => [
					'size' => 42,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item' => 'padding: calc({{SIZE}}{{UNIT}}/2) !important;',
					'{{WRAPPER}} .portfolio.news-grid .portfolio-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.news-grid.fullwidth-columns .portfolio-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2) 0;',
					'{{WRAPPER}} .portfolio.news-grid .fullwidth-block:not(.no-paddings)' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.news-grid .fullwidth-block .portfolio-row' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',
				]
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
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.news-grid.title-on-page .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .portfolio.news-grid.title-on-hover .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.news-grid.title-on-image .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.news-grid:not(.shadowed-container) .portfolio-item .image, {{WRAPPER}} .portfolio.news-grid.shadowed-container .portfolio-item .wrap',
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
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image-inner' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-item .image-inner',
			]
		);


		$control->end_controls_tab();
		$control->start_controls_tab('image_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$this->add_control(
			'image_hover_effect',
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
							'{{WRAPPER}} .portfolio.news-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.news-grid.hover-default-circular .portfolio-item .image .overlay .overlay-circle, {{WRAPPER}} .portfolio.news-grid.hover-new-circular .portfolio-item .image .overlay .overlay-circle' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio.news-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.news-grid.hover-default-circular .portfolio-item .image .overlay .overlay-circle, {{WRAPPER}} .portfolio.news-grid.hover-new-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio.news-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.news-grid.hover-default-circular .portfolio-item .image .overlay .overlay-circle, {{WRAPPER}} .portfolio.news-grid.hover-new-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
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
				'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-item:hover .image-inner',
			]
		);

		$control->add_control(
			'icon_hover_header',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						],
						[
							'name' => 'caption_position',
							'value' => 'page',
						],
					],
				],
			]
		);

		$control->add_control(
			'icon_hover_show',
			[
				'label' => __('Show', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						],
						[
							'name' => 'caption_position',
							'value' => 'page',
						],
					],
				],
			]
		);

		$this->add_control(
			'icon_hover_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						],
						[
							'name' => 'caption_position',
							'value' => 'page',
						],
						[
							'name' => 'icon_hover_show',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$control->add_control(
			'icon_hover_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .overlay .links .portfolio-icons a.self-link i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item.double-item .image .overlay .links .portfolio-icons a.self-link i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .overlay .links .portfolio-icons a.self-link svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item.double-item .image .overlay .links .portfolio-icons a.self-link svg' => 'fill: {{VALUE}};'
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						],
						[
							'name' => 'caption_position',
							'value' => 'page',
						],
						[
							'name' => 'icon_hover_show',
							'value' => 'yes',
						],
					],
				],
			]
		);


		$control->add_responsive_control(
			'icon_hover_size',
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
					'{{WRAPPER}} .news-grid.portfolio .portfolio-item .image .overlay .links .portfolio-icons a.self-link' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important; line-height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .news-grid.portfolio .portfolio-item .image .overlay .links .portfolio-icons a.self-link i' => 'font-size: {{SIZE}}{{UNIT}} !important; line-height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .news-grid.portfolio .portfolio-item .image .overlay .links .portfolio-icons a.self-link svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .news-grid.portfolio.hover-new-zooming-blur .portfolio-item .image .overlay .links .portfolio-icons a.icon' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important; line-height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .news-grid.portfolio.hover-new-zooming-blur .portfolio-item .image .overlay .links .portfolio-icons a.icon i' => 'font-size: calc({{SIZE}}{{UNIT}}/2) !important;',
					'{{WRAPPER}} .news-grid.portfolio.hover-new-zooming-blur .portfolio-item .image .overlay .links .portfolio-icons a.icon svg' => 'width: calc({{SIZE}}{{UNIT}}/2); height: calc({{SIZE}}{{UNIT}}/2);',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						],
						[
							'name' => 'caption_position',
							'value' => 'page',
						],
						[
							'name' => 'icon_hover_show',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$control->add_control(
			'icon_hover_rotate_',
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
					'{{WRAPPER}} .news-grid.portfolio .portfolio-item .image .overlay .links .portfolio-icons a.self-link i' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .news-grid.portfolio .portfolio-item .image .overlay .links .portfolio-icons a.self-link svg' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						],
						[
							'name' => 'caption_position',
							'value' => 'page',
						],
						[
							'name' => 'icon_hover_show',
							'value' => 'yes',
						],
					],
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
			'caption_style_section',
			[
				'label' => __('Caption Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$caption_options = [
			'title' => __('Title', 'thegem'),
			'description' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'categories' => __('Categories', 'thegem'),
			'author' => __('Author', 'thegem'),
		];

		foreach ($caption_options as $ekey => $elem) {

			if ($ekey == 'title') {
				$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .title *, {{WRAPPER}} .portfolio.news-grid .portfolio-item .highlight-item-alternate-box .title *';
			} else if ($ekey == 'description') {
				$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .description, {{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .subtitle';
			} else if ($ekey == 'date') {
				$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .post-date, {{WRAPPER}} .portfolio.news-grid .portfolio-item .highlight-item-alternate-box .post-date';
			} else if ($ekey == 'categories') {
				$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .info a';
			} else if ($ekey == 'author') {
				$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .author .author-name';
			}

			$control->add_control(
				$ekey . '_header',
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'blog_show_' . $ekey => 'yes',
					]
				]
			);

			if ($ekey != 'categories') {
				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => $ekey . '_typography',
						'selector' => $selector,
						'condition' => [
							'caption_position' => 'hover',
							'blog_show_' . $ekey => 'yes',
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
							$selector => 'color: {{VALUE}} !important;',
						],
						'condition' => [
							'caption_position' => 'hover',
							'blog_show_' . $ekey => 'yes',
						]
					]
				);
			}


			if ($ekey == 'date') {
				$control->add_control(
					$ekey . '_background_color',
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .info .set .in_text' => 'background-color: {{VALUE}};',
						],
						'conditions' => [
							'terms' => [
								[
									'name' => 'thegem_elementor_preset',
									'value' => 'new',
								], [
									'name' => 'caption_position',
									'value' => 'hover',
								], [
									'name' => 'image_hover_effect',
									'value' => 'horizontal-sliding',
								], [
									'name' => 'blog_show_' . $ekey,
									'value' => 'yes',
								],
							],
						],
					]
				);
			}


			if ($ekey == 'categories') {
				$control->start_controls_tabs($ekey . '_tabs', [
					'condition' => [
						'blog_show_' . $ekey => 'yes',
					],
				]);
			} else {
				$control->start_controls_tabs($ekey . '_tabs', [
					'condition' => [
						'caption_position' => 'page',
						'blog_show_' . $ekey => 'yes',
					],
				]);
			}


			if (!empty($control->states_list)) {
				foreach ((array)$control->states_list as $stkey => $stelem) {
					$condition = [];
					$state = '';
					if ($stkey == 'active') {
						continue;
					} else if ($stkey == 'hover') {
//						$condition = ['caption_position' => 'page'];
						$state = ':hover';
					}

					if ($ekey == 'title') {
						$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .caption .title *, {{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .highlight-item-alternate-box .title *';
					} else if ($ekey == 'description') {
						$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .caption .description, {{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .caption .subtitle';
					} else if ($ekey == 'date') {
						$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .caption .post-date, {{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .highlight-item-alternate-box .post-date';
					} else if ($ekey == 'categories') {
						$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .caption .info a, {{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .caption .info .sep';
					} else if ($ekey == 'author') {
						$selector = '{{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .caption .author .author-name';
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
								$selector => 'color: {{VALUE}} !important;',
							],
							'condition' => $condition
						]
					);


					if ($ekey == 'date') {
						$control->add_control(
							$ekey . '_background_color_' . $stkey,
							[
								'label' => __('Background Color', 'thegem'),
								'type' => Controls_Manager::COLOR,
								'label_block' => false,
								'selectors' => [
									'{{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .caption .info .set .in_text' => 'background-color: {{VALUE}};',
								],
								'conditions' => [
									'terms' => [
										[
											'name' => 'thegem_elementor_preset',
											'value' => 'new',
										], [
											'name' => 'caption_position',
											'value' => 'hover',
										], [
											'name' => 'image_hover_effect',
											'value' => 'horizontal-sliding',
										],
									],
								],
							]
						);
					}


					if ($ekey == 'categories') {
						$control->add_control(
							$ekey . '_background_color_' . $stkey,
							[
								'label' => __('Background Color', 'thegem'),
								'type' => Controls_Manager::COLOR,
								'label_block' => false,
								'selectors' => [
									'{{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .caption .info' => 'background-color: {{VALUE}} !important;',
								],
								'conditions' => [
									'relation' => 'or',
									'terms' => [
										[
											'terms' => [
												[
													'name' => 'thegem_elementor_preset',
													'value' => 'new',
												], [
													'name' => 'caption_position',
													'value' => 'page',
												],
											],
										],
										[
											'terms' => [
												[
													'name' => 'thegem_elementor_preset',
													'value' => 'new',
												], [
													'name' => 'caption_position',
													'value' => 'hover',
												],
												[
													'name' => 'image_hover_effect',
													'operator' => '!=',
													'value' => 'horizontal-sliding',
												],
											],
										],
									],
								],
							]
						);
					}

					$control->end_controls_tab();

				}
			}

			$control->end_controls_tabs();

			if ($ekey == 'author') {

				$this->add_control(
					'by_text',
					[
						'label' => __('“By” text', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'input_type' => 'text',
						'default' => __('By', 'thegem'),
						'condition' => [
							'blog_show_' . $ekey => 'yes',
						],
					]
				);
			}
		}

		$control->add_control(
			'caption_likes_heading',
			[
				'label' => __('Likes', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'blog_show_likes' => 'yes',
				],
			]
		);


		$control->add_control(
			'likes_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'blog_show_likes' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'likes_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .links .caption .grid-post-meta .post-meta-likes a i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .version-new.news-grid .portfolio-item .wrap > .caption .grid-post-meta .post-meta-likes a i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .links .caption .grid-post-meta .post-meta-likes a svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .version-new.news-grid .portfolio-item .wrap > .caption .grid-post-meta .post-meta-likes a svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
				],
				'condition' => [
					'blog_show_likes' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'caption_likes_typography',
				'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .links .caption .grid-post-meta .post-meta-likes a, {{WRAPPER}} .version-new.news-grid .portfolio-item .wrap > .caption .grid-post-meta .post-meta-likes a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'blog_show_likes' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'caption_likes_count_color',
			[
				'label' => __('Text  Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .links .caption .grid-post-meta .post-meta-likes a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .version-new.news-grid .portfolio-item .wrap > .caption .grid-post-meta .post-meta-likes a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'blog_show_comments' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_comments_heading',
			[
				'label' => __('Comments', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'blog_show_comments' => 'yes',
				],
			]
		);

		$control->add_control(
			'comments_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'blog_show_comments' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'comments_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .links .caption .grid-post-meta .comments-link a i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .version-new.news-grid .portfolio-item .wrap > .caption .grid-post-meta .comments-link a i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .links .caption .grid-post-meta .comments-link a svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
					'{{WRAPPER}} .version-new.news-grid .portfolio-item .wrap > .caption .grid-post-meta .comments-link a svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'blog_show_comments' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'caption_comments_typography',
				'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .links .caption .grid-post-meta .comments-link a, {{WRAPPER}} .version-new.news-grid .portfolio-item .wrap > .caption .grid-post-meta .comments-link a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'blog_show_comments' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'caption_comments_count_color',
			[
				'label' => __('Text  Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .links .caption .grid-post-meta .comments-link a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .version-new.news-grid .portfolio-item .wrap > .caption .grid-post-meta .comments-link a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'blog_show_comments' => 'yes',
				],
			]
		);


		$control->add_control(
			'caption_delimiter_heading',
			[
				'label' => __('Delimiter', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset!' => 'new',
				],
			]
		);

		$control->add_responsive_control(
			'caption_delimiter_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .grid-post-meta .post-meta-likes, {{WRAPPER}} .portfolio.news-grid .grid-post-meta .comments-link' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => 'new',
				],
			]
		);

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

		$control->add_responsive_control(
			'caption_container_alignment',
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
						'title' => __('Centered', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid.title-on-page .wrap > .caption' => 'text-align: {{VALUE}}',
				],
			]
		);

		$control->add_control(
			'caption_container_preset',
			[
				'label' => __('Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'transparent',
				'options' => [
					'transparent' => __('Transparent', 'thegem'),
					'white' => __('White', 'thegem'),
					'gray ' => __('Gray', 'thegem'),
					'dark' => __('Dark', 'thegem'),
				]
			]
		);

		$control->add_responsive_control(
			'caption_container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .wrap > .caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.news-grid.title-on-page .portfolio-item .wrap' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .wrap > .caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'caption_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-item .wrap > .caption',
			]
		);

		$control->start_controls_tabs('caption_container_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				if ($stkey == 'active') {
					continue;
				}
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
						'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .wrap > .caption',
					]
				);
				$control->remove_control('image_hover_overlay_image');

				$control->remove_control('caption_container_background_' . $stkey . '_image');

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
						'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-item' . $state . ' .wrap > .caption',
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

		$this->add_responsive_control(
			'spacing_title',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid.title-on-page .portfolio-item .wrap > .caption .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'spacing_description_header',
			[
				'label' => 'Description',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset' => 'new',
				],
			]
		);

		$this->add_responsive_control(
			'spacing_description',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => ['bottom'],
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .wrap > .caption .description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => 'new',
				],
			]
		);

		$control->add_control(
			'categories_position_header',
			[
				'label' => 'Categories',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						], [
							'name' => 'caption_position',
							'value' => 'page',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'categories_position_horizontal',
			[
				'label' => __('Horizontal Position', 'thegem'),
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
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						], [
							'name' => 'caption_position',
							'value' => 'page',
						],
					],
				],
				'selectors_dictionary' => [
					'left' => 'left: 10px; right: inherit;',
					'right' => 'left: inherit; right: 10px;',
				],
				'selectors' => [
					'{{WRAPPER}} .version-new.news-grid.portfolio.title-on-page .portfolio-item .image .links .caption .info' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'categories_position_vertical',
			[
				'label' => __('Vertical Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'top' => [
						'title' => __('Top', 'thegem'),
						'icon' => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __('Bottom', 'elementor'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'toggle' => false,
				'default' => 'top',
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						], [
							'name' => 'caption_position',
							'value' => 'page',
						],
					],
				],
				'selectors_dictionary' => [
					'top' => 'top: 10px; bottom: inherit;',
					'bottom' => 'top: inherit; bottom: 10px;',
				],
				'selectors' => [
					'{{WRAPPER}} .version-new.news-grid.portfolio.title-on-page .portfolio-item .image .links .caption .info' => '{{VALUE}}',
				],
			]
		);

		$control->add_control(
			'likes_comments_spacing_header',
			[
				'label' => 'Likes & Comments',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						], [
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'blog_show_likes',
									'value' => 'yes',
								], [
									'name' => 'blog_show_comments',
									'value' => 'yes',
								],
							],
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'likes_comments_spacing_between',
			[
				'label' => __('Spacing Between', 'thegem'),
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
					'{{WRAPPER}} .news-grid .portfolio-item .grid-post-meta .comments-link' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						], [
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'blog_show_likes',
									'value' => 'yes',
								], [
									'name' => 'blog_show_comments',
									'value' => 'yes',
								],
							],
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'likes_comments_spacing_left',
			[
				'label' => __('Left Spacing', 'thegem'),
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
					'{{WRAPPER}} .grid-post-meta-comments-likes' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						], [
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'blog_show_likes',
									'value' => 'yes',
								], [
									'name' => 'blog_show_comments',
									'value' => 'yes',
								],
							],
						],
					],
				],
			]
		);

		$control->add_control(
			'sharing_spacing_header',
			[
				'label' => 'Sharing Icon',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						], [
							'name' => 'social_sharing',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'sharing_spacing_right',
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
					'{{WRAPPER}} .grid-post-share' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'thegem_elementor_preset',
							'value' => 'new',
						], [
							'name' => 'social_sharing',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Caption Position On Image Style
	 * @access protected
	 */
	protected function caption_image_style($control) {

		$control->start_controls_section(
			'caption_image_style',
			[
				'label' => __('Caption Position On Image', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['caption_position' => 'hover']
			]
		);

		$control->add_control(
			'caption_image_header',
			[
				'label' => 'Caption',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'caption_image_title_position',
			[
				'label' => __('Horizontal Position', 'thegem'),
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
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid.title-on-hover .portfolio-item .image .links .caption .slide-content' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .portfolio.news-grid.title-on-hover .portfolio-item .image .links .caption .slide-content .description' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .portfolio.news-grid.title-on-hover .portfolio-item:hover .image .links .caption .grid-post-meta' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'caption_image_icons_position',
			[
				'label' => __('Icons Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'flex-start' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid.title-on-hover:not(.hover-new-default) .portfolio-item .image .links .caption .grid-post-meta' => 'display: flex; justify-content: {{VALUE}}',
				],
			]
		);

		$control->add_control(
			'caption_image_title_header',
			[
				'label' => 'Title',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'caption_image_title_spacing',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid.title-on-hover .portfolio-item .caption .title' => 'margin: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0 !important;',
				],
			]
		);

		$control->add_control(
			'caption_image_label_header',
			[
				'label' => 'Label',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'caption_image_categories_label_position',
			[
				'label' => __('Horizontal Position', 'thegem'),
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
				'toggle' => false,
				'default' => 'left',
				'selectors_dictionary' => [
					'left' => 'left: 0;',
					'center' => 'left: 50%; transform: translateX(-50%);',
					'right' => 'left: inherit; right: 0;',
				],
				'selectors' => [
					'{{WRAPPER}} .version-new.news-grid.portfolio.title-on-hover .portfolio-item .image .links .caption .info' => '{{VALUE}}',
					'{{WRAPPER}} .version-new.news-grid.portfolio.title-on-hover .portfolio-item .image .links .caption .post-date' => '{{VALUE}}',
				],
				'condition' => [
					'thegem_elementor_preset' => 'new',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Filter Buttons Style
	 * @access protected
	 */
	protected function filter_buttons_style($control) {

		$control->start_controls_section(
			'filter_buttons_style',
			[
				'label' => __('Filter Buttons Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['blog_show_filter' => 'yes']
			]
		);

		$control->add_control(
			'filter_buttons_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'left' => __('Left', 'thegem'),
					'right ' => __('Right', 'thegem'),
					'center' => __('Centered', 'thegem'),
				],
				'condition' => ['blog_show_sorting' => ''],
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-filters' => 'text-align: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-filters a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_buttons_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-filters a',
			]
		);

		$control->add_responsive_control(
			'filter_buttons_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-filters a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
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
					'{{WRAPPER}} .portfolio.news-grid .portfilio-top-panel' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->start_controls_tabs('filter_buttons_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					$state = '.active';
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('filter_buttons_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'filter_buttons_background_color' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.news-grid .portfolio-filters a' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'filter_buttons_border_color' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.news-grid .portfolio-filters a' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'filter_buttons_text_color' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.news-grid .portfolio-filters a' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'filter_buttons_text_typography' . $stkey,
						'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-filters a' . $state . ' span',
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$this->add_control(
			'show_all_button_text',
			[
				'label' => __('“Show All” Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Show All', 'thegem'),
			]
		);


		$control->add_control(
			'filter_responsive_header',
			[
				'label' => __('Filter in responsive mode', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'filter_responsive_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$control->add_control(
			'filter_responsive_icon_color' . $stkey,
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-filters-resp .menu-toggle i' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_responsive_icon_size',
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
					'{{WRAPPER}} .portfolio.news-grid .portfolio-filters-resp .menu-toggle i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.news-grid .portfolio-filters-resp .menu-toggle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'filter_responsive_typography',
				'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-filters-resp ul li a',
			]
		);

		$control->add_control(
			'filter_responsive_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-filters-resp ul li a' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'filter_responsive_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-filters-resp ul li' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'filter_responsive_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-filters-resp ul li, {{WRAPPER}} .portfolio.news-grid .portfolio-filters-resp ul' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_section();
	}


	/**
	 * Sorting Style
	 * @access protected
	 */
	protected function sorting_style($control) {

		$control->start_controls_section(
			'sorting_style',
			[
				'label' => __('Sorting Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['blog_show_sorting' => 'yes']
			]
		);

		$control->add_control(
			'switch_background_color',
			[
				'label' => __('Switch Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .sorting-switcher' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'switch_color',
			[
				'label' => __('Switch Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .sorting-switcher:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'sorting_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-sorting-sep' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'sorting_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-sorting label' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'sorting_text_typography',
				'selector' => '{{WRAPPER}} .portfolio.news-grid .portfolio-sorting label',
			]
		);

		$this->add_control(
			'sorting_date_text',
			[
				'label' => __('"Date" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Date', 'thegem'),
			]
		);

		$this->add_control(
			'sorting_name_text',
			[
				'label' => __('"Name" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Name', 'thegem'),
			]
		);

		$this->add_control(
			'sorting_desc_text',
			[
				'label' => __('"Desc" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Desc', 'thegem'),
			]
		);

		$this->add_control(
			'sorting_asc_text',
			[
				'label' => __('"Asc" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Asc', 'thegem'),
			]
		);


		$control->end_controls_section();
	}


	/**
	 * Pagination Style
	 * @access protected
	 */
	protected function pagination_style($control) {

		$control->start_controls_section(
			'pagination_normal_style',
			[
				'label' => __('Pagination Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['pagination_type' => 'normal']
			]
		);

		$control->add_responsive_control(
			'pagination_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
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
				'default' => [
					'size' => 100,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .gem-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_control(
			'pagination_numbers_header',
			[
				'label' => __('Numbers', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'pagination_numbers_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'pagination_numbers_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$control->start_controls_tabs('pagination_numbers_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				if ($stkey == 'active') {
					$state = '.current';
				} else if ($stkey == 'hover') {
					$state = ':hover';
				}
				$control->start_controls_tab('pagination_numbers_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'pagination_numbers_background_color' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-pagination a' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'pagination_numbers_border_type' . $stkey,
						'label' => __('Border', 'thegem'),
						'selector' => '{{WRAPPER}} .gem-pagination a' . $state,
					]
				);

				$control->add_control(
					'pagination_numbers_text_color' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-pagination a' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'pagination_numbers_text_typography' . $stkey,
						'selector' => '{{WRAPPER}} .gem-pagination a' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'pagination_arrows_header',
			[
				'label' => __('Arrows', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'pagination_arrows_left_icon',
			[
				'label' => __('Left Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$control->add_control(
			'pagination_arrows_right_icon',
			[
				'label' => __('Right Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$control->add_responsive_control(
			'pagination_arrows_icon_size',
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
					'{{WRAPPER}} .portfolio.news-grid .gem-pagination .prev i, {{WRAPPER}} .portfolio.news-grid .gem-pagination .next i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};
					font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'pagination_arrows_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .gem-pagination .prev, {{WRAPPER}} .portfolio.news-grid .gem-pagination .next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'pagination_arrows_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .gem-pagination .prev, {{WRAPPER}} .portfolio.news-grid .gem-pagination .next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$control->start_controls_tabs('pagination_arrows_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}
				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('pagination_arrows_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'pagination_arrows_background_color' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.news-grid .gem-pagination .prev' . $state . ', {{WRAPPER}} .portfolio.news-grid .gem-pagination .next' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'pagination_arrows_border_type' . $stkey,
						'label' => __('Border Type', 'thegem'),
						'selector' => '{{WRAPPER}} .portfolio.news-grid .gem-pagination .prev' . $state . ', {{WRAPPER}} .portfolio.news-grid .gem-pagination .next' . $state,
					]
				);

				$control->add_control(
					'pagination_arrows_icon_color' . $stkey,
					[
						'label' => __('Icon Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.news-grid .gem-pagination .prev' . $state . ', {{WRAPPER}} .portfolio.news-grid .gem-pagination .next' . $state => 'color: {{VALUE}};',
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
	 * Pagination More Style
	 * @access protected
	 */
	protected function pagination_more_style($control) {

		$control->start_controls_section(
			'pagination_more_style',
			[
				'label' => __('"Load More" Button Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['pagination_type' => 'more']
			]
		);

		$control->add_responsive_control(
			'pagination_more_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
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
				'default' => [
					'size' => 100,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-load-more' => 'margin-top: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_control(
			'pagination_more_button_type',
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
			'pagination_more_button_size',
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
			'pagination_more_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_more_button_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-load-more button',
			]
		);

		$control->add_responsive_control(
			'pagination_more_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('pagination_more_button_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}

				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('pagination_more_button_tab_' . $stkey, ['label' => $stelem]);

				$control->add_responsive_control(
					'pagination_more_button_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-load-more button' . $state . ' span' => 'color: {{VALUE}};',
							'{{WRAPPER}} .portfolio-load-more button' . $state . ' i:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .portfolio-load-more button' . $state . ' svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'pagination_more_button_typography_' . $stkey,
						'selector' => '{{WRAPPER}} .portfolio-load-more button' . $state . ' span',
						'scheme' => Schemes\Typography::TYPOGRAPHY_1,
					]
				);

				$control->add_responsive_control(
					'pagination_more_button_bg_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-load-more button' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'pagination_more_button_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-load-more button' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'pagination_more_button_shadow_' . $stkey,
						'label' => __('Shadow', 'thegem'),
						'selector' => '{{WRAPPER}} .portfolio-load-more button' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_responsive_control(
			'pagination_more_button_icon_align',
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
			'pagination_more_button_icon_spacing',
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
					'{{WRAPPER}} .portfolio-load-more button.gem-button-icon-position-right .gem-button-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio-load-more button.gem-button-icon-position-left .gem-button-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'pagination_more_button_separator_header',
			[
				'label' => 'Separator',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'more_show_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_more_button_separator_style_active',
			[
				'label' => __('Separator Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'gem-button-separator-type-single',
				'options' => [
					'gem-button-separator-type-single' => __('Single', 'thegem'),
					'gem-button-separator-type-square' => __('Square', 'thegem'),
					'gem-button-separator-type-soft-double' => __('Soft Double', 'thegem'),
					'gem-button-separator-type-strong-double' => __('Strong Double', 'thegem'),
				],
				'condition' => [
					'more_show_separator' => 'yes',
				],
			]
		);

		// Size Strong Double & Soft Double & Single
		$control->add_responsive_control(
			'pagination_more_button_separator_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' =>
						[
							'gem-button-separator-type-single',
							'gem-button-separator-type-soft-double',
							'gem-button-separator-type-strong-double',
						],
					'more_show_separator' => 'yes',
				],
				'size_units' => ['%',],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'size' => 50,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder' => 'width:{{SIZE}}{{UNIT}}; flex-grow: initial;',
				],
			]
		);

		// Size Square
		$control->add_responsive_control(
			'pagination_more_button_separator_size_square',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-square',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'size' => 50,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-type-square' => 'padding:0 calc( 50% - {{SIZE}}{{UNIT}} );',
				],
			]
		);

		// Weight Strong Double
		$control->add_responsive_control(
			'pagination_more_button_separator_double_weight',
			[
				'label' => __('Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-strong-double',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder .gem-button-separator-line' => 'border-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Weight Soft Double
		$control->add_responsive_control(
			'pagination_more_button_separator_soft_weight',
			[
				'label' => __('Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-soft-double',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder .gem-button-separator-line' => 'border-top:{{SIZE}}{{UNIT}} solid; border-bottom:{{SIZE}}{{UNIT}} solid;',
				],
			]
		);

		// Weight Single
		$control->add_responsive_control(
			'pagination_more_button_separator_single_weight',
			[
				'label' => __('Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-single',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder .gem-button-separator-line' => 'border-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Height Strong Double & Soft
		$control->add_responsive_control(
			'pagination_more_button_separator_double_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' =>
						[
							'gem-button-separator-type-strong-double',
							'gem-button-separator-type-soft-double',
						],
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-holder .gem-button-separator-line' => 'height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Height Square
		$control->add_responsive_control(
			'pagination_more_button_separator_square_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'pagination_more_button_separator_style_active' => 'gem-button-separator-type-square',
					'more_show_separator' => 'yes',
				],
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-type-square .gem-button-separator-button' => 'margin: {{SIZE}}{{UNIT}} 0;',
				],
			]
		);

		// Color
		$control->add_control(
			'pagination_more_button_color_border',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'more_show_separator' => 'yes',
				],
				'default' => '#b6c6c9',
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-line' => 'border-color:{{VALUE}}; color:{{VALUE}};',
					'{{WRAPPER}} .portfolio-load-more .gem-button-separator-type-square svg line' => 'stroke:{{VALUE}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Sharing Styles
	 * @access protected
	 */
	protected function sharing_styles($control) {

		$control->start_controls_section(
			'sharing_style',
			[
				'label' => __('Sharing Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'social_sharing' => 'yes',
				],
			]
		);

		$control->add_control(
			'sharing_icon_heading',
			[
				'label' => __('Sharing Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$control->add_responsive_control(
			'sharing_icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .overlay .caption .grid-post-meta .grid-post-share a.icon' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .version-new.news-grid .portfolio-item .caption .grid-post-meta .grid-post-share > a.icon' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .image .overlay .caption .grid-post-meta .grid-post-share a.icon svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .version-new.news-grid .portfolio-item .caption .grid-post-meta .grid-post-share > a.icon svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$control->start_controls_tabs('sharing_icon_tabs');
		$control->start_controls_tab('sharing_icon_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
			'sharing_icon_color',
			[
				'label' => __('Icon Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .grid-post-meta .grid-post-share .icon' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .grid-post-meta .grid-post-share .icon i' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('sharing_icon_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'sharing_icon_color_hover',
			[
				'label' => __('Icon Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .caption .grid-post-meta .grid-post-share .icon:hover i' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'social_icons_heading',
			[
				'label' => __('Social Icons', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->remove_control('social_icons_background_image');

		$control->start_controls_tabs('social_icons_tabs');
		$control->start_controls_tab('social_icons_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
			'social_icons_icon_color',
			[
				'label' => __('Icons Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .portfolio-sharing-pane a.socials-item i' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('social_icons_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'social_icons_icon_color_hover',
			[
				'label' => __('Icons Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .portfolio.news-grid .portfolio-item .portfolio-sharing-pane a.socials-item:hover i' => 'color: {{VALUE}} !important;',
				],
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
		$grid_uid = $this->get_id();

		$localize = array(
			'data' => $settings,
			'action' => 'blog_grid_extended_load_more',
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('portfolio_ajax-nonce')
		);
		wp_localize_script( 'thegem-portfolio-scripts', 'thegem_portfolio_ajax_'. $grid_uid, $localize );
		$settings['action'] = 'blog_grid_extended_load_more';

		if (!empty ($settings['categories'])) {
			$terms = $settings['categories'];
		} else { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select blog categories in "Blog" section', 'thegem') ?>
			</div>
			<?php return;
		}

		switch ($settings['thegem_elementor_preset']) {
			case 'default':
				if ($settings['caption_position'] == 'hover') {
					$hover_effect = $settings['thegem_elementor_preset'] . '-' . $settings['image_hover_effect'];
					wp_enqueue_style('thegem-news-grid-version-default-hovers-' . $settings['image_hover_effect']);
				} else {
					$hover_effect = $settings['image_hover_effect'];
					wp_enqueue_style('thegem-hovers-' . $settings['image_hover_effect']);
					wp_enqueue_style('thegem-news-grid-hovers');
				}
				break;

			case 'new':
				$hover_effect = $settings['thegem_elementor_preset'] . '-' . $settings['image_hover_effect'];
				wp_enqueue_style('thegem-news-grid-version-new-hovers-' . $settings['image_hover_effect']);
				break;
		}

		if ($settings['pagination_type'] == 'more') {
			wp_enqueue_style('thegem-button');
		}

		$layout_columns_count = -1;
		if ($settings['columns'] == '2x')
			$layout_columns_count = 2;
		if ($settings['columns'] == '3x')
			$layout_columns_count = 3;
		if ($settings['columns'] == '4x')
			$layout_columns_count = 4;

		$items_per_page = intval($settings['items_per_page']) ? intval($settings['items_per_page']) : 8;

		$page = 1;
		$next_page = 0;

		if ($settings['pagination_type'] == 'more' || $settings['pagination_type'] == 'scroll') {
			if ($settings['blog_show_sorting'] == 'yes') {
				$orderby = 'date';
			} else {
				$orderby = 'menu_order date';
			}
			if ($layout_columns_count == -1)
				$layout_columns_count = 5;
			$news_grid_loop = thegem_get_news_posts($terms, $page, $items_per_page, $orderby);
			if (ceil($news_grid_loop->found_posts / $items_per_page) > $page)
				$next_page = $page + 1;
			else
				$next_page = 0;
		} else {
			$news_grid_loop = thegem_get_news_posts($terms);
		}

		global $post;
		$news_grid_posttemp = $post;

		if ($news_grid_loop->have_posts()) :

			if (in_array('0', $terms)) {
				$terms = get_terms('category', array('hide_empty' => false));
			} else {
				foreach ($terms as $key => $term) {
					$terms[$key] = get_term_by('slug', $term, 'category');
					if (!$terms[$key]) {
						unset($terms[$key]);
					}
				}
			}

			$terms = apply_filters('news_grid_terms_filter', $terms);

			$thegem_terms_set = array();
			foreach ($terms as $term) {
				$thegem_terms_set[$term->slug] = $term;
			}
			?>

			<?php echo apply_filters('thegem_portfolio_preloader_html', '<div class="preloader"><div class="preloader-spin"></div></div>'); ?>
			<div class="portfolio-preloader-wrapper">

				<?php

				$this->add_render_attribute(
					'blog-wrap',
					[
						'class' => [
							'portfolio portfolio-grid news-grid no-padding',
							'portfolio-pagination-' . $settings['pagination_type'],
							'portfolio-style-' . $settings['layout'],
							'background-style-' . $settings['caption_container_preset'],
							'hover-' . $hover_effect,
							'title-on-' . $settings['caption_position'],
							'version-' . $settings['thegem_elementor_preset'],
							($settings['animation_effect'] ? 'item-animation-' . $settings['animation_effect'] : ''),
							($settings['image_gaps']['size'] == 0 ? 'no-gaps' : ''),
							($settings['shadowed_container'] == 'yes' ? 'shadowed-container' : ''),
							($settings['columns'] == '100%' ? 'fullwidth-columns fullwidth-columns-' . $settings['columns_100'] : ''),
							($settings['thegem_elementor_preset'] == 'new' || ($settings['thegem_elementor_preset'] == 'default' && $settings['caption_position'] == 'hover') ? 'hover-' . $settings['thegem_elementor_preset'] . '-' . $settings['image_hover_effect'] : 'hover-' . $settings['image_hover_effect']),
							($settings['caption_position'] == 'hover' ? 'hover-title' : ''),
							($settings['layout'] == 'masonry' && $settings['columns'] != '1x' ? 'portfolio-items-masonry' : ''),
							($layout_columns_count != -1 ? 'columns-' . intval($layout_columns_count) : ''),
						],
						'data-per-page' => $items_per_page,
						'data-next-page' => $next_page,
						'data-portfolio-uid' => $grid_uid,
						'data-hover' => $hover_effect,
					]
				);
				?>

				<div <?php echo $this->get_render_attribute_string('blog-wrap'); ?>>
					<?php if (($settings['blog_show_filter'] == 'yes' && count($terms) > 0) || $settings['blog_show_sorting'] == 'yes'): ?>
						<div class="portfilio-top-panel<?php if ($settings['columns'] == '100%'): ?> fullwidth-block<?php endif; ?>">
							<div class="portfilio-top-panel-row">
								<div class="portfilio-top-panel-left">
									<?php if ($settings['blog_show_filter'] == 'yes' && count($terms) > 0): ?>
										<div class="portfolio-filters">
											<a href="#" data-filter="*"
											   class="active all title-h6"><span
														class="light"><?php echo $settings['show_all_button_text']; ?></span></a>
											<?php foreach ($terms as $term) : ?>
												<a href="#" data-filter=".<?php echo $term->slug; ?>"
												   class="title-h6"><?php if (get_option('portfoliosets_' . $term->term_id . '_icon_pack') && get_option('portfoliosets_' . $term->term_id . '_icon')) {
														echo thegem_build_icon(get_option('portfoliosets_' . $term->term_id . '_icon_pack'), get_option('portfoliosets_' . $term->term_id . '_icon'));
													} ?><span class="light"><?php echo $term->name; ?></span></a>
											<?php endforeach; ?>
										</div>
										<div class="portfolio-filters-resp">
											<button class="menu-toggle dl-trigger"><?php _e('Portfolio filters', 'thegem'); ?>
												<?php if ($settings['filter_responsive_icon']['value']) {
													Icons_Manager::render_icon($settings['filter_responsive_icon'], ['aria-hidden' => 'true']);
												} else { ?>
													<span class="menu-line-1"></span>
													<span class="menu-line-2"></span>
													<span class="menu-line-3"></span>
												<?php } ?>
											</button>
											<ul class="dl-menu">
												<li><a href="#"
													   data-filter="*"><?php echo $settings['show_all_button_text']; ?></a>
												</li>
												<?php foreach ($terms as $term) : ?>
													<li><a href="#"
														   data-filter=".<?php echo esc_attr($term->slug); ?>"><?php echo $term->name; ?></a>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
									<?php endif; ?>
								</div>
								<div class="portfilio-top-panel-right">
									<?php if ($settings['blog_show_sorting'] == 'yes'): ?>
										<div class="portfolio-sorting title-h6">
											<div class="orderby light">
												<label for=""
													   data-value="date"><?php echo $settings['sorting_date_text']; ?></label>
												<a href="javascript:void(0);" class="sorting-switcher"
												   data-current="date"></a>
												<label for=""
													   data-value="title"><?php echo $settings['sorting_name_text']; ?></label>
											</div>
											<div class="portfolio-sorting-sep"></div>
											<div class="order light">
												<label for=""
													   data-value="DESC"><?php echo $settings['sorting_desc_text']; ?></label>
												<a href="javascript:void(0);" class="sorting-switcher"
												   data-current="DESC"></a>
												<label for=""
													   data-value="ASC"><?php echo $settings['sorting_asc_text']; ?></label>
											</div>
										</div>

									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<div class="<?php if ($settings['columns'] == '100%'): ?>fullwidth-block no-paddings<?php endif; ?>">
						<div class="row portfolio-row">
							<div class="portfolio-set clearfix"
								 data-max-row-height="<?php echo $settings['metro_max_row_height'] ? $settings['metro_max_row_height']['size'] : ''; ?>">

								<?php
								$eo_marker = false;
								while ($news_grid_loop->have_posts()) : $news_grid_loop->the_post(); ?>
									<?php echo thegem_news_render_item(get_the_ID(), $settings); ?>
									<?php $eo_marker = !$eo_marker;
								endwhile;
								?>

							</div><!-- .portflio-set -->
							<?php if ($settings['columns'] != '1x'): ?>
								<div class="portfolio-item-size-container">
									<?php echo thegem_news_render_item(false, $settings); ?>
								</div>
							<?php endif; ?>
						</div><!-- .row-->
						<?php if ($settings['pagination_type'] == 'normal'): ?>
							<div class="portfolio-navigator gem-pagination">
								<a href="#" class="prev">
									<?php if ($settings['pagination_arrows_left_icon']['value']) {
										Icons_Manager::render_icon($settings['pagination_arrows_left_icon'], ['aria-hidden' => 'true']);
									} else { ?>
										<i class="default"></i>
									<?php } ?>
								</a>
								<div class="pages"></div>
								<a href="#" class="next">
									<?php if ($settings['pagination_arrows_right_icon']['value']) {
										Icons_Manager::render_icon($settings['pagination_arrows_right_icon'], ['aria-hidden' => 'true']);
									} else { ?>
										<i class="default"></i>
									<?php } ?>
								</a>
							</div>
						<?php endif; ?>
						<?php
						if ($settings['pagination_type'] == 'more' && $next_page > 0):

							$separator_enabled = !empty($settings['more_show_separator']) ? true : false;

							// Container
							$classes_container = 'gem-button-container gem-widget-button ';

							if ($separator_enabled) {
								$classes_container .= 'gem-button-position-center gem-button-with-separator ';
							} else {
								if ('yes' === $settings['more_stretch_full_width']) {
									$classes_container .= 'gem-button-position-fullwidth ';
								}
							}
							$attr_container = [
								'class' => $classes_container,
							];
							$this->add_render_attribute('attr_container', $attr_container);

							// Separator
							$classes_separator = 'gem-button-separator ';

							if (!empty($settings['pagination_more_button_separator_style_active'])) {

								$classes_separator .= esc_attr($settings['pagination_more_button_separator_style_active']);
							}
							$attr_separator = [

								'class' => $classes_separator,
							];

							$this->add_render_attribute('attr_separator', $attr_separator);

							// Link

							$this->add_render_attribute(
								'button-wrap',
								[
									'class' => [
										'load-more-button gem-button',
										'gem-button-size-' . $settings['pagination_more_button_size'],
										'gem-button-style-' . $settings['pagination_more_button_type'],
										'gem-button-icon-position-' . $settings['pagination_more_button_icon_align'],
										'gem-button-text-weight-normal',
									],
								]
							);
							?>

							<div class="portfolio-load-more">
								<div class="inner">
									<?php
									$preset_path = __DIR__ . '/templates/more-button.php';

									if (!empty($preset_path) && file_exists($preset_path)) {
										include($preset_path);
									}
									?>
								</div>
							</div>
						<?php endif; ?>
						<?php if ($settings['pagination_type'] == 'scroll' && $next_page > 0): ?>
							<div class="portfolio-scroll-pagination"></div>
						<?php endif; ?>
					</div><!-- .full-width -->
				</div><!-- .portfolio-->
			</div><!-- .portfolio-preloader-wrapper-->

		<?php endif;

		$post = $news_grid_posttemp;
		wp_reset_postdata(); ?>

		<?php if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script>
				if (typeof widget_settings == 'undefined') {
					var widget_settings = [];
				}
				widget_settings['<?php echo $grid_uid ?>'] = JSON.parse('<?php echo json_encode($settings) ?>');
			</script>
			<script type="text/javascript">
				(function ($) {

					setTimeout(function () {
						if (!$('.elementor-element-<?php echo $this->get_id(); ?> .portfolio.news-grid').length) {
							return;
						}
						$('.elementor-element-<?php echo $this->get_id(); ?> .portfolio.news-grid').initPortfoliosGrid();
					}, 1000);

					elementor.channels.editor.on('change', function (view) {
						var changed = view.elementSettingsModel.changed;

						if (changed.image_gaps !== undefined || changed.caption_container_padding !== undefined || changed.spacing_title !== undefined || changed.spacing_description !== undefined ) {
							setTimeout(function () {
								$('.elementor-element-<?php echo $this->get_id(); ?> .portfolio.news-grid').initPortfoliosGrid();
							}, 500);
						}
					});

				})(jQuery);

			</script>
		<?php endif;
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_Extended_BlogGrid());
