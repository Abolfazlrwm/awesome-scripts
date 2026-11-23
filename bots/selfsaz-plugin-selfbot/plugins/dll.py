# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import logging
import os

logger = logging.getLogger(__name__)

HELP = """  
📥 **دانلود و ارسال رسانه‌ها** 📥  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌ها**:  
• دانلود رسانه (عکس، ویدیو، فایل) از پیام ریپلای شده  
• کارکرد حتی اگر ذخیره یا فوروارد غیرفعال باشد  
• ارسال خودکار به پیام‌های ذخیره شده  
• حذف فایل از سیستم پس از ارسال  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:

**انگلیسی:**
  `/download` (با ریپلای روی پیام حاوی رسانه)  

**فارسی:**
  `دانلود رسانه` (با ریپلای روی پیام حاوی رسانه)  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. ریپلای روی پیام حاوی رسانه  
2. ارسال دستور: `/download` یا `دانلود رسانه`  

⚠️ **نکات**:  
- فقط برای پیام‌هایی با رسانه قابل اجراست  
- در صورت خطا، پیام مناسب نمایش داده می‌شود  
"""

class DownloadMediaPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.download_dir = config.get("download_dir", "downloads")  # Default folder: 'downloads'
        os.makedirs(self.download_dir, exist_ok=True)  # Create folder if it doesn't exist
        logger.info(f"DownloadMediaPlugin initialized for owner: {self.owner_id}")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/download|دانلود رسانه)(?:@\w+)?$'))
        async def handler(event):
            # Silent owner check - only owner can use this
            if str(event.sender_id) != self.owner_id:
                return

            # Ensure the command is a reply
            if not event.is_reply:
                await event.reply("❌ لطفاً روی پیامی که رسانه دارد ریپلای کنید!")
                return

            try:
                # Get the replied message
                reply_msg = await event.get_reply_message()
                if not reply_msg.media:
                    await event.reply("❌ این پیام رسانه‌ای ندارد!")
                    return

                # Handle different media types
                media = reply_msg.media
                file_name = None
                file_id = None
                
                # Check media type correctly
                if hasattr(media, 'document'):
                    document = media.document
                    file_id = document.id
                    # Get file name from attributes or use a default
                    for attr in document.attributes:
                        if hasattr(attr, "file_name") and attr.file_name:
                            file_name = attr.file_name
                            break
                    if not file_name:
                        file_name = f"document_{file_id}.bin"
                elif hasattr(media, 'photo'):
                    photo = media.photo
                    file_id = photo.id
                    file_name = f"photo_{file_id}.jpg"
                else:
                    await event.reply("❌ نوع رسانه پشتیبانی نمی‌شود!")
                    return

                # Construct the full file path
                file_path = os.path.join(self.download_dir, file_name)

                # Download the media
                await event.reply(f"📥 در حال دانلود و ارسال {file_name}...")
                await self.client.download_media(
                    message=reply_msg,
                    file=file_path
                )

                # Forward the file to Saved Messages
                me = await self.client.get_me()
                
                # Send the file to Saved Messages
                await self.client.send_file(
                    entity="me",  # "me" refers to Saved Messages
                    file=file_path,
                    caption=f"📥 دانلود شده از چت: {getattr(event.chat, 'title', 'Private Chat')}"
                )
                
                # Delete the file after forwarding
                try:
                    os.remove(file_path)
                    file_status = "و از سیستم حذف شد"
                except Exception as e:
                    logger.error(f"Error deleting file {file_path}: {str(e)}")
                    file_status = "اما حذف فایل از سیستم با خطا مواجه شد"

                await event.reply(f"✅ فایل با موفقیت به پیام‌های ذخیره شده ارسال شد {file_status}")

            except Exception as e:
                await event.reply(f"❌ خطا در دانلود یا ارسال: {str(e)}")
                logger.exception("Download media error:")
