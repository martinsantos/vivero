<?php get_header(); ?>

<style>
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
}

.container-clean {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
  padding: 3rem 0;
  margin-bottom: 3rem;
  border-radius: 12px;
}

.hero-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
}

.hero-text h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.hero-text .highlight {
  color: var(--primary-color);
}

.hero-text p {
  font-size: 1.125rem;
  color: var(--text-secondary);
  margin-bottom: 2rem;
}

.hero-cta {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--primary-color);
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s ease;
  margin-bottom: 2rem;
}

.hero-cta:hover {
  background: var(--primary-dark);
  transform: translateY(-1px);
  text-decoration: none;
  color: white;
}

.hero-features {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.hero-feature {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.hero-images {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-template-rows: 1fr 1fr;
  gap: 1rem;
  height: 400px;
}

.hero-image-card {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
  transition: transform 0.3s ease;
}

.hero-image-card:hover {
  transform: translateY(-4px);
}

.hero-image-card:first-child {
  grid-row: 1 / 3;
}

.hero-image-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-image-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
  padding: 1.5rem;
  color: white;
}

.hero-image-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.hero-image-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.875rem;
  transition: background 0.2s ease;
}

.hero-image-button:hover {
  background: var(--primary-color);
  color: white;
  text-decoration: none;
}

/* Categorías */
.categories-section {
  margin-bottom: 3rem;
}

.categories-title {
  text-align: center;
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1.5rem;
}

.category-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 2rem 1.5rem;
  background: white;
  border: 2px solid var(--border-color);
  border-radius: 12px;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s ease;
  box-shadow: var(--shadow-sm);
}

.category-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary-color);
  text-decoration: none;
  color: inherit;
}

.category-card.active {
  border-color: var(--primary-color);
  background: #f0fdf4;
}

.category-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.category-name {
  font-weight: 600;
  font-size: 1rem;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  margin-top: 0;
}

.category-count {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

/* Productos */
.products-section {
  margin-bottom: 3rem;
}

.products-header {
  text-align: center;
  margin-bottom: 3rem;
}

.products-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
  text-decoration: none;
  color: inherit;
  border: 1px solid var(--border-color);
}

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  text-decoration: none;
  color: inherit;
}

.product-image {
  position: relative;
  height: 220px;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.product-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
}

.product-badge.sale {
  background: #ef4444;
  color: white;
}

.product-badge.featured {
  background: #f59e0b;
  color: white;
}

.product-badge.out-of-stock {
  background: #6b7280;
  color: white;
}

.product-content {
  padding: 1.5rem;
}

.product-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  line-height: 1.4;
}

.product-category {
  font-size: 0.875rem;
  color: var(--primary-color);
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.product-description {
  font-size: 0.875rem;
  color: var(--text-secondary);
  line-height: 1.5;
  margin-bottom: 1.5rem;
}

.product-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.product-price {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--text-primary);
}

.product-price .currency {
  font-size: 1rem;
  color: var(--text-secondary);
  margin-right: 2px;
}

.add-to-cart-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.add-to-cart-btn:hover {
  background: var(--primary-dark);
  transform: translateY(-1px);
}

/* No productos */
.no-products {
  text-align: center;
  padding: 3rem;
  background: #f9fafb;
  border-radius: 12px;
  grid-column: 1 / -1;
}

.no-products-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
}

.no-products h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.no-products p {
  color: var(--text-secondary);
  margin-bottom: 2rem;
}

.no-products-cta {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--primary-color);
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s ease;
}

.no-products-cta:hover {
  background: var(--primary-dark);
  text-decoration: none;
  color: white;
}

