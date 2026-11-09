"""v7 engine: accuracy audit + repair pass, with the user's EHSAN logo.

Why v7 exists (user feedback on v6, 2026-10-29 09:08):
  * Some layers that should NOT have been deleted were deleted
    (file 011's spinning coin is the known case), and classification
    was not precise enough.
  * The brand element is no longer the "E30TZ" text: it is the user's
    own EHSAN wordmark logo (traced to vector by
    scripts/trace_logo.py into ehsan_logo_shapes.json), and per the
    user's explicit override its on-screen size must ALWAYS be big
    and readable — target width >= 70% of canvas (358px) unless the
    old logo was already wider — centred on the old logo's centre
    (shifted only as far as needed to keep it on canvas, documented
    per file, never silently shrunk).

Pipeline for one TGS:
  1. Run the v5 engine (exact in-place placement, verbatim ks,
     selective theme recolour, other-logo deletion).
  2. AUDIT-DRIVEN REPAIRS against the original:
     a. Composite main layers (logo group baked inside a scene/theme
        layer — file 011 TON coin, file 013 FREE): rebuild the layer
        from the original, delete ONLY its logo groups, keep every
        scene/theme group, its ks and its children.
     b. Restore falsely deleted scene/theme layers (file 011 coin
        rims "TON-Rebro-*", files 017-019 badge discs "Logo CIrcle*",
        and the "Logo in" carrier instances) — evidence-based
        predicate: deleted by v5, carries NO brand geometry
        (pack-common signature / hardcoded watermark), not an empty
        shape layer, and name/geometry verified as scene or theme by
        solo renders in the audit (scripts/audit_v7.py).
     c. Secondary brand slots: a pure-logo layer deleted from a
        still-referenced asset (file 015 comp_2, file 016 comp_1 —
        the alternating triangle phase — files 017-019 comp_3) is
        replaced in place inside its asset instead of leaving the
        slot blank.
  3. LOGO SWAP: every brand element (main + secondary) gets the
     traced EHSAN logo group instead of the text shapes. Main logo:
     big-size rule. Secondary slots: exact old on-screen size (they
     live inside small carriers/discs; the big rule applies to the
     main brand element). White fill + thin dark stroke, like the
     v5/v6 text readability device.
  4. v6-style normalisation (op clamp, explicit o/r, group offsets
     baked, redundant keyframes deduped, unused assets pruned),
     extended to the "EHSAN Logo" name.

Classification helpers (classify_layer / classify_group) live here
so the audit script and the engine share one rule set.
"""
import copy
import json
import math
from pathlib import Path

from .animation_engine import (
    _walk, dump_tgs, load_tgs, shape_signature,
    text_to_lottie_shapes, tgs_prune_unused_assets,
)
from .v4 import (
    BRAND_MARK_RE, _group_bbox, _layer_shape_bbox, _mat_apply,
    _mat_mul, _on_screen_box_for_element, _prop_at, _scope_layers,
    is_blue_dominant, is_subject_layer, tgs_selective_theme_recolor,
)
from .v5 import (
    _candidate_layers, _full_matrix, _is_text_logo_name,
    _make_text_group, _solve_text_size, _text_bbox_at,
    _visible_median_box, _watermark_groups, process_tgs_v5,
)
from .v6 import _bake_group_offset, _dedupe_keyframes

_LOGO_PATH = Path(__file__).with_name("ehsan_logo_shapes.json")
_LOGO = json.loads(_LOGO_PATH.read_text())
LOGO_W = float(_LOGO["width"])    # 512.0 in traced local space
LOGO_H = float(_LOGO["height"])   # ~222.74
LOGO_SUBPATHS = _LOGO["subpaths"]

CANVAS = 512.0
BIG_TARGET_W = 0.70 * CANVAS      # 358.4 px — user's "always big" rule


# ------------------------------------------------------------------
# Classification (shared with scripts/audit_v7.py)
# ------------------------------------------------------------------

def group_has_brand_geometry(group: dict, signatures: set) -> bool:
    """True if any path in the group matches a pack-common signature."""
    if not signatures:
        return False
    for node in _walk(group):
        if isinstance(node, dict) and node.get("ty") == "sh":
            if shape_signature((node.get("ks") or {}).get("k") or {}) in signatures:
                return True
    return False


