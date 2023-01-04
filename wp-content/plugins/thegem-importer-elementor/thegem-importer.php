<?php

/*
Plugin Name: TheGem Demo Import (for Elementor)
Plugin URI: http://codex-themes.com/thegem/
Author: Codex Themes
Version: 5.0.3
Author URI: http://codex-themes.com/thegem/
Elementor tested up to: 3.2.3
*/


if (!is_admin()) {
	return;
}


include_once ('inc/parse_content.php');
include_once ('inc/process_meta.php');
include_once ('inc/import-widgets.php');
include_once ('inc/easy-mailchimp-import.php');


$thegem_importer_work_time = 20;
$thegem_importer_end_work_time = time() + $thegem_importer_work_time;
function thegem_importer_time_ended() {
	global $thegem_importer_end_work_time;
	$time = time();
	write_log('check work time, '.($thegem_importer_end_work_time-$time).' seconds left');
	return time() > $thegem_importer_end_work_time;
}

define ('THEGEM_IMPORT_URL', 'http://democontent.codex-themes.com/sites-democontent-elementor/');
//define ('THEGEM_IMPORT_URL', 'http://localhost/democontent/');


if (!function_exists('write_log')) {
	function write_log($log) {
		if (WP_DEBUG) {
			if (is_array($log) || is_object($log)) {
				trigger_error(print_r($log, true));
			} else {
				trigger_error($log);
			}
		}
/*
		if (WP_DEBUG) {
			$fp = fopen('/var/www/html/wp-content/full-debug.log', 'a');
			if (is_array($log) || is_object($log)) {
				$log = print_r($log, true);
			}

			fwrite($fp, date('Y-m-d H:i:s ').$log."\n\n");  
			fclose($fp);  
		}
*/		
	}
}

register_activation_hook(__FILE__, 'thegem_importer_activate');
add_action('admin_init', 'thegem_importer_activation_redirect');
add_action('admin_init', 'thegem_importer_elementor_template_importer_fixes');

function thegem_importer_elementor_template_importer_fixes() {
	if (is_plugin_active('elementor/elementor.php')) {
		require_once('inc/elementor-template-importer-fixes.php');
	}	
}

function thegem_importer_activate() {
	add_option('thegem_importer_do_activation_redirect', true);
}
function thegem_importer_activation_redirect() {
	if (get_option('thegem_importer_do_activation_redirect', false)) {
		delete_option('thegem_importer_do_activation_redirect');
		wp_redirect("admin.php?page=thegem-importer");
		exit;
	}
}

function thegem_importer_get_purchase() {
	if(!defined('ENVATO_HOSTED_SITE')) {
		$theme_options = get_option('thegem_theme_options');
		if($theme_options && isset($theme_options['purchase_code'])) {
			return $theme_options['purchase_code'];
		}
	} else {
		return 'envato_hosted:'.(defined('SUBSCRIPTION_CODE') ? SUBSCRIPTION_CODE : '');
	}
	return false;
}

if(!function_exists('thegem_is_plugin_active')) {
	function thegem_is_plugin_active($plugin) {
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		return is_plugin_active($plugin);
	}
}

add_action('admin_menu', 'thegem_importer_submenu_page', 20);
function thegem_importer_submenu_page() {
	add_submenu_page('thegem-theme-options', 'TheGem Import', 'Demo Import', 'manage_options', 'thegem-importer', 'thegem_importer_page');
}
add_action('admin_menu', 'thegem_importer_submenu_page_new', 30);
function thegem_importer_submenu_page_new() {
	add_submenu_page('thegem-dashboard-welcome', 'TheGem Import', 'Demo Import', 'manage_options', 'thegem-importer', 'thegem_importer_page');
}

function thegem_importer_scripts($hook) {
	if($hook == 'thegem_page_thegem-importer' || $hook == 'admin_page_thegem-importer') {
		wp_enqueue_script('thegem-importer-scripts', plugins_url( '/js/ti-scripts.js' , __FILE__ ), array('jquery', 'jquery-fancybox', 'jquery-ui-autocomplete'), false, true);
		wp_localize_script('thegem-importer-scripts', 'thegem_importer_data', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'ajax_error_msg' => '<p class="error-message">'.__('Ajax error. Try again...', 'thegem-importer').'</p>',
			'ajax_error_content' => thegem_importer_error_content('ajax'),
			'get_imports_list_msg' => '<p class="loading-text">'.__('Getting imports list...', 'thegem-importer').'</p>',
			'load_import_step_1_msg' => '<p class="loading-text">'.__('Getting import data...', 'thegem-importer').'</p>',
			'load_import_step_2_msg' => '<p class="loading-text">'.__('Getting import data...', 'thegem-importer').'</p>',
			'load_import_step_3_msg' => '<p class="loading-text">'.__('Preparing import data...', 'thegem-importer').'</p>',
			'load_import_step_4_msg' => '<p class="loading-text">'.__('Prepare to finish...', 'thegem-importer').'</p>',
			'load_import_step_finalize_msg' => '<p class="loading-text">'.__('Finalizing import...', 'thegem-importer').'</p>',
			'load_import_step_css_msg' => '<p class="loading-text">'.__('Generating CSS file...', 'thegem-importer').'</p>',
			'load_remove_demo_msg' => '<p class="loading-text">'.__('Removing demo content...', 'thegem-importer').'</p>',
		));
		wp_enqueue_style('thegem-import-css', plugins_url( '/css/ti-styles.css' , __FILE__ ), 'jquery-fancybox');
	}
}
add_action('admin_enqueue_scripts', 'thegem_importer_scripts');

function thegem_importer_page() {
	global $thegemThemeOptions;

	if (isset($thegemThemeOptions)) {
		$thegemThemeOptions->prePageWrapper('thegem-importer');
	}
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
	$wp_upload_dir = wp_upload_dir();
	$importer_dir = $wp_upload_dir['basedir'] . '/thegem-importer';
	WP_Filesystem();
	$fs = new WP_Filesystem_Direct(false);
	$fs->rmdir($importer_dir, true);
	$extensions = array();
	foreach (get_loaded_extensions() as $extension) {
		$extensions[$extension] = true;
	}
?>
<div class="wrap">
	<div class="thegem-importer-header">
		<div class="thegem-importer-logo"><img src="<?php echo plugins_url( '/images/logo.png' , __FILE__ ) ?>" alt="TheGem Import"></div>
		<div class="thegem-importer-title"><?php _e('Import of pre-built demos', 'thegem-importer'); ?></div>
		<div class="thegem-importer-remover"><a href="#"><?php _e('Remove demo content', 'thegem-importer'); ?></a></div>
	</div>
	<div class="thegem-importer-content">
	<?php if(thegem_is_plugin_active('wordpress-importer/wordpress-importer.php')) : ?>
		<p class="error-message"><?php printf(__('It seems that Wordpress Import Plugin is active. Please deactivate Wordpress Import Plugin on <a href="%s">plugins page</a> to proceed with import of TheGem\'s main demo content.', 'thegem-importer'), admin_url('plugins.php')); ?></p>
	<?php elseif(get_template() != 'thegem-elementor') : ?>
		<p class="error-message"><?php _e('Your current activated theme in not TheGem main parent theme. Please note, that this import works only with TheGem main parent theme. Please activate TheGem main parent theme before proceeding with import.', 'thegem-importer'); ?></p>
	<?php elseif(!thegem_is_plugin_active('thegem-elements-elementor/thegem-elements-elementor.php')) : ?>
		<p class="error-message"><?php _e('Plugin "TheGem Theme Elements" is not active.', 'thegem-importer'); ?></p>
	<?php elseif(!isset($extensions['gd']) && !isset($extensions['imagick']) || !isset($extensions['xmlreader'])) : ?>
		<p class="error-message"><?php _e('Following PHP extensions are required: GD or ImageMagick, XMLReader. Please contact your server administrator to enable this extensions.', 'thegem-importer'); ?></p>
	<?php elseif(!thegem_importer_get_purchase()) : ?>
		<?php if(!defined('ENVATO_HOSTED_SITE')) : ?>
			<p class="error-message"><?php printf(__('Please enter purchase code in <a href="%s">Theme Options</a>', 'thegem-importer'), admin_url('admin.php?page=thegem-theme-options#activation')); ?></p>
		<?php endif; ?>
	</div>
	<?php else : ?>
		<p class="loading-text"><?php _e('Checking purchase code...', 'thegem-importer'); ?></p>
	<?php endif; ?>
	</div>
</div>
<?php
	if (isset($thegemThemeOptions)) {
		$thegemThemeOptions->postPageWrapper('thegem-importer');
	}
}

add_action('wp_ajax_thegem_importer_check_purchase_code', 'thegem_importer_check_purchase_code');
function thegem_importer_check_purchase_code () {
	$response_p = wp_remote_get(add_query_arg(array('code' => thegem_importer_get_purchase(), 'site_url' => get_site_url(), 'type' => 'elementor'), 'http://democontent.codex-themes.com/av_validate_code'.(defined('ENVATO_HOSTED_SITE') ? '_envato' : '').'.php'), array('timeout' => 20));
	if(!is_wp_error($response_p)) {
		$rp_data = json_decode($response_p['body'], true);
		if(is_array($rp_data) && isset($rp_data['result']) && $rp_data['result'] && isset($rp_data['item_id']) && $rp_data['item_id'] === '16061685') {
			echo json_encode(array('status' => 200, 'content' => '<p>'.__('The purchase code is confirmed.', 'thegem-importer').'</p>'));
		} else {
			if(!defined('ENVATO_HOSTED_SITE')) {
					echo json_encode(array('status' => 0, 'content' => '<p class="error-message">'.sprintf(__('Purchase code verification failed. <a href="%s">Activate TheGem</a>', 'thegem-importer'), admin_url('admin.php?page=thegem-theme-options#activation')).'</p>'));
			} else {
				echo json_encode(array('status' => 0, 'content' => '<p class="error-message">'.__('Verification failed.', 'thegem-importer').'</p>'));
			}
		}
	} else {
		echo json_encode(array('status' => 0, 'content' => thegem_importer_error_content('connecting')));
	}
	die(-1);
}

