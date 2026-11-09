"""Premium UI layer (Bot API 9.4): coloured inline buttons + custom
(premium) emojis in the bot's own appearance.

Two cosmetic fields are used:
- InlineKeyboardButton.style: "primary" (blue) / "success" (green) /
  "danger" (red); omitted = the client's default look.
- icon_custom_emoji_id: a custom emoji rendered before the button
  text. Telegram honours these in messages the bot sends directly
  when the bot OWNER has Telegram Premium (verified live for this
  owner). Message texts carry premium emojis as HTML
  <tg-emoji emoji-id="...">ALT</tg-emoji>.

EVERY id below was taken verbatim from the owner's harvested,
Telegram-verified pool:
- ~/workspace/imperium/app/data/premium_emoji_ids.json (curated map)
- ~/workspace/imperium/data_harvest/pool.json (full pool)
A test asserts each shipped id exists in one of those two files, so
an invented or mistyped id cannot slip in. Categories with no
custom alt in the pool simply have no entry here — the plain emoji
in the button text remains.

Design policy (restrained — one visual per button, one accent per
keyboard; rewritten after the owner's screenshot review):
- A button carries AT MOST ONE visual: either a premium icon
  (icon_custom_emoji_id) OR the leading emoji in its text, never
  both. btn() enforces this: when an icon is set, the text's
  leading emoji is stripped automatically (strip_leading_emoji).
- Icons are used ONLY where the semantics match exactly (pack,
  publish, palette, edit, preview, stats, users, settings, check,
  cross, home, back, sparkles-for-studio, literal upload/download).
  When in doubt: no icon — the plain emoji in the text stays.
- Colour is scarce: a keyboard has AT MOST ONE PRIMARY button (its
  single main CTA); SUCCESS only on explicit confirm/approve,
  DANGER only on cancel/delete/reject in confirm/admin contexts;
  everything else is the client's clean default style.

Fallback safety lives in telegram_api: if Telegram rejects a send
over a custom-emoji/icon field, the send is retried once with the
icons stripped (and HTML premium tags reduced to their alt char).
"""
import copy
import html
import re

PRIMARY = "primary"
SUCCESS = "success"
DANGER = "danger"
STYLES = (PRIMARY, SUCCESS, DANGER)

# key -> {"alt": fallback char, "id": custom_emoji_id}
PREMIUM = {
    "brand":    {"alt": "👑", "id": "5357107601584693888"},
    "pack":     {"alt": "📦", "id": "5246780105182686360"},
    "palette":  {"alt": "🎨", "id": "5310039132297242441"},
    "font":     {"alt": "✍️", "id": "5238156910363950406"},
    "logo":     {"alt": "🖼", "id": "5262517101578443800"},
    "publish":  {"alt": "🚀", "id": "5195033767969839232"},
    "stats":    {"alt": "📊", "id": "5370727273592803309"},
    "users":    {"alt": "👥", "id": "5915556996215476302"},
    "settings": {"alt": "⚙️", "id": "5372925742797574389"},
    "home":     {"alt": "🏠", "id": "5312486108309757006"},
    "back":     {"alt": "⬅️", "id": "5875082500023258804"},
    "check":    {"alt": "✅", "id": "5237699328843200968"},
    "cross":    {"alt": "❌", "id": "5370640017037218875"},
    "sparkles": {"alt": "✨", "id": "5350720349560416260"},
    "star":     {"alt": "⭐", "id": "5267500801240092311"},
    "fire":     {"alt": "🔥", "id": "5312241539987020022"},
    "preview":  {"alt": "👁", "id": "5373076646473520062"},
    "edit":     {"alt": "✏️", "id": "5370914882059261730"},
    "help":     {"alt": "❓", "id": "5377316857231450742"},
    "download": {"alt": "⬇️", "id": "4961156159999313072"},
    "upload":   {"alt": "⬆️", "id": "4961018660916299377"},
    "studio":   {"alt": "🎛", "id": "5875431869842985304"},
    "diamond":  {"alt": "💎", "id": "5309958691854754293"},
    "warning":  {"alt": "⚠️", "id": "5881702736843511327"},
}


