// modal.js
// Manejo del modal mejorado con pack de productos y temporadas

// Variables globales
let packQuantity = 1;
const packBasePrice = 20100;
let modalOpen = false;
let countdownInterval;
let selectedSeason = 'primavera';

// Datos de temporadas
const seasonData = {
  primavera: {
    icon: '🌸',
    title: 'Consejos de Primavera',
    advice: 'La primavera es perfecta para plantar nuevas especies. Aumenta el riego gradualmente y prepara el sustrato con compost fresco. Es el momento ideal para trasplantar y fertilizar.'
  },
  verano: {
    icon: '☀️',
    title: 'Consejos de Verano',
    advice: 'En verano, intensifica el riego pero evita el encharcamiento. Proporciona sombra en las horas más calurosas y pulveriza las hojas regularmente para mantener la humedad.'
  },
  otono: {
    icon: '🍂',
    title: 'Consejos de Otoño',
    advice: 'El otoño es tiempo de preparación. Reduce gradualmente el riego, aplica abono orgánico y protege las plantas más sensibles del frío que se aproxima.'
  },
  invierno: {
    icon: '❄️',
    title: 'Consejos de Invierno',
    advice: 'En invierno, las plantas entran en reposo. Riega con moderación, mantén las plantas de interior alejadas de calefacciones y asegura buena ventilación.'
  }
};

// Mapping de estilos por temporada
const seasonStyles = {
  primavera: 'from-green-400 via-green-300 to-emerald-400',
  verano: 'from-yellow-400 via-yellow-300 to-orange-400',
  otono: 'from-orange-600 via-red-600 to-yellow-600',
  invierno: 'from-blue-300 via-blue-400 to-indigo-400'
};

// Productos por temporada
const seasonalProducts = {
  primavera: [
    {id:'tulipanes', name:'Tulipanes Radiantes', image:'https://images.unsplash.com/photo-1529539792455-d227a94b1ae4?auto=format&fit=crop&w=600&q=80', oldPrice:11000, newPrice:6000, features:[{icon:'💧',value:'Semanal'},{icon:'☀️',value:'Sol directo'}]},
    {id:'petunias', name:'Petunias Coloridas', image:'https://images.unsplash.com/photo-1505855795416-5c5b5f3de72b?auto=format&fit=crop&w=600&q=80', oldPrice:9000, newPrice:5500, features:[{icon:'💧',value:'Diario'},{icon:'☀️',value:'Media sombra'}]},
    {id:'violas', name:'Violas Elegantes', image:'https://images.unsplash.com/photo-1524597991980-0dbd19b6aa7c?auto=format&fit=crop&w=600&q=80', oldPrice:7000, newPrice:4000, features:[{icon:'💧',value:'C/2 días'},{icon:'☀️',value:'Indirecta'}]}
  ],
  verano: [
    {id:'lavanda', name:'Lavanda Aromática', image:'https://lh3.googleusercontent.com/aida-public/AB6AXuDIBncUh4bnuR-K8RhUmSlk4JG6i6VX7l27JJqaceROWmPoXpCxPUJOMt4w7Cw5tGfBnb7NUgEVUYTU-s56WxNr8BqcjuqGtcPTfp6A0VWlD8ZuKZoE8PvlPOnkj7NdLIn0uh9yoTKIlmZoMZKtwrsGAAblEPesO9vRxSebK5VxDBKrEsS4MLHGNOg_w3RnCztVUfok1JBwNqDSKfTXD5nCmSCYwtmjAFHP1SSZULOucJm-Rz9jMc7X0mycjf0JWdSBwJlszYI3M4tv', oldPrice:8900, newPrice:5300, features:[{icon:'💧',value:'Bisemanal'},{icon:'☀️',value:'Sol directo'}]},
    {id:'aloe', name:'Aloe Vera', image:'https://images.unsplash.com/photo-1598374446045-3b19a513d375?auto=format&fit=crop&w=600&q=80', oldPrice:12000, newPrice:7000, features:[{icon:'💧',value:'Mes'},{icon:'☀️',value:'Sol parcial'}]},
    {id:'girasol', name:'Girasol', image:'https://images.unsplash.com/photo-1572441710263-6f5b32fce6fa?auto=format&fit=crop&w=600&q=80', oldPrice:5000, newPrice:3500, features:[{icon:'💧',value:'Semanal'},{icon:'☀️',value:'Sol intenso'}]}
  ],
  otono: [
    {id:'crisantemo', name:'Crisantemos Otoñales', image:'https://images.unsplash.com/photo-1571689933690-27157af1dbd8?auto=format&fit=crop&w=600&q=80', oldPrice:10000, newPrice:6000, features:[{icon:'💧',value:'Semanal'},{icon:'☀️',value:'Media sombra'}]},
    {id:'helecho', name:'Helecho de Otoño', image:'https://images.unsplash.com/photo-1600459489110-58e4fd28cb05?auto=format&fit=crop&w=600&q=80', oldPrice:8000, newPrice:4500, features:[{icon:'💧',value:'2 veces/semana'},{icon:'☀️',value:'Indirecta'}]},
    {id:'arce', name:'Arce Japonés', image:'https://images.unsplash.com/photo-1470167290877-7d2b47f36d72?auto=format&fit=crop&w=600&q=80', oldPrice:15000, newPrice:9000, features:[{icon:'💧',value:'Semanal'},{icon:'☀️',value:'Sol parcial'}]}
  ],
  invierno: [
    {id:'poinsettia', name:'Poinsettia Navideña', image:'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?auto=format&fit=crop&w=600&q=80', oldPrice:7000, newPrice:4000, features:[{icon:'💧',value:'C/10 días'},{icon:'☀️',value:'Sol indirecto'}]},
    {id:'ciclamen', name:'Ciclamen Invernal', image:'https://images.unsplash.com/photo-1518020382113-a7e8fc38eac9?auto=format&fit=crop&w=600&q=80', oldPrice:9000, newPrice:5500, features:[{icon:'💧',value:'Semanal'},{icon:'☀️',value:'Media sombra'}]},
    {id:'hiedra', name:'Hiedra Clásica', image:'https://images.unsplash.com/photo-1560615304-5caf078d1b92?auto=format&fit=crop&w=600&q=80', oldPrice:6000, newPrice:3000, features:[{icon:'💧',value:'Semanal'},{icon:'☀️',value:'Indirecta'}]}
  ]
};

