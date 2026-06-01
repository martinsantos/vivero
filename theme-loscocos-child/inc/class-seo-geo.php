<?php
/**
 * SEO and GEO entity layer for Vivero Los Cocos.
 *
 * @package LosCocos_Child
 */

if (!defined('ABSPATH')) {
    exit;
}

class LosCocos_SEO_GEO {
    const PHONE = '+542614399025';
    const ADDRESS = 'Perito Moreno 1295, Godoy Cruz, Mendoza, Argentina';

    /**
     * Register hooks.
     */
    public static function init() {
        add_action('wp_head', [self::class, 'render_meta_description'], 2);
        add_action('wp_head', [self::class, 'render_json_ld'], 30);
    }

    /**
     * Render a concise page meta description when no SEO plugin has already done it.
     */
    public static function render_meta_description() {
        if (is_admin()) {
            return;
        }

        $description = self::current_meta_description();
        if ($description === '') {
            return;
        }

        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }

    /**
     * Render structured data graph for local business, commerce and product entity pages.
     */
    public static function render_json_ld() {
        if (is_admin()) {
            return;
        }

        $graph = [
            self::organization_schema(),
            self::local_business_schema(),
            self::website_schema(),
        ];

        $breadcrumb = self::breadcrumb_schema();
        if ($breadcrumb) {
            $graph[] = $breadcrumb;
        }

        if (function_exists('is_product') && is_product()) {
            $product = wc_get_product(get_the_ID());
            if ($product instanceof WC_Product) {
                $graph[] = self::product_schema($product);
                $graph[] = self::product_faq_schema($product);
            }
        }

        $payload = [
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter($graph)),
        ];

