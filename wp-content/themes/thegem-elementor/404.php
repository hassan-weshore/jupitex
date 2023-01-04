<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 */

$thegem_use_custom = get_post(thegem_get_option('404_page'));

$thegem_q = new WP_Query(array('page_id' => thegem_get_option('404_page')));

get_header(); ?>

<div id="main-content" class="main-content">

<?php if(thegem_get_option('404_page') && $thegem_use_custom && $thegem_q->have_posts()) : $thegem_q->the_post(); $GLOBALS['thagem_page_404'] = get_the_id(); ?>

<?php
	if(get_page_template_slug(get_the_ID()) === 'page-fullwidth.php' ) {
		get_template_part( 'content', 'page-fullwidth' );
	} else {
		get_template_part( 'content', 'page' );
	}
?>

<?php else : ?>
<?php echo thegem_page_title(); ?>

<div class="block-content">
	<div class="container">
		<div class="entry-content page-content content-none">
			<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'thegem' ); ?></p>
			<?php add_filter( 'get_search_form', 'thegem_serch_form_nothing_found' ); get_search_form(); remove_filter( 'get_search_form', 'thegem_serch_form_nothing_found' ); ?>
		</div><!-- .entry-content -->
	</div>
</div>
<?php endif; ?>

</div><!-- #main-content -->

<?php
get_footer();
