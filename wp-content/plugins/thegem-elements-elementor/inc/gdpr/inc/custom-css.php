<?php
	$data = $this->get_fallback_fonts_data();
	$custom_fonts = $data['custom_fonts'];
	$fallback_items = $data['fallback_items'];
	$exclude_items = $data['exclude_items'];
?>
<?php if (!empty($custom_fonts)): ?>
<?php foreach ($custom_fonts as $custom_font): ?>
@font-face {
	font-family: '<?php echo sanitize_text_field($custom_font['font_name']); ?>';
	src: url('<?php echo esc_url($custom_font['font_url_eot']); ?>');
	src: url('<?php echo esc_url($custom_font['font_url_eot']); ?>?#iefix') format('embedded-opentype'),
	url('<?php echo esc_url($custom_font['font_url_woff']); ?>') format('woff'),
	url('<?php echo esc_url($custom_font['font_url_ttf']); ?>') format('truetype'),
	url('<?php echo esc_url($custom_font['font_url_svg'].'#'.$custom_font['font_svg_id']); ?>') format('svg');
	font-weight: normal;
	font-style: normal;
}
<?php endforeach; ?>
<?php endif; ?>

<?php $default_font_name = isset($fallback_items['default']) ? esc_attr($fallback_items['default']['font_name']) : false; ?>

<?php if ((isset($fallback_items['main_menu_font']) || $default_font_name) && !in_array('main_menu_font', $exclude_items)) : ?>
#primary-menu.no-responsive > li > a,
#primary-menu.no-responsive > li.megamenu-enable > ul > li span.megamenu-column-header a,
.widget_nav_menu > div > ul > li > a,
.widget_submenu > div > ul > li > a,
.widget_pages > ul > li > a,
.widget_categories > ul > li > a,
.widget_product_categories > ul > li > a {
	font-family: '<?php echo (isset($fallback_items['main_menu_font']) ? esc_attr($fallback_items['main_menu_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['main_menu_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['main_menu_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['main_menu_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['main_menu_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['submenu_font']) || $default_font_name) && !in_array('submenu_font', $exclude_items)) : ?>
#primary-menu.no-responsive > li.megamenu-enable > ul li > a,
#primary-menu.no-responsive > li li > a,
#primary-menu.no-responsive > li .minicart-product-title,
.portfolio-filters-resp ul li a,
.widget_nav_menu ul.menu,
.widget_submenu > div > ul,
.widget_categories > ul,
.widget_product_categories > ul,
.widget_pages > ul {
	font-family: '<?php echo (isset($fallback_items['submenu_font']) ? esc_attr($fallback_items['submenu_font']['font_name']) : $default_font_name); ?>';
}
.primary-navigation.responsive li a {
	font-family: '<?php echo (isset($fallback_items['submenu_font']) ? esc_attr($fallback_items['submenu_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['submenu_font']['font_size'])) : ?>
#primary-menu.no-responsive > li.megamenu-enable > ul li > a,
#primary-menu.no-responsive > li li > a,
#primary-menu.no-responsive > li .minicart-product-title,
.portfolio-filters-resp ul li a,
.widget_nav_menu ul.menu,
.widget_submenu > div > ul,
.widget_categories > ul,
.widget_product_categories > ul,
.widget_pages > ul {
	font-size: <?php echo esc_attr($fallback_items['submenu_font']['font_size']); ?>px;
}
.primary-navigation.responsive li a {
	font-size: <?php echo esc_attr($fallback_items['submenu_font']['font_size']); ?>px;
}
<?php endif; ?>
<?php if (isset($fallback_items['submenu_font']['line_height'])) : ?>
#primary-menu.no-responsive > li.megamenu-enable > ul li > a,
#primary-menu.no-responsive > li li > a,
#primary-menu.no-responsive > li .minicart-product-title,
.portfolio-filters-resp ul li a,
.widget_nav_menu ul.menu,
.widget_submenu > div > ul,
.widget_categories > ul,
.widget_product_categories > ul,
.widget_pages > ul {
	line-height: <?php echo esc_attr($fallback_items['submenu_font']['line_height']); ?>px;
}
.primary-navigation.responsive li a {
	line-height: <?php echo esc_attr($fallback_items['submenu_font']['line_height']); ?>px;
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['overlay_menu_font']) || $default_font_name) && !in_array('overlay_menu_font', $exclude_items)) : ?>
.header-layout-overlay #primary-menu.no-responsive > li > a,
.header-layout-overlay #primary-menu.no-responsive > li li > a,
.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li > a,
.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li li > a {
	font-family: '<?php echo (isset($fallback_items['overlay_menu_font']) ? esc_attr($fallback_items['overlay_menu_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['overlay_menu_font']['font_size'])) : ?>
