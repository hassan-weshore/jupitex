<?php

defined( 'ABSPATH' ) || die();

use Elementor\Control_Base_Multiple;

/**
 * Sellkit File Uploader.
 *
 * A Sellkit File Uploader Control will let you upload any type of file in a customizable location.
 *
 * @since 1.5.0
 *
 * @param string $label       Optional. The label that appears above of the field. Default is empty.
 * @param string $title       Optional. The field title that appears on mouse hover. Default is empty.
 * @param string $description Optional. The description that appears below the field. Default is empty.
 * @param bool   $show_label  Optional. Whether to display the label. Default is true.
 * @param bool   $label_block Optional. Whether to display the label in a separate line. Default is true.
 * @param string $separator   Optional. Set the position of the control separator.
 *                            Available values are 'default', 'before', 'after' and 'none'. 'default' will position the separator
 *                            depending on the control type. 'before' / 'after' will position the separator before/after the control.
 *                            'none' will hide the separator. Default is 'default'.
 *
 * @return array Including file's string properties Name and Path : [ 'name' => '', 'path' => ''].
 */
class Sellkit_Elementor_Controls_File_Uploader extends Control_Base_Multiple {

	/**
	 * File uplodaer control constructor.
	 *
	 * Adds ajax hook for file upload.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		parent::__construct();

		// Register AJAX action.
		add_action( 'wp_ajax_sellkit_control_file_upload', [ $this, 'handle_file_upload' ] );

		// Define nonce for file upload ajax action.
		add_action( 'elementor/editor/after_enqueue_scripts', function() {
			wp_localize_script(
				'sellkit-editor',
				'sellkitNonceEditorFileUploader',
				[ wp_create_nonce( 'sellkit_fileuploader_editor' ) ]
			);
		}, 20 );
	}

	/**
	 * Get type of the control, used in declaring "type" when creating Elementor control.
	 *
	 * @return string
	 *
	 * @since 1.5.0
	 */
	public function get_type() {
		return 'sellkit_file_uploader';
	}

	/**
	 * Get array of default values for this control.
	 *
	 * @return array
	 *
	 * @since 1.5.0
	 */
	public function get_default_value() {
		return [
			'files' => [],
		];
	}

	/**
	 * Get array of default settings for this control.
	 *
	 * @return array
	 *
	 * @since 1.5.0
	 */
	protected function get_default_settings() {
		return [
			'label_block' => false,
		];
	}

	/**
	 * Echo the template of this control in the Elementor's editor panel.
	 *
	 * @since 1.5.0
	 */
	public function content_template() {
		?>
		<div class="elementor-control-field sellkit-control-file-uploader">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<div class="elementor-button-wrapper">
					<button type="button" class="elementor-button elementor-button-default elementor-repeater-add sellkit-control-file-uploader-button">
						<input
							type="file"
							data-max-upload-limit="<?php echo wp_max_upload_size(); ?>"
							data-ajax-url="<?php echo admin_url( 'admin-ajax.php' ); ?>"
							class="sellkit-control-file-uploader-input"/>
						<i class="fa fa-upload" aria-hidden="true"></i>
						<?php echo esc_html__( 'Upload File', 'sellkit' ); ?>
					</button>
					<div class="sellkit-control-file-uploader-progress">
						<?php echo esc_html__( 'Uploading...', 'sellkit' ); ?>
						<span class="fa fa-spinner fa-spin"></span>
					</div>
					<div class="sellkit-control-file-uploader-value">
						<span></span>
						<span class="fa fa-trash"></span>
					</div>
				</div>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<div class="sellkit-control-file-uploader-warning">
			<div class="elementor-panel-alert elementor-panel-alert-danger">
				<ul>
					<li class="sellkit-control-file-uploader-warning-size">
						<?php esc_html_e( 'Maximum allowed file size is', 'sellkit' ); ?>
						<strong><?php echo round( wp_max_upload_size() / ( 1024 * 1024 ), 2 ); ?>
							<?php
								/* translators: here "MB" means megabytes */
								echo esc_html__( 'MB', 'sellkit' );
							?>
						</strong>
					</li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles uploading of the selected file by answering the AJAX call.
	 *
	 * @since 1.5.0
	 */
	public static function handle_file_upload() {
		check_ajax_referer( 'sellkit_fileuploader_editor', 'nonce' );

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$movefile = wp_handle_upload( $_FILES['file'],
			[
				'test_form' => false,
				'mimes'     => get_allowed_mime_types(),
				'unique_filename_callback' => [ self::class, 'rename_uploading_file' ],
			]
		);

		if ( $movefile && ! isset( $movefile['error'] ) ) {
			wp_send_json_success(
				[
					'path' => $movefile['file'],
					'name' => pathinfo( $movefile['file'], PATHINFO_BASENAME ),
				]
			);
		}

		wp_send_json_error( $movefile['error'] );
	}

	/**
	 * Used in override propery 'unique_filename_callback' of wp_handle_upload function.
	 *
	 * @param string $dir File directory.
	 * @param string $name File name.
	 * @param string $ext File extension.
	 * @return string File name.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function rename_uploading_file( $dir, $name, $ext ) {
		return str_replace( $ext, '', $name ) . '__' . uniqid() . $ext;
	}
}
