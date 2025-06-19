document.addEventListener('DOMContentLoaded', () => {
  const productNameEl = document.getElementById('product-name');
  const productImageContainerEl = document.getElementById('product-image-container'); // The div with background-image
  const productDescriptionEl = document.getElementById('product-description');
  const productCareTipsULEl = document.getElementById('product-care-tips');

  const offerSectionEl = document.getElementById('offer-section');
  const offerTitleEl = document.getElementById('offer-title');
  const offerImageEl = document.getElementById('offer-image'); // The div with background-image
  const offerPriceDetailsEl = document.getElementById('offer-price-details');
  const offerDescriptionEl = document.getElementById('offer-description');

  const addToCartButton = document.getElementById('add-to-cart-single');
  const buyNowButton = document.getElementById('buy-now-single'); // This is the button within the offer section

  // Function to get product ID from URL query parameter
  function getProductIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('productID');
  }

  // Function to populate page with product data
  function populateProductData(product) {
    if (!product) {
      // Handle product not found
      const mainContent = document.querySelector('.layout-content-container'); // Adjust selector if needed
      if (mainContent) {
        mainContent.innerHTML = `
          <div class="text-center py-10">
            <h1 class="text-3xl font-bold text-red-500 mb-4">Producto no encontrado</h1>
            <p class="text-gray-600">Lo sentimos, el producto que buscas no existe o no está disponible.</p>
            <a href="home.html" class="mt-6 inline-block bg-primary text-white py-2 px-4 rounded hover:bg-opacity-80 transition-colors">
              Volver a la página principal
            </a>
          </div>
        `;
      }
      return;
    }

    // Update general product information
    document.title = `Los Cocos - ${product.name}`; // Update page title
    if (productNameEl) productNameEl.textContent = product.name;
    if (productDescriptionEl) productDescriptionEl.innerHTML = product.description; // Use innerHTML if description can contain HTML

    if (productImageContainerEl && product.imageSrc) {
      productImageContainerEl.style.backgroundImage = `url('${product.imageSrc}')`;
    }

    // Populate care tips
    if (productCareTipsULEl && product.careTips && product.careTips.length > 0) {
      productCareTipsULEl.innerHTML = product.careTips.map(tip => `<li>${tip}</li>`).join('');
    } else if (productCareTipsULEl) {
      productCareTipsULEl.innerHTML = '<li>No hay consejos de cuidado específicos disponibles.</li>';
    }

    // Handle offer section
    if (product.offerDetails && offerSectionEl) {
      offerSectionEl.style.display = ''; // Show section
      if (offerTitleEl) offerTitleEl.textContent = product.offerDetails.title;
      if (offerImageEl && product.offerDetails.imageSrc) {
        offerImageEl.style.backgroundImage = `url('${product.offerDetails.imageSrc}')`;
      } else if (offerImageEl) {
        offerImageEl.style.backgroundImage = 'none'; // Or a default placeholder
      }
      if (offerPriceDetailsEl) offerPriceDetailsEl.textContent = product.offerDetails.description; // Example: could be price, or discount text
      if (offerDescriptionEl) offerDescriptionEl.innerHTML = product.offerDetails.description; // Or use a specific field if available

      // Update "Buy Now" button within the offer with product data (if it's meant to add to cart)
      if (buyNowButton) {
        buyNowButton.setAttribute('data-product-id', product.id);
        buyNowButton.setAttribute('data-product-name', product.name);
        // Assuming offer price might be different, or it's just a CTA for the main product price
        buyNowButton.setAttribute('data-product-price', parseFloat(String(product.newPrice).replace('$', '')));
      }

    } else if (offerSectionEl) {
      offerSectionEl.style.display = 'none'; // Hide offer section if no details
    }

    // Update main "Add to Cart" button
    if (addToCartButton) {
      addToCartButton.setAttribute('data-product-id', product.id);
      addToCartButton.setAttribute('data-product-name', product.name);
      addToCartButton.setAttribute('data-product-price', parseFloat(String(product.newPrice).replace('$', '')));
    }

    // Price display (if there's a dedicated element for price outside the buttons or offer)
    // For example, if you add <span id="product-price-main"></span>
    const productPriceMainEl = document.getElementById('product-price-main');
    if (productPriceMainEl) {
        let priceText = '';
        if (product.oldPrice) {
            priceText += `<span class="text-gray-500 line-through mr-2">${product.oldPrice}</span>`;
        }
        priceText += `<span class="font-bold text-2xl text-primary-fg">${product.newPrice}</span>`;
        productPriceMainEl.innerHTML = priceText;
    }


    // Re-initialize cart buttons if cart.js is loaded and function is available
    if (typeof window.setupCartButtons === 'function') {
      window.setupCartButtons();
    } else {
      console.warn('setupCartButtons function from cart.js not found. Cart functionality might be affected.');
    }
  }

  // --- Main execution ---
  const productId = getProductIdFromUrl();

  if (!productId) {
    const mainContent = document.querySelector('.layout-content-container');
    if (mainContent) {
      mainContent.innerHTML = `
        <div class="text-center py-10">
          <h1 class="text-3xl font-bold">ID de Producto no especificado</h1>
          <p class="text-gray-600">Por favor, accede a esta página a través de un enlace de producto válido.</p>
          <a href="home.html" class="mt-6 inline-block bg-primary text-white py-2 px-4 rounded hover:bg-opacity-80 transition-colors">
            Volver a la página principal
          </a>
        </div>
      `;
    }
    return;
  }

  // Ensure products array is available (from js/products.js)
  if (typeof products !== 'undefined' && typeof getProductById === 'function') {
    const product = getProductById(productId);
    populateProductData(product);
  } else {
    console.error('Products array or getProductById function is not available. Ensure js/products.js is loaded correctly.');
    // Display an error on the page as well
    const mainContent = document.querySelector('.layout-content-container');
    if (mainContent) {
        mainContent.innerHTML = `<p class="text-red-500 text-center py-10">Error al cargar los datos del producto. Por favor, intente más tarde.</p>`;
    }
  }
});
