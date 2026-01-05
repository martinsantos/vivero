#!/usr/bin/env python3
"""
🌱 Los Cocos E-commerce - Real Image Automation Script
====================================================

Replaces SVG placeholder images with real, high-quality original images from multiple sources:
- Unsplash (primary plant photography)
- Pexels (secondary source)  
- Pixabay (backup source)
- Spanish-English plant name translation
- Intelligent search term generation
- Quality ranking and deduplication
- WordPress media library integration

Follows project specifications:
- Multi-source image integration requirement
- Plant-specific image search with Spanish translation
- E-commerce product image integrity requirement
- Docker-based testing requirement

Version: 2.0.0 - Real Images Only
"""

import argparse
import base64
import hashlib
import json
import logging
import os
import sys
import time
import tempfile
import re
from dataclasses import dataclass
from typing import Any, Dict, List, Optional, Tuple
from urllib.parse import urlparse

import requests
from dotenv import load_dotenv
from PIL import Image
from tqdm import tqdm

# Configuration
DEFAULT_IMAGE_SIZE = (1200, 1200)
DEFAULT_IMAGE_QUALITY = 90
DEFAULT_BATCH_SIZE = 20
DEFAULT_DELAY = 2.0
MIN_RESOLUTION = (800, 600)
LOG_FILE = "logs/real_image_automation.log"
STATE_FILE = ".real_image_automation_state.json"
USER_AGENT = "LosCocos-RealImageBot/2.0"

# Spanish to English plant translations (enhanced)
PLANT_TRANSLATIONS = {
    # Basic plant terms
    "planta": "plant", "plantas": "plants", "arbusto": "shrub", "arbustos": "shrubs", 
    "árbol": "tree", "arboles": "trees", "flor": "flower", "flores": "flowers",
    "hoja": "leaf", "hojas": "leaves", "raíz": "root", "raices": "roots",
    
    # Location/usage
    "interior": "indoor houseplant", "exterior": "outdoor garden plant", 
    "jardín": "garden plant", "jardin": "garden plant", "casa": "houseplant",
    "balcón": "balcony plant", "terraza": "terrace plant",
    
    # Plant types
    "suculenta": "succulent", "cactus": "cactus", "helecho": "fern", 
    "palmera": "palm tree", "rosa": "rose", "jazmin": "jasmine",
    "orquídea": "orchid", "orquidea": "orchid", "begonia": "begonia",
    
    # Supplies
    "vivero": "nursery plant", "maceta": "potted plant", "jardinera": "planter",
    "fertilizante": "plant fertilizer", "sustrato": "potting soil", 
    "herramienta": "garden tool", "semilla": "seeds", "abono": "fertilizer",
    
    # Containers
    "ta plastic": "plastic pot", "matri": "ceramic pot", "bols": "bowl planter",
    "redonda": "round pot", "cuadrada": "square pot", "jardinera": "planter box",
    
    # Colors (for containers)
    "negro": "black", "negra": "black", "blanco": "white", "blanca": "white",
    "verde": "green", "rojo": "red", "roja": "red", "azul": "blue",
    "amarillo": "yellow", "amarilla": "yellow", "marron": "brown",
    "violeta": "purple", "naranja": "orange", "beige": "beige"
}

# Plant genera recognition patterns
PLANT_GENERA = [
    "jazmin", "rosa", "begonia", "ficus", "dracaena", "nandina", "buxus",
    "eucalyptus", "liquidambar", "fresno", "mora", "acer", "abedul", 
    "acacia", "brachichiton", "agave", "strelizia", "forsythia", "laurel",
    "thuja", "callistemon", "evonymus", "oleander", "erika", "rocio"
]

log = logging.getLogger("real_image_automation")

def setup_logging(level: int) -> None:
    os.makedirs(os.path.dirname(LOG_FILE), exist_ok=True)
    logging.basicConfig(
        level=level,
        format="%(asctime)s %(levelname)s: %(message)s",
        handlers=[
            logging.StreamHandler(sys.stdout),
            logging.FileHandler(LOG_FILE, encoding='utf-8')
        ]
    )

@dataclass
class Config:
    wordpress_url: str
    wp_username: str
    wp_app_password: str
    wc_key: str
    wc_secret: str
    unsplash_key: Optional[str]
    pixabay_key: Optional[str]
    pexels_key: Optional[str]

@dataclass
class ImageCandidate:
    url: str
    width: int
    height: int
    source: str
    title: str = ""
    quality_score: float = 0.0

