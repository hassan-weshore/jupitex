<?php

use Elementor\Controls_Manager;

class TheGem_Section_FullPage {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {

		if (!defined('THEGEM_ELEMENTOR_SECTION_FULLPAGE_DIR')) {
			define('THEGEM_ELEMENTOR_SECTION_FULLPAGE_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_SECTION_FULLPAGE_URL')) {
			define('THEGEM_ELEMENTOR_SECTION_FULLPAGE_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'after_section_end' ), 10, 2 );
		add_action( 'elementor/frontend/section/before_render', array( $this, 'section_before_render' ) );
	}

	public function after_section_end( $element, $args ) {
		global $post;
		$page_data = thegem_get_sanitize_page_effects_data($post->ID);

		if($page_data['effects_page_scroller']) {
			$element->start_controls_section(
				'thegem_section_fullpage',
				array(
					'label' => esc_html__( 'TheGem Fullpage Slider', 'thegem' ),
					'tab'   => Elementor\Controls_Manager::TAB_LAYOUT,
					'hide_in_inner' => true,
				)
			);

			$element->add_control(
				'thegem_section_fullpage_anchor',
				[
					'label' => esc_html__( 'Slide Anchor', 'thegem' ),
					'type' => Controls_Manager::TEXT,
				]
			);

			$element->add_control(
				'thegem_section_fullpage_name',
				[
					'label' => esc_html__( 'Slide Name', 'thegem' ),
					'type' => Controls_Manager::TEXT,
				]
			);

			$element->end_controls_section();
		}
	}

    public function section_before_render( $element ) {

        if ( 'section' === $element->get_name() ) {

            $settings = $element->get_settings_for_display();

            if (!empty($settings['thegem_section_fullpage_anchor'])) {
                $thegem_section_fullpage_anchor = preg_replace('/[^A-Za-z0-9-_]/', '', $settings['thegem_section_fullpage_anchor']);
                $element->add_render_attribute( '_wrapper', 'data-anchor', esc_attr($thegem_section_fullpage_anchor));
            }

            if (!empty($settings['thegem_section_fullpage_name'])) {
                $element->add_render_attribute( '_wrapper', 'data-tooltip', esc_attr($settings['thegem_section_fullpage_name']));
            }

        }

    }

}

if (get_option('template') == 'thegem-elementor') {
    TheGem_Section_FullPage::instance();
}

