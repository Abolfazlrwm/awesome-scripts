"""v5 engine: in-place logo replacement — stacking, parenting, animation.

Why v5 exists (user feedback on v4, 2026-10-29):
  The old logo was ANIMATED and GLUED to other layers. v4 placed text
  at layer index 0 (topmost) with a composed top-level transform
  sampled at median frames. That broke exactly what the user points
  at:
  * STACKING: layers that were ABOVE the old logo (white circle,
    cursor, sparkles, banner foreground) ended up BELOW the text,
    because text was on top of everything.
  * PARENTING/GLUE: the old logo moved/rotated/scaled with its
    parent chain. v4's composed transform did not follow that
    animation frame-by-frame, and it lost the parent relationship.

v5 therefore does NOT compose a top-level transform and does NOT
insert at index 0. For each file:
  1. Capture the main logo element BEFORE any deletion: a whole
     layer (top-level or asset/precomp) or, when the logo only
     exists as a group inside a scene layer, that group.
  2. Replace it IN PLACE:
     - Layer: same list position, same ind, same parent (resolved
       through deleted logo ancestors), SAME ks verbatim (position,
       anchor, scale, rotation, opacity, ALL keyframes — including
       a 180 deg rotation, which is fidelity, not a bug), same
       ip/op (verbatim, even 9999 inside assets — asset timing is
       controlled by the precomp instance, not the doc op).
       Because the new layer keeps the old ind, every surviving
       child that was parented to the old logo stays parented to
       the text with no reparenting step at all — it stays glued.
     - Group: same group index inside the same layer, text group
       inherits the containing layer's ks/parent/animation exactly.
  3. Size the text so that, WITH that verbatim ks and the full
     parent/precomp scale chain, its on-screen box matches the old
     logo's on-screen box (width AND height). Size is solved by
     transforming the actual text bbox through the full matrix
     (iterative refinement), not by a local-width formula and not
     by a 54-110 clamp — exactness beats the old clamp. If the
     exact size is tiny (file 011 TON badge), it stays tiny and is
     reported as exact-but-small.
  4. Delete every OTHER logo/brandmark/watermark element
     (named logo layers, TON/FREE/CDC/SVN, pack-common watermark
     groups, emblem layers that are not the main element). The main
     element is replaced, never merely kept: v4 kept file 001's
     emblem layer (it was only a placement fallback), which is why
     its grey triangle peeked around the text.
  5. Keep v4's selective theme recolour, pack-common detection,
     empty-layer pruning and validator. Asset-scope text IS used
     for asset logos: v3's asset-scope failure was never proven
     (it coincided with empty-layer failures), and asset-scope is
     the only way stacking/parenting inside a precomp is preserved.
"""
import copy
import math

from .animation_engine import (
    _apply_to_gradient, _walk, load_tgs, dump_tgs,
    pack_common_signatures, shape_signature, text_to_lottie_shapes,
    tgs_hide_text_layers, tgs_logo_candidates,
    tgs_prune_empty_shape_layers, tgs_prune_unused_assets,
    tgs_remove_layers, tgs_remove_shapes_by_signatures,
    tgs_remove_watermark_shapes,
)
from .v4 import (
    BRAND_MARK_RE, SUBJECT_SKIP_RE, _chain_matrix, _group_bbox,
    _layer_matrix, _layer_shape_bbox, _mat_apply, _mat_mul, _on_screen_box,
    _on_screen_box_for_element, _prop_at, _referencing_layers,
    _scope_layers, find_logo_element, is_subject_layer,
    sample_logo_trajectory, tgs_fix_dangling_parents,
    tgs_selective_recolor,
    tgs_selective_theme_recolor,
)


def _is_text_logo_name(name: str) -> bool:
    n = str(name or "")
    return ("Outlines" in n and
            any(w in n for w in ("SVN", "اینترنت", "$SVN", "SAEED")))


def _candidate_layers(doc: dict, hide_names: set):
    """All logo/brandmark candidate layers, with scope and list index."""
    cands = set(tgs_logo_candidates(doc)) | set(hide_names or [])
    out = []
    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for pos, layer in enumerate(layers):
            name = str(layer.get("nm", ""))
            tier = None
            if _is_text_logo_name(name) and layer.get("ty") == 4:
                tier = 0
            elif name in cands:
                tier = 1
            elif BRAND_MARK_RE.search(name):
                tier = 2
            elif "emblem" in name.lower():
                tier = 3
            if layer.get("ty") == 0 and tier is not None:
                # A precomp instance is not the logo itself; the logo
                # shapes live in the asset it references. Deprioritise
                # it so the asset layer wins (file 007 SAEED -> fdsfgd).
                tier += 10
            if tier is not None:
                out.append({"scope": scope, "layers": layers, "pos": pos,
                            "layer": layer, "tier": tier, "name": name})
    return out


def _watermark_groups(doc: dict, signatures: set):
    """Groups containing a pack-common shape, with scope/layer/group index."""
    out = []
    if not signatures:
        return out
    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for layer in layers:
            for gi, item in enumerate(layer.get("shapes") or []):
                if not isinstance(item, dict) or item.get("ty") != "gr":
                    continue
                for node in _walk(item):
                    if isinstance(node, dict) and node.get("ty") == "sh":
                        if shape_signature((node.get("ks") or {}).get("k") or {}) in signatures:
                            pts = _group_bbox([item], (1, 0, 0, 1, 0, 0))
                            bbox = None
                            if pts:
                                xs = [p[0] for p in pts]
                                ys = [p[1] for p in pts]
                                bbox = (min(xs), min(ys), max(xs), max(ys))
                            out.append({"scope": scope, "layer": layer,
                                        "group_index": gi, "group": item,
                                        "bbox": bbox})
                            break
    return out


def _group_tr_matrix(group: dict, frame: int):
    """Matrix of a shape group's own transform item at a frame."""
    for item in group.get("it") or []:
        if isinstance(item, dict) and item.get("ty") == "tr":
            return _layer_matrix({"ks": item}, frame)
    return (1, 0, 0, 1, 0, 0)


