"""Manual bisect: find groups whose removal changes ONLY a target
screen region (the old-brand mark's location), remove them all,
verify the total diff stays inside the region, stage the result.

Usage: fix3_bisect.py <idx> <x0> <y0> <x1> <y1> [thresh]
Region is in 512x512 screen coords of the CURRENT final. Removal
is verified across 10 frames; a candidate qualifies when >= 65%
of its changed pixels (diff > thresh) fall inside the region and
it changes >= 120 px in at least one frame. The combined removal
must keep >= 80% of changed pixels inside the region (padded)."""
import copy
import glob
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import ImageChops

from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.v5 import (_mark_visible_box, _mat_apply,
                            _remove_group_at_path, _render_frames)
from fix3_post import _enum_candidates_any

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix3")
os.makedirs(STAGE, exist_ok=True)
FRACS = [i / 10 for i in range(10)]


def _diff_stats(base, var, region, thresh):
    """(changed_px, inside_fraction) aggregated over frames."""
    x0, y0, x1, y1 = region
    tot, inside = 0, 0
    for b, v in zip(base, var):
        d = ImageChops.difference(b, v).convert("L")
        bbox = d.point(lambda x: 255 if x > thresh else 0).getbbox()
        if bbox is None:
            continue
        region_d = d.crop(bbox)
        changed = sum(1 for x in region_d.getdata() if x > thresh)
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
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    doc = load_tgs(open(finals[idx], "rb").read())
    frames = sorted(set(int(doc.get("op", 180) * f) for f in FRACS))
    base = _render_frames(dump_tgs(doc), frames)
    if base is None:
        print("render failed")
        return
    cands = _enum_candidates_any(doc, cap=400, depth=4)
    # Cheap pre-filter: keep only candidates whose on-screen box
    # intersects the padded region (matrix math, no renders).
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
            continue
    print(f"{idx}: {len(near)}/{len(cands)} candidates near region")
    hits = []
    for cand in near:
        try:
            doc2 = copy.deepcopy(doc)
            _remove_group_at_path(doc2, cand["scope"],
                                  cand["layer"].get("ind"), cand["path"])
            var = _render_frames(dump_tgs(doc2), frames)
            if var is None:
                continue
            tot, frac = _diff_stats(base, var, region, thresh)
            if tot >= 120 and frac >= 0.65:
                hits.append((cand, tot, frac))
        except Exception:
            continue
    hits.sort(key=lambda h: -h[1])
    print(f"{idx}: {len(hits)} candidate(s) in region {region}")
    for cand, tot, frac in hits:
        print(f"  scope={cand['scope']} layer={cand['layer'].get('ind')} "
              f"path={cand['path']} nm={str(cand['group'].get('nm'))[:24]!r} "
              f"changed={tot} inside={frac:.2f}")
    if not hits:
        return
    # Apply all hits deepest-first, then verify the combined diff.
    doc3 = copy.deepcopy(doc)
    # Descending path order: descendants before ancestors, higher
    # sibling indices first — earlier removals never shift the
    # paths of later ones.
    ordered = sorted(hits, key=lambda h: (str(h[0]["scope"]),
                                          h[0]["layer"].get("ind"),
                                          h[0]["path"]),
                     reverse=True)
    applied = []
    seen = set()
    for cand, _t, _f in ordered:
        key = (cand["scope"], cand["layer"].get("ind"), cand["path"])
        if key in seen:
            continue
        seen.add(key)
        try:
            _remove_group_at_path(doc3, cand["scope"],
                                  cand["layer"].get("ind"), cand["path"])
            applied.append(cand)
        except Exception:
            pass
    var = _render_frames(dump_tgs(doc3), frames)
    pad = (max(0, region[0] - 26), max(0, region[1] - 26),
           min(512, region[2] + 26), min(512, region[3] + 26))
    tot, frac = _diff_stats(base, var, pad, thresh)
    print(f"  combined: changed={tot} inside(padded)={frac:.2f} "
          f"applied={len(applied)}")
    if frac >= 0.80 and tot >= 120:
        out = os.path.join(STAGE, os.path.basename(finals[idx]))
        with open(out, "wb") as fh:
            fh.write(dump_tgs(doc3))
        print(f"  STAGED -> {out}")
    else:
        print("  NOT staged (combined diff escapes the region)")


if __name__ == "__main__":
    main()