/* Responsivo */
@media (max-width: 768px) {
  .hero-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .hero-text h1 {
    font-size: 2rem;
  }
  
  .hero-images {
    height: 300px;
  }
  
  .categories-title,
  .products-title {
    font-size: 2rem;
  }
  
  .categories-grid {
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
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
      
      <div class="hero-images">
        <div class="hero-image-card">
          <img alt="Plantas de interior" src="https://images.unsplash.com/photo-1463320726281-696a485928c7?w=400&h=400&fit=crop&q=95"/>
          <div class="hero-image-overlay">
            <h3 class="hero-image-title">Plantas de Interior</h3>
            <a class="hero-image-button" href="<?php echo get_term_link('plantas-de-interior', 'product_cat'); ?>">Ver Colección <span class="material-icons">chevron_right</span></a>
          </div>
        </div>
        <div class="hero-image-card">
          <img alt="Herramientas de jardinería" src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=400&fit=crop&q=95"/>
          <div class="hero-image-overlay">
            <h3 class="hero-image-title">Herramientas</h3>
            <a class="hero-image-button" href="<?php echo get_term_link('hierros-soportes', 'product_cat'); ?>">Descubrir <span class="material-icons">chevron_right</span></a>
          </div>
        </div>
        <div class="hero-image-card">
          <img alt="Flores vibrantes" src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=800&h=400&fit=crop&q=95"/>
          <div class="hero-image-overlay">
            <h3 class="hero-image-title">Flores de Temporada</h3>
            <a class="hero-image-button" href="<?php echo wc_get_page_permalink('shop'); ?>">Comprar Ahora <span class="material-icons">shopping_bag</span></a>
          </div>
        </div>
      </div>
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
/**
 * The Template for displaying product archives
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 */

get_template_part('template-parts/shop-unified');
      $product_categories = get_terms(array(
          'taxonomy' => 'product_cat',
          'hide_empty' => true,
          'exclude' => array(15)
      ));

      $category_icons = array(
          'plantas-de-interior' => array('icon' => 'eco', 'color' => '#10b981'),
          'arboles' => array('icon' => 'park', 'color' => '#059669'),
          'arbustos' => array('icon' => 'local_florist', 'color' => '#ec4899'),
          'enredaderas' => array('icon' => 'nature', 'color' => '#65a30d'),
          'macetas-plasticas' => array('icon' => 'grass', 'color' => '#f59e0b'),
          'macetas-fibrocemento' => array('icon' => 'inventory_2', 'color' => '#78716c'),
          'sustratos-y-tierras' => array('icon' => 'agriculture', 'color' => '#f59e0b'),
          'fertilizantes' => array('icon' => 'science', 'color' => '#3b82f6'),
          'fitosanitarios' => array('icon' => 'health_and_safety', 'color' => '#ef4444'),
          'hierros-soportes' => array('icon' => 'construction', 'color' => '#6b7280'),
          'pies-nordicos' => array('icon' => 'chair', 'color' => '#6366f1'),
          'mensulas' => array('icon' => 'shelves', 'color' => '#8b5cf6'),
          'aros-ganchos' => array('icon' => 'radio_button_unchecked', 'color' => '#06b6d4'),
          'pies-bases' => array('icon' => 'table_restaurant', 'color' => '#f97316'),
          'porta-macetas' => array('icon' => 'view_module', 'color' => '#14b8a6')
      );

      echo '<a href="' . wc_get_page_permalink('shop') . '" class="category-card">';
      echo '<div class="category-icon" style="background-color: #dcfce7;">';
      echo '<span class="material-icons" style="font-size: 2rem; color: #10b981;">apps</span>';
      echo '</div>';
      echo '<p class="category-name">Todos</p>';
      echo '<p class="category-count">Ver todo</p>';
      echo '</a>';

      if ($product_categories && !is_wp_error($product_categories)) {
          foreach ($product_categories as $category) {
              $icon_data = isset($category_icons[$category->slug]) ? $category_icons[$category->slug] : array('icon' => 'category', 'color' => '#6b7280');
              $icon = $icon_data['icon'];
              $color = $icon_data['color'];
              
              $category_link = get_term_link($category);
              
              echo '<a href="' . esc_url($category_link) . '" class="category-card">';
              echo '<div class="category-icon" style="background-color: ' . $color . '20;">';
              echo '<span class="material-icons" style="font-size: 2rem; color: ' . $color . ';">' . $icon . '</span>';
              echo '</div>';
              echo '<p class="category-name">' . esc_html($category->name) . '</p>';
              echo '<p class="category-count">' . $category->count . ' productos</p>';
              echo '</a>';
          }
      }
      ?>
    </div>
  </section>

  <!-- Productos -->
  <section id="productos" class="products-section">
    <div class="products-header">
      <h2 class="products-title">
        <span class="material-icons">star_outline</span> 
        Nuestros Productos
      </h2>
    </div>
    
    <div class="products-grid">
      <?php
      // Consulta directa de productos - SIEMPRE FUNCIONA
      $args = array(
          'post_type' => 'product',
          'posts_per_page' => -1, // Mostrar TODOS los productos
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
                  // Obtener imagen del producto usando helper unificado con fallback SVG
                  if ( function_exists('loscocos_get_product_image_url') ) {
                      $image_url = loscocos_get_product_image_url($product->get_id(), 'woocommerce_thumbnail');
                  } else {
                      $image_id = $product->get_image_id();
                      $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : ( function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('woocommerce_thumbnail') : '' );
                  }
                  $categories = wp_get_post_terms($product->get_id(), 'product_cat');
                  $product_url = get_permalink($product->get_id());
                  
                  $badge = '';
                  $badge_class = '';
                  if ($product->is_on_sale()) {
                      $badge = 'OFERTA';
                      $badge_class = 'sale';
                  } elseif ($product->is_featured()) {
                      $badge = 'DESTACADO';
                      $badge_class = 'featured';
                  } elseif (!$product->is_in_stock()) {
                      $badge = 'AGOTADO';
                      $badge_class = 'out-of-stock';
                  }
                  
                  echo '<a href="' . esc_url($product_url) . '" class="product-card">';
                  echo '<div class="product-image">';
                  echo '<img alt="' . esc_attr($product->get_name()) . '" src="' . esc_url($image_url) . '"/>';
                  if ($badge) {
                      echo '<div class="product-badge ' . $badge_class . '">' . $badge . '</div>';
                  }
                  echo '</div>';
                  echo '<div class="product-content">';
                  echo '<h3 class="product-title">' . esc_html($product->get_name()) . '</h3>';
                  
                  if ($categories && !is_wp_error($categories)) {
                      echo '<p class="product-category">' . esc_html($categories[0]->name) . '</p>';
                  }
                  
                  $short_description = $product->get_short_description();
                  if ($short_description) {
                      echo '<p class="product-description">' . wp_trim_words($short_description, 12) . '</p>';
                  }
                  
                  echo '<div class="product-footer">';
                  echo '<span class="product-price">';
                  echo '<span class="currency">$</span>' . number_format($product->get_price(), 0, ',', '.');
                  echo '</span>';
                  echo '<button class="add-to-cart-btn" data-product-id="' . esc_attr( $product->get_id() ) . '">';
                  echo '<span class="material-icons">add_shopping_cart</span> Añadir';
                  echo '</button>';
                  echo '</div>';
                  echo '</div>';
                  echo '</a>';
              }
          }
          wp_reset_postdata();
      } else {
          echo '<div class="no-products">';
          echo '<div class="no-products-icon">🌱</div>';
          echo '<h3>No se encontraron productos</h3>';
          echo '<p>Intenta con una búsqueda diferente o explora nuestras categorías.</p>';
          echo '<a href="' . wc_get_page_permalink('shop') . '" class="no-products-cta">';
          echo '<span class="material-icons">apps</span> Ver Todos los Productos';
          echo '</a>';
          echo '</div>';
      }
      ?>
    </div>
  </section>
</main>

<!-- Cart logic handled by js/cart-woocommerce.js -->

<?php get_footer(); ?>
 