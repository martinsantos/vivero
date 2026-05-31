# Vivero Los Cocos Shop UI/UX Design

Date: 2026-05-31

## Objective

Improve the WooCommerce shop at `/tienda/` by iterating on the live production design, not replacing it. The result must feel like a stronger version of the current Vivero Los Cocos theme: same identity, same commerce structure, better hierarchy, better product emphasis, and no regression to the broken WooCommerce block-template rendering.

## Evidence Reviewed

- Production canonical URL: `https://viveroloscocos.com.ar/tienda/`
- Local URL: `http://localhost:8080/tienda/`
- Production screenshots:
  - `/private/tmp/vivero-goal-prod-desktop.png`
  - `/private/tmp/vivero-goal-prod-mobile.png`
- Local screenshots:
  - `/private/tmp/vivero-goal-local-desktop.png`
  - `/private/tmp/vivero-goal-local-mobile.png`
- Render evidence showed both production and local now use the classic WooCommerce theme shell:
  - `header#masthead` present
  - `footer` present
  - `ul.products > li.product` present
  - `.wc-block-product` absent

Note: `https://www.viveroloscocos.com.ar/tienda/` returned Cloudflare `530` from this environment, while the canonical non-`www` URL returned `200`.

## Production Baseline To Preserve

The production design has several proportions and behaviors that should remain stable:

- Fixed white header above the shop.
- Dark green shop hero with the `Tienda` headline and a short commerce description.
- Three value blocks inside the hero.
- Category chip strip immediately below the hero.
- White catalog area with breadcrumb, count/order toolbar, classic WooCommerce product grid, and pagination.
- Product cards with square media, 4 columns on desktop, 1 column on mobile.
- Rounded 8px cards with light borders and subtle shadows.
- Primary green add-to-cart CTA and secondary detail CTA.
- Footer structure and dark footer treatment.

These are the theme identity. The redesign must not remove or invert them.

## Preferred Production Qualities

Production reads better than local primarily because of the commercial product data and hierarchy:

- Categories are real commercial buckets, such as `PLANTAS`, `MACETAS`, `FERTILIZANTES`, and related counts.
- Product names are human-readable: for example `Abedul 15 Litros`, `Aloe 3 Litros`, `Aro soporte para plantas 13 Litros`.
- Descriptions explain use context instead of generic filler.
- Prices are valid and visually meaningful.
- Cards feel longer and more informative, which improves trust and scan quality.

Local currently has the correct structure but weaker perceived quality because many cards show `SIN CATEGORIZAR`, SKU-like names, `$ 0,00`, and generic descriptions. The UI layer must make good production data shine while degrading gracefully when local data is incomplete.

## Approved Direction

Use the "editorial-commerce upgrade" approach:

- Preserve the current theme and WooCommerce template structure.
- Add a stronger commercial layer above the grid.
- Improve hierarchy, scan speed, and product confidence without changing the brand.
- Avoid a new visual language, large decorative effects, or marketing-page composition.

This is not a rebuild. It is an iteration on the existing production design.

## User-Facing Design

### 1. Hero

Keep the dark green hero, headline, copy, and broad proportions. Improve the three value blocks so they carry real commerce signals:

- Total visible products.
- Active top-level categories.
- Buying/help promise, such as stock visibility, direct purchase, and real advice.

The hero should still feel calm and utilitarian. It should not become a new landing-page hero.

### 2. Category And Catalog Controls

Keep the category chips directly below the hero. Improve the controls with:

- Stronger active state for the selected category.
- Better horizontal scrolling affordance on mobile.
- A compact catalog toolbar that groups result count and ordering.
- Sticky behavior for the category/control region on desktop once the user reaches the catalog.

Sticky controls must not cover product cards or create scroll traps.

### 3. Featured Products Section

Add a new "Destacados del vivero" section between the toolbar and the full product grid.

Selection priority:

1. Products marked featured.
2. Products on sale.
3. In-stock products ordered by menu order/date as fallback.

Display:

- Desktop: 3 featured cards in a compact horizontal band.
- Mobile: horizontally scrollable cards or a stacked compact list, whichever fits without overflow.

Purpose:

- Give the page a curated entry point before the generic grid.
- Highlight real commerce intent without changing the full catalog behavior.

Fallback:

- If fewer than 3 suitable products exist, render as many as available.
- If none exist, do not show an empty section.

### 4. Product Cards

Keep the square image and existing card proportions. Improve card hierarchy:

- Category label: less noisy, still visible.
- Title: stronger, readable, with stable line height.
- Description: concise, max two to three lines, consistent height.
- Price and stock: clearer grouping, price remains prominent.
- Add-to-cart: primary action stays green and full width.
- Details CTA: secondary, quieter, still accessible.

Data fallbacks:

- If the category is the default category, do not over-emphasize `Sin categorizar`.
- If price is zero or empty, render a calmer fallback such as consultation-oriented text only if this matches WooCommerce store rules. Otherwise preserve WooCommerce price output.
- If short description is missing, keep a restrained fallback but avoid making every card read identical where possible.

### 5. Mobile

Mobile must stay recognizably the same page but reduce fatigue:

- Keep hero, category chips, featured section, toolbar, grid.
- Avoid horizontal overflow.
- Ensure first product/featured content appears after a reasonable amount of scrolling.
- Keep buttons large enough for touch.
- Avoid nested cards and decorative boxes that make the mobile page longer without adding buying value.

## Technical Design

Primary files expected to change:

- `theme-loscocos-child/archive-product.php`
- `theme-loscocos-child/woocommerce/content-product.php`
- `theme-loscocos-child/assets/css/theme.css`
- Compiled/synced `theme-loscocos-child/style.css`
- Active WordPress theme copy under `wp-content/themes/theme-loscocos-child/`
- `scripts/tests/shop-render-contract.mjs`

Implementation notes:

- Continue using classic WooCommerce templates.
- Keep the `woocommerce_has_block_template` guard that prevents `archive-product` from falling back to WooCommerce block templates.
- Use WooCommerce APIs for featured/sale/in-stock selection.
- Avoid hard-coded product IDs.
- Avoid changing product data in the database as part of this UI work.
- Do not depend on production-only content existing locally.

## Testing And Acceptance

The implementation is accepted only when all of the following are true:

- `/tienda/` local renders with `#page.site`, `header#masthead`, and `footer`.
- `/tienda/` local has zero `.wc-block-product` nodes.
- Classic product cards render through `ul.products > li.product`.
- Product images remain square on desktop and mobile.
- Desktop grid remains 4 columns at wide viewport.
- Mobile has no horizontal overflow.
- Featured section appears when eligible products exist and disappears cleanly when not.
- Category/control region does not cover cards when sticky.
- No relevant console errors.
- Screenshots are captured for desktop and mobile after implementation.
- Existing production contract tests still pass.

## Out Of Scope

- Replacing the theme.
- Switching to WooCommerce block templates.
- Redesigning checkout/cart/account pages.
- Changing the product database/import pipeline.
- Creating a new brand identity.
- Adding decorative illustration, gradient blobs, or large marketing-only sections.

## Risks

- Local fixture data is weaker than production data, so visual polish must degrade gracefully.
- Sticky controls can harm mobile usability if not constrained to desktop or carefully scoped.
- Featured queries must be lightweight and avoid interfering with the main WooCommerce loop.

## Approval Status

The user approved the "editorial-commerce upgrade" direction on 2026-05-31 with the instruction: `APROBADO`.
