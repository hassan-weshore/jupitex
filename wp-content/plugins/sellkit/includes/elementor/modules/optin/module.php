<?php

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;
use Elementor\Icons_Manager as Icon_Manager;

class Sellkit_Elementor_Optin_Module extends Sellkit_Elementor_Base_Module {

	public static $field_types = [];

	public static $action_types = [];

	public static $messages = [];

	public function __construct() {
		parent::__construct();

		$this->load_controls();
		$this->register_field_types();
		$this->register_action_types();
		$this->set_messages();
		$this->localize_editor_nonce();
		$this->localize_validation_translations();

		if ( is_admin() ) {
			sellkit()->load_files( [
				'elementor/controls/file-uploader',
			] );

			add_action( 'wp_ajax_sellkit_control_file_upload', [ Sellkit_Elementor_Controls_File_Uploader::class, 'handle_file_upload' ] );
		}
	}

	public function get_widgets() {
		return [ 'optin' ];
	}

	/**
	 * Load files in "controls" directory which hold the control adding methods.
	 *
	 * @access private
	 * @since 1.5.0
	 */
	private function load_controls() {
		sellkit()->load_files( [
			'elementor/modules/optin/controls/tab-content',
			'elementor/modules/optin/controls/tab-style',
		] );
	}

	/**
	 * Get types of Optin form fields available.
	 *
	 * @return array
	 *
	 * @access public
	 * @static
	 * @since 1.5.0
	 */
	public static function get_field_types() {
		return [
			'text'       => esc_html__( 'Text', 'sellkit' ),
			'number'     => esc_html__( 'Number', 'sellkit' ),
			'email'      => esc_html__( 'Email', 'sellkit' ),
			'tel'        => esc_html__( 'Tel', 'sellkit' ),
			'textarea'   => esc_html__( 'Textarea', 'sellkit' ),
			'date'       => esc_html__( 'Date', 'sellkit' ),
			'time'       => esc_html__( 'Time', 'sellkit' ),
			'checkbox'   => esc_html__( 'Checkbox', 'sellkit' ),
			'radio'      => esc_html__( 'Radio', 'sellkit' ),
			'select'     => esc_html__( 'Select', 'sellkit' ),
			'address'    => esc_html__( 'Address', 'sellkit' ),
			'acceptance' => esc_html__( 'Acceptance', 'sellkit' ),
			'hidden'     => esc_html__( 'Hidden', 'sellkit' ),
		];
	}

	/**
	 * Registers the field types defined in the get_field_types by creating
	 * instance from their classes.\
	 * Also loads Field_Base class.
	 *
	 * @access private
	 * @since 1.5.0
	 */
	private function register_field_types() {
		// Register base class of fields.
		sellkit()->load_files( [ 'elementor/modules/optin/fields/field-base' ] );

		// Register fields.
		foreach ( self::get_field_types() as $field_key => $field_label ) {
			$class_path = "elementor/modules/optin/fields/{$field_key}";
			sellkit()->load_files( [ $class_path ] );

			$class_name = 'Sellkit_Elementor_Optin_Field_' . ucfirst( $field_key );

			self::$field_types[ $field_key ] = new $class_name();
		}
	}

	/**
	 * Get types of available Optin actions.
	 *
	 * @return array
	 *
	 * @access public
	 * @static
	 * @since 1.5.0
	 */
	public static function get_action_types() {
		return [
			'growmatik'      => esc_html__( 'Growmatik', 'sellkit' ),
			'activecampaign' => esc_html__( 'ActiveCampaign', 'sellkit' ),
			'convertkit'     => esc_html__( 'ConvertKit', 'sellkit' ),
			'drip'           => esc_html__( 'Drip', 'sellkit' ),
			'getresponse'    => esc_html__( 'GetResponse', 'sellkit' ),
			'mailchimp'      => esc_html__( 'MailChimp', 'sellkit' ),
			'mailerlite'     => esc_html__( 'MailerLite', 'sellkit' ),
			'webhook'        => esc_html__( 'WebHook', 'sellkit' ),
		];
	}

	/**
	 * Registers the actions defined in the get_action_types by creating
	 * instance from their classes.\
	 * Also loades of Action_Base class, CRM actions trait and creates instance of Download action.
	 *
	 * @access private
	 * @since 1.5.0
	 */
	private function register_action_types() {
		// Register base class of actions, AJAX handler and CRM integration trait.
		sellkit()->load_files(
			[
				'elementor/modules/optin/actions/utils/action-base',
				'elementor/modules/optin/actions/utils/ajax-handler',
				'elementor/modules/optin/actions/utils/crm-trait',
			]
		);

		// Create AJAX handler instance to register its hooks.
		new Sellkit_Elementor_Optin_Ajaxhandler();

		// Register every single action.
		foreach ( self::get_action_types() as $action_key => $action_label ) {
			$class_path = "elementor/modules/optin/actions/{$action_key}";
			sellkit()->load_files( [ $class_path ] );

			$class_name = 'Sellkit_Elementor_Optin_Action_' . ucfirst( $action_key );

			self::$action_types[ $action_key ] = new $class_name();
		}

		// Add Download and Redirect action separately as it should be always
		// available and not be in the list above.
		sellkit()->load_files( [ 'elementor/modules/optin/actions/download-redirect' ] );

		new Sellkit_Elementor_Optin_Action_Download_Redirect();
	}

	/**
	 * Set $messages array propery.
	 *
	 * @access public
	 * @since 1.5.0
	 */
	public function set_messages() {
		self::$messages = [
			'success'    => esc_html__( 'The form was sent successfully!', 'sellkit' ),
			'error'      => esc_html__( 'Please check the errors.', 'sellkit' ),
			'required'   => esc_html__( 'Required', 'sellkit' ),
		];
	}

