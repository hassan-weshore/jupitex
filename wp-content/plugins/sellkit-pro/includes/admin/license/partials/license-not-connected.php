<?php
$license = new Sellkit_License();

?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div class="sellkit-card">
		<h3 class="sellkit-card-section flex-space-between"><?php esc_html_e( 'Activate License', 'sellkit-pro' ); ?></h3>
		<p><?php esc_html_e( 'Please activate your license to get access to plugin updates.', 'sellkit-pro' ); ?>
		</p>
		<div style="text-align: right;padding-bottom: 20px">
			<a href="<?php echo esc_url( $license->get_url( 'activate' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Connect & Activate', 'sellkit-pro' ); ?></a>
		</div>
	</div>
</div>
