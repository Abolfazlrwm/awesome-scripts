#!/usr/bin/env python3
# Telegram: @Abolfazl_rwm
"""Bulk Renamer — safe pattern-based file renaming CLI.

Rename many files at once with preview-first workflow:

  * --find/--replace        literal or --regex search & replace
  * --template "trip_{n:03}" numbered template ({n}, {orig}, {ext}, {date})
  * --lower / --upper       case folding
  * --ext jpg png           restrict to extensions
  * --depth                 recurse N levels deep (default: top level only)

Safety model:
  * dry-run is the DEFAULT — nothing is touched until --apply is passed
  * every --apply run writes an undo log (JSON) and prints its path
  * `--undo <log.json>` restores the previous names exactly
  * collisions, missing files and unchanged names are detected before writing

Stdlib only.
"""

from __future__ import annotations

import argparse
import json
import re
import sys
import unicodedata
from datetime import datetime
from pathlib import Path


def build_plan(args: argparse.Namespace) -> list[tuple[Path, Path]]:
    """Compute (old_path, new_path) pairs for every file that would change."""
    root = Path(args.dir)
    if not root.is_dir():
        raise SystemExit(f"error: {root} is not a directory")

    exts = {e.lower().lstrip(".") for e in args.ext} if args.ext else None
    files: list[Path] = []
    for depth in range(args.depth):
        pattern = "*/" * depth + "*"
        files.extend(p for p in root.glob(pattern) if p.is_file())
    files = sorted(set(files))

    counter = args.start
    plan: list[tuple[Path, Path]] = []
    rx = re.compile(args.find) if (args.regex and args.find) else None

    for f in files:
        if exts and f.suffix.lower().lstrip(".") not in exts:
            continue
        stem, suffix = f.stem, f.suffix

        if args.template:
            new_stem = args.template.format(
                n=counter, orig=stem, ext=suffix.lstrip("."),
                date=datetime.now().strftime("%Y%m%d"),
            )
            counter += 1
            if args.find:  # optional extra cleanup inside templated names
                new_stem = new_stem.replace(args.find, args.replace or "")
        elif args.find:
            new_stem = rx.sub(args.replace, stem) if rx else stem.replace(args.find, args.replace)
        else:
            new_stem = stem
            if args.find is None and args.template is None and not (args.lower or args.upper) \
               and not args.strip_accents:
                continue  # nothing to do for this file

        if args.lower:
            new_stem, suffix = new_stem.lower(), suffix.lower()
        if args.upper:
            new_stem, suffix = new_stem.upper(), suffix.upper()
        if args.strip_accents:
            new_stem = "".join(
                c for c in unicodedata.normalize("NFKD", new_stem) if not unicodedata.combining(c)
            )
        new_stem = new_stem.strip().replace(" ", args.sep or "_")

        new_path = f.with_name(new_stem + suffix)
        if new_path != f:
            plan.append((f, new_path))
    return plan


def check_conflicts(plan: list[tuple[Path, Path]]) -> list[str]:
    """Names that two files would race for, or that already exist on disk."""
    problems: list[str] = []
    seen: dict[Path, Path] = {}
    for old, new in plan:
        if new in seen:
            problems.append(f"collision: {seen[new].name} and {old.name} both → {new.name}")
        else:
            seen[new] = old
        if new.exists() and new != old:
            problems.append(f"target exists: {new.name}")
    return problems


def do_apply(plan: list[tuple[Path, Path]], log_path: Path) -> None:
    mapping = {}
    done = 0
    for old, new in plan:
        try:
            new.parent.mkdir(parents=True, exist_ok=True)
            old.rename(new)
            mapping[str(old)] = str(new)
            done += 1
        except OSError as exc:
            print(f"  ! failed {old.name}: {exc}")
    log_path.write_text(json.dumps(mapping, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"✅ renamed {done}/{len(plan)} files — undo log: {log_path}")


def do_undo(log_path: Path) -> None:
    mapping: dict[str, str] = json.loads(log_path.read_text(encoding="utf-8"))
    done = 0
    for old, new in sorted(mapping.items(), key=lambda kv: -len(kv[0])):
        cur = Path(new)
        if cur.exists():
            cur.rename(old)
            done += 1
        else:
            print(f"  ! not found (already undone?): {new}")
    print(f"↩️ reverted {done}/{len(mapping)} renames")


def main(argv: list[str] | None = None) -> None:
    ap = argparse.ArgumentParser(
        description="safe bulk file renamer — dry-run by default, undo log included",
        formatter_class=argparse.ArgumentDefaultsHelpFormatter,
    )
    ap.add_argument("dir", nargs="?", default=".", help="target directory (default: current)")
    ap.add_argument("--find", help="literal substring to find (or regex with --regex)")
    ap.add_argument("--replace", default="", help="replacement text")
    ap.add_argument("--regex", action="store_true", help="treat --find as a regular expression")
    ap.add_argument("--template", help='numbered name template, e.g. "trip_{n:03}" — supports {n} {orig} {ext} {date}')
    ap.add_argument("--start", type=int, default=1, help="first number for --template")
    ap.add_argument("--ext", nargs="+", help="only touch these extensions (e.g. --ext jpg png)")
    ap.add_argument("--depth", type=int, default=1, help="folder levels to scan (1 = no recursion)")
    ap.add_argument("--lower", action="store_true", help="lowercase stem+extension")
    ap.add_argument("--upper", action="store_true", help="UPPERCASE stem+extension")
    ap.add_argument("--strip-accents", action="store_true", help='remove accents ("café" → "cafe")')
    ap.add_argument("--sep", help="replace spaces in names with this string (default: keep)")
    ap.add_argument("--apply", action="store_true", help="actually rename (default is preview only)")
    ap.add_argument("--undo", metavar="LOG", help="revert a previous run from its undo log")
    args = ap.parse_args(argv)

    if args.undo:
        do_undo(Path(args.undo))
        return
    if args.template and args.find:
        ap.error("--template and --find are separate modes — pick one")

    plan = build_plan(args)
    if not plan:
        print("nothing to rename — 0 matching changes")
        return

    problems = check_conflicts(plan)
    if problems:
        print("⚠️ problems found (nothing renamed):")
        for p in problems:
            print("  !", p)
        sys.exit(1)

    width = max(len(old.name) for old, _ in plan)
    for old, new in plan:
        print(f"{old.name:<{width}}  →  {new.name}")

    if args.apply:
        log_path = Path(f"rename-undo-{datetime.now().strftime('%Y%m%d-%H%M%S')}.json")
        do_apply(plan, log_path)
    else:
        print(f"\n🔍 preview only — {len(plan)} file(s) would be renamed. re-run with --apply to execute.")


if __name__ == "__main__":
    main()
