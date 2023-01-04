<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_Pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Customer value.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Customer_Value extends Condition_Base {

	/**
	 * Customer_Value constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		parent::__construct();

		add_filter( "sellkit_cs_conditions_value_{$this->get_name()}", [ $this, 'update_customer_type_to_score' ], 1, 9999 );
	}

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'customer-value';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'RFM Segments', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return 'customer-value-field';
	}

	/**
	 * Gets value.
	 *
	 * @since 1.1.0
	 */
	public function get_value() {
		return intval( $this->data['rfm_r'] ) . intval( $this->data['rfm_f'] ) . intval( $this->data['rfm_m'] );
	}

	/**
	 * Get the options
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_options() {
		return [
			'champions' => [
				'label' => __( 'Champions', 'sellkit-pro' ),
				'desc' => __( 'Bought recently, order often and spend the most. These customers are responsible for a big share of your revenue so put a lot of effort into keeping them happy.', 'sellkit-pro' ),
			],
			'loyal' => [
				'label' => __( 'Loyal', 'sellkit-pro' ),
				'desc' => __( 'Orders regularly. Responsive to promotions. These are very active and therefore very valuable customers.', 'sellkit-pro' ),
			],
			'potential-loyalist' => [
				'label' => __( 'Potential Loyalist', 'sellkit-pro' ),
				'desc' => __( 'These customers already bought from you more than once but the size of their basket was not too big.', 'sellkit-pro' ),
			],
			'new-customers' => [
				'label' => __( 'New Customers', 'sellkit-pro' ),
				'desc' => __( 'These customers bought from you relatively recently for average or below-average price and they have not been frequent customers - possibly this is their first purchase from your website.', 'sellkit-pro' ),
			],
			'promising' => [
				'label' => __( 'Promising', 'sellkit-pro' ),
				'desc' => __( 'Spends frequently and a good amount. But the last purchase was long time ago.', 'sellkit-pro' ),
			],
			'customer-needing-attention' => [
				'label' => __( 'Customers Needing Attention', 'sellkit-pro' ),
				'desc' => __( 'Above average customer values whose last purchase happened in a relatively long time.', 'sellkit-pro' ),
			],
			'about-to-sleep' => [
				'label' => __( 'About To Sleep', 'sellkit-pro' ),
				'desc' => __( 'Below average recency, frequency and monetary values. Will lose them if not reactivated.', 'sellkit-pro' ),
			],
			'can-not-lose-them' => [
				'label' => __( 'Cannot Lose Them', 'sellkit-pro' ),
				'desc' => __( 'who used to visit and purchase quite often, but haven’t been visiting recently. The customer value of the members of this segment is above average but they have not made a purchase recently.', 'sellkit-pro' ),
			],
			'at-risk' => [
				'label' => __( 'At Risk', 'sellkit-pro' ),
				'desc' => __( 'Spent big money and purchased often. But long time ago. Need to bring them back!', 'sellkit-pro' ),
			],
			'hibernating-customers' => [
				'label' => __( 'Hibernating customers', 'sellkit-pro' ),
				'desc' => __( 'Last purchase was long back, low spenders and low number of orders. Do not overspend on advertising for this group as the return on your investment is not likely to be positive.', 'sellkit-pro' ),
			],
			'lost-customers' => [
				'label' => __( 'Lost customers', 'sellkit-pro' ),
				'desc' => __( 'Made last purchase long time ago and didn’t engage at all recently. Do not waste resources on them as they are very unlikely to come back.', 'sellkit-pro' ),
			],
		];
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.1.0
	 */
	public function is_pro() {
		return true;
	}


	/**
	 * Updates customer values.
	 *
	 * @since 1.1.0
	 * @param array $user_types Condition value.
	 */
	public function update_customer_type_to_score( $user_types ) {
		if ( is_numeric( $user_types[0] ) ) {
			return $user_types;
		}

		$customer_value = [
			'champions' => [ 555, 554, 544, 545, 454, 455, 445 ],
			'loyal' => [ 543, 444, 435, 355, 354, 345, 344, 335 ],
			'potential-loyalist' => [ 553, 551, 552, 541, 542, 533, 532, 531, 452, 451, 442, 441, 431, 453, 433, 432, 423, 353, 352, 351, 342, 341, 333, 323 ],
			'new-customers' => [ 512, 511, 422, 421, 412, 411, 311 ],
			'promising' => [ 525, 524, 523, 522, 521, 515, 514, 513, 425, 424, 413, 414, 415, 315, 314, 313 ],
			'customer-needing-attention' => [ 535, 534, 443, 434, 343, 334, 325, 324 ],
			'about-to-sleep' => [ 331, 321, 312, 221, 213, 231, 241, 251 ],
			'can-not-lose-them' => [ 155, 154, 144, 214, 215, 115, 114, 113 ],
			'at-risk' => [ 255, 254, 245, 244, 253, 252, 243, 242, 235, 234, 225, 224, 153, 152, 145, 143, 142, 135, 134, 133, 125, 124 ],
			'hibernating-customers' => [ 332, 322, 231, 241, 251, 233, 232, 223, 222, 132, 123, 122, 212, 211 ],
			'lost-customers' => [ 111, 112, 121, 131, 141, 151 ],
		];

		$values = [];

		foreach ( $user_types as $user_type ) {
			$values = array_merge( $values, $customer_value[ $user_type ] );
		}

		return $values;
	}
}
