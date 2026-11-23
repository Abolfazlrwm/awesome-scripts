# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# id.py
import logging
from .base import Plugin
from telethon import events, functions, utils
from telethon.tl import types
from telethon.errors import ChatAdminRequiredError
from datetime import datetime

logger = logging.getLogger(__name__)

HELP = """  
🆔 **استخراج اطلاعات کامل کاربر/گروه/کانال** 🆔  

این دستور برای دریافت اطلاعات جامع موجودیت (کاربر، گروه یا کانال) طراحی شده است.

شما می‌توانید از یکی از دو فرمان زیر استفاده کنید:

    /id

    شناسه

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های کلیدی**:  
• نمایش **شناسه منحصر به فرد** (ID) و هش دسترسی  
• بررسی وضعیت امنیتی (حساب تاییدشده، کلاهبرداری، جعلی)  
• استخراج عکس پروفایل و اطلاعات تماس  
• نمایش تاریخ ایجاد حساب/گروه  
• دریافت متادیتای پیشرفته (تعداد اعضا، DC ID، بیوگرافی)  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. ریپلای روی پیام کاربر یا ارسال در گروه:  
   /id   یا   شناسه  
2. دریافت اطلاعات کامل شامل:  
   - ID • هش • نام کاربری • وضعیت امنیتی  
   - تاریخ ایجاد • DC ID • تعداد اعضا (برای کانال)  

⚠️ **نکات مهم**:  
- برای دریافت اطلاعات کانال‌ها، ربات باید ادمین باشد  
- اطلاعات تماس فقط برای کاربران قابل مشاهده است  
- اطلاعات حساس مانند هش دسترسی فقط به مالک نمایش داده می‌شود  
"""

class IdPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        logger.info(f"پلاگین ID برای مالک: {self.owner_id} راه‌اندازی شد")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/id|شناسه)$'))
        async def handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            try:
                # تعیین موجودیت هدف
                entity = await self.get_target_entity(event)
                if not entity:
                    return

                # دریافت عکس پروفایل و اطلاعات
                photo, caption = await self.get_entity_data(event, entity)
                
                # ارسال پیام با عکس و کپشن
                if photo:
                    await event.reply(
                        caption,
                        file=photo,
                        parse_mode='md'
                    )
                else:
                    await event.reply(caption, parse_mode='md')
                
                await event.delete()

            except Exception as e:
                logger.exception("خطای فرمان ID:", exc_info=e)

    async def get_target_entity(self, event):
        """دریافت موجودیت هدف از پاسخ یا چت فعلی"""
        if event.is_reply:
            reply = await event.get_reply_message()
            return await reply.get_sender()
        return await event.get_chat()

    async def get_entity_data(self, event, entity):
        """دریافت عکس و کپشن فرمت شده برای موجودیت"""
        # دریافت عکس پروفایل
        try:
            photos = await event.client.get_profile_photos(entity, limit=1)
            photo = photos[0] if photos else None
        except Exception as e:
            logger.debug(f"خطای عکس: {str(e)}")
            photo = None

        # ساخت کپشن
        caption = "**🔍 تحلیل موجودیت**\n\n"
        caption += self.get_basic_info(entity)
        caption += self.get_security_flags(entity)
        caption += await self.get_advanced_info(event, entity)
        caption += f"\n🕰 تاریخ ایجاد: `{self.format_date(getattr(entity, 'date', None))}`"

        return photo, caption

    def get_basic_info(self, entity):
        """تولید بخش اطلاعات پایه"""
        info = "**..🆔 هویت اصلی**\n"
        info += f"→ ID: `{entity.id}`\n"
        info += f"→ Hash: `{getattr(entity, 'access_hash', 'N/A')}`\n"
        
        if isinstance(entity, types.User):
            info += f"→ نام: {entity.first_name or ''} {entity.last_name or ''}\n"
            info += f"→ نام کاربری: @{entity.username or 'N/A'}\n"
            info += f"→ تلفن: `{entity.phone or 'مخفی'}`\n"
        else:
            info += f"→ عنوان: {utils.get_display_name(entity)}\n"
            info += f"→ نوع: `{type(entity).__name__}`\n"
        
        return info + "\n"

    def get_security_flags(self, entity):
        """تولید بخش وضعیت امنیتی"""
        flags = "**🛡 وضعیت امنیتی**\n"
        flags += f"✅ تایید شده: {getattr(entity, 'verified', False)}\n"
        flags += f"🚫 محدود شده: {getattr(entity, 'restricted', False)}\n"
        flags += f"⚠️ کلاهبرداری: {getattr(entity, 'scam', False)}\n"
        flags += f"👻 جعلی: {getattr(entity, 'fake', False)}\n"
        return flags + "\n"

    async def get_advanced_info(self, event, entity):
        """تولید بخش متادیتای پیشرفته"""
        info = "**⚙️ متادیتای پیشرفته**\n"
        try:
            if isinstance(entity, types.Channel):
                full = await event.client(functions.channels.GetFullChannelRequest(entity))
                info += f"→ اعضا: `{getattr(full.full_chat, 'participants_count', 'N/A')}`\n"
                info += f"→ چت مرتبط: `{getattr(full.full_chat, 'linked_chat_id', 'N/A')}`\n"
            elif isinstance(entity, types.User):
                full = await event.client(functions.users.GetFullUserRequest(entity))
                info += f"→ بیو: `{full.full_user.about[:50]}...`\n" if full.full_user.about else ""
        except ChatAdminRequiredError:
            info += "→ نیاز به دسترسی ادمین برای اطلاعات کامل\n"
        except Exception as e:
            logger.debug(f"خطای اطلاعات پیشرفته: {str(e)}")
        
        info += f"→ DC ID: `{self.client.session.dc_id}`\n"
        return info

    def format_date(self, dt):
        """فرمت کردن شیء datetime"""
        return dt.strftime("%Y-%m-%d %H:%M") if isinstance(dt, datetime) else "N/A"
