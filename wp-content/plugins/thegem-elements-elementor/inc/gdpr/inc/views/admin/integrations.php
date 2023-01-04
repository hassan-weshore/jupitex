<form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>" novalidate="novalidate">
	<?php settings_fields(TheGemGdpr::OPTION_GROUP.'_'.$this->get_type()); ?>

	<?php $plugins = TheGemGdprIntegration::get_supported_plugins(); ?>
	<div class="thegem-gdpr-forms">
		<?php if (!empty($plugins)) { ?>
			<?php foreach ($plugins as $plugin) { ?>
				<?php $field = array($plugin['id'], 'enabled'); ?>
				<div class="thegem-gdpr-forms-item <?php echo ($this->get_field_value($field) ? 'active' : ''); ?>">
					<div class="thegem-gdpr-forms-item-head">
						<label for="<?php echo $this->get_field_id($field); ?>"><?php echo $plugin['name']; ?></label>
						<div class="field">
							<input class="thegem-gdpr-forms-item-enabled" name="<?php echo $this->get_field_name($field); ?>" <?php checked($this->get_field_value($field)); ?> type="checkbox" id="<?php echo $this->get_field_id($field); ?>">
						</div>
					</div>
					<div class="thegem-gdpr-forms-item-content">
						<?php if ($plugin['is_supported']) { ?>
							<p class="description"><?php echo esc_attr($plugin['description']); ?></p>
							<?php echo $this->get_supported_plugin_options($plugin['id']); ?>
						<?php } else {
                            echo sprintf(__('This plugin is outdated. %s supports version %s and up.', 'thegem'), $plugin['name'], $plugin['supported_version']);
						} ?>
					</div>
				</div>
			<?php } ?>
		<?php } ?>

		<div class="thegem-gdpr-forms-item <?php echo ($this->get_field_value(array(TheGemGdprWP::ID, 'enabled')) ? 'active' : ''); ?>">
			<div class="thegem-gdpr-forms-item-head">
				<?php $field = array(TheGemGdprWP::ID, 'enabled'); ?>
				<label><?php _e('WordPress Comments', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-forms-item-enabled" name="<?php echo $this->get_field_name($field); ?>" <?php checked($this->get_field_value($field)); ?> type="checkbox" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-forms-item-content">
				<p class="description"><?php _e('When activated the GDPR checkbox will be added automatically just above the submit button.', 'thegem'); ?></p>

				<div class="thegem-gdpr-form-box">
					<div class="thegem-gdpr-field-box">
						<?php $field = array(TheGemGdprWP::ID, 'checkbox_text'); ?>
						<?php $default_value = __('By using this form you agree with the storage and handling of your data by this website.', 'thegem'); ?>
						<label for="<?php echo $this->get_field_id($field)?>"><?php _e('Checkbox text', 'thegem') ?></label>
						<div class="field">
							<textarea name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field)?>" rows="3" ><?php echo esc_attr($this->get_field_value($field, $default_value)); ?></textarea>
						</div>
					</div>
					<div class="thegem-gdpr-allowed-tag"><?php echo __('You can use', 'thegem').': '.TheGemGdpr::get_allowed_html_tags_output(); ?></div>
					<div class="thegem-gdpr-field-box">
						<?php $field = array(TheGemGdprWP::ID, 'error_message'); ?>
						<?php $default_value = __('Please accept the privacy checkbox.', 'thegem'); ?>
						<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Error message', 'thegem'); ?></label>
						<div class="field">
							<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text"  id="<?php echo $this->get_field_id($field); ?>">
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<?php submit_button(); ?>
</form>