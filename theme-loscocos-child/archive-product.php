<?php
/**
 * Product archive focused on the WooCommerce buying journey.
 *
 * @package Los_Cocos_Child
 */

defined('ABSPATH') || exit;

get_header('shop');

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
$archive_title = woocommerce_page_title(false);
$queried_object = get_queried_object();
$archive_description = is_product_taxonomy() ? term_description() : '';
$product_total = (int) wc_get_loop_prop('total');
$categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'parent' => 0,
    'exclude' => array((int) get_option('default_product_cat')),
));

$active_categories = ($categories && !is_wp_error($categories)) ? count($categories) : 0;

$get_featured_shop_products = static function () {
    $query_sets = array(
        array(
            'status' => 'publish',
            'limit' => 3,
            'featured' => true,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'return' => 'objects',
        ),
        array(
            'status' => 'publish',
            'limit' => 3,
            'on_sale' => true,
            'stock_status' => 'instock',
            'orderby' => 'date',
            'order' => 'DESC',
            'return' => 'objects',
        ),
        array(
            'status' => 'publish',
            'limit' => 3,
            'stock_status' => 'instock',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'return' => 'objects',
        ),
    );

    $selected = array();
    $seen_ids = array();

    foreach ($query_sets as $query_args) {
        $products = wc_get_products($query_args);

        foreach ($products as $candidate) {
            if (!$candidate instanceof WC_Product || !$candidate->is_visible()) {
                continue;
            }

            $candidate_id = $candidate->get_id();
            if (isset($seen_ids[$candidate_id])) {
                continue;
            }

            $selected[] = $candidate;
            $seen_ids[$candidate_id] = true;

            if (count($selected) >= 3) {
                break 2;
            }
        }
    }

    return $selected;
};

$featured_products = $get_featured_shop_products();

$get_product_display_category = static function ($product_id) {
    $product_categories = wp_get_post_terms($product_id, 'product_cat');
    $default_category_id = (int) get_option('default_product_cat');

    if ($product_categories && !is_wp_error($product_categories)) {
        foreach ($product_categories as $product_category) {
            if ((int) $product_category->term_id !== $default_category_id && 'sin-categorizar' !== $product_category->slug) {
                return $product_category->name;
            }
        }
    }

    return 'Vivero Los Cocos';
};
?>

