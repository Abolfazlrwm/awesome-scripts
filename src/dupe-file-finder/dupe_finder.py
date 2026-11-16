#!/usr/bin/env python3
# Telegram: @Abolfazl_rwm
"""Dupe Finder — duplicate file detection CLI.

Find duplicate files by content: files are grouped by size first, then
hashed (partial hash for big files, full hash only when sizes match) so
big folders stay fast. Shows duplicate groups with wasted space, and
can optionally delete extras safely — dry-run is the default, the
shortest path in each group is kept, and --apply writes a deletion log
you can review.

Stdlib only (hashlib, argparse, pathlib, csv).
"""

from __future__ import annotations

import argparse
import csv
import hashlib
import os
from collections import defaultdict
from pathlib import Path

CHUNK = 1024 * 1024  # 1 MiB
PARTIAL_BYTES = 64 * 1024  # bytes hashed in the first pass


def iter_files(root: Path, exts: set[str] | None, min_size: int, depth: int) -> list[Path]:
    out: list[Path] = []
    seen_dirs: set[Path] = set()
    for level in range(depth):
        for p in root.glob("*/" * level + "*"):
            if p.is_dir() and p not in seen_dirs:
                continue
            if not p.is_file():
                continue
            try:
                if p.stat().st_size < min_size:
                    continue
                if exts and p.suffix.lower().lstrip(".") not in exts:
                    continue
            except OSError:
                continue
            out.append(p)
    # include files directly in the root when depth >= 1
    if depth >= 1:
        for p in root.iterdir():
            if p.is_file():
                try:
                    if p.stat().st_size >= min_size and (
                        not exts or p.suffix.lower().lstrip(".") in exts
                    ):
                        out.append(p)
                except OSError:
                    continue
    return sorted(set(out))


def partial_hash(path: Path) -> str:
    h = hashlib.md5()
    with open(path, "rb") as f:
        h.update(f.read(PARTIAL_BYTES))
    return h.hexdigest()


def full_hash(path: Path, algo: str) -> str:
    h = hashlib.new(algo)
    with open(path, "rb") as f:
        for block in iter(lambda: f.read(CHUNK), b""):
            h.update(block)
    return h.hexdigest()


def find_duplicates(files: list[Path], algo: str) -> list[list[Path]]:
    """3-phase pipeline: size → partial hash → full hash."""
    by_size: dict[int, list[Path]] = defaultdict(list)
    for f in files:
        by_size[f.stat().st_size].append(f)

    by_partial: dict[tuple[int, str], list[Path]] = defaultdict(list)
    for size, group in by_size.items():
        if len(group) < 2:
            continue
        for f in group:
            by_partial[(size, partial_hash(f))].append(f)

    groups: list[list[Path]] = []
    for (_size, _ph), group in by_partial.items():
        if len(group) < 2:
            continue
        by_full: dict[str, list[Path]] = defaultdict(list)
        for f in group:
            by_full[full_hash(f, algo)].append(f)
        for exact in by_full.values():
            if len(exact) >= 2:
                groups.append(sorted(exact, key=lambda p: (len(str(p)), str(p))))
    groups.sort(key=lambda g: -g[0].stat().st_size)
    return groups


def plan_deletions(groups: list[list[Path]]) -> list[Path]:
    """Keep the shortest path in every group (likely the original); mark the rest."""
    doomed: list[Path] = []
    for g in groups:
        doomed.extend(g[1:])
    return doomed


def human(nbytes: int) -> str:
    for unit in ("B", "KB", "MB", "GB", "TB"):
        if nbytes < 1024:
            return f"{nbytes:.1f} {unit}" if unit != "B" else f"{nbytes} B"
        nbytes /= 1024
    return f"{nbytes:.1f} PB"


def main(argv: list[str] | None = None) -> None:
    ap = argparse.ArgumentParser(
        description="find duplicate files by content — dry-run by default, CSV export, safe delete",
        formatter_class=argparse.ArgumentDefaultsHelpFormatter,
    )
    ap.add_argument("dir", nargs="?", default=".", help="folder to scan (default: current)")
    ap.add_argument("--ext", nargs="+", help="only scan these extensions (e.g. --ext jpg mp4)")
    ap.add_argument("--min-size", type=int, default=1024, help="ignore files smaller than N bytes")
    ap.add_argument("--depth", type=int, default=3, help="how many levels deep to scan")
    ap.add_argument("--algo", default="sha256", choices=["md5", "sha1", "sha256"], help="final hash algorithm")
    ap.add_argument("--apply", action="store_true", help="delete duplicates (default: preview only)")
    ap.add_argument("--csv", help="write a CSV report of duplicate groups")
    args = ap.parse_args(argv)

    root = Path(args.dir)
    if not root.is_dir():
        raise SystemExit(f"error: {root} is not a directory")

    exts = {e.lower().lstrip(".") for e in args.ext} if args.ext else None
    files = iter_files(root, exts, args.min_size, args.depth)
    print(f"🔎 scanning {len(files)} file(s) in {root} …")

    groups = find_duplicates(files, args.algo)
    if not groups:
        print("✅ no duplicates found")
        return

    wasted = sum(sum(p.stat().st_size for p in g[1:]) for g in groups)
    print(f"\n🧬 {len(groups)} duplicate group(s) — wasted space: {human(wasted)}\n")
    doomed = plan_deletions(groups)
    for i, g in enumerate(groups, 1):
        size = human(g[0].stat().st_size)
        print(f"[{i}] {size} × {len(g)}")
        for j, p in enumerate(g):
            mark = "  keep " if j == 0 else "  ❌ delete"
            print(f"{mark}  {p}")
        print()

    if args.csv:
        with open(args.csv, "w", newline="", encoding="utf-8") as f:
            w = csv.writer(f)
            w.writerow(["group", "status", "path", "size_bytes", "hash"])
            for i, g in enumerate(groups, 1):
                digest = full_hash(g[0], args.algo)
                for p in g:
                    w.writerow([i, "keep" if p is g[0] else "delete", p, p.stat().st_size, digest])
        print(f"📄 CSV report saved → {args.csv}")

    if args.apply:
        log = Path(f"dupe-deleted-{os.getpid()}.log")
        removed = 0
        with open(log, "w", encoding="utf-8") as lf:
            for p in doomed:
                try:
                    p.unlink()
                    lf.write(str(p) + "\n")
                    removed += 1
                except OSError as exc:
                    print(f"  ! could not delete {p}: {exc}")
        print(f"🗑 deleted {removed}/{len(doomed)} file(s) — paths logged in {log}")
    else:
        print(f"🔍 preview only — {len(doomed)} file(s) would be deleted. re-run with --apply to execute.")


if __name__ == "__main__":
    main()
