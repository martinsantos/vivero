# Vivero Los Cocos Visual System

The approved production reference is the current home and shop experience:

- `https://viveroloscocos.com.ar/`
- `https://viveroloscocos.com.ar/tienda/`

Every section must feel like the same site. Do not introduce generic Tailwind/SaaS styling, unrelated fonts, heavy borders, hard gray cards, or a separate WooCommerce admin aesthetic.

## Identity

### Typography
- UI/body: `Inter, Outfit, ui-sans-serif, system-ui, sans-serif`
- Display headings and brand: `Merriweather, serif`
- Headings use normal letter spacing. Do not use negative tracking.
- Body text should stay at `16px` or above for commercial pages.

### Palette
- Canopy: `#0f2f26`
- Canopy 2: `#143a2f`
- Leaf: `#1b4d3e`
- Leaf soft: `#2c7a63`
- Cream: `#fffdf8`
- Cream 2: `#faf8f3`
- Sage: `#e8efe2`
- Clay CTA: `#e07a5f`
- Ink: `#1f342b`
- Muted: `#68766d`

### Surfaces
- Page backgrounds use warm cream-to-white gradients.
- Cards use 8px radius for product/catalog surfaces.
- Shadows should be soft and layered, never heavy black drop shadows.
- Borders should be low contrast and warm green/cream tinted.

## Components

### Primary CTA
Use a rounded full pill in clay/coral with white text and a soft clay shadow.

### Secondary CTA
Use a white or transparent pill with canopy/leaf text and subtle border.

### Product Cards
Product cards use Inter for product name and price. Images are square, object-fit cover, and card hover motion is small: `translateY(-1px)` to `translateY(-4px)` depending on prominence.

### Page Headers
Generic page and commerce headers should use the canopy gradient, Merriweather title, and concise Inter lede. Avoid emoji labels in page headings.

### Forms
Inputs use white/cream backgrounds, 999px or 8px radii depending on context, canopy focus rings, and Inter labels.

## Anti-Patterns

- Do not use default gray Tailwind dashboards as a visual base.
- Do not mix Fraunces or unrelated display fonts.
- Do not make home, shop, product, cart, and checkout look like separate themes.
- Do not add oversized hard borders or boxed sections inside boxed sections.
- Do not use decorative emoji as primary UI chrome.
