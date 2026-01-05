/**
 * Los Cocos Theme - Main JavaScript
 * Handles AJAX requests and cart functionality
 */

jQuery(document).ready(function($) {
    'use strict';

    // Debug mode flag
    const DEBUG = false;
    
    // Debounce timer for quantity updates
    let debounceTimer;
    const DEBOUNCE_DELAY = 300; // ms
    
    // Loading indicator HTML
    const LOADING_INDICATOR = '<span class="loscocos-loading"><span class="spinner"></span></span>';
    
    // Initialize when DOM is ready
    $(function() {
        if (DEBUG) console.log('Los Cocos Vivero - Main JavaScript loaded');
        
        // Check if AJAX URL is available
        if (typeof loscocos_ajax === 'undefined') {
            console.error('loscocos_ajax object not found');
            return;
        }
        
        if (DEBUG) console.log('AJAX URL:', loscocos_ajax.ajax_url);
        
        // Initialize cart functionality
        initCart();
        addLoadingStyles();
    });

    // Initialize cart functionality
    function initCart() {
        if (DEBUG) console.log('Initializing cart functionality...');
        
        // Handle quantity changes with debounce
        $(document).on('change input', '.qty', function() {
            const $input = $(this);
            const $row = $input.closest('.cart_item, .product, tr');
            const cartKey = $row.data('cart_item_key') || $row.data('key');
            const quantity = parseInt($input.val(), 10) || 1;
            
            if (!cartKey) {
                if (DEBUG) console.error('No cart item key found');
                return;
            }
            
            clearTimeout(debounceTimer);
            
            // Show updating state
            $row.addClass('updating');
            $('body').addClass('loscocos-updating-cart');
            
            debounceTimer = setTimeout(() => {
                updateCartQuantity(cartKey, quantity, $row);
            }, DEBOUNCE_DELAY);
        });
        
        // Handle remove item clicks
        $(document).on('click', '.remove', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $row = $button.closest('.cart_item, .product, tr');
            const cartKey = $row.data('cart_item_key') || $row.data('key');
            
            if (!cartKey) {
                if (DEBUG) console.error('No cart item key found for removal');
                return;
            }
            
            if (!confirm(loscocos_ajax.i18n.remove_item)) {
                return;
            }
            
            // Show removing state
            $row.addClass('removing');
            $('body').addClass('loscocos-updating-cart');
            
            // Remove item via AJAX
            removeCartItem(cartKey, $row);
        });
        
        // Handle quantity changes with debounce
        $(document).on('change input', '.qty', function() {
            const $input = $(this);
            const $row = $input.closest('.cart_item, .product, tr');
            const cartKey = $row.data('cart_item_key') || $row.data('key');
            const quantity = parseInt($input.val(), 10) || 1;
            
            if (!cartKey) {
                if (DEBUG) console.error('No cart item key found');
                return;
            }
            
            clearTimeout(debounceTimer);
            
            // Show updating state
            $row.addClass('updating');
            $('body').addClass('loscocos-updating-cart');
            
            debounceTimer = setTimeout(() => {
                updateCartQuantity(cartKey, quantity, $row);
            }, DEBOUNCE_DELAY);
        });

        // Handle add to cart buttons
        $(document).on('click', '.add_to_cart_button:not(.product_type_variable, .product_type_grouped, .loading)', function(e) {
            e.preventDefault();
            const $button = $(this);
            if ($button.hasClass('processing')) return;
            
            const productId = $button.data('product_id');
            const quantity = parseInt($button.data('quantity') || 1, 10);
            
            if (productId) addToCart(productId, quantity, $button);
        });
        
        // Handle remove item from cart
        $(document).on('click', '.remove', function(e) {
            e.preventDefault();
            const $link = $(this);
            const $row = $link.closest('.cart_item');
            const productId = $link.data('product_id');
            
            if (productId) {
                $row.addClass('removing');
                updateCartQuantity(productId, 0, $row);
            }
        });
    }

    // Add item to cart via AJAX
    function addToCart(productId, quantity, $button) {
        if (!productId) {
            if (DEBUG) console.error('No product ID provided');
            return;
        }

        if (DEBUG) console.log(`Adding product ${productId} to cart (quantity: ${quantity})`);
        
        // Show loading state
        const $buttonText = $button.find('.button-text') || $button;
        const originalText = $buttonText.html();
        
        $button.addClass('processing').prop('disabled', true);
        $buttonText.html(LOADING_INDICATOR + ' Añadiendo...');
        
        // Get form data
        let formData = {};
        const $form = $button.closest('form.cart');
        if ($form.length) {
            formData = $form.serializeArray().reduce((obj, item) => (obj[item.name] = item.value, obj), {});
        }

        // Make AJAX request
        $.ajax({
            type: 'POST',
            url: loscocos_ajax.ajax_url,
            data: {
                action: 'loscocos_add_to_cart',
                product_id: productId,
                quantity: quantity,
                security: loscocos_ajax.nonce,
                ...formData
            },
            beforeSend: () => $('body').addClass('loscocos-adding-to-cart'),
            success: (response) => {
                if (response.success) {
                    if (DEBUG) console.log('Product added successfully', response.data);
                    if (response.data.fragments) updateCartFragments(response.data.fragments);
                    if (response.data.notice) showNotice(response.data.notice, 'success');
                    $(document.body).trigger('added_to_cart', [response.data.fragments, response.data.cart_hash, $button]);
                } else {
                    if (DEBUG) console.error('Error adding product to cart', response);
                    showNotice(response.data || 'Error al agregar el producto al carrito', 'error');
                }
            },
            error: (xhr, status, error) => {
                if (DEBUG) console.error('AJAX error:', status, error);
                showNotice('Error de conexión. Por favor, inténtalo de nuevo.', 'error');
            },
            complete: () => {
                $button.removeClass('processing').prop('disabled', false);
                $buttonText.html(originalText);
                $('body').removeClass('loscocos-adding-to-cart');
            }
        });
    }

    // Update cart quantity via AJAX
    function updateCartQuantity(cartKey, quantity, $row) {
        if (!cartKey) {
            if (DEBUG) console.error('No cart item key provided');
            return;
        }

        if (DEBUG) console.log(`Updating cart: item ${cartKey}, quantity ${quantity}`);
        
        // Show loading state
        const $cart = $('.woocommerce-cart-form, .cart-wrapper, .cart');
        $cart.addClass('updating-cart');
        
        // Disable inputs during update
        const $inputs = $cart.find('input, button');
        $inputs.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: loscocos_ajax.ajax_url,
            data: {
                action: 'update_cart_quantity',
                cart_key: cartKey,
                quantity: quantity,
                nonce: loscocos_ajax.nonce
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    if (DEBUG) console.log('Cart updated successfully', response);
                    
                    // Update fragments if available
                    if (response.fragments) {
                        $.each(response.fragments, function(selector, html) {
                            $(selector).replaceWith(html);
                        });
                    }
                    
                    // Trigger WooCommerce events
                    $(document.body).trigger('updated_wc_div');
                    $(document.body).trigger('updated_cart_totals');
                    $(document.body).trigger('wc_fragments_loaded');
                    
                    // Show success message
                    showNotice(loscocos_ajax.i18n.cart_updated, 'success');
                } else {
                    if (DEBUG) console.error('Error updating cart', response);
                    showNotice(response.data || loscocos_ajax.i18n.cart_error, 'error');
                    
                    // Revert quantity on error
                    if ($row) {
                        const $qty = $row.find('.qty');
                        $qty.val($qty.data('quantity') || 1);
                    }
                }
            },
            error: (xhr, status, error) => {
                if (DEBUG) console.error('AJAX error:', status, error);
                showNotice(loscocos_ajax.i18n.cart_error, 'error');
                
                // Revert quantity on error
                if ($row) {
                    const $qty = $row.find('.qty');
                    $qty.val($qty.data('quantity') || 1);
                }
            },
            complete: () => {
                // Re-enable inputs
                $inputs.prop('disabled', false);
                
                // Remove loading states
                $cart.removeClass('updating-cart');
                if ($row) $row.removeClass('updating removing');
                $('body').removeClass('loscocos-updating-cart');
                
                // Trigger WooCommerce events
                $(document.body).trigger('wc_fragment_refresh');
                if (typeof wc_cart_fragments_params !== 'undefined') {
                    $(document.body).trigger('wc_fragments_refreshed');
                }
            }
        });
    }

    // Remove item from cart via AJAX
    function removeCartItem(cartKey, $row) {
        if (!cartKey) {
            if (DEBUG) console.error('No cart item key provided for removal');
            return;
        }

        if (DEBUG) console.log(`Removing item from cart: ${cartKey}`);
        
        // Show loading state
        const $cart = $('.woocommerce-cart-form, .cart-wrapper, .cart');
        $cart.addClass('updating-cart');
        
        // Disable inputs during update
        const $inputs = $cart.find('input, button');
        $inputs.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: loscocos_ajax.ajax_url,
            data: {
                action: 'remove_cart_item',
                cart_key: cartKey,
                nonce: loscocos_ajax.nonce
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    if (DEBUG) console.log('Item removed successfully', response);
                    
                    // Handle empty cart
                    if (response.is_cart_empty && response.redirect) {
                        window.location.href = response.redirect;
                        return;
                    }
                    
                    // Remove the row with animation
                    if ($row) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Update fragments if available
                            if (response.fragments) {
                                updateCartFragments(response.fragments);
                            }
                            
                            // Trigger WooCommerce events
                            $(document.body).trigger('updated_wc_div');
                            $(document.body).trigger('updated_cart_totals');
                            
                            if (typeof wc_cart_fragments_params !== 'undefined') {
                                $(document.body).trigger('wc_fragments_loaded');
                            }
                        });
                    }
                    
                    // Show success message
                    showNotice(loscocos_ajax.i18n.success, 'success');
                } else {
                    if (DEBUG) console.error('Error removing item', response);
                    showNotice(response.data || loscocos_ajax.i18n.error, 'error');
                }
            },
            error: (xhr, status, error) => {
                if (DEBUG) console.error('AJAX error:', status, error);
                showNotice(loscocos_ajax.i18n.error, 'error');
            },
            complete: () => {
                // Re-enable inputs
                $inputs.prop('disabled', false);
                
                // Remove loading states
                $cart.removeClass('updating-cart');
                if ($row) $row.removeClass('removing');
                $('body').removeClass('loscocos-updating-cart');
            }
        });
    }

    // Update cart fragments
    function updateCartFragments(fragments) {
        if (!fragments) return;
        if (DEBUG) console.log('Updating cart fragments');
        
        try {
            // Update each fragment
            Object.entries(fragments).forEach(([key, html]) => {
                try {
                    const $element = $(key);
                    if ($element.length) {
                        // Save focus state if needed
                        const focusedElementId = $element.find(':focus').attr('id');
                        const selection = $element[0].selectionStart !== undefined ? {
                            start: $element[0].selectionStart,
                            end: $element[0].selectionEnd
                        } : null;
                        
                        // Update the element
                        $element.replaceWith(html);
                        
                        // Restore focus and selection if needed
                        if (focusedElementId) {
                            const $newElement = $(`#${focusedElementId}`);
                            if ($newElement.length && $newElement[0].setSelectionRange && selection) {
                                $newElement.trigger('focus');
                                $newElement[0].setSelectionRange(selection.start, selection.end);
                            }
                        }
                    }
                } catch (e) {
                    if (DEBUG) console.warn(`Failed to update fragment ${key}`, e);
                }
            });
            
            // Trigger WooCommerce events
            }
        });
        
        // Trigger WooCommerce events
        $(document.body).trigger('updated_wc_div');
        $(document.body).trigger('updated_cart_totals');
        
        // Only trigger fragments loaded if the variable exists
        if (typeof wc_cart_fragments_params !== 'undefined') {
            $(document.body).trigger('wc_fragments_loaded');
        }
    } catch (e) {
        if (DEBUG) console.error('Error updating cart fragments:', e);
    }
}

