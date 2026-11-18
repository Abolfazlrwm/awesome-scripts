#!/usr/bin/env python3
"""Project scaffolder for awesome-scripts.

Creates a ready-to-fill project folder under bots|web|desktop/<slug>/ following the
repository conventions (README structure, requirements file, attribution
header, catalog-ready description), so contributing project #75+ stays
consistent with the existing 74.

Usage:
    python tools/new_project.py my-new-bot --lang python   # Python bot/app
    python tools/new_project.py my-web-app --lang php      # PHP bot/web
    python tools/new_project.py my-tool     --lang node    # Node.js
    python tools/new_project.py my-site     --lang web     # static HTML/CSS/JS

The script never touches the README tables or the catalog — those are
generated from README.md by tools/generate_catalog.py. It prints the exact
next steps (where to register the project and which checks to run).

Everything is stdlib-only and safe to re-run (it refuses to overwrite an
existing folder).
"""

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parent.parent
LANG_DIR = {"python": "bots", "php": "bots", "node": "bots", "web": "web"}
CONTACT = "Telegram: @Abolfazl_rwm"
TELEGRAM_LINK = "https://t.me/Abolfazl_rwm"

LANGS = {
    "python": {
        "label": "Python",
        "entry": "bot.py",
        "entry_stub": '''#!/usr/bin/env python3
# {contact}
"""<one-line English description of the project>."""

def main() -> None:
    raise NotImplementedError("fill in the bot logic here")


if __name__ == "__main__":
    main()
''',
        "deps": "# add third-party dependencies here, one per line\n# e.g.  python-telegram-bot>=21.0\n# leave this file with only comments if the project is stdlib-only\n",
    },
    "php": {
        "label": "PHP",
        "entry": "bot.php",
        "entry_stub": '''<?php
// {contact}
/** <one-line English description of the project>. */

// TODO: fill in the webhook / CLI logic here.
''',
        "deps": None,  # composer.json is optional; PHP projects ship without requirements
    },
    "node": {
        "label": "Node.js",
        "entry": "index.js",
        "entry_stub": '''// {contact}
// <one-line English description of the project>

// TODO: fill in the main logic here.
console.log("hello from the scaffold");
''',
        "deps": '{\n  "name": "<slug>",\n  "version": "1.0.0",\n  "private": true,\n  "dependencies": {}\n}\n',
    },
    "web": {
        "label": "HTML/CSS/JS",
        "entry": "index.html",
        "entry_stub": '''<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- {contact} -->
<title><Title></title>
</head>
<body>
  <h1><Title></h1>
  <p>TODO: build the page.</p>
</body>
</html>
''',
        "deps": None,
    },
}

SLUG_RE = re.compile(r"^[a-z0-9][a-z0-9-]*$")


def readme_template(slug: str, lang_label: str, demo_hint: str, dir: str) -> str:
    return f'''# <Project Title>

<!-- {CONTACT} -->

<p align="center"><img src="docs/preview.png" alt="preview" width="60%"></p>

**<one-line English description>**

<یک خط توضیح فارسی>

## ✨ Features

- feature one
- feature two

## 🚀 Setup — نصب و اجرا

```bash
cd {dir}/{slug}
# {demo_hint}
```

## ⚙️ Configuration — تنظیمات

| Variable | File | Description |
| --- | --- | --- |
| `TOKEN` | code | bot token from [@BotFather](https://t.me/BotFather) |

## 📬 Contact — تماس

[{TELEGRAM_LINK}]({TELEGRAM_LINK}) — Abolfazl
'''


def main() -> int:
    ap = argparse.ArgumentParser(description="scaffold a new project folder")
    ap.add_argument("slug", help="kebab-case folder name, e.g. my-new-bot")
    ap.add_argument("--lang", choices=sorted(LANGS), default="python", help="project language (default: python)")
    ap.add_argument("--title", help="display title (defaults to the slug)")
    args = ap.parse_args()

    if not SLUG_RE.match(args.slug) or "-" not in args.slug:
        print(f"error: slug must be kebab-case (letters/digits/hyphen), got: {args.slug!r}", file=sys.stderr)
        return 1
    dest = REPO_ROOT / LANG_DIR[args.lang] / args.slug
    if dest.exists():
        print(f"error: {dest} already exists — refusing to overwrite", file=sys.stderr)
        return 1

    lang = LANGS[args.lang]
    title = args.title or args.slug.replace("-", " ").title()

    dest.mkdir(parents=True)
    (dest / "README.md").write_text(
        readme_template(args.slug, lang["label"], f"python {lang['entry']}" if args.lang == "python" else f"open {lang['entry']}", LANG_DIR[args.lang]),
        encoding="utf-8",
    )
    (dest / lang["entry"]).write_text(lang["entry_stub"].format(contact=CONTACT), encoding="utf-8")
    if lang["deps"] is not None:
        fname = "package.json" if args.lang == "node" else "requirements.txt"
        (dest / fname).write_text(lang["deps"], encoding="utf-8")
    (dest / "docs").mkdir()
    print(f"created {dest.relative_to(REPO_ROOT)} ({lang['label']})")
    print("""
Next steps — قدم‌های بعدی:
  1. fill in README.md (EN + FA), the entry file, and drop a preview at docs/preview.png
  2. add a row to the matching table in the root README.md (EN + FA sections)
  3. run:  python tools/generate_catalog.py          # refresh data/catalog.{json,js}
  4. run:  python tools/validate_projects.py         # must print all green
  5. commit:  feat(slug): add <short description>""")
    return 0


if __name__ == "__main__":
    sys.exit(main())
