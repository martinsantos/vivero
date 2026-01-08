<?php
/**
 * Template Name: Nano Banana Concept (Prototype)
 * Description: High-fidelity prototype for the new "Nano Banana" design system
 */

// Load essential WP header but skip standard theme output if we want full control
get_header(); 
?>

<!-- Nano Banana Prototype Wrapper -->
<div class="nano-body">
    
    <!-- 1. Nano Nav (Glassmorphism) -->
    <nav class="nano-nav nano-glass">
        <a href="#" class="nano-nav-link">Shop</a>
        <a href="#" class="nano-nav-link">About</a>
        <a href="#" class="nano-nav-link">Journal</a>
        <a href="#" class="nano-nav-link">Contact</a>
    </nav>

    <!-- 2. Cinematic Hero -->
    <header class="nano-hero">
        <div class="nano-hero-bg">
            <!-- Placeholder for high-res hero. Using a solid color fallback or generated image -->
            <img src="https://images.unsplash.com/photo-1596720426673-e4aaa162b712?q=80&w=2942&auto=format&fit=crop" 
                 alt="Nano Hero" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7);">
        </div>
        
        <div class="nano-hero-content">
            <h1 class="nano-heading-hero">
                Nature,<br>
                <span style="font-style: italic;">Curated.</span>
            </h1>
            
            <div style="margin-top: 32px;">
                <button class="btn-nano">
                    Explorar Colección
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 8px;">
                        <path d="M5 12H19" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 5L19 12L12 19" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            
            <!-- Nano Search -->
            <div class="nano-glass" style="margin-top: 64px; padding: 12px 24px; border-radius: 99px; display: inline-flex; align-items: center; width: 400px; max-width: 90vw;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.7;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" placeholder="Buscar 'Monstera'..." style="background: transparent; border: none; color: white; margin-left: 12px; width: 100%; outline: none; font-family: var(--font-tech); font-size: 16px;">
            </div>
        </div>
    </header>

    <!-- 3. Shop Grid (Masonry Preview) -->
    <section style="padding: 120px 24px; max-width: var(--container-width); margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 80px;">
            <span style="color: var(--nano-banana-yellow); font-family: var(--font-tech); text-transform: uppercase; letter-spacing: 0.2em; font-size: 12px; font-weight: 700;">Nueva Colección</span>
            <h2 class="nano-heading-section" style="margin-top: 16px;">Rareza Botánica</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 40px;">
            <?php 
            // Loop de ejemplo con productos reales si existen, o placeholders
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 3,
                'orderby' => 'rand'
            );
            $loop = new WP_Query( $args );
            if ( $loop->have_posts() ) :
                while ( $loop->have_posts() ) : $loop->the_post();
                    global $product;
                    $image = get_the_post_thumbnail_url($product->get_id(), 'large') ?: 'https://via.placeholder.com/600';
            ?>
                <div class="nano-card">
                    <div class="nano-card-image-wrapper">
                        <img src="<?php echo esc_url($image); ?>" class="nano-card-image" alt="Plant">
                    </div>
                    <div>
                        <h3 style="font-family: var(--font-serif); font-size: 24px; margin: 0;"><?php the_title(); ?></h3>
                        <p style="color: #999; font-size: 14px; margin: 8px 0 16px 0;">Interior / Sombra</p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 18px;"><?php echo $product->get_price_html(); ?></span>
                            <span style="width: 32px; height: 32px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">+</span>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </section>

</div>

<?php get_footer(); ?>
