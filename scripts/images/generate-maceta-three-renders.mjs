#!/usr/bin/env node
import fs from "node:fs/promises";
import http from "node:http";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { chromium } from "playwright";

const __filename = fileURLToPath(import.meta.url);
const ROOT = path.resolve(path.dirname(__filename), "../..");
const DEFAULT_INPUT = "/private/tmp/macetas-products.tsv";
const DEFAULT_OUTPUT = path.join(ROOT, "assets/product-images/macetas-three");

const COLORS = new Map([
  ["amarillo", ["#d3aa32", "amarillo"]],
  ["amarilla", ["#d3aa32", "amarillo"]],
  ["am", ["#d3aa32", "amarillo"]],
  ["azul", ["#436f9f", "azul"]],
  ["beige", ["#bda47b", "beige"]],
  ["blanco", ["#e8e3d6", "blanco"]],
  ["blanca", ["#e8e3d6", "blanco"]],
  ["bl", ["#e8e3d6", "blanco"]],
  ["crema", ["#d7c7a8", "crema"]],
  ["cre", ["#d7c7a8", "crema"]],
  ["gris claro", ["#aeb6b0", "gris claro"]],
  ["gcl", ["#aeb6b0", "gris claro"]],
  ["gris oscuro", ["#616962", "gris oscuro"]],
  ["gos", ["#616962", "gris oscuro"]],
  ["marron claro", ["#98633d", "marron claro"]],
  ["marrón claro", ["#98633d", "marron claro"]],
  ["mc", ["#98633d", "marron claro"]],
  ["marron oscuro", ["#40291e", "marron oscuro"]],
  ["marrón oscuro", ["#40291e", "marron oscuro"]],
  ["mo", ["#40291e", "marron oscuro"]],
  ["marron terracota", ["#a34e2f", "terracota"]],
  ["marrón terracota", ["#a34e2f", "terracota"]],
  ["mt", ["#a34e2f", "terracota"]],
  ["naranja", ["#cb7130", "naranja"]],
  ["na", ["#cb7130", "naranja"]],
  ["negro", ["#202321", "negro"]],
  ["negra", ["#202321", "negro"]],
  ["ne", ["#202321", "negro"]],
  ["rojo", ["#a53d33", "rojo"]],
  ["roja", ["#a53d33", "rojo"]],
  ["r", ["#a53d33", "rojo"]],
  ["rosa", ["#cf8f9f", "rosa"]],
  ["ro", ["#cf8f9f", "rosa"]],
  ["verde aqua", ["#80b5a2", "verde aqua"]],
  ["va", ["#80b5a2", "verde aqua"]],
  ["vaq", ["#80b5a2", "verde aqua"]],
  ["verde claro", ["#7fb875", "verde claro"]],
  ["vc", ["#7fb875", "verde claro"]],
  ["verde oscuro", ["#304f3b", "verde oscuro"]],
  ["vo", ["#304f3b", "verde oscuro"]],
  ["violeta", ["#766092", "violeta"]],
  ["vi", ["#766092", "violeta"]],
  ["vol", ["#766092", "violeta"]],
]);

function arg(name, fallback) {
  const i = process.argv.indexOf(name);
  return i >= 0 && process.argv[i + 1] ? process.argv[i + 1] : fallback;
}

function normalize(value) {
  return String(value || "")
    .replace(/&#215;/g, "x")
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/\s+/g, " ")
    .trim()
    .toLowerCase();
}

function slugify(value) {
  return normalize(value).replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "").slice(0, 90) || "maceta";
}

function inferColor(name, sku) {
  const text = normalize(`${name} ${sku}`);
  const colorMatch = text.match(/color:\s*([a-z ]+)/);
  if (colorMatch) {
    for (const [key, value] of COLORS) {
      if (normalize(colorMatch[1]).includes(normalize(key))) return value;
    }
  }
  for (const [key, value] of COLORS) {
    if (new RegExp(`\\b${normalize(key)}\\b`).test(text)) return value;
  }
  const suffix = normalize(sku).match(/(bl|ne|mc|mt|be|gcl|gos|vc|vaq|vi|vol|az|cre|ro|vo|am|na|r)$/);
  if (suffix && COLORS.has(suffix[1])) return COLORS.get(suffix[1]);
  return ["#98633d", "natural"];
}

