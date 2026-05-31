# Vivero Shop Premium Visual Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Upgrade `/tienda/` from a stable WooCommerce catalog into a premium botanical commerce page with stronger typography, richer palette, clearer hierarchy, softer surfaces, and better mobile rhythm.

**Architecture:** Keep the current classic WooCommerce child-theme templates and product loop. Add shop-specific semantic classes for typography/layout hooks, load the approved display typeface, replace the current shop CSS refinement block with a more deliberate premium visual system, then sync the active mounted theme copy and verify with Playwright contracts and screenshots.

**Tech Stack:** WordPress, WooCommerce classic templates, PHP, Tailwind/PostCSS child theme build, Google Fonts, Playwright render contract.

---

## File Structure

- Modify `scripts/tests/shop-render-contract.mjs`
  - Owns regression checks for classic rendering, console health, visual typography hooks, premium hierarchy, featured fallback, product image/card proportions, sticky controls, and mobile overflow.
- Modify `theme-loscocos-child/inc/class-theme-setup.php`
  - Owns Google Fonts enqueue URL and font resource hints.
- Modify `theme-loscocos-child/tailwind.config.js`
  - Owns theme font-family tokens used by templates and generated CSS.
- Modify `theme-loscocos-child/archive-product.php`
  - Owns shop archive markup and semantic classes for hero, controls, toolbar, featured products, and catalog wrapper.
- Modify `theme-loscocos-child/woocommerce/content-product.php`
  - Owns product card semantic classes and stable product-card hierarchy.
- Modify `theme-loscocos-child/assets/css/theme.css`
  - Owns the source premium shop visual system.
- Generated `theme-loscocos-child/style.css`
  - Built from `assets/css/theme.css` with `npm run build`.
- Sync ignored active files under `wp-content/themes/theme-loscocos-child/`
  - Local WordPress at `localhost:8080` reads this mounted copy.

---

### Task 1: Strengthen The Visual Contract Before Styling

**Files:**
- Modify: `scripts/tests/shop-render-contract.mjs`

- [ ] **Step 1: Add visual style extraction to the render snapshot**

In `scripts/tests/shop-render-contract.mjs`, extend the `styleFor` helper inside `page.evaluate()` so it returns font and border details. Replace the current returned object in `styleFor` with:

```js
return {
  display: styles.display,
  gridTemplateColumns: styles.gridTemplateColumns,
  borderColor: styles.borderColor,
  borderRadius: styles.borderRadius,
  boxShadow: styles.boxShadow,
  overflow: styles.overflow,
  position: styles.position,
  backgroundColor: styles.backgroundColor,
  backgroundImage: styles.backgroundImage,
  color: styles.color,
  fontFamily: styles.fontFamily,
  fontSize: styles.fontSize,
  fontWeight: styles.fontWeight,
  letterSpacing: styles.letterSpacing,
  lineHeight: styles.lineHeight,
};
```

- [ ] **Step 2: Add premium visual fields to the snapshot**

Still inside the snapshot object returned by `page.evaluate()`, add these fields after `hasCatalogToolbar`:

```js
hasPremiumShopPage: Boolean(document.querySelector('.lc-shop-page')),
heroTitleStyle: styleFor('.lc-shop-hero__title'),
heroEyebrowStyle: styleFor('.lc-shop-hero__eyebrow'),
heroMetricStyle: styleFor('.lc-shop-hero-metric'),
featuredHeadingStyle: styleFor('.lc-featured-products h2'),
featuredCardStyle: styleFor('.lc-featured-product-card'),
featuredCategories: Array.from(document.querySelectorAll('.lc-featured-product-card__category'), (node) => node.textContent.trim()),
productTitleStyle: styleFor('.lc-product-card__title'),
productPriceStyle: styleFor('.lc-product-card__price'),
productPrimaryActionStyle: styleFor('.lc-product-card__actions .button'),
catalogSectionStyle: styleFor('.lc-shop-catalog'),
```

- [ ] **Step 3: Add premium visual assertions**

After the featured-section count assertion and before card visual assertions, insert:

