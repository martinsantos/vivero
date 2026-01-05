<?php

// Incluir helpers de URLs (loscocos_product_url, etc.)
 $inc = get_template_directory() . '/includes/url-manager.php';
 if ( file_exists( $inc ) ) { require_once $inc; }

// Remove jQuery Migrate (con guardas para child theme)
if ( ! function_exists('loscocos_remove_jquery_migrate') ) {
    function loscocos_remove_jquery_migrate() {
        if (!is_admin() && !is_customize_preview()) {
            wp_deregister_script('jquery-migrate');
            wp_dequeue_script('jquery-migrate');
            
            // Remove from global script dependencies
            global $wp_scripts;
            if (isset($wp_scripts->registered['jquery-core'])) {
                $wp_scripts->registered['jquery-core']->deps = array_diff(
                    $wp_scripts->registered['jquery-core']->deps, 
                    array('jquery-migrate')
                );
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'loscocos_remove_jquery_migrate', 1);
add_action('init', 'loscocos_remove_jquery_migrate', 1);

// AJAX Setup (enqueue y nonce)
if ( ! function_exists('loscocos_ajax_setup') ) {
    function loscocos_ajax_setup() {
        wp_enqueue_script('cart-woocommerce', get_template_directory_uri() . '/js/cart-woocommerce.js', array('jquery'), '2.0.0', true);
        wp_localize_script('cart-woocommerce', 'loscocos_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('loscocos_ajax_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'loscocos_ajax_setup');

// AJAX Add to Cart Handler (con guardas)
if ( ! function_exists('loscocos_add_to_cart') ) {
    function loscocos_add_to_cart() {
        check_ajax_referer('loscocos_ajax_nonce', 'nonce');
        
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        
        if ($product_id <= 0) {
            wp_send_json_error('Invalid product ID');
        }
        
        $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
        
        if ($passed_validation) {
            $added = WC()->cart->add_to_cart($product_id, $quantity);
            
            if ($added) {
                $data = array(
                    'message' => __('Product added to cart successfully', 'loscocos'),
                    'cart_count' => WC()->cart->get_cart_contents_count(),
                    'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array())
                );
                wp_send_json_success($data);
            } else {
                wp_send_json_error(__('Error adding product to cart', 'loscocos'));
            }
        } else {
            wp_send_json_error(__('Product validation failed', 'loscocos'));
        }
    }
}
add_action('wp_ajax_loscocos_add_to_cart', 'loscocos_add_to_cart');
add_action('wp_ajax_nopriv_loscocos_add_to_cart', 'loscocos_add_to_cart');

// Enqueue theme styles and Google Fonts (global aesthetics)
if ( ! function_exists('loscocos_enqueue_styles') ) {
    function loscocos_enqueue_styles() {
        // Google Fonts for Inter and Poppins
        wp_enqueue_style(
            'loscocos-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap',
            array(),
            null
        );

        // Material Icons (requerido por cart-woocommerce.js para el spinner)
        wp_enqueue_style(
            'material-icons',
            'https://fonts.googleapis.com/icon?family=Material+Icons',
            array(),
            null
        );

        // Theme stylesheet (style.css)
        wp_enqueue_style(
            'loscocos-style',
            get_stylesheet_uri(),
            array(),
            '6.0.1'
        );

        // Unified product/card styles (asegura altura de contenedores de imagen)
        $unified = get_template_directory() . '/assets/css/unified-styles.css';
        if ( file_exists( $unified ) ) {
            wp_enqueue_style(
                'loscocos-unified-styles',
                get_template_directory_uri() . '/assets/css/unified-styles.css',
                array('loscocos-style'),
                filemtime( $unified )
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'loscocos_enqueue_styles', 5);

/**
 * Helper: Obtener URL de imagen de producto con fallback a SVG generado
 * Busca thumbnail; si no hay, intenta /uploads/loscocos-images/{ID}.svg; si no existe, usa placeholder WooCommerce.
 */
if ( ! function_exists('loscocos_get_product_image_url') ) {
    function loscocos_get_product_image_url( $product_id, $size = 'woocommerce_thumbnail' ) {
        // 1) Thumbnail nativo si existe
        $thumb = get_the_post_thumbnail_url( $product_id, $size );
        if ( $thumb ) {
            return $thumb;
        }

        // 2) Fallback a SVG generado si existe en uploads/loscocos-images/{ID}.svg
        $upload_dir = wp_upload_dir();
        if ( ! empty( $upload_dir['basedir'] ) && ! empty( $upload_dir['baseurl'] ) ) {
            $svg_path = trailingslashit( $upload_dir['basedir'] ) . 'loscocos-images/' . $product_id . '.svg';
            if ( file_exists( $svg_path ) ) {
                return trailingslashit( $upload_dir['baseurl'] ) . 'loscocos-images/' . $product_id . '.svg';
            }
        }

        // 3) Último recurso: placeholder de WooCommerce
        if ( function_exists('wc_placeholder_img_src') ) {
            return wc_placeholder_img_src( $size );
        }

        return '';
    }
}

/**
 * Filtro global: cuando WooCommerce intenta renderizar una imagen y cae en placeholder,
 * usamos el SVG generado por producto si existe. Cubre listados, miniaturas y single product.
 */
if ( ! function_exists('loscocos_wc_image_placeholder_fallback') ) {
    function loscocos_wc_image_placeholder_fallback( $image, $product, $size, $attr, $placeholder ) {
        // Si no es placeholder, no tocar
        if ( ! $placeholder && ! empty( $image ) ) {
            return $image;
        }

        if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
            return $image;
        }

        $product_id = $product->get_id();
        if ( ! $product_id ) {
            return $image;
        }

        if ( function_exists('loscocos_get_product_image_url') ) {
            $svg_url = loscocos_get_product_image_url( $product_id, $size );
            if ( $svg_url ) {
                // Armar etiqueta <img> mínima y segura
                $classes = is_string($size) ? 'attachment-' . esc_attr($size) . ' size-' . esc_attr($size) : 'wp-post-image';
                $alt = esc_attr( wp_strip_all_tags( $product->get_name() ) );
                $attrs = '';
                if ( is_array( $attr ) ) {
                    foreach ( $attr as $k => $v ) {
                        $attrs .= ' ' . esc_attr($k) . '="' . esc_attr($v) . '"';
                    }
                }
                return '<img src="' . esc_url( $svg_url ) . '" alt="' . $alt . '" class="' . $classes . '"' . $attrs . ' />';
            }
        }

        return $image; // fallback original
    }
}
add_filter( 'woocommerce_product_get_image', 'loscocos_wc_image_placeholder_fallback', 10, 5 );

/**
 * AJAX: Actualizar cantidad del carrito por cart_item_key
 */
if ( ! function_exists('loscocos_ajax_update_cart') ) {
    function loscocos_ajax_update_cart() {
        check_ajax_referer('loscocos_ajax_nonce', 'nonce');

        if ( ! isset($_POST['cart_item_key']) ) {
            wp_send_json_error(__('Falta cart_item_key', 'loscocos'));
        }

        $cart_item_key = sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) );
        $quantity      = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

        if ( ! WC()->cart ) {
            wp_send_json_error(__('Carrito no disponible', 'loscocos'));
        }

        $cart = WC()->cart->get_cart();
        if ( ! isset($cart[$cart_item_key]) ) {
            wp_send_json_error(__('Ítem no encontrado en el carrito', 'loscocos'));
        }

        // Actualizar cantidad
        $updated = WC()->cart->set_quantity( $cart_item_key, $quantity, true );

        if ( false === $updated ) {
            wp_send_json_error(__('No se pudo actualizar la cantidad', 'loscocos'));
        }

        // Preparar respuesta con datos útiles
        $response = array(
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total(),
            'fragments'  => apply_filters('woocommerce_add_to_cart_fragments', array()),
        );

        wp_send_json_success( $response );
    }
}

add_action('wp_ajax_loscocos_update_cart', 'loscocos_ajax_update_cart');
add_action('wp_ajax_nopriv_loscocos_update_cart', 'loscocos_ajax_update_cart');
