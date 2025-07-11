// js/checkout.js
// Funcionalidad completa del checkout con UX optimizada

document.addEventListener('DOMContentLoaded', function() {
    // Verificar si hay items en el carrito
    const cart = CartAPI.getCart();
    if (!cart || cart.length === 0) {
        // Redirigir al carrito si está vacío
        window.location.href = 'cart.html';
        return;
    }

    // Inicializar checkout
    initializeCheckout();
    setupFormValidation();
    setupPaymentMethods();
    setupCouponSystem();
    loadCartItems();
    updateTotals();
});

// Variables globales
let currentDiscount = 0;
let currentCoupon = null;

// Códigos de cupón válidos
const validCoupons = {
    'BIENVENIDO10': { discount: 10, type: 'percentage', description: '10% de descuento' },
    'PLANTAS20': { discount: 20, type: 'percentage', description: '20% de descuento' },
    'SPRING2024': { discount: 15, type: 'percentage', description: '15% descuento primavera' },
    'ENVIOGRATIS': { discount: 0, type: 'shipping', description: 'Envío gratis' }
};

function initializeCheckout() {
    // Configurar números de tarjeta de crédito
    const cardNumberInput = document.getElementById('cardNumber');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', formatCardNumber);
    }

    // Configurar fecha de vencimiento
    const cardExpiryInput = document.getElementById('cardExpiry');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', formatCardExpiry);
    }

    // Configurar CVC
    const cardCVCInput = document.getElementById('cardCVC');
    if (cardCVCInput) {
        cardCVCInput.addEventListener('input', formatCVC);
    }

    // Configurar teléfono
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', formatPhone);
    }
}

function setupFormValidation() {
    const form = document.getElementById('checkout-form');
    if (!form) return;

    form.addEventListener('submit', handleFormSubmit);

    // Validación en tiempo real
    const requiredFields = form.querySelectorAll('input[required], select[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', validateField);
        field.addEventListener('input', clearFieldError);
    });
}

function setupPaymentMethods() {
    const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
    paymentMethods.forEach(method => {
        method.addEventListener('change', handlePaymentMethodChange);
    });
}

function setupCouponSystem() {
    const applyCouponBtn = document.getElementById('applyCoupon');
    const couponInput = document.getElementById('couponCode');
    
    if (applyCouponBtn && couponInput) {
        applyCouponBtn.addEventListener('click', applyCoupon);
        couponInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyCoupon();
            }
        });
    }
}

function loadCartItems() {
    const container = document.getElementById('checkout-items');
    if (!container) return;

    const cart = CartAPI.getCart();
    
    container.innerHTML = cart.map(item => {
        const product = getProductById(item.id);
        const imageSrc = product ? product.imageSrc : 'https://via.placeholder.com/80x80?text=Planta';
        const subtotal = (Number(item.price) * Number(item.quantity)).toFixed(0);
        
        return `
            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <img src="${imageSrc}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900">${item.name}</h4>
                    <p class="text-sm text-gray-600">Cantidad: ${item.quantity}</p>
                    <p class="text-sm font-semibold text-primary">$${Number(subtotal).toLocaleString()}</p>
                </div>
            </div>
        `;
    }).join('');
}

function updateTotals() {
    const cart = CartAPI.getCart();
    const subtotal = cart.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity)), 0);
    
    // Calcular descuento
    let discountAmount = 0;
    if (currentCoupon) {
        if (currentCoupon.type === 'percentage') {
            discountAmount = subtotal * (currentCoupon.discount / 100);
        } else if (currentCoupon.type === 'fixed') {
            discountAmount = currentCoupon.discount;
        }
    }
    
    const total = subtotal - discountAmount;
    
    // Actualizar UI
    document.getElementById('checkout-subtotal').textContent = `$${subtotal.toLocaleString()}`;
    document.getElementById('checkout-total').textContent = `$${total.toLocaleString()}`;
    
    // Mostrar/ocultar descuento
    const discountRow = document.getElementById('discount-row');
    if (discountAmount > 0) {
        discountRow.style.display = 'flex';
        document.getElementById('checkout-discount').textContent = `-$${discountAmount.toLocaleString()}`;
    } else {
        discountRow.style.display = 'none';
    }
}

function applyCoupon() {
    const couponInput = document.getElementById('couponCode');
    const applyCouponBtn = document.getElementById('applyCoupon');
    const couponCode = couponInput.value.trim().toUpperCase();
    
    if (!couponCode) {
        showToast('Por favor ingresa un código de cupón', 'error');
        return;
    }
    
    if (validCoupons[couponCode]) {
        currentCoupon = validCoupons[couponCode];
        currentDiscount = currentCoupon.discount;
        
        // Actualizar UI del cupón
        applyCouponBtn.textContent = '✓ Aplicado';
        applyCouponBtn.className = 'px-4 py-2 bg-green-500 text-white rounded-lg font-medium';
        couponInput.disabled = true;
        
        updateTotals();
        showToast(`¡Cupón aplicado! ${currentCoupon.description}`, 'success');
    } else {
        showToast('Código de cupón inválido', 'error');
        couponInput.focus();
    }
}

