<?php
/**
 * This class is responsible to managing all JupiterX plugins.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 *
 * @package JupiterX_Core\Control_Panel_2
 */

class JupiterX_Core_Control_Panel_Install_Plugins {

	/**
	 * TGMPA Instance
	 *
	 * @var object
	 */
	protected $tgmpa;

	/**
	 * Artbees API.
	 *
	 * @var string
	 */
	protected $api_url = 'http://artbees.net/api/v2/';

	/**
	 * Reorder plugins results;
	 *
	 * @var array
	 */
	public $reorder_results = [];

	/**
	 * Class constructor.
	 *
	 * @since 1.18.0
	 */
	public function __construct() {
		if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
			return;
		}

		$menu_items_access = get_site_option( 'menu_items' );
		if ( is_multisite() && ! isset( $menu_items_access['plugins'] ) && ! current_user_can( 'manage_network_plugins' ) ) {
			return;
		}

		$this->tgmpa = isset( $GLOBALS['tgmpa'] ) ? $GLOBALS['tgmpa'] : TGM_Plugin_Activation::get_instance();

		add_action( 'wp_ajax_jupiterx_core_cp_get_plugins', [ $this, 'get_plugins_for_frontend' ] );

		add_action( 'wp_ajax_jupiterx_core_is_required_plugins_installed', [ $this, 'is_required_plugins_installed' ] );
	}

	/**
	 * Send a json list of plugins and their data and activation limit status for front-end usage.
	 *
	 * @since 1.18.0
	 */
	public function get_plugins_for_frontend() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
		}

		$plugins = jupiterx_core_get_plugins_from_api();

		$results       = [];
		$order_results = [];

		if ( is_wp_error( $plugins ) ) {
			wp_send_json_error( [ 'error' => $plugins->get_error_message() ] );
		}

		if ( isset( $plugins['raven'] ) ) {
			unset( $plugins['raven'] );
		}

		if ( version_compare( JUPITERX_VERSION, '2.0.0', '>=' ) && jupiterx_is_pro() ) {
			$sellkit['sellkit-pro'] = [
				'id' => 99999,
				'theme_name' => 'jupiterx',
				'name' => 'Sellkit Pro',
				'headline' => 'Funnel builder and checkout optimizer for WooCommerce to sell more, faster',
				'large_thumbnail' => trailingslashit( jupiterx_core()->plugin_assets_url() ) . 'images/control-panel/sellkit.png',
				'slug' => 'sellkit-pro',
				'is_callable' => 'Sellkit Pro',
				'file_path' => 'sellkit-pro/sellkit-pro.php',
				'basename' => 'sellkit-pro/sellkit-pro.php',
				'video' => '',
				'installed' => false,
				'active' => false,
				'network_active' => false,
				'install_disabled' => false,
				'label_type' => 'Optional',
				'is_pro' => true,
				'pro' => true,
				'required' => false,
				'recommended' => false,
				'version' => '',
				'desc' => 'Boost sales with sales funnels, increase order value with optimized checkout and boost engagement with personalized discounts, coupons and alerts.',
				'source' => '',
				'install_url' => '',
				'activate_url' => '',
				'update_url' => '',
				'wp_activate_url' => jupiterx_core_get_wp_action_url( 'sellkit-pro/sellkit-pro.php', 'activate' ),
			];

			$sellkit['sellkit'] = [
				'id' => 99998,
				'theme_name' => 'jupiterx',
				'name' => 'Sellkit',
				'slug' => 'sellkit',
				'is_callable' => 'Sellkit',
				'file_path' => 'sellkit/sellkit.php',
				'basename' => 'sellkit/sellkit.php',
				'video' => '',
				'installed' => false,
				'active' => false,
				'network_active' => false,
				'install_disabled' => false,
				'label_type' => 'Optional',
				'is_pro' => true,
				'pro' => true,
				'required' => false,
				'recommended' => false,
				'version' => '',
				'source' => 'wp-repo',
				'install_url' => '',
				'activate_url' => '',
				'update_url' => '',
				'wp_activate_url' => jupiterx_core_get_wp_action_url( 'sellkit/sellkit.php', 'activate' ),
			];

			$plugins = array_merge( $sellkit, $plugins );

			$increment = 0;
			foreach ( $plugins as $key => $plugin ) {
				$results = $this->reorder_optional_plugins( $key, $plugin, ++$increment );
				ksort( $results );
			}

			foreach ( $results as $result ) {
				$order_results = array_merge( $order_results, $result );
			}

			$plugins = $order_results;
		}

		$plugins = jupiterx_core_update_plugins_status( $plugins );

		foreach ( $plugins as $plugin ) {
			if ( empty( $plugin['file_path'] ) ) {
				$plugin['file_path'] = $plugin['basename'];
			}
		}

		return wp_send_json( [
			'plugins' => $plugins,
			'bulk_actions' => $this->get_plugin_bulk_actions( $plugins ),
		] );
	}

	/**
	 * Reorder array of plugins.
	 *
	 * @since 2.0.0
	 *
	 * @param string  $key       Plugin key.
	 * @param array   $plugin    Plugin data.
	 * @param integer $increment Loop increment.
	 *
	 * @return array
	 */
	public function reorder_optional_plugins( $key, $plugin, $increment ) {
		if ( 'elementor' === $key ) {
			$this->reorder_results[5] = [ $key => $plugin ];

			return $this->reorder_results;
		}

		if ( array_key_exists( $increment, $this->reorder_results ) ) {
			++$increment;
		}

		switch ( $key ) {
			case 'jet-elements':
				$this->reorder_results[6] = [ $key => $plugin ];
				break;
			case 'sellkit-pro':
				$this->reorder_results[7] = [ $key => $plugin ];
				break;
			case 'jet-woo-builder':
				$this->reorder_results[8] = [ $key => $plugin ];
				break;
			case 'jet-popup':
				$this->reorder_results[9] = [ $key => $plugin ];
				break;
			case 'wunderwp':
				$this->reorder_results[10] = [ $key => $plugin ];
				break;
			case 'marketing-automation-and-personalization':
				$this->reorder_results[11] = [ $key => $plugin ];
				break;
			case 'revslider':
				$this->reorder_results[19] = [ $key => $plugin ];
				break;
			default:
				$this->reorder_results[ $increment ] = [ $key => $plugin ];
				break;
		}

		return $this->reorder_results;
	}

	/**
	 * Get Plugin Bulk Actions.
	 *
	 * @since 1.18.0
	 *
	 * @param array $plugins Plugins list.
	 *
	 * @return array
	 */
	public function get_plugin_bulk_actions( $plugins ) {
		return [
			'activate_required_plugins' => [
				'url' => admin_url( 'plugins.php' ),
				'action' => 'activate-selected',
				'action2' => -1,
				'_wpnonce' => wp_create_nonce( 'bulk-plugins' ),
				'checked' => $this->get_required_plugins_slug( $plugins, 'basename' ),
			],
			'install_required_plugins' => [
				'url' => admin_url( 'themes.php?page=tgmpa-install-plugins' ),
				'action' => 'tgmpa-bulk-install',
				'action2' => -1,
				'_wpnonce' => wp_create_nonce( 'bulk-plugins' ),
				'tgmpa-page' => 'tgmpa-install-plugins',
				'plugin' => $this->get_required_plugins_slug( $plugins, 'slug' ),
			],
		];
	}

	/**
	 * Get plugin slugs for bulk action.
	 *
	 * @since 1.18.0
	 *
	 * @param array $plugins Plugins list.
	 * @param string $field Plugin slug or basename.
	 *
	 * @return array
	 */
	private function get_required_plugins_slug( $plugins, $field ) {
		$slugs = [];

		if ( ! is_array( $plugins ) ) {
			return $slugs;
		}

		foreach ( $plugins as $plugin ) {
			if ( 'true' === $plugin['required'] ) {
				$slugs[] = $plugin[ $field ];
			}
		}

		return $slugs;
	}

	/**
	 * Check if required plugins is installed or not.
	 * Is used for dashboard welcome box , quick check links.
	 *
	 * @since 2.0.0
	 */
	public function is_required_plugins_installed() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			wp_send_json_error();
		}

		if ( ! class_exists( 'ACF' ) ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}
}

new JupiterX_Core_Control_Panel_Install_Plugins();
