# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, functions
from telethon.errors import ChatAdminRequiredError
from .db_utils import execute_query
import asyncio
import re
import logging

logger = logging.getLogger("MutePlugin")

HELP = """  
🔇 **مدیریت سکوت کاربران در چت‌ها** 🔇  

📌 دستورات:  
  `/mute [یوزرنیم/آیدی]` ➔ بی‌صدا کردن کاربر  
  `/unmute [یوزرنیم/آیدی]` ➔ لغو سکوت  
  `/mutelist` ➔ لیست کاربران بی‌صدا  
  `/cleanmute` ➔ حذف همه سکوت‌ها  
"""

class MutePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        # In-memory cache: maps chat_id (str) to a set of muted user_ids (str)
        self._muted = {}
        asyncio.create_task(self._init_db())

    async def _init_db(self):
        await execute_query("""
            CREATE TABLE IF NOT EXISTS muted_users (
                owner_id VARCHAR(32) NOT NULL,
                user_id VARCHAR(32) NOT NULL,
                chat_id VARCHAR(32) NOT NULL,
                muted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (owner_id, user_id, chat_id)
            )
        """, fetch=False)
        
        # Load existing mutes into the cache
        await self._load_muted_users_to_cache()
        
        logger.info("Mute table initialized and in-memory mute cache loaded.")

    async def _load_muted_users_to_cache(self):
        """Load all muted users from database into memory cache on startup"""
        result = await execute_query("""
            SELECT user_id, chat_id FROM muted_users WHERE owner_id = %s
        """, (self.owner_id,), fetch=True)
        
        for row in result:
            chat_id = row['chat_id']
            user_id = row['user_id']
            if chat_id not in self._muted:
                self._muted[chat_id] = set()
            self._muted[chat_id].add(user_id)
        
        logger.info(f"Loaded {len(result)} muted users into memory cache")

    # Database methods
    async def add_muted_user(self, user_id, chat_id):
        await execute_query("""
            INSERT IGNORE INTO muted_users (owner_id, user_id, chat_id)
            VALUES (%s, %s, %s)
        """, (self.owner_id, user_id, chat_id), fetch=False)

    async def remove_muted_user(self, user_id, chat_id):
        await execute_query("""
            DELETE FROM muted_users WHERE owner_id = %s AND user_id = %s AND chat_id = %s
        """, (self.owner_id, user_id, chat_id), fetch=False)

    async def get_muted_users(self, chat_id):
        result = await execute_query("""
            SELECT user_id FROM muted_users WHERE owner_id = %s AND chat_id = %s
        """, (self.owner_id, chat_id), fetch=True)
        return [row['user_id'] for row in result] if result else []

    async def remove_all_muted_users(self, chat_id):
        await execute_query("""
            DELETE FROM muted_users WHERE owner_id = %s AND chat_id = %s
        """, (self.owner_id, chat_id), fetch=False)
        # Clear in-memory for this chat:
        self._muted.pop(chat_id, None)

    # In-memory helper for immediate mute status:
    def is_user_muted_in_cache(self, user_id, chat_id):
        return user_id in self._muted.get(chat_id, set())

    # Event handlers
    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/mute|سکوت)(?:\s+@?(\w+))?(?:\s+(\d+))?$'))
        async def mute_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            user = await self._resolve_user(event)
            if not user or user.id == int(self.owner_id):
                return await event.reply("❌ کاربر معتبر نیست")
            
            # Determine chat_id: for group, use chat id; for private, use the user's id.
            chat_id = str(event.chat_id) if not event.is_private else str(user.id)
            user_id = str(user.id)

            await self.add_muted_user(user_id, chat_id)
            # Immediately update the in-memory cache:
            if chat_id not in self._muted:
                self._muted[chat_id] = set()
            self._muted[chat_id].add(user_id)

            await event.reply(f"🔇 {user.first_name} (ID: {user.id}) بی‌صدا شد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/unmute|حذف\s+سکوت)(?:\s+@?(\w+))?(?:\s+(\d+))?$'))
        async def unmute_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            user = await self._resolve_user(event)
            if not user:
                return await event.reply("❌ کاربر یافت نشد")

            chat_id = str(event.chat_id) if not event.is_private else str(user.id)
            user_id = str(user.id)

            await self.remove_muted_user(user_id, chat_id)
            # Update in-memory cache immediately:
            if chat_id in self._muted:
                self._muted[chat_id].discard(user_id)

            await event.reply(f"🔈 {user.first_name} (ID: {user.id}) از سکوت خارج شد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/mutelist|لیست\s+سکوت)$'))
        async def list_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            chat_id = str(event.chat_id)
            # Use in-memory cache if available, fallback to DB
            muted_users = list(self._muted.get(chat_id, set()))
            if not muted_users:
                muted_users = await self.get_muted_users(chat_id)
            if not muted_users:
                return await event.reply("🔈 لیست سکوت خالی است.")

            msg = "🔇 لیست کاربران بی‌صدا:\n"
            for uid in muted_users:
                try:
                    user = await self.client.get_entity(int(uid))
                    msg += f"• {user.first_name} ({user.id})\n"
                except:
                    msg += f"• ناشناس ({uid})\n"
            await event.reply(msg)

        @self.client.on(events.NewMessage(pattern=r'^(?:/cleanmute|پاکسازی\s+سکوت)$'))
        async def clean_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            chat_id = str(event.chat_id)
            await self.remove_all_muted_users(chat_id)
            await event.reply("✅ تمام سکوت‌ها حذف شدند.")

        @self.client.on(events.NewMessage())
        async def auto_delete(event):
            # Skip messages from self and events without a valid chat_id
            if event.sender_id == self.me.id or not event.chat_id:
                return

            chat_id = str(event.chat_id)
            user_id = str(event.sender_id)

            # Check in-memory cache first for immediate effect.
            if not self.is_user_muted_in_cache(user_id, chat_id):
                return

            async def try_delete():
                try:
                    await event.delete()
                except Exception as e:
                    logger.warning(f"⚠️ Failed to delete message {event.id}: {e}")

            # Schedule deletion without blocking.
            asyncio.create_task(try_delete())

    async def _resolve_user(self, event):
        if event.is_reply:
            reply = await event.get_reply_message()
            return await reply.get_sender()
        match = re.match(r'(?:/un?mute|سکوت|حذف\s+سکوت)\s+(@?[\w\d]+)', event.raw_text)
        if match:
            try:
                return await event.client.get_entity(match.group(1))
            except:
                return None
        if event.is_private:
            return await event.get_chat()
        return None