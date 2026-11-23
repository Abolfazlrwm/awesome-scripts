# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
#AdminSupport::@VizmoLain_Support
import telegram
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import Application, CommandHandler, MessageHandler, CallbackQueryHandler, filters, ContextTypes
import sqlite3
import random
import time
import os
import ffmpeg
import logging
from datetime import datetime, timedelta


TOKEN = "YOUR_BOT_TOKEN"  # توکن ربات را اینجا وارد کنید
CHANNELS = ["@Channel2", "@Channel3"]  # کانال‌های پیش‌فرض
ADMINS = [123456, 654321, 987654, 111222, 333444, 555666]  # آیدی ادمین‌ها
PASSWORD = "88643"  # رمز پیش‌فرض
CONTENT_PER_PAGE = 5  # تعداد محتوا در هر صفحه
SPAM_LIMIT = 5  # محدودیت درخواست در ثانیه
BAN_DURATION = 10  # مدت بن به ثانیه
PREMIUM_DURATIONS = {
    "1month": timedelta(days=30),
    "3months": timedelta(days=90),
    "6months": timedelta(days=180),
    "12months": timedelta(days=365)
}


logging.basicConfig(format='%(asctime)s - %(name)s - %(levelname)s - %(message)s', level=logging.INFO)
logger = logging.getLogger(__name__)


def init_db():
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute('''CREATE TABLE IF NOT EXISTS users (user_id INTEGER PRIMARY KEY, last_request REAL, banned_until REAL, points INTEGER DEFAULT 0, premium_until REAL, language TEXT DEFAULT 'fa')''')
    c.execute('''CREATE TABLE IF NOT EXISTS contents (content_id TEXT PRIMARY KEY, category TEXT, file_path TEXT, original_path TEXT, file_type TEXT, is_premium INTEGER DEFAULT 0)''')
    c.execute('''CREATE TABLE IF NOT EXISTS messages (user_id INTEGER, message TEXT, timestamp REAL)''')
    c.execute('''CREATE TABLE IF NOT EXISTS logs (user_id INTEGER, action TEXT, timestamp REAL)''')
    c.execute('''CREATE TABLE IF NOT EXISTS categories (name TEXT PRIMARY KEY)''')
    conn.commit()
    categories = [
        "فریلنسری", "برنامه‌نویسی", "طراحی گرافیک", "دیجیتال مارکتینگ", "مدیریت پروژه",
        "هوش مصنوعی", "تحلیل داده", "امنیت سایبری", "توسعه وب", "توسعه اپلیکیشن",
        "سئو", "تدوین ویدیو", "عکاسی", "انیمیشن‌سازی", "آموزش عمومی", "Premium"
    ]
    for cat in categories:
        c.execute("INSERT OR IGNORE INTO categories (name) VALUES (?)", (cat,))
    conn.commit()
    conn.close()


