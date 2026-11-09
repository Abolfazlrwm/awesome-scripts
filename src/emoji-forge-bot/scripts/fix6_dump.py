"""Dump group tree of one layer with local bboxes."""
import sys, os
sys.path.insert(0, os.path.dirname(__file__))
from fix6_lib import load, final_path, orig_path, group_local_box

idx = int(sys.argv[1]); scope = sys.argv[2]; li = int(sys.argv[3])
src = sys.argv[4] if len(sys.argv) > 4 else 'final'
doc = load(final_path(idx) if src == 'final' else orig_path(idx))
if scope == 'top':
    layers = doc.get('layers') or []
else:
    layers = [a for a in doc.get('assets') or [] if a.get('id') == scope][0].get('layers') or []
l = layers[li]
print(f'layer nm={l.get("nm")!r} ty={l.get("ty")} parent={l.get("parent")} ind={l.get("ind")}')

def describe(it):
    ty = it.get('ty')
    if ty == 'gr':
        return f'GR nm={it.get("nm")!r} kids={len(it.get("it") or [])}'
    if ty == 'sh':
        k = (it.get('ks') or {}).get('k')
        kind = 'kf' if isinstance(k, list) else 'static'
        return f'SH {kind}'
    return f'{ty} nm={it.get("nm")!r}'

def walk(items, path, depth):
    for i, it in enumerate(items):
        if not isinstance(it, dict):
            continue
        p = path + [i]
        line = '  ' * depth + f'{p} {describe(it)}'
        if it.get('ty') == 'gr':
            box = group_local_box(it)
            if box:
                line += f' box={[round(v,1) for v in box]}'
        print(line)
        if it.get('ty') == 'gr' and depth < 4:
            walk(it.get('it') or [], p, depth + 1)

walk(l.get('shapes') or [], [], 0)
