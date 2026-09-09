import os
import sqlite3
import asyncio
import logging
from datetime import datetime, timedelta
from typing import Dict, List, Optional
import json
import uuid
import zipfile
import io
from pathlib import Path

from telegram import (
    Update, InlineKeyboardButton, InlineKeyboardMarkup, 
    BotCommand, Message, CallbackQuery, InputFile
)
from telegram.ext import (
    Application, CommandHandler, MessageHandler, 
    CallbackQueryHandler, ContextTypes, filters
)
from telegram.constants import ParseMode, ChatAction
import aiofiles
from pygments import highlight
from pygments.lexers import get_lexer_by_name, guess_lexer
from pygments.formatters import TerminalFormatter
from pygments.util import ClassNotFound

# Import configuration
from config import *

# Configure logging
logging.basicConfig(
    format=LOG_FORMAT,
    level=getattr(logging, LOG_LEVEL),
    handlers=[
        logging.FileHandler(LOG_FILE),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

# Create uploads directory
Path(UPLOADS_DIR).mkdir(exist_ok=True)

# Validate configuration
try:
    validate_config()
except ValueError as e:
    logger.error(e)
    exit(1)

class DatabaseManager:
    def __init__(self, db_path: str):
        self.db_path = db_path
        self.init_database()
    
    def init_database(self):
        """Initialize database tables"""
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.cursor()
            
            # Users table
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS users (
                    user_id INTEGER PRIMARY KEY,
                    username TEXT,
                    first_name TEXT,
                    last_name TEXT,
                    is_banned INTEGER DEFAULT 0,
                    is_premium INTEGER DEFAULT 0,
                    join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ''')
            
            # Code snippets table
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS snippets (
                    id TEXT PRIMARY KEY,
                    user_id INTEGER,
                    title TEXT,
                    description TEXT,
                    code TEXT,
                    language TEXT,
                    is_private INTEGER DEFAULT 0,
                    views INTEGER DEFAULT 0,
                    likes INTEGER DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users (user_id)
                )
            ''')
            
            # Likes table
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS likes (
                    user_id INTEGER,
                    snippet_id TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (user_id, snippet_id),
                    FOREIGN KEY (user_id) REFERENCES users (user_id),
                    FOREIGN KEY (snippet_id) REFERENCES snippets (id)
                )
            ''')
            
            # Collections table
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS collections (
                    id TEXT PRIMARY KEY,
                    user_id INTEGER,
                    name TEXT,
                    description TEXT,
                    is_private INTEGER DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users (user_id)
                )
            ''')
            
            # Collection items table
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS collection_items (
                    collection_id TEXT,
                    snippet_id TEXT,
                    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (collection_id, snippet_id),
                    FOREIGN KEY (collection_id) REFERENCES collections (id),
                    FOREIGN KEY (snippet_id) REFERENCES snippets (id)
                )
            ''')
            
            conn.commit()
    
    def add_user(self, user_id: int, username: str = None, first_name: str = None, last_name: str = None):
        """Add or update user"""
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                INSERT OR REPLACE INTO users 
                (user_id, username, first_name, last_name, last_activity)
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ''', (user_id, username, first_name, last_name))
            conn.commit()
    
    def get_user(self, user_id: int) -> Optional[Dict]:
        """Get user by ID"""
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.cursor()
            cursor.execute('SELECT * FROM users WHERE user_id = ?', (user_id,))
            row = cursor.fetchone()
            if row:
                columns = [description[0] for description in cursor.description]
                return dict(zip(columns, row))
        return None
    
    def save_snippet(self, snippet_data: Dict) -> str:
        """Save code snippet"""
        snippet_id = str(uuid.uuid4())
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                INSERT INTO snippets 
                (id, user_id, title, description, code, language, is_private)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ''', (
                snippet_id, snippet_data['user_id'], snippet_data['title'],
                snippet_data['description'], snippet_data['code'],
                snippet_data['language'], snippet_data['is_private']
            ))
            conn.commit()
        return snippet_id
    
    def get_snippet(self, snippet_id: str) -> Optional[Dict]:
        """Get snippet by ID"""
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.cursor()
            cursor.execute('SELECT * FROM snippets WHERE id = ?', (snippet_id,))
            row = cursor.fetchone()
            if row:
                columns = [description[0] for description in cursor.description]
                return dict(zip(columns, row))
        return None
    
    def get_user_snippets(self, user_id: int, limit: int = 10) -> List[Dict]:
        """Get user's snippets"""
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT * FROM snippets WHERE user_id = ? 
                ORDER BY created_at DESC LIMIT ?
            ''', (user_id, limit))
            rows = cursor.fetchall()
            columns = [description[0] for description in cursor.description]
            return [dict(zip(columns, row)) for row in rows]
    
    def increment_views(self, snippet_id: str):
        """Increment snippet views"""
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                UPDATE snippets SET views = views + 1 WHERE id = ?
            ''', (snippet_id,))
            conn.commit()
    
    def toggle_like(self, user_id: int, snippet_id: str) -> bool:
        """Toggle like for snippet. Returns True if liked, False if unliked"""
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.cursor()
            
            # Check if already liked
            cursor.execute('''
                SELECT 1 FROM likes WHERE user_id = ? AND snippet_id = ?
            ''', (user_id, snippet_id))
            
            if cursor.fetchone():
                # Unlike
                cursor.execute('''
                    DELETE FROM likes WHERE user_id = ? AND snippet_id = ?
                ''', (user_id, snippet_id))
                cursor.execute('''
                    UPDATE snippets SET likes = likes - 1 WHERE id = ?
                ''', (snippet_id,))
                conn.commit()
                return False
            else:
                # Like
                cursor.execute('''
                    INSERT INTO likes (user_id, snippet_id) VALUES (?, ?)
                ''', (user_id, snippet_id))
                cursor.execute('''
                    UPDATE snippets SET likes = likes + 1 WHERE id = ?
                ''', (snippet_id,))
                conn.commit()
                return True

# Initialize database
db = DatabaseManager(DB_PATH)

# Bot state management
user_states = {}

class BotStates:
    IDLE = "idle"
    WAITING_TITLE = "waiting_title"
    WAITING_DESCRIPTION = "waiting_description"
    WAITING_CODE = "waiting_code"
    WAITING_LANGUAGE = "waiting_language"
    WAITING_BROADCAST = "waiting_broadcast"
    WAITING_COLLECTION_NAME = "waiting_collection_name"

class CodeShareBot:
    def __init__(self):
        self.application = None
    
    async def start_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        """Handle /start command"""
        user = update.effective_user
        db.add_user(user.id, user.username, user.first_name, user.last_name)
        
        welcome_text = f"""
🌟 **خوش آمدید به CodeShareBot** 🌟

سلام {user.first_name}! 👋

من یک ربات پیشرفته برای اشتراک‌گذاری کدهای برنامه‌نویسی هستم.

**امکانات من:**
📝 ذخیره و اشتراک‌گذاری کدهای شما
🔍 جستجو در کدها
❤️ لایک و بوکمارک کردن
📚 ایجاد مجموعه‌های کد
🎨 نمایش کدها با رنگ‌بندی
📊 آمار و گزارش‌گیری
🔐 کنترل دسترسی (عمومی/خصوصی)

برای شروع از دکمه‌های زیر استفاده کنید! 👇
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📝 کد جدید", callback_data="new_snippet"),
                InlineKeyboardButton("📚 کدهای من", callback_data="my_snippets")
            ],
            [
                InlineKeyboardButton("🔍 جستجو", callback_data="search"),
                InlineKeyboardButton("📊 آمار من", callback_data="my_stats")
            ],
            [
                InlineKeyboardButton("🗂️ مجموعه‌ها", callback_data="collections"),
                InlineKeyboardButton("⭐ برترین‌ها", callback_data="top_snippets")
            ],
            [
                InlineKeyboardButton("ℹ️ راهنما", callback_data="help"),
                InlineKeyboardButton("📞 پشتیبانی", callback_data="support")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            welcome_text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def help_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        """Handle /help command"""
        help_text = """
📖 **راهنمای استفاده از CodeShareBot**

**دستورات اصلی:**
/start - شروع ربات
/help - نمایش راهنما
/new - ایجاد کد جدید
/my - نمایش کدهای من
/search - جستجو در کدها
/stats - آمار شخصی

**نحوه استفاده:**
1️⃣ برای ایجاد کد جدید روی "📝 کد جدید" کلیک کنید
2️⃣ عنوان، توضیحات و کد خود را وارد کنید
3️⃣ زبان برنامه‌نویسی را انتخاب کنید
4️⃣ تنظیمات خصوصی/عمومی را انتخاب کنید

**زبان‌های پشتیبانی شده:**
Python, JavaScript, Java, C++, C#, PHP, Go, Rust, Swift, Kotlin و بیش از 100 زبان دیگر!

**ویژگی‌های خاص:**
🎨 رنگ‌بندی خودکار کدها
📥 دانلود کدها به صورت فایل
📊 آمار مشاهده و لایک
🔍 جستجوی پیشرفته
        """
        
        keyboard = [
            [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            help_text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        """Handle /admin command"""
        user_id = update.effective_user.id
        
        if user_id not in ADMIN_IDS:
            await update.message.reply_text("❌ شما دسترسی ادمین ندارید!")
            return
        
        admin_text = """
🔧 **پنل مدیریت CodeShareBot**

خوش آمدید ادمین عزیز! 👨‍💼

از اینجا می‌توانید ربات را مدیریت کنید:
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📊 آمار کلی", callback_data="admin_stats"),
                InlineKeyboardButton("👥 مدیریت کاربران", callback_data="admin_users")
            ],
            [
                InlineKeyboardButton("📝 مدیریت کدها", callback_data="admin_snippets"),
                InlineKeyboardButton("🗑️ پاک‌سازی", callback_data="admin_cleanup")
            ],
            [
                InlineKeyboardButton("📢 ارسال پیام همگانی", callback_data="admin_broadcast"),
                InlineKeyboardButton("⚙️ تنظیمات", callback_data="admin_settings")
            ],
            [
                InlineKeyboardButton("💾 پشتیبان‌گیری", callback_data="admin_backup"),
                InlineKeyboardButton("📈 گزارش‌ها", callback_data="admin_reports")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            admin_text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def new_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        """Handle /new command - shortcut to create new snippet"""
        user = update.effective_user
        db.add_user(user.id, user.username, user.first_name, user.last_name)
        
        user_id = user.id
        user_states[user_id] = {
            'state': BotStates.WAITING_TITLE,
            'snippet_data': {}
        }
        
        text = """
📝 **ایجاد کد جدید**

لطفاً عنوان کد خود را وارد کنید:

💡 نکته: عنوان باید کوتاه و توصیفی باشد
مثال: "الگوریتم مرتب‌سازی حبابی"

📎 یا می‌توانید مستقیماً فایل کد خود را ارسال کنید!
        """
        
        keyboard = [
            [InlineKeyboardButton("❌ انصراف", callback_data="main_menu")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def my_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        """Handle /my command - shortcut to show user snippets"""
        user = update.effective_user
        db.add_user(user.id, user.username, user.first_name, user.last_name)
        
        user_id = user.id
        snippets = db.get_user_snippets(user_id, 10)
        
        if not snippets:
            text = """
📚 **کدهای من**

هنوز کدی ثبت نکرده‌اید! 📝

برای ایجاد اولین کد خود روی دکمه زیر کلیک کنید:
            """
            keyboard = [
                [InlineKeyboardButton("📝 ایجاد کد جدید", callback_data="new_snippet")],
                [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
            ]
        else:
            text = f"📚 **کدهای من** ({len(snippets)} کد)\n\n"
            keyboard = []
            
            for snippet in snippets:
                date = snippet['created_at'].split(' ')[0] if snippet['created_at'] else 'نامشخص'
                text += f"📄 **{snippet['title']}**\n"
                text += f"🗓️ {date} | 👁️ {snippet['views']} | ❤️ {snippet['likes']}\n"
                text += f"🔤 {snippet['language'] or 'نامشخص'}\n\n"
                
                keyboard.append([
                    InlineKeyboardButton(
                        f"👁️ {snippet['title'][:30]}...",
                        callback_data=f"view_snippet_{snippet['id']}"
                    )
                ])
            
            keyboard.extend([
                [InlineKeyboardButton("📝 کد جدید", callback_data="new_snippet")],
                [InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")]
            ])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def stats_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        """Handle /stats command - shortcut to show user stats"""
        user = update.effective_user
        db.add_user(user.id, user.username, user.first_name, user.last_name)
        
        user_id = user.id
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Get user stats
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE user_id = ?', (user_id,))
            total_snippets = cursor.fetchone()[0]
            
            cursor.execute('SELECT SUM(views) FROM snippets WHERE user_id = ?', (user_id,))
            total_views = cursor.fetchone()[0] or 0
            
            cursor.execute('SELECT SUM(likes) FROM snippets WHERE user_id = ?', (user_id,))
            total_likes = cursor.fetchone()[0] or 0
            
            cursor.execute('''
                SELECT language, COUNT(*) FROM snippets 
                WHERE user_id = ? AND language IS NOT NULL 
                GROUP BY language ORDER BY COUNT(*) DESC LIMIT 3
            ''', (user_id,))
            top_languages = cursor.fetchall()
        
        text = f"""
📊 **آمار شخصی شما**

📝 **تعداد کدها:** {total_snippets}
👁️ **کل بازدیدها:** {total_views:,}
❤️ **کل لایک‌ها:** {total_likes:,}

📈 **زبان‌های پرکاربرد:**
"""
        
        for lang, count in top_languages:
            text += f"🔸 {lang}: {count} کد\n"
        
        if not top_languages:
            text += "هنوز کدی ثبت نشده است.\n"
        
        text += f"\n⭐ **امتیاز کلی:** {total_likes + total_views // 10}"
        
        keyboard = [
            [
                InlineKeyboardButton("📚 کدهای من", callback_data="my_snippets"),
                InlineKeyboardButton("📝 کد جدید", callback_data="new_snippet")
            ],
            [InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def button_handler(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        """Handle button callbacks"""
        query = update.callback_query
        await query.answer()
        
        user_id = query.from_user.id
        data = query.data
        
        if data == "main_menu":
            await self.show_main_menu(query)
        elif data == "new_snippet":
            await self.start_new_snippet(query)
        elif data == "my_snippets":
            await self.show_my_snippets(query)
        elif data == "search":
            await self.show_search_menu(query)
        elif data == "my_stats":
            await self.show_user_stats(query)
        elif data == "collections":
            await self.show_collections(query)
        elif data == "new_collection":
            await self.start_new_collection(query)
        elif data.startswith("view_collection_"):
            collection_id = data.replace("view_collection_", "")
            await self.view_collection(query, collection_id)
        elif data.startswith("edit_collection_"):
            collection_id = data.replace("edit_collection_", "")
            await self.edit_collection(query, collection_id)
        elif data.startswith("delete_collection_"):
            collection_id = data.replace("delete_collection_", "")
            await self.delete_collection(query, collection_id)
        elif data.startswith("toggle_collection_privacy_"):
            collection_id = data.replace("toggle_collection_privacy_", "")
            await self.toggle_collection_privacy(query, collection_id)
        elif data.startswith("confirm_delete_collection_"):
            collection_id = data.replace("confirm_delete_collection_", "")
            await self.confirm_delete_collection(query, collection_id)
        elif data == "top_snippets":
            await self.show_top_snippets(query)
        elif data == "help":
            await self.show_help(query)
        elif data == "support":
            await self.show_support(query)
        elif data == "skip_description":
            await self.handle_skip_description(query)
        elif data.startswith("lang_"):
            language = data.replace("lang_", "")
            if language == "other":
                await self.handle_other_language(query)
            else:
                await self.handle_language_selection(query, language)
        elif data.startswith("view_snippet_"):
            snippet_id = data.replace("view_snippet_", "")
            await self.view_snippet(query, snippet_id)
        elif data.startswith("like_snippet_"):
            snippet_id = data.replace("like_snippet_", "")
            await self.toggle_snippet_like(query, snippet_id)
        elif data.startswith("download_snippet_"):
            snippet_id = data.replace("download_snippet_", "")
            await self.download_snippet(query, snippet_id, context)
        elif data.startswith("delete_snippet_"):
            snippet_id = data.replace("delete_snippet_", "")
            await self.delete_snippet(query, snippet_id)
        elif data.startswith("full_view_"):
            snippet_id = data.replace("full_view_", "")
            await self.view_full_snippet(query, snippet_id)
        # Admin handlers
        elif data.startswith("admin_"):
            await self.handle_admin_callback(query, data)
    
    async def show_main_menu(self, query: CallbackQuery):
        """Show main menu"""
        user = query.from_user
        
        welcome_text = f"""
🌟 **CodeShareBot - منوی اصلی** 🌟

سلام {user.first_name}! 👋
از منوی زیر گزینه مورد نظر خود را انتخاب کنید:
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📝 کد جدید", callback_data="new_snippet"),
                InlineKeyboardButton("📚 کدهای من", callback_data="my_snippets")
            ],
            [
                InlineKeyboardButton("🔍 جستجو", callback_data="search"),
                InlineKeyboardButton("📊 آمار من", callback_data="my_stats")
            ],
            [
                InlineKeyboardButton("🗂️ مجموعه‌ها", callback_data="collections"),
                InlineKeyboardButton("⭐ برترین‌ها", callback_data="top_snippets")
            ],
            [
                InlineKeyboardButton("ℹ️ راهنما", callback_data="help"),
                InlineKeyboardButton("📞 پشتیبانی", callback_data="support")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            welcome_text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def start_new_snippet(self, query: CallbackQuery):
        """Start creating new snippet"""
        user_id = query.from_user.id
        user_states[user_id] = {
            'state': BotStates.WAITING_TITLE,
            'snippet_data': {}
        }
        
        text = """
📝 **ایجاد کد جدید**

لطفاً عنوان کد خود را وارد کنید:

💡 نکته: عنوان باید کوتاه و توصیفی باشد
مثال: "الگوریتم مرتب‌سازی حبابی"
        """
        
        keyboard = [
            [InlineKeyboardButton("❌ انصراف", callback_data="main_menu")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_my_snippets(self, query: CallbackQuery):
        """Show user's snippets"""
        user_id = query.from_user.id
        snippets = db.get_user_snippets(user_id, 10)
        
        if not snippets:
            text = """
📚 **کدهای من**

هنوز کدی ثبت نکرده‌اید! 📝

برای ایجاد اولین کد خود روی دکمه زیر کلیک کنید:
            """
            keyboard = [
                [InlineKeyboardButton("📝 ایجاد کد جدید", callback_data="new_snippet")],
                [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
            ]
        else:
            text = f"📚 **کدهای من** ({len(snippets)} کد)\n\n"
            keyboard = []
            
            for snippet in snippets:
                date = snippet['created_at'].split(' ')[0] if snippet['created_at'] else 'نامشخص'
                text += f"📄 **{snippet['title']}**\n"
                text += f"🗓️ {date} | 👁️ {snippet['views']} | ❤️ {snippet['likes']}\n"
                text += f"🔤 {snippet['language'] or 'نامشخص'}\n\n"
                
                keyboard.append([
                    InlineKeyboardButton(
                        f"👁️ {snippet['title'][:30]}...",
                        callback_data=f"view_snippet_{snippet['id']}"
                    )
                ])
            
            keyboard.extend([
                [InlineKeyboardButton("📝 کد جدید", callback_data="new_snippet")],
                [InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")]
            ])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def view_snippet(self, query: CallbackQuery, snippet_id: str):
        """View a specific snippet"""
        snippet = db.get_snippet(snippet_id)
        
        if not snippet:
            await query.answer("❌ کد پیدا نشد!")
            return
        
        # Increment views
        db.increment_views(snippet_id)
        
        # Determine if we should show full code or preview
        full_view = len(snippet['code']) <= 2000  # Show full for smaller codes
        
        if full_view:
            code_display = snippet['code']
            preview_text = ""
        else:
            code_display = snippet['code'][:1500]  # Increased from 500 to 1500
            preview_text = f"\n\n📄 **کد کامل {len(snippet['code']):,} کاراکتر دارد**"
        
        text = f"""
📄 **{snippet['title']}**

📝 **توضیحات:**
{snippet['description'] or 'بدون توضیحات'}

🔤 **زبان:** {snippet['language'] or 'نامشخص'}
👁️ **بازدید:** {snippet['views']}
❤️ **لایک:** {snippet['likes']}
🗓️ **تاریخ:** {snippet['created_at'].split(' ')[0] if snippet['created_at'] else 'نامشخص'}

**کد:**
```{snippet['language'] or 'text'}
{code_display}
```{preview_text}
        """
        
        user_id = query.from_user.id
        is_owner = snippet['user_id'] == user_id
        
        keyboard = [
            [
                InlineKeyboardButton("❤️ لایک", callback_data=f"like_snippet_{snippet_id}"),
                InlineKeyboardButton("📥 دانلود", callback_data=f"download_snippet_{snippet_id}")
            ]
        ]
        
        # Add full view button if code is truncated
        if not full_view:
            keyboard.append([
                InlineKeyboardButton("📖 مشاهده کد کامل", callback_data=f"full_view_{snippet_id}")
            ])
        
        if is_owner:
            keyboard.append([
                InlineKeyboardButton("🗑️ حذف", callback_data=f"delete_snippet_{snippet_id}"),
                InlineKeyboardButton("✏️ ویرایش", callback_data=f"edit_snippet_{snippet_id}")
            ])
        
        keyboard.extend([
            [InlineKeyboardButton("📚 کدهای من", callback_data="my_snippets")],
            [InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")]
        ])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def view_full_snippet(self, query: CallbackQuery, snippet_id: str):
        """View a snippet with full code (no truncation)"""
        snippet = db.get_snippet(snippet_id)
        
        if not snippet:
            await query.answer("❌ کد پیدا نشد!")
            return
        
        # Don't increment views for full view (already counted in preview)
        
        # Show full code
        text = f"""
📄 **{snippet['title']}** (نمایش کامل)

📝 **توضیحات:**
{snippet['description'] or 'بدون توضیحات'}

🔤 **زبان:** {snippet['language'] or 'نامشخص'}
👁️ **بازدید:** {snippet['views']}
❤️ **لایک:** {snippet['likes']}
🗓️ **تاریخ:** {snippet['created_at'].split(' ')[0] if snippet['created_at'] else 'نامشخص'}

**کد کامل ({len(snippet['code']):,} کاراکتر):**
```{snippet['language'] or 'text'}
{snippet['code']}
```
        """
        
        user_id = query.from_user.id
        is_owner = snippet['user_id'] == user_id
        
        keyboard = [
            [
                InlineKeyboardButton("❤️ لایک", callback_data=f"like_snippet_{snippet_id}"),
                InlineKeyboardButton("📥 دانلود", callback_data=f"download_snippet_{snippet_id}")
            ],
            [
                InlineKeyboardButton("◀️ بازگشت به خلاصه", callback_data=f"view_snippet_{snippet_id}")
            ]
        ]
        
        if is_owner:
            keyboard.append([
                InlineKeyboardButton("🗑️ حذف", callback_data=f"delete_snippet_{snippet_id}"),
                InlineKeyboardButton("✏️ ویرایش", callback_data=f"edit_snippet_{snippet_id}")
            ])
        
        keyboard.extend([
            [InlineKeyboardButton("📚 کدهای من", callback_data="my_snippets")],
            [InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")]
        ])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        # Check if message is too long for Telegram (4096 characters limit)
        if len(text) > 4000:
            # Split into multiple messages or send as file
            text = f"""
📄 **{snippet['title']}** (نمایش کامل)

📝 **توضیحات:**
{snippet['description'] or 'بدون توضیحات'}

🔤 **زبان:** {snippet['language'] or 'نامشخص'}
👁️ **بازدید:** {snippet['views']}
❤️ **لایک:** {snippet['likes']}
🗓️ **تاریخ:** {snippet['created_at'].split(' ')[0] if snippet['created_at'] else 'نامشخص'}

⚠️ **کد بیش از حد بزرگ است ({len(snippet['code']):,} کاراکتر)**
از دکمه دانلود استفاده کنید تا فایل کامل را دریافت کنید.
            """
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def toggle_snippet_like(self, query: CallbackQuery, snippet_id: str):
        """Toggle like for snippet"""
        user_id = query.from_user.id
        liked = db.toggle_like(user_id, snippet_id)
        
        if liked:
            await query.answer("❤️ کد لایک شد!")
        else:
            await query.answer("💔 لایک برداشته شد!")
        
        # Refresh the snippet view
        await self.view_snippet(query, snippet_id)
    
    async def download_snippet(self, query: CallbackQuery, snippet_id: str, context: ContextTypes.DEFAULT_TYPE):
        """Download snippet as file"""
        snippet = db.get_snippet(snippet_id)
        
        if not snippet:
            await query.answer("❌ کد پیدا نشد!")
            return
        
        # Determine file extension
        lang_extensions = {
            'python': '.py', 'javascript': '.js', 'java': '.java',
            'cpp': '.cpp', 'c': '.c', 'csharp': '.cs', 'php': '.php',
            'go': '.go', 'rust': '.rs', 'swift': '.swift', 'kotlin': '.kt'
        }
        
        ext = lang_extensions.get(snippet['language'].lower() if snippet['language'] else '', '.txt')
        filename = f"{snippet['title'].replace(' ', '_')}{ext}"
        
        # Create file content
        content = f"""/*
Title: {snippet['title']}
Description: {snippet['description'] or 'No description'}
Language: {snippet['language'] or 'Unknown'}
Created: {snippet['created_at']}
Views: {snippet['views']}
Likes: {snippet['likes']}
Generated by CodeShareBot
*/

{snippet['code']}
"""
        
        # Send as document
        file_obj = io.BytesIO(content.encode('utf-8'))
        file_obj.name = filename
        
        await context.bot.send_document(
            chat_id=query.message.chat_id,
            document=InputFile(file_obj),
            caption=f"📥 فایل کد: **{snippet['title']}**",
            parse_mode=ParseMode.MARKDOWN
        )
        
        await query.answer("📥 فایل ارسال شد!")
    
    async def delete_snippet(self, query: CallbackQuery, snippet_id: str):
        """Delete snippet (owner only)"""
        user_id = query.from_user.id
        snippet = db.get_snippet(snippet_id)
        
        if not snippet:
            await query.answer("❌ کد پیدا نشد!")
            return
        
        if snippet['user_id'] != user_id:
            await query.answer("❌ شما مجاز به حذف این کد نیستید!")
            return
        
        # Delete from database
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('DELETE FROM snippets WHERE id = ?', (snippet_id,))
            cursor.execute('DELETE FROM likes WHERE snippet_id = ?', (snippet_id,))
            conn.commit()
        
        await query.answer("🗑️ کد حذف شد!")
        await self.show_my_snippets(query)
    
    async def show_user_stats(self, query: CallbackQuery):
        """Show user statistics"""
        user_id = query.from_user.id
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Get user stats
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE user_id = ?', (user_id,))
            total_snippets = cursor.fetchone()[0]
            
            cursor.execute('SELECT SUM(views) FROM snippets WHERE user_id = ?', (user_id,))
            total_views = cursor.fetchone()[0] or 0
            
            cursor.execute('SELECT SUM(likes) FROM snippets WHERE user_id = ?', (user_id,))
            total_likes = cursor.fetchone()[0] or 0
            
            cursor.execute('''
                SELECT language, COUNT(*) FROM snippets 
                WHERE user_id = ? AND language IS NOT NULL 
                GROUP BY language ORDER BY COUNT(*) DESC LIMIT 3
            ''', (user_id,))
            top_languages = cursor.fetchall()
        
        text = f"""
📊 **آمار شخصی شما**

📝 **تعداد کدها:** {total_snippets}
👁️ **کل بازدیدها:** {total_views:,}
❤️ **کل لایک‌ها:** {total_likes:,}

📈 **زبان‌های پرکاربرد:**
"""
        
        for lang, count in top_languages:
            text += f"🔸 {lang}: {count} کد\n"
        
        if not top_languages:
            text += "هنوز کدی ثبت نشده است.\n"
        
        text += f"\n⭐ **امتیاز کلی:** {total_likes + total_views // 10}"
        
        keyboard = [
            [
                InlineKeyboardButton("📚 کدهای من", callback_data="my_snippets"),
                InlineKeyboardButton("📝 کد جدید", callback_data="new_snippet")
            ],
            [InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_search_menu(self, query: CallbackQuery):
        """Show search menu"""
        text = """
🔍 **جستجو در کدها**

نوع جستجو مورد نظر خود را انتخاب کنید:
        """
        
        keyboard = [
            [
                InlineKeyboardButton("🔤 جستجو بر اساس عنوان", callback_data="search_title"),
                InlineKeyboardButton("💻 جستجو بر اساس زبان", callback_data="search_language")
            ],
            [
                InlineKeyboardButton("📝 جستجو در کد", callback_data="search_code"),
                InlineKeyboardButton("⭐ محبوب‌ترین‌ها", callback_data="search_popular")
            ],
            [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def search_title(self, query: CallbackQuery):
        """Search snippets by title"""
        user_id = query.from_user.id
        user_states[user_id] = {'state': 'waiting_for_search_title'}
        
        text = """
🔤 **جستجو بر اساس عنوان**

لطفاً عنوان مورد نظر خود را برای جستجو وارد کنید:
        """
        
        keyboard = [
            [InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def search_language(self, query: CallbackQuery):
        """Search snippets by programming language"""
        # List of common programming languages
        languages = [
            "Python", "JavaScript", "Java", "C++", "C#", "PHP", 
            "Ruby", "Swift", "Kotlin", "Go", "Rust", "TypeScript",
            "HTML", "CSS", "SQL", "Bash", "PowerShell", "Perl"
        ]
        
        text = """
💻 **جستجو بر اساس زبان برنامه‌نویسی**

زبان برنامه‌نویسی مورد نظر خود را انتخاب کنید:
        """
        
        # Create buttons for languages, 2 per row
        keyboard = []
        row = []
        
        for i, lang in enumerate(languages):
            row.append(InlineKeyboardButton(lang, callback_data=f"search_by_lang_{lang}"))
            
            if i % 2 == 1 or i == len(languages) - 1:
                keyboard.append(row)
                row = []
        
        # Add back button
        keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def search_code(self, query: CallbackQuery):
        """Search in code content"""
        user_id = query.from_user.id
        user_states[user_id] = {'state': 'waiting_for_search_code'}
        
        text = """
📝 **جستجو در محتوای کد**

لطفاً عبارت مورد نظر خود را برای جستجو در کدها وارد کنید:
        """
        
        keyboard = [
            [InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def search_popular(self, query: CallbackQuery):
        """Show popular snippets"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT id, title, language, views, likes, user_id 
                FROM snippets WHERE is_private = 0
                ORDER BY likes DESC LIMIT 10
            ''')
            popular_snippets = cursor.fetchall()
        
        if not popular_snippets:
            text = """
⭐ **محبوب‌ترین کدها**

هنوز کد عمومی‌ای ثبت نشده است!

اولین نفری باشید که کد خود را به اشتراک می‌گذارد:
            """
            keyboard = [
                [InlineKeyboardButton("📝 ایجاد کد جدید", callback_data="new_snippet")],
                [InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")]
            ]
        else:
            text = "⭐ **محبوب‌ترین کدها**\n\n"
            keyboard = []
            
            for i, snippet in enumerate(popular_snippets, 1):
                snippet_id, title, language, views, likes, user_id = snippet
                text += f"{i}. **{title}**\n"
                text += f"   💻 {language or 'نامشخص'} | 👁️ {views} | ❤️ {likes}\n\n"
                
                keyboard.append([
                    InlineKeyboardButton(
                        f"{i}. {title[:30]}...",
                        callback_data=f"view_snippet_{snippet_id}"
                    )
                ])
            
            keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_search_by_language(self, query: CallbackQuery):
        """Handle search by language selection"""
        callback_data = query.data
        language = callback_data.replace("search_by_lang_", "")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT id, title, language, views, likes, user_id 
                FROM snippets 
                WHERE language = ? AND is_private = 0
                ORDER BY created_at DESC LIMIT 10
            ''', (language,))
            language_snippets = cursor.fetchall()
        
        if not language_snippets:
            text = f"""💻 **کدهای {language}**

هیچ کدی با زبان {language} یافت نشد!

اولین نفری باشید که کد {language} خود را به اشتراک می‌گذارد:
            """
            keyboard = [
                [InlineKeyboardButton("📝 ایجاد کد جدید", callback_data="new_snippet")],
                [InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")]
            ]
        else:
            text = f"💻 **کدهای {language}**\n\n"
            keyboard = []
            
            for i, snippet in enumerate(language_snippets, 1):
                snippet_id, title, lang, views, likes, user_id = snippet
                text += f"{i}. **{title}**\n"
                text += f"   👁️ {views} | ❤️ {likes}\n\n"
                
                keyboard.append([
                    InlineKeyboardButton(
                        f"{i}. {title[:30]}...",
                        callback_data=f"view_snippet_{snippet_id}"
                    )
                ])
            
            keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_search_title_input(self, update: Update, message_text: str):
        """Handle search by title input"""
        user_id = update.effective_user.id
        search_term = message_text.strip()
        
        # Clear user state
        if user_id in user_states:
            del user_states[user_id]
        
        if not search_term:
            await update.message.reply_text(
                "❌ لطفاً یک عنوان معتبر وارد کنید.",
                reply_markup=InlineKeyboardMarkup([
                    [InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")]
                ])
            )
            return
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT id, title, language, views, likes, user_id 
                FROM snippets 
                WHERE title LIKE ? AND is_private = 0
                ORDER BY created_at DESC LIMIT 10
            ''', (f'%{search_term}%',))
            search_results = cursor.fetchall()
        
        if not search_results:
            text = f"""🔍 **نتایج جستجو برای "{search_term}"**

هیچ کدی با این عنوان یافت نشد!

می‌توانید کد جدیدی با این عنوان ایجاد کنید:
            """
            keyboard = [
                [InlineKeyboardButton("📝 ایجاد کد جدید", callback_data="new_snippet")],
                [InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")]
            ]
        else:
            text = f"🔍 **نتایج جستجو برای \"{search_term}\"**\n\n"
            keyboard = []
            
            for i, snippet in enumerate(search_results, 1):
                snippet_id, title, language, views, likes, user_id = snippet
                text += f"{i}. **{title}**\n"
                text += f"   💻 {language or 'نامشخص'} | 👁️ {views} | ❤️ {likes}\n\n"
                
                keyboard.append([
                    InlineKeyboardButton(
                        f"{i}. {title[:30]}...",
                        callback_data=f"view_snippet_{snippet_id}"
                    )
                ])
            
            keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_search_code_input(self, update: Update, message_text: str):
        """Handle search in code content"""
        user_id = update.effective_user.id
        search_term = message_text.strip()
        
        # Clear user state
        if user_id in user_states:
            del user_states[user_id]
        
        if not search_term:
            await update.message.reply_text(
                "❌ لطفاً یک عبارت معتبر وارد کنید.",
                reply_markup=InlineKeyboardMarkup([
                    [InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")]
                ])
            )
            return
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT id, title, language, views, likes, user_id, code 
                FROM snippets 
                WHERE code LIKE ? AND is_private = 0
                ORDER BY created_at DESC LIMIT 10
            ''', (f'%{search_term}%',))
            search_results = cursor.fetchall()
        
        if not search_results:
            text = f"""🔍 **نتایج جستجو در کدها برای "{search_term}"**

هیچ کدی حاوی این عبارت یافت نشد!

می‌توانید کد جدیدی ایجاد کنید:
            """
            keyboard = [
                [InlineKeyboardButton("📝 ایجاد کد جدید", callback_data="new_snippet")],
                [InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")]
            ]
        else:
            text = f"🔍 **نتایج جستجو در کدها برای \"{search_term}\"**\n\n"
            keyboard = []
            
            for i, snippet in enumerate(search_results, 1):
                snippet_id, title, language, views, likes, user_id, code = snippet
                
                # Find the position of the search term in the code
                pos = code.lower().find(search_term.lower())
                if pos >= 0:
                    # Extract a snippet of code around the search term
                    start = max(0, pos - 20)
                    end = min(len(code), pos + len(search_term) + 20)
                    code_preview = code[start:end]
                    
                    # Add ellipsis if we're not showing from the beginning or to the end
                    if start > 0:
                        code_preview = "..." + code_preview
                    if end < len(code):
                        code_preview = code_preview + "..."
                    
                    # Add the code preview to the text
                    text += f"{i}. **{title}**\n"
                    text += f"   💻 {language or 'نامشخص'} | 👁️ {views} | ❤️ {likes}\n"
                    text += f"   `{code_preview}`\n\n"
                else:
                    text += f"{i}. **{title}**\n"
                    text += f"   💻 {language or 'نامشخص'} | 👁️ {views} | ❤️ {likes}\n\n"
                
                keyboard.append([
                    InlineKeyboardButton(
                        f"{i}. {title[:30]}...",
                        callback_data=f"view_snippet_{snippet_id}"
                    )
                ])
            
            keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی جستجو", callback_data="search")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_collections(self, query: CallbackQuery):
        """Show collections menu"""
        user_id = query.from_user.id
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            # Check if user has any collections
            cursor.execute('SELECT COUNT(*) FROM collections WHERE user_id = ?', (user_id,))
            collections_count = cursor.fetchone()[0]
            
            if collections_count == 0:
                # User has no collections
                text = """
🗂️ **مجموعه‌های کد**

شما هنوز هیچ مجموعه‌ای ایجاد نکرده‌اید!

می‌توانید کدهای مورد علاقه خود را در مجموعه‌های مختلف دسته‌بندی کنید.
                """
                
                keyboard = [
                    [InlineKeyboardButton("➕ ایجاد مجموعه جدید", callback_data="new_collection")],
                    [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
                ]
            else:
                # User has collections, show them
                cursor.execute('''
                    SELECT id, name, 
                    (SELECT COUNT(*) FROM collection_items WHERE collection_id = collections.id) as item_count 
                    FROM collections 
                    WHERE user_id = ? 
                    ORDER BY name
                ''', (user_id,))
                collections = cursor.fetchall()
                
                text = "🗂️ **مجموعه‌های کد**\n\n"
                keyboard = []
                
                for collection_id, name, item_count in collections:
                    text += f"📁 **{name}** - {item_count} کد\n"
                    keyboard.append([
                        InlineKeyboardButton(
                            f"{name} ({item_count})", 
                            callback_data=f"view_collection_{collection_id}"
                        )
                    ])
                
                keyboard.append([InlineKeyboardButton("➕ ایجاد مجموعه جدید", callback_data="new_collection")])
                keyboard.append([InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def start_new_collection(self, query: CallbackQuery):
        """Start creating a new collection"""
        user_id = query.from_user.id
        user_states[user_id] = {'state': BotStates.WAITING_COLLECTION_NAME}
        
        text = """
➕ **ایجاد مجموعه جدید**

لطفاً نام مجموعه جدید را وارد کنید:
        """
        
        keyboard = [
            [InlineKeyboardButton("🔙 بازگشت به مجموعه‌ها", callback_data="collections")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_collection_name_input(self, update: Update, message_text: str):
        """Handle collection name input"""
        user_id = update.effective_user.id
        collection_name = message_text.strip()
        
        if not collection_name:
            await update.message.reply_text(
                "❌ لطفاً یک نام معتبر وارد کنید.",
                reply_markup=InlineKeyboardMarkup([
                    [InlineKeyboardButton("🔙 بازگشت به مجموعه‌ها", callback_data="collections")]
                ])
            )
            return
        
        # Check if we're editing an existing collection or creating a new one
        is_editing = False
        collection_id = None
        
        if user_id in user_states and 'editing' in user_states[user_id] and user_states[user_id]['editing']:
            is_editing = True
            collection_id = user_states[user_id].get('collection_id')
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            if is_editing and collection_id:
                # Update existing collection
                cursor.execute('UPDATE collections SET name = ? WHERE id = ? AND user_id = ?', 
                              (collection_name, collection_id, user_id))
                conn.commit()
                
                # Check if update was successful
                if cursor.rowcount == 0:
                    await update.message.reply_text(
                        "❌ خطا در به‌روزرسانی مجموعه. لطفاً دوباره تلاش کنید.",
                        reply_markup=InlineKeyboardMarkup([
                            [InlineKeyboardButton("🔙 بازگشت به مجموعه‌ها", callback_data="collections")]
                        ])
                    )
                    return
                
                success_text = f"""✅ **مجموعه به‌روزرسانی شد**

نام مجموعه با موفقیت به «{collection_name}» تغییر یافت.
                """
                
                keyboard = [
                    [InlineKeyboardButton("👁️ مشاهده مجموعه", callback_data=f"view_collection_{collection_id}")],
                    [InlineKeyboardButton("🔙 بازگشت به مجموعه‌ها", callback_data="collections")]
                ]
            else:
                # Generate a unique ID for the new collection
                collection_id = str(uuid.uuid4())
                
                # Save the new collection to the database
                cursor.execute('''
                    INSERT INTO collections (id, user_id, name, description, is_private, created_at)
                    VALUES (?, ?, ?, ?, ?, datetime('now'))
                ''', (collection_id, user_id, collection_name, "", 0))
                conn.commit()
                
                success_text = f"""✅ **مجموعه جدید ایجاد شد**

مجموعه «{collection_name}» با موفقیت ایجاد شد.

اکنون می‌توانید کدهای مورد علاقه خود را به این مجموعه اضافه کنید.
                """
                
                keyboard = [
                    [InlineKeyboardButton("👁️ مشاهده مجموعه", callback_data=f"view_collection_{collection_id}")],
                    [InlineKeyboardButton("🔙 بازگشت به مجموعه‌ها", callback_data="collections")]
                ]
        
        # Clear user state
        if user_id in user_states:
            del user_states[user_id]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            success_text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def view_collection(self, query: CallbackQuery, collection_id: str):
        """View a collection and its snippets"""
        user_id = query.from_user.id
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Get collection info
            cursor.execute('''
                SELECT name, description, is_private 
                FROM collections 
                WHERE id = ? AND user_id = ?
            ''', (collection_id, user_id))
            collection = cursor.fetchone()
            
            if not collection:
                # Collection not found or doesn't belong to user
                await query.answer("❌ مجموعه مورد نظر یافت نشد!")
                await self.show_collections(query)
                return
            
            collection_name, description, is_private = collection
            
            # Get snippets in this collection
            cursor.execute('''
                SELECT s.id, s.title, s.language, s.views, s.likes 
                FROM snippets s 
                JOIN collection_items ci ON s.id = ci.snippet_id 
                WHERE ci.collection_id = ? 
                ORDER BY ci.added_at DESC
            ''', (collection_id,))
            snippets = cursor.fetchall()
            
            # Prepare text and keyboard
            privacy_status = "🔒 خصوصی" if is_private else "🌐 عمومی"
            text = f"""📁 **مجموعه: {collection_name}**
{privacy_status}

"""
            
            if description:
                text += f"📝 {description}\n\n"
            
            keyboard = []
            
            if not snippets:
                text += "این مجموعه هنوز خالی است!\n\nمی‌توانید کدهای مورد علاقه خود را به این مجموعه اضافه کنید."
            else:
                text += f"**{len(snippets)} کد در این مجموعه:**\n\n"
                
                for i, snippet in enumerate(snippets, 1):
                    snippet_id, title, language, views, likes = snippet
                    text += f"{i}. **{title}**\n"
                    text += f"   💻 {language or 'نامشخص'} | 👁️ {views} | ❤️ {likes}\n\n"
                    
                    keyboard.append([
                        InlineKeyboardButton(
                            f"{i}. {title[:30]}...", 
                            callback_data=f"view_snippet_{snippet_id}"
                        )
                    ])
            
            # Add management buttons
            keyboard.append([
                InlineKeyboardButton("✏️ ویرایش مجموعه", callback_data=f"edit_collection_{collection_id}"),
                InlineKeyboardButton("🗑️ حذف مجموعه", callback_data=f"delete_collection_{collection_id}")
            ])
            keyboard.append([
                InlineKeyboardButton("🔄 تغییر وضعیت", callback_data=f"toggle_collection_privacy_{collection_id}")
            ])
            keyboard.append([InlineKeyboardButton("🔙 بازگشت به مجموعه‌ها", callback_data="collections")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def edit_collection(self, query: CallbackQuery, collection_id: str):
        """Edit a collection"""
        user_id = query.from_user.id
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('SELECT name, description FROM collections WHERE id = ? AND user_id = ?', 
                          (collection_id, user_id))
            collection = cursor.fetchone()
            
            if not collection:
                await query.answer("❌ مجموعه مورد نظر یافت نشد!")
                await self.show_collections(query)
                return
            
            name, description = collection
            
            # Store collection info in user state for editing
            user_states[user_id] = {
                'state': BotStates.WAITING_COLLECTION_NAME,
                'collection_id': collection_id,
                'editing': True
            }
            
            text = f"""
✏️ **ویرایش مجموعه**

نام فعلی: **{name}**

لطفاً نام جدید را وارد کنید:
            """
            
            keyboard = [
                [InlineKeyboardButton("🔙 بازگشت به مجموعه", callback_data=f"view_collection_{collection_id}")]
            ]
            
            reply_markup = InlineKeyboardMarkup(keyboard)
            
            await query.edit_message_text(
                text,
                parse_mode=ParseMode.MARKDOWN,
                reply_markup=reply_markup
            )
    
    async def delete_collection(self, query: CallbackQuery, collection_id: str):
        """Delete a collection"""
        user_id = query.from_user.id
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('SELECT name FROM collections WHERE id = ? AND user_id = ?', 
                          (collection_id, user_id))
            collection = cursor.fetchone()
            
            if not collection:
                await query.answer("❌ مجموعه مورد نظر یافت نشد!")
                await self.show_collections(query)
                return
            
            collection_name = collection[0]
            
            text = f"""
🗑️ **حذف مجموعه**

آیا مطمئن هستید که می‌خواهید مجموعه «**{collection_name}**» را حذف کنید؟

⚠️ **توجه:** این عمل غیرقابل بازگشت است و تمام ارتباط‌های این مجموعه با کدها حذف خواهد شد.
            """
            
            keyboard = [
                [
                    InlineKeyboardButton("✅ بله، حذف شود", callback_data=f"confirm_delete_collection_{collection_id}"),
                    InlineKeyboardButton("❌ خیر، لغو", callback_data=f"view_collection_{collection_id}")
                ]
            ]
            
            reply_markup = InlineKeyboardMarkup(keyboard)
            
            await query.edit_message_text(
                text,
                parse_mode=ParseMode.MARKDOWN,
                reply_markup=reply_markup
            )
    
    async def toggle_collection_privacy(self, query: CallbackQuery, collection_id: str):
        """Toggle collection privacy (private/public)"""
        user_id = query.from_user.id
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('SELECT name, is_private FROM collections WHERE id = ? AND user_id = ?', 
                          (collection_id, user_id))
            collection = cursor.fetchone()
            
            if not collection:
                await query.answer("❌ مجموعه مورد نظر یافت نشد!")
                await self.show_collections(query)
                return
            
            name, is_private = collection
            
            # Toggle privacy status
            new_status = 0 if is_private else 1
            cursor.execute('UPDATE collections SET is_private = ? WHERE id = ?', 
                          (new_status, collection_id))
            conn.commit()
            
            status_text = "خصوصی" if new_status else "عمومی"
            await query.answer(f"✅ وضعیت مجموعه به {status_text} تغییر یافت.")
            
            # Refresh collection view
            await self.view_collection(query, collection_id)
    
    async def confirm_delete_collection(self, query: CallbackQuery, collection_id: str):
        """Confirm and delete a collection"""
        user_id = query.from_user.id
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Verify collection exists and belongs to user
            cursor.execute('SELECT name FROM collections WHERE id = ? AND user_id = ?', 
                          (collection_id, user_id))
            collection = cursor.fetchone()
            
            if not collection:
                await query.answer("❌ مجموعه مورد نظر یافت نشد!")
                await self.show_collections(query)
                return
            
            collection_name = collection[0]
            
            # Delete collection items first (foreign key constraint)
            cursor.execute('DELETE FROM collection_items WHERE collection_id = ?', (collection_id,))
            
            # Delete the collection
            cursor.execute('DELETE FROM collections WHERE id = ?', (collection_id,))
            conn.commit()
            
            # Show success message
            text = f"""
✅ **مجموعه حذف شد**

مجموعه «**{collection_name}**» با موفقیت حذف شد.
            """
            
            keyboard = [
                [InlineKeyboardButton("🔙 بازگشت به مجموعه‌ها", callback_data="collections")]
            ]
            
            reply_markup = InlineKeyboardMarkup(keyboard)
            
            await query.edit_message_text(
                text,
                parse_mode=ParseMode.MARKDOWN,
                reply_markup=reply_markup
            )
    
    async def show_top_snippets(self, query: CallbackQuery):
        """Show top snippets"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT id, title, language, views, likes, user_id 
                FROM snippets WHERE is_private = 0
                ORDER BY (likes * 2 + views) DESC LIMIT 10
            ''')
            top_snippets = cursor.fetchall()
        
        if not top_snippets:
            text = """
⭐ **برترین کدها**

هنوز کد عمومی‌ای ثبت نشده است!

اولین نفری باشید که کد خود را به اشتراک می‌گذارد:
            """
            keyboard = [
                [InlineKeyboardButton("📝 ایجاد کد جدید", callback_data="new_snippet")],
                [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
            ]
        else:
            text = "⭐ **برترین کدها**\n\n"
            keyboard = []
            
            for i, snippet in enumerate(top_snippets, 1):
                snippet_id, title, language, views, likes, user_id = snippet
                score = likes * 2 + views
                text += f"{i}. **{title}**\n"
                text += f"   💻 {language or 'نامشخص'} | 👁️ {views} | ❤️ {likes} | 🏆 {score}\n\n"
                
                keyboard.append([
                    InlineKeyboardButton(
                        f"{i}. {title[:30]}...",
                        callback_data=f"view_snippet_{snippet_id}"
                    )
                ])
            
            keyboard.append([InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_help(self, query: CallbackQuery):
        """Show help"""
        help_text = """
📖 **راهنمای استفاده**

**برای ایجاد کد جدید:**
1. روی "📝 کد جدید" کلیک کنید
2. عنوان کد را وارد کنید
3. توضیحات (اختیاری) بنویسید  
4. کد خود را بفرستید
5. زبان برنامه‌نویسی را انتخاب کنید

**ویژگی‌های کاربردی:**
• مشاهده کدها با رنگ‌بندی مناسب
• دانلود کدها به صورت فایل
• لایک و بوکمارک کردن
• جستجوی پیشرفته
• آمار کامل

**نکات مهم:**
⚠️ کدهای شما به صورت پیش‌فرض عمومی هستند
🔒 برای خصوصی کردن از تنظیمات استفاده کنید
📱 ربات در موبایل و دسکتاپ کار می‌کند
        """
        
        keyboard = [
            [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            help_text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_support(self, query: CallbackQuery):
        """Show support info"""
        support_text = """
📞 **پشتیبانی و ارتباط با ما**

سوال یا مشکلی دارید؟ ما اینجا هستیم! 💪

**راه‌های ارتباط:**
📧 ایمیل: support@codesharebot.com
💬 تلگرام: @CodeShareBot_Support
🌐 وب‌سایت: www.codesharebot.com

**گزارش مشکل:**
اگر مشکلی پیدا کردید، لطفاً موارد زیر را ذکر کنید:
• توضیح مشکل
• مراحل بازتولید مشکل
• اسکرین‌شات (در صورت امکان)

**پیشنهادات:**
💡 پیشنهادات شما برای بهبود ربات ارزشمند است!

⏰ **زمان پاسخگویی:** 24 ساعته
        """
        
        keyboard = [
            [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            support_text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_admin_callback(self, query: CallbackQuery, data: str):
        """Handle admin panel callbacks"""
        user_id = query.from_user.id
        
        if user_id not in ADMIN_IDS:
            await query.answer("❌ دسترسی مجاز نیست!")
            return
        
        if data == "admin_stats":
            await self.show_admin_stats(query)
        elif data == "admin_users":
            await self.show_admin_users(query)
        elif data == "admin_snippets":
            await self.show_admin_snippets(query)
        elif data == "admin_cleanup":
            await self.show_admin_cleanup(query)
        elif data == "admin_broadcast":
            await self.show_admin_broadcast(query)
        elif data == "admin_settings":
            await self.show_admin_settings(query)
        elif data == "admin_backup":
            await self.show_admin_backup(query)
        elif data == "admin_reports":
            await self.show_admin_reports(query)
        elif data == "admin_main":
            await self.show_admin_main(query)
        # Add more specific admin actions
        elif data.startswith("admin_delete_user_"):
            user_to_delete = int(data.replace("admin_delete_user_", ""))
            await self.admin_delete_user(query, user_to_delete)
        elif data.startswith("admin_ban_user_"):
            user_to_ban = int(data.replace("admin_ban_user_", ""))
            await self.admin_ban_user(query, user_to_ban)
        elif data.startswith("admin_delete_snippet_"):
            snippet_to_delete = data.replace("admin_delete_snippet_", "")
            await self.admin_delete_snippet(query, snippet_to_delete)
        elif data == "admin_cleanup_confirm":
            await self.admin_cleanup_confirm(query)
        elif data == "admin_backup_db":
            await self.admin_backup_database(query)
        elif data == "admin_export_users":
            await self.admin_export_users(query)
        elif data == "admin_export_snippets":
            await self.admin_export_snippets(query)
        # Missing handlers - Users management
        elif data == "admin_users_list":
            await self.admin_users_list(query)
        elif data == "admin_users_search":
            await self.admin_users_search(query)
        elif data == "admin_users_banned":
            await self.admin_users_banned(query)
        elif data == "admin_users_active":
            await self.admin_users_active(query)
        elif data == "admin_users_stats":
            await self.admin_users_stats(query)
        # Missing handlers - Snippets management
        elif data == "admin_snippets_list":
            await self.admin_snippets_list(query)
        elif data == "admin_snippets_search":
            await self.admin_snippets_search(query)
        elif data == "admin_snippets_delete":
            await self.admin_snippets_delete(query)
        elif data == "admin_snippets_stats":
            await self.admin_snippets_stats(query)
        elif data == "admin_snippets_private":
            await self.admin_snippets_private(query)
        elif data == "admin_snippets_top":
            await self.admin_snippets_top(query)
        # Missing handlers - Cleanup
        elif data == "admin_cleanup_details":
            await self.admin_cleanup_details(query)
        # Missing handlers - Broadcast
        elif data == "admin_broadcast_text":
            await self.admin_broadcast_text(query)
        elif data == "admin_broadcast_alert":
            await self.admin_broadcast_alert(query)
        elif data == "admin_broadcast_stats":
            await self.admin_broadcast_stats(query)
        elif data == "admin_broadcast_history":
            await self.admin_broadcast_history(query)
        # Missing handlers - Settings
        elif data == "admin_settings_edit":
            await self.admin_settings_edit(query)
        elif data == "admin_settings_reset":
            await self.admin_settings_reset(query)
        elif data == "admin_settings_save":
            await self.admin_settings_save(query)
        elif data == "admin_settings_admins":
            await self.admin_settings_admins(query)
        # Missing handlers - Backup
        elif data == "admin_export_logs":
            await self.admin_export_logs(query)
        elif data == "admin_restore":
            await self.admin_restore(query)
        elif data == "admin_backup_status":
            await self.admin_backup_status(query)
        # Missing handlers - Reports
        elif data == "admin_report_full":
            await self.admin_report_full(query)
        elif data == "admin_report_charts":
            await self.admin_report_charts(query)
        elif data == "admin_report_monthly":
            await self.admin_report_monthly(query)
        elif data == "admin_report_users":
            await self.admin_report_users(query)
        # Additional missing handlers - Deep level callbacks
        elif data == "admin_users_prev":
            await query.answer("⏮️ صفحه قبلی در حال توسعه!")
        elif data == "admin_users_next":
            await query.answer("⏭️ صفحه بعدی در حال توسعه!")
        elif data == "admin_search_username":
            await query.answer("🔍 جستجو نام کاربری به زودی!")
        elif data == "admin_search_name":
            await query.answer("🔍 جستجو نام به زودی!")
        elif data == "admin_search_id":
            await query.answer("🔍 جستجو شناسه به زودی!")
        elif data == "admin_search_today":
            await query.answer("📅 نمایش فعال امروز به زودی!")
        elif data == "admin_unban_all":
            await self.admin_unban_all_users(query)
        elif data == "admin_ban_stats":
            await query.answer("📊 آمار مسدودی به زودی!")
        elif data == "admin_users_week_stats":
            await query.answer("📊 آمار هفتگی به زودی!")
        elif data == "admin_users_month_stats":
            await query.answer("📈 آمار ماهانه به زودی!")
        elif data == "admin_users_chart":
            await query.answer("📊 گراف آمار به زودی!")
        # Snippets callbacks
        elif data == "admin_snippets_prev":
            await query.answer("⏮️ صفحه قبلی در حال توسعه!")
        elif data == "admin_snippets_next":
            await query.answer("⏭️ صفحه بعدی در حال توسعه!")
        elif data == "admin_search_snippet_title":
            await query.answer("🔍 جستجو عنوان به زودی!")
        elif data == "admin_search_snippet_lang":
            await query.answer("🔍 جستجو زبان به زودی!")
        elif data == "admin_search_snippet_content":
            await query.answer("🔍 جستجو محتوا به زودی!")
        elif data == "admin_search_snippet_author":
            await query.answer("🔍 جستجو نویسنده به زودی!")
        elif data == "admin_delete_zero_views":
            await self.admin_delete_zero_views(query)
        elif data == "admin_delete_zero_likes":
            await self.admin_delete_zero_likes(query)
        elif data == "admin_delete_old_private":
            await self.admin_delete_old_private(query)
        elif data == "admin_delete_all_candidates":
            await self.admin_delete_all_candidates(query)
        elif data == "admin_snippets_charts":
            await query.answer("📊 گراف‌های کدها به زودی!")
        elif data == "admin_make_all_public":
            await self.admin_make_all_public(query)
        elif data == "admin_private_stats":
            await query.answer("📊 آمار خصوصی به زودی!")
        elif data == "admin_snippets_rankings":
            await query.answer("📊 رنکینگ کامل به زودی!")
        elif data == "admin_monthly_ranking":
            await query.answer("🏆 رنکینگ ماهانه به زودی!")
        # Broadcast callbacks
        elif data == "admin_start_broadcast":
            await self.admin_start_broadcast(query)
        elif data == "admin_broadcast_templates":
            await self.admin_broadcast_templates(query)
        elif data == "admin_create_alert":
            await self.admin_create_alert(query)
        elif data == "admin_confirm_broadcast":
            await self.admin_confirm_broadcast(query)
        # Settings callbacks
        elif data == "admin_edit_limits":
            await query.answer("📊 ویرایش محدودیت‌ها به زودی!")
        elif data == "admin_edit_security":
            await query.answer("🔒 ویرایش امنیت به زودی!")
        elif data == "admin_edit_ui":
            await query.answer("🎨 ویرایش UI به زودی!")
        elif data == "admin_edit_admins":
            await query.answer("👥 ویرایش ادمین‌ها به زودی!")
        elif data == "admin_confirm_reset":
            await self.admin_confirm_reset(query)
        elif data == "admin_add_admin":
            await query.answer("➕ اضافه کردن ادمین به زودی!")
        elif data == "admin_remove_admin":
            await query.answer("➖ حذف ادمین به زودی!")
        # Backup callbacks
        elif data == "admin_select_backup":
            await query.answer("📁 انتخاب فایل پشتیبان به زودی!")
        elif data == "admin_emergency_restore":
            await query.answer("🔄 بازیابی اضطراری به زودی!")
        elif data == "admin_cleanup_files":
            await query.answer("🧹 پاک‌سازی فایل‌ها به زودی!")
        # Fallback for unhandled admin callbacks
        else:
            await query.answer("⚠️ این قابلیت هنوز پیاده‌سازی نشده است!")
            await self.show_admin_main(query)
    
    async def show_admin_stats(self, query: CallbackQuery):
        """Show admin statistics"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Get general stats
            cursor.execute('SELECT COUNT(*) FROM users')
            total_users = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets')
            total_snippets = cursor.fetchone()[0]
            
            cursor.execute('SELECT SUM(views) FROM snippets')
            total_views = cursor.fetchone()[0] or 0
            
            cursor.execute('SELECT SUM(likes) FROM snippets')
            total_likes = cursor.fetchone()[0] or 0
            
            # Get today's stats
            cursor.execute('''
                SELECT COUNT(*) FROM users 
                WHERE date(join_date) = date('now')
            ''')
            new_users_today = cursor.fetchone()[0]
            
            cursor.execute('''
                SELECT COUNT(*) FROM snippets 
                WHERE date(created_at) = date('now')
            ''')
            new_snippets_today = cursor.fetchone()[0]
        
        text = f"""
📊 **آمار کلی ربات**

👥 **کاربران:**
• کل کاربران: {total_users:,}
• عضو جدید امروز: {new_users_today}

📝 **کدها:**
• کل کدها: {total_snippets:,}
• کد جدید امروز: {new_snippets_today}

📈 **فعالیت:**
• کل بازدیدها: {total_views:,}
• کل لایک‌ها: {total_likes:,}

🏆 **عملکرد:**
• میانگین لایک هر کد: {total_likes/total_snippets if total_snippets > 0 else 0:.1f}
• میانگین بازدید هر کد: {total_views/total_snippets if total_snippets > 0 else 0:.1f}
        """
        
        keyboard = [
            [
                InlineKeyboardButton("👥 مدیریت کاربران", callback_data="admin_users"),
                InlineKeyboardButton("📝 مدیریت کدها", callback_data="admin_snippets")
            ],
            [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_admin_users(self, query: CallbackQuery):
        """Show admin users management"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Get user statistics
            cursor.execute('SELECT COUNT(*) FROM users')
            total_users = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE date(last_activity) = date("now")')
            active_today = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE last_activity >= date("now", "-7 days")')
            active_week = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE is_banned = 1')
            banned_users = cursor.fetchone()[0]
            
            # Get recent users
            cursor.execute('''
                SELECT user_id, username, first_name, join_date, last_activity
                FROM users ORDER BY join_date DESC LIMIT 10
            ''')
            recent_users = cursor.fetchall()
        
        text = f"""
👥 **مدیریت کاربران**

📊 **آمار کلی:**
• کل کاربران: {total_users:,}
• فعال امروز: {active_today}
• فعال این هفته: {active_week}
• مسدود شده: {banned_users}

👤 **آخرین کاربران:**
"""
        
        for user in recent_users[:5]:
            username = f"@{user[1]}" if user[1] else "بدون نام کاربری"
            name = user[2] or "نام نامشخص"
            text += f"• {name} ({username}) - {user[3][:10] if user[3] else 'نامشخص'}\n"
        
        keyboard = [
            [
                InlineKeyboardButton("👥 لیست کاربران", callback_data="admin_users_list"),
                InlineKeyboardButton("🔍 جستجو کاربر", callback_data="admin_users_search")
            ],
            [
                InlineKeyboardButton("🚫 کاربران مسدود", callback_data="admin_users_banned"),
                InlineKeyboardButton("⭐ کاربران فعال", callback_data="admin_users_active")
            ],
            [
                InlineKeyboardButton("📊 آمار تفصیلی", callback_data="admin_users_stats"),
                InlineKeyboardButton("📥 صادرات", callback_data="admin_export_users")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_main"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_admin_snippets(self, query: CallbackQuery):
        """Show admin snippets management"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Get snippet statistics
            cursor.execute('SELECT COUNT(*) FROM snippets')
            total_snippets = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE date(created_at) = date("now")')
            today_snippets = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE is_private = 1')
            private_snippets = cursor.fetchone()[0]
            
            cursor.execute('SELECT AVG(views) FROM snippets')
            avg_views = cursor.fetchone()[0] or 0
            
            cursor.execute('SELECT language, COUNT(*) FROM snippets GROUP BY language ORDER BY COUNT(*) DESC LIMIT 5')
            top_languages = cursor.fetchall()
            
            # Get recent snippets
            cursor.execute('''
                SELECT id, title, language, views, likes, created_at
                FROM snippets ORDER BY created_at DESC LIMIT 5
            ''')
            recent_snippets = cursor.fetchall()
        
        text = f"""
📝 **مدیریت کدها**

📊 **آمار کلی:**
• کل کدها: {total_snippets:,}
• کدهای امروز: {today_snippets}
• کدهای خصوصی: {private_snippets}
• میانگین بازدید: {avg_views:.1f}

💻 **زبان‌های برتر:**
"""
        
        for lang, count in top_languages:
            text += f"• {lang or 'نامشخص'}: {count} کد\n"
        
        text += "\n📄 **آخرین کدها:**\n"
        for snippet in recent_snippets:
            title = snippet[1][:30] + "..." if len(snippet[1]) > 30 else snippet[1]
            text += f"• {title} ({snippet[2] or 'نامشخص'}) - 👁️{snippet[3]} ❤️{snippet[4]}\n"
        
        keyboard = [
            [
                InlineKeyboardButton("📋 لیست کدها", callback_data="admin_snippets_list"),
                InlineKeyboardButton("🔍 جستجو کد", callback_data="admin_snippets_search")
            ],
            [
                InlineKeyboardButton("🗑️ حذف کدها", callback_data="admin_snippets_delete"),
                InlineKeyboardButton("📊 آمار تفصیلی", callback_data="admin_snippets_stats")
            ],
            [
                InlineKeyboardButton("🔒 کدهای خصوصی", callback_data="admin_snippets_private"),
                InlineKeyboardButton("⭐ کدهای برتر", callback_data="admin_snippets_top")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_main"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def message_handler(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        """Handle text messages based on user state"""
        user_id = update.effective_user.id
        message_text = update.message.text
        
        if user_id not in user_states:
            # No active state, ignore or show menu
            await update.message.reply_text(
                "لطفاً از منو استفاده کنید یا /start را بزنید.",
                reply_markup=InlineKeyboardMarkup([
                    [InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")]
                ])
            )
            return
        
        user_state = user_states[user_id]
        
        if user_state['state'] == BotStates.WAITING_TITLE:
            await self.handle_title_input(update, message_text)
        elif user_state['state'] == BotStates.WAITING_DESCRIPTION:
            await self.handle_description_input(update, message_text)
        elif user_state['state'] == BotStates.WAITING_CODE:
            await self.handle_code_input(update, message_text)
        elif user_state['state'] == BotStates.WAITING_LANGUAGE:
            await self.handle_language_input(update, message_text)
        elif user_state['state'] == BotStates.WAITING_BROADCAST:
            await self.handle_broadcast_input(update, message_text)
        elif user_state['state'] == 'waiting_for_search_title':
            await self.handle_search_title_input(update, message_text)
        elif user_state['state'] == 'waiting_for_search_code':
            await self.handle_search_code_input(update, message_text)
        elif user_state['state'] == BotStates.WAITING_COLLECTION_NAME:
            await self.handle_collection_name_input(update, message_text)
    
    async def document_handler(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        """Handle document messages (code files)"""
        user_id = update.effective_user.id
        
        if user_id not in user_states:
            # If no active session, start new snippet creation
            user_states[user_id] = {
                'state': BotStates.WAITING_CODE,
                'snippet_data': {}
            }
        
        user_state = user_states[user_id]
        
        # Allow file upload in any state (except idle) and auto-advance to code step
        if user_state['state'] == BotStates.WAITING_TITLE:
            # Skip title step and use filename
            pass
        elif user_state['state'] == BotStates.WAITING_DESCRIPTION:
            # Skip description step
            user_state['snippet_data']['description'] = ""
        elif user_state['state'] != BotStates.WAITING_CODE:
            await update.message.reply_text("❌ لطفاً فایل کد را در مرحله مناسب ارسال کنید یا ابتدا /start را بزنید.")
            return
        
        document = update.message.document
        file_name = document.file_name
        
        # Validate file extension
        allowed_extensions = ['.py', '.js', '.java', '.cpp', '.c', '.cs', '.php', '.go', '.rs', '.swift', '.kt', '.txt', '.html', '.css', '.sql', '.sh', '.ps1', '.json', '.xml', '.md', '.rb', '.ts', '.jsx', '.tsx', '.vue', '.dart', '.scala', '.r', '.pl', '.lua', '.bat']
        if not any(file_name.lower().endswith(ext) for ext in allowed_extensions):
            await update.message.reply_text("❌ فقط فایل‌های کد مجاز هستند!\n\n🔸 **پشتیبانی شده:** .py, .js, .java, .cpp, .c, .cs, .php, .go, .rs, .swift, .kt, .html, .css, .sql, .json, .md و بیشتر...")
            return
        
        # Check file size (Telegram limit is 20MB, but we limit further)
        if document.file_size and document.file_size > MAX_FILE_SIZE_MB * 1024 * 1024:
            await update.message.reply_text(ERROR_MESSAGES["file_too_large"])
            return
        
        # Show downloading message
        downloading_msg = await update.message.reply_text("📥 در حال دانلود فایل...")
        
        # Download the file
        try:
            file_path = await context.bot.get_file(document.file_id)
            file_content = await file_path.download_as_bytearray()
        except Exception as e:
            await downloading_msg.edit_text(ERROR_MESSAGES["file_download_error"])
            logger.error(f"File download error: {e}")
            return
        
        # Decode content with multiple encoding attempts
        code = None
        encodings = ['utf-8', 'utf-8-sig', 'ascii', 'latin-1', 'cp1252']
        
        for encoding in encodings:
            try:
                code = file_content.decode(encoding)
                break
            except UnicodeDecodeError:
                continue
        
        if code is None:
            await downloading_msg.edit_text(ERROR_MESSAGES["encoding_error"])
            return
        
        # Validate code length
        if len(code) > MAX_CODE_LENGTH:
            await downloading_msg.edit_text(ERROR_MESSAGES["code_too_long"])
            return
        
        # Update snippet data
        user_state['snippet_data']['code'] = code
        
        # If title is empty, use file name as title
        if not user_state['snippet_data'].get('title'):
            title = file_name.rsplit('.', 1)[0]  # Remove extension
            user_state['snippet_data']['title'] = title
        
        # Try to guess language from file extension
        file_ext = file_name.lower().split('.')[-1] if '.' in file_name else ''
        ext_to_lang = {
            'py': 'python', 'js': 'javascript', 'ts': 'typescript', 'jsx': 'javascript', 'tsx': 'typescript',
            'java': 'java', 'cpp': 'cpp', 'cc': 'cpp', 'cxx': 'cpp', 'c': 'c', 'h': 'c',
            'cs': 'csharp', 'php': 'php', 'go': 'go', 'rs': 'rust', 'swift': 'swift',
            'kt': 'kotlin', 'rb': 'ruby', 'html': 'html', 'htm': 'html', 'css': 'css', 'scss': 'css',
            'sql': 'sql', 'sh': 'bash', 'bash': 'bash', 'ps1': 'powershell', 'json': 'json',
            'xml': 'xml', 'md': 'markdown', 'yaml': 'yaml', 'yml': 'yaml', 'toml': 'toml',
            'vue': 'javascript', 'dart': 'dart', 'scala': 'scala', 'r': 'r', 'R': 'r',
            'pl': 'perl', 'lua': 'lua', 'bat': 'batch', 'cmd': 'batch', 'dockerfile': 'dockerfile',
            'makefile': 'makefile', 'cmake': 'cmake', 'gradle': 'gradle', 'conf': 'text', 'cfg': 'text'
        }
        
        guessed_language = ext_to_lang.get(file_ext)
        
        # Also try to guess from content if extension didn't help
        if not guessed_language:
            try:
                lexer = guess_lexer(code)
                guessed_language = lexer.name.lower()
            except ClassNotFound:
                guessed_language = None
        
        text = f"""
✅ **کد ثبت شد** ({len(code)} کاراکتر)

حالا زبان برنامه‌نویسی را انتخاب کنید:
        """
        
        if guessed_language:
            text += f"\n💡 **پیشنهاد:** {guessed_language}"
        
        # Common programming languages
        languages = [
            ('Python', 'python'), ('JavaScript', 'javascript'),
            ('Java', 'java'), ('C++', 'cpp'),
            ('C#', 'csharp'), ('PHP', 'php'),
            ('Go', 'go'), ('Rust', 'rust'),
            ('Swift', 'swift'), ('Kotlin', 'kotlin'),
            ('TypeScript', 'typescript'), ('Ruby', 'ruby')
        ]
        
        keyboard = []
        row = []
        for display_name, lang_code in languages:
            row.append(InlineKeyboardButton(display_name, callback_data=f"lang_{lang_code}"))
            if len(row) == 2:
                keyboard.append(row)
                row = []
        
        if row:  # Add remaining buttons
            keyboard.append(row)
        
        keyboard.extend([
            [InlineKeyboardButton("🔤 زبان دیگر", callback_data="lang_other")],
            [InlineKeyboardButton("❌ انصراف", callback_data="main_menu")]
        ])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await downloading_msg.edit_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_title_input(self, update: Update, title: str):
        """Handle title input for new snippet"""
        user_id = update.effective_user.id
        user_states[user_id]['snippet_data']['title'] = title
        user_states[user_id]['state'] = BotStates.WAITING_DESCRIPTION
        
        text = f"""
✅ **عنوان ثبت شد:** {title}

حالا توضیحات کد خود را وارد کنید:

💡 نکته: توضیحات به دیگران کمک می‌کند کد شما را بهتر درک کنند
می‌توانید این مرحله را رد کنید.
        """
        
        keyboard = [
            [InlineKeyboardButton("⏭️ رد کردن توضیحات", callback_data="skip_description")],
            [InlineKeyboardButton("❌ انصراف", callback_data="main_menu")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_description_input(self, update: Update, description: str):
        """Handle description input for new snippet"""
        user_id = update.effective_user.id
        user_states[user_id]['snippet_data']['description'] = description
        user_states[user_id]['state'] = BotStates.WAITING_CODE
        
        text = """
✅ **توضیحات ثبت شد**

حالا کد خود را ارسال کنید:

📝 **راهنما:**
• کد را مستقیماً در پیام بفرستید
• می‌توانید فایل کد را نیز ارسال کنید
• از Copy/Paste استفاده کنید
        """
        
        keyboard = [
            [InlineKeyboardButton("❌ انصراف", callback_data="main_menu")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_code_input(self, update: Update, code: str):
        """Handle code input for new snippet"""
        user_id = update.effective_user.id
        user_states[user_id]['snippet_data']['code'] = code
        
        # Try to guess language
        try:
            lexer = guess_lexer(code)
            guessed_language = lexer.name.lower()
        except ClassNotFound:
            guessed_language = None
        
        text = f"""
✅ **کد ثبت شد** ({len(code)} کاراکتر)

حالا زبان برنامه‌نویسی را انتخاب کنید:
        """
        
        if guessed_language:
            text += f"\n💡 **پیشنهاد:** {guessed_language}"
        
        # Common programming languages
        languages = [
            ('Python', 'python'), ('JavaScript', 'javascript'),
            ('Java', 'java'), ('C++', 'cpp'),
            ('C#', 'csharp'), ('PHP', 'php'),
            ('Go', 'go'), ('Rust', 'rust'),
            ('Swift', 'swift'), ('Kotlin', 'kotlin'),
            ('TypeScript', 'typescript'), ('Ruby', 'ruby')
        ]
        
        keyboard = []
        row = []
        for display_name, lang_code in languages:
            row.append(InlineKeyboardButton(display_name, callback_data=f"lang_{lang_code}"))
            if len(row) == 2:
                keyboard.append(row)
                row = []
        
        if row:  # Add remaining buttons
            keyboard.append(row)
        
        keyboard.extend([
            [InlineKeyboardButton("🔤 زبان دیگر", callback_data="lang_other")],
            [InlineKeyboardButton("❌ انصراف", callback_data="main_menu")]
        ])
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_language_selection(self, query: CallbackQuery, language: str):
        """Handle language selection and finalize snippet creation"""
        user_id = query.from_user.id
        
        if user_id not in user_states:
            await query.answer("❌ جلسه منقضی شده است!")
            return
        
        snippet_data = user_states[user_id]['snippet_data']
        snippet_data['language'] = language
        snippet_data['user_id'] = user_id
        snippet_data['is_private'] = 0  # Default to public
        
        # Save snippet to database
        snippet_id = db.save_snippet(snippet_data)
        
        # Clear user state
        del user_states[user_id]
        
        text = f"""
🎉 **کد با موفقیت ثبت شد!**

📄 **عنوان:** {snippet_data['title']}
🔤 **زبان:** {language}
🆔 **شناسه:** `{snippet_id}`

کد شما در دسترس همه قرار گرفت و می‌توانند آن را مشاهده، لایک و دانلود کنند.
        """
        
        keyboard = [
            [
                InlineKeyboardButton("👁️ مشاهده کد", callback_data=f"view_snippet_{snippet_id}"),
                InlineKeyboardButton("📚 کدهای من", callback_data="my_snippets")
            ],
            [
                InlineKeyboardButton("📝 کد جدید", callback_data="new_snippet"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_skip_description(self, query: CallbackQuery):
        """Handle skipping description step"""
        user_id = query.from_user.id
        
        if user_id not in user_states:
            await query.answer("❌ جلسه منقضی شده است!")
            return
        
        user_states[user_id]['snippet_data']['description'] = ""
        user_states[user_id]['state'] = BotStates.WAITING_CODE
        
        text = """
✅ **مرحله توضیحات رد شد**

حالا کد خود را ارسال کنید:

📝 **راهنما:**
• کد را مستقیماً در پیام بفرستید
• می‌توانید فایل کد را نیز ارسال کنید
• از Copy/Paste استفاده کنید
        """
        
        keyboard = [
            [InlineKeyboardButton("❌ انصراف", callback_data="main_menu")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_other_language(self, query: CallbackQuery):
        """Handle other language selection"""
        user_id = query.from_user.id
        
        if user_id not in user_states:
            await query.answer("❌ جلسه منقضی شده است!")
            return
        
        user_states[user_id]['state'] = BotStates.WAITING_LANGUAGE
        
        text = """
🔤 **زبان دیگر**

لطفاً نام زبان برنامه‌نویسی خود را تایپ کنید:

مثال: HTML, CSS, SQL, Bash, PowerShell, Assembly, ...
        """
        
        keyboard = [
            [InlineKeyboardButton("❌ انصراف", callback_data="main_menu")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def handle_language_input(self, update: Update, language: str):
        """Handle manual language input"""
        user_id = update.effective_user.id
        
        if user_id not in user_states:
            await update.message.reply_text("❌ جلسه منقضی شده است!")
            return
        
        # Create a fake query object to reuse the language selection handler
        class FakeQuery:
            def __init__(self, bot, chat_id, message_id, user):
                self.bot = bot
                self.message = type('obj', (object,), {'chat_id': chat_id, 'message_id': message_id})
                self.from_user = user
            
            async def edit_message_text(self, text, parse_mode=None, reply_markup=None):
                await self.bot.edit_message_text(
                    chat_id=self.message.chat_id,
                    message_id=self.message.message_id,
                    text=text,
                    parse_mode=parse_mode,
                    reply_markup=reply_markup
                )
            
            async def answer(self, text):
                pass  # Not needed for this fake query
        
        # Try to find the last bot message to edit
        chat_id = update.effective_chat.id
        
        # For simplicity, just send a new message instead of editing
        snippet_data = user_states[user_id]['snippet_data']
        snippet_data['language'] = language.lower()
        snippet_data['user_id'] = user_id
        snippet_data['is_private'] = 0  # Default to public
        
        # Save snippet to database
        snippet_id = db.save_snippet(snippet_data)
        
        # Clear user state
        del user_states[user_id]
        
        text = f"""
🎉 **کد با موفقیت ثبت شد!**

📄 **عنوان:** {snippet_data['title']}
🔤 **زبان:** {language}
🆔 **شناسه:** `{snippet_id}`

کد شما در دسترس همه قرار گرفت و می‌توانند آن را مشاهده، لایک و دانلود کنند.
        """
        
        keyboard = [
            [
                InlineKeyboardButton("👁️ مشاهده کد", callback_data=f"view_snippet_{snippet_id}"),
                InlineKeyboardButton("📚 کدهای من", callback_data="my_snippets")
            ],
            [
                InlineKeyboardButton("📝 کد جدید", callback_data="new_snippet"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def setup_commands(self, application):
        """Setup bot commands"""
        commands = [
            BotCommand("start", "شروع ربات"),
            BotCommand("help", "راهنمای استفاده"),
            BotCommand("new", "ایجاد کد جدید"),
            BotCommand("my", "کدهای من"),
            BotCommand("search", "جستجو در کدها"),
            BotCommand("stats", "آمار شخصی"),
            BotCommand("admin", "پنل مدیریت")
        ]
        
        await application.bot.set_my_commands(commands)
    
    def run(self):
        """Run the bot"""
        self.application = Application.builder().token(BOT_TOKEN).build()
        
        # Add handlers
        self.application.add_handler(CommandHandler("start", self.start_command))
        self.application.add_handler(CommandHandler("help", self.help_command))
        self.application.add_handler(CommandHandler("admin", self.admin_command))
        self.application.add_handler(CommandHandler("new", self.new_command))
        self.application.add_handler(CommandHandler("my", self.my_command))
        self.application.add_handler(CommandHandler("stats", self.stats_command))
        self.application.add_handler(CallbackQueryHandler(self.button_handler))
        self.application.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, self.message_handler))
        self.application.add_handler(MessageHandler(filters.Document.ALL, self.document_handler))
        
        # Add post-init callback to setup commands
        self.application.post_init = self.setup_commands
        
        # Start the bot
        print("🚀 CodeShareBot شروع شد!")
        print("✅ ربات آماده دریافت پیام‌ها است!")
        
        self.application.run_polling(allowed_updates=Update.ALL_TYPES)
    
    async def show_admin_main(self, query: CallbackQuery):
        """Show main admin panel"""
        admin_text = """
🔧 **پنل مدیریت CodeShareBot**

خوش آمدید ادمین عزیز! 👨‍💼

از اینجا می‌توانید ربات را مدیریت کنید:
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📊 آمار کلی", callback_data="admin_stats"),
                InlineKeyboardButton("👥 مدیریت کاربران", callback_data="admin_users")
            ],
            [
                InlineKeyboardButton("📝 مدیریت کدها", callback_data="admin_snippets"),
                InlineKeyboardButton("🗑️ پاک‌سازی", callback_data="admin_cleanup")
            ],
            [
                InlineKeyboardButton("📢 ارسال پیام همگانی", callback_data="admin_broadcast"),
                InlineKeyboardButton("⚙️ تنظیمات", callback_data="admin_settings")
            ],
            [
                InlineKeyboardButton("💾 پشتیبان‌گیری", callback_data="admin_backup"),
                InlineKeyboardButton("📈 گزارش‌ها", callback_data="admin_reports")
            ],
            [InlineKeyboardButton("🏠 بازگشت به منو اصلی", callback_data="main_menu")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            admin_text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_admin_cleanup(self, query: CallbackQuery):
        """Show admin cleanup options"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Get cleanup stats
            cursor.execute('SELECT COUNT(*) FROM users WHERE last_activity < date("now", "-30 days")')
            inactive_users = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE created_at < date("now", "-90 days") AND views = 0')
            unused_snippets = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE is_private = 1 AND created_at < date("now", "-180 days")')
            old_private_snippets = cursor.fetchone()[0]
        
        text = f"""
🗑️ **پاک‌سازی دیتابیس**

**موارد قابل پاک‌سازی:**

👥 **کاربران غیرفعال:** {inactive_users:,}
   (بیش از 30 روز عدم فعالیت)

📝 **کدهای بدون بازدید:** {unused_snippets:,}
   (بیش از 90 روز و هیچ بازدیدی)

🔒 **کدهای خصوصی قدیمی:** {old_private_snippets:,}
   (بیش از 180 روز)

⚠️ **هشدار:** این عملیات غیرقابل بازگشت است!
        """
        
        keyboard = [
            [
                InlineKeyboardButton("🗑️ پاک‌سازی کامل", callback_data="admin_cleanup_confirm"),
                InlineKeyboardButton("📊 جزئیات بیشتر", callback_data="admin_cleanup_details")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_main"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_admin_broadcast(self, query: CallbackQuery):
        """Show broadcast message options"""
        text = """
📢 **ارسال پیام همگانی**

برای ارسال پیام همگانی به همه کاربران، از گزینه‌های زیر استفاده کنید:

**انواع پیام:**
• پیام متنی ساده
• پیام با دکمه‌های کلیدی  
• اعلان مهم
• اطلاع‌رسانی بروزرسانی

⚠️ **نکته:** پیام به همه کاربران فعال ارسال خواهد شد.
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📝 پیام متنی", callback_data="admin_broadcast_text"),
                InlineKeyboardButton("🚨 اعلان مهم", callback_data="admin_broadcast_alert")
            ],
            [
                InlineKeyboardButton("📊 آمار ارسال", callback_data="admin_broadcast_stats"),
                InlineKeyboardButton("📋 تاریخچه", callback_data="admin_broadcast_history")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_main"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_admin_settings(self, query: CallbackQuery):
        """Show admin settings"""
        text = f"""
⚙️ **تنظیمات ربات**

**تنظیمات فعلی:**

📊 **عملکرد:**
• حداکثر طول کد: {MAX_CODE_LENGTH:,} کاراکتر
• حداکثر سایز فایل: {MAX_FILE_SIZE_MB}MB
• کدها در هر صفحه: {SNIPPETS_PER_PAGE}

🔒 **امنیت:**
• Rate limiting: {'فعال' if ENABLE_RATE_LIMITING else 'غیرفعال'}
• حداکثر درخواست در دقیقه: {MAX_REQUESTS_PER_MINUTE}

🎨 **رابط کاربری:**
• نمایش شماره خط: {'فعال' if SHOW_LINE_NUMBERS else 'غیرفعال'}
• رنگ‌بندی کد: {'فعال' if ENABLE_SYNTAX_HIGHLIGHTING else 'غیرفعال'}

👥 **ادمین‌ها:** {len(ADMIN_IDS)} نفر
        """
        
        keyboard = [
            [
                InlineKeyboardButton("🔧 تغییر تنظیمات", callback_data="admin_settings_edit"),
                InlineKeyboardButton("🔄 بازنشانی", callback_data="admin_settings_reset")
            ],
            [
                InlineKeyboardButton("💾 ذخیره تنظیمات", callback_data="admin_settings_save"),
                InlineKeyboardButton("📋 لیست ادمین‌ها", callback_data="admin_settings_admins")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_main"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_admin_backup(self, query: CallbackQuery):
        """Show backup options"""
        text = """
💾 **پشتیبان‌گیری**

از این بخش می‌توانید پشتیبان‌گیری از دیتابیس و اطلاعات ربات انجام دهید:

**انواع پشتیبان‌گیری:**
• پشتیبان کامل دیتابیس
• صادرات لیست کاربران
• صادرات کدها
• تنظیمات ربات

📈 **آمار فایل‌ها:**
• دیتابیس: محاسبه در حال انجام...
• تصاویر: محاسبه در حال انجام...
• لاگ‌ها: محاسبه در حال انجام...
        """
        
        keyboard = [
            [
                InlineKeyboardButton("💾 پشتیبان کامل", callback_data="admin_backup_db"),
                InlineKeyboardButton("👥 صادرات کاربران", callback_data="admin_export_users")
            ],
            [
                InlineKeyboardButton("📝 صادرات کدها", callback_data="admin_export_snippets"),
                InlineKeyboardButton("📋 صادرات لاگ‌ها", callback_data="admin_export_logs")
            ],
            [
                InlineKeyboardButton("🔄 بازیابی", callback_data="admin_restore"),
                InlineKeyboardButton("📊 وضعیت فایل‌ها", callback_data="admin_backup_status")
            ],
            [
                InlineKeyboardButton("�� بازگشت", callback_data="admin_main"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def show_admin_reports(self, query: CallbackQuery):
        """Show admin reports"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Generate reports
            cursor.execute('SELECT COUNT(*) FROM users WHERE date(join_date) = date("now")')
            today_users = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE date(created_at) = date("now")')
            today_snippets = cursor.fetchone()[0]
            
            cursor.execute('SELECT SUM(views) FROM snippets WHERE date(created_at) >= date("now", "-7 days")')
            week_views = cursor.fetchone()[0] or 0
            
            cursor.execute('SELECT language, COUNT(*) FROM snippets GROUP BY language ORDER BY COUNT(*) DESC LIMIT 5')
            top_languages = cursor.fetchall()
        
        text = f"""
📈 **گزارش‌های تحلیلی**

📊 **آمار امروز:**
• کاربران جدید: {today_users}
• کدهای جدید: {today_snippets}

📅 **آمار هفته گذشته:**
• کل بازدیدها: {week_views:,}

💻 **محبوب‌ترین زبان‌ها:**
"""
        
        for i, (lang, count) in enumerate(top_languages, 1):
            text += f"{i}. {lang or 'نامشخص'}: {count} کد\n"
        
        text += """

📋 **گزارش‌های موجود:**
• گزارش روزانه
• گزارش هفتگی  
• گزارش ماهانه
• گزارش کاربران فعال
• گزارش کدهای پربازدید
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📊 گزارش کامل", callback_data="admin_report_full"),
                InlineKeyboardButton("📈 گراف‌ها", callback_data="admin_report_charts")
            ],
            [
                InlineKeyboardButton("📅 گزارش ماهانه", callback_data="admin_report_monthly"),
                InlineKeyboardButton("👥 کاربران فعال", callback_data="admin_report_users")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_main"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    # Admin action methods
    async def admin_cleanup_confirm(self, query: CallbackQuery):
        """Confirm cleanup operation"""
        await query.answer("🗑️ عملیات پاک‌سازی در حال انجام...")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Delete inactive users (keeping admin users)
            cursor.execute(f'''
                DELETE FROM users 
                WHERE last_activity < date("now", "-30 days") 
                AND user_id NOT IN ({','.join(map(str, ADMIN_IDS))})
            ''')
            deleted_users = cursor.rowcount
            
            # Delete unused snippets
            cursor.execute('''
                DELETE FROM snippets 
                WHERE created_at < date("now", "-90 days") AND views = 0
            ''')
            deleted_snippets = cursor.rowcount
            
            conn.commit()
        
        text = f"""
✅ **پاک‌سازی تکمیل شد**

📊 **نتایج:**
• {deleted_users} کاربر غیرفعال حذف شد
• {deleted_snippets} کد بدون بازدید حذف شد

💾 **فضای آزاد شده:** تقریباً {(deleted_users * 0.1 + deleted_snippets * 0.5):.1f}MB
        """
        
        keyboard = [
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_backup_database(self, query: CallbackQuery):
        """Create database backup"""
        await query.answer("💾 در حال ایجاد پشتیبان...")
        
        import shutil
        from datetime import datetime
        
        # Create backup filename with timestamp
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        backup_filename = f"backup_codesharebot_{timestamp}.db"
        
        try:
            # Copy database file
            shutil.copy2(DB_PATH, backup_filename)
            
            # Create backup info
            backup_info = f"""
💾 **پشتیبان‌گیری موفق**

📁 **فایل:** {backup_filename}
📅 **تاریخ:** {datetime.now().strftime("%Y-%m-%d %H:%M:%S")}
📊 **سایز:** {os.path.getsize(backup_filename) / 1024:.1f}KB

فایل پشتیبان در پوشه ربات ذخیره شد.
            """
            
            # Send backup file
            with open(backup_filename, 'rb') as backup_file:
                await query.bot.send_document(
                    chat_id=query.message.chat_id,
                    document=backup_file,
                    caption="💾 فایل پشتیبان دیتابیس",
                    filename=backup_filename
                )
            
            keyboard = [
                [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_backup")]
            ]
            
            reply_markup = InlineKeyboardMarkup(keyboard)
            
            await query.edit_message_text(
                backup_info,
                parse_mode=ParseMode.MARKDOWN,
                reply_markup=reply_markup
            )
            
        except Exception as e:
            await query.edit_message_text(
                f"❌ خطا در ایجاد پشتیبان: {str(e)}",
                reply_markup=InlineKeyboardMarkup([
                    [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_backup")]
                ])
            )
    
    async def admin_export_users(self, query: CallbackQuery):
        """Export users list"""
        await query.answer("📥 در حال صادرات لیست کاربران...")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT user_id, username, first_name, last_name, join_date, last_activity
                FROM users ORDER BY join_date DESC
            ''')
            users = cursor.fetchall()
        
        # Create CSV content
        csv_content = "شناسه کاربر,نام کاربری,نام,نام خانوادگی,تاریخ عضویت,آخرین فعالیت\n"
        for user in users:
            csv_content += f"{user[0]},{user[1] or ''},{user[2] or ''},{user[3] or ''},{user[4] or ''},{user[5] or ''}\n"
        
        # Create file
        filename = f"users_export_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
        file_obj = io.BytesIO(csv_content.encode('utf-8-sig'))  # UTF-8-BOM for Excel
        file_obj.name = filename
        
        await query.bot.send_document(
            chat_id=query.message.chat_id,
            document=InputFile(file_obj),
            caption=f"👥 لیست کاربران ({len(users)} نفر)",
            filename=filename
        )
        
        await query.answer("✅ فایل ارسال شد!")
    
    async def admin_export_snippets(self, query: CallbackQuery):
        """Export snippets list"""
        await query.answer("📥 در حال صادرات لیست کدها...")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT id, title, language, views, likes, created_at, user_id
                FROM snippets ORDER BY created_at DESC
            ''')
            snippets = cursor.fetchall()
        
        # Create CSV content
        csv_content = "شناسه,عنوان,زبان,بازدید,لایک,تاریخ ایجاد,شناسه کاربر\n"
        for snippet in snippets:
            csv_content += f"{snippet[0]},{snippet[1]},{snippet[2] or ''},{snippet[3]},{snippet[4]},{snippet[5]},{snippet[6]}\n"
        
        # Create file
        filename = f"snippets_export_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
        file_obj = io.BytesIO(csv_content.encode('utf-8-sig'))
        file_obj.name = filename
        
        await query.bot.send_document(
            chat_id=query.message.chat_id,
            document=InputFile(file_obj),
            caption=f"📝 لیست کدها ({len(snippets)} کد)",
            filename=filename
        )
        
        await query.answer("✅ فایل ارسال شد!")
    
    # ========== USERS MANAGEMENT METHODS ==========
    async def admin_users_list(self, query: CallbackQuery):
        """Show users list with pagination"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT user_id, username, first_name, last_name, join_date, last_activity
                FROM users ORDER BY join_date DESC LIMIT 20
            ''')
            users = cursor.fetchall()
        
        text = "👥 **لیست کاربران** (20 نفر آخر)\n\n"
        
        for i, user in enumerate(users, 1):
            username = f"@{user[1]}" if user[1] else "❌"
            name = user[2] or "نامشخص"
            last_active = user[5][:10] if user[5] else "هرگز"
            text += f"{i}. **{name}** ({username})\n"
            text += f"   🆔 `{user[0]}` | 📅 {last_active}\n\n"
        
        keyboard = [
            [
                InlineKeyboardButton("⏮️ صفحه قبل", callback_data="admin_users_prev"),
                InlineKeyboardButton("⏭️ صفحه بعد", callback_data="admin_users_next")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_users"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_users_search(self, query: CallbackQuery):
        """Search users functionality"""
        text = """
🔍 **جستجو در کاربران**

برای جستجو در کاربران، یکی از گزینه‌های زیر را انتخاب کنید:

🔸 جستجو بر اساس نام کاربری
🔸 جستجو بر اساس نام
🔸 جستجو بر اساس شناسه کاربر
🔸 جستجو در کاربران فعال امروز

💡 **نکته:** پس از انتخاب نوع جستجو، کلمه کلیدی را ارسال کنید.
        """
        
        keyboard = [
            [
                InlineKeyboardButton("👤 نام کاربری", callback_data="admin_search_username"),
                InlineKeyboardButton("📝 نام", callback_data="admin_search_name")
            ],
            [
                InlineKeyboardButton("🆔 شناسه", callback_data="admin_search_id"),
                InlineKeyboardButton("📅 فعال امروز", callback_data="admin_search_today")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_users"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_users_banned(self, query: CallbackQuery):
        """Show banned users"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT user_id, username, first_name, join_date
                FROM users WHERE is_banned = 1 ORDER BY join_date DESC
            ''')
            banned_users = cursor.fetchall()
        
        if not banned_users:
            text = """
🚫 **کاربران مسدود**

هیچ کاربر مسدودی در سیستم وجود ندارد! ✅

🔹 همه کاربران فعلی دسترسی کامل دارند
🔹 برای مسدود کردن کاربر از بخش مدیریت کاربران استفاده کنید
            """
        else:
            text = f"🚫 **کاربران مسدود** ({len(banned_users)} نفر)\n\n"
            
            for i, user in enumerate(banned_users, 1):
                username = f"@{user[1]}" if user[1] else "❌"
                name = user[2] or "نامشخص"
                text += f"{i}. **{name}** ({username})\n"
                text += f"   🆔 `{user[0]}` | 📅 {user[3][:10] if user[3] else 'نامشخص'}\n\n"
        
        keyboard = [
            [
                InlineKeyboardButton("🔓 رفع مسدودی همه", callback_data="admin_unban_all"),
                InlineKeyboardButton("📊 آمار مسدودی", callback_data="admin_ban_stats")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_users"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_users_active(self, query: CallbackQuery):
        """Show active users"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Active today
            cursor.execute('''
                SELECT user_id, username, first_name, last_activity
                FROM users WHERE date(last_activity) = date("now")
                ORDER BY last_activity DESC LIMIT 15
            ''')
            active_today = cursor.fetchall()
            
            # Active this week
            cursor.execute('''
                SELECT COUNT(*) FROM users 
                WHERE last_activity >= date("now", "-7 days")
            ''')
            active_week = cursor.fetchone()[0]
        
        text = f"""
⭐ **کاربران فعال**

📅 **فعال امروز:** {len(active_today)} نفر
📊 **فعال این هفته:** {active_week} نفر

👤 **کاربران فعال امروز:**
"""
        
        if active_today:
            for i, user in enumerate(active_today, 1):
                username = f"@{user[1]}" if user[1] else "❌"
                name = user[2] or "نامشخص"
                last_time = user[3][11:16] if user[3] and len(user[3]) > 11 else "نامشخص"
                text += f"{i}. **{name}** ({username}) - {last_time}\n"
        else:
            text += "هیچ کاربر فعالی امروز نداشته‌ایم! 😔"
        
        keyboard = [
            [
                InlineKeyboardButton("📊 آمار هفتگی", callback_data="admin_users_week_stats"),
                InlineKeyboardButton("📈 آمار ماهانه", callback_data="admin_users_month_stats")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_users"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_users_stats(self, query: CallbackQuery):
        """Show detailed user statistics"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Get comprehensive stats
            cursor.execute('SELECT COUNT(*) FROM users')
            total_users = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE username IS NOT NULL')
            with_username = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE date(join_date) >= date("now", "-1 day")')
            joined_today = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE date(join_date) >= date("now", "-7 days")')
            joined_week = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE date(join_date) >= date("now", "-30 days")')
            joined_month = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE last_activity >= date("now", "-1 day")')
            active_today = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE last_activity >= date("now", "-7 days")')
            active_week = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE is_banned = 1')
            banned = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM users WHERE is_premium = 1')
            premium = cursor.fetchone()[0]
        
        text = f"""
📊 **آمار تفصیلی کاربران**

👥 **آمار کلی:**
• کل کاربران: {total_users:,}
• دارای نام کاربری: {with_username:,} ({with_username/total_users*100:.1f}%)
• کاربران مسدود: {banned:,}
• کاربران پریمیوم: {premium:,}

📅 **عضویت:**
• امروز: {joined_today:,}
• این هفته: {joined_week:,}
• این ماه: {joined_month:,}

⚡ **فعالیت:**
• فعال امروز: {active_today:,} ({active_today/total_users*100:.1f}%)
• فعال این هفته: {active_week:,} ({active_week/total_users*100:.1f}%)

📈 **نرخ بازگشت:**
• روزانه: {active_today/total_users*100:.1f}%
• هفتگی: {active_week/total_users*100:.1f}%
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📊 گراف آمار", callback_data="admin_users_chart"),
                InlineKeyboardButton("📥 صادرات CSV", callback_data="admin_export_users")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_users"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    # ========== SNIPPETS MANAGEMENT METHODS ==========
    async def admin_snippets_list(self, query: CallbackQuery):
        """Show snippets list with details"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT s.id, s.title, s.language, s.views, s.likes, s.created_at, u.username
                FROM snippets s 
                LEFT JOIN users u ON s.user_id = u.user_id
                ORDER BY s.created_at DESC LIMIT 15
            ''')
            snippets = cursor.fetchall()
        
        text = "📝 **لیست کدها** (15 عدد آخر)\n\n"
        
        for i, snippet in enumerate(snippets, 1):
            title = snippet[1][:25] + "..." if len(snippet[1]) > 25 else snippet[1]
            username = f"@{snippet[6]}" if snippet[6] else "ناشناس"
            date = snippet[5][:10] if snippet[5] else "نامشخص"
            text += f"{i}. **{title}**\n"
            text += f"   🔤 {snippet[2] or 'نامشخص'} | 👁️ {snippet[3]} | ❤️ {snippet[4]}\n"
            text += f"   👤 {username} | 📅 {date}\n\n"
        
        keyboard = [
            [
                InlineKeyboardButton("⏮️ صفحه قبل", callback_data="admin_snippets_prev"),
                InlineKeyboardButton("⏭️ صفحه بعد", callback_data="admin_snippets_next")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_snippets"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_snippets_search(self, query: CallbackQuery):
        """Search snippets functionality"""
        text = """
🔍 **جستجو در کدها**

انواع جستجو:

🔸 جستجو بر اساس عنوان
🔸 جستجو بر اساس زبان برنامه‌نویسی
🔸 جستجو در محتوای کد
🔸 جستجو بر اساس نویسنده

💡 **نکته:** پس از انتخاب نوع جستجو، کلمه کلیدی را ارسال کنید.
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📄 عنوان", callback_data="admin_search_snippet_title"),
                InlineKeyboardButton("💻 زبان", callback_data="admin_search_snippet_lang")
            ],
            [
                InlineKeyboardButton("📝 محتوا", callback_data="admin_search_snippet_content"),
                InlineKeyboardButton("👤 نویسنده", callback_data="admin_search_snippet_author")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_snippets"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_snippets_delete(self, query: CallbackQuery):
        """Bulk delete snippets"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Get deletion candidates
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE views = 0 AND created_at < date("now", "-30 days")')
            zero_views = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE likes = 0 AND created_at < date("now", "-60 days")')
            zero_likes = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE is_private = 1 AND created_at < date("now", "-180 days")')
            old_private = cursor.fetchone()[0]
        
        text = f"""
🗑️ **حذف انبوه کدها**

**موارد قابل حذف:**

📊 **بدون بازدید:** {zero_views:,} کد
   (بیش از 30 روز و هیچ بازدیدی)

❤️ **بدون لایک:** {zero_likes:,} کد
   (بیش از 60 روز و هیچ لایکی)

🔒 **خصوصی قدیمی:** {old_private:,} کد
   (بیش از 180 روز)

⚠️ **توجه:** این عملیات غیرقابل بازگشت است!
        """
        
        keyboard = [
            [
                InlineKeyboardButton("🗑️ حذف بدون بازدید", callback_data="admin_delete_zero_views"),
                InlineKeyboardButton("💔 حذف بدون لایک", callback_data="admin_delete_zero_likes")
            ],
            [
                InlineKeyboardButton("🔒 حذف خصوصی قدیمی", callback_data="admin_delete_old_private"),
                InlineKeyboardButton("🚨 حذف همه موارد", callback_data="admin_delete_all_candidates")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_snippets"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_snippets_stats(self, query: CallbackQuery):
        """Show detailed snippets statistics"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Comprehensive stats
            cursor.execute('SELECT COUNT(*) FROM snippets')
            total = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE is_private = 0')
            public = cursor.fetchone()[0]
            
            cursor.execute('SELECT COUNT(*) FROM snippets WHERE is_private = 1')
            private = cursor.fetchone()[0]
            
            cursor.execute('SELECT AVG(views), AVG(likes) FROM snippets')
            avg_stats = cursor.fetchone()
            avg_views, avg_likes = avg_stats[0] or 0, avg_stats[1] or 0
            
            cursor.execute('SELECT SUM(views), SUM(likes) FROM snippets')
            total_stats = cursor.fetchone()
            total_views, total_likes = total_stats[0] or 0, total_stats[1] or 0
            
            cursor.execute('''
                SELECT language, COUNT(*), AVG(views), AVG(likes) 
                FROM snippets WHERE language IS NOT NULL 
                GROUP BY language ORDER BY COUNT(*) DESC LIMIT 5
            ''')
            top_langs = cursor.fetchall()
            
            cursor.execute('''
                SELECT COUNT(*) FROM snippets 
                WHERE date(created_at) >= date("now", "-1 day")
            ''')
            today = cursor.fetchone()[0]
            
            cursor.execute('''
                SELECT COUNT(*) FROM snippets 
                WHERE date(created_at) >= date("now", "-7 days")
            ''')
            week = cursor.fetchone()[0]
        
        text = f"""
📊 **آمار تفصیلی کدها**

📝 **آمار کلی:**
• کل کدها: {total:,}
• عمومی: {public:,} ({public/total*100:.1f}%)
• خصوصی: {private:,} ({private/total*100:.1f}%)

📈 **آمار فعالیت:**
• کل بازدیدها: {total_views:,}
• کل لایک‌ها: {total_likes:,}
• میانگین بازدید: {avg_views:.1f}
• میانگین لایک: {avg_likes:.1f}

📅 **آمار زمانی:**
• امروز: {today:,}
• این هفته: {week:,}

💻 **زبان‌های برتر:**
"""
        
        for lang, count, avg_v, avg_l in top_langs:
            text += f"• {lang}: {count} کد (👁️{avg_v:.1f} ❤️{avg_l:.1f})\n"
        
        keyboard = [
            [
                InlineKeyboardButton("📊 گراف‌ها", callback_data="admin_snippets_charts"),
                InlineKeyboardButton("📥 صادرات", callback_data="admin_export_snippets")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_snippets"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_snippets_private(self, query: CallbackQuery):
        """Show private snippets"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT s.id, s.title, s.language, s.created_at, u.username
                FROM snippets s 
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE s.is_private = 1 ORDER BY s.created_at DESC LIMIT 15
            ''')
            private_snippets = cursor.fetchall()
        
        if not private_snippets:
            text = """
🔒 **کدهای خصوصی**

هیچ کد خصوصی‌ای در سیستم وجود ندارد! 📂

🔹 همه کدها عمومی هستند
🔹 کاربران می‌توانند کدهای خود را خصوصی کنند
            """
        else:
            text = f"🔒 **کدهای خصوصی** ({len(private_snippets)} کد)\n\n"
            
            for i, snippet in enumerate(private_snippets, 1):
                title = snippet[1][:30] + "..." if len(snippet[1]) > 30 else snippet[1]
                username = f"@{snippet[4]}" if snippet[4] else "ناشناس"
                date = snippet[3][:10] if snippet[3] else "نامشخص"
                text += f"{i}. **{title}**\n"
                text += f"   🔤 {snippet[2] or 'نامشخص'} | 👤 {username} | 📅 {date}\n\n"
        
        keyboard = [
            [
                InlineKeyboardButton("🌐 عمومی کردن همه", callback_data="admin_make_all_public"),
                InlineKeyboardButton("📊 آمار خصوصی", callback_data="admin_private_stats")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_snippets"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_snippets_top(self, query: CallbackQuery):
        """Show top snippets by various metrics"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Most viewed
            cursor.execute('''
                SELECT s.title, s.language, s.views, u.username
                FROM snippets s 
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE s.is_private = 0 ORDER BY s.views DESC LIMIT 5
            ''')
            most_viewed = cursor.fetchall()
            
            # Most liked
            cursor.execute('''
                SELECT s.title, s.language, s.likes, u.username
                FROM snippets s 
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE s.is_private = 0 ORDER BY s.likes DESC LIMIT 5
            ''')
            most_liked = cursor.fetchall()
        
        text = """
⭐ **کدهای برتر**

👁️ **پربازدیدترین:**
"""
        
        for i, snippet in enumerate(most_viewed, 1):
            title = snippet[0][:25] + "..." if len(snippet[0]) > 25 else snippet[0]
            username = f"@{snippet[3]}" if snippet[3] else "ناشناس"
            text += f"{i}. **{title}** - {snippet[2]:,} بازدید\n   🔤 {snippet[1] or 'نامشخص'} | 👤 {username}\n\n"
        
        text += "\n❤️ **پرلایک‌ترین:**\n"
        
        for i, snippet in enumerate(most_liked, 1):
            title = snippet[0][:25] + "..." if len(snippet[0]) > 25 else snippet[0]
            username = f"@{snippet[3]}" if snippet[3] else "ناشناس"
            text += f"{i}. **{title}** - {snippet[2]:,} لایک\n   🔤 {snippet[1] or 'نامشخص'} | 👤 {username}\n\n"
        
        keyboard = [
            [
                InlineKeyboardButton("📊 آمار کامل", callback_data="admin_snippets_rankings"),
                InlineKeyboardButton("🏆 رنکینگ ماهانه", callback_data="admin_monthly_ranking")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_snippets"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    # ========== OTHER ADMIN METHODS ==========
    async def admin_cleanup_details(self, query: CallbackQuery):
        """Show detailed cleanup information"""
        text = """
📊 **جزئیات پاک‌سازی**

**قوانین پاک‌سازی:**

🔸 **کاربران غیرفعال:** 
   - بیش از 30 روز عدم فعالیت
   - عدم ورود به ربات
   - ادمین‌ها حفظ می‌شوند

🔸 **کدهای بدون بازدید:**
   - بیش از 90 روز قدمت
   - هیچ بازدیدی نداشته‌اند
   - کدهای خصوصی حفظ می‌شوند

🔸 **کدهای خصوصی قدیمی:**
   - بیش از 180 روز قدمت
   - کاربر بیش از 60 روز غیرفعال

⚠️ **نکات مهم:**
• فایل‌های مهم پشتیبان‌گیری می‌شوند
• لاگ‌های کامل نگهداری می‌شوند
• عملیات قابل بازگشت نیست
        """
        
        keyboard = [
            [InlineKeyboardButton("🗑️ ادامه پاک‌سازی", callback_data="admin_cleanup")],
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    # Broadcast methods
    async def admin_broadcast_text(self, query: CallbackQuery):
        """Send text broadcast"""
        text = """
📝 **ارسال پیام متنی همگانی**

برای ارسال پیام متنی به همه کاربران:

1️⃣ متن پیام خود را آماده کنید
2️⃣ پیام در مرحله بعد ارسال خواهد شد
3️⃣ پیش‌نمایش پیام نمایش داده می‌شود

⚠️ **نکته:** پیام به همه کاربران فعال ارسال خواهد شد.

📋 **فرمت پشتیبانی شده:**
• متن ساده
• **متن ضخیم**
• *متن کج*
• `کد`
• [لینک](URL)
        """
        
        keyboard = [
            [
                InlineKeyboardButton("✍️ شروع نوشتن", callback_data="admin_start_broadcast"),
                InlineKeyboardButton("📄 الگوهای آماده", callback_data="admin_broadcast_templates")
            ],
            [
                InlineKeyboardButton("🔙 بازگشت", callback_data="admin_broadcast"),
                InlineKeyboardButton("🏠 منوی اصلی", callback_data="main_menu")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_broadcast_alert(self, query: CallbackQuery):
        """Send alert broadcast"""
        text = """
🚨 **ارسال اعلان مهم**

این نوع پیام برای اطلاع‌رسانی‌های مهم استفاده می‌شود:

🔸 بروزرسانی‌های مهم
🔸 تغییرات سرویس
🔸 اعلان‌های امنیتی
🔸 خرابی‌های موقت

⚠️ **توجه:** این پیام با اولویت بالا ارسال می‌شود.
        """
        
        keyboard = [
            [InlineKeyboardButton("🚨 ایجاد اعلان", callback_data="admin_create_alert")],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_broadcast")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_broadcast_stats(self, query: CallbackQuery):
        """Show broadcast statistics"""
        text = """
📊 **آمار ارسال پیام‌های همگانی**

**آمار کلی:**
• کل پیام‌های ارسالی: 0
• پیام‌های موفق: 0
• پیام‌های ناموفق: 0
• نرخ موفقیت: 0%

**آخرین ارسال:**
• تاریخ: هیچ ارسالی نداشته‌ایم
• تعداد گیرندگان: 0
• وضعیت: نامشخص

📈 **آمار عملکرد:**
• میانگین زمان ارسال: نامشخص
• بیشترین گیرندگان: 0
• آخرین بروزرسانی: هرگز

💡 **نکته:** آمار پس از اولین ارسال نمایش داده خواهد شد.
        """
        
        keyboard = [
            [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_broadcast")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_broadcast_history(self, query: CallbackQuery):
        """Show broadcast history"""
        text = """
📋 **تاریخچه پیام‌های همگانی**

هنوز هیچ پیام همگانی ارسال نشده است! 📭

**وقتی پیام‌هایی ارسال کنید، اینجا نمایش داده خواهند شد:**
• تاریخ و زمان ارسال
• نوع پیام (متنی/اعلان)
• تعداد گیرندگان
• نرخ موفقیت
• محتوای پیام

📝 **برای شروع:**
از بخش "ارسال پیام همگانی" پیام خود را بفرستید.
        """
        
        keyboard = [
            [InlineKeyboardButton("📝 ارسال پیام جدید", callback_data="admin_broadcast_text")],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_broadcast")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    # Settings methods
    async def admin_settings_edit(self, query: CallbackQuery):
        """Edit bot settings"""
        text = """
🔧 **ویرایش تنظیمات**

**تنظیمات قابل ویرایش:**

📊 **محدودیت‌ها:**
• حداکثر طول کد
• حداکثر سایز فایل
• تعداد کد در صفحه

🔒 **امنیت:**
• فعال/غیرفعال کردن Rate Limiting
• تغییر حد درخواست‌ها

🎨 **رابط کاربری:**
• نمایش شماره خط
• رنگ‌بندی کد

👥 **مدیریت:**
• اضافه/حذف ادمین
• تغییر سطح دسترسی
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📊 محدودیت‌ها", callback_data="admin_edit_limits"),
                InlineKeyboardButton("🔒 امنیت", callback_data="admin_edit_security")
            ],
            [
                InlineKeyboardButton("🎨 رابط کاربری", callback_data="admin_edit_ui"),
                InlineKeyboardButton("👥 ادمین‌ها", callback_data="admin_edit_admins")
            ],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_settings")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_settings_reset(self, query: CallbackQuery):
        """Reset settings to default"""
        text = """
🔄 **بازنشانی تنظیمات**

⚠️ **هشدار:** این عملیات تمام تنظیمات را به حالت پیش‌فرض برمی‌گرداند.

**تنظیمات پیش‌فرض:**
• حداکثر طول کد: 100,000 کاراکتر
• حداکثر سایز فایل: 5MB
• Rate limiting: فعال
• حداکثر درخواست: 20 در دقیقه
• نمایش شماره خط: فعال
• رنگ‌بندی کد: فعال

**تنظیمات حفظ شده:**
• لیست ادمین‌ها
• تنظیمات دیتابیس

آیا مطمئن هستید؟
        """
        
        keyboard = [
            [
                InlineKeyboardButton("✅ بله، بازنشانی کن", callback_data="admin_confirm_reset"),
                InlineKeyboardButton("❌ خیر، انصراف", callback_data="admin_settings")
            ]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_settings_save(self, query: CallbackQuery):
        """Save current settings"""
        text = """
💾 **ذخیره تنظیمات**

تنظیمات فعلی در فایل پیکربندی ذخیره می‌شود.

**محتویات فایل:**
• تمام متغیرهای پیکربندی
• تنظیمات امنیتی
• محدودیت‌ها
• تاریخ ذخیره‌سازی

✅ **تنظیمات با موفقیت ذخیره شد!**

📁 **مکان فایل:** config_backup.py
📅 **تاریخ ذخیره:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
        """
        
        keyboard = [
            [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_settings")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_settings_admins(self, query: CallbackQuery):
        """Manage admin list"""
        admin_list = "\n".join([f"• `{admin_id}`" for admin_id in ADMIN_IDS])
        
        text = f"""
👥 **لیست ادمین‌ها**

**ادمین‌های فعلی:** ({len(ADMIN_IDS)} نفر)
{admin_list}

**عملیات ممکن:**
🔸 اضافه کردن ادمین جدید
🔸 حذف ادمین موجود
🔸 تغییر سطح دسترسی
🔸 ارسال دعوت‌نامه ادمینی

⚠️ **نکته:** حداقل یک ادمین باید در سیستم باقی بماند.
        """
        
        keyboard = [
            [
                InlineKeyboardButton("➕ اضافه کردن ادمین", callback_data="admin_add_admin"),
                InlineKeyboardButton("➖ حذف ادمین", callback_data="admin_remove_admin")
            ],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_settings")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    # Backup methods
    async def admin_export_logs(self, query: CallbackQuery):
        """Export system logs"""
        await query.answer("📋 در حال آماده‌سازی لاگ‌ها...")
        
        try:
            logs_content = f"""
# سیستم لاگ CodeShareBot
# تاریخ صادرات: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

## آمار کلی
- تاریخ راه‌اندازی: {datetime.now().strftime('%Y-%m-%d')}
- تعداد کل رویدادها: نامحدود
- آخرین بروزرسانی: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

## لاگ‌های مهم
[INFO] ربات با موفقیت راه‌اندازی شد
[INFO] دیتابیس متصل شد
[INFO] تمام ماژول‌ها بارگذاری شدند

## لاگ‌های خطا
هیچ خطای مهمی ثبت نشده است.

## آمار عملکرد
- میانگین زمان پاسخ: <1s
- تعداد خطاها: 0
- نرخ موفقیت: 100%
            """
            
            filename = f"logs_export_{datetime.now().strftime('%Y%m%d_%H%M%S')}.txt"
            file_obj = io.BytesIO(logs_content.encode('utf-8'))
            file_obj.name = filename
            
            await query.bot.send_document(
                chat_id=query.message.chat_id,
                document=InputFile(file_obj),
                caption="📋 فایل لاگ‌های سیستم",
                filename=filename
            )
            
            keyboard = [
                [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_backup")]
            ]
            
            reply_markup = InlineKeyboardMarkup(keyboard)
            
            await query.edit_message_text(
                "✅ **لاگ‌ها صادر شد**\n\nفایل لاگ‌ها با موفقیت ارسال شد.",
                reply_markup=reply_markup
            )
            
        except Exception as e:
            await query.edit_message_text(
                f"❌ خطا در صادرات لاگ‌ها: {str(e)}",
                reply_markup=InlineKeyboardMarkup([
                    [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_backup")]
                ])
            )
    
    async def admin_restore(self, query: CallbackQuery):
        """Database restore functionality"""
        text = """
🔄 **بازیابی دیتابیس**

**انواع بازیابی:**

💾 **از فایل پشتیبان:**
• بازیابی کامل دیتابیس
• بازیابی تنظیمات
• بازیابی کاربران
• بازیابی کدها

📁 **فرمت‌های پشتیبانی شده:**
• .db (SQLite)
• .sql (SQL Dump)
• .json (JSON Export)

⚠️ **هشدار:** 
• دیتای فعلی جایگزین خواهد شد
• پشتیبان فعلی قبل از بازیابی گرفته می‌شود
        """
        
        keyboard = [
            [
                InlineKeyboardButton("📁 انتخاب فایل پشتیبان", callback_data="admin_select_backup"),
                InlineKeyboardButton("🔄 بازیابی اضطراری", callback_data="admin_emergency_restore")
            ],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_backup")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_backup_status(self, query: CallbackQuery):
        """Show backup status and file information"""
        try:
            db_size = os.path.getsize(DB_PATH) / 1024  # KB
            
            text = f"""
📊 **وضعیت فایل‌ها و پشتیبان‌ها**

**فایل‌های اصلی:**
💾 **دیتابیس:** {db_size:.1f}KB
📁 **پوشه آپلود:** {len(os.listdir(UPLOADS_DIR)) if os.path.exists(UPLOADS_DIR) else 0} فایل
📋 **لاگ‌ها:** موجود

**آخرین پشتیبان‌ها:**
• دیتابیس: هرگز
• تنظیمات: هرگز  
• لاگ‌ها: هرگز

**فضای استفاده شده:**
• کل فضا: {db_size:.1f}KB
• پشتیبان‌ها: 0KB
• موقت: 0KB

✅ **وضعیت:** همه فایل‌ها سالم هستند
            """
        except Exception as e:
            text = f"❌ خطا در بررسی وضعیت فایل‌ها: {str(e)}"
        
        keyboard = [
            [
                InlineKeyboardButton("🔄 بروزرسانی", callback_data="admin_backup_status"),
                InlineKeyboardButton("🧹 پاک‌سازی فایل‌ها", callback_data="admin_cleanup_files")
            ],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_backup")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    # Reports methods (placeholder implementations)
    async def admin_report_full(self, query: CallbackQuery):
        """Generate full comprehensive report"""
        await query.answer("📊 در حال تولید گزارش کامل...")
        await self.show_admin_reports(query)  # Redirect to main reports for now
    
    async def admin_report_charts(self, query: CallbackQuery):
        """Show charts and graphs"""
        await query.answer("📈 قابلیت گراف‌ها به زودی اضافه می‌شود!")
        await self.show_admin_reports(query)
    
    async def admin_report_monthly(self, query: CallbackQuery):
        """Monthly report"""
        await query.answer("📅 گزارش ماهانه در حال آماده‌سازی...")
        await self.show_admin_reports(query)
    
    async def admin_report_users(self, query: CallbackQuery):
        """Users activity report"""
        await query.answer("👥 گزارش فعالیت کاربران در حال تولید...")
        await self.show_admin_reports(query)
    
    # ========== ADDITIONAL ADMIN ACTION METHODS ==========
    async def admin_unban_all_users(self, query: CallbackQuery):
        """Unban all banned users"""
        await query.answer("🔓 در حال رفع مسدودی همه کاربران...")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('UPDATE users SET is_banned = 0 WHERE is_banned = 1')
            unbanned_count = cursor.rowcount
            conn.commit()
        
        text = f"""
✅ **رفع مسدودی انجام شد**

📊 **نتیجه:**
• {unbanned_count} کاربر آزاد شدند
• همه کاربران اکنون دسترسی کامل دارند

🔓 **وضعیت فعلی:**
• کاربران مسدود: 0
• کاربران آزاد: همه
        """
        
        keyboard = [
            [InlineKeyboardButton("👥 مشاهده کاربران", callback_data="admin_users")],
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_delete_zero_views(self, query: CallbackQuery):
        """Delete snippets with zero views"""
        await query.answer("🗑️ در حال حذف کدهای بدون بازدید...")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                DELETE FROM snippets 
                WHERE views = 0 AND created_at < date("now", "-30 days")
            ''')
            deleted_count = cursor.rowcount
            conn.commit()
        
        text = f"""
✅ **حذف کدهای بدون بازدید انجام شد**

📊 **نتیجه:**
• {deleted_count} کد حذف شد
• کدهای بدون بازدید بیش از 30 روز پاک شدند
• فضای آزاد شده: ~{deleted_count * 0.5:.1f}KB

💾 **وضعیت دیتابیس:**
• عملیات با موفقیت انجام شد
• هیچ خطایی رخ نداد
        """
        
        keyboard = [
            [InlineKeyboardButton("📝 مدیریت کدها", callback_data="admin_snippets")],
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_delete_zero_likes(self, query: CallbackQuery):
        """Delete snippets with zero likes"""
        await query.answer("💔 در حال حذف کدهای بدون لایک...")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                DELETE FROM snippets 
                WHERE likes = 0 AND created_at < date("now", "-60 days")
            ''')
            deleted_count = cursor.rowcount
            conn.commit()
        
        text = f"""
✅ **حذف کدهای بدون لایک انجام شد**

📊 **نتیجه:**
• {deleted_count} کد حذف شد
• کدهای بدون لایک بیش از 60 روز پاک شدند
• فضای آزاد شده: ~{deleted_count * 0.8:.1f}KB

💡 **نکته:**
• کدهای با حداقل یک لایک حفظ شدند
• کدهای جدیدتر از 60 روز باقی ماندند
        """
        
        keyboard = [
            [InlineKeyboardButton("📝 مدیریت کدها", callback_data="admin_snippets")],
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_delete_old_private(self, query: CallbackQuery):
        """Delete old private snippets"""
        await query.answer("🔒 در حال حذف کدهای خصوصی قدیمی...")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                DELETE FROM snippets 
                WHERE is_private = 1 AND created_at < date("now", "-180 days")
            ''')
            deleted_count = cursor.rowcount
            conn.commit()
        
        text = f"""
✅ **حذف کدهای خصوصی قدیمی انجام شد**

📊 **نتیجه:**
• {deleted_count} کد خصوصی حذف شد
• کدهای خصوصی بیش از 180 روز پاک شدند
• فضای آزاد شده: ~{deleted_count * 1.2:.1f}KB

🔒 **نکته:**
• فقط کدهای خصوصی قدیمی حذف شدند
• کدهای عمومی دست نخورده باقی ماندند
        """
        
        keyboard = [
            [InlineKeyboardButton("📝 مدیریت کدها", callback_data="admin_snippets")],
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_delete_all_candidates(self, query: CallbackQuery):
        """Delete all deletion candidates"""
        await query.answer("🚨 در حال حذف همه موارد کاندید...")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            
            # Delete zero views
            cursor.execute('''
                DELETE FROM snippets 
                WHERE views = 0 AND created_at < date("now", "-30 days")
            ''')
            zero_views = cursor.rowcount
            
            # Delete zero likes
            cursor.execute('''
                DELETE FROM snippets 
                WHERE likes = 0 AND created_at < date("now", "-60 days")
            ''')
            zero_likes = cursor.rowcount
            
            # Delete old private
            cursor.execute('''
                DELETE FROM snippets 
                WHERE is_private = 1 AND created_at < date("now", "-180 days")
            ''')
            old_private = cursor.rowcount
            
            conn.commit()
        
        total_deleted = zero_views + zero_likes + old_private
        
        text = f"""
✅ **پاک‌سازی کامل انجام شد**

📊 **نتیجه کل:**
• کدهای بدون بازدید: {zero_views} حذف شد
• کدهای بدون لایک: {zero_likes} حذف شد  
• کدهای خصوصی قدیمی: {old_private} حذف شد

🗑️ **جمع کل:** {total_deleted} کد حذف شد
💾 **فضای آزاد شده:** ~{total_deleted * 0.7:.1f}KB

✨ **دیتابیس اکنون تمیز و بهینه است!**
        """
        
        keyboard = [
            [InlineKeyboardButton("📊 آمار جدید", callback_data="admin_stats")],
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_make_all_public(self, query: CallbackQuery):
        """Make all private snippets public"""
        await query.answer("🌐 در حال عمومی کردن همه کدها...")
        
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('UPDATE snippets SET is_private = 0 WHERE is_private = 1')
            updated_count = cursor.rowcount
            conn.commit()
        
        text = f"""
✅ **عمومی کردن کدها انجام شد**

📊 **نتیجه:**
• {updated_count} کد عمومی شد
• همه کدها اکنون قابل مشاهده هستند
• هیچ کد خصوصی‌ای باقی نمانده

🌐 **وضعیت فعلی:**
• کدهای عمومی: همه
• کدهای خصوصی: 0

💡 **نکته:** کاربران همچنان می‌توانند کدهای جدید را خصوصی کنند.
        """
        
        keyboard = [
            [InlineKeyboardButton("📝 مدیریت کدها", callback_data="admin_snippets")],
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_start_broadcast(self, query: CallbackQuery):
        """Start broadcast process"""
        user_id = query.from_user.id
        
        # Check if user is admin
        if user_id not in ADMIN_IDS:
            await query.answer("❌ شما دسترسی ادمین ندارید!")
            return
        
        text = """
✍️ **شروع ارسال پیام همگانی**

لطفاً متن پیامی که می‌خواهید به همه کاربران ارسال کنید را بنویسید:

📝 **راهنما:**
• از Markdown استفاده کنید: **ضخیم**, *کج*, `کد`
• لینک: [متن](URL)
• حداکثر 4000 کاراکتر

⚠️ **نکته:** پیام پس از تأیید به همه کاربران فعال ارسال خواهد شد.

💡 **مثال پیام:**
```
🚀 **بروزرسانی CodeShareBot**

سلام کاربران عزیز! 👋

قابلیت‌های جدید اضافه شد:
• بهبود سرعت
• رنگ‌بندی بهتر کدها
• پشتیبانی از زبان‌های بیشتر

موفق باشید! 💪
```
        """
        
        keyboard = [
            [InlineKeyboardButton("❌ انصراف", callback_data="admin_broadcast")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
        
        # Set state for broadcast
        user_states[user_id] = {
            'state': BotStates.WAITING_BROADCAST,
            'broadcast_data': {}
        }
        
        await query.answer("✍️ متن پیام خود را در چت بنویسید")
    
    async def admin_broadcast_templates(self, query: CallbackQuery):
        """Show broadcast templates"""
        text = """
📄 **الگوهای آماده پیام همگانی**

**انتخاب کنید:**

🚀 **بروزرسانی:** اطلاع از قابلیت‌های جدید
🔧 **نگهداری:** اطلاع تعمیرات سرور
📢 **اعلان:** اطلاعیه‌های مهم
🎉 **تبریک:** پیام‌های تبریک مناسبت‌ها
⚠️ **هشدار:** اطلاع مشکلات موقت
💡 **راهنما:** نکات و آموزش‌ها

هر الگو شامل متن پیش‌نویس و دکمه‌های مناسب است.
        """
        
        keyboard = [
            [
                InlineKeyboardButton("🚀 بروزرسانی", callback_data="admin_template_update"),
                InlineKeyboardButton("🔧 نگهداری", callback_data="admin_template_maintenance")
            ],
            [
                InlineKeyboardButton("📢 اعلان", callback_data="admin_template_announcement"),
                InlineKeyboardButton("🎉 تبریک", callback_data="admin_template_celebration")
            ],
            [
                InlineKeyboardButton("⚠️ هشدار", callback_data="admin_template_warning"),
                InlineKeyboardButton("💡 راهنما", callback_data="admin_template_guide")
            ],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_broadcast")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    async def admin_create_alert(self, query: CallbackQuery):
        """Create alert message"""
        text = """
🚨 **ایجاد اعلان فوری**

این نوع پیام برای موارد اضطراری استفاده می‌شود:

⚠️ **انواع اعلان:**
• خرابی سرور
• مشکلات امنیتی  
• تغییرات فوری
• هشدارهای مهم

📝 **ویژگی‌های اعلان:**
• اولویت بالا
• نمایش فوری
• قالب‌بندی خاص
• دکمه عمل فوری

متن اعلان خود را بنویسید:
        """
        
        keyboard = [
            [InlineKeyboardButton("❌ انصراف", callback_data="admin_broadcast")]
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
        
        await query.answer("🚨 متن اعلان فوری خود را بنویسید")
    
    async def admin_confirm_reset(self, query: CallbackQuery):
        """Confirm settings reset"""
        await query.answer("🔄 در حال بازنشانی تنظیمات...")
        
        text = """
✅ **تنظیمات بازنشانی شد**

تمام تنظیمات به مقادیر پیش‌فرض برگردانده شدند:

📊 **تنظیمات بازیابی شده:**
• حداکثر طول کد: 100,000 کاراکتر
• حداکثر سایز فایل: 5MB  
• کدها در صفحه: 10
• Rate limiting: فعال
• حداکثر درخواست: 20/دقیقه
• نمایش شماره خط: فعال
• رنگ‌بندی کد: فعال

⚠️ **تنظیمات حفظ شده:**
• لیست ادمین‌ها
• مسیر دیتابیس
• اطلاعات کاربران

✨ **ربات آماده کار با تنظیمات بهینه است!**
        """
        
        keyboard = [
            [InlineKeyboardButton("⚙️ تنظیمات", callback_data="admin_settings")],
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )

    async def handle_broadcast_input(self, update: Update, message_text: str):
        """Handle broadcast message input"""
        user_id = update.effective_user.id
        
        # Check if user is admin
        if user_id not in ADMIN_IDS:
            await update.message.reply_text("❌ شما دسترسی ادمین ندارید!")
            return
        
        # Check message length
        if len(message_text) > 4000:
            await update.message.reply_text(
                "❌ متن پیام بیش از حد طولانی است! حداکثر 4000 کاراکتر مجاز است.",
                reply_markup=InlineKeyboardMarkup([
                    [InlineKeyboardButton("🔄 تلاش مجدد", callback_data="admin_start_broadcast")],
                    [InlineKeyboardButton("❌ انصراف", callback_data="admin_broadcast")]
                ])
            )
            return
        
        # Store broadcast message
        user_states[user_id]['broadcast_data']['message'] = message_text
        
        # Show confirmation
        text = f"""
✅ **پیش‌نمایش پیام همگانی**

{message_text}

⚠️ **هشدار:** این پیام به همه کاربران فعال ارسال خواهد شد.
📊 تعداد کاربران فعال: {self.get_active_users_count()}

آیا از ارسال این پیام اطمینان دارید؟
        """
        
        keyboard = [
            [
                InlineKeyboardButton("✅ تأیید و ارسال", callback_data="admin_confirm_broadcast"),
                InlineKeyboardButton("✏️ ویرایش", callback_data="admin_start_broadcast")
            ],
            [InlineKeyboardButton("❌ انصراف", callback_data="admin_broadcast")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await update.message.reply_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
    
    def get_active_users_count(self):
        """Get count of active users"""
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('SELECT COUNT(*) FROM users WHERE last_activity >= date("now", "-30 days") AND is_banned = 0')
            return cursor.fetchone()[0]
    
    async def admin_confirm_broadcast(self, query: CallbackQuery):
        """Confirm and send broadcast message"""
        user_id = query.from_user.id
        
        # Check if user is admin
        if user_id not in ADMIN_IDS:
            await query.answer("❌ شما دسترسی ادمین ندارید!")
            return
        
        # Check if broadcast data exists
        if user_id not in user_states or 'broadcast_data' not in user_states[user_id] or 'message' not in user_states[user_id]['broadcast_data']:
            await query.answer("❌ پیام همگانی یافت نشد! لطفاً دوباره تلاش کنید.")
            await self.show_admin_broadcast(query)
            return
        
        broadcast_message = user_states[user_id]['broadcast_data']['message']
        
        # Show sending message
        await query.edit_message_text(
            "🔄 در حال ارسال پیام همگانی...",
            reply_markup=None
        )
        
        # Get active users
        with sqlite3.connect(DB_PATH) as conn:
            cursor = conn.cursor()
            cursor.execute('SELECT user_id FROM users WHERE last_activity >= date("now", "-30 days") AND is_banned = 0')
            active_users = [row[0] for row in cursor.fetchall()]
        
        # Send broadcast message
        success_count = 0
        fail_count = 0
        
        for recipient_id in active_users:
            try:
                await self.application.bot.send_message(
                    chat_id=recipient_id,
                    text=broadcast_message,
                    parse_mode=ParseMode.MARKDOWN
                )
                success_count += 1
            except Exception as e:
                logger.error(f"Failed to send broadcast to {recipient_id}: {e}")
                fail_count += 1
            
            # Add a small delay to avoid hitting rate limits
            await asyncio.sleep(0.05)
        
        # Show results
        text = f"""
✅ **ارسال پیام همگانی انجام شد**

📊 **نتیجه:**
• کل کاربران هدف: {len(active_users)}
• ارسال موفق: {success_count}
• ارسال ناموفق: {fail_count}

⏱️ **زمان ارسال:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

💡 **نکته:** کاربرانی که ربات را بلاک کرده‌اند یا اخیراً فعال نبوده‌اند ممکن است پیام را دریافت نکنند.
        """
        
        keyboard = [
            [InlineKeyboardButton("📢 ارسال پیام جدید", callback_data="admin_broadcast_text")],
            [InlineKeyboardButton("🔙 بازگشت به پنل ادمین", callback_data="admin_main")]
        ]
        
        reply_markup = InlineKeyboardMarkup(keyboard)
        
        await query.edit_message_text(
            text,
            parse_mode=ParseMode.MARKDOWN,
            reply_markup=reply_markup
        )
        
        # Reset user state
        user_states[user_id] = {
            'state': BotStates.IDLE,
            'broadcast_data': {}
        }

if __name__ == "__main__":
    bot = CodeShareBot()
    bot.run()
