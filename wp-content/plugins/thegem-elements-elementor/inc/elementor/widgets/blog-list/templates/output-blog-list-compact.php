<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Icons_Manager;

$thegem_post_data = thegem_get_sanitize_page_title_data(get_the_ID());

$thegem_classes = array();

if(is_sticky() && !is_paged()) {
	$thegem_classes = array_merge($thegem_classes, array('sticky'));
	
}

if(has_post_thumbnail()) {
	$thegem_classes[] = 'no-image';
}

$thegem_classes[] = 'clearfix';

$thegem_classes[] = ( $settings['show_featured_image'] ) ? '' : 'image-disabled';
$thegem_classes[] = ( $settings['show_comments'] || $settings['show_author'] || $settings['show_likes'] ) ? 'full-meta' : 'no-meta';
$thegem_classes[] = 'item-animations-not-inited';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($thegem_classes); ?>>
	<div class="item-post-container">
		
		<?php if ( 'yes' === ( $settings['show_featured_image'] ) ) : ?>
			<div class="gem-compact-item-left">
				<div class="gem-compact-item-image post-img">
					<a class="default" href="<?php echo esc_url(get_permalink()); ?>"><?php thegem_post_thumbnail('thegem-blog-compact', true, 'img-responsive'); ?></a>
				</div>
			</div>
		<?php endif; ?>

		<div class="gem-compact-item-right caption-container">
			<div class="gem-compact-item-content">

				<div class="post-title">
					<?php if ( 'yes' === ( $settings['show_title'] ) ) : ?>
						<?php the_title('<h5 class="entry-title reverse-link-color"><a href="' . esc_url(get_permalink()) . '" rel="bookmark"><span class="entry-title-date">'.( $settings['show_date'] ? get_the_date('d M').': ' : '').'</span><span class="light">', '</span></a></h5>'); ?>
					<?php endif; ?>
				</div>

				<?php if ( 'yes' === ( $settings['show_description'] ) ) : ?>
					<div class="post-text">
						<div class="summary">
							<?php if ( !has_excerpt() && !empty( $thegem_post_data['title_excerpt'] ) ): ?>
								<?php echo $thegem_post_data['title_excerpt']; ?>
							<?php else: ?>
								<?php echo preg_replace('%&#x[a-fA-F0-9]+;%', '', apply_filters('the_excerpt', get_the_excerpt())); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

			</div>

			<div class="post-meta">
				<div class="entry-meta clearfix gem-post-date">
					<div class="post-meta-right">
						<?php if(comments_open() && $settings['show_comments'] ): ?>
							<span class="comments-link"><div class="elementor-icon"><?php if ($settings['comments_icon']['value']) : Icons_Manager::render_icon( $settings['comments_icon'], [ 'aria-hidden' => 'true' ] ); else : ?><i class="default"></i><?php endif; ?></div><?php comments_popup_link(0, 1, '%'); ?></span>
						<?php endif; ?>
						<?php if(comments_open() && $settings['show_comments'] && function_exists('zilla_likes') && $settings['show_likes'] ): ?><span class="sep"></span><?php endif; ?>
						<?php if( function_exists('zilla_likes') && $settings['show_likes'] ) { echo '<span class="post-meta-likes">';if ($settings['likes_icon']['value']) :	Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);	else: ?><i class="default"></i><?php endif;zilla_likes();echo '</span>'; } ?>
					</div>
					<div class="post-meta-left">
						<?php if( $settings['show_author'] ) : ?><span class="post-meta-author"><?php printf( esc_attr( $settings['caption_author_by_text'] ) . esc_html__( " %s", "thegem" ), get_the_author_link() ); ?></span><?php endif; ?>
					</div>
				</div><!-- .entry-meta -->
			</div>
		</div>
	</div>	
</article><!-- #post-<?php the_ID(); ?> -->
