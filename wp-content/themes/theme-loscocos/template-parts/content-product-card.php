<?php
/**
 * Template part for displaying product cards
 *
 * @package loscocos
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}

// Get product data
$product_id = $product->get_id();
$product_url = get_permalink($product_id);
$emoji = get_home_category_emoji($product_id);

// Determine product label
$product_label = '';
$label_class = '';
if ($product->is_on_sale()) {
    $product_label = 'OFERTA';
    $label_class = 'bg-red-600';
} elseif ($product->is_featured()) {
    $product_label = 'DESTACADO';
    $label_class = 'bg-yellow-600';
} elseif (!$product->is_in_stock()) {
    $product_label = 'AGOTADO';
    $label_class = 'bg-gray-600';
} else {
    // Asignar etiqueta de temporada basada en la categoría
    $categories = wp_get_post_terms($product_id, 'product_cat');
    if ($categories && !is_wp_error($categories)) {
        $seasonal_cats = ['plantas-de-interior', 'arboles', 'arbustos'];
        foreach ($categories as $category) {
            if (in_array($category->slug, $seasonal_cats)) {
                $product_label = 'TEMPORADA';
                $label_class = 'bg-green-600';
                break;
            }
        }
    }
}
?>

<a href="<?php echo esc_url($product_url); ?>" class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden h-full flex flex-col">
    <!-- Imagen del producto -->
    <div class="relative pt-[100%] bg-gray-100">
        <?php
        // Obtener la imagen del producto con fallback SVG si no hay thumbnail
        if ( function_exists('loscocos_get_product_image_url') ) {
            $image_src = loscocos_get_product_image_url($product_id, 'woocommerce_thumbnail');
        } else {
            // Fallback a la lógica nativa si el helper no existe
            $image_id = $product->get_image_id();
            $image_src = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src('woocommerce_thumbnail');
        }
        $image_alt = $product->get_name() ?: get_the_title();
        ?>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <img src="<?php echo esc_url($image_src); ?>" 
                 alt="<?php echo esc_attr($image_alt); ?>" 
                 class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105" 
                 loading="lazy"
                 width="400"
                 height="400">
        </div>
        
        <!-- Badge de categoría -->
        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-xl p-2 rounded-lg shadow-md">
            <?php echo $emoji; ?>
        </div>
        
        <!-- Etiqueta especial si existe -->
        <?php if ($product_label) : ?>
            <div class="absolute top-3 right-3 px-3 py-1 text-xs font-bold text-white rounded-md <?php echo esc_attr($label_class); ?> shadow-lg">
                <?php echo esc_html($product_label); ?>
            </div>
        <?php endif; ?>
        
        <!-- Overlay de hover -->
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
            <div class="text-center p-4 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                <?php if ($product->is_in_stock()) : ?>
                    <button class="btn-add-to-cart-featured bg-white text-green-700 hover:bg-green-600 hover:text-white px-4 py-2 rounded-full font-medium text-sm shadow-lg transform transition-all duration-300 hover:scale-105" 
                            data-product-id="<?php echo esc_attr($product_id); ?>">
                        🛒 Agregar al Carrito
                    </button>
                <?php else : ?>
                    <div class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-medium">Agotado</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Contenido de la tarjeta -->
    <div class="p-4 flex-grow flex flex-col">
        <!-- Título del producto -->
        <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2 h-12 flex items-center" title="<?php echo esc_attr($product->get_name()); ?>">
            <?php echo esc_html($product->get_name()); ?>
        </h3>
        
        <!-- Categoría -->
        <?php
        $categories = wp_get_post_terms($product_id, 'product_cat');
        if ($categories && !is_wp_error($categories)) : ?>
            <div class="text-sm text-gray-500 mb-2">
                <?php echo esc_html($categories[0]->name); ?>
            </div>
        <?php else : ?>
            <div class="h-5 mb-2"></div>
        <?php endif; ?>
        
        <!-- Descripción corta -->
        <?php
        $short_description = $product->get_short_description();
        if ($short_description) :
            $short_desc = wp_trim_words($short_description, 12, '...');
            ?>
            <div class="text-sm text-gray-600 mb-3 line-clamp-2 h-10">
                <?php echo esc_html($short_desc); ?>
            </div>
        <?php else : ?>
            <div class="h-10"></div>
        <?php endif; ?>
        
        <!-- Precio y stock -->
        <div class="mt-auto">
            <div class="flex items-center justify-between mb-2">
                <div class="space-y-1">
                    <?php if ($product->is_on_sale()) :
                        $discount = $product->get_regular_price() > 0 
                            ? round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100)
                            : 0;
                        
                        if ($discount > 0) : ?>
                            <div class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded-md inline-block">
                                -<?php echo $discount; ?>%
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex items-baseline gap-2">
                            <span class="text-xl font-bold text-gray-900">
                                <?php echo wc_price($product->get_sale_price()); ?>
                            </span>
                            <span class="text-sm text-gray-400 line-through">
                                <?php echo wc_price($product->get_regular_price()); ?>
                            </span>
                        </div>
                    <?php else : ?>
                        <span class="text-xl font-bold text-gray-900">
                            <?php echo wc_price($product->get_price()); ?>
                        </span>
                    <?php endif; ?>
                    
                    <div class="text-xs text-gray-500">Precio final</div>
                </div>
                
                <!-- Estado de stock -->
                <?php if ($product->is_in_stock()) :
                    $stock_qty = $product->get_stock_quantity();
                    if ($stock_qty && $stock_qty <= 5) : ?>
                        <div class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-md whitespace-nowrap">
                            ¡Últimas <?php echo $stock_qty; ?>!
                        </div>
                    <?php else : ?>
                        <div class="text-green-600 text-sm whitespace-nowrap">✅ En stock</div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="text-red-600 text-sm whitespace-nowrap">❌ Agotado</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</a>
