<?php
// Activar soporte básico
add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('woocommerce');
  
  // ESSENTIAL: Enable WooCommerce features
  add_theme_support('wc-product-gallery-zoom');
  add_theme_support('wc-product-gallery-lightbox');
  add_theme_support('wc-product-gallery-slider');
  
  // Logo personalizable desde el personalizador
  add_theme_support('custom-logo', [
    'height'      => 64,
    'width'       => 200,
    'flex-height' => true,
    'flex-width'  => true,
    'unlink-homepage-logo' => true,
  ]);

  // i18n: cargar textdomain del tema en el momento correcto
  load_theme_textdomain('loscocos-clean', get_template_directory() . '/languages');

  // Menú: registrar después de cargar el textdomain
  register_nav_menus(['primary' => __('Menú principal', 'loscocos-clean')]);
});

// Enqueue CSS base y Google Fonts ligeras
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('loscocos-clean', get_stylesheet_uri(), [], '1.0.0');
  $shopCss = get_stylesheet_directory() . '/assets/css/shop.css';
  if (file_exists($shopCss)) {
    wp_enqueue_style('loscocos-clean-shop', get_stylesheet_directory_uri() . '/assets/css/shop.css', ['loscocos-clean'], filemtime($shopCss));
  }
  
  // Enqueue product enhancements and cart AJAX fix scripts
  wp_enqueue_script('loscocos-product-enhancements', get_template_directory_uri() . '/assets/js/product-enhancements.js', array('jquery'), '1.0.0', true);
  wp_enqueue_script('loscocos-cart-ajax-fix', get_template_directory_uri() . '/assets/js/cart-ajax-fix.js', array('jquery', 'wc-add-to-cart'), '1.0.0', true);
  
  // Cart/Checkout CSS
  $cartCss = get_stylesheet_directory() . "/assets/css/cart-checkout.css";
  if (file_exists($cartCss)) {
    wp_enqueue_style("loscocos-cart-checkout", get_stylesheet_directory_uri() . "/assets/css/cart-checkout.css", ["loscocos-clean"], filemtime($cartCss));
  }
  
  // Checkout Progress Bar CSS
  if (is_checkout()) {
    $checkoutProgressCss = get_stylesheet_directory() . "/assets/css/checkout-progress.css";
    if (file_exists($checkoutProgressCss)) {
      wp_enqueue_style("loscocos-checkout-progress", get_stylesheet_directory_uri() . "/assets/css/checkout-progress.css", ["loscocos-clean"], filemtime($checkoutProgressCss));
    }
  }
  
  // Tienda - Sidebar Filtros CSS
  if (is_shop() || is_product_category() || is_product_tag()) {
    $tiendaCss = get_stylesheet_directory() . "/assets/css/tienda-sidebar.css";
    if (file_exists($tiendaCss)) {
      wp_enqueue_style("loscocos-tienda-sidebar", get_stylesheet_directory_uri() . "/assets/css/tienda-sidebar.css", ["loscocos-clean"], filemtime($tiendaCss) . ".2");
    }
    
    // Enhanced sidebar con estilos de boceto
    $tiendaEnhancedCss = get_stylesheet_directory() . "/assets/css/tienda-sidebar-enhanced.css";
    if (file_exists($tiendaEnhancedCss)) {
      wp_enqueue_style("loscocos-tienda-sidebar-enhanced", get_stylesheet_directory_uri() . "/assets/css/tienda-sidebar-enhanced.css", ["loscocos-tienda-sidebar"], filemtime($tiendaEnhancedCss));
    }
    
    // Shop ordering and search
    $shopOrderingCss = get_stylesheet_directory() . "/assets/css/shop-ordering.css";
    if (file_exists($shopOrderingCss)) {
      wp_enqueue_style("loscocos-shop-ordering", get_stylesheet_directory_uri() . "/assets/css/shop-ordering.css", ["loscocos-clean"], filemtime($shopOrderingCss));
    }
  }
  
  // Product Detail CSS
  if (is_product()) {
    $productCss = get_stylesheet_directory() . "/assets/css/product-detail.css";
    if (file_exists($productCss)) {
      wp_enqueue_style("loscocos-product-detail", get_stylesheet_directory_uri() . "/assets/css/product-detail.css", ["loscocos-clean"], filemtime($productCss));
    }
    
    // Product tabs CSS
    $productTabsCss = get_stylesheet_directory() . "/assets/css/product-tabs.css";
    if (file_exists($productTabsCss)) {
      wp_enqueue_style("loscocos-product-tabs", get_stylesheet_directory_uri() . "/assets/css/product-tabs.css", ["loscocos-product-detail"], filemtime($productTabsCss));
    }
  }
  
  // Footer y Dark Mode CSS
  $footerCss = get_stylesheet_directory() . "/assets/css/footer-dark.css";
  if (file_exists($footerCss)) {
    wp_enqueue_style("loscocos-footer-dark", get_stylesheet_directory_uri() . "/assets/css/footer-dark.css", ["loscocos-clean"], filemtime($footerCss));
  }
  
  // Google Fonts: Epilogue (display elegante), Inter (body), Poppins (fallback)
  wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@400;600;700;800&display=swap', [], null);
  // Material Icons para iconografía ligera
  wp_enqueue_style('material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', [], null);
  
  // Hero enhanced CSS para home
  if (is_front_page()) {
    $heroCss = get_stylesheet_directory() . "/assets/css/hero-enhanced.css";
    if (file_exists($heroCss)) {
      wp_enqueue_style("loscocos-hero-enhanced", get_stylesheet_directory_uri() . "/assets/css/hero-enhanced.css", ["loscocos-clean"], filemtime($heroCss));
    }
  }

  // CRITICAL: WooCommerce AJAX support for all WooCommerce pages
  if (function_exists('is_woocommerce') || function_exists('is_cart') || function_exists('is_checkout') || is_front_page()) {
    wp_enqueue_script('wc-add-to-cart');
    wp_enqueue_script('wc-cart-fragments');
    wp_enqueue_script('woocommerce');
  }

  // JS home-carousel DESACTIVADO - Ahora usamos grid normal sin carousel
  // El carousel JS estaba interfiriendo con el scroll natural de la página
  
  /* DESACTIVADO - Ya no se usa carousel
  if (function_exists('is_front_page') && is_front_page()) {
    $homeJs = get_stylesheet_directory() . '/assets/js/home-carousel.js';
    if (file_exists($homeJs)) {
      wp_enqueue_script(
        'loscocos-home-carousel',
        get_stylesheet_directory_uri() . '/assets/js/home-carousel.js',
        ['jquery', 'wc-add-to-cart', 'wc-cart-fragments'],
        filemtime($homeJs),
        true
      );
    }
    
    // WooCommerce AJAX parameters
    if (function_exists('WC')) {
      wp_localize_script('loscocos-home-carousel', 'wc_add_to_cart_params', array(
        'ajax_url'                => WC()->ajax_url(),
        'wc_ajax_url'             => WC_AJAX::get_endpoint('%%endpoint%%'),
        'i18n_view_cart'          => esc_attr__('View cart', 'woocommerce'),
        'cart_url'                => apply_filters('woocommerce_add_to_cart_redirect', wc_get_cart_url(), null),
        'is_cart'                 => is_cart(),
        'cart_redirect_after_add' => get_option('woocommerce_cart_redirect_after_add')
      ));
    }
  }
  */
});

