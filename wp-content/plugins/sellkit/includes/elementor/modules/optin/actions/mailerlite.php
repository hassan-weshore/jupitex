<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * Initializing the MailerLite action by extending Action base and using CRM trait.
 *
 * @since 1.5.0
 */
class Sellkit_Elementor_Optin_Action_Mailerlite extends Sellkit_Elementor_Optin_Action_Base {
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
		return 'mailerlite';
	}

	public function get_title() {
		return esc_html__( 'MailerLite', 'sellkit' );
	}

	protected function get_base_url() {
		return 'https://api.mailerlite.com/api/v2/';
	}

	protected function get_headers() {
		return [
			'X-MailerLite-ApiKey' => $this->api_key,
			'Content-Type' => 'application/json',
		];
	}

	protected function get_get_request_args() {
		return [
			'timeout'   => 100,
			'sslverify' => false,
			'headers'   => $this->get_headers(),
		];
	}

	public function add_controls( $widget ) {
		$action = $this->get_name();

		$this->add_api_controls( $widget, esc_html__( 'Group', 'sellkit' ) );
		$this->add_field_mapping_controls( $widget );

		$widget->add_control( "{$action}_resubscribe",
			[
				'label'       => esc_html__( 'Double Opt-In', 'sellkit' ),
				'type'        => 'switcher',
				'description' => esc_html__( 'Activates the existing user, if unsubscribed.', 'sellkit' ),
				'conditions'  => [
					'terms' => [
						[
							'name'     => "{$action}_list",
							'operator' => '!in',
							'value'    => [ 'none', 'fetching', 'noList' ],
						],
					],
				],
			]
		);
	}

	public function run( AjaxHandler $ajax_handler ) {
		$form_settings = $ajax_handler->form['settings'];

		// Retrieve and check List.
		$list_id = $form_settings[ $this->get_name() . '_list' ];
		if ( empty( $list_id ) || in_array( $list_id, [ 'default', 'fetching', 'noList' ], true ) ) {
			return $ajax_handler->add_response( 'admin_errors', $this->get_invalid_list_message( 'group' ) );
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
		$results       = $this->send_get( 'groups' );
		$groups        = [];

		if ( 200 === $results['code'] && ! empty( $results['body'] ) ) {
			foreach ( $results['body'] as $group ) {
				if ( is_array( $group ) ) {
					$groups[ $group['id'] ] = $group['name'];
				}
			}
		}

		$list = [ 'lists' => $groups ];
		return $ajax_handler->add_response( 'success', $list );
	}

	public function get_additional_data( AjaxHandler $ajax_handler, $params ) {
		$this->api_key = $this->get_api_param( $params );

		$data = [
			'custom_fields' => $this->get_remote_custom_fields(),
		];

		return $ajax_handler->add_response( 'success', $data );
	}

	private function get_remote_custom_fields() {
		$results        = $this->send_get( 'fields' );
		$default_fields = $this->get_default_remote_fields();
		$default_fields = array_merge( $default_fields['required'], $default_fields['optional'] );
		$custom_fields  = [];

		if ( empty( $results['body'] ) ) {
			return $custom_fields;
		}

		foreach ( $results['body'] as $field ) {
			if ( is_array( $field ) && ! array_key_exists( $field['key'], $default_fields ) ) {
				$custom_fields[ $field['key'] ] = $field['title'];
			}
		}

		return $custom_fields;
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
		$subscriber     = [ 'resubscribe' => false ];

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

		if ( 'yes' === $this->ajax_handler->form['settings']['mailerlite_resubscribe'] ) {
			$subscriber['resubscribe'] = true;
		}

		return $subscriber;
	}

	protected function subscribe( $subscriber_data, $group ) {
		$endpoint = 'groups/' . $group . '/subscribers';
		$args     = [
			'method'  => 'POST',
			'timeout' => 100,
			'headers' => $this->get_headers(),
			'body'    => wp_json_encode( $subscriber_data ),
		];

		return $this->send_post( $endpoint, $args );
	}
}
