<?php

defined( 'ABSPATH' ) || die();

use Sellkit_Elementor_Optin_Ajaxhandler as AjaxHandler;

/**
 * Initializing the Drip action by extending Action base and using CRM trait.
 *
 * @since 1.5.0
 */
class Sellkit_Elementor_Optin_Action_Growmatik extends Sellkit_Elementor_Optin_Action_Base {
	use Sellkit_Elementor_Optin_CRM;

	private $api_key;

	private $api_secret;

	public static $instance;

	public static function get_instance() {
		if ( empty( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function get_name() {
		return 'growmatik';
	}

	public function get_title() {
		return esc_html__( 'Growmatik', 'sellkit' );
	}

	protected function get_base_url() {
		return 'https://api.growmatik.ai/public/v1/';
	}

	protected function get_headers() {
		return [
			'apiKey'        => $this->api_key,
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

	public function add_controls( $widget ) {
		$action = $this->get_name();

		$this->add_api_controls( $widget, esc_html__( 'Site', 'sellkit' ) );
		$this->add_field_mapping_controls( $widget );
		$this->add_tag_control( $widget );

		// We need to inject a new text control for API Secret.
		$widget->start_injection( [ 'of' => "{$action}_custom_api_key" ] );
		$widget->add_control( "{$action}_custom_api_secret",
			[
				'label'       => esc_html__( 'Custom API Secret', 'sellkit' ),
				'type'        => 'text',
				/* translators: Action name */
				'description' => sprintf( esc_html__( 'Enter your %s API Secret for only this form.', 'sellkit' ), $this->get_title() ),
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
			return $ajax_handler->add_response( 'admin_errors', $this->get_invalid_list_message( 'site' ) );
		}

		// Retireve and check API credentials.
		$this->api_key    = $this->get_api_param( $form_settings );
		$this->api_secret = $this->get_api_param( $form_settings, 'secret' );
		if ( empty( $this->api_key ) || empty( $this->api_secret ) ) {
			return $ajax_handler->add_response( 'admin_errors', "{$this->get_title()}: " . esc_html__( 'Missing API credentials.', 'sellkit' ) );
		}

		// Try subscription.
		$this->ajax_handler = $ajax_handler;
		$subscriber         = $this->create_subscriber_object();
		$this->subscribe( $subscriber );
	}

	public function get_list( AjaxHandler $ajax_handler, $params ) {
		$this->api_key = $this->get_api_param( $params );
		$results       = $this->send_get( 'site' );
		$site          = [];

		// Growmatik has only one site per api key, so we don't need to loop over the response.
		if ( ! empty( $results['body']['data'] ) ) {
			$site_info = $results['body']['data'];

			if ( ! $site_info['enabled'] ) {
				return [ 'lists' => $site ];
			}

			$site[ $site_info['siteId'] ] = $site_info['siteName'];
		}

		$list = [ 'lists' => $site ];

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

	private function get_remote_custom_fields() {
		$results      = $this->send_get( 'site/attributes' );
		$custom_attrs = [];

		if ( empty( $results['body']['data'] ) ) {
			return $custom_attrs;
		}

		foreach ( $results['body']['data'] as $attr ) {
			if ( 'custom' !== $attr['type'] ) {
				continue;
			}

			$custom_attrs[ $attr['name'] ] = $attr['name'];
		}

		return $custom_attrs;
	}

	private function get_remote_tags() {
		$results = $this->send_get( 'site/tags' );
		$tags    = [];

		if ( empty( $results['body']['data'] ) ) {
			return $tags;
		}

		foreach ( $results['body']['data'] as $tag ) {
			$tags[ $tag['id'] ] = $tag['name'];
		}

		return $tags;
	}

	protected function get_default_remote_fields() {
		return [
			'required' => [
				'email' => esc_html__( 'Email', 'sellkit' ),
			],
			'optional' => [
				'firstName'             => esc_html__( 'First Name', 'sellkit' ),
				'lastName'              => esc_html__( 'Last Name', 'sellkit' ),
				'userName'              => esc_html__( 'User Name', 'sellkit' ),
				'gender'                => esc_html__( 'Gender', 'sellkit' ),
				'address'              => esc_html__( 'Address 1', 'sellkit' ),
				'phoneNumber'           => esc_html__( 'Phone', 'sellkit' ),
				'country'               => esc_html__( 'Country', 'sellkit' ),
				'region'                => esc_html__( 'Region', 'sellkit' ),
				'city'                  => esc_html__( 'City', 'sellkit' ),
				'euConsent'             => esc_html__( 'EU Consent', 'sellkit' ),
				'marketingEmailConsent' => esc_html__( 'Marketing Email Consent', 'sellkit' ),
			],
		];
	}

	protected function create_subscriber_object() {
		$mapped_fields  = $this->get_field_mappings();
		$default_fields = $this->get_default_remote_fields();
		$subscriber     = [ 'data' => [] ];
		$custom_attrs   = [ 'data' => [] ];
		$tags           = [ 'tags' => [] ];
		$email          = null;

		foreach ( $mapped_fields as $key => $value ) {
			$is_custom =
				! array_key_exists( $key, $default_fields['required'] ) &&
				! array_key_exists( $key, $default_fields['optional'] );

			if ( $is_custom ) {
				$custom_attrs['data'][] = [
					'name'  => $key,
					'value' => $value,
				];
				continue;
			}

			$subscriber['user'][ $key ] = $value;

			if ( 'email' === $key ) {
				$email = $value;
			}
		}

		if ( ! $email ) {
			return [];
		}

		// Add tags if present.
		$tag_settings = $this->ajax_handler->form['settings']['growmatik_tags'];
		$tags['tags'] = is_array( $tag_settings ) ? $tag_settings : [ $tag_settings ];

		// Further data.
		$subscriber['apiSecret']   = $this->api_secret;
		$tags['apiSecret']         = $this->api_secret;
		$tags['email']             = $email;
		$custom_attrs['apiSecret'] = $this->api_secret;
		$custom_attrs['email']     = $email;

		return [
			'subscriber'   => $subscriber,
			'custom_attrs' => $custom_attrs,
			'tags'         => $tags,
		];
	}

	protected function subscribe( $subscriber_data ) {
		if ( empty( $subscriber_data ) || empty( $subscriber_data['subscriber'] ) ) {
			return;
		}

		$args = [
			'method'  => 'POST',
			'timeout' => 100,
			'headers' => $this->get_headers(),
			'body'    => wp_json_encode( $subscriber_data['subscriber'] ),
		];

		$result = $this->send_post( 'contact', $args );

		if ( $result['code'] < 200 || $result['code'] >= 300 ) {
			return;
		}

		if ( ! empty( $subscriber_data['custom_attrs'] ) ) {
			$args['body'] = wp_json_encode( $subscriber_data['custom_attrs'] );

			$this->send_post( 'contact/attribute/email', $args );
		}

		if ( ! empty( $subscriber_data['tags'] ) ) {
			$args['body'] = wp_json_encode( $subscriber_data['tags'] );

			$this->send_post( 'contact/tags/email', $args );
		}
	}
}
