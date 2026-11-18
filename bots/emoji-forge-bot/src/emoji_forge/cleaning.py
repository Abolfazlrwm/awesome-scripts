"""Cleaning, two independent modes.

Mode A (standard): fill the selected region with the average colour of
the ring of pixels immediately OUTSIDE the region. Honest limit: this is
NOT AI inpainting. It removes flat logo/text bars cleanly; for complex
texture the preview is the safeguard and the user should shrink the
region or review manually. A blurred fill was tried first and rejected:
inside a large uniform logo bar it stays the logo colour.

Mode B (limited redesign): mode A + a mild colour/saturation shift outside
the region. Skin-like pixels are skipped by a colour heuristic (not face
detection — no face detector is bundled, and none is claimed).

Regions are normalised 0..1000 so the same choice works for any size.
Presets are offered because raster images have no reliable automatic
logo/text detector; for TGS, layer-name candidates are listed instead
(see animation_engine.tgs_logo_candidates).
"""
from dataclasses import dataclass

from PIL import Image, ImageFilter

PRESETS = {
    "top": (0, 0, 1000, 180),
    "bottom": (0, 820, 1000, 1000),
    "center": (300, 300, 700, 700),
    "top-left": (0, 0, 400, 300),
    "top-right": (600, 0, 1000, 300),
    "bottom-left": (0, 700, 400, 1000),
    "bottom-right": (600, 700, 1000, 1000),
}


@dataclass
class Region:
    x0: int = 0
    y0: int = 0
    x1: int = 1000
    y1: int = 1000

    def validate(self):
        if not (0 <= self.x0 < self.x1 <= 1000 and 0 <= self.y0 < self.y1 <= 1000):
            raise ValueError("ناحیه پاک‌سازی نامعتبر است (باید ۰ تا ۱۰۰۰ و x0<x1 و y0<y1 باشد).")

    def pixels(self, width: int, height: int):
        self.validate()
        return (
            int(self.x0 * width / 1000),
            int(self.y0 * height / 1000),
            int(self.x1 * width / 1000),
            int(self.y1 * height / 1000),
        )


