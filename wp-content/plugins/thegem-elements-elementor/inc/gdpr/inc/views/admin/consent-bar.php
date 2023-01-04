<form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>" novalidate="novalidate">
	<?php settings_fields(TheGemGdpr::OPTION_GROUP.'_'.$this->get_type()); ?>

	<div class="thegem-gdpr-form-box">
		<div class="thegem-gdpr-field-box">
			<?php $field = 'active'; ?>
			<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Activate Cookies Consent Bar', 'thegem'); ?></label>
			<div class="field">
				<input class="thegem-gdpr-active-consent-bar" name="<?php echo $this->get_field_name($field); ?>" <?php checked($this->get_field_value($field, 0)); ?> type="checkbox" id="<?php echo $this->get_field_id($field); ?>">
			</div>
		</div>

		<div class="thegem-gdpr-consent-bar-fields <?php echo ($this->get_field_value($field, 0) ? 'active' : '')  ?>" >
			<div class="thegem-gdpr-field-box">
				<?php $field = 'text'; ?>
				<?php $default_value = __('Our website uses cookies from third party services to improve your browsing experience. Read more about this and how you can control cookies by clicking "Privacy Preferences".', 'thegem'); ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Text For Cookies Consent Bar', 'thegem') ?></label>
				<div class="field">
					<textarea name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>" rows="6" ><?php echo esc_attr($this->get_field_value($field, $default_value)); ?></textarea>
				</div>
			</div>

			<div class="thegem-gdpr-allowed-tag"><?php echo __('You can use', 'thegem').': '.TheGemGdpr::get_allowed_html_tags_output(); ?></div>

			<div class="thegem-gdpr-field-box">
				<?php $field = 'privacy_preferences_link_text'; ?>
				<?php $default_value = __('Privacy Preferences', 'thegem'); ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('"Privacy Preferences" Link Text', 'thegem'); ?></label>
				<div class="field">
					<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" id="<?php echo $this->get_field_id($field); ?>" type="text" >
				</div>
			</div>

			<div class="thegem-gdpr-field-box">
				<?php $field = 'i_agree_button_text'; ?>
				<?php $default_value = __('I Agree', 'thegem'); ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('"I Agree" Button Text', 'thegem'); ?></label>
				<div class="field">
					<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" id="<?php echo $this->get_field_id($field); ?>" type="text" >
				</div>
			</div>

			<div class="thegem-gdpr-field-box">
				<?php $field = 'position'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Cookies Consent Bar Position', 'thegem'); ?></label>
				<div class="field">
					<select name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>">
						<?php foreach (TheGemGdpr::get_consent_bar_position_list() as $consent_bar_position=>$consent_bar_position_text ) { ?>
							<option value="<?php echo esc_attr($consent_bar_position); ?>" <?php selected($this->get_field_value($field), $consent_bar_position); ?>><?php echo esc_html($consent_bar_position_text); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<br>
			<br>
			<h2><?php _e('Cookies Consent Bar Styling', 'thegem'); ?></h2>

			<div class="thegem-gdpr-field-box">
				<?php $field = 'use_custom_styles'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Use custom styles', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-use-consent-bar-custom-styles" name="<?php echo $this->get_field_name($field); ?>" <?php checked($this->get_field_value($field)); ?> type="checkbox" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>

			<div class="thegem-gdpr-consent-bar-custom-styles-box <?php echo ($this->get_field_value($field) ? 'active' : ''); ?>">
				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'background_color'); ?>
					<?php $default_value = '#181828'; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Bar Background Color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>
				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'background_opacity'); ?>
					<?php $default_value = 93; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Background Opacity (0-100%)', 'thegem'); ?></label>
					<div class="field fixed-number">
						<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="number" data-min-value="0" data-max-value="100" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

				<div class="thegem-gdpr-field-box">
					<label><?php _e('Text font', 'thegem'); ?></label>
					<fieldset class="thegem-gdpr-fieldset">
						<div>
							<?php $field = array('custom_styles', 'text_font', 'font_family'); ?>
							<?php $default_value = 'Source Sans Pro'; ?>
							<?php $font_family_value = esc_attr($this->get_field_value($field, $default_value)); ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Family', 'thegem'); ?></label>
							<div class="field">
								<select name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>" class="thegem-gdpr-select-font-family">
									<option value=""></option>
									<?php foreach (TheGemGdprAdmin::get_fonts_list() as $font=>$font_name) { ?>
										<option value="<?php echo esc_attr($font); ?>" <?php selected($font_family_value, $font); ?>><?php echo esc_html($font_name); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div>
							<?php $field = array('custom_styles', 'text_font', 'font_style'); ?>
							<?php $default_value = 'regular'; ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Style', 'thegem'); ?></label>
							<div class="field">
								<select name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>" class="thegem-gdpr-select-font-style">
									<option value=""></option>
									<?php foreach (TheGemGdprAdmin::get_font_styles($font_family_value) as $font_variant=>$font_variant_title)  { ?>
										<option value="<?php echo esc_attr($font_variant); ?>" <?php selected($this->get_field_value($field, $default_value), $font_variant); ?>><?php echo esc_html($font_variant_title); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div>
							<?php $field = array('custom_styles', 'text_font', 'font_sets'); ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Sets', 'thegem'); ?></label>
							<div class="field">
								<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>" data-value="<?php echo implode(',', TheGemGdprAdmin::get_font_sets($font_family_value)); ?>" class="thegem-gdpr-select-font-sets">
								<button type="button" class="btn-get-all-sets-font button"><?php _e('Get all from font', 'thegem') ?></button>
							</div>
						</div>
						<div>
							<?php $field = array('custom_styles', 'text_font', 'font_size'); ?>
							<?php $default_value = '14'; ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Size', 'thegem'); ?></label>
							<div class="field fixed-number">
								<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="number" data-min-value="10" data-max-value="100" id="<?php echo $this->get_field_id($field); ?>">
							</div>
						</div>
						<div>
							<?php $field = array('custom_styles', 'text_font', 'line_height'); ?>
							<?php $default_value = '22'; ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Line Height', 'thegem'); ?></label>
							<div class="field fixed-number">
								<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="number" data-min-value="10" data-max-value="150" id="<?php echo $this->get_field_id($field); ?>">
							</div>
						</div>
					</fieldset>
				</div>

				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'text_color'); ?>
					<?php $default_value = '#ffffff'; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Text Color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

				<div class="thegem-gdpr-field-box">
					<label><?php _e('Link font', 'thegem'); ?></label>
					<fieldset class="thegem-gdpr-fieldset">
						<div>
							<?php $field = array('custom_styles', 'link_font', 'font_family'); ?>
							<?php $default_value = 'Source Sans Pro'; ?>
							<?php $font_family_value = esc_attr($this->get_field_value($field, $default_value)); ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Family', 'thegem'); ?></label>
							<div class="field">
								<select name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>" class="thegem-gdpr-select-font-family">
									<option value=""></option>
									<?php foreach (TheGemGdprAdmin::get_fonts_list() as $font=>$font_name) { ?>
										<option value="<?php echo esc_attr($font); ?>" <?php selected($font_family_value, $font); ?>><?php echo esc_html($font_name); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div>
							<?php $field = array('custom_styles', 'link_font', 'font_style'); ?>
							<?php $default_value = 'regular'; ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Style', 'thegem'); ?></label>
							<div class="field">
								<select name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>" class="thegem-gdpr-select-font-style">
									<option value=""></option>
									<?php foreach (TheGemGdprAdmin::get_font_styles($font_family_value) as $font_variant=>$font_variant_title)  { ?>
										<option value="<?php echo esc_attr($font_variant); ?>" <?php selected($this->get_field_value($field, $default_value), $font_variant); ?>><?php echo esc_html($font_variant_title); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div>
							<?php $field = array('custom_styles', 'link_font', 'font_sets'); ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Sets', 'thegem'); ?></label>
							<div class="field">
								<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>" data-value="<?php echo implode(',', TheGemGdprAdmin::get_font_sets($font_family_value)); ?>" class="thegem-gdpr-select-font-sets">
								<button type="button" class="btn-get-all-sets-font button"><?php _e('Get all from font', 'thegem') ?></button>
							</div>
						</div>
						<div>
							<?php $field = array('custom_styles', 'link_font', 'font_size'); ?>
							<?php $default_value = '14'; ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Size', 'thegem'); ?></label>
							<div class="field fixed-number">
								<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="number" data-min-value="10" data-max-value="100" id="<?php echo $this->get_field_id($field); ?>">
							</div>
						</div>
						<div>
							<?php $field = array('custom_styles', 'link_font', 'line_height'); ?>
							<?php $default_value = '22'; ?>
							<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Line Height', 'thegem'); ?></label>
							<div class="field fixed-number">
								<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="number" data-min-value="10" data-max-value="150" id="<?php echo $this->get_field_id($field); ?>">
							</div>
						</div>
					</fieldset>
				</div>

				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'link_color'); ?>
					<?php $default_value = '#00bcd4'; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Link color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'link_hover_color'); ?>
					<?php $default_value = '#ffffff'; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Link hover color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'button_text_color'); ?>
					<?php $default_value = '#ffffff'; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button text color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'button_text_hover_color'); ?>
					<?php $default_value = '#181828'; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button text hover color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'button_background_color'); ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button background color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'button_background_hover_color'); ?>
					<?php $default_value = '#ffffff'; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button background hover color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'button_border_color'); ?>
					<?php $default_value = '#ffffff'; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button border color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

				<div class="thegem-gdpr-field-box">
					<?php $field = array('custom_styles', 'button_border_hover_color'); ?>
					<?php $default_value = '#ffffff'; ?>
					<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button border hover color', 'thegem'); ?></label>
					<div class="field">
						<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
					</div>
				</div>

			</div>
		</div>

	</div>


	<?php submit_button(); ?>
</form>