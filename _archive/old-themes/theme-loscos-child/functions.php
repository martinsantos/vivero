<?php
/**
 * Theme functions and definitions
 *
 * @package LosCocos_Child
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register AJAX actions
add_action('wp_ajax_update_cart_quantity', 'loscocos_ajax_update_cart_quantity');
add_action('wp_ajax_nopriv_update_cart_quantity', 'loscocos_ajax_update_cart_quantity');

add_action('wp_ajax_remove_cart_item', 'loscocos_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_remove_cart_item', 'loscocos_ajax_remove_cart_item');

add_action('wp_ajax_add_to_cart', 'loscocos_ajax_add_to_cart');
add_action('wp_ajax_nopriv_add_to_cart', 'loscocos_ajax_add_to_cart');

add_action('wp_ajax_update_cart', 'loscocos_ajax_update_cart');
add_action('wp_ajax_nopriv_update_cart', 'loscocos_ajax_update_cart');

/**
 * Enqueue scripts and styles.
 */
function loscocos_child_enqueue_scripts() {
    // Dequeue parent theme's Tailwind CDN
    wp_dequeue_script('tailwind-cdn');
    
    // Get theme version
    $theme_version = wp_get_theme()->get('Version');
    
    // Enqueue child theme stylesheet
    wp_enqueue_style(
        'loscocos-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array(),
        filemtime(get_stylesheet_directory() . '/style.css')
    );
    
    // Enqueue WooCommerce styles if WooCommerce is active
    if (class_exists('WooCommerce')) {
        wp_enqueue_style(
            'loscocos-woocommerce-style',
            get_stylesheet_directory_uri() . '/woocommerce.css',
            array('loscocos-child-style'),
            filemtime(get_stylesheet_directory() . '/woocommerce.css')
        );
    }
    
    // Enqueue main JavaScript with dependencies
    wp_enqueue_script('loscocos-main', 
        get_stylesheet_directory_uri() . '/assets/js/main.js', 
        array('jquery'), 
        $theme->get('Version'), 
        true
    );
    
    // Enqueue cart JavaScript
    wp_enqueue_script('loscocos-cart', 
        get_stylesheet_directory_uri() . '/assets/js/cart.js', 
        array('jquery', 'loscocos-main'), 
        $theme->get('Version'), 
        true
    );
    
    // Localize script with AJAX URL and other parameters
    wp_localize_script('loscocos-cart', 'loscocos_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('loscocos-ajax-nonce'),
        'i18n_error_message' => __('Error processing request. Please try again.', 'loscocos-child'),
        'is_cart' => is_cart() ? '1' : '0'
    ));
    
    // Enqueue performance optimizations script
    wp_enqueue_script('loscocos-optimizations', 
        get_stylesheet_directory_uri() . '/assets/js/optimizations.js', 
        array('jquery', 'loscocos-main', 'loscocos-cart'), 
        $theme->get('Version'), 
        true
    );
    
    // Add preload for critical resources
    add_action('wp_head', function() {
        echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/assets/css/theme.css" as="style">';
        echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/assets/js/main.js" as="script">';
    });
    
    // Ensure WooCommerce cart fragments script is loaded
    if (function_exists('is_woocommerce')) {
        wp_enqueue_script('wc-cart-fragments');
        
        // Localize the main script with AJAX URL and nonce
        wp_localize_script('loscocos-main', 'loscocos_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('loscocos-nonce'),
            'is_cart' => is_cart() ? '1' : '0',
            'is_checkout' => is_checkout() ? '1' : '0',
            'cart_url' => wc_get_cart_url(),
            'checkout_url' => wc_get_checkout_url(),
            'i18n' => array(
                'added_to_cart' => __('Producto añadido al carrito', 'loscocos'),
                'error_adding' => __('Error al agregar el producto al carrito', 'loscocos'),
                'updating_cart' => __('Actualizando carrito...', 'loscocos'),
                'cart_updated' => __('Carrito actualizado', 'loscocos'),
                'error_updating' => __('Error al actualizar el carrito', 'loscocos'),
                'removing_item' => __('Eliminando producto...', 'loscocos'),
                'item_removed' => __('Producto eliminado', 'loscocos'),
                'error_removing' => __('Error al eliminar el producto', 'loscocos'),
                'empty_cart' => __('El carrito está vacío', 'loscocos'),
                'loading' => __('Cargando...', 'loscocos')
            )
        ));
    }
}
add_action('wp_enqueue_scripts', 'loscocos_child_enqueue_scripts', 20);

