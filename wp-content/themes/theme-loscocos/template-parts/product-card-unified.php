<?php
/**
 * Template Unificado para Tarjeta de Producto
 * Con estilos inline para garantizar consistencia
 * 
 * @package LosCocos
 * @version 2.0.0
 */

global $product;
if (!$product || !is_a($product, 'WC_Product')) {
    return;
}

$product_id = $product->get_id();
$product_name = $product->get_name();
$product_price = $product->get_price();
$product_url = loscocos_product_url($product_id);
$product_image = loscocos_get_product_image_url($product_id);

$categories = wp_get_post_terms($product_id, 'product_cat');
$category_name = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '';

$short_description = $product->get_short_description();
$trimmed_description = $short_description ? wp_trim_words($short_description, 12, '...') : '';
?>

<div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; flex-direction: column; height: 100%;">
    
    <!-- Imagen cuadrada -->
    <a href="<?php echo esc_url($product_url); ?>" style="display: block; position: relative; width: 100%; padding-top: 100%; background: #f5f5f5; overflow: hidden;">
        <img src="<?php echo esc_url($product_image); ?>" 
             alt="<?php echo esc_attr($product_name); ?>" 
             loading="lazy"
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
    </a>
    
    <!-- Contenido -->
    <div style="padding: 16px; display: flex; flex-direction: column; flex-grow: 1;">
        
        <!-- Título - altura fija 2 líneas -->
        <h3 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: #1f2937; line-height: 1.4; height: 42px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
            <a href="<?php echo esc_url($product_url); ?>" style="color: inherit; text-decoration: none;">
                <?php echo esc_html($product_name); ?>
            </a>
        </h3>
        
        <!-- Categoría - altura fija -->
        <div style="font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; height: 14px; overflow: hidden;">
            <?php echo esc_html($category_name); ?>
        </div>
        
        <!-- Descripción - altura fija 2 líneas -->
        <div style="font-size: 13px; color: #6b7280; line-height: 1.4; margin-bottom: 12px; height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
            <?php echo $trimmed_description ? esc_html($trimmed_description) : '&nbsp;'; ?>
        </div>
        
        <!-- Footer con precio y botón -->
        <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid #f0f0f0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 20px; font-weight: 700; color: #059669;">
                    $<?php echo number_format($product_price, 0, ',', '.'); ?>
                </span>
                <?php if ($product->is_in_stock()) : ?>
                    <span style="font-size: 12px; color: #10b981;">En stock</span>
                <?php else : ?>
                    <span style="font-size: 12px; color: #ef4444;">Agotado</span>
                <?php endif; ?>
            </div>
            
            <?php if ($product->is_in_stock()) : ?>
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
        <?php if ($product->get_sku()) : ?>
            <div style="margin-top: 8px; font-size: 11px; color: #9ca3af; text-align: center;">
                SKU: <?php echo esc_html($product->get_sku()); ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>