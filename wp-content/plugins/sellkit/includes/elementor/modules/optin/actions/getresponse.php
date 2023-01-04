<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * Initializing the GetResponse action by extending Action base and using CRM trait.
 *
 * @since 1.5.0
 */
class Sellkit_Elementor_Optin_Action_Getresponse extends Sellkit_Elementor_Optin_Action_Base {
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
		return 'getresponse';
	}

	public function get_title() {
		return esc_html__( 'GetResponse', 'sellkit' );
	}

	protected function get_base_url() {
		return 'https://api.getresponse.com/v3/';
	}

	protected function get_headers() {
		return [
			'X-Auth-Token' => 'api-key ' . $this->api_key,
			'Content-Type' => 'application/json',
			'User-Agent'   => $this->user_agent,
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

		$this->add_api_controls( $widget, esc_html__( 'Campaign', 'sellkit' ) );
		$this->add_field_mapping_controls( $widget );

		$widget->add_control(
			"{$action}_dayofcycle",
			[
				'label' => esc_html__( 'Day Of Cycle', 'sellkit' ),
				'type' => 'number',
				'min' => 0,
				'conditions'  => [
					'terms' => [
						[
							'name' => "{$action}_list",
							'operator' => '!in',
							'value' => [ 'none', 'fetching', 'noList' ],
						],
					],
				],
			]
		);

		$this->add_tag_control( $widget );
	}

	public function run( AjaxHandler $ajax_handler ) {
		$form_settings = $ajax_handler->form['settings'];

		// Retrieve and check List.
		$list_id = $form_settings[ $this->get_name() . '_list' ];
		if ( empty( $list_id ) || in_array( $list_id, [ 'default', 'fetching', 'noList' ], true ) ) {
			return $ajax_handler->add_response( 'admin_errors', $this->get_invalid_list_message( 'campaign' ) );
		}

		// Retireve and check API credentials.
		$this->api_key = $this->get_api_param( $form_settings );
		if ( empty( $this->api_key ) ) {
			return $ajax_handler->add_response( 'admin_errors', "{$this->get_title()}: " . esc_html__( 'Missing API credentials.', 'sellkit' ) );
		}

		// Try subscription.
		$this->ajax_handler = $ajax_handler;
		$subscriber         = $this->create_subscriber_object();
		$this->subscribe( $subscriber );
	}

	public function get_list( AjaxHandler $ajax_handler, $params ) {
		$this->api_key = $this->get_api_param( $params );
		$results       = $this->send_get( 'campaigns' );
		$campaigns     = [];

		if ( 200 === $results['code'] && ! empty( $results['body'] ) ) {
			foreach ( $results['body'] as $campaign ) {
				if ( is_array( $campaign ) ) {
					$campaigns[ $campaign['campaignId'] ] = $campaign['name'];
				}
			}
		}

		$list = [ 'lists' => $campaigns ];
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
		$results       = $this->send_get( 'custom-fields' );
		$custom_fields = [];

		if ( empty( $results['body'] ) ) {
			return $custom_fields;
		}

		foreach ( $results['body'] as $field ) {
			if ( is_array( $field ) ) {
				$custom_fields[ $field['customFieldId'] ] = $field['name'];
			}
		}

		return $custom_fields;
	}

	private function get_remote_tags() {
		$results = $this->send_get( 'tags' );
		$tags    = [];

		if ( empty( $results['body'] ) ) {
			return $tags;
		}

		foreach ( $results['body'] as $tag ) {
			if ( is_array( $tag ) ) {
				$tags[ $tag['tagId'] ] = $tag['name'];
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
				'name' => esc_html__( 'Name', 'sellkit' ),
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
				$subscriber['customFieldValues'][] = [
					'customFieldId' => $key,
					'value'         => [ $value ],
				];

				continue;
			}

			$subscriber[ $key ] = $value;
		}

		$settings                 = $this->ajax_handler->form['settings'];
		$subscriber['ipAddress']  = $this->get_client_ip();
		$subscriber['campaign']   = [ 'campaignId' => $settings['getresponse_list'] ];
		$subscriber['dayOfCycle'] = null;

		if ( isset( $settings['getresponse_dayofcycle'] ) ) {
			$subscriber['dayOfCycle'] = intval( $settings['getresponse_dayofcycle'] );
		}

		//add tags if present
		if ( ! empty( $settings['getresponse_tags'] ) ) {
			$subscriber['tags'] = $settings['getresponse_tags'];
		}

		return $subscriber;
	}

	protected function subscribe( $subscriber_data ) {
		$endpoint = 'contacts';
		$args     = [
			'method'    => 'POST',
			'timeout'   => 100,
			'sslverify' => false,
			'headers'   => $this->get_headers(),
			'body'      => wp_json_encode( $subscriber_data ),
		];

		$result = $this->send_post( $endpoint, $args, 'temp_getresponse' );

		if ( 409 === $result['code'] ) {
			unset( $this->ajax_handler->response['admin_errors']['temp_getresponse'] );

			$_result = $this->send_get( "contacts?query[email]={$subscriber_data['email']}" );

			if ( $_result['code'] < 200 || $_result['code'] >= 300 ) {
				return $this->ajax_handler->add_response( 'admin_errors', esc_html__( 'GetResponse: Contact already exists, but cannot retrieve its ID.', 'sellkit' ) );
			}

			$contact_id = $_result['body'][0]['contactId'];

			$this->send_post( "contacts/{$contact_id}", $args );
		}
	}
}
