<?php
/**
 * The template for displaying the front page
 *
 * @package Los_Cocos_Child
 */

get_header(); ?>

<main id="primary" class="site-main">

    <!-- Hero Section -->
    <section class="relative h-screen min-h-[600px] flex items-center justify-center overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1470058869958-2a77ade41c02?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
                alt="Lush Greenhouse"
                class="w-full h-full object-cover object-center transform scale-105 animate-slow-zoom">
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-black/60"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 text-center text-white px-4 max-w-5xl mx-auto" data-aos="fade-up">
            <span
                class="inline-block py-1 px-3 rounded-full bg-white/20 backdrop-blur-sm text-sm font-medium tracking-wider mb-6 border border-white/30">
                EST. 2024 • MENDOZA
            </span>
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-serif font-bold mb-6 leading-tight tracking-tight">
                Vida Verde <br /><span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-secondary-light to-white">Para Tu
                    Hogar</span>
            </h1>
            <p class="text-lg md:text-2xl mb-10 max-w-2xl mx-auto font-light text-white/90 leading-relaxed">
                Transformamos espacios con plantas seleccionadas y macetas de diseño. Calidad premium para tu jardín
                interior.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="<?php echo wc_get_page_permalink('shop'); ?>"
                    class="group relative px-8 py-4 bg-primary hover:bg-primary-dark text-white font-medium rounded-full transition-all duration-300 shadow-lg hover:shadow-primary/50 overflow-hidden">
                    <span class="relative z-10 flex items-center gap-2">
                        Ver Colección
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </span>
                </a>
                <a href="#featured"
                    class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-medium rounded-full transition-all duration-300 border border-white/30 hover:border-white/50">
                    Explorar Tendencias
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3">
                </path>
            </svg>
        </div>
    </section>

    <!-- 🔥 OFERTAS DESTACADAS - Productos con Precio y Compra Directa -->
    <section class="py-16 relative" style="background: linear-gradient(135deg, #fef3c7 0%, #fff7ed 50%, #fef3c7 100%);">
        <!-- Badge de Oferta -->
        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <span style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%); color: white; padding: 0.75rem 2rem; border-radius: 9999px; font-weight: 700; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.1em; box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);">
                🔥 Ofertas de la Semana
            </span>
        </div>
        
        <div class="container mx-auto px-4 pt-8">
            <div class="text-center mb-10">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: #1f2937; font-family: 'Merriweather', serif; margin-bottom: 0.5rem;">
                    Productos en Oferta
                </h2>
                <p style="color: #6b7280; font-size: 1.125rem;">Precios especiales por tiempo limitado</p>
            </div>

            <!-- Grid de Productos en Oferta -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <?php
                // Obtener productos en oferta
                $sale_products = wc_get_products(array(
                    'status' => 'publish',
                    'limit' => 4,
                    'on_sale' => true,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                // Si no hay productos en oferta, mostrar los más recientes
                if (empty($sale_products)) {
                    $sale_products = wc_get_products(array(
                        'status' => 'publish',
                        'limit' => 4,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                }
                
                foreach ($sale_products as $product) :
                    $product_id = $product->get_id();
                    $product_name = $product->get_name();
                    $product_url = get_permalink($product_id);
                    $product_image = wp_get_attachment_image_src($product->get_image_id(), 'medium');
                    $product_image_url = $product_image ? $product_image[0] : wc_placeholder_img_src('medium');
                    $regular_price = $product->get_regular_price();
                    $sale_price = $product->get_sale_price();
                    $current_price = $product->get_price();
                    $is_on_sale = $product->is_on_sale();
                ?>
                <div style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: all 0.3s ease;">
                    <!-- Imagen del Producto -->
                    <a href="<?php echo esc_url($product_url); ?>" style="display: block; position: relative;">
                        <?php if ($is_on_sale) : ?>
                        <span style="position: absolute; top: 0.75rem; left: 0.75rem; background: #dc2626; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; z-index: 10;">
                            OFERTA
                        </span>
                        <?php endif; ?>
                        <img src="<?php echo esc_url($product_image_url); ?>" 
                             alt="<?php echo esc_attr($product_name); ?>"
                             style="width: 100%; height: 200px; object-fit: cover;">
                    </a>
                    
                    <!-- Info del Producto -->
                    <div style="padding: 1rem;">
                        <a href="<?php echo esc_url($product_url); ?>" style="text-decoration: none;">
                            <h3 style="font-size: 1rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.8rem;">
                                <?php echo esc_html($product_name); ?>
                            </h3>
                        </a>
                        
                        <!-- Precio -->
                        <div style="margin-bottom: 1rem;">
                            <?php if ($is_on_sale && $regular_price) : ?>
                                <span style="text-decoration: line-through; color: #9ca3af; font-size: 0.875rem; margin-right: 0.5rem;">
                                    $<?php echo number_format($regular_price, 0, ',', '.'); ?>
                                </span>
                                <span style="font-size: 1.5rem; font-weight: 700; color: #dc2626;">
                                    $<?php echo number_format($current_price, 0, ',', '.'); ?>
                                </span>
                            <?php else : ?>
                                <span style="font-size: 1.5rem; font-weight: 700; color: #059669;">
                                    $<?php echo number_format($current_price, 0, ',', '.'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Botón Agregar al Carrito -->
                        <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" 
                           data-quantity="1" 
                           data-product_id="<?php echo esc_attr($product_id); ?>"
                           class="ajax_add_to_cart add_to_cart_button"
                           style="display: block; width: 100%; text-align: center; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; text-decoration: none; transition: all 0.3s ease;">
                            🛒 Agregar al Carrito
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Botón Ver Todas las Ofertas -->
            <div style="text-align: center; margin-top: 2rem;">
                <a href="<?php echo wc_get_page_permalink('shop'); ?>?on_sale=1" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; background: #1f2937; color: white; padding: 1rem 2rem; border-radius: 9999px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;">
                    Ver Todas las Ofertas
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Features / USP Section -->
    <section class="py-16 bg-cream border-b border-neutral-200">
        <div class="container mx-auto px-4">
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-neutral-300">
                <div class="p-4 group">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-primary/10 rounded-full flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-serif font-bold text-primary-dark mb-2">Calidad Garantizada</h3>
                    <p class="text-neutral-medium">Plantas sanas y fuertes, seleccionadas una por una.</p>
                </div>
                <div class="p-4 group">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-primary/10 rounded-full flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-serif font-bold text-primary-dark mb-2">Envío Rápido</h3>
                    <p class="text-neutral-medium">Entregas en Mendoza en 24/48hs hábiles.</p>
                </div>
                <div class="p-4 group">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-primary/10 rounded-full flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-serif font-bold text-primary-dark mb-2">Asesoramiento</h3>
                    <p class="text-neutral-medium">Te ayudamos a elegir la planta ideal para tu espacio.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <span class="text-accent font-medium tracking-wider uppercase text-sm">Nuestras Colecciones</span>
                <h2 class="text-4xl md:text-5xl font-serif font-bold text-primary-dark mt-3 mb-6">Explora por Categoría
                </h2>
                <div class="w-24 h-1 bg-accent mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Category 1 -->
                <a href="<?php echo wc_get_page_permalink('shop'); ?>"
                    class="group relative h-[500px] rounded-2xl overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1485955900006-10f4d324d411?ixlib=rb-4.0.3&auto=format&fit=crop&w=2072&q=80"
                        alt="Plantas de Interior"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-90 group-hover:opacity-100 transition-opacity">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 p-8 w-full transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <h3 class="text-3xl font-serif font-bold text-white mb-2">Interior</h3>
                        <p
                            class="text-white/80 mb-4 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                            Dale vida a tus ambientes con nuestra selección premium.</p>
                        <span
                            class="inline-flex items-center text-white font-medium border-b border-white pb-1 group-hover:text-accent group-hover:border-accent transition-colors">
                            Ver Productos <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </span>
                    </div>
                </a>

                <!-- Category 2 -->
                <a href="<?php echo wc_get_page_permalink('shop'); ?>"
                    class="group relative h-[500px] rounded-2xl overflow-hidden shadow-lg lg:mt-12">
                    <img src="https://images.unsplash.com/photo-1463320726281-696a485928c7?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
                        alt="Macetas y Accesorios"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-90 group-hover:opacity-100 transition-opacity">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 p-8 w-full transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <h3 class="text-3xl font-serif font-bold text-white mb-2">Macetas</h3>
                        <p
                            class="text-white/80 mb-4 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                            Diseños únicos para realzar la belleza de tus plantas.</p>
                        <span
                            class="inline-flex items-center text-white font-medium border-b border-white pb-1 group-hover:text-accent group-hover:border-accent transition-colors">
                            Ver Productos <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </span>
                    </div>
                </a>

                <!-- Category 3 -->
                <a href="<?php echo wc_get_page_permalink('shop'); ?>"
                    class="group relative h-[500px] rounded-2xl overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1501004318641-b39e6451bec6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1973&q=80"
                        alt="Cuidados y Sustratos"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-90 group-hover:opacity-100 transition-opacity">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 p-8 w-full transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <h3 class="text-3xl font-serif font-bold text-white mb-2">Cuidados</h3>
                        <p
                            class="text-white/80 mb-4 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                            Todo lo necesario para mantener tus plantas felices.</p>
                        <span
                            class="inline-flex items-center text-white font-medium border-b border-white pb-1 group-hover:text-accent group-hover:border-accent transition-colors">
                            Ver Productos <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="py-24 bg-cream-light relative overflow-hidden">
        <!-- Decorative Elements -->
        <div
            class="absolute top-0 left-0 w-64 h-64 bg-secondary/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 right-0 w-96 h-96 bg-accent/5 rounded-full blur-3xl translate-x-1/3 translate-y-1/3">
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div>
                    <span class="text-accent font-medium tracking-wider uppercase text-sm">Destacados</span>
                    <h2 class="text-4xl md:text-5xl font-serif font-bold text-primary-dark mt-2">Favoritos de la Semana
                    </h2>
                </div>
                <a href="<?php echo wc_get_page_permalink('shop'); ?>"
                    class="hidden md:inline-flex items-center text-primary font-medium hover:text-accent transition-colors group">
                    Ver Todo el Catálogo
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <!-- WooCommerce Shortcode for Featured Products -->
            <div class="woocommerce-featured-grid">
                <?php echo do_shortcode('[products limit="4" columns="4" visibility="featured"]'); ?>
            </div>

            <div class="mt-12 text-center md:hidden">
                <a href="<?php echo wc_get_page_permalink('shop'); ?>"
                    class="inline-block px-8 py-3 border-2 border-primary text-primary font-medium rounded-full hover:bg-primary hover:text-white transition-colors">
                    Ver Todo el Catálogo
                </a>
            </div>
        </div>
    </section>

    <!-- About / Story Section -->
    <section class="py-24 bg-primary-dark text-white relative overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
                alt="Background" class="w-full h-full object-cover opacity-20">
            <div class="absolute inset-0 bg-gradient-to-r from-primary-dark via-primary-dark/90 to-transparent"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-2xl">
                <span class="text-accent-light font-medium tracking-wider uppercase text-sm mb-4 block">Sobre
                    Nosotros</span>
                <h2 class="text-4xl md:text-5xl font-serif font-bold mb-8 leading-tight">Cultivando Pasión <br />Desde
                    Mendoza</h2>
                <p class="text-lg text-white/80 mb-8 leading-relaxed">
                    En Vivero Los Cocos, no solo vendemos plantas; compartimos vida. Cada ejemplar es cuidado con
                    dedicación para asegurar que llegue a tu hogar listo para prosperar. Creemos en el poder de la
                    naturaleza para transformar espacios y mejorar el bienestar.
                </p>
                <div class="grid grid-cols-2 gap-8 mb-10">
                    <div>
                        <span class="block text-4xl font-bold text-accent mb-2">500+</span>
                        <span class="text-sm text-white/60 uppercase tracking-wider">Clientes Felices</span>
                    </div>
                    <div>
                        <span class="block text-4xl font-bold text-accent mb-2">100%</span>
                        <span class="text-sm text-white/60 uppercase tracking-wider">Garantía de Calidad</span>
                    </div>
                </div>
                <a href="/about"
                    class="inline-block px-8 py-4 bg-white text-primary-dark font-bold rounded-full hover:bg-accent hover:text-white transition-all shadow-lg">
                    Conoce Nuestra Historia
                </a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-24 bg-cream">
        <div class="container mx-auto px-4">
            <div
                class="bg-white rounded-3xl p-8 md:p-16 shadow-xl relative overflow-hidden max-w-5xl mx-auto text-center">
                <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/20 rounded-full blur-3xl -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-accent/10 rounded-full blur-3xl -ml-20 -mb-20"></div>

                <div class="relative z-10">
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary-dark mb-4">Únete a Nuestra
                        Comunidad Verde</h2>
                    <p class="text-neutral-medium mb-8 max-w-2xl mx-auto">Recibe consejos de cuidado, ofertas exclusivas
                        y novedades directamente en tu bandeja de entrada.</p>

                    <form class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
                        <input type="email" placeholder="Tu correo electrónico"
                            class="flex-1 px-6 py-4 rounded-full bg-neutral-light border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <button type="button"
                            class="px-8 py-4 bg-primary hover:bg-primary-dark text-white font-bold rounded-full shadow-lg hover:shadow-primary/50 transition-all transform hover:-translate-y-1">
                            Suscribirse
                        </button>
                    </form>
                    <p class="text-xs text-neutral-medium mt-4">Respetamos tu privacidad. Date de baja en cualquier
                        momento.</p>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>