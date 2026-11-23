# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, errors
from telethon.tl.functions.channels import GetParticipantsRequest
from telethon.tl.types import ChannelParticipantsAdmins, ChannelParticipantsSearch
from telethon.tl.types import InputChannel
import asyncio
import re

HELP = """
📢 **ابزار تگ کردن کاربران**

**عملکرد:**
- تگ کردن تعداد مشخصی از کاربران گروه یا ادمین‌های گروه

**دستورات:**
- **تگ کردن کاربران:**
  - `/tag [تعداد]` یا **تگ [تعداد]**
  - **مثال:** `/tag 10` یا **تگ 10** برای تگ کردن ۱۰ کاربر
- **تگ کردن ادمین‌ها:**
  - `/tag admins` یا **تگ admins**
  - تگ کردن تمامی ادمین‌های گروه

**نکات:**
- حداکثر تعداد قابل تگ ۵۰۰ کاربر است.
- برای عملکرد صحیح، ربات باید در گروه به عنوان ادمین فعال باشد.
- دستور تگ فقط در گروه‌ها قابل استفاده است.

**هشدار:**
- لطفاً بین دستورات تگ کردن فاصله بگذارید تا از بروز خطا جلوگیری شود.
"""

class TagUsersPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.tag_cooldown = {}  # To prevent spam
        self.cooldown_time = 60  # Cooldown in seconds

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/tag|تگ)(?:\s+(.+))?$'))
        async def tag_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            # Only allow in group chats
            if event.is_private:
                await event.reply("❌ این دستور فقط در گروه‌ها قابل استفاده است")
                return

            # Check if sender is admin or bot owner
            chat = await event.get_chat()
            sender = await event.get_sender()
            is_admin = False
            
            try:
                participant = await self.client.get_permissions(chat, sender)
                is_admin = participant.is_admin
            except:
                pass
            # Check cooldown
            chat_id = str(event.chat_id)
            current_time = asyncio.get_event_loop().time()
            if chat_id in self.tag_cooldown:
                if current_time - self.tag_cooldown[chat_id] < self.cooldown_time:
                    remaining = int(self.cooldown_time - (current_time - self.tag_cooldown[chat_id]))
                    await event.reply(f"⏳ لطفاً {remaining} ثانیه صبر کنید و سپس دوباره تلاش کنید")
                    return
            
            self.tag_cooldown[chat_id] = current_time
            
            # Process the command
            arg = event.pattern_match.group(1)
            
            if not arg:
                await event.reply("❌ لطفاً تعداد کاربران یا `admins` را مشخص کنید. مثال: `/tag 10` یا `/tag admins`")
                return
            
            # Let user know we're working on it
            processing_message = await event.reply("🔄 در حال پردازش، لطفاً صبر کنید...")
            
            try:
                if arg.lower() == 'admins':
                    await self._tag_admins(event, processing_message)
                elif arg.isdigit():
                    count = int(arg)
                    if count <= 0:
                        await processing_message.edit("❌ لطفاً یک عدد مثبت وارد کنید")
                        return
                    if count > 500:
                        count = 500  # Limit to 500 users
                        await processing_message.edit("⚠️ حداکثر تعداد کاربران محدود به ۵۰۰ نفر است")
                        await asyncio.sleep(2)
                    
                    await self._tag_users(event, count, processing_message)
                else:
                    await processing_message.edit("❌ لطفاً یک عدد یا `admins` وارد کنید")
            except Exception as e:
                await processing_message.edit(f"❌ خطا: {str(e)}")

    async def _tag_admins(self, event, processing_message):
        chat = await event.get_chat()
        
        try:
            admins = []
            async for admin in self.client.iter_participants(chat, filter=ChannelParticipantsAdmins):
                if not admin.bot and not admin.deleted:
                    admins.append(admin)
            
            if not admins:
                await processing_message.edit("❌ هیچ ادمینی یافت نشد!")
                return
            
            await self._send_mentions(event.chat_id, admins, processing_message, "ادمین‌ها")
            
        except Exception as e:
            await processing_message.edit(f"❌ خطا در تگ کردن ادمین‌ها: {str(e)}")

    async def _tag_users(self, event, count, processing_message):
        chat = await event.get_chat()
        
        try:
            users = []
            async for user in self.client.iter_participants(chat, limit=count):
                if not user.bot and not user.deleted:
                    users.append(user)
            
            if not users:
                await processing_message.edit("❌ هیچ کاربری یافت نشد!")
                return
            
            tag_type = f"{len(users)} کاربر"
            await self._send_mentions(event.chat_id, users, processing_message, tag_type)
            
        except Exception as e:
            await processing_message.edit(f"❌ خطا در تگ کردن کاربران: {str(e)}")

    async def _send_mentions(self, chat_id, users, processing_message, tag_type):
        chunks = [users[i:i+6] for i in range(0, len(users), 6)]
        
        await processing_message.edit(f"🔔 در حال تگ کردن {tag_type}...")
        
        for i, chunk in enumerate(chunks):
            mentions = []
            for user in chunk:
                name = user.first_name if user.first_name else "کاربر"
                mentions.append(f"[{name}](tg://user?id={user.id})")
            
            mention_text = " | ".join(mentions)
            
            if i == 0:
                await processing_message.edit(f"🔔 {tag_type}:\n{mention_text}")
            else:
                try:
                    await self.client.send_message(chat_id, mention_text, parse_mode='md')
                except errors.FloodWaitError as e:
                    await self.client.send_message(
                        chat_id, 
                        f"⚠️ تلگرام محدودیت زمانی اعمال کرد. لطفاً {e.seconds} ثانیه صبر کنید."
                    )
                    await asyncio.sleep(e.seconds)
                    # Try to continue after waiting
                    await self.client.send_message(chat_id, mention_text, parse_mode='md')
                
                # Add a short delay between messages to prevent flood
                await asyncio.sleep(2)
