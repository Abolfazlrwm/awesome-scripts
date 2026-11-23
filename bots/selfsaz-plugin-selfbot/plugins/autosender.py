# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import json
import asyncio
import logging
from datetime import datetime

from telethon import events
from telethon.tl.types import (
	PeerChannel,
	PeerChat,
	MessageMediaPhoto,
	MessageMediaDocument,
	MessageMediaGeo,
	MessageMediaGeoLive,
	MessageMediaContact,
	MessageMediaPoll
)

from .base import Plugin
from .db_utils import execute_query

logger = logging.getLogger("AutoMsgPlugin")

HELP = """  
🚀 **راهنمای پلاگین پیام اتوماتیک** 🚀  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• تنظیم پیام اتوماتیک در چت (با استفاده از ریپلای به یک پیام)  
• تعیین زمان‌بندی (به ثانیه) برای ارسال خودکار پیام  
• ارسال خودکار پیام به صورت دوره‌ای به چت  
• پشتیبانی از پیام‌های متنی، تصویری، فایل و فرمت‌بندی پیشرفته  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:  

• **انگلیسی:**  
	`/setautomsg [interval_sec]` ➔ تنظیم پیام اتوماتیک (به عنوان ریپلای به پیام مورد نظر)  
	`/delautomsg` ➔ حذف پیام اتوماتیک  
	`/automsglist` ➔ نمایش لیست چت‌هایی که پیام اتوماتیک تنظیم شده  
	`/helpautomsg` ➔ نمایش راهنما  

• **فارسی:**  
	`تنظیم پیام اتوماتیک [interval_sec]` ➔ تنظیم پیام اتوماتیک (با ریپلای)  
	`حذف پیام اتوماتیک` ➔ حذف پیام اتوماتیک  
	`لیست پیام اتوماتیک` ➔ نمایش لیست چت‌های تنظیم شده  
	`راهنمای پیام اتوماتیک` ➔ نمایش راهنما  

	↳ مثال‌ها:  
	`تنظیم پیام اتوماتیک 60` (برای ارسال هر 60 ثانیه)  
	`حذف پیام اتوماتیک`  
	`لیست پیام اتوماتیک`  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  

1. **تنظیم پیام اتوماتیک:**  
	به یک پیام دلخواه ریپلای کنید و دستور `تنظیم پیام اتوماتیک 60` (یا `/setautomsg 60`) را ارسال کنید.  
	پیام انتخابی برای این چت در هر 60 ثانیه ارسال خواهد شد.

2. **حذف پیام اتوماتیک:**  
	با ارسال `حذف پیام اتوماتیک` (یا `/delautomsg`) پیام اتوماتیک از چت حذف می‌شود.

3. **لیست پیام‌های اتوماتیک:**  
	ارسال دستور `لیست پیام اتوماتیک` (یا `/automsglist`) لیستی از چت‌هایی را که پیام اتوماتیک تنظیم شده است نمایش می‌دهد.

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ پیام اتوماتیک در MySQL ذخیره و در حافظه نگهداری می‌شود  
▫️ زمان‌بندی ارسال پیام به وسیله وظایف پس‌زمینه (asyncio Task) انجام می‌شود  
▫️ پیام‌ها شامل متن، رسانه (در صورت وجود) و فرمت‌بندی‌های اضافی هستند  

⚠️ **هشدارهای مهم**:  
- تنها کاربر مالک ربات (بر اساس owner_id) مجاز به تنظیم و حذف پیام اتوماتیک است  
- در صورت وجود خطا در ارسال پیام (مثلاً عدم دسترسی یا entity گم‌شده) پس از 3 شکست متوالی، پیام اتوماتیک به طور خودکار حذف می‌شود  
- ربات باید در گروه/کانال مورد نظر عضو بوده و مجوز ارسال پیام داشته باشد  
"""

