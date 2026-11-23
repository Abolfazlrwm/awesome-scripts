# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/ephemeral_media.py
from .base import Plugin
from telethon import events
from telethon.tl.types import MessageMediaPhoto
import io
import os
import sqlite3

HELP = """  
⏳ **مدیریت رسانه‌های یکبار مصرف** ⏳  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• ذخیره رسانه‌های خودنابودشونده (View-Once)  
• پشتیبانی از عکس‌ها و ویدیوهای محافظت شده  
• دور زدن محدودیت زمان نمایش رسانه‌ها  
• ارسال مجدد رسانه با فرمت اصلی  
• حالت اتوماتیک برای ذخیره خودکار رسانه‌های یکبار مصرف  
• تنظیمات مجزا برای هر کاربر  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:

**انگلیسی:**
  `/dlx` ➤ (با ریپلای) ذخیره و نمایش رسانه یکبار مصرف  
  `/dlx_auto on|off` ➤ فعال/غیرفعال کردن ذخیره خودکار رسانه‌های یکبار مصرف  
  `/dlx_status` ➤ نمایش وضعیت فعلی حالت اتوماتیک  

**فارسی:**
  `ذخیره رسانه` ➤ (با ریپلای) ذخیره و نمایش رسانه یکبار مصرف  
  `ذخیره خودکار رسانه روشن|خاموش` ➤ فعال/غیرفعال کردن ذخیره خودکار  
  `وضعیت ذخیره رسانه` ➤ نمایش وضعیت فعلی حالت اتوماتیک  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. ریپلای روی رسانه View-Once و ارسال دستور:  
   `/dlx` یا `ذخیره رسانه`  
2. برای تغییر حالت ذخیره خودکار:  
   `/dlx_auto on` یا `ذخیره خودکار رسانه روشن`  
   `/dlx_auto off` یا `ذخیره خودکار رسانه خاموش`  
3. بررسی وضعیت:  
   `/dlx_status` یا `وضعیت ذخیره رسانه`  

⚠️ **ملاحظات مهم**:  
- فقط برای رسانه‌های با قابلیت خودنابودی کار می‌کند  
- در صورت فعال بودن حفاظت‌های پیشرفته ممکن است عمل نکند  
- حتما باید روی پیام مورد نظر ریپلای شود  
- کیفیت رسانه ذخیره شده ممکن است کاهش یابد  
- در حالت اتوماتیک، تمام رسانه‌های یکبار مصرف به پیام‌های ذخیره شده ارسال می‌شوند  
"""

class EphemeralMediaPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.db_path = os.path.join("data", "ephemeral_settings.db")
        self._init_db()
        
    def _init_db(self):
        """Initialize the SQLite database"""
        try:
            # Make sure the data directory exists
            os.makedirs(os.path.dirname(self.db_path), exist_ok=True)
            
            # Connect to SQLite database (will create if not exists)
            conn = sqlite3.connect(self.db_path)
            cursor = conn.cursor()
            
            # Create settings table if not exists
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS ephemeral_settings (
                    user_id TEXT PRIMARY KEY,
                    auto_save INTEGER DEFAULT 0
                )
            ''')
            
            conn.commit()
            conn.close()
        except Exception as e:
            print(f"Database initialization error: {e}")
    
    def _get_auto_save_status(self, user_id):
        """Get auto-save status for a specific user"""
        try:
            conn = sqlite3.connect(self.db_path)
            cursor = conn.cursor()
            
            cursor.execute("SELECT auto_save FROM ephemeral_settings WHERE user_id=?", (user_id,))
            result = cursor.fetchone()
            
            conn.close()
            
            if result:
                return bool(result[0])
            return False
        except Exception as e:
            print(f"Error getting auto-save status: {e}")
            return False
    
    def _set_auto_save_status(self, user_id, status):
        """Set auto-save status for a specific user"""
        try:
            conn = sqlite3.connect(self.db_path)
            cursor = conn.cursor()
            
            cursor.execute('''
                INSERT OR REPLACE INTO ephemeral_settings (user_id, auto_save)
                VALUES (?, ?)
            ''', (user_id, int(status)))
            
            conn.commit()
            conn.close()
            return True
        except Exception as e:
            print(f"Error setting auto-save status: {e}")
            return False

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/dlx|ذخیره\s+رسانه)$'))
        async def handle_ephemeral_media(event):
            # Owner verification
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_reply:
                await event.reply("❌ برای مشاهده رسانه یکبار مصرف پاسخ دهید")
                return

            reply_message = await event.get_reply_message()
            
            if (reply_message.media and 
                hasattr(reply_message.media, 'ttl_seconds') and 
                reply_message.media.ttl_seconds is not None):

                try:
                    media_bytes = await reply_message.download_media(bytes, thumb=-1)
                    
                    if media_bytes:
                        file = io.BytesIO(media_bytes)
                        file.name = "saved_media.jpg"
                        await event.reply("🖼 رسانه ذخیره شده:", file=file)
                        file.close()
                    else:
                        await event.reply("❌ ذخیره رسانه ناموفق بود")
                except Exception as e:
                    await event.reply(f"❌ حفاظت از رسانه فعال است")
            
            else:
                await event.reply("❌ این رسانه یکبار مصرف نیست")
        
        @self.client.on(events.NewMessage(pattern=r'^(?:/dlx_auto|ذخیره\s+خودکار\s+رسانه)\s+(on|off|روشن|خاموش)$'))
        async def toggle_auto_save(event):
            # Owner verification
            if str(event.sender_id) != self.owner_id:
                return
                
            option = event.pattern_match.group(1).lower()
            user_id = str(event.sender_id)
            
            # Normalize Farsi responses
            if option in ['روشن']:
                option = "on"
            elif option in ['خاموش']:
                option = "off"
            
            if option == "on":
                success = self._set_auto_save_status(user_id, True)
                if success:
                    await event.reply("✅ حالت ذخیره خودکار رسانه‌های یکبار مصرف فعال شد")
                else:
                    await event.reply("❌ خطا در ذخیره تنظیمات")
            else:
                success = self._set_auto_save_status(user_id, False)
                if success:
                    await event.reply("❌ حالت ذخیره خودکار رسانه‌های یکبار مصرف غیرفعال شد")
                else:
                    await event.reply("❌ خطا در ذخیره تنظیمات")
        
        @self.client.on(events.NewMessage(pattern=r'^(?:/dlx_status|وضعیت\s+ذخیره\s+رسانه)$'))
        async def check_status(event):
            # Owner verification
            if str(event.sender_id) != self.owner_id:
                return
                
            user_id = str(event.sender_id)
            status = "✅ فعال" if self._get_auto_save_status(user_id) else "❌ غیرفعال"
            await event.reply(f"🔄 وضعیت فعلی ذخیره خودکار: {status}")
        
        @self.client.on(events.NewMessage)
        async def auto_save_ephemeral_media(event):
            # Process auto-save only in private chats
            if not event.is_private:
                return
                
            # Get the user's settings (using owner_id)
            user_id = str(self.owner_id)
            auto_save_enabled = self._get_auto_save_status(user_id)
            
            if not auto_save_enabled:
                return
                
            if (event.media and 
                hasattr(event.media, 'ttl_seconds') and 
                event.media.ttl_seconds is not None):
                
                try:
                    media_bytes = await event.download_media(bytes, thumb=-1)
                    
                    if media_bytes:
                        file = io.BytesIO(media_bytes)
                        file.name = "saved_media.jpg"
                        await self.client.send_file('me', file, caption="🔄 رسانه یکبار مصرف ذخیره شده اتوماتیک")
                        file.close()
                except Exception as e:
                    print(f"Error auto-saving media: {e}")
 