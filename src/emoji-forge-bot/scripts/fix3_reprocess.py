"""Reprocess one file from its ORIGINAL through process_tgs_v5,
for finals damaged by earlier processing (e.g. 169's blanked
tickets). Stages to final_fix3/ under the final's filename.
Usage: fix3_reprocess.py <idx> [aggressive=0|1]
"""
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs, pack_common_signatures
from emoji_forge.brand import BrandProfile
from emoji_forge.pipeline import detect_pack_theme_hue
from emoji_forge.v5 import process_tgs_v5

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix3")
os.makedirs(STAGE, exist_ok=True)


def main():
    idx = int(sys.argv[1])
    aggressive = len(sys.argv) > 2 and sys.argv[2] == "1"
    man = json.load(open(os.path.join(BASE, "manifest.json")))
    plan = json.load(open(os.path.join(BASE, "mati_plan_applied.json")))
    brand = BrandProfile.from_dict(plan.get("brand") or {})
    hide = plan.get("hide_layer_names") or []
    docs = []
    for item in man["items"]:
        try:
            docs.append(load_tgs(open(item["local_path"], "rb").read()))
        except Exception:
            docs.append(None)
    hue = detect_pack_theme_hue([d for d in docs if d])
    sigs = pack_common_signatures([d for d in docs if d])
    raw = open(man["items"][idx - 1]["local_path"], "rb").read()
    out, warns, pl = process_tgs_v5(raw, brand, hide, idx - 1, sigs,
                                    theme_hue_deg=hue,
                                    aggressive_marks=aggressive)
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    target = os.path.join(STAGE, os.path.basename(finals[idx]))
    with open(target, "wb") as fh:
        fh.write(out)
    print(f"{idx}: reprocessed aggressive={aggressive} -> {pl} "
          f"({len(out)} B), warns={len(warns)}")
    for w in warns[:4]:
        print("   warn:", w[:130])


if __name__ == "__main__":
    main()