class AutoMsgPlugin(Plugin):
	def __init__(self, client, config, user_id):
		super().__init__(client, config)
		self.owner_id = str(user_id)
		# In-memory cache: key is (owner_id, chat_id); value is (message_data, interval_sec)
		self.cache = {}
		# Dictionary mapping chat_id (str) to scheduled asyncio.Task for auto-sending
		self.tasks = {}
		# Event to signal that the DB table has been created
		self.ready = asyncio.Event()
		# Immediately create the table (using the same style as your mute plugin)
		asyncio.create_task(self._init_db())
		self._register_handlers()

	async def _init_db(self):
		await execute_query("""
			CREATE TABLE IF NOT EXISTS auto_messages (
				owner_id VARCHAR(32) NOT NULL,
				chat_id VARCHAR(32) NOT NULL,
				message_data TEXT,
				interval_sec INT,
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (owner_id, chat_id)
			)
		""", fetch=False)
		logger.info("✅ auto_messages table initialized.")
		self.ready.set()

	async def set_auto_message(self, chat_id: str, message_data: str, interval_sec: int) -> bool:
		await self.ready.wait()  # Wait until table is created.
		self.cache[(self.owner_id, chat_id)] = (message_data, interval_sec)
		query = """
			INSERT INTO auto_messages (owner_id, chat_id, message_data, interval_sec)
			VALUES (%s, %s, %s, %s) AS new
			ON DUPLICATE KEY UPDATE
				message_data = new.message_data,
				interval_sec = new.interval_sec
		"""
		return await execute_query(query, (self.owner_id, chat_id, message_data, interval_sec), fetch=False) is not None

	async def get_auto_message(self, chat_id: str):
		await self.ready.wait()
		key = (self.owner_id, chat_id)
		if key in self.cache:
			return self.cache[key]
		query = "SELECT message_data, interval_sec FROM auto_messages WHERE owner_id = %s AND chat_id = %s"
		result = await execute_query(query, (self.owner_id, chat_id), fetch=True)
		if result:
			row = result[0]
			message_data = row['message_data'] if isinstance(row, dict) else row[0]
			interval_sec = row['interval_sec'] if isinstance(row, dict) else row[1]
			self.cache[key] = (message_data, interval_sec)
			return (message_data, interval_sec)
		return None

	async def delete_auto_message(self, chat_id: str) -> bool:
		await self.ready.wait()
		key = (self.owner_id, chat_id)
		self.cache.pop(key, None)
		query = "DELETE FROM auto_messages WHERE owner_id = %s AND chat_id = %s"
		return await execute_query(query, (self.owner_id, chat_id), fetch=False) is not None

	async def get_all_auto_messages(self) -> list:
		await self.ready.wait()
		query = "SELECT chat_id FROM auto_messages WHERE owner_id = %s ORDER BY chat_id"
		result = await execute_query(query, (self.owner_id,), fetch=True)
		chats = []
		if result:
			for row in result:
				cid = row['chat_id'] if isinstance(row, dict) else row[0]
				chats.append(cid)
		return chats

	# Authorization function added from aichat.py
	async def _is_authorized(self, user_id: str) -> bool:
		try:
			result = await execute_query(
				"SELECT 1 FROM authorized_users WHERE user_id = %s",
				(user_id,),
				fetch=True
			)
			return bool(result)
		except Exception as e:
			print(f"❌ Authorization check failed: {e}")
			return False

	def _register_handlers(self):
		@self.client.on(events.NewMessage(pattern=r'^(?:/helpautomsg|راهنمای\s+پیام\s+اتوماتیک)$'))
		async def help_handler(event):

			if str(event.sender_id) != self.owner_id:
				return

			if not await self._is_authorized(event.sender_id):
				await event.reply("""🚫 سلف شما هنوز ویژه نیست!

✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.
با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.

🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:
👉 @VIPSelfsazxBot""")
				return

			await event.reply(HELP)

		@self.client.on(events.NewMessage(pattern=r'^(?:/setautomsg|تنظیم\s+پیام\s+اتوماتیک)\s+(\d+)$'))
		async def set_handler(event):

			if str(event.sender_id) != self.owner_id:
				return

			if not await self._is_authorized(event.sender_id):
				await event.reply("""🚫 سلف شما هنوز ویژه نیست!

✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.
با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.

🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:
👉 @VIPSelfsazxBot""")
				return
			if not event.is_reply:
				await event.reply("❌ لطفاً روی پیام مورد نظر ریپلای کنید\n❌ Please reply to a message")
				return
			try:
				interval_sec = int(event.pattern_match.group(1))
			except ValueError:
				await event.reply("❌ لطفاً یک عدد معتبر برای تایمر وارد کنید\n❌ Provide a valid number for the timer")
				return
			chat_id = str(event.chat_id)
			reply = await event.get_reply_message()
			automsg = {
				"text": reply.raw_text if reply.raw_text else None,
				"media_type": None,
				"media_data": None,
				"raw_text": reply.raw_text if reply.raw_text else None,
				"formatting": None
			}
			if reply.media:
				media_info = await self._extract_media_info(reply)
				if media_info:
					automsg["media_type"] = media_info["type"]
					automsg["media_data"] = media_info["data"]
			if reply.entities:
				automsg["formatting"] = [
					{"type": type(entity).__name__, "offset": entity.offset, "length": entity.length, "url": getattr(entity, 'url', None)}
					for entity in reply.entities
				]
			try:
				await self.set_auto_message(chat_id, json.dumps(automsg), interval_sec)
				await event.reply(f"✅ پیام اتوماتیک برای این چت تنظیم شد و هر {interval_sec} ثانیه ارسال خواهد شد.")
				await self._schedule_auto_task(chat_id)
			except Exception as e:
				await event.reply(f"❌ خطا در تنظیم پیام اتوماتیک: {e}")

		@self.client.on(events.NewMessage(pattern=r'^(?:/delautomsg|حذف\s+پیام\s+اتوماتیک)$'))
		async def delete_handler(event):

			if str(event.sender_id) != self.owner_id:
				return
            
			if not await self._is_authorized(event.sender_id):

				await event.reply("""🚫 سلف شما هنوز ویژه نیست!

                  ✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.
                    با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.

                    🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:
                    👉 @VIPSelfsazxBot""")
				return

			chat_id = str(event.chat_id)
			try:
				deleted = await self.delete_auto_message(chat_id)
				if deleted:
					await event.reply("✅ پیام اتوماتیک برای این چت حذف شد.")
					if chat_id in self.tasks:
						self.tasks[chat_id].cancel()
						del self.tasks[chat_id]
				else:
					await event.reply("❌ پیام اتوماتیک برای این چت یافت نشد.")
			except Exception as e:
				await event.reply(f"❌ خطا در حذف پیام اتوماتیک: {e}")

		@self.client.on(events.NewMessage(pattern=r'^(?:/automsglist|لیست\s+پیام\s+اتوماتیک)$'))
		async def list_handler(event):


			if str(event.sender_id) != self.owner_id:
				return


			if not await self._is_authorized(event.sender_id):
				await event.reply("""🚫 سلف شما هنوز ویژه نیست!

✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.
با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.

🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:
👉 @VIPSelfsazxBot""")
				return
			try:
				chats = await self.get_all_auto_messages()
				if chats:
					msg = "📝 لیست چت‌هایی که پیام اتوماتیک تنظیم شده:\n\n" + "\n".join(f"• {cid}" for cid in chats)
					await event.reply(msg)
				else:
					await event.reply("❌ هیچ پیام اتوماتیکی تنظیم نشده است.")
			except Exception as e:
				await event.reply(f"❌ خطا در دریافت لیست پیام‌های اتوماتیک: {e}")

	async def _schedule_auto_task(self, chat_id: str):
		if chat_id in self.tasks:
			self.tasks[chat_id].cancel()

		async def send_loop():
			failure_count = 0
			max_failures = 3
			while True:
				try:
					data = await self.get_auto_message(chat_id)
					if not data:
						break
					message_data, interval_sec = data
					automsg = json.loads(message_data)
					processed_text = await self._replace_placeholders(automsg.get("text") or automsg.get("raw_text"), chat_id)
					params = {}
					if processed_text:
						params["message"] = processed_text
					media_type = automsg.get("media_type")
					media_data = automsg.get("media_data")
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
					if automsg.get("formatting"):
						from telethon.tl.types import MessageEntityBold, MessageEntityItalic, MessageEntityCode, MessageEntityPre, MessageEntityTextUrl
						entities = []
						for fmt in automsg["formatting"]:
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
					entity = self._resolve_chat_id(chat_id)
					await self.client.send_message(entity=entity, **params)
					failure_count = 0
					await asyncio.sleep(interval_sec)
				except asyncio.CancelledError:
					break
				except Exception as e:
					failure_count += 1
					logger.warning(f"Error in auto message loop for chat {chat_id} (failure {failure_count}): {e}")
					if failure_count >= max_failures:
						logger.warning(f"Auto message for chat {chat_id} removed after {failure_count} consecutive failures.")
						await self.delete_auto_message(chat_id)
						break
					await asyncio.sleep(5)
		self.tasks[chat_id] = asyncio.create_task(send_loop())

	def _resolve_chat_id(self, chat_id: str):
		try:
			if chat_id.startswith("-100"):
				channel_id = int(chat_id.replace("-100", ""))
				return PeerChannel(channel_id)
			else:
				numeric_id = abs(int(chat_id))
				return PeerChat(numeric_id)
		except ValueError:
			return chat_id

	async def _replace_placeholders(self, text, chat_id: str):
		if not text:
			return text
		replacements = {
			"{chat.id}": chat_id,
			"{time}": datetime.now().strftime("%H:%M"),
			"{date}": datetime.now().strftime("%Y-%m-%d"),
			"{day}": datetime.now().strftime("%A")
		}
		for placeholder, value in replacements.items():
			text = text.replace(placeholder, value)
		return text

	async def _extract_media_info(self, message):
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


	async def start(self):
		# Get all chat IDs with auto messages from the DB
		chat_ids = await self.get_all_auto_messages()
		for chat_id in chat_ids:
			# This call reloads the message data from the DB into the cache (if not already)
			data = await self.get_auto_message(chat_id)
			if data:
				logger.info(f"Resuming auto message for chat {chat_id} with interval {data[1]} seconds")
				await self._schedule_auto_task(chat_id)
			else:
				logger.warning(f"No auto message data found for chat {chat_id}, skipping.")