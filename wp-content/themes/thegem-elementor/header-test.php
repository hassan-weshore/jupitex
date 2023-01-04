<?php
	$thegem_preview_options = isset($_REQUEST['options']) ? $_REQUEST['options'] : array();
	$thegem_page_id = 0;
	$thegem_header_light = '';
	if(($thegem_preview_options['logo_position'] == 'menu_center' || $thegem_preview_options['logo_position'] == 'center') && in_array($thegem_preview_options['header_layout'], array('fullwidth_hamburger', 'overlay', 'perspective'))) {
		$thegem_preview_options['logo_position'] = 'left';
	}
	if($thegem_preview_options['header_layout'] == 'vertical') {
		$thegem_preview_options['logo_position'] = 'left';
	}
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
	<?php wp_head(); ?>
	<style type="text/css">
		html, body {
			height: 100%;
			width: 100%;
		}
		#page {
			min-height: 100%;
		}
		@media (min-width: 860px) {
			.container { width: 860px; }
		}
		@-moz-document url-prefix() { 
			#primary-menu.no-responsive ul > li + li {
				border-top-width: 0px;
			}
		}
		#page:not(.vertical-header) #site-header {
			position: static;
		}
		@media (min-width: 1212px) {
			#page.vertical-header #site-header-wrapper {
				position: absolute;
				margin-left: 0 !important;
				width: 300px;
			}
			#page.vertical-header {
				padding-left: 300px;
			}
			#page.vertical-header .vertical-toggle {
				display: none;
			}
		}
		#thegem-perspective .perspective-navigation:not(.responsive) {
			position: absolute;
		}


/*		.thegem-perspective-menu-wrapper {
			transform: none !important;
		}

		.thegem-perspective-menu-wrapper #primary-navigation {
			transform: none !important;
		}*/



		/*.header-layout-fullwidth_hamburger #primary-menu {
			position: absolute !important;
			margin-top: -56px !important;
			margin-right: -40px !important;
			height: 400px;
		}*/

		.block-content {
			padding-top: 50px;
		}

		@media (min-width: 860px) {
			#site-header .header-main {
				position: static;
			}
		}
		#site-header .header-layout-overlay .site-title {
			position: static;
		}
		#page.vertical-header #site-header-wrapper {
			-webkit-transition: all 0s;
			transition: all 0s;
		}

		<?php if(!thegem_get_preview_option($thegem_preview_options, 'top_area_disable_mobile')) : ?>
			@media (max-width: 767px) {
				#top-area {
					display: block;
				}
				.top-area-socials,
				.top-area-menu {
					display: none;
				}
			}
		<?php endif; ?>

		@media (min-width: 767px) {
			#site-header .site-title a img.default, #site-header .menu-item-logo a img.default {
				display: inline;
			}
		}

		@media (max-width: 767px) {
			#site-header .site-title a img.default,
			#site-header .menu-item-logo a img.default {
				display: inline;
				width: 132px !important;
			}
		}

		<?php if(thegem_get_preview_option($thegem_preview_options, 'top_area_show')) : ?>
			#top-area {
				display: block;
			}
		<?php else : ?>
			#top-area {
				display: none;
			}
		<?php endif; ?>

		<?php if(thegem_get_preview_option($thegem_preview_options, 'top_area_disable_tablet')) : ?>
			@media (max-width: 991px) {
				#top-area {
					display: none;
				}
			}
		<?php else : ?>
			@media (max-width: 991px) {
				#top-area {
					display: block;
				}
			}
		<?php endif; ?>

		<?php if(thegem_get_preview_option($thegem_preview_options, 'top_area_disable_mobile')) : ?>
			@media (max-width: 767px) {
				#top-area {
					display: none;
				}
			}
		<?php else : ?>
			@media (max-width: 767px) {
				#top-area {
					display: block;
				}
			}
		<?php endif; ?>

