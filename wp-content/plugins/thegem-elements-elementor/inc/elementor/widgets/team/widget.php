<?php
namespace TheGem_Elementor\Widgets\Team;
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
 * Elementor widget for Team.
 */
class TheGem_Team extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */
	protected $presets;

	public $preset_elements_select;

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if( ! defined( 'THEGEM_ELEMENTOR_WIDGET_TEAM_DIR' ) ){
			define( 'THEGEM_ELEMENTOR_WIDGET_TEAM_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_TEAM_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_TEAM_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}

		$this->preset_elements_select = [
			'img' => __( 'Image', 'thegem' ),
			'name' => __( 'Name', 'thegem' ),
			'position' => __( 'Position', 'thegem' ),
			'descript' => __( 'Description', 'thegem' ),
			'phone' => __( 'Phone', 'thegem' ),
			'email' => __( 'Email', 'thegem' ),
			'social' => __( 'Social', 'thegem' ),
		];

		wp_register_style( 'thegem-team', THEGEM_ELEMENTOR_WIDGET_TEAM_URL . '/assets/css/thegem-team.css', array(), null );
		wp_register_script( 'thegem-team-hover', THEGEM_ELEMENTOR_WIDGET_TEAM_URL . '/assets/js/thegem-team-hover.js', array( 'jquery' ), null, true );

	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-team';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Team', 'thegem' );
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
		return [ 'thegem-team' ];
	}

	public function get_script_depends() {
		return [ 'thegem-team-hover' ];
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
		return '1';
	}

	/**
	 * Make options select team
	 * @access protected
	 * @return array
	 */
	protected function select_team() {
		$out   = [ 'all' => __( 'All', 'thegem' ) ];
		$terms = get_terms( [
			'taxonomy' => 'thegem_teams',
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

			$this->add_responsive_control(
				'columns',
				[
					'label' => __( 'Columns', 'thegem' ),
					'type' => Controls_Manager::SELECT,
					'default' => 3,
					'options' => [
						1 => 1,
						2 => 2,
						3 => 3,
						4 => 4,
					],
					'desktop_default' => 3,
					'tablet_default' => 2,
					'mobile_default' => 1,
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'items_count',
				[
					'label' => __( 'Number of Posts', 'thegem' ),
					'description' => __( 'Use - 1 to show all', 'thegem' ),
					'label_block' => false,
					'type' => Controls_Manager::NUMBER,
					'default' => - 1,
					'min' => - 1,
					'max' => 100,
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_settings',
			[
				'label' => __( 'Content', 'thegem' ),
			]
		);

		$this->add_control(
			'content_team_cat',
			[
				'label' => __( 'Select Team', 'thegem' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'all',
				'multiple' => true,
				'options' => $this->select_team(),
				'frontend_available' => true,
				'label_block' => true,
			]
		);

		if( !empty( $this->preset_elements_select) ){
			foreach ( (array) $this->preset_elements_select as $ekey=>$elem ) {

				if( $ekey === 'descript'){
					$def = '';
				}else{
					$def = 'yes';
				}

				$this->add_control(
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

		$this->end_controls_section();

		$this->add_styles_controls( $this );

	}

	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls($control) {


		$this->control = $control;

		/*Container Styles*/
		$this->container_styles( $control );

		/*Image  Styles*/
		$this->image_styles( $control );

		/*Name  Styles*/
		$this->name_styles( $control );

		/*Position  Styles*/
		$this->position_styles( $control );

		/*Description  Styles*/
		$this->description_styles( $control );

		/*Phone  Styles*/
		$this->phone_styles( $control );

		/*Email  Styles*/
		$this->email_styles( $control );

		/*Social  Styles*/
		$this->social_styles( $control );

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

			$control->add_responsive_control(
				'container_bgcolor',
				[
					'label' => __( 'Background Color', 'thegem' ),
					'type' => Controls_Manager::COLOR,
					'label_block' => false,
					'selectors' => [
						'{{WRAPPER}} .team-person' => 'background-color: {{VALUE}} !important;',
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
						'{{WRAPPER}} .team-person' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .team-person-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .team-person-hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$control->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'container_border',
					'label' => __( 'Border', 'thegem' ),
					'selector' => '{{WRAPPER}} .team-person',
				]
			);

			$control->add_control(
				'container_border_bottom_color',
				[
					'label' => __( 'Bottom Border Color', 'thegem' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .team-person' => 'border-bottom-color: {{VALUE}};',
					],
					'condition' => [
						'thegem_elementor_preset' => '4'
					]
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
						'left' => 'text-align: left;align-items: flex-start;',
						'center' => 'text-align: center;align-items: center;',
						'right' => 'text-align: right;align-items: flex-end;',

					],
					'selectors' => [
						'{{WRAPPER}} .team-person, {{WRAPPER}} .team-person-image, {{WRAPPER}} .team-person-contacts-group' => '{{VALUE}}',
					],
				]
			);

			$control->add_responsive_control(
				'container_gaps',
				[
					'label' => __( 'Gaps', 'thegem' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'rem', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .gem-team' => 'margin: calc(-{{SIZE}}{{UNIT}}/2);',
						'{{WRAPPER}} .thegem-wrap' => 'padding: calc({{SIZE}}{{UNIT}}/2);',
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
						'{{WRAPPER}} .gem-team:not(.gem-team-style-6) .team-person, {{WRAPPER}} .gem-team.gem-team-style-6 .team-person-hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$control->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'container_shadow',
					'label' => __( 'Shadow', 'thegem' ),
					'selector' => '{{WRAPPER}} .team-person',
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
						'{{WRAPPER}} .team-person:hover' => 'background-color: {{VALUE}} !important;',
						'{{WRAPPER}} .gem-team-style-5 .team-person:hover .team-person-hover' => 'background-color: {{VALUE}} !important;',
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
						'{{WRAPPER}} .team-person:hover' => 'border-color: {{VALUE}} !important;',
					],
				]
			);

			$control->add_control(
				'container_border_bottom_color_hv',
				[
					'label' => __( 'Bottom Border Color', 'thegem' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .team-person:hover' => 'border-bottom-color: {{VALUE}};',
					],
					'condition' => [
						'thegem_elementor_preset' => '4'
					]
				]
			);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow_hv',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .team-person:hover',
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
/*				'condition' => [
					'thegem_elementor_preset!' => '5',
				]*/
			]
		);

		$control->start_controls_tabs( 'img_tabs' );
		$control->start_controls_tab( 'img_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'img_width',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 400,
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
					'{{WRAPPER}} .team-person-image' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .team-person-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'img_padding',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'condition' => [
					'thegem_elementor_preset' => '5',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-team-style-5 .team-person-image .image-hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'default' => !empty($this->preset_styles['image_radius']) ? $this->preset_styles['image_radius'] : null,
				'selectors' => [
					'{{WRAPPER}} .team-person-image span, {{WRAPPER}} .team-person-image span:before, {{WRAPPER}} .team-person-image span img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'img_main_bgcolor',
			[
				'label' => __( 'Background Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'thegem_elementor_preset' => '5',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-team-style-5 .team-person-image .image-hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'img_border',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-team:not(.gem-team-style-5) .team-person-image span, {{WRAPPER}} .gem-team.gem-team-style-5 .team-person-image .image-hover',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .team-person-image span',
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
				'condition' => [
					'thegem_elementor_preset!' => '5',
				],
				'selectors' => [
					'{{WRAPPER}} .team-person-image > span::before' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_control(
			'img_overlay_hover',
			[
				'label' => __( 'Overlay Opacity', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'step' => '0.1',
				'min' => '0',
				'max' => '1',
				'condition' => [
					'thegem_elementor_preset!' => '5',
				],
				'selectors' => [
					'{{WRAPPER}} .w-link .team-person:hover .team-person-image span::before' => 'opacity: {{VALUE}} !important;',
				],
			]
		);

		$control->add_control(
			'img_main_bgcolor_hover',
			[
				'label' => __( 'Background Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'thegem_elementor_preset' => '5',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-team-style-5 .team-person:hover .team-person-image .image-hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'img_border_hover',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .gem-team:not(.gem-team-style-5) .w-link .team-person:hover .team-person-image span, {{WRAPPER}} .gem-team.gem-team-style-5 .team-person:hover .team-person-image .image-hover',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .w-link .team-person:hover .team-person-image span',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();

/*		$control->start_controls_section(
			'img_preset_5',
			[
				'label' => __( 'Image Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'thegem_elementor_preset' => '5',
				]
			]

		);

		$control->add_responsive_control(
			'img_width_preset_5',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 400,
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
					'{{WRAPPER}} .gem-team-style-5 .team-person-image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'img_margin_preset_5',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-team-style-5 .team-person-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'img_padding_preset_5',
			[
				'label' => __( 'Padding', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-team-style-5 .team-person-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'img_radius_preset_5',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'default' => [
					'top' => 50,
					'right' => 50,
					'bottom' => 50,
					'left' => 50,
					'unit' => '%'
				],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-team-style-5 .team-person-image span, {{WRAPPER}} .gem-team-style-5 .team-person-image span:before, {{WRAPPER}} .gem-team-style-5 .team-person-image span img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->end_controls_section();*/

	}

	/**
	 * Name Styles
	 * @access protected
	 */
	protected function name_styles( $control ) {

		$control->start_controls_section(
			'title',
			[
				'label' => __( 'Name Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->start_controls_tabs( 'name_tabs' );
		$control->start_controls_tab( 'name_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'title_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-name, {{WRAPPER}} .team-person-name span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'title_typ',
				'selector' => '{{WRAPPER}} .team-person-name, {{WRAPPER}} .team-person-name span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'name_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_responsive_control(
			'title_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .w-link .team-person:hover .team-person-name, {{WRAPPER}} .w-link .team-person:hover .team-person-name span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'title_typ_hv',
				'selector' => '{{WRAPPER}} .w-link .team-person:hover .team-person-name, {{WRAPPER}} .w-link .team-person:hover .team-person-name span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_responsive_control(
			'title_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .team-person-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'title_align',
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
					'{{WRAPPER}} .team-person-name' => '{{VALUE}}',
				],
			]
		);


		$control->end_controls_section();

	}

	/**
	 * Position Styles
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

		$control->start_controls_tabs( 'posit_tabs' );
		$control->start_controls_tab( 'posit_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'posit_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-position' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'posit_typ',
				'selector' => '{{WRAPPER}} .team-person-position',
				'scheme' => Schemes\Typography::TYPOGRAPHY_2,
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'posit_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_responsive_control(
			'posit_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .w-link .team-person:hover  .team-person-position' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'posit_typ_hv',
				'selector' => '{{WRAPPER}} .w-link .team-person:hover .team-person-position',
				'scheme' => Schemes\Typography::TYPOGRAPHY_2,
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_responsive_control(
			'posit_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .team-person-position' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'left' => 'text-align: left;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;',

				],
				'selectors' => [
					'{{WRAPPER}} .team-person-position' => '{{VALUE}}',
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

		$control->add_responsive_control(
			'descr_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-description' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'descr_typ',
				'selector' => '{{WRAPPER}} .team-person-description',
				'scheme' => Schemes\Typography::TYPOGRAPHY_2,
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
					'{{WRAPPER}} .team-person-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .team-person-description' => '{{VALUE}}',
				],
			]
		);

		$control->end_controls_section();

	}

	/**
	 * Phone Styles
	 * @access protected
	 */
	protected function phone_styles( $control ) {

		$control->start_controls_section(
			'phone',
			[
				'label' => __( 'Phone Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->start_controls_tabs( 'phone_tabs' );
		$control->start_controls_tab( 'phone_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'phone_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-phone a' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'phone_typ',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .team-person-phone a',
			]
		);

		$control->add_responsive_control(
			'phone_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .team-person-phone' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'phone_align',
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
					'{{WRAPPER}} .team-person-phone' => '{{VALUE}}',
				],
			]
		);
		$control->end_controls_tab();

		$control->start_controls_tab( 'phone_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );
		$control->add_responsive_control(
			'phone_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-phone a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'phone_typ_hv',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .team-person-phone a:hover',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();

	}

	/**
	 * Email Styles
	 * @access protected
	 */
	protected function email_styles( $control ) {

		$control->start_controls_section(
			'email',
			[
				'label' => __( 'Email Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->add_responsive_control(
			'email_icon_size',
			[
				'label' => __( 'Size Icon', 'thegem' ),
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
				'selectors' => [
					'{{WRAPPER}} .team-person-email a:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'email_icon_margin',
			[
				'label' => __( 'Margin Icon', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .team-person-email a:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'email_icon' => 'yes',
				]
			]
		);


		$control->start_controls_tabs( 'email_tabs' );
		$control->start_controls_tab( 'email_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'email_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-email a' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'email_typ',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .team-person-email a',
			]
		);

		$control->add_responsive_control(
			'email_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .team-person-email' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'email_align',
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
					'{{WRAPPER}} .team-person-email' => '{{VALUE}}',
				],
			]
		);
		$control->end_controls_tab();

		$control->start_controls_tab( 'email_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );
		$control->add_responsive_control(
			'email_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .team-person-email a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'email_typ_hv',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .team-person-email a:hover',
			]
		);


		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();

	}

	/**
	 * Social Styles
	 * @access protected
	 */
	protected function social_styles( $control ) {

		$control->start_controls_section(
			'social',
			[
				'label' => __( 'Social Icon Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$control->add_responsive_control(
			'social_ico_size',
			[
				'label' => __( 'Icon Size', 'thegem' ),
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
				'selectors' => [
					'{{WRAPPER}} .socials-item i:before' => 'font-size: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$control->add_responsive_control(
			'social_icon_blk_margin',
			[
				'label' => __( 'Margin Block Icon', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .socials' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'social_icon_margin',
			[
				'label' => __( 'Margin Icon', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .socials-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'social_tabs' );
		$control->start_controls_tab( 'social_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'social_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .socials-item' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'social_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_responsive_control(
			'social_color_hv',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .socials-item:hover i, {{WRAPPER}} .socials-item:hover i:before' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();

	}


	/**
	 * Set columns class
	 *
	 * @param int $col
	 *
	 * @access protected
	 * @return string
	 */
	protected function columns_classes( $columns, $columns_tablet, $columns_mobile ) {
		$classes = array();
		switch ( $columns ) {
			case 1:
				$classes[] = 'col-md-12'; break;
			case 2:
				$classes[] = 'col-md-6'; break;
			case 4:
				$classes[] = 'col-md-3'; break;
			default:
				$classes[] = 'col-md-4';
		}
		switch ( $columns_tablet ) {
			case 1:
				$classes[] = 'col-sm-12'; break;
			case 2:
				$classes[] = 'col-sm-6'; break;
			case 4:
				$classes[] = 'col-sm-3'; break;
			default:
				$classes[] = 'col-sm-4';
		}
		switch ( $columns_mobile ) {
			case 1:
				$classes[] = 'col-xs-12'; break;
			case 2:
				$classes[] = 'col-xs-6'; break;
			case 4:
				$classes[] = 'col-xs-3'; break;
			default:
				$classes[] = 'col-xs-4';
		}
		return implode(' ', $classes);
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

	protected function get_setting_col( $val ) {
		if ( empty( $val ) ) {
			return 3;
		}

		return (int) $val;
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
				'position',
				'descript',
				'phone',
				'email',
				'social'
			];
		}

		return (array) $val;
	}

	protected function get_query_arg( $val, $cat ) {
		if ( empty( $val ) || empty( $cat ) ) {
			return null;
		}

		$args = array(
			'post_type' => 'thegem_team_person',
			'orderby' => 'menu_order ID',
			'order' => 'ASC',
			'posts_per_page' => - 1,
		);

		if ( empty( $val ) ) {
			$args['posts_per_page'] = - 1;
		} else {
			$args['posts_per_page'] = (int) $val;
		}

		if ( ! in_array( 'all', $cat, true ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'thegem_teams',
					'field' => 'slug',
					'terms' => $cat
				)
			);
		}

		return (array) $args;
	}

	public function get_preset_data() {
		return array(
			'1' => array(
				'content_elems_descript' => '',
			),
			'2' => array(
				'content_elems_descript' => '',
			),
			'3' => array(
				'content_elems_descript' => '',
			),
			'4' => array(
				'content_elems_descript' => '',
			),
			'5' => array(
				'content_elems_descript' => 'yes',
			),
			'6' => array(
				'content_elems_descript' => '',
			)
		);
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
//print_r($settings);
		$preset = $this->get_setting_preset( $settings['thegem_elementor_preset'] );
		if( empty( $preset) ) return;

		$cat = $this->get_setting_cat( $settings[ 'content_team_cat' ] );
		$columns_classes = $this->columns_classes( $settings[ 'columns' ], $settings[ 'columns_tablet' ], $settings[ 'columns_mobile' ] );

		$els=[];
		foreach ( (array) $this->preset_elements_select as $kel=>$tel ) {
			if ( ! empty( $settings[ 'content_elems_' . $kel ] ) ) {
				$els[]= $kel;
			}
		}

		$args = $this->get_query_arg( $settings[ 'items_count' ], $cat );

		$persons = new WP_Query( $args );

		if ( $persons->have_posts() ) {
			$preset_path = __DIR__ . '/templates/team-design-' .$settings[ 'thegem_elementor_preset' ]. '.php';
			$preset_path_filtered = apply_filters( 'thegem_team_design'.$settings[ 'thegem_elementor_preset' ].'_item_preset', $preset_path);
			$preset_path_theme = get_stylesheet_directory() . '/templates/team/team-design-' . $settings[ 'thegem_elementor_preset' ] . '.php';

			$data_attr = '';
				if( $preset === '5'){
					$data_attr .= ' data-hover-colors=\'{"image_border_color":"#f4f6f7"}\'';
				}
			?>

			<div class="row gem-team gem-team-style-<?php echo esc_attr($preset); ?>" <?php echo $data_attr; ?>>

				<?php
					while ( $persons->have_posts() ) {
						$persons->the_post();

						$thegem_link_start  = '';
						$thegem_link_end    = '';
						$thegem_image_start = '<span>';
						$thegem_image_end   = '</span>';

						$thegem_item_data = thegem_get_sanitize_team_person_data( get_the_ID() );

						$thegem_link = thegem_get_data( $thegem_item_data, 'link' );
						if ( !empty( $thegem_link) ) {
							$hover_class = ' w-link';
							$thegem_link_start  = '<a class="team-person-link" href="' . esc_url( $thegem_link ) . '" target="' . esc_attr( thegem_get_data( $thegem_item_data, 'link_target' ) ) . '">';
							$thegem_link_end    = '</a>';
						}else{
							$hover_class = '';
						}

						$thegem_socials_block = '';
						foreach ( thegem_team_person_socials_list() as $thegem_key => $thegem_value ) {
							if ( $thegem_item_data[ 'social_link_' . $thegem_key ] ) {
								$protocol = $thegem_key === 'skype' ? array( 'skype' ) : '';
								thegem_additionals_socials_enqueue_style( $thegem_key );
								$thegem_socials_block .= '<a ' . ( ! empty( $settings[ 'social_color' ] ) ? 'style="color: ' . esc_attr( $settings[ 'social_color' ] ) . '"' : '' ) . ' title="' . esc_attr( $thegem_value ) . '" target="_blank" href="' . esc_url( $thegem_item_data[ 'social_link_' . $thegem_key ], $protocol ) . '" class="socials-item"><i class="socials-item-icon social-item-rounded ' . esc_attr( $thegem_key ) . '"></i></a>';
							}
						}

						$socials_list = thegem_socials_icons_list();
						foreach ( $thegem_item_data['additional_social_links'] as $thegem_social ) {
							$thegem_socials_block .= '<a ' . ( ! empty( $settings[ 'social_color' ] ) ? 'style="color: ' . esc_attr( $settings[ 'social_color' ] ) . '"' : '' ) . ' title="' . esc_attr( $socials_list[ $thegem_social['social'] ] ) . '" target="_blank" href="' . esc_url( $thegem_social['link'] ) . '" class="socials-item"><i class="socials-item-icon social-item-rounded ' . esc_attr( $thegem_social['social'] ) . '"></i></a>';
						}

						/* Include HTML Themplate */
						if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
							include($preset_path_theme);
						} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
							include($preset_path_filtered);
						}
					}
				?>

			</div>

		<?php }
		wp_reset_postdata();
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new TheGem_Team() );