```js
if (!snapshot.hasPremiumShopPage) {
  recordFailure('La tienda debe exponer la clase premium lc-shop-page para aplicar el sistema visual comercial.', { viewport, snapshot });
}

const usesFraunces = (style) => style?.fontFamily?.toLowerCase().includes('fraunces');
const usesOutfit = (style) => style?.fontFamily?.toLowerCase().includes('outfit');

if (!usesFraunces(snapshot.heroTitleStyle) || !usesFraunces(snapshot.featuredHeadingStyle)) {
  recordFailure('La tienda debe usar Fraunces como tipografia display comercial en hero y destacados.', { viewport, snapshot });
}

if (!usesOutfit(snapshot.productTitleStyle) || !usesOutfit(snapshot.productPriceStyle)) {
  recordFailure('Las cards deben usar Outfit para nombre y precio de producto.', { viewport, snapshot });
}

if (snapshot.featuredCategories.some((category) => /sin categor/i.test(category))) {
  recordFailure('Los destacados no deben mostrar Sin categorizar como categoria comercial.', { viewport, snapshot });
}

if (snapshot.productPrimaryActionStyle?.backgroundColor === 'rgba(0, 0, 0, 0)') {
  recordFailure('El CTA principal de producto debe conservar un fondo comercial visible.', { viewport, snapshot });
}
```

- [ ] **Step 4: Run the contract and confirm it fails before implementation**

Run:

```bash
node scripts/tests/shop-render-contract.mjs
```

Expected: FAIL with `La tienda debe exponer la clase premium lc-shop-page para aplicar el sistema visual comercial.` or `La tienda debe usar Fraunces como tipografia display comercial en hero y destacados.`.

- [ ] **Step 5: Commit the failing visual contract**

Run:

```bash
git add scripts/tests/shop-render-contract.mjs
git commit -m "test: cover premium shop visual system"
```

Expected: commit includes only `scripts/tests/shop-render-contract.mjs`.

---

### Task 2: Load Premium Typography Tokens

**Files:**
- Modify: `theme-loscocos-child/inc/class-theme-setup.php`
- Modify: `theme-loscocos-child/tailwind.config.js`

- [ ] **Step 1: Update the Google Fonts enqueue URL**

In `theme-loscocos-child/inc/class-theme-setup.php`, replace the current Google Fonts URL:

```php
'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700;900&family=Outfit:wght@300;400;500;700&display=swap',
```

with:

```php
'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700;900&family=Outfit:wght@400;500;600;700;800&display=swap',
```

- [ ] **Step 2: Add Tailwind font tokens**

In `theme-loscocos-child/tailwind.config.js`, replace the current `fontFamily` object:

```js
fontFamily: {
  sans: ['Inter', 'Outfit', 'ui-sans-serif', 'system-ui', 'sans-serif'],
  serif: ['Merriweather', 'serif'],
},
```

with:

```js
fontFamily: {
  sans: ['Outfit', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
  display: ['Fraunces', 'Georgia', 'serif'],
  serif: ['Fraunces', 'Merriweather', 'Georgia', 'serif'],
},
```

- [ ] **Step 3: Run PHP lint**

Run:

```bash
php -l theme-loscocos-child/inc/class-theme-setup.php
```

Expected: `No syntax errors detected in theme-loscocos-child/inc/class-theme-setup.php`.

- [ ] **Step 4: Commit typography loading**

Run:

```bash
git add theme-loscocos-child/inc/class-theme-setup.php theme-loscocos-child/tailwind.config.js
git commit -m "style: load premium shop typography"
```

Expected: commit includes only the font enqueue and Tailwind token changes.

---

### Task 3: Add Semantic Shop Visual Hooks

**Files:**
- Modify: `theme-loscocos-child/archive-product.php`
- Modify: `theme-loscocos-child/woocommerce/content-product.php`

- [ ] **Step 1: Add premium shop classes to the archive shell**

In `theme-loscocos-child/archive-product.php`, replace:

```php
<main id="primary" class="site-main bg-cream-light min-h-screen">
    <section class="bg-primary text-white pt-28 pb-12 md:pt-32 md:pb-16">
```

with:

```php
<main id="primary" class="lc-shop-page site-main bg-cream-light min-h-screen">
    <section class="lc-shop-hero bg-primary text-white pt-28 pb-12 md:pt-32 md:pb-16">
```

- [ ] **Step 2: Add hero typography classes**

