<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <?php if ( get_theme_mod('loscocos_header_topbar_enabled', true) ) : ?>
    <div class="top-banner">
      <div class="container-clean">
        <?php echo wp_kses_post( get_theme_mod('loscocos_header_topbar_text', '🚚 Envío gratis el mismo día en Mendoza · Pedidos antes de las 14hs ⚡') ); ?>
      </div>
    </div>
  <?php endif; ?>
  <header class="site-header">
    <div class="container nav">
      <div class="brand font-display">
        <?php if ( function_exists('the_custom_logo') && has_custom_logo() ) : ?>
          <a href="<?php echo esc_url( home_url('/') ); ?>" class="custom-logo-link" rel="home"><?php the_custom_logo(); ?></a>
        <?php else : ?>
          <a href="<?php echo esc_url( home_url('/') ); ?>" class="site-title" style="text-decoration:none;color:inherit;">Los Cocos</a>
        <?php endif; ?>
      </div>
      <nav class="menu">
        <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'fallback_cb' => false]); ?>
      </nav>
      <div class="header-actions">
        <?php
          $cta_label = get_theme_mod('loscocos_header_cta_label');
          $cta_url   = get_theme_mod('loscocos_header_cta_url');
          if ( $cta_label && $cta_url ) : ?>
            <a class="btn-clean btn-primary" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?></a>
        <?php endif; ?>
        <div class="header-search">
          <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url('/') ); ?>">
            <input type="search" class="search-field" placeholder="Buscar plantas…" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
            <input type="hidden" name="post_type" value="product" />
          </form>
        </div>
      </div>
    </div>
  </header>
  <?php if ( is_front_page() && get_theme_mod('loscocos_hero_enabled', true) ) :
    $hero_title    = get_theme_mod('loscocos_hero_title', 'Vivero Los Cocos');
    $hero_subtitle = get_theme_mod('loscocos_hero_subtitle', 'Sitio estable para pruebas de tienda');
    $hero_bg       = get_theme_mod('loscocos_hero_bg_image');
    $hero_cta_label= get_theme_mod('loscocos_hero_cta_label');
    $hero_cta_url  = get_theme_mod('loscocos_hero_cta_url');
    $hero_classes  = 'hero' . ($hero_bg ? ' hero--image' : '');
    $style_attr    = $hero_bg ? ' style="background-image:url(' . esc_url($hero_bg) . ');"' : '';
  ?>
    <section class="<?php echo esc_attr($hero_classes); ?>"<?php echo $style_attr; ?>>
      <div class="container">
        <div class="hero-box">
          <h1 class="title font-display"><?php echo esc_html($hero_title); ?></h1>
          <?php if ($hero_subtitle) : ?><p class="subtitle"><?php echo esc_html($hero_subtitle); ?></p><?php endif; ?>
          <?php if ($hero_cta_label && $hero_cta_url) : ?>
            <div class="actions">
              <a class="btn-clean btn-primary" href="<?php echo esc_url($hero_cta_url); ?>"><?php echo esc_html($hero_cta_label); ?></a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
  <main class="container content">
