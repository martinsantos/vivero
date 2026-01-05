jQuery(function($) {
    'use strict';

    // Update cart on quantity change with debounce
    let updateCartTimer;
    
    $(document.body).on('change', '.qty', function() {
        clearTimeout(updateCartTimer);
        
        const $this = $(this);
        const $cartItem = $this.closest('.woocommerce-cart-form__cart-item');
        const cartItemKey = $cartItem.data('cart-item-key');
        const quantity = $this.val();
        
        // Show loading state
        $this.prop('disabled', true);
        $cartItem.addClass('updating');
        
        updateCartTimer = setTimeout(function() {
            // AJAX call to update cart
            $.ajax({
                type: 'POST',
                url: loscocos_ajax.ajax_url,
                data: {
                    action: 'update_cart',
                    cart_item_key: cartItemKey,
                    quantity: quantity,
                    security: loscocos_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Trigger cart update
                        $(document.body).trigger('updated_cart_totals');
                        
                        // Update cart fragments
                        if (response.data && response.data.fragments) {
                            $.each(response.data.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }
                        
                        // Show success message
                        if (response.data && response.data.notices) {
                            showNotice(response.data.notices, 'success');
                        }
                    } else {
                        // Show error message
                        if (response.data && response.data.notices) {
                            showNotice(response.data.notices, 'error');
                        }
                    }
                },
                error: function() {
                    showNotice(loscocos_ajax.i18n_error_message, 'error');
                },
                complete: function() {
                    $this.prop('disabled', false);
                    $cartItem.removeClass('updating');
                }
            });
        }, 500); // 500ms debounce
    });
    
    // Handle remove item from cart
    $(document.body).on('click', '.remove', function(e) {
        e.preventDefault();
        
        const $this = $(this);
        const $cartItem = $this.closest('.woocommerce-cart-form__cart-item');
        
        // Show loading state
        $this.prop('disabled', true);
        $cartItem.addClass('removing');
        
        // AJAX call to remove item
        $.ajax({
            type: 'POST',
            url: loscocos_ajax.ajax_url,
            data: {
                action: 'remove_from_cart',
                cart_item_key: $cartItem.data('cart-item-key'),
                security: loscocos_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Remove item from DOM
                    $cartItem.slideUp(300, function() {
                        $(this).remove();
                        
                        // Update cart fragments
                        if (response.data && response.data.fragments) {
                            $.each(response.data.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }
                        
                        // Show success message
                        if (response.data && response.data.notices) {
                            showNotice(response.data.notices, 'success');
                        }
                        
                        // If cart is empty, reload the page
                        if ($('.woocommerce-cart-form__cart-item').length === 0) {
                            window.location.reload();
                        }
                    });
                } else {
                    // Show error message
                    if (response.data && response.data.notices) {
                        showNotice(response.data.notices, 'error');
                    }
                    $this.prop('disabled', false);
                    $cartItem.removeClass('removing');
                }
            },
            error: function() {
                showNotice(loscocos_ajax.i18n_error_message, 'error');
                $this.prop('disabled', false);
                $cartItem.removeClass('removing');
            }
        });
    });
    
    // Toggle shipping calculator
    $(document.body).on('click', '.shipping-calculator-button', function(e) {
        e.preventDefault();
        $('.shipping-calculator-form').slideToggle(300);
    });
    
    // Helper function to show notices
    function showNotice(message, type = 'success') {
        const noticeClass = type === 'success' ? 'woocommerce-message' : 'woocommerce-error';
        const notice = `
            <div class="${noticeClass} p-4 mb-6 rounded-md bg-${type === 'success' ? 'green' : 'red'}-50 border border-${type === 'success' ? 'green' : 'red'}-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-${type === 'success' ? 'green' : 'red'}-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-${type === 'success' ? 'green' : 'red'}-800">${message}</p>
                    </div>
                </div>
            </div>
        `;
        
        // Remove any existing notices
        $('.woocommerce-notices-wrapper').empty().append(notice);
        
        // Scroll to top of the page
        $('html, body').animate({
            scrollTop: $('.woocommerce-notices-wrapper').offset().top - 100
        }, 300);
        
        // Remove notice after 5 seconds
        setTimeout(function() {
            $(notice).fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Initialize cart
    function initCart() {
        // Add loading class to form when submitting
        $('.woocommerce-cart-form').on('submit', function() {
            $(this).addClass('processing');
        });
        
        // Handle quantity buttons
        $(document.body).on('click', '.plus, .minus', function() {
            const $input = $(this).siblings('.qty');
            const currentVal = parseFloat($input.val());
            const max = parseFloat($input.attr('max')) || '';
            const min = parseFloat($input.attr('min')) || 0;
            let newVal;
            
            if ($(this).hasClass('plus')) {
                newVal = max === '' || currentVal < max ? currentVal + 1 : currentVal;
            } else {
                newVal = currentVal > min ? currentVal - 1 : min;
            }
            
            $input.val(newVal).trigger('change');
            return false;
        });
    }
    
    // Initialize on document ready
    initCart();
    
    // Re-initialize after cart updates
    $(document.body).on('updated_cart_totals', function() {
        initCart();
    });
});
