# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import os
import asyncio
from telethon import events
from .base import Plugin

logger = logging.getLogger(__name__)
MAX_CHUNK_SIZE = 3900

HELP = """
🧰 مدیریت پلاگین‌های ربات
▫️ عملکرد اصلی: مدیریت و مشاهده پلاگین‌های نصب‌شده در سیستم

▫️ دستورات مرتبط:

  • **English:**
       `/plugins` ➔ دریافت لیست پلاگین‌ها با نام فایل مربوطه
       `/read filename.py` ➔ مشاهده کد منبع پلاگین

  • **فارسی (بدون /):**
       `پلاگین` ➔ دریافت لیست پلاگین‌ها با نام فایل مربوطه
       `خواندن filename.py` ➔ مشاهده کد منبع پلاگین

▫️ راهنمای جامع:
- با دستور `/plugins` یا دستور `پلاگین` لیست پلاگین‌ها با نام فایل مربوطه را دریافت کنید
- از دستور `/read filename.py` یا `خواندن filename.py` برای مشاهده کد منبع پلاگین استفاده نمایید
- حداکثر حجم هر بخش ارسالی: ۳۹۰۰ کاراکتر
- حذف خودکار کامنت‌ها از کدهای ارسالی
مثال: "/read play.py" یا "خواندن play.py" برای مشاهده کد پلاگین پخش صوت
⚠️ توجه: این دستورات فقط برای کاربر مالک ربات قابل دسترسی است
"""

class PluginManagerPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.plugins_dir = "plugins"
        self.user_client = client
        logger.info(f"PluginManager initialized for {self.owner_id}")

    async def initialize(self, me):
        self.me = me

    async def handle_events(self):
        @self.user_client.client.on(events.NewMessage(pattern=r'^(?:/plugins|پلاگین)$'))
        async def plugins_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            plugin_list = self.get_plugin_list()
            
            if not plugin_list:
                await event.reply("❌ هیچ پلاگینی نصب نشده است")
                return

            await self.send_chunked_list(event, plugin_list)

        @self.user_client.client.on(events.NewMessage(pattern=r'^(?:/read|خواندن)\s+([\w\-]+\.py)$'))
        async def read_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            plugin_name = event.pattern_match.group(1)
            await self.handle_read_request(event, plugin_name)

    def get_plugin_list(self):
        return [
            f"{type(p).__name__.replace('Plugin', '')} "
            f"(📄 `{self.get_filename(p)}.py`)"
            for p in self.user_client.plugins
            if isinstance(p, Plugin)
        ]

    def get_filename(self, plugin):
        module_parts = plugin.__class__.__module__.split('.')
        return module_parts[-1]

    async def send_chunked_list(self, event, plugins):
        header = "📦 پلاگین‌های نصب‌شده\n\n"
        current_chunk = header
        
        for idx, plugin in enumerate(plugins, 1):
            line = f"{idx}. {plugin}\n"
            
            if len(current_chunk) + len(line) > MAX_CHUNK_SIZE:
                await event.reply(current_chunk)
                current_chunk = ""
                await asyncio.sleep(0.5)
                
            current_chunk += line

        if current_chunk != header:
            await event.reply(current_chunk + "\n📝 از `/read filename.py` یا `خواندن filename.py` برای مشاهده کد منبع استفاده کنید")

    async def handle_read_request(self, event, plugin_name):
        try:
            if not plugin_name.endswith('.py') or '/' in plugin_name:
                await event.reply("❌ فرمت نام فایل نامعتبر است")
                return

            plugin_path = os.path.join(self.plugins_dir, plugin_name)
            
            if not os.path.exists(plugin_path):
                await event.reply(f"❌ فایل {plugin_name} یافت نشد")
                return

            with open(plugin_path, 'r', encoding='utf-8') as f:
                content = f.read()

            if not content:
                await event.reply(f"📄 فایل {plugin_name} خالی است")
                return

            # Remove comments from the content
            cleaned_content = self.remove_comments(content)

            chunks = [
                cleaned_content[i:i+MAX_CHUNK_SIZE]
                for i in range(0, len(cleaned_content), MAX_CHUNK_SIZE)
            ]

            await event.reply(f"📂 در حال ارسال {plugin_name} ({len(chunks)} بخش)...")
            
            for i, chunk in enumerate(chunks, 1):
                await self.user_client.client.send_message(
                    event.chat_id,
                    f"بخش {i}/{len(chunks)}:\n```python\n{chunk}\n```",
                    parse_mode='markdown'
                )
                await asyncio.sleep(0.7)

        except Exception as e:
            logger.error(f"خطای خواندن: {str(e)}")
            await event.reply(f"❌ خطا در دسترسی به {plugin_name}: {str(e)}")

    def remove_comments(self, content):
        """Remove all comments from the source code."""
        lines = content.splitlines()
        cleaned_lines = []

        for line in lines:
            # Remove inline comments (anything after #)
            line = line.split('#')[0].rstrip()
            if line:  # Keep non-empty lines
                cleaned_lines.append(line)

        return '\n'.join(cleaned_lines)