def _white_stats(items):
    """(n_paths, n_white, pts) for a group's subitems (tr excluded).

    A path counts as white when its own group's fill is near-white
    (r, g, b all >= 0.85). Fills are resolved per group, the way the
    exporter writes them ([sh, fl] siblings inside each subgroup).
    """
    n_paths = 0
    n_white = 0
    pts = []

    def _fill_of(group_items):
        for s in group_items:
            if isinstance(s, dict) and s.get("ty") == "fl":
                k = (s.get("c") or {}).get("k")
                if isinstance(k, list) and len(k) >= 3:
                    return (float(k[0]), float(k[1]), float(k[2]))
        return None

    def _rec(nodes):
        nonlocal n_paths, n_white
        for node in nodes:
            if not isinstance(node, dict):
                continue
            if node.get("ty") == "gr":
                sub = [s for s in (node.get("it") or [])
                       if not (isinstance(s, dict) and s.get("ty") == "tr")]
                col = _fill_of(sub)
                for s in sub:
                    if isinstance(s, dict) and s.get("ty") == "sh":
                        n_paths += 1
                        # "White" for a brand mark includes the pale
                        # desaturated fills exporters bake text with
                        # (Mehdi file 034's chest text is (0.71, 0.80,
                        # 0.71)); saturated colours (skin, leaves)
                        # have a wide channel spread and stay out.
                        if (col and min(col) >= 0.68
                                and max(col) - min(col) <= 0.18):
                            n_white += 1
                _rec(sub)
            elif node.get("ty") == "sh":
                n_paths += 1

    _rec([s for s in (items or [])
          if not (isinstance(s, dict) and s.get("ty") == "tr")])
    try:
        pts = _group_bbox([s for s in (items or [])
                           if not (isinstance(s, dict) and s.get("ty") == "tr")],
                          (1, 0, 0, 1, 0, 0))
    except Exception:
        # Some exporter constructs (animated rect sizes) crash the
        # bbox walker; a group we cannot measure is not a candidate.
        pts = []
    return n_paths, n_white, pts


def _find_white_mark_groups(doc: dict):
    """Structural candidates for a logo mark drawn on a held object.

    The Mehdi-pack lesson (2026-10-29): in the 'exploded' files the
    old logo (a white M + phone glyph) sits in a DEDICATED layer whose
    only item is that group, mixed white/brand-colour subpaths, with
    an invisible duplicate copy in a sibling layer. Structure alone
    cannot tell the visible copy from the dead one, so these are only
    candidates — `_verify_marks_render` confirms them against real
    rlottie renders before anything is replaced.
    """
    out = []
    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for layer in layers:
            if layer.get("ty") != 4:
                continue
            shapes = layer.get("shapes") or []
            if len(shapes) != 1:
                continue
            item = shapes[0]
            if not isinstance(item, dict) or item.get("ty") != "gr":
                continue
            try:
                _n, n_white, pts = _white_stats(item.get("it") or [])
            except Exception:
                continue
            if n_white < 2 or not pts:
                continue
            # Descend single-child wrapper groups to the CORE group
            # that directly holds the artwork subgroups. Every
            # wrapper's transform must survive the replacement, so
            # the text goes into the core, not the outer shell
            # (Mehdi file 012: two wrapper levels; dropping one
            # shifted the text off the mug).
            chain = [item]
            core = item
            while True:
                subs = [s for s in (core.get("it") or [])
                        if isinstance(s, dict) and s.get("ty") != "tr"]
                if len(subs) == 1 and subs[0].get("ty") == "gr":
                    core = subs[0]
                    chain.append(core)
                else:
                    break
            core_subs = [s for s in (core.get("it") or [])
                         if not (isinstance(s, dict) and s.get("ty") == "tr")]
            try:
                cpts = _group_bbox(core_subs, (1, 0, 0, 1, 0, 0))
            except Exception:
                continue
            if not cpts:
                continue
            xs = [p[0] for p in cpts]
            ys = [p[1] for p in cpts]
            out.append({"scope": scope, "layer": layer, "group": item,
                        "core": core, "chain": chain, "path": (0,),
                        "parent_items": shapes,
                        "local_bbox": (min(xs), min(ys), max(xs), max(ys))})
    return out


def _find_mark_candidates_deep(doc: dict, cap: int = 40, depth: int = 2):
    """Deep candidates for the hard files: any group at depth <= 2 in
    any shape layer that holds >= 2 near-white paths and is not
    scene-sized. The old logo in these files sits in the middle of a
    shared scene layer (a coin's centre, a shirt's chest), so the
    dedicated-layer rule cannot see it; the rlottie diff verification
    is what separates marks from white scene content, and every
    survivor is still eyeballed on a contact sheet before publish.
    """
    out = []

    def _consider(scope, layer, group, chain, path, parent_items):
        try:
            _n, n_white, _pts = _white_stats(group.get("it") or [])
        except Exception:
            return
        if n_white < 2:
            return
        subs = [s for s in (group.get("it") or [])
                if not (isinstance(s, dict) and s.get("ty") == "tr")]
        try:
            pts = _group_bbox(subs, (1, 0, 0, 1, 0, 0))
        except Exception:
            return
        if not pts:
            return
        xs = [p[0] for p in pts]
        ys = [p[1] for p in pts]
        w = max(xs) - min(xs)
        h = max(ys) - min(ys)
        if w <= 0 or h <= 0 or w * h > 0.6 * 512 * 512:
            return
        out.append({"scope": scope, "layer": layer, "group": group,
                    "core": group, "chain": chain, "path": path,
                    "parent_items": parent_items,
                    "local_bbox": (min(xs), min(ys), max(xs), max(ys)),
                    "_white": n_white, "_area": w * h})

    def _walk_group(scope, layer, group, chain, path, level):
        if level >= depth:
            return
        for j, sub in enumerate(group.get("it") or []):
            if isinstance(sub, dict) and sub.get("ty") == "gr":
                _consider(scope, layer, sub, chain + [sub],
                          path + (j,), group.get("it") or [])
                _walk_group(scope, layer, sub, chain + [sub],
                            path + (j,), level + 1)

    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for layer in layers:
            if layer.get("ty") != 4:
                continue
            shapes = layer.get("shapes") or []
            for i, item in enumerate(shapes):
                if not isinstance(item, dict) or item.get("ty") != "gr":
                    continue
                _consider(scope, layer, item, [item], (i,), shapes)
                _walk_group(scope, layer, item, [item], (i,), 1)
    # Prefer mark-like candidates: many white paths, compact area.
    out.sort(key=lambda m: (-m["_white"], m["_area"]))
    return out[:cap]


def _remove_group_at_path(doc: dict, scope, layer_ind, path):
    """Remove the group at an index path inside a layer's shapes
    (used on a deep copy during render verification)."""
    layers = _scope_layers(doc, scope)
    for layer in layers:
        if layer.get("ind") == layer_ind:
            items = layer.get("shapes") or []
            for idx in path[:-1]:
                if idx >= len(items) or not isinstance(items[idx], dict):
                    return
                items = items[idx].get("it") or []
            if path[-1] < len(items):
                items.pop(path[-1])
            return


def _dedupe_marks(marks: list) -> list:
    """Drop a verified mark whose group CONTAINS another verified
    mark's group (same layer, path prefix): replacing the tighter
    unit is enough, replacing both would double-place the text."""
    kept = []
    for m in sorted(marks, key=lambda x: -len(x.get("path") or ())):
        mp = m.get("path") or ()
        contained = False
        for k in kept:
            kp = k.get("path") or ()
            if (k["scope"] == m["scope"]
                    and k["layer"].get("ind") == m["layer"].get("ind")
                    and len(kp) > len(mp) and kp[:len(mp)] == mp):
                contained = True
                break
        if not contained:
            kept.append(m)
    return kept


