<?php
namespace Sellkit\Elementor\Modules\Order_Cart_Details\Classes;

/**
 * Class Order_Cart_Detail_Data
 *
 * @since 1.1.0
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 *
 * phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
 */
class Order_Cart_Detail_Data {

	/**
	 * Order cart detail instance.
	 *
	 * @since 1.1.0
	 * @var Order_Cart_Detail_Data
	 */
	public static $instance = null;

	/**
	 * Check if it's a dummy data or not.
	 *
	 * @since 1.1.0
	 * @var bool
	 */
	private $is_dummy_data = false;

	/**
	 * Order object.
	 *
	 * @since 1.1.0
	 * @var bool|\WC_Order|\WC_Order_Refund
	 */
	public $order;

	/**
	 * Funnel object.
	 *
	 * @since 1.1.0
	 * @var Sellkit_Funnel
	 */
	public $funnel;

	/**
	 * Order_Cart_Detail_Data constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		$key          = isset( $_GET['order-key'] ) ? sanitize_text_field( $_GET['order-key'] ) : false;
		$this->funnel = sellkit_funnel();

		if ( empty( $key ) && empty( $this->funnel->current_step_data ) ) {
			$this->is_dummy_data = true;
		}

		if ( $key ) {
			$order_id = wc_get_order_id_by_order_key( $key );
		}

		if ( ! empty( $order_id ) ) {
			$this->order = $this->get_order_object( $order_id );
		}
	}

	/**
	 * Get instance.
	 *
	 * @return Order_Cart_Detail_Data
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get order object.
	 *
	 * @return mixed
	 * @param string $order_id Order id.
	 */
	public function get_order_object( $order_id ) {
		$order         = wc_get_order( $order_id );
		$main_order_id = $order->get_meta( 'sellkit_main_order_id' );

		if ( empty( $main_order_id ) ) {
			return $order;
		}

		$main_order  = new \WC_Order( $main_order_id );
		$total_order = new \WC_Order();

		foreach ( $order->get_items() as $item ) {
			$total_order->add_item( $item );
		}

		foreach ( $main_order->get_items() as $item ) {
			$total_order->add_item( $item );
		}

		$total_price = $order->get_total() + $main_order->get_total();

		$total_order->set_total( $total_price );
		$total_order->set_payment_method( $main_order->get_payment_method() );
		$total_order->set_address( $order->get_address() );
		$total_order->set_address( $order->get_address( 'shipping' ), 'shipping' );

		return $total_order;
	}

	/**
	 * Get order details data.
	 *
	 * @return array|boolean
	 */
	public function get_details_data() {
		if ( empty( $this->order ) || $this->is_dummy_data ) {
			return $this->get_dummy_data();
		}

		return [
			'items'  => $this->get_items(),
			'prices' => $this->get_prices(),
		];
	}

	/**
	 * Get dummy data.
	 *
	 * @return array
	 */
	private function get_dummy_data() {
		$placeholder_image = wc_placeholder_img_src();

		return [
			'items' => [
				[
					'product_name' => 'Flying ninja',
					'product_image' => $placeholder_image,
					'quantity' => '3',
					'product_link' => '',
					'product_price' => wc_price( 82 ),
				],
				[
					'product_name' => 'Gucci watch',
					'product_image' => $placeholder_image,
					'quantity' => '2',
					'product_link' => '',
					'product_price' => wc_price( 82 ),
				],
			],
			'prices' => [
				'subtotal' => [
					'label' => 'subtotal',
					'value' => '$164.00',
				],
				'shipping' => [
					'label' => 'shipping',
					'value' => '$10.00',
				],
				'discount' => [
					'label' => 'discount',
					'value' => '$17.00',
				],
				'total' => [
					'label' => 'total',
					'value' => '$75.00',
				],
			],
		];
	}

	/**
	 * Get items.
	 *
	 * @return array
	 */
	private function get_items() {
		return $this->order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
	}

	/**
	 * Get prices.
	 *
	 * @return array
	 */
	private function get_prices() {
		return $this->order->get_order_item_totals();
	}
}

