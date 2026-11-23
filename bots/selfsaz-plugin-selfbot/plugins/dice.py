# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
from telethon.tl.types import MessageMediaDice, InputMediaDice
import asyncio
import random
import sqlite3
import os
from datetime import datetime, timedelta

HELP = """
🎲 **کنترلر تاس و انیمیشن** 🎲

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **امکانات**:
• ارسال تاس با امتیاز دلخواه (🎲, 🎯, 🏀, ⚽, 🎰, 🎳)
• پیش‌بینی نتیجه تاس دیگران (روشن/خاموش)
• کنترل دقیق مقدار انیمیشن‌ها
• تنظیم ظاهر انیمیشن‌ها
• دور زدن نتایج تصادفی (برای نیازهای خاص)

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **دستورات**:

**انگلیسی:**
  `/dice [value]` ➤ ارسال تاس با مقدار مشخص (1-6)
  `/dart [value]` ➤ ارسال دارت با امتیاز مشخص (1-6)
  `/basketball [value]` ➤ ارسال بسکتبال با امتیاز (1-5)
  `/football` ➤ ارسال انیمیشن فوتبال
  `/slot [value]` ➤ ارسال ماشین اسلات (1-64)
  `/bowling [value]` ➤ ارسال بولینگ (1-10)
  `/predict on|off` ➤ روشن/غیرفعال کردن پیش‌بینی تاس

**فارسی:**
  `تاس [عدد]` ➤ ارسال تاس با مقدار مشخص (1-6)
  `دارت [عدد]` ➤ ارسال دارت با امتیاز مشخص (1-6)
  `بسکتبال [عدد]` ➤ ارسال بسکتبال با امتیاز (1-5)
  `فوتبال` ➤ ارسال انیمیشن فوتبال
  `ماشین اسلات [عدد]` ➤ ارسال ماشین اسلات (1-64)
  `بولینگ [عدد]` ➤ ارسال بولینگ (1-10)
  `پیش‌بینی تاس روشن|خاموش` ➤ روشن/غیرفعال کردن پیش‌بینی تاس

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **نمونه‌ها**:
1. `/dice 6` یا `تاس 6` - ارسال 🎲 با عدد 6  
2. `/predict on` یا `پیش‌بینی تاس روشن` - فعال کردن پیش‌بینی تاس  
3. `/slot 64` یا `ماشین اسلات 64` - ارسال 🎰 با جکپات

⚠️ **توجه**:
- فقط در چت‌هایی کار می‌کند که تلگرام انیمیشن‌ها را اجازه می‌دهد
- برخی مقادیر ممکن است توسط محدودیت‌های تلگرام قطع شوند
- پیش‌بینی فقط برای مالک ربات فعال است
"""

class DicePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.emoji_map = {
            'dice': '🎲',
            'dart': '🎯',
            'basketball': '🏀',
            'football': '⚽',
            'slot': '🎰',
            'bowling': '🎳'
        }
        self.value_limits = {
            'dice': (1, 6),
            'dart': (1, 6),
            'basketball': (1, 5),
            'football': (1, 5),
            'slot': (1, 64),
            'bowling': (1, 10)
        }
        
        # Initialize database
        os.makedirs('data', exist_ok=True)
        self.db_path = os.path.join('data', 'dice_predict.db')
        self._init_db()

    def _init_db(self):
        with sqlite3.connect(self.db_path) as conn:
            # User activity table for spam protection
            conn.execute('''
                CREATE TABLE IF NOT EXISTS user_activity (
                    user_id INTEGER PRIMARY KEY,
                    last_prediction_time TEXT,
                    prediction_count INTEGER DEFAULT 0
                )
            ''')
            
            # Owner settings table for individual configurations
            conn.execute('''
                CREATE TABLE IF NOT EXISTS owner_settings (
                    owner_id INTEGER PRIMARY KEY,
                    predict_enabled INTEGER DEFAULT 0
                )
            ''')
            
            # Insert default settings for the owner if not exists
            conn.execute('''
                INSERT OR IGNORE INTO owner_settings (owner_id, predict_enabled)
                VALUES (?, ?)
            ''', (self.owner_id, 0))
            
            conn.commit()

    def _get_owner_settings(self, owner_id):
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.execute('''
                SELECT predict_enabled FROM owner_settings WHERE owner_id = ?
            ''', (owner_id,))
            row = cursor.fetchone()
            return {'predict_enabled': bool(row[0])} if row else {'predict_enabled': False}

    def _update_owner_settings(self, owner_id, predict_enabled):
        with sqlite3.connect(self.db_path) as conn:
            conn.execute('''
                INSERT OR REPLACE INTO owner_settings (owner_id, predict_enabled)
                VALUES (?, ?)
            ''', (owner_id, int(predict_enabled)))
            conn.commit()

    def _update_user_activity(self, user_id):
        with sqlite3.connect(self.db_path) as conn:
            now = datetime.now().isoformat()
            conn.execute('''
                INSERT OR REPLACE INTO user_activity 
                (user_id, last_prediction_time, prediction_count)
                VALUES (?, ?, COALESCE(
                    (SELECT prediction_count FROM user_activity WHERE user_id = ?) + 1,
                    1
                ))
            ''', (user_id, now, user_id))
            conn.commit()

    def _should_respond(self, user_id):
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.execute('''
                SELECT last_prediction_time, prediction_count 
                FROM user_activity 
                WHERE user_id = ?
            ''', (user_id,))
            row = cursor.fetchone()
            
            if not row:
                return True
                
            last_time, count = datetime.fromisoformat(row[0]), row[1]
            time_since_last = datetime.now() - last_time
            
            # Anti-spam logic:
            # - If more than 5 predictions in last minute, respond only 30% of the time
            # - Otherwise respond normally
            if count > 5 and time_since_last < timedelta(minutes=1):
                return random.random() < 0.3
            return True

    async def send_animation(self, event, emoji, desired_value=None):
        """تابع کمکی برای ارسال انیمیشن‌ها با منطق تلاش مجدد"""
        max_attempts = 100  # محدودیت ایمنی
        attempts = 0

        try:
            msg = await self.client.send_file(event.chat_id, InputMediaDice(emoji))
            if not hasattr(msg.media, 'value'):
                raise ValueError("تلگرام انیمیشن تاس را برنگرداند")

            result = msg.media.value
            while desired_value is not None and result != desired_value and attempts < max_attempts:
                attempts += 1
                await msg.delete()
                await asyncio.sleep(0.5)  # تاخیر کوتاه بین تلاش‌ها

                msg = await self.client.send_file(event.chat_id, InputMediaDice(emoji))
                if not hasattr(msg.media, 'value'):
                    raise ValueError("تلگرام انیمیشن تاس را برنگرداند")
                result = msg.media.value

            return msg, result, attempts

        except Exception as e:
            await event.reply(f"❌ خطا در ارسال انیمیشن: {str(e)}")
            raise

    async def handle_events(self):
        # Command handler for dice, dart, basketball, football, slot, bowling
        @self.client.on(events.NewMessage(pattern=r'^(?:/(dice|dart|basketball|football|slot|bowling)|(?:تاس|دارت|بسکتبال|فوتبال|ماشین اسلات|بولینگ))(?:\s+(\d+))?$'))
        async def dice_handler(event):
            # Accept commands only from the owner
            if str(event.sender_id) != self.owner_id:
                return
            try:
                # Determine which command was used (English or Farsi)
                command = event.pattern_match.group(1)
                if command is None:
                    # If group 1 is None then Farsi command was used.
                    # Map Farsi command to internal command name:
                    full_text = event.message.text.strip().split()[0]
                    mapping = {
                        'تاس': 'dice',
                        'دارت': 'dart',
                        'بسکتبال': 'basketball',
                        'فوتبال': 'football',
                        'ماشین اسلات': 'slot',
                        'بولینگ': 'bowling'
                    }
                    command = mapping.get(full_text, 'dice')
                value_input = event.pattern_match.group(2)
                emoji = self.emoji_map[command]
                min_val, max_val = self.value_limits.get(command, (1, 6))

                if command == 'football':
                    await self.client.send_file(event.chat_id, InputMediaDice(emoji))
                    return

                try:
                    desired_value = int(value_input) if value_input else max_val
                    desired_value = max(min(desired_value, max_val), min_val)
                except ValueError:
                    desired_value = max_val

                msg, result, attempts = await self.send_animation(event, emoji, desired_value)

                if desired_value is None or result == desired_value:
                    await event.reply(f"✅ موفقیت! {emoji} = {result} (بعد از {attempts} تلاش)")
                else:
                    await event.reply(f"⚠️ نتوانستیم {desired_value} را دریافت کنیم، اما {result} دریافت شد")

            except Exception as e:
                await event.reply(f"❌ خطا در پردازش دستور: {str(e)}")
                raise

        # Prediction command handler (for toggling prediction feature)
        @self.client.on(events.NewMessage(pattern=r'^(?:/predict|پیش‌بینی تاس)\s+(on|off|روشن|خاموش)$'))
        async def predict_handler(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            state = event.pattern_match.group(1).lower()
            # Normalize the state value: map 'روشن' to 'on' and 'خاموش' to 'off'
            if state == 'روشن':
                state = 'on'
            elif state == 'خاموش':
                state = 'off'
            predict_enabled = (state == 'on')
            self._update_owner_settings(sender_id, predict_enabled)
            status = "فعال" if predict_enabled else "غیرفعال"
            await event.reply(f"حالت پیش‌بینی تاس {status} شد.")

        @self.client.on(events.NewMessage(pattern=r'^/animate$'))
        async def animate_help(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            await event.reply(HELP)
