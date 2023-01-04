<?php

require get_template_directory() . '/inc/pagespeed/lazy-items.class.php';


class TGM_PageSpeed {
    public static function activate() {
        self::activateComponents();
    }

    private static function activateComponents() {
        self::activateLazyItemsComponent();
    }

    private static function activateLazyItemsComponent() {
        $lazyItems = new TGM_PageSpeed_Lazy_Items();
    }
}
