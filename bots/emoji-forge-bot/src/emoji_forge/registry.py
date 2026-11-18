"""Shared FONTS + COLOURS registry for Emoji Forge.

Single source of truth for the brand-text fonts and the colour
palette: the TGS engine (animation_engine.FONT_STYLES is extended
from FONTS), the bot menus and the Mini App (which reads
assets/registry.json, exported from this module) all use the same
ids, so a font/colour picked anywhere means the same thing
everywhere.

Font files live in assets/fonts/ (static TTFs instanced/downloaded
from Google Fonts at the bold/display weights listed per family).
Ids are stable slugs — never rename one, the Mini App and saved
brand profiles refer to fonts by id.
"""
import json
import os

FONTS_DIR = os.path.normpath(os.path.join(
    os.path.dirname(os.path.abspath(__file__)), "..", "..",
    "assets", "fonts"))

# Ordered: cute_bold first (Ehsan's standing default for brand
# names), cute_round second, then the display/Latin families, then
# the Persian/Arabic families (persian=True — their cmap also
# covers U+0600-U+06FF).
FONTS = [
    {"id": "cute_bold", "fa": "کیوت و پررنگ (فردوکا)",
     "file": "Fredoka-Bold.ttf", "family": "Fredoka", "persian": False},
    {"id": "cute_round", "fa": "کیوت و گرد (بالو)",
     "file": "Baloo2-ExtraBold.ttf", "family": "Baloo 2", "persian": False},
    {"id": "luckiest_guy", "fa": "کارتونی و پرانرژی (لاکیست گای)",
     "file": "LuckiestGuy-Regular.ttf", "family": "Luckiest Guy",
     "persian": False},
    {"id": "titan_one", "fa": "درشت و قدرتمند (تایتان وان)",
     "file": "TitanOne-Regular.ttf", "family": "Titan One",
     "persian": False},
    {"id": "chewy", "fa": "نرم و بامزه (چویی)",
     "file": "Chewy-Regular.ttf", "family": "Chewy", "persian": False},
    {"id": "bungee", "fa": "جسور و خیابانی (بانجی)",
     "file": "Bungee-Regular.ttf", "family": "Bungee", "persian": False},
    {"id": "rubik", "fa": "مدرن و پررنگ (روبیک)",
     "file": "Rubik-ExtraBold.ttf", "family": "Rubik", "persian": False},
    {"id": "montserrat", "fa": "هندسی و فوق‌پررنگ (مونت‌سرات)",
     "file": "Montserrat-Black.ttf", "family": "Montserrat",
     "persian": False},
    {"id": "poppins", "fa": "گرد و مدرن (پاپینز)",
     "file": "Poppins-ExtraBold.ttf", "family": "Poppins",
     "persian": False},
    {"id": "archivo_black", "fa": "سنگین و مشکی (آرچیو بلک)",
     "file": "ArchivoBlack-Regular.ttf", "family": "Archivo Black",
     "persian": False},
    {"id": "righteous", "fa": "نمایشی و شیک (رایچس)",
     "file": "Righteous-Regular.ttf", "family": "Righteous",
     "persian": False},
    {"id": "lilita_one", "fa": "بامزه و درشت (لیلیتا وان)",
     "file": "LilitaOne-Regular.ttf", "family": "Lilita One",
     "persian": False},
    {"id": "orbitron", "fa": "فضایی و آینده‌نگر (اوربیترون)",
     "file": "Orbitron-ExtraBold.ttf", "family": "Orbitron",
     "persian": False},
    {"id": "audiowide", "fa": "دیجیتال و عریض (آدیوواید)",
     "file": "Audiowide-Regular.ttf", "family": "Audiowide",
     "persian": False},
    {"id": "russo_one", "fa": "محکم و زاویه‌دار (روسو وان)",
     "file": "RussoOne-Regular.ttf", "family": "Russo One",
     "persian": False},
    {"id": "rubik_mono_one", "fa": "تک‌عرض و درشت (روبیک مونو)",
     "file": "RubikMonoOne-Regular.ttf", "family": "Rubik Mono One",
     "persian": False},
    {"id": "rubik_bubbles", "fa": "حبابی و شاد (روبیک بابلز)",
     "file": "RubikBubbles-Regular.ttf", "family": "Rubik Bubbles",
     "persian": False},
    {"id": "press_start_2p", "fa": "پیکسلی و رترو (پرس استارت)",
     "file": "PressStart2P-Regular.ttf", "family": "Press Start 2P",
     "persian": False},
    {"id": "vazirmatn", "fa": "فارسی و فوق‌پررنگ (وزیرمتن)",
     "file": "Vazirmatn-Black.ttf", "family": "Vazirmatn",
     "persian": True},
    {"id": "cairo", "fa": "عربی و پررنگ (قاهره)",
     "file": "Cairo-Black.ttf", "family": "Cairo", "persian": True},
    {"id": "almarai", "fa": "عربی و تمیز (المرعی)",
     "file": "Almarai-ExtraBold.ttf", "family": "Almarai",
     "persian": True},
    {"id": "noto_kufi_arabic", "fa": "کوفی و هندسی (نوتو کوفی عربی)",
     "file": "NotoKufiArabic-ExtraBold.ttf",
     "family": "Noto Kufi Arabic", "persian": True},
    {"id": "lemonada", "fa": "عربی و دوستانه (لیمونادا)",
     "file": "Lemonada-Bold.ttf", "family": "Lemonada", "persian": True},
    {"id": "el_messiri", "fa": "عربی و لطیف (المسیری)",
     "file": "ElMessiri-Bold.ttf", "family": "El Messiri",
     "persian": True},
    {"id": "changa", "fa": "عربی و کشیده (چانگا)",
     "file": "Changa-ExtraBold.ttf", "family": "Changa", "persian": True},
]

