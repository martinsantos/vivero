<?php
/**
 * Commerce-first front page.
 *
 * @package Los_Cocos_Child
 */

get_header();

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/tienda/');
$whatsapp_url = 'https://wa.me/5402614399025';
$hero_image_url = 'https://viveroloscocos.com.ar/wp-content/uploads/2025/09/monstera-deliciosa-potted-plant-1-1024x1024.webp';

$primary_categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'parent' => 0,
    'number' => 6,
    'orderby' => 'count',
    'order' => 'DESC',
    'exclude' => array((int) get_option('default_product_cat')),
));

if (!function_exists('loscocos_front_category_image')) {
    /**
     * Return an image element that visually represents a product category.
     */
    function loscocos_front_category_image($category) {
        if (!$category || is_wp_error($category) || !function_exists('wc_get_products')) {
            return '';
        }

        $representative_products = array(
            'macetas' => 338,
            'plantas' => 467,
            'insecticidas' => 61122,
            'fertilizantes' => 61097,
            'herbicidas' => 61103,
            'funguicidas' => 61099,
        );

        if (isset($representative_products[$category->slug])) {
            $representative = wc_get_product($representative_products[$category->slug]);
            if ($representative instanceof WC_Product && $representative->get_image_id()) {
                return wp_get_attachment_image($representative->get_image_id(), 'large', false, array(
                    'class' => 'lc-home-category-card__image',
                    'loading' => 'lazy',
                    'alt' => sprintf(__('%1$s en Vivero Los Cocos', 'loscocos-child'), $category->name),
                ));
            }
        }

        $thumbnail_id = (int) get_term_meta($category->term_id, 'thumbnail_id', true);
        if ($thumbnail_id > 0) {
            return wp_get_attachment_image($thumbnail_id, 'large', false, array(
                'class' => 'lc-home-category-card__image',
                'loading' => 'lazy',
                'alt' => sprintf(__('Categoría %s', 'loscocos-child'), $category->name),
            ));
        }

        $products = wc_get_products(array(
            'status' => 'publish',
            'limit' => 12,
            'category' => array($category->slug),
            'orderby' => 'date',
            'order' => 'DESC',
        ));

        foreach ($products as $product) {
            if (!$product instanceof WC_Product || !$product->get_image_id()) {
                continue;
            }

            return wp_get_attachment_image($product->get_image_id(), 'large', false, array(
                'class' => 'lc-home-category-card__image',
                'loading' => 'lazy',
                'alt' => sprintf(__('%1$s en Vivero Los Cocos', 'loscocos-child'), $category->name),
            ));
        }

        return '<img src="' . esc_url(LOSCOCOS_CHILD_URI . '/assets/images/placeholder.svg') . '" class="lc-home-category-card__image" loading="lazy" alt="' . esc_attr($category->name) . '">';
    }
}

$featured_products = function_exists('wc_get_products') ? wc_get_products(array(
    'status' => 'publish',
    'limit' => 8,
    'featured' => true,
    'orderby' => 'date',
    'order' => 'DESC',
)) : array();

