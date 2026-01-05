<?php
/**
 * Order details
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

$order = wc_get_order($order_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if (!$order) {
    return;
}

$order_items           = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
$show_purchase_note    = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', array('completed', 'processing')));
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ($show_downloads) {
    wc_get_template(
        'order/order-downloads.php',
        array(
            'downloads'  => $downloads,
            'show_title' => true,
        )
    );
}
?>

<div class="space-y-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php esc_html_e('Order details', 'woocommerce'); ?>
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                <?php
                printf(
                    /* translators: 1: order number 2: order date 3: order status */
                    esc_html__('Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce'),
                    '<span class="font-medium">' . $order->get_order_number() . '</span>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    '<time datetime="' . esc_attr($order->get_date_created()->date('c')) . '">' . esc_html(wc_format_datetime($order->get_date_created())) . '</time>',
                    '<span class="text-green-600">' . esc_html(wc_get_order_status_name($order->get_status())) . '</span>'
                );
                ?>
            </p>
        </div>

        <div class="px-4 py-5 sm:p-6">
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
                        do_action('woocommerce_order_details_before_order_table_items', $order);

                        foreach ($order_items as $item_id => $item) {
                            $product = $item->get_product();

                            wc_get_template(
                                'order/order-details-item.php',
                                array(
                                    'order'              => $order,
                                    'item_id'            => $item_id,
                                    'item'               => $item,
                                    'show_purchase_note' => $show_purchase_note,
                                    'purchase_note'      => $product ? $product->get_purchase_note() : '',
                                    'product'            => $product,
                                )
                            );
                        }

                        do_action('woocommerce_order_details_after_order_table_items', $order);
                        ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <?php
                        $item_totals = $order->get_order_item_totals();

                        if ($item_totals) {
                            $i = 0;
                            foreach ($item_totals as $key => $total) {
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
                        <?php if ($order->get_customer_note()) : ?>
                            <tr>
                                <th class="px-6 py-3 text-sm font-medium text-gray-500">
                                    <?php esc_html_e('Note:', 'woocommerce'); ?>
                                </th>
                                <td class="px-6 py-3 text-sm text-gray-900">
                                    <?php echo wp_kses_post(nl2br(wptexturize($order->get_customer_note()))); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <?php do_action('woocommerce_order_details_after_order_table', $order); ?>

    <?php if ($show_customer_details) : ?>
        <?php wc_get_template('order/order-details-customer.php', array('order' => $order)); ?>
    <?php endif; ?>
</div>
