<?php

/*add_action('admin_menu', 'thegem_remote_upload');
function thegem_remote_upload () {
	add_submenu_page( 'upload.php', 'Remote Upload', 'Remote Upload', 'manage_options', 'thegem-remote-upload', 'thegem_remote_upload_page', '' );
}*/

function thegem_remote_upload_page () {
?>
<div class="wrap">
	<div id="icon-tools" class="icon32"></div>
	<h2>Remote Upload</h2>
	<form method="POST">
		<?php wp_nonce_field( 'thegem_remote_upload', 'thegem_remote_upload_field' ); ?>
		<input type="text" name="url" />
		<button type="submit"><?php _e('Upload', 'thegem'); ?></button>
	</form>
</div>
<?php
}

add_action('admin_init', 'thegem_remote_upload_submit');
function thegem_remote_upload_submit () {
	if(isset($_REQUEST['thegem_remote_upload_field']) && wp_verify_nonce( $_REQUEST['thegem_remote_upload_field'], 'thegem_remote_upload' ) && !empty($_REQUEST['url'])) {
		$url = $_REQUEST['url'];
		$result = media_sideload_image($url, 0);
	}
}

add_action('post-upload-ui', 'thegem_remote_upload_test');

function thegem_remote_upload_test () {
	wp_enqueue_script('thegem-remote-upload-scripts');
?>
	<h2>Remote Upload</h2>
		<textarea class="urls-list" rows="5" cols="100" style="vertical-align: top;"></textarea>
		<button type="submit" id="thegem-remote-upload-button"><?php _e('Upload', 'thegem'); ?></button>
<?php
}

add_action('admin_enqueue_scripts', 'thegem_remote_upload_enqueue');
function thegem_remote_upload_enqueue () {
	wp_register_script('thegem-remote-upload-scripts', plugins_url( '/../js/remote-upload.js' , __FILE__ ), array('jquery'), false, true);
	wp_localize_script('thegem-remote-upload-scripts', 'thegem_remote_upload_object', array(
		'security' => wp_create_nonce('thegem_remote_upload_ajax_security'),
	));
}

add_action('wp_ajax_thegem_remote_upload_ajax', 'thegem_remote_upload_ajax');
function thegem_remote_upload_ajax () {
	$urls = !empty($_REQUEST['urls']) ? explode("\n", $_REQUEST['urls']) : array();
	foreach($urls as $url) {
		$result = media_sideload_image($url, 0);
	}
	die(-1);
}
