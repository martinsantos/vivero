<?php
/**
 * Tema principal para Vivero Los Cocos - Versión Simple
 * Esta versión funciona sin WooCommerce
 */

get_header(); ?>

<main class="main-content">
    
    <!-- Hero Section del Vivero -->
    <section class="vivero-hero">
        <div class="container">
            <h1>🌿 Bienvenido a Vivero Los Cocos</h1>
            <p>Tu jardín perfecto te está esperando en Mendoza</p>
            <a href="#productos" class="btn-loscocos">Ver Nuestras Plantas</a>
        </div>
    </section>

    <!-- Controles Estacionales -->
    <section class="seasonal-controls">
        <button class="seasonal-btn" data-season="primavera">🌸 Primavera</button>
        <button class="seasonal-btn active" data-season="verano">🌞 Verano</button>
        <button class="seasonal-btn" data-season="otono">🍂 Otoño</button>
        <button class="seasonal-btn" data-season="invierno">❄️ Invierno</button>
    </section>

    <!-- Contenido Estacional Dinámico -->
    <section class="seasonal-content" id="seasonal-content">
        <div class="container">
            <h2 id="seasonal-title">Plantas de Verano</h2>
            <p id="seasonal-description">Descubre nuestras plantas perfectas para la temporada de verano en Mendoza.</p>
        </div>
    </section>

    <!-- Categorías de Productos -->
    <section class="product-categories" id="productos">
        <div class="container">
            <h2 class="text-center mb-4">Nuestras Especialidades</h2>
            <div class="product-categories">
                <div class="product-category">
                    <div class="category-icon">🌹</div>
                    <h3>Plantas Ornamentales</h3>
                    <p>Embellece tu jardín con nuestras plantas decorativas</p>
                    <button class="btn-loscocos" onclick="alert('¡Próximamente! Instala WooCommerce para ver productos')">Ver Ornamentales</button>
                </div>
                <div class="product-category">
                    <div class="category-icon">🥬</div>
                    <h3>Huerta Orgánica</h3>
                    <p>Cultiva tus propios alimentos saludables</p>
                    <button class="btn-loscocos" onclick="alert('¡Próximamente! Instala WooCommerce para ver productos')">Ver Huerta</button>
                </div>
                <div class="product-category">
                    <div class="category-icon">🌳</div>
                    <h3>Árboles Frutales</h3>
                    <p>Disfruta de frutas frescas de tu jardín</p>
                    <button class="btn-loscocos" onclick="alert('¡Próximamente! Instala WooCommerce para ver productos')">Ver Frutales</button>
                </div>
                <div class="product-category">
                    <div class="category-icon">🌵</div>
                    <h3>Suculentas</h3>
                    <p>Plantas resistentes y de fácil cuidado</p>
                    <button class="btn-loscocos" onclick="alert('¡Próximamente! Instala WooCommerce para ver productos')">Ver Suculentas</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Plantas de ejemplo (sin WooCommerce) -->
    <section class="plantas-destacadas">
        <div class="container">
            <h2>🌟 Plantas Destacadas</h2>
            
            <div class="productos-ejemplo">
                <div class="producto-item">
                    <div class="producto-imagen">🌹</div>
                    <h3>Rosa Premium</h3>
                    <p class="precio">$2.500</p>
                    <button class="btn-loscocos" onclick="contactarVivero('Rosa Premium')">Consultar</button>
                </div>
                <div class="producto-item">
                    <div class="producto-imagen">🌿</div>
                    <h3>Albahaca Orgánica</h3>
                    <p class="precio">$800</p>
                    <button class="btn-loscocos" onclick="contactarVivero('Albahaca Orgánica')">Consultar</button>
                </div>
                <div class="producto-item">
                    <div class="producto-imagen">🌳</div>
                    <h3>Limonero Enano</h3>
                    <p class="precio">$4.200</p>
                    <button class="btn-loscocos" onclick="contactarVivero('Limonero Enano')">Consultar</button>
                </div>
                <div class="producto-item">
                    <div class="producto-imagen">🌵</div>
                    <h3>Suculenta Mix</h3>
                    <p class="precio">$1.200</p>
                    <button class="btn-loscocos" onclick="contactarVivero('Suculenta Mix')">Consultar</button>
                </div>
            </div>
            
            <div class="texto-centro mt-4">
                <p><strong>💡 Para ver el catálogo completo y comprar online:</strong></p>
                <p>Instala WooCommerce desde el panel de administración</p>
            </div>
        </div>
    </section>

    <!-- Testimonios -->
    <section class="testimonios">
        <div class="container">
            <h2 class="mb-4">Lo que dicen nuestros clientes</h2>
            <div class="testimonio">
                <p>Excelente calidad de plantas y muy buen asesoramiento. Mi jardín nunca había lucido tan hermoso.</p>
                <strong>- María González, Mendoza</strong>
            </div>
            <div class="testimonio">
                <p>Los mejores precios de la zona y plantas muy saludables. Siempre vuelvo por más.</p>
                <strong>- Carlos Rodríguez, Godoy Cruz</strong>
            </div>
        </div>
    </section>

