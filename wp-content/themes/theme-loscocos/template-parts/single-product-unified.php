<?php
/**
 * Template Unificado para Producto Individual
 * 
 * @package LosCocos
 * @version 1.0.0
 */

// Verificar que tenemos un producto válido
global $product;
if (!$product) {
    $product = wc_get_product(get_the_ID());
}

if (!$product) {
    echo '<div class="text-center py-16">';
    echo '<h1 class="text-4xl font-bold text-gray-800 mb-4">Producto no encontrado</h1>';
    echo '<p class="text-gray-600 mb-8">Lo sentimos, no pudimos encontrar el producto que buscas.</p>';
    echo '<a href="' . loscocos_shop_url() . '" class="btn-unified btn-primary">Volver a la Tienda</a>';
    echo '</div>';
    return;
}

// Obtener datos del producto
$product_id = $product->get_id();
$product_name = $product->get_name();
$product_price = $product->get_price();
$product_description = $product->get_description();
$product_short_description = $product->get_short_description();
$product_image = loscocos_get_product_image_url($product_id);

// Obtener categorías
$categories = wp_get_post_terms($product_id, 'product_cat');
$category_name = '';
if ($categories && !is_wp_error($categories)) {
    $category_name = $categories[0]->name;
}

// Breadcrumbs
$breadcrumbs = loscocos_breadcrumbs($product_id);
?>

<!-- Breadcrumbs -->
<nav class="mb-8">
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <?php
        foreach ($breadcrumbs as $index => $crumb) {
            if ($index > 0) {
                echo '<span>›</span>';
            }
            
            if ($crumb['url']) {
                echo '<a href="' . esc_url($crumb['url']) . '" class="hover:text-green-600 transition-colors">';
                echo esc_html($crumb['name']);
                echo '</a>';
            } else {
                echo '<span class="font-semibold text-gray-800">' . esc_html($crumb['name']) . '</span>';
            }
        }
        ?>
    </div>
</nav>

