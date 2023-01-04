<?php
/**
 * WPML compatibility package class.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_WPML_Package' ) ) {

	class Jet_Woo_Builder_WPML_Package {

		public function __construct() {

			add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'add_translatable_nodes' ] );
			add_filter( 'jet-woo-builder/final-custom-archive-template', [ $this, 'set_translated_object' ] );
			add_filter( 'jet-woo-builder/woocommerce/products-loop/custom-archive-template', [ $this, 'set_translated_object' ] );
			add_filter( 'jet-woo-builder/current-template/template-id', [ $this, 'modify_template_id' ] );

			add_action( 'jet-woo-builder/rest/init-endpoints', function () {
				if ( defined( 'REST_REQUEST' ) && ! isset( WC()->cart ) ) {
					wc_load_cart();
				}
			} );

		}

		/**
		 * Translation nodes.
		 *
		 * Add JetWooBuilder translation nodes.
		 *
		 * @since  1.3.5
		 * @access public
		 *
		 * @param array $nodes_to_translate Translatable nodes.
		 *
		 * @return mixed
		 */
		public function add_translatable_nodes( $nodes_to_translate ) {

			$nodes_to_translate['jet-woo-products'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-products' ],
				'fields'     => [
					[
						'field'       => 'sale_badge_text',
						'type'        => __( 'Jet Woo Products Grid: Set sale badge text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-woo-categories'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-categories' ],
				'fields'     => [
					[
						'field'       => 'count_before_text',
						'type'        => __( 'Jet Woo Categories Grid: Count Before Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'count_after_text',
						'type'        => __( 'Jet Woo Categories Grid: Count After Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'desc_after_text',
						'type'        => __( 'Jet Woo Categories Grid: Trimmed After Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-woo-taxonomy-tiles'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-taxonomy-tiles' ],
				'fields'     => [
					[
						'field'       => 'count_before_text',
						'type'        => __( 'Jet Woo Taxonomy Tiles: Count Before Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'count_after_text',
						'type'        => __( 'Jet Woo Taxonomy Tiles: Count After Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-single-attributes'] = [
				'conditions' => [ 'widgetType' => 'jet-single-attributes' ],
				'fields'     => [
					[
						'field'       => 'block_title',
						'type'        => __( 'Jet Woo Single Attributes: Title Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-woo-builder-archive-sale-badge'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-archive-sale-badge' ],
				'fields'     => [
					[
						'field'       => 'block_title',
						'type'        => __( 'Jet Woo Archive Sale Badge: Sale Badge Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-woo-builder-archive-category-count'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-archive-category-count' ],
				'fields'     => [
					[
						'field'       => 'archive_category_count_before_text',
						'type'        => __( 'Jet Woo Archive Category Count: Count Before Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-woo-builder-archive-category-count'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-archive-category-count' ],
				'fields'     => [
					[
						'field'       => 'archive_category_count_after_text',
						'type'        => __( 'Jet Woo Archive Category Count: Count After Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-woo-builder-products-navigation'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-products-navigation' ],
				'fields'     => [
					[
						'field'       => 'prev_text',
						'type'        => __( 'Jet Woo Shop Products Navigation: The previous page link text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-woo-builder-products-navigation'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-products-navigation' ],
				'fields'     => [
					[
						'field'       => 'next_text',
						'type'        => __( 'Jet Woo Shop Products Navigation: The next page link text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-woo-builder-products-pagination'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-products-pagination' ],
				'fields'     => [
					[
						'field'       => 'prev_text',
						'type'        => __( 'Jet Woo Shop Products Pagination: The previous page link text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$nodes_to_translate['jet-woo-builder-products-pagination'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-products-pagination' ],
				'fields'     => [
					[
						'field'       => 'next_text',
						'type'        => __( 'Jet Woo Shop Products Pagination: The next page link text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			return $nodes_to_translate;

		}

		/**
		 * Modify template ID.
		 *
		 * Modify JetWooBuilder template ID.
		 *
		 * @since  1.4.2
		 * @access public
		 *
		 * @param $template_id
		 *
		 * @return mixed|void
		 */
		function modify_template_id( $template_id ) {
			return apply_filters( 'wpml_object_id', $template_id, jet_woo_builder_post_type()->slug(), true );
		}

		/**
		 * Set translated object.
		 *
		 * Set translated object ID to show.
		 *
		 * @since  2.0.5
		 * @access public
		 *
		 * @param int $obj_id Object id.
		 *
		 * @return int
		 */
		public function set_translated_object( $obj_id ) {

			global $sitepress;

			$new_id = $sitepress->get_object_id( $obj_id );

			if ( $new_id ) {
				return $new_id;
			}

			return $obj_id;

		}

	}

}

new Jet_Woo_Builder_WPML_Package();