"""Rebuild Ehsan's single-emoji test properly:
edited file -> remove added 'TZ Emojis' text layer, remove the TON
diamond on the bicep, put the E30TZ logo (halo) in the diamond's slot."""
import copy, glob, gzip, json, os, sys
sys.path.insert(0, 'src')
sys.path.insert(0, 'scripts')
os.environ['FIX4_HALO'] = '1'
from rlottie_python import LottieAnimation
from PIL import Image
import numpy as np
from fix4_logo import _logo_items, TR0

SRC = 'data/projects/forge-20261030-131212-ec427f/single/edited_1793365933.tgs'
OUT = 'data/projects/forge-20261030-131212-ec427f/single/rebuilt_1793365933.tgs'
REGION = (280, 290, 480, 450)  # diamond area in 512 space

def load(p):
    return json.loads(gzip.open(p, 'rb').read().decode())
def save(doc, p):
    data = gzip.compress(json.dumps(doc, separators=(',', ':'), ensure_ascii=False).encode(), compresslevel=9)
    open(p, 'wb').write(data)
    return len(data)
def render(doc, frame=15):
    tmp = '/tmp/_fs_render.tgs'
    save(doc, tmp)
    anim = LottieAnimation.from_tgs(tmp)
    return np.asarray(anim.render_pillow_frame(frame_num=frame).convert('RGBA'), dtype=int)

doc = load(SRC)
# 1) drop the added brand text layer
doc['layers'] = [l for l in doc['layers'] if l.get('nm') != 'TZ Emojis Brand']

base = render(doc)
def region_diff(img):
    x0, y0, x1, y1 = REGION
    d = np.abs(img[y0:y1, x0:x1] - base[y0:y1, x0:x1]).sum(axis=2)
    return int((d > 24).sum())

# 2) find the diamond group inside assets by removal-diff
best = None
for a in doc.get('assets') or []:
    for li, l in enumerate(a.get('layers') or []):
        if l.get('ty') != 4:
            continue
        for gi, g in enumerate(l.get('shapes') or []):
            if not (isinstance(g, dict) and g.get('ty') == 'gr'):
                continue
            trial = copy.deepcopy(doc)
            ta = [x for x in trial['assets'] if x.get('id') == a.get('id')][0]
            del ta['layers'][li]['shapes'][gi]
            ch = region_diff(render(trial))
            print(f'asset layer {li} group {gi}: region-changed px = {ch}', flush=True)
            if ch > 1500 and (best is None or ch < best[0]):
                best = (ch, a.get('id'), li, gi)
print('diamond group:', best)
assert best, 'diamond group not found'
_, aid, li, gi = best

# measure the diamond's visible box from the diff
trial = copy.deepcopy(doc)
ta = [x for x in trial['assets'] if x.get('id') == aid][0]
del ta['layers'][li]['shapes'][gi]
img = render(trial)
x0, y0, x1, y1 = REGION
d = np.abs(img[y0:y1, x0:x1] - base[y0:y1, x0:x1]).sum(axis=2) > 24
ys, xs = np.where(d)
box_scene = (x0 + int(xs.min()), y0 + int(ys.min()), x0 + int(xs.max()), y0 + int(ys.max()))
print('diamond scene box:', box_scene)

# 3) replace the diamond group's artwork with the halo logo.
# The group lives inside a precomp asset; its local coords differ from
# scene coords. Compute the group's own artwork bbox in LOCAL space and
# fit the logo to that instead (slot-exact in local space).
asset = [x for x in doc['assets'] if x.get('id') == aid][0]
grp = asset['layers'][li]['shapes'][gi]
def group_local_box(g):
    xs, ys = [], []
    def walk(items):
        for it in items:
            if isinstance(it, dict):
                if it.get('ty') == 'sh':
                    k = (it.get('ks') or {}).get('k') or {}
                    for v in k.get('v') or []:
                        xs.append(v[0]); ys.append(v[1])
                if it.get('ty') == 'gr':
                    walk(it.get('it') or [])
    walk(g.get('it') or [])
    return (min(xs), min(ys), max(xs), max(ys))
lbox = group_local_box(grp)
print('diamond local box:', [round(v, 1) for v in lbox])
logo = _logo_items(lbox)
old_tr = [x for x in (grp.get('it') or []) if isinstance(x, dict) and x.get('ty') == 'tr']
grp['it'] = logo + (old_tr if old_tr else [copy.deepcopy(TR0)])
grp['nm'] = 'E30TZ Logo Slot'

# 4) every group must end with a tr (Telegram importer rule)
def ensure_tr(items):
    for it in items:
        if isinstance(it, dict) and it.get('ty') == 'gr':
            kids = it.get('it') or []
            ensure_tr(kids)
            if kids and not any(isinstance(x, dict) and x.get('ty') == 'tr' for x in kids):
                kids.append(copy.deepcopy(TR0))
                it['it'] = kids
def wl(layers):
    for l in layers:
        if l.get('ty') == 4:
            ensure_tr(l.get('shapes') or [])
wl(doc.get('layers') or [])
for a in doc.get('assets') or []:
    wl(a.get('layers') or [])

n = save(doc, OUT)
print('saved', OUT, n, 'bytes')
