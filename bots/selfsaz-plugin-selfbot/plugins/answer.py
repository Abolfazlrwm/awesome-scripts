# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
from telethon.tl.types import (
    MessageMediaPhoto,
    MessageMediaDocument,
    MessageMediaGeoLive,
    MessageMediaGeo,
    MessageMediaContact,
    MessageMediaPoll,
    MessageMediaWebPage,
    MessageMediaVenue,
    MessageMediaGame,
    MessageMediaInvoice,
    Message,
    MessageEntityBold,
    MessageEntityItalic,
    MessageEntityCode,
    MessageEntityPre,
    MessageEntityTextUrl,
    InputPhoto,
    InputDocument,
    InputGeoPoint
)
import json
import asyncio
from datetime import datetime
from .db_utils import execute_query  # Now an async function based on aiomysql

HELP = """  
⚡ **پاسخ‌های خودکار پیشرفته** ⚡  
⚡ **Advanced Auto-Answers** ⚡  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌ها | Features**:  
• ذخیره پاسخ‌های چندرسانه‌ای (متن، عکس، فایل، نظرسنجی و...)  
  Save multimedia answers (text, photo, file, poll, etc.)  
• فعال‌سازی با **کلیدواژه**های دلخواه شما  
  Trigger with your custom **keywords**  
• فرمت‌بندی حرفه‌ای متن (پررنگ، مورب، لینک و...)  
  Advanced text formatting (bold, italic, links, etc.)  
• جایگزینی خودکار:  
  Auto-replace placeholders:  
  {username}، {time}، {date}، {mention}، {chat.title}  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات | Commands**:  
• `/setanswer [کلید]` یا `ذخیره پاسخ [کلید]`  
  ➔ ذخیره پاسخ جدید (با ریپلای)  
  ➔ Save new answer (reply to a message)  

• `/delanswer [کلید]` یا `حذف پاسخ [کلید]`  
  ➔ حذف پاسخ  
  ➔ Delete an answer  

• `/answers` یا `لیست پاسخها`  
  ➔ نمایش کلیدهای ثبت‌شده  
  ➔ Show saved answer keys  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **مثال | Example**:  
ارسال | Send:  
`/setanswer سلام` یا `ذخیره پاسخ سلام`  
(ریپلای روی پیام دلخواه | Reply to a message)  
➜ با هر بار ارسال «سلام»، پاسخ شما ارسال می‌شود!  
➜ When anyone sends "سلام", your reply will be sent!  
"""


from collections import OrderedDict

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


