<?php

defined( 'ABSPATH' ) || die();
/**
 * Prodcuts Atribute Swatches
 *
 * @package Sellkit\Artbees_WC_Attribute_Swatches\Products
 * @since 1.1.0
 */
class Artbees_WC_Attribute_Swatches_Products extends Artbees_WC_Attribute_Swatches_Products_Fields {
	/**
	 * Swatch data for current product
	 *
	 * @var array $swatch_data
	 */
	public $swatch_data = [];

	/**
	 * Artbees_WC_Attribute_Swatches_Products constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'woocommerce_product_write_panel_tabs', [ $this, 'product_tab' ] );
		add_action( 'woocommerce_product_data_panels', [ $this, 'product_tab_fields' ] );
		add_action( 'woocommerce_process_product_meta', [ $this, 'save_product_fields' ] );
		add_action( 'wp_ajax_artbees_product_swatches_generate_product_options', [ $this, 'generate_product_options' ] );
	}

	/**
	 * Products: Return Tab Item.
	 *
	 * @since 1.1.0
	 */
	public function product_tab() {
		printf(
			'<li class="%1$s-options-tab show_if_variable"><a href="#%1$s-options"><span>%2$s</span></a></li>',
			'artbees-was',
			__( 'Swatches', 'sellkit-pro' )
		);
	}

	/**
	 * Products: Return Tab Fiels.
	 *
	 * @return string
	 */
	public function product_tab_fields() {
		$product_id = sellkit_htmlspecialchars( INPUT_GET, 'post' );

		if ( ! isset( $product_id ) ) {
			return;
		}

		$fields = '<div id="artbees-was-options" class="panel wc-metaboxes-wrapper">';

		$attributes = $this->get_attributes_for_product( $product_id );

		if ( empty( $attributes ) ) {
			return;
		}

		$fields .= '<div class="wc-metaboxes">';

		foreach ( $attributes as $attribute ) {
			// phpcs:disable
			$attributes_fields[ $attribute['slug'] ] = $this->swatch_add_field( $attribute['slug'] );
			// phpcs:enable

			$get_attribute = get_post_meta( $product_id, '_artbees-was' );
			$get_attribute = ! empty( $get_attribute[0][ $attribute['slug'] ]['swatch_type'] ) ? ' ' . $get_attribute[0][ $attribute['slug'] ]['swatch_type'] . __( ' Swatch', 'sellkit-pro' ) : '';

			$fields .= sprintf(
				'<div data-taxonomy="%1$s" data-product-id="%2$s" class="%1$s wc-metabox closed taxonomy artbees-was-attribute-wrapper postbox">',
				esc_attr( $attribute['slug'] ),
				esc_attr( $product_id )
			);

			$fields .= sprintf(
				'<h3 class="attribute-name artbees-was-attribute-name"><div class="handlediv" title="%1$s" aria-expanded="true"></div><strong>%2$s</strong><span class="artbees-was-swatch-type">%3$s</span></h3>',
				esc_html__( 'Click to toggle', 'sellkit-pro' ),
				$attribute['label'] . ':',
				! empty( $get_attribute ) ? $get_attribute : esc_html__( ' Default', 'sellkit-pro' )
			);

			$fields .= '<div class="wc-metabox-content" style="display: none;"><table cellpadding="0" cellspacing="0" class="artbees-was-attributes"><tbody>';

			foreach ( $attributes_fields as $key => $field ) {
				$fields_value = sprintf(
					'<tr class="%1$s"><td>%2$s</td><td>%3$s</td></tr>',
					'artbees-was-attribute-row artbees-was-attributes_' . str_replace( '_', '-', $key ) . '',
					$field['label'],
					$field['field']
				);
			}

			$fields .= $fields_value;
			$fields .= $this->swatch_add_types_field( $attribute['slug'] );
			$fields .= sprintf(
				'<tr id="artbees-was-terms-options" class="%1$s">%2$s</tr>',
				'artbees-was-attribute-row artbees-was-attributes_' . $attribute['slug'] . '',
				$this->create_product_options( $attribute )
			);

			$fields .= '</tbody></table></div>';
			$fields .= '</div>';
		}

		$fields .= '</div>';
		$fields .= '</div>';

		echo $fields;
	}

