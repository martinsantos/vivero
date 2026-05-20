#!/usr/bin/env node
import fs from "node:fs/promises";
import path from "node:path";
import { chromium } from "playwright";

const BASE_URL = process.env.LOSCOCOS_BASE_URL || "https://viveroloscocos.com.ar";
const OUT_DIR = process.env.LOSCOCOS_AUDIT_OUT || "/private/tmp/vivero-visual-audit";
const CATEGORIES = [
  "macetas",
  "plantas",
  "insecticidas",
  "fertilizantes",
  "herbicidas",
  "funguicidas",
  "molusquicidas",
];

function categoryPageUrl(slug, pageNumber) {
  const base = `${BASE_URL}/product-category/${slug}/`;
  return pageNumber === 1 ? base : `${base}page/${pageNumber}/`;
}

async function scrollPage(page) {
  await page.evaluate(async () => {
    const step = Math.max(500, Math.floor(window.innerHeight * 0.75));
    for (let y = 0; y < document.body.scrollHeight; y += step) {
      window.scrollTo(0, y);
      await new Promise((resolve) => setTimeout(resolve, 90));
    }
    window.scrollTo(0, 0);
  });
  await page.waitForTimeout(350);
}

async function imageLoadResults(page, selector, options = {}) {
  const images = await page.$$eval(selector, (nodes) =>
    nodes.map((img) => ({
      src: img.currentSrc || img.src,
      alt: img.alt || "",
      domNaturalWidth: img.naturalWidth,
      domNaturalHeight: img.naturalHeight,
      width: Math.round(img.getBoundingClientRect().width),
      height: Math.round(img.getBoundingClientRect().height),
      x: Math.round(img.getBoundingClientRect().x),
      y: Math.round(img.getBoundingClientRect().y),
    })).filter((item) => item.src)
  );
  const filtered = images.filter((item) => {
    if (options.minVisibleSize && (item.width < options.minVisibleSize || item.height < options.minVisibleSize)) {
      return false;
    }
    return true;
  });

  return page.evaluate(async (items) => {
    const checks = await Promise.all(items.map((item) => new Promise((resolve) => {
      const image = new Image();
      image.onload = () => resolve({ ...item, ok: image.naturalWidth > 0 && image.naturalHeight > 0, naturalWidth: image.naturalWidth, naturalHeight: image.naturalHeight });
      image.onerror = () => resolve({ ...item, ok: false, naturalWidth: 0, naturalHeight: 0 });
      image.src = item.src;
    })));
    return checks;
  }, filtered);
}

function summarizeImages(images) {
  const broken = images.filter((image) => !image.ok);
  const legacy = images.filter((image) => image.src.includes("wcimg_"));
  const unsplash = images.filter((image) => image.src.includes("images.unsplash.com"));
  const tiny = images.filter((image) => image.ok && (image.naturalWidth < 180 || image.naturalHeight < 180));
  return {
    count: images.length,
    broken: broken.length,
    legacy: legacy.length,
    unsplash: unsplash.length,
    tiny: tiny.length,
    broken_samples: broken.slice(0, 5),
    legacy_samples: legacy.slice(0, 5).map((image) => ({ src: image.src, alt: image.alt })),
  };
}

async function auditCatalogPage(page, url, name, screenshot = false, selector = "li.product img, .products img") {
  const response = await page.goto(url, { waitUntil: "domcontentloaded", timeout: 60000 });
  const status = response?.status() || 0;
  if (status >= 400) {
    return { name, url, status, images: summarizeImages([]) };
  }
  await scrollPage(page);
  const images = await imageLoadResults(page, selector);
  if (screenshot) {
    await page.screenshot({ path: path.join(OUT_DIR, `${name}.png`), fullPage: false });
  }
  return { name, url, status, images: summarizeImages(images) };
}

async function crawlCategory(page, slug) {
  const pages = [];
  for (let i = 1; i <= 40; i++) {
    const url = categoryPageUrl(slug, i);
    const result = await auditCatalogPage(page, url, `${slug}-page-${i}`, i === 1 || i % 5 === 0);
    if (result.status === 404) break;
    pages.push(result);
    if (result.images.count === 0 && i > 1) break;
  }
  return {
    slug,
    pages,
    totals: pages.reduce(
      (acc, item) => ({
        count: acc.count + item.images.count,
        broken: acc.broken + item.images.broken,
        legacy: acc.legacy + item.images.legacy,
        unsplash: acc.unsplash + item.images.unsplash,
        tiny: acc.tiny + item.images.tiny,
      }),
      { count: 0, broken: 0, legacy: 0, unsplash: 0, tiny: 0 }
    ),
  };
}