# Colour palette grouped by family; hexes are unique across the
# whole palette. Metallic entries are flat approximations of
# gold/silver/copper tones (TGS fills are flat colours).
PALETTE = [
    {"fa": "قرمزها", "colors": [
        {"name_fa": "قرمز تیره", "hex": "#8E0000"},
        {"name_fa": "قرمز آجری", "hex": "#B71C1C"},
        {"name_fa": "قرمز", "hex": "#D32F2F"},
        {"name_fa": "قرمز روشن", "hex": "#E53935"},
        {"name_fa": "قرمز مرجانی", "hex": "#EF5350"},
        {"name_fa": "قرمز یاقوتی", "hex": "#FF1744"},
        {"name_fa": "قرمز کم‌رنگ", "hex": "#FF8A80"},
    ]},
    {"fa": "نارنجی‌ها", "colors": [
        {"name_fa": "نارنجی سوخته", "hex": "#E65100"},
        {"name_fa": "نارنجی تیره", "hex": "#EF6C00"},
        {"name_fa": "نارنجی", "hex": "#F57C00"},
        {"name_fa": "نارنجی روشن", "hex": "#FB8C00"},
        {"name_fa": "کهربایی", "hex": "#FF9800"},
        {"name_fa": "نارنجی طلایی", "hex": "#FFA726"},
        {"name_fa": "نارنجی کرمی", "hex": "#FFCC80"},
    ]},
    {"fa": "زردها", "colors": [
        {"name_fa": "زرد طلایی تیره", "hex": "#F9A825"},
        {"name_fa": "زرد طلایی", "hex": "#FBC02D"},
        {"name_fa": "زرد درخشان", "hex": "#FFD600"},
        {"name_fa": "زرد", "hex": "#FFEB3B"},
        {"name_fa": "زرد روشن", "hex": "#FFEE58"},
        {"name_fa": "زرد کم‌رنگ", "hex": "#FFF176"},
        {"name_fa": "زرد کرمی", "hex": "#FFFDE7"},
    ]},
    {"fa": "سبزها", "colors": [
        {"name_fa": "سبز جنگلی", "hex": "#1B5E20"},
        {"name_fa": "سبز تیره", "hex": "#2E7D32"},
        {"name_fa": "سبز", "hex": "#388E3C"},
        {"name_fa": "سبز روشن", "hex": "#43A047"},
        {"name_fa": "سبز برگی", "hex": "#66BB6A"},
        {"name_fa": "سبز زمردی", "hex": "#00C853"},
        {"name_fa": "سبز کم‌رنگ", "hex": "#A5D6A7"},
    ]},
    {"fa": "فیروزه‌ای‌ها", "colors": [
        {"name_fa": "فیروزه‌ای تیره", "hex": "#004D40"},
        {"name_fa": "سبزآبی تیره", "hex": "#00695C"},
        {"name_fa": "فیروزه‌ای", "hex": "#00897B"},
        {"name_fa": "فیروزه‌ای روشن", "hex": "#00BFA5"},
        {"name_fa": "فیروزه‌ای ملایم", "hex": "#4DB6AC"},
        {"name_fa": "نعنایی درخشان", "hex": "#1DE9B6"},
        {"name_fa": "فیروزه‌ای کم‌رنگ", "hex": "#B2DFDB"},
    ]},
    {"fa": "آبی‌ها", "colors": [
        {"name_fa": "آبی سلطنتی", "hex": "#0D47A1"},
        {"name_fa": "آبی تیره", "hex": "#1565C0"},
        {"name_fa": "آبی", "hex": "#1E88E5"},
        {"name_fa": "آبی روشن", "hex": "#42A5F5"},
        {"name_fa": "آبی آسمانی", "hex": "#64B5F6"},
        {"name_fa": "آبی یخی", "hex": "#82B1FF"},
        {"name_fa": "آبی برقی", "hex": "#2962FF"},
    ]},
    {"fa": "بنفش‌ها", "colors": [
        {"name_fa": "بنفش تیره", "hex": "#4A148C"},
        {"name_fa": "بنفش سلطنتی", "hex": "#6A1B9A"},
        {"name_fa": "بنفش", "hex": "#7B1FA2"},
        {"name_fa": "بنفش روشن", "hex": "#8E24AA"},
        {"name_fa": "ارغوانی", "hex": "#AB47BC"},
        {"name_fa": "بنفش برقی", "hex": "#7C4DFF"},
        {"name_fa": "بنفش کم‌رنگ", "hex": "#B388FF"},
    ]},
    {"fa": "صورتی‌ها", "colors": [
        {"name_fa": "صورتی تیره", "hex": "#880E4F"},
        {"name_fa": "سرخابی تیره", "hex": "#AD1457"},
        {"name_fa": "سرخابی", "hex": "#C2185B"},
        {"name_fa": "صورتی", "hex": "#D81B60"},
        {"name_fa": "صورتی پررنگ", "hex": "#E91E63"},
        {"name_fa": "صورتی روشن", "hex": "#EC407A"},
        {"name_fa": "صورتی آب‌نباتی", "hex": "#FF80AB"},
    ]},
    {"fa": "خنثی‌ها", "colors": [
        {"name_fa": "مشکی زغالی", "hex": "#212121"},
        {"name_fa": "خاکستری تیره", "hex": "#424242"},
        {"name_fa": "خاکستری", "hex": "#616161"},
        {"name_fa": "خاکستری روشن", "hex": "#9E9E9E"},
        {"name_fa": "خاکستری خیلی روشن", "hex": "#E0E0E0"},
        {"name_fa": "سفید", "hex": "#FFFFFF"},
        {"name_fa": "قهوه‌ای", "hex": "#795548"},
    ]},
    {"fa": "نئونی‌ها", "colors": [
        {"name_fa": "سبز نئونی", "hex": "#39FF14"},
        {"name_fa": "لیمویی نئونی", "hex": "#CCFF00"},
        {"name_fa": "آبی نئونی", "hex": "#00F5FF"},
        {"name_fa": "ماژنتای نئونی", "hex": "#FF00FF"},
        {"name_fa": "قرمز نئونی", "hex": "#FF073A"},
        {"name_fa": "صورتی نئونی", "hex": "#FE59C2"},
        {"name_fa": "نارنجی نئونی", "hex": "#FFAA00"},
    ]},
    {"fa": "پاستلی‌ها", "colors": [
        {"name_fa": "صورتی پاستلی", "hex": "#FFCDD2"},
        {"name_fa": "صورتی خیلی کم‌رنگ", "hex": "#FCE4EC"},
        {"name_fa": "بنفش پاستلی", "hex": "#E1BEE7"},
        {"name_fa": "یاسی پاستلی", "hex": "#D1C4E9"},
        {"name_fa": "آبی پاستلی", "hex": "#BBDEFB"},
        {"name_fa": "سبز پاستلی", "hex": "#C8E6C9"},
        {"name_fa": "زرد پاستلی", "hex": "#FFF9C4"},
    ]},
    {"fa": "متالیک‌ها", "colors": [
        {"name_fa": "طلایی", "hex": "#FFD700"},
        {"name_fa": "طلایی قدیمی", "hex": "#C9A227"},
        {"name_fa": "شامپاینی", "hex": "#F7E7CE"},
        {"name_fa": "نقره‌ای", "hex": "#C0C0C0"},
        {"name_fa": "نقره‌ای مات", "hex": "#9AA5B1"},
        {"name_fa": "مسی", "hex": "#B87333"},
        {"name_fa": "برنزی", "hex": "#CD7F32"},
    ]},
    # v10 expansion: +28 colours (112 total). New families extend
    # the existing neon/pastel/metallic ones and add earthy tones;
    # every hex stays unique across the whole palette.
    {"fa": "نئون‌های فوق‌درخشان", "colors": [
        {"name_fa": "فیروزه‌ای نئونی تیره", "hex": "#00E5FF"},
        {"name_fa": "بنفش نئونی", "hex": "#AA00FF"},
        {"name_fa": "زرد نئونی", "hex": "#EEFF41"},
        {"name_fa": "سبز لیمویی نئونی", "hex": "#76FF03"},
        {"name_fa": "آبی الکتریکی نئونی", "hex": "#2979FF"},
        {"name_fa": "هلویی نئونی", "hex": "#FF6E40"},
        {"name_fa": "یاسی نئونی", "hex": "#EA80FC"},
    ]},
    {"fa": "پاستل‌های ملایم", "colors": [
        {"name_fa": "هلویی پاستلی", "hex": "#FFE0B2"},
        {"name_fa": "نعنایی پاستلی", "hex": "#B9F6CA"},
        {"name_fa": "آبی آسمانی پاستلی", "hex": "#B3E5FC"},
        {"name_fa": "صورتی غباری پاستلی", "hex": "#F8BBD0"},
        {"name_fa": "کرم وانیلی پاستلی", "hex": "#FFF3E0"},
        {"name_fa": "خاکستری مرواریدی پاستلی", "hex": "#ECEFF1"},
        {"name_fa": "سبز مریم‌گلی پاستلی", "hex": "#DCEDC8"},
    ]},
    {"fa": "متالیک‌های براق", "colors": [
        {"name_fa": "طلایی رزگلد", "hex": "#E0A899"},
        {"name_fa": "پلاتینی", "hex": "#E5E4E2"},
        {"name_fa": "فولادی آبی", "hex": "#4682B4"},
        {"name_fa": "برنجی", "hex": "#B5A642"},
        {"name_fa": "مسی روشن", "hex": "#DA8A67"},
        {"name_fa": "تیتانیومی", "hex": "#878681"},
        {"name_fa": "طلایی قهوه‌ای", "hex": "#996515"},
    ]},
    {"fa": "رنگ‌های خاکی", "colors": [
        {"name_fa": "خاکی شنی", "hex": "#D2B48C"},
        {"name_fa": "سفالی", "hex": "#CC7357"},
        {"name_fa": "زیتونی", "hex": "#808000"},
        {"name_fa": "خردلی", "hex": "#D4A017"},
        {"name_fa": "کاکائویی", "hex": "#4B2E2A"},
        {"name_fa": "بژ تیره", "hex": "#A68A64"},
        {"name_fa": "سبز خزه‌ای", "hex": "#5B7553"},
    ]},
]

# Two-colour gradient presets (v10). Choosing one sets the brand
# profile's primary_hex = color1 and secondary_hex = color2 (and
# enables gradient mode where the flow supports it). Ids are stable
# slugs, like the font ids.
GRADIENTS = [
    {"id": "sunset", "fa": "غروب (نارنجی به صورتی)",
     "color1": "#FF8000", "color2": "#FF00FF"},
    {"id": "ocean", "fa": "اقیانوس (آبی به فیروزه‌ای)",
     "color1": "#1565C0", "color2": "#00F5FF"},
    {"id": "forest", "fa": "جنگل (سبز تیره به سبز روشن)",
     "color1": "#1B5E20", "color2": "#A5D6A7"},
    {"id": "royal", "fa": "سلطنتی (بنفش به طلایی)",
     "color1": "#4A148C", "color2": "#FFD700"},
    {"id": "fire", "fa": "آتش (قرمز به زرد)",
     "color1": "#B71C1C", "color2": "#FFD600"},
    {"id": "candy", "fa": "آب‌نباتی (صورتی به آبی پاستلی)",
     "color1": "#FF80AB", "color2": "#BBDEFB"},
    {"id": "neon_night", "fa": "شب نئونی (بنفش نئونی به آبی نئونی)",
     "color1": "#AA00FF", "color2": "#00F5FF"},
    {"id": "gold_rose", "fa": "رز طلایی (رزگلد به شامپاینی)",
     "color1": "#E0A899", "color2": "#F7E7CE"},
    {"id": "mint_sky", "fa": "نعنا و آسمان (نعنایی به آبی آسمانی)",
     "color1": "#1DE9B6", "color2": "#64B5F6"},
    {"id": "grape_soda", "fa": "انگوری (ارغوانی به بنفش کم‌رنگ)",
     "color1": "#AB47BC", "color2": "#B388FF"},
    {"id": "ember", "fa": "ذغالی (مشکی زغالی به نارنجی سوخته)",
     "color1": "#212121", "color2": "#E65100"},
    {"id": "pearl", "fa": "مرواریدی (سفید به نقره‌ای)",
     "color1": "#FFFFFF", "color2": "#C0C0C0"},
    {"id": "tropical", "fa": "استوایی (سبز زمردی به زرد)",
     "color1": "#00C853", "color2": "#FFEB3B"},
    {"id": "lavender_dream", "fa": "رویای اسطوخودوس (یاسی به صورتی پاستلی)",
     "color1": "#D1C4E9", "color2": "#FFCDD2"},
]

_GRADIENTS_BY_ID = {entry["id"]: entry for entry in GRADIENTS}


def gradients() -> list:
    """All gradient presets, in registry order."""
    return list(GRADIENTS)


def gradient_by_id(gradient_id):
    """One gradient preset dict, or None for an unknown id."""
    return _GRADIENTS_BY_ID.get(gradient_id or "")

_FONTS_BY_ID = {entry["id"]: entry for entry in FONTS}


def font_by_id(font_id):
    """Absolute path of a registry font file, or None when the id is
    unknown or the file is missing on disk."""
    entry = _FONTS_BY_ID.get(font_id or "")
    if entry is None:
        return None
    path = os.path.join(FONTS_DIR, entry["file"])
    return path if os.path.exists(path) else None


def palette_flat():
    """Flat [(name_fa, hex), ...] over every palette family, in
    registry order."""
    return [(color["name_fa"], color["hex"])
            for family in PALETTE for color in family["colors"]]


def registry_data():
    """The registry as plain JSON-able data (Mini App payload)."""
    return {"fonts": FONTS, "palette": PALETTE, "gradients": GRADIENTS}


def write_registry_json(path=None):
    """Export assets/registry.json for the Mini App; returns the path."""
    if path is None:
        path = os.path.normpath(os.path.join(
            os.path.dirname(os.path.abspath(__file__)), "..", "..",
            "assets", "registry.json"))
    with open(path, "w", encoding="utf-8") as fh:
        json.dump(registry_data(), fh, ensure_ascii=False, indent=2)
    return path
