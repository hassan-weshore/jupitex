jQuery(function() {
    var t = jQuery("#tmpl-elementor-add-section");

    htmlCode = 
'<div id="thegem-blocks-wrapper">'+
'    <div id="thegem-blocks" class="thegem-blocks-box">'+
'        <div class="thegem-blocks-control-mode">'+
'            <div class="thegem-blocks-control-mode-logo">'+
'                Blocks'+
'            </div>'+
'           <div>'+
'                <button id="thegem-blocks-close"></button>'+
'            </div>'+
'        </div>'+
'        <div id="thegem-blocks-notification"><div class="thegem-blocks-notification-inner">'+window.thegem_blocks.message+'</div></div>'
'    </div>'+
'/div>'
;

    if (t.length > 0) {
        var n = t.text();
        n = n.replace('<div class="elementor-add-section-drag-title', '<div class="elementor-add-section-area-button elementor-add-thegem-blocks-button" title="TheGem Blocks"><i class="eicon-folder"></i></div><div class="elementor-add-section-drag-title'), t.text(n), elementor.on("preview:loaded", (function() {
            jQuery(elementor.$previewContents[0].body).on("click", ".elementor-add-thegem-blocks-button", function(event) {
                jQuery('body').append(jQuery(htmlCode));
                jQuery('#thegem-blocks-close').on('click',function() {
                    jQuery('#thegem-blocks-wrapper').remove();
                });
            })
        }))
    }
});
