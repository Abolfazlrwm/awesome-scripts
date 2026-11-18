"""Strip diagnostic: hide ONE layer at a time at a single frame
fraction and report (changed px, diff bbox) for every layer, plus
a labelled sheet of the biggest-change variants so a human eye
can find exactly which layer draws the old-brand mark.

Usage: fix3_strip.py <idx> <frac> <topn>
Writes ~/workspace/your_files/strip_<idx>.png and prints a table.
"""
import copy
import glob
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import Image, ImageChops, ImageDraw

from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.v5 import _render_frames, _scope_layers
from fix3_layerbisect import _has_brand, _scopes

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)


def main():
    idx = int(sys.argv[1])
    frac = float(sys.argv[2])
    topn = int(sys.argv[3]) if len(sys.argv) > 3 else 24
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    doc = load_tgs(open(finals[idx], "rb").read())
    frame = int(doc.get("op", 180) * frac)
    base = _render_frames(dump_tgs(doc), [frame])
    if base is None:
        print("render failed")
        return
    base_im = base[0]
    rows = []
    for scope, layers in _scopes(doc):
        for layer in layers:
            if layer.get("ty") == 3 or layer.get("hd"):
                continue
            if _has_brand(layer):
                continue
            doc2 = copy.deepcopy(doc)
            target = None
            for cand in _scope_layers(doc2, scope):
                if cand.get("ind") == layer.get("ind"):
                    target = cand
                    break
            if target is None:
                continue
            target["hd"] = True
            var = _render_frames(dump_tgs(doc2), [frame])
            if var is None:
                continue
            d = ImageChops.difference(base_im, var[0]).convert("L")
            mask = d.point(lambda x: 255 if x > 24 else 0)
            bbox = mask.getbbox()
            if bbox is None:
                continue
            changed = sum(1 for x in d.crop(bbox).getdata() if x > 24)
            if changed < 40:
                continue
            rows.append({"scope": scope, "ind": layer.get("ind"),
                         "ty": layer.get("ty"), "changed": changed,
                         "bbox": bbox, "im": var[0]})
    rows.sort(key=lambda r: -r["changed"])
    print(f"{idx} @frac {frac}: {len(rows)} layers change the frame")
    for r in rows[:40]:
        print(f"  scope={r['scope']} ind={r['ind']} ty={r['ty']} "
              f"changed={r['changed']} bbox={r['bbox']}")
    show = rows[:topn]
    if not show:
        return
    cell = 170
    cols = 6
    nrows = (len(show) + 1 + cols - 1) // cols
    sheet = Image.new("RGB", (cols * cell, nrows * (cell + 14)), "white")
    dr = ImageDraw.Draw(sheet)
    dr.text((4, 2), f"{idx} base @ {frac}", fill="black")
    sheet.paste(base_im.resize((cell - 4, cell - 4)), (2, 14))
    for k, r in enumerate(show):
        pos = k + 1
        x = (pos % cols) * cell
        y = (pos // cols) * (cell + 14)
        label = f"{str(r['scope'])[:10]}:{r['ind']} -{r['changed']}"
        dr.text((x + 4, y + 2), label, fill="black")
        sheet.paste(r["im"].resize((cell - 4, cell - 4)), (x + 2, y + 14))
    out = f"/home/hatch/workspace/your_files/strip_{idx}.png"
    sheet.save(out)
    print("saved", out)


if __name__ == "__main__":
    main()
