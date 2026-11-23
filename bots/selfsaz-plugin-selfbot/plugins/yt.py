# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
from telethon.tl.types import PeerChannel, PeerChat
import requests
import re
import os
import time
from .db_utils import execute_query

HELP = """
📥 دانلودر یوتیوب با کیفیت انتخابی 📥

دستور:
/ytdl [لینک-یوتیوب]

پس از اجرای دستور، لیستی از کیفیت‌ها دریافت می‌کنید.
برای دریافت فایل:
1. کیفیت را دقیقاً یا بخشی از آن (مثل 720p) ریپلای کنید
2. تنها کاربران مجاز قادر به دریافت فایل خواهند بود.
"""

class YouTubeDLPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.api_url = "https://api.mr-amiri.ir/api/youtube-dl.php"
        self.api_key = "PUT-YOUR-KEY-HERE"
        self.owner_id = str(user_id)
        self.download_cache = {}  # message_id -> (formats, title, duration, thumb_url, timestamp)
        # Timeout (in seconds) for quality-reply messages.
        self.download_cache_timeout = 300  # e.g., 5 minutes

    def extract_youtube_id(self, url):
        patterns = [
            r'(?:https?:\/\/)?(?:www\.)?youtu\.be\/([a-zA-Z0-9_-]+)',
            r'(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)',
            r'(?:https?:\/\/)?(?:www\.)?youtube\.com\/shorts\/([a-zA-Z0-9_-]+)'
        ]
        for pattern in patterns:
            match = re.search(pattern, url)
            if match:
                return match.group(1)
        return None

    def sanitize_filename(self, name):
        return re.sub(r'[^a-zA-Z0-9_.-]', '_', name)

    async def get_video_info(self, video_id):
        params = {
            'key': self.api_key,
            'url': f"https://youtu.be/{video_id}"
        }
        try:
            response = requests.get(self.api_url, params=params, timeout=10)
            if response.status_code == 200:
                return response.json()
            return None
        except Exception as e:
            print(f"API Error: {e}")
            return None

    # Use the auth logic from aichat.py:
    async def _is_authorized(self, user_id: str) -> bool:
        try:
            result = await execute_query(
                "SELECT 1 FROM authorized_users WHERE user_id = %s",
                (user_id,),
                fetch=True
            )
            return bool(result)
        except Exception as e:
            print(f"❌ Authorization check failed: {e}")
            return False

    def _resolve_chat_id(self, chat_id: str):
        try:
            if str(chat_id).startswith("-100"):
                channel_id = int(str(chat_id).replace("-100", ""))
                return PeerChannel(channel_id)
            else:
                numeric_id = abs(int(chat_id))
                return PeerChat(numeric_id)
        except Exception:
            return chat_id

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'/ytdl(?: |$)(.*)'))
        async def ytdl_handler(event):
            # Check owner first.
            if str(event.sender_id) != self.owner_id:
                return

            # Then check if the sender is authorized.
            if not await self._is_authorized(str(event.sender_id)):
                await event.reply(
                    "🚫 شما برای استفاده از این قابلیت مجاز نیستید!\n"
                    "✨ برای فعال‌سازی نسخه VIP، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            url = event.pattern_match.group(1).strip()
            if not url:
                await event.reply("❌ لطفاً لینک یوتیوب را وارد کنید.")
                return

            video_id = self.extract_youtube_id(url)
            if not video_id:
                await event.reply("❌ لینک یوتیوب نامعتبر است.")
                return

            msg = await event.reply("🔍 در حال دریافت اطلاعات ویدیو...")
            data = await self.get_video_info(video_id)

            if not data or not data.get('ok'):
                await msg.edit("❌ دریافت اطلاعات ویدیو ناموفق بود.")
                return

            formats = data.get("result", [])
            title = data.get('title', 'ویدیو')
            duration = data.get('duration', 'نامشخص')
            thumbnail_url = f"https://img.youtube.com/vi/{video_id}/hqdefault.jpg"

            formats_text = (
                f"🎬 **{title}**\n"
                f"⏱ **مدت زمان:** `{duration}`\n\n"
                "🎞 **کیفیت‌های موجود:**\n\n"
            )
            for fmt in formats:
                formats_text += f"• `{fmt['label']}`\n"

            formats_text += (
                "\n✂️ یکی از کیفیت‌ها را کپی کرده یا قسمتی مثل `720p` بنویسید.\n"
                "📥 با **ریپلای** روی همین پیام ارسال کنید تا دانلود شود."
            )

            resolved_chat = self._resolve_chat_id(str(event.chat_id))
            msg2 = await self.client.send_file(
                resolved_chat,
                thumbnail_url,
                caption=formats_text,
                parse_mode="md",
                reply_to=event.id
            )

            # Cache the quality message along with a timestamp.
            self.download_cache[msg2.id] = (formats, title, duration, thumbnail_url, time.time())
            await msg.delete()

        @self.client.on(events.NewMessage)
        async def reply_quality_handler(event):
            # Check owner first.
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_reply:
                return

            # Then check if the sender is authorized.
            if not await self._is_authorized(str(event.sender_id)):
                return

            replied = await event.get_reply_message()
            if replied.id not in self.download_cache:
                return

            # Unpack cached data with timestamp.
            formats, title, duration, thumb_url, cached_time = self.download_cache[replied.id]
            # Check if the reply is within the allowed timeout.
            if time.time() - cached_time > self.download_cache_timeout:
                del self.download_cache[replied.id]
                await event.reply("❌ زمان پاسخگویی به این پیام به پایان رسیده است.")
                return

            wanted_quality = event.raw_text.strip().lower()
            chosen = None
            for fmt in formats:
                if wanted_quality in fmt['label'].lower():
                    chosen = fmt
                    break

            if not chosen:
                await event.reply("❌ کیفیت مورد نظر یافت نشد. لطفاً دقیق و مطابق لیست ارسال کنید.")
                return

            url = chosen.get("url")
            label = chosen.get("label")

            resolved_chat = self._resolve_chat_id(str(event.chat_id))
            # Instead of downloading, send only a clickable URL.
            message_text = (
                f"برای دانلود **{title}** با کیفیت **{label}** روی لینک زیر کلیک کنید:\n"
                f"[دانلود کنید]({url})"
            )
            await self.client.send_message(
                resolved_chat,
                message=message_text,
                reply_to=event.id,
                parse_mode="md"
            )

            await replied.delete()
            await event.delete()
            del self.download_cache[replied.id]

    async def start(self):
        # Any startup logic can be added here.
        pass

    async def stop(self):
        # Any cleanup logic can be added here.
        pass
