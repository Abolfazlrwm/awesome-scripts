# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
from telethon.tl.types import DocumentAttributeFilename
from io import BytesIO
import requests
import random

# Async DB access
from .db_utils import execute_query

HELP = """  
🖼️ **حذف حرفه‌ای پس‌زمینه تصاویر (نسخه API)** 🖼️  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• حذف خودکار پس‌زمینه از **عکس‌ها و فایل‌های تصویری**  
• استفاده از سرویس حرفه‌ای [Remove.bg](https://www.remove.bg)  
• پشتیبانی از فرمت‌های مختلف (JPEG, PNG, WEBP و...)  
• خروجی با کیفیت بالا در قالب PNG با پس‌زمینه شفاف  
• فقط برای مالک اصلی بات (با احراز در دیتابیس)  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات قابل استفاده**:
`/removebg` یا `حذف پس‌زمینه` ➤ (با ریپلای روی عکس) حذف پس‌زمینه تصویر

▬▬▬▬▬▬▬▬▬▬▬▬  
⚠️ **محدودیت‌ها**:  
• فقط برای owner بات و در صورت احراز VIP بودن  
• حداکثر سایز تصویر: ۱۰ مگابایت  
"""

API_KEYS = [
    "qSN5pDQLEqXnD7TP7x7k7T6i",
    "H8cATYSK66cei7YvQLkopQmN",
    "QW1PFtkKnSSBLjkTgDWXCi1h"
]

class RemoveBgPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/removebg|حذف پس‌زمینه)$'))
        async def removebg_handler(event):
            sender_id = str(event.sender_id)

            # مرحله اول: فقط owner اجازه دارد
            if sender_id != self.owner_id:
                return

            # مرحله دوم: حتی owner هم باید مجاز باشد (در دیتابیس)
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 دسترسی شما به این قابلیت فعال نیست.\n"
                    "برای فعال‌سازی Remove.bg لطفاً از طریق ربات زیر اقدام کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            replied = await event.get_reply_message()
            if not replied:
                await event.reply("❌ لطفاً روی یک عکس ریپلای کنید")
                return
            
            if not replied.photo and not (replied.document and replied.document.mime_type.startswith('image')):
                await event.reply("❌ پیام ریپلای شده شامل عکس معتبر نیست")
                return

            processing_msg = await event.reply("⏳ در حال ارسال تصویر به Remove.bg ...")

            try:
                image_bytes = await replied.download_media(bytes)

                if len(image_bytes) > 10 * 1024 * 1024:
                    await event.reply("❌ حجم تصویر بیشتر از ۱۰ مگابایت است.")
                    await processing_msg.delete()
                    return

                api_key = random.choice(API_KEYS)

                response = requests.post(
                    "https://api.remove.bg/v1.0/removebg",
                    files={"image_file": ("image.png", image_bytes)},
                    data={"size": "auto"},
                    headers={"X-Api-Key": api_key}
                )

                if response.status_code == 200:
                    output_buffer = BytesIO(response.content)
                    output_buffer.seek(0)

                    await self.client.send_file(
                        event.chat_id,
                        output_buffer,
                        filename='no_bg.png',
                        attributes=[DocumentAttributeFilename(file_name='no_bg.png')],
                        reply_to=event.message.id,
                        force_document=True
                    )
                else:
                    await event.reply(f"❌ خطا از سمت API: {response.status_code}\n{response.text}")
            except Exception as e:
                await event.reply(f"❌ خطا در پردازش تصویر: {str(e)}")
            finally:
                await processing_msg.delete()

    async def _is_authorized(self, user_id: str) -> bool:
        try:
            result = await execute_query(
                "SELECT 1 FROM authorized_users WHERE user_id = %s",
                (user_id,),
                fetch=True
            )
            return bool(result)
        except Exception as e:
            print(f"❌ Authorization check failed: {e}")
            return False
