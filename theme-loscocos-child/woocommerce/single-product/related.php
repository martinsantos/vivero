<?php
/**
 * Related Products
 *
 * @package WooCommerce\Templates
 * @version 3.9.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if ($related_products): ?>

    <section class="related products">
        <h2><?php esc_html_e('Productos relacionados', 'woocommerce'); ?></h2>

        <ul class="products-grid">
            <?php foreach ($related_products as $related_product): ?>

                <?php
                $post_object = get_post($related_product->get_id());

                setup_postdata($GLOBALS['post'] =& $post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
                ?>

                <li <?php wc_product_class('product-card', $related_product); ?>>
                    <a href="<?php echo esc_url($related_product->get_permalink()); ?>" class="product-link">
                        <div class="product-image">
                            <?php echo $related_product->get_image('woocommerce_thumbnail'); ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php echo esc_html($related_product->get_name()); ?></h3>
                            <div class="product-price">
                                <?php echo $related_product->get_price_html(); ?>
                            </div>
                        </div>
                    </a>
                    <div class="product-actions">
                        <?php woocommerce_template_loop_add_to_cart(); ?>
                    </div>
                </li>

            <?php endforeach; ?>
        </ul>
    </section>

    <?php
endif;

wp_reset_postdata();
