#!/usr/bin/env python3
"""Build v7 finals from the ORIGINALS of the user's pack.

Runs process_tgs_v7 over all 20 originals into
data/projects/forge-v7-test/{originals,final}/ and writes a build
report (repairs + logo placement per file) to
data/projects/forge-v7-test/build-report.json.
Local only — no Telegram calls.
"""
import argparse
import glob
import json
import os
import shutil
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs, pack_common_signatures  # noqa: E402
from emoji_forge.brand import BrandProfile  # noqa: E402
from emoji_forge.v7 import BIG_TARGET_W, process_tgs_v7  # noqa: E402
from emoji_forge.validate_tgs import validate_tgs_bytes  # noqa: E402

# Banner files: the old SVN text was 877px wide (aspect ~6.7), so the
# default "never narrower than the old logo" rule made the Abolfazl logo
# (aspect ~2.3) 381px tall and it towered over the banner. These files
# use the standard big target width (358px, ~156px tall) instead.
BANNER_FILES = {17, 18, 19}

ORIG_DIR = "data/projects/forge-20261028-155740-3f1382/originals"
OUT_DIR = "data/projects/forge-v7-test"
# The exact brand + hide-name inputs the v5/v6 builds used, so v7 is
# comparable layer-for-layer with v6.
FINGERPRINT = "data/projects/forge-v5-test/fingerprint.json"


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--only", type=int, default=0, help="build only file N (1-based)")
    args = ap.parse_args()
    fp = json.load(open(FINGERPRINT))
    brand = BrandProfile(**fp["brand"])
    hide = set(fp.get("hide_layer_names") or [])
    origs = sorted(glob.glob(os.path.join(ORIG_DIR, "*.tgs")))
    docs = [load_tgs(open(p, "rb").read()) for p in origs]
    sigs = pack_common_signatures(docs)
    os.makedirs(os.path.join(OUT_DIR, "originals"), exist_ok=True)
    os.makedirs(os.path.join(OUT_DIR, "final"), exist_ok=True)
    for p in origs:
        shutil.copy(p, os.path.join(OUT_DIR, "originals", os.path.basename(p)))
    report = {}
    report_path = os.path.join(OUT_DIR, "build-report.json")
    if args.only and os.path.exists(report_path):
        # Partial rebuild: keep the other files' report entries.
        report = {int(k): v for k, v in
                  json.load(open(report_path)).items()}
    for idx, path in enumerate(origs, 1):
        if args.only and idx != args.only:
            continue
        data = open(path, "rb").read()
        target_w = BIG_TARGET_W if idx in BANNER_FILES else None
        out, warnings, info = process_tgs_v7(
            data, brand, hide, idx - 1, sigs, main_logo_target_w=target_w)
        name = os.path.basename(path)
        with open(os.path.join(OUT_DIR, "final", name), "wb") as fh:
            fh.write(out)
        issues, vwarn = validate_tgs_bytes(out, idx)
        report[idx] = {"file": name, "size": len(out), "info": info,
                       "issues": issues, "validator_warnings": vwarn,
                       "warnings": warnings}
        logo = info.get("logo") or {}
        print(f"[{idx:02d}] {name} {len(out)}B logo_w={logo.get('width')} "
              f"shift={logo.get('shift')} repairs={len(info.get('repairs') or [])} "
              f"secondary={len(info.get('secondary') or [])} issues={len(issues)}")
    with open(os.path.join(OUT_DIR, "build-report.json"), "w") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=1, default=str)
    blocked = sum(len(r["issues"]) for r in report.values())
    print(f"done: {len(report)} files, validator blocked issues: {blocked}")


if __name__ == "__main__":
    main()
