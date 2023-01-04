(function ($) {
    "use strict";

    function TheGemBlocks(options) {
        this.options = Object.assign({}, options);
        this.activeTabName  = this.options.tab_name;

        this.$panelTemplates = $('#vc_ui-panel-templates');

        this.$templatesEmptyNotification = null;
        this.$favoritesEmptyNotification = null;

        this.$importTemplate = null;
        this.isLoadingTemplate = false;
        this.isFrontend = window.vc_iframe_src !== undefined;

        this.activeCategory = this.options.default_category;
        this.$items = [];
        this.page = 1;
        this.perPage = parseInt(this.options.per_page);
        this.loadingItems = false;
        this.spinnerClassName = 'thegem-blocks-spinner';
        this.favorites = this.options.favorites;
        this.isDarkMode = this.options.is_dark_mode;
        this.isCustomPostTitle = this.options.is_custom_post_title;

        this.init();
    }

    TheGemBlocks.prototype = {
        init: function() {
            let self = this;

            $(function() {
                var t = $("#tmpl-elementor-add-section");
                if (t.length > 0) {
                    var n = t.text();
                    n = n.replace('<div class="elementor-add-section-drag-title', '<div class="elementor-add-section-area-button elementor-add-thegem-blocks-button" title="TheGem Blocks"><i class="eicon-folder"></i></div><div class="elementor-add-section-drag-title'), t.text(n), elementor.on("preview:loaded", (function() {
                        jQuery(elementor.$previewContents[0].body).on("click", ".elementor-add-thegem-blocks-button", function(event) {
                            self.buttonEvent = event;
                            self.show();
                        })
                    }))
                }
            });

            $(document).on('click', '.thegem-blocks-template-item-add', function(e) {
                e.preventDefault();

                self.$importTemplate = $(this).closest('.thegem-blocks-template-item');

                if (self.isLoadingTemplate) return;

                self.import().then((data)=> {
                    if (!data.result) {
                        if (data.content) {
                            self.showNotification(data.content);
                        } else {
                            alert('TheGem Blocks: Error import block!');
                            console.log(data);
                        }

                        self.removeLoaderInsert();
                        return;
                    }

                    self.insertTemplate(data.blockContent);
                }, (error)=> {
                    console.log(error);
                    self.removeLoaderInsert();
                });
            });

            $(document).on('click', '.thegem-blocks-categories-list a', function(e) {
                self.setCategory($(this).data('name'));
                e.preventDefault();
            });

            $(document).on('click', '.thegem-blocks-template-item-favorite', function(e) {
                self.changeFavorite(this);
                e.preventDefault();
            });

            $(document).on('change', '#thegem-blocks-include-media-input', function () {
                let includeMedia = $(this).is(":checked");
                $.post(self.options.ajax_url, {
                    action: 'thegem_blocks_update_include_media',
                    ajax_nonce: self.options.ajax_nonce,
                    include_media: includeMedia ? 1 : 0
                });
            });

            $('.vc_ui-minimize-button[data-vc-ui-element="button-minimize"]').on('click', function () {
                let $body =  $('body');
                if ($('.thegem-blocks-panel').hasClass('vc_minimized')) {
                    $body.css('overflow', 'hidden')
                } else {
                    $body.css('overflow', 'auto');
                }
            });

            $(document).on('change', '#thegem-blocks-mode-input', function () {
                self.updateMode($(this).is(":checked"));
            });

        },

        show: function() {
            if ($("#thegem-blocks-wrapper").length>0) {
                $("#thegem-blocks-wrapper").show();
            } else {
                $('body').append($(this.options.mainHtml));
            }

            this.$templates = $(this.options.templates);
            this.$searchField = $('#thegem-blocks-templates-filter');
            this.$templatesContainer = $('.thegem-blocks-template-items');
            this.$templatesScrollContainer = $('.thegem-blocks-right-box');
            this.$categoriesContainer = $('.thegem-blocks-categories-list');
            let self= this;

            jQuery('#thegem-blocks-close').on('click', function() {self.hide();});
    
            this.setCategory(this.activeCategory);
            this.initSearch();
        },

        hide: function() {
            $("#thegem-blocks-wrapper").hide();

            this.hideNotification();

            if (this.$searchField.val().length) {
                this.clearSearch();
            }

            this.resetFilter();
        },

        setCategory: function(name) {
            let $category = this.$categoriesContainer.find('a[data-name="'+name+'"]');
            this.activeCategory = name;

            if ($category.hasClass('active') || name===undefined) {
                return;
            }

            this.resetFilter();
            $category.addClass('active');

            this.filterItems();
            this.initGrid();

            this.updateStateModeControl($category);

            if (this.$searchField.val().length > 0) {
                this.$searchField.val(null);
            }
        },

        filterItems: function() {
            let self = this;
            self.$items = self.$templates;

            if (['all', 'favorite'].indexOf(self.activeCategory) === -1) {
                self.$items = self.$templates.filter(function(idx, item) {
                    return $(item).data('category') === self.activeCategory;
                });
            }

            if (self.activeCategory === 'favorite') {
                self.$items = self.$templates.filter(function(idx, item) {
                    return self.favorites.includes($(item).data('id'));
                });
            }
        },

        resetFilter: function() {
            this.$items = [];
            this.page = 1;

            $('li > a', this.$categoriesContainer).removeClass('active');

            this.hideEmptyNotification();
        },

        initGrid: function () {
            let self = this;
            let $grid = this.$templatesContainer.html(self.$items.slice(0, self.perPage));

            let masonryOptions = {
                itemSelector: '.thegem-blocks-template-item',
                visibleStyle: { transform: 'translateY(0)', opacity: 1 },
                hiddenStyle: { transform: 'translateY(100px)', opacity: 0 },
                transitionDuration: '0.4s'
            };

            if ($grid.data('masonry')!==undefined) {
                $grid.find('.thegem-blocks-template-item:hidden').css('display', '');
                $grid.masonry('destroy');
            }

            if ($grid.hasClass('loaded')) {
                $grid.removeClass('loaded');
            }

            if (this.$templatesScrollContainer.find('> .'+this.spinnerClassName).length === 0) {
                self.$templatesScrollContainer.prepend('<span class="'+self.spinnerClassName+'"></span>');
            }

            imagesLoaded($grid, function() {
                self.$templatesScrollContainer.find('.'+self.spinnerClassName).fadeOut(300, function () {
                    this.remove();

                    if (self.$items.length === 0) {
                        if (self.activeCategory==='favorite') {
                            self.showFavoritesEmptyNotification();
                            return;
                        }

                        self.showTemplatesEmptyNotification();
                    }
                });

                $grid.masonry(masonryOptions);
                $grid.addClass('loaded');
            });

            self.$templatesScrollContainer.on('scroll', function() {
                if (self.loadingItems) return;

                if (this.scrollTop > 0 && this.scrollTop + this.clientHeight >= this.scrollHeight) {

                    if (self.$items.length <= self.page * self.perPage) {
                        return;
                    }

                    let start = self.page * self.perPage;
                    let end = start + self.perPage;

                    let $newItems =  self.$items.slice(start, end);
                    $newItems.each(function (idx, item) {$(item).addClass('hide');});
                    $grid.append($newItems);

                    self.loadingItems = true;
                    $grid.addClass('loading-new-items').append('<div class="'+self.spinnerClassName+'"></div>');

                    imagesLoaded($grid, function() {
                        $grid.removeClass('loading-new-items').find('> .'+self.spinnerClassName).remove();
                        self.loadingItems = false;

                        $newItems.each(function (idx, item) {$(item).removeClass('hide');});
                        $grid.masonry('appended', $newItems);
                        self.page++;
                    });
                }
            });

        },

        initSearch: function() {
            let self = this;
            let searchTimer = null;

            this.$searchField.on('input', function(e) {
                let $el = $(e.currentTarget);
                clearTimeout(searchTimer);

                searchTimer = setTimeout(function() {
                    $el.val().length ? self.search($el.val()) : self.clearSearch();
                }, 400);
            });
        },

        search: function(value) {
            value = value.toLowerCase();

            //if (this.isActiveTab())  {
                this.activeCategory = 'all';
                this.resetFilter();

                this.$items = this.$templates.filter(function(idx, item) {
                    return $(item).data('name').toLowerCase().match(value);
                });

                this.initGrid();
            //} else {
            //    value = value.replace(/[^\w ]+/g, '').replace(/ +/g, '-');
            //    this.$panelTemplates.find('[data-template_name]').hide();
            //    this.$panelTemplates.find('[data-template_name*="'+value+'"]').show();
            //}
        },

        clearSearch: function() {
            this.hideEmptyNotification();

            //if (this.isActiveTab()) {
                this.setCategory('all');
            //} else {
            //    this.$panelTemplates.find('.vc_ui-template').show();
            //}
        },

        changeFavorite: function (elem) {
            let self = this;

            if ($(elem).hasClass('saving')) return;

            let $item = $(elem).closest('.thegem-blocks-template-item');
            let isFavorite = self.favorites.includes($item.data('id'));
            $item.toggleClass('is-favorite');
            
            self.startSaving(elem);

            if (self.activeCategory === 'favorite' && isFavorite) {
                self.$templatesContainer.masonry('remove', $item);
                self.$templatesContainer.masonry('layout');
            }

            $.post(this.options.ajax_url, {
                action: 'thegem_blocks_update_favorite',
                ajax_nonce: this.options.ajax_nonce,
                id: $item.data('id'),
                event: isFavorite ? 'delete' : 'add'
            }).done(function(data) {
                self.endSaving(elem);
                if (data.result) {
                    self.$categoriesContainer.find('.favorite-cnt').text(data.favorites.length);
                    self.favorites = data.favorites;

                    if (data.favorites.length === 0) {
                        self.showFavoritesEmptyNotification();
                    }
                }
            });
        },

        replaceInContent: function() {
            let $panel = $('#vc_ui-panel-templates').addClass('thegem-blocks-panel');
            $('#vc_templates-editor-button').parent('li').addClass('vc_show-mobile');
            $('.vc_ui-panel-header-heading', '.vc_templates-panel').html(this.options.texts.vc_ui_panel_header_heading);
            $('#vc_templates_name_filter').attr('placeholder', this.options.texts.vc_templates_name_filter).removeAttr("data-vc-templates-name-filter");

            let controlMode = $('.thegem-blocks-control-mode');
            let controlModeClone = controlMode.clone();
            controlMode.remove();
            $panel.find('.vc_ui-panel-header').append(controlModeClone);
        },

        startSaving: function (elem) {
            if ($(elem).hasClass('saving')) return;
            $(elem).addClass('saving');
        },

        endSaving: function (elem) {
            if (!$(elem).hasClass('saving')) return;
            $(elem).removeClass('saving');
        },

        import: function() {
            let self = this;
            self.addLoaderInsert();

            return new Promise(function(resolve, reject) {
                $.post(self.options.ajax_url, {
                    action: 'thegem_blocks_import',
                    ajax_nonce: self.options.ajax_nonce,
                    id: self.$importTemplate.data('id'),
                    post_id: elementor.getPreviewContainer().document.id
                }).done(function(data) {
                    resolve(data);
                }).fail(function (error) {
                    reject(error);
                });
            });
        },

        insertTemplate: function(content) {
            let self = this;

            let e = this.buttonEvent;

            var t = 0;
            elementorCommon && (t = jQuery(e.target).parents(".elementor-section-wrap").length > 0 ? jQuery(e.target).parents(".elementor-add-section").index() : -1) >= 0 && jQuery(e.target).parents(".elementor-add-section-inline").remove();

            if ("undefined" != typeof elementor) {
                if ("undefined" != typeof $e) {
                    for (var o = $e.internal("document/history/start-log", {
                            type: "add",
                            title: "Add TheGem Block Content"
                        }), r = 0; r < content.length; r++) {
                            $e.run("document/elements/create", {
                            container: elementor.getPreviewContainer(),
                            model: content[r],
                            options: t >= 0 ? {
                                at: t++
                            } : {}
                            });
                        }
                    $e.internal("document/history/end-log", {
                        id: o
                    })
                } else {
                    var i = new Backbone.Model({
                        getTitle: function() {
                            return "Test"
                        }
                    });
                    elementor.channels.data.trigger("template:before:insert", i);
                    for (var a = 0; a < content.length; a++) elementor.getPreviewView().addChildElement(content[a], t >= 0 ? {
                        at: t++
                    } : null);
                    elementor.channels.data.trigger("template:after:insert", {})
                }
            }


            self.removeLoaderInsert();
            self.hide();
        },

        addLoaderInsert: function () {
            let html = '<div class="thegem-blocks-loader"><span class="'+this.spinnerClassName+'"></span></div>';
            this.$importTemplate.find('.thegem-blocks-template-item-image').append(html);
            this.isLoadingTemplate = true;
        },

        removeLoaderInsert: function () {
            this.$importTemplate.find('.thegem-blocks-loader').remove();
            this.isLoadingTemplate = false;
            this.$importTemplate = null;
        },

        overridingCloseActivePanel: function () {
            let self = this;
            const origCloseActivePanel = window.vc.closeActivePanel;

            window.vc.closeActivePanel = function() {
                if (window.vc.active_panel) {
                    self.hide();
                }
                return origCloseActivePanel.apply(this, arguments);
            };
        },

        showNotification: function(content) {
            if (content===undefined)
                return;

            this.hideNotification();

            $('#thegem-blocks').append('<div id="thegem-blocks-notification"><div class="thegem-blocks-notification-inner">'+content+'</div></div>');
        },

        hideNotification: function() {
            $('#thegem-blocks-notification').remove();
        },

        updateMode: function(isDarkMode) {
            let self = this;
            self.isDarkMode = isDarkMode;

            self.hideEmptyNotification();
            self.$templatesContainer.removeClass('loaded')
            self.$templatesScrollContainer.prepend('<span class="'+self.spinnerClassName+'"></span>');

            $.post(self.options.ajax_url, {
                action: 'thegem_blocks_update_mode',
                ajax_nonce: self.options.ajax_nonce,
                is_dark_mode:  self.isDarkMode ? 1 : 0,
                is_custom_post_title: self.isCustomPostTitle ? 1 : 0
            }).done(function (data) {
                if (data.result) {
                    self.$templates = $(data.templates);
                    self.$categoriesContainer.html(data.categories);
                    self.favorites = data.favorites;

                    self.setCategory(self.activeCategory);
                }
            });
        },

        showFavoritesEmptyNotification: function () {
            let favoritesEmptyHtml = $('<div id="thegem-blocks-favorites-empty" class="thegem-blocks-text-empty"><i class="tgb-icon-info-outline"></i>'+this.options.texts.favorites_empty+'</div>');
            this.$favoritesEmptyNotification = $(favoritesEmptyHtml).appendTo(this.$templatesScrollContainer).fadeIn(300);
        },

        showTemplatesEmptyNotification: function () {
            let templatesEmptyHtml = $('<div id="thegem-blocks-templates-empty" class="thegem-blocks-text-empty"><i class="tgb-icon-info-outline"></i>'+this.options.texts.templates_empty+'</div>');
            this.$templatesEmptyNotification = $(templatesEmptyHtml).appendTo(this.$templatesScrollContainer).fadeIn(300);
        },

        hideEmptyNotification: function () {
            $('#thegem-blocks-favorites-empty').remove();
            /*
            if (this.$favoritesEmptyNotification) {
                this.$favoritesEmptyNotification.remove();
            }
            */

            $('#thegem-blocks-templates-empty').remove();
            /*
            if (this.$templatesEmptyNotification) {
                this.$templatesEmptyNotification.remove();
            }
            */
        },

        updateStateModeControl: function ($category) {
            let $switchMode = $('.thegem-blocks-switch-mode');

            if ($switchMode.hasClass('disabled')) {
                $switchMode.removeClass('disabled');
                $switchMode.find('input').attr('disabled', false);
            }

            if (!this.isDarkMode && $category.data('count-dark') === 0 || this.isDarkMode && $category.data('count-multicolor') === 0) {
                $switchMode.addClass('disabled');
                $switchMode.find('input').attr('disabled', true);
            }
        }

    };

    window.theGemBlocks = new TheGemBlocks(window.TheGemBlocksOptions || {});
})(window.jQuery);
