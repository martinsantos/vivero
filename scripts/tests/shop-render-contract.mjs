import { chromium } from 'playwright';

const shopUrl = process.env.SHOP_URL || 'http://localhost:8080/tienda/';

function fail(message, details = {}) {
  const suffix = Object.keys(details).length ? `\n${JSON.stringify(details, null, 2)}` : '';
  throw new Error(`${message}${suffix}`);
}

const viewports = [
  { name: 'desktop', width: 1440, height: 1100, isMobile: false },
  { name: 'mobile', width: 390, height: 844, isMobile: true },
];

const browser = await chromium.launch({ headless: true });
const failures = [];

function recordFailure(message, details = {}) {
  failures.push({ message, details });
}

try {
  for (const viewport of viewports) {
    const page = await browser.newPage({
      viewport: { width: viewport.width, height: viewport.height },
      isMobile: viewport.isMobile,
      deviceScaleFactor: 1,
    });
    const browserIssues = [];
    page.on('console', (message) => {
      if (['error', 'warning'].includes(message.type())) {
        browserIssues.push({
          type: message.type(),
          text: message.text(),
        });
      }
    });
    page.on('pageerror', (error) => {
      browserIssues.push({
        type: 'pageerror',
        text: error.message,
      });
    });

    try {
      await page.goto(shopUrl, { waitUntil: 'networkidle', timeout: 45000 });
      await page.screenshot({
        path: `/private/tmp/vivero-shop-contract-${viewport.name}.png`,
        fullPage: true,
      });

      const snapshot = await page.evaluate(() => {
        const rectFor = (selector) => {
          const node = document.querySelector(selector);
          if (!node) return null;
          const rect = node.getBoundingClientRect();
          return {
            width: Math.round(rect.width),
            height: Math.round(rect.height),
            top: Math.round(rect.top),
            left: Math.round(rect.left),
            bottom: Math.round(rect.bottom),
          };
        };

        const styleFor = (selector) => {
          const node = document.querySelector(selector);
          if (!node) return null;
          const styles = getComputedStyle(node);
          return {
            display: styles.display,
            gridTemplateColumns: styles.gridTemplateColumns,
            borderRadius: styles.borderRadius,
            boxShadow: styles.boxShadow,
            overflow: styles.overflow,
            position: styles.position,
            backgroundColor: styles.backgroundColor,
          };
        };

        const productCards = [...document.querySelectorAll('ul.products > li.product')];
        const firstRowTops = productCards.slice(0, 4).map((card) => Math.round(card.getBoundingClientRect().top));

        return {
          title: document.title,
          bodyClass: document.body.className,
          isUsingWooBlockTheme: document.body.classList.contains('woocommerce-uses-block-theme'),
          hasClassicPage: Boolean(document.querySelector('#page.site')),
          hasThemeHeader: Boolean(document.querySelector('header#masthead.site-header')),
          hasThemeFooter: Boolean(document.querySelector('footer')),
          hasClassicShopHero: document.body.textContent.includes('Tienda online de Vivero Los Cocos'),
          hasFeaturedSection: Boolean(document.querySelector('.lc-featured-products')),
          featuredCardCount: document.querySelectorAll('.lc-featured-product-card').length,
          hasCatalogToolbar: Boolean(document.querySelector('.lc-shop-toolbar')),
          blockProductCount: document.querySelectorAll('.wc-block-product').length,
          classicProductCount: productCards.length,
          firstCardRect: rectFor('ul.products > li.product'),
          firstImageRect: rectFor('ul.products > li.product img'),
          firstCardStyle: styleFor('ul.products > li.product'),
          productGridStyle: styleFor('ul.products'),
          controlsStyle: styleFor('.lc-shop-controls'),
          controlsRect: rectFor('.lc-shop-controls'),
          firstRowTops,
          overflowX: document.documentElement.scrollWidth - document.documentElement.clientWidth,
        };
      });

      if (browserIssues.length > 0) {
        recordFailure('La tienda no debe emitir errores o warnings relevantes en consola.', { viewport, browserIssues });
      }

      if (!snapshot.hasClassicPage || !snapshot.hasThemeHeader || !snapshot.hasThemeFooter) {
        recordFailure('La tienda no esta usando el shell clasico del theme activo.', { viewport, snapshot });
      }

      if (snapshot.isUsingWooBlockTheme) {
        recordFailure('WooCommerce detecto la tienda como block theme; debe usar templates clasicos.', { viewport, snapshot });
      }

      if (!snapshot.hasClassicShopHero) {
        recordFailure('La tienda no esta usando el archive-product.php vigente del child theme.', { viewport, snapshot });
      }

      if (snapshot.blockProductCount > 0) {
        recordFailure('La tienda esta renderizando la plantilla de bloques de WooCommerce en lugar del loop clasico.', { viewport, snapshot });
      }

      const hasClassicGrid =
        snapshot.classicProductCount >= 8 &&
        snapshot.firstCardRect &&
        snapshot.firstImageRect &&
        snapshot.firstCardStyle;

      if (!hasClassicGrid) {
        recordFailure('La grilla clasica de productos no esta renderizando suficientes cards.', { viewport, snapshot });
      }

      if (!snapshot.hasCatalogToolbar) {
        recordFailure('La tienda debe renderizar la toolbar comercial compacta.', { viewport, snapshot });
      }

      if (!snapshot.hasFeaturedSection || snapshot.featuredCardCount < 1 || snapshot.featuredCardCount > 3) {
        recordFailure('La tienda debe renderizar entre 1 y 3 destacados cuando hay productos elegibles.', { viewport, snapshot });
      }

      if (hasClassicGrid) {
        const borderRadius = Number.parseFloat(snapshot.firstCardStyle.borderRadius);
        const hasShadow = snapshot.firstCardStyle.boxShadow && snapshot.firstCardStyle.boxShadow !== 'none';
        const imageIsControlled =
          snapshot.firstImageRect.height <= snapshot.firstCardRect.width + 4 &&
          snapshot.firstImageRect.width <= snapshot.firstCardRect.width + 4;

        if (snapshot.firstCardStyle.display !== 'flex' || borderRadius < 6 || !hasShadow || !imageIsControlled) {
          recordFailure('La card de producto no conserva el layout visual controlado del theme.', { viewport, snapshot });
        }
      }

      if (snapshot.overflowX > 0) {
        recordFailure('La tienda no debe generar overflow horizontal.', { viewport, snapshot });
      }

      if (viewport.name === 'desktop') {
        const firstRowIsAligned =
          snapshot.firstRowTops.length === 4 &&
          new Set(snapshot.firstRowTops).size === 1;

        if (!firstRowIsAligned) {
          recordFailure('La grilla desktop debe conservar 4 columnas alineadas.', { viewport, snapshot });
        }

        if (snapshot.controlsStyle?.position !== 'sticky') {
          recordFailure('Los controles de catalogo deben ser sticky en desktop.', { viewport, snapshot });
        }
      }
    } catch (error) {
      recordFailure(error.message, { viewport, stack: error.stack });
    } finally {
      await page.close();
    }
  }

  if (failures.length > 0) {
    fail(failures[0].message, { failures });
  }

  console.log(`Shop render contract passed for ${shopUrl}`);
} finally {
  await browser.close();
}
