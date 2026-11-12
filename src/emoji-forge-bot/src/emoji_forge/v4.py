"""v4 engine: selective theme recolour + exact on-screen logo replacement.

Why v4 exists (measured on the real sVnTeam_Dvix pack, v3 finals):
- 9/20 v3 finals (001,002,003,006,011,013,017,018,019) put Abolfazl_rwm at
  bottom-centre (y=470) because no top-level logo layer was captured.
  That is not "in the logo's place" by any definition.
- The other finals sized text from the logo layer's LOCAL shape width
  only (54-110, local units), ignoring parent and precomp scales.
  Example: file 020 local text width 288 at scale 30% = ~86px in asset
  space before precomp scaling; file 012 text at ks.p x=2380 in asset
  space; file 009 text at ks.p (-231, 257) with a parent that only
  exists in one scope. On-screen size was never computed, so text
  looked tiny even when its local size looked large.
- v2 full duotone recoloured everything (boy skin, chick yellow,
  flame) to red/white/black. v3 preserved everything, so the blue
  theme stayed blue. The reconciled rule is selective: only
  blue-dominant theme colours in theme layers become red; natural
  colours and subject layers are untouched.

v4 therefore does, in order, for each TGS:
  1. Capture the logo element BEFORE any deletion: named logo layer,
     brand-mark layer (TON/FREE/SVN/CDC-type), or, failing that, the
     pack-common watermark group. Compute its ON-SCREEN bounding box
     by composing the full transform chain (layer + parents + precomp
     instances, sampled at visible frames).
  2. Delete logo/brand-mark layers, pack-common watermark groups,
     and the hardcoded watermark fallback (fallback only, never the
     primary mechanism). Prune empty shape layers and fix dangling
     parents (both were Telegram "wrong file type" causes).
  3. Selectively recolour blue theme fills/strokes/gradients to red,
     skipping subject layers and warm/yellow/natural colours, and
     handling keyframed flat fills (previously skipped entirely).
  4. Add Abolfazl_rwm as a TOP-LEVEL vector text layer (no asset-scope
     layer: the v3 attempt that used asset-scope text for banner
     files 17-19 failed with "wrong file type", while the otherwise
     identical attempt with top-level text succeeded; asset-scope is
     therefore treated as suspect and avoided), with keyframed
     position/opacity following the logo's on-screen trajectory and
     size chosen so the rendered text fills the logo's on-screen box
     (width AND height, within ~30% — verified by renders, not by
     local units).
"""
import colorsys
import math
import re

from .animation_engine import (
    _apply_to_gradient, _flat_colour, _walk, load_tgs, dump_tgs,
    pack_common_signatures, shape_signature, text_to_lottie_shapes,
    tgs_add_text_layer, tgs_hide_layers, tgs_hide_text_layers,
    tgs_logo_candidates, tgs_prune_empty_shape_layers,
    tgs_prune_unused_assets, tgs_remove_layers,
    tgs_remove_shapes_by_signatures, tgs_remove_watermark_shapes,
)

SUBJECT_SKIP_RE = re.compile(
    r"fire|flame|head|hair|eye|mouth|nose|ear|uho|beak|chick|head_gr|"
    r"energy|ding|glow|kist|hand|legs|te lo|telo|body|skin|face|"
    r"bot_pr|bot_l|r_lk|r_pr|dop|uh_|br_|north|all$",
    re.IGNORECASE,
)
BRAND_MARK_RE = re.compile(
    r"^(TON|FREE|CDC|SVN)(\b|-|_|\s)|^TON-Rebro|^FREE\b|SVN\s*\|",
    re.IGNORECASE,
)


def is_subject_layer(name: str) -> bool:
    return bool(SUBJECT_SKIP_RE.search(str(name or "")))


def is_blue_dominant(r: float, g: float, b: float) -> bool:
    """Blue/cyan-dominant, saturated, not warm and not grey/white/black."""
    if max(r, g, b) < 0.12 or min(r, g, b) > 0.92:
        return False
    if b <= r + 0.08 or b <= g + 0.03:
        return False
    sat = (max(r, g, b) - min(r, g, b)) / (max(r, g, b) or 1.0)
    return sat > 0.22


def _blue_to_red(r: float, g: float, b: float) -> tuple:
    """Map a blue/cyan colour to red, preserving lightness/saturation."""
    h, l, s = colorsys.rgb_to_hls(r, g, b)
    # Blue/cyan hue is ~0.5-0.75; map it to red (0.0). Keep l and s.
    nr, ng, nb = colorsys.hls_to_rgb(0.0, l, min(1.0, s))
    return nr, ng, nb


