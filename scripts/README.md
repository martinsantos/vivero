# CSV Conversion Scripts

## convert_csv.js

Converts the supplier's product CSV (`productoscocos_completo.csv`) to WooCommerce import format.

### Usage

```bash
# Run from project root directory
node scripts/convert_csv.js
```

### What it does

- Reads `productoscocos_completo.csv` from the project root
- Maps columns according to WooCommerce requirements:
  - `Codigo` → `SKU`
  - `Marca` + `Modelo` → `Name` (e.g., "Ta Plastic – Rocio 6 cm Amarillo")
  - `Precio` → `Regular price`
  - `Cantidad` → `Stock`
  - `Diametro (cm)` → `Attributes:Paño`
  - `Color` → `Attributes:Color`
- Outputs `products_woocommerce.csv` ready for WooCommerce import

### Example Output

```csv
SKU,Name,Regular price,Stock,Attributes:Paño,Attributes:Color
MTAPCAC6AM,"Ta Plastic – Rocio 6 cm Amarillo",0,0,"6 cm","Amarillo"
MTAPCAC6MT,"Ta Plastic – Rocio 6 cm Marron Terracota",0,0,"6 cm","Marron Terracota"
```

### Notes

- The script skips rows without a valid `Codigo` (SKU)
- Prices and stock quantities are cleaned to contain only numbers
- Product names are properly quoted to handle commas and special characters
- Global attributes **Paño** and **Color** can be configured in WooCommerce before import

### Re-running for Updates

When the supplier sends an updated `productoscocos_completo.csv`:

1. Replace the old file with the new one
2. Run the script again: `node scripts/convert_csv.js`
3. Import the new `products_woocommerce.csv` into WooCommerce
