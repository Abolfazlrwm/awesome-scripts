"""Admin-wide settings (v9): data/admin_settings.json, atomic writes.

Only one setting exists so far, deliberately:
- credit_in_outputs (default False): when the OWNER turns it ON,
  processing jobs for users who have NOT set their own watermark get
  the standard watermark mechanism with the text "Abolfazl". When
  OFF (default), user outputs carry ONLY the user's own branding —
  the bot never forces its watermark into user artwork.
"""
import os

from .storage import read_json, write_json

DEFAULTS = {"credit_in_outputs": False}
_cache: dict = {}


def _path(data_dir: str) -> str:
    return os.path.join(data_dir, "admin_settings.json")


def get_settings(data_dir: str) -> dict:
    if data_dir not in _cache:
        raw = read_json(_path(data_dir), default=None) or {}
        merged = dict(DEFAULTS)
        merged.update({k: raw[k] for k in DEFAULTS if k in raw})
        _cache[data_dir] = merged
    return _cache[data_dir]


def set_setting(data_dir: str, key: str, value) -> dict:
    if key not in DEFAULTS:
        raise ValueError(f"تنظیم ناشناخته است: {key}")
    settings = dict(get_settings(data_dir))
    settings[key] = value
    write_json(_path(data_dir), settings)
    _cache[data_dir] = settings
    return settings