def clean_region(image: "Image.Image", region: Region) -> "Image.Image":
    """Mode A: fill the region with the average of its outside border ring.

    Everything outside the region is pixel-identical.
    """
    region.validate()
    out = image.convert("RGBA")
    x0, y0, x1, y1 = region.pixels(out.width, out.height)
    px = out.load()
    border = []
    # Ring just outside the region (clipped to the image).
    for x in range(max(0, x0 - 2), min(out.width, x1 + 2)):
        for y in (max(0, y0 - 2), max(0, y0 - 1), min(out.height - 1, y1), min(out.height - 1, y1 + 1)):
            if 0 <= y < out.height:
                border.append(px[x, y])
    for y in range(max(0, y0 - 2), min(out.height, y1 + 2)):
        for x in (max(0, x0 - 2), max(0, x0 - 1), min(out.width - 1, x1), min(out.width - 1, x1 + 1)):
            if 0 <= x < out.width:
                border.append(px[x, y])
    if border:
        n = len(border)
        fill = tuple(sum(p[c] for p in border) // n for c in range(4))
    else:
        fill = (0, 0, 0, 0)
    draw_patch = Image.new("RGBA", (x1 - x0, y1 - y0), fill)
    out.paste(draw_patch, (x0, y0))
    return out


def strip_edge_bars(image: "Image.Image") -> tuple:
    """Remove uniform logo bars at any edge (top/bottom/left/right), if present.

    A bar is a 15% edge strip whose opaque pixels are mostly one
    dominant colour. It is filled by stretching the neighbour rows/
    columns across the strip (so a gradient background is preserved
    per column/row), not by one flat average colour.
    Returns (image, ["top"|"bottom"|"left"|"right"]).
    """
    out = image.convert("RGBA")
    w, h = out.size
    stripped = []

    def _dominant(strip):
        colours = strip.getcolors(maxcolors=strip.width * strip.height)
        if not colours:
            return None
        opaque = [(n, c) for n, c in colours if c[3] > 10]
        total = sum(n for n, _ in opaque)
        if total < (strip.width * strip.height) * 0.5:
            return None
        dominant_count, dominant = max(opaque, key=lambda t: t[0])
        if dominant_count < total * 0.6:
            return None
        return dominant

    def _fill_top():
        nonlocal out
        strip_h = max(8, h * 15 // 100)
        strip = out.crop((0, 0, w, strip_h))
        if _dominant(strip) is None:
            return
        neighbour = out.crop((0, strip_h, w, min(h, strip_h + 2)))
        fill = neighbour.resize((w, strip_h), Image.NEAREST)
        out.paste(fill, (0, 0))
        stripped.append("top")

    def _fill_bottom():
        nonlocal out
        strip_h = max(8, h * 15 // 100)
        strip = out.crop((0, h - strip_h, w, h))
        if _dominant(strip) is None:
            return
        neighbour = out.crop((0, max(0, h - strip_h - 2), w, h - strip_h))
        fill = neighbour.resize((w, strip_h), Image.NEAREST)
        out.paste(fill, (0, h - strip_h))
        stripped.append("bottom")

    def _fill_left():
        nonlocal out
        strip_w = max(8, w * 15 // 100)
        strip = out.crop((0, 0, strip_w, h))
        if _dominant(strip) is None:
            return
        neighbour = out.crop((strip_w, 0, min(w, strip_w + 2), h))
        fill = neighbour.resize((strip_w, h), Image.NEAREST)
        out.paste(fill, (0, 0))
        stripped.append("left")

    def _fill_right():
        nonlocal out
        strip_w = max(8, w * 15 // 100)
        strip = out.crop((w - strip_w, 0, w, h))
        if _dominant(strip) is None:
            return
        neighbour = out.crop((max(0, w - strip_w - 2), 0, w - strip_w, h))
        fill = neighbour.resize((strip_w, h), Image.NEAREST)
        out.paste(fill, (w - strip_w, 0))
        stripped.append("right")

    _fill_top()
    _fill_bottom()
    _fill_left()
    _fill_right()
    return out, stripped


def clean_region_smart(image: "Image.Image", region: Region) -> "Image.Image":
    """Fill a region by bilinear interpolation of its borders.

    Stronger than the flat average: a horizontal gradient is kept
    (left/right borders interpolated across x) and a vertical gradient
    is kept (top/bottom borders interpolated across y), then averaged.
    Falls back to the flat fill when the region touches all borders.
    """
    region.validate()
    out = image.convert("RGBA")
    x0, y0, x1, y1 = region.pixels(out.width, out.height)
    rw, rh = x1 - x0, y1 - y0
    if rw <= 0 or rh <= 0:
        return out
    # Horizontal gradient: left column -> right column.
    h_img = None
    if x0 > 0 and x1 < out.width:
        left = out.crop((x0 - 1, y0, x0, y1)).resize((rw, rh), Image.NEAREST)
        right = out.crop((x1, y0, x1 + 1, y1)).resize((rw, rh), Image.NEAREST)
        mask = Image.linear_gradient("L").resize((rw, rh))
        h_img = Image.composite(right, left, mask)
    elif x0 > 0:
        h_img = out.crop((x0 - 1, y0, x0, y1)).resize((rw, rh), Image.NEAREST)
    elif x1 < out.width:
        h_img = out.crop((x1, y0, x1 + 1, y1)).resize((rw, rh), Image.NEAREST)
    # Vertical gradient: top row -> bottom row.
    v_img = None
    if y0 > 0 and y1 < out.height:
        top = out.crop((x0, y0 - 1, x1, y0)).resize((rw, rh), Image.NEAREST)
        bottom = out.crop((x0, y1, x1, y1 + 1)).resize((rw, rh), Image.NEAREST)
        mask = Image.linear_gradient("L").rotate(90, expand=True).resize((rw, rh))
        v_img = Image.composite(bottom, top, mask)
    elif y0 > 0:
        v_img = out.crop((x0, y0 - 1, x1, y0)).resize((rw, rh), Image.NEAREST)
    elif y1 < out.height:
        v_img = out.crop((x0, y1, x1, y1 + 1)).resize((rw, rh), Image.NEAREST)
    if h_img is not None and v_img is not None:
        fill = Image.blend(h_img, v_img, 0.5)
    elif h_img is not None:
        fill = h_img
    elif v_img is not None:
        fill = v_img
    else:
        return clean_region(image, region)
    out.paste(fill, (x0, y0))
    return out


def detect_uniform_blob_regions(image: "Image.Image") -> list:
    """Detect uniform rectangular blobs in edge bands (logo candidates).

    Downscales to 100x100, quantises colours, finds connected
    components. A component qualifies only if it is 2-30% of the
    image, at least 70% rectangular, and sits in an edge band
    (top/bottom/left/right 25%) with its thin dimension <= 30%.
    Central character parts are deliberately NOT returned.
    Returns a list of Region (0..1000 scale).
    """
    small = image.convert("RGBA").resize((100, 100), Image.LANCZOS)
    px = small.load()
    quant = {}
    for y in range(100):
        for x in range(100):
            r, g, b, a = px[x, y]
            if a <= 10:
                continue
            quant[(x, y)] = (r // 32, g // 32, b // 32)
    visited = set()
    regions = []
    for start in list(quant.keys()):
        if start in visited:
            continue
        colour = quant[start]
        stack = [start]
        visited.add(start)
        cells = []
        while stack:
            cx, cy = stack.pop()
            cells.append((cx, cy))
            for nx, ny in ((cx + 1, cy), (cx - 1, cy), (cx, cy + 1), (cx, cy - 1)):
                if (nx, ny) in quant and (nx, ny) not in visited and quant[(nx, ny)] == colour:
                    visited.add((nx, ny))
                    stack.append((nx, ny))
        size = len(cells)
        if size < 200 or size > 3000:
            continue
        xs = [c[0] for c in cells]
        ys = [c[1] for c in cells]
        x0, x1, y0, y1 = min(xs), max(xs) + 1, min(ys), max(ys) + 1
        bw, bh = x1 - x0, y1 - y0
        if size < (bw * bh) * 0.7:
            continue
        in_band = (y0 <= 5 or y1 >= 95 or x0 <= 5 or x1 >= 95) and (bh <= 30 or bw <= 30)
        if not in_band:
            continue
        regions.append(Region(x0 * 10, y0 * 10, x1 * 10, y1 * 10))
    return regions[:3]


def detect_common_logo_region(images: list) -> Region | None:
    """Find a logo band common to a whole pack (raster only).

    Resizes every image to 100x100; a pixel is 'common' if it is
    opaque in all images and its RGB varies by < 12 across the pack.
    If an edge band (top/bottom/left/right, 5-30% thick) is > 70%
    common, it is the pack's fixed logo band — regardless of colour
    or character. Returns a Region in 0..1000 scale, or None.
    """
    if len(images) < 3:
        return None
    smalls = []
    for img in images:
        smalls.append(img.convert("RGBA").resize((100, 100), Image.LANCZOS))
    common = [[False] * 100 for _ in range(100)]
    for y in range(100):
        for x in range(100):
            pixels = [s.getpixel((x, y)) for s in smalls]
            if any(p[3] <= 50 for p in pixels):
                continue
            ok = True
            for c in range(3):
                vals = [p[c] for p in pixels]
                if max(vals) - min(vals) >= 12:
                    ok = False
                    break
            common[y][x] = ok
    row_frac = [sum(common[y]) / 100.0 for y in range(100)]
    col_frac = [sum(common[y][x] for y in range(100)) / 100.0 for x in range(100)]

    def _band(fracs, from_start: bool):
        count = 0
        for v in (fracs if from_start else list(reversed(fracs))):
            if v > 0.7:
                count += 1
            else:
                break
        return count

    candidates = []
    top = _band(row_frac, True)
    if 5 <= top <= 30:
        candidates.append((top * 100, Region(0, 0, 1000, top * 10)))
    bottom = _band(row_frac, False)
    if 5 <= bottom <= 30:
        candidates.append((bottom * 100, Region(0, 1000 - bottom * 10, 1000, 1000)))
    left = _band(col_frac, True)
    if 5 <= left <= 30:
        candidates.append((left * 100, Region(0, 0, left * 10, 1000)))
    right = _band(col_frac, False)
    if 5 <= right <= 30:
        candidates.append((right * 100, Region(1000 - right * 10, 0, 1000, 1000)))
    if not candidates:
        return None
    candidates.sort(key=lambda t: -t[0])
    return candidates[0][1]


def is_skin_like(r: int, g: int, b: int) -> bool:
    """Heuristic only: warm, red-dominant, mid-bright pixels are left alone."""
    return r > 95 and r > g > b and (r - b) > 20 and (r - g) < 90


def limited_redesign(image: "Image.Image", region: Region | None, intensity: int) -> "Image.Image":
    """Mode B: clean (if a region is given) + mild saturation shift outside it.

    intensity 0..100 maps to a small saturation factor; skin-like pixels and
    fully transparent pixels are never shifted.
    """
    from PIL import ImageEnhance
    base = clean_region(image, region) if region is not None else image.convert("RGBA")
    if intensity <= 0:
        return base
    factor = 1.0 + (intensity / 100.0) * 0.35
    enhanced = ImageEnhance.Color(base.convert("RGB")).enhance(factor).convert("RGBA")
    # Restore original alpha and skin-like pixels from the cleaned base.
    px_base = base.load()
    px_new = enhanced.load()
    w, h = base.size
    region_box = region.pixels(w, h) if region is not None else None
    for y in range(h):
        for x in range(w):
            r, g, b, a = px_base[x, y]
            if a == 0 or is_skin_like(r, g, b):
                px_new[x, y] = (r, g, b, a)
            else:
                nr, ng, nb, _ = px_new[x, y]
                px_new[x, y] = (nr, ng, nb, a)
    if region_box:
        # The cleaned region itself must not be re-saturated.
        patch = base.crop(region_box)
        enhanced.paste(patch, region_box)
    return enhanced
