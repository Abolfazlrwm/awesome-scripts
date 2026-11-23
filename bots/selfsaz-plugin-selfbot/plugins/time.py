# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import pytz
from datetime import datetime
HELP = """  
⏰ **نمایش زمان تهران** ⏰  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• نمایش خودکار زمان جاری تهران  
• پشتیبانی از منطقه زمانی Asia/Tehran  
• فرمت‌دهی استاندارد ساعت و دقیقه  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:  
• `/time` ➔ دریافت زمان دقیق تهران  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  
1. ارسال دستور ساده در چت:  
   `/time`  
2. دریافت پیام با زمان به روز شده:  
   ⏰ Current Time: 14:30  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ فرمت خروجی: ساعت ۲۴ ساعته (HH:MM)  
▫️ به‌روزرسانی: زمان واقعی در لحظه درخواست  
▫️ کتابخانه زمانی: pytz  

⚠️ **هشدارهای مهم**:  
- فقط برای کاربر مالک ربات فعال است  
- نیاز به تنظیم صحیح منطقه زمانی در سرور  
- در صورت خطا در دریافت زمان، پیام خطا نمایش داده می‌شود  
"""  
class TimePlugin(Plugin):
    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^/timdasddr3r3qre$'))
        async def handler(event):
            if event.sender_id == self.me.id:
                try:
                    tz = pytz.timezone(self.config['TEHRAN_TIMEZONE'])
                    time_str = datetime.now(tz).strftime('%H:%M')
                    await event.reply(f"⏰ Current Time: {time_str}")
                except Exception as e:
                    logger.error(f"/time error: {str(e)}")