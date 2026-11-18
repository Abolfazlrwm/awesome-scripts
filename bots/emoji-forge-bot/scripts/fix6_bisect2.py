"""Targeted bisect inside one layer: hide groups at a given parent
path's children (or deeper), rank by region change."""
import copy, sys, os
sys.path.insert(0, os.path.dirname(__file__))
from fix6_lib import load, final_path, orig_path, render_np, diff_stat

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

def get_items(d, path):
    items = get_layers(d)[li].get('shapes') or []
    for p in path:
        items = items[p].get('it') or []
    return items

base = render_np(doc, frame, f'd{idx}')
kids = get_items(doc, ppath)
print(f'{len(kids)} children at {ppath}')
rows = []
for i, it in enumerate(kids):
    if not isinstance(it, dict):
        continue
    t = copy.deepcopy(doc)
    del get_items(t, ppath)[i]
    try:
        img = render_np(t, frame, f'd{idx}')
    except Exception as e:
        print(i, 'render fail', e); continue
    inside = diff_stat(img, base, region)
    total = diff_stat(img, base)
    nm = it.get('nm') if it.get('ty') == 'gr' else it.get('ty')
    rows.append((inside, total - inside, i, it.get('ty'), repr(nm),
                 len(it.get('it') or []) if it.get('ty') == 'gr' else ''))
rows.sort(key=lambda r: (r[0] - 2 * r[1]), reverse=True)
for r in rows:
    if r[0] > 10 or r[1] > 10:
        print(f'child {r[2]:3d} ty={r[3]} nm={r[4]} kids={r[5]} in={r[0]} out={r[1]}')