def _group_fill_colours(group: dict):
    cols = []
    for node in _walk(group):
        if isinstance(node, dict) and node.get("ty") in ("fl", "gf"):
            k = (node.get("c") or {}).get("k")
            if isinstance(k, list) and len(k) >= 3 and all(
                    isinstance(x, (int, float)) for x in k[:3]):
                cols.append(tuple(k[:3]))
            gs = (node.get("g") or {}).get("p")
            if isinstance(gs, int):
                arr = ((node.get("g") or {}).get("k") or {}).get("k") or []
                for i in range(0, min(len(arr) - 3, gs * 4), 4):
                    seg = arr[i + 1:i + 4]
                    if len(seg) == 3 and all(isinstance(x, (int, float)) for x in seg):
                        cols.append(tuple(seg))
    return cols


# Evidence-based overrides from the v7 solo-render audit. Keys are
# (layer name, group name); values are the class. Everything else is
# decided by the generic rules in classify_group.
_GROUP_OVERRIDES = {
    ("TON", "Group 4"): "LOGO/BRAND",     # TON glyphs on the coin
    ("FREE", "Group 5"): "LOGO/BRAND",    # triangle mark on the square
    ("FREE", "Group 4"): "THEME",         # blue rounded-square body
    ("FREE", "Group 2"): "THEME",         # white frame around it
}


