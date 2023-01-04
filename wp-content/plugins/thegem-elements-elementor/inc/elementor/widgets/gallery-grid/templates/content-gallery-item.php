<?php use Elementor\Icons_Manager; ?>

<li <?php post_class($thegem_classes); ?> style="padding: calc(<?= $settings['container_margin']['size'].$settings['container_margin']['unit'] ?>/2)">
	<div class="wrap gem-wrapbox gem-wrapbox-style-<?php echo esc_attr($thegem_wrap_classes); ?>">
		<?php if ($settings['thegem_elementor_preset'] == '11'): ?>
		<div class="gem-wrapbox-inner">
			<div class="shadow-wrap">
				<?php endif; ?>
				<div class="overlay-wrap">
					<div class="image-wrap <?php if ($settings['thegem_elementor_preset'] == '11'): ?>img-circle<?php endif; ?>">
						<?php
						$thegem_attrs = array('alt' => get_post_meta($thegem_item->ID, '_wp_attachment_image_alt', true), 'style' => 'max-width: 110%');
						thegem_generate_picture($thegem_item->ID, $thegem_size, $thegem_sources, $thegem_attrs);
						?>
					</div>
					<div class="overlay <?php if ($settings['thegem_elementor_preset'] == '11'): ?>img-circle<?php endif; ?>">
						<div class="overlay-circle"></div>
						<?php if ($thegem_single_icon): ?>
							<a href="<?php echo esc_url($thegem_full_image_url[0]); ?>"
							   class="gallery-item-link fancy-gallery"
							   data-fancybox="gallery-<?php echo esc_attr($gallery_uid); ?>">
						<span class="slide-info">
							<?php if (!empty($thegem_item->post_excerpt)) : ?>
								<span class="slide-info-title">
									<?php echo $thegem_item->post_excerpt; ?>
				 				</span>
								<?php if (!empty($thegem_item->post_content)) : ?>
									<span class="slide-info-summary">
										<?php echo $thegem_item->post_content; ?>
									</span>
								<?php endif; ?>
							<?php endif; ?>
						</span>
							</a>
						<?php endif; ?>
						<div class="overlay-content">
							<div class="overlay-content-center">
								<div class="overlay-content-inner">
									<a href="<?php echo esc_url($thegem_full_image_url[0]); ?>"
									   class="icon photo <?php if (!$thegem_single_icon): ?>fancy-gallery<?php endif; ?>"
									   <?php if (!$thegem_single_icon): ?>data-fancybox="gallery-<?php echo esc_attr($gallery_uid); ?>"<?php endif; ?> >
										<?php if (!empty($settings['icon']['value'])) {
											Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
										} else {
											echo '<i class="default"></i>';
										} ?>

										<?php if (!$thegem_single_icon): ?>
											<span class="slide-info">
										<?php if (!empty($thegem_item->post_excerpt)) : ?>
											<span class="slide-info-title ">
												<?php echo $thegem_item->post_excerpt; ?>
											</span>
											<?php if (!empty($thegem_item->post_content)) : ?>
												<span class="slide-info-summary">
													<?php echo $thegem_item->post_content; ?>
												</span>
											<?php endif; ?>
										<?php endif; ?>
									</span>
										<?php endif; ?>
									</a>

									<?php if (!empty($thegem_attachment_link)): ?>
										<a href="<?php echo esc_url($thegem_attachment_link); ?>" target="_blank"
										   class="icon link "><i class="default"></i></a>
									<?php endif; ?>
									<?php if (!empty($settings['icon']['value']) || !empty($thegem_attachment_link)) { ?>
										<div class="overlay-line"></div>
									<?php } ?>

									<?php if (!empty($thegem_item->post_excerpt)) : ?>
										<div class="title">
											<?php echo $thegem_item->post_excerpt; ?>
										</div>
									<?php endif; ?>
									<?php if (!empty($thegem_item->post_content)) : ?>
										<div class="subtitle">
											<?php echo $thegem_item->post_content; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php if ($settings['thegem_elementor_preset'] == '11'): ?>
			</div>
		</div>
	<?php endif; ?>
	</div>
	<?php if ($settings['layout'] == 'metro' && $settings['thegem_elementor_preset']): ?><?php endif; ?>
</li>
