"""Fix-5 apply: put the Abolfazl_rwm logo into the ORIGINAL mark slots.

Modes:
  SHELL — rebuild the mark's core group in the final with logo
          contents (same parent chain / transform / stacking),
          using the verified mark from fix5_audit.json.
  SCENE — remove old logo groups; add a top layer holding the
          halo logo fitted to a scene box (static scenes).
  ADD   — like SHELL but keeps the file's existing logo groups.

Output: final_fix5/ staging. Usage:
  python scripts/fix5_apply.py [idx ...]   (default: full plan)
"""
import copy
import glob
import importlib.util
import json
import os
import sys

os.environ["FIX4_HALO"] = "1"
sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs, dump_tgs   # noqa: E402
from emoji_forge import v5                                    # noqa: E402

spec = importlib.util.spec_from_file_location(
    "fix4_logo", os.path.join(os.path.dirname(__file__), "fix4_logo.py"))
fix4 = importlib.util.module_from_spec(spec)
spec.loader.exec_module(fix4)

BASE = os.path.join(os.path.dirname(__file__), "..", "data",
                    "projects", "forge-20261029-072531-16e1fc")
STAGE = os.path.join(BASE, "final_fix5")
BRAND_NAMES = {"Abolfazl_rwm", "Abolfazl", "Abolfazl Brand"}
LOGO_NAMES = {"Abolfazl_rwm Logo Black", "Abolfazl_rwm Logo Cyan", "Abolfazl_rwm Logo Shadow"}
TR0 = {"ty": "tr", "p": {"a": 0, "k": [0, 0]}, "a": {"a": 0, "k": [0, 0]},
       "s": {"a": 0, "k": [100, 100]}, "r": {"a": 0, "k": 0},
       "o": {"a": 0, "k": 100}, "sk": {"a": 0, "k": 0},
       "sa": {"a": 0, "k": 0}, "nm": "Transform"}

# idx -> (mode, scene box or None)
PLAN = {
    5:   ("ADD", None),
    16:  ("SHELL", None),
    17:  ("SCENE", [123, 288, 242, 371]),
    30:  ("SHELL", None),
    34:  ("SHELL", None),
    39:  ("SHELL", None),
    57:  ("SHELL", None),
    66:  ("SHELL", None),
    75:  ("SHELL", None),
    78:  ("SHELL", None),
    119: ("SHELL", None),
    122: ("SCENE", [152, 421, 336, 512]),
    133: ("SHELL", None),
    135: ("SHELL", None),
    157: ("SCENE", [195, 185, 335, 335]),
    163: ("SHELL", None),
    169: ("SHELL", None),
    187: ("SCENE", [55, 105, 430, 205]),
    188: ("SCENE", [15, 200, 495, 295]),
    193: ("SCENE", [275, 295, 335, 365]),
}


def _has_logo(group):
    for it in group.get("it") or []:
        if isinstance(it, dict):
            if it.get("nm") in LOGO_NAMES:
                return True
            if it.get("ty") == "gr" and _has_logo(it):
                return True
    return False


def remove_logo_groups(doc):
    """Drop every brand group that holds logo subgroups, everywhere."""
    n = 0

    def clean(items):
        nonlocal n
        out = []
        for it in items:
            if isinstance(it, dict) and it.get("ty") == "gr":
                if it.get("nm") in BRAND_NAMES and _has_logo(it):
                    n += 1
                    continue
                if it.get("nm") in LOGO_NAMES:
                    n += 1
                    continue
                it["it"] = clean(it.get("it") or [])
            out.append(it)
        return out

    def clean_layer(layer):
        if layer.get("ty") == 4:
            layer["shapes"] = clean(layer.get("shapes") or [])

    for layer in doc.get("layers") or []:
        clean_layer(layer)
    for asset in doc.get("assets") or []:
        for layer in asset.get("layers") or []:
            clean_layer(layer)
    return n


def logo_group_for_box(fx, fy, fw, fh, flip=False):
    if flip:
        items = fix4._logo_items((-(fx + fw), fy, -fx, fy + fh))
        wrap = {"ty": "gr", "it": items + [dict(TR0, s={"a": 0, "k": [-100, 100]})],
                "nm": "Abolfazl_rwm", "bm": 0, "hd": False}
        return wrap
    return {"ty": "gr", "it": fix4._logo_items((fx, fy, fx + fw, fy + fh))
            + [copy.deepcopy(TR0)],
            "nm": "Abolfazl_rwm", "bm": 0, "hd": False}


