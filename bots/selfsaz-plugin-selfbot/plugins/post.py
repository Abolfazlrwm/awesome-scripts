# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import os
import asyncio
from datetime import datetime
import logging
import re
from telethon.errors import FloodWaitError

HELP = """
📲 **دانلودر پست‌های تلگرام** 📲

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **ویژگی‌های اصلی**:
• دانلود عکس و ویدیو از پست‌های تلگرام
• پشتیبانی از دریافت پست از طریق نام کاربری، آیدی یا لینک (فرمت: t.me/username/12345)
• امکان دانلود یک پست یا چند پست اخیر (با تعیین تعداد)
• حذف خودکار فایل‌های دانلود شده پس از ارسال

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات اصلی**:

  • **English:**
       `/downloadpost [username/id/link] [post_id]` ➔ دانلود یک پست 
           (اگر post_id داده نشود، آخرین پست دانلود می‌شود)
       `/downloadallposts [username/id/link] [limit]` ➔ دانلود چند پست اخیر (پیشفرض ۵ پست)

  • **فارسی:**
       `دانلود پست [نام کاربری/آیدی/لینک] [آیدی پست]` ➔ دانلود یک پست 
           (اگر آیدی پست داده نشود، آخرین پست دانلود می‌شود)
       `دانلود تمام پست‌ها [نام کاربری/آیدی/لینک] [تعداد]` ➔ دانلود چند پست اخیر (پیشفرض ۵ پست)

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **نمونه استفاده**:
1. دانلود آخرین پست از یک کانال:
   `/downloadpost @channelusername` یا `دانلود پست @channelusername`
2. دانلود پستی با آیدی مشخص:
   `/downloadpost @channelusername 12345` یا `دانلود پست @channelusername 12345`
3. دانلود پست از طریق لینک:
   `/downloadpost https://t.me/channelusername/12345` یا `دانلود پست https://t.me/channelusername/12345`
4. دانلود ۱۰ پست اخیر:
   `/downloadallposts @channelusername 10` یا `دانلود تمام پست‌ها @channelusername 10`

⚠️ **نکات مهم**:
- باید بتوانید به پست‌های کانال، گروه یا شخص دسترسی داشته باشید.
- فایل‌های دانلود شده پس از ارسال به صورت موقت نگهداری و سپس حذف می‌شوند.
- فقط صاحب ربات اجازه استفاده از این دستورات را دارد.
"""

class PostDownloaderPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.download_path = "downloads/posts"
        self.logger = logging.getLogger(__name__)
        os.makedirs(self.download_path, exist_ok=True)

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/downloadpost|دانلود\s+پست)(?:\s+(.+?))(?:\s+(\d+))?$'))
        async def download_post_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            target = event.pattern_match.group(1)
            post_id_cmd = event.pattern_match.group(2)  # Optional explicit post ID
            if not target:
                return await event.reply("❌ لطفا یک نام کاربری، آیدی یا لینک وارد کنید")
            await event.reply("🔍 در حال جستجوی پست...")
            try:
                # _get_entity returns (entity, post_id_from_link) if available
                entity, post_id_link = await self._get_entity(target)
                if not entity:
                    return await event.reply("❌ کاربر/کانال یافت نشد یا ورودی نامعتبر است")
                # Use the command parameter if provided; otherwise try any post id from link
                post_id = int(post_id_cmd) if post_id_cmd else (post_id_link if post_id_link else None)
                success, file_paths = await self._download_post(event, entity, post_id)
                if success:
                    await self._cleanup_files(file_paths)
                    identifier = getattr(entity, 'title', getattr(entity, 'first_name', ''))
                    await event.reply(f"✅ پست مورد نظر از {identifier} با موفقیت پردازش شد")
                else:
                    await event.reply("ℹ️ هیچ پستی برای دانلود یافت نشد یا پست فاقد رسانه است")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/downloadallposts|دانلود\s+تمام\s+پست‌ها)(?:\s+(.+?))(?:\s+(\d+))?$'))
        async def download_all_posts_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            target = event.pattern_match.group(1)
            limit_str = event.pattern_match.group(2)
            if not target:
                return await event.reply("❌ لطفا یک نام کاربری، آیدی یا لینک وارد کنید")
            # Default limit is 5 posts if not provided
            limit = int(limit_str) if limit_str and limit_str.isdigit() else 5
            await event.reply("🔍 در حال جستجوی پست‌ها...")
            try:
                entity, _ = await self._get_entity(target)
                if not entity:
                    return await event.reply("❌ کاربر/کانال یافت نشد یا ورودی نامعتبر است")
                success, file_paths = await self._download_all_posts(event, entity, limit)
                if success:
                    await self._cleanup_files(file_paths)
                    identifier = getattr(entity, 'title', getattr(entity, 'first_name', ''))
                    await event.reply(f"✅ {limit} پست از {identifier} با موفقیت پردازش شدند")
                else:
                    await event.reply("ℹ️ هیچ پستی برای دانلود یافت نشد")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

    async def _get_entity(self, target):
        """
        استخراج موجودیت کانال/گروه/فرد از نام کاربری، آیدی یا لینک.
        برای لینک‌های پست: فرمت صحیح t.me/username/12345
        همچنین، اگر ورودی یک کاربر باشد و دارای personal_channel_id باشد (کانال شخصی)؛ 
        از آن برای دریافت پست‌ها استفاده می‌شود.
        """
        try:
            post_id = None
            entity = None
            if "t.me" in target:
                # بررسی لینک به فرم: t.me/username/12345
                m = re.search(r't\.me/?@?(\w+)(?:/(\d+))?', target)
                if m:
                    username = m.group(1)
                    if m.group(2):
                        post_id = int(m.group(2))
                    entity = await self.client.get_entity(username)
            elif target.isdigit():
                entity = await self.client.get_entity(int(target))
            else:
                username = target.lstrip('@')
                entity = await self.client.get_entity(username)

            # اگر موجودیت یک کاربر باشد و دارای personal_channel_id است (کانال شخصی)
            # تلاش می‌شود کانال شخصی را دریافت کنیم
            if entity and getattr(entity, 'bot', False) is False:
                try:
                    full = await self.client(GetFullUserRequest(entity))
                    if hasattr(full.full_user, 'personal_channel_id') and full.full_user.personal_channel_id:
                        # دریافت کانال شخصی
                        channel = await self.client.get_entity(full.full_user.personal_channel_id)
                        entity = channel
                except Exception as e:
                    # در صورت بروز خطا، از موجودیت اصلی استفاده می‌شود
                    self.logger.debug(f"خطا در دریافت کانال شخصی: {str(e)}")
            return entity, post_id
        except Exception as e:
            self.logger.error(f"خطا در دریافت موجودیت: {str(e)}")
            return None, None

    async def _download_post(self, event, entity, post_id=None):
        """
        دانلود یک پست خاص از یک کانال/گروه/شخص.
        اگر post_id مشخص نباشد، آخرین پست دانلود می‌شود.
        پشتیبانی از دریافت رسانه‌های زیر:
          - عکس (jpg)
          - ویدئو (mp4)
          - در صورت وجود پیشنمایش وب (MessageMediaWebPage شامل عکس)
        """
        try:
            file_paths = []
            if post_id:
                message = await self.client.get_messages(entity, ids=post_id)
            else:
                messages = await self.client.get_messages(entity, limit=1)
                message = messages[0] if messages else None

            if not message:
                return False, None

            # تشخیص نوع رسانه موجود در پست
            if message.media:
                if hasattr(message.media, 'photo'):
                    ext = 'jpg'
                    media_to_download = message.media
                elif hasattr(message.media, 'document'):
                    ext = 'mp4'
                    media_to_download = message.media
                elif hasattr(message.media, 'webpage') and message.media.webpage and hasattr(message.media.webpage, 'photo'):
                    ext = 'jpg'
                    media_to_download = message.media.webpage.photo
                else:
                    return False, None
            else:
                return False, None

            # ایجاد پوشه جهت ذخیره فایل‌ها
            identifier = getattr(entity, 'username', None) or (getattr(entity, 'title', None) or str(entity.id))
            user_dir = os.path.join(self.download_path, identifier)
            os.makedirs(user_dir, exist_ok=True)
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            file_path = os.path.join(user_dir, f"post_{message.id}_{timestamp}.{ext}")

            await self.client.download_media(media_to_download, file_path)
            file_paths.append(file_path)

            caption = f"پست از {getattr(entity, 'title', getattr(entity, 'first_name', ''))}"
            if message.message:
                caption += f"\nمتن: {message.message}"
            await self.client.send_file(event.chat_id, file_path, caption=caption)

            return True, file_paths
        except FloodWaitError as e:
            self.logger.error(f"Flood wait: {e}")
            return False, None
        except Exception as e:
            self.logger.error(f"Download post error: {e}")
            return False, None

    async def _download_all_posts(self, event, entity, limit):
        """
        دانلود چند پست اخیر از یک کانال/گروه/شخص.
        برای هر پست، اگر رسانه موجود باشد، دانلود و ارسال می‌شود.
        """
        try:
            file_paths = []
            messages = await self.client.get_messages(entity, limit=limit)
            if not messages:
                return False, None

            identifier = getattr(entity, 'username', None) or (getattr(entity, 'title', None) or str(entity.id))
            user_dir = os.path.join(self.download_path, identifier)
            os.makedirs(user_dir, exist_ok=True)

            for message in messages:
                if not message.media:
                    continue
                if hasattr(message.media, 'photo'):
                    ext = 'jpg'
                    media_to_download = message.media
                elif hasattr(message.media, 'document'):
                    ext = 'mp4'
                    media_to_download = message.media
                elif hasattr(message.media, 'webpage') and message.media.webpage and hasattr(message.media.webpage, 'photo'):
                    ext = 'jpg'
                    media_to_download = message.media.webpage.photo
                else:
                    continue

                timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
                file_path = os.path.join(user_dir, f"post_{message.id}_{timestamp}.{ext}")
                await self.client.download_media(media_to_download, file_path)
                file_paths.append(file_path)

                caption = f"پست از {getattr(entity, 'title', getattr(entity, 'first_name', ''))}"
                if message.message:
                    caption += f"\nمتن: {message.message}"
                await self.client.send_file(event.chat_id, file_path, caption=caption)

            return True, file_paths
        except FloodWaitError as e:
            self.logger.error(f"Flood wait: {e}")
            return False, None
        except Exception as e:
            self.logger.error(f"Download all posts error: {e}")
            return False, None

    async def _cleanup_files(self, file_paths):
        if not file_paths:
            return
        for path in file_paths:
            try:
                if os.path.exists(path):
                    os.unlink(path)
                    self.logger.info(f"Deleted: {path}")
            except Exception as e:
                self.logger.error(f"Cleanup error for {path}: {e}")
