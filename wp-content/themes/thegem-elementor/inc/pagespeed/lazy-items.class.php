<?php

class TGM_PageSpeed_Lazy_Items {
    private $desktopEnable = false;
    private $mobileEnable = false;
    private $visibilityOffset = 0;
    private $cacheEnabled = false;
    private $ignoredBlocksIndex = 0;
    private $ignoredBlocksList = array();
    private $deferredStyles = array();

    public function __construct() {
        if ($this->canProcessContent()) {
            $this->initOptions();
            $this->initHooks();
        }
    }

    private function initOptions() {
        $this->desktopEnable = thegem_get_option('pagespeed_lazy_images_desktop_enable') == 1;
        $this->mobileEnable = thegem_get_option('pagespeed_lazy_images_mobile_enable') == 1;
        $this->visibilityOffset = floatval(thegem_get_option('pagespeed_lazy_images_visibility_offset'));
        $this->cacheEnabled = thegem_get_option('pagespeed_lazy_images_page_cache_enabled') == 1;
    }

    private function initHooks() {
        if (!$this->needProcessContent()) {
            return;
        }

        if (did_action('init') > 0) {
            $this->init();
        } else {
            add_action('init', array($this, 'init'), 9999);
        }

        add_action('wp_head', array($this, 'printHeadStyles'), 0);
        add_action('wp_head', array($this, 'printHeadScripts'), 0);
    }

    public function init() {
        ob_start(array($this, 'obCallback'));
    }

    public function printHeadStyles() {
        echo '<style>';
        echo '.tgpli-background-inited { background-image: none !important; }';
        echo 'img[data-tgpli-image-inited] { display:none !important;visibility:hidden !important; }';
        echo '</style>';
    }

    public function printHeadScripts() {
        ?>
        <script type="text/javascript">
            window.tgpLazyItemsOptions = {
                visibilityOffset: <?=floatval($this->visibilityOffset)?>,
                desktopEnable: <?php echo $this->desktopEnable ? 'true' : 'false' ?>,
                mobileEnable: <?php echo $this->mobileEnable ? 'true' : 'false' ?>
            };
            window.tgpQueue = {
                nodes: [],
                add: function(id, data) {
                    data = data || {};
                    if (window.tgpLazyItems !== undefined) {
                        if (this.nodes.length > 0) {
                            window.tgpLazyItems.addNodes(this.flushNodes());
                        }
                        window.tgpLazyItems.addNode({
                            node: document.getElementById(id),
                            data: data
                        });
                    } else {
                        this.nodes.push({
                            node: document.getElementById(id),
                            data: data
                        });
                    }
                },
                flushNodes: function() {
                    return this.nodes.splice(0, this.nodes.length);
                }
            };
        </script>
        <?php
        echo '<script type="text/javascript" async src="' . get_template_directory_uri() . '/js/thegem-pagespeed-lazy-items.js"></script>';
    }

    public function needProcessContent() {
        return apply_filters('thegem_lazy_items_need_process_content', !$this->cacheEnabled ? ($this->isMobile() ? $this->mobileEnable : $this->desktopEnable) && !$this->excludedDevices() : ($this->mobileEnable || $this->desktopEnable) && !$this->excludedDevices());
    }

    private function isMobile() {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
    }
    private function excludedDevices() {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        return false; // preg_match('/(iPhone|iPad).*OS 14/i',$useragent) || preg_match('/Version.*14.*Safari/i',$useragent);
    }

    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    private function canProcessContent() {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
			return false;
		}

        if (defined('WP_ADMIN')) {
			return false;
		}

        if (defined('WP_BLOG_ADMIN')) {
			return false;
		}

        if (defined('DOING_AJAX') || $this->isAjaxRequest()) {
			return false;
		}

        if (defined('DOING_CRON')) {
			return false;
		}

        if (defined('APP_REQUEST')) {
			return false;
		}

        if (defined('XMLRPC_REQUEST')) {
			return false;
		}

        if (defined('SHORTINIT') && SHORTINIT) {
			return false;
		}