	/**
	 * Products: Products Custom Fields
	 *
	 * @param int $product_id Product id.
	 * @since 1.1.0
	 */
	public function save_product_fields( $product_id ) {
		$product_settings = [];

		// phpcs:disable
		if ( isset( $_POST['artbees-was'] ) ) {
			if ( empty( $_POST['artbees-was'] ) ) {
				return;
			}

			$product = wc_get_product( $product_id );

			foreach ( wp_unslash( $_POST['artbees-was'] ) as $key => $value ) {
				$product_settings[ $key ] = [ 'swatch_type' => '' ];

				if ( ! empty( $value['swatch_type'] ) ) {
					$product_settings[ $key ] = $value;
				}
			}

			$product->update_meta_data( '_artbees-was', $product_settings );
			$product->save();
		}
		// phpcs:enable
	}

	/**
	 * Products: Get variation attributes for product
	 *
	 * @param int $product_id Product id.
	 * @return bool|array
	 */
	public function get_attributes_for_product( $product_id ) {
		if ( ! $product_id ) {
			return false;
		}

		$product              = wc_get_product( $product_id );
		$attributes           = $product->get_attributes();
		$variation_attributes = [];

		if ( ! $attributes ) {
			return false;
		}

		foreach ( $attributes as $attribute ) {
			if ( ! $attribute->get_variation() ) {
				continue;
			}

			$variation_attribute = [
				'options' => [],
			];

			$options          = [];
			$attribute_object = [];

			if ( $attribute->is_taxonomy() ) {
				$variation_attribute['slug'] = $attribute->get_name();

				$options          = wp_get_post_terms( $product_id, $attribute->get_name() );
				$attribute_object = get_taxonomy( $attribute->get_name() );

				$variation_attribute['label'] = $attribute_object->label;

				if ( $options ) {
					foreach ( $options as $option ) {
						$variation_attribute['options'][] = [
							'id'   => $option->term_id,
							'slug' => $option->slug,
							'name' => $option->name,
							'term' => $option,
						];
					}
				}
			}

			if ( isset( $variation_attribute['slug'] ) ) {
				$variation_attributes[ $variation_attribute['slug'] ] = $variation_attribute;
			}
		}

		return $variation_attributes;
	}

	/**
	 * Products: Add Swatch Type Form.
	 *
	 * @since 1.1.0
	 * @param string $attribute attribute slug.
	 * @return array
	 */
	public function swatch_add_field( $attribute ) {
		$artbees_attributes = new Artbees_WC_Attribute();

		$fields['label'] = sprintf(
			'<label for="attribute_type">%s</label>',
			__( 'Swatch type', 'sellkit-pro' )
		);

		$fields['field'] = '<div class="form-field">';

		$fields['field'] .= sprintf(
			'<select name="%s" id="product_attribute_type" class="postform">',
			'artbees-was[' . $attribute . '][swatch_type]'
		);

		foreach ( $artbees_attributes->product_swatches_types() as $key => $types ) {
			// phpcs:disable
			$parameter = get_post_meta( wp_unslash( $_GET['post'] ), '_artbees-was' );
			$parameter = ! empty( $parameter[0][ $attribute ]['swatch_type'] ) ? $parameter[0][ $attribute ]['swatch_type'] : '';
			// phpcs:enable

			$selected = $key === $parameter ? 'selected' : '';

			$fields['field'] .= sprintf(
				'<option value="%1$s" %2$s>%3$s</option>',
				$key,
				$selected,
				$types
			);
		}

		$fields['field'] .= '</select>';
		$fields['field'] .= '</div>';

		return $fields;
	}

	/**
	 * Products: Add Swatch Types Fields.
	 *
	 * @since 1.1.0
	 * @param string $attribute attribute slug.
	 * @return string
	 */
	public function swatch_add_types_field( $attribute ) {
		$artbees_attributes = new Artbees_WC_Attribute();

		$fields = '';

		foreach ( $artbees_attributes->product_swatches_types() as $key => $types ) {
			if ( ! empty( $key ) ) {
				$type_classes   = 'Artbees_WC_Attribute_Swatches_Type_' . ucwords( $key );
				$attribute_type = new $type_classes();

				$fields .= $attribute_type->add_products_fields( $attribute );
			}
		}

		return $fields;
	}

