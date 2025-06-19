// js/home-search.js

/**
 * Filters and re-renders products on the homepage based on the search term.
 * This function relies on `renderProducts` being available from `modal.js`
 * and `CartAPI` being available from `cart.js`.
 * @param {string} searchTerm - The term from the search input.
 */
function filterProductsOnHome(searchTerm) {
  const lowerSearchTerm = searchTerm.trim().toLowerCase();

  // Call renderProducts (expected to be globally available from modal.js)
  if (typeof renderProducts === 'function') {
    renderProducts('home-product-grid', 'ofertas', lowerSearchTerm);
    renderProducts('productos-destacados-grid', 'destacados', lowerSearchTerm);
  } else {
    console.error('[filterProductsOnHome] global renderProducts function not found. Make sure modal.js is loaded.');
    return; // Stop if renderProducts isn't available
  }

  // After re-rendering products, cart buttons might need their listeners re-attached
  if (window.CartAPI && typeof window.CartAPI.setupCartButtons === 'function') {
    window.CartAPI.setupCartButtons();
  } else {
    console.warn('[filterProductsOnHome] CartAPI.setupCartButtons not found. Cart buttons on filtered products might not work.');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('product-search-input');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      // `this.value` refers to the current value of the search input
      filterProductsOnHome(this.value);
    });
  } else {
    console.warn('[home-search.js] Product search input with ID "product-search-input" not found on home.html.');
  }
});
