// Function to parse CSV data
function parseCSV(csvText) {
    const lines = csvText.trim().split('\n');
    const headers = lines[0].split(',').map(header => header.trim().replace(/['"]+/g, ''));
    
    const products = [];
    for (let i = 1; i < lines.length; i++) {
        // Handle quoted fields that may contain commas
        const values = parseCSVLine(lines[i]);
        if (values.length === headers.length) {
            const product = {};
            headers.forEach((header, index) => {
                product[header] = values[index] || '';
            });
            // Only add products with a name or SKU
            if (product['Name'] || product['SKU'] || product['Descripcion'] || product['Modelo']) {
                products.push(product);
            }
        }
    }
    
    return products;
}

// Helper function to parse a CSV line with quoted fields
function parseCSVLine(line) {
    const result = [];
    let current = '';
    let inQuotes = false;
    
    for (let i = 0; i < line.length; i++) {
        const char = line[i];
        
        if (char === '"') {
            if (inQuotes && i + 1 < line.length && line[i + 1] === '"') {
                // Double quotes inside quoted field
                current += '"';
                i++; // Skip next quote
            } else {
                // Toggle quote state
                inQuotes = !inQuotes;
            }
        } else if (char === ',' && !inQuotes) {
            // End of field
            result.push(current.trim());
            current = '';
        } else {
            current += char;
        }
    }
    
    // Add the last field
    result.push(current.trim());
    return result;
}

// Function to fetch and process product data
async function loadProductData() {
    const csvFiles = [
        'products_woocommerce.csv',
        'products_woocommerce_final.csv',
        'products_woocommerce_correct.csv',
        'productosviverococos.csv',
        'productoscocos_completo.csv'
    ];
    
    // Try each CSV file in order
    for (const file of csvFiles) {
        try {
            const response = await fetch(file);
            if (response.ok) {
                const csvText = await response.text();
                const products = parseCSV(csvText);
                if (products.length > 0) {
                    console.log(`Loaded ${products.length} products from ${file}`);
                    return products;
                }
            }
        } catch (error) {
            console.warn(`Could not load ${file}:`, error);
        }
    }
    
    // Fallback to sample data if no files found
    console.warn('No product data files found, using sample data');
    return getSampleProducts();
}

// Sample data in case CSV files are not accessible
function getSampleProducts() {
    return [
        {
            "SKU": "MTAPCAC6AM",
            "Name": "Ta Plastic – Rocio 6 cm Amarillo",
            "Regular price": "1200",
            "Stock": "10",
            "Attributes:Paño": "6 cm",
            "Attributes:Color": "Amarillo"
        },
        {
            "SKU": "MTAPCAC6MT",
            "Name": "Ta Plastic – Rocio 6 cm Marron Terracota",
            "Regular price": "1200",
            "Stock": "8",
            "Attributes:Paño": "6 cm",
            "Attributes:Color": "Marron Terracota"
        },
        {
            "SKU": "MTAPCAC6VC",
            "Name": "Ta Plastic – Rocio 6 cm Verde Claro",
            "Regular price": "1200",
            "Stock": "15",
            "Attributes:Paño": "6 cm",
            "Attributes:Color": "Verde Claro"
        },
        {
            "SKU": "MTAPCAC8VC",
            "Name": "Ta Plastic – Rocio 8 cm Verde Claro",
            "Regular price": "1500",
            "Stock": "12",
            "Attributes:Paño": "8 cm",
            "Attributes:Color": "Verde Claro"
        },
        {
            "SKU": "MTAPCAC8MT",
            "Name": "Ta Plastic – Rocio 8 cm Marron Terracota",
            "Regular price": "1500",
            "Stock": "7",
            "Attributes:Paño": "8 cm",
            "Attributes:Color": "Marron Terracota"
        },
        {
            "SKU": "MTAPCAC10VC",
            "Name": "Ta Plastic – Rocio 10 cm Verde Claro",
            "Regular price": "1800",
            "Stock": "5",
            "Attributes:Paño": "10 cm",
            "Attributes:Color": "Verde Claro"
        },
        {
            "SKU": "PLANT001",
            "Name": "Planta de Interior - Espatifilo",
            "Regular price": "2500",
            "Stock": "20",
            "Categoria": "Plantas de Interior",
            "Tamaño": "Mediano"
        },
        {
            "SKU": "PLANT002",
            "Name": "Planta Suculenta - Echeveria",
            "Regular price": "800",
            "Stock": "35",
            "Categoria": "Suculentas",
            "Tamaño": "Pequeño"
        },
        {
            "SKU": "TOOL001",
            "Name": "Kit de Herramientas de Jardinería",
            "Regular price": "4200",
            "Stock": "15",
            "Categoria": "Herramientas",
            "Material": "Acero inoxidable"
        }
    ];
}

// Function to generate product HTML
function generateProductHTML(product) {
    // Extract price and format it
    const price = product['Regular price'] || product['Precio'] || '0';
    const formattedPrice = new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
        minimumFractionDigits: 0
    }).format(price);
    
    // Extract product name
    const name = product['Name'] || product['Descripcion'] || product['Modelo'] || 'Producto sin nombre';
    
    // Extract attributes
    const size = product['Attributes:Paño'] || product['Diametro (cm)'] || product['Tamaño'] || '';
    const color = product['Attributes:Color'] || product['Color'] || '';
    const category = product['Categoria'] || product['Marca'] || '';
    
    // Create a simple product image URL based on product characteristics
    const imageUrl = generateProductImageUrl(product);
    
    return `
        <div class="product-card">
            <div class="product-image-container">
                <a class="product-image-link" href="#">
                    <img src="${imageUrl}" alt="${name}">
                </a>
            </div>
            <div class="product-content">
                <a class="product-title-link" href="#">
                    <h3 class="woocommerce-loop-product__title">${name}</h3>
                </a>
                <div class="product-meta">
                    ${category ? `<span class="category">${category}</span>` : ''}
                    ${size ? `<span class="size">${size}</span>` : ''}
                    ${color ? `<span class="color">${color}</span>` : ''}
                </div>
                <div class="price">${formattedPrice}</div>
            </div>
            <div class="product-actions">
                <button class="button add_to_cart_button ajax_add_to_cart" data-product_id="${product.SKU || Math.floor(Math.random() * 10000)}">
                    Agregar al carrito
                </button>
            </div>
        </div>
    `;
}

// Function to generate a product image URL based on product characteristics
function generateProductImageUrl(product) {
    // Use a placeholder service with product-specific parameters
    const size = product['Attributes:Paño'] || product['Diametro (cm)'] || product['Tamaño'] || '6';
    const color = product['Attributes:Color'] || product['Color'] || 'verde';
    const category = product['Categoria'] || product['Marca'] || 'producto';
    
    // Map common colors to placeholder colors
    const colorMap = {
        'verde': 'green',
        'verde claro': 'lightgreen',
        'amarillo': 'yellow',
        'rojo': 'red',
        'marron': 'brown',
        'marron terracota': 'brown',
        'naranja': 'orange',
        'violeta': 'purple',
        'default': 'green'
    };
    
    // Map categories to image types
    const categoryMap = {
        'plantas': 'plant',
        'macetas': 'pot',
        'herramientas': 'tool',
        'fertilizantes': 'bottle',
        'default': 'product'
    };
    
    const mappedColor = colorMap[color.toLowerCase()] || colorMap['default'];
    
    // Extract size in cm if available
    const sizeNum = size.match(/\d+/) ? size.match(/\d+/)[0] : '6';
    
    // Determine image type based on category
    let imageType = 'product';
    for (const [key, value] of Object.entries(categoryMap)) {
        if (category.toLowerCase().includes(key)) {
            imageType = value;
            break;
        }
    }
    
    return `https://placehold.co/300x300/${mappedColor}/white?text=${imageType}+${sizeNum}cm`;
}

// Function to render products in a grid
function renderProducts(products, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    // Clear existing content
    container.innerHTML = '';
    
    if (products.length === 0) {
        container.innerHTML = '<div class="error">No se encontraron productos</div>';
        return;
    }
    
    // Create product grid
    const grid = document.createElement('div');
    grid.className = 'products';
    grid.style.display = 'grid';
    grid.style.gridTemplateColumns = 'repeat(auto-fit, minmax(250px, 1fr))';
    grid.style.gap = '1.5rem';
    grid.style.padding = '1rem 0';
    
    // Add products to grid (limit to 12 for better performance)
    const productsToShow = products.slice(0, 12);
    productsToShow.forEach(product => {
        const productElement = document.createElement('div');
        productElement.innerHTML = generateProductHTML(product);
        grid.appendChild(productElement.firstElementChild);
    });
    
    container.appendChild(grid);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const products = await loadProductData();
        
        if (products.length > 0) {
            // Render products in different sections
            renderProducts(products, 'products-container');
            
            // Also render in featured sections if they exist
            renderProducts(products.slice(0, 6), 'featured-products');
            renderProducts(products.slice(2, 8), 'new-products');
        } else {
            // Show error message
            document.querySelectorAll('.loading').forEach(el => {
                el.parentElement.innerHTML = '<div class="error">No se pudieron cargar los productos</div>';
            });
        }
    } catch (error) {
        console.error('Error initializing product data:', error);
        // Show error message
        document.querySelectorAll('.loading').forEach(el => {
            el.parentElement.innerHTML = '<div class="error">Error al cargar los productos: ' + error.message + '</div>';
        });
    }
});