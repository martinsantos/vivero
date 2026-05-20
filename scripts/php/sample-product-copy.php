<?php
/**
 * Print concise public-copy samples for selected product IDs.
 *
 * Usage:
 *   wp eval-file sample-product-copy.php 308 384 --allow-root
 */

$ids = array_values(array_filter(array_map('intval', $args ?? [])));
if (empty($ids)) {
    $ids = [308, 384, 416, 467, 548];
}

$samples = [];
foreach ($ids as $id) {
    $product = wc_get_product($id);
    if (! $product) {
        $samples[] = ['id' => $id, 'status' => 'missing'];
        continue;
    }

    $samples[] = [
        'id' => $id,
        'name' => $product->get_name(),
        'url' => get_permalink($id),
        'attributes' => array_values(array_map(
            static function ($attribute) {
                return $attribute->get_name();
            },
            $product->get_attributes()
        )),
        'short_description' => wp_strip_all_tags($product->get_short_description()),
        'description_excerpt' => mb_substr(wp_strip_all_tags($product->get_description()), 0, 420),
    ];
}

echo wp_json_encode($samples, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