.header-layout-overlay #primary-menu.no-responsive > li > a,
.header-layout-overlay #primary-menu.no-responsive > li li > a,
.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li > a,
.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li li > a {
	font-size: <?php echo esc_attr($fallback_items['overlay_menu_font']['font_size']); ?>px;
}
@media (max-width: 767px) {
	.header-layout-overlay #primary-menu.no-responsive > li > a,
	.header-layout-overlay #primary-menu.no-responsive > li li > a,
	.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li > a,
	.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li li > a {
		font-size: <?php echo round(esc_attr($fallback_items['overlay_menu_font']['font_size'])/1.333); ?>px;
	}
}
<?php endif; ?>
<?php if (isset($fallback_items['overlay_menu_font']['line_height'])) : ?>
.header-layout-overlay #primary-menu.no-responsive > li > a,
.header-layout-overlay #primary-menu.no-responsive > li li > a,
.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li > a,
.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li li > a {
	line-height: <?php echo esc_attr($fallback_items['overlay_menu_font']['line_height']); ?>px;
}
@media (max-width: 767px) {
	.header-layout-overlay #primary-menu.no-responsive > li > a,
	.header-layout-overlay #primary-menu.no-responsive > li li > a,
	.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li > a,
	.page-additional-menu.header-layout-overlay .nav-menu.no-responsive > li li > a {
		line-height: <?php echo round(esc_attr($fallback_items['overlay_menu_font']['line_height'])/1.333); ?>px;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['mobile_menu_font']) || $default_font_name) && !in_array('mobile_menu_font', $exclude_items)) : ?>
#primary-navigation.responsive #primary-menu li > a,
#primary-navigation.responsive #primary-menu li > span.megamenu-column-header > a {
	font-family: '<?php echo (isset($fallback_items['mobile_menu_font']) ? esc_attr($fallback_items['mobile_menu_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['mobile_menu_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['mobile_menu_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['mobile_menu_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['mobile_menu_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['styled_subtitle_font']) || $default_font_name) && !in_array('styled_subtitle_font', $exclude_items)) : ?>
.styled-subtitle,
.diagram-circle .text div span.title,
.diagram-circle .text div span.summary,
.vc_pie_chart .vc_pie_chart_value,
form.cart .quantity .qty,
.shop_table .quantity .qty,
.woocommerce-before-cart .cart-short-info,
input[type="text"].coupon-code,
.cart_totals table th,
.order-totals table th,
.cart_totals table .shipping td,
.woocommerce-message,
.woocommerce-info,
.woocommerce ul.woocommerce-error li,
.woocommerce table.checkout-cart-info-table tr th,
.woocommerce table.checkout-cart-info-table tr.shipping td,
.widget_calendar caption,
.blog-style-timeline .post-time,
.gem-dropcap.gem-dropcap-style-medium,
.project-info-shortcode-style-default .project-info-shortcode-item .title,
.project_info-item-style-2 .project_info-item .title,
.diagram-legend .legend-element .title,
.single-product-content .price,
.widget_shopping_cart ul li .quantity,
.widget_shopping_cart .total span.amount {
	font-family: '<?php echo (isset($fallback_items['styled_subtitle_font']) ? esc_attr($fallback_items['styled_subtitle_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['styled_subtitle_font']['font_size'])) : ?>
.styled-subtitle,
.vc_pie_chart,
form.cart .quantity .qty,
.shop_table .quantity .qty,
.woocommerce-before-cart .cart-short-info,
input[type="text"].coupon-code,
.cart_totals table th,
.order-totals table th,
.cart_totals table .shipping td,
.woocommerce-message,
.woocommerce-info,
.woocommerce ul.woocommerce-error li,
.woocommerce table.checkout-cart-info-table tr th
.woocommerce table.checkout-cart-info-table tr.shipping td,
.widget_calendar caption,
.blog-style-timeline .post-time,
.gem-dropcap.gem-dropcap-style-medium,
.project-info-shortcode-style-default .project-info-shortcode-item .title,
.project_info-item-style-2 .project_info-item .title,
.diagram-legend .legend-element .title,
.widget_shopping_cart .total span.amount {
	font-size: <?php echo esc_attr($fallback_items['styled_subtitle_font']['font_size']); ?>px;
}
@media (max-width: 600px) {
	.styled-subtitle,
	.vc_pie_chart,
	form.cart .quantity .qty,
	.shop_table .quantity .qty,
	.woocommerce-before-cart .cart-short-info,
	input[type="text"].coupon-code,
	.cart_totals table th,
	.order-totals table th,
	.cart_totals table .shipping td,
	.woocommerce-message,
	.woocommerce-info,
	.woocommerce ul.woocommerce-error li,
	.woocommerce table.checkout-cart-info-table tr th
	.woocommerce table.checkout-cart-info-table tr.shipping td,
	.widget_calendar caption,
	.blog-style-timeline .post-time,
	.gem-dropcap.gem-dropcap-style-medium,
	.project-info-shortcode-style-default .project-info-shortcode-item .title,
	.project_info-item-style-2 .project_info-item .title,
	.diagram-legend .legend-element .title,
	.widget_shopping_cart .total span.amount{
		font-size: <?php echo (esc_attr($fallback_items['styled_subtitle_font']['font_size'])*100/600); ?>vw;
	}
}
<?php endif; ?>
<?php if (isset($fallback_items['styled_subtitle_font']['line_height'])) : ?>
.styled-subtitle,
.woocommerce-before-cart .cart-short-info,
.cart_totals table th,
.order-totals table th,
.cart_totals table .shipping td,
.woocommerce-message,
.woocommerce-info,
.woocommerce ul.woocommerce-error li,
.woocommerce table.checkout-cart-info-table tr th,
.woocommerce table.checkout-cart-info-table tr.shipping td,
.widget_calendar caption,
.blog-style-timeline .post-time,
.project-info-shortcode-style-default .project-info-shortcode-item .title,
.project_info-item-style-2 .project_info-item .title,
.diagram-legend .legend-element .title {
	line-height: <?php echo esc_attr($fallback_items['styled_subtitle_font']['line_height']); ?>px;
}
@media (max-width: 600px) {
	.styled-subtitle,
	.woocommerce-before-cart .cart-short-info,
	.cart_totals table th,
	.order-totals table th,
	.cart_totals table .shipping td,
	.woocommerce-message,
	.woocommerce-info,
	.woocommerce ul.woocommerce-error li,
	.woocommerce table.checkout-cart-info-table tr th,
	.woocommerce table.checkout-cart-info-table tr.shipping td,
	.widget_calendar caption,
	.blog-style-timeline .post-time,
	.project-info-shortcode-style-default .project-info-shortcode-item .title,
	.project_info-item-style-2 .project_info-item .title,
	.diagram-legend .legend-element .title {
		line-height: <?php echo (esc_attr($fallback_items['styled_subtitle_font']['line_height'])*100/600); ?>vw;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['h1_font']) || $default_font_name) && !in_array('h1_font', $exclude_items)) : ?>
