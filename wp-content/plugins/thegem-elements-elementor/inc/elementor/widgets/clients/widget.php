<?php

namespace TheGem_Elementor\Widgets\Clients;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;


if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Team.
 */
class TheGem_Clients extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */
	protected $presets;

	public $preset_elements_select;

	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		if ( !defined('THEGEM_ELEMENTOR_WIDGET_CLIENTS_DIR' )) {
			define('THEGEM_ELEMENTOR_WIDGET_CLIENTS_DIR', rtrim(__DIR__, ' /\\'));
		}

		if ( !defined('THEGEM_ELEMENTOR_WIDGET_CLIENTS_URL') ) {
			define('THEGEM_ELEMENTOR_WIDGET_CLIENTS_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-clients', THEGEM_ELEMENTOR_WIDGET_CLIENTS_URL . '/assets/css/thegem-clients.css', array(), null);
		wp_register_script('thegem-clients', THEGEM_ELEMENTOR_WIDGET_CLIENTS_URL . '/assets/js/thegem-clients.js', array('jquery', 'jquery-carouFredSel'), null, true);
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */

	public function get_name() {

		return 'thegem-clients';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */

	public function get_title() {

		return __('Clients', 'thegem');
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

		return ['thegem-clients'];
	}

	public function get_script_depends() {

		return ['thegem-clients'];
	}

	public function is_reload_preview_required() {

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
			return $this->get_settings()[$control_id];
		}
		else {
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
			'clients-grid'	 => __('Grid', 'thegem'),
			'clients-carousel' => __('Carousel', 'thegem'),
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

		return 'clients-grid';
	}


	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		// Sections Layout
		$this->start_controls_section(
			'clients_layout',
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
			'grid_rows',
			[
				'label' => __('Rows', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['clients-grid']
				],
				'type' => Controls_Manager::SELECT,
				'default' => 1,
				'options' => [
					1 => __('1 Row', 'thegem'),
					2 => __('2 Rows', 'thegem'),
					3 => __('3 Rows', 'thegem'),
					4 => __('4 Rows', 'thegem'),
					5 => __('5 Rows', 'thegem'),
					6 => __('6 Rows', 'thegem'),
				],
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[
				'label' => __('Columns', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['clients-grid']
				],
				'type' => Controls_Manager::SELECT,
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'default' => 3,
				'options' => [
					1 => __('1 Column',  'thegem'),
					2 => __('2 Columns', 'thegem'),
					3 => __('3 Columns', 'thegem'),
					4 => __('4 Columns', 'thegem'),
					5 => __('5 Columns', 'thegem'),
					6 => __('6 Columns', 'thegem'),
				],
				'desktop_default' => 3,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
			]
		);

		$this->add_control(
			'stretch_full_width',
			[
				'condition' => [
					'thegem_elementor_preset' => ['clients-carousel']
				],
				'label' => 'Stretch to Full Width',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'thegem'),
				'label_off' => __('No', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		// Sections Clients
		$this->start_controls_section(
			'clients_settings',
			[
				'label' => __('Clients', 'thegem'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'clients_image',
			[
				'label' => __('Logo', 'thegem'),
				'type' => Controls_Manager::MEDIA,
				'frontend_available' => true,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'clients_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'show_external' => true,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],

			]
		);

		$repeater->add_control(
			'clients_name',
			[
				'label' => __('Brand Name', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __('Brand Name', 'thegem'),
			]
		);

		$this->add_control(
			'clients_logo_list',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ clients_name }}}',
				'default' => [
					['image' => ['url' => Utils::get_placeholder_image_src()]],
					['image' => ['url' => Utils::get_placeholder_image_src()]],
					['image' => ['url' => Utils::get_placeholder_image_src()]],
				]
			]
		);

		$this->end_controls_section();

		// Sections Options
		$this->start_controls_section(
			'clients_options',
			[
				'label' => __('Options', 'thegem'),
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'imagesize',
				'default' => 'full',
			]
		);

		$this->add_control(
			'navigation_dots',
			[
				'condition' => [
					'thegem_elementor_preset' => ['clients-grid']
				],
				'label' => 'Navigation Dots',
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'navigation_arrows',
			[
				'condition' => [
					'thegem_elementor_preset' => ['clients-carousel']
				],
				'label' => 'Navigation Arrows',
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoscroll',
			[
				'label' => 'Autoscroll',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoscroll_speed',
			[
				'condition' => [
					'autoscroll' => ['yes']
				],
				'label' => __('Autoscroll Speed, ms', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100000,
				'step' => 100,
				'default' => 0,
			]
		);

		$this->add_control(
			'lazy_loading',
			[
				'label' => 'Lazy Loading',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->add_styles_controls($this);
	}


	/**
	 * Container Styles
	 * @access protected
	 */
	protected function container_styles( $control ) {

		$control->start_controls_section(
			'counter_container',
			[
				'label' => __('Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'thegem_elementor_preset' => ['clients-grid'],
				],
			]
		);

		$control->add_responsive_control(
			'clients_container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-client-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$control->add_responsive_control(
			'clients_container_height',
			[
				'label' => __('Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
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
					'{{WRAPPER}} .gem-clients-type-carousel-grid  .gem-client-item a' => 'height:{{SIZE}}{{UNIT}};max-height:100%;',
				],
			]
		);

		$control->add_responsive_control(
			'clients_container_gaps',
			[
				'label' => __('Gaps', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
					'{{WRAPPER}} .gem-clients-type-carousel-grid .gem-client-item a' => 'width:calc(100% - {{SIZE}}{{UNIT}}); margin:0px 0px {{SIZE}}{{UNIT}} 0px;',
				],
			]
		);

		$control->add_responsive_control(
			'clients_padding_container_image',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-clients-type-carousel-grid .gem-client-item a img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'clients_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-client-item a',
			]
		);

		$control->start_controls_tabs('clients_container_tabs');

		$control->start_controls_tab('clients_container_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'clients_container_background',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-client-item a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'clients_container_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-client-item a',

			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('clients_container_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_responsive_control(
			'clients_container_background_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-client-item a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'clients_container_border_hover',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-client-item a:hover',

			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * Image Styles
	 * @access protected
	 */
	protected function image_styles( $control ) {

		$control->start_controls_section(
			'clients_image_style',
			[
				'label' => __('Image Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->start_controls_tabs('clients_image_tabs');

		$control->start_controls_tab('clients_image_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'clients_image_opacity',
			[
				'label' => __('Opacity', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-client-item a img' => 'opacity: {{SIZE}};',
				],
			]
		);

		// CSS Filters
		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-client-item a img',
			]
		);

		// Blend Mode
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
					'{{WRAPPER}} .gem-client-item a img' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->add_control(
			'filter_grayscale_image',
			[
				'label' => 'Grayscale',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'frontend_available' => true,
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('clients_image_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_responsive_control(
			'clients_image_opacity_hv',
			[
				'label' => __('Opacity', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-client-item a:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		// CSS Filters
		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal_hv',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-client-item a:hover img',
			]
		);

		$this->add_control(
			'filter_grayscale_image_hv',
			[
				'label' => 'Grayscale',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'frontend_available' => true,
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();
	}


	/**
	 * Navigation Dots Style
	 * @access protected
	 */
	protected function navigation_dots_style( $control ) {

		$control->start_controls_section(
			'clients_navigation_dots_style',
			[
				'label' => __('Navigation Dots Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'thegem_elementor_preset' => ['clients-grid'],
				],
			]
		);

		$control->add_responsive_control(
			'clients_size_dots',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 78,
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
					'size' => 17,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-mini-pagination a' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'clients_spacing_dots',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => -300,
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
					'size' => 30,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-mini-pagination' => 'margin-top:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'clients_between_dots',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
					'size' => 5,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-mini-pagination a' => 'margin-left:{{SIZE}}{{UNIT}}; margin-right:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->start_controls_tabs('clients_navigation_dots_tabs');

		$control->start_controls_tab('clients_navigation_dots_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'clients_navigation_dots_background',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-clients-grid-pagination a:not(.selected)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'clients_navigation_dots_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-clients-grid-pagination a:not(.selected)',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('clients_navigation_dots_tab_active', ['label' => __('Active', 'thegem'),]);

		$control->add_responsive_control(
			'clients_navigation_dots_background_hv',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-mini-pagination a.selected' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'clients_navigation_dots_border_hv',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-mini-pagination a.selected',
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * Arrows Style
	 * @access protected
	 */
	protected function arrows_styles( $control ) {

		$control->start_controls_section(
			'clients_arrows_styles',
			[
				'label' => __('Arrows Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'thegem_elementor_preset' => ['clients-carousel'],
				],
			]
		);

		$control->add_responsive_control(
			'clients_size_arrows',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
					'size' => 40,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-client-carousel-navigation a:after' => 'width:{{SIZE}}{{UNIT}} !important; height:{{SIZE}}{{UNIT}} !important; line-height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'clients_icon_size_arrows',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
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
					'{{WRAPPER}} .gem-client-carousel-navigation a:after' => 'font-size:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'clients_arrows_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-client-carousel-navigation .gem-client-prev:after, .gem-client-next:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'clients_navigation_arrows_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-client-carousel-navigation .gem-prev:after, {{WRAPPER}} .gem-client-carousel-navigation .gem-next:after',
			]
		);

		$control->add_responsive_control(
			'clients_arrow_bottom_spacing',
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
				'default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-client-carousel-navigation .gem-client-prev:after, {{WRAPPER}} .gem-client-carousel-navigation .gem-client-next:after' => 'margin-top:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('clients_arrows_tabs');

		$control->start_controls_tab('clients_arrows_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'clients_navigation_arrows_background',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-client-carousel-navigation .gem-prev:after, {{WRAPPER}} .gem-client-carousel-navigation .gem-next:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'clients_nav_arrows_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-client-carousel-navigation .gem-prev:after, {{WRAPPER}} .gem-client-carousel-navigation .gem-next:after',
			]
		);

		$control->add_responsive_control(
			'clients_navigation_arrows_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-client-carousel-navigation .gem-prev:after, {{WRAPPER}} .gem-client-carousel-navigation .gem-next:after' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('clients_arrows_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_responsive_control(
			'clients_navigation_arrows_background_hv',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-client-carousel-navigation .gem-prev:hover:after, {{WRAPPER}} .gem-client-carousel-navigation .gem-next:hover:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'clients_nav_arrows_border_hv',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-client-carousel-navigation .gem-prev:hover:after, {{WRAPPER}} .gem-client-carousel-navigation .gem-next:hover:after',
			]
		);

		$control->add_responsive_control(
			'clients_navigation_arrows_icon_color_hv',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-client-carousel-navigation .gem-prev:hover:after, {{WRAPPER}} .gem-client-carousel-navigation .gem-next:hover:after' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls( $control ) {

		$this->control = $control;

		/*Container Styles*/
		$this->container_styles( $control );

		/*Image  Styles*/
		$this->image_styles( $control );

		/*Navigation Dots Styles*/
		$this->navigation_dots_style( $control );

		/*Arrows  Styles*/
		$this->arrows_styles( $control );

	}


	/** Get current preset
	 * @param $val
	 * @return string
	 */
	protected function get_setting_preset( $val ) {

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
	public function render() {

		$settings = $this->get_settings_for_display();

		if ('yes' === $settings['lazy_loading']) {
			thegem_lazy_loading_enqueue();
		}

		$preset = $this->get_setting_preset($settings['thegem_elementor_preset']);

		if ( empty($preset) ) return;

		$preset_path = __DIR__ . '/templates/output-' . $preset . '.php';

		?>

		<div class="gem-clients-container <?php echo $preset; ?>">

			<div class="preloader"><div class="preloader-spin"></div></div>

			<?php
				if ( !empty($preset_path) && file_exists($preset_path)) {
					include($preset_path);
				}
			?>
		</div>

		<?php

		if ( is_admin() && Plugin::$instance->editor->is_edit_mode() ): ?>

			<script type="text/javascript">
				jQuery('.elementor-element-<?php echo $this->get_id(); ?> .gem-clients-type-carousel-grid').updateClientsGrid();
				jQuery('.elementor-element-<?php echo $this->get_id(); ?> .gem_client_carousel-items').updateClientsCarousel();
			</script>

		<?php endif;
	}


	/**
	 * Show Grid Clients
	 * @param $settings
	 */
	private function show_grid_clients( $settings ) {

		$clients = $settings['clients_logo_list'];

		$classes = array();

		$rows = (int) $settings['grid_rows'];

		// For Desktop
		$cols = (int) $settings['grid_columns'];

		$items_per_slide = $rows * $cols;

		$start_block = '<div class="gem-clients-slide"><div class="gem-clients-slide-inner clearfix">';

		$end_block = '</div></div>';

		$content_items = [];

		switch ( $settings['grid_columns'] ) {
			case 1:
				$classes[] = 'col-md-12'; break;
			case 2:
				$classes[] = 'col-md-6'; break;
			case 3:
				$classes[] = 'col-md-4'; break;
			case 4:
				$classes[] = 'col-md-3'; break;
			case 5:
				$classes[] = 'col-md-1-5'; break;
			default:
				$classes[] = 'col-md-2'; break;
		}

		switch ( $settings['grid_columns_tablet'] ) {
			case 1:
				$classes[] = 'col-sm-12'; break;
			case 2:
				$classes[] = 'col-sm-6'; break;
			case 3:
				$classes[] = 'col-sm-4'; break;
			case 4:
				$classes[] = 'col-sm-3'; break;
			case 5:
				$classes[] = 'col-sm-1-5'; break;
			default:
				$classes[] = 'col-sm-2'; break;
		}

		switch ( $settings['grid_columns_mobile'] ) {
			case 1:
				$classes[] = 'col-xs-12'; break;
			case 2:
				$classes[] = 'col-xs-6'; break;
			case 3:
				$classes[] = 'col-xs-4'; break;
			case 4:
				$classes[] = 'col-xs-3'; break;
			case 5:
				$classes[] = 'col-xs-1-5'; break;
			default:
				$classes[] = 'col-xs-2'; break;
		}

		$col_classes = implode(' ', $classes);

		$link = '';

		foreach ( $clients as $index => $item ) {

			$image_url = ! empty( $item['clients_image']['url'] ) ? Group_Control_Image_Size::get_attachment_image_src($item['clients_image']['id'], 'imagesize', $settings) : Utils::get_placeholder_image_src();
			$image_url = ( false !== $image_url ) ? $image_url : Utils::get_placeholder_image_src();

			$filter_grayscale = ! empty( $settings['filter_grayscale_image'] ) ? 'gem-grayscale-normal' : '';

			$filter_grayscale_hover = ! empty( $settings['filter_grayscale_image_hv'] ) ? 'gem-grayscale-hover' : '';

			$lazy_loading_item = ( 'yes' === $settings['lazy_loading'] ) ? 'lazy-loading-item' : '';

			if ( ! empty( $item['clients_link']['url'] ) ) {
				$link_key = 'link_' . $index;
				$this->add_link_attributes( $link_key, $item['clients_link'] );
				$link = $this->get_render_attribute_string( $link_key );
			}

			$content_items[] = '<div class="gem-client-item ' . $col_classes . ' '.$lazy_loading_item.'" data-ll-effect="drop-bottom">
									<a ' . $link . '>
									<img class="'.$filter_grayscale.' '.$filter_grayscale_hover.'" src="' . $image_url . '">
									</a>
								</div>';
			$link = null;
		}

		$chunk_array = array_chunk($content_items, $items_per_slide);

		foreach ( $chunk_array as $first_arr ) {
			echo $start_block;
			foreach ( $first_arr as $second_arr ) echo $second_arr;
			echo $end_block;
		}

	}




	/**
	 * Show Carousel Clients
	 * @param $settings
	 */

	private function show_carousel_clients( $settings ) {

		$clients = $settings['clients_logo_list'];

		$link = '';

		foreach ( $clients as $index => $item ) {

			$image_url = ! empty( $item['clients_image']['url'] ) ? Group_Control_Image_Size::get_attachment_image_src($item['clients_image']['id'], 'imagesize', $settings) : Utils::get_placeholder_image_src();
			$image_url = ( false !== $image_url ) ? $image_url : Utils::get_placeholder_image_src();

			$filter_grayscale = ! empty( $settings['filter_grayscale_image'] ) ? 'gem-grayscale-normal' : '';

			$filter_grayscale_hover = ! empty( $settings['filter_grayscale_image_hv'] ) ? 'gem-grayscale-hover' : '';

			$lazy_loading_item = ( 'yes' === $settings['lazy_loading'] ) ? 'lazy-loading-item' : '';

			if ( ! empty( $item['clients_link']['url'] ) ) {
				$link_key = 'link_' . $index;
				$this->add_link_attributes( $link_key, $item['clients_link'] );
				$link = $this->get_render_attribute_string( $link_key );
			}


			echo '<div class="gem-client-item '.$lazy_loading_item.'" data-ll-effect="drop-right">
							<a ' . $link . '>
								<img class="'.$filter_grayscale.' '.$filter_grayscale_hover.'" width="200" src="' . $image_url . '">
							</a>
						  </div>';

			$link = null;
		}
	}

}

Plugin::instance()->widgets_manager->register_widget_type( new TheGem_Clients() );