<?php


namespace Sellkit\Funnel\Contacts;

use Sellkit\Database;
use Sellkit_Funnel;

defined( 'ABSPATH' ) || die();

/**
 * Base contacts class.
 *
 * @since 1.5.0
 */
class Base_Contacts {

	/**
	 * SellKit database.
	 *
	 * @var Database
	 * @since 1.5.0
	 */
	public $db;

	/**
	 * SellKit funnel class.
	 *
	 * @var Object|Sellkit_Funnel|null
	 * @since 1.5.0
	 */
	public $sellkit_funnel;

	/**
	 * Base_Contacts constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		if ( headers_sent() ) {
			return;
		}

		if ( ! session_id() ) {
			session_start();
		}

		$this->sellkit_funnel = Sellkit_Funnel::get_instance();
		$this->db             = new Database();
	}

	/**
	 * Created a new log.
	 *
	 * @since 1.5.0
	 */
	public function add_new_log() {
		$analytics_id = $this->db->insert( 'funnel_contact', [
			'funnel_id' => $this->sellkit_funnel->funnel_id,
			'user_id' => get_current_user_id(),
			'updated_at' => time(),
			'created_at' => time(),
		] );

		$_SESSION['entered_funnel_id'] = $analytics_id;
	}

	/**
	 * Adds accept status.
	 *
	 * @since 1.5.0
	 */
	public static function step_is_passed() {
		$database = new Database();
		$funnel   = Sellkit_Funnel::get_instance();

		if ( empty( $funnel->funnel_id ) ) {
			return;
		}

		$type       = $funnel->current_step_data['type']['key'];
		$old_values = [];

		// Getting old data.
		$result = $database->get( 'funnel_contact', [ 'id' => $_SESSION['entered_funnel_id'] ] );

		if ( ! empty( $result[0][ $type ] ) ) {
			$old_values = unserialize( $result[ 0 ][ $type ] ); // phpcs:ignore
		}

		$new_values = array_merge( $old_values, [ $funnel->current_step_data['page_id'] ] );

		$database->update(
			'funnel_contact',
			[ $funnel->current_step_data['type']['key'] => $new_values ],
			[ 'id' => $_SESSION['entered_funnel_id'] ]
		);
	}

	/**
	 * Adds price to total spent column of this funnel.
	 *
	 * @param string $price Price value.
	 * @since 1.5.0
	 */
	public function add_total_spent( $price ) {
		// Getting old data.
		$result    = $this->db->get( 'funnel_contact', [ 'id' => $_SESSION['entered_funnel_id'] ] );
		$old_spent = ! empty( $result[0]['total_spent'] ) ? $result[0]['total_spent'] : 0;

		$new_spent = floatval( $old_spent ) + floatval( $price );

		$this->db->update(
			'funnel_contact',
			[ 'total_spent' => $new_spent ],
			[ 'id' => $_SESSION['entered_funnel_id'] ]
		);
	}
}
