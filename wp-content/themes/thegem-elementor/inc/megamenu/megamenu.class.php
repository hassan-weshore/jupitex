<?php
/**
 * TheGem Mega Menu class.
 *
*/

$TheGem_Mega_Menu_Columns_Values = array(
    1 => '1',
    2 => '2',
    3 => '3',
    4 => '4'
);

$TheGem_Mega_Menu_Styles_Values = array(
    'default' => 'Style 1',
    'grid' => 'Style 2'
);

$TheGem_Mega_Menu_Image_Position_Values = array(
    'left top' => esc_html__( 'Left Top', 'thegem' ),
    'left center' => esc_html__( 'Left Center', 'thegem' ),
    'left bottom' => esc_html__( 'Left Bottom', 'thegem' ),
    'center top' => esc_html__( 'Center Top', 'thegem' ),
    'center bottom' => esc_html__( 'Center Bottom', 'thegem' ),
    'center center' => esc_html__( 'Center Center', 'thegem' ),
    'right top' => esc_html__( 'Right Top', 'thegem' ),
    'right center' => esc_html__( 'Right Center', 'thegem' ),
    'right bottom' => esc_html__( 'Right Bottom', 'thegem' )
);

$TheGem_Mega_Menu_Default = array(
    'icon' => '',
    'enable' => false,
    'masonry' => false,
    'columns' => 3,
    'image' => '',
    'image_position' => 'center center',
    'width' => 300,
    'not_link' => false,
    'not_show' => false,
    'new_row' => false,
    'label' => '',
    'padding_left' => '0px',
    'padding_top' => '0px',
    'padding_right' => '0px',
    'padding_bottom' => '0px',
    'style' => 'default',
);

class TheGem_Mega_Menu {

	public $fat_menu = false;
	public $fat_columns = 3;

