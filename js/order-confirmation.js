// js/order-confirmation.js
// Script para cargar y mostrar los datos del pedido confirmado

document.addEventListener('DOMContentLoaded', function() {
    loadOrderData();
    generateOrderNumber();
    setOrderDate();
});

function loadOrderData() {
    try {
        const orderData = localStorage.getItem('lastOrder');
        if (!orderData) {
            // Si no hay datos de orden, redirigir al inicio
            window.location.href = 'home.html';
            return;
        }

        const order = JSON.parse(orderData);
        populateCustomerInfo(order.customer);
        populateShippingInfo(order.shipping);
        populateOrderItems(order.items);
        populateOrderTotals(order.totals);
        
        // Limpiar datos de orden después de mostrarlos
        setTimeout(() => {
            localStorage.removeItem('lastOrder');
        }, 5000);
        
    } catch (error) {
        console.error('Error loading order data:', error);
        // Si hay error, redirigir al inicio
        setTimeout(() => {
            window.location.href = 'home.html';
        }, 3000);
    }
}

function populateCustomerInfo(customer) {
    document.getElementById('customer-name').textContent = `${customer.firstName} ${customer.lastName}`;
    document.getElementById('customer-email').textContent = customer.email;
    document.getElementById('customer-phone').textContent = customer.phone;
}

function populateShippingInfo(shipping) {
    document.getElementById('shipping-address').textContent = shipping.address;
    document.getElementById('shipping-city').textContent = shipping.city;
    document.getElementById('shipping-province').textContent = shipping.province;
    document.getElementById('shipping-postal').textContent = shipping.postalCode || 'N/A';
}

function populateOrderItems(items) {
    const container = document.getElementById('order-items');
    
    container.innerHTML = items.map(item => {
        const product = getProductById(item.id);
        const imageSrc = product ? product.imageSrc : 'https://via.placeholder.com/80x80?text=Planta';
        const subtotal = (Number(item.price) * Number(item.quantity)).toFixed(0);
        
        return `
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                <img src="${imageSrc}" alt="${item.name}" class="w-20 h-20 object-cover rounded-lg">
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">${item.name}</h4>
                    <p class="text-gray-600">Precio unitario: $${Number(item.price).toLocaleString()}</p>
                    <p class="text-gray-600">Cantidad: ${item.quantity}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-primary">$${Number(subtotal).toLocaleString()}</p>
                </div>
            </div>
        `;
    }).join('');
}

function populateOrderTotals(totals) {
    document.getElementById('order-subtotal').textContent = `$${totals.subtotal.toLocaleString()}`;
    document.getElementById('order-total').textContent = `$${totals.total.toLocaleString()}`;
    
    // Mostrar descuento si existe
    if (totals.discount && totals.discount > 0) {
        const discountRow = document.getElementById('order-discount-row');
        const discountAmount = document.getElementById('order-discount');
        
        discountRow.style.display = 'flex';
        discountAmount.textContent = `-$${totals.discount.toLocaleString()}`;
    }
}

function generateOrderNumber() {
    // Generar número de orden único basado en timestamp
    const timestamp = Date.now();
    const orderNumber = `#LOS-${new Date().getFullYear()}-${String(timestamp).slice(-6)}`;
    document.getElementById('order-number').textContent = orderNumber;
}

function setOrderDate() {
    const now = new Date();
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        timeZone: 'America/Argentina/Mendoza'
    };
    
    const formattedDate = now.toLocaleDateString('es-AR', options);
    document.getElementById('order-date').textContent = formattedDate;
}

// Función para compartir el pedido (opcional)
function shareOrder() {
    const orderNumber = document.getElementById('order-number').textContent;
    const total = document.getElementById('order-total').textContent;
    
    const shareText = `🌿 ¡Acabo de hacer un pedido en Los Cocos! Orden ${orderNumber} por ${total}. ¡Espero mis nuevas plantas! 🌱`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Mi pedido en Los Cocos',
            text: shareText,
            url: window.location.href
        });
    } else {
        // Fallback para dispositivos que no soportan Web Share API
        navigator.clipboard.writeText(shareText).then(() => {
            showToast('¡Texto copiado al portapapeles!', 'success');
        });
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
        <div class="flex items-center gap-2">
            <span class="text-xl">${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
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