def classify_group(doc, scope, layer, group, signatures):
    """LOGO/BRAND | SCENE | THEME for one top-level shape item."""
    if not isinstance(group, dict) or group.get("ty") != "gr":
        return "SCENE"  # strokes/transforms ride along with their layer
    key = (str(layer.get("nm", "")), str(group.get("nm", "")))
    if key in _GROUP_OVERRIDES:
        return _GROUP_OVERRIDES[key]
    if group_has_brand_geometry(group, signatures):
        return "LOGO/BRAND"
    layer_name = str(layer.get("nm", ""))
    if "emblem" in layer_name.lower() or layer_name == "Logo":
        return "LOGO/BRAND"  # v5's own tier rules: emblem / Logo layers are marks
    for node in _walk(group):
        if isinstance(node, dict) and node.get("ty") == "sh" \
                and str(node.get("nm", "")).startswith("Logo"):
            return "LOGO/BRAND"  # paths literally named "Logo Path A/B"
    if _is_text_logo_name(layer_name) or layer_name in (
            "CDC Outlines", "$SVN Outlines"):
        return "LOGO/BRAND"  # glyph groups of a brand text/mark layer
    if BRAND_MARK_RE.search(layer_name) and layer.get("ty") == 4 \
            and _layer_shape_bbox(layer) is not None \
            and len(layer.get("shapes") or []) <= 3 \
            and not is_subject_layer(layer_name):
        # Small named brand layers are usually pure marks; composite
        # exceptions are covered by the overrides above.
        cols = _group_fill_colours(group)
        if cols:
            return "LOGO/BRAND"
    cols = _group_fill_colours(group)
    if cols and not is_subject_layer(layer_name):
        blue = sum(1 for r, g, b in cols if is_blue_dominant(r, g, b))
        if blue >= max(1, len(cols) // 2):
            return "THEME"
    return "SCENE"


def classify_layer(doc, scope, layer, signatures):
    """LOGO/BRAND | SCENE | THEME for one layer."""
    name = str(layer.get("nm", ""))
    if name.startswith("TON-Rebro"):
        return "SCENE"      # coin rim/edge during the spin (audit proof)
    if name in ("Logo CIrcle", "Logo CIrcle 2"):
        return "THEME"      # white badge disc under the logo (audit proof)
    if layer.get("ty") == 0:
        # Precomp instance: brand if it carries a brand asset.
        ref = layer.get("refId")
        for asset in doc.get("assets") or []:
            if asset.get("id") == ref:
                inner = [classify_layer(doc, ref, l, signatures)
                         for l in asset.get("layers") or []]
                if inner and all(c == "LOGO/BRAND" for c in inner):
                    return "LOGO/BRAND"
        return "SCENE"
    if layer.get("ty") != 4:
        return "SCENE"      # nulls / solids: structural
    if "emblem" in name.lower() or name == "Logo":
        return "LOGO/BRAND"
    groups = [g for g in layer.get("shapes") or []
              if isinstance(g, dict) and g.get("ty") == "gr"]
    classes = [classify_group(doc, scope, layer, g, signatures) for g in groups]
    if classes and all(c == "LOGO/BRAND" for c in classes):
        return "LOGO/BRAND"
    if _is_text_logo_name(name):
        return "LOGO/BRAND"
    if classes and all(c == "THEME" for c in classes):
        return "THEME"
    if "LOGO/BRAND" in classes:
        # Composite: brand baked into a scene/theme layer. Report the
        # dominant non-brand class (the audit lists groups separately).
        return "THEME" if classes.count("THEME") >= classes.count("SCENE") else "SCENE"
    if "THEME" in classes and "SCENE" not in classes:
        return "THEME"
    return "SCENE"


# ------------------------------------------------------------------
# Geometry helpers for the logo swap / fidelity measurement
# ------------------------------------------------------------------

def _mat_inv(m):
    a, b, c, d, e, f = m
    det = a * d - b * c
    if abs(det) < 1e-12:
        return None
    ia, ib, ic, id_ = d / det, -b / det, -c / det, a / det
    return (ia, ib, ic, id_, -(ia * e + ic * f), -(ib * e + id_ * f))


def element_trajectory(doc, scope, layer, local_bbox, frames):
    """Per-frame on-screen (cx, cy, w, h, rot_deg, opacity) of an
    element given by its LOCAL bbox inside `layer`, through the full
    transform chain (parents + precomp instances)."""
    out = []
    if not local_bbox:
        return out
    corners = [(local_bbox[0], local_bbox[1]), (local_bbox[2], local_bbox[1]),
               (local_bbox[0], local_bbox[3]), (local_bbox[2], local_bbox[3])]
    for fr in frames:
        m, op = _full_matrix(doc, scope, layer, fr)
        pts = [_mat_apply(m, x, y) for x, y in corners]
        xs = [p[0] for p in pts]
        ys = [p[1] for p in pts]
        rot = math.degrees(math.atan2(m[1], m[0]))
        out.append((fr, (min(xs) + max(xs)) / 2, (min(ys) + max(ys)) / 2,
                    max(xs) - min(xs), max(ys) - min(ys), rot, op))
    return out


def median_visible(traj):
    """Median visible row (w,h > 10 and opacity > 10) of a trajectory,
    v5's rule; falls back to the max-area row."""
    vis = [r for r in traj if r[3] > 10 and r[4] > 10 and r[6] > 10]
    if vis:
        ws = sorted(r[3] for r in vis)
        hs = sorted(r[4] for r in vis)
        mid = len(vis) // 2
        med_area = ws[mid] * hs[mid]
        return min(vis, key=lambda r: abs(r[3] * r[4] - med_area))
    if traj:
        return max(traj, key=lambda r: r[3] * r[4])
    return None


def _sample_frames(doc):
    op = doc.get("op", 180)
    return sorted(set(int(op * i / 20) for i in range(21)))


def _scaled_subpath(sp, k):
    return {"v": [[round(x * k, 2), round(y * k, 2)] for x, y in sp["v"]],
            "i": [[round(x * k, 2), round(y * k, 2)] for x, y in sp["i"]],
            "o": [[round(x * k, 2), round(y * k, 2)] for x, y in sp["o"]],
            "c": sp["c"]}


def make_logo_group(k: float, offset, subpaths=None, logo_name: str = "EHSAN Logo") -> dict:
    """A traced logo as a Lottie group: white fill + thin dark
    stroke (the v5/v6 readability device), paths pre-scaled by k in
    shape space, group transform at scale 100 with position offset.

    subpaths/logo_name default to the owner's traced EHSAN logo;
    v9 passes a USER's traced logo asset here instead (same format:
    {"width": 512-wide local space, "height", "subpaths"})."""
    items = []
    for sp in (LOGO_SUBPATHS if subpaths is None else subpaths):
        items.append({"ty": "sh", "nm": f"{logo_name} Path",
                      "ks": {"a": 0, "k": _scaled_subpath(sp, k)}})
    items.append({"ty": "fl", "c": {"a": 0, "k": [1, 1, 1, 1]},
                  "o": {"a": 0, "k": 100}, "r": 1, "bm": 0,
                  "nm": "Fill 1", "hd": False})
    items.append({"ty": "st", "c": {"a": 0, "k": [0, 0, 0, 1]},
                  "o": {"a": 0, "k": 100},
                  "w": {"a": 0, "k": round(max(1.0, 4.45 * k), 2)},
                  "lc": 1, "lj": 1, "bm": 0, "nm": "Stroke 1", "hd": False})
    items.append({"ty": "tr", "p": {"a": 0, "k": [round(offset[0], 2), round(offset[1], 2)]},
                  "a": {"a": 0, "k": [0, 0]}, "s": {"a": 0, "k": [100, 100]},
                  "r": {"a": 0, "k": 0}, "o": {"a": 0, "k": 100},
                  "sk": {"a": 0, "k": 0}, "sa": {"a": 0, "k": 0},
                  "nm": "Transform"})
    return {"ty": "gr", "it": items, "nm": logo_name, "bm": 0, "hd": False}


def _clamp_centre(cx, cy, w, h):
    """Shift a centre only as far as needed to keep the box on canvas
    (for w/h > canvas, centre the overflow symmetrically)."""
    def clamp(v, size):
        if size <= CANVAS:
            return min(max(v, size / 2), CANVAS - size / 2)
        return min(max(v, CANVAS - size / 2), size / 2)
    return clamp(cx, w), clamp(cy, h)


def solve_logo_group(matrix, target_w, desired_centre, clamp,
                     logo_w=None, logo_h=None, subpaths=None,
                     logo_name: str = "EHSAN Logo"):
    """Solve (logo_group, achieved) for a brand element.

    matrix: full on-screen matrix at the reference frame.
    target_w: desired on-screen width in px.
    desired_centre: on-screen centre to aim for. When clamp is True
    the centre is shifted the minimum amount that keeps the logo on
    canvas; the shift is reported in `achieved`.
    logo_w/logo_h/subpaths default to the EHSAN asset (v9 user logos
    pass their own traced asset instead).
    """
    logo_w = LOGO_W if logo_w is None else float(logo_w)
    logo_h = LOGO_H if logo_h is None else float(logo_h)
    inv = _mat_inv(matrix)
    # Iterate the shape-space scale k against the real matrix.
    k = target_w / logo_w
    achieved_w = achieved_h = 0.0
    for _ in range(4):
        corners = [(0, 0), (logo_w * k, 0), (0, logo_h * k), (logo_w * k, logo_h * k)]
        pts = [_mat_apply(matrix, x, y) for x, y in corners]
        xs = [p[0] for p in pts]
        ys = [p[1] for p in pts]
        achieved_w = max(xs) - min(xs)
        achieved_h = max(ys) - min(ys)
        if achieved_w <= 0:
            break
        ratio = target_w / achieved_w
        if 0.99 <= ratio <= 1.01:
            break
        k *= ratio
    cx, cy = desired_centre
    shift = 0.0
    if clamp:
        ncx, ncy = _clamp_centre(cx, cy, achieved_w, achieved_h)
        shift = math.hypot(ncx - cx, ncy - cy)
        cx, cy = ncx, ncy
    if inv is not None:
        lx, ly = _mat_apply(inv, cx, cy)
    else:  # degenerate matrix: fall back to unscaled placement
        lx, ly = cx, cy
    offset = (lx - logo_w * k / 2, ly - logo_h * k / 2)
    return make_logo_group(k, offset, subpaths=subpaths, logo_name=logo_name), {
        "k": round(k, 4), "width": round(achieved_w, 1),
        "height": round(achieved_h, 1), "centre": [round(cx, 1), round(cy, 1)],
        "shift": round(shift, 1)}


# ------------------------------------------------------------------
# Repairs
# ------------------------------------------------------------------

def _scopes(doc):
    return [(None, doc.get("layers") or [])] + [
        (a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]


def _find_layer(layers, ind):
    for i, l in enumerate(layers):
        if l.get("ind") == ind:
            return i, l
    return None, None


# Names verified by solo renders in the audit as scene/theme layers
# that v5 deleted only because a brand-name regex matched them.
_RESTORE_NAME_OK = ("TON-Rebro", "Logo CIrcle", "Logo in")


def _restorable(orig_layer, signatures):
    name = str(orig_layer.get("nm", ""))
    if orig_layer.get("ty") == 4 and not (orig_layer.get("shapes") or []):
        return False  # empty shape layer: no visual, validator forbids
    if orig_layer.get("ty") == 5:
        return False
    if not any(name.startswith(p) or name == p for p in _RESTORE_NAME_OK):
        return False
    for item in orig_layer.get("shapes") or []:
        if isinstance(item, dict) and item.get("ty") == "gr" \
                and group_has_brand_geometry(item, signatures):
            return False
    return True


def process_tgs_v7(data: bytes, brand, hide_names: set, item_index: int = 0,
                   pack_common_signatures: set | None = None,
                   main_logo_target_w: float | None = None,
                   logo_asset: dict | None = None):
    """Full v7 pipeline for one TGS. Returns (bytes, warnings, info).

    main_logo_target_w: when set, overrides the main brand element's
    on-screen target width (instead of max(BIG_TARGET_W, old width)).
    Used for the banner files 017-019, whose old text was 877px wide
    with a ~6.7 aspect; the logo (aspect ~2.3) at that width towered
    381px tall over the banner. There the target is BIG_TARGET_W
    (358px, ~156px tall, close to the old text's 130px height)."""
    signatures = pack_common_signatures or set()
    # v9: the logo asset defaults to the owner's traced EHSAN logo;
    # a user logo traced by logo_vector.trace_logo_image has the same
    # shape and can be dropped in (its groups/layers take its name).
    _asset = logo_asset or {}
    _logo_subpaths = _asset.get("subpaths")
    _logo_w = _asset.get("width")
    _logo_h = _asset.get("height")
    _logo_name = str(_asset.get("name") or "EHSAN Logo")
    orig = load_tgs(data)
    out5, warnings, placement = process_tgs_v5(
        data, brand, hide_names, item_index, signatures)
    doc = load_tgs(out5)
    info = {"placement": placement, "repairs": [], "logo": None,
            "secondary": []}
    brand_name = brand.brand_name

    # --- Locate the main brand element in the v5 doc.
    brand_loc = None
    if placement and brand_name:
        if placement["kind"] == "layer":
            brand_loc = {"type": "layer", "scope": placement["scope"],
                         "ind": placement["ind"]}
        elif placement["kind"] == "group":
            layers = _scope_layers(doc, placement["scope"])
            for l in layers:
                if l.get("nm") == placement["layer"]:
                    brand_loc = {"type": "group", "scope": placement["scope"],
                                 "layer_ind": l.get("ind")}
                    break

    # --- Repair A: composite main layer (logo group inside a scene
    # layer that v5 replaced wholesale). Detect on the ORIGINAL main
    # layer: mixed group classes with at least one LOGO group.
    main_orig = None
    if placement and placement["kind"] == "layer":
        o_layers = _scope_layers(orig, placement["scope"])
        _i, main_orig = _find_layer(o_layers, placement["ind"])
    if main_orig is not None:
        groups = [g for g in main_orig.get("shapes") or []
                  if isinstance(g, dict) and g.get("ty") == "gr"]
        classes = [classify_group(orig, placement["scope"], main_orig, g,
                                  signatures) for g in groups]
        if "LOGO/BRAND" in classes and any(c != "LOGO/BRAND" for c in classes):
            scope = placement["scope"]
            layers = _scope_layers(doc, scope)
            pos, _txt = _find_layer(layers, placement["ind"])
            if pos is not None:
                repaired = copy.deepcopy(main_orig)
                kept = []
                first_logo_idx = None
                for gi, item in enumerate(repaired.get("shapes") or []):
                    if isinstance(item, dict) and item.get("ty") == "gr" and \
                            classify_group(orig, scope, main_orig, item,
                                           signatures) == "LOGO/BRAND":
                        if first_logo_idx is None:
                            first_logo_idx = len(kept)
                        continue
                    kept.append(item)
                # Placeholder brand group at the first logo group's
                # index (the logo swap replaces it with the EHSAN
                # logo). Sized like v5 would have, as a sane fallback.
                logo_group_orig = groups[classes.index("LOGO/BRAND")]
                gb_pts = _group_bbox([logo_group_orig], (1, 0, 0, 1, 0, 0))
                gb = None
                if gb_pts:
                    xs = [p[0] for p in gb_pts]
                    ys = [p[1] for p in gb_pts]
                    gb = (min(xs), min(ys), max(xs), max(ys))
                matrix, _op = _full_matrix(orig, scope, main_orig,
                                           int(orig.get("op", 180) * 0.5))
                placeholder = None
                if gb and brand_name:
                    try:
                        from .animation_engine import choose_font_for_tgs
                        _st, font_path, _d = choose_font_for_tgs(orig, item_index)
                        traj = element_trajectory(orig, scope, main_orig, gb,
                                                  _sample_frames(orig))
                        med = median_visible(traj)
                        tw, th = (med[3], med[4]) if med else (gb[2] - gb[0], gb[3] - gb[1])
                        if med:
                            matrix, _op = _full_matrix(orig, scope, main_orig, med[0])
                        size, shapes, bb = _solve_text_size(
                            brand_name, font_path, tw, th, matrix, gb)
                        if bb:
                            off = ((gb[0] + gb[2]) / 2 - (bb[0] + bb[2]) / 2,
                                   (gb[1] + gb[3]) / 2 - (bb[1] + bb[3]) / 2)
                        else:
                            off = (0, 0)
                        placeholder = _make_text_group(shapes, off)
                        placeholder["nm"] = f"{brand_name} Brand"
                    except Exception:
                        placeholder = None
                if placeholder is not None:
                    kept.insert(first_logo_idx if first_logo_idx is not None
                                else len(kept), placeholder)
                repaired["shapes"] = kept
                layers[pos] = repaired
                brand_loc = {"type": "group", "scope": scope,
                             "layer_ind": placement["ind"]}
                info["repairs"].append(
                    f"composite layer «{main_orig.get('nm')}» rebuilt: only "
                    f"its logo groups removed, scene/theme groups kept")
                warnings.append(
                    f"v7: لایه مرکب «{main_orig.get('nm')}» از اصل بازسازی شد؛ "
                    "فقط گروه لوگو حذف شد و بقیه لایه ماند.")

    # --- Repair B: restore falsely deleted scene/theme layers.
    restored = []
    for scope, o_layers in _scopes(orig):
        d_layers = _scope_layers(doc, scope)
        d_inds = {l.get("ind") for l in d_layers}
        for o_pos, o_layer in enumerate(o_layers):
            if o_layer.get("ind") in d_inds:
                continue
            if not _restorable(o_layer, signatures):
                continue
            # Insert at the original relative position: before the
            # first surviving layer that originally followed it.
            insert_at = len(d_layers)
            o_inds = [l.get("ind") for l in o_layers]
            for later in o_inds[o_pos + 1:]:
                j, _l = _find_layer(d_layers, later)
                if j is not None:
                    insert_at = j
                    break
            d_layers.insert(insert_at, copy.deepcopy(o_layer))
            restored.append(f"{scope or 'top'}:{o_layer.get('nm')}")
    if restored:
        info["repairs"].append(f"restored layers: {', '.join(restored)}")
        warnings.append(
            f"v7: لایه‌های صحنه/تم که اشتباه حذف شده بودند برگشتند: "
            f"{', '.join(restored)}.")

    # --- Repair C: secondary brand slots in still-referenced assets.
    # A pure-logo layer deleted from an asset that surviving instances
    # still reference (alternating phases / badge interiors) is
    # replaced in place inside the asset instead of left blank.
    referenced = set()
    for _sc, ls in _scopes(doc):
        for l in ls:
            if l.get("refId"):
                referenced.add(l.get("refId"))
    for asset in orig.get("assets") or []:
        aid = asset.get("id")
        if aid not in referenced:
            continue
        o_layers = asset.get("layers") or []
        d_asset = next((a for a in doc.get("assets") or []
                        if a.get("id") == aid), None)
        d_layers = d_asset.get("layers") if d_asset is not None else []
        d_inds = {l.get("ind") for l in (d_layers or [])}
        for o_layer in o_layers:
            if o_layer.get("ind") in d_inds or o_layer.get("ty") != 4:
                continue
            if classify_layer(orig, aid, o_layer, signatures) != "LOGO/BRAND":
                continue
            # Build the in-place brand layer (verbatim ks/ip/op).
            if d_asset is None:
                d_asset = {"id": aid, "layers": []}
                doc.setdefault("assets", []).append(d_asset)
                d_layers = d_asset["layers"]
            brand_layer = {
                "ddd": 0, "ind": o_layer.get("ind"), "ty": 4,
                "nm": f"{brand_name} Brand", "sr": 1,
                "ks": copy.deepcopy(o_layer.get("ks") or {}),
                "ao": 0, "shapes": [],
                "ip": o_layer.get("ip", 0), "op": o_layer.get("op"),
                "st": 0, "bm": 0,
            }
            if o_layer.get("parent") is not None:
                brand_layer["parent"] = o_layer["parent"]
            # Placeholder text group, v5-style exact sizing (fallback;
            # the logo swap re-solves with the logo shapes).
            lb = _layer_shape_bbox(o_layer)
            try:
                from .animation_engine import choose_font_for_tgs
                _st, font_path, _d = choose_font_for_tgs(orig, item_index)
                tw, th, _f = _visible_median_box(orig, aid, o_layer, lb)
                matrix, _op = _full_matrix(orig, aid, o_layer, _f)
                size, shapes, bb = _solve_text_size(
                    brand_name, font_path, tw, th, matrix, lb)
                if lb and bb:
                    if _is_text_logo_name(str(o_layer.get("nm", ""))):
                        off = (lb[0] - bb[0], (lb[1] + lb[3]) / 2 - (bb[1] + bb[3]) / 2)
                    else:
                        off = ((lb[0] + lb[2]) / 2 - (bb[0] + bb[2]) / 2,
                               (lb[1] + lb[3]) / 2 - (bb[1] + bb[3]) / 2)
                else:
                    off = (0, 0)
                grp = _make_text_group(shapes, off)
                grp["nm"] = f"{brand_name} Brand"
                brand_layer["shapes"] = [grp]
            except Exception:
                brand_layer["shapes"] = []
            # Insert at original relative position inside the asset.
            o_inds = [l.get("ind") for l in o_layers]
            o_pos = o_inds.index(o_layer.get("ind"))
            insert_at = len(d_layers)
            for later in o_inds[o_pos + 1:]:
                j, _l = _find_layer(d_layers, later)
                if j is not None:
                    insert_at = j
                    break
            d_layers.insert(insert_at, brand_layer)
            info["secondary"].append({"scope": aid, "ind": o_layer.get("ind"),
                                      "orig_name": o_layer.get("nm")})
            warnings.append(
                f"v7: اسلات برند دوم «{o_layer.get('nm')}» در {aid} به‌جای "
                "حذف، در جای خودش جایگزین شد.")

    # Recolour restored/rebuilt original layers (idempotent on the
    # already-recoloured rest of the doc: red is not blue-dominant).
    if restored or any("composite" in r for r in info["repairs"]):
        tgs_selective_theme_recolor(doc)

    # --- Logo swap: text -> traced EHSAN logo, big-size rule (main).
    def old_element(scope, layer, local_bbox):
        traj = element_trajectory(orig, scope, layer, local_bbox,
                                  _sample_frames(orig))
        return median_visible(traj), traj

    def swap_layer_brand(scope, ind, old_layer, old_local_bbox, big):
        layers = _scope_layers(doc, scope)
        _pos, layer = _find_layer(layers, ind)
        if layer is None or old_layer is None:
            return None
        med, _traj = old_element(scope, old_layer, old_local_bbox)
        if med is None:
            return None
        frame = med[0]
        matrix, _op = _full_matrix(doc, scope, layer, frame)
        if big and main_logo_target_w is not None:
            target_w = main_logo_target_w
        else:
            target_w = max(BIG_TARGET_W, med[3]) if big else med[3]
        group, achieved = solve_logo_group(
            matrix, target_w, (med[1], med[2]), clamp=big,
            logo_w=_logo_w, logo_h=_logo_h, subpaths=_logo_subpaths,
            logo_name=_logo_name)
        layer["shapes"] = [group]
        layer["nm"] = _logo_name
        achieved["old_centre"] = [round(med[1], 1), round(med[2], 1)]
        achieved["old_size"] = [round(med[3], 1), round(med[4], 1)]
        achieved["frame"] = frame
        return achieved

    def swap_group_brand(scope, layer_ind, old_layer, old_local_bbox, big):
        layers = _scope_layers(doc, scope)
        _pos, layer = _find_layer(layers, layer_ind)
        if layer is None or old_layer is None:
            return None
        med, _traj = old_element(scope, old_layer, old_local_bbox)
        if med is None:
            return None
        frame = med[0]
        matrix, _op = _full_matrix(doc, scope, layer, frame)
        if big and main_logo_target_w is not None:
            target_w = main_logo_target_w
        else:
            target_w = max(BIG_TARGET_W, med[3]) if big else med[3]
        group, achieved = solve_logo_group(
            matrix, target_w, (med[1], med[2]), clamp=big,
            logo_w=_logo_w, logo_h=_logo_h, subpaths=_logo_subpaths,
            logo_name=_logo_name)
        shapes = layer.get("shapes") or []
        for gi, item in enumerate(shapes):
            if isinstance(item, dict) and item.get("ty") == "gr" and \
                    str(item.get("nm", "")) == f"{brand_name} Brand":
                shapes[gi] = group
                break
        else:
            return None
        achieved["old_centre"] = [round(med[1], 1), round(med[2], 1)]
        achieved["old_size"] = [round(med[3], 1), round(med[4], 1)]
        achieved["frame"] = frame
        return achieved

    if brand_loc is not None and brand_name:
        if brand_loc["type"] == "layer":
            o_layers = _scope_layers(orig, brand_loc["scope"])
            _i, o_layer = _find_layer(o_layers, brand_loc["ind"])
            res = swap_layer_brand(brand_loc["scope"], brand_loc["ind"],
                                   o_layer, _layer_shape_bbox(o_layer),
                                   big=True)
        else:
            o_layers = _scope_layers(orig, brand_loc["scope"])
            _i, o_layer = _find_layer(o_layers, brand_loc["layer_ind"])
            # Old local bbox: composite -> the deleted logo group's
            # bbox; plain group placement -> the watermark group's bbox.
            if placement and placement.get("kind") == "group" and \
                    "group_bbox" not in (placement or {}):
                pass
            gb = None
            if any("composite" in r for r in info["repairs"]) and o_layer is not None:
                for g in o_layer.get("shapes") or []:
                    if isinstance(g, dict) and g.get("ty") == "gr" and \
                            classify_group(orig, brand_loc["scope"], o_layer,
                                           g, signatures) == "LOGO/BRAND":
                        pts = _group_bbox([g], (1, 0, 0, 1, 0, 0))
                        if pts:
                            xs = [p[0] for p in pts]
                            ys = [p[1] for p in pts]
                            gb = (min(xs), min(ys), max(xs), max(ys))
                        break
            if gb is None and placement is not None:
                # v5 group placement: recompute the watermark group bbox
                # from the original via its recorded layer name.
                wm = _watermark_groups(orig, signatures)
                for w in wm:
                    if w["layer"].get("nm") == placement.get("layer"):
                        gb = w["bbox"]
                        break
            res = swap_group_brand(brand_loc["scope"], brand_loc["layer_ind"],
                                   o_layer, gb, big=True)
        info["logo"] = res
        if res:
            warnings.append(
                f"v7: لوگوی {_logo_name} جای برند نشست — پهنای روی صفحه "
                f"{res['width']:.0f}px (هدف ≥{BIG_TARGET_W:.0f})، مرکز "
                f"{res['centre']}، جابه‌جایی برای ماندن در کادر "
                f"{res['shift']:.0f}px.")

    # Secondary slots: exact old size (documented rule).
    for sec in info["secondary"]:
        o_layers = _scope_layers(orig, sec["scope"])
        _i, o_layer = _find_layer(o_layers, sec["ind"])
        res = swap_layer_brand(sec["scope"], sec["ind"], o_layer,
                               _layer_shape_bbox(o_layer), big=False)
        sec["logo"] = res

    # --- v6-style normalisation, extended to the brand logo name.
    doc_op = doc.get("op") or 0
    norm = {"clamped": 0, "o_added": 0, "r_added": 0, "baked": 0}
    for _sc, layers in _scopes(doc):
        for layer in layers:
            nm = str(layer.get("nm", ""))
            if "E30TZ" not in nm and "EHSAN" not in nm and _logo_name not in nm:
                continue
            if doc_op and (layer.get("op") or 0) > doc_op:
                layer["op"] = doc_op
                norm["clamped"] += 1
            ks = layer.setdefault("ks", {})
            if "o" not in ks:
                ks["o"] = {"a": 0, "k": 100}
                norm["o_added"] += 1
            if "r" not in ks:
                ks["r"] = {"a": 0, "k": 0}
                norm["r_added"] += 1
            norm["baked"] += _bake_group_offset(layer)
    # Brand groups inside scene layers (group placements, composite):
    # bake their offsets too, so path coordinates carry the placement.
    for _sc, layers in _scopes(doc):
        for layer in layers:
            for gi, item in enumerate(layer.get("shapes") or []):
                if isinstance(item, dict) and item.get("ty") == "gr" and \
                        str(item.get("nm", "")) == _logo_name:
                    fake = {"shapes": [item]}
                    norm["baked"] += _bake_group_offset(fake)
    deduped = _dedupe_keyframes(doc)
    pruned = tgs_prune_unused_assets(doc)
    info["normalise"] = {**norm, "deduped": deduped, "pruned_assets": pruned}
    return dump_tgs(doc), warnings, info
