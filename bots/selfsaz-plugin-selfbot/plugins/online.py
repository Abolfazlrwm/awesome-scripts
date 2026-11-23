# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import asyncio
from telethon import events, functions
from .base import Plugin
from db import set_online_mode, get_online_mode

logger = logging.getLogger(__name__)


HELP = """  
🟢 **حالت آنلاین دائمی برای حساب کاربری** 🟢  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
  • نمایش همیشگی وضعیت آنلاین در تلگرام  
  • شبیه‌سازی فعالیت‌های واقعی (تایپ کردن، خواندن پیام‌ها)  
  • نگهداری اتصال حتی در حالت غیرفعال  
  • ذخیره وضعیت پس از ریست ربات  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:  

  • **English:**  
       `/online` ➔ فعال/غیرفعال کردن حالت آنلاین  

  • **فارسی (بدون /):**  
       `آنلاین` ➔ فعال/غیرفعال کردن حالت آنلاین  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. ارسال دستور در پیوی ربات:  
       `/online`   یا   `آنلاین`  
2. حساب شما به صورت دائمی آنلاین نمایش داده می‌شود!  

⚠️ **نکات مهم**:  
  - این قابلیت ممکن است باعث افزایش مصرف باتری شود  
  - تغییر وضعیت در سرورهای تلگرام ممکن است با تاخیر نمایش داده شود  
  - در حالت فعال، هر ۴۰ ثانیه فعالیت شبیه‌سازی می‌شود  
  - فقط برای مالک ربات قابل اجراست  
"""  

class OnlineModePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.online_task = None
        logger.info(f"پلاگین حالت آنلاین برای مالک: {self.owner_id} راه‌اندازی شد")

    async def initialize(self, me):
        self.me = me
        state = await get_online_mode(self.owner_id)
        if state['enabled']:
            await self.start_online_mode()
        logger.debug("[حالت آنلاین] راه‌اندازی کامل شد")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/online|آنلاین)$'))
        async def online_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            state = await get_online_mode(self.owner_id)
            if state['enabled']:
                await self.stop_online_mode()
                await event.reply("🔴 حالت آنلاین غیرفعال شد")
            else:
                await self.start_online_mode()
                await event.reply("🟢 حالت آنلاین فعال شد")

    async def start_online_mode(self):
        if self.online_task and not self.online_task.done():
            return

        await set_online_mode(self.owner_id, True)
        self.online_task = asyncio.create_task(self._online_keepalive())
        logger.info("حالت آنلاین فعال شد")

    async def stop_online_mode(self):
        if self.online_task:
            self.online_task.cancel()
            try:
                await self.online_task
            except asyncio.CancelledError:
                pass
        await set_online_mode(self.owner_id, False)
        logger.info("حالت آنلاین غیرفعال شد")

    async def _online_keepalive(self):
        while True:
            try:
                await self.client(functions.account.UpdateStatusRequest(offline=False))
                
                async with self.client.action(self.me.id, 'typing'):
                    await asyncio.sleep(2)
                
                await self.client(functions.updates.GetStateRequest())
                
                await self.client(functions.messages.ReadHistoryRequest(
                    peer=self.me.id,
                    max_id=0
                ))

                await set_online_mode(self.owner_id, True)

                await asyncio.sleep(40 + (10 * (hash(self.me.id) % 3)))

            except asyncio.CancelledError:
                break
            except Exception as e:
                logger.error(f"خطا در نگهداری حالت آنلاین: {str(e)}")
                await asyncio.sleep(15)

    async def _safe_disconnect(self):
        await self.stop_online_mode()
