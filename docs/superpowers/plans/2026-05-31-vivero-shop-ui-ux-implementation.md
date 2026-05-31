# Vivero Shop UI/UX Upgrade Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Upgrade `/tienda/` into a stronger version of the current production shop without replacing the theme or returning to WooCommerce block templates.

**Architecture:** Keep the classic WooCommerce child-theme templates as the source of truth. Add a curated commerce layer in `archive-product.php`, improve product-card semantics in `woocommerce/content-product.php`, and style the new/updated classes in `assets/css/theme.css`, then compile to `style.css` and sync the active mounted theme copy. Contract tests verify classic rendering, featured products, card proportions, desktop grid, sticky controls, and mobile overflow.

**Tech Stack:** WordPress, WooCommerce classic templates, PHP, Tailwind-generated CSS plus hand-written CSS in `theme-loscocos-child/assets/css/theme.css`, PostCSS build script, Playwright render contract.

---

## File Structure

- Modify `scripts/tests/shop-render-contract.mjs`
  - Owns rendered `/tienda/` assertions for classic template shell, featured products, card proportions, desktop/mobile overflow, and sticky controls.
- Modify `theme-loscocos-child/archive-product.php`
  - Owns shop page composition: hero metrics, category strip, catalog toolbar, featured product query, and main WooCommerce loop.
- Modify `theme-loscocos-child/woocommerce/content-product.php`
  - Owns individual catalog card markup and product-data fallbacks.
- Modify `theme-loscocos-child/assets/css/theme.css`
  - Owns source styles for shop controls, featured band, product cards, and responsive refinements.
- Generated `theme-loscocos-child/style.css`
  - Built from `assets/css/theme.css` with `npm run build`.
- Sync active theme files under `wp-content/themes/theme-loscocos-child/`
  - WordPress at `localhost:8080` reads this mounted copy.

---

### Task 1: Strengthen The Render Contract Before UI Changes

**Files:**
- Modify: `scripts/tests/shop-render-contract.mjs`

- [ ] **Step 1: Extend the test to cover desktop and mobile**

Replace the current single-page script structure with a loop over desktop and mobile while keeping the same core shell checks. Use this complete script:

