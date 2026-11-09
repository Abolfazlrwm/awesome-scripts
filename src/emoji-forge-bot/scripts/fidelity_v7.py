#!/usr/bin/env python3
"""Numeric animation fidelity: old logo vs v6 text vs v7 EHSAN logo.

For each file, the OLD logo element (found with v5's own selection
logic) and the brand element in the v6 and v7 finals are tracked at
every 5th frame through the full transform chain (v7.element_
trajectory). Reported per file:
  centre dev   - max on-screen centre distance (px) where both are
                 visible
  traj dev     - same after removing the median offset (pure movement
                 mismatch; a constant offset is placement, not motion)
  scale dev    - max |w/w_old - 1| in % (v7 is BIGGER BY DESIGN —
                 the user's rule; reported, not treated as error)
  rot dev      - max rotation difference in degrees (wrapped)
  opacity dev  - max opacity difference
File 016's second (alternating) brand slot is measured separately.
Writes /tmp/v7_qa/fidelity.json and prints a table.
"""
import glob
import json
import math
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs, pack_common_signatures  # noqa: E402
from emoji_forge.v4 import _group_bbox, _layer_shape_bbox, _scope_layers  # noqa: E402
from emoji_forge.v5 import _candidate_layers, _visible_median_box, _watermark_groups  # noqa: E402
from emoji_forge.v7 import classify_group, element_trajectory  # noqa: E402

ORIG_DIR = "data/projects/forge-20261028-155740-3f1382/originals"
V6_DIR = "data/projects/forge-v6-test/final"
V7_DIR = "data/projects/forge-v7-test/final"
HIDE = set(json.load(open("data/projects/forge-v5-test/fingerprint.json"))["hide_layer_names"])


def main_element(doc, sigs):
    """Mirror of v5's step-1 selection. Returns (scope, layer, local_bbox)."""
    cands = _candidate_layers(doc, set(HIDE))
    for cand in cands:
        try:
            w, h, _f = _visible_median_box(doc, cand["scope"], cand["layer"],
                                           _layer_shape_bbox(cand["layer"]))
            cand["area"] = w * h
        except Exception:
            cand["area"] = 0
    cands.sort(key=lambda c: (c["tier"], -c["area"]))
    if cands:
        c = cands[0]
        layer = c["layer"]
        # Composite main layer (logo group baked into a scene layer,
        # files 011/013): the brand element is the LOGO GROUP, not the
        # whole layer — measure against that group's bbox.
        groups = [g for g in layer.get("shapes") or []
                  if isinstance(g, dict) and g.get("ty") == "gr"]
        classes = [classify_group(doc, c["scope"], layer, g, sigs)
                   for g in groups]
        if "LOGO/BRAND" in classes and any(x != "LOGO/BRAND" for x in classes):
            g = groups[classes.index("LOGO/BRAND")]
            pts = _group_bbox([g], (1, 0, 0, 1, 0, 0))
            if pts:
                xs = [p[0] for p in pts]
                ys = [p[1] for p in pts]
                return c["scope"], layer, (min(xs), min(ys), max(xs), max(ys))
        return c["scope"], layer, _layer_shape_bbox(layer)
    wm = _watermark_groups(doc, sigs)
    if wm:
        def _ga(g):
            b = g["bbox"]
            return (b[2] - b[0]) * (b[3] - b[1]) if b else 0
        wm.sort(key=_ga, reverse=True)
        g = wm[0]
        layers = _scope_layers(doc, g["scope"])
        layer = next(l for l in layers if l.get("nm") == g["layer"].get("nm")
                     and l.get("ind") == g["layer"].get("ind"))
        return g["scope"], layer, g["bbox"]
    return None, None, None


