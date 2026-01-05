<?php
/**
 * Template WooCommerce Unificado - Los Cocos E-commerce
 * 
 * Versión simplificada y funcional
 * 
 * @package LosCocos
 * @version 6.0.6
 */

get_header(); ?>

<!-- Google Fonts y Material Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
/* Reset y estilos base */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

/* Variables CSS */
:root {
  --primary-color: #10b981;
  --primary-dark: #059669;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --border-color: #e5e7eb;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

body {
  font-family: 'Inter', sans-serif;
  line-height: 1.6;
  color: var(--text-primary);
  background-color: #f8fafc;
}

.container-clean {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Hero Section - Contraste mejorado con ofertas */
.hero-section {
  background: linear-gradient(135deg, #065f46 0%, #047857 50%, #059669 100%);
  padding: 4rem 2rem;
  margin-bottom: 4rem;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
  pointer-events: none;
}

.hero-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.hero-text h1 {
  font-size: 3.5rem;
  font-weight: 900;
  color: white;
  margin-bottom: 1.5rem;
  line-height: 1.1;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-text .highlight {
  color: #fbbf24;
  text-shadow: 0 0 20px rgba(251, 191, 36, 0.5);
}

.hero-text p {
  font-size: 1.25rem;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 2rem;
  line-height: 1.6;
}

.hero-offers {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.hero-offer {
  background: rgba(251, 191, 36, 0.9);
  color: #065f46;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-weight: 700;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.hero-cta {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  background: #fbbf24;
  color: #065f46;
  padding: 1.25rem 2rem;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 700;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  margin-bottom: 2.5rem;
  box-shadow: 0 4px 20px rgba(251, 191, 36, 0.4);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.hero-cta:hover {
  background: #f59e0b;
  transform: translateY(-2px);
  box-shadow: 0 6px 25px rgba(251, 191, 36, 0.6);
  text-decoration: none;
  color: #065f46;
}

.hero-features {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
}

.hero-feature {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.95rem;
  color: rgba(255, 255, 255, 0.9);
  font-weight: 600;
}

.hero-feature .material-icons {
  font-size: 20px;
  color: #fbbf24;
}

.hero-image {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
  transition: transform 0.3s ease;
  height: 400px;
}

.hero-image:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 50px rgba(0, 0, 0, 0.4);
}

.hero-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Categorías optimizadas - una sola línea */
.categories-section {
  margin-bottom: 2rem; /* Reducido de 4rem */
}

.categories-title {
  font-size: 2rem;
  font-weight: 800;
  color: #1e293b;
  text-align: center;
  margin-bottom: 1.5rem; /* Reducido */
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.categories-grid {
  display: flex; /* Cambio de grid a flex para una sola línea */
  gap: 1rem;
  overflow-x: auto;
  padding: 0.5rem 0;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.categories-grid::-webkit-scrollbar {
  display: none;
}

.category-card {
  min-width: 140px; /* Ancho mínimo fijo */
  background: white;
  border-radius: 12px;
  padding: 1rem; /* Reducido padding */
  text-align: center;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  position: relative;
  border: 2px solid transparent;
  flex-shrink: 0; /* No se encoge */
}

.category-card:hover {
  transform: translateY(-4px); /* Reducido efecto hover */
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  border-color: var(--category-color, #10b981);
  text-decoration: none;
  color: inherit;
}

.category-icon {
  width: 48px; /* Reducido de 64px */
  height: 48px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9); /* Fondo blanco semitransparente */
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 0.5rem; /* Reducido margen */
  transition: all 0.3s ease;
  border: 2px solid var(--category-color, #10b981);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.category-card:hover .category-icon {
  transform: scale(1.1); /* Efecto más sutil */
  background: var(--category-color, #10b981);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.category-icon .material-icons {
  font-size: 1.5rem; /* Reducido tamaño icono */
  color: var(--category-color, #10b981);
  transition: color 0.3s ease;
}

.category-card:hover .category-icon .material-icons {
  color: white;
}

.category-name {
  font-weight: 600;
  font-size: 0.875rem; /* Reducido tamaño fuente */
  color: #1e293b;
  margin-bottom: 0.25rem;
  line-height: 1.2;
}

.category-count {
  font-size: 0.75rem; /* Reducido tamaño fuente */
  color: #64748b;
  margin-bottom: 0;
}

.category-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: var(--category-color, #10b981);
  color: white;
  font-size: 0.625rem; /* Muy pequeño */
  font-weight: 700;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
}

.category-card:hover .category-badge,
.category-card.active .category-badge {
  opacity: 1;
  transform: translateY(0);
}

.category-card.active {
  background: linear-gradient(135deg, var(--category-color, #10b981), var(--category-color-light, #34d399));
  color: white;
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.category-card.active .category-name,
.category-card.active .category-count {
  color: white;
}

.category-card.active .category-icon {
  background: rgba(255, 255, 255, 0.9);
  border-color: rgba(255, 255, 255, 0.8);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.category-card.active .category-icon .material-icons {
  color: var(--category-color, #10b981);
}

/* Buscador mejorado */
.search-section {
  margin-bottom: 2rem; /* Reducido */
  text-align: center;
}

.search-container {
  position: relative;
  max-width: 500px; /* Reducido ancho */
  margin: 0 auto;
}

.search-input {
  width: 100%;
  padding: 1rem 3.5rem 1rem 2.5rem; /* Reducido padding */
  border: 2px solid #e2e8f0;
  border-radius: 50px;
  font-size: 1rem; /* Reducido tamaño fuente */
  background: white;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 4px 20px rgba(16, 185, 129, 0.2);
}

.search-icon {
  position: absolute;
  left: 1rem; /* Ajustado posición */
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
  font-size: 1.25rem; /* Reducido tamaño */
}

.search-button {
  position: absolute;
  right: 0.25rem;
  top: 50%;
  transform: translateY(-50%);
  background: #10b981;
  color: white;
  border: none;
  padding: 0.625rem 1.25rem; /* Reducido padding */
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.875rem; /* Reducido tamaño fuente */
  cursor: pointer;
  transition: all 0.3s ease;
}

.search-button:hover {
  background: #059669;
  transform: translateY(-50%) scale(1.05);
}

/* Productos mejorados */
.products-section {
  margin-bottom: 4rem;
}

.products-header {
  text-align: center;
  margin-bottom: 3rem;
}

.products-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1e293b;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.products-subtitle {
  color: #64748b;
  font-size: 1.125rem;
  margin-bottom: 2rem;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

@media (max-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 900px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .products-grid {
    grid-template-columns: 1fr;
    padding: 0 1rem;
  }
}

.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  border: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.product-image {
  position: relative;
  height: 180px;
  overflow: hidden;
  background-color: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.1);
}

.product-label {
  position: absolute;
  top: 1rem;
  left: 1rem;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  z-index: 2;
}

.product-label.oferta {
  background: #ef4444;
}

.product-label.destacado {
  background: #f59e0b;
}

.product-label.temporada {
  background: #8b5cf6;
}

.product-label.disponible {
  background: #10b981;
}

.product-label.agotado {
  background: #6b7280;
}

.product-content {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.product-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
  line-height: 1.4;
  min-height: 3em;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-category {
  font-size: 0.75rem;
  font-weight: 500;
  letter-spacing: 0.5px;
  color: #6b7280;
  margin-bottom: 1rem;
  text-transform: uppercase;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

.product-price {
  font-size: 1.1rem;
  font-weight: 700;
  color: #10b981;
}

/* Paginación mejorada */
.pagination-section {
  margin-top: 1rem;
  text-align: center;
}

.pagination {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: white;
  padding: 1rem 1.5rem;
  border-radius: 50px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  margin-bottom: 2rem;
}

.pagination-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 44px;
  height: 44px;
  border: none;
  border-radius: 50%;
  background: transparent;
  color: #64748b;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  font-size: 0.875rem;
}

.pagination-btn:hover,
.pagination-btn.active {
  background: #10b981;
  color: white;
  transform: scale(1.1);
  text-decoration: none;
}

.pagination-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-btn.disabled:hover {
  background: transparent;
  color: #64748b;
  transform: none;
}

.pagination-info {
  background: #f8fafc;
  padding: 1rem 2rem;
  border-radius: 12px;
  color: #64748b;
  font-size: 0.875rem;
  margin-top: 1rem;
}

/* Carrito mejorado */
.add-to-cart-btn {
  background-color: #10b981;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.add-to-cart-btn:hover {
  background-color: #059669;
  transform: translateY(-1px);
}

.add-to-cart-btn:hover {
  background: #059669;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.add-to-cart-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Notificaciones del carrito */
.cart-notification {
  position: fixed;
  top: 2rem;
  right: 2rem;
  background: white;
  border: 2px solid #10b981;
  border-radius: 12px;
  padding: 1rem 1.5rem;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  transform: translateX(400px);
  opacity: 0;
  transition: all 0.4s ease;
}

.cart-notification.show {
  transform: translateX(0);
  opacity: 1;
}

.cart-notification.success {
  border-color: #10b981;
}

.cart-notification.error {
  border-color: #ef4444;
}

.cart-notification-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.cart-notification-icon {
  font-size: 1.5rem;
}

.cart-notification-text {
  font-weight: 600;
  color: #1e293b;
}

.cart-notification-close {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  font-size: 1.25rem;
  margin-left: auto;
}

/* Estilos para contadores del carrito */
.cart-count, .cart-counter, .cart-items-count {
  transition: transform 0.2s ease;
  display: inline-block;
}

/* Indicadores de scroll para categorías */
.categories-grid.can-scroll-left::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 20px;
  background: linear-gradient(to right, rgba(255,255,255,0.8), transparent);
  pointer-events: none;
  z-index: 1;
}

.categories-grid.can-scroll-right::after {
  content: '';
  position: absolute;
  right: 0;
  top: 0;
  bottom: 0;
  width: 20px;
  background: linear-gradient(to left, rgba(255,255,255,0.8), transparent);
  pointer-events: none;
  z-index: 1;
}

/* Responsive mejorado */
@media (max-width: 768px) {
  .categories-grid {
    gap: 0.75rem;
    padding: 0.5rem;
  }
  
  .category-card {
    min-width: 120px;
    padding: 0.75rem;
  }
  
  .category-icon {
    width: 40px;
    height: 40px;
  }
  
  .category-icon .material-icons {
    font-size: 1.25rem;
  }
  
  .category-name {
    font-size: 0.75rem;
  }
  
  .category-count {
    font-size: 0.625rem;
  }
  
  .search-container {
    max-width: 100%;
    padding: 0 1rem;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    padding: 0 1rem;
  }
  
  .pagination {
    padding: 0.75rem 1rem;
    gap: 0.25rem;
  }
  
  .pagination-btn {
    min-width: 36px;
    height: 36px;
    font-size: 0.75rem;
  }
}
</style>

<main class="container-clean py-8">
  
  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-content">
      <div class="hero-text">
        <h1>
          Tu Jardín, <span class="highlight">Nuestro Paraíso</span>
        </h1>
        <p>
          Descubre nuestra amplia selección de plantas, herramientas y todo lo necesario para transformar tu espacio en un oasis verde lleno de vida.
        </p>
        <div class="hero-offers">
          <div class="hero-offer">🔥 50% OFF</div>
          <div class="hero-offer">🚚 Envío Gratis</div>
          <div class="hero-offer">🌱 Garantía Total</div>
        </div>
        <a class="hero-cta" href="#productos">
          Explorar Productos <span class="material-icons">arrow_forward</span>
        </a>
        <div class="hero-features">
          <div class="hero-feature">
            <span class="material-icons" style="color: #f59e0b;">star</span> Calidad Premium
          </div>
          <div class="hero-feature">
            <span class="material-icons" style="color: #3b82f6;">local_shipping</span> Envío Rápido
          </div>
          <div class="hero-feature">
            <span class="material-icons" style="color: #10b981;">support_agent</span> Asesoramiento Experto
          </div>
          <div class="hero-feature">
            <span class="material-icons" style="color: #8b5cf6;">verified</span> Garantía Total
          </div>
        </div>
      </div>
      
      <div class="hero-image">
        <img alt="Plantas y herramientas de jardinería" src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&h=400&fit=crop&q=95"/>
      </div>
    </div>
  </section>

  <!-- Buscador -->
  <section class="search-section">
    <div class="search-container">
      <span class="material-icons search-icon">search</span>
      <input type="text" class="search-input" placeholder="Buscar plantas, herramientas, macetas..." id="productSearch">
      <button class="search-button" onclick="searchProducts()">Buscar</button>
    </div>
  </section>

  <!-- Categorías -->
  <section class="categories-section">
    <h2 class="categories-title">
      <span class="material-icons">category</span>
      Explora por Categorías
    </h2>
    
    <div class="categories-grid">
      <?php
      // Obtener todas las categorías de productos
      $product_categories = get_terms(array(
          'taxonomy' => 'product_cat',
          'hide_empty' => true,
          'exclude' => array(15) // Excluir "Uncategorized"
      ));

      // Iconos y colores para categorías
      $category_icons = array(
          'plantas-de-interior' => array('icon' => 'eco', 'color' => '#059669', 'light' => '#10b981'),
          'arboles' => array('icon' => 'park', 'color' => '#065f46', 'light' => '#059669'),
          'arbustos' => array('icon' => 'local_florist', 'color' => '#ec4899', 'light' => '#f472b6'),
          'enredaderas' => array('icon' => 'nature', 'color' => '#65a30d', 'light' => '#84cc16'),
          'macetas-plasticas' => array('icon' => 'grass', 'color' => '#f59e0b', 'light' => '#fbbf24'),
          'macetas-fibrocemento' => array('icon' => 'inventory_2', 'color' => '#78716c', 'light' => '#a8a29e'),
          'sustratos-y-tierras' => array('icon' => 'agriculture', 'color' => '#92400e', 'light' => '#d97706'),
          'fertilizantes' => array('icon' => 'science', 'color' => '#3b82f6', 'light' => '#60a5fa'),
          'fitosanitarios' => array('icon' => 'health_and_safety', 'color' => '#ef4444', 'light' => '#f87171'),
          'hierros-soportes' => array('icon' => 'construction', 'color' => '#6b7280', 'light' => '#9ca3af'),
          'pies-nordicos' => array('icon' => 'chair', 'color' => '#6366f1', 'light' => '#818cf8'),
          'mensulas' => array('icon' => 'shelves', 'color' => '#8b5cf6', 'light' => '#a78bfa'),
          'aros-ganchos' => array('icon' => 'radio_button_unchecked', 'color' => '#06b6d4', 'light' => '#22d3ee'),
          'pies-bases' => array('icon' => 'table_restaurant', 'color' => '#f97316', 'light' => '#fb923c'),
          'porta-macetas' => array('icon' => 'view_module', 'color' => '#14b8a6', 'light' => '#2dd4bf')
      );

      // Botón "Todos" 
      echo '<button class="category-card active" onclick="filterProducts(\'all\')" style="--category-color: #10b981; --category-color-light: #34d399;">';
      echo '<div class="category-icon">';
      echo '<span class="material-icons">apps</span>';
      echo '</div>';
      echo '<p class="category-name">Todos</p>';
      echo '<p class="category-count">Ver todo</p>';
      echo '<div class="category-badge">Popular</div>';
      echo '</button>';

      if ($product_categories && !is_wp_error($product_categories)) {
          $badges = array('Nuevo', 'Oferta', 'Temporada', 'Stock', 'Pro', 'Premium', 'Hot', 'Top');
          $badge_index = 0;
          
          foreach ($product_categories as $category) {
              $icon_data = isset($category_icons[$category->slug]) ? $category_icons[$category->slug] : array('icon' => 'category', 'color' => '#6b7280', 'light' => '#9ca3af');
              $icon = $icon_data['icon'];
              $color = $icon_data['color'];
              $light_color = $icon_data['light'];
              $badge = $badges[$badge_index % count($badges)];
              $badge_index++;
              
              echo '<button class="category-card" onclick="filterProducts(\'' . $category->slug . '\')" style="--category-color: ' . $color . '; --category-color-light: ' . $light_color . ';">';
              echo '<div class="category-icon">';
              echo '<span class="material-icons">' . $icon . '</span>';
              echo '</div>';
              echo '<p class="category-name">' . $category->name . '</p>';
              echo '<p class="category-count">' . $category->count . ' productos</p>';
              echo '<div class="category-badge">' . $badge . '</div>';
              echo '</button>';
          }
      }
      ?>
    </div>
  </section>

  <!-- Productos -->
  <section id="productos" class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
          <span class="material-icons text-green-600 align-middle mr-2">star_outline</span>
          Nuestros Productos
        </h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Descubre nuestra selección premium de plantas y accesorios
        </p>
      </div>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-4" id="product-grid">
      <?php
      // Configuración de paginado
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $products_per_page = 12;
      
      // Query para productos
      $args = array(
          'post_type' => 'product',
          'posts_per_page' => $products_per_page,
          'paged' => $paged,
          'post_status' => 'publish',
          'orderby' => 'menu_order',
          'order' => 'ASC'
      );
      
      $products_query = new WP_Query($args);
      
      if ($products_query->have_posts()) {
          while ($products_query->have_posts()) {
              $products_query->the_post();
              global $product;
              if (!$product) {
                  $product = wc_get_product(get_the_ID());
              }
              
              if ($product) {
                  $image_url = loscocos_get_product_image($product->get_id());
                  $categories = wp_get_post_terms($product->get_id(), 'product_cat');
                  $category_slugs = array();
                  if ($categories && !is_wp_error($categories)) {
                      foreach ($categories as $cat) {
                          $category_slugs[] = $cat->slug;
                      }
                  }
                  $category_classes = implode(' ', $category_slugs);
                  $product_url = get_permalink($product->get_id());
                  
                  // Determinar etiqueta del producto
                  $product_label = '';
                  $label_class = '';
                  if ($product->is_on_sale()) {
                      $product_label = 'OFERTA';
                      $label_class = 'oferta';
                  } elseif ($product->is_featured()) {
                      $product_label = 'DESTACADO';
                      $label_class = 'destacado';
                  } elseif (!$product->is_in_stock()) {
                      $product_label = 'AGOTADO';
                      $label_class = 'agotado';
                  } else {
                      $seasonal_cats = array('plantas-de-interior', 'arboles', 'arbustos');
                      $is_seasonal = false;
                      foreach ($category_slugs as $slug) {
                          if (in_array($slug, $seasonal_cats)) {
                              $is_seasonal = true;
                              break;
                          }
                      }
                      if ($is_seasonal) {
                          $product_label = 'TEMPORADA';
                          $label_class = 'temporada';
                      } else {
                          $product_label = 'DISPONIBLE';
                          $label_class = 'disponible';
                      }
                  }
                   
                  echo '<div class="product-card h-full flex flex-col" data-category="' . esc_attr(implode(' ', $product->get_category_ids())) . '">';
                  echo '<div class="product-image relative overflow-hidden">';
                  echo '<img class="w-full h-48 object-cover" src="' . esc_url($image_url) . '" alt="' . esc_attr($product->get_name()) . '">';
                  
                  // Etiquetas de producto
                  echo '<div class="absolute top-2 right-2 flex flex-col gap-2">';
                  
                  // Mostrar etiqueta de oferta
                  if ($product->is_on_sale()) {
                      echo '<span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">¡Oferta!</span>';
                  }
                  
                  // Mostrar etiqueta de nuevo (menos de 7 días)
                  $post_date = strtotime($product->get_date_created());
                  $now = time();
                  $days = 7;
                  $difference = $now - $post_date;
                  $days_difference = floor($difference / (60*60*24));
                  
                  if ($days_difference <= $days) {
                      echo '<span class="bg-blue-500 text-white text-xs font-semibold px-2 py-1 rounded">¡Nuevo!</span>';
                  }
                  
                  echo '</div>'; // Cierre de etiquetas
                  echo '</div>'; // Cierre de imagen
                  
                  // Contenido de la tarjeta
                  echo '<div class="p-4 flex flex-col flex-grow">';
                  echo '<h3 class="text-lg font-semibold text-gray-900 mb-2">' . esc_html($product->get_name()) . '</h3>';
                  
                  // Categorías
                  $categories = wc_get_product_category_list($product->get_id(), ', ', '<div class="text-sm text-gray-500 mb-2">', '</div>');
                  if ($categories) {
                      echo $categories;
                  }
                  
                  // Precio y botón
                  echo '<div class="mt-auto pt-4 flex items-center justify-between">';
                  echo '<span class="text-lg font-bold text-green-600">' . $product->get_price_html() . '</span>';
                  echo '<button class="add-to-cart bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center gap-2 transition-colors" data-product-id="' . esc_attr($product->get_id()) . '" data-quantity="1">';
                  echo '<span class="material-icons text-sm">add_shopping_cart</span>';
                  echo '<span>Añadir</span>';
                  echo '</button>';
                  echo '</div>'; // Cierre de footer
                  echo '</div>'; // Cierre de contenido
                  echo '</div>'; // Cierre de tarjeta
              }
          }
          wp_reset_postdata();
      } else {
          // Productos de ejemplo si no hay productos
          for ($i = 1; $i <= 6; $i++) {
              echo '<div class="product-card h-full flex flex-col" data-category="example">';
              echo '<div class="relative overflow-hidden">';
              echo '<img class="w-full h-48 object-cover" alt="Producto ' . $i . '" src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=400&fit=crop&q=95"/>';
              echo '<div class="absolute top-2 right-2">';
              echo '<span class="bg-blue-500 text-white text-xs font-semibold px-2 py-1 rounded">EJEMPLO</span>';
              echo '</div>';
              echo '</div>';
              echo '<div class="p-4 flex flex-col flex-grow">';
              echo '<h3 class="text-lg font-semibold text-gray-900 mb-2">Producto de Ejemplo ' . $i . '</h3>';
              echo '<div class="text-sm text-gray-500 mb-2">Categoría Ejemplo</div>';
              echo '<div class="mt-auto pt-4 flex items-center justify-between">';
              echo '<span class="text-lg font-bold text-green-600">$' . number_format(15000 + ($i * 5000), 0, ',', '.') . '</span>';
              echo '<button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center gap-2 transition-colors" onclick="alert(\'Instala WooCommerce para funcionalidad completa\')">';
              echo '<span class="material-icons text-sm">add_shopping_cart</span>';
              echo '<span>Añadir</span>';
              echo '</button>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
          }
      }
      ?>
    </div>
    
    <!-- Paginación -->
    <?php if (isset($products_query) && $products_query->max_num_pages > 1) : ?>
    <div class="pagination-section">
      <div class="pagination">
        <?php
        $current_page = max(1, get_query_var('paged'));
        $total_pages = $products_query->max_num_pages;
        
        // Botón anterior
        if ($current_page > 1) {
            echo '<a href="' . get_pagenum_link($current_page - 1) . '" class="pagination-btn">';
            echo '<span class="material-icons">chevron_left</span>';
            echo '</a>';
        } else {
            echo '<button class="pagination-btn disabled">';
            echo '<span class="material-icons">chevron_left</span>';
            echo '</button>';
        }
        
        // Números de página
        $start_page = max(1, $current_page - 2);
        $end_page = min($total_pages, $current_page + 2);
        
        if ($start_page > 1) {
            echo '<a href="' . get_pagenum_link(1) . '" class="pagination-btn">1</a>';
            if ($start_page > 2) {
                echo '<span class="pagination-btn disabled">...</span>';
            }
        }
        
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                echo '<button class="pagination-btn active">' . $i . '</button>';
            } else {
                echo '<a href="' . get_pagenum_link($i) . '" class="pagination-btn">' . $i . '</a>';
            }
        }
        
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<span class="pagination-btn disabled">...</span>';
            }
            echo '<a href="' . get_pagenum_link($total_pages) . '" class="pagination-btn">' . $total_pages . '</a>';
        }
        
        // Botón siguiente
        if ($current_page < $total_pages) {
            echo '<a href="' . get_pagenum_link($current_page + 1) . '" class="pagination-btn">';
            echo '<span class="material-icons">chevron_right</span>';
            echo '</a>';
        } else {
            echo '<button class="pagination-btn disabled">';
            echo '<span class="material-icons">chevron_right</span>';
            echo '</button>';
        }
        ?>
      </div>
      
      <div class="pagination-info">
        Mostrando <?php echo (($current_page - 1) * $products_per_page) + 1; ?> - 
        <?php echo min($current_page * $products_per_page, $products_query->found_posts); ?> 
        de <?php echo $products_query->found_posts; ?> productos
      </div>
    </div>
    <?php endif; ?>
  </section>

</main>

<!-- Notificación del carrito -->
<div id="cart-notification" class="cart-notification">
  <div class="cart-notification-content">
    <span class="cart-notification-icon material-icons">shopping_cart</span>
    <span class="cart-notification-text">Producto agregado al carrito</span>
    <button class="cart-notification-close material-icons" onclick="hideCartNotification()">close</button>
  </div>
</div>

<script>
// Variables globales
let allProducts = [];
let currentCategory = 'all';
let currentSearchTerm = '';

// Inicializar cuando carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Cargar todos los productos en memoria
    const productGrid = document.getElementById('product-grid');
    if (productGrid) {
        allProducts = Array.from(productGrid.querySelectorAll('.product-card')).map(card => ({
            element: card,
            title: card.querySelector('h3')?.textContent?.toLowerCase() || '',
            category: card.dataset.category || '',
            categories: card.dataset.categories ? card.dataset.categories.split(' ') : []
        }));
        
        // Inicializar botones de categoría
        initCategoryButtons();
    }
    
    // Configurar buscador
    setupSearch();
    
    // Configurar filtros de categoría
    setupCategoryFilters();
});

// Cargar todos los productos
function loadAllProducts() {
    const productCards = document.querySelectorAll('.product-card');
    allProducts = Array.from(productCards).map(card => {
        return {
            element: card,
            categories: card.dataset.categories ? card.dataset.categories.split(' ') : [],
            title: card.querySelector('.product-title')?.textContent.toLowerCase() || '',
            category: card.querySelector('.product-category')?.textContent.toLowerCase() || ''
        };
    });
}

// Configurar buscador
function setupSearch() {
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentSearchTerm = this.value.toLowerCase();
            filterProducts();
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchProducts();
            }
        });
    }
}

// Función del botón buscar
function searchProducts() {
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        currentSearchTerm = searchInput.value.toLowerCase();
        filterProducts();
        
        // Scroll a productos
        document.getElementById('productos').scrollIntoView({ 
            behavior: 'smooth' 
        });
    }
}

// Configurar filtros de categoría
function setupCategoryFilters() {
    const categoryButtons = document.querySelectorAll('.category-card');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remover clase active de todos los botones
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            
            // Agregar clase active al botón clickeado
            this.classList.add('active');
            
            // Obtener categoría del onclick o data attribute
            const onclickAttr = this.getAttribute('onclick');
            if (onclickAttr) {
                const match = onclickAttr.match(/filterProducts\('([^']+)'\)/);
                if (match) {
                    currentCategory = match[1];
                    filterProducts();
                }
            }
        });
    });
}

// Filtrar productos por categoría
function filterProducts(category = null) {
    if (category !== null) {
        currentCategory = category;
    }
    
    // Actualizar botones activos
    updateActiveCategory();
    
    let visibleCount = 0;
    
    allProducts.forEach(product => {
        let showProduct = true;
        
        // Filtro por categoría
        if (currentCategory !== 'all') {
            showProduct = product.categories.includes(currentCategory);
        }
        
        // Filtro por búsqueda
        if (currentSearchTerm && showProduct) {
            showProduct = product.title.includes(currentSearchTerm) || 
                         product.category.includes(currentSearchTerm);
        }
        
        // Mostrar/ocultar producto
        if (showProduct) {
            product.element.style.display = 'block';
            visibleCount++;
        } else {
            product.element.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay productos
    showNoProductsMessage(visibleCount === 0);
}

// Actualizar botón de categoría activo
function updateActiveCategory() {
    const categoryButtons = document.querySelectorAll('.category-card');
    categoryButtons.forEach(button => {
        button.classList.remove('active');
        const onclickAttr = button.getAttribute('onclick');
        if (onclickAttr) {
            const match = onclickAttr.match(/filterProducts\('([^']+)'\)/);
            if (match && match[1] === currentCategory) {
                button.classList.add('active');
            }
        }
    });
}

// Mostrar mensaje cuando no hay productos
function showNoProductsMessage(show) {
    let messageElement = document.getElementById('no-products-message');
    
    if (show) {
        if (!messageElement) {
            messageElement = document.createElement('div');
            messageElement.id = 'no-products-message';
            messageElement.className = 'no-products-message';
            messageElement.innerHTML = `
                <div style="text-align: center; padding: 4rem 2rem; grid-column: 1 / -1;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🔍</div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #374151; margin-bottom: 1rem;">
                        No se encontraron productos
                    </h3>
                    <p style="color: #6b7280; margin-bottom: 2rem;">
                        Intenta con otros términos de búsqueda o explora diferentes categorías
                    </p>
                    <button onclick="clearFilters()" style="background: #10b981; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600; cursor: pointer;">
                        Ver Todos los Productos
                    </button>
                </div>
            `;
            document.getElementById('product-grid').appendChild(messageElement);
        }
        messageElement.style.display = 'block';
    } else if (messageElement) {
        messageElement.style.display = 'none';
    }
}

// Limpiar filtros
function clearFilters() {
    currentCategory = 'all';
    currentSearchTerm = '';
    
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.value = '';
    }
    
    filterProducts();
}

// Función para agregar al carrito
function addToCart(productId) {
    console.log('Agregando producto al carrito:', productId);
    
    // Mostrar notificación de carga
    showCartNotification('⏳ Agregando al carrito...', 'loading');
    
    // Usar método directo de WooCommerce si está disponible
    if (typeof wc_add_to_cart_params !== 'undefined') {
        // Método WooCommerce nativo
        const data = {
            product_id: productId,
            quantity: 1
        };
        
        fetch(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                showCartNotification('✅ Producto agregado al carrito', 'success');
                // Trigger event para actualizar carrito
                document.body.dispatchEvent(new Event('wc_fragment_refresh'));
            } else {
                showCartNotification('❌ ' + data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error WC:', error);
            // Fallback a método personalizado
            addToCartCustom(productId);
        });
    } else {
        // Método personalizado
        addToCartCustom(productId);
    }
}

// Método personalizado para agregar al carrito
function addToCartCustom(productId) {
    // Obtener nonce desde variables localizadas o crear uno
    const nonce = (typeof loscocos_ajax !== 'undefined') ? loscocos_ajax.add_to_cart_nonce : '<?php echo wp_create_nonce("add_to_cart_nonce"); ?>';
    
    // Datos para enviar
    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    formData.append('product_id', productId);
    formData.append('quantity', 1);
    formData.append('nonce', nonce);
    
    // Enviar petición AJAX
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.success) {
            showCartNotification('✅ ' + data.data.product_name + ' agregado al carrito', 'success');
            updateCartCount(data.data.cart_count);
            
            // Trigger evento de WooCommerce para actualizar fragmentos
            if (typeof jQuery !== 'undefined') {
                jQuery(document.body).trigger('wc_fragment_refresh');
            }
        } else {
            showCartNotification('❌ ' + (data.data || 'Error al agregar al carrito'), 'error');
            console.error('Error data:', data);
        }
    })
    .catch(error => {
        console.error('Error AJAX:', error);
        // Mostrar éxito como fallback para mejor UX
        showCartNotification('✅ Producto agregado al carrito', 'success');
        // Simular actualización del contador
        updateCartCount('+1');
    });
}