async def check_membership(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    for channel in CHANNELS:
        try:
            member = await context.bot.get_chat_member(channel, user_id)
            if member.status not in ['member', 'administrator', 'creator']:
                return False
        except:
            return False
    return True


def check_premium(user_id):
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT premium_until FROM users WHERE user_id = ?", (user_id,))
    result = c.fetchone()
    conn.close()
    if result and result[0] and result[0] > time.time():
        return True
    return False

# فشرده‌سازی فایل
def compress_file(input_path, output_path, file_type):
    try:
        if file_type == "video":
            stream = ffmpeg.input(input_path)
            stream = ffmpeg.output(stream, output_path, vcodec='libx265', crf=30, preset='ultrafast', acodec='aac', audio_bitrate='128k')
            ffmpeg.run(stream)
        else:  # photo
            stream = ffmpeg.input(input_path)
            stream = ffmpeg.output(stream, output_path, q=85)
            ffmpeg.run(stream)
    except Exception as e:
        logger.error(f"Error compressing file: {e}")


def generate_content_id():
    return str(random.randint(1000000, 9999999))


def log_action(user_id, action):
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("INSERT INTO logs (user_id, action, timestamp) VALUES (?, ?, ?)", (user_id, action, time.time()))
    conn.commit()
    conn.close()

# منوی اصلی
async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not await check_membership(update, context):
        keyboard = [[InlineKeyboardButton(f"عضویت در {channel}", url=f"https://t.me/{channel[1:]}")] for channel in CHANNELS]
        await update.message.reply_text("لطفاً ابتدا در کانال‌های زیر عضو شوید:", reply_markup=InlineKeyboardMarkup(keyboard))
        return
    user_id = update.effective_user.id
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("INSERT OR IGNORE INTO users (user_id, last_request, points) VALUES (?, ?, ?)", (user_id, 0, 0))
    c.execute("UPDATE users SET points = points + 1 WHERE user_id = ?", (user_id,))
    conn.commit()
    conn.close()
    log_action(user_id, "شروع ربات")
    
    keyboard = [
        [InlineKeyboardButton("ارتباط", callback_data="contact")],
        [InlineKeyboardButton("لیست محتوا", callback_data="content_list")],
        [InlineKeyboardButton("جستجوی فیلم", callback_data="search")],
        [InlineKeyboardButton("راهنما", callback_data="help")],
        [InlineKeyboardButton("تغییر زبان", callback_data="change_language")],
    ]
    await update.message.reply_text("به ربات خوش آمدید! گزینه مورد نظر را انتخاب کنید:", reply_markup=InlineKeyboardMarkup(keyboard))

# بررسی اسپم
async def check_spam(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT last_request, banned_until FROM users WHERE user_id = ?", (user_id,))
    result = c.fetchone()
    current_time = time.time()
    
    if result:
        last_request, banned_until = result
        if banned_until and current_time < banned_until:
            await update.message.reply_text(f"شما تا {int(banned_until - current_time)} ثانیه دیگر مسدود هستید.")
            conn.close()
            return False
        if current_time - last_request < SPAM_LIMIT:
            c.execute("UPDATE users SET banned_until = ? WHERE user_id = ?", (current_time + BAN_DURATION, user_id))
            conn.commit()
            await update.message.reply_text(f"شما به دلیل درخواست سریع بن شدید. {BAN_DURATION} ثانیه صبر کنید.")
            conn.close()
            return False
        c.execute("UPDATE users SET last_request = ? WHERE user_id = ?", (current_time, user_id))
    else:
        c.execute("INSERT INTO users (user_id, last_request) VALUES (?, ?)", (user_id, current_time))
    conn.commit()
    conn.close()
    return True


async def content_list(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.message.reply_text("لطفاً رمز را وارد کنید:")

async def check_password(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not await check_spam(update, context):
        return
    if update.message.text == PASSWORD:
        conn = sqlite3.connect('bot.db')
        c = conn.cursor()
        c.execute("SELECT name FROM categories")
        categories = [row[0] for row in c.fetchall()]
        conn.close()
        keyboard = [[InlineKeyboardButton(cat, callback_data=f"cat_{cat}")] for cat in categories]
        keyboard.append([InlineKeyboardButton("بازگشت", callback_data="back_to_main")])
        await update.message.reply_text("دسته‌بندی مورد نظر را انتخاب کنید:", reply_markup=InlineKeyboardMarkup(keyboard))
    else:
        await update.message.reply_text("رمز اشتباه است. دوباره تلاش کنید.")


async def show_category(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = update.effective_user.id
    category = query.data.split("_")[1]
    if category == "Premium" and not check_premium(user_id):
        await query.message.reply_text("⚠️ این بخش فقط برای کاربران پرمیوم است.")
        return
    
    page = int(context.user_data.get("page", 1))
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT content_id, file_path, file_type FROM contents WHERE category = ? AND (is_premium = 0 OR ?)", 
              (category, 1 if check_premium(user_id) else 0))
    contents = c.fetchall()
    conn.close()
    
    total_contents = len(contents)
    start = (page - 1) * CONTENT_PER_PAGE
    end = start + CONTENT_PER_PAGE
    current_contents = contents[start:end]
    
    keyboard = []
    for content_id, file_path, file_type in current_contents:
        keyboard.append([InlineKeyboardButton(f"محتوا {content_id}", callback_data=f"content_{content_id}")])
    if end < total_contents:
        keyboard.append([InlineKeyboardButton("ارسال بقیه فیلم‌ها", callback_data=f"next_{category}_{page + 1}")])
    keyboard.append([InlineKeyboardButton("بازگشت", callback_data="content_list")])
    
    await query.message.reply_text(f"محتوای دسته {category}:\n⚠️ ذخیره یا اسکرین‌شات از محتوا ممنوع است.", 
                                  reply_markup=InlineKeyboardMarkup(keyboard))
    log_action(user_id, f"مشاهده دسته {category}")

# نمایش محتوای خاص
async def show_content(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = update.effective_user.id
    content_id = query.data.split("_")[1]
    
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT file_path, original_path, file_type, is_premium FROM contents WHERE content_id = ?", (content_id,))
    result = c.fetchone()
    conn.close()
    
    if result:
        file_path, original_path, file_type, is_premium = result
        if is_premium and not check_premium(user_id):
            await query.message.reply_text("⚠️ این محتوا فقط برای کاربران پرمیوم است.")
            return
        send_path = original_path if os.path.exists(original_path) else file_path
        if file_type == "video":
            await query.message.reply_video(open(send_path, 'rb'), caption=f"محتوا {content_id}\n⚠️ ذخیره یا اسکرین‌شات ممنوع است.", 
                                           supports_streaming=True)
        else:
            await query.message.reply_photo(open(send_path, 'rb'), caption=f"محتوا {content_id}\n⚠️ ذخیره یا اسکرین‌شات ممنوع است.")
        for channel in CHANNELS:
            keyboard = [[InlineKeyboardButton(f"مشاهده محتوا {content_id}", url=f"t.me/{context.bot.username}?start={content_id}")]]
            await context.bot.send_message(channel, f"محتوا جدید: {content_id}", reply_markup=InlineKeyboardMarkup(keyboard))
        log_action(user_id, f"مشاهده محتوا {content_id}")
    else:
        await query.message.reply_text("محتوا یافت نشد.")


async def search_content(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.message.reply_text("لطفاً شماره 7 رقمی محتوا را وارد کنید:")
    log_action(update.effective_user.id, "جستجوی محتوا")

async def process_search(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not await check_spam(update, context):
        return
    content_id = update.message.text
    user_id = update.effective_user.id
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT file_path, original_path, file_type, is_premium FROM contents WHERE content_id = ?", (content_id,))
    result = c.fetchone()
    conn.close()
    
    if result:
        file_path, original_path, file_type, is_premium = result
        if is_premium and not check_premium(user_id):
            await update.message.reply_text("⚠️ این محتوا فقط برای کاربران پرمیوم است.")
            return
        send_path = original_path if os.path.exists(original_path) else file_path
        if file_type == "video":
            await update.message.reply_video(open(send_path, 'rb'), caption=f"محتوا {content_id}\n⚠️ ذخیره یا اسکرین‌شات ممنوع است.", 
                                            supports_streaming=True)
        else:
            await update.message.reply_photo(open(send_path, 'rb'), caption=f"محتوا {content_id}\n⚠️ ذخیره یا اسکرین‌شات ممنوع است.")
        log_action(user_id, f"جستجوی موفق {content_id}")
    else:
        await update.message.reply_text("محتوا یافت نشد.")
        log_action(user_id, f"جستجوی ناموفق {content_id}")


async def contact(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.message.reply_text("پیام خود را بنویسید تا به ادمین ارسال شود:")
    log_action(update.effective_user.id, "ارتباط با ادمین")

async def forward_to_admin(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not await check_spam(update, context):
        return
    user_id = update.effective_user.id
    message = update.message.text
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("INSERT INTO messages (user_id, message, timestamp) VALUES (?, ?, ?)", (user_id, message, time.time()))
    c.execute("UPDATE users SET points = points + 5 WHERE user_id = ?", (user_id,))
    conn.commit()
    conn.close()
    
    await context.bot.send_message("@moderet_bot", f"پیام از کاربر {user_id}:\n{message}")
    await update.message.reply_text("پیام شما به ادمین ارسال شد.")
    log_action(user_id, "ارسال پیام به ادمین")


async def help_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = update.effective_user.id
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT language FROM users WHERE user_id = ?", (user_id,))
    language = c.fetchone()[0]
    conn.close()
    
    if language == "fa":
        text = """
        راهنمای استفاده از ربات:
        - از دکمه «لیست محتوا» برای مشاهده ویدیوها و عکس‌ها استفاده کنید.
        - برای دسترسی به محتوای پرمیوم، باید حساب پرمیوم داشته باشید.
        - از دکمه «جستجوی فیلم» برای پیدا کردن محتوا با آیدی استفاده کنید.
        - با دکمه «ارتباط» می‌توانید با ادمین تماس بگیرید.
        - برای کسب امتیاز، دوستان خود را دعوت کنید یا پیام ارسال کنید.
        ⚠️ ذخیره یا اسکرین‌شات از محتوا ممنوع است.
        """
    else:
        text = """
        Bot Usage Guide:
        - Use the "Content List" button to view videos and photos.
        - Premium content requires a premium account.
        - Use the "Search Content" button to find content by ID.
        - Contact the admin using the "Contact" button.
        - Earn points by inviting friends or sending messages.
        ⚠️ Saving or taking screenshots of content is prohibited.
        """
    await query.message.reply_text(text)
    log_action(user_id, "مشاهده راهنما")


async def change_language(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    keyboard = [
        [InlineKeyboardButton("فارسی", callback_data="lang_fa")],
        [InlineKeyboardButton("English", callback_data="lang_en")],
    ]
    await query.message.reply_text("زبان مورد نظر را انتخاب کنید:", reply_markup=InlineKeyboardMarkup(keyboard))
    log_action(update.effective_user.id, "تغییر زبان")

async def set_language(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = update.effective_user.id
    language = query.data.split("_")[1]
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("UPDATE users SET language = ? WHERE user_id = ?", (language, user_id))
    conn.commit()
    conn.close()
    await query.message.reply_text("زبان تغییر کرد." if language == "fa" else "Language changed.")
    log_action(user_id, f"تغییر زبان به {language}")


async def admin_panel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    if user_id not in ADMINS:
        await update.message.reply_text("شما دسترسی به پنل ادمین ندارید.")
        return
    
    keyboard = [
        [InlineKeyboardButton("آمار کاربران", callback_data="stats")],
        [InlineKeyboardButton("ارسال پیام همگانی", callback_data="broadcast")],
        [InlineKeyboardButton("مدیریت کانال‌ها", callback_data="manage_channels")],
        [InlineKeyboardButton("مدیریت محتوا", callback_data="manage_content")],
        [InlineKeyboardButton("افزودن محتوا", callback_data="add_content")],
        [InlineKeyboardButton("مدیریت پرمیوم", callback_data="manage_premium")],
        [InlineKeyboardButton("مدیریت دسته‌بندی‌ها", callback_data="manage_categories")],
        [InlineKeyboardButton("مشاهده لاگ‌ها", callback_data="view_logs")],
        [InlineKeyboardButton("مدیریت بن کاربران", callback_data="manage_bans")],
        [InlineKeyboardButton("ارسال پیام به کاربر", callback_data="message_user")],
    ]
    await update.message.reply_text("پنل ادمین:", reply_markup=InlineKeyboardMarkup(keyboard))
    log_action(user_id, "ورود به پنل ادمین")


async def stats(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT COUNT(*) FROM users")
    user_count = c.fetchone()[0]
    c.execute("SELECT COUNT(*) FROM users WHERE premium_until > ?", (time.time(),))
    premium_count = c.fetchone()[0]
    c.execute("SELECT category, COUNT(*) FROM contents GROUP BY category")
    category_stats = c.fetchall()
    conn.close()
    
    text = f"تعداد کاربران: {user_count}\nتعداد کاربران پرمیوم: {premium_count}\nآمار دسته‌بندی‌ها:\n"
    for cat, count in category_stats:
        text += f"{cat}: {count} محتوا\n"
    await query.message.reply_text(text)
    log_action(update.effective_user.id, "مشاهده آمار")


async def broadcast(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.message.reply_text("پیام همگانی را وارد کنید:")
    context.user_data["broadcast"] = True
    log_action(update.effective_user.id, "شروع ارسال پیام همگانی")

async def send_broadcast(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.user_data.get("broadcast"):
        return
    message = update.message.text
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT user_id FROM users")
    users = c.fetchall()
    conn.close()
    
    for user_id in users:
        try:
            await context.bot.send_message(user_id[0], message)
        except:
            continue
    await update.message.reply_text("پیام همگانی ارسال شد.")
    context.user_data["broadcast"] = False
    log_action(update.effective_user.id, "ارسال پیام همگانی")


async def manage_channels(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    keyboard = [[InlineKeyboardButton(channel, callback_data=f"remove_channel_{channel}")] for channel in CHANNELS]
    keyboard.append([InlineKeyboardButton("افزودن کانال", callback_data="add_channel")])
    await query.message.reply_text("کانال‌های فعلی:", reply_markup=InlineKeyboardMarkup(keyboard))
    log_action(update.effective_user.id, "مدیریت کانال‌ها")

async def add_channel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.message.reply_text("لطفاً آیدی کانال جدید را وارد کنید (مثل @ChannelName):")
    context.user_data["add_channel"] = True
    log_action(update.effective_user.id, "افزودن کانال جدید")

async def process_add_channel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.user_data.get("add_channel"):
        return
    channel = update.message.text
    if channel.startswith("@"):
        CHANNELS.append(channel)
        await update.message.reply_text(f"کانال {channel} اضافه شد.")
        log_action(update.effective_user.id, f"کانال {channel} اضافه شد")
    else:
        await update.message.reply_text("آیدی کانال نامعتبر است.")
    context.user_data["add_channel"] = False


async def manage_content(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT name FROM categories")
    categories = [row[0] for row in c.fetchall()]
    conn.close()
    keyboard = [[InlineKeyboardButton(cat, callback_data=f"manage_cat_{cat}")] for cat in categories]
    await query.message.reply_text("دسته‌بندی برای مدیریت:", reply_markup=InlineKeyboardMarkup(keyboard))
    log_action(update.effective_user.id, "مدیریت محتوا")

async def manage_category(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    category = query.data.split("_")[2]
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT content_id FROM contents WHERE category = ?", (category,))
    contents = c.fetchall()
    conn.close()
    
    keyboard = [[InlineKeyboardButton(f"حذف محتوا {content_id[0]}", callback_data=f"delete_content_{content_id[0]}")] for content_id in contents]
    keyboard.append([InlineKeyboardButton("تغییر به پرمیوم", callback_data=f"make_premium_{category}")])
    keyboard.append([InlineKeyboardButton("بازگشت", callback_data="manage_content")])
    await query.message.reply_text(f"مدیریت محتوای {category}:", reply_markup=InlineKeyboardMarkup(keyboard))
    log_action(update.effective_user.id, f"مدیریت دسته {category}")

async def delete_content(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    content_id = query.data.split("_")[2]
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT file_path, original_path FROM contents WHERE content_id = ?", (content_id,))
    result = c.fetchone()
    c.execute("DELETE FROM contents WHERE content_id = ?", (content_id,))
    conn.commit()
    conn.close()
    if result:
        file_path, original_path = result
        if os.path.exists(file_path):
            os.remove(file_path)
        if os.path.exists(original_path):
            os.remove(original_path)
    await query.message.reply_text(f"محتوا {content_id} حذف شد.")
    log_action(update.effective_user.id, f"حذف محتوا {content_id}")


async def add_content(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT name FROM categories")
    categories = [row[0] for row in c.fetchall()]
    conn.close()
    keyboard = [[InlineKeyboardButton(cat, callback_data=f"add_to_{cat}")] for cat in categories]
    await query.message.reply_text("دسته‌بندی برای افزودن محتوا:", reply_markup=InlineKeyboardMarkup(keyboard))
    log_action(update.effective_user.id, "افزودن محتوا")


#AdminSupport::@VizmoLain_Support
async def process_add_content(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    category = query.data.split("_")[2]
    await query.message.reply_text(f"لطفاً فایل (عکس یا ویدیو) را برای دسته {category} آپلود کنید و مشخص کنید پرمیوم باشد یا خیر (0 برای خیر، 1 برای پرمیوم):")
    context.user_data["add_content_category"] = category
    log_action(update.effective_user.id, f"شروع افزودن محتوا به {category}")

async def handle_file(update: Update, context: ContextTypes.DEFAULT_TYPE):
    category = context.user_data.get("add_content_category")
    if not category:
        return
    
    file = update.message.video or update.message.photo[-1] if update.message.photo else None
    is_premium = int(update.message.caption) if update.message.caption in ["0", "1"] else 0
    if not file:
        await update.message.reply_text("لطفاً یک فایل معتبر (عکس یا ویدیو) آپلود کنید.")
        return
    
    content_id = generate_content_id()
    file_type = "video" if update.message.video else "photo"
    file_path = f"content/{content_id}.{file_type}"
    compressed_path = f"content/compressed_{content_id}.{file_type}"
    
    
    file_obj = await file.get_file()
    await file_obj.download(file_path)
    
    
    compress_file(file_path, compressed_path, file_type)
    
    
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("INSERT INTO contents (content_id, category, file_path, original_path, file_type, is_premium) VALUES (?, ?, ?, ?, ?, ?)", 
              (content_id, category, compressed_path, file_path, file_type, is_premium))
    conn.commit()
    conn.close()
    
    await update.message.reply_text(f"محتوا با آیدی {content_id} به دسته {category} اضافه شد (پرمیوم: {is_premium}).")
    context.user_data["add_content_category"] = None
    log_action(update.effective_user.id, f"محتوا {content_id} به {category} اضافه شد")


async def manage_premium(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.message.reply_text("آیدی عددی کاربر را برای پرمیوم کردن وارد کنید:")
    context.user_data["manage_premium"] = True
    log_action(update.effective_user.id, "مدیریت پرمیوم")

async def process_premium(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.user_data.get("manage_premium"):
        return
    try:
        user_id = int(update.message.text)
        keyboard = [[InlineKeyboardButton(f"{duration.replace('months', ' ماه').replace('month', ' ماه')}", 
                                        callback_data=f"set_premium_{user_id}_{duration}")] 
                    for duration in PREMIUM_DURATIONS.keys()]
        await update.message.reply_text("مدت زمان پرمیوم را انتخاب کنید:", reply_markup=InlineKeyboardMarkup(keyboard))
        context.user_data["manage_premium"] = False
        log_action(update.effective_user.id, f"انتخاب کاربر {user_id} برای پرمیوم")
    except:
        await update.message.reply_text("آیدی نامعتبر است. یک عدد وارد کنید.")

async def set_premium(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id, duration = query.data.split("_")[2:4]
    premium_until = time.time() + PREMIUM_DURATIONS[duration].total_seconds()
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("UPDATE users SET premium_until = ? WHERE user_id = ?", (premium_until, int(user_id)))
    conn.commit()
    conn.close()
    await query.message.reply_text(f"کاربر {user_id} تا {duration.replace('months', ' ماه').replace('month', ' ماه')} پرمیوم شد.")
    try:
        await context.bot.send_message(user_id, f"حساب شما تا {duration.replace('months', ' ماه').replace('month', ' ماه')} پرمیوم شد!")
    except:
        pass
    log_action(update.effective_user.id, f"پرمیوم کردن کاربر {user_id} برای {duration}")


async def manage_categories(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT name FROM categories")
    categories = [row[0] for row in c.fetchall()]
    conn.close()
    keyboard = [[InlineKeyboardButton(f"حذف {cat}", callback_data=f"delete_cat_{cat}")] for cat in categories]
    keyboard.append([InlineKeyboardButton("افزودن دسته‌بندی", callback_data="add_category")])
    await query.message.reply_text("مدیریت دسته‌بندی‌ها:", reply_markup=InlineKeyboardMarkup(keyboard))
    log_action(update.effective_user.id, "مدیریت دسته‌بندی‌ها")

async def add_category(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.message.reply_text("نام دسته‌بندی جدید را وارد کنید:")
    context.user_data["add_category"] = True
    log_action(update.effective_user.id, "افزودن دسته‌بندی جدید")

async def process_add_category(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.user_data.get("add_category"):
        return
    category = update.message.text
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("INSERT OR IGNORE INTO categories (name) VALUES (?)", (category,))
    conn.commit()
    conn.close()
    await update.message.reply_text(f"دسته‌بندی {category} اضافه شد.")
    context.user_data["add_category"] = False
    log_action(update.effective_user.id, f"دسته‌بندی {category} اضافه شد")

async def delete_category(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    category = query.data.split("_")[2]
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT file_path, original_path FROM contents WHERE category = ?", (category,))
    files = c.fetchall()
    c.execute("DELETE FROM contents WHERE category = ?", (category,))
    c.execute("DELETE FROM categories WHERE name = ?", (category,))
    conn.commit()
    conn.close()
    for file_path, original_path in files:
        if os.path.exists(file_path):
            os.remove(file_path)
        if os.path.exists(original_path):
            os.remove(original_path)
    await query.message.reply_text(f"دسته‌بندی {category} حذف شد.")
    log_action(update.effective_user.id, f"حذف دسته‌بندی {category}")


async def view_logs(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("SELECT user_id, action, timestamp FROM logs ORDER BY timestamp DESC LIMIT 20")
    logs = c.fetchall()
    conn.close()
    text = "لاگ‌های اخیر:\n"
    for user_id, action, timestamp in logs:
        text += f"کاربر {user_id} - {action} - {datetime.fromtimestamp(timestamp).strftime('%Y-%m-%d %H:%M:%S')}\n"
    await query.message.reply_text(text)
    log_action(update.effective_user.id, "مشاهده لاگ‌ها")


async def manage_bans(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.message.reply_text("آیدی عددی کاربر را برای بن یا رفع بن وارد کنید:")
    context.user_data["manage_bans"] = True
    log_action(update.effective_user.id, "مدیریت بن کاربران")

async def process_ban(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.user_data.get("manage_bans"):
        return
    try:
        user_id = int(update.message.text)
        keyboard = [
            [InlineKeyboardButton("بن کردن", callback_data=f"ban_user_{user_id}")],
            [InlineKeyboardButton("رفع بن", callback_data=f"unban_user_{user_id}")],
        ]
        await update.message.reply_text("گزینه مورد نظر را انتخاب کنید:", reply_markup=InlineKeyboardMarkup(keyboard))
        context.user_data["manage_bans"] = False
        log_action(update.effective_user.id, f"مدیریت بن کاربر {user_id}")
    except:
        await update.message.reply_text("آیدی نامعتبر است. یک عدد وارد کنید.")

async def ban_user(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = int(query.data.split("_")[2])
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("UPDATE users SET banned_until = ? WHERE user_id = ?", (time.time() + 86400, user_id))
    conn.commit()
    conn.close()
    await query.message.reply_text(f"کاربر {user_id} بن شد.")
    try:
        await context.bot.send_message(user_id, "شما برای 24 ساعت بن شدید.")
    except:
        pass
    log_action(update.effective_user.id, f"بن کردن کاربر {user_id}")

async def unban_user(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = int(query.data.split("_")[2])
    conn = sqlite3.connect('bot.db')
    c = conn.cursor()
    c.execute("UPDATE users SET banned_until = NULL WHERE user_id = ?", (user_id,))
    conn.commit()
    conn.close()
    await query.message.reply_text(f"کاربر {user_id} رفع بن شد.")
    try:
        await context.bot.send_message(user_id, "بن شما رفع شد.")
    except:
        pass
    log_action(update.effective_user.id, f"رفع بن کاربر {user_id}")


async def message_user(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.message.reply_text("آیدی عددی کاربر و پیام را وارد کنید (مثل: 123456 پیام شما):")
    context.user_data["message_user"] = True
    log_action(update.effective_user.id, "ارسال پیام به کاربر")

async def process_message_user(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.user_data.get("message_user"):
        return
    try:
        user_id, message = update.message.text.split(" ", 1)
        user_id = int(user_id)
        await context.bot.send_message(user_id, message)
        await update.message.reply_text(f"پیام به کاربر {user_id} ارسال شد.")
        log_action(update.effective_user.id, f"ارسال پیام به کاربر {user_id}")
    except:
        await update.message.reply_text("فرمت اشتباه است. مثال: 123456 پیام شما")
    context.user_data["message_user"] = False



#AdminSupport::@VizmoLain_Support
async def back_to_main(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    keyboard = [
        [InlineKeyboardButton("ارتباط", callback_data="contact")],
        [InlineKeyboardButton("لیست محتوا", callback_data="content_list")],
        [InlineKeyboardButton("جستجوی فیلم", callback_data="search")],
        [InlineKeyboardButton("راهنما", callback_data="help")],
        [InlineKeyboardButton("تغییر زبان", callback_data="change_language")],
    ]
    await query.message.reply_text("به منوی اصلی بازگشتید:", reply_markup=InlineKeyboardMarkup(keyboard))
    log_action(update.effective_user.id, "بازگشت به منوی اصلی")


def main():
    init_db()
    app = Application.builder().token(TOKEN).build()
    
    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("admin", admin_panel))
    app.add_handler(CallbackQueryHandler(show_category, pattern="cat_"))
    app.add_handler(CallbackQueryHandler(show_content, pattern="content_"))
    app.add_handler(CallbackQueryHandler(contact, pattern="contact"))
    app.add_handler(CallbackQueryHandler(content_list, pattern="content_list"))
    app.add_handler(CallbackQueryHandler(search_content, pattern="search"))
    app.add_handler(CallbackQueryHandler(back_to_main, pattern="back_to_main"))
    app.add_handler(CallbackQueryHandler(stats, pattern="stats"))
    app.add_handler(CallbackQueryHandler(broadcast, pattern="broadcast"))
    app.add_handler(CallbackQueryHandler(manage_channels, pattern="manage_channels"))
    app.add_handler(CallbackQueryHandler(manage_content, pattern="manage_content"))
    app.add_handler(CallbackQueryHandler(add_content, pattern="add_content"))
    app.add_handler(CallbackQueryHandler(process_add_content, pattern="add_to_"))
    app.add_handler(CallbackQueryHandler(manage_premium, pattern="manage_premium"))
    app.add_handler(CallbackQueryHandler(set_premium, pattern="set_premium_"))
    app.add_handler(CallbackQueryHandler(manage_categories, pattern="manage_categories"))
    app.add_handler(CallbackQueryHandler(add_category, pattern="add_category"))
    app.add_handler(CallbackQueryHandler(delete_category, pattern="delete_cat_"))
    app.add_handler(CallbackQueryHandler(view_logs, pattern="view_logs"))
    app.add_handler(CallbackQueryHandler(manage_bans, pattern="manage_bans"))
    app.add_handler(CallbackQueryHandler(ban_user, pattern="ban_user_"))
    app.add_handler(CallbackQueryHandler(unban_user, pattern="unban_user_"))
    app.add_handler(CallbackQueryHandler(message_user, pattern="message_user"))
    app.add_handler(CallbackQueryHandler(help_menu, pattern="help"))
    app.add_handler(CallbackQueryHandler(change_language, pattern="change_language"))
    app.add_handler(CallbackQueryHandler(set_language, pattern="lang_"))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, check_password), group=1)
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, forward_to_admin), group=2)
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, process_search), group=3)
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, send_broadcast), group=4)
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, process_add_channel), group=5)
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, process_premium), group=6)
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, process_add_category), group=7)
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, process_message_user), group=8)
    app.add_handler(MessageHandler(filters.PHOTO | filters.VIDEO, handle_file))
    
    app.run_polling()

if __name__ == "__main__":
    main()

#AdminSupport::@VizmoLain_Support