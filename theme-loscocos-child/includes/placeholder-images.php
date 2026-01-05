<?php
/**
 * Handle product placeholder images
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get or generate product image
 */
function loscocos_get_product_image($product_id) {
    // Get WordPress upload directory info
    $upload_dir = wp_upload_dir();
    
    // Define directories
    $images_dir = $upload_dir['basedir'] . '/loscocos-images';
    $images_url = $upload_dir['baseurl'] . '/loscocos-images';
    
    // Create directory if it doesn't exist
    if (!file_exists($images_dir)) {
        wp_mkdir_p($images_dir);
        // Ensure directory has proper permissions
        chmod($images_dir, 0755);
    }
    
    // Define image paths
    $image_path = $images_dir . '/' . $product_id . '.svg';
    $image_url = $images_url . '/' . $product_id . '.svg';
    
    // Also verify the format alternative that appears in the logs
    $alt_image_path = $upload_dir['basedir'] . '/product-' . $product_id . '-placeholder.svg';
    $alt_image_url = $upload_dir['baseurl'] . '/product-' . $product_id . '-placeholder.svg';
    
    // Check if we need to create .htaccess for Apache
    $htaccess_file = $images_dir . '/.htaccess';
    if (!file_exists($htaccess_file)) {
        $htaccess_content = "<IfModule mod_mime.c>\n";
        $htaccess_content .= "AddType image/svg+xml .svg\n";
        $htaccess_content .= "AddType image/svg+xml .svgz\n";
        $htaccess_content .= "</IfModule>\n";
        $htaccess_content .= "<IfModule mod_headers.c>\n";
        $htaccess_content .= "Header set Access-Control-Allow-Origin *\n";
        $htaccess_content .= "</IfModule>\n";
        file_put_contents($htaccess_file, $htaccess_content);
        chmod($htaccess_file, 0644);
    }
    
    // Return existing image if it exists
    if (file_exists($image_path)) {
        chmod($image_path, 0644); // Ensure proper permissions
        return $image_url;
    } elseif (file_exists($alt_image_path)) {
        chmod($alt_image_path, 0644); // Ensure proper permissions
        return $alt_image_url;
    }
    
    // Get product data
    $product = wc_get_product($product_id);
    if (!$product) {
        // Create generic placeholder
        $generic_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="280" viewBox="0 0 280 280">
            <defs>
                <linearGradient id="grad-generic-' . $product_id . '" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#9ca3af" />
                    <stop offset="100%" stop-color="#6b7280" />
                </linearGradient>
            </defs>
            <rect width="100%" height="100%" fill="url(#grad-generic-' . $product_id . ')" rx="12"/>
            <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="80" fill="white">📦</text>
            <text x="50%" y="85%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16" fill="white" font-weight="bold">Producto</text>
        </svg>';
        
        file_put_contents($image_path, $generic_svg);
        chmod($image_path, 0644);
        
        return $image_url;
    }
    
    // Generate product-specific SVG
    $product_name = $product->get_name();
    $color1 = '#059669';
    $color2 = '#10b981';
    $icon = '🌱';
    
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="280" viewBox="0 0 280 280">
        <defs>
            <linearGradient id="grad-' . $product_id . '" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="' . $color1 . '" />
                <stop offset="100%" stop-color="' . $color2 . '" />
            </linearGradient>
        </defs>
        <rect width="100%" height="100%" fill="url(#grad-' . $product_id . ')" rx="12"/>
        <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="80" fill="white">' . $icon . '</text>
        <text x="50%" y="85%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16" fill="white" font-weight="bold">' . esc_html($product_name) . '</text>
    </svg>';
    
    file_put_contents($image_path, $svg);
    chmod($image_path, 0644);
    
    return $image_url;
}

/**
 * Filter product image HTML
 */
function loscocos_product_image_html($html, $product_id) {
    $image_url = loscocos_get_product_image($product_id);
    return '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title($product_id)) . '" class="woocommerce-placeholder wp-post-image" />';
}
add_filter('woocommerce_placeholder_img', 'loscocos_product_image_html', 10, 2);

/**
 * Add MIME types
 */
function loscocos_upload_mimes($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'loscocos_upload_mimes');

/**
 * Fix SVG upload verification
 */
function loscocos_fix_svg_upload_check($data, $file, $filename, $mimes) {
    $filetype = wp_check_filetype($filename, $mimes);
    
    return [
        'ext'             => $filetype['ext'],
        'type'           => $filetype['type'],
        'proper_filename' => $filename
    ];
}
add_filter('wp_check_filetype_and_ext', 'loscocos_fix_svg_upload_check', 10, 4);
