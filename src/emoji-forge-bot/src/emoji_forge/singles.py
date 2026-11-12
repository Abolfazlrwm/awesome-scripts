"""Single-emoji editing (v10): edit ONE animated emoji, not a pack.

Pure helpers live here so they are testable without Telegram:
- registry palette/font access (emoji_forge.registry is the expanded
  colour/font catalogue; until it lands, spec-shaped fallbacks below
  are used so the feature works either way),
- paginated colour/font inline keyboards (pure functions of the
  palette/font lists),
- the personal-set name candidates for delivery,
- process_single(): routes one TGS through the existing engines
  (v5 for colour/name, the v8 logo path for a traced logo image).

Bot-side flow, quota and delivery live in bot.py; this module never
talks to Telegram.
"""
import os
import threading
from urllib.parse import urlencode

from .brand import BrandProfile
from .premium import DANGER, PRIMARY, btn

ACTIONS = ("color", "name", "logo", "all")


class LogoSlotNotFound(ValueError):
    """The user's logo image could not be placed into the old logo's
    slot. Raised instead of silently delivering a fallback: the v8
    pipeline degrades to the v5 TEXT path when it finds no slot,
    which stamped the profile brand name as stray text (owner report
    2026-10-30). The bot catches this and routes the single to
    advanced processing."""


def _logo_was_placed(out_bytes: bytes) -> bool:
    """True when the output actually contains the placed logo asset
    (a layer or top-level shape group named 'Brand Logo'/'LOGO') —
    the same signal the v8 pipeline itself uses to detect its own
    fallback."""
    from .animation_engine import load_tgs
    doc = load_tgs(out_bytes)
    layers = list(doc.get("layers") or []) + [
        layer for asset in doc.get("assets") or []
        for layer in (asset.get("layers") or [])]
    for layer in layers:
        if str(layer.get("nm", "")) in ("Brand Logo", "LOGO"):
            return True
        for group in layer.get("shapes") or []:
            if isinstance(group, dict) and str(
                    group.get("nm", "")) in ("Brand Logo", "LOGO"):
                return True
    return False

SINGLES_SET_TITLE = "ایموجی‌های من | Abolfazl"

# Telegram rejects a createNewStickerSet whose name is invalid or
# already occupied; these substrings in the (redacted) error text mark
# "try the next candidate name" failures.
SET_NAME_ERROR_MARKERS = ("STICKERSET_INVALID", "occupied", "NAME_OCCUPIED")

# ------------------------------------------------------------------
# Registry access (palette + fonts), with fallbacks.
#
# registry.py spec (built in parallel): PALETTE is a list of
# {"fa": <category label>, "colors": [{"name_fa": ..., "hex": ...}]},
# FONTS is a list of {"id": ..., "fa": ..., "persian": bool} and
# font_by_id(id) -> path. Everything below codes against that spec
# and degrades to the built-in tables when the module is absent.
# ------------------------------------------------------------------

_FALLBACK_PALETTE = [
    {"fa": "رنگ‌های اصلی", "colors": [
        {"name_fa": "سبز تیره", "hex": "#0F2A1D"},
        {"name_fa": "سبز", "hex": "#2D6A4F"},
        {"name_fa": "سبز روشن", "hex": "#74C69D"},
        {"name_fa": "کرمی روشن", "hex": "#D8F3DC"},
        {"name_fa": "مشکی", "hex": "#000000"},
        {"name_fa": "سفید", "hex": "#FFFFFF"},
        {"name_fa": "طلایی", "hex": "#C9A227"},
        {"name_fa": "آبی", "hex": "#1D4ED8"},
        {"name_fa": "قرمز", "hex": "#B91C1C"},
        {"name_fa": "قرمز روشن", "hex": "#FF0000"},
        {"name_fa": "نارنجی", "hex": "#FF8000"},
        {"name_fa": "زرد", "hex": "#FFFF00"},
        {"name_fa": "سبز نئونی", "hex": "#00FF00"},
        {"name_fa": "آبی آسمانی", "hex": "#0000FF"},
        {"name_fa": "فیروزه‌ای", "hex": "#00FFFF"},
        {"name_fa": "صورتی", "hex": "#FF00FF"},
        {"name_fa": "بنفش", "hex": "#8000FF"},
    ]},
]


def load_palette() -> list:
    """The colour catalogue: registry.PALETTE when available."""
    try:
        from . import registry
        palette = getattr(registry, "PALETTE", None)
        if palette:
            return palette
    except Exception:
        pass
    return _FALLBACK_PALETTE


