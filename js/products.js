// js/products.js

// Globally accessible array of product objects.
// This serves as the central data source for product information across the site.
// Other scripts like modal.js, single-product.js, and cart-page.js consume this data.
const products = [
  {
    id: 'p1', // Changed to string ID
    name: "Suculenta Echeveria",
    imageSrc: "img/productos/suculenta_1.jpg",
    oldPrice: "$150",
    newPrice: "$100",
    description: "La Echeveria es una suculenta popular conocida por sus hermosas rosetas y su fácil cuidado. Ideal para principiantes, añade un toque decorativo a cualquier espacio interior o exterior con suficiente luz.",
    category: "ofertas",
    features: ["Fácil cuidado", "Ideal para principiantes", "Decorativa"],
    careTips: [
      "Luz: Requiere luz solar brillante e indirecta. Al menos 4-6 horas diarias.",
      "Riego: Riega abundantemente cuando el sustrato esté completamente seco. Evita el exceso de agua.",
      "Sustrato: Utiliza una mezcla bien drenada específica para cactus y suculentas.",
      "Temperatura: Prefiere temperaturas moderadas (18-24°C)."
    ],
    offerDetails: {
      title: "¡Echeveria en Oferta Especial!",
      description: "Llévate esta hermosa Suculenta Echeveria con un 33% de descuento por tiempo limitado. Perfecta para iniciar tu colección.",
      imageSrc: "img/ofertas/echeveria_oferta.jpg" // Example offer image
    }
  },
  {
    id: 'p2', // Changed to string ID
    name: "Cactus Globoso",
    imageSrc: "img/productos/cactus_1.jpg",
    oldPrice: "$200",
    newPrice: "$180",
    description: "Este cactus globoso, probablemente un Echinocactus grusonii joven, es una pieza central impresionante. Con sus espinas doradas y forma esférica, es resistente y de bajo mantenimiento.",
    category: "ofertas",
    features: ["Resistente", "Poco riego", "Forma única"],
    careTips: [
      "Luz: Pleno sol es ideal.",
      "Riego: Muy poco frecuente, especialmente en invierno. Deja que el sustrato se seque completamente.",
      "Sustrato: Drenaje excelente es crucial. Arena gruesa y perlita son buenos componentes.",
      "Temperatura: Tolera bien el calor, proteger de heladas."
    ],
    offerDetails: null // No specific offer for this one on single page
  },
  {
    id: 'p3', // Changed to string ID
    name: "Orquídea Phalaenopsis",
    imageSrc: "img/productos/flor_1.jpg",
    newPrice: "$350",
    description: "La Orquídea Phalaenopsis, u orquídea mariposa, es una de las más fáciles de cuidar en interiores. Sus elegantes flores pueden durar meses, aportando un toque sofisticado.",
    category: "destacados",
    features: ["Floración duradera", "Elegante", "Requiere cuidados específicos"],
    careTips: [
      "Luz: Luz indirecta brillante. Evitar sol directo.",
      "Riego: Regar cuando las raíces se vean plateadas y el sustrato esté casi seco. No encharcar.",
      "Humedad: Prefiere alta humedad. Considera un humidificador o bandeja con guijarros.",
      "Sustrato: Corteza especial para orquídeas."
    ],
    offerDetails: {
      title: "Belleza Exótica: Phalaenopsis",
      description: "Adorna tu hogar con la elegancia de una Orquídea Phalaenopsis. Disponible en varios colores.",
      imageSrc: "img/ofertas/phalaenopsis_detalle.jpg" // Example offer image
    }
  },
  {
    id: 'p4', // Changed to string ID
    name: "Helecho Boston",
    imageSrc: "img/productos/helecho_1.jpg",
    newPrice: "$250",
    description: "El Helecho Boston (Nephrolepis exaltata) es conocido por sus frondas arqueadas y plumosas que crean un ambiente fresco y natural. Es un excelente purificador de aire.",
    category: "destacados",
    features: ["Purificador de aire", "Ambientes húmedos", "Follaje denso"],
    careTips: [
      "Luz: Luz indirecta media a brillante. Evitar sol directo.",
      "Riego: Mantener el sustrato consistentemente húmedo, pero no empapado.",
      "Humedad: Requiere alta humedad. Pulverizar regularmente.",
      "Temperatura: Prefiere temperaturas frescas a moderadas (15-24°C)."
    ],
    offerDetails: null
  },
  {
    id: 'p5', // Changed to string ID
    name: "Rosal Miniatura",
    imageSrc: "img/productos/rosal_1.jpg",
    newPrice: "$220",
    description: "Los Rosales Miniatura ofrecen la belleza clásica de las rosas en un formato compacto, ideal para macetas en balcones, terrazas o interiores luminosos.",
    category: "flores",
    features: ["Flores perfumadas", "Compacto", "Ideal para macetas"],
    careTips: [
        "Luz: Pleno sol, al menos 6 horas diarias.",
        "Riego: Regular, manteniendo el sustrato húmedo pero bien drenado.",
        "Abono: Fertilizar regularmente durante la temporada de crecimiento.",
        "Poda: Podar para mantener la forma y estimular la floración."
    ],
    offerDetails: null
  },
  {
    id: 'p6', // Changed to string ID
    name: "Aloe Vera",
    imageSrc: "img/productos/aloe_1.jpg",
    newPrice: "$120",
    description: "El Aloe Vera no solo es una planta suculenta fácil de cuidar, sino que también es conocida por las propiedades medicinales del gel de sus hojas.",
    category: "suculentas",
    features: ["Propiedades medicinales", "Fácil cuidado", "Resistente"],
    careTips: [
        "Luz: Luz solar directa o indirecta brillante.",
        "Riego: Dejar secar el sustrato completamente entre riegos.",
        "Sustrato: Mezcla para cactus y suculentas con buen drenaje.",
        "Temperatura: No tolera las heladas."
    ],
    offerDetails: null
  },
  // Seasonal products from modal.js - example for Spring
  // For now, these won't have detailed single-page views unless their IDs are structured and they get care tips etc.
  {
    id: 'spring_1',
    name: "Tulipanes Holandeses",
    imageSrc: "img/productos/tulipanes.jpg",
    oldPrice: "$180",
    newPrice: "$150",
    description: "Coloridos tulipanes para alegrar tu primavera.",
    category: "ofertas",
    features: ["Variedad de colores", "Frescura primaveral", "Ideal para arreglos"],
    careTips: ["Luz: Sol directo.", "Riego: Moderado mientras florecen.", "Sustrato: Bien drenado."],
    offerDetails: null
  },
  {
    id: 'spring_2',
    name: "Jacintos Perfumados",
    imageSrc: "img/productos/jacintos.jpg",
    newPrice: "$160",
    description: "Jacintos con fragancia embriagadora, anuncian la primavera.",
    category: "destacados",
    features: ["Fragancia intensa", "Bulbos de temporada", "Colores vibrantes"],
    careTips: ["Luz: Sol o media sombra.", "Riego: Mantener húmedo.", "Sustrato: Rico y bien drenado."],
    offerDetails: null
  },
  {
    id: 'summer_1',
    name: "Girasol Gigante",
    imageSrc: "img/productos/girasol.jpg",
    oldPrice: "$120",
    newPrice: "$90",
    description: "Impresionantes girasoles para el verano.",
    category: "ofertas",
    features: ["Gran tamaño", "Alegre", "Atrae polinizadores"],
    careTips: ["Luz: Pleno sol.", "Riego: Regular, especialmente en crecimiento.", "Sustrato: Fértil."],
    offerDetails: null
  },
  {
    id: 'autumn_1',
    name: "Crisantemos Otoñales",
    imageSrc: "img/productos/crisantemos.jpg",
    newPrice: "$130",
    description: "Crisantemos en tonos cálidos para el otoño.",
    category: "ofertas",
    features: ["Colores otoñales", "Floración tardía", "Resistente"],
    careTips: ["Luz: Sol pleno.", "Riego: Moderado.", "Sustrato: Bien drenado."],
    offerDetails: null
  },
  {
    id: 'winter_1',
    name: "Poinsettia (Flor de Pascua)",
    imageSrc: "img/productos/poinsettia.jpg",
    newPrice: "$170",
    description: "La tradicional Flor de Pascua para decorar en invierno.",
    category: "ofertas",
    features: ["Tradicional navideña", "Color rojo intenso", "Cuidado en interiores"],
    careTips: ["Luz: Indirecta brillante.", "Riego: Dejar secar capa superior.", "Sustrato: Ligero."],
    offerDetails: null
  }
];

// Globally accessible function to retrieve a product by its ID.
// Used by various scripts to fetch specific product details.
// @param {string|number} id - The ID of the product to find.
// @returns {object|undefined} The product object if found, otherwise undefined.
function getProductById(id) {
  // Normalize ID to string for comparison, as product IDs are now strings like 'p1', 'spring_1'
  const stringId = String(id);
  return products.find(product => String(product.id) === stringId);
}
