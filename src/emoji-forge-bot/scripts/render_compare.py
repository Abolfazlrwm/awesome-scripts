"""Render-compare every repaired final against its pre-fix backup.
Frames: first, middle, last. Metric: share of pixels whose RGB differs
by more than 2 levels. A repair is accepted only if the animation is
visually identical (brand-font swap in file 24 is reported separately
for the scene area above the brand strip)."""
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

import numpy as np

from emoji_forge.animation_engine import load_tgs
from qa_v5 import render_frame

ROOT = "/home/hatch/workspace/emoji-forge-ai/data/projects/forge-20261029-072531-16e1fc"
FINAL = os.path.join(ROOT, "final")
BACKUP = os.path.join(ROOT, "final_backup_before_fix")


KNOWN_SHAPE_TY = {"sh", "gr", "fl", "st", "tr", "el", "rc", "sr", "pb",
                  "gs", "gf", "mm", "tm", "rd", "op", "sk", "sa"}


def strip_unknown_shapes(o):
    """Remove nonstandard shape items (e.g. 'zbf' from the source
    pack's exporter) that the QA parser cannot load. Applied to BOTH
    versions equally, so the comparison stays valid for everything
    else; the repairs never modify shape contents anyway."""
    if isinstance(o, dict):
        return {k: strip_unknown_shapes(v) for k, v in o.items()}
    if isinstance(o, list):
        out = []
        for v in o:
            if isinstance(v, dict) and isinstance(v.get("ty"), str) \
                    and v.get("ty") not in KNOWN_SHAPE_TY:
                continue
            out.append(strip_unknown_shapes(v))
        return out
    return o


def normalize_for_qa(doc):
    """QA-only normalization, applied identically to both versions:
    strip nonstandard shapes and drop parent pointers whose target
    does not exist (the QA builder crashes on them; Telegram treats
    them as no-parent, which is what dropping encodes)."""
    doc = strip_unknown_shapes(doc)
    for scope in [doc.get("layers") or []] + \
            [a.get("layers") or [] for a in doc.get("assets") or []]:
        inds = {l.get("ind") for l in scope}
        for l in scope:
            if l.get("parent") is not None and l.get("parent") not in inds:
                del l["parent"]
    return doc


def render_any(path, frame, tmpdir):
    try:
        return render_frame(path, frame), False
    except Exception:
        from emoji_forge.animation_engine import dump_tgs
        doc = normalize_for_qa(load_tgs(open(path, "rb").read()))
        tmp = os.path.join(tmpdir, os.path.basename(path))
        with open(tmp, "wb") as fh:
            fh.write(dump_tgs(doc))
        return render_frame(tmp, frame), True


def diff_ratio(a, b):
    x = np.asarray(a, dtype=np.int16)
    y = np.asarray(b, dtype=np.int16)
    return float((np.abs(x - y).max(axis=2) > 2).mean())


def main():
    report = json.load(open(os.path.join(ROOT, "fix_report.json")))
    names = sorted(report.keys())
    worst = []
    for name in names:
        old = os.path.join(BACKUP, name)
        new = os.path.join(FINAL, name)
        doc = load_tgs(open(new, "rb").read())
        ip, op = int(doc.get("ip") or 0), int(doc.get("op") or 0)
        frames = sorted({ip, (ip + op) // 2, max(ip, op - 1)})
        ratios = []
        scene_ratios = []
        try:
            for fr in frames:
                import tempfile
                with tempfile.TemporaryDirectory() as td:
                    a, fa = render_any(old, fr, td)
                    b, fb = render_any(new, fr, td)
                if fa or fb:
                    print(f"  ({name}: fallback parser used)", flush=True)
                ratios.append(diff_ratio(a, b))
                # scene-only: exclude the bottom brand strip (56 of 256 rows)
                xa = np.asarray(a, dtype=np.int16)[:200]
                xb = np.asarray(b, dtype=np.int16)[:200]
                scene_ratios.append(float((np.abs(xa - xb).max(axis=2) > 2).mean()))
        except Exception as exc:
            print(f"{name}: UNVERIFIABLE ({type(exc).__name__}: {str(exc)[:120]})", flush=True)
            worst.append((9.9, 9.9, name + " [UNVERIFIABLE]"))
            continue
        worst.append((max(ratios), max(scene_ratios), name))
        print(f"{name}: max diff {max(ratios):.4f} | scene-only {max(scene_ratios):.4f}", flush=True)
    worst.sort(reverse=True)
    print("\nWORST 8:")
    for r, s, n in worst[:8]:
        print(f"  {n}: {r:.4f} (scene {s:.4f})")
    bad = [w for w in worst if w[1] > 0.01]
    print("files with scene diff >1%:", [w[2] for w in bad])


if __name__ == "__main__":
    main()
