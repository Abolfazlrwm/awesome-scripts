# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
پلاگین پاکسازی پیشرفته با پشتیبانی از ایموجی‌های پریمیوم
"""

from telethon import events, types
from .base import Plugin
import logging
import asyncio
import time

logger = logging.getLogger("AdvancedCleaner")

HELP = """
🧹 **پاکسازی پیشرفته** 🧹

• **English Commands:**
    `/clean` ➔ حذف تمام موارد
    `/clean [انواع]` ➔ حذف انتخابی

• **دستورات فارسی:**
    `پاک کن` ➔ حذف تمام موارد
    `پاک کن [انواع]` ➔ حذف انتخابی

انواع موجود:
premiumemoji - ایموجی‌های پریمیوم
video - ویدیو معمولی
roundvideo - ویدیوهای گرد
gif, photo, voice, music, all

مثال:
    /clean premiumemoji
    /clean video roundvideo
    پاک کن premiumemoji
    پاک کن video roundvideo
"""

class CleanerFaEnPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.user_id = str(user_id)
        self.progress_msg = None
        self.last_update = 0
        logger.info("پلاگین پیشرفته فعال شد")

    async def _update_progress(self, stats: dict):
        """نمایش پیشرفت زنده"""
        progress = stats['deleted'] / max(stats['total'], 1) * 100
        bar = "⬛" * int(progress/10) + "⬜" * (10 - int(progress/10))
        text = (
            f"⏳ **پردازش:** {stats['processed']}\n"
            f"✅ حذف شده: {stats['deleted']} | ❌ خطاها: {stats['errors']}\n"
            f"{bar} {progress:.1f}%"
        )
        
        if time.time() - self.last_update > 5:  # Update less frequently for speed
            try:
                await self.progress_msg.edit(text)
                self.last_update = time.time()
            except Exception as e:
                logger.error(f"خطا در بروزرسانی وضعیت: {str(e)}")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/clean|پاک کن)(\s+.*)?$'))
        async def clean_handler(event):

            if str(event.sender_id) != self.user_id:
                return
            if not await self._is_admin(event):
                await event.reply("❌ نیاز به دسترسی ادمین دارم!")
                return

            args = event.pattern_match.group(1).strip().lower().split() if event.pattern_match.group(1) else []
            target_media = {'video', 'roundvideo', 'gif', 'photo', 'voice', 'music', 'premiumemoji'}
            
            if 'all' in args:
                target_media = {'all'}
            elif args:
                target_media = set(arg for arg in args if arg in target_media.union({'all'}))

            self.progress_msg = await event.reply("🔄 در حال شروع...")
            self.last_update = time.time()

            stats = {'deleted': 0, 'errors': 0, 'processed': 0, 'total': 0}
            messages_to_delete = []

            async for msg in self.client.iter_messages(event.chat_id):
                stats['total'] += 1

            async for msg in self.client.iter_messages(event.chat_id):
                stats['processed'] += 1

                try:
                    if self._should_delete(msg, target_media):
                        messages_to_delete.append(msg.id)

                    if len(messages_to_delete) >= 100:  # Bulk delete every 100 messages
                        await self.client.delete_messages(event.chat_id, messages_to_delete)
                        stats['deleted'] += len(messages_to_delete)
                        messages_to_delete = []

                    if stats['processed'] % 20 == 0:  # Less frequent updates
                        await self._update_progress(stats)

                except Exception as e:
                    stats['errors'] += 1
                    logger.error(f"خطا در حذف: {str(e)}")

            # Delete remaining messages
            if messages_to_delete:
                await self.client.delete_messages(event.chat_id, messages_to_delete)
                stats['deleted'] += len(messages_to_delete)

            final_report = (
                f"✅ **اتمام پاکسازی**\n"
                f"• حذف شده: {stats['deleted']}\n"
                f"• خطاها: {stats['errors']}\n"
                f"• کل پیام‌ها: {stats['total']}"
            )
            await self.progress_msg.edit(final_report)

    def _should_delete(self, msg, target_types):
        """تشخیص پیشرفته انواع مدیا و ایموجی‌های پریمیوم"""
        if not msg.media and 'premiumemoji' not in target_types:
            return False

        if 'all' in target_types:
            return True

        # تشخیص ایموجی‌های پریمیوم
        if 'premiumemoji' in target_types and self._has_premium_emoji(msg):
            return True

        # تشخیص مدیاهای قبلی
        if msg.media:
            if isinstance(msg.media, types.MessageMediaPhoto) and 'photo' in target_types:
                return True

            if isinstance(msg.media, types.MessageMediaDocument):
                document = msg.media.document
                mime = document.mime_type.lower()
                is_round = False
                is_video = False
                is_audio = False
                is_gif = False
                is_voice = False

                for attr in document.attributes:
                    if isinstance(attr, types.DocumentAttributeVideo):
                        is_video = True
                        is_round = attr.round_message
                    if isinstance(attr, types.DocumentAttributeAudio):
                        is_audio = True
                        is_voice = attr.voice
                    if isinstance(attr, types.DocumentAttributeAnimated):
                        is_gif = True

                # ویدیو گرد
                if 'roundvideo' in target_types and is_round:
                    return True
                    
                # ویدیو معمولی
                if 'video' in target_types and is_video and not is_round:
                    return True
                    
                # گیف
                if 'gif' in target_types and is_gif:
                    return True
                    
                # ویس
                if 'voice' in target_types and is_voice:
                    return True
                    
                # آهنگ
                if 'music' in target_types and is_audio and not is_voice:
                    return True

        return False

    def _has_premium_emoji(self, msg):
        """بررسی وجود ایموجی پریمیوم در پیام"""
        if not msg.entities:
            return False

        for entity in msg.entities:
            if isinstance(entity, types.MessageEntityCustomEmoji):
                return True
        return False

    async def _is_admin(self, event):
        try:
            bot = await self.client.get_me()
            chat = await self.client.get_entity(event.chat_id)
            perms = await self.client.get_permissions(chat, bot.id)
            return perms.is_admin and perms.delete_messages
        except Exception as e:
            logger.error(f"خطای دسترسی: {str(e)}")
            return False

    async def shutdown(self):
        logger.info("پلاگین غیرفعال شد")
