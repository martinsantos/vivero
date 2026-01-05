<?php
/**
 * Functions para Vivero Los Cocos - Estrategia de Archivos Físicos
 */

// Enqueue scripts and styles
function loscocos_enqueue_scripts() {
    // Enqueue Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap', array(), null);
    
    // Enqueue Material Icons
    wp_enqueue_style('material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), null);
    
    // Enqueue theme styles
    wp_enqueue_style('loscocos-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Load Tailwind CSS from CDN (development only)
    if (!defined('WP_ENVIRONMENT_TYPE') || WP_ENVIRONMENT_TYPE !== 'production') {
        wp_enqueue_script('tailwind-cdn', 'https://cdn.tailwindcss.com', array(), '3.3.0', false);
    } else {
        // In production, you should use a built version of Tailwind
        wp_enqueue_style('tailwind-css', get_template_directory_uri() . '/css/tailwind.min.css', array(), '3.3.0');
    }
    
    // Enqueue Tailwind config
    wp_enqueue_script('tailwind-config', get_template_directory_uri() . '/js/tailwind-config.js', array(), '1.0.0', false);
    
    // Ensure WooCommerce cart fragments script is loaded first
    if (function_exists('is_woocommerce')) {
        wp_enqueue_script('wc-cart-fragments');
    }
    
    // Enqueue custom scripts if the file exists
    $main_js_path = get_template_directory() . '/js/main.js';
    if (file_exists($main_js_path)) {
        wp_enqueue_script(
            'loscocos-script',
            get_template_directory_uri() . '/js/main.js',
            array('jquery', 'wc-cart-fragments'),
            filemtime($main_js_path),
            true
        );
        
        // Localize script for AJAX after it's registered
        wp_localize_script('loscocos-script', 'loscocos_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('loscocos-nonce'),
            'add_to_cart_nonce' => wp_create_nonce('add-to-cart-nonce'),
            'is_cart' => is_cart()
        ));
    }
}
add_action('wp_enqueue_scripts', 'loscocos_enqueue_scripts');

// Reemplazar el thumbnail de productos en listados por nuestro SVG por ID
add_filter('woocommerce_get_product_thumbnail', function ($html, $size = 'woocommerce_thumbnail', $deprecated = null) {
    if (!function_exists('wc_get_product')) {
        return $html;
    }
    global $product;
    if (!$product || !is_a($product, 'WC_Product')) {
        return $html;
    }
    $image_url = loscocos_get_product_image($product->get_id());
    return '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product->get_name()) . '" loading="lazy" />';
}, 10, 3);

// Fallback del placeholder de WooCommerce para evitar "Marcador" sin imagen
add_filter('woocommerce_placeholder_img_src', function ($src) {
    $placeholder = get_template_directory_uri() . '/placeholder.svg';
    if (file_exists(get_template_directory() . '/placeholder.svg')) {
        return $placeholder;
    }
    return $src;
});

// Add AJAX handlers for cart operations
add_action('wp_ajax_loscocos_add_to_cart', 'loscocos_ajax_add_to_cart');
add_action('wp_ajax_nopriv_loscocos_add_to_cart', 'loscocos_ajax_add_to_cart');

add_action('wp_ajax_loscocos_update_cart', 'loscocos_ajax_update_cart');
add_action('wp_ajax_nopriv_loscocos_update_cart', 'loscocos_ajax_update_cart');

add_action('wp_ajax_loscocos_remove_item', 'loscocos_ajax_remove_item');
add_action('wp_ajax_nopriv_loscocos_remove_item', 'loscocos_ajax_remove_item');

/**
 * AJAX handler for adding items to cart
 */
function loscocos_ajax_add_to_cart() {
    check_ajax_referer('loscocos-nonce', 'nonce');
    
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 1;
    
    if ($product_id <= 0) {
        wp_send_json_error('ID de producto no válido');
        return;
    }
    
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error('Producto no encontrado');
        return;
    }
    
    // Add to cart
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
    
    if ($cart_item_key) {
        // Get updated cart fragments
        $fragments = array();
        
        // Get mini cart
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();
        
        $fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>';
        
        // Cart hash
        $fragments['cart_hash'] = WC()->cart->get_cart_hash();
        
        // Cart items count
        $fragments['cart_count'] = WC()->cart->get_cart_contents_count();
        
        wp_send_json_success(array(
            'message' => 'Producto añadido al carrito',
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
            'cart_count' => WC()->cart->get_cart_contents_count()
        ));
    } else {
        wp_send_json_error('No se pudo agregar el producto al carrito');
    }
}

