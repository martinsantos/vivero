<?php
/**
 * Order details
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

$notes = $order->get_customer_order_notes();
?>

<div class="space-y-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php
                /* translators: 1: order number */
                printf(esc_html__('Order #%s', 'woocommerce'), esc_html($order->get_order_number()));
                ?>
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                <?php echo esc_html(wc_format_datetime($order->get_date_created())); ?>
            </p>
        </div>
        
        <div class="border-t border-gray-200">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        <?php esc_html_e('Status', 'woocommerce'); ?>
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                    </dd>
                </div>
                
                <?php if ($order->get_payment_method_title()) : ?>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            <?php esc_html_e('Payment method', 'woocommerce'); ?>
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <?php echo wp_kses_post($order->get_payment_method_title()); ?>
                        </dd>
                    </div>
                <?php endif; ?>
                
                <?php if ($order->get_customer_note()) : ?>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            <?php esc_html_e('Note', 'woocommerce'); ?>
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <?php echo wp_kses_post(nl2br(wptexturize($order->get_customer_note()))); ?>
                        </dd>
                    </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php esc_html_e('Order details', 'woocommerce'); ?>
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php esc_html_e('Product', 'woocommerce'); ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php esc_html_e('Total', 'woocommerce'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $order_items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
                    
                    foreach ($order_items as $item_id => $item) {
                        $product = $item->get_product();
                        $is_visible = $product && $product->is_visible();
                        $product_permalink = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);
                        
                        do_action('woocommerce_order_item_' . $item->get_type() . '_html', $item_id, $item, $order);
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <?php echo $product ? $product->get_image('thumbnail') : ''; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php
                                            echo wp_kses_post(apply_filters('woocommerce_order_item_name', $product_permalink ? sprintf('<a href="%s">%s</a>', $product_permalink, $item->get_name()) : $item->get_name(), $item, $is_visible));
                                            ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo wp_kses_post(apply_filters('woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&times; %s', $item->get_quantity()) . '</strong>', $item)); ?>
                                        </div>
                                        <?php
                                        do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);
                                        wc_display_item_meta($item);
                                        do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);
                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                <?php echo $order->get_formatted_line_subtotal($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <?php
                    $totals = $order->get_order_item_totals();
                    
                    if ($totals) {
                        $i = 0;
                        foreach ($totals as $key => $total) {
                            $i++;
                            ?>
                            <tr>
                                <th scope="row" colspan="1" class="px-6 py-3 text-right text-sm font-medium text-gray-500">
                                    <?php echo esc_html($total['label']); ?>
                                </th>
                                <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                    <?php echo wp_kses_post($total['value']); ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if (!empty($order->get_billing_email()) || !empty($order->get_billing_phone())) : ?>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <?php esc_html_e('Customer details', 'woocommerce'); ?>
                </h3>
            </div>
            
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <?php if ($order->get_billing_email()) : ?>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">
                                <?php esc_html_e('Email', 'woocommerce'); ?>
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?php echo esc_html($order->get_billing_email()); ?>
                            </dd>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($order->get_billing_phone()) : ?>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">
                                <?php esc_html_e('Phone', 'woocommerce'); ?>
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?php echo esc_html($order->get_billing_phone()); ?>
                            </dd>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($order->get_billing_address_1() || $order->get_billing_city() || $order->get_billing_postcode() || $order->get_billing_country()) : ?>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">
                                <?php esc_html_e('Billing address', 'woocommerce'); ?>
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?php echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'))); ?>
                            </dd>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($order->get_shipping_address_1() || $order->get_shipping_city() || $order->get_shipping_postcode() || $order->get_shipping_country()) : ?>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">
                                <?php esc_html_e('Shipping address', 'woocommerce'); ?>
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?php echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce'))); ?>
                            </dd>
                        </div>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($notes)) : ?>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <?php esc_html_e('Order updates', 'woocommerce'); ?>
                </h3>
            </div>
            
            <div class="px-4 py-5 sm:p-6">
                <div class="flow-root">
                    <ul class="-mb-8">
                        <?php foreach ($notes as $note) : ?>
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    <?php echo wp_kses_post($note->comment_content); ?>
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="<?php echo esc_attr(date('Y-m-d H:i:s', strtotime($note->comment_date))); ?>">
                                                    <?php echo esc_html(date_i18n('F j, Y g:i a', strtotime($note->comment_date))); ?>
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php do_action('woocommerce_view_order', $order->get_id()); ?>