function inferFamily(name, sku) {
  const text = normalize(`${name} ${sku}`);
  if (text.includes("gancho") || text.startsWith("gtap")) return "hook";
  if (text.includes("regadera")) return "watering_can";
  if (text.includes("pie nordico") || text.includes("pienord")) return "stand";
  if (text.includes("plato cuadrado") || text.includes("ptapcuad")) return "square_saucer";
  if (text.includes("plato") || text.includes("ptap")) return "saucer";
  if (text.includes("cono") || text.includes("fibcon")) return "cone_pot";
  if (text.includes("andina") || text.includes("fiband")) return "decorated_pot";
  if (text.includes("jardinera") || text.includes("jare") || text.includes("jard")) return "planter";
  if (text.includes("cuadrada") || text.includes("owcu") || (text.includes("matri") && !text.includes("red"))) return "square_pot";
  if (text.includes("bols") || text.includes("bm") || text.includes("owred")) return "bowl_pot";
  return "round_pot";
}

function inferMeasure(name, sku) {
  const text = normalize(`${name} ${sku}`);
  let match = text.match(/(\d{1,3})\s*x\s*(\d{1,3})/);
  if (match) return `${match[1]}x${match[2]} cm`;
  match = text.match(/(\d{1,3})\s*cm/);
  if (match) return `${match[1]} cm`;
  match = text.match(/(\d{1,3})\s*litros?/);
  if (match) return `${match[1]} litros`;
  match = normalize(sku).match(/(\d{2})(?:bl|ne|mc|mt|be|gcl|gos|vc|vaq|vi|vol|az|cre|ro|vo|am|na|r)$/);
  return match ? `${match[1]} cm` : "medida indicada";
}

async function loadProducts(input) {
  const rows = (await fs.readFile(input, "utf8")).trim().split(/\r?\n/);
  return rows.map((row) => {
    const [product_id, name, sku] = row.split("\t");
    const [color, colorLabel] = inferColor(name, sku);
    return {
      product_id: Number(product_id),
      name,
      sku,
      family: inferFamily(name, sku),
      color,
      colorLabel,
      measure: inferMeasure(name, sku),
      file: `${product_id}-${slugify(sku || name)}.webp`,
    };
  }).filter((item) => item.product_id && item.name);
}

