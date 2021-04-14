requirejs(['Shipay_PixQrGateway/js/vendor/clipboard'], function (ClipboardJS) {
    'use strict';
    window.addEventListener('load', function() {
        new ClipboardJS('.btn-pix-copy-paste');
        if (ClipboardJS.isSupported()) {
            var pixContainer = document.querySelector('.pix-copy-paste-container');
            if (!pixContainer) {
                return;
            }
            pixContainer.style = null;
        }
    });
});
