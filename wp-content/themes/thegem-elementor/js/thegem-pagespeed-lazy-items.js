(function(window, document) {
    function isMobileDevice() {
        var a=navigator.userAgent||navigator.vendor||window.opera;
        return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4));
    }
    function excludedDevices() {
        var a=navigator.userAgent||navigator.vendor||window.opera;
        return false; // /(iPhone|iPad).*OS 14/i.test(a) || /Version.*14.*Safari/i.test(a);
    }

    if (!Object.assign) {
        Object.defineProperty(Object, 'assign', {
            enumerable: false,
            configurable: true,
            writable: true,
            value: function(target, firstSource) {
                'use strict';
                if (target === undefined || target === null) {
                    throw new TypeError('Cannot convert first argument to object');
                }

                var to = Object(target);
                for (var i = 1; i < arguments.length; i++) {
                    var nextSource = arguments[i];
                    if (nextSource === undefined || nextSource === null) {
                        continue;
                    }

                    var keysArray = Object.keys(Object(nextSource));
                    for (var nextIndex = 0, len = keysArray.length; nextIndex < len; nextIndex++) {
                        var nextKey = keysArray[nextIndex];
                        var desc = Object.getOwnPropertyDescriptor(nextSource, nextKey);
                        if (desc !== undefined && desc.enumerable) {
                            to[nextKey] = nextSource[nextKey];
                        }
                    }
                }
                return to;
            }
        });
    }

    if (typeof window.CustomEvent !== "function") {
        function CustomEvent( event, params ) {
            params = params || { bubbles: false, cancelable: false, detail: undefined };
            var evt = document.createEvent( 'CustomEvent' );
            evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
            return evt;
        }
        CustomEvent.prototype = window.Event.prototype;
        window.CustomEvent = CustomEvent;
    }

    function getRootScrollTop() {
        return window.pageYOffset || document.documentElement.scrollTop;
    }

    function getNodePosition(node) {
        try {
            var position = node.getBoundingClientRect();
            return {
                left: position.left,
                top: position.top + getRootScrollTop(),
                width: position.width,
                height: position.height
            };
        } catch (err) {
            return null;
        }
    }

    function getAncestorNode(node, className) {
        while (node !== undefined && node !== null && node.nodeName.toUpperCase() !== 'BODY') {
            if (node.classList.contains(className)) {
                return node;
            }
            node = node.parentNode;
        }
        return null;
    }

    function addEvent(node, event, callback, useCapture) {
        if (typeof node.addEventListener == 'function') {
            node.addEventListener(event, callback, useCapture || false);
        } else if (typeof node.attachEvent == 'function') {
            node.attachEvent('on' + event, callback);
        }
    }

    function LazyGroup(node, type) {
        this.node = node;
        this.type = type || 'default';
        this.position = null;
        this.showed = false;
        this.items = [];
        this.itemLoaded = 0;
    }

    LazyGroup.prototype = {
        getType: function() {
            return this.type;
        },

        getItems: function() {
            return this.items;
        },

        allItemsLoaded: function() {
            return this.items.length == this.itemLoaded;
        },

        updatePosition: function() {
            this.position = getNodePosition(this.node);
        },

        getPosition: function() {
            return this.position;
        },

        getNode: function() {
            return this.node;
        },

        addItem: function(item) {
            if (this.isShowed()) {
                this.showItem(item);
            }

            this.items.push(item);
        },

        showItem: function(item) {
            var self = this;
            item.show(function(event) {
                self.itemLoaded++;
                item.loaded(self);
            });
        },

        show: function(force) {
            var self = this;

            if (this.isShowed()) {
                return;
            }

            if (force === undefined) {
                force = false;
            }

            this.showed = true;

            this.items.forEach(function(item) {
                if (!item.isShowed()) {
                    self.showItem(item);
                }
            });

            this.dispatchShowedEvent();
        },

        isShowed: function() {
            return this.showed;
        },

        hasItems: function() {
            return this.items.length > 0;
        },

        dispatchShowedEvent: function() {
            this.node.dispatchEvent(new window.CustomEvent('tgpliVisible', {
                bubbles: true
            }));
        }
    };

    function LazyItem(node, data) {
        this.node = node;
        this.data = data || {};
        this.showed = false;
        this.initType();
    }

    LazyItem.prototype = {
        getNode: function() {
            return this.node;
        },

        show: function(loadedCallback) {
            if (this.isShowed()) {
                return;
            }

            loadedCallback = loadedCallback || null;

            this.showed = true;

            switch (this.getType()) {
                case 'image':
                    this.showImage(loadedCallback);
                    break;

                case 'iframe':
                    this.showIframe(loadedCallback);
                    break;

                case 'custom':
                    this.showCustom(loadedCallback);
                    break;

                default:
                    this.showDefault(loadedCallback);
            }
        },

        showImage: function(loadedCallback) {
            if (loadedCallback !== undefined && typeof loadedCallback === 'function') {
                addEvent(this.node, 'load', function(event) { loadedCallback(event); }, true);
            }

            if (this.data.sources !== undefined && this.data.sources !== null && this.data.sources != '') {
                this.node.insertAdjacentHTML('beforebegin', this.data.sources);
            }

            var srcSet = this.node.getAttribute('data-tgpli-srcset');
            if (srcSet) {
                this.node.setAttribute('srcset', srcSet);
            }

            var src = this.node.getAttribute('data-tgpli-src');
            if (src) {
                this.node.src = src;
            }

            this.node.removeAttribute('data-tgpli-image-inited');
        },

        showDefault: function(loadedCallback) {
            this.node.classList.remove('tgpli-background-inited');
        },

        showIframe: function(loadedCallback) {
            var src = this.node.getAttribute('data-tgpli-src');
            if (src) {
                this.node.src = src;
            }

            this.node.removeAttribute('data-tgpli-iframe-inited');
        },

        showCustom: function(loadedCallback) {
            var action = this.node.getAttribute('data-tgpli-action');
            if (action && window[action] !== undefined && typeof window[action] == 'function') {
                window[action]();
            }

            this.node.removeAttribute('data-tgpli-custom-inited');
        },

        isShowed: function() {
            return this.showed;
        },

        initType: function() {
            if (this.data != undefined && this.data.customItem === true) {
                this.type = 'custom';
                return;
            }

            switch (this.node.nodeName.toUpperCase()) {
                case 'IMG':
                    this.type = 'image';
                    break;

                case 'IFRAME':
                    this.type = 'iframe';
                    break;

                default:
                    this.type = 'default';
            }
        },

        getType: function() {
            return this.type;
        },

        getGroupNodeInfo: function() {
            if (this.data != undefined && this.data.customItem === true) {
                return {
                    node: this.node,
                    type: 'custom'
                };
            }

            switch (this.getType()) {
                case 'image':
                    return this.getImageGroupNode();

                case 'iframe':
                    return {
                        node: this.node.parentNode,
                        type: 'iframe'
                    };

                default:
                    return {
                        node: this.node,
                        type: 'default'
                    };
            }
        },

        getImageGroupNode: function() {
            var id = this.node.id;

            if (this.node.parentNode.classList.contains('logo')) {
                return {
                    node: this.node.parentNode,
                    type: 'logo'
                };
            }

            if (id && document.querySelector('div.gem-client-item #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'gem-clients-type-carousel-grid');
                if (ancestorNode === null) {
                    var ancestorNode = getAncestorNode(this.node, 'gem_client_carousel-items');
                }
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'gem-clients'
                    };
                }
            }

            if (id && document.querySelector('#colophon .footer-widget-area #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'footer-widget-area');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'footer-widget-area'
                    };
                }
            }

            if (
                (this.node.className && this.node.className.indexOf('portfolio') != -1) ||
                (id && document.querySelector('div.portfolio #' + id) !== null &&
                 !(this.node.closest('div.portfolio').classList.contains('extended-products-grid') &&
                     this.node.closest('div.portfolio').classList.contains('portfolio-style-justified')) )
            ) {
                var ancestorNode = getAncestorNode(this.node, 'portfolio');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'portfolio'
                    };
                }
            }

            if (id && document.querySelector('div.blog #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'blog');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'blog'
                    };
                }
            }

            if (id && document.querySelector('div.gem-gallery-grid #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'gem-gallery-grid');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'gallery-grid'
                    };
                }
            }

            if (id && document.querySelector('div.gem-gallery #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'gem-gallery');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'gallery'
                    };
                }
            }

            if (id && document.querySelector('div.gem-simple-gallery #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'gem-simple-gallery');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'simple-gallery'
                    };
                }
            }

            if (id && document.querySelector('div.gem-slideshow #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'gem-slideshow');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'gem-slideshow'
                    };
                }
            }

            if (id && document.querySelector('div.gem-testimonials #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'gem-testimonials');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'gem-testimonials'
                    };
                }
            }

            if (id && document.querySelector('div.rev_slider_wrapper #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'rev_slider_wrapper');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'rev_slider'
                    };
                }
            }

            if (id && document.querySelector('div.wpb_images_carousel #' + id) !== null) {
                var ancestorNode = getAncestorNode(this.node, 'wpb_images_carousel');
                if (ancestorNode !== null) {
                    return {
                        node: ancestorNode,
                        type: 'wpb_images_carousel'
                    };
                }
            }

            return {
                node: this.node.parentNode,
                type: 'default'
            };
        },

        imageShowed: function() {
            var id = this.node.id;

            if (id && document.querySelector('div.caroufredsel_wrapper #' + id) !== null) {
                window.dispatchEvent(new window.CustomEvent('resize'));
            }

            if (window.jQuery !== undefined && window.jQuery !== null &&
                window.jQuery.waypoints !== undefined && window.jQuery.waypoints !== null &&
                typeof window.jQuery.waypoints === "function"
            ) {
                window.jQuery.waypoints('refresh');
            }
        },

        loaded: function(group) {
            var groupItems = group.getItems();

            if (group.getType() == 'default' && groupItems.length == 1 && groupItems[0].getType() == 'image') {
                this.imageShowed();
            }

            if (group.getType() == 'wpb_images_carousel' && group.allItemsLoaded()) {
                window.dispatchEvent(new window.CustomEvent('resize'));
            }
        }
    };

    function LazyItems(options) {
        options = options || {};

        this.options = Object.assign({
            visibilityOffset: 300,
            desktopEnable: true,
            mobileEnable: true
        }, options);

        this.groups = [];
        this.scrollTop = 0;
        this.rootHeight = 0;
        this.checkGroupsProcess = false;
        this.enabled = false;
        this.isPageScroller = false;
        this.isFullpage = false;
        this.visibilityOffsetFixed = false;

        this.init();
    }

    LazyItems.prototype = {
        init: function() {
            this.checkEnabled();
            this.updateRootScrollTop();
            this.updateRootSize();
            this.initEvents();
            this.initQueueNodes();
        },

        checkEnabled: function() {
            var isMobile = isMobileDevice();
            this.enabled = !(excludedDevices()) && ((isMobile && this.options.mobileEnable) || (!isMobile && this.options.desktopEnable));
        },

        isEnabled: function() {
            return this.enabled;
        },

        initQueueNodes: function() {
            if (window.tgpQueue !== undefined) {
                this.addNodes(window.tgpQueue.flushNodes());
            }
        },

        initEvents: function() {
            var self = this;
            addEvent(window, 'resize', function() { self.resizeHandle(); }, true);
            addEvent(document, 'scroll', function() { self.scrollHandle(); }, true);
            addEvent(document, 'DOMContentLoaded', function() { self.documentReadyHandle(); }, true);
        },

        resizeHandle: function() {
            var self = this;
            setTimeout(function() {
                self.updateRootScrollTop();
                self.updateRootSize();
                self.updateGroupsPositions();
                self.checkGroups();
            }, 0);
        },

        scrollHandle: function() {
            if (!this.visibilityOffsetFixed) {
                this.visibilityOffsetFixed = true;
                this.options.visibilityOffset *= 1.5;
            }
            this.updateRootScrollTop();
            this.updateGroupsPositions();
            this.checkGroups();
        },

        documentReadyHandle: function() {
            this.collectCustomItems();
            this.detectPageScroller();
            this.detectFullpage();
            this.updateRootScrollTop();
            this.updateRootSize();
            this.updateGroupsPositions();
            this.checkGroups();
        },

        detectPageScroller: function() {
            var self = this;

            this.isPageScroller = document.body.classList.contains('page-scroller');

            if (this.isPageScroller) {
                addEvent(document, 'page-scroller-updated', function() {
                    self.scrollHandle();
                });
            }
        },

        detectFullpage: function() {
            var self = this;

            this.isFullpage = document.body.classList.contains('thegem-fp');

            if (this.isFullpage) {
                addEvent(document, 'fullpage-updated', function() {
                    self.scrollHandle();
                });
            }
        },

        hasGroups: function() {
            return this.groups.length > 0;
        },

        checkGroups: function() {
            var self = this;

            if (this.checkGroupsProcess || !this.hasGroups()) {
                return;
            }

            this.checkGroupsProcess = true;

            this.groups.forEach(function(group) {
                if (group.isShowed() || !group.hasItems()) {
                    return;
                }

                if (self.isVisibleGroup(group)) {
                    self.showGroup(group, false);
                }
            });

            this.checkGroupsProcess = false;
        },

        isVisibleGroup: function(group) {
            var rootPosition = this.getRootPosition(),
                groupPosition = group.getPosition();

            if (groupPosition === null) {
                return true;
            }

            if (groupPosition.left == 0 && groupPosition.top == 0 && groupPosition.width == 0 && groupPosition.height == 0) {
                return false;
            }

            return groupPosition.top - this.options.visibilityOffset <= rootPosition.bottom &&
                groupPosition.top + groupPosition.height + this.options.visibilityOffset >= rootPosition.top;
        },

        showGroup: function(group, force) {
            if (force === undefined) {
                force = false;
            }
            group.show(force);
        },

        showItem: function(item) {
            item.show();
        },

        addItem: function(item) {
            if (!this.isEnabled() || this.isIgnoreItem(item)) {
                this.showItem(item);
                return;
            }

            this.getItemGroup(item).addItem(item);
        },

        getItemGroup: function(item) {
            var groupNodeInfo = item.getGroupNodeInfo(),
                group = this.findGroup(groupNodeInfo.node);

            if (group === null) {
                group = new LazyGroup(groupNodeInfo.node, groupNodeInfo.type);

                group.updatePosition();

                if (this.isIgnoreGroup(group) || this.isVisibleGroup(group)) {
                    this.showGroup(group, true);
                }

                this.groups.push(group);
            }

            return group;
        },

        findGroup: function(node) {
            for (var i = 0; i < this.groups.length; i++) {
                if (this.groups[i].getNode() == node) {
                    return this.groups[i];
                }
            }
            return null;
        },

        isIgnoreItem: function(item) {
            return false;
        },

        isIgnoreGroup: function(group) {
            if (group.getType() == 'gem-slideshow' || group.getType() == 'rev_slider') {
                return true;
            }

            return false;
        },

        addNodes: function(nodes) {
            var self = this;
            nodes.forEach(function(node) {
                self.addNode(node);
            });
        },

        addNode: function(data) {
            if (data.node === null) {
                return;
            }

            this.addItem(new LazyItem(data.node, data.data));
        },

        collectItems: function() {
            var self = this;

            document.querySelectorAll('img[data-tgpli-image-inited]').forEach(function(node) {
                self.addNode(node);
            });

            document.querySelectorAll('.tgpli-background-inited').forEach(function(node) {
                self.addNode(node);
            });
        },

        collectCustomItems: function() {
            var self = this;

            document.querySelectorAll('.tgpli-custom-item').forEach(function(node) {
                self.addNode({
                    node: node,
                    data: {
                        customItem: true
                    }
                });
            });
        },

        updateGroupsPositions: function() {
            this.groups.forEach(function(group) {
                group.updatePosition();
            });
        },

        updateRootScrollTop: function() {
            this.scrollTop = getRootScrollTop();
        },

        updateRootSize: function() {
            this.rootHeight = document.documentElement.clientHeight;
        },

        getRootPosition: function() {
            return {
                top: this.scrollTop,
                bottom: this.scrollTop + this.rootHeight,
                height: this.rootHeight
            };
        },

        checkGroupShowed: function(node, callback) {
            if (this.isGroupShowed(node)) {
                return true;
            } else {
                addEvent(node, 'tgpliVisible', function() {
                    callback(node);
                }, true);
                return false;
            }
        },

        isGroupShowed: function(node) {
            var group = this.findGroup(node);
            return group === null || group.isShowed();
        }
    };

    window.tgpLazyItems = new LazyItems(window.tgpLazyItemsOptions || {});
})(window, document);
