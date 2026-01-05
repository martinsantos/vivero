<?php
/**
 * My Downloads - Deprecated
 *
 * @package WooCommerce\Templates
 * @version 3.3.0
 */

defined('ABSPATH') || exit;

$downloads     = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $downloads;

do_action('woocommerce_before_account_downloads', $has_downloads); ?>

<div class="space-y-6">
    <?php if ($has_downloads) : ?>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <?php esc_html_e('Available downloads', 'woocommerce'); ?>
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    <?php esc_html_e('Your available downloads are shown below.', 'woocommerce'); ?>
                </p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?php esc_html_e('Product', 'woocommerce'); ?>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?php esc_html_e('Downloads', 'woocommerce'); ?>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?php esc_html_e('Expires', 'woocommerce'); ?>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?php esc_html_e('Download', 'woocommerce'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($downloads as $download) : ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <?php echo wp_get_attachment_image($download['product_image_id'], 'thumbnail', false, array('class' => 'h-10 w-10 rounded')); ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo esc_html($download['product_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo esc_html($download['download_name']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo esc_html($download['downloads_remaining']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo esc_html($download['access_expires']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php
                                    $download_url = add_query_arg(
                                        array(
                                            'download_file' => $download['product_id'],
                                            'order'        => $download['order_key'],
                                            'email'        => rawurlencode($download['user_email']),
                                            'key'          => $download['download_id'],
                                        ),
                                        home_url('/')
                                    );
                                    ?>
                                    <a href="<?php echo esc_url($download_url); ?>" class="text-green-600 hover:text-green-800">
                                        <?php esc_html_e('Download', 'woocommerce'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else : ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900"><?php esc_html_e('No downloads available yet.', 'woocommerce'); ?></h3>
            <p class="mt-1 text-sm text-gray-500">
                <?php esc_html_e('No downloads available yet.', 'woocommerce'); ?>
            </p>
            <div class="mt-6">
                <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <?php esc_html_e('Browse products', 'woocommerce'); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php do_action('woocommerce_after_account_downloads', $has_downloads); ?>