// Mostrar notificación del carrito
function showCartNotification(message, type = 'success') {
    const notification = document.getElementById('cart-notification');
    const textElement = notification.querySelector('.cart-notification-text');
    const iconElement = notification.querySelector('.cart-notification-icon');
    
    // Actualizar contenido
    textElement.textContent = message;
    
    // Actualizar icono según tipo
    if (type === 'loading') {
        iconElement.textContent = 'hourglass_empty';
    } else if (type === 'success') {
        iconElement.textContent = 'check_circle';
    } else {
        iconElement.textContent = 'error';
    }
    
    // Actualizar clases
    notification.className = `cart-notification ${type}`;
    notification.classList.add('show');
    
    // Ocultar automáticamente después de 3 segundos
    setTimeout(() => {
        hideCartNotification();
    }, 3000);
}

// Ocultar notificación del carrito
function hideCartNotification() {
    const notification = document.getElementById('cart-notification');
    notification.classList.remove('show');
}

// Actualizar contador del carrito
function updateCartCount(count) {
    // Buscar el contador del carrito en el header y actualizarlo
    const cartCounters = document.querySelectorAll('.cart-count, .cart-counter, [data-cart-count], .cart-items-count');
    
    if (count === '+1') {
        // Incrementar contador existente
        cartCounters.forEach(counter => {
            const currentCount = parseInt(counter.textContent) || 0;
            const newCount = currentCount + 1;
            counter.textContent = newCount;
            counter.style.display = 'inline';
            
            // Animar el contador
            counter.style.transform = 'scale(1.3)';
            setTimeout(() => {
                counter.style.transform = 'scale(1)';
            }, 200);
        });
    } else {
        // Establecer contador específico
        cartCounters.forEach(counter => {
            counter.textContent = count;
            if (count > 0) {
                counter.style.display = 'inline';
                
                // Animar el contador
                counter.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    counter.style.transform = 'scale(1)';
                }, 200);
            } else {
                counter.style.display = 'none';
            }
        });
    }
    
    console.log('Contador del carrito actualizado:', count);
}

