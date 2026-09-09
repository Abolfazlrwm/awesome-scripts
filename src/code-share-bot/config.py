# 🔧 تنظیمات CodeShareBot

import os

# 🤖 تنظیمات ربات
BOT_TOKEN = os.getenv("BOT_TOKEN", "")
ADMIN_IDS = [
    123456789,  # آیدی ادمین اول - این را با آیدی تلگرام خود جایگزین کنید
    # 987654321,  # آیدی ادمین دوم (در صورت نیاز uncomment کنید)
]

# 📁 تنظیمات دیتابیس و فایل‌ها
DB_PATH = "codeshare_bot.db"
UPLOADS_DIR = "uploads"

# 📊 تنظیمات عملکرد
MAX_CODE_LENGTH = 100000  # حداکثر طول کد (کاراکتر) - افزایش یافته برای فایل‌های بزرگ
MAX_TITLE_LENGTH = 200    # حداکثر طول عنوان - افزایش یافته
MAX_DESC_LENGTH = 1000    # حداکثر طول توضیحات - افزایش یافته
SNIPPETS_PER_PAGE = 10    # تعداد کد در هر صفحه
MAX_FILE_SIZE_MB = 5      # حداکثر سایز فایل (مگابایت)

# 🎨 تنظیمات رابط کاربری
DEFAULT_LANGUAGE = "text"
SHOW_LINE_NUMBERS = True
ENABLE_SYNTAX_HIGHLIGHTING = True

# 🔒 تنظیمات امنیتی
ENABLE_RATE_LIMITING = True
MAX_REQUESTS_PER_MINUTE = 20
BAN_DURATION_HOURS = 24

# 📱 پیام‌های سیستم
WELCOME_MESSAGE = """
🌟 **خوش آمدید به CodeShareBot** 🌟

سلام {name}! 👋

من یک ربات پیشرفته برای اشتراک‌گذاری کدهای برنامه‌نویسی هستم.
"""

ERROR_MESSAGES = {
    "bot_token_missing": "❌ توکن ربات تنظیم نشده است!",
    "admin_not_set": "⚠️ هیچ ادمینی تعریف نشده است!",
    "database_error": "❌ خطا در اتصال به دیتابیس!",
    "permission_denied": "❌ شما دسترسی لازم را ندارید!",
    "code_too_long": f"❌ کد شما بیش از {MAX_CODE_LENGTH:,} کاراکتر است! (حدود {MAX_FILE_SIZE_MB}MB)",
    "title_too_long": f"❌ عنوان باید کمتر از {MAX_TITLE_LENGTH} کاراکتر باشد!",
    "file_too_large": f"❌ فایل بیش از {MAX_FILE_SIZE_MB}MB است!",
    "invalid_language": "❌ زبان برنامه‌نویسی نامعتبر است!",
    "snippet_not_found": "❌ کد مورد نظر پیدا نشد!",
    "rate_limit_exceeded": "⏰ لطفاً کمی صبر کنید و دوباره تلاش کنید!",
    "file_download_error": "❌ خطا در دانلود فایل. لطفاً دوباره تلاش کنید!",
    "encoding_error": "❌ خطای رمزگشایی فایل. فایل‌های UTF-8, ASCII یا Latin-1 پشتیبانی می‌شوند!",
}

SUCCESS_MESSAGES = {
    "snippet_created": "🎉 کد با موفقیت ایجاد شد!",
    "snippet_deleted": "🗑️ کد حذف شد!",
    "snippet_liked": "❤️ کد لایک شد!",
    "snippet_unliked": "💔 لایک برداشته شد!",
    "file_sent": "📥 فایل ارسال شد!",
}

# 💻 زبان‌های برنامه‌نویسی پشتیبانی شده
SUPPORTED_LANGUAGES = {
    'python': {'name': 'Python', 'ext': '.py', 'emoji': '🐍'},
    'javascript': {'name': 'JavaScript', 'ext': '.js', 'emoji': '🟨'},
    'typescript': {'name': 'TypeScript', 'ext': '.ts', 'emoji': '🔷'},
    'java': {'name': 'Java', 'ext': '.java', 'emoji': '☕'},
    'cpp': {'name': 'C++', 'ext': '.cpp', 'emoji': '⚡'},
    'c': {'name': 'C', 'ext': '.c', 'emoji': '🔧'},
    'csharp': {'name': 'C#', 'ext': '.cs', 'emoji': '💜'},
    'php': {'name': 'PHP', 'ext': '.php', 'emoji': '🐘'},
    'go': {'name': 'Go', 'ext': '.go', 'emoji': '🐹'},
    'rust': {'name': 'Rust', 'ext': '.rs', 'emoji': '🦀'},
    'swift': {'name': 'Swift', 'ext': '.swift', 'emoji': '🍎'},
    'kotlin': {'name': 'Kotlin', 'ext': '.kt', 'emoji': '🟪'},
    'ruby': {'name': 'Ruby', 'ext': '.rb', 'emoji': '💎'},
    'html': {'name': 'HTML', 'ext': '.html', 'emoji': '🌐'},
    'css': {'name': 'CSS', 'ext': '.css', 'emoji': '🎨'},
    'sql': {'name': 'SQL', 'ext': '.sql', 'emoji': '🗃️'},
    'bash': {'name': 'Bash', 'ext': '.sh', 'emoji': '🐚'},
    'powershell': {'name': 'PowerShell', 'ext': '.ps1', 'emoji': '💙'},
    'json': {'name': 'JSON', 'ext': '.json', 'emoji': '📋'},
    'yaml': {'name': 'YAML', 'ext': '.yml', 'emoji': '📄'},
    'xml': {'name': 'XML', 'ext': '.xml', 'emoji': '📃'},
    'markdown': {'name': 'Markdown', 'ext': '.md', 'emoji': '📝'},
}

# 🎯 راهنمای استفاده
HELP_TEXT = """
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

**ویژگی‌های خاص:**
🎨 رنگ‌بندی خودکار کدها
📥 دانلود کدها به صورت فایل
📊 آمار مشاهده و لایک
🔍 جستجوی پیشرفته
"""

# 📞 اطلاعات پشتیبانی
SUPPORT_INFO = {
    "email": "support@codesharebot.com",
    "telegram": "@CodeShareBot_Support",
    "website": "www.codesharebot.com",
    "github": "https://github.com/username/CodeShareBot",
}

# 🔄 تنظیمات لاگ
LOG_LEVEL = "INFO"
LOG_FORMAT = "%(asctime)s - %(name)s - %(levelname)s - %(message)s"
LOG_FILE = "codesharebot.log"

def validate_config():
    """اعتبارسنجی تنظیمات"""
    if BOT_TOKEN == "YOUR_BOT_TOKEN_HERE":
        raise ValueError(ERROR_MESSAGES["bot_token_missing"])
    
    if not ADMIN_IDS or ADMIN_IDS == [123456789]:
        print("⚠️ هشدار: آیدی ادمین تنظیم نشده است!")
    
    return True 