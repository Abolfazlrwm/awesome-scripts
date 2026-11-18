"""Brand profile: storable, HEX-validated, Persian-friendly fields.

Persian text rendering note: Pillow alone does NOT shape Persian/Arabic
letters correctly. If arabic_reshaper + python-bidi are installed they are
used; otherwise brand-name overlay for Persian text is skipped with an
honest warning instead of producing disconnected letters.
"""
import re
from dataclasses import asdict, dataclass

HEX_RE = re.compile(r"^#[0-9A-Fa-f]{6}$")
POSITIONS = {
    "center", "top", "bottom", "top-left", "top-right",
    "bottom-left", "bottom-right",
}


def validate_hex(value: str) -> str:
    value = (value or "").strip()
    if not HEX_RE.match(value):
        raise ValueError(f"کد رنگ نامعتبر است: {value!r} — باید مثل #0F2A1D باشد.")
    return value.upper()


def hex_to_rgb(value: str):
    value = validate_hex(value)
    return tuple(int(value[i:i + 2], 16) for i in (1, 3, 5))


@dataclass
class BrandProfile:
    brand_name: str = ""
    logo_path: str = ""
    primary_hex: str = "#0F2A1D"
    secondary_hex: str = "#D8F3DC"
    glow_hex: str = "#74C69D"
    lighting_style: str = "soft"  # soft | vivid | neon — affects glow strength only
    effects: list | None = None
    logo_position: str = "bottom-right"
    logo_size_pct: int = 25  # percent of base width, 10..60
    fidelity: int = 80  # 0..100, loyalty to original
    redesign_intensity: int = 20  # 0..100, limited redesign (mode B)
    watermark_text: str = ""
    watermark_opacity: int = 35  # 0..100
    watermark_position: str = "bottom-left"
    gradient_enabled: bool = False  # spectrum mode: base colour -> 5 shades
    full_recolor: bool = False  # strip: only when True are ALL colours
    # neutralised/rebuilt (which also changes hair/character colours).
    # Default False = character preserved: only the logo is removed and
    # the user's name/logo/watermark are added.
    font_style: str = "bold_sans"  # raster text font: bold_sans | serif | mono
    logo_size_mode: str = "fit"  # fit = contain-fit inside the old logo
    # slot (v8 rule, the owner's latest scale instruction — DEFAULT);
    # fill = legacy fill-the-box sizing (v5 behaviour).

    def validate(self) -> list:
        problems = []
        for field_name in ("primary_hex", "secondary_hex", "glow_hex"):
            try:
                validate_hex(getattr(self, field_name))
            except ValueError as exc:
                problems.append(str(exc))
        if self.logo_position not in POSITIONS:
            problems.append(f"محل لوگو نامعتبر است: {self.logo_position}")
        if self.watermark_position not in POSITIONS:
            problems.append(f"محل واترمارک نامعتبر است: {self.watermark_position}")
        if not 0 <= self.watermark_opacity <= 100:
            problems.append("شفافیت واترمارک باید بین ۰ تا ۱۰۰ باشد.")
        if not 10 <= self.logo_size_pct <= 60:
            problems.append("اندازه لوگو باید بین ۱۰ تا ۶۰ درصد باشد.")
        if not 0 <= self.fidelity <= 100:
            problems.append("وفاداری باید بین ۰ تا ۱۰۰ باشد.")
        if not 0 <= self.redesign_intensity <= 100:
            problems.append("شدت بازطراحی باید بین ۰ تا ۱۰۰ باشد.")
        if self.font_style not in ("bold_sans", "serif", "mono"):
            problems.append(f"فونت نامعتبر است: {self.font_style}")
        if self.logo_size_mode not in ("fit", "fill"):
            problems.append(f"حالت اندازه لوگو نامعتبر است: {self.logo_size_mode}")
        return problems

    def to_dict(self) -> dict:
        data = asdict(self)
        data["effects"] = list(self.effects or [])
        return data

    @classmethod
    def from_dict(cls, data: dict) -> "BrandProfile":
        data = dict(data or {})
        data.pop("effects", None)
        profile = cls(**{k: v for k, v in data.items() if k in cls.__dataclass_fields__})
        profile.effects = list((data or {}).get("effects") or [])
        return profile


def shape_persian_text(text: str) -> tuple[str, bool]:
    """Return (renderable_text, shaped_ok). shaped_ok False means: do not render
    Persian text, it would come out disconnected."""
    if not text:
        return "", True
    is_persian = any("\u0600" <= ch <= "\u06FF" for ch in text)
    if not is_persian:
        return text, True
    try:
        import arabic_reshaper
        from bidi.algorithm import get_display
        return get_display(arabic_reshaper.reshape(text)), True
    except Exception:
        return text, False
