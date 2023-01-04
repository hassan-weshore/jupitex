<?php

if (!defined('ABSPATH')) {
    die('-1');
}

class TheGemBlocks {

    CONST VERSION = '1.0.0';
    CONST TAB_NAME = 'thegem_blocks_templates';
    CONST AJAX_NONCE = 'thegem-blocks-ajax-nonce';

    CONST OPTION_NAME = 'thegem_blocks_options';
    CONST OPTION_NAME_TEMP_INSERT_CONTENT = 'thegem_blocks_temp_insert_content';

    CONST OPTION_IS_INCLUDE_MEDIA = 'is_include_media';
    CONST OPTION_FAVORITES = 'favorites';
    CONST OPTION_FAVORITES_DARK = 'favorites_dark';
    CONST OPTION_CHECK_PURCHASE_CODE_DT = 'check_purchase_code_dt';
    CONST OPTION_IS_DARK_MODE = 'is_dark_mode';

    CONST DEMO_CONTENT_BLOCKED_TIME = '240'; // minute
    CONST TEMPLATE_TYPE_NEW = 'NEW';
    CONST ITEMS_PER_PAGE = 20;

    CONST PLUGIN_CF7 = 'contact-form-7/wp-contact-form-7.php';
    CONST PLUGIN_YIKES= 'yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php';
    CONST PLUGIN_WOO = 'woocommerce/woocommerce.php';

    public $options;
    public $templates;
    public $categories;
    public $favorites;
    //public $dummyList;
    public $dummyListByUrl;

    public $mailChimpForms;
    public $isCustomPostTitle;

    private $importPosts = [];
    private $importTerms = [];
    private $importMailChimpForms = [];

    public function __construct() {
        $this->options = get_option(static::OPTION_NAME, []);
        $this->favorites = $this->getFavorites();
    }

    /**
	 * Load JS for our custom Elementor modal.
	 */
	public function enqueue_editor_scripts() {
        $this->initData();

        wp_enqueue_script('thegem_blocks_imagesloaded', THEGEM_BLOCKS_URL.'assets/js/lib/imagesloaded.pkgd.min.js', [], '4.1.4', true);
        wp_enqueue_script('thegem_blocks_masonry', THEGEM_BLOCKS_URL.'assets/js/lib/masonry.pkgd.min.js', [], '4.2.2', true);

        wp_enqueue_script('thegem_blocks_main', THEGEM_BLOCKS_URL . 'assets/js/main.js', array( 'jquery' ), static::VERSION );
        wp_localize_script('thegem_blocks_main', 'TheGemBlocksOptions', $this->getLocalizeScriptData());
        wp_enqueue_style('thegem_blocks_googlefonts','https://fonts.googleapis.com/css?family=Montserrat:300,400,700|Source+Sans+Pro:300,400');

        // css
        wp_enqueue_style('thegem_blocks_main', THEGEM_BLOCKS_URL.'assets/css/main.css', [], static::VERSION);
        wp_enqueue_style('thegem_blocks_icons', THEGEM_BLOCKS_URL.'assets/css/icons.css', [], static::VERSION);
	}

	public function enqueue_embedded_iframe_styles(){
		//wp_enqueue_style( 'template-kit-import-admin', THEGEM_BLOCKS_URL . 'assets/css/main.css', [], filemtime( THEGEM_BLOCKS_DIR . 'assets/main.css' ) );
		wp_enqueue_style( 'thegem-blocks-elements-elementor-modal', THEGEM_BLOCKS_URL . 'assets/css/elementor_modal.css', [], static::VERSION );
	}

    public function init() {
        
        $this->initData();
        /*
        $this->loadAssets();
        */

        add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
        add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_embedded_iframe_styles' ] );

/*
        add_filter('vc_get_all_templates', [$this, 'addTab']);
        add_filter('vc_templates_render_category', [$this, 'initTabContent']);
        add_filter('vc_load_default_templates', [$this, 'loadDefaultTemplates']);
*/

        add_action('wp_ajax_thegem_blocks_update_favorite', [$this, 'updateFavorite']);
        add_action('wp_ajax_thegem_blocks_import', [$this, 'import']);
        add_action('wp_ajax_thegem_blocks_update_include_media', [$this, 'updateIncludeMedia']);
        add_action('wp_ajax_thegem_blocks_update_mode', [$this, 'updateMode']);
    }

