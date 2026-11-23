# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telethon import events
from datetime import datetime
import aiosqlite
import asyncio
from pathlib import Path
import logging
from .base import Plugin

logger = logging.getLogger(__name__)

HELP = """
⚠️ **سیستم پیشرفته مدیریت اخطارها** ⚠️

▬▬▬▬▬▬▬▬▬▬▬▬
🔧 **دستورات اصلی (انگلیسی با "/")**:
- `/warn [ریپلای/یوزرنیم/آیدی] [دلیل]` ➔ ثبت اخطار برای کاربر
- `/warns [ریپلای/یوزرنیم/آیدی]` ➔ نمایش لیست اخطارهای کاربر
- `/unwarn [شماره اخطار]` ➔ حذف یک اخطار خاص
- `/resetwarns [ریپلای/یوزرنیم/آیدی]` ➔ ریست تمام اخطارهای کاربر
- `/setwarnlimit [عدد] [ban/mute]` ➔ تنظیم حد نصاب اخطار و نوع مجازات

▬▬▬▬▬▬▬▬▬▬▬▬
• **فارسی (بدون "/" با فاصله‌های اضافی)**:
   `اخطار           [ریپلای/یوزرنیم/آیدی] [دلیل]` ➔ ثبت اخطار برای کاربر
   `لیست  اخطار    [ریپلای/یوزرنیم/آیدی]` ➔ نمایش لیست اخطارهای کاربر
   `حذف  اخطار     [شماره اخطار]` ➔ حذف یک اخطار خاص
   `ریست  اخطار    [ریپلای/یوزرنیم/آیدی]` ➔ ریست تمام اخطارهای کاربر
   `تنظیم  حد  اخطار    [عدد] [ban/mute]` ➔ تنظیم حد نصاب اخطار و نوع مجازات

▬▬▬▬▬▬▬▬▬▬▬▬
📝 **نمونه استفاده**:
1. ثبت اخطار:
   `/warn @username ارسال محتوای نامناسب`
   یا به صورت فارسی:
   `اخطار           @username ارسال محتوای نامناسب`
2. نمایش اخطارها:
   `/warns @username`
   یا:
   `لیست  اخطار    @username`
3. حذف اخطار:
   `/unwarn 1`
   یا:
   `حذف  اخطار     1`
4. ریست اخطارها:
   `/resetwarns @username`
   یا:
   `ریست  اخطار    @username`
5. تنظیم حد نصاب:
   `/setwarnlimit 3 ban`
   یا:
   `تنظیم  حد  اخطار    3 ban`

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **توضیحات**:
- **ریپلای/یوزرنیم/آیدی**: می‌توانید با ریپلای روی پیام کاربر، یوزرنیم یا آیدی او را وارد کنید.
- **دلیل**: دلیل اخطار را می‌توانید به صورت اختیاری وارد کنید.
- **حد نصاب**: پس از رسیدن به حد نصاب اخطارها، کاربر به صورت خودکار بن یا سکوت می‌شود.
- **مجازات**: می‌توانید بین `ban` (بن) و `mute` (سکوت) انتخاب کنید.

▬▬▬▬▬▬▬▬▬▬▬▬
⚠️ **نکات مهم**:
- فقط مالک ربات می‌تواند از این دستورات استفاده کند.
- برای عملکرد بهتر، ربات باید در گروه ادمین باشد.
- اطلاعات اخطارها در دیتابیس ذخیره می‌شود.
"""

