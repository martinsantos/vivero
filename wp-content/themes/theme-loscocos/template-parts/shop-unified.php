<?php
/**
 * Template Unificado para Tienda/Categorías
 * 
 * @package LosCocos
 * @version 1.0.0
 */

// Obtener información de la página actual
$page_title = 'Tienda';
$page_description = 'Descubre nuestra selección de plantas y accesorios para jardinería';

if (is_product_category()) {
    $current_category = get_queried_object();
    $page_title = $current_category->name;
    $page_description = $current_category->description ?: 'Productos de ' . $current_category->name;
} elseif (is_product_tag()) {
    $current_tag = get_queried_object();
    $page_title = 'Productos: ' . $current_tag->name;
    $page_description = $current_tag->description ?: 'Productos etiquetados como ' . $current_tag->name;
}

// Obtener productos
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$products_per_page = 12;

$args = array(
    'post_type' => 'product',
    'posts_per_page' => $products_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
    'orderby' => 'menu_order',
    'order' => 'ASC'
);

// Si estamos en una categoría específica
if (is_product_category()) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => get_query_var('product_cat'),
        ),
    );
}

$products_query = new WP_Query($args);
?>

<!-- Banner de Tienda - Estilo Premium con Imagen de Fondo -->
<div class="shop-banner relative overflow-hidden" style="margin-top: -2rem; margin-left: -1rem; margin-right: -1rem; margin-bottom: 2rem;">
    <!-- Imagen de Fondo -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" 
             alt="Plantas del vivero" 
             style="width: 100%; height: 100%; object-fit: cover;">
        <!-- Overlay degradado -->
        <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(5,46,22,0.85) 100%);"></div>
    </div>
    
    <!-- Contenido -->
    <div class="relative z-10 text-center text-white" style="padding: 8rem 2rem 4rem 2rem;">
        <h1 style="font-size: 3.5rem; font-weight: 700; margin-bottom: 1rem; text-shadow: 2px 4px 8px rgba(0,0,0,0.4); font-family: 'Merriweather', serif;">
            <?php echo esc_html($page_title); ?>
        </h1>
        <p style="font-size: 1.25rem; opacity: 0.95; max-width: 600px; margin: 0 auto; text-shadow: 1px 2px 4px rgba(0,0,0,0.3);">
            <?php echo esc_html($page_description); ?>
        </p>
    </div>
</div>

<!-- Filtros y ordenamiento -->
<div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        
        <!-- Categorías -->
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo loscocos_shop_url(); ?>" 
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                      <?php echo !is_product_category() ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                Todas
            </a>
            
            <?php
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'exclude' => array(15) // Excluir "Uncategorized"
            ));
            
            if ($categories && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    $is_current = is_product_category() && get_query_var('product_cat') === $category->slug;
                    $category_url = loscocos_product_category_url($category->slug);
                    
                    echo '<a href="' . esc_url($category_url) . '" ';
                    echo 'class="px-4 py-2 rounded-full text-sm font-medium transition-colors ';
                    echo $is_current ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200';
                    echo '">';
                    echo esc_html($category->name);
                    echo ' (' . $category->count . ')';
                    echo '</a>';
                }
            }
            ?>
        </div>
        
        <!-- Ordenamiento -->
        <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">Ordenar por:</label>
            <select id="product-sort" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="menu_order">Orden por defecto</option>
                <option value="popularity">Popularidad</option>
                <option value="rating">Calificación</option>
                <option value="date">Más recientes</option>
                <option value="price">Precio: menor a mayor</option>
                <option value="price-desc">Precio: mayor a menor</option>
            </select>
        </div>
        
    </div>
</div>

<!-- Grid de productos -->
<?php if ($products_query->have_posts()) : ?>
    
    <div class="products-grid-unified">
        <?php
        while ($products_query->have_posts()) {
            $products_query->the_post();
            global $product;
            
            if (!$product) {
                $product = wc_get_product(get_the_ID());
            }
            
            if ($product) {
                // Usar el template unificado
                include get_template_directory() . '/template-parts/product-card-unified.php';
            }
        }
        wp_reset_postdata();
        ?>
    </div>
    
    <!-- Paginación -->
    <?php if ($products_query->max_num_pages > 1) : ?>
        <div class="mt-12 flex justify-center">
            <div class="bg-white rounded-lg shadow-sm border p-4">
                <?php
                $current_page = max(1, get_query_var('paged'));
                $total_pages = $products_query->max_num_pages;
                
                // Botón anterior
                if ($current_page > 1) {
                    $prev_url = get_pagenum_link($current_page - 1);
                    echo '<a href="' . esc_url($prev_url) . '" class="btn-unified btn-outline mr-2">‹ Anterior</a>';
                }
                
                // Números de página
                for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
                    $page_url = get_pagenum_link($i);
                    $is_current = ($i == $current_page);
                    
                    echo '<a href="' . esc_url($page_url) . '" ';
                    echo 'class="btn-unified ' . ($is_current ? 'btn-primary' : 'btn-outline') . ' mx-1">';
                    echo $i;
                    echo '</a>';
                }
                
                // Botón siguiente
                if ($current_page < $total_pages) {
                    $next_url = get_pagenum_link($current_page + 1);
                    echo '<a href="' . esc_url($next_url) . '" class="btn-unified btn-outline ml-2">Siguiente ›</a>';
                }
                ?>
            </div>
        </div>
    <?php endif; ?>
    
<?php else : ?>
    
    <!-- No hay productos -->
    <div class="text-center py-16">
        <div class="text-6xl mb-4">🌱</div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">No hay productos disponibles</h2>
        <p class="text-gray-600 mb-8">No se encontraron productos en esta categoría.</p>
        <a href="<?php echo loscocos_shop_url(); ?>" class="btn-unified btn-primary">
            Ver todos los productos
        </a>
    </div>
    
<?php endif; ?>

<script>
// Ordenamiento de productos
document.getElementById('product-sort').addEventListener('change', function() {
    const sortValue = this.value;
    const currentUrl = new URL(window.location.href);
    
    // Actualizar parámetro de ordenamiento
    currentUrl.searchParams.set('orderby', sortValue);
    currentUrl.searchParams.delete('paged'); // Reset pagination
    
    // Redireccionar
    window.location.href = currentUrl.toString();
});

// Mantener selección de ordenamiento
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderby = urlParams.get('orderby');
    
    if (orderby) {
        const sortSelect = document.getElementById('product-sort');
        if (sortSelect) {
            sortSelect.value = orderby;
        }
    }
});
</script>