    public function loadAssets() {
        $this->registerScripts();

        add_action('vc_backend_editor_enqueue_js_css', [$this, 'enqueueJsCss']);
        add_action('vc_frontend_editor_enqueue_js_css', [$this, 'enqueueJsCss']);
        add_action('vc_load_iframe_jscss', [$this, 'vcLoadIframe']);
    }

    public function enqueueJsCss() {
        // js
        wp_enqueue_script('thegem_blocks_main', THEGEM_BLOCKS_URL.'assets/js/main.js', ['jquery','thegem_blocks_imagesloaded','thegem_blocks_masonry'], static::VERSION, true);
        wp_localize_script('thegem_blocks_main', 'TheGemBlocksOptions', $this->getLocalizeScriptData());

        // css
        wp_enqueue_style('thegem_blocks_main', THEGEM_BLOCKS_URL.'assets/css/main.css', [], static::VERSION);
        wp_enqueue_style('thegem_blocks_icons', THEGEM_BLOCKS_URL.'assets/css/icons.css', [], static::VERSION);
    }

    public function vcLoadIframe() {
        ?>
        <script type="text/javascript">
            window.addEventListener('load', function() {
                document.querySelector('#vc_templates-more-layouts').onclick = function () {
                    window.parent.theGemBlocks.show();
                };
            });
        </script>
        <?php
    }

    public function registerScripts() {
        wp_register_script('thegem_blocks_imagesloaded', THEGEM_BLOCKS_URL.'assets/js/lib/imagesloaded.pkgd.min.js', [], '4.1.4', true);
        wp_register_script('thegem_blocks_masonry', THEGEM_BLOCKS_URL.'assets/js/lib/masonry.pkgd.min.js', [], '4.2.2', true);
    }

    public function loadDefaultTemplates($defaultTemplates) {
        //$insertTemplate = get_option(static::OPTION_NAME_TEMP_INSERT_CONTENT);

        foreach ($this->templates as $template) {
            $defaultTemplate = [
                'name'=>$template['id'],
                'weight'=>$template['sort_order'],
                'type'=>'thegem_blocks'
            ];

            if ($insertTemplate && $insertTemplate['id'] == $template['id']) {
                $defaultTemplate['content'] = $insertTemplate['content'];
                delete_option(static::OPTION_NAME_TEMP_INSERT_CONTENT);
            } else {
                $defaultTemplate['content'] = $template['content'];
            }

            $defaultTemplates[] = $defaultTemplate;
        }

        return $defaultTemplates;
    }

    public function addTab($data) {
        $items[] = [
            'category' => static::TAB_NAME,
            'category_name' => esc_html__('TheGem Blocks', 'thegem'),
            'category_weight' => 1,
            'templates' => []
        ];

        $defaultTemplates = [];

        foreach ($data as $item) {
            if ($item['category'] == 'default_templates') {
                foreach ($item['templates'] as $defaultTemplate) {
                    $defaultTemplates[$defaultTemplate['name']] = $defaultTemplate;
                }
            }

            if ($item['category'] == 'my_templates') {
                $item['category_name'] = esc_html__('Saved Templates', 'thegem');
                $items[] = $item;
            }
        }

        return $items;
    }

    public function initTabContent($category) {
        if ($category['category'] == static::TAB_NAME) {
            $category['output'] = $this->getTabContent();
        }
        return $category;
    }

    public function getTabContent() {
        ob_start();
        require_once THEGEM_BLOCKS_DIR.'inc/views/index.php';
        return ob_get_clean();
    }

