<?php

namespace TheGem_Elementor\Widgets\ProductsGrid;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Products Grid.
 */
class TheGem_ProductsGrid extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRODUCTSGRID_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRODUCTSGRID_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRODUCTSGRID_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRODUCTSGRID_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-products-grid-styles',
			THEGEM_ELEMENTOR_WIDGET_PRODUCTSGRID_URL . '/assets/css/thegem-portfolio-products.css',
			array(
				'thegem-portfolio-styles',
				'thegem-woocommerce'
			),
			null
		);

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
		return 'thegem-products-grid';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Classic Grid', 'thegem');
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
		return ['thegem_woocommerce'];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-button',
				'thegem-hovers-default',
				'thegem-hovers-zooming-blur',
				'thegem-hovers-horizontal-sliding',
				'thegem-hovers-vertical-sliding',
				'thegem-hovers-gradient',
				'thegem-hovers-circular',
				'thegem-products-grid-styles'];
		}
		return ['thegem-products-grid-styles'];
	}

	public function get_script_depends() {
		return ['thegem-woocommerce', 'thegem-portfolio-scripts'];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Make options select products sets
	 * @access protected
	 * @return array
	 */
	protected function select_products_sets() {
		$out = ['0' => __('All', 'thegem')];
		$terms = get_terms([
			'taxonomy' => 'product_cat',
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
				'default' => '4x',
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
					'6' => __('6 Columns', 'thegem'),
				],
				'condition' => [
					'columns' => '100%',
				],
				'description' => __('Number of columns for 100% width grid for desktop resolutions starting from 1920 px and above', 'thegem'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_products',
			[
				'label' => __('Products', 'thegem'),
			]
		);

		$this->add_control(
			'content_products_cat',
			[
				'label' => __('Select Products Categories', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->select_products_sets(),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'product_show_title', [
				'label' => __('Product Title', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'product_show_description', [
				'label' => __('Product Description', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'caption_position' => 'hover',
				],
			]
		);

		$this->add_control(
			'product_show_price', [
				'label' => __('Product Price', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'product_show_new', [
				'label' => __('"New" Label', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'new_label_text',
			[
				'label' => __('Label Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('New', 'thegem'),
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_show_sale', [
				'label' => __('"Sale" Label', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'sale_label_text',
			[
				'label' => __('Label Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Sale!', 'thegem'),
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_show_out', [
				'label' => __('"Out of stock" Label', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'out_label_text',
			[
				'label' => __('Label Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Out <span class=small>of stock</span>', 'thegem'),
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_show_reviews', [
				'label' => __('Reviews', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'product_show_filter', [
				'label' => __('Filter Buttons', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);

		$this->add_control(
			'product_show_sorting', [
				'label' => __('Sorting', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true
			]
		);


		if (!defined('YITH_WCWL')) {

			$this->add_control(
				'wishlist_warning_text',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('Please install “YITH WooCommerce Wishlist” plugin to activate “add to wishlist” feature.', 'thegem'),
					'content_classes' => 'elementor-descriptor',
				]
			);

		} else {

			$this->add_control(
				'product_show_wishlist', [
					'label' => __('Add to Wishlist', 'thegem'),
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true
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
				'label' => __('Ignore Highlighted Products', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'featured_only',
			[
				'label' => __('Show only “Featured” Products', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'sale_only',
			[
				'label' => __('Show only “On Sale” Products', 'thegem'),
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

		/* Images Style */
		$this->image_style($control);

		/* Caption Style */
		$this->caption_style($control);

		/* Caption Container Style */
		$this->caption_container_style($control);

		/* Filter Buttons Style */
		$this->filter_buttons_style($control);

		/* Sorting Style */
		$this->sorting_style($control);

		/* Pagination Style */
		$this->pagination_style($control);

		/* Pagination More Style */
		$this->pagination_more_style($control);

		/* Labels Style */
		$this->labels_style($control);

		/* Hover Icons (Custom) */
		$this->hover_icons_style($control);
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item:not(.size-item)' => 'padding: calc({{SIZE}}{{UNIT}}/2) !important;',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.products-grid.fullwidth-columns .portfolio-row' => 'margin: calc(-{{SIZE}}{{UNIT}}/2) 0;',
					'{{WRAPPER}} .portfolio.products-grid .fullwidth-block:not(.no-paddings)' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.products-grid .fullwidth-block .portfolio-row' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',
				]
			]
		);

		$control->add_control(
			'product_separator',
			[
				'label' => __('Product Separator', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),

			]
		);

		$control->add_control(
			'product_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .portfolio.item-separator .portfolio-item:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item:after, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:after' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'product_separator' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'product_separator_width',
			[
				'label' => __('Separator Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 4,
					],
				],
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.item-separator .portfolio-item:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item:after, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:before, 
					{{WRAPPER}} .portfolio.item-separator .portfolio-item .item-separator-box:after' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_separator' => 'yes',
				],
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.products-grid.title-on-page .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .portfolio.products-grid.title-on-hover .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.products-grid.title-on-image .portfolio-item .wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-grid:not(.shadowed-container) .portfolio-item .image, {{WRAPPER}} .portfolio.products-grid.shadowed-container .portfolio-item .wrap',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .image-inner' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-grid .portfolio-item .image-inner',
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
				'condition' => ['caption_position' => 'hover'],
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
				],
				'condition' => ['caption_position' => 'page'],
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
							'{{WRAPPER}} .portfolio.products-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.products-grid.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio.products-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.products-grid.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .portfolio.products-grid .portfolio-item .overlay:before, {{WRAPPER}} .portfolio.products-grid.hover-circular .portfolio-item .image .overlay .overlay-circle' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
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
				'selector' => '{{WRAPPER}} .portfolio.products-grid .portfolio-item:hover .image-inner',
			]
		);

		$control->add_control(
			'icons_header',
			[
				'label' => __('Icons', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => ['caption_position!' => 'page'],
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
				'condition' => ['caption_position!' => 'page'],
			]
		);

		$control->add_control(
			'icons_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .image .overlay .links .overlay-line:after' => 'background: {{VALUE}};'
				],
				'condition' => [
					'icons_show' => 'yes',
					'caption_position!' => 'page'
				],
			]
		);

		$control->add_control(
			'icons_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon' => 'background-color: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'icons_show',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'terms' => [
										[
											'name' => 'caption_position',
											'operator' => '=',
											'value' => 'hover',
										],
										[
											'relation' => 'or',
											'terms' => [
												[
													'name' => 'image_hover_effect_hover',
													'operator' => '=',
													'value' => 'zooming-blur',
												],
												[
													'name' => 'image_hover_effect_hover',
													'operator' => '=',
													'value' => 'gradient',
												],
											],
										],
									],


								],
								[
									'terms' => [
										[
											'name' => 'caption_position',
											'operator' => '=',
											'value' => 'image',
										],
										[
											'relation' => 'or',
											'terms' => [
												[
													'name' => 'image_hover_effect_image',
													'operator' => '=',
													'value' => 'zooming-blur',
												],
												[
													'name' => 'image_hover_effect_image',
													'operator' => '=',
													'value' => 'gradient',
												],
											],
										],
									],


								],
							],

						],
					],
				],
			]
		);


		$control->add_responsive_control(
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon i, {{WRAPPER}} .portfolio.products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon:before' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.products-grid.hover-zooming-blur .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.products-grid.hover-zooming-blur .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon:before, 
					{{WRAPPER}} .portfolio.products-grid.hover-gradient .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.products-grid.hover-gradient .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon:before' => 'font-size: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.products-grid.hover-zooming-blur .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon svg,
					{{WRAPPER}} .portfolio.products-grid.hover-gradient .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon svg' => 'width: calc({{SIZE}}{{UNIT}}/2); height: calc({{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'icons_show' => 'yes',
					'caption_position!' => 'page'
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
					'caption_position!' => 'page'
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
			'title' => __('Product Title', 'thegem'),
			'description' => __('Product Description', 'thegem'),
			'price' => __('Product Price', 'thegem'),
		];

		foreach ($caption_options as $ekey => $elem) {

			if ($ekey == 'price') {
				$selector = '{{WRAPPER}} .products .portfolio-item .product-info .price, {{WRAPPER}} .products .portfolio-item .product-info .price .amount, {{WRAPPER}} .products .portfolio-item .empty-price';
			} else if ($ekey == 'description') {
				$selector = '{{WRAPPER}} .products .portfolio-item .caption .description .subtitle';
			} else {
				$selector = '{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap .overlay .caption .' . $ekey;
			}

			$condition = [
				'caption_position' => ['hover', 'image'],
			];
			if ($ekey == 'description') {
				$condition = [
					'caption_position' => 'hover',
				];
			}



			$control->add_control(
				$ekey . '_header',
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			if ($ekey == 'title') {
				$this->add_control(
					'grid_title_style',
					[
						'label' => __('Title Color Preset', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'default' => 'light',
						'options' => [
							'light' => __('Light', 'thegem'),
							'dark' => __('Dark', 'thegem'),
						],
						'condition' => ['caption_position' => 'image'],
					]
				);
			}

			$control->add_group_control(Group_Control_Typography::get_type(),
				[
					'label' => __('Typography', 'thegem'),
					'name' => $ekey . '_typography',
					'selector' => $selector,
					'condition' => $condition
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
					'condition' => $condition
				]
			);

			$control->start_controls_tabs($ekey . '_tabs', [
				'condition' => [
					'caption_position' => 'page',
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

					if ($ekey == 'price') {
						$selector = '{{WRAPPER}} .products .portfolio-item' . $state . ' .product-info .price, {{WRAPPER}} .products .portfolio-item' . $state . ' .product-info .price .amount, {{WRAPPER}} .products .portfolio-item' . $state . ' .empty-price';
					} else {
						$selector = '{{WRAPPER}} .portfolio.products-grid .portfolio-item' . $state . ' .wrap .caption .' . $ekey;
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

					$control->end_controls_tab();

				}
			}

			$control->end_controls_tabs();
		}

		$control->add_control(
			'caption_icons_header',
			[
				'label' => __('Icons', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'caption_position' => 'page',
				],
			]
		);

		$control->add_control(
			'caption_icons_show',
			[
				'label' => __('Show', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => ['caption_position' => 'page'],
			]
		);

		$control->add_control(
			'caption_icons_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-bottom a.icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .portfolio.products-grid .product-bottom a.add_to_wishlist' => 'color: {{VALUE}}',
				],
				'condition' => [
					'caption_icons_show' => 'yes',
					'caption_position' => 'page'
				],
			]
		);


		$control->add_responsive_control(
			'caption_icons_size',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .product-bottom a.icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.products-grid .portfolio-item .product-bottom a.icon:before' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.products-grid.hover-zooming-blur .portfolio-item .product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.products-grid.hover-zooming-blur .portfolio-item .product-bottom a.icon:before, 
					{{WRAPPER}} .portfolio.products-grid.hover-gradient .portfolio-item .product-bottom a.icon i, 
					{{WRAPPER}} .portfolio.products-grid.hover-gradient .portfolio-item .product-bottom a.icon:before' => 'font-size: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .product-bottom a.icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.products-grid.hover-zooming-blur .portfolio-item .product-bottom a.icon svg,
					{{WRAPPER}} .portfolio.products-grid.hover-gradient .portfolio-item .product-bottom a.icon svg' => 'width: calc({{SIZE}}{{UNIT}}/2); height: calc({{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'caption_icons_show' => 'yes',
					'caption_position' => 'page'
				],
			]
		);


		$control->add_control(
			'caption_icons_customize',
			[
				'label' => __('Want to customize?', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'caption_icons_show' => 'yes',
					'caption_position' => 'page'
				],
			]
		);

		$control->add_control(
			'rating_header',
			[
				'label' => __('Rating Stars', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'rated_color',
			[
				'label' => __('Rated Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .portfolio-item .star-rating > span:before' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'caption_position' => ['hover', 'image'],
				]
			]
		);

		$control->add_control(
			'base_color',
			[
				'label' => __('Base Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .portfolio-item .star-rating:before' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'caption_position' => ['hover', 'image'],
				]
			]
		);

		$control->start_controls_tabs('rating_tabs', [
			'condition' => [
				'caption_position' => 'page',
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

				$control->start_controls_tab('rating_tab_' . $stkey, [
					'label' => $stelem,
					'condition' => $condition
				]);

				$control->add_control(
					'rated_color_' . $stkey,
					[
						'label' => __('Rated Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .woocommerce .portfolio-item' . $state . ' .star-rating > span:before' => 'color: {{VALUE}};',
						],
						'condition' => $condition
					]
				);

				$control->add_control(
					'base_color_' . $stkey,
					[
						'label' => __('Base Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .woocommerce .portfolio-item' . $state . ' .star-rating:before' => 'color: {{VALUE}};',
						],
						'condition' => $condition
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.products-grid.title-on-page .portfolio-item .wrap' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius:{{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption .star-rating' => 'margin-{{VALUE}}: 0',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption .product-rating .empty-rating:before' => 'margin-{{VALUE}}: 0',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption .product-bottom' => 'margin-{{VALUE}}: 0',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'caption_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption',
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
						'selector' => '{{WRAPPER}} .portfolio.products-grid .portfolio-item' . $state . ' .wrap > .caption',
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
						'selector' => '{{WRAPPER}} .portfolio.products-grid .portfolio-item' . $state . ' .wrap > .caption',
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'spacing_title_header',
			[
				'label' => 'Product Title',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'spacing_price_header',
			[
				'label' => 'Product Price',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'spacing_price',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption .product-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .portfolio.products-grid .product-info .product-rating .empty-rating:before' => 'border-width: {{SIZE}}{{UNIT}}',
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
							'{{WRAPPER}} .portfolio.products-grid .portfolio-item' . $state . ' .product-info .product-rating .empty-rating:before' => 'border-color: {{VALUE}};',
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
							'{{WRAPPER}} .portfolio.products-grid .portfolio-item' . $state . ' .product-info .product-rating .empty-rating:before' => 'width: {{SIZE}}{{UNIT}}',
						]
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'spacing_icons_header',
			[
				'label' => 'Icons',
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_responsive_control(
			'spacing_icons',
			[
				'label' => __('Spacing Between', 'thegem'),
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-item .wrap > .caption .product-bottom > *' => 'margin: 0 calc({{SIZE}}{{UNIT}}/2)',
				]
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
				'condition' => ['product_show_filter' => 'yes']
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
				'condition' => ['product_show_sorting' => ''],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .portfolio-filters' => 'text-align: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-filters a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_buttons_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-grid .portfolio-filters a',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-filters a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .portfolio.products-grid .portfilio-top-panel' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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
							'{{WRAPPER}} .portfolio.products-grid .portfolio-filters a' . $state => 'background-color: {{VALUE}};',
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
							'{{WRAPPER}} .portfolio.products-grid .portfolio-filters a' . $state => 'border-color: {{VALUE}};',
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
							'{{WRAPPER}} .portfolio.products-grid .portfolio-filters a' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'filter_buttons_text_typography' . $stkey,
						'selector' => '{{WRAPPER}} .portfolio.products-grid .portfolio-filters a' . $state . ' span',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-filters-resp .menu-toggle i' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-filters-resp .menu-toggle i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .portfolio.products-grid .portfolio-filters-resp .menu-toggle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'filter_responsive_typography',
				'selector' => '{{WRAPPER}} .portfolio.products-grid .portfolio-filters-resp ul li a',
			]
		);

		$control->add_control(
			'filter_responsive_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .portfolio-filters-resp ul li a' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-filters-resp ul li' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-filters-resp ul li, {{WRAPPER}} .portfolio.products-grid .portfolio-filters-resp ul' => 'border-color: {{VALUE}};',
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
				'condition' => ['product_show_sorting' => 'yes']
			]
		);

		$control->add_control(
			'switch_background_color',
			[
				'label' => __('Switch Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .sorting-switcher' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio.products-grid .sorting-switcher:after' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-sorting-sep' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-sorting label' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'sorting_text_typography',
				'selector' => '{{WRAPPER}} .portfolio.products-grid .portfolio-sorting label',
			]
		);

		$this->add_control(
			'sorting_date_text',
			[
				'label' => __('“Date” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Date', 'thegem'),
			]
		);

		$this->add_control(
			'sorting_name_text',
			[
				'label' => __('“Name” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Name', 'thegem'),
			]
		);

		$this->add_control(
			'sorting_desc_text',
			[
				'label' => __('“Desc” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Desc', 'thegem'),
			]
		);

		$this->add_control(
			'sorting_asc_text',
			[
				'label' => __('“Asc” Text', 'thegem'),
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
					'{{WRAPPER}} .portfolio.products-grid .gem-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .portfolio.products-grid .gem-pagination .prev i, {{WRAPPER}} .portfolio.products-grid .gem-pagination .next i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};
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
					'{{WRAPPER}} .portfolio.products-grid .gem-pagination .prev, {{WRAPPER}} .portfolio.products-grid .gem-pagination .next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .portfolio.products-grid .gem-pagination .prev, {{WRAPPER}} .portfolio.products-grid .gem-pagination .next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
							'{{WRAPPER}} .portfolio.products-grid .gem-pagination .prev' . $state . ', {{WRAPPER}} .portfolio.products-grid .gem-pagination .next' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'pagination_arrows_border_type' . $stkey,
						'label' => __('Border Type', 'thegem'),
						'selector' => '{{WRAPPER}} .portfolio.products-grid .gem-pagination .prev' . $state . ', {{WRAPPER}} .portfolio.products-grid .gem-pagination .next' . $state,
					]
				);

				$control->add_control(
					'pagination_arrows_icon_color' . $stkey,
					[
						'label' => __('Icon Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio.products-grid .gem-pagination .prev' . $state . ', {{WRAPPER}} .portfolio.products-grid .gem-pagination .next' . $state => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio.products-grid .portfolio-load-more' => 'margin-top: {{SIZE}}{{UNIT}};',
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
				'label' => __('Separator', 'thegem'),
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
	 * Labels Style
	 * @access protected
	 */
	protected function labels_style($control) {

		$control->start_controls_section(
			'labels_style',
			[
				'label' => __('Labels Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'product_show_new',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'name' => 'product_show_sale',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'name' => 'product_show_out',
							'operator' => '=',
							'value' => 'yes',
						]
					],
				],
			]
		);

		$control->add_control(
			'new_label_heading',
			[
				'label' => __('“New” Label', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'new_label_size',
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
//					'{{WRAPPER}} .portfolio.products-grid .product .new-label' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .portfolio.products-grid .product .new-label' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_control(
			'new_label_box_rotate',
			[
				'label' => __('Rotate Box', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product .new-label' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .portfolio.products-grid .product .new-label .rotate-back' => 'transform: rotate(-{{SIZE}}deg); -webkit-transform: rotate(-{{SIZE}}deg);',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_control(
			'new_label_text_rotate',
			[
				'label' => __('Rotate Text', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product .new-label .text' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'new_label_background',
			[
				'label' => __('Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product .new-label' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'new_label_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product .new-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'new_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-grid .product .new-label',
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'new_label_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product .new-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_control(
			'new_label_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product .new-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'new_label_text_typo',
				'selector' => '{{WRAPPER}} .portfolio.products-grid .product .new-label',
				'condition' => [
					'product_show_new' => 'yes',
				],
			]
		);

		$control->add_control(
			'sale_label_heading',
			[
				'label' => __('“Sale” Label', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sale_label_size',
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
					'{{WRAPPER}} .portfolio.products-grid .product-labels .onsale' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_control(
			'sale_label_box_rotate',
			[
				'label' => __('Rotate Box', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .onsale' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .portfolio.products-grid .product-labels .onsale .rotate-back' => 'transform: rotate(-{{SIZE}}deg); -webkit-transform: rotate(-{{SIZE}}deg);',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_control(
			'sale_label_text_rotate',
			[
				'label' => __('Rotate Text', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .onsale .text' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sale_label_background',
			[
				'label' => __('Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .onsale' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sale_label_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sale_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-grid .product-labels .onsale',
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'sale_label_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .onsale' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_control(
			'sale_label_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .onsale' => 'color: {{VALUE}};',
				],
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'sale_label_text_typo',
				'selector' => '{{WRAPPER}} .portfolio.products-grid .product-labels .onsale',
				'condition' => [
					'product_show_sale' => 'yes',
				],
			]
		);

		$control->add_control(
			'out_label_heading',
			[
				'label' => __('“Out of stock” Label', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'out_label_size',
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
					'{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_control(
			'out_label_box_rotate',
			[
				'label' => __('Rotate Box', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label .rotate-back' => 'transform: rotate(-{{SIZE}}deg); -webkit-transform: rotate(-{{SIZE}}deg);',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_control(
			'out_label_text_rotate',
			[
				'label' => __('Rotate Text', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label .text' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'out_label_background',
			[
				'label' => __('Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'out_label_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'out_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label',
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'out_label_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_control(
			'out_label_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'out_label_text_typo',
				'selector' => '{{WRAPPER}} .portfolio.products-grid .product-labels .out-of-stock-label',
				'condition' => [
					'product_show_out' => 'yes',
				],
			]
		);

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
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'terms' => [
								[
									'name' => 'icons_customize',
									'operator' => '=',
									'value' => 'yes',
								],
								[
									'name' => 'caption_position',
									'operator' => '!=',
									'value' => 'page',
								],
							],
						],
						[
							'terms' => [
								[
									'name' => 'caption_icons_customize',
									'operator' => '=',
									'value' => 'yes',
								],
								[
									'name' => 'caption_position',
									'operator' => '=',
									'value' => 'page',
								],
							]
						]
					],
				],
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
			'cart' => __('Cart Icon', 'thegem'),
			'product-page' => __('Product Page Icon', 'thegem'),
			'wishlist' => __('Add to Wishlist Icon', 'thegem'),
		];

		foreach ($icons_list as $ekey => $elem) {

			$condition = [
				'terms' => [
					[
						'relation' => 'or',
						'terms' => [
							[
								'terms' => [
									[
										'name' => 'icons_customize',
										'operator' => '=',
										'value' => 'yes',
									],
									[
										'name' => 'caption_position',
										'operator' => '!=',
										'value' => 'page',
									],
								],
							],
							[
								'terms' => [
									[
										'name' => 'caption_icons_customize',
										'operator' => '=',
										'value' => 'yes',
									],
									[
										'name' => 'caption_position',
										'operator' => '=',
										'value' => 'page',
									],
								]
							]
						],
					]
				]
			];

			if ($ekey == 'share') {
				array_push($condition['terms'],[
					'name'  => 'social_sharing',
					'operator' => '=',
					'value' => 'yes',
				]);
			}

			$control->add_control(
				'hover_icon_header_' . $ekey,
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'conditions' => $condition
				]
			);

			$control->add_control(
				'hover_icon_' . $ekey,
				[
					'label' => __('Icon', 'thegem'),
					'type' => Controls_Manager::ICONS,
					'conditions' => $condition
				]
			);

			$control->add_control(
				'hover_icon_color_' . $ekey,
				[
					'label' => __('Icon Color', 'thegem'),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .portfolio.products-grid .portfolio-item .product-bottom a.icon.' . $ekey => 'color: {{VALUE}};',
						'{{WRAPPER}} .portfolio.products-grid .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon.' . $ekey => 'color: {{VALUE}};',
					],
					'conditions' => $condition,
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
						'{{WRAPPER}} .portfolio.products .portfolio-item .product-bottom a.' . $ekey . ' i, {{WRAPPER}} .portfolio.products .portfolio-item .product-bottom a.' . $ekey . ' svg, {{WRAPPER}} .portfolio.products .portfolio-item .product-bottom a.' . $ekey . ':before' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					],
					'conditions' => $condition,
				]
			);

			$control->end_controls_tab();

		}

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
			'action' => 'products_grid_load_more',
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('products_grid_ajax-nonce')
		);
		wp_localize_script('thegem-portfolio-scripts', 'thegem_portfolio_ajax_' . $grid_uid, $localize);
		$settings['action'] = 'products_grid_load_more';

		$terms = $settings['content_products_cat'];

		if ($settings['caption_position'] == 'image') {
			$hover_effect = $settings['image_hover_effect_image'];
		} else if ($settings['caption_position'] == 'page') {
			$hover_effect = $settings['image_hover_effect_page'];
		} else {
			$hover_effect = $settings['image_hover_effect_hover'];
		}

		wp_enqueue_style('thegem-hovers-' . $hover_effect);

		if ($settings['pagination_type'] == 'more') {
			wp_enqueue_style('thegem-button');
		}

		$portfolio_uid = $this->get_id();

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

		if ($settings['product_show_sorting'] == 'yes') {
			$orderby = 'date';
			$order = 'DESC';
		} else {
			$orderby = 'menu_order ID';
			$order = 'ASC';
		}

		$featured_only = $settings['featured_only'] == 'yes' ? true : false;
		$sale_only = $settings['sale_only'] == 'yes' ? true : false;

		if ($settings['pagination_type'] == 'more' || $settings['pagination_type'] == 'scroll') {
			if ($layout_columns_count == -1)
				$layout_columns_count = 5;
			$product_loop = thegem_get_product_posts($terms, $page, $items_per_page, $orderby, $order, $featured_only, $sale_only);
			if (ceil($product_loop->found_posts / $items_per_page) > $page)
				$next_page = $page + 1;
			else
				$next_page = 0;
		} else {
			$product_loop = thegem_get_product_posts($terms, 1, -1, $orderby, $order, $featured_only, $sale_only);
		}

		$portfolio_title = '';
		global $post;
		$portfolio_posttemp = $post;

		if ($product_loop && $product_loop->have_posts()) : ?>
			<?php

			if (in_array('0', $terms)) {
				$terms = get_terms('product_cat', array('hide_empty' => true));
			} else {
				foreach ($terms as $key => $term) {
					$terms[$key] = get_term_by('slug', $term, 'product_cat');
					if (!$terms[$key]) {
						unset($terms[$key]);
					}
				}
			}

			$terms = apply_filters('portfolio_terms_filter', $terms);

			usort($terms, 'portolios_cmp');
			$thegem_terms_set = array();
			foreach ($terms as $term) {
				$thegem_terms_set[$term->slug] = $term;
			} ?>
			<?php echo apply_filters('thegem_portfolio_preloader_html', '<div class="preloader"><div class="preloader-spin"></div></div>'); ?>
			<div class="portfolio-preloader-wrapper">
				<?php if ($portfolio_title): ?>
					<h3 class="title portfolio-title"><?php echo $portfolio_title; ?></h3>
				<?php endif; ?>

				<?php
				if ($settings['caption_position'] == 'hover') {
					$title_on = 'hover';
				} else {
					$title_on = 'page';
				}
				$this->add_render_attribute(
					'products-wrap',
					[
						'class' => [
							'portfolio portfolio-grid',
							'products-grid',
							'woocommerce',
							'products',
							'no-padding',
							'portfolio-pagination-' . $settings['pagination_type'],
							'portfolio-style-' . $settings['layout'],
							'background-style-' . $settings['caption_container_preset'],
							'title-style-' . $settings['grid_title_style'],
							'hover-' . $hover_effect,
							'title-on-' . $title_on,
							($settings['animation_effect'] ? 'item-animation-' . $settings['animation_effect'] : ''),
							($settings['image_gaps']['size'] == 0 ? 'no-gaps' : ''),
							($settings['shadowed_container'] == 'yes' ? 'shadowed-container' : ''),
							($settings['columns'] == '100%' ? 'fullwidth-columns fullwidth-columns-' . $settings['columns_100'] : ''),
							($settings['caption_position'] == 'image' && $settings['image_hover_effect_image'] == 'gradient' ? 'hover-gradient-title' : ''),
							($settings['caption_position'] == 'image' && $settings['image_hover_effect_image'] == 'circular' ? 'hover-circular-title' : ''),
							($settings['caption_position'] == 'hover' || $settings['caption_position'] == 'image' ? 'hover-title' : ''),
							($settings['social_sharing'] != 'yes' ? 'portfolio-disable-socials' : ''),
							($settings['layout'] == 'masonry' && $settings['columns'] != '1x' ? 'portfolio-items-masonry' : ''),
							($layout_columns_count != -1 ? 'columns-' . intval($layout_columns_count) : ''),
							($settings['product_separator'] == 'yes' ? 'item-separator' : ''),
						],
						'data-per-page' => $items_per_page,
						'data-next-page' => $next_page,
						'data-portfolio-uid' => $portfolio_uid,
						'data-hover' => $hover_effect,
					]
				);

				?>

				<div <?php echo $this->get_render_attribute_string('products-wrap'); ?>>
					<?php if (($settings['product_show_filter'] == 'yes' && count($terms) > 0) || $settings['product_show_sorting'] == 'yes'): ?>
						<div class="portfilio-top-panel<?php if ($settings['columns'] == '100%'): ?> fullwidth-block<?php endif; ?>">
							<div class="portfilio-top-panel-row">
								<div class="portfilio-top-panel-left">
									<?php if ($settings['product_show_filter'] == 'yes' && count($terms) > 0): ?>
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
									<?php if ($settings['product_show_sorting'] == 'yes'): ?>
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
								while ($product_loop->have_posts()) : $product_loop->the_post(); ?>
									<?php echo thegem_product_render_item(get_the_ID(), $settings); ?>
									<?php $eo_marker = !$eo_marker;
								endwhile;
								wp_reset_postdata();
								?>
							</div><!-- .portflio-set -->
							<?php if ($settings['columns'] != '1x'): ?>
								<div class="portfolio-item-size-container">
									<?php echo thegem_product_render_item(false, $settings); ?>
								</div>
							<?php endif; ?>
						</div><!-- .row-->
						<?php

						/** Pagination */

						if ('yes' === ($settings['show_pagination'])) : ?>
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
						<?php endif; ?>
					</div><!-- .full-width -->
				</div><!-- .portfolio-->
			</div><!-- .portfolio-preloader-wrapper-->
		<?php

		else: ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select products in "Products" section', 'thegem') ?>
			</div>
		<?php endif;
		$post = $portfolio_posttemp;

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script>
				if (typeof widget_settings == 'undefined') {
					var widget_settings = [];
				}
				widget_settings['<?php echo $grid_uid ?>'] = JSON.parse('<?php echo json_encode($settings) ?>');
			</script>
			<script type="text/javascript">
				(function ($) {

					setTimeout(function () {
						if (!$('.elementor-element-<?php echo $this->get_id(); ?> .products-grid').length) {
							return;
						}
						$('.elementor-element-<?php echo $this->get_id(); ?> .products-grid').initPortfoliosGrid();
					}, 1000);

					elementor.channels.editor.on('change', function (view) {
						var changed = view.elementSettingsModel.changed;

						if (changed.image_gaps !== undefined || changed.caption_container_padding !== undefined || changed.spacing_title !== undefined || changed.spacing_description !== undefined) {
							setTimeout(function () {
								$('.elementor-element-<?php echo $this->get_id(); ?> .products-grid').initPortfoliosGrid();
							}, 500);
						}
					});

				})(jQuery);

			</script>
		<?php endif;
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_ProductsGrid());