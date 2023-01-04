<?php

if ( ! function_exists( 'thegem_paging_nav' ) ) :

function thegem_paging_nav() {
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $GLOBALS['wp_query']->max_num_pages,
		'current'  => $paged,
		'mid_size' => 1,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => esc_html__( '&larr; Previous', 'thegem' ),
		'next_text' => esc_html__( 'Next &rarr;', 'thegem' ),
	) );

	if ( $links ) :

	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'thegem' ); ?></h1>
		<div class="pagination loop-pagination">
			<?php echo $links; ?>
		</div><!-- .pagination -->
	</nav><!-- .navigation -->
	<?php
	endif;
}
endif;

if ( ! function_exists( 'thegem_post_nav' ) ) :
function thegem_post_nav() {
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}

	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'thegem' ); ?></h1>
		<div class="nav-links">
			<?php
			if ( is_attachment() ) :
				previous_post_link( '%link', wp_kses(__( '<span class="meta-nav">Published In</span>%title', 'thegem' ), array('span' => array('class' => array()))) );
			else :
				previous_post_link( '%link', wp_kses(__( '<span class="meta-nav">Previous Post</span>%title', 'thegem' ), array('span' => array('class' => array()))) );
				next_post_link( '%link', wp_kses(__( '<span class="meta-nav">Next Post</span>%title', 'thegem' ), array('span' => array('class' => array()))) );
			endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'thegem_posted_on' ) ) :
function thegem_posted_on() {
	printf( '<span class="entry-date">%1$s</span>', esc_html(get_the_date()));
}
endif;

function thegem_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'thegem_category_count' ) ) ) {
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'thegem_category_count', $all_the_cool_cats );
	}

	if ( 1 !== (int) $all_the_cool_cats ) {
		return true;
	} else {
		return false;
	}
}

function thegem_category_transient_flusher() {
	delete_transient( 'thegem_category_count' );
}
add_action( 'edit_category', 'thegem_category_transient_flusher' );
add_action( 'save_post',     'thegem_category_transient_flusher' );