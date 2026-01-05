<?php
/**
 * Template Unificado para Tarjeta de Producto
 * Usado en todas las vistas de productos para mantener consistencia
 * 
 * @package LosCocos
 * @version 1.0.0
 */

// Verificar que tenemos un producto válido
global $product;
if (!$product || !is_a($product, 'WC_Product')) {
    return;
}

// Obtener datos del producto
$product_id = $product->get_id();
$product_name = $product->get_name();
$product_price = $product->get_price();
$product_url = loscocos_product_url($product_id);
$product_image = loscocos_get_product_image_url($product_id);

// Obtener categorías
$categories = wp_get_post_terms($product_id, 'product_cat');
$category_name = '';
if ($categories && !is_wp_error($categories)) {
    $category_name = $categories[0]->name;
}

// Determinar estado del producto
$product_status = 'disponible';
$status_text = 'Disponible';
$status_class = 'success';

if (!$product->is_in_stock()) {
    $product_status = 'agotado';
    $status_text = 'Agotado';
    $status_class = 'error';
} elseif ($product->is_on_sale()) {
    $product_status = 'oferta';
    $status_text = 'Oferta';
    $status_class = 'warning';
} elseif ($product->is_featured()) {
    $product_status = 'destacado';
    $status_text = 'Destacado';
    $status_class = 'info';
}

// Obtener stock
$stock_quantity = $product->get_stock_quantity();
$low_stock = $stock_quantity && $stock_quantity <= 5;
?>

<div class="product-card-unified" data-product-id="<?php echo esc_attr($product_id); ?>">
    
    <!-- Imagen del producto -->
    <div class="product-image-unified">
        <a href="<?php echo esc_url($product_url); ?>">
            <img src="<?php echo esc_url($product_image); ?>" 
                 alt="<?php echo esc_attr($product_name); ?>" 
                 loading="lazy">
        </a>
        
        <!-- Etiquetas de estado -->
        <div class="absolute top-2 right-2 flex flex-col gap-1">
            <?php if ($product_status !== 'disponible') : ?>
                <span class="px-2 py-1 text-xs font-bold rounded-full text-white
                    <?php echo $product_status === 'oferta' ? 'bg-red-500' : 
                              ($product_status === 'destacado' ? 'bg-yellow-500' : 'bg-gray-500'); ?>">
                    <?php echo esc_html($status_text); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($low_stock && $product->is_in_stock()) : ?>
                <span class="px-2 py-1 text-xs font-bold rounded-full bg-orange-500 text-white">
                    ¡Últimas <?php echo $stock_quantity; ?>!
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Contenido del producto -->
    <div class="product-content-unified">
        
        <!-- Título del producto -->
        <h3 class="product-title-unified">
            <a href="<?php echo esc_url($product_url); ?>" class="text-inherit hover:text-primary-color transition-colors">
                <?php echo esc_html($product_name); ?>
            </a>
        </h3>
        
        <!-- Categoría -->
        <?php if ($category_name) : ?>
            <div class="product-category-unified">
                <?php echo esc_html($category_name); ?>
            </div>
        <?php endif; ?>
        
        <!-- Descripción corta -->
        <?php 
        $short_description = $product->get_short_description();
        if ($short_description) : 
            $trimmed_description = wp_trim_words($short_description, 15, '...');
        ?>
            <div class="text-sm text-gray-600 mb-4 line-clamp-2">
                <?php echo wp_kses_post($trimmed_description); ?>
            </div>
        <?php endif; ?>
        
        <!-- Footer del producto -->
        <div class="product-footer-unified">
            
            <!-- Precio -->
            <div class="product-price-unified">
                <?php if ($product->is_on_sale()) : ?>
                    <span class="text-sm text-gray-500 line-through mr-2">
                        $<?php echo number_format($product->get_regular_price(), 0, ',', '.'); ?>
                    </span>
                    <span class="text-lg font-bold text-red-600">
                        $<?php echo number_format($product->get_sale_price(), 0, ',', '.'); ?>
                    </span>
                    <div class="text-xs text-red-600 font-medium">
                        <?php 
                        $discount = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                        echo $discount . '% OFF';
                        ?>
                    </div>
                <?php else : ?>
                    <span class="text-xl font-bold text-primary-color">
                        $<?php echo number_format($product_price, 0, ',', '.'); ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- Botón añadir al carrito -->
            <?php if ($product->is_in_stock()) : ?>
                <button class="add-to-cart-unified" 
                        data-product-id="<?php echo esc_attr($product_id); ?>"
                        data-product-name="<?php echo esc_attr($product_name); ?>"
                        data-product-price="<?php echo esc_attr($product_price); ?>">
                    <span class="material-icons text-sm">add_shopping_cart</span>
                    <span>Añadir</span>
                </button>
            <?php else : ?>
                <button class="add-to-cart-unified" disabled>
                    <span class="material-icons text-sm">block</span>
                    <span>Agotado</span>
                </button>
            <?php endif; ?>
            
        </div>
        
        <!-- Información adicional -->
        <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
            
            <!-- Stock status -->
            <div class="flex items-center gap-1">
                <?php if ($product->is_in_stock()) : ?>
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <span>En stock</span>
                <?php else : ?>
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    <span>Sin stock</span>
                <?php endif; ?>
            </div>
            
            <!-- SKU si existe -->
            <?php if ($product->get_sku()) : ?>
                <div>
                    SKU: <?php echo esc_html($product->get_sku()); ?>
                </div>
            <?php endif; ?>
            
        </div>
        
    </div>
    
</div>

<!-- Cart logic is handled by js/cart-woocommerce.js -->