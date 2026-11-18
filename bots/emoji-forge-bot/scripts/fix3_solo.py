"""Solo diagnostic: render each layer ALONE (all others hidden,
except its parent chain) to see exactly what it draws. Reveals
redundantly stacked duplicate layers that defeat hide-one tests.

Usage: fix3_solo.py <idx> <frac> [scope]  (scope default: top)
"""
import copy
import glob
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import Image, ImageDraw

from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.v5 import _render_frames

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)


def _scope_lists(doc):
    out = [(None, doc.get("layers") or [])]
    out += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    return out


def main():
    idx = int(sys.argv[1])
    frac = float(sys.argv[2])
    want_scope = sys.argv[3] if len(sys.argv) > 3 else None
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    doc = load_tgs(open(finals[idx], "rb").read())
    frame = int(doc.get("op", 180) * frac)
    scopes = _scope_lists(doc)
    tiles = []
    for s_i, (scope, layers) in enumerate(scopes):
        if want_scope is not None and str(scope) != want_scope:
            continue
        by_ind = {}
        for pos, l in enumerate(layers):
            by_ind.setdefault(l.get("ind"), []).append(pos)
        for pos, layer in enumerate(layers):
            if layer.get("ty") == 3:
                continue
            keep = {pos}
            p = layer.get("parent")
            seen = set()
            while p is not None and p not in seen:
                seen.add(p)
                for pp in by_ind.get(p, []):
                    keep.add(pp)
                    p = layers[pp].get("parent")
                    break
                else:
                    break
            doc2 = copy.deepcopy(doc)
            lst = _scope_lists(doc2)[s_i][1]
            for j, l2 in enumerate(lst):
                if j not in keep:
                    l2["hd"] = True
            var = _render_frames(dump_tgs(doc2), [frame])
            if var is None:
                continue
            im = var[0]
            # skip fully transparent/empty solos
            if im.convert("L").getbbox() is None:
                continue
            tiles.append((f"{str(scope)[:8]}#{pos} i{layer.get('ind')}", im))
    cell = 150
    cols = 7
    nrows = (len(tiles) + cols - 1) // cols
    sheet = Image.new("RGB", (cols * cell, max(1, nrows) * (cell + 13)), "white")
    dr = ImageDraw.Draw(sheet)
    for k, (label, im) in enumerate(tiles):
        x = (k % cols) * cell
        y = (k // cols) * (cell + 13)
        dr.text((x + 3, y + 1), label, fill="black")
        sheet.paste(im.resize((cell - 4, cell - 4)), (x + 2, y + 13))
    out = f"/home/hatch/workspace/your_files/solo_{idx}.png"
    sheet.save(out)
    print(f"{idx}: {len(tiles)} non-empty solos -> {out}")


if __name__ == "__main__":
    main()
