<?php get_header(); ?>

<main class="min-h-screen bg-gray-50 py-8">
    <div class="container-clean">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold mb-6">Debug - Productos Individuales</h1>
            
            <?php
            // Obtener algunos productos para probar
            $products = get_posts(array(
                'post_type' => 'product',
                'posts_per_page' => 10,
                'post_status' => 'publish'
            ));
            
            if ($products) {
                echo '<h2 class="text-xl font-semibold mb-4">Enlaces a Productos (primeros 10):</h2>';
                echo '<ul class="space-y-2">';
                
                foreach ($products as $product_post) {
                    $product = wc_get_product($product_post->ID);
                    if ($product) {
                        $permalink = get_permalink($product_post->ID);
                        echo '<li class="border-b pb-2">';
                        echo '<strong>ID:</strong> ' . $product_post->ID . '<br>';
                        echo '<strong>Título:</strong> ' . $product->get_name() . '<br>';
                        echo '<strong>Permalink:</strong> <a href="' . $permalink . '" class="text-blue-600 hover:underline" target="_blank">' . $permalink . '</a><br>';
                        echo '<strong>Precio:</strong> $' . number_format($product->get_price(), 0, ',', '.') . '<br>';
                        echo '</li>';
                    }
                }
                
                echo '</ul>';
            } else {
                echo '<p>No se encontraron productos.</p>';
            }
            
            // Información de permalinks
            echo '<h2 class="text-xl font-semibold mb-4 mt-8">Configuración de Permalinks:</h2>';
            echo '<p><strong>Estructura actual:</strong> ' . get_option('permalink_structure') . '</p>';
            echo '<p><strong>Rewrite rules activas:</strong> ' . (get_option('rewrite_rules') ? 'Sí' : 'No') . '</p>';
            
            // Test de template
            echo '<h2 class="text-xl font-semibold mb-4 mt-8">Templates disponibles:</h2>';
            $theme_dir = get_template_directory();
            $files = array(
                'single-product.php',
                'single.php',
                'woocommerce.php',
                'index.php'
            );
            
            foreach ($files as $file) {
                $file_path = $theme_dir . '/' . $file;
                echo '<p><strong>' . $file . ':</strong> ' . (file_exists($file_path) ? 'Existe' : 'No existe') . '</p>';
            }
            ?>
            
            <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                <h3 class="font-semibold mb-2">Instrucciones de prueba:</h3>
                <ol class="list-decimal list-inside space-y-1 text-sm">
                    <li>Haz clic en cualquier permalink de arriba</li>
                    <li>Si no funciona, prueba agregando <code>?p=ID</code> al final de la URL</li>
                    <li>Si sigue sin funcionar, hay un problema con los permalinks</li>
                </ol>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?> 