def _recolor_node_colours(node: dict, skip: bool, predicate=None, mapper=None) -> int:
    if skip:
        return 0
    predicate = predicate or is_blue_dominant
    mapper = mapper or _blue_to_red
    changed = 0
    ty = node.get("ty")
    if ty in ("fl", "st"):
        k = _flat_colour(node)
        if k is not None:
            r, g, b = float(k[0]), float(k[1]), float(k[2])
            if predicate(r, g, b):
                nr, ng, nb = mapper(r, g, b)
                k[0], k[1], k[2] = nr, ng, nb
                changed += 1
        else:
            # Keyframed flat fill: k is a list of keyframe dicts.
            ck = (node.get("c") or {}).get("k")
            if isinstance(ck, list):
                for kf in ck:
                    if isinstance(kf, dict):
                        s = kf.get("s")
                        if isinstance(s, list) and len(s) >= 3 and all(
                                isinstance(v, (int, float)) for v in s[:3]):
                            r, g, b = float(s[0]), float(s[1]), float(s[2])
                            if predicate(r, g, b):
                                nr, ng, nb = mapper(r, g, b)
                                s[0], s[1], s[2] = nr, ng, nb
                                changed += 1
    elif ty in ("gf", "gs"):
        def _fn(r, g, b):
            return mapper(r, g, b) if predicate(r, g, b) else (r, g, b)
        changed += _apply_to_gradient(node, _fn, skip_skin=False)
    return changed


def hex_to_rgb01(value: str) -> tuple:
    v = str(value or "").lstrip("#")
    if len(v) != 6:
        raise ValueError(f"invalid hex colour: {value!r}")
    return tuple(int(v[i:i + 2], 16) / 255.0 for i in (0, 2, 4))


def make_hue_band_mapper(source_deg: float, target_hex: str, band_deg: float = 42.0):
    """(predicate, mapper) for the GENERAL selective recolour: colours
    whose hue sits within ±band of the pack's theme hue are rotated to
    the target colour's hue, keeping lightness and saturation — so a
    family of theme shades becomes the target's spectrum, while
    natural colours (skin, gold, yellow, red family) and greys are
    outside the band and stay untouched."""
    tr, tg, tb = hex_to_rgb01(target_hex)
    target_h, _l, _s = colorsys.rgb_to_hls(tr, tg, tb)

    def _ang_dist(a: float, b: float) -> float:
        d = abs(a - b) % 360.0
        return min(d, 360.0 - d)

    def predicate(r: float, g: float, b: float) -> bool:
        mx, mn = max(r, g, b), min(r, g, b)
        if mx < 0.12 or mn > 0.92:
            return False
        sat = (mx - mn) / (mx or 1.0)
        if sat <= 0.22:
            return False
        h, _l, _s = colorsys.rgb_to_hls(r, g, b)
        return _ang_dist(h * 360.0, source_deg) <= band_deg

    def mapper(r: float, g: float, b: float) -> tuple:
        _h, l, s = colorsys.rgb_to_hls(r, g, b)
        # Vibrancy lift: very dark theme colours would read as black
        # after a pure hue rotation (a dark-green shirt became a
        # near-black purple) — floor their lightness slightly, and
        # gently boost dull saturations, so the new theme reads as a
        # rich spectrum of the target colour.
        if l < 0.13:
            l = 0.13
        if s < 0.85:
            s = min(1.0, s * 1.12)
        return colorsys.hls_to_rgb(target_h, l, s)

    return predicate, mapper


def tgs_selective_recolor(doc: dict, target_hex: str, source_deg: float,
                          band_deg: float = 42.0) -> int:
    """General selective theme recolour: rotate the pack theme hue
    band to the target colour's hue (spectrum preserved via lightness).
    Returns changed colour/stop count.

    No subject-layer skip here (unlike the legacy blue->red path):
    natural colours are protected BY HUE — skin, gold, yellow and the
    red family sit outside the theme band and never match the
    predicate. A layer whose colours ARE in the band is wearing the
    pack's theme colour (a green suit in a green-themed pack), and
    rebranding the pack means rebranding it too — the name-based skip
    left whole characters green in the Mehdi pack (file 038) while
    their accents turned purple."""
    predicate, mapper = make_hue_band_mapper(source_deg, target_hex, band_deg)
    changed = 0
    scopes = [doc.get("layers") or []]
    scopes += [a.get("layers") or [] for a in doc.get("assets") or []]
    for layers in scopes:
        for layer in layers:
            for node in _walk(layer.get("shapes") or []):
                if isinstance(node, dict):
                    changed += _recolor_node_colours(node, False, predicate, mapper)
    return changed


def tgs_selective_theme_recolor(doc: dict) -> int:
    """Recolour blue theme colours to red, skipping subject layers.

    Walks each scope's layers separately so a layer's name decides
    whether its shapes are recoloured. Asset layers are separate
    scopes with their own names. Returns changed colour/stop count.
    """
    changed = 0
    scopes = [doc.get("layers") or []]
    scopes += [a.get("layers") or [] for a in doc.get("assets") or []]
    for layers in scopes:
        for layer in layers:
            skip = is_subject_layer(layer.get("nm", ""))
            for node in _walk(layer.get("shapes") or []):
                if isinstance(node, dict):
                    changed += _recolor_node_colours(node, skip)
    return changed


# ---------------------------------------------------------------- transforms