add_action('wp_ajax_thegem_importer_get_imports_list', 'thegem_importer_get_imports_list');
function thegem_importer_get_imports_list () {
	$response = wp_remote_get(esc_url(THEGEM_IMPORT_URL.'imports.php'), array('timeout' => 20));
	if(!is_wp_error($response)) {
		$rp_data = json_decode($response['body'], true);
		if(is_array($rp_data)) {
			update_option('thegem_importer_imports_data', $rp_data);
			ob_start();
			$categories = array();
			$other_categories = array();
			$keywords = array();
			foreach($rp_data as $id => $data) {
				if(is_array($data)) {
					if(!empty($data['category']) && is_string($data['category'])) {
						if(strpos($data['category'], 'Other/') === 0 && !in_array(str_replace('Other/','',$data['category']), $other_categories)) {
							$other_categories[] = str_replace('Other/','',$data['category']);
						} elseif(strpos($data['category'], 'Other/') === false && !in_array($data['category'], $categories)) {
							$categories[] = $data['category'];
						}
					}
					if(!empty($data['keywords']) && is_string($data['keywords'])) {
						$keywords[] = $data['keywords'];
					}
				}
			}
			$keywords = implode(' ', $keywords);
			$keywords = array_unique(explode(' ', $keywords));
?>
<div class="imports-filter">
	<div class="imports-search">
		<form class="imports-search-form">
			<input class="autocomplete-field" type="text" placeholder="<?php echo esc_attr(__('Search website...', 'thegem-importer')); ?>"<?php echo (!empty($keywords) ? ' data-keywords="'.esc_attr(implode(' ', $keywords)).'"': ''); ?>>
			<button type="submit"><?php _e('Search', 'thegem-importer') ?></button>
		</form>
	</div>
	<div class="imports-categories">
		<div class="imports-category imports-category-all">
			<a href="javascript:void();" data-category="all" class="active"><?php _e('All', 'thegem-importer') ?></a>
		</div>
		<?php foreach($categories as $category) : ?>
			<div class="imports-category">
				<a href="javascript:void();" data-category="<?php echo sanitize_title($category); ?>"><?php echo $category; ?></a>
			</div>
		<?php endforeach; ?>
		<div class="imports-category imports-category-other">
			<a href="javascript:void();" data-category="other">
				<span class="import-line-1"></span>
				<span class="import-line-2"></span>
				<span class="import-line-3"></span>
				<?php _e('Other', 'thegem-importer') ?>
			</a>
			<div class="imports-category-other-list">
				<a href="javascript:void();" class="imports-category-other-close">
					<span class="import-line-1"></span>
					<span class="import-line-3"></span>
				</a>
				<div class="imports-category-other-list-inner">
					<?php foreach($other_categories as $category) : ?>
						<div class="imports-category">
							<a href="javascript:void();" data-category="<?php echo sanitize_title($category); ?>"><?php echo $category; ?></a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="imports-list">
	<?php foreach($rp_data as $id => $data) : ?>
		<?php if(is_array($data)) : ?>
			<div class="import-item <?php echo esc_attr($id); ?>"<?php echo (!empty($data['category']) && is_string($data['category']) ? ' data-category="'.sanitize_title(str_replace('Other/','',$data['category'])).'"': ''); ?><?php echo (!empty($data['keywords']) && is_string($data['keywords']) ? ' data-keywords="'.esc_attr($data['keywords']).'"': ''); ?>>
				<div class="import-item-wrap">
					<?php if(!empty($data['image'])) : ?>
						<div class="import-image"><img src="<?php echo esc_url($data['image']); ?>" alt="<?php echo esc_attr($id); ?>"></div>
					<?php endif; ?>
					<div class="import-description">
						<?php if(!empty($data['title'])) : ?>
							<div class="import-title"><?php echo $data['title']; ?><?php echo !empty($data['marker']) ? $data['marker'] : ''; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<a href="#" class="import-link" data-import="<?php echo esc_attr($id); ?>"></a>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
<?php
			$content = ob_get_clean();
			echo json_encode(array('status' => 200, 'content' => $content));
		}
	} else {
		echo json_encode(array('status' => 0, 'content' => thegem_importer_error_content('connecting')));
	}
	die(-1);
}

add_action('wp_ajax_thegem_importer_get_import_step_1', 'thegem_importer_get_import_step_1');
function thegem_importer_get_import_step_1() {
	$imports_data = get_option('thegem_importer_imports_data');
	$new_prefix = '';
	if(!empty($imports_data[$_REQUEST['import']]['updated_full'])) {
		$new_prefix = '_updated/';
	}
	$response = wp_remote_get(esc_url(THEGEM_IMPORT_URL.$new_prefix.$_REQUEST['import'].'/import.json'), array('timeout' => 20));
	if(!is_wp_error($response)) {
		$rp_data = json_decode($response['body'], true);
		if(is_array($rp_data)) {
			ob_start();
			$import_data = $rp_data;
			$imports_data = get_option('thegem_importer_imports_data');
			$import_data['image'] = esc_url(THEGEM_IMPORT_URL.$_REQUEST['import'].'/screenshot.jpg');
			$sliders = $import_data['sliders'];
			foreach($sliders as $alias => $filename) {
				$sliders[$alias] = esc_url(THEGEM_IMPORT_URL.$new_prefix.$_REQUEST['import'].'/sliders/'.$filename);
			}
			$import_data['sliders'] = $sliders;
			update_option('thegem_importer_data', $import_data);
			$status = 200;
?>
<div class="thegem-importer-wrap thegem-importer-step-1">
	<?php if($import_data['image'] && $import_data['title']) : ?>
		<div class="import-popup-title">
			<div class="import-image"><img src="<?php echo esc_url($import_data['image']); ?>" alt="<?php echo esc_attr($import_data['title']); ?>"><?php if(!empty($imports_data[$_REQUEST['import']]['link'])) : ?><a href="<?php echo esc_url($imports_data[$_REQUEST['import']]['link']); ?>" class="import-popup-preview-link" target="_blank"><span><?php _e('Preview Pre-built Website', 'thegem-importer'); ?></span></a><?php endif; ?></div>
			<div class="import-title"><?php echo $import_data['title']; ?></div>
		</div>
	<?php endif; ?>
	<?php if(!empty($imports_data[$_REQUEST['import']]['woocommerce']) && empty($_REQUEST['ignore_plugins']) && (!function_exists('WC') || !defined( 'YITH_WCWL' ))) : $status = 100; ?>
		<div class="import-select-warning"><?php _e('Please install following plugins before<br>importing this demo:', 'thegem-importer'); ?></div>
		<div class="import-select-woocommerce-required-table">
			<div class="required-table-row">
				<div class="required-table-title"><span class="plugin-name"><?php _e('WooCommerce', 'thegem-importer'); ?></span><span class="required-plugin"><?php _e('(required)', 'thegem-importer'); ?></span></div>
				<div class="required-table-button">
					<?php if(!function_exists('WC')) : ?>
						<a href="<?php echo admin_url('plugin-install.php?s=woocommerce&tab=search&type=term'); ?>" target="_blank" class="plugin-install"><?php _e('Install', 'thegem-importer'); ?></a>
					<?php else : ?>
						<span class="plugin-installed"><?php _e('Installed', 'thegem-importer'); ?></span>
					<?php endif; ?>
				</div>
			</div>
			<div class="required-table-row">
				<div class="required-table-title"><span class="plugin-name"><?php _e('YITH Wishlist', 'thegem-importer'); ?></span><span class="recommended-plugin"><?php _e('(recommended)', 'thegem-importer'); ?></span></div>
				<div class="required-table-button">
					<?php if(!defined( 'YITH_WCWL' )) : ?>
						<a href="<?php echo admin_url('plugin-install.php?s=yith-woocommerce-wishlist&tab=search&type=term'); ?>" target="_blank" class="plugin-install"><?php _e('Install', 'thegem-importer'); ?></a>
					<?php else : ?>
						<span class="plugin-installed"><?php _e('Installed', 'thegem-importer'); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="submit-buttons">
			<button type="button" class="cancel-import plugins-cancel-import"><?php _e('Cancel', 'thegem-importer'); ?></button>
			<?php if(function_exists('WC')) : ?>
				<button type="button" class="plugins-proceed-import"><?php _e('Proceed', 'thegem-importer'); ?></button>
			<?php endif; ?>
		</div>
	<?php elseif(!empty($imports_data[$_REQUEST['import']]['required_child']) && get_stylesheet() != $imports_data[$_REQUEST['import']]['required_child']) : ?>
		<div class="import-select-title"><?php _e('Warning', 'thegem-importer'); ?></div>
		<div class="import-select-warning">
			<?php if(!empty($imports_data[$_REQUEST['import']]['required_child_message'])) : ?>
				<?php echo $imports_data[$_REQUEST['import']]['required_child_message']; ?>
			<?php else : ?>
				<?php printf(__('%s child theme is required. Please install it.', 'thegem-importer'),$imports_data[$_REQUEST['import']]['required_child']); ?>
			<?php endif; ?>
		</div>
		<div class="submit-buttons">
			<button type="button" class="cancel-import"><?php _e('Cancel', 'thegem-importer'); ?></button>
			<a href="<?php echo admin_url('theme-install.php'); ?>" class="submit-import"><?php _e('Install Theme', 'thegem-importer'); ?></a>
		</div>
	<?php else : ?>
		<div class="import-select-title"><?php _e('Installation Options', 'thegem-importer'); ?></div>
		<form class="import-select-type">
			<input type="hidden" name="import" value="<?php echo esc_attr($_REQUEST['import']); ?>" />
			<div class="import-type-wrap active">
				<div class="field"><label><input type="radio" name="import_type" value="full" checked="checked"> <?php _e('Install complete pre-built website', 'thegem-importer'); ?></label></div>
				<div class="description"><?php _e('<span class="note">Note:</span> This will automatically replace your menus and homepages', 'thegem-importer'); ?></div>
			</div>
			<div class="import-type-wrap">
				<div class="field"><label><input type="radio" name="import_type" value="part"> <?php _e('Install single pages from pre-built website', 'thegem-importer'); ?></label></div>
				<div class="description"><?php _e('Here you can select single pages from pre-built demo website to be imported', 'thegem-importer'); ?></div>
			</div>
			<div class="submit-buttons">
				<button type="button" class="cancel-import"><?php _e('Cancel', 'thegem-importer'); ?></button>
				<button type="submit" class="submit-import"><?php _e('Next Step', 'thegem-importer'); ?></button>
			</div>
		</form>
	<?php endif; ?>
</div>
<?php
			$content = ob_get_clean();
			echo json_encode(array('status' => $status, 'content' => $content));
		} else {
			echo json_encode(array('status' => 0, 'content' => thegem_importer_error_content('data')));
		}
	} else {
		echo json_encode(array('status' => 0, 'content' => thegem_importer_error_content('connecting')));
	}
	die(-1);
}

