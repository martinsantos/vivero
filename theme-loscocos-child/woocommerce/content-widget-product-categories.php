<?php
/**
 * Product Categories Widget
 *
 * @package WooCommerce\Templates
 * @version 7.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!empty($args['before_widget'])) {
    echo $args['before_widget'];
}

if ($args['title']) {
    echo $args['before_title'] . apply_filters('widget_title', $args['title']) . $args['after_title'];
}

$list_class = 'space-y-2';
$list_class .= $args['hierarchical'] ? ' is-hierarchical' : '';
$list_class .= ' list-none';

$category_list = wp_list_categories(apply_filters('woocommerce_product_categories_widget_args', array(
    'taxonomy'          => 'product_cat',
    'show_option_none'  => esc_html__('No product categories exist.', 'woocommerce'),
    'echo'              => false,
    'title_li'          => null,
    'show_count'        => $args['count'],
    'hierarchical'      => $args['hierarchical'],
    'orderby'           => $args['orderby'],
    'walker'            => new WC_Product_Cat_List_Walker(),
)));

// Replace default list items with custom Tailwind classes
$category_list = str_replace('<li class="cat-item cat-item-', '<li class="flex items-center justify-between py-1 text-sm" data-cat-id="', $category_list);
$category_list = str_replace('<a href="', '<a class="text-gray-600 hover:text-green-600 transition-colors duration-200" href="', $category_list);
$category_list = str_replace('</a> (', '<span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-0.5 rounded-full">', $category_list);
$category_list = str_replace(')', '</span></a>', $category_list);
$category_list = str_replace('current-cat', 'font-medium text-green-600', $category_list);

echo '<ul class="' . esc_attr($list_class) . '">';

echo $category_list;

echo '</ul>';

if (!empty($args['after_widget'])) {
    echo $args['after_widget'];
}
?>