def _prop_at(prop: dict, frame: int):
    """Sample a Lottie transform property at a frame (linear interp)."""
    if not isinstance(prop, dict):
        return None
    k = prop.get("k")
    if prop.get("a") != 1 or not isinstance(k, list) or not k:
        return k
    # Keyframes: list of dicts with t and s.
    kfs = [kf for kf in k if isinstance(kf, dict) and "t" in kf and "s" in kf]
    if not kfs:
        return None
    if frame <= kfs[0]["t"]:
        return kfs[0]["s"]
    if frame >= kfs[-1]["t"]:
        return kfs[-1]["s"]
    for a, b in zip(kfs, kfs[1:]):
        if a["t"] <= frame <= b["t"]:
            t = (frame - a["t"]) / (b["t"] - a["t"]) if b["t"] != a["t"] else 0
            sa, sb = a["s"], b["s"]
            if isinstance(sa, list) and isinstance(sb, list) and len(sa) == len(sb):
                return [sa[i] + (sb[i] - sa[i]) * t for i in range(len(sa))]
            return sa
    return kfs[-1]["s"]


def _vec3(v, default):
    if isinstance(v, list) and len(v) >= 2:
        return float(v[0]), float(v[1]), (float(v[2]) if len(v) > 2 else 0.0)
    return default


def _mat_apply(m, x, y):
    """Apply 2D affine (a,b,c,d,e,f) to a point."""
    a, b, c, d, e, f = m
    return (a * x + c * y + e, b * x + d * y + f)


def _mat_mul(m2, m1):
    """Return m2 * m1 for 2D affine (a,b,c,d,e,f)."""
    a2, b2, c2, d2, e2, f2 = m2
    a1, b1, c1, d1, e1, f1 = m1
    return (
        a2 * a1 + c2 * b1,
        b2 * a1 + d2 * b1,
        a2 * c1 + c2 * d1,
        b2 * c1 + d2 * d1,
        a2 * e1 + c2 * f1 + e2,
        b2 * e1 + d2 * f1 + f2,
    )


def _layer_matrix(layer: dict, frame: int):
    ks = layer.get("ks") or {}
    p = _vec3(_prop_at(ks.get("p") or {}, frame), (0.0, 0.0, 0.0))
    a = _vec3(_prop_at(ks.get("a") or {}, frame), (0.0, 0.0, 0.0))
    s = _vec3(_prop_at(ks.get("s") or {}, frame), (100.0, 100.0, 100.0))
    r = _prop_at(ks.get("r") or {}, frame)
    rdeg = float(r[0]) if isinstance(r, list) and r else float(r or 0.0)
    th = math.radians(rdeg)
    cos, sin = math.cos(th), math.sin(th)
    sx, sy = s[0] / 100.0, s[1] / 100.0
    # M = T(p) * R * S * T(-a)
    m = (cos * sx, sin * sx, -sin * sy, cos * sy, p[0], p[1])
    # Apply T(-a) on the right: x' = M*(x - a) + p  ==  M*x + (p - M*a)
    # Our m already has translation p; adjust:
    e = p[0] - (m[0] * a[0] + m[2] * a[1])
    f = p[1] - (m[1] * a[0] + m[3] * a[1])
    return (m[0], m[1], m[2], m[3], e, f)


def _apply_mat(m, x, y):
    return (m[0] * x + m[2] * y + m[4], m[1] * x + m[3] * y + m[5])