def _render_frames(data: bytes, frames):
    """Render TGS frames with rlottie (Telegram's own renderer).
    Returns a list of PIL RGB images, or None when unavailable."""
    try:
        import os
        import tempfile

        from PIL import Image  # noqa: F401  (ensures Pillow exists)
        from rlottie_python import LottieAnimation
    except Exception:
        return None
    path = None
    try:
        with tempfile.NamedTemporaryFile(suffix=".tgs", delete=False) as fh:
            fh.write(data)
            path = fh.name
        anim = LottieAnimation.from_tgs(path)
        return [anim.render_pillow_frame(f).convert("RGB") for f in frames]
    except Exception:
        return None
    finally:
        if path:
            try:
                os.unlink(path)
            except OSError:
                pass


def _verify_marks_render(doc: dict, marks: list, min_changed: int = 300,
                         min_white: int = 120, frame_fracs=(0, 0.25, 0.5, 0.75)) -> list:
    """Keep only candidates that provably draw the old logo.

    For each candidate: render the document with and without the
    candidate group at sampled frames (rlottie). The candidate is
    the visible logo when removing it changes a compact region
    (>= min_changed px) holding many bright pixels (>= min_white)
    in the original render. CONSISTENCY RULE (the file-166 lesson):
    if at ANY sampled frame the removal changes a scene-scale area
    (> 40,000 px or a box > 320 px), the candidate is a scene group
    wearing the logo, not a logo — reject it outright, because
    replacing it would gut the scene. The measured box is stored on
    the mark for exact size solving. Without rlottie, nothing is
    verified and the caller falls back to the bottom-text placement.
    """
    if not marks:
        return []
    from .animation_engine import dump_tgs as _dump
    doc_op = doc.get("op", 180)
    frames = sorted(set(int(doc_op * f) for f in frame_fracs))
    base = _render_frames(_dump(doc), frames)
    if base is None:
        return []
    from PIL import ImageChops
    verified = []
    for mark in marks:
        try:
            doc2 = copy.deepcopy(doc)
            _remove_group_at_path(doc2, mark["scope"],
                                  mark["layer"].get("ind"),
                                  mark.get("path") or (0,))
            var = _render_frames(_dump(doc2), frames)
            if var is None:
                continue
            best = None
            rejected = False
            for b_img, v_img in zip(base, var):
                d = ImageChops.difference(b_img, v_img).convert("L")
                mask = d.point(lambda v: 255 if v > 24 else 0)
                bbox = mask.getbbox()
                if bbox is None:
                    continue
                region = d.crop(bbox)
                changed = sum(1 for v in region.getdata() if v > 24)
                w = bbox[2] - bbox[0]
                h = bbox[3] - bbox[1]
                if changed >= min_changed and (changed > 40000 or w > 320 or h > 320):
                    rejected = True
                    break
                orig_region = b_img.crop(bbox)
                # Bright-mark pixels in the original render: >= 175
                # per channel (pale baked text renders ~180-204,
                # pure-white marks 255; both are brand marks).
                whites = sum(1 for r, g, b in orig_region.getdata()
                             if r >= 175 and g >= 175 and b >= 175)
                if (min_changed <= changed <= 40000 and whites >= min_white
                        and w <= 320 and h <= 320):
                    if best is None or changed > best[0]:
                        best = (changed, w, h)
            if best is not None and not rejected:
                mark["measured"] = (best[1], best[2])
                verified.append(mark)
        except Exception:
            continue
    return verified


def _mark_matrix(doc: dict, mark: dict, frame: int):
    m, op = _full_matrix(doc, mark["scope"], mark["layer"], frame)
    for grp in mark.get("chain") or [mark["group"]]:
        m = _mat_mul(m, _group_tr_matrix(grp, frame))
    return m, op


def _mark_visible_box(doc: dict, mark: dict):
    """(on_w, on_h, matrix, frame, opacity) at the median visible frame."""
    lb = mark["local_bbox"]
    doc_op = doc.get("op", 180)
    rows = []
    for f in sorted(set(int(doc_op * i / 10) for i in range(11))):
        m, op = _mark_matrix(doc, mark, f)
        pts = [_mat_apply(m, x, y) for x, y in
               [(lb[0], lb[1]), (lb[2], lb[1]), (lb[0], lb[3]), (lb[2], lb[3])]]
        xs = [q[0] for q in pts]
        ys = [q[1] for q in pts]
        rows.append((f, max(xs) - min(xs), max(ys) - min(ys), m, op))
    vis = [r for r in rows if r[1] > 10 and r[2] > 10 and r[4] > 10]
    if vis:
        ws = sorted(r[1] for r in vis)
        hs = sorted(r[2] for r in vis)
        mid = len(vis) // 2
        med_area = ws[mid] * hs[mid]
        best = min(vis, key=lambda r: abs(r[1] * r[2] - med_area))
        return ws[mid], hs[mid], best[3], best[0], best[4]
    if rows:
        best = max(rows, key=lambda r: r[1] * r[2])
        return best[1], best[2], best[3], best[0], best[4]
    return 0, 0, (1, 0, 0, 1, 0, 0), int(doc_op * 0.5), 0


def _mat_det(m) -> float:
    return m[0] * m[3] - m[1] * m[2]


def _apply_flip(text_group: dict, flip: bool) -> dict:
    """Counter-mirror a text group when its slot's transform chain
    flips horizontally (det < 0): without this the brand name renders
    as mirror writing (Mehdi files 019/075/122/151). The group's own
    transform scales x by -1, cancelling the chain's flip; the caller
    must also mirror the baked centring offset (see _center_offset).
    """
    if not flip:
        return text_group
    for item in text_group.get("it") or []:
        if isinstance(item, dict) and item.get("ty") == "tr":
            item["s"] = {"a": 0, "k": [-100, 100]}
    return text_group


def _center_offset(lb, bb, flip: bool):
    lcx = (lb[0] + lb[2]) / 2
    lcy = (lb[1] + lb[3]) / 2
    tcx = (bb[0] + bb[2]) / 2
    tcy = (bb[1] + bb[3]) / 2
    return ((-lcx - tcx) if flip else (lcx - tcx), lcy - tcy)


def _replace_mark_with_text(mark: dict, brand, font_path: str, doc: dict):
    """Replace one white-mark group's artwork with the brand text,
    in place: same group shell, same transform, same stacking."""
    lb = mark["local_bbox"]
    on_w, on_h, matrix, _frame, _op = _mark_visible_box(doc, mark)
    if mark.get("measured"):
        # Ground truth from the rlottie diff beats the matrix estimate.
        on_w, on_h = mark["measured"]
    if not (12 <= on_w <= 320 and 12 <= on_h <= 320):
        return None
    size, shapes, bb = _solve_text_size(brand.brand_name, font_path,
                                        on_w, on_h, matrix, lb)
    if not bb:
        return None
    flip = _mat_det(matrix) < 0
    offset = _center_offset(lb, bb, flip)
    tg = _apply_flip(_make_text_group(shapes, offset, *_text_style(brand, size)), flip)
    tg["nm"] = f"{brand.brand_name} Brand"
    group = mark.get("core") or mark["group"]
    tr_items = [s for s in (group.get("it") or [])
                if isinstance(s, dict) and s.get("ty") == "tr"]
    group["it"] = [tg] + tr_items
    group["nm"] = f"{brand.brand_name} Brand"
    return {"size": size, "on_w": on_w, "on_h": on_h}