In the hero intro block, replace:

```php
<p class="text-sm font-semibold uppercase tracking-widest text-secondary-light mb-4">
```

with:

```php
<p class="lc-shop-hero__eyebrow text-sm font-semibold uppercase text-secondary-light mb-4">
```

Then replace:

```php
<h1 class="text-4xl md:text-6xl font-serif font-bold leading-tight mb-5">
```

with:

```php
<h1 class="lc-shop-hero__title text-4xl md:text-6xl font-display font-bold leading-tight mb-5">
```

Then replace:

```php
<p class="text-lg md:text-xl text-white/85 max-w-3xl leading-relaxed">
```

with:

```php
<p class="lc-shop-hero__lede text-lg md:text-xl text-white/85 max-w-3xl leading-relaxed">
```

- [ ] **Step 3: Add catalog section class**

Replace:

```php
<section class="container mx-auto px-4 py-10 md:py-14">
```

with:

```php
<section class="lc-shop-catalog container mx-auto px-4 py-10 md:py-14">
```

- [ ] **Step 4: Add classes to stock badges in product cards**

In `theme-loscocos-child/woocommerce/content-product.php`, replace the in-stock badge:

```php
<span class="rounded-full bg-secondary-light px-3 py-1 text-xs font-semibold text-primary-dark">En stock</span>
```

with:

```php
<span class="lc-product-card__stock rounded-full bg-secondary-light px-3 py-1 text-xs font-semibold text-primary-dark">En stock</span>
```

Replace the out-of-stock badge:

```php
<span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-medium">Sin stock</span>
```

with:

```php
<span class="lc-product-card__stock lc-product-card__stock--out rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-medium">Sin stock</span>
```

- [ ] **Step 5: Run PHP lint**

Run:

```bash
php -l theme-loscocos-child/archive-product.php
php -l theme-loscocos-child/woocommerce/content-product.php
```

Expected:

```text
No syntax errors detected in theme-loscocos-child/archive-product.php
No syntax errors detected in theme-loscocos-child/woocommerce/content-product.php
```

- [ ] **Step 6: Commit semantic hooks**

Run:

```bash
git add theme-loscocos-child/archive-product.php theme-loscocos-child/woocommerce/content-product.php
git commit -m "style: add premium shop markup hooks"
```

Expected: commit includes only the two PHP template files.

---

### Task 4: Replace The Shop CSS With A Premium Visual System

**Files:**
- Modify: `theme-loscocos-child/assets/css/theme.css`
- Generated: `theme-loscocos-child/style.css`

- [ ] **Step 1: Replace the existing shop archive CSS block**

In `theme-loscocos-child/assets/css/theme.css`, replace the entire block from:

```css
/* Shop archive refinement */
```

through the closing `}` of the mobile `@media (max-width: 767px)` block immediately before:

```css
/* Single product safety layer */
```

with this block:

```css
/* Premium shop visual system */
.lc-shop-page {
  --lc-shop-canopy: #0f2f26;
  --lc-shop-canopy-2: #143a2f;
  --lc-shop-leaf: #1b4d3e;
  --lc-shop-leaf-soft: #2c7a63;
  --lc-shop-cream: #fffdf8;
  --lc-shop-cream-2: #faf8f3;
  --lc-shop-sage: #e8efe2;
  --lc-shop-moss: #dce8d5;
  --lc-shop-clay: #c96a4a;
  --lc-shop-ink: #1f342b;
  --lc-shop-muted: #68766d;
  --lc-shop-font-sans: Outfit, Inter, ui-sans-serif, system-ui, sans-serif;
  --lc-shop-font-display: Fraunces, Georgia, serif;
  background:
    linear-gradient(180deg, #fffdf8 0%, #fbfaf5 42%, #fff 100%);
  color: var(--lc-shop-ink);
  font-family: var(--lc-shop-font-sans);
}

.lc-shop-hero {
  position: relative;
  overflow: hidden;
  background:
    linear-gradient(135deg, #0f2f26 0%, #143a2f 58%, #1b4d3e 100%) !important;
}

.lc-shop-hero::after {
  position: absolute;
  inset: auto 0 0;
  height: 1px;
  content: "";
  background: rgba(255, 255, 255, 0.18);
}

.lc-shop-hero > .container {
  position: relative;
  z-index: 1;
}

.lc-shop-hero__eyebrow,
.lc-shop-toolbar__eyebrow,
.lc-featured-products__eyebrow,
.lc-product-card__category {
  letter-spacing: 0.06em;
}

.lc-shop-hero__eyebrow {
  color: rgba(232, 239, 226, 0.82) !important;
  font-family: var(--lc-shop-font-sans);
  font-size: 0.78rem;
  font-weight: 800;
}

.lc-shop-hero__title {
  max-width: 12ch;
  color: #fff;
  font-family: var(--lc-shop-font-display);
  font-size: 3.15rem;
  font-weight: 750;
  letter-spacing: 0;
  line-height: 0.95;
}

.lc-shop-hero__lede {
  color: rgba(255, 255, 255, 0.82) !important;
  font-family: var(--lc-shop-font-sans);
  font-size: 1.05rem;
  line-height: 1.58;
}

@media (min-width: 768px) {
  .lc-shop-hero__title {
    font-size: 5.35rem;
  }

  .lc-shop-hero__lede {
    font-size: 1.24rem;
  }
}

.lc-shop-hero-metrics {
  max-width: 1100px;
}

.lc-shop-hero-metric {
  min-height: 92px;
  border-color: rgba(255, 255, 255, 0.1) !important;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.115), rgba(255, 255, 255, 0.055)) !important;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.16),
    0 12px 34px rgba(4, 22, 16, 0.12);
}

.lc-shop-hero-metric strong {
  font-family: var(--lc-shop-font-sans);
  font-size: 1rem;
  font-weight: 800;
}

.lc-shop-hero-metric span {
  color: rgba(255, 255, 255, 0.68) !important;
  line-height: 1.45;
}

.lc-shop-controls {
  z-index: 30;
  border-color: rgba(220, 230, 214, 0.45) !important;
  background: rgba(255, 253, 248, 0.96) !important;
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 0.9) inset,
    0 1px 2px rgba(22, 42, 32, 0.035);
}

@media (min-width: 1024px) {
  .lc-shop-controls {
    position: sticky;
    top: 80px;
  }
}

.lc-category-strip {
  align-items: center;
}

.lc-category-strip a {
  border-color: rgba(216, 226, 210, 0.54) !important;
  background: rgba(255, 255, 255, 0.82);
  color: var(--lc-shop-ink) !important;
  box-shadow: 0 1px 2px rgba(18, 36, 28, 0.03);
}

.lc-category-strip a.bg-primary {
  border-color: rgba(15, 47, 38, 0.96) !important;
  background: var(--lc-shop-canopy) !important;
  color: #fff !important;
  box-shadow:
    0 1px 1px rgba(8, 30, 22, 0.12),
    0 6px 16px rgba(15, 47, 38, 0.16);
}

.lc-category-strip a:hover {
  border-color: rgba(44, 122, 99, 0.5) !important;
  box-shadow: 0 5px 14px rgba(27, 77, 62, 0.08);
}

.lc-shop-catalog {
  background:
    linear-gradient(180deg, rgba(255, 253, 248, 0.86), rgba(255, 255, 255, 0));
}

.lc-shop-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.7rem;
  padding: 1.05rem 1.2rem;
  border: 1px solid rgba(224, 232, 219, 0.34);
  border-radius: 8px;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(250, 253, 246, 0.9));
  box-shadow:
    0 1px 1px rgba(18, 34, 27, 0.03),
    0 12px 28px rgba(18, 34, 27, 0.045),
    inset 0 1px 0 rgba(255, 255, 255, 0.86);
}

.lc-shop-toolbar__eyebrow {
  margin: 0 0 0.28rem;
  color: var(--lc-shop-muted);
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
}

.lc-shop-toolbar__count {
  margin: 0;
  color: var(--lc-shop-ink);
  font-size: 1rem;
  font-weight: 800;
}

.lc-shop-toolbar__ordering .woocommerce-ordering {
  float: none;
  margin: 0;
}

.lc-shop-toolbar__ordering select {
  min-height: 42px;
  border-color: rgba(209, 222, 202, 0.72);
  border-radius: 999px;
  background-color: rgba(255, 255, 255, 0.92);
  color: var(--lc-shop-ink);
  font-family: var(--lc-shop-font-sans);
  font-size: 0.86rem;
  font-weight: 700;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.86),
    0 1px 2px rgba(18, 34, 27, 0.04);
}

.lc-featured-products {
  margin-bottom: 2.25rem;
  padding: 1.35rem;
  border: 1px solid rgba(220, 230, 214, 0.28);
  border-radius: 8px;
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 252, 243, 0.9) 100%);
  box-shadow:
    0 1px 1px rgba(18, 34, 27, 0.03),
    0 16px 38px rgba(18, 34, 27, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

.lc-featured-products__header {
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.15rem;
}

.lc-featured-products__eyebrow {
  margin: 0 0 0.28rem;
  color: var(--lc-shop-clay);
  font-size: 0.72rem;
  font-weight: 900;
  text-transform: uppercase;
}

.lc-featured-products h2 {
  margin: 0;
  color: var(--lc-shop-canopy);
  font-family: var(--lc-shop-font-display);
  font-size: 1.55rem;
  font-weight: 750;
  letter-spacing: 0;
  line-height: 1.04;
}

@media (min-width: 768px) {
  .lc-featured-products h2 {
    font-size: 2.15rem;
  }
}

.lc-featured-products__link {
  color: var(--lc-shop-leaf);
  font-size: 0.9rem;
  font-weight: 800;
  text-decoration: none;
}

.lc-featured-products__link:hover {
  color: var(--lc-shop-clay);
}

.lc-featured-products__grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.lc-featured-product-card {
  display: grid;
  grid-template-columns: 122px minmax(0, 1fr);
  min-height: 146px;
  overflow: hidden;
  border: 1px solid rgba(224, 233, 218, 0.3);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.94);
  box-shadow:
    0 1px 1px rgba(18, 34, 27, 0.032),
    0 5px 14px rgba(18, 34, 27, 0.042);
  transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
}

.lc-featured-product-card:hover,
.lc-featured-product-card:focus-within {
  transform: translateY(-1px);
  border-color: rgba(201, 106, 74, 0.2);
  box-shadow:
    0 1px 2px rgba(18, 34, 27, 0.045),
    0 10px 24px rgba(18, 34, 27, 0.07);
}

.lc-featured-product-card__media {
  position: relative;
  display: block;
  min-height: 100%;
  margin: 0.48rem 0 0.48rem 0.48rem;
  overflow: hidden;
  border-radius: 6px;
  background: var(--lc-shop-sage);
}

.lc-featured-product-card__media::after {
  position: absolute;
  inset: 0;
  content: "";
  box-shadow: inset -1px 0 0 rgba(255, 255, 255, 0.42);
  pointer-events: none;
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
  margin: 0 0 0.36rem;
  color: var(--lc-shop-leaf-soft);
  font-size: 0.67rem;
  font-weight: 900;
  letter-spacing: 0.055em;
  text-transform: uppercase;
}

.lc-featured-product-card h3 {
  margin: 0;
  color: var(--lc-shop-canopy);
  font-family: var(--lc-shop-font-sans);
  font-size: 1.02rem;
  font-weight: 800;
  line-height: 1.22;
  overflow-wrap: anywhere;
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
  color: var(--lc-shop-ink);
  font-weight: 900;
}

.lc-featured-product-card__meta small {
  flex-shrink: 0;
  border-radius: 999px;
  background: #eef7ed;
  padding: 0.2rem 0.56rem;
  color: var(--lc-shop-leaf);
  font-size: 0.67rem;
  font-weight: 800;
}

.lc-product-card {
  border-color: rgba(224, 232, 219, 0.2) !important;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(253, 255, 250, 0.96)) !important;
}

.woocommerce ul.products li.product.lc-product-card {
  box-shadow:
    0 1px 1px rgba(18, 34, 27, 0.026),
    0 4px 12px rgba(18, 34, 27, 0.036),
    0 18px 34px rgba(18, 34, 27, 0.032) !important;
}

.lc-product-card__media {
  margin: 0.55rem 0.55rem 0;
  border-radius: 6px;
}

.lc-product-card:hover,
.lc-product-card:focus-within {
  border-color: rgba(183, 202, 174, 0.48) !important;
}

.woocommerce ul.products li.product.lc-product-card:hover,
.woocommerce ul.products li.product.lc-product-card:focus-within {
  box-shadow:
    0 1px 2px rgba(18, 34, 27, 0.045),
    0 6px 18px rgba(18, 34, 27, 0.055),
    0 18px 38px rgba(18, 34, 27, 0.065) !important;
}

.lc-product-card__media::after {
  position: absolute;
  inset: 0;
  content: "";
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0) 58%, rgba(12, 32, 25, 0.035) 100%);
  box-shadow: inset 0 -1px 0 rgba(12, 32, 25, 0.055);
  pointer-events: none;
}

.lc-product-card__category {
  color: var(--lc-shop-leaf-soft);
  font-size: 0.69rem;
  font-weight: 900;
  letter-spacing: 0.055em;
}

.lc-product-card__title {
  color: var(--lc-shop-canopy);
  font-family: var(--lc-shop-font-sans);
  font-size: 1.08rem;
  font-weight: 800;
}

.lc-product-card__description {
  display: -webkit-box;
  overflow: hidden;
  color: var(--lc-shop-muted);
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}

.lc-product-card__price {
  color: var(--lc-shop-ink);
  font-family: var(--lc-shop-font-sans);
  font-size: 1.28rem;
  font-weight: 900;
}

.lc-product-card__stock {
  background: #eef7ed !important;
  color: var(--lc-shop-leaf) !important;
}

.lc-product-card__stock--out {
  background: #f4f1ec !important;
  color: var(--lc-shop-muted) !important;
}

.lc-product-card__actions .button {
  background: var(--lc-shop-leaf) !important;
  box-shadow:
    0 1px 1px rgba(17, 94, 70, 0.08),
    0 5px 14px rgba(27, 77, 62, 0.16);
}

.lc-product-card__actions .button:hover {
  background: var(--lc-shop-canopy) !important;
  box-shadow:
    0 1px 2px rgba(17, 94, 70, 0.1),
    0 8px 20px rgba(27, 77, 62, 0.2);
}

.lc-product-card__actions a:not(.button) {
  border-color: rgba(217, 226, 211, 0.72) !important;
  background: rgba(255, 255, 255, 0.74);
}

@media (prefers-reduced-motion: reduce) {
  .lc-featured-product-card,
  .lc-product-card,
  .lc-product-card img {
    transition: none !important;
  }

  .lc-featured-product-card:hover,
  .lc-featured-product-card:focus-within,
  .lc-product-card:hover,
  .lc-product-card:focus-within {
    transform: none !important;
  }
}

@media (max-width: 767px) {
  .lc-shop-page {
    background: #fffdf8;
  }

  .lc-shop-hero__title {
    font-size: 3rem;
  }

  .lc-shop-hero-metric {
    min-height: auto;
  }

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

  .lc-product-card__title {
    font-size: 1.04rem;
  }
}
```

