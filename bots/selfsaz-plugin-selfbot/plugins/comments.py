# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
AutoComment Plugin v3 with Simple Commands in Farsi

Features:
- Enable/disable auto-comments via /ct or "تغییر کامنت"
- Set custom comment via /st or "تنظیم کامنت" (works with text or replying to media)
- Clear all custom comments via /cl or "پاکسازی کامنت"
- Messages sent in Farsi
- Preserves captions when using media comments
- No external debug file – logs print only to console
- Keeps user settings persistent (does not force a default off each restart)
"""

from telethon import events, types, functions
from telethon.errors import ChatWriteForbiddenError, FileReferenceExpiredError
from .base import Plugin
import sqlite3
import os
import asyncio
import time
import logging

# Configure logging to print only to console
logging.basicConfig(
    level=logging.DEBUG,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[logging.StreamHandler()]
)
logger = logging.getLogger("AutoComment")

HELP = """  
💬 **کامنت خودکار در گروه‌های بحث** 💬  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• ارسال خودکار کامنت در **گروه‌های مرتبط با کانال**  
• پشتیبانی از کامنت‌های متنی و چندرسانه‌ای (عکس/فایل)  
• ذخیره خودکار تنظیمات حتی پس از ریست ربات  
• قابلیت فعال/غیرفعال کردن سریع با یک دستور  
• مدیریت هوشمند خطاها و آپدیت خودکار لینک‌های رسانه  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:

**انگلیسی:**
  `/ct` ➤ فعال/غیرفعال کردن سیستم  
  `/st [متن]` ➤ تنظیم کامنت (با ریپلای روی رسانه)  
  `/cl` ➤ حذف تمام تنظیمات کامنت  

**فارسی:**
  `تغییر کامنت` ➤ فعال/غیرفعال کردن سیستم  
  `تنظیم کامنت [متن]` ➤ تنظیم کامنت (با ریپلای روی رسانه)  
  `پاکسازی کامنت` ➤ حذف تمام تنظیمات کامنت  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه اجرا**:  
1. ریپلای روی یک عکس/متن در پیوی  
2. ارسال:  
   `/st بهترین پست هفته!` یا `تنظیم کامنت بهترین پست هفته!`  
3. با هر پست جدید در کانال، کامنت به صورت خودکار ارسال می‌شود!  

