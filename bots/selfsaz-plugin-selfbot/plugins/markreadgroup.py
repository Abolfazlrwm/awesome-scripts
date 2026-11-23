# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/markreadgroup.py
from .base import Plugin
from telethon import events, types, functions
import logging
from db import get_markread_group, set_markread_group

logger = logging.getLogger(__name__)
HELP = """
👥 **علامت‌گذاری خودکار پیام‌های گروه** 👥

این دستور به شما امکان می‌دهد پیام‌های گروه را به صورت خودکار به عنوان خوانده شده علامت بزنید.

شما می‌توانید از یکی از دو فرمان زیر استفاده کنید:

    /markreadgroup

    علامت‌گذاری گروه

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت اصلی**: 
• علامت زدن پیام‌های گروه به عنوان خوانده شده

🎯 **دستورات**: 
• `/markreadgroup` یا `علامت‌گذاری گروه` ➔ تغییر وضعیت علامت‌گذاری خواندن

✨ **مثال**: 
در گروه مورد نظر ارسال کنید:
    /markreadgroup

    یا

    علامت‌گذاری گروه
"""

class MarkReadGroupPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.mark_read_group = False
        logger.info(f"GroupReadPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.mark_read_group = await get_markread_group(self.owner_id)

    async def handle_events(self):
        @self.client.on(events.NewMessage(incoming=True, func=lambda e: e.is_group))
        async def mark_read_handler(event):
            if self.mark_read_group:
                await event.message.mark_read()

        @self.client.on(events.NewMessage(pattern=r'^(?:/markreadgroup|علامت‌گذاری\s+گروه)$'))
        async def toggle_mark_read_group(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            self.mark_read_group = not self.mark_read_group
            await set_markread_group(self.owner_id, self.mark_read_group)
            status = "فعال شد" if self.mark_read_group else "غیرفعال شد"
            await event.reply(f"👥 علامت‌گذاری خواندن گروه: {status}")