    private function getLocalizeScriptData() {
        return [
            'ajax_url'=>esc_url(admin_url('admin-ajax.php')),
            'ajax_nonce'=>wp_create_nonce(static::AJAX_NONCE),
            'mainHtml'=>$this->getTabContent(),
            'texts'=>[
                'vc_ui_panel_header_heading' => esc_html__('Blocks & Saved Templates', 'thegem'),
                'vc_templates_name_filter'=> esc_html__('Search by name', 'thegem'),
                'favorites_empty'=>$this->getFavoritesEmptyText(),
                'templates_empty'=>$this->getTemplatesEmptyText()
            ],
            'templates'=>$this->getTemplatesAsHtml(),
            'per_page'=>static::ITEMS_PER_PAGE,
            'favorites'=>$this->getFavorites(),
            'is_dark_mode'=>$this->isDarkMode(),
            'default_category'=>$this->getDefaultCategory(),
            'is_custom_post_title'=>$this->isCustomPostTitle
        ];
    }

    public function checkingData() {
        $dataFile = 'data.php';
        $contentFile = 'content.xml';

        if (!is_file(THEGEM_BLOCKS_DIR.'data/'.$dataFile)) {
            wp_die('File '.THEGEM_BLOCKS_DIR.'data/'.$dataFile.' does not exist.');
        }

        if (!is_file(THEGEM_BLOCKS_DIR.'data/'.$contentFile)) {
            wp_die('File '.THEGEM_BLOCKS_DIR.'data/'.$contentFile.' does not exist.');
        }
    }

    public function initData() {
        $this->checkingData();

        static $categories;
        static $templates;
        //static $dummyList;
        static $globalColors;
        static $dummyListByUrl;
        static $globalColors;
        static $mailChimpForms;

        if (!$categories || !$templates || !$mailChimpForms || !$dummyListByUrl || !$globalColors) {
            require_once THEGEM_BLOCKS_DIR.'data/data.php';

            if (!empty($templates)) {
                $arrSort = array_column($templates, 'sort_order');
                array_multisort($arrSort, SORT_ASC, $templates);
            }
        }

        $this->categories = $categories;
        $this->templates = $templates;
        //$this->dummyList = $dummyList;
        $this->dummyListByUrl = $dummyListByUrl;
        $this->globalColors = $globalColors;
        $this->mailChimpForms = $mailChimpForms;
        $this->isCustomPostTitle = $this->isCustomPostTitle();
    }

    public function getCountTotalTemplates() {
        return array_sum(array_column($this->categories, $this->isDarkMode() ? 'count_dark' : 'count_multicolor'));
    }

    public function getPreviewImage($image) {
        if (empty($image)) {
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mOcuXLrfwAG0gL43X2vPAAAAABJRU5ErkJggg==';
        }

        return THEGEM_BLOCKS_URL.'data/preview/'.$image;
    }

    public function getTemplatesById() {
        static $items;

        if (!$items) {
            foreach ($this->templates as $template) {
                $items[$template['id']] = $template;
            }
        }

        return $items;
    }

    public function getTemplate($id) {
        if (empty($this->getTemplatesById()[$id])) {
            wp_die('TheGemBlocks: template not found');
        }

        return $this->getTemplatesById()[$id];
    }

    public function getTemplatesAsHtml() {
        ob_start();
        foreach ($this->getTemplates() as $template) {
            include THEGEM_BLOCKS_DIR.'inc/views/_item.php';
        }
        return ob_get_clean();
    }

    public function getTemplates($all = false) {
        static $templates;

        if (!$templates) {
            /*
            $defaultTemplates = array_filter(visual_composer()->templatesPanelEditor()->getDefaultTemplates(), function ($item) {
                return isset($item['type']) && $item['type'] == 'thegem_blocks';
            });
            */

            $defaultTemplates = $this->templates;

            $templates = [];
            foreach ($defaultTemplates as $key=>$defaultTemplate) {
                $template = $this->getTemplate($defaultTemplate['id']);
                $template['unique_id'] = $key;
                $templates[] = $template;
            }

            if (!$all) {
                $templates = array_filter($templates, function ($item) {
                    return $item['is_dark'] == $this->isDarkMode() || $item['is_universal'];
                });
            }

        }

        return $templates;
    }