h1,
body .pricing-table-style-8 .pricing-cost,
.title-h1 {
	font-family: '<?php echo (isset($fallback_items['h1_font']) ? esc_attr($fallback_items['h1_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['h1_font']['font_size'])) : ?>
h1,
.title-h1 {
	font-size: <?php echo esc_attr($fallback_items['h1_font']['font_size']); ?>px;
}
@media (max-width: 1000px) {
	h1,
	.title-h1 {
		font-size: <?php echo (esc_attr($fallback_items['h1_font']['font_size'])*100/1000); ?>vw;
	}
}
<?php endif; ?>
<?php if (isset($fallback_items['h1_font']['line_height'])) : ?>
h1,
.title-h1 {
	line-height: <?php echo esc_attr($fallback_items['h1_font']['line_height']); ?>px;
}
@media (max-width: 1000px) {
	h1,
	.title-h1 {
		line-height: <?php echo (esc_attr($fallback_items['h1_font']['line_height'])*100/1000); ?>vw;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['h2_font']) || $default_font_name) && !in_array('h2_font', $exclude_items)) : ?>
h2,
.title-h2,
h3.comment-reply-title,
body .pricing-table-style-6 .pricing-price-title {
	font-family: '<?php echo (isset($fallback_items['h2_font']) ? esc_attr($fallback_items['h2_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['h2_font']['font_size'])) : ?>
h2,
.title-h2,
body .pricing-table-style-5  .pricing-price-title,
body .pricing-table-style-6 .pricing-price-title,
body .pricing-table-style-6 .pricing-price-subtitle,
h3.comment-reply-title,
body .pricing-table-style-2 .pricing-price-title {
	font-size: <?php echo esc_attr($fallback_items['h2_font']['font_size']); ?>px;
}
@media (max-width: 1000px) {
	h2,
	.title-h2,
	body .pricing-table-style-5  .pricing-price-title,
	body .pricing-table-style-6 .pricing-price-title,
	body .pricing-table-style-6 .pricing-price-subtitle,
	h3.comment-reply-title,
	body .pricing-table-style-2 .pricing-price-title {
		font-size: <?php echo (esc_attr($fallback_items['h2_font']['font_size'])*100/1000); ?>vw;
	}
}
<?php endif; ?>
<?php if (isset($fallback_items['h2_font']['line_height'])) : ?>
h2,
.title-h2,
body .pricing-table-style-6 .pricing-price-title,
h3.comment-reply-title {
	line-height: <?php echo esc_attr($fallback_items['h2_font']['line_height']); ?>px;
}
@media (max-width: 1000px) {
	h2,
	.title-h2,
	body .pricing-table-style-6 .pricing-price-title,
	h3.comment-reply-title {
		line-height: <?php echo (esc_attr($fallback_items['h2_font']['line_height'])*100/1000); ?>vw;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['h3_font']) || $default_font_name) && !in_array('h3_font', $exclude_items)) : ?>
h3,
.title-h3,
.cart_totals table .order-total td,
.woocommerce table.shop_table.order-details tr.cart_item td.product-total .amount,
.woocommerce table.shop_table.order-details tr.order_item td.product-total .amount,
.gem-dropcap,
.woocommerce .woocommerce-checkout-one-page #order_review table thead th {
	font-family: '<?php echo (isset($fallback_items['h3_font']) ? esc_attr($fallback_items['h3_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['h3_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['h3_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['h3_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['h3_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['h4_font']) || $default_font_name) && !in_array('h4_font', $exclude_items)) : ?>
