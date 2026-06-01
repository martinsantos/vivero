import { chromium } from 'playwright';

const baseUrl = (process.env.SITE_URL || 'http://localhost:8080').replace(/\/$/, '');

function fail(message, details = {}) {
  const suffix = Object.keys(details).length ? `\n${JSON.stringify(details, null, 2)}` : '';
  throw new Error(`${message}${suffix}`);
}

const browser = await chromium.launch({ headless: true });
const failures = [];

function recordFailure(message, details = {}) {
  failures.push({ message, details });
}

async function snapshotPage(page, path, name) {
  const url = `${baseUrl}${path}`;
  const issues = [];
  page.on('console', (message) => {
    if (['error', 'warning'].includes(message.type())) {
      issues.push({ type: message.type(), text: message.text() });
    }
  });
  page.on('pageerror', (error) => {
    issues.push({ type: 'pageerror', text: error.message });
  });

  await page.goto(url, { waitUntil: 'networkidle', timeout: 45000 });
  await page.screenshot({
    path: `/private/tmp/vivero-site-visual-${name}.png`,
    fullPage: false,
  });

  const snapshot = await page.evaluate(() => {
    const styleFor = (selector) => {
      const node = document.querySelector(selector);
      if (!node) return null;
      const styles = getComputedStyle(node);
      return {
        backgroundColor: styles.backgroundColor,
        backgroundImage: styles.backgroundImage,
        borderRadius: styles.borderRadius,
        boxShadow: styles.boxShadow,
        color: styles.color,
        fontFamily: styles.fontFamily,
        fontSize: styles.fontSize,
        fontWeight: styles.fontWeight,
      };
    };

    const rectFor = (selector) => {
      const node = document.querySelector(selector);
      if (!node) return null;
      const rect = node.getBoundingClientRect();
      return {
        top: Math.round(rect.top),
        left: Math.round(rect.left),
        width: Math.round(rect.width),
        height: Math.round(rect.height),
      };
    };

    return {
      title: document.title,
      url: window.location.href,
      statusText: document.body.textContent.slice(0, 240),
      hasSiteClass: document.body.classList.contains('lc-site'),
      hasHeader: Boolean(document.querySelector('header#masthead.site-header')),
      hasFooter: Boolean(document.querySelector('.lc-site-footer')),
      hasHome: Boolean(document.querySelector('.lc-home-page')),
      hasShop: Boolean(document.querySelector('.lc-shop-page')),
      hasCommerce: Boolean(document.querySelector('.lc-commerce-page')),
      hasProduct: Boolean(document.querySelector('.lc-product-page')),
      bodyStyle: styleFor('body'),
      h1Style: styleFor('h1'),
      primarySurfaceStyle: styleFor('.lc-page__article, .lc-commerce-panel, .lc-product-page__summary, .lc-shop-toolbar, .lc-home-category-card'),
      primaryButtonStyle: styleFor('.button.alt, button[name="add-to-cart"], .checkout-button, .lc-hero__primary-cta, .btn-checkout-premium'),
      h1Rect: rectFor('h1'),
      overflowX: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      productLinks: Array.from(document.querySelectorAll('ul.products > li.product a[href]'), (link) => link.href).filter(Boolean),
    };
  });

  return { snapshot, issues };
}

function assertSharedVisuals(name, snapshot, issues) {
  const fontFamily = (style) => style?.fontFamily?.toLowerCase() || '';

  if (issues.length > 0) {
    recordFailure(`${name}: no debe emitir errores o warnings relevantes en consola.`, { issues, snapshot });
  }

  if (!snapshot.hasSiteClass || !snapshot.hasHeader || !snapshot.hasFooter) {
    recordFailure(`${name}: debe usar el shell global lc-site con header y footer del theme.`, { snapshot });
  }

  if (!fontFamily(snapshot.bodyStyle).startsWith('inter')) {
    recordFailure(`${name}: debe usar Inter como tipografia principal.`, { snapshot });
  }

  if (!fontFamily(snapshot.h1Style).includes('merriweather')) {
    recordFailure(`${name}: debe usar Merriweather en el H1 principal.`, { snapshot });
  }

  if (snapshot.overflowX > 0) {
    recordFailure(`${name}: no debe generar overflow horizontal.`, { snapshot });
  }

  if (snapshot.h1Rect && snapshot.h1Rect.height < 32) {
    recordFailure(`${name}: el H1 principal esta visualmente colapsado.`, { snapshot });
  }
}

