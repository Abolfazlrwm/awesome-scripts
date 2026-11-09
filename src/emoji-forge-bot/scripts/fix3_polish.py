"""Polish pass: files whose name exists but is weak/hidden get the
settled top fallback layer added (the pack's common pattern is a
bottom name next to a slot name). File 151 is special: its sign
text overflows the sign, so it is shrunk to fit instead.
Outputs to final_fix3/ only.
Usage: fix3_polish.py <idx> [<idx> ...]"""
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.brand import BrandProfile
from fix3_post import add_fallback, brand_qa, scale_brand_text

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix3")
os.makedirs(STAGE, exist_ok=True)


def main():
    plan = json.load(open(os.path.join(BASE, "mati_plan_applied.json")))
    brand = BrandProfile.from_dict(plan.get("brand") or {})
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    for arg in sys.argv[1:]:
        idx = int(arg)
        doc = load_tgs(open(finals[idx], "rb").read())
        qa0 = brand_qa(doc)
        if idx == 151:
            scale_brand_text(doc, 0.55)
            act = "shrunk x0.55"
        else:
            add_fallback(doc, brand)
            act = "fallback-added"
        qa1 = brand_qa(doc)
        out = os.path.join(STAGE, os.path.basename(finals[idx]))
        with open(out, "wb") as fh:
            fh.write(dump_tgs(doc))
        print(f"{idx}: {act} qa {qa0['med_h']}->{qa1['med_h']} "
              f"px {qa0['px']}->{qa1['px']} edge {qa0['edge']:.2f}->{qa1['edge']:.2f}")


if __name__ == "__main__":
    main()
