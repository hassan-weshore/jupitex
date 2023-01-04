<form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>" novalidate="novalidate">
	<?php settings_fields(TheGemGdpr::OPTION_GROUP.'_'.$this->get_type()); ?>

	<h2><?php _e('General Settings', 'thegem'); ?></h2>
	<div class="thegem-gdpr-form-box">
		<div class="thegem-gdpr-field-box">
			<?php $field = 'privacy_policy_page'; ?>
			<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Privacy Policy Page', 'thegem'); ?></label>
			<div class="field">
				<select name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>">
					<option value="0"><?php _e('— Select —', 'thegem'); ?></option>
					<?php foreach (get_pages() as $page ) { ?>
						<option value="<?php echo esc_attr($page->ID); ?>" <?php selected($this->get_field_value($field), $page->ID); ?>><?php echo esc_html($page->post_title ); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="thegem-gdpr-field-box">
			<?php $field = 'privacy_policy_link_text'; ?>
			<?php $default_value = __('Privacy Policy', 'thegem'); ?>
			<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Privacy Policy Link Text', 'thegem'); ?></label>
			<div class="field">
				<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" id="<?php echo $this->get_field_id($field); ?>" type="text" >
			</div>
		</div>
		<div class="thegem-gdpr-field-box">
			<?php $field = 'cookies_policy_page'; ?>
			<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Cookies Policy Page', 'thegem'); ?></label>
			<div class="field">
				<select name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>">
					<option value="0"><?php _e('— Select —', 'thegem'); ?></option>
					<?php foreach (get_pages() as $page ) { ?>
						<option value="<?php echo esc_attr($page->ID); ?>" <?php selected($this->get_field_value($field), $page->ID); ?>><?php echo esc_html($page->post_title); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="thegem-gdpr-field-box">
			<?php $field = 'cookies_policy_link_text'; ?>
			<?php $default_value = __('Cookies Policy', 'thegem'); ?>
			<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Cookies Policy Link Text', 'thegem'); ?></label>
			<div class="field">
				<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text"  id="<?php echo $this->get_field_id($field); ?>">
			</div>
		</div>
		<div class="thegem-gdpr-field-box">
			<?php $field = 'modal_title'; ?>
			<?php $default_value = __('Privacy Preferences', 'thegem'); ?>
			<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Privacy Preferences Modal Title', 'thegem'); ?></label>
			<div class="field">
				<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>" >
			</div>
		</div>
		<div class="thegem-gdpr-field-box">
			<?php $field = 'excerpt_text'; ?>
			<?php $default_value = __('When you visit our website, it may store information through your browser from specific services, usually in form of cookies. Here you can change your privacy preferences. Please note that blocking some types of cookies may impact your experience on our website and the services we offer.', 'thegem'); ?>
			<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Privacy Excerpt Text For Privacy Modal', 'thegem'); ?></label>
			<div class="field">
				<textarea name="<?php echo $this->get_field_name($field); ?>" rows="6"  id="<?php echo $this->get_field_id($field); ?>"><?php echo esc_attr($this->get_field_value($field, $default_value)); ?></textarea>
			</div>
		</div>
		<div class="thegem-gdpr-field-box">
			<?php $field = 'notice_text'; ?>
			<?php $default_value = __('This content is blocked. Please review your [gem_privacy_settings_link]Privacy Preferences[/gem_privacy_settings_link].', 'thegem')?>
			<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Consent Notice Text In Content', 'thegem'); ?></label>
			<div class="field">
				<textarea name="<?php echo $this->get_field_name($field); ?>" rows="6"  id="<?php echo $this->get_field_id($field); ?>"><?php echo esc_attr($this->get_field_value($field, $default_value)); ?></textarea>
			</div>
			<p class="thegem-gdpr-field-notice"><?php _e('Important: No HTML code is allowed. Use the shortcode [gem_privacy_settings_link]Your Text[/gem_privacy_settings_link] to print a link that opens the preferences box.', 'thegem') ?></p>
		</div>
		<div class="thegem-gdpr-field-box">
			<?php $field = 'save_preferences_button_text'; ?>
			<?php $default_value = __('Save Preferences', 'thegem'); ?>
			<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('"Save Preferences" Button Text', 'thegem'); ?></label>
			<div class="field">
				<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>" >
			</div>
		</div>
	</div>

	<br>
	<br>

	<h2><?php _e('Consents', 'thegem'); ?></h2>

	<div class="thegem-gdpr-consent-type-added thegem-gdpr-field-box">
		<div class="field">
			<select name="thegem-gdpr-consent-type" id="thegem-gdpr-consent-type-select" data-prefix="<?php echo $this->get_field_id(array('consents')); ?>">
				<option value=""><?php _e('— Select —', 'thegem') ?></option>
				<?php foreach (TheGemGdpr::get_consent_type_list() as $consent_type=>$consent_name ) { ?>
					<option value="<?php echo esc_attr($consent_type) ?>" data-title="<?php echo esc_html($consent_name); ?>"><?php echo esc_html($consent_name); ?></option>
				<?php } ?>
			</select>
		</div>
		<button type="button" class="button thegem-gdpr-consent-add"><?php _e('Add', 'thegem')?></button>
	</div>

	<div class="thegem-gdpr-consent-type-items">
		<?php
			$page_options = $this->get_page_options();
			if (!empty($page_options)) {
				$consents = $page_options['consents'];
			} else {
				$consent_type_list = TheGemGdpr::get_consent_type_list();
				$consents_description_default_value = $this->get_consents_description_default_value();
				$consents =  array(
					TheGemGdpr::CONSENT_NAME_PRIVACY_POLICY=>array(
						'title' => $consent_type_list[TheGemGdpr::CONSENT_NAME_PRIVACY_POLICY],
						'description' => $consents_description_default_value[TheGemGdpr::CONSENT_NAME_PRIVACY_POLICY]
					)
				);
			}
		?>
		<?php if (!empty($consents)) { ?>
			<?php foreach ($consents as $consent_type=>$consent_item) { ?>
				<div class="thegem-gdpr-consent-type-item" id="<?php echo $this->get_field_id(array('consents', $consent_type)); ?>">
					<div class="thegem-gdpr-consent-type-head">
						<div class="thegem-gdpr-consent-type-title"><?php echo esc_attr($consent_item['title']) ?></div>
						<button class="thegem-gdpr-consent-type-delete button" type="button"><?php _e('Remove', 'thegem'); ?></button>
					</div>
					<div class="thegem-gdpr-consent-type-body">
						<div class="thegem-gdpr-form-box">
							<?php if (in_array($consent_type, $this->get_consents_with_field_required())) { ?>
								<div class="thegem-gdpr-field-box">
									<?php $field = array('consents', $consent_type, 'required'); ?>
									<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Required', 'thegem'); ?></label>
									<div class="field thegem-gdpr-consent-required-field">
										<input name="<?php echo $this->get_field_name($field); ?>" <?php checked($this->get_field_value($field)); ?> id="<?php echo $this->get_field_id($field); ?>" type="checkbox">
									</div>
								</div>
							<?php } ?>

							<?php if (in_array($consent_type, $this->get_consents_with_field_title())) { ?>
								<div class="thegem-gdpr-field-box thegem-gdpr-consent-title-field">
									<?php $field = array('consents', $consent_type, 'title'); ?>
									<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Consent title', 'thegem'); ?></label>
									<div class="field">
										<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field)) ?>" id="<?php echo $this->get_field_id($field); ?>" type="text">
									</div>
								</div>
							<?php } ?>

							<?php if (in_array($consent_type, $this->get_consents_with_field_state())) { ?>
								<div class="thegem-gdpr-field-box thegem-gdpr-consent-state-field <?php echo ($this->get_field_value($field) ? 'hide' : ''); ?>">
									<?php $field = array('consents', $consent_type, 'state'); ?>
									<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Active by default', 'thegem'); ?></label>
									<div class="field">
										<input name="<?php echo $this->get_field_name($field); ?>" <?php checked($this->get_field_value($field)); ?> id="<?php echo $this->get_field_id($field); ?>" type="checkbox">
									</div>
								</div>
							<?php } ?>

							<div class="thegem-gdpr-field-box">
								<?php $field = array('consents', $consent_type, 'description'); ?>
								<?php !empty($consent_item['description']) ? $default_value = $consent_item['description'] : $default_value = ''; ?>
								<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Consent description', 'thegem'); ?></label>
								<div class="field">
									<textarea name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>" rows="3" ><?php echo esc_attr($this->get_field_value($field, $default_value)); ?></textarea>
								</div>
							</div>

							<div class="thegem-gdpr-allowed-tag"><?php echo __('You can use', 'thegem').': '.TheGemGdpr::get_allowed_html_tags_output(); ?></div>

							<?php if (in_array($consent_type, $this->get_consents_with_field_poster())) { ?>
								<div class="thegem-gdpr-field-box">
									<?php $field = array('consents', $consent_type, 'poster'); ?>
									<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Consent Poster', 'thegem'); ?></label>
									<div class="field">
										<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo $this->get_field_value($field); ?>" id="<?php echo $this->get_field_id($field); ?>" class="picture-select" type="text">
									</div>
								</div>
							<?php } ?>

							<?php if ($consent_type == TheGemGdpr::CONSENT_NAME_GOOGLE_FONTS && !TheGemGdprAdmin::is_selected_fallback_fonts()) { ?>
								<p><?php _e('No fallback fonts are selected.', 'thegem'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=fonts-manager')); ?>" target="_blank"><?php _e('Setup fallback fonts', 'thegem'); ?></a></p>
							<?php } ?>

						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	</div>

	<br>
	<br>
	<h2><?php _e('Privacy Preferences Overlay Styling', 'thegem'); ?></h2>
	<div class="thegem-gdpr-form-box">
		<div class="thegem-gdpr-field-box">
			<?php $field = 'use_overlay_custom_styles'; ?>
			<label for=""><?php _e('Use custom styles', 'thegem'); ?></label>
			<div class="field">
				<input class="thegem-gdpr-use-overlay-custom-styles" name="<?php echo $this->get_field_name($field); ?>" <?php checked($this->get_field_value($field)); ?> type="checkbox" id="<?php echo $this->get_field_id($field); ?>">
			</div>
		</div>
	</div>

	<div class="thegem-gdpr-overlay-custom-styles-box <?php echo ($this->get_field_value($field) ? 'active' : ''); ?>">
		<div class="thegem-gdpr-form-box">
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'background_color'); ?>
				<?php $default_value = '#393D50'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Website Background Fill Color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'background_opacity'); ?>
				<?php $default_value = 80; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Website Background Fill Opacity (0-100%)', 'thegem'); ?></label>
				<div class="field fixed-number">
					<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="number" data-min-value="0" data-max-value="100" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'overlay_background_color'); ?>
				<?php $default_value = '#ffffff'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Overlay Background Color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'bottom_bar_background_color'); ?>
				<?php $default_value = '#f0f3f2'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Bottom Bar Background Color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<label><?php _e('Title font', 'thegem'); ?></label>
				<fieldset class="thegem-gdpr-fieldset">
					<div>
						<?php $field = array('overlay_styles', 'title_font', 'font_family'); ?>
						<?php $default_value = 'Montserrat UltraLight'; ?>
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
						<?php $field = array('overlay_styles', 'title_font', 'font_style'); ?>
						<?php $default_value = 'regular'; ?>
						<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Style', 'thegem'); ?></label>
						<div class="field">
							<select name="<?php echo $this->get_field_name($field); ?>" id="<?php echo $this->get_field_id($field); ?>" class="thegem-gdpr-select-font-style">
								<option value=""></option>
								<?php foreach (TheGemGdprAdmin::get_font_styles($font_family_value) as $font_variant=>$font_variant_title) { ?>
									<option value="<?php echo esc_attr($font_variant); ?>" <?php selected($this->get_field_value($field, $default_value), $font_variant); ?>><?php echo esc_html($font_variant_title); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div>
						<?php $field = array('overlay_styles', 'title_font', 'font_sets'); ?>
						<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Sets', 'thegem'); ?></label>
						<div class="field">
							<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>" data-value="<?php echo implode(',', TheGemGdprAdmin::get_font_sets($font_family_value)); ?>" class="thegem-gdpr-select-font-sets">
							<button type="button" class="btn-get-all-sets-font button"><?php _e('Get all from font', 'thegem') ?></button>
						</div>
					</div>
					<div>
						<?php $field = array('overlay_styles', 'title_font', 'font_size'); ?>
						<?php $default_value = 24; ?>
						<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Size', 'thegem'); ?></label>
						<div class="field fixed-number">
							<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" data-min-value="10" data-max-value="100" type="number" id="<?php echo $this->get_field_id($field); ?>">
						</div>
					</div>
					<div>
						<?php $field = array('overlay_styles', 'title_font', 'line_height'); ?>
						<?php $default_value = 38; ?>
						<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Line Height', 'thegem'); ?></label>
						<div class="field fixed-number">
							<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" data-min-value="10" data-max-value="150" type="number" id="<?php echo $this->get_field_id($field); ?>">
						</div>
					</div>
				</fieldset>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'title_color'); ?>
				<?php $default_value = '#3c3950'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Title color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'title_icon_color'); ?>
				<?php $default_value = '#00bcd4'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Title icon color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<label><?php _e('Text font', 'thegem'); ?></label>
				<fieldset class="thegem-gdpr-fieldset">
					<div>
						<?php $field = array('overlay_styles', 'text_font', 'font_family'); ?>
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
						<?php $field = array('overlay_styles', 'text_font', 'font_style'); ?>
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
						<?php $field = array('overlay_styles', 'text_font', 'font_sets'); ?>
						<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Sets', 'thegem'); ?></label>
						<div class="field">
							<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>" data-value="<?php echo implode(',', TheGemGdprAdmin::get_font_sets($font_family_value)); ?>" class="thegem-gdpr-select-font-sets">
							<button type="button" class="btn-get-all-sets-font button"><?php _e('Get all from font', 'thegem') ?></button>
						</div>
					</div>
					<div>
						<?php $field = array('overlay_styles', 'text_font', 'font_size'); ?>
						<?php $default_value = 14; ?>
						<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Font Size', 'thegem'); ?></label>
						<div class="field fixed-number">
							<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" data-min-value="10" data-max-value="100" type="number" id="<?php echo $this->get_field_id($field); ?>">
						</div>
					</div>
					<div>
						<?php $field = array('overlay_styles', 'text_font', 'line_height'); ?>
						<?php $default_value = 23; ?>
						<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Line Height', 'thegem'); ?></label>
						<div class="field fixed-number">
							<input name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" data-min-value="10" data-max-value="150" type="number" id="<?php echo $this->get_field_id($field); ?>">
						</div>
					</div>
				</fieldset>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'text_color'); ?>
				<?php $default_value = '#5f727f'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Text color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'separator_color'); ?>
				<?php $default_value = '#dfe5e8'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Separator color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'switch_disabled_background_color'); ?>
				<?php $default_value = '#b6c6c9'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('"Disabled" background color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'switch_enabled_background_color'); ?>
				<?php $default_value = '#00bcd4'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('"Enabled" background color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>
			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'button_text_color'); ?>
				<?php $default_value = '#00bcd4'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button text color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>

			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'button_text_hover_color'); ?>
				<?php $default_value = '#f0f3f2'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button text hover color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>

			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'button_background_color'); ?>
				<?php $default_value = '#f0f3f2'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button background color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>

			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'button_background_hover_color'); ?>
				<?php $default_value = '#00bcd4'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button background hover color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>

			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'button_border_color'); ?>
				<?php $default_value = '#00bcd4'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button border color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>

			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'button_border_hover_color'); ?>
				<?php $default_value = '#00bcd4'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Button border hover color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>

			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'link_color'); ?>
				<?php $default_value = '#00bcd4'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Link color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>

			<div class="thegem-gdpr-field-box">
				<?php $field = array('overlay_styles', 'link_hover_color'); ?>
				<?php $default_value = '#00bcd4'; ?>
				<label for="<?php echo $this->get_field_id($field); ?>"><?php _e('Link hover color', 'thegem'); ?></label>
				<div class="field">
					<input class="thegem-gdpr-color-picker" name="<?php echo $this->get_field_name($field); ?>" value="<?php echo esc_attr($this->get_field_value($field, $default_value)); ?>" type="text" id="<?php echo $this->get_field_id($field); ?>">
				</div>
			</div>

		</div>
	</div>

	<?php submit_button(); ?>
