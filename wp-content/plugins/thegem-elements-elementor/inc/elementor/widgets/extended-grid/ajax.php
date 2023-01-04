<?php use Elementor\Icons_Manager;

function blog_grid_extended_more_callback() {
	$settings = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : array();
	ob_start();
	$response = array('status' => 'success');
	$page = isset($settings['more_page']) ? intval($settings['more_page']) : 1;
	if ($page == 0)
		$page = 1;
	$news_grid_loop = thegem_get_news_posts($settings['categories'], $page, $settings['items_per_page'], $settings['orderby'], $settings['order']);
	if ($news_grid_loop->max_num_pages > $page)
		$next_page = $page + 1;
	else
		$next_page = 0;
	?>
	<div data-page="<?php echo $page; ?>" data-next-page="<?php echo $next_page; ?>">
		<?php
		$eo_marker = false;
		while ($news_grid_loop->have_posts()) : $news_grid_loop->the_post(); ?>
			<?php echo thegem_news_render_item(get_the_ID(), $settings); ?>
			<?php $eo_marker = !$eo_marker;
		endwhile; ?>
	</div>
	<?php $response['html'] = trim(preg_replace('/\s\s+/', '', ob_get_clean()));
	$response = json_encode($response);
	header("Content-Type: application/json");
	echo $response;
	exit;
}

add_action('wp_ajax_blog_grid_extended_load_more', 'blog_grid_extended_more_callback');
add_action('wp_ajax_nopriv_blog_grid_extended_load_more', 'blog_grid_extended_more_callback');


function thegem_get_news_posts($news_cat, $page = 1, $ppp = -1, $orderby = 'menu_order date', $order = 'DESC') {
	if (empty($news_cat)) {
		return null;
	}

	$grid_post_types = post_type_exists('thegem_news') ? array('post', 'thegem_news') : array('post');

	$args = array(
		'post_type' => $grid_post_types,
		'post_status' => 'publish',
		'orderby' => $orderby,
		'order' => $order,
		'paged' => $page,
		'posts_per_page' => $ppp,
	);

	if (!in_array('0', $news_cat, true)) {
		$args['tax_query'] = array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'category',
				'field' => 'slug',
				'terms' => $news_cat
			),
		);
		if (taxonomy_exists('thegem_news_sets')) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'thegem_news_sets',
				'field' => 'slug',
				'terms' => $news_cat
			);
		}
	}

	$news_loop = new WP_Query($args);

	return $news_loop;
}

