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
from telethon.tl.functions.users import GetFullUserRequest
from telethon.errors import FloodWaitError, UserNotParticipantError
import re

HELP = """
📲 **دانلودر استوری تلگرام** 📲

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **ویژگی‌های اصلی**:
• دانلود عکس و ویدیو از استوری‌های تلگرام
• پشتیبانی از دانلود با نام کاربری، آیدی یا لینک استوری
• حذف خودکار فایل‌های دانلود شده از سیستم شما
• امکان دانلود تمام استوری‌ها یا فقط آخرین استوری
• بررسی دوره‌ای برای استوری‌های جدید

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات اصلی**:

  • **English:**
       `/downloadstory [username/id/link]` ➔ دانلود آخرین استوری
       `/downloadallstories [username/id/link]` ➔ دانلود تمام استوری‌ها
       `/autostory [username/id/link] [فاصله دقیقه]` ➔ فعال کردن دانلود خودکار
       `/stopautostory [username/id/link]` ➔ غیرفعال کردن دانلود خودکار
       `/liststoryusers` ➔ نمایش کاربران با دانلود خودکار فعال

  • **فارسی (بدون /):**
       `دانلود استوری [username/id/link]` ➔ دانلود آخرین استوری
       `دانلود تمام استوری‌ها [username/id/link]` ➔ دانلود تمام استوری‌ها
       `دانلود خودکار استوری [username/id/link] [فاصله دقیقه]` ➔ فعال کردن دانلود خودکار
       `متوقف دانلود خودکار استوری [username/id/link]` ➔ غیرفعال کردن دانلود خودکار
       `لیست استوری کاربران` ➔ نمایش کاربران با دانلود خودکار فعال

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **نمونه استفاده**:
1. دانلود آخرین استوری یک کاربر:
   `/downloadstory @username` یا `دانلود استوری @username`
2. دانلود تمام استوری‌های موجود:
   `/downloadallstories 12345678` یا `دانلود تمام استوری‌ها 12345678`
3. دانلود استوری از طریق لینک:
   `/downloadstory https://t.me/stories/username/987654` یا `دانلود استوری https://t.me/username/s/987654`
4. دانلود خودکار هر 30 دقیقه:
   `/autostory @username 30` یا `دانلود خودکار استوری @username 30`

⚠️ **نکات مهم**:
- باید بتوانید استوری‌های کاربر را ببینید
- فایل‌ها پس از آپلود از سیستم شما حذف می‌شوند
- فقط صاحب ربات می‌تواند این دستورات را اجرا کند
"""

class StoryDownloaderPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.download_path = "downloads/stories"
        self.auto_download_tasks = {}
        self.logger = logging.getLogger(__name__)
        os.makedirs(self.download_path, exist_ok=True)

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/downloadstory|دانلود\s+استوری)(?:\s+(.+))?$'))
        async def download_story_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            target = event.pattern_match.group(1)
            if not target:
                return await event.reply("❌ لطفا یک نام کاربری، آیدی یا لینک استوری وارد کنید")
            await event.reply("🔍 در حال جستجوی استوری‌ها...")
            try:
                user_entity, story_id = await self._get_entity(target)
                if not user_entity:
                    return await event.reply("❌ کاربر یافت نشد یا ورودی نامعتبر است")
                success, file_paths = await self._download_stories(event, user_entity, latest_only=True, story_id=story_id)
                if success:
                    await self._cleanup_files(file_paths)
                    await event.reply(f"✅ استوری مورد نظر از {user_entity.first_name} با موفقیت پردازش شد")
                else:
                    await event.reply(f"ℹ️ استوری‌ای برای {user_entity.first_name} یافت نشد")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/downloadallstories|دانلود\s+تمام\s+استوری‌ها)(?:\s+(.+))?$'))
        async def download_all_stories_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            target = event.pattern_match.group(1)
            if not target:
                return await event.reply("❌ لطفا یک نام کاربری، آیدی یا لینک استوری وارد کنید")
            await event.reply("🔍 در حال جستجوی استوری‌ها...")
            try:
                user_entity, story_id = await self._get_entity(target)
                if not user_entity:
                    return await event.reply("❌ کاربر یافت نشد یا ورودی نامعتبر است")
                success, file_paths = await self._download_stories(event, user_entity, latest_only=False, story_id=story_id)
                if success:
                    await self._cleanup_files(file_paths)
                    await event.reply(f"✅ استوری‌های {user_entity.first_name} با موفقیت پردازش شدند")
                else:
                    await event.reply(f"ℹ️ استوری‌ای برای {user_entity.first_name} یافت نشد")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/autostory|دانلود\s+خودکار\s+استوری)(?:\s+(.+))?(?:\s+(\d+))?$'))
        async def auto_story_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            match = event.pattern_match
            target = match.group(1)
            interval = match.group(2)
            if not target:
                return await event.reply("❌ لطفا یک نام کاربری، آیدی یا لینک وارد کنید")
            interval = int(interval) if interval and interval.isdigit() else 30
            try:
                user_entity, _ = await self._get_entity(target)
                if not user_entity:
                    return await event.reply("❌ کاربر یافت نشد یا ورودی نامعتبر است")
                user_id = str(user_entity.id)
                if user_id in self.auto_download_tasks:
                    self.auto_download_tasks[user_id].cancel()
                task = asyncio.create_task(self._auto_download_stories(event, user_entity, interval))
                self.auto_download_tasks[user_id] = task
                await event.reply(f"✅ دانلود خودکار برای {user_entity.first_name} هر {interval} دقیقه فعال شد")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/stopautostory|متوقف\s+دانلود\s+خودکار\s+استوری)(?:\s+(.+))?$'))
        async def stop_auto_story_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            target = event.pattern_match.group(1)
            if not target:
                return await event.reply("❌ لطفا یک نام کاربری، آیدی یا لینک وارد کنید")
            try:
                user_entity, _ = await self._get_entity(target)
                if not user_entity:
                    return await event.reply("❌ کاربر یافت نشد یا ورودی نامعتبر است")
                user_id = str(user_entity.id)
                if user_id in self.auto_download_tasks:
                    self.auto_download_tasks[user_id].cancel()
                    del self.auto_download_tasks[user_id]
                    await event.reply(f"✅ دانلود خودکار برای {user_entity.first_name} غیرفعال شد")
                else:
                    await event.reply(f"ℹ️ دانلود خودکار برای {user_entity.first_name} فعال نبود")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/liststoryusers|لیست\s+استوری\s+کاربران)$'))
        async def list_story_users_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if not self.auto_download_tasks:
                return await event.reply("ℹ️ هیچ کاربری با دانلود خودکار فعال وجود ندارد")
            response = "👥 کاربران با دانلود خودکار فعال:\n\n"
            for user_id in self.auto_download_tasks:
                try:
                    user = await self.client.get_entity(int(user_id))
                    if user.username:
                        response += f"• {user.first_name} (@{user.username})\n"
                    else:
                        response += f"• آیدی کاربر: {user_id}\n"
                except:
                    response += f"• آیدی کاربر: {user_id}\n"
            await event.reply(response)

    async def _get_entity(self, target):
        """
        استخراج موجودیت کاربر و (اختیاری) story_id از نام کاربری، آیدی یا لینک.
        پشتیبانی از لینک‌های:
          • https://t.me/stories/username/123
          • https://t.me/username/s/123
        """
        try:
            story_id = None

            # بررسی لینک با قالب t.me/stories/username/123
            m1 = re.search(r't\.me/stories/?@?(\w+)(?:/(\d+))?', target)
            # بررسی لینک با قالب t.me/username/s/123
            m2 = re.search(r't\.me/?@?(\w+)/s/(\d+)', target)

            if m1:
                username = m1.group(1)
                if m1.group(2):
                    story_id = int(m1.group(2))
                return await self.client.get_entity(username), story_id
            elif m2:
                username = m2.group(1)
                story_id = int(m2.group(2))
                return await self.client.get_entity(username), story_id
            elif target.isdigit():
                return await self.client.get_entity(int(target)), None
            else:
                username = target.lstrip('@')
                return await self.client.get_entity(username), None
        except Exception as e:
            self.logger.error(f"خطا در دریافت موجودیت: {str(e)}")
            return None, None

    async def _download_stories(self, event, user_entity, latest_only=False, story_id=None):
        try:
            user_id = user_entity.id
            user_dir = os.path.join(self.download_path, f"{user_entity.username or user_id}")
            os.makedirs(user_dir, exist_ok=True)
            full_user = await self.client(GetFullUserRequest(user_entity))
            stories = getattr(getattr(full_user, 'full_user', None), 'stories', None)
            if not stories or not stories.stories:
                return False, None
            user_stories = stories.stories

            if story_id:
                user_stories = [s for s in user_stories if s.id == story_id]
            elif latest_only:
                user_stories = [user_stories[0]]

            file_paths = []
            for story in user_stories:
                timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
                if not hasattr(story, 'media'):
                    continue
                if hasattr(story.media, 'photo'):
                    ext = 'jpg'
                elif hasattr(story.media, 'document'):
                    ext = 'mp4'
                else:
                    continue
                file_path = os.path.join(user_dir, f"story_{story.id}_{timestamp}.{ext}")
                await self.client.download_media(story.media, file_path)
                file_paths.append(file_path)
                caption = f"استوری از {user_entity.first_name}"
                if hasattr(story, 'caption') and story.caption:
                    caption += f"\nکپشن: {story.caption}"
                await self.client.send_file(event.chat_id, file_path, caption=caption)
            return True, file_paths
        except FloodWaitError as e:
            self.logger.error(f"Flood wait: {e}")
            return False, None
        except UserNotParticipantError:
            self.logger.error("User not participant - cannot access stories.")
            return False, None
        except Exception as e:
            self.logger.error(f"Download error: {e}")
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

    async def _auto_download_stories(self, event, user_entity, interval_minutes):
        try:
            while True:
                success, file_paths = await self._download_stories(event, user_entity, latest_only=True)
                if success:
                    await self._cleanup_files(file_paths)
                await asyncio.sleep(interval_minutes * 60)
        except asyncio.CancelledError:
            pass
        except Exception as e:
            self.logger.error(f"Auto-download error: {e}")
            await event.reply(f"❌ خطا در دانلود خودکار برای {user_entity.first_name}: {str(e)}")
