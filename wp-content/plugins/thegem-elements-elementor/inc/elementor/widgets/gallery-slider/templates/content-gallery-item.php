<?php
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Icons_Manager;
?>

<div class="gem-gallery-item">
	<div class="gem-gallery-item-image">
		<a href="<?php echo($item['slide_image']['id'] ? $preview_image_url[0] : Utils::get_placeholder_image_src()); ?>"
		   data-elementor-open-lightbox="no"
		   data-fancybox="gallery-<?php echo esc_attr($gallery_uid); ?>">
			<svg width="20" height="10">
				<path d="M 0,10 Q 9,9 10,0 Q 11,9 20,10"/>
			</svg>
			<img src="<?php echo($item['slide_image']['id'] ? $thumb_image_url[0] : Utils::get_placeholder_image_src()); ?>"
				 alt="<?php echo Control_Media::get_image_alt($item['slide_image']); ?>" class="img-responsive">
			<span class="gem-gallery-caption slide-info">
				<span class="gem-gallery-item-title ">
					<?php if ($item['slide_image']['id']) {
						echo($item['slide_title'] ? $item['slide_title'] : apply_filters('the_excerpt', get_post($item['slide_image']['id'])->post_excerpt));
					} else {
						echo($item['slide_title'] ? $item['slide_title'] : '');
					} ?>
				</span>
				<span class="gem-gallery-item-description">
					<?php if ($item['slide_image']['id']) {
						echo($item['slide_subtitle'] ? $item['slide_subtitle'] : get_post($item['slide_image']['id'])->post_content);
					} else {
						echo($item['slide_subtitle'] ? $item['slide_subtitle'] : '');
					} ?>
				</span>
			</span>
			<?php if (!empty($icon['value'])) {
				Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']);
			} ?>
			<?php if ((!empty($icon['value']) && 'horizontal-sliding' === $settings['hover_effect']) || (!empty($icon['value']) && 'vertical-sliding' === $settings['hover_effect'])) : ?>
				<span class="gem-gallery-line"></span>
			<?php endif; ?>
		</a>
	</div>
</div>
