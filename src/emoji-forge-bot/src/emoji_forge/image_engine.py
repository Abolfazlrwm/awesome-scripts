"""Static image engine: Pillow only, alpha preserved, 2D compositing only.

No claim of real 3D matching is made anywhere: logo placement is a 2D
overlay with optional glow, scaled to the base. Perspective/3D matching
is explicitly out of scope for v0.1.
"""
import io
import os

from PIL import Image, ImageDraw, ImageFilter, ImageFont

from .brand import hex_to_rgb, shape_persian_text
from .cleaning import is_skin_like


# Raster text fonts (v9): user-selectable via BrandProfile.font_style.
# Each style lists candidate files in preference order; the first one
# that exists wins, else PIL's bitmap default is used (as before).
FONT_STYLES = {
    "bold_sans": [
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf",
    ],
    "serif": [
        "/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf",
        "/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf",
    ],
    "mono": [
        "/usr/share/fonts/truetype/dejavu/DejaVuSansMono-Bold.ttf",
        "/usr/share/fonts/truetype/liberation/LiberationMono-Bold.ttf",
    ],
}


def load_font(font_style: str | None, size: int):
    """Truetype font for a style at `size`, or PIL's default font."""
    for path in FONT_STYLES.get(font_style or "bold_sans", []):
        try:
            return ImageFont.truetype(path, size=size)
        except Exception:
            continue
    return ImageFont.load_default()


def load_image(data: bytes) -> Image.Image:
    return Image.open(io.BytesIO(data)).convert("RGBA")


