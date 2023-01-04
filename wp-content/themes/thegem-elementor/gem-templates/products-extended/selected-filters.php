<div class="portfolio-filter-item portfolio-selected-filters <?php if (isset($params['duplicate_selected_in_sidebar']) && $params['duplicate_selected_in_sidebar'] != 'yes' && $params['duplicate_selected_in_sidebar'] != '1') {
	echo 'hide-on-sidebar';
} ?> <?php echo esc_attr($params['filter_buttons_standard_alignment']); ?>">
	<div class="portfolio-selected-filter-item clear-filters">
		<?php echo esc_html($params['filters_text_labels_clear_text']); ?>
	</div>
	<?php if (isset($_GET[$grid_uid . '-category']) || isset($_GET['category'])) { ?>
		<div class="portfolio-selected-filter-item category"
			 data-filter="<?php echo esc_attr($active_cat); ?>"><?php $category = get_term_by('slug', $active_cat, 'product_cat');
			echo esc_html($category->name); ?> <i class="delete-filter"></i></div>
	<?php }
	if ($has_attr_url) {
		foreach ($attributes_current as $key => $value) {
			foreach ($value as $attr_value) { ?>
				<div class="portfolio-selected-filter-item attribute" data-attr="<?php echo esc_attr($key); ?>"
					 data-filter="<?php echo esc_attr($attr_value); ?>"><?php $category = get_term_by('slug', $attr_value, 'pa_' . $key);
					echo esc_html($category->name); ?><i class="delete-filter"></i></div>
			<?php }
		}
	}
	if (isset($_GET[$grid_uid . '-status']) || isset($_GET['status'])) {
		if (in_array('sale', $status_current)) { ?>
			<div class="portfolio-selected-filter-item status" data-filter="sale"><?php echo esc_html($params['filter_by_status_sale_text']); ?><i
						class="delete-filter"></i></div>
		<?php }
		if (in_array('stock', $status_current)) { ?>
			<div class="portfolio-selected-filter-item status" data-filter="stock"><?php echo esc_html($params['filter_by_status_stock_text']); ?>
				<i class="delete-filter"></i></div>
			<?php
		}
	}
	if (isset($_GET[$grid_uid . '-min_price']) || isset($_GET[$grid_uid . '-max_price']) || isset($_GET['min_price']) || isset($_GET['max_price'])) { ?>
		<div class="portfolio-selected-filter-item price"><?php echo esc_html(get_woocommerce_currency_symbol() . $current_min_price . ' - ' . get_woocommerce_currency_symbol() . $current_max_price); ?>
			<i class="delete-filter"></i></div>
		<?php
	}
	if (isset($_GET[$grid_uid . '-product-search']) || isset($_GET['product-search']) || isset($_GET['s'])) { ?>
		<div class="portfolio-selected-filter-item search"><?php echo esc_html($search_current); ?><i class="delete-filter"></i></div>
	<?php } ?>
</div>