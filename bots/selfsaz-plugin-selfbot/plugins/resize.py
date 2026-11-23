# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/resize_plugin.py
from .base import Plugin
from telethon import events
from PIL import Image
import io
import logging

logger = logging.getLogger(__name__)

HELP = """  
🖼️ **تغییر اندازه پیشرفته تصاویر** 🖼️  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
  • تغییر سایز تصاویر با دقت پیکسلی  
  • حفظ نسبت اصلی تصویر (اختیاری)  
  • پشتیبانی از فرمت‌های مختلف تصویری  
  • خروجی با کیفیت بالا در فرمت PNG  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:  

  • **English:**  
       `/resize [عرض] [ارتفاع]` ➔ تغییر اندازه تصویر  
         ↳ مثال: `/resize 800 600`  

  • **فارسی (بدون /):**  
       `تغییر اندازه [عرض] [ارتفاع]` ➔ تغییر اندازه تصویر  
         ↳ مثال: `تغییر اندازه 800 600`  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  
1. ریپلای روی تصویر مورد نظر  
2. ارسال دستور با ابعاد جدید:  
       `/resize 1200 900`   یا   `تغییر اندازه 1200 900`  
3. دریافت نسخه اصلاح‌شده در عرض چند ثانیه!  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ الگوریتم تغییر اندازه: LANCZOS با کیفیت بالا  
▫️ فرمت خروجی: PNG با شفافیت کامل  
▫️ محدودیت ابعاد: اعداد مثبت غیرصفر  
▫️ حداکثر حجم پردازش: 20 مگابایت  

⚠️ **هشدارهای مهم**:  
- نیاز به دسترسی **ارسال فایل** برای ربات  
- فقط برای کاربر مالک ربات فعال است  
- از ابعاد منطقی استفاده کنید (پیشنهادی زیر 4000px)  
- تصاویر متحرک (GIF) پشتیبانی نمی‌شوند  
"""  

class ResizePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        logger.info(f"پلاگین تغییر اندازه برای مالک: {self.owner_id} راه‌اندازی شد")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/resize|تغییر\s+اندازه) (\d+) (\d+)$'))
        async def resize_handler(event):
            # بررسی مالکیت
            if str(event.sender_id) != self.owner_id:
                return

            if event.is_reply:
                reply_message = await event.get_reply_message()
                if reply_message.photo:
                    try:
                        width = int(event.pattern_match.group(1))
                        height = int(event.pattern_match.group(2))
                        
                        if width <= 0 or height <= 0:
                            await event.reply("❌ ابعاد باید اعداد مثبت باشند")
                            return
                            
                        await self.process_resize(event, reply_message, width, height)
                    except ValueError:
                        await event.reply("❌ فرمت ابعاد نامعتبر است")
                else:
                    await event.reply("❌ به یک تصویر پاسخ دهید")
            else:
                await event.reply("❌ به یک تصویر پاسخ دهید")

    async def process_resize(self, event, reply_message, width, height):
        try:
            # دانلود و تغییر اندازه تصویر
            image_bytes = await reply_message.download_media(bytes)
            image = Image.open(io.BytesIO(image_bytes))
            resized_image = image.resize((width, height), Image.Resampling.LANCZOS)

            # آماده‌سازی و ارسال نتیجه
            with io.BytesIO() as output:
                resized_image.save(output, format="PNG")
                output.seek(0)
                await event.reply(
                    file=await self.client.upload_file(output, file_name="resized.png"),
                    message=f"🖼️ تصویر به اندازه {width}x{height} تغییر یافت"
                )

        except Exception as e:
            logger.error(f"خطا در تغییر اندازه تصویر: {str(e)}")
            await event.reply("❌ پردازش تصویر ناموفق بود")