- [ ] **Step 2: Build compiled CSS**

Run from `theme-loscocos-child`:

```bash
npm run build
```

Expected: build exits `0`. The existing stale Browserslist warning is acceptable; PostCSS errors are not.

- [ ] **Step 3: Confirm compiled CSS includes premium selectors**

Run:

```bash
rg -n "lc-shop-page|Fraunces|lc-shop-hero__title|lc-featured-products|lc-product-card__stock|prefers-reduced-motion" theme-loscocos-child/style.css
```

Expected: matches for all selector groups.

- [ ] **Step 4: Commit source and compiled CSS**

Run:

```bash
git add theme-loscocos-child/assets/css/theme.css theme-loscocos-child/style.css
git commit -m "style: apply premium shop visual system"
```

Expected: commit includes source CSS and generated CSS only.

---

### Task 5: Sync Active WordPress Theme Copy

**Files:**
- Modify ignored active copy:
  - `wp-content/themes/theme-loscocos-child/archive-product.php`
  - `wp-content/themes/theme-loscocos-child/woocommerce/content-product.php`
  - `wp-content/themes/theme-loscocos-child/assets/css/theme.css`
  - `wp-content/themes/theme-loscocos-child/style.css`
  - `wp-content/themes/theme-loscocos-child/inc/class-theme-setup.php`
  - `wp-content/themes/theme-loscocos-child/tailwind.config.js`

