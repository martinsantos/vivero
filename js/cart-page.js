document.addEventListener('DOMContentLoaded', () => {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const cartSummarySection = document.getElementById('cart-summary');
    const cartEmptyMessage = document.getElementById('cart-empty-message');
    const checkoutButton = document.getElementById('checkout-button');
    const subtotalElement = document.getElementById('cart-subtotal');
    const grandTotalElement = document.getElementById('cart-grand-total');

    function renderCartItems() {
        if (!cartItemsContainer || !CartAPI || typeof CartAPI.getCart !== 'function') {
            console.error('Cart container or CartAPI not found. Cannot render cart.');
            return;
        }

        const cart = CartAPI.getCart();

        if (cart.length === 0) {
            if (cartEmptyMessage) cartEmptyMessage.style.display = 'block';
            if (cartItemsContainer) cartItemsContainer.innerHTML = ''; // Clear any previous items
            if (cartSummarySection) cartSummarySection.style.display = 'none';
            if (checkoutButton) checkoutButton.style.display = 'none';
            // Also hide the items container itself if it's separate from the message
            if (document.getElementById('cart-content')) { // Assuming cart-content wraps items and summary
                document.getElementById('cart-content').style.display = 'none';
            }
            return;
        }

        // Cart is not empty, show relevant sections
        if (cartEmptyMessage) cartEmptyMessage.style.display = 'none';
        if (cartSummarySection) cartSummarySection.style.display = 'block';
        if (checkoutButton) checkoutButton.style.display = 'block';
         if (document.getElementById('cart-content')) {
            document.getElementById('cart-content').style.display = 'block';
        }


        cartItemsContainer.innerHTML = ''; // Clear previous items before rendering

        cart.forEach(item => {
            // Fetch full product details to get imageSrc, as cart items might only store id, name, price, quantity
            const productDetails = (typeof getProductById === 'function') ? getProductById(item.id) : null;
            const imageSrc = productDetails ? productDetails.imageSrc : 'img/placeholder.png'; // Fallback image

            const productDetails = (typeof getProductById === 'function') ? getProductById(item.id) : null;
            const imageSrc = productDetails ? productDetails.imageSrc : 'img/placeholder.png';

            const itemElement = document.createElement('div');
            // Responsive classes: stack vertically on small screens, row on sm+
            itemElement.classList.add('flex', 'flex-col', 'sm:flex-row', 'sm:items-center', 'py-4', 'sm:py-6', 'gap-3', 'sm:gap-4', 'cart-item');
            itemElement.dataset.productId = item.id;

            const itemSubtotal = (Number(item.price) || 0) * (Number(item.quantity) || 1);

            // Structure for better responsive layout:
            // Top part (Image + Name/Price) - always flex, but direction changes
            // Bottom part (Qty + Subtotal + Remove) - also flex, direction changes
            itemElement.innerHTML = `
                <div class="flex flex-row items-center gap-3 sm:gap-4 w-full sm:w-auto">
                    <img src="${imageSrc}" alt="${item.name}" class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-md shadow flex-shrink-0">
                    <div class="flex-grow sm:flex-grow-0"> {/* On sm screens, name/price div shouldn't grow excessively */}
                        <h3 class="text-md sm:text-lg font-semibold text-gray-800">${item.name}</h3>
                        <p class="text-sm text-gray-500">Precio: $${Number(item.price).toLocaleString()}</p>
                    </div>
                </div>

                {/* Spacer for larger screens, content for smaller screens */}
                <div class="flex-grow hidden sm:block"></div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 w-full sm:w-auto">
                    <div class="flex items-center gap-1 sm:gap-2 order-2 sm:order-1">
                        <button class="quantity-change bg-gray-200 hover:bg-gray-300 text-gray-700 p-1 rounded-full w-7 h-7 flex items-center justify-center" data-action="decrease" data-product-id="${item.id}">-</button>
                        <input type="number" class="quantity-input w-16 text-center border-gray-300 rounded-md shadow-sm text-sm sm:text-base focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="${item.quantity}" min="1" data-product-id="${item.id}"> {/* Changed w-12 to w-16 */}
                        <button class="quantity-change bg-gray-200 hover:bg-gray-300 text-gray-700 p-1 rounded-full w-7 h-7 flex items-center justify-center" data-action="increase" data-product-id="${item.id}">+</button>
                    </div>
                    <div class="text-sm sm:text-md font-semibold text-gray-700 w-full sm:w-24 text-left sm:text-right order-1 sm:order-2">
                        Subtotal: $${itemSubtotal.toLocaleString()}
                    </div>
                    <button class="remove-item text-red-500 hover:text-red-700 font-medium sm:font-semibold ml-auto sm:ml-2 order-3 text-xs sm:text-sm self-start sm:self-center" data-product-id="${item.id}">
                        Eliminar
                    </button>
                </div>
            `;
            cartItemsContainer.appendChild(itemElement);
        });

        addEventListenersToCartItems();
        updateCartSummary();
    }

    function updateCartSummary() {
        if (!CartAPI || typeof CartAPI.getCartTotal !== 'function') return;

        const total = CartAPI.getCartTotal();
        if (subtotalElement) subtotalElement.textContent = `$${total.toLocaleString()}`;
        if (grandTotalElement) grandTotalElement.textContent = `$${total.toLocaleString()}`; // Assuming no shipping/taxes for now
    }

    function addEventListenersToCartItems() {
        const quantityChangeButtons = cartItemsContainer.querySelectorAll('.quantity-change');
        const quantityInputs = cartItemsContainer.querySelectorAll('.quantity-input');
        const removeButtons = cartItemsContainer.querySelectorAll('.remove-item');

        quantityChangeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.target.dataset.productId;
                const action = e.target.dataset.action;
                const input = cartItemsContainer.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                if (!input) return;
                let currentQuantity = parseInt(input.value, 10);

                if (action === 'increase') {
                    currentQuantity++;
                } else if (action === 'decrease') {
                    currentQuantity--;
                }

                if (currentQuantity <= 0) { // Also handle if user manually types 0 and clicks +/-
                    CartAPI.removeFromCart(productId);
                } else {
                    CartAPI.updateQuantity(productId, currentQuantity);
                }
                renderCartItems(); // Re-render to reflect changes
            });
        });

        quantityInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const productId = e.target.dataset.productId;
                let newQuantity = parseInt(e.target.value, 10);
                if (isNaN(newQuantity) || newQuantity <= 0) {
                     // If invalid input or zero, remove item or set to 1?
                     // For now, let's treat 0 or less as removal, consistent with buttons.
                    CartAPI.removeFromCart(productId);
                } else {
                    CartAPI.updateQuantity(productId, newQuantity);
                }
                renderCartItems(); // Re-render
            });
        });

        removeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.target.dataset.productId;
                CartAPI.removeFromCart(productId);
                renderCartItems(); // Re-render
            });
        });
    }

    if (checkoutButton) {
        checkoutButton.addEventListener('click', () => {
            // Later, this would redirect to a checkout page
            alert('Redirigiendo al checkout...\n(Funcionalidad de checkout no implementada en este paso)');
        });
    }

    // Initial render
    renderCartItems();
});
