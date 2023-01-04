<?php
namespace TheGem_Elementor\DynamicTags;


use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Title_Excerpt extends Tag {
	public function get_name() {
		return 'thegem-custom-title-excerpt';
	}

	public function get_title() {
		return __( 'Page/Post Excerpt', 'thegem');
	}

	public function get_group() {
		return 'thegem-title';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	public function render() {
		global $thegem_page_title_template_data;
		$page_data = $thegem_page_title_template_data;
		echo !empty($page_data['title_excerpt']) ? $page_data['title_excerpt'] : 'Page/Post Excerpt';
	}
}