def _full_matrix(doc: dict, scope, layer: dict, frame: int, _depth: int = 0):
    """Full on-screen matrix for a layer, following precomp instances.

    Returns (matrix, opacity) where matrix maps layer-space points to
    on-screen points. For an asset layer, the largest-area referencing
    instance wins (same rule as v4's on-screen box).
    """
    layers = _scope_layers(doc, scope)
    m = _chain_matrix(doc, layers, layer, frame)
    o = _prop_at((layer.get("ks") or {}).get("o") or {}, frame)
    op = float(o[0]) if isinstance(o, list) and o else float(o) if isinstance(o, (int, float)) else 100.0
    if scope is None:
        return m, op
    best = None
    for ref_scope, ref_layer in _referencing_layers(doc, scope):
        if not (ref_layer.get("ip", 0) <= frame <= (ref_layer.get("op") or 10 ** 9)):
            continue
        # Recursive: the referencing layer may itself live in an asset
        # (file 017: comp_2 -> comp_0 -> top), so its chain must also
        # be followed to frame space.
        ref_m, _ro = (_full_matrix(doc, ref_scope, ref_layer, frame, _depth + 1)
                        if _depth < 6 else (_chain_matrix(doc, _scope_layers(doc, ref_scope), ref_layer, frame), 100.0))
        full_m = _mat_mul(ref_m, m)
        # Area proxy: determinant magnitude.
        det = abs(full_m[0] * full_m[3] - full_m[1] * full_m[2])
        ro = _prop_at((ref_layer.get("ks") or {}).get("o") or {}, frame)
        ref_op = float(ro[0]) if isinstance(ro, list) and ro else float(ro) if isinstance(ro, (int, float)) else 100.0
        if best is None or det > best[0]:
            best = (det, full_m, op * ref_op / 100.0)
    if best is None:
        return m, op
    return best[1], best[2]


def _text_bbox_at(shapes):
    xs, ys = [], []
    for sh in shapes:
        v = (sh.get("ks") or {}).get("k") or {}
        for pt in v.get("v") or []:
            xs.append(float(pt[0]))
            ys.append(float(pt[1]))
    if not xs:
        return None
    return (min(xs), min(ys), max(xs), max(ys))


def _solve_text_size(text: str, font_path: str, target_w: float, target_h: float,
                     matrix, logo_bbox):
    """Solve the font size whose transformed text bbox matches target_w x target_h.

    matrix maps layer-space to on-screen. logo_bbox is only used for
    its centre (the offset is computed by the caller from the same
    bbox); here we iterate: start from the scale-magnitude estimate,
    transform the actual text bbox, correct by the worst ratio.
    Returns (size, shapes, text_bbox) with size unclamped (exactness
    beats the old 54-110 clamp; floor at 8 so a degenerate logo does
    not produce an invisible or zero-size text).
    """
    if target_w <= 0 or target_h <= 0:
        _sh, _w = text_to_lottie_shapes(text, font_path, 54.0)
        return 54.0, _sh, _text_bbox_at(_sh)
    sx = math.hypot(matrix[0], matrix[1])
    sy = math.hypot(matrix[2], matrix[3])
    if sx <= 0 or sy <= 0:
        sx = sy = 1.0
    _, w36 = text_to_lottie_shapes(text, font_path, 36.0)
    shapes36, _ = text_to_lottie_shapes(text, font_path, 36.0)
    bb36 = _text_bbox_at(shapes36)
    h36 = (bb36[3] - bb36[1]) if bb36 else 25.0
    if w36 <= 0 or h36 <= 0:
        _sh, _w = text_to_lottie_shapes(text, font_path, 54.0)
        return 54.0, _sh, _text_bbox_at(_sh)
    size = 36.0 * min(target_w / (w36 * sx), target_h / (h36 * sy))
    size = max(8.0, size)
    shapes = None
    bb = None
    for _ in range(3):
        shapes, _w = text_to_lottie_shapes(text, font_path, size)
        bb = _text_bbox_at(shapes)
        if not bb:
            break
        corners = [(bb[0], bb[1]), (bb[2], bb[1]), (bb[0], bb[3]), (bb[2], bb[3])]
        pts = [_mat_apply(matrix, x, y) for x, y in corners]
        xs = [p[0] for p in pts]
        ys = [p[1] for p in pts]
        ow, oh = max(xs) - min(xs), max(ys) - min(ys)
        if ow <= 0 or oh <= 0:
            break
        ratio = min(target_w / ow, target_h / oh)
        if 0.95 <= ratio <= 1.05:
            break
        size = max(8.0, size * ratio)
    shapes, _w = text_to_lottie_shapes(text, font_path, size)
    bb = _text_bbox_at(shapes)
    return size, shapes, bb


def _text_style(brand, size):
    """(stroke_width, stroke_rgb) for the brand text group: a chunky
    outline in a deep shade of the brand's own primary colour, so the
    white cute-bold letters read like a sticker on any background."""
    try:
        import colorsys
        from .v4 import hex_to_rgb01
        r, g, b = hex_to_rgb01(getattr(brand, "primary_hex", "") or "#8000FF")
        h, _l, s = colorsys.rgb_to_hls(r, g, b)
        sr, sg, sb = colorsys.hls_to_rgb(h, 0.14, min(1.0, s))
    except Exception:
        sr, sg, sb = 0.0, 0.0, 0.0
    return max(2.0, min(7.0, size * 0.055)), (sr, sg, sb)


def _make_text_group(shapes, offset, stroke_w=2.0, stroke_rgb=(0.0, 0.0, 0.0)):
    dx, dy = offset
    # Bake the centring offset into the path vertices and keep the
    # group transform at [0,0] — the accepted v3/v6 brand shape. A
    # non-zero group transform offset was one of the three traits of
    # the only Telegram-rejected brand layer (file 016, v5).
    baked = []
    for sh in shapes:
        sh = copy.deepcopy(sh)
        k = (sh.get("ks") or {}).get("k") or {}
        v = k.get("v")
        if isinstance(v, list):
            k["v"] = [[round(float(p[0]) + dx, 2), round(float(p[1]) + dy, 2)]
                      if isinstance(p, list) and len(p) >= 2 else p for p in v]
        baked.append(sh)
    return {"ty": "gr", "it": baked + [
        {"ty": "fl", "c": {"a": 0, "k": [1, 1, 1, 1]}, "o": {"a": 0, "k": 100},
         "r": 1, "bm": 0, "nm": "Fill 1", "hd": False},
        {"ty": "st", "c": {"a": 0, "k": [round(stroke_rgb[0], 3), round(stroke_rgb[1], 3),
                                        round(stroke_rgb[2], 3), 1]},
         "o": {"a": 0, "k": 100},
         "w": {"a": 0, "k": round(stroke_w, 2)}, "lc": 1, "lj": 1, "bm": 0,
         "nm": "Stroke 1", "hd": False},
        {"ty": "tr", "p": {"a": 0, "k": [0, 0]},
         "a": {"a": 0, "k": [0, 0]}, "s": {"a": 0, "k": [100, 100]},
         "r": {"a": 0, "k": 0}, "o": {"a": 0, "k": 100},
         "sk": {"a": 0, "k": 0}, "sa": {"a": 0, "k": 0}, "nm": "Transform"}],
        "nm": "E30TZ", "bm": 0, "hd": False}


