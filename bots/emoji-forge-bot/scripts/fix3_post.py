"""Fix-3 post-processor: cures applied to the CURRENT finals.

Pass 1 (residual sweep): old-brand groups that survived processing
are found by matching the ORIGINAL's render-verified white marks
(geometry) against colour-agnostic candidates in the final, then
render-verified again (no colour assumptions) and deleted — or
replaced with the brand text when the file has no visible name.

Pass 2 (brand QA + cure): the brand text is measured by render-diff
(with/without brand elements) across 8 frames. Invisible -> add the
standard bottom fallback layer. Too small -> enlarge the text group
in place (QA feedback loop). Clipped at the canvas edge -> shift it
inside via the inverse chain matrix. Unfixable -> fallback layer.

Outputs go to final_fix3/ only; finals are never touched here.
Usage: fix3_post.py <idx> [<idx> ...]
"""
import copy
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from PIL import ImageChops

from emoji_forge.animation_engine import (choose_font_for_tgs, dump_tgs,
                                          load_tgs, tgs_add_text_layer,
                                          tgs_remove_shapes_by_signatures)
from emoji_forge.brand import BrandProfile
from emoji_forge.v5 import (_find_mark_candidates_deep, _find_white_mark_groups,
                            _group_bbox, _mark_matrix, _mark_visible_box,
                            _mat_apply, _remove_group_at_path, _render_frames,
                            _replace_mark_with_text, _scope_layers,
                            _text_bbox_at, _verify_marks_render)

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix3")
os.makedirs(STAGE, exist_ok=True)
FRACS = (0, 0.15, 0.3, 0.45, 0.6, 0.75, 0.9)
NAME_PAT = ("store mehdi", "storemehdi", "shadifar", "shadi far")


# ---------------------------------------------------------------- brand find
def find_brand_elements(doc):
    """All brand elements: layers or shape groups named '* Brand'."""
    out = []

    def walk_group(scope, layer, group, chain, path, parent_items):
        nm = str(group.get("nm") or "")
        if "brand" in nm.lower():
            out.append({"kind": "group", "scope": scope, "layer": layer,
                        "group": group, "chain": chain, "path": path,
                        "parent_items": parent_items})
            return
        for j, sub in enumerate(group.get("it") or []):
            if isinstance(sub, dict) and sub.get("ty") == "gr":
                walk_group(scope, layer, sub, chain + [sub], path + (j,),
                           group.get("it") or [])

    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for layer in layers:
            if "brand" in str(layer.get("nm") or "").lower():
                out.append({"kind": "layer", "scope": scope, "layer": layer})
                continue
            if layer.get("ty") != 4:
                continue
            shapes = layer.get("shapes") or []
            for i, item in enumerate(shapes):
                if isinstance(item, dict) and item.get("ty") == "gr":
                    walk_group(scope, layer, item, [item], (i,), shapes)
    return out


def remove_brand_elements(doc):
    els = find_brand_elements(doc)
    n = 0
    # Groups first (deepest paths first per layer), then layers.
    groups = [e for e in els if e["kind"] == "group"]
    groups.sort(key=lambda e: (str(e["scope"]), e["layer"].get("ind"),
                               -len(e["path"]), e["path"]), reverse=True)
    seen = set()
    for e in groups:
        key = (e["scope"], e["layer"].get("ind"), e["path"])
        if key in seen:
            continue
        seen.add(key)
        _remove_group_at_path(doc, e["scope"], e["layer"].get("ind"), e["path"])
        n += 1
    for e in [x for x in els if x["kind"] == "layer"]:
        layers = _scope_layers(doc, e["scope"])
        layers[:] = [l for l in layers if l is not e["layer"]]
        n += 1
    return n


def _frames_for(doc):
    op = doc.get("op", 180)
    return sorted(set(int(op * f) for f in FRACS))


