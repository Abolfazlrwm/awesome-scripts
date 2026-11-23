# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import json
import asyncio
import logging
from collections import OrderedDict
from datetime import datetime

from telethon import events
from telethon.tl.types import (
    MessageMediaPhoto,
    MessageMediaDocument,
    MessageMediaGeo,
    MessageMediaGeoLive,
    MessageMediaContact,
    MessageMediaPoll
)

from .base import Plugin
from .db_utils import execute_query

# -----------------------------------------------------------------------------
# HELP Text for Welcome Plugin
# -----------------------------------------------------------------------------
HELP = """  
🎉 **راهنمای پلاگین خوشامدگویی پیشرفته** 🎉  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• تنظیم پیام خوشامد اختصاصی برای چت  
• ارسال خودکار پیام خوشامد هنگام ورود کاربر جدید  
• پشتیبانی از پیام‌های متنی، تصویر، فایل و فرمت‌بندی پیشرفته  
• جایگزینی پویا برای عناوین و اطلاعات مانند {first.name}, {chat.title}, {time} و ...  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:  

• **انگلیسی:**  
   `/setwelcome` ➔ تنظیم پیام خوشامد (در پاسخ به یک پیام)  
   `/delwelcome` ➔ حذف پیام خوشامد  
   `/welcomelist` ➔ نمایش لیست چت‌هایی که پیام خوشامد تنظیم شده  

• **فارسی:**  
   `تنظیم خوشامد` ➔ تنظیم پیام خوشامد (در پاسخ به یک پیام)  
   `حذف خوشامد` ➔ حذف پیام خوشامد  
   `لیست خوشامد` ➔ نمایش لیست چت‌های تنظیم شده  

   ↳ مثال‌ها:  
   `تنظیم خوشامد` (با ریپلای روی پیام دلخواه)  
   `حذف خوشامد`  
   `لیست خوشامد`  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  

1. **تنظیم پیام خوشامد:**  
   ریپلای به یک پیام دلخواه و ارسال دستور `تنظیم خوشامد`  
   یا `/setwelcome`  

2. **حذف پیام خوشامد:**  
   ارسال دستور `حذف خوشامد`  
   یا `/delwelcome`  

3. **مشاهده لیست پیام‌های خوشامد:**  
   ارسال دستور `لیست خوشامد`  
   یا `/welcomelist`  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ پیام‌های خوشامد در MySQL ذخیره و در حافظه موقت (LRU Cache) نگهداری می‌شوند  
▫️ پشتیبانی از پیام‌های متنی، تصاویر، فایل‌ها و فرمت‌بندی  
▫️ جایگزینی خودکار اطلاعات کاربر و چت با استفاده از کدهای {first.name}، {chat.title} و ...  

⚠️ **هشدارهای مهم**:  
- فقط کاربر مالک ربات مجاز به تغییر تنظیمات است  
- برای عملکرد صحیح نیاز به دسترسی‌های لازم در گروه/کانال دارید  
- از دستورها و فرمت‌های پیشنهادی استفاده کنید  
"""

# -----------------------------------------------------------------------------
# LRUCache: In-memory caching based on an ordered dictionary.
# -----------------------------------------------------------------------------
class LRUCache:
    def __init__(self, capacity=1000):
        self.capacity = capacity
        self.cache = OrderedDict()

    def get(self, key):
        if key in self.cache:
            self.cache.move_to_end(key)
            return self.cache[key]
        return None

    def set(self, key, value):
        self.cache[key] = value
        self.cache.move_to_end(key)
        if len(self.cache) > self.capacity:
            self.cache.popitem(last=False)

    def delete(self, key):
        if key in self.cache:
            del self.cache[key]

    def clear(self):
        self.cache.clear()

