"""Render all finals (rlottie, 3 frames) into contact sheets for a
visual residual-mark hunt (MR / ShadiFar / StoreMehdi / M emblems)."""
import glob, os, sys
sys.path.insert(0, 'src')
from PIL import Image, ImageDraw
from rlottie_python import LottieAnimation
BASE = 'data/projects/forge-20261029-072531-16e1fc'
OUT = '/home/hatch/workspace/your_files/fix6_sheets'
os.makedirs(OUT, exist_ok=True)
files = sorted(glob.glob(f'{BASE}/final/*.tgs'))
CELL = 170
per_sheet = 20
for s0 in range(0, len(files), per_sheet):
    batch = files[s0:s0 + per_sheet]
    cols, rows = 5, 4
    sheet = Image.new('RGB', (cols * CELL, rows * (CELL + 16)), 'white')
    d = ImageDraw.Draw(sheet)
    for j, p in enumerate(batch):
        idx = s0 + j + 1
        try:
            anim = LottieAnimation.from_tgs(p)
            total = 60
            frames = [5, 25, 45]
            tile = Image.new('RGB', (CELL, CELL), (240, 240, 240))
            sub = CELL // 3
            for k, fr in enumerate(frames):
                im = anim.render_pillow_frame(frame_num=fr).convert('RGB').resize((sub * 3, sub))
                tile.paste(im, (0, k * sub))
            # single full frame is more legible: use middle frame big instead
            im = anim.render_pillow_frame(frame_num=frames[1]).convert('RGB').resize((CELL, CELL))
            tile = im
        except Exception as e:
            tile = Image.new('RGB', (CELL, CELL), (255, 200, 200))
        x, y = (j % cols) * CELL, (j // cols) * (CELL + 16)
        sheet.paste(tile, (x, y + 16))
        d.text((x + 4, y + 3), f'{idx:03d}', fill='red')
    sheet.save(f'{OUT}/sheet_{s0 // per_sheet + 1:02d}.png')
    print('sheet', s0 // per_sheet + 1, 'done', flush=True)
print('ALL SHEETS DONE')
