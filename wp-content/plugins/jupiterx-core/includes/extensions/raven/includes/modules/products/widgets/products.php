<?php
namespace JupiterX_Core\Raven\Modules\Products\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use JupiterX_Core\Raven\Modules\Products\Module;
use JupiterX_Core\Raven\Controls\Query as Control_Query;
use JupiterX_Core\Raven\Utils;

defined( 'ABSPATH' ) || die();

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class Products extends Base_Widget {

	public function get_name() {
		return 'raven-wc-products';
	}

	public function get_title() {
		return esc_html__( 'Products', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-products';
	}

	public function get_script_depends() {
		return [ 'imagesloaded', 'raven-pagination' ];
	}

	protected function register_controls() {
		$this->register_section_content();
		$this->register_section_layout();
		$this->register_section_warning();
		$this->register_section_widget_title();
		$this->register_section_box();
		$this->register_section_product_content();
		$this->register_section_pagination();
		$this->register_section_sale_badge();
		$this->register_section_wishlist();
	}

	private function register_section_content() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'query_filter',
			[
				'label' => esc_html__( 'Filter', 'jupiterx-core' ),
				'type' => 'select',
				'label_block' => true,
				'default' => 'all',
				'options' => Module::get_filters(),
			]
		);

		$this->add_control(
			'query_fallback_filter',
			[
				'label' => esc_html__( 'Fallback Filter', 'jupiterx-core' ),
				'type' => 'select',
				'label_block' => true,
				'default' => '',
				'options' => array_merge(
					[ '' => esc_html__( 'None', 'jupiterx-core' ) ],
					Module::get_filters()
				),
				'conditions' => [
					'terms' => [
						[
							'name' => 'query_filter',
							'operator' => '!in',
							'value' => [
								'all',
								'ids',
								'categories_tags',
								'current_archive_query',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'query_product_includes',
			[
				'label' => esc_html__( 'Search & Select Products', 'jupiterx-core' ),
				'type' => 'raven_query',
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'query_filter',
							'operator' => '==',
							'value' => 'ids',
						],
						[
							'name' => 'query_fallback_filter',
							'operator' => '==',
							'value' => 'ids',
						],
					],
				],
				'query' => [
					'source' => Control_Query::QUERY_SOURCE_POST,
					'post_type' => 'product',
				],
			]
		);

		$this->add_control(
			'query_filter_categories',
			[
				'label' => esc_html__( 'Search & Select Product Categories', 'jupiterx-core' ),
				'type' => 'raven_query',
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'query_filter',
							'operator' => '==',
							'value' => 'categories_tags',
						],
						[
							'name' => 'query_fallback_filter',
							'operator' => '==',
							'value' => 'categories_tags',
						],
					],
				],
				'query' => [
					'source'   => Control_Query::QUERY_SOURCE_TAX,
					'taxonomy' => 'product_cat',
				],
			]
		);

		$this->add_control(
			'query_filter_tags',
			[
				'label' => esc_html__( 'Search & Select Product Tags', 'jupiterx-core' ),
				'type' => 'raven_query',
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'query_filter',
							'operator' => '==',
							'value' => 'categories_tags',
						],
						[
							'name' => 'query_fallback_filter',
							'operator' => '==',
							'value' => 'categories_tags',
						],
					],
				],
				'query' => [
					'source' => Control_Query::QUERY_SOURCE_TAX,
					'taxonomy' => 'product_tag',
				],
			]
		);

		$this->add_control(
			'query_filter_by',
			[
				'label' => esc_html__( 'Filter By', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'jupiterx-core' ),
					'featured' => esc_html__( 'Featured Products', 'jupiterx-core' ),
					'sale' => esc_html__( 'Products on Sale', 'jupiterx-core' ),
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'query_filter',
							'operator' => '!==',
							'value' => 'ids',
						],
						[
							'name' => 'query_filter',
							'operator' => '!==',
							'value' => 'current_archive_query',
						],
					],
				],
			]
		);

		$this->add_control(
			'query_orderby',
			[
				'label' => esc_html__( 'Sort By', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'date',
				'options' => [
					'price' => esc_html__( 'Price', 'jupiterx-core' ),
					'popularity' => esc_html__( 'Popularity', 'jupiterx-core' ),
					'_wc_average_rating' => esc_html__( 'Average Rating', 'jupiterx-core' ),
					'date' => esc_html__( 'Date', 'jupiterx-core' ),
					'title' => esc_html__( 'Title', 'jupiterx-core' ),
					'menu_order' => esc_html__( 'Menu Order', 'jupiterx-core' ),
					'rand' => esc_html__( 'Random', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'query_order',
			[
				'label' => esc_html__( 'Sort', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'DESC',
				'options' => [
					'ASC' => esc_html__( 'Low to High', 'jupiterx-core' ),
					'DESC' => esc_html__( 'High to Low', 'jupiterx-core' ),
				],
				'condition' => [
					'query_orderby!' => 'rand',
				],
			]
		);

		$this->add_control(
			'query_offset',
			[
				'label' => esc_html__( 'Offset', 'jupiterx-core' ),
				'description' => esc_html__( 'Use this setting to skip over posts (e.g. \'4\' to skip over 4 posts).', 'jupiterx-core' ),
				'type' => 'number',
				'default' => 0,
				'min' => 0,
				'max' => 100,
				'frontend_available' => true,
				'condition' => [
					'query_filter!' => 'ids',
					'query_orderby!' => 'rand',
				],
			]
		);

		$this->add_control(
			'query_excludes',
			[
				'label' => esc_html__( 'Excludes', 'jupiterx-core' ),
				'type' => 'select2',
				'multiple' => true,
				'label_block' => true,
				'default' => [ 'current_post' ],
				'options' => [
					'current_post' => esc_html__( 'Current Product', 'jupiterx-core' ),
					'manual_selection' => esc_html__( 'Manual Selection', 'jupiterx-core' ),
				],
				'condition' => [
					'query_filter!' => 'ids',
				],
			]
		);

		$this->add_control(
			'query_excludes_ids',
			[
				'label' => esc_html__( 'Search & Select Products', 'jupiterx-core' ),
				'type' => 'raven_query',
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'query_excludes' => 'manual_selection',
				],
				'query' => [
					'source' => Control_Query::QUERY_SOURCE_POST,
					'post_type' => 'product',
				],
			]
		);

		$this->add_control(
			'widget_title',
			[
				'label' => esc_html__( 'Widget Title', 'jupiterx-core' ),
				'type' => 'text',
				'label_block' => true,
				'placeholder' => esc_html__( 'Enter your title', 'jupiterx-core' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'widget_fallback_title',
			[
				'label' => esc_html__( 'Widget Fallback Title', 'jupiterx-core' ),
				'type' => 'text',
				'label_block' => true,
				'placeholder' => esc_html__( 'Enter your fallback title', 'jupiterx-core' ),
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'query_filter',
							'operator' => '!in',
							'value' => [
								'all',
								'ids',
								'categories_tags',
							],
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_layout() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Choose Layout', 'jupiterx-core' ),
				'type' => 'select',
				'label_block' => true,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Theme Default from Customizer', 'jupiterx-core' ),
					'custom' => esc_html__( 'Custom', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'swap_effect',
			[
				'label' => esc_html__( 'Product Image Swap Effect', 'jupiterx-core' ),
				'description' => esc_html__( "Zoom and Gallery effects don't work in here (editor). Check them out on frontend.", 'jupiterx-core' ),
				'type' => 'select',
				'label_block' => true,
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'jupiterx-core' ),
					'fade_hover' => esc_html__( 'Fade on Hover', 'jupiterx-core' ),
					'zoom_hover' => esc_html__( 'Zoom on Hover', 'jupiterx-core' ),
					'enlarge_hover' => esc_html__( 'Enlarge on Hover', 'jupiterx-core' ),
					'flip_hover' => esc_html__( 'Flip on Hover', 'jupiterx-core' ),
					'gallery_arrows' => esc_html__( 'Gallery Slide with Arrows', 'jupiterx-core' ),
					'gallery_pagination' => esc_html__( 'Gallery Slide with Pagination', 'jupiterx-core' ),
				],
				'prefix_class' => 'raven-swap-effect-',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_all_products',
			[
				'label' => esc_html__( 'Show All Products', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'frontend_available' => true,
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'view_as',
			[
				'label' => esc_html__( 'View As', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'column',
				'options' => [
					'column' => esc_html__( 'Column', 'jupiterx-core' ),
				],
				'condition' => [
					'layout' => 'custom',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '3',
				'options' => [
					'1' => esc_html__( '1', 'jupiterx-core' ),
					'2' => esc_html__( '2', 'jupiterx-core' ),
					'3' => esc_html__( '3', 'jupiterx-core' ),
					'4' => esc_html__( '4', 'jupiterx-core' ),
					'5' => esc_html__( '5', 'jupiterx-core' ),
					'6' => esc_html__( '6', 'jupiterx-core' ),
				],
				'frontend_available' => true,
				'render_type' => 'template',
				'condition' => [
					'layout!' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'columns_custom',
			[
				'label' => esc_html__( 'Columns', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '3',
				'options' => [
					'1' => esc_html__( '1', 'jupiterx-core' ),
					'2' => esc_html__( '2', 'jupiterx-core' ),
					'3' => esc_html__( '3', 'jupiterx-core' ),
					'4' => esc_html__( '4', 'jupiterx-core' ),
					'5' => esc_html__( '5', 'jupiterx-core' ),
					'6' => esc_html__( '6', 'jupiterx-core' ),
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'layout',
							'value' => 'custom',
						],
						[
							'name' => 'view_as',
							'value' => 'column',
						],
					],
				],
				'frontend_available' => true,
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_control(
			'rows',
			[
				'label' => esc_html__( 'Rows', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '3',
				'options' => [
					'1' => esc_html__( '1', 'jupiterx-core' ),
					'2' => esc_html__( '2', 'jupiterx-core' ),
					'3' => esc_html__( '3', 'jupiterx-core' ),
					'4' => esc_html__( '4', 'jupiterx-core' ),
					'5' => esc_html__( '5', 'jupiterx-core' ),
					'6' => esc_html__( '6', 'jupiterx-core' ),
				],
				'frontend_available' => true,
				'render_type' => 'template',
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_all_products',
							'value' => '',
						],
						[
							'name' => 'view_as',
							'value' => 'column',
						],
					],
				],
			]
		);

		$this->add_control(
			'posts_per_view',
			[
				'label' => esc_html__( 'Posts per View', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '3',
				'options' => [
					'1' => esc_html__( '1', 'jupiterx-core' ),
					'2' => esc_html__( '2', 'jupiterx-core' ),
					'3' => esc_html__( '3', 'jupiterx-core' ),
					'4' => esc_html__( '4', 'jupiterx-core' ),
					'5' => esc_html__( '5', 'jupiterx-core' ),
					'6' => esc_html__( '6', 'jupiterx-core' ),
				],
				'condition' => [
					'view_as' => 'carousel',
				],
				'render_type' => 'template',
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_control(
			'slides_to_scroll',
			[
				'label' => esc_html__( 'Slides to Scroll', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '3',
				'options' => [
					'1' => esc_html__( '1', 'jupiterx-core' ),
					'2' => esc_html__( '2', 'jupiterx-core' ),
					'3' => esc_html__( '3', 'jupiterx-core' ),
					'4' => esc_html__( '4', 'jupiterx-core' ),
					'5' => esc_html__( '5', 'jupiterx-core' ),
					'6' => esc_html__( '6', 'jupiterx-core' ),
				],
				'condition' => [
					'view_as' => 'carousel',
				],
				'render_type' => 'template',
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_group_control(
			'image-size',
			[
				'name' => 'image',
				'default' => 'woocommerce_thumbnail',
				'frontend_available' => true,
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__( 'Pagination', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'frontend_available' => true,
				'condition' => [
					'show_all_products' => '',
				],
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => esc_html__( 'View Pagination As', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'page_based',
				'options' => [
					'page_based' => esc_html__( 'Page Based', 'jupiterx-core' ),
					'load_more' => esc_html__( 'Load More', 'jupiterx-core' ),
					'infinite_load' => esc_html__( 'Infinite Load', 'jupiterx-core' ),
				],
				'condition' => [
					'show_pagination' => 'yes',
					'show_all_products' => '',
				],
				'prefix_class' => 'raven-pagination-',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'allow_ordering',
			[
				'label' => esc_html__( 'Allow Ordering', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-allow-ordering-',
				'condition' => [
					'show_pagination' => 'yes',
					'show_all_products' => '',
				],
			]
		);

		$this->add_control(
			'show_result_count',
			[
				'label' => esc_html__( 'Show Result Count', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-result-count-',
				'condition' => [
					'show_pagination' => 'yes',
					'show_all_products' => '',
				],
			]
		);

		$this->add_control(
			'display_elements_heading',
			[
				'label' => esc_html__( 'Display Elements', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'categories',
			[
				'label' => esc_html__( 'Categories', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-categories-',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-title-',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'price',
			[
				'label' => esc_html__( 'Price', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-price-',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'rating',
			[
				'label' => esc_html__( 'Rating', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-rating-',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'atc_button',
			[
				'label' => esc_html__( 'Add to Cart Button', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-atc-button-',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'sale_badge',
			[
				'label' => esc_html__( 'Sale Badge', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-sale-badge-',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'oos_badge',
			[
				'label' => esc_html__( 'Out of Stock Badge', 'jupiterx-core' ),
				'type' => 'hidden',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-oos-badge-',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'wishlist',
			[
				'label' => esc_html__( 'Wishlist', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-wishlist-',
				'render_type' => 'template',
				'condition' => [
					'layout' => 'custom',
				],
				'frontend_available' => true,
			]
		);

		if (
			! class_exists( 'YITH_WCWL' )
		) {
			$this->add_control(
				'wishlist_warning',
				[
					'raw' => esc_html__( 'In order to use Wishlist feature, you need to install YITH WooCommerce Wishlist plugin.', 'jupiterx-core' ),
					'type' => 'raw_html',
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'conditions' => [
						'terms' => [
							[
								'name' => 'layout',
								'operator' => '==',
								'value' => 'custom',
							],
							[
								'name' => 'wishlist',
								'operator' => '==',
								'value' => 'show',
							],
						],
					],
				]
			);
		}

		$this->add_control(
			'quick_view',
			[
				'label' => esc_html__( 'Quick View', 'jupiterx-core' ),
				'type' => 'hidden',
				'default' => 'show',
				'return_value' => 'show',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'prefix_class' => 'raven-quick-view-',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_warning() {
		$this->start_controls_section(
			'section_style_warning',
			[
				'label' => esc_html__( 'Styles', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'layout!' => 'custom',
				],
			]
		);

		$this->add_control(
			'style_warning',
			[
				'raw' => esc_html__( "In order to style this widget, you need to choose 'Custom' from Layout options in the Content tab, otherwise you can only edit pagination and rest of styles can be edited from Customizer options.", 'jupiterx-core' ),
				'type' => 'raw_html',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			]
		);

		$this->end_controls_section();
	}

	private function register_section_widget_title() {
		$this->start_controls_section(
			'section_style_widget_title',
			[
				'label' => esc_html__( 'Widget Title', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'widget_title_alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => is_rtl() ? esc_html__( 'Right', 'jupiterx-core' ) : esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => is_rtl() ? 'eicon-text-align-right' : 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => is_rtl() ? esc_html__( 'Left', 'jupiterx-core' ) : esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => is_rtl() ? 'eicon-text-align-left' : 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'widget_title_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'widget_title_typography',
				'label' => esc_html__( 'Typography', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-wc-products-title',
			]
		);

		$this->add_responsive_control(
			'widget_title_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_box() {
		$this->start_controls_section(
			'section_style_box',
			[
				'label' => esc_html__( 'Box', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'box_columns_gap',
			[
				'label' => esc_html__( 'Columns Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_rows_gap',
			[
				'label' => esc_html__( 'Rows Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => is_rtl() ? esc_html__( 'Right', 'jupiterx-core' ) : esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => is_rtl() ? 'eicon-text-align-right' : 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => is_rtl() ? esc_html__( 'Left', 'jupiterx-core' ) : esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => is_rtl() ? 'eicon-text-align-left' : 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products li.product' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products li.product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'box_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
						'default' => [
							'unit'   => 'px',
							'top'    => '1',
							'left'   => '1',
							'right'  => '1',
							'bottom' => '1',
						],
					],
				],
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products li.product > .jupiterx-product-container',
			]
		);

		$this->add_responsive_control(
			'box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products li.product > .jupiterx-product-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_box' );

		$this->start_controls_tab(
			'tabs_box_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'box_box_shadow',
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products li.product',
			]
		);

		$this->add_control(
			'box_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products li.product' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products li.product' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_box_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'box_box_shadow_hover',
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products li.product:hover',
			]
		);

		$this->add_control(
			'box_background_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products li.product:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products li.product:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function register_section_product_content() {
		$this->start_controls_section(
			'section_style_product_content',
			[
				'label' => esc_html__( 'Product Content', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'layout' => 'custom',
				],
			]
		);

		$this->add_control(
			'pc_image_heading',
			[
				'label' => esc_html__( 'Image', 'jupiterx-core' ),
				'type' => 'heading',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'pc_image_border',
				'placeholder' => '1px',
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
						'default' => [
							'unit'   => 'px',
							'top'    => '1',
							'left'   => '1',
							'right'  => '1',
							'bottom' => '1',
						],
					],
				],
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wc-loop-product-image',
			]
		);

		$this->add_control(
			'pc_image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wc-loop-product-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pc_image_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 15,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wc-loop-product-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pc_rating_heading',
			[
				'label' => esc_html__( 'Rating', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'rating' => 'show',
				],
			]
		);

		$this->add_responsive_control(
			'pc_rating_size',
			[
				'label' => esc_html__( 'Icon size', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .star-rating' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rating' => 'show',
				],
			]
		);

		$this->start_controls_tabs( 'rating_tabs' );

		$this->start_controls_tab(
			'rating_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
				'condition' => [
					'rating' => 'show',
				],
			]
		);

		$this->add_control(
			'rating_normal_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .star-rating::before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'rating' => 'show',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'rating_active_tab',
			[
				'label' => esc_html__( 'Active', 'jupiterx-core' ),
				'condition' => [
					'rating' => 'show',
				],
			]
		);

		$this->add_control(
			'rating_active_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .star-rating span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'rating' => 'show',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pc_categories_heading',
			[
				'label' => esc_html__( 'Categories', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'categories' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_categories_view',
			[
				'label' => esc_html__( 'View as', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '',
				'options' => [
					'' => esc_html__( 'Stacked', 'jupiterx-core' ),
					'inline-block' => esc_html__( 'Inline', 'jupiterx-core' ),
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .posted_in' => 'display: {{VALUE}};',
				],
				'condition' => [
					'categories' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_categories_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#656565',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .posted_in' => 'color: {{VALUE}};',
				],
				'condition' => [
					'categories' => 'show',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'pc_categories_typography',
				'label' => esc_html__( 'Typography', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .posted_in',
				'condition' => [
					'categories' => 'show',
				],
			]
		);

		$this->add_responsive_control(
			'pc_categories_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .posted_in' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'categories' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_title_heading',
			[
				'label' => esc_html__( 'Title', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'title' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_title_view',
			[
				'label' => esc_html__( 'View as', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '',
				'options' => [
					'' => esc_html__( 'Stacked', 'jupiterx-core' ),
					'inline-block' => esc_html__( 'Inline', 'jupiterx-core' ),
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .woocommerce-loop-product__title' => 'display: {{VALUE}};',
				],
				'condition' => [
					'title' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_title_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .woocommerce-loop-product__title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'title' => 'show',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'pc_title_typography',
				'label' => esc_html__( 'Typography', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .woocommerce-loop-product__title',
				'condition' => [
					'title' => 'show',
				],
			]
		);

		$this->add_responsive_control(
			'pc_title_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .woocommerce-loop-product__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_price_heading',
			[
				'label' => esc_html__( 'Price', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'price' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_price_view',
			[
				'label' => esc_html__( 'View as', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '',
				'options' => [
					'' => esc_html__( 'Stacked', 'jupiterx-core' ),
					'inline-block' => esc_html__( 'Inline', 'jupiterx-core' ),
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .price' => 'display: {{VALUE}};',
				],
				'condition' => [
					'price' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_price_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .price' => 'color: {{VALUE}};',
				],
				'condition' => [
					'price' => 'show',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'pc_price_typography',
				'label' => esc_html__( 'Typography', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .price',
				'condition' => [
					'price' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_price_regular_heading',
			[
				'label' => esc_html__( 'Price Regular', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'price' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_price_regular_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .price del' => 'color: {{VALUE}};',
				],
				'condition' => [
					'price' => 'show',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'pc_price_regular_typography',
				'label' => esc_html__( 'Typography', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .price del',
				'condition' => [
					'price' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_heading',
			[
				'label' => esc_html__( 'Add to Cart Button', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_location',
			[
				'label' => esc_html__( 'Location', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'inside',
				'frontend_available' => true,
				'options' => [
					'inside' => esc_html__( 'Inside Image', 'jupiterx-core' ),
					'outside' => esc_html__( 'Outside Image', 'jupiterx-core' ),
				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_icon',
			[
				'label' => esc_html__( 'Icon', 'jupiterx-core' ),
				'type' => 'icons',
				'frontend_available' => true,
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_pc_atc_button' );

		$this->start_controls_tab(
			'tabs_pc_atc_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'default' => '#000',
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'pc_atc_button_typography',
				'label' => esc_html__( 'Typography', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .button',
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		if ( 'active' === get_option( 'elementor_experiment-e_font_icon_svg' ) ) {
			$this->add_responsive_control(
				'pc_atc_button_icon_size',
				[
					'label' => __( 'Size', 'jupiterx-core' ),
					'type' => 'slider',
					'default' => [
						'size' => 14,
					],
					'range' => [
						'px' => [
							'min' => 6,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .raven-wc-products-custom ul.products .button svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_pc_atc_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button:hover svg' => 'fill: {{VALUE}};',

				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_background_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'pc_atc_button_typography_hover',
				'label' => esc_html__( 'Typography', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .button:hover',
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			'border',
			[
				'name' => 'pc_atc_button_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'separator' => 'before',
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
						'default' => [
							'unit'   => 'px',
							'top'    => '1',
							'left'   => '1',
							'right'  => '1',
							'bottom' => '1',
						],
					],
				],
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .button',
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_text_padding',
			[
				'label' => esc_html__( 'Text Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->add_control(
			'pc_atc_button_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'top' => 10,
					'right' => 10,
					'bottom' => 10,
					'left' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'atc_button' => 'show',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function register_section_pagination() {
		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__( 'Pagination', 'jupiterx-core' ),
				'tab' => 'style',
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_pagination',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'show_all_products',
							'operator' => '==',
							'value' => '',
						],
					],
				],
			]
		);

		// Page based.
		$page_based_condition = [
			'terms' => [
				[
					'name' => 'layout',
					'operator' => '==',
					'value' => 'custom',
				],
				[
					'name' => 'pagination_type',
					'operator' => '==',
					'value' => 'page_based',
				],
			],
		];

		$this->add_responsive_control(
			'page_based_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'conditions' => $page_based_condition,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'page_based_border',
				'placeholder' => '1px',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
						'default' => [
							'unit'   => 'px',
							'top'    => '1',
							'left'   => '1',
							'right'  => '1',
							'bottom' => '1',
						],
					],
					'color' => [
						'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
					],
				],
				'conditions' => $page_based_condition,
				'selector' => '{{WRAPPER}} .woocommerce-pagination .page-numbers .page-numbers',
			]
		);

		$this->add_responsive_control(
			'page_based_space_between',
			[
				'label' => esc_html__( 'Space Between', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'unit' => 'px',
					'size' => -1,
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'conditions' => $page_based_condition,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination .page-numbers li' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'page_based_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'conditions' => $page_based_condition,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination .page-numbers .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'paged_based_typography',
				'scheme' => '3',
				'conditions' => $page_based_condition,
				'selector' => '{{WRAPPER}} .woocommerce-pagination .page-numbers',
			]
		);

		$this->start_controls_tabs( 'tabs_page_based' );

		$this->start_controls_tab(
			'tabs_page_based_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
				'conditions' => $page_based_condition,
			]
		);

		$this->add_control(
			'page_based_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000',
				'conditions' => $page_based_condition,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination .page-numbers' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'page_based_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'conditions' => $page_based_condition,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination .page-numbers .page-numbers' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_page_based_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
				'conditions' => $page_based_condition,
			]
		);

		$this->add_control(
			'page_based_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'conditions' => $page_based_condition,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination .page-numbers .page-numbers:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'page_based_background_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'conditions' => $page_based_condition,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination .page-numbers .page-numbers:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_page_based_active',
			[
				'label' => esc_html__( 'Active', 'jupiterx-core' ),
				'conditions' => $page_based_condition,
			]
		);

		$this->add_control(
			'page_based_color_active',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#fff',
				'conditions' => $page_based_condition,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination .page-numbers .page-numbers.current' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'page_based_background_color_active',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000',
				'conditions' => $page_based_condition,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-pagination .page-numbers .page-numbers.current' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Load more.
		$load_more_condition = [
			'pagination_type' => 'load_more',
		];

		$this->add_control(
			'load_more_text',
			[
				'label' => esc_html__( 'Button Label', 'jupiterx-core' ),
				'type' => 'text',
				'default' => esc_html__( 'Load More', 'jupiterx-core' ),
				'frontend_available' => true,
				'condition' => $load_more_condition,
			]
		);

		$this->add_responsive_control(
			'load_more_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'condition' => $load_more_condition,
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_height',
			[
				'label' => esc_html__( 'Height', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'condition' => $load_more_condition,
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'condition' => $load_more_condition,
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'condition' => $load_more_condition,
				'selectors' => [
					'{{WRAPPER}} .raven-load-more' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_load_more' );

		$this->start_controls_tab(
			'tabs_load_more_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
				'condition' => $load_more_condition,
			]
		);

		$this->add_control(
			'load_more_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => $load_more_condition,
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'load_more_typography',
				'scheme' => '3',
				'condition' => $load_more_condition,
				'selector' => '{{WRAPPER}} .raven-load-more-button',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'load_more_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'condition' => $load_more_condition,
				'selector' => '{{WRAPPER}} .raven-load-more-button',
			]
		);

		$this->add_control(
			'load_more_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => $load_more_condition,
			]
		);

		$this->add_control(
			'load_more_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'load_more_border_border!' => '',
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'load_more_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'condition' => $load_more_condition,
				'selector' => '{{WRAPPER}} .raven-load-more-button',
			]
		);

		$this->add_control(
			'load_more_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'condition' => $load_more_condition,
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'load_more_box_shadow',
				'condition' => $load_more_condition,
				'selector' => '{{WRAPPER}} .raven-load-more-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_load_more_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
				'condition' => $load_more_condition,
			]
		);

		$this->add_control(
			'hover_load_more_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => $load_more_condition,
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'hover_load_more_typography',
				'scheme' => '3',
				'condition' => $load_more_condition,
				'selector' => '{{WRAPPER}} .raven-load-more-button:hover',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'hover_load_more_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'condition' => $load_more_condition,
				'selector' => '{{WRAPPER}} .raven-load-more-button:hover',
			]
		);

		$this->add_control(
			'hover_load_more_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => $load_more_condition,
			]
		);

		$this->add_control(
			'hover_load_more_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'hover_load_more_border_border!' => '',
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'hover_load_more_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'condition' => $load_more_condition,
				'selector' => '{{WRAPPER}} .raven-load-more-button:hover',
			]
		);

		$this->add_control(
			'hover_load_more_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'condition' => $load_more_condition,
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'hover_load_more_box_shadow',
				'condition' => $load_more_condition,
				'selector' => '{{WRAPPER}} .raven-load-more-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function register_section_sale_badge() {
		$this->start_controls_section(
			'section_style_sale_badge',
			[
				'label' => esc_html__( 'Sale Badge', 'jupiterx-core' ),
				'tab' => 'style',
				'conditions' => [
					'terms' => [
						[
							'name' => 'layout',
							'operator' => '==',
							'value' => 'custom',
						],
						[
							'name' => 'sale_badge',
							'operator' => '==',
							'value' => 'show',
						],
					],
				],
			]
		);

		$this->add_control(
			'sale_badge_position',
			[
				'label'  => esc_html__( 'Position', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left' => 'left: 0; right: auto;',
					'right' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .onsale' => '{{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'sale_badge_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
						'default' => [
							'unit'   => 'px',
							'top'    => '1',
							'left'   => '1',
							'right'  => '1',
							'bottom' => '1',
						],
					],
				],
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .onsale',
			]
		);

		$this->add_control(
			'sale_badge_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'top'    => '0',
					'left'   => '0',
					'right'  => '0',
					'bottom' => '0',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sale_badge_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'top'    => '5',
					'left'   => '10',
					'right'  => '10',
					'bottom' => '5',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .onsale' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sale_badge_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'top'    => '10',
					'left'   => '10',
					'right'  => '10',
					'bottom' => '10',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .onsale' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sale_badge_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .onsale' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sale_badge_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .onsale' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sale_badge_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .onsale' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'sale_badge_typography',
				'label' => esc_html__( 'Typography', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-wc-products-custom ul.products .onsale',
			]
		);

		$this->end_controls_section();
	}

	private function register_section_wishlist() {
		$this->start_controls_section(
			'section_style_wishlist',
			[
				'label' => esc_html__( 'Wishlist', 'jupiterx-core' ),
				'tab' => 'style',
				'conditions' => [
					'terms' => [
						[
							'name' => 'layout',
							'operator' => '==',
							'value' => 'custom',
						],
						[
							'name' => 'wishlist',
							'operator' => '==',
							'value' => 'show',
						],
					],
				],
			]
		);

		$this->add_control(
			'wishlist_icon',
			[
				'label' => esc_html__( 'Icon', 'jupiterx-core' ),
				'type' => 'icons',
				'default' => [
					'value' => 'far fa-heart',
					'library' => 'fa-regular',
				],
			]
		);

		$this->add_control(
			'wishlist_icon_remove',
			[
				'label' => esc_html__( 'Icon Active', 'jupiterx-core' ),
				'type' => 'icons',
				'default' => [
					'value' => 'fas fa-heart',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'wishlist_position',
			[
				'label'  => esc_html__( 'Position', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => 'right',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left' => 'left: 0; right: auto;',
					'right' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist' => '{{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_wishlist' );

		$this->start_controls_tab(
			'tabs_wishlist_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'wishlist_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'wishlist_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'wishlist_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_wishlist_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'wishlist_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'wishlist_background_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'wishlist_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'wishlist_border_width',
			[
				'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
				'type' => 'dimensions',
				'separator' => 'before',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};;',
				],
			]
		);

		$this->add_control(
			'wishlist_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};;',
				],
			]
		);

		$this->add_control(
			'wishlist_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};;',
				],
			]
		);

		$this->add_control(
			'wishlist_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
					'top' => '10',
					'left' => '10',
					'right' => '10',
					'bottom' => '10',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-wc-products-custom ul.products .jupiterx-wishlist' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};;',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function register_section_quick_view() {
		$this->start_controls_section(
			'section_style_quick_view',
			[
				'label' => esc_html__( 'Quick View', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'quick_view' => 'show',
				],
			]
		);

		$this->add_control(
			'quick_view_icon',
			[
				'label' => esc_html__( 'Icon', 'jupiterx-core' ),
				'type' => 'icons',
				'default' => [
					'value' => 'fas fa-search-plus',
				],
			]
		);

		$this->add_control(
			'quick_view_position',
			[
				'label'  => esc_html__( 'Position', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => 'right',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
			]
		);

		$this->start_controls_tabs( 'tabs_quick_view' );

		$this->start_controls_tab(
			'tabs_quick_view_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'quick_view_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
			]
		);

		$this->add_control(
			'quick_view_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
			]
		);

		$this->add_control(
			'quick_view_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_quick_view_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'quick_view_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
			]
		);

		$this->add_control(
			'quick_view_background_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
			]
		);

		$this->add_control(
			'quick_view_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'quick_view_border_width',
			[
				'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
				'type' => 'dimensions',
				'separator' => 'before',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
			]
		);

		$this->add_control(
			'quick_view_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
			]
		);

		$this->add_control(
			'quick_view_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
			]
		);

		$this->add_control(
			'quick_view_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings                 = $this->get_settings_for_display();
		$is_current_archive_query = 'current_archive_query' === $settings['query_filter'];

		if ( $is_current_archive_query && ! Module::is_editor_or_preview() && ! is_tax() ) {
			return;
		}

		$query         = Module::query( $this, $settings );
		$products      = $query->get_content();
		$query_results = $products['query_results'];
		$layout        = empty( $settings['layout'] ) ? 'default' : $settings['layout'];

		$loop_data = [
			'layout' => ! empty( $settings['layout'] ) ? $settings['layout'] : 'default',
			'total_pages' => 'yes' === $settings['show_pagination'] ? (int) $query_results->total_pages : 1,
			'image_size' => ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'woocommerce_thumbnail',
		];

		?>
		<div
			class="raven-wc-products-wrapper raven-wc-products-<?php echo esc_attr( $layout ); ?>"
			data-settings="<?php echo esc_attr( wp_json_encode( $loop_data ) ); ?>"
			<?php echo $this->archive_query_parameters(); ?>
		>
			<?php
				echo $this->get_widget_title( $settings, $query, $query_results );
				Module::get_pagination( $settings );

				$content = $query->get_content();
				echo $content['data'];
			?>
		</div>
		<?php
	}

	private function get_widget_title( $settings, $query, $query_results ) {
		$text  = $settings['widget_title'];
		$total = ! empty( $query_results->total ) ? $query_results->total : 0;

		if ( ! empty( $query->fallback_filter ) ) {
			$text = $settings['widget_fallback_title'];
		}

		if ( 0 === (int) $total ) {
			return;
		}

		echo "<h2 class='raven-wc-products-title'>{$text}</h2>";
	}

	/**
	 * Return the taxonomy and term as an attribute for the widget.
	 *
	 * @return string
	 * @since 2.5.3
	 */
	private function archive_query_parameters() {
		global $wp_query;

		if ( ! is_archive() ) {
			return '';
		}

		$archive_query = $wp_query->get_queried_object();
		$json_query    = wp_json_encode( [
			'taxonomy' => ! empty( $archive_query->taxonomy ) ? $archive_query->taxonomy : '',
			'term'     => ! empty( $archive_query->slug ) ? $archive_query->slug : '',
		] );

		return sprintf( 'data-raven-archive-query="%s"', esc_attr( $json_query ) );
	}
}
