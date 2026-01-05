<?php
/**
 * WooCommerce Template - Los Cocos E-commerce
 * Versión optimizada y unificada v2.0
 * 
 * @package LosCocos
 * @version 2.0.0
 */

get_header(); ?>

<style>
/* Variables CSS centralizadas */
:root {
  --primary-color: #10b981;
  --primary-dark: #059669;
  --secondary-color: #fbbf24;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --border-color: #e5e7eb;
  --bg-light: #f8fafc;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Reset y base */
* {
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  line-height: 1.6;
  color: var(--text-primary);
  background-color: var(--bg-light);
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Header de tienda */
.shop-header {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: white;
  padding: 3rem 0;
  margin-bottom: 3rem;
  text-align: center;
}

.shop-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.shop-header p {
  font-size: 1.2rem;
  opacity: 0.9;
}

/* Grid de productos */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

/* Tarjeta de producto unificada */
.product-card {
  background: white;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
  overflow: hidden;
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.product-image {
  position: relative;
  width: 100%;
  height: 280px;
  overflow: hidden;
  background: #f8f9fa;
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

.product-content {
  padding: 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.product-category {
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.product-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1rem;
  line-height: 1.4;
}

.product-title a {
  color: var(--text-primary);
  text-decoration: none;
}

.product-title a:hover {
  color: var(--primary-color);
}

.product-description {
  color: var(--text-secondary);
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
  flex-grow: 1;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
}

.add-to-cart-btn {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.add-to-cart-btn:hover {
  background: var(--primary-dark);
  transform: translateY(-1px);
  color: white;
  text-decoration: none;
}

.add-to-cart-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
  transform: none;
}

/* Página de producto individual */
.single-product-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  margin-bottom: 3rem;
}

.single-product-image {
  position: relative;
}

.single-product-image img {
  width: 100%;
  height: auto;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
}

.single-product-details h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.single-product-price {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-bottom: 1.5rem;
}

.single-product-description {
  color: var(--text-secondary);
  margin-bottom: 2rem;
  line-height: 1.8;
}

/* Carrito */
.cart-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

.cart-table th,
.cart-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

.cart-table th {
  background: var(--bg-light);
  font-weight: 600;
}

.cart-item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
}

/* Responsive */
@media (max-width: 768px) {
  .shop-header h1 {
    font-size: 2rem;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
  }
  
  .single-product-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .product-footer {
    flex-direction: column;
    gap: 1rem;
  }
}

/* Estados de carga */
.loading {
  opacity: 0.6;
  pointer-events: none;
}

/* Notificaciones */
.notice {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.notice.success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.notice.error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}
</style>

<div class="container">
  <?php
  // Determinar qué tipo de página WooCommerce estamos mostrando
  if (is_shop() || is_product_category() || is_product_tag()) {
    // Página de tienda/categoría
    ?>
    <div class="shop-header">
      <h1>
        <?php
        if (is_shop()) {
          echo 'Tienda Los Cocos';
        } elseif (is_product_category()) {
          single_cat_title();
        } elseif (is_product_tag()) {
          single_tag_title();
        }
        ?>
      </h1>
      <p>Descubre nuestra selección de plantas y accesorios para jardín</p>
    </div>

    <?php
    // Mostrar productos
    if (have_posts()) :
      echo '<div class="products-grid">';
      
      while (have_posts()) : the_post();
        global $product;
        
        // Obtener datos del producto
        $product_id = get_the_ID();
        $product_name = get_the_title();
        $product_price = $product->get_price_html();
        $product_image = loscocos_get_product_image($product_id);
        $product_categories = wc_get_product_category_list($product_id);
        $product_description = wp_trim_words(get_the_excerpt(), 20);
        $add_to_cart_url = $product->add_to_cart_url();
        ?>
        
        <div class="product-card">
          <div class="product-image">
            <a href="<?php the_permalink(); ?>">
              <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_name); ?>" loading="lazy">
            </a>
          </div>
          
          <div class="product-content">
            <?php if ($product_categories) : ?>
              <div class="product-category"><?php echo $product_categories; ?></div>
            <?php endif; ?>
            
            <h3 class="product-title">
              <a href="<?php the_permalink(); ?>"><?php echo esc_html($product_name); ?></a>
            </h3>
            
            <?php if ($product_description) : ?>
              <div class="product-description"><?php echo esc_html($product_description); ?></div>
            <?php endif; ?>
            
            <div class="product-footer">
              <div class="product-price"><?php echo $product_price; ?></div>
              
              <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
                <a href="<?php echo esc_url($add_to_cart_url); ?>" 
                   class="add-to-cart-btn" 
                   data-product-id="<?php echo esc_attr($product_id); ?>">
                  <span class="material-icons" style="font-size: 18px;">add_shopping_cart</span>
                  Añadir al carrito
                </a>
              <?php else : ?>
                <span class="add-to-cart-btn" style="background: #ccc; cursor: not-allowed;">
                  No disponible
                </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <?php
      endwhile;
      
      echo '</div>';
      
      // Paginación
      woocommerce_pagination();
      
    else :
      echo '<p>No se encontraron productos.</p>';
    endif;

  } elseif (is_product()) {
    // Página de producto individual
    while (have_posts()) : the_post();
      global $product;
      
      $product_id = get_the_ID();
      $product_name = get_the_title();
      $product_price = $product->get_price_html();
      $product_image = loscocos_get_product_image($product_id);
      $product_description = get_the_content();
      $product_short_description = get_the_excerpt();
      ?>
      
      <div class="single-product-content">
        <div class="single-product-image">
          <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_name); ?>">
        </div>
        
        <div class="single-product-details">
          <h1><?php echo esc_html($product_name); ?></h1>
          
          <div class="single-product-price"><?php echo $product_price; ?></div>
          
          <?php if ($product_short_description) : ?>
            <div class="single-product-description">
              <?php echo wpautop($product_short_description); ?>
            </div>
          <?php endif; ?>
          
          <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
            <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
              <?php
              do_action('woocommerce_before_add_to_cart_button');
              
              if ($product->is_type('simple')) {
                ?>
                <div style="margin-bottom: 1rem;">
                  <label for="quantity">Cantidad:</label>
                  <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product->get_stock_quantity(); ?>" style="width: 80px; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                </div>
                <?php
              }
              ?>
              
              <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product_id); ?>" class="add-to-cart-btn" style="font-size: 1.1rem; padding: 1rem 2rem;">
                <span class="material-icons">add_shopping_cart</span>
                Añadir al carrito
              </button>
              
              <?php do_action('woocommerce_after_add_to_cart_button'); ?>
            </form>
          <?php else : ?>
            <p style="color: #dc2626; font-weight: 600;">Este producto no está disponible actualmente.</p>
          <?php endif; ?>
          
          <?php if ($product_description) : ?>
            <div style="margin-top: 3rem;">
              <h3>Descripción</h3>
              <div style="margin-top: 1rem; line-height: 1.8;">
                <?php echo wpautop($product_description); ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
      
      <?php
    endwhile;

  } elseif (is_cart()) {
    // Página del carrito
    echo '<h1>Carrito de Compras</h1>';
    woocommerce_content();

  } elseif (is_checkout()) {
    // Página de checkout
    echo '<h1>Finalizar Compra</h1>';
    woocommerce_content();

  } else {
    // Otras páginas de WooCommerce
    woocommerce_content();
  }
  ?>
