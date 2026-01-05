<?php
// Script to properly activate the Los Cocos Clean theme
require_once('wp-config.php');

// Activate the theme
$theme = 'loscocos-clean';
switch_theme($theme, $theme);

// Update theme mods for proper configuration
set_theme_mod('loscocos_header_topbar_enabled', true);
set_theme_mod('loscocos_header_topbar_text', '🚚 Envío gratis el mismo día en Mendoza · Pedidos antes de las 14hs ⚡');
set_theme_mod('loscocos_header_cta_label', 'Club Premium');
set_theme_mod('loscocos_header_cta_url', home_url('/club-premium/'));

// Hero section
set_theme_mod('loscocos_hero_enabled', true);
set_theme_mod('loscocos_hero_title', 'Vivero Los Cocos');
set_theme_mod('loscocos_hero_subtitle', 'Tu jardín, nuestro paraíso');
set_theme_mod('loscocos_hero_cta_label', 'Ver productos');
set_theme_mod('loscocos_hero_cta_url', wc_get_page_permalink('shop'));

// Promos section
set_theme_mod('loscocos_promos_enabled', true);
for ($i = 1; $i <= 3; $i++) {
    set_theme_mod("loscocos_promo_{$i}_title", "Promoción {$i}");
    set_theme_mod("loscocos_promo_{$i}_subtitle", "Descripción de la promoción {$i}");
    set_theme_mod("loscocos_promo_{$i}_cta_label", "Ver más");
    set_theme_mod("loscocos_promo_{$i}_cta_url", wc_get_page_permalink('shop'));
}

echo "Theme 'loscocos-clean' activated successfully with default configuration!\n";

// Verify the theme is active
$current_theme = wp_get_theme();
if ($current_theme->stylesheet == 'loscocos-clean') {
    echo "Theme activation confirmed.\n";
} else {
    echo "Warning: Theme may not have been activated properly.\n";
    echo "Current theme: " . $current_theme->name . "\n";
}
?>