<?php
/**
 * Add to wishlist button template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $base_url string Current page url
 * @var $wishlist_url			  string Url to wishlist page
 * @var $exists					bool Whether current product is already in wishlist
 * @var $show_exists			   bool Whether to show already in wishlist link on multi wishlist
 * @var $show_count				bool Whether to show count of times item was added to wishlist
 * @var $product_id				int Current product id
 * @var $parent_product_id		 int Parent for current product
 * @var $product_type			  string Current product type
 * @var $label					 string Button label
 * @var $browse_wishlist_text	  string Browse wishlist text
 * @var $already_in_wishslist_text string Already in wishlist text
 * @var $product_added_text		string Product added text
 * @var $icon					  string Icon for Add to Wishlist button
 * @var $link_classes			  string Classed for Add to Wishlist button
 * @var $available_multi_wishlist  bool Whether add to wishlist is available or not
 * @var $disable_wishlist		  bool Whether wishlist is disabled or not
 * @var $template_part			 string Template part
 * @var $container_classes		 string Container classes
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

global $product;

$thegem_product_data = thegem_get_output_product_page_data($product_id);

$isLegacy = $thegem_product_data['product_page_layout'] == 'legacy';
$wishlistIcon = $thegem_product_data['product_page_button_add_to_wishlist_icon'];
$wishlistIconPack = $thegem_product_data['product_page_button_add_to_wishlist_icon_pack'];
$wishlistIconColor = thegem_get_option('product_page_button_add_to_wishlist_color') ? thegem_get_option('product_page_button_add_to_wishlist_color') : thegem_get_option('system_icons_font_2');
$wishlistIconColorHover = thegem_get_option('product_page_button_add_to_wishlist_color_hover') ? thegem_get_option('product_page_button_add_to_wishlist_color_hover') : thegem_get_option('hover_link_color');

?>

<?php if(isset($thegem_product_page) && $thegem_product_page) : ?>
	<?php
		if (!$isLegacy) {
			thegem_button(array(
				'href' => esc_url( add_query_arg( 'add_to_wishlist', $product_id, $base_url ) ),
				'text_color' => $wishlistIconColor,
				'hover_text_color' => $wishlistIconColorHover,
				'background_color' => 'transparent',
				'hover_background_color' => 'transparent',
				'icon' => $wishlistIcon,
				'icon_pack' => $wishlistIconPack,
				'attributes' => array(
					'rel' => 'nofollow',
					'data-product-id' => esc_attr( $product_id ),
					'data-product-type' => $product_type,
					'class' => 'add_to_wishlist',
				),
				'extra_class' => 'yith-wcwl-add-button',
			),1);
		} else {
			thegem_button(array(
				'style' => 'outline',
				'text' => wp_kses_post( $label ),
				'href' => esc_url( add_query_arg( 'add_to_wishlist', $product_id, $base_url ) ),
				'text_color' => thegem_get_option('button_background_basic_color'),
				'border_color' => thegem_get_option('button_background_basic_color'),
				'hover_text_color' => thegem_get_option('button_outline_text_hover_color'),
				'hover_background_color' => thegem_get_option('button_background_basic_color'),
				'hover_border_color' => thegem_get_option('button_background_basic_color'),
				'icon' => 'add-to-wishlist',
				'attributes' => array(
					'rel' => 'nofollow',
					'data-product-id' => esc_attr( $product_id ),
					'data-product-type' => $product_type,
					'class' => 'add_to_wishlist',
				),
				'extra_class' => 'yith-wcwl-add-button',
			),1);
		}
	?>
<?php else : ?>
	<div class="yith-wcwl-add-button">
		<a href="<?php echo esc_url( add_query_arg( 'add_to_wishlist', $product_id, $base_url ) ); ?>" rel="nofollow" data-product-id="<?php echo esc_attr( $product_id ) ?>" data-product-type="<?php echo $product_type?>" data-original-product-id="<?php echo $parent_product_id ?>" class="<?php echo esc_attr( $link_classes ); ?>" data-title="<?php echo esc_attr( apply_filters( 'yith_wcwl_add_to_wishlist_title', $label ) ); ?>">
			<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php echo wp_kses_post( $label ); ?></span>
		</a>
	</div>
<?php endif; ?>