	/**
	 * Renders form field.
	 *
	 * Calls the "render_content" method in the corresponding field class.
	 *
	 * @param object $widget Widget instance.
	 * @param array $field   Field settings.
	 *
	 * @access public
	 * @static
	 * @since 1.5.0
	 */
	public static function render_field( $widget, $field ) {
		self::$field_types[ $field['type'] ]->render( $widget, $field );
	}

	/**
	 * Render icon based on settings.
	 *
	 * @param Array $icon_data including "library" and "value".
	 *
	 * @access public
	 * @static
	 * @since 1.5.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public static function render_icon( $icon_data ) {
		// Scenario 1: if font icon experimental feature is activated, it's preferred.
		if ( Elementor::$instance->experiments->is_feature_active( 'e_font_icon_svg' ) && $icon_data['value'] ) {
			if ( 'svg' === $icon_data['library'] ) {
				$font_icon = Icon_Manager::render_uploaded_svg_icon( $icon_data['value'] );
			} else {
				$font_icon = Icon_Manager::render_font_icon( $icon_data );
			}

			if ( ! empty( $font_icon ) ) {
				echo $font_icon;
				return;
			}
		}

		// Scenario 2: Otherwise, when the user has used font awesome option.
		if ( 'svg' !== $icon_data['library'] && ! empty( $icon_data['value'] ) ) {
			echo '<i class="' . esc_attr( $icon_data['value'] ) . '"></i>';
			return;
		}

		// Scenario 3: Otherwise, when the user has used upload svg option.
		if ( 'svg' === $icon_data['library'] && $icon_data['value'] && ! empty( $icon_data['value']['url'] ) ) {
			echo '<object type="image/svg+xml" data="' . esc_attr( $icon_data['value']['url'] ) . '"></object>';
		}
	}

	/**
	 * Get active breakpoint of the Elementor.
	 *
	 * @return array Active Elementor breakpoints and their sizes.
	 * @access public
	 * @static
	 * @since 1.5.0
	 */
	public static function get_active_breakpoints() {
		$breakpoints = Elementor::$instance->breakpoints->get_breakpoints_config();
		$result      = [ 'desktop' => '' ];

		foreach ( $breakpoints as $device => $config ) {
			if ( $config['is_enabled'] ) {
				$result[ $device ] = $config['value'];
			}
		}

		return $result;
	}

	/**
	 * Localize a nonce for AJAX calls inside editor panel.
	 *
	 * @access private
	 * @since 1.5.0
	 */
	private function localize_editor_nonce() {
		add_action( 'elementor/editor/after_enqueue_scripts', function() {
			wp_localize_script(
				'sellkit-editor',
				'sellkitNonceEditorOptin',
				[ wp_create_nonce( 'sellkit_optin_editor' ) ]
			);
		}, 20 );
	}

	/**
	 * Localize the translation strings needed by JS to render validation errors
	 *
	 * @access private
	 * @since 1.5.0
	 */
	private function localize_validation_translations() {
		$translations = [
			'general' => [
				'errorExists'     => self::$messages['error'],
				'required'        => self::$messages['required'],
				'invalidEmail'    => esc_html__( 'Invalid Email address.', 'sellkit' ),
				'invalidPhone'    => esc_html__( 'The value should only consist numbers and phone characters (-, +, (), etc).', 'sellkit' ),
				'invalidNumber'   => esc_html__( 'Invalid number.', 'sellkit' ),
				'invalidMaxValue' => esc_html__( 'Value must be less than or equal to MAX_VALUE.', 'sellkit' ),
				'invalidMinValue' => esc_html__( 'Value must be greater than or equal to MIN_VALUE.', 'sellkit' ),
			],
			// Validation messages specific to Intelligent Tel Input plugin.
			'itiValidation' => [
				'invalidCountryCode' => esc_html__( 'Invalid country code.', 'sellkit' ),
				'tooShort'           => esc_html__( 'Phone number is too short.', 'sellkit' ),
				'tooLong'            => esc_html__( 'Phone number is too long.', 'sellkit' ),
				'areaCodeMissing'    => esc_html__( 'Area code is required..', 'sellkit' ),
				'invalidLength'      => esc_html__( 'Phone number has an invalid length.', 'sellkit' ),
				'invalidGeneral'     => esc_html__( 'Invalid phone number.', 'sellkit' ),
				'typeMismatch'       => [
					'0'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'Fixed Line', 'sellkit' ) . '.',
					'1'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'Mobile', 'sellkit' ) . '.',
					'2'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'Fixed Line or Mobile', 'sellkit' ) . '.',
					'3'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'Toll Free', 'sellkit' ) . '.',
					'4'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'Premium Rate', 'sellkit' ) . '.',
					'5'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'Shared Cost', 'sellkit' ) . '.',
					'6'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'VOIP', 'sellkit' ) . '.',
					'7'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'Personal Number', 'sellkit' ) . '.',
					'8'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'Pager', 'sellkit' ) . '.',
					'9'  => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'UAN', 'sellkit' ) . '.',
					'10' => esc_html__( 'Phone number must be of type: ', 'sellkit' ) . esc_html__( 'Voicemail', 'sellkit' ) . '.',
				],
			],
		];

		add_action( 'elementor/frontend/after_enqueue_scripts', function() use ( $translations ) {
			wp_localize_script(
				'sellkit-initialize-widgets',
				'sellkitOptinValidationsTranslations',
				$translations
			);
		}, 20 );
	}
}
