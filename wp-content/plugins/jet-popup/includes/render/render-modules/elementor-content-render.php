<?php
namespace Jet_Popup\Render;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Elementor_Content_Render extends Base_Render {

	/**
	 * [$name description]
	 * @var string
	 */
	protected $name = 'elementor-template-render';

	/**
	 * @var array
	 */
	public $depended_styles = [];

	/**
	 * [$depended_scripts description]
	 * @var array
	 */
	public $depended_scripts = [];

	/**
	 * @var array
	 */
	public $elementor_widgets = [];

	/**
	 * @var bool
	 */
	public $post_css_exist = false;

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function init() {}

	/**
	 * [get_name description]
	 * @return [type] [description]
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function get_depended_styles() {
		return array_unique( $this->depended_styles );
	}

	/**
	 * @return array
	 */
	public function get_depended_scripts() {
		return array_unique( $this->depended_scripts );
	}

	/**
	 * @return array
	 */
	public function get_elementor_widgets() {
		return array_unique( $this->elementor_widgets );
	}

	/**
	 * [render description]
	 * @return [type] [description]
	 */
	public function render() {

		if ( ! \Jet_Popup_Utils::has_elementor() ) {
			echo __( 'Elementor not installed', 'jet-popup' );

			return false;
		}

		if ( $this->is_editor() ) {
			echo __( 'Elementor editor content is not available in the Blocks Editor', 'jet-popup' );

			return false;
		}

		$popup_id   = $this->get( 'popup_id', false );
		$with_css   = $this->get( 'with_css', false );
		$is_content = $this->get( 'is_content', true );

		if ( ! $popup_id || ! $is_content ) {
			return false;
		}

		$before_styles_queue  = wp_styles()->queue;
		$before_scripts_queue = wp_scripts()->queue;
		$content              = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $popup_id, $with_css );
		$after_styles_queue   = wp_styles()->queue;
		$after_scripts_queue  = wp_scripts()->queue;

		$styles_depends         = array_diff( $after_styles_queue, $before_styles_queue );
		$script_depends         = array_diff( $after_scripts_queue, $before_scripts_queue );

		$this->depended_styles  = array_merge( $this->depended_styles, $styles_depends );
		$this->depended_scripts = array_merge( $this->depended_scripts, $script_depends );

