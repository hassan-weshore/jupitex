<?php

$consent_bar = $this->options[TheGemGdpr::TYPE_CONSENT_BAR];

?>

<div class="gdpr-consent-bar <?php echo esc_attr($consent_bar['position']); ?>">
	<div class="gdpr-consent-bar-box">
		<div class="gdpr-consent-bar-text"><?php echo nl2br(wp_kses(wp_unslash($consent_bar['text']), TheGemGdpr::get_allowed_html_tags())); ?></div>
		<div class="gdpr-consent-bar-buttons">
			<button type="button" class="btn-gdpr-preferences-open"><?php echo esc_html($consent_bar['privacy_preferences_link_text']); ?></button>
			<button type="button" class="btn-gdpr-agreement"><?php echo esc_html($consent_bar['i_agree_button_text']); ?></button>
		</div>
	</div>
</div>