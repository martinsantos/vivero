// cart.js
// Manages shopping cart functionality using localStorage and updates the UI.
// Exposes a global CartAPI object for interaction from other scripts.

// IIFE to encapsulate cart logic and avoid polluting the global scope directly,
// except for the intentionally exposed CartAPI.
(function () {
  const STORAGE_KEY = 'vivero_cart_items'; // Key for localStorage

  // --- Private Utility Functions ---
  // These functions are not meant to be called directly from outside this IIFE.

  /**
   * Retrieves the cart items from localStorage.
   * @returns {Array} An array of cart item objects.
   */
  function _getCart() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch (e) {
      console.error('Error leyendo el carrito', e);
      return [];
    }
  }

  /**
   * Saves the cart items to localStorage.
   * @param {Array} cart - The array of cart item objects to save.
   */
  function _saveCart(cart) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
  }

  /**
   * Updates the cart counter display in the header.
   * Shows the number of unique product lines in the cart.
   */
  function _updateCounter() {
    const cart = _getCart();
    const counter = document.getElementById('cart-count');
    if (counter) {
      counter.textContent = cart.length;
      counter.style.display = cart.length > 0 ? 'flex' : 'none';
    }
  }

  /**
   * Displays a modal confirming a product has been added to the cart.
   * @param {object} item - The item that was added to the cart (should have name and price).
   */
  function _showAddedToCartModal(item) {
    let modal = document.getElementById('cart-modal');
    if (!modal) {
      modal = _createCartModal();
    }

    const productName = modal.querySelector('#modal-product-name');
    const productPrice = modal.querySelector('#modal-product-price');
    
    if (productName) productName.textContent = item.name;
    // Ensure item.price is treated as a number for toLocaleString
    if (productPrice) productPrice.textContent = `$${Number(item.price).toLocaleString()} ARS`;

    // Mostrar modal
    modal.style.display = 'flex';
    
    // Auto-cerrar después de 4 segundos (incrementado de 3000 a 4000)
    setTimeout(() => {
      if (modal) modal.style.display = 'none';
    }, 4000);
  }

  /**
   * Creates the HTML structure for the "Added to Cart" confirmation modal.
   * Uses Tailwind CSS classes for styling.
   * @returns {HTMLElement} The created modal element.
   */
  function _createCartModal() {
    const modal = document.createElement('div');
    modal.id = 'cart-modal';
    modal.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1000;
    `;

    modal.innerHTML = `
      <div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl max-w-sm w-full m-4 text-center animate-modal-enter">
        <div class="text-[var(--primary-color)] text-5xl mb-4 mx-auto">✓</div>
        <h3 class="text-[var(--text-primary)] text-2xl font-bold mb-2">¡Producto añadido!</h3>
        <p class="text-[var(--text-secondary)] mb-6">
          <span id="modal-product-name" class="font-semibold">Producto</span><br>
          <span id="modal-product-price" class="font-bold"></span>
        </p>
        <button onclick="document.getElementById('cart-modal').style.display='none'" 
                class="bg-[var(--primary-color)] hover:bg-[var(--accent-color)] text-white font-bold py-2 px-6 rounded-lg transition-colors w-full">
          Continuar comprando
        </button>
      </div>
    `;
    // The modalSlideIn animation is expected to be in the global styles (home.html, single.html, cart.html)
    // So, no need to inject @keyframes modalSlideIn here if it's already globally defined.
    // If it's NOT globally defined, the <style> block below would be needed.
    // For now, assuming it IS globally available from the main style block copied to all pages.

    document.body.appendChild(modal);

    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });

    return modal;
  }

  // --- Public API Functions ---
  // Exposed on window.CartAPI for other scripts to use.
  const CartAPI = {
    /**
     * Adds an item to the shopping cart or increments its quantity if it already exists.
     * @param {object} itemData - Object containing product details {id, name, price}.
     *                           Price should be a number.
     */
    addToCart: function(itemData) {
      const cart = _getCart();
      const existingItem = cart.find(cartItem => cartItem.id === itemData.id);
      if (existingItem) {
        existingItem.quantity = (existingItem.quantity || 1) + 1;
      } else {
        cart.push({ ...itemData, price: Number(itemData.price), quantity: 1 });
      }
      _saveCart(cart);
      _updateCounter();
      _showAddedToCartModal(itemData); // Provide feedback to the user
    },

    /**
     * Retrieves all items currently in the cart.
     * @returns {Array} An array of cart item objects.
     */
    getCart: function() {
      return _getCart();
    },

    /**
     * Updates the quantity of a specific item in the cart.
     * If quantity becomes 0 or less, the item is removed.
     * @param {string|number} productId - The ID of the product to update.
     * @param {number} newQuantity - The new quantity for the product.
     */
    updateQuantity: function(productId, newQuantity) {
      let cart = _getCart();
      const itemIndex = cart.findIndex(item => String(item.id) === String(productId));
      if (itemIndex > -1) {
        const numQuantity = parseInt(newQuantity, 10);
        if (isNaN(numQuantity)) {
          console.error(`Invalid quantity provided for product ${productId}: ${newQuantity}`);
          return;
        }
        if (numQuantity > 0) {
          cart[itemIndex].quantity = numQuantity;
        } else {
          cart.splice(itemIndex, 1); // Remove item
        }
        _saveCart(cart);
        _updateCounter();
      } else {
        console.warn(`Product with ID ${productId} not found in cart for quantity update.`);
      }
    },

    /**
     * Removes an item completely from the cart.
     * @param {string|number} productId - The ID of the product to remove.
     */
    removeFromCart: function(productId) {
      let cart = _getCart();
      cart = cart.filter(item => String(item.id) !== String(productId));
      _saveCart(cart);
      _updateCounter();
    },

    /**
     * Calculates the total monetary value of all items in the cart.
     * @returns {number} The total cart value.
     */
    getCartTotal: function() {
      const cart = _getCart();
      return cart.reduce((total, item) => {
        const price = Number(item.price) || 0;
        const quantity = Number(item.quantity) || 1;
        return total + (price * quantity);
      }, 0);
    },

    /**
     * Manually triggers an update of the cart counter in the header.
     * Useful if cart modifications happen outside direct CartAPI calls.
     */
    refreshCounter: _updateCounter,

    /**
     * Sets up event listeners for all "Add to Cart" buttons on the page.
     * Buttons should have `data-add-cart`, `data-product-id`, `data-product-name`,
     * and `data-product-price` attributes.
     * This function is idempotent; it won't add multiple listeners to the same button.
     */
    setupCartButtons: function() {
      const buttons = document.querySelectorAll('[data-add-cart]');
      buttons.forEach((btn) => {
        if (btn.dataset.cartListenerAttached) return;

        btn.addEventListener('click', () => {
          const itemData = {
            id: btn.getAttribute('data-product-id') || Date.now().toString(),
            name: btn.getAttribute('data-product-name') || 'Planta',
            price: parseFloat(String(btn.getAttribute('data-product-price')).replace('$', '')) || 0,
          };
          CartAPI.addToCart(itemData); // Use CartAPI.addToCart
        });
        btn.dataset.cartListenerAttached = 'true';
      });
    }
  };

  window.CartAPI = CartAPI;

  document.addEventListener('DOMContentLoaded', () => {
    _updateCounter();
    CartAPI.setupCartButtons();
  });

})(); 