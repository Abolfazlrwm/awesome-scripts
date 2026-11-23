# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
from telethon.errors import FloodWaitError, MessageNotModifiedError, ChatAdminRequiredError
import logging
import asyncio
from .db_utils import execute_query

logger = logging.getLogger(__name__)

HELP = """
🖋️ **مدیریت پیشرفته قالب‌بندی متن** 🖋️
(همراه با رفع مشکل عدم تغییر با اولین دستور)

📌 دستورات:
/bold — فعال/غیرفعال بولد
/italic — فعال/غیرفعال کج
/underline — فعال/غیرفعال زیرخط
/strike — فعال/غیرفعال خط‌خورده
/code — فعال/غیرفعال کد خطی
/pre — فعال/غیرفعال بلاک کد
/clearformat — حذف همه فرمت‌ها
"""

# ---------- Utility Functions ----------

async def ensure_format_row(owner_id: str):
    """Ensure the row exists only once."""
    query = """
    INSERT IGNORE INTO text_format (owner_id, bold, italic, underline, strike, code, pre)
    VALUES (%s, 0, 0, 0, 0, 0, 0)
    """
    await execute_query(query, (owner_id,), fetch=False)

async def get_text_format(owner_id: str) -> dict:
    """Get current formatting settings."""
    await ensure_format_row(owner_id)
    query = "SELECT bold, italic, underline, strike, code, pre FROM text_format WHERE owner_id = %s"
    result = await execute_query(query, (owner_id,), fetch=True)
    if result:
        row = result[0]
        return {
            'bold': bool(row.get('bold')),
            'italic': bool(row.get('italic')),
            'underline': bool(row.get('underline')),
            'strike': bool(row.get('strike')),
            'code': bool(row.get('code')),
            'pre': bool(row.get('pre'))
        }
    return {key: False for key in ['bold', 'italic', 'underline', 'strike', 'code', 'pre']}

async def update_text_format(owner_id: str, format_type: str, value: bool) -> bool:
    """Update the format toggle."""
    await ensure_format_row(owner_id)
    query = f"UPDATE text_format SET {format_type} = %s WHERE owner_id = %s"
    result = await execute_query(query, (1 if value else 0, owner_id), fetch=False)
    return result is not None

# ---------- Plugin Definition ----------

class TextFormatPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        asyncio.create_task(self._init_db())
        logger.info("✅ TextFormatPlugin initialized")

    async def _init_db(self):
        """Create table if not exists."""
        query = """
        CREATE TABLE IF NOT EXISTS text_format (
            owner_id VARCHAR(255) PRIMARY KEY,
            bold BOOLEAN DEFAULT 0,
            italic BOOLEAN DEFAULT 0,
            underline BOOLEAN DEFAULT 0,
            strike BOOLEAN DEFAULT 0,
            code BOOLEAN DEFAULT 0,
            pre BOOLEAN DEFAULT 0
        )
        """
        await execute_query(query, fetch=False)

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^/(bold|italic|underline|strike|code|pre|clearformat)$'))
        async def english_command(event):
            if str(event.sender_id) != self.owner_id:
                return
            format_type = event.pattern_match.group(1)
            response = await self._toggle_format(format_type)
            await event.reply(response)

        @self.client.on(events.NewMessage(pattern=r'^(ضخیم|کج|زیرخط|خط\s*خورده|کد|بلوک\s*کد|پاکسازی\s*قالب)$'))
        async def persian_command(event):
            if str(event.sender_id) != self.owner_id:
                return
            msg = event.pattern_match.group(1).strip()
            cmd_map = {
                'ضخیم': 'bold', 'کج': 'italic', 'زیرخط': 'underline',
                'خط خورده': 'strike', 'کد': 'code', 'بلوک کد': 'pre',
                'پاکسازی قالب': 'clearformat'
            }
            format_type = cmd_map.get(msg)
            if format_type:
                response = await self._toggle_format(format_type)
                await event.reply(response)

        @self.client.on(events.NewMessage())
        async def format_handler(event):
            if str(event.sender_id) != self.owner_id or not event.message.text or event.message.media:
                return
            settings = await get_text_format(self.owner_id)
            if not any(settings.values()):
                return
            updated = self._apply_formats(event.message.text, settings)
            if updated != event.message.text:
                await self._safe_edit(event, updated)

    async def _toggle_format(self, format_type):
        if format_type == 'clearformat':
            for f in ['bold', 'italic', 'underline', 'strike', 'code', 'pre']:
                await update_text_format(self.owner_id, f, False)
            return "✅ همه فرمت‌ها حذف شدند"
        settings = await get_text_format(self.owner_id)
        new_value = not settings.get(format_type, False)
        success = await update_text_format(self.owner_id, format_type, new_value)
        if not success:
            return "❌ خطا در ذخیره تنظیمات"
        return f"🔁 حالت **{format_type}** {'فعال شد ✅' if new_value else 'غیرفعال شد ❌'}"

    def _apply_formats(self, text, settings):
        wrappers = []
        if settings.get('bold'): wrappers.append(('**', '**'))
        if settings.get('italic'): wrappers.append(('_', '_'))
        if settings.get('underline'): wrappers.append(('__', '__'))
        if settings.get('strike'): wrappers.append(('~~', '~~'))
        if settings.get('code'): wrappers.append(('`', '`'))
        if settings.get('pre'): wrappers.append(('```', '```'))
        for w in reversed(wrappers):
            text = f"{w[0]}{text}{w[1]}"
        return text

    async def _safe_edit(self, event, new_text):
        try:
            await event.client.edit_message(
                event.chat_id,
                event.message.id,
                new_text,
                parse_mode='markdown'
            )
        except (FloodWaitError, MessageNotModifiedError):
            pass
        except ChatAdminRequiredError:
            logger.warning("⛔️ دسترسی به ویرایش پیام ندارم")
        except Exception as e:
            logger.error(f"❌ ویرایش پیام ناموفق: {e}")