    public function getCategories() {
        static $categories;

        if (!$categories) {
            $categories = array_filter($this->categories, function ($item) {
                return $item[$this->isDarkMode() ? 'count_dark' : 'count_multicolor'] > 0;
            });
        }

        return $categories;
    }

    public function getCategoriesAsHtml() {
        ob_start();
        include THEGEM_BLOCKS_DIR.'inc/views/_categories.php';
        return ob_get_clean();
    }

    public function updateOption($name, $value) {
        $options = get_option(static::OPTION_NAME, []);
        $options[$name] = $value;
        update_option(static::OPTION_NAME, $options);
        $this->options = $options;
        return $options;
    }

    public function getFavorites() {
        $optionFavorites = $this->isDarkMode() ? static::OPTION_FAVORITES_DARK : static::OPTION_FAVORITES;
        return isset($this->options[$optionFavorites]) ? $this->options[$optionFavorites] : [];
    }

    public function isFavorite($id) {
        return !empty($this->favorites) ? in_array($id, $this->favorites) : false;
    }

    public function updateFavorite() {
        check_ajax_referer(static::AJAX_NONCE, 'ajax_nonce');

        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        $event = isset($_POST['event']) ? strval($_POST['event']) : null;

        if (!isset($id, $event) || $id==='' || $event==='') {
            wp_die('TheGemBlocks error: update favorite');
        }

        switch ($event) {
            case 'add':
                $this->favorites[] = $id;
                break;
            case 'delete':
                $this->favorites = array_diff($this->favorites, [$id]);
                break;
        }

        $this->favorites = array_values($this->favorites);
        $this->updateOption($this->isDarkMode() ? static::OPTION_FAVORITES_DARK : static::OPTION_FAVORITES, $this->favorites);

        wp_send_json([
            'result'=>true,
            'favorites'=>$this->getFavorites()
        ]);

        wp_die();
    }

    public function updateIncludeMedia() {
        check_ajax_referer(static::AJAX_NONCE, 'ajax_nonce');

        $includeMedia = isset($_POST['include_media']) ? intval($_POST['include_media']) : false;
        $this->updateOption(static::OPTION_IS_INCLUDE_MEDIA, boolval($includeMedia));

        wp_send_json(['result'=>true]);
        wp_die();
    }

    public function isIncludeMedia() {
        return isset($this->options[static::OPTION_IS_INCLUDE_MEDIA]) ? $this->options[static::OPTION_IS_INCLUDE_MEDIA] : true;
    }

    public function updateMode() {
        check_ajax_referer(static::AJAX_NONCE, 'ajax_nonce');

        $isDarkMode = isset($_POST['is_dark_mode']) ? intval($_POST['is_dark_mode']) : false;
        $isCustomPostTitle = isset($_POST['is_custom_post_title']) ? intval($_POST['is_custom_post_title']) : false;
        $this->updateOption(static::OPTION_IS_DARK_MODE, boolval($isDarkMode));

        $this->favorites = $this->getFavorites();
        $this->isCustomPostTitle = $isCustomPostTitle;

        wp_send_json([
            'result'=>true,
            'templates'=>$this->getTemplatesAsHtml(),
            'categories'=>$this->getCategoriesAsHtml(),
            'favorites'=>$this->favorites
        ]);

        wp_die();
    }

    public function isDarkMode() {
        return isset($this->options[static::OPTION_IS_DARK_MODE]) ? $this->options[static::OPTION_IS_DARK_MODE] : false;
    }

    public function generateIds(&$content) {
        if (isset($content['elType']) && isset($content['id'])) {
            $content['id']=\Elementor\Utils::generate_random_string();
        }

        foreach($content as $k=>$v) {
            if (is_array($content[$k])) {
                $this->generateIds($content[$k]);
            }
        }
    }

