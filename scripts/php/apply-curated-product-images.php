<?php
/**
 * Upload curated product images and assign them as featured images.
 *
 * Usage:
 *   wp eval-file /tmp/apply-curated-product-images.php /tmp/curated-accessory-manifest.json /tmp/curated-product-images --allow-root
 */

$manifest_file = $args[0] ?? '';
$base_dir = rtrim($args[1] ?? '', '/');

if (! file_exists($manifest_file) || ! is_dir($base_dir)) {
    fwrite(STDERR, "Usage: wp eval-file apply-curated-product-images.php manifest.json image_dir --allow-root\n");
    exit(2);
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$rows = json_decode(file_get_contents($manifest_file), true);
if (! is_array($rows)) {
    fwrite(STDERR, "Invalid manifest JSON\n");
    exit(2);
}

$updated = 0;
$failed = 0;
$results = [];

foreach ($rows as $row) {
    $product_id = isset($row['product_id']) ? (int) $row['product_id'] : 0;
    $filename = isset($row['file']) ? basename((string) $row['file']) : '';
    $alt = isset($row['alt']) ? trim((string) $row['alt']) : '';
    $source = $base_dir . '/' . $filename;

    if ($product_id <= 0 || $filename === '' || ! file_exists($source)) {
        $failed++;
        $results[] = ['product_id' => $product_id, 'status' => 'failed', 'reason' => 'missing input'];
        continue;
    }

    $tmp = wp_tempnam($filename);
    if (! $tmp || ! copy($source, $tmp)) {
        $failed++;
        $results[] = ['product_id' => $product_id, 'status' => 'failed', 'reason' => 'copy failed'];
        continue;
    }

    $file_array = [
        'name' => $filename,
        'tmp_name' => $tmp,
    ];

    $attachment_id = media_handle_sideload($file_array, $product_id, $alt);
    if (is_wp_error($attachment_id)) {
        @unlink($tmp);
        $failed++;
        $results[] = ['product_id' => $product_id, 'status' => 'failed', 'reason' => $attachment_id->get_error_message()];
        continue;
    }

    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt);
    set_post_thumbnail($product_id, $attachment_id);
    $updated++;
    $results[] = ['product_id' => $product_id, 'status' => 'updated', 'attachment_id' => $attachment_id];
}

echo wp_json_encode([
    'updated' => $updated,
    'failed' => $failed,
    'results' => $results,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
