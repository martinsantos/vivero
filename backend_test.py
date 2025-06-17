import requests
import unittest
import json

# Get the backend URL from the frontend .env file
BACKEND_URL = "https://5653f7a4-7eab-40da-b582-db5b47b8c560.preview.emergentagent.com"

class ViveroLosCocosTester(unittest.TestCase):
    def test_get_plants(self):
        """Test GET /api/plants - should return list of plants"""
        print("\n🔍 Testing GET /api/plants...")
        response = requests.get(f"{BACKEND_URL}/api/plants")
        
        self.assertEqual(response.status_code, 200, "Expected status code 200")
        plants = response.json()
        self.assertIsInstance(plants, list, "Expected a list of plants")
        self.assertGreater(len(plants), 0, "Expected at least one plant")
        
        # Check plant structure
        plant = plants[0]
        self.assertIn("id", plant, "Plant should have an id")
        self.assertIn("name", plant, "Plant should have a name")
        self.assertIn("price", plant, "Plant should have a price")
        self.assertIn("description", plant, "Plant should have a description")
        self.assertIn("category", plant, "Plant should have a category")
        self.assertIn("image_url", plant, "Plant should have an image_url")
        self.assertIn("is_daily_offer", plant, "Plant should have is_daily_offer flag")
        
        print("✅ GET /api/plants test passed")
        return plants

    def test_get_daily_offers(self):
        """Test GET /api/plants/daily-offers - should return plants marked as daily offers"""
        print("\n🔍 Testing GET /api/plants/daily-offers...")
        response = requests.get(f"{BACKEND_URL}/api/plants/daily-offers")
        
        self.assertEqual(response.status_code, 200, "Expected status code 200")
        offers = response.json()
        self.assertIsInstance(offers, list, "Expected a list of offers")
        
        # Check if all returned plants are marked as daily offers
        for offer in offers:
            self.assertTrue(offer["is_daily_offer"], "All plants should be marked as daily offers")
            
        print("✅ GET /api/plants/daily-offers test passed")
        return offers

    def test_get_categories(self):
        """Test GET /api/categories - should return plant categories"""
        print("\n🔍 Testing GET /api/categories...")
        response = requests.get(f"{BACKEND_URL}/api/categories")
        
        self.assertEqual(response.status_code, 200, "Expected status code 200")
        categories = response.json()
        self.assertIsInstance(categories, list, "Expected a list of categories")
        self.assertGreater(len(categories), 0, "Expected at least one category")
        
        # Check if expected categories are present
        expected_categories = ["Interior", "Exterior", "Suculentas"]
        for category in expected_categories:
            self.assertIn(category, categories, f"Expected category '{category}' to be present")
            
        print("✅ GET /api/categories test passed")
        return categories

    def test_get_plants_by_category(self):
        """Test GET /api/plants/category/{category} - should return plants filtered by category"""
        # First get all categories
        categories = self.test_get_categories()
        
        if not categories:
            self.fail("No categories available to test")
        
        # Test for each category
        for category in categories:
            print(f"\n🔍 Testing GET /api/plants/category/{category}...")
            response = requests.get(f"{BACKEND_URL}/api/plants/category/{category}")
            
            self.assertEqual(response.status_code, 200, f"Expected status code 200 for category {category}")
            plants = response.json()
            self.assertIsInstance(plants, list, f"Expected a list of plants for category {category}")
            
            # Check if all plants belong to the requested category
            for plant in plants:
                self.assertEqual(plant["category"], category, f"Plant should belong to category {category}")
                
            print(f"✅ GET /api/plants/category/{category} test passed")

    def test_create_order(self):
        """Test POST /api/orders - should create new orders"""
        print("\n🔍 Testing POST /api/orders...")
        
        # First get some plants to include in the order
        plants = self.test_get_plants()
        if not plants or len(plants) < 2:
            self.fail("Not enough plants available to test order creation")
        
        # Create order data
        order_data = {
            "customer_name": "Test Customer",
            "customer_email": "test@example.com",
            "customer_phone": "123-456-7890",
            "items": [
                {"plant_id": plants[0]["id"], "quantity": 2},
                {"plant_id": plants[1]["id"], "quantity": 1}
            ],
            "total": plants[0]["price"] * 2 + plants[1]["price"]
        }
        
        # Send order request
        response = requests.post(
            f"{BACKEND_URL}/api/orders",
            json=order_data,
            headers={"Content-Type": "application/json"}
        )
        
        self.assertEqual(response.status_code, 200, "Expected status code 200")
        result = response.json()
        self.assertIn("message", result, "Response should contain a message")
        self.assertIn("order_id", result, "Response should contain an order_id")
        
        print("✅ POST /api/orders test passed")

    def test_get_plant_by_id(self):
        """Test GET /api/plants/{plant_id} - should return a specific plant"""
        # First get all plants
        plants = self.test_get_plants()
        
        if not plants:
            self.fail("No plants available to test")
        
        plant_id = plants[0]["id"]
        print(f"\n🔍 Testing GET /api/plants/{plant_id}...")
        
        response = requests.get(f"{BACKEND_URL}/api/plants/{plant_id}")
        
        self.assertEqual(response.status_code, 200, "Expected status code 200")
        plant = response.json()
        self.assertEqual(plant["id"], plant_id, "Returned plant should have the requested ID")
        
        print(f"✅ GET /api/plants/{plant_id} test passed")

if __name__ == "__main__":
    # Run the tests
    unittest.main(argv=['first-arg-is-ignored'], exit=False)