/**
 * AJAX handler for updating cart item quantities
 */
function loscocos_ajax_update_cart() {
    check_ajax_referer('loscocos-nonce', 'nonce');
    
    $cart_item_key = isset($_POST['cart_item_key']) ? wc_clean($_POST['cart_item_key']) : '';
    $quantity = isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 0;
    
    if (empty($cart_item_key)) {
        wp_send_json_error('Clave de artículo del carrito no válida');
        return;
    }
    
    // Update cart
    $cart_updated = WC()->cart->set_quantity($cart_item_key, $quantity);
    
    if ($cart_updated) {
        // Get updated cart fragments
        $fragments = array();
        
        // Get mini cart
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();
        
        $fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>';
        
        // Cart hash
        $fragments['cart_hash'] = WC()->cart->get_cart_hash();
        
        // Cart items count
        $fragments['cart_count'] = WC()->cart->get_cart_contents_count();
        
        wp_send_json_success(array(
            'message' => 'Carrito actualizado',
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'subtotal' => WC()->cart->get_cart_subtotal()
        ));
    } else {
        wp_send_json_error('No se pudo actualizar el carrito');
    }
}

/**
 * AJAX handler for removing items from cart
 */
function loscocos_ajax_remove_item() {
    check_ajax_referer('loscocos-nonce', 'nonce');
    
    $cart_item_key = isset($_POST['cart_item_key']) ? wc_clean($_POST['cart_item_key']) : '';
    
    if (empty($cart_item_key)) {
        wp_send_json_error('Clave de artículo del carrito no válida');
        return;
    }
    
    // Remove item from cart
    $cart_updated = WC()->cart->remove_cart_item($cart_item_key);
    
    if ($cart_updated) {
        // Get updated cart fragments
        $fragments = array();
        
        // Get mini cart
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();
        
        $fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>';
        
        // Cart hash
        $fragments['cart_hash'] = WC()->cart->get_cart_hash();
        
        // Cart items count
        $fragments['cart_count'] = WC()->cart->get_cart_contents_count();
        
        wp_send_json_success(array(
            'message' => 'Producto eliminado del carrito',
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'subtotal' => WC()->cart->get_cart_subtotal()
        ));
    } else {
        wp_send_json_error('No se pudo eliminar el producto del carrito');
    }
}

// Configuración básica del tema
function loscocos_theme_setup() {
    // Add theme support
    add_theme_support('woocommerce');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Menú Principal', 'loscocos'),
        'footer'  => __('Menú de Pie', 'loscocos'),
    ));
}
add_action('after_setup_theme', 'loscocos_theme_setup');

// =================================================================================
// SISTEMA DE IMÁGENES SVG - GUARDANDO COMO ARCHIVOS
// =================================================================================