		echo do_shortcode( $content );

    }

	/**
	 * @return array
	 */
	public function get_render_data() {
		$popup_id = $this->get( 'popup_id', false );
		$popup_id = apply_filters( 'jet-popup/popup-generator/before-define-popup-assets/popup-id', $popup_id, $this->get_settings() );

		$template_content = $this->get_content();
		$template_styles  = [];
		$template_scripts  = [];

		do_action( 'jet_plugins/frontend/register_scripts' );

		$styles_data = $this->get_content_style_data( $popup_id );
		$scripts_data = $this->get_content_script_data( $popup_id );

		$template_styles = $styles_data['depends'];
		$template_scripts = $scripts_data['depends'];

		$render_data = [
			'content'       => $template_content,
			'styles'        => $template_styles,
			'scripts'       => $template_scripts,
			'afterScripts'  => [],
		];

		return apply_filters( 'jet-plugins/render/render-data', $render_data, $popup_id, $scripts_data['widgets'], 'elementor' );
	}

	/**
	 * [get_elementor_template_scripts_url description]
	 * @param  [type] $template_id [description]
	 * @return [type]              [description]
	 */
	public function get_content_script_data( $popup_id = false ) {

		if ( ! $popup_id ) {
			return [
				'depends' => [],
				'widgets' => [],
			];
		}

		$is_deps = $this->get( 'is_script_deps', true );

		if ( ! $is_deps ) {
			return [
				'depends' => [],
				'widgets' => [],
			];
		}

		$data_cache = get_post_meta( $popup_id, '_is_script_deps', true );
		$use_cache = apply_filters( 'jet-popup/elementor-render/render-data/use-cache', true );

		if ( ! empty( $data_cache ) && $use_cache ) {
			return $data_cache;
		}

		$document = \Elementor\Plugin::$instance->documents->get( $popup_id );

		if ( ! $document ) {
			return [
				'depends' => [],
				'widgets' => [],
			];
		}

		$elements_data = $document->get_elements_raw_data();

		if ( empty( $elements_data ) ) {
			return [
				'depends' => [],
				'widgets' => [],
			];
		}

		$this->find_widgets_script_handlers( $elements_data );

		$depended_scripts = $this->get_depended_scripts();

		$scripts_data = [
			'depends' => array_map( function ( $handle ) {
				$script_obj = $this->get_registered_script_obj_by_handler( $handle );

				return [
					'handle' => $handle,
					'src'    => $script_obj->src,
					'obj'    => $script_obj
				];
			}, $depended_scripts ),
			'widgets' => $this->get_elementor_widgets(),
		];

		update_post_meta( $popup_id, '_is_script_deps', $scripts_data );

		return $scripts_data;
	}

	/**
	 * [get_elementor_template_scripts_url description]
	 * @param  [type] $template_id [description]
	 * @return [type]              [description]
	 */
	public function get_content_style_data( $popup_id = false ) {

		if ( ! $popup_id ) {
			return [
				'depends' => [],
			];
		}

		$is_deps = $this->get( 'is_style_deps', true );

		if ( ! $is_deps ) {
			return [
				'depends' => [],
			];
		}

		$data_cache = get_post_meta( $popup_id, '_is_style_deps', true );
		$use_cache  = apply_filters( 'jet-popup/elementor-render/render-data/use-cache', true );

		if ( ! empty( $data_cache ) && $use_cache ) {
			return $data_cache;
		}

		$depended_styles = $this->get_depended_styles();

		$styles_data = [
			'depends' => array_map( function ( $handle ) {
				$style_obj = $this->get_registered_style_obj_by_handler( $handle );

				return [
					'handle' => $handle,
					'src'    => $style_obj->src,
					'obj'    => $style_obj
				];
			}, $depended_styles ),
		];

		update_post_meta( $popup_id, '_is_style_deps', $styles_data );

		return $styles_data;
	}

	/**
	 * @param $template_id
	 *
	 * @return false|string
	 */
	public function get_elementor_template_font_url_list( $popup_id ) {
		$post_css = new \Elementor\Core\Files\CSS\Post( $popup_id );
		$post_meta = $post_css->get_meta();
		$this->post_css_exist = ! empty( $post_meta['status'] ) ? true : false;
		$urls_list = [];

		if ( ! isset( $post_meta['fonts'] ) ) {
			return false;
		}

		$fonts = $post_meta['fonts'];

		if ( empty( $fonts ) ) {
			return false;
		}

		$fonts = array_unique( $fonts );

		$google_fonts = [
			'google' => [],
			'early' => [],
		];

		foreach ( $fonts as $key => $font ) {
			$font_type = \Elementor\Fonts::get_font_type( $font );

			switch ( $font_type ) {
				case \Elementor\Fonts::GOOGLE:
					$google_fonts['google'][] = $font;
					break;

				case \Elementor\Fonts::EARLYACCESS:
					$google_fonts['early'][] = $font;
					break;
			}
		}

		if ( ! empty( $google_fonts['google'] ) ) {
			foreach ( $google_fonts['google'] as &$font ) {
				$font = str_replace( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
			}

			$fonts_url = sprintf( 'https://fonts.googleapis.com/css?family=%s', implode( rawurlencode( '|' ), $google_fonts['google'] ) );

			$subsets = [
				'ru_RU' => 'cyrillic',
				'bg_BG' => 'cyrillic',
				'he_IL' => 'hebrew',
				'el'    => 'greek',
				'vi'    => 'vietnamese',
				'uk'    => 'cyrillic',
				'cs_CZ' => 'latin-ext',
				'ro_RO' => 'latin-ext',
				'pl_PL' => 'latin-ext',
				'hr_HR' => 'latin-ext',
				'hu_HU' => 'latin-ext',
				'sk_SK' => 'latin-ext',
				'tr_TR' => 'latin-ext',
				'lt_LT' => 'latin-ext',
			];

			$locale = get_locale();

			if ( isset( $subsets[ $locale ] ) ) {
				$fonts_url .= '&subset=' . $subsets[ $locale ];
			}

			$urls_list[ "jet-popup-google-fonts-{$popup_id}" ] = $fonts_url;

		}

		if ( ! empty( $google_fonts['early'] ) ) {
			foreach ( $google_fonts['early'] as $current_font ) {
				$font_url = sprintf( 'https://fonts.googleapis.com/earlyaccess/%s.css', strtolower( str_replace( ' ', '', $current_font ) ) );
				$urls_list[ "jet-popup-google-earlyaccess-{$popup_id}" ] = $font_url;
			}
		}

		if ( $this->post_css_exist ) {
			//update_post_meta( $popup_id, '_is_style_deps', $urls_list );
		}

		return $urls_list;
	}

	/**
	 * [find_widgets_script_handlers description]
	 * @param  [type] $elements_data [description]
	 * @return [type]                [description]
	 */
	public function find_widgets_script_handlers( $elements_data ) {

		foreach ( $elements_data as $element_data ) {

			if ( 'widget' === $element_data['elType'] ) {
				$widget                    = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );
				$this->elementor_widgets[] = $widget->get_name();
			} else {
				$element = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );
				$childrens = $element->get_children();

				foreach ( $childrens as $key => $children ) {
					$children_data[ $key ] = $children->get_raw_data();
					$this->find_widgets_script_handlers( $children_data );
				}
			}
		}
	}

	/**
	 * @param $handler
	 *
	 * @return \_WP_Dependency|false
	 */
	public function get_registered_style_obj_by_handler( $handler ) {

		if ( isset( wp_styles()->registered[ $handler ] ) ) {
			$src = wp_styles()->registered[ $handler ]->src;

			if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( wp_styles()->content_url && 0 === strpos( $src, wp_styles()->content_url ) ) ) {
				wp_styles()->registered[ $handler ]->src = wp_styles()->base_url . $src;
			}

			return wp_styles()->registered[ $handler ];
		}

		return false;
	}

	/**
	 * [get_script_uri_by_handler description]
	 * @param  [type] $handler [description]
	 * @return [type]          [description]
	 */
	public function get_registered_script_obj_by_handler( $handler ) {

		if ( isset( wp_scripts()->registered[ $handler ] ) ) {
			$src = wp_scripts()->registered[ $handler ]->src;

			if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( wp_scripts()->content_url && 0 === strpos( $src, wp_scripts()->content_url ) ) ) {
				wp_scripts()->registered[ $handler ]->src = wp_scripts()->base_url . $src;
			}

			return wp_scripts()->registered[ $handler ];
		}

		return false;
	}

}
