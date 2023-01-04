<?php

if (!defined( 'ABSPATH')) {
    die( '-1' );
}

if (!function_exists('thegem_blocks_helper_init')) {
    function thegem_blocks_helper_init() {
        $theGemThemeOptions = get_option('thegem_theme_options');
        //if (is_user_logged_in() && $theGemThemeOptions && isset($theGemThemeOptions['purchase_code'])) {
            $theGemBlocksHelper = new TheGemBlocksHelper();
            $theGemBlocksHelper->init();
        //}
    }
}

add_action('admin_notices', [new TheGemBlocksHelper, 'getPluginAvailableNotice']);
thegem_blocks_helper_init();


class TheGemBlocksHelper {

    CONST VERSION = '1.0.0';
    CONST TAB_NAME = 'thegem_blocks_helper_templates';
    CONST LANDING_PAGE_LINK = 'https://codex-themes.com/thegem/blocks-landing/?utm_source=wp_dashboard&utm_medium=learn_more&utm_campaign=dashboard_blocks';
    CONST COOKIE_NAME_NOTICE = 'thegem_blocks_helper_notice';

    public function init() {
        if (!class_exists('TheGemBlocks')) {
            add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
            add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_embedded_iframe_styles' ] );
        }
        /*
        add_action('admin_init', [$this, 'updatePointers']);

        add_action('vc_backend_editor_enqueue_js_css', [$this, 'enqueueJsCss']);
        add_action('vc_frontend_editor_enqueue_js_css', [$this, 'enqueueJsCss']);
        add_action('vc_load_iframe_jscss', [$this, 'enqueueJsCss']);

        if (!class_exists('TheGemBlocks')) {
            add_filter('vc_get_all_templates', [$this, 'addTab']);
            add_filter('vc_templates_render_category', [$this, 'initTabContent']);
        }
        */
    }

    public function enqueue_editor_scripts() {
        wp_enqueue_script('thegem_blocks_main', plugin_dir_url(__FILE__) . 'assets/js/main.js', array( 'jquery' ), static::VERSION );
        wp_enqueue_style('thegem_blocks_main', plugin_dir_url(__FILE__) . 'assets/css/main.css', [], static::VERSION);
        wp_enqueue_style('thegem_blocks_icons', plugin_dir_url(__FILE__) . 'assets/css/icons.css', [], static::VERSION);

        wp_localize_script('thegem_blocks_main', 'thegem_blocks', ['message'=>
         '<i class="thegem-blocks-notification-icon tgb-icon-info-outline"></i>'.
         '<p>'.sprintf(__('Please install TheGem Blocks plugin to get access<br>to the blocks collection.', 'thegem')).'</p>'.
         '<a href="'.esc_url(admin_url('admin.php?page=install-required-plugins')).'" target="_blank" class="thegem-blocks-notification-btn"><i class="tgb-icon-checkbox-marked-outline"></i> '.__('Install now', 'thegem').'</a>'
        ]
    );
	}

