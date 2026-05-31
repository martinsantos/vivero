# Shop Production UI/UX Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Align the local shop with the current production typography and proportions, then improve catalog focus, featured-product density, toolbar lightness, and card polish without changing the theme identity.

**Architecture:** Keep the existing classic WooCommerce archive template and child-theme CSS. Update the Playwright render contract first, then modify `archive-product.php` markup minimally and tune the scoped `.lc-shop-*` CSS so the catalog reaches products earlier and cards use production-like proportions.

**Tech Stack:** WordPress child theme, WooCommerce classic loop, Tailwind-generated CSS via PostCSS, Playwright render contract tests.

---

### Task 1: Protect Production Proportions In The Render Contract

**Files:**
- Modify: `scripts/tests/shop-render-contract.mjs`

- [ ] **Step 1: Add proportion data to the snapshot**

In the `page.evaluate()` return object, keep the existing style snapshots and ensure these existing fields remain available:

```js
featuredSectionStyle: styleFor('.lc-featured-products'),
featuredCardStyle: styleFor('.lc-featured-product-card'),
firstCardRect: rectFor('ul.products > li.product'),
firstImageRect: rectFor('ul.products > li.product img'),
controlsRect: rectFor('.lc-shop-controls'),
```

Add these numeric fields:

```js
catalogToolbarRect: rectFor('.lc-shop-toolbar'),
featuredSectionRect: rectFor('.lc-featured-products'),
productGridRect: rectFor('ul.products'),
```

- [ ] **Step 2: Add failing assertions for the current regression**

After the existing typography assertions, add desktop-only checks:

```js
if (viewport.name === 'desktop') {
  if (snapshot.firstCardRect?.top > 930) {
    recordFailure('La primera fila de productos debe aparecer mucho antes; los destacados no pueden demorar el catalogo.', { viewport, snapshot });
  }

  const imageRatio = snapshot.firstImageRect && snapshot.firstCardRect
    ? snapshot.firstImageRect.width / snapshot.firstCardRect.width
    : 0;

  if (imageRatio < 0.96) {
    recordFailure('La imagen de producto debe recuperar escala cercana a produccion dentro de la card.', { viewport, snapshot, imageRatio });
  }

  if (snapshot.catalogToolbarRect?.height > 70) {
    recordFailure('La toolbar de catalogo debe ser mas compacta y liviana que la regresion actual.', { viewport, snapshot });
  }

  if (snapshot.featuredSectionRect?.height > 190) {
    recordFailure('Los destacados deben ser una franja comercial compacta, no una seccion dominante.', { viewport, snapshot });
  }
}
```

- [ ] **Step 3: Run the test and confirm it fails before implementation**

Run:

```bash
node scripts/tests/shop-render-contract.mjs
```

Expected: FAIL on first product row top, image ratio, toolbar height, or featured height.

- [ ] **Step 4: Commit the failing contract**

```bash
git add scripts/tests/shop-render-contract.mjs
git commit -m "test: protect production shop proportions"
```

### Task 2: Compact Featured Products And Restore Catalog Priority

**Files:**
- Modify: `theme-loscocos-child/archive-product.php`
- Modify: `theme-loscocos-child/assets/css/theme.css`
- Generated: `theme-loscocos-child/style.css`

- [ ] **Step 1: Keep existing featured data but make the section compact**

In `theme-loscocos-child/archive-product.php`, keep the current featured-products loop, heading, and product data. Do not remove the section. The markup may remain structurally unchanged, because CSS will convert it into a compact strip.

- [ ] **Step 2: Update CSS for compact featured section**

In `theme-loscocos-child/assets/css/theme.css`, change the featured section rules to:

```css
.lc-featured-products {
  margin-bottom: 1.35rem;
  padding: 1rem 1.1rem;
  border: 1px solid rgba(224, 232, 219, 0.32);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.72);
  box-shadow: 0 1px 2px rgba(18, 34, 27, 0.026);
}

.lc-featured-products__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 0.8rem;
}

.lc-featured-products h2 {
  margin: 0;
  color: var(--lc-shop-canopy);
  font-family: var(--lc-shop-font-display);
  font-size: 1.55rem;
  font-weight: 700;
  letter-spacing: 0;
  line-height: 1.08;
}

.lc-featured-products__grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.85rem;
}

.lc-featured-product-card {
  display: grid;
  grid-template-columns: 88px minmax(0, 1fr);
  min-height: 104px;
  overflow: hidden;
  border: 1px solid rgba(224, 233, 218, 0.34);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.9);
  box-shadow: 0 1px 2px rgba(18, 34, 27, 0.028);
  transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
}
```

Adjust inner featured media/body typography so cards remain compact:

```css
.lc-featured-product-card__media {
  margin: 0.42rem 0 0.42rem 0.42rem;
  border-radius: 6px;
}

.lc-featured-product-card__body {
  padding: 0.72rem 0.85rem;
}

.lc-featured-product-card h3 {
  font-size: 0.94rem;
  line-height: 1.18;
}
```

