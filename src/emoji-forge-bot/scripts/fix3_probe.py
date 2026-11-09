"""Probe: for given files, dump candidate groups (colour-agnostic
enumeration) in the ORIGINAL with fill profiles, so the true mark's
structure can be seen next to scene decoys. Dry-run only."""
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs
from emoji_forge.v5 import _group_bbox, _mark_visible_box

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)


def fill_profile(item):
    colors, grads, shapes = set(), 0, 0
    stack = [item]
    while stack:
        it = stack.pop()
        if not isinstance(it, dict):
            continue
        ty = it.get("ty")
        if ty == "fl":
            c = (it.get("c") or {}).get("k")
            if isinstance(c, list):
                colors.add(tuple(round(x, 2) for x in c[:3]))
        elif ty == "gs":
            grads += 1
        elif ty == "sh":
            shapes += 1
        for sub in it.get("it") or []:
            stack.append(sub)
    return colors, grads, shapes


def enum_any(doc, depth=4):
    out = []

    def _consider(scope, layer, group, chain, path):
        subs = [s for s in (group.get("it") or [])
                if isinstance(s, dict) and s.get("ty") in ("sh", "gr")]
        if not subs:
            return
        try:
            pts = _group_bbox([s for s in (group.get("it") or [])
                               if not (isinstance(s, dict) and s.get("ty") == "tr")],
                              (1, 0, 0, 1, 0, 0))
        except Exception:
            return
        if not pts:
            return
        xs = [p[0] for p in pts]
        ys = [p[1] for p in pts]
        w, h = max(xs) - min(xs), max(ys) - min(ys)
        if w <= 4 or h <= 4:
            return
        out.append({"scope": scope, "layer": layer, "group": group,
                    "chain": chain, "path": path,
                    "local_bbox": (min(xs), min(ys), max(xs), max(ys))})

    def _walk(scope, layer, group, chain, path, level):
        if level >= depth:
            return
        for j, sub in enumerate(group.get("it") or []):
            if isinstance(sub, dict) and sub.get("ty") == "gr":
                _consider(scope, layer, sub, chain + [sub], path + (j,))
                _walk(scope, layer, sub, chain + [sub], path + (j,), level + 1)

    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for layer in layers:
            if layer.get("ty") != 4:
                continue
            shapes = layer.get("shapes") or []
            n_bare = sum(1 for s in shapes
                         if isinstance(s, dict) and s.get("ty") == "sh")
            if n_bare:
                print(f"    layer ind={layer.get('ind')} nm={layer.get('nm')!r} "
                      f"BARE sh items: {n_bare} of {len(shapes)} shapes")
            for i, item in enumerate(shapes):
                if isinstance(item, dict) and item.get("ty") == "gr":
                    _consider(scope, layer, item, [item], (i,))
                    _walk(scope, layer, item, [item], (i,), 1)
    return out


def main():
    man = json.load(open(os.path.join(BASE, "manifest.json")))
    for arg in sys.argv[1:]:
        idx = int(arg)
        doc = load_tgs(open(man["items"][idx - 1]["local_path"], "rb").read())
        print(f"== {idx} ==")
        for cand in enum_any(doc):
            colors, grads, shapes = fill_profile(cand["group"])
            try:
                vw, vh, _m, _f, _o = _mark_visible_box(doc, cand)
                vis = f"vis {vw:.0f}x{vh:.0f}"
            except Exception:
                vis = "vis ?"
            print(f"  scope={cand['scope']} layer={cand['layer'].get('ind')} "
                  f"path={cand['path']} nm={str(cand['group'].get('nm'))[:28]!r} "
                  f"sh={shapes} fills={len(colors)} grads={grads} {vis} "
                  f"cols={sorted(colors)[:4]}")


if __name__ == "__main__":
    main()
