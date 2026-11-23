# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
from .base import Plugin
from telethon import events, types, errors, functions
from .db_utils import execute_query

logger = logging.getLogger(__name__)

HELP = """
🔄 **سیستم پذیرش خودکار درخواست‌های عضویت** 🔄

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت‌های کلیدی**:
- پذیرش خودکار درخواست‌های عضویت
- فعال/غیرفعال‌سازی پذیرش خودکار در چت جاری یا با آیدی
- مشاهده لیست چت‌های فعال
- آمار کاربران پذیرفته‌شده
▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات**:

**انگلیسی:**
  `/accept on` ➤ فعال‌سازی پذیرش خودکار در چت جاری  
  `/accept off` ➤ غیرفعال‌سازی پذیرش خودکار در چت جاری  
  `/autoacceptlist` ➤ نمایش لیست چت‌های فعال برای پذیرش خودکار  
  `/autoacceptstats` ➤ نمایش آمار پذیرش‌های انجام شده  

**فارسی:**
  `پذیرش خودکار روشن` ➤ فعال‌سازی پذیرش خودکار در چت جاری  
  `پذیرش خودکار خاموش` ➤ غیرفعال‌سازی پذیرش خودکار در چت جاری  
  `لیست پذیرش خودکار` ➤ نمایش لیست چت‌های فعال برای پذیرش خودکار  
  `آمار پذیرش خودکار` ➤ نمایش آمار پذیرش‌های انجام شده  

⚠️ توجه: ربات باید ادمین بوده و مجوز پذیرش درخواست‌ها را داشته باشد
"""

class AutoAcceptPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.accepted_count = 0
        self.enabled_chats_cache = set()
        self.db_initialized = False

    async def start(self):
        try:
            await self._init_mysql()
            await self._load_user_config()
            logger.info("AutoAccept plugin initialized.")
        except Exception as e:
            logger.error(f"Startup failed: {e}")
            raise

    async def _init_mysql(self):
        await execute_query("""
            CREATE TABLE IF NOT EXISTS enabled_chats (
                owner_id VARCHAR(50),
                chat_id VARCHAR(50),
                PRIMARY KEY (owner_id, chat_id)
            ) ENGINE=InnoDB;
        """)
        await execute_query("""
            CREATE TABLE IF NOT EXISTS stats (
                owner_id VARCHAR(50) PRIMARY KEY,
                accepted_count INT DEFAULT 0
            ) ENGINE=InnoDB;
        """)
        self.db_initialized = True

    async def _load_user_config(self):
        if not self.db_initialized:
            return
        rows = await execute_query("SELECT chat_id FROM enabled_chats WHERE owner_id = %s", (self.owner_id,), fetch=True)
        self.enabled_chats_cache = set(r['chat_id'] for r in rows)
        row = await execute_query("SELECT accepted_count FROM stats WHERE owner_id = %s", (self.owner_id,), fetch=True)
        if row:
            self.accepted_count = row[0]['accepted_count']
        else:
            await execute_query("INSERT INTO stats (owner_id, accepted_count) VALUES (%s, 0)", (self.owner_id,))

    async def handle_events(self):
        @self.client.on(events.Raw)
        async def join_handler(update):
            if not self.db_initialized:
                return
            if isinstance(update, types.UpdatePendingJoinRequests):
                chat_id = getattr(update.peer, 'channel_id', None)
                if not chat_id:
                    return
                chat_id_str = f"-100{chat_id}"
                if chat_id_str not in self.enabled_chats_cache:
                    return
                await self._process_user_request(update.peer, len(update.recent_requesters))

        @self.client.on(events.NewMessage(pattern=r'^(?:/(autoaccepton|accept on)|پذیرش خودکار روشن)(?:\s+(.+))?$'))
        async def enable(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 دسترسی شما به این قابلیت فعال نیست.\n"
                    "برای فعال‌سازی Accept لطفاً از طریق ربات زیر اقدام کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            target = event.pattern_match.group(2)
            chat_id = await self._resolve_chat(event, target)
            if not chat_id:
                await event.reply("❌ چت نامعتبر است.")
                return

            chat_id = str(chat_id)
            await execute_query("INSERT IGNORE INTO enabled_chats (owner_id, chat_id) VALUES (%s, %s)", (self.owner_id, chat_id))
            self.enabled_chats_cache.add(chat_id)

            try:
                chat = await self.client.get_entity(int(chat_id))
                name = chat.title
            except:
                name = chat_id
            await event.reply(f"✅ پذیرش خودکار برای «{name}» فعال شد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/(autoacceptoff|accept off)|پذیرش خودکار خاموش)(?:\s+(.+))?$'))
        async def disable(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 دسترسی شما به این قابلیت فعال نیست.\n"
                    "برای فعال‌سازی accept لطفاً از طریق ربات زیر اقدام کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            target = event.pattern_match.group(2)
            chat_id = await self._resolve_chat(event, target)
            if not chat_id:
                await event.reply("❌ چت نامعتبر است.")
                return

            chat_id = str(chat_id)
            await execute_query("DELETE FROM enabled_chats WHERE owner_id = %s AND chat_id = %s", (self.owner_id, chat_id))
            self.enabled_chats_cache.discard(chat_id)

            try:
                chat = await self.client.get_entity(int(chat_id))
                name = chat.title
            except:
                name = chat_id
            await event.reply(f"❎ پذیرش خودکار برای «{name}» غیرفعال شد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/autoacceptlist|لیست پذیرش خودکار)$'))
        async def list_chats(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 دسترسی شما به این قابلیت فعال نیست.\n"
                    "برای فعال‌سازی Accept لطفاً از طریق ربات زیر اقدام کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            response = "📋 **چت‌های فعال برای پذیرش خودکار**:\n\n"
            for chat_id in self.enabled_chats_cache:
                try:
                    entity = await self.client.get_entity(int(chat_id))
                    response += f"• {entity.title} (ID: {chat_id})\n"
                except:
                    response += f"• {chat_id}\n"
            await event.reply(response if self.enabled_chats_cache else "📋 هیچ چتی فعال نیست")

        @self.client.on(events.NewMessage(pattern=r'^(?:/autoacceptstats|آمار پذیرش خودکار)$'))
        async def stats(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 دسترسی شما به این قابلیت فعال نیست.\n"
                    "برای فعال‌سازی Accept لطفاً از طریق ربات زیر اقدام کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            await event.reply(f"📊 آمار:\n\n• چت‌های فعال: {len(self.enabled_chats_cache)}\n• کاربران پذیرفته‌شده: {self.accepted_count}")

    async def _process_user_request(self, peer, accepted_count):
        try:
            input_peer = await self.client.get_input_entity(peer)
            await self.client(functions.messages.HideAllChatJoinRequestsRequest(
                peer=input_peer,
                approved=True
            ))
            self.accepted_count += accepted_count
            await execute_query("UPDATE stats SET accepted_count = %s WHERE owner_id = %s", (self.accepted_count, self.owner_id))
            logger.info(f"✅ {accepted_count} کاربر پذیرفته شدند")
        except errors.ChatAdminRequiredError:
            logger.error("❌ نیاز به دسترسی ادمین دارد")
        except Exception as e:
            logger.exception(f"❌ خطا در پذیرش: {e}")

    async def _resolve_chat(self, event, target):
        if not target:
            return event.chat_id
        if target.lstrip("-").isdigit():
            return target
        try:
            entity = await self.client.get_entity(target)
            return str(entity.id)
        except:
            return None

    async def _is_authorized(self, user_id: str) -> bool:
        try:
            result = await execute_query("SELECT 1 FROM authorized_users WHERE user_id = %s", (user_id,), fetch=True)
            return bool(result)
        except Exception as e:
            logger.error(f"Authorization check failed: {e}")
            return False