h4,
.title-h4,
.widget .gem-teams-name,
body .pricing-table-style-3 .pricing_row_title,
body .pricing-table-style-8 .pricing_row_title,
body .pricing-table-style-4 .pricing_row_title,
.gem-gallery-hover-gradient .gem-gallery-item-title,
.gem-gallery-grid.hover-gradient .gallery-item .overlay .title,
.gem-gallery-hover-gradient .gem-gallery-preview-carousel-wrap .gem-gallery-item .gem-gallery-item-title {
	font-family: '<?php echo (isset($fallback_items['h4_font']) ? esc_attr($fallback_items['h4_font']['font_name']) : $default_font_name); ?>';
}
@media only screen and (min-width: 992px) and (max-width: 1150px) {
	.with-sidebar .portfolio.hover-horizontal-sliding .overlay .links .caption .title {
		font-family: '<?php echo (isset($fallback_items['h4_font']) ? esc_attr($fallback_items['h4_font']['font_name']) : $default_font_name); ?>';
	}
}
<?php if (isset($fallback_items['h4_font']['font_size'])) : ?>
h4,
.title-h4,
body .pricing-table-style-7 .pricing-price-title,
body .pricing-table-style-4 .pricing_row_title,
body .pricing-table-style-3 .pricing_row_title,
body .pricing-table-style-2 .pricing-cost,
body .pricing-table-style-2 .time,
body .pricing-table-style-1 .pricing-price-title,
.widget .gem-teams-name,
body .pricing-table-style-8 .pricing_row_title,
.gem-gallery-hover-gradient .gem-gallery-item-title,
.gem-gallery-grid.hover-gradient .gallery-item .overlay .title,
.gem-gallery-hover-gradient .gem-gallery-preview-carousel-wrap .gem-gallery-item .gem-gallery-item-title,
body .vc_separator h4 {
	font-size: <?php echo esc_attr($fallback_items['h4_font']['font_size']); ?>px;
}
@media only screen and (min-width: 992px) and (max-width: 1150px) {
	.with-sidebar .portfolio.hover-horizontal-sliding .overlay .links .caption .title {
		font-size: <?php echo esc_attr($fallback_items['h4_font']['font_size']); ?>px;
	}
}
<?php endif; ?>
<?php if (isset($fallback_items['h4_font']['line_height'])) : ?>
h4,
.title-h4,
body .pricing-table-style-7 .pricing-price-title,
body .pricing-table-style-5 .pricing-price-title,
body .pricing-table-style-4 .pricing_row_title,
body .pricing-table-style-3 .pricing_row_title,
body .pricing-table-style-2 .pricing-cost,
body .pricing-table-style-2 .time,
body .pricing-table-style-2 .pricing-price-title,
body .pricing-table-style-1 .pricing-price-title,
.widget .gem-teams-name,
.portfolio.columns-1 .portfolio-item .caption .title,
.gem-gallery-hover-gradient .gem-gallery-item-title,
.gem-gallery-grid.hover-gradient .gallery-item .overlay .title,
.gem-gallery-hover-gradient .gem-gallery-preview-carousel-wrap .gem-gallery-item .gem-gallery-item-title,
body .vc_separator h4 {
	line-height: <?php echo esc_attr($fallback_items['h4_font']['line_height']); ?>px;
}
@media only screen and (min-width: 992px) and (max-width: 1150px) {
	.with-sidebar .portfolio.hover-horizontal-sliding .overlay .links .caption .title {
		line-height: <?php echo esc_attr($fallback_items['h4_font']['line_height']); ?>px;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['h5_font']) || $default_font_name) && !in_array('h5_font', $exclude_items)) : ?>
h5,
.gem-table thead th,
.title-h5,
.gem-teams-phone,
.shop_table td.product-price,
.shop_table td.product-subtotal,
.cart_totals table .cart-subtotal td,
.woocommerce-cart-form.responsive .cart-item .gem-table .shop_table td.product-subtotal,
.woocommerce table.shop_table.order-details thead tr th.product-name,
.woocommerce table.shop_table.order-details thead tr th.product-total,
.widget-gem-portfolio-item.gem-portfolio-dummy:after,
.resp-tabs-list li,
.gem-quote.gem-quote-style-4,
.gem-quote.gem-quote-style-5,
.blog-style-styled_list1 .post-time,
.gem-teams-phone,
blockquote.wp-block-quote.is-large,
blockquote.wp-block-quote.is-style-large,
.woocommerce .woocommerce-checkout.woocommerce-checkout-one-page .shop_table.woocommerce-checkout-payment-total th {
	font-family: '<?php echo (isset($fallback_items['h5_font']) ? esc_attr($fallback_items['h5_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['h5_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['h5_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['h5_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['h5_font']['line_height']); ?>px;}
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['h6_font']) || $default_font_name) && !in_array('h6_font', $exclude_items)) : ?>
h6,
.title-h6,
.project_info-item-style-1 .project_info-item .title,
.project-info-shortcode-style-2 .project-info-shortcode-item .title,
.gem_accordion_header a,
#wp-calendar caption,
.hamburger-minicart .minicart-item-count,
.wpb_toggle,
.vc_toggle_title h4,
.testimonials-style-1-name.gem-testimonial-name,
.testimonials-style-2-name.gem-testimonial-name,
.diagram-wrapper .digram-line-box .skill-amount,
.diagram-wrapper.style-3 .digram-line-box .skill-title,
.row .vc_progress_bar .vc_label,
.pricing-column-top-choice .pricing-column-top-choice-text {
	font-family: '<?php echo (isset($fallback_items['h6_font']) ? esc_attr($fallback_items['h6_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['h6_font']['font_size'])) : ?>
