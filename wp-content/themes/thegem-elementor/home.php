<?php

get_header();

$thegem_output_settings = thegem_get_output_page_settings(0, thegem_theme_options_get_page_settings('blog'), 'blog');
$thegem_page_data = array(
	'title' => $thegem_output_settings,
	'effects' => $thegem_output_settings,
	'slideshow' => $thegem_output_settings,
	'sidebar' => $thegem_output_settings
);

$thegem_page_effects = $thegem_page_data['effects'];

$thegem_no_margins_block = '';
if ($thegem_page_effects['effects_no_bottom_margin'] || $thegem_page_effects['content_padding_bottom'] === 0) {
	$thegem_no_margins_block .= ' no-bottom-margin';
}
if ($thegem_page_effects['effects_no_top_margin'] || $thegem_page_effects['content_padding_top'] === 0) {
	$thegem_no_margins_block .= ' no-top-margin';
}

$thegem_panel_classes = array('panel', 'row');
$thegem_center_classes = 'panel-center';
$thegem_sidebar_classes = '';
if (is_active_sidebar('page-sidebar') && $thegem_page_data['sidebar']['sidebar_show'] && $thegem_page_data['sidebar']['sidebar_position']) {
	$thegem_panel_classes[] = 'panel-sidebar-position-' . $thegem_page_data['sidebar']['sidebar_position'];
	$thegem_panel_classes[] = 'with-sidebar';
	$thegem_center_classes .= ' col-lg-9 col-md-9 col-sm-12';
	if ($thegem_page_data['sidebar']['sidebar_position'] == 'left') {
		$thegem_center_classes .= ' col-md-push-3 col-sm-push-0';
		$thegem_sidebar_classes .= ' col-md-pull-9 col-sm-pull-0';
	}
} else {
	$thegem_center_classes .= ' col-xs-12';
}
if ($thegem_page_data['sidebar']['sidebar_sticky']) {
	$thegem_panel_classes[] = 'panel-sidebar-sticky';
	wp_enqueue_script('thegem-sticky');
}

?>

<div id="main-content" class="main-content">

<?php

if(thegem_get_option('home_content_enabled')) :

	thegem_home_content_builder();

else :

	wp_enqueue_style('thegem-blog');
	wp_enqueue_style('thegem-additional-blog');
	wp_enqueue_style('thegem-blog-timeline-new');
	wp_enqueue_script('thegem-scroll-monitor');
	wp_enqueue_script('thegem-items-animations');
	wp_enqueue_script('thegem-blog');
	wp_enqueue_script('thegem-gallery');

?>

	<div class="block-content">
		<div class="container">
			<div class="<?php echo esc_attr(implode(' ', $thegem_panel_classes)); ?>">
				<div class="<?php echo esc_attr($thegem_center_classes); ?>">
				<?php
					if ( have_posts() ) {

						if(!is_singular()) { echo '<div class="blog blog-style-default item-animation-disabled">'; }

						while ( have_posts() ) : the_post();

							get_template_part( 'content', 'blog-item' );

						endwhile;

						if(!is_singular()) { thegem_pagination(); echo '</div>'; }

					} else {
						get_template_part( 'content', 'none' );
					}
				?>
				</div>
				<?php
					if(is_active_sidebar('page-sidebar') && $thegem_page_data['sidebar']['sidebar_show'] && $thegem_page_data['sidebar']['sidebar_position']) {
						echo '<div class="sidebar col-lg-3 col-md-3 col-sm-12'.esc_attr($thegem_sidebar_classes).'" role="complementary">';
						get_sidebar('page');
						echo '</div><!-- .sidebar -->';
					}
				?>
			</div>
		</div><!-- .container -->
	</div><!-- .block-content -->

<?php endif; ?>

</div><!-- #main-content -->

<?php

get_footer();
