<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Excerpt extends Tag {

	public function get_name() {
		return 'thegem-post-excerpt';
	}

	public function get_title() {
		return __( 'Page/Post Excerpt', 'thegem' );
	}

	public function get_group() {
		return 'thegem';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	public function render() {
		// Allow only a real `post_excerpt` and not the trimmed `post_content` from the `get_the_excerpt` filter
		$post = get_post();

		if ( ! $post || empty( $post->post_excerpt ) ) {
			return;
		}

		echo wp_kses_post( $post->post_excerpt );
	}
}
