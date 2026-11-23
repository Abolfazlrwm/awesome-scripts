# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/markreadpv.py
from .base import Plugin
from telethon import events
import logging
from db import get_markread_pv, set_markread_pv

logger = logging.getLogger(__name__)
HELP = """
👤 **مدیریت خواندن پیام‌های خصوصی** 👤

این دستور به شما امکان می‌دهد پیام‌های خصوصی (پی‌وی) را به صورت خودکار به عنوان خوانده شده علامت بزنید.

شما می‌توانید از یکی از دو فرمان زیر استفاده کنید:

    /markreadpv

    علامت‌گذاری پیوی

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت اصلی**: 
• علامت زدن خودکار پیام‌های پیوی به عنوان خوانده شده

🎯 **دستورات**: 
• `/markreadpv` یا `علامت‌گذاری پیوی` ➔ فعال/غیرفعال کردن

✨ **نمونه**: 
در چت خصوصی با ربات:
    /markreadpv

    یا

    علامت‌گذاری پیوی
"""

class MarkReadPVPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        logger.info(f"پلاگین خواندن پیام خصوصی برای مالک: {self.owner_id} راه‌اندازی شد")

    async def initialize(self, me):
        self.mark_read_pv = await get_markread_pv(self.owner_id)

    async def handle_events(self):
        @self.client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
        async def mark_read_handler(event):
            if self.mark_read_pv:
                await event.message.mark_read()

        @self.client.on(events.NewMessage(pattern=r'^(?:/markreadpv|علامت‌گذاری\s+پیوی)$'))
        async def toggle_mark_read_pv(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            self.mark_read_pv = not self.mark_read_pv
            await set_markread_pv(self.owner_id, self.mark_read_pv)
            status = "فعال" if self.mark_read_pv else "غیرفعال"
            await event.reply(f"👤 علامت‌گذاری خواندن پیام خصوصی: {status}")
