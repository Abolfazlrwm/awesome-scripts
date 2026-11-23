#!/usr/bin/env python3
"""Build the global code-search index consumed by search.html.

Produces (both gzip-compressed, decompressed in the browser via
DecompressionStream — keeps the repo lean):
  data/code-index.json.gz   — token -> [fileIdx, lineNo, ...] postings
  data/code-texts.json.gz   — full source text of every indexed file

Usage:
    python tools/generate_code_index.py
"""
from __future__ import annotations

import gzip
import json
import re
import time
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parent.parent
PROJECT_DIRS = [REPO_ROOT / d for d in ("bots", "web", "desktop")]
SKIP_DIRS = {"__pycache__", "node_modules", ".git", ".venv", "venv"}
CODE_EXTS = {".py", ".js", ".php", ".java", ".cs", ".cpp", ".cc", ".h", ".ps1", ".lua",
             ".html", ".css", ".sql", ".md", ".json", ".yml", ".yaml", ".txt"}
MAX_FILE_BYTES = 400_000
DF_CAP = 1500      # tokens with more postings are treated as stopwords
POST_CAP = 300     # keep at most this many (file,line) pairs per token
TOKEN_RE = re.compile(r"[A-Za-z_][A-Za-z0-9_]{2,}|[\u0600-\u06FF]{2,}")


def collect_files() -> list[tuple[str, str, str]]:
    files: list[tuple[str, str, str]] = []
    for base in PROJECT_DIRS:
        for path in sorted(base.rglob("*")):
            if not path.is_file():
                continue
            if any(part in SKIP_DIRS for part in path.parts):
                continue
            if path.suffix.lower() not in CODE_EXTS:
                continue
            try:
                if path.stat().st_size > MAX_FILE_BYTES:
                    continue
                text = path.read_text(encoding="utf-8", errors="replace")
            except OSError:
                continue
            if not text.strip():
                continue
            rel = path.relative_to(REPO_ROOT).as_posix()
            files.append((rel.split("/")[1], rel, text))
    return files


def main() -> None:
    started = time.time()
    files = collect_files()
    projects: list[str] = []
    proj_idx: dict[str, int] = {}
    meta_files: list[list] = []
    texts: list[str] = []
    postings: dict[str, list[int]] = {}
    total_lines = 0
    for slug, rel, text in files:
        if slug not in proj_idx:
            proj_idx[slug] = len(projects)
            projects.append(slug)
        fi = len(meta_files)
        meta_files.append([proj_idx[slug], rel.split("/", 1)[1]])
        texts.append(text)
        total_lines += text.count("\n") + 1
        for ln, line in enumerate(text.splitlines(), 1):
            for tok in {t.lower() for t in TOKEN_RE.findall(line)}:
                plist = postings.setdefault(tok, [])
                plist.append(fi)
                plist.append(ln)

    tokens = {tok: flat[: POST_CAP * 2]
              for tok, flat in postings.items() if len(flat) <= DF_CAP}
    index = {
        "v": 1,
        "projects": projects,
        "files": meta_files,
        "tokens": tokens,
        "meta": {
            "files": len(files),
            "lines": total_lines,
            "tokens": len(tokens),
            "dropped_stopwords": len(postings) - len(tokens),
            "caps": {"df": DF_CAP, "post": POST_CAP},
        },
    }
    idx_json = json.dumps(index, ensure_ascii=False, separators=(",", ":"))
    idx_gz = gzip.compress(idx_json.encode("utf-8"), 9)
    (REPO_ROOT / "data/code-index.json.gz").write_bytes(idx_gz)
    txt_json = json.dumps(texts, ensure_ascii=False, separators=(",", ":"))
    txt_gz = gzip.compress(txt_json.encode("utf-8"), 9)
    (REPO_ROOT / "data/code-texts.json.gz").write_bytes(txt_gz)
    print(f"✓ code index: {len(files)} files · {total_lines:,} lines · "
          f"{len(tokens):,} tokens ({len(postings) - len(tokens)} stopwords dropped) · "
          f"index {len(idx_gz) / 1024 / 1024:.2f} MB gz · texts {len(txt_gz) / 1024 / 1024:.2f} MB gz · "
          f"{time.time() - started:.1f}s")


if __name__ == "__main__":
    main()
