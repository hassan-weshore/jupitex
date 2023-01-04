<?php 


class ThegemImporterElementorImportImages extends Elementor\TemplateLibrary\Classes\Import_Images {
	public function import( $attachment, $parent_post_id = null ) {
		if (empty($attachment['id'])) {
			static $id = 0;
			$attachment['id'] = --$id;
		}

		return parent::import( $attachment, $parent_post_id);
	}
}


class ThegemImporterElementorTemplateManager extends Elementor\TemplateLibrary\Manager {
	private $_import_images;


	public function get_import_images_instance() {
		if ( null === $this->_import_images ) {
			$this->_import_images = new ThegemImporterElementorImportImages();
		}

		return $this->_import_images;
	}
}


function thegem_init_elementor_importer() {
	static $initialized = false;

	if (!$initialized) {
		\Elementor\Plugin::$instance->templates_manager = new ThegemImporterElementorTemplateManager();
		$initialized = true;
	}
}
