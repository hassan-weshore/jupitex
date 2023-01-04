<?php

defined( 'ABSPATH' ) || die();

/**
 * Steps class.
 *
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Sellkit_Admin_Steps {

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Admin_Steps
	 */
	private static $instance = null;

	/**
	 * Class instance.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	const SELLKIT_STEP_POST_TYPE = 'sellkit_step';

	/**
	 * Get a class instance.
	 *
	 * @since 1.1.0
	 *
	 * @return Sellkit_Admin_Steps Class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->register_step_post_type();

		add_action( 'wp_ajax_sellkit_steps_get_products', [ $this, 'get_products' ] );
		add_action( 'wp_ajax_sellkit_steps_get_products_data', [ $this, 'get_products_data' ] );
		add_action( 'sellkit_funnels_after_create_new_step', [ $this, 'after_create_step_callback' ], 10 );
		add_action( 'sellkit_funnels_after_edit_step_name_slug', [ $this, 'edit_step_name_and_slug' ], 10 );
		add_action( 'sellkit_funnels_after_delete_step_post', [ $this, 'delete_related_page' ], 10 );
		add_action( 'sellkit_funnels_update_steps', [ $this, 'save_steps_data' ], 10, 2 );
		add_filter( 'template_include', [ $this, 'load_page_template' ], 10 );
		add_action( 'wp_ajax_sellkit_steps_get_coupons', [ $this, 'get_coupons' ] );
		add_action( 'wp_loaded', [ $this, 'sellkit_steps_flush_rules' ] );
		add_action( 'sellkit_after_toggle_status_sellkit-funnels', [ $this, 'update_steps_post_status' ], 10, 2 );
		add_action( 'sellkit_funnels_after_delete_posts_sellkit-funnels', [ $this, 'remove_steps_pages' ], 10, 1 );
	}

	/**
	 * Removes steps pages.
	 *
	 * @since 1.5.4
	 * @return void
	 * @param array $post Posts.
	 */
	public function remove_steps_pages( $post ) {
		$nodes = get_post_meta( $post['ID'], 'nodes' );

		if ( empty( $nodes[0] ) ) {
			return;
		}

		foreach ( $nodes[0] as $node ) {
			wp_delete_post( $node['page_id'] );
		}
	}

	/**
	 * Get default funnels.
	 *
	 * @since 1.1.0
	 */
	public static function get_default_funnel_steps() {
		return [
			'sales-page' => esc_html__( 'Sales Page', 'sellkit' ),
			'checkout' => esc_html__( 'Checkout', 'sellkit' ),
			'thankyou' => esc_html__( 'Thank You', 'sellkit' ),
			'decision' => esc_html__( 'Decision', 'sellkit' ),
			'upsell' => esc_html__( 'Upsell', 'sellkit' ),
			'downsell' => esc_html__( 'Downsell', 'sellkit' ),
			'optin-confirmation' => esc_html__( 'Opt-in Confirmation' ),
			'optin' => esc_html__( 'Opt-in', 'sellkit' ),
		];
	}

	/**
	 * Save_steps_data.
	 *
	 * @since 1.1.0
	 * @param array $steps Steps.
	 * @param int   $funnel_id Funnel Id.
	 */
	public static function save_steps_data( $steps, $funnel_id ) {
		foreach ( $steps as $step_number => $step ) {
			$step['funnel_id'] = $funnel_id;
			$step['number']    = $step_number;

			update_post_meta( $step['page_id'], 'step_data', $step );
		}
	}

	/**
	 * Get all coupons.
	 *
	 * @since 1.1.0
	 */
	public static function get_coupons() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		$filtered_products = [];
		$args              = [
			'post_type' => 'shop_coupon',
			'post_status' => 'any',
			's' => $input_value,
		];

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$filtered_products[] = [
					'label' => htmlspecialchars_decode( get_the_title() ),
					'value' => get_the_ID(),
				];
			}
		}

		wp_send_json_success( $filtered_products );
	}

	/**
	 * Get products.
	 *
	 * @since 1.1.0
	 */
	public static function get_products() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		if ( ! sellkit()->has_valid_dependencies() ) {
			wp_send_json_error( __( 'Please install and activate WooCommerce.', 'sellkit' ) );
		}

		$filtered_products = [];

		$args = [
			'status'    => 'any',
			'orderby' => 'name',
			'order'   => 'ASC',
			's' => $input_value,
			'posts_per_page' => -1,
		];

		$all_products = wc_get_products( $args );

		foreach ( $all_products as $product ) {
			if ( $product->get_type() !== 'variable' && $product->get_type() !== 'external' ) {
				$filtered_products[] = [
					'label' => $product->get_title(),
					'value' => $product->get_id(),
				];
			}

			if ( $product->get_type() === 'variable' ) {
				foreach ( $product->get_available_variations() as $available_variation ) {
					$attributes = $available_variation['attributes'];
					$attributes = implode( ', ', array_values( $attributes ) );

					$filtered_products[] = [
						'label' => "{$product->get_title()} - {$attributes} (#{$available_variation['variation_id']})",
						'value' => $available_variation['variation_id'],
					];
				}
			}
		}

		wp_send_json_success( $filtered_products );
	}

	/**
	 * Get products.
	 *
	 * @since 1.1.0
	 */
	public static function get_products_data() {
		check_ajax_referer( 'sellkit', 'nonce' );

		$filtered_products = [];
		$products_id       = filter_input( INPUT_GET, 'products_id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! sellkit()->has_valid_dependencies() ) {
			wp_send_json_error( __( 'Please install and activate WooCommerce.', 'sellkit' ) );
		}

		if ( empty( $products_id ) ) {
			wp_send_json_error( __( 'Please send some product ids' ) );
		}

		$products_id = is_array( $products_id ) ? $products_id : [ $products_id ];

		foreach ( $products_id as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( empty( $product ) ) {
				wp_send_json_error( __( 'Product does not exist', 'sellkit' ) );
				continue;
			}

			$attributes = $product->get_attributes();
			$label      = $product->get_title();

			if ( $product->get_type() === 'variation' ) {
				$attributes = implode( ', ', array_values( $attributes ) );
				$label      = "{$product->get_title()} - $attributes (#{$product->get_id()})";
			}

			$filtered_products[] = [
				'label' => $label,
				'id' => $product->get_id(),
				'title' => $label,
				'thumbnail' => $product->get_image(),
				'regular_price' => $product->get_regular_price( 'edit' ),
				'sale_price' => ( $product->is_on_sale() ) ? $product->get_sale_price() : false,
			];
		}

		wp_send_json_success( $filtered_products );
	}

	/**
	 * Filter step data before saving.
	 *
	 * @since 1.1.0
	 * @param string $post_id Post id.
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function after_create_step_callback( $post_id ) {
		$sub_action_data = sellkit_post( 'sub_action_data' );
		$origin_node     = isset( $sub_action_data['origin_node'] ) ? $sub_action_data['origin_node'] : '';
		$target_node     = ! empty( $sub_action_data['target_node'] ) ? $sub_action_data['target_node'] : null;
		$target_index    = isset( $sub_action_data['target_index'] ) ? $sub_action_data['target_index'] : 0;
		$nodes           = get_post_meta( $post_id, 'nodes', true );
		$last_node       = end( $nodes );

		array_pop( $nodes );

		$new_node_id = wp_insert_post( [
			'post_type' => self::SELLKIT_STEP_POST_TYPE,
			'post_title' => $last_node['title'],
			'post_name' => sanitize_title( $last_node['title'] ),
			'post_status' => 'publish',
		] );

		$current_target       = [ 'nodeId' => $target_node ];
		$last_node['page_id'] = $new_node_id;
		$last_node['slug']    = get_post_field( 'post_name', $new_node_id );
		$last_node['targets'] = ! is_null( $target_node ) ? [ $current_target ] : [];

		if (
			'decision' === $last_node['type']['key'] && ! is_null( $target_node ) ||
			'upsell' === $last_node['type']['key'] && ! is_null( $target_node ) ||
			'downsell' === $last_node['type']['key'] && ! is_null( $target_node )
		) {
			$last_node['targets'] = [
				$current_target,
				$current_target
			];
		}

		// Adding current target info the the step.
		$last_node['current_target_index'] = $target_index;

		if ( 'last-node' === $origin_node ) {
			$first_end_path_node_key  = self::get_first_end_path_node_key( $nodes );
			$last_node['origin_node'] = 'last-node';

			if ( $first_end_path_node_key ) {
				unset( $nodes[ $first_end_path_node_key ]['origin_node'] );
			}
		}

		$node_data              = $last_node;
		$node_data['funnel_id'] = $post_id;
		$node_data['number']    = count( $nodes );

		update_post_meta( $new_node_id, 'step_data', $node_data );// it should be updated.

		$old_node_keys                      = array_keys( $nodes );
		$nodes[ end( $old_node_keys ) + 1 ] = $last_node;
		$keys                               = array_keys( $nodes );

		if (
			'last-node' !== $origin_node &&
			'decision' !== $nodes[ $origin_node ]['type']['key'] &&
			'upsell' !== $nodes[ $origin_node ]['type']['key'] &&
			'downsell' !== $nodes[ $origin_node ]['type']['key']
		) {
			$nodes[ $origin_node ]['targets'] = [ [ 'nodeId' => strval( end( $keys ) ) ] ];
		}

		if (
			'last-node' !== $origin_node &&
			(
				'decision' === $nodes[ $origin_node ]['type']['key'] ||
				'upsell' === $nodes[ $origin_node ]['type']['key'] ||
				'downsell' === $nodes[ $origin_node ]['type']['key']
			)
		) {
			if ( is_array( $nodes[ $origin_node ]['targets'] ) && empty( $nodes[ $origin_node ]['targets'][0] ) ) {
				$nodes[ $origin_node ]['targets'][0] = ! empty( $target_node ) ? [ 'nodeId' => $target_node ] : null;
			}

			if ( is_array( $nodes[ $origin_node ]['targets'] ) && empty( $nodes[ $origin_node ]['targets'][1] ) ) {
				$nodes[ $origin_node ]['targets'][1] = ! empty( $target_node ) ? [ 'nodeId' => $target_node ] : null;
			}

			$nodes[ $origin_node ]['targets'][ (int) $target_index ] = [ 'nodeId' => strval( end( $old_node_keys ) + 1 ) ];
		}

		if ( '' === $origin_node && null === $target_node ) {
			update_post_meta( $post_id, 'nodes', [ 1 => $last_node ] );
			return;
		}

		update_post_meta( $post_id, 'nodes', $nodes );
	}

	/**
	 * Gets last node origin node
	 *
	 * @param array $nodes Nodes.
	 * @since 1.5.0
	 */
	public static function get_first_end_path_node_key( $nodes ) {
		foreach ( $nodes as $key => $node ) {
			if ( ! empty( $node['origin_node'] ) && 'last-node' === $node['origin_node'] ) {
				return $key;
			}
		}

		return false;
	}

	/**
	 * Loading page templates.
	 *
	 * @since 1.1.0
	 * @param string $template Template name.
	 */
	public function load_page_template( $template ) {

		global $post;

		if ( 'string' !== gettype( $template ) || ! is_object( $post ) || self::SELLKIT_STEP_POST_TYPE !== $post->post_type ) {
			return $template;
		}

		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
		add_filter( 'next_post_rel_link', '__return_empty_string' );
		add_filter( 'previous_post_rel_link', '__return_empty_string' );

		$file = sellkit()->plugin_dir() . 'includes/templates/canvas.php';

		if ( ! file_exists( $template ) ) {
			return $template;
		}

		return $file;
	}

	/**
	 * Register step post type.
	 *
	 * @since 1.1.0
	 */
	public function register_step_post_type() {
		$labels = array(
			'name'          => esc_html_x( 'Steps', 'flow step general name', 'sellkit' ),
			'singular_name' => esc_html_x( 'Step', 'flow step singular name', 'sellkit' ),
			'search_items'  => esc_html__( 'Search Steps', 'sellkit' ),
			'all_items'     => esc_html__( 'All Steps', 'sellkit' ),
			'edit_item'     => esc_html__( 'Edit Step', 'sellkit' ),
			'view_item'     => esc_html__( 'View Step', 'sellkit' ),
			'add_new'       => esc_html__( 'Add New', 'sellkit' ),
			'update_item'   => esc_html__( 'Update Step', 'sellkit' ),
			'add_new_item'  => esc_html__( 'Add New', 'sellkit' ),
			'new_item_name' => esc_html__( 'New Step Name', 'sellkit' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'query_var'           => true,
			'can_export'          => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'publicly_queryable'  => true,
			'supports'            => [ 'title', 'editor', 'elementor', 'revisions' ],
			'capability_type'     => 'post',
			'capabilities'        => [
				'create_posts' => 'do_not_allow', // Prior to Wordpress 4.5, this was false.
			],
			'map_meta_cap'        => true,
		);

		register_post_type( self::SELLKIT_STEP_POST_TYPE, $args );
	}

	/**
	 * Edit step name or slug.
	 *
	 * @since 1.1.0
	 * @param string $funnel_id Funnel Id.
	 */
	public function edit_step_name_and_slug( $funnel_id ) {
		$sub_action_data = sellkit_post( 'sub_action_data' );
		$title           = ! empty( $sub_action_data['title'] ) ? $sub_action_data['title'] : '';
		$slug            = ! empty( $sub_action_data['slug'] ) ? $sub_action_data['slug'] : sanitize_title( $title );
		$page_id         = ! empty( $sub_action_data['page_id'] ) ? $sub_action_data['page_id'] : '';
		$selected_step   = $sub_action_data['selected_step'];

		if ( empty( $page_id ) ) {
			return;
		}

		wp_update_post( [
			'ID'         => $page_id,
			'post_title' => $title,
			'post_name'  => $slug,
		] );

		if ( get_post_field( 'post_name', $page_id ) === $slug ) {
			return;
		}

		$post_data                           = get_post_meta( $funnel_id, 'nodes', true );
		$post_data[ $selected_step ]['slug'] = get_post_field( 'post_name', $page_id );

		update_post_meta( $funnel_id, 'nodes', $post_data );
	}

	/**
	 * Delete step page id.
	 *
	 * @since 1.1.0
	 */
	public function delete_related_page() {
		$sub_action_data = sellkit_post( 'sub_action_data' );

		if ( empty( $sub_action_data['page_id'] ) ) {
			return;
		}

		wp_delete_post( $sub_action_data['page_id'], true );
	}

	/**
	 * Flash rules.
	 *
	 * @since 1.1.0
	 */
	public function sellkit_steps_flush_rules() {
		$rules = get_option( 'rewrite_rules' );

		if ( ! isset( $rules['sellkit_step/[^/]+/([^/]+)/?$'] ) ) {
			flush_rewrite_rules();
		}
	}

	/**
	 * Make publish steps page.
	 *
	 * @since 1.1.0
	 * @param string $funnel_id Funnel Id.
	 * @param string $new_status Status.
	 */
	public function update_steps_post_status( $funnel_id, $new_status ) {
		$steps = get_post_meta( $funnel_id, 'sellkit_steps', true );

		foreach ( $steps as $step ) {
			if ( $step['page_id'] === $funnel_id ) {
				continue;
			}

			wp_update_post(array(
				'ID' => $step['page_id'],
				'post_status' => $new_status,
			) );
		}
	}
}

Sellkit_Admin_Steps::get_instance();
