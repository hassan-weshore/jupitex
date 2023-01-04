<?php
namespace TheGem_Elementor\Widgets\QuickFinders;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Schemes;


if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Elementor widget for QuickFinders.
 */
class TheGem_QuickFinders extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		if( ! defined( 'THEGEM_ELEMENTOR_WIDGET_QUICKFINDERS_DIR' ) ){
			define( 'THEGEM_ELEMENTOR_WIDGET_QUICKFINDERS_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_QUICKFINDERS_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_QUICKFINDERS_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}
		wp_register_style( 'thegem-quickfinder', THEGEM_ELEMENTOR_WIDGET_QUICKFINDERS_URL . '/assets/css/thegem-quickfinder.css', array( 'thegem-icon-css', ), null );
		wp_register_style( 'thegem-quickfinder-vertical', THEGEM_ELEMENTOR_WIDGET_QUICKFINDERS_URL . '/assets/css/thegem-quickfinder-vertical.css', array(), null );
	}


	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-quickfinders';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Quickfinders', 'thegem' );
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
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-quickfinder-vertical',
				'thegem-quickfinder',
				'thegem-button'];
		}
		return [ 'thegem-quickfinder' ];
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
		$out = array_merge(
			[
				'grid-basic-centered' => [
					'label' => __( 'Basic Centered', 'thegem' ),
					'group' => 'grid',
				],
				'grid-basic-left-align' => [
					'label' => __( 'Basic Left Align', 'thegem' ),
					'group' => 'grid',
				],
				'grid-basic-right-align' => [
					'label' => __( 'Basic Right Align', 'thegem' ),
					'group' => 'grid',
				],
				'grid-box-solid' => [
					'label' => __( 'Box Solid', 'thegem' ),
					'group' => 'grid',
				],
				'grid-box-outlined' => [
					'label' => __( 'Box Outlined', 'thegem' ),
					'group' => 'grid',
				],
				'iconed' => [
					'label' => __( 'Iconic Box', 'thegem' ),
					'group' => 'grid',
				],
				'vertical-4' => [
					'label' => __( 'Basic Centered', 'thegem' ),
					'group' => 'vertical',
				],
				'vertical-3' => [
					'label' => __( 'Basic Left Align', 'thegem' ),
					'group' => 'vertical',
				],
				'vertical-3-right-align' => [
					'label' => __( 'Basic Right Align', 'thegem' ),
					'group' => 'vertical',
				],
				'vertical-1' => [
					'label' => __( 'Bubbles Centered', 'thegem' ),
					'group' => 'vertical',
				],
				'vertical-2' => [
					'label' => __( 'Bubbles Left Align', 'thegem' ),
					'group' => 'vertical',
				],
				'vertical-2-right-align' => [
					'label' => __( 'Bubbles Right Align', 'thegem' ),
					'group' => 'vertical',
				],
			]
		);
		return $out;
	}

	private function filter_skins_by( $array, $key, $value ) {
		return array_filter( $array, function( $skin ) use ( $key, $value ) {
			return $value === $skin[ $key ];
		} );
	}

	private function get_options_by_groups( $skins, $group = false ) {
		foreach ( $skins as $key => $skin ) {
			if ( ! isset( $groups[ $skin['group'] ] ) ) {
				$groups[ $skin['group'] ] = [
					'label' => ucwords( str_replace( '_', '', $skin['group'] ) ),
					'options' => [],
				];
			}
			$groups[ $skin['group'] ]['options'][ $key ] = $skin['label'];
		}

		if ( $group && isset( $groups[ $group ] ) ) {
			return $groups[ $group ];
		}
		return $groups;
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
		return 'grid-basic-centered';
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$skins = $this->get_presets_options();

		$this->start_controls_section(
			'qf_items_heading',
			[
				'label' => __( 'Quickfinder Items', 'thegem' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'qf_items_tabs' );
		$repeater->start_controls_tab( 'qf_items_tab_general', [ 'label' => __( 'GENERAL', 'thegem' ), ] );
		
		$repeater->add_control(
			'heading_block_title',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'qf_item_title',
			[
				'label' => __( 'Title Text', 'thegem' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Quick Item', 'thegem' ),
				'label_block' => 'true',
			]
		);

		$repeater->add_control(
			'qf_item_title_html_tag',
			[
				'label' => __( 'Title HTML Tag', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default' => 'h4',
			]
		);

		$repeater->add_control(
			'qf_item_title_html_tag_weight',
			[
				'label' => __( 'Title Weight', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold' => __( 'Bold', 'thegem' ),
					'light' => __( 'Thin', 'thegem' ),
				],
			]
		);

		$repeater->add_control(
			'qf_item_title_html_tag_h_disable',
			[
				'label' => __( 'Disable H-tag', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'thegem' ),
				'label_off' => __( 'No', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'heading_block_description',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'qf_item_description',
			[
				'label' => __( 'Content Text', 'thegem' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'thegem' ),
			]
		);

		$repeater->add_control(
			'heading_block_link',
			[
				'label' => __( 'Item Link', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'qf_item_link',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);
		$repeater->end_controls_tab();
		
		$repeater->start_controls_tab( 'qf_items_tab_additional', [ 'label' => __( 'ADDITIONAL ', 'thegem' ), ] );
	
		$repeater->add_control(
			'heading_block_icon_image',
			[
				'label' => __( 'Icon / Image', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$repeater->add_control(
			'qf_item_icon_image_select',
			[
				'label' => __( 'Icon / Image', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'none' => [
						'title' => __( 'None', 'thegem' ),
						'icon' => 'eicon-ban',
					],
					'image' => [
						'title' => __( 'Image', 'thegem' ),
						'icon' => 'fa fa-picture-o',
					],
					'icon' => [
						'title' => __( 'Icon', 'thegem' ),
						'icon' => 'eicon-star',
					],
				],
				'default' => 'icon',
			]
		);

		$repeater->add_control(
			'qf_item_graphic_image',
			[
				'label' => __( 'Choose Image', 'thegem' ),
				'description' => __( 'You can also adjust image size in "Style > Image Style > Size"', 'thegem' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'qf_item_icon_image_select' => 'image',
				],
				'show_label' => false,
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'qf_item_graphic_image', 
				'default' => 'thumbnail',
				'condition' => [
					'qf_item_icon_image_select' => 'image',
					'qf_item_graphic_image[id]!' => '',
				],
			]
		);

		$repeater->add_control(
			'qf_item_selected_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'qf_item_icon_image_select' => 'icon',
				],
			]
		);


		$repeater->add_control(
			'qf_item_icon_image_link_to',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __( 'None', 'thegem' ),
					'file' => __( 'Media File', 'thegem' ),
					'custom' => __( 'Custom URL', 'thegem' ),
				],
			]
		);

		$repeater->add_control(
			'qf_item_icon_image_link',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'condition' => [
					'qf_item_icon_image_link_to' => 'custom',
				],
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'qf_item_icon_image_open_lightbox',
			[
				'label' => __( 'Lightbox', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'yes',
				'options' => [
					'yes' => __( 'Yes', 'thegem' ),
					'no'  => __( 'No', 'thegem' ),
				],
				'condition' => [
					'qf_item_icon_image_link_to' => 'file',
				],
			]
		);

		$repeater->add_control(
			'heading_block_button',
			[
				'label' => __( 'Button', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'qf_item_show_button',
			[
				'label' => __( 'Show Button', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		
		$repeater->add_control(
			'qf_item_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Learn More', 'thegem'),
				'condition' => [ 
					'qf_item_show_button' => 'yes'
				],
			]
		);

		$repeater->add_control(
			'qf_item_button_text_weight',
			[
				'label' => __('Text Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold' => __('Bold', 'thegem'),
					'thin' => __('Thin', 'thegem'),
				],
				'condition' => [ 
					'qf_item_show_button' => 'yes'
				],
			]
		);

		$repeater->add_control(
			'qf_item_button_link',
			[
				'label' => __('Button Link', 'thegem'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('#', 'thegem'),
				'condition' => [ 
					'qf_item_show_button' => 'yes'
				],
			]
		);

		$repeater->add_control(
			'qf_item_button_icon',
			[
				'label' => __( 'Button Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [ 
					'qf_item_show_button' => 'yes'
				],
			]
		);
			
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs(); 

		$this->add_control(
			'qf_list',
			[
				'label' => __( 'Quickfinder List', 'thegem' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'qf_item_title' => __( 'Lorem Ipsum', 'thegem' ),
						'qf_item_description' => __( 'Lorem ipsum dolor sit amet, con secte tur adipi sicing elit, sed do eiusmod tempor incididunt ut labore et dolore', 'thegem' ),
					],
					[
						'qf_item_title' => __( 'Lorem Ipsum', 'thegem' ),
						'qf_item_description' => __( 'Lorem ipsum dolor sit amet, con secte tur adipi sicing elit, sed do eiusmod tempor incididunt ut labore et dolore', 'thegem' ),
					],
					[
						'qf_item_title' => __( 'Lorem Ipsum', 'thegem' ),
						'qf_item_description' => __( 'Lorem ipsum dolor sit amet, con secte tur adipi sicing elit, sed do eiusmod tempor incididunt ut labore et dolore', 'thegem' ),
					],
					[
						'qf_item_title' => __( 'Lorem Ipsum', 'thegem' ),
						'qf_item_description' => __( 'Lorem ipsum dolor sit amet, con secte tur adipi sicing elit, sed do eiusmod tempor incididunt ut labore et dolore', 'thegem' ),
					],
				],
				'title_field' => '{{{ qf_item_title }}}',
				'render' => 'template',
			]
		);
		
		$this->end_controls_section();

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
				'type'	=> Controls_Manager::SELECT,
				'groups' => array_values( $this->get_options_by_groups( $skins ) ),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columns', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 4,
				'options' => [
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				],
				'desktop_default' => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);

		$this->add_control(
			'no_gaps',
			[
				'label' => __( 'No Gaps', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_options',
			[
				'label' => __('Options', 'thegem'),
			]
		);

		$this->add_control(
			'lazy',
			[
				'label' => __('Lazy Loading Animation', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
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

		/* Container Styles*/
		$this->container_styles( $control );

		/* Title and Description Styles */
		$this->title_description_styles( $control );
	
		/* Icon Styles */
		$this->icon_styles( $control );

		/* Image Styles */
		$this->image_styles( $control );

		/* Button Styles */
		$this->button_styles( $control );

		/* Connector Styles */
		$this->connector_styles( $control );

	}

	/**
	 * Container Styles
	 * @access protected
	 */
	protected function container_styles( $control ) {

		$skins = $this->get_presets_options();

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
				'name' => 'container_bg_color_grid',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .quickfinder-item-inner',
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);

		$control->add_responsive_control(
			'container_bg_color_vertical',
			[
				'label' => __( 'Background Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item-info' => 'background-color: {{VALUE}};', 
					'{{WRAPPER}} .quickfinder-style-vertical .qf-svg-arrow-left, {{WRAPPER}} .quickfinder-style-vertical .qf-svg-arrow-right' => 'fill: {{VALUE}};', 
				],
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'vertical' ) ),
				],
			]
		);

		$control->add_responsive_control(
			'container_radius',
			[
				'label' => __( 'Radius', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .quickfinder-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border_grid',
				'separator' => 'before',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .quickfinder-item-inner',
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);
		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border_vertical',
				'separator' => 'before',
				'label' => __( 'Border', 'thegem' ),
				'selector' => '{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item-info',
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'vertical' ) ),
				],
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
					'left' => 'text-align: left;justify-content: flex-start;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;justify-content: flex-end;',

				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder-item-inner, {{WRAPPER}} .quickfinder-item-inner .gem-qf-icon-image-wrapper' => '{{VALUE}}',
					'{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item-info' => '{{VALUE}}',
				],
				'conditions'   => [
					'terms' => [
						[
							'name'	 => 'thegem_elementor_preset',
							'operator' => 'in',
							'value'	=> [
								'grid-basic-centered',
								'grid-basic-left-align',
								'grid-basic-right-align',
								'grid-box-solid',
								'grid-box-outlined',
								'iconed',
								'vertical-3',
								'vertical-3-right-align',
								'vertical-2',
								'vertical-2-right-align',
							],
						],
					]
				],

			]
		);

		$control->add_control(
			'content_position_vertical',
			[
				'label' => __('Content Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'align-to-icon'   => __('Aligned to Icon', 'thegem'),
					'align-to-border' => __('Aligned to Section Border', 'thegem'),
					'align-center'	=> __('Centered', 'thegem'),
				],
				'default' => 'align-to-icon',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'	 => 'thegem_elementor_preset',
							'operator' => ' == ', 
							'value'	=> 'vertical-4',
						],
						[
							'name'	 => 'thegem_elementor_preset',
							'operator' => ' == ',
							'value'	=> 'vertical-1',
						],
					]
				],
			]
		);

		$control->add_responsive_control(
			'container_horizontal_gaps',
			[
				'label' => __('Horizontal Gaps', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2) ; padding-right: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .quickfinder' => 'margin-left: calc(-{{SIZE}}{{UNIT}} / 2) ; margin-right: calc(-{{SIZE}}{{UNIT}} / 2);',
				],
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);

		$control->add_responsive_control(
			'container_vertical_gaps',
			[
				'label' => __('Vertical Gaps', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
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
				'default' => [
					'unit' => 'px',
					'size' => 42,
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item' => 'padding-top: calc({{SIZE}}{{UNIT}} / 2);padding-bottom: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .quickfinder' => 'margin-top: calc(-{{SIZE}}{{UNIT}} / 2);margin-bottom: calc(-{{SIZE}}{{UNIT}} / 2);',
				],
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
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
					'{{WRAPPER}} .quickfinder-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow_grid',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .quickfinder-item-inner',
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow_vertical',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item-info',
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'vertical' ) ),
				],
			]
		);

		
		$control->end_controls_tab();

		$control->start_controls_tab( 'container_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'container_bg_color_grid_hover',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-item-inner',
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);

		$control->add_responsive_control(
			'container_bg_color_vertical_hover',
			[
				'label' => __( 'Background Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item:hover .quickfinder-item-info' => 'background-color: {{VALUE}};', 
					'{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item:hover .qf-svg-arrow-left, {{WRAPPER}} .quickfinder .quickfinder-item:hover .qf-svg-arrow-right' => 'fill: {{VALUE}};', 
				],
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'vertical' ) ),
				],
			]
		);

		$control->add_responsive_control(
			'container_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-item-inner' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item:hover .quickfinder-item-info' => 'border-color: {{VALUE}};',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow_grid_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-item-inner',
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);
		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow_vertical_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .quickfinder-style-vertical .quickfinder-item:hover .quickfinder-item-info',
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'vertical' ) ),
				],
			]
		);


		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();

	}

	/**
	 *  Title and Description Styles
	 * @access protected
	 */
	protected function title_description_styles( $control ) {

		$control->start_controls_section(
			'block_title_description',
			[
				'label' => __( 'Title & Description Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'title_heading',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$control->start_controls_tabs( 'title_tabs' );
		$control->start_controls_tab( 'title_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'title_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
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
					'{{WRAPPER}} .quickfinder .quickfinder-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-title' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( 
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-title',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);	
			
		$control->end_controls_tab();

		$control->start_controls_tab( 'title_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'title_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-title' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( 
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'title_typography_hover',
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-title',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);	

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'descr_heading',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$control->start_controls_tabs( 'descr_tabs' );
		$control->start_controls_tab( 'descr_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'descr_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-description' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'descr_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-description' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'descr_typography',
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-description',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);	

		$control->end_controls_tab();

		$control->start_controls_tab( 'tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'descr_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-description' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-description p' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-description span p' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-description span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control( Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'descr_typography_hover',
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .quickfinder-description',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();	

		$control->end_controls_section();
	}


	/**
	 * Icon Image Styles
	 * @access protected
	 */
	protected function icon_styles( $control ) {

		$skins = $this->get_presets_options();

		$control->start_controls_section(
			'block_icon',
			[
				'label' => __( 'Icon Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'icon_heading',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$control->add_control(
			'icon_shape',
			[
				'label' => __('Shape', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'default' => __('Default', 'thegem'),
					'circle'  => __('Circle', 'thegem'),
					'square'  => __('Square', 'thegem'),
					'romb'	=> __('Diamond', 'thegem'),
					'hexagon' => __('Hexagon', 'thegem'),
				],
				'default' => 'default',
			]
		);

		$control->add_control(
			'icon_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'small'  => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large'  => __('Large', 'thegem'),
					'xlarge' => __('Xlarge', 'thegem'),
				],
				'default' => 'medium',
			]
		);

		$control->add_control(
			'icon_color_split',
			[
				'label' => __('Color Split', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					''			  => __('None', 'thegem'),
					'gradient'	  => __('Gradient', 'thegem'),
					'angle-45deg-r' => __('45 degree Right', 'thegem'),
					'angle-45deg-l' => __('45 degree Left', 'thegem'),
					'angle-90deg'   => __('90 degree', 'thegem'),					
				],
				'default' => '',
			]
		);

		$control->add_control(
			'icon_vertical_position',
			[
				'label' => __('Vertical Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'separator' => 'before',
				'default' => 'icon-top',
				'options' => [
					'icon-top' => __('Top', 'thegem'),
					'icon-bottom' => __('Bottom', 'thegem'),
				],
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);

		$control->add_control(
			'icon_horizontal_position',
			[
				'label' => __('Horizontal Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'right' => __('Right', 'thegem'),
					'center' => __('Center', 'thegem'),
					'left' => __('Left', 'thegem'),
				],
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);
	
		$control->add_control(
			'icon_text_wrapping',
			[
				'label' => __( 'Text Wrapping', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'   => __('None', 'thegem'),
					'inline' => __('Inline, no wrap', 'thegem'),
					'wrap'   => __('Wrap Text', 'thegem'),
				],
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'icon_vertical_position',
							'operator' => '==',
							'value' => 'icon-top',
						],
						[
							'name' => 'icon_horizontal_position',
							'operator' => '!=',
							'value' => 'center',
						],	
					],
				],
			]
		);

		$control->add_responsive_control(
			'icon_size_custom',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px  !important;',
					'{{WRAPPER}} .gem-qf-icon-image-wrapper  .gem-icon-inner .padding-box-inner' => 'width: calc(1.3 * {{SIZE}}px) !important;height: calc(1.3 * {{SIZE}}px) !important;line-height: calc(1.3 * {{SIZE}}px) !important;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'icon_size_small',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 36,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'small',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px;',			
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon svg' => 'width: {{SIZE}}px;'
				],
			]
		);

		$control->add_responsive_control(
			'icon_size_medium',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 58,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'medium',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px;',	
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon svg' => 'width: {{SIZE}}px;'	
				],
			]
		);

		$control->add_responsive_control(
			'icon_size_large',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 114,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'large',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px;',	
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon svg' => 'width: {{SIZE}}px;'	
				],
			]
		);

		$control->add_responsive_control(
			'icon_size_xlarge',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 164,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'xlarge',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon' => 'font-size: {{SIZE}}px;',	
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon svg' => 'width: {{SIZE}}px;'
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_padding',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon' => 'padding: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_padding_small',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'small',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}}  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2); 
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(59px - {{SIZE}}px * 2);height: calc(49px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_padding_medium',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'medium',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors' => [
					'{{WRAPPER}}  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2); 
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(91px - {{SIZE}}px * 2);height: calc(77px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_padding_large',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'large',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}}  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2); 
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(181px - {{SIZE}}px * 2);height: calc(153px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_responsive_control(
			'icon_box_padding_xlarge',
			[
				'label' => __('Box Padding', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_size',
							'operator' => '=',
							'value' => 'xlarge',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}}  .gem-icon-shape-hexagon-top' => 'margin-top: calc({{SIZE}}px * 2); margin-left: calc({{SIZE}}px * 2); 
					left: calc(0px - {{SIZE}}px);top: calc(0px - {{SIZE}}px);width: calc(267px - {{SIZE}}px * 2);height: calc(227px - {{SIZE}}px * 2);',
				],
			]
		);

		$control->add_control(
			'icon_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);

		$control->add_control(
			'icon_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'none' => __('None', 'thegem'),
					'solid' => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'border-style: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);

		$control->add_responsive_control(
			'icon_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
			]
		);
		$control->add_responsive_control(
			'icon_margin',
			[
				'label' => __( 'Margin', 'thegem' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'icon_shadow',
				'label' => __( 'Icon Shadow', 'thegem' ),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'default',
						],
					],
				],
				'fields_options' => [
					'text_shadow' => [
						'selectors' => [
							'{{WRAPPER}} .gem-icon .back-angle i' => 'text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
							'{{WRAPPER}} .gem-icon svg' => 'filter: drop-shadow({{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}});',
						],
					]
				],	
			]
		);

		$control->start_controls_tabs( 'icon_tabs' );
		$control->start_controls_tab( 'icon_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );
		
		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon',
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_hexagon',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon-shape-hexagon-top-inner-before',
			]
		);

		$control->add_responsive_control(
			'icon_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon .gem-icon-shape-hexagon-back-inner-before' => 'background-color: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
					],
				],
			]
		);

		
		$control->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'color_gradient',
				'types' => ['gradient'],
				'default' => 'gradient',
				'fields_options' => [
					'background' => [
						'label' => _x('Icon Color', 'Background Control', 'thegem'),
					],
					'gradient_angle' => [
						'selectors' => [
							'{{SELECTOR}}' => '-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
					],
					'color' => [
						'default' => '#91a0ac',
					],
					'color_b' => [
						'default' => '#65707e',
					]
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '=',
							'value' => 'gradient',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon .back-angle i',
			]
		);

		$control->add_control(
			'icon_color_1',
			[
				'label' => __( 'Icon Color 1', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
				],
			]
		);	


		$control->add_control(
			'icon_color_2',
			[
				'label' => __( 'Icon Color 2', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'default' => '#65707e',
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);	

		$control->add_responsive_control(
			'icon_rotate',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-icon .back-angle i' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);	

		$control->add_responsive_control(
			'icon_box_rotate',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'icon_box_shadow',
				'label' => __('Box Shadow', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .gem-qf-icon-image-wrapper .gem-icon',
			]
		);

	
		$control->end_controls_tab();
		
		$control->start_controls_tab( 'icon_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );
	
		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
												[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .icon-hover-bg',
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background_hexagon_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '=',
							'value' => 'hexagon',
						],
					],
				],
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon-shape-hexagon-top-inner-before',
			]
		);

		$control->add_responsive_control(
			'icon_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-icon' => 'border-color: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
					],
				],
			]
		);

		
		$control->add_control(
			'icon_color_hover',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'color_gradient_hover',
				'types' => ['gradient'],
				'default' => 'gradient',
				'fields_options' => [
					'background' => [
						'label' => _x('Icon Color', 'Background Control', 'thegem'),
					],
					'gradient_angle' => [
						'selectors' => [
							'{{SELECTOR}}' => '-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
						],
					]
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '=',
							'value' => 'gradient',
						],
					],
				],
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon .back-angle i',
			]
		);

		$control->add_control(
			'icon_color_1_hover',
			[
				'label' => __( 'Icon Color 1', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'default' => '#65707e',
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-1 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-1 svg' => 'fill: {{VALUE}};',
				],
			]
		);	


		$control->add_control(
			'icon_color_2_hover',
			[
				'label' => __( 'Icon Color 2', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name'  => 'icon_color_split',
							'operator' => '!=',
							'value' => 'gradient',
						],
					],
				],
				'default' => '#65707e',
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-2 i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon .gem-icon-half-2 svg' => 'fill: {{VALUE}};',
				],
			]
		);	

		$control->add_responsive_control(
			'icon_rotate_hover',
			[
				'label' => __( 'Rotate Icon', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-icon .back-angle i' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);	

		$control->add_responsive_control(
			'icon_box_rotate_hover',
			[
				'label' => __( 'Rotate Box', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',

				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'icon_box_shadow_hover',
				'label' => __('Box Shadow', 'thegem'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'icon_shape',
							'operator' => '!=',
							'value' => 'default',
						],
					],
				],
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-qf-icon-image-wrapper .gem-icon',
			]
		);		

		$control->add_control(
			'icon_hover_effect',
			[
				'label' => __('Hover Effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'fade'	 => __('Fade', 'thegem'),				
					'fill-out' => __('Fill Out', 'thegem'),				
				],
				'default' => 'fade',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'icon_background_hover_background',
							'operator' => '!=', 
							'value' => '',
						],
						[
							'name'  => 'icon_background_hexagon_hover_background',
							'operator' => '!=',
							'value' => '',
						],
					]
				],
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
			'block_img',
			[
				'label' => __( 'Image Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'img_heading',
			[
				'label' => __( 'Image', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);		

		$control->add_responsive_control(
			'img_size',
			[
				'label' => __( 'Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 60,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-image img, {{WRAPPER}} .quickfinder a .gem-image img' => 'width: {{SIZE}}{{UNIT}};max-width:  {{SIZE}}{{UNIT}};height: auto;',
				],
			]
		);
		$control->add_control(
			'img_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'none',
				'options' => [
					'none' => __('None', 'thegem'),
					'solid' => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-image span' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'img_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-image span' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'img_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-image span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quickfinder .gem-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .quickfinder .gem-image span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .quickfinder .gem-image span',
			]
		);

		$control->start_controls_tabs( 'image_tabs' );
		$control->start_controls_tab( 'image_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'img_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-image span' => 'border-color: {{VALUE}};',
				],
			]
		);


		$control->add_control(
			'img_opacity',
			[
				'label' => __( 'Opacity', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'step' => '0.1',
				'min' => '0',
				'max' => '1',
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-image img' => 'opacity: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'img_css_filters',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .quickfinder .gem-image img',
			]
		);

		$control->add_control(
			'img_blend_mode',
			[
				'label' => __( 'Blend Mode', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					''			=> __( 'Normal', 'thegem' ),
					'multiply'	=> 'Multiply',
					'screen'	  => 'Screen',
					'overlay'	 => 'Overlay',
					'darken'	  => 'Darken',
					'lighten'	 => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'color-burn'  => 'Color Burn',
					'hue'		 => 'Hue',
					'saturation'  => 'Saturation',
					'color'	   => 'Color',
					'exclusion'   => 'Exclusion',
					'luminosity'  => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-image img' => 'mix-blend-mode: {{VALUE}}',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'image_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_responsive_control(
			'img_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-image span' => 'border-color: {{VALUE}};',
				],
			]
		);
		
		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'img_overlay_color_hover',
				'label' => __( 'Overlay Color Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'default' => 'classic',
				'fields_options' => [
					'background' => [
						'label' => _x('Overlay Color Type', 'Background Control', 'thegem'),
					],
					'color' => [
						'default' => thegem_get_option('styled_elements_background_color'),
					],
				],
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-image span::before',
			]
		);

		$control->add_control(
			'img_opacity_opacity_hover',
			[
				'label' => __( 'Overlay Opacity', 'thegem' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '0.5',
				'step' => '0.1',
				'min' => '0',
				'max' => '1',
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-image > span::before' => 'opacity: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'img_css_filters_hover',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-image img',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_responsive_control(
			'img_rotate',
			[
				'label' => __( 'Rotate Image', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item .gem-image' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);	

		$control->end_controls_section();
	}

	/**
	 * Button Styles
	 * @access protected
	 */
	protected function button_styles( $control ) {

		$skins = $this->get_presets_options();

		$control->start_controls_section(
			'block_button',
			[
				'label' => __( 'Button Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$control->add_control(
			'button_heading',
			[
				'label' => __( 'Button', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);	

		$control->add_control(
			'button_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'centered-box button-bottom',
				'options' => [
					'centered-box button-top' => __('Top', 'thegem'),
					'centered-box button-bottom' => __('Bottom', 'thegem'),
				],
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ),
				],
			]
		);

		$control->add_control(
			'button_type',
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

		$control->add_control(
			'button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'small',
				'options' => [
					'tiny' => __('Tiny', 'thegem' ),
					'small' => __('Small', 'thegem' ),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem' ),
					'giant' => __('Giant', 'thegem' ),
				],
			]
		);

		$control->add_responsive_control(
			'button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'button_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'none' => __('None', 'thegem'),
					'solid' => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-button' => 'border-style: {{VALUE}};',
				],
			]
		);
		
		$control->add_control(
			'button_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-inner-wrapper-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'button_tabs' );
		$control->start_controls_tab( 'button_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_control(
			'button_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-button .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-button .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-button .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control( 
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .quickfinder .gem-button-container .gem-button',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'button_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .quickfinder .gem-button-container .gem-button',
			]
		);

		$control->end_controls_tab();
		
		$control->start_controls_tab( 'button_tab_hover', [ 'label' => __( 'Hover', 'thegem' ),] );

		$control->add_control(
			'button_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button:hover .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button:hover .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button:hover .gem-button-icon svg' => 'fill:{{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button.item-linked .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button.item-linked .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button.item-linked .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control( 
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'button_typography_hover',
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item .gem-button-container .gem-button:hover span, {{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button.item-linked span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'button_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item .gem-button-container .gem-button:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button.item-linked' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'button_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder .quickfinder-item .gem-button-container .gem-button:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button.item-linked' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .quickfinder .quickfinder-item .gem-button-container .gem-button:hover, {{WRAPPER}} .quickfinder .quickfinder-item:hover .gem-button-container .gem-button.item-linked',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_responsive_control(
			'button_icon_align',
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
					'left' => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .quickfinder .gem-inner-wrapper-btn' => '{{VALUE}}',
				],
			]
		);
		$control->add_responsive_control(
			'button_icon_spacing_right',
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
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-left:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'button_icon_align' => ['right'],
				],
			]
		);

		$control->add_responsive_control(
			'button_icon_spacing_left',
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
					'{{WRAPPER}} .quickfinder .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-right:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'button_icon_align' => ['left'],
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Connector Styles
	 * @access protected
	 */
	protected function connector_styles( $control ) {

		$skins = $this->get_presets_options();

		$control->start_controls_section(
			'connector_style',
			[
				'label' => __( 'Connector Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'thegem_elementor_preset' => array_keys( $this->filter_skins_by( $skins, 'group', 'vertical' ) ),
				],

			]
		);

		$control->add_control(
			'connector_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .quickfinder-style-vertical .connector-container span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'connector_weight',
			[
				'label' => __( 'Weight, px', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 2,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder-style-vertical .connector-container span' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'connector_length',
			[
				'label' => __( 'Length, px', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 60,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 60,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder-style-vertical .connector-container span'	 => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'connector_vertical_margin',
			[
				'label' => __( 'Top Margin, %', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => 0,
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 0,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder-style-vertical .connector-container span' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'connector_vertical_position',
			[
				'label' => __( 'Vertical Postition, %', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 100,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .quickfinder-style-vertical .connector-container span' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'connector_disable_last_item',
			[
				'label' => __( 'Disable Overlength Connector', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'thegem' ),
				'label_off' => __( 'No', 'thegem' ),
				'description' => 'Check this option if bottom connector is beyond icon',
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

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
		if( empty( $val ) ) {
			return '';
		}

		return $val;
	}

	protected function get_presets_arg( $val ) {
		if ( empty( $val ) ) {
			return null;
		}

		return json_decode( $val, true );
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
			case 3:
				$classes[] = 'col-md-4'; break;
			case 4:
				$classes[] = 'col-md-3'; break;
			case 5:
				$classes[] = 'col-md-5_12'; break;
			case 6:
				$classes[] = 'col-md-2'; break;
			default:
				$classes[] = 'col-md-4';
		}
		switch ( $columns_tablet ) {
			case 1:
				$classes[] = 'col-sm-12'; break;
			case 2:
				$classes[] = 'col-sm-6'; break;
			case 3:
				$classes[] = 'col-sm-4'; break;
			case 4:
				$classes[] = 'col-sm-3'; break;
			case 5:
				$classes[] = 'col-sm-5_12'; break;
			case 6:
				$classes[] = 'col-sm-2'; break;
			default:
				$classes[] = 'col-sm-4';
		}
		switch ( $columns_mobile ) {
			case 1:
				$classes[] = 'col-xs-12'; break;
			case 2:
				$classes[] = 'col-xs-6'; break;
			case 3:
				$classes[] = 'col-xs-4'; break;
			case 4:
				$classes[] = 'col-xs-3'; break;
			case 5:
				$classes[] = 'col-xs-5_12'; break;
			case 6:
				$classes[] = 'col-xs-2'; break;
			default:
				$classes[] = 'col-xs-4';
		}
		return implode(' ', $classes);
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
		$skins = $this->get_presets_options();

		if (!isset($settings[ 'columns_tablet' ])) {
			$columns_tablet = 2;
		} else {
			$columns_tablet = $settings[ 'columns_tablet' ];
		}
		if (!isset($settings[ 'columns_mobile' ])) {
			$columns_mobile = 1;
		} else {
			$columns_mobile = $settings[ 'columns_mobile' ];
		}
		$columns_classes = $this->columns_classes( $settings[ 'columns' ], $columns_tablet, $columns_mobile );

		$qf_item_link  = __DIR__ . '/templates/parts/qf_item_link.php';
		$qf_item_link = apply_filters( 'thegem_quickfinders_item_link', $qf_item_link);
		$qf_item_link_theme = get_stylesheet_directory() . '/templates/quickfinders/qf_item_link.php';
		if (!empty($qf_item_link_theme) && file_exists($qf_item_link_theme)) {
			$qf_item_link = $qf_item_link_theme;
		}

		$qf_item_info  = __DIR__ . '/templates/parts/qf_item_info.php';
		$qf_item_info = apply_filters( 'thegem_quickfinders_item_info', $qf_item_info);
		$qf_item_info_theme = get_stylesheet_directory() . '/templates/quickfinders/qf_item_info.php';
		if (!empty($qf_item_info_theme) && file_exists($qf_item_info_theme)) {
			$qf_item_info = $qf_item_info_theme;
		}

		$qf_icon_image = __DIR__ . '/templates/parts/qf_icon_image.php';
		$qf_icon_image = apply_filters( 'thegem_quickfinders_item_image', $qf_icon_image);
		$qf_icon_image_theme = get_stylesheet_directory() . '/templates/quickfinders/qf_item_image.php';
		if (!empty($qf_icon_image_theme) && file_exists($qf_icon_image_theme)) {
			$qf_icon_image = $qf_icon_image_theme;
		}

		$this->add_render_attribute( 'qf_item_info', 'class', 'quickfinder-item-info' );
		$this->add_render_attribute( 'qf_icon_image', 'class', 'gem-qf-icon-image-wrapper' );

		$this->add_render_attribute( 'qf_svg_arrow_right', 'class', 'qf-svg-arrow-right' );
		$this->add_render_attribute( 'qf_svg_arrow_right', 'viewBox', '0 0 50 100' );
		$this->add_render_attribute( 'qf_svg_arrow_left', 'class', 'qf-svg-arrow-left' );
		$this->add_render_attribute( 'qf_svg_arrow_left', 'viewBox', '0 0 50 100' );

		if ( 'yes' === $settings['lazy'] ) {
			thegem_lazy_loading_enqueue();
			$this->add_render_attribute( 'qf_item', 'class', [
				'lazy-loading',
				( \Elementor\Plugin::$instance->editor->is_edit_mode() ? 'lazy-loading-not-hide' : '' )
				]
			);
			$this->add_render_attribute( 'qf_item_info', 'class', 'lazy-loading-item' );
			$this->add_render_attribute( 'qf_item_info', 'data-ll-effect', 'fading' );
			$this->add_render_attribute( 'qf_item_info', 'data-ll-item-delay', '200' );
			$this->add_render_attribute( 'qf_icon_image', 'class', 'lazy-loading-item' );
			$this->add_render_attribute( 'qf_icon_image', 'data-ll-effect', 'clip' );
			$this->add_render_attribute( 'qf_svg_arrow_right', 'class', 'lazy-loading-item' );
			$this->add_render_attribute( 'qf_svg_arrow_right', 'data-ll-effect', 'fading' );
			$this->add_render_attribute( 'qf_svg_arrow_right', 'data-ll-item-delay', '200' );
			$this->add_render_attribute( 'qf_svg_arrow_left', 'class', 'lazy-loading-item' );
			$this->add_render_attribute( 'qf_svg_arrow_left', 'data-ll-effect', 'fading' );
			$this->add_render_attribute( 'qf_svg_arrow_left', 'data-ll-item-delay', '200' );
		}

		$this->add_render_attribute( 'qf_item_inner', 'class', 'quickfinder-item-inner' );

		if(in_array( $settings['thegem_elementor_preset'], array('grid-box-solid') )) {
			$this->add_render_attribute( 'qf_item_inner', 'class', 'default-background' );
		}

		if(in_array( $settings['thegem_elementor_preset'], array('grid-box-outlined', 'iconed') )) {
			$this->add_render_attribute( 'qf_item_inner', 'class', 'bordered-box' );
		}
		
		if ( ! empty( $settings['icon_vertical_position'] ) ) {
			$this->add_render_attribute( 'qf_item_inner', 'class', $settings['icon_vertical_position'] );
		}
		if ( ! empty( $settings['icon_horizontal_position'] ) ) {
			$this->add_render_attribute( 'qf_item_inner', 'class', 'icon-horizontal-' . $settings['icon_horizontal_position'] );
		}
		if ( ! empty( $settings['icon_text_wrapping'] ) ) {
			$this->add_render_attribute( 'qf_item_inner', 'class', 'icon-wrapping-' . $settings['icon_text_wrapping'] );
		}
		if ( ! empty( $settings['button_position'] )) {
			$this->add_render_attribute( 'qf_item_inner', 'class', $settings['button_position'] );
		}

		/** Grid conditions */
		if ( in_array( $settings['thegem_elementor_preset'], array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ) ) ) {

			$this->add_render_attribute( 'qf_item_inner', 'class', 'lazy-loading-item' );
			$this->add_render_attribute( 'qf_item_inner', 'data-ll-effect', 'fading' );
			$this->add_render_attribute( 'qf_item_inner', 'data-ll-item-delay', '200' );

			if ( ! empty( $settings['thegem_elementor_preset'] ) ) {
				$this->add_render_attribute( 'main_wrap', [
					'class' => [ 'quickfinder', 'row', 'inline-row', 'quickfinder-style-' . esc_attr( $settings['thegem_elementor_preset'] ) ],
				] );
			}
			
			$this->add_render_attribute( 'qf_item', [
				'class' => [ 'quickfinder-item',  'inline-column',  $columns_classes ],
			] );

			if ( 'yes' === ( $settings['no_gaps'] ) )  {
				$this->add_render_attribute( 'qf_item', 'class', 'no-gap' );
			}

		}
		
		/** Vertical conditions */
		if ( in_array( $settings['thegem_elementor_preset'], array_keys( $this->filter_skins_by( $skins, 'group', 'vertical' ) ) ) ) {
			wp_enqueue_style( 'thegem-quickfinder-vertical' );
			if ( 'vertical-1' === $settings['thegem_elementor_preset'] || 'vertical-4' === $settings['thegem_elementor_preset'] ) {
				$preset_path = __DIR__ . '/templates/output-vertical-1-4.php';
				$preset_path_filtered = apply_filters( 'thegem_quickfinders_vertical_1_4', $preset_path);
				$preset_path_theme = get_stylesheet_directory() . '/templates/quickfinders/output-vertical-1-4.php';
			}
			if ( 'vertical-2' === $settings['thegem_elementor_preset'] || 'vertical-2-right-align' === $settings['thegem_elementor_preset'] ) {
				$preset_path = __DIR__ . '/templates/output-vertical-2-2r.php';
				$preset_path_filtered = apply_filters( 'thegem_quickfinders_vertical_2_2r', $preset_path);
				$preset_path_theme = get_stylesheet_directory() . '/templates/quickfinders/output-vertical-2-2r.php';
			}
			if ( 'vertical-3' === $settings['thegem_elementor_preset'] || 'vertical-3-right-align' === $settings['thegem_elementor_preset'] ) {
				$preset_path = __DIR__ . '/templates/output-vertical-3-3r.php';
				$preset_path_filtered = apply_filters( 'thegem_quickfinders_vertical_3_3r', $preset_path);
				$preset_path_theme = get_stylesheet_directory() . '/templates/quickfinders/output-vertical-3-3r.php';
			}
			if ( 'yes' === ( $settings['connector_disable_last_item'] ) ) : ?>
				<style>.elementor-element-<?php echo $this->get_id(); ?> .quickfinder-style-vertical .quickfinder-item:last-child .connector-container:first-child span{visibility: hidden;}</style>
			<?php endif;
		}

		/** Grid conditions */
		if ( in_array( $settings['thegem_elementor_preset'], array_keys( $this->filter_skins_by( $skins, 'group', 'grid' ) ) ) ) {
			$preset_path = __DIR__ . '/templates/output-grid.php';
			$preset_path_filtered = apply_filters( 'thegem_quickfinders_grid', $preset_path);
			$preset_path_theme = get_stylesheet_directory() . '/templates/quickfinders/output-grid.php';
		}

		if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
			include($preset_path_theme);
		} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
			include($preset_path_filtered);
		}

	}

	public function get_preset_data() {

		return array(
			/** Grid: Basic Centered */
			'grid-basic-centered' => array(
				'img_size' => ['size' => 60,'unit' => 'px'],
				'container_vertical_gaps' => ['size' => 42,'unit' => 'px'],
			),

			/** Grid: Basic Left Align */
			'grid-basic-left-align' => array(
				'container_align' => 'left',
				'container_padding' => ['top' => '50', 'right' => '20', 'bottom' => '50', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'icon_horizontal_position' => 'left',
				'icon_text_wrapping' => 'inline',
				'icon_margin' => ['top' => '10', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'img_size' => ['size' => 60,'unit' => 'px'],
			),

			/** Grid: Basic Right Align */
			'grid-basic-right-align' => array(
				'container_align' => 'left',
				'container_padding' => ['top' => '50', 'right' => '0', 'bottom' => '50', 'left' => '20', 'unit' => 'px', 'isLinked' => false],
				'icon_horizontal_position' => 'right',
				'icon_text_wrapping' => 'inline',
				'icon_margin' => ['top' => '10', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'img_size' => ['size' => 60,'unit' => 'px'],
			),

			/** Grid: Box Solid */
			'grid-box-solid' => array(
				'container_padding' => ['top' => '50', 'right' => '25', 'bottom' => '50', 'left' => '25', 'unit' => 'px', 'isLinked' => false],
				'img_size' => ['size' => 60,'unit' => 'px'],
			),

			/** Grid: Box Outlined */
			'grid-box-outlined' => array(
				'container_padding' => ['top' => '50', 'right' => '25', 'bottom' => '50', 'left' => '25', 'unit' => 'px', 'isLinked' => false],
				'img_size' => ['size' => 60,'unit' => 'px'],
			),

			/** Grid: Iconic Box */
			'iconed' => array(
				'icon_background_background' => 'classic',
				'icon_background_color' => '#FFFFFF',
				'container_border_grid_width' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px', 'isLinked' => true],
				'container_border_grid_border' => 'solid',
				'container_border_grid_color' => thegem_get_option('box_border_color'),
				'container_padding' => ['top' => '85', 'right' => '25', 'bottom' => '50', 'left' => '25', 'unit' => 'px', 'isLinked' => false],
				'icon_shape' => 'circle',
				'icon_border_width' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px', 'isLinked' => true],
				'img_size' => ['size' => 60,'unit' => 'px'],
			),

			/** Vertical: Bubbles Centered */
			'vertical-1' => array(
				'container_postition' => 'align-to-icon',
				'container_padding' => ['top' => '35', 'right' => '25', 'bottom' => '35', 'left' => '25', 'unit' => 'px', 'isLinked' => false],
				'icon_shape' => 'circle',
				'connector_color' => '#b6c6c9',
				'connector_length' => ['size' => 102,'unit' => 'px'],
				'icon_border_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px', 'isLinked' => true],
				'connector_weight' => ['size' => 1,'unit' => 'px'],
				'img_size' => ['size' => 128,'unit' => 'px'],
			),

			/** Vertical: Bubbles Left Align */
			'vertical-2' => array(
				'container_align' => 'left',
				'container_padding' => ['top' => '35', 'right' => '25', 'bottom' => '35', 'left' => '25', 'unit' => 'px', 'isLinked' => false],
				'icon_shape' => 'circle',
				'connector_color' => '#b6c6c9',
				'connector_length' => ['size' => 102,'unit' => 'px'],
				'icon_border_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px', 'isLinked' => true],
				'connector_weight' => ['size' => 1,'unit' => 'px'],
				'img_size' => ['size' => 128,'unit' => 'px'],
			),

			/** Vertical: Bubbles Right Align */
			'vertical-2-right-align' => array(
				'container_align' => 'right',
				'container_padding' => ['top' => '35', 'right' => '25', 'bottom' => '35', 'left' => '25', 'unit' => 'px', 'isLinked' => false],
				'icon_shape' => 'circle',
				'connector_color' => '#b6c6c9',
				'connector_length' => ['size' => 102,'unit' => 'px'],
				'icon_border_width' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px', 'isLinked' => true],
				'connector_weight' => ['size' => 1,'unit' => 'px'],
				'img_size' => ['size' => 128,'unit' => 'px'],
			),

			/** Vertical: Basic Left Align */
			'vertical-3' => array(
				'container_align' => 'left',
				'container_padding' => ['top' => '55', 'right' => '50', 'bottom' => '35', 'left' => '50', 'unit' => 'px', 'isLinked' => false],
				'connector_color' => '#b6c6c9',
				'connector_length' => ['size' => 102,'unit' => 'px'],
				'connector_weight' => ['size' => 1,'unit' => 'px'],
				'img_size' => ['size' => 128,'unit' => 'px'],
			),

			/** Vertical: Basic Right Align */
			'vertical-3-right-align' => array(
				'container_align' => 'right',
				'container_padding' => ['top' => '55', 'right' => '50', 'bottom' => '35', 'left' => '50', 'unit' => 'px', 'isLinked' => false],
				'connector_color' => '#b6c6c9',
				'connector_length' => ['size' => 102,'unit' => 'px'],
				'connector_weight' => ['size' => 1,'unit' => 'px'],
				'img_size' => ['size' => 128,'unit' => 'px'],
			),

			/** Vertical: Basic Centered */
			'vertical-4' => array(
				'container_postition' => 'align-to-icon',
				'container_padding' => ['top' => '55', 'right' => '50', 'bottom' => '35', 'left' => '50', 'unit' => 'px', 'isLinked' => false],
				'connector_color' => '#b6c6c9',
				'connector_length' => ['size' => 102,'unit' => 'px'],
				'connector_weight' => ['size' => 1,'unit' => 'px'],
				'img_size' => ['size' => 128,'unit' => 'px'],),
		);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new TheGem_QuickFinders() );
