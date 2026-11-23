# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/stream.py
import logging
import asyncio
import os
import shutil
import re
from pytgcalls import PyTgCalls
from pytgcalls.types.raw import AudioStream, VideoParameters, AudioParameters
from pytgcalls.types import MediaStream
from .base import Plugin
from telethon import events, types
from telethon.errors import FloodWaitError

logger = logging.getLogger(__name__)

HELP = """
🎬 **پخش کننده ویدیو تلگرام**

**عملکرد اصلی:**
- پخش فایل‌های ویدیویی در چت‌های صوتی با کیفیت بالا

**دستورات موجود:**
- **شروع پخش:**
  - `/stream` یا **پخش ویدیو**
  - _برای استفاده:_ در یک پیام که شامل فایل ویدیویی است، پاسخ (ریپلای) دهید و از این دستور استفاده کنید.
- **پخش از لینک:**
  - `/stream_url [لینک]` یا **پخش لینک [لینک]**
  - _برای استفاده:_ دستور را همراه با لینک مستقیم فایل ویدیویی وارد کنید.
- **توقف پخش:**
  - `/stop_stream` یا **توقف پخش**
  - _برای استفاده:_ این دستور جریان پخش فعال را متوقف می‌کند.

**ملاحظات:**
- **اندازه فایل:** حداکثر حجم مجاز ۱۰۰ مگابایت است.
- **مدیریت منابع:** امکان پخش همزمان در چند چت با مدیریت خودکار منابع فراهم شده است.
- **دسترسی:** این دستورات فقط برای کاربر مالک ربات فعال هستند.

**نمونه‌ها:**
- برای پخش یک فایل ویدئویی از یک پیام ریپلای شده:
  - `/stream` یا **پخش ویدیو**
- برای پخش ویدیو از یک لینک مستقیم:
  - `/stream_url https://example.com/video.mp4` یا **پخش لینک https://example.com/video.mp4**

⚠️ **توجه:** استفاده از این دستورات محدود به کاربر مالک ربات می‌باشد.
"""

class StreamPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.py_tgcalls = None
        self.active_streams = {}
        logger.info(f"StreamPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.me = me
        self.py_tgcalls = PyTgCalls(self.client)
        await self.py_tgcalls.start()
        await self.clean_downloads()  # Clean residual files on startup
        logger.info("PyTgCalls client initialized for video streaming")

    async def clean_downloads(self):
        """Clean downloads directory using async thread pool"""
        loop = asyncio.get_event_loop()
        if await loop.run_in_executor(None, os.path.exists, 'video_downloads/'):
            await loop.run_in_executor(None, shutil.rmtree, 'video_downloads/')
        await loop.run_in_executor(None, 
            lambda: os.makedirs('video_downloads/', exist_ok=True)
        )

    async def delete_file(self, path: str):
        """Asynchronously delete a file"""
        loop = asyncio.get_event_loop()
        try:
            await loop.run_in_executor(None, os.remove, path)
        except Exception as e:
            logger.error(f"Error deleting file {path}: {str(e)}")

    async def is_video_format(self, file):
        """Check if file is a supported video format"""
        video_mimetypes = [
            'video/mp4', 'video/x-matroska', 'video/webm', 
            'video/avi', 'video/quicktime', 'video/x-flv'
        ]
        
        if hasattr(file, 'mime_type'):
            return file.mime_type in video_mimetypes
        return False

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(\/stream|پخش ویدیو)$'))
        async def stream_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_reply:
                await event.reply("❌ لطفاً به یک پیام ویدیویی پاسخ دهید تا پخش شود.")
                await event.delete()
                return

            replied_msg = await event.get_reply_message()
            
            # Validate video content
            file = replied_msg.video or replied_msg.document
            if not file or not await self.is_video_format(file):
                await event.reply("❌ پیام پاسخ داده شده یک فایل ویدیویی نیست.")
                await event.delete()
                return

            # Get file size before downloading
            file_size = file.size
            if file_size > 100 * 1024 * 1024:  # 100MB limit
                await event.reply("❌ حجم فایل بیش از ۱۰۰ مگابایت است.")
                await event.delete()
                return

            video_path = None
            try:
                status_msg = await event.reply("🔄 در حال دانلود ویدیو...")
                video_path = await replied_msg.download_media(file='video_downloads/')
                await status_msg.edit("📥 دانلود انجام شد. در حال شروع پخش...")
                
                chat_id = event.chat_id
                
                # Stop existing stream and clean old file
                if chat_id in self.active_streams:
                    old_path = self.active_streams[chat_id]
                    await self.py_tgcalls.leave_call(chat_id)
                    await self.delete_file(old_path)

                # Start new video stream with optimized parameters
                await self.py_tgcalls.play(
                    chat_id,
                    MediaStream(
                        video_path,
                        video_parameters=VideoParameters(
                            width=1280,
                            height=720,
                            frame_rate=30,
                        ),
                        audio_parameters=AudioParameters(
                            bitrate=128000,
                        ),
                    ),
                )
                
                self.active_streams[chat_id] = video_path
                await status_msg.edit("🎬 پخش ویدیو در چت صوتی شروع شد!")
                await event.delete()

            except Exception as e:
                logger.error(f"Video streaming error: {str(e)}")
                await event.reply(f"❌ پخش ناموفق بود: {str(e)}")
                if video_path and os.path.exists(video_path):
                    await self.delete_file(video_path)
                await event.delete()

        @self.client.on(events.NewMessage(pattern=r'^(\/stream_url|پخش\s+لینک) (.+)'))
        async def stream_url_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            # Extract URL from command
            match = re.match(r'^(\/stream_url|پخش\s+لینک) (.+)', event.raw_text)
            if not match:
                await event.reply("❌ لطفاً لینک ویدیو را وارد کنید.")
                await event.delete()
                return
                
            url = match.group(2).strip()
            
            # Validate URL format
            if not re.match(r'https?://.+\.(mp4|mkv|webm|avi|mov|flv)$', url, re.IGNORECASE):
                await event.reply("❌ لینک وارد شده معتبر نیست. فقط لینک‌های مستقیم به فایل‌های ویدیویی پشتیبانی می‌شوند.")
                await event.delete()
                return

            # Start streaming from URL
            try:
                chat_id = event.chat_id
                
                # Stop existing stream
                if chat_id in self.active_streams:
                    old_path = self.active_streams[chat_id]
                    await self.py_tgcalls.leave_call(chat_id)
                    # If old_path is not a URL, delete the file
                    if os.path.exists(old_path):
                        await self.delete_file(old_path)
                
                # Stream directly from URL
                await self.py_tgcalls.play(
                    chat_id,
                    MediaStream(
                        url,
                        video_parameters=VideoParameters(
                            width=1280,
                            height=720,
                            frame_rate=30,
                        ),
                        audio_parameters=AudioParameters(
                            bitrate=128000,
                        ),
                    ),
                )
                
                self.active_streams[chat_id] = url  # Store URL instead of path
                await event.reply("🎬 پخش ویدیو از لینک در چت صوتی شروع شد!")
                await event.delete()

            except Exception as e:
                logger.error(f"URL streaming error: {str(e)}")
                await event.reply(f"❌ پخش از لینک ناموفق بود: {str(e)}")
                await event.delete()

        @self.client.on(events.NewMessage(pattern=r'^(\/stop_stream|توقف\s+پخش)$'))
        async def stop_stream_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            chat_id = event.chat_id
            if chat_id in self.active_streams:
                old_path = self.active_streams.pop(chat_id)
                await self.py_tgcalls.leave_call(chat_id)
                
                # Only delete if it's a local file
                if os.path.exists(old_path):
                    await self.delete_file(old_path)
                    
                await event.reply("⏹ پخش ویدیو متوقف شد")
            else:
                await event.reply("❌ هیچ پخش ویدیویی فعالی در این چت وجود ندارد")
            await event.delete()
