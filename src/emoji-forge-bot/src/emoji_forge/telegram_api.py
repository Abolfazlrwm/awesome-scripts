"""Thin Telegram Bot API client (requests, persistent session, retries).

Only the methods this project actually uses. No token is ever logged:
errors are returned with the token redacted by the caller via security.redact.
"""
import json
import re
import time

import requests

from .errors import AccessError, ExternalError
from .premium import strip_icons, strip_premium_tags
from .security import redact


def _is_cosmetic_error(exc: Exception) -> bool:
    """True when Telegram's error blames a cosmetic premium field
    (custom emoji / button icon) — those must never break a
    functional message; the caller retries once, stripped."""
    text = str(exc).lower()
    return "custom emoji" in text or "custom_emoji" in text or "icon" in text


def _plain_fallback_text(text: str) -> str:
    """HTML-intended text reduced to plain: premium tags become
    their alt char, remaining simple tags are dropped."""
    return re.sub(r"</?[a-zA-Z][^>]*>", "", strip_premium_tags(text or ""))


class TelegramApi:
    def __init__(self, token: str, timeout: int = 60):
        if not token or ":" not in token:
            raise AccessError("توکن ربات تنظیم نشده است؛ EMOJI_FORGE_TOKEN را در .env بگذارید.")
        self._token = token
        self.base = f"https://api.telegram.org/bot{token}"
        self.file_base = f"https://api.telegram.org/file/bot{token}"
        self.session = requests.Session()
        self.timeout = timeout

    # Telegram 400 descriptions that are TRANSIENT in practice
    # (proven during the 200-pack replace runs: the same proven-good
    # file is accepted on a later attempt). Everything else with a
    # 400 is a real error and surfaces immediately.
    TRANSIENT_400_MARKERS = ("file must be non-empty", "wrong file type")

    @staticmethod
    def _rewind_files(files):
        """Rewind uploaded file handles before a retry — requests
        leaves them at EOF after the first attempt."""
        if not files:
            return
        for value in files.values():
            handle = value[1] if isinstance(value, (tuple, list)) and len(value) > 1 else value
            try:
                handle.seek(0)
            except Exception:
                pass

    def _call(self, method: str, payload=None, files=None, retries: int = 4):
        """Resilient wrapper (v10): up to `retries` attempts with
        exponential backoff 2/4/8s. Retries on network errors
        (requests.RequestException), on HTTP/error 429 honouring
        Telegram's retry_after (sleep = max(backoff, retry_after)),
        and on the transient 400 descriptions in TRANSIENT_400_MARKERS.
        Non-transient Telegram errors (401/409/other 400s) surface
        immediately."""
        url = f"{self.base}/{method}"
        last_error = ""
        for attempt in range(max(1, retries)):
            backoff = 2 * (2 ** attempt)  # 2, 4, 8, ...
            try:
                self._rewind_files(files)
                if files:
                    resp = self.session.post(url, data=payload, files=files, timeout=self.timeout)
                else:
                    resp = self.session.post(url, json=payload or {}, timeout=self.timeout)
                data = resp.json()
                if data.get("ok"):
                    return data["result"]
                desc = data.get("description", "خطای ناشناخته تلگرام")
                code = data.get("error_code")
                if code == 401:
                    raise AccessError("توکن ربات معتبر نیست (401).")
                if code == 409:
                    raise ExternalError("نمونه دیگری از ربات در حال اجراست (409).")
                retry_after = 0
                params = data.get("parameters") or {}
                if isinstance(params, dict):
                    retry_after = int(params.get("retry_after") or 0)
                is_429 = code == 429 or getattr(resp, "status_code", None) == 429
                is_transient_400 = code == 400 and any(
                    marker in desc.lower() for marker in self.TRANSIENT_400_MARKERS)
                if (is_429 or is_transient_400) and attempt < retries - 1:
                    last_error = desc
                    time.sleep(max(backoff, retry_after))
                    continue
                raise ExternalError(f"تلگرام خطا داد: {desc}")
            except (AccessError, ExternalError):
                raise
            except Exception as exc:
                last_error = str(exc)
                if attempt < retries - 1:
                    time.sleep(backoff)
        raise ExternalError(f"ارتباط با تلگرام برقرار نشد: {last_error[:200]}")

    # -- reads ---------------------------------------------------------
    def get_me(self):
        return self._call("getMe")

    def get_updates(self, offset=None, timeout=30):
        payload = {"timeout": timeout, "allowed_updates": ["message", "callback_query"]}
        if offset is not None:
            payload["offset"] = offset
        return self._call("getUpdates", payload, retries=1)

    def get_custom_emoji_stickers(self, ids: list):
        return self._call("getCustomEmojiStickers", {"custom_emoji_ids": json.dumps(ids)})

    def get_sticker_set(self, name: str):
        return self._call("getStickerSet", {"name": name})

    def get_file(self, file_id: str):
        return self._call("getFile", {"file_id": file_id})

    def download_file(self, file_path: str) -> bytes:
        url = f"{self.file_base}/{file_path}"
        resp = self.session.get(url, timeout=self.timeout)
        resp.raise_for_status()
        return resp.content

    # -- sends ---------------------------------------------------------
    def send_message(self, chat_id, text: str, reply_markup=None,
                     parse_mode: str | None = None):
        payload = {"chat_id": chat_id, "text": text}
        if reply_markup:
            payload["reply_markup"] = json.dumps(reply_markup)
        if parse_mode:
            payload["parse_mode"] = parse_mode
        try:
            return self._call("sendMessage", payload)
        except ExternalError as exc:
            if not _is_cosmetic_error(exc):
                raise
            fallback = {"chat_id": chat_id, "text": _plain_fallback_text(text)}
            stripped = strip_icons(reply_markup)
            if stripped:
                fallback["reply_markup"] = json.dumps(stripped)
            return self._call("sendMessage", fallback)

    def send_document(self, chat_id, path: str, caption: str = ""):
        with open(path, "rb") as fh:
            files = {"document": (path.split("/")[-1], fh)}
            payload = {"chat_id": chat_id, "caption": caption}
            return self._call("sendDocument", payload, files=files)

    def edit_message_text(self, chat_id, message_id, text: str, reply_markup=None,
                          parse_mode: str | None = None):
        payload = {"chat_id": chat_id, "message_id": message_id, "text": text}
        if reply_markup:
            payload["reply_markup"] = json.dumps(reply_markup)
        if parse_mode:
            payload["parse_mode"] = parse_mode
        try:
            return self._call("editMessageText", payload, retries=2)
        except ExternalError as exc:
            if not _is_cosmetic_error(exc):
                raise
            fallback = {"chat_id": chat_id, "message_id": message_id,
                        "text": _plain_fallback_text(text)}
            stripped = strip_icons(reply_markup)
            if stripped:
                fallback["reply_markup"] = json.dumps(stripped)
            return self._call("editMessageText", fallback, retries=2)

    def send_photo(self, chat_id, path: str, caption: str = ""):
        with open(path, "rb") as fh:
            files = {"photo": (path.split("/")[-1], fh)}
            payload = {"chat_id": chat_id, "caption": caption}
            return self._call("sendPhoto", payload, files=files)

    def send_sticker(self, chat_id, sticker: str):
        """Send an existing sticker by file_id (preview copies)."""
        return self._call("sendSticker", {"chat_id": chat_id, "sticker": sticker})

    def answer_callback(self, callback_query_id: str, text: str = ""):
        return self._call("answerCallbackQuery",
                          {"callback_query_id": callback_query_id, "text": text})

    # -- bot identity (v9 branding; owner-triggered via /apply_branding) --
    def set_my_name(self, name: str):
        return self._call("setMyName", {"name": name})

    def set_my_short_description(self, short_description: str):
        return self._call("setMyShortDescription",
                          {"short_description": short_description})

    def set_my_description(self, description: str):
        return self._call("setMyDescription", {"description": description})

    def set_my_commands(self, commands: list):
        return self._call("setMyCommands", {"commands": json.dumps(commands)})

    # -- publishing a NEW set owned by the bot's user (official route) --
    def upload_sticker_file(self, user_id: int, path: str, sticker_format: str):
        with open(path, "rb") as fh:
            files = {"sticker": (path.split("/")[-1], fh)}
            payload = {"user_id": user_id, "sticker_format": sticker_format}
            return self._call("uploadStickerFile", payload, files=files)

    def create_new_sticker_set(self, user_id: int, name: str, title: str,
                               stickers: list, sticker_type: str = "custom_emoji",
                               sticker_format: str | None = None):
        payload = {
            "user_id": user_id, "name": name, "title": title,
            "stickers": json.dumps(stickers), "sticker_type": sticker_type,
        }
        if sticker_format:
            payload["sticker_format"] = sticker_format
        return self._call("createNewStickerSet", payload)

    def add_sticker_to_set(self, user_id: int, name: str, sticker: dict):
        payload = {"user_id": user_id, "name": name, "sticker": json.dumps(sticker)}
        return self._call("addStickerToSet", payload)

    def safe_error(self, exc: Exception) -> str:
        return redact(str(exc), self._token)
