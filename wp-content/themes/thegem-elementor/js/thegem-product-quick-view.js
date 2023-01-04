(function ($) {
    $(function () {
        $(document.body).on('click', '.product .quick-view-button, .product-quick-view-navigation a', function (e) {
            e.preventDefault();
            var $button = $(this);
            $.fancybox.close();
            $.fancybox.open({
                type: 'ajax',
                src: thegem_woo_data.ajax_url,
                ajax: {
                    settings: {
                        method: 'POST',
                        data: {
                            ajax_nonce: thegem_woo_data.ajax_nonce,
                            action: 'thegem_product_quick_view',
                            product_id: $(this).data('product-id')
                        }
                    }
                },
                slideClass: 'woo-modal-product',
                baseClass: "product-quick-view",
                afterShow: function (el) {
                    var quickViewGalleryType = $('.product-quick-view .single-product').attr("data-quick-view");

                    if (quickViewGalleryType === 'legacy'){
                        legacyQuickViewGallery(el);
                    } else {
                        defaultQuickViewGallery(el);
                    }
                }
            }, {
                spinnerTpl: '<div class="gem-fancybox-preloader"><div class="preloader-spin"></div></div>',
                caption: '<div class="product-navigation-caption"></div>'
            });
        });

        function defaultQuickViewGallery(el) {
            $('.gem-combobox').combobox();
            el.current.$content.buildQuickViewGallery();
            el.current.$content.updateProductGalleries();
            $('.variations_form', el.current.$content).each(function () {
                var $form = $(this);
                $form.on('reset_image show_variation', function () {
                    el.removeEvents();
                    window.setTimeout(function () {
                        el.addEvents();
                    }, 100);
                });
                $form.on('show_variation', function (e, variation) {
                    if(variation.image_id) {
                        var $product_content = $(this).closest('.single-product-content');
                        var $gallery = $product_content.find('.product-gallery').eq(0);
                        var $mainCarousel = $gallery.find('.product-gallery-slider');
                        if($gallery.length) {
                            var $gallery_item = $gallery.find('.product-gallery-slider .product-gallery-slider-item[data-image-id="'+variation.image_id+'"]').parent('.owl-item').index();
                            $mainCarousel.trigger('to.owl.carousel', [$gallery_item, 300, true]);
                        }
                    }
                });
                $form.wc_variation_form();
                $form.on('change', '.variations select', function (event) {
                    var $text = $(this).closest('.combobox-wrapper').find('.combobox-text');
                    $text.text($('option:selected', $(this)).text());
                });
            });
        }

        function legacyQuickViewGallery(el) {
            $('.gem-combobox').combobox();
            el.current.$content.buildSimpleGalleries();
            el.current.$content.updateSimpleGalleries();
            $('.variations_form', el.current.$content).each(function () {
                var $form = $(this);
                $form.on('reset_image show_variation', function () {
                    el.removeEvents();
                    window.setTimeout(function () {
                        el.addEvents();
                    }, 100);
                });
                $form.on('show_variation', function (e, variation) {
                    if (variation.image && variation.image.src) {
                        var $g_item = $('.gem-quick-view-gallery .gem-gallery-item img[src="' + variation.image.src + '"]', el.current.$content).closest('.gem-gallery-item');
                        $('.gem-quick-view-gallery .gem-gallery-items-carousel', el.current.$content).trigger('slideTo', [$g_item]);
                    }
                });
                $form.wc_variation_form();
                $form.on('change', '.variations select', function (event) {
                    var $text = $(this).closest('.combobox-wrapper').find('.combobox-text');
                    $text.text($('option:selected', $(this)).text());
                });
            });
        }
    });
})(jQuery);