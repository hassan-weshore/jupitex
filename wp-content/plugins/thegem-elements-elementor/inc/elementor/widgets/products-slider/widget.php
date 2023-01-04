<?php

namespace TheGem_Elementor\Widgets\ProductsSlider;

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
 * Elementor widget for Products Slider.
 */
class TheGem_ProductsSlider extends Widget_Base {

	public $states_list;

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRODUCTSSLIDER_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRODUCTSSLIDER_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRODUCTSLIDER_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRODUCTSLIDER_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-products-slider',
			THEGEM_ELEMENTOR_WIDGET_PRODUCTSLIDER_URL . '/assets/css/thegem-products-slider.css',
			array(
				'thegem-portfolio-slider',
				'thegem-woocommerce'
			),
			null
		);

		wp_register_script(
			'thegem-products-slider',
			THEGEM_ELEMENTOR_WIDGET_PRODUCTSLIDER_URL . '/assets/js/thegem-products.js',
			array(
				'jquery',
				'thegem-juraSlider',
				'thegem-woocommerce'
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
		return 'thegem-products-slider';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Products Slider', 'thegem');
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
				'thegem-hovers-default',
				'thegem-hovers-zooming-blur',
				'thegem-hovers-horizontal-sliding',
				'thegem-hovers-vertical-sliding',
				'thegem-hovers-gradient',
				'thegem-hovers-circular',
				'thegem-products-slider'];
		}
		return ['thegem-products-slider'];
	}

	public function get_script_depends() {
		return ['thegem-products-slider'];
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
				'default' => '3x',
				'options' => [
					'2x' => __('2x columns', 'thegem'),
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
					'6' => __('6 Columns', 'thegem'),
				],
				'condition' => [
					'columns' => '100%',
				],
				'description' => __(' Number of columns for 100% width portfolio slider 
					for desktop resolutions starting from 1920 px and above', 'thegem'),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __('Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'masonry',
				'options' => [
					'masonry' => __('Masonry', 'thegem'),
					'justified' => __('Justified', 'thegem'),
				],
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
				'label' => __('Select Product Categories', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->select_products_sets(),
				'frontend_available' => true,
			]
		);

		$products_fields = [
			'title' => __('Product Title', 'thegem'),
			'description' => __('Description', 'thegem'),
			'price' => __('Product Price', 'thegem'),
			'new_label' => __('New', 'thegem'),
			'sale_label' => __('SALE!', 'thegem'),
			'out_label' => __('Out of stock', 'thegem'),
			'reviews' => __('Reviews', 'thegem'),
			'wishlist' => __('Add To Wishlist', 'thegem'),
		];

		foreach ($products_fields as $ekey => $elem) {

			$condition = [];

			if ($ekey == 'description') {
				$condition = [
					'caption_position' => 'hover',
				];
			}

			if ($ekey == 'out_label') {

				$this->add_control(
					'products_show_' . $ekey,
					[
						'label' => __('"' . $elem . '" Label', 'thegem'),
						'default' => 'yes',
						'type' => Controls_Manager::SWITCHER,
						'label_on' => __('Show', 'thegem'),
						'label_off' => __('Hide', 'thegem'),
						'frontend_available' => true,
						// 'condition' => $condition,
					]
				);

				$this->add_control(
					'products_text_' . $ekey,
					[
						'label' => __('Label Text', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'input_type' => 'text',
						'default' => __('Out <span class="small">of stock</span>', 'thegem'),
						'condition' => [
							'products_show_' . $ekey => ['yes'],
						]
					]
				);
			} else if ($ekey == 'new_label' || $ekey == 'sale_label') {

				$this->add_control(
					'products_show_' . $ekey,
					[
						'label' => __('"' . $elem . '" Label', 'thegem'),
						'default' => 'yes',
						'type' => Controls_Manager::SWITCHER,
						'label_on' => __('Show', 'thegem'),
						'label_off' => __('Hide', 'thegem'),
						'frontend_available' => true,
						// 'condition' => $condition,
					]
				);

				$this->add_control(
					'products_text_' . $ekey,
					[
						'label' => __('Label Text', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'input_type' => 'text',
						'default' => $elem,
						'condition' => [
							'products_show_' . $ekey => ['yes'],
						]
					]
				);

			} else if ($ekey == 'wishlist') {

				if (function_exists('thegem_is_plugin_active') && thegem_is_plugin_active('yith-woocommerce-wishlist/init.php')) {

					$this->add_control(
						'products_show_' . $ekey,
						[
							'label' => $elem,
							'default' => 'yes',
							'type' => Controls_Manager::SWITCHER,
							'label_on' => __('Show', 'thegem'),
							'label_off' => __('Hide', 'thegem'),
							'frontend_available' => true,
							// 'condition' => $condition,
						]
					);

				} else {

					$this->add_control(
						'YITH_WCWL_note',
						[
							'type' => Controls_Manager::RAW_HTML,
							'raw' => __('Please install "YITH WooCommerce Wishlist" plugin to activate "add to wishlist" feature'),
							'content_classes' => 'elementor-control-field-description',
						]
					);

				}

			} else {

				$this->add_control(
					'products_show_' . $ekey,
					[
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
			'left_icon_2_3x',
			[
				'label' => __('Left Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'gem-mdi mdi-chevron-left',
					'library' => 'thegem-mdi',
				],
				'condition' => [
					'arrows_show' => 'yes',
					'columns!' => '100%',
				],
			]
		);

		$this->add_control(
			'right_icon_2_3x',
			[
				'label' => __('Right Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'gem-mdi mdi-chevron-right',
					'library' => 'thegem-mdi',
				],
				'condition' => [
					'arrows_show' => 'yes',
					'columns!' => '100%',
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

		/* Labels Style */
		$this->labels_style($control);

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
					'{{WRAPPER}} .portfolio-slider.item-separator .portfolio-item:before, 
					{{WRAPPER}} .portfolio-slider.item-separator .portfolio-item:after, 
					{{WRAPPER}} .portfolio-slider.item-separator .portfolio-item .item-separator-box:before, 
					{{WRAPPER}} .portfolio-slider.item-separator .portfolio-item .item-separator-box:after' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio-slider.item-separator .portfolio-item:before, 
					{{WRAPPER}} .portfolio-slider.item-separator .portfolio-item:after, 
					{{WRAPPER}} .portfolio-slider.item-separator .portfolio-item .item-separator-box:before, 
					{{WRAPPER}} .portfolio-slider.item-separator .portfolio-item .item-separator-box:after' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'product_separator' => 'yes',
				],
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
				'condition' => [
					'caption_position!' => 'page',
				],
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
				'condition' => [
					'caption_position!' => 'page',
				],
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
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image .overlay .links .yith-wcwl-add-to-wishlist i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image .overlay .links .yith-wcwl-wishlistexistsbrowse a:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image .overlay .links .overlay-line:after' => 'background: {{VALUE}};'
				],
				'condition' => [
					'icons_show' => 'yes',
					'caption_position!' => 'page',
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
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image .overlay .links .portfolio-icons.product-bottom a.icon' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image .overlay .links a.icon i' => 'font-size: {{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image .overlay .links .yith-wcwl-add-to-wishlist i' => 'font-size: {{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .portfolio-slider .portfolio-item .image .overlay .links .yith-wcwl-wishlistexistsbrowse a:before' => 'font-size: {{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .portfolio-slider.hover-zooming-blur .portfolio-item .image .overlay .links a.icon' => 'width: calc(24px + {{SIZE}}{{UNIT}})!important;height: calc(24px + {{SIZE}}{{UNIT}})!important;line-height: calc(24px + {{SIZE}}{{UNIT}})!important;',
				],
				'condition' => [
					'icons_show' => 'yes',
					'caption_position!' => 'page',
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
					'caption_position!' => 'page',
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

		// $control->add_control(
		// 	'title_header',
		// 		[
		// 			'label' => __('Product Title', 'thegem'),
		// 			'type' => Controls_Manager::HEADING,
		// 			'separator' => 'before',
		// 		]
		// 	);

		// $control->start_controls_tabs('caption_title_tabs');
		// $control->start_controls_tab('caption_title_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		// $control->end_controls_tab();
		// $control->start_controls_tab('caption_title_tabs_hover', ['label' => __('Hover', 'thegem'),]);

		// $control->end_controls_tab();
		// $control->end_controls_tabs();	

		$caption_options = [
			'title' => __('Product Title', 'thegem'),
			'description' => __('Product Description', 'thegem'),
			'price' => __('Product Price', 'thegem'),
			'capicons' => __('Icons', 'thegem'),
			'stars' => __('Rating Stars', 'thegem'),
		];

		foreach ($caption_options as $ekey => $elem) {

			$condition = [
				'caption_position' => ['hover', 'image'],
			];

			// set = category field
			if ($ekey == 'title') {
				$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .overlay  .caption .' . $ekey;
			} else if ($ekey == 'description') {
				$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .caption .description .subtitle';
				$condition = [
					'caption_position' => 'hover',
				];
			}else if ($ekey == 'price') {
				$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .overlay .caption .price, {{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .overlay .caption .price .amount';
			} else if ($ekey == 'stars') {
				$rated_selector = '{{WRAPPER}} .portfolio.portfolio-slider .star-rating > span:before';
				$base_selector = '{{WRAPPER}} .portfolio.portfolio-slider .star-rating:before';
			} else {
				$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .overlay  .caption .' . $ekey;
			}

			if ($ekey == 'capicons') {

				$control->add_control(
					$ekey . '_header',
					[
						'label' => $elem,
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'caption_position' => 'page',
						],
					]
				);

				$control->add_control(
					'capicons_show',
					[
						'label' => __('Show', 'thegem'),
						'default' => 'yes',
						'type' => Controls_Manager::SWITCHER,
						'label_on' => __('Show', 'thegem'),
						'label_off' => __('Hide', 'thegem'),
						'frontend_available' => true,
						'condition' => [
							'caption_position' => 'page',
						],
					]
				);

				$control->add_control(
					'capicons_color',
					[
						'label' => __('Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-slider .portfolio-item .product-bottom a.icon i' => 'color: {{VALUE}};',
							'{{WRAPPER}} .portfolio-slider .portfolio-item .product-bottom .yith-wcwl-add-to-wishlist i' => 'color: {{VALUE}};',
							'{{WRAPPER}} .portfolio-slider .portfolio-item .product-bottom .yith-wcwl-wishlistexistsbrowse a:before' => 'color: {{VALUE}};',
						],
						'condition' => [
							'capicons_show' => 'yes',
							'caption_position' => 'page',
						],
					]
				);


				$control->add_control(
					'capicons_size',
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
							'{{WRAPPER}} .portfolio-slider .portfolio-item .product-bottom a.icon i' => 'font-size: {{SIZE}}{{UNIT}}!important;',
							'{{WRAPPER}} .portfolio-slider .portfolio-item .product-bottom .yith-wcwl-add-to-wishlist i' => 'font-size: {{SIZE}}{{UNIT}}!important;',
							'{{WRAPPER}} .portfolio-slider .portfolio-item .product-bottom .yith-wcwl-wishlistexistsbrowse a:before' => 'font-size: {{SIZE}}{{UNIT}}!important;',
						],
						'condition' => [
							'capicons_show' => 'yes',
							'caption_position' => 'page',
						],
					]
				);


				$control->add_control(
					'capicons_customize',
					[
						'label' => __('Want to customize?', 'thegem'),
						'default' => '',
						'type' => Controls_Manager::SWITCHER,
						'label_on' => __('On', 'thegem'),
						'label_off' => __('Off', 'thegem'),
						'condition' => [
							'capicons_show' => 'yes',
							'caption_position' => 'page',
						],
					]
				);
			} elseif ($ekey == 'stars') {

				$control->add_control(
					$ekey . '_header',
					[
						'label' => $elem,
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$control->add_control(
					$ekey . '_rated_color',
					[
						'label' => __('Rated Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							$rated_selector => 'color: {{VALUE}} !important;',
						],
						'condition' => [
							'caption_position' => ['hover', 'image'],
						]
					]
				);

				$control->add_control(
					$ekey . '_base_color',
					[
						'label' => __('Base Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							$base_selector => 'color: {{VALUE}} !important;',
						],
						'condition' => [
							'caption_position' => ['hover', 'image'],
						]
					]
				);

			} else {

				$control->add_control(
					$ekey . '_header',
					[
						'label' => $elem,
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				if ($ekey == 'title') {

					$control->add_control(
						'title_color_preset',
						[
							'label' => __('Title Color Preset', 'thegem'),
							'type' => Controls_Manager::SELECT,
							'default' => 'light',
							'options' => [
								'light' => __('Light', 'thegem'),
								'dark' => __('Dark', 'thegem'),
							],
							'condition' => ['caption_position' => 'image',],
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
							$selector => 'color: {{VALUE}}!important;',
						],
						'condition' => $condition
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
					if ($ekey == 'title') {
						$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .wrap > .caption .title a' . $state;
					} else if ($ekey == 'price') {
						$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .product-info .price, {{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .product-info .price .amount';
					} else if ($ekey == 'stars') {
						$rated_selector = '{{WRAPPER}} .portfolio.portfolio-slider .star-rating' . $state . ' > span:before';
						$base_selector = '{{WRAPPER}} .portfolio.portfolio-slider .star-rating' . $state . ':before';
					} else {
						$selector = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item' . $state . ' .caption .' . $ekey;
					}

					if ($ekey == 'stars') {

						$control->start_controls_tab($ekey . '_tab_' . $stkey, [
							'label' => $stelem,
							'condition' => $condition
						]);

						$control->add_control(
							$ekey . '_rated_color' . $stkey,
							[
								'label' => __('Rated Color', 'thegem'),
								'type' => Controls_Manager::COLOR,
								'label_block' => false,
								'selectors' => [
									$rated_selector => 'color: {{VALUE}} !important;',
								],
								'condition' => $condition
							]
						);

						$control->add_control(
							$ekey . '_base_color' . $stkey,
							[
								'label' => __('Base Color', 'thegem'),
								'type' => Controls_Manager::COLOR,
								'label_block' => false,
								'selectors' => [
									$base_selector => 'color: {{VALUE}} !important;',
								],
								'condition' => $condition
							]
						);

					} elseif ($ekey == 'title' || $ekey == 'price') {

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
									$selector => 'color: {{VALUE}}!important;',
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
					'transparent' => __('Transparent', 'thegem'),
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

		$control->add_responsive_control(
			'caption_container_alignment',
			[
				'label' => __('Content Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'center',
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
						'selector' => '{{WRAPPER}} .portfolio-slider .portfolio-item' . $state . ' .wrap > .caption',
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
						'selector' => '{{WRAPPER}} .portfolio-slider .portfolio-item' . $state . ' .wrap > .caption',
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

		$this->add_control(
			'spacing_title',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portfolio-item .wrap > .caption .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'spacing_price',
			[
				'label' => __('Top and Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .portfolio-slider .portfolio-item .wrap > .caption .product-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .products-slider .product-info .product-rating .empty-rating:before' => 'border-top-width: {{SIZE}}{{UNIT}}',
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
							'{{WRAPPER}} .products-slider .portfolio-item' . $state . ' .product-info .product-rating .empty-rating:before' => 'border-color: {{VALUE}}',
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
							'{{WRAPPER}} .products-slider .portfolio-item' . $state . ' .product-info .product-rating .empty-rating:before' => 'width: {{SIZE}}{{UNIT}}',
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
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .products-slider .portfolio-item .wrap > .caption .product-bottom .yith-wcwl-add-to-wishlist,
					{{WRAPPER}} .products-slider .portfolio-item .wrap > .caption .product-bottom .add_to_cart_button,
					{{WRAPPER}} .products-slider .portfolio-item .wrap > .caption .product-bottom .full-product-link,
					{{WRAPPER}} .products-slider .portfolio-item .wrap > .caption .product-bottom .post-footer-sharing' => 'margin-left: {{SIZE}}px;margin-right: {{SIZE}}px;',
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
			'arrows_style',
			[
				'label' => __('Arrows Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'arrow_preset_2_3x',
			[
				'label' => __('Size Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'small' => __('Small', 'thegem'),
					'big' => __('Big', 'thegem'),
				],
				'condition' => [
					'columns' => ['2x', '3x'],
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
							'{{WRAPPER}} .portfolio-slider .portolio-slider-prev' . $state . ' span, {{WRAPPER}} .portfolio-slider .portolio-slider-next' . $state . ' span' => 'background-color: {{VALUE}};',
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
							'{{WRAPPER}} .portfolio-slider .portolio-slider-prev' . $state . ' span, {{WRAPPER}} .portfolio-slider .portolio-slider-next' . $state . ' span' => 'border-color: {{VALUE}};',
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
							'{{WRAPPER}} .portfolio-slider .portolio-slider-prev' . $state . ' span i, {{WRAPPER}} .portfolio-slider .portolio-slider-next' . $state . ' span i' => 'color: {{VALUE}}!important;',
							'{{WRAPPER}} .portfolio-slider .portolio-slider-prev' . $state . ' span svg, {{WRAPPER}} .portfolio-slider .portolio-slider-next' . $state . ' span svg' => 'fill: {{VALUE}}!important;',
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
							'name' => 'products_show_new_label',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'name' => 'products_show_sale_label',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'name' => 'products_show_out_label',
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
					'products_show_new_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product .new-label' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'products_show_new_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product .new-label' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .portfolio.products-slider .product .new-label .rotate-back' => 'transform: rotate(-{{SIZE}}deg); -webkit-transform: rotate(-{{SIZE}}deg);',
				],
				'condition' => [
					'products_show_new_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product .new-label .text' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'products_show_new_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product .new-label' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'products_show_new_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product .new-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'products_show_new_label' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'new_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-slider .product .new-label',
				'condition' => [
					'products_show_new_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product .new-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'products_show_new_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product .new-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'products_show_new_label' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'new_label_text_typo',
				'selector' => '{{WRAPPER}} .portfolio.products-slider .product .new-label',
				'condition' => [
					'products_show_new_label' => 'yes',
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
					'products_show_sale_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .onsale' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'products_show_sale_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .onsale' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .portfolio.products-slider .product-labels .onsale .rotate-back' => 'transform: rotate(-{{SIZE}}deg); -webkit-transform: rotate(-{{SIZE}}deg);',
				],
				'condition' => [
					'products_show_sale_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .onsale .text' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'products_show_sale_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .onsale' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'products_show_sale_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'products_show_sale_label' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sale_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-slider .product-labels .onsale',
				'condition' => [
					'products_show_sale_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .onsale' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'products_show_sale_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .onsale' => 'color: {{VALUE}};',
				],
				'condition' => [
					'products_show_sale_label' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'sale_label_text_typo',
				'selector' => '{{WRAPPER}} .portfolio.products-slider .product-labels .onsale',
				'condition' => [
					'products_show_sale_label' => 'yes',
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
					'products_show_out_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'products_show_out_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					'{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label .rotate-back' => 'transform: rotate(-{{SIZE}}deg); -webkit-transform: rotate(-{{SIZE}}deg);',
				],
				'condition' => [
					'products_show_out_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label .text' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'products_show_out_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'products_show_out_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'products_show_out_label' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'out_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label',
				'condition' => [
					'products_show_out_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'products_show_out_label' => 'yes',
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
					'{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'products_show_out_label' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'out_label_text_typo',
				'selector' => '{{WRAPPER}} .portfolio.products-slider .product-labels .out-of-stock-label',
				'condition' => [
					'products_show_out_label' => 'yes',
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
									'name' => 'caption_position',
									'operator' => '==',
									'value' => 'page'
								],
								[
									'name' => 'capicons_customize',
									'operator' => '==',
									'value' => 'yes'
								]
							]
						],
						[
							'terms' => [
								[
									'name' => 'caption_position',
									'operator' => '!=',
									'value' => 'page'
								],
								[
									'name' => 'icons_customize',
									'operator' => '==',
									'value' => 'yes'
								],
							]
						]
					]
				]
			]
		);

		// $this->add_control(
		// 	'important_note',
		// 	[
		// 		'type' => Controls_Manager::RAW_HTML,
		// 		'raw' => __('Hover icons depend on types of portfolio item (image, link, video etc.). If some type is not selected, corresponding icon will not appear. ', 'thegem'),
		// 		'content_classes' => 'elementor-control-field-description',
		// 	]
		// );

		$icons_list = [
			'share' => [
				'name' => __('Sharing Button', 'thegem'),
				'icon' => [
					'value' => 'gem-mdi mdi-share-variant',
					'library' => 'thegem-mdi',
				]
			],
			'cart' => [
				'name' => __('Cart Icon', 'thegem'),
				'icon' => [
					'value' => 'gem-mdi mdi-cart',
					'library' => 'thegem-mdi',
				]
			],
			'full' => [
				'name' => __('Product Page Icon', 'thegem'),
				'icon' => [
					'value' => 'gem-elegant icon-menu-square-alt',
					'library' => 'thegem-mdi',
				]
			],
			'wishlist' => [
				'name' => __('Add to Wishlist Icon', 'thegem'),
				'icon' => [
					'value' => 'far fa-heart',
					'library' => 'fa-regular',
				]
			],
		];

		foreach ($icons_list as $ekey => $elem) {

			$condition = [];

			$add_selector = '';
			if ($ekey == 'share') {
				$condition = [
					'social_sharing' => 'yes'
				];
				$selector1 = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .portfolio-icons-inner .share';
				$selector2 = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .caption .product-bottom .post-footer-sharing';
			} else if ($ekey == 'cart') {
				$selector1 = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .portfolio-icons-inner .add_to_cart_button';
				$selector2 = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .caption .product-bottom .add_to_cart_button';
			} else if ($ekey == 'full') {
				$selector1 = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .portfolio-icons-inner .full-product-link';
				$selector2 = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .caption .product-bottom .full-product-link';
			} else {
				$condition = [
					'products_show_wishlist' => 'yes'
				];
				$selector1 = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .portfolio-icons-inner .yith-wcwl-add-to-wishlist';
				$selector2 = '{{WRAPPER}} .portfolio.portfolio-slider .portfolio-item .caption .product-bottom .yith-wcwl-add-to-wishlist';
			}

			$control->add_control(
				'hover_icon_header_' . $ekey,
				[
					'label' => $elem['name'],
					'type' => Controls_Manager::HEADING,
					'condition' => $condition
				]
			);

			$control->add_control(
				'hover_icon_' . $ekey,
				[
					'label' => __('Icon', 'thegem'),
					'type' => Controls_Manager::ICONS,
					// 'default' => $elem['icon'],
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
						$selector1 . ' i' => 'color: {{VALUE}}!important;',
						$selector1 . ' svg' => 'fill: {{VALUE}}!important;',
						$selector2 . ' i' => 'color: {{VALUE}}!important;',
						$selector2 . ' svg' => 'fill: {{VALUE}}!important;',
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
						$selector1 . ' i' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
						$selector1 . ' svg' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
						$selector2 . ' i' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
						$selector2 . ' svg' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
					],
					'condition' => $condition
				]
			);

			$control->end_controls_tab();


		}

		$control->end_controls_section();
	}

	public function get_products($product_cat, $featured_only = false, $sale_only = false) {
		if (empty($product_cat)) {
			return null;
		}

		$tax_query = [];

		if (!in_array('0', $product_cat, true)) {
			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field' => 'slug',
				'terms' => $product_cat
			);
		}

		if ($featured_only) {
			$tax_query[] = array(
				'taxonomy' => 'product_visibility',
				'field' => 'name',
				'terms' => 'featured',
			);
		}

		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'orderby' => 'menu_order title',
			'order' => 'ASC',
			'posts_per_page' => -1,
			'tax_query' => $tax_query,
		);

		if ($sale_only) {
			$args['post__in'] = array_merge(array(0), wc_get_product_ids_on_sale());
		}

		$product_loop = new WP_Query($args);

		return $product_loop;
	}


	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render() {

		global $post, $product, $woocommerce_loop;

		$settings = $this->get_settings_for_display();

		if ('yes' === $settings['loading_animation']) {
			thegem_lazy_loading_enqueue();
		}

		$terms = $settings['content_products_cat'];

		if ($settings['caption_position'] == 'image') {
			$hover_effect = $settings['image_hover_effect_image'];
		} else if ($settings['caption_position'] == 'page') {
			$hover_effect = $settings['image_hover_effect_page'];
		} else {
			$hover_effect = $settings['image_hover_effect_hover'];
		}

		wp_enqueue_style('thegem-hovers-' . $hover_effect);

		if ($settings['caption_position'] == 'hover') {
			$title_on = 'hover';
		} else {
			$title_on = 'page';
		}

		wp_enqueue_script('thegem-woocommerce');

		$this->add_render_attribute(
			'products-wrap',
			[
				'class' => [
					'portfolio portfolio-slider products-slider products clearfix col-lg-12 col-md-12 col-sm-12',
					($settings['columns'] === '100%' ? 'fullwidth-columns-' . $settings['columns_100'] : 'columns-3'),
					'background-style-' . $settings['caption_container_preset'],
					'caption-' . $settings['caption_container_alignment'],
					'portfolio-items-' . $settings['style'],
					'portfolio_slider_arrow_' . ($settings['columns'] === '100%' ? $settings['arrow_preset_100'] : $settings['arrow_preset_2_3x']),
					'hover-' . $hover_effect,
					'title-on-' . $title_on,
					'gem-slider-animation-' . $settings['animation_effect'],
					($settings['shadowed_container'] === 'yes' ? 'shadowed-container' : ''),
					($settings['loading_animation'] === 'yes' ? 'lazy-loading' : ''),
					($settings['caption_position'] === 'image' ? 'title-style-' . $settings['title_color_preset'] : ''),
					($settings['caption_position'] === 'image' && $settings['image_hover_effect_image'] === 'gradient' ? 'hover-gradient-title' : ''),
					($settings['caption_position'] === 'image' && $settings['image_hover_effect_image'] === 'circular' ? 'hover-circular-title' : ''),
					($settings['caption_position'] === 'hover' || $settings['caption_position'] === 'image' ? 'hover-title' : ''),
					($settings['social_sharing'] != 'yes' ? 'portfolio-disable-socials' : ''),
					($settings['product_separator'] == 'yes' ? 'item-separator' : ''),
				],
				'data-hover' => $hover_effect,
			]
		);

		if ('yes' === $settings['autoscroll']) {
			$this->add_render_attribute('scroll-wrap', 'data-autoscroll', $settings['autoscroll_speed']['size']);
		}

		$featured_only = $settings['featured_only'] == 'yes' ? true : false;
		$sale_only = $settings['sale_only'] == 'yes' ? true : false;

		if (!empty ($terms)) {
			$product_loop = $this->get_products($terms, $featured_only, $sale_only);
		} else { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select product categories in "Products" section', 'thegem') ?>
			</div>
			<?php return;
		}

		wp_enqueue_script('thegem-woocommerce');

		if ($product_loop && $product_loop->have_posts()) {

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
			<div class="preloader">
				<div class="preloader-spin"></div>
			</div>
			<div <?php echo $this->get_render_attribute_string('products-wrap') . ('yes' === $settings['loading_animation'] ? ' data-ll-item-delay="0"' : ''); ?> >
				<div class="navigation <?php if ($settings['columns'] === '100%'): ?>fullwidth-block<?php endif; ?>">
					<?php if ($settings['arrows_show'] === 'yes'): ?>
						<div class="portolio-slider-prev">
						<span>
							<?php if ($settings['columns'] === '100%') {
								if (!empty($settings['left_icon_full']['value'])) {
									Icons_Manager::render_icon($settings['left_icon_full'], ['aria-hidden' => 'true']);
								}
							} else {
								if (!empty($settings['left_icon_2_3x']['value'])) {
									Icons_Manager::render_icon($settings['left_icon_2_3x'], ['aria-hidden' => 'true']);
								}
							} ?>
						</span>
						</div>
						<div class="portolio-slider-next">
						<span>
							<?php if ($settings['columns'] === '100%') {
								if (!empty($settings['right_icon_full']['value'])) {
									Icons_Manager::render_icon($settings['right_icon_full'], ['aria-hidden' => 'true']);
								}
							} else {
								if (!empty($settings['right_icon_2_3x']['value'])) {
									Icons_Manager::render_icon($settings['right_icon_2_3x'], ['aria-hidden' => 'true']);
								}
							} ?>
						</span>
						</div>
					<?php endif; ?>
					<div class="portolio-slider-content">
						<div class="portolio-slider-center">
							<div class="<?php if ($settings['columns'] == '100%'): ?>fullwidth-block<?php endif; ?>">
								<div>
									<div class="portfolio-set clearfix" <?php echo $this->get_render_attribute_string('scroll-wrap'); ?>>

										<?php while ($product_loop->have_posts()) : $product_loop->the_post();
											$slugs = wp_get_object_terms($post->ID, 'product_cat', array('fields' => 'slugs'));

											$preset_path = __DIR__ . '/templates/content-product-carusel-item.php';
											$preset_path_filtered = apply_filters( 'thegem_products_slider_item_preset', $preset_path);
											$preset_path_theme = get_stylesheet_directory() . '/templates/products-slider/content-product-carusel-item.php';

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
		<?php } else {
			echo __('Products not found', 'thegem');
		}
		wp_reset_postdata();

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script type="text/javascript">
				jQuery('.elementor-element-<?php echo $this->get_id(); ?> .portfolio.products-slider').initPortfoliosSlider();

				elementor.channels.editor.on('change', function (view) {
					var changed = view.elementSettingsModel.changed;

					// if (changed.image_gaps || changed.spacing_description || changed.spacing_title) {
					if (changed.image_gaps) {
						jQuery('.elementor-element-<?php echo $this->get_id(); ?> .portfolio.products-slider').initPortfoliosSlider();
					}
				});

			</script>
		<?php endif;
	}
}


\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TheGem_ProductsSlider());

