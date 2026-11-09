"""Importer guard (v10): structural repairs for the three ways
Telegram's TGS importer rejects files that rlottie renders fine.

Lessons proven by live test uploads during the 200-pack fix rounds
(2026-10-30, files 070/084/097/144/151):

1. Missing-tr: a shape GROUP whose items do not END with a 'tr'
   item is rejected ('wrong file type'). Our own slot surgery
   created 218 such groups; the original packs have none.
2. Paint-mixing: a group with NO direct shape item, MULTIPLE
   subgroups and BOTH a group-level 'fl' and 'st' (the exact
   signature [gr, gr, gr, fl, st, tr] of the text-era brand groups)
   is rejected. Calibration on 420 Telegram-accepted files (the
   pack originals + the live finals): groups with only ONE paint
   type, or a single subgroup, occur in accepted scene art and are
   NOT rejected — so the guard/validator signature requires both
   paints AND >= 2 subgroups AND no direct shape, which matches
   4/4 proven-bad files and 0/420 accepted files.
3. Vertex volume: the SAME 1109-vertex logo uploads fine in most
   files but is rejected in a few (context-dependent ceiling;
   file 097 passed only after Douglas-Peucker simplification to
   295 vertices). Groups over ~900 vertices are therefore a
   warning, and the guard simplifies only oversized groups whose
   shapes carry zero/identity tangents (where DP cannot distort
   curves); curved shapes are reported as skipped, never mangled.

fix_importer_risks() is render-safe by construction: it only
appends identity transforms, removes the proven-rejected group
paints (whose subgroups carry their own paints — the render delta
on file 070 was ~405 px of stray text-era paint), and simplifies
straight-segment paths. It is idempotent.
"""
import copy

# Identity transform, byte-identical in shape to the pack's own tr
# items and to scripts/fix4_logo.py's TR0.
TR0 = {"ty": "tr", "p": {"a": 0, "k": [0, 0]}, "a": {"a": 0, "k": [0, 0]},
       "s": {"a": 0, "k": [100, 100]}, "r": {"a": 0, "k": 0},
       "o": {"a": 0, "k": 100}, "sk": {"a": 0, "k": 0},
       "sa": {"a": 0, "k": 0}, "nm": "Transform"}

DIRECT_SHAPE_TYS = {"sh", "rc", "el", "sr", "pl"}

VERTEX_WARN_LIMIT = 900
VERTEX_SIMPLIFY_LIMIT = 1100
VERTEX_TARGET = 600


def iter_groups(items):
    """Yield every 'gr' group dict in a shapes/items tree (recursive)."""
    for item in items or []:
        if isinstance(item, dict):
            if item.get("ty") == "gr":
                yield item
                yield from iter_groups(item.get("it") or [])
            elif isinstance(item.get("it"), list):
                yield from iter_groups(item["it"])


def all_groups(doc: dict):
    """Every group in the document (top layers + asset layers)."""
    scopes = [doc.get("layers") or []] + [
        asset.get("layers") or [] for asset in doc.get("assets") or []]
    for layers in scopes:
        for layer in layers:
            yield from iter_groups(layer.get("shapes") or [])


def group_ends_with_tr(group: dict) -> bool:
    items = [it for it in group.get("it") or [] if isinstance(it, dict)]
    return bool(items) and items[-1].get("ty") == "tr"


def is_paint_mixing(group: dict) -> bool:
    """The proven rejection signature (see module docstring):
    no direct shape item, >= 2 subgroups, BOTH group-level 'fl'
    and 'st', and nothing else besides subgroups/paints/'tr'."""
    items = [it for it in group.get("it") or [] if isinstance(it, dict)]
    tys = [it.get("ty") for it in items]
    if any(t in DIRECT_SHAPE_TYS for t in tys):
        return False
    if tys.count("gr") < 2:
        return False
    if "fl" not in tys or "st" not in tys:
        return False
    return set(tys) <= {"gr", "fl", "st", "tr"}


def _shape_vertices(shape: dict):
    """(v, i, o, closed) of a static 'sh' item, or None when the
    shape is animated or malformed."""
    ks = shape.get("ks") or {}
    if ks.get("a"):
        return None
    k = ks.get("k")
    if not isinstance(k, dict):
        return None
    v = k.get("v")
    if not isinstance(v, list) or not v:
        return None
    return v, k.get("i") or [], k.get("o") or [], bool(k.get("c"))


def group_vertex_count(group: dict) -> int:
    """Total path vertices of all 'sh' descendants of the group."""
    total = 0
    stack = [group]
    while stack:
        node = stack.pop()
        for item in node.get("it") or []:
            if not isinstance(item, dict):
                continue
            if item.get("ty") == "sh":
                got = _shape_vertices(item)
                if got:
                    total += len(got[0])
            elif item.get("ty") == "gr":
                stack.append(item)
    return total


