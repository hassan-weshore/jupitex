<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Icons_Manager;

$thegem_post_data = thegem_get_sanitize_page_title_data(get_the_ID());

$thegem_categories = get_the_category();
$thegem_categories_list = array();
foreach($thegem_categories as $thegem_category) {
	$thegem_categories_list[] = '<a href="'.esc_url(get_category_link( $thegem_category->term_id )).'" title="'.esc_attr( sprintf( __( "View all posts in %s", "thegem" ), $thegem_category->name ) ).'">'.$thegem_category->cat_name.'</a>';
}

$thegem_classes = array();

if(is_sticky() && !is_paged()) {
	$thegem_classes = array_merge($thegem_classes, array('sticky', 'default-background'));
}

$thegem_link = get_permalink();

$thegem_classes[] = 'item-animations-not-inited';

if (!has_post_thumbnail())
	$thegem_classes[] = 'no-image';

?>

	<article id="post-<?php the_ID(); ?>" <?php post_class($thegem_classes); ?> class="post">
		<div class="item-post-container">
			<div class="post-item clearfix caption-container">

					<?php if( ! is_single() && is_sticky() && ! is_paged() ) {?>
						<div class="sticky-label">
							<div class="elementor-icon">
								<?php if ($settings['sticky_label_icon']['value']) {
									Icons_Manager::render_icon( $settings['sticky_label_icon'], [ 'aria-hidden' => 'true' ] );
								} else { ?>
									<i class="default"></i>
								<?php } ?>
							</div>
						</div>
					<?php } elseif (is_sticky() && is_admin()) {	?>
						<div class="sticky-label">
							<div class="elementor-icon">
								<?php if ($settings['sticky_label_icon']['value']) {
									Icons_Manager::render_icon( $settings['sticky_label_icon'], [ 'aria-hidden' => 'true' ] );
								} else { ?>
									<i class="default"></i>
								<?php } ?>
							</div>
						</div>
					<?php } ?>

				<div class="post-info-wrap <?php echo ( $settings['show_featured_image'] ) ? '' : 'image-disabled'; ?>">
					<div class="post-info">
						<?php if( has_post_thumbnail() && $settings['show_featured_image'] ): ?>
							<div class="post-img">
								<a href="<?php echo esc_url($thegem_link); ?>" class="default"><?php thegem_post_thumbnail('thegem-post-thumb-large', true, 'img-responsive', array('srcset' => array('1x' => 'thegem-post-thumb-small', '2x' => 'thegem-post-thumb-large'))); ?></a>
							</div>
						<?php elseif( $settings['show_featured_image'] ): ?>
							<div class="post-img">
								<a href="<?php echo esc_url($thegem_link); ?>" class="default"><span class="dummy">&#xe640</span></a>
							</div>
						<?php endif; ?>
						
						<?php if ( $settings['show_date'] ) : ?>
							<div class="post-date"><?php echo get_the_date('d F') ?></div>
							<div class="post-time"><?php echo get_the_date('H:i') ?></div>
						<?php endif; ?>

					</div>
				</div>

				<?php if ( 'yes' === ( $settings['caption_container_bubbles_show'] ) ) : ?>
					<svg class="wrap-style"><path id="dec-post-arrow" d="M 50,0 Q 45,45 0,50 Q 45,55 50,100" /></svg>
				<?php endif; ?>

				<div class="post-text-wrap">

					<div class="post-title">
						<?php if ( 'yes' === ( $settings['show_title'] ) ) : ?>
						<?php the_title('<'.(is_sticky() && !is_paged() ? 'h2' : 'h3').' class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark"><span class="light">', '</span></a></'.(is_sticky() && !is_paged() ? 'h2' : 'h3').'>'); ?>
						<?php endif; ?>
					</div>

					<?php if ( 'yes' === ( $settings['show_description'] ) ) : ?>
						<div class="post-content">
							<div class="summary">
								<?php if ( !has_excerpt() && !empty( $thegem_post_data['title_excerpt'] ) ): ?>
									<?php echo $thegem_post_data['title_excerpt']; ?>
								<?php else: ?>
									<?php echo preg_replace('%&#x[a-fA-F0-9]+;%', '', apply_filters('the_excerpt', get_the_excerpt())); ?>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="post-misc">
						<div class="post-author">

							<?php if ( $settings['show_author'] ) : ?>
								<span class="post-meta-author"><?php printf( esc_attr( $settings['caption_author_by_text'] ) . esc_html__( " %s", "thegem" ), get_the_author_link() ); ?></span>
							<?php endif ?>

							<?php if( $thegem_categories && $settings['show_categories'] ): ?>
								<span class="post-meta-categories"><?php echo esc_attr( $settings['caption_categories_in_text'] ) . implode('<span class="sep"></span>', $thegem_categories_list); ?></span>
							<?php endif ?>

						</div>

						<div class="post-soc-info">
							<span class="post-comments">
								<?php if(comments_open() && $settings['show_comments'] ): ?>
									<span class="comments-link"><div class="elementor-icon"><?php if ($settings['comments_icon']['value']) : Icons_Manager::render_icon( $settings['comments_icon'], [ 'aria-hidden' => 'true' ] ); else : ?><i class="default"></i><?php endif; ?></div><?php comments_popup_link(0, 1, '%'); ?></span>
								<?php endif; ?>
								<?php if(comments_open() && $settings['show_comments'] && $settings['show_likes'] && function_exists('zilla_likes')): ?>
									<span class="sep"></span>
								<?php endif; ?>
							</span>
							<span class="post-likes">
								<?php if( function_exists('zilla_likes') && $settings['show_likes'] ) { echo '<span class="post-meta-likes">';if ($settings['likes_icon']['value']) :	Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);	else: ?><i class="default"></i><?php endif;zilla_likes();echo '</span>'; } ?>
							</span>
						</div>
						<div class="post-links">
							<div class="post-footer-sharing">
								<?php if ( $settings['show_social_sharing'] && ! empty( $bl_social_sharing ) && file_exists( $bl_social_sharing ) ) : include $bl_social_sharing; endif; ?>
							</div>
							<div class="post-read-more">
								<?php if ( $settings['show_readmore_button'] && ! empty( $bl_readmore_button ) && file_exists( $bl_readmore_button ) ) : include $bl_readmore_button; endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</article><!-- #post-<?php the_ID(); ?> -->