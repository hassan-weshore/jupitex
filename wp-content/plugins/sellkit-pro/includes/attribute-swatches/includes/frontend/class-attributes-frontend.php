<?php
defined( 'ABSPATH' ) || die();

/**
 * Prodcuts Atribute Frontend.
 *
 * @package Sellkit\Artbees_WC_Attribute_Swatches\Frontend
 * @since 1.1.0
 */
class Artbees_WC_Attribute_Frontend {
	/**
	 * Attribute Option Base Name.
	 *
	 * @since 1.1.0
	 */
	const ATTRIBUTE_OPTION_NAME = 'artbees_wc_attributes';

	/**
	 * Attribute Term Meta Name for Fields.
	 *
	 * @since 1.1.0
	 */
	const ATTRIBUTE_TERM_META_NAME = 'artbees_was_term_meta';

	/**
	 * Artbees_WC_Attribute_Frontend constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		if ( $this->check_is_variable_product() ) {
			return;
		}

		add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'modify_attribute_label' ] );
		add_action( 'woocommerce_after_add_to_cart_form', [ $this, 'disable_modify_attribute_label' ] );
		add_filter( 'woocommerce_reset_variations_link', [ $this, 'reset_options' ] );
		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'attribute_html_structure' ], 10, 2 );
	}

	/**
	 * Enable attribute label modification.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public static function check_is_variable_product() {
		global $product;

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Disable attribute label modification for out of add to cart.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function disable_modify_attribute_label() {
		remove_filter( 'woocommerce_attribute_label', [ $this, 'modified_attribute_label' ], 999, 1 );
	}

	/**
	 * Enable attribute label modification.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function modify_attribute_label() {
		add_filter( 'woocommerce_attribute_label', [ $this, 'modified_attribute_label' ], 999, 1 );
	}

	/**
	 * Modify Label for Frontend.
	 *
	 * @since 1.1.0
	 * @param string $label  Woocommerce default label.
	 * @return string
	 */
	public static function modified_attribute_label( $label ) {
		$attribute_label = sprintf(
			'<strong>%1$s</strong>: <span class="artbees-was-chosen-attribute"  data-no-selection="%2$s"><span class="no-selection"></span></span>',
			$label,
			__( 'No selection', 'sellkit-pro' )
		);

		return $attribute_label;
	}

	/**
	 * Modify reset button for Frontend.
	 *
	 * @since 1.1.0
	 */
	public static function reset_options() {
		$reset_options = sprintf(
			'<div class="artbees-was-reset-options"><a class="reset_variations" href="#"><i class="fa fa-refresh"></i><span>%s</span></a></div>',
			esc_html__( 'Clear', 'sellkit-pro' )
		);

		return $reset_options;
	}

	/**
	 * Modify Dropdown html structure for Frontend.
	 *
	 * @since 1.1.0
	 * @param string $html  WooCommerce HTML default structure.
	 * @param array  $args  WooCommerce dropdown arguments.
	 * @return string
	 */
	public function attribute_html_structure( $html, $args ) {
		global $product;

		if ( empty( $args['options'] ) ) {
			return;
		}

		$attribute_slug  = $args['attribute'];
		$attribute_id    = wc_attribute_taxonomy_id_by_name( $attribute_slug );
		$attribute_terms = $this->get_attribute_terms( $product->get_id(), $attribute_slug );

		$attribute_general_settings = $this->get_attribute_general_settings( $attribute_id, $attribute_slug );
		$attribute_general_class    = $this->generate_attributes_class_for_settings( $attribute_general_settings );

		$html .= sprintf( '<ul class="artbees-was-swatches %s">', $attribute_general_class );

		foreach ( $attribute_terms as $data ) {
			$html .= sprintf(
				'<li class="artbees-was-swatches-item"><a href="#" class="artbees-was-swatch" data-term="%1$s" data-attribute="%2$s" data-attribute_name="%3$s">%4$s</a></li>',
				esc_attr( $data->slug ),
				esc_attr( '#' . $data->taxonomy ),
				esc_attr( $data->name ),
				wp_kses_post( $this->get_attribute_term_data( $data, $attribute_general_settings, $attribute_slug ) )
			);
		}

		$html .= '</ul>';
		return $html;
	}

	/**
	 * Get Attribute Terms.
	 *
	 * @since 1.1.0
	 * @param int    $product_id      Current product id.
	 * @param string $attribute_slug  Current product attribute slug.
	 */
	public function get_attribute_terms( $product_id, $attribute_slug ) {
		return wc_get_product_terms( $product_id, $attribute_slug, [ 'fields' => 'all' ] );
	}