h6,
.title-h6,
.project_info-item-style-1 .project_info-item .title,
.gem_accordion_header a,
#wp-calendar caption,
.wpb_toggle,
.gem-table-responsive .tabletolist.rh > li > .titles,
.vc_toggle_title h4,
.diagram-wrapper.style-3 .digram-line-box  .skill-title,
.row .vc_progress_bar .vc_label {
font-size: <?php echo esc_attr($fallback_items['h6_font']['font_size']); ?>px;
}
<?php endif; ?>
<?php if (isset($fallback_items['h6_font']['line_height'])) : ?>
h6,
.title-h6,
.project_info-item-style-1 .project_info-item .title,
.gem_accordion_header a,
#wp-calendar caption,
.wpb_toggle,
.gem-table-responsive .tabletolist.rh > li > .titles,
.vc_toggle_title h4,
.diagram-wrapper.style-3 .digram-line-box  .skill-title,
.row .vc_progress_bar .vc_label {
	line-height: <?php echo esc_attr($fallback_items['h6_font']['line_height']); ?>px;
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['xlarge_title_font']) || $default_font_name) && !in_array('xlarge_title_font', $exclude_items)) : ?>
.title-xlarge {
	font-family: '<?php echo (isset($fallback_items['xlarge_title_font']) ? esc_attr($fallback_items['xlarge_title_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['xlarge_title_font']['font_size'])) : ?>
.title-xlarge {
	font-size: <?php echo esc_attr($fallback_items['xlarge_title_font']['font_size']); ?>px;
}
@media (max-width: 1000px) {
	.title-xlarge{
		font-size: <?php echo (esc_attr($fallback_items['xlarge_title_font']['font_size'])*100/1000); ?>vw;
	}
}
<?php endif; ?>
<?php if (isset($fallback_items['xlarge_title_font']['line_height'])) : ?>
.title-xlarge {
	line-height: <?php echo esc_attr($fallback_items['xlarge_title_font']['line_height']); ?>px;
}
@media (max-width: 1000px) {
	.title-xlarge {
		line-height: <?php echo (esc_attr($fallback_items['xlarge_title_font']['line_height'])*100/1000); ?>vw;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['light_title_font']) || $default_font_name) && !in_array('light_title_font', $exclude_items)) : ?>
h1 .light,
h2 .light,
h3 .light,
h4 .light,
h5 .light,
h6 .light,
.title-h1 .light,
.title-h2 .light,
.title-h3 .light,
.title-h4 .light,
.title-h5 .light,
.title-h6 .light,
.title-xlarge .light,
h1.light,
h2.light,
h3.light,
h4.light,
h5.light,
h6.light,
.title-h1.light,
.title-h2.light,
.title-h3.light,
.title-h4.light,
.title-h5.light,
.title-h6.light,
.title-xlarge.light,
.widget .gem-teams-name,
.gem-counter-style-2 .gem-counter-number,
.gem-gallery-grid .gallery-item .overlay .title,
.gem-quote.gem-quote-style-3 blockquote,
.row  .vc_pie_wrapper span,
.blog-style-styled_list1 .post-title,
.blog-style-styled_list2 .post-time,
.blog-style-styled_list2 .post-title,
.blog-style-timeline .post-title,
body .pricing-table-style-1 .pricing-price-title,
body .pricing-table-style-1  .pricing-cost,
body .pricing-table-style-2 .pricing-price-title,
body .pricing-table-style-2  .pricing-cost,
body .pricing-table-style-2 .time,
body .pricing-table-style-5  .pricing-price-title,
body .pricing-table-style-6 .pricing-price-subtitle,
body .pricing-table-style-7 .pricing-price-title,
body .pricing-table-style-7 .pricing-cost,
body .vc_grid-container ul.vc_grid-filter li.vc_grid-filter-item > span,
.gem-media-grid .vc_gitem-post-data-source-post_title a,
.gem-media-grid-2 .vc_gitem-post-data-source-post_title a,
.woocommerce .woocommerce-checkout-one-page #order_review table thead th,
.woocommerce .woocommerce-checkout.woocommerce-checkout-one-page .shop_table.woocommerce-checkout-payment-total th {
	font-family: '<?php echo (isset($fallback_items['light_title_font']) ? esc_attr($fallback_items['light_title_font']['font_name']) : $default_font_name); ?>';
}
<?php endif; ?>

