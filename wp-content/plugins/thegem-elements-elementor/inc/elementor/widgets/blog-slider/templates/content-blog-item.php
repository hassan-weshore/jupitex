<?php
use Elementor\Icons_Manager;
?>

<article id="post-<?php the_ID(); ?>" class="clearfix">

	<a href="<?php echo esc_url(get_permalink()); ?>" class="gem-slider-blog-link"></a>

	<div class="gem-slider-item-image" style="background: url('<?php echo esc_url(the_post_thumbnail_url( 'full' )); ?>') no-repeat 50% 50% / cover;">
		<img src="<?php echo esc_url(the_post_thumbnail_url( 'full' )); ?>"  style="visibility: hidden;">
	</div>

	<div class="gem-slider-item-overlay">
		<div class="gem-compact-item-content">
			<?php if ( 'yes' === ( $settings['show_title'] ) ) : ?>
				<div class="post-title">
					<?php the_title('<h5 class="entry-title reverse-link-color"><span class="entry-title-date">'.( $settings['show_date'] ? get_the_date('d M').': ' : '').'</span><span class="light">', '</span></h5>'); ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === ( $settings['show_description'] ) ) : ?>
				<div class="post-text date-color">
					<div class="summary">
						<?php if ( !has_excerpt() && !empty( $thegem_post_data['title_excerpt'] ) ): ?>
							<?php echo wpautop($thegem_post_data['title_excerpt']); ?>
						<?php else: ?>
							<?php echo preg_replace('%&#x[a-fA-F0-9]+;%', '', apply_filters('the_excerpt', get_the_excerpt())); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>
		<div class="post-meta date-color">
			<div class="entry-meta clearfix gem-post-date">
				<div class="post-meta-right">
					<?php if ( comments_open() && $settings['show_comments'] ): ?>
						<span class="comments-link">
										<?php Icons_Manager::render_icon( $settings['comments_icon'], [ 'aria-hidden' => 'true' ] ); ?>
										<?php comments_popup_link( 0, 1, '%' ); ?>
									</span>
					<?php endif; ?>
					<?php if ( comments_open() && $settings['show_comments'] && function_exists('zilla_likes') && $settings['show_likes'] ): ?>
						<span class="sep"></span>
					<?php endif; ?>

					<?php if ($settings['show_likes'] === 'yes' && function_exists('zilla_likes')) {
						echo '<span class="post-meta-likes">';
						zilla_likes();
						if ($settings['likes_icon']['value']) {
							Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);
						}
						echo '</span>';
					} ?>

				</div>
				<div class="post-meta-left">
					<?php if ( $settings['show_author'] ) : ?>
						<span class="post-meta-author">
										<?php printf( esc_attr( $settings['caption_author_by_text'] ) . esc_html__( " %s", "thegem" ), get_the_author_link() ); ?>
									</span>
					<?php endif; ?>
				</div>
			</div><!-- .entry-meta -->
		</div>

		<?php if ( 'yes' === ( $settings['arrows_show'] ) ) : ?>
			<div class="gem-blog-slider-navigation">
				<a href="#" class="gem-blog-slider-prev gem-button gem-button-size-tiny">
					<?php Icons_Manager::render_icon( $settings['left_icon'], [ 'aria-hidden' => 'true', 'class' => 'gem-print-icon gem-icon-prev' ] ); ?>
				</a>
				<a href="#" class="gem-blog-slider-next gem-button gem-button-size-tiny">
					<?php Icons_Manager::render_icon( $settings['right_icon'], [ 'aria-hidden' => 'true', 'class' => 'gem-print-icon gem-icon-prev' ] ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
