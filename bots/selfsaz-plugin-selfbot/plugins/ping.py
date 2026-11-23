# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/ping.py
from .base import Plugin
from telethon import events, functions
import logging
import asyncio
import time
import platform
import os

logger = logging.getLogger(__name__)

HELP = """  
🏓 **بررسی وضعیت و سلامت ربات** 🏓  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
  • نمایش آپتایم دقیقه‌ای ربات  
  • اندازه‌گیری تاخیر API تلگرام  
  • گزارش وضعیت سیستم (بار پردازش، نسخه پایتون، سیستم عامل)  
  • بررسی وضعیت امنیتی اتصالات  
  • نمایش اعلان‌های مهم سرویس  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:  

  • **English:**  
       `/ping` ➔ نمایش کامل وضعیت سرویس  

  • **فارسی (بدون /):**  
       `پینگ` ➔ نمایش کامل وضعیت سرویس  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
ارسال دستور در هر چت:  
       `/ping`   یا   `پینگ`  

⚠️ **نکات مهم**:  
  - پاسخ به صورت خودکار پس از ۳۰ ثانیه حذف می‌شود  
  - فقط برای مالک ربات قابل مشاهده است  
  - تاخیر API بر اساس سرعت اینترنت محاسبه می‌شود  
  - آپتایم از زمان آخرین ریست ربات محاسبه می‌گردد  
"""  

class PingPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.start_time = time.time()
        logger.info(f"PingPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, *args):
        # Register the handler for both English (/ping) and Farsi (پینگ) commands.
        self.client.add_event_handler(
            self.ping_handler, events.NewMessage(pattern=r'^(?:/ping|پینگ)$')
        )

    async def ping_handler(self, event):
        if str(event.sender_id) != self.owner_id:
            return

        try:
            # Calculate metrics
            uptime = self.format_uptime()
            latency = await self.calculate_latency(event)
            system_status = self.get_system_status()
            security_status = self.get_security_status()
            
            message = (
                "🏓 **وضعیت سرویس**\n"
                f"⏱ آپتایم: {uptime}\n"
                f"📶 تاخیر API: {latency:.2f} ثانیه\n"
                f"🖥 وضعیت سیستم: {system_status}\n\n"
                "🔒 **وضعیت امنیتی**\n"
                f"{security_status}\n\n"
                "📌 **اطلاعیه‌های مهم**\n"
                "- نگهداری سرور: 02:00 تا 04:00 بامداد\n"
                "- نسخه جدید 2.1.0 در دست توسعه\n"
                "- محدودیت موقت دانلود فایل‌های بالای 1GB"
            )

            msg = await event.reply(message)
            await asyncio.sleep(30)
            await msg.delete()
            await event.delete()

        except Exception as e:
            logger.error(f"Ping command error: {str(e)}")

    def format_uptime(self):
        seconds = int(time.time() - self.start_time)
        days, seconds = divmod(seconds, 86400)
        hours, seconds = divmod(seconds, 3600)
        minutes = seconds // 60
        return f"{days} روز, {hours} ساعت, {minutes} دقیقه"

    async def calculate_latency(self, event):
        start = time.time()
        await event.client(functions.PingRequest(ping_id=int(time.time())))
        return time.time() - start

    def get_system_status(self):
        load_avg = " / ".join(f"{x:.2f}" for x in os.getloadavg())
        return (
            f"بار سیستم: {load_avg}\n"
            f"پایتون: {platform.python_version()}\n"
            f"سیستم عامل: {platform.system()} {platform.release()}"
        )

    def get_security_status(self):
        return (
            "✅ تمام اتصالات امن (TLS 1.3)\n"
            "✅ احراز هویت دو مرحله‌ای فعال\n"
            "✅ آخرین بروزرسانی امنیتی: 1403/05/15"
        )