```js
import { chromium } from 'playwright';

const shopUrl = process.env.SHOP_URL || 'http://localhost:8080/tienda/';

function fail(message, details = {}) {
  const suffix = Object.keys(details).length ? `\n${JSON.stringify(details, null, 2)}` : '';
  throw new Error(`${message}${suffix}`);
}

const viewports = [
  { name: 'desktop', width: 1440, height: 1100, isMobile: false },
  { name: 'mobile', width: 390, height: 844, isMobile: true },
];

const browser = await chromium.launch({ headless: true });

try {
  for (const viewport of viewports) {
    const page = await browser.newPage({
      viewport: { width: viewport.width, height: viewport.height },
      isMobile: viewport.isMobile,
      deviceScaleFactor: 1,
    });

    await page.goto(shopUrl, { waitUntil: 'networkidle', timeout: 45000 });
    await page.screenshot({
      path: `/private/tmp/vivero-shop-contract-${viewport.name}.png`,
      fullPage: true,
    });

    const snapshot = await page.evaluate(() => {
      const rectFor = (selector) => {
        const node = document.querySelector(selector);
        if (!node) return null;
        const rect = node.getBoundingClientRect();
        return {
          width: Math.round(rect.width),
          height: Math.round(rect.height),
          top: Math.round(rect.top),
          left: Math.round(rect.left),
          bottom: Math.round(rect.bottom),
        };
      };

      const styleFor = (selector) => {
        const node = document.querySelector(selector);
        if (!node) return null;
        const styles = getComputedStyle(node);
        return {
          display: styles.display,
          gridTemplateColumns: styles.gridTemplateColumns,
          borderRadius: styles.borderRadius,
          boxShadow: styles.boxShadow,
          overflow: styles.overflow,
          position: styles.position,
          backgroundColor: styles.backgroundColor,
        };
      };

      const productCards = [...document.querySelectorAll('ul.products > li.product')];
      const firstRowTops = productCards.slice(0, 4).map((card) => Math.round(card.getBoundingClientRect().top));

      return {
        title: document.title,
        bodyClass: document.body.className,
        isUsingWooBlockTheme: document.body.classList.contains('woocommerce-uses-block-theme'),
        hasClassicPage: Boolean(document.querySelector('#page.site')),
        hasThemeHeader: Boolean(document.querySelector('header#masthead.site-header')),
        hasThemeFooter: Boolean(document.querySelector('footer')),
        hasClassicShopHero: document.body.textContent.includes('Tienda online de Vivero Los Cocos'),
        hasFeaturedSection: Boolean(document.querySelector('.lc-featured-products')),
        featuredCardCount: document.querySelectorAll('.lc-featured-product-card').length,
        hasCatalogToolbar: Boolean(document.querySelector('.lc-shop-toolbar')),
        blockProductCount: document.querySelectorAll('.wc-block-product').length,
        classicProductCount: productCards.length,
        firstCardRect: rectFor('ul.products > li.product'),
        firstImageRect: rectFor('ul.products > li.product img'),
        firstCardStyle: styleFor('ul.products > li.product'),
        productGridStyle: styleFor('ul.products'),
        controlsStyle: styleFor('.lc-shop-controls'),
        controlsRect: rectFor('.lc-shop-controls'),
        firstRowTops,
        overflowX: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      };
    });

    if (!snapshot.hasClassicPage || !snapshot.hasThemeHeader || !snapshot.hasThemeFooter) {
      fail('La tienda no esta usando el shell clasico del theme activo.', { viewport, snapshot });
    }

    if (snapshot.isUsingWooBlockTheme) {
      fail('WooCommerce detecto la tienda como block theme; debe usar templates clasicos.', { viewport, snapshot });
    }

    if (!snapshot.hasClassicShopHero) {
      fail('La tienda no esta usando el archive-product.php vigente del child theme.', { viewport, snapshot });
    }

    if (snapshot.blockProductCount > 0) {
      fail('La tienda esta renderizando la plantilla de bloques de WooCommerce en lugar del loop clasico.', { viewport, snapshot });
    }

    if (snapshot.classicProductCount < 8 || !snapshot.firstCardRect || !snapshot.firstImageRect || !snapshot.firstCardStyle) {
      fail('La grilla clasica de productos no esta renderizando suficientes cards.', { viewport, snapshot });
    }

    if (!snapshot.hasCatalogToolbar) {
      fail('La tienda debe renderizar la toolbar comercial compacta.', { viewport, snapshot });
    }

    if (!snapshot.hasFeaturedSection || snapshot.featuredCardCount < 1 || snapshot.featuredCardCount > 3) {
      fail('La tienda debe renderizar entre 1 y 3 destacados cuando hay productos elegibles.', { viewport, snapshot });
    }

    const borderRadius = Number.parseFloat(snapshot.firstCardStyle.borderRadius);
    const hasShadow = snapshot.firstCardStyle.boxShadow && snapshot.firstCardStyle.boxShadow !== 'none';
    const imageIsControlled =
      snapshot.firstImageRect.height <= snapshot.firstCardRect.width + 4 &&
      snapshot.firstImageRect.width <= snapshot.firstCardRect.width + 4;

    if (snapshot.firstCardStyle.display !== 'flex' || borderRadius < 6 || !hasShadow || !imageIsControlled) {
      fail('La card de producto no conserva el layout visual controlado del theme.', { viewport, snapshot });
    }

    if (snapshot.overflowX > 0) {
      fail('La tienda no debe generar overflow horizontal.', { viewport, snapshot });
    }

    if (viewport.name === 'desktop') {
      const firstRowIsAligned =
        snapshot.firstRowTops.length === 4 &&
        new Set(snapshot.firstRowTops).size === 1;

      if (!firstRowIsAligned) {
        fail('La grilla desktop debe conservar 4 columnas alineadas.', { viewport, snapshot });
      }

      if (snapshot.controlsStyle?.position !== 'sticky') {
        fail('Los controles de catalogo deben ser sticky en desktop.', { viewport, snapshot });
      }
    }

    await page.close();
  }

  console.log(`Shop render contract passed for ${shopUrl}`);
} finally {
  await browser.close();
}
```

- [ ] **Step 2: Run the contract and confirm it fails before implementation**

Run:

```bash
node scripts/tests/shop-render-contract.mjs
```

Expected: FAIL with `La tienda debe renderizar la toolbar comercial compacta.` or `La tienda debe renderizar entre 1 y 3 destacados...`.

- [ ] **Step 3: Commit the failing contract**

Run:

```bash
git add scripts/tests/shop-render-contract.mjs
git commit -m "test: cover upgraded shop catalog contract"
```

Expected: commit succeeds with only the test file staged. If unrelated files are dirty, leave them unstaged.

---

