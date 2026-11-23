# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os
import importlib.util
import re
from telegram import (
    Update, InlineKeyboardButton, InlineKeyboardMarkup,
    InlineQueryResultArticle, InputTextMessageContent
)
from telegram.ext import (
    Application, CommandHandler, CallbackQueryHandler,
    ContextTypes, InlineQueryHandler
)
from telegram.constants import ParseMode

TOKEN = "PUT-YOUR-BOT-TOKEN"  # Replace with your actual bot token

categories_farsi = {
    "⚙️ مدیریت": ["بن", "بن کل", "آن بن", "سکوت", "حذف پیام", "قفل گروه", "پنل",
                "عضویت اجباری", "پاکسازی پیشرفته", "مدیریت ربات", "تنظیمات پیشرفته", "تگ کاربران", 
                "فیلتر کلمه", "مدیریت اخطار", "تگ", "لفت کانال/گروه", "دعوت اجباری", 
                "پیام‌های حذف شده", "کپچا"],
    "🧩 ابزارها": ["خالی", "پیام‌همگانی", "کامنت خودکار", "کپی‌محتوا", "دانلودپیشرفته", 
                 "نرخ‌ارز/تبدیل‌واحد", "اطلاعات اکانت", "استخراج‌متن‌ازتصاویر",
                 "وضعیت‌ربات", "تغییرسایزتصاویر", "استریم2", "تصویر از وب", "استریم1", 
                 "فونت نوشتن", "ترجمه پیشرفته", "متن به گفتار", "وضعیت تایپ", "ضد خیانت", 
                 "استخراج استیکر", "مبدل‌استیکر/تصویر", "پیشرفته", "اسپم", "دانلود استوری", 
                 "شناسایی آهنگ", "ویدیومعمولی‌به‌گرد", "دانلودراینستاگرام", "دانلودریوتیوب", 
                 "اشتراک‌ فایل", "هک گیم", "ذخیره رسانه", "کنترل تاس"],
    "⏱ زمان و پروفایل": ["تایم", "تایم‌بیو", "تایم‌اسم", "زمان‌پروفایل", "کپی‌پروفایل‌دیگران", 
                        "امضا پیام", "قفل پیوی"],
    "📨 پیام‌ها": ["پاسخ خودکار", "خالی", "ارسال خودکار پیام", "تنظیمات دشمن", 
                "مدیریت فایل یکبارمصرف", "تغییر نام فایل ارسال شده", "سین‌خودکار‌چنل", 
                "سین‌خودکار‌گروه", "مدیریت‌پیوی", "تنظیمات‌آنلاین‌بودن‌اکانت", "دانلود رسانه"],
    "🎨 افکت‌ها": ["آنتی‌لاگین", "حذف‌پس‌زمینه‌تصاویر", "پلی‌موزیک", "عضویت‌اجباری"],
    "👋 خوش‌آمد": ["خوش آمدید"]
}

farsi_name_map = {}

def escape_markdown(text):
    to_escape = r'_*[]()~`>#+-=|{}.!'
    return re.sub(f'([{re.escape(to_escape)}])', r'\\\1', text)

def sanitize_markdown_v2(text):
    text = re.sub(r"__([^_]+)__", r"__\1__", text)
    text = re.sub(r"\*\*([^*]+)\*\*", r"**\1**", text)
    text = re.sub(r"~~([^~]+)~~", r"~~\1~~", text)
    text = re.sub(r"`([^`]+)`", r"`\1`", text)
    return escape_markdown(text)

def get_plugins():
    return sorted([
        f[:-3] for f in os.listdir("plugins")
        if f.endswith(".py") and f != "__init__.py" and not f.startswith("__")
    ], key=lambda x: x.lower())

