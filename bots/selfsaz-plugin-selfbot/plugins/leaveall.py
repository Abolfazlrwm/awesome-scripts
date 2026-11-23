# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, types
import asyncio
import logging

HELP = """
🚪 **خروج هوشمند از گروه‌ها و کانال‌ها** 🚪

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت‌های کلیدی**:
• خروج از تمام گروه‌ها، کانال‌ها و سوپرگروه‌ها
• خروج انتخابی با فیلتر نوع چت
• نمایش لیست چت‌های فعلی با اطلاعات کامل
• قابلیت توقف فرآیند خروج در صورت نیاز

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات اصلی**:

**انگلیسی:**
  `/leaveall` ➔ خروج از تمام چت‌ها  
  `/leaveall groups` ➔ خروج فقط از گروه‌ها  
  `/leaveall channels` ➔ خروج فقط از کانال‌ها  
  `/leaveall supergroups` ➔ خروج فقط از سوپرگروه‌ها  
  `/listchats` ➔ نمایش لیست چت‌های فعلی  
  `/stopleaving` ➔ توقف فرآیند خروج در حال اجرا  

**فارسی:**
  `خروج تمام` ➔ خروج از تمام چت‌ها  
  `خروج گروه‌ها` ➔ خروج فقط از گروه‌ها  
  `خروج کانال‌ها` ➔ خروج فقط از کانال‌ها  
  `خروج سوپرگروه‌ها` ➔ خروج فقط از سوپرگروه‌ها  
  `لیست چت‌ها` ➔ نمایش لیست چت‌های فعلی  
  `توقف خروج` ➔ توقف فرآیند خروج در حال اجرا  

▬▬▬▬▬▬▬▬▬▬▬▬
⚠️ **نکات مهم**:
• این فرآیند برگشت‌ناپذیر است! قبل از اجرا مطمئن شوید.
• تلگرام محدودیت‌هایی برای خروج همزمان دارد، بنابراین بین خروج‌ها تأخیر وجود دارد.
• چت‌های شخصی (PV) از این فرآیند مستثنی هستند.
"""

class LeaveAllPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.is_leaving = False
        self.logger = logging.getLogger(__name__)
        self.confirm_ids = {}  # Store confirmation message IDs here

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/leaveall|خروج\s+تمام)(?:\s+(.+))?$'))
        async def leaveall_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            # Check if already running
            if self.is_leaving:
                await event.reply("⚠️ فرآیند خروج در حال اجراست. برای توقف از دستور `/stopleaving` یا `توقف خروج` استفاده کنید.")
                return
                
            filter_type = event.pattern_match.group(1)
            
            # Validate filter type if provided
            valid_filters = [None, "groups", "channels", "supergroups",
                             "گروه‌ها", "کانال‌ها", "سوپرگروه‌ها"]
            if filter_type and filter_type not in valid_filters:
                await event.reply("❌ فیلتر نامعتبر! گزینه‌های معتبر: groups, channels, supergroups (یا معادل فارسی: گروه‌ها، کانال‌ها، سوپرگروه‌ها)")
                return
            
            # Generate a unique confirmation ID for this request
            confirm_id = str(event.id)
            
            # Confirmation message with warning
            confirm_msg = await event.reply(
                f"⚠️ **هشدار**: شما در حال خروج از تمام چت‌ها هستید. این عملیات برگشت‌ناپذیر است!\n\n"
                f"برای تأیید، روی این پیام ریپلای کرده و بنویسید `تأیید {confirm_id}`"
            )
            
            # Store the confirmation details along with a timeout task
            self.confirm_ids[confirm_id] = {
                'filter_type': filter_type,
                'event': event,
                'timeout': asyncio.create_task(self._handle_timeout(confirm_id, confirm_msg))
            }
        
        # Handler for confirmation responses (only from owner)
        @self.client.on(events.NewMessage(from_users=int(self.owner_id)))
        async def confirmation_handler(event):
            if not event.is_reply:
                return
                
            # Check if it's a reply to one of our confirmation messages
            for confirm_id, data in list(self.confirm_ids.items()):
                expected_text = f"تأیید {confirm_id}"
                if event.raw_text.strip() == expected_text:
                    # Cancel the timeout task and remove confirmation info
                    data['timeout'].cancel()
                    filter_type = data['filter_type']
                    original_event = data['event']
                    del self.confirm_ids[confirm_id]
                    
                    # Start the leaving process
                    self.is_leaving = True
                    status_msg = await event.reply("🔄 در حال شروع فرآیند خروج...")
                    
                    try:
                        await self._leave_all_chats(status_msg, filter_type)
                    finally:
                        self.is_leaving = False
                    return
        
        @self.client.on(events.NewMessage(pattern=r'^(?:/stopleaving|توقف\s+خروج)$'))
        async def stopleaving_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            if not self.is_leaving:
                await event.reply("❌ هیچ فرآیند خروجی در حال اجرا نیست.")
                return
                
            self.is_leaving = False
            await event.reply("✅ درخواست توقف فرآیند خروج ثبت شد. پس از اتمام عملیات فعلی، فرآیند متوقف خواهد شد.")
            
        @self.client.on(events.NewMessage(pattern=r'^(?:/listchats|لیست\s+چت‌ها)$'))
        async def listchats_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            await event.reply("🔄 در حال دریافت لیست چت‌ها...")
            
            dialogs = await self.client.get_dialogs()
            
            groups = []
            channels = []
            supergroups = []
            
            for dialog in dialogs:
                entity = dialog.entity
                if isinstance(entity, types.Channel):
                    if entity.broadcast:
                        channels.append(f"• {dialog.name} ({dialog.id})")
                    else:
                        supergroups.append(f"• {dialog.name} ({dialog.id})")
                elif isinstance(entity, types.Chat):
                    groups.append(f"• {dialog.name} ({dialog.id})")
            
            response = "📊 **لیست چت‌های فعلی**:\n\n"
            if groups:
                response += "**گروه‌ها**:\n" + "\n".join(groups) + "\n\n"
            if channels:
                response += "**کانال‌ها**:\n" + "\n".join(channels) + "\n\n"
            if supergroups:
                response += "**سوپرگروه‌ها**:\n" + "\n".join(supergroups) + "\n\n"
            if not groups and not channels and not supergroups:
                response += "❌ شما در هیچ گروه، کانال یا سوپرگروهی عضو نیستید."
                
            await event.reply(response)
    
    async def _handle_timeout(self, confirm_id, confirm_msg):
        # Wait 60 seconds then cancel the confirmation if not responded to
        await asyncio.sleep(60)
        if confirm_id in self.confirm_ids:
            await confirm_msg.edit("⏱️ زمان تأیید به پایان رسید. عملیات لغو شد.")
            del self.confirm_ids[confirm_id]
    
    async def _leave_all_chats(self, status_msg, filter_type=None):
        dialogs = await self.client.get_dialogs()
        
        total = 0
        left = 0
        skipped = 0
        failed = 0
        
        for dialog in dialogs:
            if not self.is_leaving:
                await status_msg.edit(f"⚠️ فرآیند خروج متوقف شد.\n\n"
                                      f"✅ خروج موفق: {left}\n"
                                      f"⏭️ رد شده: {skipped}\n"
                                      f"❌ ناموفق: {failed}\n"
                                      f"📊 مجموع پردازش شده: {total}/{len(dialogs)}")
                return
                
            entity = dialog.entity
            chat_id = dialog.id
            chat_name = dialog.name
            
            # Skip private chats
            if isinstance(entity, types.User):
                skipped += 1
                total += 1
                continue
            
            # Apply filter if provided (supporting both English and Farsi filters)
            if filter_type:
                if filter_type in ["groups", "گروه‌ها"]:
                    if not isinstance(entity, types.Chat):
                        skipped += 1
                        total += 1
                        continue
                if filter_type in ["channels", "کانال‌ها"]:
                    if not (isinstance(entity, types.Channel) and entity.broadcast):
                        skipped += 1
                        total += 1
                        continue
                if filter_type in ["supergroups", "سوپرگروه‌ها"]:
                    if not (isinstance(entity, types.Channel) and not entity.broadcast):
                        skipped += 1
                        total += 1
                        continue
            
            try:
                if total % 5 == 0:  # Update every 5 chats
                    await status_msg.edit(f"🔄 در حال خروج از چت‌ها: {total}/{len(dialogs)}\n"
                                         f"🚪 در حال خروج از: {chat_name}\n\n"
                                         f"✅ خروج موفق: {left}\n"
                                         f"⏭️ رد شده: {skipped}\n"
                                         f"❌ ناموفق: {failed}")
            except Exception:
                pass
            
            try:
                await self.client.delete_dialog(chat_id)
                left += 1
                self.logger.info(f"Left chat: {chat_name} ({chat_id})")
            except Exception as e:
                failed += 1
                self.logger.error(f"Failed to leave chat {chat_name} ({chat_id}): {str(e)}")
            
            total += 1
            await asyncio.sleep(2)
        
        await status_msg.edit(f"✅ فرآیند خروج به پایان رسید!\n\n"
                             f"✅ خروج موفق: {left}\n"
                             f"⏭️ رد شده: {skipped}\n"
                             f"❌ ناموفق: {failed}\n"
                             f"📊 مجموع پردازش شده: {total}/{len(dialogs)}")
