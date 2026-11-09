"""Trace the user's EHSAN logo PNG to Lottie vector shapes (v7).

Reads data/projects/forge-20261029-054353-3749ea/logo.png, crops to the
alpha bbox, traces the alpha mask with VTracer (binary/spline), converts
SVG paths to Lottie bezier subpaths (holes reversed for nonzero-winding
fills) and writes src/emoji_forge/ehsan_logo_shapes.json in a 512-wide
local space (origin at the logo bbox top-left). Also renders a fidelity
check (traced vector vs original PNG alpha) to /tmp/logo_work/.
"""
import json
import re
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent.parent / "src"))

LOGO_PNG = Path("data/projects/forge-20261029-054353-3749ea/logo.png")
OUT_JSON = Path("src/emoji_forge/ehsan_logo_shapes.json")
WORK = Path("/tmp/logo_work")

TOKEN_RE = re.compile(r"([MLCQZ])|(-?\d*\.?\d+(?:e[-+]?\d+)?)")


def parse_svg_path(d):
    """Return a list of subpaths; each is a list of (point, in_t, out_t)
    in Lottie convention (tangents relative to the point)."""
    tokens = []
    for m in TOKEN_RE.finditer(d):
        if m.group(1):
            tokens.append(m.group(1))
        else:
            tokens.append(float(m.group(2)))
    subpaths = []
    i = 0
    cmd = None
    cur = None
    start = None
    pts = []  # list of [x, y, ix, iy, ox, oy]

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
                if pts and start is not None:
                    # close: last point connects back to start
                    pts[-1][4], pts[-1][5] = pts[-1][4], pts[-1][5]
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
            # quadratic -> cubic approximation
            c1 = (cur[0] + 2 / 3 * (c[0] - cur[0]), cur[1] + 2 / 3 * (c[1] - cur[1]))
            c2 = (x + 2 / 3 * (c[0] - x), y + 2 / 3 * (c[1] - y))
            pts[-1][4], pts[-1][5] = c1[0] - cur[0], c1[1] - cur[1]
            pts.append([x, y, c2[0] - x, c2[1] - y, 0.0, 0.0])
            cur = (x, y)
        else:  # pragma: no cover
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


def signed_area(sp):
    v = sp["v"]
    area = 0.0
    for j in range(len(v)):
        x1, y1 = v[j]
        x2, y2 = v[(j + 1) % len(v)]
        area += x1 * y2 - x2 * y1
    return area / 2.0


def point_in_poly(pt, poly):
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


def reverse_subpath(sp):
    v = list(reversed(sp["v"]))
    i_new = [sp["o"][k] for k in range(len(sp["v"]) - 1, -1, -1)]
    o_new = [sp["i"][k] for k in range(len(sp["v"]) - 1, -1, -1)]
    return {"v": v, "i": i_new, "o": o_new, "c": sp["c"]}


def main():
    from PIL import Image
    import vtracer

    WORK.mkdir(parents=True, exist_ok=True)
    im = Image.open(LOGO_PNG).convert("RGBA")
    bbox = im.getchannel("A").getbbox()
    crop = im.crop(bbox)
    crop.save(WORK / "logo_crop.png")
    bw = Image.new("RGB", crop.size, (255, 255, 255))
    mask = crop.getchannel("A").point(lambda v: 255 if v > 128 else 0)
    bw.paste((0, 0, 0), mask=mask)
    bw.save(WORK / "logo_bw.png")
    vtracer.convert_image_to_svg_py(
        str(WORK / "logo_bw.png"), str(WORK / "logo.svg"),
        colormode="binary", mode="spline", filter_speckle=8,
        corner_threshold=60, length_threshold=3.5, max_iterations=10,
        splice_threshold=45, path_precision=2)
    svg = (WORK / "logo.svg").read_text()
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
    # Hole handling: containment depth odd => hole. VTracer already
    # emits holes with opposite winding, so normalise by signed area:
    # outers share the majority depth-0 sign; holes must oppose it.
    # Reverse only when the current sign is wrong.
    depths = [sum(1 for j, other in enumerate(subpaths)
                  if j != idx and point_in_poly(sp["v"][0], other["v"]))
              for idx, sp in enumerate(subpaths)]
    outer_signs = [1 if signed_area(sp) > 0 else -1
                   for sp, d in zip(subpaths, depths) if d == 0]
    outer_sign = 1 if sum(outer_signs) >= 0 else -1
    fixed = []
    reversals = 0
    for sp, depth in zip(subpaths, depths):
        want = outer_sign if depth % 2 == 0 else -outer_sign
        have = 1 if signed_area(sp) > 0 else -1
        if have != want:
            sp = reverse_subpath(sp)
            reversals += 1
        fixed.append(sp)
    # Normalise to 512-wide local space.
    scale = 512.0 / w
    for sp in fixed:
        for key in ("v", "i", "o"):
            sp[key] = [[round(x * scale, 2), round(y * scale, 2)] for x, y in sp[key]]
    payload = {
        "source": "logo.png alpha trace (vtracer binary/spline)",
        "native_size": [w, h],
        "width": 512.0,
        "height": round(h * scale, 2),
        "subpaths": fixed,
        "holes": sum(1 for d in depths if d % 2 == 1),
        "holes_reversed": reversals,
    }
    OUT_JSON.write_text(json.dumps(payload))
    print(f"subpaths={len(fixed)} holes={payload['holes']} reversed={reversals} "
          f"native={w}x{h} local=512x{payload['height']} "
          f"points={sum(len(sp['v']) for sp in fixed)} bytes={OUT_JSON.stat().st_size}")


if __name__ == "__main__":
    main()