<?php if(thegem_get_preview_option($thegem_preview_options, 'header_layout') !== 'overlay') : ?>
<?php if(thegem_get_preview_option($thegem_preview_options, 'menu_appearance_tablet_portrait') == 'responsive') : ?>
@media (min-width: 768px) and (max-width: 979px) {
	#site-header .header-main {
		position: relative;
		display: table;
		width: 100%;
		z-index: 11;
	}
	#primary-navigation .menu-toggle,
	#perspective-menu-buttons .menu-toggle {
		display: inline-block;
	}
	.mobile-cart-position-top #site-header .mobile-cart {
		display: table-cell;
	}
	.mobile-cart-position-top #site-header .site-title {
		text-align: center;
		width: 99%;
	}
	.mobile-cart-position-top #site-header .site-title .site-logo {
		margin: 0 auto;
	}
	.mobile-cart-position-top #site-header .primary-navigation,
	.mobile-cart-position-top #site-header #perspective-menu-buttons {
		width: auto;
	}
	#perspective-menu-buttons .perspective-toggle,
	#perspective-menu-buttons .hamburger-minicart {
		display: none;
	}
	#primary-navigation .hamburger-toggle,
	#primary-navigation .overlay-toggle {
		display: none;
	}
	.primary-navigation .dl-menu {
		position: absolute;
		left: 0;
		right: 0;
		opacity: 0;
	}
	.mobile-menu-layout-overlay #primary-navigation .overlay-menu-wrapper {
		visibility: hidden;
		height: 0;
	}
	#page.vertical-header #site-header-wrapper{
		position: static;
		width: 100%;
		padding: 0;
	}
	#page.vertical-header{
		padding-left: 0;
	}
	#page.vertical-header #site-header .site-title {
		display: table-cell;
	}
	#page.vertical-header #site-header .primary-navigation,
	#page.vertical-header #site-header #perspective-menu-buttons {
		display: table-cell;
	}
	.vertical-menu-item-widgets{display: none;}
	#page #site-header .site-title {
		display: table-cell;
		padding-top: 15px;
		padding-bottom: 15px;
	}
	#page #site-header .primary-navigation,
	#page #site-header #perspective-menu-buttons {
		display: table-cell;
		text-align: right;
	}
	#page #site-header .logo-position-right .primary-navigation,
	#page #site-header .logo-position-right #perspective-menu-buttons {
		text-align: left;
	}
	#page.vertical-header .vertical-toggle {
		display: none;
	}
	#page.vertical-header {
		padding-left: 0;
	}
	#page.vertical-header #site-header-wrapper {
		margin-left: 0;
	}
	#page.vertical-header #site-header-wrapper .header-main {
		display: table;
		visibility: visible;
	}
}
<?php elseif(thegem_get_preview_option($thegem_preview_options, 'menu_appearance_tablet_portrait') == 'centered') : ?>
@media (min-width: 768px) and (max-width: 979px) {
	#site-header .header-main:not(.header-layout-fullwidth_hamburger) .site-title,
	#site-header .header-main:not(.header-layout-fullwidth_hamburger) .primary-navigation,
	#site-header .header-main:not(.header-layout-fullwidth_hamburger) #perspective-menu-buttons {
		display: block;
		text-align: center;
	}
	#site-header .header-main:not(.header-layout-fullwidth_hamburger):not(.logo-position-menu_center) .site-title {
		padding-top: 30px;
		padding-bottom: 0;
	}
	#site-header .header-main:not(.header-layout-fullwidth_hamburger).logo-position-right .site-title {
		padding-top: 0;
		padding-bottom: 30px;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if(thegem_get_preview_option($thegem_preview_options, 'header_layout') !== 'overlay') : ?>
