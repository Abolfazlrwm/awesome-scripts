"""Fix-10: in-scene restoration for files 052 and 176.

176: restore the deleted 'LOGO PLACE' layer (wrapped_content ind=19)
     and its asset comp_0 from the ORIGINAL; inside comp_0 replace the
     injected mark artwork with the halo E30TZ logo contain-fitted
     into the mark's drawn local box, expressed in the layer's own
     shape space (original layer ks kept). Drop the inert
     'TZ Emojis Brand' layer inside comp_0_1 (renders 0 px).
052: refill the emptied precomp mark layer (asset
     {e4ce82d4-...} ind=1) with the halo logo fitted to the mark's
     drawn local box (layer ks kept, box inverse-mapped into shape
     space); transplant the gutted hand layer's (top ind=10) shapes
     from final_backup_fix3 (intact, colours identical to original);
     remove the bottom fallback 'TZ Emojis Brand' top layer ind=12.

Stages to final_fix10/. final/ is NOT touched.
"""
import copy
import importlib.util
import json
import os
import sys

os.environ["FIX4_HALO"] = "1"  # must precede fix4_logo import

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
from emoji_forge.animation_engine import dump_tgs, load_tgs  # noqa: E402

_spec = importlib.util.spec_from_file_location(
    "fix4_logo", os.path.join(os.path.dirname(__file__), "fix4_logo.py"))
fix4 = importlib.util.module_from_spec(_spec)
_spec.loader.exec_module(fix4)
assert fix4.HALO, "halo variant required"

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix10")
os.makedirs(STAGE, exist_ok=True)

ORIG176 = os.path.join(BASE, "originals",
                       f"{PROJECT}_176_AgADBgcAAueKSUQ.tgs")
FIN176 = os.path.join(BASE, "final",
                      f"{PROJECT}_176_{PROJECT}_176_AgADBgc.tgs")
ORIG052 = os.path.join(BASE, "originals",
                       f"{PROJECT}_052_AgADBRcAAsiiMEQ.tgs")
FIN052 = os.path.join(BASE, "final",
                      f"{PROJECT}_052_{PROJECT}_052_AgADBRc.tgs")
BAK052_FIX3 = os.path.join(BASE, "final_backup_fix3",
                           f"{PROJECT}_052_{PROJECT}_052_AgADBRc.tgs")
AID052 = "{e4ce82d4-c14b-4c82-ad7c-1842abbccf9e}"


def load(path):
    return load_tgs(open(path, "rb").read())


def asset(doc, aid):
    for a in doc.get("assets") or []:
        if a.get("id") == aid:
            return a
    raise KeyError(aid)


def inv_box(box, ks):
    """Map an asset-space box into the layer's shape space, inverting
    the layer ks (static, r=0): shape = a + (asset - p) / (s/100)."""
    def k0(prop):
        return (prop or {}).get("k")
    p = k0(ks.get("p")) or [0, 0]
    a = k0(ks.get("a")) or [0, 0]
    s = k0(ks.get("s")) or [100, 100]
    sx = (s[0] if isinstance(s, list) else s) / 100.0
    sy = (s[1] if isinstance(s, list) and len(s) > 1 else s[0]) / 100.0
    x0, y0, x1, y1 = box
    return (a[0] + (x0 - p[0]) / sx, a[1] + (y0 - p[1]) / sy,
            a[0] + (x1 - p[0]) / sx, a[1] + (y1 - p[1]) / sy)


# ---------------------------------------------------------------- 176
def build_176():
    odoc, fdoc = load(ORIG176), load(FIN176)
    # mark's drawn local box inside comp_0 (standalone rlottie probe,
    # big-canvas, alpha>10): (0,23)-(503,438)
    mark_box = (0, 23, 503, 438)

    new_comp0 = copy.deepcopy(asset(odoc, "comp_0"))
    layer1 = new_comp0["layers"][0]
    assert layer1.get("ind") == 1
    shape_box = inv_box(mark_box, layer1["ks"])
    layer1["shapes"] = fix4._logo_items(shape_box)
    layer1["nm"] = "E30TZ Logo"
    layer1["op"] = 120  # asset/doc op; was 180 (dead range), brand rule
    # layers 2..6 are empty placeholders in the original; keep verbatim.

    # insert comp_0 at assets index 0 (original order)
    fdoc["assets"].insert(0, new_comp0)

    # restore LOGO PLACE layer ind=19 at wrapped_content list index 18
    owc = asset(odoc, "wrapped_content")
    layer19 = copy.deepcopy(
        [l for l in owc["layers"] if l.get("ind") == 19][0])
    fwc = asset(fdoc, "wrapped_content")
    assert fwc["layers"][17].get("ind") == 18
    assert fwc["layers"][18].get("ind") == 20
    fwc["layers"].insert(18, layer19)

    # drop the inert 'TZ Emojis Brand' layer inside comp_0_1
    fc01 = asset(fdoc, "comp_0_1")
    before = len(fc01.get("layers") or [])
    fc01["layers"] = [l for l in fc01.get("layers") or []
                      if l.get("nm") != "TZ Emojis Brand"]
    dropped = before - len(fc01["layers"])

    out = os.path.join(STAGE, os.path.basename(FIN176))
    data = dump_tgs(fdoc)
    open(out, "wb").write(data)
    print(f"176: staged {out} ({len(data)} B) comp_0_1 brand layers "
          f"dropped={dropped} shape_box={tuple(round(v,1) for v in shape_box)}")
    return out


# ---------------------------------------------------------------- 052
def build_052():
    fdoc = load(FIN052)
    bdoc = load(BAK052_FIX3)
    # mark's drawn local box inside the precomp (standalone rlottie
    # probe of asset layer ind=1 only; analytic vertex box agreed):
    # (29,144)-(438,463)
    mark_box = (29, 144, 438, 463)

    fa = asset(fdoc, AID052)
    layer1 = [l for l in fa["layers"] if l.get("ind") == 1][0]
    assert len(layer1.get("shapes") or []) == 1  # the empty shell
    shape_box = inv_box(mark_box, layer1["ks"])
    layer1["shapes"] = fix4._logo_items(shape_box)

    # transplant hand layer (top ind=10) shapes from final_backup_fix3
    def top10(doc):
        return [l for l in doc["layers"] if l.get("ind") == 10][0]
    f10, b10 = top10(fdoc), top10(bdoc)
    assert len(f10.get("shapes") or []) == 0, "final ind=10 not gutted?"
    assert len(b10.get("shapes") or []) == 2, "backup ind=10 not intact?"
    assert json.dumps(f10["ks"], sort_keys=True) == \
        json.dumps(b10["ks"], sort_keys=True), "ind=10 ks mismatch"
    f10["shapes"] = copy.deepcopy(b10["shapes"])

    # remove the bottom fallback brand layer ind=12
    before = len(fdoc["layers"])
    fdoc["layers"] = [l for l in fdoc["layers"]
                      if not (l.get("ind") == 12
                              and l.get("nm") == "TZ Emojis Brand")]
    assert len(fdoc["layers"]) == before - 1, "brand layer not removed"

    out = os.path.join(STAGE, os.path.basename(FIN052))
    data = dump_tgs(fdoc)
    open(out, "wb").write(data)
    print(f"052: staged {out} ({len(data)} B) shape_box="
          f"{tuple(round(v,1) for v in shape_box)}")
    return out


if __name__ == "__main__":
    build_176()
    build_052()
