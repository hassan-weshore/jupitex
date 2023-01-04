<div class="<?php echo ( $settings['pagination_more_button_separator_style_active'] === 'gem-button-separator-type-square' ) ? 'gem-button-separator-line' : 'gem-button-separator-holder'; ?>">
	<?php if( ( $settings['pagination_more_button_separator_style_active'] === 'gem-button-separator-type-square') ) :?>
        <svg width="100%" height="8px">
            <line x1="4" x2="100%" y1="4" y2="4" stroke-width="8" stroke-linecap="square" stroke-dasharray="0, 15"></line>
        </svg>
	<?php else : ?>
        <div class="gem-button-separator-line"></div>
	<?php endif; ?>
</div>