"""Contact sheet: original (left) vs processed (right) for sample files."""
import os
import sys
import tempfile

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import Image, ImageDraw

from render_compare import render_any

SRC = "/home/hatch/workspace/emoji-forge-ai/data/projects/forge-20261029-072531-16e1fc"
DST = "/home/hatch/workspace/emoji-forge-ai/data/projects/forge-test-v8/final"
OUT = "/home/hatch/workspace/your_files/emoji-forge-v8-samples.png"
CELL = 256


def main():
    import json
    man = json.load(open(os.path.join(SRC, "manifest.json")))
    finals = sorted(os.listdir(DST))
    rows = []
    for name in finals:
        idx = int(name.split("_")[1])
        orig = man["items"][idx - 1]["local_path"]
        rows.append((idx, orig, os.path.join(DST, name)))
    sheet = Image.new("RGB", (CELL * 4 + 70, CELL * len(rows) + 10), "white")
    d = ImageDraw.Draw(sheet)
    with tempfile.TemporaryDirectory() as td:
        for r, (idx, orig, fin) in enumerate(rows):
            y = r * CELL + 5
            d.text((8, y + CELL // 2), str(idx), fill="black")
            for c, path in enumerate((orig, fin)):
                for half, fr in enumerate((10, 100)):
                    try:
                        im, _norm = render_any(path, fr, td)
                        im = im.convert("RGBA")
                    except Exception as exc:
                        im = Image.new("RGBA", (512, 512), (255, 0, 0, 255))
                        print("render fail", idx, path, exc)
                    im.thumbnail((CELL // 2 - 4, CELL - 4))
                    bg = Image.new("RGBA", im.size, (240, 240, 240, 255))
                    bg.alpha_composite(im)
                    sheet.paste(bg.convert("RGB"), (40 + c * CELL + half * (CELL // 2), y))
    sheet.save(OUT)
    print("saved", OUT, sheet.size)


if __name__ == "__main__":
    main()