function rendererHtml() {
  return `<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    html, body { margin: 0; width: 1200px; height: 1200px; overflow: hidden; background: #f4f1e9; }
    canvas { width: 1200px; height: 1200px; display: block; }
  </style>
</head>
<body>
<canvas id="c" width="1200" height="1200"></canvas>
<script type="module">
import * as THREE from "/node_modules/three/build/three.module.js";

const canvas = document.getElementById("c");
const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: false, preserveDrawingBuffer: true });
renderer.setSize(1200, 1200, false);
renderer.setPixelRatio(1);
renderer.outputColorSpace = THREE.SRGBColorSpace;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.08;
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;

function mat(color, roughness = 0.58, metalness = 0.02) {
  return new THREE.MeshPhysicalMaterial({
    color: new THREE.Color(color),
    roughness,
    metalness,
    clearcoat: 0.28,
    clearcoatRoughness: 0.52,
    sheen: 0.12,
  });
}

function add(mesh, group) {
  mesh.castShadow = true;
  mesh.receiveShadow = true;
  group.add(mesh);
  return mesh;
}

function cyl(radiusTop, radiusBottom, height, color, y = 0, segments = 128) {
  const mesh = new THREE.Mesh(new THREE.CylinderGeometry(radiusTop, radiusBottom, height, segments, 1, false), mat(color));
  mesh.position.y = y;
  return mesh;
}

function torus(radius, tube, color, y) {
  const mesh = new THREE.Mesh(new THREE.TorusGeometry(radius, tube, 24, 160), mat(color, 0.52));
  mesh.rotation.x = Math.PI / 2;
  mesh.position.y = y;
  return mesh;
}

function insert(radius, y, color = "#342b22") {
  const mesh = new THREE.Mesh(new THREE.CylinderGeometry(radius, radius * 0.96, 0.045, 128), mat(color, 0.86, 0));
  mesh.position.y = y;
  return mesh;
}

function potLathe(points, color) {
  const geo = new THREE.LatheGeometry(points.map(([x, y]) => new THREE.Vector2(x, y)), 192);
  geo.computeVertexNormals();
  return new THREE.Mesh(geo, mat(color, 0.55));
}

function makeRound(color, scale = 1) {
  const g = new THREE.Group();
  add(potLathe([[0.78,-1.15],[0.9,-0.94],[1.08,0.76],[1.18,0.98],[1.08,1.12],[0.72,1.03]], color), g);
  add(torus(1.02, 0.075, color, 1.05), g);
  add(insert(0.72, 1.02), g);
  add(torus(0.7, 0.045, color, -1.06), g);
  g.scale.setScalar(scale);
  return g;
}

function makeBowl(color) {
  const g = new THREE.Group();
  add(potLathe([[0.55,-0.82],[0.78,-0.68],[1.22,0.54],[1.35,0.79],[1.12,0.95],[0.75,0.88]], color), g);
  add(torus(1.18, 0.07, color, 0.88), g);
  add(insert(0.82, 0.84), g);
  g.scale.setScalar(1.08);
  return g;
}

function makeCone(color) {
  const g = new THREE.Group();
  add(potLathe([[0.45,-1.26],[0.67,-1.05],[1.16,0.97],[1.26,1.12],[1.08,1.24],[0.7,1.1]], color), g);
  add(torus(1.13, 0.07, color, 1.16), g);
  add(insert(0.72, 1.12), g);
  g.scale.set(1, 1.08, 1);
  return g;
}

function makeDecorated(color) {
  const g = makeRound(color, 1.03);
  const cream = mat("#e7d8ab", 0.44);
  const positions = [[-0.42,0.2,0.92],[-0.2,-0.02,1.02],[0.2,-0.02,1.02],[0.42,0.2,0.92],[-0.52,-0.28,0.86],[0,-0.35,0.96],[0.52,-0.28,0.86]];
  for (const [x, y, z] of positions) {
    const dot = new THREE.Mesh(new THREE.SphereGeometry(0.055, 32, 16), cream);
    dot.position.set(x, y, z);
    dot.castShadow = true;
    g.add(dot);
  }
  const curveMat = new THREE.LineBasicMaterial({ color: "#e7d8ab", linewidth: 2 });
  for (const side of [-1, 1]) {
    const curve = new THREE.QuadraticBezierCurve3(new THREE.Vector3(0, 0.34, 1.035), new THREE.Vector3(side * 0.22, 0.52, 1.02), new THREE.Vector3(side * 0.48, 0.18, 0.91));
    g.add(new THREE.Line(new THREE.BufferGeometry().setFromPoints(curve.getPoints(28)), curveMat));
  }
  return g;
}

function makePlanter(color) {
  const g = new THREE.Group();
  const body = new THREE.Mesh(new THREE.BoxGeometry(3.0, 1.12, 1.28, 1, 1, 1), mat(color, 0.58));
  body.position.y = -0.25;
  body.scale.set(1, 1, 0.92);
  add(body, g);
  const rim = new THREE.Mesh(new THREE.BoxGeometry(3.28, 0.22, 1.48), mat(color, 0.5));
  rim.position.y = 0.42;
  add(rim, g);
  const inner = new THREE.Mesh(new THREE.BoxGeometry(2.82, 0.08, 1.1), mat("#46372d", 0.82));
  inner.position.y = 0.56;
  add(inner, g);
  g.scale.setScalar(0.9);
  return g;
}

function makeSquare(color) {
  const g = new THREE.Group();
  const body = new THREE.Mesh(new THREE.BoxGeometry(1.75, 1.9, 1.75), mat(color, 0.58));
  body.position.y = -0.2;
  add(body, g);
  const rim = new THREE.Mesh(new THREE.BoxGeometry(2.05, 0.25, 2.05), mat(color, 0.5));
  rim.position.y = 0.85;
  add(rim, g);
  g.rotation.y = Math.PI / 4;
  return g;
}

function makeSaucer(color, square) {
  const g = new THREE.Group();
  if (square) {
    const plate = new THREE.Mesh(new THREE.BoxGeometry(2.6, 0.18, 2.0), mat(color, 0.5));
    add(plate, g);
    const well = new THREE.Mesh(new THREE.BoxGeometry(2.1, 0.12, 1.5), mat(color, 0.62));
    well.position.y = 0.13;
    add(well, g);
  } else {
    add(cyl(1.45, 1.55, 0.22, color, 0), g);
    add(torus(1.33, 0.055, color, 0.14), g);
    add(cyl(1.0, 1.05, 0.08, color, 0.18), g);
  }
  g.scale.set(1.05, 1, 1.05);
  return g;
}

function makeHook(color) {
  const g = new THREE.Group();
  const material = mat(color, 0.43);
  const ring = new THREE.Mesh(new THREE.TorusGeometry(0.78, 0.07, 24, 120), material);
  ring.position.y = 0.35;
  add(ring, g);
  const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.06, 0.06, 1.3, 32), material);
  stem.position.y = -0.35;
  add(stem, g);
  g.rotation.x = -0.15;
  return g;
}

function makeWateringCan(color) {
  const g = new THREE.Group();
  add(cyl(0.85, 0.78, 1.25, color, -0.15), g);
  add(torus(0.62, 0.055, color, 0.52), g);
  const spout = new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.11, 1.35, 32), mat(color, 0.52));
  spout.rotation.z = Math.PI / 2.9;
  spout.position.set(0.92, 0.22, 0);
  add(spout, g);
  const handle = new THREE.Mesh(new THREE.TorusGeometry(0.58, 0.06, 24, 80, Math.PI * 1.45), mat(color, 0.5));
  handle.rotation.y = Math.PI / 2;
  handle.position.set(-0.82, 0.1, 0);
  add(handle, g);
  return g;
}

function makeStand(color) {
  const g = new THREE.Group();
  const wood = "#8c603d";
  const top = new THREE.Mesh(new THREE.CylinderGeometry(1.05, 1.05, 0.12, 96), mat(wood, 0.5));
  top.position.y = 0.62;
  add(top, g);
  for (const x of [-0.7, 0.7]) {
    for (const z of [-0.48, 0.48]) {
      const leg = new THREE.Mesh(new THREE.CylinderGeometry(0.055, 0.075, 1.35, 24), mat(wood, 0.55));
      leg.position.set(x, -0.1, z);
      leg.rotation.z = x * 0.12;
      leg.rotation.x = z * -0.16;
      add(leg, g);
    }
  }
  return g;
}

function modelFor(config) {
  switch (config.family) {
    case "bowl_pot": return makeBowl(config.color);
    case "cone_pot": return makeCone(config.color);
    case "decorated_pot": return makeDecorated(config.color);
    case "planter": return makePlanter(config.color);
    case "square_pot": return makeSquare(config.color);
    case "saucer": return makeSaucer(config.color, false);
    case "square_saucer": return makeSaucer(config.color, true);
    case "hook": return makeHook(config.color);
    case "watering_can": return makeWateringCan(config.color);
    case "stand": return makeStand(config.color);
    default: return makeRound(config.color, 1);
  }
}

function addStudio(scene) {
  scene.background = new THREE.Color("#f2efe7");
  const floorMat = new THREE.ShadowMaterial({ color: "#6f6b5c", opacity: 0.18 });
  const floor = new THREE.Mesh(new THREE.PlaneGeometry(200, 200), floorMat);
  floor.rotation.x = -Math.PI / 2;
  floor.position.y = -1.35;
  floor.receiveShadow = true;
  scene.add(floor);

  scene.add(new THREE.HemisphereLight("#ffffff", "#d8d1c3", 1.55));
  const key = new THREE.DirectionalLight("#fff8ed", 3.4);
  key.position.set(-4.8, 6.2, 5.4);
  key.castShadow = true;
  key.shadow.mapSize.width = 2048;
  key.shadow.mapSize.height = 2048;
  key.shadow.camera.near = 0.5;
  key.shadow.camera.far = 18;
  key.shadow.camera.left = -5;
  key.shadow.camera.right = 5;
  key.shadow.camera.top = 5;
  key.shadow.camera.bottom = -5;
  scene.add(key);
  const fill = new THREE.DirectionalLight("#dbe9ff", 1.2);
  fill.position.set(4.8, 3.8, 3.4);
  scene.add(fill);
  const rim = new THREE.DirectionalLight("#fff", 1.1);
  rim.position.set(0, 3.2, -5);
  scene.add(rim);
}

window.renderProduct = async function renderProduct(config) {
  const scene = new THREE.Scene();
  addStudio(scene);
  const camera = new THREE.PerspectiveCamera(36, 1, 0.1, 100);
  camera.position.set(3.2, 2.05, 5.15);
  camera.lookAt(0, -0.08, 0);

  const group = modelFor(config);
  group.rotation.y = -0.42;
  group.position.y = config.family.includes("saucer") ? -0.7 : -0.1;
  scene.add(group);

  const box = new THREE.Box3().setFromObject(group);
  const size = new THREE.Vector3();
  box.getSize(size);
  const maxDim = Math.max(size.x, size.y, size.z);
  const target = config.family === "planter" ? 3.1 : config.family.includes("saucer") ? 3.25 : 2.75;
  group.scale.multiplyScalar(target / maxDim);

  renderer.render(scene, camera);
  await new Promise((resolve) => requestAnimationFrame(resolve));
  renderer.render(scene, camera);
  return canvas.toDataURL("image/webp", 0.94);
};
</script>
</body>
</html>`;
}

