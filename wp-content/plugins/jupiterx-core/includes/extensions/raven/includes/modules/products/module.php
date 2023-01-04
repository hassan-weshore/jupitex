<?php
namespace JupiterX_Core\Raven\Modules\Products;

use JupiterX_Core\Raven\Base\Module_base;
use JupiterX_Core\Raven\Utils;
use Elementor\Plugin as Elementor;

defined( 'ABSPATH' ) || die();

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Module extends Module_Base {

	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_raven_products_query', [ $this, 'ajax_query' ] );
		add_action( 'wp_ajax_nopriv_raven_products_query', [ $this, 'ajax_query' ] );

		add_filter( 'jx_products_apply_image_size', [ $this, 'apply_image_size' ], 10 );
		add_filter( 'jx_products_apply_swap_effects', [ $this, 'apply_swap_effects' ], 10 );
		add_filter( 'jx_products_apply_button_location', [ $this, 'apply_button_location' ], 10 );
		add_filter( 'jx_products_apply_button_icon', [ $this, 'apply_button_icon' ], 10 );
		add_filter( 'jx_products_apply_wishlist', [ $this, 'apply_wishlist' ], 10 );
	}

	public function get_widgets() {
		return [ 'products', 'products-carousel' ];
	}

	public static function is_active() {
		return function_exists( 'WC' );
	}

	public static function get_filters() {
		$filters        = [];
		$sorted_filters = [];

		$filter_files = glob( plugin_dir_path( __FILE__ ) . 'filters/*.php' );

		foreach ( $filter_files as $filter_file ) {
			$filter_name = basename( $filter_file, '.php' );

			if ( 'filter-base' === $filter_name ) {
				continue;
			}

			$filter_class = self::get_filter( $filter_name );

			$filters[ $filter_class::get_order() ] = [
				'name' => $filter_class::get_name(),
				'title' => $filter_class::get_title(),
			];
		}

		ksort( $filters );

		foreach ( $filters as $filter ) {
			$sorted_filters[ $filter['name'] ] = $filter['title'];
		}

		return $sorted_filters;
	}

	public static function get_filter( $filter_name ) {
		if ( empty( $filter_name ) ) {
			return false;
		}

		$filter_name = str_replace( '-', '_', $filter_name );

		return __NAMESPACE__ . '\Filters\\' . ucfirst( $filter_name );
	}

	public static function query( $widget, $settings ) {
		$filter          = self::get_filter( $settings['query_filter'] );
		$fallback_filter = self::get_filter( $settings['query_fallback_filter'] );

		remove_action( 'woocommerce_shop_loop_item_title', 'jupiterx_wc_template_loop_product_title' );

		if ( isset( $settings['layout'] ) && 'custom' === $settings['layout'] ) {
			remove_action( 'woocommerce_before_shop_loop_item', 'jupiterx_wc_loop_elements_enabled' );
		}

		add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
		add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );

		apply_filters( 'jx_products_apply_image_size', $settings );
		apply_filters( 'jx_products_apply_swap_effects', $settings );
		apply_filters( 'jx_products_apply_button_location', $settings );
		apply_filters( 'jx_products_apply_button_icon', $settings );
		apply_filters( 'jx_products_apply_wishlist', $settings );

		$query = $filter::query( $widget, $settings );

		if ( empty( $fallback_filter ) ) {
			return $query;
		}

		$products      = $query->get_content();
		$query_results = $products['query_results'];

		if ( 0 === (int) $query_results->total ) {
			$query = $fallback_filter::query( $widget, $settings );

			$query->fallback_filter = true;
		}

		return $query;
	}

	public static function ajax_query() {
		$post_id       = filter_input( INPUT_GET, 'post_id' );
		$model_id      = filter_input( INPUT_GET, 'model_id' );
		$paged         = filter_input( INPUT_GET, 'paged' );
		$archive_query = filter_input( INPUT_GET, 'raven_archive_query' );

		if ( empty( $post_id ) ) {
			wp_send_json_error( new \WP_Error( 'no_post_id', __( 'No post_id defined.', 'jupiterx-core' ) ) );
		}

		if ( empty( $model_id ) ) {
			wp_send_json_error( new \WP_Error( 'no_model_id', __( 'No model_id defined.', 'jupiterx-core' ) ) );
		}

		// Widget.
		$widget_data     = Elementor::$instance->documents->get( $post_id )->get_elements_data();
		$widget          = Utils::find_element_recursive( $widget_data, $model_id );
		$widget_instance = Elementor::$instance->elements_manager->create_element_instance( $widget );
		$widget_settings = $widget_instance->get_settings_for_display();

		$widget_settings['page']          = $paged;
		$widget_settings['archive_query'] = json_decode( $archive_query );

		self::get_pagination( $widget_settings );

		// Query.
		$query      = static::query( $widget_instance, $widget_settings );
		$products   = $query->get_content();
		$query_args = $query->get_query_args();

		wp_send_json_success( [
			'products' => self::format_query_products( $products ),
			'query_results' => $products['query_results'],
			'paged' => ! empty( $query_args['paged'] ) ? $query_args['paged'] : 1,
			'result_count' => self::get_query_result_count( $products ),
		] );
	}

	public static function get_pagination( $settings ) {
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
		remove_action( 'woocommerce_after_shop_loop', 'jupiterx_add_load_more', 30 );

		if ( 'yes' === $settings['show_all_products'] ) {
			return;
		}

		if (
			'yes' !== $settings['show_pagination'] ||
			'infinite_load' === $settings['pagination_type']
		) {
			add_action( 'woocommerce_after_shop_loop', function() {
				echo wp_kses_post( '<span class="raven-products-preloader"></span>' );
			}, 30 );

			return;
		}

		if ( 'load_more' === $settings['pagination_type'] ) {
			$text = $settings['load_more_text'];
			add_action( 'woocommerce_after_shop_loop', function() use ( $text ) {
				$load_more = sprintf(
					'<span class="raven-products-preloader"></span><div class="raven-load-more"><a class="raven-load-more-button" href="#"><span>%s</span></a></div>',
					$text
				);

				echo wp_kses_post( $load_more );
			}, 30 );
		}

		if ( 'page_based' === $settings['pagination_type'] ) {
			add_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
		}
	}

	private static function format_query_products( $products ) {
		$products = $products['data'];

		preg_match(
			'/<li.+li>/s',
			$products,
			$matches
		);

		return ! empty( $matches ) ? $matches[0] : false;
	}

	private static function get_query_result_count( $products ) {
		$args = [
			'total'    => ! empty( $products['query_results']->total ) ? $products['query_results']->total : '',
			'per_page' => ! empty( $products['query_results']->per_page ) ? $products['query_results']->per_page : '',
			'current'  => ! empty( $products['query_results']->current_page ) ? $products['query_results']->current_page : '',
		];

		ob_start();

		wc_get_template( 'loop/result-count.php', $args );

		return ob_get_clean();
	}

	/**
	 * Note: The control name for image size here should be 'image'.
	 *       Elementor will add '_size' and '_custom_dimension' prefix to the control name.
	 *
	 * @param $settings
	 *
	 * @return void
	 */
	public static function apply_image_size( $settings ) {
		add_filter( 'single_product_archive_thumbnail_size', function( $size ) use ( $settings ) {

			if ( 'custom' !== $settings['image_size'] ) {
				$image_size = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'woocommerce_thumbnail';
				$size       = $image_size;

				return $size;
			}

			$size = [
				0 => $settings['image_custom_dimension']['width'] || 100,
				1 => $settings['image_custom_dimension']['height'] || 100,
			];

			return $size;
		} );
	}

	/**
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public static function apply_swap_effects( $settings ) {
		// Return on 'None', 'Enlarge on Hover' since no markup modification is needed.
		if (
			empty( $settings['swap_effect'] ) ||
			'enlarge_hover' === $settings['swap_effect']
		) {
			return;
		}

		// Enqueue WC Zoom library for 'Zoom on Hover' effect and add full src of image.
		if ( 'zoom_hover' === $settings['swap_effect'] ) {
			wp_enqueue_script( 'zoom' );

			add_action( 'jupiterx_wc_loop_product_image_prepend_markup', function() {
				global $product;

				$product_image = wp_get_attachment_image_src( $product->get_image_id(), 'full' );

				if ( ! empty( $product_image ) ) {
					echo "<img style='display: none;' src='" . esc_attr( $product_image[0] ) . "'>";
				}
			} );

			return;
		}

		// Add a class to the parent of images with gallery images.
		add_filter( 'woocommerce_post_class', function( $classes ) {
			global $product;

			$gallery_ids = $product->get_gallery_image_ids();

			if ( ! empty( $gallery_ids ) ) {
				$classes[] = 'jupiterx-has-gallery-images';
			}

			return $classes;
		} );

		// Add gallery images to the markup.
		add_action( 'jupiterx_wc_loop_product_image_append_markup', function() use ( $settings ) {
			global $product;

			$output      = '';
			$size        = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
			$gallery_ids = $product->get_gallery_image_ids();

			if ( strpos( $settings['swap_effect'], 'gallery' ) !== false ) {
				wp_enqueue_script( 'flexslider' );
			}

			if ( empty( $gallery_ids ) ) {
				return;
			}

			if ( in_array( $settings['swap_effect'], [ 'fade_hover', 'flip_hover' ], true ) ) {
				$output = wp_get_attachment_image( array_shift( $gallery_ids ), $size );
			}

			if ( strpos( $settings['swap_effect'], 'gallery' ) !== false ) {
				$output = '<ul class="raven-swap-effect-gallery-slides">';

				$output .= '<li>' . wp_get_attachment_image( $product->get_image_id(), $size ) . '</li>';

				foreach ( $gallery_ids as $id ) {
					$output .= '<li>' . wp_get_attachment_image( $id, $size ) . '</li>';
				}

				$output .= '</ul>';
			}

			echo wp_kses_post( $output );
		} );
	}

	public static function apply_button_location( $settings ) {
		if ( isset( $settings['layout'] ) && 'custom' !== $settings['layout'] ) {
			return;
		}

		if ( isset( $settings['pc_atc_button_location'] ) && 'outside' === $settings['pc_atc_button_location'] ) {
			return;
		}

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
		add_action( 'jupiterx_wc_loop_product_image_append_markup', 'woocommerce_template_loop_add_to_cart' );
	}

	public static function apply_button_icon( $settings ) {
		if ( empty( $settings['pc_atc_button_icon'] ) ) {
			return;
		}

		add_filter( 'woocommerce_loop_add_to_cart_link', function( $link ) use ( $settings ) {
			ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['pc_atc_button_icon'], [ 'aria-hidden' => 'true' ] );
			$icon = ob_get_clean();

			$link = preg_replace( '/>(.+)</m', ">{$icon} $1<", $link );

			return $link;
		} );
	}

	public static function apply_wishlist( $settings ) {
		if ( empty( $settings['wishlist'] ) ) {
			return;
		}

		add_action( 'jupiterx_wc_loop_product_image_prepend_markup', function() use ( $settings ) {
			if ( ! class_exists( 'YITH_WCWL' ) ) {
				return;
			}

			global $product;

			$product_id   = $product->get_id();
			$state        = 'add';
			$classes      = 'jupiterx-wishlist';
			$nonce_add    = wp_create_nonce( 'add_to_wishlist' );
			$nonce_remove = wp_create_nonce( 'remove_from_wishlist' );

			if ( YITH_WCWL()->is_product_in_wishlist( $product_id ) ) {
				$classes .= ' jupiterx-wishlist-remove';
				$state    = 'remove';
			}

			$wishlist_button = "<button class='{$classes}' data-state='{$state}' data-product-id='{$product_id}' data-nonce-add='{$nonce_add}' data-nonce-remove='{$nonce_remove}'>";

			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['wishlist_icon'], [
				'aria-hidden' => 'true',
				'class'       => 'jupiterx-wishlist-add-icon',
			] );

			\Elementor\Icons_Manager::render_icon( $settings['wishlist_icon_remove'], [
				'aria-hidden' => 'true',
				'class'       => 'jupiterx-wishlist-remove-icon',
			] );

			$wishlist_button .= ob_get_clean();

			$wishlist_button .= '</button>';

			echo wp_kses_post( $wishlist_button );
		} );
	}

	public static function is_editor_or_preview() {
		$elementor  = \Elementor\Plugin::instance();
		$is_preview = (bool) filter_input( INPUT_GET, 'elementor_library', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		return $elementor->editor->is_edit_mode() || $is_preview;
	}
}
