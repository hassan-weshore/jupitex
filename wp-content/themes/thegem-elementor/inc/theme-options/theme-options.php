<?php

class ThegemThemeOptions {

    const THEGEM_IMPORTER_PLUGIN = 'thegem-importer-elementor/thegem-importer.php';

    function __construct() {
        global $thegem_use_new_theme_options, $thegem_use_new_page_options;
        // hooks for pageoptions
        if ($thegem_use_new_page_options) {
            add_action('add_meta_boxes', array($this,'addPageOptionsMetaboxCallbacks'));
            add_action('save_post', array($this,'savePageOptions'));
            /*add_filter('thegem_page_title_data_defaults', array($this, 'default_page_data'), 10, 4);
            add_filter('thegem_page_header_data_defaults', array($this, 'default_page_data'), 10, 4);
            add_filter('thegem_page_effects_data_defaults', array($this, 'default_page_data'), 10, 4);
            add_filter('thegem_page_preloader_data_defaults', array($this, 'default_page_data'), 10, 4);
            add_filter('thegem_page_sidebar_data_defaults', array($this, 'default_page_data'), 10, 4);*/
            add_action('admin_menu', array($this,'addTaxonomyOptionsMetaboxCallbacks'));
            add_action('edit_term', array($this,'saveTaxonomyOptions'));
        }

        // hooks for themeoptions
        if ($thegem_use_new_theme_options) {
            add_action('admin_menu', array($this,'addAdminMenuItem'),15);
            add_action('admin_enqueue_scripts', array($this,'enqueuePageFiles'));
            add_action('wp_ajax_thegem_theme_options_api', array($this,'ajaxApi'));
            add_action('wp_ajax_thegem_theme_options_credentials', array($this,'filesystemCredentials'));
        }
    }

    function addTaxonomyOptionsMetaboxCallbacks() {
        $taxonomies = get_taxonomies(array('show_ui' => true), 'objects');
        foreach ($taxonomies as $taxonomy) {
            if($taxonomy->publicly_queryable) {
                add_action($taxonomy->name . '_edit_form', array($this,'addTaxonomyOptionsMetabox'), 15, 2);
                add_action($taxonomy->name . '_edit_form_fields','thegem_taxonomy_edit_form_fields', 15);
            }
        }
    }

    function addPageOptionsMetaboxCallbacks() {
        $post_types = array_merge(array('post', 'page', 'thegem_pf_item', 'thegem_news', 'product'), thegem_get_available_po_custom_post_types());
        foreach ($post_types as $post_type) {
            add_meta_box('thegem_page_options', esc_html__('Page Options', 'thegem'), array($this,'addPageOptionsMetabox'), $post_type, 'normal', 'high');
        }
    }

    function getTaxonomyOptions($post) {
        $page_data = array();

/*        $type = 'term';

        if(get_term_meta($post->term_id, 'thegem_page_data', true)) {
            $page_data = thegem_get_sanitize_admin_page_data($post->term_id, array(), $type);
        } else {
            $page_data_default = thegem_theme_options_get_page_settings('blog');
            $page_data = thegem_get_sanitize_admin_page_data($page_data_default);
            if($page_data_default['title_style'] != 2) {
              $page_data['title_template'] = 0;
            }
            if(!$page_data_default['footer_custom_show']) {
              $page_data['footer_custom'] = 0;
            }
        }*/

        $page_data = thegem_get_sanitize_admin_page_data($post->term_id, array(), 'term');

        return $page_data;
    }

    function addTaxonomyOptionsMetabox($tag, $taxonomy) {
		echo '<div class="postbox taxonomy-box" id="thegem_taxonomy_custom_page_options_boxes2" style="display:none">';
		echo '<h3 class="hndle">' . __('Custom Page Options', 'thegem') . '</h3>';
		echo '<div class="inside">';

        $this->renderPageOptions();

        echo '</div>';
		echo '</div>';

        $appData = array(
            'isTaxonomy' => true,
            'galleries' => $this->getGalleriesList(),
            'sliders' => $this->getSliders(),
            'options' => $this->getTaxonomyOptions($tag, $taxonomy),
            'taxonomy' => $taxonomy,
            'menus' => $this->getMenus(),
            'patternsUrl' => get_template_directory_uri().'/images/backgrounds/patterns/',
            'iconPacks' =>  $this->getIconPacks(),
        );

        $this->addAppData($appData);
    }

    function addPageOptionsMetabox($post, $type = false) {
        $this->renderPageOptions();

        $appData = array(
            'isPage' => true,
            'galleries' => $this->getGalleriesList(),
            'sliders' => $this->getSliders(),
            'postType' => $post->post_type,
            'options' => $this->getPageOptions($post, $type),
            'menus' => $this->getMenus(),
            'patternsUrl' => get_template_directory_uri().'/images/backgrounds/patterns/',
            'iconPacks' =>  $this->getIconPacks(),

        );

        $this->addAppData($appData);
    }

    function addAdminMenuItem() {
        add_menu_page(esc_html__('TheGem','thegem'), esc_html__('TheGem','thegem'), 'edit_theme_options', 'thegem-dashboard-welcome', [$this,'renderDashboardPage'], '', '3.1');
        add_submenu_page('thegem-dashboard-welcome',esc_html__('TheGem Dashboard','thegem'), esc_html__('Dashboard','thegem'), 'edit_theme_options', 'thegem-dashboard-welcome', [$this,'renderDashboardPage'], 10);
        add_submenu_page('thegem-dashboard-welcome',esc_html__('TheGem Theme Options','thegem'), esc_html__('Theme Options','thegem'), 'edit_theme_options', 'thegem-theme-options', [$this,'renderThemeOptions'], 20);
       //add_submenu_page('thegem-theme-options',esc_html__('TheGem Dashboard','thegem'), esc_html__('D - Plugins','thegem'), 'edit_theme_options', 'thegem-dashboard-plugins', [$this,'renderDashboardPage']);
        //add_submenu_page('thegem-theme-options',esc_html__('TheGem Dashboard','thegem'), esc_html__('D - Demo Import','thegem'), 'edit_theme_options', 'thegem-dashboard-demoimport', [$this,'renderDashboardPage']);
        //add_submenu_page('thegem-theme-options',esc_html__('TheGem Dashboard','thegem'), esc_html__('D - Manual & Support','thegem'), 'edit_theme_options', 'thegem-dashboard-manual-and-support', [$this,'renderDashboardPage']);
        add_submenu_page('thegem-dashboard-welcome',esc_html__('TheGem Dashboard','thegem'), esc_html__('System Status','thegem'), 'edit_theme_options', 'thegem-dashboard-system-status', [$this,'renderdashboardPage'], 50);
        add_submenu_page('thegem-dashboard-welcome',esc_html__('TheGem Dashboard','thegem'), esc_html__('Changelog','thegem'), 'edit_theme_options', 'thegem-dashboard-changelog', [$this,'renderDashboardPage'], 70);
        add_submenu_page(null, esc_html__('TheGem Importer','thegem'), esc_html__('TheGem Importer','thegem'), 'edit_theme_options', 'thegem-dashboard-importer', [$this,'renderDashboardPage'], 20);
    }

    function renderDashboardPage() {
        echo '<div id="thegem-themeoptions"></div>';
    }

    function renderThemeOptions() {
        echo '<div id="thegem-themeoptions"></div>';
    }

    function renderPageOptions() {
        wp_nonce_field('thegem_page_options_data', 'thegem_page_options_data_nonce');
        echo '<input type="hidden" name="thegem_page_options_data" id="thegem_page_options_data"/><div id="thegem-themeoptions"></div>';
    }

    function getSettings() {
        $settings = get_option('thegem_theme_options_settings');
        if (!$settings) {
            $settings = array (
                'theme' => 'light',
                'background_image_gallery' => array(),
                'colorpicker_favorites' => array('default'=>array())
            );
        }

        return $settings;
    }

    function apiSaveSettings($request) {
        update_option('thegem_theme_options_settings',$request['settings']);
    }

    function saveTaxonomyOptions($term_id) {
        if (!isset($_POST['thegem_page_options_data_nonce']) ||
            !wp_verify_nonce($_POST['thegem_page_options_data_nonce'], 'thegem_page_options_data')) {
            return;
        }


        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_term', $term_id)) {
            return;
        }

        $options=json_decode(stripslashes_deep($_POST['thegem_page_options_data']),true);

        $page_data = thegem_get_sanitize_admin_page_data(0, $options);

