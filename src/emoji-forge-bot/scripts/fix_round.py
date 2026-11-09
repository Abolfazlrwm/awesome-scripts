"""Targeted fix round for the sweep-flagged files of the Mehdi pack:
reprocess each flagged original with the aggressive mark search +
scene guard + mirror counter-flip, replacing its final (old final
backed up to final_backup_fixround/). Prints placements + warnings
so the round can be reviewed file by file."""
import glob
import json
import os
import shutil
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs, pack_common_signatures
from emoji_forge.brand import BrandProfile
from emoji_forge.pipeline import detect_pack_theme_hue
from emoji_forge.v5 import process_tgs_v5

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)

FLAGGED = [2, 7, 10, 13, 15, 17, 18, 19, 22, 23, 24, 26, 30, 32, 33,
           40, 50, 68, 75, 76, 83, 86, 92, 97, 98, 103, 108, 111, 112,
           113, 117, 119, 122, 123, 125, 134, 143, 145, 146, 149, 150,
           151, 152, 153, 154, 157, 159, 161, 162, 164, 166, 170, 171,
           185, 187, 188, 192, 194, 195]


def main():
    man = json.load(open(os.path.join(BASE, "manifest.json")))
    plan = json.load(open(os.path.join(BASE, "mati_plan_applied.json")))
    brand = BrandProfile.from_dict(plan.get("brand") or {})
    hide = set(plan.get("hide_layer_names") or [])
    docs = [load_tgs(open(it["local_path"], "rb").read()) for it in man["items"]]
    hue = detect_pack_theme_hue(docs)
    sigs = pack_common_signatures(docs)
    final_dir = os.path.join(BASE, "final")
    backup_dir = os.path.join(BASE, "final_backup_fixround")
    os.makedirs(backup_dir, exist_ok=True)
    finals = {}
    for f in glob.glob(os.path.join(final_dir, "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    for idx in FLAGGED:
        raw = open(man["items"][idx - 1]["local_path"], "rb").read()
        out, warns, pl = process_tgs_v5(
            raw, brand, hide, idx - 1, sigs, theme_hue_deg=hue,
            aggressive_marks=True)
        target = finals.get(idx)
        if target is None:
            print(idx, "NO EXISTING FINAL — skipped")
            continue
        shutil.copy2(target, os.path.join(backup_dir, os.path.basename(target)))
        with open(target, "wb") as fh:
            fh.write(out)
        print(idx, "->", pl, f"({len(out)} B)")
        if idx in (30, 83, 143, 153):
            for w in warns:
                print("    warn:", w[:150])


if __name__ == "__main__":
    main()