<?php if ((isset($fallback_items['body_font']) || $default_font_name) && !in_array('body_font', $exclude_items)) : ?>
body,
option,
.gem-table thead th,
.portfolio.columns-1 .portfolio-item .caption .subtitle,
.gallery-item .overlay .subtitle,
.cart_totals table .shipping td label,
.widget_archive li,
.woocommerce-cart-form.responsive .cart-item .gem-table .shop_table td.product-price,
.gem-media-grid-2 .vc_gitem-animated-block .vc_gitem-zone-b .vc_gitem-post-data-source-post_excerpt > div {
	font-family: '<?php echo (isset($fallback_items['body_font']) ? esc_attr($fallback_items['body_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['body_font']['font_size'])) : ?>
body,
.gem-table thead th,
.inline-column,
.inline-inside > *,
option,
.portfolio.columns-1 .portfolio-item .caption .subtitle,
.gallery-item .overlay .subtitle,
.cart_totals table .shipping td label,
.woocommerce-cart-form.responsive .cart-item .gem-table .shop_table td.product-price,
.portfolio-filters a,
.gem-media-grid-2 .vc_gitem-animated-block .vc_gitem-zone-b .vc_gitem-post-data-source-post_excerpt > div {
	font-size: <?php echo esc_attr($fallback_items['body_font']['font_size']); ?>px;
}
<?php endif; ?>
<?php if (isset($fallback_items['body_font']['line_height'])) : ?>
body,
option,
.inline-column,
.inline-inside > *,
.related-element-info > a,
.gallery-item .overlay .subtitle,
.cart_totals table .shipping td label,
.woocommerce-cart-form.responsive .cart-item .gem-table .shop_table td.product-price,
.gem-media-grid-2 .vc_gitem-animated-block .vc_gitem-zone-b .vc_gitem-post-data-source-post_excerpt > div {
	line-height: <?php echo esc_attr($fallback_items['body_font']['line_height']); ?>px;
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['title_excerpt_font']) || $default_font_name) && !in_array('title_excerpt_font', $exclude_items)) : ?>
.page-title-excerpt,
.custom-title-excerpt {
	font-family: '<?php echo (isset($fallback_items['title_excerpt_font']) ? esc_attr($fallback_items['title_excerpt_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['title_excerpt_font']['font_size'])) : ?>
.page-title-excerpt,
.custom-title-excerpt {
	font-size: <?php echo esc_attr($fallback_items['title_excerpt_font']['font_size']); ?>px;
}
@media (max-width: 600px) {
	.page-title-excerpt,
	.custom-title-excerpt {
		font-size: <?php echo (esc_attr($fallback_items['title_excerpt_font']['font_size'])*100/600); ?>vw;
	}
}
<?php endif; ?>
<?php if (isset($fallback_items['title_excerpt_font']['line_height'])) : ?>
.page-title-excerpt,
.custom-title-excerpt {
	line-height: <?php echo esc_attr($fallback_items['title_excerpt_font']['line_height']); ?>px;
}
@media (max-width: 600px) {
	.page-title-excerpt,
	.custom-title-excerpt {
		line-height: <?php echo (esc_attr($fallback_items['title_excerpt_font']['line_height'])*100/600); ?>vw;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['widget_title_font']) || $default_font_name) && !in_array('widget_title_font', $exclude_items)) : ?>
.widget-title {
	font-family: '<?php echo (isset($fallback_items['widget_title_font']) ? esc_attr($fallback_items['widget_title_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['widget_title_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['widget_title_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['widget_title_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['widget_title_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['button_font']) || $default_font_name) && !in_array('button_font', $exclude_items)) : ?>
.gem-button,
input[type='submit'],
.gem-pagination a,
.gem-pagination .current,
.blog-load-more button,
body .wp-block-button .wp-block-button__link,
body .vc_grid-pagination .vc_grid-pagination-list li.vc_grid-page a {
	font-family: '<?php echo (isset($fallback_items['button_font']) ? esc_attr($fallback_items['button_font']['font_name']) : $default_font_name); ?>';
}
<?php endif; ?>

<?php if ((isset($fallback_items['button_thin_font']) || $default_font_name) && !in_array('button_thin_font', $exclude_items)) : ?>
.gem-button.gem-button-text-weight-thin {
	font-family: '<?php echo (isset($fallback_items['button_thin_font']) ? esc_attr($fallback_items['button_thin_font']['font_name']) : $default_font_name); ?>';
}
<?php endif; ?>

<?php if ((isset($fallback_items['portfolio_title_font']) || $default_font_name) && !in_array('portfolio_title_font', $exclude_items)) : ?>
.portfolio-item-title,
.portfolio-item .wrap > .caption .title,
.fancybox-title .slide-info-title,
.blog.blog-style-masonry article .description .title {
	font-family: '<?php echo (isset($fallback_items['portfolio_title_font']) ? esc_attr($fallback_items['portfolio_title_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['portfolio_title_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['portfolio_title_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['portfolio_title_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['portfolio_title_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['portfolio_description_font']) || $default_font_name) && !in_array('portfolio_description_font', $exclude_items)) : ?>