	public function enqueue_embedded_iframe_styles(){
		//wp_enqueue_style( 'template-kit-import-admin', THEGEM_BLOCKS_URL . 'assets/css/main.css', [], filemtime( THEGEM_BLOCKS_DIR . 'assets/main.css' ) );
		wp_enqueue_style( 'thegem-blocks-elements-elementor-modal', plugin_dir_url(__FILE__) . 'assets/css/elementor_modal.css', [], static::VERSION );
	}

/*
    public function enqueueJsCss() {
        wp_enqueue_style('thegem_blocks_helper', plugin_dir_url(__FILE__).'assets/css/main.css', [], static::VERSION);
        wp_enqueue_script('thegem_blocks_helper', plugin_dir_url(__FILE__).'assets/js/main.js', ['jquery'], static::VERSION);
        wp_localize_script('thegem_blocks_helper', 'TheGemBlocksHelperOptions', $this->getLocalizeScriptData());
    }

    private function getLocalizeScriptData() {
        return [
            'texts'=>[
                'vc_welcome_header' => esc_html__('you have blank page. start adding elements, pre-made blocks or saved templates.', 'thegem'),
                'vc_templates_more_layouts' => esc_html__('Add Blocks & Templates', 'thegem'),
                'vc_ui_help_block' => sprintf(wp_kses(__('Don\'t know where to start? Visit TheGem <a href="%s" target="_blank">documentation</a> or Page Builder <a href="%s" target="_blank">knowledge base</a>.', 'thegem'), ['a' => ['href' => [], 'target'=>[]]]), 'https://codex-themes.com/thegem/documentation/', 'https://kb.wpbakery.com'),
            ]
        ];
    }

    public function updatePointers() {
        if (is_admin()) {
            foreach (vc_editor_post_types() as $post_type) {
                add_filter('vc_ui-pointers-'.$post_type, [$this, 'updateBackendPointers']);
            }
        }

        vc_is_frontend_editor() && add_filter('vc-ui-pointers', [$this, 'updateFrontendPointers']);
    }

    public function updateBackendPointers($pointers) {
        if (!empty($pointers) && !empty($pointers['vc_pointers_backend_editor'])) {
            $messages = $pointers['vc_pointers_backend_editor']['messages'];
            foreach ($messages as $k=>&$message) {
                if ($message['target'] == '#vc_templates-editor-button, #vc-templatera-editor-button') {
                    $message['target'] = '#vc_add-new-element';
                    $message['options']['content'] = $this->getPointerContentForAddElement();
                    $message['options']['position'] = ['edge' => 'top', 'align' => 'left'];
                }
            }

            $pointers['vc_pointers_backend_editor']['messages'] = $messages;
        }

        return $pointers;
    }

    public function updateFrontendPointers($pointers) {
        if (!empty($pointers) && !empty($pointers['vc_pointers_frontend_editor'])) {
            $messages = $pointers['vc_pointers_frontend_editor']['messages'];
            foreach ($messages as $k=>&$message) {
                if ($message['target'] == '#vc_add-new-element') {
                    $message['options']['content'] = $this->getPointerContentForAddElement();
                }
            }

            $pointers['vc_pointers_frontend_editor']['messages'] = $messages;
        }

        return $pointers;
    }

    private function getPointerContentForAddElement() {
        return sprintf('<h3>%s</h3><p>%s</p>', esc_html__('Add Elements or TheGem Blocks', 'thegem'), esc_html__('Add new content elements, pre-made TheGem blocks or saved templates', 'thegem'));
    }

    public function addTab($data) {
        $items[] = [
            'category' => static::TAB_NAME,
            'category_name' => esc_html__('TheGem Blocks', 'thegem'),
            'category_weight' => 1,
            'templates' => []
        ];

        foreach ($data as $item) {
            if ($item['category'] == 'my_templates') {
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

    private function getTabContent() { ?>
        <div class="vc_column vc_col-sm-12 thegem-blocks-helper-box">
            <p><?= __('Please install TheGem Blocks plugin to get the access to the blocks collection.', 'thegem'); ?></p>
            <a href="<?= esc_url(admin_url('admin.php?page=install-required-plugins')) ?>" class="vc_general vc_ui-button vc_ui-button-size-sm vc_ui-button-action vc_ui-button-shape-rounded"><?= __('Install TheGem Blocks', 'thegem'); ?></a>
        </div>
    <?php return true; }
*/
    public function getPluginAvailableNotice() {
        $thegem_theme = wp_get_theme(wp_get_theme()->get('Template'));
        $min_version = '4.5.8';        
        if (version_compare($thegem_theme->get('Version'), $min_version) >= 0 && !thegem_is_plugin_active('thegem-blocks-elementor/thegem-blocks-elementor.php') && !isset($_COOKIE[static::COOKIE_NAME_NOTICE])) {
            wp_enqueue_style('thegem_blocks_helper_notice', plugin_dir_url(__FILE__).'assets/css/notice.css', []);
            ?>

            <div id="thegem-blocks-notice" class="notice notice-success is-dismissible">
                <div class="thegem-blocks-notice-inner" style="display: flex; align-items: center; width: 100%;">
                    <div class="thegem-blocks-notice-logo">
                        <img src="<?= plugin_dir_url(__FILE__).'assets/img/logo.svg' ?>" width="40px" alt="thegem-blocks-logo">
                    </div>

                    <div class="thegem-blocks-notice-info">
                        <p><b><?= __('Meet TheGem Blocks for Elementor (free)', 'thegem')?></b></p>
                        <p><?= __('Design faster. Convert better. TheGem Blocks plugin is a huge collection of pre-designed page sections like hero, about, services etc. Speed up your workflow. Create unique layouts. Mix & match on the fly.', 'thegem') ?></p>
                        <p><a href="<?= esc_url(static::LANDING_PAGE_LINK) ?>" target="_blank"><b><?= __('Learn more...', 'thegem') ?></b></a></p>
                    </div>

                    <a href="<?= esc_url($this->getInstallPluginUrl()) ?>" class="button button-primary"><?= __('Install now', 'thegem'); ?></a>
                </div>
            </div>

            <script type="text/javascript">
                window.addEventListener('load', function() {
                    (function ($) {
                        $('#thegem-blocks-notice').on('click', '.notice-dismiss', function() {
                            let dt = new Date();
                            let days = 30;
                            dt.setDate(dt.getDate() + days);

                            let name = '<?= esc_attr(static::COOKIE_NAME_NOTICE) ?>';
                            let value = encodeURIComponent('<?= esc_attr(static::VERSION); ?>') + ("; expires=" + dt.toUTCString());
                            document.cookie = name + "=" + value;
                        });
                    })(window.jQuery);
                });
            </script>

        <?php }
    }

    public function getInstallPluginUrl() {
        $theme = wp_get_theme('thegem');
        /*
        if ($theme->exists() && $theme->get('Version') < '4.3.0') {
            return admin_url('update-core.php');
        }
        */

        return admin_url('admin.php?page=install-required-plugins');
    }

}
