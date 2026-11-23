# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/play.py (Updated)
import logging
import asyncio
import os
import shutil
from pytgcalls import PyTgCalls
from pytgcalls.types import MediaStream
from .base import Plugin
from telethon import events, types
from telethon.errors import FloodWaitError

logger = logging.getLogger(__name__)

HELP = """
🎶 پخش کننده صوت تلگرام
▫️ عملکرد اصلی: پخش فایل‌های صوتی در چت‌های صوتی با کیفیت بالا
▫️ دستورات مربوطه:

  • **English:**
       `/play` ➔ پخش فایل صوتی (با ریپلای روی فایل صوتی)
       `/stop` ➔ توقف پخش

  • **فارسی (بدون /):**
       `پخش` ➔ پخش فایل صوتی (با ریپلای روی فایل صوتی)
       `توقف` ➔ توقف پخش

▫️ راهنمای جامع: 
- با ریپلای روی فایل صوتی و دستور `/play` یا `پخش` پخش را شروع کنید
- حداکثر حجم فایل: ۲۰ مگابایت
- قابلیت پخش همزمان در چند چت با مدیریت خودکار منابع
مثال: "/play (ریپلای روی فایل صوتی)" یا "پخش (ریپلای روی فایل صوتی)"
⚠️ توجه: دسترسی محدود به کاربر مالک ربات
"""

class PlayPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.py_tgcalls = None
        self.active_chats = {}
        logger.info(f"PlayPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.me = me
        self.py_tgcalls = PyTgCalls(self.client)
        await self.py_tgcalls.start()
        await self.clean_downloads()  # Clean residual files on startup
        logger.info("PyTgCalls client initialized")

    async def clean_downloads(self):
        """Clean downloads directory using async thread pool"""
        loop = asyncio.get_event_loop()
        if await loop.run_in_executor(None, os.path.exists, 'downloads/'):
            await loop.run_in_executor(None, shutil.rmtree, 'downloads/')
        # Fix: Wrap in lambda to pass keyword arguments
        await loop.run_in_executor(None, 
            lambda: os.makedirs('downloads/', exist_ok=True)
        )

    async def delete_file(self, path: str):
        """Asynchronously delete a file"""
        loop = asyncio.get_event_loop()
        try:
            await loop.run_in_executor(None, os.remove, path)
        except Exception as e:
            logger.error(f"Error deleting file {path}: {str(e)}")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/play|پخش)$'))
        async def play_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_reply:
                await event.reply("❌ لطفاً به یک پیام صوتی پاسخ دهید تا پخش شود.")
                await event.delete()
                return

            replied_msg = await event.get_reply_message()
            
            # Validate audio content
            if not replied_msg.audio and not (replied_msg.document and replied_msg.document.mime_type.startswith('audio/')):
                await event.reply("❌ پیام پاسخ داده شده یک فایل صوتی نیست.")
                await event.delete()
                return

            # Get file size before downloading
            file = replied_msg.audio or replied_msg.document
            file_size = file.size
            if file_size > 20 * 1024 * 1024:  # 20MB limit
                await event.reply("❌ حجم فایل بیش از ۲۰ مگابایت است.")
                await event.delete()
                return

            audio_path = None
            try:
                audio_path = await replied_msg.download_media(file='downloads/')
                chat_id = event.chat_id
                
                # Stop existing playback and clean old file
                if chat_id in self.active_chats:
                    old_path = self.active_chats[chat_id]
                    await self.py_tgcalls.leave_call(chat_id)
                    await self.delete_file(old_path)

                # Start new playback
                await self.py_tgcalls.play(
                    chat_id,
                    MediaStream(audio_path),
                )
                self.active_chats[chat_id] = audio_path
                await event.reply("🎶 پخش صوت در چت صوتی شروع شد!")
                await event.delete()

            except Exception as e:
                logger.error(f"Playback error: {str(e)}")
                await event.reply(f"❌ پخش ناموفق بود: {str(e)}")
                if audio_path and os.path.exists(audio_path):
                    await self.delete_file(audio_path)
                await event.delete()

        @self.client.on(events.NewMessage(pattern=r'^(?:/stop|توقف)$'))
        async def stop_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            chat_id = event.chat_id
            if chat_id in self.active_chats:
                old_path = self.active_chats.pop(chat_id)
                await self.py_tgcalls.leave_call(chat_id)
                await self.delete_file(old_path)
                await event.reply("⏹ پخش متوقف شد")
            else:
                await event.reply("❌ هیچ پخش فعالی در این چت وجود ندارد")
            await event.delete()
