# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/profile_copier.py
import logging
import asyncio
import io
from PIL import Image
import magic
from telethon import events, types
from telethon.tl.functions.photos import UploadProfilePhotoRequest
from .base import Plugin

logger = logging.getLogger(__name__)
HELP = """  
📸 **مدیریت کپی پروفایل کاربران** 📸  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
  • کپی خودکار عکس پروفایل از کاربران/گروه‌ها  
  • پردازش تصویر پیشرفته (تبدیل به JPEG - تغییر سایز)  
  • مدیریت همزمان چندین پروفایل با تاخیر امنیتی  
  • نمایش نتایج عملیات با آمار دقیق  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:  

  • **English:**  
       `/copypic [تعداد]` ➔ کپی پروفایل‌ها (پیش‌فرض: ۵)  
         ↳ مثال: `/copypic 12`  

  • **فارسی (بدون /):**  
       `کپی پروفایل [تعداد]` ➔ کپی پروفایل‌ها (پیش‌فرض: ۵)  
         ↳ مثال: `کپی پروفایل 12`  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ حداکثر تعداد در هر درخواست: ۱۵ پروفایل  
▫️ تاخیر بین عملیات: ۲۰ ثانیه برای جلوگیری از محدودیت  
▫️ فرمت خروجی: JPEG با کیفیت 95%  
▫️ حداقل سایز تصویر: 256x256 پیکسل  

⚠️ **هشدارهای مهم**:  
- نیاز به دسترسی **مدیریت حساب کاربری**  
- فقط برای کاربر مالک ربات فعال است  
- از ریپلای روی ادمین‌ها خودداری شود  
- اطلاعات EXIF تصاویر حذف می‌شود  
"""  

class ProfilePicCopier(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.delay = 20  # Increased safety delay
        self.max_count = 15  # Max profiles per request
        logger.info(f"ProfileCopier initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.me = me
        logger.debug("[ProfileCopier] Initialization complete")

    async def handle_events(self):
        @self.client.on(events.NewMessage(
            pattern=r'^(?:/copypic|کپی\s*پروفایل)(?:\s+(\d+))?$'
        ))
        async def copier_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            try:
                count = int(event.pattern_match.group(1) or 5)
                count = min(count, self.max_count)
                
                users = await self._get_target_users(event)
                if not users:
                    await event.reply("❌ No valid users found in this context")
                    return

                success = 0
                errors = 0
                message = await event.reply(f"🔄 Starting copy process for {count} profiles...")
                
                for user in users[:count]:
                    try:
                        await self._process_user(user)
                        success += 1
                        await asyncio.sleep(self.delay)
                    except Exception as e:
                        errors += 1
                        logger.error(f"Failed {user.id}: {str(e)}")
                        await asyncio.sleep(5)

                await message.edit(
                    f"📊 Results:\n"
                    f"• Success: {success}\n"
                    f"• Failed: {errors}\n"
                    f"• Total: {count}"
                )

            except Exception as e:
                logger.error(f"Handler error: {str(e)}")
                await event.reply("❌ Critical error processing request")

    async def _get_target_users(self, event):
        """Get users based on context with validation"""
        try:
            if event.is_reply:
                reply = await event.get_reply_message()
                user = await self.client.get_entity(reply.sender_id)
                return [user] if user else []
            
            chat = await event.get_chat()
            if isinstance(chat, (types.Chat, types.Channel)):
                return await self.client.get_participants(chat, limit=50)
            
            return []
        except Exception as e:
            logger.error(f"User fetch error: {str(e)}")
            return []

    async def _process_user(self, user):
        """Full image processing pipeline"""
        try:
            # 1. Download original photo
            photo_bytes = await self.client.download_profile_photo(user, file=bytes)
            if not photo_bytes:
                raise ValueError("No profile photo available")

            # 2. Validate MIME type
            mime = magic.from_buffer(photo_bytes, mime=True)
            if mime not in ["image/jpeg", "image/png"]:
                raise ValueError(f"Invalid MIME type: {mime}")

            # 3. Process image
            with Image.open(io.BytesIO(photo_bytes)) as img:
                # Convert to RGB for JPEG compatibility
                if img.mode != 'RGB':
                    img = img.convert('RGB')
                
                # Resize if too small
                if min(img.size) < 256:
                    new_size = (max(img.size), max(img.size))
                    img = img.resize(new_size, Image.Resampling.LANCZOS)
                
                # Convert to JPEG
                output = io.BytesIO()
                img.save(output, format='JPEG', quality=95)
                jpeg_bytes = output.getvalue()

            # 4. Upload with forced .jpg extension
            file = await self.client.upload_file(
                jpeg_bytes,
                file_name="profile.jpg"
            )
            await self.client(UploadProfilePhotoRequest(
                file=file,
                fallback=True,
                video=None
            ))
            logger.info(f"Successfully copied {user.id}")

        except Exception as e:
            logger.error(f"Processing failed: {str(e)}")
            raise
