<?php
/**
 * Debug de la página de tienda
 */

get_header(); ?>

<div class="container mx-auto px-4 py-8">
    <h1>DEBUG - Página de Tienda</h1>
    
    <div class="bg-gray-100 p-4 rounded mb-4">
        <h2>Información de la página:</h2>
        <ul>
            <li><strong>is_shop():</strong> <?php echo is_shop() ? 'true' : 'false'; ?></li>
            <li><strong>is_product_category():</strong> <?php echo is_product_category() ? 'true' : 'false'; ?></li>
            <li><strong>is_woocommerce():</strong> <?php echo is_woocommerce() ? 'true' : 'false'; ?></li>
            <li><strong>Template actual:</strong> <?php echo get_page_template_slug(); ?></li>
            <li><strong>Post type:</strong> <?php echo get_post_type(); ?></li>
            <li><strong>Query vars:</strong> <?php print_r(get_query_var('post_type')); ?></li>
        </ul>
    </div>
    
    <div class="bg-blue-100 p-4 rounded mb-4">
        <h2>Productos disponibles:</h2>
        <?php
        $products = wc_get_products(array('limit' => 5));
        if ($products) {
            echo '<ul>';
            foreach ($products as $product) {
                echo '<li>' . $product->get_name() . ' - $' . $product->get_price() . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No hay productos disponibles</p>';
        }
        ?>
    </div>
    
    <div class="bg-green-100 p-4 rounded">
        <h2>Contenido WooCommerce:</h2>
        <?php woocommerce_content(); ?>
    </div>
</div>

<?php get_footer(); ?>