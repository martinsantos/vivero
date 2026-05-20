<?php
/**
 * Export a few real product URLs per category for production QA.
 *
 * Usage:
 *   wp eval-file export-category-sample-urls.php --allow-root
 */

$categories = [
    'MACETAS',
    'PLANTAS',
    'INSECTICIDAS',
    'FERTILIZANTES',
    'HERBICIDAS',
    'FUNGUICIDAS',
    'MOLUSQUICIDAS',
];

foreach ($categories as $category_name) {
    $term = get_term_by('name', $category_name, 'product_cat');
    if (! $term || is_wp_error($term)) {
        continue;
    }

    $products = wc_get_products([
        'limit' => 3,
        'status' => 'publish',
        'category' => [$term->slug],
        'orderby' => 'date',
        'order' => 'DESC',
    ]);

    foreach ($products as $product) {
        echo implode("\t", [
            $category_name,
            $product->get_id(),
            $product->get_sku(),
            get_permalink($product->get_id()),
        ]) . "\n";
    }
}
