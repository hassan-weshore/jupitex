<?php
/**
 * Add Bank Account Transfer Information.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.2.8
 */

namespace Sellkit\Elementor\Modules\Order_Details\Items;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;

/**
 * Shipping Method Item.
 *
 * Initializing the shipping method item by extending item base abstract class.
 *
 * @since 1.2.8
 */
class Bank_Transfer extends Item_Base {

	/**
	 * Get Item class postfix.
	 *
	 * Retrieve the Item class postfix.
	 *
	 * @since 1.2.8
	 * @access public
	 *
	 * @return string Item class postfix.
	 */
	public function get_class_name() {
		return 'bank';
	}
	/**
	 * Get Item type.
	 *
	 * Retrieve the Item type.
	 *
	 * @since 1.2.8
	 * @access public
	 *
	 * @return string Item type.
	 */
	public function get_type() {
		return 'bank_transfer';
	}

	/**
	 * Add render attribute.
	 *
	 * Add render attributes for each item based on the settings.
	 *
	 * @since 1.2.8
	 * @access public
	 */
	public function add_field_render_attribute() {
		$attributes = [
			'class' => 'order-details-item-content',
			'id' => 'order-details-' . $this->get_id(),
		];

		$this->widget->add_render_attribute( 'order-details-item-' . $this->get_id(), $attributes );
	}

	/**
	 * Render content.
	 *
	 * Render the item content.
	 *
	 * @since 1.2.8
	 * @access public
	 * @param object $order_data Order data.
	 */
	public function render_content( $order_data ) {
		$account_details = get_option( 'woocommerce_bacs_accounts' );

		if ( 'bacs' !== $order_data['payment_method'] || empty( $account_details ) ) {
			return;
		}

		$content = '<section class="woocommerce-bacs-bank-details">';

		foreach ( $account_details as $account_detail ) {
			$content .= '<h3 class="wc-bacs-bank-details order_details bacs_details">' . esc_html( $account_detail['account_name'] ) . '</h3>';
			$content .= '<ul class="wc-bacs-bank-details order_details bacs_details">';

			foreach ( $account_detail as $key => $detail ) {
				if ( empty( $detail ) ) {
					continue;
				}

				$title = esc_html( strtoupper( str_replace( '_', ' ', $key ) ) );
				$class = esc_attr( str_replace( '_', '-', $key ) );

				/* Translators: 1:className 2:title 3:value */
				$content .= sprintf(
					'<li class="%1$s">%2$s<strong>%3$s</strong></li>',
					$class,
					$title,
					esc_html( $detail )
				);
			}

			$content .= '</ul>';
		}

		$content .= '</section>';

		echo wp_kses_post( $content );
	}

	/**
	 * Render dummy content.
	 *
	 * Render the Item dummy content.
	 *
	 * @since 1.2.8
	 * @access public
	 */
	public function render_dummy_content() {
		?>
			<div>
				<?php echo esc_html__( 'Bank Details', 'sellkit' ); ?>
			</div>
		<?php
	}
}