/**
 * AJAX Handlers for Cart Operations
 */
add_action('wp_ajax_loscocos_add_to_cart', 'loscocos_ajax_add_to_cart');
add_action('wp_ajax_nopriv_loscocos_add_to_cart', 'loscocos_ajax_add_to_cart');
add_action('wp_ajax_loscocos_update_cart', 'loscocos_ajax_update_cart');
add_action('wp_ajax_nopriv_loscocos_update_cart', 'loscocos_ajax_update_cart');
add_action('wp_ajax_update_cart_quantity', 'loscocos_ajax_update_cart_quantity');
add_action('wp_ajax_nopriv_update_cart_quantity', 'loscocos_ajax_update_cart_quantity');
add_action('wp_ajax_remove_cart_item', 'loscocos_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_remove_cart_item', 'loscocos_ajax_remove_cart_item');

/**
 * Update cart quantity AJAX handler
 */
function loscocos_ajax_update_cart_quantity() {
    check_ajax_referer('loscocos-nonce', 'nonce');
    
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        wp_send_json_error('Invalid request');
        return;
    }
    
    $cart_key = isset($_POST['cart_key']) ? wc_clean($_POST['cart_key']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if (empty($cart_key)) {
        wp_send_json_error('Invalid cart item');
        return;
    }
    
    // Update cart item quantity
    $cart = WC()->cart;
    $cart_item = $cart->get_cart_item($cart_key);
    
    if (!$cart_item) {
        wp_send_json_error('Cart item not found');
        return;
    }
    
    // Validate quantity
    $product = $cart_item['data'];
    $min_quantity = $product->get_min_purchase_quantity();
    $max_quantity = $product->get_max_purchase_quantity();
    
    if ($quantity < $min_quantity) {
        $quantity = $min_quantity;
    } elseif ($max_quantity > 0 && $quantity > $max_quantity) {
        $quantity = $max_quantity;
    }
    
    // Update cart
    $updated = $cart->set_quantity($cart_key, $quantity);
    
    if (is_wp_error($updated)) {
        wp_send_json_error($updated->get_error_message());
        return;
    }
    
    // Get updated fragments
    $fragments = array();
    
    // Get mini cart
    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();
    
    // Get cart contents
    ob_start();
    woocommerce_cart_totals();
    $cart_totals = ob_get_clean();
    
    // Get cart items
    ob_start();
    woocommerce_cart_totals_order_total_html();
    $cart_total = ob_get_clean();
    
    // Get cart count
    $cart_count = $cart->get_cart_contents_count();
    
    // Prepare response
    $response = array(
        'success' => true,
        'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
            'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
            '.cart-totals' => $cart_totals,
            '.cart-count' => '<span class="cart-count">' . $cart_count . '</span>',
            '.cart-total' => $cart_total,
        )),
        'cart_hash' => $cart->get_cart_hash(),
    );
    
    wp_send_json($response);
}

/**
 * Remove cart item AJAX handler
 */
function loscocos_ajax_remove_cart_item() {
    check_ajax_referer('loscocos-nonce', 'nonce');
    
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        wp_send_json_error('Invalid request');
        return;
    }
    
    $cart_key = isset($_POST['cart_key']) ? wc_clean($_POST['cart_key']) : '';
    
    if (empty($cart_key)) {
        wp_send_json_error('Invalid cart item');
        return;
    }
    
    // Remove item from cart
    $removed = WC()->cart->remove_cart_item($cart_key);
    
    if (!$removed) {
        wp_send_json_error('Error removing item from cart');
        return;
    }
    
    // Get updated fragments
    $fragments = array();
    
    // Get mini cart
    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();
    
    // Get cart contents
    ob_start();
    woocommerce_cart_totals();
    $cart_totals = ob_get_clean();
    
    // Get cart items
    ob_start();
    woocommerce_cart_totals_order_total_html();
    $cart_total = ob_get_clean();
    
    // Get cart count
    $cart_count = WC()->cart->get_cart_contents_count();
    
    // Check if cart is empty
    $is_cart_empty = WC()->cart->is_empty();
    
    // Prepare response
    $response = array(
        'success' => true,
        'is_cart_empty' => $is_cart_empty,
        'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
            'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
            '.cart-totals' => $cart_totals,
            '.cart-count' => '<span class="cart-count">' . $cart_count . '</span>',
            '.cart-total' => $cart_total,
        )),
        'cart_hash' => WC()->cart->get_cart_hash(),
    );
    
    // If cart is empty, add redirect URL
    if ($is_cart_empty) {
        $response['redirect'] = wc_get_cart_url();
    }
    
    wp_send_json($response);
}