if (empty($featured_products) && function_exists('wc_get_products')) {
    $featured_products = wc_get_products(array(
        'status' => 'publish',
        'limit' => 8,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
}

$sale_products = function_exists('wc_get_products') ? wc_get_products(array(
    'status' => 'publish',
    'limit' => 4,
    'on_sale' => true,
    'orderby' => 'date',
    'order' => 'DESC',
)) : array();

if (empty($sale_products)) {
    $sale_products = array_slice($featured_products, 0, 4);
}
?>

<main id="primary" class="site-main bg-cream-light text-neutral-dark">

    <section class="lc-hero relative overflow-hidden bg-primary-dark text-white">
        <div class="absolute inset-0 bg-primary-dark"></div>
        <div class="container relative z-10 mx-auto grid min-h-[720px] grid-cols-1 items-center gap-10 px-4 pb-16 pt-32 md:pb-20 md:pt-36 lg:grid-cols-2">
            <div class="max-w-3xl">
                <h1 class="max-w-4xl text-5xl font-serif font-bold leading-[1.02] tracking-normal text-white md:text-7xl">
                    Plantas, macetas e insumos para comprar hoy
                </h1>
                <p class="lc-hero__lede mt-6 max-w-2xl text-lg leading-relaxed md:text-2xl">
                    Catálogo online de Vivero Los Cocos con precios visibles, stock real, asesoramiento directo y entrega coordinada en Mendoza.
                </p>

                <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                    <a href="<?php echo esc_url($shop_url); ?>"
                       class="inline-flex min-h-[54px] items-center justify-center rounded-full bg-accent px-8 py-4 text-base font-bold text-white shadow-lg shadow-black/20 transition-colors hover:bg-accent-hover">
                        Comprar ahora
                    </a>
                    <a href="#categorias"
                       class="lc-hero__secondary-cta inline-flex min-h-[54px] items-center justify-center rounded-full px-8 py-4 text-base font-bold transition-colors">
                        Ver categorías
                    </a>
                </div>

                <div class="mt-12 grid max-w-3xl grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="border-t border-white/25 pt-4">
                        <strong class="block text-base text-white">Stock visible</strong>
                        <span class="lc-hero__feature-text mt-1 block text-sm leading-relaxed">Productos publicados desde WooCommerce.</span>
                    </div>
                    <div class="border-t border-white/25 pt-4">
                        <strong class="block text-base text-white">Entrega local</strong>
                        <span class="lc-hero__feature-text mt-1 block text-sm leading-relaxed">Retiro y coordinación en Mendoza.</span>
                    </div>
                    <div class="border-t border-white/25 pt-4">
                        <strong class="block text-base text-white">Compra asistida</strong>
                        <span class="lc-hero__feature-text mt-1 block text-sm leading-relaxed">WhatsApp antes y después de comprar.</span>
                    </div>
                </div>
            </div>

            <div class="relative hidden min-h-[560px] lg:block">
                <div class="lc-hero__image-frame absolute inset-0 rounded-lg"></div>
                <img
                    src="<?php echo esc_url($hero_image_url); ?>"
                    alt="Planta de interior seleccionada en Vivero Los Cocos"
                    class="absolute inset-6 h-[calc(100%-3rem)] w-[calc(100%-3rem)] rounded-lg object-cover shadow-2xl shadow-black/30"
                    fetchpriority="high">
                <div class="lc-hero__image-caption absolute bottom-10 left-10 right-10 rounded-lg p-5">
                    <strong class="block text-lg">Vivero Los Cocos</strong>
                    <span class="mt-1 block text-sm leading-relaxed">Una tienda online simple para elegir, comprar y coordinar tu pedido.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-neutral-200 bg-white">
        <div class="container mx-auto grid grid-cols-1 gap-px px-4 py-0 md:grid-cols-4">
            <div class="border-b border-neutral-200 py-6 md:border-b-0 md:border-r md:pr-6">
                <strong class="block text-primary-dark">Plantas seleccionadas</strong>
                <span class="mt-1 block text-sm text-neutral-medium">Ejemplares cuidados en vivero.</span>
            </div>
            <div class="border-b border-neutral-200 py-6 md:border-b-0 md:border-r md:px-6">
                <strong class="block text-primary-dark">Precios visibles</strong>
                <span class="mt-1 block text-sm text-neutral-medium">Sin pasos ocultos para comprar.</span>
            </div>
            <div class="border-b border-neutral-200 py-6 md:border-b-0 md:border-r md:px-6">
                <strong class="block text-primary-dark">WhatsApp activo</strong>
                <span class="mt-1 block text-sm text-neutral-medium">Consultas rápidas con el equipo.</span>
            </div>
            <div class="py-6 md:pl-6">
                <strong class="block text-primary-dark">Local en Godoy Cruz</strong>
                <span class="mt-1 block text-sm text-neutral-medium">Retiro o entrega coordinada.</span>
            </div>
        </div>
    </section>

    <section id="categorias" class="bg-cream-light py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-4xl font-serif font-bold leading-tight text-primary-dark md:text-5xl">Comprar por categoría</h2>
                    <p class="mt-3 max-w-2xl text-neutral-medium">Entrá directo al tipo de producto que necesitás. Cada categoría usa datos reales del catálogo.</p>
                </div>
                <a href="<?php echo esc_url($shop_url); ?>" class="inline-flex items-center font-bold text-primary hover:text-accent">
                    Ver tienda completa
                </a>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                <?php if ($primary_categories && !is_wp_error($primary_categories)) : ?>
                    <?php foreach ($primary_categories as $category) : ?>
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="lc-home-category-card group">
                            <div class="lc-home-category-card__media">
                                <?php echo wp_kses_post(loscocos_front_category_image($category)); ?>
                                <span class="lc-home-category-card__count">
                                    <?php echo esc_html(number_format_i18n($category->count)); ?>
                                </span>
                            </div>
                            <div class="lc-home-category-card__body">
                                <div class="min-w-0">
                                    <span class="lc-home-category-card__label">Categoría</span>
                                    <h3 class="lc-home-category-card__title"><?php echo esc_html($category->name); ?></h3>
                                    <p class="lc-home-category-card__text">
                                        <?php echo esc_html($category->count); ?> productos disponibles
                                    </p>
                                </div>
                                <span class="lc-home-category-card__arrow" aria-hidden="true">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if (!empty($sale_products)) : ?>
        <section class="bg-white py-16 md:py-24">
            <div class="container mx-auto px-4">
                <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-4xl font-serif font-bold text-primary-dark md:text-5xl">Destacados para comprar hoy</h2>
                        <p class="mt-3 max-w-2xl text-neutral-medium">Productos con precio, stock y acceso directo al carrito.</p>
                    </div>
                    <a href="<?php echo esc_url($shop_url); ?>" class="inline-flex items-center font-bold text-primary hover:text-accent">
                        Ver más productos
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-4">
                    <?php foreach ($sale_products as $product) : ?>
                        <?php
                        if (!$product instanceof WC_Product) {
                            continue;
                        }
                        $product_id = $product->get_id();
                        $product_url = get_permalink($product_id);
                        $image_attrs = array(
                            'class' => 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-105',
                            'loading' => 'lazy',
                        );
                        $image = $product->get_image_id()
                            ? wp_get_attachment_image($product->get_image_id(), 'woocommerce_thumbnail', false, $image_attrs)
                            : '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_attr($product->get_name()) . '" class="' . esc_attr($image_attrs['class']) . '" loading="lazy">';
                        $quick_add = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
                        ?>
                        <article class="group flex h-full flex-col overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg">
                            <a href="<?php echo esc_url($product_url); ?>" class="relative block aspect-square overflow-hidden bg-neutral-100">
                                <?php echo wp_kses_post($image); ?>
                                <?php if ($product->is_on_sale()) : ?>
                                    <span class="absolute left-3 top-3 rounded-full bg-accent px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">Oferta</span>
                                <?php endif; ?>
                            </a>
                            <div class="flex flex-1 flex-col p-5">
                                <h3 class="mb-3 min-h-[3.25rem] text-lg font-bold leading-snug text-primary-dark">
                                    <a href="<?php echo esc_url($product_url); ?>" class="hover:text-accent">
                                        <?php echo esc_html($product->get_name()); ?>
                                    </a>
                                </h3>
                                <div class="mb-4 flex items-end justify-between gap-3">
                                    <div class="text-xl font-extrabold text-neutral-dark">
                                        <?php echo wp_kses_post($product->get_price_html()); ?>
                                    </div>
                                    <?php if ($product->is_in_stock()) : ?>
                                        <span class="rounded-full bg-secondary-light px-3 py-1 text-xs font-semibold text-primary-dark">En stock</span>
                                    <?php else : ?>
                                        <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-medium">Sin stock</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-auto grid gap-2">
                                    <?php if ($quick_add) : ?>
                                        <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
                                           data-quantity="1"
                                           data-product_id="<?php echo esc_attr($product_id); ?>"
                                           data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                                           class="button product_type_simple add_to_cart_button ajax_add_to_cart inline-flex min-h-[44px] items-center justify-center rounded-full bg-primary px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-primary-dark">
                                            Agregar al carrito
                                        </a>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url($product_url); ?>" class="inline-flex min-h-[44px] items-center justify-center rounded-full bg-primary px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-primary-dark">
                                            Ver producto
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url($product_url); ?>" class="inline-flex min-h-[40px] items-center justify-center rounded-full border border-neutral-200 px-5 py-2 text-sm font-semibold text-neutral-dark transition-colors hover:border-primary hover:text-primary">
                                        Detalles
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section id="featured" class="bg-primary-dark py-16 text-white md:py-24">
        <div class="container mx-auto grid grid-cols-1 gap-10 px-4 lg:grid-cols-[1fr_0.9fr] lg:items-center">
            <div>
                <h2 class="text-4xl font-serif font-bold leading-tight text-white md:text-5xl">Una compra de vivero con criterio profesional.</h2>
                <p class="mt-5 max-w-2xl text-lg leading-relaxed text-white/78">
                    Elegimos productos que funcionan en hogares, patios y jardines de Mendoza. Si necesitás ayuda, podés consultar antes de comprar y coordinar la mejor forma de entrega.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="<?php echo esc_url($shop_url); ?>" class="inline-flex min-h-[50px] items-center justify-center rounded-full bg-white px-7 py-3 font-bold text-primary-dark transition-colors hover:bg-accent hover:text-white">
                        Explorar catálogo
                    </a>
                    <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener" class="inline-flex min-h-[50px] items-center justify-center rounded-full border border-white/30 px-7 py-3 font-bold text-white transition-colors hover:bg-white hover:text-primary-dark">
                        Consultar por WhatsApp
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-lg border border-white/15 bg-white/10 p-5">
                    <strong class="block text-2xl font-bold text-accent">Catálogo activo</strong>
                    <span class="mt-2 block text-sm text-white/70">Productos publicados desde WooCommerce.</span>
                </div>
                <div class="rounded-lg border border-white/15 bg-white/10 p-5">
                    <strong class="block text-2xl font-bold text-accent">Precios visibles</strong>
                    <span class="mt-2 block text-sm text-white/70">Compra directa desde cada ficha.</span>
                </div>
                <div class="col-span-2 rounded-lg border border-white/15 bg-white/10 p-5">
                    <strong class="block text-xl text-white">Perito Moreno 1295, Godoy Cruz</strong>
                    <span class="mt-2 block text-sm text-white/70">Atención local, retiro y entregas coordinadas.</span>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
