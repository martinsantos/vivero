<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    <style>
        /* Base styles */
        :root {
            --primary-color: #10B981;
            --primary-dark: #059669;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background-color: #f8fafc;
        }
        
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        /* Base Reset */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #1f2937; background: #f8fafc; }
        
        /* Container */
        .container { width: 100%; max-width: 1280px; margin: 0 auto; padding: 0 15px; }
        
        /* Product Grid */
        .product-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 1.5rem; 
            padding: 1.25rem 0;
            width: 100%;
            max-width: 100%;
        }
        
        /* Product Card */
        .product-card {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .product-image {
            position: relative;
            width: 100%;
            padding-top: 100%; /* 1:1 Aspect Ratio */
            overflow: hidden;
        }
        
        .product-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-label {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: var(--primary-color);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            z-index: 2;
        }
        
        .product-info {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1.375;
        }
        
        .product-category {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.75rem;
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: auto;
            padding-top: 0.75rem;
        }
        
        .product-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .add-to-cart {
            flex: 1;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .add-to-cart:hover {
            background: var(--primary-dark);
        }
        
        .add-to-cart .material-icons {
            font-size: 1.25rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                padding: 0.75rem;
            }
            
            .product-info {
                padding: 1rem;
            }
            
            .product-title {
                font-size: 1rem;
            }
            
            .product-price {
                font-size: 1.125rem;
            }
        }
        
        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr;
                padding: 0.5rem;
            }
        }
            background: white; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Product Image */
        .product-image {
            height: 200px;
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .product-image img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            padding: 15px;
        }
        
        /* Product Labels */
        .product-labels {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .product-label {
            font-size: 12px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 12px;
            color: white;
        }
        .label-sale { background: #f59e0b; }
        .label-new { background: #10b981; }
        
        /* Product Content */
        .product-content { padding: 15px; }
        .product-title { 
            font-size: 16px; 
            font-weight: 600; 
            margin-bottom: 8px;
            color: #1f2937;
        }
        .product-category {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            margin-top: auto;
        }
        .product-price {
            font-size: 18px;
            font-weight: 700;
            color: #10b981;
        }
        .add-to-cart {
            background: #10b981;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: background 0.2s;
        }
        .add-to-cart:hover {
            background: #059669;
        }
        .add-to-cart .material-icons {
            font-size: 16px;
        }
        :root {
            --primary-color: #10B981;
            --primary-dark: #059669;
            --secondary-color: #F8FAFC;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --accent-color: #F59E0B;
            --border-color: #E5E7EB;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }
        
        body {
            font-family: "Inter", system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background-color: var(--secondary-color);
        }
        
        /* Sistema de tipografía mejorado */
        .font-display {
            font-family: "Poppins", "Inter", sans-serif;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        
        .font-body {
            font-family: "Inter", system-ui, sans-serif;
            font-weight: 400;
        }
        
        /* Sistema de botones limpio y profesional */
        .btn-clean {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            line-height: 1.5;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: 1px solid transparent;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            background: linear-gradient(135deg, var(--primary-dark), #047857);
        }
        
        .btn-secondary {
            background: white;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background: #F9FAFB;
            border-color: var(--primary-color);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Cards y contenedores limpios */
        .clean-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        
        .clean-card:hover {
            box-shadow: var(--shadow-md);
            border-color: #D1D5DB;
        }

        /* Banner superior */
        .top-banner {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Header mejorado */
        .main-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
        }

        /* Container responsivo */
        .container-clean {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Utilidades adicionales */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Efectos de hover mejorados */
        .group:hover .group-hover\:scale-105 {
            transform: scale(1.05);
        }

        /* Animaciones para modales */
        @keyframes modal-enter {
            0% {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes modal-backdrop {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        .animate-modal-enter {
            animation: modal-enter 0.3s ease-out;
        }

        .animate-modal-backdrop {
            animation: modal-backdrop 0.3s ease-out;
        }

        /* Efectos adicionales para productos */
        .seasonal-product-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Animaciones de pulso personalizadas */
        @keyframes pulse-slow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        body { font-family: 'Inter', sans-serif; }
        .container { width: 100%; max-width: 1280px; margin: 0 auto; padding: 0 1rem; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .product-card { border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden; }
        .product-image { height: 200px; background: #f9fafb; display: flex; align-items: center; justify-content: center; }
        .product-image img { max-height: 100%; max-width: 100%; object-fit: contain; }
        .product-content { padding: 1rem; }
        .product-title { font-weight: 600; margin-bottom: 0.5rem; }
        .product-price { color: #10b981; font-weight: 700; font-size: 1.125rem; }
        .add-to-cart { background: #10b981; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.375rem; cursor: pointer; }
        .add-to-cart:hover { background: #059669; }
    </style>
</head>
<body <?php body_class(); ?>>

<!-- Banner Superior -->
<div class="top-banner">
    <div class="container-clean">
        <div class="flex items-center justify-center text-white text-sm font-medium">
            <span class="mr-2">🚚</span>
            <span class="font-semibold">ENVÍO GRATIS EL MISMO DÍA EN MENDOZA</span>
            <span class="mx-3">•</span>
            <span>Pedidos antes de las 14hs</span>
            <span class="ml-2">⚡</span>
        </div>
    </div>
</div>

<!-- Header Principal -->
<header class="main-header">
    <div class="container-clean">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-white font-bold text-sm">🌿</span>
                </div>
                <a href="<?php echo home_url(); ?>" class="font-display text-2xl font-bold text-gray-900">
                    Los Cocos
                </a>
            </div>
            
            <!-- Navegación -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="<?php echo home_url(); ?>" class="font-body text-gray-700 hover:text-green-600 transition-colors">Inicio</a>
                <a href="<?php echo home_url('/?post_type=product'); ?>" class="font-body text-gray-700 hover:text-green-600 transition-colors">Productos</a>
                <a href="#" class="font-body text-gray-700 hover:text-green-600 transition-colors">Servicios</a>
                <a href="<?php echo home_url('/?page_id=560'); ?>" class="font-body text-gray-700 hover:text-green-600 transition-colors">Consejos</a>
                <a href="#" class="btn-clean btn-primary">
                    <span>⭐</span>
                    <span>Club Premium</span>
                </a>
                <a href="#" class="font-body text-gray-700 hover:text-green-600 transition-colors">Contacto</a>
            </nav>
            
            <!-- Buscador y Carrito -->
            <div class="flex items-center space-x-4">
                <div class="relative hidden md:block">
                    <input type="text" placeholder="Buscar plantas..." 
                           class="w-64 px-4 py-2 pl-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-400">🔍</span>
                    </div>
                </div>
                
                <a href="<?php echo wc_get_cart_url(); ?>" class="relative p-2 text-gray-700 hover:text-green-600 transition-colors">
                    <span class="text-xl">🛒</span>
                    <?php if (function_exists('WC') && WC()->cart) : ?>
                        <span class="cart-count absolute -top-1 -right-1 bg-green-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo WC()->cart->get_cart_contents_count(); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</header>

<main class="flex-1 bg-white"><?php wp_body_open(); ?> 