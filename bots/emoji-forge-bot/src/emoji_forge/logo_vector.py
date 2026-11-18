"""User-logo vectorisation (v9): trace ANY uploaded logo image to
Lottie vector shapes, in the same asset format as the owner's traced
Abolfazl logo (abolfazl_logo_shapes.json): {"name", "width": 512.0,
"height", "subpaths": [{v, i, o, c}]} in a 512-wide local space.

Method (same as scripts/trace_logo.py, generalised): build a binary
mask — from the alpha channel when the image has transparency,
otherwise from distance to the corner (background) colour — trace it
with VTracer (binary/spline), parse the SVG paths to Lottie beziers,
normalise hole winding by signed area, scale to 512 wide.

Honest limits: the trace is a SILHOUETTE. In a TGS the group is
rendered white-filled with a thin dark stroke (the house readability
device), so a multi-colour logo loses its internal colours in TGS
output. Raster outputs (WEBM/static) keep the original image via
overlay instead — vectorisation is only for the TGS path.
Returns None when tracing is impossible (no vtracer, empty mask,
unreadable image) — callers fall back to the brand-name text path.
"""
import io
import os
import re
import tempfile

TOKEN_RE = re.compile(r"([MLCQZ])|(-?\d*\.?\d+(?:e[-+]?\d+)?)")


def parse_svg_path(d):
    """SVG path (M/L/C/Q/Z, absolute) -> Lottie subpaths."""
    tokens = []
    for m in TOKEN_RE.finditer(d):
        tokens.append(m.group(1) if m.group(1) else float(m.group(2)))
    subpaths = []
    i = 0
    cmd = None
    cur = None
    start = None
    pts = []

    def flush(close=False):
        nonlocal pts
        if pts:
            subpaths.append((pts, close))
        pts = []

    while i < len(tokens):
        t = tokens[i]
        if isinstance(t, str):
            cmd = t
            i += 1
            if cmd == "Z":
                flush(close=True)
                cur = start
            continue
        if cmd == "M":
            flush()
            x, y = tokens[i], tokens[i + 1]
            i += 2
            cur = start = (x, y)
            pts = [[x, y, 0.0, 0.0, 0.0, 0.0]]
            cmd = "L"
        elif cmd == "L":
            x, y = tokens[i], tokens[i + 1]
            i += 2
            pts.append([x, y, 0.0, 0.0, 0.0, 0.0])
            cur = (x, y)
        elif cmd == "C":
            c1 = (tokens[i], tokens[i + 1])
            c2 = (tokens[i + 2], tokens[i + 3])
            x, y = tokens[i + 4], tokens[i + 5]
            i += 6
            pts[-1][4], pts[-1][5] = c1[0] - cur[0], c1[1] - cur[1]
            pts.append([x, y, c2[0] - x, c2[1] - y, 0.0, 0.0])
            cur = (x, y)
        elif cmd == "Q":
            c = (tokens[i], tokens[i + 1])
            x, y = tokens[i + 2], tokens[i + 3]
            i += 4
            c1 = (cur[0] + 2 / 3 * (c[0] - cur[0]), cur[1] + 2 / 3 * (c[1] - cur[1]))
            c2 = (x + 2 / 3 * (c[0] - x), y + 2 / 3 * (c[1] - y))
            pts[-1][4], pts[-1][5] = c1[0] - cur[0], c1[1] - cur[1]
            pts.append([x, y, c2[0] - x, c2[1] - y, 0.0, 0.0])
            cur = (x, y)
        else:  # pragma: no cover - vtracer only emits M/L/C/Q/Z here
            raise ValueError(f"unsupported token stream at {i}: {cmd}")
    flush()
    out = []
    for pts, closed in subpaths:
        if len(pts) < 2:
            continue
        out.append({
            "v": [[round(p[0], 2), round(p[1], 2)] for p in pts],
            "i": [[round(p[2], 2), round(p[3], 2)] for p in pts],
            "o": [[round(p[4], 2), round(p[5], 2)] for p in pts],
            "c": bool(closed),
        })
    return out


def _signed_area(sp):
    v = sp["v"]
    area = 0.0
    for j in range(len(v)):
        x1, y1 = v[j]
        x2, y2 = v[(j + 1) % len(v)]
        area += x1 * y2 - x2 * y1
    return area / 2.0


