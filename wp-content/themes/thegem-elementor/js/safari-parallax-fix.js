(function() {
    if (window.gemBrowser.name == 'safari') {
        try {
            var safariVersion = parseInt(window.gemBrowser.version);
        } catch(e) {
            var safariVersion = 0;
        }
        if (safariVersion >= 9) {
            window.gemSettings.parallaxDisabled = true;
            window.gemSettings.fillTopArea = true;
        }
    }
})();
