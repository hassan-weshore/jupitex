<?php
namespace JupiterX_Core\Raven\Modules\Posts\Widgets;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Utils;
use JupiterX_Core\Raven\Modules\Posts\Classes\Base_Widget;
use JupiterX_Core\Raven\Modules\Posts\Module;
use JupiterX_Core\Raven\Modules\Posts\Carousel\Skins;
use JupiterX_Core\Raven\Controls\Query as Control_Query;

/**
 * Temporary suppressed.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class Posts_Carousel extends Base_Widget {

	public function get_name() {
		return 'raven-posts-carousel';
	}

	public function get_title() {
		return __( 'Posts Carousel', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-posts-carousel';
	}

	public function get_script_depends() {
		return [ 'swiper', 'jupiterx-core-raven-object-fit' ];
	}

	public function get_style_depends() {
		return [ 'dashicons' ];
	}

	protected function register_skins() {
		$this->add_skin( new Skins\Classic( $this ) );
		$this->add_skin( new Skins\Cover( $this ) );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_settings_controls();
		$this->register_sort_filter_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'is_archive_template', [
				'label'        => esc_html__( 'Is Archive Template', 'jupiterx-core' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'    => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'true',
				'default'      => '',
			]
		);

		$this->add_group_control(
			'raven-posts',
			[
				'name' => 'query',
				'condition'    => [
					'is_archive_template' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->update_control(
			'_skin',
			[
				'frontend_available' => 'true',
			]
		);
	}

	protected function register_settings_controls() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'query_posts_per_page',
			[
				'label' => __( 'How many posts?', 'jupiterx-core' ),
				'type' => 'number',
				'default' => 10,
				'min' => 1,
				'max' => 50,
				'frontend_available' => true,
				'condition'    => [
					'is_archive_template' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_sort_filter_controls() {
		$this->start_controls_section(
			'section_sort_filter',
			[
				'label' => __( 'Sort & Filter', 'jupiterx-core' ),
				'condition'    => [
					'is_archive_template' => '',
				],
			]
		);

		$this->add_control(
			'query_orderby',
			[
				'label' => __( 'Order By', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'date',
				'options' => [
					'date' => __( 'Date', 'jupiterx-core' ),
					'title' => __( 'Title', 'jupiterx-core' ),
					'menu_order' => __( 'Menu Order', 'jupiterx-core' ),
					'rand' => __( 'Random', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'query_order',
			[
				'label' => __( 'Order', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'DESC',
				'options' => [
					'ASC' => __( 'ASC', 'jupiterx-core' ),
					'DESC' => __( 'DESC', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'query_offset',
			[
				'label' => __( 'Offset', 'jupiterx-core' ),
				'description' => __( 'Use this setting to skip over posts (e.g. \'4\' to skip over 4 posts).', 'jupiterx-core' ),
				'type' => 'number',
				'default' => 0,
				'min' => 0,
				'max' => 100,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'query_excludes',
			[
				'label' => __( 'Excludes', 'jupiterx-core' ),
				'type' => 'select2',
				'multiple' => true,
				'label_block' => true,
				'default' => [ 'current_post' ],
				'options' => [
					'current_post' => __( 'Current Post', 'jupiterx-core' ),
					'manual_selection' => __( 'Manual Selection', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'query_excludes_ids',
			[
				'label' => __( 'Search & Select', 'jupiterx-core' ),
				'type' => 'raven_query',
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'query_excludes' => 'manual_selection',
				],
				'query' => [
					'source' => Control_Query::QUERY_SOURCE_POST,
					'control_query' => [
						'post_type' => 'query_post_type',
					],
				],
			]
		);

		$this->end_controls_section();
	}

	public function get_query_posts() {
		$settings            = $this->get_settings();
		$args                = Utils::get_query_args( $settings );
		$is_archive_template = ! empty( $settings['is_archive_template'] );

		if ( is_archive() && $is_archive_template ) {
			global $wp_query;

			$args = $wp_query->query_vars;
		}

		// Don't need to collect total rows since this widget doesn't have query based pagination.
		$args['no_found_rows'] = true;

		$new_query = new \WP_Query( $args );

		return $new_query;
	}

	public static function excerpt_more() {
		return '';
	}

	protected function render() {}
}
