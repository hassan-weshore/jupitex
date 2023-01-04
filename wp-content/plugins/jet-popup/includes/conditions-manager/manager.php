<?php
namespace Jet_Popup\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * @var array
	 */
	private $_conditions = [];

	/**
	 * @var array
	 */
	private $_condition_sub_groups = [];

	/**
	 * @var string
	 */
	public  $conditions_key = 'jet_popup_conditions';

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {}

	/**
	 * [register_conditions description]
	 * @return [type] [description]
	 */
	public function register_conditions() {

		$this->_condition_sub_groups = apply_filters( 'jet-popup/conditions/condition-sub-groups', [
			'page-singular'  => [
				'label'  => __( 'Page', 'jet-popup' ),
				'options' => [],
			],
			'post-archive'  => [
				'label'  => __( 'Post', 'jet-popup' ),
				'options' => [],
			],
			'post-singular' => [
				'label'  => __( 'Post', 'jet-popup' ),
				'options' => [],
			],
		] );

		$base_path = jet_popup()->plugin_path( 'includes/conditions-manager/conditions/' );

		require $base_path . 'base.php';

		$default_conditions = apply_filters( 'jet-popup/conditions/conditions-list', [
			'\Jet_Popup\Conditions\Entire'                 => $base_path . 'entire.php',

			// Singular conditions
			'\Jet_Popup\Conditions\Front_Page'             => $base_path . 'singular-front-page.php',
			'\Jet_Popup\Conditions\Page'                   => $base_path . 'singular-page.php',
			'\Jet_Popup\Conditions\Page_Child'             => $base_path . 'singular-page-child.php',
			'\Jet_Popup\Conditions\Page_Template'          => $base_path . 'singular-page-template.php',
			'\Jet_Popup\Conditions\Page_404'               => $base_path . 'singular-404.php',
			'\Jet_Popup\Conditions\Post'                   => $base_path . 'singular-post.php',
			'\Jet_Popup\Conditions\Post_From_Category'     => $base_path . 'singular-post-from-cat.php',
			'\Jet_Popup\Conditions\Post_From_Tag'          => $base_path . 'singular-post-from-tag.php',

			// Archive conditions
			'\Jet_Popup\Conditions\Archive_All'            => $base_path . 'archive-all.php',
			'\Jet_Popup\Conditions\Archive_Category'       => $base_path . 'archive-category.php',
			'\Jet_Popup\Conditions\Archive_Tag'            => $base_path . 'archive-tag.php',
			'\Jet_Popup\Conditions\Archive_Search'         => $base_path . 'archive-search-results.php',

			// Custom Post Type
			'\Jet_Popup\Conditions\CPT_Singular_Post_Type' => $base_path . 'cpt-singular/cpt-singular-post-type.php',
			'\Jet_Popup\Conditions\CPT_Archive_Post_Type'  => $base_path . 'cpt-archive/cpt-archive-post-type.php',
			'\Jet_Popup\Conditions\CPT_Archive_Taxonomy'   => $base_path . 'cpt-archive/cpt-archive-taxonomy.php',

			// Advanced
			'\Jet_Popup\Conditions\Url_Param'              => $base_path . 'advanced-url-param.php',
			'\Jet_Popup\Conditions\Device'                 => $base_path . 'advanced-device.php',
			'\Jet_Popup\Conditions\Roles'                  => $base_path . 'advanced-roles.php',
		] );

		foreach ( $default_conditions as $class => $file ) {
			require $file;

			$instance = new $class;
			$id = $instance->get_id();
			$label = $instance->get_label();
			$sub_group = $instance->get_sub_group();

			$this->_conditions[ $id ] = $instance;

			$this->add_condition_sub_group_option( $sub_group, $id, $label );
		}

		$this->register_cpt_conditions();

		do_action( 'jet-popup/conditions/register', $this );

	}

	/**
	 * Register CPT conditions
	 */
	public function register_cpt_conditions() {
		$base_path = jet_popup()->plugin_path( 'includes/conditions-manager/conditions/' );

		require $base_path . 'cpt-archive/cpt-archive.php';
		require $base_path . 'cpt-archive/cpt-taxonomy.php';
		require $base_path . 'cpt-singular/cpt-single-post.php';
		require $base_path . 'cpt-singular/cpt-single-post-term.php';

		$post_types = \Jet_Popup_Utils::get_post_types_options();

		foreach ( $post_types as $type ) {
			$post_type_slug = $type[ 'value' ];
			$post_type_label = $type[ 'label' ];
			$post_type_obj = get_post_type_object( $post_type_slug );
			$post_type_taxonomies = \Jet_Popup_Utils::get_taxonomies_by_post_type( $post_type_slug );

			$archive_sub_group = $post_type_slug . '-archive';
			$this->register_condition_sub_group( $archive_sub_group, $post_type_label );

			$single_sub_group = $post_type_slug . '-single-post';
			$this->register_condition_sub_group( $single_sub_group, $post_type_label );

			$instance = new CPT_Single_Post( [
				'id'             => 'cpt-single-' . $post_type_slug,
				'label'          => sprintf( __( '%s Single', 'jet-popup' ), $post_type_obj->labels->singular_name ),
				'group'          => 'singular',
				'sub_group'      => $single_sub_group,
				'priority'       => 28,
				'body_structure' => 'jet_single',
				'value_control'  => [
					'type'        => 'f-search-select',
					'placeholder' => __( 'Select', 'jet-popup' ),
				],
				'value_options'  => false,
				'ajax_action'    =>  [
					'action' => 'get-posts',
					'params' => [
						'post_type' => $post_type_slug,
						'query'     => '',
					],
				],
			] );

			$this->_conditions[ $instance->get_id() ] = $instance;
			$this->add_condition_sub_group_option( $single_sub_group, 'cpt-single-' . $post_type_slug, $post_type_label );

			$instance = new CPT_Archive( [
				'id'             => 'cpt-archive-' . $post_type_slug,
				'label'          =>  sprintf( __( 'All %s Archives', 'jet-popup' ), $post_type_label ),
				'group'          => 'archive',
				'sub_group'      => $archive_sub_group,
				'priority'       => 9,
				'body_structure' => 'jet_archive',
			] );

			$this->_conditions[ $instance->get_id() ] = $instance;
			$this->add_condition_sub_group_option( $archive_sub_group, 'cpt-archive-' . $post_type_slug, sprintf( __( 'All %s Archives', 'jet-popup' ), $post_type_label ) );

			foreach ( $post_type_taxonomies as $taxonomy => $taxonomy_obj ) {

				$instance = new CPT_Taxonomy( [
					'id'             => 'cpt-taxonomy-' . $taxonomy_obj->name,
					'label'          => $taxonomy_obj->label,
					'group'          => 'archive',
					'sub_group'      => $archive_sub_group,
					'priority'       => 45,
					'body_structure' => 'jet_archive',
					'value_control'  => [
						'type'        => 'f-search-select',
						'placeholder' => __( 'Select taxonomy', 'jet-popup' ),
					],
					'value_options'  => false,
					'ajax_action'    =>  [
						'action' => 'get-tax-terms',
						'params' => [
							'tax_name' => $taxonomy_obj->name,
						],
					],
				] );

				$this->_conditions[ $instance->get_id() ] = $instance;
				$this->add_condition_sub_group_option( $archive_sub_group, 'cpt-taxonomy-' . $taxonomy_obj->name, $taxonomy_obj->label );

				$instance = new CPT_Single_Post_Term( [
					'id'             => 'cpt-post-term-' . $taxonomy_obj->name,
					'label'          => $taxonomy_obj->label,
					'group'          => 'singular',
					'sub_group'      => $single_sub_group,
					'priority'       => 27,
					'body_structure' => 'jet_single',
					'value_control'  => [
						'type'        => 'f-search-select',
						'placeholder' => __( 'Select taxonomy', 'jet-popup' ),
					],
					'value_options'  => false,
					'ajax_action'    =>  [
						'action' => 'get-tax-terms',
						'params' => [
							'tax_name' => $taxonomy_obj->name,
						],
					],
				] );

				$this->_conditions[ $instance->get_id() ] = $instance;
				$this->add_condition_sub_group_option( $single_sub_group, 'cpt-post-term-' . $taxonomy_obj->name, sprintf( 'In %s', $taxonomy_obj->label) );
			}
		}

	}

	/**
	 * @param false $id
	 * @param string $label
	 *
	 * @return false
	 */
	public function register_condition_sub_group( $id = false, $label = '' ) {

		if ( ! $id ) {
			return false;
		}

		if ( array_key_exists( $id, $this->_condition_sub_groups ) ) {
			return false;
		}

		$this->_condition_sub_groups[ $id ] = [
			'label'   => $label,
			'options' => [],
		];

	}

	/**
	 * @return array
	 */
	public function get_condition_sub_groups() {
		return $this->_condition_sub_groups;
	}

	/**
	 * @param false $sub_group
	 * @param false $id
	 * @param string $label
	 */
	public function add_condition_sub_group_option( $sub_group = false, $id = false, $label = '' ) {

		if ( ! $sub_group ) {
			return false;
		}

		if ( ! array_key_exists( $sub_group, $this->_condition_sub_groups ) ) {
			return false;
		}

		$this->_condition_sub_groups[ $sub_group ]['options'][] = [
			'label' => $label,
			'value' => $id,
		];
	}

	/**
	 * [get_condition description]
	 * @param  [type] $condition_id [description]
	 * @return [type]               [description]
	 */
	public function get_condition( $condition_id ) {
		return isset( $this->_conditions[ $condition_id ] ) ? $this->_conditions[ $condition_id ] : false;
	}

	/**
	 * [prepare_data_for_localize description]
	 * @return [type] [description]
	 */
	public function get_conditions_raw_data() {

		$sorted_conditions = apply_filters( 'jet-popup/conditions/conditions-group-list', [
			'entire'       => [
				'label'      => __( 'Entire', 'jet-popup' ),
				'sub-groups' => [],
			],
			'singular'     => [
				'label'      => __( 'Singular', 'jet-popup' ),
				'sub-groups' => [],
			],
			'archive'      => [
				'label'      => __( 'Archive', 'jet-popup' ),
				'sub-groups' => [],
			],
			'advanced'     => [
				'label'      => __( 'Advanced', 'jet-popup' ),
				'sub-groups' => [],
			],
		] );

		foreach ( $this->_conditions as $cid => $instance ) {
			$group = $instance->get_group();

			$current = [
				'label'         => $instance->get_label(),
				'priority'      => $instance->get_priority(),
				'action'        => $instance->ajax_action(),
				'options'       => $instance->get_avaliable_options(),
				'control'       => $instance->get_control(),
				'bodyStructure' => $instance->get_body_structure(),
			];

			$sorted_conditions[ $group ]['sub-groups'][ $cid ] = $current;
		}

		foreach ( $sorted_conditions as $group => $group_conditions ) {

			if ( isset( $group_conditions['sub-groups'] ) ) {
				$group_options = $this->get_condition_group_options( $group_conditions['sub-groups'] );
			} else {
				$group_options = [];
			}

			$sorted_conditions[ $group ]['options'] = $group_options;
		}

		return $sorted_conditions;
	}

	/**
	 * @param array $group_conditions
	 *
	 * @return false
	 */
	public function get_condition_group_options( $group_conditions = [] ) {

		if ( empty( $group_conditions ) ) {
			return [];
		}

		$options = [];
		$condition_sub_groups = $this->get_condition_sub_groups();

		foreach ( $group_conditions as $condition_id => $condition_data ) {
			$instance = $this->get_condition( $condition_id );
			$sub_group = $instance->get_sub_group();

			if ( ! $sub_group ) {
				$options[ $condition_id ] = [
					'label' => $condition_data['label'],
					'value' => $condition_id,
				];
			} else {
				if ( array_key_exists( $sub_group, $condition_sub_groups ) ) {

					if ( ! array_key_exists( $sub_group, $options ) ) {
						$options[ $sub_group ] = $condition_sub_groups[ $sub_group ];
					}
				}
			}
		}

		return array_values( $options );
	}

	/**
	 * [get_popup_id description]
	 * @return [type] [description]
	 */
	public function get_popup_id() {
		return get_the_ID();
	}

	/**
	 * @return array
	 */
	public function get_site_popup_conditions() {

		$site_conditions = get_option( $this->conditions_key, [] );

		if ( empty( $site_conditions ) || empty( $site_conditions['jet-popup'] ) ) {
			return [];
		}

		$site_popup_conditions = $site_conditions['jet-popup'];

		return array_map( function( $popup_data ) {

			if ( ! isset( $popup_data['conditions'] ) ) {
				return $popup_data = [
					'conditions'    => $popup_data,
					'relation_type' => 'or',
				];
			}

			return $popup_data;
		}, $site_popup_conditions );
	}

	/**
	 * @param $popup_id
	 * @param $conditions
	 * @param $relation_type
	 *
	 * @return void
	 */
	public function update_site_popup_conditions( $popup_id, $conditions, $relation_type ) {
		$site_conditions = get_option( $this->conditions_key, [] );

		if ( ! isset( $site_conditions['jet-popup'] )) {
			$site_conditions['jet-popup'] = [];
		}

		$site_conditions['jet-popup'][ $popup_id ] = [
			'conditions'    => $conditions,
			'relation_type' => $relation_type,
		];

		update_option( $this->conditions_key, $site_conditions, true );

	}

	/**
	 * [update_popup_conditions description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function update_popup_conditions( $popup_id = false, $conditions = [], $relation_type = 'or' ) {
		$popup_page_settings = get_post_meta( $popup_id, '_elementor_page_settings', true );
		$popup_page_settings['jet_popup_conditions'] = $conditions;
		$popup_page_settings['jet_popup_relation_type'] = $relation_type;
		update_post_meta( $popup_id, '_elementor_page_settings', $popup_page_settings );

		update_post_meta( $popup_id, '_conditions', $conditions );
		update_post_meta( $popup_id, '_relation_type', $relation_type );

		$this->update_site_popup_conditions( $popup_id, $conditions, $relation_type );
	}

	/**
	 * [get_popup_conditions description]
	 * @param  boolean $post_id [description]
	 * @return [type]           [description]
	 */
	public function get_popup_conditions( $popup_id = false ) {

		$popup_conditions = get_post_meta( $popup_id, '_conditions', true );

		if ( ! empty( $popup_conditions ) ) {
			$relation_type = get_post_meta( $popup_id, '_relation_type', true );

			$popup_conditions = array_map( function ( $condition ) {

				if ( 'entire' === $condition['group'] && empty( $condition['subGroup'] ) ) {
					$condition['subGroup'] = 'entire';
				}

				return $condition;
			}, $popup_conditions );

			return [
				'relationType' => $relation_type,
				'conditions'   => $popup_conditions,
			];
		}

		$popup_page_settings = get_post_meta( $popup_id, '_elementor_page_settings', true );

		if ( isset( $popup_page_settings['jet_popup_conditions'] ) ) {
			$relation_type = isset( $popup_page_settings['jet_popup_relation_type'] ) ? $popup_page_settings['jet_popup_relation_type'] : 'or';

			return [
				'relationType' => $relation_type,
				'conditions'   => array_map( function ( $condition ) {

					if ( 'entire' === $condition['group'] && empty( $condition['subGroup'] ) ) {
						$condition['subGroup'] = 'entire';
					}

					return $condition;
				}, $popup_page_settings['jet_popup_conditions'] ),
			];
		}

		// Backward compatibility conditions
		$old_conditions = $this->get_old_conditions( $popup_id );

		return $this->maybe_convert_popup_conditions( $old_conditions );
	}

	/**
	 * [get_post_conditions description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function get_old_conditions( $post_id ) {
		$group      = '';
		$conditions = get_post_meta( $post_id, '_elementor_page_settings', true );
		$sanitized  = array();

		if ( ! $conditions ) {
			$conditions = [];
		}

		foreach ( $conditions as $condition => $value ) {

			if ( false === strpos( $condition, 'conditions_' ) ) {
				continue;
			}

			if ( 'conditions_top' === $condition ) {
				$group             = $value;
				$sanitized['main'] = $group;
				continue;
			}

			if ( 'conditions_sub_' . $group === $condition ) {
				$sanitized[ $value ] = $this->get_old_condition_args( $value, $conditions );
				continue;
			}
		}

		return $sanitized;
	}

	/**
	 * Find condition arguments in saved data
	 *
	 * @param  [type] $cid        [description]
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function get_old_condition_args( $cid, $conditions ) {

		$args   = [];
		$prefix = 'conditions_' . $cid . '_';

		foreach ( $conditions as $condition => $value ) {

			if ( false === strpos( $condition, $prefix ) ) {
				continue;
			}

			$args[ str_replace( $prefix, '', $condition ) ] = $value;
		}

		return $args;
	}

	/**
	 * [convert_popup_conditions description]
	 * @param  boolean $post_id [description]
	 * @return [type]           [description]
	 */
	public function maybe_convert_popup_conditions( $condition = [] ) {

		if ( ! array_key_exists( 'main', $condition ) ) {
			return [
				'relationType' => 'or',
				'conditions'   => $condition,
			];
		}

		$new_condition = [];

		$condition_array_keys = array_keys( $condition );
		$sub_group            = isset( $condition_array_keys[1] ) ? $condition_array_keys[1] : false;
		$sub_group_value      = '';

		if ( $sub_group && isset( $sub_group ) ) {
			$sub_group_key = $condition[ $sub_group ];

			$key_value = ! empty( array_keys( $sub_group_key ) ) ? array_keys( $sub_group_key )[0] : false;
			$sub_group_value = $key_value ? $sub_group_key[ $key_value ] : '';
		}

		if ( ! empty( $sub_group_value ) && is_array( $sub_group_value ) ) {

			foreach ( $sub_group_value as $key => $value ) {
				$new_condition[] = [
					'id'            => uniqid( '_' ),
					'include'       => 'true',
					'group'         => $condition['main'],
					'subGroup'      => $sub_group ? $sub_group : '',
					'subGroupValue' => $value,
				];
			}
		} else {
			$sub_group_value = ! is_array( $sub_group_value ) ? $sub_group_value : '';

			$new_condition[] = [
				'id'            => uniqid( '_' ),
				'include'       => 'true',
				'group'         => $condition['main'],
				'subGroup'      =>  $sub_group ? $sub_group : '',
				'subGroupValue' => $sub_group_value,
			];
		}

		return [
			'relationType' => 'or',
			'conditions'   => $new_condition,
		];
	}

	/**
	 * @param $popup_id
	 *
	 * @return false|string
	 */
	public function popup_conditions_verbose( $popup_id = null ) {

		$verbose = '';

		$conditions_data = $this->get_popup_conditions( $popup_id );

		if ( empty( $conditions_data ) ) {
			return false;
		}

		$conditions = $conditions_data['conditions'];
		$relation_type = $conditions_data['relationType'];

		$verbose = '';

		if ( ! empty( $conditions ) ) {

			foreach ( $conditions as $key => $condition ) {
				$include         = filter_var( $condition['include'], FILTER_VALIDATE_BOOLEAN );
				$group           = $condition['group'];
				$sub_group       = $condition['subGroup'];
				$sub_group_value = $condition['subGroupValue'];
				$instance = $this->get_condition( $sub_group );

				$item_class = 'jet-popup-conditions-list__item';

				if ( ! $include ) {
					$item_class .= ' exclude';
					$include_icon = '<span class="dashicons dashicons-minus"></span>';
				} else {
					$include_icon = '<span class="dashicons dashicons-plus-alt2"></span>';
				}

				$relation_type_label = ( 'or' === $relation_type ) ? __( 'Or', 'jet-popup' ) : __( 'And', 'jet-popup' );

				if ( $instance ) {
					if ( ! empty( $sub_group_value ) ) {
						$label = $instance->get_label_by_value( $sub_group_value );
						$verbose .= sprintf( '<div class="%1$s">%4$s<span>%2$s: </span><i>%3$s</i><span class="relation-type">%5$s</span></div>', $item_class, $instance->get_label(), $label, $include_icon, $relation_type_label );
					} else {
						$verbose .= sprintf( '<div class="%1$s">%3$s<span>%2$s</span><span class="relation-type">%4$s</span></div>', $item_class, $instance->get_label(), $include_icon, $relation_type_label );
					}
				} else {
					$verbose .= sprintf( '<div class="%1$s">%3$s<span>%2$s</span><span class="relation-type"></span></div>', $item_class, __( 'Undefined condition type', 'jet-popup' ), \Jet_Popup_Utils::get_admin_ui_icon( 'warning' ) );
				}

			}
		} else {
			$verbose .= sprintf(
				'<div class="jet-popup-conditions-list__item not-selected">%2$s<span>%1$s</span></div>',
				__( 'Conditions not selected', 'jet-popup' ),
				\Jet_Popup_Utils::get_admin_ui_icon( 'warning' ),
			);
		}

		return $verbose;
	}

	/**
	 * Run condtions check for passed type. Return {template_id} on firs condition match.
	 * If not matched - return false
	 *
	 * @return int|bool
	 */
	public function find_matched_popups_by_conditions() {

		$conditions = $this->get_site_popup_conditions();

		$popup_id_list = [];

		foreach ( $conditions as $popup_id => $popup_data ) {

			$popup_conditions = $popup_data['conditions'];
			$relation_type = $popup_data['relation_type'];

			if ( empty( $popup_conditions ) ) {
				continue;
			}

			$check_list = [];

			// for multi-language plugins
			$popup_id = apply_filters( 'jet-popup/get_conditions/template_id', $popup_id );

			$popup_conditions = array_map( function( $condition ) use ( $popup_id ) {

				$include = filter_var( $condition['include'], FILTER_VALIDATE_BOOLEAN );

				$sub_group = $condition['subGroup'];

				$instance = $this->get_condition( $sub_group );

				if ( ! $instance ) {
					$condition['match'] = true;

					return $condition;
				}

				$sub_group_value = isset( $condition['subGroupValue'] ) ? $condition['subGroupValue'] : '';

				$instance_check = call_user_func( array( $instance, 'check' ), $sub_group_value, $sub_group );

				$condition['match'] = $instance_check;

				return $condition;

			}, $popup_conditions );

			$includes_matchs = [];
			$excludes_matchs = [];

			foreach ( $popup_conditions as $key => $condition ) {
				$include = filter_var( $condition['include'], FILTER_VALIDATE_BOOLEAN );

				if ( $include ) {
					$includes_matchs[] = $condition['match'];
				} else {
					$excludes_matchs[] = $condition['match'];
				}
			}

			if ( 'and' === $relation_type ) {
				// 'and' check
				// include only we have at least 1 include condition and if all included conditions are met (no failed conditions)
				$is_included = ( ! empty( $includes_matchs ) && ! in_array( false, $includes_matchs ) ) ? true : false;
				// exclude if we have at least 1 exclude condition and all exclude condition are met
				$is_excluded = ( ! empty( $excludes_matchs ) && ! in_array( false, $excludes_matchs ) ) ? true : false;
			} else {
				// 'or' check
				// include if we have at least 1 include condition and if at least 1 include condition are met
				$is_included = ( ! empty( $includes_matchs ) && in_array( true, $includes_matchs ) ) ? true : false;
				// exclude if we have at least 1 exclude condition and if at least 1 exclude condition are met
				$is_excluded = ( ! empty( $excludes_matchs ) && in_array( true, $excludes_matchs ) ) ? true : false;
			}

			// final check - this template are valid only if its included and not excluded at the same time.
			// this relation potentially also could be controlled by option
			if ( $is_included && ! $is_excluded ) {
				$popup_id_list[] = $popup_id;
			}

		}

		if ( ! empty( $popup_id_list ) ) {
			return $popup_id_list;
		}

		return false;
	}

	/**
	 * [__construct description]
	 */
	public function __construct() {
		$this->load_files();

		add_action( 'init', [ $this, 'register_conditions' ], 999  );
	}

}