add_action('wp_ajax_thegem_importer_check_plugins', 'thegem_importer_check_plugins');
function thegem_importer_check_plugins() {
	$import_data = get_option('thegem_importer_data');
	if(is_array($import_data)) {
		$status =200;
		if(!empty($import_data['woocommerce']) && (!function_exists('WC') || !defined( 'YITH_WCWL' ))) {
			$status = 100;
		}
		ob_start();
?>
<div class="import-select-woocommerce-required-table">
	<div class="required-table-row">
		<div class="required-table-title"><span class="plugin-name"><?php _e('WooCommerce', 'thegem-importer'); ?></span><span class="required-plugin"><?php _e('(required)', 'thegem-importer'); ?></span></div>
		<div class="required-table-button">
			<?php if(!function_exists('WC')) : ?>
				<a href="<?php echo admin_url('plugin-install.php?s=woocommerce&tab=search&type=term'); ?>" target="_blank" class="plugin-install"><?php _e('Install', 'thegem-importer'); ?></a>
			<?php else : ?>
				<span class="plugin-installed"><?php _e('Installed', 'thegem-importer'); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<div class="required-table-row">
		<div class="required-table-title"><span class="plugin-name"><?php _e('YITH Wishlist', 'thegem-importer'); ?></span><span class="recommended-plugin"><?php _e('(recommended)', 'thegem-importer'); ?></span></div>
		<div class="required-table-button">
			<?php if(!defined( 'YITH_WCWL' )) : ?>
				<a href="<?php echo admin_url('plugin-install.php?s=yith-woocommerce-wishlist&tab=search&type=term'); ?>" target="_blank" class="plugin-install"><?php _e('Install', 'thegem-importer'); ?></a>
			<?php else : ?>
				<span class="plugin-installed"><?php _e('Installed', 'thegem-importer'); ?></span>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
	$content_table = ob_get_clean();
	ob_start();
?>
		<div class="submit-buttons">
			<button type="button" class="cancel-import plugins-cancel-import"><?php _e('Cancel', 'thegem-importer'); ?></button>
			<?php if(function_exists('WC')) : ?>
				<button type="button" class="plugins-proceed-import"><?php _e('Proceed', 'thegem-importer'); ?></button>
			<?php endif; ?>
		</div>
<?php
			$content_buttons = ob_get_clean();
		echo json_encode(array('status' => $status, 'content_table' => $content_table, 'content_buttons' => $content_buttons));
	} else {
		echo json_encode(array('status' => 0, 'content' => thegem_importer_error_content('connecting')));
	}
	die(-1);
}

