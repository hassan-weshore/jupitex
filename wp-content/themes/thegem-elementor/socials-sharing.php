<?php
	$post_image = '';
	$attachment_id = get_post_thumbnail_id($post->ID);
	if ($attachment_id) {
		$post_image = thegem_generate_thumbnail_src($attachment_id, 'thegem-blog-timeline-large');
		if ($post_image && $post_image[0]) {
			$post_image = $post_image[0];
		}
	}

    global $thegem_product_data;
    $isProduct = get_post_type($post->ID) == 'product';
    if ($isProduct) {
	    $isLegacy = $thegem_product_data['product_page_layout'] === 'legacy';
	    $isShare = $thegem_product_data['product_page_elements_share'];
	    $shareTitle = $thegem_product_data['product_page_elements_share_title'];
    }
?>

<div class="socials-sharing socials socials-colored-hover">
    <?php if ($isProduct && !$isLegacy && $shareTitle): ?>
        <span class="socials-item-title"><?=esc_html_e( $shareTitle.':' )?></span>
    <?php endif; ?>
	<a class="socials-item" target="_blank" href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u='.urlencode(get_permalink())); ?>" title="Facebook"><i class="socials-item-icon facebook"></i></a>
	<a class="socials-item" target="_blank" href="<?php echo esc_url('https://twitter.com/intent/tweet?text='.urlencode(get_the_title()).'&amp;url='.urlencode(get_permalink())); ?>" title="Twitter"><i class="socials-item-icon twitter"></i></a>
	<a class="socials-item" target="_blank" href="<?php echo esc_url('https://pinterest.com/pin/create/button/?url=' . urlencode(get_permalink()) . '&amp;description=' . urlencode(get_the_title()) . ($post_image ? '&amp;media=' . urlencode($post_image) : '' )); ?>" title="Pinterest"><i class="socials-item-icon pinterest"></i></a>
	<a class="socials-item" target="_blank" href="<?php echo esc_url('http://tumblr.com/widgets/share/tool?canonicalUrl='.urlencode(get_permalink())); ?>" title="Tumblr"><i class="socials-item-icon tumblr"></i></a>
	<a class="socials-item" target="_blank" href="<?php echo esc_url('https://www.linkedin.com/shareArticle?mini=true&url='.urlencode(get_permalink()).'&amp;title='.urlencode(get_the_title())); ?>&amp;summary=<?php echo urlencode(get_the_excerpt()); ?>" title="LinkedIn"><i class="socials-item-icon linkedin"></i></a>
	<a class="socials-item" target="_blank" href="<?php echo esc_url('https://www.reddit.com/submit?url='.urlencode(get_permalink()).'&amp;title='.urlencode(get_the_title())); ?>" title="Reddit"><i class="socials-item-icon reddit"></i></a>
</div>
