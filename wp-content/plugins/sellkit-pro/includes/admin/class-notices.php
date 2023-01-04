<?php

defined( 'ABSPATH' ) || die();

/**
 * Check if Sellkit_Notices Class exists.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
if ( ! class_exists( 'Sellkit_Notices' ) ) {
	/**
	 * Components class.
	 *
	 * @since 1.1.0
	 */
	class Sellkit_Notices {
		/**
		 * Notice key.
		 *
		 * @since 1.1.1
		 * @var string
		 */
		public $key = 'sellkit-free-not-installed';

		/**
		 * Notice buttons.
		 *
		 * @since 1.1.1
		 * @var array
		 */
		public $buttons = [];

		/**
		 * Sellkit_Notices constructor.
		 *
		 * @since 1.1.1
		 */
		public function __construct() {
			$this->load_notices();
		}

		/**
		 * Load notices.
		 *
		 * @since NEXR
		 */
		public function load_notices() {
			add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
			add_action( 'admin_notices', [ $this, 'notice_content_wrapper' ], 1 );
		}

		/**
		 * Enqueue required assets because these won't be loaded if Sellkit Free is not installed.
		 *
		 * @since 1.1.1
		 */
		public function assets() {
			wp_enqueue_style(
				'sellkit-admin-notices-activation',
				sellkit_pro()->plugin_url() . 'assets/dist/css/admin.min.css',
				[],
				sellkit_pro()->version()
			);
		}

		/**
		 * Notice content wrapper.
		 *
		 * @since 1.1.1
		 */
		public function notice_content_wrapper() {
			?>
			<div class="sellkit-notice notice" data-key="<?php echo esc_attr( $this->key ); ?>">
				<div class="sellkit-notice-aside"><span class="sellkit-notice-aside-icon"><span></span></span></div>
				<div class="sellkit-notice-content">
					<div class="sellkit-notice-content-body">
						<?php echo esc_sql( $this->content_html() ); ?>
					</div>
					<div class="sellkit-notice-content-footer">
						<?php
						foreach ( $this->buttons as $url => $text ) {
							printf( '<a class="button-primary" href="%1s">%2s</a>', esc_url( $url ), esc_js( $text ) );
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Content of notice.
		 *
		 * @since 1.1.1
		 */
		public function content_html() {
			$message = sprintf(
				'<span>'
				. '<h3>'
				. esc_html__( 'Welcome to SellKit Pro', 'sellkit-pro' )
				. '</h3>'
				/* Translators: 1: bold SellKit Pro 2: bold SellKit Free */
				. esc_html__( 'The %1$s plugin requires the %2$s plugin to be installed & activated as well.', 'sellkit-pro' )
				. '</span>',
				'<b>SellKit Pro</b>',
				'<b>SellKit Free</b>'
			);

			return $message;
		}
	}

	new Sellkit_Notices();
}
