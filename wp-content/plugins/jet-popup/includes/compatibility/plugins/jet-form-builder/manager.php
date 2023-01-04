<?php
namespace Jet_Popup\Compatibility;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Jet_Form_Builder {

	/**
	 * Include files
	 */
	public function load_files() {}

	/**
	 * [__construct description]
	 */
	public function __construct() {

		if ( ! defined( 'JET_FORM_BUILDER_VERSION' ) ) {
			return false;
		}

		$this->load_files();

	}

}