def _resolve_parent(doc: dict, scope, layers, layer: dict, deleted_inds: set):
    """Parent ind for the replacement: walk up through deleted logo
    ancestors until a surviving layer (or None)."""
    seen = set()
    parent = layer.get("parent")
    while parent is not None and parent in deleted_inds and parent not in seen:
        seen.add(parent)
        anc = None
        for l in layers:
            if l.get("ind") == parent:
                anc = l
                break
        parent = anc.get("parent") if anc is not None else None
    if parent is not None:
        inds = {l.get("ind") for l in layers}
        if parent not in inds:
            return None
    return parent



def _visible_median_box(doc: dict, scope, layer: dict, local_bbox):
    """On-screen box at the max-area visible frame (see below).

    v4's median across 0/25/50/75/100% included frames where a parent
    scales the logo to zero (file 009 Back: scale-x 0->100->0), giving
    a 0-width median and a wrong text size. Sample every 10%, keep
    only frames with w>10, h>10, opacity>10, take the median of those;
    if none, take the max-area frame. Returns (w, h, frame).
    """
    doc_op = doc.get("op", 180)
    frames = sorted(set(int(doc_op * f) for f in [i / 10 for i in range(11)]))
    rows = []
    for f in frames:
        try:
            box = _on_screen_box_for_element(doc, {"scope": scope, "layer": layer}, f)
        except Exception:
            box = None
        if box is None:
            continue
        x0, y0, x1, y1, _rot, op = box
        rows.append((f, x1 - x0, y1 - y0, op))
    vis = [r for r in rows if r[1] > 10 and r[2] > 10 and r[3] > 10]
    if vis:
        # Median visible area (robust to a 1-frame scale pop, e.g.
        # file 009 Null2 1180%), with the matrix taken at the frame
        # whose area is closest to that median. Because the verbatim ks
        # scales text and logo identically at every frame, exactness at
        # that frame propagates to all frames.
        ws = sorted(r[1] for r in vis)
        hs = sorted(r[2] for r in vis)
        mid = len(vis) // 2
        med_area = ws[mid] * hs[mid]
        best = min(vis, key=lambda r: abs(r[1] * r[2] - med_area))
        return ws[mid], hs[mid], best[0]
    if rows:
        best = max(rows, key=lambda r: r[1] * r[2])
        return best[1], best[2], best[0]
    if local_bbox:
        return local_bbox[2] - local_bbox[0], local_bbox[3] - local_bbox[1], int(doc_op * 0.5)
    return 0, 0, int(doc_op * 0.5)


