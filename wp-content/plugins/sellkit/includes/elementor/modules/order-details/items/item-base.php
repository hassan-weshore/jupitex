<?php
/**
 * Add Item Base.
 *
 * @package JupiterX_Core\sellkit
 * @since 1.1.0
 */

namespace Sellkit\Elementor\Modules\Order_Details\Items;

defined( 'ABSPATH' ) || die();

use Elementor\Settings;
use Elementor\Plugin as Elementor;
/**
 * Item Base.
 *
 * An abstract class to register new order details item.
 *
 * @since 1.1.0
 * @abstract
 */
abstract class Item_Base {

	/**
	 * The funnel object.
	 *
	 * @since 1.1.0
	 * @var \Sellkit_Funnel Sellki funnel.
	 */
	public $funnel;

	/**
	 * Item_Base constructor.
	 *
	 * @NEXT
	 */
	public function __construct() {
		$this->funnel = sellkit_funnel();

		add_action( 'woocommerce_thankyou', [ $this, 'check_order_page' ] );
		remove_action( 'woocommerce_thankyou', [ $this, 'check_order_page' ] );
	}

	/**
	 * Order details widget.
	 *
	 * Holds the Order details widget instance.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $widget;

	/**
	 * Item field.
	 *
	 * Holds all the Items attributes.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $field;

	/**
	 * Get Item ID.
	 *
	 * Retrieve the Item type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string Item ID.
	 */
	public function get_id() {
		return $this->field['_id'];
	}

	/**
	 * Get item type.
	 *
	 * Retrieve the item type.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string Field type.
	 */
	public function get_type() {
		return $this->field['type'];
	}

	/**
	 * Get item label.
	 *
	 * Retrieve the item label.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string item label.
	 */
	public function get_label() {
		return $this->field['label'];
	}

	/**
	 * Get item icon.
	 *
	 * Retrieve the item icon.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string item icon.
	 */
	public function get_icon() {
		return $this->field['detail_item_icon'];
	}

	/**
	 * Get item class.
	 *
	 * Retrieve the item class.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string item class.
	 */
	public function get_class() {
		return 'sellkit-field';
	}

	/**
	 * Get item width.
	 *
	 * Retrieve the item width.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return integer Field width.
	 */
	public function get_width() {
		return $this->field['detail_item_width'];
	}

	/**
	 * Get item title.
	 *
	 * Retrieve the item title.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string Field title.
	 */
	public function get_title() {
		return '';
	}


	/**
	 * Render item.
	 *
	 * Render item label and content.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param object $widget Widget instance.
	 * @param array  $field Field.
	 */
	public function render( $widget, $field ) {
		$this->widget = $widget;
		$this->field  = $field;

		$this->add_field_render_attribute();
		$this->widget->add_render_attribute(
			'item-group-' . $this->get_id(),
			[
				'id' => 'sellkit-order-details-items-group-' . $this->get_id(),
				'class' => 'order_details woocommerce-order-overview__' . $this->get_class_name() . ' ' . $this->get_class_name() . ' sellkit-flex-wrap sellkit-item-type-' . $this->get_type() . ' sellkit-order-details-items-group elementor-column elementor-col-' . $this->get_width(),
			]
		);

		if ( ! empty( $field['width_tablet'] ) ) {
			$this->widget->add_render_attribute(
				'item-group-' . $this->get_id(),
				'class',
				'elementor-md-' . $field['width_tablet']
			);
		}

		if ( ! empty( $field['width_mobile'] ) ) {
			$this->widget->add_render_attribute(
				'item-group-' . $this->get_id(),
				'class',
				'elementor-sm-' . $field['width_mobile']
			);
		}
		?>
		<li <?php echo $this->widget->get_render_attribute_string( 'item-group-' . $this->get_id() ); ?>>
			<div class="order-details-heading-group">
			<?php
			$this->render_icon();
			$this->render_label();
			?>
			</div>
			<?php
			$this->check_order_page();
			?>
		</li>
		<?php
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
		$title = $this->get_title();

		$attributes = [
			'id' => 'order-details-item-' . $this->get_id(),
			'class' => $this->get_class(),
		];

		if ( ! empty( $title ) ) {
			$attributes['title'] = $title;
		}

		$this->widget->add_render_attribute( 'order-details-item-' . $this->get_id(), $attributes );
	}

	/**
	 * Render label.
	 *
	 * Render the label for each item.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function render_label() {

		if ( empty( $this->get_label() ) ) {
			return;
		}
		?>
		<h5
			class="sellkit-order-details-heading">
			<?php echo $this->get_label(); ?>
			</h5>
		<?php
	}

	/**
	 * Render icon.
	 *
	 * Render the icon for each item.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function render_icon() {
		if ( empty( $this->get_icon() ) ) {
			return;
		}
		?>
		<span class="sellkit-order-details-icon">
			<?php Elementor::$instance->icons_manager->render_icon( $this->get_icon() ); ?>
		</span>
		<?php
	}

	/**
	 * Check order page.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function check_order_page() {
		// phpcs:ignore
		$key = isset( $_GET['order-key'] ) ? sanitize_text_field( $_GET['order-key'] ) : false;
		$id  = wc_get_order_id_by_order_key( $key );

		if ( $key && ! empty( $this->funnel->funnel_id ) ) {
			$order      = wc_get_order( $id );
			$order_data = $order->get_data();

			$this->render_content( $order_data );
		}
	}

	/**
	 * Render content.
	 *
	 * Render the item content.
	 *
	 * @since 1.1.0
	 * @access public
	 * @abstract
	 *
	 * @return string The field content.
	 * @param object $order_data Order data.
	 */
	abstract public function render_content( $order_data );

	/**
	 * Render dummy content.
	 *
	 * Render the item dummy content.
	 *
	 * @since 1.1.0
	 * @access public
	 * @abstract
	 *
	 * @return string The field dummy content.
	 */
	abstract public function render_dummy_content();

	/**
	 * Render class name.
	 *
	 * Render the item class name.
	 *
	 * @since 1.1.0
	 * @access public
	 * @abstract
	 *
	 * @return string The field class name.
	 */
	abstract public function get_class_name();
}
