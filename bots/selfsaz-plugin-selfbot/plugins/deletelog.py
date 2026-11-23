# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
from telethon.tl.types import PeerChannel, PeerUser, PeerChat, Channel, Chat
import sqlite3
import os
import asyncio
from datetime import datetime

HELP = """
⚠️ **ردیاب پیام‌های حذف شده** ⚠️

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **ویژگی‌های اصلی**:
• ردیابی پیام‌های حذف شده در چت‌های تحت نظر
• ذخیره پیام‌های حذف شده در کانال مشخص شده شما
• پشتیبانی از چت‌های خصوصی و گروه‌ها
• حفظ محتوا و اطلاعات ارسال کننده پیام

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات**:

**انگلیسی:**
  `/trackchat` ➤ شروع ردیابی پیام‌های حذف شده در این چت  
  `/untrackchat` ➤ توقف ردیابی پیام‌های حذف شده در این چت  
  `/setlogchannel [کانال]` ➤ تنظیم کانال ذخیره پیام‌ها  
  `/trackedchats` ➤ نمایش تمام چت‌های تحت نظر  
  `/deletionstats` ➤ نمایش آمار حذف پیام‌ها  
  `/deletestatus` ➤ نمایش وضعیت ردیاب  
  `/testdelete` ➤ ارسال پیام تست حذف

**فارسی:**
  `ردیاب چت` ➤ شروع ردیابی پیام‌های حذف شده در این چت  
  `عدم ردیابی چت` ➤ توقف ردیابی پیام‌های حذف شده در این چت  
  `تنظیم کانال ذخیره` ➤ تنظیم کانال ذخیره پیام‌ها  
  `نمایش چت‌های ردیابی` ➤ نمایش تمام چت‌های تحت نظر  
  `آمار حذف پیام` ➤ نمایش آمار حذف پیام‌ها  
  `وضعیت حذف` ➤ نمایش وضعیت ردیاب  
  `تست حذف پیام` ➤ ارسال پیام تست حذف

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **راهنمای نصب**:
1. ربات را به چتی که می‌خواهید نظارت کنید اضافه کنید  
2. یک کانال برای ذخیره پیام‌ها تنظیم کنید: `/setlogchannel @کانال_شما` یا `تنظیم کانال ذخیره @کانال_شما`  
3. ردیابی را شروع کنید: `/trackchat` یا `ردیاب چت`  
4. تمام پیام‌های حذف شده در کانال شما ذخیره می‌شوند
"""

class DeleteTrackerPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.db_path = "data/deletion_tracker.db"
        self._init_db()
        self.message_cache = {}  # ذخیره پیام‌ها با آیدی چت به عنوان کلید
        self.max_messages_per_chat = 1000  # حداکثر تعداد پیام‌های ذخیره شده برای هر چت
        
    def _init_db(self):
        os.makedirs(os.path.dirname(self.db_path), exist_ok=True)
        
        with sqlite3.connect(self.db_path) as conn:
            conn.execute("""
                CREATE TABLE IF NOT EXISTS tracked_chats (
                    owner_id TEXT,
                    chat_id TEXT,
                    PRIMARY KEY (owner_id, chat_id)
                )
            """)
            
            conn.execute("""
                CREATE TABLE IF NOT EXISTS log_channels (
                    owner_id TEXT PRIMARY KEY,
                    channel_id TEXT
                )
            """)
            
            conn.execute("""
                CREATE TABLE IF NOT EXISTS deletion_stats (
                    owner_id TEXT,
                    chat_id TEXT,
                    count INTEGER DEFAULT 0,
                    PRIMARY KEY (owner_id, chat_id)
                )
            """)
            conn.commit()

    async def _db_execute(self, query, params):
        try:
            async with asyncio.Lock():
                with sqlite3.connect(self.db_path) as conn:
                    conn.execute(query, params)
                    conn.commit()
            return True
        except Exception as e:
            print(f"خطای دیتابیس: {e}")
            return False

    async def _db_fetch(self, query, params):
        try:
            async with asyncio.Lock():
                with sqlite3.connect(self.db_path) as conn:
                    cursor = conn.execute(query, params)
                    return cursor.fetchall()
        except Exception as e:
            print(f"خطای دریافت از دیتابیس: {e}")
            return []

    async def _increment_deletion_count(self, chat_id):
        await self._db_execute(
            """
            INSERT INTO deletion_stats (owner_id, chat_id, count)
            VALUES (?, ?, 1)
            ON CONFLICT(owner_id, chat_id) DO UPDATE SET count = count + 1
            """,
            (self.owner_id, str(chat_id))
        )

    def _cache_message(self, message):
        """ذخیره پیام با آیدی چت برای بازیابی بعدی"""
        chat_id = str(message.chat_id)
        msg_id = message.id
        
        if chat_id not in self.message_cache:
            self.message_cache[chat_id] = {}
            
        self.message_cache[chat_id][msg_id] = {
            'text': message.text if hasattr(message, 'text') else None,
            'media': bool(message.media) if hasattr(message, 'media') else False,
            'sender_id': message.sender_id if hasattr(message, 'sender_id') else None,
            'date': message.date if hasattr(message, 'date') else datetime.now(),
            'chat_id': chat_id,
            'msg_id': msg_id,
            'raw': message  # ذخیره پیام خام برای استفاده بعدی
        }
        
        if len(self.message_cache[chat_id]) > self.max_messages_per_chat:
            oldest_key = next(iter(self.message_cache[chat_id]))
            del self.message_cache[chat_id][oldest_key]
            
    def _get_cached_messages_by_id(self, msg_ids):
        """یافتن پیام‌های ذخیره شده با آیدی در تمام چت‌ها"""
        results = []
        
        for chat_id, messages in self.message_cache.items():
            for msg_id in msg_ids:
                if msg_id in messages:
                    results.append((chat_id, messages[msg_id]))
                    
        return results

    async def handle_events(self):
        @self.client.on(events.NewMessage())
        async def cache_new_messages(event):
            chat_id = str(event.chat_id)
            
            is_tracked = await self._db_fetch(
                "SELECT 1 FROM tracked_chats WHERE owner_id = ? AND chat_id = ?",
                (self.owner_id, chat_id))
            
            if is_tracked:
                self._cache_message(event.message)
                print(f"پیام {event.id} از چت {chat_id} ذخیره شد")

        @self.client.on(events.MessageDeleted)
        async def message_deleted_handler(event):
            print(f"رویداد حذف پیام. آیدی‌ها: {event.deleted_ids}, آیدی چت: {event.chat_id}")
            
            log_channel = await self._db_fetch(
                "SELECT channel_id FROM log_channels WHERE owner_id = ?",
                (self.owner_id,))
            
            if not log_channel:
                print("کانال ذخیره تنظیم نشده است!")
                return
                
            log_channel_id = int(log_channel[0][0])
            
            try:
                channel_entity = await self.client.get_entity(log_channel_id)
                if not isinstance(channel_entity, (Channel, Chat)):
                    print("کانال ذخیره معتبر نیست!")
                    return
            except Exception as e:
                print(f"خطای دسترسی به کانال ذخیره: {e}")
                return
            
            chat_id = event.chat_id
            is_tracked = False
            
            if chat_id:
                is_tracked = await self._db_fetch(
                    "SELECT 1 FROM tracked_chats WHERE owner_id = ? AND chat_id = ?",
                    (self.owner_id, str(chat_id)))
                
                if not is_tracked:
                    print(f"چت {chat_id} تحت نظر نیست، نادیده گرفته شد")
                    return
            
            cached_msgs = self._get_cached_messages_by_id(event.deleted_ids)
            
            if not cached_msgs:
                print(f"پیامی با آیدی‌های {event.deleted_ids} یافت نشد")
                return
                
            for cached_chat_id, cached_msg in cached_msgs:
                if not chat_id:
                    is_tracked = await self._db_fetch(
                        "SELECT 1 FROM tracked_chats WHERE owner_id = ? AND chat_id = ?",
                        (self.owner_id, cached_chat_id))
                    
                    if not is_tracked:
                        print(f"پیام ذخیره شده یافت شد اما چت {cached_chat_id} تحت نظر نیست")
                        continue
                
                try:
                    try:
                        chat = await self.client.get_entity(int(cached_chat_id))
                        chat_title = getattr(chat, 'title', 'چت خصوصی')
                    except Exception as e:
                        print(f"خطای دریافت اطلاعات چت: {e}")
                        chat_title = f"آیدی چت: {cached_chat_id}"
                    
                    sender_name = "ناشناس"
                    sender_username = None
                    sender_id = cached_msg.get('sender_id', 'ناشناس')
                    
                    if sender_id and sender_id != 'ناشناس':
                        try:
                            sender = await self.client.get_entity(sender_id)
                            sender_name = getattr(sender, 'first_name', '') or getattr(sender, 'title', '') or "ناشناس"
                            sender_username = getattr(sender, 'username', None)
                        except Exception as e:
                            print(f"خطای دریافت اطلاعات ارسال کننده: {e}")
                    
                    chat_id_str = cached_chat_id.replace('-100', '') if cached_chat_id.startswith('-100') else cached_chat_id
                    message_link = f"https://t.me/c/{chat_id_str}/{cached_msg['msg_id']}" if cached_chat_id.startswith('-100') else "ناموجود"
                    
                    log_text = (
                        f"⚠️ **پیام حذف شده** ⚠️\n\n"
                        f"🗓 **تاریخ**: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n"
                        f"💬 **چت**: {chat_title}\n"
                        f"👤 **ارسال کننده**: {sender_name}"
                        f" ({f'@{sender_username}' if sender_username else 'بدون نام کاربری'})\n"
                        f"🆔 **آیدی کاربر**: `{sender_id}`\n"
                        f"🔗 **لینک پیام**: {message_link}\n\n"
                        f"📝 **محتوا**:\n"
                    )
                    
                    if cached_msg.get('text'):
                        log_text += f"`{cached_msg['text']}`\n"
                    elif cached_msg.get('media'):
                        log_text += "`[پیام رسانه]`\n"
                    else:
                        log_text += "`[نوع پیام پشتیبانی نمی‌شود]`\n"
                    
                    try:
                        await self.client.send_message(
                            log_channel_id,
                            log_text,
                            link_preview=False
                        )
                        
                        raw_msg = cached_msg.get('raw')
                        if raw_msg and hasattr(raw_msg, 'media') and raw_msg.media:
                            try:
                                media = raw_msg.media
                                if hasattr(media, 'document'):
                                    await self.client.send_file(
                                        log_channel_id,
                                        file=media,
                                        caption=f"📁 رسانه از پیام حذف شده در {chat_title}\n"
                                                f"آیدی فایل: {media.document.id}\n"
                                                f"هش دسترسی: {media.document.access_hash}"
                                    )
                                elif hasattr(media, 'photo'):
                                    await self.client.send_file(
                                        log_channel_id,
                                        file=media,
                                        caption=f"🖼 عکس از پیام حذف شده در {chat_title}\n"
                                                f"آیدی عکس: {media.photo.id}\n"
                                                f"هش دسترسی: {media.photo.access_hash}"
                                    )
                            except Exception as e:
                                print(f"خطای ارسال رسانه: {e}")
                        
                        await self._increment_deletion_count(cached_chat_id)
                        print(f"پیام حذف شده {cached_msg['msg_id']} از {chat_title} با موفقیت ذخیره شد")
                    except Exception as e:
                        print(f"خطای ارسال به کانال ذخیره: {e}")
                
                except Exception as e:
                    print(f"خطای پردازش پیام ذخیره شده: {e}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/trackchat|ردیاب چت)$'))
        async def track_chat_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            chat_id = str(event.chat_id)
            
            try:
                await self._db_execute(
                    "INSERT OR IGNORE INTO tracked_chats (owner_id, chat_id) VALUES (?, ?)",
                    (self.owner_id, chat_id))
                await event.reply(f"✅ این چت اکنون تحت نظر است و پیام‌های حذف شده ذخیره می‌شوند")
                print(f"چت {chat_id} به لیست چت‌های تحت نظر اضافه شد")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/untrackchat|عدم ردیابی چت)$'))
        async def untrack_chat_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            chat_id = str(event.chat_id)
            
            try:
                await self._db_execute(
                    "DELETE FROM tracked_chats WHERE owner_id = ? AND chat_id = ?",
                    (self.owner_id, chat_id))
                
                if chat_id in self.message_cache:
                    del self.message_cache[chat_id]
                    
                await event.reply(f"✅ این چت دیگر تحت نظر نیست")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^(?:/setlogchannel|تنظیم کانال ذخیره)(?:\s+(.+))?$'))
        async def set_log_channel_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            target = event.pattern_match.group(1)
            if not target and not event.is_reply:
                await event.reply("❌ لطفا یک کانال (نام کاربری یا آیدی) مشخص کنید یا به پیامی در کانال ریپلای کنید")
                return

            try:
                if target:
                    if target.startswith('-100') and target[1:].isdigit():
                        channel = int(target)
                    else:
                        channel = target
                else:
                    reply = await event.get_reply_message()
                    channel = reply.chat_id

                try:
                    channel_obj = await self.client.get_entity(channel)
                    if not isinstance(channel_obj, (Channel, Chat)):
                        await event.reply("❌ باید یک کانال/سوپرگروه باشد، نه یک کاربر!")
                        return
                        
                    channel_id = channel_obj.id
                    channel_title = getattr(channel_obj, 'title', 'کانال خصوصی')
                except Exception as e:
                    await event.reply(f"❌ خطای دریافت اطلاعات کانال: {str(e)}")
                    return
                
                success = await self._db_execute(
                    """
                    INSERT INTO log_channels (owner_id, channel_id)
                    VALUES (?, ?)
                    ON CONFLICT(owner_id) DO UPDATE SET channel_id = ?
                    """,
                    (self.owner_id, str(channel_id), str(channel_id)))
                
                if success:
                    await event.reply(f"✅ کانال ذخیره تنظیم شد به: {channel_title} (آیدی: {channel_id})")
                    print(f"کانال ذخیره به {channel_id} تنظیم شد")
                    
                    try:
                        test_msg = await self.client.send_message(
                            channel_id,
                            "✅ این کانال به عنوان کانال ذخیره پیام‌های حذف شده تنظیم شد."
                        )
                        print(f"پیام تست به کانال ذخیره {channel_id} ارسال شد")
                    except Exception as e:
                        await event.reply(f"⚠️ کانال ذخیره تنظیم شد اما ارسال پیام تست ناموفق بود: {str(e)}\n"
                                        "مطمئن شوید ربات مجوز ارسال پیام در این کانال را دارد.")
                else:
                    await event.reply("❌ خطای به‌روزرسانی دیتابیس. لطفا دوباره تلاش کنید.")
                
            except ValueError:
                await event.reply("❌ فرمت آیدی کانال نامعتبر است. سوپرگروه‌ها/کانال‌ها باید با -100 شروع شوند")
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}\n\n"
                                "راه‌حل‌های ممکن:\n"
                                "1. ابتدا ربات را به عنوان ادمین به کانال اضافه کنید\n"
                                "2. از نام کاربری کانال استفاده کنید (@کانال)\n"
                                "3. برای کانال‌های خصوصی، از آیدی عددی کامل استفاده کنید (100123...-)")
                
        @self.client.on(events.NewMessage(pattern=r'^(?:/trackedchats|نمایش چت‌های ردیابی)$'))
        async def tracked_chats_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            chats = await self._db_fetch(
                "SELECT chat_id FROM tracked_chats WHERE owner_id = ?",
                (self.owner_id,))
            
            if not chats:
                await event.reply("❌ هیچ چتی در حال حاضر تحت نظر نیست")
                return

            response = "📋 **چت‌های تحت نظر**:\n\n"
            for chat in chats:
                try:
                    chat_entity = await self.client.get_entity(int(chat[0]))
                    chat_name = getattr(chat_entity, 'title', 'چت خصوصی')
                    response += f"• {chat_name} (آیدی: `{chat[0]}`)\n"
                except:
                    response += f"• چت ناشناس (آیدی: `{chat[0]}`)\n"
            
            await event.reply(response)

        @self.client.on(events.NewMessage(pattern=r'^(?:/deletionstats|آمار حذف پیام)$'))
        async def deletion_stats_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            stats = await self._db_fetch(
                """
                SELECT chat_id, count 
                FROM deletion_stats 
                WHERE owner_id = ?
                ORDER BY count DESC
                """,
                (self.owner_id,))
            
            if not stats:
                await event.reply("❌ هیچ آماری از حذف پیام موجود نیست")
                return

            response = "📊 **آمار حذف پیام‌ها**:\n\n"
            for stat in stats:
                try:
                    chat_entity = await self.client.get_entity(int(stat[0]))
                    chat_name = getattr(chat_entity, 'title', 'چت خصوصی')
                    response += f"• {chat_name}: `{stat[1]}` حذف\n"
                except:
                    response += f"• چت ناشناس (آیدی: `{stat[0]}`): `{stat[1]}` حذف\n"
            
            await event.reply(response)

        @self.client.on(events.NewMessage(pattern=r'^(?:/deletestatus|وضعیت حذف)$'))
        async def delete_status_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            log_channel = await self._db_fetch(
                "SELECT channel_id FROM log_channels WHERE owner_id = ?",
                (self.owner_id,))
            
            tracked_chats = await self._db_fetch(
                "SELECT COUNT(*) FROM tracked_chats WHERE owner_id = ?",
                (self.owner_id,))
            
            total_deletions = await self._db_fetch(
                """
                SELECT SUM(count) 
                FROM deletion_stats 
                WHERE owner_id = ?
                """,
                (self.owner_id,))
            
            cache_info = {}
            for chat_id in self.message_cache:
                cache_info[chat_id] = len(self.message_cache[chat_id])
                
            status = "📊 **وضعیت ردیاب حذف پیام‌ها**\n\n"
            
            if log_channel:
                try:
                    channel_obj = await self.client.get_entity(int(log_channel[0][0]))
                    channel_name = getattr(channel_obj, 'title', 'ناشناس')
                    status += f"📣 **کانال ذخیره**: {channel_name} (`{log_channel[0][0]}`)\n"
                except:
                    status += f"📣 **کانال ذخیره**: آیدی `{log_channel[0][0]}` (عدم امکان دریافت اطلاعات)\n"
            else:
                status += "📣 **کانال ذخیره**: تنظیم نشده\n"
                
            status += f"👁️ **چت‌های تحت نظر**: {tracked_chats[0][0] if tracked_chats else 0}\n"
            status += f"🗑️ **تعداد کل حذف‌ها**: {total_deletions[0][0] if total_deletions and total_deletions[0][0] else 0}\n"
            
            total_cached = sum(cache_info.values())
            status += f"💾 **پیام‌های ذخیره شده**: {total_cached} در {len(cache_info)} چت\n\n"
            
            status += "🔍 **اطلاعات دیباگ**:\n"
            status += f"ربات در حال اجرا، ردیابی فعال: {'بله' if self.client else 'خیر'}\n"
            status += f"فایل دیتابیس: {os.path.exists(self.db_path)}\n"
            
            await event.reply(status)
            
        @self.client.on(events.NewMessage(pattern=r'^(?:/testdelete|تست حذف پیام)$'))
        async def test_delete_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            is_tracked = await self._db_fetch(
                "SELECT 1 FROM tracked_chats WHERE owner_id = ? AND chat_id = ?",
                (self.owner_id, str(event.chat_id)))
            
            if not is_tracked:
                await event.reply("❌ این چت تحت نظر نیست. ابتدا `/trackchat` یا `ردیاب چت` را اجرا کنید.")
                return
                
            log_channel = await self._db_fetch(
                "SELECT channel_id FROM log_channels WHERE owner_id = ?",
                (self.owner_id,))
            
            if not log_channel:
                await event.reply("❌ کانال ذخیره تنظیم نشده. ابتدا `/setlogchannel` یا `تنظیم کانال ذخیره` را اجرا کنید.")
                return
                
            test_msg = await event.reply("🧪 این یک پیام تست است که در 3 ثانیه حذف خواهد شد.")
            
            self._cache_message(test_msg)
            
            await asyncio.sleep(3)
            await test_msg.delete()
            
            await event.reply("✅ پیام تست حذف شد. کانال ذخیره خود را بررسی کنید.")
