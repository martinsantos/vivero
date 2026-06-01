# SEO GEO Botanical Foundation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Establish the technical SEO/GEO foundation for Vivero Los Cocos so products and key pages are understandable as commerce, local business, and botanical entities.

**Architecture:** Add a focused `LosCocos_SEO_GEO` module loaded by the child theme. It emits meta descriptions, JSON-LD graph data, and a visible local botanical guide on product pages using existing WooCommerce product data and Los Cocos infographic metadata.

**Tech Stack:** WordPress child theme, WooCommerce, PHP JSON-LD generation, Playwright SEO/GEO contract.

---

### Task 1: SEO/GEO Module

**Files:**
- Create: `theme-loscocos-child/inc/class-seo-geo.php`
- Modify: `theme-loscocos-child/functions.php`

- [x] Add a new class that hooks into `wp_head` for meta description and JSON-LD output.
- [x] Load the class from the existing child theme module list.
- [x] Emit `LocalBusiness`, `Organization`, `WebSite`, `BreadcrumbList`, `Product`, and product `FAQPage` nodes where relevant.

### Task 2: Visible Product GEO Block

**Files:**
- Modify: `theme-loscocos-child/woocommerce/single-product.php`
- Modify: `theme-loscocos-child/assets/css/theme.css`
- Generate: `theme-loscocos-child/style.css`

- [x] Render a visible “Guía local para Mendoza y Cuyo” block on product pages.
- [x] Use product name, category, botanical meta, light/water/care metadata, stock, and Mendoza/Cuyo copy.
- [x] Style it with the approved site visual system.

### Task 3: Validation Contract

**Files:**
- Create: `scripts/tests/seo-geo-contract.mjs`

- [x] Validate homepage JSON-LD includes LocalBusiness, Organization, and WebSite.
- [x] Validate shop/product pages include BreadcrumbList and Product schema.
- [x] Validate product pages include visible local botanical guide and FAQ schema.
- [x] Validate meta descriptions exist and are meaningful.

### Task 4: Verify, Deploy, QA

- [x] Run PHP lint.
- [x] Build CSS and sync active theme.
- [x] Run existing visual contracts.
- [x] Run SEO/GEO contract locally and against production after deploy.
