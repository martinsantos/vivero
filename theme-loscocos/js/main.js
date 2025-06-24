/**
 * Main JavaScript file for Los Cocos Vivero
 * Handles frontend interactions and cart functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Los Cocos Vivero - Main JavaScript loaded');
    console.log('loscocos_ajax:', typeof loscocos_ajax !== 'undefined' ? 'Available' : 'Not available');
    
    // Initialize mobile menu toggle if it exists
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-navigation');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            menuToggle.classList.toggle('active');
            mainNav.classList.toggle('active');
        });
    }
    
    // Initialize cart event listeners
    initCartFunctionality();
    
    // Debug: Log all add to cart buttons
    console.log('Add to cart buttons:', document.querySelectorAll('.add-to-cart'));
});

/**
 * Initialize all cart-related functionality
 */
function initCartFunctionality() {
    // Add to cart buttons
    document.body.addEventListener('click', function(e) {
        // Handle add to cart
        if (e.target.closest('.add-to-cart')) {
            e.preventDefault();
            const button = e.target.closest('.add-to-cart');
            const productId = button.dataset.productId;
            const quantity = button.dataset.quantity || 1;
            addToCart(productId, quantity);
        }
        
        // Handle update cart
        if (e.target.closest('.update-cart')) {
            e.preventDefault();
            const button = e.target.closest('.update-cart');
            const itemKey = button.dataset.itemKey;
            const quantity = button.closest('.quantity').querySelector('input').value;
            updateCart(itemKey, quantity);
        }
        
        // Handle remove item
        if (e.target.closest('.remove-item')) {
            e.preventDefault();
            const button = e.target.closest('.remove-item');
            const itemKey = button.dataset.itemKey;
            removeFromCart(itemKey);
        }
    });
}

/**
 * Add product to cart
 */
function addToCart(productId, quantity = 1) {
    // Debug log
    console.log('Attempting to add to cart - Product ID:', productId, 'Quantity:', quantity);
    
    // Check if loscocos_ajax is available
    if (typeof loscocos_ajax === 'undefined') {
        console.error('loscocos_ajax is not defined');
        showNotification('Error: AJAX configuration not loaded', 'error');
        return;
    }
    
    showLoading();
    
    // Prepare form data
    const formData = new FormData();
    formData.append('action', 'loscocos_add_to_cart');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('nonce', loscocos_ajax.nonce);
    
    // Debug log
    console.log('Sending AJAX request to:', loscocos_ajax.ajax_url);
    console.log('Request data:', {
        action: 'loscocos_add_to_cart',
        product_id: productId,
        quantity: quantity,
        nonce: loscocos_ajax.nonce
    });
    
    fetch(loscocos_ajax.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Cache-Control': 'no-cache',
        },
        body: formData
    })
    .then(response => {
        console.log('Raw response:', response);
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('AJAX response:', data);
        if (data.success) {
            if (data.data && data.data.fragments) {
                updateCartFragments(data.data.fragments);
            }
            showNotification('Producto añadido al carrito', 'success');
            
            // Refresh cart fragments
            if (typeof wc_cart_fragments_params !== 'undefined') {
                $(document.body).trigger('wc_fragment_refresh');
            }
        } else {
            throw new Error(data.data || 'Error al agregar al carrito');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Ocurrió un error al agregar al carrito', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

/**
 * Update cart item quantity
 */
function updateCart(itemKey, quantity) {
    showLoading();
    
    fetch(loscocos_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'action': 'loscocos_update_cart',
            'cart_item_key': itemKey,
            'quantity': quantity,
            'nonce': loscocos_ajax.nonce
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartFragments(data.data.fragments);
            showNotification('Carrito actualizado', 'success');
        } else {
            throw new Error(data.data || 'Error al actualizar el carrito');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Ocurrió un error', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

/**
 * Remove item from cart
 */
function removeFromCart(itemKey) {
    if (!confirm('¿Estás seguro de que deseas eliminar este producto del carrito?')) {
        return;
    }
    
    showLoading();
    
    fetch(loscocos_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'action': 'loscocos_remove_item',
            'cart_item_key': itemKey,
            'nonce': loscocos_ajax.nonce
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartFragments(data.data.fragments);
            showNotification('Producto eliminado del carrito', 'success');
        } else {
            throw new Error(data.data || 'Error al eliminar el producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Ocurrió un error', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

/**
 * Update cart fragments after AJAX operations
 */
function updateCartFragments(fragments) {
    if (!fragments) return;
    
    // Update cart count
    const cartCount = document.querySelector('.cart-contents-count');
    if (cartCount && fragments.cart_count !== undefined) {
        cartCount.textContent = fragments.cart_count;
    }
    
    // Update cart fragments
    Object.keys(fragments).forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
            element.outerHTML = fragments[selector];
        }
    });
    
    // Trigger event for other scripts
    document.body.dispatchEvent(new Event('updated_cart'));
}

/**
 * Show loading state
 */
function showLoading() {
    document.body.classList.add('loading');
    document.body.style.cursor = 'wait';
}

/**
 * Hide loading state
 */
function hideLoading() {
    document.body.classList.remove('loading');
    document.body.style.cursor = 'default';
}

/**
 * Show notification to user
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelectorAll('.loscocos-notification');
    existing.forEach(el => el.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `loscocos-notification fixed bottom-4 right-4 p-4 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.5s';
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}