// Sugerencias de cuidados por temporada
const seasonCare = {
  primavera: `
    <strong>Plantación y trasplante:</strong> Es el momento ideal para sembrar nuevas plantas y flores, especialmente las que florecen en primavera, como tulipanes, narcisos y rosas. Trasplanta las plantas que necesiten temperaturas más suaves.<br>
    <strong>Poda:</strong> Podar los arbustos que hayan terminado de florecer. También es un buen momento para podar árboles y trepadoras, excepto aquellos que florecen en primavera.<br>
    <strong>Riego:</strong> Empieza a regar las plantas y macetas con suavidad, evitando encharcamientos. El mejor momento para regar es a primera hora de la mañana.<br>
    <strong>Control de plagas:</strong> Marzo es el mes del pulgón, así que, si es necesario, trata las plagas a tiempo para evitar que se propaguen.<br>
    <strong>Césped:</strong> El césped requiere cortes regulares, dependiendo de su velocidad de crecimiento. Si hay musgo, usa un fertilizante que lo combata.
  `,
  verano: `
    <strong>Riego:</strong> Aumenta la frecuencia del riego, especialmente en las horas más frescas del día (temprano por la mañana o al atardecer).<br>
    <strong>Poda:</strong> Podar árboles, arbustos y trepadoras para mantener su forma y estimular un nuevo crecimiento.<br>
    <strong>Protección solar:</strong> Asegúrate de que las plantas no estén expuestas al sol directo durante las horas más calurosas del día.<br>
    <strong>Control de plagas:</strong> Vigila la presencia de plagas y enfermedades y actúa rápidamente si es necesario.
  `,
  otono: `
    <strong>Limpieza:</strong> Retira las hojas caídas, ramas secas y restos de plantas muertas para evitar la proliferación de enfermedades.<br>
    <strong>Reducción del riego:</strong> Disminuye la frecuencia de riego a medida que las temperaturas bajan.<br>
    <strong>Abono:</strong> Aplica abono para preparar las plantas para el invierno y proporcionarles los nutrientes necesarios.<br>
    <strong>Protección contra el frío:</strong> Protege las plantas más vulnerables del frío, especialmente las que están en macetas.
  `,
  invierno: `
    <strong>Limpieza:</strong> Retira las plantas podridas y secas, y elimina la maleza.<br>
    <strong>Protección de plantas:</strong> Utiliza invernaderos o cubiertas para proteger las plantas más sensibles al frío.<br>
    <strong>Herramientas:</strong> Guarda las herramientas de jardín y limpia los dispositivos y máquinas que no vayas a utilizar durante el invierno.<br>
    <strong>Riego:</strong> Reduce el riego al mínimo, ya que las plantas necesitan menos agua en invierno.<br>
    <strong>Evitar pisar el césped:</strong> Evita pisar el césped cuando haya escarcha, ya que las hojas se pueden romper fácilmente.
  `
};

