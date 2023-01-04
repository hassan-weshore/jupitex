<?php
defined( 'ABSPATH' ) || die();

/**
 * Field Base Structure
 *
 * @package Sellkit\Artbees_WC_Attribute_Swatches\Fields
 * @since 1.1.0
 */
class Artbees_WC_Attribute_Swatches_Field_Base {

	/**
	 * Generate Select Fields.
	 *
	 * @param string $field_id  Attribute option id as field id.
	 * @since 1.1.0
	 * @return string
	 */
	public function create_field_select( $field_id ) {
		$is_parent = '';

		foreach ( $this->swatch_get_fields( $field_id ) as $key => $arg ) {
			if ( 'is_parent' === $key ) {
				$is_parent = 'data-is-parent=' . $field_id;
			}
		}

		$field = sprintf(
			'<select id="%1$s" class="artbees-was-select" name="%2$s" %3$s disabled="disabled">',
			$field_id,
			$field_id,
			$is_parent
		);

		// phpcs:disable
		if ( ! empty( $_GET['edit'] ) ) {
			$get_attr = get_option( 'artbees_wc_attributes-' . wp_unslash( $_GET['edit'] ) . '' );

			if ( ! is_string( $get_attr ) ) {
				return;
			}

			$get_attr = json_decode( $get_attr );
		}
		// phpcs:enable

		foreach ( $this->swatch_get_fields( $field_id ) as $key => $arg ) {
			if ( is_array( $arg ) ) {
				foreach ( $arg as $key => $value ) {
					$parameter = ! empty( $get_attr->$field_id ) ? $get_attr->$field_id : '';
					$selected  = strval( $parameter ) === strval( $key ) ? 'selected' : '';

					$field .= '<option value=' . $key . ' ' . $selected . '>' . $value . '</option>';
				}
			}
		}

		$field .= '</select>';

		return $field;
	}

	/**
	 * Generate Dimensions Fields.
	 *
	 * @param string $field_id  Attribute option id as field id.
	 * @since 1.1.0
	 * @return string
	 */
	public function create_field_dimensions( $field_id ) {
		$field    = sprintf( '<div id="%s" class="artbees-was-dimensions">', $field_id );
		$get_attr = [];

		// phpcs:disable
		if ( ! empty( $_GET['edit'] ) ) {
			$get_attr = get_option( 'artbees_wc_attributes-' . wp_unslash( $_GET['edit'] ) . '' );

			if ( ! is_string( $get_attr ) ) {
				return;
			}

			$get_attr = json_decode( $get_attr );
		}
		// phpcs:enable

		$arg = $this->swatch_get_fields( $field_id );

		$parameter = ! empty( $get_attr->$field_id ) ? $get_attr->$field_id : $arg['default'];

		$field .= '<div class="artbees-was-dimensions-item">';
		$field .= sprintf(
			'<input type="range" name="%1$s"  value="%2$s" min="%3$s" max="%4$s" disabled="disabled">',
			$field_id,
			$parameter,
			20,
			100
		);
		$field .= '</div>';

		$field .= '</div>';

		return $field;
	}

	/**
	 * Generate Label Fields.
	 *
	 * @param string $field_id  Attribute option id as field id.
	 * @since 1.1.0
	 * @return string
	 */
	public function create_field_label( $field_id ) {
		$field = '';
		foreach ( $this->swatch_get_fields( $field_id ) as $key => $arg ) {
			if ( 'title' === $key ) {
				$field .= '<label for=' . $field_id . '>' . $arg . '</label>';
			}
		}

		return $field;
	}

	/**
	 * Generate Description Fields.
	 *
	 * @param string $field_id  Attribute option id as field id.
	 * @since 1.1.0
	 * @return string
	 */
	public function create_field_description( $field_id ) {
		$field = '';
		foreach ( $this->swatch_get_fields( $field_id ) as $key => $arg ) {
			if ( 'description' === $key ) {
				$field .= '<p class="description">' . $arg . '</p>';
			}
		}

		return $field;
	}

	/**
	 * Return Parent ID.
	 *
	 * @param string $field_id  Attribute option id as field id.
	 * @since 1.1.0
	 * @return string
	 */
	public function create_field_parent_id( $field_id ) {
		foreach ( $this->swatch_get_fields( $field_id ) as $key => $arg ) {
			if ( 'parent' === $key ) {
				return $arg;
			}
		}
	}

	/**
	 * Generate Select Fields For Products.
	 *
	 * @param string $field_id   Attribute option id as field id.
	 * @param string $attribute  Attribute slug as field id.
	 * @since 1.1.0
	 * @return string
	 */
	public function create_product_field_select( $field_id, $attribute ) {
		$is_parent = '';

		foreach ( $this->swatch_get_fields( $field_id ) as $key => $arg ) {
			if ( 'is_parent' === $key ) {
				$is_parent = 'data-is-parent=' . $field_id;
			}
		}

		$field = sprintf(
			'<select id="%1$s" class="artbees-was-product-select" name="%2$s" %3$s>',
			$field_id,
			'artbees-was[' . $attribute . '][' . $field_id . ']',
			$is_parent
		);

		$parameter = [];

		// phpcs:disable
		if ( ! empty( $_GET['post'] ) ) {
			$parameter = get_post_meta( $_GET['post'], '_artbees-was' );
			$parameter = ! empty( $parameter[0][ $attribute ][ $field_id ] ) ? $parameter[0][ $attribute ][ $field_id ] : "";
		}
		// phpcs:enable

		foreach ( $this->swatch_get_fields( $field_id ) as $key => $arg ) {
			if ( is_array( $arg ) ) {
				foreach ( $arg as $key => $value ) {
					$selected = strval( $parameter ) === strval( $key ) ? 'selected' : '';

					$field .= '<option value=' . $key . ' ' . $selected . '>' . $value . '</option>';
				}
			}
		}

		$field .= '</select>';

		return $field;
	}

	/**
	 * Generate Dimensions Fields For Products.
	 *
	 * @param string $field_id   Attribute option id as field id.
	 * @param string $attribute  Attribute slug as field id.
	 * @since 1.1.0
	 * @return string
	 */
	public function create_product_field_dimensions( $field_id, $attribute ) {
		$field     = sprintf( '<div id="%1$s" class="%2$s">', $field_id, 'artbees-was-dimensions' );
		$parameter = [];

		// phpcs:disable
		if ( ! empty( $_GET['post'] ) ) {
			$parameter = get_post_meta( wp_unslash( $_GET['post'] ), '_artbees-was' );
		}
		// phpcs:enable

		$arg       = $this->swatch_get_fields( $field_id );
		$parameter = ! empty( $parameter[0][ $attribute ][ $field_id ] ) ? $parameter[0][ $attribute ][ $field_id ] : $arg['default'];

		$field .= '<div class="artbees-was-dimensions-item">';
		$field .= sprintf(
			'<input type="range" name="%1$s"  value="%2$s" min="%3$s" max="%4$s">',
			'artbees-was[' . $attribute . '][' . $field_id . ']',
			$parameter,
			20,
			100
		);

		$field .= '</div>';
		$field .= '</div>';

		return $field;
	}
}
