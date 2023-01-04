<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * Initializing the ConvertKit action by extending Action base and using CRM trait.
 *
 * @since 1.5.0
 */
class Sellkit_Elementor_Optin_Action_Convertkit extends Sellkit_Elementor_Optin_Action_Base {
	use Sellkit_Elementor_Optin_CRM;

	private $api_key;

	public static $instance;

	public static function get_instance() {
		if ( empty( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function get_name() {
		return 'convertkit';
	}

	public function get_title() {
		return esc_html__( 'ConvertKit', 'sellkit' );
	}

	protected function get_base_url() {
		return 'https://api.convertkit.com/v3/';
	}

	protected function get_headers() {
		return [
			'Content-Type' => 'application/json',
		];
	}

	protected function get_get_request_args() {
		return [
			'timeout'   => 100,
			'sslverify' => false,
		];
	}

	public function add_controls( $widget ) {
		$this->add_api_controls( $widget, esc_html__( 'Form', 'sellkit' ) );
		$this->add_field_mapping_controls( $widget );
		$this->add_tag_control( $widget );
	}

	public function run( AjaxHandler $ajax_handler ) {
		$form_settings = $ajax_handler->form['settings'];

		// Retrieve and check List.
		$list_id = $form_settings[ $this->get_name() . '_list' ];
		if ( empty( $list_id ) || in_array( $list_id, [ 'default', 'fetching', 'noList' ], true ) ) {
			return $ajax_handler->add_response( 'admin_errors', $this->get_invalid_list_message( 'form' ) );
		}

		// Retireve and check API credentials.
		$this->api_key = $this->get_api_param( $form_settings );
		if ( empty( $this->api_key ) ) {
			return $ajax_handler->add_response( 'admin_errors', "{$this->get_title()}: " . esc_html__( 'Missing API credentials.', 'sellkit' ) );
		}

		// Try subscription.
		$this->ajax_handler = $ajax_handler;
		$subscriber         = $this->create_subscriber_object();
		$this->subscribe( $subscriber, $list_id );
	}

	public function get_list( AjaxHandler $ajax_handler, $params ) {
		$this->api_key = $this->get_api_param( $params );
		$results       = $this->send_get( 'forms/?api_key=' . $this->api_key );
		$forms         = [];

		if ( ! empty( $results['body']['forms'] ) ) {
			foreach ( $results['body']['forms'] as $index => $form ) {
				if ( is_array( $form ) ) {
					$forms[ $form['id'] ] = $form['name'];
				}
			}
		}

		$list = [ 'lists' => $forms ];

		return $ajax_handler->add_response( 'success', $list );
	}

	public function get_additional_data( AjaxHandler $ajax_handler, $params ) {
		$this->api_key = $this->get_api_param( $params );

		$data = [
			'custom_fields' => $this->get_remote_custom_fields(),
			'tags'          => $this->get_remote_tags(),
		];

		return $ajax_handler->add_response( 'success', $data );
	}

	private function get_remote_custom_fields() {
		$results       = $this->send_get( 'custom_fields?api_key=' . $this->api_key );
		$custom_fields = [];

		if ( empty( $results['body']['custom_fields'] ) ) {
			return $custom_fields;
		}

		foreach ( $results['body']['custom_fields'] as $field ) {
			if ( is_array( $field ) ) {
				$custom_fields[ $field['key'] ] = $field['label'];
			}
		}

		return $custom_fields;
	}

	private function get_remote_tags() {
		$results = $this->send_get( 'tags/?api_key=' . $this->api_key );
		$tags    = [];

		if ( empty( $results['body']['tags'] ) ) {
			return $tags;
		}

		foreach ( $results['body']['tags'] as $index => $tag ) {
			if ( is_array( $tag ) ) {
				$tags[ $tag['id'] ] = $tag['name'];
			}
		}

		return $tags;
	}

	protected function get_default_remote_fields() {
		return [
			'required' => [
				'email' => esc_html__( 'Email', 'sellkit' ),
			],
			'optional' => [
				'first_name' => esc_html__( 'First Name', 'sellkit' ),
			],
		];
	}

	protected function create_subscriber_object() {
		$mapped_fields  = $this->get_field_mappings();
		$subscriber     = [ 'api_key' => $this->api_key ];
		$default_fields = $this->get_default_remote_fields();

		foreach ( $mapped_fields as $key => $value ) {
			$is_custom =
				! array_key_exists( $key, $default_fields['required'] ) &&
				! array_key_exists( $key, $default_fields['optional'] );

			if ( $is_custom ) {
				$subscriber['fields'][ $key ] = $value;
				continue;
			}

			$subscriber[ $key ] = $value;
		}

		//add tags if present
		$form_settings = $this->ajax_handler->form['settings'];

		if ( ! empty( $form_settings['convertkit_tags'] ) ) {
			$subscriber['tags'] = $form_settings['convertkit_tags'];
		}

		return $subscriber;
	}

	protected function subscribe( $subscriber_data, $form_id ) {
		$endpoint = 'forms/' . $form_id . '/subscribe?api_key=' . $this->api_key;
		$args     = [
			'method'  => 'POST',
			'timeout' => 100,
			'headers' => $this->get_headers(),
			'body'    => wp_json_encode( $subscriber_data ),
		];

		return $this->send_post( $endpoint, $args );
	}
}