// Determinar temporada actual
function getCurrentSeason() {
  const month = new Date().getMonth() + 1;
  if (month >= 3 && month <= 5) return 'primavera';
  if (month >= 6 && month <= 8) return 'verano';
  if (month >= 9 && month <= 11) return 'otono';
  return 'invierno';
}

// Actualizar modal según temporada
function updateModalForSeason() {
  const season = getCurrentSeason();
  // Header gradient
  const header = document.getElementById('modal-header');
  if (header) header.className = `bg-gradient-to-r ${seasonStyles[season]} text-white p-6`;
  // Títulos
  const title = document.getElementById('modal-title-season');
  const subtitle = document.getElementById('modal-subtitle-season');
  if (title) title.textContent = `Ofertas de ${season.charAt(0).toUpperCase() + season.slice(1)}`;
  if (subtitle) subtitle.textContent = `Descubre las mejores ofertas de ${season} de nuestros productos.`;
  
  // Actualizar consejos
  const data = seasonData[season];
  const iconElement = document.getElementById('season-icon');
  const titleElement = document.getElementById('season-title');
  const adviceElement = document.getElementById('season-advice');
  
  if (iconElement) iconElement.textContent = data.icon;
  if (titleElement) titleElement.textContent = data.title;
  if (adviceElement) adviceElement.textContent = data.advice;
  // Inyectar productos de la temporada
  updateProductGrid(season);
}

// Función para seleccionar temporada
function selectSeason(season) {
  selectedSeason = season;
  
  // Actualizar estilos de botones
  document.querySelectorAll('.season-btn').forEach(btn => {
    btn.classList.remove('bg-white/30', 'scale-105');
    btn.classList.add('bg-white/10');
  });
  
  const selectedBtn = document.getElementById(`season-${season}`);
  if (selectedBtn) {
    selectedBtn.classList.remove('bg-white/10');
    selectedBtn.classList.add('bg-white/30', 'scale-105');
  }
  
  // Actualizar consejos
  const data = seasonData[season];
  const iconElement = document.getElementById('season-icon');
  const titleElement = document.getElementById('season-title');
  const adviceElement = document.getElementById('season-advice');
  
  if (iconElement) iconElement.textContent = data.icon;
  if (titleElement) titleElement.textContent = data.title;
  if (adviceElement) adviceElement.textContent = data.advice;
  
  // Actualizar grid de ofertas de la home
  updateHomeOffers(season);
  // Mostrar popup de cuidados de jardín
  openTipsModal(season);
}

// Función para abrir el modal
function openProductModal() {
  const modal = document.getElementById('product-modal');
  if (modal) {
    // Aplicar diseño de temporada según fecha actual
    updateModalForSeason();
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modalOpen = true;
    
    // Iniciar countdown
    startCountdown();
    
    // Prevenir scroll del body
    document.body.style.overflow = 'hidden';
  }
}

// Función para cerrar el modal
function closeProductModal() {
  const modal = document.getElementById('product-modal');
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modalOpen = false;
    
    // Detener countdown
    if (countdownInterval) {
      clearInterval(countdownInterval);
    }
    
    // Restaurar scroll del body
    document.body.style.overflow = 'auto';
  }
}

