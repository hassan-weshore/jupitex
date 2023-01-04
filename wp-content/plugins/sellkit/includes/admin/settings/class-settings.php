<?php

namespace Sellkit\Admin\Settings;

defined( 'ABSPATH' ) || die();

/**
 * Steps class.
 *
 * @since 1.1.0
 */
class Sellkit_Admin_Settings {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Admin_Settings
	 */
	private static $instance = null;

	/**
	 * Sellkit_Admin_Settings constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_sellkit_save_settings', [ $this, 'save_settings' ] );
		add_action( 'wp_ajax_sellkit_get_settings', [ $this, 'get_settings' ] );

		add_action( 'wp_ajax_sellkit_remove_content_box', [ $this, 'remove_content_box' ] );

		add_action( 'wp_ajax_sellkit_settings_get_templates', [ $this, 'get_templates' ] );
	}

	/**
	 * Get the class instance.
	 *
	 * @since 1.1.0
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Saving Settings.
	 *
	 * @since 1.1.0
	 */
	public function save_settings() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$fields = sellkit_post( 'fields' );

		if ( ! is_array( $fields ) ) {
			wp_send_json_error( __( 'Somethings went wrong.', 'sellkit' ) );
		}

		foreach ( $fields as $option => $value ) {
			$safe_value = ! is_array( $value ) ? sanitize_text_field( $value ) : $value;
			$this->sellkit_update_option( $option, $safe_value );

			if ( 'delete_data' === $option ) {
				update_site_option( 'delete_data', $safe_value );
			}
		}

		wp_send_json_success( __( 'The update process has been completed.', 'sellkit' ) );
	}

	/**
	 * Gets sellkit Settings.
	 *
	 * @since 1.1.0
	 */
	public function get_settings() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$options = get_option( 'sellkit', [] );

		wp_send_json_success( $options );
	}

	/**
	 * Update Sellkit options.
	 *
	 * @since 1.1.0
	 * @return bool
	 * @param string $option Option.
	 * @param string $value String.
	 */
	public function sellkit_update_option( $option, $value ) {
		$options = get_option( 'sellkit', [] );

		// No need to update the same value.
		if ( isset( $options[ $option ] ) && $value === $options[ $option ] ) {
			return false;
		}

		// Update the option.
		$options[ $option ] = $value;

		update_option( 'sellkit', $options );

		return true;
	}

	/**
	 * Removes content box.
	 *
	 * @since 1.1.0
	 */
	public function remove_content_box() {
		$new_content_box_id           = sellkit_htmlspecialchars( INPUT_POST, 'content_box_id' );
		$removed_content_box_data     = sellkit_get_option( 'removed_content_box_data' );
		$removed_content_box_data     = empty( $removed_content_box_data ) ? [] : $removed_content_box_data;
		$new_removed_content_box_data = array_merge( $removed_content_box_data, [ $new_content_box_id ] );

		sellkit_update_option( 'removed_content_box_data', array_unique( $new_removed_content_box_data ) );
	}

	/**
	 * Gets removed content box.
	 *
	 * @since 1.1.0
	 * @return array|bool|mixed
	 */
	public static function get_removed_content_box() {
		$removed_content_boxes = sellkit_get_option( 'removed_content_box_data' );

		return ! empty( $removed_content_boxes ) ? $removed_content_boxes : [];
	}

	/**
	 * Gets Elementor templates for sellkit settings to set empty cart template.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function get_templates() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$input = filter_input( INPUT_GET, 'input_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( empty( $input ) ) {
			wp_send_json_error();
		}

		$filtered_templates = [];
		$args               = [
			'post_type' => 'elementor_library',
			'post_status' => 'publish',
			's' => $input,
			'posts_per_page' => 20,
		];

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$filtered_templates[] = [
					'label' => html_entity_decode( get_the_title() ),
					'value' => get_the_ID(),
				];
			}
		}

		wp_send_json_success( $filtered_templates );
	}
}

Sellkit_Admin_Settings::get_instance();
