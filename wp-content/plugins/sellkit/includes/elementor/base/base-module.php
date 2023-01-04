<?php

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit_Elementor_Base_Module
 *
 * @since 1.1.0
 */
abstract class Sellkit_Elementor_Base_Module {

	/**
	 * The class instance.
	 *
	 * @var array
	 */
	public static $instances = [];

	/**
	 * Get the current class name.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public static function class_name() {
		return get_called_class();
	}

	/**
	 * Gets the instance.
	 *
	 * @since 1.1.0
	 * @return mixed
	 */
	public static function get_instance() {
		if ( empty( static::$instances[ static::class_name() ] ) ) {
			static::$instances[ static::class_name() ] = new static();
		}

		return static::$instances[ static::class_name() ];
	}

	/**
	 *
	 * Gets the widgets
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_widgets() {
		return [];
	}

	/**
	 * Class constructor
	 *
	 * @since 1.1.0
	 * Sellkit_Elementor_Base_Module constructor.
	 */
	public function __construct() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
	}

	/**
	 * Register widgets.
	 *
	 * @since 1.1.0
	 * @param object $widgets_manager Widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		foreach ( $this->get_widgets() as $widget_name ) {
			// Prepare class name.
			$class_name = str_replace( '-', ' ', $widget_name );
			$class_name = str_replace( ' ', '_', ucwords( $class_name ) );
			$class_name = "Sellkit_Elementor_{$class_name}_Widget";

			// Prepare class path.
			$class_path = "elementor/modules/{$widget_name}/widgets/{$widget_name}";

			// Require.
			sellkit()->load_files( [ $class_path ] );

			// Register.
			if ( $class_name::is_active() ) {
				$widgets_manager->register_widget_type( new $class_name() );
			}
		}
	}

	/**
	 * Check if the module is active.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public static function is_active() {
		return true;
	}
}
