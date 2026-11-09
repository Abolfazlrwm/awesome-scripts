"""Prototype: render-verified white-mark detection on one file.

Candidates: dedicated layers (exactly one top-level group) whose
group holds >= 2 near-white paths. Verification: rlottie renders of
the doc with/without the candidate at sampled frames; the candidate
is a visible logo mark when removing it changes a compact region
that contains many near-white pixels in the original render.
"""
import copy
import json
import os
import sys
import tempfile

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from PIL import Image, ImageChops

from emoji_forge.animation_engine import load_tgs, dump_tgs
from emoji_forge.v5 import _white_stats


def render_frames(data: bytes, frames):
    from rlottie_python import LottieAnimation
    with tempfile.NamedTemporaryFile(suffix=".tgs", delete=False) as fh:
        fh.write(data)
        path = fh.name
    try:
        anim = LottieAnimation.from_tgs(path)
        return [anim.render_pillow_frame(f).convert("RGB") for f in frames]
    finally:
        os.unlink(path)


def candidates(doc):
    out = []
    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for layer in layers:
            if layer.get("ty") != 4:
                continue
            shapes = layer.get("shapes") or []
            if len(shapes) != 1:
                continue
            item = shapes[0]
            if not isinstance(item, dict) or item.get("ty") != "gr":
                continue
            try:
                _n, n_white, _pts = _white_stats(item.get("it") or [])
            except Exception:
                continue
            if n_white >= 2:
                out.append({"scope": scope, "layer": layer, "group": item})
    return out


def diff_stats(base_img, var_img):
    d = ImageChops.difference(base_img, var_img)
    gray = d.convert("L")
    bbox = gray.point(lambda v: 255 if v > 24 else 0).getbbox()
    if bbox is None:
        return 0, None, 0
    region = gray.crop(bbox)
    changed = sum(1 for v in region.getdata() if v > 24)
    orig_region = base_img.crop(bbox)
    whites = sum(1 for r, g, b in orig_region.getdata()
                 if r >= 200 and g >= 200 and b >= 200)
    return changed, bbox, whites


def main():
    man = json.load(open("data/projects/forge-20261029-072531-16e1fc/manifest.json"))
    idx = int(sys.argv[1]) if len(sys.argv) > 1 else 12
    raw = open(man["items"][idx - 1]["local_path"], "rb").read()
    doc = load_tgs(raw)
    op = doc.get("op", 180)
    frames = sorted(set(int(op * f) for f in (0, 0.25, 0.5, 0.75)))
    base = render_frames(raw, frames)
    cands = candidates(doc)
    print(f"file {idx}: {len(cands)} structural candidates")
    for c in cands:
        doc2 = copy.deepcopy(doc)
        # find the same layer in the copy by ind within the same scope
        layers2 = doc2.get("layers") or [] if c["scope"] is None else next(
            a.get("layers") or [] for a in doc2.get("assets") or []
            if a.get("id") == c["scope"])
        for l2 in layers2:
            if l2.get("ind") == c["layer"].get("ind"):
                l2["shapes"] = []
        var = render_frames(dump_tgs(doc2), frames)
        tot_changed = 0
        max_white = 0
        boxes = []
        for b_img, v_img in zip(base, var):
            changed, bbox, whites = diff_stats(b_img, v_img)
            tot_changed = max(tot_changed, changed)
            max_white = max(max_white, whites)
            if bbox:
                boxes.append(bbox)
        print(f"  scope={str(c['scope'])[:8]} layer={str(c['layer'].get('nm'))[:10]} "
              f"max_changed={tot_changed} max_white={max_white} boxes={boxes}")


if __name__ == "__main__":
    main()
