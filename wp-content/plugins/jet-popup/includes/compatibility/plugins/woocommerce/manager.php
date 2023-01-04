<?php
namespace Jet_Popup\Compatibility;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Woocommerce {

	/**
	 * Include files
	 */
	public function load_files() {}

	/**
	 * @param $conditions_list
	 *
	 * @return mixed
	 */
	public function modify_popup_conditions_group_list( $conditions_group_list ) {

		$woo_groups_list = [
			'woocommerce' => [
				'label'      => __( 'WooCommerce', 'jet-popup' ),
				'sub-groups' => [],
			],
		];

		return wp_parse_args( $woo_groups_list, $conditions_group_list );
	}

	/**
	 * @param $conditions_sub_group_list
	 *
	 * @return array|object
	 */
	public function modify_popup_conditions_sub_group_list( $conditions_sub_group_list ) {

		$woo_groups_list = [
			'woocommerce-archive' => [
				'label'   => __( 'Products Archive', 'jet-popup' ),
				'options' => [],
			],
			'woocommerce-single' => [
				'label'   => __( 'Single Product', 'jet-popup' ),
				'options' => [],
			],
			'woocommerce-page' => [
				'label'   => __( 'Pages', 'jet-popup' ),
				'options' => [],
			],
		];

		return wp_parse_args( $woo_groups_list, $conditions_sub_group_list );
	}

	/**
	 * @param $conditions_list
	 *
	 * @return mixed
	 */
	public function modify_popup_conditions_list( $conditions_list ) {

		$base_path = jet_popup()->plugin_path( 'includes/compatibility/plugins/woocommerce/conditions/' );

		$woo_conditions_list = [
			'\Jet_Popup\Conditions\Woo_All_Product_Archives'            => $base_path . 'all-products-archive.php',
			'\Jet_Popup\Conditions\Woo_Product_Categories'              => $base_path . 'product-categories.php',
			'\Jet_Popup\Conditions\Woo_Product_Tags'                    => $base_path . 'product-tags.php',
			'\Jet_Popup\Conditions\Woo_Singular_Product'                => $base_path . 'singular-product.php',
			'\Jet_Popup\Conditions\Woo_Singular_Product_Categories'     => $base_path . 'singular-product-categories.php',
			'\Jet_Popup\Conditions\Woo_Singular_Product_Category_Child' => $base_path . 'singular-product-category-child.php',
			'\Jet_Popup\Conditions\Woo_Singular_Product_Tags'           => $base_path . 'singular-product-tags.php',
			'\Jet_Popup\Conditions\Woo_Shop_Page'                       => $base_path . 'shop-page.php',
			'\Jet_Popup\Conditions\Woo_Search_Results'                  => $base_path . 'search-results.php',
			'\Jet_Popup\Conditions\Woo_Product_Card'                    => $base_path . 'product-card.php',
			'\Jet_Popup\Conditions\Woo_Product_Empty_Card'              => $base_path . 'product-empty-card.php',
			'\Jet_Popup\Conditions\Woo_Product_Checkout'                => $base_path . 'product-checkout.php',
			'\Jet_Popup\Conditions\Woo_Thanks_Page'                     => $base_path . 'thanks-page.php',
			'\Jet_Popup\Conditions\Woo_Account_Page'                    => $base_path . 'account-page.php',
			'\Jet_Popup\Conditions\Woo_Account_Login_Page'              => $base_path . 'account-login-page.php',
		];

		return wp_parse_args( $woo_conditions_list, $conditions_list );
	}

	/**
	 * @param $endpoints_list
	 *
	 * @return mixed
	 */
	public function modify_popup_endpoint_list( $endpoints_list ) {

		$base_path = jet_popup()->plugin_path( 'includes/compatibility/plugins/woocommerce/rest-api/' );

		$endpoints_list[ '\Jet_Popup\Endpoints\Get_Product_Categories' ] = $base_path . 'get-product-categories.php';
		$endpoints_list[ '\Jet_Popup\Endpoints\Get_Product_Tags' ]       = $base_path . 'get-product-tags.php';
		$endpoints_list[ '\Jet_Popup\Endpoints\Get_Products' ]           = $base_path . 'get-products.php';

		return $endpoints_list;
	}

	/**
	 * [__construct description]
	 */
	public function __construct() {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		$this->load_files();

		add_filter( 'jet-popup/conditions/conditions-group-list', [ $this, 'modify_popup_conditions_group_list' ], 10, 2 );
		add_filter( 'jet-popup/conditions/condition-sub-groups', [ $this, 'modify_popup_conditions_sub_group_list' ], 10, 2 );
		add_filter( 'jet-popup/conditions/conditions-list', [ $this, 'modify_popup_conditions_list' ], 10, 2 );
		add_filter( 'jet-popup/rest-api/endpoint-list', [ $this, 'modify_popup_endpoint_list' ], 10, 2 );

	}

}