// Menú simple (movido a after_setup_theme para evitar carga temprana del textdomain)

// Favicon: respetar Site Icon solo si existe (sin fallbacks que generen 404)
add_action('wp_head', function () {
  $icon = get_site_icon_url(32);
  if ($icon) {
    echo '<link rel="icon" href="' . esc_url($icon) . '" sizes="32x32" />';
  }
});

// Placeholder WooCommerce: usar el placeholder local del theme si falta imagen
add_filter('woocommerce_placeholder_img_src', function ($src) {
  // Ruta segura al placeholder del theme
  $placeholder = get_stylesheet_directory_uri() . '/placeholder.svg';
  return $placeholder ?: $src;
}, 999);

// Asegurar placeholder robusto en thumbnails de loop (relacionados, tienda, etc.)
add_filter('woocommerce_get_product_thumbnail', function ($html, $size = 'woocommerce_thumbnail') {
  // Si Woo genera un placeholder con clase 'woocommerce-placeholder', forzamos el SVG local
  if (strpos($html, 'woocommerce-placeholder') !== false) {
    $placeholder = esc_url(get_stylesheet_directory_uri() . '/placeholder.svg');
    // Reemplazar src del <img>
    $html = preg_replace('/src="[^"]*"/', 'src="' . $placeholder . '"', $html);
    // Remover srcset/sizes para evitar rutas inexistentes (404)
    $html = preg_replace('/\s(srcset|sizes)="[^"]*"/', '', $html);
  }
  return $html;
}, 10, 2);

