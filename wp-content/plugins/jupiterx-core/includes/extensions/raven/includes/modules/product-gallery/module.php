<?php
namespace JupiterX_Core\Raven\Modules\Product_Gallery;

use JupiterX_Core\Raven\Base\Module_base;

defined( 'ABSPATH' ) || die();

class Module extends Module_Base {
	public function get_widgets() {
		return [ 'product-gallery' ];
	}

	public static function is_active() {
		return function_exists( 'WC' );
	}
}
