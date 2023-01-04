<div class="wrap thegem-gdpr-admin-index ui-no-theme">
	<h1><?php _e('Privacy & GDPR', 'thegem'); ?></h1>

	<?php settings_errors(); ?>

	<ul class="thegem-gdpr-admin-tabs">
		<li><a <?php echo checked('', $type, false) ? 'class="active"' : ''; ?> href="<?php echo $this->get_plugin_url() ?>"><?php _e('Cookies Consent Bar', 'thegem'); ?></a></li>
		<li><a <?php echo checked(TheGemGdpr::TYPE_INTEGRATIONS, $type, false) ? 'class="active"' : ''; ?> href="<?php echo $this->get_plugin_url(TheGemGdpr::TYPE_INTEGRATIONS) ?>"><?php _e('Forms', 'thegem'); ?></a></li>
		<li><a <?php echo checked(TheGemGdpr::TYPE_SETTINGS, $type, false) ? 'class="active"' : ''; ?> href="<?php echo $this->get_plugin_url(TheGemGdpr::TYPE_SETTINGS) ?>"><?php _e('Privacy Preferences & Consents', 'thegem'); ?></a></li>
	</ul>

	<div class="thegem-gdpr-admin-content">
		<?php
			switch ($type) {
				case TheGemGdpr::TYPE_INTEGRATIONS:
					$this->init_page(TheGemGdpr::TYPE_INTEGRATIONS);
					include plugin_dir_path(__DIR__).'admin/integrations.php';
					break;
				case TheGemGdpr::TYPE_SETTINGS:
					$this->init_page(TheGemGdpr::TYPE_SETTINGS);
					include plugin_dir_path(__DIR__).'admin/settings.php';
					break;
				default:
					$this->init_page(TheGemGdpr::TYPE_CONSENT_BAR);
					include plugin_dir_path(__DIR__).'admin/consent-bar.php';
					break;
			}
		?>
	</div>
</div>