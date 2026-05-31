# Vivero Los Cocos Shop Premium Visual Design

Date: 2026-05-31

## Goal

Move `/tienda/` from a stable WooCommerce catalog into a more commercial, attractive, premium nursery storefront. The work must preserve the current Vivero Los Cocos theme, classic WooCommerce templates, product loop, cart behavior, and production proportions, but it must materially improve the visual language: typography, palette, hierarchy, surfaces, product desirability, and mobile rhythm.

This is not another structural repair pass. The next iteration is an aesthetic system pass.

## Current Problem

The shop is now functional and no longer broken, but it still reads too much like a WooCommerce template with CSS layered on top:

- Typography is serviceable but not commercial enough.
- The hierarchy between hero, toolbar, featured products, product names, prices, and CTAs is too mechanical.
- The palette is mostly green plus white, with not enough tonal range or accent discipline.
- Cards still rely too heavily on boxes, borders, and repeated modules.
- Product imagery is present, but the composition does not yet make products feel curated or desirable.
- Mobile is stable, but visually long and repetitive.

The next work must make the page feel like a real online shop for a trusted nursery.

## Context To Preserve

Preserve all of the following:

- Classic WooCommerce theme shell.
- Zero WooCommerce block-template fallback.
- Current production identity: dark green, botanical, local, practical, commerce-first.
- Existing `/tienda/` structure: hero, category chips, toolbar, featured products, product grid, pagination, footer.
- Product card behavior: square imagery, price, stock, add-to-cart, details CTA.
- Desktop 4-column product grid.
- Mobile no-horizontal-overflow rule.
- Active mounted theme sync workflow under `wp-content/themes/theme-loscocos-child`.

## Approaches Considered

### Option A: Cosmetic Polish Only

Only soften borders, shadows, and spacing.

Tradeoff: low risk, but the result remains generic. This is not enough for the user request.

### Option B: Premium Visual System Pass

Define a tighter shop-specific visual system for type, color, rhythm, cards, featured products, and interaction states, then apply it across the current templates.

Tradeoff: more work, but it addresses the actual issue without replacing the theme.

Recommended.

### Option C: Full Brand Redesign

Replace the identity, layout model, and visual language across the whole site.

Tradeoff: too broad and too risky. The user explicitly wants to improve the existing theme, not break it.

Rejected for this phase.

## Approved Direction

Use Option B: a premium visual system pass.

Target feel:

- Botanical commerce, not SaaS dashboard.
- Warm, confident, editorial, but still practical.
- More like a curated nursery catalog than a generic product grid.
- Less bordered and boxy; more tonal, layered, and image-led.
- Commercial enough to make products feel buyable, not just listed.

## Typography Direction

The current stack uses `Inter`, `Outfit`, and `Merriweather`. That is workable, but the hierarchy is not using typography hard enough.

The next pass should make typography a deliberate commerce asset:

- Use a more expressive display face for hero and section headings.
- Keep a highly legible sans for product names, prices, buttons, controls, and body text.
- Avoid excessive weights. Use size, line-height, spacing, and contrast before adding more font weights.
- Product names need stronger presence than descriptions, but should not become oversized.
- Price should be visually decisive and easier to scan.
- Eyebrows and labels should be quieter; they should organize, not shout.

Preferred implementation direction:

- Primary commerce sans: `Outfit`, because it is already loaded and warmer than `Inter`.
- Display/editorial headings: use `Fraunces` for hero and section headings, loaded through the existing Google Fonts enqueue path.
- `Merriweather` should be reduced or removed from the shop surface unless it remains necessary as a stable fallback.
- Keep fallbacks stable: `ui-sans-serif`, `system-ui`, `Georgia`, and `serif`.

Acceptance:

- Hero heading, section headings, product titles, price, and CTA text have distinct roles.
- No viewport-width font scaling.
- Letter spacing remains `0` except small uppercase labels where tracking is intentionally restrained.
- Text never overlaps or escapes buttons/cards on desktop or mobile.

## Palette Direction

The current palette has good raw materials but reads too flat. The next pass should expand tonal range without becoming colorful noise.

Use a restrained botanical palette:

- Deep canopy: `#0F2F26` / `#143A2F` for hero, footer, and strong text.
- Leaf green: `#1B4D3E` / `#2C7A63` for primary actions and active states.
- Warm cream: `#FAF8F3` / `#FFFDF8` for page backgrounds and soft sections.
- Sage/moss: `#E8EFE2` / `#DCE8D5` for quiet surfaces.
- Terracotta accent: around `#C96A4A` or current `#E07A5F`, used sparingly for links, hover accents, and editorial highlights.
- Pollen/gold accent only if needed for small badges or seasonal emphasis, never as a dominant theme.

Rules:

- Avoid one-note green everywhere.
- Avoid high-contrast gray borders as the main separation mechanism.
- Use color to establish hierarchy: dark for authority, cream for warmth, green for action, terracotta for emphasis.
- CTAs stay green, but with better depth and contrast.

Acceptance:

- The page is no longer visually dominated by white cards with visible borders.
- Active controls are obvious.
- Product CTAs remain the strongest repeated action.
- Accent colors are sparse and purposeful.

## Layout And Rhythm

