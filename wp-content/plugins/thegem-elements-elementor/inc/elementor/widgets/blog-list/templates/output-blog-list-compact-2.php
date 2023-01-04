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
$thegem_classes[] = 'item-animations-not-inited';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($thegem_classes); ?>>
	<div class="item-post-container">
		<?php if ( 'yes' === ( $settings['show_featured_image'] ) ) : ?>
			<div class="gem-news-item-left">
				<div class="gem-news-item-image post-img">
					<a class="default" href="<?php echo esc_url(get_permalink()); ?>"><?php thegem_post_thumbnail('thegem-news-carousel'); ?></a>
				</div>
			</div>
		<?php endif; ?>	


		<div class="gem-news-item-right caption-container <?php echo ( $settings['show_featured_image'] ) ? '' : 'image-disabled'; ?>">
			<div class="gem-news-item-right-conteiner">

				<?php if ( 'yes' === ( $settings['show_title'] ) ) : ?>
					<?php the_title('<div class="gem-news-item-title post-title entry-title"><span class="light"><a href="'.esc_url(get_permalink()).'">', '</span></a></div>'); ?>
				<?php endif; ?>

				<?php if ( 'yes' === ( $settings['show_description'] ) ) : ?>
					<?php if(has_excerpt()) : ?>
						<div class='gem-news_title-excerpt summary'>
							<?php the_excerpt(); ?>
						</div>
					<?php else : ?>
						<div class='gem-news_title_excerpt'>
							<?php
								$thegem_item_title_data = thegem_get_sanitize_page_title_data(get_the_ID());

								echo $thegem_item_title_data['title_excerpt'];
							?>
						</div>
					<?php endif; ?>
				<?php endif; ?>	

			</div>

			<div  class="gem-news-item-meta">
			
				<?php if ( $settings['show_date'] ) : ?>
					<div class="gem-news-item-date small-body"><?php echo get_the_date(); ?></div>
				<?php endif; ?>

				<div class="gem-news-zilla-likes">
					<?php if( function_exists( 'zilla_likes' ) && $settings['show_likes'] ) { echo '<span class="post-meta-likes">';if ($settings['likes_icon']['value']) :	Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);	else: ?><i class="default"></i><?php endif;zilla_likes();echo '</span>'; } ?>
					<?php if(comments_open() && $settings['show_comments'] ): ?>
						<span class="comments-link"><div class="elementor-icon"><?php if ($settings['comments_icon']['value']) : Icons_Manager::render_icon( $settings['comments_icon'], [ 'aria-hidden' => 'true' ] ); else : ?><i class="default"></i> <?php endif; ?></div><?php comments_popup_link(0, 1, '%'); ?></span>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->