// Función para aumentar cantidad del pack
function increasePackQuantity() {
  packQuantity++;
  updatePackQuantityDisplay();
}

// Función para disminuir cantidad del pack
function decreasePackQuantity() {
  if (packQuantity > 1) {
    packQuantity--;
    updatePackQuantityDisplay();
  }
}

// Función para actualizar la visualización de cantidad y precio del pack
function updatePackQuantityDisplay() {
  const quantityElement = document.getElementById('pack-quantity');
  const finalTotalElement = document.getElementById('final-total');
  
  if (quantityElement) {
    quantityElement.textContent = packQuantity;
  }
  
  if (finalTotalElement) {
    const total = packBasePrice * packQuantity;
    finalTotalElement.textContent = `$${total.toLocaleString()} ARS`;
    
    // Actualizar ahorro
    const originalPrice = 38000 * packQuantity;
    const savings = originalPrice - total;
    const savingsPercent = Math.round((savings / originalPrice) * 100);
    
    const savingsElement = finalTotalElement.nextElementSibling;
    if (savingsElement) {
      savingsElement.textContent = `Ahorro: $${savings.toLocaleString()} (${savingsPercent}%)`;
    }
  }
}

// Función para añadir pack al carrito
function addPackToCart() {
  // Añadir cada producto del pack al carrito con las cantidades correctas
  for (let i = 0; i < packQuantity; i++) {
    // Añadir productos individuales
    addToCart('monstera-premium', 'Monstera Deliciosa Premium', 15000);
    addToCart('pothos-dorado', 'Pothos Dorado + Maceta', 5100);
    addToCart('suculenta-mix', 'Suculenta Mix (3 unidades)', 0); // Gratis en el pack
  }
  
  // Mostrar toast
  showToast(`${packQuantity} Pack${packQuantity > 1 ? 's' : ''} Premium añadido${packQuantity > 1 ? 's' : ''} al carrito`);
  
  // Cerrar modal
  closeProductModal();
}

// Función para comprar pack ahora
function buyPackNow() {
  // Añadir al carrito primero
  addPackToCart();
  
  // Simular redirección a checkout
  setTimeout(() => {
    alert(`🌿 ¡Excelente elección! Procesando compra de ${packQuantity} Pack${packQuantity > 1 ? 's' : ''} Premium por $${(packBasePrice * packQuantity).toLocaleString()} ARS.\n\n✅ Incluye:\n• Monstera Deliciosa Premium\n• Pothos Dorado + Maceta\n• Suculenta Mix (3 variedades)\n• Envío gratis\n• Soporte 24/7\n\n¡Te contactaremos pronto para coordinar la entrega!`);
    // Redirigir a checkout
    window.location.href = 'checkout.html';
  }, 500);
}

// Mantener funciones anteriores para compatibilidad
function increaseQuantity() {
  increasePackQuantity();
}

function decreaseQuantity() {
  decreasePackQuantity();
}

function addToCartFromModal() {
  addPackToCart();
}

function buyNowFromModal() {
  buyPackNow();
}

// Función para mostrar toast de confirmación
function showToast(message) {
  const toast = document.createElement('div');
  toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
  toast.innerHTML = `
    <div class="flex items-center gap-2">
      <span class="text-xl">🌿</span>
      <span>${message}</span>
    </div>
  `;
  
  document.body.appendChild(toast);
  
  // Animar entrada
  setTimeout(() => {
    toast.style.transform = 'translateX(0)';
  }, 100);
  
  // Animar salida
  setTimeout(() => {
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => {
      if (document.body.contains(toast)) {
        document.body.removeChild(toast);
      }
    }, 300);
  }, 3000);
}

// Función para iniciar countdown
function startCountdown() {
  // Countdown de 24 horas desde ahora
  const endTime = new Date().getTime() + (24 * 60 * 60 * 1000);
  
  countdownInterval = setInterval(() => {
    const now = new Date().getTime();
    const timeLeft = endTime - now;
    
    const timerElement = document.getElementById('countdown-timer');
    
    if (timeLeft > 0 && timerElement) {
      const hours = Math.floor(timeLeft / (1000 * 60 * 60));
      const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
      
      const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
      timerElement.innerHTML = `
        <div class="flex items-center gap-2">
          <span class="animate-pulse">⏰</span>
          <span>${timeString}</span>
        </div>
      `;
    } else {
      clearInterval(countdownInterval);
      if (timerElement) {
        timerElement.innerHTML = `
          <div class="flex items-center gap-2 text-gray-400">
            <span>⏰</span>
            <span>¡OFERTA TERMINADA!</span>
          </div>
        `;
      }
    }
  }, 1000);
}