add_action('wp_ajax_thegem_importer_get_import_step_2', 'thegem_importer_get_import_step_2');
function thegem_importer_get_import_step_2() {
	$import_data = get_option('thegem_importer_data');
	$imports_data = get_option('thegem_importer_imports_data');
	if(is_array($import_data)) {
			ob_start();
?>
<div class="thegem-importer-wrap thegem-importer-step-2">
	<div class="import-select-data-title"><?php _e('Additional import options', 'thegem-importer'); ?></div>
	<form class="import-data-select <?php echo esc_attr($_REQUEST['import_type']); ?>">
		<input type="hidden" name="import" value="<?php echo esc_attr($_REQUEST['import']); ?>" />
		<input type="hidden" name="import_type" value="<?php echo esc_attr($_REQUEST['import_type']); ?>" />
		<div class="basic-data-select">
			<div class="import-data-wrap">
				<div class="field"><label><input type="checkbox" name="import_theme_options" value="1"<?php echo $_REQUEST['import_type'] != 'part' ? ' checked="checked"' : ''; ?>> <?php _e('Import theme options', 'thegem-importer'); ?></label></div>
				<div class="description"<?php echo $_REQUEST['import_type'] == 'part' ? ' style="display: none;"' : ''; ?>><?php printf(__('<span class="note">Note:</span> your current theme options will be overwritten. We recommend to <a href="%s"> backup your theme options.</a>', 'thegem-importer'), admin_url('admin.php?page=thegem-theme-options#backup')); ?></div>
			</div>
			<div class="import-data-wrap">
				<div class="field"><label><input type="checkbox" name="import_rev_sliders" value="1" checked="checked"> <?php _e('Import Revolution sliders', 'thegem-importer'); ?></label></div>
			</div>
			<div class="import-data-wrap">
				<div class="field"><label><input type="checkbox" name="import_media" value="1" checked="checked"> <?php _e('Import media', 'thegem-importer'); ?></label></div>
			</div>
		</div>
		<?php
			if($_REQUEST['import_type'] == 'part') {
				$new_prefix = '';
				if(!empty($imports_data[$_REQUEST['import']]['updated_full'])) {
					$new_prefix = '_updated/';
				}
				$resp_content = wp_remote_get(esc_url(THEGEM_IMPORT_URL.$new_prefix.$_REQUEST['import'].'/content/content_singles.json'), array('timeout' => 20));
				$resp_links = wp_remote_get(esc_url(THEGEM_IMPORT_URL.$new_prefix.$_REQUEST['import'].'/posts_links.json'), array('timeout' => 20));
				if(!is_wp_error($resp_content) && !is_wp_error($resp_links)) {
					$rp_content = json_decode($resp_content['body'], true);
					$rp_links = json_decode($resp_links['body'], true);
					if(is_array($rp_content) && is_array($rp_links)) : ?>
						<div class="part-data-select">
							<?php if(count($rp_content['pages'])) : ?>
								<div class="import-data-part-column">
									<div class="column-title"><?php _e('Pages', 'thegem-importer'); ?></div>
									<div class="column-items">
										<label class="column-title-select-all"><input type="checkbox" name="select_all" value="1" checked="checked"> <?php _e('Select / Deselect All', 'thegem-importer'); ?></label>
										<?php foreach($rp_content['pages'] as $page) : ?>
											<div class="column-group">
												<div class="column-group-parent">
													<div class="field"><label><?php if(!isset($page['children'])) : ?><input type="checkbox" name="import_part_select[]" value="<?php echo esc_attr(implode(',',$page['ids'])); ?>" checked="checked"> <?php endif; ?><?php echo esc_attr($page['title']); ?></label><?php if(!empty($import_data['live_url']) && !isset($page['children'])) : ?> <a target="_blank" href="<?php echo esc_url($rp_links[$page['id']]); ?>"><?php _e('Preview', 'thegem-importer'); ?></a><?php endif; ?></div>
												</div>
												<?php if(isset($page['children'])) : ?>
													<div class="column-group-children">
														<?php foreach($page['children'] as $child) : ?>
															<div class="column-group-child">
																<div class="field"><label><input type="checkbox" name="import_part_select[]" value="<?php echo esc_attr(implode(',',$child['ids'])); ?>" checked="checked"> <?php echo esc_attr($child['title']); ?></label><?php if(!empty($import_data['live_url'])) : ?> <a target="_blank" href="<?php echo esc_url($rp_links[$child['id']]); ?>"><?php _e('Preview', 'thegem-importer'); ?></a><?php endif; ?></div>
																<?php if(isset($child['children'])) : ?>
																	<div class="column-group-children-children">
																		<?php foreach($child['children'] as $child_child) : ?>
																			<div class="column-group-child">
																				<div class="field"><label><input type="checkbox" name="import_part_select[]" value="<?php echo esc_attr(implode(',',$child_child['ids'])); ?>" checked="checked"> <?php echo esc_attr($child_child['title']); ?></label><?php if(!empty($import_data['live_url'])) : ?> <a target="_blank" href="<?php echo esc_url($rp_links[$child_child['id']]); ?>"><?php _e('Preview', 'thegem-importer'); ?></a><?php endif; ?></div>
																			</div>
																		<?php endforeach; ?>
																	</div>
																<?php endif; ?>
															</div>
														<?php endforeach; ?>
													</div>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
							<?php if(count($rp_content['posts'])) : ?>
								<div class="import-data-part-column">
									<div class="column-title"><?php _e('Posts', 'thegem-importer'); ?></div>
									<div class="column-items">
										<label class="column-title-select-all" ><input type="checkbox" name="select_all" value="1" checked="checked"> <?php _e('Select / Deselect All', 'thegem-importer'); ?></label>
										<?php foreach($rp_content['posts'] as $page) : ?>
											<div class="column-group">
												<div class="column-group-parent">
													<div class="field"><label><input type="checkbox" name="import_part_select[]" value="<?php echo esc_attr(implode(',',$page['ids'])); ?>" checked="checked"> <?php echo esc_attr($page['title']); ?></label><?php if(!empty($import_data['live_url'])) : ?> <a target="_blank" href="<?php echo esc_url($rp_links[$page['id']]); ?>"><?php _e('Preview', 'thegem-importer'); ?></a><?php endif; ?></div>
												</div>
												<?php if(isset($page['children'])) : ?>
													<div class="column-group-children">
														<?php foreach($page['children'] as $child) : ?>
															<div class="column-group-child">
																<div class="field"><label><input type="checkbox" name="import_part_select[]" value="<?php echo esc_attr(implode(',',$child['ids'])); ?>" checked="checked"> <?php echo esc_attr($child['title']); ?></label><?php if(!empty($import_data['live_url'])) : ?> <a target="_blank" href="<?php echo esc_url($rp_links[$child['id']]); ?>"><?php _e('Preview', 'thegem-importer'); ?></a><?php endif; ?></div>
															</div>
														<?php endforeach; ?>
													</div>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
							<?php if(count($rp_content['portfolios'])) : ?>
								<div class="import-data-part-column">
									<div class="column-title"><?php _e('Portfolios', 'thegem-importer'); ?></div>
									<label class="column-title-select-all"><input type="checkbox" name="select_all" value="1" checked="checked"> <?php _e('Select / Deselect All', 'thegem-importer'); ?></label>
									<div class="column-items">
										<?php foreach($rp_content['portfolios'] as $page) : ?>
											<div class="column-group">
												<div class="column-group-parent">
													<div class="field"><label><input type="checkbox" name="import_part_select[]" value="<?php echo esc_attr(implode(',',$page['ids'])); ?>" checked="checked"> <?php echo esc_attr($page['title']); ?></label><?php if(!empty($import_data['live_url'])) : ?> <a target="_blank" href="<?php echo esc_url($rp_links[$page['id']]); ?>"><?php _e('Preview', 'thegem-importer'); ?></a><?php endif; ?></div>
												</div>
												<?php if(isset($page['children'])) : ?>
													<div class="column-group-children">
														<?php foreach($page['children'] as $child) : ?>
															<div class="column-group-child">
																<div class="field"><label><input type="checkbox" name="import_part_select[]" value="<?php echo esc_attr(implode(',',$child['ids'])); ?>" checked="checked"> <?php echo esc_attr($child['title']); ?></label><?php if(!empty($import_data['live_url'])) : ?> <a target="_blank" href="<?php echo esc_url($rp_links[$child['id']]); ?>"><?php _e('Preview', 'thegem-importer'); ?></a><?php endif; ?></div>
															</div>
														<?php endforeach; ?>
													</div>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
							<?php if(count($rp_content['products'])) : ?>
								<div class="import-data-part-column">
									<div class="column-title"><?php _e('Products', 'thegem-importer'); ?></div>
									<label class="column-title-select-all" ><input type="checkbox" name="select_all" value="1" checked="checked"> <?php _e('Select / Deselect All', 'thegem-importer'); ?></label>
									<div class="column-items">
										<?php foreach($rp_content['products'] as $page) : ?>
											<div class="column-group">
												<div class="column-group-parent">
													<div class="field"><label><input type="checkbox" name="import_part_select[]" value="<?php echo esc_attr(implode(',',$page['ids'])); ?>" checked="checked"> <?php echo esc_attr($page['title']); ?></label><?php if(!empty($import_data['live_url'])) : ?> <a target="_blank" href="<?php echo esc_url($rp_links[$page['id']]); ?>"><?php _e('Preview', 'thegem-importer'); ?></a><?php endif; ?></div>
												</div>
												<?php if(isset($page['children'])) : ?>
													<div class="column-group-children">
														<?php foreach($page['children'] as $child) : ?>
															<div class="column-group-child">
																<div class="field"><label><input type="checkbox" name="import_part_select[]" value="<?php echo esc_attr(implode(',',$child['ids'])); ?>" checked="checked"> <?php echo esc_attr($child['title']); ?></label><?php if(!empty($import_data['live_url'])) : ?> <a target="_blank" href="<?php echo esc_url($rp_links[$child['id']]); ?>"><?php _e('Preview', 'thegem-importer'); ?></a><?php endif; ?></div>
															</div>
														<?php endforeach; ?>
													</div>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php endif;
				}
			}
		?>
		<div class="submit-buttons">
			<button type="button" class="cancel-import"><?php _e('Cancel', 'thegem-importer'); ?></button>
			<button type="submit" class="submit-import"><?php _e('Submit', 'thegem-importer'); ?></button>
		</div>
	</form>
</div>
<?php
		$content = ob_get_clean();
		echo json_encode(array('status' => 200, 'content' => $content));
	} else {
		echo json_encode(array('status' => 0, 'content' => thegem_importer_error_content('data')));
	}
	die(-1);
}

add_action('wp_ajax_thegem_importer_get_import_step_3', 'thegem_importer_get_import_step_3');
function thegem_importer_get_import_step_3() {
	$import_data = get_option('thegem_importer_data');
	$imports_data = get_option('thegem_importer_imports_data');
	if(is_array($import_data)) {
		ob_start();
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		$wp_upload_dir = wp_upload_dir();
		$importer_dir = $wp_upload_dir['basedir'] . '/thegem-importer';
		if(!wp_mkdir_p($importer_dir)) {
			echo json_encode(array('status' => 0, 'content' => '<p class="error-message">'.__('Can\'t create import directory.', 'thegem-importer').'</p>'));
	die(-1);
}
			$import_selection = array_merge(array(
				'import' => '',
				'import_type' => '',
				'import_part_select' => '',
				'import_theme_options' => 0,
				'import_rev_sliders' => 0,
				'import_media' => 0,
				'site_url' => site_url(),
			), $_REQUEST);
			update_option('thegem_importer_selection', $import_selection);
		wp_remote_get(esc_url(add_query_arg($import_selection, THEGEM_IMPORT_URL.'statistic.php'), null, ''), array('timeout' => 20));
		$new_prefix = '';
		$single_new_prefix = '';
		if(!empty($imports_data[$import_selection['import']]['updated'])) {
			$new_prefix = '_updated/';
			$single_new_prefix = '_updated/';
		}
		if($import_selection['import_type'] == 'part' && empty($imports_data[$import_selection['import']]['updated_full'])) {
			$single_new_prefix = '';
		}
		$content_file_temp = download_url(THEGEM_IMPORT_URL.$single_new_prefix.$import_selection['import'].'/content/content.xml');
		$attachments_file_temp = download_url(THEGEM_IMPORT_URL.$single_new_prefix.$import_selection['import'].'/content/content_attachments.json');
		$options_file_temp = download_url(THEGEM_IMPORT_URL.$new_prefix.$import_selection['import'].'/theme_options.json');
		$page_settings_file_temp = download_url(THEGEM_IMPORT_URL.$new_prefix.$import_selection['import'].'/page_settings.json');
		$additionals_fonts_file_temp = download_url(THEGEM_IMPORT_URL.$new_prefix.$import_selection['import'].'/additionals_fonts.json');
		$menu_file_temp = download_url(THEGEM_IMPORT_URL.$new_prefix.$import_selection['import'].'/content/content_menu.xml');
		$widgets_file_temp = download_url(THEGEM_IMPORT_URL.$new_prefix.$import_selection['import'].'/widgets.json');
		$forms_file_temp = download_url(THEGEM_IMPORT_URL.$single_new_prefix.$import_selection['import'].'/content/content_contact_forms.json');

		if( is_wp_error( $content_file_temp ) || is_wp_error( $attachments_file_temp ) || is_wp_error( $options_file_temp ) || is_wp_error( $page_settings_file_temp ) || is_wp_error( $menu_file_temp ) || is_wp_error( $widgets_file_temp ) || is_wp_error( $forms_file_temp )) {
			echo json_encode(array('status' => 0, 'content' => thegem_importer_error_content( THEGEM_IMPORT_URL.$single_new_prefix.$import_selection['import'].'/content/content.xml '. json_encode($content_file_temp->get_error_messages()) . __('Can\'t download files from demo-content server.', 'thegem-importer'))));
			die(-1);
		} else {
			$content_file = @copy($content_file_temp, $importer_dir.'/content.xml');
			$attachments_file = @copy($attachments_file_temp, $importer_dir.'/content_attachments.json');
			$options_file = @copy($options_file_temp, $importer_dir.'/theme_options.json');
			$page_settings_file = @copy($page_settings_file_temp, $importer_dir.'/page_settings.json');
			$additionals_fonts_file = @copy($additionals_fonts_file_temp, $importer_dir.'/additionals_fonts.json');
			$menu_file = @copy($menu_file_temp, $importer_dir.'/content_menu.xml');
			$widgets_file = @copy($widgets_file_temp, $importer_dir.'/widgets.json');
			$forms_file = @copy($forms_file_temp, $importer_dir.'/content_contact_forms.json');
		}
		unlink($content_file_temp);
		unlink($attachments_file_temp);
		unlink($options_file_temp);
		unlink($additionals_fonts_file_temp);
		unlink($menu_file_temp);
		unlink($widgets_file_temp);
		unlink($forms_file_temp);

		if (! defined('WP_LOAD_IMPORTERS')) define('WP_LOAD_IMPORTERS', true);
		require_once(plugin_dir_path( __FILE__ ) . 'inc/wordpress-importer.php');
		$wp_import = new WP_Import();
		$wp_import->fetch_attachments = true;
		$parse = $wp_import->parse($importer_dir.'/content.xml');

		update_option('thegem_importer_progress', array('current' => 0, 'total' => count($parse['posts'])));

		delete_option('thegem_importer_ids');

		if($import_selection['import_type'] == 'part' && is_array($import_selection['import_part_select'])) {
			$ids = array();
			foreach($import_selection['import_part_select'] as $ids_group) {
				$ids = array_merge($ids, explode(',', $ids_group));
			}
			update_option('thegem_importer_progress', array('current' => 0, 'total' => count($ids)));
			update_option('thegem_importer_ids', $ids);
		}

		$attachments_content = file_get_contents($importer_dir.'/content_attachments.json');
		$attachments_data = json_decode($attachments_content, true);
		update_option('thegem_importer_attachments_data', $attachments_data);

		$forms_content = file_get_contents($importer_dir.'/content_contact_forms.json');
		$forms_data = json_decode($forms_content, true);
		update_option('thegem_importer_forms_data', $forms_data);


		delete_option('thegem_importer_finalize_step');
?>
<div class="thegem-importer-wrap thegem-importer-step-3">
	<?php if($import_data['image'] && $import_data['title']) : ?>
		<div class="import-popup-title">
			<div class="import-image"><img src="<?php echo esc_url($import_data['image']); ?>" alt="<?php echo esc_attr($import_data['title']); ?>"></div>
			<div class="import-title"><?php echo $import_data['title']; ?></div>
		</div>
	<?php endif; ?>
	<div class="import-select-title"><?php _e('Installation in progress', 'thegem-importer'); ?></div>
	<div class="import-progress-bar"><div class="import-progress-bar-line" style="width: 0%;" data-percent="0"></div><div class="import-progress-bar-percents"><span class="number">0</span>%</div></div>
	<?php if($import_selection['import_type'] != 'part') : ?>
			<div class="import-select-data-desription"><?php _e('Installing website and options, please be patient.', 'thegem-importer'); ?></div>
		<?php else : ?>
			<div class="import-select-data-desription"><?php _e('Installing pages and options, please be patient.', 'thegem-importer'); ?></div>
	<?php endif; ?>


	<div class="submit-buttons">
		<button type="button" class="cancel-import"><?php _e('Cancel', 'thegem-importer'); ?></button>
	</div>
</div>
<?php
		$content = ob_get_clean();
		echo json_encode(array('status' => 200, 'content' => $content));
	} else {
		echo json_encode(array('status' => 0, 'content' => thegem_importer_error_content('data')));
	}
	die(-1);
}

