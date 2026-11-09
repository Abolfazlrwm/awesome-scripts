"""Animation engines — one route per format, never a single fixed engine.

TGS (Lottie JSON, gzipped):
  - timing (fr/ip/op) is never changed; layers are never reordered;
  - cleaning = hide layers whose name looks like a logo/text layer;
  - branding = hue-shift solid fill colours (c.k arrays), skin-like colours
    skipped by the same heuristic as raster;
  - adding a raster logo into a vector TGS is NOT done silently: it is
    reported as unsupported in v0.1 (a raster fallback would lose animation).

WEBM (VP9):
  - frames are extracted with FFmpeg, every frame goes through the SAME
    static pipeline with the SAME parameters (no per-frame randomness),
    then re-encoded VP9 + alpha, no audio, same fps, same frame count
    except trimming over 3 seconds;
  - bitrate is lowered stepwise until the file fits 256 KiB, or the
    failure is reported honestly.
"""
import colorsys
import gzip
import json
import os
import re
import shutil
import subprocess
import tempfile

from PIL import Image

from .cleaning import is_skin_like
from .formats import probe_webm

LOGO_NAME_RE = re.compile(r"logo|text|name|brand|watermark|title", re.IGNORECASE)


def load_tgs(data: bytes) -> dict:
    raw = gzip.decompress(data) if data.startswith(b"\x1f\x8b") else data
    doc = json.loads(raw.decode("utf-8"))
    if not isinstance(doc, dict) or "layers" not in doc:
        raise ValueError("فایل TGS معتبر نیست (لایه‌ها پیدا نشدند).")
    return doc


def dump_tgs(doc: dict) -> bytes:
    raw = json.dumps(doc, separators=(",", ":"), ensure_ascii=False).encode("utf-8")
    return gzip.compress(raw, compresslevel=9)


def tgs_logo_candidates(doc: dict) -> list:
    """Layer names that look like logo/text layers — candidates for cleaning.

    Includes layers inside precomp assets, not just top-level layers:
    real packs often put the logo in an asset.
    """
    out = []
    seen = set()
    for layer in list(doc.get("layers") or []):
        name = str(layer.get("nm", ""))
        if LOGO_NAME_RE.search(name) and name not in seen:
            seen.add(name)
            out.append(name)
    for asset in doc.get("assets") or []:
        for layer in asset.get("layers") or []:
            name = str(layer.get("nm", ""))
            if LOGO_NAME_RE.search(name) and name not in seen:
                seen.add(name)
                out.append(name)
    return out


def tgs_hide_text_layers(doc: dict) -> int:
    """Hide every text layer (ty=5), including inside assets. Returns count."""
    hidden = 0
    for layer in list(doc.get("layers") or []):
        if layer.get("ty") == 5:
            layer["hd"] = 1
            hidden += 1
    for asset in doc.get("assets") or []:
        for layer in asset.get("layers") or []:
            if layer.get("ty") == 5:
                layer["hd"] = 1
                hidden += 1
    return hidden


def tgs_hide_layers(doc: dict, names: set) -> int:
    """Hide (hd=1) matching layers, including inside assets. Returns count."""
    hidden = 0
    for layer in list(doc.get("layers") or []):
        if str(layer.get("nm", "")) in names:
            layer["hd"] = 1
            hidden += 1
    for asset in doc.get("assets") or []:
        for layer in asset.get("layers") or []:
            if str(layer.get("nm", "")) in names:
                layer["hd"] = 1
                hidden += 1
    return hidden


def tgs_remove_layers(doc: dict, names: set) -> tuple:
    """Actually DELETE layers by name (top-level + assets), not just hide.

    A layer is deleted unless it is the parent of a layer that SURVIVES;
    if its only children are also being removed, deleting it is safe and
    it is deleted too (transitive case: a logo precomp whose parent logo
    layer would otherwise be left hidden alone — Telegram rejected the
    one file where that happened, file 009 in the sVnTeam_Dvix pack).
    Returns (removed, hidden).
    """
    removed = 0
    hidden = 0
    for scope in [doc.get("layers") or []] + [a.get("layers") or [] for a in doc.get("assets") or []]:
        # Inds of layers that survive (not in the removal set).
        surviving = {l.get("ind") for l in scope if str(l.get("nm", "")) not in names}
        parented_by_survivor = {l.get("parent") for l in scope
                                if l.get("parent") is not None and l.get("ind") in surviving}
        keep = []
        for layer in scope:
            if str(layer.get("nm", "")) in names:
                if layer.get("ind") in parented_by_survivor:
                    # Keep it as a Null control layer (ty=3, no shapes)
                    # so surviving children keep its transform, but no
                    # logo pixels remain and no hd=1 layer is left for
                    # Telegram to reject (the v1 file 009 failure mode).
                    layer["ty"] = 3
                    layer.pop("shapes", None)
                    layer.pop("hd", None)
                    hidden += 1
                    keep.append(layer)
                else:
                    removed += 1
            else:
                keep.append(layer)
        scope[:] = keep
    return removed, hidden


