import { chromium } from 'playwright';

const homeUrl = process.env.HOME_URL || 'http://localhost:8080/';

function fail(message, details = {}) {
  const suffix = Object.keys(details).length ? `\n${JSON.stringify(details, null, 2)}` : '';
  throw new Error(`${message}${suffix}`);
}

const viewports = [
  { name: 'desktop', width: 1440, height: 1000, isMobile: false },
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
        browserIssues.push({ type: message.type(), text: message.text() });
      }
    });
    page.on('pageerror', (error) => {
      browserIssues.push({ type: 'pageerror', text: error.message });
    });

    try {
      await page.goto(homeUrl, { waitUntil: 'networkidle', timeout: 45000 });
      await page.screenshot({
        path: `/private/tmp/vivero-home-contract-${viewport.name}.png`,
        fullPage: false,
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
            borderRadius: styles.borderRadius,
            boxShadow: styles.boxShadow,
            backgroundColor: styles.backgroundColor,
            color: styles.color,
            fontFamily: styles.fontFamily,
            fontSize: styles.fontSize,
            fontWeight: styles.fontWeight,
            lineHeight: styles.lineHeight,
          };
        };

        const firstCategory = document.querySelector('.lc-home-category-card');
        const firstCategoryImage = firstCategory?.querySelector('img');
        const firstFeatured = document.querySelector('.lc-home-product-card');
        const firstFeaturedImage = firstFeatured?.querySelector('img');

        return {
          title: document.title,
          hasHomePage: Boolean(document.querySelector('.lc-home-page')),
          heroRect: rectFor('.lc-hero'),
          heroTitleStyle: styleFor('.lc-hero h1'),
          heroImageRect: rectFor('.lc-hero img'),
          categorySectionRect: rectFor('.lc-home-categories'),
          firstCategoryRect: rectFor('.lc-home-category-card'),
          firstCategoryStyle: styleFor('.lc-home-category-card'),
          firstCategoryImageRect: firstCategoryImage ? rectFor('.lc-home-category-card img') : null,
          featuredSectionRect: rectFor('.lc-home-featured'),
          firstFeaturedRect: rectFor('.lc-home-product-card'),
          firstFeaturedStyle: styleFor('.lc-home-product-card'),
          firstFeaturedImageRect: firstFeaturedImage ? rectFor('.lc-home-product-card img') : null,
          trustSectionRect: rectFor('.lc-home-trust'),
          bodyStyle: styleFor('body'),
          overflowX: document.documentElement.scrollWidth - document.documentElement.clientWidth,
        };
      });

      if (browserIssues.length > 0) {
        recordFailure('La home no debe emitir errores o warnings relevantes en consola.', { viewport, browserIssues });
      }

      if (!snapshot.hasHomePage) {
        recordFailure('La home debe exponer la clase lc-home-page para aplicar el sistema visual de produccion.', { viewport, snapshot });
      }

      const fontFamily = (style) => style?.fontFamily?.toLowerCase() || '';
      if (!fontFamily(snapshot.bodyStyle).startsWith('inter')) {
        recordFailure('La home debe usar Inter como tipografia principal de UI.', { viewport, snapshot });
      }

      if (!fontFamily(snapshot.heroTitleStyle).includes('merriweather')) {
        recordFailure('La home debe mantener Merriweather/serif en el titular principal.', { viewport, snapshot });
      }

      if (!snapshot.firstCategoryRect || !snapshot.firstCategoryStyle || !snapshot.featuredSectionRect) {
        recordFailure('La home debe renderizar categorias y destacados comerciales.', { viewport, snapshot });
      }

      if (viewport.name === 'desktop') {
        if (snapshot.heroRect?.height > 760) {
          recordFailure('El hero desktop debe dejar aparecer antes las secciones comerciales.', { viewport, snapshot });
        }

        if (snapshot.firstCategoryRect?.top > 1070) {
          recordFailure('Las categorias deben aparecer mas arriba en la primera lectura comercial.', { viewport, snapshot });
        }

        const categoryImageRatio = snapshot.firstCategoryImageRect && snapshot.firstCategoryRect
          ? snapshot.firstCategoryImageRect.width / snapshot.firstCategoryRect.width
          : 0;

        if (categoryImageRatio < 0.98) {
          recordFailure('Las cards de categoria deben usar imagen protagonista a ancho completo.', { viewport, snapshot, categoryImageRatio });
        }

        if (!snapshot.firstFeaturedStyle?.boxShadow || snapshot.firstFeaturedStyle.boxShadow === 'none') {
          recordFailure('Los destacados de home deben tener una superficie premium con sombra suave.', { viewport, snapshot });
        }
      }

      if (snapshot.overflowX > 0) {
        recordFailure('La home no debe generar overflow horizontal.', { viewport, snapshot });
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

  console.log(`Home render contract passed for ${homeUrl}`);
} finally {
  await browser.close();
}
