<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * Initializing the ActiveCampaign action by extending Action base and using CRM trait.
 *
 * @since 1.5.0
 */
class Sellkit_Elementor_Optin_Action_ActiveCampaign extends Sellkit_Elementor_Optin_Action_Base {
	use Sellkit_Elementor_Optin_CRM;

	private $api_key;

	private $api_url;

	public static $instance;

	public static function get_instance() {
		if ( empty( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function get_name() {
		return 'activecampaign';
	}

	public function get_title() {
		return esc_html__( 'ActiveCampaign', 'sellkit' );
	}

	protected function get_base_url() {
		return trailingslashit( $this->api_url ) . 'api/3/';
	}

	protected function get_headers() {
		return [
			'Api-Token'    => $this->api_key,
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
		];
	}

	protected function get_get_request_args() {
		return [
			'timeout'    => 100,
			'sslverify'  => false,
			'headers'    => $this->get_headers(),
		];
	}

	public function add_controls( $widget ) {
		$action = $this->get_name();

		$this->add_api_controls( $widget, esc_html__( 'List', 'sellkit' ) );
		$this->add_field_mapping_controls( $widget );
		$this->add_tag_control( $widget );

		// We need to inject a new text control for API URL.
		$widget->start_injection( [ 'of' => "{$action}_custom_api_key" ] );
		$widget->add_control( "{$action}_custom_api_url",
			[
				'label'       => esc_html__( 'Custom API URL', 'sellkit' ),
				'type'        => 'text',
				'placeholder' => 'https://ACCOUNT-NAME.api-us1.com',
				'title'       => 'https://ACCOUNT-NAME.api-us1.com',
				/* translators: Action name */
				'description' => sprintf( esc_html__( 'Enter your %s API URL for only this form.', 'sellkit' ), $this->get_title() ),
				'condition'   => [ "{$action}_api_key_source" => 'custom' ],
			]
		);
		$widget->end_injection();
	}

	public function run( AjaxHandler $ajax_handler ) {
		$form_settings = $ajax_handler->form['settings'];

		// Retrieve and check List.
		$list_id = $form_settings[ $this->get_name() . '_list' ];
		if ( empty( $list_id ) || in_array( $list_id, [ 'default', 'fetching', 'noList' ], true ) ) {
			return $ajax_handler->add_response( 'admin_errors', $this->get_invalid_list_message( 'list' ) );
		}

		// Retireve and check API credentials.
		$this->api_key = $this->get_api_param( $form_settings );
		$this->api_url = $this->get_api_param( $form_settings, 'url' );
		if ( empty( $this->api_key ) || empty( $this->api_url ) ) {
			return $ajax_handler->add_response( 'admin_errors', "{$this->get_title()}: " . esc_html__( 'Missing API credentials.', 'sellkit' ) );
		}

		// Try subscription.
		$this->ajax_handler = $ajax_handler;
		$subscriber         = $this->create_subscriber_object();
		$this->subscribe( $subscriber, $list_id, $form_settings['activecampaign_tags'] );
	}

	public function get_list( AjaxHandler $ajax_handler, $params ) {
		$this->api_key = $this->get_api_param( $params );
		$this->api_url = $this->get_api_param( $params, 'url' );

		$results = $this->send_get( 'lists' );
		$lists   = [];

		if ( ! empty( $results['body']['lists'] ) ) {
			foreach ( $results['body']['lists'] as $index => $list ) {
				$lists[ $list['id'] ] = $list['name'];
			}
		}

		$list = [ 'lists' => $lists ];

		return $ajax_handler->add_response( 'success', $list );
	}

	public function get_additional_data( AjaxHandler $ajax_handler, $params ) {
		$this->api_key = $this->get_api_param( $params );
		$this->api_url = $this->get_api_param( $params, 'url' );

		$data = [
			'custom_fields' => $this->get_remote_custom_fields(),
			'tags'          => $this->get_remote_tags(),
		];

		return $ajax_handler->add_response( 'success', $data );
	}

	private function get_remote_custom_fields() {
		$results = $this->send_get( 'fields' );

		$default_fields = $this->get_default_remote_fields()['optional'];
		$custom_fields  = [];

		if ( empty( $results['body']['fields'] ) ) {
			return $custom_fields;
		}

		foreach ( $results['body']['fields'] as $field ) {
			if ( ! array_key_exists( $field['id'], $default_fields ) ) {
				$custom_fields[ $field['id'] ] = $field['title'];
			}
		}

		return $custom_fields;
	}

	private function get_remote_tags() {
		$results = $this->send_get( 'tags' );
		$tags    = [];

		if ( empty( $results['body']['tags'] ) ) {
			return $tags;
		}

		foreach ( $results['body']['tags'] as $tag ) {
			$tags[ $tag['id'] ] = $tag['tag'];
		}

		return $tags;
	}

	protected function get_default_remote_fields() {
		return [
			'required' => [
				'email' => esc_html__( 'Email', 'sellkit' ),
			],
			'optional' => [
				'firstName' => esc_html__( 'First Name', 'sellkit' ),
				'lastName'  => esc_html__( 'Last Name', 'sellkit' ),
				'phone'     => esc_html__( 'Phone', 'sellkit' ),
			],
		];
	}

	protected function create_subscriber_object() {
		$mapped_fields  = $this->get_field_mappings();
		$default_fields = $this->get_default_remote_fields();

		foreach ( $mapped_fields as $key => $value ) {
			$is_custom =
				! array_key_exists( $key, $default_fields['required'] ) &&
				! array_key_exists( $key, $default_fields['optional'] );

			if ( $is_custom ) {
				$subscriber['fieldValues'][] = [
					'field' => $key,
					'value' => $value,
				];
				continue;
			}

			$subscriber[ $key ] = $value;
		}

		return [ 'contact' => $subscriber ];
	}

	protected function subscribe( $subscriber_data, $list_id, $tags ) {
		$args = [
			'method'  => 'POST',
			'timeout' => 100,
			'headers' => $this->get_headers(),
			'body'    => wp_json_encode( $subscriber_data ),
		];

		$result = $this->send_post( 'contacts', $args, 'temp_activecampaign' );

		// If subscriber already exists, sync it with new data.
		if ( 422 === $result['code'] ) {
			unset( $this->ajax_handler->response['admin_errors']['temp_activecampaign'] );

			$result = $this->send_post( 'contact/sync', $args );
		}

		if ( isset( $result['body'] ) && isset( $result['body']['contact'] ) ) {
			$subscriber_id = $result['body']['contact']['id'];

			$this->add_subscriber_to_list( $subscriber_id, $list_id );
			$this->add_tags_to_subscriber( $subscriber_id, $tags );
		}
	}

	private function add_subscriber_to_list( $subscriber_id, $list_id ) {
		$args = [
			'method'  => 'POST',
			'headers' => $this->get_headers(),
			'body'    => wp_json_encode( [
				'contactList' => [
					'list'    => strval( $list_id ),
					'contact' => strval( $subscriber_id ),
					'status'  => '1',
				],
			] ),
		];

		return $this->send_post( 'contactLists', $args );
	}

	private function add_tags_to_subscriber( $subscriber_id, $tags ) {
		if ( empty( $tags ) ) {
			return;
		}

		foreach ( $tags as $tag ) {
			$args = [
				'method'  => 'POST',
				'headers' => $this->get_headers(),
				'body'    => wp_json_encode( [
					'contactTag' => [
						'contact' => $subscriber_id,
						'tag'     => $tag,
					],
				] ),
			];

			return $this->send_post( 'contactTags', $args );
		}
	}
}
