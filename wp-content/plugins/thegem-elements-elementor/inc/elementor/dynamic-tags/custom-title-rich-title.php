<?php
namespace TheGem_Elementor\DynamicTags;


use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Title_Rich_Title extends Tag {
	public function get_name() {
		return 'thegem-custom-title-rich-title';
	}

	public function get_title() {
		return __( 'Page/Post Rich Content Title', 'thegem');
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
		$type = $page_data['title_rich_content'] ? 'rich' : 'simple';
		$text = !empty($thegem_page_title_template_data['main_title']) ? $thegem_page_title_template_data['main_title'] : '';
		$content = $page_data['title_content'];

		$output = '';
		if($type == 'simple') {
			$output .= '<h1>';
		} else {
			$output .= '<div class="custom-title-rich">';
		}
		if($type == 'simple' && !empty($text)) {
			$output .= $text;
		} elseif($type == 'rich') {
			$output .= do_shortcode($content);
		} else {
			$output .= 'Page/Post Rich Content Title';
		}
		if($type == 'simple') {
			$output .= '</h1>';
		} else {
			$output .= '</div>';
		}
		echo $output;
	}
}
