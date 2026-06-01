import { chromium } from 'playwright';

const baseUrl = (process.env.SITE_URL || 'http://localhost:8080').replace(/\/$/, '');

function fail(message, details = {}) {
  const suffix = Object.keys(details).length ? `\n${JSON.stringify(details, null, 2)}` : '';
  throw new Error(`${message}${suffix}`);
}

function record(failures, message, details = {}) {
  failures.push({ message, details });
}

function typeIncludes(node, type) {
  const value = node?.['@type'];
  if (Array.isArray(value)) return value.includes(type);
  return value === type;
}

function hasGraphType(graph, type) {
  return graph.some((node) => typeIncludes(node, type));
}

function propertyNames(productNode) {
  return (productNode?.additionalProperty || [])
    .map((item) => item?.name)
    .filter(Boolean);
}

async function pageSnapshot(page, pathOrUrl) {
  const url = pathOrUrl.startsWith('http') ? pathOrUrl : `${baseUrl}${pathOrUrl}`;
  await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 45000 });
  await page.waitForTimeout(1200);

  return page.evaluate(() => {
    const ldScripts = Array.from(document.querySelectorAll('script[type="application/ld+json"]'));
    const graphs = [];

    for (const script of ldScripts) {
      try {
        const payload = JSON.parse(script.textContent || '{}');
        if (Array.isArray(payload)) {
          graphs.push(...payload);
        } else if (Array.isArray(payload['@graph'])) {
          graphs.push(...payload['@graph']);
        } else if (payload['@type']) {
          graphs.push(payload);
        }
      } catch (error) {
        graphs.push({ parseError: error.message });
      }
    }

    const geoBlock = document.querySelector('.lc-product-geo');
    const productLinks = Array.from(document.querySelectorAll('ul.products > li.product a[href]'), (link) => link.href)
      .filter((href) => /\/producto?\//.test(new URL(href).pathname));

    return {
      url: window.location.href,
      title: document.title,
      metaDescription: document.querySelector('meta[name="description"]')?.content || '',
      graph: graphs,
      productLinks: [...new Set(productLinks)],
      geoBlock: geoBlock ? {
        text: geoBlock.textContent.replace(/\s+/g, ' ').trim(),
        display: getComputedStyle(geoBlock).display,
        width: Math.round(geoBlock.getBoundingClientRect().width),
        height: Math.round(geoBlock.getBoundingClientRect().height),
      } : null,
      overflowX: document.documentElement.scrollWidth - document.documentElement.clientWidth,
    };
  });
}

function assertMeta(failures, name, snapshot) {
  const description = snapshot.metaDescription.trim();
  if (description.length < 70 || description.length > 180) {
    record(failures, `${name}: debe tener meta description comercial entre 70 y 180 caracteres.`, {
      description,
      length: description.length,
      url: snapshot.url,
    });
  }
}

function assertNoParseErrors(failures, name, snapshot) {
  const errors = snapshot.graph.filter((node) => node.parseError);
  if (errors.length) {
    record(failures, `${name}: JSON-LD debe ser parseable.`, { errors, url: snapshot.url });
  }
}

const browser = await chromium.launch({ headless: true });
const failures = [];

try {
  const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });

  const home = await pageSnapshot(page, '/');
  assertMeta(failures, 'home', home);
  assertNoParseErrors(failures, 'home', home);
  for (const type of ['Organization', 'LocalBusiness', 'Store', 'WebSite']) {
    if (!hasGraphType(home.graph, type)) {
      record(failures, `home: JSON-LD debe incluir ${type}.`, { types: home.graph.map((node) => node['@type']) });
    }
  }

  const shop = await pageSnapshot(page, '/tienda/');
  assertMeta(failures, 'shop', shop);
  assertNoParseErrors(failures, 'shop', shop);
  if (!hasGraphType(shop.graph, 'BreadcrumbList')) {
    record(failures, 'shop: JSON-LD debe incluir BreadcrumbList.', { types: shop.graph.map((node) => node['@type']) });
  }
  if (!shop.productLinks.length) {
    record(failures, 'shop: debe exponer links de producto para validar SEO/GEO de fichas.', { url: shop.url });
  }

  if (shop.productLinks.length) {
    const product = await pageSnapshot(page, shop.productLinks[0]);
    await page.screenshot({
      path: '/private/tmp/vivero-seo-geo-product-desktop.png',
      fullPage: false,
    });

    assertMeta(failures, 'product', product);
    assertNoParseErrors(failures, 'product', product);

    const productNode = product.graph.find((node) => typeIncludes(node, 'Product'));
    if (!productNode) {
      record(failures, 'product: JSON-LD debe incluir Product.', { types: product.graph.map((node) => node['@type']) });
    } else {
      const names = propertyNames(productNode);
      for (const expected of ['Contexto GEO', 'Área servida', 'Uso recomendado']) {
        if (!names.includes(expected)) {
          record(failures, `product: Product.additionalProperty debe incluir ${expected}.`, { names });
        }
      }
    }

    if (!hasGraphType(product.graph, 'FAQPage')) {
      record(failures, 'product: JSON-LD debe incluir FAQPage.', { types: product.graph.map((node) => node['@type']) });
    }
    if (!hasGraphType(product.graph, 'BreadcrumbList')) {
      record(failures, 'product: JSON-LD debe incluir BreadcrumbList.', { types: product.graph.map((node) => node['@type']) });
    }
    if (!product.geoBlock || product.geoBlock.display === 'none' || product.geoBlock.height < 180) {
      record(failures, 'product: debe renderizar la guía local visible.', { geoBlock: product.geoBlock, url: product.url });
    } else {
      for (const phrase of ['Mendoza', 'Cuyo', 'Guía local', 'Preguntas frecuentes']) {
        if (!product.geoBlock.text.includes(phrase)) {
          record(failures, `product: la guía local debe mencionar ${phrase}.`, { text: product.geoBlock.text.slice(0, 500) });
        }
      }
    }
    if (product.overflowX > 0) {
      record(failures, 'product: la guía SEO/GEO no debe producir overflow horizontal.', {
        overflowX: product.overflowX,
        url: product.url,
      });
    }
  }

  await page.close();

  if (failures.length > 0) {
    fail(failures[0].message, { failures });
  }

  console.log(`SEO/GEO contract passed for ${baseUrl}`);
} finally {
  await browser.close();
}
