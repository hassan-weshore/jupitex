<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;


if ( is_user_logged_in() ) {
	$this_user = wp_get_current_user();
	$email     = get_user_meta( $this_user->ID, 'billing_email', true );

	if ( empty( $email ) ) {
		$email = $this_user->user_email;
	}

	?>
	<div class="sellkit-checkout-widget-login-section sellkit-checkout-widget-logged-user sellkit-checkout-local-fields">
		<h4 class="header heading"><?php echo __( 'Contact Details', 'sellkit' ); ?></h4>
		<div class="sellkit-checkout-widget-email-holder">
			<div class="sellkit-checkout-fields-wrapper sellkit-widget-checkout-fields sellkit-checkout-excluded-wrapper-fields sellkit-login-section">
				<div style="position:relative">
					<span class="mini-title">
						<?php echo __( 'Email address', 'sellkit' ); ?>
					</span>
				</div>
				<p class="log-email">
					<span class="woocommerce-input-wrapper">
						<input
							type="email"
							class="input-text validate-email"
							name="billing_email"
							id="billing_email"
							readonly
							placeholder="<?php echo __( 'Email Address', 'sellkit' ); ?>"
							value="<?php echo $email; ?>"
							autocomplete="email"
						/>
					</span>
				</p>
			</div>
		</div>
	</div>
	<?php
} else {
	?>
		<div class="sellkit-checkout-widget-login-section sellkit-checkout-local-fields">
			<h4 class="header heading"><?php echo __( 'Contact Details', 'sellkit' ); ?></h4>
			<div class="sellkit-checkout-widget-email-holder">
				<div class="sellkit-checkout-fields-wrapper sellkit-widget-checkout-fields sellkit-checkout-excluded-wrapper-fields sellkit-login-section">
					<div style="position:relative">
						<span class="mini-title">
							<?php echo __( 'Email address', 'sellkit' ); ?>
						</span>
					</div>
					<p>
						<span class="woocommerce-input-wrapper">
							<input
								type="text"
								name="billing_email"
								id="billing_email"
								class="login-mail validate-email"
								placeholder="<?php echo __( 'Email Address', 'sellkit' ); ?>"
								autocomplete=""
							>
						</span>
						<span class="sellkit-checkout-widget-email-error login-section-error">
							<?php echo __( 'Email address is not valid.', 'sellkit' ); ?>
						</span>
						<span class="sellkit-checkout-widget-email-empty login-section-error">
							<?php echo __( 'Email address is empty.', 'sellkit' ); ?>
						</span>
						<span class="jupiter-checkout-widget-email-search">
							<i class="fas fa-sync fa-spin"></i>
							<?php echo __( 'Checking...', 'sellkit' ); ?>
						</span>
					</p>
				</div>
			</div>
			<div class="login_hidden_section sellkit-checkout-widget-username-field">
				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
					<div class="sellkit-checkout-fields-wrapper sellkit-widget-checkout-fields sellkit-checkout-excluded-wrapper-fields sellkit-login-section">
						<div style="position:relative">
							<span class="mini-title">
								<?php echo __( 'Username', 'sellkit' ); ?>
							</span>
						</div>
						<p>
							<input
								type="text"
								class="login-username"
								name="account_username"
								id="register_user"
								placeholder="<?php echo __( 'Username', 'sellkit' ); ?>"
							>
							<span class="sellkit-checkout-widget-username-error login-section-error">
								<?php echo __( 'An account is already registered with that username. Please choose another..', 'sellkit' ); ?>
							</span>
						</p>
					</div>
				<?php endif; ?>
			</div>
			<div class="login_hidden_section sellkit-checkout-widget-password-field">
				<div class="sellkit-checkout-fields-wrapper sellkit-widget-checkout-fields sellkit-checkout-excluded-wrapper-fields sellkit-login-section">
					<div style="position:relative">
						<span class="mini-title">
							<?php echo __( 'Password', 'sellkit' ); ?>
						</span>
					</div>
					<p>
						<input
							type="password"
							class="login-pass"
							<?php
								if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
									echo 'name="account_password"';
									echo 'id="register_pass"';
								} else {
									echo 'name="account_password"';
									echo 'id="login_pass"';
								}
							?>
							placeholder="<?php echo __( 'Password', 'sellkit' ); ?>"
						>
					</p>
				</div>
			</div>
			<?php if ( 'yes' === get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) ) : ?> 
			<div class="create-desc">
				<div class="sellkit-checkout-fields-wrapper sellkit-widget-checkout-fields sellkit-checkout-excluded-wrapper-fields sellkit-login-section">
					<p>
						<input
							type="checkbox"
							class="sellkit-create-account-checkbox woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
							id="createaccount"
							name="createaccount"
							value="1"
						>
						<label for="createaccount" class="sellkit-create-account-checkbox-label">
							<?php
								do_action( 'sellkit_core/widgets/checkout/custom_message/create_website_account' );
							?>
						</label>
					</p>
				</div>
			</div>
			<?php endif; ?>
			<div class="login-wrapper login_hidden_section">
				<div class="sellkit-checkout-fields-wrapper sellkit-widget-checkout-fields sellkit-login-section">
					<span class="login-submit sellkit-checkout-widget-secondary-button">
						<?php echo __( 'Login', 'sellkit' ); ?>
					</span>
					<label class="login-result"></label>
				</div>
			</div>
		</div>
	<?php
}