// Add loading styles to the page
function addLoadingStyles() {
    if ($('#loscocos-loading-styles').length) return;
    
    const styles = `
        <style id="loscocos-loading-styles">
            .loscocos-loading { 
                display: inline-block; 
                vertical-align: middle; 
                width: 20px; 
                height: 20px; 
                border: 2px solid rgba(0,0,0,0.1); 
                border-radius: 50%; 
                border-top-color: #000; 
                animation: spin 0.8s ease-in-out infinite; 
                margin-right: 8px; 
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            
            .loscocos-updating-cart *,
            .loscocos-updating-cart *:before,
            .loscocos-updating-cart *:after {
                cursor: wait !important;
            }
            
            .loscocos-updating-cart a,
            .loscocos-updating-cart button,
            .loscocos-updating-cart input[type="submit"],
            .loscocos-updating-cart input[type="button"],
            .loscocos-updating-cart input[type="number"],
            .loscocos-updating-cart select {
                pointer-events: none;
                opacity: 0.7;
            }
            
            .cart_item.updating {
                opacity: 0.7;
                position: relative;
            }
            
            .cart_item.updating:after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255,255,255,0.7);
                z-index: 1;
            }
            
            .loscocos-notice {
                position: fixed;
                top: 20px;
                right: 20px;
                max-width: 350px;
                padding: 15px 20px;
                background: #fff;
                border-left: 4px solid #00a32a;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                z-index: 9999;
                display: flex;
                align-items: center;
                transform: translateX(120%);
                transition: transform 0.3s ease-out;
            }
            
            .loscocos-notice.show {
                transform: translateX(0);
            }
            
            .loscocos-notice.error {
                border-left-color: #d63638;
            }
            
            .loscocos-notice-icon {
                margin-right: 12px;
                font-size: 20px;
            }
            
            .loscocos-notice-dismiss {
                margin-left: 15px;
                background: none;
                border: none;
                font-size: 20px;
                line-height: 1;
                cursor: pointer;
                padding: 0;
                color: #787c82;
            }
            
            .loscocos-notice-dismiss:hover {
                color: #000;
            }
            
            .fade-in {
                animation: fadeIn 0.3s ease-out;
            }
            
            .fade-out {
                animation: fadeOut 0.3s ease-in;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(-20px); }
            }
        </style>
    `;
    
    $('head').append(styles);
}

