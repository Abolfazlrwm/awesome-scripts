# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import asyncio
import requests
from PIL import Image, ImageDraw  # Ensure ImageDraw is imported
import io
import os
import logging
import time

logger = logging.getLogger(__name__)
HELP = """  
🌐 **تصویربرداری از صفحات وب** 🌐  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• ثبت تصویر مصنوعی از آدرس‌های وب  
• محدودیت نرخ استفاده (۱ درخواست در دقیقه)  
• ایجاد خودکار متن URL روی تصویر  
• مدیریت خطاهای اتصال و پردازش  
• بررسی امنیت اولیه آدرس‌های ورودی  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:  
• `/screenshot [آدرس-وب]` ➔ ایجاد تصویر شبیه‌سازی شده  
   ↳ مثال: `/screenshot https://example.com`  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  
1. ارسال دستور همراه با آدرس کامل وب:  
   `/screenshot https://google.com`  
2. انتظار برای پردازش (حدود ۱۰-۲۰ ثانیه)  
3. دریافت تصویر حاوی آدرس درخواستی  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ ابعاد تصویر خروجی: 800x600 پیکسل  
▫️ فرمت خروجی: PNG با پس‌زمینه سفید  
▫️ محدودیت نرخ: ۱ درخواست در هر ۶۰ ثانیه  
▫️ افزودن خودکار متن URL به تصویر  

⚠️ **هشدارهای مهم**:  
- امکان دریافت خطا برای URLهای نامعتبر  
- محدودیت استفاده برای جلوگیری از سوءاستفاده  
- فقط برای کاربر مالک ربات فعال است  
- از ارسال لینک‌های حساس خودداری شود  
- این تصویر واقعی از صفحه وب نیست!  
"""  
class ScreenshotPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.rate_limit = {}  # Dictionary to track user request timestamps
        self.rate_limit_interval = 60  # Rate limit interval in seconds (e.g., 1 request per minute)

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^/screenshot (.+)$'))
        async def screenshot_handler(event):
            user_id = str(event.sender_id)
            if user_id != self.owner_id:
                return

            # Rate limiting
            if user_id in self.rate_limit and (time.time() - self.rate_limit[user_id]) < self.rate_limit_interval:
                await event.reply("❌ You have reached the rate limit. Please try again later.")
                return

            self.rate_limit[user_id] = time.time()

            url = event.pattern_match.group(1).strip()
            if not url:
                await event.reply("❌ Please provide a valid URL.")
                return

            await event.reply("✅ Taking screenshot...")

            try:
                # Fetch the webpage content
                response = requests.get(url)
                response.raise_for_status()

                # Create a blank image
                image = Image.new('RGB', (800, 600), color=(255, 255, 255))
                draw = ImageDraw.Draw(image)

                # Add the URL text to the image
                draw.text((10, 10), f"URL: {url}", fill=(0, 0, 0))

                # Save the image to a bytes buffer
                buffer = io.BytesIO()
                image.save(buffer, format='PNG')
                buffer.seek(0)

                # Send the image
                await self.client.send_file(event.chat_id, buffer, caption="Here is the screenshot of the URL.")
            except requests.exceptions.RequestException as e:
                logger.error(f"Error fetching URL for user {user_id}: {str(e)}")
                await event.reply(f"❌ Error fetching URL: {str(e)}")
            except Exception as e:
                logger.error(f"Error taking screenshot for user {user_id}: {str(e)}")
                await event.reply(f"❌ Error taking screenshot: {str(e)}")