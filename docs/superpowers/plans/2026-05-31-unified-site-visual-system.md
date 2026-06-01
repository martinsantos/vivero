# Unified Site Visual System Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make every public section of Vivero Los Cocos feel like one website, using the approved production home and shop aesthetic as the baseline.

**Architecture:** Keep `/` and `/tienda/` as the approved visual reference. Add global Los Cocos tokens and shared commerce/page surfaces, then bring product, cart, checkout, account, footer, and generic pages into that system without rewriting their flows.

**Tech Stack:** WordPress child theme, WooCommerce templates, Tailwind/PostCSS-generated CSS, Playwright render contracts.

---

### Task 1: Lock Global Tokens And Documentation

**Files:**
- Modify: `theme-loscocos-child/STYLEGUIDE.md`
- Modify: `theme-loscocos-child/assets/css/theme.css`

- [x] Replace the old generic Tailwind styleguide with the production visual system: Inter UI, Merriweather display, canopy green, cream backgrounds, clay/coral CTAs, 8px commerce cards, soft shadows.
- [x] Add global `:root`, `body.lc-site`, `.lc-page`, `.lc-commerce-page`, and shared WooCommerce form/button/notice rules.

### Task 2: Give Shared Templates The Unified Shell

**Files:**
- Modify: `theme-loscocos-child/header.php`
- Modify: `theme-loscocos-child/footer.php`
- Modify: `theme-loscocos-child/page.php`
- Modify: `theme-loscocos-child/woocommerce.php`
- Modify: `theme-loscocos-child/woocommerce/single-product.php`

- [x] Add `lc-site` to the body shell.
- [x] Move generic pages to `lc-page`.
- [x] Move WooCommerce non-archive pages to `lc-commerce-page`.
- [x] Move single product to `lc-product-page` with softer surfaces and matching typography.
- [x] Bring footer into the same canopy/cream/clay palette.

### Task 3: Protect Rendered Consistency

**Files:**
- Create: `scripts/tests/site-visual-contract.mjs`

- [x] Add a Playwright contract that checks home, shop, product, cart, and a generic page for Inter/Merriweather, no horizontal overflow, header/footer presence, and shared visual classes.
- [x] Keep screenshots in `/private/tmp`, outside the repo.

### Task 4: Build, Sync, Verify

**Files:**
- Modify generated: `theme-loscocos-child/style.css`
- Sync ignored active theme: `wp-content/themes/theme-loscocos-child/*`

- [x] Run CSS build.
- [x] Sync source theme files to the active theme copy.
- [x] Run PHP lint and render contracts.
- [x] Deploy only after verification passes.
