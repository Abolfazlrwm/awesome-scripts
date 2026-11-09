#!/usr/bin/env python3
"""Catalog generator for awesome-scripts.

Parses the bilingual project tables in the root README.md (the single
source of truth) and emits machine-readable catalog data used by the
interactive site and the CI validation pipeline:

    data/catalog.json   structured catalog (consumed by tools & CI)
    data/catalog.js     same data exposed as window.AWESOME_CATALOG
                        (consumed by the GitHub Pages site)

Usage:
    python tools/generate_catalog.py            # regenerate data files
    python tools/generate_catalog.py --check    # exit 1 if data is stale (CI)
    python tools/generate_catalog.py --stats    # print one-line summary

Everything is stdlib-only and deterministic: same README in, same data out.
"""

from __future__ import annotations

import argparse
import json
import re
import sys
from datetime import datetime, timedelta, timezone
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parent.parent
README_PATH = REPO_ROOT / "README.md"
DATA_DIR = REPO_ROOT / "data"
SRC_DIR = REPO_ROOT / "src"

TEHRAN_TZ = timezone(timedelta(hours=3, minutes=30))
REPO_URL = "https://github.com/Abolfazlrwm/awesome-scripts"
PAGES_URL = "https://abolfazlrwm.github.io/awesome-scripts/"

# Section heading -> category slug
EN_SECTIONS = {
    "Telegram bots": "telegram-bot",
    "Web tools": "web-tool",
    "Desktop tools": "desktop-tool",
}
FA_SECTIONS = {
    "ربات‌های تلگرام": "telegram-bot",
    "ابزارهای وب": "web-tool",
    "ابزارهای دسکتاپ": "desktop-tool",
}

CATEGORY_LABELS_EN = {
    "telegram-bot": "Telegram Bot",
    "web-tool": "Web Tool",
    "desktop-tool": "Desktop Tool",
}
CATEGORY_ICONS = {
    "telegram-bot": "🤖",
    "web-tool": "🌐",
    "desktop-tool": "🖥️",
}

# Human-friendly display names that generic title-casing would mangle
NAME_OVERRIDES = {
    "happydog-economy-bot": "HappyDog Economy Bot",
    "tic-tac-toe-game": "Tic-Tac-Toe Game",
    "pyautogui-automation-demo": "PyAutoGUI Automation Demo",
    "connect4-minesweeper-bot": "Connect4 & Minesweeper Bot",
    "premium-emoji-poster-cfworker": "Premium Emoji Poster (CF Worker)",
}
ACRONYMS = {"ai", "3d", "qr", "id", "vip", "rss", "gui", "vpn", "url", "php", "v2ray"}

# | [slug](./src/slug) | Lang | Desc | [demo](url)? |
EN_ROW_RE = re.compile(
    r"^\|\s*\[(?P<name>[^\]]+)\]\(\./src/(?P<slug>[A-Za-z0-9._-]+)\)\s*"
    r"\|\s*(?P<lang>[^|]+?)\s*\|\s*(?P<desc>.*?)(?:\s*\|\s*\[demo\]\((?P<demo>[^)]+)\))?\s*\|\s*$"
)
# | [FA name](./src/slug)( · [دمو زنده](url))? | Lang | Desc |
FA_ROW_RE = re.compile(
    r"^\|\s*\[(?P<name>[^\]]+)\]\(\./src/(?P<slug>[A-Za-z0-9._-]+)\)"
    r"(?:\s*·\s*\[[^\]]*\]\((?P<demo>[^)]+)\))?\s*"
    r"\|\s*(?P<lang>[^|]+?)\s*\|\s*(?P<desc>.+?)\s*\|\s*$"
)


ANY_HEADING_RE = re.compile(r"^#{2,4}\s+", re.M)


def extract_sections(text: str, headings: dict[str, str], level: int = 3) -> list[tuple[str, str, int]]:
    """Return (category, declared_count, section_body) triples for EN or FA tables.

    A section body ends at the very next markdown heading of any level,
    so EN tables never bleed into FA tables (and vice versa).
    """
    results: list[tuple[str, str, int]] = []
    heading_names = "|".join(re.escape(h) for h in headings)
    section_re = re.compile(
        rf"^#{{{level}}}\s+\S+\s+({heading_names})\s+(?:—\s*)?\(?(\d+)\)?\s*$",
        re.M,
    )
    for match in section_re.finditer(text):
        start = match.end()
        next_heading = ANY_HEADING_RE.search(text, start)
        end = next_heading.start() if next_heading else len(text)
        results.append((headings[match.group(1)], match.group(2), text[start:end]))
    return results


def parse_rows(body: str, pattern: re.Pattern[str]) -> list[dict[str, str]]:
    rows = []
    for line in body.splitlines():
        line = line.strip()
        if not line.startswith("| ["):
            continue
        match = pattern.match(line)
        if match:
            rows.append(match.groupdict())
    return rows


def prettify_slug(slug: str) -> str:
    if slug in NAME_OVERRIDES:
        return NAME_OVERRIDES[slug]
    words = []
    for word in slug.split("-"):
        words.append(word.upper() if word in ACRONYMS else word.capitalize())
    return " ".join(words)


def strip_leading_symbols(name: str) -> str:
    """Drop emoji/punctuation prefix from FA display names."""
    return re.sub(r"^[^\w\u0600-\u06FF\u200c]+", "", name).strip()