def brand_element(doc, names, want_scope="__any__"):
    """Find a brand layer or group by name, preferring `want_scope`
    (files with a secondary slot have two brand elements; the main
    one lives in the old element's scope)."""
    scopes = [(None, doc.get("layers") or [])] + [
        (a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    ordered = sorted(scopes, key=lambda s: 0 if s[0] == want_scope else 1)
    for scope, layers in ordered:
        for layer in layers:
            if str(layer.get("nm", "")) in names:
                return scope, layer, _layer_shape_bbox(layer)
    for scope, layers in ordered:
        for layer in layers:
            for item in layer.get("shapes") or []:
                if isinstance(item, dict) and item.get("ty") == "gr" \
                        and str(item.get("nm", "")) in names:
                    pts = _group_bbox([item], (1, 0, 0, 1, 0, 0))
                    if pts:
                        xs = [p[0] for p in pts]
                        ys = [p[1] for p in pts]
                        return scope, layer, (min(xs), min(ys), max(xs), max(ys))
    return None, None, None


def compare(old_traj, new_traj):
    rows = []
    by_frame = {r[0]: r for r in new_traj}
    offs = []
    for r in old_traj:
        n = by_frame.get(r[0])
        if n is None:
            continue
        if r[3] > 10 and r[4] > 10 and r[6] > 10 and n[3] > 10 and n[4] > 10 and n[6] > 10:
            offs.append((n[1] - r[1], n[2] - r[2]))
            rows.append((r, n))
    if not rows:
        return {"frames_compared": 0}
    mdx = sorted(o[0] for o in offs)[len(offs) // 2]
    mdy = sorted(o[1] for o in offs)[len(offs) // 2]
    centre = max(math.hypot(n[1] - r[1], n[2] - r[2]) for r, n in rows)
    traj = max(math.hypot((n[1] - r[1]) - mdx, (n[2] - r[2]) - mdy) for r, n in rows)
    scale = max(max(abs(n[3] / r[3] - 1), abs(n[4] / r[4] - 1)) * 100
                for r, n in rows if r[3] > 20 and r[4] > 20)
    def wrap(a):
        while a > 180:
            a -= 360
        while a < -180:
            a += 360
        return abs(a)
    rot = max(wrap(n[5] - r[5]) for r, n in rows)
    opa = max(abs(n[6] - r[6]) for r, n in rows)
    wid = sorted(n[3] for r, n in rows)[len(rows) // 2]
    return {"frames_compared": len(rows), "centre_dev_px": round(centre, 2),
            "traj_dev_px": round(traj, 2), "scale_dev_pct": round(scale, 1),
            "rot_dev_deg": round(rot, 2), "opacity_dev": round(opa, 1),
            "median_offset": [round(mdx, 1), round(mdy, 1)],
            "new_median_width": round(wid, 1)}


def main():
    origs = sorted(glob.glob(os.path.join(ORIG_DIR, "*.tgs")))
    docs = [load_tgs(open(p, "rb").read()) for p in origs]
    sigs = pack_common_signatures(docs)

    def final_path(ddir, idx):
        hits = [p for p in glob.glob(os.path.join(ddir, "*.tgs"))
                if f"_{idx:03d}_" in os.path.basename(p)]
        return hits[0] if hits else None
    out = {}
    print(f"{'#':>2} {'v6 centre':>9} {'v6 traj':>8} {'v6 rot':>7} | "
          f"{'v7 centre':>9} {'v7 traj':>8} {'v7 rot':>7} {'v7 opa':>7} {'v7 logoW':>8}")
    for idx, path in enumerate(origs, 1):
        name = os.path.basename(path)
        doc = docs[idx - 1]
        scope, layer, bbox = main_element(doc, sigs)
        frames = list(range(0, (doc.get("op") or 180) + 1, 5))
        old_traj = element_trajectory(doc, scope, layer, bbox, frames)
        res = {"element": f"{scope or 'top'}:{layer.get('nm') if layer else '?'}"
                 }
        for tag, ddir, names in (("v6", V6_DIR, {"E30TZ Brand"}),
                                 ("v7", V7_DIR, {"EHSAN Logo"})):
            fdoc = load_tgs(open(final_path(ddir, idx), "rb").read())
            fs, fl, fb = brand_element(fdoc, names, want_scope=scope)
            if fl is None:
                res[tag] = {"frames_compared": 0, "note": "brand element not found"}
                continue
            ntraj = element_trajectory(fdoc, fs, fl, fb, frames)
            res[tag] = compare(old_traj, ntraj)
        # File 016 second slot: comp_1 CDC in the original.
        if idx == 16:
            o_assets = {a.get("id"): a for a in doc.get("assets") or []}
            cdc = next(l for l in o_assets["comp_1"]["layers"]
                       if l.get("nm") == "CDC Outlines")
            ot = element_trajectory(doc, "comp_1", cdc,
                                    _layer_shape_bbox(cdc), frames)
            res["slot2_old"] = {"frames": len(ot)}
            for tag, ddir, names in (("v6", V6_DIR, {"E30TZ Brand"}),
                                     ("v7", V7_DIR, {"EHSAN Logo"})):
                fdoc = load_tgs(open(final_path(ddir, idx), "rb").read())
                fa = next((a for a in fdoc.get("assets") or []
                           if a.get("id") == "comp_1"), None)
                lay = None
                if fa:
                    lay = next((l for l in fa.get("layers") or []
                                if str(l.get("nm", "")) in names), None)
                if lay is None:
                    res[f"slot2_{tag}"] = {"present": False}
                else:
                    nt = element_trajectory(fdoc, "comp_1", lay,
                                            _layer_shape_bbox(lay), frames)
                    res[f"slot2_{tag}"] = {"present": True, **compare(ot, nt)}
        out[idx] = res
        v6, v7 = res.get("v6", {}), res.get("v7", {})
        print(f"{idx:>2} {v6.get('centre_dev_px', '-'):>9} {v6.get('traj_dev_px', '-'):>8} "
              f"{v6.get('rot_dev_deg', '-'):>7} | {v7.get('centre_dev_px', '-'):>9} "
              f"{v7.get('traj_dev_px', '-'):>8} {v7.get('rot_dev_deg', '-'):>7} "
              f"{v7.get('opacity_dev', '-'):>7} {v7.get('new_median_width', '-'):>8}")
    os.makedirs("/tmp/v7_qa", exist_ok=True)
    with open("/tmp/v7_qa/fidelity.json", "w") as fh:
        json.dump(out, fh, ensure_ascii=False, indent=1)
    with open("data/projects/forge-v7-test/fidelity.json", "w") as fh:
        json.dump(out, fh, ensure_ascii=False, indent=1)
    print("wrote /tmp/v7_qa/fidelity.json")


if __name__ == "__main__":
    main()
