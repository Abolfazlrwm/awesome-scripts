# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os
from dotenv import load_dotenv

# Load environment variables from .env file if it exists
load_dotenv()

# Bot token
TOKEN = os.getenv("TOKEN", "PUT-YOUR-BOT-TOKEN")

# Admin user ID
ADMIN_ID = int(os.getenv("ADMIN_ID", "7135477742"))

# Database configuration
DB_FILE = "bot_database.db"

# NSFW Content Detection API
NSFW_API_KEY = os.getenv("NSFW_API_KEY", "")
NSFW_API_URL = "https://api.deepai.org/api/nsfw-detector"

# Banned words and phrases (in Persian/Farsi)
BANNED_WORDS = [
    "کیری", "کس", "کون", "جنده", "گاییدم", "کیر", "سکس", 
    "لاشی", "بی ناموس", "بی شرف", "حرومزاده", "گوه", "عوضی",
    
    # Extended list
    "کصکش", "کص", "جاکش", "مادر جنده", "پدرسگ", "گاییدن", "گایید", "کسکش", 
    "کوس", "کوص", "کیرم", "کسخل", "بی پدر", "دیوث", "دیوس", "قرمساق", "قرمصاق",
    "کونی", "گوز", "کصخل", "کوسخل", "شاش", "شاشیدن", "ریدن", "گوزیدن", "مادرتو",
    "خارکسده", "خارکصده", "کیرخر", "کصمغز", "کصمشنگ", "جق", "جقی", "منی", "آب کیر",
    "داگ استایل", "سگی", "حشری", "فاحشه", "لختی", "لخت", "مالیدن", "بکن", "بکنمت",
    "بکن توش", "بکن بکن", "خفه شو", "خفه خون", "گمشو", "مرتیکه", "زنیکه", "پورن",
    "پورنو", "سوراخ کون", "کص لیس", "کص لیسی", "کیر خور", "کیر خوری", "ساک", "ساک زدن",
    "تخم", "تخمی", "تخم سگ", "ممه", "دول", "شهوتی", "شهوت", "منی", "ارضا", "ارضاء"
]

# Link detection patterns
LINK_PATTERNS = [
    r'https?://\S+', 
    r'www\.\S+', 
    r't\.me/\S+',
    r'@\w+',
    r'telegram\.me/\S+',
    # Add more link patterns as needed
]

# Design elements
GLASS_DESIGN = {
    # Modern Glass Design Elements
    "header": "┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓",
    "footer": "┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛",
    "separator": "┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫",
    "side": "┃",
    "corner_top_left": "┏",
    "corner_top_right": "┓",
    "corner_bottom_left": "┗",
    "corner_bottom_right": "┛",
    
    # Icons
    "bullet": "•",
    "check": "✅",
    "cross": "❌",
    "warning": "⚠️",
    "info": "ℹ️",
    "success": "✓",
    "error": "✗",
    "admin": "👑",
    "user": "👤",
    "group": "👥",
    "settings": "⚙️",
    "stats": "📊",
    "rules": "📜",
    "mute": "🔇",
    "unmute": "🔊",
    "ban": "🚫",
    "unban": "✅",
    "warn": "⚠️",
    "unwarn": "🔄",
    "link": "🔗",
    "profanity": "🤬",
    "porn": "🔞",
    "forward": "↪️",
    "welcome": "👋",
    "strict": "🔒",
    "lock": "🔐",
    "unlock": "🔓",
    "message_limit": "📝",
    "custom_commands": "🤖",
    "add": "➕",
    "remove": "➖",
    "back": "◀️",
    "close": "❌",
    "clock": "🕒",
    "calendar": "📅",
    "timer": "⏱️",
    "location": "📍",
    "timed_lock": "⏳",
    
    # Button Styles
    "button": {
        "normal": "〔 [TEXT] 〕",
        "selected": "『 [TEXT] 』",
        "danger": "【 [TEXT] 】",
        "success": "「 [TEXT] 」",
        "info": "{ [TEXT] }",
        "primary": "┃ [TEXT] ┃",
        "secondary": "┊ [TEXT] ┊",
        "disabled": "┄ [TEXT] ┄",
    },
    
    # Decorative Elements
    "star": "★",
    "diamond": "♦",
    "heart": "♥",
    "sparkle": "✨",
    "fire": "🔥",
    "cool": "😎",
    "crown": "👑",
    "divider": "•───────────────•",
    "bullet_point": "◉",
    "arrow_right": "➤",
    "arrow_left": "◄",
    
    # Status Indicators
    "online": "🟢",
    "offline": "⚫",
    "busy": "🔴",
    "away": "🟠",
    "status": "📊",
    
    # New icons for better design
    "gem": "💎",
    "shield": "🛡️",
    "key": "🔑",
    "globe": "🌐",
    "lightning": "⚡",
    "bell": "🔔",
    "gift": "🎁",
    "rocket": "🚀",
    "target": "🎯",
    "trophy": "🏆",
    "medal": "🏅",
    "chart": "📊",
    "money": "💰",
    "bulb": "💡",
    "hammer": "🔨",
    "tools": "🛠️",
    "flower": "🌸",
    "leaf": "🍃",
    "crystal": "🔮",
    "diamond_blue": "🔷",
    "diamond_orange": "🔶",
}

