# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/ocr_plugin.py
from .base import Plugin
from telethon import events
from PIL import Image
import pytesseract
import re
import io
import logging

logger = logging.getLogger(__name__)

HELP = """  
🔍 **استخراج متن و اطلاعات از تصاویر** 🔍  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
  • استخراج متن از تصاویر با فناوری OCR  
  • فیلتر اطلاعات خاص (شماره، ایمیل، لینک)  
  • پشتیبانی از فرمت‌های مختلف تصویر  
  • نمایش نتایج در قالب کد برای خوانایی بهتر  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:  

  • **English:**  
       `/ocr` ➔ استخراج تمام متن (با ریپلای روی عکس)  
       `/ocr numbers` ➔ استخراج فقط اعداد  
       `/ocr email` ➔ استخراج آدرس‌های ایمیل  
       `/ocr links` ➔ استخراج لینک‌ها  

  • **فارسی (بدون /):**  
       `استخراج متن` ➔ استخراج تمام متن (با ریپلای روی عکس)  
       `استخراج اعداد` ➔ استخراج فقط اعداد  
       `استخراج ایمیل` ➔ استخراج آدرس‌های ایمیل  
       `استخراج لینک` ➔ استخراج لینک‌ها  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. ریپلای روی یک عکس حاوی متن  
2. ارسال دستور مورد نظر:  
       `/ocr email`   یا   `استخراج ایمیل`  
3. دریافت لیست ایمیل‌های استخراج شده در کمتر از 10 ثانیه!  

⚠️ **محدودیت‌ها و نکات**:  
  - فقط برای مالک ربات قابل اجراست  
  - کیفیت نتایج به وضوح تصویر بستگی دارد  
  - فونت‌های غیراستاندارد ممکن است شناسایی نشوند  
  - حداکثر حجم تصویر: 5MB  
  - از تصاویر با نور کافی و کنتراست مناسب استفاده کنید  
"""  

class OCRPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        logger.info(f"OCRPlugin initialized for owner: {self.owner_id}")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/ocr|استخراج متن)$'))
        async def ocr_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if event.is_reply:
                reply_message = await event.get_reply_message()
                if reply_message.photo:
                    await self.process_ocr(event, reply_message, "all")
                else:
                    await event.reply("❌ Reply to an image.")
            else:
                await event.reply("❌ Reply to an image.")

        @self.client.on(events.NewMessage(pattern=r'^(?:/ocr numbers|استخراج اعداد)$'))
        async def ocr_numbers_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if event.is_reply:
                reply_message = await event.get_reply_message()
                if reply_message.photo:
                    await self.process_ocr(event, reply_message, "numbers")
                else:
                    await event.reply("❌ Reply to an image.")
            else:
                await event.reply("❌ Reply to an image.")

        @self.client.on(events.NewMessage(pattern=r'^(?:/ocr email|استخراج ایمیل)$'))
        async def ocr_email_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if event.is_reply:
                reply_message = await event.get_reply_message()
                if reply_message.photo:
                    await self.process_ocr(event, reply_message, "email")
                else:
                    await event.reply("❌ Reply to an image.")
            else:
                await event.reply("❌ Reply to an image.")

        @self.client.on(events.NewMessage(pattern=r'^(?:/ocr links|استخراج لینک)$'))
        async def ocr_links_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if event.is_reply:
                reply_message = await event.get_reply_message()
                if reply_message.photo:
                    await self.process_ocr(event, reply_message, "links")
                else:
                    await event.reply("❌ Reply to an image.")
            else:
                await event.reply("❌ Reply to an image.")

    async def process_ocr(self, event, reply_message, mode):
        try:
            image_bytes = await reply_message.download_media(bytes)
            image = Image.open(io.BytesIO(image_bytes))
            text = pytesseract.image_to_string(image)

            if mode == "numbers":
                filtered_text = self.extract_numbers(text)
            elif mode == "email":
                filtered_text = self.extract_emails(text)
            elif mode == "links":
                filtered_text = self.extract_links(text)
            else:
                filtered_text = text

            if filtered_text:
                await event.reply(f"🔍 {mode.capitalize()} found:\n```{filtered_text}```")
            else:
                await event.reply(f"❌ No {mode} found")
        except Exception as e:
            logger.error(f"OCR error: {str(e)}")
            await event.reply("❌ Processing failed")

    def extract_numbers(self, text):
        return "\n".join(re.findall(r'\b\d+\b', text))

    def extract_emails(self, text):
        return "\n".join(re.findall(r'[\w\.-]+@[\w\.-]+\.\w+', text))

    def extract_links(self, text):
        return "\n".join(re.findall(r'https?://\S+', text))
