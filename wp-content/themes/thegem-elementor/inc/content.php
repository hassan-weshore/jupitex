<?php

function thegem_home_content_builder() {
	$home_content = thegem_get_option('home_content') ? json_decode(stripslashes(thegem_get_option('home_content')), TRUE) : array();
	$block_number = 1;
	if(count($home_content)) {
		foreach($home_content as $block) {
			$block_function = 'thegem_'.$block['block_type'].'_block';
			if(function_exists($block_function)) {
				echo '<section id="'.esc_attr(!empty($block['block_id']) ? $block['block_id'] : 'home-content-block-'.$block_number).'" class="home-content-block block-'.esc_attr($block['block_type']).'">';
				$block_function($block);
				echo '</section>';
				$block_number++;
			}
		}
	} else {
?>
	<div class="block-content">
		<div class="container">
			<h1 class="page-title"><?php esc_html_e('TheGem Theme', 'thegem') ?></h1>
			<div class="inner">
				<p><?php printf(wp_kses(__('Log in to <a href="%s">wordpress</a> admin and set up your starting page using <a href="%s">Home Constructor</a>.', 'thegem'), array('a' => array('href' => array()))), esc_url(admin_url('/')), esc_url(admin_url('admin.php?page=thegem-theme-options#home_constructor'))); ?></p>
				<p><?php esc_html_e('Please refer to TheGem documentation <b>(Getting Started &mdash; Setting Up Homepage)</b> in order to learn how to use Home Constructor.', 'thegem'); ?></p>
				<p><?php esc_html_e('Additionally you can use demo content included in TheGem to quickly setup a demo of your starting page.', 'thegem'); ?></p>
			</div>
		</div>
	</div>
<?php
	}
}


function thegem_content_block($params = array()) {
	$content_block_query = new WP_Query('page_id=' . $params['page']);
	if($content_block_query->have_posts()) {
		while ($content_block_query->have_posts()) {
			$content_block_query->the_post();
			get_template_part('content', 'page-content-block');
		}
	}
	wp_reset_postdata();
}

function thegem_pw_filter_widgets($sidebars_widgets) {
	if(!thegem_is_plugin_active('wp-page-widget/wp-page-widgets.php')) {
		return $sidebars_widgets;
	}
	global $post, $pagenow;
	$objTaxonomy = getTaxonomyAccess();
	if(
			(is_admin()
			&& !in_array($pagenow, array('post-new.php', 'post.php', 'edit-tags.php'))
			&& (!in_array($pagenow, array('admin.php')) && (isset($_GET['page']) && ($_GET['page'] == 'pw-front-page') || isset($_GET['page']) && $_GET['page'] == 'pw-search-page'))
			)
			|| (!is_admin() && !is_singular() && !is_search() && empty($objTaxonomy['taxonomy']) && !(is_home() && is_object($post) && $post->post_type == 'page'))
	) {

		return $sidebars_widgets;
	}


	// Search page
	if(is_search() || (is_admin() && (isset($_GET['page']) && $_GET['page'] == 'pw-search-page'))) {
		$enable_customize = get_option('_pw_search_page', true);
		$_sidebars_widgets = get_option('_search_page_sidebars_widgets', true);
	}


	// Post page
	elseif(empty($objTaxonomy['taxonomy'])) {
		//if admin alway use query string post = ID
		//Fix conflic when other plugins use query post after load editing post!

		if(is_object($post) && isset($_GET['post'])) {
			$postID = $_GET['post'];
		}
		if(is_admin() && isset($postID)) {
			if(!is_object($post)) $post = new stdClass();
				$post->ID = $postID;
		}
		if(isset($post->ID)) {
		$enable_customize = get_post_meta($post->ID, '_customize_sidebars', true);
		$_sidebars_widgets = get_post_meta($post->ID, '_sidebars_widgets', true); }
	}

	// Taxonomy page
	else {

		$taxonomyMetaData = getTaxonomyMetaData($objTaxonomy['taxonomy'], $objTaxonomy['term_id']);
		$enable_customize = $taxonomyMetaData['_customize_sidebars'];
		$_sidebars_widgets = $taxonomyMetaData['_sidebars_widgets'];
	}

	if(isset($enable_customize) && $enable_customize == 'yes' && !empty($_sidebars_widgets)) {
		if(is_array($_sidebars_widgets) && isset($_sidebars_widgets['array_version']))
			unset($_sidebars_widgets['array_version']);

		$sidebars_widgets = wp_parse_args($_sidebars_widgets, $sidebars_widgets);
	}
	global $wp_registered_widgets;
	foreach($sidebars_widgets as $sid => $sidebar) {
		if(is_array($sidebar)) {
			foreach($sidebar as $wid => $widget) {
				if(!isset($wp_registered_widgets[$widget])) {
					unset($sidebars_widgets[$sid][$wid]);
				}
			}
		}
	}
	return $sidebars_widgets;
}
//add_filter('sidebars_widgets', 'thegem_pw_filter_widgets');
if (!function_exists('thegem_contacts')) {
	function thegem_contacts()
	{
		$output = '';
		if (locate_template('contacts-widget.php') != '') {
			ob_start();
			get_template_part('contacts', 'widget');
			$output = ob_get_clean();
			return $output;
		}
		if (thegem_get_option('contacts_address')) {
			$output .= '<div class="gem-contacts-item gem-contacts-address">' . esc_html__('Address:', 'thegem') . '</br> ' . stripslashes(thegem_get_option('contacts_address')) . '</div>';
		}
		if (thegem_get_option('contacts_phone')) {
			$output .= '<div class="gem-contacts-item gem-contacts-phone">' . esc_html__('Phone:', 'thegem') . ' <a href="tel:' . esc_attr(stripslashes(thegem_get_option('contacts_phone'))) . '">' . esc_html(stripslashes(thegem_get_option('contacts_phone'))) . '</a></div>';
		}
		if (thegem_get_option('contacts_fax')) {
			$output .= '<div class="gem-contacts-item gem-contacts-fax">' . esc_html__('Fax:', 'thegem') . ' ' . esc_html(stripslashes(thegem_get_option('contacts_fax'))) . '</div>';
		}
		if (thegem_get_option('contacts_email')) {
			$output .= '<div class="gem-contacts-item gem-contacts-email">' . esc_html__('Email:', 'thegem') . ' <a href="' . esc_url('mailto:' . sanitize_email(thegem_get_option('contacts_email'))) . '">' . sanitize_email(thegem_get_option('contacts_email')) . '</a></div>';
		}
		if (thegem_get_option('contacts_website')) {
			$output .= '<div class="gem-contacts-item gem-contacts-website">' . esc_html__('Website:', 'thegem') . ' <a href="' . esc_url(thegem_get_option('contacts_website')) . '">' . esc_html(thegem_get_option('contacts_website')) . '</a></div>';
		}
		if ($output) {
			return '<div class="gem-contacts">' . $output . '</div>';
		}
		return;
	}
}
if (!function_exists('thegem_top_area_contacts')) {
	function thegem_top_area_contacts()
	{
		$output = '';
		if (locate_template('contacts-top-area.php') != '') {
			ob_start();
			get_template_part('contacts', 'top-area');
			$output = ob_get_clean();
			return $output;
		}
		if (thegem_get_option('top_area_contacts_address')) {
			wp_enqueue_style('icons-' . thegem_get_option('top_area_contacts_address_icon_pack'));
			$output .= '<div class="gem-contacts-item gem-contacts-address">' . esc_html(stripslashes(thegem_get_option('top_area_contacts_address'))) . '</div>';
		}
		if (thegem_get_option('top_area_contacts_phone')) {
			wp_enqueue_style('icons-' . thegem_get_option('top_area_contacts_phone_icon_pack'));
			$output .= '<div class="gem-contacts-item gem-contacts-phone"><a href="tel:' . esc_attr(stripslashes(thegem_get_option('top_area_contacts_phone'))) . '">' . esc_html(stripslashes(thegem_get_option('top_area_contacts_phone'))) . '</a></div>';
		}
		if (thegem_get_option('top_area_contacts_fax')) {
			wp_enqueue_style('icons-' . thegem_get_option('top_area_contacts_fax_icon_pack'));
			$output .= '<div class="gem-contacts-item gem-contacts-fax">' . esc_html(stripslashes(thegem_get_option('top_area_contacts_fax'))) . '</div>';
		}
		if (thegem_get_option('top_area_contacts_email')) {
			wp_enqueue_style('icons-' . thegem_get_option('top_area_contacts_email_icon_pack'));
			$output .= '<div class="gem-contacts-item gem-contacts-email"><a href="' . esc_url('mailto:' . sanitize_email(thegem_get_option('top_area_contacts_email'))) . '">' . sanitize_email(thegem_get_option('top_area_contacts_email')) . '</a></div>';
		}
		if (thegem_get_option('top_area_contacts_website')) {
			wp_enqueue_style('icons-' . thegem_get_option('top_area_contacts_website_icon_pack'));
			$output .= '<div class="gem-contacts-item gem-contacts-website"><a href="' . esc_url(thegem_get_option('top_area_contacts_website')) . '">' . esc_html(thegem_get_option('top_area_contacts_website')) . '</a></div>';
		}
		if ($output) {
			return '<div class="gem-contacts inline-inside">' . $output . '</div>';
		}
		return;
	}
}