add_action('wp_ajax_thegem_importer_process', 'thegem_importer_process');
function thegem_importer_process() {
	$import_selection = get_option('thegem_importer_selection');
	ob_start();
	$wp_upload_dir = wp_upload_dir();
	$importer_dir = $wp_upload_dir['basedir'] . '/thegem-importer';
	if (! defined('WP_LOAD_IMPORTERS')) define('WP_LOAD_IMPORTERS', true);
	require_once(plugin_dir_path( __FILE__ ) . '/inc/wordpress-importer.php');

	WP_Import::setImportDir($importer_dir);
	$wp_import = WP_Import::getInstance();
	$wp_import->watchPostProcessed = true;

	$wp_import->fetch_attachments = $import_selection['import_media'] ? true : false;
	$ids = get_option('thegem_importer_ids');

	if(!empty($ids) && is_array($ids)) {
		$result = $wp_import->import($importer_dir.'/content.xml', $ids);
	} else {
		$result = $wp_import->import($importer_dir.'/content.xml');
	}

	//$progress = get_option('thegem_importer_progress');
	//update_option('thegem_importer_progress', array('current' => 0, 'max' => 0, 'total' => $progress['total']));
	$content = ob_get_clean();
	if($result === 10 || $result === 20) {
		echo json_encode(array('status' => $result, 'content' => 'Repeat'));
	} else {
		echo json_encode(array('status' => 200, 'content' => 'End'));
		$progress = get_option('thegem_importer_progress');
		update_option('thegem_importer_progress', array('current' => $progress['total'], 'total' => $progress['total']));
	}
	die(-1);
}

add_action('wp_ajax_thegem_importer_get_import_step_finalize', 'thegem_importer_get_import_step_finalize');
function thegem_importer_get_import_step_finalize() {
	$step = get_option('thegem_importer_finalize_step');
	$import_selection = get_option('thegem_importer_selection');
	$status = 200;
	write_log('finalize step is '.$step);
	if(!$step) {
		thegem_importer_update_images();
		if($import_selection['import_type'] == 'part') {
			if($import_selection['import_rev_sliders']) {
				update_option('thegem_importer_finalize_step', 'sliders');
			} else {
				update_option('thegem_importer_finalize_step', 'settings');
			}
		} else {
			update_option('thegem_importer_finalize_step', 'menu');
		}
		$status = 10;
		$content = __('Importing menu.', 'thegem-importer');
	} elseif($step == 'menu') {
		$result = thegem_importer_import_menu();
		if($result == 20) {
			$content = __('Importing menu.', 'thegem-importer');
		} else {
			if($import_selection['import_rev_sliders']) {
				update_option('thegem_importer_finalize_step', 'sliders');
			} else {
				update_option('thegem_importer_finalize_step', 'settings');
			}
			$content = __('Importing sliders.', 'thegem-importer');
		}
		$status = 10;
	} elseif($step == 'sliders') {
		if(thegem_importer_import_sliders()) {
			update_option('thegem_importer_finalize_step', 'settings');
			$content = __('Importing settings.', 'thegem-importer');
		} else {
			update_option('thegem_importer_finalize_step', 'sliders');
			$content = __('Importing sliders.', 'thegem-importer');
		}
		$status = 10;
	} elseif($step == 'settings') {
		thegem_importer_import_settings();
		delete_option('thegem_importer_finalize_step');
		$content = __('All done.', 'thegem-importer');
	}
	echo json_encode(array('status' => $status, 'content' => $content));
	die(-1);
}

add_action('wp_ajax_thegem_importer_progress', 'thegem_importer_progress');
function thegem_importer_progress() {
	$progress = get_option('thegem_importer_progress');
	if(isset($progress['current']) && isset($progress['total'])) {
		$percent = intval($progress['current']) * 100 / intval($progress['total']);
	} else {
		$percent = 0;
	}
	echo json_encode(array('status' => 200, 'percent' => round($percent, 1)));
	die(-1);
}

add_action('wp_ajax_thegem_importer_get_import_step_4', 'thegem_importer_get_import_step_4');
function thegem_importer_get_import_step_4() {
	$import_data = get_option('thegem_importer_data');
	$import_selection = get_option('thegem_importer_selection');
	if(is_array($import_data)) {
		ob_start();
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
		require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
		$wp_upload_dir = wp_upload_dir();
		$importer_dir = $wp_upload_dir['basedir'] . '/thegem-importer';
		WP_Filesystem();
		$fs = new WP_Filesystem_Direct(false);
		$fs->rmdir($importer_dir, true);
?>
<div class="thegem-importer-wrap thegem-importer-step-4">
	<?php if($import_data['image'] && $import_data['title']) : ?>
		<div class="import-popup-title">
			<div class="import-image"><img src="<?php echo esc_url($import_data['image']); ?>" alt="<?php echo esc_attr($import_data['title']); ?>"></div>
			<div class="import-title"><?php echo $import_data['title']; ?></div>
		</div>
	<?php endif; ?>
	<?php if($import_selection['import_type'] != 'part') : ?>
		<div class="import-select-title"><?php _e('Demo has been successfully installed.', 'thegem-importer'); ?></div>
		<div class="import-select-title bold"><?php _e('You have a new website now!', 'thegem-importer'); ?></div>
	<?php else : ?>
		<div class="import-select-title"><?php _e('Pages have been successfully installed.', 'thegem-importer'); ?></div>
	<?php endif; ?>
	<div class="import-select-data-text"><?php _e('What would you like to do next?', 'thegem-importer'); ?></div>
	<div class="submit-buttons">
		<a href="<?php echo admin_url('admin.php?page=thegem-theme-options'); ?>"><?php _e('Go to Theme Options', 'thegem-importer'); ?></a>
		<a href="<?php echo get_site_url(); ?>" class="submit-import" target="_blank"><?php _e('Preview Website', 'thegem-importer'); ?></a>
	</div>
	<div class="import-select-data-text bottom-text"><?php _e('or', 'thegem-importer'); ?></div>
	<div class="import-select-data-text bottom-text"><?php printf(__('Learn how to use TheGem theme from <a href="%s" target="_blank">our detailed manual</a>.', 'thegem-importer'), esc_url('https://codex-themes.com/thegem/documentation/')); ?></div>
</div>
<?php
		$content = ob_get_clean();
		echo json_encode(array('status' => 200, 'content' => $content));
	} else {
		echo json_encode(array('status' => 0, 'content' => thegem_importer_error_content('data')));
	}
	die(-1);
}

