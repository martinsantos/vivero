/**
 * Los Cocos Child Theme Main JavaScript
 */

(function ($) {
    'use strict';

    // Force product gallery to be visible
    $(document).ready(function () {
        // Remove inline opacity style from WooCommerce gallery
        $('.woocommerce-product-gallery').removeAttr('style').css('opacity', '1');

        // Also ensure it's visible after a short delay (in case WooCommerce JS runs after)
        setTimeout(function () {
            $('.woocommerce-product-gallery').css('opacity', '1');
        }, 100);
    });

    // Cart functionality
    $(document.body).on('added_to_cart removed_from_cart', function () {
        // Update cart count in header
        $(document.body).trigger('wc_fragment_refresh');
    });

})(jQuery);
