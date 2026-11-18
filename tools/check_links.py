#!/usr/bin/env python3
"""Link health check: probe every live-demo URL in data/catalog.json.

Used by the weekly Link Check workflow. Writes a markdown table to
GITHUB_STEP_SUMMARY when run in CI. Exits 0 even when a link is down —
external demos flake; the summary is the deliverable, not a red build.
"""
import json
import os
import sys
import urllib.request
import concurrent.futures
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
UA = {"User-Agent": "awesome-scripts-link-check/1.0"}


def probe(url):
    try:
        req = urllib.request.Request(url, method="HEAD", headers=UA)
        with urllib.request.urlopen(req, timeout=15) as r:
            return url, r.status
    except Exception:
        try:  # some hosts reject HEAD — retry with GET
            req = urllib.request.Request(url, headers=UA)
            with urllib.request.urlopen(req, timeout=15) as r:
                return url, r.status
        except Exception as e:
            return url, f"ERR: {type(e).__name__}"


def main():
    cat = json.loads((ROOT / "data" / "catalog.json").read_text(encoding="utf-8"))
    urls = sorted({p["demo"] for p in cat["projects"] if p.get("demo")})
    with concurrent.futures.ThreadPoolExecutor(max_workers=8) as ex:
        results = list(ex.map(probe, urls))

    bad = [(u, s) for u, s in results if s != 200]
    print(f"checked {len(urls)} demo links — {len(bad)} unreachable")
    for u, s in bad:
        print(f"  WARN {s} {u}")

    summary = os.environ.get("GITHUB_STEP_SUMMARY")
    if summary:
        rows = "\n".join(f"| {u} | {s} |" for u, s in results)
        Path(summary).write_text(
            f"## 🔗 Link check — {len(urls)} demo URLs\n\n"
            f"{'⚠️ unreachable: ' + str(len(bad)) if bad else '✅ all healthy'}\n\n| URL | status |\n| --- | --- |\n{rows}\n",
            encoding="utf-8",
        )
    return 0


if __name__ == "__main__":
    sys.exit(main())
