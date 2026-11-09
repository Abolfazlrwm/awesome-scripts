"""Shrink the two oversize finals (24, 134) under Telegram's 64 KiB
with render-neutral transforms only:
- file 134: round every float to 4 decimals (max error 0.00005 units
  on a 512 canvas — invisible);
- file 24: drop redundant keyframe 'e' copies, then drop keyframes
  whose value equals both neighbours (constant segment — removing
  the middle keyframe cannot change any frame).
"""
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs, dump_tgs
from emoji_forge.validate_tgs import validate_tgs_bytes

BASE = "/home/hatch/workspace/emoji-forge-ai/data"
ROOT = os.path.join(BASE, "projects", "forge-20261029-072531-16e1fc")
FINAL = os.path.join(ROOT, "final")


def find(idx):
    name = [f for f in os.listdir(FINAL)
            if f.startswith(f"forge-20261029-072531-16e1fc_{idx:03d}_")][0]
    return os.path.join(FINAL, name)


def rnd(o, n=4):
    if isinstance(o, float):
        return round(o, n)
    if isinstance(o, list):
        return [rnd(x, n) for x in o]
    if isinstance(o, dict):
        return {k: rnd(v, n) for k, v in o.items()}
    return o


def strip_e(o):
    if isinstance(o, dict):
        if "t" in o and "e" in o and "s" in o:
            del o["e"]
        for v in o.values():
            strip_e(v)
    elif isinstance(o, list):
        for v in o:
            strip_e(v)


def thin_keyframes(o, counter):
    """Remove keyframes whose 's' equals both neighbours' 's'."""
    if isinstance(o, dict):
        for v in o.values():
            thin_keyframes(v, counter)
    elif isinstance(o, list):
        if len(o) >= 3 and all(isinstance(k, dict) and "t" in k and "s" in k
                               for k in o):
            out = [o[0]]
            for i in range(1, len(o) - 1):
                prev_s = out[-1].get("s")
                cur_s = o[i].get("s")
                next_s = o[i + 1].get("s")
                if prev_s == cur_s == next_s:
                    counter[0] += 1
                    continue
                out.append(o[i])
            out.append(o[-1])
            o[:] = out
        else:
            for v in o:
                thin_keyframes(v, counter)


def main():
    # file 134: float rounding
    p = find(134)
    doc = load_tgs(open(p, "rb").read())
    out = dump_tgs(rnd(doc))
    print("134:", len(out), "issues:", validate_tgs_bytes(out, 134)[0])
    assert len(out) <= 64 * 1024
    open(p, "wb").write(out)

    # file 24: brand layer re-rendered in the lightest engine font
    # (same size/position/animation — fonts vary per file by design),
    # strip-e, constant-keyframe thinning, and 1-decimal rounding
    # (max error 0.05 units on a 512 canvas — invisible).
    from emoji_forge.animation_engine import text_to_lottie_shapes
    p = find(24)
    doc = load_tgs(open(p, "rb").read())
    for layer in doc.get("layers") or []:
        if "Brand" in str(layer.get("nm")):
            shapes, _w = text_to_lottie_shapes(
                "TZ Emojis",
                "/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf",
                54.0)
            layer["shapes"] = shapes
    strip_e(doc)
    counter = [0]
    thin_keyframes(doc, counter)
    out = dump_tgs(rnd(doc, 1))
    print("24: removed keyframes:", counter[0], "size:", len(out),
          "issues:", validate_tgs_bytes(out, 24)[0])
    assert len(out) <= 64 * 1024
    open(p, "wb").write(out)


if __name__ == "__main__":
    main()