function loscocos_get_product_image($product_id) {
    $upload_dir = wp_upload_dir();
    $images_dir = $upload_dir['basedir'] . '/loscocos-images';
    $images_url = $upload_dir['baseurl'] . '/loscocos-images';
    $image_path = $images_dir . '/' . $product_id . '.svg';
    $image_url = $images_url . '/' . $product_id . '.svg';

    // Crear directorio si no existe
    if (!file_exists($images_dir)) {
        wp_mkdir_p($images_dir);
        // Asegurar permisos correctos
        chmod($images_dir, 0755);
    }

    // Si ya existe, devolverlo
    if (file_exists($image_path)) {
        return $image_url;
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return get_template_directory_uri() . '/placeholder.svg';
    }

    $product_name = $product->get_name();
    $price = $product->get_price();
    
    // Colores para gradientes basados en el ID del producto
    $colors = [
        ['#10b981', '#059669'], // Verde
        ['#3b82f6', '#1d4ed8'], // Azul
        ['#8b5cf6', '#7c3aed'], // Púrpura
        ['#f59e0b', '#d97706'], // Amarillo
        ['#ef4444', '#dc2626'], // Rojo
        ['#06b6d4', '#0891b2'], // Cian
        ['#84cc16', '#65a30d'], // Lima
        ['#f97316', '#ea580c'], // Naranja
    ];
    
    // Emojis para productos
    $emojis = ['🌱', '🌿', '🍃', '🌾', '🌳', '🌲', '🌴', '🌵', '🌺', '🌻', '🌼', '🌷', '🌹', '🥀', '🌸'];
    
    // Seleccionar color y emoji basado en el ID
    $color_index = $product_id % count($colors);
    $emoji_index = $product_id % count($emojis);
    $color_pair = $colors[$color_index];
    $emoji = $emojis[$emoji_index];
    
    // Generar nombre corto
    $display_name = mb_strlen($product_name) > 18 ? mb_substr($product_name, 0, 18) . '...' : $product_name;
    
    $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="400" height="400" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="grad-' . $product_id . '" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="' . $color_pair[0] . '" />
            <stop offset="100%" stop-color="' . $color_pair[1] . '" />
        </linearGradient>
        <filter id="shadow-' . $product_id . '" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="2" dy="2" stdDeviation="3" flood-color="rgba(0,0,0,0.3)"/>
        </filter>
    </defs>
    
    <!-- Fondo con gradiente -->
    <rect width="100%" height="100%" fill="url(#grad-' . $product_id . ')" rx="20" ry="20"/>
    
    <!-- Patrón decorativo -->
    <circle cx="80" cy="80" r="40" fill="rgba(255,255,255,0.1)"/>
    <circle cx="320" cy="120" r="30" fill="rgba(255,255,255,0.1)"/>
    <circle cx="280" cy="280" r="35" fill="rgba(255,255,255,0.1)"/>
    <circle cx="120" cy="320" r="25" fill="rgba(255,255,255,0.1)"/>
    
    <!-- Emoji principal -->
    <text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" 
          font-size="80" filter="url(#shadow-' . $product_id . ')">' . $emoji . '</text>
    
    <!-- Nombre del producto -->
    <text x="50%" y="70%" dominant-baseline="middle" text-anchor="middle" 
          font-family="system-ui, sans-serif" font-size="18" 
          fill="white" font-weight="600" filter="url(#shadow-' . $product_id . ')">' . esc_html($display_name) . '</text>
    
    <!-- Precio -->
    <text x="50%" y="85%" dominant-baseline="middle" text-anchor="middle" 
          font-family="system-ui, sans-serif" font-size="22" 
          fill="white" font-weight="bold" filter="url(#shadow-' . $product_id . ')">$' . number_format($price, 0) . '</text>
    
    <!-- Indicador "Los Cocos" -->
    <text x="50%" y="95%" dominant-baseline="middle" text-anchor="middle" 
          font-family="system-ui, sans-serif" font-size="12" 
          fill="rgba(255,255,255,0.8)" font-weight="400">Los Cocos</text>
</svg>';
    
    // Guardar archivo
    $result = file_put_contents($image_path, $svg);
    if ($result !== false) {
        chmod($image_path, 0644);
        return $image_url;
    }
    
    // Fallback si no se pudo escribir
    return get_template_directory_uri() . '/placeholder.svg';
}

// =================================================================================
// FILTRO UNIVERSAL PARA FORZAR IMÁGENES EN TODO EL SITIO
// =================================================================================

add_filter('woocommerce_product_get_image', 'loscocos_force_svg_image_file', 10, 2);

function loscocos_force_svg_image_file($image, $product) {
    $image_url = loscocos_get_product_image($product->get_id());
    return '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product->get_name()) . '" />';
}

// =================================================================================
// FORZAR TEMPLATE SINGLE-PRODUCT PARA PRODUCTOS INDIVIDUALES
// =================================================================================

// Forzar el uso del template single-product para productos individuales
function loscocos_force_single_product_template($template) {
    global $wp_query;
    
    // Si es una consulta de producto individual estándar de WooCommerce
    if (is_singular('product')) {
        // Buscar el template single-product.php en el tema
        $single_product_template = locate_template('single-product.php');
        if ($single_product_template) {
            return $single_product_template;
        }
        
        // Si no existe, buscar en woocommerce/single-product.php
        $woo_single_template = locate_template('woocommerce/single-product.php');
        if ($woo_single_template) {
            return $woo_single_template;
        }
    }
    
    return $template;
}
add_filter('template_include', 'loscocos_force_single_product_template', 99);
?>
