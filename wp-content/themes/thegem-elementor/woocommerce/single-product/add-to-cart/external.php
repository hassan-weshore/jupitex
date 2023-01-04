<?php
/**
 * External product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/external.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $thegem_product_data;

$isLegacy = $thegem_product_data['product_page_layout'] === 'legacy';
$addToCartText = $thegem_product_data['product_page_button_add_to_cart_text'];
$addToCartIcon = $thegem_product_data['product_page_button_add_to_cart_icon'];
$addToCartIconPack = $thegem_product_data['product_page_button_add_to_cart_icon_pack'];
$addToCartIconPosition = $thegem_product_data['product_page_button_add_to_cart_icon_position'];
$addToCartTextColor = thegem_get_option('product_page_button_add_to_cart_color') ? thegem_get_option('product_page_button_add_to_cart_color') : thegem_get_option('button_text_basic_color');
$addToCartTextColorHover = thegem_get_option('product_page_button_add_to_cart_color_hover') ? thegem_get_option('product_page_button_add_to_cart_color_hover') : thegem_get_option('button_text_hover_color');
$addToCartBackground = thegem_get_option('product_page_button_add_to_cart_background') ? thegem_get_option('product_page_button_add_to_cart_background') : thegem_get_option('styled_elements_color_1');
$addToCartBackgroundHover = thegem_get_option('product_page_button_add_to_cart_background_hover') ? thegem_get_option('product_page_button_add_to_cart_background_hover') : thegem_get_option('button_background_hover_color');
$addToCartBorder = thegem_get_option('product_page_button_add_to_cart_border_width') ? thegem_get_option('product_page_button_add_to_cart_border_width') : '0';
$addToCartBorderRadius = thegem_get_option('product_page_button_add_to_cart_border_radius') ? thegem_get_option('product_page_button_add_to_cart_border_radius') : '3';
$addToCartBorderColor = thegem_get_option('product_page_button_add_to_cart_border_color') ? thegem_get_option('product_page_button_add_to_cart_border_color') : $addToCartBackground;
$addToCartBorderColorHover = thegem_get_option('product_page_button_add_to_cart_border_color_hover') ? thegem_get_option('product_page_button_add_to_cart_border_color_hover') : $addToCartBackgroundHover;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart" action="<?php echo esc_url( $product_url ); ?>" method="get">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<?php
	    if (!$isLegacy) {
		    thegem_button( array(
			    'tag' => 'button',
			    'style' => 'outline',
			    'text' => $addToCartText ? $addToCartText : esc_html( $button_text ),
			    'icon' => $addToCartIcon,
			    'icon_pack' => $addToCartIconPack,
			    'icon_position' => $addToCartIconPosition,
			    'text_color' => $addToCartTextColor,
			    'hover_text_color' => $addToCartTextColorHover,
			    'background_color' => $addToCartBackground,
			    'hover_background_color' => $addToCartBackgroundHover,
			    'border_color' => $addToCartBorderColor,
			    'hover_border_color' => $addToCartBorderColorHover,
			    'border' => $addToCartBorder,
			    'corner' => $addToCartBorderRadius,
			    'attributes' => array(
				    'type'  => 'submit',
				    'class' => 'single_add_to_cart_button button alt'
			    ),
		    ), 1 );
        } else {
	        thegem_button(array(
		        'tag' => 'button',
		        'text' => esc_html( $button_text ),
		        'icon' => 'cart',
		        'background_color' => thegem_get_option('styled_elements_color_1'),
		        'hover_background_color' => thegem_get_option('button_background_hover_color'),
		        'attributes' => array(
			        'type' => 'submit',
			        'class' => 'single_add_to_cart_button button alt',
		        ),
	        ), 1);
        }
    ?>

	<?php do_action('thegem_woocommerce_after_add_to_cart_button'); ?>

	<?php wc_query_string_form_fields( $product_url ); ?>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
