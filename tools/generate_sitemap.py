#!/usr/bin/env python3
"""Regenerate sitemap.xml from data/catalog.json (root + every project page).

Usage:
    python tools/generate_sitemap.py        # rewrite sitemap.xml
"""
from __future__ import annotations

import json
from datetime import datetime, timedelta, timezone
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parent.parent
PAGES_URL = "https://abolfazlrwm.github.io/awesome-scripts/"
TEHRAN_TZ = timezone(timedelta(hours=3, minutes=30))


def main() -> None:
    catalog = json.loads((REPO_ROOT / "data" / "catalog.json").read_text(encoding="utf-8"))
    today = datetime.now(TEHRAN_TZ).date().isoformat()
    urls = [(PAGES_URL, "1.0"), (PAGES_URL + "compare.html", "0.9"), (PAGES_URL + "search.html", "0.9"), (PAGES_URL + "stats.html", "0.8")]
    for project in sorted(catalog["projects"], key=lambda p: p["path"]):
        # only pages that actually serve: bots/desktop folders have no index.html
        if not (REPO_ROOT / project["path"] / "index.html").exists():
            continue
        urls.append((PAGES_URL + project["path"] + "/", "0.7"))
    lines = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
    ]
    for loc, priority in urls:
        lines.append(
            f"  <url><loc>{loc}</loc><lastmod>{today}</lastmod>"
            f"<changefreq>weekly</changefreq><priority>{priority}</priority></url>"
        )
    lines.append("</urlset>")
    (REPO_ROOT / "sitemap.xml").write_text("\n".join(lines) + "\n", encoding="utf-8")
    print(f"sitemap.xml: {len(urls)} URLs — root + servable pages (lastmod {today})")


if __name__ == "__main__":
    main()