# Welcome message with glass design
WELCOME_MESSAGE = f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["sparkle"]} *به گروه خوش آمدید* {GLASS_DESIGN["welcome"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["user"]} *نام:* {{name}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["group"]} *گروه:* {{group_name}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}} | {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["footer"]}
"""

# Bot command descriptions
COMMANDS = {
    "start": "شروع کار با ربات",
    "help": "نمایش راهنمای ربات",
    "ban": "حذف کاربر از گروه",
    "unban": "رفع محدودیت کاربر",
    "mute": "بی‌صدا کردن کاربر",
    "unmute": "لغو بی‌صدا کردن کاربر",
    "warn": "اخطار به کاربر",
    "unwarn": "حذف اخطار کاربر",
    "rules": "نمایش قوانین گروه",
    "setrules": "تنظیم قوانین گروه",
    "admin": "دسترسی به پنل ادمین",
    "settings": "تنظیمات گروه",
    "stats": "آمار گروه",
    "currency": "نمایش قیمت ارزها",
    "arz": "نمایش قیمت ارزها",
    "crypto": "نمایش قیمت ارزهای دیجیتال",
    "gold": "نمایش قیمت طلا و سکه",
    "top": "نمایش کاربران فعال گروه",
    "time": "نمایش ساعت و تاریخ ایران",
    "promote": "ارتقا کاربر به ادمین",
    "demote": "عزل کاربر از ادمینی",
    "admins": "نمایش لیست ادمین‌ها",
    "info": "نمایش اطلاعات کاربر",
    "user": "نمایش اطلاعات کاربر",
    "userinfo": "نمایش اطلاعات کاربر"
}

# Admin panel settings
ADMIN_PANEL_SETTINGS = [
    "ضد لینک",
    "ضد فحش",
    "ضد فوروارد",
    "خوشامدگویی",
    "حالت سختگیرانه",
    "قفل گروه",
    "محدودیت پیام"
]

# Default group rules with glass design
DEFAULT_RULES = f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["rules"]} *قوانین گروه*
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["bullet_point"]} احترام به همه اعضا الزامی است
{GLASS_DESIGN["side"]} {GLASS_DESIGN["bullet_point"]} ارسال محتوای نامناسب ممنوع است
{GLASS_DESIGN["side"]} {GLASS_DESIGN["bullet_point"]} ارسال لینک ممنوع است
{GLASS_DESIGN["side"]} {GLASS_DESIGN["bullet_point"]} فوروارد پیام از کانال‌ها ممنوع است
{GLASS_DESIGN["side"]} {GLASS_DESIGN["bullet_point"]} از فرستادن پیام‌های تکراری خودداری کنید
{GLASS_DESIGN["footer"]}
"""

# Message templates with glass design
MESSAGE_TEMPLATES = {
    "ban": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["ban"]} *کاربر از گروه اخراج شد* {GLASS_DESIGN["shield"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["user"]} *کاربر:* {{user}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["admin"]} *توسط:* {{admin}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["info"]} *دلیل:* {{reason}}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}} | {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["footer"]}
""",
    "warn": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["warn"]} *به کاربر اخطار داده شد* {GLASS_DESIGN["bell"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["user"]} *کاربر:* {{user}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["warning"]} *تعداد اخطار:* {{warn_count}}/3
{GLASS_DESIGN["side"]} {GLASS_DESIGN["info"]} *دلیل:* {{reason}}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}} | {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["footer"]}
""",
    "mute": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["mute"]} *کاربر بی‌صدا شد* {GLASS_DESIGN["key"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["user"]} *کاربر:* {{user}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["admin"]} *توسط:* {{admin}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["timer"]} *مدت زمان:* {{duration}}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}} | {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["footer"]}