class WordPressClient:
    """WordPress REST API client for media uploads and product management"""
    
    def __init__(self, config: Config):
        self.config = config
        self.base_url = config.wordpress_url.rstrip('/')
        self.auth = base64.b64encode(
            f"{config.wp_username}:{config.wp_app_password}".encode()
        ).decode()
        self.headers = {
            "Authorization": f"Basic {self.auth}",
            "User-Agent": USER_AGENT
        }
    
    def get_products_without_real_images(self, per_page: int = 100) -> List[Dict]:
        """Get products that have SVG placeholder images using WordPress REST API"""
        log.info("Fetching products with SVG placeholder images...")
        
        # Use WordPress REST API to get product posts
        url = f"{self.base_url}/wp-json/wp/v2/posts"
        params = {
            "post_type": "product",
            "status": "publish", 
            "per_page": per_page,
            "_embed": "wp:featuredmedia"
        }
        
        try:
            response = requests.get(url, headers=self.headers, params=params, timeout=30)
            response.raise_for_status()
            wp_posts = response.json()
            
            # Convert to product format and check for SVG images
            products = []
            for post in wp_posts:
                # Get featured media
                featured_media = post.get("_embedded", {}).get("wp:featuredmedia", [])
                has_svg = False
                
                if featured_media:
                    media_url = featured_media[0].get("source_url", "")
                    if "placeholder.svg" in media_url or media_url.endswith(".svg"):
                        has_svg = True
                else:
                    # No featured media = needs image
                    has_svg = True
                
                if has_svg:
                    products.append({
                        "id": post["id"],
                        "name": post["title"]["rendered"],
                        "images": [{"src": media_url if featured_media else ""}] if featured_media else []
                    })
            
            log.info(f"Found {len(products)} products with SVG/no images out of {len(wp_posts)} total")
            return products
            
        except Exception as e:
            log.error(f"Error fetching products: {e}")
            return []
    
    def upload_image(self, image_path: str, title: str, alt_text: str) -> Optional[int]:
        """Upload image to WordPress media library"""
        url = f"{self.base_url}/wp-json/wp/v2/media"
        
        try:
            with open(image_path, 'rb') as f:
                files = {
                    'file': (os.path.basename(image_path), f, 'image/jpeg')
                }
                data = {
                    'title': title,
                    'alt_text': alt_text,
                    'caption': f'Image for {title}'
                }
                
                response = requests.post(
                    url, 
                    headers={"Authorization": f"Basic {self.auth}"},
                    files=files,
                    data=data,
                    timeout=60
                )
                
                if response.status_code == 201:
                    media_data = response.json()
                    media_id = media_data.get('id')
                    log.info(f"✅ Uploaded image for '{title}' - Media ID: {media_id}")
                    return media_id
                else:
                    log.error(f"❌ Upload failed for '{title}': {response.status_code} - {response.text[:200]}")
                    return None
                    
        except Exception as e:
            log.error(f"❌ Exception uploading image for '{title}': {e}")
            return None
    
    def set_product_image(self, product_id: int, media_id: int) -> bool:
        """Set media as product featured image"""
        url = f"{self.base_url}/wp-json/wc/v3/products/{product_id}"
        
        data = {
            "images": [{"id": media_id}]
        }
        
        try:
            response = requests.put(
                url,
                headers={**self.headers, "Content-Type": "application/json"},
                json=data,
                timeout=30
            )
            
            if response.status_code == 200:
                log.info(f"✅ Set image for product {product_id}")
                return True
            else:
                log.error(f"❌ Failed to set image for product {product_id}: {response.status_code}")
                return False
                
        except Exception as e:
            log.error(f"❌ Exception setting image for product {product_id}: {e}")
            return False

