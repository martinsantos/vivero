<?php
/**
 * Audit public WooCommerce product copy for internal review phrases.
 *
 * Usage:
 *   wp eval-file audit-product-content-phrases.php --allow-root
 */

$phrases = [
    'La ficha evita afirmar',
    'evita afirmar',
    'altura final',
    'floración exacta',
    'floracion exacta',
    'fuente del catálogo',
    'fuente del catalogo',
    'dato no está verificado',
    'dato no esta verificado',
    'no está verificado',
    'no esta verificado',
];

global $wpdb;

$results = [];
foreach ($phrases as $phrase) {
    $like = '%' . $wpdb->esc_like($phrase) . '%';
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT ID, post_title, post_name
             FROM {$wpdb->posts}
             WHERE post_type = 'product'
               AND post_status = 'publish'
               AND (post_content LIKE %s OR post_excerpt LIKE %s)
             ORDER BY ID ASC
             LIMIT 20",
            $like,
            $like
        ),
        ARRAY_A
    );

    $count = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*)
             FROM {$wpdb->posts}
             WHERE post_type = 'product'
               AND post_status = 'publish'
               AND (post_content LIKE %s OR post_excerpt LIKE %s)",
            $like,
            $like
        )
    );

    $results[] = [
        'phrase' => $phrase,
        'count' => $count,
        'sample' => array_map(
            static function ($row) {
                return [
                    'id' => (int) $row['ID'],
                    'title' => $row['post_title'],
                    'url' => get_permalink((int) $row['ID']),
                ];
            },
            $rows
        ),
    ];
}

$plant_ids = get_posts([
    'post_type' => 'product',
    'post_status' => 'publish',
    'fields' => 'ids',
    'posts_per_page' => -1,
    'tax_query' => [
        [
            'taxonomy' => 'product_cat',
            'field' => 'name',
            'terms' => ['PLANTAS'],
        ],
    ],
]);

echo wp_json_encode([
    'audited_at' => gmdate('c'),
    'published_products' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish'"),
    'plant_products' => count($plant_ids),
    'phrase_results' => $results,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