def _group_bbox(items, mat):
    """Collect shape vertices under group transforms (recursive)."""
    pts = []
    for item in items or []:
        if not isinstance(item, dict):
            continue
        if item.get("ty") == "gr":
            tr = None
            inner = item.get("it") or []
            for sub in inner:
                if isinstance(sub, dict) and sub.get("ty") == "tr":
                    tr = sub
            gmat = mat
            if tr is not None:
                # Group transform: p, a, s, r (usually static; if
                # keyframed, use the first keyframe as the bbox
                # approximation — exact per-frame boxes are sampled
                # at the layer level, not inside groups).
                def _gv(d, default):
                    k = (d or {}).get("k")
                    if isinstance(k, list) and len(k) >= 2 and all(
                            isinstance(v, (int, float)) for v in k[:2]):
                        return float(k[0]), float(k[1])
                    if isinstance(k, list) and k and isinstance(k[0], dict):
                        s = k[0].get("s")
                        if isinstance(s, list) and len(s) >= 2 and all(
                                isinstance(v, (int, float)) for v in s[:2]):
                            return float(s[0]), float(s[1])
                    return default
                gp = _gv(tr.get("p"), (0.0, 0.0))
                ga = _gv(tr.get("a"), (0.0, 0.0))
                gs = _gv(tr.get("s"), (100.0, 100.0))
                gr = tr.get("r") or {}
                rk = gr.get("k")
                if isinstance(rk, list) and rk and isinstance(rk[0], (int, float)):
                    rdeg = float(rk[0])
                elif isinstance(rk, (int, float)):
                    rdeg = float(rk)
                else:
                    rdeg = 0.0
                th = math.radians(rdeg)
                cos, sin = math.cos(th), math.sin(th)
                gm = (cos * gs[0] / 100.0, sin * gs[0] / 100.0,
                      -sin * gs[1] / 100.0, cos * gs[1] / 100.0, gp[0], gp[1])
                gm = (gm[0], gm[1], gm[2], gm[3],
                      gp[0] - (gm[0] * ga[0] + gm[2] * ga[1]),
                      gp[1] - (gm[1] * ga[0] + gm[3] * ga[1]))
                gmat = _mat_mul(mat, gm)
            pts += _group_bbox(inner, gmat)
        elif item.get("ty") == "rc":
            # Rectangle shape: centre p, size s.
            rp = (item.get("p") or {}).get("k") or [0, 0]
            rs = (item.get("s") or {}).get("k") or [0, 0]
            if isinstance(rp, list) and len(rp) >= 2 and isinstance(rs, list) and len(rs) >= 2:
                cx, cy = float(rp[0]), float(rp[1])
                hw, hh = float(rs[0]) / 2, float(rs[1]) / 2
                for dx, dy in ((-hw, -hh), (hw, -hh), (-hw, hh), (hw, hh)):
                    pts.append(_mat_apply(mat, cx + dx, cy + dy))
        elif item.get("ty") == "sh":
            k = (item.get("ks") or {}).get("k")
            v = None
            if isinstance(k, dict):
                v = k.get("v")
                if v is None and isinstance(k.get("k"), list) and k["k"]:
                    first = k["k"][0]
                    if isinstance(first, dict):
                        sv = first.get("s")
                        v = sv.get("v") if isinstance(sv, dict) else None
            elif isinstance(k, list) and k and isinstance(k[0], dict):
                # Keyframed path in list form: first keyframe's s is
                # either a dict {i,o,v,c} or a list of such dicts.
                sv = k[0].get("s")
                if isinstance(sv, dict):
                    v = sv.get("v")
                elif isinstance(sv, list) and sv and isinstance(sv[0], dict):
                    v = sv[0].get("v")
            if isinstance(v, list):
                for pt in v:
                    if isinstance(pt, (list, tuple)) and len(pt) >= 2:
                        pts.append(_apply_mat(mat, float(pt[0]), float(pt[1])))
    return pts


def _layer_shape_bbox(layer: dict):
    """Local (untransformed) bbox of a layer's shapes, with groups."""
    ident = (1.0, 0.0, 0.0, 1.0, 0.0, 0.0)
    pts = _group_bbox(layer.get("shapes") or [], ident)
    if not pts:
        return None
    xs = [p[0] for p in pts]
    ys = [p[1] for p in pts]
    return (min(xs), min(ys), max(xs), max(ys))


def _find_layer_by_ind(layers, ind):
    for layer in layers or []:
        if layer.get("ind") == ind:
            return layer
    return None


def _chain_matrix(doc: dict, scope_layers, layer: dict, frame: int, depth=0):
    """Compose layer matrix + parent chain within one scope."""
    if depth > 10:
        return _layer_matrix(layer, frame)
    m = _layer_matrix(layer, frame)
    parent_ind = layer.get("parent")
    if parent_ind is not None:
        parent = _find_layer_by_ind(scope_layers, parent_ind)
        if parent is not None and parent is not layer:
            pm = _chain_matrix(doc, scope_layers, parent, frame, depth + 1)
            m = _mat_mul(pm, m)
    return m


def _scope_layers(doc: dict, scope):
    if scope is None:
        return doc.get("layers") or []
    for asset in doc.get("assets") or []:
        if asset.get("id") == scope:
            return asset.get("layers") or []
    return []


def _referencing_layers(doc: dict, asset_id: str):
    """All (scope, layer) pairs whose refId points at asset_id."""
    out = []
    for layer in doc.get("layers") or []:
        if layer.get("refId") == asset_id:
            out.append((None, layer))
    for asset in doc.get("assets") or []:
        for layer in asset.get("layers") or []:
            if layer.get("refId") == asset_id:
                out.append((asset.get("id"), layer))
    return out



def _chain_to_frame(doc: dict, scope, layer: dict, frame: int, depth: int = 0,
                    _seen: frozenset | None = None):
    """Matrix from a layer's local space to frame space, following ALL
    precomp levels (not just one). Previous code composed only the
    referencing layer's intra-scope chain, missing the top-level
    instance (file 017: comp_2 -> comp_0 -> top Pre-comp 1), so asset
    on-screen boxes were in comp_0 space, not frame space, and v5
    text sized/placed from them rendered in the wrong place (or
    off-frame). Largest-determinant referencing instance wins.
    """
    m = _chain_matrix(doc, _scope_layers(doc, scope), layer, frame)
    if scope is None or depth > 6:
        return m
    seen = set(_seen or set())
    key = (scope, layer.get("ind"))
    if key in seen:
        return m
    seen.add(key)
    best = None
    for ref_scope, ref_layer in _referencing_layers(doc, scope):
        if not (ref_layer.get("ip", 0) <= frame <= (ref_layer.get("op") or 10 ** 9)):
            continue
        ref_m = _chain_to_frame(doc, ref_scope, ref_layer, frame, depth + 1, frozenset(seen))
        full_m = _mat_mul(ref_m, m)
        det = abs(full_m[0] * full_m[3] - full_m[1] * full_m[2])
        if best is None or det > best[0]:
            best = (det, full_m)
    return best[1] if best else m


