<?php
/**
 * Plantilla unificada para páginas de WooCommerce.
 * Usa los hooks por defecto para máxima compatibilidad y cero roturas.
 */
get_header();
?>

<div class="woocommerce-wrap">
  <?php woocommerce_content(); ?>
</div>

<?php get_footer(); ?>
