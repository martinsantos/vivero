<?php
/**
 * Front Page: reutiliza `home.php` como layout de portada
 * Evita duplicaciones y mantiene una sola fuente de verdad.
 */

// Carga directamente la plantilla home.php (que ya incluye header y footer)
$home_template = locate_template('home.php', true, true);
if (!$home_template) {
    // Fallback mínimo
    get_header();
    echo '<main class="container-clean" style="padding:2rem 0">';
    echo '<h1>Portada</h1><p>No se encontró <code>home.php</code>.</p>';
    echo '</main>';
    get_footer();
}