def process_tgs_v5(data: bytes, brand, hide_names: set, item_index: int = 0,
                   pack_common_signatures: set | None = None,
                   theme_hue_deg: float | None = None,
                   aggressive_marks: bool = False,
                   recolor: bool = True):
    """Full v5 pipeline for one TGS. Returns (output_bytes, warnings, placement).

    placement: dict with scope, kind ('layer'|'group'|'fallback'),
    index, parent, size, logo_box (on-screen w x h) — or None on fallback.
    """
    doc = load_tgs(data)
    warnings = []
    placement = None
    # --- 1. Capture main logo element BEFORE any deletion.
    cands = _candidate_layers(doc, set(hide_names or []))
    wm_groups = _watermark_groups(doc, pack_common_signatures or set())
    # Score candidate layers by tier, then on-screen area at mid-frame.
    doc_op = doc.get("op", 180)
    mid_frame = int(doc_op * 0.5)
    main_layer = None
    for cand in cands:
        try:
            _w, _h, _f = _visible_median_box(
                doc, cand["scope"], cand["layer"],
                _layer_shape_bbox(cand["layer"]))
            cand["area"] = _w * _h
        except Exception:
            cand["area"] = 0
    cands.sort(key=lambda c: (c["tier"], -c["area"]))
    main_group = None
    if not cands and wm_groups:
        # Largest watermark group (by local bbox area) is the main logo.
        def _garea(g):
            b = g["bbox"]
            return (b[2] - b[0]) * (b[3] - b[1]) if b else 0
        wm_groups.sort(key=_garea, reverse=True)
        main_group = wm_groups[0]
    if cands:
        main_layer = cands[0]
    # Record main details for the report before deletion.
    if main_layer is not None:
        layer = main_layer["layer"]
        scope = main_layer["scope"]
        layers = main_layer["layers"]
        pos = main_layer["pos"]
        logo_local_bbox = _layer_shape_bbox(layer)
        # Visible-median box: only frames where the logo actually
        # renders (parent scale/opacity > 0), NOT a blind median that
        # includes zero-scale frames (file 009 Back scale-x 0->100->0).
        try:
            logo_on_w, logo_on_h, vis_frame = _visible_median_box(
                doc, scope, layer, logo_local_bbox)
            mid_frame = vis_frame
        except Exception:
            logo_on_w = logo_on_h = 0
            if logo_local_bbox:
                logo_on_w = logo_local_bbox[2] - logo_local_bbox[0]
                logo_on_h = logo_local_bbox[3] - logo_local_bbox[1]
        children = [l.get("nm") for l in layers if l.get("parent") == layer.get("ind")]
        above = [l.get("nm") for l in layers[:pos]]
        below = [l.get("nm") for l in layers[pos + 1:]]
        main_info = {"scope": scope, "kind": "layer", "index": pos,
                     "ind": layer.get("ind"), "name": layer.get("nm"),
                     "parent": layer.get("parent"),
                     "ks": copy.deepcopy(layer.get("ks") or {}),
                     "ip": layer.get("ip", 0), "op": layer.get("op"),
                     "local_bbox": logo_local_bbox,
                     "on_w": logo_on_w, "on_h": logo_on_h,
                     "children": children, "above": above, "below": below,
                     "frame": mid_frame}
    elif main_group is not None:
        layer = main_group["layer"]
        scope = main_group["scope"]
        # On-screen box of the group: transform its local bbox corners
        # through the containing layer's full matrix.
        gb = main_group["bbox"]
        on_w = on_h = 0
        matrix, _op = _full_matrix(doc, scope, layer, mid_frame)
        if gb:
            # Visible-median across frames (same rule as layers).
            doc_op2 = doc.get("op", 180)
            rows = []
            for f in sorted(set(int(doc_op2 * i / 10) for i in range(11))):
                m2, _o2 = _full_matrix(doc, scope, layer, f)
                pts = [_mat_apply(m2, x, y) for x, y in
                       [(gb[0], gb[1]), (gb[2], gb[1]), (gb[0], gb[3]), (gb[2], gb[3])]]
                xs = [q[0] for q in pts]
                ys = [q[1] for q in pts]
                rows.append((f, max(xs) - min(xs), max(ys) - min(ys), m2))
            vis = [r for r in rows if r[1] > 10 and r[2] > 10]
            if vis:
                ws = sorted(r[1] for r in vis)
                hs = sorted(r[2] for r in vis)
                mid = len(vis) // 2
                med_area = ws[mid] * hs[mid]
                best = min(vis, key=lambda r: abs(r[1] * r[2] - med_area))
                on_w, on_h = ws[mid], hs[mid]
                matrix = best[3]
                mid_frame = best[0]
            else:
                m2, _o2 = _full_matrix(doc, scope, layer, mid_frame)
                pts = [_mat_apply(m2, x, y) for x, y in
                       [(gb[0], gb[1]), (gb[2], gb[1]), (gb[0], gb[3]), (gb[2], gb[3])]]
                xs = [q[0] for q in pts]
                ys = [q[1] for q in pts]
                on_w, on_h = max(xs) - min(xs), max(ys) - min(ys)
        main_info = {"scope": scope, "kind": "group", "index": main_group["group_index"],
                     "layer_name": layer.get("nm"), "layer_ind": layer.get("ind"),
                     "group_bbox": gb, "on_w": on_w, "on_h": on_h,
                     "matrix": matrix, "frame": mid_frame}
    else:
        main_info = None
    # Scene guard (the file-166 lesson): in a near-single-layer file,
    # a "logo layer" whose on-screen box is a large BLOCK of the
    # canvas is the scene itself wearing the logo — replacing the
    # whole layer would gut the file. Demote it and let the
    # group/mark stages find the logo inside it; the layer is also
    # protected from the candidate-deletion step below. The
    # layer-count condition keeps legitimate big logo layers in
    # multi-layer comps (sVnTeam file 013's FREE board) untouched.
    protected_layers = set()
    if (main_info is not None and main_info["kind"] == "layer"
            and len(main_layer["layers"]) <= 2
            and main_info["on_w"] * main_info["on_h"] > 0.45 * 512 * 512
            and min(main_info["on_w"], main_info["on_h"]) > 150):
        protected_layers.add(id(main_layer["layer"]))
        warnings.append(
            "لایه «لوگو» در واقع خود صحنه است (باکس بزرگ)؛ تعویض کامل "
            "لایه لغو شد و لوگو داخل آن جست‌وجو می‌شود.")
        main_info = None
        main_layer = None
    if main_info is not None:
        warnings.append(
            f"لوگوی اصلی v5: {main_info['kind']} در {main_info['scope'] or 'top'} "
            f"با باکس روی صفحه {main_info['on_w']:.0f}×{main_info['on_h']:.0f}.")
    # White-mark groups (the old logo drawn white on a held object —
    # the Mehdi mug lesson): only relevant when no main element was
    # found by the layer/group detectors.
    marks = []
    extra_marks = []
    if main_info is None:
        try:
            if aggressive_marks:
                _found = _find_white_mark_groups(doc) + \
                    _find_mark_candidates_deep(doc, cap=200, depth=3)
                marks = _verify_marks_render(
                    doc, _found, min_changed=120, min_white=60,
                    frame_fracs=(0, 0.2, 0.4, 0.6, 0.8, 0.95))
            else:
                marks = _verify_marks_render(doc, _find_white_mark_groups(doc))
                if not marks:
                    # Hard files: the mark hides inside a shared scene
                    # layer — search every group (depth <= 2) the same
                    # render-verified way.
                    marks = _verify_marks_render(
                        doc, _find_mark_candidates_deep(doc))
            marks = _dedupe_marks(marks)
        except Exception:
            marks = []
    elif aggressive_marks:
        # A main placement exists, but a SECOND old mark may survive
        # elsewhere (double-branding files). Verify deep candidates;
        # the survivors are deleted (not replaced) after step 2.
        try:
            extra_marks = _dedupe_marks(_verify_marks_render(
                doc, _find_mark_candidates_deep(doc, cap=200, depth=3),
                min_changed=120, min_white=60,
                frame_fracs=(0, 0.2, 0.4, 0.6, 0.8, 0.95)))
        except Exception:
            extra_marks = []
    # Parent map BEFORE deletions, for grandparent reparenting later:
    # a surviving layer whose parent was deleted keeps the deleted
    # layer's parent (glue preserved) instead of dropping to None.
    parent_map = {}
    for _sc, _ls in [(None, doc.get("layers") or [])] + [
            (a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]:
        parent_map[_sc] = {l.get("ind"): l.get("parent") for l in _ls}
    main_scope = main_layer["scope"] if main_layer is not None else (
        main_group["scope"] if main_group is not None else None)
    # --- 2. Delete OTHER logo layers + watermark groups (not the main element).
    deleted_inds_by_scope = {}
    if main_layer is not None:
        # Delete every candidate layer except the main one, in every scope.
        for cand in cands[1:]:
            # Carrier preservation: a ty=0 instance referencing the main
            # asset CARRIES the replacement text (file 009: LOGO PLACE
            # instances carry the CDC text to front and back phones).
            # Deleting it would hide the text it carries.
            if (cand["layer"].get("ty") == 0
                    and cand["layer"].get("refId") == main_scope
                    and main_scope is not None):
                continue
            sc_layers = cand["layers"]
            sc = cand["scope"]
            deleted_inds_by_scope.setdefault(sc, set()).add(cand["layer"].get("ind"))
            # Remove from the live list (identity, not name — names repeat).
            sc_layers[:] = [l for l in sc_layers if l is not cand["layer"]]
        _ndel = sum(len(v) for v in deleted_inds_by_scope.values())
        if _ndel:
            warnings.append(f"لایه‌های لوگوی دیگر حذف شدند: {_ndel} لایه.")
    else:
        # No main layer: delete all candidate layers.
        for cand in cands:
            if id(cand["layer"]) in protected_layers:
                # Scene-sized layer (file-166 guard): never delete.
                continue
            if any(cand["layer"] is _mk["layer"] for _mk in marks):
                # Carries a white-mark group that step 4 will replace.
                continue
            if (cand["layer"].get("ty") == 0
                    and cand["layer"].get("refId") == main_scope
                    and main_scope is not None):
                continue
            sc = cand["scope"]
            deleted_inds_by_scope.setdefault(sc, set()).add(cand["layer"].get("ind"))
            cand["layers"][:] = [l for l in cand["layers"] if l is not cand["layer"]]
        if cands:
            warnings.append(f"لایه‌های لوگو حذف شدند: {len(cands)} لایه.")
    # Aggressive mode: delete SECOND verified marks that survived
    # next to a main placement (double-branding files). The main
    # element itself and anything inside the main layer are skipped —
    # the main replacement already handles those.
    if extra_marks:
        _main_layer_obj = main_layer["layer"] if main_layer is not None else None
        _main_group_obj = main_group["group"] if main_group is not None else None
        _ndelm = 0
        for _mk in extra_marks:
            if _mk["group"] is _main_group_obj:
                continue
            if _main_layer_obj is not None and _mk["layer"] is _main_layer_obj:
                continue
            _parent = _mk.get("parent_items")
            if _parent is None:
                continue
            for _i, _it in enumerate(_parent):
                if _it is _mk["group"]:
                    _parent.pop(_i)
                    _ndelm += 1
                    break
        if _ndelm:
            warnings.append(f"علامت‌های لوگوی دوم (تأییدشده با رندر) حذف شدند: {_ndelm}.")
    # Text layers (ty=5) are old brand text: hide them (validator
    # forbids hd=1 only for shape layers we leave; tgs_hide_text_layers
    # is the v4 behaviour — keep it, but the validator flags hd
    # layers of any type, so instead DELETE ty=5 layers outright.
    for layers in [doc.get("layers") or []] + [a.get("layers") or [] for a in doc.get("assets") or []]:
        before = len(layers)
        layers[:] = [l for l in layers if l.get("ty") != 5]
        if len(layers) != before:
            warnings.append(f"لایه‌های متنی قدیمی حذف شدند: {before - len(layers)} لایه.")
    pruned_assets = tgs_prune_unused_assets(doc)
    if pruned_assets:
        warnings.append(f"بخش‌های داخلی بدون ارجاع حذف شدند: {pruned_assets} بخش.")
    emptied = tgs_prune_empty_shape_layers(doc)
    if emptied:
        warnings.append(f"لایه‌های خالی‌شده حذف شدند: {emptied} لایه.")
    # NOTE: no tgs_fix_dangling_parents here — it would drop parents
    # that the grandparent-reparenting step (after insertion) needs.
    # Dangling parents are fixed there, with grandparent preservation.
    # --- 3. Selective theme recolour (before text insertion).
    # recolor=False (single-emoji «اسم/برند» / «لوگو» actions): the
    # user asked only for a name/logo swap — colours stay untouched.
    if not recolor:
        warnings.append("بازرنگی انجام نشد (به درخواست کاربر؛ فقط اسم/لوگو).")
    elif theme_hue_deg is not None and getattr(brand, "primary_hex", ""):
        changed = tgs_selective_recolor(doc, brand.primary_hex, theme_hue_deg)
        warnings.append(
            f"بازرنگی انتخابی تم (رنگ تم پک ← {brand.primary_hex}، طیف با روشنایی "
            f"حفظ شد؛ سوژه‌ها و رنگ‌های طبیعی دست نخوردند): {changed} رنگ/درجه.")
    else:
        changed = tgs_selective_theme_recolor(doc)
        warnings.append(f"بازرنگی انتخابی تم (آبی→قرمز، سوژه‌ها حفظ شدند): {changed} رنگ/درجه.")
    # --- 4. Insert text IN PLACE.
    marks_done = 0
    if brand.brand_name and main_info is None and marks:
        try:
            from .animation_engine import choose_font_for_tgs
            _mstyle, _mfont, _mdesc = choose_font_for_tgs(doc, item_index)
            _msizes = []
            for _mk in marks:
                _r = _replace_mark_with_text(_mk, brand, _mfont, doc)
                if _r:
                    marks_done += 1
                    _msizes.append(_r["size"])
            if marks_done:
                placement = {"scope": None, "kind": "named-mark",
                             "count": marks_done,
                             "size": round(_msizes[0], 1) if _msizes else 0}
                warnings.append(
                    f"نام برند «{brand.brand_name}» دقیقاً جای {marks_done} علامت "
                    f"سفید لوگو (روی شیء صحنه) نشست (فونت: {_mdesc}).")
        except Exception as exc:
            warnings.append(f"جایگذاری جای علامت سفید ناموفق بود: {str(exc)[:200]}")
            marks_done = 0
    if brand.brand_name and main_info is not None:
        try:
            from .animation_engine import choose_font_for_tgs
            style, font_path, desc = choose_font_for_tgs(doc, item_index)
            if main_info["kind"] == "layer":
                scope = main_info["scope"]
                layers = _scope_layers(doc, scope)
                # Re-find the main layer by ind (lists may have shifted).
                layer = None
                pos = None
                for i, l in enumerate(layers):
                    if l.get("ind") == main_info["ind"]:
                        layer, pos = l, i
                        break
                if layer is None:
                    raise RuntimeError("لایه لوگوی اصلی پس از حذف‌ها پیدا نشد.")
                parent = _resolve_parent(doc, scope, layers, layer,
                                         deleted_inds_by_scope.get(scope, set()))
                matrix, _op = _full_matrix(doc, scope, layer, main_info["frame"])
                size, shapes, bb = _solve_text_size(
                    brand.brand_name, font_path, main_info["on_w"], main_info["on_h"],
                    matrix, main_info["local_bbox"])
                if main_info["local_bbox"] and bb:
                    lb = main_info["local_bbox"]
                    lcy = (lb[1] + lb[3]) / 2
                    tcy = (bb[1] + bb[3]) / 2
                    _flip = _mat_det(matrix) < 0
                    if _is_text_logo_name(main_info["name"]):
                        # TEXT logos read left-to-right: align the LEFT
                        # edge, not the centre. The old banner text's
                        # centre is off-frame right (box 390->1267);
                        # centre-aligning a shorter "E30TZ" puts it
                        # entirely off-frame (v5 file 017 invisible).
                        # Left-edge alignment keeps the readable start
                        # exactly where the old text's start was.
                        # Under a flipped chain, align the mirrored
                        # left edge instead (mirror-writing guard).
                        offset = ((-lb[0] - bb[2]) if _flip else (lb[0] - bb[0]),
                                  lcy - tcy)
                    else:
                        offset = _center_offset(lb, bb, _flip)
                else:
                    offset = (0, 0)
                    _flip = False
                text_layer = {
                    "ddd": 0, "ind": main_info["ind"], "ty": 4,
                    "nm": f"{brand.brand_name} Brand", "sr": 1,
                    "ks": copy.deepcopy(main_info["ks"]),
                    "ao": 0, "shapes": [_apply_flip(
                        _make_text_group(shapes, offset, *_text_style(brand, size)),
                        _flip)],
                    "ip": main_info["ip"], "op": main_info["op"], "st": 0, "bm": 0,
                }
                # Normalise OUR brand layer to the accepted v3/v6
                # shape (the file-016 lesson): op clamped to the
                # document op and explicit o/r in ks — the old logo's
                # verbatim ks can carry op=9999 or no o/r at all.
                _doc_op = doc.get("op") or 180
                if (text_layer["op"] or 0) > _doc_op:
                    text_layer["op"] = _doc_op
                text_layer["ks"].setdefault("o", {"a": 0, "k": 100})
                text_layer["ks"].setdefault("r", {"a": 0, "k": 0})
                if parent is not None:
                    text_layer["parent"] = parent
                # Replace in place: same list position, same ind.
                layers[pos] = text_layer
                placement = {"scope": scope, "kind": "layer", "index": pos,
                             "ind": main_info["ind"], "parent": parent,
                             "size": round(size, 1),
                             "logo_on_w": round(main_info["on_w"], 1),
                             "logo_on_h": round(main_info["on_h"], 1),
                             "logo_name": main_info["name"],
                             "children_kept": main_info["children"]}
                warnings.append(
                    f"نام برند «{brand.brand_name}» دقیقاً جای لایه لوگوی قبلی "
                    f"({main_info['name']}) نشست: همان شاخص، همان پدر، همان ks و "
                    f"ip/op، اندازه {size:.0f} (فونت: {desc}).")
            else:
                # Group replacement inside the containing layer.
                scope = main_info["scope"]
                layers = _scope_layers(doc, scope)
                layer = None
                for l in layers:
                    if l.get("ind") == main_info["layer_ind"]:
                        layer = l
                        break
                if layer is None:
                    raise RuntimeError("لایه حاوی گروه لوگو پیدا نشد.")
                shapes_list = layer.get("shapes") or []
                gi = min(main_info["index"], len(shapes_list))
                matrix = main_info["matrix"]
                size, shapes, bb = _solve_text_size(
                    brand.brand_name, font_path, main_info["on_w"], main_info["on_h"],
                    matrix, main_info["group_bbox"])
                _gflip = _mat_det(matrix) < 0
                if main_info["group_bbox"] and bb:
                    offset = _center_offset(main_info["group_bbox"], bb, _gflip)
                else:
                    offset = (0, 0)
                text_group = _apply_flip(
                    _make_text_group(shapes, offset, *_text_style(brand, size)),
                    _gflip)
                text_group["nm"] = f"{brand.brand_name} Brand"
                # Insert at the old group's index (bulk watermark deletion
                # may have shifted indices; insert, not append, to keep
                # stacking relative to the remaining groups).
                shapes_list.insert(gi, text_group)
                layer["shapes"] = shapes_list
                placement = {"scope": scope, "kind": "group", "index": gi,
                             "layer": main_info["layer_name"],
                             "size": round(size, 1),
                             "logo_on_w": round(main_info["on_w"], 1),
                             "logo_on_h": round(main_info["on_h"], 1)}
                warnings.append(
                    f"نام برند «{brand.brand_name}» دقیقاً جای گروه لوگوی قبلی "
                    f"در لایه «{main_info['layer_name']}» نشست، اندازه {size:.0f} (فونت: {desc}).")
        except Exception as exc:
            warnings.append(f"جایگذاری در جای لوگو ناموفق بود: {str(exc)[:200]}")
            placement = None
    if brand.brand_name and placement is None and not marks_done:
        # Honest fallback: no logo element found at all — or the
        # in-place insertion failed after deletions (Mehdi files
        # 030/083/143/153: a 0x0-box main layer pruned away before
        # insertion). A visible bottom name beats no name at all.
        try:
            from .animation_engine import choose_font_for_tgs, tgs_add_text_layer
            style, font_path, desc = choose_font_for_tgs(doc, item_index)
            tgs_add_text_layer(doc, brand.brand_name, font_path, size=54.0)
            # The helper appends (bottom-most z-order); a scene that
            # covers the bottom centre hides the name entirely (Mehdi
            # pack files 012/034 in the strip run). Move the fallback
            # layer to the FRONT so the name stays visible.
            _layers = doc.get("layers") or []
            for _i, _l in enumerate(_layers):
                if str(_l.get("nm", "")) == f"{brand.brand_name} Brand":
                    _layers.insert(0, _layers.pop(_i))
                    break
            warnings.append(
                f"لوگوی قابل‌شناسایی پیدا نشد؛ نام برند «{brand.brand_name}» "
                f"به‌صورت جایگزین پایین صفحه (روی صحنه) اضافه شد (فونت: {desc}).")
        except Exception as exc:
            warnings.append(f"افزودن نام برند ناموفق بود: {str(exc)[:200]}")
    # Watermark groups: delete AFTER insertion, so the main element
    # survives until it is replaced (v5 order fix: bulk deletion before
    # insertion emptied CDC layers, prune then deleted them, and the
    # in-place replacement had nothing to replace — files 4,5,8,10,12,14,20).
    if pack_common_signatures:
        # tgs_remove_shapes_by_signatures deletes every matching group;
        # if the main element IS a watermark group, handle it separately
        # below instead of bulk-deleting it first.
        if main_group is None:
            pc = tgs_remove_shapes_by_signatures(doc, pack_common_signatures)
            if pc:
                warnings.append(f"شکل مشترک پک (تشخیص عمومی) حذف شد: {pc} گروه.")
        else:
            # Delete matching groups in every layer except the main
            # group's containing layer position — simplest safe path:
            # delete all matching groups, then re-create the main
            # group's bbox record from main_info (the group itself is
            # replaced by text below, so bulk deletion is fine as long
            # as we do not also delete the containing layer).
            pc = tgs_remove_shapes_by_signatures(doc, pack_common_signatures)
            if pc:
                warnings.append(f"شکل مشترک پک (تشخیص عمومی) حذف شد: {pc} گروه.")
            # After bulk deletion the main group's layer shapes changed;
            # the text group will be appended at the main group's old
            # index (clamped) in step 4 — stacking within the layer is
            # still preserved relative to the remaining groups.
    wm = tgs_remove_watermark_shapes(doc)
    if wm:
        warnings.append(f"شکل واترمارک (مسیر جایگزین) حذف شد: {wm} گروه.")
    # Grandparent reparenting: surviving layers whose parent was
    # deleted take the deleted layer's parent (from parent_map), so
    # glue is preserved instead of dropping to top level.
    for _sc, _ls in [(None, doc.get("layers") or [])] + [
            (a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]:
        _inds = {l.get("ind") for l in _ls}
        _pmap = parent_map.get(_sc, {})
        for _l in _ls:
            _par = _l.get("parent")
            _seen = set()
            while _par is not None and _par not in _inds and _par not in _seen:
                _seen.add(_par)
                _par = _pmap.get(_par)
            if _par is None:
                _l.pop("parent", None)
            else:
                _l["parent"] = _par
    # Final safety: prune empty shape layers again, but never the new
    # text layer (it has shapes by construction).
    tgs_prune_empty_shape_layers(doc)
    tgs_fix_dangling_parents(doc)
    out = dump_tgs(doc)
    return out, warnings, placement