</div>

<!-- JavaScript para mejorar la experiencia del carrito -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Mejorar botones de añadir al carrito
  const addToCartButtons = document.querySelectorAll('.add-to-cart-btn[data-product-id]');
  
  addToCartButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      const productId = this.getAttribute('data-product-id');
      const originalText = this.innerHTML;
      
      // Mostrar estado de carga
      this.disabled = true;
      this.classList.add('loading');
      this.innerHTML = '<span class="material-icons">hourglass_empty</span> Añadiendo...';
      
      // Simular añadir al carrito (aquí deberías hacer la llamada AJAX real)
      setTimeout(() => {
        this.disabled = false;
        this.classList.remove('loading');
        this.innerHTML = '<span class="material-icons">check</span> ¡Añadido!';
        this.style.background = '#059669';
        
        // Mostrar notificación
        showNotification('Producto añadido al carrito correctamente', 'success');
        
        // Restaurar botón después de 2 segundos
        setTimeout(() => {
          this.innerHTML = originalText;
          this.style.background = '';
        }, 2000);
      }, 1000);
    });
  });
});

function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = `notice ${type}`;
  notification.textContent = message;
  notification.style.position = 'fixed';
  notification.style.top = '20px';
  notification.style.right = '20px';
  notification.style.zIndex = '9999';
  notification.style.maxWidth = '300px';
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.remove();
  }, 5000);
}
</script>

<?php get_footer(); ?>