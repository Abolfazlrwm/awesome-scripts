"""Before/after sheets for staged fix3 files: current final (top)
vs staged fix (bottom) at three timeline fractions."""
import glob
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from PIL import Image, ImageDraw
from rlottie_python import LottieAnimation

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
FRACS = [0.15, 0.5, 0.85]


def frames_of(path):
    anim = LottieAnimation.from_tgs(path)
    total = anim.lottie_animation_get_totalframe()
    return [anim.render_pillow_frame(int(total * fr)).convert("RGB") for fr in FRACS]


def main():
    idxs = [int(x) for x in sys.argv[1:]]
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    staged = {}
    for f in glob.glob(os.path.join(BASE, "final_fix3", "*.tgs")):
        staged[int(os.path.basename(f).split("_")[1])] = f
    cell = 150
    sheet = Image.new("RGB", (cell * 3, len(idxs) * (cell * 2 + 16)), "white")
    d = ImageDraw.Draw(sheet)
    for k, idx in enumerate(idxs):
        y = k * (cell * 2 + 16)
        d.text((4, y + 2), f"{idx} final/staged", fill="black")
        for j, im in enumerate(frames_of(finals[idx])):
            im.thumbnail((cell - 4, cell - 4))
            sheet.paste(im, (j * cell + 2, y + 14))
        sp = staged.get(idx)
        if sp:
            for j, im in enumerate(frames_of(sp)):
                im.thumbnail((cell - 4, cell - 4))
                sheet.paste(im, (j * cell + 2, y + 14 + cell))
    out = "/home/hatch/workspace/your_files/fix3_check.png"
    sheet.save(out)
    print("saved", out)


if __name__ == "__main__":
    main()