def _on_screen_box(doc: dict, scope, layer: dict, frame: int, depth=0):
    """On-screen bbox of a layer at a frame, following precomp chain.

    Returns (x0, y0, x1, y1, rotation_deg, opacity) or None.
    For an asset layer, every referencing precomp instance is tried
    and the largest-area visible instance wins (banner files have
    several stacked instances; the largest is the one the user sees).
    """
    if depth > 6:
        return None
    layers = _scope_layers(doc, scope)
    m = _chain_matrix(doc, layers, layer, frame)
    # Opacity: the layer's OWN opacity only. In Lottie/AE, parent
    # opacity is NOT inherited (a Null parent with o=0 does not hide
    # its children); multiplying parents in made v4 text invisible
    # whenever a Null control layer had o=0.
    op = 100.0
    o = _prop_at((layer.get("ks") or {}).get("o") or {}, frame)
    if isinstance(o, list) and o:
        op = float(o[0])
    elif isinstance(o, (int, float)):
        op = float(o)
    rot = math.degrees(math.atan2(m[1], m[0])) if (m[0] or m[1]) else 0.0
    if scope is None:
        pts = _group_bbox(layer.get("shapes") or [], m)
        if not pts:
            return None
        xs = [p[0] for p in pts]
        ys = [p[1] for p in pts]
        return (min(xs), min(ys), max(xs), max(ys), rot, op)
    # Asset scope: full recursive chain to frame space.
    full_m = _chain_to_frame(doc, scope, layer, frame)
    pts = _group_bbox(layer.get("shapes") or [], full_m)
    if not pts:
        return None
    xs = [p[0] for p in pts]
    ys = [p[1] for p in pts]
    rot = math.degrees(math.atan2(full_m[1], full_m[0])) if (full_m[0] or full_m[1]) else 0.0
    return (min(xs), min(ys), max(xs), max(ys), rot, op)


