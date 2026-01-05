<?php
/**
 * Shop breadcrumb
 *
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

if (!empty($breadcrumb)) {
    echo $wrap_before;

    foreach ($breadcrumb as $key => $crumb) {
        echo $before;

        if (!empty($crumb[1]) && sizeof($breadcrumb) !== $key + 1) {
            echo '<a href="' . esc_url($crumb[1]) . '" class="text-green-600 hover:text-green-800 transition-colors duration-200">' . esc_html($crumb[0]) . '</a>';
        } else {
            echo '<span class="text-gray-500">' . esc_html($crumb[0]) . '</span>';
        }

        echo $after;

        if (sizeof($breadcrumb) !== $key + 1) {
            echo '<span class="mx-2 text-gray-400">/</span>';
        }
    }

    echo $wrap_after;
}
?>
