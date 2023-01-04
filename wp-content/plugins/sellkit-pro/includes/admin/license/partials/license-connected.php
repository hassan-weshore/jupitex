<?php
$license = new Sellkit_License();

?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div class="sellkit-card">
		<h3 class="sellkit-card-section flex-space-between">
			<span><?php esc_html_e( 'Status', 'sellkit-pro' ); ?>: 
				<?php if ( 'expired' === $license->get( 'status' ) ) : ?>
					<span style="color: #ff1919;"><?php esc_html_e( 'Expired', 'sellkit-pro' ); ?></span>
				<?php elseif ( 'invalid' === $license->get( 'status' ) ) : ?>
					<span style="color: #ff1919;"><?php esc_html_e( 'Invalid', 'sellkit-pro' ); ?></span>
				<?php else : ?>
					<span style="color: #00a600;"><?php esc_html_e( 'Active', 'sellkit-pro' ); ?></span>
				<?php endif; ?>
			</span>
			<a class="button" style="font-weight: 400;" href="https://my.getsellkit.com/"><?php esc_html_e( 'View Dashboard', 'sellkit-pro' ); ?></a>
		</h3>

		<div class="sellkit-card-section flex-space-between">
			<p>
				<?php
					echo sprintf(
						/* Translators: %s: user email */
						__( 'You are connected as %s.', 'sellkit-pro' ),
						'<strong>' . $license->get( 'user_email' ) . '</strong>'
					);
				?>
				<br>
				<?php esc_html_e( 'Would you like to switch your license via another Sellkit Account?', 'sellkit-pro' ); ?>
			</p>
			<a href="<?php echo $license->get_url( 'switch' ); ?>" class="button button-primary"><?php esc_html_e( 'Switch Account', 'sellkit-pro' ); ?></a>	
		</div>
		<div class="sellkit-card-section flex-space-between">
			<p><?php esc_html_e( 'Do you want to remove your license from this website?', 'sellkit-pro' ); ?></p>
			<a href="<?php echo esc_url( $license->get_url( 'deactivate' ) ); ?>" class="button"><?php esc_html_e( 'Deactivate Licence', 'sellkit-pro' ); ?></a>	
		</div>
	</div>
</div>