### Task 2: Add Featured Product Selection And Shop Metrics

**Files:**
- Modify: `theme-loscocos-child/archive-product.php`

- [ ] **Step 1: Add query helpers before template output**

In `theme-loscocos-child/archive-product.php`, after the `$categories = get_terms(...)` block and before `?>`, add this PHP:

```php
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
```

- [ ] **Step 2: Run PHP lint**

Run:

```bash
php -l theme-loscocos-child/archive-product.php
```

Expected: `No syntax errors detected in theme-loscocos-child/archive-product.php`.

- [ ] **Step 3: Commit**

Run:

```bash
git add theme-loscocos-child/archive-product.php
git commit -m "feat: prepare shop metrics and featured products"
```

Expected: commit succeeds with the archive template staged.

---

### Task 3: Upgrade Shop Archive Markup Without Changing The Theme Shell

**Files:**
- Modify: `theme-loscocos-child/archive-product.php`

- [ ] **Step 1: Replace the three hero value blocks**

Replace the existing `<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-10">...</div>` inside the hero with:

```php
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
```

- [ ] **Step 2: Add a shop controls wrapper around categories and toolbar**

Change the category strip section opening from:

```php
<section class="border-b border-neutral-200 bg-white">
```

to:

```php
<section class="lc-shop-controls border-b border-neutral-200 bg-white">
```

Then replace the catalog area before `<ul class="products...">`. Replace:

```php
<?php if (woocommerce_product_loop()) : ?>
    <?php woocommerce_catalog_ordering(); ?>
    <ul class="products columns-<?php echo esc_attr(wc_get_loop_prop('columns')); ?> grid grid-cols-1 gap-6 lg:grid-cols-3 xl:grid-cols-4">
```

with:

```php
<?php if (woocommerce_product_loop()) : ?>
    <div class="lc-shop-toolbar">
        <div>
            <p class="lc-shop-toolbar__eyebrow">Catálogo online</p>
            <p class="lc-shop-toolbar__count">
                <?php echo esc_html(number_format_i18n($product_total)); ?> productos disponibles
            </p>
        </div>
        <div class="lc-shop-toolbar__ordering">
            <?php woocommerce_catalog_ordering(); ?>
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
                    $featured_categories = wp_get_post_terms($featured_id, 'product_cat');
                    $featured_category = ($featured_categories && !is_wp_error($featured_categories)) ? $featured_categories[0]->name : 'Vivero Los Cocos';
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
```

- [ ] **Step 3: Run PHP lint**

Run:

```bash
php -l theme-loscocos-child/archive-product.php
```

Expected: `No syntax errors detected in theme-loscocos-child/archive-product.php`.

- [ ] **Step 4: Run the render contract and confirm only styling/card assertions still fail**

Run:

```bash
node scripts/tests/shop-render-contract.mjs
```

Expected: if the local server is reading the source copy, the featured/toolbar checks should pass and styling checks may fail. If the mounted theme copy is still stale, the featured/toolbar checks will still fail until Task 6 syncs files.

- [ ] **Step 5: Commit**

Run:

```bash
git add theme-loscocos-child/archive-product.php
git commit -m "feat: add upgraded shop catalog sections"
```

Expected: commit succeeds with only the archive template staged.

---

### Task 4: Improve Product Card Markup And Data Fallbacks

**Files:**
- Modify: `theme-loscocos-child/woocommerce/content-product.php`

- [ ] **Step 1: Replace category and description fallback variables**

Replace:

```php
$categories = wp_get_post_terms($product_id, 'product_cat');
$category_name = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '';
$short_description = wp_trim_words(wp_strip_all_tags($product->get_short_description()), 14, '...');
```

with:

```php
$categories = wp_get_post_terms($product_id, 'product_cat');
$default_category_id = (int) get_option('default_product_cat');
$category_name = '';

if ($categories && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        if ((int) $category->term_id !== $default_category_id && 'sin-categorizar' !== $category->slug) {
            $category_name = $category->name;
            break;
        }
    }
}

if (!$category_name) {
    $category_name = 'Vivero Los Cocos';
}

$short_description = wp_trim_words(wp_strip_all_tags($product->get_short_description()), 18, '...');

if (!$short_description) {
    $short_description = sprintf(
        '%s seleccionado para compra directa o consulta personalizada.',
        $category_name
    );
}
```

- [ ] **Step 2: Replace the `<li>` and descendant class structure**

In the same file, update the opening card and key descendants to add stable `lc-product-card` classes while keeping WooCommerce classes:

```php
<li <?php wc_product_class('lc-product-card group flex h-full flex-col overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg', $product); ?>>
    <a href="<?php echo esc_url($product_url); ?>" class="lc-product-card__media relative block aspect-square overflow-hidden bg-neutral-100">
```

Change the body wrapper from:

```php
<div class="flex flex-1 flex-col p-5">
```

to:

```php
<div class="lc-product-card__body flex flex-1 flex-col p-5">
```

Change the category `<p>` class to:

```php
<p class="lc-product-card__category mb-2 text-xs font-semibold uppercase tracking-widest text-primary-light">
```

Change the title `<h2>` class to:

```php
<h2 class="lc-product-card__title mb-3 min-h-[3.5rem] text-lg font-bold leading-snug text-primary-dark">
```

Change the description `<p>` class to:

```php
<p class="lc-product-card__description mb-5 min-h-[2.75rem] text-sm leading-relaxed text-neutral-medium">
```

Change the price/stock row wrapper to:

```php
<div class="lc-product-card__meta mb-4 flex items-end justify-between gap-3">
```

Change the price wrapper to:

```php
<div class="lc-product-card__price text-xl font-extrabold text-neutral-dark">
```

Change the actions wrapper to:

```php
<div class="lc-product-card__actions grid grid-cols-1 gap-2">
```

- [ ] **Step 3: Run PHP lint**

Run:

```bash
php -l theme-loscocos-child/woocommerce/content-product.php
```

Expected: `No syntax errors detected in theme-loscocos-child/woocommerce/content-product.php`.

- [ ] **Step 4: Commit**

Run:

```bash
git add theme-loscocos-child/woocommerce/content-product.php
git commit -m "feat: refine shop product card markup"
```

Expected: commit succeeds with only the content product template staged.

---

### Task 5: Add Shop UI CSS

**Files:**
- Modify: `theme-loscocos-child/assets/css/theme.css`
- Generated: `theme-loscocos-child/style.css`

- [ ] **Step 1: Add source CSS before `/* Single product safety layer */`**

In `theme-loscocos-child/assets/css/theme.css`, insert this block after `.lc-category-strip::-webkit-scrollbar`:

