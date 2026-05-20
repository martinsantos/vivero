<?php
/**
 * Apply product content updates from a generated manifest.
 *
 * Usage:
 *   wp eval-file apply-product-content-manifest.php manifest.json --allow-root
 *   wp eval-file apply-product-content-manifest.php manifest.json apply --allow-root
 */

$manifest_file = $args[0] ?? '';
$apply = in_array('--apply', $args, true) || in_array('apply', $args, true);

if (! $manifest_file || ! file_exists($manifest_file)) {
    fwrite(STDERR, "Usage: wp eval-file apply-product-content-manifest.php manifest.json [--apply] --allow-root\n");
    exit(2);
}

$payload = json_decode(file_get_contents($manifest_file), true);
if (! is_array($payload) || ! isset($payload['updates']) || ! is_array($payload['updates'])) {
    fwrite(STDERR, "Invalid content manifest\n");
    exit(2);
}

function loscocos_content_term_id($taxonomy, $name) {
    $existing = get_term_by('name', $name, $taxonomy);
    if ($existing && ! is_wp_error($existing)) {
        return (int) $existing->term_id;
    }

    $created = wp_insert_term($name, $taxonomy);
    if (is_wp_error($created)) {
        return 0;
    }

    return (int) $created['term_id'];
}

function loscocos_category_names_for_hint($hint) {
    $hint = strtolower((string) $hint);
    if ($hint === 'macetas') {
        return ['MACETAS'];
    }
    if ($hint === 'plantas') {
        return ['PLANTAS'];
    }
    if ($hint === 'fertilizante') {
        return ['FERTILIZANTES'];
    }
    if ($hint === 'fungicida') {
        return ['FUNGUICIDAS'];
    }
    if ($hint === 'herbicida') {
        return ['HERBICIDAS'];
    }
    if ($hint === 'molusquicida') {
        return ['MOLUSQUICIDAS'];
    }
    if ($hint === 'insecticida' || $hint === 'insumos') {
        return ['INSECTICIDAS'];
    }
    return [];
}

$updated = 0;
$previewed = 0;
$failed = 0;
$results = [];

foreach ($payload['updates'] as $update) {
    $product_id = isset($update['product_id']) ? (int) $update['product_id'] : 0;
    $product = $product_id ? wc_get_product($product_id) : null;

    if (! $product) {
        $failed++;
        $results[] = ['product_id' => $product_id, 'status' => 'failed', 'reason' => 'product not found'];
        continue;
    }

    $change_summary = [
        'product_name' => isset($update['product_name']),
        'short_description' => isset($update['short_description']),
        'description' => isset($update['description']),
        'attributes' => count($update['attributes'] ?? []),
        'meta_data' => count($update['meta_data'] ?? []),
        'tags' => count($update['tags'] ?? []),
        'category_hint' => $update['category_hint'] ?? '',
    ];

    if (! $apply) {
        $previewed++;
        $results[] = ['product_id' => $product_id, 'status' => 'preview', 'changes' => $change_summary];
        continue;
    }

    try {
        if (isset($update['product_name'])) {
            $product->set_name(sanitize_text_field($update['product_name']));
        }
        if (isset($update['short_description'])) {
            $product->set_short_description(wp_kses_post($update['short_description']));
        }
        if (isset($update['description'])) {
            $product->set_description(wp_kses_post($update['description']));
        }

        if (! empty($update['attributes']) && is_array($update['attributes'])) {
            $attributes = [];
            foreach ($update['attributes'] as $index => $attr) {
                $name = sanitize_text_field($attr['name'] ?? '');
                $options = array_map('sanitize_text_field', (array) ($attr['options'] ?? []));
                if ($name === '' || empty($options)) {
                    continue;
                }
                $attribute = new WC_Product_Attribute();
                $attribute->set_id(0);
                $attribute->set_name($name);
                $attribute->set_options($options);
                $attribute->set_position($index);
                $attribute->set_visible(true);
                $attribute->set_variation(false);
                $attributes[] = $attribute;
            }
            if (! empty($attributes)) {
                $product->set_attributes($attributes);
            }
        }

        $product->save();

        if (! empty($update['category_hint'])) {
            $category_ids = [];
            foreach (loscocos_category_names_for_hint($update['category_hint']) as $category_name) {
                $term_id = loscocos_content_term_id('product_cat', $category_name);
                if ($term_id > 0) {
                    $category_ids[] = $term_id;
                }
            }
            if (! empty($category_ids)) {
                wp_set_object_terms($product_id, $category_ids, 'product_cat', false);
            }
        }

        if (! empty($update['tags']) && is_array($update['tags'])) {
            $tag_names = array_values(array_filter(array_map('sanitize_text_field', $update['tags'])));
            if (! empty($tag_names)) {
                wp_set_object_terms($product_id, $tag_names, 'product_tag', false);
            }
        }

        if (! empty($update['meta_data']) && is_array($update['meta_data'])) {
            foreach ($update['meta_data'] as $meta) {
                $key = sanitize_key($meta['key'] ?? '');
                if ($key === '') {
                    continue;
                }
                update_post_meta($product_id, $key, $meta['value'] ?? '');
            }
        }

        update_post_meta($product_id, '_loscocos_content_review_status', $update['review_status'] ?? 'Generated Review');
        update_post_meta($product_id, '_loscocos_content_source_policy', $update['source_policy'] ?? '');
        $updated++;
        $results[] = ['product_id' => $product_id, 'status' => 'updated', 'changes' => $change_summary];
    } catch (Throwable $e) {
        $failed++;
        $results[] = ['product_id' => $product_id, 'status' => 'failed', 'reason' => $e->getMessage()];
    }
}

echo wp_json_encode([
    'mode' => $apply ? 'apply' : 'preview',
    'updated' => $updated,
    'previewed' => $previewed,
    'failed' => $failed,
    'results' => $results,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
