<?php
/**
 * WooCommerce Shop Archive override
 * Reutiliza el template de `archive-product.php` ubicado en la raíz del tema
 * para que WooCommerce lo tome correctamente desde `woocommerce/archive-product.php`.
 */

// Aseguramos que el header/footer del tema se carguen correctamente
require get_template_directory() . '/archive-product.php';
?>
