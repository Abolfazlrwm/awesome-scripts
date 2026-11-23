# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""پلاگین تگ همه برای گروه‌های تلگرام"""

from telethon import events, types, functions
from .base import Plugin
import asyncio
import time

HELP = """  
🏷️ **تگ جمعی هوشمند اعضا** 🏷️  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• تگ تمامی اعضای گروه به صورت خودکار  
• حذف بات‌ها و اکانت‌های حذف شده از لیست  
• تقسیم کاربران به گروه‌های ۶ نفره برای جلوگیری از اسپم  
• مدیریت زمان انتظار بین درخواست‌ها (۳۰ ثانیه)  
• نمایش پیشرفت عملیات به صورت زنده  

▬▬▬▬▬▬▬▬▬▬▬▬▌▬▬▬▬  
🎯 **دستورات کلیدی**:  

**دستور فارسی:**  
تگ همه  (بدون اسلش، با فاصله)

**دستور انگلیسی:**  
/tagall

(هر دو دستور شروع فرایند تگ تمام اعضا را فعال می‌کنند)

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  
1. در گروه مورد نظر دستور را ارسال کنید:  
   - **تگ همه** (فارسی) یا  
   - **/tagall** (انگلیسی)  
2. منتظر بمانید تا لیست اعضا به صورت گروه‌های ۶ نفره تگ شود  
3. دریافت پیام تأیید نهایی پس از اتمام  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ حداکثر تعداد در هر گروه تگی: ۶ کاربر  
▫️ تأخیر بین ارسال گروه‌ها: ۱.۵ ثانیه  
▫️ زمان انتظار بین درخواست‌ها: ۳۰ ثانیه  
▫️ فرمت تگ: نام کوچک کاربر + لینک پروفایل  

⚠️ **هشدارهای مهم**:  
- نیاز به دسترسی **ادمین** در گروه  
- فقط برای کاربر مالک ربات فعال است  
- استفاده مکرر ممکن است باعث محدودیت موقت شود  
- از این قابلیت فقط در گروه‌های ضروری استفاده کنید  
"""  

class TagAllPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = user_id
        self.cooldown = {}

    async def _is_admin(self, event):
        """بررسی آیا کاربر ادمین یا مالک است"""
        if event.sender_id == self.owner_id:
            return True
        try:
            participant = await event.client.get_permissions(
                entity=event.chat_id,
                user=event.sender_id
            )
            return participant.is_admin
        except:
            return False

    async def _get_members(self, chat):
        """دریافت اعضا بدون بات‌ها و اکانت‌های حذف شده"""
        members = []
        async for user in self.client.iter_participants(chat):
            if not user.bot and not user.deleted:
                members.append(user)
        return members

    async def _mention_chunk(self, event, chunk):
        """ساخت متن تگ برای هر گروه از کاربران"""
        mentions = []
        for user in chunk:
            name = user.first_name.replace(" ", "")
            mentions.append(f"[{name}](tg://user?id={user.id})")
        return " ".join(mentions)

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/tagall|تگ\s+همه)$'))
        async def tagall_handler(event):
            # بررسی مجوزها
            if str(event.sender_id) != self.owner_id:
                return

            # بررسی زمان انتظار (30 ثانیه)
            chat_id = event.chat_id
            if self.cooldown.get(chat_id, 0) > time.time():
                remaining = int(self.cooldown[chat_id] - time.time())
                await event.reply(f"⏳ لطفا {remaining} ثانیه قبل از استفاده مجدد صبر کنید!")
                return

            # دریافت اعضا
            try:
                members = await self._get_members(event.chat_id)
            except Exception as e:
                await event.reply(f"❌ خطا در دریافت اعضا: {str(e)}")
                return

            if not members:
                await event.reply("❌ هیچ عضوی برای تگ کردن وجود ندارد!")
                return

            # تقسیم به گروه‌های 6 نفره
            chunks = [members[i:i+6] for i in range(0, len(members), 6)]
            total = len(chunks)
            
            # ارسال وضعیت اولیه
            msg = await event.reply(f"🔔 در حال تگ کردن {len(members)} عضو ({total} گروه)...")

            # ارسال تگ‌ها با تاخیر
            for i, chunk in enumerate(chunks, 1):
                mention_text = await self._mention_chunk(event, chunk)
                await event.respond(mention_text)
                
                # به روزرسانی پیشرفت هر 3 گروه
                if i % 3 == 0:
                    await msg.edit(f"پیشرفت: ارسال گروه {i}/{total}")
                
                await asyncio.sleep(1.5)  # تاخیر ضد اسپم

            # پاک کردن پیام وضعیت و تنظیم زمان انتظار
            await msg.delete()
            self.cooldown[chat_id] = time.time() + 30
            await event.respond("✅ تمام اعضا با موفقیت تگ شدند!")
    
    async def shutdown(self):
        pass
