# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, functions, types
import re
import asyncio
from emoji import EMOJI_DATA
import logging
from .db_utils import execute_query  # This is now an async function

logger = logging.getLogger(__name__)

HELP = """  
🔒 **مدیریت هوشمند محدودیت‌های محتوایی** 🔒  

این افزونه به شما امکان مدیریت محدودیت‌ها را می‌دهد.
شما می‌توانید از یکی از دو نسخه زیر برای قفل و آزادسازی محتوا استفاده کنید:

    /lock [نوع]
    قفل [نوع]
    /unlock [نوع]
    بازکردن قفل [نوع]
    /locklist
    لیست قفل
    /adminexempt [on/off]
    معافیت ادمین [on/off]

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های کلیدی**:  
• مسدودسازی ۱۵+ نوع محتوا (لینک، گیف، انگلیسی، ایموجی، اسپم و...)  
• تشخیص خودکار اسپم و پیام‌های تکراری  
• معافیت ادمین‌ها از محدودیت‌ها  
• مدیریت لیست فعال محدودیت‌ها  
• پشتیبانی از استیکرهای معمولی و انیمیشنی  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات اصلی**:

**انگلیسی:**
  `/lock [type]` ➔ قفل کردن محتوای خاص  
  `/unlock [type]` ➔ بازکردن قفل  
  `/locklist` ➔ نمایش محدودیت‌های فعال  
  `/adminexempt [on/off]` ➔ تنظیم معافیت ادمین‌ها  

**فارسی:**
  `قفل [نوع]` ➔ قفل کردن محتوای خاص  
  `بازکردن قفل [نوع]` ➔ آزادسازی قفل  
  `لیست قفل` ➔ نمایش محدودیت‌های فعال  
  `معافیت ادمین [on/off]` ➔ تنظیم معافیت ادمین‌ها  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه اجرا**:  
1. قفل کردن لینک‌ها در گروه:  
   `/lock link` یا `قفل link`  
2. آزادسازی قفل لینک‌ها:  
   `/unlock link` یا `بازکردن قفل link`  
3. نمایش لیست محدودیت‌ها:  
   `/locklist` یا `لیست قفل`  
4. غیرفعال کردن معافیت ادمین‌ها:  
   `/adminexempt off` یا `معافیت ادمین off`
"""

class ContentLockPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        # Schedule asynchronous initialization of required tables.
        asyncio.create_task(self._init_db_async())
        
        # Regular expressions for content detection
        self.link_pattern = re.compile(
            r'(https?://\S+|www\.\S+|\S+\.\S+/\S*|\S+\.(com|org|net|edu|gov|io|co|me|xyz|info|app)(/\S*)?)'
        )
        self.english_pattern = re.compile(r'[a-zA-Z]+')
        self.mention_pattern = re.compile(r'@\w+')
        self.spam_threshold = 5  # Number of same messages in a short period to consider spam

        # Cache for spam detection (chat_id -> {user_id -> [recent_messages]})
        self.message_cache = {}
        # Lock status cache to reduce database queries
        self.lock_cache = {}
        # Default filter mode
        self.filter_mode = "delete"  # Or "warn"

    async def _init_db_async(self):
        """Asynchronously create the MySQL tables needed for the content lock plugin."""
        try:
            await execute_query("""
            CREATE TABLE IF NOT EXISTS content_locks (
                chat_id VARCHAR(64),
                lock_type VARCHAR(32),
                PRIMARY KEY (chat_id, lock_type)
            )
            """, None, fetch=False)
            
            await execute_query("""
            CREATE TABLE IF NOT EXISTS admin_exceptions (
                chat_id VARCHAR(64) PRIMARY KEY
            )
            """, None, fetch=False)
            
            await execute_query("""
            CREATE TABLE IF NOT EXISTS filtered_words (
                id INT AUTO_INCREMENT PRIMARY KEY,
                owner_id VARCHAR(32),
                word VARCHAR(255),
                INDEX (owner_id)
            )
            """, None, fetch=False)
            
            logger.info("Database tables for content lock initialized.")
        except Exception as e:
            logger.error(f"Error initializing DB tables: {e}")

    async def _is_locked(self, chat_id, lock_type):
        """Check if a specific lock type is active for a chat."""
        cache_key = f"{chat_id}_{lock_type}"
        if cache_key in self.lock_cache:
            return self.lock_cache[cache_key]
        result = await execute_query(
            "SELECT 1 FROM content_locks WHERE chat_id = %s AND lock_type = %s",
            (str(chat_id), lock_type),
            fetch=True
        )
        is_locked = bool(result)
        self.lock_cache[cache_key] = is_locked
        return is_locked

    async def _is_admin_exempt(self, chat_id):
        """Check if admins are exempt from locks in this chat."""
        result = await execute_query(
            "SELECT 1 FROM admin_exceptions WHERE chat_id = %s",
            (str(chat_id),),
            fetch=True
        )
        return bool(result)

    async def _is_user_admin(self, chat_id, user_id):
        """Check if user is an admin in the chat."""
        try:
            participant = await self.client(functions.channels.GetParticipantRequest(
                channel=chat_id,
                participant=user_id
            ))
            return isinstance(participant.participant, (types.ChannelParticipantAdmin, types.ChannelParticipantCreator))
        except Exception:
            return False

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/lock|قفل)\s+(\w+)$'))
        async def lock_handler(event):
            if not event.is_group and not event.is_channel:
                return
            if str(event.sender_id) != self.owner_id:
                return

            lock_type = event.pattern_match.group(1).lower()
            valid_types = ["link", "gif", "english", "emoji", "mention", "spam", "forward", "bot",
                           "sticker", "asticker", "photo", "pemoji", "پموجی", "all"]
            if lock_type not in valid_types:
                await event.reply(f"❌ Invalid lock type. Valid types: {', '.join(valid_types)}")
                return
            chat_id = str(event.chat_id)
            if lock_type == "all":
                for type_name in valid_types:
                    if type_name != "all":
                        await execute_query(
                            "INSERT IGNORE INTO content_locks (chat_id, lock_type) VALUES (%s, %s)",
                            (chat_id, type_name),
                            fetch=False
                        )
                        self.lock_cache[f"{chat_id}_{type_name}"] = True
                await event.reply("✅ All content types are now locked in this chat")
                return
            await execute_query(
                "INSERT IGNORE INTO content_locks (chat_id, lock_type) VALUES (%s, %s)",
                (chat_id, lock_type),
                fetch=False
            )
            self.lock_cache[f"{chat_id}_{lock_type}"] = True
            await event.reply(f"✅ {lock_type.capitalize()} has been locked in this chat")

        @self.client.on(events.NewMessage(pattern=r'^(?:/unlock|بازکردن\s+قفل)\s+(\w+)$'))
        async def unlock_handler(event):
            if not event.is_group and not event.is_channel:
                return
            if str(event.sender_id) != self.owner_id:
                return

            lock_type = event.pattern_match.group(1).lower()
            valid_types = ["link", "gif", "english", "emoji", "mention", "spam", "forward", "bot",
                           "sticker", "asticker", "photo", "pemoji", "پموجی", "all"]
            if lock_type not in valid_types:
                await event.reply(f"❌ Invalid lock type. Valid types: {', '.join(valid_types)}")
                return
            chat_id = str(event.chat_id)
            if lock_type == "all":
                await execute_query(
                    "DELETE FROM content_locks WHERE chat_id = %s",
                    (chat_id,),
                    fetch=False
                )
                for key in list(self.lock_cache.keys()):
                    if key.startswith(f"{chat_id}_"):
                        del self.lock_cache[key]
                await event.reply("✅ All content types are now unlocked in this chat")
                return
            await execute_query(
                "DELETE FROM content_locks WHERE chat_id = %s AND lock_type = %s",
                (chat_id, lock_type),
                fetch=False
            )
            if f"{chat_id}_{lock_type}" in self.lock_cache:
                del self.lock_cache[f"{chat_id}_{lock_type}"]
            await event.reply(f"✅ {lock_type.capitalize()} has been unlocked in this chat")

        @self.client.on(events.NewMessage(pattern=r'^(?:/locklist|لیست\s+قفل)$'))
        async def locklist_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            chat_id = str(event.chat_id)
            locks = await execute_query(
                "SELECT lock_type FROM content_locks WHERE chat_id = %s",
                (chat_id,),
                fetch=True
            )
            if not locks:
                await event.reply("❌ No active locks in this chat")
                return
            lock_types = [lock['lock_type'] if isinstance(lock, dict) else lock[0] for lock in locks]
            lock_descriptions = {
                "link": "Links",
                "gif": "GIFs",
                "english": "English text",
                "emoji": "Regular emojis",
                "mention": "Username mentions",
                "spam": "Spam messages",
                "forward": "Forwarded messages",
                "bot": "Bot messages",
                "sticker": "Stickers",
                "asticker": "Animated stickers",
                "photo": "Photos",
                "pemoji": "Premium emojis",
                "پموجی": "Premium emojis (پموجی)"
            }
            formatted_locks = []
            for lt in lock_types:
                desc = lock_descriptions.get(lt, lt.capitalize())
                formatted_locks.append(f"• {desc}")
            locks_text = "\n".join(formatted_locks)
            await event.reply(f"🔒 Active locks in this chat:\n\n{locks_text}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/adminexempt|معافیت\s+ادمین)\s+(on|off)$'))
        async def admin_exempt_handler(event):
            if not event.is_group and not event.is_channel:
                return
            if str(event.sender_id) != self.owner_id:
                return
            status = event.pattern_match.group(1).lower()
            chat_id = str(event.chat_id)
            if status == "on":
                await execute_query(
                    "INSERT IGNORE INTO admin_exceptions (chat_id) VALUES (%s)",
                    (chat_id,),
                    fetch=False
                )
                await event.reply("✅ Admins are now exempt from content locks")
            else:
                await execute_query(
                    "DELETE FROM admin_exceptions WHERE chat_id = %s",
                    (chat_id,),
                    fetch=False
                )
                await event.reply("✅ Admins are no longer exempt from content locks")

        @self.client.on(events.NewMessage(incoming=True))
        async def content_filter_handler(event):
            if not event.is_group and not event.is_channel:
                return
            if not event.sender_id or str(event.sender_id) == self.owner_id:
                return
            chat_id = str(event.chat_id)
            is_admin_exempt = await self._is_admin_exempt(chat_id)
            if is_admin_exempt and await self._is_user_admin(event.chat_id, event.sender_id):
                return
            if not event.raw_text:
                return
            message_text = event.raw_text.lower()
            filtered_words = await execute_query(
                "SELECT word FROM filtered_words WHERE owner_id = %s",
                (self.owner_id,),
                fetch=True
            )
            import re
            for word_tuple in filtered_words:
                word = word_tuple['word'] if isinstance(word_tuple, dict) else word_tuple[0]
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
            if await self._check_locked_content(chat_id, event):
                try:
                    await event.delete()
                except Exception as e:
                    logger.error(f"Failed to delete message: {e}")

        @self.client.on(events.ChatAction)
        async def spam_check_handler(event):
            # This handler can be used to monitor spam in real-time if needed.
            pass

    async def _check_locked_content(self, chat_id, event):
        """Check if message contains any locked content type."""
        message = event.message
        if await self._is_locked(chat_id, "link") and self.link_pattern.search(message.text or ""):
            return True
        if await self._is_locked(chat_id, "gif") and getattr(message.media, "gif", False):
            return True
        if await self._is_locked(chat_id, "english") and self.english_pattern.search(message.text or ""):
            return True
        if await self._is_locked(chat_id, "emoji") and any(c in EMOJI_DATA for c in (message.text or "")):
            return True
        if await self._is_locked(chat_id, "mention") and self.mention_pattern.search(message.text or ""):
            return True
        if await self._is_locked(chat_id, "spam") and await self._is_spam(chat_id, event):
            return True
        if await self._is_locked(chat_id, "forward") and message.forward:
            return True
        if await self._is_locked(chat_id, "bot"):
            sender = await event.get_sender()
            if getattr(sender, "bot", False):
                return True
        if await self._is_locked(chat_id, "sticker") and message.sticker:
            return True
        if await self._is_locked(chat_id, "asticker") and message.sticker and getattr(message.sticker, "animated", False):
            return True
        if await self._is_locked(chat_id, "photo") and message.photo:
            return True
        if (await self._is_locked(chat_id, "pemoji") or await self._is_locked(chat_id, "پموجی")) and getattr(message, "entities", None):
            for entity in message.entities:
                if getattr(entity, "premium", False):
                    return True
        return False

    async def _is_spam(self, chat_id, event):
        """Check if a message is spam based on repetition patterns."""
        if not event.text:
            return False
        user_id = event.sender_id
        if chat_id not in self.message_cache:
            self.message_cache[chat_id] = {}
        if user_id not in self.message_cache[chat_id]:
            self.message_cache[chat_id][user_id] = []
        user_messages = self.message_cache[chat_id][user_id]
        current_time = event.date.timestamp()
        user_messages.append((event.text, current_time))
        user_messages = [(text, t) for text, t in user_messages if current_time - t <= 60]
        self.message_cache[chat_id][user_id] = user_messages
        message_counts = {}
        for text, _ in user_messages:
            message_counts[text] = message_counts.get(text, 0) + 1
        return any(count >= self.spam_threshold for count in message_counts.values())

    async def add_filtered_word(self, word):
        """Add a word to the filtered words list."""
        await execute_query(
            "INSERT INTO filtered_words (owner_id, word) VALUES (%s, %s)",
            (self.owner_id, word),
            fetch=False
        )

    async def remove_filtered_word(self, word):
        """Remove a word from the filtered words list."""
        await execute_query(
            "DELETE FROM filtered_words WHERE owner_id = %s AND word = %s",
            (self.owner_id, word),
            fetch=False
        )

    async def get_filtered_words(self):
        """Get all filtered words for this owner."""
        result = await execute_query(
            "SELECT word FROM filtered_words WHERE owner_id = %s",
            (self.owner_id,),
            fetch=True
        )
        if result:
            return [row['word'] if isinstance(row, dict) else row[0] for row in result]
        return []

    def set_filter_mode(self, mode):
        """Set the filter mode (delete or warn)."""
        if mode in ["delete", "warn"]:
            self.filter_mode = mode
            return True
        return False