function thegem_related_posts() {
	$post_tags = wp_get_post_tags(get_the_ID());
	$post_tags_ids = array();
	foreach($post_tags as $individual_tag) {
		$post_tags_ids[] = $individual_tag->term_id;
	}
	if($post_tags_ids) {
		$args=array(
			'tag__in' => $post_tags_ids,
			'post__not_in' => array(get_the_ID()),
			'posts_per_page' => 15,
			'orderby' => 'rand'
		);
		$related_query = new WP_Query($args);
		if($related_query->have_posts()) {
			wp_enqueue_script('thegem-related-posts-carousel');
?>
	<div class="post-related-posts">
		<h2><?php esc_html_e('Related Posts', 'thegem'); ?></h2>
		<div class="post-related-posts-block clearfix">
			<div class="preloader"><div class="preloader-spin"></div></div>
			<div class="related-posts-carousel">
				<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
					<div class="related-element">
						<a href="<?php echo esc_url(get_permalink()); ?>"><?php thegem_post_thumbnail('thegem-post-thumb', true, '', array('srcset' => array('1x' => 'thegem-post-thumb-small', '2x' => 'thegem-post-thumb-large'))); ?></a>
						<div class="related-element-info clearfix">
							<div class="related-element-info-conteiner">
								<?php the_title('<a href="'.esc_url(get_permalink()).'">', '</a>'); ?>
								<div class='related-element-info-excerpt'>
									<?php the_excerpt(); ?>
								</div>
							</div>
							<div class="post-meta date-color">
								<div class="entry-meta clearfix">
									<div class="post-meta-right">
										<?php if(comments_open()): ?>
											<span class="comments-link"><?php comments_popup_link(0, 1, '%'); ?></span>
										<?php endif; ?>
										<?php if(comments_open() && function_exists('zilla_likes')): ?><?php endif; ?>
										<?php if( function_exists('zilla_likes') ) { echo '<span class="post-meta-likes">';zilla_likes();echo '</span>'; } ?>
									</div>
									<div class="post-meta-left">
										<span class="post-meta-date gem-post-date gem-date-color small-body"><?php echo get_the_date('d M Y'); ?></span>
									</div>
								</div><!-- .entry-meta -->
							</div>
						</div>
					</div>
				<?php endwhile; wp_reset_postdata() ?>
			</div>

		</div>
	</div>
<?php
		}
	}
}

function thegem_comment_form_before_fields() {
	echo '<div class="row comment-form-fields">';
}
add_action( 'comment_form_before_fields', 'thegem_comment_form_before_fields' );

function thegem_comment_form_after_fields() {
	echo '</div>';
}
add_action( 'comment_form_after_fields', 'thegem_comment_form_after_fields' );

function thegem_comment($comment, $args, $depth) {
		if('div' == $args['style']) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
?>
		<<?php echo $tag; ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
		<?php if('div' != $args['style']) : ?>
		<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
		<?php endif; ?>
		<div class="comment-inner <?php echo ($depth == 1 ? 'default-background' : 'bordered-box'); ?>">
			<div class="comment-header clearfix">
				<div class="comment-author vcard">
					<?php if(0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>
					<?php printf(wp_kses(__('<div class="fn title-h6">%s</div>', 'thegem'), array('div' => array('class' => array()))), get_comment_author_link()); ?>
					<div class="comment-meta commentmetadata date-color"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID, $args)); ?>">
						<?php
							/* translators: 1: date, 2: time */
							printf(esc_html__('%1$s at %2$s', 'thegem'), get_comment_date(),  get_comment_time()); ?></a><?php edit_comment_link(esc_html__('(Edit)', 'thegem'), '&nbsp;&nbsp;', '');
						?>
					</div>
				</div>
				<div class="reply">
					<?php echo str_replace('comment-reply-link', 'comment-reply-link gem-button gem-button-style-outline gem-button-size-tiny', get_comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'])))); ?>
				</div>
			</div>
			<?php if('0' == $comment->comment_approved) : ?>
			<div class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'thegem') ?></div>
			<?php endif; ?>

			<div class="comment-text"><?php comment_text(get_comment_id(), array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?></div>

			<?php if('div' != $args['style']) : ?>
			</div>
			<?php endif; ?>
		</div>
<?php
}

function thegem_toparea_search_form() {
?>
<form role="search" method="get" id="top-area-searchform" class="searchform" action="<?php echo esc_url(home_url('/')); ?>">
	<div>
		<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="top-area-s" />
		<button type="submit" id="top-area-searchsubmit" value="<?php echo esc_attr_x('Search', 'submit button', 'thegem'); ?>"></button>
	</div>
</form>
<?php
}

function thegem_author_info($post_id, $full = FALSE) {
	$post = get_post($post_id);
	$user_id = $post->post_author;
	$user_data = get_userdata( $user_id );
	$thegem_post_elements = thegem_get_output_post_elements_data(get_the_ID());
	$show = TRUE;
	if(!$thegem_post_elements['show_author']) {
		$show = FALSE;
	}
	?>
	<?php if ($show): ?>
		<div class="post-author-block rounded-corners clearfix">
			<?php if ( get_the_author_meta('url', $user_id) ) : ?>
				<a href="<?php echo esc_url( get_the_author_meta('url', $user_id) ); ?>" class="post-author-avatar"><?php echo get_avatar( $user_id, 100 ); ?></a>
			<?php else : ?>
				<div class="post-author-avatar"><?php echo get_avatar( $user_id, 100 ); ?></div>
			<?php endif; ?>
			<div class="post-author-info">
				<div class="name title-h5"><?php the_author_meta('display_name', $user_id); ?> <span class="light"><?php esc_html_e('/ About Author', 'thegem'); ?></span></div>
				<div class="post-author-description"><?php echo do_shortcode(nl2br(get_the_author_meta('description', $user_id))); ?></div>
				<div class="post-author-posts-link"><a href="<?php echo esc_url(get_author_posts_url( $user_id )); ?>"><?php printf(esc_html__('More posts by %s', 'thegem'), $user_data->data->display_name); ?></a></div>
			</div>
		</div>
	<?php endif; ?>
<?php
}