- [ ] **Step 1: Copy source files into the mounted active theme**

Run:

```bash
cp theme-loscocos-child/archive-product.php wp-content/themes/theme-loscocos-child/archive-product.php
cp theme-loscocos-child/woocommerce/content-product.php wp-content/themes/theme-loscocos-child/woocommerce/content-product.php
cp theme-loscocos-child/assets/css/theme.css wp-content/themes/theme-loscocos-child/assets/css/theme.css
cp theme-loscocos-child/style.css wp-content/themes/theme-loscocos-child/style.css
cp theme-loscocos-child/inc/class-theme-setup.php wp-content/themes/theme-loscocos-child/inc/class-theme-setup.php
cp theme-loscocos-child/tailwind.config.js wp-content/themes/theme-loscocos-child/tailwind.config.js
```

Expected: commands produce no output.

- [ ] **Step 2: Verify mounted files match source files**

Run:

```bash
cmp -s theme-loscocos-child/archive-product.php wp-content/themes/theme-loscocos-child/archive-product.php
cmp -s theme-loscocos-child/woocommerce/content-product.php wp-content/themes/theme-loscocos-child/woocommerce/content-product.php
cmp -s theme-loscocos-child/assets/css/theme.css wp-content/themes/theme-loscocos-child/assets/css/theme.css
cmp -s theme-loscocos-child/style.css wp-content/themes/theme-loscocos-child/style.css
cmp -s theme-loscocos-child/inc/class-theme-setup.php wp-content/themes/theme-loscocos-child/inc/class-theme-setup.php
cmp -s theme-loscocos-child/tailwind.config.js wp-content/themes/theme-loscocos-child/tailwind.config.js
```

