"""Strip diagnostic v2: hide layers by LIST POSITION (not ind —
these exploded exports contain duplicate inds, so ind-matching
hid the wrong layer and the true mark layer was never tested).

Usage: fix3_strip2.py <idx> <frac> <topn>
"""
import copy
import glob
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import Image, ImageChops, ImageDraw

from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.v5 import _render_frames
from fix3_layerbisect import _has_brand, _scopes

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)


def _scope_lists(doc):
    """(scope_id, layers_list) with the LIVE list objects."""
    out = [(None, doc.get("layers") or [])]
    out += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    return out


def main():
    idx = int(sys.argv[1])
    frac = float(sys.argv[2])
    topn = int(sys.argv[3]) if len(sys.argv) > 3 else 24
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    doc = load_tgs(open(finals[idx], "rb").read())
    # duplicate-ind report
    for scope, layers in _scope_lists(doc):
        inds = [l.get("ind") for l in layers]
        dups = {i for i in inds if inds.count(i) > 1}
        if dups:
            print(f"  scope={scope}: DUPLICATE inds {sorted(dups, key=str)}")
    frame = int(doc.get("op", 180) * frac)
    base = _render_frames(dump_tgs(doc), [frame])
    if base is None:
        print("render failed")
        return
    base_im = base[0]
    rows = []
    for s_i, (scope, layers) in enumerate(_scope_lists(doc)):
        for pos, layer in enumerate(layers):
            if layer.get("ty") == 3 or layer.get("hd"):
                continue
            if _has_brand(layer):
                continue
            doc2 = copy.deepcopy(doc)
            target = _scope_lists(doc2)[s_i][1][pos]
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
            rows.append({"scope": scope, "pos": pos,
                         "ind": layer.get("ind"), "ty": layer.get("ty"),
                         "changed": changed, "bbox": bbox, "im": var[0]})
    rows.sort(key=lambda r: -r["changed"])
    print(f"{idx} @frac {frac}: {len(rows)} layers change the frame")
    for r in rows[:40]:
        print(f"  scope={r['scope']} pos={r['pos']} ind={r['ind']} "
              f"ty={r['ty']} changed={r['changed']} bbox={r['bbox']}")
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
        label = f"{str(r['scope'])[:8]}#{r['pos']} i{r['ind']} -{r['changed']}"
        dr.text((x + 4, y + 2), label, fill="black")
        sheet.paste(r["im"].resize((cell - 4, cell - 4)), (x + 2, y + 14))
    out = f"/home/hatch/workspace/your_files/strip2_{idx}.png"
    sheet.save(out)
    print("saved", out)


if __name__ == "__main__":
    main()
