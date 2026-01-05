<?php
/**
 * Template para la página de inicio - Vivero Los Cocos
 * Diseño equilibrado y hermoso
 */

get_header(); ?>

<style>
/* Variables CSS */
:root {
    --primary-green: #4CAF50;
    --primary-dark: #45a049;
    --primary-light: #81C784;
    --accent-green: #66BB6A;
    --background-light: #f8fdf8;
    --background-card: #ffffff;
    --text-primary: #2c3e50;
    --text-secondary: #5a6c7d;
    --text-muted: #95a5a6;
    --border-light: #e8f5e8;
    --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-large: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --gradient-primary: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-dark) 100%);
    --gradient-soft: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
}

/* Reset base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    color: var(--text-primary);
    background: var(--background-light);
}

.font-display {
    font-family: 'Poppins', 'Inter', sans-serif;
    font-weight: 700;
    letter-spacing: -0.025em;
}

.container-clean {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

@media (max-width: 768px) {
    .container-clean {
        padding: 0 1rem;
    }
}

/* Hero Section Equilibrada */
.hero-section {
    min-height: 80vh;
    background: linear-gradient(135deg, var(--gradient-soft), rgba(255, 255, 255, 0.9));
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background: url('https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&h=600&fit=crop&q=80') center/cover;
    opacity: 0.1;
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    padding: 4rem 0;
}

.hero-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

@media (max-width: 1024px) {
    .hero-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
        text-align: center;
    }
}

.hero-text h1 {
    font-size: 3.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    line-height: 1.1;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

@media (max-width: 768px) {
    .hero-text h1 {
        font-size: 2.5rem;
    }
}

.hero-text p {
    font-size: 1.25rem;
    color: var(--text-secondary);
    margin-bottom: 2rem;
    line-height: 1.7;
    max-width: 500px;
}

.hero-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: var(--gradient-primary);
    color: white;
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-medium);
}

.hero-cta:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-large);
    color: white;
    text-decoration: none;
}

.hero-features {
    display: flex;
    gap: 2rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.hero-feature {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.hero-visual {
    position: relative;
}

.hero-image-main {
    width: 100%;
    max-width: 500px;
    height: 400px;
    object-fit: cover;
    border-radius: 2rem;
    box-shadow: var(--shadow-large);
    position: relative;
    z-index: 2;
}

.hero-floating-card {
    position: absolute;
    background: white;
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: var(--shadow-large);
    z-index: 3;
}

.hero-floating-card.top-left {
    top: -1rem;
    left: -1rem;
    max-width: 150px;
}

.hero-floating-card.bottom-right {
    bottom: -1rem;
    right: -1rem;
    max-width: 180px;
}

.floating-card-icon {
    width: 3rem;
    height: 3rem;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
}

.floating-card-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.floating-card-text {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

/* Sección de Características */
.features-section {
    padding: 5rem 0;
    background: white;
}

.features-header {
    text-align: center;
    margin-bottom: 4rem;
}

.features-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.features-header p {
    font-size: 1.125rem;
    color: var(--text-secondary);
    max-width: 600px;
    margin: 0 auto;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: var(--gradient-soft);
    padding: 2rem;
    border-radius: 1.5rem;
    text-align: center;
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-medium);
}

.feature-icon {
    width: 4rem;
    height: 4rem;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin: 0 auto 1.5rem;
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.feature-text {
    color: var(--text-secondary);
    line-height: 1.6;
}

/* Sección de Productos */
.products-section {
    padding: 5rem 0;
    background: var(--gradient-soft);
}

.products-header {
    text-align: center;
    margin-bottom: 4rem;
}

.products-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.products-header p {
    font-size: 1.125rem;
    color: var(--text-secondary);
    max-width: 600px;
    margin: 0 auto 2rem;
}

.products-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    color: var(--primary-green);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    border: 2px solid var(--primary-green);
    transition: all 0.3s ease;
}

.products-cta:hover {
    background: var(--primary-green);
    color: white;
    text-decoration: none;
}

.products-preview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.product-preview-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    transition: all 0.3s ease;
    border: 1px solid var(--border-light);
}

.product-preview-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-medium);
}

