<?php
/**
 * Order item details
 *
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;

$is_visible        = $product && $product->is_visible();
$product_permalink  = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);
$product_id         = $item->get_product_id();
$product_variation  = $item->get_variation_id() ? wc_get_product($item->get_variation_id()) : null;
$product_image      = $product ? $product->get_image('thumbnail') : '';
$product_name       = $product ? $product->get_name() : $item->get_name();
$product_quantity   = $item->get_quantity();
$product_price      = $order->get_formatted_line_subtotal($item);
$product_meta       = wc_display_item_meta($item, array('echo' => false));
?>

<tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order)); ?>">
    <td class="px-6 py-4">
        <div class="flex items-center">
            <?php if ($product_image) : ?>
                <div class="flex-shrink-0 h-16 w-16 rounded-md overflow-hidden bg-gray-100">
                    <?php echo $product_permalink ? sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $product_image) : $product_image; ?>
                </div>
            <?php endif; ?>
            
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">
                    <?php
                    echo $product_permalink ? sprintf(
                        '<a href="%s">%s</a>',
                        esc_url($product_permalink),
                        esc_html($product_name)
                    ) : esc_html($product_name);
                    ?>
                </div>
                
                <?php if ($product_meta) : ?>
                    <div class="mt-1 text-sm text-gray-500">
                        <?php echo wp_kses_post($product_meta); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($product && $product->get_sku()) : ?>
                    <div class="mt-1 text-sm text-gray-500">
                        <?php esc_html_e('SKU:', 'woocommerce'); ?> <?php echo esc_html($product->get_sku()); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($product_quantity > 1) : ?>
                    <div class="mt-1 text-sm text-gray-500">
                        <?php echo esc_html__('Quantity:', 'woocommerce') . ' ' . esc_html($product_quantity); ?>
                    </div>
                <?php endif; ?>
                
                <?php
                if ($show_purchase_note && $purchase_note) {
                    echo '<div class="mt-2 text-sm text-gray-500">' . wp_kses_post($purchase_note) . '</div>';
                }
                ?>
            </div>
        </div>
    </td>
    
    <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
        <?php echo $product_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </td>
</tr>

<?php if ($show_purchase_note && $purchase_note) : ?>
    <tr class="woocommerce-table__product-purchase-note product-purchase-note">
        <td colspan="2" class="px-6 py-4">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <?php echo wp_kses_post($purchase_note); ?>
                        </p>
                    </div>
                </div>
            </div>
        </td>
    </tr>
<?php endif; ?>
