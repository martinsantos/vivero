<?php get_header(); ?>

<!-- Google Fonts y Material Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
/* Asegurar que Material Icons funcionen */
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

/* Reset de fuentes para toda la página */
body, * {
  font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
}

.product-single-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.product-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: start;
}

@media (max-width: 768px) {
  .product-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}

.product-image-container {
  position: relative;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
}

.product-image {
  width: 100%;
  height: 500px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-image:hover {
  transform: scale(1.05);
}

.product-info {
  padding: 1rem 0;
}

.product-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1F2937;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.product-price {
  font-size: 2rem;
  font-weight: 700;
  color: #10B981;
  margin-bottom: 1.5rem;
}

.product-description {
  color: #6B7280;
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.product-categories {
  display: flex;
  flex-wrap: gap;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.category-badge {
  background: linear-gradient(135deg, #10B981, #059669);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
}

.add-to-cart-btn {
  background: linear-gradient(135deg, #10B981, #059669);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 0.75rem;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.add-to-cart-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 2rem;
  font-size: 0.875rem;
  color: #6B7280;
}

.breadcrumb a {
  color: #10B981;
  text-decoration: none;
}

.breadcrumb a:hover {
  text-decoration: underline;
}

.stock-status {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.in-stock {
  background: #D1FAE5;
  color: #065F46;
}

.out-of-stock {
  background: #FEE2E2;
  color: #991B1B;
}
</style>

<main class="bg-gray-50 min-h-screen py-8">
  <div class="product-single-container">
    
    <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
        <?php 
        global $product;
        if (!$product) {
            $product = wc_get_product(get_the_ID());
        }
        
        $image_url = loscocos_get_product_image(get_the_ID());
        $categories = wp_get_post_terms(get_the_ID(), 'product_cat');
        ?>
        
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
          <a href="<?php echo home_url(); ?>">
            <span class="material-icons" style="font-size: 16px;">home</span>
            Inicio
          </a>
          <span class="material-icons" style="font-size: 16px;">chevron_right</span>
          <a href="<?php echo home_url('/tienda-moderna/'); ?>">
            <span class="material-icons" style="font-size: 16px;">store</span>
            Tienda
          </a>
          <?php if ($categories && !is_wp_error($categories)) : ?>
            <span class="material-icons" style="font-size: 16px;">chevron_right</span>
            <span><?php echo $categories[0]->name; ?></span>
          <?php endif; ?>
          <span class="material-icons" style="font-size: 16px;">chevron_right</span>
          <span><?php the_title(); ?></span>
        </nav>
        
        <!-- Producto Principal -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
          <div class="product-grid">
            
            <!-- Imagen del Producto -->
            <div class="product-image-container">
              <img src="<?php echo $image_url; ?>" 
                   alt="<?php the_title(); ?>" 
                   class="product-image">
            </div>
            
            <!-- Información del Producto -->
            <div class="product-info p-8">
              
              <!-- Estado del Stock -->
              <?php if ($product->is_in_stock()) : ?>
                <div class="stock-status in-stock">
                  <span class="material-icons" style="font-size: 16px;">check_circle</span>
                  En Stock
                </div>
              <?php else : ?>
                <div class="stock-status out-of-stock">
                  <span class="material-icons" style="font-size: 16px;">cancel</span>
                  Sin Stock
                </div>
              <?php endif; ?>
              
              <!-- Título -->
              <h1 class="product-title"><?php the_title(); ?></h1>
              
              <!-- Categorías -->
              <?php if ($categories && !is_wp_error($categories)) : ?>
                <div class="product-categories">
                  <?php foreach ($categories as $category) : ?>
                    <span class="category-badge"><?php echo $category->name; ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              
              <!-- Precio -->
              <div class="product-price">
                $<?php echo number_format($product->get_price(), 0, ',', '.'); ?>
              </div>
              
              <!-- Descripción -->
              <div class="product-description">
                <?php if ($product->get_description()) : ?>
                  <?php echo $product->get_description(); ?>
                <?php else : ?>
                  <p>Producto de alta calidad para tu jardín. Perfecto para crear un espacio verde único y especial.</p>
                <?php endif; ?>
              </div>
              
              <!-- Botón Agregar al Carrito -->
              <?php if ($product->is_in_stock()) : ?>
                <button class="add-to-cart-btn" onclick="addToCart(<?php echo get_the_ID(); ?>)">
                  <span class="material-icons">add_shopping_cart</span>
                  Agregar al Carrito
                </button>
              <?php else : ?>
                <button class="add-to-cart-btn" style="background: #9CA3AF; cursor: not-allowed;" disabled>
                  <span class="material-icons">remove_shopping_cart</span>
                  No Disponible
                </button>
              <?php endif; ?>
              
              <!-- Información Adicional -->
              <div class="mt-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                  <span class="material-icons">info</span>
                  Información del Producto
                </h3>
                <div class="space-y-2 text-sm text-gray-600">
                  <div class="flex justify-between">
                    <span>SKU:</span>
                    <span><?php echo $product->get_sku() ? $product->get_sku() : 'N/A'; ?></span>
                  </div>
                  <?php if ($product->get_weight()) : ?>
                    <div class="flex justify-between">
                      <span>Peso:</span>
                      <span><?php echo $product->get_weight(); ?> kg</span>
                    </div>
                  <?php endif; ?>
                  <?php if ($categories && !is_wp_error($categories)) : ?>
                    <div class="flex justify-between">
                      <span>Categoría:</span>
                      <span><?php echo $categories[0]->name; ?></span>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              
            </div>
          </div>
        </div>
        
      <?php endwhile; ?>
    <?php endif; ?>
    
  </div>
</main>

<!-- JavaScript -->
<script>
function addToCart(productId) {
    console.log('Agregando producto al carrito:', productId);
    
    // Mostrar notificación
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    notification.innerHTML = '✅ Producto agregado al carrito';
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<?php get_footer(); ?> 