<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Popup_Assets' ) ) {

	/**
	 * Define Jet_Popup_Assets class
	 */
	class Jet_Popup_Assets {

		/**
		 * [$localize_data description]
		 * @var [type]
		 */
		public $localize_data = [
			'elements_data' => [
				'sections' => [],
				'columns'  => [],
				'widgets'  => [],
			]
		];

		/**
		 * [$editor_localize_data description]
		 * @var array
		 */
		public $editor_localize_data = [];

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 10 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_popup_edit_assets' ), 11 );
			add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Enqueue public-facing stylesheets.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue_styles() {
			$deps = apply_filters( 'jet-popup/assets/frontend-css-deps', [] );

			wp_enqueue_style(
				'jet-popup-frontend',
				jet_popup()->plugin_url( 'assets/css/jet-popup-frontend.css' ),
				$deps,
				jet_popup()->get_version()
			);
		}

		/**
		 * Enqueue plugin scripts only with elementor scripts
		 *
		 * @return void
		 */
		public function enqueue_scripts() {

			wp_enqueue_script(
				'jet-anime-js',
				jet_popup()->plugin_url( 'assets/js/lib/anime-js/anime.min.js' ),
				array( 'jquery' ),
				'2.0.2',
				true
			);

			wp_enqueue_script(
				'jet-popup-frontend',
				jet_popup()->plugin_url( 'assets/js/jet-popup-frontend.js' ),
				array( 'jquery', 'elementor-frontend' ),
				jet_popup()->get_version(),
				true
			);

			$this->localize_data['version'] = jet_popup()->get_version();
			$this->localize_data['ajax_url'] = esc_url( admin_url( 'admin-ajax.php' ) );

			wp_localize_script(
				'jet-popup-frontend',
				'jetPopupData',
				$this->localize_data
			);

		}

		/**
		 * Enqueue admin styles
		 *
		 * @return void
		 */
		public function enqueue_admin_assets() {

			wp_register_script(
				'jet-axios',
				jet_popup()->plugin_url( 'assets/js/lib/axios/axios.min.js' ),
				[],
				'0.19.0-beta',
				true
			);

			wp_register_script(
				'jet-popup-tippy',
				jet_popup()->plugin_url( 'assets/js/lib/tippy/tippy.all.min.js' ),
				array(),
				'2.5.4',
				true
			);
		}

		/**
		 * [enqueue_admin_popup_edit_assets description]
		 * @return [type] [description]
		 */
		public function enqueue_admin_popup_edit_assets() {
			$screen = get_current_screen();

			if ( $screen->id === 'edit-' . jet_popup()->post_type->slug() ) {

				$module_data = jet_popup()->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );
				$ui          = new \CX_Vue_UI( $module_data );
				$ui->enqueue_assets();

				wp_enqueue_style(
					'jet-popup-admin',
					jet_popup()->plugin_url( 'assets/css/jet-popup-admin.css' ),
					[],
					jet_popup()->get_version()
				);

				wp_enqueue_script(
					'jet-popup-admin',
					jet_popup()->plugin_url( 'assets/js/jet-popup-admin.js' ),
					[
						'jquery',
						'cx-vue-ui',
						'wp-api-fetch',
					],
					jet_popup()->get_version(),
					true
				);

				wp_localize_script( 'jet-popup-admin', 'JetPopupLibraryConfig', array (
					'getPopupConditionsPath'    => 'jet-popup/v2/get-popup-conditions',
					'updatePopupConditionsPath' => 'jet-popup/v2/update-popup-conditions',
					'getPopupSettingsPath'      => 'jet-popup/v2/get-popup-settings',
					'updatePopupSettingsPath'   => 'jet-popup/v2/update-popup-settings',
					'createPopupPath'           => 'jet-popup/v2/create-popup',
					'presetsData'               => array_values( jet_popup()->post_type->get_predesigned_popups() ),
					'rawConditionsData'         => jet_popup()->conditions_manager->get_conditions_raw_data(),
					'popupImportAction'         => add_query_arg( [ 'action' => 'jet_popup_import_preset', ], esc_url( admin_url( 'admin.php' ) ) ),
					'popupAnimationTypeOptions' => \Jet_Popup_Utils::get_popup_animation_list( true ),
					'popupOpenTriggerOptions'   => \Jet_Popup_Utils::get_popup_open_trigger_list( true ),
					'popupTimeDelayOptions'     => \Jet_Popup_Utils::get_popup_time_delay_list( true ),
					'labels'                    => [
						'importButtonLabel' => __( 'Import Popup', 'jet-popup' ),
					],
				) );
			}
		}

		/**
		 * [suffix description]
		 * @return [type] [description]
		 */
		public function suffix() {
			return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}
