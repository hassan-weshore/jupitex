<?php

namespace Sellkit\Funnel\Steps;

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit_Sales_Page.
 *
 * @since 1.1.0
 */
class Sales_Page extends Base_Step {

	/**
	 * Sales_Page constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct();
	}

	/**
	 * Sales page actions.
	 *
	 * @since 1.5.0
	 */
	public function do_actions() {
		if ( empty( $this->sellkit_funnel->funnel_id ) ) {
			return;
		}

		if ( 'sales-page' !== $this->sellkit_funnel->current_step_data['type']['key'] ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		$this->contacts->add_new_log();
	}
}
