<?php use Elementor\Icons_Manager; ?>

<?php if (!isset($portfolio_item_size)): ?>
	<div <?php post_class($thegem_classes); ?> style="padding: calc(<?= $settings['image_gaps']['size'].$settings['image_gaps']['unit'] ?>/2)" data-default-sort="<?php echo intval(get_post()->menu_order); ?>"
											   data-sort-date="<?php echo get_the_date('U'); ?>">
		<div class="wrap clearfix">
			<div <?php post_class($thegem_image_classes); ?>>
				<div class="image-inner">
					<?php thegem_post_picture($thegem_size, $thegem_sources, array('alt' => get_the_title(), 'style' => 'max-width: 110%')); ?>
				</div>
				<div class="overlay">
					<div class="overlay-circle"></div>
					<?php if ($settings['icons_show'] !== 'yes'): ?>
						<?php
						$thegem_ptype = reset($thegem_portfolio_item_data['types']);
						if ($thegem_ptype['type'] == 'full-image') {
							$thegem_link = $thegem_large_image_url[0];
						} elseif ($thegem_ptype['type'] == 'self-link') {
							$thegem_link = get_permalink();
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
						} ?>
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
										   class="icon <?php echo esc_attr($thegem_ptype['type']); ?> <?php echo esc_attr($link_icon); ?> <?php if ($thegem_ptype['type'] == 'full-image') echo 'fancy'; ?>">
											<?php if ($settings['hover_icon_' . $link_icon] && $settings['hover_icon_' . $link_icon]['value']) {
												Icons_Manager::render_icon($settings['hover_icon_' . $link_icon], ['aria-hidden' => 'true']);
											} else { ?>
												<i class="default"></i>
											<?php } ?>
										</a>
									<?php endforeach; ?>
									<?php if ($settings['social_sharing'] == 'yes'): ?>
										<a href="javascript: void(0);" class="icon share">
											<?php if ($settings['hover_icon_share'] && $settings['hover_icon_share']['value']) {
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

							<?php if (($settings['caption_position'] == 'hover' && $settings['columns'] != '1x') || $hover_effect == 'gradient' || $hover_effect == 'circular'): ?>
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
											<?php if ($hover_effect != 'default') {
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
												<?php if ($settings['columns'] == '1x'): ?>
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
																	echo '<span class="separator">|</span><a data-slug="' . $term->slug . '">' . $term->name . '</a>';
																}
															}
														}
														echo '</span>';
													} ?>
												<?php else: ?>
													<?php if ($settings['portfolio_show_date'] == 'yes') {
														echo '<span class="date">' . get_the_date('j F, Y') . '</span>';
													}
													if ($settings['portfolio_show_date'] == 'yes' && $settings['portfolio_show_sets'] == 'yes') {
														echo(' ');
													}
													if ($settings['portfolio_show_sets'] == 'yes') {
														echo '<span class="set">';
														if (count($slugs) > 0) {
															echo('<span class="in_text">' . $in_text . '</span> ');
														}
														$thegem_index = 0;
														foreach ($slugs as $thegem_k => $thegem_slug) {
															if (in_array($thegem_slug, $terms)) {
																$term = get_term_by('slug', $thegem_slug, 'thegem_portfolios');
																if ($term) {
																	echo ($thegem_index > 0 ? '<span class="sep"></span> ' : '') . '<a data-slug="' . $term->slug . '">' . $term->name . '</a>';
																	$thegem_index++;
																}
															}

														}
														echo '</span>';
													} ?>
												<?php endif; ?>
											</div>
										<?php endif ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php if (($settings['caption_position'] == 'page' || $settings['columns'] == '1x') && $hover_effect != 'gradient' && $hover_effect != 'circular'): ?>
				<div <?php post_class($thegem_caption_classes); ?>>
					<?php if ($settings['portfolio_show_title'] == 'yes'): ?>
						<div class="title">
							<?php if (!empty($thegem_portfolio_item_data['overview_title'])) : ?>
								<?php echo $thegem_portfolio_item_data['overview_title']; ?>
							<?php else : ?>
								<?php the_title(); ?>
							<?php endif; ?>
						</div>
						<div class="caption-separator"></div>
					<?php endif; ?>
					<?php if ($settings['portfolio_show_description'] == 'yes' && has_excerpt()) : ?>
						<div class="subtitle"><?php the_excerpt(); ?></div><?php endif; ?>
					<?php if ($settings['portfolio_show_date'] == 'yes' || $settings['portfolio_show_sets'] == 'yes'): ?>
						<div class="info">
							<?php if ($settings['columns'] == '1x'): ?>
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
												echo '<span class="separator">|</span><a data-slug="' . $term->slug . '">' . $term->name . '</a>';
											}
										}
									}
									echo '</span>';
								} ?>
							<?php else: ?>
								<?php if ($settings['portfolio_show_date'] == 'yes') {
									echo '<span class="date">' . get_the_date('j F, Y') . '</span>';
								}
								if ($settings['portfolio_show_date'] == 'yes' && $settings['portfolio_show_sets'] == 'yes') {
									echo(' ');
								}
								if ($settings['portfolio_show_sets'] == 'yes') {
									echo '<span class="set">';
									if (count($slugs) > 0) {

										echo('<span class="in_text">' . $in_text . '</span> ');
									}
									$thegem_index = 0;
									foreach ($slugs as $thegem_k => $thegem_slug) {
										if (in_array($thegem_slug, $terms)) {
											$term = get_term_by('slug', $thegem_slug, 'thegem_portfolios');
											if ($term) {
												echo ($thegem_index > 0 ? '<span class="sep"></span> ' : '') . '<a data-slug="' . $term->slug . '">' . $term->name . '</a>';
												$thegem_index++;
											}
										}
									}
									echo '</span>';
								} ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ($settings['portfolio_show_likes'] == 'yes' && function_exists('zilla_likes')) {
						echo '<div class="portfolio-likes' . (($settings['portfolio_show_date'] == 'yes' || $settings['portfolio_show_sets'] == 'yes') ? '' : ' visible') . '">';
						if ($settings['likes_icon'] && $settings['likes_icon']['value']) {
							Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);
						} else { ?>
							<i class="default"></i>
						<?php }
						zilla_likes();
						echo '</div>';
					} ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php else: ?>
	<div <?php post_class($thegem_classes); ?>>
	</div>
<?php endif; ?>
