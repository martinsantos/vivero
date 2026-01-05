<?php
define('WP_USE_THEMES', false);
require('./wp-load.php');
echo "WordPress loaded successfully.\n";
echo "PHP Version: " . phpversion() . "\n";
if (function_exists('WC')) {
    echo "WooCommerce is active. Version: " . WC()->version . "\n";
} else {
    echo "WooCommerce is NOT active.\n";
}
