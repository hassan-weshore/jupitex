<?php

namespace Sellkit\Admin\Notices;

defined( 'ABSPATH' ) || die();

/**
 * Notice base class.
 *
 * @since 1.1.0
 */
abstract class Notice_Base {
	/**
	 * Dismissed notices.
	 *
	 * @since 1.1.0
	 * @var array|bool|mixed
	 */
	public $dismissed_notices;

	/**
	 * Notice content.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $content;

	/**
	 * Notice buttons.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	public $buttons;

	/**
	 * Notice key
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $key;

	/**
	 * Notice_Base constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$dismissed_notices = sellkit_get_option( 'dismissed_notices', '' );

		$this->dismissed_notices = empty( $dismissed_notices ) ? [] : $dismissed_notices;
	}

	/**
	 * Notice content wrapper.
	 *
	 * @since 1.1.0
	 */
	public function notice_content_wrapper() {
		?>
		<div class="sellkit-notice notice is-dismissible" data-key="<?php echo esc_attr( $this->key ); ?>">
			<div class="sellkit-notice-aside"><span class="sellkit-notice-aside-icon"><span></span></span></div>
			<div class="sellkit-notice-content">
				<div class="sellkit-notice-content-body">
					<?php echo esc_sql( $this->content ); ?>
				</div>
				<div class="sellkit-notice-content-footer">
					<?php
					foreach ( $this->buttons as $url => $text ) {
						printf( '<a class="button-primary" href="%1s">%2s</a>', esc_url( $url ), esc_js( $text ) );
					}
					?>
				</div>
			</div>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
		<?php
	}
}
