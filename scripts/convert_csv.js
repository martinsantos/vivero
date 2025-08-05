#!/usr/bin/env node

const fs = require('fs');
const path = require('path');

/**
 * Script to convert productoscocos_completo.csv to WooCommerce format
 * 
 * Maps columns:
 * - Codigo → SKU
 * - Marca + Modelo → Name (e.g., "Ta Plastic – Rocio 6 cm Amarillo")
 * - Precio → Regular price
 * - Cantidad → Stock
 * - Diametro (cm) → Paño attribute
 * - Color → Color attribute
 */

function convertCSV() {
    const inputFile = 'productoscocos_completo.csv';
    const outputFile = 'products_woocommerce.csv';
    
    console.log('🔄 Converting CSV for WooCommerce...');
    
    try {
        // Read the input CSV file
        const inputPath = path.resolve(inputFile);
        const data = fs.readFileSync(inputPath, 'utf8');
        
        // Split into lines and filter out empty lines
        const lines = data.split('\n').filter(line => line.trim());
        
        if (lines.length === 0) {
            throw new Error('Input file is empty');
        }
        
        // Parse header line (remove line number prefix)
        const headerLine = lines[0].replace(/^\d+\|/, '');
        const headers = headerLine.split(',').map(h => h.trim());
        
        console.log('📋 Found headers:', headers);
        
        // Find column indices
        const codigoIndex = headers.findIndex(h => h.toLowerCase().includes('codigo'));
        const marcaIndex = headers.findIndex(h => h.toLowerCase().includes('marca'));
        const modeloIndex = headers.findIndex(h => h.toLowerCase().includes('modelo'));
        const diametroIndex = headers.findIndex(h => h.toLowerCase().includes('diametro'));
        const colorIndex = headers.findIndex(h => h.toLowerCase().includes('color'));
        const cantidadIndex = headers.findIndex(h => h.toLowerCase().includes('cantidad'));
        const precioIndex = headers.findIndex(h => h.toLowerCase().includes('precio'));
        
        console.log('🔍 Column mapping:');
        console.log(`  Codigo: ${codigoIndex} (${headers[codigoIndex]})`);
        console.log(`  Marca: ${marcaIndex} (${headers[marcaIndex]})`);
        console.log(`  Modelo: ${modeloIndex} (${headers[modeloIndex]})`);
        console.log(`  Diametro: ${diametroIndex} (${headers[diametroIndex]})`);
        console.log(`  Color: ${colorIndex} (${headers[colorIndex]})`);
        console.log(`  Cantidad: ${cantidadIndex} (${headers[cantidadIndex]})`);
        console.log(`  Precio: ${precioIndex} (${headers[precioIndex]})`);
        
        // WooCommerce headers
        const wooHeaders = [
            'SKU',
            'Name',
            'Regular price',
            'Stock',
            'Attributes:Paño',
            'Attributes:Color'
        ];
        
        // Convert data rows
        const convertedRows = [];
        
        for (let i = 1; i < lines.length; i++) {
            const line = lines[i].trim();
            if (!line) continue;
            
            // Remove line number prefix and split by pipe
            const cleanLine = line.replace(/^\d+\|/, '');
            const values = cleanLine.split(',').map(v => v.trim());
            
            if (values.length < headers.length) {
                console.log(`⚠️  Skipping incomplete row ${i}: ${cleanLine}`);
                continue;
            }
            
            // Extract values
            const codigo = values[codigoIndex] || '';
            const marca = values[marcaIndex] || '';
            const modelo = values[modeloIndex] || '';
            const diametro = values[diametroIndex] || '';
            const color = values[colorIndex] || '';
            const cantidad = values[cantidadIndex] || '0';
            const precio = values[precioIndex] || '0';
            
            // Skip rows with empty SKU
            if (!codigo) {
                console.log(`⚠️  Skipping row ${i} - no codigo/SKU`);
                continue;
            }
            
            // Create product name: "Marca – Modelo Diametro cm Color"
            let productName = '';
            if (marca && modelo) {
                productName = `${marca} – ${modelo}`;
                if (diametro) {
                    productName += ` ${diametro} cm`;
                }
                if (color) {
                    productName += ` ${color}`;
                }
            } else if (marca) {
                productName = marca;
                if (color) {
                    productName += ` ${color}`;
                }
            } else {
                productName = codigo; // Fallback to SKU
            }
            
            // Clean up price (remove any non-numeric characters except decimal point)
            const cleanPrice = precio.replace(/[^\d.]/g, '') || '0';
            
            // Clean up stock
            const cleanStock = cantidad.replace(/[^\d]/g, '') || '0';
            
            // Create WooCommerce row
            const wooRow = [
                codigo,                    // SKU
                `"${productName}"`,        // Name (quoted to handle commas)
                cleanPrice,                // Regular price
                cleanStock,                // Stock
                diametro ? `"${diametro} cm"` : '""',  // Paño attribute
                color ? `"${color}"` : '""'            // Color attribute
            ];
            
            convertedRows.push(wooRow.join(','));
        }
        
        // Create output content (proper CSV format without line numbers)
        const outputContent = [
            wooHeaders.join(','),
            ...convertedRows
        ].join('\n');
        
        // Write output file
        const outputPath = path.resolve(outputFile);
        fs.writeFileSync(outputPath, outputContent, 'utf8');
        
        console.log(`✅ Successfully converted ${convertedRows.length} products`);
        console.log(`📄 Output written to: ${outputPath}`);
        
        // Show sample of output
        console.log('\n📋 Sample output (first 3 rows):');
        const sampleLines = outputContent.split('\n').slice(0, 4);
        sampleLines.forEach((line, idx) => {
            console.log(`  ${idx === 0 ? 'Header' : `Row ${idx}`}: ${line}`);
        });
        
    } catch (error) {
        console.error('❌ Error converting CSV:', error.message);
        process.exit(1);
    }
}

// Run the conversion if this script is executed directly
if (require.main === module) {
    convertCSV();
}

module.exports = { convertCSV };