def get_categorized_plugins():
    categories = {
        "⚙️ مدیریت": ["ban", "banall", "unban", "mute", "delmessages", "manage", "panel",
                    "force", "multiCleaner", "plugin_manager", "settings", "tagall", 
                    "filter", "warn", "tag", "leaveall", "forceinv", "deletelog", "captcha"],
        "🧩 ابزارها": ["base", "cast", "comments", "copier", "dl", "finance", "id", "ocr",
                   "ping", "resize", "rtmp", "screenshot", "stream", "textformat",
                   "translate", "tts", "typing", "zed", "Extractsticker",
                   "tosticker", "afk", "spam", "story", "shazam", "roundvideo", 
                   "instagram", "yt", "upload", "hack", "save", "dice"],
        "⏱ زمان و پروفایل": ["time", "timebio", "timename", "timepfp", "profile", 
                            "sign_mode", "pvlock"],
        "📨 پیام‌ها": ["answer", "auto", "autosender", "enemy", "ephemeral_media", 
                    "filerename", "markreadchannel", "markreadgroup", "markreadpv", 
                    "online", "dll"],
        "🎨 افکت‌ها": ["anti", "bg", "play", "TelebotCraft"],
        "👋 خوش‌آمد": ["welcome"]
    }

    for cat, names in zip(categories.keys(), categories_farsi.values()):
        for en, fa in zip(categories[cat], names):
            farsi_name_map[en] = fa

    all_categorized = sum(categories.values(), [])
    uncategorized = [p for p in get_plugins() if p not in all_categorized]
    if uncategorized:
        categories["🔍 سایر"] = uncategorized

    return categories

def get_help(plugin_name):
    def process_text(text):
        # Convert common markdown to Telegram's format
        text = re.sub(r'\*\*(.*?)\*\*', r'*\1*', text)  # **bold** to *bold*
        text = re.sub(r'__(.*?)__', r'_\1_', text)      # __italic__ to _italic_
        
        # Split into code blocks and normal text
        parts = re.split(r'(`.*?`)', text, flags=re.DOTALL)
        
        # Escape special characters only outside code blocks
        escaped_parts = []
        special_chars = r'_*[]()~`>#+-=|{}.!'
        
        for i, part in enumerate(parts):
            if i % 2 == 0:  # Normal text
                # Escape special characters
                part = re.sub(f'([{re.escape(special_chars)}])', r'\\\1', part)
                # Preserve bullets and arrows
                part = part.replace('•', '•')
                part = part.replace('➔', '➔')
            else:  # Code block
                # Remove surrounding backticks temporarily
                code_content = part[1:-1]
                # Escape backticks inside code blocks
                code_content = code_content.replace('`', '\\`')
                # Preserve code block formatting
                part = f'`{code_content}`'
                
            escaped_parts.append(part)
            
        return ''.join(escaped_parts)

    try:
        spec = importlib.util.spec_from_file_location(
            f"plugins.{plugin_name}",
            f"plugins/{plugin_name}.py"
        )
        module = importlib.util.module_from_spec(spec)
        spec.loader.exec_module(module)
        raw_help = getattr(module, "HELP", "ℹ️ راهنمایی برای این پلاگین موجود نیست.")
        return process_text(raw_help)
        
    except Exception as e:
        return f"⚠️ خطا در بارگذاری پلاگین: {process_text(str(e))}"

def create_category_menu(user_id):
    categories = get_categorized_plugins()
    keyboard = [
        [InlineKeyboardButton(f"{name} • {len(plugins)}", 
         callback_data=f"category:{name}:{user_id}")]
        for name, plugins in categories.items()
    ]
    keyboard.append([
        InlineKeyboardButton(
            f"📋 همه پلاگین‌ها ({sum(len(p) for p in categories.values())})", 
            callback_data=f"all_plugins:{user_id}"
        )
    ])
    return keyboard

def create_grid_layout(items, user_id, columns=2, category=None):
    keyboard = []
    for i in range(0, len(items), columns):
        row = items[i:i+columns]
        keyboard.append([
            InlineKeyboardButton(
                f"📁 {farsi_name_map.get(item, item)}",
                callback_data=f"help:{item}:{category if category else 'all'}:{user_id}"
            ) for item in row
        ])
    keyboard.append([
        InlineKeyboardButton("🔙 بازگشت", callback_data=f"main_menu:{user_id}")
    ])
    return keyboard

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    keyboard = create_category_menu(user_id)
    total_plugins = sum(len(p) for p in get_categorized_plugins().values())
    await update.message.reply_text(
        f"🎛 *دسته‌بندی پلاگین‌ها*\n📦 تعداد کل: {total_plugins}",
        reply_markup=InlineKeyboardMarkup(keyboard),
        parse_mode=ParseMode.MARKDOWN_V2
    )