    public function import() {
        check_ajax_referer(static::AJAX_NONCE, 'ajax_nonce');

        $this->checkPurchaseCode();

        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        $isIncludeMedia = $this->isIncludeMedia();
        $template = $this->getTemplate($id);

        $this->checkInstallAdditionalPlugins($template);

        $importData = $template['import_data'];
        $importPostIds = $importData['post_ids'];
        $importPostIds[] = -1;

        $data['result'] = true;

        if (! defined('WP_LOAD_IMPORTERS')) define('WP_LOAD_IMPORTERS', true);
        require_once THEGEM_BLOCKS_DIR.'/inc/classes/thegem-blocks-wp-importer.php';
        $wpImporter = new TheGemBlocksWPImporter();
        $wpImporter->fetch_attachments = $isIncludeMedia;
        $wpImporter->dummyListByUrl = $this->dummyListByUrl;
        $wpImporter->globalColors = $this->globalColors;
        $wpImporter->mailChimpForms = $this->mailChimpForms;

        ob_start();
        $wpImporter->import(THEGEM_BLOCKS_DIR.'data/content.xml', $importPostIds, []);

        $content = $wpImporter->process_elementor_data(null, $template['content']);

        $post = array(
            'post_id' => -1,
            'postmeta' => array(
                array(
                    'key' => '_elementor_data',
                    'value' => $content
                )
            )
        );

        write_log(json_encode($post));
        $wpImporter->import_post_elementor_dependencies($post);
        write_log(json_encode($post));
        $content = json_decode($post['postmeta'][0]['value'], true);
        $this->generateIds($content);

        $this->importPosts = $this->importPosts + $wpImporter->processed_posts;
        $this->importTerms = $wpImporter->processed_terms;

        if (!empty($importData['mailchimp_form_ids'])) {
            $this->importMailChimpForms($importData['mailchimp_form_ids']);
        }

        if (!empty($importPostIds) || !empty($importData['mailchimp_form_ids'])) {
            update_option(static::OPTION_NAME_TEMP_INSERT_CONTENT, [
                'id'=>$template['id'],
                'content'=>$this->replaceImportData($template['content'])
            ]);
        }

        $this->updateImportData($importData, $isIncludeMedia);

        \Elementor\Plugin::$instance->editor->set_edit_mode( true );

        $content = \Elementor\Plugin::$instance->documents->get_doc_or_auto_save( intval($_POST['post_id']) )->get_elements_raw_data( $content, true );

        $result = ob_get_clean();

        //var_dump($GLOBALS['wp_styles']);exit;

        $data['blockContent'] = $content;

        wp_send_json($data);

        wp_die();
    }