def flatten_palette(palette: list) -> list:
    """Flatten the catalogued palette to [{"name_fa", "hex", "category_fa"}].

    Tolerant of the spec shape ({fa, colors:[...]}), of flat colour
    dicts, and of (hex, label) tuples, so a registry revision never
    breaks the keyboard builder."""
    out = []
    for entry in palette or []:
        if isinstance(entry, dict) and isinstance(entry.get("colors"), list):
            category = entry.get("fa", "")
            for color in entry["colors"]:
                if isinstance(color, dict) and color.get("hex"):
                    out.append({"name_fa": color.get("name_fa", ""),
                                "hex": color["hex"],
                                "category_fa": category})
                elif isinstance(color, (list, tuple)) and len(color) >= 2:
                    out.append({"name_fa": color[1], "hex": color[0],
                                "category_fa": category})
        elif isinstance(entry, dict) and entry.get("hex"):
            out.append({"name_fa": entry.get("name_fa", ""),
                        "hex": entry["hex"], "category_fa": ""})
        elif isinstance(entry, (list, tuple)) and len(entry) >= 2:
            out.append({"name_fa": entry[1], "hex": entry[0],
                        "category_fa": ""})
    return out


def load_fonts() -> list:
    """The font catalogue: registry.FONTS when available, else the
    animation engine's curated styles that exist on this machine."""
    try:
        from . import registry
        fonts = getattr(registry, "FONTS", None)
        if fonts:
            return fonts
    except Exception:
        pass
    try:
        from .animation_engine import FONT_STYLES
        return [{"id": style_id, "fa": desc, "persian": False}
                for style_id, (path, desc) in FONT_STYLES.items()
                if os.path.exists(path)]
    except Exception:
        return []


def font_label(font_id: str, fonts: list | None = None) -> str:
    """Persian label for a stored font_id ('خودکار' when unset)."""
    if not font_id:
        return "خودکار"
    for font in (fonts if fonts is not None else load_fonts()):
        if font.get("id") == font_id:
            return font.get("fa", font_id)
    return font_id


def font_path_for(font_id: str) -> str | None:
    """Resolve a stored font_id to a TTF path, or None (= automatic)."""
    if not font_id:
        return None
    try:
        from . import registry
        path = registry.font_by_id(font_id)
        if path and os.path.exists(path):
            return path
    except Exception:
        pass
    try:
        from .animation_engine import FONT_STYLES
        entry = FONT_STYLES.get(font_id)
        if entry and os.path.exists(entry[0]):
            return entry[0]
    except Exception:
        pass
    return None


# ------------------------------------------------------------------
# Paginated keyboards (pure functions).
# ------------------------------------------------------------------

