#!/usr/bin/env python3
"""Helper for the MuseBridge queue (data/brain/).

This script does NOT call any AI model. It only lists pending requests
and validates decision JSON files that a Muse session wrote.

Usage (run by a human or by a Muse session, never by the bot itself):
  python3 scripts/brain_queue.py list --data-dir data
  python3 scripts/brain_queue.py show <request_id> --data-dir data
  python3 scripts/brain_queue.py validate <decision.json> --data-dir data

A Muse session that wants to answer a queued request must:
  1. read data/brain/requests/<id>.json,
  2. analyse it with its own intelligence (that analysis happens in the
     Muse session, not in this script and not in the bot),
  3. write data/brain/decisions/<id>.json with:
     {"mode": "preserve| clean|redesign|branding or \"\"",
      "suggestion_fa": "...", "message_fa": "...",
      "warnings": ["..."], "confidence": "low|medium|high"},
  4. never put tokens, file bytes, or credentials in either file.
"""
import argparse
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "src"))

from emoji_forge.brain import validate_bridge_decision
from emoji_forge.storage import read_json


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("command", choices=["list", "show", "validate"])
    parser.add_argument("arg", nargs="?")
    parser.add_argument("--data-dir", default="data")
    args = parser.parse_args()
    base = os.path.join(args.data_dir, "brain")
    req_dir, dec_dir = os.path.join(base, "requests"), os.path.join(base, "decisions")
    if args.command == "list":
        reqs = sorted(f[:-5] for f in os.listdir(req_dir) if f.endswith(".json")) if os.path.isdir(req_dir) else []
        decs = {f[:-5] for f in os.listdir(dec_dir) if f.endswith(".json")} if os.path.isdir(dec_dir) else set()
        pending = [r for r in reqs if r not in decs]
        print(f"pending: {len(pending)}")
        for r in pending:
            print(r)
        return 0
    if args.command == "show":
        data = read_json(os.path.join(req_dir, f"{args.arg}.json"), default=None)
        print(json.dumps(data, ensure_ascii=False, indent=2) if data else "not found")
        return 0 if data else 1
    data = read_json(args.arg, default=None) if args.arg else None
    errors = validate_bridge_decision(data) if data is not None else ["فایل خوانده نشد."]
    print("valid" if not errors else "\n".join(errors))
    return 0 if not errors else 1


if __name__ == "__main__":
    raise SystemExit(main())
