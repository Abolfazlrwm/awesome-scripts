"""Dense before/after: ORIGINAL (top) vs current FINAL (bottom),
10 frames each, for manual-batch diagnosis."""
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from PIL import Image, ImageDraw
from rlottie_python import LottieAnimation

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)


def frames_of(path, n=10):
    anim = LottieAnimation.from_tgs(path)
    total = anim.lottie_animation_get_totalframe()
    return [anim.render_pillow_frame(int(total * i / n)).convert("RGB")
            for i in range(n)]


def main():
    man = json.load(open(os.path.join(BASE, "manifest.json")))
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    idxs = [int(x) for x in sys.argv[1:]]
    cell = 128
    sheet = Image.new("RGB", (cell * 5, len(idxs) * (cell * 2 + 14)), "white")
    d = ImageDraw.Draw(sheet)
    for k, idx in enumerate(idxs):
        y = k * (cell * 2 + 14)
        d.text((4, y + 2), f"{idx} orig/final dense", fill="black")
        rows = [frames_of(man["items"][idx - 1]["local_path"]),
                frames_of(finals[idx])]
        for r, row in enumerate(rows):
            for j in range(5):
                im = row[j * 2]
                im.thumbnail((cell - 4, cell - 4))
                sheet.paste(im, (j * cell + 2, y + 12 + r * cell))
    out = "/home/hatch/workspace/your_files/dense_check.png"
    sheet.save(out)
    print("saved", out)


if __name__ == "__main__":
    main()
