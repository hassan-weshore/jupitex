<?php

use Elementor\Utils as ElementorUtils;

defined( 'ABSPATH' ) || die();

/**
 * Class Sellkit_Funnel
 *
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Sellkit_Funnel {

	/**
	 * Funnel Id.
	 *
	 * @var integer Funnel Id.
	 * @since 1.1.0
	 */
	public $funnel_id;

	/**
	 * Next step data.
	 *
	 * @var array $next_step_data.
	 * @since 1.1.0
	 */
	public $next_step_data;

	/**
	 * Next no step data.
	 *
	 * @var array $next_no_step_data.
	 * @since 1.5.0
	 */
	public $next_no_step_data;

	/**
	 * Ending node step data.
	 *
	 * @var array $end_node_step_data.
	 * @since 1.5.0
	 */
	public $end_node_step_data;

	/**
	 * The current step data.
	 *
	 * @var array $current_step_data.
	 * @since 1.1.0
	 */
	public $current_step_data;

	/**
	 * The class instance.
	 *
	 * @var Object Class instance.
	 * @since 1.1.0
	 */
	public static $instance = null;

	/**
	 * Sellkit_Funnel constructor.
	 *
	 * @param string $page_id Page Id.
	 * @since 1.1.0
	 */
	public function __construct( $page_id = null ) {
		$this->order_customization();

		if ( null === $page_id ) {
			$page_id = self::get_page_id();
		}

		if ( empty( $page_id ) ) {
			return;
		}

		$this->set_funnel_data( $page_id );

		if ( empty( $this->funnel_id ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_funnel_frontend_script' ] );
	}

	/**
	 * Class Instance.
	 *
	 * @since 1.1.0
	 * @return Sellkit_Funnel|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Gets funnel if the page is step otherwise do nothing.
	 *
	 * @since 1.1.0
	 * @return void
	 * @param string $page_id Page id.
	 */
	public function set_funnel_data( $page_id ) {
		$this->current_step_data = get_post_meta( $page_id, 'step_data', true );

		if ( empty( $this->current_step_data['funnel_id'] ) ) {
			return;
		}

		$is_nofollow = sellkit_get_option( 'disallow_funnels_from_search_engine' );

		if ( true === (bool) $is_nofollow ) {
			add_action( 'wp_head', function () {
				echo '<meta name="robots" content="noindex" />';
			} );
		}

		$funnel_data  = (array) get_post_meta( $this->current_step_data['funnel_id'], 'nodes', true );
		$is_flowchart = true;

		if ( empty( $funnel_data ) ) {
			$funnel_data  = get_post_meta( $this->current_step_data['funnel_id'], 'sellkit_steps', true );
			$is_flowchart = false;
		}

		$this->funnel_id = $this->current_step_data['funnel_id'];

		if ( false === $is_flowchart ) {
			$this->next_step_data = ! empty( $funnel_data[ $this->current_step_data['number'] + 1 ] ) ? $funnel_data[ $this->current_step_data['number'] + 1 ] : false;
		}

		if (
			true === $is_flowchart &&
			! empty( $this->current_step_data['targets'] ) &&
			! empty( $funnel_data[ $this->current_step_data['targets'][0]['nodeId'] ] )
		) {
			$this->next_step_data = 'none' !== $funnel_data[ $this->current_step_data['targets'][0]['nodeId'] ] ? $funnel_data[ $this->current_step_data['targets'][0]['nodeId'] ] : false;
		}

		if ( true === $is_flowchart && ! empty( $this->current_step_data['targets'][1]['nodeId'] ) ) {
			$this->next_no_step_data = ! empty( $funnel_data[ $this->current_step_data['targets'][1]['nodeId'] ] ) ? $funnel_data[ $this->current_step_data['targets'][1]['nodeId'] ] : false;
		}

		$end_node_id = $this->get_end_node_id( $funnel_data );

		if ( ! empty( $end_node_id ) ) {
			$this->end_node_step_data = $funnel_data[ $end_node_id ];
		}
	}

	/**
	 * Enqueue funnel frontend scripts.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function enqueue_funnel_frontend_script() {
		$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'funnel-frontend',
			sellkit()->plugin_url() . 'assets/dist/js/funnel-frontend' . $suffix . '.js',
			[ 'jquery', 'wp-util' ],
			sellkit()->version(),
			true
		);

		wp_localize_script( 'funnel-frontend', 'funnel', $this->get_localize_data() );

		if ( empty( sellkit_get_option( 'google_analytics' ) ) && empty( sellkit_get_option( 'facebook_pixel' ) ) ) {
			return;
		}

		wp_enqueue_script(
			'funnel-settings-variables',
			sellkit()->plugin_url() . 'assets/dist/js/funnel-settings-variables' . $suffix . '.js',
			[ 'jquery', 'wp-util' ],
			sellkit()->version(),
			true
		);
	}

	/**
	 * Get localize data.
	 *
	 * @return array
	 */
	public function get_localize_data() {
		if ( ! empty( $this->next_step_data['page_id'] ) ) {
			$next_step_link = add_query_arg( [
				'post_type' => Sellkit_Admin_Steps::SELLKIT_STEP_POST_TYPE,
				'p'         => $this->next_step_data['page_id'],
			], site_url() );
		}

		return [
			'currentStep' => $this->current_step_data,
			'nextStep' => $this->next_step_data,
			'nextStepLink' => ! empty( $next_step_link ) ? $next_step_link : '',
			'siteUrl' => site_url(),
		];
	}

	/**
	 * Gets page id.
	 *
	 * @since 1.1.0
	 * @return false|int
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public static function get_page_id() {
		$page_id   = sellkit_htmlspecialchars( INPUT_POST, 'sellkit_current_page_id' );
		$preview   = sellkit_htmlspecialchars( INPUT_GET, 'preview' );
		$structure = get_option( 'permalink_structure' );

		if ( ! empty( $preview ) ) {
			return false;
		}

		if ( is_numeric( $page_id ) ) {
			return $page_id;
		}

		$current_url = esc_url_raw( remove_query_arg( 'order-key' ) );

		if ( ! empty( $structure ) ) {
			$current_url = self::get_base_url( $current_url );
		}

		if ( empty( strpos( $current_url, 'sellkit_step' ) ) ) {
			return false;
		}

		if ( empty( $current_url ) ) {
			return false;
		}

		$page_id   = sellkit_htmlspecialchars( INPUT_GET, 'p' );
		$page_slug = sellkit_htmlspecialchars( INPUT_GET, 'sellkit_step' );
		$page      = get_page_by_path( basename( untrailingslashit( $current_url ) ), null, 'sellkit_step' );

		if ( empty( $page ) && ! empty( $page_slug ) ) {
			$page = get_page_by_path( sanitize_text_field( $page_slug ), null, 'sellkit_step' );
		}

		if ( empty( $page ) && empty( $page_id ) ) {
			return false;
		}

		if ( ! empty( $page_id ) && 'sellkit_step' === get_post_type( $page_id ) ) {
			return $page_id;
		}

		if ( empty( $page->ID ) ) {
			return false;
		}

		return $page->ID;
	}

	/**
	 * Gets main url.
	 *
	 * @since 1.5.4
	 * @param string $url Url.
	 * @return string
	 */
	private static function get_base_url( $url ) {
		$parsed = wp_parse_url( $url );

		if ( empty( $parsed['query'] ) ) {
			return $url;
		}

		return explode( '?', $url )[0];
	}

	/**
	 * Customization order pages in admin area.
	 *
	 * @since 1.1.0
	 */
	public function order_customization() {
		if ( ! is_admin() ) {
			return;
		}

		// Orders filters.
		add_filter( 'woocommerce_get_formatted_order_total', [ $this, 'add_order_type' ], 10, 2 );
		add_action( 'woocommerce_admin_order_data_after_order_details', [ $this, 'show_linked_orders' ], 10, 1 );
	}

	/**
	 * Adding order type to order list table.
	 *
	 * @since 1.1.0
	 * @return string
	 * @param string $formatted_total formatted total price.
	 * @param object $order Order object.
	 */
	public function add_order_type( $formatted_total, $order ) {
		$screen    = get_current_screen();
		$is_upsell = $order->get_meta( 'sellkit_main_order_id' );

		if ( ! $screen || 'edit' !== $screen->base || 'shop_order' !== $screen->post_type ) {
			return $formatted_total;
		}

		if ( is_numeric( $is_upsell ) ) {
			$formatted_total .= '<div><span>' . esc_html__( 'Sellkit Upsell', 'sellkit' ) . '</span></div>';
		}

		return $formatted_total;
	}

	/**
	 * Show the related between orders.
	 *
	 * @since 1.1.0
	 * @param object $order Order object.
	 */
	public function show_linked_orders( $order ) {
		$upsell_order_id = $order->get_meta( 'sellkit_upsell_order_id' );
		$main_order_id   = $order->get_meta( 'sellkit_main_order_id' );

		if ( is_numeric( $main_order_id ) ) {
			$parent_order       = wc_get_order( $main_order_id );
			$order_number       = $parent_order->get_order_number();
			$parent_order_html  = sprintf( '<p class="form-field form-field-wide" style= "margin-top: 20px;"><strong>%s: </strong>', esc_html__( 'SellKit Parent Order', 'sellkit' ) );
			$parent_order_html .= sprintf( '<span style= "display: block;"><a href="%1s"><strong>#%2s</strong></a></span>', get_edit_post_link( $main_order_id ), esc_attr( $order_number ) );
			$parent_order_html .= '</p>';
			echo $parent_order_html;
		}

		if ( is_numeric( $upsell_order_id ) ) {
			$upsell_order       = wc_get_order( $upsell_order_id );
			$order_number       = $upsell_order->get_order_number();
			$upsell_order_html  = sprintf( '<p class="form-field form-field-wide" style= "margin-top: 20px;"><strong>%s: </strong>', esc_html__( 'SellKit Upsell Order', 'sellkit' ) );
			$upsell_order_html .= sprintf( '<span style= "display: block;"><a href="%1s"><strong>#%2s</strong></a></span>', get_edit_post_link( $upsell_order_id ), esc_attr( $order_number ) );
			$upsell_order_html .= '</p>';
			echo $upsell_order_html;
		}
	}

	/**
	 * Gets end node.
	 *
	 * @since 1.5.0
	 * @param array $funnels Funnels array.
	 */
	public function get_end_node_id( $funnels ) {
		$funnels = (array) $funnels;

		foreach ( $funnels as $key => $funnel ) {
			if ( ! empty( $funnel['origin_node'] ) && 'last-node' === $funnel['origin_node'] ) {
				return $key;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'sellkit_funnel' ) ) {
	/**
	 * Sellkit funnel wrapper.
	 *
	 * @since 1.1.0
	 * @return Object|Sellkit_Funnel|null
	 */
	function sellkit_funnel() {
		return Sellkit_Funnel::get_instance();
	}
}

