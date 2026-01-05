/**
 * Sistema de Carrito WooCommerce - Los Cocos
 * Garantiza que solo se añada UN producto por clic
 * 
 * @version 2.0.0
 */

(function() {
    'use strict';
    
    // Variables globales
    let isAddingToCart = false;
    let cartButtons = [];
    
    /**
     * Inicializar el sistema de carrito
     */
    function initCart() {
        console.log('🛒 Inicializando sistema de carrito Los Cocos...');
        
        // Configurar botones de añadir al carrito
        setupAddToCartButtons();
        
        // Configurar fragmentos de carrito de WooCommerce
        setupCartFragments();
        
        // Configurar controles de cantidad (delegado)
        setupQuantityControls();
        
        // Actualizar contador inicial
        updateCartCounter();
        
        console.log('✅ Sistema de carrito inicializado');
    }
    
    /**
     * Controles de cantidad (delegado)
     */
    let qtyListenerAdded = false;
    function setupQuantityControls() {
        if (qtyListenerAdded) return;
        document.addEventListener('click', function (event) {
            const btn = event.target.closest('.qty-btn');
            if (!btn) return;
            
            event.preventDefault();
            if (btn.disabled) return;
            
            const action = btn.getAttribute('data-action');
            const control = btn.closest('.qty-control');
            const input = control ? control.querySelector('input.qty, #product-quantity, input[name="quantity"]') : null;
            const cartKey = btn.getAttribute('data-cart-key') || (control ? control.getAttribute('data-cart-key') : null);
            
            if (!input) return;
            
            const current = parseInt(input.value, 10) || 0;
            const min = parseInt(input.getAttribute('min') || '1', 10);
            const maxAttr = input.getAttribute('max');
            const max = maxAttr ? parseInt(maxAttr, 10) : null;
            const delta = action === 'minus' ? -1 : 1;
            let next = current + delta;
            if (next < min) next = min;
            if (max && next > max) next = max;
            if (next === current) return;
            
            // Si tenemos cartKey, actualizamos por AJAX; de lo contrario, sólo ajustamos el input localmente
            if (cartKey) {
                btn.disabled = true;
                try {
                    if (typeof window.updateQuantity === 'function') {
                        window.updateQuantity(cartKey, next);
                    }
                } finally {
                    // Rehabilitar el botón poco después; el DOM se actualizará por fragments
                    setTimeout(() => { btn.disabled = false; }, 1200);
                }
            } else {
                input.value = String(next);
                // Disparar eventos por si hay listeners de UI
                const ev = new Event('change', { bubbles: true });
                input.dispatchEvent(ev);
            }
        }, false);
        qtyListenerAdded = true;
    }
    
    /**
     * Configurar todos los botones de añadir al carrito
     */
    function setupAddToCartButtons() {
        // Buscar todos los botones de añadir al carrito con selectores más específicos
        const selectors = [
            '.add-to-cart',
            '.add-to-cart-unified', 
            '[data-product-id]',
            '.single_add_to_cart_button',
            'button[name="add-to-cart"]',
            '.add-to-cart-btn',
            '.btn-add-to-cart-featured'
        ];
        
        const buttons = document.querySelectorAll(selectors.join(', '));
        
        buttons.forEach(button => {
            // Evitar duplicar event listeners
            if (button.dataset.cartListenerAdded) return;
            
            // Verificar que el botón tiene un ID de producto válido
            const productId = getProductId(button);
            if (productId) {
                button.addEventListener('click', handleAddToCart);
                button.dataset.cartListenerAdded = 'true';
                cartButtons.push(button);
                console.log(`🔘 Botón configurado para producto ${productId}`);
            } else {
                console.warn('⚠️ Botón sin ID de producto válido:', button);
            }
        });
        
        console.log(`🔘 ${cartButtons.length} botones de carrito configurados correctamente`);
    }
    
    /**
     * Manejar clic en botón de añadir al carrito
     */
    function handleAddToCart(event) {
        event.preventDefault();
        event.stopPropagation();
        
        // Prevenir múltiples clics
        if (isAddingToCart) {
            console.log('⚠️ Ya se está añadiendo un producto al carrito');
            return;
        }
        
        const button = event.currentTarget;
        const productId = getProductId(button);
        // Detectar cantidad desde inputs cercanos si existen
        let quantity = 1;
        try {
            const form = button.closest('form');
            const container = button.closest('[data-product-id]') || document;
            const qtyInput = (form && form.querySelector('input[name="quantity"], input.qty, #product-quantity'))
                || container.querySelector('input[name="quantity"], input.qty, #product-quantity');
            if (qtyInput) {
                const parsed = parseInt(qtyInput.value, 10);
                if (!isNaN(parsed) && parsed > 0) {
                    quantity = parsed;
                }
            }
        } catch (e) {
            // Silencio: mantener quantity = 1 en caso de error
        }
        
        if (!productId) {
            console.error('❌ No se pudo obtener el ID del producto');
            return;
        }
        
        console.log(`🛒 Añadiendo producto ${productId} al carrito (cantidad: ${quantity})`);
        
        addToCartAjax(button, productId, quantity);
    }
    
    /**
     * Obtener ID del producto desde el botón
     */
    function getProductId(button) {
        // Intentar diferentes métodos para obtener el ID
        let productId = null;
        
        // Método 1: data-product-id attribute
        productId = button.getAttribute('data-product-id');
        if (productId) {
            console.log('🔍 ID encontrado via data-product-id:', productId);
            return productId;
        }
        
        // Método 2: dataset.productId
        productId = button.dataset.productId;
        if (productId) {
            console.log('🔍 ID encontrado via dataset:', productId);
            return productId;
        }
        
        // Método 3: value attribute (para botones de WooCommerce)
        if (button.name === 'add-to-cart' && button.value) {
            productId = button.value;
            console.log('🔍 ID encontrado via value:', productId);
            return productId;
        }
        
        // Método 4: buscar en el contenedor padre
        const productCard = button.closest('[data-product-id]');
        if (productCard) {
            productId = productCard.getAttribute('data-product-id');
            console.log('🔍 ID encontrado en contenedor padre:', productId);
            return productId;
        }
        
        // Método 5: buscar en formulario padre
        const form = button.closest('form');
        if (form) {
            const hiddenInput = form.querySelector('input[name="add-to-cart"]');
            if (hiddenInput && hiddenInput.value) {
                productId = hiddenInput.value;
                console.log('🔍 ID encontrado en formulario:', productId);
                return productId;
            }
        }
        
        // Método 6: extraer del onclick attribute
        const onclickAttr = button.getAttribute('onclick');
        if (onclickAttr) {
            const match = onclickAttr.match(/addToCart\((\d+)\)/);
            if (match && match[1]) {
                productId = match[1];
                console.log('🔍 ID encontrado via onclick:', productId);
                return productId;
            }
        }
        
        console.error('❌ No se pudo encontrar el ID del producto en:', button);
        return null;
    }
    
    /**
     * Añadir producto al carrito vía AJAX
     */
    function addToCartAjax(button, productId, quantity) {
        isAddingToCart = true;
        
        // Cambiar estado del botón
        const originalContent = button.innerHTML;
        const originalDisabled = button.disabled;
        
        button.disabled = true;
        button.innerHTML = '<span class="material-icons animate-spin">refresh</span> Añadiendo...';
        
        // Preparar datos para AJAX
        const formData = new FormData();
        formData.append('action', 'loscocos_add_to_cart');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('nonce', loscocos_ajax.nonce);
        
        // Realizar petición AJAX
        fetch(loscocos_ajax.ajax_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Producto añadido exitosamente');
                
                // Actualizar fragmentos del carrito
                if (data.data.fragments) {
                    updateCartFragments(data.data.fragments);
                }
                
                // Mostrar notificación
                showSuccessNotification(data.data.message || 'Producto añadido al carrito');
                
                // Actualizar contador
                updateCartCounter(data.data.cart_count);
                
            } else {
                console.error('❌ Error al añadir producto:', data.data);
                showErrorNotification(data.data || 'Error al añadir producto al carrito');
            }
        })
        .catch(error => {
            console.error('❌ Error de red:', error);
            showErrorNotification('Error de conexión. Inténtalo de nuevo.');
        })
        .finally(() => {
            // Restaurar botón
            button.disabled = originalDisabled;
            button.innerHTML = originalContent;
            isAddingToCart = false;
        });
    }
    
    /**
     * Actualizar fragmentos del carrito
     */
    function updateCartFragments(fragments) {
        if (!fragments) return;
        
        Object.keys(fragments).forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                if (selector === 'cart_count' || selector === 'cart_hash') {
                    // Estos son valores, no HTML
                    return;
                }
                element.innerHTML = fragments[selector];
            });
        });
        
        console.log('🔄 Fragmentos del carrito actualizados');
    }
    
    /**
     * Actualizar contador del carrito
     */
    function updateCartCounter(count) {
        const counters = document.querySelectorAll('.cart-count, .cart-counter, #cart-count, .cart-items-count');
        
        if (count !== undefined) {
            counters.forEach(counter => {
                counter.textContent = count;
                counter.style.display = count > 0 ? 'inline-block' : 'none';
                
                // Animación de actualización
                counter.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    counter.style.transform = 'scale(1)';
                }, 200);
            });
        }
    }
    
    /**
     * Configurar fragmentos de carrito de WooCommerce
     */
    function setupCartFragments() {
        // Escuchar eventos de fragmentos de WooCommerce
        document.body.addEventListener('wc_fragments_refreshed', function() {
            console.log('🔄 Fragmentos de WooCommerce actualizados');
            updateCartCounter();
        });
        
        document.body.addEventListener('wc_fragments_loaded', function() {
            console.log('📦 Fragmentos de WooCommerce cargados');
            updateCartCounter();
        });
    }
    
    /**
     * Mostrar notificación de éxito
     */
    function showSuccessNotification(message) {
        showNotification(message, 'success');
    }
    
    /**
     * Mostrar notificación de error
     */
    function showErrorNotification(message) {
        showNotification(message, 'error');
    }
    
    /**
     * Mostrar notificación
     */
    function showNotification(message, type = 'success') {
        // Crear o obtener contenedor de notificaciones
        let container = document.getElementById('cart-notifications');
        if (!container) {
            container = document.createElement('div');
            container.id = 'cart-notifications';
            container.style.cssText = `
                position: fixed;
                top: 2rem;
                right: 2rem;
                z-index: 9999;
                pointer-events: none;
            `;
            document.body.appendChild(container);
        }
        
        // Crear notificación
        const notification = document.createElement('div');
        notification.className = `cart-notification cart-notification-${type}`;
        notification.style.cssText = `
            background: white;
            border: 2px solid ${type === 'success' ? '#10b981' : '#ef4444'};
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.4s ease;
            pointer-events: auto;
            max-width: 300px;
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="material-icons" style="color: ${type === 'success' ? '#10b981' : '#ef4444'};">
                    ${type === 'success' ? 'check_circle' : 'error'}
                </span>
                <div>
                    <div style="font-weight: 600; color: #1e293b;">
                        ${type === 'success' ? '¡Producto añadido!' : 'Error'}
                    </div>
                    <div style="font-size: 0.875rem; color: #64748b;">
                        ${message}
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 1.25rem; margin-left: auto;">
                    ×
                </button>
            </div>
        `;
        
        container.appendChild(notification);
        
        // Mostrar notificación
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 100);
        
        // Auto-ocultar después de 4 segundos
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 400);
        }, 4000);
    }
    
    /**
     * Función pública para añadir producto (para compatibilidad)
     */
    window.addToCart = function(productId, quantity = 1) {
        console.log(`🔧 Función legacy addToCart llamada: ${productId}`);
        
        // Buscar botón correspondiente
        const button = document.querySelector(`[data-product-id="${productId}"]`);
        if (button) {
            button.click();
        } else {
            // Crear llamada AJAX directa
            addToCartAjax({
                disabled: false,
                innerHTML: 'Añadir al carrito'
            }, productId, 1); // SIEMPRE cantidad 1
        }
    };
    
    /**
     * Función para añadir producto destacado (para compatibilidad con index.php)
     */
    window.addToCartFeatured = function(productId, productName) {
        console.log(`🌟 Añadiendo producto destacado: ${productName} (${productId})`);
        window.addToCart(productId, 1); // SIEMPRE cantidad 1
    };
    
    /**
     * Función para actualizar cantidad en el carrito
     */
    window.updateQuantity = function(cartItemKey, quantity) {
        console.log(`🔄 Actualizando cantidad: ${cartItemKey} -> ${quantity}`);
        
        // Validar parámetros
        if (!cartItemKey || quantity < 1) {
            console.error('❌ Parámetros inválidos para updateQuantity');
            showErrorNotification('Parámetros inválidos para actualizar cantidad');
            return;
        }
        
        // Mostrar indicador de carga
        const cartForm = document.querySelector('.woocommerce-cart-form');
        if (cartForm) {
            cartForm.classList.add('processing');
        }
        
        // Preparar datos
        const formData = new FormData();
        formData.append('action', 'loscocos_update_cart');
        formData.append('cart_item_key', cartItemKey);
        formData.append('quantity', quantity);
        formData.append('nonce', loscocos_ajax.nonce);
        
        // Enviar solicitud AJAX
        fetch(loscocos_ajax.ajax_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Cantidad actualizada exitosamente');
                
                // Actualizar fragmentos del carrito
                if (data.data && data.data.fragments) {
                    updateCartFragments(data.data.fragments);
                }
                
                // Actualizar totales
                if (data.data && data.data.cart_total) {
                    const totalElements = document.querySelectorAll('.cart-total, .order-total');
                    totalElements.forEach(element => {
                        element.textContent = data.data.cart_total;
                    });
                }
                
                // Actualizar contador
                if (data.data && data.data.cart_count !== undefined) {
                    updateCartCounter(data.data.cart_count);
                }
                
                showSuccessNotification('Cantidad actualizada correctamente');
                
            } else {
                console.error('❌ Error al actualizar carrito:', data.data);
                showErrorNotification(data.data || 'Error al actualizar cantidad');
            }
        })
        .catch(error => {
            console.error('❌ Error de red en updateQuantity:', error);
            showErrorNotification('Error de conexión al actualizar cantidad');
        })
        .finally(() => {
            // Quitar indicador de carga
            if (cartForm) {
                cartForm.classList.remove('processing');
            }
        });
    };
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCart);
    } else {
        initCart();
    }
    
    // Re-inicializar después de actualizaciones AJAX
    document.body.addEventListener('updated_wc_div', function() {
        console.log('🔄 Contenido WooCommerce actualizado, re-inicializando botones');
        setTimeout(setupAddToCartButtons, 100);
    });
    
    // Exponer funciones para debugging
    window.LoscocosCart = {
        init: initCart,
        setupButtons: setupAddToCartButtons,
        addToCart: window.addToCart,
        isAddingToCart: () => isAddingToCart,
        cartButtons: () => cartButtons
    };
    
    console.log('🌱 Los Cocos Cart System v2.0.0 cargado');
    
})();