// Contador de carrito en el header
add_filter('wp_nav_menu_items', function ($items, $args) {
  if ($args->theme_location !== 'primary') return $items;
  $count = function_exists('WC') ? WC()->cart->get_cart_contents_count() : 0;
  $items .= '<li class="menu-item cart-link"><a href="' . esc_url(wc_get_cart_url()) . '"><span class="material-icons" aria-hidden="true">shopping_cart</span><span class="screen-reader-text">Carrito</span><span class="cart-count">' . intval($count) . '</span></a></li>';
  return $items;
}, 10, 2);

// Fragmento AJAX: refrescar contador del carrito en el header
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
  $count = function_exists('WC') ? WC()->cart->get_cart_contents_count() : 0;
  // Actualiza solo el span del contador
  $fragments['span.cart-count'] = '<span class="cart-count">' . intval($count) . '</span>';
  return $fragments;
});

// Checkout: barra de progreso simple (no intrusiva)
add_action('woocommerce_before_checkout_form', function () {
  if (!function_exists('is_checkout') || !is_checkout()) return;
  // Evitar en la página de confirmación (gracias)
  if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received')) return;
  echo '<div class="progress-steps" aria-hidden="true">'
     . '<div class="step active">' . esc_html__('Carrito', 'loscocos-clean') . '</div>'
     . '<div class="step active">' . esc_html__('Detalles', 'loscocos-clean') . '</div>'
     . '<div class="step">' . esc_html__('Pago', 'loscocos-clean') . '</div>'
     . '<div class="step">' . esc_html__('Confirmación', 'loscocos-clean') . '</div>'
     . '</div>';
}, 5);

// Checkout: badge de seguridad encima del resumen
add_action('woocommerce_checkout_before_order_review', function () {
  echo '<div class="secure-badge">'
     . '<span class="material-icons" aria-hidden="true">lock</span>'
     . '<span>' . esc_html__('Pago seguro', 'loscocos-clean') . '</span>'
     . '</div>';
}, 5);

// Wrappers de contenedor para páginas clave (sin overrides)
add_action('woocommerce_before_cart', function () {
  if (function_exists('is_cart') && is_cart()) {
    echo '<div class="container cart-page">';
  }
}, 1);
add_action('woocommerce_after_cart', function () {
  if (function_exists('is_cart') && is_cart()) {
    echo '</div>';
  }
}, 999);

add_action('woocommerce_before_checkout_form', function () {
  if (function_exists('is_checkout') && is_checkout()) {
    echo '<div class="container checkout-page">';
  }
}, 1);
add_action('woocommerce_after_checkout_form', function () {
  if (function_exists('is_checkout') && is_checkout()) {
    echo '</div>';
  }
}, 999);

add_action('woocommerce_before_thankyou', function () {
  echo '<div class="container order-confirmation">';
}, 1);
add_action('woocommerce_after_thankyou', function () {
  echo '</div>';
}, 999);

// Carrito: barra de progreso (solo paso Carrito activo)
add_action('woocommerce_before_cart', function () {
  if (!function_exists('is_cart') || !is_cart()) return;
  echo '<div class="progress-steps" aria-hidden="true">'
     . '<div class="step active">' . esc_html__('Carrito', 'loscocos-clean') . '</div>'
     . '<div class="step">' . esc_html__('Detalles', 'loscocos-clean') . '</div>'
     . '<div class="step">' . esc_html__('Pago', 'loscocos-clean') . '</div>'
     . '<div class="step">' . esc_html__('Confirmación', 'loscocos-clean') . '</div>'
     . '</div>';
}, 5);

// Gracias: barra de progreso completa + banner de éxito
add_action('woocommerce_before_thankyou', function () {
  echo '<div class="progress-steps" aria-hidden="true">'
     . '<div class="step active">' . esc_html__('Carrito', 'loscocos-clean') . '</div>'
     . '<div class="step active">' . esc_html__('Detalles', 'loscocos-clean') . '</div>'
     . '<div class="step active">' . esc_html__('Pago', 'loscocos-clean') . '</div>'
     . '<div class="step active">' . esc_html__('Confirmación', 'loscocos-clean') . '</div>'
     . '</div>';
  echo '<div class="order-success">'
     . '<span class="material-icons" aria-hidden="true">check_circle</span>'
     . '<span>' . esc_html__('¡Gracias! Tu pedido ha sido recibido.', 'loscocos-clean') . '</span>'
     . '</div>';
}, 5);

