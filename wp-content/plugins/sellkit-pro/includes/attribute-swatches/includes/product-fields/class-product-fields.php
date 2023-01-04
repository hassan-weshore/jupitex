<?php
defined( 'ABSPATH' ) || die();

/**
 * Prodcuts Atribute Swatches Fields
 *
 * @package Sellkit\Artbees_WC_Attribute_Swatches\Products
 * @since 1.1.0
 */
class Artbees_WC_Attribute_Swatches_Products_Fields {

	/**
	 * Get Terms Swatch Type.
	 *
	 * @since 1.1.0
	 * @param string $attribue_type Attribute swatches type.
	 * @param string $slug          Attribute term slug.
	 * @param string $taxonomy      Attribute term taxonomy name.
	 * @param string $saved_values  Attribute term saved value.
	 * @param string $option        Attribute term option name.
	 * @return string
	 */
	public function output_attribute_product_term_fields( $attribue_type, $slug, $taxonomy, $saved_values, $option ) {
		switch ( $attribue_type ) {
			case 'color':
				return $this->output_color_attribute_product_term_fields( $slug, $taxonomy, $saved_values, $option );
			case 'image':
				return $this->output_image_attribute_product_term_fields( $slug, $taxonomy, $saved_values, $option );
		}
	}

	/**
	 * Get Terms Swatch Type Image.
	 *
	 * @since 1.1.0
	 * @param string $slug          Attribute term slug.
	 * @param string $taxonomy      Attribute term taxonomy name.
	 * @param string $saved_values  Attribute term saved value.
	 * @param string $option        Attribute term option name.
	 * @return string
	 */
	public function output_image_attribute_product_term_fields( $slug, $taxonomy, $saved_values, $option ) {
		$saved_image = ! empty( $saved_values[ $slug ]['image'] ) ? $saved_values[ $slug ]['image'] : '';
		$saved_image = wp_get_attachment_image( $saved_image );

		$image_field = $this->output_label_attribute_product_term_fields( $option );

		$image_field .= '<div class="form-field">';
		$image_field .= '<div class="artbees-was-image-picker">';
		$image_field .= sprintf(
			'<div class="artbees-was-image-picker__preview">%s</div>',
			! empty( $saved_image ) ? $saved_image : ''
		);

		$image_field .= sprintf(
			'<input id="artbees-was-image-picker-field" type="hidden" name="%1$s" value="%2$s" class="artbees-was-image-picker__field regular-text">',
			'artbees-was[' . $taxonomy . '][' . $slug . '][image]',
			! empty( $saved_values[ $slug ]['image'] ) ? $saved_values[ $slug ]['image'] : ''
		);

		$image_field .= sprintf(
			'<input type="hidden" name="%s" value="image">',
			'artbees-was[' . $taxonomy . '][' . $slug . '][type]'
		);

		$image_field .= sprintf(
			'<a href="javascript: void(0);" class="%1$s" title="%2$s" id="%3$s" data-title="%2$s" data-button-text="%4$s"><span class="%5$s"></span><span class="%6$s"></span></a>',
			! empty( $saved_image ) ? 'artbees-was-image-picker__button artbees-was-image-picker__upload artbees-was-image-picker__upload--edit' : ' artbees-was-image-picker__button artbees-was-image-picker__upload',
			__( 'Upload/Add Image', 'sellkit-pro' ),
			'upload-artbees-was-image-picker',
			__( 'Insert Image', 'sellkit-pro' ),
			'dashicons dashicons-edit',
			'dashicons dashicons-plus'
		);

		$image_field .= sprintf(
			'<a href="javascript: void(0);" class="%1$s" title="%2$s" style="%3$s"><span class="%4$s"></span></a>',
			'artbees-was-image-picker__button artbees-was-image-picker__remove',
			__( 'Remove Image', 'sellkit-pro' ),
			! empty( $saved_image ) ? 'display: block;' : 'display: none;',
			'dashicons dashicons-no'
		);

		$image_field .= '</div>';
		$image_field .= '</div>';

		return $image_field;
	}


	/**
	 * Get Terms Swatch Type Color.
	 *
	 * @since 1.1.0
	 * @param string $slug          Attribute term slug.
	 * @param string $taxonomy      Attribute term taxonomy name.
	 * @param string $saved_values  Attribute term saved value.
	 * @param string $option        Attribute term option name.
	 * @return string
	 */
	public function output_color_attribute_product_term_fields( $slug, $taxonomy, $saved_values, $option ) {
		$saved_color = ! empty( $saved_values[ $slug ]['color'] ) ? $saved_values[ $slug ]['color'] : '';
		$color_field = $this->output_label_attribute_product_term_fields( $option );

		$color_field .= '<div class="form-field">';
		$color_field .= '<div class="artbees-color-swatch">';
		$color_field .= sprintf(
			'<input id="artbees-color-swatch-field" type="text" name="%1$s" value="%2$s" class="artbees-color-swatch-picker">',
			'artbees-was[' . $taxonomy . '][' . $slug . '][color]',
			$saved_color
		);

		$color_field .= sprintf(
			'<input type="hidden" name="%s" value="color">',
			"artbees-was[' $taxonomy '][' $slug '][type]"
		);

		$color_field .= '</div>';
		$color_field .= '</div>';

		return $color_field;
	}

	/**
	 * Get Terms Label.
	 *
	 * @since 1.1.0
	 * @param string $option  Attribute term option name.
	 * @return string
	 */
	public function output_label_attribute_product_term_fields( $option ) {
		$label_field = sprintf(
			'<strong class="artbees-was-swatch-option-label">%s</strong>',
			$option
		);

		return $label_field;
	}
}
new Artbees_WC_Attribute_Swatches_Products_Fields();
