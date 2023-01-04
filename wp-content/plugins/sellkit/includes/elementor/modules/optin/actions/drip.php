<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * Initializing the Drip action by extending Action base and using CRM trait.
 *
 * @since 1.5.0
 */
class Sellkit_Elementor_Optin_Action_Drip extends Sellkit_Elementor_Optin_Action_Base {
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
		return 'drip';
	}

	public function get_title() {
		return esc_html__( 'Drip', 'sellkit' );
	}

	protected function get_base_url() {
		return 'https://api.getdrip.com/v2/';
	}

	protected function get_headers() {
		return [
			'Authorization' => 'Basic ' . base64_encode( $this->api_key ),
			'Content-Type'  => 'application/vnd.api+json',
			'User-Agent'    => $this->user_agent,
		];
	}

	protected function get_get_request_args() {
		return [
			'api_key'    => $this->api_key,
			'api_output' => 'json',
			'timeout'    => 100,
			'sslverify'  => false,
			'headers'    => $this->get_headers(),
		];
	}

	public function add_controls( $widget ) {
		$this->add_api_controls( $widget, esc_html__( 'Account', 'sellkit' ) );
		$this->add_field_mapping_controls( $widget );
		$this->add_tag_control( $widget );
	}

	public function run( AjaxHandler $ajax_handler ) {
		$form_settings = $ajax_handler->form['settings'];

		// Retrieve and check List.
		$list_id = $form_settings[ $this->get_name() . '_list' ];
		if ( empty( $list_id ) || in_array( $list_id, [ 'default', 'fetching', 'noList' ], true ) ) {
			return $ajax_handler->add_response( 'admin_errors', $this->get_invalid_list_message( 'account' ) );
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
		$results       = $this->send_get( 'accounts' );
		$accounts      = [];

		if ( ! empty( $results['body']['accounts'] ) ) {
			foreach ( $results['body']['accounts'] as $account ) {
				$accounts[ $account['id'] ] = $account['name'];
			}
		}

		$list = [ 'lists' => $accounts ];

		return $ajax_handler->add_response( 'success', $list );
	}

	public function get_additional_data( AjaxHandler $ajax_handler, $params ) {
		$this->api_key = $this->get_api_param( $params );

		$data = [
			'custom_fields' => $this->get_remote_custom_fields( $params['list_id'] ),
			'tags'          => $this->get_remote_tags( $params['list_id'] ),
		];

		return $ajax_handler->add_response( 'success', $data );
	}

	private function get_remote_custom_fields( $account_id ) {
		$results = $this->send_get( "{$account_id}/custom_field_identifiers" );

		$default_fields = $this->get_default_remote_fields()['optional'];
		$custom_fields  = [];

		if ( empty( $results['body']['custom_field_identifiers'] ) ) {
			return $custom_fields;
		}

		foreach ( $results['body']['custom_field_identifiers'] as $field ) {
			if ( ! array_key_exists( $field, $default_fields ) ) {
				$custom_fields[ $field ] = $field;
			}
		}

		return $custom_fields;
	}

	private function get_remote_tags( $account_id ) {
		$results = $this->send_get( "{$account_id}/tags" );
		$tags    = [];

		if ( empty( $results['body']['tags'] ) ) {
			return $tags;
		}

		foreach ( $results['body']['tags'] as $tag ) {
			$tags[ $tag ] = $tag;
		}

		return $tags;
	}

	protected function get_default_remote_fields() {
		return [
			'required' => [
				'email' => esc_html__( 'Email', 'sellkit' ),
			],
			'optional' => [
				'first_name'  => esc_html__( 'First Name', 'sellkit' ),
				'last_name'   => esc_html__( 'Last Name', 'sellkit' ),
				'address1'    => esc_html__( 'Address 1', 'sellkit' ),
				'address2'    => esc_html__( 'Address 2', 'sellkit' ),
				'city'        => esc_html__( 'City', 'sellkit' ),
				'state'       => esc_html__( 'State', 'sellkit' ),
				'country'     => esc_html__( 'Country', 'sellkit' ),
				'zip'         => esc_html__( 'Zip', 'sellkit' ),
				'phone'       => esc_html__( 'Phone', 'sellkit' ),
				'sms_number'  => esc_html__( 'SMS Number', 'sellkit' ),
				'sms_consent' => esc_html__( 'SMS Consent', 'sellkit' ),
				'time_zone'   => esc_html__( 'Timezone', 'sellkit' ),
			],
		];
	}

	protected function create_subscriber_object() {
		$mapped_fields  = $this->get_field_mappings();
		$subscriber     = [ 'ip_address' => static::get_client_ip() ];
		$default_fields = $this->get_default_remote_fields();

		foreach ( $mapped_fields as $key => $value ) {
			$is_custom =
				! array_key_exists( $key, $default_fields['required'] ) &&
				! array_key_exists( $key, $default_fields['optional'] );

			if ( $is_custom ) {
				$subscriber['custom_fields'][ $key ] = $value;
				continue;
			}

			$subscriber[ $key ] = $value;
		}

		//add tags if present
		$form_settings = $this->ajax_handler->form['settings'];

		if ( ! empty( $form_settings['drip_tags'] ) ) {
			$subscriber['tags'] = $form_settings['drip_tags'];
		}

		return $subscriber;
	}

	protected function subscribe( $subscriber_data, $account_id ) {
		$endpoint = $account_id . '/subscribers/';
		$args     = [
			'method'  => 'POST',
			'timeout' => 100,
			'headers' => $this->get_headers(),
			'body'    => wp_json_encode( [
				'subscribers' => [ $subscriber_data ],
			] ),
		];

		return $this->send_post( $endpoint, $args );
	}
}
