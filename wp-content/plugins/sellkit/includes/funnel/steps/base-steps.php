<?php

namespace Sellkit\Funnel\Steps;

use Sellkit\Database;
use Sellkit\Funnel\Contacts\Base_Contacts;
use Sellkit_Funnel;

defined( 'ABSPATH' ) || die();

/**
 * Base statistics class.
 *
 * @since 1.5.0
 */
class Base_Step {

	/**
	 * SellKit funnel class.
	 *
	 * @var Object|Sellkit_Funnel|null
	 * @since 1.5.0
	 */
	public $sellkit_funnel;

	/**
	 * SellKit funnel contact class.
	 *
	 * @var Base_Contact
	 * @since 1.5.0
	 */
	public $contacts;

	/**
	 * Base_Step constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		$this->sellkit_funnel = Sellkit_Funnel::get_instance();
		$this->contacts       = new Base_Contacts();
	}
}
