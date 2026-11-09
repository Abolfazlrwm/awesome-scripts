"""Format detection and Telegram output limits.

Limits (Bot API / sticker docs, checked 2026-10-28):
- sticker static: one side exactly 512px, <= 512 KiB
- sticker animated TGS: 512x512, <= 64 KiB, <= 3s, 30-60 fps
- sticker video WEBM: one side 512px, <= 256 KiB, <= 3s, <= 30 fps, no audio
- custom emoji output: exactly 100x100 (per format limits above)
"""
import gzip
import json
import re
import subprocess
from dataclasses import dataclass

from .security import sniff_format

STICKER_LIMITS = {
    "static": {"max_bytes": 512 * 1024, "long_side": 512},
    "animated": {"max_bytes": 64 * 1024, "size": (512, 512), "max_seconds": 3.0},
    "video": {"max_bytes": 256 * 1024, "long_side": 512, "max_seconds": 3.0, "max_fps": 30},
}


@dataclass
class FileInfo:
    extension: str
    kind: str  # static | animated | video | unknown
    width: int | None = None
    height: int | None = None
    size_bytes: int = 0
    fps: float | None = None
    duration_seconds: float | None = None
    frame_count: int | None = None
    tgs_layers: list | None = None

    def check_sticker_limits(self) -> list:
        problems = []
        lim = STICKER_LIMITS.get(self.kind)
        if not lim:
            problems.append("نوع فایل برای استیکر شناخته نشد.")
            return problems
        if self.size_bytes > lim["max_bytes"]:
            problems.append(f"حجم {self.size_bytes} بایت بیشتر از حد {lim['max_bytes']} بایت است.")
        if self.kind == "animated":
            if (self.width, self.height) != (512, 512):
                problems.append(f"ابعاد TGS باید 512×512 باشد، نه {self.width}×{self.height}.")
            if self.duration_seconds and self.duration_seconds > 3.0:
                problems.append("مدت TGS بیشتر از ۳ ثانیه است.")
        if self.kind == "video" and self.duration_seconds and self.duration_seconds > 3.0:
            problems.append("مدت WEBM بیشتر از ۳ ثانیه است.")
        return problems


def describe(data: bytes, filename: str = "") -> FileInfo:
    ext, kind = sniff_format(data)
    info = FileInfo(extension=ext or "", kind=kind or "unknown", size_bytes=len(data))
    if kind == "static" and ext in (".png", ".webp"):
        try:
            from PIL import Image
            import io
            with Image.open(io.BytesIO(data)) as im:
                info.width, info.height = im.size
        except Exception:
            pass
    elif kind == "animated":
        try:
            raw = gzip.decompress(data) if data.startswith(b"\x1f\x8b") else data
            doc = json.loads(raw.decode("utf-8"))
            info.width = int(doc.get("w") or 0) or None
            info.height = int(doc.get("h") or 0) or None
            fr = float(doc.get("fr") or 0)
            ip = float(doc.get("ip") or 0)
            op = float(doc.get("op") or 0)
            if fr > 0:
                info.fps = fr
                info.duration_seconds = (op - ip) / fr if op > ip else None
                info.frame_count = int(op - ip) if op > ip else None
            layers = doc.get("layers") or []
            info.tgs_layers = [str(l.get("nm", "")) for l in layers]
        except Exception:
            pass
    return info


def probe_webm(path: str) -> dict:
    """Probe a WEBM with ffmpeg -i (stderr parse). No ffprobe required.

    Returns {} when ffmpeg is missing or parsing fails — never invented numbers.
    """
    try:
        proc = subprocess.run(
            ["ffmpeg", "-hide_banner", "-i", path],
            capture_output=True, text=True, timeout=30,
        )
    except Exception:
        return {}
    text = proc.stderr or ""
    out = {}
    m = re.search(r"Stream .*Video: vp9.*? (\d+)x(\d+)", text)
    if m:
        out["width"], out["height"] = int(m.group(1)), int(m.group(2))
    m = re.search(r"(\d+(?:\.\d+)?) fps", text)
    if m:
        out["fps"] = float(m.group(1))
    m = re.search(r"Duration: (\d+):(\d+):([\d.]+)", text)
    if m:
        out["duration_seconds"] = int(m.group(1)) * 3600 + int(m.group(2)) * 60 + float(m.group(3))
    return out