def _dp_keep(points, tol):
    """Douglas-Peucker: indices to KEEP for an open polyline."""
    n = len(points)
    if n <= 2:
        return set(range(n))
    keep = {0, n - 1}
    stack = [(0, n - 1)]
    while stack:
        a, b = stack.pop()
        if b <= a + 1:
            continue
        x1, y1 = points[a]
        x2, y2 = points[b]
        dx, dy = x2 - x1, y2 - y1
        denom = (dx * dx + dy * dy) ** 0.5 or 1.0
        worst, worst_i = -1.0, None
        for i in range(a + 1, b):
            px, py = points[i]
            dist = abs(dy * px - dx * py + x2 * y1 - y2 * x1) / denom
            if dist > worst:
                worst, worst_i = dist, i
        if worst_i is not None and worst > tol:
            keep.add(worst_i)
            stack.append((a, worst_i))
            stack.append((worst_i, b))
    return keep


def _simplify_closed(points, tol):
    """Kept indices for a CLOSED ring: split at the point farthest
    from points[0], DP both arcs, union the keeps."""
    n = len(points)
    if n <= 3:
        return set(range(n))
    x0, y0 = points[0]
    far, far_d = 0, -1.0
    for i, (px, py) in enumerate(points):
        d = (px - x0) ** 2 + (py - y0) ** 2
        if d > far_d:
            far, far_d = i, d
    if far == 0:
        return set(range(n))
    arc1 = list(range(0, far + 1))
    arc2 = list(range(far, n)) + [0]
    keep = set()
    for arc in (arc1, arc2):
        sub = [points[i] for i in arc]
        for j in _dp_keep(sub, tol):
            keep.add(arc[j])
    return keep if len(keep) >= 3 else set(range(n))


def _zero_tangents(shape: dict) -> bool:
    got = _shape_vertices(shape)
    if not got:
        return False
    _v, ins, outs, _c = got
    for arr in (ins, outs):
        for pt in arr:
            if isinstance(pt, (list, tuple)) and len(pt) >= 2:
                if float(pt[0]) != 0.0 or float(pt[1]) != 0.0:
                    return False
    return True


def _simplify_shape(shape: dict, tol: float) -> int:
    """DP-simplify one zero-tangent static shape in place.
    Returns the number of vertices removed (0 = untouched)."""
    got = _shape_vertices(shape)
    if not got:
        return 0
    v, _ins, _outs, closed = got
    pts = [(float(p[0]), float(p[1])) for p in v
           if isinstance(p, (list, tuple)) and len(p) >= 2]
    if len(pts) != len(v) or len(pts) <= 3:
        return 0
    keep = (_simplify_closed(pts, tol) if closed
            else _dp_keep(pts, tol))
    if len(keep) >= len(pts):
        return 0
    idx = sorted(keep)
    k = shape["ks"]["k"]
    k["v"] = [v[i] for i in idx]
    k["i"] = [[0, 0] for _ in idx]
    k["o"] = [[0, 0] for _ in idx]
    return len(v) - len(idx)


def fix_importer_risks(data: dict):
    """Repair the proven importer risks in a parsed TGS document.

    Returns (fixed_data, report) where report counts every action:
    {"tr_added", "paint_stripped", "groups_simplified",
     "vertices_removed", "simplify_skipped"}. The input is NOT
    mutated (a deep copy is fixed and returned). Idempotent: a
    second run on the output reports all zeros (shapes that had to
    be skipped for curved tangents report as skipped again but are
    never modified)."""
    doc = copy.deepcopy(data)
    report = {"tr_added": 0, "paint_stripped": 0,
              "groups_simplified": 0, "vertices_removed": 0,
              "simplify_skipped": 0}
    groups = list(all_groups(doc))
    for group in groups:
        if is_paint_mixing(group):
            before = len(group.get("it") or [])
            group["it"] = [it for it in group.get("it") or []
                           if not (isinstance(it, dict)
                                   and it.get("ty") in ("fl", "st"))]
            report["paint_stripped"] += before - len(group["it"])
        if not group_ends_with_tr(group):
            group.setdefault("it", []).append(copy.deepcopy(TR0))
            report["tr_added"] += 1
    for group in groups:
        if group_vertex_count(group) <= VERTEX_SIMPLIFY_LIMIT:
            continue
        shapes = []

        def _collect(node):
            for item in node.get("it") or []:
                if isinstance(item, dict):
                    if item.get("ty") == "sh":
                        shapes.append(item)
                    elif item.get("ty") == "gr":
                        _collect(item)

        _collect(group)
        removed_here = 0
        tol = 0.45
        while group_vertex_count(group) > VERTEX_TARGET and tol <= 8.0:
            progressed = False
            for shape in shapes:
                if _shape_vertices(shape) is None:
                    continue
                if not _zero_tangents(shape):
                    continue
                removed = _simplify_shape(shape, tol)
                if removed:
                    removed_here += removed
                    progressed = True
            if not progressed:
                break
            tol *= 1.6
        if removed_here:
            report["groups_simplified"] += 1
            report["vertices_removed"] += removed_here
        if group_vertex_count(group) > VERTEX_SIMPLIFY_LIMIT:
            curved = sum(1 for s in shapes
                         if _shape_vertices(s) is not None
                         and not _zero_tangents(s))
            report["simplify_skipped"] += curved
    return doc, report