// Carrito: icono Material en link de quitar item (no modifica comportamiento)
add_filter('woocommerce_cart_item_remove_link', function ($link, $cart_item_key) {
  if (!function_exists('wc_get_cart_remove_url')) return $link;
  // Solo ajustar en la página de carrito para evitar sorpresas en mini-cart u otros contextos
  if (function_exists('is_cart') && !is_cart()) return $link;
  $url = wc_get_cart_remove_url($cart_item_key);
  $aria = esc_attr__('Quitar este producto', 'loscocos-clean');
  $new = '<a href="' . esc_url($url) . '" class="remove" aria-label="' . $aria . '" title="' . $aria . '">'
       . '<span class="material-icons" aria-hidden="true">delete</span>'
       . '<span class="screen-reader-text">' . esc_html__('Quitar', 'loscocos-clean') . '</span>'
       . '</a>';
  return $new;
}, 10, 2);

// Preconnect a Google Fonts para mejorar rendimiento
add_filter('wp_resource_hints', function ($urls, $relation_type) {
  if ('preconnect' === $relation_type) {
    $urls[] = 'https://fonts.gstatic.com';
    $urls[] = 'https://fonts.googleapis.com';
  }
  return $urls;
}, 10, 2);

// Personalizador: Opciones de Header (top banner y CTA)
add_action('customize_register', function ($wp_customize) {
  $section = 'loscocos_header_section';
  $wp_customize->add_section($section, [
    'title'    => __('Header - Los Cocos', 'loscocos-clean'),
    'priority' => 30,
  ]);

  // Toggle Top Banner
  $wp_customize->add_setting('loscocos_header_topbar_enabled', [
    'default'           => true,
    'sanitize_callback' => function ($value) { return (bool)$value; },
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_header_topbar_enabled', [
    'type'    => 'checkbox',
    'section' => $section,
    'label'   => __('Mostrar banner superior', 'loscocos-clean'),
  ]);

  // Texto Top Banner
  $wp_customize->add_setting('loscocos_header_topbar_text', [
    'default'           => '🚚 Envío gratis el mismo día en Mendoza · Pedidos antes de las 14hs ⚡',
    'sanitize_callback' => 'wp_kses_post',
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_header_topbar_text', [
    'type'    => 'textarea',
    'section' => $section,
    'label'   => __('Texto del banner superior', 'loscocos-clean'),
  ]);

  // CTA Label
  $wp_customize->add_setting('loscocos_header_cta_label', [
    'default'           => 'Club Premium',
    'sanitize_callback' => 'sanitize_text_field',
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_header_cta_label', [
    'type'    => 'text',
    'section' => $section,
    'label'   => __('Etiqueta del botón CTA', 'loscocos-clean'),
  ]);

  // CTA URL
  $wp_customize->add_setting('loscocos_header_cta_url', [
    'default'           => home_url('/club-premium/'),
    'sanitize_callback' => 'esc_url_raw',
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_header_cta_url', [
    'type'    => 'url',
    'section' => $section,
    'label'   => __('URL del botón CTA', 'loscocos-clean'),
  ]);
  
  // ==========================
  // Hero - Portada (Editable)
  // ==========================
  $hero_section = 'loscocos_hero_section';
  $wp_customize->add_section($hero_section, [
    'title'    => __('Hero - Portada', 'loscocos-clean'),
    'priority' => 31,
  ]);

  // Mostrar/ocultar hero en portada
  $wp_customize->add_setting('loscocos_hero_enabled', [
    'default'           => true,
    'sanitize_callback' => function ($value) { return (bool)$value; },
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_hero_enabled', [
    'type'    => 'checkbox',
    'section' => $hero_section,
    'label'   => __('Mostrar Hero en la portada', 'loscocos-clean'),
  ]);

  // Título del hero
  $wp_customize->add_setting('loscocos_hero_title', [
    'default'           => 'Vivero Los Cocos',
    'sanitize_callback' => 'sanitize_text_field',
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_hero_title', [
    'type'    => 'text',
    'section' => $hero_section,
    'label'   => __('Título del Hero', 'loscocos-clean'),
  ]);

  // Subtítulo del hero
  $wp_customize->add_setting('loscocos_hero_subtitle', [
    'default'           => 'Sitio estable para pruebas de tienda',
    'sanitize_callback' => 'sanitize_text_field',
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_hero_subtitle', [
    'type'    => 'text',
    'section' => $hero_section,
    'label'   => __('Subtítulo del Hero', 'loscocos-clean'),
  ]);

  // Imagen de fondo del hero
  $wp_customize->add_setting('loscocos_hero_bg_image', [
    'default'           => '',
    'sanitize_callback' => 'esc_url_raw',
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'loscocos_hero_bg_image', [
    'label'    => __('Imagen de fondo', 'loscocos-clean'),
    'section'  => $hero_section,
    'settings' => 'loscocos_hero_bg_image',
  ]));

  // CTA del hero
  $wp_customize->add_setting('loscocos_hero_cta_label', [
    'default'           => 'Ver productos',
    'sanitize_callback' => 'sanitize_text_field',
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_hero_cta_label', [
    'type'    => 'text',
    'section' => $hero_section,
    'label'   => __('Etiqueta del botón (Hero)', 'loscocos-clean'),
  ]);

  $wp_customize->add_setting('loscocos_hero_cta_url', [
    'default'           => ( function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/tienda/') ),
    'sanitize_callback' => 'esc_url_raw',
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_hero_cta_url', [
    'type'    => 'url',
    'section' => $hero_section,
    'label'   => __('URL del botón (Hero)', 'loscocos-clean'),
  ]);

  // ==========================
  // Promos - Portada (3 banners)
  // ==========================
  $promos_section = 'loscocos_home_promos';
  $wp_customize->add_section($promos_section, [
    'title'    => __('Promociones - Portada', 'loscocos-clean'),
    'priority' => 32,
  ]);

  // Mostrar/ocultar sección de promos
  $wp_customize->add_setting('loscocos_promos_enabled', [
    'default'           => true,
    'sanitize_callback' => function ($value) { return (bool)$value; },
    'transport'         => 'refresh',
  ]);
  $wp_customize->add_control('loscocos_promos_enabled', [
    'type'    => 'checkbox',
    'section' => $promos_section,
    'label'   => __('Mostrar sección de Promociones', 'loscocos-clean'),
  ]);

  // Campos por cada promo (1..3)
  for ($i = 1; $i <= 3; $i++) {
    // Título
    $wp_customize->add_setting("loscocos_promo_{$i}_title", [
      'default'           => '',
      'sanitize_callback' => 'sanitize_text_field',
      'transport'         => 'refresh',
    ]);
    $wp_customize->add_control("loscocos_promo_{$i}_title", [
      'type'    => 'text',
      'section' => $promos_section,
      'label'   => sprintf(__('Promo %d - Título', 'loscocos-clean'), $i),
    ]);

    // Subtítulo
    $wp_customize->add_setting("loscocos_promo_{$i}_subtitle", [
      'default'           => '',
      'sanitize_callback' => 'sanitize_text_field',
      'transport'         => 'refresh',
    ]);
    $wp_customize->add_control("loscocos_promo_{$i}_subtitle", [
      'type'    => 'text',
      'section' => $promos_section,
      'label'   => sprintf(__('Promo %d - Subtítulo', 'loscocos-clean'), $i),
    ]);

    // Imagen
    $wp_customize->add_setting("loscocos_promo_{$i}_image", [
      'default'           => '',
      'sanitize_callback' => 'esc_url_raw',
      'transport'         => 'refresh',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "loscocos_promo_{$i}_image", [
      'label'    => sprintf(__('Promo %d - Imagen', 'loscocos-clean'), $i),
      'section'  => $promos_section,
      'settings' => "loscocos_promo_{$i}_image",
    ]));

    // CTA Label
    $wp_customize->add_setting("loscocos_promo_{$i}_cta_label", [
      'default'           => '',
      'sanitize_callback' => 'sanitize_text_field',
      'transport'         => 'refresh',
    ]);
    $wp_customize->add_control("loscocos_promo_{$i}_cta_label", [
      'type'    => 'text',
      'section' => $promos_section,
      'label'   => sprintf(__('Promo %d - Etiqueta del botón', 'loscocos-clean'), $i),
    ]);

    // CTA URL
    $wp_customize->add_setting("loscocos_promo_{$i}_cta_url", [
      'default'           => '',
      'sanitize_callback' => 'esc_url_raw',
      'transport'         => 'refresh',
    ]);
    $wp_customize->add_control("loscocos_promo_{$i}_cta_url", [
      'type'    => 'url',
      'section' => $promos_section,
      'label'   => sprintf(__('Promo %d - URL del botón', 'loscocos-clean'), $i),
    ]);
  }
});

// ===============================
// WOOCOMMERCE AJAX FUNCTIONALITY
// ===============================

// Enable AJAX add to cart on shop pages
add_filter('woocommerce_loop_add_to_cart_link', function($link, $product, $args) {
  if ($product && $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) {
    $class = implode(
      ' ',
      array_filter([
        'button',
        'product_type_' . $product->get_type(),
        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
        $product->supports('ajax_add_to_cart') && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
      ])
    );
    
    $link = sprintf(
      '<a href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s" %s rel="nofollow">%s</a>',
      esc_url($product->add_to_cart_url()),
      esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
      esc_attr($product->get_id()),
      esc_attr($product->get_sku()),
      esc_attr($class),
      isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
      esc_html($product->add_to_cart_text())
    );
  }
  
  return $link;
}, 10, 3);

// AJAX Add to Cart Handler
function loscocos_woocommerce_ajax_add_to_cart_handler() {
  if (!isset($_POST['product_id'])) {
    wp_die();
  }
  
  $product_id = absint($_POST['product_id']);
  $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
  $variation_id = absint($_POST['variation_id']);
  $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
  $product_status = get_post_status($product_id);
  
  if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {
    do_action('woocommerce_ajax_added_to_cart', $product_id);
    
    if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
      wc_add_to_cart_message(array($product_id => $quantity), true);
    }
    
    WC_AJAX::get_refreshed_fragments();
  } else {
    $data = array(
      'error' => true,
      'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
    );
    
    wp_send_json($data);
  }
  
  wp_die();
}
add_action('wp_ajax_woocommerce_add_to_cart', 'loscocos_woocommerce_ajax_add_to_cart_handler');
add_action('wp_ajax_nopriv_woocommerce_add_to_cart', 'loscocos_woocommerce_ajax_add_to_cart_handler');

