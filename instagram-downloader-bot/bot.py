import telebot
import requests
import sqlite3
import random
import time
import os
import json
from datetime import datetime, timedelta
from telebot import types
import logging

# ==================== تنظیمات لاگینگ ====================
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

print("=" * 60)
print("🤖 ربات دانلودر اینستاگرام - نسخه VIP v4.0")
print("=" * 60)

# 🔐 اطلاعات ربات
BOT_TOKEN = "توکن ربات خودتو اینجا بزار"
ADMIN_ID = 123456789  # آیدی عددی خودتو اینجا بزار
FAST_CREAT_TOKEN = "Api خودتو اینجا بزار "
SUPPORT_USERNAME = "@YourSupportUsername"
CHANNEL_USERNAME = "@YourChannelUsername"
CHANNEL_LINK = "@YourChannelUsername"

# 📊 دیتابیس
DB_NAME = "instagram_bot.db"

# ==================== سیستم دیتابیس پیشرفته ====================
class Database:
    def __init__(self):
        try:
            self.conn = sqlite3.connect(DB_NAME, check_same_thread=False)
            self.conn.execute("PRAGMA foreign_keys = ON")
            self.create_tables()
            self.migrate_tables()
            logger.info("✅ پایگاه داده با موفقیت ایجاد شد")
        except Exception as e:
            logger.error(f"❌ خطا در ایجاد پایگاه داده: {e}")
    
    def create_tables(self):
        cursor = self.conn.cursor()
        
        # جدول کاربران با ستون VIP
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                user_id INTEGER PRIMARY KEY,
                username TEXT,
                first_name TEXT,
                last_name TEXT,
                join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                daily_downloads INTEGER DEFAULT 0,
                last_download_date DATE DEFAULT NULL,
                total_downloads INTEGER DEFAULT 0,
                invite_code TEXT,
                invited_by INTEGER DEFAULT 0,
                invite_count INTEGER DEFAULT 0,
                extra_downloads INTEGER DEFAULT 0,
                is_banned INTEGER DEFAULT 0,
                is_vip INTEGER DEFAULT 0,
                vip_until DATE DEFAULT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                url TEXT,
                type TEXT,
                date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                success INTEGER DEFAULT 1,
                response_time REAL
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS required_channels (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                channel_id TEXT,
                channel_username TEXT UNIQUE,
                channel_link TEXT,
                added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_active INTEGER DEFAULT 1
            )
        ''')
        
        self.conn.commit()
    
    def migrate_tables(self):
        """مهاجرت جدول برای کاربران قدیمی"""
        try:
            cursor = self.conn.cursor()
            cursor.execute("PRAGMA table_info(users)")
            columns = [col[1] for col in cursor.fetchall()]
            
            # اضافه کردن ستون‌های جدید اگر وجود ندارند
            if 'is_vip' not in columns:
                cursor.execute('ALTER TABLE users ADD COLUMN is_vip INTEGER DEFAULT 0')
            if 'vip_until' not in columns:
                cursor.execute('ALTER TABLE users ADD COLUMN vip_until DATE DEFAULT NULL')
            if 'invite_code' not in columns:
                cursor.execute('ALTER TABLE users ADD COLUMN invite_code TEXT')
            
            # ایجاد invite_code برای کاربران قدیمی
            cursor.execute('SELECT user_id FROM users WHERE invite_code IS NULL OR invite_code = ""')
            users_without_code = cursor.fetchall()
            
            for user in users_without_code:
                user_id = user[0]
                new_invite_code = f"INV{user_id}{random.randint(1000, 9999)}"
                cursor.execute('UPDATE users SET invite_code = ? WHERE user_id = ?', 
                             (new_invite_code, user_id))
            
            # ادمین اصلی همیشه VIP باشد
            cursor.execute('UPDATE users SET is_vip = 1 WHERE user_id = ?', (ADMIN_ID,))
            
            self.conn.commit()
            logger.info(f"✅ مهاجرت دیتابیس انجام شد")
        except Exception as e:
            logger.error(f"⚠️ خطا در مهاجرت دیتابیس: {e}")
    
    def add_or_update_user(self, user_id, username, first_name, last_name):
        """اضافه کردن کاربر جدید یا به‌روزرسانی کاربر موجود"""
        try:
            cursor = self.conn.cursor()
            cursor.execute('SELECT user_id FROM users WHERE user_id = ?', (user_id,))
            existing = cursor.fetchone()
            
            if not existing:
                # کاربر جدید
                invite_code = f"INV{user_id}{random.randint(1000, 9999)}"
                is_vip = 1 if user_id == ADMIN_ID else 0
                
                cursor.execute('''
                    INSERT INTO users (user_id, username, first_name, last_name, invite_code, is_vip)
                    VALUES (?, ?, ?, ?, ?, ?)
                ''', (user_id, username or "", first_name or "", last_name or "", invite_code, is_vip))
                self.conn.commit()
                logger.info(f"✅ کاربر جدید اضافه شد: {user_id}")
                return True, "new"
            else:
                # کاربر موجود - به‌روزرسانی اطلاعات
                cursor.execute('''
                    UPDATE users 
                    SET username = ?, first_name = ?, last_name = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE user_id = ?
                ''', (username or "", first_name or "", last_name or "", user_id))
                
                # بررسی و ایجاد invite_code اگر وجود ندارد
                cursor.execute('SELECT invite_code FROM users WHERE user_id = ?', (user_id,))
                user_data = cursor.fetchone()
                
                if user_data and (not user_data[0] or user_data[0] == ""):
                    invite_code = f"INV{user_id}{random.randint(1000, 9999)}"
                    cursor.execute('UPDATE users SET invite_code = ? WHERE user_id = ?', (invite_code, user_id))
                
                self.conn.commit()
                logger.info(f"✅ کاربر به‌روز شد: {user_id}")
                return False, "updated"
        except Exception as e:
            logger.error(f"❌ خطا در افزودن/به‌روزرسانی کاربر: {e}")
            return False, "error"
    
    def is_vip(self, user_id):
        """بررسی VIP بودن کاربر"""
        try:
            # ادمین اصلی همیشه VIP است
            if user_id == ADMIN_ID:
                return True
            
            cursor = self.conn.cursor()
            cursor.execute('SELECT is_vip, vip_until FROM users WHERE user_id = ?', (user_id,))
            result = cursor.fetchone()
            
            if result:
                is_vip = result[0]
                vip_until = result[1]
                
                if is_vip == 1:
                    # بررسی تاریخ انقضای VIP
                    if vip_until:
                        try:
                            vip_date = datetime.strptime(vip_until, '%Y-%m-%d').date()
                            today = datetime.now().date()
                            if vip_date < today:
                                # VIP منقضی شده
                                cursor.execute('UPDATE users SET is_vip = 0, vip_until = NULL WHERE user_id = ?', (user_id,))
                                self.conn.commit()
                                return False
                        except:
                            pass
                    return True
            return False
        except:
            return False
    
    def set_vip(self, user_id, is_vip=True, days=None):
        """تنظیم وضعیت VIP کاربر"""
        try:
            cursor = self.conn.cursor()
            
            if is_vip:
                vip_until = None
                if days:
                    vip_until = (datetime.now() + timedelta(days=days)).strftime('%Y-%m-%d')
                
                cursor.execute('''
                    UPDATE users 
                    SET is_vip = 1, vip_until = ?
                    WHERE user_id = ?
                ''', (vip_until, user_id))
            else:
                cursor.execute('''
                    UPDATE users 
                    SET is_vip = 0, vip_until = NULL
                    WHERE user_id = ?
                ''', (user_id,))
            
            self.conn.commit()
            return True
        except Exception as e:
            logger.error(f"❌ خطا در تنظیم VIP: {e}")
            return False
    
    def get_vip_users(self):
        """دریافت لیست کاربران VIP"""
        cursor = self.conn.cursor()
        cursor.execute('SELECT user_id, username, first_name, vip_until FROM users WHERE is_vip = 1 ORDER BY vip_until DESC')
        return cursor.fetchall()
    
    def get_user_stats(self, user_id):
        """دریافت آمار کاربر با بررسی ریست روزانه"""
        try:
            cursor = self.conn.cursor()
            
            # ریست روزانه (فقط برای کاربران غیر VIP)
            today = datetime.now().strftime('%Y-%m-%d')
            cursor.execute('SELECT last_download_date, daily_downloads, is_vip FROM users WHERE user_id = ?', (user_id,))
            user_data = cursor.fetchone()
            
            if user_data and user_data[0] and not self.is_vip(user_id):
                last_date_str = user_data[0]
                if isinstance(last_date_str, str):
                    try:
                        last_date = datetime.strptime(last_date_str, '%Y-%m-%d').date()
                    except:
                        last_date = datetime.now().date()
                else:
                    last_date = last_date_str
                
                today_date = datetime.now().date()
                
                if last_date != today_date:
                    cursor.execute('''
                        UPDATE users 
                        SET daily_downloads = 0, 
                            last_download_date = ?
                        WHERE user_id = ?
                    ''', (today, user_id))
                    self.conn.commit()
            
            cursor.execute('SELECT * FROM users WHERE user_id = ?', (user_id,))
            return cursor.fetchone()
        except Exception as e:
            logger.error(f"❌ خطا در دریافت آمار کاربر: {e}")
            return None
    
    def get_today_downloads(self, user_id):
        """تعداد دانلودهای امروز کاربر"""
        try:
            user_data = self.get_user_stats(user_id)
            if user_data:
                return user_data[5] or 0
            return 0
        except:
            return 0
    
    def can_download(self, user_id):
        """بررسی امکان دانلود"""
        try:
            # کاربران VIP و ادمین محدودیتی ندارند
            if self.is_vip(user_id):
                return True
            
            current_downloads = self.get_today_downloads(user_id)
            cursor = self.conn.cursor()
            cursor.execute('SELECT extra_downloads FROM users WHERE user_id = ?', (user_id,))
            extra = cursor.fetchone()
            extra_downloads = extra[0] if extra else 0
            
            total_allowed = 5 + extra_downloads
            return current_downloads < total_allowed
        except:
            return False
    
    def increment_download(self, user_id):
        """افزایش تعداد دانلودهای کاربر"""
        try:
            cursor = self.conn.cursor()
            today = datetime.now().strftime('%Y-%m-%d')
            
            # کاربران VIP نیازی به ثبت daily_downloads ندارند
            if not self.is_vip(user_id):
                cursor.execute('SELECT last_download_date FROM users WHERE user_id = ?', (user_id,))
                result = cursor.fetchone()
                
                if result and result[0]:
                    last_date_str = result[0]
                    if isinstance(last_date_str, str):
                        try:
                            last_date = datetime.strptime(last_date_str, '%Y-%m-%d').date()
                        except:
                            last_date = datetime.now().date()
                    else:
                        last_date = result[0]
                    
                    today_date = datetime.now().date()
                    
                    if last_date != today_date:
                        cursor.execute('''
                            UPDATE users 
                            SET daily_downloads = 1, 
                                last_download_date = ?,
                                total_downloads = total_downloads + 1
                            WHERE user_id = ?
                        ''', (today, user_id))
                    else:
                        cursor.execute('''
                            UPDATE users 
                            SET daily_downloads = daily_downloads + 1,
                                total_downloads = total_downloads + 1
                            WHERE user_id = ?
                        ''', (user_id,))
                else:
                    cursor.execute('''
                        UPDATE users 
                        SET daily_downloads = 1, 
                            last_download_date = ?,
                            total_downloads = total_downloads + 1
                        WHERE user_id = ?
                    ''', (today, user_id))
            else:
                # فقط total_downloads افزایش می‌یابد
                cursor.execute('''
                    UPDATE users 
                    SET total_downloads = total_downloads + 1
                    WHERE user_id = ?
                ''', (user_id,))
            
            self.conn.commit()
            return True
        except Exception as e:
            logger.error(f"❌ خطا در افزایش دانلود: {e}")
            return False
    
    def get_remaining_downloads(self, user_id):
        """محاسبه تعداد دانلودهای باقیمانده"""
        try:
            # کاربران VIP و ادمین نامحدود
            if self.is_vip(user_id):
                return 999, 0, 999
            
            user_data = self.get_user_stats(user_id)
            if user_data:
                current_downloads = user_data[5] or 0
                extra_downloads = user_data[11] or 0
                total_allowed = 5 + extra_downloads
                remaining = max(0, total_allowed - current_downloads)
                return remaining, current_downloads, total_allowed
            return 0, 0, 5
        except:
            return 0, 0, 5
    
    def get_invite_link(self, user_id, bot_username):
        """دریافت لینک دعوت"""
        try:
            cursor = self.conn.cursor()
            
            cursor.execute('SELECT user_id FROM users WHERE user_id = ?', (user_id,))
            if not cursor.fetchone():
                self.add_or_update_user(user_id, "", "", "")
            
            cursor.execute('SELECT invite_code FROM users WHERE user_id = ?', (user_id,))
            result = cursor.fetchone()
            
            if result and result[0]:
                return f"https://t.me/{bot_username}?start={result[0]}"
            else:
                new_invite_code = f"INV{user_id}{random.randint(1000, 9999)}"
                cursor.execute('UPDATE users SET invite_code = ? WHERE user_id = ?', (new_invite_code, user_id))
                self.conn.commit()
                return f"https://t.me/{bot_username}?start={new_invite_code}"
        except Exception as e:
            logger.error(f"❌ خطا در دریافت لینک دعوت: {e}")
            return f"https://t.me/{bot_username}?start=INV{user_id}{random.randint(1000, 9999)}"
    
    def add_invite_reward(self, inviter_id):
        """اضافه کردن پاداش دعوت"""
        try:
            cursor = self.conn.cursor()
            cursor.execute('UPDATE users SET invite_count = invite_count + 1, extra_downloads = extra_downloads + 20 WHERE user_id = ?', (inviter_id,))
            self.conn.commit()
            return True
        except Exception as e:
            logger.error(f"❌ خطا در افزودن پاداش دعوت: {e}")
            return False
    
    def add_required_channel(self, channel_username):
        """افزودن کانال اجباری"""
        try:
            cursor = self.conn.cursor()
            clean_username = channel_username.replace('@', '')
            channel_link = f"https://t.me/{clean_username}"
            
            cursor.execute('''
                INSERT OR REPLACE INTO required_channels (channel_username, channel_link)
                VALUES (?, ?)
            ''', (channel_username, channel_link))
            self.conn.commit()
            return True
        except Exception as e:
            logger.error(f"❌ خطا در افزودن کانال: {e}")
            return False
    
    def remove_required_channel(self, channel_username):
        """حذف کانال اجباری"""
        try:
            cursor = self.conn.cursor()
            cursor.execute('DELETE FROM required_channels WHERE channel_username = ?', (channel_username,))
            self.conn.commit()
            return True
        except Exception as e:
            logger.error(f"❌ خطا در حذف کانال: {e}")
            return False
    
    def get_required_channels(self):
        """دریافت کانال‌های اجباری"""
        cursor = self.conn.cursor()
        cursor.execute('SELECT * FROM required_channels WHERE is_active = 1')
        return cursor.fetchall()
    
    def log_request(self, user_id, url, request_type, success=True, response_time=0):
        """ثبت درخواست"""
        try:
            cursor = self.conn.cursor()
            cursor.execute('INSERT INTO requests (user_id, url, type, success, response_time) VALUES (?, ?, ?, ?, ?)', 
                          (user_id, url, request_type, success, response_time))
            self.conn.commit()
            return True
        except Exception as e:
            logger.error(f"❌ خطا در ثبت درخواست: {e}")
            return False
    
    def get_all_users(self):
        """دریافت تمام کاربران"""
        cursor = self.conn.cursor()
        cursor.execute('SELECT * FROM users ORDER BY join_date DESC')
        return cursor.fetchall()
    
    def get_total_stats(self):
        """دریافت آمار کلی"""
        cursor = self.conn.cursor()
        cursor.execute('SELECT COUNT(*) FROM users')
        total_users = cursor.fetchone()[0] or 0
        cursor.execute('SELECT COUNT(*) FROM requests')
        total_requests = cursor.fetchone()[0] or 0
        cursor.execute('SELECT SUM(total_downloads) FROM users')
        total_downloads = cursor.fetchone()[0] or 0
        cursor.execute('SELECT COUNT(*) FROM users WHERE is_vip = 1')
        total_vip = cursor.fetchone()[0] or 0
        return total_users, total_requests, total_downloads, total_vip
    
    def reset_user_downloads(self, user_id):
        """ریست دانلودهای کاربر"""
        try:
            cursor = self.conn.cursor()
            cursor.execute('UPDATE users SET daily_downloads = 0 WHERE user_id = ?', (user_id,))
            self.conn.commit()
            return True
        except Exception as e:
            logger.error(f"❌ خطا در ریست دانلودها: {e}")
            return False
    
    def backup_database(self):
        """پشتیبان‌گیری از دیتابیس"""
        try:
            if os.path.exists(DB_NAME):
                backup_name = f"{DB_NAME}.backup.{datetime.now().strftime('%Y%m%d_%H%M%S')}"
                import shutil
                shutil.copy2(DB_NAME, backup_name)
                logger.info(f"✅ پشتیبان از دیتابیس گرفته شد: {backup_name}")
                return backup_name
        except Exception as e:
            logger.error(f"❌ خطا در پشتیبان‌گیری: {e}")
        return None

# ایجاد اتصال دیتابیس
db = Database()
bot = telebot.TeleBot(BOT_TOKEN, parse_mode="HTML")

# ==================== سیستم عضویت اجباری هوشمند ====================
def check_subscription(user_id):
    """بررسی هوشمند عضویت کاربر در کانال‌های اجباری"""
    try:
        required_channels = db.get_required_channels()
        
        if not required_channels:
            return True, []
        
        not_joined = []
        
        for channel in required_channels:
            channel_username = channel[2]
            channel_link = channel[3]
            
            try:
                clean_username = channel_username.replace('@', '')
                
                try:
                    chat_member = bot.get_chat_member(f"@{clean_username}", user_id)
                    
                    if chat_member.status in ['member', 'administrator', 'creator']:
                        continue
                    else:
                        not_joined.append({
                            'username': channel_username,
                            'link': channel_link
                        })
                        
                except Exception as e:
                    if "Chat not found" in str(e) or "bot is not a member" in str(e):
                        logger.warning(f"⚠️ ربات در کانال {channel_username} نیست یا ادمین نیست")
                        not_joined.append({
                            'username': channel_username,
                            'link': channel_link
                        })
                    else:
                        not_joined.append({
                            'username': channel_username,
                            'link': channel_link
                        })
                        
            except Exception as e:
                logger.error(f"❌ خطا در بررسی عضویت: {e}")
                not_joined.append({
                    'username': channel_username,
                    'link': channel_link
                })
        
        return len(not_joined) == 0, not_joined
    except Exception as e:
        logger.error(f"❌ خطا در check_subscription: {e}")
        return True, []

# ==================== طراحی منوها ====================
def glass_effect_menu(user_id=None):
    """منوی اصلی - اگر کاربر ادمین باشد، دکمه پنل مدیریت اضافه می‌شود"""
    keyboard = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=2)
    buttons = [
        "🌐 دانلود از اینستاگرام",
        "📊 آمار کاربری من",
        "ℹ️ راهنمای استفاده",
        "🆘 پشتیبانی",
        "👥 دعوت دوستان"
    ]
    
    if user_id == ADMIN_ID:
        buttons.append("👑 پنل مدیریت")
    
    keyboard.add(buttons[0])
    keyboard.add(buttons[1], buttons[2])
    keyboard.add(buttons[3], buttons[4])
    
    if user_id == ADMIN_ID:
        keyboard.add(buttons[5])
    
    return keyboard

def glass_effect_admin_panel():
    """پنل مدیریت ادمین"""
    keyboard = types.InlineKeyboardMarkup(row_width=2)
    buttons = [
        ("📊 آمار کلی", "admin_stats"),
        ("👥 کاربران امروز", "admin_today"),
        ("⭐ مدیریت VIP", "admin_manage_vip"),
        ("📢 ارسال همگانی", "admin_broadcast"),
        ("➕ افزودن کانال", "admin_add_channel"),
        ("➖ حذف کانال", "admin_remove_channel"),
        ("📋 لیست کانال‌ها", "admin_list_channels"),
        ("🔄 ریست کاربر", "admin_reset_user"),
        ("📨 پیام به کاربر", "admin_message_user")
    ]
    
    for i in range(0, len(buttons), 2):
        row = buttons[i:i+2]
        keyboard.add(
            types.InlineKeyboardButton(row[0][0], callback_data=row[0][1]),
            types.InlineKeyboardButton(row[1][0], callback_data=row[1][1]) if len(row) > 1 else types.InlineKeyboardButton(" ", callback_data="none")
        )
    
    keyboard.add(types.InlineKeyboardButton("💾 پشتیبان دیتابیس", callback_data="admin_backup"))
    keyboard.add(types.InlineKeyboardButton("🔄 بازخوانی ربات", callback_data="admin_restart"))
    
    return keyboard

def vip_management_panel():
    """پنل مدیریت VIP"""
    keyboard = types.InlineKeyboardMarkup(row_width=2)
    buttons = [
        ("➕ افزودن VIP", "admin_add_vip"),
        ("➖ حذف VIP", "admin_remove_vip"),
        ("📋 لیست VIP‌ها", "admin_list_vip"),
        ("⏰ تنظیم مدت VIP", "admin_set_vip_time"),
        ("🔙 بازگشت", "admin_back")
    ]
    
    for i in range(0, len(buttons), 2):
        row = buttons[i:i+2]
        keyboard.add(
            types.InlineKeyboardButton(row[0][0], callback_data=row[0][1]),
            types.InlineKeyboardButton(row[1][0], callback_data=row[1][1]) if len(row) > 1 else types.InlineKeyboardButton(" ", callback_data="none")
        )
    
    return keyboard

# ==================== سیستم API دانلود ====================
def download_instagram_content(url):
    """دانلود محتوای اینستاگرام با استفاده از API"""
    start_time = time.time()
    
    if 'stories' in url or '/story/' in url:
        content_type = 'story'
    elif 'reel' in url or 'reels' in url:
        content_type = 'post2'
    elif '/p/' in url or '/tv/' in url:
        content_type = 'post2'
    else:
        content_type = 'post2'
    
    api_url = "https://api.fast-creat.ir/instagram"
    params = {
        'apikey': FAST_CREAT_TOKEN,
        'type': content_type,
        'url': url
    }
    
    try:
        logger.info(f"📡 ارسال درخواست به API برای URL: {url}")
        response = requests.get(api_url, params=params, timeout=45)
        response_time = time.time() - start_time
        
        logger.info(f"✅ پاسخ API دریافت شد. زمان: {response_time:.2f} ثانیه")
        
        if response.status_code == 200:
            result = response.json()
            logger.info(f"📊 پاسخ API: {json.dumps(result, ensure_ascii=False)[:200]}...")
            
            if result.get('ok'):
                return {
                    'success': True,
                    'data': result.get('result', {}),
                    'response_time': response_time
                }
            else:
                logger.warning(f"⚠️ API خطا داد: {result.get('message', 'خطای ناشناخته')}")
                return {
                    'success': False,
                    'error': result.get('message', 'خطا در پردازش لینک'),
                    'response_time': response_time
                }
        else:
            logger.error(f"❌ خطای HTTP: {response.status_code}")
            return {
                'success': False,
                'error': f"خطای HTTP: {response.status_code}",
                'response_time': response_time
            }
            
    except requests.exceptions.Timeout:
        logger.error("⏰ زمان اتصال به API به پایان رسید")
        return {
            'success': False,
            'error': "زمان اتصال به سرور به پایان رسید",
            'response_time': time.time() - start_time
        }
    except requests.exceptions.ConnectionError:
        logger.error("🔌 خطای اتصال به API")
        return {
            'success': False,
            'error': "خطا در اتصال به سرور",
            'response_time': time.time() - start_time
        }
    except Exception as e:
        logger.error(f"❌ خطای ناشناخته در API: {str(e)}")
        return {
            'success': False,
            'error': f"خطای سیستم: {str(e)}",
            'response_time': time.time() - start_time
        }

# ==================== دستورات اصلی ====================
@bot.message_handler(commands=['start', 'restart'])
def start_command(message):
    try:
        user = message.from_user
        logger.info(f"👤 کاربر: {user.id} - {user.first_name}")
        
        db.add_or_update_user(user.id, user.username, user.first_name, user.last_name)
        
        if len(message.text.split()) > 1:
            invite_code = message.text.split()[1]
            if invite_code.startswith("INV"):
                cursor = db.conn.cursor()
                cursor.execute('SELECT user_id FROM users WHERE invite_code = ?', (invite_code,))
                inviter = cursor.fetchone()
                
                if inviter and inviter[0] != user.id:
                    if db.add_invite_reward(inviter[0]):
                        try:
                            bot.send_message(inviter[0], 
                                f"🎉 <b>دوست شما با لینک دعوت شما وارد شد!</b>\n\n"
                                f"👤 کاربر: {user.first_name}\n"
                                f"🆔 آیدی: {user.id}\n"
                                f"🎁 <b>20 دانلود اضافی دریافت کردید!</b>")
                        except:
                            pass
        
        is_subscribed, not_joined = check_subscription(user.id)
        
        if not is_subscribed:
            keyboard = types.InlineKeyboardMarkup()
            for channel_info in not_joined:
                keyboard.add(types.InlineKeyboardButton(
                    f"👉 عضویت در {channel_info['username']}", 
                    url=channel_info['link']
                ))
            keyboard.add(types.InlineKeyboardButton(
                "✅ بررسی مجدد عضویت", 
                callback_data=f"check_sub_{user.id}"
            ))
            
            channels_list = "\n".join([f"• {chan['username']}" for chan in not_joined])
            
            welcome_text = f"""
👋 <b>سلام {user.first_name} عزیز!</b>

🔒 برای استفاده از ربات، ابتدا در کانال‌های زیر عضو شوید:

{channels_list}

⚠️ <b>توجه:</b> پس از عضویت، دکمه «بررسی مجدد عضویت» را بزنید.
            """
            
            bot.send_message(
                message.chat.id,
                welcome_text,
                reply_markup=keyboard,
                parse_mode='HTML'
            )
            return
        
        user_stats = db.get_user_stats(user.id)
        
        if user_stats:
            remaining, current, total = db.get_remaining_downloads(user.id)
        else:
            remaining, current, total = 5, 0, 5
        
        # متن مخصوص VIP یا عادی
        if db.is_vip(user.id):
            status_text = "⭐ <b>وضعیت: کاربر ویژه (دانلود نامحدود)</b>"
        else:
            status_text = f"📥 <b>وضعیت دانلود امروز:</b>\n├ دانلود شده: {current} از {total}\n└ باقی مانده: {remaining}"
        
        welcome_text = f"""
✨ <b>سلام {user.first_name} عزیز!</b>

🎉 به ربات دانلودر حرفه‌ای اینستاگرام خوش آمدید!

<b>🚀 ویژگی‌های ربات:</b>
✅ دانلود پست، ریلس، استوری
✅ کیفیت اصلی بدون افت
✅ دانلود نامحدود برای کاربران ویژه

{status_text}

<b>💡 نحوه استفاده:</b>
۱. لینک پست اینستاگرام را کپی کنید
۲. برای ربات ارسال کنید
۳. فایل را دانلود کنید

🔗 <b>کانال ما:</b> {CHANNEL_USERNAME}

<b>شروع کنید! یک لینک اینستاگرام ارسال کنید. 👇</b>
        """
        
        bot.send_message(
            message.chat.id,
            welcome_text,
            reply_markup=glass_effect_menu(user.id),
            parse_mode='HTML'
        )
        
    except Exception as e:
        logger.error(f"❌ خطا در start_command: {e}")
        bot.reply_to(message, "⚠️ خطا در پردازش دستور. لطفاً مجدد تلاش کنید.")

# ==================== توابع سیستم پشتیبانی ====================
def support_category_selection(message):
    """نمایش دسته‌بندی‌های پشتیبانی"""
    keyboard = types.InlineKeyboardMarkup(row_width=2)
    buttons = [
        ("💼 اسپانسر شدن", "support_cat_sponsor"),
        ("📢 تبلیغات در ربات", "support_cat_ads"),
        ("🐞 خطا یا مشکل", "support_cat_bug"),
        ("📝 سایر موارد", "support_cat_other"),
        ("❌ انصراف", "support_cat_cancel")
    ]
    for btn in buttons:
        keyboard.add(types.InlineKeyboardButton(btn[0], callback_data=btn[1]))
    
    bot.send_message(
        message.chat.id,
        "🆘 <b>پشتیبانی ربات</b>\n\n"
        "لطفاً موضوع درخواست خود را انتخاب کنید:\n"
        "👇👇👇",
        reply_markup=keyboard,
        parse_mode='HTML'
    )

@bot.callback_query_handler(func=lambda call: call.data.startswith('support_cat_'))
def support_category_callback(call):
    try:
        user_id = call.from_user.id
        category = call.data.replace('support_cat_', '')
        
        if category == 'cancel':
            bot.edit_message_text(
                "❌ درخواست پشتیبانی لغو شد.",
                call.message.chat.id,
                call.message.message_id,
                parse_mode='HTML'
            )
            bot.answer_callback_query(call.id, "لغو شد")
            return
        
        category_names = {
            'sponsor': '💼 اسپانسر شدن',
            'ads': '📢 تبلیغات در ربات',
            'bug': '🐞 خطا یا مشکل',
            'other': '📝 سایر موارد'
        }
        
        cat_name = category_names.get(category, 'سایر موارد')
        
        # ذخیره موقت دسته‌بندی در حافظه (می‌توانید از دیتابیس یا دیکشنری استفاده کنید)
        # برای سادگی، از دیکشنری سراسری استفاده نمی‌کنیم. در عوض از register_next_step_handler استفاده می‌کنیم.
        
        msg = bot.edit_message_text(
            f"🆘 <b>ارسال پیام به پشتیبانی</b>\n\n"
            f"📋 <b>موضوع:</b> {cat_name}\n\n"
            f"📝 لطفاً متن درخواست خود را ارسال کنید:\n"
            f"(متن، عکس، ویدیو، فایل و ...)\n\n"
            f"✏️ برای لغو دستور /cancel را ارسال کنید.",
            call.message.chat.id,
            call.message.message_id,
            parse_mode='HTML'
        )
        
        # ثبت مرحله بعدی برای دریافت پیام کاربر
        bot.register_next_step_handler_by_chat_id(
            call.message.chat.id,
            lambda m: process_support_message(m, category, cat_name)
        )
        
        bot.answer_callback_query(call.id)
        
    except Exception as e:
        logger.error(f"❌ خطا در support_category_callback: {e}")
        bot.answer_callback_query(call.id, "❌ خطا", show_alert=True)

def process_support_message(message, category, category_name):
    """دریافت پیام کاربر و ارسال به ادمین"""
    if message.text and message.text.lower() == '/cancel':
        bot.send_message(
            message.chat.id,
            "❌ ارسال درخواست پشتیبانی لغو شد.",
            reply_markup=glass_effect_menu(message.from_user.id),
            parse_mode='HTML'
        )
        return
    
    try:
        user = message.from_user
        
        # ارسال پیام به ادمین
        admin_text = f"""
📨 <b>پیام پشتیبانی جدید</b>

👤 <b>کاربر:</b> {user.first_name} {user.last_name or ''}
🆔 <b>آیدی:</b> <code>{user.id}</code>
📎 <b>یوزرنیم:</b> @{user.username or 'ندارد'}
📋 <b>موضوع:</b> {category_name}
🕒 <b>زمان:</b> {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

<b>📝 محتوای پیام:</b>
        """
        
        # ارسال محتوای پیام به ادمین
        if message.content_type == 'text':
            bot.send_message(
                ADMIN_ID,
                admin_text + f"\n{message.text}",
                parse_mode='HTML'
            )
        elif message.content_type == 'photo':
            bot.send_photo(
                ADMIN_ID,
                message.photo[-1].file_id,
                caption=admin_text + (f"\n{message.caption}" if message.caption else ""),
                parse_mode='HTML'
            )
        elif message.content_type == 'video':
            bot.send_video(
                ADMIN_ID,
                message.video.file_id,
                caption=admin_text + (f"\n{message.caption}" if message.caption else ""),
                parse_mode='HTML'
            )
        elif message.content_type == 'document':
            bot.send_document(
                ADMIN_ID,
                message.document.file_id,
                caption=admin_text + (f"\n{message.caption}" if message.caption else ""),
                parse_mode='HTML'
            )
        elif message.content_type == 'audio':
            bot.send_audio(
                ADMIN_ID,
                message.audio.file_id,
                caption=admin_text + (f"\n{message.caption}" if message.caption else ""),
                parse_mode='HTML'
            )
        elif message.content_type == 'voice':
            bot.send_voice(
                ADMIN_ID,
                message.voice.file_id,
                caption=admin_text,
                parse_mode='HTML'
            )
        elif message.content_type == 'sticker':
            bot.send_sticker(ADMIN_ID, message.sticker.file_id)
            bot.send_message(ADMIN_ID, admin_text + "\n[استیکر]", parse_mode='HTML')
        elif message.content_type == 'animation':
            bot.send_animation(
                ADMIN_ID,
                message.animation.file_id,
                caption=admin_text + (f"\n{message.caption}" if message.caption else ""),
                parse_mode='HTML'
            )
        else:
            bot.send_message(
                ADMIN_ID,
                admin_text + "\n[محتوای قابل نمایش نیست]",
                parse_mode='HTML'
            )
        
        # تایید به کاربر
        bot.send_message(
            message.chat.id,
            f"✅ <b>پیام شما با موفقیت ارسال شد!</b>\n\n"
            f"📋 <b>موضوع:</b> {category_name}\n"
            f"🆔 <b>کد پیگیری:</b> {user.id}-{datetime.now().strftime('%H%M%S')}\n\n"
            f"📌 در اسرع وقت پاسخ شما از طریق همین ربات ارسال خواهد شد.",
            reply_markup=glass_effect_menu(user.id),
            parse_mode='HTML'
        )
        
        logger.info(f"📨 پیام پشتیبانی از {user.id} با موضوع {category_name} به ادمین ارسال شد")
        
    except Exception as e:
        logger.error(f"❌ خطا در process_support_message: {e}")
        bot.send_message(
            message.chat.id,
            "❌ <b>خطا در ارسال پیام!</b>\nلطفاً دوباره تلاش کنید یا با پشتیبانی مستقیم تماس بگیرید.",
            reply_markup=glass_effect_menu(message.from_user.id),
            parse_mode='HTML'
        )

# ==================== پردازش پیام‌ها ====================
@bot.message_handler(func=lambda message: True)
def handle_messages(message):
    try:
        user_id = message.from_user.id
        text = message.text.strip()
        
        # بررسی عضویت
        is_subscribed, not_joined = check_subscription(user_id)
        if not is_subscribed:
            keyboard = types.InlineKeyboardMarkup()
            for channel_info in not_joined:
                keyboard.add(types.InlineKeyboardButton(
                    f"عضویت در {channel_info['username']}", 
                    url=channel_info['link']
                ))
            keyboard.add(types.InlineKeyboardButton(
                "✅ بررسی مجدد", 
                callback_data=f"check_sub_{user_id}"
            ))
            
            bot.reply_to(
                message,
                f"⚠️ <b>لطفاً ابتدا در کانال‌های زیر عضو شوید:</b>\n\n" +
                "\n".join([f"• {chan['username']}" for chan in not_joined]),
                reply_markup=keyboard,
                parse_mode='HTML'
            )
            return
        
        # پردازش منوها
        if text == "🌐 دانلود از اینستاگرام":
            if db.is_vip(user_id):
                bot.reply_to(
                    message,
                    f"⭐ <b>سیستم دانلود VIP فعال</b>\n\n"
                    f"🎉 شما کاربر ویژه هستید و دانلود نامحدود دارید!\n\n"
                    f"🔗 <b>لطفاً لینک اینستاگرام را ارسال کنید:</b>\n\n"
                    f"مثال: https://www.instagram.com/p/...\n"
                    f"یا https://www.instagram.com/reel/...",
                    parse_mode='HTML'
                )
            else:
                remaining, current, total = db.get_remaining_downloads(user_id)
                
                if remaining > 0:
                    bot.reply_to(
                        message,
                        f"📥 <b>سیستم دانلود فعال</b>\n\n"
                        f"📊 <b>وضعیت امروز:</b>\n"
                        f"├ دانلودها: {current}/{total}\n"
                        f"└ باقیمانده: {remaining}\n\n"
                        f"🔗 <b>لطفاً لینک اینستاگرام را ارسال کنید:</b>\n\n"
                        f"مثال: https://www.instagram.com/p/...\n"
                        f"یا https://www.instagram.com/reel/...",
                        parse_mode='HTML'
                    )
                else:
                    invite_link = db.get_invite_link(user_id, bot.get_me().username)
                    bot.reply_to(
                        message,
                        f"😔 <b>دانلودهای امروزت تموم شد!</b>\n\n"
                        f"🎁 <b>با دعوت دوستان ۲۰ دانلود اضافی بگیر!</b>\n\n"
                        f"🔗 <b>لینک دعوت شما:</b>\n"
                        f"<code>{invite_link}</code>\n\n"
                        f"📱 هر دوست که با این لینک بیاد، ۲۰ دانلود اضافی میگیری!",
                        parse_mode='HTML'
                    )
        
        elif text == "📊 آمار کاربری من":
            user = message.from_user
            db.add_or_update_user(user.id, user.username, user.first_name, user.last_name)
            
            user_stats = db.get_user_stats(user.id)
            
            if user_stats:
                join_date = user_stats[4]
                if isinstance(join_date, str):
                    join_date = join_date[:10]
                else:
                    join_date = 'جدید'
                    
                remaining, current, total = db.get_remaining_downloads(user.id)
                is_vip = db.is_vip(user.id)
                
                stats_text = f"""
📊 <b>آمار کاربری شما</b>

<b>👤 اطلاعات شخصی:</b>
├ نام: {user_stats[2] or 'ندارد'}
├ یوزرنیم: @{user_stats[1] or 'ندارد'}
├ آیدی: <code>{user_stats[0]}</code>
└ عضویت: {join_date}
"""
                
                if is_vip:
                    vip_until = user_stats[14]
                    if vip_until:
                        stats_text += f"⭐ <b>وضعیت: کاربر ویژه (تا {vip_until})</b>\n"
                    else:
                        stats_text += "⭐ <b>وضعیت: کاربر ویژه (دائمی)</b>\n"
                
                stats_text += f"""
<b>📥 آمار دانلود:</b>
├ امروز: {current} از {total}
├ کل: {user_stats[7] or 0}
├ دعوت‌ها: {user_stats[10] or 0}
└ دانلود اضافی: {user_stats[11] or 0}

"""
                
                if not is_vip:
                    stats_text += f"<b>🎯 باقیمانده امروز: {remaining}</b>\n\n"
                
                stats_text += f"🔗 <b>کانال ما:</b> {CHANNEL_USERNAME}"
                
                bot.reply_to(message, stats_text, parse_mode='HTML')
            else:
                remaining, current, total = db.get_remaining_downloads(user.id)
                stats_text = f"""
📊 <b>آمار کاربری شما</b>

<b>👤 اطلاعات شخصی:</b>
├ نام: {user.first_name}
├ یوزرنیم: @{user.username or 'ندارد'}
├ آیدی: <code>{user.id}</code>
└ عضویت: امروز

<b>📥 آمار دانلود:</b>
├ امروز: {current} از {total}
├ کل: 0
├ دعوت‌ها: 0
└ دانلود اضافی: 0

<b>🎯 باقیمانده امروز: {remaining}</b>

🔗 <b>کانال ما:</b> {CHANNEL_USERNAME}
                """
                bot.reply_to(message, stats_text, parse_mode='HTML')
        
        elif text == "👑 پنل مدیریت":
            if user_id == ADMIN_ID:
                total_users, total_requests, total_downloads, total_vip = db.get_total_stats()
                
                admin_text = f"""
👑 <b>پنل مدیریت ربات</b>

📊 <b>آمار کلی:</b>
├ کاربران: {total_users} نفر
├ درخواست‌ها: {total_requests} بار
└ دانلودها: {total_downloads} فایل
⭐ کاربران ویژه: {total_vip} نفر

🕒 زمان: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

<b>لطفاً گزینه مورد نظر را انتخاب کنید:</b>
                """
                
                bot.send_message(
                    message.chat.id,
                    admin_text,
                    reply_markup=glass_effect_admin_panel(),
                    parse_mode='HTML'
                )
            else:
                bot.reply_to(message, "⛔ <b>دسترسی محدود!</b>", parse_mode='HTML')
        
        elif text == "ℹ️ راهنمای استفاده":
            help_text = f"""
📚 <b>راهنمای کامل ربات</b>

<b>🎯 نحوه استفاده:</b>
۱. لینک پست/ریلس/استوری اینستاگرام را کپی کنید
۲. در ربات ارسال کنید (پیست کنید)
۳. منتظر دانلود باشید

<b>📊 سیستم دانلود:</b>
• روزانه ۵ دانلود رایگان
• هر دعوت = ۲۰ دانلود اضافی
• کاربران ویژه: دانلود نامحدود
• محدودیت روزانه هر شب ساعت ۱۲ ریست می‌شود

<b>🎁 سیستم دعوت:</b>
هر دوستی که با لینک شما بیاید:
├ ۲۰ دانلود اضافی برای شما
└ ۵ دانلود رایگان برای دوست شما

<b>⭐ سیستم کاربر ویژه:</b>
• فقط توسط ادمین قابل فعال‌سازی
• دانلود نامحدود
• بدون نیاز به دعوت دوستان

<b>⚠️ نکات مهم:</b>
• از لینک اصلی اینستاگرام استفاده کنید
• پست‌های خصوصی قابل دانلود نیستند
• برای پست‌های طولانی صبر کنید

<b>🆘 پشتیبانی:</b> {SUPPORT_USERNAME}
<b>📢 کانال:</b> {CHANNEL_USERNAME}
            """
            bot.reply_to(message, help_text, parse_mode='HTML')
        
        elif text == "🆘 پشتیبانی":
            # فراخوانی سیستم جدید پشتیبانی
            support_category_selection(message)
        
        elif text == "👥 دعوت دوستان":
            user = message.from_user
            db.add_or_update_user(user.id, user.username, user.first_name, user.last_name)
            
            invite_link = db.get_invite_link(user.id, bot.get_me().username)
            user_stats = db.get_user_stats(user.id)
            invite_count = user_stats[10] if user_stats and user_stats[10] else 0
            
            keyboard = types.InlineKeyboardMarkup()
            keyboard.add(types.InlineKeyboardButton(
                "📱 اشتراک‌گذاری لینک", 
                url=f"https://t.me/share/url?url={invite_link}&text=🎉 ربات دانلودر اینستاگرام! بدون تبلیغات و کاملاً رایگان!"
            ))
            
            bot.reply_to(
                message,
                f"📣 <b>سیستم دعوت دوستان</b>\n\n"
                f"🎁 <b>هر دعوت = ۲۰ دانلود اضافی!</b>\n\n"
                f"🔗 <b>لینک اختصاصی شما:</b>\n"
                f"<code>{invite_link}</code>\n\n"
                f"📊 <b>دعوت‌های شما:</b> {invite_count} نفر\n\n"
                f"💡 <b>روش استفاده:</b>\n"
                f"۱. این لینک را برای دوستان بفرستید\n"
                f"۲. دوستان روی لینک کلیک کنند\n"
                f"۳. شما ۲۰ دانلود اضافی دریافت می‌کنید\n\n"
                f"🔗 <b>کانال ما:</b> {CHANNEL_USERNAME}",
                reply_markup=keyboard,
                parse_mode='HTML'
            )
        
        # پردازش لینک اینستاگرام
        elif 'instagram.com' in text:
            if not ('https://www.instagram.com/' in text or 'http://www.instagram.com/' in text):
                bot.reply_to(
                    message,
                    "⚠️ <b>لینک نامعتبر!</b>\n\n"
                    "لطفاً لینک معتبر اینستاگرام ارسال کنید.\n"
                    "مثال: https://www.instagram.com/p/...",
                    parse_mode='HTML'
                )
                return
            
            # بررسی محدودیت دانلود (فقط برای کاربران غیر VIP)
            if not db.can_download(user_id):
                invite_link = db.get_invite_link(user_id, bot.get_me().username)
                keyboard = types.InlineKeyboardMarkup()
                keyboard.add(types.InlineKeyboardButton(
                    "📱 اشتراک‌گذاری لینک", 
                    url=f"https://t.me/share/url?url={invite_link}&text=🎉 ربات دانلودر اینستاگرام!"
                ))
                
                bot.reply_to(
                    message,
                    f"😔 <b>دانلودهای امروزت تموم شد!</b>\n\n"
                    f"🎁 <b>با دعوت دوستان ۲۰ دانلود اضافی بگیر!</b>\n\n"
                    f"🔗 <b>لینک دعوت:</b>\n"
                    f"<code>{invite_link}</code>\n\n"
                    f"📊 هر دعوت = ۲۰ دانلود اضافی",
                    reply_markup=keyboard,
                    parse_mode='HTML'
                )
                return
            
            processing_msg = bot.reply_to(
                message,
                "⏳ <b>در حال پردازش لینک...</b>\n\n"
                "لطفاً چند ثانیه صبر کنید.",
                parse_mode='HTML'
            )
            
            result = download_instagram_content(text)
            
            if result.get('success'):
                data = result.get('data', {})
                
                db.increment_download(user_id)
                db.log_request(user_id, text, 'download', True, result.get('response_time', 0))
                
                try:
                    bot.delete_message(message.chat.id, processing_msg.message_id)
                except:
                    pass
                
                files_sent = 0
                files = data.get('files', [])
                
                if not files:
                    bot.reply_to(
                        message,
                        "❌ <b>فایلی برای دانلود یافت نشد!</b>\n\n"
                        "لطفاً لینک دیگری ارسال کنید.",
                        parse_mode='HTML'
                    )
                    return
                
                for file in files:
                    try:
                        if file.get('type') == 'video':
                            bot.send_video(
                                chat_id=message.chat.id,
                                video=file.get('url'),
                                caption=f"✅ <b>دانلود موفق!</b>\n\n"
                                        f"🎬 <b>نوع:</b> ویدیو\n"
                                        f"📊 <b>کیفیت:</b> {file.get('quality', 'HD')}\n"
                                        f"👤 <b>سازنده:</b> {data.get('author', 'اینستاگرام')}\n\n"
                                        f"✨ <b>ممنون از دانلودت!</b>\n"
                                        f"🔗 {CHANNEL_USERNAME}",
                                parse_mode='HTML'
                            )
                            files_sent += 1
                            time.sleep(1)
                        
                        elif file.get('type') == 'image':
                            bot.send_photo(
                                chat_id=message.chat.id,
                                photo=file.get('url'),
                                caption=f"✅ <b>دانلود موفق!</b>\n\n"
                                        f"📸 <b>نوع:</b> عکس\n"
                                        f"👤 <b>سازنده:</b> {data.get('author', 'اینستاگرام')}\n\n"
                                        f"✨ <b>ممنون از دانلودت!</b>\n"
                                        f"🔗 {CHANNEL_USERNAME}",
                                parse_mode='HTML'
                            )
                            files_sent += 1
                            time.sleep(1)
                            
                    except Exception as e:
                        logger.error(f"❌ خطا در ارسال فایل: {e}")
                        continue
                
                if files_sent > 0:
                    remaining, current, total = db.get_remaining_downloads(user_id)
                    
                    # پیام متفاوت برای VIP و عادی
                    if db.is_vip(user_id):
                        success_text = f"""
✨ <b>عملیات دانلود با موفقیت انجام شد!</b>

⭐ <b>وضعیت: کاربر ویژه (دانلود نامحدود)</b>

✅ <b>{files_sent} فایل ارسال شد.</b>

🔗 {CHANNEL_USERNAME}
                        """
                    else:
                        success_text = f"""
✨ <b>عملیات دانلود با موفقیت انجام شد!</b>

📊 <b>وضعیت دانلود شما:</b>
├ امروز: {current} از {total}
└ باقیمانده: {remaining}

✅ <b>{files_sent} فایل ارسال شد.</b>

🎁 <b>با دعوت دوستان دانلودهای بیشتری دریافت کنید!</b>

🔗 {CHANNEL_USERNAME}
                        """
                    
                    bot.send_message(
                        message.chat.id,
                        success_text,
                        parse_mode='HTML'
                    )
                else:
                    bot.reply_to(
                        message,
                        "❌ <b>خطا در ارسال فایل‌ها!</b>\n\n"
                        "لطفاً مجدد تلاش کنید یا با پشتیبانی تماس بگیرید.",
                        parse_mode='HTML'
                    )
                    
            else:
                db.log_request(user_id, text, 'download', False, result.get('response_time', 0))
                
                error_msg = result.get('error', 'خطای ناشناخته')
                bot.edit_message_text(
                    f"❌ <b>خطا در دانلود!</b>\n\n"
                    f"📛 <b>علت خطا:</b> {error_msg}\n\n"
                    f"🔍 <b>راه‌حل‌ها:</b>\n"
                    f"• لینک را بررسی کنید\n"
                    f"• از لینک اصلی استفاده کنید\n"
                    f"• پست خصوصی قابل دانلود نیست\n"
                    f"• چند دقیقه دیگر تلاش کنید\n\n"
                    f"🆘 <b>پشتیبانی:</b> {SUPPORT_USERNAME}",
                    message.chat.id,
                    processing_msg.message_id,
                    parse_mode='HTML'
                )
        
        else:
            bot.reply_to(
                message,
                f"🤖 <b>سلام!</b>\n\n"
                f"لطفاً یکی از گزینه‌های منو را انتخاب کنید.\n\n"
                f"🔗 <b>کانال ما:</b> {CHANNEL_USERNAME}",
                reply_markup=glass_effect_menu(user_id),
                parse_mode='HTML'
            )
            
    except Exception as e:
        logger.error(f"❌ خطا در handle_messages: {e}")
        bot.reply_to(
            message,
            "⚠️ <b>خطا در پردازش پیام!</b>\n\nلطفاً مجدد تلاش کنید.",
            parse_mode='HTML'
        )

# ==================== مدیریت Callback ====================
@bot.callback_query_handler(func=lambda call: True)
def handle_callbacks(call):
    try:
        if call.data.startswith('check_sub_'):
            user_id = int(call.data.split('_')[2])
            
            if call.from_user.id != user_id:
                bot.answer_callback_query(call.id, "این دکمه برای شما نیست!", show_alert=True)
                return
            
            is_subscribed, not_joined = check_subscription(user_id)
            
            if is_subscribed:
                try:
                    bot.delete_message(call.message.chat.id, call.message.message_id)
                except:
                    pass
                
                bot.send_message(
                    user_id,
                    "✅ <b>عالی! عضویت شما تایید شد.</b>\n\n"
                    "🎉 حالا می‌توانید از ربات استفاده کنید!\n\n"
                    "🔽 <b>لطفاً از منوی زیر گزینه مورد نظر را انتخاب کنید:</b>",
                    reply_markup=glass_effect_menu(user_id),
                    parse_mode='HTML'
                )
                bot.answer_callback_query(call.id, "✅ عضویت شما تایید شد!")
            else:
                keyboard = types.InlineKeyboardMarkup()
                for channel_info in not_joined:
                    keyboard.add(types.InlineKeyboardButton(
                        f"عضویت در {channel_info['username']}", 
                        url=channel_info['link']
                    ))
                keyboard.add(types.InlineKeyboardButton(
                    "✅ بررسی مجدد", 
                    callback_data=f"check_sub_{user_id}"
                ))
                
                try:
                    bot.edit_message_text(
                        f"⚠️ <b>هنوز در کانال‌های زیر عضو نیستید:</b>\n\n" +
                        "\n".join([f"• {chan['username']}" for chan in not_joined]) +
                        f"\n\n📌 پس از عضویت روی «بررسی مجدد» کلیک کنید.",
                        call.message.chat.id,
                        call.message.message_id,
                        reply_markup=keyboard,
                        parse_mode='HTML'
                    )
                except:
                    pass
                
                bot.answer_callback_query(call.id, f"هنوز در {len(not_joined)} کانال عضو نیستید!", show_alert=True)
        
        # پنل ادمین
        elif call.from_user.id == ADMIN_ID:
            if call.data == "admin_stats":
                total_users, total_requests, total_downloads, total_vip = db.get_total_stats()
                
                stats_text = f"""
📊 <b>آمار کلی ربات</b>

👥 <b>کاربران کل:</b> {total_users} نفر
📥 <b>درخواست‌ها:</b> {total_requests} بار
⬇️ <b>دانلودها:</b> {total_downloads} فایل
⭐ <b>کاربران ویژه:</b> {total_vip} نفر
💾 <b>حافظه دیتابیس:</b> {os.path.getsize(DB_NAME) // 1024} KB

🕒 <b>زمان:</b> {datetime.now().strftime('%H:%M:%S')}
                """
                
                bot.edit_message_text(
                    stats_text,
                    call.message.chat.id,
                    call.message.message_id,
                    reply_markup=glass_effect_admin_panel(),
                    parse_mode='HTML'
                )
            
            elif call.data == "admin_today":
                users = db.get_all_users()
                today = datetime.now().date()
                today_users = []
                
                for user in users:
                    if user[4]:
                        if isinstance(user[4], str):
                            try:
                                join_date = datetime.strptime(user[4], '%Y-%m-%d %H:%M:%S').date()
                            except:
                                continue
                        else:
                            join_date = user[4]
                        
                        if join_date == today:
                            today_users.append(user)
                
                if today_users:
                    text = "👥 <b>کاربران امروز</b>\n\n"
                    for i, user in enumerate(today_users[:20], 1):
                        vip_status = "⭐" if db.is_vip(user[0]) else ""
                        text += f"{i}. {user[2] or 'بدون نام'} (@{user[1] or 'ندارد'}) {vip_status}\n"
                    
                    if len(today_users) > 20:
                        text += f"\n📈 <b>تعداد کل:</b> {len(today_users)} کاربر"
                else:
                    text = "📭 <b>امروز کاربر جدیدی نیامده است</b>"
                
                keyboard = types.InlineKeyboardMarkup()
                keyboard.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="admin_back"))
                bot.edit_message_text(text, call.message.chat.id, call.message.message_id, reply_markup=keyboard, parse_mode='HTML')
            
            elif call.data == "admin_manage_vip":
                vip_users = db.get_vip_users()
                vip_count = len(vip_users)
                
                vip_text = f"""
⭐ <b>مدیریت کاربران ویژه</b>

👥 <b>تعداد VIP‌ها:</b> {vip_count} نفر

<b>🔧 گزینه‌های مدیریت:</b>
• افزودن کاربر به VIP
• حذف کاربر از VIP
• تنظیم مدت VIP
• مشاهده لیست VIP‌ها

<b>⚠️ توجه:</b> کاربران ویژه دانلود نامحدود دارند.
                """
                
                bot.edit_message_text(
                    vip_text,
                    call.message.chat.id,
                    call.message.message_id,
                    reply_markup=vip_management_panel(),
                    parse_mode='HTML'
                )
            
            elif call.data == "admin_add_vip":
                msg = bot.send_message(
                    call.message.chat.id,
                    "➕ <b>افزودن کاربر به VIP</b>\n\n"
                    "👤 <b>آیدی کاربر را ارسال کنید:</b>\n"
                    "مثال: 123456789\n\n"
                    "✏️ <i>برای لغو دستور /cancel را ارسال کنید.</i>",
                    parse_mode='HTML'
                )
                bot.register_next_step_handler(msg, process_add_vip)
            
            elif call.data == "admin_remove_vip":
                vip_users = db.get_vip_users()
                if vip_users:
                    keyboard = types.InlineKeyboardMarkup()
                    for user in vip_users[:20]:
                        user_id, username, first_name, vip_until = user
                        display_name = first_name or username or f"User {user_id}"
                        keyboard.add(types.InlineKeyboardButton(
                            f"❌ {display_name} ({user_id})", 
                            callback_data=f"del_vip_{user_id}"
                        ))
                    keyboard.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="admin_back"))
                    
                    bot.edit_message_text(
                        "❌ <b>حذف کاربر از VIP</b>\n\nبرای حذف روی نام کاربر کلیک کنید:",
                        call.message.chat.id,
                        call.message.message_id,
                        reply_markup=keyboard,
                        parse_mode='HTML'
                    )
                else:
                    bot.answer_callback_query(call.id, "کاربر VIPی وجود ندارد!", show_alert=True)
            
            elif call.data.startswith("del_vip_"):
                user_id = int(call.data.replace("del_vip_", ""))
                if db.set_vip(user_id, False):
                    try:
                        bot.send_message(
                            user_id,
                            "⚠️ <b>وضعیت VIP شما تغییر کرد!</b>\n\n"
                            "❌ وضعیت کاربر ویژه شما توسط مدیریت غیرفعال شد.\n"
                            "📊 اکنون مانند کاربران عادی محدودیت دانلود دارید."
                        )
                    except:
                        pass
                    
                    bot.answer_callback_query(call.id, "✅ کاربر از VIP حذف شد!")
                    bot.edit_message_text(
                        f"✅ <b>کاربر {user_id} از لیست VIP‌ها حذف شد!</b>",
                        call.message.chat.id,
                        call.message.message_id,
                        reply_markup=glass_effect_admin_panel(),
                        parse_mode='HTML'
                    )
                else:
                    bot.answer_callback_query(call.id, "❌ خطا در حذف VIP!", show_alert=True)
            
            elif call.data == "admin_list_vip":
                vip_users = db.get_vip_users()
                if vip_users:
                    text = "⭐ <b>لیست کاربران ویژه</b>\n\n"
                    for i, user in enumerate(vip_users, 1):
                        user_id, username, first_name, vip_until = user
                        display_name = first_name or username or f"User {user_id}"
                        vip_status = f"تا {vip_until}" if vip_until else "دائمی"
                        text += f"{i}. {display_name}\n   ├ آیدی: {user_id}\n   └ وضعیت: {vip_status}\n\n"
                    
                    text += f"📊 <b>تعداد کل:</b> {len(vip_users)} کاربر"
                else:
                    text = "📭 <b>کاربر VIPی وجود ندارد</b>"
                
                keyboard = types.InlineKeyboardMarkup()
                keyboard.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="admin_back"))
                bot.edit_message_text(text, call.message.chat.id, call.message.message_id, reply_markup=keyboard, parse_mode='HTML')
            
            elif call.data == "admin_set_vip_time":
                msg = bot.send_message(
                    call.message.chat.id,
                    "⏰ <b>تنظیم مدت VIP</b>\n\n"
                    "📝 <b>دستور:</b> آیدی_کاربر تعداد_روز\n"
                    "مثال: 123456789 30\n\n"
                    "برای VIP دائمی از 0 استفاده کنید:\n"
                    "مثال: 123456789 0\n\n"
                    "✏️ <i>برای لغو دستور /cancel را ارسال کنید.</i>",
                    parse_mode='HTML'
                )
                bot.register_next_step_handler(msg, process_set_vip_time)
            
            elif call.data == "admin_broadcast":
                msg = bot.send_message(
                    call.message.chat.id,
                    "📢 <b>ارسال پیام همگانی</b>\n\n"
                    "هر نوع محتوایی را ارسال کنید:\n"
                    "📝 متن، 📸 عکس، 🎬 ویدیو، 📁 فایل، 🎵 موزیک، 📌 استیکر، 🔗 لینک\n\n"
                    "⚠️ <b>توجه:</b> این پیام بدون اضافه کردن متن اضافی ارسال می‌شود.\n\n"
                    "✏️ <i>برای لغو دستور /cancel را ارسال کنید.</i>",
                    parse_mode='HTML'
                )
                bot.register_next_step_handler(msg, process_broadcast)
            
            elif call.data == "admin_add_channel":
                msg = bot.send_message(
                    call.message.chat.id,
                    "➕ <b>افزودن کانال اجباری</b>\n\n"
                    "🔗 <b>یوزرنیم کانال را ارسال کنید:</b>\n"
                    "مثال: @Cafe_Chot\n\n"
                    "⚠️ <i>ربات باید در کانال ادمین باشد!</i>\n\n"
                    "✏️ <i>برای لغو دستور /cancel را ارسال کنید.</i>",
                    parse_mode='HTML'
                )
                bot.register_next_step_handler(msg, process_add_channel)
            
            elif call.data == "admin_remove_channel":
                channels = db.get_required_channels()
                if channels:
                    keyboard = types.InlineKeyboardMarkup()
                    for channel in channels:
                        keyboard.add(types.InlineKeyboardButton(
                            f"حذف {channel[2]}", 
                            callback_data=f"del_chan_{channel[2]}"
                        ))
                    keyboard.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="admin_back"))
                    
                    bot.edit_message_text(
                        "📋 <b>کانال‌های اجباری</b>\n\nبرای حذف روی نام کانال کلیک کنید:",
                        call.message.chat.id,
                        call.message.message_id,
                        reply_markup=keyboard,
                        parse_mode='HTML'
                    )
                else:
                    bot.answer_callback_query(call.id, "کانالی وجود ندارد!", show_alert=True)
            
            elif call.data.startswith("del_chan_"):
                channel_username = call.data.replace("del_chan_", "")
                if db.remove_required_channel(channel_username):
                    bot.answer_callback_query(call.id, "✅ حذف شد!")
                    bot.edit_message_text(
                        f"✅ <b>کانال {channel_username} با موفقیت حذف شد!</b>",
                        call.message.chat.id,
                        call.message.message_id,
                        reply_markup=glass_effect_admin_panel(),
                        parse_mode='HTML'
                    )
                else:
                    bot.answer_callback_query(call.id, "❌ خطا در حذف!", show_alert=True)
            
            elif call.data == "admin_list_channels":
                channels = db.get_required_channels()
                if channels:
                    text = "📋 <b>کانال‌های اجباری</b>\n\n"
                    for chan in channels:
                        text += f"• {chan[2]}\n  └ {chan[3]}\n"
                else:
                    text = "📭 <b>کانالی وجود ندارد</b>"
                
                keyboard = types.InlineKeyboardMarkup()
                keyboard.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="admin_back"))
                bot.edit_message_text(text, call.message.chat.id, call.message.message_id, reply_markup=keyboard, parse_mode='HTML')
            
            elif call.data == "admin_back":
                bot.edit_message_text(
                    "👑 <b>پنل مدیریت</b>\n\nلطفاً گزینه مورد نظر را انتخاب کنید:",
                    call.message.chat.id,
                    call.message.message_id,
                    reply_markup=glass_effect_admin_panel(),
                    parse_mode='HTML'
                )
            
            elif call.data == "admin_reset_user":
                msg = bot.send_message(
                    call.message.chat.id,
                    "🔄 <b>ریست دانلود کاربر</b>\n\n"
                    "👤 <b>آیدی کاربر را ارسال کنید:</b>\n"
                    "مثال: 123456789\n\n"
                    "✏️ <i>برای لغو دستور /cancel را ارسال کنید.</i>",
                    parse_mode='HTML'
                )
                bot.register_next_step_handler(msg, process_reset_user)
            
            elif call.data == "admin_message_user":
                msg = bot.send_message(
                    call.message.chat.id,
                    "📨 <b>پیام به کاربر</b>\n\n"
                    "👤 <b>آیدی کاربر را ارسال کنید:</b>\n"
                    "مثال: 123456789\n\n"
                    "✏️ <i>برای لغو دستور /cancel را ارسال کنید.</i>",
                    parse_mode='HTML'
                )
                bot.register_next_step_handler(msg, process_message_user_step1)
            
            elif call.data == "admin_backup":
                backup_file = db.backup_database()
                if backup_file:
                    try:
                        with open(backup_file, 'rb') as f:
                            bot.send_document(
                                call.message.chat.id,
                                f,
                                caption=f"💾 <b>پشتیبان دیتابیس</b>\n\n"
                                        f"📅 تاریخ: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n"
                                        f"📊 اندازه: {os.path.getsize(backup_file) // 1024} KB"
                            )
                        bot.answer_callback_query(call.id, "✅ پشتیبان ارسال شد!")
                    except Exception as e:
                        logger.error(f"❌ خطا در ارسال پشتیبان: {e}")
                        bot.answer_callback_query(call.id, "❌ خطا در ارسال پشتیبان!", show_alert=True)
                else:
                    bot.answer_callback_query(call.id, "❌ خطا در ایجاد پشتیبان!", show_alert=True)
            
            elif call.data == "admin_restart":
                bot.answer_callback_query(call.id, "🔄 ربات در حال بازخوانی...")
                bot.send_message(
                    ADMIN_ID,
                    "🔄 <b>ربات با موفقیت بازخوانی شد!</b>\n\n"
                    f"🕒 زمان: {datetime.now().strftime('%H:%M:%S')}",
                    parse_mode='HTML'
                )
        
        # اگر کالبک مربوط به پشتیبانی بود و اینجا پردازش نشد، در تابع جداگانه هندل می‌شود.
        # این بخش فقط برای پنل ادمین و بررسی عضویت است.
        
        bot.answer_callback_query(call.id)
    except Exception as e:
        logger.error(f"❌ خطا در handle_callbacks: {e}")
        try:
            bot.answer_callback_query(call.id, "❌ خطا در پردازش!", show_alert=True)
        except:
            pass

# ==================== توابع مدیریت VIP ====================
def process_add_vip(message):
    if message.from_user.id != ADMIN_ID:
        return
    
    if message.text and message.text.lower() == '/cancel':
        bot.send_message(message.chat.id, "❌ افزودن VIP لغو شد.", reply_markup=glass_effect_admin_panel())
        return
    
    try:
        user_id = int(message.text)
        
        # بررسی وجود کاربر
        cursor = db.conn.cursor()
        cursor.execute('SELECT user_id FROM users WHERE user_id = ?', (user_id,))
        if not cursor.fetchone():
            # اگر کاربر وجود ندارد، اضافه کن
            db.add_or_update_user(user_id, "", "", "")
        
        if db.set_vip(user_id, True):
            try:
                bot.send_message(
                    user_id,
                    "🎉 <b>تبریک! شما کاربر ویژه شدید!</b>\n\n"
                    "⭐ <b>امتیازات کاربر ویژه:</b>\n"
                    "• دانلود نامحدود از اینستاگرام\n"
                    "• بدون نیاز به دعوت دوستان\n"
                    "• بدون محدودیت روزانه\n\n"
                    "✨ از امکانات ویژه ربات لذت ببرید!"
                )
            except:
                pass
            
            bot.send_message(
                message.chat.id,
                f"✅ <b>کاربر {user_id} با موفقیت VIP شد!</b>\n\n"
                f"⭐ کاربر اکنون دسترسی نامحدود دارد.",
                reply_markup=glass_effect_admin_panel(),
                parse_mode='HTML'
            )
        else:
            bot.send_message(
                message.chat.id,
                "❌ <b>خطا در VIP کردن کاربر!</b>",
                reply_markup=glass_effect_admin_panel(),
                parse_mode='HTML'
            )
    except:
        bot.send_message(
            message.chat.id,
            "❌ <b>آیدی نامعتبر!</b>\nلطفاً عدد معتبر وارد کنید.",
            reply_markup=glass_effect_admin_panel(),
            parse_mode='HTML'
        )

def process_set_vip_time(message):
    if message.from_user.id != ADMIN_ID:
        return
    
    if message.text and message.text.lower() == '/cancel':
        bot.send_message(message.chat.id, "❌ تنظیم مدت VIP لغو شد.", reply_markup=glass_effect_admin_panel())
        return
    
    try:
        parts = message.text.split()
        if len(parts) != 2:
            raise ValueError
        
        user_id = int(parts[0])
        days = int(parts[1])
        
        # بررسی وجود کاربر
        cursor = db.conn.cursor()
        cursor.execute('SELECT user_id FROM users WHERE user_id = ?', (user_id,))
        if not cursor.fetchone():
            db.add_or_update_user(user_id, "", "", "")
        
        if days == 0:
            # VIP دائمی
            if db.set_vip(user_id, True, None):
                try:
                    bot.send_message(
                        user_id,
                        "🎉 <b>تبریک! شما کاربر ویژه دائمی شدید!</b>\n\n"
                        "⭐ <b>امتیازات:</b>\n"
                        "• دانلود نامحدود دائمی\n"
                        "• بدون محدودیت\n\n"
                        "✨ مادامی که ربات فعال است، VIP هستید!"
                    )
                except:
                    pass
                
                bot.send_message(
                    message.chat.id,
                    f"✅ <b>کاربر {user_id} VIP دائمی شد!</b>",
                    reply_markup=glass_effect_admin_panel(),
                    parse_mode='HTML'
                )
        elif days > 0:
            # VIP موقت
            if db.set_vip(user_id, True, days):
                expiry_date = (datetime.now() + timedelta(days=days)).strftime('%Y-%m-%d')
                try:
                    bot.send_message(
                        user_id,
                        f"🎉 <b>تبریک! شما کاربر ویژه شدید!</b>\n\n"
                        f"⭐ <b>امتیازات:</b>\n"
                        f"• دانلود نامحدود\n"
                        f"• بدون محدودیت روزانه\n"
                        f"• اعتبار تا: {expiry_date}\n\n"
                        f"✨ از امکانات ویژه ربات لذت ببرید!"
                    )
                except:
                    pass
                
                bot.send_message(
                    message.chat.id,
                    f"✅ <b>کاربر {user_id} VIP شد!</b>\n\n"
                    f"📅 مدت: {days} روز\n"
                    f"⏰ انقضا: {expiry_date}",
                    reply_markup=glass_effect_admin_panel(),
                    parse_mode='HTML'
                )
        else:
            bot.send_message(
                message.chat.id,
                "❌ <b>تعداد روز نامعتبر!</b>\n"
                "برای VIP دائمی از 0 استفاده کنید.",
                reply_markup=glass_effect_admin_panel(),
                parse_mode='HTML'
            )
    except:
        bot.send_message(
            message.chat.id,
            "❌ <b>فرمت نامعتبر!</b>\n"
            "فرمت صحیح: آیدی_کاربر تعداد_روز\n"
            "مثال: 123456789 30",
            reply_markup=glass_effect_admin_panel(),
            parse_mode='HTML'
        )

# ==================== توابع مدیریت ====================
def process_broadcast(message):
    if message.from_user.id != ADMIN_ID:
        return
    
    if message.text and message.text.lower() == '/cancel':
        bot.send_message(message.chat.id, "❌ ارسال همگانی لغو شد.", reply_markup=glass_effect_admin_panel())
        return
    
    users = db.get_all_users()
    if not users:
        bot.send_message(message.chat.id, "❌ هیچ کاربری وجود ندارد!", reply_markup=glass_effect_admin_panel())
        return
    
    processing_msg = bot.send_message(message.chat.id, f"⏳ در حال ارسال به {len(users)} کاربر...")
    
    success = 0
    failed = 0
    
    for user in users:
        try:
            if message.content_type == 'text':
                bot.send_message(
                    user[0],
                    message.text,
                    parse_mode='HTML',
                    disable_web_page_preview=True
                )
            elif message.content_type == 'photo':
                bot.send_photo(
                    user[0],
                    message.photo[-1].file_id,
                    caption=message.caption or '',
                    parse_mode='HTML'
                )
            elif message.content_type == 'video':
                bot.send_video(
                    user[0],
                    message.video.file_id,
                    caption=message.caption or '',
                    parse_mode='HTML'
                )
            elif message.content_type == 'document':
                bot.send_document(
                    user[0],
                    message.document.file_id,
                    caption=message.caption or '',
                    parse_mode='HTML'
                )
            elif message.content_type == 'audio':
                bot.send_audio(
                    user[0],
                    message.audio.file_id,
                    caption=message.caption or '',
                    parse_mode='HTML'
                )
            elif message.content_type == 'voice':
                bot.send_voice(
                    user[0],
                    message.voice.file_id
                )
            elif message.content_type == 'sticker':
                bot.send_sticker(user[0], message.sticker.file_id)
            elif message.content_type == 'animation':
                bot.send_animation(
                    user[0],
                    message.animation.file_id,
                    caption=message.caption or '',
                    parse_mode='HTML'
                )
            
            success += 1
            time.sleep(0.05)
            
        except Exception as e:
            failed += 1
            logger.error(f"❌ خطا در ارسال به کاربر {user[0]}: {e}")
    
    try:
        bot.delete_message(message.chat.id, processing_msg.message_id)
    except:
        pass
    
    report_text = f"""
✅ <b>گزارش ارسال همگانی</b>

👥 <b>کاربران کل:</b> {len(users)}
✅ <b>موفق:</b> {success}
❌ <b>ناموفق:</b> {failed}
📊 <b>درصد موفقیت:</b> {(success/len(users)*100):.1f}%

🕒 <b>زمان:</b> {datetime.now().strftime('%H:%M:%S')}
    """
    
    bot.send_message(message.chat.id, report_text, reply_markup=glass_effect_admin_panel(), parse_mode='HTML')

def process_add_channel(message):
    if message.from_user.id != ADMIN_ID:
        return
    
    if message.text and message.text.lower() == '/cancel':
        bot.send_message(message.chat.id, "❌ افزودن کانال لغو شد.", reply_markup=glass_effect_admin_panel())
        return
    
    try:
        channel_username = message.text.strip()
        
        if not channel_username.startswith('@'):
            channel_username = '@' + channel_username
        
        db.add_required_channel(channel_username)
        bot.send_message(
            message.chat.id,
            f"✅ <b>کانال {channel_username} با موفقیت اضافه شد!</b>\n\n"
            f"🔗 لینک: https://t.me/{channel_username.replace('@', '')}\n\n"
            f"👤 از این پس کاربران جدید باید در این کانال عضو شوند.",
            reply_markup=glass_effect_admin_panel(),
            parse_mode='HTML'
        )
    except Exception as e:
        bot.send_message(
            message.chat.id,
            f"❌ <b>خطا در افزودن کانال!</b>\n\n{str(e)}",
            reply_markup=glass_effect_admin_panel(),
            parse_mode='HTML'
        )

def process_reset_user(message):
    if message.from_user.id != ADMIN_ID:
        return
    
    if message.text and message.text.lower() == '/cancel':
        bot.send_message(message.chat.id, "❌ ریست کاربر لغو شد.", reply_markup=glass_effect_admin_panel())
        return
    
    try:
        user_id = int(message.text)
        if db.reset_user_downloads(user_id):
            try:
                bot.send_message(
                    user_id,
                    "🔄 <b>ریست دانلود</b>\n\n"
                    "✅ دانلودهای روزانه شما توسط مدیریت ریست شد!\n\n"
                    "📥 اکنون می‌توانید دانلود کنید.",
                    parse_mode='HTML'
                )
            except:
                pass
            
            bot.send_message(
                message.chat.id,
                f"✅ <b>دانلودهای کاربر {user_id} ریست شد!</b>",
                reply_markup=glass_effect_admin_panel(),
                parse_mode='HTML'
            )
        else:
            bot.send_message(
                message.chat.id,
                "❌ <b>خطا در ریست کاربر!</b>",
                reply_markup=glass_effect_admin_panel(),
                parse_mode='HTML'
            )
    except:
        bot.send_message(
            message.chat.id,
            "❌ <b>آیدی نامعتبر!</b>\nلطفاً عدد معتبر وارد کنید.",
            reply_markup=glass_effect_admin_panel(),
            parse_mode='HTML'
        )

def process_message_user_step1(message):
    if message.from_user.id != ADMIN_ID:
        return
    
    if message.text and message.text.lower() == '/cancel':
        bot.send_message(message.chat.id, "❌ ارسال پیام لغو شد.", reply_markup=glass_effect_admin_panel())
        return
    
    try:
        user_id = int(message.text)
        msg = bot.send_message(
            message.chat.id,
            "📝 <b>پیام خود را وارد کنید:</b>\n\n"
            "هر نوع محتوایی را می‌توانید ارسال کنید.\n\n"
            "✏️ <i>برای لغو دستور /cancel را ارسال کنید.</i>",
            parse_mode='HTML'
        )
        
        bot.register_next_step_handler(msg, lambda m: process_message_user_step2(m, user_id))
    except:
        bot.send_message(
            message.chat.id,
            "❌ <b>آیدی نامعتبر!</b>",
            reply_markup=glass_effect_admin_panel(),
            parse_mode='HTML'
        )

def process_message_user_step2(message, user_id):
    if message.from_user.id != ADMIN_ID:
        return
    
    if message.text and message.text.lower() == '/cancel':
        bot.send_message(message.chat.id, "❌ ارسال پیام لغو شد.", reply_markup=glass_effect_admin_panel())
        return
    
    try:
        if message.content_type == 'text':
            bot.send_message(
                user_id,
                message.text,
                parse_mode='HTML'
            )
        elif message.content_type == 'photo':
            bot.send_photo(
                user_id,
                message.photo[-1].file_id,
                caption=message.caption or '',
                parse_mode='HTML'
            )
        elif message.content_type == 'video':
            bot.send_video(
                user_id,
                message.video.file_id,
                caption=message.caption or '',
                parse_mode='HTML'
            )
        elif message.content_type == 'document':
            bot.send_document(
                user_id,
                message.document.file_id,
                caption=message.caption or '',
                parse_mode='HTML'
            )
        
        bot.send_message(
            message.chat.id,
            f"✅ <b>پیام با موفقیت به کاربر {user_id} ارسال شد!</b>",
            reply_markup=glass_effect_admin_panel(),
            parse_mode='HTML'
        )
    except Exception as e:
        bot.send_message(
            message.chat.id,
            f"❌ <b>خطا در ارسال پیام!</b>\n\n{str(e)}",
            reply_markup=glass_effect_admin_panel(),
            parse_mode='HTML'
        )

# ==================== راه‌اندازی ربات ====================
def start_bot():
    print("\n" + "=" * 60)
    print("🚀 در حال راه‌اندازی ربات...")
    print("=" * 60)
    
    try:
        if os.path.exists(DB_NAME):
            print(f"✅ دیتابیس موجود ({os.path.getsize(DB_NAME) // 1024} KB) بارگذاری شد")
        else:
            print("📁 دیتابیس جدید ایجاد شد")
        
        global db
        db = Database()
        
        try:
            db.add_required_channel(CHANNEL_USERNAME)
            print(f"✅ کانال اصلی {CHANNEL_USERNAME} اضافه شد")
        except:
            print(f"⚠️ کانال اصلی از قبل وجود دارد")
        
        bot_info = bot.get_me()
        print(f"✅ ربات: @{bot_info.username}")
        print(f"🆔 آیدی ربات: {bot_info.id}")
        print(f"👑 ادمین: {ADMIN_ID}")
        print(f"📢 کانال: {CHANNEL_USERNAME}")
        
        total_users, total_requests, total_downloads, total_vip = db.get_total_stats()
        print(f"📊 آمار: {total_users} کاربر، {total_requests} درخواست، {total_downloads} دانلود، {total_vip} VIP")
        
        print(f"⏰ زمان: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 60)
        print("\n📱 ربات آنلاین و آماده است!")
        print("⭐ ویژگی جدید: سیستم کاربران ویژه")
        print("💡 دستورات:")
        print("   /start - شروع ربات")
        print("=" * 60)
        
        # ارسال پنل مدیریت به ادمین
        try:
            bot.send_message(
                ADMIN_ID,
                f"✅ <b>ربات VIP راه‌اندازی شد!</b>\n\n"
                f"🤖 ربات: @{bot_info.username}\n"
                f"👥 کاربران: {total_users}\n"
                f"📥 درخواست‌ها: {total_requests}\n"
                f"⬇️ دانلودها: {total_downloads}\n"
                f"⭐ VIP‌ها: {total_vip}\n"
                f"🕒 زمان: {datetime.now().strftime('%H:%M:%S')}\n\n"
                f"👑 برای دسترسی به پنل مدیریت، دکمه «👑 پنل مدیریت» را از منو انتخاب کنید.",
                parse_mode='HTML',
                reply_markup=glass_effect_menu(ADMIN_ID)
            )
        except Exception as e:
            print(f"⚠️ خطا در اطلاع به ادمین: {e}")
        
        bot.polling(none_stop=True, timeout=60, long_polling_timeout=30)
        
    except Exception as e:
        logger.error(f"❌ خطا در راه‌اندازی: {e}")
        print("🔄 تلاش مجدد در 15 ثانیه...")
        time.sleep(15)
        start_bot()

# ==================== اجرای اصلی ====================
if __name__ == "__main__":
    print("🤖 شروع برنامه...")
    start_bot()