function thegem_socials_sharing() {
	if(thegem_get_option('show_social_icons')) {
		get_template_part('socials', 'sharing');
	}
}

function thegem_post_tags() {
	$post_tags = wp_get_post_tags(get_the_ID());
	$post_tags_ids = array();
	foreach($post_tags as $individual_tag) {
		$post_tags_ids[] = $individual_tag->term_id;
	}
	if ($post_tags_ids) {
		$args=array(
			'tag__in' => $post_tags_ids,
			'post__not_in' => array(get_the_ID()),
			'posts_per_page'=>3,
			'orderby' => 'rand'
		);
		$related_query = new WP_Query( $args );
	}

	echo '<div class="block-tags">';
	echo '<div class="block-date">';
	echo get_the_date();
	echo '</div>';

	if ($post_tags_ids) {
		echo '<span class="sep"></span>';
	}
	$tag_list = get_the_tag_list( '', wp_kses(__( '<span class="sep"></span>', 'thegem' ), array('span' => array('class' => array()))) );
	if ( $tag_list ) {
		echo '<span class="tags-links">' . $tag_list . '</span>';
	}
	echo '</div>';
}

if (!function_exists('thegem_blog')) {
function thegem_blog($params = array()) {
	$params = array_merge(array(
		'blog_style' => 'default',
		'justified_style' => 'justified-style-1',
		'slider_style' => 'fullwidth',
		'slider_autoscroll' => 0,
		'blog_post_per_page' => '',
		'blog_categories' => '',
		'blog_post_types' => '',
		'blog_pagination' => '',
		'blog_ignore_sticky' => 0,
		'is_ajax' => 0,
		'paged' => -1,
		'effects_enabled' => 0,
		'loading_animation' => 'move-up',
		'hide_date' => 0,
		'hide_author' => 0,
		'hide_comments' => 0,
		'hide_likes' => 0,
		'button' => array(),
		'item_colors' => array(),
	), $params);

	$params['button'] = array_merge(array(
		'text' => __('Load More', 'thegem'),
		'style' => 'flat',
		'size' => 'medium',
		'text_weight' => 'normal',
		'no_uppercase' => 0,
		'corner' => 25,
		'border' => 2,
		'text_color' => '',
		'background_color' => '#00bcd5',
		'border_color' => '',
		'hover_text_color' => '',
		'hover_background_color' => '',
		'hover_border_color' => '',
		'icon_pack' => 'elegant',
		'icon_elegant' => '',
		'icon_material' => '',
		'icon_fontawesome' => '',
		'icon_userpack' => '',
		'icon_position' => 'left',
		'separator' => 'load-more',
	), $params['button']);

	$params['item_colors'] = array_merge(array(
		'background_color' => '',
		'post_title_color' => '',
		'post_title_hover_color' => '',
		'post_excerpt_color' => '',
		'sharing_button_color' => '',
		'sharing_button_icon_color' => '',
		'time_line_color' => '',
		'month_color' => '',
		'date_color' => '',
		'time_color' => '',
		'border_color' => '',
		'additional_background_color' => '',
	), $params['item_colors']);

	$params['button']['icon'] = '';
	if($params['button']['icon_elegant'] && $params['button']['icon_pack'] == 'elegant') {
		$params['button']['icon'] = $params['button']['icon_elegant'];
	}
	if($params['button']['icon_material'] && $params['button']['icon_pack'] == 'material') {
		$params['button']['icon'] = $params['button']['icon_material'];
	}
	if($params['button']['icon_fontawesome'] && $params['button']['icon_pack'] == 'fontawesome') {
		$params['button']['icon'] = $params['button']['icon_fontawesome'];
	}
	if($params['button']['icon_userpack'] && $params['button']['icon_pack'] == 'userpack') {
		$params['button']['icon'] = $params['button']['icon_userpack'];
	}

	$params['blog_pagination'] = thegem_check_array_value(array('normal', 'more', 'scroll', 'disable'), $params['blog_pagination'], 'normal');
	$params['justified_style'] = thegem_check_array_value(array('justified-style-1', 'justified-style-2'), $params['justified_style'], 'justified-style-1');
	$params['slider_style'] = thegem_check_array_value(array('fullwidth', 'halfwidth'), $params['slider_style'], 'fullwidth');
	$params['loading_animation'] = thegem_check_array_value(array('disabled', 'bounce', 'move-up', 'fade-in', 'fall-perspective', 'scale', 'flip'), $params['loading_animation'], 'move-up');

	if ($params['blog_pagination'] == 'scroll' && $params['blog_style'] != 'grid_carousel' && $params['blog_style'] != 'slider') {
		$params['effects_enabled'] = true;
	}

	$paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
	if ($params['blog_pagination'] == 'disable' || $params['blog_style'] == 'grid_carousel'|| $params['blog_style'] == 'slider')
		$paged = 1;

	if ($params['paged'] != -1)
		$paged = $params['paged'];

	$params['blog_style'] = thegem_check_array_value(array('default', 'timeline', 'timeline_new', '2x', '3x', '4x', 'justified-2x', 'justified-3x', 'justified-4x', '100%', 'grid_carousel', 'styled_list1', 'styled_list2', 'multi-author', 'compact', 'compact-2', 'slider'), $params['blog_style'], 'default');
	$params['blog_post_per_page'] = intval($params['blog_post_per_page']) > 0 ? intval($params['blog_post_per_page']) : 5;

	if(!is_array($params['blog_categories']) && $params['blog_categories']) {
		$params['blog_categories'] = explode(',', $params['blog_categories']);
	}

	$params['blog_post_types'] = is_array($params['blog_post_types']) && !empty($params['blog_post_types']) ? $params['blog_post_types'] : array('post');

	if ($params['blog_style'] == 'timeline_new') {
		$params['blog_ignore_sticky'] = 1;
	}

	$args = array(
		'post_type' => $params['blog_post_types'],
		'posts_per_page' => $params['blog_post_per_page'],
		'post_status' => 'publish',
		'ignore_sticky_posts' => $params['blog_ignore_sticky'],
		'paged' => $paged
	);
	if(!empty($params['blog_categories']) && !in_array('--all--', $params['blog_categories'])) {
		$args['tax_query'] = array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'category',
				'field' => 'slug',
				'terms' => $params['blog_categories']
			),
		);
		if(taxonomy_exists('thegem_news_sets')) {
			$args['tax_query'][] = array(
				'taxonomy' => 'thegem_news_sets',
				'field' => 'slug',
				'terms' => $params['blog_categories']
			);
		}
	}

	$posts = new WP_Query($args);

	$next_page = 0;
	if($params['blog_pagination'] == 'more' || $params['blog_pagination'] == 'scroll') {
		if($posts->max_num_pages > $paged)
			$next_page = $paged + 1;
		else
			$next_page = 0;
	}


	$blog_style = $params['blog_style'];

	wp_enqueue_style('thegem-blog');
	wp_enqueue_style('thegem-additional-blog');
	if ($blog_style == 'timeline' || $blog_style == 'timeline_new') {
		wp_enqueue_style('thegem-blog-timeline-new');
	}
	wp_enqueue_style('thegem-animations');

	if ($blog_style == '2x' || $blog_style == '3x' || $blog_style == '4x' || $blog_style == '100%' || $blog_style == 'timeline_new') {
		$enqueued_stript = 'thegem-blog-isotope';
	} else {
		$enqueued_stript = 'thegem-blog';
	}

	wp_enqueue_script($enqueued_stript);

	$localize = array_merge(
		array('data' => $params),
		array(
			'url' => esc_url(admin_url('admin-ajax.php')),
			'nonce' => wp_create_nonce('blog_ajax-nonce')
		)
	);
	if($params['blog_pagination'] == 'more' || $params['blog_pagination'] == 'scroll') {
		wp_localize_script($enqueued_stript, 'thegem_blog_ajax', $localize);
	}

	if ($params['effects_enabled']) {
		thegem_lazy_loading_enqueue();
	}

	if($posts->have_posts()) {
		if ($params['blog_style'] == 'grid_carousel') {
			wp_enqueue_script('thegem-news-carousel');
			echo '<div class="preloader"><div class="preloader-spin"></div></div>';
			echo '<div class="gem-news gem-news-type-carousel clearfix ' . ($params['effects_enabled'] ? 'lazy-loading' : '') . '" ' . ($params['effects_enabled'] ? 'data-ll-item-delay="0"' : '') . '>';
			while($posts->have_posts()) {
				$posts->the_post();
				include(locate_template('content-news-carousel-item.php'));
			}
			echo '</div>';
		} elseif ($params['blog_style'] == 'slider') {
			$thegem_slider_style = $params['slider_style'];
			wp_enqueue_script('thegem-news-carousel');
			echo '<div class="preloader"><div class="preloader-spin"></div></div>';
			echo '<div class="gem-blog-slider gem-blog-slider-style-'.$thegem_slider_style.' clearfix"'.(intval($params['slider_autoscroll']) ? ' data-autoscroll="'.intval($params['slider_autoscroll']).'"' : '').'>';
			while($posts->have_posts()) {
				$posts->the_post();
				include(locate_template('gem-templates/blog/content-blog-item-slider.php'));
			}
			echo '</div>';
		} else {
			if($params['is_ajax']) {
				echo '<div data-page="' . $paged . '" data-next-page="' . $next_page . '">';
			} else {
				if ($blog_style == '2x' || $blog_style == '3x' || $blog_style == '4x' || $blog_style == '100%')
					echo '<div class="preloader"><div class="preloader-spin"></div></div>';
				if ($blog_style == 'timeline_new') {
					echo '<div class="timeline_new-wrapper"><div class="timeline-new-line"'.(!empty($params['item_colors']['time_line_color']) ? ' style="background-color: '.esc_attr($params['item_colors']['time_line_color']).'"' : '').'></div>';
				}
				echo '<div class="blog blog-style-'.str_replace('%', '', $blog_style) . ($blog_style == 'timeline_new' ? ' blog-style-timeline' : '').' '. ($blog_style == 'justified-2x' || $blog_style == 'justified-3x' || $blog_style == 'justified-4x' && $params['justified_style'] ? $params['justified_style'].' inline-row' : '').' clearfix '.($blog_style == '2x' || $blog_style == '3x' || $blog_style == '4x' || $blog_style == '100%' ? 'blog-style-masonry ' : '').($blog_style == '100%' ? 'fullwidth-block' : '') . ' item-animation-' . $params['loading_animation'] . '" data-next-page="' . $next_page . '">';
			}

			$last_post_date = '';
			while($posts->have_posts()) {
				$posts->the_post();
				if($blog_style == '2x' || $blog_style == '3x' || $blog_style == '4x' || $blog_style == '100%') {
					include(locate_template(array('gem-templates/blog/content-blog-item-masonry.php', 'content-blog-item.php')));
				} elseif($blog_style == 'justified-2x' || $blog_style == 'justified-3x' || $blog_style == 'justified-4x') {
					include(locate_template(array('gem-templates/blog/content-blog-item-justified.php', 'content-blog-item.php')));
				} else {
					include(locate_template(array('gem-templates/blog/content-blog-item-'.$blog_style.'.php', 'content-blog-item.php')));
				}
				$last_post_date = get_the_date("M Y");
			}
			echo '</div>';
			if (!$params['is_ajax'] && $blog_style == 'timeline_new') {
				echo "</div>";
			}
			if ($params['blog_pagination'] == 'normal' && !$params['is_ajax'])
				thegem_pagination($posts);
			?>

			<?php if($params['blog_pagination'] == 'more' && !$params['is_ajax'] && $posts->max_num_pages > $paged): ?>
				<div class="blog-load-more <?php if ($blog_style == 'timeline_new') echo 'blog-load-more-style-timeline-new'?>">
					<div class="inner">
						<?php thegem_button(array_merge($params['button'], array('tag' => 'button', 'position' => 'center')), 1); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if($params['blog_pagination'] == 'scroll' && !$params['is_ajax'] && $posts->max_num_pages > $paged): ?>
				<div class="blog-scroll-pagination"></div>
			<?php endif; ?>

			<?php
		}
	}

	wp_reset_postdata();
}
}