    private function replaceImportData($content) {
        // replace attachment ids
        $content = preg_replace_callback("/{{IMG_ID=\d+}}/", function ($matches) {
            $id = preg_replace('/[^0-9]/', '', $matches[0]);
            return !empty($this->importPosts[$id]) ? $this->importPosts[$id] : $id;
        }, $content);

        $content = preg_replace_callback("/{{IMG_URL=\d+}}/", function ($matches) {
            $id = preg_replace('/[^0-9]/', '', $matches[0]);
            $newId = !empty($this->importPosts[$id]) ? $this->importPosts[$id] : $id;
            return wp_get_attachment_url($newId).'?id='.$newId;
        }, $content);

        // replace video id
        $content = preg_replace_callback("/{{VIDEO_URL=\d+}}/", function ($matches) {
            $id = preg_replace('/[^0-9]/', '', $matches[0]);
            $newId = !empty($this->importPosts[$id]) ? $this->importPosts[$id] : $id;
            return wp_get_attachment_url($newId);
        }, $content);

        // replace gallery ids
        $content = preg_replace_callback("/{{GALLERY_ID=\d+}}/", function ($matches) {
            $replaceId = preg_replace('/[^0-9]/', '', $matches[0]);
            $id = !empty($this->importPosts[$replaceId]) ? $this->importPosts[$replaceId] : $replaceId;

            $newGalleryItems = [];
            $galleryItems = explode(',', get_post_meta($id, 'thegem_gallery_images', true));
            foreach ($galleryItems as $galleryItemId) {
                $newGalleryItems[] = !empty($this->importPosts[$galleryItemId]) ? $this->importPosts[$galleryItemId] : $galleryItemId;
            }
            update_post_meta($id, 'thegem_gallery_images', implode(',', $newGalleryItems));

            return $id;
        }, $content);

        // replace product category ids
        $content = preg_replace_callback("/{{PRODUCT_CATEGORY_IDS=[0-9,\s]+}}/", function ($matches) {
            $ids = explode(',', preg_replace('/[^0-9,]/', '', $matches[0]));
            $newIds = [];
            foreach ($ids as $id) {
                $newId = !empty($this->importTerms[$id]) ? $this->importTerms[$id] : $id;
                $this->updateProductDataAfterImport(get_term($newId));
                $newIds[] = $newId;
            }
            return implode(', ', $newIds);
        }, $content);

        // replace contact form
        $content = preg_replace_callback("/{{CF_ID=\d+}}/", function ($matches) {
            $id = preg_replace('/[^0-9]/', '', $matches[0]);
            return !empty($this->importPosts[$id]) ? $this->importPosts[$id] : $id;
        }, $content);

        // replace team person
        $content = preg_replace_callback("/{{TEAM_PERSON_ID=\d+}}/", function ($matches) {
            $id = preg_replace('/[^0-9]/', '', $matches[0]);
            return !empty($this->importPosts[$id]) ? $this->importPosts[$id] : $id;
        }, $content);

        // replace mailchimp form
        $content = preg_replace_callback("/{{MF_ID=\d+}}/", function ($matches) {
            $id = preg_replace('/[^0-9]/', '', $matches[0]);
            return !empty($this->importMailChimpForms[$id]) ? $this->importMailChimpForms[$id] : $id;
        }, $content);
        
        return $content;
    }

    private function updateProductDataAfterImport($term) {
        if (!$term) return false;

        $products = (new WP_Query())->query([
            'post_type'=>'product',
            'tax_query'=>['relation' => 'OR', ['taxonomy' => $term->taxonomy, 'field' => 'slug', 'terms' => $term->slug]],
            'posts_per_page'=>-1
        ]);

        foreach ($products as $product) {
            $imageGalleryIds = explode(',', get_post_meta($product->ID, '_product_image_gallery', true));
            $newImageGalleryIds = [];
            foreach ($imageGalleryIds as $imageGalleryId) {
                $newImageGalleryIds[] = !empty($this->importPosts[$imageGalleryId]) ? $this->importPosts[$imageGalleryId] : $imageGalleryId;
            }
            update_post_meta($product->ID, '_product_image_gallery', implode(',', $newImageGalleryIds));
        }

        return true;
    }

    private function updateImportData($importData, $includeMedia = false) {
        $terms = $importData['terms'];

        if (!empty($terms['product_cat'])) {
            foreach ($terms['product_cat'] as $slug) {
                $term = get_term_by('slug', $slug, 'product_cat');
                $this->updateProductDataAfterImport($term);
            }
        }

        if (!$includeMedia) {
            $posts = get_posts(['post__in'=>array_values($this->importPosts), 'post_type'=>'any', 'posts_per_page'=>-1]);
            foreach ($posts as $post) {
                $thumbId = get_post_thumbnail_id($post);
                if ($thumbId) {
                    set_post_thumbnail($post, $this->importPosts[$thumbId]);
                }
            }
        }

        return true;
    }

    private function getAttachmentIdByFilename($filename) {
        $name = preg_replace('/\.(jpg|png|gif|bmp|svg|jpeg)/', '', $filename);
        $posts = get_posts(['post_type'=>'attachment', 'name'=>$name, 'post_mime_type' => 'image', 'posts_per_page'=>-1]);
        return !empty($posts) ? $posts[0]->ID : false;
    }

