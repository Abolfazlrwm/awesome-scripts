#!/usr/bin/env python3
"""Numeric animation fidelity: old logo vs v7 logo vs v8 fitted logo.

Same measurement as scripts/fidelity_v7 (old element found with
v5's own selection logic; trajectories through the full transform
chain at every 5th frame), plus a CONTAINMENT metric for v8: at
every compared frame, how far (px) the new logo's on-screen bbox
extends beyond the OLD element's on-screen bbox on any side
(0 = fully inside). Writes /tmp/v8_qa/fidelity.json and
data/projects/forge-v8-test/fidelity.json and prints a table.
"""
import glob
import json
import math
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from emoji_forge.animation_engine import load_tgs, pack_common_signatures  # noqa: E402
from emoji_forge.v4 import _layer_shape_bbox  # noqa: E402
from fidelity_v7 import brand_element, compare, main_element  # noqa: E402

ORIG_DIR = "data/projects/forge-20261028-155740-3f1382/originals"
V7_DIR = "data/projects/forge-v7-test/final"
V8_DIR = "data/projects/forge-v8-test/final"
HIDE = set(json.load(open("data/projects/forge-v5-test/fingerprint.json"))["hide_layer_names"])


def containment(old_traj, new_traj):
    """Max per-side overflow (px) of the new element's bbox beyond
    the old element's bbox over frames where both are visible, and
    the share of compared frames with zero overflow."""
    by_frame = {r[0]: r for r in new_traj}
    worst = 0.0
    n = inside = 0
    for r in old_traj:
        m = by_frame.get(r[0])
        if m is None:
            continue
        if r[3] > 10 and r[4] > 10 and r[6] > 10 and m[3] > 10 and m[4] > 10 and m[6] > 10:
            n += 1
            ov = max(
                (m[1] + m[3] / 2) - (r[1] + r[3] / 2),
                (r[1] - r[3] / 2) - (m[1] - m[3] / 2),
                (m[2] + m[4] / 2) - (r[2] + r[4] / 2),
                (r[2] - r[4] / 2) - (m[2] - m[4] / 2))
            worst = max(worst, ov)
            if ov <= 0.5:
                inside += 1
    return {"frames": n, "max_overflow_px": round(worst, 2),
            "frames_inside_pct": round(100.0 * inside / n, 1) if n else 0.0}


def main():
    from emoji_forge.v7 import element_trajectory
    origs = sorted(glob.glob(os.path.join(ORIG_DIR, "*.tgs")))
    docs = [load_tgs(open(p, "rb").read()) for p in origs]
    sigs = pack_common_signatures(docs)

    def final_path(ddir, idx):
        hits = [p for p in glob.glob(os.path.join(ddir, "*.tgs"))
                if f"_{idx:03d}_" in os.path.basename(p)]
        return hits[0] if hits else None

    out = {}
    print(f"{'#':>2} {'v8 centre':>9} {'v8 traj':>8} {'v8 rot':>7} {'v8 opa':>7} "
          f"{'v8 logoW':>8} {'overflow':>8} {'inside%':>7} | {'v7 centre':>9} {'v7 logoW':>8}")
    for idx, path in enumerate(origs, 1):
        doc = docs[idx - 1]
        scope, layer, bbox = main_element(doc, sigs)
        frames = list(range(0, (doc.get("op") or 180) + 1, 5))
        old_traj = element_trajectory(doc, scope, layer, bbox, frames)
        res = {"element": f"{scope or 'top'}:{layer.get('nm') if layer else '?'}"}
        trajs = {}
        for tag, ddir in (("v7", V7_DIR), ("v8", V8_DIR)):
            fdoc = load_tgs(open(final_path(ddir, idx), "rb").read())
            fs, fl, fb = brand_element(fdoc, {"EHSAN Logo"}, want_scope=scope)
            if fl is None:
                res[tag] = {"frames_compared": 0, "note": "brand element not found"}
                continue
            ntraj = element_trajectory(fdoc, fs, fl, fb, frames)
            trajs[tag] = ntraj
            res[tag] = compare(old_traj, ntraj)
        if "v8" in trajs:
            res["v8_containment"] = containment(old_traj, trajs["v8"])
        if "v7" in trajs:
            res["v7_containment"] = containment(old_traj, trajs["v7"])
        out[idx] = res
        v7, v8, ct = res.get("v7", {}), res.get("v8", {}), res.get("v8_containment", {})
        print(f"{idx:>2} {v8.get('centre_dev_px', '-'):>9} {v8.get('traj_dev_px', '-'):>8} "
              f"{v8.get('rot_dev_deg', '-'):>7} {v8.get('opacity_dev', '-'):>7} "
              f"{v8.get('new_median_width', '-'):>8} {ct.get('max_overflow_px', '-'):>8} "
              f"{ct.get('frames_inside_pct', '-'):>7} | {v7.get('centre_dev_px', '-'):>9} "
              f"{v7.get('new_median_width', '-'):>8}")
    os.makedirs("/tmp/v8_qa", exist_ok=True)
    with open("/tmp/v8_qa/fidelity.json", "w") as fh:
        json.dump(out, fh, ensure_ascii=False, indent=1)
    with open("data/projects/forge-v8-test/fidelity.json", "w") as fh:
        json.dump(out, fh, ensure_ascii=False, indent=1)
    print("wrote /tmp/v8_qa/fidelity.json")


if __name__ == "__main__":
    main()
