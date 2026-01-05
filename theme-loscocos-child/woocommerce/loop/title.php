<?php
/**
 * Show the product title in the product loop.
 *
 * @package WooCommerce\Templates
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

echo '<h2 class="text-base font-medium text-gray-900 group-hover:text-green-600 transition-colors duration-200 line-clamp-2" title="' . get_the_title() . '">' . get_the_title() . '</h2>';