// Auto-abrir modal después de 3 segundos
setTimeout(() => {
  if (!modalOpen) {
    openProductModal();
  }
}, 3000);

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  // Inicializar temporada por defecto
  const currentSeason = getCurrentSeason();
  selectSeason(currentSeason);
  
  // Cargar ofertas iniciales en la home
  updateHomeOffers(currentSeason);
  
  // Mostrar popup de cuidados inicial
  openTipsModal(currentSeason);
  
  // Configurar event listeners
  const modal = document.getElementById('product-modal');
  if (modal) {
    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeProductModal();
      }
    });
  }
  
  // Cerrar modal con tecla Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modalOpen) {
      closeProductModal();
    }
  });
  
  // Inicializar cantidad del pack
  updatePackQuantityDisplay();
});

// Función para inyectar productos dinámicamente
function updateProductGrid(season) {
  const grid = document.getElementById('season-product-grid');
  if (!grid) return;
  const prods = seasonalProducts[season] || seasonalProducts['primavera'];
  grid.innerHTML = prods.map(p => `
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative">
      <div class="h-64 bg-cover bg-center relative" style="background-image: url('${p.image}')">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        <div class="absolute bottom-4 left-4 text-white">
          <h3 class="text-xl font-bold mb-1">${p.name}</h3>
          <p class="text-sm opacity-90">${p.name}</p>
        </div>
      </div>
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <div>
            <span class="text-gray-500 line-through text-sm">$${p.oldPrice.toLocaleString()}</span>
            <span class="text-2xl font-bold text-green-600 ml-2">$${p.newPrice.toLocaleString()}</span>
          </div>
        </div>
        <div class="flex gap-4 mb-2">
          ${p.features.map(f => `<div class="flex items-center gap-1 text-xs"><span>${f.icon}</span><span>${f.value}</span></div>`).join('')}
        </div>
      </div>
    </div>
  `).join('');
}

// Funciones para el modal de sugerencias de cuidados
function openTipsModal(season) {
  const modal = document.getElementById('tips-modal');
  const title = document.getElementById('tips-modal-title');
  const content = document.getElementById('tips-modal-content');
  if (modal && content && title) {
    title.textContent = `Cuidados de ${season.charAt(0).toUpperCase() + season.slice(1)}`;
    content.innerHTML = seasonCare[season] || '';
    modal.classList.remove('hidden');
  }
}

function closeTipsModal() {
  const modal = document.getElementById('tips-modal');
  if (modal) modal.classList.add('hidden');
}

// Función para actualizar ofertas de la home según temporada
function updateHomeOffers(season) {
  const grid = document.getElementById('home-product-grid');
  if (!grid) return;
  const prods = seasonalProducts[season] || seasonalProducts['primavera'];
  grid.innerHTML = prods.map(p => `
    <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col group transform hover:shadow-xl transition-shadow duration-300">
      <div class="relative w-full h-56 sm:h-64 bg-center bg-no-repeat bg-cover" style="background-image: url('${p.image}');">
        <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition-opacity duration-300"></div>
      </div>
      <div class="p-5 sm:p-6 flex-grow">
        <h3 class="text-xl font-semibold text-[var(--text-primary)] mb-2">${p.name}</h3>
        <p class="text-[var(--text-secondary)] text-sm leading-relaxed">Disfruta de ${p.name} esta temporada.</p>
      </div>
      <div class="p-5 sm:p-6 border-t border-gray-200">
        <button class="w-full bg-primary hover:bg-[var(--accent-color)] text-white text-sm font-semibold py-2.5 px-4 rounded-lg transition-colors" data-add-cart data-product-id="${p.id}" data-product-name="${p.name}" data-product-price="${p.newPrice}">Añadir al Carrito</button>
      </div>
    </div>
  `).join('');
} 