# -----------------------------------------------------------------------------
# WelcomeDB: Uses MySQL (via execute_query from db_utils) with an LRU cache.
# The table stores a welcome message for each chat per owner.
# -----------------------------------------------------------------------------
class WelcomeDB:
    def __init__(self):
        self.lock = asyncio.Lock()
        self.cache = LRUCache(capacity=1000)
        # Initialize the table asynchronously on startup.
        asyncio.create_task(self._init_db_async())

    async def _init_db_async(self):
        """
        Create the welcome_messages table if it doesn't exist.
        Each row is keyed by (owner_id, chat_id).
        """
        create_table_query = """
        CREATE TABLE IF NOT EXISTS welcome_messages (
            owner_id VARCHAR(255),
            chat_id VARCHAR(255),
            message_data TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (owner_id, chat_id)
        )
        """
        try:
            await execute_query(create_table_query, fetch=False)
            logging.info("✅ Welcome messages table initialized.")
        except Exception as e:
            logging.error(f"❌ Error initializing welcome_messages table: {e}")

    async def set_welcome(self, owner_id: str, chat_id: str, message_data: str) -> bool:
        """
        Inserts or updates the welcome message for the specified (owner_id, chat_id).
        Uses alias-based syntax to avoid the deprecation warning.
        """
        async with self.lock:
            self.cache.set((owner_id, chat_id), message_data)
            insert_query = """
            INSERT INTO welcome_messages (owner_id, chat_id, message_data)
            VALUES (%s, %s, %s) AS new
            ON DUPLICATE KEY UPDATE
                message_data = new.message_data
            """
            result = await execute_query(insert_query, (owner_id, chat_id, message_data), fetch=False)
            return result is not None

    async def get_welcome(self, owner_id: str, chat_id: str) -> str:
        """
        Retrieves a welcome message from cache or DB for the given (owner_id, chat_id).
        """
        async with self.lock:
            cached = self.cache.get((owner_id, chat_id))
            if cached is not None:
                return cached
            select_query = "SELECT message_data FROM welcome_messages WHERE owner_id = %s AND chat_id = %s"
            result = await execute_query(select_query, (owner_id, chat_id), fetch=True)
            if result:
                row = result[0]
                message = row['message_data'] if isinstance(row, dict) else row[0]
                self.cache.set((owner_id, chat_id), message)
                return message
            return None

    async def delete_welcome(self, owner_id: str, chat_id: str) -> bool:
        """
        Deletes the welcome message for (owner_id, chat_id).
        """
        async with self.lock:
            self.cache.delete((owner_id, chat_id))
            delete_query = "DELETE FROM welcome_messages WHERE owner_id = %s AND chat_id = %s"
            result = await execute_query(delete_query, (owner_id, chat_id), fetch=False)
            return (result > 0) if isinstance(result, int) else bool(result)

    async def get_all_welcomes(self, owner_id: str) -> list:
        """
        Retrieves a list of all chat_ids that have a welcome message set for the owner.
        """
        async with self.lock:
            query = "SELECT chat_id FROM welcome_messages WHERE owner_id = %s ORDER BY chat_id"
            result = await execute_query(query, (owner_id,), fetch=True)
            welcomes = []
            if result:
                for row in result:
                    chat_id = row['chat_id'] if isinstance(row, dict) else row[0]
                    welcomes.append(chat_id)
            return welcomes