```css
/* Shop archive refinement */
.lc-shop-controls {
  z-index: 30;
  box-shadow: 0 1px 0 rgba(27, 77, 62, 0.08);
}

@media (min-width: 1024px) {
  .lc-shop-controls {
    position: sticky;
    top: 80px;
  }
}

.lc-shop-hero-metric {
  min-height: 82px;
}

.lc-shop-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding: 1rem 1.25rem;
  border: 1px solid #e4ebe0;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 10px 24px rgba(23, 35, 29, 0.04);
}

.lc-shop-toolbar__eyebrow {
  margin: 0 0 0.25rem;
  color: #6b7280;
  font-size: 0.72rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.lc-shop-toolbar__count {
  margin: 0;
  color: #20362b;
  font-weight: 800;
}

.lc-shop-toolbar__ordering .woocommerce-ordering {
  float: none;
  margin: 0;
}

.lc-shop-toolbar__ordering select {
  min-height: 42px;
  border-color: #dce4d8;
  border-radius: 999px;
  color: #20362b;
  font-size: 0.875rem;
  font-weight: 700;
}

.lc-featured-products {
  margin-bottom: 2rem;
  padding: 1.25rem;
  border: 1px solid #dce4d8;
  border-radius: 8px;
  background: linear-gradient(180deg, #fff 0%, #f7faf4 100%);
  box-shadow: 0 14px 30px rgba(23, 35, 29, 0.06);
}

.lc-featured-products__header {
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.lc-featured-products__eyebrow {
  margin: 0 0 0.25rem;
  color: #2c7a63;
  font-size: 0.75rem;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.lc-featured-products h2 {
  margin: 0;
  color: #143a2f;
  font-family: Merriweather, serif;
  font-size: clamp(1.35rem, 2vw, 1.9rem);
  line-height: 1.15;
}

.lc-featured-products__link {
  color: #1b4d3e;
  font-size: 0.9rem;
  font-weight: 800;
  text-decoration: none;
}

.lc-featured-products__link:hover {
  color: #e07a5f;
}

.lc-featured-products__grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.lc-featured-product-card {
  display: grid;
  grid-template-columns: 118px minmax(0, 1fr);
  min-height: 140px;
  overflow: hidden;
  border: 1px solid #e4ebe0;
  border-radius: 8px;
  background: #fff;
}

.lc-featured-product-card__media {
  display: block;
  min-height: 100%;
  overflow: hidden;
  background: #eef4eb;
}

.lc-featured-product-card__image {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.lc-featured-product-card__body {
  display: flex;
  min-width: 0;
  flex-direction: column;
  padding: 1rem;
}

.lc-featured-product-card__category {
  margin: 0 0 0.35rem;
  color: #2c7a63;
  font-size: 0.68rem;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.lc-featured-product-card h3 {
  margin: 0;
  color: #143a2f;
  font-size: 1rem;
  line-height: 1.25;
}

.lc-featured-product-card h3 a {
  color: inherit;
  text-decoration: none;
}

.lc-featured-product-card__meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-top: auto;
  color: #20362b;
  font-weight: 900;
}

.lc-featured-product-card__meta small {
  flex-shrink: 0;
  border-radius: 999px;
  background: #e8f5e9;
  padding: 0.2rem 0.55rem;
  color: #1b4d3e;
  font-size: 0.68rem;
  font-weight: 800;
}

.lc-product-card {
  box-shadow: 0 8px 22px rgba(23, 35, 29, 0.06);
}

.lc-product-card:hover,
.lc-product-card:focus-within {
  border-color: #b8cbb3;
  box-shadow: 0 16px 36px rgba(23, 35, 29, 0.12);
}

.lc-product-card__category {
  color: #2c7a63;
}

.lc-product-card__title {
  color: #143a2f;
}

.lc-product-card__description {
  display: -webkit-box;
  overflow: hidden;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}

.lc-product-card__price {
  color: #20362b;
}

.lc-product-card__actions .button {
  box-shadow: 0 8px 18px rgba(18, 184, 134, 0.18);
}

@media (max-width: 767px) {
  .lc-shop-toolbar {
    display: grid;
    gap: 0.85rem;
    padding: 1rem;
  }

  .lc-shop-toolbar__ordering,
  .lc-shop-toolbar__ordering .woocommerce-ordering,
  .lc-shop-toolbar__ordering select {
    width: 100%;
  }

  .lc-featured-products {
    margin-inline: -0.25rem;
    padding: 1rem;
  }

  .lc-featured-products__header {
    align-items: start;
  }

  .lc-featured-products__link {
    display: none;
  }

  .lc-featured-products__grid {
    display: flex;
    gap: 0.85rem;
    overflow-x: auto;
    padding-bottom: 0.35rem;
    scroll-snap-type: x proximity;
  }

  .lc-featured-product-card {
    grid-template-columns: 96px 220px;
    min-width: 330px;
    scroll-snap-align: start;
  }
}
```

- [ ] **Step 2: Build CSS**

Run:

```bash
npm run build
```

from `theme-loscocos-child`.

Expected: `style.css` is regenerated without PostCSS errors.

- [ ] **Step 3: Confirm compiled CSS contains the new selectors**

Run:

```bash
rg -n "lc-featured-products|lc-shop-toolbar|lc-product-card|lc-shop-controls" theme-loscocos-child/style.css
```

Expected: matches for all four selector groups.

- [ ] **Step 4: Commit CSS source and compiled CSS**

Run:

```bash
git add theme-loscocos-child/assets/css/theme.css theme-loscocos-child/style.css
git commit -m "style: refine shop catalog ui"
```

Expected: commit succeeds with source and compiled CSS staged.

---

### Task 6: Sync Active WordPress Theme Copy

**Files:**
- Modify active copy:
  - `wp-content/themes/theme-loscocos-child/archive-product.php`
  - `wp-content/themes/theme-loscocos-child/woocommerce/content-product.php`
  - `wp-content/themes/theme-loscocos-child/assets/css/theme.css`
  - `wp-content/themes/theme-loscocos-child/style.css`

- [ ] **Step 1: Copy source files into the mounted theme**

Run:

```bash
cp theme-loscocos-child/archive-product.php wp-content/themes/theme-loscocos-child/archive-product.php
cp theme-loscocos-child/woocommerce/content-product.php wp-content/themes/theme-loscocos-child/woocommerce/content-product.php
cp theme-loscocos-child/assets/css/theme.css wp-content/themes/theme-loscocos-child/assets/css/theme.css
cp theme-loscocos-child/style.css wp-content/themes/theme-loscocos-child/style.css
```

Expected: commands produce no output.

- [ ] **Step 2: Verify mounted files match source files**

Run:

```bash
node scripts/tests/theme-child-production-contract.mjs
```