# Watermark shapes found in every file of the sVnTeam_Dvix pack, under
# many different layer names (CDC Outlines, Front 2, head, TON, FREE,
# Shape Layer 1/4, ...). Identical vertices across files = watermark,
# not scene content. Removing them by geometry, not by name, is what
# finally removes the logo from files where the name did not match.
# Two signatures: the 8-vertex "C" shape and the 6-vertex shape that
# sits in the same watermark layers (CDC Outlines / head groups).
_WATERMARK_VERTICES = [(-145.6, -133.1), (-113.7, -76.7), (59.9, -76.7), (62.1, -73),
                        (-23.5, 76.7), (8.4, 133.1), (132.1, -80.7), (101.9, -133.1)]
_WATERMARK_VERTICES_2 = [(-65.0, -160.9)]  # first vertex only; full check below


def _is_watermark_shape(path_k: dict) -> bool:
    v = path_k.get("v") if isinstance(path_k, dict) else None
    if not isinstance(v, list):
        return False
    if len(v) == len(_WATERMARK_VERTICES):
        if all(abs(float(v[i][0]) - _WATERMARK_VERTICES[i][0]) < 1.0 and
               abs(float(v[i][1]) - _WATERMARK_VERTICES[i][1]) < 1.0
               for i in range(len(v))):
            return True
    if len(v) == 6:
        # Second watermark part: 209x313 shape, first vertex (-65,-160.9).
        try:
            xs = [float(p[0]) for p in v]
            ys = [float(p[1]) for p in v]
            if (abs(float(v[0][0]) + 65.0) < 1.0 and abs(float(v[0][1]) + 160.9) < 1.0
                    and abs((max(xs) - min(xs)) - 209) < 5 and abs((max(ys) - min(ys)) - 313) < 5):
                return True
        except Exception:
            pass
    # Scale-invariant fallback for the main shape.
    if len(v) != len(_WATERMARK_VERTICES):
        return False
    try:
        xs = [float(p[0]) for p in v]
        ys = [float(p[1]) for p in v]
        w = max(xs) - min(xs)
        h = max(ys) - min(ys)
        if w <= 0 or h <= 0:
            return False
        norm = [((float(p[0]) - min(xs)) / w, (float(p[1]) - min(ys)) / h) for p in v]
        txs = [p[0] for p in _WATERMARK_VERTICES]
        tys = [p[1] for p in _WATERMARK_VERTICES]
        tw = max(txs) - min(txs)
        th = max(tys) - min(tys)
        tnorm = [((p[0] - min(txs)) / tw, (p[1] - min(tys)) / th) for p in _WATERMARK_VERTICES]
        return all(abs(norm[i][0] - tnorm[i][0]) < 0.02 and abs(norm[i][1] - tnorm[i][1]) < 0.02
                   for i in range(len(v)))
    except Exception:
        return False


def shape_signature(path_k: dict):
    """Generic pack-common shape signature, independent of any one pack.

    A shape that appears with identical geometry (after normalising by
    its own bounding box) in many items of a pack is almost certainly a
    watermark/logo element, not scene content — scene content varies
    from emoji to emoji, a watermark does not. This is how the bot
    removes a new pack's watermark without hardcoding that pack's
    vertices: count signatures across the whole pack first, then
    remove shapes whose signature is pack-common. Returns None for
    shapes that are too small or degenerate to be a watermark.
    """
    v = path_k.get("v") if isinstance(path_k, dict) else None
    if not isinstance(v, list) or len(v) < 3:
        return None
    try:
        xs = [float(p[0]) for p in v]
        ys = [float(p[1]) for p in v]
        w = max(xs) - min(xs)
        h = max(ys) - min(ys)
        if w <= 0 or h <= 0 or max(w, h) < 50:
            return None
        scale = max(w, h)
        norm = tuple((round((float(p[0]) - min(xs)) / scale, 2),
                      round((float(p[1]) - min(ys)) / scale, 2)) for p in v)
        return (len(v), norm)
    except Exception:
        return None