.product-preview-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: var(--gradient-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--primary-green);
}

.product-preview-content {
    padding: 1.5rem;
}

.product-preview-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.product-preview-category {
    color: var(--text-muted);
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.product-preview-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-green);
}

/* Sección CTA Final */
.cta-section {
    padding: 5rem 0;
    background: var(--gradient-primary);
    color: white;
    text-align: center;
}

.cta-content h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.cta-content p {
    font-size: 1.125rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-button {
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.cta-button.primary {
    background: white;
    color: var(--primary-green);
}

.cta-button.primary:hover {
    background: var(--background-light);
    transform: translateY(-2px);
    color: var(--primary-green);
    text-decoration: none;
}

.cta-button.secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.cta-button.secondary:hover {
    background: white;
    color: var(--primary-green);
    text-decoration: none;
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease forwards;
}

.fade-in-up-delay-1 {
    animation: fadeInUp 0.6s ease 0.2s forwards;
    opacity: 0;
}

.fade-in-up-delay-2 {
    animation: fadeInUp 0.6s ease 0.4s forwards;
    opacity: 0;
}

.fade-in-up-delay-3 {
    animation: fadeInUp 0.6s ease 0.6s forwards;
    opacity: 0;
}
</style>

<main>
    <!-- Hero Section Equilibrada -->
    <section class="hero-section">
        <div class="container-clean">
            <div class="hero-content">
                <div class="hero-grid">
                    <div class="hero-text">
                        <h1 class="font-display fade-in-up">
                            Vivero Los Cocos
                        </h1>
                        <p class="fade-in-up-delay-1">
                            Tu jardín perfecto comienza aquí. Descubre nuestra amplia selección de plantas, herramientas y todo lo necesario para crear el oasis verde de tus sueños.
                        </p>
                        <a href="<?php echo home_url('/?post_type=product'); ?>" class="hero-cta fade-in-up-delay-2">
                            <span class="material-icons">yard</span>
                            Explorar Productos
                        </a>
                        <div class="hero-features fade-in-up-delay-3">
                            <div class="hero-feature">
                                <span class="material-icons" style="color: #f59e0b;">star</span>
                                Calidad Premium
                            </div>
                            <div class="hero-feature">
                                <span class="material-icons" style="color: #3b82f6;">local_shipping</span>
                                Envío Rápido
                            </div>
                            <div class="hero-feature">
                                <span class="material-icons" style="color: var(--primary-green);">eco</span>
                                Plantas Saludables
                            </div>
                        </div>
                    </div>
                    
                    <div class="hero-visual fade-in-up-delay-1">
                        <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=500&h=400&fit=crop&q=80" 
                             alt="Vivero Los Cocos" 
                             class="hero-image-main">
                        
                        <div class="hero-floating-card top-left">
                            <div class="floating-card-icon">
                                <span class="material-icons">emoji_events</span>
                            </div>
                            <div class="floating-card-title">+500 Productos</div>
                            <div class="floating-card-text">Amplio catálogo</div>
                        </div>
                        
                        <div class="hero-floating-card bottom-right">
                            <div class="floating-card-icon">
                                <span class="material-icons">group</span>
                            </div>
                            <div class="floating-card-title">Clientes Satisfechos</div>
                            <div class="floating-card-text">Miles de jardines felices</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Características -->
    <section class="features-section">
        <div class="container-clean">
            <div class="features-header fade-in-up">
                <h2 class="font-display">¿Por qué elegir Los Cocos?</h2>
                <p>Somos más que un vivero, somos tus compañeros en la aventura de crear espacios verdes extraordinarios.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card fade-in-up-delay-1">
                    <div class="feature-icon">
                        <span class="material-icons">local_florist</span>
                    </div>
                    <h3 class="feature-title">Plantas Seleccionadas</h3>
                    <p class="feature-text">Cada planta es cuidadosamente seleccionada y cultivada con amor para garantizar su salud y belleza.</p>
                </div>
                
                <div class="feature-card fade-in-up-delay-2">
                    <div class="feature-icon">
                        <span class="material-icons">handyman</span>
                    </div>
                    <h3 class="feature-title">Herramientas de Calidad</h3>
                    <p class="feature-text">Equipos profesionales y herramientas duraderas para que tu jardín siempre luzca impecable.</p>
                </div>
                
                <div class="feature-card fade-in-up-delay-3">
                    <div class="feature-icon">
                        <span class="material-icons">school</span>
                    </div>
                    <h3 class="feature-title">Asesoramiento Experto</h3>
                    <p class="feature-text">Nuestro equipo de especialistas te guía en cada paso para crear el jardín de tus sueños.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Productos Destacados -->
    <section class="products-section">
        <div class="container-clean">
            <div class="products-header fade-in-up">
                <h2 class="font-display">Productos Destacados</h2>
                <p>Descubre nuestra selección especial de plantas y productos que transformarán tu jardín en un paraíso verde.</p>
                <a href="<?php echo home_url('/?post_type=product'); ?>" class="products-cta">
                    <span class="material-icons">visibility</span>
                    Ver Todos los Productos
                </a>
            </div>
            
            <!-- Grid de Productos Reales -->
            <div class="products-grid-featured fade-in-up-delay-1">
                <?php 
                if (class_exists('WooCommerce')) {
                    // Intentar productos destacados primero
                    $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => 8,
                        'post_status'    => 'publish',
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'product_visibility',
                                'field'    => 'name',
                                'terms'    => array('featured'),
                                'operator' => 'IN',
                            )
                        )
                    );
                    $query = new WP_Query($args);

                    if (!$query->have_posts()) {
                        // Fallback a últimos productos si no hay destacados
                        wp_reset_postdata();
                        $query = new WP_Query(array(
                            'post_type'      => 'product',
                            'posts_per_page' => 8,
                            'post_status'    => 'publish',
                            'orderby'        => 'date',
                            'order'          => 'DESC',
                        ));
                    }

                    if ($query->have_posts()) :
                        while ($query->have_posts()) : $query->the_post();
                            $product = wc_get_product(get_the_ID());
                            if (!$product) { continue; }
                            // Imagen del producto con fallback a SVG generado si no hay thumbnail
                            if ( function_exists('loscocos_get_product_image_url') ) {
                                $thumb = loscocos_get_product_image_url($product->get_id(), 'woocommerce_thumbnail');
                            } else {
                                $thumb = get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail');
                                $thumb = $thumb ? $thumb : wc_placeholder_img_src('woocommerce_thumbnail');
                            }
                            $categories = wc_get_product_category_list($product->get_id());
                            $price_html = $product->get_price_html();
                            $can_add = $product->is_purchasable() && $product->is_in_stock() && !$product->is_type('variable');
                ?>
                    <div class="product-card-standard">
                        <a href="<?php the_permalink(); ?>" class="product-image" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"/>
                            <div class="hover-overlay"><div class="hover-text">Ver detalle</div></div>
                        </a>
                        <div class="product-content">
                            <a class="product-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            <?php if ($categories) : ?>
                                <div class="product-category"><?php echo wp_kses_post($categories); ?></div>
                            <?php endif; ?>
                            <div class="product-price"><?php echo wp_kses_post($price_html); ?></div>
                            <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                                <?php if ($can_add) : ?>
                                    <button class="btn-clean btn-primary btn-add-to-cart-featured" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                                        <span class="material-icons">add_shopping_cart</span>
                                        Añadir
                                    </button>
                                <?php else : ?>
                                    <a class="btn-clean btn-secondary" href="<?php the_permalink(); ?>">
                                        <span class="material-icons">visibility</span>
                                        Ver producto
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php 
                        endwhile; 
                        wp_reset_postdata();
                    else:
                        echo '<p style="text-align:center; color: var(--text-secondary);">No hay productos para mostrar por ahora.</p>';
                    endif;
                }
                ?>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="cta-section">
        <div class="container-clean">
            <div class="cta-content fade-in-up">
                <h2 class="font-display">¿Listo para transformar tu jardín?</h2>
                <p>Únete a miles de clientes satisfechos que han creado jardines extraordinarios con nuestra ayuda. Tu oasis verde te está esperando.</p>
                <div class="cta-buttons">
                    <?php if (class_exists('WooCommerce')) : ?>
                        <a href="<?php echo home_url('/?post_type=product'); ?>" class="cta-button primary">
                            <span class="material-icons">shopping_bag</span>
                            Ver Catálogo Completo
                        </a>
                        <a href="<?php echo home_url('/cart/'); ?>" class="cta-button secondary">
                            <span class="material-icons">shopping_cart</span>
                            Ver Mi Carrito
                        </a>
                    <?php else : ?>
                        <a href="#" class="cta-button primary">
                            <span class="material-icons">yard</span>
                            Comenzar Ahora
                        </a>
                        <a href="#" class="cta-button secondary">
                            <span class="material-icons">info</span>
                            Más Información
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Modal de Temporada -->
<div class="lc-modal" id="seasonal-modal" role="dialog" aria-modal="true" aria-labelledby="seasonal-title" aria-hidden="true">
    <div class="lc-modal__overlay" data-close></div>
    <div class="lc-modal__dialog">
        <button class="lc-modal__close" type="button" aria-label="Cerrar" data-close>
            <span class="material-icons">close</span>
        </button>
        <div class="lc-modal__header">
            <span class="material-icons" style="color: var(--primary-color);">local_florist</span>
            <h3 class="lc-modal__title" id="seasonal-title">Temporada de Plantación</h3>
        </div>
        <div class="lc-modal__body">
            Es el momento ideal para elegir tus plantas y preparar tu jardín. Descubre nuestras selecciones especiales de temporada.
        </div>
        <div class="lc-modal__actions">
            <a href="<?php echo esc_url( home_url('/?post_type=product') ); ?>" class="btn-clean btn-primary">
                <span class="material-icons">shopping_bag</span>
                Ver catálogo
            </a>
            <button type="button" class="btn-clean btn-secondary" data-close>
                <span class="material-icons">close</span>
                Cerrar
            </button>
        </div>
    </div>
    
    <script>
    (function() {
        const KEY = 'lc_seasonal_popup_v1';
        const DAYS = 14; // volver a mostrar después de 14 días
        const now = Date.now();
        const modal = document.getElementById('seasonal-modal');
        if (!modal) return;
        const raw = localStorage.getItem(KEY);
        const until = raw ? parseInt(raw, 10) : 0;
        if (Number.isFinite(until) && now < until) return;

        const open = () => {
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        };
        const close = () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            localStorage.setItem(KEY, String(now + DAYS * 24 * 60 * 60 * 1000));
        };

        // Abrir de forma no intrusiva
        setTimeout(open, 1200);
        modal.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', close));
        modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
    })();
    </script>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌱 Vivero Los Cocos - Home equilibrada y hermosa cargada');
    
    // Animaciones de entrada con Intersection Observer
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observar todos los elementos con animación
    document.querySelectorAll('.fade-in-up, .fade-in-up-delay-1, .fade-in-up-delay-2, .fade-in-up-delay-3').forEach(el => {
        if (!el.style.animation) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        }
        observer.observe(el);
    });
    
    // Animación para las cards de productos
    document.querySelectorAll('.product-card-standard').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});
</script>

<?php get_footer(); ?>
