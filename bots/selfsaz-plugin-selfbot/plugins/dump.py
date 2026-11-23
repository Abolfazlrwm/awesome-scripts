# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/devtools.py
from .base import Plugin
from telethon import events
from telethon.tl.functions.users import GetFullUserRequest
from telethon.tl.functions.channels import GetFullChannelRequest
from telethon.tl.functions.messages import GetFullChatRequest
import asyncio
import json

HELP = """  
🛠️ **ابزارهای توسعه‌دهنده تلگرام** 🛠️  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• استخراج اطلاعات خام از کاربران، کانال‌ها و گروه‌ها
• نمایش اطلاعات کامل پیام‌ها و کاربران
• دسترسی به متدهای مفید Telethon برای توسعه‌دهندگان
• خروجی خام و کامل برای تحلیل‌های پیشرفته

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:

**اطلاعات خام**:  
  `/dump` ➤ (با ریپلای) نمایش داده‌های خام پیام
  
**اطلاعات کاربران**:  
  `/getuser` ➤ (با ریپلای یا آیدی) دریافت اطلاعات کاربر
  `/getfulluser` ➤ (با ریپلای یا آیدی) دریافت اطلاعات کامل کاربر
  
**اطلاعات چت**:  
  `/getchat` ➤ دریافت اطلاعات چت فعلی
  `/getfullchat` ➤ دریافت اطلاعات کامل چت فعلی
  
**متدهای کاربردی**:  
  `/entity` ➤ (با ریپلای یا آیدی) دریافت entity کاربر/چت
  `/resolve` ➤ (با آیدی) تبدیل username به entity
  
**راهنما**:  
  `/devhelp` ➤ نمایش این راهنما

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
• `/getuser @username` یا ریپلای + `/getuser`
• `/getfullchat` در یک گروه یا کانال

⚠️ **نکات فنی**:  
- خروجی‌های طولانی به صورت خودکار تقسیم می‌شوند
- بعضی از دستورات به دسترسی ادمین نیاز دارند
- اطلاعات حساس را در چت‌های خصوصی بررسی کنید
"""

class DumpRawPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        
    async def split_and_send(self, event, text):
        """Split text into chunks and send them as responses"""
        max_length = 4000  # Safe limit for code blocks
        chunks = [text[i:i+max_length] for i in range(0, len(text), max_length)]
        
        for i, chunk in enumerate(chunks):
            await event.respond(f"```\n{chunk}\n```", parse_mode='markdown')
            await asyncio.sleep(0.5)
            
    async def get_entity_from_event(self, event):
        """Extract entity from reply or command arguments"""
        if event.is_reply:
            reply = await event.get_reply_message()
            return await self.client.get_entity(reply.sender_id)
        else:
            # Try to parse the command arguments
            text = event.raw_text.split(maxsplit=1)
            if len(text) > 1:
                try:
                    return await self.client.get_entity(text[1])
                except:
                    await event.reply("❌ نمی‌توان entity را از آرگومان دستور پیدا کرد!")
                    return None
            else:
                await event.reply("❌ لطفاً روی پیام کاربر ریپلای کنید یا آیدی/یوزرنیم کاربر را وارد کنید!")
                return None
    
    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/dump|نمایش خام)$'))
        async def raw_dump_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_reply:
                await event.reply("❌ لطفاً روی یک پیام ریپلای کنید تا داده‌های خام آن نمایش داده شود!")
                return

            try:
                replied_msg = await event.get_reply_message()
                raw_data = str(replied_msg.to_dict())
                await self.split_and_send(event, raw_data)
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/getuser(?:\s+.+)?$'))
        async def get_user_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                entity = await self.get_entity_from_event(event)
                if entity:
                    raw_data = str(entity.to_dict())
                    await self.split_and_send(event, raw_data)
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/getfulluser(?:\s+.+)?$'))
        async def get_full_user_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                entity = await self.get_entity_from_event(event)
                if entity:
                    full_user = await self.client(GetFullUserRequest(entity))
                    raw_data = str(full_user.to_dict())
                    await self.split_and_send(event, raw_data)
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/getchat$'))
        async def get_chat_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                chat_entity = await event.get_chat()
                raw_data = str(chat_entity.to_dict())
                await self.split_and_send(event, raw_data)
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/getfullchat$'))
        async def get_full_chat_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                chat = await event.get_chat()
                if hasattr(chat, 'megagroup') or hasattr(chat, 'channel'):
                    full_chat = await self.client(GetFullChannelRequest(chat))
                else:
                    full_chat = await self.client(GetFullChatRequest(chat.id))
                    
                raw_data = str(full_chat.to_dict())
                await self.split_and_send(event, raw_data)
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/entity(?:\s+.+)?$'))
        async def entity_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                entity = await self.get_entity_from_event(event)
                if entity:
                    raw_data = str(entity.to_dict())
                    await self.split_and_send(event, raw_data)
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/resolve\s+(.+)$'))
        async def resolve_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                username = event.pattern_match.group(1).strip()
                entity = await self.client.get_entity(username)
                raw_data = str(entity.to_dict())
                await self.split_and_send(event, raw_data)
            except Exception as e:
                await event.reply(f"❌ Failed to resolve '{username}': {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/devhelp$'))
        async def help_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            await event.reply(HELP)

    async def initialize(self, me):
        pass