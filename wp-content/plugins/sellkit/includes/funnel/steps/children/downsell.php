<?php

namespace Sellkit\Funnel\Steps;

use Sellkit\Funnel\Analytics\Data_Updater;
use Sellkit_Funnel;
use Sellkit\Funnel\Steps\Upsell;

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit Downsell.
 *
 * @since 1.5.0
 */
class Downsell extends Upsell {
	/**
	 * Upsell constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct();
	}
}
