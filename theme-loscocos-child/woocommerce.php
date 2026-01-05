<?php
/**
 * WooCommerce Template Router for Child Theme
 * Overrides parent theme's woocommerce.php to force usage of child theme templates for single products.
 */

if (!defined('ABSPATH')) {
    exit;
}

// If it's a single product, load our custom template and exit
if (is_singular('product')) {
    wc_get_template('single-product.php');
    return;
}

// For everything else, replicate parent theme logic or fallback
get_header(); ?>

<div class="container mx-auto px-4 py-8">

    <?php if (is_shop() || is_product_category() || is_product_tag()): ?>
        <!-- VISTA DE TIENDA/CATEGORÍA (PARENT FALLBACK) -->
        <?php
        if (file_exists(get_template_directory() . '/template-parts/shop-unified.php')) {
            include get_template_directory() . '/template-parts/shop-unified.php';
        } else {
            woocommerce_content();
        }
        ?>

    <?php elseif (is_cart()): ?>
        <!-- VISTA DE CARRITO -->
        <h1 class="text-4xl font-bold mb-8">Carrito de Compras</h1>
        <?php woocommerce_content(); ?>

    <?php elseif (is_checkout()): ?>
        <!-- VISTA DE CHECKOUT -->
        <h1 class="text-4xl font-bold mb-8">Finalizar Compra</h1>
        <?php woocommerce_content(); ?>

    <?php elseif (is_account_page()): ?>
        <!-- VISTA DE MI CUENTA -->
        <h1 class="text-4xl font-bold mb-8">Mi Cuenta</h1>
        <?php woocommerce_content(); ?>

    <?php else: ?>
        <!-- VISTA POR DEFECTO -->
        <?php woocommerce_content(); ?>

    <?php endif; ?>

</div>

<?php get_footer(); ?>