def build_catalog() -> dict:
    text = README_PATH.read_text(encoding="utf-8")

    projects: dict[str, dict] = {}

    # --- English tables: slug + language + description (+ demo URL) ---
    for category, expected, body in extract_sections(text, EN_SECTIONS):
        rows = parse_rows(body, EN_ROW_RE)
        if len(rows) != int(expected):
            raise SystemExit(
                f"ERROR: EN section '{category}' declares {expected} projects but table has {len(rows)}"
            )
        for row in rows:
            slug = row["slug"]
            if slug in projects:
                raise SystemExit(f"ERROR: duplicate project slug in EN table: {slug}")
            projects[slug] = {
                "slug": slug,
                "path": f"src/{slug}",
                "category": category,
                "language": row["lang"].strip(),
                "name_en": prettify_slug(slug),
                "description_en": row["desc"].strip(),
                "demo": row.get("demo") or None,
            }

    # --- Persian tables: FA names + FA descriptions ---
    fa_seen = set()
    for category, expected, body in extract_sections(text, FA_SECTIONS, level=4):
        rows = parse_rows(body, FA_ROW_RE)
        if len(rows) != int(expected):
            raise SystemExit(
                f"ERROR: FA section '{category}' declares {expected} projects but table has {len(rows)}"
            )
        for row in rows:
            slug = row["slug"]
            if slug not in projects:
                raise SystemExit(f"ERROR: FA table lists '{slug}' which is missing from EN table")
            if slug in fa_seen:
                raise SystemExit(f"ERROR: duplicate project slug in FA table: {slug}")
            fa_seen.add(slug)
            project = projects[slug]
            if project["category"] != category:
                raise SystemExit(f"ERROR: category mismatch for '{slug}' between EN and FA tables")
            project["name_fa"] = strip_leading_symbols(row["name"])
            project["description_fa"] = row["desc"].strip()

    missing_fa = sorted(set(projects) - fa_seen)
    if missing_fa:
        raise SystemExit(f"ERROR: projects missing from FA tables: {', '.join(missing_fa)}")

    # --- Cross-check against the filesystem ---
    fs_slugs = sorted(p.name for p in SRC_DIR.iterdir() if p.is_dir())
    table_slugs = sorted(projects)
    if fs_slugs != table_slugs:
        only_fs = sorted(set(fs_slugs) - set(table_slugs))
        only_table = sorted(set(table_slugs) - set(fs_slugs))
        raise SystemExit(
            "ERROR: src/ folders and README tables disagree.\n"
            f"  only on disk: {only_fs}\n  only in tables: {only_table}"
        )

    ordered = [projects[slug] for slug in table_slugs]
    languages: dict[str, int] = {}
    categories: dict[str, int] = {}
    for project in ordered:
        languages[project["language"]] = languages.get(project["language"], 0) + 1
        categories[project["category"]] = categories.get(project["category"], 0) + 1

    generated_at = datetime.now(TEHRAN_TZ).replace(microsecond=0).isoformat()
    return {
        "repo": "Abolfazlrwm/awesome-scripts",
        "repo_url": REPO_URL,
        "site_url": PAGES_URL,
        "generated_at": generated_at,
        "count": len(ordered),
        "stats": {"categories": categories, "languages": languages},
        "category_labels_en": CATEGORY_LABELS_EN,
        "category_icons": CATEGORY_ICONS,
        "projects": ordered,
    }


def render_js(catalog: dict) -> str:
    payload = json.dumps(catalog, ensure_ascii=False, indent=2)
    return (
        "// Auto-generated by tools/generate_catalog.py — do not edit manually.\n"
        "// Regenerate with: python tools/generate_catalog.py\n"
        f"window.AWESOME_CATALOG = {payload};\n"
    )


def main() -> None:
    parser = argparse.ArgumentParser(description="Generate site catalog data from README.md")
    parser.add_argument("--check", action="store_true", help="verify committed data is up to date")
    parser.add_argument("--stats", action="store_true", help="print a one-line summary")
    args = parser.parse_args()

    catalog = build_catalog()

    if args.stats:
        stats = catalog["stats"]
        cats = " · ".join(
            f"{CATEGORY_LABELS_EN[key]}: {value}" for key, value in stats["categories"].items()
        )
        print(
            f"Projects: {catalog['count']} ({cats}) · "
            f"Languages: {len(stats['languages'])} · License: MIT"
        )
        return

    json_bytes = (json.dumps(catalog, ensure_ascii=False, indent=2) + "\n").encode("utf-8")
    js_bytes = render_js(catalog).encode("utf-8")

    if args.check:
        problems = []
        json_path = DATA_DIR / "catalog.json"
        js_path = DATA_DIR / "catalog.js"
        if not json_path.exists():
            problems.append("data/catalog.json is missing")
        elif json_path.read_bytes() != json_bytes:
            # tolerate timestamp drift, but nothing else may differ
            old = json.loads(json_path.read_text(encoding="utf-8"))
            new = json.loads(json_bytes.decode("utf-8"))
            old.pop("generated_at", None)
            new.pop("generated_at", None)
            if old != new:
                problems.append("data/catalog.json is stale — README changed? re-run the generator")
        if not js_path.exists():
            problems.append("data/catalog.js is missing")
        elif js_path.read_bytes() != js_bytes:
            problems.append("data/catalog.js is stale — re-run the generator")
        if problems:
            for problem in problems:
                print(f"✗ {problem}", file=sys.stderr)
            raise SystemExit(1)
        print(f"✓ catalog data is up to date ({catalog['count']} projects)")
        return

    DATA_DIR.mkdir(exist_ok=True)
    (DATA_DIR / "catalog.json").write_bytes(json_bytes)
    (DATA_DIR / "catalog.js").write_bytes(js_bytes)

    stats = catalog["stats"]
    print(f"✓ wrote data/catalog.json + data/catalog.js ({catalog['count']} projects)")
    for key, value in stats["categories"].items():
        print(f"  · {CATEGORY_LABELS_EN[key]}: {value}")
    print(f"  · languages: {', '.join(sorted(stats['languages']))}")


if __name__ == "__main__":
    main()
