# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
from telethon.errors import FloodWaitError
import asyncio

HELP = """  
📢 **پخش همگانی پیام در تمام چت‌ها** 📢  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• ارسال پیام به **گروه‌ها، کانال‌ها، پیوی‌ها یا همه** به صورت یکجا  
• پشتیبانی از ارسال **هم متن و هم مدیا** (عکس، ویدئو، فایل و...)  
• مدیریت خودکار محدودیت‌های نرخ ارسال (FloodWait)  
• گزارش کامل موفقیت/شکست هر ارسال  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:

**انگلیسی:**
  `/sendall [groups|channels|pvs|all]` ➤ ارسال به تمامی چت‌های مورد نظر

**فارسی:**
  `پخش همه [گروه‌ها|کانال‌ها|پیوی‌ها|همه]` ➤ ارسال به تمامی چت‌های مورد نظر

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. ریپلای روی پیام مورد نظر  
2. ارسال دستور:  
   `/sendall groups` یا `پخش همه گروه‌ها`  
3. دریافت گزارش کامل پس از اتمام  

⚠️ **نکات مهم**:  
- فقط برای **مالک ربات** قابل اجراست  
- حتما باید روی پیام مورد نظر ریپلای شود  
- فاصله ۱ ثانیه‌ای بین هر ارسال برای جلوگیری از محدودیت  
- در صورت بروز خطای FloodWait، سیستم به صورت خودکار منتظر می‌ماند  
"""

# Mapping for Farsi keywords to English equivalents
KEYWORD_MAP = {
    "گروه‌ها": "groups",
    "کانال‌ها": "channels",
    "پیوی‌ها": "pvs",
    "همه": "all"
}

class BroadcastPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/sendall|پخش همه)\s+(groups|channels|pvs|all|گروه‌ها|کانال‌ها|پیوی‌ها|همه)$'))
        async def broadcast_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_reply:
                await event.reply("❌ لطفاً به پیامی که می‌خواهید پخش کنید پاسخ دهید.")
                return

            reply = await event.get_reply_message()
            if not reply:
                await event.reply("❌ هیچ پیامی برای پخش وجود ندارد.")
                return

            target_type = event.pattern_match.group(1)

            # If a Farsi keyword is used, map it to the equivalent English term
            target_type = KEYWORD_MAP.get(target_type, target_type)

            await event.reply(f"✅ در حال پخش پیام به {target_type}...")

            dialogs = await self.client.get_dialogs(limit=200)
            targets = []

            if target_type in ['groups', 'all']:
                targets.extend([dialog for dialog in dialogs if dialog.is_group])
            if target_type in ['channels', 'all']:
                targets.extend([dialog for dialog in dialogs if dialog.is_channel])
            if target_type in ['pvs', 'all']:
                targets.extend([dialog for dialog in dialogs if dialog.is_user])

            # حذف دیالوگ‌های تکراری
            targets = list({dialog.id: dialog for dialog in targets}.values())

            success_count = 0
            failure_count = 0
            errors = []

            for target in targets:
                max_retries = 3
                for attempt in range(max_retries):
                    try:
                        if reply.media:
                            await self.client.send_file(
                                target.id,
                                file=reply.media,
                                caption=reply.text,  # ارسال متن همراه مدیا
                                reply_to=None
                            )
                        else:
                            await self.client.send_message(
                                target.id,
                                message=reply.text
                            )
                        success_count += 1
                        await asyncio.sleep(1)
                        break
                    except FloodWaitError as e:
                        if attempt < max_retries - 1:
                            await asyncio.sleep(e.seconds)
                            continue
                        failure_count += 1
                        errors.append(f"⏳ FloodWaitError برای {target.id} بعد از {max_retries} تلاش")
                    except Exception as e:
                        failure_count += 1
                        errors.append(f"❌ خطا در ارسال به {target.id}: {str(e)}")
                        break

            report = (
                f"✅ پخش به {target_type} تکمیل شد!\n"
                f"موفقیت: {success_count}\n"
                f"شکست: {failure_count}\n"
            )
            if errors:
                report += "\nخطاها:\n" + "\n".join(errors[:5])  # نمایش حداکثر ۵ خطا

            await event.reply(report)
