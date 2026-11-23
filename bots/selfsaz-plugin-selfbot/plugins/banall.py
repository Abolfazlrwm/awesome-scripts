# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/banall.py

from .base import Plugin
from telethon import events, functions
from telethon.tl import types
from telethon.errors import ChatAdminRequiredError, UserAdminInvalidError, FloodWaitError
import asyncio
import logging
from db import save_ban_progress, get_ban_progress, delete_ban_progress

logger = logging.getLogger(__name__)


HELP = """  
💥 **مسدودسازی گروهی کاربران** 💥  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های کلیدی**:  
• مسدود کردن **تمام اعضای گروه** به صورت یکجا  
• پردازش هوشمند با **باتچ‌های ۱۰ تایی** برای جلوگیری از محدودیت  
• ذخیره خودکار پیشرفت عملیات (قابلیت ادامه پس از قطعی)  
• مدیریت خطاهای FloodWait و محدودیت‌های تلگرام  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:

**انگلیسی:**
  `/banall` ➤ شروع فرآیند مسدودسازی گروهی  

**فارسی:**
  `بن همه` ➤ شروع فرآیند مسدودسازی گروهی  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه اجرا**:  
1. ارسال دستور در گروه:  
   `/banall` یا `بن همه`  
2. سیستم به صورت خودکار تمام کاربران غیرادمین را مسدود می‌کند!  

⚠️ **شرایط ضروری**:  
- فقط در **سوپرگروه‌ها** قابل اجراست  
- نیاز به دسترسی **Ban Users** برای ربات  
- کاربران با دسترسی ادمین ممکن است مسدود نشوند  
- تأخیر ۵ ثانیه‌ای بین هر مرحله برای رعایت محدودیت‌ها  
"""

class BanAllPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.banning = False
        logger.info(f"پلاگین BanAll برای مالک: {self.owner_id} راه‌اندازی شد")

    async def initialize(self, me):
        self.me = me
        await self.handle_events()

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/banall|بن همه)$'))
        async def ban_all_handler(event):
            # بررسی مالک به صورت خاموش
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_group:
                await event.reply("❌ این دستور فقط در گروه‌ها قابل استفاده است.")
                return

            try:
                participant = await event.client(functions.channels.GetParticipantRequest(
                    event.chat_id, self.me.id
                ))
                if not participant.participant.admin_rights.ban_users:
                    await event.reply("❌ شما به مجوزهای ادمین با دسترسی به مسدود کردن نیاز دارید.")
                    return
            except ChatAdminRequiredError:
                await event.reply("❌ من به مجوزهای ادمین با دسترسی به مسدود کردن نیاز دارم.")
                return

            if self.banning:
                await event.reply("⚠️ عملیات مسدود کردن در حال انجام است.")
                return

            self.banning = True
            await event.reply("🚀 شروع مسدود کردن گروهی...")

            try:
                participants = await event.client.get_participants(event.chat_id)
                progress = await get_ban_progress(event.chat_id)
                banned_count = progress.get("banned_count", 0) if progress else 0

                batch_size = 10
                delay = 5

                for i in range(banned_count, len(participants), batch_size):
                    batch = participants[i:i + batch_size]

                    for user in batch:
                        if user.id != self.me.id and not user.is_self:
                            try:
                                await event.client(functions.channels.EditBannedRequest(
                                    event.chat_id, user.id,
                                    types.ChatBannedRights(until_date=None, view_messages=True)
                                ))
                                banned_count += 1
                            except UserAdminInvalidError:
                                continue
                            except FloodWaitError as e:
                                await asyncio.sleep(e.seconds)
                                continue
                            except Exception as e:
                                logger.error(f"خطای مسدود کردن: {str(e)}")
                                continue

                    await save_ban_progress(event.chat_id, {"banned_count": banned_count})
                    await asyncio.sleep(delay)

                await event.reply(f"✅ {banned_count} کاربر مسدود شدند.")
            except Exception as e:
                await event.reply(f"❌ شکست خورد: {str(e)}")
                logger.exception("خطای BanAll:")
            finally:
                self.banning = False
                await delete_ban_progress(event.chat_id)
