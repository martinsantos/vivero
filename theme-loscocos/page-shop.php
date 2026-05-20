<?php
/**
 * Template específico para la página de tienda
 * Template Name: Tienda Los Cocos - Diseño Moderno
 */

get_header(); ?>

<!-- Google Fonts y Material Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<!-- Estilos específicos para la nueva tienda -->
<style>
/* Reset y fuentes base */
body, * {
  font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
  box-sizing: border-box;
}

/* Material Icons */
.material-icons {
  font-family: 'Material Icons' !important;
  font-weight: normal !important;
  font-style: normal !important;
  font-size: 24px !important;
  line-height: 1 !important;
  letter-spacing: normal !important;
  text-transform: none !important;
  display: inline-block !important;
  white-space: nowrap !important;
  word-wrap: normal !important;
  direction: ltr !important;
  -webkit-font-feature-settings: 'liga' !important;
  -webkit-font-smoothing: antialiased !important;
}

/* Contenedor principal */
.container-clean {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Hero Section Mejorada */
.hero-section {
  background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #d1fae5 100%);
  border-radius: 2rem;
  padding: 3rem 2rem;
  margin-bottom: 4rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 800;
  color: #065f46;
  margin-bottom: 1.5rem;
  line-height: 1.1;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hero-subtitle {
  font-size: 1.25rem;
  color: #374151;
  margin-bottom: 2rem;
  line-height: 1.6;
  font-weight: 500;
}

.hero-cta {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 1rem 2rem;
  border-radius: 9999px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
}

.hero-cta:hover {
  transform: translateY(-2px);
  box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.4);
  color: white;
  text-decoration: none;
}

.hero-features {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-top: 2rem;
}

.hero-feature {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #065f46;
  font-weight: 600;
  font-size: 0.875rem;
}

/* Grid de imágenes hero */
.hero-images {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.hero-image-card {
  position: relative;
  overflow: hidden;
  border-radius: 1rem;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.hero-image-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
}

.hero-image-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.hero-image-card:hover img {
  transform: scale(1.05);
}

.hero-image-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0) 100%);
  padding: 1.5rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.hero-image-card:hover .hero-image-overlay {
  opacity: 1;
}

.hero-image-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: white;
  margin-bottom: 0.5rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.hero-image-button {
  background: #10b981;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 9999px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.875rem;
  transition: background 0.2s ease;
}

.hero-image-button:hover {
  background: #059669;
  color: white;
  text-decoration: none;
}

/* Categorías */
.categories-section {
  margin-bottom: 4rem;
}

.categories-scroll {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  padding: 1rem 0;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.categories-scroll::-webkit-scrollbar {
  display: none;
}

.category-item {
  min-width: 180px;
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  text-align: center;
  border: 2px solid transparent;
  transition: all 0.3s ease;
  cursor: pointer;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.category-item:hover {
  transform: scale(1.05);
  border-color: #10b981;
  box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.2);
}

.category-item.active {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border-color: #059669;
  box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.3);
}

.category-icon-container {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 0.75rem;
  transition: all 0.3s ease;
}

.category-item:hover .category-icon-container {
  transform: rotateY(180deg);
}

.category-name {
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 0.25rem;
  color: inherit;
}

.category-count {
  font-size: 0.875rem;
  color: #6b7280;
}

.category-item.active .category-count {
  color: rgba(255, 255, 255, 0.8);
}

.category-item.active .material-icons {
  color: white !important;
}

/* Productos */
.products-section {
  margin-bottom: 4rem;
}

.products-header {
  text-align: center;
  margin-bottom: 3rem;
}

.products-title {
  font-size: 3rem;
  font-weight: 800;
  color: #374151;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.products-grid {
  display: grid !important;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
  gap: 1.5rem !important;
  padding: 2rem 0 !important;
}

/* 
   Estilos de .product-card removidos - ahora usamos .product-card-standard 
   que está definido en style.css para uniformidad en todo el sitio
*/

.load-more-btn {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 9999px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0 auto;
  transition: all 0.3s ease;
  font-size: 1.125rem;
}

.load-more-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
}

