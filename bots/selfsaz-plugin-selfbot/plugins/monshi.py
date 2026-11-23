# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, functions, types
from telethon.tl.types import UserStatusOffline, UserStatusOnline
import asyncio
from datetime import datetime, timedelta
from collections import defaultdict
import logging
import random

from .db_utils import execute_query  # Must be async

logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger('MonshiPlugin')

HELP = """
🤖 **پلاگین حالت آفلاین منشی** 🤖

▬▬▬▬▬▬▬▬▬▬▬▬

🔧 **قابلیت‌ها**:  
  • پاسخ خودکار به پیام‌های خصوصی در حالت آفلاین  
  • پشتیبانی از متن و رسانه (عکس، فیلم، فایل)  
  • سیستم ضد اسپم هوشمند  
  • پشتیبانی از تگ‌های پویای کاربر:
  
      - {first.name}         : نام کوچک کاربر  
      - {first.name.upper}   : نام کوچک با حروف بزرگ  
      - {first.name.lower}   : نام کوچک با حروف کوچک  
      - {last.name}          : نام خانوادگی  
      - {username}           : نام کاربری (با @)  
      - {full.name}          : نام کامل کاربر  
      - {user.id}            : شناسه کاربر  
      - {mention}            : منشن کاربر (با @ در صورت وجود نام کاربری)  
      - {date}               : تاریخ کنونی (YYYY-MM-DD)  
      - {time}               : زمان کنونی (HH:MM)  
      - {day}                : روز ماه (DD)  
      - {month}              : نام ماه  
      - {year}               : سال کنونی  
      - {day.of.week}        : روز هفته  
      - {hour}               : ساعت کنونی  
      - {minute}             : دقیقه کنونی  
      - {second}             : ثانیه کنونی  

▬▬▬▬▬▬▬▬▬▬▬▬

📌 **دستورات**:

  • **Toggle Auto-Reply**:  
      `/monshi on`  or  `/monshi off`   or   `منشی روشن`  or  `منشی خاموش`

  • **Set Reply Text/Media**:  
      `/monshitext`  or  `منشی متنی`  (برای تنظیم پاسخ، ریپلای روی پیام مورد نظر را انجام دهید)

  • **Show Settings**:  
      `/monshishow`  or  `منشی نمایش`

  • **Set Cooldown**:  
      `/monshicooldown <seconds>`  or  `منشی زمانانتظار <ثانیه>`

  • **Help**:  
      `/monshihelp`  or  `منشی کمک`
"""

class MonshiPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.user_cooldowns = {}  # {user_id: last_response_time}
        self.spam_count = defaultdict(int)

        # For caching settings to reduce DB calls
        self._settings_cache = None     
        self._cache_timestamp = None    
        self._cache_ttl = 999999        

        asyncio.create_task(self._init_db_async())
        logger.info(f"MonshiPlugin initialized for owner ID: {self.owner_id}")

    async def _init_db_async(self):
        """
        Create 'monshi_settings' table if not exists,
        and ensure 'file_reference' column exists.
        """
        try:
            await execute_query("""
                CREATE TABLE IF NOT EXISTS monshi_settings (
                    owner_id VARCHAR(32) PRIMARY KEY,
                    enabled INT DEFAULT 0,
                    message_text TEXT,
                    media_id BIGINT,
                    media_access_hash BIGINT,
                    media_type TEXT,
                    cooldown INT DEFAULT 60,
                    file_reference BLOB
                )
            """, None, fetch=False)

            # Check if 'file_reference' column exists
            result = await execute_query(
                "SHOW COLUMNS FROM monshi_settings LIKE 'file_reference'",
                None, fetch=True
            )
            if not result:
                await execute_query(
                    "ALTER TABLE monshi_settings ADD COLUMN file_reference BLOB",
                    None, fetch=False
                )
            logger.info("Database initialized successfully for Monshi.")
        except Exception as e:
            logger.error(f"Error initializing database for Monshi: {str(e)}")

    # --------------------------------------------------------------------------
    #                            DB & CACHING
    # --------------------------------------------------------------------------
    async def _db_fetchone(self) -> dict:
        """
        Return monshi_settings row for self.owner_id as a dict, or None if not found.
        Uses caching to avoid repeated DB hits.
        """
        # Check cache
        if self._settings_cache and self._cache_timestamp:
            if (datetime.now() - self._cache_timestamp).total_seconds() < self._cache_ttl:
                return self._settings_cache

        rows = await execute_query(
            "SELECT * FROM monshi_settings WHERE owner_id = %s",
            (self.owner_id,),
            fetch=True
        )
        if rows and len(rows) > 0:
            self._settings_cache = dict(rows[0])
        else:
            self._settings_cache = None

        self._cache_timestamp = datetime.now()
        return self._settings_cache

    async def _db_update_settings(self, **kwargs):
        """
        Update or insert monshi_settings with the given columns => values.
        Then invalidate the cache so future reads re-fetch from DB.
        """
        set_clause = ", ".join(f"{col}=%s" for col in kwargs.keys())
        values = list(kwargs.values())

        existing = await self._db_fetchone()
        if existing is None:
            # Insert
            columns = ", ".join(kwargs.keys())
            placeholders = ", ".join(["%s"] * len(kwargs))
            insert_q = f"INSERT INTO monshi_settings (owner_id, {columns}) VALUES (%s, {placeholders})"
            await execute_query(insert_q, (self.owner_id, *values), fetch=False)
        else:
            # Update
            update_q = f"UPDATE monshi_settings SET {set_clause} WHERE owner_id=%s"
            await execute_query(update_q, (*values, self.owner_id), fetch=False)

        # Invalidate cache
        self._settings_cache = None
        self._cache_timestamp = None

    # --------------------------------------------------------------------------
    #                        SPAM & OFFLINE CHECKS
    # --------------------------------------------------------------------------
    def _check_spam(self, user_id: str) -> bool:
        """
        Check if a user is spamming (more than 5 messages in 5 minutes).
        """
        now = datetime.now()
        if user_id in self.user_cooldowns:
            if now - self.user_cooldowns[user_id] > timedelta(minutes=5):
                self.spam_count[user_id] = 0
        self.spam_count[user_id] += 1
        return self.spam_count[user_id] > 5

    async def _is_me_offline(self) -> bool:
        """
        Checks if 'me' (the owner's account) is offline via Telethon's user status.
        If status is UserStatusOnline => online
        If status is UserStatusOffline => offline
        Otherwise => assume offline
        """
        me = await self.client.get_entity("me")
        status = getattr(me, 'status', None)
        if isinstance(status, UserStatusOnline):
            return False
        return True  # If explicitly offline or no status, treat as offline

    # --------------------------------------------------------------------------
    #                          TAG PROCESSING
    # --------------------------------------------------------------------------
    async def _process_tags(self, text, user):
        """
        Replace placeholders (e.g. {first.name}, {time}, etc.) with user info & date/time.
        """
        if not text:
            return text
        try:
            if not hasattr(user, 'first_name'):
                user = await self.client.get_entity(user)
            now = datetime.now()
            replacements = {
                '{first.name}': user.first_name or "کاربر",
                '{first.name.upper}': (user.first_name or "کاربر").upper(),
                '{first.name.lower}': (user.first_name or "کاربر").lower(),
                '{last.name}': user.last_name or "",
                '{username}': f"@{user.username}" if user.username else "کاربر",
                '{full.name}': f"{user.first_name or ''} {user.last_name or ''}".strip() or "کاربر",
                '{user.id}': str(user.id),
                '{mention}': f"@{user.username}" if user.username else (user.first_name or "کاربر"),
                '{date}': now.strftime("%Y-%m-%d"),
                '{time}': now.strftime("%H:%M"),
                '{day}': now.strftime("%d"),
                '{month}': now.strftime("%B"),
                '{year}': now.strftime("%Y"),
                '{day.of.week}': now.strftime("%A"),
                '{hour}': now.strftime("%H"),
                '{minute}': now.strftime("%M"),
                '{second}': now.strftime("%S"),
            }
            for tag, val in replacements.items():
                text = text.replace(tag, val)
            return text
        except Exception as e:
            logger.error(f"Error processing tags: {str(e)}")
            return text

    # --------------------------------------------------------------------------
    #                            EVENT HANDLERS
    # --------------------------------------------------------------------------
    async def handle_events(self):
        # Toggle on/off
        @self.client.on(events.NewMessage(pattern=r'^(?:/monshi|منشی) (on|off|روشن|خاموش)$'))
        async def toggle_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            cmd = event.pattern_match.group(1)
            state = True if cmd in ["on", "روشن"] else False
            try:
                await self._db_update_settings(enabled=int(state))
                await event.reply(f"✅ حالت پاسخ خودکار {'فعال' if state else 'غیرفعال'} شد")
            except Exception as e:
                logger.error(f"Error in toggle_handler: {str(e)}")
                await event.reply("❌ مشکلی در تنظیم حالت پاسخ خودکار رخ داد")

        # Set text/media
        @self.client.on(events.NewMessage(pattern=r'^(?:/monshitext|منشی متنی)$'))
        async def text_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if not event.is_reply:
                await event.reply("❌ لطفاً به پیام مورد نظر ریپلای کنید")
                return

            reply = await event.get_reply_message()
            media_id = None
            access_hash = None
            media_type = None
            message_text = None
            file_reference = None

            # Replied message text
            if reply.text:
                message_text = reply.text

            # Replied media
            if reply.media:
                if hasattr(reply.media, 'document'):
                    media = reply.media.document
                    media_id = media.id
                    access_hash = media.access_hash
                    file_reference = media.file_reference
                    media_type = 'document'
                elif hasattr(reply.media, 'photo'):
                    media = reply.media.photo
                    media_id = media.id
                    access_hash = media.access_hash
                    file_reference = media.file_reference
                    media_type = 'photo'

            # If text has placeholders, show them
            if message_text and ('{' in message_text and '}' in message_text):
                tags_explanation = (
                    "ℹ️ تگ‌های پشتیبانی شده:\n"
                    "• {first.name}\n• {first.name.upper}\n• {first.name.lower}\n"
                    "• {last.name}\n• {username}\n• {full.name}\n"
                    "• {user.id}\n• {mention}\n• {date}\n• {time}\n"
                    "• {day}\n• {month}\n• {year}\n• {day.of.week}\n"
                    "• {hour}\n• {minute}\n• {second}"
                )
                await event.reply(tags_explanation)

            try:
                await self._db_update_settings(
                    message_text=message_text,
                    media_id=media_id,
                    media_access_hash=access_hash,
                    media_type=media_type,
                    file_reference=file_reference
                )
                await event.reply("✅ پاسخ جدید با موفقیت تنظیم شد")
            except Exception as e:
                logger.error(f"Error in text_handler: {str(e)}")
                await event.reply("❌ مشکلی در تنظیم پاسخ رخ داد")

        # Show current settings
        @self.client.on(events.NewMessage(pattern=r'^(?:/monshishow|منشی نمایش)$'))
        async def show_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            try:
                row = await self._db_fetchone()
                if not row:
                    response = "⚙️ تنظیمات فعلی:\nوضعیت: غیرفعال\nپاسخ: تنظیم نشده\nمدت زمان انتظار: 60 ثانیه"
                else:
                    enabled = row.get('enabled', 0)
                    text = row.get('message_text')
                    media_type = row.get('media_type')
                    cooldown_val = row.get('cooldown', 60)
                    status = "فعال" if enabled else "غیرفعال"
                    response = f"⚙️ تنظیمات فعلی:\nوضعیت: {status}\n"

                    if text:
                        snippet = text[:30] + "..." if len(text) > 30 else text
                        response += f"نوع پاسخ: متن\nمتن پاسخ: {snippet}\n"
                    elif media_type:
                        response += f"نوع پاسخ: رسانه ({media_type})\n"
                    else:
                        response += "نوع پاسخ: تنظیم نشده\n"

                    response += f"مدت زمان انتظار: {cooldown_val} ثانیه"
                await event.reply(response)
            except Exception as e:
                logger.error(f"Error in show_handler: {str(e)}")
                await event.reply(f"❌ خطایی رخ داد: {str(e)}")

        # Set cooldown
        @self.client.on(events.NewMessage(pattern=r'^(?:/monshicooldown\s+(\d+)|منشی زمانانتظار\s+(\d+))$'))
        async def cooldown_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            new_cd = event.pattern_match.group(1) or event.pattern_match.group(2)
            try:
                new_cooldown = int(new_cd)
                await self._db_update_settings(cooldown=new_cooldown)
                await event.reply(f"✅ مدت زمان انتظار بین پاسخ‌ها به {new_cooldown} ثانیه تنظیم شد")
            except Exception as e:
                logger.error(f"Error in cooldown_handler: {str(e)}")
                await event.reply(f"❌ خطایی رخ داد: {str(e)}")

        # Help
        @self.client.on(events.NewMessage(pattern=r'^(?:/monshihelp|منشی کمک)$'))
        async def help_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            await event.reply(HELP)

        # Auto-reply to incoming private messages, only if me is offline
        @self.client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
        async def response_handler(event):
            try:
                sender_str = str(event.sender_id)
                # Don’t reply to ourselves
                if sender_str == self.owner_id:
                    return

                # Check if me is offline
                if not await self._is_me_offline():
                    return

                row = await self._db_fetchone()
                if not row or not row.get('enabled'):
                    return  # Not enabled

                # spam check
                if self._check_spam(sender_str):
                    return

                # cooldown check
                now = datetime.now()
                cooldown_val = row.get('cooldown', 60)
                if sender_str in self.user_cooldowns:
                    if now - self.user_cooldowns[sender_str] < timedelta(seconds=cooldown_val):
                        return

                raw_text = row.get('message_text', '')
                # Possibly multiple responses, split by "|||"
                if "|||" in raw_text:
                    parts = [p.strip() for p in raw_text.split("|||") if p.strip()]
                    if parts:
                        raw_text = random.choice(parts)

                # Replace tags
                final_text = await self._process_tags(raw_text, event.sender_id)
                if final_text and len(final_text) > 1024:
                    final_text = final_text[:1020] + "..."

                media_id = row.get('media_id')
                access_hash = row.get('media_access_hash')
                media_type = row.get('media_type')
                file_reference = row.get('file_reference') or b''

                # Try to send media (document/photo)
                replied = False
                if media_id and access_hash and media_type:
                    try:
                        if media_type == 'document':
                            file = types.InputDocument(
                                id=int(media_id),
                                access_hash=int(access_hash),
                                file_reference=file_reference
                            )
                        else:
                            file = types.InputPhoto(
                                id=int(media_id),
                                access_hash=int(access_hash),
                                file_reference=file_reference
                            )
                        if final_text:
                            await event.reply(final_text, file=file)
                        else:
                            await event.reply(file=file)
                        replied = True
                    except Exception as ex:
                        logger.error(f"Error sending media: {ex}")

                # Fallback to text only if media failed or not set
                if not replied and final_text:
                    await event.reply(final_text)

                # Mark last response time
                self.user_cooldowns[sender_str] = now

                # ---------------------------------------------
                # Force you offline again (after replying)
                # ---------------------------------------------
                try:
                    await self.client(functions.account.UpdateStatusRequest(offline=True))
                    logger.info("Forced account offline after replying.")
                except Exception as e2:
                    logger.error(f"Error forcing offline status: {e2}")

            except Exception as e:
                logger.error(f"Error in response_handler: {str(e)}")

    async def start(self):
        """
        Called when the bot/plugin is starting up.
        We'll load our existing settings from the DB (if any) into the cache,
        so the plugin is aware of prior toggles etc.
        """
        settings = await self._db_fetchone()
        if settings:
            logger.info(f"[MonshiPlugin] Loaded existing settings from DB: {settings}")
        else:
            logger.info("[MonshiPlugin] No existing settings found in DB.")
