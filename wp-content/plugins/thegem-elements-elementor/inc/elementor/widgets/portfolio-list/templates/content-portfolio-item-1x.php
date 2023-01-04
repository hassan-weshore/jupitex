<?php use Elementor\Icons_Manager; ?>

<div <?php post_class($thegem_classes); ?> data-sort-date="<?php echo get_the_date('U'); ?>">
	<div class="wrap clearfix">
		<div <?php post_class($thegem_image_classes); ?>>
			<div class="image-inner">
				<?php if (has_post_thumbnail()) { ?>
					<img src="<?php echo esc_url($thegem_small_image_url[0]); ?>"
						 width="<?php echo esc_attr($thegem_small_image_url[1]); ?>"
						 height="<?php echo esc_attr($thegem_small_image_url[2]); ?>" alt="<?php the_title(); ?>"/>
				<?php }?>
			</div>
			<div class="overlay">
				<div class="overlay-circle"></div>
				<?php if (count($thegem_portfolio_item_data['types']) == 1 && $settings['social_sharing'] != 'yes'): ?>
					<?php
					$thegem_ptype = reset($thegem_portfolio_item_data['types']);
					if ($thegem_ptype['type'] == 'full-image') {
						$thegem_link = $thegem_large_image_url[0];
					} elseif ($thegem_ptype['type'] == 'self-link') {
						$thegem_link = get_permalink();
						$thegem_bottom_line = true;
						$thegem_portfolio_button_link = $thegem_link;
					} elseif ($thegem_ptype['type'] == 'youtube') {
						$thegem_link = '//www.youtube.com/embed/' . $thegem_ptype['link'] . '?autoplay=1';
					} elseif ($thegem_ptype['type'] == 'vimeo') {
						$thegem_link = '//player.vimeo.com/video/' . $thegem_ptype['link'] . '?autoplay=1';
					} else {
						$thegem_link = $thegem_ptype['link'];
					}
					if (!$thegem_link) {
						$thegem_link = '#';
					}
					if ($thegem_ptype['type'] == 'self_video') {
						$thegem_self_video = $thegem_ptype['link'];
						wp_enqueue_style('wp-mediaelement');
						wp_enqueue_script('thegem-mediaelement');
					}

					?>
					<a href="<?php echo esc_url($thegem_link); ?>"
					   target="<?php echo esc_attr($thegem_ptype['link_target']); ?>"
					   class="portolio-item-link <?php echo esc_attr($thegem_ptype['type']); ?> <?php if ($thegem_ptype['type'] == 'full-image') echo 'fancy'; ?>"></a>
				<?php endif; ?>
				<div class="links-wrapper">
					<div class="links">
						<?php if ($settings['icons_show'] == 'yes') { ?>
							<div class="portfolio-icons">
								<?php foreach ($thegem_portfolio_item_data['types'] as $thegem_ptype): ?>
									<?php
									if ($thegem_ptype['type'] == 'full-image') {
										$thegem_link = $thegem_large_image_url[0];
									} elseif ($thegem_ptype['type'] == 'self-link') {
										$thegem_link = get_permalink();
										$thegem_bottom_line = true;
										$thegem_portfolio_button_link = $thegem_link;
									} elseif ($thegem_ptype['type'] == 'youtube') {
										$thegem_link = '//www.youtube.com/embed/' . $thegem_ptype['link'] . '?autoplay=1';
									} elseif ($thegem_ptype['type'] == 'vimeo') {
										$thegem_link = '//player.vimeo.com/video/' . $thegem_ptype['link'] . '?autoplay=1';
									} else {
										$thegem_link = $thegem_ptype['link'];
									}
									if (!$thegem_link) {
										$thegem_link = '#';
									}
									if ($thegem_ptype['type'] == 'self_video') {
										$thegem_self_video = $thegem_ptype['link'];
										wp_enqueue_style('wp-mediaelement');
										wp_enqueue_script('thegem-mediaelement');
									}

									if ($thegem_ptype['type'] == 'youtube' || $thegem_ptype['type'] == 'vimeo' || $thegem_ptype['type'] == 'self_video') {
										$link_icon = 'video';
									} else {
										$link_icon = $thegem_ptype['type'];
									}
									?>
									<a href="<?php echo esc_url($thegem_link); ?>"
									   target="<?php echo esc_attr($thegem_ptype['link_target']); ?>"
									   class="icon <?php echo esc_attr($thegem_ptype['type']); ?> <?php echo esc_attr($link_icon); ?> <?php if ($thegem_ptype['type'] == 'full-image' && (count($thegem_portfolio_item_data['types']) > 1 || $settings['social_sharing'] == 'yes')) echo 'fancy'; ?>">
										<?php if ($settings['hover_icon_' . $link_icon]['value']) {
											Icons_Manager::render_icon($settings['hover_icon_' . $link_icon], ['aria-hidden' => 'true']);
										} else { ?>
											<i class="default"></i>
										<?php } ?>
									</a>
								<?php endforeach; ?>
								<?php if ($settings['social_sharing'] == 'yes'): ?>
									<a href="javascript: void(0);" class="icon share">
										<?php if ($settings['hover_icon_share']['value']) {
											Icons_Manager::render_icon($settings['hover_icon_share'], ['aria-hidden' => 'true']);
										} else { ?>
											<i class="default"></i>
										<?php } ?>
									</a>
								<?php endif; ?>
								<div class="overlay-line"></div>
								<?php if ($settings['social_sharing'] == 'yes'): ?>
									<div class="portfolio-sharing-pane"><?php include 'socials-sharing.php'; ?></div>
								<?php endif; ?>
							</div>
						<?php } ?>
						<?php if ($settings['caption_position'] == 'image' || $settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular'): ?>
							<div class="caption">
								<?php if ($settings['portfolio_show_title'] == 'yes') { ?>
									<div class="title title-h4">
										<?php if ($hover_effect != 'default' && $hover_effect != 'gradient' && $hover_effect != 'circular') {
											echo '<span class="light">';
										} ?>
										<?php if (!empty($thegem_portfolio_item_data['overview_title'])) : ?>
											<?php echo $thegem_portfolio_item_data['overview_title']; ?>
										<?php else : ?>
											<?php the_title(); ?>
										<?php endif; ?>
										<?php if ($settings['image_hover_effect'] != 'default') {
											echo '</span>';
										} ?>
									</div>
								<?php } ?>
								<div class="description">
									<?php if ($settings['portfolio_show_description'] == 'yes' && has_excerpt()) : ?>
										<div class="subtitle"><?php the_excerpt(); ?></div>
									<?php endif; ?>
									<?php if ($settings['portfolio_show_date'] == 'yes' || $settings['portfolio_show_sets'] == 'yes'): ?>
										<div class="info">
											<?php if ($settings['portfolio_show_date'] == 'yes') {
												echo '<span class="date">' . get_the_date('j F, Y') . '</span>';
											}
											if ($settings['portfolio_show_date'] == 'yes' && $settings['portfolio_show_sets'] == 'yes') {
												echo(' ');
											}
											if ($settings['portfolio_show_sets'] == 'yes') {
												echo '<span class="set">';
												foreach ($slugs as $thegem_k => $thegem_slug) {
													if (in_array($thegem_slug, $terms)) {
														$term = get_term_by('slug', $thegem_slug, 'thegem_portfolios');
														if ($term) {
															echo '<a data-slug="' . $term->slug . '">' . $term->name . '</a>';
														}
													}
												}
												echo '</span>';
											} ?>
										</div>
									<?php endif ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php if ($settings['caption_position'] != 'image' && $settings['image_hover_effect'] != 'gradient' && $settings['image_hover_effect'] != 'circular'): ?>
			<div <?php post_class($thegem_caption_classes); ?>>
				<div class="caption-sizable-content<?php echo($thegem_bottom_line ? ' with-bottom-line' : ''); ?>">
					<?php if ($settings['portfolio_show_title'] == 'yes') { ?>
						<div class="title title-h3"><span class="light">
						<?php if (!empty($thegem_portfolio_item_data['overview_title'])) : ?>
							<?php echo $thegem_portfolio_item_data['overview_title']; ?>
						<?php else : ?>
							<?php the_title(); ?>
						<?php endif; ?>
					</span></div>
					<?php } ?>
					<?php if ($settings['portfolio_show_date'] == 'yes' || $settings['portfolio_show_likes'] == 'yes'): ?>
						<div class="info clearfix">
							<?php if ($settings['portfolio_show_date'] == 'yes') { ?>
								<div class="caption-separator-line"><span
										class="date"><?php echo get_the_date('j F, Y'); ?></span></div><?php }
							if ($settings['portfolio_show_likes'] == 'yes' && function_exists('zilla_likes')) {
								?><div class="caption-separator-line-hover"><?php
								if ($settings['portfolio_show_date'] == 'yes') { ?><span class="sep"></span><?php }
								?><span class="post-meta-likes portfolio-list-likes"><?php
										if ($settings['likes_icon']['value']) {
											Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);
										} else { ?><i class="default"></i><?php }
										zilla_likes();
										?></span>
								</div>
							<?php } ?>
						</div>
					<?php endif; ?>
					<?php
					if ($settings['portfolio_show_description'] == 'yes') {
						if (has_excerpt()) : ?>
							<div class="subtitle"><?php the_excerpt(); ?></div>
						<?php elseif ($thegem_title_data['title_excerpt']) : ?>
							<div class="subtitle"><?php echo nl2br($thegem_page_data['title_excerpt']); ?></div>
						<?php endif;
					} ?>
					<?php if ($settings['portfolio_show_sets'] == 'yes'): ?>
						<div class="info">
							<?php
							echo '<span class="set">';
							if (count($slugs) > 0) {

								echo('<span class="in_text">' . $in_text . '</span> ');
							}
							$thegem_index = 0;
							foreach ($slugs as $thegem_k => $thegem_slug) {
								if (in_array($thegem_slug, $terms)) {
									$term = get_term_by('slug', $thegem_slug, 'thegem_portfolios');
									if ($term) {
										echo ($thegem_index > 0 ? '<span class="portfolio-set-comma">,</span> ' : '') . '<a data-slug="' . $term->slug . '">' . $term->name . '</a>';
										$thegem_index++;
									}
								}
							}
							echo '</span>';
							?>
						</div>
					<?php endif; ?>
					<div class="after-overlay"></div>
				</div>
				<div class="caption-bottom-line">
					<?php if ($thegem_portfolio_item_data['project_link']) {
						thegem_button(array('size' => 'tiny', 'href' => $thegem_portfolio_item_data['project_link'], 'text' => ($thegem_portfolio_item_data['project_text'] ? $thegem_portfolio_item_data['project_text'] : __('Launch', 'thegem')), 'extra_class' => 'project-button'), 1);
					} ?>
					<?php if ($settings['portfolio_show_details'] == 'yes' && $thegem_portfolio_button_link) { ?>
						<div class="details-button gem-button-container gem-widget-button gem-button-position-inline">
							<a href="<?= get_permalink() ?>" <?php post_class($button_classes); ?>>
								<span class="gem-inner-wrapper-btn">
									<span class="gem-text-button">
										<?php echo '<span>' . wp_kses($settings['details_button_text'], 'post') . '</span>'; ?>
									</span>
								</span>
							</a>
						</div>
					<?php } ?>
					<?php if ($settings['social_sharing'] == 'yes'): ?>
						<div class="post-footer-sharing">
							<div class="gem-button-container gem-widget-button gem-button-position-inline">
								<?php thegem_button(array('icon' => 'share', 'size' => 'tiny'), 1); ?>
							</div>
							<div class="sharing-popup"><?php include 'socials-sharing.php'; ?>
								<svg class="sharing-styled-arrow">
									<use xlink:href="<?php echo esc_url(get_template_directory_uri() . '/css/post-arrow.svg'); ?>#dec-post-arrow"></use>
								</svg>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