<!-- Producto principal -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="grid md:grid-cols-2 gap-8 p-8">
        
        <!-- Imagen del producto -->
        <div class="space-y-4">
            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                <img src="<?php echo esc_url($product_image); ?>" 
                     alt="<?php echo esc_attr($product_name); ?>" 
                     class="w-full h-full object-cover">
            </div>
            
            <!-- Galería de imágenes (si hay más imágenes) -->
            <?php
            $gallery_ids = $product->get_gallery_image_ids();
            if (!empty($gallery_ids)) {
                echo '<div class="grid grid-cols-4 gap-2">';
                foreach (array_slice($gallery_ids, 0, 4) as $image_id) {
                    $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                    echo '<div class="aspect-square bg-gray-100 rounded overflow-hidden cursor-pointer hover:opacity-75 transition-opacity">';
                    echo '<img src="' . esc_url($image_url) . '" alt="Imagen del producto" class="w-full h-full object-cover">';
                    echo '</div>';
                }
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Información del producto -->
        <div class="space-y-6">
            
            <!-- Título y categoría -->
            <div>
                <?php if ($category_name) : ?>
                    <div class="text-sm font-medium text-green-600 mb-2">
                        <?php echo esc_html($category_name); ?>
                    </div>
                <?php endif; ?>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-4">
                    <?php echo esc_html($product_name); ?>
                </h1>
                
                <?php if ($product_short_description) : ?>
                    <div class="text-gray-600 text-lg">
                        <?php echo wp_kses_post($product_short_description); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Precio -->
            <div class="border-t border-b border-gray-200 py-6">
                <div class="flex items-center gap-4">
                    <?php if ($product->is_on_sale()) : ?>
                        <span class="text-3xl font-bold text-red-600">
                            $<?php echo number_format($product->get_sale_price(), 0, ',', '.'); ?>
                        </span>
                        <span class="text-xl text-gray-500 line-through">
                            $<?php echo number_format($product->get_regular_price(), 0, ',', '.'); ?>
                        </span>
                        <span class="bg-red-100 text-red-800 text-sm font-medium px-2 py-1 rounded">
                            <?php 
                            $discount = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                            echo $discount . '% OFF';
                            ?>
                        </span>
                    <?php else : ?>
                        <span class="text-3xl font-bold text-green-600">
                            $<?php echo number_format($product_price, 0, ',', '.'); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Stock status -->
                <div class="mt-4 flex items-center gap-2">
                    <?php if ($product->is_in_stock()) : ?>
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-green-700 font-medium">En stock</span>
                        
                        <?php 
                        $stock_quantity = $product->get_stock_quantity();
                        if ($stock_quantity && $stock_quantity <= 5) : ?>
                            <span class="ml-2 text-orange-600 text-sm">
                                (¡Solo quedan <?php echo $stock_quantity; ?>!)
                            </span>
                        <?php endif; ?>
                        
                    <?php else : ?>
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="text-red-700 font-medium">Sin stock</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Formulario de compra -->
            <?php if ($product->is_in_stock()) : ?>
                <div class="space-y-4">
                    
                    <!-- Selector de cantidad -->
                    <div class="flex items-center gap-4">
                        <label class="font-medium text-gray-700">Cantidad:</label>
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden qty-control">
                            <button type="button" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors qty-btn" data-action="minus">-</button>
                            <input type="number" id="product-quantity" value="1" min="1" max="<?php echo $product->get_stock_quantity() ?: 999; ?>" class="w-16 text-center py-2 border-0 focus:ring-0 qty">
                            <button type="button" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors qty-btn" data-action="plus">+</button>
                        </div>
                    </div>
                    
                    <!-- Botón añadir al carrito -->
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition-colors flex items-center justify-center gap-3"
                            data-product-id="<?php echo esc_attr($product_id); ?>"
                            data-product-name="<?php echo esc_attr($product_name); ?>"
                            data-product-price="<?php echo esc_attr($product_price); ?>">
                        <span class="material-icons">add_shopping_cart</span>
                        <span>Añadir al Carrito</span>
                    </button>
                    
                    <!-- Botones adicionales -->
                    <div class="grid grid-cols-2 gap-4">
                        <button class="border border-gray-300 text-gray-700 font-medium py-3 px-6 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                            <span class="material-icons text-sm">favorite_border</span>
                            <span>Favoritos</span>
                        </button>
                        <button class="border border-gray-300 text-gray-700 font-medium py-3 px-6 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2"
                                onclick="shareProduct()">
                            <span class="material-icons text-sm">share</span>
                            <span>Compartir</span>
                        </button>
                    </div>
                    
                </div>
            <?php else : ?>
                <div class="bg-gray-100 rounded-lg p-6 text-center">
                    <span class="material-icons text-4xl text-gray-400 mb-2">inventory_2</span>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Producto agotado</h3>
                    <p class="text-gray-600 mb-4">Este producto no está disponible actualmente.</p>
                    <button class="bg-gray-600 text-white font-medium py-2 px-6 rounded-lg">
                        Notificarme cuando esté disponible
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Información adicional -->
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span class="material-icons text-green-600 mt-1">local_shipping</span>
                    <div>
                        <h4 class="font-semibold text-green-800">Envío gratis</h4>
                        <p class="text-green-700 text-sm">En compras superiores a $50.000 en Mendoza</p>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</div>

<!-- Descripción detallada -->
<?php if ($product_description) : ?>
    <div class="bg-white rounded-2xl shadow-lg p-8 mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Descripción del producto</h2>
        <div class="prose prose-lg max-w-none text-gray-700">
            <?php echo wp_kses_post($product_description); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Productos relacionados -->
<?php
$related_products = wc_get_products(array(
    'limit' => 4,
    'exclude' => array($product_id),
    'category' => $categories ? array($categories[0]->slug) : array(),
    'status' => 'publish'
));

if (!empty($related_products)) : ?>
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Productos relacionados</h2>
        <div class="products-grid-unified">
            <?php
            foreach ($related_products as $related_product) {
                $GLOBALS['product'] = $related_product;
                include get_template_directory() . '/template-parts/product-card-unified.php';
            }
            ?>
        </div>
    </div>
<?php endif; ?>

<script>
// Funciones no relacionadas con el carrito
function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo esc_js($product_name); ?>',
            text: 'Mira este producto de Vivero Los Cocos',
            url: window.location.href
        });
    } else {
        // Fallback: copiar URL al portapapeles
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('URL copiada al portapapeles');
        });
    }
}
</script>