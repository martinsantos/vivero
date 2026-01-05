<?php
/**
 * Product Tag Cloud Widget
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

echo '<div class="tagcloud flex flex-wrap gap-2">';

$tags = get_terms(array(
    'taxonomy'   => 'product_tag',
    'number'     => $args['number'],
    'orderby'    => $args['orderby'],
    'order'      => $args['order'],
    'hide_empty' => $args['hide_empty'] ? 1 : 0,
));

if (!empty($tags) && !is_wp_error($tags)) {
    $largest  = 1;
    $smallest = 1;
    $unit     = 'px';
    
    foreach ($tags as $tag) {
        $count = $tag->count;
        $link  = get_term_link($tag, 'product_tag');
        
        if (is_wp_error($link)) {
            continue;
        }
        
        // Calculate font size based on count
        $size = $smallest + ((1 - (($largest - $tag->count) / ($largest - $smallest))) * 0.5);
        $size = number_format($size, 2);
        
        echo sprintf(
            '<a href="%s" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-colors duration-200 %s" style="font-size: %s%s" aria-label="%s">%s%s</a>',
            esc_url($link),
            'bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-700',
            $size,
            $unit,
            /* translators: %s: Tag name */
            esc_attr(sprintf(__('View products tagged %s', 'woocommerce'), $tag->name)),
            esc_html($tag->name),
            $args['count'] ? ' <span class="ml-1 text-xs text-gray-500">(' . esc_html($count) . ')</span>' : ''
        );
    }
} else {
    echo '<span class="text-sm text-gray-500">' . esc_html__('No product tags found.', 'woocommerce') . '</span>';
}

echo '</div>';

if (!empty($args['after_widget'])) {
    echo $args['after_widget'];
}
?>
