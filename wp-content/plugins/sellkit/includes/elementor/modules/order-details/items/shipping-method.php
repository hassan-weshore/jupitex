<?php
/**
 * Add Order Details Shipping Method Items.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit\Elementor\Modules\Order_Details\Items;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;

/**
 * Shipping Method Item.
 *
 * Initializing the shipping method item by extending item base abstract class.
 *
 * @since 1.1.0
 */
class Shipping_Method extends Item_Base {

	/**
	 * Get Item class postfix.
	 *
	 * Retrieve the Item class postfix.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string Item class postfix.
	 */
	public function get_class_name() {
		return 'method';
	}
	/**
	 * Get Item type.
	 *
	 * Retrieve the Item type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string Item type.
	 */
	public function get_type() {
		return 'shipping_method';
	}

	/**
	 * Add render attribute.
	 *
	 * Add render attributes for each item based on the settings.
	 *
	 * @since 1.1.0
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
	 * @since 1.1.0
	 * @access public
	 * @param object $order_data Order data.
	 */
	public function render_content( $order_data ) {
		?>
		<strong
			<?php
				echo $this->widget->get_render_attribute_string( 'order-details-item-' . $this->get_id() );
			?>
			>
			<?php
				echo $order_data['payment_method_title'];
			?>
		</strong>
		<?php
	}

	/**
	 * Render dummy content.
	 *
	 * Render the Item dummy content.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function render_dummy_content() {
		?>
		<strong
			<?php
				echo $this->widget->get_render_attribute_string( 'order-details-item-' . $this->get_id() );
			?>
			>
			<?php
				echo __( 'COD... ( shipping Method )', 'sellkit' )
			?>
		</strong>
		<?php
	}
}