try {
  const desktop = { width: 1440, height: 1000 };
  const mobile = { width: 390, height: 844 };
  const page = await browser.newPage({ viewport: desktop });

  const home = await snapshotPage(page, '/', 'home-desktop');
  assertSharedVisuals('home desktop', home.snapshot, home.issues);
  if (!home.snapshot.hasHome) {
    recordFailure('home desktop: debe conservar lc-home-page como baseline aprobado.', home.snapshot);
  }

  const shop = await snapshotPage(page, '/tienda/', 'shop-desktop');
  assertSharedVisuals('shop desktop', shop.snapshot, shop.issues);
  if (!shop.snapshot.hasShop) {
    recordFailure('shop desktop: debe conservar lc-shop-page como baseline aprobado.', shop.snapshot);
  }

  const productUrl = shop.snapshot.productLinks.find((href) => href.includes('/producto/'));
  if (!productUrl) {
    recordFailure('shop desktop: debe exponer al menos un link de producto para validar la vista interna.', shop.snapshot);
  } else {
    await page.goto(productUrl, { waitUntil: 'networkidle', timeout: 45000 });
    await page.screenshot({
      path: '/private/tmp/vivero-site-visual-product-desktop.png',
      fullPage: false,
    });
    const product = await page.evaluate(() => {
      const styleFor = (selector) => {
        const node = document.querySelector(selector);
        if (!node) return null;
        const styles = getComputedStyle(node);
        return {
          borderRadius: styles.borderRadius,
          boxShadow: styles.boxShadow,
          fontFamily: styles.fontFamily,
          color: styles.color,
        };
      };
      return {
        title: document.title,
        hasSiteClass: document.body.classList.contains('lc-site'),
        hasHeader: Boolean(document.querySelector('header#masthead.site-header')),
        hasFooter: Boolean(document.querySelector('.lc-site-footer')),
        hasCommerce: Boolean(document.querySelector('.lc-commerce-page')),
        hasProduct: Boolean(document.querySelector('.lc-product-page')),
        bodyStyle: styleFor('body'),
        h1Style: styleFor('h1'),
        primarySurfaceStyle: styleFor('.lc-product-page__summary'),
        primaryButtonStyle: styleFor('button[name="add-to-cart"]'),
        h1Rect: (() => {
          const rect = document.querySelector('h1')?.getBoundingClientRect();
          return rect ? { height: Math.round(rect.height) } : null;
        })(),
        overflowX: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      };
    });
    assertSharedVisuals('product desktop', product, []);
    if (!product.hasCommerce || !product.hasProduct) {
      recordFailure('product desktop: debe usar lc-commerce-page y lc-product-page.', product);
    }
    if (!product.primarySurfaceStyle?.boxShadow || product.primarySurfaceStyle.boxShadow === 'none') {
      recordFailure('product desktop: la superficie principal debe mantener sombra suave premium.', product);
    }
  }

  const cart = await snapshotPage(page, '/carrito/', 'cart-desktop');
  assertSharedVisuals('cart desktop', cart.snapshot, cart.issues);
  if (!cart.snapshot.hasCommerce) {
    recordFailure('cart desktop: debe usar la capa lc-commerce-page.', cart.snapshot);
  }

  await page.setViewportSize(mobile);
  const mobileHome = await snapshotPage(page, '/', 'home-mobile');
  assertSharedVisuals('home mobile', mobileHome.snapshot, mobileHome.issues);
  const mobileShop = await snapshotPage(page, '/tienda/', 'shop-mobile');
  assertSharedVisuals('shop mobile', mobileShop.snapshot, mobileShop.issues);

  await page.close();

  if (failures.length > 0) {
    fail(failures[0].message, { failures });
  }

  console.log(`Site visual contract passed for ${baseUrl}`);
} finally {
  await browser.close();
}
