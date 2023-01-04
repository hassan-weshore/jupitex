/**
 * Metro layout mode
 */

( function( window, factory ) {
  'use strict';
  // universal module definition
  if ( typeof define == 'function' && define.amd ) {
    // AMD
    define( [
        '../layout-mode'
      ],
      factory );
  } else if ( typeof exports == 'object' ) {
    // CommonJS
    module.exports = factory(
      require('../layout-mode')
    );
  } else {
    // browser global
    factory(
      window.Isotope.LayoutMode
    );
  }

}( window, function factory( LayoutMode ) {
'use strict';

var Metro = LayoutMode.create('metro');

Metro.prototype._resetLayout = function() {
  this.x = 0;
  this.y = 0;
  this.maxY = 0;
  this._getMeasurement( 'gutter', 'outerWidth' );
  this.perColumn = 0;
  this.rows = [];
  this.rowIndex = 0;

  var container = this.options.isFitWidth ? this.element.parentNode : this.element;

  this.targetHeight = parseFloat(jQuery(container).data('max-row-height')) || 380;
  this.minHeight = 150;

  if (this.minHeight > this.targetHeight) {
      this.minHeight = this.targetHeight * 0.8;
  }

  this.getColumnCount();
  this.initItems();
  this.buildRows();
};

Metro.prototype.getRowItemsHeight = function(rowItems, item, minHeight) {
    var rowItemsInnerWidth = 0;
    var rowItemsOuterWidth = 0;

    rowItems.push(item);

    rowItems.forEach(function(item, index) {
        if (item.imageHeight > minHeight) {
            var relHeight = minHeight / item.imageHeight;
        } else {
            var relHeight = 1;
        }
        rowItemsInnerWidth += item.size.innerWidth * relHeight;
        rowItemsOuterWidth += item.size.innerWidth * relHeight + (item.size.outerWidth - item.size.innerWidth);
    });

    if (rowItemsOuterWidth > this.containerWidth) {
        return (this.containerWidth - (rowItemsOuterWidth - rowItemsInnerWidth)) * minHeight / rowItemsInnerWidth;
    } else {
        return minHeight;
    }
};

Metro.prototype.checkRowItemsFull = function(rowItems) {
    var rowItemsMinHeight = -1;

    rowItems.forEach(function(item, index) {
        if (rowItemsMinHeight == -1 || item.imageHeight < rowItemsMinHeight) {
            rowItemsMinHeight = item.imageHeight;
        }
    });

    if (rowItemsMinHeight == -1) {
        return false;
    }

    var rowItemsHeight = this.getRowItemsHeight(rowItems.slice(0, rowItems.length - 1), rowItems.slice(-1)[0], rowItemsMinHeight);

    if (rowItemsHeight < this.targetHeight * 1.3) {
        var rowItemsInnerWidth = 0;
        var rowItemsOuterWidth = 0;

        rowItems.forEach(function(item, index) {
            if (item.imageHeight > rowItemsHeight) {
                var relHeight = rowItemsHeight / item.imageHeight;
            } else {
                var relHeight = 1;
            }

            rowItemsInnerWidth += item.size.innerWidth * relHeight;
            rowItemsOuterWidth += item.size.innerWidth * relHeight + (item.size.outerWidth - item.size.innerWidth);
        });

        return {
                items: rowItems,
                innerWidth: rowItemsInnerWidth,
                outerWidth: rowItemsOuterWidth,
                height: rowItemsHeight
            };
    }

    return false;
};

Metro.prototype.getRowItems = function(targetHeight) {
    var rowItems = [];
    var rowItemsInnerWidth = 0;
    var rowItemsOuterWidth = 0;
    var defaultItemIndex = this.itemIndex;
    var minHeight = targetHeight;

    while ( (rowItemsOuterWidth < this.containerWidth || rowItems.length < this.perColumn) && this.itemIndex < this.items.length ) {
        var item = this.items[ this.itemIndex ];
        item.getSize();

        if (rowItems.length >= this.perColumn) {
            var canSizeResult = this.checkRowItemsFull(rowItems);
            if (canSizeResult) {
                return canSizeResult;
            }
        }

        var rowHeight = this.getRowItemsHeight(rowItems.slice(0), item, minHeight);
        if (rowHeight < this.minHeight && rowItems.length > 0) {
            var canSizeResult = this.checkRowItemsFull(rowItems);
            if (canSizeResult) {
                return canSizeResult;
            }

            return {
                    items: rowItems,
                    innerWidth: rowItemsInnerWidth,
                    outerWidth: rowItemsOuterWidth,
                    height: targetHeight
                };
        }

        if (item.imageHeight > targetHeight) {
            var relHeight = targetHeight / item.imageHeight;
        } else {
            var relHeight = 1;
        }

        rowItemsInnerWidth += item.size.innerWidth * relHeight;
        rowItemsOuterWidth += item.size.innerWidth * relHeight + (item.size.outerWidth - item.size.innerWidth);

        if (minHeight > item.imageHeight) {
            minHeight = item.imageHeight;
        }

        rowItems.push(item);
        this.itemIndex += 1;

        if (minHeight < targetHeight) {
            this.itemIndex = defaultItemIndex;
            return this.getRowItems(minHeight);
        }
    }

    if (rowItemsOuterWidth < this.containerWidth) {
        var canSizeResult = this.checkRowItemsFull(rowItems);
        if (canSizeResult) {
            return canSizeResult;
        }
    }

    return {
            items: rowItems,
            innerWidth: rowItemsInnerWidth,
            outerWidth: rowItemsOuterWidth,
            height: targetHeight
        };
}

Metro.prototype.buildRows = function() {
    var self = this;

    var container = this.options.isFitWidth ? this.element.parentNode : this.element;
    jQuery(this.isotope.options.itemSelector + ' .caption', container).hide();

    this.getContainerWidth();

    this.itemIndex = 0;

    while (this.itemIndex < this.items.length) {
        var targetHeight = this.targetHeight;
        var rowItems = this.getRowItems(this.targetHeight);

        if (rowItems.outerWidth > this.containerWidth) {
            var height = (this.containerWidth - (rowItems.outerWidth - rowItems.innerWidth)) * rowItems.height / rowItems.innerWidth;
        } else {
            var height = rowItems.height;
        }

        rowItems.items.forEach(function(item, index) {
            var rel = 1;
            if (item.imageHeight > height) {
                rel = height / item.imageHeight;
            }

            var calculatedWidth = Math.round(item.size.innerWidth * rel - 0.5);
            calculatedWidth += (item.size.outerWidth - item.size.innerWidth);
            jQuery(item.element).css('width', calculatedWidth);
        });
    }

    jQuery(this.isotope.options.itemSelector + ' .caption', container).show();
};

Metro.prototype.initItems = function() {
    var self = this;

    this.items = this.isotope.filteredItems;
    var container = this.options.isFitWidth ? this.element.parentNode : this.element;

    this.items.forEach(function(item, index) {
        if (item.imageHeight == null || item.imageHeight == undefined) {
            var maxHeight = 0,
                maxHeightImage = null;

            jQuery(self.isotope.options.itemImageWrapperSelector + ' img', item.element).each(function() {
                var imageHeight = parseInt(jQuery(this).attr('height'));
                if (!isNaN(imageHeight) && imageHeight > maxHeight) {
                    maxHeight = imageHeight;
                    maxHeightImage = this;
                }
            });

            if (maxHeightImage !== null) {
                self.items[ index ].imageWidth = parseInt(jQuery(maxHeightImage).attr('width'));
                self.items[ index ].imageHeight = parseInt(jQuery(maxHeightImage).attr('height'));
            }

            if (isNaN(self.items[ index ].imageHeight)) {
                self.items[ index ].imageHeight = self.targetHeight;
            }
        }

        var $element = jQuery(item.element);
        if (!$element.data('original-width')) {
            var original = self.items[ index ].imageWidth / 1.1;
            var padding = parseFloat($element.css('padding-left'));
            if (isNaN(padding)) {
                padding = 0;
            }
            $element.data('original-width', original + 2 * padding);
        }
        $element.css('width', $element.data('original-width'));
    });
};

Metro.prototype.getColumnCount = function() {
    var container = this.options.isFitWidth ? this.element.parentNode : this.element;
    if (this.isotope.options.itemSelector == '.portfolio-item') {
        var classes = jQuery(container).closest('.portfolio').attr('class');
    } else {
        var classes = jQuery(container).closest('.gem-gallery-grid').attr('class');
    }
    var m;
    if (classes.match(/extended-products-grid/)) {
        if (jQuery(window).width() < 768) {
            m = classes.match(/columns-mobile-(\d)/);
            if (m) {
                this.perColumn = parseInt(m[1]);
            } else {
                this.perColumn = 1;
            }
        } else if (jQuery(window).width() < 992) {
            m = classes.match(/columns-tablet-(\d)/);
            if (m) {
                this.perColumn = parseInt(m[1]);
            } else {
                this.perColumn = 2;
            }
        } else {
            m = classes.match(/columns-desktop-(\d)/);
            if (m) {
                this.perColumn = parseInt(m[1]);
            } else {
                this.perColumn = 4;
            }
        }
    } else {
        m = classes.match(/columns-(\d)/);
        if (m) {
            this.perColumn = parseInt(m[1]);
        } else {
            this.perColumn = 4;
        }
    }
};

Metro.prototype.getContainerWidth = function() {
    // container is parent if fit width
    var container = this.options.isFitWidth ? this.element.parentNode : this.element;
    // check that this.size and size are there
    // IE8 triggers resize on body size change, so they might not be
    var size = getSize( container );
    this.containerWidth = size && size.innerWidth;
};

Metro.prototype._getItemLayoutPosition = function( item ) {
  item.getSize();

  var itemWidth = item.size.outerWidth + this.gutter;
  // if this element cannot fit in the current row
  var containerWidth = this.isotope.size.innerWidth + this.gutter;
  if ( this.x !== 0 && itemWidth + this.x > containerWidth ) {
    this.x = 0;
    this.y = this.maxY;
  }

  var position = {
    x: this.x,
    y: this.y
  };

  this.maxY = Math.max( this.maxY, this.y + item.size.outerHeight );
  this.x += itemWidth;

  return position;
};

Metro.prototype._getContainerSize = function() {
  return { height: this.maxY };
};

return Metro;

}));
