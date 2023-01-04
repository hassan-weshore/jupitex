<?php
namespace Jet_Popup\Render;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Block_Editor_Content_Render extends Base_Render {

	/**
	 * [$name description]
	 * @var string
	 */
	protected $name = 'block-editor-template-render';

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function init() {}

	/**
	 * [get_name description]
	 * @return [type] [description]
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * [render description]
	 * @return [type] [description]
	 */
	public function render() {
		$popup_id = $this->get( 'popup_id' );

		$template_obj = get_post( $popup_id );
		$raw_template_content = $template_obj->post_content;

		if ( empty( $raw_template_content ) ) {
			return false;
		}

		$blocks_template_content = apply_filters( 'jet-popup/render/block-editor/content', do_blocks( $raw_template_content ), $popup_id ) ;

		$this->maybe_enqueue_css();

		echo do_shortcode( $blocks_template_content );

	}

	/**
	 * @return array
	 */
	public function get_render_data() {

		$popup_id = $this->get( 'popup_id', false );
		$popup_id = apply_filters( 'jet-popup/popup-generator/before-define-popup-assets/popup-id', $popup_id, $this->get_settings() );

		$template_scripts = [];
		$template_styles = [];

		$render_data = [
			'content' => $this->get_content(),
			'scripts' => $template_scripts,
			'styles'  => $template_styles,
		];

		return apply_filters( 'jet-plugins/render/render-data', $render_data, $popup_id, [], 'default' );
	}

	/**
	 * [render description]
	 * @return [type] [description]
	 */
	public function maybe_enqueue_css() {
		
		if ( ! class_exists( '\JET_SM\Gutenberg\Style_Manager' ) ) {
			return;
		}

		$popup_id = $this->get( 'popup_id' );
		
		\JET_SM\Gutenberg\Style_Manager::get_instance()->render_blocks_style( $popup_id );
		\JET_SM\Gutenberg\Style_Manager::get_instance()->render_blocks_fonts( $popup_id );

	}
}
