# Los Cocos WooCommerce CSV Importer (WP-CLI)

Este proyecto incluye un MU-plugin con comandos WP-CLI para importar productos desde CSV y para asegurar las páginas núcleo de WooCommerce.

## Ubicación estándar del CSV
- Carpeta: `wp-content/uploads/import/`
- Archivo por defecto: `products_woocommerce.csv`
- Puedes especificar otra ruta con `--file=...`.

## Formato del CSV
- Delimitador por defecto: `,` (coma). Cambiable con `--delimiter=';'`.
- Encabezados esperados (mínimos):
  - `SKU` (obligatorio)
  - `Name` (nombre del producto)
  - `Regular price` (precio)
  - `Stock` (entero; activa manage_stock automáticamente)
- Opcionales:
  - `Categories` (separadores aceptados: `|`, `,`, `>`, `/`; se crean si no existen, por nombre)
  - Cualquier columna de atributos con prefijo `Attributes:`. Ejemplos: `Attributes:Altura`, `Attributes:Color`
- Notas:
  - El parser es tolerante a mayúsculas/minúsculas en encabezados.
  - Los precios aceptan `,` o `.` como separador decimal; se normalizan a punto.

## Ejemplos de uso (Docker)
- Seco (recomendado):
```
docker compose exec wpcli wp loscocos import-csv --dry-run
```
- Archivo explícito y delimitador `;`:
```
docker compose exec wpcli wp loscocos import-csv --file=wp-content/uploads/import/products_woocommerce.csv --delimiter=';' --dry-run
```
- Importar realmente:
```
docker compose exec wpcli wp loscocos import-csv --file=wp-content/uploads/import/products_woocommerce.csv
```

## Lógica de importación (resumen)
- Busca producto por `SKU`; si existe, actualiza. Si no, crea un `simple product` publicado y visible.
- `Regular price` se guarda como `regular_price`.
- `Stock` activa `manage_stock`, setea cantidad y `instock/outofstock`.
- `Categories` por nombre; crea términos faltantes y asigna IDs al producto.
- `Attributes:*` se guardan como atributos personalizados no taxonómicos (visibles, no variación).

## Asegurar páginas de WooCommerce
Comando para verificar/crear y vincular páginas núcleo (Tienda, Carrito, Finalizar compra, Mi cuenta):
```
docker compose exec wpcli wp loscocos ensure-wc-pages --flush
```
- Usa slugs en español: `/tienda/`, `/carrito/`, `/finalizar-compra/`, `/mi-cuenta/`.
- `--flush` regenera las reglas de reescritura (permalinks).

## Solución de problemas
- 404 en `/tienda/`: ejecutar `ensure-wc-pages --flush`.
- Errores de CSV: verificar delimitador y encabezados; ejecutar primero con `--dry-run`.
- Categorías duplicadas: se deduplican por ID; confirmar nombres exactos en CSV.

## Seguridad y recomendaciones
- Siempre ejecutar primero en `--dry-run`.
- Realizar backup de DB antes de importaciones masivas.
- Mantener el CSV en `wp-content/uploads/import/` para estandarizar procesos.