        return true;
    }

    public function obCallback($buffer) {
        $this->processIgnoredBlocks($buffer);
        $this->processImages($buffer);
        $this->processStyleBackgrounds($buffer);
        $this->processIframes($buffer);
        $this->returnIgnoredBlocks($buffer);
        $this->processIconStyles($buffer);
        $this->processDeferredStyles($buffer);
        return $buffer;
    }

    private function processIgnoredBlocks(&$buffer) {
        $buffer = preg_replace_callback(array(
            '%<!--(?:[^-]++|-)*?-->%si',
            '%<script\b(?:"(?:[^"\\\\]++|\\\\.)*+"|\'(?:[^\'\\\\]++|\\\\.)*+\'|[^>"\']++)*>(?:[^<]++|<)*?</\s*script\s*>%si',
            '%<!\[CDATA\[(?:[^\]]++|\])*?\]\]>%si'
        ), array($this, 'processIgnoredBlock'), $buffer);
    }

    private function processImages(&$buffer) {
        $buffer = preg_replace_callback('%((<source[^>]*>\s*)*)(<img[^>]*>)%i', array($this, 'processImage'), $buffer);
    }

    private function processStyleBackgrounds(&$buffer) {
        $buffer = preg_replace_callback('%<div[^>]*?style="[^"]*?url([^)]*)[^"]*"[^>]*>%i', array($this, 'processStyleBackground'), $buffer);
    }

    private function processIframes(&$buffer) {
        $buffer = preg_replace_callback('%(<iframe[^>]*>\s*</iframe>)%i', array($this, 'processIframe'), $buffer);
    }

    private function processIconStyles(&$buffer) {
        $buffer = preg_replace_callback("%<link[^>]*?href=('|\")([^'\"]*?(icons\-(fontawesome|elegant|material)\.css)[^'\"]*)('|\")[^>]*>%i", array($this, 'processIconStyle'), $buffer);
    }

    private function returnIgnoredBlocks(&$buffer) {
        if (empty($this->ignoredBlocksList)) {
            return;
        }
        $buffer = preg_replace(array_reverse(array_keys($this->ignoredBlocksList)), array_reverse(array_values($this->ignoredBlocksList)), $buffer);
    }

    public function processIgnoredBlock($matches) {
        if (empty($matches[0])) {
            return $matches[0];
        }

        $key = 'TGM_PAGESPEED_LAZY_ITEMS_INORED_BLOCK_' . $this->ignoredBlocksIndex++ . '_' . $this->ignoredBlocksIndex;
        $this->ignoredBlocksList[ '%' . $key . '%' ] = $matches[0];
        return $key;
    }

    public function processStyleBackground($matches) {
        $result = $matches[0];

        $boxAttributes = array();
        if (preg_match_all('%\s(class|id|style)="([^"]*)"%i', $result, $boxMatches) && is_array($boxMatches[1])) {
            foreach ($boxMatches[1] as $index => $attr) {
                $boxAttributes[ $attr ] = $boxMatches[2][ $index ];
            }
        }

        if (preg_match('%url\(data:%i', $boxAttributes['style'])) {
            return $matches[0];
        }

        $additionalAtttributes = array();

        $backgroundClasses = 'tgpli-inited tgpli-background-inited';
        if (!empty($boxAttributes['class'])) {
            $result = preg_replace('%\sclass="%', ' class="' . $backgroundClasses . ' ', $result);
        } else {
            $additionalAtttributes[] = 'class="' . $backgroundClasses . '"';
        }

        if (!empty($boxAttributes['id'])) {
            $boxId = $boxAttributes['id'];
        } else {
            $boxId = 'tgpli-' . uniqid();
            $additionalAtttributes[] = 'id="' . $boxId . '"';
        }

        if (count($additionalAtttributes) > 0) {
            $lastQuoteIndex = $this->getLastImageQuote($result);
            if ($lastQuoteIndex !== false) {
                $result = mb_substr($result, 0, $lastQuoteIndex + 1) . ' ' . implode(' ', $additionalAtttributes) . ' ' . mb_substr($result, $lastQuoteIndex + 1);
            }
        }

        $result .= "<script>window.tgpQueue.add('" . $boxId . "')</script>";

        return $result;
    }

    public function processImage($matches) {
        // return '%%%%%' . var_export($matches, true);

        $result = $matches[3];

        $imageAttributes = array();
        if (preg_match_all('%\s(src|srcset|style|class|id|data\-ww)="([^"]*)"%i', $result, $imageMatches) && is_array($imageMatches[1])) {
            foreach ($imageMatches[1] as $index => $attr) {
                $imageAttributes[ $attr ] = $imageMatches[2][ $index ];
            }
        }

        if (
            empty($imageAttributes['src']) ||
            preg_match('%^data:%', $imageAttributes['src']) ||
            !empty($imageAttributes['data-ww']) ||
            (!empty($imageAttributes['class']) && preg_match('%(rev-slidebg|tgp-exclude)%i', $imageAttributes['class']))
        ) {
            return $matches[0];
        }

        $additionalAtttributes = array();
        // $additionalAtttributes[] = 'style="' . (!empty($imageAttributes['style']) ? $imageAttributes['style'] : '') . ';display:none;visibility:hidden;"';
        $additionalAtttributes[] = 'data-tgpli-inited';
        $additionalAtttributes[] = 'data-tgpli-image-inited';

        if (!empty($imageAttributes['id'])) {
            $imgId = $imageAttributes['id'];
        } else {
            $imgId = 'tgpli-' . uniqid();
            $additionalAtttributes[] = 'id="' . $imgId . '"';
        }

        $result = preg_replace('%\s(src|srcset)=%', ' data-tgpli-${1}=', $result);

        $lastQuoteIndex = $this->getLastImageQuote($result);
        if ($lastQuoteIndex !== false) {
            $result = mb_substr($result, 0, $lastQuoteIndex + 1) . ' ' . implode(' ', $additionalAtttributes) . ' ' . mb_substr($result, $lastQuoteIndex + 1);
        }

        $result .= "<script>window.tgpQueue.add('" . $imgId . "'" . (!empty($matches[1]) ? ", { sources: '" . preg_replace(array("%'%", "%\s+%"), array("\'", " "), $matches[1]) . "' }" : "") . ")</script>";

        $result .= '<noscript>' . $matches[0] . '</noscript>';

        return $result;
    }

    public function processIframe($matches) {
        $result = $matches[0];

        $iframeAttributes = array();
        if (preg_match_all('%\s(src|style|class|id)="([^"]*)"%i', $result, $iframeMatches) && is_array($iframeMatches[1])) {
            foreach ($iframeMatches[1] as $index => $attr) {
                $iframeAttributes[ $attr ] = $iframeMatches[2][ $index ];
            }
        }

        if (empty($iframeAttributes['src']) || !preg_match('%(maps|vimeo|youtube)%i', $iframeAttributes['src'])) {
            return $matches[0];
        }

        $additionalAtttributes = array();
        $additionalAtttributes[] = 'data-tgpli-inited';
        $additionalAtttributes[] = 'data-tgpli-iframe-inited';

        if (!empty($iframeAttributes['id'])) {
            $iframeId = $iframeAttributes['id'];
        } else {
            $iframeId = 'tgpli-' . uniqid();
            $additionalAtttributes[] = 'id="' . $iframeId . '"';
        }

        $result = preg_replace('%\s(src)=%', ' data-tgpli-${1}=', $result);

        $lastQuoteIndex = $this->getLastImageQuote($result);
        if ($lastQuoteIndex !== false) {
            $result = mb_substr($result, 0, $lastQuoteIndex + 1) . ' ' . implode(' ', $additionalAtttributes) . ' ' . mb_substr($result, $lastQuoteIndex + 1);
        }

        $result .= "<script>window.tgpQueue.add('" . $iframeId . "')</script>";

        $result .= '<noscript>' . $matches[0] . '</noscript>';

        return $result;
    }

    public function processIconStyle($matches) {
        if (empty($matches[2])) {
            return $matches[0];
        }

        $this->deferredStyles[] = $matches[2];
        return '';
    }

    private function processDeferredStyles(&$buffer) {
        if (count($this->deferredStyles) == 0) {
            $result = '<script type="text/javascript">(function() {window.addEventListener("load",function(){var elem = document.getElementById("thegem-preloader-inline-css"); if (elem!==null) setTimeout(function() {elem.parentNode.removeChild(elem)}, 300); });})();</script>';
            $buffer = preg_replace('%</body>%i', $result . '</body>', $buffer);
            return $buffer;
        }

        $result = '<script type="text/javascript">(function() {';
        $result .= 'var parent = document.getElementById("page");';
        foreach ($this->deferredStyles as $index => $file) {
            $filename = 'deferredFile' . ($index + 1);

            $result .= 'var ' . $filename . ' = document.createElement("link");';
            $result .= $filename . '.rel = "stylesheet";';
            $result .= $filename . '.type = "text/css";';
            $result .= $filename . '.href = "' . $file . '";';
            $result .= 'document.body.appendChild(' . $filename . ');';

            $result .= '';
        }
        $result .= 'window.addEventListener("load",function(){var elem = document.getElementById("thegem-preloader-inline-css"); if (elem!==null) setTimeout(function() {elem.parentNode.removeChild(elem)}, 300); });';

        $result .= '})();';
        $result .= '</script>' . "\n";

        $buffer = preg_replace('%</body>%i', $result . '</body>', $buffer);

        return $buffer;
    }

    private function getLastImageQuote($img) {
        $index = mb_strrpos($img, '"');
        if ($index === false) {
            $index = mb_strrpos($img, "'");
        }
        return $index !== false ? $index : mb_strrpos($img, '"');
    }
}
