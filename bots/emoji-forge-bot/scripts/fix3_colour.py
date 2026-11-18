"""Colour restore: the pipeline's theme recolour also hit colours
it should never touch (frog's white body -> black, yellow duck ->
purple, muted money-green -> purple). Match shapes between the
ORIGINAL and the current FINAL by path signature; when a shape's
original fill belongs to a protected class and the final fill is
in the purple/dark replacement range, restore the original fill.

Classes:
  WHITE->DARK   orig min>=0.90, final max<=0.35
  YELLOW->PURP  orig hue 30..70, final hue 240..320
  MGREEN->PURP  orig hue 60..170 & sat<=0.50, final hue 240..320
Outputs to final_fix3/ only.
Usage: fix3_colour.py <idx> [<idx> ...]
"""
import colorsys
import copy
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import dump_tgs, load_tgs, shape_signature

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix3")
os.makedirs(STAGE, exist_ok=True)


def _fill_colour(fl):
    """(r, g, b) floats of a fill item: static or first keyframe."""
    c = (fl.get("c") or {}).get("k")
    if isinstance(c, list) and c and isinstance(c[0], (int, float)):
        return tuple(c[:3])
    if isinstance(c, list) and c and isinstance(c[0], dict):
        s = c[0].get("s") or []
        if len(s) >= 3:
            return tuple(s[:3])
    return None


def _group_fills(doc):
    """yield (signature, fl_item) for every sh+fl pair in a group."""
    out = []

    def walk(items):
        sigs = []
        for it in items:
            if not isinstance(it, dict):
                continue
            if it.get("ty") == "sh":
                try:
                    sigs.append(shape_signature(it))
                except Exception:
                    pass
            elif it.get("ty") == "gr":
                walk(it.get("it") or [])
        for it in items:
            if isinstance(it, dict) and it.get("ty") == "fl":
                for s in sigs:
                    out.append((s, it))

    def walk_layers(layers):
        for layer in layers:
            if layer.get("ty") == 4:
                walk(layer.get("shapes") or [])

    walk_layers(doc.get("layers") or [])
    for a in doc.get("assets") or []:
        walk_layers(a.get("layers") or [])
    return out


def _class_of(orig, fin):
    if orig is None or fin is None:
        return None
    oh, _ol, osat = colorsys.rgb_to_hls(*orig)
    fh, _fl2, _fs = colorsys.rgb_to_hls(*fin)
    ohd, fhd = oh * 360, fh * 360
    if min(orig) >= 0.90 and max(fin) <= 0.35:
        return "WHITE->DARK"
    if 30 <= ohd <= 70 and 240 <= fhd <= 320:
        return "YELLOW->PURP"
    if 60 <= ohd <= 170 and osat <= 0.50 and 240 <= fhd <= 320:
        return "MGREEN->PURP"
    return None


def main():
    man = json.load(open(os.path.join(BASE, "manifest.json")))
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    for arg in sys.argv[1:]:
        idx = int(arg)
        orig = load_tgs(open(man["items"][idx - 1]["local_path"], "rb").read())
        fin = load_tgs(open(finals[idx], "rb").read())
        omap = {}
        for sig, fl in _group_fills(orig):
            col = _fill_colour(fl)
            if col is not None:
                omap.setdefault(sig, (col, copy.deepcopy(fl.get("c"))))
        counts = {}
        changed = 0
        for sig, fl in _group_fills(fin):
            if sig not in omap:
                continue
            ocol, ocprop = omap[sig]
            fcol = _fill_colour(fl)
            cls = _class_of(ocol, fcol)
            if cls:
                fl["c"] = copy.deepcopy(ocprop)
                counts[cls] = counts.get(cls, 0) + 1
                changed += 1
        out = os.path.join(STAGE, os.path.basename(finals[idx]))
        with open(out, "wb") as fh:
            fh.write(dump_tgs(fin))
        print(f"{idx}: restored {changed} fills {counts} -> staged")


if __name__ == "__main__":
    main()
