<?php
/**
 * Unified Product Card Template
 * Single source of truth for all product card displays
 * 
 * @package LosCocos_Child
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;
if (!$product || !is_a($product, 'WC_Product')) {
    return;
}

$product_id     = $product->get_id();
$product_name   = $product->get_name();
$product_price  = $product->get_price();
$product_url    = get_permalink($product_id);
$product_image  = loscocos_get_product_image($product_id);
$category_name  = loscocos_get_product_categories($product_id);
$short_desc     = $product->get_short_description();
$trimmed_desc   = $short_desc ? loscocos_truncate_text($short_desc, 12) : '';
$is_in_stock    = $product->is_in_stock();
$sku            = $product->get_sku();
?>

<div class="product-card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; flex-direction: column; height: 100%; transition: transform 0.3s ease, box-shadow 0.3s ease;">
    
    <!-- Imagen cuadrada -->
    <a href="<?php echo esc_url($product_url); ?>" 
       style="display: block; position: relative; width: 100%; padding-top: 100%; background: #f5f5f5; overflow: hidden;">
        <img src="<?php echo esc_url($product_image); ?>" 
             alt="<?php echo esc_attr($product_name); ?>" 
             loading="lazy"
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
        
        <?php if ($product->is_on_sale()) : ?>
            <span style="position: absolute; top: 8px; right: 8px; background: #ef4444; color: white; padding: 4px 8px; font-size: 11px; font-weight: 600; border-radius: 4px;">
                OFERTA
            </span>
        <?php endif; ?>
    </a>
    
    <!-- Contenido -->
    <div style="padding: 16px; display: flex; flex-direction: column; flex-grow: 1;">
        
        <!-- Título -->
        <h3 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: #1f2937; line-height: 1.4; height: 42px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
            <a href="<?php echo esc_url($product_url); ?>" style="color: inherit; text-decoration: none;">
                <?php echo esc_html($product_name); ?>
            </a>
        </h3>
        
        <!-- Categoría -->
        <div style="font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; height: 14px; overflow: hidden;">
            <?php echo esc_html($category_name); ?>
        </div>
        
        <!-- Descripción -->
        <div style="font-size: 13px; color: #6b7280; line-height: 1.4; margin-bottom: 12px; height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
            <?php echo $trimmed_desc ? esc_html($trimmed_desc) : '&nbsp;'; ?>
        </div>
        
        <!-- Footer -->
        <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid #f0f0f0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 20px; font-weight: 700; color: #059669;">
                    <?php echo loscocos_format_price($product_price); ?>
                </span>
                <?php if ($is_in_stock) : ?>
                    <span style="font-size: 12px; color: #10b981;">En stock</span>
                <?php else : ?>
                    <span style="font-size: 12px; color: #ef4444;">Agotado</span>
                <?php endif; ?>
            </div>
            
            <?php if ($is_in_stock) : ?>
                <button class="add-to-cart-unified" 
                        data-product-id="<?php echo esc_attr($product_id); ?>"
                        style="width: 100%; padding: 10px 16px; background: #059669; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    🛒 Añadir al carrito
                </button>
            <?php else : ?>
                <button disabled style="width: 100%; padding: 10px 16px; background: #d1d5db; color: #6b7280; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: not-allowed;">
                    Agotado
                </button>
            <?php endif; ?>
        </div>
        
        <!-- SKU -->
        <?php if ($sku) : ?>
            <div style="margin-top: 8px; font-size: 11px; color: #9ca3af; text-align: center;">
                SKU: <?php echo esc_html($sku); ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>
