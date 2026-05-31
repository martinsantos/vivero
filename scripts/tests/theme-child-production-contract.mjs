import fs from 'node:fs';
import path from 'node:path';
import assert from 'node:assert/strict';

const root = process.cwd();
const read = (file) => fs.readFileSync(path.join(root, file), 'utf8');

const childDir = path.join(root, 'theme-loscocos-child');
assert.ok(fs.existsSync(childDir), 'Production design source must live in theme-loscocos-child.');

const sourceCss = read('theme-loscocos-child/assets/css/theme.css');
const compiledCss = read('theme-loscocos-child/style.css');
const categoryCss = read('theme-loscocos-child/assets/css/category-navigation.css');
const homepageCardCss = `${compiledCss}\n${categoryCss}`;
const frontPage = read('theme-loscocos-child/front-page.php');
const header = read('theme-loscocos-child/header.php');
const mainJs = read('theme-loscocos-child/assets/js/main.js');
const singleProductJs = read('theme-loscocos-child/assets/js/single-product.js');
const mountedChildDir = path.join(root, 'wp-content/themes/theme-loscocos-child');

for (const css of [sourceCss, compiledCss]) {
  assert.match(
    css,
    /Template:\s+theme-loscocos\b/,
    'Child theme stylesheet header must point to the production parent directory: theme-loscocos.'
  );
  assert.doesNotMatch(
    css,
    /Template:\s+loscocos\b/,
    'Child theme stylesheet header must not point to the stale loscocos parent directory.'
  );
}

for (const productionText of [
  'Plantas, macetas e insumos para comprar hoy',
  'Catálogo online de Vivero Los Cocos',
  'Comprar por categoría',
  'Destacados para comprar hoy',
  'Una compra de vivero con criterio profesional.',
]) {
  assert.ok(frontPage.includes(productionText), `front-page.php must keep live production theme text: ${productionText}`);
}

for (const retiredText of [
  'Vida Verde',
  'Para Tu Hogar',
  'Ofertas de la Semana',
  'Productos en Oferta',
  'Cultivando Pasión',
  'Únete a Nuestra Comunidad Verde',
]) {
  assert.ok(!frontPage.includes(retiredText), `front-page.php must not render stale indexed/cached demo text: ${retiredText}`);
}

for (const expectedClass of [
  'lc-hero',
  'lc-hero__secondary-cta',
  'lc-hero__image-frame',
  'lc-home-category-card',
  'lc-home-category-card__media',
  'lc-home-category-card__body',
]) {
  assert.ok(frontPage.includes(expectedClass), `front-page.php is missing live production component class: ${expectedClass}`);
  assert.ok(homepageCardCss.includes(`.${expectedClass}`), `Theme CSS must style live production component class: ${expectedClass}`);
}

assert.ok(frontPage.includes('wc_get_products'), 'front-page.php must render from WooCommerce catalog data.');
assert.ok(
  frontPage.includes('monstera-deliciosa-potted-plant-1-1024x1024.webp'),
  'Homepage hero must keep the verified live production hero image.'
);
assert.doesNotMatch(frontPage, /images\.unsplash\.com/, 'Homepage must not depend on stale indexed stock-image demo markup.');

for (const expectedMarkup of [
  'id="search-toggle"',
  'aria-controls="search-bar"',
  'aria-expanded="false"',
  'id="mobile-menu-toggle"',
  'aria-controls="mobile-navigation"',
  'text-neutral-dark',
]) {
  assert.ok(header.includes(expectedMarkup), `header.php is missing ${expectedMarkup}.`);
}

assert.ok(mainJs.includes("setAttribute('aria-expanded'"), 'main.js must keep menu/search expanded state in sync.');
assert.ok(mainJs.includes("querySelector('input[type=\"search\"]')"), 'main.js must focus the search field when opened.');
assert.ok(singleProductJs.includes('selectedPanel'), 'single-product.js must guard tab panel lookup.');

for (const expectedCss of [
  '.lc-hero',
  '.lc-hero__secondary-cta',
  '.lc-home-category-card',
  '.bg-primary-dark',
  '.text-white\\/75',
  '.bg-white\\/10',
  '.rounded-lg',
]) {
  assert.ok(compiledCss.includes(expectedCss), `Compiled child CSS is missing ${expectedCss}.`);
}

if (fs.existsSync(mountedChildDir)) {
  assert.equal(
    frontPage,
    read('wp-content/themes/theme-loscocos-child/front-page.php'),
    'Mounted WordPress child theme front-page.php must match the source child theme.'
  );
  assert.equal(
    compiledCss,
    read('wp-content/themes/theme-loscocos-child/style.css'),
    'Mounted WordPress child theme style.css must match the compiled source child theme.'
  );
  assert.equal(
    sourceCss,
    read('wp-content/themes/theme-loscocos-child/assets/css/theme.css'),
    'Mounted WordPress child theme source CSS must match the source child theme.'
  );
}

console.log('Child theme production contract checks passed.');
