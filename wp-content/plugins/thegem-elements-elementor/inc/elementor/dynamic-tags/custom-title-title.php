<?php
namespace TheGem_Elementor\DynamicTags;


use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Title_Title extends Tag {
	public function get_name() {
		return 'thegem-custom-title-title';
	}

	public function get_title() {
		return __( 'Page/Post Title', 'thegem');
	}

	public function get_group() {
		return 'thegem-title';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	public function render() {
		global $thegem_page_title_template_data;
		echo !empty($thegem_page_title_template_data['main_title']) ? $thegem_page_title_template_data['main_title'] : 'Page/Post Title';
	}
}