function startServer(html) {
  const server = http.createServer(async (req, res) => {
    try {
      if (req.url === "/" || req.url === "/renderer.html") {
        res.writeHead(200, { "content-type": "text/html; charset=utf-8" });
        res.end(html);
        return;
      }

      if (req.url && req.url.startsWith("/node_modules/three/build/")) {
        const requested = path.basename(req.url);
        const file = await fs.readFile(path.join(ROOT, "node_modules/three/build", requested));
        res.writeHead(200, { "content-type": "text/javascript; charset=utf-8" });
        res.end(file);
        return;
      }

      res.writeHead(404);
      res.end("not found");
    } catch (error) {
      res.writeHead(500);
      res.end(String(error && error.stack || error));
    }
  });

  return new Promise((resolve) => {
    server.listen(0, "127.0.0.1", () => {
      const address = server.address();
      resolve({ server, url: `http://127.0.0.1:${address.port}/renderer.html` });
    });
  });
}

async function main() {
  const input = arg("--input", DEFAULT_INPUT);
  const outputDir = arg("--output-dir", DEFAULT_OUTPUT);
  const limit = Number(arg("--limit", "0"));
  const offset = Number(arg("--offset", "0"));
  await fs.mkdir(outputDir, { recursive: true });

  const products = (await loadProducts(input)).slice(offset, limit > 0 ? offset + limit : undefined);
  const { server, url } = await startServer(rendererHtml());
  let browser;
  try {
    try {
      browser = await chromium.launch({ headless: true });
    } catch (error) {
      if (!String(error && error.message || error).includes("Executable doesn't exist")) {
        throw error;
      }
      browser = await chromium.launch({ channel: "chrome", headless: true });
    }
    const page = await browser.newPage({ viewport: { width: 1200, height: 1200 }, deviceScaleFactor: 1 });
    page.on("pageerror", (error) => console.error("pageerror:", error.message));
    page.on("console", (message) => {
      if (message.type() === "error") {
        console.error("console:", message.text());
      }
    });
    await page.goto(url, { waitUntil: "load", timeout: 30000 });
    await page.waitForFunction("window.renderProduct !== undefined", { timeout: 30000 });

    const manifest = [];
    const counts = new Map();
    for (const product of products) {
      const dataUrl = await page.evaluate((config) => window.renderProduct(config), product);
      const base64 = dataUrl.split(",", 2)[1];
      await fs.writeFile(path.join(outputDir, product.file), Buffer.from(base64, "base64"));
      manifest.push({
        product_id: product.product_id,
        file: product.file,
        alt: `${product.name} - imagen 3D realista de maceta o accesorio en Vivero Los Cocos`,
        family: product.family,
        color: product.colorLabel,
        measure: product.measure,
      });
      counts.set(product.family, (counts.get(product.family) || 0) + 1);
    }

    await fs.writeFile(path.join(outputDir, "macetas-three-manifest.json"), JSON.stringify(manifest, null, 2));
    console.log(JSON.stringify({ count: manifest.length, outputDir, family_counts: Object.fromEntries([...counts].sort()) }, null, 2));
  } finally {
    if (browser) {
      await browser.close();
    }
    server.close();
  }
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