class UnsplashProvider:
    """Unsplash image provider for high-quality plant photography"""
    
    def __init__(self, api_key: str):
        self.api_key = api_key
        self.base_url = "https://api.unsplash.com"
    
    def search_images(self, query: str, count: int = 5) -> List[ImageCandidate]:
        """Search Unsplash for plant images"""
        if not self.api_key:
            return []
        
        url = f"{self.base_url}/search/photos"
        params = {
            "query": query,
            "per_page": count,
            "orientation": "squarish",
            "order_by": "relevant"
        }
        headers = {"Authorization": f"Client-ID {self.api_key}"}
        
        try:
            response = requests.get(url, headers=headers, params=params, timeout=10)
            response.raise_for_status()
            data = response.json()
            
            candidates = []
            for result in data.get("results", []):
                # Prefer regular size for quality
                image_url = result["urls"].get("regular", result["urls"]["small"])
                width = result.get("width", 0)
                height = result.get("height", 0)
                title = result.get("alt_description") or result.get("description") or query
                
                if width >= MIN_RESOLUTION[0] and height >= MIN_RESOLUTION[1]:
                    score = self._calculate_quality_score(result, query)
                    candidates.append(ImageCandidate(
                        url=image_url,
                        width=width,
                        height=height,
                        source="unsplash",
                        title=title,
                        quality_score=score
                    ))
            
            log.debug(f"Unsplash found {len(candidates)} candidates for '{query}'")
            return candidates
            
        except Exception as e:
            log.warning(f"Unsplash search failed for '{query}': {e}")
            return []
    
    def _calculate_quality_score(self, result: Dict, query: str) -> float:
        """Calculate image quality score"""
        score = 0.0
        
        # Size bonus
        width = result.get("width", 0)
        height = result.get("height", 0)
        if width >= 1920 and height >= 1080:
            score += 3.0
        elif width >= 1200 and height >= 800:
            score += 2.0
        else:
            score += 1.0
        
        # Likes bonus
        likes = result.get("likes", 0)
        score += min(likes / 100, 2.0)
        
        # Description relevance
        description = (result.get("alt_description") or "").lower()
        title = (result.get("description") or "").lower()
        query_lower = query.lower()
        
        if query_lower in description or query_lower in title:
            score += 2.0
        
        return score

class PexelsProvider:
    """Pexels image provider"""
    
    def __init__(self, api_key: str):
        self.api_key = api_key
        self.base_url = "https://api.pexels.com/v1"
    
    def search_images(self, query: str, count: int = 5) -> List[ImageCandidate]:
        """Search Pexels for images"""
        if not self.api_key:
            return []
        
        url = f"{self.base_url}/search"
        params = {
            "query": query,
            "per_page": count,
            "orientation": "square"
        }
        headers = {"Authorization": self.api_key}
        
        try:
            response = requests.get(url, headers=headers, params=params, timeout=10)
            response.raise_for_status()
            data = response.json()
            
            candidates = []
            for photo in data.get("photos", []):
                image_url = photo["src"].get("large", photo["src"]["medium"])
                width = photo.get("width", 0)
                height = photo.get("height", 0)
                title = photo.get("alt", query)
                
                if width >= MIN_RESOLUTION[0] and height >= MIN_RESOLUTION[1]:
                    score = 2.0 + (width * height) / 1000000  # Base score + megapixel bonus
                    candidates.append(ImageCandidate(
                        url=image_url,
                        width=width,
                        height=height,
                        source="pexels",
                        title=title,
                        quality_score=score
                    ))
            
            log.debug(f"Pexels found {len(candidates)} candidates for '{query}'")
            return candidates
            
        except Exception as e:
            log.warning(f"Pexels search failed for '{query}': {e}")
            return []

class PixabayProvider:
    """Pixabay image provider (backup)"""
    
    def __init__(self, api_key: str):
        self.api_key = api_key
        self.base_url = "https://pixabay.com/api/"
    
    def search_images(self, query: str, count: int = 5) -> List[ImageCandidate]:
        """Search Pixabay for images"""
        if not self.api_key:
            return []
        
        params = {
            "key": self.api_key,
            "q": query,
            "image_type": "photo",
            "per_page": count,
            "min_width": MIN_RESOLUTION[0],
            "min_height": MIN_RESOLUTION[1],
            "safesearch": "true"
        }
        
        try:
            response = requests.get(self.base_url, params=params, timeout=10)
            response.raise_for_status()
            data = response.json()
            
            candidates = []
            for hit in data.get("hits", []):
                # Use webformatURL for good quality
                image_url = hit.get("webformatURL", hit.get("largeImageURL"))
                width = hit.get("imageWidth", 0)
                height = hit.get("imageHeight", 0)
                title = hit.get("tags", query)
                
                if width >= MIN_RESOLUTION[0] and height >= MIN_RESOLUTION[1]:
                    score = 1.5 + hit.get("likes", 0) / 50  # Lower base score as backup
                    candidates.append(ImageCandidate(
                        url=image_url,
                        width=width,
                        height=height,
                        source="pixabay",
                        title=title,
                        quality_score=score
                    ))
            
            log.debug(f"Pixabay found {len(candidates)} candidates for '{query}'")
            return candidates
            
        except Exception as e:
            log.warning(f"Pixabay search failed for '{query}': {e}")
            return []

