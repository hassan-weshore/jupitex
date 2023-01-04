<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Icons_Manager;

$thegem_post_data = thegem_get_sanitize_page_title_data( get_the_ID() );

$thegem_categories = get_the_category();
$thegem_categories_list = array();
foreach( $thegem_categories as $thegem_category ) {
	$thegem_categories_list[] = '<a href="'.esc_url(get_category_link( $thegem_category->term_id )).'" title="'.esc_attr( sprintf( __( "View all posts in %s", "thegem" ), $thegem_category->name ) ).'">'.$thegem_category->cat_name.'</a>';
}

$blog_style = $settings['columns'];

$thegem_classes = array();
$thegem_sources = array();
$has_content_gallery = get_post_format(get_the_ID()) == 'gallery';

if ( is_sticky() && !is_paged() ) {
	$thegem_classes = array_merge( $thegem_classes, array( 'sticky' ) );
	if ( 'justified' === ( $settings['layout'] ) ) {
		$thegem_featured_content = thegem_get_post_featured_content( get_the_ID(), 'thegem-blog-justified-sticky' );
	} elseif ( 'masonry' === ( $settings['layout'] ) ) {
		$thegem_featured_content = thegem_get_post_featured_content( get_the_ID(), 'thegem-blog-masonry-sticky' );
	}
} else {
	if ( 'justified' === ( $settings['layout'] ) ) {
		$thegem_post_gallery_size = 'thegem-blog-justified';
		if ( $has_content_gallery ) {
			if ( $blog_style == '3x' ) {
				$thegem_post_gallery_size = 'thegem-blog-justified-3x';
			} elseif ( $blog_style == '100%' ) {
				$thegem_post_gallery_size = 'thegem-blog-justified-4x';
			} elseif ( $blog_style == '4x' ) {
				$thegem_post_gallery_size = 'thegem-blog-justified-4x';
			}
		}
	} elseif ( 'masonry' === ( $settings['layout'] ) ) {
		$thegem_post_gallery_size = 'thegem-blog-masonry';
		if ( $has_content_gallery ) {
			if ( $blog_style == '100%' ) {
				$thegem_post_gallery_size = 'thegem-blog-masonry-100';
			} elseif ( $blog_style == '3x' ) {
				$thegem_post_gallery_size = 'thegem-blog-masonry-3x';
			} elseif ( $blog_style == '4x' ) {
				$thegem_post_gallery_size = 'thegem-blog-masonry-4x';
			}
		}
	}


	if ( has_post_thumbnail() && !$has_content_gallery ) {
		if ( 'justified' === ( $settings['layout'] ) ) {
			if ( $blog_style == '100%' ) {
				$thegem_sources = array(
					array('media' => '(max-width: 600px)', 'srcset' => array('1x' => 'thegem-blog-justified', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 992px)', 'srcset' => array('1x' => 'thegem-blog-justified-4x', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 1125px)', 'srcset' => array('1x' => 'thegem-blog-justified-3x-small', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-blog-justified-4x', '2x' => 'thegem-blog-justified'))
				);
			} elseif  ( $blog_style == '3x' ) {
				$thegem_sources = array(
					array('media' => '(max-width: 992px)', 'srcset' => array('1x' => 'thegem-blog-justified', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 1100px)', 'srcset' => array('1x' => 'thegem-blog-justified-3x-small', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-blog-justified-3x', '2x' => 'thegem-blog-justified'))
				);
			} elseif ( $blog_style == '4x' ) {
				$thegem_sources = array(
					array('media' => '(max-width: 600px)', 'srcset' => array('1x' => 'thegem-blog-justified', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 992px)', 'srcset' => array('1x' => 'thegem-blog-justified-4x', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 1125px)', 'srcset' => array('1x' => 'thegem-blog-justified-3x-small', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-blog-justified-4x', '2x' => 'thegem-blog-justified'))
				);
			}	
		} elseif ( 'masonry' === ( $settings['layout'] ) ) {
			if ( $blog_style == '100%' ) {
				$thegem_sources = array(
					array('media' => '(max-width: 600px)', 'srcset' => array('1x' => 'thegem-blog-masonry', '2x' => 'thegem-blog-masonry')),
					array('media' => '(max-width: 992px)', 'srcset' => array('1x' => 'thegem-blog-masonry-100-medium', '2x' => 'thegem-blog-masonry')),
					array('media' => '(max-width: 1100px)', 'srcset' => array('1x' => 'thegem-blog-masonry-100-small', '2x' => 'thegem-blog-masonry')),
					array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-blog-masonry-100', '2x' => 'thegem-blog-masonry'))
				);
			} elseif ( $blog_style == '3x' ) {
				$thegem_sources = array(
					array('media' => '(max-width: 600px)', 'srcset' => array('1x' => 'thegem-blog-masonry', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 992px)', 'srcset' => array('1x' => 'thegem-blog-masonry-100-medium', '2x' => 'thegem-blog-justified')),
					array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-blog-masonry-100', '2x' => 'thegem-blog-justified'))
				);
			} elseif ( $blog_style == '4x' ) {
				$thegem_sources = array(
					array('media' => '(max-width: 600px)', 'srcset' => array('1x' => 'thegem-blog-masonry', '2x' => 'thegem-blog-masonry')),
					array('media' => '(max-width: 992px)', 'srcset' => array('1x' => 'thegem-blog-masonry-100-medium', '2x' => 'thegem-blog-masonry')),
					array('media' => '(max-width: 1920px)', 'srcset' => array('1x' => 'thegem-blog-masonry-4x', '2x' => 'thegem-blog-masonry'))
				);
			}
		}

	}
	if ( 'justified' === ( $settings['layout'] ) ) {
		$thegem_featured_content = thegem_get_post_featured_content(get_the_ID(), $has_content_gallery ? $thegem_post_gallery_size : 'thegem-blog-justified', false, $thegem_sources);
	} elseif ( 'masonry' === ( $settings['layout'] ) ) {
		$thegem_featured_content = thegem_get_post_featured_content(get_the_ID(), $has_content_gallery ? $thegem_post_gallery_size : 'thegem-blog-masonry', false, $thegem_sources);
	}
}

if ( 'justified' === ( $settings['layout'] ) ) {
	if ( $blog_style == '3x' ){
		if ( is_sticky() && !is_paged() )
			$thegem_classes = array_merge($thegem_classes, array( 'col-lg-8', 'col-md-8', 'col-sm-6', 'col-xs-12', 'inline-column' ) );
		else
			$thegem_classes = array_merge($thegem_classes, array( 'col-lg-4', 'col-md-4', 'col-sm-6', 'col-xs-6', 'inline-column' ) );
	} elseif ($blog_style == '4x' || $blog_style == '100%') {
		if ( is_sticky() && !is_paged() )
			$thegem_classes = array_merge($thegem_classes, array( 'col-lg-6', 'col-md-6', 'col-sm-12', 'col-xs-12', 'inline-column' ) );
		else
			$thegem_classes = array_merge($thegem_classes, array( 'col-lg-3', 'col-md-3', 'col-sm-6', 'col-xs-6', 'inline-column' ) );
	}
} elseif ( 'masonry' === ( $settings['layout'] ) ) {
	if ( $blog_style == '3x' ) {
		if ( is_sticky() && !is_paged() )
			$thegem_classes = array_merge( $thegem_classes, array( 'col-lg-8', 'col-md-8', 'col-sm-6', 'col-xs-12') );
		else
			$thegem_classes = array_merge( $thegem_classes, array( 'col-lg-4', 'col-md-4', 'col-sm-6', 'col-xs-6') );
	} elseif ($blog_style == '4x' || $blog_style == '100%') {
		if ( is_sticky() && !is_paged() )
			$thegem_classes = array_merge( $thegem_classes, array( 'col-lg-6', 'col-md-6', 'col-sm-12', 'col-xs-12') );
		else
			$thegem_classes = array_merge( $thegem_classes, array( 'col-lg-3', 'col-md-3', 'col-sm-6', 'col-xs-6') );
	}
}
if ( is_sticky() && !is_paged() && empty( $thegem_featured_content ) ) {
	$thegem_classes[] = 'no-image';
}

$thegem_classes[] = 'item-animations-not-inited';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $thegem_classes ); ?>>
<?php if ( 'masonry' === ( $settings['layout'] ) ) : ?>
	<?php if ( isset($settings['show_animation']) && $settings['show_animation'] ): ?>
		<div class="item-lazy-scroll-wrap">
	<?php endif; ?>
<?php endif; ?>

	<?php if ( get_post_format() == 'quote' && $thegem_featured_content ) : ?>
		<?php echo $thegem_featured_content; ?>
	<?php else : ?>
		<div class="post-content-wrapper">
		
			<?php if( ! is_single() && is_sticky() && ! is_paged() ) {?>
				<div class="sticky-label">
					<div class="elementor-icon">
						<?php if ( $settings['sticky_label_icon']['value'] ) {
							Icons_Manager::render_icon( $settings['sticky_label_icon'], [ 'aria-hidden' => 'true' ] );
						} else { ?>
							<i class="default"></i>
						<?php } ?>
					</div>
				</div>
			<?php } elseif (is_sticky() && is_admin()) {	?>
				<div class="sticky-label">
					<div class="elementor-icon">
						<?php if ( $settings['sticky_label_icon']['value'] ) {
							Icons_Manager::render_icon( $settings['sticky_label_icon'], [ 'aria-hidden' => 'true' ] );
						} else { ?>
							<i class="default"></i>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			
			<?php if ( $thegem_featured_content ): ?>
				<div class="post-image"><?php echo $thegem_featured_content; ?></div>
			<?php endif; ?>

			<div class="caption-container">
				<div class="description">
					<div class="post-meta-conteiner">

							<span class="post-meta-author">
								<?php if ( $settings['show_author'] ) : ?>
									<?php printf( esc_attr( $settings['caption_author_by_text'] ) . esc_html__( " %s", "thegem" ), get_the_author_link() ); ?>
								<?php endif; ?>
							</span>

						<div class="post-meta-right">
							<?php if( comments_open() && $settings['show_comments'] ) : ?>
								<span class="comments-link"><div class="elementor-icon"><?php if ($settings['comments_icon']['value']) : Icons_Manager::render_icon( $settings['comments_icon'], [ 'aria-hidden' => 'true' ] ); else : ?><i class="default"></i> <?php endif; ?></div><?php comments_popup_link(0, 1, '%'); ?></span>
							<?php endif; ?>

							<?php if( comments_open() && $settings['show_comments'] && $settings['show_likes'] && function_exists('zilla_likes') ): ?>
								<span class="sep"></span>
							<?php endif; ?>
							<?php if( function_exists( 'zilla_likes' ) && $settings['show_likes'] ) { echo '<span class="post-meta-likes">';if ($settings['likes_icon']['value']) :	Icons_Manager::render_icon($settings['likes_icon'], ['aria-hidden' => 'true']);	else: ?><i class="default"></i><?php endif;zilla_likes();echo '</span>'; } ?>
						</div>
					</div>

					<div class="post-title">
						<?php if ( 'yes' === ( $settings['show_title'] ) ) : ?>
							<?php the_title('<div class="entry-title title-h4"><a href="' . esc_url(get_permalink()) . '" rel="bookmark"><span class="entry-title-date">'.($settings['show_date'] ? get_the_date('d M').': ' : '').'</span><span class="light">', '</span></a></div>'); ?>
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

					<div class="info clearfix">
							<div class="post-footer-sharing">
								<?php if ( $settings['show_social_sharing'] && ! empty( $bg_social_sharing ) && file_exists( $bg_social_sharing ) ) : include $bg_social_sharing; endif; ?>
							</div>
							<div class="post-read-more">
								<?php if ( $settings['show_readmore_button'] && ! empty( $bg_readmore_button ) && file_exists( $bg_readmore_button ) ) : include $bg_readmore_button; endif; ?>
							</div>
					</div>
				</div>
			</div>

		</div>
<?php endif; ?>

<?php if ( 'masonry' === ( $settings['layout'] ) ) : ?>
	<?php if (isset($settings['show_animation']) && $settings['show_animation']): ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
</article>