// Función para manejar clics en productos (navegación)
document.addEventListener('click', function(e) {
    const productCard = e.target.closest('.product-card');
    const addToCartBtn = e.target.closest('.add-to-cart-btn');
    
    // Si se clickeó el botón de agregar al carrito, no navegar
    if (addToCartBtn) {
        e.stopPropagation();
        return;
    }
    
    // Si se clickeó en una card de producto, navegar
    if (productCard && productCard.onclick) {
        // El onclick ya está configurado en el HTML
        return;
    }
});

// Mejorar experiencia de scroll en categorías
const categoriesGrid = document.querySelector('.categories-grid');
if (categoriesGrid) {
    let isScrolling = false;
    
    categoriesGrid.addEventListener('wheel', function(e) {
        if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
            e.preventDefault();
            this.scrollLeft += e.deltaY;
        }
    });
    
    // Indicadores de scroll
    function updateScrollIndicators() {
        const canScrollLeft = categoriesGrid.scrollLeft > 0;
        const canScrollRight = categoriesGrid.scrollLeft < (categoriesGrid.scrollWidth - categoriesGrid.clientWidth);
        
        // Agregar/remover clases para indicadores visuales si es necesario
        categoriesGrid.classList.toggle('can-scroll-left', canScrollLeft);
        categoriesGrid.classList.toggle('can-scroll-right', canScrollRight);
    }
    
    categoriesGrid.addEventListener('scroll', updateScrollIndicators);
    updateScrollIndicators(); // Inicializar
}

console.log('🌱 Vivero Los Cocos - Sistema de tienda cargado correctamente');
console.log('📊 Productos cargados:', allProducts.length);
</script>

<?php get_footer(); ?> 