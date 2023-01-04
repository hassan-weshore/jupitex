<?php

namespace Sellkit\Admin\Funnel\Importer\Page_Builder;

use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;

defined( 'ABSPATH' ) || die();

/**
 * Class Elementor.
 *
 * @since 1.1.0
 */
class Elementor_Importer extends Source_Local {

	/**
	 * Elementor_Importer constructor.
	 *
	 * @since 1.1.0
	 * @param object $data All data from api.
	 * @param int    $step_id The id of imported step.
	 */
	public function __construct( $data, $step_id ) {
		parent::__construct();

		$meta_data = (array) $data->meta;

		$this->import_single_template( $step_id, $meta_data['_elementor_data'] );
	}

	/**
	 * Imports single template.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $content Content.
	 */
	public function import_single_template( $post_id, $content ) {
		if ( empty( $content ) ) {
			return;
		}

		if ( ! is_array( $content ) ) {
			$content = add_magic_quotes( $content );
			$content = json_decode( $content, true );
		}

		if ( is_array( $content ) ) {
			$content = $this->process_export_import_content( $content, 'on_import' );

			update_post_meta( $post_id, '_elementor_data', $content );

			$this->clear_elementor_cache();
		}
	}

	/**
	 * Clear Cache.
	 *
	 * @since 1.0.0
	 */
	public function clear_elementor_cache() {
		// Clear 'Elementor' file cache.
		if ( class_exists( '\Elementor\Plugin' ) ) {
			Plugin::$instance->files_manager->clear_cache();
		}
	}
}
