"""v8 engine: SCALE CORRECTION of v7 — precise contain-fit logos.

Why v8 exists (user feedback on v7, 2026-10-29 09:59): the EHSAN
logo at v7's "always big" rule (>=358px wide) is "way too big — the
scales must be measured precisely so it fits in the frame and is
logical in shape, appearance and subject". That instruction
SUPERSEDES the big rule. v8's rule, for every brand element:

  * The logo must FIT ENTIRELY inside the old logo's on-screen
    bounding box (the median visible box v5/v7 already compute:
    full transform chain, visible frames, median), preserving the
    logo's own aspect ratio — contain-fit:
        scale = min(box_w / logo_w, box_h / logo_h)
  * Centred on the box centre, same index/scope/parent and the same
    animation relationship as v5-v7 (the logo lives in the same
    layer/group slot, so it follows the old element's transform
    chain frame by frame).
  * Never overflows its box, never covers the subject beyond the
    old logo's own footprint. Small results (tiny old slots) are
    CORRECT now: precise and logical beats big.

Implementation strategy: v8 does NOT rebuild from originals. It
transforms the v7 finals — the only thing replaced is each "EHSAN
Logo" group, re-solved for contain-fit. Everything else in the v7
finals (audit repairs, restored coin/disc layers, theme recolour,
the banner disc fix, file 016's proven-empty comp_1) is preserved
byte-for-byte at the parsed-JSON level; `canonical_without_logo`
erases logo groups so a diff proves exactly that.

Centre handling mirrors v7: the main logo's centre is the old box
centre, shifted only by v7's minimal on-canvas clamp (needed for
the banner files 017-019, whose old box centre is off-canvas
because the tracked precomp instance scrolls; the shift is a
constant, so the motion trajectory is unchanged). Secondary slots
are centred exactly on their box centre, as in v7.

Stroke presence is preserved per element: if the v7 group being
replaced has no stroke (the banner disc slots, made stroke-less by
the v7 disc fix so they stay invisible on the white disc), the new
group is stroke-less too.
"""
import copy
import math

from .v4 import _group_bbox, _layer_shape_bbox, _mat_apply, _scope_layers
from .v5 import _candidate_layers, _full_matrix, _visible_median_box, _watermark_groups
from .v6 import _bake_group_offset
from .v7 import (
    LOGO_H, LOGO_W, _clamp_centre, _mat_inv, _sample_frames,
    classify_group, element_trajectory, make_logo_group, median_visible,
)

LOGO_NAME = "EHSAN Logo"


# ------------------------------------------------------------------
# Contain-fit solver
# ------------------------------------------------------------------

def contain_fit_scale(unit_w: float, unit_h: float,
                      box_w: float, box_h: float) -> float:
    """Scale that fits a unit_w x unit_h rect inside box_w x box_h
    preserving aspect: min(box_w/unit_w, box_h/unit_h)."""
    if unit_w <= 0 or unit_h <= 0 or box_w <= 0 or box_h <= 0:
        return 0.0
    return min(box_w / unit_w, box_h / unit_h)


def _logo_screen_size(matrix, k, logo_w=None, logo_h=None):
    """On-screen axis-aligned size of the logo rect (0,0)-(W*k, H*k)
    under `matrix`. Sizes are exactly linear in k. W/H default to
    the EHSAN asset; v9 user logos pass their own traced dims."""
    logo_w = LOGO_W if logo_w is None else float(logo_w)
    logo_h = LOGO_H if logo_h is None else float(logo_h)
    corners = [(0, 0), (logo_w * k, 0), (0, logo_h * k), (logo_w * k, logo_h * k)]
    pts = [_mat_apply(matrix, x, y) for x, y in corners]
    xs = [p[0] for p in pts]
    ys = [p[1] for p in pts]
    return max(xs) - min(xs), max(ys) - min(ys)


