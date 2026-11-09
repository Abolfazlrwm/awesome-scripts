"""Removal-diff bisection: hide each candidate (layer / group by
position path) and rank by changed px inside REGION vs outside."""
import copy, json, sys, os
sys.path.insert(0, os.path.dirname(__file__))
from fix6_lib import load, final_path, orig_path, render_np, diff_stat
import numpy as np

def candidates(doc, max_depth=3):
    """Yield (kind, scope, layer_list, layer_i, path or None, desc)."""
    scopes = [('top', doc.get('layers') or [])]
    scopes += [(a.get('id'), a.get('layers') or []) for a in doc.get('assets') or []]
    for scope, layers in scopes:
        for li, l in enumerate(layers):
            yield ('layer', scope, layers, li, None,
                   f'{scope} layer[{li}] nm={l.get("nm")!r} ty={l.get("ty")}')
            if l.get('ty') != 4:
                continue
            def walk(items, path, depth):
                for gi, g in enumerate(items):
                    if isinstance(g, dict) and g.get('ty') == 'gr':
                        yield ('group', scope, layers, li, path + [gi],
                               f'{scope} layer[{li}] path={path + [gi]} nm={g.get("nm")!r}')
                        if depth < max_depth:
                            yield from walk(g.get('it') or [], path + [gi], depth + 1)
            yield from walk(l.get('shapes') or [], [], 1)

def apply_hide(doc, cand):
    kind, scope, layers, li, path, desc = cand
    # re-locate layer list inside the (copied) doc
    if scope == 'top':
        ll = doc.get('layers') or []
    else:
        ll = [a for a in doc.get('assets') or [] if a.get('id') == scope][0].get('layers') or []
    if kind == 'layer':
        ll[li]['hd'] = True
    else:
        items = ll[li].get('shapes') or []
        for p in path[:-1]:
            items = items[p].get('it') or []
        del items[path[-1]]

def main():
    idx = int(sys.argv[1]); frame = int(sys.argv[2])
    region = tuple(int(v) for v in sys.argv[3].split(','))
    src = sys.argv[4] if len(sys.argv) > 4 else 'final'
    doc = load(final_path(idx) if src == 'final' else orig_path(idx))
    base = render_np(doc, frame, f'b{idx}')
    x0, y0, x1, y1 = region
    results = []
    cands = list(candidates(doc))
    print(f'{len(cands)} candidates', flush=True)
    for cand in cands:
        trial = copy.deepcopy(doc)
        try:
            apply_hide(trial, cand)
            img = render_np(trial, frame, f'b{idx}')
        except Exception:
            continue
        inside = diff_stat(img, base, region)
        total = diff_stat(img, base)
        outside = total - inside
        if inside > 30:
            results.append((inside, outside, cand[5]))
    results.sort(key=lambda r: (r[0] - 2 * r[1]), reverse=True)
    for inside, outside, desc in results[:15]:
        print(f'in={inside:6d} out={outside:6d}  {desc}')

if __name__ == '__main__':
    main()
