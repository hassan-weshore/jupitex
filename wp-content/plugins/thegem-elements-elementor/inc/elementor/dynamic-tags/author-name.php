<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Author_Name extends Tag {

	public function get_name() {
		return 'thegem-author-name';
	}

	public function get_title() {
		return __( 'Author Name', 'thegem' );
	}

	public function get_group() {
		return 'thegem';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	public function render() {
		echo wp_kses_post( get_the_author() );
	}
}