def _point_in_poly(pt, poly):
    x, y = pt
    inside = False
    n = len(poly)
    for j in range(n):
        x1, y1 = poly[j]
        x2, y2 = poly[(j + 1) % n]
        if (y1 > y) != (y2 > y):
            xin = x1 + (y - y1) * (x2 - x1) / (y2 - y1)
            if x < xin:
                inside = not inside
    return inside


def _reverse_subpath(sp):
    v = list(reversed(sp["v"]))
    i_new = [sp["o"][k] for k in range(len(sp["v"]) - 1, -1, -1)]
    o_new = [sp["i"][k] for k in range(len(sp["v"]) - 1, -1, -1)]
    return {"v": v, "i": i_new, "o": o_new, "c": sp["c"]}


def _mask_image(data: bytes):
    """Binary (black artwork on white) PIL image for the tracer,
    cropped to the artwork bbox. None when there is no artwork."""
    from PIL import Image
    im = Image.open(io.BytesIO(data)).convert("RGBA")
    if im.width < 4 or im.height < 4:
        return None
    alpha = im.getchannel("A")
    if alpha.getextrema()[0] < 250:  # real transparency present
        mask = alpha.point(lambda v: 255 if v > 128 else 0)
    else:
        # Opaque image: artwork = pixels far from the corner colour.
        rgb = im.convert("RGB")
        corner = rgb.getpixel((0, 0))
        px = rgb.load()
        mask = Image.new("L", rgb.size, 0)
        mp = mask.load()
        for y in range(rgb.height):
            for x in range(rgb.width):
                r, g, b = px[x, y]
                if abs(r - corner[0]) + abs(g - corner[1]) + abs(b - corner[2]) > 60:
                    mp[x, y] = 255
    bbox = mask.getbbox()
    if bbox is None:
        return None
    mask = mask.crop(bbox)
    bw = Image.new("RGB", mask.size, (255, 255, 255))
    bw.paste((0, 0, 0), mask=mask)
    return bw


def trace_logo_image(data: bytes, name: str = "Brand Logo") -> dict | None:
    """Trace logo image bytes to a Lottie-shapes asset dict, or None."""
    try:
        import vtracer
        bw = _mask_image(data)
        if bw is None:
            return None
        with tempfile.TemporaryDirectory() as tmp:
            src = os.path.join(tmp, "logo_bw.png")
            dst = os.path.join(tmp, "logo.svg")
            bw.save(src)
            vtracer.convert_image_to_svg_py(
                src, dst, colormode="binary", mode="spline",
                filter_speckle=8, corner_threshold=60, length_threshold=3.5,
                max_iterations=10, splice_threshold=45, path_precision=2)
            with open(dst, encoding="utf-8") as fh:
                svg = fh.read()
        w = float(re.search(r'width="([\d.]+)"', svg).group(1))
        h = float(re.search(r'height="([\d.]+)"', svg).group(1))
        subpaths = []
        for tag in re.findall(r"<path[^>]*>", svg):
            d = re.search(r'd="([^"]+)"', tag).group(1)
            tm = re.search(r'transform="translate\(([\d.\-]+)[ ,]+([\d.\-]+)\)"', tag)
            tx, ty = (float(tm.group(1)), float(tm.group(2))) if tm else (0.0, 0.0)
            for sp in parse_svg_path(d):
                sp["v"] = [[round(x + tx, 2), round(y + ty, 2)] for x, y in sp["v"]]
                subpaths.append(sp)
        if not subpaths or w <= 0 or h <= 0:
            return None
        depths = [sum(1 for j, other in enumerate(subpaths)
                      if j != idx and _point_in_poly(sp["v"][0], other["v"]))
                  for idx, sp in enumerate(subpaths)]
        outer_signs = [1 if _signed_area(sp) > 0 else -1
                       for sp, d in zip(subpaths, depths) if d == 0]
        outer_sign = 1 if sum(outer_signs) >= 0 else -1
        fixed = []
        for sp, depth in zip(subpaths, depths):
            want = outer_sign if depth % 2 == 0 else -outer_sign
            have = 1 if _signed_area(sp) > 0 else -1
            fixed.append(_reverse_subpath(sp) if have != want else sp)
        scale = 512.0 / w
        for sp in fixed:
            for key in ("v", "i", "o"):
                sp[key] = [[round(x * scale, 2), round(y * scale, 2)]
                           for x, y in sp[key]]
        return {"name": name, "width": 512.0,
                "height": round(h * scale, 2), "subpaths": fixed,
                "holes": sum(1 for d in depths if d % 2 == 1)}
    except Exception:
        return None