<main id="primary" class="lc-shop-page site-main bg-cream-light min-h-screen">
    <section class="lc-shop-hero bg-primary text-white pt-28 pb-12 md:pt-32 md:pb-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl">
                <p class="lc-shop-hero__eyebrow text-sm font-semibold uppercase text-secondary-light mb-4">
                    Tienda online de Vivero Los Cocos
                </p>
                <h1 class="lc-shop-hero__title text-4xl md:text-6xl font-serif font-bold leading-tight mb-5">
                    <?php echo esc_html($archive_title ?: 'Tienda'); ?>
                </h1>
                <p class="lc-shop-hero__lede text-lg md:text-xl text-white/85 max-w-3xl leading-relaxed">
                    <?php if ($archive_description) : ?>
                        <?php echo wp_kses_post(wp_strip_all_tags($archive_description)); ?>
                    <?php else : ?>
                        Plantas, macetas, sustratos e insumos seleccionados con compra directa, stock visible y entrega coordinada en Mendoza.
                    <?php endif; ?>
                </p>
            </div>

            <div class="lc-shop-hero-metrics grid grid-cols-1 md:grid-cols-3 gap-4 mt-10">
                <div class="lc-shop-hero-metric rounded-lg border border-white/15 bg-white/10 px-5 py-4">
                    <strong class="block text-white"><?php echo esc_html(number_format_i18n($product_total)); ?> productos</strong>
                    <span class="text-sm text-white/75">Catálogo visible para comprar o consultar hoy.</span>
                </div>
                <div class="lc-shop-hero-metric rounded-lg border border-white/15 bg-white/10 px-5 py-4">
                    <strong class="block text-white"><?php echo esc_html(number_format_i18n($active_categories)); ?> categorías activas</strong>
                    <span class="text-sm text-white/75">Plantas, macetas e insumos ordenados por rubro.</span>
                </div>
                <div class="lc-shop-hero-metric rounded-lg border border-white/15 bg-white/10 px-5 py-4">
                    <strong class="block text-white">Compra con asesoramiento</strong>
                    <span class="text-sm text-white/75">Stock visible y ayuda real antes de elegir.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="lc-shop-controls border-b border-neutral-200 bg-white">
        <div class="container mx-auto px-4 py-5">
            <div class="lc-category-strip flex flex-nowrap gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible">
                <a href="<?php echo esc_url($shop_url); ?>"
                   class="shrink-0 rounded-full border px-4 py-2 text-sm font-semibold transition-colors <?php echo is_shop() ? 'border-primary bg-primary text-white' : 'border-neutral-200 bg-white text-neutral-dark hover:border-primary hover:text-primary'; ?>">
                    Todos
                </a>
                <?php if ($categories && !is_wp_error($categories)) : ?>
                    <?php foreach ($categories as $category) : ?>
                        <?php
                        $active = isset($queried_object->term_id) && (int) $queried_object->term_id === (int) $category->term_id;
                        ?>
                        <a href="<?php echo esc_url(get_term_link($category)); ?>"
                           class="shrink-0 rounded-full border px-4 py-2 text-sm font-semibold transition-colors <?php echo $active ? 'border-primary bg-primary text-white' : 'border-neutral-200 bg-white text-neutral-dark hover:border-primary hover:text-primary'; ?>">
                            <?php echo esc_html($category->name); ?>
                            <span class="<?php echo $active ? 'text-white/75' : 'text-neutral-medium'; ?>">
                                <?php echo esc_html($category->count); ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="lc-shop-catalog container mx-auto px-4 py-10 md:py-14">
        <?php do_action('woocommerce_before_main_content'); ?>
        <?php woocommerce_output_all_notices(); ?>

        <?php if (woocommerce_product_loop()) : ?>
            <?php
            $show_ordering_form = wc_get_loop_prop('is_paginated') && woocommerce_products_will_display();
            $catalog_orderby_options = array();
            $orderby = '';

            if ($show_ordering_form) {
                $show_default_orderby = 'menu_order' === apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
                $catalog_orderby_options = apply_filters(
                    'woocommerce_catalog_orderby',
                    array(
                        'menu_order' => __('Orden recomendado', 'woocommerce'),
                        'popularity' => __('Más vendidos', 'woocommerce'),
                        'rating'     => __('Mejor valorados', 'woocommerce'),
                        'date'       => __('Más recientes', 'woocommerce'),
                        'price'      => __('Precio: menor a mayor', 'woocommerce'),
                        'price-desc' => __('Precio: mayor a menor', 'woocommerce'),
                    )
                );

                $default_orderby = wc_get_loop_prop('is_search') ? 'relevance' : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby', ''));
                $orderby = isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

                if (wc_get_loop_prop('is_search')) {
                    $catalog_orderby_options = array_merge(array('relevance' => __('Relevancia', 'woocommerce')), $catalog_orderby_options);
                    unset($catalog_orderby_options['menu_order']);
                }

                if (!$show_default_orderby) {
                    unset($catalog_orderby_options['menu_order']);
                }

                if ('no' === get_option('woocommerce_enable_review_rating')) {
                    unset($catalog_orderby_options['rating']);
                }

                $valid_orderby_keys = array_keys($catalog_orderby_options);

                if (is_array($orderby)) {
                    $orderby_values = array_filter($orderby, 'is_scalar');
                    $orderby = current(array_intersect($orderby_values, $valid_orderby_keys));
                }

                if (!is_string($orderby) && !is_int($orderby)) {
                    $orderby = '';
                }

                if ('' === $orderby || !array_key_exists($orderby, $catalog_orderby_options)) {
                    $orderby = current($valid_orderby_keys);
                }
            }
            ?>
            <div class="lc-shop-toolbar">
                <div>
                    <p class="lc-shop-toolbar__eyebrow">Catálogo online</p>
                    <p class="lc-shop-toolbar__count">
                        <?php echo esc_html(number_format_i18n($product_total)); ?> productos disponibles
                    </p>
                </div>
                <div class="lc-shop-toolbar__ordering">
                    <?php if ($show_ordering_form) : ?>
                        <form class="woocommerce-ordering" method="get">
                            <div class="relative">
                                <select name="orderby" class="block min-h-[44px] w-full appearance-none rounded-full border border-neutral-200 bg-white py-2 pl-4 pr-10 text-sm font-semibold text-neutral-dark focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" aria-label="<?php esc_attr_e('Shop order', 'woocommerce'); ?>">
                                    <?php foreach ($catalog_orderby_options as $id => $name) : ?>
                                        <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-neutral-medium">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                            <input type="hidden" name="paged" value="1" />
                            <?php wc_query_string_form_fields(null, array('orderby', 'submit', 'paged', 'product-page')); ?>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($featured_products)) : ?>
                <section class="lc-featured-products" aria-labelledby="lc-featured-products-title">
                    <div class="lc-featured-products__header">
                        <div>
                            <p class="lc-featured-products__eyebrow">Selección del vivero</p>
                            <h2 id="lc-featured-products-title">Destacados del vivero</h2>
                        </div>
                        <a href="#catalogo" class="lc-featured-products__link">Ver catálogo completo</a>
                    </div>
                    <div class="lc-featured-products__grid">
                        <?php foreach ($featured_products as $featured_product) : ?>
                            <?php
                            $featured_id = $featured_product->get_id();
                            $featured_url = get_permalink($featured_id);
                            $featured_image = $featured_product->get_image('woocommerce_thumbnail', array(
                                'class' => 'lc-featured-product-card__image',
                                'loading' => 'lazy',
                            ));
                            $featured_category = $get_product_display_category($featured_id);
                            ?>
                            <article class="lc-featured-product-card">
                                <a href="<?php echo esc_url($featured_url); ?>" class="lc-featured-product-card__media">
                                    <?php echo wp_kses_post($featured_image); ?>
                                </a>
                                <div class="lc-featured-product-card__body">
                                    <p class="lc-featured-product-card__category"><?php echo esc_html($featured_category); ?></p>
                                    <h3>
                                        <a href="<?php echo esc_url($featured_url); ?>">
                                            <?php echo esc_html($featured_product->get_name()); ?>
                                        </a>
                                    </h3>
                                    <div class="lc-featured-product-card__meta">
                                        <span><?php echo wp_kses_post($featured_product->get_price_html()); ?></span>
                                        <?php if ($featured_product->is_in_stock()) : ?>
                                            <small>En stock</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <ul id="catalogo" class="products columns-<?php echo esc_attr(wc_get_loop_prop('columns')); ?> grid grid-cols-1 gap-6 lg:grid-cols-3 xl:grid-cols-4">

            <?php if (wc_get_loop_prop('total')) : ?>
                <?php while (have_posts()) : ?>
                    <?php the_post(); ?>
                    <?php wc_get_template_part('content', 'product'); ?>
                <?php endwhile; ?>
            <?php endif; ?>

            </ul>
            <?php do_action('woocommerce_after_shop_loop'); ?>
        <?php else : ?>
            <div class="rounded-lg border border-neutral-200 bg-white px-6 py-16 text-center">
                <h2 class="text-2xl font-serif font-bold text-primary-dark mb-3">No encontramos productos para esta búsqueda.</h2>
                <p class="text-neutral-medium mb-6">Probá con otra categoría o volvé al catálogo completo.</p>
                <a href="<?php echo esc_url($shop_url); ?>" class="inline-flex items-center justify-center rounded-full bg-primary px-6 py-3 font-semibold text-white hover:bg-primary-dark">
                    Ver tienda completa
                </a>
            </div>
            <?php do_action('woocommerce_no_products_found'); ?>
        <?php endif; ?>

        <?php do_action('woocommerce_after_main_content'); ?>
    </section>
</main>

<?php
get_footer('shop');
