<?php
namespace JupiterX_Core\Raven\Modules\Post_Meta;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_base;

class Module extends Module_Base {

	/**
	 * Register module widgets.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_widgets() {
		return [ 'post-meta' ];
	}
}
