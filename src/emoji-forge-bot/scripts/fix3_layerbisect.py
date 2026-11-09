"""Layer-level bisect: hide one layer at a time (hd=True) and keep
the layers whose hiding changes ONLY the target region. For the
exploded exports where a brand script is a whole layer fused with
nothing else — group surgery cannot reach those. Brand-carrying
layers are never hidden. Chain mode via final_fix3/ staging.

Usage: fix3_layerbisect.py <idx> <x0> <y0> <x1> <y1> [thresh]
"""
import copy
import glob
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import ImageChops

from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.v5 import _render_frames, _scope_layers

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix3")
os.makedirs(STAGE, exist_ok=True)
FRACS = (0, 0.15, 0.3, 0.45, 0.55, 0.7, 0.85, 0.95)


def _has_brand(node):
    if isinstance(node, dict):
        if "brand" in str(node.get("nm") or "").lower():
            return True
        return any(_has_brand(v) for v in node.values())
    if isinstance(node, list):
        return any(_has_brand(v) for v in node)
    return False


def _diff_stats(base, var, region, thresh):
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


def _scopes(doc):
    out = [(None, doc.get("layers") or [])]
    out += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    return out


def main():
    idx = int(sys.argv[1])
    region = tuple(int(x) for x in sys.argv[2:6])
    thresh = int(sys.argv[6]) if len(sys.argv) > 6 else 24
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    staged_path = os.path.join(STAGE, os.path.basename(finals[idx]))
    src = staged_path if os.path.exists(staged_path) else finals[idx]
    doc = load_tgs(open(src, "rb").read())
    frames = sorted(set(int(doc.get("op", 180) * f) for f in FRACS))
    base = _render_frames(dump_tgs(doc), frames)
    if base is None:
        print("render failed")
        return
    hits = []
    total = 0
    for scope, layers in _scopes(doc):
        for layer in layers:
            if layer.get("ty") == 3 or layer.get("hd"):
                continue
            if _has_brand(layer):
                continue
            total += 1
            doc2 = copy.deepcopy(doc)
            target = None
            for cand in _scope_layers(doc2, scope):
                if cand.get("ind") == layer.get("ind"):
                    target = cand
                    break
            if target is None:
                continue
            target["hd"] = True
            var = _render_frames(dump_tgs(doc2), frames)
            if var is None:
                continue
            tot, frac = _diff_stats(base, var, region, thresh)
            if 120 <= tot <= 30000 and frac >= 0.80:
                hits.append((scope, layer.get("ind"), tot, frac))
    print(f"{idx}: {len(hits)}/{total} layers qualify")
    for scope, ind, tot, frac in hits:
        print(f"  scope={scope} ind={ind} changed={tot} inside={frac:.2f}")
    if not hits:
        return
    doc3 = copy.deepcopy(doc)
    for scope, ind, _t, _f in hits:
        for cand in _scope_layers(doc3, scope):
            if cand.get("ind") == ind:
                cand["hd"] = True
    var = _render_frames(dump_tgs(doc3), frames)
    pad = (max(0, region[0] - 26), max(0, region[1] - 26),
           min(512, region[2] + 26), min(512, region[3] + 26))
    tot, frac = _diff_stats(base, var, pad, thresh)
    print(f"  combined: changed={tot} inside(padded)={frac:.2f}")
    if frac >= 0.85 and tot >= 120:
        with open(staged_path, "wb") as fh:
            fh.write(dump_tgs(doc3))
        print(f"  STAGED -> {staged_path}")
    else:
        print("  NOT staged")


if __name__ == "__main__":
    main()
