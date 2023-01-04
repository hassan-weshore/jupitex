<?php

namespace Sellkit\Dynamic_Keywords\Contact_Segmentation;

use Sellkit\Contact_Segmentation\Contact_Data;
use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Contact segmentation base.
 *
 * @since 1.1.0
 */
abstract class Contact_Segmentation_Base {

	/**
	 * Contact segmentation data.
	 *
	 * @var object
	 * @since 1.1.0
	 */
	public static $contact_segmentation = [];

	/**
	 * Cart items.
	 *
	 * @var object
	 * @since 1.1.0
	 */
	public static $cart_items = [];

	/**
	 * Alert ID.
	 *
	 * @var integer|null
	 * @since 1.2.1
	 */
	public static $alert_id = null;

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		if ( ! sellkit()->has_valid_dependencies() ) {
			return;
		}

		self::$cart_items = ! empty( WC()->session->cart ) ? WC()->session->cart : '';
	}

	/**
	 * Get shortcode default data.
	 *
	 * @since 1.1.0
	 */
	public function get_data() {
		if ( ! class_exists( 'Sellkit\Contact_Segmentation\Contact_Data' ) ) {
			return;
		}

		$contact_data = Contact_Data::get_instance();

		if ( empty( $contact_data->get_data() ) ) {
			return;
		}

		$this::$contact_segmentation = $contact_data->get_data();

		return $contact_data->get_data();
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
	 * @param string $type condition slug.
	 */
	public function get_content_meta( $type ) {
		if ( is_admin() ) {
			return;
		}

		$this::$alert_id = get_query_var( 'alert_id' );

		$alert_meta = get_post_meta( $this::$alert_id, 'conditions', true );

		if ( empty( $alert_meta ) ) {
			return;
		}

		foreach ( $alert_meta as $meta ) {
			if ( in_array( $type, $meta, true ) ) {
				return $meta['condition_value'];
			}
		}

		return [];
	}

	/**
	 * Check products and categories count.
	 *
	 * @since 1.1.0
	 * @param array $list array of shortcode result.
	 */
	public function get_result( $list ) {
		if ( count( $list ) < 4 ) {
			return $list;
		}

		$list           = array_slice( $list, 0, 4 );
		$list_last_item = $list[3];

		array_splice( $list, -1, 2, esc_html__( 'And ', 'sellkit' ) );

		array_push( $list, $list_last_item );

		return $list;
	}

	/**
	 * Get Keywords type.
	 *
	 * @since 1.1.0
	 */
	public static function get_keywords_type() {
		return 'contact_segmentation';
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
