<div class="gem-button-separator-holder">
	<div class="gem-button-separator-line"<?php echo (!$separator_style_square ? ' style="border-color:'.esc_attr($sep_color).'"' : ''); ?>>
		<?php if( ( $separator_style_square ) ) :?>
			<svg width="100%" height="8px">
				<line x1="4" x2="100%" y1="4" y2="4" stroke-width="8" stroke="<?php echo esc_attr($sep_color); ?>" stroke-linecap="square" stroke-dasharray="0, 15"></line>
			</svg>
		<?php endif; ?>
	</div>
</div>