<?php if(thegem_get_preview_option($thegem_preview_options, 'menu_appearance_tablet_landscape') == 'responsive') : ?>
@media (min-width: 980px) and (max-width: 1212px) {
	#site-header .header-main {
		position: relative;
		display: table;
		width: 100%;
		z-index: 11;
	}
	#primary-navigation .menu-toggle,
	#perspective-menu-buttons .menu-toggle {
		display: inline-block;
	}
	.mobile-cart-position-top #site-header .mobile-cart {
		display: table-cell;
	}
	.mobile-cart-position-top #site-header .site-title {
		text-align: center;
		width: 99%;
	}
	.mobile-cart-position-top #site-header .site-title .site-logo {
		margin: 0 auto;
	}
	.mobile-cart-position-top #site-header .primary-navigation,
	.mobile-cart-position-top #site-header #perspective-menu-buttons {
		width: auto;
	}
	#perspective-menu-buttons .perspective-toggle,
	#perspective-menu-buttons .hamburger-minicart {
		display: none;
	}
	#primary-navigation .hamburger-toggle,
	#primary-navigation .overlay-toggle {
		display: none;
	}
	.primary-navigation .dl-menu {
		position: absolute;
		left: 0;
		right: 0;
		opacity: 0;
	}
	.mobile-menu-layout-overlay #primary-navigation .overlay-menu-wrapper {
		visibility: hidden;
		height: 0;
	}
	#page.vertical-header #site-header-wrapper{
		position: static;
		width: 100%;
		padding: 0;
	}
	#page.vertical-header{
		padding-left: 0;
	}
	#page.vertical-header #site-header .site-title {
		display: table-cell;
	}
	#page.vertical-header #site-header .primary-navigation,
	#page.vertical-header #site-header #perspective-menu-buttons {
		display: table-cell;
	}
	.vertical-menu-item-widgets{display: none;}
	#page #site-header .site-title {
		display: table-cell;
		padding-top: 15px;
		padding-bottom: 15px;
	}
	#page #site-header .primary-navigation,
	#page #site-header #perspective-menu-buttons {
		display: table-cell;
		text-align: right;
	}
	#page #site-header .logo-position-right .primary-navigation,
	#page #site-header .logo-position-right #perspective-menu-buttons {
		text-align: left;
	}
	#page.vertical-header .vertical-toggle {
		display: none;
	}
	#page.vertical-header {
		padding-left: 0;
	}
	#page.vertical-header #site-header-wrapper {
		margin-left: 0;
	}
	#page.vertical-header #site-header-wrapper .header-main {
		display: table;
		visibility: visible;
	}
}
<?php elseif(thegem_get_preview_option($thegem_preview_options, 'menu_appearance_tablet_landscape') == 'centered') : ?>
@media (min-width: 980px) and (max-width: 1212px) {
	#site-header .header-main:not(.header-layout-fullwidth_hamburger):not(.logo-position-menu_center) .site-title,
	#site-header .header-main:not(.header-layout-fullwidth_hamburger) .primary-navigation,
	#site-header .header-main:not(.header-layout-fullwidth_hamburger) #perspective-menu-buttons {
		display: block;
		text-align: center;
	}
	#site-header .header-main:not(.header-layout-fullwidth_hamburger):not(.logo-position-menu_center) .site-title {
		padding-top: 30px;
		padding-bottom: 0;
	}
	#site-header .header-main:not(.header-layout-fullwidth_hamburger).logo-position-right .site-title {
		padding-top: 0;
		padding-bottom: 30px;
	}
}
<?php endif; ?>
<?php endif; ?>

	</style>
</head>

<?php
	if(isset($thegem_preview_options['top_area_style']) && in_array($thegem_preview_options['top_area_style'], array(1,2,3))) {
		wp_enqueue_style('thegem-preview-custom-top-area-'.$thegem_preview_options['top_area_style']);
	}
	if(isset($thegem_preview_options['header_style']) && in_array($thegem_preview_options['header_style'], array(1,2,3,4))) {
		wp_enqueue_style('thegem-preview-custom-menu-'.$thegem_preview_options['header_style']);
	}
	if(isset($thegem_preview_options['header_layout']) && in_array($thegem_preview_options['header_layout'], array('overlay'))){
		wp_enqueue_style('thegem-preview-custom-menu-overlay');
	}
	if(isset($thegem_preview_options['header_layout']) && in_array($thegem_preview_options['header_layout'], array('perspective'))){
		wp_enqueue_style('thegem-layout-perspective');
	}
	if(isset($thegem_preview_options['mobile_menu_layout']) && in_array($thegem_preview_options['mobile_menu_layout'], array('default'))){
		if(isset($thegem_preview_options['mobile_menu_layout_style']) && $thegem_preview_options['mobile_menu_layout_style'] == 'dark') {
			wp_enqueue_style('thegem-preview-mobile-default-dark');
		}
	}
	if(isset($thegem_preview_options['mobile_menu_layout']) && in_array($thegem_preview_options['mobile_menu_layout'], array('slide-horizontal', 'slide-vertical'))){
		if(isset($thegem_preview_options['mobile_menu_layout_style']) && $thegem_preview_options['mobile_menu_layout_style'] == 'dark') {
			wp_enqueue_style('thegem-preview-mobile-slide-dark');
		} else {
			wp_enqueue_style('thegem-preview-mobile-slide-light');
		}
	}
	if(isset($thegem_preview_options['mobile_menu_layout']) && in_array($thegem_preview_options['mobile_menu_layout'], array('overlay'))){
		if(isset($thegem_preview_options['mobile_menu_layout_style']) && $thegem_preview_options['mobile_menu_layout_style'] == 'dark') {
			wp_enqueue_style('thegem-preview-mobile-overlay-dark');
		} else {
			wp_enqueue_style('thegem-preview-mobile-overlay-light');
		}
	}
