# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telethon import events
from .base import Plugin
from .db_utils import execute_query
import asyncio
import logging

logger = logging.getLogger("SignMode")

HELP = """
🖋️ **امضای خودکار** 🖋️

• `/setsign [متن]` — تنظیم امضا  
• `/removesign` — حذف امضا  

امضا به‌صورت خودکار به انتهای پیام‌های شما اضافه می‌شود.
"""

class SignModePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.user_id = str(user_id)
        # In-memory signature cache for immediate change
        self._signature = None  
        # Create a combined task for initialization and wait for it to complete
        self._init_task = asyncio.create_task(self._initialize())
        logger.info("✅ SignModePlugin initialization started")

    async def _initialize(self):
        """Handle complete initialization sequence in proper order"""
        await self._ensure_table()
        await self._load_signature()
        logger.info("✅ SignModePlugin fully initialized")

    async def _ensure_table(self):
        """Create the signatures table if it doesn't exist."""
        query = """
        CREATE TABLE IF NOT EXISTS botdata.signatures (
            user_id VARCHAR(32) PRIMARY KEY,
            sign_text TEXT NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
        """
        await execute_query(query, fetch=False)
        logger.info("✅ SignMode table ready")

    async def _load_signature(self):
        """Load the signature from the database at startup into memory."""
        query = """
        SELECT sign_text FROM botdata.signatures
        WHERE user_id = %s AND is_active = TRUE
        """
        result = await execute_query(query, (self.user_id,), fetch=True)
        if result:
            self._signature = result[0]["sign_text"].strip()
            logger.info(f"✍️ Loaded signature: {self._signature}")
        else:
            self._signature = None
            logger.info("ℹ️ No active signature found")

    async def _set_signature(self, text: str):
        """Set the new signature in both the DB and the in-memory variable."""
        text = text.strip()
        query = """
        INSERT INTO botdata.signatures (user_id, sign_text, is_active)
        VALUES (%s, %s, TRUE) AS new
        ON DUPLICATE KEY UPDATE sign_text = new.sign_text, is_active = TRUE
        """
        await execute_query(query, (self.user_id, text), fetch=False)
        self._signature = text
        logger.info(f"✍️ Signature set to: {self._signature}")

    async def _remove_signature(self):
        """Disable the signature (update DB and clear in-memory)."""
        query = "UPDATE botdata.signatures SET is_active = FALSE WHERE user_id = %s"
        await execute_query(query, (self.user_id,), fetch=False)
        self._signature = None
        logger.info("🚫 Signature removed")

    async def handle_events(self):
        # Make sure initialization is complete before handling events
        await self._init_task
        
        # Handle setting signature:
        @self.client.on(events.NewMessage(pattern=r'^/setsign\s+(.+)$'))
        async def set_sign_handler(event):
            if str(event.sender_id) != self.user_id:
                return
            sign_text = event.pattern_match.group(1)
            await self._set_signature(sign_text)
            await event.reply(f"✅ امضا تنظیم شد:\n`{sign_text}`")

        # Handle removing signature:
        @self.client.on(events.NewMessage(pattern=r'^/removesign$'))
        async def remove_sign_handler(event):
            if str(event.sender_id) != self.user_id:
                return
            await self._remove_signature()
            await event.reply("✅ امضا حذف شد")

        # Automatically append signature to outgoing messages:
        @self.client.on(events.NewMessage())
        async def sign_appender(event):
            # Only process owner's outgoing messages without reply or media
            if (str(event.sender_id) != self.user_id or not event.out or 
                not event.text or event.is_reply or event.media):
                return
            # Use the in-memory signature for immediate effect
            if not self._signature:
                return
            text = event.text.strip()
            # Avoid double-signing if already appended
            if text.endswith(self._signature):
                return
            new_text = f"{text}\n\n{self._signature}"
            try:
                await event.edit(new_text)
                logger.debug(f"✍️ Message {event.id} signed.")
            except Exception as e:
                logger.error(f"❌ Failed to sign message {event.id}: {e}")