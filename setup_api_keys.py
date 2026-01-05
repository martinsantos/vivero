#!/usr/bin/env python3
"""
Generate WooCommerce API Keys for Ultimate Image Automation
"""

import requests
import base64
import json
import sys

def generate_wc_api_keys():
    base_url = "http://localhost:8080"
    username = "admin"
    password = "loscocos2024"
    
    print("🔑 Generando claves de WooCommerce API...")
    
    # First, get user ID
    print("   1. Obteniendo user ID...")
    auth = base64.b64encode(f"{username}:{password}".encode()).decode()
    headers = {"Authorization": f"Basic {auth}"}
    
    try:
        response = requests.get(f"{base_url}/wp-json/wp/v2/users/me", headers=headers, timeout=30)
        if response.status_code != 200:
            print(f"   ❌ Error obteniendo user: {response.status_code}")
            print(f"   Respuesta: {response.text[:200]}")
            return None, None
        
        user_data = response.json()
        user_id = user_data.get("id")
        print(f"   ✅ User ID: {user_id}")
        
    except Exception as e:
        print(f"   ❌ Error de conexión: {e}")
        return None, None
    
    # Create API keys
    print("   2. Creando claves API...")
    payload = {
        "description": "Ultimate Image Automation",
        "user_id": user_id,
        "permissions": "read_write"
    }
    
    try:
        response = requests.post(
            f"{base_url}/wp-json/wc/v3/keys",
            headers={**headers, "Content-Type": "application/json"},
            json=payload,
            timeout=30
        )
        
        if response.status_code not in (200, 201):
            print(f"   ❌ Error creando claves: {response.status_code}")
            print(f"   Respuesta: {response.text[:300]}")
            return None, None
        
        keys_data = response.json()
        consumer_key = keys_data.get("consumer_key") or keys_data.get("key")
        consumer_secret = keys_data.get("consumer_secret") or keys_data.get("secret")
        
        if consumer_key and consumer_secret:
            print("   ✅ Claves generadas exitosamente!")
            return consumer_key, consumer_secret
        else:
            print(f"   ❌ Claves no encontradas en respuesta: {keys_data}")
            return None, None
            
    except Exception as e:
        print(f"   ❌ Error creando claves: {e}")
        return None, None

def update_env_file(consumer_key, consumer_secret):
    print("📝 Actualizando archivo .env...")
    
    try:
        # Read current .env
        with open(".env", "r") as f:
            lines = f.readlines()
        
        # Update lines
        updated_lines = []
        found_ck = False
        found_cs = False
        
        for line in lines:
            if line.startswith("WC_CONSUMER_KEY="):
                updated_lines.append(f"WC_CONSUMER_KEY={consumer_key}\n")
                found_ck = True
            elif line.startswith("WC_CONSUMER_SECRET="):
                updated_lines.append(f"WC_CONSUMER_SECRET={consumer_secret}\n")
                found_cs = True
            else:
                updated_lines.append(line)
        
        # Add keys if not found
        if not found_ck:
            updated_lines.append(f"WC_CONSUMER_KEY={consumer_key}\n")
        if not found_cs:
            updated_lines.append(f"WC_CONSUMER_SECRET={consumer_secret}\n")
        
        # Write back
        with open(".env", "w") as f:
            f.writelines(updated_lines)
        
        print("   ✅ Archivo .env actualizado!")
        return True
        
    except Exception as e:
        print(f"   ❌ Error actualizando .env: {e}")
        return False

def test_api_keys(consumer_key, consumer_secret):
    print("🧪 Probando claves API...")
    
    try:
        # Test with WooCommerce package if available
        try:
            from woocommerce import API
            
            wcapi = API(
                url="http://localhost:8080",
                consumer_key=consumer_key,
                consumer_secret=consumer_secret,
                version="wc/v3",
                wp_api=True,
                query_string_auth=True,
            )
            
            response = wcapi.get("products", params={"per_page": 1})
            if response.status_code == 200:
                products = response.json()
                print(f"   ✅ API funcionando! Productos disponibles: {len(products)}")
                return True
            else:
                print(f"   ❌ Error en API: {response.status_code}")
                return False
                
        except ImportError:
            print("   ⚠️  woocommerce package no disponible, probando con requests...")
            
            # Test with direct requests
            url = f"http://localhost:8080/wp-json/wc/v3/products?consumer_key={consumer_key}&consumer_secret={consumer_secret}&per_page=1"
            response = requests.get(url, timeout=30)
            
            if response.status_code == 200:
                products = response.json()
                print(f"   ✅ API funcionando con requests! Productos: {len(products)}")
                return True
            else:
                print(f"   ❌ Error con requests: {response.status_code}")
                return False
            
    except Exception as e:
        print(f"   ❌ Error probando API: {e}")
        return False

if __name__ == "__main__":
    print("🌱 Configurador de API Keys para Los Cocos")
    print("==========================================")
    
    # Generate keys
    ck, cs = generate_wc_api_keys()
    
    if not ck or not cs:
        print("\n❌ No se pudieron generar las claves automáticamente")
        print("   Debes crearlas manualmente en:")
        print("   http://localhost:8080/wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys")
        sys.exit(1)
    
    # Update .env file
    if update_env_file(ck, cs):
        # Test the keys
        if test_api_keys(ck, cs):
            print("\n🎉 ¡Configuración completada exitosamente!")
            print("   Las claves API están funcionando correctamente")
            print("   Ya puedes ejecutar: python3 ultimate_image_automation.py")
        else:
            print("\n⚠️  Claves creadas pero hay problemas de conexión")
    else:
        print("\n❌ Error actualizando archivo .env")
        print(f"   Consumer Key: {ck}")
        print(f"   Consumer Secret: {cs}")