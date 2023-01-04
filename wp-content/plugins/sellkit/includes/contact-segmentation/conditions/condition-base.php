<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Contact_Data;

defined( 'ABSPATH' ) || die();

/**
 * Class Condition_Base.
 *
 * @package Sellkit\Contact_Segmentation\Base.
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Condition_Base {

	const SELLKIT_DROP_DOWN_CONDITION_VALUE    = 'drop-down';
	const SELLKIT_TEXT_CONDITION_VALUE         = 'text';
	const SELLKIT_NUMBER_CONDITION_VALUE       = 'number';
	const SELLKIT_MULTISELECT_CONDITION_VALUE  = 'multi-select';
	const SELLKIT_REACT_SELECT_CONDITION_VALUE = 'react-select';

	/**
	 * Main contact data.
	 *
	 * @var Contact_Data
	 * @since 1.1.0
	 */
	public $data;

	/**
	 * Cart_Category constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$contact_data = Contact_Data::get_instance();

		$this->data = $contact_data->get_data();
	}

	/**
	 * Get title.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	abstract public function get_title();

	/**
	 * Get name.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get type.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	abstract public function get_type();

	/**
	 * Get value.
	 *
	 * @since 1.1.0
	 */
	public function get_value() {
		if ( empty( $this->data[ str_replace( '-', '_', $this->get_name() ) ] ) ) {
			return '';
		}

		return $this->data[ str_replace( '-', '_', $this->get_name() ) ];
	}

	/**
	 * It is pro or not.
	 *
	 * @since 1.1.0
	 */
	abstract public function is_pro();

	/**
	 * All the conditions are not searchable by default.
	 *
	 * @return false
	 * @since 1.1.0
	 */
	public function is_searchable() {
		return false;
	}

	/**
	 * Check if the condition is active or not, Conditions are active by default.
	 *
	 * @return bool
	 */
	public function is_active() {
		return true;
	}
}
