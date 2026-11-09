"""Fix-12: file 162 ONLY — remove the «استور مهدی» calligraphy
baked into the book cover and seat the halo E30TZ logo in its
exact slot.

Diagnosis (renders via rlottie from_tgs + hide-by-position):
- Cover decorations live in top layer position 14 (ind=15,
  parent chain of nulls, layer rotation -45, opacity holds:
  visible only while the book is closed).
- Within that layer, shape item 14 is the cover-centre group;
  its subgroup A holds exactly 6 glyph subgroups = the two
  calligraphy lines «استور / مهدی» (fill lavender). Everything
  else in the layer (frame lines items 10-13, flourishes
  items 0-9) is generic ornament and stays untouched.
- The perspective (scale 109.9/68) sits in item 14's own
  trailing tr, and the offset in A's trailing tr (p=[0,35]) —
  both are KEPT, so logo artwork fitted axis-aligned into the
  glyph bbox (A-local coords) inherits the exact cover tilt.

Method: replace A's glyph items with fix4_logo._logo_items(box)
(FIX4_HALO=1: plain logo, light stroke + shadow, no plate),
keeping A's trailing tr. Stages to final_fix12/, backs the
current final up to final_backup_fix12/.
"""
import copy
import importlib.util
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
os.environ["FIX4_HALO"] = "1"

from emoji_forge.animation_engine import dump_tgs, load_tgs

spec = importlib.util.spec_from_file_location(
    "fix4_logo", os.path.join(os.path.dirname(__file__), "fix4_logo.py"))
fix4 = importlib.util.module_from_spec(spec)
spec.loader.exec_module(fix4)

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data",
                    "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix12")
BACKUP = os.path.join(BASE, "final_backup_fix12")
os.makedirs(STAGE, exist_ok=True)
os.makedirs(BACKUP, exist_ok=True)

LAYER_POS = 14   # top-layer position (ind=15)
ITEM_POS = 14    # shape-item position inside that layer


def glyph_box(items):
    boxes = []

    def walk(its):
        for it in its:
            if not isinstance(it, dict):
                continue
            if it.get("ty") == "gr":
                walk(it.get("it") or [])
            elif it.get("ty") == "sh":
                k = (it.get("ks") or {}).get("k") or {}
                pts = [p for p in k.get("v") or []
                       if isinstance(p, list) and len(p) >= 2]
                if pts:
                    xs = [p[0] for p in pts]
                    ys = [p[1] for p in pts]
                    boxes.append((min(xs), min(ys), max(xs), max(ys)))

    walk(items)
    if not boxes:
        return None
    return (min(b[0] for b in boxes), min(b[1] for b in boxes),
            max(b[2] for b in boxes), max(b[3] for b in boxes))


def main():
    finals = [f for f in os.listdir(os.path.join(BASE, "final"))
              if "_162_" in f]
    assert len(finals) == 1, finals
    src = os.path.join(BASE, "final", finals[0])
    raw = open(src, "rb").read()
    with open(os.path.join(BACKUP, finals[0]), "wb") as fh:
        fh.write(raw)
    doc = load_tgs(raw)

    layer = doc["layers"][LAYER_POS]
    assert layer.get("ind") == 15, layer.get("ind")
    item = layer["shapes"][ITEM_POS]
    assert item.get("ty") == "gr"
    a_group = item["it"][0]
    assert a_group.get("ty") == "gr"
    a_items = a_group["it"]
    trailing = a_items[-1]
    assert trailing.get("ty") == "tr", trailing.get("ty")
    glyphs = a_items[:-1]
    assert len(glyphs) == 6 and all(g.get("ty") == "gr" for g in glyphs)

    box = glyph_box(glyphs)
    print("calligraphy box (A-local):", box)
    assert box and box[2] - box[0] > 50 and box[3] - box[1] > 50

    logo_items = fix4._logo_items(box)
    a_group["it"] = logo_items + [trailing]

    out = os.path.join(STAGE, finals[0])
    with open(out, "wb") as fh:
        fh.write(dump_tgs(doc))
    print(f"staged -> {out} ({os.path.getsize(out)} B, was {len(raw)} B)")


if __name__ == "__main__":
    main()