function thegem_news_render_item($post_id = false, $settings) {
	$terms = $settings['categories'];
	if (in_array('0', $terms)) {
		$terms = get_terms('category', array('hide_empty' => false));
	} else {
		foreach ($terms as $key => $term) {
			$terms[$key] = get_term_by('slug', $term, 'category');
			if (!$terms[$key]) {
				unset($terms[$key]);
			}
		}
	}
	$terms = apply_filters('news_grid_terms_filter', $terms);

	$thegem_terms_set = array();
	foreach ($terms as $term) {
		$thegem_terms_set[$term->slug] = $term;
	}

	$taxonomies = array('category');
	if (taxonomy_exists('thegem_news_sets')) {
		$taxonomies[] = 'thegem_news_sets';
	}

	if ($post_id) {
		$slugs = wp_get_object_terms($post_id, $taxonomies, array('fields' => 'slugs'));
	} else {
		$slugs = array();
		$portfolio_item_size = true;
	}

	$thegem_classes = array('portfolio-item');
	$thegem_classes = array_merge($thegem_classes, $slugs);

	$thegem_image_classes = array('image');
	$thegem_caption_classes = array('caption');

	if (!isset($portfolio_item_size)) {
		$thegem_post_data = thegem_get_sanitize_page_title_data(get_the_ID());
		$post_item_data = thegem_get_sanitize_post_data(get_the_ID());
		$post_format = get_post_format(get_the_ID());
	} else {
		$slugs = array();
		$thegem_post_data = array();
		$post_item_data = array();
	}

	if ($settings['ignore_highlights'] == 'yes') {
		unset($post_item_data['highlight']);
		unset($post_item_data['highlight_type']);
		unset($post_item_data['highlight_style']);
	}

	if (!empty($post_item_data['highlight_type'])) {
		$thegem_highlight_type = $post_item_data['highlight_type'];
	} else {
		$thegem_highlight_type = 'squared';
	}

	$alternative_highlight_style_enabled = isset($post_item_data['highlight']) && $post_item_data['highlight'] && $post_item_data['highlight_style'] == 'alternative' && $settings['caption_position'] == 'hover';

	if ($settings['layout'] != 'metro') {
		if ($settings['columns'] == '1x') {
			$thegem_classes = array_merge($thegem_classes, array('col-xs-12'));
			$thegem_image_classes = array_merge($thegem_image_classes, array('col-sm-5', 'col-xs-12'));
			$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-sm-7', 'col-xs-12'));
		}

		if ($settings['columns'] == '2x') {
			if (isset($post_item_data['highlight']) && $post_item_data['highlight'] && $thegem_highlight_type != 'vertical')
				$thegem_classes = array_merge($thegem_classes, array('col-xs-12'));
			else
				$thegem_classes = array_merge($thegem_classes, array('col-sm-6', 'col-xs-12'));
		}

		if ($settings['columns'] == '3x') {
			if (isset($post_item_data['highlight']) && $post_item_data['highlight'] && $thegem_highlight_type != 'vertical')
				$thegem_classes = array_merge($thegem_classes, array('col-md-8', 'col-xs-8'));
			else
				$thegem_classes = array_merge($thegem_classes, array('col-md-4', 'col-xs-4'));
		}

		if ($settings['columns'] == '4x') {
			if (isset($post_item_data['highlight']) && $post_item_data['highlight'] && $thegem_highlight_type != 'vertical')
				$thegem_classes = array_merge($thegem_classes, array('col-md-6', 'col-sm-8', 'col-xs-8'));
			else
				$thegem_classes = array_merge($thegem_classes, array('col-md-3', 'col-sm-4', 'col-xs-4'));
		}
	}

	if (isset($post_item_data['highlight']) && $post_item_data['highlight'])
		$thegem_classes[] = 'double-item';

	if (isset($post_item_data['highlight']) && $post_item_data['highlight']) {
		$thegem_classes[] = 'double-item-' . $thegem_highlight_type;
	}

	if ($alternative_highlight_style_enabled) {
		$thegem_classes[] = 'double-item-style-' . $post_item_data['highlight_style'];
		$thegem_classes[] = 'double-item-style-' . $post_item_data['highlight_style'] . '-' . $thegem_highlight_type;

		if ($thegem_highlight_type == 'squared') {
			$thegem_highlight_type = 'vertical';
		} else {
			$post_item_data['highlight'] = false;
		}
	}

	$thegem_size = 'thegem-portfolio-justified';
	$thegem_sizes = thegem_image_sizes();
	if ($settings['columns'] != '1x') {
		if ($settings['layout'] == 'masonry') {
			$thegem_size = 'thegem-portfolio-masonry';
			if (isset($post_item_data['highlight']) && $post_item_data['highlight'])
				$thegem_size = 'thegem-portfolio-masonry-double';
		} elseif ($settings['layout'] == 'metro') {
			$thegem_size = 'thegem-portfolio-metro';
		} else {
			if (isset($post_item_data['highlight']) && $post_item_data['highlight']) {
				$thegem_size = 'thegem-portfolio-double-' . str_replace('%', '', $settings['columns']);

				if ($settings['caption_position'] == 'hover' && isset($thegem_sizes[$thegem_size . '-hover'])) {
					$thegem_size .= '-hover';
				}

				if ($settings['columns'] == '100%' && $settings['caption_position'] == 'page') {
					$thegem_size .= '-page';
				}

			}
		}

		if (isset($post_item_data['highlight']) && $post_item_data['highlight'] && $settings['layout'] != 'metro' && $thegem_highlight_type != 'squared') {
			$thegem_size .= '-' . $thegem_highlight_type;
		}
	} else {
		$thegem_size = 'thegem-portfolio-1x';
	}

	$thegem_classes[] = 'item-animations-not-inited';

	$thegem_size = apply_filters('portfolio_size_filter', $thegem_size);

// $thegem_large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
	$thegem_self_video = '';

	$thegem_sources = array();

	$thegem_has_post_thumbnail = has_post_thumbnail(get_the_ID());

	if ($settings['layout'] == 'metro') {
		$thegem_sources = array(
			array('media' => '(min-width: 550px) and (max-width: 1100px)', 'srcset' => array('1x' => 'thegem-portfolio-metro-medium', '2x' => 'thegem-portfolio-metro-retina'))
		);
	}

	if (!isset($post_item_data['highlight']) || !$post_item_data['highlight'] ||
		($settings['layout'] == 'masonry' && isset($post_item_data['highlight']) && $post_item_data['highlight']) && $thegem_highlight_type == 'vertical') {

		$retina_size = $settings['layout'] == 'justified' ? $thegem_size : 'thegem-portfolio-masonry-double';

		if ($settings['columns'] == '100%') {
			if ($settings['layout'] == 'justified' || $settings['layout'] == 'masonry') {
				switch ($settings['columns_100']) {
					case '4':
						$thegem_sources = array(
							array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
							array('media' => '(min-width: 1280px) and (max-width: 1495px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-fullwidth-5x', '2x' => $retina_size)),
							array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-fullwidth-4x', '2x' => $retina_size))
						);
						break;

					case '5':
						$thegem_sources = array(
							array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
							array('media' => '(min-width: 1495px) and (max-width: 1680px), (min-width: 550px) and (max-width: 1280px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-fullwidth-4x', '2x' => $retina_size)),
							array('media' => '(min-width: 1680px) and (max-width: 1920px), (min-width: 1280px) and (max-width: 1495px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-fullwidth-5x', '2x' => $retina_size))
						);
						break;
				}
			}
		} else {
			if ($settings['layout'] == 'justified' || $settings['layout'] == 'masonry') {
				switch ($settings['columns']) {
					case '2x':
						$thegem_sources = array(
							array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
							array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-2x', '2x' => $retina_size))
						);
						break;

					case '3x':
						$thegem_sources = array(
							array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
							array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-3x', '2x' => $retina_size))
						);
						break;

					case '4x':
						$thegem_sources = array(
							array('media' => '(max-width: 550px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-2x-500', '2x' => $retina_size)),
							array('media' => '(max-width: 1100px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-3x', '2x' => $retina_size)),
							array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-portfolio-' . $settings['layout'] . '-4x', '2x' => $retina_size))
						);
						break;
				}
			}
		}
	}

	if ($settings['blog_show_categories'] != 'yes') {
		$thegem_classes[] = 'post-hide-categories';
	}

	if ($settings['blog_show_date'] != 'yes') {
		$thegem_classes[] = 'post-hide-date';
	}

	$post_excerpt = !has_excerpt() && !empty($thegem_post_data['title_excerpt']) ? $thegem_post_data['title_excerpt'] : preg_replace('%&#x[a-fA-F0-9]+;%', '', apply_filters('the_excerpt', get_the_excerpt()));

	$has_comments = comments_open() && $settings['blog_show_comments'] == 'yes';

	$has_likes = function_exists('zilla_likes') && $settings['blog_show_likes'] == 'yes';

	if ($settings['thegem_elementor_preset'] != 'default' && $settings['caption_position'] == 'page' && $settings['caption_container_preset'] == 'transparent' && ($has_likes || $has_comments || $settings['social_sharing'] == 'yes')) {
		$thegem_classes[] = 'show-caption-border';
	}

	if ($settings['thegem_elementor_preset'] == 'default' && $settings['caption_position'] == 'page' && $settings['caption_container_preset'] == 'transparent') {
		$thegem_classes[] = 'show-caption-border';
	}

	if (empty($post_excerpt)) {
		$thegem_classes[] = 'post-empty-excerpt';
	}


	if ($settings['blog_show_categories'] == 'yes') {
		foreach ($slugs as $thegem_k => $thegem_slug) {
			if (isset($thegem_terms_set[$thegem_slug])) {
				$thegem_classes[] = 'post-has-sets';
				break;
			}
		}
	}

	if ($settings['blog_show_author'] != 'yes') {
		$thegem_classes[] = 'post-has-author';
	}

	$preset_path = dirname(__FILE__) . '/templates/content-news-item.php';
	$preset_path_filtered = apply_filters( 'thegem_extended_grid_item_preset', $preset_path);
	$preset_path_theme = get_stylesheet_directory() . '/templates/extended-grid/content-news-item.php';

	if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
		include($preset_path_theme);
	} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
		include($preset_path_filtered);
	}

}

function thegem_news_grid_extended_item_author($settings) {
	if ($settings['blog_show_author'] != 'yes') return;
	?>

	<div class="author">
		<?php if ($settings['blog_show_author_avatar'] == 'yes'): ?>
			<span class="author-avatar"><?php echo get_avatar(get_the_author_meta('ID'), 50) ?></span>
		<?php endif; ?>
		<span class="author-name"><?php printf(esc_html__($settings['by_text'] . " %s", "thegem"), get_the_author_link()) ?></span>
	</div>
	<?php
}

function thegem_news_grid_extended_item_meta($settings, $has_comments, $has_likes, $post_id) {
	global $post;
	if (!$has_comments && !$has_likes && $settings['social_sharing'] != 'yes') return;
	?>

	<div class="grid-post-meta clearfix <?php if (!$has_likes): ?>without-likes<?php endif; ?>">
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

				<?php if ($has_likes) {
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

			<?php if ($settings['social_sharing'] == 'yes'): ?>
				<div class="portfolio-sharing-pane"><?php include 'templates/socials-sharing.php'; ?></div>
			<?php endif; ?>
		</div>
	</div>

	<?php
}
