<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * Initializing the MailChimp action by extending Action base and using CRM trait.
 *
 * @since 1.5.0
 */
class Sellkit_Elementor_Optin_Action_Mailchimp extends Sellkit_Elementor_Optin_Action_Base {
	use Sellkit_Elementor_Optin_CRM;

	private $api_key;

	private $api_server;

	public static $instance;

	public static function get_instance() {
		if ( empty( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function get_name() {
		return 'mailchimp';
	}

	public function get_title() {
		return esc_html__( 'MailChimp', 'sellkit' );
	}

	protected function get_base_url() {
		return 'https://' . $this->api_server . '.api.mailchimp.com/3.0/';
	}

	protected function get_headers() {
		return [
			'Authorization' => 'Basic ' . base64_encode( 'user:' . $this->api_key ),
			'Content-Type'  => 'application/json',
			'User-Agent'    => $this->user_agent,
		];
	}

	protected function get_get_request_args() {
		return [
			'timeout'   => 100,
			'sslverify' => false,
			'headers'   => $this->get_headers(),
		];
	}

	/**
	 * An override of the same named function in CRM trait.\
	 * Because MailChimp's api key is like XXXX-us1 and we must extract its parts.
	 */
	private function get_api_params( $settings ) {
		$name           = $this->get_name();
		$api_key_source = $settings[ "{$name}_api_key_source" ];
		$api_key        = '';

		if ( 'custom' === $api_key_source ) {
			$api_key = $settings[ "{$name}_custom_api_key" ];
		} else {
			$options = get_option( 'sellkit' );

			if ( ! empty( $options[ "{$name}_api_key" ] ) ) {
				$api_key = $options[ "{$name}_api_key" ];
			}
		}

		$result = [
			'token'  => '',
			'server' => '',
		];

		$parts = explode( '-', $api_key );
		if ( 2 === count( $parts ) ) {
			$result['token']  = $parts[0];
			$result['server'] = $parts[1];
		}

		return $result;
	}

	public function add_controls( $widget ) {
		$action = $this->get_name();

		$this->add_api_controls( $widget, esc_html__( 'Audience', 'sellkit' ) );
		$this->add_field_mapping_controls( $widget );

		$widget->add_control( "{$action}_groups",
			[
				'label'       => esc_html__( 'Groups', 'sellkit' ),
				'type'        => 'select2',
				'multiple'    => 'true',
				'label_block' => true,
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

		$this->add_tag_control( $widget );

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
			return $ajax_handler->add_response( 'admin_errors', $this->get_invalid_list_message( 'audience' ) );
		}

		// Retireve and check API credentials.
		$api_data         = $this->get_api_params( $form_settings );
		$this->api_key    = $api_data['token'];
		$this->api_server = $api_data['server'];
		if ( empty( $this->api_key ) || empty( $this->api_server ) ) {
			return $ajax_handler->add_response( 'admin_errors', "{$this->get_title()}: " . esc_html__( 'Missing API credentials.', 'sellkit' ) );
		}

		// Try subscription.
		$this->ajax_handler = $ajax_handler;
		$subscriber         = $this->create_subscriber_object();
		$this->subscribe( $subscriber, $list_id );
	}

	public function get_list( AjaxHandler $ajax_handler, $params ) {
		$api_data         = $this->get_api_params( $params );
		$this->api_key    = $api_data['token'];
		$this->api_server = $api_data['server'];
		$results          = $this->send_get( 'lists?count=999' );
		$lists            = [];

		if ( ! empty( $results['body']['lists'] ) ) {
			foreach ( $results['body']['lists'] as $list ) {
				$lists[ $list['id'] ] = $list['name'];
			}
		}

		$list = [ 'lists' => $lists ];

		return $ajax_handler->add_response( 'success', $list );
	}

	public function get_additional_data( AjaxHandler $ajax_handler, $params ) {
		$api_data         = $this->get_api_params( $params );
		$this->api_key    = $api_data['token'];
		$this->api_server = $api_data['server'];

		$data = [
			'custom_fields' => $this->get_remote_custom_fields( $params['list_id'] ),
			'groups'        => $this->get_remote_groups( $params['list_id'] ),
			'tags'          => $this->get_remote_tags( $params['list_id'] ),
		];

		return $ajax_handler->add_response( 'success', $data );
	}

	private function get_remote_custom_fields( $list_id ) {
		$results = $this->send_get( "lists/{$list_id}/merge-fields?count=999" );

		$default_fields = $this->get_default_remote_fields()['optional'];
		$custom_fields  = [];

		if ( empty( $results['body']['merge_fields'] ) ) {
			return $custom_fields;
		}

		foreach ( $results['body']['merge_fields'] as $field ) {
			if ( ! array_key_exists( $field['tag'], $default_fields ) ) {
				$custom_fields[ $field['tag'] ] = $field['name'];
			}
		}

		return $custom_fields;
	}

	private function get_remote_tags( $list_id ) {
		$results = $this->send_get( "lists/{$list_id}/tag-search" );
		$tags    = [];

		if ( empty( $results['body']['tags'] ) ) {
			return $tags;
		}

		foreach ( $results['body']['tags'] as $tag ) {
			$tags[ $tag['name'] ] = $tag['name'];
		}

		return $tags;
	}

	private function get_remote_groups( $list_id ) {
		$results = $this->send_get( "lists/{$list_id}/interest-categories?count=999" );
		$groups  = [];

		if ( ! empty( $results['body']['categories'] ) ) {
			foreach ( $results['body']['categories'] as $category ) {
				$_results = $this->send_get( "lists/{$list_id}/interest-categories/{$category['id']}/interests?count=999" );

				if ( ! empty( $_results['body']['interests'] ) ) {
					foreach ( $_results['body']['interests'] as $interests ) {
						$groups[ $interests['id'] ] = "{$category['title']}: {$interests['name']}";
					}
				}
			}
		}

		return $groups;
	}

	protected function get_default_remote_fields() {
		return [
			'required' => [
				'email_address' => esc_html__( 'Email', 'sellkit' ),
			],
			'optional' => [
				'full_name' => esc_html__( 'Full Name', 'sellkit' ),
			],
		];
	}

	protected function create_subscriber_object() {
		$mapped_fields  = $this->get_field_mappings();
		$subscriber     = [
			'ip_opt' => static::get_client_ip(),
			'status' => 'subscribed',
			'status_if_new' => 'subscribed',
			'skip_merge_validation' => true,
		];
		$default_fields = $this->get_default_remote_fields();

		foreach ( $mapped_fields as $key => $value ) {
			$is_custom =
				! array_key_exists( $key, $default_fields['required'] ) &&
				! array_key_exists( $key, $default_fields['optional'] );

			if ( $is_custom ) {
				$subscriber['merge_fields'][ $key ] = $value;
				continue;
			}

			$subscriber[ $key ] = $value;
		}

		if ( empty( $subscriber['email_address'] ) ) {
			return [];
		}

		$settings = $this->ajax_handler->form['settings'];

		// Reactivation status.
		$reactivate = $settings['mailchimp_resubscribe'];
		if ( 'yes' !== $reactivate ) {
			$email_hash = md5( strtolower( $subscriber['email_address'] ) );
			$list_id    = $settings['mailchimp_list'];

			$subscriber_info = $this->send_get( "lists/{$list_id}/members/{$email_hash}" );

			if ( ! empty( $subscriber_info['body']['status'] ) && 'subscribed' !== $subscriber_info['body']['status'] ) {
				$subscriber['status'] = 'pending';
			}
		}

		// Add additional data.
		$subscriber['tags'] = $settings['mailchimp_tags'];

		$groups = $settings['mailchimp_groups'];

		if ( ! empty( $groups ) ) {
			$groups                  = is_array( $groups ) ? $groups : [ $groups ];
			$subscriber['interests'] = array_fill_keys( $groups, true );
		}

		return $subscriber;
	}

	protected function subscribe( $subscriber_data, $list_id ) {
		if ( empty( $subscriber_data['email_address'] ) ) {
			return;
		}

		$email_hash = md5( strtolower( $subscriber_data['email_address'] ) );

		$endpoint = "lists/{$list_id}/members/{$email_hash}";

		$args = [
			'method'  => 'PUT',
			'timeout' => 100,
			'headers' => $this->get_headers(),
			'body'    => wp_json_encode( $subscriber_data ),
		];

		return $this->send_post( $endpoint, $args );
	}
}
