#!/usr/bin/env python3
"""
Create WooCommerce API Keys directly in database
"""

import mysql.connector
import hashlib
import secrets
import base64
from datetime import datetime

def create_wc_api_keys():
    # Database connection
    conn = mysql.connector.connect(
        host='localhost',
        port=3306,
        user='loscocos_user',
        password='loscocos_2024',
        database='loscocos_wp'
    )
    
    cursor = conn.cursor()
    
    # Generate API keys
    consumer_key = 'ck_' + secrets.token_urlsafe(32)
    consumer_secret = 'cs_' + secrets.token_urlsafe(32)
    
    # Hash the consumer secret for storage
    consumer_secret_hash = hashlib.sha256(consumer_secret.encode()).hexdigest()
    
    # Get user ID (admin)
    cursor.execute("SELECT ID FROM wp_users WHERE user_login = 'admin'")
    user_result = cursor.fetchone()
    
    if not user_result:
        print("Admin user not found")
        return None, None
    
    user_id = user_result[0]
    
    # Insert API key into database
    insert_query = """
    INSERT INTO wp_woocommerce_api_keys 
    (user_id, description, permissions, consumer_key, consumer_secret, nonces, truncated_key, last_access) 
    VALUES (%s, %s, %s, %s, %s, %s, %s, NULL)
    """
    
    # Truncated key is first 7 characters for display
    truncated_key = consumer_key[:7] + '...'
    
    cursor.execute(insert_query, (
        user_id,
        'Ultimate Image Automation',
        'read_write',
        consumer_key,
        consumer_secret_hash,
        '',  # nonces
        truncated_key
    ))
    
    conn.commit()
    cursor.close()
    conn.close()
    
    print("✅ WooCommerce API keys created successfully!")
    print(f"Consumer Key: {consumer_key}")
    print(f"Consumer Secret: {consumer_secret}")
    
    return consumer_key, consumer_secret

if __name__ == "__main__":
    try:
        ck, cs = create_wc_api_keys()
        
        # Update .env file
        if ck and cs:
            with open('.env_ultimate', 'w') as f:
                f.write(f"""# WordPress/WooCommerce Configuration
WORDPRESS_URL=http://localhost:8080
WP_USERNAME=admin
WP_APP_PASSWORD=loscocos2024

# WooCommerce API
WC_CONSUMER_KEY={ck}
WC_CONSUMER_SECRET={cs}

# Image API Keys
UNSPLASH_API_KEY=YjiiXP_kb4z7yhpBMrK3OWeWx1jf_VQrzl37VfFssLY
PIXABAY_API_KEY=
PEXELS_API_KEY=
""")
            print("✅ .env_ultimate file updated!")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        print("   Try creating keys manually in WordPress admin:")
        print("   http://localhost:8080/wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys")