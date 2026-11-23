# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/panel.py
from .base import Plugin
from telethon import events
from telethon.errors import FloodWaitError
import logging
import asyncio

logger = logging.getLogger(__name__)
HELP = """  
🎛 **پنل کنترل پیشرفته ربات** 🎛  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
  • دسترسی سریع به تمام تنظیمات ربات از طریق یک رابط اینلاین  
  • مدیریت تمامی ماژول‌ها در یک محیط یکپارچه  
  • نمایش آمار و اطلاعات حیاتی ربات  
  • تعامل با بات کمکی برای عملکرد پیشرفته  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:  

  • **English:**  
       `/panel` ➔ نمایش پنل کنترل اصلی  

  • **فارسی (بدون /):**  
       `پنل` ➔ نمایش پنل کنترل اصلی  

▬▬▬▬▌▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. ارسال دستور در چت مورد نظر:  
       `/panel`   یا   `پنل`  
2. انتخاب گزینه‌های دلخواه از منوی اینلاین  

⚠️ **نکات مهم**:  
  - نیاز به فعال بودن بات کمکی (`panel_helperbot`)  
  - ممکن است با محدودیت نرخ ارسال پیام مواجه شوید  
  - فقط برای مالک ربات قابل مشاهده و استفاده است  
  - رابط اینلاین پس از ۳۰ ثانیه به صورت خودکار حذف می‌شود  
"""  

class PanelPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        logger.info(f"PanelPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.me = me

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/panel|پنل)$'))
        async def panel_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            try:
                # Delete the original command first
                await event.delete()

                # Perform inline query to the helper bot
                inline_results = await event.client.inline_query(
                    'panel_helperbot',
                    '_help'
                )

                if not inline_results:
                    raise ValueError("No results found from panel helper bot")

                # Send the first inline result to the chat
                await inline_results[0].click(event.chat_id, reply_to=event.reply_to_msg_id)
                
                # Optional: Delete the result after some time
                # await asyncio.sleep(30)
                # await event.client.delete_messages(event.chat_id, sent_msg)

            except FloodWaitError as e:
                msg = await event.respond(f"⚠️ Please wait {e.seconds} seconds before trying again.")
                await asyncio.sleep(5)
                await msg.delete()
            except Exception as e:
                error_msg = await event.respond(f"❌ Failed to get panel: {str(e)}")
                await asyncio.sleep(10)
                await error_msg.delete()
                logger.error(f"Panel error: {str(e)}")