function thegem_get_search_form($form) {
	$form = '<form role="search" method="get" id="searchform" class="searchform" action="' . esc_url(home_url('/')) . '">
				<div>
					<input type="text" value="' . get_search_query() . '" name="s" id="s" />
					 <button class="gem-button" type="submit" id="searchsubmit" value="' . esc_attr_x('Search', 'submit button', 'thegem') . '">'.esc_attr_x('Search', 'submit button', 'thegem').'</button>
				</div>
			</form>';
	return $form;
}
add_filter('get_search_form', 'thegem_get_search_form');

if(!function_exists('thegem_video_background')) {
function thegem_video_background($video_type, $video, $aspect_ratio = '16:9', $headerUp = false, $color = '', $opacity = '', $poster='') {
	$output = '';
	$video_type = thegem_check_array_value(array('', 'youtube', 'vimeo', 'self'), $video_type, '');
	if($video_type && $video) {
		$video_block = '';
		if($video_type == 'youtube' || $video_type == 'vimeo') {
			$link = '';
			if($video_type == 'youtube') {
				$link = '//www.youtube.com/embed/'.$video.'?playlist='.$video.'&autoplay=1&controls=0&loop=1&fs=0&showinfo=0&autohide=1&iv_load_policy=3&rel=0&disablekb=1&wmode=transparent';
			}
			if($video_type == 'vimeo') {
				$link = '//player.vimeo.com/video/'.$video.'?autoplay=1&controls=0&loop=1&title=0&badge=0&byline=0&autopause=0';
			}
			$video_block = '<iframe src="'.esc_url($link).'" frameborder="0"></iframe>';
		} else {
			$video_block = '<video autoplay="autoplay" controls="" loop="loop" src="'.esc_url($video).'" muted="muted"'.($poster ? ' poster="'.esc_url($poster).'"' : '').'></video>';
		}
		$overlay_css = '';
		if($color) {
			$overlay_css = 'background-color: '.$color.'; opacity: '.floatval($opacity).';';
		}
		$output = '<div class="gem-video-background" data-aspect-ratio="'.esc_attr($aspect_ratio).'"'.($headerUp ? ' data-headerup="1"' : '').'><div class="gem-video-background-inner">'.$video_block.'</div><div class="gem-video-background-overlay" style="'.$overlay_css.'"></div></div>';
	}
	return $output;
}
}

