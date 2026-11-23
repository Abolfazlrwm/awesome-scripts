# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, functions
from telethon.errors import ChatAdminRequiredError, UserAdminInvalidError
from telethon.tl.types import Channel, Chat, PeerUser
import logging

logger = logging.getLogger(__name__)

HELP = """  
🔓 **مدیریت آزادسازی کاربران مسدودشده** 🔓  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• آزادسازی کاربران مسدودشده در گروه/کانال  
• شناسایی کاربر از طریق ریپلای یا نام کاربری/آیدی  
• بررسی مجوزهای ادمین و دسترسی ربات  
• مدیریت خطاهای مربوط به دسترسی و شناسه نامعتبر  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:  

• **انگلیسی:**  
   `/unban [یوزرنیم/آیدی]` ➔ آزادسازی کاربر  

• **فارسی:**  
   `آزادسازی    [یوزرنیم/آیدی]` ➔ آزادسازی کاربر  
   (بدون استفاده از کاراکتر `/` و با فاصله‌های اضافی برای تاکید)  

   ↳ مثال‌ها:  
   `/unban @username`  
   یا  
   `آزادسازی    @username`  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  

1. **روش اول - ریپلای روی پیام کاربر مسدودشده:**  
   `/unban`  
   یا  
   `آزادسازی`  

2. **روش دوم - ارسال مستقیم آیدی یا یوزرنیم:**  
   `/unban 123456789`  
   یا  
   `آزادسازی    123456789`  

3. دریافت تأییدیه آزادسازی در صورت موفقیت  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ نیاز به دسترسی **ادمین** با مجوز Ban Users  
▫️ پشتیبانی از گروه‌ها و سوپرگروه‌ها  
▫️ بررسی دو مرحله‌ای مجوزهای کاربر و ربات  
▫️ سیستم تشخیص هویت پیشرفته کاربران  

⚠️ **هشدارهای مهم**:  
- فقط در گروه‌ها و کانال‌ها قابل اجراست  
- نیاز به دسترسی سطح ادمین برای کاربر فراخواننده  
- فقط برای کاربر مالک ربات فعال است  
- در صورت عدم دسترسی ربات، عملیات لغو می‌شود  
- از آیدی/یوزرنیم معتبر استفاده کنید  
"""  

class UnbanPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        logger.info(f"UnbanPlugin initialized for owner: {self.owner_id}")

    async def process_unban(self, event):
        # Silent owner check
        if str(event.sender_id) != self.owner_id:
            return

        try:
            chat = await event.get_chat()
            if not isinstance(chat, (Channel, Chat)):
                await event.reply("❌ This command only works in groups!")
                return

            sender = await event.get_sender()
            permissions = await self.client.get_permissions(chat, sender)
            
            if not permissions.is_admin or not permissions.ban_users:
                await event.reply("❌ You need admin privileges with ban rights!")
                return

            # Target resolution logic
            if event.is_reply:
                reply_msg = await event.get_reply_message()
                target_user = await reply_msg.get_sender()
            else:
                args = event.message.text.split()
                if len(args) < 2:
                    if event.message.text.startswith("آزادسازی"):
                        usage_msg = "❌ استفاده: آزادسازی    <یوزرنیم یا آیدی> OR ریپلای به یک پیام"
                    else:
                        usage_msg = "❌ Usage: /unban <username or user ID> OR reply to a message"
                    await event.reply(usage_msg)
                    return

                target = args[1].strip()
                try:
                    target_user = await self.client.get_entity(
                        PeerUser(int(target)) if target.isdigit() else target
                    )
                except Exception as e:
                    await event.reply(f"❌ Could not find user: {str(e)}")
                    return

            # Unban execution
            await self.client.edit_permissions(
                entity=chat.id,
                user=target_user.id,
                view_messages=True
            )
            await event.reply(f"✅ Unbanned user {target_user.first_name} ({target_user.id})")

        except (ChatAdminRequiredError, UserAdminInvalidError):
            await event.reply("❌ I don't have permission to unban users here!")
        except Exception as e:
            await event.reply(f"❌ Error: {str(e)}")
            logger.exception("Unban command error:")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern='/unban'))
        async def english_handler(event):
            await self.process_unban(event)

        @self.client.on(events.NewMessage(pattern='^آزادسازی'))
        async def farsi_handler(event):
            await self.process_unban(event)