def icon(key: str):
    """The custom_emoji_id for a category key, or None."""
    entry = PREMIUM.get(key)
    return entry["id"] if entry else None


def alt(key: str) -> str:
    """The plain fallback char for a category key ('' if unknown)."""
    entry = PREMIUM.get(key)
    return entry["alt"] if entry else ""


def pe(key: str) -> str:
    """HTML premium-emoji snippet for a message text (parse_mode
    HTML): <tg-emoji emoji-id="ID">ALT</tg-emoji>. Unknown keys
    degrade to an empty string."""
    entry = PREMIUM.get(key)
    if not entry:
        return ""
    return (f'<tg-emoji emoji-id="{entry["id"]}">'
            f'{html.escape(entry["alt"])}</tg-emoji>')


# One leading emoji cluster: pictographs, symbols, dingbats, arrows
# and flags, including VS16 (U+FE0F) and ZWJ-joined sequences
# (e.g. 🧑‍💼). Deliberately excludes letters, digits and punctuation
# so a label that merely starts with text is never eaten.
_EMOJI_CHAR = (
    "[\U0001F000-\U0001FAFF"
    "\u2190-\u21FF\u2300-\u23FF\u2600-\u27BF"
    "\u2934-\u2935\u2B00-\u2BFF\u3030\u303D\u3297\u3299]"
)
_LEADING_EMOJI_RE = re.compile(
    r"^(?:" + _EMOJI_CHAR + r"(?:\uFE0F|‍" + _EMOJI_CHAR + r"\uFE0F?)*)"
    r"\s*"
)


def strip_leading_emoji(text: str) -> str:
    """Remove ONE leading emoji cluster (plus following spaces)
    from a button label: "✅ پردازش کامل" -> "پردازش کامل",
    "🧑‍💼 بررسی" -> "بررسی". Text that does not start with an emoji
    is returned unchanged."""
    if not text:
        return text
    return _LEADING_EMOJI_RE.sub("", text, count=1)


def btn(text: str, *, callback_data=None, url=None, web_app=None,
        style=None, icon=None, **extra) -> dict:
    """Build one inline button dict. `style`/`icon` are emitted ONLY
    when set (icon may be a PREMIUM key or a raw custom_emoji_id).
    Callback data, url and web_app pass through untouched.

    One-visual rule: when an icon is set, the text's leading emoji
    is stripped automatically, so a button can never show the same
    pictogram twice (premium icon + emoji in the label)."""
    icon_id = PREMIUM.get(icon, {}).get("id", icon) if icon else None
    if icon_id:
        text = strip_leading_emoji(text)
    button = {"text": text}
    if callback_data is not None:
        button["callback_data"] = callback_data
    if url is not None:
        button["url"] = url
    if web_app is not None:
        button["web_app"] = web_app
    if style:
        if style not in STYLES:
            raise ValueError(f"unknown button style: {style!r}")
        button["style"] = style
    if icon_id:
        button["icon_custom_emoji_id"] = icon_id
    button.update(extra)
    return button


def strip_icons(markup):
    """Deep copy of a reply markup with every icon_custom_emoji_id
    removed (styles and everything else kept). None passes through."""
    if not markup:
        return markup
    cleaned = copy.deepcopy(markup)
    for row in cleaned.get("inline_keyboard") or []:
        for button in row:
            if isinstance(button, dict):
                button.pop("icon_custom_emoji_id", None)
    return cleaned


_TG_EMOJI_RE = re.compile(r"<tg-emoji[^>]*>(.*?)</tg-emoji>", re.DOTALL)


def strip_premium_tags(text: str) -> str:
    """Reduce HTML <tg-emoji> tags to their plain alt character —
    the text fallback when parse_mode HTML is dropped."""
    if not text:
        return text
    return _TG_EMOJI_RE.sub(lambda m: m.group(1), text)
