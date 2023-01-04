<?php

namespace TheGem_Elementor\Widgets\TheGem_Styled_Button;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Team.
 */
class TheGem_Styled_Button extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */
	protected $presets;

	public $preset_elements_select;

	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		if ( ! defined('THEGEM_ELEMENTOR_WIDGET_STYLED_BUTTON_DIR' )) {
			   define('THEGEM_ELEMENTOR_WIDGET_STYLED_BUTTON_DIR', rtrim(__DIR__, ' /\\'));
		}

		if ( ! defined('THEGEM_ELEMENTOR_WIDGET_STYLED_BUTTON_URL') ) {
			   define('THEGEM_ELEMENTOR_WIDGET_STYLED_BUTTON_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-button', THEGEM_ELEMENTOR_WIDGET_STYLED_BUTTON_URL . '/assets/css/thegem-button.css', array(), null);
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */

	public function get_name() {

		return 'thegem-styledbutton';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */

	public function get_title() {

		return __('Button', 'thegem');
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

		return [ 'thegem_elements' ];
	}

	public function get_style_depends() {

		return [ 'thegem-button' ];
	}

	public function get_script_depends() {

		return [];
	}

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
			'flat'    => __('Flat', 'thegem'),
			'outline' => __('Outline', 'thegem'),
		);

		return $out;
	}

	/**
	 * Get default presets options for Select
	 *
	 *
	 * @access protected
	 * @return string
	 */
	protected function set_default_presets_options() {

		return 'flat';
	}


	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		// Sections Layout
		$this->start_controls_section(
			'layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'thegem_button_skin',
			[
				'label' => __('Skin', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'tiny',
				'options' => [
					'tiny'   => __('Tiny', 'thegem'),
					'small'  => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large'  => __('Large', 'thegem'),
					'giant'  => __('Giant', 'thegem'),
				],
			]
		);

		$this->add_control(
			'stretch_full_width',
			[
				'label' => 'Stretch to Full Width',
				'condition' => [
					'show_separator' => [''],
				],
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();


		// Sections Content
		$this->start_controls_section(
			'content',
			[
				'label' => __('Content', 'thegem'),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __('Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __('Button Text', 'thegem'),
			]
		);

		$this->add_control(
			'button_text_weight',
			[
				'label' => __('Text Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold' => __('Bold', 'thegem'),
					'thin' => __('Thin', 'thegem'),
				],
			]
		);

		$this->add_control(
			'button_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'show_external' => true,
				'label_block' => true,
				'default' => [
					'url' => '#'
				]
			]
		);

		$this->add_control(
			'add_icon',
			[
				'label' => 'Add Icon',
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'button_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'add_icon'	=> [ 'yes' ]
				],
			]
		);

		$this->end_controls_section();


		// Sections Separator
		$this->start_controls_section(
			'_separator',
			[
				'label' => __('Separator', 'thegem'),
			]
		);

		$this->add_control(
			'show_separator',
			[
				'label' => 'Show Separator',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();


		// Sections Additional Options
		$this->start_controls_section(
			'_options',
			[
				'label' => __('Additional Options', 'thegem'),
			]
		);

		$this->add_control(
			'effects_enabled',
			[
				'label' => 'Lazy Loading Animation',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'button_id',
			[
				'label' => __('Button ID', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __('Button Text', 'thegem'),

			]
		);

		$this->add_control(
			'button_class',
			[
				'label' => __('Button Class', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __('Button Text', 'thegem'),

			]
		);

		$this->end_controls_section();

		$this->add_styles_controls($this);

	}



	/**
	 * Button Styles
	 * @access protected
	 */
	protected function button_styles( $control ) {

		$control->start_controls_section(
			'_button_style',
			[
				'label' => __('Button Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [],
			]
		);

		$control->add_responsive_control(
			'button_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'condition' => [
					'show_separator' => [''],
					'stretch_full_width' => [''],
				],
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => false,
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .gem-button-container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$control->add_responsive_control(
			'button_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button .gem-inner-wrapper-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('styledbutton_button_tabs');

		$control->start_controls_tab('styledbutton_button_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'label' => __( 'Background Color', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-button-container .gem-button'
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'label' => __( 'Border', 'thegem' ),
				'condition' => [],
				'selector' => '{{WRAPPER}} .gem-button-container .gem-button',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-button-container .gem-button',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('button_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_hv',
				'label' => __( 'Background Color 1', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .gem-button-container .gem-button:hover'
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border_hv',
				'label' => __( 'Border', 'thegem' ),
				'condition' => [],
				'selector' => '{{WRAPPER}} .gem-button-container .gem-button:hover',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_shadow_hv',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-button-container .gem-button:hover',
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}



	/**
	 * Text  Styles
	 * @access protected
	 */
	protected function text_styles( $control ) {

		$control->start_controls_section(
			'button_text_style',
			[
				'label' => __('Text Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [],
			]
		);

		$control->start_controls_tabs('styledbutton_button_text_tabs');

		$control->start_controls_tab('styledbutton_button_text_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
			'button_text_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button .gem-text-button' => 'color:{{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'button_typ_text',
				'selector' => '{{WRAPPER}} .gem-button-container .gem-button .gem-text-button',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('button_text_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'button_text_color_hv',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button:hover .gem-text-button' => 'color:{{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'button_typ_text_hv',
				'selector' => '{{WRAPPER}} .gem-button-container .gem-button:hover .gem-text-button',
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * Icon  Styles
	 * @access protected
	 */
	protected function icon_style( $control ) {

		$control->start_controls_section(
			'button_icon_style',
			[
				'label' => __('Icon Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'add_icon'	=> [ 'yes' ]
				],
			]
		);

		$control->add_responsive_control(
			'button_position_icon',
			[
				'label' => __('Position', 'thegem'),
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
				'default' => 'left',
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .gem-inner-wrapper-btn' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'button_spacing_icon_right',
			[
				'label' => __('Spacing', 'thegem'),
				'condition' => [
					'button_position_icon' => ['right'],
				],
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
					'{{WRAPPER}} .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-left:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_spacing_icon_left',
			[
				'label' => __('Spacing', 'thegem'),
				'condition' => [
					'button_position_icon' => ['left'],
				],
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
					'{{WRAPPER}} .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-right:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'button_size_icon',
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
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button .gem-button-icon' => 'font-size:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('styledbutton_button_icon_tabs');

		$control->start_controls_tab('styledbutton_button_icon_tab_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
			'button_icon_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button .gem-button-icon' => 'color:{{VALUE}};',
					'{{WRAPPER}} .gem-button-container .gem-button .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_control(
			'button_icon_rotate',
			[
				'label' => __( 'Rotate Icon, %', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 360,
					],
					'px' => [
						'min' => 0,
						'max' => 360,
					],
					'em' => [
						'min' => 0,
						'max' => 360,
					],
					'rem' => [
						'min' => 0,
						'max' => 360,
					]
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button i, {{WRAPPER}} .gem-button-container .gem-button svg' => 'transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('styledbutton_button_icon_tab_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'button_icon_color_hv',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button:hover .gem-button-icon' => 'color:{{VALUE}};',
					'{{WRAPPER}} .gem-button-container .gem-button:hover .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_control(
			'button_icon_rotate_hv',
			[
				'label' => __( 'Rotate Icon, %', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 360,
					],
					'px' => [
						'min' => 0,
						'max' => 360,
					],
					'em' => [
						'min' => 0,
						'max' => 360,
					],
					'rem' => [
						'min' => 0,
						'max' => 360,
					]
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button:hover .gem-button-icon' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * Separator Style
	 * @access protected
	 */
	protected function separator_style( $control ) {

		$control->start_controls_section(
			'button_separator_style',
			[
				'label' => __('Separator Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_separator' => ['yes'],
				],
			]
		);

		$this->add_control(
			'button_separator_style_active',
			[
				'label' => __('Separator Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'single',
				'options' => [
					'single' => __('Single', 'thegem'),
					'square' => __('Square', 'thegem'),
					'soft-double' => __('Soft Double', 'thegem'),
					'strong-double' => __('Strong Double', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'button_separator_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%', 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button-separator-line' => 'width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'button_separator_weight_single',
			[
				'label' => __('Weight, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'button_separator_style_active' => ['single'],
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-separator .gem-button-separator-line' => 'border-top-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'button_separator_weight_soft_double',
			[
				'label' => __('Weight, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'button_separator_style_active' => ['soft-double'],
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-separator .gem-button-separator-line' => 'border-top-width:{{SIZE}}{{UNIT}}; border-bottom-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'button_separator_weight_strong_double',
			[
				'label' => __('Weight, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'button_separator_style_active' => ['strong-double'],
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-separator .gem-button-separator-line' => 'border-top-width:{{SIZE}}{{UNIT}}; border-bottom-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Height Strong Double & Soft
		$control->add_responsive_control(
			'button_separator_double_height',
			[
				'label' => __('Height, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'button_separator_style_active' =>
						[
							'strong-double',
							'soft-double',
						],
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button-separator-holder .gem-button-separator-line' => 'height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Spacing Button
		$control->add_responsive_control(
			'button_separator_spacing',
			[
				'label' => __('Spacing, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-button-container .gem-button-separator a' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);



		// Color
		$this->add_control(
			'button_color_square_border',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [],
			]
		);

		$control->end_controls_section();
	}


	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls( $control ) {

		$this->control = $control;

		/*Button Styles*/
		$this->button_styles( $control );

		/*Text Styles*/
		$this->text_styles( $control );

		/*Icon Styles*/
		$this->icon_style( $control );

		/*Separator Styles*/
		$this->separator_style( $control );

	}


	/** Get current preset
	 * @param $val
	 * @return string
	 */
	protected function get_setting_preset( $val ) {

		if ( empty($val) ) {
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

		if ( 'yes' === $settings['effects_enabled'] ) {
			thegem_lazy_loading_enqueue();
			$this->add_render_attribute( 'link', 'class', 'lazy-loading-item' );
			$this->add_render_attribute( 'link', 'data-ll-effect', 'drop-right-without-wrap' );
		}

		$preset = $this->get_setting_preset($settings['thegem_button_skin']);

		if ( empty($preset) ) return;

		$this->add_render_attribute( 'button_text', 'class', 'gem-text-button' );
		$this->add_inline_editing_attributes( 'button_text', 'none' );

		$separator_enabled = ! empty( $settings['show_separator'] ) ? true : false;

		if( $separator_enabled ) {
			$separator_style_square =  ( $settings['button_separator_style_active'] === 'square' ) ? true : false;
		}

		switch ( $settings['button_size'] ) {

			case 'small' : $line_thickness = 2; break;
			case 'medium': $line_thickness = 3; break;
			case 'large' : $line_thickness = 4; break;
			case 'giant' : $line_thickness = 6; break;
			default      : $line_thickness = 2; break;
		}

		$color_default = ( 'flat' === $settings['thegem_button_skin'] ) ? thegem_get_option('button_background_basic_color') : thegem_get_option('button_outline_border_basic_color');

		$sep_color = !empty( $settings['button_color_square_border'] ) ? $settings['button_color_square_border'] : $color_default;

		$this->add_attributes_items( $settings, $separator_enabled, $line_thickness );

		$link = $this->get_link_url( $settings );

		if ( $link ) {
			$this->add_link_attributes( 'link', $link );
		}

		$preset_path = __DIR__ . '/templates/output.php';

		if ( ! empty( $preset_path ) && file_exists( $preset_path ) ) {
			include( $preset_path );
		}
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

		if ( empty( $settings['button_link' ]['url'] ) ) {
			return false;
		}

		return [
			'url'         => $settings['button_link' ]['url'],
			'nofollow'    => $settings['button_link']['nofollow'],
			'is_external' => $settings['button_link']['is_external'],
		];
	}


	/**
	 * Add attributes in elements
	 *
	 * @param array $settings
	 * @param boolean $separator_enabled
	 * @param integer $line_thickness
	 * @return array|string
	 */
	private function add_attributes_items( $settings, $separator_enabled, $line_thickness ) {

		// Container
		$this->add_render_attribute( 'attr_container', 'class', ['gem-button-container', 'gem-widget-button'] );
		if( $separator_enabled ) {
			$this->add_render_attribute( 'attr_container', 'class', ['gem-button-position-center', 'gem-button-with-separator'] );
		} else {
			if( 'yes' === $settings['stretch_full_width'] ) {
				$this->add_render_attribute( 'attr_container', 'class', 'gem-button-position-fullwidth' );
			}
		}
		if( ! empty( $settings['button_class'] ) ) {
			$this->add_render_attribute( 'attr_container', 'class', esc_attr($settings['button_class']) );
		}
		if( 'yes' === $settings['effects_enabled'] ) {
			$this->add_render_attribute( 'attr_container', 'class', 'lazy-loading' );
		}
		if(!empty($settings['button_id'])) {
			$this->add_render_attribute( 'attr_container', 'id', esc_attr($settings['button_id']) );
		}

		// Separator
		$this->add_render_attribute( 'attr_separator', 'class', 'gem-button-separator' );
		if( ! empty( $settings['button_separator_style_active'] )) {
			$this->add_render_attribute( 'attr_separator', 'class', 'gem-button-separator-type-'.$settings['button_separator_style_active'] );
		}

		// Link
		$this->add_render_attribute( 'link', 'class', ['gem-button', 'gem-button-size-'.esc_attr( $settings['button_size'] ), 'gem-button-text-weight-'.esc_attr( $settings['button_text_weight'] )] );
		if( ( 'flat' === $settings['thegem_button_skin'] ) ) {
			$this->add_render_attribute( 'link', 'class', 'gem-button-style-flat' );
		}
		else {
			$this->add_render_attribute( 'link', 'class', 'gem-button-style-outline' );
		}
		if( ! empty($settings['button_position_icon']) && $settings['button_position_icon'] === 'right' ) {
			$this->add_render_attribute( 'link', 'class', 'gem-button-icon-position-right' );
		}
		if( ( 'outline' === $settings['thegem_button_skin'] ) ) {
			$this->add_render_attribute( 'link', 'class', 'gem-button-border-'.$line_thickness );
		}

	}

}

Plugin::instance()->widgets_manager->register_widget_type( new TheGem_Styled_Button() );