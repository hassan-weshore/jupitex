<?php

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;
use Sellkit\Funnel\Contacts\Base_Contacts;
use Sellkit_Elementor_Optin_Module as Module;

/**
 * Initializing the ajax handler class for handling form ajax requests.
 *
 * @since 1.5.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Sellkit_Elementor_Optin_Ajaxhandler {

	/**
	 * Holds all the responses.
	 *
	 * @access public
	 * @var array
	 */
	public $response = [
		'message' => [],
		'errors' => [],
		'admin_errors' => [],
	];

	/**
	 * Holds the form settings.
	 *
	 * @access public
	 * @var array
	 */
	public $form;

	/**
	 * Holds a record of the user-filled form.
	 *
	 * @access public
	 * @var array
	 */
	public $form_data;

	/**
	 * Holds the reponse state.
	 *
	 * @access public
	 * @var bool
	 */
	public $is_success = true;

	/**
	 * Initializes the AJAX handler class with registering AJAX hooks.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_sellkit_optin_frontend', [ $this, 'handle_frontend' ] );
		add_action( 'wp_ajax_nopriv_sellkit_optin_frontend', [ $this, 'handle_frontend' ] );
		add_action( 'wp_ajax_sellkit_optin_editor', [ $this, 'handle_editor' ] );
	}

	/**
	 * Handle the form submit in frontend.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function handle_frontend() {
		if ( false === check_ajax_referer( 'sellkit_elementor', 'nonce', false ) ) {
			$this
				->add_response( 'admin_errors', esc_html__( 'Error: Nonce mismatch or expired. Please reload the page and retry.', 'sellkit' ) )
				->set_success( false )
				->send_response();

			return;
		}

		$post_id = filter_input( INPUT_POST, 'post_id' );
		$form_id = filter_input( INPUT_POST, 'form_id' );

		$this->form_data = $_POST; // @codingStandardsIgnoreLine

		$post_elements = Elementor::$instance->documents->get( $post_id )->get_elements_data();
		$this->form    = self::find_form_recursive( $post_elements, $form_id );

		$this
			->validate_form()
			->fill_form_settings()
			->set_custom_messages()
			->run_actions()
			->send_response();
	}

	/**
	 * Handle the form requests in editor.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function handle_editor() {
		$nonce_valid = check_ajax_referer( 'sellkit_optin_editor', 'nonce', false );
		$action      = filter_input( INPUT_POST, 'service' );
		$request     = filter_input( INPUT_POST, 'request' );
		$params      = filter_input( INPUT_POST, 'params', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( false === $nonce_valid ) {
			$this->set_success( false )->send_response();
			return;
		}

		$class_name = 'Sellkit_Elementor_Optin_Action_' . ucfirst( $action );
		$class_name::get_instance()->$request( $this, empty( $params ) ? [] : $params );

		$this->send_response();
	}

	/**
	 * Loop recursively through post meta and find the form element.
	 *
	 * @param array  $post_elements elements data of the post.
	 * @param string $form_id id of the form.
	 * @return array|false data of the form in question or false on failure.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 */
	public static function find_form_recursive( $post_elements, $form_id ) {
		foreach ( $post_elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = self::find_form_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	/**
	 * Set form state to success/error.
	 *
	 * @param boolean $bool True or false.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function set_success( $bool ) {
		$this->is_success = $bool;
		return $this;
	}

	/**
	 * Fills "settings" key of the form by creating an instance of it.
	 *
	 * @since 1.5.0
	 * @access private
	 */
	private function fill_form_settings() {
		$this->form['settings'] =
			Elementor::$instance
				->elements_manager
				->create_element_instance( $this->form )
				->get_settings_for_display();

		return $this;
	}

	/**
	 * Validate the form based on form ID.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function set_custom_messages() {
		$form = $this->form;

		if ( ! $form ) {
			return $this;
		}

		if ( empty( $form['settings']['messages_custom'] ) ) {
			return $this;
		}

		Module::$messages = [
			'success'    => $form['settings']['messages_success'],
			'error'      => $form['settings']['messages_error'],
			'required'   => $form['settings']['messages_required'],
		];

		return $this;
	}

	/**
	 * Validate the form based on form ID.
	 *
	 * @since 1.5.0
	 * @access private
	 */
	private function validate_form() {
		if ( $this->form ) {
			return $this;
		}

		$this
			->add_response( 'message', esc_html__( 'There\'s something wrong. The form is not valid.', 'sellkit' ) )
			->set_success( false )
			->send_response();

		return $this;
	}

	/**
	 * Run all the specified actions.
	 *
	 * @since 1.5.0
	 * @access private
	 */
	private function run_actions() {
		$actions = $this->form['settings']['crm_actions'];

		if ( ! empty( $actions ) ) {
			$actions = is_array( $actions ) ? $actions : [ $actions ];

			foreach ( $actions as $action ) {
				$class_name = Module::$action_types[ $action ];
				$class_name::get_instance()->run( $this );
			}
		}

		// Always run "After Submit Actions".
		Sellkit_Elementor_Optin_Action_Download_Redirect::get_instance()->run( $this );

		return $this;
	}

	/**
	 * Add response to ajax response.
	 *
	 * @param string $type Response type.
	 * @param string $text Response text.
	 * @param string $text_key Response text key.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function add_response( $type, $text = '', $text_key = '' ) {
		if ( ! empty( $text_key ) ) {
			$this->response[ $type ][ $text_key ] = $text;
			return $this;
		}

		$this->response[ $type ][] = $text;
		return $this;
	}

	/**
	 * Send success/fail response.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function send_response() {
		if ( ! current_user_can( 'administrator' ) ) {
			unset( $this->response['admin_errors'] );
		} else {
			// Flatten admin_errors.
			$this->response['admin_errors'] = array_values( $this->response['admin_errors'] );
		}

		if ( $this->is_success ) {
			if ( empty( $this->response['message'] ) ) {
				$this->add_response( 'message', Module::$messages['success'] );
			}

			Base_Contacts::step_is_passed();

			wp_send_json_success( $this->response );
		}

		if ( ! empty( $this->response['errors'] ) ) {
			$this->add_response( 'message', Module::$messages['error'] );
		}

		wp_send_json_error( $this->response );
	}
}