?>
<body <?php body_class(); ?>>
<script type="text/javascript">
(function($) {
	$(window).on('load', function (){
		if($('.menu-toggle').is(':visible')) {
			$('.menu-toggle').trigger('click');
		}
	});
	$(document).on('click', 'a', function(e) {
		e.preventDefault();
	})
})(jQuery);
</script>
<?php if(thegem_get_preview_option($thegem_preview_options, 'header_layout') == 'perspective') : ?>
	<div id="thegem-perspective" class="thegem-perspective effect-moveleft">
		<div class="thegem-perspective-menu-wrapper mobile-menu-layout-<?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'mobile_menu_layout', 'default')); ?>">
			<nav id="primary-navigation" class="site-navigation primary-navigation perspective-navigation vertical right" role="navigation">
				<?php thegem_before_perspective_nav_menu_preview($thegem_preview_options); ?>
				<?php thegem_preview_menu_html(); ?>
				<?php thegem_after_perspective_nav_menu_preview($thegem_preview_options); ?>
			</nav>
		</div>
<?php endif; ?>

<div id="page" class="layout-fullwidth<?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'header_layout') == 'vertical' ? ' vertical-header' : '') ; ?> header-style-<?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'header_layout') == 'vertical' || thegem_get_preview_option($thegem_preview_options, 'header_layout') == 'fullwidth_hamburger' ? 'vertical' : thegem_get_preview_option($thegem_preview_options, 'header_style')); ?>">

	<?php if(thegem_get_preview_option($thegem_preview_options, 'top_area_show') || !thegem_get_preview_option($thegem_preview_options, 'top_area_disable_tablet') || !thegem_get_preview_option($thegem_preview_options, 'top_area_disable_mobile')) : ?>
		<div id="top-area" class="top-area top-area-style-default top-area-alignment-<?php echo esc_attr(thegem_get_option('top_area_alignment', 'left')); ?>">
			<div class="container<?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'top_area_width') == 'full' ? ' container-fullwidth' : ''); ?>">
				<div class="top-area-items inline-inside">
						<div class="top-area-block top-area-contacts">
							<div class="gem-contacts inline-inside">
								<div class="gem-contacts-item gem-contacts-address">19th Ave New York, NY 95822, USA</div>
							</div>
					</div>
					<div class="top-area-block top-area-socials socials-colored-hover">
						<div class="socials inline-inside">
							<a class="socials-item" href="#" target="_blank" title="Facebook"><i class="socials-item-icon facebook "></i></a>
							<a class="socials-item" href="#" target="_blank" title="LinkedIn"><i class="socials-item-icon linkedin "></i></a>
							<a class="socials-item" href="#" target="_blank" title="Twitter"><i class="socials-item-icon twitter "></i></a>
							<a class="socials-item" href="#" target="_blank" title="Instagram"><i class="socials-item-icon instagram "></i></a>
							<a class="socials-item" href="#" target="_blank" title="Pinterest"><i class="socials-item-icon pinterest "></i></a>
							<a class="socials-item" href="#" target="_blank" title="YouTube"><i class="socials-item-icon youtube "></i></a>
						</div>
					</div>
					<div class="top-area-block top-area-menu">
						<nav id="top-area-menu">
							<ul id="top-area-navigation" class="nav-menu styled inline-inside">
								<li class="menu-item"><a href="#">Contact Us</a></li>
								<li class="menu-item"><a href="#">Sing In</a></li>
								<li class="menu-item"><a href="#">More Menu</a></li>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div id="site-header-wrapper">

		<?php if(thegem_get_preview_option($thegem_preview_options, 'header_layout') == 'fullwidth_hamburger') : ?><div class="hamburger-overlay"></div><?php endif; ?>

		<?php thegem_before_header_preview($thegem_preview_options); ?>

		<header id="site-header" class="site-header mobile-menu-layout-<?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'mobile_menu_layout', 'default')); ?>" role="banner">
			<?php if(thegem_get_preview_option($thegem_preview_options, 'header_layout') == 'vertical') : ?><button class="vertical-toggle"><?php esc_html_e('Primary Menu', 'thegem'); ?><span class="menu-line-1"></span><span class="menu-line-2"></span><span class="menu-line-3"></span></button><?php endif; ?>

			<div class="container<?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'header_width') == 'full' || thegem_get_preview_option($thegem_preview_options, 'header_layout') == 'fullwidth_hamburger' ? ' container-fullwidth' : ''); ?>">
				<div class="header-main logo-position-<?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'logo_position', 'left')); ?> header-layout-<?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'header_layout')); ?><?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'header_width') == 'full' ? ' header-layout-fullwidth' : ''); ?> header-style-<?php echo esc_attr(thegem_get_preview_option($thegem_preview_options, 'header_layout') == 'vertical' || thegem_get_preview_option($thegem_preview_options, 'header_layout') == 'fullwidth_hamburger' ? 'vertical' : thegem_get_preview_option($thegem_preview_options, 'header_style')); ?>">
					<?php if(thegem_get_preview_option($thegem_preview_options, 'logo_position', 'left') != 'right') : ?>
						<div class="site-title">
							<?php thegem_preview_logo_html(thegem_get_preview_option($thegem_preview_options, 'header_style')); ?>
						</div>
						<?php if(thegem_get_preview_option($thegem_preview_options, 'header_layout') != 'perspective') : ?>
							<nav id="primary-navigation" class="site-navigation primary-navigation" role="navigation">
								<?php thegem_before_nav_menu_preview($thegem_preview_options); ?>
								<?php thegem_preview_menu_html($thegem_preview_options); ?>
								<?php thegem_after_nav_menu_preview($thegem_preview_options); ?>
							</nav>
						<?php else: ?>
							<?php thegem_perspective_menu_buttons_preview($thegem_preview_options); ?>
						<?php endif; ?>
					<?php else : ?>
						<?php if(thegem_get_preview_option($thegem_preview_options, 'header_layout') != 'perspective') : ?>
							<nav id="primary-navigation" class="site-navigation primary-navigation" role="navigation">
								<?php thegem_before_nav_menu_preview($thegem_preview_options); ?>
								<?php thegem_preview_menu_html($thegem_preview_options); ?>
								<?php thegem_after_nav_menu_preview($thegem_preview_options); ?>
							</nav>
						<?php else: ?>
							<?php thegem_perspective_menu_buttons_preview($thegem_preview_options); ?>
						<?php endif; ?>
						<div class="site-title">
							<?php thegem_preview_logo_html(thegem_get_preview_option($thegem_preview_options, 'header_style')); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</header><!-- #site-header -->
	</div><!-- #site-header-wrapper -->


	<div id="main" class="site-main">

		<div class="block-content" style="background: transparent;">
			<div class="container">
				<div class="styled-subtitle">
					<p>This interactive preview is not a live preview of your website, it is a preview of your site's header & menu layout & style. Use controls above to make the desired setup. The changes will be visible here. Hover menus to see the submenu style. Click on hamburger icon to open menus. This text is just some dummy body text sample. Don't pay attention to this dummy text, concentrate on menu & header settings.</p>
				</div>
			</div><!-- .container -->
		</div>

	</div><!-- #main -->
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