class AnswerDB:
    def __init__(self):
        self.lock = asyncio.Lock()
        self.cache = LRUCache(capacity=1000)
        # Schedule asynchronous initialization of the table.
        asyncio.create_task(self._init_db_async())

    async def _init_db_async(self):
        """Asynchronously create the answers table in MySQL if it doesn't exist."""
        query = """
        CREATE TABLE IF NOT EXISTS answers (
            owner_id VARCHAR(255),
            `trigger` VARCHAR(255),
            answer_data TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (owner_id, `trigger`)
        )
        """
        try:
            await execute_query(query, fetch=False)
            print("✅ Answers table initialized in MySQL.")
        except Exception as e:
            print(f"❌ Error initializing answers table: {e}")

    async def save_answer(self, owner_id: str, trigger: str, answer_data: str) -> bool:
        """Save or update an answer in the MySQL database."""
        async with self.lock:
            self.cache.set((owner_id, trigger), answer_data)
            query = """
            INSERT INTO answers (owner_id, `trigger`, answer_data)
            VALUES (%s, %s, %s)
            ON DUPLICATE KEY UPDATE answer_data = VALUES(answer_data)
            """
            result = await execute_query(query, (owner_id, trigger, answer_data), fetch=False)
            return result is not None

    
    async def start(self, owner_id: str):
        """Pre-load all answers into cache on startup."""
        async with self.lock:
            query = "SELECT `trigger`, answer_data FROM answers WHERE owner_id = %s"
            results = await execute_query(query, (owner_id,), fetch=True)
            if results:
                for row in results:
                    trigger = row['trigger'] if isinstance(row, dict) else row[0]
                    answer = row['answer_data'] if isinstance(row, dict) else row[1]
                    self.cache.set((owner_id, trigger), answer)
                print(f"✅ Preloaded {len(results)} answers into cache.")
            else:
                print("ℹ️ No answers found to preload.")

    async def get_answer(self, owner_id: str, trigger: str) -> str:
        """Retrieve an answer from the MySQL database."""
        async with self.lock:
            query = "SELECT answer_data FROM answers WHERE owner_id = %s AND `trigger` = %s"
            cached = self.cache.get((owner_id, trigger))
            if cached is not None:
                return cached
            result = await execute_query(query, (owner_id, trigger), fetch=True)
            if result:
                answer = result[0]['answer_data'] if isinstance(result[0], dict) else result[0][0]
                self.cache.set((owner_id, trigger), answer)
                return answer
            return None

    async def delete_answer(self, owner_id: str, trigger: str) -> bool:
        """Delete an answer from the MySQL database."""
        async with self.lock:
            self.cache.delete((owner_id, trigger))
            query = "DELETE FROM answers WHERE owner_id = %s AND `trigger` = %s"
            result = await execute_query(query, (owner_id, trigger), fetch=False)
            return (result > 0) if result is not None else False

    async def get_all_answers(self, owner_id: str) -> list:
        """Retrieve all triggers for an owner from the MySQL database."""
        async with self.lock:
            query = "SELECT `trigger` FROM answers WHERE owner_id = %s ORDER BY `trigger`"
            result = await execute_query(query, (owner_id,), fetch=True)
            if result:
                # Handle both DictCursor and tuple result types.
                if isinstance(result[0], dict):
                    return [row['trigger'] for row in result]
                else:
                    return [row[0] for row in result]
            return []

class SetAnswerPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        # Initialize AnswerDB (which sets up the MySQL table)
        self.db = AnswerDB()
        asyncio.create_task(self.db.start(self.owner_id))

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(/setanswer|ذخیره\s+پاسخ)\s+(.+)$'))
        async def setanswer_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_reply:
                await event.reply("❌ لطفاً روی پیام مورد نظر ریپلای کنید\n❌ Please reply to a message")
                return

            trigger = event.pattern_match.group(2).strip()
            reply = await event.get_reply_message()

            # Create answer data structure
            answer_data = {
                "text": reply.raw_text if reply.raw_text else None,
                "media_type": None,
                "media_data": None,
                "raw_text": reply.raw_text if reply.raw_text else None,
                "formatting": None
            }

            # Handle attached media if any
            if reply.media:
                media_info = await self._extract_media_info(reply)
                if media_info:
                    answer_data["media_type"] = media_info["type"]
                    answer_data["media_data"] = media_info["data"]

            # Save message formatting/entities if they exist
            if reply.entities:
                answer_data["formatting"] = [
                    {
                        "type": type(entity).__name__,
                        "offset": entity.offset,
                        "length": entity.length,
                        "url": getattr(entity, 'url', None)
                    }
                    for entity in reply.entities
                ]

            try:
                await self.db.save_answer(self.owner_id, trigger, json.dumps(answer_data))
                await event.reply(f"✅ پاسخ برای کلید '{trigger}' ذخیره شد\n✅ Answer saved for key '{trigger}'")
            except Exception as e:
                await event.reply(f"❌ خطا در ذخیره پاسخ: {str(e)}\n❌ Error saving answer: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(/delanswer|حذف\s+پاسخ)\s+(.+)$'))
        async def delanswer_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            trigger = event.pattern_match.group(2).strip()
            try:
                deleted = await self.db.delete_answer(self.owner_id, trigger)
                if deleted:
                    await event.reply(f"✅ پاسخ برای کلید '{trigger}' حذف شد\n✅ Answer deleted for key '{trigger}'")
                else:
                    await event.reply(f"❌ پاسخی برای کلید '{trigger}' یافت نشد\n❌ No answer found for key '{trigger}'")
            except Exception as e:
                await event.reply(f"❌ خطا در حذف پاسخ: {str(e)}\n❌ Error deleting answer: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(/answers|لیست\s+پاسخها)$'))
        async def answers_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            try:
                triggers = await self.db.get_all_answers(self.owner_id)
                if not triggers:
                    await event.reply("❌ هیچ پاسخی تنظیم نشده است\n❌ No answers have been set")
                    return

                response = "📝 لیست پاسخ‌های خودکار:\n📝 Auto-answer list:\n\n"
                for trig in triggers:
                    response += f"• {trig}\n"
                await event.reply(response)
            except Exception as e:
                await event.reply(f"❌ خطا در دریافت لیست پاسخ‌ها: {str(e)}\n❌ Error getting answer list: {str(e)}")

        @self.client.on(events.NewMessage)
        async def answer_handler(event):
            if not event.raw_text:
                return

            try:
                answer_data = await self.db.get_answer(self.owner_id, event.raw_text)
                if not answer_data:
                    return

                answer = json.loads(answer_data)
                await self._send_answer(event, answer)
            except Exception as e:
                print(f"Error in answer handler: {str(e)}")

    async def _replace_placeholders(self, text, event):
        """Replace placeholders in text with actual values."""
        if not text:
            return text

        sender = await event.get_sender()
        chat = await event.get_chat()

        replacements = {
            "{first.name}": getattr(sender, 'first_name', ''),
            "{last.name}": getattr(sender, 'last_name', ''),
            "{full.name}": f"{getattr(sender, 'first_name', '')} {getattr(sender, 'last_name', '')}".strip(),
            "{username}": f"@{sender.username}" if getattr(sender, 'username', None) else '',
            "{user.id}": str(getattr(sender, 'id', '')),
            "{chat.title}": getattr(chat, 'title', ''),
            "{chat.id}": str(getattr(chat, 'id', '')),
            "{mention}": f"[{sender.first_name}](tg://user?id={sender.id})" if sender else '',
            "{time}": datetime.now().strftime("%H:%M"),
            "{date}": datetime.now().strftime("%Y-%m-%d"),
            "{day}": datetime.now().strftime("%A"),
            "{message.id}": str(event.id),
        }

        for placeholder, value in replacements.items():
            text = text.replace(placeholder, str(value))

        return text

    async def _extract_media_info(self, message):
        """Extract media information without downloading the file."""
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
                if hasattr(attr, 'CONSTRUCTOR_ID'):
                    attr_dict['constructor_id'] = attr.CONSTRUCTOR_ID
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
                "long": message.media.geo.long,
                "lat": message.media.geo.lat,
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
                    {"text": answer.text, "option": answer.option.decode()}
                    for answer in message.media.poll.answers
                ],
                "closed": message.media.poll.closed,
                "multiple_choice": message.media.poll.multiple_choice,
                "quiz": message.media.poll.quiz,
                "public_voters": message.media.poll.public_voters
            }

        return media_info

    async def _send_answer(self, event, answer):
        """Send the stored answer with its original formatting and media."""
        try:
            params = {
                "reply_to": event.id
            }

            if answer.get("text") or answer.get("raw_text"):
                processed_text = await self._replace_placeholders(answer.get("text") or answer.get("raw_text"), event)
                params["message"] = processed_text

            if answer.get("media_type") and answer.get("media_data"):
                media_type = answer["media_type"]
                media_data = answer["media_data"]

                if media_type == "photo":
                    params["file"] = InputPhoto(
                        id=media_data["id"],
                        access_hash=media_data["access_hash"],
                        file_reference=bytes.fromhex(media_data["file_reference"])
                    )
                elif media_type == "document":
                    params["file"] = InputDocument(
                        id=media_data["id"],
                        access_hash=media_data["access_hash"],
                        file_reference=bytes.fromhex(media_data["file_reference"])
                    )
                elif media_type == "geo":
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

            if answer.get("formatting"):
                from telethon.tl.types import MessageEntityBold, MessageEntityItalic, MessageEntityCode, MessageEntityPre, MessageEntityTextUrl
                entities = []
                for fmt in answer["formatting"]:
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
            print(f"Error sending answer: {str(e)}")