.portfolio-item-description,
.portfolio-item .caption .subtitle,
.fancybox-title .slide-info-summary,
.blog.blog-style-masonry article .description .summary {
	font-family: '<?php echo (isset($fallback_items['portfolio_description_font']) ? esc_attr($fallback_items['portfolio_description_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['portfolio_description_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['portfolio_description_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['portfolio_description_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['portfolio_description_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php if (isset($fallback_items['portfolio_description_font']['line_height'])) : ?>
.portfolio:not(.columns-1):not(.portfolio-style-masonry) .portfolio-item .wrap > .caption .subtitle {
	max-height: <?php echo esc_attr($fallback_items['portfolio_description_font']['line_height']); ?>px;
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['quickfinder_title_font']) || $default_font_name) && !in_array('quickfinder_title_font', $exclude_items)) : ?>
.quickfinder-item-title {
	font-family: '<?php echo (isset($fallback_items['quickfinder_title_font']) ? esc_attr($fallback_items['quickfinder_title_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['quickfinder_title_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['quickfinder_title_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['quickfinder_title_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['quickfinder_title_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['quickfinder_title_thin_font']) || $default_font_name) && !in_array('quickfinder_title_thin_font', $exclude_items)) : ?>
.quickfinder.quickfinder-title-thin .quickfinder-item-title {
	font-family: '<?php echo (isset($fallback_items['quickfinder_title_thin_font']) ? esc_attr($fallback_items['quickfinder_title_thin_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['quickfinder_title_thin_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['quickfinder_title_thin_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['quickfinder_title_thin_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['quickfinder_title_thin_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['quickfinder_description_font']) || $default_font_name) && !in_array('quickfinder_description_font', $exclude_items)) : ?>
.quickfinder-item-text {
	font-family: '<?php echo (isset($fallback_items['quickfinder_description_font']) ? esc_attr($fallback_items['quickfinder_description_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['quickfinder_description_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['quickfinder_description_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['quickfinder_description_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['quickfinder_description_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['gallery_title_font']) || $default_font_name) && !in_array('gallery_title_font', $exclude_items)) : ?>
.gem-gallery-hover-zooming-blur .gem-gallery-item-title,
.gem-gallery-grid .gallery-item .overlay .title,
.gem-gallery .gem-gallery-caption .gem-gallery-item-title{
	font-family: '<?php echo (isset($fallback_items['gallery_title_font']) ? esc_attr($fallback_items['gallery_title_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['gallery_title_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['gallery_title_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['gallery_title_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['gallery_title_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['gallery_title_bold_font']) || $default_font_name) && !in_array('gallery_title_bold_font', $exclude_items)) : ?>
.gem-gallery-grid.hover-default .gallery-item .overlay .title,
.gem-gallery-hover-default .gem-gallery-item .gem-gallery-item-title{
	font-family: '<?php echo (isset($fallback_items['gallery_title_bold_font']) ? esc_attr($fallback_items['gallery_title_bold_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['gallery_title_bold_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['gallery_title_bold_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['gallery_title_bold_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['gallery_title_bold_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['gallery_description_font']) || $default_font_name) && !in_array('gallery_description_font', $exclude_items)) : ?>
.gallery-description,
.gem-gallery-item-description {
	font-family: '<?php echo (isset($fallback_items['gallery_description_font']) ? esc_attr($fallback_items['gallery_description_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['gallery_description_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['gallery_description_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['gallery_description_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['gallery_description_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['testimonial_font']) || $default_font_name) && !in_array('testimonial_font', $exclude_items)) : ?>
body .gem-testimonial-text,
body .testimonials-style-1-text {
	font-family: '<?php echo (isset($fallback_items['testimonial_font']) ? esc_attr($fallback_items['testimonial_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['testimonial_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['testimonial_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['testimonial_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['testimonial_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php if (isset($fallback_items['testimonial_font']['font_size'])) : ?>
@media (max-width: 600px) {
	body .gem-testimonial-text,
	body .testimonials-style-1-text {
		font-size: <?php echo (esc_attr($fallback_items['testimonial_font']['font_size'])*100/600); ?>vw;
	}
}
<?php endif; ?>
<?php if (isset($fallback_items['testimonial_font']['line_height'])) : ?>
@media (max-width: 600px) {
	body .gem-testimonial-text,
	body .testimonials-style-1-text {
		line-height: <?php echo (esc_attr($fallback_items['testimonial_font']['line_height'])*100/600); ?>vw;
	}
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['counter_font']) || $default_font_name) && !in_array('counter_font', $exclude_items)) : ?>
.gem-counter-number,
.diagram-circle .text div span {
	font-family: '<?php echo (isset($fallback_items['counter_font']) ? esc_attr($fallback_items['counter_font']['font_name']) : $default_font_name); ?>';
}
<?php if (isset($fallback_items['counter_font']['font_size'])) : ?>
.gem-counter-number {
	font-size: <?php echo esc_attr($fallback_items['counter_font']['font_size']); ?>px;
}
<?php endif; ?>
<?php if (isset($fallback_items['counter_font']['line_height'])) : ?>
.gem-counter-number {
	line-height: <?php echo esc_attr($fallback_items['counter_font']['line_height']); ?>px;
}
<?php endif; ?>
<?php endif; ?>

<?php if ((isset($fallback_items['tabs_title_font']) || $default_font_name) && !in_array('tabs_title_font', $exclude_items)) : ?>
.wpb-js-composer .vc_tta.vc_tta-tabs .vc_tta-tab.vc_active > a,
.wpb-js-composer .vc_tta.vc_tta-tabs.vc_tta-style-outline .vc_tta-tab a,
.wpb-js-composer .vc_tta.vc_tta-tabs.vc_tta-style-modern .vc_tta-tab a,
.wpb-js-composer .vc_tta.vc_tta-tabs .vc_tta-panel.vc_active .vc_tta-panel-title,
.wpb-js-composer .vc_tta.vc_tta-accordion .vc_tta-panel.vc_active .vc_tta-panel-title,
.wpb-js-composer .vc_tta.vc_tta-tabs.vc_tta-style-outline .vc_tta-panel .vc_tta-panel-title,
.wpb-js-composer .vc_tta.vc_tta-accordion.vc_tta-style-outline .vc_tta-panel .vc_tta-panel-title
.wpb-js-composer .vc_tta.vc_tta-tabs .vc_tta-panel.vc_tta-style-modern .vc_tta-panel-title,
.wpb-js-composer .vc_tta.vc_tta-accordion.vc_tta-style-modern .vc_tta-panel .vc_tta-panel-title,
.wpb-js-composer .vc_tta.vc_tta-accordion.vc_tta-style-simple_dashed .vc_tta-panel .vc_tta-panel-title {
	font-family: '<?php echo (isset($fallback_items['tabs_title_font']) ? esc_attr($fallback_items['tabs_title_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['tabs_title_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['tabs_title_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['tabs_title_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['tabs_title_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['tabs_title_thin_font']) || $default_font_name) && !in_array('tabs_title_thin_font', $exclude_items)) : ?>
.wpb-js-composer .vc_tta.vc_tta-tabs .vc_tta-tab a,
.wpb-js-composer .vc_tta.vc_tta-tabs .vc_tta-panel .vc_tta-panel-title,
.wpb-js-composer .vc_tta.vc_tta-accordion .vc_tta-panel .vc_tta-panel-title {
	font-family: '<?php echo (isset($fallback_items['tabs_title_thin_font']) ? esc_attr($fallback_items['tabs_title_thin_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['tabs_title_thin_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['tabs_title_thin_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['tabs_title_thin_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['tabs_title_thin_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['woocommerce_price_font']) || $default_font_name) && !in_array('woocommerce_price_font', $exclude_items)) : ?>
.widget_shopping_cart_content .quantity,
.widget_shopping_cart_content .total .amount {
	font-family: '<?php echo (isset($fallback_items['woocommerce_price_font']) ? esc_attr($fallback_items['woocommerce_price_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['woocommerce_price_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['woocommerce_price_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['woocommerce_price_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['woocommerce_price_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['slideshow_title_font']) || $default_font_name) && !in_array('slideshow_title_font', $exclude_items)) : ?>
.gem-nivoslider-title {
	font-family: '<?php echo (isset($fallback_items['slideshow_title_font']) ? esc_attr($fallback_items['slideshow_title_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['slideshow_title_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['slideshow_title_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['slideshow_title_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['slideshow_title_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ((isset($fallback_items['slideshow_description_font']) || $default_font_name) && !in_array('slideshow_description_font', $exclude_items)) : ?>
.gem-nivoslider-description {
	font-family: '<?php echo (isset($fallback_items['slideshow_description_font']) ? esc_attr($fallback_items['slideshow_description_font']['font_name']) : $default_font_name); ?>';
<?php if (isset($fallback_items['slideshow_description_font']['font_size'])) : ?>
	font-size: <?php echo esc_attr($fallback_items['slideshow_description_font']['font_size']); ?>px;
<?php endif; ?>
<?php if (isset($fallback_items['slideshow_description_font']['line_height'])) : ?>
	line-height: <?php echo esc_attr($fallback_items['slideshow_description_font']['line_height']); ?>px;
<?php endif; ?>
}
<?php endif; ?>

<?php if ($default_font_name): ?>
.gdpr-privacy-preferences-title,
.gdpr-privacy-preferences-text,
.gdpr-privacy-preferences-consent-item,
.btn-gdpr-privacy-save-preferences,
.gdpr-privacy-preferences-footer-links a,
.gdpr-consent-bar-text,
.btn-gdpr-preferences-open,
.gem-gdpr-no-consent-notice-text,
.btn-gdpr-agreement {
	font-family: '<?php echo $default_font_name; ?>' !important;
}
<?php endif; ?>