Expected: all commands exit `0`.

- [ ] **Step 3: Run production guard**

Run:

```bash
node scripts/tests/theme-child-production-contract.mjs
```

Expected: `Child theme production contract checks passed.`

- [ ] **Step 4: Commit if mounted files are tracked**

Run:

```bash
git add wp-content/themes/theme-loscocos-child/archive-product.php wp-content/themes/theme-loscocos-child/woocommerce/content-product.php wp-content/themes/theme-loscocos-child/assets/css/theme.css wp-content/themes/theme-loscocos-child/style.css wp-content/themes/theme-loscocos-child/inc/class-theme-setup.php wp-content/themes/theme-loscocos-child/tailwind.config.js
git commit -m "chore: sync active premium shop theme files"
```

Expected: if Git reports the mounted path is ignored, do not force-add. Continue with a note that active files were synced but not committed.

---

### Task 6: Verify Premium Render And Visual Metrics

**Files:**
- Test: `scripts/tests/shop-render-contract.mjs`

- [ ] **Step 1: Run the upgraded render contract**

Run:

```bash
node scripts/tests/shop-render-contract.mjs
```

Expected:

```text
Shop render contract passed for http://localhost:8080/tienda/
```

- [ ] **Step 2: Capture final screenshots and computed visual evidence**

Run:

```bash
node --input-type=module -e 'import { chromium } from "playwright"; const browser = await chromium.launch({ headless: true }); for (const target of [{name:"desktop", viewport:{width:1440,height:1100}}, {name:"mobile", viewport:{width:390,height:844}, isMobile:true}]) { const page = await browser.newPage({ viewport: target.viewport, isMobile: Boolean(target.isMobile), deviceScaleFactor: 1 }); const errors = []; page.on("console", msg => { if (["error","warning"].includes(msg.type())) errors.push({ type: msg.type(), text: msg.text() }); }); page.on("pageerror", error => errors.push({ type: "pageerror", text: error.message })); await page.goto("http://localhost:8080/tienda/", { waitUntil: "networkidle", timeout: 45000 }); await page.screenshot({ path: `/private/tmp/vivero-shop-premium-visual-${target.name}.png`, fullPage: true }); const metrics = await page.evaluate(() => { const style = (selector) => { const node = document.querySelector(selector); if (!node) return null; const s = getComputedStyle(node); return { fontFamily: s.fontFamily, fontSize: s.fontSize, fontWeight: s.fontWeight, color: s.color, backgroundColor: s.backgroundColor, boxShadow: s.boxShadow, borderColor: s.borderColor, position: s.position }; }; const rect = (selector) => { const node = document.querySelector(selector); if (!node) return null; const r = node.getBoundingClientRect(); return { width: Math.round(r.width), height: Math.round(r.height), top: Math.round(r.top), left: Math.round(r.left) }; }; const cards = Array.from(document.querySelectorAll("ul.products > li.product")); return { title: document.title, featured: document.querySelectorAll(".lc-featured-product-card").length, blocks: document.querySelectorAll(".wc-block-product").length, classic: cards.length, overflowX: document.documentElement.scrollWidth - document.documentElement.clientWidth, heroTitle: style(".lc-shop-hero__title"), featuredHeading: style(".lc-featured-products h2"), productTitle: style(".lc-product-card__title"), productPrice: style(".lc-product-card__price"), primaryAction: style(".lc-product-card__actions .button"), controls: style(".lc-shop-controls"), firstCard: rect("ul.products > li.product"), firstImage: rect("ul.products > li.product img"), firstRowTops: cards.slice(0, 4).map((card) => Math.round(card.getBoundingClientRect().top)) }; }); console.log(JSON.stringify({ viewport: target.name, metrics, errors }, null, 2)); await page.close(); } await browser.close();'
```

Expected:

- Desktop `blocks` is `0`.
- Desktop `classic` is at least `8`.
- Desktop `featured` is between `1` and `3`.
- Desktop `overflowX` is `0`.
- Desktop `heroTitle.fontFamily` includes `Fraunces`.
- Desktop `featuredHeading.fontFamily` includes `Fraunces`.
- Desktop `productTitle.fontFamily` includes `Outfit`.
- Desktop first-row tops for 4 product cards are identical.
- Mobile `overflowX` is `0`.
- `errors` is `[]` for both viewports.
- Screenshots exist:
  - `/private/tmp/vivero-shop-premium-visual-desktop.png`
  - `/private/tmp/vivero-shop-premium-visual-mobile.png`

- [ ] **Step 3: Run static guard again**

Run:

```bash
node scripts/tests/theme-child-production-contract.mjs
```

Expected: `Child theme production contract checks passed.`

- [ ] **Step 4: Commit test updates if needed**

Run:

```bash
git add scripts/tests/shop-render-contract.mjs
git commit -m "test: verify premium shop visual rendering"
```

Expected: if Task 1 already committed the contract and no changes remain, Git reports nothing to commit; continue.

---

### Task 7: Final Visual Audit

**Files:**
- Inspect:
  - `docs/superpowers/specs/2026-05-31-vivero-shop-premium-visual-design.md`
  - `theme-loscocos-child/inc/class-theme-setup.php`
  - `theme-loscocos-child/tailwind.config.js`
  - `theme-loscocos-child/archive-product.php`
  - `theme-loscocos-child/woocommerce/content-product.php`
  - `theme-loscocos-child/assets/css/theme.css`
  - `theme-loscocos-child/style.css`
  - `scripts/tests/shop-render-contract.mjs`

- [ ] **Step 1: Review acceptance coverage**

Create a final audit note covering these checks:

```text
Typography: hero and featured headings use Fraunces; product title and price use Outfit.
Palette: deep canopy, warm cream, sage/moss, green actions, terracotta accent are present and restrained.
Hierarchy: hero > toolbar > featured > grid reads in clear order.
Product cards: square images, quieter category labels, stronger title/price, visible CTA.
Featured products: distinct from grid, no Sin categorizar, 1-3 cards.
Borders: low-contrast hairlines; shadows provide depth.
Mobile: no overflow, no console errors, stable tap targets.
WooCommerce classic: shell present, zero .wc-block-product.
Screenshots: /private/tmp/vivero-shop-premium-visual-desktop.png and /private/tmp/vivero-shop-premium-visual-mobile.png.
```

- [ ] **Step 2: Review git status**

Run:

```bash
git status --short
```

Expected: only pre-existing `wp-config.php` remains modified. Do not stage or print it.

- [ ] **Step 3: Final response**

Report:

- What changed visually.
- What tests passed.
- Screenshot paths.
- Remaining risk: local fixture data quality is still weaker than production.
- Commit list for the premium visual work.