add_action('wp_ajax_thegem_importer_get_import_cancel', 'thegem_importer_get_import_cancel');
function thegem_importer_get_import_cancel() {
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
	$wp_upload_dir = wp_upload_dir();
	$importer_dir = $wp_upload_dir['basedir'] . '/thegem-importer';
	WP_Filesystem();
	$fs = new WP_Filesystem_Direct(false);
	$fs->rmdir($importer_dir, true);
	die(-1);
}

add_filter('wp_import_post_data_raw', 'thegem_importer_wp_import_post_data_raw');
function thegem_importer_wp_import_post_data_raw($post) {
/*
	$import_data = get_option('thegem_importer_data');
	$upload_dir = wp_upload_dir();
	$post['post_content'] = str_replace($import_data['uploads_url'], $upload_dir['baseurl'], $post['post_content']);
	$post['post_content'] = str_replace($import_data['theme_url'], get_template_directory_uri(), $post['post_content']);
*/

	if($post['post_type'] != 'nav_menu_item') {
		$post['post_title'] = $post['post_title'].' (Demo)';
	}

	return $post;
}

add_filter('wp_import_categories', 'thegem_importer_wp_import_categories');
function thegem_importer_wp_import_categories($categories) {
	if(!empty($categories)) {
		foreach ($categories as $key => $value) {
			$categories[$key]['cat_name']=$categories[$key]['cat_name'].' (Demo)';
		}
	}
	return $categories;
}

add_filter('wp_import_tags', 'thegem_importer_wp_import_tags');
function thegem_importer_wp_import_tags($tags) {
	if(!empty($tags)) {
		foreach ($tags as $key => $value) {
			$tags[$key]['tag_name']=$tags[$key]['tag_name'].' (Demo)';
		}
	}
	return $tags;
}

add_filter('wp_import_terms', 'thegem_importer_wp_import_terms');
function thegem_importer_wp_import_terms($terms) {
	if(!empty($terms)) {
		foreach ($terms as $key => $value) {
			$terms[$key]['term_name']=$terms[$key]['term_name'].' (Demo)';
		}
	}
	return $terms;
}

add_action('import_post_meta', 'thegem_import_post_meta', 11, 3);
function thegem_import_post_meta($post_id, $key, $value) {
/*
	$import_data = get_option('thegem_importer_data');
	$upload_dir = wp_upload_dir();
	if(is_array($value)) {
		foreach($value as $k => $v) {
			if(is_array($v)) {
				foreach($v as $a => $b) {
					if(is_string($value[$k][$a])) {
						$value[$k][$a] = str_replace($import_data['uploads_url'], $upload_dir['baseurl'], $value[$k][$a]);
						$value[$k][$a] = str_replace($import_data['theme_url'], get_template_directory_uri(), $value[$k][$a]);
					}
				}
			} elseif(is_string($value[$k])) {
				$value[$k] = str_replace($import_data['uploads_url'], $upload_dir['baseurl'], $value[$k]);
				$value[$k] = str_replace($import_data['theme_url'], get_template_directory_uri(), $value[$k]);
			}
		}
	} elseif(is_string($value)) {
		$value = str_replace($import_data['uploads_url'], $upload_dir['baseurl'], $value);
		$value = str_replace($import_data['theme_url'], get_template_directory_uri(), $value);
	}

	update_post_meta($post_id, $key, $value);
*/
	//if ($post_id==6 && $key == '_elementor_data') {
	//	write_log("update postmeta $key $value");
	//	write_log('value in db '.get_post_meta(6,'_elementor_data',true));
	//}
}

function thegem_importer_update_images() {
	$attachments_data = get_option('thegem_importer_attachments_data');
	$attachments_data = thegem_get_ids_attachment($attachments_data);
	$forms_data = get_option('thegem_importer_forms_data');
	$forms_data = thegem_get_ids_forms($forms_data);
	$rev_sliders = array();
	delete_option('thegem_importer_revsliders_for_import');
	//$product_categories_data = thegem_get_categories_product_json_data();
	$query = new WP_Query( array('post_status' => 'any', 'post_type' => 'any', 'posts_per_page'=>-1) );
	$content_sliders = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		$p = get_post();
		//thegem_importer_fix_post_meta($p);
		if(get_post_type() != 'attachment' && !empty($p->post_content)) {
			$p->post_content = thegem_replace_attachments_content($p->post_content, $attachments_data);
			//$p->post_content = thegem_replace_product_categories_content($p->post_content, $product_categories_data);
			$p->post_content = thegem_replace_forms_content($p->post_content, $forms_data);
			$post_content_sliders = thegem_get_content_sliders($p->post_content);
			if(is_array($post_content_sliders) && !empty($post_content_sliders)) {
				$content_sliders = array_merge($content_sliders, $post_content_sliders);
			}
			wp_update_post($p);
		}
		$slider_data = thegem_get_sanitize_page_slideshow_data($p->ID);
		if($slider_data['slideshow_type'] == 'revslider') {
			$rev_sliders[$p->ID] = $slider_data['slideshow_revslider'];
		}
	}
	if(is_array($content_sliders) && !empty($content_sliders)) {
		$rev_sliders = array_merge($rev_sliders, $content_sliders);
	}
	update_option('thegem_importer_revsliders_for_import', $rev_sliders);

	$query = new WP_Query( array('post_status' => 'any', 'post_type' => array('thegem_title', 'thegem_footer'), 'posts_per_page'=>-1) );
	while ( $query->have_posts() ) {
		$query->the_post();
		$p = get_post();
		//thegem_importer_fix_post_meta($p);
		if(get_post_type() != 'attachment' && !empty($p->post_content)) {
			$p->post_content = thegem_replace_attachments_content($p->post_content, $attachments_data);
			//$p->post_content = thegem_replace_product_categories_content($p->post_content, $product_categories_data);
			$p->post_content = thegem_replace_forms_content($p->post_content, $forms_data);
			wp_update_post($p);
		}
	}
}

function thegem_importer_import_menu() {
	//global $thegem_importer_end_work_time;
	//$thegem_importer_end_work_time = time() + 1;

	write_log('start import menu');

	ob_start();
	$wp_upload_dir = wp_upload_dir();
	$importer_dir = $wp_upload_dir['basedir'] . '/thegem-importer';
	if (! defined('WP_LOAD_IMPORTERS')) define('WP_LOAD_IMPORTERS', true);
	require_once(plugin_dir_path( __FILE__ ) . '/inc/wordpress-importer.php');

	WP_Import::setImportDir($importer_dir,'import_menu.state');
	$wp_import = WP_Import::getInstance();
	$wp_import->savePeriod = 0;
	$wp_import->fetch_attachments = true;
	$result = $wp_import->import($importer_dir.'/content_menu.xml');
	$content = ob_get_clean();
	return $result;
}

function thegem_importer_import_sliders() {
	$rev_sliders = get_option('thegem_importer_revsliders_for_import');
	if(is_array($rev_sliders)) {
		$id = key($rev_sliders);
		$alias = $rev_sliders[$id];

		$slider_link = thegem_importer_slider_link($alias);
		if(!empty($slider_link)) {
			if (class_exists( 'RevSlider' ) ) {
				$slider = new RevSlider();
				if(!$slider->isAliasExists($alias)) {
					require_once(ABSPATH . 'wp-admin/includes/file.php');
					$tmp = download_url($slider_link);
					if( !is_wp_error( $tmp ) ) {
						$result = $slider->importSliderFromPost(true,true,$tmp);
					}
					unlink($tmp);
				}
			}
		}

		unset($rev_sliders[$id]);
	}
	if(!empty($rev_sliders)) {
		update_option('thegem_importer_revsliders_for_import', $rev_sliders);
		return false;
	}
	return true;
}

function thegem_importer_slider_link($alias) {
	$import_data = get_option('thegem_importer_data');
	if(isset($import_data['sliders']) && isset($import_data['sliders'][$alias])) {
		return $import_data['sliders'][$alias];
	}
	return false;
}

function thegem_importer_process_options() {
	global $wpdb;

	$map = get_option('thegem_import_posts_map');
	write_log('posts map has ' . count($map) . ' elements');
	if (! defined('WP_LOAD_IMPORTERS')) define('WP_LOAD_IMPORTERS', true);
	require_once(plugin_dir_path( __FILE__ ) . 'inc/wordpress-importer.php');

	$options = array(
		'thegem_theme_options',
		'thegem_options_page_settings_blog',
		'thegem_options_page_settings_default',
		'thegem_options_page_settings_global',
		'thegem_options_page_settings_portfolio',
		'thegem_options_page_settings_post',
		'thegem_options_page_settings_product',
		'thegem_options_page_settings_product_categories',
		'thegem_options_page_settings_search'
	);

	foreach($options as $option) {
		write_log("process option $option");

		$data = get_option($option);

		thegem_importer_process_option_helper($data, $map);
		$data = WP_Import::process_content_links($data);
	
		update_option($option, $data);
	}

	$postIds = $wpdb->get_col("select post_id from $wpdb->postmeta where meta_key='thegem_page_data'");

	foreach($postIds as $postId) {
		write_log("process page options for post  $postId");

		$data = get_post_meta($postId, 'thegem_page_data', true);

		thegem_importer_process_option_helper($data, $map);
		$data = WP_Import::process_content_links($data);

		update_post_meta($postId,'thegem_page_data', $data);
	}

	$termIds = $wpdb->get_col("select term_id from $wpdb->termmeta where meta_key='thegem_page_data'");

	foreach($termIds as $termId) {
		write_log("process page options for term  $termId");

		$data = get_term_meta($termId, 'thegem_page_data', true);

		thegem_importer_process_option_helper($data, $map);
		$data = WP_Import::process_content_links($data);

		update_term_meta($termId,'thegem_page_data', $data);
	}

	delete_option('thegem_import_posts_map');
}

