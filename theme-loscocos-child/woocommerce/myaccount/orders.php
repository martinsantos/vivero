<?php
/**
 * My Orders
 *
 * Shows recent orders on the account page
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

$customer_orders = get_query_var('customer_orders');
$has_orders      = !empty($customer_orders) && $customer_orders->have_posts();

?>

<div class="space-y-6">
    <?php if ($has_orders) : ?>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php esc_html_e('Order', 'woocommerce'); ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php esc_html_e('Date', 'woocommerce'); ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php esc_html_e('Status', 'woocommerce'); ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php esc_html_e('Total', 'woocommerce'); ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php esc_html_e('Actions', 'woocommerce'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    foreach ($customer_orders->posts as $customer_order) {
                        $order      = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                        $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                        $order_date = $order->get_date_created();
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="text-green-600 hover:text-green-800">
                                    <?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <time datetime="<?php echo esc_attr($order_date->date('c')); ?>">
                                    <?php echo esc_html(wc_format_datetime($order_date)); ?>
                                </time>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php
                                /* translators: 1: formatted order total 2: total order items */
                                echo wp_kses_post(sprintf(_n('%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce'), $order->get_formatted_order_total(), $item_count));
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <?php
                                $actions = wc_get_account_orders_actions($order);

                                if (!empty($actions)) {
                                    foreach ($actions as $key => $action) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                        echo '<a href="' . esc_url($action['url']) . '" class="' . esc_attr(isset($action['class']) ? $action['class'] : '') . ' text-green-600 hover:text-green-800 ml-4">' . esc_html($action['name']) . '</a>';
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if (1 < $customer_orders->max_num_pages) : ?>
            <div class="flex justify-between items-center mt-8">
                <?php if (1 !== $current_page) : ?>
                    <a class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>">
                        <?php esc_html_e('Previous', 'woocommerce'); ?>
                    </a>
                <?php endif; ?>

                <?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
                    <a class="ml-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>">
                        <?php esc_html_e('Next', 'woocommerce'); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900"><?php esc_html_e('No orders', 'woocommerce'); ?></h3>
            <p class="mt-1 text-sm text-gray-500"><?php esc_html_e('You haven\'t placed any orders yet.', 'woocommerce'); ?></p>
            <div class="mt-6">
                <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <?php esc_html_e('Browse products', 'woocommerce'); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?>