	function __construct() {

		// add custom menu fields to menu
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'add_custom_nav_fields' ) );

		// save menu custom fields
		add_action( 'wp_update_nav_menu_item', array( $this, 'update_custom_nav_fields' ), 10, 3 );

		// replace menu walker
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'replace_walker_class' ), 90, 2 );

		// add admin css
		add_action( 'admin_print_styles-nav-menus.php', array( $this, 'add_admin_menu_inline_css' ), 15 );

		// add some javascript
		add_action( 'admin_print_footer_scripts', array( $this, 'javascript_magick' ), 99 );

		// add media uploader
		add_action( 'admin_enqueue_scripts', array( $this, 'uploader_scripts' ), 15 );
	}

	function add_custom_nav_fields( $menu_item ) {
        global $TheGem_Mega_Menu_Columns_Values, $TheGem_Mega_Menu_Image_Position_Values, $TheGem_Mega_Menu_Default, $TheGem_Mega_Menu_Styles_Values;

        $data = get_post_meta( $menu_item->ID, '_menu_item_thegem_mega_menu', true );
        $menu_item->thegem_mega_menu = array_merge($TheGem_Mega_Menu_Default, (array) $data);

        $menu_item->thegem_mega_menu_columns_values = $TheGem_Mega_Menu_Columns_Values;
        $menu_item->thegem_mega_menu_image_position_values = $TheGem_Mega_Menu_Image_Position_Values;
        $menu_item->thegem_mega_menu_default = $TheGem_Mega_Menu_Default;
        $menu_item->thegem_mega_menu_styles_values = $TheGem_Mega_Menu_Styles_Values;

        $menu_item->thegem_mobile_clickable = get_post_meta( $menu_item->ID, '_menu_item_thegem_mobile_clickable', true );

		return $menu_item;
	}

	function update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
        global $TheGem_Mega_Menu_Columns_Values, $TheGem_Mega_Menu_Image_Position_Values, $TheGem_Mega_Menu_Default, $TheGem_Mega_Menu_Styles_Values;

        $data = get_post_meta( $menu_item_db_id, '_menu_item_thegem_mega_menu', true );
        $menu_data = array_merge($TheGem_Mega_Menu_Default, (array) $data);

        if ( isset($_REQUEST['thegem_mega_menu_icon'], $_REQUEST['thegem_mega_menu_icon'][$menu_item_db_id]) )
            $menu_data['icon'] = $_REQUEST['thegem_mega_menu_icon'][$menu_item_db_id];

        $menu_data['enable'] = isset($_REQUEST['thegem_mega_menu_enable'], $_REQUEST['thegem_mega_menu_enable'][$menu_item_db_id]);
        $menu_data['masonry'] = isset($_REQUEST['thegem_mega_menu_masonry'], $_REQUEST['thegem_mega_menu_masonry'][$menu_item_db_id]);

        if ( isset($_REQUEST['thegem_mega_menu_columns'], $_REQUEST['thegem_mega_menu_columns'][$menu_item_db_id]) ) {
            $menu_data['columns'] = absint($_REQUEST['thegem_mega_menu_columns'][$menu_item_db_id]);
            $valid_values = array_keys($TheGem_Mega_Menu_Columns_Values);
            if (!in_array($menu_data['columns'], $valid_values))
                $menu_data['columns'] = $TheGem_Mega_Menu_Default['columns'];
        }

        if ( isset($_REQUEST['thegem_mega_menu_style'], $_REQUEST['thegem_mega_menu_style'][$menu_item_db_id]) ) {
            $menu_data['style'] = $_REQUEST['thegem_mega_menu_style'][$menu_item_db_id];
            $valid_values = array_keys($TheGem_Mega_Menu_Styles_Values);
            if (!in_array($menu_data['style'], $valid_values))
                $menu_data['style'] = $TheGem_Mega_Menu_Default['style'];
        }

        if ( isset($_REQUEST['thegem_mega_menu_image'], $_REQUEST['thegem_mega_menu_image'][$menu_item_db_id]) )
            $menu_data['image'] = $_REQUEST['thegem_mega_menu_image'][$menu_item_db_id];

        if ( isset($_REQUEST['thegem_mega_menu_image_position'], $_REQUEST['thegem_mega_menu_image_position'][$menu_item_db_id]) ) {
            $menu_data['image_position'] = $_REQUEST['thegem_mega_menu_image_position'][$menu_item_db_id];
            $valid_values = array_keys($TheGem_Mega_Menu_Image_Position_Values);
            if (!in_array($menu_data['image_position'], $valid_values))
                $menu_data['image_position'] = $TheGem_Mega_Menu_Default['image_position'];
        }

        if ( isset($_REQUEST['thegem_mega_menu_width'], $_REQUEST['thegem_mega_menu_width'][$menu_item_db_id]) )
            $menu_data['width'] = absint($_REQUEST['thegem_mega_menu_width'][$menu_item_db_id]);

        $menu_data['not_link'] = isset($_REQUEST['thegem_mega_menu_not_link'], $_REQUEST['thegem_mega_menu_not_link'][$menu_item_db_id]);

        $menu_data['not_show'] = isset($_REQUEST['thegem_mega_menu_not_show'], $_REQUEST['thegem_mega_menu_not_show'][$menu_item_db_id]);

        $menu_data['new_row'] = isset($_REQUEST['thegem_mega_menu_new_row'], $_REQUEST['thegem_mega_menu_new_row'][$menu_item_db_id]);

        if ( isset($_REQUEST['thegem_mega_menu_label'], $_REQUEST['thegem_mega_menu_label'][$menu_item_db_id]) )
            $menu_data['label'] = $_REQUEST['thegem_mega_menu_label'][$menu_item_db_id];

        if ( isset($_REQUEST['thegem_mega_menu_padding_left'], $_REQUEST['thegem_mega_menu_padding_left'][$menu_item_db_id]) ) {
            $menu_data['padding_left'] = $_REQUEST['thegem_mega_menu_padding_left'][$menu_item_db_id];

            if (preg_match('%^\d+$%', $menu_data['padding_left'])) {
                $menu_data['padding_left'] .= 'px';
            }
        }

        if ( isset($_REQUEST['thegem_mega_menu_padding_right'], $_REQUEST['thegem_mega_menu_padding_right'][$menu_item_db_id]) ) {
            $menu_data['padding_right'] = $_REQUEST['thegem_mega_menu_padding_right'][$menu_item_db_id];

            if (preg_match('%^\d+$%', $menu_data['padding_right'])) {
                $menu_data['padding_right'] .= 'px';
            }
        }

        if ( isset($_REQUEST['thegem_mega_menu_padding_top'], $_REQUEST['thegem_mega_menu_padding_top'][$menu_item_db_id]) ) {
            $menu_data['padding_top'] = $_REQUEST['thegem_mega_menu_padding_top'][$menu_item_db_id];

            if (preg_match('%^\d+$%', $menu_data['padding_top'])) {
                $menu_data['padding_top'] .= 'px';
            }
        }

        if ( isset($_REQUEST['thegem_mega_menu_padding_bottom'], $_REQUEST['thegem_mega_menu_padding_bottom'][$menu_item_db_id]) ) {
            $menu_data['padding_bottom'] = $_REQUEST['thegem_mega_menu_padding_bottom'][$menu_item_db_id];

            if (preg_match('%^\d+$%', $menu_data['padding_bottom'])) {
                $menu_data['padding_bottom'] .= 'px';
            }
        }

        update_post_meta( $menu_item_db_id, '_menu_item_thegem_mega_menu', $menu_data );

        if (isset($_REQUEST['thegem_mobile_clickable'], $_REQUEST['thegem_mobile_clickable'][$menu_item_db_id]))
        	update_post_meta( $menu_item_db_id, '_menu_item_thegem_mobile_clickable', true );
        else
        	update_post_meta( $menu_item_db_id, '_menu_item_thegem_mobile_clickable', false );
	}

	function replace_walker_class( $walker, $menu_id ) {
        return 'TheGem_Edit_Mega_Menu_Walker';
	}

	/**
	 * Add some beautiful inline css for admin menus.
	 *
	 */
	function add_admin_menu_inline_css() {
		$css = '
            .wrapper-thegem-mobile-clickable {
                padding-top: 10px;
            }

            .menu.ui-sortable .thegem-megamenu-fields .fieldset-thegem-megamenu-padding {
                border: 1px solid #dfdfdf;
            }

			.menu.ui-sortable .thegem-megamenu-fields p,
            .menu.ui-sortable .thegem-megamenu-fields .fieldset-thegem-megamenu-padding {
				display: none;
			}

            .menu.ui-sortable .thegem-megamenu-fields p select {
                width: 190px;
            }

			.menu.ui-sortable .menu-item-depth-0 .thegem-megamenu-fields .field-thegem-megamenu-enable,
			.menu.ui-sortable .menu-item-depth-0.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-masonry,
            .menu.ui-sortable .menu-item-depth-0.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-columns,
            .menu.ui-sortable .menu-item-depth-0.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-style,
            .menu.ui-sortable .menu-item-depth-0.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-image,
            .menu.ui-sortable .menu-item-depth-0.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-image-position,
            .menu.ui-sortable .menu-item-depth-0.field-thegem-megamenu-enabled .thegem-megamenu-fields .fieldset-thegem-megamenu-padding,
            .menu.ui-sortable .menu-item-depth-0.field-thegem-megamenu-enabled .thegem-megamenu-fields .fieldset-thegem-megamenu-padding p {
				display: block;
			}

            .menu.ui-sortable .menu-item-depth-1.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-icon,
            .menu.ui-sortable .menu-item-depth-1.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-width,
            .menu.ui-sortable .menu-item-depth-1.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-not-link,
            .menu.ui-sortable .menu-item-depth-1.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-not-show,
            .menu.ui-sortable .menu-item-depth-1.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-new-row {
                display: block;
            }

            .menu.ui-sortable .menu-item-depth-2.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-icon,
            .menu.ui-sortable .menu-item-depth-2.field-thegem-megamenu-enabled .thegem-megamenu-fields .field-thegem-megamenu-label {
                display: block;
            }
		';
		wp_add_inline_style( 'wp-admin', $css );
	}

	/**
	 * Enqueue uploader scripts.
	 *
	 */
	function uploader_scripts() {
		if ( function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
	}

	/**
	 * Javascript magick.
	 *
	 */
	function javascript_magick() {
		?>
		<SCRIPT TYPE="text/javascript">
			jQuery(function(){

				var thegem_mega_menu = {
					reTimeout: false,

					recalc : function() {
						$menuItems = jQuery('.menu-item', '#menu-to-edit');

						$menuItems.each( function(i) {
							var $item = jQuery(this),
								$checkbox = jQuery('.thegem-edit-menu-item-icon-enable', this);

							if ( !$item.is('.menu-item-depth-0') ) {

								var checkItem = $menuItems.filter(':eq('+(i-1)+')');
								if ( checkItem.is('.field-thegem-megamenu-enabled') ) {
									$item.addClass('field-thegem-megamenu-enabled');
									$checkbox.attr('checked','checked');
								} else {
									$item.removeClass('field-thegem-megamenu-enabled');
									$checkbox.attr('checked','');
								}
							}

						});

					},

					binds: function() {

						jQuery('#menu-to-edit').on('click', '.thegem-edit-menu-item-icon-enable', function(event) {
							var $checkbox = jQuery(this),
								$container = $checkbox.closest('.menu-item');

                            if ( $checkbox.is(':checked') ) {
								$container.addClass('field-thegem-megamenu-enabled');
							} else {
								$container.removeClass('field-thegem-megamenu-enabled');
							}

							thegem_mega_menu.recalc();

							return true;
						});

					},

					init: function() {
						thegem_mega_menu.binds();
						thegem_mega_menu.recalc();

						jQuery( document ).on("mouseup", ".menu-item-bar", function(event, ui) {
							if ( !jQuery(event.target).is('a') ) {
								clearTimeout(thegem_mega_menu.reTimeout);
								thegem_mega_menu.reTimeout = setTimeout(thegem_mega_menu.recalc, 700);
							}
						});
					},


				}

				thegem_mega_menu.init();
			});
		</SCRIPT>
		<?php
	}
}

if ( !class_exists('Dt_Edit_Menu_Walker') ) {
	include_once( get_template_directory() . '/inc/megamenu//edit-megamenu-walker.class.php' );
}
