# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/markreadchannel.py
from .base import Plugin
from telethon import events
import logging
from db import get_markread_channel, set_markread_channel

logger = logging.getLogger(__name__)
HELP = """
📢 **علامت‌گذاری خودکار پیام‌های کانال** 📢

این دستور به شما امکان می‌دهد پیام‌های کانال را به صورت خودکار به عنوان خوانده شده علامت بزنید.

شما می‌توانید از یکی از دو فرمان زیر استفاده کنید:

    /markreadchannel

    علامت‌گذاری کانال

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت اصلی**: 
• علامت زدن پیام‌های کانال به عنوان خوانده شده به صورت خودکار

🎯 **دستورات**: 
• `/markreadchannel` یا `علامت‌گذاری کانال` ➔ فعال/غیرفعال کردن علامت‌گذاری خواندن

✨ **مثال**: 
ارسال دستور در کانال:
    /markreadchannel

    یا

    علامت‌گذاری کانال
"""

class MarkReadChannelPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.mark_read_channel = False
        logger.info(f"ChannelReadPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.mark_read_channel = await get_markread_channel(self.owner_id)

    async def handle_events(self):
        @self.client.on(events.NewMessage(incoming=True, func=lambda e: e.is_channel))
        async def mark_read_handler(event):
            if self.mark_read_channel:
                await event.message.mark_read()

        @self.client.on(events.NewMessage(pattern=r'^/markreadchannel$|^علامت‌گذاری\s+کانال$'))
        async def toggle_mark_read_channel(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            self.mark_read_channel = not self.mark_read_channel
            await set_markread_channel(self.owner_id, self.mark_read_channel)
            status = "فعال شد" if self.mark_read_channel else "غیرفعال شد"
            await event.reply(f"📢 علامت‌گذاری خواندن کانال: {status}")
