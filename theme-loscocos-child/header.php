<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class('font-sans text-neutral-dark antialiased bg-neutral-light'); ?>>
    <?php wp_body_open(); ?>

    <div id="page" class="site min-h-screen flex flex-col">
        <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'loscocos'); ?></a>

        <!-- Header -->
        <header id="masthead"
            class="site-header fixed w-full z-50 transition-all duration-300 <?php echo is_front_page() ? 'bg-transparent text-white' : 'bg-white text-primary-dark shadow-sm'; ?>">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between h-20">

                    <!-- Logo -->
                    <div class="site-branding flex-shrink-0">
                        <?php
                        if (has_custom_logo()) {
                            the_custom_logo();
                        } else {
                            ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"
                                class="text-2xl font-serif font-bold tracking-tight hover:opacity-80 transition-opacity">
                                Vivero Los Cocos
                            </a>
                            <?php
                        }
                        ?>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav id="site-navigation" class="main-navigation hidden md:block">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'menu-1',
                            'menu_id' => 'primary-menu',
                            'container' => false,
                            'menu_class' => 'flex space-x-8 font-medium',
                            'fallback_cb' => false, // We'll hardcode links if no menu is assigned for now
                        ));

                        // Fallback links if no menu assigned
                        if (!has_nav_menu('menu-1')): ?>
                            <ul class="flex space-x-8 font-medium">
                                <li><a href="<?php echo function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop'); ?>"
                                        class="hover:text-accent transition-colors">Tienda</a></li>
                                <li><a href="<?php echo function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop'); ?>"
                                        class="hover:text-accent transition-colors">Plantas</a></li>
                                <li><a href="<?php echo function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop'); ?>"
                                        class="hover:text-accent transition-colors">Macetas</a></li>
                                <li><a href="https://wa.me/5402614399025" target="_blank"
                                        class="hover:text-accent transition-colors">Contacto</a></li>
                            </ul>
                        <?php endif; ?>
                    </nav>

                    <!-- Icons -->
                    <div class="header-icons flex items-center space-x-6">
                        <button id="search-toggle" class="hover:text-accent transition-colors" aria-label="Search">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <a href="<?php echo function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart'); ?>"
                            class="relative hover:text-accent transition-colors" aria-label="Cart">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <?php if (function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0): ?>
                                <span
                                    class="absolute -top-2 -right-2 bg-accent text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                    <?php echo WC()->cart->get_cart_contents_count(); ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- Mobile Menu Button -->
                        <button id="mobile-menu-toggle" class="md:hidden hover:text-accent transition-colors" aria-label="Menu">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Bar (hidden by default) -->
            <div id="search-bar" class="hidden bg-white border-t border-gray-200 py-4">
                <div class="container mx-auto px-4">
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex gap-2">
                        <input type="search" name="s" placeholder="Buscar productos..."
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            value="<?php echo get_search_query(); ?>">
                        <button type="submit"
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors font-medium">
                            Buscar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mobile Navigation (hidden by default) -->
            <nav id="mobile-navigation" class="hidden md:hidden bg-white border-t border-gray-200">
                <div class="container mx-auto px-4 py-4">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'menu-1',
                        'menu_id' => 'mobile-menu',
                        'container' => false,
                        'menu_class' => 'flex flex-col space-y-3 font-medium text-primary-dark',
                        'fallback_cb' => false,
                    ));

                    if (!has_nav_menu('menu-1')): ?>
                        <ul class="flex flex-col space-y-3 font-medium text-primary-dark">
                            <li><a href="<?php echo function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop'); ?>"
                                    class="block py-2 hover:text-accent transition-colors">Tienda</a></li>
                            <li><a href="<?php echo function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop'); ?>"
                                    class="block py-2 hover:text-accent transition-colors">Plantas</a></li>
                            <li><a href="<?php echo function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop'); ?>"
                                    class="block py-2 hover:text-accent transition-colors">Macetas</a></li>
                            <li><a href="https://wa.me/5402614399025" target="_blank"
                                    class="block py-2 hover:text-accent transition-colors">Contacto</a></li>
                        </ul>
                    <?php endif; ?>
                </div>
            </nav>
        </header>