class ImageSearcher:
    """Manages multiple image providers and search strategies"""
    
    def __init__(self, config: Config):
        self.providers = []
        
        if config.unsplash_key:
            self.providers.append(UnsplashProvider(config.unsplash_key))
            log.info("✅ Unsplash provider initialized")
        
        if config.pexels_key:
            self.providers.append(PexelsProvider(config.pexels_key))
            log.info("✅ Pexels provider initialized")
        
        if config.pixabay_key:
            self.providers.append(PixabayProvider(config.pixabay_key))
            log.info("✅ Pixabay provider initialized")
        
        if not self.providers:
            raise ValueError("No image providers available - check API keys")
    
    def generate_search_terms(self, product_name: str) -> List[str]:
        """Generate intelligent search terms for a product"""
        name_lower = product_name.lower()
        terms = []
        
        # Direct translation
        for spanish, english in PLANT_TRANSLATIONS.items():
            if spanish in name_lower:
                terms.append(english)
                break
        
        # Plant genus recognition
        for genus in PLANT_GENERA:
            if genus in name_lower:
                terms.append(f"{genus} plant")
                terms.append(genus)
                break
        
        # Container/tool detection
        if any(word in name_lower for word in ["plastic", "matri", "jardinera", "maceta"]):
            terms.extend(["garden pot", "plant container", "flower pot"])
        
        # Fallback terms
        if not terms:
            if any(word in name_lower for word in ["l", "cm"]):  # Size indicators
                terms.extend(["potted plant", "garden plant", "houseplant"])
            else:
                terms.extend(["plant", "garden"])
        
        # Add specific combinations
        if "interior" in name_lower:
            terms = [f"indoor {term}" for term in terms] + terms
        elif "exterior" in name_lower:
            terms = [f"outdoor {term}" for term in terms] + terms
        
        return list(dict.fromkeys(terms))  # Remove duplicates while preserving order
    
    def search_best_image(self, product_name: str) -> Optional[ImageCandidate]:
        """Find the best image for a product across all providers"""
        search_terms = self.generate_search_terms(product_name)
        log.info(f"Searching for '{product_name}' with terms: {search_terms}")
        
        all_candidates = []
        
        for term in search_terms:
            for provider in self.providers:
                candidates = provider.search_images(term, count=3)
                all_candidates.extend(candidates)
                
                # If we have high-quality candidates, we can stop early
                if len([c for c in all_candidates if c.quality_score > 4.0]) >= 3:
                    break
        
        if not all_candidates:
            log.warning(f"No images found for '{product_name}'")
            return None
        
        # Sort by quality score and return best
        all_candidates.sort(key=lambda x: x.quality_score, reverse=True)
        best = all_candidates[0]
        
        log.info(f"Best image for '{product_name}': {best.source} (score: {best.quality_score:.1f})")
        return best

def download_and_process_image(candidate: ImageCandidate, output_path: str) -> bool:
    """Download and process image to specified path"""
    try:
        headers = {"User-Agent": USER_AGENT}
        response = requests.get(candidate.url, headers=headers, timeout=30)
        response.raise_for_status()
        
        # Save to temporary file first
        with tempfile.NamedTemporaryFile(delete=False, suffix='.jpg') as temp_file:
            temp_file.write(response.content)
            temp_path = temp_file.name
        
        # Process with PIL
        with Image.open(temp_path) as img:
            # Convert to RGB if needed
            if img.mode != 'RGB':
                img = img.convert('RGB')
            
            # Resize to target size
            img = img.resize(DEFAULT_IMAGE_SIZE, Image.Resampling.LANCZOS)
            
            # Save as high-quality JPEG
            img.save(output_path, 'JPEG', quality=DEFAULT_IMAGE_QUALITY, optimize=True)
        
        # Clean up temp file
        os.unlink(temp_path)
        
        log.debug(f"✅ Downloaded and processed: {candidate.url}")
        return True
        
    except Exception as e:
        log.error(f"❌ Failed to download {candidate.url}: {e}")
        return False

def load_config() -> Config:
    """Load configuration from environment"""
    load_dotenv()
    
    config = Config(
        wordpress_url=os.getenv("WORDPRESS_URL", ""),
        wp_username=os.getenv("WP_USERNAME", ""),
        wp_app_password=os.getenv("WP_APP_PASSWORD", ""),
        wc_key=os.getenv("WC_CONSUMER_KEY", ""),
        wc_secret=os.getenv("WC_CONSUMER_SECRET", ""),
        unsplash_key=os.getenv("UNSPLASH_API_KEY"),
        pixabay_key=os.getenv("PIXABAY_API_KEY"),
        pexels_key=os.getenv("PEXELS_API_KEY")
    )
    
    if not config.wordpress_url:
        raise ValueError("WORDPRESS_URL must be set")
    if not config.wp_username or not config.wp_app_password:
        raise ValueError("WP_USERNAME and WP_APP_PASSWORD must be set")
    
    return config

