<?php
namespace Jet_Popup\Compatibility;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * [$registered_subpage_modules description]
	 * @var array
	 */
	private $registered_mobules = array();

	/**
	 * Constructor for the class
	 */
	function __construct() {
		$this->registered_mobules = apply_filters( 'jet-popup/compatibility-manager/registered-plugins', [
			'woocommerce' => array(
				'class'    => '\\Jet_Popup\\Compatibility\\Woocommerce',
				'instance' => false,
				'path'     => jet_popup()->plugin_path( 'includes/compatibility/plugins/woocommerce/manager.php' ),
			),
			'wpml' => array(
				'class'    => '\\Jet_Popup\\Compatibility\\WPML',
				'instance' => false,
				'path'     => jet_popup()->plugin_path( 'includes/compatibility/plugins/wpml/manager.php' ),
			),
			'polylang' => array(
				'class'    => '\\Jet_Popup\\Compatibility\\Polylang',
				'instance' => false,
				'path'     => jet_popup()->plugin_path( 'includes/compatibility/plugins/polylang/manager.php' ),
			),
			'jet-engine' => array(
				'class'    => '\\Jet_Popup\\Compatibility\\Jet_Engine',
				'instance' => false,
				'path'     => jet_popup()->plugin_path( 'includes/compatibility/plugins/jet-engine/manager.php' ),
			),
			'jet-form-builder' => array(
				'class'    => '\\Jet_Popup\\Compatibility\\Jet_Form_Builder',
				'instance' => false,
				'path'     => jet_popup()->plugin_path( 'includes/compatibility/plugins/jet-form-builder/manager.php' ),
			),
		] );

		$this->load_compatibility_modules();
	}

	/**
	 * [maybe_load_theme_module description]
	 * @return [type] [description]
	 */
	public function load_compatibility_modules() {

		$this->registered_mobules = array_map( function( $module_data ) {
			$class = $module_data['class'];

			if ( file_exists( $module_data['path'] ) ) {
				require $module_data['path'];
			}

			if ( ! $module_data['instance'] && class_exists( $class ) ) {
				$module_data['instance'] = new $class();
			}

			return $module_data;
		}, $this->registered_mobules );

	}

}
