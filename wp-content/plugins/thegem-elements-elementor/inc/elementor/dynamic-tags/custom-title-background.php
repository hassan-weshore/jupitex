<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Title_Background extends Data_Tag {

	public function get_name() {
		return 'thegem-custom-title-background';
	}

	public function get_group() {
		return 'thegem-title';
	}

	public function get_categories() {
		return [ TagsModule::IMAGE_CATEGORY ];
	}

	public function get_title() {
		return __( 'Background Image (set in Page Options)', 'thegem' );
	}

	public function get_value( array $options = [] ) {
		global $thegem_page_title_template_data;
		$background_image = array('url' => Utils::get_placeholder_image_src());
		if($thegem_page_title_template_data['title_use_page_settings']) {
			$page_data = $thegem_page_title_template_data;
			$background_image = array('url' => $page_data['title_background_image'] ? $page_data['title_background_image'] : Utils::get_placeholder_image_src());
		}
		return $background_image;
	}
}