        echo '<script type="application/ld+json" class="loscocos-seo-geo-schema">' . wp_json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }

    /**
     * Visible GEO block for product pages. This mirrors the JSON-LD FAQ and botanical properties.
     */
    public static function render_product_geo_block($product) {
        if (!$product instanceof WC_Product) {
            return;
        }

        $profile = self::product_profile($product);
        $category = self::product_primary_category($product);
        ?>
        <section class="lc-product-geo" aria-labelledby="lc-product-geo-title">
            <div class="lc-product-geo__header">
                <p class="lc-product-geo__eyebrow">Guía local</p>
                <h2 id="lc-product-geo-title">Cómo elegir <?php echo esc_html($product->get_name()); ?> en Mendoza y Cuyo</h2>
                <p>
                    Información práctica para comprar, ubicar y cuidar este producto en jardines, patios, veredas y balcones de Mendoza,
                    con asesoramiento de Vivero Los Cocos en Godoy Cruz.
                </p>
            </div>

            <div class="lc-product-geo__grid">
                <article>
                    <span>Uso local</span>
                    <strong><?php echo esc_html($profile['use']); ?></strong>
                    <p><?php echo esc_html($profile['local_context']); ?></p>
                </article>
                <article>
                    <span>Cuidado</span>
                    <strong><?php echo esc_html($profile['light']); ?></strong>
                    <p>Riego: <?php echo esc_html($profile['water']); ?>. Nivel de cuidado: <?php echo esc_html($profile['care']); ?>.</p>
                </article>
                <article>
                    <span>Entidad botánica</span>
                    <strong><?php echo esc_html($profile['botanical_name'] ?: $category); ?></strong>
                    <p><?php echo esc_html($profile['entity_note']); ?></p>
                </article>
                <article>
                    <span>Disponibilidad</span>
                    <strong><?php echo esc_html($product->is_in_stock() ? 'Stock disponible' : 'Consultar stock'); ?></strong>
                    <p>Compra online con retiro o entrega coordinada desde Godoy Cruz para Mendoza.</p>
                </article>
            </div>

            <div class="lc-product-geo__faq">
                <h3>Preguntas frecuentes</h3>
                <?php foreach (self::product_faq_items($product, $profile) as $item) : ?>
                    <details>
                        <summary><?php echo esc_html($item['question']); ?></summary>
                        <p><?php echo esc_html($item['answer']); ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }

    private static function current_meta_description() {
        if (function_exists('is_product') && is_product()) {
            $product = wc_get_product(get_the_ID());
            if ($product instanceof WC_Product) {
                return self::clip_description(sprintf(
                    '%s en Vivero Los Cocos: precio, stock, compra online y asesoramiento para jardines, patios y espacios verdes de Mendoza y Cuyo.',
                    $product->get_name()
                ));
            }
        }

        if (function_exists('is_shop') && is_shop()) {
            return 'Tienda online de Vivero Los Cocos: plantas, macetas, sustratos e insumos con stock visible, precios y entrega coordinada en Mendoza.';
        }

        if (is_front_page()) {
            return 'Vivero Los Cocos en Godoy Cruz, Mendoza: tienda online de plantas, macetas e insumos con asesoramiento y compra directa.';
        }

        if (is_page()) {
            return self::clip_description(get_the_title() . ' de Vivero Los Cocos: información, compra online y asesoramiento local en Mendoza.');
        }

        return '';
    }

    private static function clip_description($text) {
        $text = trim(wp_strip_all_tags($text));
        if (function_exists('mb_substr')) {
            return mb_substr($text, 0, 158);
        }
        return substr($text, 0, 158);
    }

    private static function organization_schema() {
        return [
            '@type' => 'Organization',
            '@id' => home_url('/#organization'),
            'name' => 'Vivero Los Cocos',
            'url' => home_url('/'),
            'telephone' => self::PHONE,
            'sameAs' => [
                'https://www.instagram.com/viveroloscocos/',
            ],
        ];
    }

    private static function local_business_schema() {
        return [
            '@type' => ['LocalBusiness', 'Store'],
            '@id' => home_url('/#localbusiness'),
            'name' => 'Vivero Los Cocos',
            'url' => home_url('/'),
            'telephone' => self::PHONE,
            'priceRange' => '$$',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Perito Moreno 1295',
                'addressLocality' => 'Godoy Cruz',
                'addressRegion' => 'Mendoza',
                'addressCountry' => 'AR',
            ],
            'areaServed' => [
                ['@type' => 'AdministrativeArea', 'name' => 'Mendoza'],
                ['@type' => 'AdministrativeArea', 'name' => 'Cuyo'],
                ['@type' => 'Country', 'name' => 'Argentina'],
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'opens' => '09:30',
                    'closes' => '18:30',
                ],
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => 'Sunday',
                    'opens' => '10:30',
                    'closes' => '13:30',
                ],
            ],
        ];
    }

    private static function website_schema() {
        return [
            '@type' => 'WebSite',
            '@id' => home_url('/#website'),
            'url' => home_url('/'),
            'name' => 'Vivero Los Cocos',
            'publisher' => ['@id' => home_url('/#organization')],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}&post_type=product'),
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    private static function product_schema($product) {
        $profile = self::product_profile($product);
        $image = wp_get_attachment_image_url($product->get_image_id(), 'large');
        $price = $product->get_price();

        $schema = [
            '@type' => 'Product',
            '@id' => $product->get_permalink() . '#product',
            'name' => $product->get_name(),
            'url' => $product->get_permalink(),
            'description' => self::clip_description($product->get_short_description() ?: $product->get_description() ?: $product->get_name()),
            'sku' => $product->get_sku() ?: (string) $product->get_id(),
            'brand' => ['@id' => home_url('/#organization')],
            'category' => self::product_primary_category($product),
            'offers' => [
                '@type' => 'Offer',
                'url' => $product->get_permalink(),
                'priceCurrency' => get_woocommerce_currency(),
                'price' => $price !== '' ? wc_format_decimal($price, wc_get_price_decimals()) : '0',
                'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
                'seller' => ['@id' => home_url('/#localbusiness')],
            ],
            'additionalProperty' => self::product_additional_properties($profile),
        ];

        if ($image) {
            $schema['image'] = [$image];
        }

        return $schema;
    }

    private static function product_faq_schema($product) {
        $profile = self::product_profile($product);
        $items = [];

        foreach (self::product_faq_items($product, $profile) as $item) {
            $items[] = [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['answer'],
                ],
            ];
        }

        return [
            '@type' => 'FAQPage',
            '@id' => $product->get_permalink() . '#faq',
            'mainEntity' => $items,
        ];
    }

    private static function product_faq_items($product, $profile) {
        $name = $product->get_name();

        return [
            [
                'question' => '¿Dónde comprar ' . $name . ' en Mendoza?',
                'answer' => $name . ' está disponible en Vivero Los Cocos, Godoy Cruz, con compra online, stock visible y coordinación de retiro o entrega en Mendoza.',
            ],
            [
                'question' => '¿Qué cuidados necesita ' . $name . ' en Cuyo?',
                'answer' => 'Para el clima de Mendoza y Cuyo recomendamos revisar ubicación, exposición y riego. Referencia de esta ficha: luz ' . $profile['light'] . ', riego ' . $profile['water'] . ' y cuidado ' . $profile['care'] . '.',
            ],
            [
                'question' => '¿Puedo pedir asesoramiento antes de comprar?',
                'answer' => 'Sí. Vivero Los Cocos brinda asesoramiento para elegir plantas, macetas e insumos según sol, sombra, patio, balcón, jardín o vereda.',
            ],
        ];
    }

    private static function product_additional_properties($profile) {
        $properties = [
            'Nombre botánico' => $profile['botanical_name'],
            'Uso recomendado' => $profile['use'],
            'Luz' => $profile['light'],
            'Riego' => $profile['water'],
            'Nivel de cuidado' => $profile['care'],
            'Contexto GEO' => $profile['local_context'],
            'Área servida' => 'Mendoza, Cuyo, Argentina',
        ];

        $output = [];
        foreach ($properties as $name => $value) {
            if ($value === '') {
                continue;
            }
            $output[] = [
                '@type' => 'PropertyValue',
                'name' => $name,
                'value' => $value,
            ];
        }

        return $output;
    }

    private static function product_profile($product) {
        $product_id = $product->get_id();
        $category = self::product_primary_category($product);
        $botanical = trim((string) get_post_meta($product_id, '_loscocos_nombre_botanico', true));
        $light = self::meta_or_attribute($product, '_loscocos_luminosidad', 'Luz', 'sol directo o media sombra luminosa');
        $water = self::meta_or_attribute($product, '_loscocos_riego', 'Riego', 'moderado, ajustado a estación y ubicación');
        $care = self::meta_or_attribute($product, '_loscocos_nivel_cuidado', 'Cuidado', 'medio');

        return [
            'botanical_name' => $botanical,
            'light' => self::readable_meta_value($light),
            'water' => self::readable_meta_value($water),
            'care' => self::readable_meta_value($care),
            'use' => self::recommended_use($category),
            'local_context' => 'Selección pensada para Mendoza y Cuyo, donde conviene evaluar sol fuerte, amplitud térmica, viento zonda, heladas y eficiencia de riego.',
            'entity_note' => $botanical ? 'Nombre científico o botánico de referencia para comparar la especie y sus cuidados.' : 'Ficha comercial enriquecida para búsqueda local, compra asistida y recomendaciones de vivero.',
        ];
    }

    private static function meta_or_attribute($product, $meta_key, $attribute_name, $fallback) {
        $meta = get_post_meta($product->get_id(), $meta_key, true);
        if (is_array($meta)) {
            $meta = implode(', ', $meta);
        }
        if ($meta !== '') {
            return (string) $meta;
        }

        $attribute = $product->get_attribute($attribute_name);
        if ($attribute !== '') {
            return $attribute;
        }

        return $fallback;
    }

    private static function readable_meta_value($value) {
        $map = [
            'sol_directo' => 'sol directo',
            'luz_parcial' => 'luz parcial o semisombra',
            'sombra' => 'sombra o interior luminoso',
            'escaso' => 'escaso',
            'moderado' => 'moderado',
            'intenso' => 'intenso',
            'facil' => 'fácil',
            'dificil' => 'difícil',
        ];

        return $map[$value] ?? str_replace('_', ' ', (string) $value);
    }

    private static function recommended_use($category) {
        $normalized = strtolower(remove_accents($category));

        if (strpos($normalized, 'maceta') !== false) {
            return 'presentación de plantas en patios, balcones, galerías e interiores';
        }
        if (strpos($normalized, 'fertilizante') !== false || strpos($normalized, 'insecticida') !== false || strpos($normalized, 'fungicida') !== false || strpos($normalized, 'herbicida') !== false) {
            return 'mantenimiento y sanidad de jardines, macetas y espacios verdes';
        }

        return 'jardines, patios, veredas, balcones y proyectos paisajísticos de Mendoza';
    }

    private static function product_primary_category($product) {
        $terms = get_the_terms($product->get_id(), 'product_cat');
        if (!$terms || is_wp_error($terms)) {
            return 'Plantas de vivero';
        }

        $term = reset($terms);
        return $term ? $term->name : 'Plantas de vivero';
    }

    private static function breadcrumb_schema() {
        $items = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Inicio',
                'item' => home_url('/'),
            ],
        ];

        if (function_exists('is_shop') && is_shop()) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Tienda',
                'item' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/tienda/'),
            ];
        } elseif (function_exists('is_product') && is_product()) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Tienda',
                'item' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/tienda/'),
            ];
            $items[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => get_the_title(),
                'item' => get_permalink(),
            ];
        } elseif (is_page()) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => get_the_title(),
                'item' => get_permalink(),
            ];
        }

        return [
            '@type' => 'BreadcrumbList',
            '@id' => home_url(add_query_arg([], $GLOBALS['wp']->request ?? '')) . '#breadcrumb',
            'itemListElement' => $items,
        ];
    }
}