def brand_qa(doc):
    """Render-diff the brand elements. Returns stats dict."""
    els = find_brand_elements(doc)
    if not els:
        return {"present": False, "px": 0, "med_h": 0, "edge": 0.0, "frames": 0}
    frames = _frames_for(doc)
    base = _render_frames(dump_tgs(doc), frames)
    if base is None:
        return {"present": False, "px": 0, "med_h": 0, "edge": 0.0, "frames": 0}
    doc2 = copy.deepcopy(doc)
    remove_brand_elements(doc2)
    var = _render_frames(dump_tgs(doc2), frames)
    if var is None:
        return {"present": False, "px": 0, "med_h": 0, "edge": 0.0, "frames": 0}
    heights, total_px, edge_frames, seen_frames = [], 0, 0, 0
    for b, v in zip(base, var):
        d = ImageChops.difference(b, v).convert("L")
        mask = d.point(lambda x: 255 if x > 24 else 0)
        bbox = mask.getbbox()
        if bbox is None:
            continue
        seen_frames += 1
        px = sum(1 for x in d.crop(bbox).getdata() if x > 24)
        total_px += px
        heights.append(bbox[3] - bbox[1])
        if bbox[0] <= 3 or bbox[1] <= 3 or bbox[2] >= 509 or bbox[3] >= 509:
            edge_frames += 1
    if not heights:
        return {"present": False, "px": 0, "med_h": 0, "edge": 0.0, "frames": 0}
    heights.sort()
    return {"present": True, "px": total_px,
            "med_h": heights[len(heights) // 2],
            "edge": (edge_frames / seen_frames) if seen_frames else 0.0,
            "frames": seen_frames}


# ---------------------------------------------------------------- cures
def _text_group_of(el):
    """The inner text group (holds the letter shapes) of a brand element."""
    if el["kind"] == "layer":
        shapes = el["layer"].get("shapes") or []
        for s in shapes:
            if isinstance(s, dict) and s.get("ty") == "gr":
                return s
        return None
    # group kind: the brand group itself wraps [text_group, tr]
    for s in el["group"].get("it") or []:
        if isinstance(s, dict) and s.get("ty") == "gr":
            return s
    return el["group"]


def _tr_of(group):
    for s in group.get("it") or []:
        if isinstance(s, dict) and s.get("ty") == "tr":
            return s
    return None


def _static_pk(prop):
    """(value, is_static) for a lottie property dict."""
    if not isinstance(prop, dict):
        return None, False
    if prop.get("a"):
        return None, False
    k = prop.get("k")
    return k, True


def scale_brand_text(doc, factor):
    """Scale every brand text group about its own rendered centre."""
    els = find_brand_elements(doc)
    done = False
    for el in els:
        tg = _text_group_of(el)
        if tg is None:
            continue
        tr = _tr_of(tg)
        if tr is None:
            continue
        s, s_ok = _static_pk(tr.get("s"))
        p, p_ok = _static_pk(tr.get("p"))
        if not (s_ok and p_ok) or not isinstance(s, list) or not isinstance(p, list):
            continue
        shapes = [x for x in tg.get("it") or []
                  if isinstance(x, dict) and x.get("ty") in ("sh", "gr")]
        bb = _text_bbox_at(shapes)
        if not bb:
            continue
        cx, cy = (bb[0] + bb[2]) / 2, (bb[1] + bb[3]) / 2
        tr["s"] = {"a": 0, "k": [s[0] * factor, s[1] * factor]}
        tr["p"] = {"a": 0, "k": [p[0] + s[0] / 100.0 * cx * (1 - factor),
                                 p[1] + s[1] / 100.0 * cy * (1 - factor)]}
        done = True
    return done


def shift_brand_text(doc, dx, dy):
    """Shift brand text groups by a canvas-space delta, using each
    element's full chain matrix to convert into local space."""
    els = find_brand_elements(doc)
    done = False
    for el in els:
        if el["kind"] == "layer":
            ks = el["layer"].get("ks") or {}
            p, ok = _static_pk(ks.get("p"))
            if ok and isinstance(p, list):
                ks["p"] = {"a": 0, "k": [p[0] + dx, p[1] + dy] + list(p[2:])}
                done = True
            continue
        tg = _text_group_of(el)
        if tg is None:
            continue
        tr = _tr_of(tg)
        if tr is None:
            continue
        p, ok = _static_pk(tr.get("p"))
        if not ok or not isinstance(p, list):
            continue
        mark = {"scope": el["scope"], "layer": el["layer"],
                "group": el["group"], "chain": el["chain"]}
        try:
            m, _op = _mark_matrix(doc, mark, int(doc.get("op", 180) * 0.5))
        except Exception:
            continue
        a, b, c, d = m[0], m[1], m[2], m[3]
        det = a * d - b * c
        if abs(det) < 1e-9:
            continue
        lx = (d * dx - b * dy) / det
        ly = (-c * dx + a * dy) / det
        tr["p"] = {"a": 0, "k": [p[0] + lx, p[1] + ly]}
        done = True
    return done


def _layer_diff_bbox(doc, layer_obj):
    """Render-diff bbox of ONE layer across sampled frames."""
    frames = _frames_for(doc)
    base = _render_frames(dump_tgs(doc), frames)
    if base is None:
        return None
    doc2 = copy.deepcopy(doc)
    layers2 = _scope_layers(doc2, None)
    # find the twin of layer_obj by ind + name in the copy
    ind, nm = layer_obj.get("ind"), layer_obj.get("nm")
    layers2[:] = [l for l in layers2
                  if not (l.get("ind") == ind and l.get("nm") == nm)]
    var = _render_frames(dump_tgs(doc2), frames)
    if var is None:
        return None
    union = None
    for b, v in zip(base, var):
        d = ImageChops.difference(b, v).convert("L")
        bbox = d.point(lambda x: 255 if x > 24 else 0).getbbox()
        if bbox is None:
            continue
        if union is None:
            union = list(bbox)
        else:
            union[0] = min(union[0], bbox[0])
            union[1] = min(union[1], bbox[1])
            union[2] = max(union[2], bbox[2])
            union[3] = max(union[3], bbox[3])
    return tuple(union) if union else None


def add_fallback(doc, brand):
    _style, font_path, _desc = choose_font_for_tgs(doc, 0)
    tgs_add_text_layer(doc, brand.brand_name, font_path, size=54.0)
    layers = doc.get("layers") or []
    if not layers:
        return
    new_layer = layers[-1]
    # tgs_add_text_layer APPENDS (bottom of stack = hidden behind
    # full-canvas scenes). Move it to the TOP (index 0).
    layers[:] = [new_layer] + layers[:-1]
    # Settle: shift the layer until its rendered bbox sits inside
    # the canvas (some docs offset the whole scene via a precomp).
    for _ in range(2):
        bbox = _layer_diff_bbox(doc, new_layer)
        if bbox is None:
            return
        dx = dy = 0.0
        if bbox[3] > 504:
            dy = 504 - bbox[3]
        elif bbox[1] < 8:
            dy = 8 - bbox[1]
        cx = (bbox[0] + bbox[2]) / 2
        if abs(cx - 256) > 60:
            dx = 256 - cx
        if not dx and not dy:
            return
        ks = new_layer.get("ks") or {}
        p, ok = _static_pk(ks.get("p"))
        if not ok or not isinstance(p, list):
            return
        ks["p"] = {"a": 0, "k": [p[0] + dx, p[1] + dy] + list(p[2:])}


def cure_brand(doc, brand):
    """QA -> cure loop. Returns (qa_before, qa_after, actions)."""
    qa0 = brand_qa(doc)
    actions = []
    if qa0["present"] and qa0["med_h"] and qa0["med_h"] > 90:
        # The diff spans a body part, not a text line (occluded or
        # over-wide slot text) — automatic cures do more harm here.
        return qa0, qa0, ["manual-huge-brand"]
    if not qa0["present"] or qa0["px"] < 400:
        remove_brand_elements(doc)
        add_fallback(doc, brand)
        actions.append("fallback-added")
        return qa0, brand_qa(doc), actions
    qa = qa0
    for _ in range(2):
        if qa["med_h"] and qa["med_h"] < 30:
            f = min(2.2, 34.0 / max(qa["med_h"], 6))
            if scale_brand_text(doc, f):
                actions.append(f"scaled x{f:.2f}")
                qa = brand_qa(doc)
                continue
        break
    if qa["edge"] >= 0.5:
        # Push the text off the canvas edge: measure the diff bbox.
        els_doc = copy.deepcopy(doc)
        remove_brand_elements(els_doc)
        frames = _frames_for(doc)
        base = _render_frames(dump_tgs(doc), frames)
        var = _render_frames(dump_tgs(els_doc), frames)
        dx = dy = 0.0
        if base and var:
            for b, v in zip(base, var):
                d = ImageChops.difference(b, v).convert("L")
                bbox = d.point(lambda x: 255 if x > 24 else 0).getbbox()
                if bbox is None:
                    continue
                if bbox[0] <= 3:
                    dx = max(dx, 14 - bbox[0])
                if bbox[2] >= 509:
                    dx = min(dx, 498 - bbox[2])
                if bbox[1] <= 3:
                    dy = max(dy, 14 - bbox[1])
                if bbox[3] >= 509:
                    dy = min(dy, 498 - bbox[3])
        if (dx or dy) and shift_brand_text(doc, dx, dy):
            actions.append(f"shifted {dx:.0f},{dy:.0f}")
            qa = brand_qa(doc)
    if (not qa["present"] or qa["px"] < 400 or qa["med_h"] < 18
            or qa["edge"] >= 0.75):
        remove_brand_elements(doc)
        add_fallback(doc, brand)
        actions.append("fallback-replaced")
        qa = brand_qa(doc)
    return qa0, qa, actions


# ---------------------------------------------------------------- sweep
def _enum_candidates_any(doc, cap=80, depth=3):
    """Colour-agnostic version of the deep candidate search: any
    group with >= 2 shape items and a non-scene local bbox, Brand
    groups excluded."""
    out = []

    def _consider(scope, layer, group, chain, path, parent_items):
        nm = str(group.get("nm") or "").lower()
        if "brand" in nm:
            return
        subs = [s for s in (group.get("it") or [])
                if isinstance(s, dict) and s.get("ty") in ("sh", "gr")]
        if len(subs) < 1:
            return
        try:
            pts = _group_bbox([s for s in (group.get("it") or [])
                               if not (isinstance(s, dict) and s.get("ty") == "tr")],
                              (1, 0, 0, 1, 0, 0))
        except Exception:
            return
        if not pts:
            return
        xs = [p[0] for p in pts]
        ys = [p[1] for p in pts]
        w, h = max(xs) - min(xs), max(ys) - min(ys)
        if w <= 4 or h <= 4 or w * h > 0.6 * 512 * 512:
            return
        out.append({"scope": scope, "layer": layer, "group": group,
                    "core": group, "chain": chain, "path": path,
                    "parent_items": parent_items,
                    "local_bbox": (min(xs), min(ys), max(xs), max(ys)),
                    "_area": w * h})

    def _walk(scope, layer, group, chain, path, level):
        if level >= depth:
            return
        for j, sub in enumerate(group.get("it") or []):
            if isinstance(sub, dict) and sub.get("ty") == "gr":
                _consider(scope, layer, sub, chain + [sub],
                          path + (j,), group.get("it") or [])
                _walk(scope, layer, sub, chain + [sub], path + (j,), level + 1)

    scopes = [(None, doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    for scope, layers in scopes:
        for layer in layers:
            if layer.get("ty") != 4:
                continue
            shapes = layer.get("shapes") or []
            for i, item in enumerate(shapes):
                if isinstance(item, dict) and item.get("ty") == "gr":
                    _consider(scope, layer, item, [item], (i,), shapes)
                    _walk(scope, layer, item, [item], (i,), 1)
    out.sort(key=lambda m: m["_area"])
    return out[:cap]


def _center_of(doc, mark):
    try:
        _w, _h, m, _f, _o = _mark_visible_box(doc, mark)
        lb = mark["local_bbox"]
        cx, cy = _mat_apply(m, (lb[0] + lb[2]) / 2, (lb[1] + lb[3]) / 2)
        return cx, cy
    except Exception:
        return None


def _verify_any(doc, marks, min_changed=300):
    """Colour-agnostic render verification (changed px + compactness
    + the consistency rule), no brightness test."""
    if not marks:
        return []
    frames = sorted(set(int(doc.get("op", 180) * f)
                        for f in (0, 0.2, 0.4, 0.6, 0.8)))
    base = _render_frames(dump_tgs(doc), frames)
    if base is None:
        return []
    verified = []
    for mark in marks:
        try:
            doc2 = copy.deepcopy(doc)
            _remove_group_at_path(doc2, mark["scope"],
                                  mark["layer"].get("ind"),
                                  mark.get("path") or (0,))
            var = _render_frames(dump_tgs(doc2), frames)
            if var is None:
                continue
            best, rejected = 0, False
            for b_img, v_img in zip(base, var):
                d = ImageChops.difference(b_img, v_img).convert("L")
                bbox = d.point(lambda x: 255 if x > 24 else 0).getbbox()
                if bbox is None:
                    continue
                changed = sum(1 for x in d.crop(bbox).getdata() if x > 24)
                w, h = bbox[2] - bbox[0], bbox[3] - bbox[1]
                if changed >= min_changed and (changed > 40000 or w > 320 or h > 320):
                    rejected = True
                    break
                best = max(best, changed)
            if not rejected and best >= min_changed:
                mark["_changed"] = best
                verified.append(mark)
        except Exception:
            continue
    return verified


def _verify_marks_render2(doc, marks, min_changed=300,
                          frame_fracs=(0, 0.25, 0.5, 0.75)):
    """Like _verify_marks_render but the mark-pixel test is
    colour-general: a pixel counts as mark when it is bright on all
    channels (white/pale), dark on all channels (black logos), or
    strongly saturated (neon/script colours)."""
    if not marks:
        return []
    frames = sorted(set(int(doc.get("op", 180) * f) for f in frame_fracs))
    base = _render_frames(dump_tgs(doc), frames)
    if base is None:
        return []
    verified = []
    for mark in marks:
        try:
            doc2 = copy.deepcopy(doc)
            _remove_group_at_path(doc2, mark["scope"],
                                  mark["layer"].get("ind"),
                                  mark.get("path") or (0,))
            var = _render_frames(dump_tgs(doc2), frames)
            if var is None:
                continue
            best, rejected = None, False
            for b_img, v_img in zip(base, var):
                d = ImageChops.difference(b_img, v_img).convert("L")
                bbox = d.point(lambda x: 255 if x > 24 else 0).getbbox()
                if bbox is None:
                    continue
                region = d.crop(bbox)
                changed = sum(1 for x in region.getdata() if x > 24)
                w, h = bbox[2] - bbox[0], bbox[3] - bbox[1]
                if changed >= min_changed and (changed > 40000 or w > 320 or h > 320):
                    rejected = True
                    break
                marky = 0
                for r, g, b in b_img.crop(bbox).getdata():
                    if ((r >= 175 and g >= 175 and b >= 175)
                            or (r <= 110 and g <= 110 and b <= 110)
                            or (max(r, g, b) >= 200
                                and max(r, g, b) - min(r, g, b) >= 90)):
                        marky += 1
                if min_changed <= changed <= 40000 and marky >= 120 and w <= 320 and h <= 320:
                    if best is None or changed > best[0]:
                        best = (changed, w, h)
            if best is not None and not rejected:
                mark["measured"] = (best[1], best[2])
                verified.append(mark)
        except Exception:
            continue
    return verified


def _fill_profile(item):
    colors, grads, shapes = set(), 0, 0
    stack = [item]
    while stack:
        it = stack.pop()
        if not isinstance(it, dict):
            continue
        ty = it.get("ty")
        if ty == "fl":
            c = (it.get("c") or {}).get("k")
            vals = []
            if isinstance(c, list) and c and isinstance(c[0], (int, float)):
                vals = [c[:3]]
            elif isinstance(c, list):
                # keyframed colour: collect each keyframe's start value
                for kf in c:
                    if isinstance(kf, dict) and isinstance(kf.get("s"), list):
                        vals.append(kf["s"][:3])
            for v in vals:
                try:
                    colors.add(tuple(round(float(x), 2) for x in v))
                except Exception:
                    colors.add(("?",))
        elif ty == "gs":
            grads += 1
        elif ty == "sh":
            shapes += 1
        for sub in it.get("it") or []:
            stack.append(sub)
    return colors, grads, shapes


def _g1_ok(mark):
    """Structural gate for colour-general marks: flat artwork only
    (few solid fills, no gradients, several paths). Scene art in
    these packs is gradient-shaded or single-path; flat multi-path
    groups are text/logos. Keeps the dark/saturated channel from
    eating faces (file 028 disaster)."""
    colors, grads, shapes = _fill_profile(mark["group"])
    return len(colors) <= 4 and grads == 0 and shapes >= 3


def _orig_marks(orig_doc):
    """Returns (marks, fn2_distrusted). White marks are proven safe.
    Colour-general marks (dark/saturated) must pass the G1 gate, and
    are dropped entirely when implausibly many verify (breaker)."""
    white = _verify_marks_render(orig_doc, _find_white_mark_groups(orig_doc))
    for m in white:
        m["_kind"] = "white"
    cands = []
    for c in _enum_candidates_any(orig_doc, cap=400, depth=3):
        if not _g1_ok(c):
            continue
        try:
            vw, vh, _m, _f, _o = _mark_visible_box(orig_doc, c)
        except Exception:
            continue
        # Text/logo proportions only (drops tiny deco and scenes).
        if 30 <= vw <= 300 and 10 <= vh <= 300:
            cands.append(c)
    fn2 = _verify_marks_render2(orig_doc, cands)
    distrusted = len(fn2) > 6
    if distrusted:
        fn2 = []
    for m in fn2:
        m["_kind"] = "fn2"
    seen, out = set(), []
    for m in white + fn2:
        key = (m["scope"], m["layer"].get("ind"), m.get("path"))
        if key not in seen:
            seen.add(key)
            out.append(m)
    return out, distrusted


_LOGO_SIGS = None


def _logo_signatures():
    """Path-geometry signatures of the pack's standard M+phone logo
    (the 'SVGImport' artwork), harvested from the originals of files
    015 and 145 where it was verified by hand. Colour/scale free."""
    global _LOGO_SIGS
    if _LOGO_SIGS is not None:
        return _LOGO_SIGS
    from emoji_forge.animation_engine import shape_signature
    man = json.load(open(os.path.join(BASE, "manifest.json")))
    # Signatures of shapes inside groups NAMED 'SVGImport' only
    # (the logo import root), not the whole documents.
    sigs = set()
    for idx in (15, 145):
        doc = load_tgs(open(man["items"][idx - 1]["local_path"], "rb").read())
        stack = [doc]
        while stack:
            node = stack.pop()
            if isinstance(node, dict):
                if node.get("ty") == "gr" and node.get("nm") == "SVGImport":
                    sub = [node]
                    while sub:
                        n2 = sub.pop()
                        if isinstance(n2, dict):
                            if n2.get("ty") == "sh":
                                sig = shape_signature(
                                    (n2.get("ks") or {}).get("k") or {})
                                if sig is not None:
                                    sigs.add(sig)
                            sub.extend(n2.values())
                        elif isinstance(n2, list):
                            sub.extend(n2)
                    continue
                stack.extend(node.values())
            elif isinstance(node, list):
                stack.extend(node)
    _LOGO_SIGS = sigs
    return sigs


def sweep_residuals(doc, orig_doc, brand):
    """Delete/replace surviving old-brand groups. Returns notes.
    Iterates: deleting an invisible duplicate can expose its visible
    twin to the next round's verification."""
    notes = []
    # Logo-signature pre-step: the pack's standard M+phone artwork
    # has identical path geometry in every file; remove any top
    # group containing it, guarded by a scene-scale render check.
    try:
        sigs = _logo_signatures()
        if sigs:
            frames = _frames_for(doc)
            before = _render_frames(dump_tgs(doc), frames)
            trial = copy.deepcopy(doc)
            n_sig = tgs_remove_shapes_by_signatures(trial, sigs)
            if n_sig and before is not None:
                after = _render_frames(dump_tgs(trial), frames)
                ok = after is not None
                if ok:
                    for b_img, v_img in zip(before, after):
                        d = ImageChops.difference(b_img, v_img).convert("L")
                        bbox = d.point(lambda x: 255 if x > 24 else 0).getbbox()
                        if bbox is None:
                            continue
                        changed = sum(1 for x in d.crop(bbox).getdata() if x > 24)
                        if changed > 40000 or (bbox[2] - bbox[0]) > 340 or (bbox[3] - bbox[1]) > 340:
                            ok = False
                            break
                if ok:
                    tgs_remove_shapes_by_signatures(doc, sigs)
                    notes.append(f"logo-signature removed {n_sig} group(s)")
    except Exception:
        pass
    marks, distrusted = _orig_marks(orig_doc)
    if distrusted:
        notes.append("fn2-distrusted (white-only)")
    if not marks:
        return notes
    orig_centers = []
    for m in marks:
        c = _center_of(orig_doc, m)
        if c:
            orig_centers.append((c, m.get("_kind", "white")))
    if not orig_centers:
        return notes
    for _round in range(3):
        cands = _enum_candidates_any(doc, cap=400)
        matched = []
        for cand in cands:
            c = _center_of(doc, cand)
            if not c:
                continue
            cand["_center"] = c
            kinds = {kind for oc, kind in orig_centers
                     if abs(c[0] - oc[0]) <= 48 and abs(c[1] - oc[1]) <= 48}
            if not kinds:
                continue
            if "white" not in kinds and not _g1_ok(cand):
                continue
            matched.append(cand)
        if not matched:
            break
        verified = _verify_any(doc, matched)
        if not verified:
            break
        qa = brand_qa(doc)
        have_name = qa["present"] and qa["px"] >= 400
        tiny_name = have_name and qa["med_h"] and qa["med_h"] < 22
        verified.sort(key=lambda m: -(m.get("_changed") or 0))
        acted = []
        # Tiny-name rule: the biggest residual becomes the name slot.
        if tiny_name and verified and (verified[0].get("_changed") or 0) >= 2500:
            big = verified[0]
            old_els = find_brand_elements(doc)
            _style, font_path, _d = choose_font_for_tgs(doc, 0)
            m2 = dict(big)
            m2["measured"] = None
            res = _replace_mark_with_text(m2, brand, font_path, doc)
            if res:
                notes.append(f"replaced BIG residual with text at {big['_center']}")
                acted.append(big)
                have_name = True
                # drop the old tiny brand elements
                for el in old_els:
                    if el["kind"] == "group":
                        _remove_group_at_path(doc, el["scope"],
                                              el["layer"].get("ind"), el["path"])
                    else:
                        layers = _scope_layers(doc, el["scope"])
                        layers[:] = [l for l in layers if l is not el["layer"]]
                notes.append("removed old tiny brand")
        verified.sort(key=lambda m: -len(m.get("path") or ()))
        for m in verified:
            if any(a is m for a in acted):
                continue
            mp = m.get("path") or ()
            if any(a["scope"] == m["scope"] and a["layer"] is m["layer"]
                   and len(a["path"]) > len(mp) and a["path"][:len(mp)] == mp
                   for a in acted):
                continue
            if have_name:
                _remove_group_at_path(doc, m["scope"], m["layer"].get("ind"), mp)
                notes.append(f"deleted residual at {m['_center']}")
                acted.append(m)
            else:
                _style, font_path, _d = choose_font_for_tgs(doc, 0)
                m2 = dict(m)
                m2["measured"] = None
                res = _replace_mark_with_text(m2, brand, font_path, doc)
                if res:
                    notes.append(f"replaced residual with text at {m['_center']}")
                    acted.append(m)
                    have_name = True
                else:
                    _remove_group_at_path(doc, m["scope"], m["layer"].get("ind"), mp)
                    notes.append(f"deleted residual (replace failed) at {m['_center']}")
                    acted.append(m)
        if not acted:
            break
    return notes


# ---------------------------------------------------------------- main
def main():
    plan = json.load(open(os.path.join(BASE, "mati_plan_applied.json")))
    brand = BrandProfile.from_dict(plan.get("brand") or {})
    man = json.load(open(os.path.join(BASE, "manifest.json")))
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    for arg in sys.argv[1:]:
        idx = int(arg)
        raw_final = open(finals[idx], "rb").read()
        doc = load_tgs(raw_final)
        orig_doc = load_tgs(open(man["items"][idx - 1]["local_path"], "rb").read())
        notes = sweep_residuals(doc, orig_doc, brand)
        qa0, qa1, actions = cure_brand(doc, brand)
        out = os.path.join(STAGE, os.path.basename(finals[idx]))
        with open(out, "wb") as fh:
            fh.write(dump_tgs(doc))
        print(f"{idx}: sweep={notes} qa {qa0['med_h']}->{qa1['med_h']} "
              f"px {qa0['px']}->{qa1['px']} edge {qa0['edge']:.2f}->{qa1['edge']:.2f} "
              f"actions={actions} ({os.path.getsize(out)} B)")


if __name__ == "__main__":
    main()