def scene_insert(doc, box):
    x0, y0, x1, y1 = box
    grp = logo_group_for_box(x0, y0, x1 - x0, y1 - y0)
    inds = [l.get("ind", 0) for l in doc.get("layers") or []]
    layer = {
        "ty": 4, "nm": "Abolfazl_rwm Logo Layer", "ind": (max(inds) + 1 if inds else 1),
        "ks": {"o": {"a": 0, "k": 100}, "r": {"a": 0, "k": 0},
               "p": {"a": 0, "k": [0, 0, 0]}, "a": {"a": 0, "k": [0, 0, 0]},
               "s": {"a": 0, "k": [100, 100, 100]}},
        "shapes": [grp], "ip": 0, "op": doc.get("op", 180), "st": 0,
        "bm": 0, "hd": False, "ddd": 0, "ao": 0}
    doc.setdefault("layers", []).insert(0, layer)


def find_mark(odoc, ref):
    cands = v5._dedupe_marks(v5._find_white_mark_groups(odoc)
                             + v5._find_mark_candidates_deep(odoc))
    for mk in cands:
        if (mk["scope"] == ref["scope"]
                and mk["layer"].get("ind") == ref["layer_ind"]
                and list(mk.get("path") or (0,)) == ref["path"]):
            return mk
    return None


def navigate_core(doc, scope, layer_ind, path):
    layers = v5._scope_layers(doc, scope)
    for layer in layers:
        if layer.get("ind") != layer_ind or layer.get("ty") != 4:
            continue
        items = layer.get("shapes") or []
        node = None
        ok = True
        for j, p in enumerate(path):
            if p >= len(items) or not isinstance(items[p], dict):
                ok = False
                break
            node = items[p]
            if j < len(path) - 1:
                items = node.get("it") or []
        if not ok or node is None or node.get("ty") != "gr":
            continue
        # descend single-child wrappers to the core, as the finder did
        core = node
        while True:
            subs = [s for s in (core.get("it") or [])
                    if isinstance(s, dict) and s.get("ty") != "tr"]
            if len(subs) == 1 and subs[0].get("ty") == "gr":
                core = subs[0]
            else:
                break
        return core
    return None


def shell_insert(fdoc, odoc, ref):
    mk = find_mark(odoc, ref)
    if mk is None:
        return False
    core = navigate_core(fdoc, ref["scope"], ref["layer_ind"], ref["path"])
    if core is None:
        return False
    lb = mk["local_bbox"]
    _w, _h, matrix, _f, _op = v5._mark_visible_box(odoc, mk)
    flip = v5._mat_det(matrix) < 0
    grp = logo_group_for_box(lb[0], lb[1], lb[2] - lb[0], lb[3] - lb[1],
                             flip=flip)
    tr_items = [s for s in (core.get("it") or [])
                if isinstance(s, dict) and s.get("ty") == "tr"]
    core["it"] = [grp] + tr_items
    core["nm"] = "Abolfazl_rwm"
    return True


def main():
    audit = json.load(open(os.path.join(BASE, "fix5_audit.json")))
    os.makedirs(STAGE, exist_ok=True)
    targets = {int(x) for x in sys.argv[1:]} or set(PLAN)
    for idx in sorted(targets):
        mode, sbox = PLAN[idx]
        ff = glob.glob(os.path.join(BASE, "final", f"*_{idx:03d}_*.tgs"))[0]
        of = glob.glob(os.path.join(BASE, "originals", f"*_{idx:03d}_*.tgs"))[0]
        fdoc = load_tgs(open(ff, "rb").read())
        odoc = load_tgs(open(of, "rb").read())
        removed = 0
        if mode != "ADD":
            removed = remove_logo_groups(fdoc)
        done = False
        if mode in ("SHELL", "ADD"):
            unmatched = audit.get(str(idx), {}).get("unmatched") or []
            ref = None
            if unmatched:
                ref = {k: unmatched[0][k] for k in
                       ("scope", "layer_ind", "path", "local_bbox")}
            if ref and shell_insert(fdoc, odoc, ref):
                done = True
            elif unmatched:
                b = unmatched[0]["box"]
                scene_insert(fdoc, b)
                done = True
                print(f"{idx}: shell failed -> scene fallback {b}")
        if mode == "SCENE":
            scene_insert(fdoc, sbox)
            done = True
        out = os.path.join(STAGE, os.path.basename(ff))
        with open(out, "wb") as fh:
            fh.write(dump_tgs(fdoc))
        print(f"{idx}: mode={mode} removed={removed} ok={done} "
              f"-> {os.path.getsize(out)} B", flush=True)


if __name__ == "__main__":
    main()
