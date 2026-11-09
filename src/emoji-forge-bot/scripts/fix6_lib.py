"""Shared helpers for fix6: careful one-file remnant removal."""
import copy, glob, gzip, json, os, sys
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'src'))
sys.path.insert(0, os.path.dirname(__file__))
os.environ.setdefault('FIX4_HALO', '1')
from rlottie_python import LottieAnimation
from PIL import Image
import numpy as np
from fix4_logo import _logo_items, TR0

BASE = os.path.join(os.path.dirname(__file__), '..', 'data', 'projects',
                    'forge-20261029-072531-16e1fc')
DIAG = os.path.join(BASE, 'fix6_diag')
STAGE = os.path.join(BASE, 'final_fix6')

def final_path(idx):
    return glob.glob(os.path.join(BASE, 'final', f'*_{idx:03d}_*.tgs'))[0]

def orig_path(idx):
    return glob.glob(os.path.join(BASE, 'originals', f'*_{idx:03d}_*.tgs'))[0]

def load(p):
    return json.loads(gzip.open(p, 'rb').read().decode())

def save(doc, p):
    data = gzip.compress(json.dumps(doc, separators=(',', ':'),
                                    ensure_ascii=False).encode(),
                         compresslevel=9)
    open(p, 'wb').write(data)
    return len(data)

def render(doc, frame=25, tag='r'):
    tmp = f'/tmp/_fix6_{tag}.tgs'
    save(doc, tmp)
    anim = LottieAnimation.from_tgs(tmp)
    return anim.render_pillow_frame(frame_num=frame).convert('RGBA')

def render_np(doc, frame=25, tag='r'):
    return np.asarray(render(doc, frame, tag), dtype=int)

def sheet(idx, frames=(5, 25, 45), after_doc=None):
    """original | final | (after) rows x frames -> diag png."""
    odoc = load(orig_path(idx))
    fdoc = load(final_path(idx))
    rows = [('orig', odoc), ('final', fdoc)]
    if after_doc is not None:
        rows.append(('after', after_doc))
    W = 256
    img = Image.new('RGBA', (W * len(frames), W * len(rows)), (30, 30, 30, 255))
    for ri, (_, d) in enumerate(rows):
        for ci, fr in enumerate(frames):
            im = render(d, fr, f's{idx}')
            img.paste(im.resize((W, W)), (ci * W, ri * W))
    out = os.path.join(DIAG, f'{idx:03d}_sheet.png')
    img.convert('RGB').save(out)
    return out

def diff_stat(a, b, region=None):
    d = np.abs(a - b).sum(axis=2)
    if region:
        x0, y0, x1, y1 = region
        d = d[y0:y1, x0:x1]
    return int((d > 24).sum())

def ensure_tr(doc):
    """Every group must end with a tr (Telegram importer invariant)."""
    n = [0]
    def walk_items(items):
        for it in items:
            if isinstance(it, dict) and it.get('ty') == 'gr':
                kids = it.get('it') or []
                walk_items(kids)
                if kids and not any(isinstance(x, dict) and x.get('ty') == 'tr'
                                    for x in kids):
                    kids.append(copy.deepcopy(TR0))
                    it['it'] = kids
                    n[0] += 1
    def wl(layers):
        for l in layers:
            if l.get('ty') == 4:
                walk_items(l.get('shapes') or [])
    wl(doc.get('layers') or [])
    for a in doc.get('assets') or []:
        wl(a.get('layers') or [])
    return n[0]

def group_local_box(g):
    """Bbox of a group's own shape vertices (local coords)."""
    xs, ys = [], []
    def walk(items):
        for it in items:
            if isinstance(it, dict):
                if it.get('ty') == 'sh':
                    k = (it.get('ks') or {}).get('k') or {}
                    if isinstance(k, dict):
                        for v in k.get('v') or []:
                            xs.append(v[0]); ys.append(v[1])
                    elif isinstance(k, list):
                        for kf in k:
                            for se in ('s', 'e'):
                                vv = kf.get(se)
                                if isinstance(vv, list) and vv and isinstance(vv[0], dict):
                                    for v in vv[0].get('v') or []:
                                        xs.append(v[0]); ys.append(v[1])
                if it.get('ty') == 'gr':
                    walk(it.get('it') or [])
    walk(g.get('it') or [])
    if not xs:
        return None
    return (min(xs), min(ys), max(xs), max(ys))

def all_group_paths(doc):
    """Yield (scope_desc, layers_list, layer_i, shapes_list, shape_i, group)
    for every top-level shape group in every shape layer."""
    scopes = [('top', doc.get('layers') or [])]
    scopes += [(a.get('id'), a.get('layers') or []) for a in doc.get('assets') or []]
    for scope, layers in scopes:
        for li, l in enumerate(layers):
            if l.get('ty') != 4:
                continue
            for gi, g in enumerate(l.get('shapes') or []):
                if isinstance(g, dict) and g.get('ty') == 'gr':
                    yield (scope, layers, li, l.get('shapes'), gi, g)

def get_layers(doc, scope):
    if scope == 'top':
        return doc.get('layers') or []
    return [a for a in doc.get('assets') or [] if a.get('id') == scope][0].get('layers') or []

def get_group(doc, scope, li, path):
    items = get_layers(doc, scope)[li].get('shapes') or []
    node = None
    for p in path:
        node = items[p]
        items = node.get('it') or []
    return node

def replace_group_with_logo(doc, scope, li, path, box=None, rename='E30TZ Logo Slot'):
    """Replace the target group's artwork with the halo logo fitted
    to the group's own local artwork box; keep its own tr."""
    g = get_group(doc, scope, li, path)
    if box is None:
        box = group_local_box(g)
    old_tr = [x for x in (g.get('it') or [])
              if isinstance(x, dict) and x.get('ty') == 'tr']
    g['it'] = _logo_items(box) + (old_tr if old_tr else [copy.deepcopy(TR0)])
    g['nm'] = rename
    return box

def finish_and_stage(doc, idx, stage=True):
    n_tr = ensure_tr(doc)
    import os as _os
    out = _os.path.join(STAGE, _os.path.basename(final_path(idx)))
    size = save(doc, out)
    from emoji_forge.validate_tgs import validate_tgs_bytes
    issues, warnings = validate_tgs_bytes(open(out, 'rb').read(), idx)
    return out, size, n_tr, issues, warnings
