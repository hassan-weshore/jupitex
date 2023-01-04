<?php

namespace Sellkit_Pro\Core\Update;

use Sellkit_pro\Core\Install;

defined( 'ABSPATH' ) || die();

/**
 * Class Updater functions.
 *
 * @since 1.1.0
 */
class Updater_Functions {

	/**
	 * First background process updating function.
	 *
	 * @since 1.1.0
	 */
	public function check_database_tables_1_0_0() {
		Install::check_database_tables();
	}
}
