<?php
namespace TheGem_Elementor\Widgets\Pricing_Table;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Repeater;
use Elementor\Plugin;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Pricing Table.
 */
class TheGem_Pricing_Table extends Widget_Base
{
	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */
	protected $presets;

	public function __construct($data = [], $args = null)
	{
		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRICING_TABLE_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRICING_TABLE_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_PRICING_TABLE_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_PRICING_TABLE_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style(
			'thegem-pricing-table',
			THEGEM_ELEMENTOR_WIDGET_PRICING_TABLE_URL . '/assets/css/thegem-pricing-table.css',
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
		return 'thegem-pricing-table';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Pricing Table', 'thegem');
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
		return ['thegem-pricing-table'];
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
			'1' => __( 'Design 1', 'thegem' ),
			'2' => __( 'Design 2', 'thegem' ),
			'3' => __( 'Design 3', 'thegem' ),
			'4' => __( 'Design 4', 'thegem' ),
			'5' => __( 'Design 5', 'thegem' ),
			'6' => __( 'Design 6', 'thegem' ),
			'7' => __( 'Design 7', 'thegem' ),
			'8' => __( 'Design 8', 'thegem' ),
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
	protected function set_default_presets_options($index = 0) {
		return '1';
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'thegem_elementor_preset',
			[
				'label'   => __('Skin', 'thegem'),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'highlighted',
			[
				'label' => __('Highlighted', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'thegem_elementor_preset!' => ['5', '6'],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_header_settings',
			[
				'label' => __('Title', 'thegem'),
			]
		);

		$this->add_control(
			'content_header_show',
			[
				'label' => __('Show', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'content_header_title',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Title', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'content_header_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'content_header_subtitle',
			[
				'label' => __('Subtitle', 'thegem'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __('Subtitle', 'thegem'),
				'rows' => 2,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'content_header_show',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'terms' => [
										[
											'name' => 'thegem_elementor_preset',
											'operator' => '=',
											'value' => '1'
										],
										[
											'name' => ' highlighted',
											'operator' => '=',
											'value' => 'yes'
										],
									],
								],
								[
									'terms' => [
										[
											'name' => 'thegem_elementor_preset',
											'operator' => '=',
											'value' => '2'
										],
										[
											'name' => ' highlighted',
											'operator' => '=',
											'value' => 'yes'
										],
									],
								],
								[
									'terms' => [
										[
											'name' => 'thegem_elementor_preset',
											'operator' => '=',
											'value' => '5'
										],
									],
								],
								[
									'terms' => [
										[
											'name' => 'thegem_elementor_preset',
											'operator' => '=',
											'value' => '6'
										],
									],
								],
								[
									'terms' => [
										[
											'name' => 'thegem_elementor_preset',
											'operator' => '=',
											'value' => '7'
										],
										[
											'name' => ' highlighted',
											'operator' => '=',
											'value' => 'yes'
										]
									]
								]
							]
						]
					]
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_pricing_settings',
			[
				'label' => __('Pricing', 'thegem'),
			]
		);

		$this->add_control(
			'content_pricing_show',
			[
				'label' => __('Show', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'content_pricing_price',
			[
				'label' => __('Price', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('$249', 'thegem'),
				'condition' => [
					'content_pricing_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'content_pricing_period',
			[
				'label' => __('Period', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Per Month', 'thegem'),
				'condition' => [
					'content_pricing_show' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_features_settings',
			[
				'label' => __('Features', 'thegem'),
			]
		);

		$this->add_control(
			'content_features_show',
			[
				'label' => __('Show', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'feature_text',
			[
				'label' => __('Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __('Standart Feature', 'thegem'),
			]
		);

		$repeater->add_control(
			'feature_na',
			[
				'label' => __('N/A', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem')
			]
		);

		$this->add_control(
			'features_list',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'feature_text' => __('Standard Feature', 'thegem'),
						'feature_availability' => 'no',
					],
					[
						'feature_text' => __('Another Great Feature', 'thegem'),
						'feature_availability' => 'no',
					],
					[
						'feature_text' => __('Obsolete Feature', 'thegem'),
						'feature_availability' => 'yes',
					],
					[
						'feature_text' => __('Exciting Feature', 'thegem'),
						'feature_availability' => 'no',
					],
				],
				'condition' => [
					'content_features_show' => 'yes',
				],
				'title_field' => '{{{ feature_text }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_footer_settings',
			[
				'label' => __('Footer', 'thegem'),
			]
		);

		$this->add_control(
			'content_footer_show',
			[
				'label' => __('Show', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'content_footer_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Click Here', 'thegem'),
				'condition' => [
					'content_footer_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'content_footer_button_link',
			[
				'label' => __('Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('#', 'thegem'),
				'default' => [
					'url' => '#',
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'content_footer_show' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_label_settings',
			[
				'label' => __('Label', 'thegem'),
			]
		);

		$this->add_control(
			'content_label_show',
			[
				'label' => __('Show', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'content_label_text',
			[
				'label' => __('Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('NEW', 'thegem'),
				'condition' => [
					'content_label_show' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->controls($this);

	}

	/**
	 * Controls call
	 * @access public
	 */
	public function controls($control) {
		$this->control = $control;

		/*Container Styles*/
		$this->container_styles($control);

		/*Header Styles*/
		$this->header_styles($control);

		/*Header Styles*/
		$this->pricing_styles($control);

		/*Features Styles*/
		$this->features_styles($control);

		/*Footer Styles*/
		$this->footer_styles($control);

		/*Label Styles*/
		$this->label_styles($control);
	}


	/**
	 * Container Styles
	 * @access protected
	 */
	protected function container_styles($control) {
		$control->start_controls_section(
			'style_2_7_common_header',
			[
				'label' => __('Header Container', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'thegem_elementor_preset',
							'operator' => 'in',
							'value' => ['2', '7'],
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'content_header_show',
									'operator' => '=',
									'value' => 'yes'
								],
								[
									'name' => 'content_pricing_show',
									'operator' => '=',
									'value' => 'yes'
								],
							],
						],
					],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_2_header_new_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .pricing-price-row' => 'background-color: {{VALUE}};',
						],
					],
				],
				'condition' => [
					'thegem_elementor_preset' => ['2'],
				],
				'selector' => '{{WRAPPER}} .pricing-price-row',
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_7_header_new_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .pricing-price-row' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .pricing-table.pricing-table-style-7 .wrap-style' => 'fill: {{VALUE}};'
						],
					],
				],
				'condition' => [
					'thegem_elementor_preset' => ['7'],
				],
				'selector' => '{{WRAPPER}} .pricing-price-row',
			]
		);

		$control->remove_control('style_7_header_new_background_image');

		$control->add_responsive_control(
			'style_2_7_header_new_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'before',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-price-row' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_2_7_header_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .pricing-table .pricing-price-row',
			]
		);

		$control->add_responsive_control(
			'style_2_7_common_new_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-price-row' => 'margin-right: {{RIGHT}}{{UNIT}}; margin-left:{{LEFT}}{{UNIT}};margin-top: {{TOP}}{{UNIT}};margin-bottom:{{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'style_2_7_common_new_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-price-row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'style_2_7_common_align',
			[
				'label' => __('Content Align', 'thegem'),
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
				'selectors_dictionary' => [
					'left' => 'text-align: left; padding-left: 15px;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right; padding-right: 15px;',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-price-title, {{WRAPPER}} .pricing-price-subtitle, {{WRAPPER}} .pricing-price' => '{{VALUE}}',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Header Styles
	 * @access protected
	 */
	protected function header_styles($control) {
		$control->start_controls_section(
			'style_header',
			[
				'label' => __('Title', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_header_show' => 'yes'
				]
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_header_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-1 .pricing-price-title-wrapper,
				{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-price-row',
				'condition' => [
					'thegem_elementor_preset' => ['1', '6'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_3_4_8_header_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-3 .pricing-row-title,
				{{WRAPPER}} .pricing-table.pricing-table-style-4 .pricing-row-title,
				{{WRAPPER}} .pricing-table.pricing-table-style-8 .pricing-row-title',
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->remove_control('style_3_4_8_header_background_image');

		$control->add_responsive_control(
			'style_header_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'before',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-table.pricing-table-style-1 .pricing-price-title-wrapper,'.
					'{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-price-row,'.
					'{{WRAPPER}} .pricing-table:not(.pricing-table-style-1):not(.pricing-table-style-6) .pricing-row-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset!' => ['2', '5', '7'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_header_border_1',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['1'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '0',
							'left' => '1',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-1 .pricing-price-title-wrapper',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_header_border_2',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '6'],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-3 .pricing-row-title,'.
					'{{WRAPPER}} .pricing-table.pricing-table-style-4 .pricing-row-title,'.
					'{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-price-row',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_header_border_3',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['8'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '0',
							'bottom' => '2',
							'left' => '0',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-8 .pricing-row-title',
			]
		);

		$control->add_control(
			'style_header_align',
			[
				'label' => __('Content Align', 'thegem'),
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
				'default' => 'center',
				'condition' => [
					'thegem_elementor_preset' => ['1', '5', '6'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_header_title_background',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient'],
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .pricing-price-row' => 'background-color: {{VALUE}}!important; background-image:none!important',
						],
					],
				],
				'selector' => '{{WRAPPER}} .pricing-price-row',
				'condition' => [
					'thegem_elementor_preset' => ['5'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_header_title_border',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .pricing-price-row',
				'condition' => [
					'thegem_elementor_preset' => ['5'],
				]
			]
		);

		$control->add_responsive_control(
			'style_header_title_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'separator' => 'before',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-price-row' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['5'],
				]
			]
		);

		$control->add_responsive_control(
			'style_header_title_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-price-row' => 'margin-right: {{RIGHT}}{{UNIT}}; margin-left:{{LEFT}}{{UNIT}};margin-top: {{TOP}}{{UNIT}};margin-bottom:{{BOTTOM}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['5'],
				]
			]
		);

		$control->add_responsive_control(
			'style_header_title_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'default' => [
					'bottom' => '96',
					'isLinked' => false,
				],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-price-row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['5'],
				]
			]
		);

		$control->add_responsive_control(
			'style_title_5_spacing',
			[
				'label' => __('Title Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
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
					'size' => 128,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-price-title' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['5'],
				]
			]
		);		

		$control->add_responsive_control(
			'style_header_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-table.pricing-table-style-1 .pricing-price-title-wrapper,'.
					'{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-price-row,'.
					'{{WRAPPER}} .pricing-table:not(.pricing-table-style-1):not(.pricing-table-style-6) .pricing-row-title' => 'margin-right: {{RIGHT}}{{UNIT}}; margin-left:{{LEFT}}{{UNIT}};margin-top: {{TOP}}{{UNIT}};margin-bottom:{{BOTTOM}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset!' => ['2', '5', '7'],
				]
			]
		);

		$control->add_responsive_control(
			'style_header_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-table.pricing-table-style-1 .pricing-price-title-wrapper,'.
					'{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-price-row,'.
					'{{WRAPPER}} .pricing-table:not(.pricing-table-style-1):not(.pricing-table-style-6) .pricing-row-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset!' => ['2', '5', '7'],
				]
			]
		);		

		$control->add_control(
			'style_header_separator',
			[
				'label' => __('Separator', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset' => ['3'],
				]
			]
		);

		$control->add_responsive_control(
			'style_header_separator_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .pricing-row-title:after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['3'],
				]
			]
		);

		$control->add_responsive_control(
			'style_header_separator_width',
			[
				'label' => __('Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 50,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-row-title:after' => 'width: {{SIZE}}{{UNIT}}; left: calc((100% - {{SIZE}}{{UNIT}})/2);',
				],
				'condition' => [
					'thegem_elementor_preset' => ['3'],
				]
			]
		);

		$control->add_responsive_control(
			'style_header_separator_height',
			[
				'label' => __('Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-row-title:after' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['3'],
				]
			]
		);

		$control->add_responsive_control(
			'style_header_separator_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'rem' => [
						'min' => -100,
						'max' => 100,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-row-title:after' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['3'],
				]
			]
		);

		$control->add_control(
			'style_header_title',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'thegem_elementor_preset!' => ['5'],
				]
			]
		);

		$control->add_responsive_control(
			'style_title_6_spacing',
			[
				'label' => __('Title Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
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
					'size' => 132,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-price-title' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['6'],
				]
			]
		);

		$control->add_control(
			'style_header_title_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .pricing-price-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pricing_row_title' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_header_title_typo',
				'selector' => '{{WRAPPER}} .pricing-price-title, {{WRAPPER}} .pricing_row_title',
			]
		);

		$control->add_control(
			'style_header_subtitle',
			[
				'label' => __('Subtitle', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'content_header_show' => 'yes',
					'thegem_elementor_preset' => ['1', '2', '7'],
					'highlighted' => 'yes',
				],
			]
		);

		$control->add_control(
			'style_header_subtitle_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .pricing-price-subtitle' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_header_show' => 'yes',
					'thegem_elementor_preset' => ['1', '2', '7'],
					'highlighted' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_header_subtitle_typo',
				'selector' => '{{WRAPPER}} .pricing-price-subtitle',
				'condition' => [
					'content_header_show' => 'yes',
					'thegem_elementor_preset' => ['1', '2', '7'],
					'highlighted' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'style_header_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'before',
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 150,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'rem' => [
						'min' => -100,
						'max' => 100,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-price' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['2', '7'],
				]
			]
		);

		$control->end_controls_section();

		$control->start_controls_section(
			'style_subtitle',
			[
				'label' => __('Subtitle', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_header_show' => 'yes',
					'thegem_elementor_preset' => ['5', '6'],
				],				
			]
		);

		$control->add_responsive_control(
			'style_subtitle_align_1',
			[
				'label' => __('Content Align', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'center',
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
				'selectors_dictionary' => [
					'left' => 'text-align: left; padding-left: 20px;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right; padding-right: 20px;',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-row.pricing-row-title' => '{{VALUE}}',
				],
				'condition' => [
					'thegem_elementor_preset' => '5',
				],
			]
		);

		$control->add_responsive_control(
			'style_subtitle_align_2',
			[
				'label' => __('Content Align', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'left',
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
				'selectors_dictionary' => [
					'left' => 'text-align: left; padding-left: 43px;',
					'center' => 'text-align: center; padding-left: 0; padding-right: 0;',
					'right' => 'text-align: right; padding-right: 43px;',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-price-subtitle' => '{{VALUE}}',
				],
				'condition' => [
					'thegem_elementor_preset' => '6',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_header_subtitle_background',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient'],
				'condition' => [
					'thegem_elementor_preset' => '5',
				],
				'selector' => '{{WRAPPER}} .pricing-row.pricing-row-title',
			]
		);

		$control->remove_control('style_header_subtitle_background_image');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_header_subtitle_border',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} {{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-row.pricing-row-title,
				.pricing-table.pricing-table-style-6 .pricing-price-subtitle',
			]
		);

		$control->add_responsive_control(
			'style_header_subtitle_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'separator' => 'before',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} {{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-row.pricing-row-title,
					.pricing-table.pricing-table-style-6 .pricing-price-subtitle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'style_header_subtitle_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} {{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-row.pricing-row-title,
					.pricing-table.pricing-table-style-6 .pricing-price-subtitle' => 'margin-right: {{RIGHT}}{{UNIT}}; margin-left:{{LEFT}}{{UNIT}};margin-top: {{TOP}}{{UNIT}};margin-bottom:{{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'style_header_subtitle_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} {{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-row.pricing-row-title,
					.pricing-table.pricing-table-style-6 .pricing-price-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'style_header_subtitle_new_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} {{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-row.pricing-row-title,
					.pricing-table.pricing-table-style-6 .pricing-price-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_header_subtitle_new_typo',
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-row.pricing-row-title,
				.pricing-table.pricing-table-style-6 .pricing-price-subtitle',
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Pricing Styles
	 * @access protected
	 */
	protected function pricing_styles($control) {
		$control->start_controls_section(
			'style_pricing',
			[
				'label' => __('Pricing', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_pricing_show' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_1_pricing_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pricing-price',
				'condition' => [
					'thegem_elementor_preset' => ['1'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_pricing_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pricing-price',
				'condition' => [
					'thegem_elementor_preset' => ['5', '6'],
				]
			]
		);

		$control->remove_control('style_pricing_background_image');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_pricing_border_1',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['1'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '1',
							'bottom' => '0',
							'left' => '1',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table .pricing-price',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_pricing_border_2',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['5', '6'],
				],
				'selector' => '{{WRAPPER}} .pricing-table .pricing-price',
			]
		);	

		$control->add_control(
			'style_pricing_align',
			[
				'label' => __('Pricing Align', 'thegem'),
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
				'default' => 'center',
				'condition' => [
					'thegem_elementor_preset' => ['1', '5', '6'],
				]
			]
		);

		$control->add_responsive_control(
			'style_pricing_margin',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-price-wrapper' => 'margin-right: {{RIGHT}}{{UNIT}}; margin-left:{{LEFT}}{{UNIT}};margin-top: {{TOP}}{{UNIT}};margin-bottom:{{BOTTOM}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['1'],
				]
			]
		);

		$control->add_responsive_control(
			'style_pricing_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['1'],
				]
			]
		);

		$control->add_control(
			'style_pricing_inner',
			[
				'label' => __('Inner Container', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_pricing_background_inner',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pricing-price',
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->remove_control('style_pricing_background_inner_image');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_pricing_border_inner_1',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .pricing-price',
				'condition' => [
					'thegem_elementor_preset' => ['3'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_pricing_border_inner_2',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '10',
							'right' => '10',
							'bottom' => '10',
							'left' => '10',
							'isLinked' => true,
						],
					],
					'color' => [
						'default' => '#1f5a6c',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-price',
				'condition' => [
					'thegem_elementor_preset' => ['4'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_pricing_border_inner_3',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '3',
							'right' => '3',
							'bottom' => '3',
							'left' => '3',
							'isLinked' => false,
						],
					],
				],
				'selector' => '{{WRAPPER}} .pricing-price',
				'condition' => [
					'thegem_elementor_preset' => ['8'],
				]
			]
		);

		$control->add_responsive_control(
			'style_pricing_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
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
				'default' => [
					'size' => 180,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-price' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pricing-table.pricing-table-style-3 .pricing-price-wrapper,
					{{WRAPPER}} .pricing-table.pricing-table-style-8 .pricing-price-wrapper' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2) ;',
					'{{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-price-wrapper,
					{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-price-wrapper' => 'margin-top: calc(-{{SIZE}}{{UNIT}} / 2);margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
				],
				'condition' => [
					'thegem_elementor_preset!' => ['1', '2', '7'],
				]
			]
		);

		$control->add_responsive_control(
			'style_pricing_radius_inner',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-price' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'style_pricing_inner_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .pricing-price',
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->add_control(
			'style_pricing_outer',
			[
				'label' => __('Outer Container', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_pricing_background_outer',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-3 .pricing-price-row,
				{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-4 .pricing-price-wrapper,
				{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-8 .pricing-price-row',
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->remove_control('style_pricing_background_outer_image');

		$control->add_responsive_control(
			'style_pricing_radius_outer',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-table.pricing-table-style-3 .pricing-price-row,
					{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-4 .pricing-price-wrapper,
					{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-8 .pricing-price-row' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_pricing_border_outer',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-3 .pricing-price-row,
				{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-4 .pricing-price-wrapper,
				{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-8 .pricing-price-row',
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->add_responsive_control(
			'style_pricing_margin_outer',
			[
				'label' => __('Margin', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-table.pricing-table-style-3 .pricing-price-row,
					{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-4 .pricing-price-wrapper,
					{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-8 .pricing-price-row' => 'margin-right: {{RIGHT}}{{UNIT}}; margin-left:{{LEFT}}{{UNIT}};margin-top: {{TOP}}{{UNIT}};margin-bottom:{{BOTTOM}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->add_responsive_control(
			'style_pricing_padding_outer',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-table.pricing-table-style-3 .pricing-price-row,
					{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-4 .pricing-price-wrapper,
					{{WRAPPER}} .pricing-table.pricing-table.pricing-table-style-8 .pricing-price-row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '8'],
				]
			]
		);

		$control->add_control(
			'style_pricing_price',
			[
				'label' => __('Price', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$control->add_control(
			'style_pricing_price_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .pricing-cost' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_pricing_price_typo',
				'selector' => '{{WRAPPER}} .pricing-cost',
			]
		);

		$control->add_control(
			'style_pricing_period',
			[
				'label' => __('Period', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'style_pricing_period_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .time' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_pricing_period_typo',
				'selector' => '{{WRAPPER}} .time',
			]
		);

		$control->end_controls_section();

	}


	/**
	 * Features Styles
	 * @access protected
	 */
	protected function features_styles($control) {
		$control->start_controls_section(
			'style_features',
			[
				'label' => __('Features', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'style_features_spacing',
			[
				'label' => __('Top Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .pricing-column' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'thegem_elementor_preset' => ['7'],
				]
			]
		);

		$control->add_responsive_control(
			'style_features_background_odd',
			[
				'label' => __('First Row Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} figure.pricing-row:nth-child(odd)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'style_features_background_even',
			[
				'label' => __('Second Row Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} figure.pricing-row:nth-child(even)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_features_border_1',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['1'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-1 figure.pricing-row',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_features_border_2',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['2'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '2',
							'bottom' => '0',
							'left' => '2',
							'isLinked' => false,
						],
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-2 figure.pricing-row',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_features_border_3',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['3', '4', '5'],
				],
				'selector' => '{{WRAPPER}} .pricing-table figure.pricing-row',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_features_border_4',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['6'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-6 figure.pricing-row',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_features_border_5',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['7'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '0',
							'bottom' => '1',
							'left' => '0',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-7 figure.pricing-row',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_features_border_6',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['8'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '0',
							'bottom' => '2',
							'left' => '0',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-8 figure.pricing-row',
			]
		);

		$control->add_responsive_control(
			'style_features_align',
			[
				'label' => __('Content Align', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'separator' => 'before',
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
				'default' => 'center',
					'condition' => [
					'thegem_elementor_preset!' => ['3', '4', '8'],
				]
		]
		);

		$control->add_responsive_control(
			'style_features_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} figure.pricing-row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'style_features_heading',
			[
				'label' => __('Feature', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'style_feature_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$control->add_responsive_control(
			'style_feature_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} figure.pricing-row i' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} figure.pricing-row svg' => 'fill: {{VALUE}}!important;',
				]
			]
		);

		$control->add_control(
			'style_features_text',
			[
				'label' => __('Text', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'style_features_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} figure.pricing-row' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_features_text_typo',
				'selector' => '{{WRAPPER}} figure.pricing-row',
			]
		);

		$control->add_control(
			'style_features_textna',
			[
				'label' => __('Text N/A', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'style_features_textna_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .strike' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_features_textna_typo',
				'selector' => '{{WRAPPER}} .strike',
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Label Styles
	 * @access protected
	 */
	protected function label_styles($control) {
		$control->start_controls_section(
			'style_label',
			[
				'label' => __('Label', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_label_show' => 'yes'
				]
			]
		);

		$control->add_responsive_control(
			'style_label_size',
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
				'default' => [
					'size' => 50,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-table.pricing-table-style-1 .pricing-column-top-choice,
					{{WRAPPER}} .pricing-table.pricing-table-style-2 .pricing-column-top-choice,
					{{WRAPPER}} .pricing-table.pricing-table-style-3 .pricing-column-top-choice,
					{{WRAPPER}} .pricing-table.pricing-table-style-4 .pricing-column-top-choice' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2);',	
					'{{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-column-top-choice,
					{{WRAPPER}} .pricing-table.pricing-table-style-7 .pricing-column-top-choice' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};-webkit-mask-size: {{SIZE}}{{UNIT}};mask-size: {{SIZE}}{{UNIT}};margin-top: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/4);margin-right: calc(-{{SIZE}}{{UNIT}}/4);',
					'{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-column-top-choice' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};-webkit-mask-size: {{SIZE}}{{UNIT}};mask-size: {{SIZE}}{{UNIT}};margin-top: calc(-{{SIZE}}{{UNIT}}/2);margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2);',						
					'{{WRAPPER}} .pricing-table.pricing-table-style-8 .pricing-column-top-choice' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',					
				]
			]
		);

		$control->add_control(
			'style_label_text_rotate',
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
					'{{WRAPPER}} .pricing-table:not(.pricing-table-style-5):not(.pricing-table-style-6):not(.pricing-table-style-7) .pricing-column-top-choice-text,
					{{WRAPPER}} .pricing-table.pricing-table-style-5 .pricing-column-top-choice,
					{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-column-top-choice,
					{{WRAPPER}} .pricing-table.pricing-table-style-7 .pricing-column-top-choice' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
			]
		);

		$control->add_responsive_control(
			'style_label_background',
			[
				'label' => __('Background', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .pricing-column-top-choice' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'style_label_with_price_align',
			[
				'label' => __('Alignment', 'thegem'),
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
				'selectors_dictionary' => [
					'right' => 'left: calc(100% - 15px); right: unset; margin-left:0!important;',
					'left' => 'right: calc(100% - 15px); left: unset; margin-right:0!important;',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-column-top-choice' => '{{VALUE}}',
				],
				'default' => 'right',
				'condition' => [
					'content_pricing_show' => 'yes',
					'thegem_elementor_preset' => ['6'],
				],
			]
		);

		$control->add_responsive_control(
			'style_label_noprice_align',
			[
				'label' => __('Alignment', 'thegem'),
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
				'selectors_dictionary' => [
					'right' => 'left: unset; right: 43px; top:0;margin-right: 0;',
					'center' => 'left: 50%; right: unset; top:0;',
					'left' => 'right: unset; left: 43px; top:0;margin-left: 0;',
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-column-top-choice' => '{{VALUE}}',
				],
				'default' => 'right',
				'condition' => [
					'content_pricing_show!' => 'yes',
					'thegem_elementor_preset' => ['6'],
				],
			]
		);

		$control->add_responsive_control(
			'style_label_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-column-top-choice' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset!' => ['5', '6', '7', '8'],
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_label_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .pricing-column-top-choice',
				'condition' => [
					'thegem_elementor_preset!' => ['5', '6', '7', '8'],
				]
			]
		);

		$control->add_control(
			'style_label_3d_align',
			[
				'label' => __('Alignment', 'thegem'),
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
				'default' => 'left',
				'condition' => [
					'thegem_elementor_preset' => ['1', '2', '3', '7'],
				]
			]
		);

		$control->add_control(
			'style_label_2d_align',
			[
				'label' => __('Alignment', 'thegem'),
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
				'condition' => [
					'thegem_elementor_preset' => ['5', '8'],
				]
			]
		);

		$control->add_control(
			'style_label_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .pricing-column-top-choice-text' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_label_text_typo',
				'selector' => '{{WRAPPER}} .pricing-column-top-choice-text',
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Footer Styles
	 * @access protected
	 */
	protected function footer_styles($control) {
		$control->start_controls_section(
			'style_footer',
			[
				'label' => __('Footer', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_footer_show' => 'yes'
				]
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'style_footer_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pricing-footer',
			]
		);

		$control->remove_control('style_footer_background_image');

		$control->add_responsive_control(
			'style_footer_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				// 'separator' => 'after',
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-footer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_footer_border_1',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['1'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-1 .pricing-footer',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_footer_border_2',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['2'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '2',
							'bottom' => '2',
							'left' => '2',
							'isLinked' => false,
						],
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-2 .pricing-footer',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_footer_border_3',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset!' => ['1', '2', '6'],
				],
				'selector' => '{{WRAPPER}} .pricing-table .pricing-footer',
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_footer_border_4',
				'label' => __('Border', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => ['6'],
				],
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '0',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#dfe5e8',
					],
				],
				'selector' => '{{WRAPPER}} .pricing-table.pricing-table-style-6 .pricing-footer',
			]
		);

		$control->add_responsive_control(
			'style_footer_align',
			[
				'label' => __('Content Align', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'separator' => 'before',
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
				'default' => 'center',
				'condition' => [
					'thegem_elementor_preset!' => ['3', '4'],
				]
			]
		);

		$control->add_responsive_control(
			'style_footer_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .pricing-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'style_footer_button',
			[
				'label' => __('Button', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'style_footer_button_type',
			[
				'label' => __('Button Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					' gem-button-style-flat ' => __('Flat', 'thegem'),
					' gem-button-style-outline ' => __('Outline', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'style_footer_button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					' gem-button-size-small ' => __('Small', 'thegem'),
					' gem-button-size-medium ' => __('Medium', 'thegem'),
					' gem-button-size-large ' => __('Large', 'thegem'),
				],
			]
		);

		$control->start_controls_tabs('style_footer_tabs');
		$control->start_controls_tab('style_footer_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_control(
			'style_footer_normal_textcolor',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button' => 'color: {{VALUE}}!important;',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_footer_normal_typo',
				'selector' => '{{WRAPPER}} .gem-button',
			]
		);

		$control->add_responsive_control(
			'style_footer_normal_bgcolor',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button-style-flat' => 'background-color: {{VALUE}}!important;',
					'{{WRAPPER}} .gem-button-style-outline' => 'border-color: {{VALUE}}!important;',
				]
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab('style_footer_tabs_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_control(
			'style_footer_hover_textcolor',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button:hover' => 'color: {{VALUE}}!important;',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'style_footer_hover_typo',
				'selector' => '{{WRAPPER}} .gem-button:hover',
			]
		);

		$control->add_responsive_control(
			'style_footer_hover_bgcolor',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-button:hover' => 'background-color: {{VALUE}}!important;',
				]
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'style_footer_button_border',
				'label' => __('Border', 'thegem'),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .gem-button',
			]
		);

		$control->add_responsive_control(
			'style_footer_button_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->add_responsive_control(
			'style_footer_button_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$control->add_control(
			'style_footer_button_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'gem-elegant arrow-triangle-right',
					'library' => 'thegem-elegant',
				],
				'condition' => [
					'thegem_elementor_preset' => ['1', '2', '7'],
				]
			]
		);

		$control->add_control(
			'style_footer_button_icon_noicon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'thegem_elementor_preset!' => ['1', '2', '7'],
				]
			]
		);

		$control->add_responsive_control(
			'style_footer_button_iconalign',
			[
				'label' => __('Icon Alignment', 'thegem'),
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
				'selectors_dictionary' => [
					'left' => 'left',
					'right' => 'right',
				],
				'default' => 'left'
			]
		);

		$control->add_responsive_control(
			'style_footer_button_iconspacing',
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
					'{{WRAPPER}} .gem-button-icon-position-right i' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-button-icon-position-right svg' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-button-icon-position-left i' => 'padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-button-icon-position-left svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				]
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
	public function render()
	{
		$settings = $this->get_settings_for_display();

		$preset = $settings['thegem_elementor_preset'];
		if (empty($preset)) return;

		$this->add_render_attribute( 'content_pricing_price', 'class', 'pricing-cost' );
		$this->add_render_attribute( 'content_pricing_period', 'class', 'time' );
		$this->add_render_attribute( 'content_label_text', 'class', 'pricing-column-top-choice-text' );

		$this->add_inline_editing_attributes( 'content_header_title', 'none' );
		$this->add_inline_editing_attributes( 'content_header_subtitle', 'none' );
		$this->add_inline_editing_attributes( 'content_pricing_price', 'none' );
		$this->add_inline_editing_attributes( 'content_pricing_period', 'none' );
		$this->add_inline_editing_attributes( 'content_footer_button_text', 'none' );
		$this->add_inline_editing_attributes( 'content_label_text', 'none' );

		$preset_path = __DIR__ . '/templates/preset_html' . $preset . '.php';

		if (!empty($preset_path) && file_exists($preset_path)) {
			include($preset_path);
		}
	}

	public function get_preset_data() {

		return array(
			'1' => array(
				'style_label_3d_align' => 'left',
				'style_pricing_align' => 'center',	
				'style_label_size' => ['size' => 50,'unit' => 'px'],
			),
			'2' => array(
				'style_label_3d_align' => 'center',
				'style_label_size' => ['size' => 62,'unit' => 'px'],
			),
			'3' => array(
				'style_label_3d_align' => 'left',				
				'style_pricing_size' => ['size' => 180,'unit' => 'px'],
				'style_label_size' => ['size' => 62,'unit' => 'px'],
			),
			'4' => array(
				'style_pricing_size' => ['size' => 190,'unit' => 'px'],
				'style_label_size' => ['size' => 80,'unit' => 'px'],
				'style_feature_icon' => ['value' => 'gem-elegant icon-check','library' => 'thegem-elegant'],
			),
			'5' => array(
				'style_label_2d_align' => 'left',
				'style_pricing_align' => 'center',
				'style_pricing_size' => ['size' => 120,'unit' => 'px'],
				'style_label_size' => ['size' => 66,'unit' => 'px'],
			),
			'6' => array(
				'style_header_align' => 'left',
				'style_pricing_align' => 'left',
				'style_pricing_size' => ['size' => 120,'unit' => 'px'],
				'style_label_size' => ['size' => 66,'unit' => 'px'],
				'style_features_align' => 'left',
				'style_footer_align' => 'left',
				'style_feature_icon' => ['value' => 'gem-elegant icon-box-checked','library' => 'thegem-elegant'],
			),
			'7' => array(
				'style_label_3d_align' => 'left',
				'style_label_size' => ['size' => 66,'unit' => 'px'],
			),
			'8' => array(
				'style_label_2d_align' => 'left',
				'style_pricing_size' => ['size' => 160,'unit' => 'px'],
				'style_label_size' => ['size' => 50,'unit' => 'px'],
			),
		);
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new TheGem_Pricing_Table());
