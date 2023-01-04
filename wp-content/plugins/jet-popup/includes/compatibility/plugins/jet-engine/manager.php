<?php
namespace Jet_Popup\Compatibility;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Compatibility Manager
 */
class Jet_Engine {

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

		$groups_list = [
			'jet-engine' => [
				'label'      => __( 'Jet Engine', 'jet-popup' ),
				'sub-groups' => [],
			],
		];

		return wp_parse_args( $groups_list, $conditions_group_list );
	}

	/**
	 * @param $conditions_sub_group_list
	 *
	 * @return array|object
	 */
	public function modify_popup_conditions_sub_group_list( $conditions_sub_group_list ) {

		$sub_groups_list = [
			'jet-engine-custom-query' => [
				'label'   => __( 'Custom Query', 'jet-popup' ),
				'options' => [],
			],
		];

		return wp_parse_args( $sub_groups_list, $conditions_sub_group_list );
	}

	/**
	 * @param $conditions_list
	 *
	 * @return mixed
	 */
	public function modify_popup_conditions_list( $conditions_list ) {

		$base_path = jet_popup()->plugin_path( 'includes/compatibility/plugins/jet-engine/conditions/' );

		$new_conditions_list = [
			'\Jet_Popup\Conditions\Jet_Engine_Custom_Query_Has_Items'    => $base_path . 'custom-query-has-items.php',
			'\Jet_Popup\Conditions\Jet_Engine_Custom_Query_Has_No_Items' => $base_path . 'custom-query-has-no-items.php',
		];

		return wp_parse_args( $new_conditions_list, $conditions_list );
	}


	/**
	 * [__construct description]
	 */
	public function __construct() {

		if ( ! class_exists( 'Jet_Engine' ) ) {
			return false;
		}

		$this->load_files();

		add_filter( 'jet-popup/conditions/conditions-group-list', [ $this, 'modify_popup_conditions_group_list' ], 10, 2 );
		add_filter( 'jet-popup/conditions/condition-sub-groups', [ $this, 'modify_popup_conditions_sub_group_list' ], 10, 2 );
		add_filter( 'jet-popup/conditions/conditions-list', [ $this, 'modify_popup_conditions_list' ], 10, 2 );
		//add_filter( 'jet-popup/rest-api/endpoint-list', [ $this, 'modify_popup_endpoint_list' ], 10, 2 );

	}

}
