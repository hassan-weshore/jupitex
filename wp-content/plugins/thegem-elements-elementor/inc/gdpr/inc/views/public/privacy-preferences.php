<?php

$settings = $this->options[TheGemGdpr::TYPE_SETTINGS];
$user_consents = isset($_COOKIE[TheGemGdpr::COOKIE_NAME_CONSENTS]) ? json_decode(wp_unslash($_COOKIE[TheGemGdpr::COOKIE_NAME_CONSENTS]), true) : array();

?>
<div class="gdpr-privacy-preferences">
	<div class="gdpr-privacy-preferences-box">
		<button class="btn-gdpr-privacy-preferences-close" type="button"></button>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<input type="hidden" name="action" value="thegem_gdpr_update_privacy_preferences">
			<?php wp_nonce_field('thegem_gdpr_update_privacy_preferences', 'update-privacy-preferences-nonce' ); ?>
			<div class="gdpr-privacy-preferences-header">
				<div class="gdpr-privacy-preferences-title"><?php echo esc_html($settings['modal_title']); ?></div>
			</div>
			<div class="gdpr-privacy-preferences-body">
				<div class="gdpr-privacy-preferences-text"><?php echo nl2br(esc_html($settings['excerpt_text'])); ?></div>
				<div class="gdpr-privacy-preferences-consents">
					<?php foreach ($settings['consents'] as $type=>$consent) { ?>
						<div class="gdpr-privacy-preferences-consent-item">
							<div class="gdpr-privacy-consent-param">
								<div class="gdpr-privacy-consent-title"><?php echo esc_html($consent['title']); ?></div>
								<div class="gdpr-privacy-consent-description"><?php echo wp_kses(wp_unslash($consent['description']), TheGemGdpr::get_allowed_html_tags()) ?></div>
							</div>
							<div class="gdpr-privacy-consent-value">
								<?php if ($consent['required']) { ?>
									<div class="gdpr-privacy-consent-always-active"><?php _e('Required', 'thegem'); ?></div>
									<input name="consents[<?php echo esc_attr($type); ?>]" value="1" type="hidden">
								<?php } else { ?>
									<label class="gdpr-privacy-checkbox">
										<?php
										$checked = !empty($consent['state']) ? $consent['state'] : 0;
										if (!empty($user_consents) && array_key_exists($type, $user_consents)) {
											$checked = $user_consents[$type];
										}
										?>
										<input name="consents[<?php echo esc_attr($type); ?>]" type="hidden" value="<?php esc_attr($checked) ?>">
										<input name="consents[<?php echo esc_attr($type); ?>]" type="checkbox" <?php checked($checked); ?>>
										<span class="gdpr-privacy-checkbox-check"></span>
									</label>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="gdpr-privacy-preferences-footer">
				<button class="btn-gdpr-privacy-save-preferences" type="submit"><?php echo esc_attr($settings['save_preferences_button_text']); ?></button>
				<div class="gdpr-privacy-preferences-footer-links">
					<?php if (!empty($settings['privacy_policy_page'])) { ?>
						<a href="<?php echo esc_url(get_permalink($settings['privacy_policy_page'])); ?>"  target="_blank"><?php echo esc_attr($settings['privacy_policy_link_text']); ?></a>
					<?php } ?>
					<?php if (!empty($settings['cookies_policy_page'])) { ?>
						<a href="<?php echo esc_url(get_permalink($settings['cookies_policy_page'])); ?>"  target="_blank"><?php echo esc_attr($settings['cookies_policy_link_text']); ?></a>
					<?php } ?>
				</div>
			</div>
		</form>
	</div>
</div>