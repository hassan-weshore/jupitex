<?php

/**
 * TheGem Mega Menu Walker class.
 *
*/

class TheGem_Mega_Menu_Walker extends Walker_Nav_Menu {

	private $items_data = array();
	private $tree_megamenu_root = array();
	private $line_columns_count = 0;

	private function get_item_data($item_id) {
		global $TheGem_Mega_Menu_Default;

		if (!isset($this->items_data[$item_id])) {
			$data = get_post_meta( $item_id, '_menu_item_thegem_mega_menu', true );
			$this->items_data[$item_id] = array_merge($TheGem_Mega_Menu_Default, (array) $data);
		}
		$this->thegem_mobile_clickable = get_post_meta( $item_id, '_menu_item_thegem_mobile_clickable', true );
		return $this->items_data[$item_id];
	}

	private function init_menu_logo($args) {

		if ( !isset( $this->menu_logo_inited ) ) {

			$this->menu_logo_inited = true;
			$this->root_items_counter = 0;
			$this->logo_breakpoint = -1;

			$logo_position = thegem_get_option('logo_position', 'left');
			if ( $args->menu_id == 'primary-menu' && !empty($args->menu) && $logo_position == 'menu_center' ) {
				if(is_object($args->menu)) {
					$items = wp_get_nav_menu_items( $args->menu->term_id );
				} else {
					$items = wp_get_nav_menu_items( $args->menu );
				}
				$root_elements_number = $this->get_number_of_root_elements( $items );
				$this->logo_breakpoint = intval($root_elements_number / 2.0 + 0.7);
			}

		}
	}

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);

		$styles = array();
		$classes = array();

		if ($depth == 0 && $this->tree_megamenu_root['enable'] && !empty($this->tree_megamenu_root['image'])) {
			$styles['background-image'] = "url(" . $this->tree_megamenu_root['image'] . ")";
			$styles['background-position'] = $this->tree_megamenu_root['image_position'];
		}

		if ($depth == 0 && $this->tree_megamenu_root['enable'] && $this->tree_megamenu_root["padding_left"] != "") {
			if (strpos($this->tree_megamenu_root["padding_left"], 'px') !== false) {
				$padding_left = floatval($this->tree_megamenu_root["padding_left"]);

				if ($this->tree_megamenu_root['style'] == 'default' && $padding_left < 20) {
					$this->tree_megamenu_root['padding_left'] = '20px';
				}
			}

			$styles['padding-left'] = $this->tree_megamenu_root['padding_left'];
		}

		if ($depth == 0 && $this->tree_megamenu_root['enable'] && $this->tree_megamenu_root["padding_right"] != "")
			$styles['padding-right'] = $this->tree_megamenu_root['padding_right'];

		if ($depth == 0 && $this->tree_megamenu_root['enable'] && $this->tree_megamenu_root["padding_top"] != "")
			$styles['padding-top'] = $this->tree_megamenu_root['padding_top'];

		if ($depth == 0 && $this->tree_megamenu_root['enable'] && $this->tree_megamenu_root["padding_bottom"] != "")
			$styles['padding-bottom'] = $this->tree_megamenu_root['padding_bottom'];

		$styles_str = '';
		foreach ($styles as $k => $v)
			$styles_str .= $k . ':' . $v . '; ';

		if ($depth == 0 && $this->tree_megamenu_root['enable']) {
			if (intval($styles['padding-left']) == 0) {
				$classes[] = 'megamenu-empty-left';
			}

			if (intval($styles['padding-right']) == 0) {
				$classes[] = 'megamenu-empty-right';
			}

			if (intval($styles['padding-top']) == 0) {
				$classes[] = 'megamenu-empty-top';
			}

			if (intval($styles['padding-bottom']) == 0) {
				$classes[] = 'megamenu-empty-bottom';
			}
		}

		if ($this->tree_megamenu_root['masonry']) {
			$classes[] = 'megamenu-masonry';
		}

		$data_columns = '';
		if ($depth == 0 && $this->tree_megamenu_root['enable'])
			$data_columns = ' data-megamenu-columns="' . $this->tree_megamenu_root["columns"] .'" ';

		if (thegem_get_option('mobile_menu_layout') == 'default') {
			$classes[] = 'dl-submenu';
		}

		$output .= "\n$indent<ul class=\"sub-menu styled " . implode(' ', $classes) . "\"" . $data_columns . (!empty($styles_str) ? ' style="' . $styles_str .'"' : '') . ">\n";
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$mega_data = $this->get_item_data($item->ID);
		if(thegem_get_option('header_layout') === 'overlay' && $mega_data['enable']) {
			$mega_data['enable'] = false;
		}
		$this->init_menu_logo($args);

		if ($depth == 0)
			$this->tree_megamenu_root = $mega_data;

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		if ($depth == 0 && $mega_data['enable']) {
			$classes[] = 'megamenu-enable';
			$classes[] = 'megamenu-style-' . $mega_data['style'];
			$this->line_columns_count = 0;
			wp_enqueue_style('icons-fontawesome');
		}

		$new_line_li = '';
		if ($depth == 1 && $this->tree_megamenu_root['enable'] && ($mega_data['new_row'] || $this->line_columns_count == $this->tree_megamenu_root['columns'])) {
			$new_line_li = '<li class="megamenu-new-row"></li>';
			$this->line_columns_count = 0;
		}

		if ($this->line_columns_count == 0)
			$classes[] = 'megamenu-first-element';

		if ($depth == 1 && $this->tree_megamenu_root['enable'])
			$this->line_columns_count++;

		$column_style = '';
		if ($depth == 1 && $this->tree_megamenu_root['enable'] && $mega_data['width'] > 0) {
			$column_style = ' style="width: '. $mega_data['width'] .'px;" ';
		}

		$icon_attr = '';
		if ($depth == 2 && $this->tree_megamenu_root['enable'] && !empty($mega_data['icon'])) {
			$classes[] = 'megamenu-has-icon';
			//$icon_attr = ' data-icon="&#x'.$mega_data['icon'].';" ';
		}
		if ($this->thegem_mobile_clickable)
			$classes[] = 'mobile-clickable';

		/**
		 * Filter the CSS class(es) applied to a menu item's <li>.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's <li>.
		 * @param object $item	The current menu item.
		 * @param array  $args	An array of wp_nav_menu() arguments.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's <li>.
		 *
		 * @since 3.0.1
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $menu_id The ID that is applied to the menu item's <li>.
		 * @param object $item	The current menu item.
		 * @param array  $args	An array of wp_nav_menu() arguments.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . $new_line_li . '<li' . $id . $class_names . $icon_attr . $column_style . '>';

		if (true) {
			$atts = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )	 ? $item->target	 : '';
			$atts['rel']	= ! empty( $item->xfn )		? $item->xfn		: '';
			$atts['href']   = ! empty( $item->url )		? $item->url		: '';
			$atts['class'] = '';
			if ($depth == 1 && $this->tree_megamenu_root['enable'] && $mega_data['not_link'])
				$atts['class'] .= 'mega-no-link';
			if ($this->tree_megamenu_root['enable'] && !empty($mega_data['icon'])) {
				$atts['data-icon'] = "&#x" . $mega_data['icon'] . ";";
				$atts['class'] .= " megamenu-has-icon";
			}

			/**
			 * Filter the HTML attributes applied to a menu item's <a>.
			 *
			 * @since 3.6.0
			 *
			 * @see wp_nav_menu()
			 *
			 * @param array $atts {
			 *	 The HTML attributes applied to the menu item's <a>, empty strings are ignored.
			 *
			 *	 @type string $title  Title attribute.
			 *	 @type string $target Target attribute.
			 *	 @type string $rel	The rel attribute.
			 *	 @type string $href   The href attribute.
			 * }
			 * @param object $item The current menu item.
			 * @param array  $args An array of wp_nav_menu() arguments.
			 */
			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;
			if ($depth == 1 && $this->tree_megamenu_root['enable'])
				$item_output .= '<span class="megamenu-column-header' . ($mega_data['not_show'] ? ' mega-not-show' : '') . '">';
			$item_output .= '<a'. $attributes .'>';
			/** This filter is documented in wp-includes/post-template.php */
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			if ($depth == 2 && $this->tree_megamenu_root['enable'] && !empty($mega_data['label'])) {
				$item_output .= '<span class="mega-label rounded-corners">'. $mega_data['label'] .'</span>';
			}
			$item_output .= '</a>';
			if ($depth == 1 && $this->tree_megamenu_root['enable'])
				$item_output .= '</span>';
			if (stripos($class_names, 'menu-item-parent') !== false) {
				$item_output .= '<span class="menu-item-parent-toggle"></span>';
			}
			$item_output .= $args->after;
		}

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes $args->before, the opening <a>,
		 * the menu item's title, the closing </a>, and $args->after. Currently, there is
		 * no filter for modifying the opening and closing <li> for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item		Menu item data object.
		 * @param int	$depth	   Depth of menu item. Used for padding.
		 * @param array  $args		An array of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";

		if ($depth == 0) {
			$this->root_items_counter++;

			if ($this->logo_breakpoint != -1 && $this->root_items_counter == $this->logo_breakpoint) {
				$this->print_logo($output);
			}
		}
	}

	private function print_logo(&$output) {
		$page_id = is_singular() ? get_the_ID() : 0;
		$thegem_shop_page = 0;
		if(is_404() && get_post(thegem_get_option('404_page'))) {
			$page_id = thegem_get_option('404_page');
		}
		if(is_post_type_archive('product') && function_exists('wc_get_page_id')) {
			$page_id = wc_get_page_id('shop');
			$thegem_shop_page = 1;
		}
		$header_params = thegem_get_output_page_settings($page_id);
		if((is_archive() || is_home()) && !$thegem_shop_page) {
			if(is_tax('product_cat') || is_tax('product_tag')) {
				$header_params = thegem_get_output_page_settings(0, thegem_theme_options_get_page_settings('product_categories'), 'product_category');
			} else {
				$header_params = thegem_get_output_page_settings(0, thegem_theme_options_get_page_settings('blog'), 'blog');
			}
		}
		if(is_tax() || is_category() || is_tag()) {
			$thegem_term_id = get_queried_object()->term_id;
			if(get_term_meta($thegem_term_id , 'thegem_taxonomy_custom_page_options', true)) {
				$header_params = thegem_get_output_page_settings($thegem_term_id, array(), 'term');
			}
		}
		if(is_search()) {
			$header_params = thegem_get_output_page_settings(0, thegem_theme_options_get_page_settings('search'), 'search');
		}

		$header_light = $header_params['header_menu_logo_light'] ? '_light' : '';
		$output .= '<li class="menu-item-logo">' . thegem_print_logo($header_light, false) . '</li>';
	}

} // TheGem_Mega_Menu_Walker

?>
