"""Security helpers: admin gate, file validation, secret redaction, cleanup.

Uploaded files are NEVER executed. Only their bytes are read by Pillow/FFmpeg.
"""
import os
import re
import time

ALLOWED_EXTENSIONS = {".png", ".webp", ".tgs", ".webm"}
# Magic prefixes: PNG, RIFF/WEBP, gzip (TGS), EBML (WEBM), JPEG is rejected on purpose.
MAGIC = (
    (b"\x89PNG\r\n\x1a\n", ".png"),
    (b"RIFF", ".webp"),  # WEBP check below also requires b"WEBP" at offset 8
    (b"\x1f\x8b", ".tgs"),
    (b"\x1a\x45\xdf\xa3", ".webm"),
)

_SAFE_NAME = re.compile(r"^[A-Za-z0-9._-]+$")


def redact(text: str, token: str = "") -> str:
    """Remove a token from log text. Never log the token itself."""
    if not text:
        return text
    out = text
    if token:
        out = out.replace(token, "[TOKEN]")
    # Also mask anything that looks like a bot token, just in case.
    out = re.sub(r"\d{5,}:[A-Za-z0-9_-]{20,}", "[TOKEN]", out)
    return out


def is_admin(user_id: int, admin_ids) -> bool:
    if not admin_ids:
        return False
    return user_id in admin_ids


def safe_filename(name: str) -> str:
    """Reject path traversal and weird names; return a plain file name."""
    base = os.path.basename((name or "").strip())
    if not base or base in (".", "..") or not _SAFE_NAME.match(base):
        raise ValueError(f"نام فایل نامعتبر است: {name!r}")
    return base


def sniff_format(data: bytes):
    """Return (extension, kind) from magic bytes, or (None, None).

    kind: 'static' | 'animated' | 'video' | None
    A plain JSON TGS (not gzipped) is also recognised by its content.
    """
    if not data:
        return None, None
    if data.startswith(b"\x89PNG"):
        return ".png", "static"
    if data.startswith(b"RIFF") and data[8:12] == b"WEBP":
        return ".webp", "static"
    if data.startswith(b"\x1f\x8b"):
        return ".tgs", "animated"
    if data.startswith(b"\x1a\x45\xdf\xa3"):
        return ".webm", "video"
    stripped = data.lstrip()
    if stripped.startswith(b"{") and b'"layers"' in stripped[:4096]:
        return ".tgs", "animated"
    return None, None


def validate_upload(data: bytes, declared_name: str, max_bytes: int):
    """Validate size, extension and magic. Raises ValueError on any problem."""
    if len(data) > max_bytes:
        raise ValueError(f"فایل بزرگ‌تر از حد مجاز است ({len(data)} بایت).")
    if len(data) == 0:
        raise ValueError("فایل خالی است.")
    ext, kind = sniff_format(data)
    if ext is None:
        raise ValueError("فرمت فایل شناخته نشد یا قابل‌اجرا/نامعتبر است؛ فقط PNG، WEBP، TGS و WEBM پذیرفته می‌شوند.")
    declared_ext = os.path.splitext(declared_name or "")[1].lower()
    if declared_ext and declared_ext != ext and not (declared_ext == ".jpg"):
        # Declared name is only a hint; magic wins, but a mismatch is reported.
        raise ValueError(f"پسوند اعلام‌شده ({declared_ext}) با محتوای واقعی فایل ({ext}) مطابقت ندارد.")
    if ext not in ALLOWED_EXTENSIONS:
        raise ValueError(f"پسوند {ext} مجاز نیست.")
    return ext, kind


def cleanup_old_files(root: str, max_age_seconds: int = 7 * 24 * 3600) -> int:
    """Delete files under root older than max_age_seconds. Returns count removed."""
    removed = 0
    now = time.time()
    for dirpath, _dirs, files in os.walk(root):
        for name in files:
            path = os.path.join(dirpath, name)
            try:
                if now - os.path.getmtime(path) > max_age_seconds:
                    os.remove(path)
                    removed += 1
            except OSError:
                continue
    return removed