def load_state() -> Dict:
    """Load processing state from file"""
    if os.path.exists(STATE_FILE):
        try:
            with open(STATE_FILE, 'r') as f:
                return json.load(f)
        except Exception:
            pass
    return {"processed": [], "last_product_id": 0}

def save_state(state: Dict) -> None:
    """Save processing state to file"""
    try:
        with open(STATE_FILE, 'w') as f:
            json.dump(state, f, indent=2)
    except Exception as e:
        log.warning(f"Failed to save state: {e}")

def main():
    parser = argparse.ArgumentParser(description="Replace SVG placeholders with real original images")
    parser.add_argument("--batch-size", type=int, default=DEFAULT_BATCH_SIZE, help="Products to process per batch")
    parser.add_argument("--delay", type=float, default=DEFAULT_DELAY, help="Delay between requests (seconds)")
    parser.add_argument("--resume", action="store_true", help="Resume from last processed product")
    parser.add_argument("--dry-run", action="store_true", help="Test run without making changes")
    parser.add_argument("--log-level", choices=["DEBUG", "INFO", "WARNING", "ERROR"], default="INFO")
    parser.add_argument("--force", action="store_true", help="Replace even existing real images")
    
    args = parser.parse_args()
    
    # Setup logging
    setup_logging(getattr(logging, args.log_level))
    
    log.info("🌱 Los Cocos Real Image Automation Starting...")
    log.info(f"Batch size: {args.batch_size}, Delay: {args.delay}s")
    
    try:
        # Load configuration
        config = load_config()
        log.info(f"WordPress URL: {config.wordpress_url}")
        
        # Initialize clients
        wp_client = WordPressClient(config)
        image_searcher = ImageSearcher(config)
        
        # Load state for resume capability
        state = load_state() if args.resume else {"processed": [], "last_product_id": 0}
        
        # Get products needing real images
        log.info("Fetching products with SVG placeholder images...")
        products = wp_client.get_products_without_real_images(per_page=100)
        
        if not products:
            log.info("✅ All products already have real images!")
            return
        
        # Filter out already processed (if resuming)
        if args.resume:
            products = [p for p in products if p["id"] not in state["processed"]]
            log.info(f"Resuming: {len(products)} products remaining")
        
        log.info(f"Processing {len(products)} products...")
        
        # Process products in batches
        success_count = 0
        error_count = 0
        
        os.makedirs("temp_images", exist_ok=True)
        
        for i, product in enumerate(tqdm(products[:args.batch_size], desc="Processing products")):
            product_id = product["id"]
            product_name = product["name"]
            
            log.info(f"\n📦 Processing: {product_name} (ID: {product_id})")
            
            try:
                # Find best image
                best_image = image_searcher.search_best_image(product_name)
                if not best_image:
                    log.warning(f"⚠️ No suitable image found for '{product_name}'")
                    error_count += 1
                    continue
                
                if args.dry_run:
                    log.info(f"🔍 DRY RUN: Would use {best_image.source} image (score: {best_image.quality_score:.1f})")
                    continue
                
                # Download and process image
                temp_image_path = f"temp_images/product_{product_id}.jpg"
                if not download_and_process_image(best_image, temp_image_path):
                    error_count += 1
                    continue
                
                # Upload to WordPress
                media_id = wp_client.upload_image(
                    temp_image_path, 
                    product_name, 
                    f"Image of {product_name}"
                )
                
                if not media_id:
                    error_count += 1
                    continue
                
                # Set as product image
                if wp_client.set_product_image(product_id, media_id):
                    success_count += 1
                    state["processed"].append(product_id)
                    state["last_product_id"] = product_id
                    log.info(f"✅ Successfully updated '{product_name}' with real image")
                else:
                    error_count += 1
                
                # Clean up temp file
                try:
                    os.unlink(temp_image_path)
                except:
                    pass
                
            except Exception as e:
                log.error(f"❌ Error processing '{product_name}': {e}")
                error_count += 1
            
            # Save state periodically
            if (i + 1) % 5 == 0:
                save_state(state)
            
            # Rate limiting
            if args.delay > 0:
                time.sleep(args.delay)
        
        # Final state save
        save_state(state)
        
        log.info(f"\n🎉 Processing complete!")
        log.info(f"✅ Success: {success_count} products")
        log.info(f"❌ Errors: {error_count} products")
        
        if success_count > 0:
            log.info(f"🌟 {success_count} products now have real, original images!")
        
    except KeyboardInterrupt:
        log.info("⏹️ Processing interrupted by user")
        save_state(state)
    except Exception as e:
        log.error(f"💥 Fatal error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()