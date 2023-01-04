<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * Initializing the WebHook action by extending Action_Base.
 *
 * @since 1.5.0
 */
class Sellkit_Elementor_Optin_Action_Webhook extends Sellkit_Elementor_Optin_Action_Base {

	private $exclude_fields = [];

	public static $instance;

	public static function get_instance() {
		if ( empty( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function get_name() {
		return 'webhook';
	}

	public function get_title() {
		return esc_html__( 'WebHook', 'sellkit' );
	}

	public function add_controls( $widget ) {

		$description = sprintf( '%1$s<a href="https://zapier.com/apps/webhook/integrations" target="_blank">%2$s</a>.',
			esc_html__( 'Enter the webhook URL where you want to send your Form data after submit e.g. ', 'sellkit' ),
			esc_html__( 'Integrate with Zapier Webhook', 'sellkit' )
		);

		$widget->add_control( 'webhook_url',
			[
				'label'       => esc_html__( 'Webhook URL', 'sellkit' ),
				'type'        => 'text',
				'label_block' => true,
				'placeholder' => 'http://webhook-endpoint.com',
				'description' => $description,
			]
		);
	}

	public function run( AjaxHandler $ajax_handler ) {
		$settings = $ajax_handler->form['settings'];

		if ( empty( $settings['webhook_url'] ) ) {
			return;
		}

		$body = $this->get_form_data( $ajax_handler, $settings );
		$args = [ 'body' => wp_json_encode( $body ) ];

		$response = wp_remote_post( $settings['webhook_url'], $args );

		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( $response_code < 200 || $response_code >= 300 ) {
			$ajax_handler->add_response( 'admin_errors', esc_html__( 'Webhook Action: Webhook Error.', 'sellkit' ) );
		}
	}

	private function get_form_data( AjaxHandler $ajax_handler, $settings ) {
		$fields = [];

		foreach ( $settings['fields'] as $field ) {
			if ( in_array( $field['type'], $this->exclude_fields, true ) ) {
				continue;
			}

			if ( isset( $ajax_handler->form_data['fields'][ $field['_id'] ] ) ) {
				$field_value = $ajax_handler->form_data['fields'][ $field['_id'] ];
			}

			if ( 'acceptance' === $field['type'] ) {
				$field_value = empty( $field_value ) ? esc_html__( 'No', 'sellkit' ) : esc_html__( 'Yes', 'sellkit' );
			}

			if ( empty( $field['label'] ) ) {
				$fields[ esc_html__( 'No Label', 'sellkit' ) . ' ' . $field['_id'] ] = $field_value;
			} else {
				$fields[ $field['label'] ] = $field_value;
			}
		}

		return $fields;
	}
}
