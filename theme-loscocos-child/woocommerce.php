<?php
/**
 * WooCommerce Template Router for Child Theme
 * Simplified version that guarantees header/footer display
 * 
 * @package LosCocos_Child
 * @version 1.0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

// If it's a single product, use our custom template from woocommerce/single-product.php
if (is_singular('product')) {
    // This already has get_header/get_footer in the template
    wc_get_template('single-product.php');
    return;
}

// For all other WooCommerce pages, guarantee header/footer
get_header(); 
?>

<main class="woocommerce-main bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">

        <?php if (is_shop() || is_product_category() || is_product_tag()): ?>
            <!-- TIENDA / CATEGORÍA -->
            <?php
            $shop_template = get_template_directory() . '/template-parts/shop-unified.php';
            if (file_exists($shop_template)) {
                include $shop_template;
            } else {
                // Fallback: usar WooCommerce estándar con estilos mínimos
                ?>
                <div class="shop-header text-center py-12 bg-green-700 text-white rounded-xl mb-8">
                    <h1 class="text-4xl font-bold mb-2"><?php woocommerce_page_title(); ?></h1>
                    <p class="text-lg opacity-90">Descubre nuestra selección de plantas y productos</p>
                </div>
                <?php woocommerce_content(); ?>
                <?php
            }
            ?>

        <?php elseif (is_cart()): ?>
            <!-- CARRITO -->
            <div class="bg-white rounded-xl shadow-sm p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">🛒 Carrito de Compras</h1>
                <?php woocommerce_content(); ?>
            </div>

        <?php elseif (is_checkout()): ?>
            <!-- CHECKOUT -->
            <div class="bg-white rounded-xl shadow-sm p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">✓ Finalizar Compra</h1>
                <?php woocommerce_content(); ?>
            </div>

        <?php elseif (is_account_page()): ?>
            <!-- MI CUENTA -->
            <div class="bg-white rounded-xl shadow-sm p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">👤 Mi Cuenta</h1>
                <?php woocommerce_content(); ?>
            </div>

        <?php else: ?>
            <!-- OTRAS PÁGINAS WOOCOMMERCE -->
            <?php woocommerce_content(); ?>

        <?php endif; ?>

    </div>
</main>

<?php 
get_footer();