</form>

<div id="thegem-gdpr-consent-type-item-template">
	<div class="thegem-gdpr-consent-type-item" id="<?php echo $this->get_field_id(array('consents')); ?>-{{type}}">
		<div class="thegem-gdpr-consent-type-head">
			<div class="thegem-gdpr-consent-type-title">{{title}}</div>
			<button class="thegem-gdpr-consent-type-delete button" type="button"><?php _e('Remove', 'thegem'); ?></button>
		</div>
		<div class="thegem-gdpr-consent-type-body">
			<?php $fieldGroup = 'consents'; ?>
			<div class="thegem-gdpr-form-box">
				<div class="thegem-gdpr-field-box thegem-gdpr-consent-required-field">
					<?php $field = 'required'; ?>
					<label for="<?php echo $this->get_field_id($fieldGroup); ?>-{{type}}-<?php echo $field; ?>"><?php _e('Required', 'thegem'); ?></label>
					<div class="field thegem-gdpr-consent-required-field">
						<input class="thegem-gdpr-consent-state-field-required" name="<?php echo $this->get_field_name($fieldGroup); ?>[{{type}}][<?php echo $field; ?>]" id="<?php echo $this->get_field_id($fieldGroup); ?>-{{type}}-<?php echo $field; ?>" type="checkbox">
					</div>
				</div>
				<div class="thegem-gdpr-field-box thegem-gdpr-consent-state-field">
					<?php $field = 'state'; ?>
					<label for="<?php echo $this->get_field_id($fieldGroup) ?>-{{type}}-<?php echo $field; ?>"><?php _e('Active by default', 'thegem'); ?></label>
					<div class="field">
						<input name="<?php echo $this->get_field_name($fieldGroup); ?>[{{type}}][<?php echo $field; ?>]" id="<?php echo $this->get_field_id($fieldGroup) ?>-{{type}}-<?php echo $field; ?>" type="checkbox">
					</div>
				</div>
				<div class="thegem-gdpr-field-box thegem-gdpr-consent-title-field">
					<?php $field = 'title'; ?>
					<label for="<?php echo $this->get_field_id($fieldGroup); ?>-{{type}}-<?php echo $field; ?>"><?php _e('Consent title', 'thegem'); ?></label>
					<div class="field">
						<input name="<?php echo $this->get_field_name($fieldGroup); ?>[{{type}}][<?php echo $field; ?>]" type="text" id="<?php echo $this->get_field_id($fieldGroup); ?>-{{type}}-<?php echo $field; ?>">
					</div>
				</div>
				<div class="thegem-gdpr-field-box">
					<?php $field = 'description'; ?>
					<label for="<?php echo $this->get_field_id($fieldGroup); ?>-{{type}}-<?php echo $field; ?>"><?php _e('Consent description', 'thegem'); ?></label>
					<div class="field">
						<textarea name="<?php echo $this->get_field_name($fieldGroup); ?>[{{type}}][<?php echo $field; ?>]" id="<?php echo $this->get_field_id($fieldGroup); ?>-{{type}}-<?php echo $field; ?>" rows="3" class="thegem-gdpr-consent-description-value"></textarea>
					</div>
				</div>
				<div class="thegem-gdpr-allowed-tag"><?php echo __('You can use', 'thegem').': '.TheGemGdpr::get_allowed_html_tags_output(); ?></div>
				<div class="thegem-gdpr-field-box thegem-gdpr-consent-poster-field">
					<?php $field = 'poster'; ?>
					<label for="<?php echo $this->get_field_id($fieldGroup); ?>-{{type}}-<?php echo $field; ?>"><?php _e('Consent Poster', 'thegem'); ?></label>
					<div class="field">
						<input name="<?php echo $this->get_field_name($fieldGroup); ?>[{{type}}][<?php echo $field; ?>]" id="<?php echo $this->get_field_id($fieldGroup); ?>-{{type}}-<?php echo $field; ?>" class="picture-select" type="text">
					</div>
				</div>
				<?php if (!TheGemGdprAdmin::is_selected_fallback_fonts()) { ?>
					<p class="thegem-gdpr-consent-google-fonts-notice"><?php _e('No fallback fonts are selected.', 'thegem'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=fonts-manager')); ?>" target="_blank"><?php _e('Setup fallback fonts', 'thegem'); ?></a></p>
				<?php } ?>
			</div>
		</div>
	</div>
</div>