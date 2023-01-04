<?php

use Elementor\Controls_Stack;
use Elementor\Plugin;
use Elementor\Core\Settings\Manager as SettingsManager;

/**
 * Process content for export/import.
 *
 * Process the content and all the inner elements, and prepare all the
 * elements data for export/import.
 *
 * @since 1.5.0
 * @access protected
 *
 * @param array  $content A set of elements.
 * @param string $method  Accepts either `on_export` to export data or
 *                        `on_import` to import data.
 *
 * @return mixed Processed content data.
 */
function thegem_importer_process_export_import_content($post_id, $content, $method ) {
	// write_log('process json by elementor '.$content);
	$obj = json_decode($content, true);

	if ($obj) {
		$obj = Plugin::$instance->db->iterate_data(
			$obj, function( $element_data ) use ( $method ) {
				$element = Plugin::$instance->elements_manager->create_element_instance( $element_data );

				// If the widget/element isn't exist, like a plugin that creates a widget but deactivated
				if ( ! $element ) {
					return null;
				}

				return thegem_importer_process_element_export_import_content( $element, $method );
			}
		);

		$content = json_encode($obj);
		// write_log('processed json by elementor '.$content);
	}

	return $content;
}

/**
 * Process single element content for export/import.
 *
 * Process any given element and prepare the element data for export/import.
 *
 * @since 1.5.0
 * @access protected
 *
 * @param Controls_Stack $element
 * @param string         $method
 *
 * @return array Processed element data.
 */
function thegem_importer_process_element_export_import_content( Controls_Stack $element, $method ) {
	$element_data = $element->get_data();

	//write_log('process element');

	if ( method_exists( $element, $method ) ) {
		// TODO: Use the internal element data without parameters.
		$element_data = $element->{$method}( $element_data );
	}

	foreach ( $element->get_controls() as $control ) {
		$control_class = Plugin::$instance->controls_manager->get_control( $control['type'] );

		// If the control isn't exist, like a plugin that creates the control but deactivated.
		if ( ! $control_class ) {
			return $element_data;
		}

		if ( method_exists( $control_class, $method ) ) {
			//write_log("process control ".json_encode($element_data['settings'][ $control['name'] ]));

			$element_data['settings'][ $control['name'] ] = $control_class->{$method}( $element->get_settings( $control['name'] ), $control );

			//write_log("processed control ".json_encode($element_data['settings'][ $control['name'] ]));
		}

		// On Export, check if the control has an argument 'export' => false.
		if ( 'on_export' === $method && isset( $control['export'] ) && false === $control['export'] ) {
			unset( $element_data['settings'][ $control['name'] ] );
		}
	}

	return $element_data;
}

function thegem_importer_get_last_post_id() {
	global $wpdb;
	
	$result = $wpdb->get_results("SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1");
    $row = $result[0];
    $id = $row->ID;

	return $id;
}

function thegem_importer_media_add_suffix($lastPostId) {
	global $wpdb;
	if ($lastPostId>0) {
		$wpdb->query("update $wpdb->posts set post_title=concat(post_title,' (Demo)') where ID>".esc_sql($lastPostId));
	}
}

function thegem_importer_process_elementor_data($post_id, $value) {
	$lastPostId = thegem_importer_get_last_post_id();
	write_log('process elementor data '.$post_id.$value);
	$res = thegem_importer_process_export_import_content($post_id, $value, 'on_import' );
	thegem_importer_media_add_suffix($lastPostId);
	return $res;
}

function thegem_importer_process_elementor_page_settings($post_id, $value) {
	$lastPostId = thegem_importer_get_last_post_id();
    $page = SettingsManager::get_settings_managers( 'page' )->get_model( $post_id );

    $page_settings_data = thegem_importer_process_element_export_import_content( $page, 'on_import' );
	thegem_importer_media_add_suffix($lastPostId);

    if ( !empty( $page_settings_data['settings'] ) ) {
        return $page_settings_data['settings'];
    }

    return null;
}
