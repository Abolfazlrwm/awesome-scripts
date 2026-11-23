# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import os
import re
import asyncio
from pathlib import Path
from telethon import events
from FastTelethonhelper import fast_download, fast_upload
from .base import Plugin

logger = logging.getLogger(__name__)

HELP = """  
📝 **تغییر نام فایل‌های ارسالی** 📝  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• تغییر نام فایل‌ها بدون تغییر فرمت و محتوا  
• پشتیبانی از تمامی انواع فایل (اسناد، ویدیو، آهنگ و...)  
• بررسی خودکار نام‌های غیرمجاز (۲ تا ۶۴ کاراکتر)  
• پاکسازی خودکار فایل‌های موقت پس از انجام عملیات  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:

**انگلیسی:**  
  `/rename [نام جدید]` ➤ (با ریپلای) تغییر نام فایل  

**فارسی:**  
  `تغییر نام [نام جدید]` ➤ (با ریپلای) تغییر نام فایل  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. ریپلای روی فایل مورد نظر  
2. ارسال دستور: `/rename filename.pdf` یا `تغییر نام filename.pdf`  
3. دریافت فایل با نام جدید در کمتر از ۱۰ ثانیه!  

⚠️ **محدودیت‌ها**:  
- فقط حروف انگلیسی، اعداد و خط تیره مجازند  
- نام فایل نباید با نقطه شروع شود  
- حداکثر طول نام: ۶۴ کاراکتر  
- تغییر نام فایل‌های حذف شده ممکن نیست  
"""

class FileRenamePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.temp_dir = Path("./temp_rename/")
        self.temp_dir.mkdir(exist_ok=True)
        logger.info(f"FileRenamePlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.me = me
        logger.debug("[Finance] Initialization complete")
        
        @self.client.on(events.NewMessage(pattern=r'^(?:/rename|تغییر\s+نام)\s+(.+)$'))
        async def handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            temp_path = None
            renamed_path = None

            try:
                if not event.is_reply:
                    await event.reply("❌ لطفاً به یک پیام حاوی فایل پاسخ دهید.")
                    return

                original_msg = await event.get_reply_message()
                new_name = event.pattern_match.group(1).strip()

                if not self.validate_filename(new_name):
                    await event.reply("❌ نام فایل نامعتبر است (2-64 کاراکتر الفبایی-عددی).")
                    return

                if not original_msg.file:
                    await event.reply("❌ پیام پاسخ‌داده‌شده حاوی فایل نیست.")
                    return

                temp_path = await fast_download(
                    client=self.client,
                    msg=original_msg,
                    download_folder=str(self.temp_dir),
                )

                if not temp_path or not os.path.exists(temp_path):
                    await event.reply("❌ دانلود فایل ناموفق بود.")
                    return

                renamed_path = self.temp_dir / new_name
                os.rename(str(temp_path), str(renamed_path))

                file_obj = await fast_upload(
                    client=self.client,
                    file_location=str(renamed_path),
                    name=new_name,
                )

                await self.client.send_message(
                    entity=event.chat_id,
                    file=file_obj,
                    message=f"✅ تغییر نام داده شد: {new_name}"
                )

            except Exception as e:
                logger.error(f"خطا: {str(e)}")
                await event.reply(f"❌ خطا: {str(e)}")
            finally:
                for path in [temp_path, renamed_path]:
                    if path and os.path.exists(path):
                        try:
                            os.remove(str(path))
                        except Exception as e:
                            logger.error(f"خطای پاکسازی: {str(e)}")

    def validate_filename(self, name):
        return re.match(r'^[\w\-.]{2,64}$', name) and not name.startswith('.')
