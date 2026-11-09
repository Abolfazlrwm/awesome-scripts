"""Diagnosis sheets for the residual-fix round: for each flagged
file, render original (top row of its block) and current final at
three timeline fractions so frame-dependent marks/texts show up."""
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from PIL import Image, ImageDraw
from rlottie_python import LottieAnimation

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
FLAGGED = [2, 7, 13, 15, 22, 23, 24, 26, 40, 50, 97, 103, 108, 111,
           112, 119, 123, 125, 134, 145, 146, 149, 150, 152, 154, 157,
           159, 161, 162, 164, 170, 171, 185, 187, 188, 192, 194, 195]
FRACS = [0.15, 0.5, 0.85]


def frames_of(path):
    anim = LottieAnimation.from_tgs(path)
    total = anim.lottie_animation_get_totalframe()
    out = []
    for fr in FRACS:
        out.append(anim.render_pillow_frame(int(total * fr)).convert("RGB"))
    return out


def main():
    man = json.load(open(os.path.join(BASE, "manifest.json")))
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        parts = os.path.basename(f).split("_")
        finals[int(parts[1])] = f
    cell = 128
    per_sheet = 6
    for s in range(0, len(FLAGGED), per_sheet):
        batch = FLAGGED[s:s + per_sheet]
        sheet = Image.new("RGB", (cell * 3, per_sheet * (cell * 2 + 16)), "white")
        d = ImageDraw.Draw(sheet)
        for k, idx in enumerate(batch):
            y = k * (cell * 2 + 16)
            d.text((4, y + 2), f"{idx} orig/final", fill="black")
            try:
                for j, im in enumerate(frames_of(man["items"][idx - 1]["local_path"])):
                    im.thumbnail((cell - 4, cell - 4))
                    sheet.paste(im, (j * cell + 2, y + 14))
            except Exception as exc:
                d.text((4, y + 30), f"orig err {exc}"[:40], fill="red")
            fp = finals.get(idx)
            if fp:
                try:
                    for j, im in enumerate(frames_of(fp)):
                        im.thumbnail((cell - 4, cell - 4))
                        sheet.paste(im, (j * cell + 2, y + 14 + cell))
                except Exception as exc:
                    d.text((4, y + 30 + cell), f"final err {exc}"[:40], fill="red")
        out = f"/home/hatch/workspace/your_files/fix2_diag_{batch[0]:03d}_{batch[-1]:03d}.png"
        sheet.save(out)
        print("saved", out)


if __name__ == "__main__":
    main()