def find_logo_element(doc: dict, hide_names: set, pack_common_signatures: set | None):
    """Find the main logo element, preferring the largest named one.

    Returns dict {scope, layer, bbox_local, kind} or None.
    kind: 'named' | 'brandmark' | 'watermark'.
    """
    candidates = set(tgs_logo_candidates(doc)) | set(hide_names or [])
    best = None
    for scope, layers in [(None, doc.get("layers") or [])] + [
            (a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]:
        for layer in layers:
            name = str(layer.get("nm", ""))
            kind = None
            if name in candidates:
                kind = "named"
            elif BRAND_MARK_RE.search(name):
                kind = "brandmark"
            if kind is None:
                continue
            bbox = _layer_shape_bbox(layer)
            if bbox is None:
                continue
            area = (bbox[2] - bbox[0]) * (bbox[3] - bbox[1])
            # Prefer named over brandmark, then largest area.
            score = (1 if kind == "named" else 0, area)
            if best is None or score > best[0]:
                best = (score, {"scope": scope, "layer": layer, "bbox_local": bbox, "kind": kind})
    if best is not None:
        return best[1]
    # Fallback: an "emblem" layer is a brand mark by definition
    # (shield/badge), even when it carries no logo name. Used for
    # file 001, whose triangle lives inside emblem/base shapes with
    # keyframed paths and no pack-common signature.
    for scope, layers in [(None, doc.get("layers") or [])] + [
            (a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]:
        for layer in layers:
            if "emblem" in str(layer.get("nm", "")).lower():
                bbox = _layer_shape_bbox(layer)
                if bbox is not None:
                    return {"scope": scope, "layer": layer, "bbox_local": bbox, "kind": "emblem"}
    # Fallback: pack-common watermark group in any layer.
    if pack_common_signatures:
        for scope, layers in [(None, doc.get("layers") or [])] + [
                (a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]:
            for layer in layers:
                for item in layer.get("shapes") or []:
                    if not isinstance(item, dict) or item.get("ty") != "gr":
                        continue
                    found = False
                    for node in _walk(item):
                        if isinstance(node, dict) and node.get("ty") == "sh":
                            if shape_signature((node.get("ks") or {}).get("k") or {}) in pack_common_signatures:
                                found = True
                                break
                    if found:
                        pts = _group_bbox([item], (1, 0, 0, 1, 0, 0))
                        if pts:
                            xs = [pt[0] for pt in pts]
                            ys = [pt[1] for pt in pts]
                            bbox = (min(xs), min(ys), max(xs), max(ys))
                        else:
                            bbox = _layer_shape_bbox(layer)
                        if bbox is not None:
                            return {"scope": scope, "layer": layer, "bbox_local": bbox,
                                    "kind": "watermark", "group_bbox": bbox}
    return None


def _on_screen_box_for_element(doc: dict, element: dict, frame: int):
    """On-screen box of an element, using its group_bbox when set
    (watermark fallback) so the text lands on the watermark itself,
    not on the whole scene layer that happens to contain it."""
    gb = element.get("group_bbox")
    if not gb:
        return _on_screen_box(doc, element["scope"], element["layer"], frame)
    scope, layer = element["scope"], element["layer"]
    layers = _scope_layers(doc, scope)
    m = _chain_matrix(doc, layers, layer, frame)
    o = _prop_at((layer.get("ks") or {}).get("o") or {}, frame)
    op = float(o[0]) if isinstance(o, list) and o else float(o) if isinstance(o, (int, float)) else 100.0
    rot = math.degrees(math.atan2(m[1], m[0])) if (m[0] or m[1]) else 0.0
    x0, y0, x1, y1 = gb
    corners = [(x0, y0), (x1, y0), (x0, y1), (x1, y1)]
    pts = [_mat_apply(m, x, y) for x, y in corners]
    if scope is None:
        xs = [pt[0] for pt in pts]
        ys = [pt[1] for pt in pts]
        return (min(xs), min(ys), max(xs), max(ys), rot, op)
    # Asset scope: full recursive chain to frame space.
    full_m = _chain_to_frame(doc, scope, layer, frame)
    p2 = [_mat_apply(full_m, x, y) for x, y in corners]
    xs = [pt[0] for pt in p2]
    ys = [pt[1] for pt in p2]
    rot = math.degrees(math.atan2(full_m[1], full_m[0])) if (full_m[0] or full_m[1]) else 0.0
    return (min(xs), min(ys), max(xs), max(ys), rot, op)


def sample_logo_trajectory(doc: dict, element: dict):
    """Sample on-screen box at 0/25/50/75/100% of the doc animation.

    Returns (median_box, trajectory) where median_box is
    (cx, cy, w, h, rot, opacity) and trajectory is a list of
    (frame, cx, cy, w, h, rot, opacity). Frames outside the logo
    layer's ip-op are skipped; if none survive, frame 0 is used.
    """
    layer = element["layer"]
    scope = element["scope"]
    doc_op = doc.get("op", 180)
    lip, lop = layer.get("ip", 0), layer.get("op") or doc_op
    frames = []
    for frac in (0.0, 0.25, 0.5, 0.75, 1.0):
        f = int(doc_op * frac)
        if lip <= f <= lop:
            frames.append(f)
    if not frames:
        frames = [0]
    traj = []
    for f in frames:
        box = _on_screen_box_for_element(doc, element, f)
        if box is None:
            continue
        x0, y0, x1, y1, rot, op = box
        traj.append((f, (x0 + x1) / 2, (y0 + y1) / 2, x1 - x0, y1 - y0, rot, op))
    if not traj:
        # Fall back to the local bbox centred on the doc canvas.
        x0, y0, x1, y1 = element["bbox_local"]
        traj = [(0, (x0 + x1) / 2, (y0 + y1) / 2, x1 - x0, y1 - y0, 0.0, 100.0)]
    def _norm_rot(r):
        # A shape logo flipped 180 deg still reads as a shape, but
        # text rotated 180 deg reads upside-down (file 002 rendered
        # "Z10E3"). Normalise to [-90, 90] so text stays upright.
        while r > 90:
            r -= 180
        while r < -90:
            r += 180
        return r
    ws = sorted(t[3] for t in traj)
    hs = sorted(t[4] for t in traj)
    cxs = sorted(t[1] for t in traj)
    cys = sorted(t[2] for t in traj)
    rots = sorted(_norm_rot(t[5]) for t in traj)
    ops = sorted(t[6] for t in traj)
    mid = len(traj) // 2
    median = (cxs[mid], cys[mid], ws[mid], hs[mid], rots[mid], ops[mid])
    traj = [(f, cx, cy, w, h, _norm_rot(r), o) for f, cx, cy, w, h, r, o in traj]
    return median, traj


def tgs_fix_dangling_parents(doc: dict) -> int:
    """Drop parent pointers whose ind does not exist in the same scope."""
    fixed = 0
    for layers in [doc.get("layers") or []] + [a.get("layers") or [] for a in doc.get("assets") or []]:
        inds = {l.get("ind") for l in layers}
        for layer in layers:
            if layer.get("parent") is not None and layer.get("parent") not in inds:
                layer.pop("parent", None)
                fixed += 1
    return fixed


def add_top_level_text_from_box(doc: dict, text: str, font_path: str, median, traj):
    """Add Abolfazl_rwm at top level, sized to the logo's on-screen box.

    median: (cx, cy, w, h, rot, opacity). Size is the smaller of the
    width-based and height-based sizes (so the text fits the box in
    both dimensions), clamped to 24..220. Position follows the
    trajectory as keyframes when the centre moves >10px; rotation and
    opacity follow keyframes when they vary materially.
    Returns (layer_name, size, box_w, box_h).
    """
    cx, cy, bw, bh, rot, op = median
    if op < 1:
        # Sampled frames caught the logo only while faded out; a brand
        # text that is never visible is worse than one that does not
        # follow the fade. Always visible in that case.
        op = 100.0
        traj = [(f, cxx, cyy, w, h, r, 100.0) for f, cxx, cyy, w, h, r, _o in traj]
    # Text geometry at 36pt.
    _, w36 = text_to_lottie_shapes(text, font_path, 36.0)
    # Height at 36pt: measure actual shape bbox, not nominal size.
    shapes36, _ = text_to_lottie_shapes(text, font_path, 36.0)
    xs, ys = [], []
    for sh in shapes36:
        v = (sh.get("ks") or {}).get("k") or {}
        for pt in v.get("v") or []:
            xs.append(float(pt[0]))
            ys.append(float(pt[1]))
    h36 = (max(ys) - min(ys)) if ys else 36.0 * 0.7
    if w36 <= 0 or h36 <= 0 or bw <= 0 or bh <= 0:
        size = 54.0
    else:
        size_w = 36.0 * (bw * 0.92) / w36
        size_h = 36.0 * (bh * 0.92) / h36
        size = max(24.0, min(220.0, size_w, size_h))
    shapes, width = text_to_lottie_shapes(text, font_path, size)
    xs, ys = [], []
    for sh in shapes:
        v = (sh.get("ks") or {}).get("k") or {}
        for pt in v.get("v") or []:
            xs.append(float(pt[0]))
            ys.append(float(pt[1]))
    ax = (min(xs) + max(xs)) / 2 if xs else width / 2
    ay = (min(ys) + max(ys)) / 2 if ys else -size / 2

    def _keyframes(values):
        # values: list of (frame, value). Return Lottie prop dict.
        if len(values) <= 1:
            return {"a": 0, "k": values[0][1] if values else 0}
        return {"a": 1, "k": [{"t": int(f), "s": v, "i": {"x": [0.667], "y": [1]},
                               "o": {"x": [0.333], "y": [0]}} for f, v in values]}

    centres = [(f, [round(cxx, 2), round(cyy, 2), 0]) for f, cxx, cyy, _, _, _, _ in traj]
    moves = max(abs(c[1][0] - centres[0][1][0]) for c in centres) if centres else 0
    moves = max(moves, max(abs(c[1][1] - centres[0][1][1]) for c in centres) if centres else 0)
    p_prop = _keyframes(centres) if len(centres) > 1 and moves > 10 else {"a": 0, "k": [round(cx, 2), round(cy, 2), 0]}
    rots = [(f, [round(r, 2)]) for f, _, _, _, _, r, _ in traj]
    r_var = (max(r for _, r in [(f, v[0]) for f, v in rots]) - min(r for _, r in [(f, v[0]) for f, v in rots])) if rots else 0
    r_prop = _keyframes(rots) if len(rots) > 1 and r_var > 5 else {"a": 0, "k": [round(rot, 2)]}
    ops = [(f, [round(o, 2)]) for f, _, _, _, _, _, o in traj]
    o_var = (max(v[0] for _, v in ops) - min(v[0] for _, v in ops)) if ops else 0
    o_prop = _keyframes(ops) if len(ops) > 1 and o_var > 5 else {"a": 0, "k": [round(op, 2)]}
    ks = {"o": o_prop, "r": r_prop,
          "p": p_prop, "a": {"a": 0, "k": [round(ax, 2), round(ay, 2), 0]},
          "s": {"a": 0, "k": [100, 100, 100]}}
    max_ind = max([l.get("ind", 0) for l in doc.get("layers") or []] or [0])
    layer = {
        "ddd": 0, "ind": max_ind + 1, "ty": 4, "nm": f"{text} Brand", "sr": 1,
        "ks": ks, "ao": 0,
        "shapes": [{"ty": "gr", "it": shapes + [
            {"ty": "fl", "c": {"a": 0, "k": [1, 1, 1, 1]}, "o": {"a": 0, "k": 100},
             "r": 1, "bm": 0, "nm": "Fill 1", "hd": False},
            {"ty": "st", "c": {"a": 0, "k": [0, 0, 0, 1]}, "o": {"a": 0, "k": 100},
             "w": {"a": 0, "k": 2}, "lc": 1, "lj": 1, "bm": 0, "nm": "Stroke 1", "hd": False},
            {"ty": "tr", "p": {"a": 0, "k": [0, 0]}, "a": {"a": 0, "k": [0, 0]},
             "s": {"a": 0, "k": [100, 100]}, "r": {"a": 0, "k": 0}, "o": {"a": 0, "k": 100},
             "sk": {"a": 0, "k": 0}, "sa": {"a": 0, "k": 0}, "nm": "Transform"}],
            "nm": text, "bm": 0, "hd": False}],
        "ip": 0, "op": doc.get("op", 180), "st": 0, "bm": 0,
    }
    # Lottie renders the FIRST layer on top; appending would put the
    # text BEHIND every opaque shape (v4 grid showed E/Z only outside
    # the phone). Insert at index 0.
    doc.setdefault("layers", []).insert(0, layer)
    return layer["nm"], size, bw, bh


def process_tgs_v4(data: bytes, brand, hide_names: set, item_index: int = 0,
                   pack_common_signatures: set | None = None):
    """Full v4 pipeline for one TGS. Returns (output_bytes, warnings)."""
    from .brand import BrandProfile  # noqa: F401  (type hint only)
    doc = load_tgs(data)
    warnings = []
    # 1. Capture logo element + on-screen box BEFORE deletion.
    element = find_logo_element(doc, set(hide_names or []), pack_common_signatures)
    median = traj = None
    if element is not None:
        try:
            median, traj = sample_logo_trajectory(doc, element)
            warnings.append(
                f"لوگوی قبلی ({element['kind']}: {element['layer'].get('nm')}) "
                f"با باکس روی صفحه {median[2]:.0f}×{median[3]:.0f} در "
                f"({median[0]:.0f},{median[1]:.0f}) شناسایی شد.")
        except Exception as exc:
            warnings.append(f"محاسبه باکس لوگو ناموفق بود: {str(exc)[:150]}")
            element = None
    # 2. Delete logo/brand-mark layers, pack-common shapes, fallback.
    if pack_common_signatures:
        pc = tgs_remove_shapes_by_signatures(doc, pack_common_signatures)
        if pc:
            warnings.append(f"شکل مشترک پک (تشخیص عمومی) حذف شد: {pc} گروه.")
    wm = tgs_remove_watermark_shapes(doc)
    if wm:
        warnings.append(f"شکل واترمارک (مسیر جایگزین) حذف شد: {wm} گروه.")
    brand_names = set()
    for scope_layers in [doc.get("layers") or []] + [a.get("layers") or [] for a in doc.get("assets") or []]:
        for layer in scope_layers:
            if BRAND_MARK_RE.search(str(layer.get("nm", ""))):
                brand_names.add(str(layer.get("nm", "")))
    names_to_remove = set(tgs_logo_candidates(doc)) | set(hide_names or []) | brand_names
    # Never delete character Outlines parts (br_/uh_/north are in
    # BRAND_MARK? no — BRAND_MARK only matches TON/FREE/CDC/SVN, so
    # character parts survive; double-check by excluding subject names
    # that are not brand marks).
    names_to_remove = {n for n in names_to_remove if not (
        is_subject_layer(n) and not BRAND_MARK_RE.search(n))}
    removed, hidden = tgs_remove_layers(doc, names_to_remove)
    if removed:
        warnings.append(f"لایه‌های لوگو/برند حذف شدند: {removed} لایه.")
    if hidden:
        warnings.append(f"لایه‌های لوگوی والد به لایه کنترل Null تبدیل شدند (شکل حذف شد، فرزندان انیمیشن خود را حفظ می\u200cکنند): {hidden} لایه.")
    tgs_hide_text_layers(doc)
    pruned_assets = tgs_prune_unused_assets(doc)
    if pruned_assets:
        warnings.append(f"بخش‌های داخلی بدون ارجاع حذف شدند: {pruned_assets} بخش.")
    emptied = tgs_prune_empty_shape_layers(doc)
    if emptied:
        warnings.append(f"لایه‌های خالی‌شده حذف شدند: {emptied} لایه.")
    fixed = tgs_fix_dangling_parents(doc)
    if fixed:
        warnings.append(f"ارجاع‌های پدرِ معلق اصلاح شدند: {fixed} لایه.")
    # 3. Selective theme recolour (blue -> red, subjects preserved).
    changed = tgs_selective_theme_recolor(doc)
    warnings.append(f"بازرنگی انتخابی تم (آبی→قرمز، سوژه‌ها حفظ شدند): {changed} رنگ/درجه.")
    # 4. Top-level text sized to the on-screen logo box.
    if brand.brand_name:
        try:
            from .animation_engine import choose_font_for_tgs
            style, font_path, desc = choose_font_for_tgs(doc, item_index)
            if median is not None and traj is not None:
                name, size, bw, bh = add_top_level_text_from_box(
                    doc, brand.brand_name, font_path, median, traj)
                warnings.append(
                    f"نام برند «{brand.brand_name}» در سطح اصلی، با اندازه {size:.0f} "
                    f"دقیقاً در باکس لوگوی قبلی ({bw:.0f}×{bh:.0f}) نشست (فونت: {desc}).")
            else:
                # Fallback (honest): no logo element found, bottom-centre.
                tgs_add_text_layer(doc, brand.brand_name, font_path, size=54.0)
                warnings.append(
                    f"لوگوی قابل‌شناسایی پیدا نشد؛ نام برند «{brand.brand_name}» "
                    f"به‌صورت جایگزین پایین صفحه اضافه شد (فونت: {desc}).")
        except Exception as exc:
            warnings.append(f"افزودن نام برند ناموفق بود: {str(exc)[:200]}")
    # Final safety: prune again (text addition cannot empty layers, but
    # recolour cannot either; keep the file clean for the validator).
    tgs_prune_empty_shape_layers(doc)
    out = dump_tgs(doc)
    return out, warnings
