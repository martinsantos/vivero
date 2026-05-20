#!/usr/bin/env python3
"""Update WordPress attachment alt text from the Catalog Visual Premium audit.

This script is intentionally narrow: it only updates attachment alt text for
featured images that are missing/weak and are not shared by multiple products.
It does not upload, delete, or reassign images.
"""

from __future__ import annotations

import argparse
import base64
import json
import os
import sys
from pathlib import Path
from typing import Any, Dict, List, Set

import requests

try:
    from dotenv import load_dotenv
except Exception:  # pragma: no cover
    load_dotenv = None


ROOT = Path(__file__).resolve().parents[2]
DEFAULT_AUDIT = ROOT / "logs" / "catalog-image-goal-audit.json"


def load_env() -> None:
    if load_dotenv:
        load_dotenv(ROOT / ".env")


def auth_header() -> Dict[str, str]:
    user = os.getenv("WP_USERNAME", "")
    password = os.getenv("WP_APP_PASSWORD", "")
    if not user or not password:
        raise RuntimeError("Missing WP_USERNAME or WP_APP_PASSWORD")
    token = base64.b64encode(f"{user}:{password}".encode("utf-8")).decode("ascii")
    return {"Authorization": f"Basic {token}"}


def clean_text(value: str) -> str:
    return " ".join(str(value or "").replace("&#215;", "x").split())


def build_alt(finding: Dict[str, Any]) -> str:
    name = clean_text(finding.get("name", ""))
    kind = finding.get("product_kind", "")
    if kind == "plant_species":
        suffix = "planta de vivero"
    elif kind == "packaged_input":
        suffix = "insumo para jardin y vivero"
    elif kind == "pot_or_accessory":
        suffix = "maceta o accesorio de vivero"
    else:
        suffix = "producto de vivero"
    return f"{name} - {suffix} en Vivero Los Cocos"[:180]


def shared_media_ids(audit: Dict[str, Any]) -> Set[int]:
    duplicate_groups = audit.get("duplicates", {}).get("duplicate_media_ids", {})
    ids: Set[int] = set()
    for media_id in duplicate_groups:
        try:
            ids.add(int(media_id))
        except ValueError:
            continue
    return ids


def candidates(audit: Dict[str, Any]) -> List[Dict[str, Any]]:
    shared = shared_media_ids(audit)
    rows = []
    seen_media: Set[int] = set()
    for finding in audit.get("findings", []):
        media_id = finding.get("image_id")
        if not media_id:
            continue
        media_id = int(media_id)
        if media_id in shared or media_id in seen_media:
            continue
        flags = set(finding.get("flags") or [])
        if not ({"missing_alt", "weak_alt"} & flags):
            continue
        seen_media.add(media_id)
        rows.append({
            "product_id": finding.get("product_id"),
            "media_id": media_id,
            "name": finding.get("name", ""),
            "old_alt": finding.get("image_alt", ""),
            "new_alt": build_alt(finding),
        })
    return rows


def update_alt(base_url: str, headers: Dict[str, str], media_id: int, alt_text: str) -> None:
    url = f"{base_url.rstrip('/')}/wp-json/wp/v2/media/{media_id}"
    response = requests.post(url, headers={**headers, "Content-Type": "application/json"}, json={"alt_text": alt_text}, timeout=30)
    if response.status_code not in (200, 201):
        raise RuntimeError(f"media {media_id}: HTTP {response.status_code} {response.text[:180]}")


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Update missing product image alt text from image audit.")
    parser.add_argument("--audit", default=str(DEFAULT_AUDIT))
    parser.add_argument("--apply", action="store_true", help="Actually update WordPress. Without this, dry-run only.")
    parser.add_argument("--limit", type=int, default=0, help="Limit updates for a small batch. 0 means all candidates.")
    parser.add_argument("--output", default="", help="Write full candidate list to JSON for WP-CLI/SSH workflows.")
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    load_env()
    base_url = os.getenv("WORDPRESS_URL", "").strip()
    if not base_url:
        print("Missing WORDPRESS_URL", file=sys.stderr)
        return 2

    audit = json.loads(Path(args.audit).read_text(encoding="utf-8"))
    rows = candidates(audit)
    if args.limit:
        rows = rows[: args.limit]

    if args.output:
        Path(args.output).parent.mkdir(parents=True, exist_ok=True)
        Path(args.output).write_text(json.dumps(rows, ensure_ascii=False, indent=2), encoding="utf-8")

    print(json.dumps({
        "mode": "apply" if args.apply else "dry-run",
        "candidates": len(rows),
        "first_items": rows[:10],
    }, ensure_ascii=False, indent=2))

    if not args.apply:
        return 0

    headers = auth_header()
    updated = 0
    failed = 0
    for row in rows:
        try:
            update_alt(base_url, headers, int(row["media_id"]), row["new_alt"])
            updated += 1
        except Exception as exc:
            failed += 1
            print(f"WARNING {row['media_id']}: {exc}", file=sys.stderr)
    print(json.dumps({"updated": updated, "failed": failed}, ensure_ascii=False))
    return 1 if failed else 0


if __name__ == "__main__":
    raise SystemExit(main())
