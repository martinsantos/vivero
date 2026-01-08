<?php
/**
 * Product Card Unified Template
 * Used by shop-unified.php for consistent product display
 * 
 * @package LosCocos
 * @version 1.0.0
 */

if (!isset($product) || !$product) return;

$image_url = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
$permalink = $product->get_permalink();
$title = $product->get_name();
$price = $product->get_price_html();
$in_stock = $product->is_in_stock();
$short_desc = $product->get_short_description();
$sku = $product->get_sku();
$categories = wc_get_product_category_list($product->get_id(), ', ');
?>

<div class="product-card bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col">
    <!-- Image -->
    <a href="<?php echo esc_url($permalink); ?>" class="block aspect-square overflow-hidden bg-gray-50">
        <?php if ($image_url): ?>
            <img src="<?php echo esc_url($image_url); ?>" 
                 alt="<?php echo esc_attr($title); ?>" 
                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                 loading="lazy">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        <?php endif; ?>
    </a>
    
    <!-- Content -->
    <div class="p-4 flex flex-col flex-grow">
        <!-- Title -->
        <a href="<?php echo esc_url($permalink); ?>" class="block mb-2">
            <h3 class="font-semibold text-gray-800 line-clamp-2 hover:text-green-600 transition-colors min-h-[2.5rem]">
                <?php echo esc_html($title); ?>
            </h3>
        </a>
        
        <!-- Category -->
        <?php if ($categories): ?>
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">
                <?php echo strip_tags($categories); ?>
            </p>
        <?php endif; ?>
        
        <!-- Short description -->
        <?php if ($short_desc): ?>
            <p class="text-sm text-gray-500 line-clamp-2 mb-3 h-10">
                <?php echo wp_trim_words(strip_tags($short_desc), 12); ?>
            </p>
        <?php endif; ?>
        
        <div class="mt-auto">
            <!-- Price and Stock Row -->
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xl font-bold text-green-600">
                    <?php echo $price; ?>
                </span>
                <span class="text-xs <?php echo $in_stock ? 'text-green-600' : 'text-red-500'; ?>">
                    <?php echo $in_stock ? 'En stock' : 'Sin stock'; ?>
                </span>
            </div>
            
            <!-- Add to Cart Button -->
            <?php if ($in_stock): ?>
                <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" 
                   data-quantity="1"
                   data-product_id="<?php echo $product->get_id(); ?>"
                   class="ajax_add_to_cart add_to_cart_button block w-full text-center bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Añadir al carrito
                    </span>
                </a>
            <?php else: ?>
                <span class="block w-full text-center bg-gray-300 text-gray-500 py-3 px-4 rounded-lg cursor-not-allowed font-medium">
                    Sin stock
                </span>
            <?php endif; ?>
            
            <!-- SKU -->
            <?php if ($sku): ?>
                <p class="text-xs text-gray-400 mt-2 text-center">SKU: <?php echo esc_html($sku); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.product-card:hover {
    transform: translateY(-4px);
}
</style>
