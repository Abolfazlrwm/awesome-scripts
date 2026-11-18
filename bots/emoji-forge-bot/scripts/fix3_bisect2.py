"""Manual bisect v2: item-level surgery.

Candidates are ITEMS (bare 'sh' paths and 'gr' groups) inside any
shapes list, at any depth — old-brand scripts in these packs are
often bare letter paths fused into a scene group. A candidate is
removed when its render-diff concentrates in the target region.
Chain mode: if a staged file exists for the index it is used as
the input, so repeated runs accumulate removals.

Usage: fix3_bisect.py <idx> <x0> <y0> <x1> <y1> [thresh] [minpx]
"""
import copy
import glob
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import ImageChops

from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.v5 import (_group_bbox, _mark_visible_box, _mat_apply,
                            _render_frames, _scope_layers)

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix3")
os.makedirs(STAGE, exist_ok=True)
FRACS = (0, 0.15, 0.3, 0.45, 0.55, 0.7, 0.85, 0.95)


def _remove_item_at_path(doc, scope, layer_ind, path):
    layers = _scope_layers(doc, scope)
    layer = None
    for cand in layers:
        if cand.get("ind") == layer_ind:
            layer = cand
            break
    if layer is None:
        return False
    items = layer.get("shapes") or []
    for idx in path[:-1]:
        items = items[idx].get("it") or []
    if 0 <= path[-1] < len(items):
        items.pop(path[-1])
        return True
    return False


def _enum_items(doc):
    out = []

    def walk_list(scope, layer, items, chain, prefix):
        for i, item in enumerate(items):
            if not isinstance(item, dict):
                continue
            ty = item.get("ty")
            if ty not in ("sh", "gr"):
                continue
            try:
                pts = _group_bbox([item], (1, 0, 0, 1, 0, 0))
            except Exception:
                pts = None
            if not pts:
                continue
            xs = [p[0] for p in pts]
            ys = [p[1] for p in pts]
            if max(xs) - min(xs) <= 2 or max(ys) - min(ys) <= 2:
                continue
            out.append({"scope": scope, "layer": layer, "group": item,
                        "chain": chain + [item] if ty == "gr" else list(chain),
                        "path": prefix + (i,),
                        "local_bbox": (min(xs), min(ys), max(xs), max(ys))})
            if ty == "gr":
                walk_list(scope, layer, item.get("it") or [],
                          chain + [item], prefix + (i,))

    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for layer in layers:
            if layer.get("ty") != 4:
                continue
            walk_list(scope, layer, layer.get("shapes") or [], [], ())
    return out


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


def main():
    idx = int(sys.argv[1])
    region = tuple(int(x) for x in sys.argv[2:6])
    thresh = int(sys.argv[6]) if len(sys.argv) > 6 else 24
    minpx = int(sys.argv[7]) if len(sys.argv) > 7 else 120
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
    cands = _enum_items(doc)
    px0, py0 = max(0, region[0] - 30), max(0, region[1] - 30)
    px1, py1 = min(512, region[2] + 30), min(512, region[3] + 30)
    near = []
    for cand in cands:
        try:
            _w, _h, m, _f, _o = _mark_visible_box(doc, cand)
            lb = cand["local_bbox"]
            pts = [_mat_apply(m, x, y) for x in (lb[0], lb[2])
                   for y in (lb[1], lb[3])]
            xs = [p[0] for p in pts]
            ys = [p[1] for p in pts]
            if (max(xs) >= px0 and min(xs) <= px1
                    and max(ys) >= py0 and min(ys) <= py1):
                near.append(cand)
        except Exception:
            near.append(cand)
    print(f"{idx}: {len(near)}/{len(cands)} items near region "
          f"(src={'staged' if src == staged_path else 'final'})")
    hits = []
    for cand in near:
        try:
            doc2 = copy.deepcopy(doc)
            if not _remove_item_at_path(doc2, cand["scope"],
                                         cand["layer"].get("ind"),
                                         cand["path"]):
                continue
            var = _render_frames(dump_tgs(doc2), frames)
            if var is None:
                continue
            tot, frac = _diff_stats(base, var, region, thresh)
            if tot >= minpx and frac >= 0.65:
                hits.append((cand, tot, frac))
        except Exception:
            continue
    hits.sort(key=lambda h: -h[1])
    print(f"{idx}: {len(hits)} hit(s)")
    for cand, tot, frac in hits[:20]:
        print(f"  scope={cand['scope']} layer={cand['layer'].get('ind')} "
              f"path={cand['path']} ty={cand['group'].get('ty')} "
              f"nm={str(cand['group'].get('nm'))[:20]!r} "
              f"changed={tot} inside={frac:.2f}")
    if not hits:
        return
    doc3 = copy.deepcopy(doc)
    ordered = sorted(hits, key=lambda h: (str(h[0]["scope"]),
                                          h[0]["layer"].get("ind"),
                                          h[0]["path"]),
                     reverse=True)
    applied, seen = 0, set()
    for cand, _t, _f in ordered:
        key = (cand["scope"], cand["layer"].get("ind"), cand["path"])
        if key in seen:
            continue
        seen.add(key)
        if _remove_item_at_path(doc3, cand["scope"],
                                cand["layer"].get("ind"), cand["path"]):
            applied += 1
    var = _render_frames(dump_tgs(doc3), frames)
    pad = (max(0, region[0] - 26), max(0, region[1] - 26),
           min(512, region[2] + 26), min(512, region[3] + 26))
    tot, frac = _diff_stats(base, var, pad, thresh)
    print(f"  combined: changed={tot} inside(padded)={frac:.2f} "
          f"applied={applied}")
    if frac >= 0.80 and tot >= minpx:
        with open(staged_path, "wb") as fh:
            fh.write(dump_tgs(doc3))
        print(f"  STAGED -> {staged_path}")
    else:
        print("  NOT staged (combined diff escapes the region)")


if __name__ == "__main__":
    main()