- [ ] **Step 3: Run the contract and expect remaining failures**

Run:

```bash
node scripts/tests/shop-render-contract.mjs
```

Expected: featured height should improve; other assertions may still fail until Task 3.

- [ ] **Step 4: Build generated CSS**

Run:

```bash
npm run build
```

from `theme-loscocos-child`.

- [ ] **Step 5: Commit compact featured work**

```bash
git add theme-loscocos-child/archive-product.php theme-loscocos-child/assets/css/theme.css theme-loscocos-child/style.css
git commit -m "style: compact shop featured products"
```

### Task 3: Lighten Toolbar And Restore Product Image Proportions

**Files:**
- Modify: `theme-loscocos-child/assets/css/theme.css`
- Generated: `theme-loscocos-child/style.css`

- [ ] **Step 1: Make the catalog toolbar production-like**

In `theme-loscocos-child/assets/css/theme.css`, tune `.lc-shop-toolbar`:

```css
.lc-shop-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.35rem;
  padding: 0.78rem 1rem;
  border: 1px solid rgba(224, 232, 219, 0.42);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.88);
  box-shadow: 0 1px 2px rgba(18, 34, 27, 0.026);
}
```

Keep `.lc-shop-toolbar__eyebrow` visible but quiet:

```css
.lc-shop-toolbar__eyebrow {
  margin: 0 0 0.16rem;
  color: var(--lc-shop-muted);
  font-size: 0.66rem;
  font-weight: 700;
  text-transform: uppercase;
}
```

- [ ] **Step 2: Restore image dominance in product cards**

Change product card media spacing:

```css
.lc-shop-page .lc-product-card__media {
  margin: 1px 1px 0;
  border-radius: 8px 8px 0 0;
}
```

Keep image fit stable:

```css
.lc-shop-page .lc-product-card img {
  transform: scale(1) !important;
  transition: transform 220ms ease;
}
```

- [ ] **Step 3: Soft-polish card shadows and borders**

Tune cards:

```css
.lc-shop-page ul.products li.product.lc-product-card,
.lc-shop-page .woocommerce ul.products li.product.lc-product-card {
  transform: translateY(0) !important;
  box-shadow:
    0 1px 2px rgba(18, 34, 27, 0.035),
    0 10px 24px rgba(18, 34, 27, 0.035) !important;
  transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
}
```

- [ ] **Step 4: Build and run the render contract**

Run:

```bash
npm run build
node scripts/tests/shop-render-contract.mjs
```

Expected: PASS.

- [ ] **Step 5: Commit toolbar/card polish**

```bash
git add theme-loscocos-child/assets/css/theme.css theme-loscocos-child/style.css
git commit -m "style: refine shop catalog proportions"
```

### Task 4: Sync Active Theme And Validate Rendered UI

**Files:**
- Copy to active ignored theme: `wp-content/themes/theme-loscocos-child/archive-product.php`
- Copy to active ignored theme: `wp-content/themes/theme-loscocos-child/assets/css/theme.css`
- Copy to active ignored theme: `wp-content/themes/theme-loscocos-child/style.css`

- [ ] **Step 1: Copy source theme changes to active theme**

Run:

```bash
cp theme-loscocos-child/archive-product.php wp-content/themes/theme-loscocos-child/archive-product.php
cp theme-loscocos-child/assets/css/theme.css wp-content/themes/theme-loscocos-child/assets/css/theme.css
cp theme-loscocos-child/style.css wp-content/themes/theme-loscocos-child/style.css
```

- [ ] **Step 2: Verify active files match**

Run:

```bash
cmp -s theme-loscocos-child/archive-product.php wp-content/themes/theme-loscocos-child/archive-product.php
cmp -s theme-loscocos-child/assets/css/theme.css wp-content/themes/theme-loscocos-child/assets/css/theme.css
cmp -s theme-loscocos-child/style.css wp-content/themes/theme-loscocos-child/style.css
```

Expected: all exit 0.

- [ ] **Step 3: Run PHP and contract checks**

Run:

```bash
php -l theme-loscocos-child/archive-product.php
node scripts/tests/shop-render-contract.mjs
node scripts/tests/theme-child-production-contract.mjs
```

Expected: all pass.

- [ ] **Step 4: Capture desktop and mobile screenshots**

Run a temporary Playwright script from the shell that captures:

```text
/private/tmp/vivero-shop-production-uiux-desktop.png
/private/tmp/vivero-shop-production-uiux-mobile.png
```

and reports:

```js
{
  firstCardTop,
  imageRatio,
  toolbarHeight,
  featuredHeight,
  overflowX
}
```

Expected desktop targets:

- `firstCardTop <= 930`
- `imageRatio >= 0.96`
- `toolbarHeight <= 70`
- `featuredHeight <= 190`
- `overflowX === 0`

- [ ] **Step 5: Review final status**

Run:

```bash
git status -sb
git log --oneline -5
```

Expected: only `wp-config.php` remains dirty; latest commits show the spec, test, and style changes.
