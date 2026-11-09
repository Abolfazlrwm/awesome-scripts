"""Real publishing helpers (pure, testable without Telegram).

Rules taken from the Bot API (checked 2026-10-28):
- A set name must start with a letter, contain only letters/digits/
  underscores, have no consecutive underscores, be 1-64 chars, and end
  in "_by_<bot_username>".
- A bot can only create/edit sets it creates; it cannot edit someone
  else's pack. Publishing therefore ALWAYS creates a new set.
- createNewStickerSet takes 1-50 initial stickers; emoji sets can grow
  to 200 via addStickerToSet.
- All stickers in one set must share one format (static/animated/video).

Nothing here talks to Telegram; bot.py calls TelegramApi with the
payloads built here, and reports Telegram's real answer or error.
"""
import json
import os
import re

FORMAT_BY_EXT = {".webp": "static", ".png": "static", ".tgs": "animated", ".webm": "video"}

# ------------------------------------------------------------------
# Resumable publishing checkpoint (v10).
#
# publish_progress.json lives INSIDE the project dir and records,
# per sticker index: the uploaded file_id and whether the sticker
# is fully published (inside the created set). A publish job that
# dies mid-run (crash, VM restart, network) can be re-run and SKIPS
# every sticker already recorded — no duplicate uploads, no second
# set. Writes are atomic (tmp + rename, same as storage.write_json).
# ------------------------------------------------------------------

CHECKPOINT_NAME = "publish_progress.json"


def checkpoint_path(project_root: str) -> str:
    return os.path.join(project_root, CHECKPOINT_NAME)


def load_checkpoint(project_root: str) -> dict:
    """The project checkpoint, or a fresh empty one. Shape:
    {"set_name": str|None, "set_created": bool,
     "stickers": {"<index>": {"file_name", "file_id", "published"}}}"""
    try:
        with open(checkpoint_path(project_root), encoding="utf-8") as fh:
            data = json.load(fh)
        if isinstance(data, dict) and isinstance(data.get("stickers"), dict):
            data.setdefault("set_name", None)
            data.setdefault("set_created", False)
            return data
    except (OSError, ValueError):
        pass
    return {"set_name": None, "set_created": False, "stickers": {}}


def save_checkpoint(project_root: str, checkpoint: dict) -> dict:
    path = checkpoint_path(project_root)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    tmp = path + ".tmp"
    with open(tmp, "w", encoding="utf-8") as fh:
        json.dump(checkpoint, fh, ensure_ascii=False, indent=2)
    os.replace(tmp, path)
    return checkpoint


def checkpoint_uploaded(checkpoint: dict, index: int, file_name: str,
                        file_id: str) -> dict:
    entry = checkpoint["stickers"].setdefault(str(index), {})
    entry.update({"file_name": file_name, "file_id": file_id})
    entry.setdefault("published", False)
    return checkpoint


def checkpoint_published(checkpoint: dict, indices) -> dict:
    for index in indices:
        entry = checkpoint["stickers"].setdefault(str(index), {})
        entry["published"] = True
    return checkpoint


def checkpoint_done_indices(checkpoint: dict) -> set:
    """Indices whose upload can be skipped (a file_id is recorded)."""
    return {int(idx) for idx, entry in checkpoint.get("stickers", {}).items()
            if isinstance(entry, dict) and entry.get("file_id")}


def checkpoint_published_indices(checkpoint: dict) -> set:
    return {int(idx) for idx, entry in checkpoint.get("stickers", {}).items()
            if isinstance(entry, dict) and entry.get("published")}


def manifest_emojis(project_root: str) -> dict:
    """Per-sticker emoji lists from the project's manifest.json:
    {index: [emoji, ...]}. Empty dict when no manifest exists —
    callers fall back to their in-memory manifest. This is the
    on-disk source of truth, so a resumed publish never falls back
    to a hardcoded emoji."""
    try:
        with open(os.path.join(project_root, "manifest.json"),
                  encoding="utf-8") as fh:
            manifest = json.load(fh)
    except (OSError, ValueError):
        return {}
    out = {}
    for item in manifest.get("items") or []:
        emoji = item.get("emoji")
        if item.get("index") is not None and emoji:
            out[int(item["index"])] = [emoji]
    return out


