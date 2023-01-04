<?php
namespace TheGem_Elementor\Widgets\BlogList;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Schemes;

use WP_Query;


if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Elementor widget for BlogList.
 */
class TheGem_Bloglist extends Widget_Base {

	/**
	 * Presets
	 * @access protected
	 * @var array $presets Array objects presets.
	 */

	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		if( ! defined( 'THEGEM_ELEMENTOR_WIDGET_BLOGLIST_DIR' ) ){
			define( 'THEGEM_ELEMENTOR_WIDGET_BLOGLIST_DIR', rtrim( __DIR__, ' /\\' ) );
		}

		if ( ! defined( 'THEGEM_ELEMENTOR_WIDGET_BLOGLIST_URL' ) ) {
			define( 'THEGEM_ELEMENTOR_WIDGET_BLOGLIST_URL', rtrim( plugin_dir_url( __FILE__ ), ' /\\' ) );
		}
		wp_register_style( 'thegem-bloglist', THEGEM_ELEMENTOR_WIDGET_BLOGLIST_URL . '/assets/css/thegem-bloglist.css', array( 'thegem-blog', 'thegem-animations' ), NULL );

	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-bloglist';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Blog List', 'thegem' );
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
		return [ 'thegem_blog' ];
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-bloglist',
				'thegem-button'];
		}
		return [ 'thegem-bloglist' ];
	}

	public function get_script_depends() {
		return [ 'thegem-items-animations', 'thegem-blog', 'thegem-blog-isotope' ];
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
			'default' => __( 'Classic', 'thegem' ),
			'styled_list1' => __( 'Styled List 1', 'thegem' ),
			'styled_list2' => __( 'Styled List 2', 'thegem' ),
			'compact' => __( 'Compact List 1', 'thegem' ),
			'compact-2' => __( 'Compact List 2', 'thegem' ),
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
		$out   = [ '0' => __( 'All', 'thegem' ) ];
		$terms = get_terms( [
			'taxonomy' => 'category',
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
			'section_layout',
			[
				'label' => __( 'Layout', 'thegem' ),
			]
		);

		$this->add_control(
			'thegem_elementor_preset',
			[
				'label' => __( 'Skin', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_blog',
			[
				'label' => __( 'Blog', 'thegem' ),
			]
		);

		$this->add_control(
			'select_blog_cat',
			[
				'label' => __( 'Select Blog Categories', 'thegem' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->select_blog_categories(),
				'frontend_available' => true,
				'label_block' => true,
			]
		);

		$this->add_control(
			'show_featured_image',
			[
				'label' => __( 'Show Featured Image', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => __( 'Show Title', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_description',
			[
				'label' => __( 'Show Description', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_date',
			[
				'label' => __( 'Show Date', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_categories',
			[
				'label' => __( 'Show Categories', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ]
				],
			]
		);

		$this->add_control(
			'show_author',
			[
				'label' => __( 'Show Author', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'thegem_elementor_preset!' => 'compact-2',
				],
			]
		);

		$this->add_control(
			'show_comments',
			[
				'label' => __( 'Show Comments', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_likes',
			[
				'label' => __( 'Show Likes', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_readmore_button',
			[
				'label' => __( 'Show "Read More" Button', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ]
				],
			]
		);

		$this->add_control(
			'readmore_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Read More', 'thegem'),
				'condition' => [
					'show_readmore_button' => 'yes',
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ]
				],
			]
		);

		$this->add_control(
			'readmore_button_icon',
			[
				'label' => __( 'Button Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_readmore_button' => 'yes',
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ]
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pagination',
			[
				'label' => __( 'Pagination', 'thegem' ),
			]
		);

		$this->add_control(
			'items_per_page',
			[
				'label' => __( 'Items per page', 'thegem' ),
				'description' => __( 'Use - 1 to show all', 'thegem' ),
				'label_block' => false,
				'type' => Controls_Manager::NUMBER,
				'default' => 8,
				'min' => - 1,
				'max' => 100,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => __( 'Pagination', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => __('Pagination Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'numbers',
				'options' => [
					'numbers' => __('Numbers', 'thegem'),
					'load-more-button' => __('Load More Button', 'thegem'),
					'infinite-scroll' => __('Infinite Scroll', 'thegem'),
				],
				'condition' => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'loadmore_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Load More', 'thegem'),
				'condition' => [
					'pagination_type' => 'load-more-button',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'loadmore_button_icon',
			[
				'label' => __( 'Button Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'pagination_type' => 'load-more-button',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'loadmore_button_stretch_fullwidth',
			[
				'label' => 'Stretch to Fullwidth',
				'default' => 'no',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'pagination_type' => 'load-more-button',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'loadmore_button_show_separator',
			[
				'label' => 'Show Separator',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
				'frontend_available' => true,
				'condition' => [
					'loadmore_button_stretch_fullwidth!' => 'yes',
					'pagination_type' => 'load-more-button',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social_sharing',
			[
				'label' => __( 'Social Sharing', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'default', 'styled_list1', 'styled_list2' ]
				],
			]
		);

		$this->add_control(
			'show_social_sharing',
			[
				'label' => __( 'Social Sharing', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sharing_icon',
			[
				'label' => __( 'Sharing Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_social_sharing' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_social_facebook',
			[
				'label' => __( 'Facebook', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_social_sharing' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_social_twitter',
			[
				'label' => __( 'Twitter', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_social_sharing' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_social_pinterest',
			[
				'label' => __( 'Pinterest', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_social_sharing' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_social_tumblr',
			[
				'label' => __( 'Tumblr', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_social_sharing' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_social_linkedin',
			[
				'label' => __( 'Linkedin', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_social_sharing' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_social_reddit',
			[
				'label' => __( 'Reddit', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'thegem' ),
				'label_off' => __( 'Hide', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_social_sharing' => 'yes'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_animations',
			[
				'label' => __( 'Animations', 'thegem' ),
			]
		);

		$this->add_control(
			'show_animation',
			[
				'label' => __( 'Lazy Loading Animation', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'no',
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
					'show_animation' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => __( 'Additional Options', 'thegem' ),
			]
		);

		$this->add_control(
			'ignore_sticky_posts',
			[
				'label' => __( 'Ignore Sticky Posts', 'thegem' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'thegem' ),
				'label_off' => __( 'Off', 'thegem' ),
				'return_value' => 'yes',
				'default' => 'yes',
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
		$this->general_styles( $control );

		/* Image Styles */
		$this->image_styles( $control );

		/* Image Container Styles */
		$this->image_container_styles( $control );

		/* Caption Styles */
		$this->caption_styles( $control );

		/* Caption Container Styles */
		$this->caption_container_styles( $control );

		/* Caption Container Styles Styled Presets */
		$this->image_container_styles_styled( $control );

		/* Pagination Numbers Styles */
		$this->pagination_numbers_styles( $control );

		/* Pagination Load More Styles */
		$this->pagination_loadmore_styles( $control );

		/* ReadMore Button Styles */
		$this->readmore_button_styles( $control );

		/* Sharing Styles */
		$this->sharing_styles( $control );

		/* Sticky Post Containers Styles */
		$this->sticky_post_containers_styles( $control );

		/* Sticky Post Captions Styles */
		$this->sticky_post_captions_styles( $control );


	}

	/**
	 * General Styles
	 * @access protected
	 */
	protected function general_styles( $control ) {

		$control->start_controls_section(
			'general_section',
			[
				'label' => __( 'General', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'section_bottom_spacing',
			[
				'label' => __( 'Bottom Gap', 'thegem' ),
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
					'{{WRAPPER}} .bloglist.blog-style-default article' => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2);padding-bottom: calc( {{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .bloglist.blog-style-styled_list1 article' => 'margin: 0 auto {{SIZE}}{{UNIT}} auto;',
					'{{WRAPPER}} .bloglist.blog-style-styled_list2 article' => 'margin: 0 auto {{SIZE}}{{UNIT}} auto;',
					'{{WRAPPER}} .bloglist.blog-style-compact article:not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bloglist.blog-style-compact-2 article' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'article_separator_weight',
			[
				'label' => __('Separator Weight', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'thegem_elementor_preset' => 'default',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist.blog-style-default article:not(.sticky)' => 'border-bottom-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'article_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => thegem_get_option('box_border_color'),
				'selectors' => [
					'{{WRAPPER}} .bloglist.blog-style-default article:not(.sticky)' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'thegem_elementor_preset' => 'default',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow_default',
				'label' => __('Common Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .item-post-container',
				'condition' => [
					'thegem_elementor_preset' => 'default',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow_styled_list1',
				'label' => __('Common Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky)',
				'condition' => [
					'thegem_elementor_preset' => 'styled_list1',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Image Styles
	 * @access protected
	 */
	protected function image_styles( $control ) {

		$control->start_controls_section(
			'image_style_section',
			[
				'label' => __( 'Image Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_featured_image' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'image_size_default',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 1170,
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
					'{{WRAPPER}} .bloglist.blog-style-default .post-image img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
					'{{WRAPPER}} .bloglist.blog-style-default .post-image a:hover::before' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'default'],
				],
			]
		);
		$control->add_responsive_control(
			'image_size_compact1',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 185,
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
					'size' => 183,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist.blog-style-compact .gem-compact-item-image img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
					'{{WRAPPER}} .bloglist.blog-style-compact a:hover::before' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
					'{{WRAPPER}} .bloglist.blog-style-compact .gem-compact-item-left' => 'min-width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bloglist.blog-style-compact .gem-compact-item-right' => 'max-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bloglist.blog-style-compact .full-meta .gem-compact-item-content' => 'max-height: calc({{SIZE}}{{UNIT}} / 1.5);',
					'{{WRAPPER}} .bloglist.blog-style-compact .no-meta .gem-compact-item-content' => 'max-height: calc({{SIZE}}{{UNIT}} / 1.3);',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'compact' ]
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
					'{{WRAPPER}} .bloglist .type-post .post-img a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bloglist .type-post .post-img a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a img',
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
					],
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'styled_list1', 'styled_list2' ]
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a',
			]
		);

		$control->start_controls_tabs('image_tabs');
		$control->start_controls_tab('image_tabs_normal', ['label' => __('Normal', 'thegem'),]);

		$control->add_responsive_control(
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
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img' => 'opacity: calc({{SIZE}}/100);',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_normal',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a img',
			]
		);

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
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img img' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$control->end_controls_tab();
		$control->start_controls_tab('image_tabs_hover', ['label' => __('Hover', 'thegem'),]);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_hover_overlay',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'toggle' => true,
				'fields_options' => [
					'background' => [
						'label' => _x('Overlay Type', 'Background Control', 'thegem'),
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(255, 255, 255, 0.8)',
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post .post-img a:hover::before' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post .post-img a:hover::before' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
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
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a:hover img',
			]
		);

		$control->add_control(
			'image_hover_blend_mode',
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
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img:hover' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$control->add_control(
			'image_icon_header',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'image_icon_show',
			[
				'label' => __('Show', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$control->add_responsive_control(
			'image_icon_color',
			[
				'label' => __('Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'image_icon_show' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'image_icon_background_color',
			[
				'label' => __('Icon Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#3c3950',
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a:after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'image_icon_show' => 'yes',
				],
			]
		);


		$control->add_responsive_control(
			'image_icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
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
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a:after' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'image_icon_show' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'image_icon_box_size',
			[
				'label' => __('Icon Box Size', 'thegem'),
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
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a:after' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2)',
				],
				'condition' => [
					'image_icon_show' => 'yes',
				],
			]
		);

		$control->add_control(
			'image_icon_rotate',
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
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a:after' => 'transform: rotate({{SIZE}}deg); -webkit-transform: rotate({{SIZE}}deg);',
				],
				'condition' => [
					'image_icon_show' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'image_outer_border_heading_1',
			[
				'label' => __( 'Outer Border 1', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
				],
			]
		);

		$control->add_responsive_control(
			'image_outer_border_radius_1',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .blog-style-styled_list1 .post-img a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .blog-style-styled_list2 .post-img a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_outer_border_1',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'color' => [
						'default' => '#ffffff',
					],
				],
				'selector' => '{{WRAPPER}} .blog-style-styled_list1 .post-img a, {{WRAPPER}} .blog-style-styled_list2 .post-img a',
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
				],
			]
		);

		$control->add_control(
			'image_outer_border_heading_2',
			[
				'label' => __( 'Outer Border 2', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
				],
			]
		);

		$control->add_responsive_control(
			'image_outer_border_radius_2',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .blog-style-styled_list1 .post-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .blog-style-styled_list2 .post-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_outer_border_2',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
					],
				],
				'selector' => '{{WRAPPER}} .blog-style-styled_list1 .post-img, {{WRAPPER}} .blog-style-styled_list2 .post-img',
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Image Container Styles
	 * @access protected
	 */
	protected function image_container_styles( $control ) {

		$control->start_controls_section(
			'image_container_style_section',
			[
				'label' => __( 'Image Container Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'thegem_elementor_preset!' => [ 'styled_list1', 'compact-2' ],
					'show_featured_image' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_container_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img',
			]
		);

		$control->remove_control( 'image_container_background_image' );

		$control->add_responsive_control(
			'image_container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .blog-style-default .type-post:not(.sticky) .item-post-container' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_container_border',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
					],
				],
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img',
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
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'default', 'compact', 'styled_list2' ],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img',
				'condition' => [
					'thegem_elementor_preset!' => [ 'default', 'styled_list1', 'styled_list2' ],
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Image Container Styles
	 * @access protected
	 */
	protected function image_container_styles_styled( $control ) {

		$control->start_controls_section(
			'image_container_style_section_styled',
			[
				'label' => __( 'Image Container Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1' ],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_container_background_styled',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky)',
			]
		);

		$control->remove_control('image_container_background_styled_image');

		$control->add_responsive_control(
			'image_container_radius_styled',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'allowed_dimensions' => ['top', 'left'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_container_border_styled',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
					],
				],
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky)',
			]
		);

		$control->add_responsive_control(
			'image_container_padding_styled',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-img a img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list2' ],
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Caption Styles
	 * @access protected
	 */
	protected function caption_styles( $control ) {

		$control->start_controls_section(
			'caption_style_section',
			[
				'label' => __( 'Caption Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_control(
			'caption_title_heading',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'caption_title_tabs' );
		$control->start_controls_tab(
			'caption_title_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);


		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_title_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-title *',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_title_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-title *' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'caption_title_tab_hover',
			[ 
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_title_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-title *',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_title_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-title *' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'caption_description_heading',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'caption_description_tabs' );
		$control->start_controls_tab(
			'caption_description_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);


		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_description_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .summary',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_description_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .summary' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'caption_description_tab_hover', 
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_description_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .summary',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_description_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .summary' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'caption_date_heading',
			[
				'label' => __( 'Date', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'caption_date_tabs' );
		$control->start_controls_tab(
			'caption_date_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'show_date' => 'yes',
				],
			] 
		);


		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_date_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .entry-title-date, {{WRAPPER}} .bloglist .type-post:not(.sticky) .post-date , {{WRAPPER}} .bloglist .type-post:not(.sticky) .gem-news-item-date',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_date_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .entry-title-date, {{WRAPPER}} .bloglist .type-post:not(.sticky) .post-date , {{WRAPPER}} .bloglist .type-post:not(.sticky) .gem-news-item-date' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'caption_date_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'show_date' => 'yes',
				],
			]
			);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_date_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .entry-title-date, {{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-date, {{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .gem-news-item-date',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_date_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .entry-title-date, {{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-date, {{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .gem-news-item-date' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'caption_time_heading',
			[
				'label' => __( 'Time', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'caption_time_tabs' );
		$control->start_controls_tab(
			'caption_time_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);


		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_time_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-time',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_time_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-time' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'caption_time_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_time_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-time',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_time_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-time' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'caption_categories_heading',
			[
				'label' => __( 'Categories', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'caption_categories_tabs' );
		$control->start_controls_tab(
			'caption_categories_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_categories_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-meta-categories a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_categories_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-meta-categories a' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'caption_categories_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_categories_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-meta-categories a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_categories_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-meta-categories a' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$this->add_control(
			'caption_categories_in_text',
			[
				'label' => __('"In" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('in ', 'thegem'),
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_author_heading',
			[
				'label' => __( 'Author', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'caption_author_tabs' );
		$control->start_controls_tab(
			'caption_author_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			] 
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_author_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-meta-author',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_author_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-meta-author' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'caption_author_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_author_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-meta-author',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_author_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .post-meta-author' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$this->add_control(
			'caption_author_by_text',
			[
				'label' => __('"By" Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('By', 'thegem'),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_delimiter_heading',
			[
				'label' => __( 'Delimiter', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);
		$control->start_controls_tabs( 'caption_delimiter_tabs' );
		$control->start_controls_tab(
			'caption_delimiter_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);


		$control->add_control(
			'caption_delimiter_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .sep' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'caption_delimiter_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);

		$control->add_control(
			'caption_delimiter_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .sep' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'caption_likes_heading',
			[
				'label' => __( 'Likes', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->add_control(
			'likes_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->start_controls_tabs( 'likes_icon_tab' );
		$control->start_controls_tab(
			'likes_icon_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);

		$control->add_responsive_control(
			'likes_icon_color',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .zilla-likes::before' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-meta-likes i' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-meta-likes svg' => 'fill: {{VALUE}} !important;',
				],
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_likes_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .zilla-likes-count',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_likes_count_color',
			[
				'label' => __('Text  Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .zilla-likes-count' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'likes_icon_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ), 
			]
		);

		$control->add_responsive_control(
			'likes_icon_color_hover',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#393d50',
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .zilla-likes::before:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-meta-likes i:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .post-meta-likes svg:hover' => 'fill: {{VALUE}} !important;',
				],
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_likes_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .zilla-likes-count:hover',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_likes_count_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#393d50',
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .zilla-likes-count:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_control(
			'caption_comments_heading',
			[
				'label' => __( 'Comments', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$control->add_control(
			'comments_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$control->start_controls_tabs( 'comments_icon_tab' );
		$control->start_controls_tab(
			'comments_icon_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);

		$control->add_responsive_control(
			'comments_icon_color',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#99a9b5',
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .comments-link .elementor-icon i' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_comments_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .comments-link a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_comments_count_color',
			[
				'label' => __('Text  Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .comments-link a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'image_icon_show' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'comments_icon_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ), 
			]
		);

		$control->add_responsive_control(
			'comments_icon_color_hover',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#00bcd4',
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .comments-link .elementor-icon i:hover' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'caption_comments_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .comments-link a:hover',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_comments_count_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#00bcd4',
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .comments-link a:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'image_icon_show' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Caption Container Styles
	 * @access protected
	 */
	protected function caption_container_styles( $control ) {

		$control->start_controls_section(
			'caption_container_style_section',
			[
				'label' => __( 'Caption Container Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .bloglist .type-post .caption-container .post-title' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .bloglist .type-post .caption-container .post-text, {{WRAPPER}} .bloglist .type-post .caption-container .post-content, {{WRAPPER}} .bloglist .type-post .caption-container .gem-news_title-excerpt.summary' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .bloglist .type-post .caption-container .post-footer' => 'text-align: {{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_left_spacing',
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
					'{{WRAPPER}} .bloglist.blog-style-compact .gem-compact-item-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bloglist.blog-style-compact-2 .gem-news-item-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'compact', 'compact-2' ],
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_border_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .item-post-container' => 'border-bottom-left-radius: {{LEFT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'caption_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'default'    => [
					'top'      => '10',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => false,
				],
				'mobile_default'    => [
					'top'      => '30',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => false,
				],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'caption_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container',
				'condition' => [
					'thegem_elementor_preset!' => [ 'default', 'styled_list1' ],
				],
			]
		);

		$control->start_controls_tabs( 'caption_container_tabs' );
		$control->start_controls_tab( 'caption_container_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'caption_container_background',
				'label' => __('Background', 'thegem'),
				'types' => ['classic', 'gradient'],
				'default' => 'classic',
				'fields_options' => [
					'background' => [
						'label' => _x('Background ', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container' => 'background: {{VALUE}} !important;',
							'{{WRAPPER}} .bloglist.blog-style-compact article .gem-compact-item-content:after' => 'box-shadow: 0 5px 15px 15px {{VALUE}} !important;',
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .wrap-style' => 'fill: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				]

			]
		);

		$control->remove_control('caption_container_background_image');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'caption_container_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container',
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container' => 'border-color: {{VALUE}} !important;',
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .wrap-style' => 'stroke: {{VALUE}} !important;',
						],
					],
					'width' => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .wrap-style' => 'stroke-width: {{TOP}}{{UNIT}} !important;',
						],
					],
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'caption_container_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'caption_container_background_hover',
				'label' => __('Background', 'thegem'),
				'types' => ['classic', 'gradient'],
				'default' => 'classic',
				'fields_options' => [
					'background' => [
						'label' => _x('Background ', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover' => 'background: {{VALUE}} !important;',
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .wrap-style' => 'fill: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				]

			]
		);

		$control->remove_control('caption_container_background_hover_image');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'caption_container_border_hover',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container',
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover' => 'border-color: {{VALUE}} !important;',
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .wrap-style' => 'stroke: {{VALUE}} !important;',
						],
					],
					'width' => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
							'{{WRAPPER}} .bloglist .type-post:not(.sticky) .caption-container:hover .wrap-style' => 'stroke-width: {{TOP}}{{UNIT}} !important;',
						],
					],
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->add_control(
			'title_spacing_heading',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'title_top_spacing',
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
				'default' => [
					'size' => 50,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist:not(.blog-style-compact-2) .type-post:not(.sticky) .post-title' => 'margin-top: calc({{SIZE}}{{UNIT}} - 50px);',
					'{{WRAPPER}} .bloglist.blog-style-compact-2 .type-post:not(.sticky) .post-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

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
				'default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist:not(.blog-style-compact-2) .type-post:not(.sticky) .post-title' => 'margin-bottom: calc({{SIZE}}{{UNIT}} - 20px);',
					'{{WRAPPER}} .bloglist.blog-style-compact-2 .type-post:not(.sticky) .post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'description_bottom_spacing_heading',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'description_bottom_spacing',
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
					'size' => 40,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post:not(.sticky) .summary' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'caption_container_bubbles_show',
			[
				'label' => __('Bubble', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ]
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Pagination Numbers Styles
	 * @access protected
	 */
	protected function pagination_numbers_styles( $control ) {

		$control->start_controls_section(
			'pagination_numbers_style_section',
			[
				'label' => __( 'Pagination Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pagination_type' => 'numbers',
				],
			]
		);

		$control->add_responsive_control(
			'numbers_position',
			[
				'label' => __('Position', 'thegem'),
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
			]
		);

		$control->add_responsive_control(
			'pagination_numbers_top_spacing',
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
					'{{WRAPPER}} .bloglist-pagination .gem-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_control(
			'pagination_numbers_heading',
			[
				'label' => __( 'Numbers', 'thegem' ),
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
					'{{WRAPPER}} .gem-pagination .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .gem-pagination .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'pagination_numbers_tabs' );
		$control->start_controls_tab( 'pagination_numbers_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_responsive_control(
			'pagination_numbers_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination a, {{WRAPPER}} .gem-pagination .current' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_numbers_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-pagination a',
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'pagination_numbers_typography',
				'selector' => '{{WRAPPER}} .gem-pagination a, {{WRAPPER}} .gem-pagination span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'pagination_numbers_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination a' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'pagination_numbers_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_responsive_control(
			'pagination_numbers_background_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination a:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_numbers_border_hover',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-pagination a:hover',
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'pagination_numbers_typography_hover',
				'selector' => '{{WRAPPER}} .gem-pagination a:hover',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'pagination_numbers_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'pagination_numbers_tab_active', [ 'label' => __( 'Active', 'thegem' ), ] );

		$control->add_responsive_control(
			'pagination_numbers_background_color_active',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination .current' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_numbers_border_active',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-pagination .current',
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'pagination_numbers_typography_active',
				'selector' => '{{WRAPPER}} .gem-pagination .current',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'pagination_numbers_color_active',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination .current' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();


		$control->add_control(
			'pagination_arrows_heading',
			[
				'label' => __( 'Arrows', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'pagination_left_icon',
			[
				'label' => __( 'Left Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'pagination_left_icon_other',
			]
		);

		$control->add_control(
			'pagination_right_icon',
			[
				'label' => __( 'Right Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'pagination_right_icon_other',
			]
		);

		$control->add_responsive_control(
			'pagination_arrows_icon_size',
			[
				'label' => __( 'Icon Size', 'thegem' ),
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
					'size' => 24,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-pagination .prev i, {{WRAPPER}} .gem-pagination .next i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-pagination .prev svg, {{WRAPPER}} .gem-pagination .next svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
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
					'{{WRAPPER}} .gem-pagination .prev, {{WRAPPER}} .gem-pagination .next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .gem-pagination .page-numbers.prev, {{WRAPPER}} .gem-pagination .page-numbers.next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'pagination_arrows_tabs' );
		$control->start_controls_tab( 'pagination_arrows_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );


		$control->add_responsive_control(
			'pagination_arrows_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination .prev, {{WRAPPER}} .gem-pagination .next' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_arrows_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-pagination .prev, {{WRAPPER}} .gem-pagination .next',
			]
		);

		$control->add_responsive_control(
			'pagination_arrows_color',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination .prev i, {{WRAPPER}} .gem-pagination .next i' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .gem-pagination .prev svg, {{WRAPPER}} .gem-pagination .next svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'pagination_arrows_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_responsive_control(
			'pagination_arrows_background_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination .prev:hover, {{WRAPPER}} .gem-pagination .next:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_arrows_border_hover',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-pagination .prev:hover, {{WRAPPER}} .gem-pagination .next:hover',
			]
		);

		$control->add_responsive_control(
			'pagination_arrows_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .gem-pagination .prev:hover i, {{WRAPPER}} .gem-pagination .next:hover i' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Pagination Styles
	 * @access protected
	 */
	protected function pagination_loadmore_styles( $control ) {

		$control->start_controls_section(
			'pagination_loadmore_style_section',
			[
				'label' => __( '"Load More" Button Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pagination_type' => 'load-more-button',
				],
			]
		);

		$control->add_responsive_control(
			'loadmore_button_position',
			[
				'label' => __('Position', 'thegem'),
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
			]
		);

		$control->add_responsive_control(
			'loadmore_top_spacing',
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
					'{{WRAPPER}} .blog-load-more' => 'margin-top: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$control->add_control(
			'loadmore_button_heading',
			[
				'label' => __( 'Button', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'loadmore_button_type',
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
			'loadmore_button_size',
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
				],
			]
		);

		$control->add_responsive_control(
			'loadmore_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'loadmore_button_border_type',
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
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'loadmore_button_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'loadmore_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-inner-wrapper-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'loadmore_button_tabs' );
		$control->start_controls_tab( 'loadmore_button_tab_normal', [ 'label' => __( 'Normal', 'thegem' ),'condition' => [ 'pagination_type' => 'load-more-button'], ] );

		$control->add_control(
			'loadmore_button_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'loadmore_button_typography',
				'selector' => '{{WRAPPER}} .blog-load-more .gem-button-container .gem-button',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'loadmore_button_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'loadmore_button_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'loadmore_button_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .blog-load-more .gem-button-container .gem-button',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'loadmore_button_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'pagination_type' => 'load-more-button',
				],
			]
		);

		$control->add_control(
			'loadmore_button_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button:hover .gem-text-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button:hover .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button:hover .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'loadmore_button_typography_hover',
				'selector' => '{{WRAPPER}} .blog-load-more:hover .gem-button-container .gem-button span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'loadmore_button_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .blog-load-more:hover .gem-button-container .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'loadmore_button_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .blog-load-more:hover .gem-button-container .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'loadmore_button_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .blog-load-more:hover .gem-button-container .gem-button',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_responsive_control(
			'loadmore_button_icon_align',
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
					'{{WRAPPER}} .blog-load-more .gem-inner-wrapper-btn' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'loadmore_button_icon_spacing_right',
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
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-left:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'loadmore_button_icon_align' => ['right'],
				],
			]
		);

		$control->add_responsive_control(
			'loadmore_button_icon_spacing_left',
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
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-right:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'loadmore_button_icon_align' => ['left'],
				],
			]
		);

		$control->add_control(
			'loadmore_button_separator_heading',
			[
				'label' => __( 'Separator', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'loadmore_button_show_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'loadmore_button_separator_style_active',
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
				'condition' => [
					'loadmore_button_show_separator' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'loadmore_separator_size',
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
					'{{WRAPPER}} .blog-load-more .gem-button-separator .gem-button-separator-line' => 'width:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'loadmore_button_show_separator' => 'yes',
				],
			]
		);

		$control->add_control(
			'loadmore_separator_weight_single',
			[
				'label' => __('Weight, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'loadmore_button_separator_style_active' => ['single'],
					'loadmore_button_show_separator' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-separator .gem-button-separator-line' => 'border-top-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'loadmore_separator_weight_soft_double',
			[
				'label' => __('Weight, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'loadmore_button_separator_style_active' => ['soft-double'],
					'loadmore_button_show_separator' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,	
					],
				],
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-separator .gem-button-separator-line' => 'border-top-width:{{SIZE}}{{UNIT}}; border-bottom-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'loadmore_separator_weight_strong_double',
			[
				'label' => __('Weight, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'loadmore_button_separator_style_active' => ['strong-double'],
					'loadmore_button_show_separator' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-separator .gem-button-separator-line' => 'border-top-width:{{SIZE}}{{UNIT}}; border-bottom-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Height Strong Double & Soft
		$control->add_responsive_control(
			'loadmore_separator_double_height',
			[
				'label' => __('Height, px', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'loadmore_button_separator_style_active' =>
						[
							'strong-double',
							'soft-double',
						],
					'loadmore_button_show_separator' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .blog-load-more .gem-button-separator .gem-button-separator-holder .gem-button-separator-line' => 'height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		// Spacing Button
		$control->add_responsive_control(
			'loadmore_separator_spacing',
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
					'{{WRAPPER}} .blog-load-more .gem-button-container .gem-button-separator a' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'loadmore_button_show_separator' => 'yes',
				],
			]
		);



		// Color
		$this->add_control(
			'loadmore_color_square_border',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [],
				'condition' => [
					'loadmore_button_show_separator' => 'yes',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Readmore  Button Styles
	 * @access protected
	 */
	protected function readmore_button_styles( $control ) {

		$control->start_controls_section(
			'readmore_button_section',
			[
				'label' => __( '"Read More" Button Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_readmore_button' => 'yes',
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ]
				],
			]
		);

		$control->add_control(
			'readmore_button_type',
			[
				'label' => __('Button Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'outline',
				'options' => [
					'flat' => __('Flat', 'thegem'),
					'outline' => __('Outline', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'readmore_button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'tiny',
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
			'readmore_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'readmore_button_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'none'   => __('None', 'thegem'),
					'solid'  => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'readmore_button_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'readmore_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-inner-wrapper-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'readmore_button_tabs' );
		$control->start_controls_tab(
			'readmore_button_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'show_readmore_button' => 'yes',
				],
			]
		);

		$control->add_control(
			'readmore_button_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'readmore_button_typography',
				'selector' => '{{WRAPPER}} .post-read-more .gem-button-container .gem-button',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'readmore_button_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'readmore_button_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'readmore_button_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .post-read-more .gem-button-container .gem-button',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'readmore_button_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'show_readmore_button' => 'yes',
				],
			]
		);

		$control->add_control(
			'readmore_button_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button:hover span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button:hover .gem-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-button:hover .gem-button-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'readmore_button_typography_hover',
				'selector' => '{{WRAPPER}} .post-read-more:hover .gem-button-container .gem-button span',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$control->add_responsive_control(
			'readmore_button_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .post-read-more:hover .gem-button-container .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'readmore_button_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .post-read-more:hover .gem-button-container .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'readmore_button_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .post-read-more:hover .gem-button-container .gem-button',
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_responsive_control(
			'readmore_button_icon_align',
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
				'default' => 'left',
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .post-read-more .gem-inner-wrapper-btn' => '{{VALUE}}',
				],
			]
		);

		$control->add_responsive_control(
			'readmore_button_icon_spacing_right',
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
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-left:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'readmore_button_icon_align' => ['right'],
				],
			]
		);

		$control->add_responsive_control(
			'readmore_button_icon_spacing_left',
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
					'{{WRAPPER}} .post-read-more .gem-button-container .gem-inner-wrapper-btn .gem-button-icon' => 'margin-right:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'readmore_button_icon_align' => ['left'],
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Sharing Styles
	 * @access protected
	 */
	protected function sharing_styles( $control ) {

		$control->start_controls_section(
			'sharing_style_section',
			[
				'label' => __( 'Sharing Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_social_sharing' => 'yes',
				],
			]
		);

		$control->add_control(
			'sharing_icon_heading',
			[
				'label' => __( 'Sharing Icon', 'thegem' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$control->add_responsive_control(
			'social_icons_box_spacing',
			[
				'label' => __( 'Spacing', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'default' => [
					'size' => 13,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button-container .gem-button' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'sharing_icon_size',
			[
				'label' => __( 'Icon Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'sharing_icon_box_size',
			[
				'label' => __( 'Box Size', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'sharing_icon_box_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'default' => [
					'top' => 3,
					'right' => 3,
					'bottom' => 3,
					'left' => 3,
					'unit' => 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'sharing_icon_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'none'   => __('None', 'thegem'),
					'solid'  => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'sharing_icon_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'sharing_icon_tabs' );
		$control->start_controls_tab( 'sharing_icon_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sharing_icon_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bloglist .post-footer-sharing .gem-button',
			]
		);

		$control->remove_control('sharing_icon_background_image');


		$control->add_responsive_control(
			'sharing_icon_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'sharing_icon_color',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button .elementor-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'sharing_icon_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sharing_icon_background_hover',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bloglist .post-footer-sharing .gem-button:hover',
			]
		);

		$control->remove_control('sharing_icon_background_hover_image');

		$control->add_responsive_control(
			'sharing_icon_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'sharing_icon_color_hover',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bloglist .post-footer-sharing .gem-button:hover .elementor-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'social_icons_heading',
			[
				'label' => __( 'Social Icons', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'social_icons_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'default' => 'classic',
				'fields_options' => [
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .bloglist .sharing-popup' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .bloglist .sharing-popup .sharing-styled-arrow' => 'fill: {{VALUE}};',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .bloglist .sharing-popup' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				],
			]
		);

		$control->remove_control('social_icons_background_image');

		$control->start_controls_tabs( 'social_icons_tabs' );
		$control->start_controls_tab( 'social_icons_tab_normal', [ 'label' => __( 'Normal', 'thegem' ), ] );

		$control->add_control(
			'social_icons_icon_color',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#99a9b5',
				'selectors' => [
					'{{WRAPPER}} .bloglist .sharing-popup .socials-sharing a.socials-item' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab( 'social_icons_tab_hover', [ 'label' => __( 'Hover', 'thegem' ), ] );

		$control->add_control(
			'social_icons_icon_color_hover',
			[
				'label' => __( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bloglist .sharing-popup .socials-colored-hover a:hover .socials-item-icon' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Sticky Post Containers Styles
	 * @access protected
	 */
	protected function sticky_post_containers_styles( $control ) {

		$control->start_controls_section(
			'sticky_post_containers_style_section',
			[
				'label' => __( 'Sticky Post Containers Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ignore_sticky_posts!' => 'yes',
				],
			]
		);

		$control->add_control(
			'sticky_general_container_heading',
			[
				'label' => __( 'General Container', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset!' => 'styled_list1',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sticky_general_container_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bloglist.blog-style-default .type-post.sticky',
				'condition' => [
					'thegem_elementor_preset' => 'default',
				],
			]
		);

		$control->remove_control('sticky_general_container_background_image');

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sticky_general_container_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist.blog-style-default .type-post.sticky, {{WRAPPER}} .bloglist.blog-style-default .type-post.sticky:after',
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
						'selectors' => [
							'{{WRAPPER}} .bloglist.blog-style-default .type-post.sticky' => 'border-color: {{VALUE}} !important;',
							'{{WRAPPER}} .bloglist.blog-style-default .type-post.sticky:after' => 'border-color: {{VALUE}} !important;',
						],
					],
					'width' => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						],
						'selectors' => [
							'{{WRAPPER}} .bloglist.blog-style-default .type-post.sticky' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
							'{{WRAPPER}} .bloglist.blog-style-default .type-post.sticky:after' => 'border-width: 0{{UNIT}} 0{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;bottom: calc(-75px - {{BOTTOM}}{{UNIT}});left: -{{BOTTOM}}{{UNIT}};',
						],
					],
				],
				'condition' => [
					'thegem_elementor_preset' => 'default',
				],
			]
		);
		
		$control->add_responsive_control(
			'sticky_general_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist.blog-style-default .type-post.sticky' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => 'default',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'sticky_general_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist.blog-style-default .type-post.sticky',
				'condition' => [
					'thegem_elementor_preset' => 'default',
				],
			]
		);
		
		$control->add_control(
			'sticky_image_container_heading',
			[
				'label' => __( 'Image Container', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',	
			]
		);
		
		$control->remove_control('sticky_image_container_background_image');

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sticky_image_container_background_styled',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky',
				'condition' => [
					'thegem_elementor_preset' => 'styled_list1',
				],
			]
		);

		$control->remove_control('sticky_image_container_background_styled_image');

		$control->add_responsive_control(
			'sticky_image_container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .post-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .blog-style-default .type-post.sticky .item-post-container' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset!' => 'styled_list1',
				],
			]
		);
		
		$control->add_responsive_control(
			'sticky_image_container_radius_styled',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'allowed_dimensions' => ['top', 'left'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => 'styled_list1',
				],
			]
		);


		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sticky_image_container_border',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
					],
				],
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .post-img',
				'condition' => [
					'thegem_elementor_preset!' => 'styled_list1',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sticky_image_container_border_styled',
				'label' => __('Border', 'thegem'),
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
					],
				],
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky',
				'condition' => [
					'thegem_elementor_preset' => 'styled_list1',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_image_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .post-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'default', 'compact', 'styled_list2' ],
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'sticky_image_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .post-img',
				'condition' => [
					'thegem_elementor_preset!' => [ 'default', 'styled_list1', 'styled_list2' ],
				],
			]
		);

		$control->add_control(
			'sticky_caption_container_heading',
			[
				'label' => __( 'Caption Container', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sticky_caption_container_background',
				'label' => __('Background', 'thegem'),
				'types' => ['classic', 'gradient'],
				'default' => 'classic',
				'fields_options' => [
					'background' => [
						'label' => _x('Background ', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post.sticky .caption-container' => 'background: {{VALUE}} !important;',
							'{{WRAPPER}} .bloglist .type-post.sticky .wrap-style' => 'fill: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post.sticky .caption-container' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				]
			]
		);

		$control->remove_control('sticky_caption_container_background_image');

		$control->add_responsive_control(
			'sticky_caption_container_border_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .caption-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sticky_caption_container_border',
				'label' => __('Border', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .caption-container',
				'fields_options' => [
					'color' => [
						'default' => thegem_get_option('box_border_color'),
						'selectors' => [
							'{{WRAPPER}} .bloglist .type-post.sticky .caption-container' => 'border-color: {{VALUE}} !important;',
							'{{WRAPPER}} .bloglist .type-post.sticky .wrap-style' => 'stroke: {{VALUE}} !important;',
						],
					],
				]
			]
		);

		$control->add_responsive_control(
			'sticky_caption_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .caption-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'sticky_caption_container_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .caption-container',
			]
		);

		$control->add_control(
			'sticky_label_heading',
			[
				'label' => __( 'Label Style', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sticky_label_background_color',
				'label' => __( 'Background Type', 'thegem' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bloglist article.sticky .sticky-label::after, {{WRAPPER}} .bloglist article.sticky .sticky-label::before, {{WRAPPER}} .bloglist article.sticky .sticky-label',
			]
		);

		$control->remove_control('sticky_label_background_color_image');

		$control->add_control(
			'sticky_label_icon',
			[
				'label' => __( 'Icon', 'thegem' ),
				'type' => Controls_Manager::ICONS,
			]
		);

		$control->add_responsive_control(
			'sticky_label_icon_color',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .bloglist .sticky-label .elementor-icon i' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Sticky Post Captions Styles
	 * @access protected
	 */
	protected function sticky_post_captions_styles( $control ) {

		$control->start_controls_section(
			'sticky_post_captions_style_section',
			[
				'label' => __( 'Sticky Post Captions Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ignore_sticky_posts!' => 'yes',
				],
			]
		);

		$control->add_control(
			'sticky_post_caption_title_heading',
			[
				'label' => __( 'Title', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'sticky_post_caption_title_tabs' );
		$control->start_controls_tab(
			'sticky_post_caption_title_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);


		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_title_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .entry-title span.light, {{WRAPPER}} .bloglist .type-post.sticky .entry-title span.light a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_title_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .entry-title span.light, {{WRAPPER}} .bloglist .type-post.sticky .entry-title span.light a' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'sticky_post_caption_title_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_title_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .entry-title span.light, {{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .entry-title span.light a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_title_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .entry-title span.light, {{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .entry-title span.light a ' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'sticky_post_caption_description_heading',
			[
				'label' => __( 'Description', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'sticky_post_caption_description_tabs' );
		$control->start_controls_tab(
			'sticky_post_caption_description_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);


		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_description_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .summary',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_description_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .summary' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'sticky_post_caption_description_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_description_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .summary',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_description_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .summary' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'sticky_post_caption_date_heading',
			[
				'label' => __( 'Date', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'sticky_post_caption_date_tabs' );
		$control->start_controls_tab(
			'sticky_post_caption_date_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);


		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_date_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .entry-title-date, {{WRAPPER}} .bloglist .type-post.sticky .post-date , {{WRAPPER}} .bloglist .type-post.sticky .gem-news-item-date',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_date_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .entry-title-date, {{WRAPPER}} .bloglist .type-post.sticky .post-date , {{WRAPPER}} .bloglist .type-post.sticky .gem-news-item-date' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'sticky_post_caption_date_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_date_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .entry-title-date, {{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .post-date, {{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .gem-news-item-date',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_date_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .entry-title-date, {{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .post-date, {{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .gem-news-item-date' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'sticky_post_caption_time_heading',
			[
				'label' => __( 'Time', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'sticky_post_caption_time_tabs' );
		$control->start_controls_tab(
			'sticky_post_caption_time_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);


		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_time_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .post-time',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_time_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .post-time' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'sticky_post_caption_time_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_time_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .post-time',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_time_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .post-time' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset' => [ 'styled_list1', 'styled_list2' ],
					'show_date' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'sticky_post_caption_categories_heading',
			[
				'label' => __( 'Categories', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'sticky_post_caption_categories_tabs' );
		$control->start_controls_tab(
			'sticky_post_caption_categories_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_categories_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .post-meta-categories a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_categories_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .post-meta-categories a' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'sticky_post_caption_categories_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_categories_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .post-meta-categories a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_categories_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .post-meta-categories a' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact', 'compact-2' ],
					'show_categories' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'sticky_post_caption_author_heading',
			[
				'label' => __( 'Author', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);
		$control->start_controls_tabs( 'sticky_post_caption_author_tabs' );
		$control->start_controls_tab(
			'sticky_post_caption_author_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_author_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .post-meta-author',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_author_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .post-meta-author' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'sticky_post_caption_author_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_author_typography_hover',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .post-meta-author',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_author_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .post-meta-author' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
					'show_author' => 'yes',
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'sticky_post_caption_delimiter_heading',
			[
				'label' => __( 'Delimiter', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);
		$control->start_controls_tabs( 'sticky_post_caption_delimiter_tabs' );
		$control->start_controls_tab(
			'sticky_post_caption_delimiter_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);


		$control->add_responsive_control(
			'sticky_post_caption_delimiter_color',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .sep' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'sticky_post_caption_delimiter_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_delimiter_color_hover',
			[
				'label' => __( 'Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .caption-container:hover .sep' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [
					'thegem_elementor_preset!' => [ 'compact-2' ],
				],
			]
		);

		$control->end_controls_tab();
		$control->end_controls_tabs();

		$control->add_control(
			'sticky_post_caption_likes_heading',
			[
				'label' => __( 'Likes', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_likes_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .zilla-likes-count',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_likes_icon_color',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .zilla-likes::before' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bloglist .type-post.sticky .post-meta-likes i' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bloglist .type-post.sticky .post-meta-likes svg' => 'fill: {{VALUE}} !important;',
				],
				'condition' => [
					'show_likes' => 'yes',
				],
			]
		);

		$control->add_control(
			'sticky_post_caption_comments_heading',
			[
				'label' => __( 'Comments', 'thegem' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'sticky_post_caption_comments_typography',
				'selector' => '{{WRAPPER}} .bloglist .type-post.sticky .comments-link a',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'condition' => [
					'show_comments' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'sticky_post_caption_comments_icon_color',
			[
				'label' => __( 'Icon Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .bloglist .type-post.sticky .comments-link a::before' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'show_comments' => 'yes',
				],
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

	protected function thegem_bloglist_pagination($query = false) {

		$settings = $this->get_settings_for_display();

		if(!$query) {
			$query = $GLOBALS['wp_query'];
		}
		if($query->max_num_pages < 2) {
			return;
		}

		$paged		= (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
		$pagenum_link = html_entity_decode(get_pagenum_link());
		$query_args   = array();
		$url_parts	= explode('?', $pagenum_link);

		if(isset($url_parts[1])) {
			wp_parse_str($url_parts[1], $query_args);
		}

		$pagenum_link = remove_query_arg(array_keys($query_args), $pagenum_link);
		$pagenum_link = trailingslashit($pagenum_link) . '%_%';

		$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
		$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit('page/%#%', 'paged') : '?paged=%#%';

		if ( $settings['pagination_left_icon']['value'] ) {
			ob_start();
			Icons_Manager::render_icon( $settings['pagination_left_icon'], [ 'aria-hidden' => 'true' ] );
			$icon_html_prev = ob_get_clean();
			$prev_text = $icon_html_prev;
		} else {
			$prev_text = '<i class="default"></i>';
		}
		if ( $settings['pagination_right_icon']['value'] ) {
			ob_start();
			Icons_Manager::render_icon( $settings['pagination_right_icon'], [ 'aria-hidden' => 'true' ] );
			$icon_html_next = ob_get_clean();
			$next_text = $icon_html_next;
		} else {
			$next_text = '<i class="default"></i>';
		}

		// Set up paginated links.
		$links = paginate_links(array(
			'base'	 => $pagenum_link,
			'format'   => $format,
			'total'	=> $query->max_num_pages,
			'current'  => $paged,
			'mid_size' => 1,
			'add_args' => array_map('urlencode', $query_args),
			'prev_text' => $prev_text,
			'next_text' => $next_text,
		));

		if($links) :

		?>
		<div class="gem-pagination"><div class="gem-pagination-links gem-pagination-position-<?php echo esc_attr ( $settings['numbers_position'] ); ?>">
			<?php echo $links; ?>
		</div></div><!-- .pagination -->
		<?php
		endif;
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

		$bl_readmore_button = __DIR__ . '/templates/parts/bl_readmore_button.php';
		$bl_social_sharing  = __DIR__ . '/templates/parts/bl_social_sharing.php';
		$bl_loadmore_button  = __DIR__ . '/templates/parts/bl_loadmore_button.php';

		$this->add_render_attribute('blog_wrapper', 'class',
			[
				'bloglist blog clearfix',
				' blog-style-' . $settings['thegem_elementor_preset'],
				($settings['show_animation'] == 'yes' ? ' item-animation-' . $settings['animation_effect'] : ''),
				(Plugin::$instance->editor->is_edit_mode() ? 'lazy-loading-not-hide' : '')
			]);

		if ( 'yes' === ( $settings['show_readmore_button'] ) ) :
			wp_enqueue_style( 'thegem-button' );
			
			$this->add_render_attribute( 'button_container', 'class', ['gem-button-container', 'gem-widget-button', 'gem-button-position-inline'] );
			$this->add_render_attribute( 'readmore_button_text', 'class', 'gem-text-button' );
			$this->add_inline_editing_attributes( 'readmore_button_text', 'none' );
			$this->add_render_attribute( 'readmore_button', 'class', ['gem-button', 'gem-button-size-'.esc_attr( $settings['readmore_button_size'] )] );
			if( ! empty( $settings['readmore_button_icon_align'] ) && $settings['readmore_button_icon_align'] === 'right' ) {
				$this->add_render_attribute( 'readmore_button', 'class', 'gem-button-icon-position-right' );
			}
			if( ! empty( $settings['readmore_button_type'] ) ) {
				$this->add_render_attribute( 'readmore_button', 'class', 'gem-button-style-'.$settings['readmore_button_type'] );
			}

			$button_container_attributes = $this->get_render_attribute_string( 'button_container' );
			$readmore_button = $this->get_render_attribute_string( 'readmore_button' );
			$readmore_button_text = $this->get_render_attribute_string( 'readmore_button_text' );
		endif;

		if ( $settings['pagination_type'] == 'load-more-button' ) :
			wp_enqueue_style( 'thegem-button' );
			$this->add_render_attribute( 'loadmore_button_text', 'class', 'gem-text-button' );

			$separator_enabled = ! empty( $settings['loadmore_button_show_separator'] ) ? true : false;

			if( $separator_enabled ) {
				$separator_style_square =  ( $settings['loadmore_button_separator_style_active'] === 'square' ) ? true : false;
			}

			switch ( $settings['loadmore_button_size'] ) {

				case 'small' : $line_thickness = 2; break;
				case 'medium': $line_thickness = 3; break;
				case 'large' : $line_thickness = 4; break;
				case 'giant' : $line_thickness = 6; break;
				default      : $line_thickness = 2; break;
			}

			$color_default = ( 'flat' === $settings['loadmore_button_type'] ) ? thegem_get_option('button_background_basic_color') : thegem_get_option('button_outline_border_basic_color');

			$sep_color = !empty( $settings['loadmore_color_square_border'] ) ? $settings['loadmore_color_square_border'] : $color_default;

			$this->add_loadmore_attributes_items( $settings, $separator_enabled, $line_thickness );
		endif;

		if ( 'yes' === ( $settings['image_icon_show'] ) ) : ?>
			<style>.bloglist .post-img a:hover:after{opacity: 1 !important;}</style>
		<?php else: ?>
			<style>.bloglist .post-img a:hover:after{opacity: 0 !important;}</style>
		<?php endif;

		if (in_array("0", $settings['select_blog_cat'])) {
			$cat = '';
		} elseif ( ! empty ( $settings['select_blog_cat'] ) ) {
			$cat = implode(',', $settings['select_blog_cat']);
		} else { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select blog categories in "Blog" section', 'thegem') ?>
			</div>
			<?php return;
		}

		$ignore_sticky_posts = ( 'yes' === $settings['ignore_sticky_posts'] ) ? 1 : 0;
		$sticky =  ( 'yes' === $settings['ignore_sticky_posts'] ) ? get_option( 'sticky_posts' ) : NULL;

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$posts = new WP_Query(array(
			'post_type' => 'post',
			'category_name' => $cat,
			'post_status' => 'publish',
			'posts_per_page' => $settings['items_per_page'],
			'ignore_sticky_posts' => $ignore_sticky_posts,
			'post__not_in' => $sticky,
			'paged' => $paged
		));


		$next_page = 0;
		if ( $settings['pagination_type'] == 'load-more-button' || $settings['pagination_type'] == 'infinite-scroll' ) {
			if( $posts->max_num_pages > $paged ) {
				$next_page = $paged + 1;
			} else {
				$next_page = 0;
			}
		}

		$this->add_render_attribute( 'blog_wrapper', 'data-page',  $paged );
		$this->add_render_attribute( 'blog_wrapper', 'data-paged',  $paged );
		$this->add_render_attribute( 'blog_wrapper', 'data-next-page',  $next_page );
		$this->add_render_attribute( 'blog_wrapper', 'data-load-more-action', 'thegem_bloglist_load_more' );
		
		if ( $posts->have_posts() ) {

			$preset_path = __DIR__ . '/templates/output-blog-list-' . $settings['thegem_elementor_preset'] . '.php';
			$preset_path_filtered = apply_filters( 'thegem_blog_list_' . $settings['thegem_elementor_preset'] . '_item_preset', $preset_path);
			$preset_path_theme = get_stylesheet_directory() . '/templates/blog-list/output-blog-list-' . $settings['thegem_elementor_preset'] . '.php';
			?>
			<div class="preloader"><div class="preloader-spin"></div></div>
			<div <?php echo $this->get_render_attribute_string( 'blog_wrapper' ); ?>>
			<?php
				while ( $posts->have_posts() ) {
					$posts->the_post();
					if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
						include($preset_path_theme);
					} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
						include($preset_path_filtered);
					}
				}
		} else { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select blog categories in "Blog" section', 'thegem') ?>
			</div>
		<?php }
		?>
			</div>
		<?php

		/** Pagination */

		if ( 'yes' === ( $settings['show_pagination'] ) ) : ?>

			<?php

			$localize = array(
				'data' => $settings,
				'url' => admin_url('admin-ajax.php'),
			);

			if( $settings['pagination_type'] == 'infinite-scroll' || $settings['pagination_type'] == 'load-more-button' ) {
				wp_localize_script( 'thegem-blog', 'thegem_blog_ajax', $localize );
			}
			?>

			<?php if ( $settings['pagination_type'] == 'numbers' ) : ?>
				<div class="preloader"><div class="preloader-spin"></div></div>
				<div class="bloglist-pagination" data-page="<?php echo esc_attr( $paged ); ?>" data-next-page="<?php echo esc_attr( $next_page ); ?>">
					<?php if ( $settings['pagination_type'] == 'numbers' ): ?>
						<?php $this->thegem_bloglist_pagination( $posts ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if( $settings['pagination_type'] == 'load-more-button' && $posts->max_num_pages > $paged ): ?>
				<div class="preloader"><div class="preloader-spin"></div></div>
				<div class="blog-load-more">
					<div class="inner">
						<?php if ( ! empty( $bl_loadmore_button ) && file_exists( $bl_loadmore_button ) ) : include $bl_loadmore_button; endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if( $settings['pagination_type'] == 'infinite-scroll' && $posts->max_num_pages > $paged ): ?>
					<div class="blog-scroll-pagination"></div>
			<?php endif; ?>

		<?php endif;
		wp_reset_postdata();
	}

	private function add_loadmore_attributes_items( $settings, $separator_enabled, $line_thickness ) {

		// Container
		$this->add_render_attribute( 'loadmore_button_container', 'class', ['gem-button-container', 'gem-widget-button'] );
		if( $separator_enabled ) {
			$this->add_render_attribute( 'loadmore_button_container', 'class', ['gem-button-position-center', 'gem-button-with-separator'] );
		} else {
			if( 'yes' === $settings['loadmore_button_stretch_fullwidth'] ) {
				$this->add_render_attribute( 'loadmore_button_container', 'class', 'gem-button-position-fullwidth' );
			} else {
				$this->add_render_attribute( 'loadmore_button_container', 'class', 'gem-button-position-'.$settings['loadmore_button_position'] );
			}
		}
		 // Separator
		$this->add_render_attribute( 'attr_separator', 'class', 'gem-button-separator' );
		if( ! empty( $settings['loadmore_button_separator_style_active'] )) {
			$this->add_render_attribute( 'attr_separator', 'class', 'gem-button-separator-type-'.$settings['loadmore_button_separator_style_active'] );
		}
		// Link
		$this->add_render_attribute( 'loadmore_button', 'class', ['gem-button', 'gem-button-size-'.esc_attr( $settings['loadmore_button_size'] )] );
		if( ( 'flat' === $settings['loadmore_button_type'] ) ) {
			$this->add_render_attribute( 'loadmore_button', 'class', 'gem-button-style-flat' );
		}
		else {
			$this->add_render_attribute( 'loadmore_button', 'class', 'gem-button-style-outline' );
		}
		if( ! empty($settings['loadmore_button_icon_align']) && $settings['loadmore_button_icon_align'] === 'right' ) {
			$this->add_render_attribute( 'loadmore_button', 'class', 'gem-button-icon-position-right' );
		}
		if( ( 'outline' === $settings['loadmore_button_type'] ) ) {
			$this->add_render_attribute( 'loadmore_button', 'class', 'gem-button-border-'.$line_thickness );
		}

	}

	public function get_preset_data() {

		return array(
			'default' => array(
				'caption_container_padding' => ['top' => '10', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
				'description_bottom_spacing' => ['size' => 40,'unit' => 'px'],
			),
			'styled_list1' => array(
				'caption_container_padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => true],
			),
			'styled_list2' => array(
				'caption_container_padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => true],
			),
			'compact' => array(
				'image_size_compact1' => ['size' => 183,'unit' => 'px'],
				'image_size_compact1_mobile' => ['size' => 100,'unit' => '%'],
				'description_bottom_spacing' => ['size' => 20,'unit' => 'px'],
				'caption_container_padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => true],
				'caption_container_background_background' => 'classic',
				'caption_container_background_color' => '#FFFFFF',

			),
			'compact-2' => array(
				'caption_container_padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => true],
				'caption_container_left_spacing' => ['size' => 12,'unit' => 'px'],
				'title_top_spacing' => ['size' => 0,'unit' => 'px'],
				'title_bottom_spacing' => ['size' => 0,'unit' => 'px'],
				'description_bottom_spacing' => ['size' => 0,'unit' => 'px'],
				'image_border_radius' => ['top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true],
			),
		);
	}
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new TheGem_Bloglist() );