// Ensure WooCommerce AJAX URL is available in frontend - MOVED UP to work with home carousel
add_action('wp_enqueue_scripts', function() {
  // This now happens in the main enqueue function above
}, 11);

// ===============================
// FORCE PURCHASABLE PRODUCTS
// ===============================

// Forzar botón "Agregar al carrito" incluso sin precio
add_filter('woocommerce_product_is_purchasable', function($purchasable, $product) {
  if ($product && $product->is_type('simple') && $product->is_in_stock()) {
    return true;
  }
  return $purchasable;
}, 10, 2);

// Cambiar texto del botón a "Agregar al carrito"
add_filter('woocommerce_product_add_to_cart_text', function($text, $product) {
  if ($product && $product->is_type('simple')) {
    return 'Agregar al carrito';
  }
  return $text;
}, 10, 2);

// Personalizar mensaje de "agregado al carrito" - quitar emoji roto
add_filter('wc_add_to_cart_message_html', function($message, $products, $show_qty) {
  // Remover emojis problemáticos
  $message = preg_replace('/[\x{1F300}-\x{1F9FF}]/u', '', $message);
  $message = str_replace(['✓', '✔', '☑'], '', $message);
  
  // Agregar icono Material check
  $icon = '<span class="material-icons" style="color:#10b981;font-size:20px;margin-right:8px;">check_circle</span>';
  
  // Insertar el icono al inicio del mensaje
  if (strpos($message, '<a') !== false) {
    $message = preg_replace('/^(<[^>]+>)?/', '$1' . $icon, $message, 1);
  } else {
    $message = $icon . $message;
  }
  
  return $message;
}, 10, 3);

// Mejorar formato de títulos de producto
add_filter('the_title', function($title, $id = null) {
  if (is_product() && $id === get_the_ID()) {
    // Reemplazar guiones y underscores por espacios
    $title = str_replace(['_', '-'], ' ', $title);
    // Capitalizar correctamente
    $title = ucwords(strtolower($title));
  }
  return $title;
}, 10, 2);

