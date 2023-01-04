<?php

namespace Sellkit_Pro\Contact_Segmentation;

use Sellkit_pro\Contact_Segmentation\Conditions;

defined( 'ABSPATH' ) || die();

/**
 * Class Contact_Segmentation
 *
 * @package Sellkit\Contact_Segmentation
 * @since 1.1.0
 */
class Contact_Segmentation {

	/**
	 * Conditions object.
	 *
	 * @var array
	 * @since 1.1.0
	 */
	public $conditions;

	/**
	 * Contact_Segmentation constructor.
	 */
	public function __construct() {
		if ( strpos($_SERVER[ 'REQUEST_URI' ], '/wp-json/' ) !== false ) { // phpcs:ignore
			return;
		}

		$this->load_contact_segmentation_files();
	}

	/**
	 * Load contact segmentation files.
	 *
	 * @since 1.1.0
	 */
	public function load_contact_segmentation_files() {
		sellkit_pro()->load_files( [
			'contact-segmentation/conditions',
		] );

		if ( ! sellkit_pro()->has_valid_dependencies() ) {
			return;
		}

		sellkit()->load_files( [
			'contact-segmentation/libraries/mobile-detect',
			'contact-segmentation/operators',
			'contact-segmentation/functions',
			'contact-segmentation/contact-data',
			'contact-segmentation/contact-data-updater',
		] );

		$this->conditions = new Conditions();
	}
}

new Contact_Segmentation();
