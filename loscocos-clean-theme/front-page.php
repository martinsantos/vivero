<?php
/*
 * Front Page Template - Los Cocos Clean
 * - Hero desde header.php (Customizer)
 * - Secciones de productos con carrusel horizontal (CSS, sin JS)
 * - Usa plantillas nativas para mantener AJAX add-to-cart
 * - Popup estacional liviano (no intrusivo)
 */

get_header();
?>

<?php if ( class_exists('WooCommerce') ) : ?>
  
    <?php
    // Promos de portada (Customizer)
    if ( get_theme_mod('loscocos_promos_enabled', true) ) {
      $promos = [];
      for ($i = 1; $i <= 3; $i++) {
        $t   = get_theme_mod("loscocos_promo_{$i}_title", '');
        $st  = get_theme_mod("loscocos_promo_{$i}_subtitle", '');
        $img = get_theme_mod("loscocos_promo_{$i}_image", '');
        $cta = get_theme_mod("loscocos_promo_{$i}_cta_label", '');
        $url = get_theme_mod("loscocos_promo_{$i}_cta_url", '');
        if ($t || $st || $img) {
          $promos[] = [
            'title'    => $t,
            'subtitle' => $st,
            'image'    => $img,
            'cta'      => $cta,
            'url'      => $url,
          ];
        }
      }
      if (!empty($promos)) : ?>
        <section class="home-promos" aria-label="Promociones destacadas">
          <div class="home-promos__grid">
            <?php foreach ($promos as $p) :
              $has_img = !empty($p['image']);
              $style   = $has_img ? ' style="background-image:url(' . esc_url($p['image']) . ');"' : '';
              $card_cls = 'promo-card' . ($has_img ? '' : ' promo-card--noimg');
            ?>
              <article class="<?php echo esc_attr($card_cls); ?>"<?php echo $style; ?>>
                <div class="promo-card__content">
                  <?php if (!empty($p['title'])) : ?>
                    <h3 class="title font-display"><?php echo esc_html($p['title']); ?></h3>
                  <?php endif; ?>
                  <?php if (!empty($p['subtitle'])) : ?>
                    <p class="subtitle"><?php echo esc_html($p['subtitle']); ?></p>
                  <?php endif; ?>
                  <?php if (!empty($p['cta']) && !empty($p['url'])) : ?>
                    <a class="btn-clean btn-secondary" href="<?php echo esc_url($p['url']); ?>"><?php echo esc_html($p['cta']); ?></a>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; }
    ?>
    <?php
    // Helper local para evitar duplicación
    if ( ! function_exists('loscocos_render_products_section') ) {
      function loscocos_render_products_section( $title, $query_args, $more_link = '' ) {
        $loop = new WP_Query( $query_args );
        if ( ! $loop->have_posts() ) return;

        echo '<section class="home-section">';
        echo '<div class="home-section__header">'
            . '<h2 class="section-title">' . esc_html( $title ) . '</h2>';
        if ( $more_link ) {
          echo '<a class="btn-clean btn-secondary" href="' . esc_url( $more_link ) . '">' . esc_html__( 'Ver todo', 'loscocos-clean' ) . '</a>';
        }
        echo '</div>';

        // Lista simple de productos (grid via CSS)
        if ( function_exists('woocommerce_product_loop_start') ) {
          woocommerce_product_loop_start();
        } else {
          echo '<ul class="products">';
        }

        while ( $loop->have_posts() ) : $loop->the_post();
          wc_get_template_part( 'content', 'product' );
        endwhile;

        if ( function_exists('woocommerce_product_loop_end') ) {
          woocommerce_product_loop_end();
        } else {
          echo '</ul>';
        }

        echo '</section>';
        wp_reset_postdata();
      }
    }

    $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink( 'shop' ) : home_url('/');

    // 1) Promociones (en oferta)
    $on_sale_ids = function_exists('wc_get_product_ids_on_sale') ? wc_get_product_ids_on_sale() : [];
    if ( ! empty( $on_sale_ids ) ) {
      loscocos_render_products_section(
        __( 'Promociones', 'loscocos-clean' ),
        [
          'post_type'      => 'product',
          'post__in'       => $on_sale_ids,
          'posts_per_page' => 12,
          'post_status'    => 'publish',
          'orderby'        => 'date',
          'order'          => 'DESC',
        ],
        $shop_url
      );
    }

    // 2) Novedades (más recientes)
    loscocos_render_products_section(
      __( 'Novedades', 'loscocos-clean' ),
      [
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => function_exists('WC') ? WC()->query->get_meta_query() : [],
        'tax_query'      => function_exists('WC') ? WC()->query->get_tax_query() : [],
      ],
      $shop_url
    );

    // 3) Más vendidos
    loscocos_render_products_section(
      __( 'Más vendidos', 'loscocos-clean' ),
      [
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
        'meta_key'       => 'total_sales',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'meta_query'     => function_exists('WC') ? WC()->query->get_meta_query() : [],
        'tax_query'      => function_exists('WC') ? WC()->query->get_tax_query() : [],
      ],
      $shop_url
    );
    ?>
  
<?php else : ?>
  <?php // Fallback: contenido estándar si WooCommerce no está activo ?>
  <div class="container">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div><?php the_content(); ?></div>
      </article>
    <?php endwhile; endif; ?>
  </div>
<?php endif; ?>

<!-- Popup estacional -->
<div id="seasonal-modal" class="modal" aria-hidden="true" role="dialog" aria-labelledby="seasonal-title">
  <div class="modal-backdrop" data-modal-close></div>
  <div class="modal-box" role="document">
    <button class="modal-close" type="button" aria-label="Cerrar" data-modal-close>&times;</button>
    <h3 id="seasonal-title" style="margin-top:0;">Promo de temporada</h3>
    <p>Aprovechá envíos con descuento esta semana.</p>
  </div>
</div>

<script>
(function(){
  // Popup no intrusivo: se muestra 1 vez por mes
  try {
    var now = new Date();
    var key = 'lc_seasonal_popup_' + now.getFullYear() + '_' + (now.getMonth()+1);
    if (localStorage.getItem(key)) return;
    var modal = document.getElementById('seasonal-modal');
    if (!modal) return;
    function open(){ modal.classList.add('is-open'); modal.setAttribute('aria-hidden','false'); }
    function close(){ modal.classList.remove('is-open'); modal.setAttribute('aria-hidden','true'); localStorage.setItem(key, '1'); }
    modal.addEventListener('click', function(e){ if(e.target && e.target.hasAttribute('data-modal-close')) close(); });
    setTimeout(open, 400);
  } catch(e){}
})();
</script>

<?php get_footer(); ?>