Expected: `Child theme production contract checks passed.`

- [ ] **Step 3: Commit active-copy changes if tracked**

Run:

```bash
git add wp-content/themes/theme-loscocos-child/archive-product.php wp-content/themes/theme-loscocos-child/woocommerce/content-product.php wp-content/themes/theme-loscocos-child/assets/css/theme.css wp-content/themes/theme-loscocos-child/style.css
git commit -m "chore: sync active shop theme files"
```

Expected: if these files are tracked, commit succeeds. If Git reports no staged changes because mounted files are ignored, continue without a commit.

---

### Task 7: Verify Rendered Shop UI

**Files:**
- Test: `scripts/tests/shop-render-contract.mjs`

- [ ] **Step 1: Run the upgraded render contract**

Run:

```bash
node scripts/tests/shop-render-contract.mjs
```

Expected: `Shop render contract passed for http://localhost:8080/tienda/`.

- [ ] **Step 2: Capture final desktop and mobile screenshots**

Run:

```bash
node --input-type=module -e 'import { chromium } from "playwright"; const browser = await chromium.launch({ headless: true }); for (const target of [{name:"desktop", viewport:{width:1440,height:1100}}, {name:"mobile", viewport:{width:390,height:844}, isMobile:true}]) { const page = await browser.newPage({ viewport: target.viewport, isMobile: Boolean(target.isMobile), deviceScaleFactor: 1 }); const errors = []; page.on("console", msg => { if (["error","warning"].includes(msg.type())) errors.push({ type: msg.type(), text: msg.text() }); }); await page.goto("http://localhost:8080/tienda/", { waitUntil: "networkidle", timeout: 45000 }); await page.screenshot({ path: `/private/tmp/vivero-shop-ui-upgrade-${target.name}.png`, fullPage: true }); const metrics = await page.evaluate(() => ({ title: document.title, featured: document.querySelectorAll(".lc-featured-product-card").length, blocks: document.querySelectorAll(".wc-block-product").length, classic: document.querySelectorAll("ul.products > li.product").length, overflowX: document.documentElement.scrollWidth - document.documentElement.clientWidth })); console.log(JSON.stringify({ viewport: target.name, metrics, errors }, null, 2)); await page.close(); } await browser.close();'
```

Expected:

- `featured` is between `1` and `3`.
- `blocks` is `0`.
- `classic` is at least `8`.
- `overflowX` is `0`.
- `errors` is an empty array or contains only irrelevant browser/network noise that is explained in the final report.

- [ ] **Step 3: Check production guard still passes**

Run:

```bash
node scripts/tests/theme-child-production-contract.mjs
```

Expected: `Child theme production contract checks passed.`

- [ ] **Step 4: Commit validation test updates if not already committed**

Run:

```bash
git add scripts/tests/shop-render-contract.mjs
git commit -m "test: verify upgraded shop rendering"
```

Expected: if Task 1 already committed the test and no changes remain, Git reports nothing to commit; continue.

---

### Task 8: Final Completion Audit

**Files:**
- Inspect:
  - `docs/superpowers/specs/2026-05-31-vivero-shop-ui-ux-design.md`
  - `theme-loscocos-child/archive-product.php`
  - `theme-loscocos-child/woocommerce/content-product.php`
  - `theme-loscocos-child/assets/css/theme.css`
  - `theme-loscocos-child/style.css`
  - `scripts/tests/shop-render-contract.mjs`

- [ ] **Step 1: Verify each spec acceptance item**

Create a short audit table in the final response covering:

```text
Classic shell: evidence from shop-render-contract
No block template: evidence from .wc-block-product count
Classic cards: evidence from ul.products > li.product count
Square images: evidence from first image/card dimensions
Desktop 4 columns: evidence from first-row alignment
Mobile overflow: evidence from overflowX = 0
Featured section: evidence from .lc-featured-product-card count
Sticky controls: evidence from .lc-shop-controls computed position
Console health: evidence from Playwright console capture
Screenshots: /private/tmp/vivero-shop-ui-upgrade-desktop.png and /private/tmp/vivero-shop-ui-upgrade-mobile.png
Production contracts: evidence from theme-child-production-contract
```

- [ ] **Step 2: Review git status**

Run:

```bash
git status --short
```

Expected: only intentional changes remain. Do not revert unrelated user changes.

- [ ] **Step 3: Final response**

Report:

- What changed.
- What was tested.
- Screenshot paths.
- Any remaining risk, especially local product data quality versus production data.
