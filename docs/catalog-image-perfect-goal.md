# GOAL: Imagenes perfectas sin pagar licencias

## Objetivo

Cada producto de Vivero Los Cocos debe tener una imagen principal correcta,
atractiva, legalmente defendible y consistente con ecommerce de alto nivel,
sin comprar bancos pagos.

## Principio operativo

No se aplica ninguna imagen sin trazabilidad. Cada producto queda registrado en
`assets/product-images/source-manifest/catalog-image-source-manifest.json` con:

- `source_type`
- `provider`
- `source_url`
- `source_file`
- `license_status`
- `confidence_score`
- `review_status`
- `recommended_action`

## Jerarquia de fuentes

1. Fabricante o proveedor oficial.
2. Asset local ya verificado del proveedor.
3. Foto propia del vivero.
4. Imagen libre exacta por especie, con licencia trazable.
5. Render 3D propio para productos genericos sin foto oficial.
6. Unsplash solo para editorial, home y categorias; no para producto exacto.

## Politica por tipo de producto

### Insumos con marca

Usar imagen del fabricante o asset local del producto real. Ejemplos:
Glacoxan, Fertifox, FungoXAN, Terrafertil.

### Macetas

No usar fotos genericas. Si no hay foto de proveedor, generar render 3D propio
por familia, color y medida. La imagen debe mostrar el objeto aislado, sin texto
decorativo agregado, con iluminacion de estudio y fondo neutro.

### Plantas

Primero identificar especie comun y cientifica. Luego usar foto propia,
proveedor autorizado o fuente libre exacta. Si la coincidencia es debil, queda
en revision manual.

### Home y categorias

Puede usarse Unsplash o imagen editorial curada, siempre que no se presente
como producto exacto.

## Criterios de aceptacion

- 100% de productos con imagen cargada.
- 0 imagenes rotas.
- 0 paisajes, personas u objetos irrelevantes en imagen principal de producto.
- 95% o mas con imagen exacta, oficial, propia o render propio.
- Imagen cuadrada o recortable a cuadrado sin deformacion.
- Fondo blanco o neutro para producto.
- Manifest auditable para cada decision.
- Aplicacion por lotes revisables, no reemplazo masivo a ciegas.

## Estado inicial auditado

- Total productos: 555.
- Insumos con asset local/proveedor aplicable: 33.
- Macetas que requieren render premium o foto de proveedor: 307.
- Plantas que requieren mapeo de especie/fuente exacta: 215.

## Implementacion

- Manifest maestro: `assets/product-images/source-manifest/catalog-image-source-manifest.json`
- Resumen CSV: `assets/product-images/source-manifest/catalog-image-source-summary.csv`
- Assets oficiales aplicables: `assets/product-images/provider-official/`
- Script de manifest: `scripts/images/build-product-image-source-manifest.py`
- Script de render 3D para macetas: `scripts/images/generate-maceta-three-renders.mjs`
