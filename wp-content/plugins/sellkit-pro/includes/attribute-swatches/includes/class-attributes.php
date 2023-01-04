<?php
defined( 'ABSPATH' ) || die();

/**
 * Atribute Swatches
 *
 * @package Sellkit\Artbees_WC_Attribute_Swatches\Attributes
 * @since 1.1.0
 */
class Artbees_WC_Attribute {
	/**
	 * Artbees_WC_Attribute constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'woocommerce_after_add_attribute_fields', [ $this, 'swatch_add_field' ], 10 );
		add_action( 'woocommerce_after_edit_attribute_fields', [ $this, 'swatch_edit_field' ] );

		add_action( 'woocommerce_attribute_added', [ $this, 'swatch_save_field' ], 10, 2 );
		add_action( 'woocommerce_attribute_updated', [ $this, 'swatch_update_field' ] );
		add_action( 'woocommerce_attribute_deleted', [ $this, 'swatch_delete_field' ], 20, 2 );
	}

	/**
	 * Add Swatch Type Form.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function swatch_add_field() {
		?>
		<div class="form-field ">
			<label for="attribute_type"><?php echo __( 'Swatch type', 'sellkit-pro' ); ?></label>
			<select name="attribute_type" id="attribute_type" class="postform">
				<?php
					foreach ( $this->swatches_types() as $key => $types ) {
						echo '<option value=' . $key . '>' . $types . '</option>';
					}
				?>
			</select>
			<p class="description"><?php echo __( 'Choose the type of swatches to use for this attribute.', 'sellkit-pro' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Edit Swatch Type Form.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function swatch_edit_field() {
		?>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="attribute_type"><?php echo __( 'Swatch type', 'sellkit-pro' ); ?></label>
			</th>
			<td>
				<select name="attribute_type" id="attribute_type" class="postform">
					<?php
						// phpcs:disable
						$get_attr  = get_option( 'artbees_wc_attributes-' . wp_unslash( $_GET['edit'] ) . '' );
						$get_attr  = json_decode( $get_attr );
						$attr_type = empty( $get_attr ) ? '' : $get_attr->attribute_type;
						// phpcs:enable

						foreach ( $this->swatches_types() as $key => $types ) {
							$selected = $attr_type === $key ? 'selected' : '';

							echo '<option value=' . $key . ' ' . $selected . '>' . $types . '</option>';
						}
					?>
				</select>
				<p class="description"><?php echo __( 'Choose the type of swatches to use for this attribute.', 'sellkit-pro' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save Attribute Field in Database.
	 *
	 * @param int   $attribute_id  Attribute id.
	 * @param array $attribute     Attribute data.
	 * @since 1.1.0
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	public function swatch_save_field( $attribute_id, $attribute ) {
		// phpcs:disable
		if ( isset( $_POST ) ) {
			$option = 'artbees_wc_attributes-' . $attribute_id . '';

			// phpcs:disable
			update_option( $option,  sanitize_text_field( json_encode( $_POST ) ) );
		}
		// phpcs:enable
	}

	/**
	 * Update Attribute Field in Database.
	 *
	 * @param int $attribute_id Attribute id.
	 * @since 1.2.3
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	public function swatch_update_field( $attribute_id ) {
		$value     = sellkit_htmlspecialchars( INPUT_POST, 'attribute_type' );
		$option    = 'artbees_wc_attributes-' . $attribute_id . '';
		$attribute = [];

		wp_cache_flush();

		if ( empty( $_POST ) ) { // phpcs:ignore
			return;
		}

		$saved_options = get_option( $option, true );
		$get_value     = ! empty( $_POST['attribute_type'] ) ? htmlspecialchars( $_POST['attribute_type'] ) : ''; // phpcs:ignore

		update_option( $option, json_encode( array_map( 'sanitize_text_field', $_POST ) ) ); // phpcs:ignore

		if ( ! empty( $saved_options ) ) {
			$saved_options = json_decode( $saved_options, true );
		}

		if ( isset( $saved_options['attribute_type'] ) && $saved_options['attribute_type'] === $get_value ) {
			return;
		}

		$attribute = wc_get_attribute( $attribute_id );
		$terms     = get_terms( [
			'taxonomy' => $attribute->slug,
		] );

		if ( empty( $terms ) ) {
			return;
		}

		foreach ( $terms as $term ) {
			$saved_value  = get_term_meta( $term->term_id, 'artbees_was_term_meta', true );
			$meta['type'] = $value;

			if ( isset( $saved_value[ $value ] ) ) {
				$meta[ $value ] = $saved_value[ $value ];
			}

			update_term_meta( $term->term_id, 'artbees_was_term_meta', $meta );
		}
	}

	/**
	 * Delete Attribute Fields in Database.
	 *
	 * @param int   $attribute_id  Attribute id.
	 * @param array $attribute     Attribute data.
	 * @since 1.1.0
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	public function swatch_delete_field( $attribute_id, $attribute ) {
		delete_option( 'artbees_wc_attributes-' . $attribute_id . '' );
	}

	/**
	 * Swatches Types List.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function swatches_types() {
		$swatches_types = [
			'' => esc_html__( 'None', 'sellkit-pro' ),
			'color' => esc_html__( 'Color Swatch', 'sellkit-pro' ),
			'image' => esc_html__( 'Image Swatch', 'sellkit-pro' ),
			'radio' => esc_html__( 'Radio Swatch', 'sellkit-pro' ),
			'text' => esc_html__( 'Text Swatch', 'sellkit-pro' ),
		];

		return $swatches_types;
	}

	/**
	 * Swatches Types List for product edit page.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function product_swatches_types() {
		$swatches_types = [
			'' => esc_html__( 'Default', 'sellkit-pro' ),
			'color' => esc_html__( 'Color Swatch', 'sellkit-pro' ),
			'image' => esc_html__( 'Image Swatch', 'sellkit-pro' ),
			'radio' => esc_html__( 'Radio Swatch', 'sellkit-pro' ),
			'text' => esc_html__( 'Text Swatch', 'sellkit-pro' ),
		];

		return $swatches_types;
	}
}
new Artbees_WC_Attribute();