/* Responsividad */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-subtitle {
    font-size: 1.125rem;
  }
  
  .hero-images {
    grid-template-columns: 1fr;
  }
  
  .hero-features {
    flex-direction: column;
    gap: 1rem;
  }
  
  .products-title {
    font-size: 2rem;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)) !important;
    gap: 1.5rem !important;
  }
  
  .category-item {
    min-width: 150px;
    padding: 1rem;
  }
  
  .category-icon-container {
    width: 48px;
    height: 48px;
  }
}

@media (max-width: 480px) {
  .container-clean {
    padding: 0 0.5rem;
  }
  
  .hero-section {
    padding: 2rem 1rem;
  }
  
  .products-grid {
    grid-template-columns: 1fr !important;
  }
}

/* Animaciones */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.product-card-standard {
  animation: fadeInUp 0.6s ease forwards;
}

/* Notificaciones */
.notification {
  position: fixed;
  top: 1rem;
  right: 1rem;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  color: white;
  font-weight: 600;
  z-index: 1000;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  animation: fadeInUp 0.3s ease;
}

.notification.success {
  background: linear-gradient(135deg, #10b981, #059669);
}

.notification.error {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}

.notification.loading {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
}
</style>

<main class="container-clean py-8">
  <!-- Hero Section Mejorada -->
  <section class="hero-section">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
      <div>
        <h1 class="hero-title">
          Tu Jardín, <span style="color: #10b981;">Nuestro Paraíso</span>
        </h1>
        <p class="hero-subtitle">
          Descubre nuestra amplia selección de plantas, herramientas y todo lo necesario para transformar tu espacio en un oasis verde lleno de vida.
        </p>
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
      
      <!-- Grid de imágenes hero -->
      <div class="hero-images">
        <div class="hero-image-card" style="height: 250px;">
          <img alt="Plantas de interior" src="https://images.unsplash.com/photo-1463320726281-696a485928c7?w=400&h=400&fit=crop&q=95"/>
          <div class="hero-image-overlay">
            <h3 class="hero-image-title">Plantas de Interior</h3>
            <a class="hero-image-button" href="#plantas-interior">Ver Colección <span class="material-icons">chevron_right</span></a>
          </div>
        </div>
        <div class="hero-image-card" style="height: 250px;">
          <img alt="Herramientas de jardinería" src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=400&fit=crop&q=95"/>
          <div class="hero-image-overlay">
            <h3 class="hero-image-title">Herramientas</h3>
            <a class="hero-image-button" href="#herramientas">Descubrir <span class="material-icons">chevron_right</span></a>
          </div>
        </div>
        <div class="hero-image-card" style="grid-column: span 2; height: 200px;">
          <img alt="Flores vibrantes" src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=800&h=400&fit=crop&q=95"/>
          <div class="hero-image-overlay">
            <h3 class="hero-image-title">Flores de Temporada</h3>
            <a class="hero-image-button" href="#flores">Comprar Ahora <span class="material-icons">shopping_bag</span></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Categorías mejoradas -->
  <section class="categories-section">
    <h2 style="text-align: center; font-size: 2.5rem; font-weight: 800; color: #374151; margin-bottom: 2rem;">
      <span class="material-icons" style="font-size: 3rem; color: #10b981; vertical-align: middle; margin-right: 0.5rem;">category</span>
      Explora por Categorías
    </h2>
    
    <div class="categories-scroll">
      <?php
      // Obtener todas las categorías de productos
      $product_categories = get_terms(array(
          'taxonomy' => 'product_cat',
          'hide_empty' => true,
          'exclude' => array(15) // Excluir "Uncategorized"
      ));

      // Iconos y colores para categorías
      $category_icons = array(
          'plantas-de-interior' => array('icon' => 'eco', 'color' => 'green'),
          'arboles' => array('icon' => 'park', 'color' => 'emerald'),
          'arbustos' => array('icon' => 'local_florist', 'color' => 'pink'),
          'enredaderas' => array('icon' => 'nature', 'color' => 'lime'),
          'macetas-plasticas' => array('icon' => 'grass', 'color' => 'amber'),
          'macetas-fibrocemento' => array('icon' => 'inventory_2', 'color' => 'stone'),
          'sustratos-y-tierras' => array('icon' => 'agriculture', 'color' => 'amber'),
          'fertilizantes' => array('icon' => 'science', 'color' => 'blue'),
          'fitosanitarios' => array('icon' => 'health_and_safety', 'color' => 'red'),
          'hierros-soportes' => array('icon' => 'construction', 'color' => 'gray'),
          'pies-nordicos' => array('icon' => 'chair', 'color' => 'indigo'),
          'mensulas' => array('icon' => 'shelves', 'color' => 'purple'),
          'aros-ganchos' => array('icon' => 'radio_button_unchecked', 'color' => 'cyan'),
          'pies-bases' => array('icon' => 'table_restaurant', 'color' => 'orange'),
          'porta-macetas' => array('icon' => 'view_module', 'color' => 'teal')
      );

      // Botón "Todos" activo por defecto
      echo '<button class="category-item active" onclick="filterProducts(\'all\')">';
      echo '<div class="category-icon-container" style="background-color: #dcfce7;">';
      echo '<span class="material-icons" style="font-size: 2rem; color: #10b981;">apps</span>';
      echo '</div>';
      echo '<p class="category-name">Todos</p>';
      echo '<p class="category-count">Ver todo</p>';
      echo '</button>';

      if ($product_categories && !is_wp_error($product_categories)) {
          foreach ($product_categories as $category) {
              $icon_data = isset($category_icons[$category->slug]) ? $category_icons[$category->slug] : array('icon' => 'category', 'color' => 'gray');
              $icon = $icon_data['icon'];
              $color = $icon_data['color'];
              
              // Colores de fondo para iconos
              $bg_colors = array(
                  'green' => '#dcfce7', 'emerald' => '#d1fae5', 'pink' => '#fce7f3', 'lime' => '#ecfccb',
                  'amber' => '#fef3c7', 'stone' => '#f5f5f4', 'blue' => '#dbeafe', 'red' => '#fee2e2',
                  'gray' => '#f3f4f6', 'indigo' => '#e0e7ff', 'purple' => '#f3e8ff', 'cyan' => '#cffafe',
                  'orange' => '#fed7aa', 'teal' => '#ccfbf1'
              );
              
              // Colores de iconos
              $icon_colors = array(
                  'green' => '#10b981', 'emerald' => '#059669', 'pink' => '#ec4899', 'lime' => '#65a30d',
                  'amber' => '#f59e0b', 'stone' => '#78716c', 'blue' => '#3b82f6', 'red' => '#ef4444',
                  'gray' => '#6b7280', 'indigo' => '#6366f1', 'purple' => '#8b5cf6', 'cyan' => '#06b6d4',
                  'orange' => '#f97316', 'teal' => '#14b8a6'
              );
              
              $bg_color = isset($bg_colors[$color]) ? $bg_colors[$color] : '#f3f4f6';
              $icon_color = isset($icon_colors[$color]) ? $icon_colors[$color] : '#6b7280';
              
              echo '<button class="category-item" onclick="filterProducts(\'' . $category->slug . '\')">';
              echo '<div class="category-icon-container" style="background-color: ' . $bg_color . ';">';
              echo '<span class="material-icons" style="font-size: 2rem; color: ' . $icon_color . ';">' . $icon . '</span>';
              echo '</div>';
              echo '<p class="category-name">' . $category->name . '</p>';
              echo '<p class="category-count">' . $category->count . ' productos</p>';
              echo '</button>';
          }
      }
      ?>
    </div>
  </section>

  <!-- Productos Destacados -->
  <section id="productos" class="products-section">
    <div class="products-header">
      <h2 class="products-title">
        <span class="material-icons" style="color: #f59e0b;">star_outline</span> 
        Productos Destacados
      </h2>
    </div>
    
    <div id="products-container" class="products-grid">
      <?php
      // Obtener productos (paginados)
      $paged = get_query_var('paged') ? get_query_var('paged') : 1;
      $args = array(
          'post_type' => 'product',
          'posts_per_page' => 12,
          'paged' => $paged,
          'post_status' => 'publish',
          'orderby' => 'menu_order title',
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
                  
                  // Obtener emoji de categoría
                  $category_emoji = '🌱';
                  if ($categories && !is_wp_error($categories)) {
                      $category_emojis = array(
                          'plantas-de-interior' => '🪴',
                          'arboles' => '🌳',
                          'arbustos' => '🌿',
                          'macetas-plasticas' => '🏺',
                          'macetas-fibrocemento' => '🏺',
                          'sustratos-y-tierras' => '🌱',
                          'fertilizantes' => '🧪',
                          'hierros-soportes' => '🔧'
                      );
                      $category_slug = $categories[0]->slug;
                      $category_emoji = isset($category_emojis[$category_slug]) ? $category_emojis[$category_slug] : '🌱';
                  }
                  
                  // Determinar etiqueta del producto
                  $product_label = '';
                  $label_class = '';
                  if ($product->is_on_sale()) {
                      $product_label = 'OFERTA';
                      $label_class = 'oferta';
                  } elseif ($product->is_featured()) {
                      $product_label = 'DESTACADO';
                      $label_class = 'promocion';
                  } elseif (!$product->is_in_stock()) {
                      $product_label = 'AGOTADO';
                      $label_class = 'agotado';
                  } else {
                      $product_label = 'DISPONIBLE';
                      $label_class = 'temporada';
                  }
                   
                   echo '<a href="' . esc_url($product_url) . '" class="product-card-standard" data-categories="' . $category_classes . '">';
                   
                   // Imagen del producto
                   echo '<div class="product-image">';
                   echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product->get_name()) . '">';
                   
                   // Badge de categoría
                   echo '<div class="category-badge">' . $category_emoji . '</div>';
                   
                   // Etiqueta especial
                   if ($product_label) {
                       echo '<div class="product-label ' . $label_class . '">' . $product_label . '</div>';
                   }
                   
                   // Overlay de hover
                   echo '<div class="hover-overlay">';
                   echo '<div class="hover-text">👁️ Ver Detalles del Producto</div>';
                   echo '</div>';
                   echo '</div>';
                   
                   // Contenido de la card
                   echo '<div class="product-content">';
                   echo '<h3 class="product-title">' . esc_html($product->get_name()) . '</h3>';
                   
                   // Categoría
                   if ($categories && !is_wp_error($categories)) {
                       echo '<div class="product-category">' . esc_html($categories[0]->name) . '</div>';
                   }
                   
                   // Precio
                   echo '<div class="product-price">';
                   echo '<div>';
                   if ($product->is_on_sale()) {
                       echo '<span class="price-current">$' . number_format($product->get_sale_price(), 0, ',', '.') . '</span>';
                       echo '<span class="price-original">$' . number_format($product->get_regular_price(), 0, ',', '.') . '</span>';
                   } else {
                       echo '<span class="price-current">$' . number_format($product->get_price(), 0, ',', '.') . '</span>';
                   }
                   echo '<div class="price-label">Precio final</div>';
                   echo '</div>';
                   echo '<div class="hover-arrow">→</div>';
                   echo '</div>';
                   
                   echo '</div>'; // product-content
                   echo '</a>'; // product-card-standard
              }
          }
          wp_reset_postdata();
      }
      ?>
    </div>
    
    <!-- Botón Ver Más -->
    <div style="text-align: center; margin-top: 3rem;">
      <button id="load-more-btn" class="load-more-btn">
        Ver Más Productos <span class="material-icons">expand_more</span>
      </button>
    </div>
  </section>