    /*
    private function uploadDummyAttachments($postIds) {
        if (empty($postIds)) return [];

        $items = [];

        foreach ($postIds as $postId) {
            if (!empty($this->dummyList) && $this->dummyList[$postId]) {
                $filename = $this->dummyList[$postId];
                $attachmentId = $this->getAttachmentIdByFilename($filename);

                if ($attachmentId) {
                    $items[$postId] = $attachmentId;
                } else {
                    $file = THEGEM_BLOCKS_DIR . 'data/dummy/' . $filename;

                    if (file_exists(wp_upload_dir()['path'].'/'.$filename)) {
                        unlink(wp_upload_dir()['path'].'/'.$filename);
                    }

                    $tmpFile = wp_upload_dir()['basedir'].'/'.$filename;

                    if (!copy($file, $tmpFile)) {
                        continue;
                    }

                    $file_array = [
                        'name' => $filename,
                        'tmp_name' => $tmpFile,
                        'error' => 0,
                        'size' => filesize($tmpFile),
                    ];

                    $items[$postId] = media_handle_sideload($file_array);
                }
            }
        }

        return $items;
    }
    */

    public function isCustomPostTitle() {
        $isIssetCategory = false;
        foreach ($this->categories as $category) {
            if ($category['name']=='custom-title') {
                $isIssetCategory = true;
                break;
            }
        }

        if ($isIssetCategory) {
            if (isset($_REQUEST['post'])) {
                return $this->isCustomPostTitle = get_post_type($_REQUEST['post']) == 'thegem_title';
            }

            if (isset($_REQUEST['post_type'])) {
                return $this->isCustomPostTitle = $_REQUEST['post_type'] == 'thegem_title';
            }
        }

        return $this->isCustomPostTitle = false;
    }

    public function getDefaultCategory() {
        $category = 'all';

        if ($this->isCustomPostTitle) {
            $category = 'custom-title';
            $this->updateOption(static::OPTION_IS_DARK_MODE, false);
        }

        return $category;
    }

    private function checkPurchaseCode() {
        $checkDt = $this->options[static::OPTION_CHECK_PURCHASE_CODE_DT];
        if ($checkDt && $checkDt >= (new DateTime())->modify('-'.static::DEMO_CONTENT_BLOCKED_TIME.' minute')->format('Y-m-d H:i:s')) {
            return true;
        }

        $response = wp_remote_get(add_query_arg(['code' => $this->getPurchaseCode(), 'site_url' => get_site_url()], 'http://democontent.codex-themes.com/av_validate_code.php'), ['timeout' => 20]);

        if (!is_wp_error($response)) {
            $responseBody = json_decode($response['body'], true);
            if (is_array($responseBody) && isset($responseBody['result']) && $responseBody['result'] && isset($responseBody['item_id']) && $responseBody['item_id'] === '16061685') {
                $this->updateOption(static::OPTION_CHECK_PURCHASE_CODE_DT, (new DateTime())->format('Y-m-d H:i:s'));
                return true;
            } else {
                wp_send_json(['result'=>false, 'content'=>$this->getContentActivationFailed()]);
                wp_die();
            }
        } else {
            wp_send_json(['result'=>false, 'content'=>$this->getContentErrorConnection()]);
            wp_die();
        }
    }

    private function getPurchaseCode() {
        $theme_options = get_option('thegem_theme_options');
        if($theme_options && isset($theme_options['purchase_code'])) {
            return $theme_options['purchase_code'];
        }
        return false;
    }

    private function getContentActivationFailed() {
        $output = '<i class="thegem-blocks-notification-icon tgb-icon-key"></i>';
        $output .= '<p>'.__('Theme activation failed. Please activate TheGem using your purchase code in order to be able to use TheGem Blocks.', 'thegem').'</p>';
        $output .= '<a href="'.esc_url(admin_url('admin.php?page=thegem-theme-options#activation')).'" class="thegem-blocks-notification-btn"><i class="tgb-icon-checkbox-marked-outline"></i> '.__('Activate now', 'thegem').'</a>';
        return $output;
    }

