<?php


defined( 'ABSPATH' ) || die();

/**
 * Prodcuts Atribute Loop for frontend
 *
 * @package Sellkit\Artbees_WC_Attribute_Swatches\Frontend
 * @since 1.1.0
 */
class Artbees_WC_Attribute_Loop extends Artbees_WC_Attribute_Frontend {
	/**
	 * Artbees_WC_Attribute_Loop constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		if ( $this->check_is_variable_product() ) {
			return;
		}

		add_action( 'woocommerce_after_shop_loop_item', [ $this, 'shop_loop_attributes' ], 9 );
	}

	/**
	 * Add Attribute for Shop Product.
	 *
	 * @since 1.1.0
	 */
	public function shop_loop_attributes() {
		if ( is_singular( 'product' ) ) {
			return;
		}

		echo $this->shop_loop_attributes_data();
	}

	/**
	 * Get Attribute data for Shop Product.
	 *
	 * @since 1.1.0
	 */
	private function shop_loop_attributes_data() {
		global $product;

		$product_id         = $product->get_id();
		$product_attributes = $product->get_attributes();

		if ( empty( $product_attributes ) ) {
			return;
		}

		$content = '';

		foreach ( $product_attributes as $key => $attribute ) {
			$terms    = $this->get_attribute_terms( $product_id, $key );
			$settings = $this->get_attribute_general_settings( $attribute['id'], $attribute['name'] );
			$class    = $this->generate_attributes_class_for_settings( $settings );

			$content .= $this->shop_loop_attributes_structure( $terms, $class, $settings, $key, $product_id );
		}

		return $content;
	}

	/**
	 * Generate Attribute Structure for Shop Product.
	 *
	 * @since 1.1.0
	 * @param array  $terms       Product attribute term.
	 * @param string $class       attribute term CSS classes.
	 * @param array  $settings    attribute term settings data.
	 * @param string $slug        attribute slug.
	 * @param int    $product_id  Product id.
	 * @return string
	 */
	private function shop_loop_attributes_structure( $terms, $class, $settings, $slug, $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		$structure = sprintf( '<ul class="artbees-was-swatches %s">', $class );

		if ( ! $this->get_catalog_mode( $settings ) ) {
			return;
		}

		$catalog_type = $this->get_catalog_type( $settings );

		foreach ( $terms as $term ) {
			$catalog_data = $this->get_catalog_type_data( $catalog_type, $term, $product_id );

			$structure .= sprintf(
				'<li class="artbees-was-swatches-item"><a href="%1$s" class="artbees-was-swatch" data-term="%2$s" data-attribute="%3$s" %4$s>%5$s</a></li>',
				$catalog_data,
				$term->slug,
				'#' . $term->taxonomy,
				'link' === $catalog_type ? 'data-catalog=link' : 'data-catalog=image',
				$this->get_attribute_term_data( $term, $settings, $slug )
			);
		}

		$structure .= '</ul>';

		return $structure;
	}

	/**
	 * Get Catalog Mode.
	 *
	 * @since 1.1.0
	 * @param array $settings  attribute term settings data.
	 */
	private function get_catalog_mode( $settings ) {
		if ( empty( $settings ) ) {
			return false;
		}

		$type = ! empty( $settings['attribute_type'] ) ? $settings['attribute_type'] : '';

		if ( ! empty( $settings['swatch_type'] ) ) {
			$type = $settings['swatch_type'];
		}

		$valid_key = $type . '_catalog';

		if ( empty( $settings[ $valid_key ] ) || 'selected' === $settings[ $valid_key ] ) {
			return false;
		}

		return true;
	}

	/**
	 * Get Catalog type.
	 *
	 * @since 1.1.0
	 * @param array $settings  attribute term settings data.
	 * @return string
	 */
	private function get_catalog_type( $settings ) {
		$type = ! empty( $settings['attribute_type'] ) ? $settings['attribute_type'] : '';

		if ( ! empty( $settings['swatch_type'] ) ) {
			$type = $settings['swatch_type'];
		}

		$valid_key = $type . '_catalog_sub_field';

		if ( 'link' === $settings[ $valid_key ] ) {
			return $settings[ $valid_key ];
		}

		return $settings[ $valid_key ];
	}

	/**
	 * Get Catalog Data.
	 *
	 * @since 1.1.0
	 * @param string $type term catalog type name.
	 * @param object $term term data.
	 * @param int    $id   product id.
	 * @return string
	 */
	private function get_catalog_type_data( $type, $term, $id ) {
		if ( 'link' === $type ) {
			$attribute_url = '?attribute_' . $term->taxonomy . '= ' . $term->slug . '';

			return get_the_permalink( $id ) . $attribute_url;
		}

		$product = wc_get_product( $id );

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		$variations = $product->get_available_variations();
		$thumbnail  = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'thumbnail' );
		$image      = ! empty( $thumbnail[0] ) ? $thumbnail[0] : '';

		foreach ( $variations as $variation ) {
			if ( in_array( $term->slug, $variation['attributes'], true ) ) {
				if ( ! empty( $variation['image']['thumb_src'] ) ) {
					$image = $variation['image']['thumb_src'];
				}
			}
		}

		return $image;
	}
}
new Artbees_WC_Attribute_Loop();
