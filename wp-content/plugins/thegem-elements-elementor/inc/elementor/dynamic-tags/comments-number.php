<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Comments_Number extends Tag {

	public function get_name() {
		return 'thegem-comments-number';
	}

	public function get_title() {
		return __( 'Comments Number', 'thegem' );
	}

	public function get_group() {
		return 'thegem';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	protected function _register_controls() {
		$this->add_control(
			'format_no_comments',
			[
				'label' => __( 'No Comments Format', 'thegem' ),
				'default' => __( 'No Responses', 'thegem' ),
			]
		);

		$this->add_control(
			'format_one_comments',
			[
				'label' => __( 'One Comment Format', 'thegem' ),
				'default' => __( 'One Response', 'thegem' ),
			]
		);

		$this->add_control(
			'format_many_comments',
			[
				'label' => __( 'Many Comment Format', 'thegem' ),
				'default' => __( '{number} Responses', 'thegem' ),
			]
		);

		$this->add_control(
			'link_to',
			[
				'label' => __( 'Link', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'thegem' ),
					'comments_link' => __( 'Comments Link', 'thegem' ),
				],
			]
		);
	}

	public function render() {
		$settings = $this->get_settings();

		$comments_number = get_comments_number();

		if ( ! $comments_number ) {
			$count = $settings['format_no_comments'];
		} elseif ( 1 === $comments_number ) {
			$count = $settings['format_one_comments'];
		} else {
			$count = strtr( $settings['format_many_comments'], [
				'{number}' => number_format_i18n( $comments_number ),
			] );
		}

		if ( 'comments_link' === $this->get_settings( 'link_to' ) ) {
			$count = sprintf( '<a href="%s">%s</a>', get_comments_link(), $count );
		}

		echo wp_kses_post( $count );
	}
}
