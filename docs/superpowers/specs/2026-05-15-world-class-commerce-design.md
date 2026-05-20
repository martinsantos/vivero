# Los Cocos World-Class Commerce Design

## Goal

Convert Vivero Los Cocos into a world-class nursery e-commerce site: fast, secure, trustworthy, visually consistent, easy to buy from, and commercially useful for customers choosing plants, pots, fertilizers, and garden supplies.

The project explicitly excludes the `/jardin` garden designer. All effort goes into the core WooCommerce buying journey.

## Success Metrics

- 0 PHP syntax errors in the active child theme.
- `npm run build` works from `theme-loscocos-child`.
- 0 products without a valid price.
- 100% active products have SKU, price, stock status, category, image, short description, and basic care/use attributes.
- Public debug endpoints removed or restricted.
- No real SSH, database, WooCommerce, WordPress, or API credentials in committed docs or scripts.
- Login hardening remains active: `/ingre`, direct `wp-login.php` blocked, XML-RPC blocked, application passwords disabled unless explicitly re-enabled.
- Mobile Lighthouse targets: Performance 90+, SEO 95+, Best Practices 95+, Accessibility 90+ on Home, Shop, Product, Cart, Checkout.
- Critical purchase journey works on mobile and desktop: Home -> Shop -> Product -> Add to Cart -> Cart -> Checkout.
- Product pages answer: what it is, price, size/presentation, care needs, where it works, shipping/guarantee, and how to ask for help.

## Current State Summary

Production uses:

- Active stylesheet: `theme-loscocos-child`
- Parent template: `theme-loscocos`
- Active plugin observed: WooCommerce

Repository issues found:

- `theme-loscocos-child/assets/css/theme.css` is deleted, so the CSS build pipeline fails.
- `theme-loscocos-child/woocommerce/myaccount/dashboard.php` has a PHP syntax error.
- `data/productos_sin_precio.csv` lists 499 products without price.
- Several templates mix Tailwind classes, inline styles, emojis, and old visual experiments.
- There are debug MU plugins in `wp-content/mu-plugins`.
- Documentation and scripts contain real or historical infrastructure secrets.
- Multiple theme generations coexist, making active ownership unclear.

## Product Principles

1. Sell clearly before decorating.
2. Every product card must communicate price, stock, category, image, and next action.
3. Every product page must reduce buyer uncertainty.
4. Checkout must feel predictable and safe.
5. Visual polish must come from a shared design system, not one-off inline styling.
6. Security and operational checks are part of the product, not an afterthought.

## Architecture Direction

The active child theme becomes the only production UI surface. Old theme experiments are archived or documented as inactive, not used as shared source.

The child theme gets a reproducible CSS pipeline:

- `assets/css/theme.css` is restored as Tailwind input.
- `style.css` is generated output.
- Component-specific CSS is kept only when it cannot reasonably be represented with the theme system.
- Inline styles in templates are removed over time and replaced with named classes.

WooCommerce templates remain in the child theme only where they provide real business value. Templates that simply duplicate WooCommerce defaults are removed or minimized to reduce maintenance risk.

## Core Surfaces

### Home

Purpose: introduce the brand, route customers into the catalog, surface promotions, and build trust.

Required sections:

- Real brand signal above the fold.
- Primary category paths: plants, pots, fertilizers/care, offers.
- Featured products driven by actual catalog data.
- Trust strip: local delivery, plant quality guarantee, WhatsApp guidance.
- Seasonal or commercial collection, but only if products are available and priced.

### Shop

Purpose: let customers find products quickly.

Required behavior:

- Category navigation by buying intent.
- Search visible and useful.
- Product cards with stable image ratio, category, title, price, stock, and add-to-cart or detail CTA.
- Empty/filter states that guide the user.
- Mobile-first layout with no text overlap.

### Product

Purpose: answer buyer questions and convert.

Required content:

- Image gallery with consistent framing.
- Price, stock, SKU, category.
- Short description in plain Spanish.
- Care/use attributes: light, water, difficulty, presentation/size, ideal location/use when applicable.
- Shipping, guarantee, and WhatsApp help affordances.
- Related products that are in stock and priced.

### Cart

Purpose: confirm selection and move to checkout.

Required behavior:

- Product list with image, name, unit price, quantity, subtotal, remove.
- Totals and shipping expectation.
- Continue shopping and checkout CTAs.
- Mobile layout without horizontal overflow.

### Checkout

Purpose: complete the purchase with minimum friction.

Required behavior:

- Clear customer fields.
- Clear delivery notes.
- Clear order review.
- Visible payment/security reassurance.
- No hidden or broken WooCommerce notices.

### Account

Purpose: support order review and customer self-service without breaking WooCommerce.

Required behavior:

- No PHP errors.
- No over-customized templates unless needed.
- Login, orders, addresses, account details, reset password all work.

## Catalog Requirements

Every active product needs:

- SKU
- Name
- Regular price or sale price
- Stock status
- Product category
- Main image
- Short description
- Long description or care/use section
- Attributes when relevant:
  - presentation/size
  - light
  - watering
  - use/location
  - difficulty
  - season or product family

Data work should be scripted and repeatable. Manual edits are acceptable only for final copy polish.

## Security Requirements

Immediate hardening remains:

- `/ingre` as login URL.
- Direct `/wp-login.php` blocked.
- XML-RPC blocked.
- Application passwords disabled.
- Anonymous user enumeration blocked.

Additional requirements:

- Remove or protect debug REST endpoints.
- Remove hardcoded credentials from docs/scripts.
- Add a security audit script that checks exposed endpoints and secret patterns.
- Add Cloudflare/WAF recommendations for `/ingre`.
- Confirm only expected admin users exist.

## Performance Requirements

- CSS build must be reproducible.
- No external stock photos in primary commerce sections unless intentionally kept and optimized.
- Product images should use WordPress image sizes and lazy loading, with priority only for above-the-fold hero/product image.
- JavaScript should be scoped to pages that need it.
- Avoid duplicate header scroll scripts and duplicate product gallery behavior.

## Testing Requirements

Minimum repeatable checks:

- PHP lint for all active child theme PHP files.
- `npm run build` for CSS.
- HTTP checks for home, shop, product, cart, checkout.
- WooCommerce CLI or REST checks for product counts and missing data.
- Security endpoint checks for `/wp-login.php`, `/xmlrpc.php`, `/wp-json/wp/v2/users`, and debug routes.
- Browser visual QA for mobile and desktop on Home, Shop, Product, Cart, Checkout.

## Phases

### Phase 1: Base Firme

Repair build, PHP errors, security exposure, active theme ownership, and audit scripts.

### Phase 2: Catalogo Campeon

Fix pricing, product data completeness, category taxonomy, descriptions, care attributes, and image consistency.

### Phase 3: Commerce UX Mundial

Unify Home, Shop, Product, Cart, Checkout, and Account around one visual system and one purchase journey.

### Phase 4: SEO Comercial

Create commercial landing paths and metadata around real purchase intent, not only score-based SEO.

### Phase 5: Operacion Continua

Automate weekly health checks for catalog quality, build status, PHP lint, exposed endpoints, broken images, and checkout smoke tests.

## Non-Goals

- Do not build or improve `/jardin`.
- Do not add unrelated marketing pages before checkout and catalog are reliable.
- Do not add new plugins for core behavior unless a plugin is the simplest stable option.
- Do not rewrite WooCommerce itself.
- Do not chase visual novelty at the expense of buying clarity.
