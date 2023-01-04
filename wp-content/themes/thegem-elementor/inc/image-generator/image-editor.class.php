<?php

require_once ABSPATH . WPINC . '/class-wp-image-editor.php';

class TheGem_Dummy_WP_Image_Editor extends WP_Image_Editor {

	public function load() {
	}

	public function save( $destfilename = null, $mime_type = null ) {
	}

	public function resize( $max_w, $max_h, $crop = false ) {
	}

	public function multi_resize( $sizes ) {
	}

	public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false ) {
	}

	public function rotate( $angle ) {
	}

	public function flip( $horz, $vert ) {
	}

	public function stream( $mime_type = null ) {
	}

	public function set_file( $file ) {
		$this->file = $file;
	}

	public function get_size() {
		$implementation = $this->get_image_editor_implementation();

		if (stripos($implementation, 'Imagick') !== false) {
			$imagick = new Imagick($this->file);
			return $imagick->getImageGeometry();
		}

		if (stripos($implementation, 'GD') !== false) {
			$size = @getimagesize( $this->file );
			if (!$size) {
				return false;
			}
			return array(
				'width' => $size[0],
				'height' => $size[1]
			);
		}

		$image_editor = wp_get_image_editor($this->file);
		if (!is_wp_error($image_editor)) {
			return $image_editor->get_size();
		}

		return false;
	}

	function get_image_editor_implementation() {
		$args = array(
			'path' => $this->file
		);

		if ( ! isset( $args['mime_type'] ) ) {
			$file_info = wp_check_filetype( $args['path'] );
			if ( isset( $file_info ) && $file_info['type'] )
				$args['mime_type'] = $file_info['type'];
		}

		return _wp_image_editor_choose( $args );
	}
}

?>
