# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from collections import OrderedDict
from .base import Plugin
from telethon import events
import os
import random
import asyncio
from .db_utils import execute_query  # Your async DB utility

HELP = """  
⚠️ **مدیریت هوشمند لیست دشمنان** ⚠️  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های کلیدی**:  
• افزودن/حذف کاربران به لیست دشمنان با **آیدی، یوزرنیم یا ریپلای**  
• پاسخ خودکار به پیام‌های دشمنان با **جملات تصادفی** از فایل Fosh.txt  
• نمایش لیست دشمنان با اطلاعات کامل  
• پاکسازی یکجا و مدیریت آسان لیست  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات اصلی**:

/addenemy [آیدی/یوزرنیم] ➤ افزودن دشمن  
/delenemy [آیدی/یوزرنیم] ➤ حذف دشمن  
/clearenemy ➤ پاکسازی کامل  
/enemylist ➤ لیست دشمنان

فارسی:
افزودن دشمن | حذف دشمن | پاکسازی دشمن | لیست دشمنان

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ نمونه:
ریپلای کنید و بنویسید `افزودن دشمن`  
"""

class LRUCache:
    def __init__(self, capacity=1000):
        self.capacity = capacity
        self.cache = OrderedDict()

    def get(self, key):
        if key in self.cache:
            self.cache.move_to_end(key)
            return self.cache[key]
        return None

    def set(self, key, value=True):
        self.cache[key] = value
        self.cache.move_to_end(key)
        if len(self.cache) > self.capacity:
            self.cache.popitem(last=False)

    def delete(self, key):
        if key in self.cache:
            del self.cache[key]

    def clear(self):
        self.cache.clear()


class EnemyManagerPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.fosh_file = "Fosh.txt"
        self.cache = LRUCache(capacity=1000)
        asyncio.create_task(self._init_db_async())
        asyncio.create_task(self.start())

        if not os.path.exists(self.fosh_file):
            with open(self.fosh_file, "w", encoding="utf-8") as f:
                f.write("سلام\nسلام\nخوبی؟")

    async def _init_db_async(self):
        await execute_query("""
            CREATE TABLE IF NOT EXISTS enemies (
                owner_id VARCHAR(32),
                user_id VARCHAR(32),
                PRIMARY KEY (owner_id, user_id)
            )
        """, None, fetch=False)

    async def start(self):
        results = await execute_query(
            "SELECT user_id FROM enemies WHERE owner_id = %s",
            (self.owner_id,), fetch=True
        )
        for row in results or []:
            user_id = row['user_id'] if isinstance(row, dict) else row[0]
            self.cache.set(user_id)

    async def _resolve_target(self, event, target):
        if event.is_reply:
            reply = await event.get_reply_message()
            return str(reply.sender_id)
        if target:
            if target.isdigit():
                return target
            try:
                entity = await self.client.get_entity(target)
                return str(entity.id)
            except:
                return None
        return None

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/addenemy|افزودن\s+دشمن)(?:\s+(.+))?$'))
        async def addenemy(event):
            if str(event.sender_id) != self.owner_id: return
            target = event.pattern_match.group(1)
            user_id = await self._resolve_target(event, target)
            if not user_id:
                await event.reply("❌ کاربر نامعتبر!")
                return
            await execute_query(
                "INSERT IGNORE INTO enemies (owner_id, user_id) VALUES (%s, %s)",
                (self.owner_id, str(user_id)), fetch=False
            )
            self.cache.set(user_id)
            await event.reply(f"✅ کاربر {user_id} به لیست دشمنان اضافه شد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/delenemy|حذف\s+دشمن)(?:\s+(.+))?$'))
        async def delenemy(event):
            if str(event.sender_id) != self.owner_id: return
            target = event.pattern_match.group(1)
            user_id = await self._resolve_target(event, target)
            if not user_id:
                await event.reply("❌ کاربر نامعتبر!")
                return
            await execute_query(
                "DELETE FROM enemies WHERE owner_id = %s AND user_id = %s",
                (self.owner_id, str(user_id)), fetch=False
            )
            self.cache.delete(user_id)  # ✅ remove from cache
            await event.reply(f"✅ کاربر {user_id} از لیست دشمنان حذف شد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/clearenemy|پاکسازی\s+دشمن)$'))
        async def clearenemy(event):
            if str(event.sender_id) != self.owner_id: return
            await execute_query(
                "DELETE FROM enemies WHERE owner_id = %s",
                (self.owner_id,), fetch=False
            )
            self.cache.clear()  # ✅ clear cache
            await event.reply("✅ لیست دشمنان پاکسازی شد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/enemylist|لیست\s+دشمنان)$'))
        async def enemylist(event):
            if str(event.sender_id) != self.owner_id: return
            enemies = await execute_query(
                "SELECT user_id FROM enemies WHERE owner_id = %s",
                (self.owner_id,), fetch=True
            )
            if not enemies:
                await event.reply("❌ لیست دشمنان خالی است")
                return
            msg = "👥 لیست دشمنان:\n\n"
            for row in enemies:
                uid = row[0]
                try:
                    entity = await self.client.get_entity(int(uid))
                    name = getattr(entity, "first_name", "")
                    username = f"@{entity.username}" if entity.username else uid
                    msg += f"• {name} ({username})\n"
                except:
                    msg += f"• {uid}\n"
            await event.reply(msg)

        @self.client.on(events.NewMessage(incoming=True))
        async def auto_reply(event):
            if not event.sender_id: return
            user_id = str(event.sender_id)
            is_enemy = self.cache.get(user_id)
            if is_enemy is None:
                result = await execute_query(
                    "SELECT 1 FROM enemies WHERE owner_id = %s AND user_id = %s",
                    (self.owner_id, user_id), fetch=True
                )
                if result:
                    self.cache.set(user_id)
                    is_enemy = True
            if is_enemy:
                try:
                    with open(self.fosh_file, "r", encoding="utf-8") as f:
                        lines = f.readlines()
                    if lines:
                        await event.reply(random.choice(lines).strip())
                except Exception as e:
                    print(f"Error reading Fosh.txt: {e}")
