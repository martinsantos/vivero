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
?>

<main id="primary" class="site-main bg-cream-light min-h-screen">
    <section class="bg-primary text-white pt-28 pb-12 md:pt-32 md:pb-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl">
                <p class="text-sm font-semibold uppercase tracking-widest text-secondary-light mb-4">
                    Tienda online de Vivero Los Cocos
                </p>
                <h1 class="text-4xl md:text-6xl font-serif font-bold leading-tight mb-5">
                    <?php echo esc_html($archive_title ?: 'Tienda'); ?>
                </h1>
                <p class="text-lg md:text-xl text-white/85 max-w-3xl leading-relaxed">
                    <?php if ($archive_description) : ?>
                        <?php echo wp_kses_post(wp_strip_all_tags($archive_description)); ?>
                    <?php else : ?>
                        Plantas, macetas, sustratos e insumos seleccionados con compra directa, stock visible y entrega coordinada en Mendoza.
                    <?php endif; ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-10">
                <div class="rounded-lg border border-white/15 bg-white/10 px-5 py-4">
                    <strong class="block text-white">Stock visible</strong>
                    <span class="text-sm text-white/75">Productos publicados desde WooCommerce.</span>
                </div>
                <div class="rounded-lg border border-white/15 bg-white/10 px-5 py-4">
                    <strong class="block text-white">Compra simple</strong>
                    <span class="text-sm text-white/75">Agregá al carrito o consultá la ficha.</span>
                </div>
                <div class="rounded-lg border border-white/15 bg-white/10 px-5 py-4">
                    <strong class="block text-white">Asesoramiento real</strong>
                    <span class="text-sm text-white/75">Te ayudamos a elegir bien antes de comprar.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-neutral-200 bg-white">
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

    <section class="container mx-auto px-4 py-10 md:py-14">
        <?php do_action('woocommerce_before_main_content'); ?>
        <?php woocommerce_output_all_notices(); ?>

        <?php if (woocommerce_product_loop()) : ?>
            <?php woocommerce_catalog_ordering(); ?>
            <ul class="products columns-<?php echo esc_attr(wc_get_loop_prop('columns')); ?> grid grid-cols-1 gap-6 lg:grid-cols-3 xl:grid-cols-4">

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
