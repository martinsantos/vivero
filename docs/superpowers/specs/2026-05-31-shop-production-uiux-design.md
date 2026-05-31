# Shop Production UI/UX Design

## Goal

Bring the local shop UI back under the current production design contract from `https://viveroloscocos.com.ar/` and `https://viveroloscocos.com.ar/tienda/`, then improve its commercial polish without changing the identity, theme, typography direction, or buying flow.

## Production Reference

Production is the source of truth for this iteration:

- Typography: Inter for UI, controls, product cards, and body copy; Merriweather-style serif for major display headings.
- Palette: deep forest green, warm white/cream backgrounds, muted sage dividers, restrained terracotta accents.
- Shape language: 8px or smaller radii for cards and tool surfaces; rounded category pills are acceptable for filters.
- Surface treatment: light borders, low-opacity shadows, no heavy outlines, no decorative gradient blobs.
- Catalog priority: the shop must get users to products quickly.

Observed production shop proportions at 1440px desktop:

- Hero height: about 486px.
- Category strip starts around y=506.
- First product row starts around y=788.
- Product grid: 4 columns, 294px card width.
- Product images: about 292x292.
- Product cards: light 8px radius and subtle shadow.

Observed local gap before this iteration:

- Hero is close, but local catalog starts later.
- First product row starts around y=1095 because the local featured block interrupts the catalog.
- Product images are smaller, about 274x274.
- Toolbar is heavier and taller than production.
- Local featured products are useful but visually too dominant for the shop page.

## Design Direction

The local shop should feel like a refined version of production, not a redesign. The work should remove friction and excess chrome before adding any new visual effect.

The first viewport should show the same production hierarchy:

1. Header.
2. Deep green shop hero with serif title and Inter supporting copy.
3. Category strip.
4. Breadcrumb and compact catalog toolbar.
5. Product grid as the primary commercial surface.

Featured products may remain, but they must not block the catalog. They should become a compact, optional commercial row that supports discovery without pushing the first product row several hundred pixels down.

## Components

### Hero

Keep the current production-aligned hero typography and palette:

- Title uses Merriweather/serif at production scale.
- Eyebrow, lede, and metrics use Inter.
- Metrics remain useful, but their copy and visual weight should stay quiet.
- Height must stay close to production and avoid growing from added content.

### Category Strip

Keep a production-like horizontal pill strip:

- Active state uses deep green with white text.
- Inactive state uses white/cream background, subtle border, dark text.
- Desktop should remain a single readable row when category count allows.
- Mobile should scroll horizontally without overflow.

### Catalog Toolbar

Make the toolbar closer to production:

- Lower height and lower visual weight.
- Keep product count and ordering control.
- Avoid large card-like chrome that competes with products.
- Keep sticky desktop behavior only if it remains visually quiet.

### Featured Products

Move from a dominant section to a compact commercial strip:

- Maximum 3 products.
- Low height.
- Smaller heading.
- Inline or horizontal layout above the grid only if it does not delay product discovery.
- No large editorial card treatment before the catalog.
- If there are not useful eligible products, the section should collapse cleanly.

### Product Cards

Preserve the existing WooCommerce loop and improve polish:

- Keep 4 columns desktop and 1 column mobile.
- Restore image dominance close to production: image should be near full card width.
- Use 8px radius or less.
- Use softer shadows and borders.
- Keep Inter-first product typography.
- Price, stock, primary CTA, and secondary detail action remain visible and stable.
- Hover should be subtle: small lift, slight image scale, no loud transitions.

## Constraints

- Do not change the brand identity.
- Do not introduce a different theme.
- Do not reintroduce Fraunces/Outfit as the primary shop visual direction.
- Do not remove WooCommerce classic rendering.
- Do not break current header, footer, checkout, cart, or product loop behavior.
- Do not commit `wp-config.php`.

## Testing

The implementation must update or add contracts that protect:

- Production typography direction: Inter-first UI and Merriweather/serif display headings.
- No WooCommerce block template regression.
- Desktop first product row appears materially earlier than the current local y=1095 regression.
- Product images are close to production scale relative to card width.
- No horizontal overflow on desktop or mobile.
- Toolbar remains present and usable.
- Featured section does not dominate the first catalog viewport.

Rendered validation must include:

- `http://localhost:8080/tienda/` desktop 1440px.
- `http://localhost:8080/tienda/` mobile around 390px.
- Screenshot comparison against production reference.
- Console health check.

## Out of Scope

- Data cleanup for product categories and stock counts.
- Product photography replacement.
- Checkout redesign.
- Header/footer redesign beyond regressions caused by shop changes.
