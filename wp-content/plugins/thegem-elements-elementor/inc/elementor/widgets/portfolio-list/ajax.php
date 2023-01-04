<?php
function portfolio_list_more_callback() {
	$settings = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : array();
	ob_start();
	$response = array('status' => 'success');
	$page = isset($settings['more_page']) ? intval($settings['more_page']) : 1;
	if ($page == 0)
		$page = 1;
	$news_grid_loop = thegem_get_portfolio_list_posts($settings['content_portfolios_cat'], $page, $settings['items_per_page'], $settings['orderby'], $settings['order']);
	if ($news_grid_loop->max_num_pages > $page)
		$next_page = $page + 1;
	else
		$next_page = 0;
	?>

	<div data-page="<?php echo $page; ?>" data-next-page="<?php echo $next_page; ?>">
		<?php
		$eo_marker = false;
		while ($news_grid_loop->have_posts()) : $news_grid_loop->the_post(); ?>
			<?php echo thegem_portfolio_list_render_item(get_the_ID(), $settings); ?>
			<?php $eo_marker = !$eo_marker;
		endwhile; ?>
	</div>
	<?php $response['html'] = trim(preg_replace('/\s\s+/', '', ob_get_clean()));
	$response = json_encode($response);
	header("Content-Type: application/json");
	echo $response;
	exit;
}

add_action('wp_ajax_portfolio_list_load_more', 'portfolio_list_more_callback');
add_action('wp_ajax_nopriv_portfolio_list_load_more', 'portfolio_list_more_callback');

function thegem_get_portfolio_list_posts($portfolios_cat, $page = 1, $ppp = -1, $orderby = 'menu_order ID', $order = 'ASC') {
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

function thegem_portfolio_list_render_item($post_id = false, $settings) {
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

	$thegem_portfolio_item_data = thegem_get_sanitize_pf_item_data(get_the_ID());
	$thegem_title_data = thegem_get_sanitize_page_title_data(get_the_ID());

	if (empty($thegem_portfolio_item_data['types']))
		$thegem_portfolio_item_data['types'] = array();

	$thegem_classes = array_merge($thegem_classes, array('col-xs-12'));

	if ($settings['caption_position'] != 'image') {
		if ($settings['portfolio_layout_version'] == 'fullwidth') {
			$thegem_image_classes = array_merge($thegem_image_classes, array('col-md-8', 'col-xs-12'));
			$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-md-4', 'col-xs-12'));
			if ($settings['caption_position'] == 'left') {
				$thegem_image_classes = array_merge($thegem_image_classes, array('col-md-push-4'));
				$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-md-pull-8'));
			}
		} else {
			$thegem_image_classes = array_merge($thegem_image_classes, array('col-md-7', 'col-xs-12'));
			$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-md-5', 'col-xs-12'));
			if ($settings['caption_position'] == 'left') {
				$thegem_image_classes = array_merge($thegem_image_classes, array('col-md-push-5'));
				$thegem_caption_classes = array_merge($thegem_caption_classes, array('col-md-pull-7'));
			}
		}
	}

	$thegem_size = 'thegem-portfolio-1x';
	if ($settings['caption_position'] == 'image') {
		$thegem_size .= '-hover';
	} else {
		$thegem_size .= $settings['portfolio_layout_version'] == 'sidebar' ? '-sidebar' : '';
	}

	$thegem_small_image_url = thegem_generate_thumbnail_src(get_post_thumbnail_id(), $thegem_size);
	$thegem_large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
	$thegem_self_video = '';

	$thegem_bottom_line = false;
	$thegem_portfolio_button_link = '';
	if ($thegem_portfolio_item_data['project_link'] || $settings['social_sharing'] == 'yes') {
		$thegem_bottom_line = true;
	}

	if ($settings['caption_position'] == 'image') {
		$hover_effect = $settings['image_hover_effect_image'];
	} else {
		$hover_effect = $settings['image_hover_effect'];
	}

	if ($settings['category_in_text']) {
		$in_text = $settings['category_in_text'];
	} else if ($settings['category_in_text_page']) {
		$in_text = $settings['category_in_text_page'];
	} else {
		$in_text = '';
	}

	$thegem_classes[] = 'item-animations-not-inited';


	if ($settings['portfolio_show_details'] == 'yes') {

		$button_classes = [
			'gem-button',
			'gem-button-size-' . $settings['details_button_size'],
			'gem-button-style-' . $settings['details_button_type'],
			'gem-button-text-weight-normal',
		];

	}


	$preset_path = __DIR__ . '/templates/content-portfolio-item-1x.php';
	$preset_path_filtered = apply_filters( 'thegem_portfolio_list_item_preset', $preset_path);
	$preset_path_theme = get_stylesheet_directory() . '/templates/portfolio-list/content-portfolio-item-1x.php';

	if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
		include($preset_path_theme);
	} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
		include($preset_path_filtered);
	}
}
