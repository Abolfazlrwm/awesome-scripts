"""Swap every 'Abolfazl' TEXT brand group in a file for Ehsan's
Abolfazl_rwm LOGO (vectorised in fix4_logo_prep.py), fitted into the
exact box the text occupied — same parent, same slot, same
animation. Stages to final_fix4/.
Usage: fix4_logo.py <idx> [<idx> ...]
"""
import copy
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import dump_tgs, load_tgs

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, os.environ.get("FIX4_STAGE", "final_fix4"))
os.makedirs(STAGE, exist_ok=True)
LOGO = json.load(open(os.path.join(os.path.dirname(__file__), "..",
                                   "data", "logo_e30tz.json")))
TR0 = {"ty": "tr", "p": {"a": 0, "k": [0, 0]}, "a": {"a": 0, "k": [0, 0]},
       "s": {"a": 0, "k": [100, 100]}, "r": {"a": 0, "k": 0},
       "o": {"a": 0, "k": 100}, "sk": {"a": 0, "k": 0},
       "sa": {"a": 0, "k": 0}, "nm": "Transform"}


def _fill(rgb):
    return {"ty": "fl",
            "c": {"a": 0, "k": [round(rgb[0], 3), round(rgb[1], 3),
                                round(rgb[2], 3), 1]},
            "o": {"a": 0, "k": 100}, "r": 1, "bm": 0,
            "nm": "Fill 1", "hd": False}


PLATE = os.environ.get("FIX4_PLATE") == "1"
HALO = os.environ.get("FIX4_HALO") == "1"


def _plate_group(box):
    x0, y0, x1, y1 = box
    w, h = x1 - x0, y1 - y0
    pad_x, pad_y = h * 0.28, h * 0.22
    items = [{
        "ty": "rc",
        "p": {"a": 0, "k": [round((x0 + x1) / 2, 2), round((y0 + y1) / 2, 2)]},
        "s": {"a": 0, "k": [round(w + 2 * pad_x, 2), round(h + 2 * pad_y, 2)]},
        "r": {"a": 0, "k": round(h * 0.55, 2)},
        "nm": "Plate", "hd": False},
        {"ty": "fl", "c": {"a": 0, "k": [0.96, 0.97, 0.99, 1]},
         "o": {"a": 0, "k": 96}, "r": 1, "bm": 0, "nm": "Fill 1",
         "hd": False},
        copy.deepcopy(TR0)]
    return {"ty": "gr", "it": items, "nm": "Abolfazl_rwm Logo Plate",
            "bm": 0, "hd": False}


