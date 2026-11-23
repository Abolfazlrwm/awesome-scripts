# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, errors
from telethon.tl.functions.messages import GetHistoryRequest
from telethon.tl.types import ChannelParticipantsAdmins, InputPeerChannel
import asyncio
import datetime
import re

HELP = """  
🗑️ **ابزار حذف هوشمند پیام‌ها** 🗑️  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های کلیدی**:  
• حذف تعداد مشخصی از پیام‌های اخیر  
• حذف پیام‌های قدیمی‌تر از زمان مشخص  
• حذف پیام‌های حاوی کلمات کلیدی  
• حذف همه پیام‌های گروه  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات اصلی**:

**انگلیسی:**
  `/del [count]` ➤ حذف تعداد مشخصی از پیام‌های اخیر  
  `/del all` ➤ حذف تمام پیام‌های گروه  
  `/del time [h/d] [number]` ➤ حذف پیام‌های قدیمی‌تر از زمان مشخص  
  `/del keyword [word]` ➤ حذف پیام‌های حاوی کلمه کلیدی  

**فارسی:**
  `حذف [تعداد]` ➤ حذف تعداد مشخصی از پیام‌های اخیر  
  `حذف همه` ➤ حذف تمام پیام‌های گروه  
  `حذف زمان [h/d] [عدد]` ➤ حذف پیام‌های قدیمی‌تر از زمان مشخص  
  `حذف کلمه [کلمه]` ➤ حذف پیام‌های حاوی کلمه کلیدی  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه اجرا**:  
1. برای حذف ۱۰ پیام اخیر:  
   `/del 10` یا `حذف 10`  
2. برای حذف پیام‌های قدیمی‌تر از ۲ روز:  
   `/del time d 2` یا `حذف زمان d 2`  
3. برای حذف پیام‌های حاوی کلمه "تبلیغات":  
   `/del keyword تبلیغات` یا `حذف کلمه تبلیغات`  
"""

class DelMessagesPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.delete_cooldown = {}  # To prevent spam per chat
        self.cooldown_time = 30    # Cooldown in seconds
        self.active_deletions = set()  # Track active deletion processes by chat ID
        self.max_deletion_batch = 100  # Maximum messages to delete in one batch
        self.deletion_delay = 0.5  # Delay between deletion batches
     
    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/del|حذف)(?:\s+(.+))?$'))
        async def delete_handler(event):
            # Only act if the sender is the owner
            if str(event.sender_id) != self.owner_id:
                return

            # Obtain the raw text and split it into words.
            message = event.raw_text.strip()
            words = message.split()
            if not words:
                return

            # Check that the command begins with the correct keyword.
            if words[0] not in ["/del", "حذف"]:
                return

            # For reply commands, require that the command contains exactly one word.
            if event.is_reply:
                if len(words) != 1:
                    return  # If extra words are present, ignore the command.
            else:
                # For non-reply commands, accept only if there are exactly 2 or 3 words.
                if len(words) not in [2, 3]:
                    return

                # For non-reply commands using target username/ID, the second word must either start with '@' or be all digits.
                # For commands like "del time ..." or "del keyword ...", the total word count is 3 and will be parsed later.
                if len(words) == 2:
                    target = words[1].strip()
                    if not (target.startswith("@") or target.isdigit() or target.lower() in ["all"]):
                        return  # If the parameter is not in a proper format, ignore.

            # Only allow in group chats.
            if event.is_private:
                await event.reply("❌ این دستور فقط در گروه‌ها قابل استفاده است")
                return

            # Check if sender is admin.
            try:
                chat = await event.get_chat()
                sender = await event.get_sender()
                permissions = await self.client.get_permissions(chat, sender)
                is_admin = permissions.is_admin or (str(sender.id) == self.owner_id)
                if not is_admin:
                    await event.reply("❌ فقط ادمین‌ها می‌توانند از این دستور استفاده کنند")
                    return
            except Exception as e:
                await event.reply(f"❌ خطا در بررسی دسترسی: {str(e)}")
                return

            # Check for any active deletion process in the same chat.
            chat_id = str(event.chat_id)
            current_time = asyncio.get_event_loop().time()
            if chat_id in self.active_deletions:
                await event.reply("⚠️ یک فرآیند حذف پیام در این گروه در حال اجراست. لطفاً صبر کنید.")
                return
            if chat_id in self.delete_cooldown:
                if current_time - self.delete_cooldown[chat_id] < self.cooldown_time:
                    remaining = int(self.cooldown_time - (current_time - self.delete_cooldown[chat_id]))
                    await event.reply(f"⏳ لطفاً {remaining} ثانیه صبر کنید و سپس دوباره تلاش کنید")
                    return
            self.delete_cooldown[chat_id] = current_time

            # Process the parameter (if any).
            arg = event.pattern_match.group(1)
            if not arg:
                await event.reply("❌ لطفاً پارامتر مورد نیاز را وارد کنید. مثال: `/del 10` یا `حذف 10` یا سایر فرمت‌ها")
                return

            processing_message = await event.reply("🔄 در حال آماده‌سازی برای حذف پیام‌ها...")
            try:
                self.active_deletions.add(chat_id)
                
                # Branch depending on the parameter.
                if arg.lower() == 'all' or arg.lower() == 'همه':
                    await self._delete_all_messages(event, chat, processing_message)
                
                elif arg.lower().startswith('time') or arg.lower().startswith('زمان'):
                    time_args = arg.split()
                    if len(time_args) != 3:
                        await processing_message.edit("❌ فرمت نادرست. نمونه درست: `/del time d 2` یا `حذف زمان d 2` برای حذف پیام‌های قدیمی‌تر از ۲ روز")
                        self.active_deletions.remove(chat_id)
                        return
                    
                    time_unit = time_args[1].lower()
                    if time_unit not in ['h', 'd']:
                        await processing_message.edit("❌ واحد زمانی نامعتبر. از 'h' برای ساعت یا 'd' برای روز استفاده کنید")
                        self.active_deletions.remove(chat_id)
                        return
                    
                    try:
                        time_value = int(time_args[2])
                        if time_value <= 0:
                            await processing_message.edit("❌ مقدار زمان باید مثبت باشد")
                            self.active_deletions.remove(chat_id)
                            return
                    except ValueError:
                        await processing_message.edit("❌ مقدار زمان باید یک عدد باشد")
                        self.active_deletions.remove(chat_id)
                        return
                    
                    await self._delete_by_time(event, chat, processing_message, time_unit, time_value)
                
                elif arg.lower().startswith('keyword') or arg.lower().startswith('کلمه'):
                    keyword_args = arg.split(maxsplit=1)
                    if len(keyword_args) != 2:
                        await processing_message.edit("❌ فرمت نادرست. نمونه درست: `/del keyword تبلیغات` یا `حذف کلمه تبلیغات`")
                        self.active_deletions.remove(chat_id)
                        return
                    
                    keyword = keyword_args[1].strip()
                    if not keyword:
                        await processing_message.edit("❌ لطفاً یک کلمه کلیدی معتبر وارد کنید")
                        self.active_deletions.remove(chat_id)
                        return
                    
                    await self._delete_by_keyword(event, chat, processing_message, keyword)
                
                elif arg.isdigit():
                    count = int(arg)
                    if count <= 0:
                        await processing_message.edit("❌ لطفاً یک عدد مثبت وارد کنید")
                        self.active_deletions.remove(chat_id)
                        return
                    
                    if count > 1000:
                        await processing_message.edit("⚠️ برای حفاظت از سرور، حداکثر ۱۰۰۰ پیام می‌توان در یک دستور حذف کرد")
                        count = 1000
                        await asyncio.sleep(2)
                    
                    await self._delete_messages_by_count(event, chat, processing_message, count)
                
                else:
                    await processing_message.edit("❌ دستور نامعتبر. برای مشاهده راهنما، از `/help del` استفاده کنید")
            
            except Exception as e:
                await processing_message.edit(f"❌ خطا در اجرای دستور: {str(e)}")
            finally:
                if chat_id in self.active_deletions:
                    self.active_deletions.remove(chat_id)

    async def _delete_messages_by_count(self, event, chat, processing_message, count):
        deleted_count = 0
        failed_count = 0
        
        await processing_message.edit(f"🔄 در حال حذف {count} پیام اخیر...")
        
        try:
            messages = []
            async for message in self.client.iter_messages(chat, limit=count):
                if message.id == processing_message.id:
                    continue
                messages.append(message)
            
            if not messages:
                await processing_message.edit("❌ هیچ پیامی برای حذف یافت نشد!")
                return
            
            total_messages = len(messages)
            
            for i in range(0, len(messages), self.max_deletion_batch):
                batch = messages[i:i + self.max_deletion_batch]
                progress = min(i + self.max_deletion_batch, total_messages)
                await processing_message.edit(f"🔄 در حال حذف... {progress}/{total_messages}")
                try:
                    deleted = await self.client.delete_messages(chat, batch)
                    deleted_count += len(deleted)
                    failed_count += len(batch) - len(deleted)
                except errors.FloodWaitError as e:
                    await asyncio.sleep(e.seconds)
                    try:
                        deleted = await self.client.delete_messages(chat, batch)
                        deleted_count += len(deleted)
                        failed_count += len(batch) - len(deleted)
                    except:
                        failed_count += len(batch)
                except Exception as e:
                    failed_count += len(batch)
                await asyncio.sleep(self.deletion_delay)
            
            if failed_count > 0:
                await processing_message.edit(f"✅ عملیات پایان یافت\n✓ {deleted_count} پیام حذف شد\n✗ {failed_count} پیام حذف نشد")
            else:
                await processing_message.edit(f"✅ عملیات با موفقیت انجام شد\n✓ {deleted_count} پیام حذف شد")
                
        except Exception as e:
            await processing_message.edit(f"❌ خطا در حذف پیام‌ها: {str(e)}")

    async def _delete_all_messages(self, event, chat, processing_message):
        deleted_count = 0
        failed_count = 0
        batch_size = self.max_deletion_batch
        
        await processing_message.edit("⚠️ شروع حذف تمام پیام‌های گروه. این عملیات ممکن است زمان‌بر باشد...")
        await asyncio.sleep(2)
        
        try:
            while True:
                messages = []
                async for message in self.client.iter_messages(chat, limit=batch_size):
                    if message.id == processing_message.id:
                        continue
                    messages.append(message)
                
                if not messages:
                    await processing_message.edit("ℹ️ هیچ پیام دیگری برای حذف یافت نشد!")
                    break
                
                current_batch_size = len(messages)
                try:
                    deleted = await self.client.delete_messages(chat, messages)
                    deleted_count += len(deleted)
                    failed_count += current_batch_size - len(deleted)
                    await processing_message.edit(f"🔄 در حال حذف پیام‌ها...\n✓ {deleted_count} پیام حذف شده\n⏳ ادامه دارد...")
                except errors.FloodWaitError as e:
                    await processing_message.edit(f"⏳ محدودیت تلگرام: باید {e.seconds} ثانیه صبر کنیم...")
                    await asyncio.sleep(e.seconds)
                    try:
                        deleted = await self.client.delete_messages(chat, messages)
                        deleted_count += len(deleted)
                        failed_count += current_batch_size - len(deleted)
                    except:
                        failed_count += current_batch_size
                except Exception as e:
                    failed_count += current_batch_size
                await asyncio.sleep(self.deletion_delay)
            
            if failed_count > 0:
                await processing_message.edit(f"✅ عملیات پایان یافت\n✓ {deleted_count} پیام حذف شد\n✗ {failed_count} پیام حذف نشد")
            else:
                await processing_message.edit(f"✅ عملیات با موفقیت انجام شد\n✓ {deleted_count} پیام حذف شد")
            
        except Exception as e:
            await processing_message.edit(f"❌ خطا در حذف همه پیام‌ها: {str(e)}")

    async def _delete_by_time(self, event, chat, processing_message, time_unit, time_value):
        deleted_count = 0
        failed_count = 0
        batch_size = self.max_deletion_batch
        
        now = datetime.datetime.now()
        if time_unit == 'h':
            threshold_time = now - datetime.timedelta(hours=time_value)
            time_description = f"{time_value} ساعت"
        else:
            threshold_time = now - datetime.timedelta(days=time_value)
            time_description = f"{time_value} روز"
        
        await processing_message.edit(f"🔄 در حال حذف پیام‌های قدیمی‌تر از {time_description}...")
        
        try:
            while True:
                messages_to_delete = []
                async for message in self.client.iter_messages(chat, limit=batch_size):
                    if message.id == processing_message.id:
                        continue
                    if message.date < threshold_time:
                        messages_to_delete.append(message)
                
                if not messages_to_delete:
                    break
                
                current_batch_size = len(messages_to_delete)
                try:
                    deleted = await self.client.delete_messages(chat, messages_to_delete)
                    deleted_count += len(deleted)
                    failed_count += current_batch_size - len(deleted)
                    await processing_message.edit(f"🔄 در حال حذف پیام‌های قدیمی‌تر از {time_description}...\n✓ {deleted_count} پیام حذف شده\n⏳ ادامه دارد...")
                except errors.FloodWaitError as e:
                    await processing_message.edit(f"⏳ محدودیت تلگرام: باید {e.seconds} ثانیه صبر کنیم...")
                    await asyncio.sleep(e.seconds)
                    try:
                        deleted = await self.client.delete_messages(chat, messages_to_delete)
                        deleted_count += len(deleted)
                        failed_count += current_batch_size - len(deleted)
                    except:
                        failed_count += current_batch_size
                except Exception as e:
                    failed_count += current_batch_size
                await asyncio.sleep(self.deletion_delay)
            
            if deleted_count == 0:
                await processing_message.edit(f"ℹ️ هیچ پیام قدیمی‌تر از {time_description} یافت نشد")
            elif failed_count > 0:
                await processing_message.edit(f"✅ عملیات پایان یافت\n✓ {deleted_count} پیام قدیمی‌تر از {time_description} حذف شد\n✗ {failed_count} پیام حذف نشد")
            else:
                await processing_message.edit(f"✅ عملیات با موفقیت انجام شد\n✓ {deleted_count} پیام قدیمی‌تر از {time_description} حذف شد")
            
        except Exception as e:
            await processing_message.edit(f"❌ خطا در حذف پیام‌های قدیمی: {str(e)}")

    async def _delete_by_keyword(self, event, chat, processing_message, keyword):
        deleted_count = 0
        failed_count = 0
        checked_count = 0
        max_search_messages = 1000  
        batch_size = self.max_deletion_batch
        keyword_lower = keyword.lower()
        
        await processing_message.edit(f"🔄 در حال جستجو و حذف پیام‌های حاوی کلمه '{keyword}'...")
        
        try:
            messages_to_delete = []
            async for message in self.client.iter_messages(chat, limit=max_search_messages):
                if message.id == processing_message.id:
                    continue
                checked_count += 1
                if message.text and keyword_lower in message.text.lower():
                    messages_to_delete.append(message)
                
                if len(messages_to_delete) >= batch_size:
                    try:
                        deleted = await self.client.delete_messages(chat, messages_to_delete)
                        deleted_count += len(deleted)
                        failed_count += len(messages_to_delete) - len(deleted)
                        messages_to_delete = []
                        await processing_message.edit(
                            f"🔄 جستجو و حذف پیام‌های حاوی '{keyword}'...\n✓ {deleted_count} پیام حذف شده\n🔍 {checked_count} پیام بررسی شده\n⏳ ادامه دارد..."
                        )
                    except errors.FloodWaitError as e:
                        await processing_message.edit(f"⏳ محدودیت تلگرام: باید {e.seconds} ثانیه صبر کنیم...")
                        await asyncio.sleep(e.seconds)
                        try:
                            deleted = await self.client.delete_messages(chat, messages_to_delete)
                            deleted_count += len(deleted)
                            failed_count += len(messages_to_delete) - len(deleted)
                        except:
                            failed_count += len(messages_to_delete)
                        messages_to_delete = []
                    await asyncio.sleep(self.deletion_delay)
            
            if messages_to_delete:
                try:
                    deleted = await self.client.delete_messages(chat, messages_to_delete)
                    deleted_count += len(deleted)
                    failed_count += len(messages_to_delete) - len(deleted)
                except errors.FloodWaitError as e:
                    await processing_message.edit(f"⏳ محدودیت تلگرام: باید {e.seconds} ثانیه صبر کنیم...")
                    await asyncio.sleep(e.seconds)
                    try:
                        deleted = await self.client.delete_messages(chat, messages_to_delete)
                        deleted_count += len(deleted)
                        failed_count += len(messages_to_delete) - len(deleted)
                    except:
                        failed_count += len(messages_to_delete)
                except Exception as e:
                    failed_count += len(messages_to_delete)
            
            if deleted_count == 0:
                await processing_message.edit(
                    f"ℹ️ هیچ پیامی حاوی کلمه '{keyword}' یافت نشد\n🔍 {checked_count} پیام بررسی شد"
                )
            elif failed_count > 0:
                await processing_message.edit(
                    f"✅ عملیات پایان یافت\n✓ {deleted_count} پیام حاوی '{keyword}' حذف شد\n✗ {failed_count} پیام حذف نشد\n🔍 {checked_count} پیام بررسی شد"
                )
            else:
                await processing_message.edit(
                    f"✅ عملیات با موفقیت انجام شد\n✓ {deleted_count} پیام حاوی '{keyword}' حذف شد\n🔍 {checked_count} پیام بررسی شد"
                )
            
        except Exception as e:
            await processing_message.edit(f"❌ خطا در جستجو و حذف پیام‌ها: {str(e)}")