function thegem_aspect_ratio_to_percents($aspect_ratio) {
	if($aspect_ratio) {
		$aspect_ratio = explode(':', $aspect_ratio);
		if(count($aspect_ratio) > 1 && intval($aspect_ratio[0]) > 0 && intval($aspect_ratio[1]) > 0) {
			return round(intval($aspect_ratio[1])/intval($aspect_ratio[0]), 4)*100;
		}
	}
	return '56.25';
}

if(!function_exists('thegem_button')) {
function thegem_button($params = array(), $echo = false) {
	$params = array_merge(array(
		'tag' => 'a',
		'text' => '',
		'href' => '#',
		'target' => '_self',
		'title' => '',
		'style' => 'flat',
		'size' => 'small',
		'text_weight' => 'normal',
		'no-uppercase' => 0,
		'corner' => 3,
		'border' => 2,
		'position' => 'inline',
		'text_color' => '',
		'background_color' => '',
		'border_color' => '',
		'hover_text_color' => '',
		'hover_background_color' => '',
		'hover_border_color' => '',
		'icon' => '',
		'icon_pack' => '',
		'icon_position' => 'left',
		'separator' => '',
		'extra_class' => '',
		'id' => '',
		'attributes' => array(),
		'effects_enabled' => false,
		'gradient_backgound' => '',
		'gradient_backgound_from' => '',
		'gradient_backgound_to' => '',
		'gradient_backgound_hover_from' => '',
		'gradient_backgound_hover_to' => '',
		'gradient_backgound_style' => 'linear',
		'gradient_backgound_angle' => 'to bottom',
		'gradient_backgound_cusotom_deg' => '180',
		'gradient_radial_backgound_position' => 'at top',
		'gradient_radial_swap_colors' => '',

	), $params);

	$params['tag'] = thegem_check_array_value(array('a', 'button', 'input'), $params['tag'], 'a');
	$params['text'] = esc_html($params['text']);
	if($params['href'] === 'post_link') {
		$params['href'] = '{{ post_link_url }}';
	} else {
		$params['href'] = esc_url($params['href']);
	}
	$params['target'] = thegem_check_array_value(array('_self', '_blank'), trim($params['target']), '_self');
	$params['title'] = esc_attr($params['title']);
	$params['style'] = thegem_check_array_value(array('flat', 'outline'), $params['style'], 'flat');
	$params['size'] = thegem_check_array_value(array('tiny', 'small', 'medium', 'large', 'giant'), $params['size'], 'small');
	$params['text_weight'] = thegem_check_array_value(array('normal', 'thin'), $params['text_weight'], 'normal');
	$params['no-uppercase'] = $params['no-uppercase'] ? 1 : 0;
	$params['corner'] = intval($params['corner']) >= 0 ? intval($params['corner']) : 3;
	$params['border'] = thegem_check_array_value(array('1', '2', '3', '4', '5', '6'), $params['border'], '2');
	$params['position'] = thegem_check_array_value(array('inline', 'left', 'right', 'center', 'fullwidth'), $params['position'], 'inline');
	$params['text_color'] = esc_attr($params['text_color']);
	$params['background_color'] = esc_attr($params['background_color']);
	$params['border_color'] = esc_attr($params['border_color']);
	$params['hover_text_color'] = esc_attr($params['hover_text_color']);
	$params['hover_background_color'] = esc_attr($params['hover_background_color']);
	$params['hover_border_color'] = esc_attr($params['hover_border_color']);
	$params['icon'] = esc_attr($params['icon']);
	$params['icon_pack'] = thegem_check_array_value(array('thegem-icons', 'elegant', 'material', 'fontawesome', 'userpack'), $params['icon_pack'], 'thegem-icons');
	$params['icon_position'] = thegem_check_array_value(array('left', 'right'), $params['icon_position'], 'left');
	$params['separator'] = thegem_check_array_value(array('', 'single', 'square', 'soft-double', 'strong-double', 'load-more'), $params['separator'], '');
	$params['extra_class'] = esc_attr($params['extra_class']);
	$params['id'] = sanitize_title($params['id']);
	$params['gradient_backgound'] = $params['gradient_backgound'] ? 1 : 0;
	$params['gradient_radial_swap_colors'] = $params['gradient_radial_swap_colors'] ? 1 : 0;
	$params['gradient_backgound_from'] = esc_attr($params['gradient_backgound_from']);
	$params['gradient_backgound_to'] = esc_attr($params['gradient_backgound_to']);
	$params['gradient_backgound_hover_from'] = esc_attr($params['gradient_backgound_hover_from']);
	$params['gradient_backgound_hover_to'] = esc_attr($params['gradient_backgound_hover_to']);
	$params['gradient_backgound_style'] = thegem_check_array_value(array('linear', 'radial'), $params['gradient_backgound_style']);
	$params['gradient_backgound_angle'] = thegem_check_array_value(array('to bottom', 'to top','to right', 'to left', 'to bottom right', 'to top right', 'to bottom left', 'to top left', 'cusotom_deg'), $params['gradient_backgound_angle']);
	$params['gradient_backgound_cusotom_deg'] = esc_attr($params['gradient_backgound_cusotom_deg']);
	$params['gradient_radial_backgound_position'] = thegem_check_array_value(array('at top', 'at bottom', 'at right', 'at left', 'at center'), $params['gradient_radial_backgound_position']);

	if ($params['effects_enabled']) {
		thegem_lazy_loading_enqueue();
	}

	$sep = '';
	if($params['separator']) {
		$params['position'] = 'center';
		if($params['style'] == 'flat') {
			$sep_color = $params['background_color'] ? $params['background_color'] : thegem_get_option('button_background_basic_color');
		} else {
			$sep_color = $params['border_color'] ? $params['border_color'] : thegem_get_option('button_outline_border_basic_color');
		}
		if($params['separator'] == 'load-more') {
			$sep_color = thegem_get_option('box_border_color');
		}
		if($params['separator'] == 'square') {
			$sep.= '<div class="gem-button-separator-line"><svg width="100%" height="8px"><line x1="4" x2="100%" y1="4" y2="4" stroke="'.esc_attr($sep_color).'" stroke-width="8" stroke-linecap="square" stroke-dasharray="0, 15"/></svg></div>';
		} else {
			$sep.= '<div class="gem-button-separator-holder"><div class="gem-button-separator-line" style="border-color: '.esc_attr($sep_color).';"></div></div>';
		}
	}

	$output = '';

	$output .= '<div'.($params['id'] ? ' id="'.esc_attr($params['id']).'"' : '').' class="'.esc_attr('gem-button-container gem-button-position-'.$params['position'].($params['extra_class'] ? ' '.$params['extra_class'] : '').($params['separator'] ? ' gem-button-with-separator' : '') . ($params['effects_enabled'] ? ' lazy-loading' : '') ).'">';
	if($params['separator']) {
		$output .= '<div class="gem-button-separator gem-button-separator-type-'.esc_attr($params['separator']).'">'.$sep.'<div class="gem-button-separator-button">';
	}
	$output .= '<'.$params['tag'];
	if($params['title']) {
		$output .= ' title="'.esc_attr($params['title']).'"';
	}
	$output .= ' class="'.esc_attr('gem-button gem-button-size-'.$params['size'].' gem-button-style-'.$params['style'].' gem-button-text-weight-'.$params['text_weight'].($params['style'] == 'outline' ? ' gem-button-border-'.$params['border'] : '').($params['text'] == '' ? ' gem-button-empty' : '').($params['icon'] && $params['text'] != '' ? ' gem-button-icon-position-'.$params['icon_position'] : '').($params['no-uppercase'] ? ' gem-button-no-uppercase' : '').(empty($params['attributes']['class']) ? '' : ' '.$params['attributes']['class']) . ($params['effects_enabled'] ? ' lazy-loading-item' : '')) .'"';
	$output .= $params['effects_enabled'] ? 'data-ll-effect="drop-right-without-wrap"' : '';
	$output .= ' style="';
	$output .= 'border-radius: '.esc_attr($params['corner']).'px;';
	if($params['style'] == 'outline' && $params['border_color']) {
		$output .= 'border-color: '.esc_attr($params['border_color']).';';
	}
	if($params['style'] == 'flat' && $params['background_color']) {
		$output .= 'background-color: '.esc_attr($params['background_color']).';';
	}
	if ($params['gradient_backgound_angle'] == 'cusotom_deg') {
		$params['gradient_backgound_angle'] = $params['gradient_backgound_cusotom_deg'].'deg';
	}

	if($params['gradient_backgound'] == 1 && $params['gradient_backgound_style'] == 'linear') {
		$output .= 'background: linear-gradient('.$params['gradient_backgound_angle'].', '.$params['gradient_backgound_from'].', '.$params['gradient_backgound_to'].');';
	}
	if($params['gradient_backgound'] == 1 && $params['gradient_backgound_style'] == 'radial') {
		$output .= 'background: radial-gradient('.$params['gradient_radial_backgound_position'].', '.$params['gradient_backgound_from'].', '.$params['gradient_backgound_to'].');';
	}
	if($params['gradient_backgound'] == 1 && $params['gradient_backgound_style'] == 'radial' && $params['gradient_radial_swap_colors'] == 1 )  {
		$output .= 'background: radial-gradient('.$params['gradient_radial_backgound_position'].', '.$params['gradient_backgound_to'].', '.$params['gradient_backgound_from'].');';
	}

	if($params['text_color']) {
		$output .= 'color: '.esc_attr($params['text_color']).';';
	}
	$output .= '"';
	$output .= ' onmouseleave="';
	if($params['style'] == 'outline' && $params['border_color']) {
		$output .= 'this.style.borderColor=\''.esc_attr($params['border_color']).'\';';
	}
	if($params['style'] == 'flat' && $params['background_color']) {
		$output .= 'this.style.backgroundColor=\''.esc_attr($params['background_color']).'\';';
	}
	if($params['style'] == 'outline' && $params['hover_background_color']) {
		$output .= 'this.style.backgroundColor=\'transparent\';';
	}
	if($params['text_color']) {
		$output .= 'this.style.color=\''.esc_attr($params['text_color']).'\';';
	}
	if($params['gradient_backgound'] == 1 && $params['gradient_backgound_style'] == 'linear') {
		$output .= 'this.style.background=\'linear-gradient(' . $params['gradient_backgound_angle'] .' , '.  $params['gradient_backgound_from'] .' , '. $params['gradient_backgound_to'].')\';';
	}
	if($params['gradient_backgound'] == 1 && $params['gradient_backgound_style'] == 'radial') {
		$output .= 'this.style.background=\'radial-gradient(' . $params['gradient_radial_backgound_position'] .' , '.  $params['gradient_backgound_from'] .' , '. $params['gradient_backgound_to'].')\';';
	}
	if($params['gradient_backgound'] == 1 && $params['gradient_backgound_style'] == 'radial' && $params['gradient_radial_swap_colors'] == 1 )  {
		$output .= 'this.style.background=\'radial-gradient(' . $params['gradient_radial_backgound_position'] .' , '.  $params['gradient_backgound_to'] .' , '. $params['gradient_backgound_from'].')\';';
	}
	$output .= '"';
	$output .= ' onmouseenter="';
	if($params['hover_border_color']) {
		$output .= 'this.style.borderColor=\''.esc_attr($params['hover_border_color']).'\';';
	}
	if($params['hover_background_color']) {
		$output .= 'this.style.backgroundColor=\''.esc_attr($params['hover_background_color']).'\';';
	}
	if($params['hover_text_color']) {
		$output .= 'this.style.color=\''.esc_attr($params['hover_text_color']).'\';';
	}
	if($params['gradient_backgound'] == 1 && $params['gradient_backgound_style'] == 'linear') {
		$output .= 'this.style.background=\'linear-gradient(' . $params['gradient_backgound_angle'] .' , '.  $params['gradient_backgound_hover_from'] .' , '. $params['gradient_backgound_hover_to'].')\';';
	}
	if($params['gradient_backgound'] == 1 && $params['gradient_backgound_style'] == 'radial') {
		$output .= 'this.style.background=\'radial-gradient(' . $params['gradient_radial_backgound_position'] .' , '.  $params['gradient_backgound_hover_from'] .' , '. $params['gradient_backgound_hover_to'].')\';';
	}
	if($params['gradient_backgound'] == 1 && $params['gradient_backgound_style'] == 'radial' && $params['gradient_radial_swap_colors'] == 1 ) {
		$output .= 'this.style.background=\'radial-gradient(' . $params['gradient_radial_backgound_position'] .' , '.  $params['gradient_backgound_hover_to'] .' , '. $params['gradient_backgound_hover_from'].')\';';

	}
	$output .= '"';
	if($params['tag'] == 'a') {
		$output .= ' href="'.$params['href'].'"';
		$output .= ' target="'.esc_attr($params['target']).'"';
	}
	if(!empty($params['attributes']) && is_array($params['attributes'])) {
		foreach($params['attributes'] as $param => $value) {
			if($param != 'class') {
				$output .= ' '.esc_attr($param).'="'.esc_attr($value).'"';
			}
		}
	}
	if($params['tag'] != 'input') {
		$output .= '>';
		if($params['icon']) {
			if($params['icon_position'] == 'left') {
				$output .= thegem_build_icon($params['icon_pack'], $params['icon']).$params['text'];
			} else {
				$output .= $params['text'].thegem_build_icon($params['icon_pack'], $params['icon']);
			}
		} else {
			$output .= $params['text'];
		}
		$output .= '</'.$params['tag'].'>';
	} else {
		$output .= ' />';
	}
	if($params['separator']) {
		$output .= '</div>'.$sep.'</div>';
	}
	$output .= '</div> ';
	if($echo) {
		echo $output;
	} else {
		return $output;
	}
}
}

