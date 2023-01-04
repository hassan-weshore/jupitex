<?php
/**
 * The file class that handles performance tips.
 *
 * @package JupiterX_Core\Control_Panel_2\Performance_Tips
 *
 * @since 1.18.0
 */

/**
 * Performance tips class.
 *
 * @since 1.18.0
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class JupiterX_Core_Control_Panel_Performance_Tips {

	/**
	 * Class instance.
	 *
	 * @since 1.18.0
	 *
	 * @var JupiterX_Core_Control_Panel_Performance_Tips Class instance.
	 */
	private static $instance = null;

	/**
	 * Tips response.
	 *
	 * @since 1.18.0
	 */
	private $tips_response = [];

	/**
	 * Get a class instance.
	 *
	 * @since 1.18.0
	 *
	 * @return JupiterX_Core_Control_Panel_Performance_Tips Class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.18.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_jupiterx_cp_get_performance_tips', [ $this, 'get_performance_tips' ] );
	}

	/**
	 * Get performance tips.
	 *
	 * @since 1.18.0
	 */
	public function get_performance_tips() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		foreach ( $this->get_tips() as $tip ) {
			call_user_func( [ $this, "check_$tip" ] );
		}

		wp_send_json_success( $this->tips_response );
	}

	private function get_tips( $tip = false ) {
		$tips = [
			'updates' => [
				'message' => esc_html__( 'Make sure to install all the updates.' ),
				'action' => [
					'url' => 'https://themes.artbees.net/docs/jupiter-x-performance-tips#updates',
					'label' => esc_html__( 'Learn more' ),
				],
			],
			'active_plugins' => [
				'message' => esc_html__( 'Reduce the number of active plugins.' ),
				'action' => [
					'url' => 'https://themes.artbees.net/docs/jupiter-x-performance-tips#active_plugins',
					'label' => esc_html__( 'Learn more' ),
				],
			],
			'development_mode' => [
				'message' => esc_html__( 'Turn off Development Mode on a production site.' ),
				'action' => [
					'url' => 'https://themes.artbees.net/docs/jupiter-x-performance-tips#development_mode',
					'label' => esc_html__( 'Learn more' ),
				],
			],
			'revisions' => [
				'message' => esc_html__( 'Limit the number of post revisions (recommended under 10).' ),
				'action' => [
					'url' => 'https://themes.artbees.net/docs/jupiter-x-performance-tips#revisions',
					'label' => esc_html__( 'Learn more' ),
				],
			],
		];

		if ( empty( $tip ) ) {
			return array_keys( $tips );
		}

		return $tips[ $tip ];
	}

	/**
	 * Check active plugins.
	 *
	 * @since 1.18.0
	 */
	private function check_updates() {

		$updates = wp_get_update_data();

		if (
			! empty( $updates['counts'] ) &&
			( $updates['counts']['plugins'] < 1 || $updates['counts']['themes'] < 1 )
		) {
			return;
		}

		$this->tips_response[] = $this->get_tips( 'updates' );

		return $this->tips_response;
	}

	/**
	 * Check active plugins.
	 *
	 * @since 1.18.0
	 */
	private function check_active_plugins() {
		$active_plugins = get_option( 'active_plugins', [] );

		if ( count( $active_plugins ) < 20 ) {
			return;
		}

		$this->tips_response[] = $this->get_tips( 'active_plugins' );

		return $this->tips_response;
	}

	/**
	 * Check development mode.
	 *
	 * @since 1.18.0
	 */
	private function check_development_mode() {
		if ( empty( jupiterx_get_option( 'dev_mode', false ) ) ) {
			return;
		}

		$this->tips_response[] = $this->get_tips( 'development_mode' );

		return $this->tips_response;
	}

	/**
	 * Check revisions.
	 *
	 * @since 1.18.0
	 */
	private function check_revisions() {
		$revisions = defined( 'WP_POST_REVISIONS' ) ? WP_POST_REVISIONS : true;

		if ( true === $revisions ) {
			$revisions = -1;
		} else {
			$revisions = intval( $revisions );
		}

		if ( -1 !== $revisions && 10 >= $revisions ) {
			return;
		}

		$this->tips_response[] = $this->get_tips( 'revisions' );

		return $this->tips_response;
	}

}

JupiterX_Core_Control_Panel_Performance_Tips::get_instance();
