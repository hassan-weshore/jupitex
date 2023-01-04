<?php

defined( 'ABSPATH' ) || die();

/**
 * Promote sellkit pro.
 *
 * @since 1.2.3
 */
class Promote_Sellkit_Pro {
	/**
	 * Class instance.
	 *
	 * @since 1.2.3
	 * @var Promote_Sellkit_Pro
	 */
	private static $instance = null;

	/**
	 * Get a class instance.
	 *
	 * @since 1.2.3
	 *
	 * @return Promote_Sellkit_Pro Class instance.
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
	 * @since 1.2.3
	 */
	public function __construct() {
		if ( class_exists( 'Sellkit_Pro' ) ) {
			return;
		}

		add_action( 'admin_menu', [ $this, 'admin_sub_menu' ], 15 );
		add_filter( 'sellkit_sub_menu', [ $this, 'update_sub_menu_list' ] );
		add_filter( 'sellkit_contact_segmentation_conditions_data', [ $this, 'update_conditions_data' ] );
	}

	/**
	 * Add upgrade to pro sub menu.
	 *
	 * @since 1.2.3
	 */
	public function admin_sub_menu() {
		add_submenu_page(
			'sellkit-dashboard',
			'',
			'<span class="sellkit-upgrade-to-pro"><i class="sellkit-icon-upgarde-to-pro"></i>' . esc_html__( 'Upgrade to Pro', 'sellkit' ) . '</span>',
			'manage_options',
			'upgrade_to_pro',
			function () {
				wp_redirect( 'https://getsellkit.com/pricing/' ); // phpcs:ignore
				exit;
			}
		);
	}

	/**
	 * Add sellkit pro sub menu.
	 *
	 * @since 1.2.3
	 * @param array $submenu array of submenu.
	 */
	public function update_sub_menu_list( $submenu ) {
		$pro_sub_menu = [
			'sellkit-discount' => esc_html__( 'Discount', 'sellkit' ),
			'sellkit-coupon' => esc_html__( 'Coupons', 'sellkit' ),
			'sellkit-alert' => esc_html__( 'Notices', 'sellkit' ),
		];

		sellkit()->sellkit_menus = array_merge( sellkit()->sellkit_menus, array_keys( $pro_sub_menu ) );

		return array_merge( $submenu, $pro_sub_menu );
	}

	/**
	 * Update conditions data base on sellkit pro.
	 *
	 * @since 1.2.3
	 * @param array $conditions array of conditions.
	 */
	public function update_conditions_data( $conditions ) {
		$pro = [];

		$pro_conditions = [
			'customer-value' => esc_html__( 'RFM Segments', 'sellkit' ),
			'first-order-date' => esc_html__( 'First Order Date', 'sellkit' ),
			'last-order-date' => esc_html__( 'Last Order Date', 'sellkit' ),
			'purchased-category' => esc_html__( 'Purchased Categories', 'sellkit' ),
			'purchased-product' => esc_html__( 'Purchased Products', 'sellkit' ),
			'time-deadline' => esc_html__( 'Time Deadline', 'sellkit' ),
			'total-spent' => esc_html__( 'Total Spent', 'sellkit' ),
			'viewed-category' => esc_html__( 'Viewed Categories', 'sellkit' ),
			'viewed-product' => esc_html__( 'Viewed Products', 'sellkit' ),
			'visitor-city' => __( 'Visitor City & Region', 'sellkit' ),
			'visitor-country' => esc_html__( 'Visitor Country', 'sellkit' ),
			'visitor-currency' => esc_html__( 'Visitor\'s Local Currency', 'sellkit' ),
			'visitor-timezone' => esc_html__( 'Visitor\'s Timezone', 'sellkit' ),
			'upsell' => esc_html__( 'Upsell', 'sellkit' ),
			'downsell' => esc_html__( 'Downsell', 'sellkit' ),
		];

		foreach ( $pro_conditions as $key => $value ) {
			$pro[ $key ] = [
				'title' => $value . ' ' . esc_html__( '(PRO)', 'sellkit' ),
				'type' => 'promote',
				'isSearchable' => '',
				'openMenuOnClick' => '',
			];
		}

		return array_merge( $conditions, $pro );
	}
}

Promote_Sellkit_Pro::get_instance();