// Show notice message
function showNotice(message, type = 'success') {
    const $existingNotice = $('.woocommerce-notices-wrapper');
    
    if ($existingNotice.length) {
        $existingNotice.addClass('fade-out');
        setTimeout(() => $existingNotice.remove(), 300);
    }
    
    const notice = `
        <div class="woocommerce-notices-wrapper fade-in">
            <div class="woocommerce-message woocommerce-${type}" role="alert">
                <div class="loscocos-notice-content">
                    <span class="loscocos-notice-icon">${type === 'error' ? '⚠️' : '✓'}</span>
                    <span class="loscocos-notice-text">${message}</span>
                </div>
            </div>
        `;
        
        $('.woocommerce').prepend(notice);
        
        $('.loscocos-notice-dismiss').on('click', function() {
            $(this).closest('.woocommerce-notices-wrapper').addClass('fade-out');
            setTimeout(() => $(this).closest('.woocommerce-notices-wrapper').remove(), 300);
        });
        
        if (type !== 'error') {
            let noticeTimer = setTimeout(() => {
                $('.woocommerce-notices-wrapper').addClass('fade-out');
                setTimeout(() => $('.woocommerce-notices-wrapper').remove(), 300);
            }, 5000);
            
            $('.woocommerce-notices-wrapper')
                .on('mouseenter', () => clearTimeout(noticeTimer))
                .on('mouseleave', () => {
                    noticeTimer = setTimeout(() => {
                        $('.woocommerce-notices-wrapper').addClass('fade-out');
                        setTimeout(() => $('.woocommerce-notices-wrapper').remove(), 300);
                    }, 1000);
                });
        }
    }
});