⚠️ **ملاحظات مهم**:  
- به صورت پیش‌فرض کامنت خاموش است  
- حتما ربات را در گروه بحث ادمین کنید  
- برای رسانه‌ها حتما از دستور در پیوی استفاده کنید  
- کامنت‌ها با تأخیر ۳ ثانیه‌ای ارسال می‌شوند  
"""

class AutoCommentPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.bot_id = None
        self.processed_messages = set()
        self.db_path = "data/comments.db"
        self._init_db()
        # Removed forced enabling on start: the stored setting is preserved.
        logger.info(f"AutoCommentPlugin initialized for user {user_id}")

    def _init_db(self):
        """Initialize database with settings, custom messages, and media tables."""
        try:
            os.makedirs(os.path.dirname(self.db_path), exist_ok=True)
            with sqlite3.connect(self.db_path) as conn:
                # Settings table (on/off toggle)
                conn.execute("""
                    CREATE TABLE IF NOT EXISTS settings (
                        owner_id TEXT PRIMARY KEY,
                        enabled BOOLEAN DEFAULT 0
                    )
                """)
                # Custom text comments table
                conn.execute("""
                    CREATE TABLE IF NOT EXISTS custom_messages (
                        owner_id TEXT PRIMARY KEY,
                        message TEXT
                    )
                """)
                # Media comments table with file reference
                conn.execute("""
                    CREATE TABLE IF NOT EXISTS media_comments (
                        owner_id TEXT PRIMARY KEY,
                        file_id TEXT,
                        file_hash TEXT,
                        file_reference BLOB,
                        media_type TEXT,
                        caption TEXT,
                        timestamp INTEGER
                    )
                """)
                # Insert default setting (off) if not exists
                conn.execute("""
                    INSERT OR IGNORE INTO settings (owner_id, enabled)
                    VALUES (?, ?)
                """, (self.owner_id, 0))
                # Insert default message if none exists
                conn.execute("""
                    INSERT OR IGNORE INTO custom_messages (owner_id, message)
                    VALUES (?, ?)
                """, (self.owner_id, "."))
                conn.commit()
            logger.info("Database initialized successfully")
        except Exception as e:
            logger.error(f"Database initialization error: {str(e)}")

    async def _db_execute(self, query, params):
        """Execute a database query with thread safety."""
        try:
            with sqlite3.connect(self.db_path) as conn:
                conn.execute(query, params)
                conn.commit()
            return True
        except Exception as e:
            logger.error(f"Database execute error: {str(e)}")
            return False

    async def _db_fetch(self, query, params):
        """Fetch a single result from the database."""
        try:
            with sqlite3.connect(self.db_path) as conn:
                cursor = conn.execute(query, params)
                return cursor.fetchone()
        except Exception as e:
            logger.error(f"Database fetch error: {str(e)}")
            return None

    async def _is_enabled(self):
        """Check if auto-comment feature is enabled."""
        try:
            result = await self._db_fetch(
                "SELECT enabled FROM settings WHERE owner_id = ?",
                (self.owner_id,)
            )
            enabled = result[0] if result is not None else 0  # Default to off (0)
            logger.info(f"Auto-comment enabled status: {enabled}")
            return bool(enabled)
        except Exception as e:
            logger.error(f"Error checking enabled status: {str(e)}")
            return False

    async def _get_custom_message(self):
        """Get the user's custom text comment."""
        try:
            result = await self._db_fetch(
                "SELECT message FROM custom_messages WHERE owner_id = ?",
                (self.owner_id,)
            )
            message = result[0] if result else "این کامنت توسط ربات ایجاد شده است!"  # Default message
            logger.info(f"Retrieved custom message: {message}")
            return message
        except Exception as e:
            logger.error(f"Error getting custom message: {str(e)}")
            return "این کامنت توسط ربات ایجاد شده است!"

    async def _get_media(self):
        """Get the user's stored media metadata."""
        try:
            result = await self._db_fetch(
                """SELECT file_id, file_hash, file_reference, media_type, caption, timestamp 
                FROM media_comments WHERE owner_id = ?""",
                (self.owner_id,)
            )
            logger.info(f"Retrieved media data: {result is not None}")
            return result
        except Exception as e:
            logger.error(f"Error getting media data: {str(e)}")
            return None

    async def _refresh_file_reference(self, file_id, file_hash, media_type):
        """Refresh file reference for a media."""
        try:
            logger.info(f"Attempting to refresh file reference for {media_type} with ID {file_id}")
            messages = await self.client.get_messages(None, limit=100)
            for message in messages:
                if media_type == 'photo' and hasattr(message, 'photo') and message.photo and message.photo.id == int(file_id):
                    logger.info("Found matching photo, returning new file reference")
                    return message.photo.file_reference
                elif media_type == 'document' and hasattr(message, 'document') and message.document and message.document.id == int(file_id):
                    logger.info("Found matching document, returning new file reference")
                    return message.document.file_reference
            logger.warning("Could not find media in recent messages to refresh reference")
            return None
        except Exception as e:
            logger.error(f"Failed to refresh file reference: {str(e)}")
            return None

    async def handle_events(self):
        logger.info("Registering AutoComment event handlers")

        @self.client.on(events.NewMessage(pattern=r'^(?:/ct|تغییر کامنت)$'))
        async def toggle_handler(event):
            """Toggle auto-comments on/off."""
            if str(event.sender_id) != self.owner_id:
                return
            try:
                current_state = await self._is_enabled()
                new_state = not current_state
                success = await self._db_execute(
                    "UPDATE settings SET enabled = ? WHERE owner_id = ?",
                    (new_state, self.owner_id)
                )
                if success:
                    state_text = "فعال" if new_state else "غیرفعال"
                    await event.reply(f"🔄 کامنت خودکار اکنون: **{state_text}** است")
                    logger.info(f"Auto-comment toggled to {new_state}")
                else:
                    await event.reply("❌ خطا در تغییر وضعیت")
                    logger.error("Failed to toggle auto-comment state")
            except Exception as e:
                logger.error(f"Toggle handler error: {str(e)}")
                await event.reply("❌ خطایی رخ داد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/st|تنظیم کامنت)(?:\s+(.+))?$'))
        async def setcomment_handler(event):
            """Set custom comment - works with text or reply to media."""
            if str(event.sender_id) != self.owner_id:
                return
            try:
                text_comment = event.pattern_match.group(1).strip() if event.pattern_match.group(1) else None
                logger.info(f"Setting comment with text: {text_comment}")
                is_reply = event.is_reply
                media_message = None
                if is_reply:
                    reply = await event.get_reply_message()
                    if reply.media:
                        media_message = reply
                        logger.info(f"Reply contains media: {type(reply.media).__name__}")
                if not text_comment and not (is_reply and media_message):
                    await event.reply("❌ لطفا یک متن کامنت بنویسید یا به یک پیام رسانه‌ای پاسخ دهید")
                    return
                if text_comment:
                    success = await self._db_execute(
                        """INSERT OR REPLACE INTO custom_messages 
                        (owner_id, message) VALUES (?, ?)""",
                        (self.owner_id, text_comment)
                    )
                    if not success:
                        await event.reply("❌ خطا در ذخیره متن کامنت")
                        return
                if is_reply and media_message:
                    media = media_message.media
                    caption = text_comment or media_message.text or ""
                    file_data = None
                    if isinstance(media, types.MessageMediaPhoto):
                        file_data = (
                            media.photo.id, 
                            media.photo.access_hash, 
                            media.photo.file_reference, 
                            'photo'
                        )
                        logger.info(f"Extracted photo data with ID: {media.photo.id}")
                    elif isinstance(media, types.MessageMediaDocument):
                        doc = media.document
                        file_data = (
                            doc.id, 
                            doc.access_hash, 
                            doc.file_reference, 
                            'document'
                        )
                        logger.info(f"Extracted document data with ID: {doc.id}")
                    if not file_data:
                        await event.reply("❌ نوع رسانه پشتیبانی نمی‌شود")
                        return
                    current_time = int(time.time())
                    success = await self._db_execute(
                        """INSERT OR REPLACE INTO media_comments 
                        (owner_id, file_id, file_hash, file_reference, media_type, caption, timestamp)
                        VALUES (?, ?, ?, ?, ?, ?, ?)""",
                        (self.owner_id, *file_data, caption, current_time)
                    )
                    if not success:
                        await event.reply("❌ خطا در ذخیره رسانه")
                        return
                    if text_comment:
                        await event.reply("✅ کامنت با متن و رسانه تنظیم شد!")
                    else:
                        await event.reply("✅ کامنت رسانه‌ای با عنوان اصلی تنظیم شد!")
                else:
                    await event.reply("✅ کامنت متنی تنظیم شد!")
            except Exception as e:
                logger.error(f"Set comment handler error: {str(e)}")
                await event.reply("❌ خطایی در تنظیم کامنت رخ داد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/cl|پاکسازی کامنت)$'))
        async def clear_handler(event):
            """Clear all custom comments (both text and media)."""
            if str(event.sender_id) != self.owner_id:
                return
            try:
                text_success = await self._db_execute(
                    "DELETE FROM custom_messages WHERE owner_id = ?",
                    (self.owner_id,)
                )
                media_success = await self._db_execute(
                    "DELETE FROM media_comments WHERE owner_id = ?",
                    (self.owner_id,)
                )
                if text_success and media_success:
                    await event.reply("✅ تمام کامنت‌های سفارشی پاک شدند")
                    logger.info("All comments cleared successfully")
                else:
                    await event.reply("❌ خطا در پاک کردن کامنت‌ها")
                    logger.error("Failed to clear comments")
            except Exception as e:
                logger.error(f"Clear handler error: {str(e)}")
                await event.reply("❌ خطایی رخ داد")

        @self.client.on(events.NewMessage)
        async def comment_handler(event):
            try:
                logger.debug(f"Received message: {event.message.id} from {event.chat_id}")
                is_channel = False
                try:
                    if hasattr(event.message.peer_id, 'channel_id'):
                        chat = await event.get_chat()
                        is_channel = chat.broadcast
                        logger.debug(f"Message is from a channel: {is_channel}")
                except Exception as e:
                    logger.error(f"Error checking channel: {str(e)}")
                if not is_channel:
                    return
                if not await self._is_enabled():
                    logger.debug("Auto-comment is disabled, skipping")
                    return
                if await self._is_self_bot(event):
                    logger.debug("Message is from bot itself, skipping")
                    return
                channel_id = event.message.peer_id.channel_id
                message_id = event.message.id
                unique_id = f"{channel_id}:{message_id}"
                logger.info(f"Processing new channel post: {unique_id}")
                if unique_id in self.processed_messages:
                    logger.debug(f"Message {unique_id} already processed, skipping")
                    return
                self.processed_messages.add(unique_id)
                linked_chat_id = await self._get_linked_chat(channel_id)
                if not linked_chat_id:
                    logger.warning(f"No linked chat found for channel {channel_id}")
                    logger.info("Attempting alternative method to find linked chat")
                    try:
                        full_channel = await self.client(functions.channels.GetFullChannelRequest(
                            channel=event.input_chat
                        ))
                        linked_chat_id = full_channel.full_chat.linked_chat_id
                        logger.info(f"Alternative method found linked chat: {linked_chat_id}")
                    except Exception as e:
                        logger.error(f"Alternative method failed: {str(e)}")
                        return
                    if not linked_chat_id:
                        logger.error("No linked chat found with either method")
                        return
                logger.info(f"Found linked chat: {linked_chat_id}")
                try:
                    result = await self.client(functions.messages.GetDiscussionMessageRequest(
                        peer=await self.client.get_input_entity(event.message.peer_id),
                        msg_id=message_id
                    ))
                    if not result or not result.messages:
                        logger.warning("No discussion message found via API")
                        logger.info("Waiting 3 seconds before retrying...")
                        await asyncio.sleep(3)
                        try:
                            result = await self.client(functions.messages.GetDiscussionMessageRequest(
                                peer=await self.client.get_input_entity(event.message.peer_id),
                                msg_id=message_id
                            ))
                        except Exception as e:
                            logger.error(f"Second attempt failed: {str(e)}")
                        if not result or not result.messages:
                            logger.error("No discussion message found after retry")
                            return
                    discussion_msg_id = result.messages[0].id
                    logger.info(f"Found discussion message ID: {discussion_msg_id}")
                    discussion_entity = await self.client.get_entity(types.PeerChannel(linked_chat_id))
                    custom_text = await self._get_custom_message()
                    media_data = await self._get_media()
                    default_text = "این کامنت توسط ربات ایجاد شده است!"
                    logger.info(f"Preparing comment - Has custom text: {custom_text is not None}, Has media: {media_data is not None}")
                    if media_data:
                        file_id, file_hash, file_reference, media_type, caption, timestamp = media_data
                        comment_text = caption or custom_text or default_text
                        current_time = int(time.time())
                        if current_time - timestamp > 86400 or not file_reference:
                            logger.info("File reference may have expired, attempting to refresh")
                            new_file_reference = await self._refresh_file_reference(file_id, file_hash, media_type)
                            if new_file_reference:
                                logger.info("Successfully refreshed file reference")
                                await self._db_execute(
                                    """UPDATE media_comments 
                                    SET file_reference = ?, timestamp = ? 
                                    WHERE owner_id = ?""",
                                    (new_file_reference, current_time, self.owner_id)
                                )
                                file_reference = new_file_reference
                            else:
                                logger.warning("Failed to refresh file reference, falling back to text-only")
                                await self.client.send_message(
                                    entity=discussion_entity,
                                    message=f"{comment_text}\n\n⚠️ پیوست رسانه با مشکل مواجه شد - لطفا با استفاده از /st مجددا تنظیم کنید",
                                    reply_to=discussion_msg_id,
                                    parse_mode='md'
                                )
                                logger.info("Sent text-only comment as fallback")
                                return
                        try:
                            if media_type == 'document':
                                file = types.InputDocument(
                                    id=int(file_id),
                                    access_hash=int(file_hash),
                                    file_reference=file_reference
                                )
                            else:  # photo
                                file = types.InputPhoto(
                                    id=int(file_id),
                                    access_hash=int(file_hash),
                                    file_reference=file_reference
                                )
                            logger.info("Sending comment with media to discussion group")
                            await self.client.send_file(
                                entity=discussion_entity,
                                file=file,
                                message=comment_text,
                                reply_to=discussion_msg_id,
                                parse_mode='md'
                            )
                            logger.info("Media comment sent successfully")
                        except FileReferenceExpiredError:
                            logger.warning("File reference expired during send, falling back to text-only")
                            await self.client.send_message(
                                entity=discussion_entity,
                                message=f"{comment_text}\n\n⚠️ مرجع رسانه منقضی شده است - لطفا با استفاده از /st مجددا تنظیم کنید",
                                reply_to=discussion_msg_id,
                                parse_mode='md'
                            )
                            logger.info("Sent text-only comment as fallback after file reference expired")
                    else:
                        logger.info("Sending text-only comment to discussion group")
                        await self.client.send_message(
                            entity=discussion_entity,
                            message=custom_text or default_text,
                            reply_to=discussion_msg_id,
                            parse_mode='md'
                        )
                        logger.info("Text comment sent successfully")
                except ChatWriteForbiddenError:
                    logger.error(f"Missing permissions in group {linked_chat_id}")
                except Exception as e:
                    logger.error(f"Error processing discussion message: {str(e)}")
            except Exception as e:
                logger.error(f"Comment handler error: {str(e)}")

    async def _get_linked_chat(self, channel_id):
        """Get the linked discussion group ID for a channel."""
        try:
            logger.info(f"Getting linked chat for channel {channel_id}")
            try:
                channel_entity = await self.client.get_entity(types.PeerChannel(channel_id))
                input_channel = types.InputChannel(channel_id=channel_id, access_hash=channel_entity.access_hash)
            except Exception as e:
                logger.warning(f"Could not get full entity, using zero access_hash: {str(e)}")
                input_channel = types.InputChannel(channel_id=channel_id, access_hash=0)
            full_channel = await self.client(functions.channels.GetFullChannelRequest(
                channel=input_channel
            ))
            linked_chat_id = full_channel.full_chat.linked_chat_id
            logger.info(f"Linked chat ID: {linked_chat_id}")
            return linked_chat_id
        except Exception as e:
            logger.error(f"Linked chat error: {str(e)}")
            return None

    async def _is_self_bot(self, event):
        """Check if the message is from the bot itself."""
        try:
            if not self.bot_id:
                me = await self.client.get_me()
                self.bot_id = me.id
                logger.info(f"Bot ID identified as {self.bot_id}")
            sender_id = event.sender_id
            logger.debug(f"Comparing sender_id: {sender_id} with bot_id: {self.bot_id}")
            return sender_id == self.bot_id
        except Exception as e:
            logger.error(f"Error checking if message is from self: {str(e)}")
            return False

    async def shutdown(self):
        """Cleanup on plugin shutdown."""
        logger.info("AutoComment plugin shutting down")
