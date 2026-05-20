# Catalogo Visual Premium Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a controlled image-quality pipeline for every WooCommerce product before replacing images at scale.

**Architecture:** Add a read-only catalog image audit that classifies each product, detects missing/placeholder/synthetic/duplicate images, and writes JSON, CSV, Markdown, and HTML review outputs. Keep image replacement separate from the audit so production can be changed by approved batches only.

**Tech Stack:** Python 3, WooCommerce REST API, WordPress Application Password/Woo keys from `.env`, existing `scripts/images/wc_image_automation.py` for future approved replacements.

---

### Task 1: Catalog Image Goal Audit

**Files:**
- Create: `scripts/audit/catalog-image-goal-audit.py`
- Output: `logs/catalog-image-goal-audit.json`
- Output: `logs/catalog-image-goal-priority.csv`
- Output: `docs/reports/catalog-image-goal-audit.md`
- Output: `docs/reports/catalog-image-goal-review.html`

- [x] **Step 1: Create a read-only WooCommerce audit**

Implement a script that fetches published products, classifies each product as plant/species, packaged input, pot/accessory, or general product, and calculates image health flags without changing WordPress.

- [x] **Step 2: Emit machine-readable and human-readable reports**

Write JSON for full detail, CSV for prioritization, Markdown for executive summary, and HTML contact sheet for manual review.

- [x] **Step 3: Verify script syntax**

Run: `python3 -m py_compile scripts/audit/catalog-image-goal-audit.py`

- [x] **Step 4: Run production audit read-only**

Run: `python3 scripts/audit/catalog-image-goal-audit.py --limit 0`

Expected: report files are created and no product/image assignment is modified.

### Task 2: Replacement Policy

**Files:**
- Modify: `docs/README-WC-IMAGES.md`

- [x] **Step 1: Document the approved source policy**

Add a section explaining that plants should use species-accurate botanical/nursery photos, packaged inputs should use actual packaging, and macetas/accessories should use model/color/size-correct images.

- [x] **Step 2: Document the safe execution order**

Add commands for dry-run, local curated image matching, candidate review, then approved batch replacement.

### Task 2.5: Alt Text Repair

**Files:**
- Create: `scripts/images/update-product-image-alt-text.py`

- [x] **Step 1: Add a narrow alt updater**

Create a script that reads `logs/catalog-image-goal-audit.json`, skips shared media IDs, and updates only missing/weak WordPress media alt text when run with `--apply`.

- [x] **Step 2: Verify dry-run first**

Run: `python3 scripts/images/update-product-image-alt-text.py`

Expected: JSON preview with candidate count and no WordPress writes.

- [x] **Step 3: Apply alt text via WP-CLI when REST lacks media edit permission**

Export candidates to `logs/product-image-alt-updates.json`, upload the JSON to the server, and run `scripts/php/apply-product-image-alt-updates.php` with WP-CLI.

- [x] **Step 4: Re-audit after metadata repair**

Run the full audit again with `--perceptual` and confirm `missing_alt` falls from 422 to 4.

### Task 3: Batch Replacement Guardrails

**Files:**
- Modify: `scripts/images/wc_image_automation.py`

- [x] **Step 1: Add an optional product-id allowlist argument**

Add `--product-ids` so approved reports can drive a small replacement batch instead of processing the entire catalog.

- [x] **Step 2: Add source-policy hints to generated queries**

Use product classification from the audit/reporting layer to separate plant/species, packaged inputs, pots/accessories, and approved visual variants before any replacement batch.

- [x] **Step 3: Verify dry-run only**

Verify `wc_image_automation.py --help` exposes `--product-ids` and use WP-CLI-only replacement scripts for the approved curated image batch.

### Task 4: Curated P1 Corrections

**Files:**
- Create: `data/catalog-image-duplicate-approvals.json`
- Create: `scripts/images/generate-curated-accessory-renders.py`
- Create: `scripts/php/apply-curated-product-images.php`
- Output: `assets/product-images/curated/`

- [x] **Step 1: Register approved visual duplicate groups**

Record variant groups that are visually acceptable so the audit reports only unapproved risks.

- [x] **Step 2: Generate curated replacement images for wrong accessories and internal-code products**

Create explicit SKU/product-id keyed WebP images for products whose current image was wrong or duplicated.

- [x] **Step 3: Apply replacements by WP-CLI**

Upload curated images and set them as featured images for products `22`, `89`, `185`, `189`, `303`, `511`, `516`, and `61112`.

- [x] **Step 4: Final audit**

Run the full audit and confirm `555/555` products are `OK`, no missing featured images, no missing alt text, no duplicate media IDs, no duplicate files, and no unapproved visual duplicate groups.
