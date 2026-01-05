<?php
/**
 * Pagination - Show numbered pagination for catalog pages
 *
 * @package WooCommerce\Templates
 * @version 3.3.1
 */

if (!defined('ABSPATH')) {
    exit;
}

$total   = isset($total) ? $total : wc_get_loop_prop('total_pages');
$current = isset($current) ? $current : wc_get_loop_prop('current_page');
$base    = isset($base) ? $base : esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false))));
$format  = isset($format) ? $format : '';

if ($total <= 1) {
    return;
}
?>
<nav class="woocommerce-pagination flex items-center justify-between border-t border-gray-200 px-4 sm:px-0 mt-8">
    <div class="-mt-px w-0 flex-1 flex">
        <?php if (1 !== $current) : ?>
            <a href="<?php echo esc_url(apply_filters('woocommerce_pagination_prev_link', remove_query_arg('add-to-cart', get_pagenum_link($current - 1, false)), $current)); ?>" class="inline-flex items-center pt-4 pr-1 text-sm font-medium text-gray-500 hover:text-gray-700 border-t-2 border-transparent hover:border-gray-300">
                <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <?php esc_html_e('Previous', 'woocommerce'); ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="hidden md:-mt-px md:flex">
        <?php
        echo paginate_links(
            apply_filters(
                'woocommerce_pagination_args',
                array( // WPCS: XSS ok.
                    'base'      => $base,
                    'format'    => $format,
                    'add_args'  => false,
                    'current'   => max(1, $current),
                    'total'     => $total,
                    'prev_text' => '',
                    'next_text' => '',
                    'type'      => 'list',
                    'end_size'  => 3,
                    'mid_size'  => 3,
                )
            )
        );
        ?>
    </div>

    <div class="-mt-px w-0 flex-1 flex justify-end">
        <?php if ($current < $total) : ?>
            <a href="<?php echo esc_url(apply_filters('woocommerce_pagination_next_link', remove_query_arg('add-to-cart', get_pagenum_link($current + 1, false)), $current, $total)); ?>" class="inline-flex items-center pt-4 pl-1 text-sm font-medium text-gray-500 hover:text-gray-700 border-t-2 border-transparent hover:border-gray-300">
                <?php esc_html_e('Next', 'woocommerce'); ?>
                <svg class="ml-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </a>
        <?php endif; ?>
    </div>
</nav>

<?php if (wp_doing_ajax()) : ?>
    <script>
        jQuery(document.body).on('wc_products_pagination', 'nav.woocommerce-pagination', function() {
            jQuery(this).closest('.products').find('.woocommerce-pagination').remove();
            jQuery(this).closest('.products').append(jQuery(this));
        });
    </script>
<?php endif; ?>
