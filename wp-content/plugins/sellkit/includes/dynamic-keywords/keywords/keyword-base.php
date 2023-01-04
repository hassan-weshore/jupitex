<?php

defined( 'ABSPATH' ) || die();
/**
 * Tag Base.
 *
 * @since 1.1.0
 */
abstract class Tag_Base {

	/**
	 * Order data.
	 *
	 * @var object
	 * @since 1.1.0
	 */
	public static $order = null;

	/**
	 * Get order data.
	 *
	 * @since 1.1.0
	 */
	public function get_keyword_content() {
		return $this->render_content();
	}

	/**
	 * Get shortcode default data.
	 *
	 * @since 1.1.0
	 * @param array $atts shortcode attributes.
	 */
	public function shortcode_content( $atts ) {
		$atts = shortcode_atts( [
			'fallback' => '',
		], $atts );

		return $atts['fallback'];
	}

	/**
	 * Get shortcode default data.
	 *
	 * @since 1.1.0
	 */
	public function get_data() {
		if ( ! empty( $this::$order ) ) {
			return $this::$order;
		}

		$order_key = ! empty( sellkit_htmlspecialchars( INPUT_GET, 'order-key' ) ) ? sellkit_htmlspecialchars( INPUT_GET, 'order-key' ) : sellkit_htmlspecialchars( INPUT_GET, 'key' );

		if ( empty( $order_key ) ) {
			return;
		}

		$order_id = wc_get_order_id_by_order_key( $order_key );

		$this::$order = wc_get_order( $order_id );

		return $this::$order;
	}

	/**
	 * Get Keywords type.
	 *
	 * @since 1.1.0
	 */
	public static function get_keywords_type() {
		return 'order_keyword';
	}

	/**
	 * Get keyword id.
	 *
	 * @since 1.1.0
	 * @access public
	 * @abstract
	 *
	 * @return string
	 */
	abstract public function get_id();

	/**
	 * Get keyword title.
	 *
	 * @since 1.1.0
	 * @access public
	 * @abstract
	 *
	 * @return string
	 */
	abstract public function get_title();

	/**
	 * Render content.
	 *
	 * @since 1.1.0
	 * @access public
	 * @param array $atts array of shortcode arguments.
	 * @abstract
	 *
	 * @return string
	 */
	abstract public function render_content( $atts );
}