# -----------------------------------------------------------------------------
# WelcomePlugin: Handles setting, deleting, listing, and auto-sending welcome messages.
# Only the designated owner (by owner_id) is allowed to configure the welcome message.
# -----------------------------------------------------------------------------
class WelcomePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.db = WelcomeDB()
        self._register_handlers()

    def _register_handlers(self):
        # ---------------------------------------------------------------------
        # Help command: "/helpwelcome" or "راهنما خوشامد"
        # ---------------------------------------------------------------------
        @self.client.on(events.NewMessage(pattern=r'^(?:/helpwelcome|راهنما\s+خوشامد)$'))
        async def help_welcome_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            await event.reply(HELP)

        # ---------------------------------------------------------------------
        # Set welcome command: "تنظیم خوشامد" or "/setwelcome"
        # Must be sent as a reply to the message to be used as the welcome.
        # ---------------------------------------------------------------------
        @self.client.on(events.NewMessage(pattern=r'^(?:/setwelcome|تنظیم\s+خوشامد)$'))
        async def set_welcome_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if not event.is_reply:
                await event.reply("❌ لطفاً روی پیام مورد نظر ریپلای کنید\n❌ Please reply to a message")
                return

            chat_id = str(event.chat_id)
            reply = await event.get_reply_message()

            welcome_data = {
                "text": reply.raw_text if reply.raw_text else None,
                "media_type": None,
                "media_data": None,
                "raw_text": reply.raw_text if reply.raw_text else None,
                "formatting": None
            }

            if reply.media:
                media_info = await self._extract_media_info(reply)
                if media_info:
                    welcome_data["media_type"] = media_info["type"]
                    welcome_data["media_data"] = media_info["data"]

            if reply.entities:
                welcome_data["formatting"] = [
                    {
                        "type": type(entity).__name__,
                        "offset": entity.offset,
                        "length": entity.length,
                        "url": getattr(entity, 'url', None)
                    }
                    for entity in reply.entities
                ]

            try:
                await self.db.set_welcome(self.owner_id, chat_id, json.dumps(welcome_data))
                await event.reply("✅ پیام خوشامد برای این چت تنظیم شد.")
            except Exception as e:
                await event.reply(f"❌ خطا در تنظیم پیام خوشامد: {e}")

        # ---------------------------------------------------------------------
        # Delete welcome command: "حذف خوشامد" or "/delwelcome"
        # ---------------------------------------------------------------------
        @self.client.on(events.NewMessage(pattern=r'^(?:/delwelcome|حذف\s+خوشامد)$'))
        async def delete_welcome_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            chat_id = str(event.chat_id)
            try:
                deleted = await self.db.delete_welcome(self.owner_id, chat_id)
                if deleted:
                    await event.reply("✅ پیام خوشامد برای این چت حذف شد.")
                else:
                    await event.reply("❌ پیام خوشامد برای این چت یافت نشد.")
            except Exception as e:
                await event.reply(f"❌ خطا در حذف پیام خوشامد: {e}")

        # ---------------------------------------------------------------------
        # List welcome command: "لیست خوشامد" or "/welcomelist"
        # ---------------------------------------------------------------------
        @self.client.on(events.NewMessage(pattern=r'^(?:/welcomelist|لیست\s+خوشامد)$'))
        async def list_welcome_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            try:
                welcomes = await self.db.get_all_welcomes(self.owner_id)
                if welcomes:
                    welcome_list_text = "📝 لیست چت‌های تنظیم شده پیام خوشامد:\n\n"
                    for chat_id in welcomes:
                        welcome_list_text += f"• {chat_id}\n"
                    await event.reply(welcome_list_text)
                else:
                    await event.reply("❌ هیچ پیام خوشامدی تنظیم نشده است.")
            except Exception as e:
                await event.reply(f"❌ خطا در دریافت لیست پیام‌های خوشامد: {e}")

        # ---------------------------------------------------------------------
        # Auto-send welcome message when a new user joins the chat.
        # ---------------------------------------------------------------------
        @self.client.on(events.ChatAction)
        async def welcome_new_member(event):
            if not (event.user_joined or event.user_added):
                return

            chat_id = str(event.chat_id)
            welcome_message_data = await self.db.get_welcome(self.owner_id, chat_id)
            if not welcome_message_data:
                return

            reply_to = event.action_message.id if hasattr(event, "action_message") and event.action_message else None

            try:
                welcome = json.loads(welcome_message_data)
                text_to_send = await self._replace_placeholders(
                    welcome.get("text") or welcome.get("raw_text"),
                    event,
                    event.user
                )

                params = {"reply_to": reply_to} if reply_to else {}
                if text_to_send:
                    params["message"] = text_to_send

                media_type = welcome.get("media_type")
                media_data = welcome.get("media_data")
                if media_type and media_data:
                    if media_type == "photo":
                        from telethon.tl.types import InputPhoto
                        params["file"] = InputPhoto(
                            id=media_data["id"],
                            access_hash=media_data["access_hash"],
                            file_reference=bytes.fromhex(media_data["file_reference"])
                        )
                    elif media_type == "document":
                        from telethon.tl.types import InputDocument
                        params["file"] = InputDocument(
                            id=media_data["id"],
                            access_hash=media_data["access_hash"],
                            file_reference=bytes.fromhex(media_data["file_reference"])
                        )
                    elif media_type == "geo":
                        from telethon.tl.types import InputGeoPoint
                        params["file"] = InputGeoPoint(
                            lat=media_data["lat"],
                            long=media_data["long"]
                        )
                    elif media_type == "contact":
                        from telethon.tl.types import InputMediaContact
                        params["file"] = InputMediaContact(
                            phone_number=media_data["phone_number"],
                            first_name=media_data["first_name"],
                            last_name=media_data["last_name"],
                            vcard=media_data["vcard"]
                        )
                if welcome.get("formatting"):
                    from telethon.tl.types import (
                        MessageEntityBold, MessageEntityItalic, MessageEntityCode,
                        MessageEntityPre, MessageEntityTextUrl
                    )
                    entities = []
                    for fmt in welcome["formatting"]:
                        if fmt["type"] == "MessageEntityBold":
                            entities.append(MessageEntityBold(fmt["offset"], fmt["length"]))
                        elif fmt["type"] == "MessageEntityItalic":
                            entities.append(MessageEntityItalic(fmt["offset"], fmt["length"]))
                        elif fmt["type"] == "MessageEntityCode":
                            entities.append(MessageEntityCode(fmt["offset"], fmt["length"]))
                        elif fmt["type"] == "MessageEntityPre":
                            entities.append(MessageEntityPre(fmt["offset"], fmt["length"], ""))
                        elif fmt["type"] == "MessageEntityTextUrl" and fmt.get("url"):
                            entities.append(MessageEntityTextUrl(fmt["offset"], fmt["length"], fmt["url"]))
                    if entities:
                        params["formatting_entities"] = entities

                await event.respond(**params)
            except Exception as e:
                logging.error(f"Error sending welcome message: {e}")

    async def _replace_placeholders(self, text, event, new_member):
        """
        Replace placeholders in the welcome message with runtime values.
        Supported placeholders: {first.name}, {last.name}, {full.name}, {username},
        {user.id}, {chat.title}, {chat.id}, {time}, {date}, {day}
        """
        if not text:
            return text

        chat = await event.get_chat()
        replacements = {
            "{first.name}": getattr(new_member, 'first_name', ''),
            "{last.name}": getattr(new_member, 'last_name', ''),
            "{full.name}": f"{getattr(new_member, 'first_name', '')} {getattr(new_member, 'last_name', '')}".strip(),
            "{username}": f"@{new_member.username}" if getattr(new_member, 'username', None) else '',
            "{user.id}": str(new_member.id),
            "{chat.title}": getattr(chat, 'title', ''),
            "{chat.id}": str(chat.id),
            "{time}": datetime.now().strftime("%H:%M"),
            "{date}": datetime.now().strftime("%Y-%m-%d"),
            "{day}": datetime.now().strftime("%A"),
        }
        for placeholder, value in replacements.items():
            text = text.replace(placeholder, str(value))
        return text

    async def _extract_media_info(self, message):
        """
        Extracts media information from a message without downloading the file.
        """
        if not message.media:
            return None

        media_info = {"type": None, "data": {}}

        if isinstance(message.media, MessageMediaPhoto):
            media_info["type"] = "photo"
            media_info["data"] = {
                "id": message.media.photo.id,
                "access_hash": message.media.photo.access_hash,
                "file_reference": message.media.photo.file_reference.hex(),
                "sizes": [
                    {"type": size.type, "bytes": len(size.bytes) if hasattr(size, 'bytes') else None}
                    for size in message.media.photo.sizes
                ]
            }
        elif isinstance(message.media, MessageMediaDocument):
            media_info["type"] = "document"
            doc = message.media.document
            attributes = []
            for attr in doc.attributes:
                attr_dict = {}
                if hasattr(attr, 'file_name'):
                    attr_dict['file_name'] = attr.file_name
                if hasattr(attr, 'duration'):
                    attr_dict['duration'] = attr.duration
                if hasattr(attr, 'title'):
                    attr_dict['title'] = attr.title
                if hasattr(attr, 'performer'):
                    attr_dict['performer'] = attr.performer
                if hasattr(attr, 'voice'):
                    attr_dict['voice'] = attr.voice
                if hasattr(attr, 'w'):
                    attr_dict['width'] = attr.w
                if hasattr(attr, 'h'):
                    attr_dict['height'] = attr.h
                if hasattr(attr, 'round_message'):
                    attr_dict['round_message'] = attr.round_message
                if hasattr(attr, 'supports_streaming'):
                    attr_dict['supports_streaming'] = attr.supports_streaming
                if attr_dict:
                    attributes.append(attr_dict)
            media_info["data"] = {
                "id": doc.id,
                "access_hash": doc.access_hash,
                "file_reference": doc.file_reference.hex(),
                "mime_type": doc.mime_type,
                "size": doc.size,
                "attributes": attributes
            }
        elif isinstance(message.media, (MessageMediaGeo, MessageMediaGeoLive)):
            media_info["type"] = "geo"
            media_info["data"] = {
                "lat": message.media.geo.lat,
                "long": message.media.geo.long,
                "access_hash": getattr(message.media.geo, 'access_hash', None)
            }
        elif isinstance(message.media, MessageMediaContact):
            media_info["type"] = "contact"
            media_info["data"] = {
                "phone_number": message.media.phone_number,
                "first_name": message.media.first_name,
                "last_name": message.media.last_name,
                "vcard": message.media.vcard,
                "user_id": message.media.user_id
            }
        elif isinstance(message.media, MessageMediaPoll):
            media_info["type"] = "poll"
            media_info["data"] = {
                "question": message.media.poll.question,
                "answers": [
                    {"text": ans.text, "option": ans.option.decode()}
                    for ans in message.media.poll.answers
                ],
                "closed": message.media.poll.closed,
                "multiple_choice": message.media.poll.multiple_choice,
                "quiz": message.media.poll.quiz,
                "public_voters": message.media.poll.public_voters
            }
        return media_info
