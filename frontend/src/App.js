import React, { useState, useEffect } from 'react';
import './App.css';

const API_URL = process.env.REACT_APP_BACKEND_URL || 'http://localhost:8001';

function App() {
  const [plants, setPlants] = useState([]);
  const [dailyOffers, setDailyOffers] = useState([]);
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [cart, setCart] = useState([]);
  const [showCart, setShowCart] = useState(false);
  const [showCheckout, setShowCheckout] = useState(false);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchPlants();
    fetchDailyOffers();
    fetchCategories();
  }, []);

  const fetchPlants = async () => {
    try {
      const response = await fetch(`${API_URL}/api/plants`);
      const data = await response.json();
      setPlants(data);
      setLoading(false);
    } catch (error) {
      console.error('Error fetching plants:', error);
      setLoading(false);
    }
  };

  const fetchDailyOffers = async () => {
    try {
      const response = await fetch(`${API_URL}/api/plants/daily-offers`);
      const data = await response.json();
      setDailyOffers(data);
    } catch (error) {
      console.error('Error fetching daily offers:', error);
    }
  };

  const fetchCategories = async () => {
    try {
      const response = await fetch(`${API_URL}/api/categories`);
      const data = await response.json();
      setCategories(data);
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  };

  const addToCart = (plant) => {
    const existingItem = cart.find(item => item.id === plant.id);
    if (existingItem) {
      setCart(cart.map(item => 
        item.id === plant.id 
          ? { ...item, quantity: item.quantity + 1 }
          : item
      ));
    } else {
      setCart([...cart, { ...plant, quantity: 1 }]);
    }
  };

  const removeFromCart = (plantId) => {
    setCart(cart.filter(item => item.id !== plantId));
  };

  const updateQuantity = (plantId, quantity) => {
    if (quantity === 0) {
      removeFromCart(plantId);
    } else {
      setCart(cart.map(item => 
        item.id === plantId 
          ? { ...item, quantity: quantity }
          : item
      ));
    }
  };

  const getCartTotal = () => {
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
  };

  const getCartItemCount = () => {
    return cart.reduce((total, item) => total + item.quantity, 0);
  };

  const filteredPlants = selectedCategory === 'all' 
    ? plants 
    : plants.filter(plant => plant.category === selectedCategory);

  const CheckoutForm = () => {
    const [formData, setFormData] = useState({
      customer_name: '',
      customer_email: '',
      customer_phone: ''
    });

    const handleSubmit = async (e) => {
      e.preventDefault();
      
      const orderData = {
        ...formData,
        items: cart.map(item => ({
          plant_id: item.id,
          quantity: item.quantity
        })),
        total: getCartTotal()
      };

      try {
        const response = await fetch(`${API_URL}/api/orders`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(orderData)
        });

        if (response.ok) {
          alert('¡Pedido realizado con éxito! Te contactaremos pronto.');
          setCart([]);
          setShowCheckout(false);
          setShowCart(false);
        } else {
          alert('Error al procesar el pedido. Inténtalo de nuevo.');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar el pedido. Inténtalo de nuevo.');
      }
    };

    return (
      <div className="checkout-form">
        <h3 className="text-2xl font-bold mb-4">Finalizar Pedido</h3>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-1">Nombre completo</label>
            <input
              type="text"
              required
              value={formData.customer_name}
              onChange={(e) => setFormData({...formData, customer_name: e.target.value})}
              className="w-full p-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">Email</label>
            <input
              type="email"
              required
              value={formData.customer_email}
              onChange={(e) => setFormData({...formData, customer_email: e.target.value})}
              className="w-full p-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">Teléfono</label>
            <input
              type="tel"
              required
              value={formData.customer_phone}
              onChange={(e) => setFormData({...formData, customer_phone: e.target.value})}
              className="w-full p-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div className="border-t pt-4">
            <div className="flex justify-between items-center mb-4">
              <span className="text-lg font-semibold">Total: ${getCartTotal().toFixed(2)}</span>
            </div>
            <div className="flex gap-2">
              <button
                type="button"
                onClick={() => setShowCheckout(false)}
                className="flex-1 bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600"
              >
                Cancelar
              </button>
              <button
                type="submit"
                className="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700"
              >
                Confirmar Pedido
              </button>
            </div>
          </div>
        </form>
      </div>
    );
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-green-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-green-500 mx-auto"></div>
          <p className="mt-4 text-green-600 font-semibold">Cargand plantas...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-green-50">
      {/* Header */}
      <header className="bg-green-600 text-white shadow-lg">
        <div className="container mx-auto px-4 py-4">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-3xl font-bold">🌱 Vivero Los Cocos</h1>
              <p className="text-green-100">Las mejores plantas para tu hogar</p>
            </div>
            <button
              onClick={() => setShowCart(!showCart)}
              className="bg-green-700 px-4 py-2 rounded-lg hover:bg-green-800 flex items-center gap-2"
            >
              🛒 Carrito ({getCartItemCount()})
            </button>
          </div>
        </div>
      </header>

      {/* Daily Offers Section */}
      {dailyOffers.length > 0 && (
        <section className="bg-yellow-100 border-b-4 border-yellow-400 py-6">
          <div className="container mx-auto px-4">
            <h2 className="text-2xl font-bold text-yellow-800 mb-4 text-center">
              🔥 ¡OFERTAS DEL DÍA! 🔥
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {dailyOffers.map(plant => (
                <div key={plant.id} className="bg-white rounded-lg shadow-md p-4 border-2 border-yellow-400">
                  <div className="relative">
                    <img
                      src={plant.image_url}
                      alt={plant.name}
                      className="w-full h-48 object-cover rounded-lg mb-3"
                    />
                    <div className="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-full text-sm font-bold">
                      ¡OFERTA!
                    </div>
                  </div>
                  <h3 className="font-bold text-lg mb-2">{plant.name}</h3>
                  <p className="text-gray-600 text-sm mb-3">{plant.description}</p>
                  <div className="flex justify-between items-center">
                    <div>
                      <span className="text-2xl font-bold text-green-600">${plant.price}</span>
                      {plant.original_price && (
                        <span className="text-gray-500 line-through ml-2">${plant.original_price}</span>
                      )}
                    </div>
                    <button
                      onClick={() => addToCart(plant)}
                      className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                    >
                      Agregar
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Category Filter */}
      <div className="container mx-auto px-4 py-6">
        <div className="flex flex-wrap gap-2 justify-center mb-6">
          <button
            onClick={() => setSelectedCategory('all')}
            className={`px-4 py-2 rounded-lg ${
              selectedCategory === 'all' 
                ? 'bg-green-600 text-white' 
                : 'bg-white text-green-600 border border-green-600'
            }`}
          >
            Todas las Plantas
          </button>
          {categories.map(category => (
            <button
              key={category}
              onClick={() => setSelectedCategory(category)}
              className={`px-4 py-2 rounded-lg ${
                selectedCategory === category 
                  ? 'bg-green-600 text-white' 
                  : 'bg-white text-green-600 border border-green-600'
              }`}
            >
              {category}
            </button>
          ))}
        </div>

        {/* Plants Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {filteredPlants.map(plant => (
            <div key={plant.id} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
              <img
                src={plant.image_url}
                alt={plant.name}
                className="w-full h-48 object-cover"
              />
              <div className="p-4">
                <h3 className="font-bold text-lg mb-2">{plant.name}</h3>
                <p className="text-gray-600 text-sm mb-3">{plant.description}</p>
                <div className="flex justify-between items-center">
                  <span className="text-xl font-bold text-green-600">${plant.price}</span>
                  <button
                    onClick={() => addToCart(plant)}
                    className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                  >
                    Agregar
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Cart Sidebar */}
      {showCart && (
        <div className="fixed inset-0 bg-black bg-opacity-50 z-50">
          <div className="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-lg">
            <div className="p-4 border-b">
              <div className="flex justify-between items-center">
                <h2 className="text-xl font-bold">Carrito de Compras</h2>
                <button
                  onClick={() => setShowCart(false)}
                  className="text-gray-500 hover:text-gray-700"
                >
                  ✕
                </button>
              </div>
            </div>
            
            <div className="p-4 flex-1 overflow-y-auto" style={{maxHeight: 'calc(100vh - 200px)'}}>
              {cart.length === 0 ? (
                <p className="text-gray-500 text-center py-8">Tu carrito está vacío</p>
              ) : (
                <>
                  {showCheckout ? (
                    <CheckoutForm />
                  ) : (
                    <>
                      <div className="space-y-4">
                        {cart.map(item => (
                          <div key={item.id} className="flex items-center gap-3 bg-gray-50 p-3 rounded-lg">
                            <img
                              src={item.image_url}
                              alt={item.name}
                              className="w-16 h-16 object-cover rounded"
                            />
                            <div className="flex-1">
                              <h4 className="font-semibold">{item.name}</h4>
                              <p className="text-green-600 font-bold">${item.price}</p>
                              <div className="flex items-center gap-2 mt-1">
                                <button
                                  onClick={() => updateQuantity(item.id, item.quantity - 1)}
                                  className="bg-gray-200 px-2 py-1 rounded"
                                >
                                  -
                                </button>
                                <span>{item.quantity}</span>
                                <button
                                  onClick={() => updateQuantity(item.id, item.quantity + 1)}
                                  className="bg-gray-200 px-2 py-1 rounded"
                                >
                                  +
                                </button>
                                <button
                                  onClick={() => removeFromCart(item.id)}
                                  className="text-red-500 ml-2"
                                >
                                  🗑️
                                </button>
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>
                      
                      <div className="border-t pt-4 mt-4">
                        <div className="flex justify-between items-center mb-4">
                          <span className="text-lg font-semibold">Total: ${getCartTotal().toFixed(2)}</span>
                        </div>
                        <button
                          onClick={() => setShowCheckout(true)}
                          className="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold"
                        >
                          Proceder al Checkout
                        </button>
                      </div>
                    </>
                  )}
                </>
              )}
            </div>
          </div>
        </div>
      )}

      {/* Footer */}
      <footer className="bg-green-800 text-white py-8 mt-12">
        <div className="container mx-auto px-4 text-center">
          <h3 className="text-2xl font-bold mb-2">🌱 Vivero Los Cocos</h3>
          <p className="text-green-200">Las mejores plantas para transformar tu hogar</p>
          <p className="text-green-200 mt-2">Contacto: info@viveroloscocos.com | Tel: (555) 123-4567</p>
        </div>
      </footer>
    </div>
  );
}

export default App;