function thegem_importer_process_option_helper(&$data, $map) {
	if (is_array($data)) {
		foreach($data as $k=>$v) {
			if ($k == 'title_template' || $k == 'footer_custom' || $k == 'custom_footer') {
				if ($v && isset($map[$v])) {
					$data[$k] = is_string($v) ? strval($map[$v])  :intval($map[$v]);
					write_log("found option $k with value $v, replacing with $data[$k]");
				} else {
					write_log("found option $k with value $v");
				}
			}
			if (is_array($v)) {
				thegem_importer_process_option_helper($data[$k], $map);
			}
		}
	}
}

function thegem_importer_import_settings() {
	$import_data = get_option('thegem_importer_data');
	$import_selection = get_option('thegem_importer_selection');
	if(is_array($import_data)) {
		if($import_selection['import_type'] != 'part') {
			$menus = array();
			foreach($import_data['menus'] as $position => $slug) {
				$term = get_term_by( 'slug', $slug, 'nav_menu' );
				if($term) {
					$menus[$position] = $term->term_id;
				}
			}
			set_theme_mod('nav_menu_locations', $menus);

			if(isset($import_data['homepage'])) {
				$front_page_id = get_page_by_title(html_entity_decode($import_data['homepage']).' (Demo)');
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $front_page_id->ID );
			}


			if(function_exists('WC') && !empty($import_data['woocommerce'])) {
				if(!empty($import_data['woocommerce']['shop_page'])) {
					$shop_page_id = get_page_by_title($import_data['woocommerce']['shop_page'].' (Demo)');
					if($shop_page_id->ID) {
						update_option('woocommerce_shop_page_id', $shop_page_id->ID);
					}
				}
				if(!empty($import_data['woocommerce']['cart_page'])) {
					$cart_page_id = get_page_by_title($import_data['woocommerce']['cart_page'].' (Demo)');
					if(!empty($cart_page_id->ID)) {
						update_option('woocommerce_cart_page_id', $cart_page_id->ID);
					}
				}
				if(!empty($import_data['woocommerce']['checkout_page'])) {
					$checkout_page_id = get_page_by_title($import_data['woocommerce']['checkout_page'].' (Demo)');
					if(!empty($checkout_page_id->ID)) {
						update_option('woocommerce_checkout_page_id', $checkout_page_id->ID);
					}
				}
				if(!empty($import_data['woocommerce']['myaccount_page'])) {
					$myaccount_page_id = get_page_by_title($import_data['woocommerce']['myaccount_page'].' (Demo)');
					if(!empty($myaccount_page_id->ID)) {
						update_option('woocommerce_myaccount_page_id', $myaccount_page_id->ID);
					}
				}
				if(!empty($import_data['woocommerce']['terms_page'])) {
					$terms_page_id = get_page_by_title($import_data['woocommerce']['terms_page'].' (Demo)');
					if(!empty($terms_page_id->ID)) {
						update_option('woocommerce_terms_page_id', $terms_page_id->ID);
					}
				}
				if(!empty($import_data['woocommerce']['wishlist_page'])) {
					$wishlist_page_id = get_page_by_title($import_data['woocommerce']['wishlist_page'].' (Demo)');
					if(!empty($wishlist_page_id->ID)) {
						update_option('yith_wcwl_wishlist_page_id', $wishlist_page_id->ID);
					}
				}
				if(function_exists('wc_update_product_lookup_tables')) {
					wc_update_product_lookup_tables();
				}
			}

		}

		if($import_selection['import_theme_options']) {
			$wp_upload_dir = wp_upload_dir();
			$importer_dir = $wp_upload_dir['basedir'] . '/thegem-importer';
			$options_content = file_get_contents($importer_dir.'/theme_options.json');
			$theme_url = str_replace('/', '\/', get_template_directory_uri());
			$uploads_url = str_replace('/', '\/', $wp_upload_dir['baseurl']);
			$options = get_option('thegem_theme_options');
			$options_content = str_replace(array('#theme_url#', '#upload_url#'), array($theme_url, $uploads_url), $options_content);
			$options_new = json_decode($options_content, true);
			$options_new['purchase_code'] = $options['purchase_code'];
			update_option('thegem_theme_options', $options_new);

			$page_settings_content = file_get_contents($importer_dir.'/page_settings.json');
			$page_settings_content = str_replace(array('#theme_url#', '#upload_url#'), array($theme_url, $uploads_url), $page_settings_content);
			$page_settings = json_decode($page_settings_content, true);
			update_option('thegem_options_page_settings_default', $page_settings['default']);
			update_option('thegem_options_page_settings_blog', $page_settings['blog']);
			update_option('thegem_options_page_settings_search', $page_settings['search']);
			update_option('thegem_options_page_settings_global', $page_settings['global']);
			update_option('thegem_options_page_settings_portfolio', $page_settings['portfolio']);
			update_option('thegem_options_page_settings_post', $page_settings['post']);
			update_option('thegem_options_page_settings_product', $page_settings['product']);
			update_option('thegem_options_page_settings_product_categories', $page_settings['product_categories']);

			if ($import_selection['import_type'] != 'part') {
				$additionals_fonts_content = file_get_contents($importer_dir.'/additionals_fonts.json');
				$additionals_fonts = json_decode($additionals_fonts_content, true);

				$remap_urls = get_option('thegem_import_attachment_urls');

				foreach($additionals_fonts as $fontKey=>$font) {
					foreach($font as $k=>$v) {
						if (preg_match('%^font_url%',$k)) {
							if (isset($remap_urls[$v])) {
								$additionals_fonts[$fontKey][$k] = $remap_urls[$v];
							}
						}
					}
				} 
				update_option('thegem_additionals_fonts', $additionals_fonts);
			}

			thegem_importer_process_options();
		}

		if($import_selection['import_type'] != 'part') {
			$wp_upload_dir = wp_upload_dir();
			$importer_dir = $wp_upload_dir['basedir'] . '/thegem-importer';
			$widgets_content = file_get_contents($importer_dir.'/widgets.json');
			$data = json_decode($widgets_content, true);
			global $wp_registered_sidebars;
			$available_widgets = thegem_importer_available_widgets();
			$widget_instances = array();
			foreach ( $available_widgets as $widget_data ) {
				$widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
			}
			foreach ( $data as $sidebar_id => $widgets ) {
				if ( 'wp_inactive_widgets' === $sidebar_id ) {
					continue;
				}
				if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
					$sidebar_available	= true;
					$use_sidebar_id	   = $sidebar_id;
				} else {
					$sidebar_available	= false;
					$use_sidebar_id	   = 'wp_inactive_widgets';
				}
				foreach ( $widgets as $widget_instance_id => $widget ) {
					$fail = false;
					$id_base			= preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
					$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );
					if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
						$fail				= true;
					}
					$widget = json_decode( wp_json_encode( $widget ), true );
					if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {
						$sidebars_widgets = get_option( 'sidebars_widgets' );
						$sidebar_widgets = isset( $sidebars_widgets[ $use_sidebar_id ] ) ? $sidebars_widgets[ $use_sidebar_id ] : array();
						$single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : array();
						foreach ( $single_widget_instances as $check_id => $check_widget ) {
							if ( in_array( "$id_base-$check_id", $sidebar_widgets, true ) && (array) $widget === $check_widget ) {
								$fail = true;
								break;
							}
						}
					}
					if ( ! $fail ) {
						$single_widget_instances = get_option( 'widget_' . $id_base );
						$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array(
							'_multiwidget' => 1,
						);
						if(!empty($widget['text'])) {
							$attachments_data = get_option('thegem_importer_attachments_data');
							$attachments_data = thegem_get_ids_attachment($attachments_data);
							$forms_data = get_option('thegem_importer_forms_data');
							$forms_data = thegem_get_ids_forms($forms_data);
							$widget['text'] = thegem_replace_attachments_content($widget['text'], $attachments_data);
							$widget['text'] = thegem_replace_forms_content($widget['text'], $forms_data);
						}

						$single_widget_instances[] = $widget;
						end( $single_widget_instances );
						$new_instance_id_number = key( $single_widget_instances );
						if ( '0' === strval( $new_instance_id_number ) ) {
							$new_instance_id_number = 1;
							$single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
							unset( $single_widget_instances[0] );
						}
						if ( isset( $single_widget_instances['_multiwidget'] ) ) {
							$multiwidget = $single_widget_instances['_multiwidget'];
							unset( $single_widget_instances['_multiwidget'] );
							$single_widget_instances['_multiwidget'] = $multiwidget;
						}
						update_option( 'widget_' . $id_base, $single_widget_instances );
						$sidebars_widgets = get_option( 'sidebars_widgets' );
						if ( ! $sidebars_widgets ) {
							$sidebars_widgets = array();
						}
						$new_instance_id = $id_base . '-' . $new_instance_id_number;
						$sidebars_widgets[ $use_sidebar_id ][] = $new_instance_id;
						update_option( 'sidebars_widgets', $sidebars_widgets );
					}
				}
			}
		}

		global $wpdb;
		$prefix = thegem_get_image_cache_option_key_prefix();
		$wpdb->query("DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '%{$prefix}%'");

	}
	flush_rewrite_rules();
}

function thegem_importer_available_widgets() {
	global $wp_registered_widget_controls;
	$widget_controls = $wp_registered_widget_controls;
	$available_widgets = array();
	foreach ( $widget_controls as $widget ) {
		if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
			$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
			$available_widgets[ $widget['id_base'] ]['name'] = $widget['name'];
		}
	}
	return $available_widgets;
}

