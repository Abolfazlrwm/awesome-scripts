"""Contact sheets (original over current final) for an explicit
list of file indices — the fix-round verification sweep."""
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from PIL import Image, ImageDraw
from rlottie_python import LottieAnimation

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
FLAGGED = [2, 7, 10, 13, 15, 17, 18, 19, 22, 23, 24, 26, 30, 32, 33,
           40, 50, 68, 75, 76, 83, 86, 92, 97, 98, 103, 108, 111, 112,
           113, 117, 119, 122, 123, 125, 134, 143, 145, 146, 149, 150,
           151, 152, 153, 154, 157, 159, 161, 162, 164, 166, 170, 171,
           185, 187, 188, 192, 194, 195]


def frame_of(path, frac=0.4):
    anim = LottieAnimation.from_tgs(path)
    total = anim.lottie_animation_get_totalframe()
    return anim.render_pillow_frame(int(total * frac)).convert("RGB")


def main():
    man = json.load(open(os.path.join(BASE, "manifest.json")))
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    per_sheet = 20
    cell_w, cell_h = 150, 306
    for s in range(0, len(FLAGGED), per_sheet):
        batch = FLAGGED[s:s + per_sheet]
        cols = 5
        rows = (len(batch) + cols - 1) // cols
        sheet = Image.new("RGB", (cols * cell_w, rows * cell_h), "white")
        d = ImageDraw.Draw(sheet)
        for k, idx in enumerate(batch):
            r, c = divmod(k, cols)
            x, y = c * cell_w, r * cell_h
            try:
                o = frame_of(man["items"][idx - 1]["local_path"])
                o.thumbnail((cell_w - 4, 148))
                sheet.paste(o, (x + 2, y + 14))
            except Exception:
                pass
            fp = finals.get(idx)
            if fp:
                try:
                    n = frame_of(fp)
                    n.thumbnail((cell_w - 4, 148))
                    sheet.paste(n, (x + 2, y + 164))
                except Exception:
                    pass
            d.text((x + 4, y + 2), f"{idx}", fill="black")
        out = f"/home/hatch/workspace/your_files/fixsweep_{batch[0]:03d}_{batch[-1]:03d}.png"
        sheet.save(out)
        print("saved", out)


if __name__ == "__main__":
    main()
