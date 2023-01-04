<?php
/**
 * Integrate posts-related widgets with JetEngine Query Builder
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Blog_Query_Builder' ) ) {

	/**
	 * Define Jet_Blog_Query_Builder class
	 */
	class Jet_Blog_Query_Builder {

		public function __construct() {

			add_action( 'jet-blog/query-controls', array( $this, 'register_query_controls' ), 10, 2 );
			add_filter( 'jet-blog/pre-query', array( $this, 'maybe_do_query' ), 10, 4 );
			add_filter( 'jet-blog/smart-listing/exported-options', array( $this, 'export_smart_list_options' ) );
			add_filter( 'jet-blog/query-conditions', array( $this, 'register_query_conditions' ) );

		}

		public function register_query_conditions( $conditions = array() ) {
			$conditions['use_custom_query!'] = 'true';
			return $conditions;
		}

		public function export_smart_list_options( $options ) {
			$options[] = 'query_builder_id';
			return $options;
		}

		public function get_request_data( $key, $default = false ) {
			return isset( $_REQUEST['jet_request_data'][ $key ] ) ? $_REQUEST['jet_request_data'][ $key ] : $default;
		}

		public function maybe_do_query( $result, $settings, $query_args, $widget ) {

			$use_custom = ! empty( $settings['use_custom_query'] ) ? filter_var( $settings['use_custom_query'], FILTER_VALIDATE_BOOLEAN ) : false;

			if ( ! $use_custom || empty( $settings['query_builder_id'] ) || ! empty( $settings['is_archive_template'] ) ) {
				return $result;
			}

			$query_id = absint( $settings['query_builder_id'] );
			$query    = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $query_id );

			if ( ! $query ) {
				return $result;
			}

			$term = $this->get_request_data( 'term' );

			$query->setup_query();

			if ( $term ) {
				
				$tax = isset( $settings['filter_by'] ) ? $settings['filter_by'] : false;

				if ( $tax ) {
					$query->replace_tax_query_row( array( array(
						'taxonomy' => $tax,
						'field'    => 'term_id',
						'terms'    => array( $term )
					) ) );
				}

			}

			if ( ! empty( $query_args['posts_per_page'] ) ) {
				$query->final_query['posts_per_page'] = absint( $query_args['posts_per_page'] );
			} elseif ( isset( $settings['posts_columns'] ) && isset( $settings['posts_rows'] ) ) {
			
				$cols = isset( $settings['posts_columns'] ) ? absint( $settings['posts_columns'] ) : 1;
				$rows = ! empty( $settings['posts_rows'] ) ? absint( $settings['posts_rows'] ) : 3;
				$num  = $cols * $rows;

				$featured  = ! empty( $settings['featured_post'] ) ? true : false;

				if ( $featured ) {
					$num++;
				}

				$query->final_query['posts_per_page'] = $num;

			}

			$paged = $this->get_request_data( 'paged' );

			if ( $paged ) {
				$query->final_query['paged'] = $paged;
				$query->final_query['page']  = $paged;
			}

			if ( isset( $widget->query_data ) ) {
				$widget->query_data['max_pages']    = $query->get_items_pages_count();
				$widget->query_data['current_page'] = $query->get_current_items_page();
			}

			return $query->get_items();

		}

		public function get_query_builder_options() {
			
			$result  = array();
			$queries = \Jet_Engine\Query_Builder\Manager::instance()->get_queries();

			if ( ! empty( $queries ) ) {

				$result[''] = __( 'Select query...', 'jet-blog' );

				foreach( $queries as $query ) {

					if ( ! $query || ! is_object( $query ) ) {
						continue;
					}

					if ( 'posts' === $query->query_type ) {
						$result[ $query->id ] = $query->name;
					}
				}
			}

			return $result;

		}

		public function register_query_controls( $widget, $has_custom_query ) {

			$options = $this->get_query_builder_options();

			if ( empty( $options ) ) {
				$url  = add_query_arg( array( 'page' => 'jet-engine-query' ), admin_url( 'admin.php' ) );
				$desc = sprintf(
					__( 'You not have any Posts queries, create new here - %s', 'jet-blog' ),
					'<a href="' . $url . '">JetEngine Query Builder</a>'
				);

				$options = array( '' => __( 'Create new Posts Query with Query builder', 'jet-blog' ) );

			} else {
				$desc = esc_html__( 'Select query from JetEngine query builder to use as source. Supports only Posts queries', 'jet-blog' );
			}

			$conditions = array(
				'use_custom_query' => 'true',
			);

			if ( $has_custom_query ) {
				$conditions['is_archive_template!'] = 'yes';
				$label = esc_html__( 'Or use JetEngine Query Builder', 'jet-blog' );
			} else {
				$label = esc_html__( 'Select query from JetEngine Query Builder', 'jet-blog' );
				$widget->add_control(
					'use_custom_query',
					array(
						'label'        => esc_html__( 'Use Custom Query', 'jet-blog' ),
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
						'label_off'    => esc_html__( 'No', 'jet-blog' ),
						'return_value' => 'true',
						'default'      => '',
						'separator'    => 'before',
					)
				);
			}

			$widget->add_control(
				'query_builder_id',
				array(
					'label'       => $label,
					'description' => $desc,
					'type'        => \Elementor\Controls_Manager::SELECT,
					'default'     => '',
					'label_block' => true,
					'options'     => $options,
					'condition'   => $conditions,
				)
			);
		}

	}

}
