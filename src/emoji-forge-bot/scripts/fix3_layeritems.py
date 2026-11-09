"""Item bisect restricted to ONE layer (found via solo diagnosis):
test-remove every shapes item in that layer; keep removals whose
render diff concentrates in the target region; apply together,
verify, stage. For marks fused inside a single scene layer.

Usage: fix3_layeritems.py <idx> <scope|None> <pos> <x0> <y0> <x1> <y1> [maxpx]
maxpx caps per-item changed px (keeps big body shapes, takes only
small letter/mark items).
"""
import copy
import glob
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import ImageChops

from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.v5 import _render_frames

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix3")
os.makedirs(STAGE, exist_ok=True)
FRACS = (0.15, 0.5, 0.85)


def _scope_lists(doc):
    out = [(None, doc.get("layers") or [])]
    out += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    return out


def _find(doc, scope, pos):
    for s, layers in _scope_lists(doc):
        if str(s) == scope:
            return layers[pos]
    return None


def _enum_items(items, prefix=()):
    out = []
    for i, item in enumerate(items):
        if not isinstance(item, dict):
            continue
        if item.get("ty") in ("sh", "gr", "el", "rc"):
            out.append(prefix + (i,))
        if item.get("ty") == "gr":
            out += _enum_items(item.get("it") or [], prefix + (i,))
    return out


def _remove(items, path):
    if len(path) == 1:
        items.pop(path[0])
    else:
        _remove(items[path[0]].get("it") or [], path[1:])


def _diff_stats(base, var, region, thresh=24):
    x0, y0, x1, y1 = region
    tot, inside = 0, 0
    for b, v in zip(base, var):
        d = ImageChops.difference(b, v).convert("L")
        bbox = d.point(lambda x: 255 if x > thresh else 0).getbbox()
        if bbox is None:
            continue
        changed = sum(1 for x in d.crop(bbox).getdata() if x > thresh)
        ix0, iy0 = max(bbox[0], x0), max(bbox[1], y0)
        ix1, iy1 = min(bbox[2], x1), min(bbox[3], y1)
        ins = 0
        if ix1 > ix0 and iy1 > iy0:
            ins = sum(1 for x in d.crop((ix0, iy0, ix1, iy1)).getdata()
                      if x > thresh)
        tot += changed
        inside += ins
    return tot, (inside / tot if tot else 0.0)


def main():
    idx = int(sys.argv[1])
    scope = sys.argv[2]
    pos = int(sys.argv[3])
    region = tuple(int(x) for x in sys.argv[4:8])
    maxpx = int(sys.argv[8]) if len(sys.argv) > 8 else 100000
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    staged_path = os.path.join(STAGE, os.path.basename(finals[idx]))
    src = staged_path if os.path.exists(staged_path) else finals[idx]
    doc = load_tgs(open(src, "rb").read())
    layer = _find(doc, scope, pos)
    if layer is None:
        print("layer not found")
        return
    frames = sorted(set(int(doc.get("op", 180) * f) for f in FRACS))
    base = _render_frames(dump_tgs(doc), frames)
    items = _enum_items(layer.get("shapes") or [])
    print(f"{idx} layer pos={pos} ind={layer.get('ind')}: {len(items)} items")
    hits = []
    for path in items:
        doc2 = copy.deepcopy(doc)
        l2 = _find(doc2, scope, pos)
        try:
            _remove(l2.get("shapes") or [], path)
        except Exception:
            continue
        var = _render_frames(dump_tgs(doc2), frames)
        if var is None:
            continue
        tot, frac = _diff_stats(base, var, region)
        if 60 <= tot <= maxpx and frac >= 0.70:
            hits.append((path, tot, frac))
    hits.sort(key=lambda h: -h[1])
    for path, tot, frac in hits:
        print(f"  item {path} changed={tot} inside={frac:.2f}")
    if not hits:
        print("  no hits")
        return
    doc3 = copy.deepcopy(doc)
    l3 = _find(doc3, scope, pos)
    for path, _t, _f in sorted(hits, key=lambda h: h[0], reverse=True):
        try:
            _remove(l3.get("shapes") or [], path)
        except Exception:
            pass
    var = _render_frames(dump_tgs(doc3), frames)
    pad = (max(0, region[0] - 20), max(0, region[1] - 20),
           min(512, region[2] + 20), min(512, region[3] + 20))
    tot, frac = _diff_stats(base, var, pad)
    print(f"  combined: changed={tot} inside(padded)={frac:.2f}")
    if frac >= 0.85 and tot >= 60:
        with open(staged_path, "wb") as fh:
            fh.write(dump_tgs(doc3))
        print(f"  STAGED -> {staged_path}")
    else:
        print("  NOT staged")


if __name__ == "__main__":
    main()
