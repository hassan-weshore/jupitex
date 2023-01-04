<?php
function portfolio_grid_more_callback() {
	$settings = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : array();
	ob_start();
	$response = array('status' => 'success');
	$page = isset($settings['more_page']) ? intval($settings['more_page']) : 1;
	if ($page == 0)
		$page = 1;
	$news_grid_loop = thegem_get_portfolio_posts($settings['content_portfolios_cat'], $page, $settings['items_per_page'], $settings['orderby'], $settings['order']);
	if ($news_grid_loop->max_num_pages > $page)
		$next_page = $page + 1;
	else
		$next_page = 0;
	?>

	<div data-page="<?php echo $page; ?>" data-next-page="<?php echo $next_page; ?>">
		<?php
		$eo_marker = false;
		while ($news_grid_loop->have_posts()) : $news_grid_loop->the_post(); ?>
			<?php echo thegem_portfolio_render_item(get_the_ID(), $settings); ?>
			<?php $eo_marker = !$eo_marker;
		endwhile; ?>
	</div>
	<?php $response['html'] = trim(preg_replace('/\s\s+/', '', ob_get_clean()));
	$response = json_encode($response);
	header("Content-Type: application/json");
	echo $response;
	exit;
}

add_action('wp_ajax_portfolio_grid_load_more', 'portfolio_grid_more_callback');
add_action('wp_ajax_nopriv_portfolio_grid_load_more', 'portfolio_grid_more_callback');

function thegem_get_portfolio_posts($portfolios_cat, $page = 1, $ppp = -1, $orderby = 'menu_order ID', $order = 'ASC') {
	if (empty($portfolios_cat)) {
		return null;
	}

	$args = array(
		'post_type' => 'thegem_pf_item',
		'post_status' => 'publish',
		'orderby' => $orderby,
		'order' => $order,
		'paged' => $page,
		'posts_per_page' => $ppp,
	);

	if (!in_array('0', $portfolios_cat, true)) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'thegem_portfolios',
				'field' => 'slug',
				'terms' => $portfolios_cat
			)
		);
	}

	$portfolio_loop = new WP_Query($args);

	return $portfolio_loop;
}