add_action('wp_ajax_thegem_importer_generate_css', 'thegem_importer_generate_css');
function thegem_importer_generate_css() {
	ob_start();
	if (false === ($creds = request_filesystem_credentials(site_url()) ) ) {
		$form_output = ob_get_clean();
		ob_start();
?>
<div class="thegem-importer-wrap thegem-importer-step-css">
	<div class="import-popup-title import-popup-filesystem-credentials-title"><?php _e('Unable to update the theme\'s styles & settings, FTP access details needed.', 'thegem-importer'); ?></div>
	<div class="import-popup-title import-popup-filesystem-credentials-form-title"><?php _e('Connection Information'); ?></div>
	<div class="thegem-importer-filesystem-credentials-form-wrap">
		<?php if(isset($_REQUEST['error'])) : ?>
			<div class="thegem-importer-filesystem-credentials-error"><?php echo esc_html($_REQUEST['error']); ?></div>
		<?php endif; ?>
		<p class="thegem-importer-filesystem-credentials-form-desc"><?php _e('In order to update the theme\'s styles & settings please enter your FTP access details below:', 'thegem-importer'); ?></p>
		<?php echo $form_output; ?>
	</div>
</div>
<?php
		$result_output = ob_get_clean();
		echo json_encode(array('status' => 10, 'content' => $result_output));
		die(-1);
	}
	if(!WP_Filesystem($creds)) {
		ob_clean();
		echo json_encode(array('status' => 20, 'content' => __('Unable to connect to the filesystem. Please confirm your credentials.', 'thegem-importer')));
		die(-1);
	}
	ob_clean();
	ob_start();
	thegem_custom_fonts();
	require get_template_directory() . '/inc/custom-css.php';
	if(file_exists(get_stylesheet_directory() . '/inc/custom-css.php') && get_stylesheet_directory() != get_template_directory()) {
		require get_stylesheet_directory() . '/inc/custom-css.php';
	}
	$custom_css = ob_get_clean();
	ob_start();
	require get_template_directory() . '/inc/style-editor-css.php';
	$editor_css = ob_get_clean();
	global $wp_filesystem;
	$old_name = thegem_get_custom_css_filename();
	$new_name = thegem_generate_custom_css_filename();
	if(!$wp_filesystem->put_contents($wp_filesystem->find_folder(get_stylesheet_directory()) . 'css/'.$new_name.'.css', $custom_css)) {
	} else {
		$wp_filesystem->put_contents($wp_filesystem->find_folder(get_template_directory()) . 'css/style-editor.css', $editor_css);
		if($old_name != 'custom') {
			$wp_filesystem->delete($wp_filesystem->find_folder(get_stylesheet_directory()) . 'css/'.$old_name.'.css', $custom_css);
		}
		thegem_save_custom_css_filename($new_name);
	}
	echo json_encode(array('status' => 200, 'content' => __('Successful', 'thegem-importer')));
	die(-1);
}

add_action('wp_ajax_thegem_importer_remove_demo_confirm', 'thegem_importer_remove_demo_confirm');
function thegem_importer_remove_demo_confirm() {
	ob_start();
?>
<div class="thegem-importer-wrap thegem-importer-remove-demo-confirm">
	<div class="import-remove-demo-text">
		<p><?php _e('With this option you can remove all demo content, which you have imported and which you have not modified.', 'thegem-importer'); ?></p>
		<p><?php _e('IMPORTANT: In case you have edited (and saved) some posts, pages etc., this content will not be removed. This concerns media content as well. If you wish to keep some demo media on your website, you need to remove the word " (Demo)" from the media description of the corresponding media file (image, video etc), otherwise this file will be removed as well.', 'thegem-importer'); ?></p>
	</div>
	<div class="submit-buttons">
		<button type="button" class="remove-demo-confirm"><?php _e('Remove demo content', 'thegem-importer'); ?></button>
	</div>
</div>
<?php
	$output = ob_get_clean();
	echo json_encode(array('status' => 200, 'content' => $output));
	die(-1);
}

add_action('wp_ajax_thegem_importer_remove_demo', 'thegem_importer_remove_demo');
function thegem_importer_remove_demo() {

	global $wpdb;
	$demo_prefix = $wpdb->esc_like(' (Demo)');
	$demo_prefix = '%'.$demo_prefix.'%';

	//deleted attachments
/*	$sql = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title LIKE %s AND post_type = %s", $demo_prefix, 'attachment');
	$attachments = $wpdb->get_results($sql, OBJECT);
	foreach ($attachments as $key => $value) {
			wp_delete_attachment($value->ID, true);
	}*/

	//deleted posts
	$sql = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title LIKE %s", $demo_prefix);
	$posts = $wpdb->get_results($sql, OBJECT);
	foreach ($posts as $key => $value) {
		if ($value->post_type == 'elementor_library' && strpos($value->post_title, 'Default Kit')!==false) {
			continue;
		}
		wp_delete_post($value->ID, true);
	}

	//deleted terms
	$sql = $wpdb->prepare("SELECT term_id FROM $wpdb->terms WHERE name LIKE %s", $demo_prefix);
	$terms = $wpdb->get_results($sql, OBJECT);
	foreach ($terms as $key => $value) {
		$term = get_term($value->term_id);
		wp_delete_term($term->term_id, $term->taxonomy);
	}

	delete_option('thegem_import_attachment_urls');

	//update menu
	$menus = get_nav_menu_locations();
	foreach($menus as $position => $id) {
		$term = get_term_by( 'term_id', $id, 'nav_menu' );
		if(empty($term)) {
			$menus[$position] = false;
		}
	}
	set_theme_mod('nav_menu_locations', $menus);

	ob_start();
?>
<div class="thegem-importer-wrap thegem-importer-remove-demo">
	<div class="import-select-title"><?php _e('Demo data has been removed.', 'thegem-importer'); ?></div>
	<div class="submit-buttons">
		<button type="button" class="cancel-import"><?php _e('Close', 'thegem-importer'); ?></button>
	</div>
</div>
<?php
	$output = ob_get_clean();
	echo json_encode(array('status' => 200, 'content' => $output));
	die(-1);
}

//add_filter('wp_import_terms', 'thegem_importer_wp_import_pa_terms');
function thegem_importer_wp_import_pa_terms($terms) {
	if(function_exists('wc_create_attribute')) {
		foreach($terms as $term ) {
			if(strpos($term['term_taxonomy'], 'pa_') === 0) {
				wc_create_attribute(array(
					'slug' => substr($term['term_taxonomy'], 3),
					'name' => ucfirst(substr($term['term_taxonomy'], 3)),
				));
			}
		}
	}
	return $terms;
}

function thegem_importer_fix_post_meta($p) {
	$metas = get_post_meta($p->ID, '', true);
	if(!empty($metas) && is_array($metas)) {
		foreach($metas as $key => $meta) {
			$value = get_post_meta($p->ID, $key, true);

			do_action( 'import_post_meta', $p->ID, wp_slash($key), wp_slash($value) );
		}
	}
}

function thegem_importer_error_content($error = '') {
	ob_start();
?>
<div class="thegem-importer-wrap thegem-importer-error">
	<div class="import-select-data-title"><?php _e('Attention', 'thegem-importer'); ?></div>
	<div class="import-error-info">
		<?php if($error == 'ajax') : ?>
			<div class="import-error-title"><?php _e('Problem occured while processing the ajax request. Possible reasons for that:', 'thegem-importer'); ?></div>
			<div class="import-error-text">
				<p><?php printf(__('1. Your server settings do not correspond with the recommended server settings (check here <a href="%s" target="_blank">%s</a>). Please contact your hoster and ask them to adjust your settings.', 'thegem-importer'), 'https://codexthemes.ticksy.com/article/8024/', 'https://codexthemes.ticksy.com/article/8024/'); ?></p>
				<p><?php _e('2. Your desktop is using some proxy with restricted ajax processing settings. Please disable any proxy on your desktop.', 'thegem-importer'); ?></p>
				<p><?php _e('3. No internet connection. Please check your internet connection.', 'thegem-importer'); ?></p>
				<p><?php printf(__('Please check this points and restart demo import. In case the problem will remain, <a href="%s" target="_blank">contact our support at codexthemes.ticksy.com</a>.', 'thegem-importer'), 'https://codexthemes.ticksy.com/'); ?></p>
			</div>
		<?php elseif($error == 'connecting') : ?>
			<div class="import-error-title"><?php _e('Some troubles with connecting to demo-content server. Possible reasons for that:', 'thegem-importer'); ?></div>
			<div class="import-error-text">
				<p><?php _e('1. Your server\'s firewall is blocking requests to our server with demo content (IP 142.44.212.145). Please contact your hoster and ask them to whitelist this IP.', 'thegem-importer'); ?></p>
				<p><?php _e('2. cURL extension is not installed on your host/server. Please contact your hoster and ask them to install cURL.', 'thegem-importer'); ?></p>
				<p><?php _e('3. Our demo-content server is not accessible. Please try to import again later.', 'thegem-importer'); ?></p>
			</div>
		<?php elseif($error == 'data') : ?>
			<div class="import-error-title"><?php _e('Bad data package. Please re-try importing or contact our support.', 'thegem-importer'); ?></div>
		<?php else : ?>
			<div class="import-error-title"><?php echo $error; ?></div>
		<?php endif; ?>
		<div class="submit-buttons">
			<button type="button" class="cancel-import"><?php _e('Close', 'thegem-importer'); ?></button>
		</div>
	</div>
</div>
<?php
	$output = ob_get_clean();
	return $output;
}
