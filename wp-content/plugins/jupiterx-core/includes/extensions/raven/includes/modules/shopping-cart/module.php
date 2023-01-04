<?php
namespace JupiterX_Core\Raven\Modules\Shopping_Cart;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_base;

class Module extends Module_Base {

	public static function is_active() {
		return function_exists( 'WC' ) && defined( 'JUPITERX_VERSION' ) && defined( 'JUPITERX_API' );
	}

	public function get_widgets() {
		return [ 'shopping-cart' ];
	}

	public function __construct() {
		parent::__construct();

		// update cart count using ajax while adding items from cart using add to cart button or removing them from quick cart.
		add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'menu_cart_fragments' ] );
	}

	public function menu_cart_fragments( $fragments ) {
		$has_cart = is_a( WC()->cart, 'WC_Cart' );

		if ( ! $has_cart ) {
			return $fragments;
		}

		$product_count = WC()->cart->get_cart_contents_count();

		ob_start();
		?>
		<span class="raven-shopping-cart-count"><?php echo $product_count; ?></span>
		<?php
		$cart_count_html = ob_get_clean();

		if ( ! empty( $cart_count_html ) ) {
			$fragments['body:not(.elementor-editor-active) div.elementor-element.elementor-widget.elementor-widget-raven-shopping-cart .raven-shopping-cart-count'] = $cart_count_html;
		}

		return $fragments;
	}
}
