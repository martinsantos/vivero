// cart.js
// Sencillo manejo del carrito de compras usando localStorage
// y actualización del contador en el header.

(function () {
  const STORAGE_KEY = 'vivero_cart_items';

  // Obtiene los ítems actuales del carrito desde localStorage
  function getCart() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch (e) {
      console.error('Error leyendo el carrito', e);
      return [];
    }
  }

  // Guarda el carrito nuevamente
  function saveCart(cart) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
  }

  // Añade un ítem al carrito y actualiza la vista
  function addToCart(item) {
    const cart = getCart();
    cart.push(item);
    saveCart(cart);
    updateCounter(cart.length);
    showModal(item);
  }

  // Actualiza el número en la burbuja del carrito
  function updateCounter(count) {
    const counter = document.getElementById('cart-count');
    if (counter) {
      counter.textContent = count;
      counter.style.display = count > 0 ? 'flex' : 'none';
    }
  }

  // Muestra modal cuando se añade un producto
  function showModal(item) {
    // Crear el modal si no existe
    let modal = document.getElementById('cart-modal');
    if (!modal) {
      modal = createModal();
    }

    // Actualizar contenido del modal
    const productName = modal.querySelector('#modal-product-name');
    const productPrice = modal.querySelector('#modal-product-price');
    
    if (productName) productName.textContent = item.name;
    if (productPrice) productPrice.textContent = `$${parseInt(item.price).toLocaleString()} ARS`;

    // Mostrar modal
    modal.style.display = 'flex';
    
    // Auto-cerrar después de 3 segundos
    setTimeout(() => {
      if (modal) modal.style.display = 'none';
    }, 3000);
  }

  // Crea el modal HTML
  function createModal() {
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
      <div style="
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        max-width: 400px;
        margin: 1rem;
        text-align: center;
        animation: modalSlideIn 0.3s ease-out;
      ">
        <div style="color: #4CAF50; font-size: 3rem; margin-bottom: 1rem;">✓</div>
        <h3 style="color: #1F2937; font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">¡Producto añadido!</h3>
        <p style="color: #4B5563; margin-bottom: 1rem;">
          <span id="modal-product-name">Producto</span><br>
          <span id="modal-product-price" style="font-weight: bold;">$0 ARS</span>
        </p>
        <button onclick="document.getElementById('cart-modal').style.display='none'" 
                style="
                  background: #4CAF50;
                  color: white;
                  border: none;
                  padding: 0.75rem 1.5rem;
                  border-radius: 8px;
                  font-weight: bold;
                  cursor: pointer;
                  transition: background 0.2s;
                "
                onmouseover="this.style.background='#388E3C'"
                onmouseout="this.style.background='#4CAF50'">
          Continuar comprando
        </button>
      </div>
    `;

    // Añadir estilos CSS para la animación
    if (!document.querySelector('#modal-styles')) {
      const style = document.createElement('style');
      style.id = 'modal-styles';
      style.textContent = `
        @keyframes modalSlideIn {
          from {
            opacity: 0;
            transform: translateY(-30px) scale(0.9);
          }
          to {
            opacity: 1;
            transform: translateY(0) scale(1);
          }
        }
      `;
      document.head.appendChild(style);
    }

    document.body.appendChild(modal);

    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });

    return modal;
  }

  // Configura listeners en botones
  function setupButtons() {
    const buttons = document.querySelectorAll('[data-add-cart]');
    buttons.forEach((btn) => {
      btn.addEventListener('click', () => {
        const item = {
          id: btn.getAttribute('data-product-id') || Date.now().toString(),
          name: btn.getAttribute('data-product-name') || 'Planta',
          price: btn.getAttribute('data-product-price') || '0',
        };
        addToCart(item);
      });
    });
  }

  // Inicialización
  document.addEventListener('DOMContentLoaded', () => {
    const cart = getCart();
    updateCounter(cart.length);
    setupButtons();
  });
})(); 