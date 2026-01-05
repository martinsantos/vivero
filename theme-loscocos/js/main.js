/**
 * Los Cocos E-commerce - Main JavaScript
 * Version: 2.0.0
 * 
 * Handles cart functionality, product interactions, and UI enhancements
 */

(function($) {
    'use strict';

    // Main app object
    const LosCocos = {
        
        init: function() {
            this.bindEvents();
            this.initCartFragments();
            this.initProductCards();
            console.log('🌱 Los Cocos E-commerce initialized v2.0');
        },

        bindEvents: function() {
            // Add to cart via AJAX
            $(document).on('click', '.ajax-add-to-cart', this.addToCart);
            
            // Update cart quantities
            $(document).on('change', '.cart-quantity-input', this.updateCartQuantity);
            
            // Remove from cart
            $(document).on('click', '.remove-from-cart', this.removeFromCart);
            
            // Product image lazy loading
            this.initLazyLoading();
        },

        addToCart: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const productId = $button.data('product-id');
            const quantity = $button.closest('.product-card').find('.quantity-input').val() || 1;
            
            if (!productId) {
                console.error('Product ID not found');
                return;
            }

            // Disable button and show loading
            $button.addClass('loading').prop('disabled', true);
            const originalText = $button.html();
            $button.html('<span class="material-icons">hourglass_empty</span> Añadiendo...');

            // AJAX request
            $.ajax({
                url: loscocos_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'loscocos_add_to_cart',
                    product_id: productId,
                    quantity: quantity,
                    nonce: loscocos_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update cart fragments
                        if (response.data.fragments) {
                            LosCocos.updateCartFragments(response.data.fragments);
                        }
                        
                        // Update cart count
                        if (response.data.cart_count !== undefined) {
                            LosCocos.updateCartCount(response.data.cart_count);
                        }
                        
                        // Show success state
                        $button.removeClass('loading')
                               .addClass('success')
                               .html('<span class="material-icons">check</span> ¡Añadido!');
                        
                        // Show notification
                        LosCocos.showNotification(response.data.message || 'Producto añadido al carrito', 'success');
                        
                        // Reset button after 2 seconds
                        setTimeout(function() {
                            $button.removeClass('success')
                                   .prop('disabled', false)
                                   .html(originalText);
                        }, 2000);
                        
                    } else {
                        throw new Error(response.data || 'Error al añadir producto');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    LosCocos.showNotification('Error al añadir producto al carrito', 'error');
                    
                    // Reset button
                    $button.removeClass('loading')
                           .prop('disabled', false)
                           .html(originalText);
                }
            });
        },

        updateCartQuantity: function(e) {
            const $input = $(this);
            const cartItemKey = $input.data('cart-item-key');
            const quantity = parseInt($input.val());
            
            if (!cartItemKey || quantity < 0) {
                return;
            }

            // Show loading state
            $input.addClass('loading');
            
            $.ajax({
                url: loscocos_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'loscocos_update_cart',
                    cart_item_key: cartItemKey,
                    quantity: quantity,
                    nonce: loscocos_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update cart fragments
                        if (response.data.fragments) {
                            LosCocos.updateCartFragments(response.data.fragments);
                        }
                        
                        // Update cart totals if on cart page
                        if (loscocos_ajax.is_cart && response.data.subtotal) {
                            $('.cart-subtotal .amount').html(response.data.subtotal);
                        }
                        
                        LosCocos.showNotification('Carrito actualizado', 'success');
                    } else {
                        throw new Error(response.data || 'Error al actualizar carrito');
                    }
                },
                error: function() {
                    LosCocos.showNotification('Error al actualizar carrito', 'error');
                },
                complete: function() {
                    $input.removeClass('loading');
                }
            });
        },

        removeFromCart: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const cartItemKey = $button.data('cart-item-key');
            
            if (!cartItemKey) {
                return;
            }

            if (!confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                return;
            }

            $button.addClass('loading');
            
            $.ajax({
                url: loscocos_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'loscocos_remove_item',
                    cart_item_key: cartItemKey,
                    nonce: loscocos_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Remove item from DOM
                        $button.closest('.cart-item').fadeOut(function() {
                            $(this).remove();
                        });
                        
                        // Update cart fragments
                        if (response.data.fragments) {
                            LosCocos.updateCartFragments(response.data.fragments);
                        }
                        
                        LosCocos.showNotification('Producto eliminado del carrito', 'success');
                    } else {
                        throw new Error(response.data || 'Error al eliminar producto');
                    }
                },
                error: function() {
                    LosCocos.showNotification('Error al eliminar producto', 'error');
                    $button.removeClass('loading');
                }
            });
        },

        updateCartFragments: function(fragments) {
            // Update cart fragments (WooCommerce standard)
            if (fragments) {
                $.each(fragments, function(key, value) {
                    if (key === 'cart_count') {
                        LosCocos.updateCartCount(value);
                    } else {
                        $(key).replaceWith(value);
                    }
                });
            }
        },

        updateCartCount: function(count) {
            // Update cart count in header/nav
            $('.cart-count, .cart-contents-count').each(function() {
                $(this).text(count);
                
                // Add animation
                $(this).addClass('updated');
                setTimeout(() => {
                    $(this).removeClass('updated');
                }, 300);
            });
        },

        initCartFragments: function() {
            // Initialize WooCommerce cart fragments if available
            if (typeof wc_cart_fragments_params !== 'undefined') {
                $(document.body).on('wc_fragments_refreshed', function() {
                    console.log('Cart fragments refreshed');
                });
            }
        },

        initProductCards: function() {
            // Add hover effects and interactions to product cards
            $('.product-card').each(function() {
                const $card = $(this);
                const $image = $card.find('.product-image img');
                
                // Lazy load images
                if ($image.length && $image.attr('data-src')) {
                    LosCocos.lazyLoadImage($image[0]);
                }
                
                // Add quick view functionality (future enhancement)
                $card.find('.quick-view-btn').on('click', function(e) {
                    e.preventDefault();
                    // TODO: Implement quick view modal
                });
            });
        },

        initLazyLoading: function() {
            // Use Intersection Observer for lazy loading if supported
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            LosCocos.lazyLoadImage(img);
                            observer.unobserve(img);
                        }
                    });
                });

                // Observe all images with data-src
                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        },

        lazyLoadImage: function(img) {
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.classList.add('loaded');
                img.removeAttribute('data-src');
            }
        },

        showNotification: function(message, type = 'success') {
            // Remove existing notifications
            $('.los-cocos-notification').remove();
            
            const $notification = $(`
                <div class="los-cocos-notification notification-${type}">
                    <div class="notification-content">
                        <span class="material-icons">${type === 'success' ? 'check_circle' : 'error'}</span>
                        <span class="notification-message">${message}</span>
                        <button class="notification-close" aria-label="Cerrar">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                </div>
            `);
            
            // Add styles if not already present
            if (!$('#los-cocos-notification-styles').length) {
                $('<style id="los-cocos-notification-styles">').html(`
                    .los-cocos-notification {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        z-index: 9999;
                        max-width: 400px;
                        min-width: 300px;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        animation: slideInRight 0.3s ease;
                    }
                    
                    .notification-success {
                        background: #d1fae5;
                        border: 1px solid #a7f3d0;
                        color: #065f46;
                    }
                    
                    .notification-error {
                        background: #fee2e2;
                        border: 1px solid #fca5a5;
                        color: #991b1b;
                    }
                    
                    .notification-content {
                        display: flex;
                        align-items: center;
                        padding: 1rem;
                        gap: 0.75rem;
                    }
                    
                    .notification-message {
                        flex-grow: 1;
                        font-weight: 500;
                    }
                    
                    .notification-close {
                        background: none;
                        border: none;
                        cursor: pointer;
                        color: inherit;
                        opacity: 0.7;
                        transition: opacity 0.2s;
                    }
                    
                    .notification-close:hover {
                        opacity: 1;
                    }
                    
                    @keyframes slideInRight {
                        from {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                    
                    .cart-count.updated {
                        animation: bounce 0.3s ease;
                    }
                    
                    @keyframes bounce {
                        0%, 100% { transform: scale(1); }
                        50% { transform: scale(1.2); }
                    }
                `).appendTo('head');
            }
            
            // Add to page
            $('body').append($notification);
            
            // Handle close button
            $notification.find('.notification-close').on('click', function() {
                $notification.fadeOut(200, function() {
                    $(this).remove();
                });
            });
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $notification.fadeOut(200, function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        LosCocos.init();
    });

    // Expose to global scope for debugging
    window.LosCocos = LosCocos;

})(jQuery);