	/**
	 * Get Attribute Terms data.
	 *
	 * @since 1.1.0
	 * @param object $term            Term data.
	 * @param array  $settings        Attribute data.
	 * @param string $attribute_slug  Attribute slug.
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function get_attribute_term_data( $term, $settings, $attribute_slug ) {
		$term_meta = get_term_meta( $term->term_id, self::ATTRIBUTE_TERM_META_NAME, true );

		if ( ! empty( $this->get_product_inside_attribute( $attribute_slug ) ) ) {
			$attribute = $this->get_product_inside_attribute( $attribute_slug );

			$term_meta = $this->get_product_inside_attribute( $attribute_slug );
			$term_meta = isset( $term_meta[ $term->slug ] ) ? $term_meta[ $term->slug ] : $term_meta;
		}

		if ( ! is_array( $term_meta ) ) {
			return;
		}

		$type = array_key_exists( 'type', $term_meta ) ? $term_meta['type'] : $attribute['swatch_type'];

		if ( empty( $type ) ) {
			return;
		}

		$term_color = isset( $term_meta['color'] ) ? $term_meta['color'] : '';
		$term_image = isset( $term_meta['image'] ) ? $term_meta['image'] : '';

		switch ( $type ) {
			case 'color':
				return $this->get_attribute_color( $term_color, $settings );
			case 'text':
				return $this->get_attribute_text( $term );
			case 'radio':
				return $this->get_attribute_radio( $term );
			case 'image':
				return $this->get_attribute_image( $term_image, $settings );
		}
	}

	/**
	 * Color Attribute Output.
	 *
	 * @since 1.1.0
	 * @param string $value     Value of attribute term.
	 * @param array  $settings  settings data of attribute term.
	 * @return string
	 */
	public function get_attribute_color( $value, $settings ) {
		if ( empty( $value ) || empty( $settings ) ) {
			return;
		}

		$color_size = ! empty( $settings['color_size'] ) ? $settings['color_size'] : '';

		return sprintf(
			'<span class="artbees-was-content artbees-was-content-color"><span style="background-color: %1$s; width: %2$s; height: %2$s;"></span></span>',
			$value,
			$color_size + 10 . 'px'
		);
	}

	/**
	 * Image Attribute Output.
	 *
	 * @since 1.1.0
	 * @param string $value     Value of attribute term.
	 * @param array  $settings  settings data of attribute term.
	 * @return string
	 */
	public function get_attribute_image( $value, $settings ) {
		if ( empty( $value ) || empty( $settings ) ) {
			return;
		}

		$image_size = ! empty( $settings['image_size'] ) ? $settings['image_size'] : '';

		return sprintf(
			'<div class="artbees-was-content artbees-was-content-image" style="width: %1$s; height: %1$s;">%2$s</div>',
			$image_size + 10 . 'px',
			! empty( wp_get_attachment_image( $value ) ) ? wp_get_attachment_image( $value ) : ''
		);
	}

	/**
	 * Text Attribute Output.
	 *
	 * @since 1.1.0
	 * @param object $term  object of attribute term.
	 * @return string
	 */
	public function get_attribute_text( $term ) {
		return sprintf(
			'<span class="artbees-was-content artbees-was-text">%s</span>',
			$term->name
		);
	}


	/**
	 * Radio Attribute Output.
	 *
	 * @since 1.1.0
	 * @param object $term  object of attribute term.
	 * @return string
	 */
	public function get_attribute_radio( $term ) {
		return sprintf(
			'<span class="artbees-was-content artbees-was-radio">%s</span>',
			$term->name
		);
	}
	/**
	 * Get Attrinute General Settings.
	 *
	 * @since 1.1.0
	 * @param int    $id              attribute term id.
	 * @param string $attribute_slug  attribute term slug.
	 * @return array
	 */
	public function get_attribute_general_settings( $id, $attribute_slug ) {
		$settings = get_option( self::ATTRIBUTE_OPTION_NAME . '-' . $id );
		$settings = json_decode( $settings, true );

		if ( ! empty( $this->get_product_inside_attribute( $attribute_slug ) ) ) {
			$settings = $this->get_product_inside_attribute( $attribute_slug );
		}

		return $settings;
	}

	/**
	 * Generate Attrinute Classes for Settings.
	 *
	 * @since 1.1.0
	 * @param array $settings  array of attribute term settings.
	 * @return string
	 */
	public function generate_attributes_class_for_settings( $settings ) {
		if ( empty( $settings ) ) {
			return;
		}

		$type = ! empty( $settings['attribute_type'] ) ? $settings['attribute_type'] : '';

		if ( ! empty( $settings['swatch_type'] ) ) {
			$type = $settings['swatch_type'];
		}

		$class = '';

		foreach ( $settings as $key => $setting ) {
			$valid_keys = [ "{$type}_shape", "{$type}_layout", "{$type}_column_count" ];

			if ( in_array( $key, $valid_keys, true ) ) {
				$class .= ' artbees-was-setting-' . $setting;
			}
		}

		if ( ! empty( $type ) ) {
			$class .= " artbees-was-type-{$type}";
		}

		return $class;
	}

	/**
	 * Get Product Attributes.
	 *
	 * @since 1.1.0
	 * @param string $attribute_slug  attribute slug.
	 * @return array
	 */
	public function get_product_inside_attribute( $attribute_slug ) {
		global $product;

		$product_meta = get_post_meta( $product->get_id(), '_artbees-was' );

		if ( empty( $product_meta[0][ $attribute_slug ]['swatch_type'] ) ) {
			return [];
		}

		$product_meta = $product_meta[0][ $attribute_slug ];

		return $product_meta;
	}
}
new Artbees_WC_Attribute_Frontend();
