<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class User_Role_Condition
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class User_Role extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'user-role';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'User Role', 'sellkit' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return self::SELLKIT_MULTISELECT_CONDITION_VALUE;
	}

	/**
	 * Get the options
	 *
	 * @since 1.1.0
	 * @return string[]
	 */
	public function get_options() {
		global $wp_roles;

		$neat_roles = [];

		$all_roles = $wp_roles->roles;

		foreach ( $all_roles as $role_key => $role ) {
			$neat_roles[ $role_key ] = $role['name'];
		}

		return $neat_roles;
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.1.0
	 */
	public function is_pro() {
		return false;
	}
}
