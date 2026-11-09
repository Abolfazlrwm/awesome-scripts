"""Solo-render one group path in one layer: keep only that branch,
hide all other layers. Report painted px in region + save crops.
usage: fix6_solo.py idx frame region scope layer_i group_path_csv [src]"""
import copy, sys, os
sys.path.insert(0, os.path.dirname(__file__))
from fix6_lib import load, final_path, orig_path, render_np, diff_stat, DIAG
import numpy as np
from PIL import Image

idx = int(sys.argv[1]); frame = int(sys.argv[2])
region = tuple(int(v) for v in sys.argv[3].split(','))
scope = sys.argv[4]; li = int(sys.argv[5])
gpath = [int(v) for v in sys.argv[6].split(',')]
src = sys.argv[7] if len(sys.argv) > 7 else 'final'
doc = load(final_path(idx) if src == 'final' else orig_path(idx))

def get_layers(d, sc):
    if sc == 'top':
        return d.get('layers') or []
    return [a for a in d.get('assets') or [] if a.get('id') == sc][0].get('layers') or []

# hide every layer except the target scope/layer
for lyr in doc.get('layers') or []:
    lyr['hd'] = True
for a in doc.get('assets') or []:
    for lyr in a.get('layers') or []:
        lyr['hd'] = True
target_layers = get_layers(doc, scope)
layer = target_layers[li]
layer.pop('hd', None)

items = layer.get('shapes') or []
# prune siblings along path
cur = items
for depth, p in enumerate(gpath):
    keep = cur[p]
    new = [keep]
    if depth == 0:
        layer['shapes'] = new
    else:
        parent[items_key] = new  # set below via ref
    parent = keep
    items_key = 'it'
    cur = keep.get('it') or []

img = render_np(doc, frame, f'solo{idx}')
x0, y0, x1, y1 = region
crop = img[y0:y1, x0:x1]
painted = int(((crop[:, :, 3] > 10)).sum())
print(f'solo {gpath}: painted px in region = {painted}')
Image.fromarray(img.astype('uint8')).convert('RGB').save(
    os.path.join(DIAG, f'{idx:03d}_solo_{scope}_{li}_{"_".join(map(str,gpath))}.png'))
