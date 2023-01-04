<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Elementor\Icons_Manager;

global $post;
$post_image = '';
$attachment_id = get_post_thumbnail_id($post->ID);
	if ($attachment_id) {
	$post_image = thegem_generate_thumbnail_src($attachment_id, 'thegem-blog-timeline-large');
		if ($post_image && $post_image[0]) {
			$post_image = $post_image[0];
		}
}

?>

<div class="gem-button-container gem-widget-button gem-button-position-inline">
	<a class="gem-button gem-button-size-tiny gem-button-style-flat gem-button-text-weight-normal gem-button-empty" href="#" target="_self">
		<div class="elementor-icon">
			<?php if ($settings['sharing_icon']['value']) {
				Icons_Manager::render_icon( $settings['sharing_icon'], [ 'aria-hidden' => 'true' ] );
			} else { ?>
				<i class="default"></i>
			<?php } ?>
		</div>
	</a>
</div>

<div class="sharing-popup">
	<div class="socials-sharing socials socials-colored-hover">

		<?php if ( 'yes' === ( $settings['show_social_facebook'] ) ) : ?>
			<a class="socials-item" target="_blank" href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u='.urlencode(get_permalink())); ?>" title="Facebook"><i class="socials-item-icon facebook"></i></a>
		<?php endif; ?>

		<?php if ( 'yes' === ( $settings['show_social_twitter'] ) ) : ?>
			<a class="socials-item" target="_blank" href="<?php echo esc_url('https://twitter.com/intent/tweet?text='.urlencode(get_the_title()).'&amp;url='.urlencode(get_permalink())); ?>" title="Twitter"><i class="socials-item-icon twitter"></i></a>
		<?php endif; ?>

		<?php if ( 'yes' === ( $settings['show_social_pinterest'] ) ) : ?>
			<a class="socials-item" target="_blank" href="<?php echo esc_url('https://pinterest.com/pin/create/button/?url=' . urlencode(get_permalink()) . '&amp;description=' . urlencode(get_the_title()) . ($post_image ? '&amp;media=' . urlencode($post_image) : '' )); ?>" title="Pinterest"><i class="socials-item-icon pinterest"></i></a>
		<?php endif; ?>

		<?php if ( 'yes' === ( $settings['show_social_tumblr'] ) ) : ?>
			<a class="socials-item" target="_blank" href="<?php echo esc_url('http://tumblr.com/widgets/share/tool?canonicalUrl='.urlencode(get_permalink())); ?>" title="Tumblr"><i class="socials-item-icon tumblr"></i></a>
		<?php endif; ?>

		<?php if ( 'yes' === ( $settings['show_social_linkedin'] ) ) : ?>
			<a class="socials-item" target="_blank" href="<?php echo esc_url('https://www.linkedin.com/shareArticle?mini=true&url='.urlencode(get_permalink()).'&amp;title='.urlencode(get_the_title())); ?>&amp;summary=<?php echo urlencode(get_the_excerpt()); ?>" title="LinkedIn"><i class="socials-item-icon linkedin"></i></a>
		<?php endif; ?>

		<?php if ( 'yes' === ( $settings['show_social_reddit'] ) ) : ?>
			<a class="socials-item" target="_blank" href="<?php echo esc_url('https://www.reddit.com/submit?url='.urlencode(get_permalink()).'&amp;title='.urlencode(get_the_title())); ?>" title="Reddit"><i class="socials-item-icon reddit"></i></a>
		<?php endif; ?>

	</div>
<svg class="sharing-styled-arrow"><use xlink:href="<?php echo esc_url(get_template_directory_uri() . '/css/post-arrow.svg'); ?>#dec-post-arrow"></use></svg>
</div>
