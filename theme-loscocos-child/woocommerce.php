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

// Shop archives use the child theme commerce-first catalog template.
if (is_shop() || is_product_category() || is_product_tag()) {
    include get_stylesheet_directory() . '/archive-product.php';
    return;
}

// For all other WooCommerce pages, guarantee header/footer
get_header(); 
?>

<main class="lc-commerce-page woocommerce-main bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-10 md:py-14">

        <?php if (is_cart()): ?>
            <!-- CARRITO -->
            <div class="lc-commerce-panel bg-white rounded-xl shadow-sm p-8">
                <p class="lc-page__eyebrow">Compra online</p>
                <h1 class="lc-page__title text-3xl font-bold text-gray-800 mb-8">Carrito de compras</h1>
                <?php woocommerce_content(); ?>
            </div>

        <?php elseif (is_checkout()): ?>
            <!-- CHECKOUT -->
            <div class="lc-commerce-panel bg-white rounded-xl shadow-sm p-8">
                <p class="lc-page__eyebrow">Compra segura</p>
                <h1 class="lc-page__title text-3xl font-bold text-gray-800 mb-8">Finalizar compra</h1>
                <?php woocommerce_content(); ?>
            </div>

        <?php elseif (is_account_page()): ?>
            <!-- MI CUENTA -->
            <div class="lc-commerce-panel bg-white rounded-xl shadow-sm p-8">
                <p class="lc-page__eyebrow">Vivero Los Cocos</p>
                <h1 class="lc-page__title text-3xl font-bold text-gray-800 mb-8">Mi cuenta</h1>
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