def pack_common_signatures(docs: list) -> set:
    """Signatures that appear in at least max(3, len(docs)//3) docs."""
    from collections import Counter
    counts = Counter()
    for doc in docs:
        sigs = set()
        for node in _walk(doc):
            if isinstance(node, dict) and node.get("ty") == "sh":
                sig = shape_signature((node.get("ks") or {}).get("k") or {})
                if sig is not None:
                    sigs.add(sig)
        for sig in sigs:
            counts[sig] += 1
    threshold = max(3, len(docs) // 3) if docs else 3
    return {sig for sig, n in counts.items() if n >= threshold}


def tgs_remove_shapes_by_signatures(doc: dict, signatures: set) -> int:
    """Remove groups containing a shape whose signature is pack-common."""
    if not signatures:
        return 0
    removed = [0]

    def clean_shapes(shapes):
        kept = []
        for item in shapes:
            if isinstance(item, dict) and item.get("ty") == "gr":
                contains = False
                for node in _walk(item):
                    if isinstance(node, dict) and node.get("ty") == "sh":
                        if shape_signature((node.get("ks") or {}).get("k") or {}) in signatures:
                            contains = True
                            break
                if contains:
                    removed[0] += 1
                    continue
            kept.append(item)
        return kept

    for layer in doc.get("layers") or []:
        if isinstance(layer.get("shapes"), list):
            layer["shapes"] = clean_shapes(layer["shapes"])
    for asset in doc.get("assets") or []:
        for layer in asset.get("layers") or []:
            if isinstance(layer.get("shapes"), list):
                layer["shapes"] = clean_shapes(layer["shapes"])
    return removed[0]


def tgs_remove_watermark_shapes(doc: dict) -> int:
    """Remove groups that contain the watermark shape, anywhere in the
    document (layers + assets, nested groups). Other groups in the same
    layer survive. Returns the number of groups removed."""
    removed = [0]

    def prune_items(items: list):
        keep = []
        for item in items:
            if not isinstance(item, dict):
                keep.append(item)
                continue
            if item.get("ty") == "gr":
                inner = item.get("it") or []
                # If this group directly contains the watermark path, drop the whole group.
                if any(isinstance(sub, dict) and sub.get("ty") == "sh" and
                       _is_watermark_shape((sub.get("ks") or {}).get("k") or {})
                       for sub in inner):
                    removed[0] += 1
                    continue
                prune_items(inner)
                # Drop groups left empty by pruning (except transform-only groups).
                if not inner:
                    continue
            keep.append(item)
        items[:] = keep

    for layer in doc.get("layers") or []:
        prune_items(layer.get("shapes") or [])
    for asset in doc.get("assets") or []:
        for layer in asset.get("layers") or []:
            prune_items(layer.get("shapes") or [])
    return removed[0]


def tgs_prune_empty_shape_layers(doc: dict) -> int:
    """Delete ty=4 shape layers left with no shapes at all.

    Watermark removal can empty a layer (its only groups were the
    watermark) — Telegram rejects such a file as "wrong file type",
    which is what killed the v3 publish (file 003, Shape Layer 1;
    file 011, three layers). Only delete a layer no other layer is
    parented to; Null layers (ty=3) are never touched, even with no
    shapes, because they are animation anchors for their children.
    """
    removed = [0]

    def prune(layers):
        parents = {l.get("parent") for l in layers or []}
        kept = []
        for layer in layers or []:
            if (layer.get("ty") == 4 and not (layer.get("shapes") or [])
                    and layer.get("ind") not in parents):
                removed[0] += 1
                continue
            kept.append(layer)
        return kept

    if isinstance(doc.get("layers"), list):
        doc["layers"] = prune(doc["layers"])
    for asset in doc.get("assets") or []:
        if isinstance(asset.get("layers"), list):
            asset["layers"] = prune(asset["layers"])
    return removed[0]


def tgs_prune_unused_assets(doc: dict) -> int:
    """Remove precomp assets no remaining layer references (by refId).

    After logo layers are deleted, their assets can become empty and
    unreferenced; pruning them keeps the file clean for Telegram's
    validator. Returns the number of assets removed.
    """
    refs = set()

    def collect(layers):
        for layer in layers or []:
            if layer.get("refId"):
                refs.add(layer.get("refId"))

    collect(doc.get("layers"))
    for asset in doc.get("assets") or []:
        collect(asset.get("layers"))
    before = len(doc.get("assets") or [])
    doc["assets"] = [a for a in doc.get("assets") or [] if a.get("id") in refs]
    return before - len(doc.get("assets") or [])


def _quad_to_cubic(p0, q, p2):
    return ([p0[0] + 2 / 3 * (q[0] - p0[0]), p0[1] + 2 / 3 * (q[1] - p0[1])],
            [p2[0] + 2 / 3 * (q[0] - p2[0]), p2[1] + 2 / 3 * (q[1] - p2[1])])


def text_to_lottie_shapes(text: str, font_path: str, size: float = 36.0) -> tuple:
    """Convert text to Lottie shape items using a TrueType font.

    Requires fontTools (in requirements.txt). Glyph outlines are read
    with RecordingPen; quadratic curves become cubic beziers, lines stay
    lines. Returns (shapes, total_width). Y is flipped (font up -> Lottie
    down). Raises RuntimeError when fontTools or a glyph is missing —
    never a fake shape.
    """
    try:
        from fontTools.ttLib import TTFont
        from fontTools.pens.recordingPen import RecordingPen
    except ImportError as exc:
        raise RuntimeError("fontTools نصب نیست؛ برای افزودن متن برداری، fonttools را نصب کنید.") from exc
    font = TTFont(font_path)
    cmap = font.getBestCmap()
    glyph_set = font.getGlyphSet()
    units = font["head"].unitsPerEm
    scale = size / units
    shapes = []
    x_cursor = 0.0
    for ch in text:
        glyph_name = cmap.get(ord(ch))
        if not glyph_name:
            raise RuntimeError(f"حرف «{ch}» در فونت پیدا نشد.")
        pen = RecordingPen()
        glyph_set[glyph_name].draw(pen)
        contours = []
        cur_ops = []
        for op, args in pen.value:
            if op == "moveTo" and cur_ops:
                contours.append(cur_ops)
                cur_ops = []
            cur_ops.append((op, args))
        if cur_ops:
            contours.append(cur_ops)
        for ops in contours:
            flipped = []
            for op, args in ops:
                new_args = []
                for pt in args:
                    if isinstance(pt, tuple):
                        new_args.append(((x_cursor + pt[0]) * scale, -pt[1] * scale))
                    else:
                        new_args.append(pt)
                flipped.append((op, tuple(new_args)))
            # Build vertices / tangents for one contour.
            pts = []
            segs = []
            cur = None
            start = None
            for op, args in flipped:
                if op == "moveTo":
                    cur = args[0]
                    start = cur
                    pts.append(cur)
                elif op == "lineTo":
                    segs.append(("line", cur, args[0]))
                    cur = args[0]
                    pts.append(cur)
                elif op == "qCurveTo":
                    segs.append(("quad", cur, args))
                    cur = args[-1]
                    pts.append(cur)
                elif op == "curveTo":
                    segs.append(("cubic", cur, args))
                    cur = args[-1]
                    pts.append(cur)
                elif op == "closePath":
                    if cur != start:
                        segs.append(("line", cur, start))
            n = len(pts)
            if n == 0:
                continue
            iv = [[0.0, 0.0] for _ in range(n)]
            ov = [[0.0, 0.0] for _ in range(n)]
            for k, seg in enumerate(segs):
                j = (k + 1) % n
                if seg[0] == "line":
                    pass
                elif seg[0] == "quad":
                    q = seg[2][-1] if len(seg[2]) > 1 else seg[2][0]
                    # Single-control case is exact; multi-control (implied
                    # on-curve points) uses the last control as approximation.
                    c1, c2 = _quad_to_cubic(seg[1], q, seg[2][-1])
                    ov[k] = [round(c1[0] - seg[1][0], 2), round(c1[1] - seg[1][1], 2)]
                    iv[j] = [round(c2[0] - seg[2][-1][0], 2), round(c2[1] - seg[2][-1][1], 2)]
                elif seg[0] == "cubic":
                    c1, c2, p2 = seg[2]
                    ov[k] = [round(c1[0] - seg[1][0], 2), round(c1[1] - seg[1][1], 2)]
                    iv[j] = [round(c2[0] - p2[0], 2), round(c2[1] - p2[1], 2)]
            shapes.append({"ind": 0, "ty": "sh",
                           "ks": {"a": 0, "k": {"i": iv, "o": ov,
                                                "v": [[round(x, 2), round(y, 2)] for x, y in pts],
                                                "c": True}},
                           "nm": f"Path {ch}", "hd": False})
        x_cursor += glyph_set[glyph_name].width
    return shapes, x_cursor * scale


def tgs_add_text_layer(doc: dict, text: str, font_path: str, size: float = 36.0,
                       fill_hex: str = "#FFFFFF", position: tuple = None,
                       logo_ks: dict | None = None, logo_ip: int | None = None,
                       logo_op: int | None = None, logo_parent=None,
                       scope: str | None = None, logo_name: str | None = None) -> str:
    """Add text as vector shapes on top of a TGS document.

    Returns the new layer name. Position defaults to bottom-centre
    (canvas 512 wide, y=470). When logo_ks is given (the deleted old
    logo layer's transform, copied verbatim), the new text sits exactly
    where the old logo sat and follows its animation (position, scale,
    rotation, opacity keyframes) instead of sitting static at the bottom.
    The layer spans the whole animation (ip=0, op=doc op) unless logo
    ip/op are given. When scope is an asset id, the text layer is added
    INSIDE that asset (in the asset's own coordinate space), which is
    how a logo that lived in a precomp is replaced in its own place.
    """
    import copy as _copy
    shapes, width = text_to_lottie_shapes(text, font_path, size)
    if not shapes:
        raise RuntimeError("متن به شکل تبدیل نشد.")
    r = int(fill_hex[1:3], 16) / 255.0
    g = int(fill_hex[3:5], 16) / 255.0
    b = int(fill_hex[5:7], 16) / 255.0
    if logo_ks is not None:
        ks = _copy.deepcopy(logo_ks)
        # Anchor: for a TEXT logo (Outlines), the original anchor is
        # near the text's origin, so the text extends rightwards from
        # the logo position — keep that anchor, otherwise the text
        # shifts left under the banner's white circle. For a SHAPE
        # logo, centre the text on the logo's anchor instead.
        _is_text_logo = any(t in str(logo_name or "") for t in ("Outlines", "Text", "متن"))
        if not _is_text_logo:
            # Centre the text on the logo's anchor: shapes run x=0..width,
            # y=-size..0, so the centre is (width/2, -size/2).
            ks["a"] = {"a": 0, "k": [round(width / 2, 2), round(-size / 2, 2), 0]}
        ks.setdefault("o", {"a": 0, "k": 100})
        ks.setdefault("r", {"a": 0, "k": 0})
        ks.setdefault("s", {"a": 0, "k": [100, 100, 100]})
    else:
        x = 256 - width / 2 if position is None else position[0]
        y = 470 if position is None else position[1]
        ks = {"o": {"a": 0, "k": 100}, "r": {"a": 0, "k": 0},
              "p": {"a": 0, "k": [round(x, 2), y, 0]}, "a": {"a": 0, "k": [0, 0, 0]},
              "s": {"a": 0, "k": [100, 100, 100]}}
    max_ind = max([l.get("ind", 0) for l in doc.get("layers") or []] or [0])
    layer = {
        "ddd": 0, "ind": max_ind + 1, "ty": 4, "nm": f"{text} Brand", "sr": 1,
        "ks": ks,
        "ao": 0,
        "shapes": [{"ty": "gr", "it": shapes + [
            {"ty": "fl", "c": {"a": 0, "k": [r, g, b, 1]}, "o": {"a": 0, "k": 100},
             "r": 1, "bm": 0, "nm": "Fill 1", "hd": False},
            {"ty": "st", "c": {"a": 0, "k": [0, 0, 0, 1]}, "o": {"a": 0, "k": 100},
             "w": {"a": 0, "k": 2}, "lc": 1, "lj": 1, "bm": 0, "nm": "Stroke 1", "hd": False},
            {"ty": "tr", "p": {"a": 0, "k": [0, 0]}, "a": {"a": 0, "k": [0, 0]},
             "s": {"a": 0, "k": [100, 100]}, "r": {"a": 0, "k": 0}, "o": {"a": 0, "k": 100},
             "sk": {"a": 0, "k": 0}, "sa": {"a": 0, "k": 0}, "nm": "Transform"}],
            "nm": text, "bm": 0, "hd": False}],
        "ip": logo_ip if logo_ip is not None else 0,
        "op": logo_op if logo_op is not None else doc.get("op", 180), "st": 0, "bm": 0,
    }
    if logo_parent is not None:
        layer["parent"] = logo_parent
    # Clamp op to the document's own op (a logo layer can report a larger op).
    if layer["op"] > doc.get("op", 180):
        layer["op"] = doc.get("op", 180)
    if scope is not None:
        # Place inside the asset, in its own space (banner case).
        for asset in doc.get("assets") or []:
            if asset.get("id") == scope:
                ainds = [l.get("ind", 0) for l in asset.get("layers") or []]
                layer["ind"] = (max(ainds) + 1) if ainds else 1
                if layer.get("parent") is not None and layer["parent"] not in ainds:
                    layer.pop("parent", None)
                asset.setdefault("layers", []).append(layer)
                return layer["nm"]
        # Asset not found: fall through to top-level.
    doc.setdefault("layers", []).append(layer)
    return layer["nm"]


# Curated font styles. The cute-bold pair lives in the project's own
# assets/fonts (downloaded from Google Fonts and instanced to static
# bold weights) so it survives machine rebuilds; the rest are system
# fonts. Ehsan's standing choice (2026-10-29): brand names use the
# cute-bold font — choose_font_for_tgs prefers it whenever present.
import os as _os

_FONTS_DIR = _os.path.join(
    _os.path.dirname(_os.path.abspath(__file__)), "..", "..", "assets", "fonts")
FONT_STYLES = {
    "cute_bold": (_os.path.join(_FONTS_DIR, "Fredoka-Bold.ttf"), "کیوت و پررنگ (فردوکا)"),
    "cute_round": (_os.path.join(_FONTS_DIR, "Baloo2-ExtraBold.ttf"), "کیوت و گرد (بالو)"),
    "bold_sans": ("/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf", "پررنگ و بازیگوش"),
    "display": ("/usr/share/fonts/truetype/noto/NotoSansDisplay-Bold.ttf", "مدرن و تمیز"),
    "mono": ("/usr/share/fonts/truetype/dejavu/DejaVuSansMono-Bold.ttf", "تکنولوژیک و گیمی"),
    "serif": ("/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf", "شیک و کلاسیک"),
    "liberation_sans": ("/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf", "ساده و خوانا"),
    "noto_serif": ("/usr/share/fonts/truetype/noto/NotoSerif-Bold.ttf", "رسمی و لوکس"),
}

# The shared registry (registry.py — same data the Mini App reads)
# extends the style table with every downloaded display/Persian
# font; the six system fonts above stay as fallbacks. Ids already
# present (cute_bold, cute_round) keep their entries above.
try:
    from .registry import FONTS as _REGISTRY_FONTS
except Exception:  # pragma: no cover - direct-module import fallback
    try:
        from emoji_forge.registry import FONTS as _REGISTRY_FONTS
    except Exception:
        _REGISTRY_FONTS = []
for _entry in _REGISTRY_FONTS:
    if _entry["id"] not in FONT_STYLES:
        FONT_STYLES[_entry["id"]] = (
            _os.path.join(_FONTS_DIR, _entry["file"]), _entry["fa"])
del _entry

# Module-level font override: when set (and the font file exists),
# choose_font_for_tgs returns that style for every document until
# cleared. With no override set, behaviour is exactly as before.
_FONT_OVERRIDE = None


def set_font_override(style_id):
    """Force one FONT_STYLES id for subsequent choose_font_for_tgs
    calls (e.g. a font picked in the Mini App)."""
    global _FONT_OVERRIDE
    _FONT_OVERRIDE = style_id or None


def clear_font_override():
    """Drop the override set by set_font_override."""
    global _FONT_OVERRIDE
    _FONT_OVERRIDE = None


def choose_font_for_tgs(doc: dict, index: int = 0) -> tuple:
    """Pick a font style for one TGS document, automatically and varied.

    Subject hints come from layer names in the file itself: games/camera
    scenes get mono, VPN/internet/logo scenes get display, emblem/base
    scenes get serif, smoke/particles get bold sans. Anything else falls
    back to rotation by index, so neighbouring emojis do not all share
    one font. Returns (style_name, font_path, persian_description).
    Mati's personal review in mati_plan.json can override this per item.
    """
    import os as _os
    # Explicit override (Mini App / caller choice) wins when set and
    # the font file is actually present.
    if _FONT_OVERRIDE is not None and _FONT_OVERRIDE in FONT_STYLES:
        _opath, _odesc = FONT_STYLES[_FONT_OVERRIDE]
        if _os.path.exists(_opath):
            return _FONT_OVERRIDE, _opath, _odesc
    # Ehsan standing choice (2026-10-29): cute-bold font for brand names.
    _cute_path, _cute_desc = FONT_STYLES["cute_bold"]
    if _os.path.exists(_cute_path):
        return "cute_bold", _cute_path, _cute_desc
    names = " ".join(str(l.get("nm", "")) for l in _walk(doc)).lower()
    if any(w in names for w in ["game", "camera", "button", "cursor"]):
        style = "mono"
    elif any(w in names for w in ["svn", "internet", "logo", "cdc"]):
        style = "display"
    elif any(w in names for w in ["emblem", "base", "crown", "gold"]):
        style = "serif"
    elif any(w in names for w in ["smoke", "particle", "spark"]):
        style = "bold_sans"
    else:
        styles = list(FONT_STYLES.keys())
        style = styles[index % len(styles)]
    path, desc = FONT_STYLES[style]
    if not _os.path.exists(path):
        path, desc = FONT_STYLES["bold_sans"]
        style = "bold_sans"
    return style, path, desc


def _walk(node):
    """Yield every dict in the whole document (layers + assets + groups)."""
    if isinstance(node, dict):
        yield node
        for value in node.values():
            yield from _walk(value)
    elif isinstance(node, list):
        for value in node:
            yield from _walk(value)


def _flat_colour(node: dict):
    """Return the flat [r,g,b,a] list of a fill/stroke colour, or None."""
    colour = node.get("c") or {}
    k = colour.get("k")
    if isinstance(k, list) and len(k) >= 3 and all(isinstance(v, (int, float)) for v in k[:3]):
        return k
    return None


def _gradient_colour_stops(g_node: dict):
    """Return (p, flat_list) for a gradient fill, or None.

    Flat gradients: g.k is a number list, first p*4 numbers are colour
    stops (offset, r, g, b), the rest are alpha stops and are untouched.
    Animated gradients: g.k is {"k": [keyframes]}, each keyframe's "s"
    list has the same layout. This helper handles the flat case; the
    animated case is handled by _recolor_gradient_list.
    """
    g = g_node.get("g") or {}
    p = g.get("p")
    k = g.get("k")
    if isinstance(p, int) and isinstance(k, list) and all(isinstance(v, (int, float)) for v in k):
        return p, k
    return None


def _recolor_gradient_list(values: list, p: int, fn, skip_skin: bool = False) -> int:
    """Apply fn(r,g,b) -> (r,g,b) to each colour stop in a gradient list.

    Only the first p*4 numbers are colour stops; alpha stops after
    them are left untouched. Returns how many stops changed.
    """
    changed = 0
    for i in range(p):
        base = i * 4
        if base + 3 >= len(values):
            break
        r, g, b = float(values[base + 1]), float(values[base + 2]), float(values[base + 3])
        if skip_skin and is_skin_like(int(r * 255), int(g * 255), int(b * 255)):
            continue
        nr, ng, nb = fn(r, g, b)
        values[base + 1], values[base + 2], values[base + 3] = nr, ng, nb
        changed += 1
    return changed


def _apply_to_gradient(node: dict, fn, skip_skin: bool = False) -> int:
    """Apply fn to every colour stop of a gradient fill node. Handles
    both flat gradients and keyframed gradients. Returns stop count."""
    g = node.get("g") or {}
    p = g.get("p")
    k = g.get("k")
    if not isinstance(p, int):
        return 0
    if isinstance(k, list) and all(isinstance(v, (int, float)) for v in k):
        return _recolor_gradient_list(k, p, fn, skip_skin)
    if isinstance(k, dict):
        inner = k.get("k")
        if isinstance(inner, list) and all(isinstance(v, (int, float)) for v in inner):
            # Animated flag off (a=0) but wrapped in a dict: flat list.
            return _recolor_gradient_list(inner, p, fn, skip_skin)
        total = 0
        for keyframe in inner or []:
            s = keyframe.get("s") if isinstance(keyframe, dict) else None
            if isinstance(s, list) and all(isinstance(v, (int, float)) for v in s):
                total += _recolor_gradient_list(s, p, fn, skip_skin)
        return total
    return 0


def _shift_rgb(r: float, g: float, b: float, strength: float, target_hex: str) -> tuple:
    from .brand import hex_to_rgb
    tr, tg, tb = [c / 255.0 for c in hex_to_rgb(target_hex)]
    return (r + (tr - r) * strength, g + (tg - g) * strength, b + (tb - b) * strength)


def tgs_has_keyframed_fills(doc: dict) -> bool:
    """True when any solid fill uses keyframes instead of a flat colour."""
    for node in _walk(doc):
        if node.get("ty") == "fl":
            k = (node.get("c") or {}).get("k")
            if isinstance(k, list) and k and isinstance(k[0], dict):
                return True
    return False


def _palette_stops(primary_hex: str, secondary_hex: str, gradient_base: str | None):
    """Stops for duotone/gradient mapping, as 0..1 RGB tuples.

    With a gradient base, the theme is red / white / black, not just
    shades of red: dark originals go black, mid-tones go red, light
    originals go white — so the old blue theme is fully gone and the
    new theme reads red-white-black, as the user asked.
    """
    from .brand import hex_to_rgb
    if gradient_base:
        from .image_engine import gradient_palette
        palette = [tuple(c / 255.0 for c in stop) for stop in gradient_palette(gradient_base)]
        base = palette[2] if len(palette) > 2 else palette[-1]
        dark = tuple(c * 0.35 for c in base)
        light = tuple(c + (1 - c) * 0.6 for c in base)
        return [(0.0, 0.0, 0.0), dark, base, light, (1.0, 1.0, 1.0)]
    pr, pg, pb = [c / 255.0 for c in hex_to_rgb(primary_hex)]
    sr, sg, sb = [c / 255.0 for c in hex_to_rgb(secondary_hex)]
    if (sr, sg, sb) == (pr, pg, pb):
        sr, sg, sb = (pr + (1 - pr) * 0.6, pg + (1 - pg) * 0.6, pb + (1 - pb) * 0.6)
    return [(pr * 0.65, pg * 0.65, pb * 0.65), (sr, sg, sb)]


def _map_luminance(stops, lum: float):
    pos = lum * (len(stops) - 1)
    i = min(len(stops) - 2, int(pos))
    f = pos - i
    a, b = stops[i], stops[i + 1]
    return tuple(a[c] + (b[c] - a[c]) * f for c in range(3))


def tgs_duotone(doc: dict, primary_hex: str, secondary_hex: str, gradient_base: str | None = None) -> int:
    """Neutralise every colour in the whole TGS (layers + assets), then
    rebuild it from a spectrum: flat fills, strokes, and gradient stops
    (flat and keyframed). Luminance picks the stop. Keyframed flat fills
    are skipped. Returns how many colours/stops changed.
    """
    stops = _palette_stops(primary_hex, secondary_hex, gradient_base)
    changed = 0
    for node in _walk(doc):
        ty = node.get("ty")
        if ty in ("fl", "st"):
            k = _flat_colour(node)
            if k is not None:
                lum = 0.299 * float(k[0]) + 0.587 * float(k[1]) + 0.114 * float(k[2])
                nr, ng, nb = _map_luminance(stops, lum)
                k[0], k[1], k[2] = nr, ng, nb
                changed += 1
        elif ty in ("gf", "gs"):
            def _gf_map(r, g, b, _stops=stops):
                lum = 0.299 * r + 0.587 * g + 0.114 * b
                return _map_luminance(_stops, lum)
            changed += _apply_to_gradient(node, _gf_map, skip_skin=False)
    return changed


def tgs_recolor(doc: dict, target_hex: str, strength: float) -> int:
    """Shift colours in the whole TGS (layers + assets): flat fills,
    strokes, and gradient stops. Skin-like colours skipped. Keyframed
    flat fills are skipped. Returns how many colours/stops changed.
    """
    if strength <= 0:
        return 0
    changed = 0
    for node in _walk(doc):
        ty = node.get("ty")
        if ty in ("fl", "st"):
            k = _flat_colour(node)
            if k is not None:
                r, g, b = float(k[0]), float(k[1]), float(k[2])
                if not is_skin_like(int(r * 255), int(g * 255), int(b * 255)):
                    nr, ng, nb = _shift_rgb(r, g, b, strength, target_hex)
                    k[0], k[1], k[2] = nr, ng, nb
                    changed += 1
        elif ty in ("gf", "gs"):
            def _gf_shift(r, g, b, _s=strength, _t=target_hex):
                return _shift_rgb(r, g, b, _s, _t)
            changed += _apply_to_gradient(node, _gf_shift, skip_skin=True)
    return changed


def process_webm(input_path: str, output_path: str, frame_fn, max_seconds: float = 3.0,
                 output_kind: str = "emoji") -> dict:
    """Extract frames, apply frame_fn(frame: PIL Image, index: int) to each,
    re-encode VP9 with alpha, no audio. frame_fn must be deterministic.

    Output sizing (official Telegram rules):
    - emoji: every frame is padded to a square and resized to exactly 100x100.
    - sticker: longest side exactly 512, aspect preserved.

    Returns a report dict with real numbers (frames, fps, sizes) or raises.
    """
    if shutil.which("ffmpeg") is None:
        raise RuntimeError("FFmpeg در دسترس نیست؛ پردازش WEBM ممکن نیست.")
    info = probe_webm(input_path)
    fps = info.get("fps") or 30.0
    fps = min(fps, 30.0)
    with tempfile.TemporaryDirectory(prefix="forge-webm-") as tmp:
        frames_dir = os.path.join(tmp, "frames")
        os.makedirs(frames_dir)
        proc = subprocess.run(
            ["ffmpeg", "-y", "-v", "error", "-i", input_path,
             "-vf", f"fps={fps}", os.path.join(frames_dir, "f%05d.png")],
            capture_output=True, text=True, timeout=180,
        )
        if proc.returncode != 0:
            raise RuntimeError(f"استخراج فریم WEBM ناموفق بود: {(proc.stderr or '')[:300]}")
        frame_files = sorted(f for f in os.listdir(frames_dir) if f.endswith(".png"))
        if not frame_files:
            raise RuntimeError("هیچ فریمی از WEBM استخراج نشد.")
        max_frames = int(max_seconds * fps)
        trimmed = len(frame_files) > max_frames
        frame_files = frame_files[:max_frames]
        out_dir = os.path.join(tmp, "out")
        os.makedirs(out_dir)
        for idx, name in enumerate(frame_files):
            frame = Image.open(os.path.join(frames_dir, name)).convert("RGBA")
            processed = frame_fn(frame, idx)
            processed = processed.convert("RGBA")
            if output_kind == "emoji":
                from .image_engine import to_square_100
                processed = to_square_100(processed)
            elif output_kind == "sticker":
                from .image_engine import to_sticker_512
                processed = to_sticker_512(processed)
            processed.save(os.path.join(out_dir, name), format="PNG")
        os.makedirs(os.path.dirname(output_path), exist_ok=True)
        last_error = ""
        for bitrate in ("500k", "300k", "180k", "100k"):
            proc = subprocess.run(
                ["ffmpeg", "-y", "-v", "error", "-framerate", str(fps),
                 "-i", os.path.join(out_dir, "f%05d.png"),
                 "-c:v", "libvpx-vp9", "-b:v", bitrate, "-pix_fmt", "yuva420p",
                 "-an", output_path],
                capture_output=True, text=True, timeout=180,
            )
            if proc.returncode != 0:
                last_error = proc.stderr or ""
                continue
            if os.path.getsize(output_path) <= 256 * 1024:
                break
        else:
            if not os.path.exists(output_path):
                raise RuntimeError(f"بازکدگذاری WEBM ناموفق بود: {last_error[:300]}")
    return {
        "frames": len(frame_files),
        "fps": fps,
        "trimmed": trimmed,
        "output_bytes": os.path.getsize(output_path),
        "fits_256k": os.path.getsize(output_path) <= 256 * 1024,
        "output_kind": output_kind,
    }
