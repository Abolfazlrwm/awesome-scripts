# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, types
import sqlite3
import os
import time
import asyncio
import random
from datetime import datetime

HELP = """
⚠️ **پلاگین AFK حرفه‌ای** ⚠️

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت‌های کلیدی**:
• تنظیم AFK با پیام دلخواه و **استیکر/عکس** 🖼️
• نمایش مدت زمان AFK با **دقت ثانیه** ⏱️
• پاسخ‌های تصادفی از فایل `afk_messages.txt` 🎲
• آمار کامل AFK (تعداد دفعات، کل زمان) 📊
• سیستم **چندکاربره** برای گروه‌ها 👥
• دستور `/afkstats` برای مشاهده آمار 🔍
• سیستم **ضد اسپم** برای جلوگیری از تگ‌های مکرر 🛡️

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات**:
• `/afk [پیام/ریپلای]` ➔ فعال‌سازی AFK
• `/back` ➔ غیرفعال‌سازی AFK
• `/afkstats [یوزر]` ➔ نمایش آمار کاربر
• `/afkmessages` ➔ مدیریت پیام‌های تصادفی

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **نمونه**:
1. فعال‌سازی AFK با عکس:
   `/afk` (با ریپلای روی یک عکس)
2. تگ کاربر AFK:
   ربات پاسخ می‌دهد: "🚫 کاربر 2 ساعت و 15 دقیقه پیش AFK شد! (پیام تصادفی: مشغول ریکاوری باتری هستم!)"
3. مشاهده آمار:
   `/afkstats` → "📊 آمار AFK شما: 3 بار | کل زمان: 12 ساعت"
"""

class AdvancedAFKPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.db_path = "data/afk_advanced.db"
        self._init_db()
        # سیستم ضد اسپم - ذخیره آخرین زمان پاسخ به هر کاربر
        self.mention_cooldowns = {}
        # مدت زمان کولداون به ثانیه
        self.cooldown_time = 60  # 60 ثانیه، می‌توانید تغییر دهید

    def _init_db(self):
        os.makedirs(os.path.dirname(self.db_path), exist_ok=True)
        with sqlite3.connect(self.db_path) as conn:
            conn.execute("""
                CREATE TABLE IF NOT EXISTS afk (
                    user_id TEXT PRIMARY KEY,
                    afk_message TEXT,
                    start_time REAL,
                    media_id TEXT,
                    total_afk INTEGER DEFAULT 0,
                    total_duration REAL DEFAULT 0
                )
            """)
            # اضافه کردن جدول جدید برای پیام‌های اختصاصی
            conn.execute("""
                CREATE TABLE IF NOT EXISTS afk_messages (
                    user_id TEXT,
                    message TEXT,
                    PRIMARY KEY(user_id, message)
                )
            """)
            conn.commit()

    async def _db_execute(self, query, params):
        async with asyncio.Lock():
            with sqlite3.connect(self.db_path) as conn:
                conn.execute(query, params)
                conn.commit()

    async def _db_fetch(self, query, params):
        async with asyncio.Lock():
            with sqlite3.connect(self.db_path) as conn:
                cursor = conn.execute(query, params)
                return cursor.fetchone()

    async def add_afk_message(self, user_id, message):
        await self._db_execute(
            "INSERT OR IGNORE INTO afk_messages VALUES (?, ?)",
            (user_id, message)
        )

    async def remove_afk_message(self, user_id, message):
        await self._db_execute(
            "DELETE FROM afk_messages WHERE user_id = ? AND message = ?",
            (user_id, message)
        )

    async def _get_random_message(self, user_id):
        messages = await self._db_fetch(
            "SELECT message FROM afk_messages WHERE user_id = ?",
            (user_id,)
        )
        if messages:
            return random.choice([msg[0] for msg in messages])
        return "فعلاً در دسترس نیستم!"

    async def _format_duration(self, seconds):
        hours = int(seconds // 3600)
        minutes = int((seconds % 3600) // 60)
        seconds = int(seconds % 60)
        return f"{hours} ساعت و {minutes} دقیقه و {seconds} ثانیه"

    async def _check_spam(self, sender_id):
        """بررسی اسپم و کولداون برای پاسخ‌دهی به منشن‌ها"""
        current_time = time.time()
        # بررسی آیا کاربر در کولداون است
        if sender_id in self.mention_cooldowns:
            last_mention_time = self.mention_cooldowns[sender_id]
            # اگر هنوز در زمان کولداون هستیم
            if current_time - last_mention_time < self.cooldown_time:
                return False  # اسپم است، پاسخ نده
        
        # بروزرسانی زمان آخرین منشن
        self.mention_cooldowns[sender_id] = current_time
        return True  # اسپم نیست، می‌توان پاسخ داد

    async def handle_events(self):
        # دستور /afk با پشتیبانی از رسانه
        @self.client.on(events.NewMessage(pattern=r'^/afk(?:\s+(.+))?$'))
        async def afk_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            user_id = str(event.sender_id)
            afk_message = event.pattern_match.group(1) or await self._get_random_message(user_id)
            start_time = time.time()
            media_id = None

            if event.is_reply:
                reply = await event.get_reply_message()
                if reply.media:
                    media_id = reply.id  # ذخیره ID رسانه برای استفاده بعدی

            try:
                await self._db_execute(
                    """INSERT OR REPLACE INTO afk 
                    (user_id, afk_message, start_time, media_id, total_afk, total_duration)
                    VALUES (?, ?, ?, ?, COALESCE((SELECT total_afk + 1 FROM afk WHERE user_id = ?), 1),
                    COALESCE((SELECT total_duration FROM afk WHERE user_id = ?), 0))""",
                    (user_id, afk_message, start_time, media_id, user_id, user_id)
                )
                reply_msg = await event.reply(f"✅ **وضعیت AFK فعال شد!**\nپیام: `{afk_message}`")
                if media_id and reply.media:
                    await self.client.send_file(event.chat_id, reply.media, caption=reply_msg.text)
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        # دستور /back
        @self.client.on(events.NewMessage(pattern=r'^/back$'))
        async def back_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            user_id = str(event.sender_id)
            afk_data = await self._db_fetch("SELECT start_time FROM afk WHERE user_id = ?", (user_id,))
            if not afk_data:
                return

            duration = time.time() - afk_data[0]
            await self._db_execute(
                "UPDATE afk SET total_duration = total_duration + ? WHERE user_id = ?",
                (duration, user_id)
            )
            await self._db_execute("DELETE FROM afk WHERE user_id = ?", (user_id,))
            await event.reply(f"🎉 **خوش برگشتی!**\nمدت AFK: {await self._format_duration(duration)}")

        # دستور /afkstats
        @self.client.on(events.NewMessage(pattern=r'^/afkstats(?:\s+@?(\w+))?$'))
        async def stats_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            target = event.pattern_match.group(1) or str(event.sender_id)
            user_data = await self._db_fetch(
                "SELECT total_afk, total_duration FROM afk WHERE user_id = ?", 
                (target,)
            )
            if not user_data:
                await event.reply("ℹ️ هیچ آماری یافت نشد!")
                return

            total_afk, total_duration = user_data
            await event.reply(
                f"📊 **آمار AFK کاربر:**\n"
                f"• تعداد دفعات: `{total_afk}`\n"
                f"• کل زمان: `{await self._format_duration(total_duration)}`"
            )

        # دستور مدیریت پیام‌های AFK
        @self.client.on(events.NewMessage(pattern=r'^/afkmessages (add|remove) (.+)$'))
        async def afkmsg_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            action = event.pattern_match.group(1)
            message = event.pattern_match.group(2)
            user_id = str(event.sender_id)

            if action == 'add':
                await self.add_afk_message(user_id, message)
                await event.reply(f"✅ پیام `{message}` اضافه شد!")
            elif action == 'remove':
                await self.remove_afk_message(user_id, message)
                await event.reply(f"✅ پیام `{message}` حذف شد!")

        # پاسخ به تگ‌ها و غیرفعال‌سازی خودکار
        @self.client.on(events.NewMessage(incoming=True))
        async def message_handler(event):
            sender_id = str(event.sender_id)
            
            # غیرفعال کردن AFK اگر کاربر پیام داد
            if sender_id == self.owner_id:
                afk_data = await self._db_fetch("SELECT 1 FROM afk WHERE user_id = ?", (sender_id,))
                if afk_data:
                    await self._db_execute("DELETE FROM afk WHERE user_id = ?", (sender_id,))
                    await event.reply("🔔 **وضعیت AFK شما غیرفعال شد!**")

            # بررسی وجود mentions در پیام
            if event.message.entities:
                for entity in event.message.entities:
                    if isinstance(entity, types.MessageEntityMention):
                        # استخراج متن mention
                        mention_text = event.message.message[entity.offset:entity.offset + entity.length]
                        # اگر mention با @ شروع شده باشد، آن را به user_id تبدیل کنید
                        if mention_text.startswith('@'):
                            username = mention_text[1:]  # حذف @
                            try:
                                # دریافت user_id از username
                                user = await self.client.get_entity(username)
                                user_id_str = str(user.id)
                                
                                # بررسی کنید که آیا کاربر منشن شده در حالت AFK است
                                afk_data = await self._db_fetch(
                                    "SELECT afk_message, start_time, media_id FROM afk WHERE user_id = ?",
                                    (user_id_str,)
                                )
                                if afk_data:
                                    # بررسی اسپم برای کاربر فرستنده
                                    if not await self._check_spam(sender_id):
                                        # اگر کاربر در کولداون است، پاسخ نده
                                        return
                                    
                                    afk_msg, start_time, media_id = afk_data
                                    duration = await self._format_duration(time.time() - start_time)
                                    response = (
                                        f"🚫 کاربر <a href='tg://user?id={user_id_str}'>AFK</a> است!\n"
                                        f"• مدت: {duration}\n"
                                        f"• پیام: {afk_msg}"
                                    )
                                    if media_id:
                                        await event.reply(response, file=media_id)
                                    else:
                                        await event.reply(response)
                            except Exception as e:
                                print(f"خطا در دریافت اطلاعات کاربر: {e}")