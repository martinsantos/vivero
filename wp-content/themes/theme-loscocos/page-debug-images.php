<?php
/**
 * Template Name: Diagnóstico de Imágenes
 */

get_header();
?>

<div class="container-clean" style="padding: 2rem;">
    <h1>Diagnóstico de Imágenes de Productos</h1>
    
    <div class="products-grid" style="margin-top: 2rem;">
        <?php
        $test_products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => 8,
            'orderby' => 'rand'
        ));
        
        foreach ($test_products as $product) {
            $product_obj = wc_get_product($product->ID);
            if (!$product_obj) continue;
            
            // Obtener URL de imagen con helper y fallback nativo
            if ( function_exists('loscocos_get_product_image_url') ) {
                $image_url = loscocos_get_product_image_url($product_obj->get_id(), 'woocommerce_thumbnail');
            } else {
                $image_url = get_the_post_thumbnail_url($product_obj->get_id(), 'woocommerce_thumbnail');
                if ( ! $image_url && function_exists('wc_placeholder_img_src') ) {
                    $image_url = wc_placeholder_img_src('woocommerce_thumbnail');
                }
            }
            ?>
            
            <div class="product-card-standard">
                <div class="product-image">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product_obj->get_name()); ?>" />
                </div>
                <div class="product-content">
                    <h3 class="product-title"><?php echo esc_html($product_obj->get_name()); ?></h3>
                    <div class="debug-info" style="font-size: 0.8rem; color: #666;">
                        <p>ID: <?php echo $product_obj->get_id(); ?></p>
                        <p>Imagen ID: <?php echo $product_obj->get_image_id(); ?></p>
                        <p style="word-break: break-all;">URL: <?php echo esc_url($image_url); ?></p>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    
    <div style="margin-top: 2rem; padding: 1rem; background: #f5f5f5; border-radius: 0.5rem;">
        <h2>Información del Sistema</h2>
        <ul>
            <li>Tema activo: <?php echo wp_get_theme()->get('Name'); ?></li>
            <li>Versión de WooCommerce: <?php echo WC()->version; ?></li>
            <li>Tamaño de imagen thumbnail: <?php 
                $size = wc_get_image_size('woocommerce_thumbnail');
                echo $size['width'] . 'x' . $size['height'] . 'px';
            ?></li>
        </ul>
    </div>
</div>

<?php get_footer(); ?> 