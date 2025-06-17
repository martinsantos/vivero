from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional
from datetime import datetime
import os
from pymongo import MongoClient
import uuid

# MongoDB setup
MONGO_URL = os.environ.get('MONGO_URL', 'mongodb://localhost:27017/')
client = MongoClient(MONGO_URL)
db = client.vivero_db

app = FastAPI(title="Vivero Los Cocos API")

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Pydantic models
class Plant(BaseModel):
    id: str
    name: str
    price: float
    original_price: Optional[float] = None
    description: str
    category: str
    image_url: str
    is_daily_offer: bool = False
    stock: int = 10

class CartItem(BaseModel):
    plant_id: str
    quantity: int

class Order(BaseModel):
    customer_name: str
    customer_email: str
    customer_phone: str
    items: List[CartItem]
    total: float

# Initialize sample data
@app.on_event("startup")
async def startup_event():
    # Check if plants collection is empty
    if db.plants.count_documents({}) == 0:
        sample_plants = [
            {
                "id": str(uuid.uuid4()),
                "name": "Cactus Decorativo",
                "price": 25.0,
                "original_price": 35.0,
                "description": "Hermoso cactus ideal para decoración interior. Muy fácil de cuidar.",
                "category": "Interior",
                "image_url": "https://images.pexels.com/photos/7240176/pexels-photo-7240176.jpeg",
                "is_daily_offer": True,
                "stock": 15
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Planta Morada Exótica",
                "price": 45.0,
                "description": "Planta de interior con hojas moradas únicas. Perfecta para dar color a tu hogar.",
                "category": "Interior",
                "image_url": "https://images.unsplash.com/photo-1695113021051-c9dfcf4a5c89",
                "is_daily_offer": False,
                "stock": 8
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Conjunto de Plantas Pequeñas",
                "price": 60.0,
                "original_price": 80.0,
                "description": "Set de 3 plantas pequeñas ideales para decorar escritorios y mesas.",
                "category": "Interior",
                "image_url": "https://images.unsplash.com/photo-1685381142869-6ba90c5400c0",
                "is_daily_offer": True,
                "stock": 12
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Planta de Jardín Elegante",
                "price": 85.0,
                "description": "Perfecta para jardines exteriores. Resistente y de gran belleza natural.",
                "category": "Exterior",
                "image_url": "https://images.pexels.com/photos/31356131/pexels-photo-31356131.png",
                "is_daily_offer": False,
                "stock": 6
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Arbusto Floreciente",
                "price": 55.0,
                "original_price": 75.0,
                "description": "Hermoso arbusto con flores. Ideal para patios y jardines grandes.",
                "category": "Exterior",
                "image_url": "https://images.unsplash.com/photo-1710637973428-2f5e4844f558",
                "is_daily_offer": True,
                "stock": 10
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Plantas Verdes Decorativas",
                "price": 40.0,
                "description": "Conjunto de plantas verdes para exterior. Muy resistentes al clima.",
                "category": "Exterior",
                "image_url": "https://images.unsplash.com/photo-1627220578926-8ca0b5c5a001",
                "is_daily_offer": False,
                "stock": 20
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Suculenta Premium",
                "price": 30.0,
                "original_price": 45.0,
                "description": "Suculenta de colores vibrantes. Muy fácil de mantener.",
                "category": "Suculentas",
                "image_url": "https://images.pexels.com/photos/5905540/pexels-photo-5905540.jpeg",
                "is_daily_offer": True,
                "stock": 25
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Mini Plantas de Escritorio",
                "price": 35.0,
                "description": "Pequeñas plantas perfectas para oficinas y espacios reducidos.",
                "category": "Interior",
                "image_url": "https://images.unsplash.com/photo-1611651624966-989152e2f753",
                "is_daily_offer": False,
                "stock": 18
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Plantas en Macetas Azules",
                "price": 50.0,
                "description": "Hermosas plantas en elegantes macetas de cerámica azul.",
                "category": "Interior",
                "image_url": "https://images.unsplash.com/photo-1616078206521-5d7e6dc51545",
                "is_daily_offer": False,
                "stock": 14
            }
        ]
        
        db.plants.insert_many(sample_plants)
        print("Sample plants data inserted successfully!")

# API Routes
@app.get("/api/plants")
async def get_plants():
    plants = list(db.plants.find({}, {"_id": 0}))
    return plants

@app.get("/api/plants/daily-offers")
async def get_daily_offers():
    offers = list(db.plants.find({"is_daily_offer": True}, {"_id": 0}))
    return offers

@app.get("/api/plants/category/{category}")
async def get_plants_by_category(category: str):
    plants = list(db.plants.find({"category": category}, {"_id": 0}))
    return plants

@app.get("/api/plants/{plant_id}")
async def get_plant(plant_id: str):
    plant = db.plants.find_one({"id": plant_id}, {"_id": 0})
    if not plant:
        raise HTTPException(status_code=404, detail="Plant not found")
    return plant

@app.post("/api/orders")
async def create_order(order: Order):
    order_data = order.dict()
    order_data["id"] = str(uuid.uuid4())
    order_data["created_at"] = datetime.now()
    order_data["status"] = "pending"
    
    # Insert order
    db.orders.insert_one(order_data)
    
    return {"message": "Order created successfully", "order_id": order_data["id"]}

@app.get("/api/categories")
async def get_categories():
    categories = db.plants.distinct("category")
    return categories

@app.get("/")
async def root():
    return {"message": "Vivero Los Cocos API is running!"}