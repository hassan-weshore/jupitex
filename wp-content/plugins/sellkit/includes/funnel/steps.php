<?php

use Sellkit\Funnel\Analytics\Data_Updater;

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit_Steps.
 *
 * @since 1.1.0
 */
class Sellkit_Steps {

	/**
	 * Funnel Instance
	 *
	 * @var Sellkit_Funnel Funnel Object.
	 * @since 1.1.0
	 */
	public $funnel;

	/**
	 * Step Instance array.
	 *
	 * @var array Steps instances.
	 * @since 1.1.0
	 */
	public static $steps;

	/**
	 * Updates analytics.
	 *
	 * @var object Analytics updater.
	 * @since 1.1.0
	 */
	public $analytics_updater;

	/**
	 * Sellkit_Steps constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->funnel = Sellkit_Funnel::get_instance();

		$this->load_steps();
		$this->init();

		add_action( 'template_redirect', [ $this, 'add_start_and_finish_logs' ] );
	}

	/**
	 * Initialize steps.
	 *
	 * @since 1.1.0
	 */
	public function init() {
		if ( empty( $this->funnel->current_step_data ) ) {
			return;
		}

		$current_step_type = $this->funnel->current_step_data['type']['key'];

		add_action( 'wp', function () {
			if ( is_user_logged_in() && current_user_can( 'administrator' ) ) {
				return;
			}

			if ( ! empty( $this->funnel->current_step_data['status'] ) && 'publish' === $this->funnel->current_step_data['status'] ) {
				return;
			}

			if ( ! empty( $this->funnel->next_step_data['page_id'] ) ) {
				wp_safe_redirect( add_query_arg( [ $_GET ], get_permalink( $this->funnel->next_step_data['page_id'] ) ) ); // phpcs:ignore
			}

			if ( empty( $this->funnel->next_step_data['page_id'] ) ) {
				echo esc_html__( 'There is nothing to view here.', 'sellkit' );
				die();
			}
		} );

		if ( method_exists( self::$steps[ $current_step_type ], 'do_actions' ) ) {
			add_action( 'wp', [ self::$steps[ $current_step_type ], 'do_actions' ] );
		}
	}

	/**
	 * Create step instances.
	 *
	 * @since 1.1.0
	 * @param string $path Step path.
	 * @return void
	 */
	public function load_steps( $path = 'includes/funnel/steps' ) {
		$real_path  = trailingslashit( Sellkit::$plugin_dir . $path );
		$file_paths = glob( $real_path . '*.php' );

		if ( ! empty( $this->funnel->funnel_id ) ) {
			$this->analytics_updater = new Data_Updater();

			$this->analytics_updater->set_funnel_id( $this->funnel->funnel_id );
			$this->add_new_visit_log();
			$this->maybe_add_unique_visit_log();
		}

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				continue;
			}

			require_once $file_path;

			$file_name  = str_replace( '.php', '', basename( $file_path ) );
			$step_class = str_replace( '-', ' ', $file_name );
			$step_class = str_replace( ' ', '_', ucwords( $step_class ) );
			$step_class = "Sellkit\Funnel\Steps\\{$step_class}";

			if ( ! class_exists( $step_class ) || 'base-step' === $file_name ) {
				continue;
			}

			$step = new $step_class();

			self::$steps[ $file_name ] = $step;
		}

		// load children steps.
		if ( 'includes/funnel/steps' === $path ) {
			$this->load_steps( 'includes/funnel/steps/children' );
		}
	}

	/**
	 * Adds new visit log.
	 *
	 * @since 1.1.0
	 */
	private function add_new_visit_log() {
		add_action( 'init', function () {
			$this->analytics_updater->add_new_visit();
		} );
	}

	/**
	 * Adds new unique visit log.
	 *
	 * @since 1.1.0
	 */
	private function maybe_add_unique_visit_log() {
		if ( ! session_id() ) {
			session_start();
		}

		if ( array_key_exists( 'sellkit_viewed_funnels', $_SESSION ) && in_array( $this->funnel->funnel_id, $_SESSION['sellkit_viewed_funnels'] ) ) { // phpcs:ignore
			return;
		}

		if ( empty( $_SESSION['sellkit_viewed_funnels'] ) ) {
			$_SESSION['sellkit_viewed_funnels'] = [];
		}

		$_SESSION['sellkit_viewed_funnels'] = array_merge( $_SESSION['sellkit_viewed_funnels'], [ $this->funnel->funnel_id ] );

		add_action( 'init', function () {
			$this->analytics_updater->add_new_visit( true );
		} );
	}


	/**
	 * Adds starting and finishing logs.
	 *
	 * @since 1.1.0
	 */
	public function add_start_and_finish_logs() {
		$funnel            = sellkit_funnel();
		$analytics_updater = new Data_Updater();

		$analytics_updater->set_funnel_id( $funnel->funnel_id );

		if ( isset( $funnel->current_step_data['number'] ) && 0 === $funnel->current_step_data['number'] ) {
			$analytics_updater->add_new_start_log();
		}

		if ( empty( $funnel->next_step_data ) ) {
			$analytics_updater->add_new_finish_log();
		}
	}
}

new Sellkit_Steps();