</main>

<!-- JavaScript optimizado -->
<script>
// Función para filtrar productos por categoría
function filterProducts(category) {
    const products = document.querySelectorAll('.product-card-standard');
    const categoryButtons = document.querySelectorAll('.category-item');
    
    // Actualizar botones activos
    categoryButtons.forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.category-item').classList.add('active');
    
    // Filtrar productos con animación
    products.forEach((product, index) => {
        setTimeout(() => {
            if (category === 'all') {
                product.style.display = 'block';
                product.style.animation = 'fadeInUp 0.6s ease forwards';
            } else {
                const productCategories = product.dataset.categories;
                if (productCategories && productCategories.includes(category)) {
                    product.style.display = 'block';
                    product.style.animation = 'fadeInUp 0.6s ease forwards';
                } else {
                    product.style.display = 'none';
                }
            }
        }, index * 50);
    });
}

// Función para mostrar notificaciones
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Función para cargar más productos
document.getElementById('load-more-btn')?.addEventListener('click', function() {
    // Aquí se puede implementar la carga de más productos vía AJAX
    showNotification('🔄 Función de cargar más productos próximamente', 'loading');
});

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌱 Tienda moderna con cards uniformes cargada correctamente');
    
    // Animación de entrada para las cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.product-card-standard').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});
</script>

<?php get_footer(); ?> 