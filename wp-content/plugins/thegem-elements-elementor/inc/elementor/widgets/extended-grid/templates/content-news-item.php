<?php use Elementor\Icons_Manager; ?>

<?php if (!isset($portfolio_item_size)): ?>
	<div <?php post_class($thegem_classes); ?> style="padding: calc(<?= $settings['image_gaps']['size'].$settings['image_gaps']['unit'] ?>/2)" data-default-sort="<?php echo intval(get_post()->menu_order); ?>" data-sort-date="<?php echo get_the_date('U'); ?>">
		<?php if ($alternative_highlight_style_enabled): ?>
			<div class="highlight-item-alternate-box">
				<div class="highlight-item-alternate-box-content caption">
					<div class="highlight-item-alternate-box-content-inline">
						<?php if ($settings['blog_show_date'] == 'yes'): ?>
							<div class="post-date"><?php echo get_the_date(); ?></div>
						<?php endif; ?>

						<?php if ($settings['blog_show_title'] == 'yes') { ?>
							<div class="title">
								<?php the_title('<div class="title-' . ($settings['thegem_elementor_preset'] == 'new' ? 'h4' : 'h5') . '">', '</div>'); ?>
							</div>
						<?php } ?>

						<?php if ($settings['thegem_elementor_preset'] == 'default' && $settings['blog_show_categories'] == 'yes'): ?>
							<div class="info">
								<?php
								$thegem_index = 0;

								foreach ($slugs as $thegem_k => $thegem_slug)
									if (isset($thegem_terms_set[$thegem_slug])) {
										echo ($thegem_index > 0 ? '<span class="sep"></span> ': '').'<a data-slug="'.$thegem_terms_set[$thegem_slug]->slug.'">'.$thegem_terms_set[$thegem_slug]->name.'</a>';
										$thegem_index++;
									}
								?>
							</div>
						<?php endif; ?>

						<a href="<?php echo esc_url(get_permalink()); ?>" class="portolio-item-link"></a>
					</div>
				</div>
			</div>
			<style>
				<?php if (!empty($post_item_data['highlight_title_left_background'])): ?>
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative .highlight-item-alternate-box {
					background-color: <?php echo $post_item_data['highlight_title_left_background']; ?>;
				}
				<?php endif; ?>

				<?php if (!empty($post_item_data['highlight_title_left_color'])): ?>
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative .highlight-item-alternate-box .caption .title,
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative .highlight-item-alternate-box .caption .title > *,
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative .highlight-item-alternate-box .caption .post-date,
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative .highlight-item-alternate-box .caption .info a,
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative .highlight-item-alternate-box .caption .info .sep {
					color: <?php echo $post_item_data['highlight_title_left_color']; ?> !important;
				}

				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative .highlight-item-alternate-box .caption .info .sep {
					border-left-color: <?php echo $post_item_data['highlight_title_left_color']; ?>;
				}
				<?php endif; ?>

				<?php if (!empty($post_item_data['highlight_title_right_background'])): ?>
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative.right-item .highlight-item-alternate-box {
					background-color: <?php echo $post_item_data['highlight_title_right_background']; ?>;
				}
				<?php endif; ?>

				<?php if (!empty($post_item_data['highlight_title_right_color'])): ?>
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative.right-item .highlight-item-alternate-box .caption .title,
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative.right-item .highlight-item-alternate-box .caption .title > *,
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative.right-item .highlight-item-alternate-box .caption .post-date,
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative.right-item .highlight-item-alternate-box .caption .info a,
				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative.right-item .highlight-item-alternate-box .caption .info .sep {
					color: <?php echo $post_item_data['highlight_title_right_color']; ?> !important;
				}

				.news-grid .portfolio-item.post-<?php echo get_the_ID(); ?>.double-item-style-alternative.right-item .highlight-item-alternate-box .caption .info .sep {
					border-left-color: <?php echo $post_item_data['highlight_title_right_color']; ?>;
				}
				<?php endif; ?>
			</style>
		<?php endif; ?>

		<div class="wrap clearfix">
			<div <?php post_class($thegem_image_classes); ?>>
				<div class="image-inner">
					<?php if ($settings['layout'] == 'justified'): ?>
						<?php thegem_generate_picture('THEGEM_TRANSPARENT_IMAGE', $thegem_size, array(), array('alt' => get_the_title(), 'style' => 'max-width: 110%')); ?>
					<?php endif; ?>

					<?php if ($settings['layout'] == 'metro' && ($post_format == 'video' || $post_format == 'audio')): ?>
						<?php thegem_generate_picture('THEGEM_TRANSPARENT_IMAGE', 'thegem-news-grid-metro-video', array(), array('alt' => get_the_title(), 'style' => 'max-width: 110%')); ?>
					<?php endif; ?>

					<?php if ($settings['layout'] == 'metro' && $post_format == 'quote'): ?>
						<?php thegem_generate_picture('THEGEM_TRANSPARENT_IMAGE', 'thegem-portfolio-metro-retina', array(), array('alt' => get_the_title(), 'style' => 'max-width: 110%')); ?>
					<?php endif; ?>

					<?php
					if (!isset($portfolio_item_size)) {
						if ($post_format == 'video' && $thegem_has_post_thumbnail) {
							echo '<div class="post-featured-content"><a href="' . esc_url(get_permalink(get_the_ID())) . '">';
							thegem_post_picture($thegem_size, $thegem_sources, array('class' => 'img-responsive', 'alt' => get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true), 'style' => 'max-width: 110%'));
							echo '</a></div>';
						} else {
							echo thegem_get_post_featured_content(get_the_ID(), $thegem_size, false, $thegem_sources);
						}
					}
					?>
				</div>

				<?php if (($post_format != 'video' || $thegem_has_post_thumbnail) && $post_format != 'audio' && $post_format != 'quote' && $post_format != 'gallery'): ?>
					<div class="overlay">
						<div class="overlay-circle"></div>
						<?php if (!isset($portfolio_item_size) && $post_format == 'video' && $thegem_has_post_thumbnail && !empty($post_item_data['video'])): ?>
							<?php
							switch ($post_item_data['video_type']) {
								case 'youtube':
									$thegem_video_link = '//www.youtube.com/embed/' . $post_item_data['video'] . '?autoplay=1';
									$thegem_video_class = 'youtube';
									break;

								case 'vimeo':
									$thegem_video_link = '//player.vimeo.com/video/' . $post_item_data['video'] . '?autoplay=1';
									$thegem_video_class = 'vimeo';
									break;

								default:
									$thegem_video_link = $post_item_data['video'];
									$thegem_video_class = 'self_video';
							}
							?>
							<a href="<?php echo esc_url($thegem_video_link); ?>" class="news-video-icon <?php echo $thegem_video_class; ?>"></a>
						<?php endif; ?>

						<div class="links-wrapper">
							<div class="links">
								<div class="caption">
									<a href="<?php echo esc_url(get_permalink()); ?>" class="portolio-item-link"></a>

									<?php if ($post_format != 'video'): ?>
										<?php if ($settings['caption_position'] == 'page' && $settings['thegem_elementor_preset'] == 'new' && $settings['icon_hover_show'] == 'yes'): ?>
											<div class="portfolio-icons">
												<a href="javascript: void(0);" class="icon self-link">
													<?php if ($settings['icon_hover_icon']['value']) {
														Icons_Manager::render_icon($settings['icon_hover_icon'], ['aria-hidden' => 'true']);
													} else { ?>
														<i class="default"></i>
													<?php } ?>
												</a>
											</div>

											<?php if ($settings['blog_show_categories'] == 'yes' && $post_format != 'quote'): ?>
												<div class="info">
													<?php
													$thegem_index = 0;
													foreach ($slugs as $thegem_k => $thegem_slug)
														if (isset($thegem_terms_set[$thegem_slug])) {
															echo ($thegem_index > 0 ? '<span class="sep"></span> ': '').'<a data-slug="'.$thegem_terms_set[$thegem_slug]->slug.'">'.$thegem_terms_set[$thegem_slug]->name.'</a>';
															$thegem_index++;
														}
													?>
												</div>
											<?php endif; ?>
										<?php endif; ?>

										<?php if ($settings['thegem_elementor_preset'] == 'default' && $settings['caption_position'] == 'page'): ?>
											<?php if (!$alternative_highlight_style_enabled && ($settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular')): ?>
												<div class="gradient-top-box">
											<?php endif; ?>

											<?php if ($has_comments || $has_likes): ?>
												<div class="grid-post-meta <?php if ( !$has_likes): ?>without-likes<?php endif; ?>">
													<?php if ($has_comments) {
														echo '<span class="comments-link">';
														if ($settings['comments_icon']['value']) {
															Icons_Manager::render_icon($settings['comments_icon'], ['aria-hidden' => 'true']);
														} else { ?>
															<i class="default"></i>
														<?php }
														comments_popup_link(0, 1, '%');
														echo '</span>'; ?>
													<?php } ?>
													<?php if( $has_likes ) {
														echo '<span class="post-meta-likes">';
														if ($settings['likes_icon']['value']) {
															Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);
														} else { ?>
															<i class="default"></i>
														<?php }
														zilla_likes();
														echo '</span>';
													} ?>
												</div>
											<?php endif; ?>

											<div class="description <?php if ( empty($post_excerpt) && $settings['blog_show_description'] != 'yes' ): ?>empty-excerpt<?php endif; ?>">
												<?php if (!empty($post_excerpt) && $settings['blog_show_description'] == 'yes'): ?>
													<div class="subtitle">
														<?php echo $post_excerpt; ?>
													</div>
												<?php endif; ?>
											</div>

											<div class="post-author-outer">
												<?php thegem_news_grid_extended_item_author($settings) ; ?>
											</div>

											<?php if (!$alternative_highlight_style_enabled && ($settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular')): ?>
												</div>
											<?php endif; ?>
										<?php endif; ?>
									<?php endif; ?>

									<?php if ($settings['thegem_elementor_preset'] == 'default' && $settings['caption_position'] == 'hover'): ?>
										<div class="slide-content">
											<div class="slide-content-visible">
												<?php if ($settings['image_hover_effect'] == 'vertical-sliding'): ?>
													<?php thegem_news_grid_extended_item_meta($settings, $has_comments, $has_likes, $post_id); ?>
												<?php endif; ?>

												<?php if (($settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'vertical-sliding') && $settings['blog_show_date'] == 'yes'): ?>
													<div class="post-date"><?php echo get_the_date(); ?></div>
												<?php endif; ?>

												<?php if ($settings['blog_show_title'] == 'yes') { ?>
													<div class="title">
														<?php the_title('<div class="title-' . (isset($post_item_data['highlight']) && $post_item_data['highlight'] && $settings['layout'] != 'metro' && $thegem_highlight_type == 'squared' ? 'h4' : 'h5') .'">', '</div>'); ?>
													</div>
												<?php } ?>

												<?php if ($settings['image_hover_effect'] == 'zooming-blur'): ?>
													<?php thegem_news_grid_extended_item_author($settings) ; ?>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] == 'zooming-blur'): ?>
													<?php if (!empty($post_excerpt) && $settings['blog_show_description'] == 'yes'): ?>
														<div class="description">
															<div class="subtitle">
																<?php echo $post_excerpt; ?>
															</div>
														</div>
													<?php endif; ?>
												<?php endif; ?>

												<?php if ($settings['blog_show_categories'] == 'yes' && ($settings['image_hover_effect'] == 'circular' || $settings['image_hover_effect'] == 'zooming-blur' || $settings['image_hover_effect'] == 'vertical-sliding')): ?>
													<div class="info">
														<?php
														$thegem_index = 0;
														foreach ($slugs as $thegem_k => $thegem_slug)
															if (isset($thegem_terms_set[$thegem_slug])) {
																echo ($thegem_index > 0 ? '<span class="sep"></span> ': '').'<a data-slug="'.$thegem_terms_set[$thegem_slug]->slug.'">'.$thegem_terms_set[$thegem_slug]->name.'</a>';
																$thegem_index++;
															}
														?>
													</div>
												<?php endif; ?>

												<?php if (($settings['image_hover_effect'] == 'default' || $settings['image_hover_effect'] == 'circular' || $settings['image_hover_effect'] == 'horizontal-sliding') && $settings['blog_show_date'] == 'yes'): ?>
													<div class="post-date"><?php echo get_the_date(); ?></div>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] == 'default' || $settings['image_hover_effect'] == 'horizontal-sliding'): ?>
													<?php thegem_news_grid_extended_item_meta($settings, $has_comments, $has_likes, $post_id); ?>
												<?php endif; ?>
											</div>

											<div class="slide-content-hidden">
												<?php if ($settings['image_hover_effect'] == 'default' || $settings['image_hover_effect'] == 'horizontal-sliding'): ?>
													<?php thegem_news_grid_extended_item_author($settings) ; ?>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular' || $settings['image_hover_effect'] == 'zooming-blur'): ?>
													<?php thegem_news_grid_extended_item_meta($settings, $has_comments, $has_likes, $post_id); ?>
												<?php endif; ?>

												<?php if (($settings['image_hover_effect'] == 'zooming-blur') && $settings['blog_show_date'] == 'yes'): ?>
													<div class="post-date"><?php echo get_the_date(); ?></div>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] != 'zooming-blur'): ?>
													<?php if (!empty($post_excerpt) && $settings['blog_show_description'] == 'yes'): ?>
														<div class="description">
															<div class="subtitle">
																<?php echo $post_excerpt; ?>
															</div>
														</div>
													<?php endif; ?>
												<?php endif; ?>

												<?php if ($settings['blog_show_categories'] == 'yes' && ($settings['image_hover_effect'] == 'default' || $settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'horizontal-sliding')): ?>
													<div class="info">
														<?php
														$thegem_index = 0;
														foreach ($slugs as $thegem_k => $thegem_slug)
															if (isset($thegem_terms_set[$thegem_slug])) {
																echo ($thegem_index > 0 ? '<span class="sep"></span> ': '').'<a data-slug="'.$thegem_terms_set[$thegem_slug]->slug.'">'.$thegem_terms_set[$thegem_slug]->name.'</a>';
																$thegem_index++;
															}
														?>
													</div>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular' || $settings['image_hover_effect'] == 'vertical-sliding'): ?>
													<?php thegem_news_grid_extended_item_author($settings) ; ?>
												<?php endif; ?>
											</div>
										</div>
									<?php endif; ?>

									<?php if ($settings['thegem_elementor_preset'] == 'new' && $settings['caption_position'] == 'hover'): ?>
										<div class="slide-content">
											<div class="slide-content-visible">
												<?php if (($settings['image_hover_effect'] == 'default' || $settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular') && $settings['blog_show_date'] == 'yes'): ?>
													<div class="post-date"><?php echo get_the_date(); ?></div>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] == 'zooming-blur'): ?>
													<?php thegem_news_grid_extended_item_author($settings) ; ?>
													<?php thegem_news_grid_extended_item_meta($settings, $has_comments, $has_likes, $post_id); ?>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] == 'vertical-sliding' || $settings['image_hover_effect'] == 'horizontal-sliding'): ?>
													<?php thegem_news_grid_extended_item_author($settings) ; ?>
												<?php endif; ?>

												<?php if ($settings['blog_show_title'] == 'yes') { ?>
													<div class="title">
														<?php the_title('<div class="title-' . (isset($post_item_data['highlight']) && $post_item_data['highlight'] && $settings['layout'] != 'metro' && $thegem_highlight_type == 'squared' ? 'h4' : 'h5') .'">', '</div>'); ?>
													</div>
												<?php } ?>

												<?php if ($settings['image_hover_effect'] == 'default'): ?>
													<?php thegem_news_grid_extended_item_author($settings) ; ?>
												<?php endif; ?>

												<?php if ($settings['blog_show_categories'] == 'yes' && ($settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular')): ?>
													<div class="info">
														<?php
														$thegem_index = 0;
														foreach ($slugs as $thegem_k => $thegem_slug)
															if (isset($thegem_terms_set[$thegem_slug])) {
																echo ($thegem_index > 0 ? '<span class="sep"></span> ': '').'<a data-slug="'.$thegem_terms_set[$thegem_slug]->slug.'">'.$thegem_terms_set[$thegem_slug]->name.'</a>';
																$thegem_index++;
															}
														?>
													</div>
												<?php endif; ?>
											</div>

											<div class="slide-content-hidden">
												<?php if ($settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular'): ?>
													<?php thegem_news_grid_extended_item_author($settings) ; ?>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] == 'vertical-sliding'): ?>
													<?php thegem_news_grid_extended_item_author($settings) ; ?>

													<?php if ($settings['blog_show_author'] == 'yes'): ?>
														<div class="overlay-line"></div>
													<?php endif; ?>

													<?php thegem_news_grid_extended_item_meta($settings, $has_comments, $has_likes, $post_id); ?>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] == 'horizontal-sliding'): ?>
													<?php if ($settings['blog_show_author'] == 'yes'): ?>
														<div class="overlay-line"></div>
													<?php endif; ?>
												<?php endif; ?>

												<?php if ($settings['blog_show_categories'] == 'yes' && $settings['image_hover_effect'] == 'horizontal-sliding'): ?>
													<div class="info">
														<?php
														$thegem_index = 0;
														foreach ($slugs as $thegem_k => $thegem_slug)
															if (isset($thegem_terms_set[$thegem_slug])) {
																echo ($thegem_index > 0 ? '<span class="sep"></span> ': '').'<a data-slug="'.$thegem_terms_set[$thegem_slug]->slug.'">'.$thegem_terms_set[$thegem_slug]->name.'</a>';
																$thegem_index++;
															}
														?>
													</div>
												<?php endif; ?>

												<?php if (!empty($post_excerpt) && $settings['blog_show_description'] == 'yes'): ?>
													<div class="description">
														<div class="subtitle">
															<?php echo $post_excerpt; ?>
														</div>
													</div>
												<?php endif; ?>

												<?php if (($settings['image_hover_effect'] == 'zooming-blur' || $settings['image_hover_effect'] == 'vertical-sliding') && $settings['blog_show_date'] == 'yes'): ?>
													<div class="post-date"><?php echo get_the_date(); ?></div>
												<?php endif; ?>

												<?php if ($settings['image_hover_effect'] == 'gradient' || $settings['image_hover_effect'] == 'circular' || $settings['image_hover_effect'] == 'horizontal-sliding'): ?>
													<?php thegem_news_grid_extended_item_meta($settings, $has_comments, $has_likes, $post_id); ?>
												<?php endif; ?>
											</div>
										</div>

										<?php if ($settings['blog_show_categories'] == 'yes' && ($settings['image_hover_effect'] != 'horizontal-sliding' && $settings['image_hover_effect'] != 'gradient'  && $settings['image_hover_effect'] != 'circular')): ?>
											<div class="info">
												<?php
												$thegem_index = 0;
												foreach ($slugs as $thegem_k => $thegem_slug)
													if (isset($thegem_terms_set[$thegem_slug])) {
														echo ($thegem_index > 0 ? '<span class="sep"></span> ': '').'<a data-slug="'.$thegem_terms_set[$thegem_slug]->slug.'">'.$thegem_terms_set[$thegem_slug]->name.'</a>';
														$thegem_index++;
													}
												?>
											</div>
										<?php endif; ?>

										<?php if ($settings['image_hover_effect'] == 'default'): ?>
											<?php thegem_news_grid_extended_item_meta($settings, $has_comments, $has_likes, $post_id); ?>
										<?php endif; ?>

										<?php if ($settings['image_hover_effect'] == 'horizontal-sliding' && $settings['blog_show_date'] == 'yes'): ?>
											<div class="post-date"><?php echo get_the_date(); ?></div>
										<?php endif; ?>

									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $settings['caption_position'] == 'page' && $post_format != 'quote'): ?>
				<div <?php post_class($thegem_caption_classes); ?>>

					<?php if ($settings['thegem_elementor_preset'] == 'new' && ($settings['blog_show_date'] == 'yes' || $settings['blog_show_author'] == 'yes')): ?>
					<div class="post-author-date">
						<?php thegem_news_grid_extended_item_author($settings) ; ?>
						<?php endif; ?>

						<?php if ($settings['blog_show_date'] == 'yes'): ?>
							<?php if ($settings['thegem_elementor_preset'] == 'new' && $settings['blog_show_author'] == 'yes'): ?>
								<div class="post-author-date-separator">&nbsp;-&nbsp;</div>
							<?php endif; ?>
							<div class="post-date"><?php echo get_the_date(); ?></div>
						<?php endif; ?>

						<?php if ($settings['thegem_elementor_preset'] == 'new' && ($settings['blog_show_date'] == 'yes' || $settings['blog_show_author'] == 'yes')): ?>
					</div>
				<?php endif; ?>

					<?php if ($settings['blog_show_title'] == 'yes') { ?>
						<div class="title">
							<?php the_title('<div class="title-h' . ($settings['thegem_elementor_preset'] == 'new' ? 4 : 6) . '"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></div>'); ?>
						</div>
					<?php } ?>

					<?php if ($settings['thegem_elementor_preset'] == 'default' && $settings['blog_show_categories'] == 'yes' && $post_format != 'quote'): ?>
						<div class="info">
							<?php
							$thegem_index = 0;
							foreach ($slugs as $thegem_k => $thegem_slug)
								if (isset($thegem_terms_set[$thegem_slug])) {
									echo ($thegem_index > 0 ? '<span class="sep"></span> ': '').'<a data-slug="'.$thegem_terms_set[$thegem_slug]->slug.'">'.$thegem_terms_set[$thegem_slug]->name.'</a>';
									$thegem_index++;
								}
							?>
						</div>
					<?php endif; ?>

					<?php if ($settings['thegem_elementor_preset'] == 'new' && (!empty($post_excerpt) || $has_comments || $has_likes || $settings['social_sharing'] == 'yes')): ?>
						<?php if (!empty($post_excerpt) && $settings['blog_show_description'] == 'yes'): ?>
							<div class="description">
								<?php echo $post_excerpt; ?>
							</div>
						<?php endif; ?>

						<?php if ($has_comments || $has_likes || $settings['social_sharing'] == 'yes'): ?>
							<div class="grid-post-meta clearfix <?php if ( !$has_likes): ?>without-likes<?php endif; ?>">
								<div class="grid-post-meta-inner">
									<?php if ($settings['social_sharing'] == 'yes'): ?>
										<div class="grid-post-share">
											<a href="javascript: void(0);" class="icon share">
												<?php if ($settings['sharing_icon']['value']) {
													Icons_Manager::render_icon($settings['sharing_icon'], ['aria-hidden' => 'true']);
												} else { ?>
													<i class="default"></i>
												<?php } ?>
											</a>
										</div>
										<div class="portfolio-sharing-pane"><?php include 'socials-sharing.php'; ?></div>
									<?php endif; ?>

									<div class="grid-post-meta-comments-likes">
										<?php if ($has_comments) {
											echo '<span class="comments-link">';
											if ($settings['comments_icon']['value']) {
												Icons_Manager::render_icon($settings['comments_icon'], ['aria-hidden' => 'true']);
											} else { ?>
												<i class="default"></i>
											<?php }
											comments_popup_link(0, 1, '%');
											echo '</span>'; ?>
										<?php } ?>

										<?php if( $has_likes ) {
											echo '<span class="post-meta-likes">';
											if ($settings['likes_icon']['value']) {
												Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);
											} else { ?>
												<i class="default"></i>
											<?php }
											zilla_likes();
											echo '</span>';
										} ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php else: ?>
	<div <?php post_class($thegem_classes); ?>>
	</div>
<?php endif; ?>
