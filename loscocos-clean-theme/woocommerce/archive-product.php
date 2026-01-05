<?php
/**
 * Archivo de tienda seguro, usando hooks por defecto
 */
defined('ABSPATH') || exit;

get_header('shop');

echo '<div class="shop-archive-wrap">';
  echo '<div class="shop-main">';
    do_action('woocommerce_before_main_content');

  if (apply_filters('woocommerce_show_page_title', true)) {
    echo '<h1 class="page-title">' . esc_html(get_the_title(wc_get_page_id('shop'))) . '</h1>';
  }

  do_action('woocommerce_archive_description');

  if (woocommerce_product_loop()) {
    do_action('woocommerce_before_shop_loop');

    woocommerce_product_loop_start();

    if (wc_get_loop_prop('total')) {
      while (have_posts()) {
        the_post();
        do_action('woocommerce_shop_loop');
        wc_get_template_part('content', 'product');
      }
    }

    woocommerce_product_loop_end();

    do_action('woocommerce_after_shop_loop');
  } else {
    do_action('woocommerce_no_products_found');
  }

    do_action('woocommerce_after_main_content');
  echo '</div>';

  do_action('woocommerce_sidebar');

echo '</div>';

get_footer('shop');
?>
