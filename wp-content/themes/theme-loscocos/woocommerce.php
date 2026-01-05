<?php
/**
 * Template WooCommerce Simple - Los Cocos
 * Template simplificado para debugging
 */

get_header(); ?>

<div class="container mx-auto px-4 py-8">
    
    <?php if (is_shop() || is_product_category() || is_product_tag()) : ?>
        <!-- VISTA DE TIENDA/CATEGORÍA -->
        <?php include get_template_directory() . '/template-parts/shop-unified.php'; ?>
        
    <?php elseif (is_product()) : ?>
        <!-- VISTA DE PRODUCTO INDIVIDUAL -->
        <?php include get_template_directory() . '/template-parts/single-product-unified.php'; ?>
        
    <?php elseif (is_cart()) : ?>
        <!-- VISTA DE CARRITO -->
        <h1 class="text-4xl font-bold mb-8">Carrito de Compras</h1>
        <?php woocommerce_content(); ?>
        
    <?php elseif (is_checkout()) : ?>
        <!-- VISTA DE CHECKOUT -->
        <h1 class="text-4xl font-bold mb-8">Finalizar Compra</h1>
        <?php woocommerce_content(); ?>
        
    <?php elseif (is_account_page()) : ?>
        <!-- VISTA DE MI CUENTA -->
        <h1 class="text-4xl font-bold mb-8">Mi Cuenta</h1>
        <?php woocommerce_content(); ?>
        
    <?php else : ?>
        <!-- VISTA POR DEFECTO -->
        <div class="text-center py-16">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Tienda Los Cocos</h1>
            <p class="text-gray-600 mb-8">Bienvenido a nuestra tienda online</p>
            <a href="<?php echo loscocos_shop_url(); ?>" class="btn-unified btn-primary">
                Ver Productos
            </a>
        </div>
        
    <?php endif; ?>
    
</div>

<?php get_footer(); ?>