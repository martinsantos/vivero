</main>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-16">
    <div class="container-clean">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Logo y descripción -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-white font-bold">🌿</span>
                    </div>
                    <h3 class="font-display text-2xl font-bold">Los Cocos</h3>
                </div>
                <p class="text-gray-300 font-body mb-6 max-w-md">
                    Tu vivero de confianza en Mendoza. Especialistas en plantas, jardines y todo lo que necesitas para crear tu oasis verde.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-green-600 transition-colors">
                        <span>📱</span>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-green-600 transition-colors">
                        <span>📧</span>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-green-600 transition-colors">
                        <span>📍</span>
                    </a>
                </div>
            </div>
            
            <!-- Enlaces rápidos -->
            <div>
                <h4 class="font-display font-semibold text-lg mb-4">Enlaces Rápidos</h4>
                <ul class="space-y-2 font-body">
                    <li><a href="<?php echo home_url(); ?>" class="text-gray-300 hover:text-green-400 transition-colors">Inicio</a></li>
                    <li><a href="<?php echo home_url('/?post_type=product'); ?>" class="text-gray-300 hover:text-green-400 transition-colors">Productos</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-green-400 transition-colors">Servicios</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-green-400 transition-colors">Consejos</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-green-400 transition-colors">Contacto</a></li>
                </ul>
            </div>
            
            <!-- Contacto -->
            <div>
                <h4 class="font-display font-semibold text-lg mb-4">Contacto</h4>
                <div class="space-y-3 font-body text-gray-300">
                    <div class="flex items-center">
                        <span class="mr-2">📞</span>
                        <span>+54 9 261 123-4567</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">📧</span>
                        <span>info@loscocos.com.ar</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">📍</span>
                        <span>Mendoza, Argentina</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">🕒</span>
                        <span>Lun-Sáb: 8:00-18:00</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Línea divisoria -->
        <div class="border-t border-gray-700 mt-12 pt-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <p class="text-gray-400 font-body text-sm">
                    © 2024 Vivero Los Cocos. Todos los derechos reservados.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-green-400 text-sm font-body transition-colors">Política de Privacidad</a>
                    <a href="#" class="text-gray-400 hover:text-green-400 text-sm font-body transition-colors">Términos de Servicio</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html> 