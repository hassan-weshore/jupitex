<div id="top-area" class="top-area top-area-style-2 top-area-alignment-<?php echo esc_attr(thegem_get_option('top_area_alignment', 'left')); ?>">
	<div class="container">
		<div class="top-area-items inline-inside">
			<?php if(thegem_get_option('top_area_button_text')) : ?>
				<div class="top-area-block top-area-button"><?php thegem_button(array('href' => thegem_get_option('top_area_button_link'), 'text' => thegem_get_option('top_area_button_text'), 'size' => 'tiny', 'no-uppercase' => true), true); ?></div>
			<?php endif; ?>
			<?php if(has_nav_menu('top_area')) : ?>
				<div class="top-area-block top-area-menu">
					<nav id="top-area-menu">
						<?php wp_nav_menu(array('theme_location' => 'top_area', 'menu_id' => 'top-area-navigation', 'menu_class' => 'nav-menu styled inline-inside', 'container' => false, 'walker' => new thegem_walker_footer_nav_menu)); ?>
					</nav>
				</div>
			<?php endif; ?>
			<?php if(thegem_get_option('top_area_socials')) : ?>
				<div class="top-area-block top-area-socials<?php echo esc_attr(thegem_get_option('top_area_style') == 1 ? ' socials-colored-hover' : ''); ?>"><?php thegem_print_socials(); ?></div>
			<?php endif; ?>
			<?php if(thegem_get_option('top_area_contacts')) : ?>
				<div class="top-area-block top-area-contacts"><?php echo thegem_top_area_contacts(); ?></div>
			<?php endif; ?>
		</div>
	</div>
</div>