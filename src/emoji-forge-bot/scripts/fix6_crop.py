"""Crop sheet: base + hide-one-child variants, cropped to region.
usage: fix6_crop.py <idx> <frame> <region> <scope> <layer_i> <parent_path_csv> <outname>
Hides each child of the group at parent_path (or each top-level shape
when parent_path is empty)."""
import copy, sys, os
sys.path.insert(0, os.path.dirname(__file__))
from fix6_lib import load, final_path, orig_path, render, DIAG
from PIL import Image

idx = int(sys.argv[1]); frame = int(sys.argv[2])
region = tuple(int(v) for v in sys.argv[3].split(','))
scope = sys.argv[4]; li = int(sys.argv[5])
ppath = [int(v) for v in sys.argv[6].split(',')] if sys.argv[6] else []
src = sys.argv[7] if len(sys.argv) > 7 else 'final'
doc = load(final_path(idx) if src == 'final' else orig_path(idx))

def get_layers(d):
    if scope == 'top':
        return d.get('layers') or []
    return [a for a in d.get('assets') or [] if a.get('id') == scope][0].get('layers') or []

def get_parent_items(d):
    items = get_layers(d)[li].get('shapes') or []
    for p in ppath:
        items = items[p].get('it') or []
    return items

n = len(get_parent_items(doc))
x0, y0, x1, y1 = region
cw, ch = x1 - x0, y1 - y0
scale = max(1, 220 // max(1, cw))
tiles = [('base', None)] + [(f'-{i}', i) for i in range(n)]
cols = min(len(tiles), 8)
rows = (len(tiles) + cols - 1) // cols
sheet = Image.new('RGB', (cols * cw * scale, rows * ch * scale), (40, 40, 40))
from PIL import ImageDraw
d = ImageDraw.Draw(sheet)
for ti, (label, hide_i) in enumerate(tiles):
    t = copy.deepcopy(doc)
    if hide_i is not None:
        del get_parent_items(t)[hide_i]
    im = render(t, frame, f'c{idx}')
    crop = im.crop(region).resize((cw * scale, ch * scale), Image.NEAREST)
    x, y = (ti % cols) * cw * scale, (ti // cols) * ch * scale
    sheet.paste(crop, (x, y))
    d.text((x + 3, y + 3), label, fill=(255, 255, 0))
out = os.path.join(DIAG, f'{idx:03d}_crop_{scope}_{li}_{"_".join(map(str,ppath)) or "root"}.png')
sheet.save(out)
print(out, 'children:', n)
