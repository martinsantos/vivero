<?php
/**
 * Template WooCommerce Unificado - Los Cocos E-commerce
 * 
 * Este template maneja TODAS las vistas de WooCommerce de forma unificada
 * Elimina duplicaciones y mantiene consistencia visual
 * 
 * @package LosCocos
 * @version 2.0.0
 */

get_header(); ?>

<div class="container-unified py-8">
    
    <?php
    // Determinar qué vista mostrar
    if (is_shop() || is_product_category() || is_product_tag()) {
        // VISTA DE TIENDA/CATEGORÍA
        include get_template_directory() . '/template-parts/shop-unified.php';
        
    } elseif (is_product()) {
        // VISTA DE PRODUCTO INDIVIDUAL
        include get_template_directory() . '/template-parts/single-product-unified.php';
        
    } elseif (is_cart()) {
        // VISTA DE CARRITO
        include get_template_directory() . '/template-parts/cart-unified.php';
        
    } elseif (is_checkout()) {
        // VISTA DE CHECKOUT
        include get_template_directory() . '/template-parts/checkout-unified.php';
        
    } elseif (is_account_page()) {
        // VISTA DE MI CUENTA
        include get_template_directory() . '/template-parts/account-unified.php';
        
    } else {
        // VISTA POR DEFECTO
        ?>
        <div class="text-center py-16">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Tienda Los Cocos</h1>
            <p class="text-gray-600 mb-8">Bienvenido a nuestra tienda online</p>
            <a href="<?php echo loscocos_shop_url(); ?>" class="btn-unified btn-primary">
                Ver Productos
            </a>
        </div>
        <?php
    }
    ?>
    
</div>

<?php get_footer(); ?>