class WarnSystemPlugin(Plugin):
    def __init__(self, client, config, owner_id):
        super().__init__(client, config)
        self.owner_id = str(owner_id)
        self.db_path = "data/warnings.db"
        self._init_db()

    def _init_db(self):
        Path("data").mkdir(exist_ok=True)
        async def create_tables():
            async with aiosqlite.connect(self.db_path) as db:
                await db.execute("""
                    CREATE TABLE IF NOT EXISTS warns (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        user_id TEXT NOT NULL,
                        reason TEXT,
                        timestamp DATETIME,
                        chat_id TEXT NOT NULL
                    )""")
                await db.execute("""
                    CREATE TABLE IF NOT EXISTS warn_settings (
                        chat_id TEXT PRIMARY KEY,
                        warn_limit INTEGER DEFAULT 3,
                        action TEXT DEFAULT 'ban'
                    )""")
                await db.commit()
        asyncio.create_task(create_tables())

    async def _is_owner(self, event):
        """بررسی مالک بودن کاربر"""
        return str(event.sender_id) == self.owner_id

    async def handle_events(self):
        # English commands

        @self.client.on(events.NewMessage(pattern=r'^/warn(?:\s+(\S+))?(?:\s+(.+))?$'))
        async def warn_user(event):
            try:
                if not await self._is_owner(event):
                    return

                target, reason = await self.parse_target_with_reason(event)
                logger.info(f"Target: {target}, Reason: {reason}")

                if not target:
                    return await event.reply("❌ کاربر معتبری یافت نشد!")

                await self._execute_query(
                    """INSERT INTO warns (user_id, reason, timestamp, chat_id)
                    VALUES (?, ?, ?, ?)""",
                    (target, reason, datetime.now().isoformat(), str(event.chat_id)))
                
                user = await event.client.get_entity(int(target))
                msg = (
                    f"⚠️ به کاربر [{user.first_name}](tg://user?id={target}) اخطار داده شد!\n"
                    f"▬▬▬▬\n📝 **دلیل**: {reason or 'بدون دلیل'}\n"
                    f"{await self.check_warn_limit(target, event.chat_id)}"
                )
                await event.reply(msg)

            except Exception as e:
                logger.error(f"Error in warn_user: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

        @self.client.on(events.NewMessage(pattern=r'^/warns(?:\s+(\S+))?$'))
        async def show_warns(event):
            try:
                if not await self._is_owner(event):
                    return

                target = await self.parse_target(event)
                if not target:
                    return await event.reply("❌ کاربر معتبری یافت نشد!")

                warns = await self._fetch_query(
                    "SELECT id, reason, timestamp FROM warns WHERE user_id = ? AND chat_id = ?",
                    (target, str(event.chat_id)))
                
                if not warns:
                    return await event.reply("ℹ️ این کاربر هیچ اخطاری ندارد!")

                user = await event.client.get_entity(int(target))
                response = f"📜 لیست اخطارهای [{user.first_name}](tg://user?id={target}):\n▬▬▬▬\n"
                for warn in warns:
                    response += f"🔴 **#{warn[0]}** - {warn[1]}\n⏰ `{warn[2][:16]}`\n\n"
                
                total = len(warns)
                settings = await self._fetch_query(
                    "SELECT warn_limit, action FROM warn_settings WHERE chat_id = ?",
                    (str(event.chat_id),))
                limit, action = settings[0] if settings else (3, 'ban')
                response += f"🔔 مجموع اخطارها: {total}/{limit} (اتمام: {action})"
                await event.reply(response)

            except Exception as e:
                logger.error(f"Error in show_warns: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

        @self.client.on(events.NewMessage(pattern=r'^/unwarn(?:\s+(\d+))?$'))
        async def unwarn_handler(event):
            try:
                if not await self._is_owner(event):
                    return

                warn_id = event.pattern_match.group(1)
                if not warn_id:
                    return await event.reply("❌ شماره اخطار را وارد کنید!")

                await self._execute_query(
                    "DELETE FROM warns WHERE id = ? AND chat_id = ?",
                    (warn_id, str(event.chat_id)))
                await event.reply(f"✅ اخطار #{warn_id} حذف شد!")

            except Exception as e:
                logger.error(f"Error in unwarn_handler: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

        @self.client.on(events.NewMessage(pattern=r'^/resetwarns(?:\s+(\S+))?$'))
        async def reset_warns_handler(event):
            try:
                if not await self._is_owner(event):
                    return

                target = await self.parse_target(event)
                if not target:
                    return await event.reply("❌ کاربر معتبری یافت نشد!")

                await self._execute_query(
                    "DELETE FROM warns WHERE user_id = ? AND chat_id = ?",
                    (target, str(event.chat_id)))
                await event.reply(f"✅ تمام اخطارهای کاربر [{(await event.client.get_entity(int(target))).first_name}](tg://user?id={target}) ریست شد!")

            except Exception as e:
                logger.error(f"Error in reset_warns_handler: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

        @self.client.on(events.NewMessage(pattern=r'^/setwarnlimit(?:\s+(\d+))?(?:\s+(ban|mute))?$'))
        async def set_limit_handler(event):
            try:
                if not await self._is_owner(event):
                    return

                limit = event.pattern_match.group(1)
                action = event.pattern_match.group(2)
                if not limit or not action:
                    return await event.reply("❌ لطفا حد نصاب و نوع مجازات را وارد کنید!")

                await self._execute_query(
                    """INSERT OR REPLACE INTO warn_settings (chat_id, warn_limit, action)
                    VALUES (?, ?, ?)""",
                    (str(event.chat_id), int(limit), action))
                await event.reply(f"✅ حد نصاب اخطار به {limit} با مجازات {action} تنظیم شد!")

            except Exception as e:
                logger.error(f"Error in set_limit_handler: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

        # Persian commands (without "/" and with extra spaces)

        @self.client.on(events.NewMessage(pattern=r'^اخطار(?:\s+(\S+))?(?:\s+(.+))?$'))
        async def warn_user_farsi(event):
            try:
                if not await self._is_owner(event):
                    return

                target, reason = await self.parse_target_with_reason(event)
                logger.info(f"(فارسی) Target: {target}, Reason: {reason}")

                if not target:
                    return await event.reply("❌ کاربر معتبری یافت نشد!")

                await self._execute_query(
                    """INSERT INTO warns (user_id, reason, timestamp, chat_id)
                    VALUES (?, ?, ?, ?)""",
                    (target, reason, datetime.now().isoformat(), str(event.chat_id)))
                
                user = await event.client.get_entity(int(target))
                msg = (
                    f"⚠️ به کاربر [{user.first_name}](tg://user?id={target}) اخطار داده شد!\n"
                    f"▬▬▬▬\n📝 **دلیل**: {reason or 'بدون دلیل'}\n"
                    f"{await self.check_warn_limit(target, event.chat_id)}"
                )
                await event.reply(msg)

            except Exception as e:
                logger.error(f"(فارسی) Error in warn_user: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

        @self.client.on(events.NewMessage(pattern=r'^لیست\s+اخطار(?:\s+(\S+))?$'))
        async def show_warns_farsi(event):
            try:
                if not await self._is_owner(event):
                    return

                target = await self.parse_target(event)
                if not target:
                    return await event.reply("❌ کاربر معتبری یافت نشد!")

                warns = await self._fetch_query(
                    "SELECT id, reason, timestamp FROM warns WHERE user_id = ? AND chat_id = ?",
                    (target, str(event.chat_id)))
                
                if not warns:
                    return await event.reply("ℹ️ این کاربر هیچ اخطاری ندارد!")

                user = await event.client.get_entity(int(target))
                response = f"📜 لیست اخطارهای [{user.first_name}](tg://user?id={target}):\n▬▬▬▬\n"
                for warn in warns:
                    response += f"🔴 **#{warn[0]}** - {warn[1]}\n⏰ `{warn[2][:16]}`\n\n"
                
                total = len(warns)
                settings = await self._fetch_query(
                    "SELECT warn_limit, action FROM warn_settings WHERE chat_id = ?",
                    (str(event.chat_id),))
                limit, action = settings[0] if settings else (3, 'ban')
                response += f"🔔 مجموع اخطارها: {total}/{limit} (اتمام: {action})"
                await event.reply(response)

            except Exception as e:
                logger.error(f"(فارسی) Error in show_warns: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

        @self.client.on(events.NewMessage(pattern=r'^حذف\s+اخطار(?:\s+(\d+))?$'))
        async def unwarn_handler_farsi(event):
            try:
                if not await self._is_owner(event):
                    return

                warn_id = event.pattern_match.group(1)
                if not warn_id:
                    return await event.reply("❌ شماره اخطار را وارد کنید!")

                await self._execute_query(
                    "DELETE FROM warns WHERE id = ? AND chat_id = ?",
                    (warn_id, str(event.chat_id)))
                await event.reply(f"✅ اخطار #{warn_id} حذف شد!")

            except Exception as e:
                logger.error(f"(فارسی) Error in unwarn_handler: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

        @self.client.on(events.NewMessage(pattern=r'^ریست\s+اخطار(?:\s+(\S+))?$'))
        async def reset_warns_handler_farsi(event):
            try:
                if not await self._is_owner(event):
                    return

                target = await self.parse_target(event)
                if not target:
                    return await event.reply("❌ کاربر معتبری یافت نشد!")

                await self._execute_query(
                    "DELETE FROM warns WHERE user_id = ? AND chat_id = ?",
                    (target, str(event.chat_id)))
                await event.reply(f"✅ تمام اخطارهای کاربر [{(await event.client.get_entity(int(target))).first_name}](tg://user?id={target}) ریست شد!")

            except Exception as e:
                logger.error(f"(فارسی) Error in reset_warns_handler: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

        @self.client.on(events.NewMessage(pattern=r'^تنظیم\s+حد\s+اخطار(?:\s+(\d+))?(?:\s+(ban|mute))?$'))
        async def set_limit_handler_farsi(event):
            try:
                if not await self._is_owner(event):
                    return

                limit = event.pattern_match.group(1)
                action = event.pattern_match.group(2)
                if not limit or not action:
                    return await event.reply("❌ لطفا حد نصاب و نوع مجازات را وارد کنید!")

                await self._execute_query(
                    """INSERT OR REPLACE INTO warn_settings (chat_id, warn_limit, action)
                    VALUES (?, ?, ?)""",
                    (str(event.chat_id), int(limit), action))
                await event.reply(f"✅ حد نصاب اخطار به {limit} با مجازات {action} تنظیم شد!")

            except Exception as e:
                logger.error(f"(فارسی) Error in set_limit_handler: {e}", exc_info=True)
                await event.reply("❌ خطایی رخ داد!")

    async def parse_target(self, event):
        """پارس کردن هدف از ریپلای، یوزرنیم یا آیدی برای دستوراتی که دلیل ندارند"""
        target = None

        # اگر ریپلای شده باشد
        if event.is_reply:
            reply = await event.get_reply_message()
            target = str(reply.sender_id)
        else:
            # اگر یوزرنیم یا آیدی وارد شده باشد
            match = event.pattern_match.group(1)
            if match:
                try:
                    if match.isdigit():
                        target = match
                    else:
                        entity = await event.client.get_entity(match)
                        target = str(entity.id)
                except Exception as e:
                    logger.error(f"خطا در پارس کردن هدف: {e}")
                    return None

        return target

    async def parse_target_with_reason(self, event):
        """پارس کردن هدف و دلیل از ریپلای، یوزرنیم یا آیدی برای دستور warn"""
        target = None
        reason = None

        # اگر ریپلای شده باشد
        if event.is_reply:
            reply = await event.get_reply_message()
            target = str(reply.sender_id)
            # دلیل برای دستور `/warn`
            if event.pattern_match.group(2):
                reason = event.pattern_match.group(2)
        else:
            # اگر یوزرنیم یا آیدی وارد شده باشد
            match = event.pattern_match.group(1)
            if match:
                try:
                    if match.isdigit():
                        target = match
                    else:
                        entity = await event.client.get_entity(match)
                        target = str(entity.id)
                    # دلیل برای دستور `/warn`
                    if event.pattern_match.group(2):
                        reason = event.pattern_match.group(2)
                except Exception as e:
                    logger.error(f"خطا در پارس کردن هدف: {e}")
                    return None, None

        return target, reason

    async def check_warn_limit(self, user_id, chat_id):
        """اعمال مجازات توسط مالک"""
        total = len(await self._fetch_query(
            "SELECT id FROM warns WHERE user_id = ? AND chat_id = ?",
            (user_id, str(chat_id))))
        
        settings = await self._fetch_query(
            "SELECT warn_limit, action FROM warn_settings WHERE chat_id = ?",
            (str(chat_id),))
        
        limit, action = settings[0] if settings else (3, 'ban')
        
        if total >= limit:
            try:
                if action == 'ban':
                    await self.client.edit_permissions(chat_id, int(user_id), view_messages=False)
                    return "🚨 کاربر بن شد!"
                elif action == 'mute':
                    await self.client.edit_permissions(chat_id, int(user_id), send_messages=False)
                    return "🔇 کاربر سکوت شد!"
            except Exception as e:
                return f"⚠️ خطا در اعمال مجازات: {str(e)}"
        return f"🔔 تعداد اخطارها: {total}/{limit}"

    async def _execute_query(self, query, params=None):
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute(query, params or ())
            await db.commit()

    async def _fetch_query(self, query, params=None):
        async with aiosqlite.connect(self.db_path) as db:
            cursor = await db.execute(query, params or ())
            return await cursor.fetchall()
