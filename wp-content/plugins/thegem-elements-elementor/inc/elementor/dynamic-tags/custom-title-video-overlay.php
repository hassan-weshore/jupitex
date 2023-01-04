<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Title_Video_Overlay extends Tag {

	public function get_name() {
		return 'thegem-custom-title-video-overlay';
	}

	public function get_title() {
		return __( 'Background Video Overlay (set in Page Options)', 'thegem' );
	}

	public function get_group() {
		return 'thegem-title';
	}

	public function get_categories() {
		return [
			TagsModule::COLOR_CATEGORY,
		];
	}

	public function render() {
		global $thegem_page_title_template_data;
		$color = '';
		if($thegem_page_title_template_data['title_use_page_settings']) {
			$page_data = $thegem_page_title_template_data;
			$color = $page_data['title_background_video_overlay'] ? $page_data['title_background_video_overlay'] : '';
		}
		echo $color;
	}
}