function handlePaymentMethodChange(e) {
    const cardDetails = document.getElementById('card-details');
    if (e.target.value === 'card') {
        cardDetails.style.display = 'grid';
        // Hacer campos de tarjeta requeridos
        cardDetails.querySelectorAll('input').forEach(input => {
            input.required = true;
        });
    } else {
        cardDetails.style.display = 'none';
        // Quitar requerimiento de campos de tarjeta
        cardDetails.querySelectorAll('input').forEach(input => {
            input.required = false;
        });
    }
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        showToast('Por favor corrige los errores en el formulario', 'error');
        return;
    }
    
    // Mostrar loading
    const submitBtn = document.getElementById('complete-purchase');
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = `
        <div class="flex items-center justify-center gap-2">
            <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div>
            <span>Procesando...</span>
        </div>
    `;
    submitBtn.disabled = true;
    
    // Simular procesamiento
    setTimeout(() => {
        processOrder();
    }, 2000);
}

function processOrder() {
    const formData = new FormData(document.getElementById('checkout-form'));
    const orderData = {
        items: CartAPI.getCart(),
        customer: {
            firstName: formData.get('firstName'),
            lastName: formData.get('lastName'),
            email: formData.get('email'),
            phone: formData.get('phone')
        },
        shipping: {
            address: formData.get('address'),
            city: formData.get('city'),
            postalCode: formData.get('postalCode'),
            province: formData.get('province'),
            notes: formData.get('notes')
        },
        payment: {
            method: formData.get('paymentMethod')
        },
        totals: {
            subtotal: CartAPI.getCartTotal(),
            discount: currentDiscount,
            total: CartAPI.getCartTotal() - currentDiscount
        }
    };
    
    // Guardar orden en localStorage
    localStorage.setItem('lastOrder', JSON.stringify(orderData));
    
    // Limpiar carrito
    localStorage.removeItem('vivero_cart_items');
    
    // Redirigir a confirmación
    window.location.href = 'order-confirmation.html';
}

function validateForm() {
    const form = document.getElementById('checkout-form');
    const requiredFields = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!validateField({ target: field })) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Validación requerido
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Este campo es requerido';
    }
    
    // Validaciones específicas
    if (value && isValid) {
        switch (field.type) {
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Email inválido';
                }
                break;
                
            case 'tel':
                const phoneRegex = /^\(\d{3}\)\s\d{3}-\d{4}$/;
                if (!phoneRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Formato: (261) 123-4567';
                }
                break;
        }
        
        // Validaciones por ID
        switch (field.id) {
            case 'cardNumber':
                if (field.required && value.replace(/\s/g, '').length < 16) {
                    isValid = false;
                    errorMessage = 'Número de tarjeta inválido';
                }
                break;
                
            case 'cardExpiry':
                if (field.required) {
                    const expiryRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
                    if (!expiryRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Formato: MM/AA';
                    }
                }
                break;
                
            case 'cardCVC':
                if (field.required && (value.length < 3 || value.length > 4)) {
                    isValid = false;
                    errorMessage = 'CVC inválido (3-4 dígitos)';
                }
                break;
        }
    }
    
    // Mostrar/ocultar error
    showFieldError(field, isValid ? null : errorMessage);
    
    return isValid;
}

function showFieldError(field, message) {
    // Remover error anterior
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    if (message) {
        // Agregar clase de error
        field.classList.add('border-red-500');
        
        // Crear mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-red-500 text-xs mt-1';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    } else {
        // Remover clase de error
        field.classList.remove('border-red-500');
    }
}

function clearFieldError(e) {
    const field = e.target;
    field.classList.remove('border-red-500');
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
}

// Funciones de formateo
function formatCardNumber(e) {
    let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
    const formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
    e.target.value = formattedValue;
}

function formatCardExpiry(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
}

function formatCVC(e) {
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
}

function formatPhone(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 10) {
        value = `(${value.substring(0, 3)}) ${value.substring(3, 6)}-${value.substring(6, 10)}`;
    } else if (value.length >= 6) {
        value = `(${value.substring(0, 3)}) ${value.substring(3, 6)}-${value.substring(6)}`;
    } else if (value.length >= 3) {
        value = `(${value.substring(0, 3)}) ${value.substring(3)}`;
    }
    e.target.value = value;
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
    }, 4000);
}