if(!function_exists('thegem_build_icon')) {
function thegem_build_icon($pack, $icon, $class = '') {
	if($icon) {
		if(in_array($pack, array('elegant', 'material', 'fontawesome', 'userpack', 'thegemdemo'))) {
			wp_enqueue_style('icons-'.$pack);
			return '<i class="gem-print-icon gem-icon-pack-'.esc_attr($pack).' '.esc_attr($class).'">&#x'.$icon.';</i>';
		} else {
			return '<i class="gem-print-icon gem-icon-pack-'.esc_attr($pack).' gem-icon-'.esc_attr($icon).' '.esc_attr($class).'"></i>';
		}
	}
}
}

function thegem_get_post_featured_content($post_id, $thumb_size = 'thegem-blog-default-large', $single = false, $picture_sources=array()) {
	$format = get_post_format($post_id);
	$post_item_data = thegem_get_sanitize_post_data($post_id);
	$output = '';
	if($post_item_data['show_featured_content'] || !$single) {
		if($format == 'video' && $post_item_data['video']) {
			$aspect_percents = thegem_aspect_ratio_to_percents($post_item_data['video_aspect_ratio']);
			$video_block = '';
			if($post_item_data['video_type'] == 'youtube') {
				$video_block = '<iframe frameborder="0" allowfullscreen="allowfullscreen" scrolling="no" marginheight="0" marginwidth="0" src="'.esc_url('//www.youtube.com/embed/'.$post_item_data['video'].'?rel=0&amp;wmode=opaque').'"></iframe>';
				if (class_exists('TheGemGdpr')) {
					$video_block = TheGemGdpr::getInstance()->replace_disallowed_content($video_block, TheGemGdpr::CONSENT_NAME_YOUTUBE);
				}
			} elseif($post_item_data['video_type'] == 'vimeo') {
				$video_block = '<iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.esc_url('//player.vimeo.com/video/'.$post_item_data['video'].'?title=0&amp;byline=0&amp;portrait=0').'"></iframe>';
				if (class_exists('TheGemGdpr')) {
					$video_block = TheGemGdpr::getInstance()->replace_disallowed_content($video_block, TheGemGdpr::CONSENT_NAME_VIMEO);
				}
			} else {
				wp_enqueue_style('wp-mediaelement');
				wp_enqueue_script('thegem-mediaelement');
				$img = thegem_generate_thumbnail_src(get_post_thumbnail_id($post_id), $thumb_size);
				$video_block = '<video width="100%" height="100%" controls="controls" src="'.esc_url($post_item_data['video']).'" '.(has_post_thumbnail() ? ' poster="'.esc_url($img[0]).'"' : '').' preload="none"></video>';
			}
			$output = '<div class="video-block" style="padding-top: '.esc_attr($aspect_percents).'%;">'.$video_block.'</div>';
		} elseif($format == 'audio' && $post_item_data['audio']) {
			wp_enqueue_style('wp-mediaelement');
			wp_enqueue_script('thegem-mediaelement');
			$output = '<div class="audio-block"><audio width="100%" controls="controls" src="'.esc_url($post_item_data['audio']).'" preload="none"></audio></div>';
		} elseif($format == 'gallery' && thegem_is_plugin_active('thegem-elements/thegem-elements.php') && $post_item_data['gallery']) {
			ob_start();
			thegem_simple_gallery(array('gallery' => $post_item_data['gallery'], 'thumb_size' => $thumb_size, 'autoscroll' => $post_item_data['gallery_autoscroll'], 'responsive' => 1));
			$output = ob_get_clean();
		} elseif($format == 'quote' && $post_item_data['quote_text']) {
			$output = '<blockquote'.($post_item_data['quote_background'] ? ' style="background-color: '.$post_item_data['quote_background'].';"' : '').'>'.$post_item_data['quote_text'];
			if($post_item_data['quote_author'] || !$single) {
				$quote_author = $post_item_data['quote_author'] ? '<div class="quote-author"'.($post_item_data['quote_author_color'] ? ' style="color: '.$post_item_data['quote_author_color'].';"' : '').'>'.$post_item_data['quote_author'].'</div>' : '';
				$quote_link = !$single ? '<div class="quote-link"'.($post_item_data['quote_author_color'] ? ' style="color: '.$post_item_data['quote_author_color'].';"' : '').'><a href="'.esc_url(get_permalink($post_id)).'"></a></div>' : '';
				$output .= '<div class="quote-bottom clearfix">'.$quote_author.$quote_link.'</div>';
			}
			$output .= '</blockquote>';
		} elseif(has_post_thumbnail()) {
			ob_start();
			thegem_generate_picture(get_post_thumbnail_id($post_id), $thumb_size, $picture_sources, array('class' => 'img-responsive', 'alt' => get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true)));
			$image = ob_get_clean();
			if($single) {
				$output = $image;
			} else {
				$output = '<a href="'.esc_url(get_permalink($post_id)).'">'.$image.'</a>';
			}
		}
		$output = $output ? '<div class="post-featured-content">'.$output.'</div>' : '';
	}
	return $output;
}