def solve_logo_group_fit(matrix, box_w, box_h, desired_centre, clamp,
                         logo_asset: dict | None = None):
    """Solve (logo_group, achieved) so the logo CONTAIN-FITS the
    box (box_w x box_h on screen) centred on desired_centre.

    Mirrors v7.solve_logo_group's conventions (same group builder,
    same centre/offset maths, same minimal on-canvas clamp when
    `clamp` is set) but the scale binds on BOTH dimensions and a
    final guard shrinks k if rounding pushed either dimension over
    the box: the result never overflows the box.
    logo_asset (v9): a traced user logo {"width","height","subpaths",
    "name"}; defaults to the owner's EHSAN asset.
    """
    _asset = logo_asset or {}
    logo_w = _asset.get("width")
    logo_h = _asset.get("height")
    subpaths = _asset.get("subpaths")
    logo_name = str(_asset.get("name") or LOGO_NAME)
    eff_w = LOGO_W if logo_w is None else float(logo_w)
    eff_h = LOGO_H if logo_h is None else float(logo_h)
    w0, h0 = _logo_screen_size(matrix, 1.0, eff_w, eff_h)
    k = contain_fit_scale(w0, h0, box_w, box_h)
    if k <= 0:
        return None, None
    achieved_w = achieved_h = 0.0
    for _ in range(4):
        achieved_w, achieved_h = _logo_screen_size(matrix, k, eff_w, eff_h)
        if achieved_w <= 0 or achieved_h <= 0:
            return None, None
        ratio = contain_fit_scale(achieved_w, achieved_h, box_w, box_h)
        if 0.995 <= ratio <= 1.005:
            break
        k *= ratio
    # Hard guarantee: never overflow the box.
    achieved_w, achieved_h = _logo_screen_size(matrix, k, eff_w, eff_h)
    over = max(achieved_w / box_w, achieved_h / box_h)
    if over > 1.0:
        k /= over
        achieved_w, achieved_h = _logo_screen_size(matrix, k, eff_w, eff_h)
    cx, cy = desired_centre
    shift = 0.0
    if clamp:
        ncx, ncy = _clamp_centre(cx, cy, achieved_w, achieved_h)
        shift = math.hypot(ncx - cx, ncy - cy)
        cx, cy = ncx, ncy
    inv = _mat_inv(matrix)
    if inv is not None:
        lx, ly = _mat_apply(inv, cx, cy)
    else:  # degenerate matrix: fall back to unscaled placement
        lx, ly = cx, cy
    offset = (lx - eff_w * k / 2, ly - eff_h * k / 2)
    return make_logo_group(k, offset, subpaths=subpaths, logo_name=logo_name), {
        "k": round(k, 4), "width": round(achieved_w, 1),
        "height": round(achieved_h, 1),
        "centre": [round(cx, 1), round(cy, 1)], "shift": round(shift, 1)}


# ------------------------------------------------------------------
# Element finding (old side / final side)
# ------------------------------------------------------------------

def find_old_main_element(doc, signatures, hide_names):
    """The old pack's main brand element: (scope, layer, local_bbox).

    Mirror of v5's step-1 selection (same logic scripts/fidelity_v7
    uses): candidate layers first (composite layers resolve to their
    LOGO group's bbox), else the largest watermark group."""
    cands = _candidate_layers(doc, set(hide_names))
    for cand in cands:
        try:
            w, h, _f = _visible_median_box(
                doc, cand["scope"], cand["layer"],
                _layer_shape_bbox(cand["layer"]))
            cand["area"] = w * h
        except Exception:
            cand["area"] = 0
    cands.sort(key=lambda c: (c["tier"], -c["area"]))
    if cands:
        c = cands[0]
        layer = c["layer"]
        groups = [g for g in layer.get("shapes") or []
                  if isinstance(g, dict) and g.get("ty") == "gr"]
        classes = [classify_group(doc, c["scope"], layer, g, signatures)
                   for g in groups]
        if "LOGO/BRAND" in classes and any(x != "LOGO/BRAND" for x in classes):
            g = groups[classes.index("LOGO/BRAND")]
            pts = _group_bbox([g], (1, 0, 0, 1, 0, 0))
            if pts:
                xs = [p[0] for p in pts]
                ys = [p[1] for p in pts]
                return c["scope"], layer, (min(xs), min(ys), max(xs), max(ys))
        return c["scope"], layer, _layer_shape_bbox(layer)
    wm = _watermark_groups(doc, signatures)
    if wm:
        def _ga(g):
            b = g["bbox"]
            return (b[2] - b[0]) * (b[3] - b[1]) if b else 0
        wm.sort(key=_ga, reverse=True)
        g = wm[0]
        layers = _scope_layers(doc, g["scope"])
        layer = next(l for l in layers
                     if l.get("nm") == g["layer"].get("nm")
                     and l.get("ind") == g["layer"].get("ind"))
        return g["scope"], layer, g["bbox"]
    return None, None, None