def _logo_items(box):
    """Two groups (black body, cyan glow) fitted into box."""
    x0, y0, x1, y1 = box
    fw, fh = LOGO["frame_w"], LOGO["frame_h"]
    sc = min((x1 - x0) / fw, (y1 - y0) / fh)
    cx, cy = (x0 + x1) / 2, (y0 + y1) / 2

    def xf(pt):
        return [round(cx + (pt[0] - fw / 2) * sc, 2),
                round(cy + (pt[1] - fh / 2) * sc, 2)]

    def xr(pt):
        return [round(pt[0] * sc, 2), round(pt[1] * sc, 2)]

    drawn_h = fh * sc
    groups = []
    for key, nm in (("black", "Abolfazl_rwm Logo Black"),
                    ("cyan", "Abolfazl_rwm Logo Cyan")):
        part = LOGO[key]
        items = []
        for sp in part["subpaths"]:
            items.append({
                "ty": "sh", "ks": {"a": 0, "k": {
                    "v": [xf(p) for p in sp["v"]],
                    "i": [xr(p) for p in sp["i"]],
                    "o": [xr(p) for p in sp["o"]],
                    "c": sp["c"]}},
                "nm": "Path 1", "hd": False})
        items.append(_fill(part["rgb"]))
        if HALO and key == "black":
            # light outline so the black letterforms read on any
            # background (Ehsan: 'estroke roshan, kamel khana')
            items.append({
                "ty": "st",
                "c": {"a": 0, "k": [0.93, 0.97, 1.0, 1]},
                "o": {"a": 0, "k": 100},
                "w": {"a": 0, "k": round(max(1.2, drawn_h * 0.075), 2)},
                "lc": 2, "lj": 2, "bm": 0, "nm": "Stroke 1",
                "hd": False})
        items.append(copy.deepcopy(TR0))
        groups.append({"ty": "gr", "it": items, "nm": nm,
                       "bm": 0, "hd": False})
    if HALO:
        # soft drop shadow: a dark copy of the letterforms, offset
        # down-right, drawn UNDER everything (last in list = bottom)
        dx, dy = drawn_h * 0.030, drawn_h * 0.048
        items = []
        for sp in LOGO["black"]["subpaths"]:
            items.append({
                "ty": "sh", "ks": {"a": 0, "k": {
                    "v": [[round(p[0] + dx, 2), round(p[1] + dy, 2)]
                          for p in (xf(q) for q in sp["v"])],
                    "i": [xr(p) for p in sp["i"]],
                    "o": [xr(p) for p in sp["o"]],
                    "c": sp["c"]}},
                "nm": "Path 1", "hd": False})
        items.append({"ty": "fl", "c": {"a": 0, "k": [0.01, 0.01, 0.04, 1]},
                      "o": {"a": 0, "k": 55}, "r": 1, "bm": 0,
                      "nm": "Fill 1", "hd": False})
        items.append(copy.deepcopy(TR0))
        groups.append({"ty": "gr", "it": items, "nm": "Abolfazl_rwm Logo Shadow",
                       "bm": 0, "hd": False})
    if PLATE:
        # Lottie draws earlier group items ON TOP: the plate must
        # come LAST in the list to sit under the letters.
        groups.append(_plate_group(box))
    return groups


def _group_box(items):
    """Union bbox of the group's GLYPH shapes only. Brand groups can
    carry a scene-sized helper shape (e.g. a fallback banner rect);
    glyph shapes share a similar height, so drop height outliers
    against the median before unioning."""
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
    if len(boxes) > 2:
        heights = sorted(b[3] - b[1] for b in boxes)
        med = heights[len(heights) // 2] or 1
        kept = [b for b in boxes
                if 0.35 * med <= (b[3] - b[1]) <= 2.8 * med]
        if kept:
            boxes = kept
    return (min(b[0] for b in boxes), min(b[1] for b in boxes),
            max(b[2] for b in boxes), max(b[3] for b in boxes))


def _swap_in_items(items):
    n = 0
    for it in items:
        if not isinstance(it, dict):
            continue
        if it.get("ty") == "gr":
            if it.get("nm") in ("Abolfazl_rwm", "Abolfazl", "Abolfazl Brand"):
                box = _group_box(it.get("it") or [])
                if box and box[2] - box[0] > 4 and box[3] - box[1] > 2:
                    it["it"] = _logo_items(box)
                    n += 1
                continue
            n += _swap_in_items(it.get("it") or [])
    return n


def swap_doc(doc):
    n = 0
    def wl(layers):
        nonlocal n
        for layer in layers:
            if layer.get("ty") == 4:
                n += _swap_in_items(layer.get("shapes") or [])
    wl(doc.get("layers") or [])
    for a in doc.get("assets") or []:
        wl(a.get("layers") or [])
    return n


def main():
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    for arg in sys.argv[1:]:
        idx = int(arg)
        doc = load_tgs(open(finals[idx], "rb").read())
        n = swap_doc(doc)
        out = os.path.join(STAGE, os.path.basename(finals[idx]))
        with open(out, "wb") as fh:
            fh.write(dump_tgs(doc))
        print(f"{idx}: swapped {n} brand group(s) -> staged "
              f"({os.path.getsize(out)} B)")


if __name__ == "__main__":
    main()