if(!function_exists('thegem_add_srcset_rule')) {
	function thegem_add_srcset_rule(&$srcset, $condition, $size, $id=false) {
		if (!$id) {
			$id = get_post_thumbnail_id();
		}
		$im = thegem_generate_thumbnail_src($id, $size);
		$srcset[$condition] = $im[0];
	}
}

if(!function_exists('thegem_srcset_list_to_string')) {
	function thegem_srcset_list_to_string($srcset) {
		if (count($srcset) == 0) {
			return '';
		}
		$srcset_condtions = array();
		foreach ($srcset as $condition => $url) {
			$srcset_condtions[] = $url . ' ' . $condition;
		}
		return implode(', ', $srcset_condtions);
	}
}

if(!function_exists('thegem_quickfinder_srcset')) {
	function thegem_quickfinder_srcset($thegem_item_data) {
		$attr = array('srcset' => array());

		switch ($thegem_item_data['icon_size']) {
			case 'small':
			case 'medium':
				$attr['srcset']['1x'] = 'thegem-person-80';
				$attr['srcset']['2x'] = 'thegem-person-160';
				break;

			case 'large':
				$attr['srcset']['1x'] = 'thegem-person-160';
				$attr['srcset']['2x'] = 'thegem-person';
				break;

			case 'xlarge':
				$attr['srcset']['1x'] = 'thegem-person-240';
				$attr['srcset']['2x'] = 'thegem-person';
				break;
		}

		return $attr;
	}
}

if(!function_exists('thegem_post_picture')) {
	function thegem_post_picture($default_size, $sources=array(), $attrs=array(), $dummy = true) {
		if (has_post_thumbnail()) {
			thegem_generate_picture(get_post_thumbnail_id(), $default_size, $sources, $attrs);
		} elseif ($dummy) {
			if (empty($attrs['class'])) {
				$attrs['class'] = 'gem-dummy';
			} else {
				$attrs['class'] .= ' gem-dummy';
			}
			echo '<span class="' . esc_attr($attrs['class']) . '"></span>';
		}
	}
}

if(!function_exists('thegem_generate_picture')) {
	function thegem_generate_picture($attachment_id, $default_size, $sources=array(), $attrs=array(), $return_info=false) {
		if (!$attachment_id || !in_array($default_size, array_keys(thegem_image_sizes()))) {
			return '';
		}
		$default_image = thegem_generate_thumbnail_src($attachment_id, $default_size);
		if (!$default_image) {
			return '';
		}
		list($src, $width, $height) = $default_image;
		$hwstring = image_hwstring($width, $height);

		$default_attrs = array('class' => "attachment-$default_size");
		if (empty($attrs['alt'])) {
			$attachment = get_post($attachment_id);
			$attrs['alt'] = trim(strip_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)));
			if(empty($default_attr['alt']))
				$attrs['alt'] = trim(strip_tags($attachment->post_excerpt));
			if(empty($default_attr['alt']))
				$attrs['alt'] = trim(strip_tags($attachment->post_title));
		}

		$attrs = wp_parse_args($attrs, $default_attrs);
		$attrs = array_map('esc_attr', $attrs);
		$attrs_set = array();
		foreach ($attrs as $attr_key => $attr_value) {
			$attrs_set[] = $attr_key . '="' . $attr_value . '"';
		}
		?>
		<picture>
			<?php thegem_generate_picture_sources($attachment_id, $sources); ?>
			<img src="<?php echo $src; ?>" <?php echo $hwstring; ?> <?php echo implode(' ', $attrs_set); ?> />
		</picture>
		<?php
		if ($return_info) {
			return array(
				'default' => $default_image
			);
		}
	}
}

