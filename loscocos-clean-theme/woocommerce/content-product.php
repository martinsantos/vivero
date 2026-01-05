<?php
/**
 * Card de producto segura: usa hooks de WooCommerce.
 */
defined('ABSPATH') || exit;

global $product;
if (empty($product) || !$product->is_visible()) return;

// Asegurar un solo enlace alrededor de la imagen (no envolver título/precio)
// Remueve el link por defecto de Woo (abre/cierra alrededor de todo el contenido)
remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);

echo '<li '; wc_product_class('', $product); echo ' data-permalink="' . esc_url( get_the_permalink() ) . '">';
  
  // Contenedor clickeable para imagen (navegación al producto)
  echo '<div class="product-image-container">';
    echo '<a class="product-image-link" href="' . esc_url( get_the_permalink() ) . '" aria-label="Ver ' . esc_attr( get_the_title() ) . '">';
      do_action('woocommerce_before_shop_loop_item_title');
    echo '</a>';
  echo '</div>';
  
  // Contenedor para texto clickeable (navegación al producto)
  echo '<div class="product-content">';
    echo '<a class="product-title-link" href="' . esc_url( get_the_permalink() ) . '">';
      do_action('woocommerce_shop_loop_item_title');
    echo '</a>';
    do_action('woocommerce_after_shop_loop_item_title');
  echo '</div>';
  
  // Contenedor para botones (sin overlay, completamente libre)
  echo '<div class="product-actions">';
    do_action('woocommerce_after_shop_loop_item');
  echo '</div>';
echo '</li>';
