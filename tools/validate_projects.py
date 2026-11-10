#!/usr/bin/env python3
"""Project validator for awesome-scripts.

Quality gate for every project folder under src/:

  1. every folder must ship a real README (heading + setup content)
  2. every folder must contain runnable code or a web entry point
  3. no secrets may be committed — anywhere in the repository
     (Telegram bot tokens, GitHub tokens, AWS keys, generic key/value
      literals with high-entropy values)

Also scans repo-level folders (tools/, data/, .github/, root files).

Usage:
    python tools/validate_projects.py              # full scan, exit 1 on failure
    python tools/validate_projects.py --quiet      # only print failures + summary

Exit codes: 0 = all green, 1 = one or more problems found.
"""

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parent.parent
SRC_DIR = REPO_ROOT / "src"

CODE_EXTS = {".py", ".js", ".php", ".java", ".cs", ".cpp", ".cc", ".h", ".ps1", ".html"}
SCAN_EXTS = CODE_EXTS | {".json", ".yml", ".yaml", ".txt", ".md", ".css", ".xml", ".sh", ".env", ".example", ".cfg", ".ini"}
SCAN_NAME_EXTRA = {".env", ".env.example", ".gitignore", "string.json", "update.json"}

MAX_SCAN_BYTES = 1_000_000

SECRET_RULES: list[tuple[str, re.Pattern[str]]] = [
    ("Telegram bot token", re.compile(r"\b\d{8,10}:[A-Za-z0-9_-]{30,}\b")),
    ("GitHub token", re.compile(r"\bgh[pousr]_[A-Za-z0-9]{20,}\b")),
    ("AWS access key", re.compile(r"\bAKIA[0-9A-Z]{16}\b")),
    ("OpenAI-style key", re.compile(r"\bsk-[A-Za-z0-9_-]{20,}\b")),
    ("Private key block", re.compile(r"-----BEGIN [A-Z ]*PRIVATE KEY-----")),
    (
        "Hard-coded credential literal",
        re.compile(
            r"(?:api[_-]?key|secret|passwd|password|token)\s*[:=]\s*['\"][A-Za-z0-9/+_-]{24,}['\"]",
            re.I,
        ),
    ),
]

# Values that look like secrets but are documented placeholders
PLACEHOLDER_HINTS = ("your", "xxx", "example", "placeholder", "here", "<", ">", "abcd", "changeme", "1234")


def is_binary(path: Path) -> bool:
    try:
        return b"\x00" in path.read_bytes()[:8000]
    except OSError:
        return True


def looks_like_placeholder(match_text: str) -> bool:
    low = match_text.lower()
    return any(hint in low for hint in PLACEHOLDER_HINTS)


def scan_text(path: Path) -> list[str]:
    """Return list of 'rule: evidence' findings for one text file."""
    try:
        text = path.read_text(encoding="utf-8-sig", errors="replace")
    except OSError:
        return []
    findings: list[str] = []
    for rule, pattern in SECRET_RULES:
        for match in pattern.finditer(text):
            evidence = match.group(0)
            if looks_like_placeholder(evidence):
                continue
            # keep evidence short so logs stay clean
            findings.append(f"{rule}: {evidence[:18]}…")
    return findings


def files_to_scan(root: Path) -> list[Path]:
    out: list[Path] = []
    for path in root.rglob("*"):
        if not path.is_file():
            continue
        if any(part in {".git", "node_modules", "__pycache__", ".venv", "vendor"} for part in path.parts):
            continue
        if path.suffix.lower() in SCAN_EXTS or path.name.lower() in SCAN_NAME_EXTRA:
            if path.stat().st_size <= MAX_SCAN_BYTES and not is_binary(path):
                out.append(path)
    return out


def check_project(folder: Path) -> list[str]:
    problems: list[str] = []

    readme = folder / "README.md"
    if not readme.exists():
        problems.append("missing README.md")
    else:
        # utf-8-sig: tolerate files saved with a UTF-8 BOM before the heading
        content = readme.read_text(encoding="utf-8-sig", errors="replace")
        if not re.search(r"^#\s+\S", content, re.M):
            problems.append("README has no top-level heading")
        if len(content.strip()) < 200:
            problems.append("README is too short to be a setup guide (<200 chars)")

    code_files = [p for p in folder.rglob("*") if p.is_file() and p.suffix.lower() in CODE_EXTS]
    if not code_files:
        problems.append("no runnable code or web entry point found")

    for path in files_to_scan(folder):
        rel = path.relative_to(folder).as_posix()
        for finding in scan_text(path):
            problems.append(f"secret in {rel} — {finding}")

    return problems


def main() -> None:
    parser = argparse.ArgumentParser(description="Validate src/ project folders and secrets hygiene")
    parser.add_argument("--quiet", action="store_true", help="only print failures and the summary")
    args = parser.parse_args()

    if not SRC_DIR.exists():
        print("✗ src/ directory not found", file=sys.stderr)
        raise SystemExit(1)

    folders = sorted(p for p in SRC_DIR.iterdir() if p.is_dir())
    failed = 0

    for folder in folders:
        problems = check_project(folder)
        if problems:
            failed += 1
            print(f"✗ {folder.name}")
            for problem in problems:
                print(f"    - {problem}")
        elif not args.quiet:
            print(f"✓ {folder.name}")

    # repo-level scan (tools, data, workflows, root files)
    repo_findings: list[str] = []
    for path in files_to_scan(REPO_ROOT):
        if path.parts[0] == "src":
            continue  # already covered per-project
        for finding in scan_text(path):
            repo_findings.append(f"{path.relative_to(REPO_ROOT).as_posix()} — {finding}")
    if repo_findings:
        failed += 1
        print("✗ repo-level secret scan")
        for finding in repo_findings:
            print(f"    - {finding}")
    elif not args.quiet:
        print("✓ repo-level secret scan (tools, data, workflows, root)")

    total = len(folders)
    print()
    if failed:
        print(f"✗ {failed} problem area(s) — {total - failed}/{total} projects clean")
        raise SystemExit(1)
    print(f"✓ {total}/{total} projects clean · no secrets committed")


if __name__ == "__main__":
    main()
