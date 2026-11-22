#!/usr/bin/env python3
"""Build the release-history dataset consumed by stats.html.

For every git tag (oldest → newest, plus a final "main" snapshot) this
streams `git archive` in-memory and measures:
  - projects: bots|web|desktop folders shipping a README.md
  - files / loc: tracked source files and their line counts
plus current language distribution and monthly commit activity.

Output: data/stats-history.js  →  window.AWESOME_STATS_HISTORY

Usage: python tools/generate_stats_history.py
"""
from __future__ import annotations

import datetime as dt
import json
import re
import subprocess
import tarfile
from collections import Counter
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parent.parent
PROJECT_DIRS = ("bots", "web", "desktop")
# older releases kept every project under src/ — include it so the whole
# history is measurable with one rule
HISTORY_ROOTS = ("bots", "web", "desktop", "src")
CODE_EXTS = {".py", ".js", ".php", ".java", ".cs", ".cpp", ".cc", ".h", ".ps1",
             ".html", ".css", ".sql"}
SKIP_DIRS = {"__pycache__", "node_modules", ".git"}
LANG_OF = {".py": "Python", ".js": "JavaScript", ".html": "HTML", ".css": "CSS",
           ".php": "PHP", ".cs": "C#", ".cpp": "C/C++", ".cc": "C/C++",
           ".h": "C/C++", ".ps1": "PowerShell", ".java": "Java", ".sql": "SQL"}
MONTHS_BACK = 18


def run(*args: str) -> bytes:
    return subprocess.run(args, capture_output=True, check=True,
                          cwd=REPO_ROOT).stdout


def git_tags() -> list[tuple[str, str]]:
    """(tag, iso-date) oldest first, natural version order."""
    out = run("git", "for-each-ref", "refs/tags",
              "--format=%(refname:short)|%(creatordate:iso-strict)",
              "--sort=version:refname").decode().strip().splitlines()
    tags = []
    for line in out:
        tag, date = line.split("|", 1)
        if re.fullmatch(r"v\d+\.\d+\.\d+", tag):
            tags.append((tag, date))
    return tags


def catalog_projects(tag: str) -> int | None:
    """Project count from the tag's own data/catalog.json (source of truth)."""
    r = subprocess.run(["git", "show", f"{tag}:data/catalog.json"],
                       capture_output=True, cwd=REPO_ROOT)
    if r.returncode != 0:
        return None
    try:
        return len(json.loads(r.stdout.decode("utf-8"))["projects"])
    except (ValueError, KeyError, TypeError):
        return None


def measure_archive(tag: str) -> dict:
    """Stream one tag's tarball and measure without touching the disk."""
    proc = subprocess.Popen(["git", "archive", "--format=tar", tag],
                            stdout=subprocess.PIPE, stderr=subprocess.DEVNULL,
                            cwd=REPO_ROOT)
    files = loc = 0
    readme_slugs = set()
    tf = tarfile.open(fileobj=proc.stdout, mode="r|")
    for member in tf:
        if not member.isfile():
            continue
        parts = member.name.split("/")
        if parts[0] not in HISTORY_ROOTS or any(p in SKIP_DIRS for p in parts):
            continue
        ext = Path(member.name).suffix.lower()
        if ext == ".md" and len(parts) == 3:
            readme_slugs.add((parts[0], parts[1]))
            continue
        if ext not in CODE_EXTS:
            continue
        files += 1
        fh = tf.extractfile(member)
        if fh:
            loc += fh.read().decode("utf-8", "replace").count("\n") + 1
    proc.wait()
    return {"tag": tag,
            "projects": catalog_projects(tag) or len(readme_slugs),
            "files": files, "loc": loc}


def measure_worktree() -> dict:
    files = loc = 0
    slugs = set()
    for base in HISTORY_ROOTS:
        for path in (REPO_ROOT / base).rglob("*"):
            if not path.is_file():
                continue
            rel = path.relative_to(REPO_ROOT).parts
            if any(p in SKIP_DIRS for p in rel):
                continue
            ext = path.suffix.lower()
            if ext == ".md" and len(rel) == 3:
                slugs.add((rel[0], rel[1]))
                continue
            if ext not in CODE_EXTS:
                continue
            files += 1
            loc += path.read_text(encoding="utf-8",
                                  errors="replace").count("\n") + 1
    cat = REPO_ROOT / "data/catalog.json"
    projects = len(json.loads(cat.read_text(encoding="utf-8"))["projects"]) \
        if cat.exists() else len(slugs)
    return {"tag": "main", "projects": projects, "files": files, "loc": loc}


def language_distribution() -> dict[str, int]:
    langs: Counter[str] = Counter()
    for base in PROJECT_DIRS:
        for path in (REPO_ROOT / base).rglob("*"):
            if not path.is_file():
                continue
            if any(p in SKIP_DIRS for p in path.relative_to(REPO_ROOT).parts):
                continue
            lang = LANG_OF.get(path.suffix.lower())
            if lang:
                langs[lang] += path.read_text(
                    encoding="utf-8", errors="replace").count("\n") + 1
    return dict(langs.most_common())


def commit_activity() -> list[dict]:
    dates = run("git", "log", "--format=%ad",
                "--date=format:%Y-%m").decode().strip().splitlines()
    counts = Counter(dates)
    now = dt.date.today().replace(day=1)
    months = []
    for i in range(MONTHS_BACK - 1, -1, -1):
        m = now - dt.timedelta(days=30 * i + now.day - 1)
        ym = m.strftime("%Y-%m")
        months.append({"ym": ym, "count": counts.get(ym, 0)})
    return months


def main() -> None:
    releases = []
    for tag, date in git_tags():
        entry = measure_archive(tag)
        entry["date"] = date
        releases.append(entry)
        print(f"  {tag:<10} projects={entry['projects']:<3} files={entry['files']:<4} loc={entry['loc']:,}")
    today = measure_worktree()
    today["date"] = dt.datetime.now().astimezone().isoformat(timespec="seconds")
    releases.append(today)
    data = {
        "v": 1,
        "generated": today["date"],
        "releases": releases,
        "langs": language_distribution(),
        "months": commit_activity(),
    }
    js = json.dumps(data, ensure_ascii=False, separators=(",", ":"))
    (REPO_ROOT / "data/stats-history.js").write_text(
        "// Auto-generated by tools/generate_stats_history.py — do not edit.\n"
        "// Regenerate with: python tools/generate_stats_history.py\n"
        f"window.AWESOME_STATS_HISTORY = {js};\n",
        encoding="utf-8")
    print(f"✓ stats-history: {len(releases)} points · langs={sum(data['langs'].values()):,} LOC · "
          f"{len(data['months'])} months · {len(js) / 1024:.0f} KB")


if __name__ == "__main__":
    main()
