<?php
defined( 'ABSPATH' ) || die();

/**
 * Atribute Swatches Terms Fields
 *
 * @package Sellkit\Artbees_WC_Attribute_Swatches\Products
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Artbees_WC_Attribute_Swatches_Term_Fields {
	/**
	 * Get Attributes Fields.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	private $fields = [];

	/**
	 * Attribute Term Meta Name for Fields.
	 *
	 * @var string $attribute_term_meta_name
	 * @since 1.1.0
	 */
	public $attribute_term_meta_name = 'artbees_was_term_meta';

	/**
	 * Artbees_WC_Attribute_Swatches_Term_Fields constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'get_attribute_term_fields' ], 10 );
		add_action( 'admin_init', [ $this, 'add_attribute_term_fields' ], 10 );
	}

	/**
	 * Terms Fields.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function get_attribute_term_fields() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		$attributes = wc_get_attribute_taxonomies();

		if ( ! $attributes ) {
			return;
		}

		foreach ( $attributes as $attribute ) {
			$this->fields[ $attribute->attribute_id ] = 'pa_' . $attribute->attribute_name;
		}

		return $this->fields;
	}

	/**
	 * Terms Fields.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function add_attribute_term_fields() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		$attributes = wc_get_attribute_taxonomies();

		if ( ! $attributes ) {
			return;
		}

		foreach ( $attributes as $attribute ) {
			add_action( sprintf( 'pa_%s_add_form_fields', $attribute->attribute_name ), [
				$this,
				'output_attribute_term_fields',
			], 100, 2 );

			add_action( sprintf( 'pa_%s_edit_form', $attribute->attribute_name ), [
				$this,
				'output_attribute_term_fields',
			], 100, 2 );

			add_action( sprintf( 'create_pa_%s', $attribute->attribute_name ), [
				$this,
				'save_attribute_term_fields',
			] );

			add_action( sprintf( 'edited_pa_%s', $attribute->attribute_name ), [
				$this,
				'save_attribute_term_fields',
			] );

			add_filter( sprintf( 'manage_edit-pa_%s_columns', $attribute->attribute_name ), [
				$this,
				'add_attribute_columns',
			] );

			add_filter( sprintf( 'manage_pa_%s_custom_column', $attribute->attribute_name ), [
				$this,
				'add_attribute_column_content',
			], 10, 3 );
		}
	}

	/**
	 * Terms Fields Output.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function output_attribute_term_fields() {
		// phpcs:disable
		if ( empty( $_GET['taxonomy'] ) ) {
			return;
		}

		foreach ( $this->fields as $key => $value ) {
			if ( $_GET['taxonomy'] === $value ) {
				$id = $key;
			}
		}
		// phpcs:enable

		$artbees_wc_attributes = get_option( 'artbees_wc_attributes-' . $id . '' );
		$artbees_wc_attributes = json_decode( $artbees_wc_attributes );

		if ( empty( $artbees_wc_attributes->attribute_type ) ) {
			return;
		}

		switch ( $artbees_wc_attributes->attribute_type ) {
			case 'color':
				$this->output_color_attribute_term_fields();
				break;
			case 'text':
				$this->output_text_attribute_term_fields();
				break;
			case 'radio':
				$this->output_radio_attribute_term_fields();
				break;
			case 'image':
				$this->output_image_attribute_term_fields();
				break;
		}
	}

	/**
	 * Text Term Field Output.
	 *
	 * @since 1.1.0
	 */
	public function output_text_attribute_term_fields() {
		echo '<input type="hidden" name="artbees_was_term_meta[type]" value="text">';
	}

	/**
	 * Radio Term Field Output.
	 *
	 * @since 1.1.0
	 */
	public function output_radio_attribute_term_fields() {
		echo '<input type="hidden" name="artbees_was_term_meta[type]" value="radio">';
	}

	/**
	 * Image Term Field Output.
	 *
	 * @since 1.1.0
	 */
	public function output_image_attribute_term_fields() {
		$term_id = sellkit_htmlspecialchars( INPUT_GET, 'tag_ID' );

		$default = $this->generate_default_value( $term_id );
		$image   = '';

		$upload_and_edit_clasee = ' artbees-was-image-picker__upload';
		$remove_button_style    = 'style="display: none;"';

		if ( ! empty( $default['default'] ) ) {
			$image = ! empty( wp_get_attachment_image( $default['default'] ) ) ? wp_get_attachment_image( $default['default'] ) : '';

			$upload_and_edit_clasee .= ' artbees-was-image-picker__upload--edit';
			$remove_button_style     = '';
		}
		?>
		<div class="form-field <?php echo $default['class']; ?>">
			<label><?php echo __( 'Image Swatch', 'sellkit-pro' ); ?></label>
			<div class="artbees-was-image-picker">
				<div class="artbees-was-image-picker__preview">
					<?php
						echo $image;
					?>
				</div>
				<input id="artbees-was-image-picker-field" type="hidden" name="artbees_was_term_meta[image]" value="<?php echo $default['default']; ?>" class="artbees-was-image-picker__field regular-text">
				<input type="hidden" name="artbees_was_term_meta[type]" value="image">
				<a href="javascript: void(0);" class="artbees-was-image-picker__button <?php echo $upload_and_edit_clasee; ?>" title="Upload/Add Image" id="upload-artbees-was-image-picker" data-title="Upload/Add Image" data-button-text="Insert Image"><span class="dashicons dashicons-edit"></span><span class="dashicons dashicons-plus"></span></a>
				<a href="javascript: void(0);" class="artbees-was-image-picker__button artbees-was-image-picker__remove" title="Remove Image" <?php echo $remove_button_style; ?>><span class="dashicons dashicons-no"></span></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Color Term Field Output.
	 *
	 * @since 1.1.0
	 */
	public function output_color_attribute_term_fields() {
		$term_id = sellkit_htmlspecialchars( INPUT_GET, 'tag_ID' );

		$default = $this->generate_default_value( $term_id );
		?>
		<div class="form-field <?php echo $default['class']; ?>">
			<label><?php echo esc_html__( 'Color Swatch', 'sellkit-pro' ); ?></label>
			<div class="artbees-color-swatch">
				<input id="artbees-color-swatch-field" type="text" name="artbees_was_term_meta[color]" value="<?php echo esc_attr( $default['default'] ); ?>" class="artbees-color-swatch-picker">
				<input type="hidden" name="artbees_was_term_meta[type]" value="color">
			</div>
		</div>
		<?php
	}

	/**
	 * Generate default value and class for edit page.
	 *
	 * @param int $term_id Attribute term id.
	 * @since 1.1.0
	 * @return array
	 */
	public function generate_default_value( $term_id ) {
		$field_class   = '';
		$default_value = '';

		if ( empty( $term_id ) ) {
			return [
				'class' => $field_class,
				'default' => $default_value,
			];
		}

		$default_value = get_term_meta( $term_id, 'artbees_was_term_meta' );

		$value_type    = isset( $default_value[0]['type'] ) ? $default_value[0]['type'] : '';
		$field_class   = "term-{$value_type}-wrap";
		$default_value = isset( $default_value[0][ $value_type ] ) ? $default_value[0][ $value_type ] : '';

		return [
			'class' => $field_class,
			'default' => $default_value,
		];
	}

	/**
	 * Save Term Fields for Product Meta.
	 *
	 * @param int $term_id Attribute term id.
	 * @since 1.1.0
	 */
	public function save_attribute_term_fields( $term_id ) {
		// phpcs:disable
		if ( isset( $_POST[ $this->attribute_term_meta_name ] ) ) {
			$previous_termmeta = get_term_meta( $term_id, $this->attribute_term_meta_name, true );
			$previous_termmeta = $previous_termmeta ? $previous_termmeta : [];

			$term_meta_data = array_map( 'sanitize_text_field', $_POST[ $this->attribute_term_meta_name ] );

			if ( ! is_array( $term_meta_data ) ) {
				return;
			}

			// get value, sanitize, and save it into the database
			$new_termmeta = isset( $term_meta_data ) ? $term_meta_data : [];
			$termmeta     = array_replace( $previous_termmeta, $new_termmeta );

			update_term_meta( $term_id, $this->attribute_term_meta_name, $termmeta );
		}
		// phpcs:enable
	}

	/**
	 * Save Attribute Columns.
	 *
	 * @param array $columns Attribute terms list columns.
	 * @return array
	 * @since 1.1.0
	 */
	public function add_attribute_columns( $columns ) {
		// phpcs:disable
		if ( ! isset( $_GET['taxonomy'] ) ) {
			return $columns;
		}

		foreach ( $this->fields as $key => $value ) {
			// phpcs:disable
			if ( $_GET['taxonomy'] === $value ) {
				$id = $key;
			}
		}
		// phpcs:enable

		$get_attr = get_option( 'artbees_wc_attributes-' . $id . '' );
		$get_attr = json_decode( $get_attr );

		if ( empty( $get_attr->attribute_type ) ) {
			return $columns;
		}

		if ( in_array( $get_attr->attribute_type, [ 'radio', 'text' ], true ) ) {
			return $columns;
		}

		$columns['artbees-was-swatch'] = __( 'Swatch', 'sellkit-pro' );

		return $columns;
	}

	/**
	 * Add Content to Attribute Columns.
	 *
	 * @param string $content     Attribute terms list content.
	 * @param string $column_name Attribute terms list column name.
	 * @param int    $term_id     Attribute term id.
	 * @return string
	 * @since 1.1.0
	 * @SuppressWarnings(PHPMD)
	 */
	public function add_attribute_column_content( $content, $column_name, $term_id ) {
		// phpcs:disable
		if ( ! isset( $_GET[ 'taxonomy' ] ) ) {
			return $content;
		}
		// phpcs:disable

		$attribute_terms = get_term( $term_id , wp_unslash( $_GET['taxonomy'] ) );

		if ( ! empty( $attribute_terms ) ) {
			$swatch_value = get_term_meta( $attribute_terms->term_id, $this->attribute_term_meta_name, true );

			$this->generate_attribute_column_content( $swatch_value );
		}
		// phpcs:enable

		return $content;
	}

	/**
	 * Generate Attribute Column Content.
	 *
	 * @param array $swatch_value  Swatch content value for attribute term.
	 * @return string
	 * @since 1.1.0
	 */
	public function generate_attribute_column_content( $swatch_value ) {
		if ( empty( $swatch_value ) ) {
			return;
		}

		foreach ( $swatch_value as $key => $value ) {
			switch ( $key ) {
				case 'color':
					$this->generate_attribute_color_column_content_output( $value );
					break;
				case 'image':
					$this->generate_attribute_image_column_content_output( $value );
					break;
			}
		}
	}

	/**
	 * Generate Attribute Column Content for Color.
	 *
	 * @param array $value  Swatch content value.
	 * @since 1.1.0
	 */
	public function generate_attribute_color_column_content_output( $value ) {
		printf( '<span class="artbees-was-color-content" style="background-color: %s"><span>', $value );
	}

	/**
	 * Generate Attribute Column Content for Image.
	 *
	 * @param array $value  Swatch content value.
	 * @since 1.1.0
	 */
	public function generate_attribute_image_column_content_output( $value ) {
		printf(
			'<div class="artbees-was-image-content" >%s<div>',
			! empty( wp_get_attachment_image( $value ) ) ? wp_get_attachment_image( $value ) : ''
		);
	}
}
new Artbees_WC_Attribute_Swatches_Term_Fields();