/**
 * Add to cart AJAX handler
 */
function loscocos_ajax_add_to_cart() {
    check_ajax_referer('loscocos-nonce', 'security');
    
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        wp_send_json_error('Invalid request');
        return;
    }
    
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 1;
    
    if (!$product_id) {
        wp_send_json_error('Invalid product');
        return;
    }
    
    // Add item to cart
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
    
    if ($cart_item_key) {
        // Get updated fragments
        $fragments = array();
        
        // Get mini cart
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();
        
        $fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>';
        
        // Get cart count
        $fragments['span.cart-count'] = '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
        
        // Get cart total
        $fragments['span.cart-total'] = '<span class="cart-total">' . WC()->cart->get_cart_total() . '</span>';
        
        wp_send_json_success(array(
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
            'notice' => apply_filters('wc_add_to_cart_message', 'Producto añadido correctamente', $product_id)
        ));
    } else {
        wp_send_json_error('Error al agregar el producto al carrito');
    }
}

// Register AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_update_cart', 'loscocos_ajax_update_cart');
add_action('wp_ajax_nopriv_update_cart', 'loscos_ajax_update_cart');

/**
 * Update cart AJAX handler
 */
function loscocos_ajax_update_cart() {
    check_ajax_referer('loscocos-nonce', 'security');
    
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        wp_send_json_error('Invalid request');
        return;
    }
    
    $cart_item_key = isset($_POST['cart_item_key']) ? wc_clean($_POST['cart_item_key']) : '';
    $quantity = isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 0;
    
    if ($cart_item_key && $quantity > 0) {
        WC()->cart->set_quantity($cart_item_key, $quantity);
        
        if (WC()->cart->get_cart_item($cart_item_key)) {
            // Get updated fragments
            $fragments = array();
            
            // Get mini cart
            ob_start();
            woocommerce_mini_cart();
            $mini_cart = ob_get_clean();
            
            $fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>';
            
            // Get cart count
            $fragments['span.cart-count'] = '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
            
            // Get cart total
            $fragments['span.cart-total'] = '<span class="cart-total">' . WC()->cart->get_cart_total() . '</span>';
            
            wp_send_json_success(array(
                'fragments' => $fragments,
                'cart_hash' => WC()->cart->get_cart_hash(),
                'notice' => 'Carrito actualizado correctamente'
            ));
        } else {
            wp_send_json_error('Error al actualizar la cantidad');
        }
    } else {
        wp_send_json_error('Parámetros inválidos');
    }
}

/**
 * Properly handle jQuery and jQuery Migrate
 */