def _group_has_stroke(group) -> bool:
    return any(isinstance(it, dict) and it.get("ty") == "st"
               for it in (group or {}).get("it") or [])


def enumerate_logo_elements(doc, logo_name: str = LOGO_NAME):
    """Every brand logo element in a v7/v8 final doc (named
    `logo_name` — "EHSAN Logo" for the owner's asset, "Brand Logo"
    for a v9 user's traced logo).

    Returns [{kind: "layer"|"group", scope, layer, group_index,
    group, has_stroke}]. For kind="layer" the brand layer's single
    shape group is the logo group; for kind="group" the logo group
    sits inside a scene host layer."""
    out = []
    scopes = [(None, doc.get("layers") or [])] + [
        (a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for layer in layers:
            if str(layer.get("nm", "")) == logo_name:
                groups = [g for g in layer.get("shapes") or []
                          if isinstance(g, dict) and g.get("ty") == "gr"]
                if groups:
                    out.append({"kind": "layer", "scope": scope,
                                "layer": layer, "group_index": None,
                                "group": groups[0],
                                "has_stroke": _group_has_stroke(groups[0])})
                continue
            for gi, item in enumerate(layer.get("shapes") or []):
                if isinstance(item, dict) and item.get("ty") == "gr" \
                        and str(item.get("nm", "")) == logo_name:
                    out.append({"kind": "group", "scope": scope,
                                "layer": layer, "group_index": gi,
                                "group": item,
                                "has_stroke": _group_has_stroke(item)})
    return out


def _median_box(doc, scope, layer, local_bbox):
    """Median visible on-screen row (frame, cx, cy, w, h, rot, op)
    of an element — the box definition v5/v7 use."""
    traj = element_trajectory(doc, scope, layer, local_bbox,
                              _sample_frames(doc))
    return median_visible(traj), traj


def _strip_strokes(group) -> int:
    before = len(group.get("it") or [])
    group["it"] = [it for it in group.get("it") or []
                   if not (isinstance(it, dict) and it.get("ty") == "st")]
    return before - len(group.get("it"))


def _replace_group(el, new_group):
    """Put new_group where el's logo group lives, bake its offset
    into the path coordinates (the form v7 finals use), and strip
    its stroke if the replaced group had none. Returns baked count."""
    if el["kind"] == "layer":
        shapes = el["layer"].get("shapes") or []
        for gi, item in enumerate(shapes):
            if item is el["group"]:
                shapes[gi] = new_group
                break
        else:
            shapes[0] = new_group
    else:
        el["layer"]["shapes"][el["group_index"]] = new_group
    if not el["has_stroke"]:
        _strip_strokes(new_group)
    return _bake_group_offset({"shapes": [new_group]})


def _find_layer_by_ind(layers, ind):
    for l in layers or []:
        if l.get("ind") == ind:
            return l
    return None


# ------------------------------------------------------------------
# The transform
# ------------------------------------------------------------------

def rescale_doc_to_fit(orig_doc, final_doc, signatures, hide_names,
                       logo_asset: dict | None = None):
    """Re-solve every brand logo group in `final_doc` (a v7 final)
    for contain-fit inside its old element's median on-screen box,
    computed from `orig_doc`. Mutates final_doc; returns per-element
    report rows. The ONLY mutation is logo group replacement.
    logo_asset (v9): the traced user logo the v7 run placed; its
    name/dims drive element lookup and the fit solver."""
    logo_name = str((logo_asset or {}).get("name") or LOGO_NAME)
    rows = []
    m_scope, m_layer, m_bbox = find_old_main_element(
        orig_doc, signatures, hide_names)
    elements = enumerate_logo_elements(final_doc, logo_name)
    for el in elements:
        scope, layer = el["scope"], el["layer"]
        is_main = (m_layer is not None and scope == m_scope
                   and layer.get("ind") == m_layer.get("ind"))
        if is_main:
            old_layer, old_bbox, role = m_layer, m_bbox, "main"
        else:
            old_layer = _find_layer_by_ind(
                _scope_layers(orig_doc, scope), layer.get("ind"))
            old_bbox = _layer_shape_bbox(old_layer) if old_layer else None
            role = "secondary"
        row = {"role": role, "kind": el["kind"],
               "scope": scope or "top",
               "layer": str(layer.get("nm", "")),
               "old_layer": str(old_layer.get("nm", "")) if old_layer else None}
        med = None
        if old_layer is not None and old_bbox is not None:
            med, _traj = _median_box(orig_doc, scope, old_layer, old_bbox)
        if med is None:
            row["status"] = "SKIPPED: no visible median box for old element"
            rows.append(row)
            continue
        frame, bcx, bcy, bw, bh = med[0], med[1], med[2], med[3], med[4]
        matrix, _op = _full_matrix(final_doc, scope, layer, frame)
        group, achieved = solve_logo_group_fit(
            matrix, bw, bh, (bcx, bcy), clamp=(role == "main"),
            logo_asset=logo_asset)
        if group is None:
            row["status"] = "SKIPPED: degenerate transform"
            rows.append(row)
            continue
        baked = _replace_group(el, group)
        # Measure the fitted logo from the doc itself (post-bake):
        pts = _group_bbox([group], (1, 0, 0, 1, 0, 0))
        measured = None
        if pts:
            xs = [p[0] for p in pts]
            ys = [p[1] for p in pts]
            local = (min(xs), min(ys), max(xs), max(ys))
            traj = element_trajectory(final_doc, scope, layer, local,
                                      [frame])
            if traj:
                r = traj[0]
                measured = {"width": round(r[3], 1),
                            "height": round(r[4], 1),
                            "centre": [round(r[1], 1), round(r[2], 1)]}
        row.update({
            "status": "ok", "frame": frame,
            "box": [round(bw, 1), round(bh, 1)],
            "box_centre": [round(bcx, 1), round(bcy, 1)],
            "solved": achieved, "measured": measured, "baked": baked,
            "stroke_kept": el["has_stroke"],
        })
        rows.append(row)
    return rows


# ------------------------------------------------------------------
# Proof helper: canonical form with logo content erased
# ------------------------------------------------------------------

_LOGO_TOKEN = {"ty": "gr", "nm": LOGO_NAME, "it": [], "__logo__": True}


def canonical_without_logo(doc):
    """Deep copy of a doc with every EHSAN logo group's CONTENT
    erased (paths/fill/stroke/transform), so two builds can be
    diffed on everything EXCEPT the logo groups themselves."""
    doc = copy.deepcopy(doc)
    scopes = [doc.get("layers") or []] + [a.get("layers") or []
                                          for a in doc.get("assets") or []]
    for layers in scopes:
        for layer in layers:
            shapes = layer.get("shapes") or []
            if str(layer.get("nm", "")) == LOGO_NAME:
                layer["shapes"] = [copy.deepcopy(_LOGO_TOKEN)] if shapes else []
                continue
            layer["shapes"] = [
                copy.deepcopy(_LOGO_TOKEN)
                if isinstance(it, dict) and it.get("ty") == "gr"
                and str(it.get("nm", "")) == LOGO_NAME else it
                for it in shapes]
    return doc