        update_term_meta($term_id, 'thegem_taxonomy_custom_page_options', !empty($_POST['thegem_taxonomy_custom_page_options']));
        update_term_meta($term_id, 'thegem_page_data', $page_data);
    }

    function savePageOptions($post_id) {

        if (!isset($_POST['thegem_page_options_data_nonce']) ||
            !wp_verify_nonce($_POST['thegem_page_options_data_nonce'], 'thegem_page_options_data')) {
            return;
        }


        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(isset($_POST['post_type']) && in_array($_POST['post_type'], array_merge(array('post', 'page', 'thegem_pf_item', 'thegem_news', 'product'), thegem_get_available_po_custom_post_types()))) {
            if(!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if(!current_user_can('edit_post', $post_id)) {
                return;
            }
        }

        $options=json_decode(stripslashes_deep($_POST['thegem_page_options_data']),true);

        $post = get_post($post_id);

        switch ($post->post_type) {
            case 'post':
                update_post_meta($post_id, 'thegem_post_general_item_data', $options['post_item_data']);
                update_post_meta($post_id, 'thegem_show_featured_posts_slider', $options['post_item_data']['show_featured_posts_slider']);
                unset($options['post_item_data']);

                $data = thegem_get_sanitize_admin_post_elements_data($post_id, $options['post_elements_data']);
                update_post_meta($post_id, 'thegem_post_page_elements_data', $data);
                unset($options['post_elements_data']);
                break;

            case 'thegem_pf_item':
                $portfolio_item_data = thegem_get_sanitize_pf_item_data(0, $options['portfolio_item_data']);
                update_post_meta($post_id, 'thegem_portfolio_item_data', $portfolio_item_data);
                unset($options['portfolio_item_data']);

                $data = thegem_get_sanitize_pf_item_elements_data($post_id, $options['portfolio_elements_data']);
                update_post_meta($post_id, 'thegem_pf_item_page_elements_data', $data);
                unset($options['portfolio_elements_data']);
                break;

            case 'product':
                $product_item_data = thegem_get_sanitize_pf_item_data(0, $options['product_item_data']);

                $size_guide = thegem_get_sanitize_product_size_guide_data(0, array(
                    'size_guide' => $product_item_data['size_guide'],
                    'custom_image' => $product_item_data['size_guide_image'],
                ));
                update_post_meta($post_id, 'thegem_product_size_guide_data', $size_guide);
                $highlight = thegem_get_sanitize_product_featured_data(0, array(
                    'highlight' => $product_item_data['highlight'],
                    'highlight_type' => $product_item_data['highlight_type'],
                ));
                update_post_meta($post_id, 'thegem_product_featured_data', $highlight);
                update_post_meta($post_id, 'thegem_product_disable_hover', !empty($product_item_data['thegem_product_disable_hover']));

                $product_page = thegem_get_sanitize_product_page_data(0, array(
                    'product_layout_settings' => $product_item_data['product_layout_settings'],

                    'product_gallery' => $product_item_data['product_gallery'],
                    'product_gallery_type' => $product_item_data['product_gallery_type'],
                    'product_gallery_column_position' => $product_item_data['product_gallery_column_position'],
                    'product_gallery_column_width' => $product_item_data['product_gallery_column_width'],
                    'product_gallery_show_image' => $product_item_data['product_gallery_show_image'],
                    'product_gallery_zoom' => $product_item_data['product_gallery_zoom'],
                    'product_gallery_lightbox' => $product_item_data['product_gallery_lightbox'],
                    'product_gallery_labels' => $product_item_data['product_gallery_labels'],
                    'product_gallery_label_sale' => $product_item_data['product_gallery_label_sale'],
                    'product_gallery_label_new' => $product_item_data['product_gallery_label_new'],
                    'product_gallery_label_out_stock' => $product_item_data['product_gallery_label_out_stock'],
                    'product_gallery_auto_height' => $product_item_data['product_gallery_auto_height'],
                    'product_gallery_elements_color' => $product_item_data['product_gallery_elements_color'],

                    'product_page_layout' => $product_item_data['product_page_layout'],
                    'product_page_layout_style' => $product_item_data['product_page_layout_style'],
                    'product_page_layout_preset' => $product_item_data['product_page_layout_preset'],
                    'product_page_layout_fullwidth' => $product_item_data['product_page_layout_fullwidth'],
                    'product_page_layout_sticky' => $product_item_data['product_page_layout_sticky'],
                    'product_page_layout_title_area' => $product_item_data['product_page_layout_title_area'],
                    'product_page_ajax_add_to_cart' => $product_item_data['product_page_ajax_add_to_cart'],
                    'product_page_desc_review_source' => $product_item_data['product_page_desc_review_source'],
                    'product_page_desc_review_layout' => $product_item_data['product_page_desc_review_layout'],
                    'product_page_desc_review_layout_tabs_style' => $product_item_data['product_page_desc_review_layout_tabs_style'],
                    'product_page_desc_review_layout_tabs_alignment' => $product_item_data['product_page_desc_review_layout_tabs_alignment'],
                    'product_page_desc_review_layout_acc_position' => $product_item_data['product_page_desc_review_layout_acc_position'],
                    'product_page_desc_review_layout_one_by_one_description_background' => $product_item_data['product_page_desc_review_layout_one_by_one_description_background'],
                    'product_page_desc_review_layout_one_by_one_additional_info_background' => $product_item_data['product_page_desc_review_layout_one_by_one_additional_info_background'],
                    'product_page_desc_review_layout_one_by_one_reviews_background' => $product_item_data['product_page_desc_review_layout_one_by_one_reviews_background'],
                    'product_page_desc_review_description' => $product_item_data['product_page_desc_review_description'],
                    'product_page_desc_review_description_title' => $product_item_data['product_page_desc_review_description_title'],
                    'product_page_desc_review_additional_info' => $product_item_data['product_page_desc_review_additional_info'],
                    'product_page_desc_review_additional_info_title' => $product_item_data['product_page_desc_review_additional_info_title'],
                    'product_page_desc_review_reviews' => $product_item_data['product_page_desc_review_reviews'],
                    'product_page_desc_review_reviews_title' => $product_item_data['product_page_desc_review_reviews_title'],
                    'product_page_button_add_to_cart_text' => $product_item_data['product_page_button_add_to_cart_text'],
                    'product_page_button_add_to_cart_icon' => $product_item_data['product_page_button_add_to_cart_icon'],
                    'product_page_button_add_to_cart_icon_pack' => $product_item_data['product_page_button_add_to_cart_icon_pack'],
                    'product_page_button_add_to_cart_icon_position' => $product_item_data['product_page_button_add_to_cart_icon_position'],
                    'product_page_button_add_to_cart_border_width' => $product_item_data['product_page_button_add_to_cart_border_width'],
                    'product_page_button_add_to_cart_border_radius' => $product_item_data['product_page_button_add_to_cart_border_radius'],
                    'product_page_button_add_to_cart_color' => $product_item_data['product_page_button_add_to_cart_color'],
                    'product_page_button_add_to_cart_color_hover' => $product_item_data['product_page_button_add_to_cart_color_hover'],
                    'product_page_button_add_to_cart_background' => $product_item_data['product_page_button_add_to_cart_background'],
                    'product_page_button_add_to_cart_background_hover' => $product_item_data['product_page_button_add_to_cart_background_hover'],
                    'product_page_button_add_to_cart_border_color' => $product_item_data['product_page_button_add_to_cart_border_color'],
                    'product_page_button_add_to_cart_border_color_hover' => $product_item_data['product_page_button_add_to_cart_border_color_hover'],
                    'product_page_button_add_to_wishlist_icon' => $product_item_data['product_page_button_add_to_wishlist_icon'],
                    'product_page_button_add_to_wishlist_icon_pack' => $product_item_data['product_page_button_add_to_wishlist_icon_pack'],
                    'product_page_button_add_to_wishlist_color' => $product_item_data['product_page_button_add_to_wishlist_color'],
                    'product_page_button_add_to_wishlist_color_hover' => $product_item_data['product_page_button_add_to_wishlist_color_hover'],
                    'product_page_button_add_to_wishlist_color_filled' => $product_item_data['product_page_button_add_to_wishlist_color_filled'],
                    'product_page_button_added_to_wishlist_icon' => $product_item_data['product_page_button_added_to_wishlist_icon'],
                    'product_page_button_added_to_wishlist_icon_pack' => $product_item_data['product_page_button_added_to_wishlist_icon_pack'],
                    'product_page_button_clear_attributes_text' => $product_item_data['product_page_button_clear_attributes_text'],
                    'product_page_elements_prev_next' => $product_item_data['product_page_elements_prev_next'],
                    'product_page_elements_preview_on_hover' => $product_item_data['product_page_elements_preview_on_hover'],
                    'product_page_elements_back_to_shop' => $product_item_data['product_page_elements_back_to_shop'],
                    'product_page_elements_back_to_shop_link' => $product_item_data['product_page_elements_back_to_shop_link'],
                    'product_page_elements_back_to_shop_link_custom_url' => $product_item_data['product_page_elements_back_to_shop_link_custom_url'],
                    'product_page_elements_title' => $product_item_data['product_page_elements_title'],
                    'product_page_elements_attributes' => $product_item_data['product_page_elements_attributes'],
                    'product_page_elements_attributes_data' => $product_item_data['product_page_elements_attributes_data'],
                    'product_page_elements_reviews' => $product_item_data['product_page_elements_reviews'],
                    'product_page_elements_reviews_text' => $product_item_data['product_page_elements_reviews_text'],
                    'product_page_elements_price' => $product_item_data['product_page_elements_price'],
                    'product_page_elements_price_strikethrough' => $product_item_data['product_page_elements_price_strikethrough'],
                    'product_page_elements_description' => $product_item_data['product_page_elements_description'],
                    'product_page_elements_stock_amount' => $product_item_data['product_page_elements_stock_amount'],
                    'product_page_elements_stock_amount_text' => $product_item_data['product_page_elements_stock_amount_text'],
                    'product_page_elements_size_guide' => $product_item_data['product_page_elements_size_guide'],
                    'product_page_elements_sku' => $product_item_data['product_page_elements_sku'],
                    'product_page_elements_sku_title' => $product_item_data['product_page_elements_sku_title'],
                    'product_page_elements_categories' => $product_item_data['product_page_elements_categories'],
                    'product_page_elements_categories_title' => $product_item_data['product_page_elements_categories_title'],
                    'product_page_elements_tags' => $product_item_data['product_page_elements_tags'],
                    'product_page_elements_tags_title' => $product_item_data['product_page_elements_tags_title'],
                    'product_page_elements_share' => $product_item_data['product_page_elements_share'],
                    'product_page_elements_share_title' => $product_item_data['product_page_elements_share_title'],
                    'product_page_elements_upsell' => $product_item_data['product_page_elements_upsell'],
                    'product_page_elements_upsell_title' => $product_item_data['product_page_elements_upsell_title'],
                    'product_page_elements_upsell_items' => $product_item_data['product_page_elements_upsell_items'],
                    'product_page_elements_upsell_columns_desktop' => $product_item_data['product_page_elements_upsell_columns_desktop'],
                    'product_page_elements_upsell_columns_tablet' => $product_item_data['product_page_elements_upsell_columns_tablet'],
                    'product_page_elements_upsell_columns_mobile' => $product_item_data['product_page_elements_upsell_columns_mobile'],
                    'product_page_elements_upsell_columns_100' => $product_item_data['product_page_elements_upsell_columns_100'],
                    'product_page_elements_related' => $product_item_data['product_page_elements_related'],
                    'product_page_elements_related_title' => $product_item_data['product_page_elements_related_title'],
                    'product_page_elements_related_items' => $product_item_data['product_page_elements_related_items'],
                    'product_page_elements_related_columns_desktop' => $product_item_data['product_page_elements_related_columns_desktop'],
                    'product_page_elements_related_columns_tablet' => $product_item_data['product_page_elements_related_columns_tablet'],
                    'product_page_elements_related_columns_mobile' => $product_item_data['product_page_elements_related_columns_mobile'],
                    'product_page_elements_related_columns_100' => $product_item_data['product_page_elements_related_columns_100'],
                ));
                update_post_meta($post_id, 'thegem_product_page_data', $product_page);

                unset($options['product_item_data']);
                break;
        }

        $page_data = thegem_get_sanitize_admin_page_data(0, $options);

        update_post_meta($post_id, 'thegem_page_data', $page_data);

    }

    function getBackupsInfo() {
        $backups=get_option('thegem_theme_options_backup2');

        $info=array();
        if ($backups) {
            foreach($backups as $idx=>$backup) {
                array_push($info,array('id'=>$idx,'dt'=>date('Y-m-d H:i', $backup['dt'])));
            }
        }

        return $info;
    }

    function processPageFileUrl($url) {
        return get_template_directory_uri().'/'.$url;
    }

    function apiPurgeThumbnailsCache() {
        global $wpdb;
        $prefix = thegem_get_image_cache_option_key_prefix();
        $wpdb->query("DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '%{$prefix}%'");

        return array();
    }

    function apiActivate($request) {
        delete_option('thegem_activation');
        if(!empty($request['purchaseCode'])) {
            $theme_options = get_option('thegem_theme_options');
            $theme_options['purchase_code'] = $request['purchaseCode'];
            update_option('thegem_theme_options', $theme_options);
            thegem_get_option(false, false, false, true);
            $response_p = wp_remote_get(add_query_arg(array('code' => $request['purchaseCode'], 'info'=>thegem_get_activation_info(), 'site_url' => get_site_url(), 'type' => 'elementor'), esc_url('http://democontent.codex-themes.com/av_validate_code.php')), array('timeout' => 20));

            if(is_wp_error($response_p)) {
                return array('activated' => false, 'error' => __('Some troubles with connecting to TheGem server.', 'thegem'));
            } else {
                $rp_data = json_decode($response_p['body'], true);
                if(is_array($rp_data) && isset($rp_data['result']) && $rp_data['result'] && isset($rp_data['item_id']) && $rp_data['item_id'] === '16061685') {
                    update_option('thegem_activation', 1);
                    update_option('thegem_print_google_code', 1);
                    return array('activated' => true);
                } else {
                    return array('activated' => false, 'error' => isset($rp_data['message']) ? $rp_data['message'] : __('The purchase code you have entered is not valid. TheGem has not been activated.', 'thegem'));
                }
            }
        } else {
            return array('activated' => false, 'error' => __('Purchase code is empty. (E01)', 'thegem'));
        }
    }

    function apiBackup() {
        $backups=get_option('thegem_theme_options_backup2');

        if (!$backups) {
            $backups=array();
        }

        array_unshift($backups,array('dt'=>time(),'data'=>$this->getFullOptions()));
        $backups=array_slice($backups,0,5);

        update_option('thegem_theme_options_backup2',$backups);

        return array('backups'=>$this->getBackupsInfo());
    }

    function apiRegenerateCss($request) {
        $_POST=array_merge($_POST,$request['credentials']);

        $cssResult=$this->thegem_generate_custom_css();

        if ($cssResult!==true) {
            return array('status'=>$cssResult);
        };

        return array();
    }

    function apiRegenerateEmptyCss($request) {
        $_POST=array_merge($_POST,$request['credentials']);

        $thegem_theme = wp_get_theme(wp_get_theme()->get('Template'));
        update_option('thegem_generate_empty_css_forced_redirect_done',$thegem_theme->get('Version'));

        $cssResult=$this->thegem_generate_custom_css(_('Custom.css file is missing in your TheGem installation. Custom.css is important for proper functioning of TheGem. Please regenerate it now. All your settings will remain, this action will not affect your setup.'));

        if ($cssResult!==true) {
            return array('status'=>$cssResult);
        };

        return array();
    }

    function apiRestore($request) {
        $backups=get_option('thegem_theme_options_backup2');

        $_POST=array_merge($_POST,$request['credentials']);

        $backup=$backups[$request['id']]['data'];
        if ($backup) {
            $page_options=$backup['page_options'];
            unset($backup['page_options']);

            thegem_check_activation($backup);
            update_option('thegem_theme_options', $backup);
            foreach($page_options as $page=>$options) {
                thegem_theme_options_set_page_settings($page, $options);
            }

            delete_option( 'rewrite_rules' );

            thegem_get_option(false, false, false, true);
            $cssResult=$this->thegem_generate_custom_css();

            if ($cssResult!==true) {
                return array('status'=>$cssResult);
            };
        }

        return array('options'=>$this->getFullOptions());
    }

    function apiSave($request) {
        $theme_options = $request['options'];
        unset($theme_options['page_options']);

        $_POST=array_merge($_POST,$request['credentials']);

        if(thegem_get_current_language()) {
            $ml_options = thegem_translated_options();
            foreach ($ml_options as $ml_option) {
                $value = thegem_get_option($ml_option, false, true);
                if(!is_array($value)) {
                    if(thegem_get_default_language()) {
                        $value = array(thegem_get_default_language() => $value);
                    }
                }
                $value[thegem_get_current_language()] = $theme_options[$ml_option];
                $theme_options[$ml_option] = $value;
            }
        }

        thegem_check_activation($theme_options);

        // save socials
        $customSocials = array();
        foreach($theme_options['customSocials'] as $social) {
            $saveSocial = array(
                'id' => sanitize_title($social['id']),
                'name' => !empty($social['name']) ? esc_html($social['name']) : '',
                'icon_pack' => !empty($social['icon_pack']) ? thegem_check_array_value(array('elegant', 'material', 'fontawesome', 'userpack'), $social['icon_pack'], 'elegant') : 'elegant',
                'icon' => !empty($social['icon']) ? esc_html($social['icon']) : '',
                'rounded_icon' => !empty($social['rounded_icon']) ? esc_html($social['rounded_icon']) : '',
                'squared_icon' => !empty($social['squared_icon']) ? esc_html($social['squared_icon']) : '',
                'color' => !empty($social['color']) ? esc_html($social['color']) : '',
            );

            if (!$saveSocial['id'] && trim($social['name'])!='') {
                $saveSocial['id']='social-'.preg_replace('%[^a-z0-9]%','',strtolower(trim($social['name']))).'-'.time();
            }

            if ($saveSocial['id']) {
                $customSocials[] = $saveSocial;
                $theme_options[$saveSocial['id'].'_active'] = $social['active'];
                $theme_options[$saveSocial['id'].'_link'] = $social['link'];
            }
        }
        unset($theme_options['customSocials']);
        update_option('thegem_additionals_socials', $customSocials);

        update_option('thegem_theme_options', $theme_options);

        foreach($request['options']['page_options'] as $page=>$options) {
            thegem_theme_options_set_page_settings($page, $options);
        }

        delete_option( 'rewrite_rules' );

        thegem_get_option(false, false, false, true);
        $cssResult=$this->thegem_generate_custom_css();

        if ($cssResult!==true) {
            return array('status'=>$cssResult);
        };

        return array();
    }

    function applyPageSettings($request) {
        $type = $request['type'];
        $offset = $request['offset'];

        switch ($type) {
            case 'default':
                $typeName = 'Page';
                $offset = thegem_apply_options_page_settings('page', thegem_theme_options_get_page_settings('default'), $offset);
                break;
            case 'post':
                $typeName = 'Post';
                $offset = thegem_apply_options_page_settings('post', thegem_theme_options_get_page_settings('post'), $offset);
                break;
            case 'portfolio':
                $typeName = 'Portfolio';
                $offset = thegem_apply_options_page_settings('thegem_pf_item', thegem_theme_options_get_page_settings('portfolio'), $offset);
                break;
            case 'product':
                $typeName = 'Product';
                $offset = thegem_apply_options_page_settings('product', thegem_theme_options_get_page_settings('product'), $offset);
                break;
            case 'product_layout':
                $typeName = 'Product layout';
                $offset = thegem_apply_options_page_settings('product_layout', thegem_theme_options_get_page_settings('product_layout'), $offset);
                break;
            case 'product_categories':
                $typeName = 'Product categories';
                $offset = thegem_apply_options_page_settings('product_cats', thegem_theme_options_get_page_settings('product_categories'), $offset);
                break;
            case 'blog':
                $typeName = 'Blog categories';
                $offset = thegem_apply_options_page_settings('cats', thegem_theme_options_get_page_settings('blog'), $offset);
                break;
        }

        $data = array ( 'status' => true, 'offset'=> $offset);
        if (!$offset) {
            $data['message'] = __($typeName.' '.'settings applied successfully.', 'thegem');
        }

        return $data;
    }

    function ajaxApi() {
        $request = json_decode(file_get_contents('php://input'), true);

        switch($request['action']) {
            case 'save':
                $response = $this->apiSave($request);
                break;
            case 'saveSettings':
                $response = $this->apiSaveSettings($request);
                break;
            case 'purgeThumbnailsCache':
                $response = $this->apiPurgeThumbnailsCache();
                break;
            case 'backup':
                $response = $this->apiBackup($request);
                break;
            case 'restore':
                $response = $this->apiRestore($request);
                break;
            case 'regenerateCss':
                $response = $this->apiRegenerateCss($request);
                break;
            case 'regenerateEmptyCss':
                $response = $this->apiRegenerateEmptyCss($request);
                break;
            case 'activate':
                $response = $this->apiActivate($request);
                break;
            case 'applyPageSettings':
                $response = $this->applyPageSettings($request);
                break;
                default:
                $response['status'] = false;

        }

        if (!isset($response['status'])) {
            $response['status'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    function getFullOptions() {
        $options = get_option('thegem_theme_options');

        $ml_options = thegem_translated_options();
        foreach ($ml_options as $ml_option) {
            if(is_array($options[$ml_option])) {
                if(thegem_get_current_language()) {
                    if(isset($options[$ml_option][thegem_get_current_language()])) {
                        $options[$ml_option] = $options[$ml_option][thegem_get_current_language()];
                    } elseif(thegem_get_default_language() && isset($options[$ml_option][thegem_get_default_language()])) {
                        $options[$ml_option] = $options[$ml_option][thegem_get_default_language()];
                    } else {
                        $options[$ml_option] = '';
                    }
                } else {
                    $options[$ml_option] = reset($options[$ml_option]);
                }
            }
        }

        $socials = get_option('thegem_additionals_socials');
        if (!$socials) {
            $socials = array();
        }

        $socials = array_values($socials);

        foreach($socials as $k=>$v) {
            $socials[$k]['active']=isset($options[$v['id'].'_active']) && $options[$v['id'].'_active'] ? '1' : '';
            $socials[$k]['link']=isset($options[$v['id'].'_link']) && $options[$v['id'].'_link'] ? $options[$v['id'].'_link'] : '';
            $socials[$k]['key']=$k;
        }

        $options['customSocials'] = $socials;

        return array_merge(
            $options,
            array(
                'page_options'=>array(
                    'default'=>thegem_theme_options_get_page_settings('default'),
                    'blog'=>thegem_theme_options_get_page_settings('blog'),
                    'search'=>thegem_theme_options_get_page_settings('search'),
                    'global'=>thegem_theme_options_get_page_settings('global'),
                    'post'=>thegem_theme_options_get_page_settings('post'),
                    'portfolio'=>thegem_theme_options_get_page_settings('portfolio'),
                    'product'=>thegem_theme_options_get_page_settings('product'),
                    'product_categories'=>thegem_theme_options_get_page_settings('product_categories'),
                ),
            )
        );
    }

    function getCustomFooters() {
        $data = array(array('value' => '0', 'label' => __('Please select', 'thegem'), 'disabled' => true));

        foreach(thegem_get_footers_list() as $k=>$v) {
            $data[] = array( 'value' => strval($k), 'label' => $v, 'preview' => $k ? get_permalink($k) : null);
        }

        return $data;
    }

    function getWCAttributes() {
        global $wpdb;
        $data = array(array('value' => '0', 'label' => __('Please select', 'thegem'), 'disabled' => true));

        if (class_exists('WooCommerce')) {
            $table_name = $wpdb->prefix . "woocommerce_attribute_taxonomies";
            $query_results = $wpdb->get_results("SELECT * FROM $table_name order by attribute_label");
            foreach($query_results as $query_result) {
                $data[] = array('value' => $query_result->attribute_name, 'label' => $query_result->attribute_label);
            }
        }

        return $data;
    }

    function getPagesList() {
        $data = array();

        foreach(thegem_get_pages_list() as $k=>$v) {
            $data[] = array( 'value' => strval($k), 'label' => $v, 'preview' => get_permalink($k));
        }

        return $data;
    }

    function getParentPagesForPortfolioList() {
        $pages = array( array( 'value' => '' , 'label' => __('Default', 'thegem')));
        $pages_list = get_pages();
        foreach ($pages_list as $page) {
            $pages[] = array( 'value' => strval($page->ID), 'label' => $page->post_title . ' (ID = ' . $page->ID . ')');
        }

        return $pages;
    }

    function getCustomTitles() {
        $data = array(array('value' => '0', 'label' => __('Please select', 'thegem'), 'disabled' => true));

        foreach(thegem_get_titles_list() as $k=>$v) {
            $data[] = array( 'value' => strval($k), 'label' => $v, 'preview' => get_permalink($k));
        }

        return $data;
    }

    function getGalleriesList() {
        $list = get_posts(array(
            'post_type' => 'thegem_gallery',
            'numberposts' => -1,
            'post_status' => 'any'
        ));

        $galleries = array(array('value' => 0, 'label' => __('Please select', 'thegem'), 'disabled' => true));
        foreach ($list as $gallery) {
        $galleries[] = array( 'value' => $gallery->ID, 'label' => $gallery->post_title . ' (ID = ' . $gallery->ID . ')'/*, 'preview' => get_permalink($gallery->ID)*/);
        }

        return $galleries;
    }

    function getSliders() {
        $data = array(
            'types' => array( array('value' => '', 'label' => __('None', 'thegem'))),
            'slideshows' => array( array('value' => '', 'label' => __('All Slides', 'thegem'))),
            'layersliders' => array(),
            'revsliders' => array(),
        );

        if(thegem_get_option('activate_nivoslider')) {
            $data['types'][] = array ('value' => 'NivoSlider', 'label' => 'NivoSlider');
            $slideshows_terms = get_terms('thegem_slideshows', array('hide_empty' => false));
            foreach($slideshows_terms as $type) {
                $data['slideshows'][] = array ( 'value' => $type->slug, 'label' => $type->name);
            }
        };

        if(thegem_is_plugin_active('LayerSlider/layerslider.php')) {
            $data['types'][] = array ('value' => 'LayerSlider', 'label' => 'LayerSlider');
            global $wpdb;
            $table_name = $wpdb->prefix . "layerslider";
            $query_results = $wpdb->get_results("SELECT * FROM $table_name WHERE flag_hidden = '0' AND flag_deleted = '0' ORDER BY id ASC");
            foreach($query_results as $query_result) {
                $data['layersliders'][] = array ( 'value' => $query_result->id, 'label' => $query_result->name);
            }
        }

        if(thegem_is_plugin_active('revslider/revslider.php')) {
            $data['types'][] = array ('value' => 'revslider', 'label' => 'Revolution Slider');
            $slider = new RevSlider();
            $arrSliders = $slider->getArrSliders();
            foreach($arrSliders as $arrSlider) {
                $data['revsliders'][] = array ( 'value' => $arrSlider->getAlias(), 'label' => $arrSlider->getTitle());
            }
        }

        return $data;
    }

    function getMenus() {
        $data = array(array('label' => __('Default Menu', 'thegem'), 'value'=> 0));

        $menus = wp_get_nav_menus();
        foreach($menus as $item) {
            $data[] = array('label' => $item->name, 'value' => $item->term_id);
        }

        return $data;
    }

    function getIconPacks() {
        $iconPacks=$this->getIconPacksInfo();
        foreach($iconPacks as $k=>$v) {
            wp_add_inline_style('thegem-theme-options-0',$v['fontFaces']);
            unset($iconPacks[$k]['fontFaces']);
        }

        return $iconPacks;
    }

    function getCustomPostTypes() {
        $data = array();

        $postTypes = thegem_get_list_po_custom_post_types(false);
        foreach($postTypes as $k => $v) {
            $data[] = array('label' => $v, 'value' => $k);
        }

        return $data;
    }

    function cutSchemeFromUrl($url) {
        return preg_replace('%^[^:]+://%', '//', $url);
    }

    function addAppData($specificData) {

        wp_enqueue_media();
        wp_enqueue_editor();

        $content=file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR . 'index.html');

        preg_match_all('%<link[^>]*?([^"=]*\.css)%',$content,$m);
        foreach(array_unique($m[1]) as $idx=>$styleFile) {
            wp_enqueue_style("thegem-theme-options-$idx",$this->processPageFileUrl($styleFile));
        }

        wp_enqueue_style('icons-elegant', get_template_directory_uri() . '/css/icons-elegant.css');
        wp_enqueue_style('icons-material', get_template_directory_uri() . '/css/icons-material.css');
        wp_enqueue_style('icons-fontawesome', get_template_directory_uri() . '/css/icons-fontawesome.css');

        preg_match_all('%<script[^>]*?([^"=]*\.js)%',$content,$m);
        foreach($m[1] as $idx=>$scriptFile) {
            wp_enqueue_script("thegem-theme-options-$idx",$this->processPageFileUrl($scriptFile),array(),false,true);
        }

        $thegem_theme = wp_get_theme(wp_get_theme()->get('Template'));

        $appData=array_merge( array(
            'version' => $thegem_theme->get('Version'),
            'appUrl' => get_template_directory_uri().'/inc/theme-options/dist/',
            'apiUrl' => admin_url( 'admin-ajax.php' ).'?action=thegem_theme_options_api',
            'styleEditorCssUrl' => get_template_directory_uri().'/css/style-editor.css',
            'adminUrl' => admin_url(),
            'homeUrl' => $this->cutSchemeFromUrl(trailingslashit(get_home_url())),
            'i18n' => array(
                'locale' => get_bloginfo('language'),
                'messages' => require('messages.php')
            ),
            'customFooters' => $this->getCustomFooters(),
            'WCAttributes' => $this->getWCAttributes(),
            'customTitles' => $this->getCustomTitles(),
            'customPostTypes' => $this->getCustomPostTypes(),
            'wysiwygFormats' => $this->getWysiwygFormats(),
            'settings' => $this->getSettings(),
            'presetsUrl' => get_template_directory_uri().'/images/backgrounds/presets/',
        ), $specificData);

        wp_localize_script("thegem-theme-options-0", 'theme_options_app_data', $appData);
    }

    function getWysiwygFormats() {
        $settings = thegem_init_editor(array());
        return json_decode($settings['style_formats'], true);
    }

    function getPageOptions($post, $type) {
        $page_data = array();
        /*if($type === 'term') {
            if(get_term_meta($post->term_id, 'thegem_page_data', true)) {
                $page_data = thegem_get_sanitize_admin_page_data($post->term_id, array(), $type);
            } else {
                $page_data = thegem_get_sanitize_admin_page_data(0, thegem_theme_options_get_page_settings('blog'), 'blog');
            }
        } elseif(in_array($type, array('default', 'blog', 'search'))) {
            $page_data = thegem_get_sanitize_admin_page_data(0, thegem_theme_options_get_page_settings($type), $type);
        } else {
            if(get_post_meta($post->ID, 'thegem_page_data', true)) {
                $page_data = thegem_get_sanitize_admin_page_data($post->ID);
            } else {
                $page_data_defaut = thegem_theme_options_get_page_settings('default');
                $page_data =thegem_get_sanitize_admin_page_data(0, $page_data_defaut, 'default');
                if($page_data_defaut['title_style'] != 2) {
                  $page_data['title_template'] = 0;
                }
                if(!$page_data_defaut['footer_custom_show']) {
                  $page_data['footer_custom'] = 0;
                }
            }
        }*/
        $page_data = thegem_get_sanitize_admin_page_data($post->ID);

        switch ($post->post_type) {
            case 'post':
                $page_data['post_item_data'] = thegem_get_sanitize_admin_post_data($post->ID);
                $page_data['post_elements_data'] = thegem_get_sanitize_admin_post_elements_data($post->ID);
                break;

            case 'thegem_pf_item':
                $portfolio_item_data = thegem_get_sanitize_pf_item_data($post->ID);
                if (empty($portfolio_item_data['types']))
                    $portfolio_item_data['types'] = array(0 => array('link' => '', 'link_target' => '_self', 'type' => 'self-link'));
                $page_data['portfolio_item_data'] = $portfolio_item_data;

                $page_data['portfolio_elements_data'] = thegem_get_sanitize_pf_item_elements_data($post->ID);
                break;

            case 'product':
                $size_guide = thegem_get_sanitize_product_size_guide_data($post->ID);
                $highlight = thegem_get_sanitize_product_featured_data($post->ID);
                $hover = !empty(get_post_meta($post->ID, 'thegem_product_disable_hover', true));
                $product_page = thegem_get_sanitize_product_page_data($post->ID);
                $page_data['product_item_data'] = array(
                    'thegem_product_disable_hover' => $hover ? 1 : 0,
                    'highlight' => $highlight['highlight'],
                    'highlight_type' => $highlight['highlight_type'],
                    'size_guide' => $size_guide['size_guide'],
                    'size_guide_image' => $size_guide['custom_image'],

                    'product_layout_settings' => $product_page['product_layout_settings'],

                    'product_gallery' => $product_page['product_gallery'],
                    'product_gallery_type' => $product_page['product_gallery_type'],
                    'product_gallery_column_position' => $product_page['product_gallery_column_position'],
                    'product_gallery_column_width' => $product_page['product_gallery_column_width'],
                    'product_gallery_show_image' => $product_page['product_gallery_show_image'],
                    'product_gallery_zoom' => $product_page['product_gallery_zoom'],
                    'product_gallery_lightbox' => $product_page['product_gallery_lightbox'],
                    'product_gallery_labels' => $product_page['product_gallery_labels'],
                    'product_gallery_label_sale' => $product_page['product_gallery_label_sale'],
                    'product_gallery_label_new' => $product_page['product_gallery_label_new'],
                    'product_gallery_label_out_stock' => $product_page['product_gallery_label_out_stock'],
                    'product_gallery_auto_height' => $product_page['product_gallery_auto_height'],
                    'product_gallery_elements_color' => $product_page['product_gallery_elements_color'],

                    'product_page_layout' => $product_page['product_page_layout'],
                    'product_page_layout_style' => $product_page['product_page_layout_style'],
                    'product_page_layout_preset' => $product_page['product_page_layout_preset'],
                    'product_page_layout_fullwidth' => $product_page['product_page_layout_fullwidth'],
                    'product_page_layout_sticky' => $product_page['product_page_layout_sticky'],
                    'product_page_layout_title_area' => $product_page['product_page_layout_title_area'],
                    'product_page_ajax_add_to_cart' => $product_page['product_page_ajax_add_to_cart'],
                    'product_page_desc_review_source' => $product_page['product_page_desc_review_source'],
                    'product_page_desc_review_layout' => $product_page['product_page_desc_review_layout'],
                    'product_page_desc_review_layout_tabs_style' => $product_page['product_page_desc_review_layout_tabs_style'],
                    'product_page_desc_review_layout_tabs_alignment' => $product_page['product_page_desc_review_layout_tabs_alignment'],
                    'product_page_desc_review_layout_acc_position' => $product_page['product_page_desc_review_layout_acc_position'],
                    'product_page_desc_review_layout_one_by_one_description_background' => $product_page['product_page_desc_review_layout_one_by_one_description_background'],
                    'product_page_desc_review_layout_one_by_one_additional_info_background' => $product_page['product_page_desc_review_layout_one_by_one_additional_info_background'],
                    'product_page_desc_review_layout_one_by_one_reviews_background' => $product_page['product_page_desc_review_layout_one_by_one_reviews_background'],
                    'product_page_desc_review_description' => $product_page['product_page_desc_review_description'],
                    'product_page_desc_review_description_title' => $product_page['product_page_desc_review_description_title'],
                    'product_page_desc_review_additional_info' => $product_page['product_page_desc_review_additional_info'],
                    'product_page_desc_review_additional_info_title' => $product_page['product_page_desc_review_additional_info_title'],
                    'product_page_desc_review_reviews' => $product_page['product_page_desc_review_reviews'],
                    'product_page_desc_review_reviews_title' => $product_page['product_page_desc_review_reviews_title'],
                    'product_page_button_add_to_cart_text' => $product_page['product_page_button_add_to_cart_text'],
                    'product_page_button_add_to_cart_icon' => $product_page['product_page_button_add_to_cart_icon'],
                    'product_page_button_add_to_cart_icon_pack' => $product_page['product_page_button_add_to_cart_icon_pack'],
                    'product_page_button_add_to_cart_icon_position' => $product_page['product_page_button_add_to_cart_icon_position'],
                    'product_page_button_add_to_cart_border_width' => $product_page['product_page_button_add_to_cart_border_width'],
                    'product_page_button_add_to_cart_border_radius' => $product_page['product_page_button_add_to_cart_border_radius'],
                    'product_page_button_add_to_cart_color' => $product_page['product_page_button_add_to_cart_color'],
                    'product_page_button_add_to_cart_color_hover' => $product_page['product_page_button_add_to_cart_color_hover'],
                    'product_page_button_add_to_cart_background' => $product_page['product_page_button_add_to_cart_background'],
                    'product_page_button_add_to_cart_background_hover' => $product_page['product_page_button_add_to_cart_background_hover'],
                    'product_page_button_add_to_cart_border_color' => $product_page['product_page_button_add_to_cart_border_color'],
                    'product_page_button_add_to_cart_border_color_hover' => $product_page['product_page_button_add_to_cart_border_color_hover'],
                    'product_page_button_add_to_wishlist_icon' => $product_page['product_page_button_add_to_wishlist_icon'],
                    'product_page_button_add_to_wishlist_icon_pack' => $product_page['product_page_button_add_to_wishlist_icon_pack'],
                    'product_page_button_add_to_wishlist_color' => $product_page['product_page_button_add_to_wishlist_color'],
                    'product_page_button_add_to_wishlist_color_hover' => $product_page['product_page_button_add_to_wishlist_color_hover'],
                    'product_page_button_add_to_wishlist_color_filled' => $product_page['product_page_button_add_to_wishlist_color_filled'],
                    'product_page_button_added_to_wishlist_icon' => $product_page['product_page_button_added_to_wishlist_icon'],
                    'product_page_button_added_to_wishlist_icon_pack' => $product_page['product_page_button_added_to_wishlist_icon_pack'],
                    'product_page_button_clear_attributes_text' => $product_page['product_page_button_clear_attributes_text'],
                    'product_page_elements_prev_next' => $product_page['product_page_elements_prev_next'],
                    'product_page_elements_preview_on_hover' => $product_page['product_page_elements_preview_on_hover'],
                    'product_page_elements_back_to_shop' => $product_page['product_page_elements_back_to_shop'],
                    'product_page_elements_back_to_shop_link' => $product_page['product_page_elements_back_to_shop_link'],
                    'product_page_elements_back_to_shop_link_custom_url' => $product_page['product_page_elements_back_to_shop_link_custom_url'],
                    'product_page_elements_title' => $product_page['product_page_elements_title'],
                    'product_page_elements_attributes' => $product_page['product_page_elements_attributes'],
                    'product_page_elements_attributes_data' => $product_page['product_page_elements_attributes_data'],
                    'product_page_elements_reviews' => $product_page['product_page_elements_reviews'],
                    'product_page_elements_reviews_text' => $product_page['product_page_elements_reviews_text'],
                    'product_page_elements_price' => $product_page['product_page_elements_price'],
                    'product_page_elements_price_strikethrough' => $product_page['product_page_elements_price_strikethrough'],
                    'product_page_elements_description' => $product_page['product_page_elements_description'],
                    'product_page_elements_stock_amount' => $product_page['product_page_elements_stock_amount'],
                    'product_page_elements_stock_amount_text' => $product_page['product_page_elements_stock_amount_text'],
                    'product_page_elements_size_guide' => $product_page['product_page_elements_size_guide'],
                    'product_page_elements_sku' => $product_page['product_page_elements_sku'],
                    'product_page_elements_sku_title' => $product_page['product_page_elements_sku_title'],
                    'product_page_elements_categories' => $product_page['product_page_elements_categories'],
                    'product_page_elements_categories_title' => $product_page['product_page_elements_categories_title'],
                    'product_page_elements_tags' => $product_page['product_page_elements_tags'],
                    'product_page_elements_tags_title' => $product_page['product_page_elements_tags_title'],
                    'product_page_elements_share' => $product_page['product_page_elements_share'],
                    'product_page_elements_share_title' => $product_page['product_page_elements_share_title'],
                    'product_page_elements_upsell' => $product_page['product_page_elements_upsell'],
                    'product_page_elements_upsell_title' => $product_page['product_page_elements_upsell_title'],
                    'product_page_elements_upsell_items' => $product_page['product_page_elements_upsell_items'],
                    'product_page_elements_upsell_columns_desktop' => $product_page['product_page_elements_upsell_columns_desktop'],
                    'product_page_elements_upsell_columns_tablet' => $product_page['product_page_elements_upsell_columns_tablet'],
                    'product_page_elements_upsell_columns_mobile' => $product_page['product_page_elements_upsell_columns_mobile'],
                    'product_page_elements_upsell_columns_100' => $product_page['product_page_elements_upsell_columns_100'],
                    'product_page_elements_related' => $product_page['product_page_elements_related'],
                    'product_page_elements_related_title' => $product_page['product_page_elements_related_title'],
                    'product_page_elements_related_items' => $product_page['product_page_elements_related_items'],
                    'product_page_elements_related_columns_desktop' => $product_page['product_page_elements_related_columns_desktop'],
                    'product_page_elements_related_columns_tablet' => $product_page['product_page_elements_related_columns_tablet'],
                    'product_page_elements_related_columns_mobile' => $product_page['product_page_elements_related_columns_mobile'],
                    'product_page_elements_related_columns_100' => $product_page['product_page_elements_related_columns_100'],
                );
                break;
        }

        return $page_data;
    }

    function getPurchaseCode() {
        $code = thegem_get_option('purchase_code');

        for ($i = 18; $i < strlen($code); $i++) {
            if ($code[$i] != '-') {
                $code[$i] = '*';
            }
        }

        return $code;
    }


    function getSelfHostedFonts() {
        $data = thegem_additionals_fonts();

        $fonts = array();

        foreach($data as $fontData) {
            $fonts[]= $fontData['font_name'];
        };

        return implode(',', $fonts);
    }

    function enqueuePageFiles($hook) {
        if ($hook == 'thegem_page_thegem-theme-options') {
            $appData = array(
                'isTheme' => true,
                'defaultOptions' => thegem_color_skin_defaults(),
                'options' => $this->getFullOptions(),
                'backups' => $this->getBackupsInfo(),
                'fonts' => $this->getFontsInfo(),
                'selfhostedFonts' => $this->getSelfHostedFonts(),
                'pages' => $this->getPagesList(),
                'parentPagesForPortfolioList' => $this->getParentPagesForPortfolioList(),
                'patternsUrl' => get_template_directory_uri().'/images/backgrounds/patterns/',
                'iconPacks' => $this->getIconPacks(),
                'isWooCommerce' => thegem_is_plugin_active('woocommerce/woocommerce.php') ? true : false,
                'isWishlist' => thegem_is_plugin_active('yith-woocommerce-wishlist/init.php') ? true : false,
                'presetsUrl' => get_template_directory_uri().'/images/backgrounds/presets/',
            );

            $this->addAppData($appData);
        }

        if (preg_match('%^thegem_page_(thegem-.+|install-required-plugins)$%', $hook, $matches) || in_array($hook, array(
            'toplevel_page_thegem-dashboard-welcome','admin_page_thegem-importer','admin_page_thegem-dashboard-importer'))) {

            switch ($hook) {
                case 'admin_page_thegem-importer':
                    $dashboardPage = 'thegem-importer';
                    break;
                case 'admin_page_thegem-dashboard-importer':
                    $dashboardPage = 'thegem-dashboard-importer';
                    break;
                case 'toplevel_page_thegem-dashboard-welcome':
                    $dashboardPage = 'thegem-dashboard-welcome';
                    break;
                default:
                    $dashboardPage = $matches[1];
                    break;
            }

            switch($dashboardPage) {
                case 'thegem-dashboard-welcome':
                    $pageData = array(
                        'systemStatus' => $this->getSystemStatus(),
                        'purchaseCode' => $this->getPurchaseCode(),
                        'activated' => get_option('thegem_activation'),
                        'buyUrl' => 'https://themeforest.net/checkout/from_item/16061685?license=regular',
                        'licenseManagerUrl' => 'https://codex-themes.com/thegem/license_manager',
                        'blocksUrl' => 'https://codex-themes.com/thegem/documentation/blocks/',
                        'supportUrl' => 'http://codexthemes.ticksy.com/',
                        'documentationUrl' => 'https://codex-themes.com/thegem/documentation/',
                    );
                    break;

                case 'install-required-plugins':
                    $pageData = array(
                    );
                    break;

                case 'thegem-dashboard-importer':
                    $installed_plugins = get_plugins();
                    $importer_installed = array_key_exists( static::THEGEM_IMPORTER_PLUGIN, $installed_plugins ) || in_array( static::THEGEM_IMPORTER_PLUGIN, $installed_plugins, true );

                    $action = $importer_installed ? 'activate' : 'install';

                    $url = wp_nonce_url(
                        add_query_arg(
                            array(
                                'page' => 'install-required-plugins',
                                'plugin' => preg_replace('%/.*%', '', static::THEGEM_IMPORTER_PLUGIN),
                                'tgmpa-'.$action => $action.'-plugin'
                            ),
                            admin_url( 'admin.php' )
                        ),
                        'tgmpa-' . $action,
                        'tgmpa-nonce'
                    );

                    $pageData = array(
                        'pluginUrl' => $url,
                        'isPluginInstalled' => $importer_installed
                    );
                    break;

                case 'thegem-importer':
                    $pageData = array(
                    );
                    break;

                case 'thegem-dashboard-manual-and-support':
                    $pageData = array(
                    );
                    break;

                case 'thegem-dashboard-system-status':
                    $pageData = array(
                        'systemStatus' => $this->getSystemStatus(),
                    );
                    break;

                case 'thegem-dashboard-changelog':
                    $pageData = array(
                    );
                    break;
            }

            if (isset($pageData)) {
                $appData = array_merge($pageData, array(
                    'isDashboard' => true,
                    'dashboardPage' => $dashboardPage,
                    'activated' => get_option('thegem_activation'),
                    'themeUpdate' => $this->getThemeUpdateInfo(),
                    'pluginElementsUpdate' => $this->getPluginElementsUpdateInfo(),
                    'importPage' => is_plugin_active(static::THEGEM_IMPORTER_PLUGIN) ? 'thegem-importer':'thegem-dashboard-importer'
                ));

                $this->addAppData($appData);
            }
        }
    }

    function parseFontPack($filename,$regexp,$data) {
        $css = file_get_contents(get_template_directory() . $filename);

        preg_match_all('%@font-face[^}]*}%',$css,$matches);
        $fontFaces=implode("\n",$matches[0]);
        $data['fontFaces']=str_replace('..',get_template_directory_uri(),$fontFaces);

        preg_match_all($regexp, $css, $matches);
        $data['icons'] = array();
        foreach($matches[1] as $k=>$v) {
            $data['icons'][$matches[2][$k]]=$v;
        }

        return $data;
    }

    private function unitToInt($s) {
        return (int)preg_replace_callback('/(\-?\d+)(.)/', function ($m) {
            return $m[1] * pow(1024, strpos('BKMG', $m[2]));
        }, strtoupper($s));
    }

    function getPluginElementsUpdateInfo() {
        $plugins = get_site_transient('update_plugins');

        if ( isset($plugins->response) && is_array($plugins->response) ) {
            $plugins_ids = array_keys( $plugins->response );
            $plugin_file = 'thegem-elements-elementor/thegem-elements-elementor.php';
            if(in_array($plugin_file, $plugins_ids)) {
                $plugin_data = get_plugin_data(trailingslashit(WP_PLUGIN_DIR).$plugin_file);
                $plugin_update = $plugins->response[$plugin_file];

                return array(
                    'updateUrl' => esc_url(admin_url('update-core.php')),
                    'currentVersion' => $plugin_data['Version'],
                    'newVersion' => $plugin_update->new_version
                );
            } else {
                if (!is_plugin_active('thegem-elements-elementor/thegem-elements-elementor.php')) {
                    return false;
                }

                $plugin_data = get_plugin_data(trailingslashit(WP_PLUGIN_DIR).'thegem-elements-elementor/thegem-elements-elementor.php');

                return array(
                    'currentVersion' => $plugin_data['Version'],
                );
            }
        }
        return false;
    }

    function getThemeUpdateInfo() {
        if ( !current_user_can('update_themes' ) )
            return false;
		$themes_update = get_site_transient('update_themes');

        if ( isset($themes_update->response['thegem']) ) {
            $update = $themes_update->response['thegem'];
            $theme = wp_prepare_themes_for_js( array( wp_get_theme('thegem') ) );
            if(isset($theme[0]) && isset($theme[0]['hasUpdate']) && $theme[0]['hasUpdate']) {
                return array(
                    'detailsUrl' => add_query_arg(array(), $update['url']),
                    'updateUrl' => admin_url( 'update.php?action=upgrade-theme&theme=' . urlencode( 'thegem' ) ."&_wpnonce=". wp_create_nonce('upgrade-theme_thegem') ),
                    'version' => $update['new_version']
                );
            }
        }

        return false;
    }

    function formatBytes($bytes, $precision = 2) {
        $unit = array("B", "KB", "MB", "GB");
        $exp = floor(log($bytes, 1024)) | 0;
        return round($bytes / (pow(1024, $exp)), $precision).$unit[$exp];
    }

    function getApiServerConnection() {
        $ok = get_transient('thegem_theme_options_server_connection');

        if ($ok === false) {
            $result = wp_remote_get( 'http://democontent.codex-themes.com/av_validate_code.php', array(
                'timeout'     => 5,
            ) );

            $ok = $result['response']['code'] = 200;

            set_transient('thegem_theme_options_server_connection',$ok ? 1:0 ,$ok ? 300:60);
        }

        return boolval($ok);
    }

    function getUploadsFolderWritable() {
        $ok = get_transient('thegem_theme_options_uploads_writable');

        if ($ok === false) {
            $result = wp_upload_bits('thegem_test.jpg',null,base64_decode('/9j/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkICQkKDA8MCgsOCwkJDRENDg8QEBEQCgwSExIQEw8QEBD/yQALCAABAAEBAREA/8wABgAQEAX/2gAIAQEAAD8A0s8g/9k='));

            $ok = !boolval($result['error']);

            if ($ok) {
                unlink($result['file']);
            }

            set_transient('thegem_theme_options_uploads_writable',$ok ? 1:0 ,$ok ? 300:60);
        }

        return boolval($ok);
    }

    function getSystemStatus() {
        $data = array(
            'apiServerConnection' => $this->getApiServerConnection(),
            'uploadsFolderWritable' => $this->getUploadsFolderWritable(),
            'activated' => boolval(get_option('thegem_activation')),
            'pluginElementsActive' => !!thegem_is_plugin_active('thegem-elements-elementor/thegem-elements-elementor.php'),
            'pageBuilderVersion' => !!thegem_is_plugin_active('elementor/elementor.php'),
            'phpVersion' => PHP_VERSION,
            'phpMemoryLimit' => $this->unitToInt(ini_get('memory_limit')),
            'phpTimeLimit' => ini_get('max_execution_time'),
            'phpMaxInputVars' => ini_get('max_input_vars'),
            'phpPostMaxSize' => $this->unitToInt(ini_get('post_max_size')),
            'phpUploadMaxFilesize' => $this->unitToInt(ini_get('upload_max_filesize')),
            'wpVersion' => $GLOBALS['wp_version'],
            'wpFileSystem' => get_filesystem_method(),
            'blogLanguage' => get_bloginfo('language'),
            'siteUrl' => get_site_url(),
            'direction' => is_rtl() ? 'RTL':'LTR',
            'homeUrl' => get_home_url()
        );

        $data=array_merge($data, array(
            'phpMemoryLimitFormatted' => $this->formatBytes($data['phpMemoryLimit']),
            'phpMemoryLimitOk' => $data['phpMemoryLimit'] >= 256*1024*1024,
            'phpVersionOk' => version_compare(PHP_VERSION,'5.6.22'),
            'phpTimeLimitOk' => $data['phpTimeLimit'] >= 180,
            'phpMaxInputVarsOk' => $data['phpMaxInputVars'] >= 4000,
            'phpPostMaxSizeFormatted' => $this->formatBytes($data['phpPostMaxSize']),
            'phpPostMaxSizeOk' => $data['phpPostMaxSize'] >= 96*1024*1024,
            'phpUploadMaxFilesizeFormatted' => $this->formatBytes($data['phpUploadMaxFilesize']),
            'phpUploadMaxFilesizeOk' => $data['phpUploadMaxFilesize'] >= 16*1024*1024,
            'wpVersionOk' => version_compare(get_bloginfo('version'), '5.2.0') >= 0,
        ));

        return $data;
    }

    function getIconPacksInfo() {
        $fontpacks = array(
            $this->parseFontPack('/css/icons-material.css','%\.mdi-([^:]*).*?content:\s*".([^"]*)%s', array(
                'title'=>'Material Design Icons',
                'icon'=>'font-size',
                'value'=>'material',
                'fontFamily'=>'MaterialDesignIcons'
            )),
            $this->parseFontPack('/css/icons-fontawesome.css','%\.fa-([^:]*).*?content:\s*".([^"]*)%s', array(
                'title'=>'Font Awesome Icons',
                'icon'=>'font-size',
                'value'=>'fontawesome',
                'fontFamily'=>'FontAwesome',
            )),
            $this->parseFontPack('/css/icons-elegant.css','%\.([^:{}]*):before.*?content:\s*".([^"]*)%s', array(
                'title'=>'Elegant Icons',
                'icon'=>'font-size',
                'value'=>'elegant',
                'fontFamily'=>'ElegantIcons'
            )),
        );

        return $fontpacks;
    }

    function prePageWrapper($page) {
        $this->renderDashboardPage();
        echo '<div class="thegem-install-plugins-wrapper">';
    }

    function postPageWrapper($page) {
        echo "</div>";
        echo
        "<style>
            .wrap{
                position: relative;
                z-index: 100;
            }
            .dashboard + .wrap{
                margin-top: -50px;
            }
            .thegem-importer-header{
                background-color: #ffffff;
                margin-top: 0;
            }
            .thegem-importer-content{
                padding-top: 20px;
                border-bottom-left-radius: 5px;
                border-bottom-right-radius: 5px;
            }
            .thegem-importer-header > .thegem-importer-logo{
                display: none;
            }
             .thegem-importer-header > .thegem-importer-title{
                font-family: 'Montserrat UltraLight';
                margin-left: 0;
                color: #3c3950;
            }
            .thegem-importer-header > .thegem-importer-remover a:hover {
                background-color: #3c3950;
            }

            .tgmpa{
                background-color: #ffffff;
                padding: 0 40px 40px 40px;
            }
            .tgmpa.wrap{
                margin-top: -20px;
            }
            .tgmpa.wrap > h1{
                display: none;
            }
        </style>";
    }

    function getFontsInfo() {
        $fonts = array();
        foreach(thegem_fonts_list(true) as $font) {
            $fontData=array(
                'family' => $font['family'],
            );

            if (!empty($font['subsets'])) {
                $fontData['subsets']=$font['subsets'];
            }

            $fonts[]=$fontData;
        }

        return $fonts;
    }

    function filesystemCredentials() {
        ob_start();

        $url = wp_nonce_url('admin_ajax.php?page=thegem-theme-options','thegem-theme-options');
        $creds = request_filesystem_credentials($url, '', false, get_stylesheet_directory() . '/css/', array('action'=>'wp_ajax_thegem_theme_options_credentials'));
        if (!WP_Filesystem($creds)) {
            request_filesystem_credentials($url, '', false, get_stylesheet_directory() . '/css/', array('action'=>'wp_ajax_thegem_theme_options_credentials'));
        }
        $form = ob_get_clean();

    }

    function getCredentialsForm($additionalText) {
        $form = ob_get_clean();

        if ($additionalText) {
           $form = preg_replace('%<[^>]*id="request-filesystem-credentials-desc[^>]*>%', "$0<strong>$additionalText</strong><br/><br/>", $form);
        }

        return $form;
    }

    function thegem_generate_custom_css($additionalText= false) {
        thegem_get_option(false, false, false, true);
        ob_start();
        thegem_custom_fonts();
        require get_template_directory() . '/inc/custom-css.php';
        if(file_exists(get_stylesheet_directory() . '/inc/custom-css.php') && get_stylesheet_directory() != get_template_directory()) {
            require get_stylesheet_directory() . '/inc/custom-css.php';
        }
        $custom_css = ob_get_clean();
        ob_start();
        require get_template_directory() . '/inc/style-editor-css.php';
        $editor_css = ob_get_clean();
        $action = array('action');

        ob_start();

        $url = wp_nonce_url('admin.php?page=thegem-theme-options','thegem-theme-options');
        if (false === ($creds = request_filesystem_credentials($url, '', false, get_stylesheet_directory() . '/css/', $action) ) ) {
            $form = $this->getCredentialsForm($additionalText);
            return array('reason'=>'credentials', 'form'=> $form);
        }

        if(!WP_Filesystem($creds)) {
            request_filesystem_credentials($url, '', true, get_stylesheet_directory() . '/css/', $action);
            $form = $this->getCredentialsForm($additionalText);
            return array('reason'=>'credentials', 'form'=> $form);
        }

        global $wp_filesystem;
        $old_name = thegem_get_custom_css_filename();
        $new_name = thegem_generate_custom_css_filename();
        if(!$wp_filesystem->put_contents($wp_filesystem->find_folder(get_stylesheet_directory()) . 'css/'.$new_name.'.css', $custom_css)) {
            update_option('thegem_genearte_css_error', '1');
            return array('reason'=>'error','message'=>sprintf(esc_html__('TheGem\'s styles cannot be customized because file "%s" cannot be modified. Please check your server\'s settings. Then click "Save" button.', 'thegem'), get_stylesheet_directory() . '/css/custom.css'));
        } else {
            $wp_filesystem->put_contents($wp_filesystem->find_folder(get_template_directory()) . 'css/style-editor.css', $editor_css);
            $custom_css_files = glob(get_template_directory().'/css/custom-*.css');
            foreach($custom_css_files as $file) {
                if(basename($file, '.css') != $new_name) {
                    $wp_filesystem->delete($wp_filesystem->find_folder(get_stylesheet_directory()) . 'css/'.basename($file, '.css').'.css', $custom_css);
                }
            }
            thegem_save_custom_css_filename($new_name);
            delete_option('thegem_genearte_css_error');
            delete_option('thegem_generate_empty_custom_css_fail');
        }

        return true;
    }

    function default_page_data($data, $post_id, $item_data, $type) {
        $defaults = array(
            'global' => get_option('thegem_options_page_settings_global'),
            'page' => get_option('thegem_options_page_settings_default'),
            'post' => get_option('thegem_options_page_settings_post'),
            'portfolio' => get_option('thegem_options_page_settings_portfolio'),
            'blog' => get_option('thegem_options_page_settings_blog'),
            'search' => get_option('thegem_options_page_settings_search'),
        );
        foreach($data as $key => $value) {
            $data[$key] = !empty($defaults['global'][$key]) ? $defaults['global'][$key] : $data[$key];
            if($type === 'blog' || $type === 'term') {
                $data[$key] = !empty($defaults['blog'][$key]) && thegem_get_option('global_settings_apply_blog') ? $defaults['blog'][$key] : $data[$key];
            } elseif($type === 'search') {
                $data[$key] = !empty($defaults['search'][$key]) && thegem_get_option('global_settings_apply_search') ? $defaults['search'][$key] : $data[$key];
            } else {
                if(in_array(get_post_type($post_id), array('post', 'page', 'portfolio')) || $type === 'default') {
                    $data[$key] = !empty($defaults['page'][$key]) && thegem_get_option('global_settings_apply_default') ? $defaults['page'][$key] : $data[$key];
                }
                if(get_post_type($post_id) === 'post') {
                    $data[$key] = !empty($defaults['post'][$key]) && thegem_get_option('global_settings_apply_post') ? $defaults['post'][$key] : $data[$key];
                }
                if(get_post_type($post_id) === 'portfolio') {
                    $data[$key] = !empty($defaults['portfolio'][$key]) && thegem_get_option('global_settings_apply_portfolio') ? $defaults['portfolio'][$key] : $data[$key];
                }
            }
        }
        return $data;
    }

    function apply_page_settings($page_settings_type) {
        /* update post data */
    }
}

$thegemThemeOptions=new ThegemThemeOptions();
