# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, types
import os
import logging
import asyncio
from datetime import datetime

logger = logging.getLogger(__name__)

HELP = """
📡 **پلاگین ذخیره‌سازی رسانه** 📡

▫️ **دستورات:**

  • **English:**
       `/dlr [آیدی/یوزرنیم]` ➔ ذخیره رسانه‌های کانال
       `/help` ➔ نمایش راهنما

  • **فارسی (بدون /):**
       `ذخیره [آیدی/یوزرنیم]` ➔ ذخیره رسانه‌های کانال
       `راهنما` ➔ نمایش راهنما
"""

class UniversalChannelSaver(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.download_path = "data/temp_downloads"
        os.makedirs(self.download_path, exist_ok=True)

    async def _get_channel(self, identifier):
        try:
            return await self.client.get_entity(identifier)
        except Exception as e:
            logger.error(f"خطا در یافتن کانال: {e}")
            return None

    async def _generate_filename(self, msg):
        """Generate an appropriate filename for the media"""
        if hasattr(msg.media, 'document'):
            file_name = msg.file.name if hasattr(msg.file, 'name') and msg.file.name else f"file_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
            if '.' not in file_name:
                mime_type = msg.file.mime_type if hasattr(msg.file, 'mime_type') else ""
                ext = ""
                if mime_type:
                    if "image" in mime_type:
                        ext = ".jpg" if "jpeg" in mime_type else ".png"
                    elif "video" in mime_type:
                        ext = ".mp4"
                    elif "audio" in mime_type:
                        ext = ".mp3"
                file_name = f"{file_name}{ext}"
        else:
            file_name = f"media_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
            
        return os.path.join(self.download_path, file_name)

    async def _save_media(self, msg, channel_title):
        if not msg.media:
            return False
            
        try:
            # Generate appropriate filename
            file_path = await self._generate_filename(msg)
            
            # Download file
            downloaded_file = await self.client.download_media(msg.media, file_path)
            
            if downloaded_file:
                # Send to saved messages
                await self.client.send_file(
                    'me',
                    downloaded_file,
                    caption=f"ذخیره شده از {channel_title}"
                )
                
                # Delete the file from server
                os.remove(downloaded_file)
                return True
            return False
        except Exception as e:
            logger.error(f"خطا در ذخیره رسانه: {e}")
            return False

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/dlr|ذخیره)\s+(\S+)'))
        async def dlr_handler(event):
            if str(event.sender_id) != self.owner_id:
                return 

            channel = await self._get_channel(event.pattern_match.group(1))
            if not channel:
                return await event.reply("❌ کانال یافت نشد")

            status_msg = await event.reply("⏳ در حال دانلود رسانه ها...")
            
            saved = 0
            total_processed = 0
            already_seen = set()  # Simple in-memory tracking to avoid duplicates in single session
            
            try:
                async for msg in self.client.iter_messages(channel):
                    total_processed += 1
                    
                    # Simple deduplication using message ID
                    msg_id = str(msg.id)
                    if msg_id in already_seen:
                        continue
                        
                    already_seen.add(msg_id)
                    
                    if msg.media and await self._save_media(msg, getattr(channel, 'title', 'ناشناس')):
                        saved += 1
                        
                    # Update status every 20 messages
                    if total_processed % 20 == 0:
                        await status_msg.edit(f"⏳ در حال پردازش... {total_processed} پیام بررسی شد، {saved} رسانه ذخیره شد")
                        
                    await asyncio.sleep(0.5)  # Rate limiting
            except Exception as e:
                logger.error(f"خطا در پردازش پیام‌ها: {e}")
                await status_msg.edit(f"❌ خطا: {str(e)}\n✅ {saved} رسانه از {total_processed} پیام ذخیره شد")
                return

            await status_msg.edit(f"✅ {saved} رسانه از {total_processed} پیام ذخیره شد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/helpppppp|رhhhhhnnاهنما)$'))
        async def help_handler(event):
            if str(event.sender_id) != self.owner_id:
                return 
            await event.reply(HELP)
