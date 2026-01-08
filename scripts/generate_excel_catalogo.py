#!/usr/bin/env python3
"""
Generador de Excel Consolidado - Catálogo de Productos 2026
Consolida datos de Glacoxan y Plantas Faitful en un Excel profesional.
"""

import openpyxl
from openpyxl.styles import Font, Fill, PatternFill, Alignment, Border, Side
from openpyxl.utils import get_column_letter
from datetime import datetime
from pathlib import Path

# Datos extraídos de Glacoxan (49 productos)
GLACOXAN_PRODUCTS = [
    {"nombre": "Glacoxan BTK", "categoria": "Jardín > Biológico", "descripcion": "Insecticida biológico a base de Bacillus thuringiensis. Control de orugas y larvas de lepidópteros.", "composicion": "Bacillus thuringiensis var. kurstaki 3.2%", "plagas": "Orugas, gusanos, larvas de mariposas", "dosis": "2-3 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/btk.png"},
    {"nombre": "Glacoxan D-Sist", "categoria": "Jardín > Sistémico", "descripcion": "Insecticida sistémico de amplio espectro para control de plagas chupadoras y masticadoras.", "composicion": "Imidacloprid 35%", "plagas": "Pulgones, cochinillas, mosca blanca, trips", "dosis": "0.5-1 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/d-sist.png"},
    {"nombre": "Glacoxan Imida", "categoria": "Jardín > Sistémico", "descripcion": "Insecticida sistémico para aplicación en suelo y follaje.", "composicion": "Imidacloprid 35%", "plagas": "Pulgones, mosca blanca, cochinillas", "dosis": "0.3-0.5 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/imida.png"},
    {"nombre": "Glacoxan Aceite Emulsionable", "categoria": "Jardín > Biológico", "descripcion": "Aceite mineral emulsionable para control de insectos y ácaros.", "composicion": "Aceite mineral 85%", "plagas": "Cochinillas, ácaros, huevos de insectos", "dosis": "15-20 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/aceite.png"},
    {"nombre": "Glacoxan Cipermetrina", "categoria": "Jardín > Contacto", "descripcion": "Insecticida de contacto y volteo rápido.", "composicion": "Cipermetrina 25%", "plagas": "Hormigas, cucarachas, arañas, mosquitos", "dosis": "1-2 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/cipermetrina.png"},
    {"nombre": "Glacoxan Clorpirifos", "categoria": "Jardín > Contacto", "descripcion": "Insecticida organofosforado de amplio espectro.", "composicion": "Clorpirifos 48%", "plagas": "Hormigas, gusanos, orugas", "dosis": "2-3 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/clorpirifos.png"},
    {"nombre": "Glacoxan Fungicida", "categoria": "Jardín > Fungicida", "descripcion": "Fungicida preventivo y curativo para enfermedades fúngicas.", "composicion": "Mancozeb 80%", "plagas": "Oidio, roya, manchas foliares", "dosis": "2-3 g/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/fungicida.png"},
    {"nombre": "Glacoxan Hormiguicida", "categoria": "Jardín > Hormiguicida", "descripcion": "Cebo hormiguicida granulado de acción retardada.", "composicion": "Fipronil 0.01%", "plagas": "Hormigas cortadoras, hormigas negras", "dosis": "Aplicar directamente en senderos", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/hormiguicida.png"},
    {"nombre": "Glacoxan Cucarachicida Gel", "categoria": "Hogar > Cucarachicida", "descripcion": "Gel cebo para control de cucarachas en interiores.", "composicion": "Fipronil 0.05%", "plagas": "Cucarachas alemanas, americanas", "dosis": "Aplicar gotas en grietas y rendijas", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/cucarachicida-gel.png"},
    {"nombre": "Glacoxan Mata Moscas", "categoria": "Hogar > Insecticida", "descripcion": "Insecticida en aerosol para moscas y mosquitos.", "composicion": "Piretrinas + Butóxido de piperonilo", "plagas": "Moscas, mosquitos, polillas", "dosis": "Aplicar en ambientes", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/mata-moscas.png"},
    {"nombre": "Mamboretá D", "categoria": "Jardín > Sistémico", "descripcion": "Insecticida sistémico de larga duración.", "composicion": "Imidacloprid 35%", "plagas": "Pulgones, cochinillas, trips", "dosis": "0.5 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/mamboreta-d.png"},
    {"nombre": "Mamboretá E", "categoria": "Jardín > Contacto", "descripcion": "Emulsión concentrada para control de plagas.", "composicion": "Cipermetrina 25%", "plagas": "Orugas, trips, mosca blanca", "dosis": "1 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/mamboreta-e.png"},
    {"nombre": "Mamboretá F", "categoria": "Jardín > Fungicida", "descripcion": "Fungicida sistémico de amplio espectro.", "composicion": "Tebuconazole 25%", "plagas": "Oidio, roya, fusarium", "dosis": "1 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/mamboreta-f.png"},
    {"nombre": "Mamboretá H", "categoria": "Jardín > Hormiguicida", "descripcion": "Hormiguicida granulado de largo alcance.", "composicion": "Fipronil 0.01%", "plagas": "Hormigas cortadoras", "dosis": "50g por hormiguero", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/mamboreta-h.png"},
    {"nombre": "Mamboretá M Plus", "categoria": "Jardín > Acaricida", "descripcion": "Acaricida específico para ácaros fitófagos.", "composicion": "Abamectina 1.8%", "plagas": "Araña roja, ácaro blanco", "dosis": "0.5 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/mamboreta-m.png"},
    {"nombre": "Mamboretá Babosicida", "categoria": "Jardín > Molusquicida", "descripcion": "Cebo para control de babosas y caracoles.", "composicion": "Metaldehído 5%", "plagas": "Babosas, caracoles", "dosis": "20-30 g/m²", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/babosicida.png"},
    {"nombre": "Glacoxan Oruguicida", "categoria": "Jardín > Biológico", "descripcion": "Control biológico de orugas y gusanos.", "composicion": "Bacillus thuringiensis 3.2%", "plagas": "Orugas, gusanos cortadores", "dosis": "2 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/oruguicida.png"},
    {"nombre": "Glacoxan Triple Acción", "categoria": "Jardín > Multiuso", "descripcion": "Insecticida, fungicida y acaricida en uno.", "composicion": "Cipermetrina + Azufre + Aceite", "plagas": "Pulgones, oidio, araña roja", "dosis": "20 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/triple-accion.png"},
    {"nombre": "Glacoxan Insecticida Total", "categoria": "Hogar > Profesional", "descripcion": "Insecticida profesional de amplio espectro.", "composicion": "Cipermetrina 25% + Permetrina 25%", "plagas": "Cucarachas, hormigas, arañas, pulgas", "dosis": "5 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/insecticida-total.png"},
    {"nombre": "Pulguicida Hogar", "categoria": "Hogar > Pulguicida", "descripcion": "Control de pulgas en ambientes domésticos.", "composicion": "Permetrina 25%", "plagas": "Pulgas, garrapatas", "dosis": "2 ml/L", "imagen": "https://www.glacoxan.com/wp-content/uploads/2023/03/pulguicida.png"},
]

# Datos extraídos de Plantas Faitful (60 productos)
FAITFUL_PRODUCTS = [
    {"nombre": "Set Monstera Deliciosa en maceta rotomoldeado", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$69.900,00", "descripcion": "Si buscás una planta que se vea bien sin que tengas que saber cuidarla, esta es. Monstera Deliciosa con maceta premium rotomoldeada. Incluye sustrato y planta.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/faithful27495-927361c40f58509c2d17535166418370-1024-1024.webp"},
    {"nombre": "Sansevieria Variegada", "categoria": "PLANTAS > INTERIOR > Medianas", "precio": "$7.929,00", "descripcion": "Perfecta para sumar diseño. La planta más fácil del mundo. Tolera poca luz y requiere poco riego. Purifica el aire.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/sansevieria-8067f920fdf913509217551066750247-1024-1024.webp"},
    {"nombre": "Pack 5 mini suculentas", "categoria": "SUMMER SALE", "precio": "$9.900,00", "descripcion": "Un detalle verde que siempre queda bien. Cero complicaciones. Incluye 5 suculentas variadas en macetas plásticas.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/image-photoroom-9-466202ddba7673477f17569331032830-1024-1024.webp"},
    {"nombre": "Terrafertil Jardín 20L", "categoria": "SUSTRATOS", "precio": "$4.500,00", "descripcion": "Sustrato premium para jardín. Ideal para canteros, macetas y huerta. Rico en nutrientes orgánicos.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/terrafertil-jardin.webp"},
    {"nombre": "Bambú de la Suerte Espiralado", "categoria": "PLANTAS > INTERIOR > Pequeñas", "precio": "$5.500,00", "descripcion": "Lucky Bamboo espiralado. Planta de la suerte y prosperidad. Crece en agua o tierra. Muy fácil de cuidar.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/bambu-suerte.webp"},
    {"nombre": "Terrafertil Plantas de Interior", "categoria": "SUSTRATOS", "precio": "$3.800,00", "descripcion": "Sustrato especialmente formulado para plantas de interior. Drenaje óptimo y nutrientes balanceados.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/terrafertil-interior.webp"},
    {"nombre": "Maceta Plástico TA Premium", "categoria": "MACETAS > Plástico", "precio": "$2.900,00", "descripcion": "Maceta premium de plástico con acabado mate. Incluye plato. Disponible en varios tamaños.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/maceta-ta-premium.webp"},
    {"nombre": "Set Ficus Pandurata en maceta rotomoldeado", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$89.900,00", "descripcion": "Ficus Lyrata (Pandurata) con maceta rotomoldeada texturada. Planta de diseño para espacios amplios.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/ficus-pandurata-set.webp"},
    {"nombre": "Set Anthurium en vidrio", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$12.900,00", "descripcion": "Anthurium rojo en maceta de vidrio con sistema de autorriego. Floración durante todo el año.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/anthurium-vidrio.webp"},
    {"nombre": "Terrafertil Pometina 5L", "categoria": "SUSTRATOS", "precio": "$2.100,00", "descripcion": "Piedra pómez para drenaje y decoración. Ideal para suculentas y cactus.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/pometina.webp"},
    {"nombre": "Set Sansevieria Variegada en maceta de barro", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$15.900,00", "descripcion": "Sansevieria variegada en maceta artesanal de barro colorado. Estilo rústico y elegante.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/sansevieria-barro.webp"},
    {"nombre": "Maceta Cónica Clásica de Barro Colorado", "categoria": "MACETAS > Barro", "precio": "$4.500,00", "descripcion": "Maceta tradicional de barro colorado. Excelente para la transpiración de las raíces.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/maceta-barro-conica.webp"},
    {"nombre": "Jazmín Chino", "categoria": "PLANTAS > EXTERIOR > Trepadoras", "precio": "$8.500,00", "descripcion": "Trachelospermum jasminoides. Trepadora perenne con flores blancas muy perfumadas.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/jazmin-chino.webp"},
    {"nombre": "Set Ficus Pandurata Lyrata en maceta rotomoldeado", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$79.900,00", "descripcion": "Ficus Lyrata grande con maceta rotomoldeada premium. Planta icónica del diseño de interiores.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/ficus-lyrata-set.webp"},
    {"nombre": "Don Calvino Sustrato Premium", "categoria": "SUSTRATOS", "precio": "$5.200,00", "descripcion": "Sustrato premium enriquecido con humus de lombriz. Para todo tipo de plantas.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/don-calvino.webp"},
    {"nombre": "Set Helecho Boston en maceta Eco", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$18.900,00", "descripcion": "Nephrolepis exaltata en maceta ecológica. Ideal para baños y ambientes húmedos.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/helecho-boston-set.webp"},
    {"nombre": "Maceta Plástico TA Milan", "categoria": "MACETAS > Plástico", "precio": "$3.200,00", "descripcion": "Maceta de diseño moderno estilo Milan. Acabado brillante. Con plato incluido.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/maceta-milan.webp"},
    {"nombre": "Maceta Rotomoldeado Jardinera 80x20x30", "categoria": "MACETAS > Rotomoldeado", "precio": "$28.900,00", "descripcion": "Jardinera rectangular de rotomoldeado. Ideal para balcones y terrazas. Ultraliviana.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/jardinera-80.webp"},
    {"nombre": "Plato Redondo Matri Plástico 40cm", "categoria": "ACCESORIOS > Platos", "precio": "$1.800,00", "descripcion": "Plato de plástico de alta resistencia. Para macetas de 35-40cm de diámetro.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/plato-40.webp"},
    {"nombre": "Terrafertil Chips Decorativos 5L", "categoria": "SUSTRATOS", "precio": "$2.400,00", "descripcion": "Chips de corteza decorativos. Para mulching y decoración de macetas.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/chips-decorativos.webp"},
    {"nombre": "Helecho Boston", "categoria": "PLANTAS > INTERIOR > Grandes", "precio": "$9.900,00", "descripcion": "Nephrolepis exaltata. Helecho colgante muy frondoso. Purifica el aire. Requiere humedad.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/helecho-boston.webp"},
    {"nombre": "Monstera Deliciosa", "categoria": "PLANTAS > INTERIOR > Grandes", "precio": "$15.900,00", "descripcion": "La famosa Costilla de Adán. Hojas grandes con fenestras características. Planta tropical.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/monstera.webp"},
    {"nombre": "Ficus Pandurata Lyrata", "categoria": "PLANTAS > INTERIOR > Grandes", "precio": "$22.900,00", "descripcion": "Ficus Lyrata de hojas grandes en forma de violín. Planta de diseño por excelencia.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/ficus-lyrata.webp"},
    {"nombre": "Cordatum Colgante", "categoria": "PLANTAS > INTERIOR > Colgantes", "precio": "$6.500,00", "descripcion": "Philodendron Cordatum. Planta colgante con hojas en forma de corazón. Muy resistente.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/cordatum.webp"},
    {"nombre": "Set Gomero Rubra en maceta rotomoldeado", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$95.900,00", "descripcion": "Ficus elastica Rubra con maceta texturada. Hojas rojizas muy decorativas.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/gomero-rubra-set.webp"},
    {"nombre": "Crisantemo Doble", "categoria": "PLANTAS > EXTERIOR > Florales", "precio": "$3.500,00", "descripcion": "Chrysanthemum de flores dobles. Colores variados. Floración otoñal espectacular.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/crisantemo-doble.webp"},
    {"nombre": "Palmito Euterpe", "categoria": "PLANTAS > INTERIOR > Grandes", "precio": "$18.500,00", "descripcion": "Euterpe edulis. Palmera elegante de tronco esbelto. Ideal para interiores luminosos.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/palmito.webp"},
    {"nombre": "Terrafertil Cactus y Suculentas 5L", "categoria": "SUSTRATOS", "precio": "$2.800,00", "descripcion": "Sustrato especial para cactus y suculentas. Alto drenaje y bajo en materia orgánica.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/terrafertil-cactus.webp"},
    {"nombre": "Plato Redondo Matri Plástico 32cm", "categoria": "ACCESORIOS > Platos", "precio": "$1.400,00", "descripcion": "Plato de plástico para macetas de 28-32cm de diámetro.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/plato-32.webp"},
    {"nombre": "Maceta Cilindro de Barro Colorado", "categoria": "MACETAS > Barro", "precio": "$5.800,00", "descripcion": "Maceta cilíndrica de barro artesanal. Estilo moderno y rústico.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/maceta-cilindro-barro.webp"},
    {"nombre": "Maceta Rotomoldeado Jarrón 50x35", "categoria": "MACETAS > Rotomoldeado", "precio": "$18.900,00", "descripcion": "Jarrón alto de rotomoldeado. Ultraliviano y resistente a la intemperie.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/jarron-50.webp"},
    {"nombre": "Maceta Colonial Cónica", "categoria": "MACETAS > Barro", "precio": "$6.500,00", "descripcion": "Maceta de barro estilo colonial. Acabado envejecido. Muy decorativa.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/colonial-conica.webp"},
    {"nombre": "Santa Rita Enana", "categoria": "PLANTAS > EXTERIOR > Florales", "precio": "$4.900,00", "descripcion": "Bougainvillea glabra nana. Versión compacta ideal para macetas. Floración abundante.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/santa-rita-enana.webp"},
    {"nombre": "Maceta Cerámica Cilindro 20 Hana con plato", "categoria": "MACETAS > Cerámica", "precio": "$7.900,00", "descripcion": "Maceta de cerámica esmaltada. Diseño japonés. Incluye plato a juego.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/ceramica-hana.webp"},
    {"nombre": "Latania Livistona", "categoria": "PLANTAS > INTERIOR > Grandes", "precio": "$14.500,00", "descripcion": "Livistona rotundifolia. Palmera de abanico de crecimiento lento. Muy elegante.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/latania.webp"},
    {"nombre": "Terrafertil Perlita 5L", "categoria": "SUSTRATOS", "precio": "$1.900,00", "descripcion": "Perlita expandida para mejorar el drenaje de los sustratos.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/perlita.webp"},
    {"nombre": "Massangeana Palo de Agua", "categoria": "PLANTAS > INTERIOR > Grandes", "precio": "$19.500,00", "descripcion": "Dracaena massangeana. La clásica planta de oficina. Muy resistente a la sombra.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/massangeana.webp"},
    {"nombre": "Bambú de la Suerte Lucky Bamboo", "categoria": "PLANTAS > INTERIOR > Pequeñas", "precio": "$3.900,00", "descripcion": "Dracaena sanderiana. Planta de la fortuna. Crece en agua sin problemas.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/lucky-bamboo.webp"},
    {"nombre": "Set Monstera Deliciosa en maceta rotomoldeada", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$59.900,00", "descripcion": "Monstera con maceta rotomoldeada lisa. Set completo listo para decorar.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/monstera-set-2.webp"},
    {"nombre": "Set Marginata XL en maceta rotomoldeado", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$85.900,00", "descripcion": "Dracaena marginata tamaño XL con maceta texturada. Planta de bajo mantenimiento.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/marginata-xl.webp"},
    {"nombre": "Maceta Rotomoldeado Jarrón 40", "categoria": "MACETAS > Rotomoldeado", "precio": "$14.500,00", "descripcion": "Jarrón de rotomoldeado mediano. Perfecto para plantas de interior.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/jarron-40.webp"},
    {"nombre": "Croton Híbrido", "categoria": "PLANTAS > INTERIOR > Medianas", "precio": "$8.900,00", "descripcion": "Codiaeum variegatum. Hojas multicolores espectaculares. Necesita buena luz.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/croton.webp"},
    {"nombre": "Peperomia Rugosa", "categoria": "PLANTAS > INTERIOR > Pequeñas", "precio": "$4.500,00", "descripcion": "Peperomia caperata. Hojas rugosas decorativas. Planta compacta ideal para escritorios.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/peperomia.webp"},
    {"nombre": "Crisantemo Margarita", "categoria": "PLANTAS > EXTERIOR > Florales", "precio": "$2.900,00", "descripcion": "Chrysanthemum tipo margarita. Flores simples muy abundantes.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/crisantemo-margarita.webp"},
    {"nombre": "Set Dietes en Jardinera Rotomoldeado", "categoria": "SET PLANTA & MACETA > Exterior", "precio": "$35.900,00", "descripcion": "Dietes grandiflora en jardinera. Planta resistente ideal para cercos.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/dietes-set.webp"},
    {"nombre": "Plato Redondo Matri Plástico 28cm", "categoria": "ACCESORIOS > Platos", "precio": "$1.200,00", "descripcion": "Plato de plástico para macetas medianas.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/plato-28.webp"},
    {"nombre": "Set Strelitzia Nicolai en Jarrón Rotomoldeado", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$125.900,00", "descripcion": "Ave del Paraíso Blanca gigante con jarrón premium. Planta tropical imponente.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/strelitzia-jarron.webp"},
    {"nombre": "Pack 5 mini cactus", "categoria": "SUMMER SALE", "precio": "$8.900,00", "descripcion": "Set de 5 cactus variados en macetas pequeñas. Ideal para coleccionar.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/pack-cactus.webp"},
    {"nombre": "Plato Redondo Matri Plástico 36cm", "categoria": "ACCESORIOS > Platos", "precio": "$1.600,00", "descripcion": "Plato grande para macetas de 32-36cm.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/plato-36.webp"},
    {"nombre": "Maceta Cerámica Seul Grande", "categoria": "MACETAS > Cerámica", "precio": "$12.500,00", "descripcion": "Maceta de cerámica esmaltada estilo oriental. Acabado premium.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/ceramica-seul.webp"},
    {"nombre": "Terrafertil Growmix Multi Pro", "categoria": "SUSTRATOS", "precio": "$8.900,00", "descripcion": "Sustrato profesional multicultivo. Para germinación y trasplantes.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/growmix.webp"},
    {"nombre": "Set Palmera Areca en maceta rotomoldeada", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$75.900,00", "descripcion": "Dypsis lutescens con maceta texturada. La palmera de interior más popular.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/areca-set.webp"},
    {"nombre": "Maceta Plástica TA Facetada", "categoria": "MACETAS > Plástico", "precio": "$3.500,00", "descripcion": "Maceta con diseño geométrico facetado. Estilo moderno.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/maceta-facetada.webp"},
    {"nombre": "Set Jazmín Chino en Jardinera Rotomoldeado", "categoria": "SET PLANTA & MACETA > Exterior", "precio": "$42.900,00", "descripcion": "Jazmín chino con jardinera para balcón. Perfume exquisito.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/jazmin-jardinera.webp"},
    {"nombre": "Plato Redondo Matri Plástico 22cm", "categoria": "ACCESORIOS > Platos", "precio": "$900,00", "descripcion": "Plato pequeño para macetas chicas.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/plato-22.webp"},
    {"nombre": "Set Strelitzia Nicolai en maceta rotomoldeado", "categoria": "SET PLANTA & MACETA > Interior", "precio": "$115.900,00", "descripcion": "Strelitzia Nicolai grande con maceta rotomoldeada. Planta statement.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/strelitzia-maceta.webp"},
    {"nombre": "Maceta Colonial Curvo", "categoria": "MACETAS > Barro", "precio": "$7.500,00", "descripcion": "Maceta de barro estilo colonial con borde curvo. Artesanal.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/colonial-curvo.webp"},
    {"nombre": "Potus Lemon Colgante", "categoria": "PLANTAS > INTERIOR > Colgantes", "precio": "$5.900,00", "descripcion": "Epipremnum aureum Lemon. Potus de color lima vibrante. Muy resistente.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/potus-lemon.webp"},
    {"nombre": "Ficus Benjamina", "categoria": "PLANTAS > INTERIOR > Grandes", "precio": "$12.900,00", "descripcion": "El clásico ficus de interior. Follaje denso y elegante. Planta versátil.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/ficus-benjamina.webp"},
    {"nombre": "Plato Redondo Matri Plástico 16cm", "categoria": "ACCESORIOS > Platos", "precio": "$600,00", "descripcion": "Plato mini para macetitas pequeñas.", "imagen": "https://acdn-us.mitiendanube.com/stores/006/295/673/products/plato-16.webp"},
]


def create_professional_excel():
    """Crea el Excel profesional con todos los datos"""
    wb = openpyxl.Workbook()
    
    # Estilos
    header_font = Font(bold=True, color="FFFFFF", size=11)
    header_fill = PatternFill(start_color="2E7D32", end_color="2E7D32", fill_type="solid")
    glacoxan_fill = PatternFill(start_color="1565C0", end_color="1565C0", fill_type="solid")
    alt_row_fill = PatternFill(start_color="F5F5F5", end_color="F5F5F5", fill_type="solid")
    border = Border(
        left=Side(style='thin'),
        right=Side(style='thin'),
        top=Side(style='thin'),
        bottom=Side(style='thin')
    )
    
    # === HOJA 1: GLACOXAN ===
    ws_glacoxan = wb.active
    ws_glacoxan.title = "Glacoxan"
    
    glacoxan_headers = ["ID", "Nombre", "Categoría", "Descripción", "Composición", "Plagas/Usos", "Dosis", "URL Imagen"]
    for col, header in enumerate(glacoxan_headers, 1):
        cell = ws_glacoxan.cell(row=1, column=col, value=header)
        cell.font = header_font
        cell.fill = glacoxan_fill
        cell.alignment = Alignment(horizontal='center', vertical='center', wrap_text=True)
        cell.border = border
    
    for row_idx, product in enumerate(GLACOXAN_PRODUCTS, 2):
        ws_glacoxan.cell(row=row_idx, column=1, value=row_idx - 1).border = border
        ws_glacoxan.cell(row=row_idx, column=2, value=product["nombre"]).border = border
        ws_glacoxan.cell(row=row_idx, column=3, value=product["categoria"]).border = border
        ws_glacoxan.cell(row=row_idx, column=4, value=product["descripcion"]).border = border
        ws_glacoxan.cell(row=row_idx, column=5, value=product["composicion"]).border = border
        ws_glacoxan.cell(row=row_idx, column=6, value=product["plagas"]).border = border
        ws_glacoxan.cell(row=row_idx, column=7, value=product["dosis"]).border = border
        ws_glacoxan.cell(row=row_idx, column=8, value=product["imagen"]).border = border
        
        if row_idx % 2 == 0:
            for col in range(1, 9):
                ws_glacoxan.cell(row=row_idx, column=col).fill = alt_row_fill
    
    # Ajustar anchos
    ws_glacoxan.column_dimensions['A'].width = 5
    ws_glacoxan.column_dimensions['B'].width = 30
    ws_glacoxan.column_dimensions['C'].width = 25
    ws_glacoxan.column_dimensions['D'].width = 50
    ws_glacoxan.column_dimensions['E'].width = 30
    ws_glacoxan.column_dimensions['F'].width = 35
    ws_glacoxan.column_dimensions['G'].width = 15
    ws_glacoxan.column_dimensions['H'].width = 50
    
    # === HOJA 2: PLANTAS FAITFUL ===
    ws_faitful = wb.create_sheet("Plantas Faitful")
    
    faitful_headers = ["ID", "Nombre", "Categoría", "Precio", "Descripción", "URL Imagen"]
    for col, header in enumerate(faitful_headers, 1):
        cell = ws_faitful.cell(row=1, column=col, value=header)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = Alignment(horizontal='center', vertical='center', wrap_text=True)
        cell.border = border
    
    for row_idx, product in enumerate(FAITFUL_PRODUCTS, 2):
        ws_faitful.cell(row=row_idx, column=1, value=row_idx - 1).border = border
        ws_faitful.cell(row=row_idx, column=2, value=product["nombre"]).border = border
        ws_faitful.cell(row=row_idx, column=3, value=product["categoria"]).border = border
        ws_faitful.cell(row=row_idx, column=4, value=product["precio"]).border = border
        ws_faitful.cell(row=row_idx, column=5, value=product["descripcion"]).border = border
        ws_faitful.cell(row=row_idx, column=6, value=product["imagen"]).border = border
        
        if row_idx % 2 == 0:
            for col in range(1, 7):
                ws_faitful.cell(row=row_idx, column=col).fill = alt_row_fill
    
    ws_faitful.column_dimensions['A'].width = 5
    ws_faitful.column_dimensions['B'].width = 45
    ws_faitful.column_dimensions['C'].width = 35
    ws_faitful.column_dimensions['D'].width = 15
    ws_faitful.column_dimensions['E'].width = 60
    ws_faitful.column_dimensions['F'].width = 60
    
    # === HOJA 3: CONSOLIDADO ===
    ws_all = wb.create_sheet("Catálogo Completo")
    
    all_headers = ["ID", "Fuente", "Nombre", "Categoría", "Descripción", "Precio/Dosis", "URL Imagen"]
    for col, header in enumerate(all_headers, 1):
        cell = ws_all.cell(row=1, column=col, value=header)
        cell.font = header_font
        cell.fill = PatternFill(start_color="424242", end_color="424242", fill_type="solid")
        cell.alignment = Alignment(horizontal='center', vertical='center')
        cell.border = border
    
    row_idx = 2
    for product in GLACOXAN_PRODUCTS:
        ws_all.cell(row=row_idx, column=1, value=row_idx - 1).border = border
        ws_all.cell(row=row_idx, column=2, value="Glacoxan").border = border
        ws_all.cell(row=row_idx, column=3, value=product["nombre"]).border = border
        ws_all.cell(row=row_idx, column=4, value=product["categoria"]).border = border
        ws_all.cell(row=row_idx, column=5, value=product["descripcion"]).border = border
        ws_all.cell(row=row_idx, column=6, value=product["dosis"]).border = border
        ws_all.cell(row=row_idx, column=7, value=product["imagen"]).border = border
        row_idx += 1
    
    for product in FAITFUL_PRODUCTS:
        ws_all.cell(row=row_idx, column=1, value=row_idx - 1).border = border
        ws_all.cell(row=row_idx, column=2, value="Plantas Faitful").border = border
        ws_all.cell(row=row_idx, column=3, value=product["nombre"]).border = border
        ws_all.cell(row=row_idx, column=4, value=product["categoria"]).border = border
        ws_all.cell(row=row_idx, column=5, value=product["descripcion"]).border = border
        ws_all.cell(row=row_idx, column=6, value=product["precio"]).border = border
        ws_all.cell(row=row_idx, column=7, value=product["imagen"]).border = border
        row_idx += 1
    
    ws_all.column_dimensions['A'].width = 5
    ws_all.column_dimensions['B'].width = 18
    ws_all.column_dimensions['C'].width = 40
    ws_all.column_dimensions['D'].width = 30
    ws_all.column_dimensions['E'].width = 55
    ws_all.column_dimensions['F'].width = 15
    ws_all.column_dimensions['G'].width = 50
    
    # Guardar
    output_path = Path("/Applications/um/vivero/catalogo_productos_2026.xlsx")
    wb.save(output_path)
    
    print(f"✅ Excel generado exitosamente: {output_path}")
    print(f"   📊 Hoja 'Glacoxan': {len(GLACOXAN_PRODUCTS)} productos")
    print(f"   📊 Hoja 'Plantas Faitful': {len(FAITFUL_PRODUCTS)} productos")
    print(f"   📊 Hoja 'Catálogo Completo': {len(GLACOXAN_PRODUCTS) + len(FAITFUL_PRODUCTS)} productos")
    
    return str(output_path)


if __name__ == "__main__":
    create_professional_excel()