function thegem_portfolio_render_item($post_id = false, $settings) {
	if ($post_id) {
		$slugs = wp_get_object_terms($post_id, 'thegem_portfolios', array('fields' => 'slugs'));
	} else {
		$slugs = array();
		$portfolio_item_size = true;
	}
	$terms = $settings['content_portfolios_cat'];

	$thegem_classes = array('portfolio-item');
	$thegem_classes = array_merge($thegem_classes, $slugs);

	$thegem_image_classes = array('image');
	$thegem_caption_classes = array('caption');

	if (!isset($portfolio_item_size)) {
		$thegem_portfolio_item_data = get_post_meta(get_the_ID(), 'thegem_portfolio_item_data', 1);
	} else {
		$thegem_portfolio_item_data = array();
	}

	if (!empty($thegem_portfolio_item_data['highlight_type'])) {
		$thegem_highlight_type = $thegem_portfolio_item_data['highlight_type'];
	} else {
		$thegem_highlight_type = 'squared';
	}

	if (empty($thegem_portfolio_item_data['types']))
		$thegem_portfolio_item_data['types'] = array();

	if ($settings['layout'] != 'metro') {
		if ($settings['columns'] == '1x') {
			$thegem_classes = array_merge($thegem_classes, array('col-xs-12'));
			$thegem_image_classes = array_merge($thegem_image_classes, array('col-sm-5', 'col-xs-12'));
			$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-sm-7', 'col-xs-12'));
		}

		if ($settings['columns'] == '2x') {
			if (isset($thegem_portfolio_item_data['highlight']) && $thegem_portfolio_item_data['highlight'] && $thegem_highlight_type != 'vertical')
				$thegem_classes = array_merge($thegem_classes, array('col-xs-12'));
			else
				$thegem_classes = array_merge($thegem_classes, array('col-sm-6', 'col-xs-12'));
		}

		if ($settings['columns'] == '3x') {
			if (isset($thegem_portfolio_item_data['highlight']) && $thegem_portfolio_item_data['highlight'] && $thegem_highlight_type != 'vertical')
				$thegem_classes = array_merge($thegem_classes, array('col-md-8', 'col-xs-8'));
			else
				$thegem_classes = array_merge($thegem_classes, array('col-md-4', 'col-xs-4'));
		}

		if ($settings['columns'] == '4x') {
			if (isset($thegem_portfolio_item_data['highlight']) && $thegem_portfolio_item_data['highlight'] && $thegem_highlight_type != 'vertical')
				$thegem_classes = array_merge($thegem_classes, array('col-md-6', 'col-sm-8', 'col-xs-8'));
			else
				$thegem_classes = array_merge($thegem_classes, array('col-md-3', 'col-sm-4', 'col-xs-4'));
		}
	}

	if (isset($thegem_portfolio_item_data['highlight']) && $thegem_portfolio_item_data['highlight'])
		$thegem_classes[] = 'double-item';

	if (isset($thegem_portfolio_item_data['highlight']) && $thegem_portfolio_item_data['highlight']) {
		$thegem_classes[] = 'double-item-' . $thegem_highlight_type;
	}

	$thegem_size = 'thegem-portfolio-justified';
	$thegem_sizes = thegem_image_sizes();
	if ($settings['columns'] != '1x') {
		if ($settings['layout'] == 'masonry') {
			$thegem_size = 'thegem-portfolio-masonry';
			if (isset($thegem_portfolio_item_data['highlight']) && $thegem_portfolio_item_data['highlight'])
				$thegem_size = 'thegem-portfolio-masonry-double';
		} elseif ($settings['layout'] == 'metro') {
			$thegem_size = 'thegem-portfolio-metro';
		} else {
			if (isset($thegem_portfolio_item_data['highlight']) && $thegem_portfolio_item_data['highlight']) {
				$thegem_size = 'thegem-portfolio-double-' . str_replace('%', '', $settings['columns']);

				if (($settings['caption_position'] == 'hover' || $settings['caption_position'] == 'image') && isset($thegem_sizes[$thegem_size . '-hover'])) {
					$thegem_size .= '-hover';
				}

				if(isset($thegem_sizes[$thegem_size.'-gap-'.$settings['image_gaps']['size']])) {
					$thegem_size .= '-gap-'.$settings['image_gaps']['size'];
				}

				if ($settings['columns'] == '100%' && $settings['caption_position'] == 'page') {
					$thegem_size .= '-page';
				}

			}
		}

		if (isset($thegem_portfolio_item_data['highlight']) && $thegem_portfolio_item_data['highlight'] && $settings['layout'] != 'metro' && $thegem_highlight_type != 'squared') {
			$thegem_size .= '-' . $thegem_highlight_type;
		}
	} else {
		$thegem_size = 'thegem-portfolio-1x';
	}

	$thegem_classes[] = 'item-animations-not-inited';

	$thegem_size = apply_filters('portfolio_size_filter', $thegem_size);

	if (!isset($portfolio_item_size)) {
		$thegem_large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
		$thegem_self_video = '';
	}

	$thegem_sources = array();

	if ($settings['layout'] == 'metro') {
		$thegem_sources = array(
			array('media' => '(min-width: 550px) and (max-width: 1100px)', 'srcset' => array('1x' => 'thegem-portfolio-metro-medium', '2x' => 'thegem-portfolio-metro-retina'))
		);
	}

	if (!isset($thegem_portfolio_item_data['highlight']) || !$thegem_portfolio_item_data['highlight'] ||
		($settings['layout'] == 'masonry' && isset($thegem_portfolio_item_data['highlight']) && $thegem_portfolio_item_data['highlight']) && $thegem_highlight_type == 'vertical') {

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

	if ($settings['caption_position'] == 'image') {
		$hover_effect = $settings['image_hover_effect_image'];
	} else if ($settings['caption_position'] == 'page') {
		$hover_effect = $settings['image_hover_effect_page'];
	} else {
		$hover_effect = $settings['image_hover_effect_hover'];
	}

	if ($settings['category_in_text']) {
		$in_text = $settings['category_in_text'];
	} else if ($settings['category_in_text_page']) {
		$in_text = $settings['category_in_text_page'];
	} else {
		$in_text = 'in';
	}

	$preset_path = __DIR__ . '/templates/content-portfolio-item.php';
	$preset_path_filtered = apply_filters( 'thegem_portfolio_grid_item_preset', $preset_path);
	$preset_path_theme = get_stylesheet_directory() . '/templates/portfolio-grid/content-portfolio-item.php';

	if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
		include($preset_path_theme);
	} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
		include($preset_path_filtered);
	}
}
