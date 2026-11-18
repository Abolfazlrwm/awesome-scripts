"""Fix-5 audit: for the fallback-kind files (text/logo NOT placed
in an original slot) plus the fix-3 manual files, find the ORIGINAL
brand marks in the original TGS (deep candidates + rlottie
removal verification) and compare their on-screen boxes with the
current logo boxes in final/. Writes fix5_audit.json with the
unmatched original slots for scripts/fix5_apply.py.

Usage: python scripts/fix5_audit.py [idx ...]
"""
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs          # noqa: E402
from emoji_forge import v5                                  # noqa: E402

BASE = os.path.join(os.path.dirname(__file__), "..", "data",
                    "projects", "forge-20261029-072531-16e1fc")
MANUAL = {11, 17, 34, 47, 103, 108, 163, 169, 187, 188, 193}


def scene_box(doc, mark):
    lb = mark["local_bbox"]
    _w, _h, m, _f, _op = v5._mark_visible_box(doc, mark)
    pts = [v5._mat_apply(m, x, y) for x, y in
           [(lb[0], lb[1]), (lb[2], lb[1]), (lb[0], lb[3]), (lb[2], lb[3])]]
    xs = [p[0] for p in pts]
    ys = [p[1] for p in pts]
    box = [round(min(xs)), round(min(ys)), round(max(xs)), round(max(ys))]
    if mark.get("measured"):
        mw, mh = mark["measured"]
        cx, cy = (box[0] + box[2]) / 2, (box[1] + box[3]) / 2
        box = [round(cx - mw / 2), round(cy - mh / 2),
               round(cx + mw / 2), round(cy + mh / 2)]
    return box


def logo_boxes(doc):
    """On-screen boxes of the 'Abolfazl_rwm Logo Black' groups in a final."""
    out = []

    def walk_items(items, mat, frame):
        for it in items:
            if not isinstance(it, dict) or it.get("ty") != "gr":
                continue
            m2 = v5._mat_mul(mat, v5._group_tr_matrix(it, frame))
            if it.get("nm") == "Abolfazl_rwm Logo Black":
                subs = [s for s in (it.get("it") or [])
                        if not (isinstance(s, dict) and s.get("ty") == "tr")]
                try:
                    pts = v5._group_bbox(subs, (1, 0, 0, 1, 0, 0))
                except Exception:
                    pts = None
                if pts:
                    xs = [p[0] for p in pts]
                    ys = [p[1] for p in pts]
                    lb = (min(xs), min(ys), max(xs), max(ys))
                    sp = [v5._mat_apply(m2, x, y) for x, y in
                          [(lb[0], lb[1]), (lb[2], lb[1]),
                           (lb[0], lb[3]), (lb[2], lb[3])]]
                    sx = [p[0] for p in sp]
                    sy = [p[1] for p in sp]
                    out.append([round(min(sx)), round(min(sy)),
                                round(max(sx)), round(max(sy))])
                continue
            walk_items(it.get("it") or [], m2, frame)

    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or [])
               for a in doc.get("assets") or []]
    frame = int(doc.get("op", 180) * 0.5)
    for scope, layers in scopes:
        for layer in layers:
            if layer.get("ty") != 4:
                continue
            m, _op = v5._full_matrix(doc, scope, layer, frame)
            walk_items(layer.get("shapes") or [], m, frame)
    return out


def centre(b):
    return ((b[0] + b[2]) / 2, (b[1] + b[3]) / 2)


def main():
    scan = json.load(open(os.path.join(BASE, "scan_engine_rev2.json")))
    targets = set(scan["fallback"]) | MANUAL
    if len(sys.argv) > 1:
        targets = {int(x) for x in sys.argv[1:]}
    report = {}
    for idx in sorted(targets):
        of = glob.glob(os.path.join(BASE, "originals", f"*_{idx:03d}_*.tgs"))
        ff = glob.glob(os.path.join(BASE, "final", f"*_{idx:03d}_*.tgs"))
        if not of or not ff:
            continue
        odoc = load_tgs(open(of[0], "rb").read())
        cands = v5._dedupe_marks(
            v5._find_white_mark_groups(odoc)
            + v5._find_mark_candidates_deep(odoc))
        verified = v5._verify_marks_render(
            odoc, cands, min_changed=120, min_white=60)
        oboxes, refs = [], []
        for mk in verified:
            try:
                b = scene_box(odoc, mk)
            except Exception:
                continue
            if not (12 <= b[2] - b[0] <= 340 and 12 <= b[3] - b[1] <= 340):
                continue
            oboxes.append(b)
            refs.append({"scope": mk["scope"],
                         "layer_ind": mk["layer"].get("ind"),
                         "path": list(mk.get("path") or (0,)),
                         "local_bbox": [round(v, 2)
                                        for v in mk["local_bbox"]]})
        fdoc = load_tgs(open(ff[0], "rb").read())
        lboxes = logo_boxes(fdoc)
        unmatched = []
        for b, ref in zip(oboxes, refs):
            cx, cy = centre(b)
            hit = any(abs(centre(l)[0] - cx) <= 70
                      and abs(centre(l)[1] - cy) <= 70 for l in lboxes)
            if not hit:
                unmatched.append({"box": b, **ref})
        report[idx] = {"orig_boxes": oboxes, "logo_boxes": lboxes,
                       "unmatched": unmatched}
        print(f"{idx}: orig={oboxes} logo={lboxes} "
              f"unmatched={len(unmatched)}", flush=True)
    out = os.path.join(BASE, "fix5_audit.json")
    json.dump(report, open(out, "w"), indent=1)
    bad = [i for i, r in report.items() if r["unmatched"]]
    print("BAD files:", bad)


if __name__ == "__main__":
    main()
