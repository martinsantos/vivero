<?php
// Create navigation menu for Los Cocos
require_once '/var/www/html/wp-load.php';

$menu_name = 'Primary Menu';
$menu_exists = wp_get_nav_menu_object($menu_name);

if (!$menu_exists) {
    $menu_id = wp_create_nav_menu($menu_name);
    
    // Add menu items
    wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title' => 'Inicio',
        'menu-item-url' => home_url('/'),
        'menu-item-status' => 'publish'
    ));
    
    wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title' => 'Tienda',
        'menu-item-url' => home_url('/tienda/'),
        'menu-item-status' => 'publish'
    ));
    
    wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title' => 'Carrito',
        'menu-item-url' => home_url('/carro/'),
        'menu-item-status' => 'publish'
    ));
    
    // Set menu location
    set_theme_mod('nav_menu_locations', array('primary' => $menu_id));
    
    echo 'Menu created successfully!';
} else {
    echo 'Menu already exists';
}
?>