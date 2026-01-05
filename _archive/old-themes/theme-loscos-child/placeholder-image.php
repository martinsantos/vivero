<?php
/**
 * Handle placeholder image requests
 */

// Only handle requests for placeholder images
if (strpos($_SERVER['REQUEST_URI'], 'product-') !== false && 
    strpos($_SERVER['REQUEST_URI'], '-placeholder.') !== false) {
    
    // Get the requested dimensions from the URL if available
    $width = isset($_GET['w']) ? (int)$_GET['w'] : 300;
    $height = isset($_GET['h']) ? (int)$_GET['h'] : 300;
    
    // Set the content type to SVG
    header('Content-Type: image/svg+xml');
    
    // Create a simple SVG placeholder
    $svg = '<?xml version="1.0" encoding="UTF-8"?>
    <svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" 
         xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
        <rect width="100%" height="100%" fill="#f8f8f8"/>
        <line x1="0" y1="0" x2="' . $width . '" y2="' . $height . '" stroke="#e0e0e0" stroke-width="1" stroke-dasharray="5,5"/>
        <line x1="' . $width . '" y1="0" x2="0" y2="' . $height . '" stroke="#e0e0e0" stroke-width="1" stroke-dasharray="5,5"/>
        <text x="50%" y="50%" font-family="Arial" font-size="14" text-anchor="middle" dominant-baseline="middle" fill="#999">
            No image available
        </text>
    </svg>';
    
    // Output the SVG
    echo $svg;
    exit;
}
