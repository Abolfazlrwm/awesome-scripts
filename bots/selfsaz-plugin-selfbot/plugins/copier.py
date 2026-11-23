# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import asyncio
from .base import Plugin
from telethon import events, types
from telethon.tl import functions
from telethon.errors import FloodWaitError

logger = logging.getLogger(__name__)

HELP = """  
🚀 **کپی هوشمند محتوا بین کانال‌ها و گروه‌ها** 🚀  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های کلیدی**:  
• انتقال کامل محتوا شامل **متن، مدیا، فرمت‌بندی و لینک‌ها**  
• پشتیبانی از کپی از/به **کانال‌ها، سوپرگروه‌ها و پیوی‌ها**  
• کنترل خودکار نرخ ارسال (۰.۵ ثانیه بین پیام‌ها)  
• مدیریت خطاهای FloodWait به صورت خودکار  
• امکان توقف لحظه‌ای عملیات  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات اصلی**:

**انگلیسی:**  
  `/copy @source @destination` ➤ شروع عملیات کپی  
  `/stop` ➤ توقف تمام عملیات‌های در حال اجرا  

**فارسی:**  
  `کپی @منبع @مقصد` ➤ شروع عملیات کپی  
  `توقف` ➤ توقف تمام عملیات‌های در حال اجرا  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه اجرا**:  
1. کپی از یک کانال به گروه:  
   `/copy @my_channel @private_group` یا `کپی @my_channel @private_group`  
2. توقف عملیات در صورت نیاز:  
   `/stop` یا `توقف`  

⚠️ **شرایط و محدودیت‌ها**:  
- ربات باید در منبع و مقصد **ادمین** باشد  
- حداکثر حجم فایل‌های قابل انتقال: 2GB  
- در صورت قطع ارتباط، عملیات از آخرین پیام ادامه می‌یابد  
- کپی پیام‌های حاوی استیکر انیمیشنی ممکن است با خطا مواجه شود  
"""

class CopierPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.active_tasks = {}
        self.lock = asyncio.Lock()
        logger.info(f"پلاگین کپی‌کننده برای مالک: {self.owner_id} راه‌اندازی شد")

    async def initialize(self, me):
        self.me = me

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/copy|کپی)(?:@\w+)?\s+(@\w+)\s+(@\w+)$'))
        async def copy_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            try:
                _, src, dest = event.pattern_match.groups()
                task = asyncio.create_task(
                    self.copy_operation(event, src, dest)
                )
                async with self.lock:
                    self.active_tasks[dest] = task
                
                await event.reply(f"🚀 شروع کپی‌کردن از {src} به {dest}")
                await event.delete()

            except Exception as e:
                logger.error(f"خطای کپی: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/stop|توقف)$'))
        async def stop_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            async with self.lock:
                for dest, task in self.active_tasks.items():
                    task.cancel()
                self.active_tasks.clear()
            
            await event.reply("🛑 همه عملیات‌ها متوقف شدند")
            await event.delete()

    async def copy_operation(self, event, src_username, dest_username):
        try:
            src = await self.client.get_entity(src_username)
            dest = await self.client.get_entity(dest_username)
            
            async for msg in self.client.iter_messages(src):
                if dest_username in self.active_tasks:
                    try:
                        if msg.media:
                            await self.client.send_message(
                                dest,
                                msg.text,
                                file=msg.media,
                                formatting_entities=msg.entities,
                                link_preview=isinstance(msg.media, types.MessageMediaWebPage),
                                silent=True
                            )
                        else:
                            await self.client.send_message(
                                dest,
                                message=msg.text
                            )
                        await asyncio.sleep(0.5)  # کنترل نرخ ارسال
                    except FloodWaitError as e:
                        await asyncio.sleep(e.seconds)
                    except Exception as e:
                        logger.error(f"خطای پیام {msg.id}: {str(e)}")
                else:
                    break

        except asyncio.CancelledError:
            await event.reply(f"⏹ کپی‌کردن به {dest_username} متوقف شد")
        except Exception as e:
            await event.reply(f"❌ خطا در کپی‌کردن به {dest_username}: {str(e)}")
        finally:
            async with self.lock:
                if dest_username in self.active_tasks:
                    del self.active_tasks[dest_username]