	/**
	 * Products: Get Product Swatch Data for Attribute.
	 *
	 * @since 1.1.0
	 * @param int    $product_id     Product id.
	 * @param string $attribute_slug Attribute slug.
	 * @return array
	 */
	public function get_product_swatch_data( $product_id, $attribute_slug ) {
		if ( ! isset( $this->swatch_data[ $product_id ] ) ) {
			$product                          = wc_get_product( $product_id );
			$this->swatch_data[ $product_id ] = $product->get_meta( '_artbees-was', true );
		}

		if ( isset( $this->swatch_data[ $product_id ][ $attribute_slug ] ) ) {
			return $this->swatch_data[ $product_id ][ $attribute_slug ];
		}

		return $this->swatch_data;
	}

	/**
	 * Products: Generate Attributes Terms Fields For Products.
	 *
	 * @since 1.1.0
	 * @param array $attribute Attribute data.
	 * @return string
	 */
	public function create_product_options( $attribute ) {
		if ( empty( $attribute ) ) {
			return;
		}

		$product_id = sellkit_htmlspecialchars( INPUT_GET, 'post' );

		if ( ! isset( $product_id ) ) {
			return;
		}

		$saved_values = $this->get_product_swatch_data( $product_id, $attribute['slug'] );

		$get_attribute = get_post_meta( $product_id, '_artbees-was' );
		$attribue_type = ! empty( $get_attribute[0][ $attribute['slug'] ]['swatch_type'] ) ? $get_attribute[0][ $attribute['slug'] ]['swatch_type'] : '';

		$field = '<td class="artbees-was-swatch-options-wrapper">';

		$field .= sprintf(
			'<div class="%1$s">',
			'artbees-was-swatch-options'
		);

		$field_group = '';

		foreach ( $attribute['options'] as $option ) {
			$field_option = '<div class="artbees-was-swatch-options-items">';

			$field_option .= $this->output_attribute_product_term_fields( $attribue_type, $option['slug'], $option['term']->taxonomy, $saved_values, $option['name'] );

			$field_option .= '</div>';
			$field_group  .= $field_option;
		}

		$field .= $field_group;
		$field .= '</div>';
		$field .= '</td>';

		return $field;
	}


	/**
	 * Products: Generate Attributes Terms Fields For Products.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function generate_product_options() {
		$product_id = sellkit_htmlspecialchars( INPUT_POST, 'product_id' );

		if ( ! isset( $product_id ) ) {
			return;
		}

		$attributes = $this->get_attributes_for_product( sanitize_text_field( $product_id ) );

		if ( empty( $attributes ) ) {
			return;
		}

		$terms_taxonomy = sellkit_htmlspecialchars( INPUT_POST, 'terms_taxonomy' );

		$attributes   = $attributes [ sanitize_text_field( $terms_taxonomy ) ];
		$saved_values = $this->get_product_swatch_data( sanitize_text_field( $product_id ), $attributes['slug'] );

		$attribue_type = sellkit_htmlspecialchars( INPUT_POST, 'swatch_type' );

		$field = '<td class="artbees-was-swatch-options-wrapper">';

		$field .= sprintf(
			'<div class="%1$s">',
			'artbees-was-swatch-options'
		);

		$field_group = '';

		foreach ( $attributes['options'] as $option ) {
			$field_option = '<div class="artbees-was-swatch-options-items">';

			$field_option .= $this->output_attribute_product_term_fields( $attribue_type, $option['slug'], $option['term']->taxonomy, $saved_values, $option['name'] );

			$field_option .= '</div>';
			$field_group  .= $field_option;
		}

		$field .= $field_group;
		$field .= '</div>';
		$field .= '</td>';

		wp_send_json_success( $field );

	}
}
new Artbees_WC_Attribute_Swatches_Products();