    private function getContentErrorConnection() {
        $output = '<i class="thegem-blocks-notification-icon tgb-icon-close-network"></i>';
        $output .= '<p>'.sprintf(__('No internet connection or your firewall is blocking<br> access to blocks. Check your internet connection<br> or adjust your firewall settings. If nothing helps,<br> please <a href="%s">contact our support</a>.', 'thegem'), 'https://codexthemes.ticksy.com/').'</p>';
        return $output;
    }

    private function getContentErrorInstallPlugins($plugin) {
        $output = '<i class="thegem-blocks-notification-icon tgb-icon-info-outline"></i>';
        $output .= '<p>'.sprintf(__('To insert this block please install and activate <br> "%s" plugin first, then save and reload your page.', 'thegem'), $plugin['name']).'</p>';
        $output .= '<a href="'.esc_url($plugin['install_url']).'" target="_blank" class="thegem-blocks-notification-btn"><i class="tgb-icon-checkbox-marked-outline"></i> '.__('Install now', 'thegem').'</a>';
        return $output;
    }


    public function getAdditionalPluginList() {
        static $items;

        if (!$items) {
            $items = [
                static::PLUGIN_CF7 => [
                    'name'=>__('Contact Form 7', 'thegem'),
                    'install_url'=>admin_url('admin.php?page=install-required-plugins')
                ],
                static::PLUGIN_YIKES => [
                    'name'=>__('Easy Forms for Mailchimp', 'thegem'),
                    'install_url'=>admin_url('admin.php?page=install-required-plugins')
                ],
                static::PLUGIN_WOO => [
                    'name'=>__('WooCommerce', 'thegem'),
                    'install_url'=>admin_url('plugin-install.php?tab=plugin-information&plugin=woocommerce')
                ]
            ];
        }

        return $items;
    }

    private function checkInstallAdditionalPlugins($template) {
        $plugin = null;

        if (!$plugin && $template['is_wpcf7'] === true && !is_plugin_active(static::PLUGIN_CF7)) {
            $plugin = static::PLUGIN_CF7;
        }

        if (!$plugin && $template['is_mailchimp'] === true && !is_plugin_active(static::PLUGIN_YIKES)) {
            $plugin = static::PLUGIN_YIKES;
        }

        if (!$plugin && $template['is_woocommerce'] === true && !is_plugin_active(static::PLUGIN_WOO)) {
            $plugin = static::PLUGIN_WOO;
        }

        if ($plugin) {
            $pluginData = $this->getAdditionalPluginList()[$plugin];

            wp_send_json([
                'result'=>false,
                'content'=>$this->getContentErrorInstallPlugins($pluginData)
            ]);

            wp_die();
        }

        return true;
    }

    private function importMailChimpForms($ids=[]) {
        if (empty($ids)) return [];

        $interface = yikes_easy_mailchimp_extender_get_form_interface();

        $formIds = [];
        foreach ($interface->get_all_forms() as $itemForm) {
            $formIds[$itemForm['unique_id']] = $itemForm;
        }

        foreach ($ids as $id) {
            $importForm = $this->mailChimpForms[$id];

            if (empty($importForm)) {
                continue;
            }

            if (!empty($formIds[$id])) {
                $this->importMailChimpForms[$id] = $formIds[$id]['id'];
            } else {
                $newFormId = $interface->create_form($importForm);
                $this->importMailChimpForms[$id] = $newFormId;
            }
        }
        
        return $this->importMailChimpForms;
    }

    public function getFavoritesEmptyText() {
        return sprintf(__('No Favorites added.<br> Use %s sign to add TheGem Blocks of your choice to Favorites.', 'thegem'), '<i class="tgb-icon-star-outline"></i>');
    }

    public function getTemplatesEmptyText() {
        return __('No TheGem Blocks found. <br> Try different search...', 'thegem');
    }    
}