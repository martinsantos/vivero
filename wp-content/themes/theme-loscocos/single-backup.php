<!DOCTYPE html>
<html>
<head>
    <title>SINGLE PAGE DEBUG</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f0f0; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .debug { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #ffebee; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 SINGLE PAGE DEBUG - FUNCIONANDO</h1>
        
        <div class="success">
            <h2>✅ Template Cargado Correctamente</h2>
            <p><strong>Archivo:</strong> single.php</p>
            <p><strong>Fecha:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
        
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                
                <div class="debug">
                    <h3>📋 Información del Post</h3>
                    <p><strong>ID:</strong> <?php echo get_the_ID(); ?></p>
                    <p><strong>Título:</strong> <?php the_title(); ?></p>
                    <p><strong>Tipo:</strong> <?php echo get_post_type(); ?></p>
                    <p><strong>Estado:</strong> <?php echo get_post_status(); ?></p>
                    <p><strong>URL:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></p>
                </div>
                
                <?php if (get_post_type() == 'product') : ?>
                    <div class="success">
                        <h3>🛍️ Es un Producto WooCommerce</h3>
                        <?php 
                        global $product;
                        if (!$product) {
                            $product = wc_get_product(get_the_ID());
                        }
                        
                        if ($product) {
                            echo '<p><strong>Nombre:</strong> ' . $product->get_name() . '</p>';
                            echo '<p><strong>Precio:</strong> $' . number_format($product->get_price(), 0, ',', '.') . '</p>';
                            echo '<p><strong>SKU:</strong> ' . ($product->get_sku() ?: 'N/A') . '</p>';
                            echo '<p><strong>Stock:</strong> ' . ($product->is_in_stock() ? 'Disponible' : 'Sin stock') . '</p>';
                            
                            if ($product->get_description()) {
                                echo '<p><strong>Descripción:</strong> ' . wp_trim_words($product->get_description(), 20) . '</p>';
                            }
                        } else {
                            echo '<p class="error">❌ Error: No se pudo cargar el objeto producto</p>';
                        }
                        ?>
                    </div>
                <?php else : ?>
                    <div class="debug">
                        <h3>📄 Post Normal (No es producto)</h3>
                        <p><strong>Contenido:</strong></p>
                        <div style="background: #f5f5f5; padding: 10px; border-radius: 3px;">
                            <?php the_content(); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php endwhile; ?>
        <?php else : ?>
            <div class="error">
                <h3>❌ No se encontró contenido</h3>
                <p>El loop de WordPress no devolvió ningún post.</p>
            </div>
        <?php endif; ?>
        
        <div class="debug">
            <h3>🔗 Enlaces de Prueba</h3>
            <p><a href="<?php echo home_url(); ?>">🏠 Inicio</a></p>
            <p><a href="<?php echo home_url('/?post_type=product'); ?>">🛍️ Tienda</a></p>
            <p><a href="<?php echo home_url('/?p=545'); ?>">🧪 Producto ID 545</a></p>
            <p><a href="<?php echo home_url('/?p=546'); ?>">🧪 Producto ID 546</a></p>
        </div>
    </div>
</body>
</html> 