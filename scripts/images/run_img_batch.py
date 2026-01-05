import os, sys, json, subprocess, requests
BASE = "http://localhost:8082".rstrip("/")
USER = "admin"
PASS = "zRRE Twt3 b3TD mSRR NOJY ccSp"
auth = (USER, PASS)
print("[1/3] Resolviendo user_id ...", flush=True)
r = requests.get(BASE + "/wp-json/wp/v2/users/me", auth=auth, timeout=30)
print("users/me status:", r.status_code)
if r.status_code != 200:
    print(r.text[:1000])
    sys.exit(1)
uid = r.json().get(id)
if not uid:
    print("ERROR: user_id vacío. Respuesta:", r.text[:500])
    sys.exit(1)
print(f"OK user_id={uid}")
print("[2/3] Creando claves WooCommerce read_write ...", flush=True)
payload = {"description":"img-automation","user_id":uid,"permissions":"read_write"}
r2 = requests.post(BASE + "/wp-json/wc/v3/keys", auth=auth, json=payload, timeout=30)
print("wc/v3/keys status:", r2.status_code)
if r2.status_code not in (200, 201):
    print(r2.text[:1000])
    sys.exit(1)
keys = r2.json()
ck = keys.get("consumer_key") or keys.get("key")
cs = keys.get("consumer_secret") or keys.get("secret")
if not ck or not cs:
    print("ERROR: respuesta sin ck/cs:", json.dumps(keys)[:400])
    sys.exit(1)
print("OK claves generadas")
print("WC_CONSUMER_KEY=", ck)
print("WC_CONSUMER_SECRET=", cs)
print("[3/3] Ejecutando batch (5 items, 2s delay, dedupe global) ...", flush=True)
env = os.environ.copy()
env.update({
    "WORDPRESS_URL": BASE + "/",
    "WP_USERNAME": USER,
    "WP_APP_PASSWORD": PASS,
    "WC_CONSUMER_KEY": ck,
    "WC_CONSUMER_SECRET": cs,
    "UNSPLASH_API_KEY": "YjiiXP_kb4z7yhpBMrK3OWeWx1jf_VQrzl37VfFssLY",
    "PEXELS_API_KEY": "Ru0qn9ob5D5XqNyzacPTNoZiaTzfbmjRrHduXFisV4G97BADZ4EDBNiw",
})
cmd = [
    ".venv/bin/python", "wc_image_automation.py",
    "--target", "missing",
    "--providers", "inaturalist,unsplash,pexels",
    "--min-resolution", "1000x800",
    "--image-size", "1200x1200",
    "--quality", "85",
    "--batch-size", "5",
    "--delay", "2",
    "--enrich-queries",
    "--query-prefix", "vivero plantas jardin",
    "--assign-mode", "featured",
    "--global-dedupe",
    "--log-level", "INFO",
]
res = subprocess.run(cmd, env=env)
sys.exit(res.returncode)
