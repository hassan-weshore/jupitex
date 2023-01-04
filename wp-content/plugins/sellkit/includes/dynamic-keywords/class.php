<?php

defined( 'ABSPATH' ) || die();

/**
 * Dynamic Keywords class.
 *
 * @since 1.1.0
 */
class Sellkit_Dynamic_Keywords {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Dynamic_Keywords
	 */
	private static $instance = null;

	/**
	 * Keywords.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	public static $keywords = [];

	/**
	 * Get a class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return Sellkit_Dynamic_Keywords Class
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->load_keywords();
	}

	/**
	 * Get keywords.
	 *
	 * @since 1.1.0
	 * @param string $keyword_id keyword id.
	 * @param string $keyword_title keyword title.
	 * @param string $type keyword type name.
	 */
	public static function get_keywords( $keyword_id, $keyword_title, $type ) {
		$keyword_id = 'sellkit' . str_replace( '_', '-', $keyword_id );

		if ( 'order_keyword' === $type ) {
			self::$keywords[ $type ][ $keyword_id ] = $keyword_title;

			return;
		}

		self::$keywords[ $type ][ $keyword_id ] = $keyword_title;
	}

	/**
	 * Loads all of the keywords.
	 *
	 * @since 1.1.0
	 */
	public function load_keywords() {
		sellkit()->load_files( [
			'dynamic-keywords/keywords/keyword-base',
			'dynamic-keywords/contact-segmentation/contact-base',
		] );

		$path = trailingslashit( sellkit()->plugin_dir() . 'includes/dynamic-keywords' );

		$file_paths = glob( $path . '*/*.php' );

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				continue;
			}

			require_once $file_path;

			$file_name     = str_replace( '.php', '', basename( $file_path ) );
			$keyword_class = str_replace( '-', ' ', $file_name );
			$keyword_class = str_replace( ' ', '_', ucwords( $keyword_class ) );

			if ( ! class_exists( $keyword_class ) ) {
				$keyword_class = "Sellkit\Dynamic_Keywords\Contact_Segmentation\\{$keyword_class}";
			}

			if (
				! class_exists( $keyword_class ) ||
				( 'keyword-base' === $file_name || 'contact-base' === $file_name )
			) {
				continue;
			}

			$keyword = new $keyword_class();

			$this->get_keywords( $keyword->get_id(), $keyword->get_title(), $keyword->get_keywords_type() );

			add_shortcode( "sellkit-{$file_name}", [ $keyword, 'render_content' ] );
		}
	}
}

Sellkit_Dynamic_Keywords::get_instance();