async function auditProduct(page, sample) {
  const response = await page.goto(sample.url, { waitUntil: "networkidle", timeout: 60000 });
  const status = response?.status() || 0;
  const mainImages = await imageLoadResults(page, "main img, .site-main img, article img, .product img", { minVisibleSize: 180 });
  const buttons = await page.$$eval("button, a.button, input[type='submit']", (nodes) =>
    nodes
      .map((node) => ({ text: node.textContent.trim() || node.value || "", disabled: node.disabled, visible: !!(node.offsetWidth || node.offsetHeight || node.getClientRects().length) }))
      .filter((item) => /carrito|comprar|agregar/i.test(item.text))
  );
  await page.screenshot({ path: path.join(OUT_DIR, `product-${sample.category}-${sample.id}.png`), fullPage: false });
  return {
    ...sample,
    status,
    main_images: summarizeImages(mainImages),
    add_to_cart_buttons: buttons,
  };
}

async function auditCartFlow(page, productUrl) {
  await page.goto(productUrl, { waitUntil: "networkidle", timeout: 60000 });
  const button = page.getByRole("button", { name: /agregar al carrito/i }).first();
  const hasButton = await button.count();
  if (!hasButton) {
    return { productUrl, status: "no_button" };
  }
  await button.click();
  await page.waitForTimeout(1200);
  await page.goto(`${BASE_URL}/cart/`, { waitUntil: "networkidle", timeout: 60000 });
  await scrollPage(page);
  const cartImages = await imageLoadResults(page, ".cart img, .woocommerce-cart-form img, .cart_item img");
  await page.screenshot({ path: path.join(OUT_DIR, "cart-after-add.png"), fullPage: false });
  return {
    productUrl,
    cart_url: page.url(),
    cart_images: summarizeImages(cartImages),
    cart_items_text: await page.locator("body").innerText({ timeout: 10000 }),
  };
}

async function loadSamples(filePath) {
  const raw = await fs.readFile(filePath, "utf8");
  return raw.trim().split(/\r?\n/).map((line) => {
    const [category, id, sku, url] = line.split("\t");
    return { category, id, sku, url };
  }).filter((item) => item.url);
}

async function main() {
  const samplesPath = process.argv[2] || "/private/tmp/category-sample-urls.tsv";
  await fs.mkdir(OUT_DIR, { recursive: true });
  const browser = await chromium.launch({ channel: "chrome", headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });

  const topPages = [];
  for (const [name, url] of [
    ["home", `${BASE_URL}/`],
    ["tienda", `${BASE_URL}/tienda/`],
  ]) {
    topPages.push(await auditCatalogPage(page, url, name, true, "main img, .site-main img, .lc-home-category-card__image, li.product img, .products img"));
  }

  const categories = [];
  for (const slug of CATEGORIES) {
    categories.push(await crawlCategory(page, slug));
  }

  const samples = await loadSamples(samplesPath);
  const products = [];
  for (const sample of samples) {
    products.push(await auditProduct(page, sample));
  }

  const cartSample = samples.find((item) => item.category === "MACETAS") || samples[0];
  const cart = cartSample ? await auditCartFlow(page, cartSample.url) : null;

  await browser.close();

  const report = { generated_at: new Date().toISOString(), base_url: BASE_URL, top_pages: topPages, categories, products, cart };
  const reportPath = path.join(OUT_DIR, "production-visual-image-audit.json");
  await fs.writeFile(reportPath, JSON.stringify(report, null, 2));

  const productFailures = products.filter((item) =>
    item.status >= 400 ||
    item.main_images.count === 0 ||
    item.main_images.broken > 0 ||
    !item.add_to_cart_buttons.some((button) => button.visible && !button.disabled)
  );

  const cartFailure = cart && cart.status !== "no_button" ? (cart.cart_images?.broken || 0) : 1;

  const failureCount =
    topPages.reduce((acc, item) => acc + item.images.broken, 0) +
    categories.reduce((acc, category) => acc + category.totals.broken, 0) +
    products.reduce((acc, item) => acc + item.main_images.broken, 0) +
    productFailures.length +
    cartFailure;

  const totals = {
    top_pages: topPages.map((item) => ({ name: item.name, ...item.images })),
    categories: categories.map((item) => ({ slug: item.slug, pages: item.pages.length, ...item.totals })),
    products: products.map((item) => ({ category: item.category, id: item.id, status: item.status, main_images: item.main_images, buttons: item.add_to_cart_buttons })),
    productFailures,
    cart: cart ? { status: cart.status || "checked", images: cart.cart_images } : null,
    failureCount,
    reportPath,
    outDir: OUT_DIR,
  };

  console.log(JSON.stringify(totals, null, 2));
  process.exit(failureCount > 0 ? 1 : 0);
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