The page should feel composed instead of stacked.

Hero:

- Keep current production proportions.
- Improve typography scale and spacing.
- Make the three value metrics feel integrated into the hero, not three separate mini-cards.

Controls:

- Category strip should feel like a refined buying control, not tags floating in a blank band.
- Sticky desktop behavior remains, but the surface should be subtle.
- Mobile controls should avoid adding height without buying value.

Featured section:

- Must feel curated.
- Should use stronger product imagery and typography than the normal grid.
- Should not look like a smaller copy of product cards.

Grid:

- Keep 4 columns desktop.
- Improve card rhythm: image, title, description, price, actions should read in a clear buying order.
- Reduce visible repetitive outlines.

Mobile:

- Reduce fatigue by tightening nonessential whitespace.
- Keep tap targets large.
- Featured section must not create page overflow.

## Product Card Design

Cards need to move from "list item in a box" to "buyable product surface."

Required card qualities:

- Image remains square but framed with better art direction.
- Product name and price become the primary scan anchors.
- Category label remains but is quieter.
- Description should support the decision, not dominate the card.
- Stock badge should be visible but not compete with price.
- Add-to-cart remains a strong primary action.
- Details CTA should be quieter and more elegant.

Surface rules:

- 8px radius stays unless an existing theme rule requires otherwise.
- Borders should be hairline and low contrast, not dark outlines.
- Use layered shadows at low opacity.
- Avoid nested-card appearance.
- Hover should be subtle: small lift or tonal shift, not dramatic movement.

## Featured Product Design

The featured band should become the page's main commercial hook after the hero.

Required:

- Visually distinct from the regular grid.
- Uses 1 to 3 eligible products.
- Filters weak category fallback just like normal cards.
- Has a stronger section title and a clearer path to the full catalog.
- Mobile treatment can be a contained carousel or compact list, but must avoid overflow.

Acceptance:

- It feels intentionally curated even with weak local fixture data.
- It does not show `Sin categorizar`.
- It does not duplicate the normal grid card style.

## Motion And Interaction

Motion should be quiet and useful:

- Hover states should raise contrast, not lower it.
- Cards can lift by 1px only.
- Button shadow/contrast can increase on hover.
- Image hover can gently scale, but avoid dramatic zoom.
- Respect `prefers-reduced-motion` if new motion is added.

No scroll hijacking, page snap, decorative blobs, or generic AI-gradient effects.

## Technical Scope

Expected files:

- `theme-loscocos-child/assets/css/theme.css`
- `theme-loscocos-child/style.css`
- `theme-loscocos-child/tailwind.config.js`
- `theme-loscocos-child/inc/class-theme-setup.php`
- `theme-loscocos-child/archive-product.php`
- `theme-loscocos-child/woocommerce/content-product.php`
- active mounted copies under `wp-content/themes/theme-loscocos-child`
- `scripts/tests/shop-render-contract.mjs`

Possible changes:

- Adjust font enqueue URL.
- Adjust Tailwind `fontFamily` tokens.
- Add shop-specific visual classes.
- Add test assertions for typography, no console errors, no overflow, featured fallback, and key computed styles.

Do not:

- Change product database content.
- Change cart/checkout/account pages in this phase.
- Replace WooCommerce classic templates with blocks.
- Add a large landing-page redesign above the shop.
- Add heavy JS for purely visual polish.

## Testing And Visual QA

Required automated checks:

- `npm run build` from `theme-loscocos-child`.
- `php -l theme-loscocos-child/archive-product.php`.
- `php -l theme-loscocos-child/woocommerce/content-product.php`.
- `node scripts/tests/shop-render-contract.mjs`.
- `node scripts/tests/theme-child-production-contract.mjs`.

Required browser QA:

- Desktop screenshot at `1440x1100`.
- Mobile screenshot at `390x844`.
- Verify no horizontal overflow.
- Verify zero `.wc-block-product`.
- Verify 4-column desktop grid remains.
- Verify product images remain square.
- Verify console warnings/errors are empty.
- Visually inspect that cards no longer read as hard-bordered boxes.
- Visually inspect that mobile does not become longer without added commercial value.

## Acceptance Criteria

The work is accepted when `/tienda/` reads as a premium botanical commerce page while preserving the current theme:

- The typography feels deliberate and commercial.
- The palette has richer tonal range and restrained accent use.
- The hero, toolbar, featured band, and grid have clear hierarchy.
- Product cards feel buyable, not generic.
- Featured products feel curated.
- Borders are not the main visual device.
- Mobile is stable, readable, and less fatiguing.
- All existing classic WooCommerce protections still pass.

## Out Of Scope

- New brand identity.
- New logo.
- Checkout/cart/account redesign.
- Product import or product data cleanup.
- SEO content pages.
- Replacing all site typography outside the commerce surfaces unless required for consistency.

## Risks

- Adding a new font can hurt performance if not loaded carefully.
- A richer palette can become noisy if accents are overused.
- Better visual hierarchy cannot fully compensate for weak local fixture data.
- CSS specificity can fight Tailwind utility classes if not scoped carefully.

## Approval Status

The user approved this premium visual direction on 2026-05-31 with the instruction: `APROBADO`.