def to_square_100(image: Image.Image) -> Image.Image:
    """Custom-emoji output: pad to square with transparency, resize to 100x100."""
    w, h = image.size
    side = max(w, h)
    canvas = Image.new("RGBA", (side, side), (0, 0, 0, 0))
    canvas.paste(image, ((side - w) // 2, (side - h) // 2))
    return canvas.resize((100, 100), Image.LANCZOS)


def to_sticker_512(image: Image.Image) -> Image.Image:
    """Sticker output: longest side exactly 512, aspect preserved."""
    w, h = image.size
    scale = 512 / max(w, h)
    return image.resize((max(1, int(w * scale)), max(1, int(h * scale))), Image.LANCZOS)


def gradient_palette(base_hex: str) -> list:
    """Build a 5-stop spectrum from one base colour.

    Stops: dark, deep, base, light, pale — all in the base hue, so
    picking red gives dark red, red, light red/pink, pale pink, not a
    random rainbow. Returns list of (r, g, b) 0..255 tuples.
    """
    import colorsys
    from .brand import hex_to_rgb
    r, g, b = [c / 255.0 for c in hex_to_rgb(base_hex)]
    h, s, v = colorsys.rgb_to_hsv(r, g, b)
    stops = [
        (h, min(1.0, s * 1.1), v * 0.35),
        (h, s, v * 0.65),
        (h, s, v),
        (h, s * 0.85, v + (1 - v) * 0.45),
        (h, s * 0.6, v + (1 - v) * 0.85),
    ]
    out = []
    for hh, ss, vv in stops:
        rr, gg, bb = colorsys.hsv_to_rgb(hh, ss, vv)
        out.append((int(rr * 255), int(gg * 255), int(bb * 255)))
    return out


def gradient_palette_hex(base_hex: str) -> list:
    return ["#%02X%02X%02X" % c for c in gradient_palette(base_hex)]


def _interp_stops(stops, t: float):
    """Interpolate a list of (r,g,b) stops at t in [0,1]."""
    if len(stops) == 1:
        return stops[0]
    pos = t * (len(stops) - 1)
    i = min(len(stops) - 2, int(pos))
    f = pos - i
    a, b = stops[i], stops[i + 1]
    return tuple(int(a[c] + (b[c] - a[c]) * f) for c in range(3))


def neutralize_gradient(image: Image.Image, base_hex: str) -> Image.Image:
    """Strip colours, then rebuild with the base colour's spectrum.

    Luminance -> spectrum: dark pixels get the dark stop, bright
    pixels get the pale stop. Alpha preserved exactly.
    """
    stops = gradient_palette(base_hex)
    gray = image.convert("L")
    luts = []
    for channel in range(3):
        lut = []
        for v in range(256):
            lut.append(_interp_stops(stops, v / 255.0)[channel])
        luts.append(lut)
    r = gray.point(luts[0])
    g = gray.point(luts[1])
    b = gray.point(luts[2])
    out = Image.merge("RGB", (r, g, b)).convert("RGBA")
    out.putalpha(image.split()[3])
    return out


def neutralize_duotone(image: Image.Image, primary_hex: str, secondary_hex: str) -> Image.Image:
    """Strip colours completely, then rebuild with the user's colours.

    Step 1: luminance (grey) keeps only shape/light, all hue is gone.
    Step 2: duotone — dark pixels become darkened primary, light pixels
    become secondary (or lightened primary if secondary equals primary).
    Alpha is preserved exactly, so transparent areas stay transparent.
    Skin is NOT protected here on purpose: strip mode neutralises the
    whole pack, by the user's own philosophy.
    """
    from .brand import hex_to_rgb
    pr, pg, pb = hex_to_rgb(primary_hex)
    sr, sg, sb = hex_to_rgb(secondary_hex)
    if (sr, sg, sb) == (pr, pg, pb):
        sr, sg, sb = (pr + (255 - pr) * 3 // 5, pg + (255 - pg) * 3 // 5, pb + (255 - pb) * 3 // 5)
    dark = (int(pr * 0.65), int(pg * 0.65), int(pb * 0.65))
    light = (sr, sg, sb)
    gray = image.convert("L")
    dark_img = Image.new("RGB", image.size, dark)
    light_img = Image.new("RGB", image.size, light)
    tinted = Image.composite(light_img, dark_img, gray).convert("RGBA")
    tinted.putalpha(image.split()[3])
    return tinted


def recolor_non_skin(image: Image.Image, target_hex: str, strength: float) -> Image.Image:
    """Shift hue of non-skin, non-transparent pixels toward target_hex.

    strength 0..1. Luminance is roughly preserved by blending the original
    pixel with the target colour instead of flat-replacing it.
    """
    if strength <= 0:
        return image.copy()
    tr, tg, tb = hex_to_rgb(target_hex)
    out = image.copy()
    px = out.load()
    for y in range(out.height):
        for x in range(out.width):
            r, g, b, a = px[x, y]
            if a == 0 or is_skin_like(r, g, b):
                continue
            px[x, y] = (
                int(r + (tr - r) * strength),
                int(g + (tg - g) * strength),
                int(b + (tb - b) * strength),
                a,
            )
    return out


def _position_box(base_w: int, base_h: int, logo_w: int, logo_h: int, position: str, margin: int):
    if position == "center":
        return (base_w - logo_w) // 2, (base_h - logo_h) // 2
    if position == "top":
        return (base_w - logo_w) // 2, margin
    if position == "bottom":
        return (base_w - logo_w) // 2, base_h - logo_h - margin
    if position == "top-left":
        return margin, margin
    if position == "top-right":
        return base_w - logo_w - margin, margin
    if position == "bottom-left":
        return margin, base_h - logo_h - margin
    return base_w - logo_w - margin, base_h - logo_h - margin  # bottom-right


def overlay_logo(image: Image.Image, logo: Image.Image, position: str, size_pct: int,
                 glow_hex: str | None = None, glow_strength: float = 0.0) -> Image.Image:
    """2D logo overlay (not 3D). Same transform on every frame => no logo jitter."""
    out = image.copy()
    target_w = max(8, int(out.width * size_pct / 100))
    scale = target_w / logo.width
    logo_resized = logo.resize((target_w, max(1, int(logo.height * scale))), Image.LANCZOS)
    margin = max(4, out.width // 50)
    x, y = _position_box(out.width, out.height, logo_resized.width, logo_resized.height, position, margin)
    if glow_hex and glow_strength > 0:
        gr, gg, gb = hex_to_rgb(glow_hex)
        glow = Image.new("RGBA", out.size, (0, 0, 0, 0))
        glow_layer = Image.new("RGBA", logo_resized.size, (gr, gg, gb, 255))
        glow_layer.putalpha(logo_resized.split()[3].point(lambda a: int(a * glow_strength)))
        glow.paste(glow_layer, (x, y))
        glow = glow.filter(ImageFilter.GaussianBlur(radius=6))
        out = Image.alpha_composite(out, glow)
    out.paste(logo_resized, (x, y), logo_resized)
    return out


def overlay_name(image: Image.Image, name: str, primary_hex: str,
                 font_style: str | None = None) -> tuple[Image.Image, str | None]:
    """Draw brand name as a bottom band. Returns (image, warning_or_None).

    Persian names without reshaper/bidi installed are NOT rendered;
    a warning is returned so the caller reports it honestly.
    """
    if not name:
        return image, None
    text, ok = shape_persian_text(name)
    if not ok:
        return image, "نام فارسی بدون نصب arabic-reshaper و python-bidi روی تصویر رسم نشد تا حروف جدا جدا نشوند."
    out = image.copy()
    draw = ImageDraw.Draw(out)
    font = load_font(font_style, size=max(12, out.width // 10))
    # Bottom band, semi-transparent dark, for readability on any background.
    band_h = max(18, out.height // 5)
    band = Image.new("RGBA", (out.width, band_h), (0, 0, 0, 140))
    out.paste(band, (0, out.height - band_h), band)
    draw = ImageDraw.Draw(out)
    r, g, b = hex_to_rgb(primary_hex)
    bbox = draw.textbbox((0, 0), text, font=font)
    tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
    draw.text(((out.width - tw) // 2, out.height - band_h + (band_h - th) // 2),
              text, font=font, fill=(255, 255, 255, 255))
    return out, None


def overlay_watermark(image: Image.Image, text: str, position: str = "bottom-left",
                      opacity: int = 35, font_style: str | None = None) -> tuple[Image.Image, str | None]:
    """Tiny 2D watermark text in a safe-margin corner. Returns (image, warning)."""
    if not text or opacity <= 0:
        return image, None
    shaped, ok = shape_persian_text(text)
    if not ok:
        return image, "واترمارک فارسی بدون نصب arabic-reshaper و python-bidi رسم نشد."
    out = image.copy()
    font = load_font(font_style, size=max(10, out.width // 22))
    layer = Image.new("RGBA", out.size, (0, 0, 0, 0))
    draw = ImageDraw.Draw(layer)
    bbox = draw.textbbox((0, 0), shaped, font=font)
    tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
    margin = max(4, out.width // 50)
    x, y = _position_box(out.width, out.height, tw, th, position, margin)
    draw.text((x, y), shaped, font=font, fill=(255, 255, 255, int(255 * opacity / 100)))
    return Image.alpha_composite(out, layer), None


def save_png(image: Image.Image, path: str) -> None:
    os.makedirs(os.path.dirname(path), exist_ok=True)
    image.convert("RGBA").save(path, format="PNG", optimize=True)


def save_webp(image: Image.Image, path: str, quality: int = 90) -> None:
    os.makedirs(os.path.dirname(path), exist_ok=True)
    image.convert("RGBA").save(path, format="WEBP", quality=quality, method=6)
