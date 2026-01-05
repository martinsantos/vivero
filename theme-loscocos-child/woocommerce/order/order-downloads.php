<?php
/**
 * Order Downloads.
 *
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;

$downloads = $args['downloads'];

if (!$downloads) {
    return;
}
?>

<div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            <?php esc_html_e('Download files', 'woocommerce'); ?>
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            <?php esc_html_e('Your downloadable products are listed below.', 'woocommerce'); ?>
        </p>
    </div>

    <div class="px-4 py-5 sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <?php foreach (wc_get_account_downloads_columns() as $column_id => $column_name) : ?>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?php echo esc_html($column_name); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($downloads as $download) : ?>
                        <tr class="hover:bg-gray-50">
                            <?php foreach (wc_get_account_downloads_columns() as $column_id => $column_name) : ?>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php
                                    if (has_action('woocommerce_account_downloads_column_' . $column_id)) {
                                        do_action('woocommerce_account_downloads_column_' . $column_id, $download);
                                    } else {
                                        switch ($column_id) {
                                            case 'download-product':
                                                if ($download['product_url']) {
                                                    echo '<a href="' . esc_url($download['product_url']) . '" class="text-green-600 hover:text-green-800">' . esc_html($download['product_name']) . '</a>';
                                                } else {
                                                    echo esc_html($download['product_name']);
                                                }
                                                break;
                                            case 'download-file':
                                                echo '<a href="' . esc_url($download['download_url']) . '" class="text-green-600 hover:text-green-800">' . esc_html($download['download_name']) . '</a>';
                                                break;
                                            case 'download-expires':
                                                if (!empty($download['access_expires'])) {
                                                    echo '<time datetime="' . esc_attr(date('Y-m-d', strtotime($download['access_expires']))) . '" title="' . esc_attr(strtotime($download['access_expires'])) . '">' . esc_html(apply_filters('woocommerce_account_downloads_column_download_expires', $download['access_expires'] === 'never' ? __('Never', 'woocommerce') : date_i18n(get_option('date_format'), strtotime($download['access_expires'])), $download)) . '</time>';
                                                } else {
                                                    esc_html_e('Never', 'woocommerce');
                                                }
                                                break;
                                            case 'download-remaining':
                                                echo is_numeric($download['downloads_remaining']) ? esc_html($download['downloads_remaining']) : esc_html__('&infin;', 'woocommerce');
                                                break;
                                            case 'download-actions':
                                                $actions = array(
                                                    'download' => array(
                                                        'url'  => $download['download_url'],
                                                        'name' => __('Download', 'woocommerce'),
                                                    ),
                                                );
                                                
                                                if ($actions = apply_filters('woocommerce_account_download_actions', $actions, $download)) {
                                                    foreach ($actions as $key => $action) {
                                                        echo '<a href="' . esc_url($action['url']) . '" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">' . esc_html($action['name']) . '</a>';
                                                    }
                                                }
                                                break;
                                        }
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
