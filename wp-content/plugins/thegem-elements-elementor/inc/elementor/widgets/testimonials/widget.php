<?php
namespace TheGem_Elementor\Widgets\Testimonials;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;

use WP_Query;


if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Elementor widget for Testimonials.
 */
class TheGem_Testimonials extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */

	public $preset_elements_select;

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if( ! defined( 'THEGEM_ELEMENTOR_WIDGET_TESTIMONIALS_DIR' ) ){
			define( 'THEGEM_ELEMENTOR_WIDGET_TESTIMONIALS_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_TESTIMONIALS_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_TESTIMONIALS_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}

		$this->preset_elements_select = [
			'img' => __( 'Image', 'thegem' ),
			'name' => __( 'Name', 'thegem' ),
			'position' => __( 'Position', 'thegem' ),
			'company' => __( 'Company', 'thegem' ),
			'quote' => __( 'Quote', 'thegem' ),
		];

		wp_register_style( 'thegem-testimonials', THEGEM_ELEMENTOR_WIDGET_TESTIMONIALS_URL . '/assets/css/thegem-testimonials.css', array(), null );
		wp_register_script( 'thegem-testimonials-carousel', THEGEM_ELEMENTOR_WIDGET_TESTIMONIALS_URL . '/assets/js/testimonials-carousel.js', array( 'jquery', 'jquery-carouFredSel' ), null, true );
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-testimonials';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Testimonials', 'thegem' );
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
		return [ 'thegem-testimonials' ];
	}

	public function get_script_depends() {
		return [ 'thegem-testimonials-carousel' ]; 
	}

	/*Show reload button*/
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
			return $this->get_settings()[ $control_id ];
		} else {
			return $this->get_settings()[ $control_id ][ $control_sub ];
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
			'style1' => __( 'Classic', 'thegem' ),
			'style2' => __( 'Bubble', 'thegem' ),
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
		return 'style1';
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
				'label' => __( 'Layout', 'thegem' ),
			]
		);

			$this->add_control(
				'thegem_elementor_preset',
				[
					'label'   => __( 'Skin', 'thegem' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $this->get_presets_options(),
					'default' => $this->set_default_presets_options(),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'fullwidth',
				[
					'label' => __( 'Stretch to Full Width', 'thegem' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'thegem' ),
					'label_off' => __( 'No', 'thegem' ),
					'return_value' => 'yes',
					'default' => 'no',
				]
			);

		$this->end_controls_section();

		$this->add_styles_controls( $this );

	}

	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls( $control ) {

		$this->control = $control;

		/*Content Settings*/
		$this->content_settings( $control );

		/*Options Settings*/
		$this->options_settings( $control );

		/*Container Styles*/
		$this->container_styles( $control );

		/*Image  Styles*/
		$this->image_styles( $control );

		/*Name  Styles*/
		$this->name_styles( $control );

		/*Position  Styles*/
		$this->position_styles( $control );

		/*Company  Styles*/
		$this->company_styles( $control );

		/*Description  Styles*/
		$this->description_styles( $control );

		/*Quote  Styles*/
		$this->quote_styles( $control );

		/*Arrows  Styles*/
		$this->arrows_styles( $control );

	}


	/**
	 * Make options select testimonials
	 * @access protected
	 * @return array
	 */
	protected function select_testimonials() {
		$out   = [ 'all' => __( 'All', 'thegem' ) ];
		$terms = get_terms( [
			'taxonomy' => 'thegem_testimonials_sets',
			'hide_empty' => true,
		] );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return $out;
		}

		foreach ( (array) $terms as $term ) {
			if ( ! empty( $term->name ) ) {
				$out[ $term->slug ] = $term->name;
			}
		}

		return $out;
	}

	/**
	 * Content Settings
	 * @access protected
	 */
	protected function content_settings( $control ) {


		$control->start_controls_section(
			'content_settings',
			[
				'label' => __( 'Content', 'thegem' ),
			]
		);

		$control->add_control(
			'content_testimonials_cat',
			[
				'label' => __( 'Select Testimonials', 'thegem' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => 'all',
				'options' => $this->select_testimonials(),
				'frontend_available' => true,
				'label_block' => true,
			]
		);

		if( !empty( $control->preset_elements_select) ){
			foreach ( (array) $control->preset_elements_select as $ekey=>$elem ) {

					$def = 'yes';

					$control->add_control(
								'content_elems_'. $ekey, [
									'label' => $elem,
									'default' => $def,
									'type' => Controls_Manager::SWITCHER,
									'label_on' => __( 'Show', 'thegem' ),
									'label_off' => __( 'Hide', 'thegem' ),
							'frontend_available' => true,
						]
					);

			}
		}

		$control->end_controls_section();
	}


	/**
	 * Options Settings
	 * @access protected
	 */
	protected function options_settings( $control ) {


		$control->start_controls_section(
			'options_settings',
			[
				'label' => __( 'Options', 'thegem' ),
			]
		);

		if( !empty( $control->preset_options_select) ){
			foreach ( (array) $control->preset_options_select as $ekey=>$elem ) {
					$def = 'yes';

			$control->add_control(
						'content_elems_'. $ekey, [
							'label' => $elem,
							'default' => $def,
							'type' => Controls_Manager::SWITCHER,
							'label_on' => __( 'On', 'thegem' ),
							'label_off' => __( 'Off', 'thegem' ),
					'frontend_available' => true,
				]
			);

			}
		}
		$control->add_control(
			'show_arrows',
			[
				'label' => __( 'Show Arrows', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'thegem' ),
				'label_off' => __( 'No', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$control->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$control->add_control(
			'autoscroll',
			[
				'label' => __( 'Autoplay Speed', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Container Styles
	 * @access protected
	 */
	protected function container_styles( $control ) {


		$control->start_controls_section(
			'container',
			[
				'label' => __( 'Container Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->start_controls_tabs( 'container_tabs' );
			$control->start_controls_tab( 'container_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

			$control->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'container_bgcolor',
					'label' => __( 'Background Type', 'thegem' ),
					'types' => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .gem-testimonials',
					'condition' => [
						'thegem_elementor_preset' => 'style1',
					],
				]
			);

			$control->add_responsive_control(
				'container_bgcolor_2',
				[
					'label' => __( 'Background Color', 'thegem' ),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .gem-testimonials' => 'background-color: {{VALUE}} !important;',
						'{{WRAPPER}} .gem-testimonials .testimonials_svg svg' => 'fill: {{VALUE}} !important;',
					],
					'condition' => [
						'thegem_elementor_preset' => 'style2',
					],
				]
			);

			$control->add_responsive_control(
				'container_radius',
				[
					'label' => __( 'Radius', 'thegem' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'rem', 'em' ],
					'separator' => 'after',
					'label_block' => true,
					'selectors' => [
						'{{WRAPPER}} .gem-testimonials' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$control->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'container_border',
					'label' => __( 'Border', 'thegem' ),
					'selector' => '{{WRAPPER}} .gem-testimonials',

				]
			);

			$control->add_responsive_control(
				'container_align',
				[
					'label' => __( 'Content Align', 'thegem' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'separator' => 'before',
					'options' => [
						'left' => [
							'title' => __( 'Left', 'thegem' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'elementor' ),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'thegem' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'toggle' => false,
					'selectors_dictionary' => [
						'left' => 'text-align: left;padding-left: 80px;',
						'center' => 'text-align: center;',
						'right' => 'text-align: right;padding-right: 80px',

					],
					'selectors' => [
						'{{WRAPPER}} .gem-testimonial-name,
						{{WRAPPER}} .gem-testimonial-company,
						{{WRAPPER}} .gem-testimonial-position,
						{{WRAPPER}} .gem-testimonial-text' => '{{VALUE}}',
					],
					'condition' => [
						'thegem_elementor_preset' => 'style1',
					],
				]
			);


			$control->add_responsive_control(
				'bubbleposition',
				[
					'label' => __( 'Bubble Position', 'thegem' ),
					'type' => Controls_Manager::CHOOSE,
					//'default' => 'center',
					'label_block' => false,
					'separator' => 'before',
					'options' => [
						'left' => [
							'title' => __( 'Left', 'thegem' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'elementor' ),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'thegem' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'toggle' => false,
					'selectors_dictionary' => [
						'left' => 'margin-left: 105px;',
						'center' => 'margin-left: 46%;',
						'right' => 'margin-left: 80%;',

					],
					'selectors' => [
						'{{WRAPPER}}  .gem-testimonials .testimonials_svg' => '{{VALUE}}',
					],
					'condition' => [
						'thegem_elementor_preset' => 'style2',
					],
				]
			);

			$control->add_responsive_control(
				'container_padding',
				[
					'label' => __( 'Padding', 'thegem' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'rem', 'em' ],
					'label_block' => true,
					'selectors' => [
						'{{WRAPPER}} .gem-testimonial-item .gem-testimonial-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$control->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'container_shadow',
					'label' => __( 'Shadow', 'thegem' ),
					'selector' => '{{WRAPPER}} .gem-testimonials',
				]
			);

		$control->end_controls_tab();

		$control->start_controls_tab( 'container_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

			$control->add_responsive_control(
				'container_bgcolor_hv',
				[
					'label' => __( 'Background Color', 'thegem' ),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .gem-testimonials:hover' => 'background-color: {{VALUE}} !important;',
						'{{WRAPPER}} .gem-testimonials:hover .testimonials_svg svg' => 'fill: {{VALUE}} !important;',
					],
				]
			);

			$control->add_responsive_control(
				'container_brdcolor_hv',
				[
					'label' => __( 'Border Container Color', 'thegem' ),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .gem-testimonials:hover' => 'border-color: {{VALUE}} !important;',
					],
				]
			);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow_hv',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-testimonials:hover',
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
			'img',
			[
				'label' => __( 'Image Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);


		$control->start_controls_tabs( 'img_tabs' );
		$control->start_controls_tab( 'img_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );


		$control->add_responsive_control(
			'thegem_elementor_image_size',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 240,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 128,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-image' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'img_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-image img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'img_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'default' => [
					'top' => 60,
					'right' => 60,
					'bottom' => 60,
					'left' => 60, 
					'unit' => '%', 
					'isLinked' => true
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-image, {{WRAPPER}} .gem-testimonial-image img, {{WRAPPER}} .gem-testimonial-image > span::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'img_border',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-testimonial-image',
			]
		);

		$control->add_responsive_control(
			'img_align',
			[
				'label' => __( 'Alignment', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'separator' => 'before',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'thegem' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'thegem' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'margin: 0 0 0 80px;',
					'center' => 'margin: 0 auto;',
					'right' => 'margin: 0 80px 0 auto;',

				],
				'selectors' => [
					'{{WRAPPER}}  .gem-testimonial-image' => '{{VALUE}}',
				],
				'condition' => [
					'thegem_elementor_preset' => 'style1',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'img_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_responsive_control(
			'img_bgcolor',
			[
				'label' => __( 'Overlay Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-image > span::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'img_border_hover',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-testimonial-image:hover',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-testimonial-image:hover',
			]
		);


		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();

	}

	/**
	 * Name Styles
	 * @access protected
	 */
	protected function name_styles( $control ) {

		$control->start_controls_section(
			'name',
			[
				'label' => __( 'Name Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->add_control(
			'name_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-name, {{WRAPPER}} .gem-testimonial-name span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'name_typ',
				'selector' => '{{WRAPPER}} .gem-testimonial-name, {{WRAPPER}} .gem-testimonial-name span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'name_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'name_align',
			[
				'label' => __( 'Align', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'thegem' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'thegem' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'text-align: left;padding-left: 80px;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;padding-right: 80px;',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-name' => '{{VALUE}}',
				],
			]
		);


		$control->end_controls_section();

	}

	/**
	 * Company Styles
	 * @access protected
	 */
	protected function position_styles( $control ) {

		$control->start_controls_section(
			'posit',
			[
				'label' => __( 'Position Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->add_control(
			'posit_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-position' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'posit_typ',
				'selector' => '{{WRAPPER}} .gem-testimonial-position',
				'scheme' => Schemes\Typography::TYPOGRAPHY_2,
			]
		);

		$control->add_responsive_control(
			'posit_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-position' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'posit_align',
			[
				'label' => __( 'Align', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'thegem' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'thegem' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'text-align: left;padding-left: 80px;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;padding-right: 80px;',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-position' => '{{VALUE}}',
				],
			]
		);


		$control->end_controls_section();

	}
	/**
	 * Position Styles
	 * @access protected
	 */
	protected function company_styles( $control ) {

		$control->start_controls_section(
			'company',
			[
				'label' => __( 'Company Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->add_control(
			'company_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-company' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'company_typ',
				'selector' => '{{WRAPPER}} .gem-testimonial-company',
				'scheme' => Schemes\Typography::TYPOGRAPHY_2,
			]
		);

		$control->add_responsive_control(
			'company_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-company' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'company_align',
			[
				'label' => __( 'Align', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'thegem' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'thegem' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'text-align: left;padding-left: 80px;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;padding-right: 80px;',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-company' => '{{VALUE}}',
				],
			]
		);


		$control->end_controls_section();

	}

	/**
	 * Description Styles
	 * @access protected
	 */
	protected function description_styles( $control ) {

		$control->start_controls_section(
			'descr',
			[
				'label' => __( 'Description Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->start_controls_tabs( 'descr_tabs' );
		$control->start_controls_tab( 'descr_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );
		
		$control->add_control(
			'descr_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#5f727f',
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-text p, {{WRAPPER}} .gem-testimonial-text div' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'descr_typ',
				'selector' => '{{WRAPPER}} .gem-testimonial-text p, {{WRAPPER}} .gem-testimonial-text div',
				'scheme' => Schemes\Typography::TYPOGRAPHY_2,
				'fields_options' => [
					'font_weight' => [
						'default' => '300',
					],
					'font_family' => [
						'default' => 'Source Sans Pro',
					],
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 24
						]
					],
				],
			]
		);

		$control->add_responsive_control(
			'descr_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-text p, {{WRAPPER}} .gem-testimonial-text div' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'descr_align',
			[
				'label' => __( 'Align', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'thegem' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'thegem' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'text-align: left;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;',

				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-text p, {{WRAPPER}} .gem-testimonial-text div' => '{{VALUE}}',
				],
			]
		);
		$control->end_controls_tab();

		$control->start_controls_tab( '__descr_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'descr_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials:hover .gem-testimonial-text p, {{WRAPPER}} .gem-testimonials:hover .gem-testimonial-text div' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'descr_typ_hv',
				'selector' => '{{WRAPPER}} .gem-testimonials:hover .gem-testimonial-text p, {{WRAPPER}} .gem-testimonials:hover .gem-testimonial-text div',
				'scheme' => Schemes\Typography::TYPOGRAPHY_2,
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();

	}

	/**
	 * Quote Styles
	 * @access protected
	 */
	protected function quote_styles( $control ) {

		$control->start_controls_section(
			'quote',
			[
				'label' => __( 'Quote Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->add_control(
			'quote_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-wrapper::after, {{WRAPPER}} .gem-testimonial-wrapper p::after' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'quote_size',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
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
					'size' => 60,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-testimonial-wrapper p::after, {{WRAPPER}} .gem-testimonials .gem-testimonial-wrapper::after' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'quote_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-testimonial-wrapper p::after, {{WRAPPER}} .gem-testimonials .gem-testimonial-wrapper::after' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => 'style1',
				],
			]
		);

		$control->add_responsive_control(
			'quote_top_spacing',
			[
				'label' => __( 'Top Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-testimonial-wrapper p::after' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => 'style2',
				],
			]
		);

		$control->add_responsive_control(
			'quote_left_spacing',
			[
				'label' => __( 'Left Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-testimonial-wrapper p::after' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => 'style2',
				],
			]
		);

		$control->add_responsive_control(
			'quote_align',
			[
				'label' => __( 'Alignment', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'thegem' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'thegem' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'padding-left: 90px;text-align: left;',
					'center' => 'text-align: center;',
					'right' => 'padding-right: 90px;text-align: right;',

				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonial-wrapper::after' => '{{VALUE}}',
				],
				'condition' => [
					'thegem_elementor_preset' => 'style1',
				],
			]
		);


		$control->end_controls_section();

	}

	/**
	 * Arrows Styles
	 * @access protected
	 */
	protected function arrows_styles( $control ) {

		$control->start_controls_section(
			'arrows',
			[
				'label' => __( 'Arrows Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_arrows' => 'yes',
				],
			]

		);

		$control->add_responsive_control(
			'arrow_size',
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
					'{{WRAPPER}} .gem-testimonials .gem-prev, {{WRAPPER}} .gem-testimonials .gem-next' => 'width: {{SIZE}}px;', 
					'{{WRAPPER}} .gem-testimonials .gem-prev:after, {{WRAPPER}} .gem-testimonials .gem-next:after' => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;',
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
						// 'step' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-prev:after, {{WRAPPER}} .gem-testimonials .gem-next:after' => 'font-size: {{SIZE}}px;',
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
                    '{{WRAPPER}} .gem-testimonials .gem-prev:after, {{WRAPPER}} .gem-testimonials .gem-next:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrow_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-testimonials .gem-prev:after, {{WRAPPER}} .gem-testimonials .gem-next:after',
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
					'{{WRAPPER}} .gem-testimonials .gem-prev:after, {{WRAPPER}} .gem-testimonials .gem-next:after' => 'top: calc(50% - {{SIZE}}px);',
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
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-prev:after' => 'left: calc(50% + {{SIZE}}px);',
					'{{WRAPPER}} .gem-testimonials .gem-next:after' => 'left: calc(50% - {{SIZE}}px);',
				],
			]
		);

		$control->start_controls_tabs('arrow_tabs');
		$control->start_controls_tab('arrow_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
			'arrow_bg_color_normal',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-prev:after, {{WRAPPER}} .gem-testimonials .gem-next:after' => 'background-color: {{VALUE}};',
				],
			]
		);	

		$control->add_responsive_control(
			'arrow_border_color_normal',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-prev:after, {{WRAPPER}} .gem-testimonials .gem-next:after' => 'border-color: {{VALUE}};',
				],
			]
		);	

		$control->add_responsive_control(
			'arrow_icon_color_normal',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-prev:after, {{WRAPPER}} .gem-testimonials .gem-next:after' => 'color: {{VALUE}}!important;',
				],
			]
		);	

		

		$control->end_controls_tab();

		$control->start_controls_tab(
			'arrow_tabs_hover',
			[
				'label' => __('Hover', 'thegem'),
			]
		);	

		$control->add_responsive_control(
			'arrow_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-prev:hover:after, {{WRAPPER}} .gem-testimonials .gem-next:hover:after' => 'background-color: {{VALUE}};',
				],
			]
		);	

		$control->add_responsive_control(
			'arrow_border_color_hover',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-prev:hover:after, {{WRAPPER}} .gem-testimonials .gem-next:hover:after' => 'border-color: {{VALUE}};',
				],
			]
		);	

		$control->add_responsive_control(
			'arrow_icon_color_hover',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-testimonials .gem-prev:hover:after, {{WRAPPER}} .gem-testimonials .gem-next:hover:after' => 'color: {{VALUE}}!important;',
				],
			]
		);		

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();

	}

	/**
	 * Helper check in array value
	 * @access protected
	 * @return string
	 */
	function is_in_array_value( $array = array(), $value = '', $default = '' ) {
		if ( in_array( $value, $array ) ) {
			return $value;
		}
		return $default;
	}


	protected function get_setting_preset( $val ) {
		if( empty( $val) ) {
			return '';
		}

		return $val;
	}

	protected function get_setting_cat( $val ) {
		if ( empty( $val ) ) {
			return (array) 'all';
		}

		return (array) $val;
	}

	protected function get_presets_arg( $val ) {
		if ( empty( $val ) ) {
			return null;
		}

		return json_decode( $val, true );
	}

	protected function get_setting_els( $val ) {
		if ( empty( $val ) ) {
			return [
				'img',
				'name',
				'company',
				'position',
				'quote'
			];
		}

		return (array) $val;
	}

	protected function get_query_arg( $cat ) {
		if ( empty( $cat ) ) {
			return null;
		}

		$args = array(
			'post_type' => 'thegem_testimonial',
			'orderby' => 'menu_order ID',
			'order' => 'ASC',
			'posts_per_page' => - 1,
		);

		if ( ! in_array( 'all', $cat, true ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'thegem_testimonials_sets',
					'field' => 'slug',
					'terms' => $cat
				)
			);
		}

		return (array) $args;
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

		if ( ! empty ( $settings['content_testimonials_cat'] ) ) {
			$cat = $this->get_setting_cat( $settings[ 'content_testimonials_cat' ] );
		} else { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select testimonials in "Content" section', 'thegem') ?>
			</div>
			<?php return;
		}

		$els=[];
		foreach ( (array) $this->preset_elements_select as $kel=>$tel ) {
			if ( ! empty( $settings[ 'content_elems_' . $kel ] ) ) {
				$els[]= $kel;
			}
		}

		$args = $this->get_query_arg( $cat );

		$testimonials_items = new WP_Query( $args );

		if ( $testimonials_items->have_posts() ) {
			?>

			<div class="preloader">
				<div class="preloader-spin"></div>
			</div>
			
			<div class="<?php echo ! empty( $settings['thegem_elementor_preset'] ) ? esc_attr( $settings['thegem_elementor_preset'] ) : '' ?> gem-testimonials
			<?php if ( 'yes' === $settings['fullwidth'] ) :	echo esc_attr( 'fullwidth-block'); endif; ?>"
			<?php if ($settings[ 'autoplay' ]) : echo esc_attr( ( intval( $settings [ 'autoscroll' ] ) ? ' data-autoscroll='.intval( $settings [ 'autoscroll' ] ).'' : '' )); endif; ?> >
			
			<?php if ( 'yes' === ( $settings['show_arrows'] ) ) : ?>
				<style>.gem-testimonials-navigation{display: block;}</style>
			<?php else: ?> 
				<style>.gem-testimonials-navigation{display: none;}</style>
			<?php endif; ?> 

				<?php
				while ( $testimonials_items->have_posts() ) {
					$testimonials_items->the_post();

					$thegem_item_data = thegem_get_sanitize_testimonial_data( get_the_ID() );

					$preset_path = __DIR__ . '/templates/output-testimonials-' . $settings[ 'thegem_elementor_preset' ] . '.php';
					$preset_path_filtered = apply_filters( 'thegem_testimonials_item_preset', $preset_path);
					$preset_path_theme = get_stylesheet_directory() . '/templates/testimonials/output-testimonials-' . $settings[ 'thegem_elementor_preset' ] . '.php';

					if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
						include($preset_path_theme);
					} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
						include($preset_path_filtered);
					}
				}
				?>

				<div class="testimonials_svg"><svg width="100" height="50"><path d="M 0,-1 Q 45,5 50,50 Q 55,5 100,-1" /></svg></div>
			</div>

			<?php if( is_admin() && \Elementor\Plugin::$instance->editor->is_edit_mode() ): ?>
				<script type="text/javascript">jQuery('.elementor-element-<?php echo $this->get_id(); ?>').buildTestimonialsCarousel();jQuery('.elementor-element-<?php echo $this->get_id(); ?>').updateTestimonialsCarousel();</script>
			<?php endif; ?> 



		<?php } else { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select testimonials in "Content" section', 'thegem') ?>
			</div>
		<?php }
		wp_reset_postdata();
	}

	public function get_preset_data() {
		return array(
			'style1' => array(
				'thegem_elementor_image_size' => ['unit' => 'px', 'size' => 128],
				'img_radius' => ['top' => 60, 'right' => 60, 'bottom' => 60, 'left' => 60, 'unit' => '%', 'isLinked' => true],
			),
			'style2' => array(
				'thegem_elementor_image_size' => ['unit' => 'px', 'size' => 80],
				'img_radius' => ['top' => 60, 'right' => 60, 'bottom' => 60, 'left' => 60, 'unit' => '%', 'isLinked' => true],
			),
		);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new TheGem_Testimonials() );
