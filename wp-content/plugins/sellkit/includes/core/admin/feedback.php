<?php

namespace Sellkit\Core\Admin;

defined( 'ABSPATH' ) || die();

/**
 * Class Feedback.
 *
 * @since 1.1.0
 */
class Feedback {

	/**
	 * Install constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'current_screen', function () {
			if ( ! $this->is_plugins_screen() ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_feedback_dialog_scripts' ] );
		} );

		add_action( 'wp_ajax_sellkit_deactivate_feedback', [ $this, 'send_feedback_data' ] );
	}

	/**
	 * Sends feedback data
	 *
	 * @since 1.1.0
	 */
	public function send_feedback_data() {
		$nonce       = sellkit_htmlspecialchars( INPUT_POST, '_wpnonce' );
		$reason_key  = sellkit_htmlspecialchars( INPUT_POST, 'reason_key' );
		$description = sellkit_htmlspecialchars( INPUT_POST, 'description' );

		if ( empty( wp_verify_nonce( $nonce, 'sellkit_deactivate_feedback_nonce' ) ) ) {
			wp_send_json_error( __( 'Nonce is wrong.', 'sellkit' ) );
		}

		$remote = wp_remote_post(
			'https://my.getsellkit.com/wp-json/sellkit/v1/customer/feedback',
			[
				'timeout' => 10,
				'body'    => [
					'intent' => 'Plugin Deactivation',
					'answer_id' => $reason_key,
					'answer_desc' => $description, // For other reason description.
				],
			]
		);

		if ( is_wp_error( $remote ) ) {
			wp_send_json_error( __( 'Something went wrong.', 'sellkit' ) );
		}

		wp_send_json_success( __( 'Deactivation feedback is sent.', 'sellkit' ) );
	}

	/**
	 * Checks if current screen is plugin.
	 *
	 * @since 1.1.0
	 */
	private function is_plugins_screen() {
		return in_array( get_current_screen()->id, [ 'plugins', 'plugins-network' ], true );
	}

	/**
	 * Enqueue feedback dialog scripts.
	 * Registers the feedback dialog scripts and enqueues them.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function enqueue_feedback_dialog_scripts() {
		add_action( 'admin_footer', [ $this, 'print_deactivate_feedback_dialog' ] );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'sellkit-admin-feedback',
			sellkit()->plugin_url() . 'assets/dist/js/admin-feedback' . $suffix . '.js',
			[ 'jquery', 'wp-util' ],
			sellkit()->version(),
			true
		);

		wp_enqueue_script( 'sellkit-admin-feedback' );

		wp_localize_script( 'sellkit-admin-feedback', 'sellkit_feedback', [
			'spinIcon' => site_url( '/wp-admin/images/wpspin_light.gif' ),
		] );

		wp_enqueue_style(
			'sellkit-feedback',
			sellkit()->plugin_url() . 'assets/dist/css/admin-feedback' . $suffix . '.css',
			[],
			sellkit()->version()
		);
	}

	/**
	 * Print deactivate feedback dialog.
	 *
	 * @since 1.1.0
	 */
	public function print_deactivate_feedback_dialog() {
		$deactivate_reasons = [
			'1' => [
				'title' => esc_html__( 'It is a temporary deactivation, I am just debugging an issue', 'sellkit' ),
			],
			'2' => [
				'title' => esc_html__( 'I need more feature', 'sellkit' ),
			],
			'3' => [
				'title' => esc_html__( 'I no longer need it', 'sellkit' ),
			],
			'4' => [
				'title' => esc_html__( 'Customer service was less than expected', 'sellkit' ),
			],
			'5' => [
				'title' => esc_html__( 'Ease of use was less than expected', 'sellkit' ),
			],
			'6' => [
				'title' => esc_html__( 'Quality was less than expected', 'sellkit' ),
			],
			'7' => [
				'title' => esc_html__( 'Lack of integration with my tools', 'sellkit' ),
			],
			'8' => [
				'title' => esc_html__( 'Other reason', 'sellkit' ),
				'input_placeholder' => esc_html__( 'Please share the reason', 'sellkit' ),
			],
		];

		?>
		<div id="sellkit-deactivate-feedback-dialog-wrapper">
			<div id="sellkit-deactivate-feedback-dialog-header">
				<img src="<?php echo sellkit()->plugin_url() . 'assets/img/icons/sellkit-logo.svg'; ?>">
				<span id="sellkit-deactivate-feedback-dialog-header-title"><?php echo esc_html__( 'Sellkit Feedback', 'sellkit' ); ?></span>
			</div>
			<form id="sellkit-deactivate-feedback-dialog-form" method="post">
				<?php
				wp_nonce_field( 'sellkit_deactivate_feedback_nonce' );
				?>
				<input type="hidden" name="action" value="sellkit_deactivate_feedback" />

				<div id="sellkit-deactivate-feedback-dialog-form-caption"><?php echo esc_html__( 'May we have a little info about why you are deactivating?', 'sellkit' ); ?></div>
				<div id="sellkit-deactivate-feedback-dialog-form-body">
					<?php foreach ( $deactivate_reasons as $reason_key => $reason ) : ?>
						<div class="sellkit-deactivate-feedback-dialog-input-wrapper">
							<input id="sellkit-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" class="sellkit-deactivate-feedback-dialog-input" <?php echo 1 === $reason_key ? 'checked' : ''; ?> type="radio" name="reason_key" value="<?php echo esc_attr( $reason_key ); ?>" />
							<label for="sellkit-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" class="sellkit-deactivate-feedback-dialog-label"><?php echo esc_html( $reason['title'] ); ?></label>
							<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
								<input class="sellkit-feedback-text" type="text" name="description" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>" />
							<?php endif; ?>
							<?php if ( ! empty( $reason['alert'] ) ) : ?>
								<div class="sellkit-feedback-text-alert"><?php echo esc_html( $reason['alert'] ); ?></div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="sellkit-deactivate-feedback-footer wp-core-ui">
					<input type="button" class="button action sellkit-deactivate-feedback-cancel-button" value="<?php echo esc_html__( 'Discard', 'sellkit' ); ?>">
					<div>
						<img class="sellkit-deactivate-feedback-footer-spinner-icon sellkit-hide" src="<?php echo site_url( '/wp-admin/images/wpspin_light.gif' ); ?>">
						<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__( 'Submit & Deactivate', 'sellkit' ); ?>">
					</div>
				</div>
			</form>
		</div>
		<?php
	}
}

new Feedback();
