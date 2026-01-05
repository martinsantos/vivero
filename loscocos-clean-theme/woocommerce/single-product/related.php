<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( $related_products ) : ?>

  <section class="related products">
    <h2><?php esc_html_e( 'Productos relacionados', 'woocommerce' ); ?></h2>

    <?php woocommerce_product_loop_start(); ?>
      <?php foreach ( $related_products as $related_product ) :
          $post_object = get_post( $related_product->get_id() );
          setup_postdata( $GLOBALS['post'] =& $post_object );
          wc_get_template_part( 'content', 'product' );
      endforeach; ?>
    <?php woocommerce_product_loop_end(); ?>

  </section>

  <?php wp_reset_postdata(); ?>

<?php endif; ?>