def _page_slice(items: list, page: int, per_page: int):
    total_pages = max(1, -(-len(items) // per_page))
    page = min(max(0, page), total_pages - 1)
    return items[page * per_page:(page + 1) * per_page], page, total_pages


def _nav_row(page: int, total_pages: int, page_prefix: str):
    if total_pages <= 1:
        return None
    row = []
    if page > 0:
        row.append(btn("▶️ قبلی", callback_data=f"{page_prefix}:{page - 1}"))
    row.append(btn(f"صفحه {page + 1} از {total_pages}",
                   callback_data="single:noop"))
    if page < total_pages - 1:
        row.append(btn("◀️ بعدی", callback_data=f"{page_prefix}:{page + 1}"))
    return row


def build_color_keyboard(colors: list, page: int = 0, per_page: int = 12) -> dict:
    """Paginated colour picker over a FLAT colour list
    ([{"name_fa", "hex"}, ...]). 3 buttons per row."""
    chunk, page, total_pages = _page_slice(list(colors), page, per_page)
    rows = []
    row = []
    for color in chunk:
        row.append(btn(f"{color.get('name_fa', '')} {color['hex']}",
                       callback_data=f"single:color:{color['hex']}"))
        if len(row) == 3:
            rows.append(row)
            row = []
    if row:
        rows.append(row)
    nav = _nav_row(page, total_pages, "single:colors")
    if nav:
        rows.append(nav)
    rows.append([btn("⌨️ کد رنگ دلخواه", callback_data="single:colorhex")])
    rows.append([btn("🌈 گرادیان‌های آماده", callback_data="single:gradients")])
    rows.append([btn("⬅️ بازگشت", callback_data="single:menu", icon="back")])
    return {"inline_keyboard": rows}


def build_gradient_keyboard(gradients: list, page: int = 0, per_page: int = 8) -> dict:
    """Gradient preset picker (v10): choosing one sets the brand
    profile's primary = color1 and secondary = color2. 1 per row so
    the two hexes stay readable."""
    chunk, page, total_pages = _page_slice(list(gradients), page, per_page)
    rows = []
    for grad in chunk:
        rows.append([btn(f"{grad.get('fa', grad.get('id', ''))} "
                         f"{grad.get('color1', '')} ← {grad.get('color2', '')}",
                         callback_data=f"single:grad:{grad.get('id', '')}")])
    nav = _nav_row(page, total_pages, "single:grads")
    if nav:
        rows.append(nav)
    rows.append([btn("⬅️ بازگشت", callback_data="single:menu", icon="back")])
    return {"inline_keyboard": rows}


def load_gradients() -> list:
    """The gradient catalogue: registry.GRADIENTS when available."""
    try:
        from . import registry
        grads = getattr(registry, "GRADIENTS", None)
        if grads:
            return grads
    except Exception:
        pass
    return []


def build_font_keyboard(fonts: list, page: int = 0, per_page: int = 8) -> dict:
    """Paginated font picker over [{"id", "fa", "persian"}, ...].
    2 buttons per row; Persian-capable fonts are marked."""
    chunk, page, total_pages = _page_slice(list(fonts), page, per_page)
    rows = []
    row = []
    for font in chunk:
        label = font.get("fa", font.get("id", ""))
        if font.get("persian"):
            label += " (فارسی)"
        row.append(btn(label,
                       callback_data=f"single:font:{font.get('id', '')}"))
        if len(row) == 2:
            rows.append(row)
            row = []
    if row:
        rows.append(row)
    nav = _nav_row(page, total_pages, "single:fonts")
    if nav:
        rows.append(nav)
    rows.append([btn("⬅️ بازگشت", callback_data="single:menu", icon="back")])
    return {"inline_keyboard": rows}


# Mini App entry point: REMOVED by owner order (2026-11-01,
# «مینی اپو حذف کن فعلا»). The helpers below stay dormant so the
# feature can be re-enabled later; no button is built from them.
MINIAPP_ENABLED = False


def _studio_base_url() -> str | None:
    """The bare web-app URL exactly as stored in data/miniapp_url.txt
    (None when the file is missing/empty)."""
    try:
        path = os.path.join(os.path.dirname(os.path.abspath(__file__)),
                            "..", "..", "data", "miniapp_url.txt")
        with open(path, encoding="utf-8") as fh:
            url = fh.read().strip()
        return url or None
    except Exception:
        return None


def studio_url(extra: dict | None = None) -> str | None:
    """Public URL of the bot's web-app page, stored in
    data/miniapp_url.txt so it can be rotated without a code change.
    Currently UNUSED (the entry point is disabled — see
    MINIAPP_ENABLED); kept for reversibility.

    `extra` carries bot-side context — keys mode / pid / name /
    color / font — appended as a query string (empty values
    dropped)."""
    base = _studio_base_url()
    if not base:
        return None
    if extra:
        params = {k: v for k, v in extra.items() if v not in (None, "")}
        if params:
            sep = "&" if "?" in base else "?"
            return base + sep + urlencode(params)
    return base


def studio_button_row(extra: dict | None = None) -> list | None:
    """Dormant stub: the web-app button was removed from the bot by
    owner order (2026-11-01). Always returns None so no menu can
    render an entry point; re-enabling means restoring the button
    construction here behind MINIAPP_ENABLED."""
    if not MINIAPP_ENABLED:
        return None
    url = studio_url(extra)
    if not url:
        return None
    return [btn("🎨 ویرایش پیشرفته (رنگ، فونت، لوگو، پیش‌نمایش)",
                web_app={"url": url}, icon="sparkles")]


def single_action_keyboard(brand: BrandProfile, font_fa: str = "خودکار",
                           has_logo: bool = False,
                           studio_extra: dict | None = None) -> dict:
    """The 'what should happen to this emoji?' menu. Colour/font/logo
    rows show the CURRENT profile values and open their pickers, so
    the user tweaks the saved profile inline before firing an action.
    `studio_extra` is accepted for call-site compatibility and is
    currently unused (the web-app row was removed)."""
    studio_row = studio_button_row(studio_extra)
    return {"inline_keyboard": [
        [btn("🎨 فقط تغییر رنگ", callback_data="single:act:color", icon="palette"),
         btn("✍️ اسم/برند", callback_data="single:act:name")],
        [btn("🖼 لوگو", callback_data="single:act:logo"),
         btn("✨ همه (رنگ+اسم+لوگو)", callback_data="single:act:all", style=PRIMARY)],
        [btn(f"🎨 رنگ من: {brand.primary_hex}", callback_data="single:colors:0", icon="palette")],
        [btn(f"🔤 فونت: {font_fa}", callback_data="single:fonts:0")],
        [btn("🖼 لوگوی من: دارد ✔" if has_logo else "🖼 لوگوی من: ندارد — افزودن",
             callback_data="single:logo")],
        *([studio_row] if studio_row else []),
        [btn("❌ لغو", callback_data="single:cancel", style=DANGER, icon="cross")],
    ]}


# ------------------------------------------------------------------
# Personal singles set naming (pure).
# ------------------------------------------------------------------

def single_set_candidates(user_id: int, bot_username: str,
                          stored_name: str | None = None) -> list:
    """Ordered full set names to try for the user's personal singles
    set: the stored winner first, then tzs_<id>, then suffixed
    fallbacks for when Telegram rejects a name (invalid/occupied)."""
    from .publish import build_set_name
    names = []
    if stored_name:
        names.append(stored_name)
    for slug in (f"tzs_{user_id}", f"tzs_{user_id}_x2", f"tzs_{user_id}_x3"):
        full = build_set_name(slug, bot_username)
        if full not in names:
            names.append(full)
    return names


# ------------------------------------------------------------------
# Processing one TGS.
# ------------------------------------------------------------------

_PROCESS_LOCK = threading.Lock()


def _guard_or_raise(out_bytes: bytes) -> bytes:
    """Importer guard gate (v10): run fix_importer_risks over the
    processed output and re-validate. Returns the (possibly repaired)
    bytes; raises ValueError when blocking issues remain, so a file
    Telegram's importer would reject is never delivered. Bytes that
    do not parse as TGS at all pass through untouched — the bot's
    delivery gate validates again and rejects those loudly."""
    from .animation_engine import dump_tgs, load_tgs
    from .importer_guard import fix_importer_risks
    from .validate_tgs import validate_tgs_bytes
    try:
        doc = load_tgs(out_bytes)
    except Exception:
        return out_bytes
    doc, _report = fix_importer_risks(doc)
    if _report["tr_added"] or _report["paint_stripped"] or _report["vertices_removed"]:
        out_bytes = dump_tgs(doc)
    issues, _warnings = validate_tgs_bytes(out_bytes, 1)
    if issues:
        raise ValueError("خروجی پس از گارد ایمپورتر هنوز رد می‌شود: "
                         + "; ".join(issues)[:300])
    return out_bytes


def _theme_hue(data: bytes):
    """Best-effort theme hue of this single file (None = engine default)."""
    try:
        from .animation_engine import load_tgs
        from .pipeline import detect_pack_theme_hue
        return detect_pack_theme_hue([load_tgs(data)])
    except Exception:
        return None


def process_single(data: bytes, brand: BrandProfile, action: str,
                   font_id: str | None = None,
                   logo_data: bytes | None = None):
    """Edit ONE TGS. Returns (output_bytes, warnings).

    Routing (the existing engines, nothing re-implemented):
    - colour/name -> v5 (process_tgs_v5) with recolor limited to the
      actions that asked for colour, so «اسم/برند» never recolours;
    - logo (with a real logo image) -> the pipeline's v8 path, which
      traces the image and places it contain-fit in the old logo
      slot (and falls back to the v5 text path when it cannot).
    The chosen font is applied through animation_engine's font
    override (a FONT_STYLES/registry id) for the duration of the
    call; a lock serialises singles so the process-global override
    can never leak into a pack job running at the same moment."""
    if action not in ACTIONS:
        raise ValueError(f"عملیات ویرایش تکی نامعتبر است: {action}")
    want_color = action in ("color", "all")
    want_name = action in ("name", "all")
    want_logo = action in ("logo", "all")
    if want_logo and not logo_data:
        raise ValueError("برای این عملیات اول باید لوگو بفرستید.")
    from dataclasses import replace
    eff_brand = replace(
        brand, brand_name=brand.brand_name if want_name else "")
    theme_hue = _theme_hue(data) if want_color else None
    with _PROCESS_LOCK:
        override_set = False
        try:
            if font_id:
                from .animation_engine import set_font_override
                set_font_override(font_id)
                override_set = True
            if want_logo:
                from .logo_vector import trace_logo_image
                if trace_logo_image(logo_data) is None:
                    raise ValueError(
                        "لوگوی تصویری قابل برداری‌سازی نبود؛ "
                        "یک PNG شفاف واضح‌تر بفرستید.")
                from .pipeline import process_tgs
                # Owner rule (2026-10-30): when a logo IMAGE is
                # given, the brand name must never be stamped as a
                # fallback text, and a run that placed no logo must
                # not be delivered as success. Suppress the name for
                # this call and verify the placement afterwards.
                logo_brand = replace(brand, brand_name="")
                out, warnings = process_tgs(
                    data, logo_brand, set(), "v8", logo_data=logo_data,
                    theme_hue_deg=theme_hue)
                if not _logo_was_placed(out):
                    raise LogoSlotNotFound(
                        "اسلات لوگوی قبلی پیدا نشد و لوگو جایگزین نشد.")
                return _guard_or_raise(out), warnings
            from .v5 import process_tgs_v5
            out, warnings, _placement = process_tgs_v5(
                data, eff_brand, set(), 0, None,
                theme_hue_deg=theme_hue, recolor=want_color)
            return _guard_or_raise(out), warnings
        finally:
            if override_set:
                from .animation_engine import clear_font_override
                clear_font_override()
