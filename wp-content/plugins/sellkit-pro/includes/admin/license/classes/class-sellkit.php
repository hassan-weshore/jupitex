<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       Artbees.net
 * @since      1.0.0
 *
 * @package    Sellkit
 * @subpackage Sellkit/includes
 */

if ( ! class_exists( 'Sellkit_Pro_Connect' ) ) {
	/**
	 * The core plugin class.
	 *
	 * This is used to define internationalization, admin-specific hooks, and
	 * public-facing site hooks.
	 *
	 * Also maintains the unique identifier of this plugin as well as the current
	 * version of the plugin.
	 *
	 * @since      1.0.0
	 * @package    Sellkit
	 * @subpackage Sellkit/includes
	 * @author     Artbees <info@artbees.net>
	 */
	class Sellkit_Pro_Connect {

		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      Sellkit_Loader    $loader    Maintains and registers all hooks for the plugin.
		 */
		protected $loader;

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;

		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $version    The current version of the plugin.
		 */
		protected $version;

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			if ( defined( 'SELLKIT_VERSION' ) ) {
				$this->version = SELLKIT_VERSION;
			} else {
				$this->version = '1.0.0';
			}
			$this->plugin_name = 'sellkit-pro';

			$this->load_dependencies();
			$this->define_admin_hooks();
		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * Include the following files that make up the plugin:
		 *
		 * - Sellkit_Loader. Orchestrates the hooks of the plugin.
		 * - Sellkit_i18n. Defines internationalization functionality.
		 * - Sellkit_Admin. Defines all hooks for the admin area.
		 * - Sellkit_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function load_dependencies() {

			sellkit_pro()->load_files( [
				'admin/license/classes/class-sellkit-loader',
				'admin/license/classes/class-sellkit-license',
				'admin/license/classes/class-sellkit-extras',
				'admin/license/classes/class-updates-api',
			] );

			$this->loader = new Sellkit_Loader();

		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_admin_hooks() {

			add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets' ] );
		}

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function run() {
			$this->loader->run();
		}

		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    string    The name of the plugin.
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since     1.0.0
		 * @return    Sellkit_Loader    Orchestrates the hooks of the plugin.
		 */
		public function get_loader() {
			return $this->loader;
		}

		/**
		 * Retrieve the version number of the plugin.
		 *
		 * @since     1.0.0
		 * @return    string    The version number of the plugin.
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Enqueue admin assets.
		 *
		 * @since 1.1.0
		 * @return void
		 */
		public function admin_assets() {
			wp_enqueue_script(
				$this->plugin_name,
				sellkit_pro()->plugin_url() . 'includes/admin/license/assets/admin/pro.js',
				[ 'jquery' ],
				$this->version,
				false
			);

			wp_enqueue_style(
				$this->plugin_name,
				sellkit_pro()->plugin_url() . 'includes/admin/license/assets/admin/style.css',
				[],
				$this->version,
				'all'
			);
		}

	}
}
