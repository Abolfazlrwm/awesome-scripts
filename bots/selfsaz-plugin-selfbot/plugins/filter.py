# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import sqlite3
import os
import re
import asyncio

HELP = """  
🛡️ **مدیریت فیلتر کلمات** 🛡️  

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت‌های کلیدی**:
• افزودن/حذف کلمات به لیست فیلتر
• حذف خودکار پیام‌های حاوی کلمات فیلتر شده
• پاسخ اختیاری به پیام‌های فیلتر شده
• نمایش لیست کلمات فیلتر شده
• پاکسازی یکجای لیست فیلتر

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات اصلی**:

**انگلیسی:**
  `/filter [کلمه]` ➤ افزودن کلمه به لیست فیلتر  
  `/unfilter [کلمه]` ➤ حذف کلمه از لیست فیلتر  
  `/clearfilter` ➤ پاکسازی کامل لیست فیلتر  
  `/filterlist` ➤ نمایش لیست کلمات فیلتر شده  
  `/filtermode [delete/warn]` ➤ تنظیم واکنش به کلمات فیلتر شده

**فارسی:**
  `فیلتر [کلمه]` ➤ افزودن کلمه به لیست فیلتر  
  `حذف فیلتر [کلمه]` ➤ حذف کلمه از لیست فیلتر  
  `پاکسازی فیلتر` ➤ پاکسازی کامل لیست فیلتر  
  `لیست فیلتر` ➤ نمایش لیست کلمات فیلتر شده  
  `حالت فیلتر [حذف/اخطار]` ➤ تنظیم واکنش به کلمات فیلتر شده

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **نمونه اجرا**:
1. افزودن کلمه به فیلتر:  
   `/filter test` یا `فیلتر test`
2. حذف کلمه از فیلتر:  
   `/unfilter test` یا `حذف فیلتر test`
3. پاکسازی فیلتر:  
   `/clearfilter` یا `پاکسازی فیلتر`
4. نمایش لیست:  
   `/filterlist` یا `لیست فیلتر`
5. تنظیم حالت:  
   `/filtermode delete` یا `حالت فیلتر حذف`

⚠️ **نکات مهم**:
- فیلتر به صورت غیرحساس به بزرگی و کوچکی حروف عمل می‌کند  
- می‌توانید واکنش به کلمات فیلتر شده را با دستور `/filtermode` تنظیم کنید  
- برای عملکرد بهتر، ربات باید در چت مورد نظر حضور داشته باشد
"""

class WordFilterPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.db_path = "data/filters.db"
        self.filter_mode = "delete"  # Default mode: "delete" or "warn"
        self._init_db()

    def _init_db(self):
        # Ensure the data directory exists
        os.makedirs(os.path.dirname(self.db_path), exist_ok=True)
        
        db_exists = os.path.exists(self.db_path)
        with sqlite3.connect(self.db_path) as conn:
            if not db_exists:
                conn.execute("""
                    CREATE TABLE IF NOT EXISTS filtered_words (
                        owner_id TEXT,
                        word TEXT,
                        PRIMARY KEY (owner_id, word)
                    )
                """)
                conn.execute("""
                    CREATE TABLE IF NOT EXISTS settings (
                        owner_id TEXT PRIMARY KEY,
                        filter_mode TEXT DEFAULT 'delete'
                    )
                """)
                conn.commit()
            else:
                cursor = conn.execute("SELECT * FROM sqlite_master WHERE type='table' AND name='filtered_words'")
                if cursor.fetchone() is None:
                    conn.execute("""
                        CREATE TABLE filtered_words (
                            owner_id TEXT,
                            word TEXT,
                            PRIMARY KEY (owner_id, word)
                        )
                    """)
                    conn.commit()
                cursor = conn.execute("SELECT * FROM sqlite_master WHERE type='table' AND name='settings'")
                if cursor.fetchone() is None:
                    conn.execute("""
                        CREATE TABLE settings (
                            owner_id TEXT PRIMARY KEY,
                            filter_mode TEXT DEFAULT 'delete'
                        )
                    """)
                    conn.commit()
            
            cursor = conn.execute("SELECT filter_mode FROM settings WHERE owner_id = ?", (self.owner_id,))
            result = cursor.fetchone()
            if result:
                self.filter_mode = result[0]
            else:
                conn.execute("INSERT INTO settings (owner_id, filter_mode) VALUES (?, ?)", (self.owner_id, self.filter_mode))
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
                return cursor.fetchall()

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/filter|فیلتر)(?:\s+(.+))?$'))
        async def filter_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            word = event.pattern_match.group(1)
            if not word:
                await event.reply("❌ لطفاً کلمه مورد نظر برای فیلتر را وارد کنید")
                return

            word = word.lower().strip()
            try:
                await self._db_execute(
                    "INSERT OR IGNORE INTO filtered_words (owner_id, word) VALUES (?, ?)",
                    (self.owner_id, word)
                )
                await event.reply(f"✅ کلمه «{word}» به لیست فیلتر اضافه شد")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/unfilter|حذف\s+فیلتر)(?:\s+(.+))?$'))
        async def unfilter_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            word = event.pattern_match.group(1)
            if not word:
                await event.reply("❌ لطفاً کلمه مورد نظر برای حذف از فیلتر را وارد کنید")
                return

            word = word.lower().strip()
            try:
                await self._db_execute(
                    "DELETE FROM filtered_words WHERE owner_id = ? AND word = ?",
                    (self.owner_id, word)
                )
                await event.reply(f"✅ کلمه «{word}» از لیست فیلتر حذف شد")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/clearfilter|پاکسازی\s+فیلتر)$'))
        async def clearfilter_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            try:
                await self._db_execute(
                    "DELETE FROM filtered_words WHERE owner_id = ?",
                    (self.owner_id,)
                )
                await event.reply("✅ لیست کلمات فیلتر شده پاکسازی شد")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/filterlist|لیست\s+فیلتر)$'))
        async def filterlist_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            words = await self._db_fetch(
                "SELECT word FROM filtered_words WHERE owner_id = ? ORDER BY word",
                (self.owner_id,)
            )
            
            if not words:
                await event.reply("❌ لیست کلمات فیلتر شده خالی است")
                return

            words_list = [word[0] for word in words]
            response = f"🔍 **لیست کلمات فیلتر شده ({len(words_list)}):**\n\n"
            response += "\n".join([f"• {word}" for word in words_list])
            response += f"\n\n🔧 **حالت فعلی:** {self.filter_mode}"
            
            await event.reply(response)

        @self.client.on(events.NewMessage(pattern=r'^(?:/filtermode|حالت\s+فیلتر)(?:\s+(.+))?$'))
        async def filtermode_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            mode = event.pattern_match.group(1)
            if not mode:
                await event.reply("❌ لطفاً حالت صحیح را وارد کنید: delete یا warn (یا حذف/اخطار)")
                return

            mode = mode.lower().strip()
            if mode in ['حذف']:
                mode = "delete"
            elif mode in ['اخطار']:
                mode = "warn"
            elif mode not in ["delete", "warn"]:
                await event.reply("❌ لطفاً حالت صحیح را وارد کنید: delete یا warn (یا حذف/اخطار)")
                return

            try:
                await self._db_execute(
                    "INSERT OR REPLACE INTO settings (owner_id, filter_mode) VALUES (?, ?)",
                    (self.owner_id, mode)
                )
                self.filter_mode = mode
                
                mode_persian = "حذف پیام" if mode == "delete" else "اخطار"
                await event.reply(f"✅ حالت فیلتر به «{mode_persian}» تغییر یافت")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(incoming=True))
        async def filter_message_handler(event):
            if not event.sender_id or str(event.sender_id) == self.owner_id:
                return

            if not event.raw_text:
                return
                
            message_text = event.raw_text.lower()
            filtered_words = await self._db_fetch(
                "SELECT word FROM filtered_words WHERE owner_id = ?",
                (self.owner_id,)
            )
            
            for word_tuple in filtered_words:
                word = word_tuple[0]
                pattern = r'\b' + re.escape(word) + r'\b'
                if re.search(pattern, message_text):
                    if self.filter_mode == "delete":
                        await event.delete()
                        return
                    elif self.filter_mode == "warn":
                        sender = await event.get_sender()
                        sender_name = sender.first_name if hasattr(sender, 'first_name') else "کاربر"
                        await event.reply(f"⚠️ {sender_name}: پیام شما حاوی کلمات فیلتر شده است!")
                        return
