# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, types, errors
from telethon.tl.types import (
    InputMediaPhoto,
    InputMediaDocument,
    InputMediaUploadedDocument
)
import asyncio
import random
from datetime import datetime

HELP = """
⚡ **پلاگین اسپم پیشرفته** ⚡

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت‌ها**:
  • ارسال پیام‌ها با حداکثر سرعت
  • مدیریت خودکار Flood Wait تلگرام
  • ادامه اسپم پس از پایان زمان محدودیت
  • پشتیبانی از متن، عکس، ویدئو و فایل

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات**:

  • **English:**
       `/spam [تعداد] [متن]` - ارسال متن
       `/spam [تعداد]` (با ریپلای روی رسانه) - ارسال رسانه
       `/spamstop` - توقف فوری اسپم
       `/spamhelp` - نمایش راهنما

  • **فارسی (بدون /):**
       `اسپم [تعداد] [متن]` - ارسال متن
       `اسپم [تعداد]` (با ریپلای روی رسانه) - ارسال رسانه
       `توقف اسپم` - توقف فوری اسپم
       `راهنمای اسپم` - نمایش راهنما
"""

class SpamPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.active_spams = {}
        self.media_cache = {}

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/spam|اسپم)\s+(\d+)(?:\s+(.+))?$'))
        async def spam_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            count = int(event.pattern_match.group(1))
            text = event.pattern_match.group(2) or ""

            if count > 100:
                await event.reply("❌ حداکثر 100 پیام مجاز است!")
                return

            media = None
            if event.is_reply:
                reply = await event.get_reply_message()
                media = await self._process_media(reply)

            if event.chat_id in self.active_spams:
                await event.reply("⚠️ اسپم فعال است! ابتدا با /spamstop یا 'توقف اسپم' توقف دهید.")
                return

            self.active_spams[event.chat_id] = {
                "task": asyncio.create_task(
                    self._smart_spam(
                        chat_id=event.chat_id,
                        count=count,
                        text=text,
                        media=media
                    )
                ),
                "active": True
            }
            await event.reply(f"🚀 شروع اسپم ({count} پیام)")

        @self.client.on(events.NewMessage(pattern=r'^(?:/spamstop|توقف\s+اسپم)$'))
        async def spam_stop_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            if event.chat_id in self.active_spams:
                self.active_spams[event.chat_id]["active"] = False
                self.active_spams[event.chat_id]["task"].cancel()
                del self.active_spams[event.chat_id]
                await event.reply("✅ اسپم متوقف شد!")

        @self.client.on(events.NewMessage(pattern=r'^(?:/spamhelp|راهنمای\s+اسپم)$'))
        async def spam_help_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            await event.reply(HELP)

    async def _process_media(self, message):
        """پردازش رسانه با فرمت صحیح برای Telethon v2+"""
        if not message.media:
            return None

        if isinstance(message.media, types.MessageMediaPhoto):
            # For photos, using the photo directly
            return message.media.photo
        elif isinstance(message.media, types.MessageMediaDocument):
            # For documents, using the document directly
            return message.media.document
        return None

    async def _smart_spam(self, chat_id, count, text, media):
        """اسپم هوشمند با مدیریت FloodWait"""
        remaining = count
        try:
            while remaining > 0 and self.active_spams.get(chat_id, {}).get("active", False):
                try:
                    if media:
                        await self.client.send_file(chat_id, file=media)
                    else:
                        await self.client.send_message(chat_id, text)
                    remaining -= 1
                except errors.FloodWaitError as e:
                    wait_time = e.seconds
                    await self.client.send_message(
                        chat_id, 
                        f"⏳ FloodWait: ربات به مدت {wait_time} ثانیه متوقف میشود..."
                    )
                    await asyncio.sleep(wait_time)
                    continue
                except Exception as e:
                    print(f"خطا: {str(e)}")
                    break
        except asyncio.CancelledError:
            pass
        finally:
            if chat_id in self.active_spams:
                del self.active_spams[chat_id]