</main>

<style>
/* Estilos adicionales para la versión simple */
.productos-ejemplo {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.producto-item {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.producto-item:hover {
    transform: translateY(-5px);
}

.producto-imagen {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.producto-item h3 {
    color: var(--color-primario);
    margin-bottom: 0.5rem;
    font-family: var(--font-titulo);
}

.precio {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--color-acento);
    margin: 1rem 0;
}

.texto-centro {
    text-align: center;
    background: rgba(139, 195, 74, 0.1);
    padding: 2rem;
    border-radius: 15px;
    margin-top: 2rem;
}
</style>

<!-- JavaScript para el sistema estacional -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const seasonalBtns = document.querySelectorAll('.seasonal-btn');
    const seasonalTitle = document.getElementById('seasonal-title');
    const seasonalDescription = document.getElementById('seasonal-description');
    
    const seasonalContent = {
        primavera: {
            title: 'Plantas de Primavera 🌸',
            description: 'La primavera es perfecta para plantar y renovar tu jardín. Descubre nuestras variedades ideales para esta estación.'
        },
        verano: {
            title: 'Plantas de Verano 🌞',
            description: 'Plantas resistentes al calor mendocino. Perfectas para decorar y refrescar tu hogar en verano.'
        },
        otono: {
            title: 'Plantas de Otoño 🍂',
            description: 'Es tiempo de preparar el jardín para el invierno. Plantas que resisten las temperaturas más frescas.'
        },
        invierno: {
            title: 'Plantas de Invierno ❄️',
            description: 'Mantén tu jardín hermoso durante el invierno con nuestras plantas resistentes al frío.'
        }
    };
    
    seasonalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            seasonalBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const season = this.dataset.season;
            const content = seasonalContent[season];
            
            seasonalTitle.textContent = content.title;
            seasonalDescription.textContent = content.description;
        });
    });
    
    // Detectar estación automáticamente
    const currentMonth = new Date().getMonth() + 1;
    let currentSeason = 'verano';
    
    if (currentMonth >= 9 && currentMonth <= 11) currentSeason = 'primavera';
    else if (currentMonth >= 12 || currentMonth <= 2) currentSeason = 'verano';
    else if (currentMonth >= 3 && currentMonth <= 5) currentSeason = 'otono';
    else if (currentMonth >= 6 && currentMonth <= 8) currentSeason = 'invierno';
    
    const currentSeasonBtn = document.querySelector(`[data-season="${currentSeason}"]`);
    if (currentSeasonBtn) {
        currentSeasonBtn.click();
    }
});

function contactarVivero(producto) {
    const mensaje = `Hola! Me interesa el producto: ${producto}. ¿Podrían darme más información?`;
    const whatsapp = '+542611234567'; // Cambiar por el número real
    const url = `https://wa.me/${whatsapp.replace(/\D/g, '')}?text=${encodeURIComponent(mensaje)}`;
    window.open(url, '_blank');
}
</script>

<?php get_footer(); ?> 