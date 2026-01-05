<?php
/**
 * Loop Start
 *
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<ul class="products columns-<?php echo esc_attr(wc_get_loop_prop('columns')); ?> grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
