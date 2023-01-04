<?php
defined( 'ABSPATH' ) || die();

/**
 * Atribute Swatch Type Color
 *
 * @package Sellkit\Artbees_WC_Attribute_Swatches\Fields
 * @since 1.1.0
 */
class Artbees_WC_Attribute_Swatches_Type_Radio extends Artbees_WC_Attribute_Swatches_Field_Base {
	/**
	 * Fields IDs.
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 */
	private const FIELD = [
		'radio_catalog',
		'radio_catalog_sub_field',
		'radio_layout',
		'radio_column_count',
	];

	/**
	 * Toggle box IDs.
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 */
	private const TOGGLE_BOX = [
		'radio_column_count',
		'radio_catalog_sub_field',
	];

	private const TYPE = 'radio';

	/**
	 * Artbees_WC_Attribute_Swatches_Type_Radio constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'woocommerce_after_add_attribute_fields', [ $this, 'get_add_fields' ], 10 );
		add_action( 'woocommerce_after_edit_attribute_fields', [ $this, 'get_edit_fields' ] );
	}

	/**
	 * Generate All Fields.
	 *
	 * @since 1.1.0
	 */
	public function get_add_fields() {
		foreach ( self::FIELD as $field_id ) {
			$output = sprintf(
				'<div class="form-field artbees-form-field" data-conditional="%1$s" %2$s>%3$s %4$s %5$s</div>',
				self::TYPE,
				in_array( $field_id, self::TOGGLE_BOX, true ) ? 'data-parent=' . $this->create_field_parent_id( $field_id ) : '',
				$this->create_field_label( $field_id ),
				$this->create_field_select( $field_id ),
				$this->create_field_description( $field_id )
			);

			echo $output;
		}
	}

	/**
	 * Generate All Fields for Edit.
	 *
	 * @since 1.1.0
	 */
	public function get_edit_fields() {
		foreach ( self::FIELD as $field_id ) {
			$output = sprintf(
				'<tr class="form-field artbees-form-field" data-conditional="%1$s" %2$s><th scope="row" valign="top">%3$s</th><td>%4$s %5$s</td></tr>',
				self::TYPE,
				in_array( $field_id, self::TOGGLE_BOX, true ) ? 'data-parent=' . $this->create_field_parent_id( $field_id ) : '',
				$this->create_field_label( $field_id ),
				$this->create_field_select( $field_id ),
				$this->create_field_description( $field_id )
			);

			echo $output;
		}
	}

	/**
	 * Generate All Fields for Products.
	 *
	 * @param string $attribute  Attribute slug.
	 * @since 1.1.0
	 * @return string
	 */
	public function add_products_fields( $attribute ) {
		$output = '';

		foreach ( self::FIELD as $field_id ) {
			$output .= sprintf(
				'<tr class="form-field artbees-form-field" data-conditional="%1$s" data-taxonomy="%2$s" %3$s><td>%4$s</td><td>%5$s</td><tr>',
				self::TYPE,
				$attribute,
				in_array( $field_id, self::TOGGLE_BOX, true ) ? 'data-parent=' . $this->create_field_parent_id( $field_id ) : '',
				$this->create_field_label( $field_id ),
				$this->create_product_field_select( $field_id, $attribute )
			);

		}

		return $output;
	}

	/**
	 * List of All Fields.
	 *
	 * @param string $field_id  Attribute slug as field id.
	 * @since 1.1.0
	 * @return array
	 */
	public function swatch_get_fields( $field_id ) {
		$field = [
			'radio_catalog' => [
				'title' => __( 'Show in catalog', 'sellkit-pro' ),
				'type'  => 'select',
				'is_parent' => true,
				'options' => [
					'' => __( 'No', 'sellkit-pro' ),
					'1' => __( 'Yes', 'sellkit-pro' ),
				],
			],
			'radio_catalog_sub_field' => [
				'title' => __( 'Click behaviour in catalog', 'sellkit-pro' ),
				'type'  => 'select',
				'parent' => 'radio_catalog',
				'options' => [
					'link' => __( 'Link to the variable product', 'sellkit-pro' ),
					'image' => __( 'Switch product image', 'sellkit-pro' ),
				],
				'description' => __( 'will be shown when "Show in catalog" is enabled', 'sellkit-pro' ),
			],
			'radio_layout' => [
				'title' => __( 'Layout', 'sellkit-pro' ),
				'type'  => 'select',
				'is_parent' => true,
				'options' => [
					'horizontal' => __( 'Horizontal', 'sellkit-pro' ),
					'vertical' => __( 'Vertical', 'sellkit-pro' ),
					'column' => __( 'Column', 'sellkit-pro' ),
				],
			],
			'radio_column_count' => [
				'title' => __( 'Column count', 'sellkit-pro' ),
				'type'  => 'select',
				'parent' => 'radio_layout',
				'options' => [
					'2' => __( '2', 'sellkit-pro' ),
					'3' => __( '3', 'sellkit-pro' ),
					'4' => __( '4', 'sellkit-pro' ),
					'5' => __( '5', 'sellkit-pro' ),
					'6' => __( '6', 'sellkit-pro' ),
				],
			],
		];

		return $field[ $field_id ];
	}
}
new Artbees_WC_Attribute_Swatches_Type_Radio();