function loscocos_child_handle_scripts() {
    if (!is_admin()) {
        // First, completely remove jQuery Migrate
        wp_deregister_script('jquery-migrate');
        wp_dequeue_script('jquery-migrate');
        
        // Remove the default jQuery
        wp_deregister_script('jquery');
        
        // Register a clean version of jQuery
        wp_register_script('jquery', 
            includes_url('/js/jquery/jquery.min.js'),
            array(),
            '3.7.1',
            true
        );
        
        // Re-enqueue jQuery
        wp_enqueue_script('jquery');
        
        // Remove jQuery Migrate from any scripts that might be adding it back
        global $wp_scripts;
        if (!empty($wp_scripts->registered['jquery']->deps)) {
            $wp_scripts->registered['jquery']->deps = array_diff(
                $wp_scripts->registered['jquery']->deps,
                array('jquery-migrate')
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'loscocos_child_handle_scripts', 1);

// Remove jQuery Migrate from the frontend
function loscocos_remove_jquery_migrate($scripts) {
    if (!is_admin() && !empty($scripts->registered['jquery'])) {
        $jquery_dependencies = $scripts->registered['jquery']->deps;
        $scripts->registered['jquery']->deps = array_diff($jquery_dependencies, array('jquery-migrate'));
    }
}
add_action('wp_default_scripts', 'loscocos_remove_jquery_migrate');

/**
 * Add WooCommerce theme support and customize WooCommerce setup
 */
function loscocos_child_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'loscocos_child_woocommerce_setup');

/**
 * Custom WooCommerce placeholder image
 */
function loscocos_child_woocommerce_placeholder_img($image, $size, $dimensions) {
    // Get default dimensions if not provided
    if (empty($dimensions['width'])) {
        $dimensions = wc_get_image_size($size);
        $dimensions = array(
            'width' => $dimensions['width'] ?: 400,
            'height' => $dimensions['height'] ?: 300,
        );
    }
    
    // Create a simple SVG placeholder
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . esc_attr($dimensions['width']) . ' ' . esc_attr($dimensions['height']) . '" width="' . esc_attr($dimensions['width']) . '" height="' . esc_attr($dimensions['height']) . '">
        <rect width="100%" height="100%" fill="#f3f4f6"/>
        <text x="50%" y="50%" font-family="system-ui, -apple-system, sans-serif" font-size="16" text-anchor="middle" dy=".3em" fill="#9ca3af">' . esc_html__('Sin imagen', 'loscocos-child') . '</text>
              font-size="14" 
              text-anchor="middle" 
              dominant-baseline="middle" 
              fill="#999">
            Sin imagen disponible
        </text>
    </svg>';
    
    // Return as data URI
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/**
 * Filter to handle missing product images
 */
function loscocos_handle_missing_product_image($image_html, $product, $size) {
    // If the image HTML is empty or doesn't contain a valid src
    if (empty($image_html) || strpos($image_html, 'src="') === false) {
        // Get the image dimensions for the requested size
        $image_size = wc_get_image_size($size);
        $dimensions = array(
            'width' => isset($image_size['width']) ? $image_size['width'] : 400,
            'height' => isset($image_size['height']) ? $image_size['height'] : 300
        );
        
        // Get the SVG placeholder as a data URL
        $placeholder = loscocos_child_woocommerce_placeholder_img('', $size, $dimensions);
        
        // Return a clean image tag with the SVG data URL
        return sprintf(
            '<img src="%s" alt="%s" class="%s" width="%d" height="%d" loading="lazy" />',
            esc_url($placeholder),
            esc_attr__('Product placeholder', 'loscocos-child'),
            'woocommerce-placeholder wp-post-image',
            $dimensions['width'],
            $dimensions['height']
        );
    }
    
    return $image_html;
}
add_filter('woocommerce_placeholder_img', 'loscocos_handle_missing_product_image', 10, 3);

/**
 * Filter to handle missing product thumbnails
 */
function loscocos_handle_missing_product_thumbnail($html, $post_id) {
    // If we already have an image, return it
    if (!empty($html)) {
        return $html;
    }
    
    // Get the product
    $product = wc_get_product($post_id);
    if (!$product) {
        return $html;
    }
    
    // Get the image size
    $dimensions = wc_get_image_size('woocommerce_thumbnail');
    $width = $dimensions['width'] ?? 300;
    $height = $dimensions['height'] ?? 300;
    
    // Generate a placeholder SVG
    $placeholder = loscocos_child_woocommerce_placeholder_img('', 'woocommerce_thumbnail', $dimensions);
    
    // Return the image HTML with the placeholder
    return sprintf(
        '<img src="%s" alt="%s" width="%d" height="%d" class="woocommerce-placeholder wp-post-image" loading="lazy" />',
        esc_url($placeholder),
        esc_attr($product->get_name()),
        esc_attr($width),
        esc_attr($height)
    );
}
add_filter('woocommerce_placeholder_img_src', 'loscocos_child_woocommerce_placeholder_img', 10, 3);
add_filter('woocommerce_placeholder_img_src', 'loscocos_child_woocommerce_placeholder_img', 10, 3);

/**
 * Custom WooCommerce placeholder image for product variations
 */
function loscocos_child_woocommerce_placeholder_img_html($image_html, $product, $size) {
    try {
        // Get dimensions for the requested size
        $dimensions = wc_get_image_size($size);
        
        // If no dimensions found, use defaults
        if (empty($dimensions)) {
            $dimensions = array(
                'width' => 300,
                'height' => 300,
                'crop' => 1
            );
        }
        
        // Generate the placeholder image URL
        $placeholder_url = loscocos_child_woocommerce_placeholder_img('', $size, $dimensions);
        
        // Create the image HTML
        $image_html = sprintf(
            '<img src="%s" alt="%s" width="%d" height="%d" class="woocommerce-placeholder wp-post-image" loading="lazy" />',
            esc_url($placeholder_url),
            esc_attr__('Product placeholder', 'woocommerce'),
            esc_attr($dimensions['width']),
            esc_attr($dimensions['height'])
        );
        
    } catch (Exception $e) {
        // Fallback to default WooCommerce placeholder if something goes wrong
        $image_html = wc_placeholder_img($size);
    }
    
    return $image_html;
}
add_filter('woocommerce_placeholder_img_html', 'loscocos_child_woocommerce_placeholder_img_html', 10, 3);