async def inline_query_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.inline_query.from_user.id
    if update.inline_query.query.strip() == "_help":
        keyboard = create_category_menu(user_id)
        total = sum(len(p) for p in get_categorized_plugins().values())
        content = InputTextMessageContent(
            f"🎛 *منوی پلاگین‌ها*\n📦 تعداد: {total}",
            parse_mode=ParseMode.MARKDOWN_V2
        )
        results = [InlineQueryResultArticle(
            id="1",
            title="📚 مشاهده دسته‌بندی پلاگین‌ها",
            input_message_content=content,
            reply_markup=InlineKeyboardMarkup(keyboard)
        )]
        await update.inline_query.answer(results, cache_time=0)

async def button_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    data = query.data
    current_user_id = query.from_user.id
    try:
        parts = data.split(":")
        action = parts[0]

        if action == "help":
            plugin, category, owner_id = parts[1], parts[2], parts[3]
            if str(current_user_id) != owner_id:
                await query.answer("⛔ فقط کاربر منو می‌تونه باهاش کار کنه!", show_alert=True)
                return
            help_text = get_help(plugin)
            back = [[InlineKeyboardButton("🔙 برگشت", 
                    callback_data=f"back_to_plugins:{category}:{owner_id}")]]
            await query.edit_message_text(
                f"📘 راهنمای پلاگین: {farsi_name_map.get(plugin, plugin)}\n\n{help_text}",
                reply_markup=InlineKeyboardMarkup(back),
                parse_mode=ParseMode.MARKDOWN_V2
            )

        elif action == "back_to_plugins":
            category = parts[1]
            owner_id = parts[2]
            plugins = get_categorized_plugins().get(category, []) if category != "all" else get_plugins()
            keyboard = create_grid_layout(plugins, owner_id, category=category)
            await query.edit_message_text(
                f"📂 دسته: {category} • تعداد: {len(plugins)}",
                reply_markup=InlineKeyboardMarkup(keyboard),
                parse_mode=ParseMode.MARKDOWN_V2
            )

        elif action == "category":
            category, owner_id = parts[1], parts[2]
            plugins = get_categorized_plugins().get(category, [])
            keyboard = create_grid_layout(plugins, owner_id, category=category)
            await query.edit_message_text(
                f"📂 دسته: {category} • تعداد: {len(plugins)}",
                reply_markup=InlineKeyboardMarkup(keyboard),
                parse_mode=ParseMode.MARKDOWN_V2
            )

        elif action == "all_plugins":
            owner_id = parts[1]
            plugins = get_plugins()
            keyboard = create_grid_layout(plugins, owner_id)
            await query.edit_message_text(
                f"📃 همه پلاگین‌ها • تعداد: {len(plugins)}",
                reply_markup=InlineKeyboardMarkup(keyboard),
                parse_mode=ParseMode.MARKDOWN_V2
            )

        elif action == "main_menu":
            owner_id = parts[1]
            keyboard = create_category_menu(owner_id)
            total = sum(len(p) for p in get_categorized_plugins().values())
            await query.edit_message_text(
                f"🎛 *دسته‌بندی پلاگین‌ها*\n📦 تعداد کل: {total}",
                reply_markup=InlineKeyboardMarkup(keyboard),
                parse_mode=ParseMode.MARKDOWN_V2
            )

    except Exception as e:
        await query.answer(f"❗ خطا: {escape_markdown(str(e))}", show_alert=True)

def main():
    app = Application.builder().token(TOKEN).build()
    app.add_handler(CommandHandler("start", start))
    app.add_handler(CallbackQueryHandler(button_handler))
    app.add_handler(InlineQueryHandler(inline_query_handler))
    print("🤖 ربات فعال شد...")
    app.run_polling()

if __name__ == "__main__":
    main()