def slugify(text: str) -> str:
    slug = re.sub(r"[^A-Za-z0-9]+", "_", text or "").strip("_").lower()
    slug = re.sub(r"_+", "_", slug)
    if not slug or not slug[0].isalpha():
        slug = "forge_" + slug
    return slug


def build_set_name(project_slug: str, bot_username: str) -> str:
    """Build a valid set name, or raise ValueError with the exact problem."""
    username = (bot_username or "").strip().lstrip("@").lower()
    if not username:
        raise ValueError("نام کاربری ربات مشخص نیست؛ انتشار بدون آن ممکن نیست.")
    base = slugify(project_slug)[:30]
    name = f"{base}_by_{username}"
    if len(name) > 64:
        name = f"{base[:64 - len('_by_') - len(username)]}_by_{username}"
    if not re.fullmatch(r"[A-Za-z][A-Za-z0-9_]*", name):
        raise ValueError(f"نام ست ساخته‌شده نامعتبر است: {name}")
    if "__" in name:
        raise ValueError(f"نام ست نباید خط زیر متوالی داشته باشد: {name}")
    return name


def set_link(name: str, sticker_type: str = "custom_emoji") -> str:
    kind = "addemoji" if sticker_type == "custom_emoji" else "addstickers"
    return f"https://t.me/{kind}/{name}"


def validate_before_upload(final_files: list, final_dir: str) -> list:
    """v6 pre-upload gate: run the improved validator over every file
    and return blocking issues as (index, file_name, issues) tuples.
    Call this BEFORE any uploadStickerFile call; an empty list means
    the pack may be uploaded. One bad file must never be discovered
    after 15 successful uploads."""
    import os
    from emoji_forge.validate_tgs import validate_tgs_bytes
    blocked = []
    for name in final_files:
        with open(os.path.join(final_dir, name), "rb") as fh:
            data = fh.read()
        idx = 0
        for part in name.split("_"):
            if part.isdigit() and len(part) == 3:
                idx = int(part)
                break
        issues, _warnings = validate_tgs_bytes(data, idx)
        if issues:
            blocked.append((idx, name, issues))
    return blocked


def single_file_upload_plan(file_name: str, emoji: str = "🙂") -> dict:
    """v6 single-file re-upload: describe how one fixed file joins an
    already-created set without re-publishing the whole pack.
    Steps (run by the bot, not here): validate the single file,
    uploadStickerFile -> file_id, then addStickerToSet on the existing
    set name. No createNewStickerSet call, so the set is never
    recreated and the other 19 stickers are untouched."""
    return {
        "file_name": file_name,
        "emoji_list": [emoji],
        "steps": ["validate_single_file", "uploadStickerFile", "addStickerToSet"],
    }


def build_stickers_payload(final_files: list, emoji_by_index: dict) -> dict:
    """Validate a final-files list and build createNewStickerSet payload parts.

    final_files: sorted file names in final/. Returns
    {"format": ..., "stickers": [{"file_name":..., "format":..., "emoji_list":[...]}, ...]}
    Raises ValueError on mixed formats or empty input — never silently
    publishes a half set.
    """
    if not final_files:
        raise ValueError("فایل نهایی برای انتشار وجود ندارد؛ اول /process را کامل کنید.")
    formats = set()
    stickers = []
    for name in final_files:
        ext = os.path.splitext(name)[1].lower()
        fmt = FORMAT_BY_EXT.get(ext)
        if fmt is None:
            raise ValueError(f"فرمت فایل {name} برای انتشار پشتیبانی نمی‌شود.")
        formats.add(fmt)
        m = re.match(r".*?_(\d{3})_", name)
        index = int(m.group(1)) if m else 0
        emoji_list = emoji_by_index.get(index) or ["✨"]
        stickers.append({"file_name": name, "format": fmt, "emoji_list": emoji_list[:20]})
    if len(formats) != 1:
        raise ValueError(
            f"فایل‌های نهایی چند فرمت دارند ({', '.join(sorted(formats))})؛ "
            "یک ست تلگرام فقط یک فرمت دارد. یا خروجی‌ها را یکدست کنید، "
            "یا هر فرمت را جداگانه منتشر کنید، یا از مسیر دستی ZIP استفاده کنید.")
    return {"format": formats.pop(), "stickers": stickers}
