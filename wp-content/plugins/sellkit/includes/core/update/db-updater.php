<?php

namespace Sellkit\Core\Update;

use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Class Data base Updater.
 *
 * @package Sellkit\Contact_Segmentation
 * @SuppressWarnings(ExcessiveClassComplexity)
 * @since 1.1.0
 */
class Db_Updater extends \WP_Background_Process {

	/**
	 * SellKit db version.
	 *
	 * @since 1.1.0
	 * @var $db_version
	 */
	public $db_version;

	/**
	 * Queue Action.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $action = 'sellkit-database-updater';

	/**
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @since 1.1.0
	 * @param mixed $data Queue item to iterate over.
	 */
	protected function task( $data ) {
		if ( empty( $data['callback_function'] ) || empty( $data['db_version'] ) ) {
			return false;
		}

		$this->db_version  = $data['db_version'];
		$updater_functions = new Updater_Functions();

		call_user_func( [ $updater_functions, $data['callback_function'] ] );

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 *
	 * @since 1.1.0
	 */
	protected function complete() {
		parent::complete();

		sellkit_update_option( 'current_db_version', $this->db_version );
	}
}