if(!function_exists('thegem_generate_picture_sources')) {
	function thegem_generate_picture_sources($attachment_id, $sources) {
		if (!$sources) {
			return '';
		}
		?>
		<?php foreach ($sources as $source): ?>
			<?php
				$srcset = thegem_srcset_generate_urls($attachment_id, $source['srcset']);
				if (!$srcset) {
					continue;
				}
			?>
			<source srcset="<?php echo thegem_srcset_list_to_string($srcset); ?>" <?php if(!empty($source['media'])): ?>media="<?php echo esc_attr($source['media']); ?>"<?php endif; ?> <?php if(!empty($source['type'])): ?>type="<?php echo esc_attr($source['type']); ?>"<?php endif; ?> sizes="<?php echo !empty($source['sizes']) ? esc_attr($source['sizes']) : '100vw'; ?>">
		<?php endforeach; ?>
		<?php
	}
}

if(!function_exists('thegem_srcset_generate_urls')) {
	function thegem_srcset_generate_urls($attachment_id, $srcset) {
		$result = array();
		$thegem_sizes = array_keys(thegem_image_sizes());
		foreach ($srcset as $condition => $size) {
			if (!in_array($size, $thegem_sizes)) {
				continue;
			}
			$im = thegem_generate_thumbnail_src($attachment_id, $size);
			$result[$condition] = esc_url($im[0]);
		}
		return $result;
	}
}

function thegem_page_options_get_post_data($page_data, $post_id, $item_data, $type) {
	if($post_id == 0) {
		if(is_search() && $type != 'search') {
			$page_data = thegem_theme_options_get_page_settings('search');
		}
		if(is_home() && $type != 'blog') {
			$page_data = thegem_theme_options_get_page_settings('blog');
		}
	}
	return $page_data;
}
add_filter('thegem_page_title_data', 'thegem_page_options_get_post_data', 10, 4);
add_filter('thegem_page_header_data', 'thegem_page_options_get_post_data', 10, 4);
add_filter('thegem_page_effects_data', 'thegem_page_options_get_post_data', 10, 4);
add_filter('thegem_page_preloader_data', 'thegem_page_options_get_post_data', 10, 4);
add_filter('thegem_page_slideshow_data', 'thegem_page_options_get_post_data', 10, 4);
add_filter('thegem_page_sidebar_data', 'thegem_page_options_get_post_data', 10, 4);

function thegem_remove_hentry_class($classes) {
	$classes = array_diff($classes, array('hentry'));
	return $classes;
}
add_filter('post_class', 'thegem_remove_hentry_class');

function thegem_get_attached_file_filter($file, $attachment_id) {
	if ($attachment_id === 'THEGEM_TRANSPARENT_IMAGE') {
		return get_template_directory() . '/images/dummy/transparent.png';
	}

	return $file;
}
add_filter('get_attached_file', 'thegem_get_attached_file_filter', 10, 2);

function thegem_attachment_thumbnail_path_filter($thumb_path, $attachment_id) {
	if ($attachment_id === 'THEGEM_TRANSPARENT_IMAGE') {
		$uploads = wp_upload_dir();
		return $uploads['path'] . '/' . basename($thumb_path);
	}

	return $thumb_path;
}
add_filter('thegem_attachment_thumbnail_path', 'thegem_attachment_thumbnail_path_filter', 10, 2);

function thegem_wpcf7_form_class_attr($class) {
	if(substr_count($class, 'gem-contact-form-white')) {
		$GLOBALS['thegem_wpcf_style'] = 'white';
	}
	if(substr_count($class, 'gem-contact-form-dark')) {
		$GLOBALS['thegem_wpcf_style'] = 'dark';
	}
	return $class;
}
add_filter( 'wpcf7_form_class_attr', 'thegem_wpcf7_form_class_attr');

function thegem_wpcf7_form_response_output($output) {
	if(!empty($GLOBALS['thegem_wpcf_style'])) {
		unset($GLOBALS['thegem_wpcf_style']);
	}
	return $output;
}
add_filter( 'wpcf7_form_response_output', 'thegem_wpcf7_form_response_output');

remove_action( 'wpcf7_init', 'wpcf7_add_form_tag_submit' );
add_action( 'wpcf7_init', 'thegem_wpcf7_add_form_tag_submit' );
function thegem_wpcf7_add_form_tag_submit() {
	wpcf7_add_form_tag( 'submit', 'thegem_wpcf7_submit_form_tag_handler' );
}

function thegem_wpcf7_submit_form_tag_handler( $tag ) {
	$class = wpcf7_form_controls_class( $tag->type );

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $value ) ) {
		$value = __( 'Send', 'contact-form-7' );
	}

	$atts['type'] = 'submit';
	$atts['value'] = $value;

	if(isset($GLOBALS['thegem_wpcf_style']) && ($GLOBALS['thegem_wpcf_style'] == 'white' || $GLOBALS['thegem_wpcf_style'] == 'dark')) {
		$form_style = $GLOBALS['thegem_wpcf_style'] == 'white' ? 'light' : 'dark';
		if(thegem_get_option('contact_form_'.$form_style.'_custom_styles')) {
			$atts['class'] .= ' gem-button-wpcf-custom';
			$html = thegem_button(array(
				'tag' => 'input',
				'text' => $value,
				'style' => thegem_get_option('contact_form_'.$form_style.'_button_style'),
				'size' => thegem_get_option('contact_form_'.$form_style.'_button_size'),
				'text_weight' => thegem_get_option('contact_form_'.$form_style.'_button_text_weight'),
				'no-uppercase' => thegem_get_option('contact_form_'.$form_style.'_button_no_uppercase'),
				'corner' => thegem_get_option('contact_form_'.$form_style.'_button_corner'),
				'border' => thegem_get_option('contact_form_'.$form_style.'_button_border'),
				'position' => thegem_get_option('contact_form_'.$form_style.'_button_position'),
				'text_color' => thegem_get_option('contact_form_'.$form_style.'_button_text_color'),
				'background_color' => thegem_get_option('contact_form_'.$form_style.'_button_background_color'),
				'border_color' => thegem_get_option('contact_form_'.$form_style.'_button_border_color'),
				'hover_text_color' => thegem_get_option('contact_form_'.$form_style.'_button_hover_text_color'),
				'hover_background_color' => thegem_get_option('contact_form_'.$form_style.'_button_hover_background_color'),
				'hover_border_color' => thegem_get_option('contact_form_'.$form_style.'_button_hover_border_color'),
				'attributes' => $atts,
			));
			return $html;
		}
	}

	$atts = wpcf7_format_atts( $atts );
	$html = sprintf( '<input %1$s />', $atts );

	return $html;
}

function thegem_yikes_mailchimp_form_submit_button_classes($classes) {
	return $classes.' gem-button';
}
add_filter('yikes-mailchimp-form-submit-button-classes', 'thegem_yikes_mailchimp_form_submit_button_classes');