""",
    "nsfw_detected": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["porn"]} *محتوای نامناسب حذف شد* {GLASS_DESIGN["shield"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["user"]} *کاربر:* {{user}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["info"]} *نوع محتوا:* {{content_type}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}} | {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["footer"]}
""",
    "timed_lock": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["timed_lock"]} *گروه برای مدت مشخص قفل شد* {GLASS_DESIGN["key"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["admin"]} *توسط:* {{admin}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["timer"]} *مدت زمان:* {{duration}}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ شروع:* {{start_date}} | {GLASS_DESIGN["clock"]} *ساعت شروع:* {{start_time}}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ پایان:* {{end_date}} | {GLASS_DESIGN["clock"]} *ساعت پایان:* {{end_time}}
{GLASS_DESIGN["footer"]}
""",
    "unlock": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["unlock"]} *گروه از حالت قفل خارج شد* {GLASS_DESIGN["key"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["admin"]} *توسط:* {{admin}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}} | {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["footer"]}
""",
    # New message templates
    "currency": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["money"]} *نرخ ارز و طلا در بازار آزاد* {GLASS_DESIGN["chart"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}} | {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["separator"]}
{{content}}
{GLASS_DESIGN["footer"]}
""",
    "crypto": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["globe"]} *قیمت ارزهای دیجیتال* {GLASS_DESIGN["lightning"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["crystal"]} ┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄ {GLASS_DESIGN["crystal"]}
{{content}}
{GLASS_DESIGN["footer"]}
""",
    "gold": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["gem"]} *قیمت طلا و سکه* {GLASS_DESIGN["trophy"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["crystal"]} ┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄ {GLASS_DESIGN["crystal"]}
{{content}}
{GLASS_DESIGN["footer"]}
""",
    # اضافه کردن قالب جدید برای پیام اخطار اسپم
    "spam_warning": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["warning"]} *اخطار* {GLASS_DESIGN["bell"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["user"]} *کاربر:* {{user}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["info"]} ارسال پیام‌های مکرر (اسپم) در گروه ممنوع است
{GLASS_DESIGN["side"]} {GLASS_DESIGN["warning"]} *تعداد اخطار:* {{warn_count}}/3
{GLASS_DESIGN["side"]} {GLASS_DESIGN["diamond_blue"]} ┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄ {GLASS_DESIGN["diamond_blue"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["footer"]}
""",
    # اضافه کردن قالب جدید برای پیام بی‌صدا کردن بعد از 3 اخطار
    "mute_after_warns": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["mute"]} *کاربر به دلیل دریافت 3 اخطار بی‌صدا شد* {GLASS_DESIGN["key"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["user"]} *کاربر:* {{user}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["timer"]} *مدت زمان:* 24 ساعت
{GLASS_DESIGN["side"]} {GLASS_DESIGN["shield"]} اخطارهای کاربر پاک شد
{GLASS_DESIGN["side"]} {GLASS_DESIGN["diamond_orange"]} ┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄ {GLASS_DESIGN["diamond_orange"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} *تاریخ:* {{date}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["clock"]} *ساعت:* {{time}}
{GLASS_DESIGN["footer"]}
""",
    # اضافه کردن قالب جدید برای نمایش اطلاعات کاربر
    "user_info": f"""
{GLASS_DESIGN["header"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["sparkle"]} <b>اطلاعات کاربر</b> {GLASS_DESIGN["gem"]}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["user"]} <b>نام:</b> {{name}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["info"]} <b>شناسه کاربری:</b> {{user_id}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["globe"]} <b>نام کاربری:</b> {{username}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} <b>تاریخ ساخت حساب:</b> {{creation_date}}
{GLASS_DESIGN["separator"]}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["message_limit"]} <b>تعداد پیام‌ها:</b> {{message_count}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["warning"]} <b>تعداد اخطارها:</b> {{warnings}}/3
{GLASS_DESIGN["side"]} {GLASS_DESIGN["status"]} <b>وضعیت:</b> {{status}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["crown"]} <b>نقش:</b> {{role}}
{GLASS_DESIGN["side"]} {GLASS_DESIGN["calendar"]} <b>تاریخ:</b> {{date}} | {GLASS_DESIGN["clock"]} <b>ساعت:</b> {{time}}
{GLASS_DESIGN["footer"]}
"""
} 