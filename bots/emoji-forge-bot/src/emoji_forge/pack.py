"""Pack logic (pure, testable without Telegram):

- extract custom_emoji_ids from a message update (entities);
- parse pack links t.me/addemoji/<name> and t.me/addstickers/<name>;
- build a project manifest from a StickerSet-like dict;
- regular, ordered file naming.
"""
import re
from urllib.parse import urlparse

PACK_LINK_RE = re.compile(
    r"(?:t\.me|telegram\.me)/(addemoji|addstickers)/([A-Za-z0-9_]+)", re.IGNORECASE)


def extract_custom_emoji_ids(update: dict) -> list:
    """Return custom_emoji_ids from message entities (text or caption)."""
    msg = (update or {}).get("message") or {}
    ids = []
    for field in ("entities", "caption_entities"):
        for ent in msg.get(field) or []:
            if ent.get("type") == "custom_emoji" and ent.get("custom_emoji_id"):
                ids.append(ent["custom_emoji_id"])
    return ids


def parse_pack_link(text: str) -> dict | None:
    """Parse a pack link. Returns {'kind': 'emoji'|'sticker', 'name': ...} or None."""
    if not text:
        return None
    m = PACK_LINK_RE.search(text)
    if not m:
        return None
    kind = "emoji" if m.group(1).lower() == "addemoji" else "sticker"
    return {"kind": kind, "name": m.group(2)}


def sticker_kind(sticker: dict) -> str:
    if sticker.get("is_animated"):
        return "animated"
    if sticker.get("is_video"):
        return "video"
    return "static"


def sticker_extension(sticker: dict) -> str:
    kind = sticker_kind(sticker)
    return {  # extension is a hint; real files are re-checked by magic bytes
        "static": ".webp",
        "animated": ".tgs",
        "video": ".webm",
    }[kind]


def manifest_from_sticker_set(sticker_set: dict, project_id: str) -> dict:
    """Build a manifest from a getStickerSet response dict.

    Only fields the Bot API actually returns are used. If set_name is
    missing from a single sticker, the caller must ask for a link instead
    of inventing a pack name.
    """
    stickers = sticker_set.get("stickers") or []
    items = []
    for idx, st in enumerate(stickers, start=1):
        items.append({
            "index": idx,
            "file_id": st.get("file_id", ""),
            "file_unique_id": st.get("file_unique_id", ""),
            "custom_emoji_id": st.get("custom_emoji_id", ""),
            "kind": sticker_kind(st),
            "extension_hint": sticker_extension(st),
            "emoji": st.get("emoji", ""),
            "width": st.get("width"),
            "height": st.get("height"),
            "file_size": st.get("file_size"),
            "set_name": st.get("set_name", sticker_set.get("name", "")),
            "status": "listed",
            "local_path": "",
        })
    return {
        "project_id": project_id,
        "set_name": sticker_set.get("name", ""),
        "set_title": sticker_set.get("title", ""),
        "sticker_type": sticker_set.get("sticker_type", ""),
        "count": len(items),
        "items": items,
    }
